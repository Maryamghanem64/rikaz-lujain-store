<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DeliveryZone;
use App\Models\Product;
use App\Models\Section;
use App\Models\User;
use App\Services\OrderService;
use App\Services\OrderWorkflowService;
use App\Services\PaymentProofService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cash_reservation_confirmation_and_duplicate_actions_are_inventory_safe(): void
    {
        [$product, $zone, $admin] = $this->catalog();
        $order = $this->createOrder($product, $zone, 'cash');

        $this->assertInventory($product, 1, 1);

        $workflow = app(OrderWorkflowService::class);
        $workflow->confirmCash($order, $admin);
        $workflow->confirmCash($order, $admin);

        $this->assertInventory($product, 0, 0);
        $this->assertSame(1, $order->statusHistory()->where('status', 'confirmed')->count());
    }

    public function test_pending_and_confirmed_cancellations_are_idempotent(): void
    {
        [$product, $zone, $admin] = $this->catalog(stock: 2);
        $workflow = app(OrderWorkflowService::class);

        $pending = $this->createOrder($product, $zone, 'cash');
        $workflow->cancel($pending, $admin);
        $workflow->cancel($pending, $admin);
        $this->assertInventory($product, 2, 0);

        $confirmed = $this->createOrder($product, $zone, 'cash');
        $workflow->confirmCash($confirmed, $admin);
        $workflow->cancel($confirmed, $admin);
        $workflow->cancel($confirmed, $admin);
        $this->assertInventory($product, 2, 0);
    }

    public function test_whish_verification_is_pending_then_idempotently_converts_reservation_to_sale(): void
    {
        Storage::fake('public');
        Storage::fake('payment_proofs');
        [$product, $zone, $admin] = $this->catalog();
        $order = $this->createOrder($product, $zone, 'whish');
        $proof = $order->paymentProofs()->firstOrFail();

        Storage::disk('payment_proofs')->assertExists($proof->public_id);
        Storage::disk('public')->assertMissing('payment-proofs/'.$proof->public_id);
        $this->assertSame($proof->public_id, $proof->url);
        $this->assertSame('pending', $proof->review_status);
        $this->assertSame('pending_verification', $order->payment_status);
        $this->assertInventory($product, 1, 1);

        $service = app(PaymentProofService::class);
        $service->verify($order, $proof, $admin);
        $service->verify($order, $proof, $admin);

        $this->assertInventory($product, 0, 0);
        $this->assertSame('verified', $order->fresh()->payment_status);
    }

    public function test_whish_rejection_still_records_reviewer_reason_and_statuses(): void
    {
        Storage::fake('payment_proofs');
        [$product, $zone, $admin] = $this->catalog();
        $order = $this->createOrder($product, $zone, 'whish');
        $proof = $order->paymentProofs()->firstOrFail();

        app(PaymentProofService::class)->reject($order, $proof, $admin, 'Unreadable receipt');

        $this->assertSame('payment_rejected', $order->fresh()->status);
        $this->assertSame('rejected', $order->fresh()->payment_status);
        $this->assertSame('rejected', $proof->fresh()->review_status);
        $this->assertSame($admin->id, $proof->fresh()->reviewed_by);
        $this->assertSame('Unreadable receipt', $proof->fresh()->rejection_reason);
        $this->assertNotNull($proof->fresh()->reviewed_at);
    }

    public function test_manual_release_rejects_confirmed_orders(): void
    {
        [$product, $zone, $admin] = $this->catalog();
        $order = $this->createOrder($product, $zone, 'cash');
        $workflow = app(OrderWorkflowService::class);
        $workflow->confirmCash($order, $admin);

        $this->expectException(ValidationException::class);
        $workflow->releasePendingReservation($order, $admin);
    }

    public function test_cash_delivery_marks_paid_and_expiry_releases_only_pending_reservations(): void
    {
        [$product, $zone, $admin] = $this->catalog(stock: 2);
        $workflow = app(OrderWorkflowService::class);

        $delivered = $this->createOrder($product, $zone, 'cash');
        $workflow->confirmCash($delivered, $admin);
        $workflow->advance($delivered, $admin, 'preparing');
        $workflow->advance($delivered, $admin, 'shipped');
        $workflow->advance($delivered, $admin, 'delivered');
        $this->assertSame('paid_on_delivery', $delivered->fresh()->payment_status);

        $expired = $this->createOrder($product, $zone, 'cash');
        $expired->update(['reservation_expires_at' => now()->subMinute()]);
        $this->artisan('orders:release-expired-reservations')->assertSuccessful();

        $this->assertSame('cancelled', $expired->fresh()->status);
        $this->assertNull($expired->fresh()->reservation_expires_at);
        $this->assertInventory($product, 1, 0);
        $this->assertSame('delivered', $delivered->fresh()->status);
    }

    private function catalog(int $stock = 1): array
    {
        $section = Section::create([
            'name_ar' => 'ركاز', 'slug' => 'rikaz', 'audience' => 'men', 'is_active' => true,
        ]);
        $category = Category::create([
            'section_id' => $section->id, 'name_ar' => 'خواتم', 'slug' => 'rings',
            'sort_order' => 0, 'is_active' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id, 'name_ar' => 'خاتم', 'slug' => 'ring-'.uniqid(),
            'silver_purity' => '925', 'price' => 10, 'stock_quantity' => $stock,
            'reserved_quantity' => 0, 'is_active' => true, 'is_featured' => false,
        ]);
        $zone = DeliveryZone::create([
            'name_ar' => 'بيروت', 'fee' => 3, 'is_active' => true, 'sort_order' => 0,
        ]);
        $admin = User::factory()->create(['role' => 'admin']);

        return [$product, $zone, $admin];
    }

    private function createOrder(Product $product, DeliveryZone $zone, string $method): mixed
    {
        session()->put('cart', [$product->id => 1]);

        return app(OrderService::class)->create([
            'customer_name' => 'عميل', 'customer_phone' => '70123456',
            'customer_whatsapp' => '70123456', 'delivery_zone_id' => $zone->id,
            'address' => 'بيروت', 'payment_method' => $method,
            'payment_proof' => $method === 'whish'
                ? UploadedFile::fake()->create('receipt.jpg', 100, 'image/jpeg')
                : null,
        ]);
    }

    private function assertInventory(Product $product, int $stock, int $reserved): void
    {
        $product->refresh();
        $this->assertSame($stock, $product->stock_quantity);
        $this->assertSame($reserved, $product->reserved_quantity);
        $this->assertGreaterThanOrEqual(0, $product->stock_quantity);
        $this->assertGreaterThanOrEqual(0, $product->reserved_quantity);
    }
}
