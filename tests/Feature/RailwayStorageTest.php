<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Section;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RailwayStorageTest extends TestCase
{
    use RefreshDatabase;

    public function test_configured_railway_disks_receive_portable_product_and_receipt_keys(): void
    {
        $this->fakeRailwayDisks();
        $service = app(ImageService::class);

        $product = $service->uploadProductImage(
            UploadedFile::fake()->create('ring.jpg', 10, 'image/jpeg')
        );
        $proof = $service->uploadPaymentProof(
            UploadedFile::fake()->create('receipt.jpg', 10, 'image/jpeg')
        );

        Storage::disk('railway_products')->assertExists($product['public_id']);
        Storage::disk('railway_payment_proofs')->assertExists($proof['public_id']);
        $this->assertStringNotContainsString('products/products/', $product['public_id']);
        $this->assertStringNotContainsString('payment-proofs/payment-proofs/', $proof['public_id']);
        $this->assertStringNotContainsString('C:\\', $product['public_id']);
        $this->assertSame($product['public_id'], $product['url']);
        $this->assertSame($proof['public_id'], $proof['url']);
    }

    public function test_public_product_media_route_streams_configured_private_bucket_object(): void
    {
        $this->fakeRailwayDisks();
        $image = $this->productImage('ring.jpg');
        Storage::disk('railway_products')->put('ring.jpg', 'public catalog image');

        $this->get(route('media.products.show', $image))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg')
            ->assertHeader('Cache-Control', 'max-age=86400, public')
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->assertSame(route('media.products.show', $image), $image->displayUrl());
    }

    public function test_public_product_media_route_rejects_missing_and_arbitrary_objects(): void
    {
        $this->fakeRailwayDisks();
        $missing = $this->productImage('missing.jpg');
        $unsafe = $this->productImage('../payment-proofs/secret.jpg');

        $this->get(route('media.products.show', $missing))->assertNotFound();
        $this->get(route('media.products.show', $unsafe))->assertNotFound();
        $this->get('/media/products/payment-proofs/secret.jpg')->assertNotFound();
    }

    public function test_authenticated_admin_can_stream_railway_receipt_but_guest_cannot(): void
    {
        $this->fakeRailwayDisks();
        $order = $this->order();
        $proof = $order->paymentProofs()->create([
            'url' => 'receipt.jpg',
            'public_id' => 'receipt.jpg',
            'review_status' => 'pending',
        ]);
        Storage::disk('railway_payment_proofs')->put('receipt.jpg', 'private receipt');
        $url = route('admin.orders.payment-proofs.file', [$order, $proof]);

        $this->get($url)->assertRedirect(route('admin.login'));
        $this->actingAs($this->admin())->get($url)
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_product_deletion_removes_object_from_configured_railway_disk(): void
    {
        $this->fakeRailwayDisks();
        $image = $this->productImage('delete-me.jpg');
        Storage::disk('railway_products')->put('delete-me.jpg', 'image');
        $admin = User::factory()->create([
            'role' => 'admin',
            'section_id' => $image->product->category->section_id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.products.destroy', $image->product))
            ->assertRedirect(route('admin.products.index'));

        Storage::disk('railway_products')->assertMissing('delete-me.jpg');
    }

    public function test_local_public_product_media_remains_supported(): void
    {
        Storage::fake('public');
        config(['filesystems.product_images_disk' => 'public']);
        $uploaded = app(ImageService::class)->uploadProductImage(
            UploadedFile::fake()->create('local.jpg', 10, 'image/jpeg')
        );
        $image = $this->productImage($uploaded['public_id'], $uploaded['url']);

        Storage::disk('public')->assertExists($uploaded['public_id']);
        $this->assertStringContainsString('/storage/products/', $image->displayUrl());
    }

    private function fakeRailwayDisks(): void
    {
        Storage::fake('railway_products');
        Storage::fake('railway_payment_proofs');
        config([
            'filesystems.product_images_disk' => 'railway_products',
            'filesystems.payment_proofs_disk' => 'railway_payment_proofs',
        ]);
    }

    private function productImage(string $key, ?string $url = null): ProductImage
    {
        $section = Section::firstOrCreate(
            ['slug' => 'rikaz'],
            ['name_ar' => 'Rikaz', 'audience' => 'men', 'is_active' => true]
        );
        $category = Category::firstOrCreate(
            ['section_id' => $section->id, 'slug' => 'rings'],
            ['name_ar' => 'Rings', 'sort_order' => 0, 'is_active' => true]
        );
        $product = Product::create([
            'category_id' => $category->id, 'name_ar' => 'Ring', 'slug' => 'ring-'.uniqid(),
            'silver_purity' => '925', 'price' => 10, 'stock_quantity' => 1,
            'reserved_quantity' => 0, 'is_active' => true, 'is_featured' => false,
        ]);

        return $product->images()->create([
            'url' => $url ?? $key,
            'public_id' => $key,
            'is_primary' => true,
            'sort_order' => 0,
        ]);
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

    private function admin(): User
    {
        $section = Section::firstOrCreate(
            ['slug' => 'lujain'],
            ['name_ar' => 'Lujain', 'audience' => 'women', 'is_active' => true]
        );

        return User::factory()->create(['role' => 'admin', 'section_id' => $section->id]);
    }
}
