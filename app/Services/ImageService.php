<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    public function uploadProductImage(UploadedFile $file): array
    {
        $path = $file->store('products', 'public');

        return [
            'url' => Storage::url($path),
            'public_id' => $path,
        ];
    }

    public function delete(?string $publicId): void
    {
        if (blank($publicId)) {
            return;
        }

        Storage::disk('public')->delete($publicId);
    }
}