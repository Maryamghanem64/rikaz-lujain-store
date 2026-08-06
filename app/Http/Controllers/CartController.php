<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(
        CartService $cart
    ): View {
        return view('store.cart', [
            'items' => $cart->items(),
            'subtotal' => $cart->subtotal(),
        ]);
    }


    public function store(
        Request $request,
        Product $product,
        CartService $cart
    ): RedirectResponse {
        $validated = $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $cart->add(
            $product,
            (int) $validated['quantity']
        );

        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'تمت إضافة المنتج إلى السلة.'
            );
    }


    public function update(
        Request $request,
        Product $product,
        CartService $cart
    ): RedirectResponse {
        $validated = $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $cart->update(
            $product,
            (int) $validated['quantity']
        );

        return back()->with(
            'success',
            'تم تحديث الكمية.'
        );
    }


    public function destroy(
        Product $product,
        CartService $cart
    ): RedirectResponse {
        $cart->remove($product);

        return back()->with(
            'success',
            'تم حذف المنتج من السلة.'
        );
    }


    public function clear(
        CartService $cart
    ): RedirectResponse {
        $cart->clear();

        return back()->with(
            'success',
            'تم إفراغ السلة.'
        );
    }
}