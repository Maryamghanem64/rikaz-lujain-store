<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with([
                'items',
                'latestPaymentProof',
            ])

            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'status',
                    $request->string('status')->toString()
                )
            )

            ->when(
                $request->filled('payment_method'),
                fn ($query) => $query->where(
                    'payment_method',
                    $request->string('payment_method')->toString()
                )
            )

            ->when(
                $request->filled('q'),
                function ($query) use ($request) {

                    $search =
                        $request->string('q')->toString();

                    $query->where(function ($query) use ($search) {

                        $query
                            ->where(
                                'order_number',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'customer_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'customer_phone',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            )

            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view(
            'admin.orders.index',
            compact('orders')
        );
    }

    public function show(Order $order): View
    {
        $order->load([
            'items.product',
            'deliveryZone',
            'paymentProofs.reviewer',
            'statusHistory.changedBy',
        ]);

        return view(
            'admin.orders.show',
            compact('order')
        );
    }

    public function confirmCash(
        Request $request,
        Order $order,
        OrderWorkflowService $workflow
    ): RedirectResponse {

        $validated = $request->validate([
            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $workflow->confirmCash(
            $order,
            $request->user(),
            $validated['note'] ?? null
        );

        return back()->with(
            'success',
            'تم تأكيد الطلب النقدي بنجاح.'
        );
    }

    public function cancel(
        Request $request,
        Order $order,
        OrderWorkflowService $workflow
    ): RedirectResponse {

        $validated = $request->validate([
            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $workflow->cancel(
            $order,
            $request->user(),
            $validated['note'] ?? null
        );

        return back()->with(
            'success',
            'تم إلغاء الطلب بنجاح.'
        );
    }

    public function advance(
        Request $request,
        Order $order,
        OrderWorkflowService $workflow
    ): RedirectResponse {

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'preparing',
                    'shipped',
                    'delivered',
                ]),
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $workflow->advance(
            $order,
            $request->user(),
            $validated['status'],
            $validated['note'] ?? null
        );

        return back()->with(
            'success',
            'تم تحديث حالة الطلب بنجاح.'
        );
    }

    public function releaseReservation(
        Request $request,
        Order $order,
        OrderWorkflowService $workflow
    ): RedirectResponse {

        $validated = $request->validate([
            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $note = $validated['note']
            ?? 'تم تحرير حجز المنتجات يدويًا من لوحة الإدارة.';

        $workflow->releasePendingReservation(
            $order,
            $request->user(),
            $note
        );

        return back()->with(
            'success',
            'تم تحرير حجز المنتجات وإلغاء الطلب.'
        );
    }
}
