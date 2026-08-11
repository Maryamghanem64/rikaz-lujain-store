<?php

namespace App\Services;

use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class OrderService
{
    public function __construct(
        private CartService $cart,
        private ImageService $imageService
    ) {}

    public function create(array $data): Order
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Read cart
        |--------------------------------------------------------------------------
        */

        $cartItems = $this->cart->items()
            ->sortBy(fn (array $item) => $item['product']->id)
            ->values();

        if ($cartItems->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'سلة التسوق فارغة.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Validate delivery zone from database
        |--------------------------------------------------------------------------
        */

        $deliveryZone = DeliveryZone::query()
            ->whereKey($data['delivery_zone_id'])
            ->where('is_active', true)
            ->first();

        if (! $deliveryZone) {
            throw ValidationException::withMessages([
                'delivery_zone_id' => 'منطقة التوصيل المختارة غير متاحة.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Store settings
        |--------------------------------------------------------------------------
        */

        $settings = Setting::first();

        $reservationHours = (int) (
            $settings?->reservation_hours ?? 24
        );

        /*
        |--------------------------------------------------------------------------
        | 4. Upload Whish payment proof
        |--------------------------------------------------------------------------
        */

        $uploadedProof = null;

        if ($data['payment_method'] === 'whish') {
            $proof = $data['payment_proof'] ?? null;

            if (! $proof instanceof UploadedFile) {
                throw ValidationException::withMessages([
                    'payment_proof' => 'يجب رفع إيصال تحويل Whish.',
                ]);
            }

            $uploadedProof =
                $this->imageService
                    ->uploadPaymentProof($proof);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Create order inside transaction
        |--------------------------------------------------------------------------
        */

        try {
            return DB::transaction(function () use (
                $data,
                $cartItems,
                $deliveryZone,
                $reservationHours,
                $uploadedProof
            ) {

                $deliveryZone = DeliveryZone::query()
                    ->whereKey($deliveryZone->id)
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->first();

                if (! $deliveryZone) {
                    throw ValidationException::withMessages([
                        'delivery_zone_id' => 'منطقة التوصيل المختارة لم تعد متاحة.',
                    ]);
                }

                $subtotal = 0;

                $preparedItems = [];

                /*
                |--------------------------------------------------------------------------
                | 6. Re-check products + stock + price from DB
                |--------------------------------------------------------------------------
                */

                foreach ($cartItems as $cartItem) {
                    $product = Product::query()
                        ->storefrontAvailable()
                        ->whereKey(
                            $cartItem['product']->id
                        )
                        ->lockForUpdate()
                        ->first();

                    if (
                        ! $product
                    ) {
                        throw ValidationException::withMessages([
                            'cart' => 'أحد المنتجات لم يعد متاحًا.',
                        ]);
                    }

                    $quantity = (int) $cartItem['quantity'];

                    if ($quantity < 1) {
                        throw ValidationException::withMessages([
                            'cart' => 'الكمية المطلوبة غير صحيحة.',
                        ]);
                    }

                    if (
                        $quantity >
                        $product->available_quantity
                    ) {
                        throw ValidationException::withMessages([
                            'cart' => "الكمية المطلوبة من {$product->name_ar} لم تعد متوفرة.",
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Price always comes from database
                    |--------------------------------------------------------------------------
                    */

                    $unitPrice = (float) $product->price;

                    $itemSubtotal =
                        $unitPrice * $quantity;

                    $subtotal += $itemSubtotal;

                    $preparedItems[] = [
                        'product' => $product,

                        'quantity' => $quantity,

                        'unit_price' => $unitPrice,

                        'subtotal' => $itemSubtotal,
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | 7. Calculate totals
                |--------------------------------------------------------------------------
                */

                $deliveryFee =
                    (float) $deliveryZone->fee;

                $total =
                    $subtotal + $deliveryFee;

                /*
                |--------------------------------------------------------------------------
                | 8. Determine statuses
                |--------------------------------------------------------------------------
                */

                $isWhish =
                    $data['payment_method'] === 'whish';

                $status = $isWhish
                    ? 'awaiting_payment_verification'
                    : 'new_cash';

                $paymentStatus = $isWhish
                    ? 'pending_verification'
                    : 'cash_pending';

                /*
                |--------------------------------------------------------------------------
                | 9. Create order
                |--------------------------------------------------------------------------
                */

                $order = Order::create([
                    'order_number' => $this->generateOrderNumber(),

                    'customer_name' => $data['customer_name'],

                    'customer_phone' => $data['customer_phone'],

                    'customer_whatsapp' => $data['customer_whatsapp'] ?? null,

                    'delivery_zone_id' => $deliveryZone->id,

                    /*
                     * Snapshot because delivery zones
                     * may change later.
                     */
                    'delivery_zone_name' => $deliveryZone->name_ar,

                    'address' => $data['address'],

                    'notes' => $data['notes'] ?? null,

                    'subtotal' => $subtotal,

                    /*
                     * Snapshot of current delivery fee.
                     */
                    'delivery_fee' => $deliveryFee,

                    'total' => $total,

                    'payment_method' => $data['payment_method'],

                    'payment_status' => $paymentStatus,

                    'status' => $status,

                    'reservation_expires_at' => now()->addHours(
                        $reservationHours
                    ),
                ]);

                /*
                |--------------------------------------------------------------------------
                | 10. Save Whish payment proof
                |--------------------------------------------------------------------------
                */

                if ($uploadedProof) {
                    $order->paymentProofs()->create([
                        'url' => $uploadedProof['url'],

                        'public_id' => $uploadedProof['public_id'],

                        'review_status' => 'pending',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | 11. Create order items + reserve stock
                |--------------------------------------------------------------------------
                */

                foreach ($preparedItems as $item) {
                    /** @var Product $product */
                    $product = $item['product'];

                    /*
                     * Store product snapshot.
                     */
                    $order->items()->create([
                        'product_id' => $product->id,

                        'product_name_ar' => $product->name_ar,

                        'stone_name' => $product->stone_name,

                        'stone_weight' => $product->stone_weight,

                        'silver_purity' => $product->silver_purity,

                        'size' => $product->size,

                        'unit_price' => $item['unit_price'],

                        'quantity' => $item['quantity'],

                        'subtotal' => $item['subtotal'],
                    ]);

                    /*
                     * Reserve product quantity.
                     *
                     * stock_quantity remains unchanged.
                     * available = stock - reserved.
                     */
                    $product->increment(
                        'reserved_quantity',
                        $item['quantity']
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | 12. First status history entry
                |--------------------------------------------------------------------------
                */

                $order->statusHistory()->create([
                    'status' => $status,

                    'changed_by' => null,

                    'note' => 'تم إنشاء الطلب من المتجر.',
                ]);

                /*
                |--------------------------------------------------------------------------
                | 13. Return completed order
                |--------------------------------------------------------------------------
                */

                return $order->load([
                    'items',
                    'deliveryZone',
                    'paymentProofs',
                    'statusHistory',
                ]);
            });

        } catch (Throwable $exception) {

            /*
            |--------------------------------------------------------------------------
            | Remove uploaded proof if DB transaction failed
            |--------------------------------------------------------------------------
            */

            if ($uploadedProof) {
                $this->imageService->deletePaymentProof(
                    $uploadedProof['public_id']
                );
            }

            throw $exception;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Generate unique order number
    |--------------------------------------------------------------------------
    */

    private function generateOrderNumber(): string
    {
        do {
            $number =
                'RL-'.
                now()->format('ymd').
                '-'.
                strtoupper(
                    Str::random(6)
                );

        } while (
            Order::query()
                ->where(
                    'order_number',
                    $number
                )
                ->exists()
        );

        return $number;
    }
}
