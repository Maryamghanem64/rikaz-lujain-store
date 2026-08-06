<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    public function uploadProductImage(UploadedFile $file): array
    {
        return $this->upload(
            $file,
            'products'
        );
    }

    public function uploadPaymentProof(UploadedFile $file): array
    {
        return $this->upload(
            $file,
            'payment-proofs'
        );
    }

    private function upload(
        UploadedFile $file,
        string $folder
    ): array {
        $path = $file->store(
            $folder,
            'public'
        );

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

        Storage::disk('public')
            ->delete($publicId);
    }
}