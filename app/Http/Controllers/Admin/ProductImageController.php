<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductImageController extends Controller
{
    public function __construct(
        private ImageService $imageService
    ) {
    }

    public function store(
        Request $request,
        Product $product
    ): RedirectResponse {
        $validated = $request->validate([
            'images' => [
                'required',
                'array',
                'min:1',
                'max:8',
            ],

            'images.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        $hasPrimary = $product->images()
            ->where('is_primary', true)
            ->exists();

        $nextSortOrder =
            ((int) $product->images()->max('sort_order')) + 1;

        foreach ($validated['images'] as $index => $file) {
            $uploaded =
                $this->imageService->uploadProductImage($file);

            $product->images()->create([
                'url' => $uploaded['url'],
                'public_id' => $uploaded['public_id'],

                'is_primary' =>
                    ! $hasPrimary && $index === 0,

                'sort_order' =>
                    $nextSortOrder + $index,
            ]);
        }

        return back()->with(
            'success',
            'تم رفع الصور بنجاح.'
        );
    }

    public function setPrimary(
        Product $product,
        ProductImage $image
    ): RedirectResponse {
        abort_unless(
            $image->product_id === $product->id,
            404
        );

        DB::transaction(function () use ($product, $image) {
            $product->images()->update([
                'is_primary' => false,
            ]);

            $image->update([
                'is_primary' => true,
            ]);
        });

        return back()->with(
            'success',
            'تم تعيين الصورة الرئيسية.'
        );
    }

    public function updateOrder(
        Request $request,
        Product $product
    ): RedirectResponse {
        $validated = $request->validate([
            'orders' => [
                'required',
                'array',
            ],

            'orders.*' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        foreach ($validated['orders'] as $imageId => $sortOrder) {
            $product->images()
                ->whereKey($imageId)
                ->update([
                    'sort_order' => $sortOrder,
                ]);
        }

        return back()->with(
            'success',
            'تم تحديث ترتيب الصور.'
        );
    }

    public function destroy(
        Product $product,
        ProductImage $image
    ): RedirectResponse {
        abort_unless(
            $image->product_id === $product->id,
            404
        );

        $wasPrimary = $image->is_primary;

        $this->imageService->delete(
            $image->public_id
        );

        $image->delete();

        if ($wasPrimary) {
            $nextImage = $product->images()
                ->orderBy('sort_order')
                ->first();

            if ($nextImage) {
                $nextImage->update([
                    'is_primary' => true,
                ]);
            }
        }

        return back()->with(
            'success',
            'تم حذف الصورة بنجاح.'
        );
    }
}