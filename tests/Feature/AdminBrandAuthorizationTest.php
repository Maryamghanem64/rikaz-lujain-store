<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminBrandAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private Section $rikaz;

    private Section $lujain;

    private Category $rikazCategory;

    private Category $lujainCategory;

    private Product $rikazProduct;

    private Product $lujainProduct;

    private User $rikazAdmin;

    private User $lujainAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rikaz = $this->section('rikaz', 'ركاز');
        $this->lujain = $this->section('lujain', 'لجين');
        $this->rikazCategory = $this->category($this->rikaz, 'rikaz-rings');
        $this->lujainCategory = $this->category($this->lujain, 'lujain-rings');
        $this->rikazProduct = $this->product($this->rikazCategory, 'rikaz-product');
        $this->lujainProduct = $this->product($this->lujainCategory, 'lujain-product');
        $this->rikazAdmin = $this->admin($this->rikaz, 'rikaz@example.test');
        $this->lujainAdmin = $this->admin($this->lujain, 'lujain@example.test');
    }

    public function test_both_brand_admins_can_access_the_dashboard(): void
    {
        $this->actingAs($this->rikazAdmin)->get(route('admin.dashboard'))->assertOk()->assertSee('إدارة ركاز');
        $this->actingAs($this->lujainAdmin)->get(route('admin.dashboard'))->assertOk()->assertSee('إدارة لجين');
    }

    public function test_each_admin_can_edit_only_their_own_products(): void
    {
        $this->actingAs($this->rikazAdmin)->get(route('admin.products.edit', $this->rikazProduct))->assertOk();
        $this->actingAs($this->rikazAdmin)->get(route('admin.products.edit', $this->lujainProduct))->assertForbidden();
        $this->actingAs($this->lujainAdmin)->get(route('admin.products.edit', $this->lujainProduct))->assertOk();
        $this->actingAs($this->lujainAdmin)->get(route('admin.products.edit', $this->rikazProduct))->assertForbidden();
    }

    public function test_product_updates_and_deletes_are_brand_protected(): void
    {
        $this->actingAs($this->rikazAdmin)
            ->put(route('admin.products.update', $this->lujainProduct), $this->productPayload($this->lujainCategory))
            ->assertForbidden();
        $this->actingAs($this->lujainAdmin)
            ->delete(route('admin.products.destroy', $this->rikazProduct))
            ->assertForbidden();

        $own = $this->product($this->rikazCategory, 'rikaz-deletable');
        $this->actingAs($this->rikazAdmin)->delete(route('admin.products.destroy', $own))->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $own->id]);
    }

    public function test_product_creation_cannot_assign_the_other_brand_category(): void
    {
        $this->actingAs($this->rikazAdmin)
            ->post(route('admin.products.store'), $this->productPayload($this->lujainCategory, 'crafted-product'))
            ->assertSessionHasErrors('category_id');

        $this->assertDatabaseMissing('products', ['slug' => 'crafted-product']);
    }

    public function test_product_indexes_and_category_indexes_are_brand_scoped(): void
    {
        $this->actingAs($this->rikazAdmin)->get(route('admin.products.index'))
            ->assertOk()->assertSee('rikaz-product')->assertDontSee('lujain-product');
        $this->actingAs($this->lujainAdmin)->get(route('admin.categories.index'))
            ->assertOk()->assertSee('lujain-rings')->assertDontSee('rikaz-rings');
    }

    public function test_category_create_update_and_delete_are_brand_protected(): void
    {
        $this->actingAs($this->rikazAdmin)->post(route('admin.categories.store'), [
            'section_id' => $this->lujain->id,
            'name_ar' => 'فئة ركاز',
            'slug' => 'rikaz-new',
            'sort_order' => 2,
            'is_active' => 1,
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('categories', ['section_id' => $this->rikaz->id, 'slug' => 'rikaz-new']);

        $this->actingAs($this->rikazAdmin)->put(route('admin.categories.update', $this->lujainCategory), [
            'name_ar' => 'ممنوع', 'slug' => 'forbidden', 'sort_order' => 0,
        ])->assertForbidden();
        $this->actingAs($this->rikazAdmin)->delete(route('admin.categories.destroy', $this->lujainCategory))->assertForbidden();
    }

    public function test_product_image_operations_are_brand_protected(): void
    {
        $image = ProductImage::create([
            'product_id' => $this->lujainProduct->id,
            'url' => '/storage/products/test.jpg',
            'public_id' => 'test',
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        $this->actingAs($this->rikazAdmin)
            ->patch(route('admin.products.images.primary', [$this->lujainProduct, $image]))
            ->assertForbidden();
        $this->actingAs($this->rikazAdmin)
            ->patch(route('admin.products.images.order', $this->lujainProduct), ['orders' => [$image->id => 1]])
            ->assertForbidden();
        $this->actingAs($this->rikazAdmin)
            ->delete(route('admin.products.images.destroy', [$this->lujainProduct, $image]))
            ->assertForbidden();
        $this->actingAs($this->rikazAdmin)
            ->post(route('admin.products.images.store', $this->lujainProduct), ['images' => [UploadedFile::fake()->create('ring.jpg', 10, 'image/jpeg')]])
            ->assertForbidden();
    }

    public function test_both_admins_access_the_same_mixed_brand_order(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-MIXED-1', 'customer_name' => 'عميل', 'customer_phone' => '70123456',
            'delivery_zone_name' => 'بيروت', 'address' => 'بيروت', 'subtotal' => 20, 'delivery_fee' => 0,
            'total' => 20, 'payment_method' => 'cash', 'payment_status' => 'pending', 'status' => 'new_cash',
        ]);
        foreach ([$this->rikazProduct, $this->lujainProduct] as $product) {
            OrderItem::create([
                'order_id' => $order->id, 'product_id' => $product->id, 'product_name_ar' => $product->name_ar,
                'silver_purity' => '925', 'unit_price' => 10, 'quantity' => 1, 'subtotal' => 10,
            ]);
        }

        $this->actingAs($this->rikazAdmin)->get(route('admin.orders.show', $order))->assertOk()->assertSee('طلب مختلط العلامات')->assertSee('ركاز')->assertSee('لجين');
        $this->actingAs($this->lujainAdmin)->get(route('admin.orders.show', $order))->assertOk()->assertSee('طلب مختلط العلامات');
    }

    public function test_guests_and_non_admin_users_cannot_access_admin(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect('/admin/login');
        $user = User::factory()->create(['role' => 'user', 'section_id' => $this->rikaz->id]);
        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    private function section(string $slug, string $name): Section
    {
        return Section::create(['slug' => $slug, 'name_ar' => $name, 'audience' => 'all', 'is_active' => true]);
    }

    private function category(Section $section, string $slug): Category
    {
        return Category::create(['section_id' => $section->id, 'name_ar' => $slug, 'slug' => $slug, 'sort_order' => 0, 'is_active' => true]);
    }

    private function product(Category $category, string $slug): Product
    {
        return Product::create(['category_id' => $category->id, 'name_ar' => $slug, 'slug' => $slug, 'silver_purity' => '925', 'price' => 10, 'stock_quantity' => 2, 'reserved_quantity' => 0, 'is_active' => true, 'is_featured' => false]);
    }

    private function admin(Section $section, string $email): User
    {
        return User::factory()->create(['role' => 'admin', 'section_id' => $section->id, 'email' => $email]);
    }

    private function productPayload(Category $category, string $slug = 'updated-product'): array
    {
        return ['category_id' => $category->id, 'name_ar' => $slug, 'slug' => $slug, 'silver_purity' => '925', 'price' => 15, 'stock_quantity' => 2, 'is_active' => 1];
    }
}
