<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'category_id',
    'name_ar',
    'slug',
    'description_ar',
    'stone_name',
    'stone_weight',
    'silver_purity',
    'size',
    'price',
    'stock_quantity',
    'reserved_quantity',
    'is_active',
    'is_featured',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'stone_weight' => 'decimal:2',
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'reserved_quantity' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeStorefrontAvailable(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->whereHas(
                'category',
                fn (Builder $query) => $query
                    ->where('is_active', true)
                    ->whereHas(
                        'section',
                        fn (Builder $query) => $query
                            ->where('is_active', true)
                    )
            );
    }

    public function isAvailableOnStorefront(): bool
    {
        return self::query()
            ->whereKey($this->getKey())
            ->storefrontAvailable()
            ->exists();
    }

    protected function availableQuantity(): Attribute
    {
        return Attribute::get(
            fn (): int => max(
                0,
                $this->stock_quantity - $this->reserved_quantity
            )
        );
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)
            ->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)
            ->where('is_primary', true);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
