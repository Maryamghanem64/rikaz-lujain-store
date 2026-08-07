<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentProof;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentProofService
{
    public function __construct(
        private InventoryService $inventoryService
    ) {}

    public function verify(
        Order $order,
        PaymentProof $proof,
        User $admin
    ): Order {
        return DB::transaction(function () use (
            $order,
            $proof,
            $admin
        ) {
            $lockedOrder = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedProof = PaymentProof::query()
                ->whereKey($proof->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedProof->order_id !== $lockedOrder->id) {
                throw ValidationException::withMessages([
                    'payment_proof' => 'إثبات الدفع لا يعود لهذا الطلب.',
                ]);
            }

            if ($lockedOrder->payment_method !== 'whish') {
                throw ValidationException::withMessages([
                    'payment' => 'هذا الطلب لا يستخدم Whish Money.',
                ]);
            }

            /*
             * Idempotency:
             * إذا كان الدفع verified سابقًا،
             * لا نخصم المخزون مرة ثانية.
             */
            if (
                $lockedProof->review_status === 'verified' &&
                $lockedOrder->payment_status === 'verified' &&
                in_array(
                    $lockedOrder->status,
                    [
                        'confirmed',
                        'preparing',
                        'shipped',
                        'delivered',
                    ],
                    true
                )
            ) {
                return $lockedOrder;
            }

            if (
                $lockedOrder->status !==
                'awaiting_payment_verification'
            ) {
                throw ValidationException::withMessages([
                    'status' => 'حالة الطلب لا تسمح بالتحقق من الدفع.',
                ]);
            }

            if ($lockedProof->review_status !== 'pending') {
                throw ValidationException::withMessages([
                    'payment_proof' => 'تمت مراجعة هذا الإيصال مسبقًا.',
                ]);
            }

            /*
             * Reservation → final sale
             */
            $this->inventoryService
                ->convertReservationToSale(
                    $lockedOrder
                );

            $lockedProof->update([
                'review_status' => 'verified',
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'rejection_reason' => null,
            ]);

            $lockedOrder->update([
                'payment_status' => 'verified',
                'status' => 'confirmed',
                'reservation_expires_at' => null,
            ]);

            $lockedOrder->statusHistory()->create([
                'status' => 'confirmed',
                'changed_by' => $admin->id,
                'note' => 'تم التحقق من وصول دفعة Whish وتأكيد الطلب.',
            ]);

            return $lockedOrder->load([
                'items',
                'paymentProofs',
                'statusHistory',
            ]);
        });
    }

    public function reject(
        Order $order,
        PaymentProof $proof,
        User $admin,
        string $reason
    ): Order {
        return DB::transaction(function () use (
            $order,
            $proof,
            $admin,
            $reason
        ) {
            $lockedOrder = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedProof = PaymentProof::query()
                ->whereKey($proof->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedProof->order_id !== $lockedOrder->id) {
                throw ValidationException::withMessages([
                    'payment_proof' => 'إثبات الدفع لا يعود لهذا الطلب.',
                ]);
            }

            if ($lockedOrder->payment_method !== 'whish') {
                throw ValidationException::withMessages([
                    'payment' => 'هذا الطلب لا يستخدم Whish Money.',
                ]);
            }

            /*
             * Idempotent reject
             */
            if (
                $lockedProof->review_status === 'rejected' &&
                $lockedOrder->payment_status === 'rejected' &&
                $lockedOrder->status === 'payment_rejected'
            ) {
                return $lockedOrder;
            }

            if (
                $lockedOrder->status !==
                'awaiting_payment_verification'
            ) {
                throw ValidationException::withMessages([
                    'status' => 'حالة الطلب لا تسمح برفض الإيصال.',
                ]);
            }

            if ($lockedProof->review_status !== 'pending') {
                throw ValidationException::withMessages([
                    'payment_proof' => 'تمت مراجعة هذا الإيصال مسبقًا.',
                ]);
            }

            $lockedProof->update([
                'review_status' => 'rejected',
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'rejection_reason' => $reason,
            ]);

            $lockedOrder->update([
                'payment_status' => 'rejected',
                'status' => 'payment_rejected',
            ]);

            $lockedOrder->statusHistory()->create([
                'status' => 'payment_rejected',
                'changed_by' => $admin->id,
                'note' => 'تم رفض إثبات الدفع. السبب: '.
                    $reason,
            ]);

            return $lockedOrder->load([
                'paymentProofs',
                'statusHistory',
            ]);
        });
    }
}
