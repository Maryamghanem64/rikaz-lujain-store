<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Services\PaymentProofService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentProofController extends Controller
{
    public function verify(
        Request $request,
        Order $order,
        PaymentProof $proof,
        PaymentProofService $paymentProofService
    ): RedirectResponse {

        $paymentProofService->verify(
            $order,
            $proof,
            $request->user()
        );

        return back()->with(
            'success',
            'تم التحقق من دفعة Whish وتأكيد الطلب.'
        );
    }


    public function reject(
        Request $request,
        Order $order,
        PaymentProof $proof,
        PaymentProofService $paymentProofService
    ): RedirectResponse {

        $validated = $request->validate([
            'rejection_reason' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        $paymentProofService->reject(
            $order,
            $proof,
            $request->user(),
            $validated['rejection_reason']
        );

        return back()->with(
            'success',
            'تم رفض إثبات الدفع.'
        );
    }
}