<?php

namespace Tests\Feature;

use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\Section;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentProofSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_and_non_admin_cannot_access_private_receipt(): void
    {
        [$order, $proof] = $this->privateProof();
        $url = route('admin.orders.payment-proofs.file', [$order, $proof]);

        $this->get($url)->assertRedirect(route('admin.login'));

        $user = User::factory()->create(['role' => 'customer']);
        $this->actingAs($user)->get($url)->assertForbidden();
    }

    public function test_both_brand_admins_can_access_shared_private_receipt(): void
    {
        [$order, $proof] = $this->privateProof();
        $rikaz = $this->adminFor('rikaz');
        $lujain = $this->adminFor('lujain');
        $url = route('admin.orders.payment-proofs.file', [$order, $proof]);

        $this->actingAs($rikaz)->get($url)
            ->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg')
            ->assertHeader('Content-Disposition', 'inline; filename=receipt.jpg')
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->actingAs($lujain)->get($url)->assertOk();
    }

    public function test_manipulated_order_proof_pair_and_unsafe_key_return_not_found(): void
    {
        [$order, $proof] = $this->privateProof();
        [$otherOrder] = $this->privateProof('other.jpg');
        $admin = $this->adminFor('rikaz');

        $this->actingAs($admin)
            ->get(route('admin.orders.payment-proofs.file', [$otherOrder, $proof]))
            ->assertNotFound();

        $proof->update(['public_id' => '../receipt.jpg']);
        $this->actingAs($admin)
            ->get(route('admin.orders.payment-proofs.file', [$order, $proof]))
            ->assertNotFound();
    }

    public function test_product_images_remain_public_and_receipts_are_private(): void
    {
        Storage::fake('public');
        Storage::fake('payment_proofs');
        $service = app(ImageService::class);

        $product = $service->uploadProductImage(
            UploadedFile::fake()->create('product.jpg', 10, 'image/jpeg')
        );
        $proof = $service->uploadPaymentProof(
            UploadedFile::fake()->create('receipt.jpg', 10, 'image/jpeg')
        );

        Storage::disk('public')->assertExists($product['public_id']);
        $this->assertStringContainsString('/storage/products/', $product['url']);
        Storage::disk('payment_proofs')->assertExists($proof['public_id']);
        Storage::disk('public')->assertMissing('payment-proofs/'.$proof['public_id']);
        $this->assertSame($proof['public_id'], $proof['url']);
    }

    public function test_upload_service_uses_environment_selected_storage_disks(): void
    {
        Storage::fake('catalog_media');
        Storage::fake('secure_receipts');
        config([
            'filesystems.product_images_disk' => 'catalog_media',
            'filesystems.payment_proofs_disk' => 'secure_receipts',
        ]);

        $service = app(ImageService::class);
        $product = $service->uploadProductImage(
            UploadedFile::fake()->create('product.jpg', 10, 'image/jpeg')
        );
        $proof = $service->uploadPaymentProof(
            UploadedFile::fake()->create('receipt.jpg', 10, 'image/jpeg')
        );

        Storage::disk('catalog_media')->assertExists($product['public_id']);
        Storage::disk('secure_receipts')->assertExists($proof['public_id']);
    }

    public function test_legacy_public_receipt_migration_is_safe_and_idempotent(): void
    {
        Storage::fake('public');
        Storage::fake('payment_proofs');
        $order = $this->order();
        $proof = $order->paymentProofs()->create([
            'url' => '/storage/payment-proofs/legacy.jpg',
            'public_id' => 'payment-proofs/legacy.jpg',
            'review_status' => 'pending',
        ]);
        Storage::disk('public')->put('payment-proofs/legacy.jpg', 'legacy receipt');

        $this->artisan('payment-proofs:migrate-private')->assertSuccessful();

        Storage::disk('payment_proofs')->assertExists('legacy.jpg');
        Storage::disk('public')->assertMissing('payment-proofs/legacy.jpg');
        $this->assertSame('legacy.jpg', $proof->fresh()->public_id);
        $this->assertSame('legacy.jpg', $proof->fresh()->url);

        $this->artisan('payment-proofs:migrate-private')->assertSuccessful();
        Storage::disk('payment_proofs')->assertExists('legacy.jpg');
    }

    private function privateProof(string $key = 'receipt.jpg'): array
    {
        Storage::fake('payment_proofs');
        Storage::disk('payment_proofs')->put($key, 'private receipt');
        $order = $this->order();
        $proof = $order->paymentProofs()->create([
            'url' => $key,
            'public_id' => $key,
            'review_status' => 'pending',
        ]);

        return [$order, $proof];
    }

    private function order(): Order
    {
        $zone = DeliveryZone::create([
            'name_ar' => 'Beirut', 'fee' => 3, 'is_active' => true, 'sort_order' => 0,
        ]);

        return Order::create([
            'order_number' => 'RL-'.uniqid(), 'customer_name' => 'Customer',
            'customer_phone' => '70123456', 'delivery_zone_id' => $zone->id,
            'delivery_zone_name' => 'Beirut', 'address' => 'Beirut', 'subtotal' => 10,
            'delivery_fee' => 3, 'total' => 13, 'payment_method' => 'whish',
            'payment_status' => 'pending_verification', 'status' => 'awaiting_payment_verification',
        ]);
    }

    private function adminFor(string $slug): User
    {
        $section = Section::firstOrCreate(
            ['slug' => $slug],
            ['name_ar' => $slug, 'audience' => 'all', 'is_active' => true]
        );

        return User::factory()->create([
            'role' => 'admin',
            'section_id' => $section->id,
        ]);
    }
}
