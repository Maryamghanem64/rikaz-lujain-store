<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Services\PaymentProofService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentProofController extends Controller
{
    public function file(Order $order, PaymentProof $proof): StreamedResponse
    {
        abort_unless($proof->order_id === $order->id, 404);

        $storageKey = $proof->public_id;

        abort_unless(
            is_string($storageKey)
                && $storageKey !== ''
                && ! str_contains($storageKey, '..')
                && ! str_contains($storageKey, '\\')
                && ! str_starts_with($storageKey, '/'),
            404
        );

        $disk = Storage::disk(config('filesystems.payment_proofs_disk'));

        abort_unless($disk->exists($storageKey), 404);

        return $disk->response(
            $storageKey,
            null,
            ['X-Content-Type-Options' => 'nosniff'],
            'inline'
        );
    }

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
