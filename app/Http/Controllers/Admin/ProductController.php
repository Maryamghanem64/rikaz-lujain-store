<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->section_id !== null, 403);

        $products = Product::with([
            'category.section',
            'primaryImage',
        ])
            ->whereHas('category', fn ($query) => $query->where('section_id', $request->user()->section_id))
            ->latest()
            ->paginate(20);

        return view(
            'admin.products.index',
            compact('products')
        );
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->section_id !== null, 403);

        $categories = Category::with('section')
            ->where('is_active', true)
            ->where('section_id', $request->user()->section_id)
            ->orderBy('sort_order')
            ->get();

        return view(
            'admin.products.create',
            compact('categories')
        );
    }

    public function store(
        StoreProductRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        $data['is_active'] =
            $request->boolean('is_active');

        $data['is_featured'] =
            $request->boolean('is_featured');

        $data['reserved_quantity'] = 0;

        $product = Product::create($data);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with(
                'success',
                'تم إنشاء المنتج بنجاح. يمكنك الآن إضافة الصور.'
            );
    }

    public function edit(Product $product): View
    {
        abort_unless(request()->user()->managesProduct($product), 403);

        $categories = Category::with('section')
            ->where('is_active', true)
            ->where('section_id', request()->user()->section_id)
            ->orderBy('sort_order')
            ->get();

        $product->load('images');

        return view(
            'admin.products.edit',
            compact('product', 'categories')
        );
    }

    public function update(
        UpdateProductRequest $request,
        Product $product
    ): RedirectResponse {
        abort_unless($request->user()->managesProduct($product), 403);

        $data = $request->validated();

        $data['is_active'] =
            $request->boolean('is_active');

        $data['is_featured'] =
            $request->boolean('is_featured');

        $product->update($data);

        return back()->with(
            'success',
            'تم تحديث المنتج بنجاح.'
        );
    }

    public function destroy(
        Product $product,
        ImageService $imageService
    ): RedirectResponse {
        abort_unless(request()->user()->managesProduct($product), 403);

        if ($product->orderItems()->exists()) {
            $product->update([
                'is_active' => false,
            ]);

            return back()->with(
                'success',
                'المنتج مرتبط بطلبات سابقة، لذلك تم إخفاؤه بدل حذفه.'
            );
        }

        $imagePublicIds = $product->images()
            ->pluck('public_id');

        $product->delete();

        $imagePublicIds->each(
            fn (?string $publicId) => $imageService->delete($publicId)
        );

        return redirect()
            ->route('admin.products.index')
            ->with(
                'success',
                'تم حذف المنتج بنجاح.'
            );
    }
}
