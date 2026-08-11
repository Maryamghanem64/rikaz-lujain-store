<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CartService
{
    private string $sessionKey = 'cart';

    public function raw(): array
    {
        return session()->get($this->sessionKey, []);
    }

    public function items(): Collection
    {
        $cart = $this->raw();

        if (empty($cart)) {
            return collect();
        }

        $products = Product::query()
            ->with([
                'category.section',
                'images',
            ])
            ->storefrontAvailable()
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        return collect($cart)
            ->map(function ($quantity, $productId) use ($products) {

                $product = $products->get((int) $productId);

                if (! $product) {
                    return null;
                }

                $quantity = (int) $quantity;

                return [
                    'product' => $product,

                    'quantity' => $quantity,

                    'unit_price' => (float) $product->price,

                    'subtotal' => (float) $product->price * $quantity,
                ];
            })
            ->filter()
            ->values();
    }

    public function add(
        Product $product,
        int $quantity = 1
    ): void {
        if (! $product->isAvailableOnStorefront()) {
            throw ValidationException::withMessages([
                'quantity' => 'هذا المنتج غير متاح حاليًا.',
            ]);
        }

        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'يجب أن تكون الكمية واحدًا على الأقل.',
            ]);
        }

        $cart = $this->raw();

        $currentQuantity =
            (int) ($cart[$product->id] ?? 0);

        $newQuantity =
            $currentQuantity + $quantity;

        if (
            $newQuantity >
            $product->available_quantity
        ) {
            throw ValidationException::withMessages([
                'quantity' => 'الكمية المطلوبة غير متوفرة.',
            ]);
        }

        $cart[$product->id] = $newQuantity;

        session()->put(
            $this->sessionKey,
            $cart
        );
    }

    public function update(
        Product $product,
        int $quantity
    ): void {
        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'يجب أن تكون الكمية واحدًا على الأقل.',
            ]);
        }

        $product->refresh();

        if (! $product->isAvailableOnStorefront()) {
            throw ValidationException::withMessages([
                'quantity' => 'هذا المنتج لم يعد متاحًا.',
            ]);
        }

        if (
            $quantity >
            $product->available_quantity
        ) {
            throw ValidationException::withMessages([
                'quantity' => 'الكمية المطلوبة أكبر من الكمية المتوفرة.',
            ]);
        }

        $cart = $this->raw();

        if (! isset($cart[$product->id])) {
            return;
        }

        $cart[$product->id] = $quantity;

        session()->put(
            $this->sessionKey,
            $cart
        );
    }

    public function remove(Product $product): void
    {
        $cart = $this->raw();

        unset($cart[$product->id]);

        session()->put(
            $this->sessionKey,
            $cart
        );
    }

    public function clear(): void
    {
        session()->forget(
            $this->sessionKey
        );
    }

    public function subtotal(): float
    {
        return $this->items()
            ->sum('subtotal');
    }

    public function count(): int
    {
        return collect($this->raw())
            ->sum();
    }

    public function isEmpty(): bool
    {
        return empty($this->raw());
    }
}
