<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReleaseExpiredReservations extends Command
{
    protected $signature = 'orders:release-expired-reservations';

    protected $description =
        'Release expired order reservations and cancel pending orders.';

    public function handle(
        InventoryService $inventoryService
    ): int {
        $orderIds = Order::query()
            ->whereIn('status', [
                'new_cash',
                'awaiting_payment_verification',
                'payment_rejected',
            ])
            ->whereNotNull('reservation_expires_at')
            ->where(
                'reservation_expires_at',
                '<=',
                now()
            )
            ->pluck('id');

        if ($orderIds->isEmpty()) {
            $this->info(
                'No expired reservations found.'
            );

            return self::SUCCESS;
        }

        $released = 0;

        foreach ($orderIds as $orderId) {

            DB::transaction(function () use (
                $orderId,
                $inventoryService,
                &$released
            ) {

                /*
                |--------------------------------------------------------------------------
                | Lock order so expiry cannot race with admin confirmation
                |--------------------------------------------------------------------------
                */

                $order = Order::query()
                    ->whereKey($orderId)
                    ->lockForUpdate()
                    ->first();

                if (! $order) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Re-check status after locking
                |--------------------------------------------------------------------------
                */

                if (
                    ! in_array(
                        $order->status,
                        [
                            'new_cash',
                            'awaiting_payment_verification',
                            'payment_rejected',
                        ],
                        true
                    )
                ) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Re-check deadline
                |--------------------------------------------------------------------------
                */

                if (
                    ! $order->reservation_expires_at ||
                    $order->reservation_expires_at->isFuture()
                ) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Release reserved stock
                |--------------------------------------------------------------------------
                */

                $inventoryService
                    ->releaseReservation($order);

                /*
                |--------------------------------------------------------------------------
                | Cancel expired order
                |--------------------------------------------------------------------------
                */

                $order->update([
                    'status' => 'cancelled',
                    'reservation_expires_at' => null,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Audit history
                |--------------------------------------------------------------------------
                */

                $order->statusHistory()->create([
                    'status' => 'cancelled',
                    'changed_by' => null,
                    'note' => 'تم إلغاء الطلب تلقائيًا لانتهاء مهلة حجز المنتجات.',
                ]);

                $released++;
            });
        }

        $this->info(
            "Released {$released} expired reservation(s)."
        );

        return self::SUCCESS;
    }
}
