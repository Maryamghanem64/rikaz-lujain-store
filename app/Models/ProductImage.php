<?php

namespace App\Models;

use Database\Factories\ProductImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'product_id',
    'url',
    'public_id',
    'is_primary',
    'sort_order',
])]
class ProductImage extends Model
{
    /** @use HasFactory<ProductImageFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function displayUrl(): string
    {
        $diskName = config('filesystems.product_images_disk');
        $diskConfig = config("filesystems.disks.{$diskName}");

        if (($diskConfig['driver'] ?? null) === 'local'
            && ($diskConfig['visibility'] ?? null) === 'public') {
            return Storage::disk($diskName)->url($this->public_id);
        }

        return route('media.products.show', $this);
    }

    public function isDisplayableOnStorefront(): bool
    {
        $diskName = config('filesystems.product_images_disk');
        $diskConfig = config("filesystems.disks.{$diskName}");

        if (($diskConfig['driver'] ?? null) !== 'local' || blank($this->public_id)) {
            return true;
        }

        $localPath = Storage::disk($diskName)->path($this->public_id);
        $dimensions = is_file($localPath) ? @getimagesize($localPath) : false;

        if ($dimensions === false || ! str_starts_with((string) ($dimensions['mime'] ?? ''), 'image/')) {
            return false;
        }

        [$width, $height] = $dimensions;

        // Product photography should be square or portrait. Reject wide
        // development screenshots instead of presenting them as jewelry.
        return $width > 0 && $height > 0 && ($width / $height) <= 1.35;
    }
}
