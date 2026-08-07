<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    /**
     * Convert a pending reservation into a completed sale.
     *
     * Example:
     *
     * Before:
     * stock_quantity = 1
     * reserved_quantity = 1
     *
     * After:
     * stock_quantity = 0
     * reserved_quantity = 0
     */
    public function convertReservationToSale(
        Order $order
    ): void {
        $order->loadMissing('items');

        foreach ($order->items as $item) {

            if (! $item->product_id) {
                throw ValidationException::withMessages([
                    'inventory' => 'تعذر العثور على أحد المنتجات المرتبطة بالطلب.',
                ]);
            }

            $product = Product::query()
                ->whereKey($item->product_id)
                ->lockForUpdate()
                ->first();

            if (! $product) {
                throw ValidationException::withMessages([
                    'inventory' => "المنتج {$item->product_name_ar} غير موجود.",
                ]);
            }

            $quantity = (int) $item->quantity;

            if (
                $product->reserved_quantity <
                $quantity
            ) {
                throw ValidationException::withMessages([
                    'inventory' => "الكمية المحجوزة للمنتج {$item->product_name_ar} غير صحيحة.",
                ]);
            }

            if (
                $product->stock_quantity <
                $quantity
            ) {
                throw ValidationException::withMessages([
                    'inventory' => "المخزون الفعلي للمنتج {$item->product_name_ar} غير كافٍ.",
                ]);
            }

            $product->decrement(
                'reserved_quantity',
                $quantity
            );

            $product->decrement(
                'stock_quantity',
                $quantity
            );
        }
    }

    /**
     * Release a pending reservation.
     *
     * Example:
     *
     * Before:
     * stock_quantity = 1
     * reserved_quantity = 1
     *
     * After:
     * stock_quantity = 1
     * reserved_quantity = 0
     */
    public function releaseReservation(
        Order $order
    ): void {
        $order->loadMissing('items');

        foreach ($order->items as $item) {

            if (! $item->product_id) {
                continue;
            }

            $product = Product::query()
                ->whereKey($item->product_id)
                ->lockForUpdate()
                ->first();

            if (! $product) {
                continue;
            }

            $quantity = (int) $item->quantity;

            /*
             * If reservation was already released,
             * don't make reserved_quantity negative.
             */
            if (
                $product->reserved_quantity <
                $quantity
            ) {
                continue;
            }

            $product->decrement(
                'reserved_quantity',
                $quantity
            );
        }
    }

    /**
     * Restore stock if an already confirmed sale
     * is later cancelled.
     */
    public function restoreSale(
        Order $order
    ): void {
        $order->loadMissing('items');

        foreach ($order->items as $item) {

            if (! $item->product_id) {
                continue;
            }

            $product = Product::query()
                ->whereKey($item->product_id)
                ->lockForUpdate()
                ->first();

            if (! $product) {
                continue;
            }

            $product->increment(
                'stock_quantity',
                (int) $item->quantity
            );
        }
    }
}
