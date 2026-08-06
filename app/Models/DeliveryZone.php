<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
#[Fillable([
    'name_ar',
    'fee',
    'is_active',
    'sort_order',
])]
class DeliveryZone extends Model
{
    protected function casts(): array
    {
        return [
            'fee' => 'decimal:2',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
    public function orders(): HasMany
{
    return $this->hasMany(Order::class);
}
}