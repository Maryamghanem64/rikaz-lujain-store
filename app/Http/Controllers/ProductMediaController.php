<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductMediaController extends Controller
{
    public function show(ProductImage $productImage): StreamedResponse
    {
        $storageKey = $productImage->public_id;

        abort_unless(
            is_string($storageKey)
                && $storageKey !== ''
                && ! str_contains($storageKey, '..')
                && ! str_contains($storageKey, '\\')
                && ! str_starts_with($storageKey, '/'),
            404
        );

        $disk = Storage::disk(config('filesystems.product_images_disk'));

        abort_unless($disk->exists($storageKey), 404);

        $mimeType = $disk->mimeType($storageKey);

        abort_unless(in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true), 404);

        return $disk->response(
            $storageKey,
            null,
            [
                'Cache-Control' => 'public, max-age=86400',
                'Content-Type' => $mimeType,
                'X-Content-Type-Options' => 'nosniff',
            ],
            'inline'
        );
    }
}
