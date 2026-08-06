<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
#[Fillable([
    'order_number',
    'customer_name',
    'customer_phone',
    'customer_whatsapp',
    'delivery_zone_id',
    'delivery_zone_name',
    'address',
    'notes',
    'subtotal',
    'delivery_fee',
    'total',
    'payment_method',
    'payment_status',
    'status',
    'reservation_expires_at',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'total' => 'decimal:2',
            'reservation_expires_at' => 'datetime',
        ];
    }

    public function deliveryZone(): BelongsTo
    {
        return $this->belongsTo(DeliveryZone::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    public function paymentProofs(): HasMany
{
    return $this->hasMany(PaymentProof::class);
}

public function latestPaymentProof(): HasOne
{
    return $this->hasOne(PaymentProof::class)->latestOfMany();
}

public function statusHistory(): HasMany
{
    return $this->hasMany(OrderStatusHistory::class)
        ->orderBy('created_at');
}
}