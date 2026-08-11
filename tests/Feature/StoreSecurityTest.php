<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_slug_is_unique_only_within_its_section(): void
    {
        $rikaz = $this->section('rikaz');
        $lujain = $this->section('lujain');
        $rikazAdmin = User::factory()->create(['role' => 'admin', 'section_id' => $rikaz->id]);
        $lujainAdmin = User::factory()->create(['role' => 'admin', 'section_id' => $lujain->id]);
        Category::create([
            'section_id' => $rikaz->id, 'name_ar' => 'خواتم', 'slug' => 'rings',
            'sort_order' => 0, 'is_active' => true,
        ]);

        $this->actingAs($lujainAdmin)->post(route('admin.categories.store'), [
            'section_id' => $lujain->id, 'name_ar' => 'خواتم', 'slug' => 'rings',
            'sort_order' => 0, 'is_active' => '1',
        ])->assertSessionHasNoErrors();

        $this->actingAs($rikazAdmin)->post(route('admin.categories.store'), [
            'section_id' => $rikaz->id, 'name_ar' => 'مكرر', 'slug' => 'rings',
            'sort_order' => 0, 'is_active' => '1',
        ])->assertSessionHasErrors('slug');
    }

    public function test_success_page_requires_the_order_id_in_the_current_session(): void
    {
        $order = Order::create([
            'order_number' => 'RL-TEST', 'customer_name' => 'عميل', 'customer_phone' => '70',
            'delivery_zone_name' => 'بيروت', 'address' => 'عنوان', 'subtotal' => 10,
            'delivery_fee' => 2, 'total' => 12, 'payment_method' => 'cash',
            'payment_status' => 'cash_pending', 'status' => 'new_cash',
        ]);

        $this->get(route('checkout.success', $order->order_number))->assertForbidden();
    }

    private function section(string $slug): Section
    {
        return Section::create([
            'name_ar' => $slug, 'slug' => $slug, 'audience' => 'test', 'is_active' => true,
        ]);
    }
}
