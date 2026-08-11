<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_admin_entry_redirects_to_login(): void
    {
        $this->get(route('admin.entry'))
            ->assertRedirectToRoute('admin.login');
    }

    public function test_authenticated_admin_entry_redirects_to_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.entry'))
            ->assertRedirectToRoute('admin.dashboard');
    }

    public function test_authenticated_non_admin_is_forbidden_from_admin_entry(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get(route('admin.entry'))
            ->assertForbidden();
    }

    public function test_guest_cannot_access_admin_dashboard_directly(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect('/admin/login');
    }

    public function test_legacy_admin_without_section_sees_dashboard_instead_of_404(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'section_id' => null]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('هذا حساب إداري قديم وغير مرتبط بعلامة بعد');
    }

    public function test_legacy_admin_without_section_cannot_manage_any_catalog(): void
    {
        $section = Section::create([
            'name_ar' => 'ركاز', 'slug' => 'rikaz', 'audience' => 'men', 'is_active' => true,
        ]);
        $category = Category::create([
            'section_id' => $section->id, 'name_ar' => 'خواتم', 'slug' => 'rings',
            'sort_order' => 0, 'is_active' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id, 'name_ar' => 'خاتم', 'slug' => 'legacy-guard-ring',
            'silver_purity' => '925', 'price' => 10, 'stock_quantity' => 1,
            'reserved_quantity' => 0, 'is_active' => true, 'is_featured' => false,
        ]);
        $admin = User::factory()->create(['role' => 'admin', 'section_id' => null]);

        $this->actingAs($admin)->get(route('admin.products.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.products.create'))->assertForbidden();
        $this->actingAs($admin)->post(route('admin.products.store'), [])->assertForbidden();
        $this->actingAs($admin)->get(route('admin.products.edit', $product))->assertForbidden();
        $this->actingAs($admin)->delete(route('admin.products.destroy', $product))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.categories.index'))->assertForbidden();
        $this->actingAs($admin)->post(route('admin.categories.store'), [])->assertForbidden();
        $this->actingAs($admin)->put(route('admin.categories.update', $category), [])->assertForbidden();
        $this->actingAs($admin)->delete(route('admin.categories.destroy', $category))->assertForbidden();
    }

    public function test_admin_login_page_remains_available(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('تسجيل دخول الإدارة')
            ->assertSee('البريد الإلكتروني')
            ->assertSee('كلمة المرور');
    }

    public function test_logout_invalidates_admin_access_and_returns_to_login(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.logout'))
            ->assertRedirectToRoute('admin.login');

        $this->assertGuest();
    }

    public function test_public_storefront_has_no_admin_or_login_entry(): void
    {
        $this->get(route('store.home'))
            ->assertOk()
            ->assertDontSee(route('admin.entry'))
            ->assertDontSee(route('admin.login'))
            ->assertDontSee('إدارة المتجر')
            ->assertDontSee('تسجيل الدخول');
    }
}
