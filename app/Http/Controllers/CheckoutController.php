<?php

namespace App\Http\Controllers;

use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\Setting;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function show(
        CartService $cart
    ): View|RedirectResponse {

        if ($cart->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->withErrors([
                    'cart' => 'السلة فارغة.',
                ]);
        }

        $zones = DeliveryZone::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $settings = Setting::first();

        return view('store.checkout', [
            'items' => $cart->items(),
            'subtotal' => $cart->subtotal(),
            'zones' => $zones,
            'settings' => $settings,
        ]);
    }

    public function store(
        Request $request,
        OrderService $orderService,
        CartService $cart
    ): RedirectResponse {

        $validated = $request->validate([
            'customer_name' => [
                'required',
                'string',
                'max:255',
            ],

            'customer_phone' => [
                'required',
                'string',
                'max:50',
            ],

            'customer_whatsapp' => [
                'nullable',
                'string',
                'max:50',
            ],

            'delivery_zone_id' => [
                'required',

                Rule::exists(
                    'delivery_zones',
                    'id'
                )->where(
                    fn ($query) => $query->where(
                        'is_active',
                        true
                    )
                ),
            ],

            'address' => [
                'required',
                'string',
                'max:1000',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'payment_method' => [
                'required',
                Rule::in([
                    'cash',
                    'whish',
                ]),
            ],

            'payment_proof' => [
                Rule::requiredIf(
                    $request->input('payment_method')
                        === 'whish'
                ),

                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'policy_agreement' => [
                'accepted',
            ],
        ]);

        unset(
            $validated['policy_agreement']
        );

        if (
            blank($validated['customer_whatsapp'] ?? null)
        ) {
            $validated['customer_whatsapp'] =
                $validated['customer_phone'];
        }

        $order = $orderService->create(
            $validated
        );

        $cart->clear();

        session()->put(
            'last_order_id',
            $order->id
        );

        return redirect()->route(
            'checkout.success',
            $order->order_number
        );
    }

    public function success(
        string $orderNumber
    ): View {

        $order = Order::query()
            ->with([
                'items',
                'paymentProofs',
            ])
            ->where(
                'order_number',
                $orderNumber
            )
            ->firstOrFail();

        abort_unless(
            (int) session('last_order_id')
                === $order->id,
            403
        );

        $settings = Setting::first();

        return view(
            'store.order-success',
            compact(
                'order',
                'settings'
            )
        );
    }
}
