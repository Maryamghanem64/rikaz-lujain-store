<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $section = $request->user()->section;
        $catalogProducts = Product::query()
            ->when(
                $section,
                fn ($query) => $query->whereHas(
                    'category',
                    fn ($categoryQuery) => $categoryQuery->where('section_id', $section->id)
                ),
                fn ($query) => $query->whereRaw('1 = 0')
            );

        $stats = [
            'total_orders' => Order::count(),

            'new_cash' => Order::query()
                ->where('status', 'new_cash')
                ->count(),

            'whish_pending' => Order::query()
                ->where(
                    'status',
                    'awaiting_payment_verification'
                )
                ->count(),

            'active_orders' => Order::query()
                ->whereIn('status', [
                    'confirmed',
                    'preparing',
                    'shipped',
                ])
                ->count(),

            'delivered_orders' => Order::query()
                ->where('status', 'delivered')
                ->count(),

            'cancelled_orders' => Order::query()
                ->where('status', 'cancelled')
                ->count(),

            'total_sales' => Order::query()
                ->where('status', 'delivered')
                ->sum('total'),

            'total_products' => (clone $catalogProducts)->count(),

            'total_categories' => $section
                ? Category::where('section_id', $section->id)->count()
                : 0,

            'low_stock_products' => (clone $catalogProducts)
                ->where('is_active', true)
                ->whereRaw(
                    '(stock_quantity - reserved_quantity) <= 1'
                )
                ->count(),

            'out_of_stock_products' => (clone $catalogProducts)
                ->where('is_active', true)
                ->whereRaw(
                    '(stock_quantity - reserved_quantity) <= 0'
                )
                ->count(),
        ];

        $recentOrders = Order::query()
            ->latest()
            ->take(8)
            ->get();

        $lowStockProducts = Product::query()
            ->with([
                'category.section',
                'primaryImage',
            ])
            ->when(
                $section,
                fn ($query) => $query->whereHas(
                    'category',
                    fn ($categoryQuery) => $categoryQuery->where('section_id', $section->id)
                ),
                fn ($query) => $query->whereRaw('1 = 0')
            )
            ->where('is_active', true)
            ->whereRaw(
                '(stock_quantity - reserved_quantity) <= 1'
            )
            ->orderByRaw(
                '(stock_quantity - reserved_quantity) ASC'
            )
            ->take(8)
            ->get();

        return view(
            'admin.dashboard',
            compact(
                'stats',
                'recentOrders',
                'lowStockProducts', 'section'
            )
        );
    }
}
