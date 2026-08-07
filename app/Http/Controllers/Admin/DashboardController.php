<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
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

            'low_stock_products' => Product::query()
                ->where('is_active', true)
                ->whereRaw(
                    '(stock_quantity - reserved_quantity) <= 1'
                )
                ->count(),

            'out_of_stock_products' => Product::query()
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
                'lowStockProducts'
            )
        );
    }
}