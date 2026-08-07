<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderWorkflowService
{
    public function __construct(
        private InventoryService $inventoryService
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | Confirm Cash Order
    |--------------------------------------------------------------------------
    */

    public function confirmCash(
        Order $order,
        User $admin,
        ?string $note = null
    ): Order {
        return DB::transaction(function () use (
            $order,
            $admin,
            $note
        ) {
            $lockedOrder = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedOrder->payment_method !== 'cash') {
                throw ValidationException::withMessages([
                    'payment' =>
                        'هذا الطلب ليس طلب دفع نقدي.',
                ]);
            }

            /*
             * Prevent duplicate stock deduction.
             */
            if (
                in_array(
                    $lockedOrder->status,
                    [
                        'confirmed',
                        'preparing',
                        'shipped',
                        'delivered',
                    ],
                    true
                )
            ) {
                return $lockedOrder;
            }

            if ($lockedOrder->status !== 'new_cash') {
                throw ValidationException::withMessages([
                    'status' =>
                        'لا يمكن تأكيد الطلب من حالته الحالية.',
                ]);
            }

            $this->inventoryService
                ->convertReservationToSale(
                    $lockedOrder
                );

            $lockedOrder->update([
                'status' => 'confirmed',
                'reservation_expires_at' => null,
            ]);

            $lockedOrder->statusHistory()->create([
                'status' => 'confirmed',
                'changed_by' => $admin->id,
                'note' =>
                    $note ?: 'تم تأكيد الطلب النقدي.',
            ]);

            return $lockedOrder->load([
                'items',
                'statusHistory',
            ]);
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Cancel Order
    |--------------------------------------------------------------------------
    */

    public function cancel(
        Order $order,
        User $admin,
        ?string $note = null
    ): Order {
        return DB::transaction(function () use (
            $order,
            $admin,
            $note
        ) {
            $lockedOrder = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Already cancelled = no second inventory change.
             */
            if ($lockedOrder->status === 'cancelled') {
                return $lockedOrder;
            }

            /*
             * بعد الشحن أو التسليم ما منعمل Cancel عادي.
             */
            if (
                in_array(
                    $lockedOrder->status,
                    [
                        'shipped',
                        'delivered',
                    ],
                    true
                )
            ) {
                throw ValidationException::withMessages([
                    'status' =>
                        'لا يمكن إلغاء طلب تم شحنه أو تسليمه.',
                ]);
            }


            /*
             * Pending orders:
             * stock unchanged,
             * release reserved_quantity only.
             */
            if (
                in_array(
                    $lockedOrder->status,
                    [
                        'new_cash',
                        'awaiting_payment_verification',
                        'payment_rejected',
                    ],
                    true
                )
            ) {
                $this->inventoryService
                    ->releaseReservation(
                        $lockedOrder
                    );
            }


            /*
             * Confirmed sale:
             * stock was already deducted,
             * so restore physical stock.
             */
            elseif (
                in_array(
                    $lockedOrder->status,
                    [
                        'confirmed',
                        'preparing',
                    ],
                    true
                )
            ) {
                $this->inventoryService
                    ->restoreSale(
                        $lockedOrder
                    );
            }

            else {
                throw ValidationException::withMessages([
                    'status' =>
                        'لا يمكن إلغاء الطلب من حالته الحالية.',
                ]);
            }

            $lockedOrder->update([
                'status' => 'cancelled',
                'reservation_expires_at' => null,
            ]);

            $lockedOrder->statusHistory()->create([
                'status' => 'cancelled',
                'changed_by' => $admin->id,
                'note' =>
                    $note ?: 'تم إلغاء الطلب.',
            ]);

            return $lockedOrder->load([
                'items',
                'statusHistory',
            ]);
        });
    }


    /*
    |--------------------------------------------------------------------------
    | confirmed → preparing → shipped → delivered
    |--------------------------------------------------------------------------
    */

    public function advance(
        Order $order,
        User $admin,
        string $targetStatus,
        ?string $note = null
    ): Order {
        return DB::transaction(function () use (
            $order,
            $admin,
            $targetStatus,
            $note
        ) {
            $lockedOrder = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Same status = harmless duplicate click.
             */
            if ($lockedOrder->status === $targetStatus) {
                return $lockedOrder;
            }

            $allowedTransitions = [
                'confirmed' => 'preparing',
                'preparing' => 'shipped',
                'shipped' => 'delivered',
            ];

            $expectedTarget =
                $allowedTransitions[
                    $lockedOrder->status
                ] ?? null;

            if ($expectedTarget !== $targetStatus) {
                throw ValidationException::withMessages([
                    'status' =>
                        'هذا الانتقال بين حالات الطلب غير مسموح.',
                ]);
            }

           $updateData = [
    'status' => $targetStatus,
];

if (
    $targetStatus === 'delivered' &&
    $lockedOrder->payment_method === 'cash'
) {
    $updateData['payment_status'] = 'paid_on_delivery';
}

$lockedOrder->update($updateData);

            $lockedOrder->statusHistory()->create([
                'status' => $targetStatus,
                'changed_by' => $admin->id,
                'note' =>
                    $note ?: 'تم تحديث حالة الطلب.',
            ]);

            return $lockedOrder->load(
                'statusHistory'
            );
        });
    }
}