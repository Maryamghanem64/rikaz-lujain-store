<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    public function uploadProductImage(UploadedFile $file): array
    {
        $disk = config('filesystems.product_images_disk');
        $directory = config("filesystems.disks.{$disk}.upload_directory", '');
        $path = $file->store($directory, $disk);
        $diskConfig = config("filesystems.disks.{$disk}");

        return [
            'url' => ($diskConfig['driver'] ?? null) === 'local'
                && ($diskConfig['visibility'] ?? null) === 'public'
                    ? Storage::disk($disk)->url($path)
                    : $path,
            'public_id' => $path,
        ];
    }

    public function uploadPaymentProof(UploadedFile $file): array
    {
        $path = $file->store('', config('filesystems.payment_proofs_disk'));

        return [
            // `url` is required by the existing schema. It intentionally holds
            // an internal key, never a web-accessible URL.
            'url' => $path,
            'public_id' => $path,
        ];
    }

    public function deleteProductImage(?string $publicId): void
    {
        if (blank($publicId)) {
            return;
        }

        Storage::disk(config('filesystems.product_images_disk'))
            ->delete($publicId);
    }

    public function deletePaymentProof(?string $storageKey): void
    {
        if (blank($storageKey)) {
            return;
        }

        Storage::disk(config('filesystems.payment_proofs_disk'))->delete($storageKey);
    }
}
