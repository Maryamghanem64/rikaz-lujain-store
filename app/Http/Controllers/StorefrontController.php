<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Section;
use Illuminate\View\View;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function home(): View
    {
        $sections = Section::query()
            ->where('is_active', true)
            ->with([
                'categories' => fn ($query) =>
                    $query->where('is_active', true)
                        ->orderBy('sort_order'),
            ])
            ->get();

        $featuredProducts = Product::query()
            ->with([
                'category.section',
                'primaryImage',
            ])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->take(8)
            ->get();

        $newProducts = Product::query()
            ->with([
                'category.section',
                'primaryImage',
            ])
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        return view(
            'store.home',
            compact(
                'sections',
                'featuredProducts',
                'newProducts'
            )
        );
    }

    public function section(
    Request $request,
    string $sectionSlug
): View {
    $section = Section::query()
        ->where('slug', $sectionSlug)
        ->where('is_active', true)
        ->with([
            'categories' => fn ($query) =>
                $query->where('is_active', true)
                    ->orderBy('sort_order'),
        ])
        ->firstOrFail();

    $productsQuery = Product::query()
        ->with([
            'category.section',
            'primaryImage',
        ])
        ->where('is_active', true)
        ->whereHas(
            'category',
            fn ($query) =>
                $query
                    ->where('section_id', $section->id)
                    ->where('is_active', true)
        );

    if ($request->filled('q')) {
        $search = $request->string('q')->toString();

        $productsQuery->where(function ($query) use ($search) {
            $query
                ->where('name_ar', 'like', "%{$search}%")
                ->orWhere('stone_name', 'like', "%{$search}%")
                ->orWhere('silver_purity', 'like', "%{$search}%");
        });
    }

    if ($request->filled('stone')) {
        $productsQuery->where(
            'stone_name',
            $request->string('stone')->toString()
        );
    }

    if ($request->availability === 'available') {
        $productsQuery->whereColumn(
            'stock_quantity',
            '>',
            'reserved_quantity'
        );
    }

    if ($request->availability === 'sold_out') {
        $productsQuery->whereColumn(
            'stock_quantity',
            '<=',
            'reserved_quantity'
        );
    }

    match ($request->sort) {
        'price_asc' =>
            $productsQuery->orderBy('price'),

        'price_desc' =>
            $productsQuery->orderByDesc('price'),

        default =>
            $productsQuery->latest(),
    };

    $products = $productsQuery
        ->paginate(12)
        ->withQueryString();

    $stones = Product::query()
        ->where('is_active', true)
        ->whereNotNull('stone_name')
        ->where('stone_name', '!=', '')
        ->whereHas(
            'category',
            fn ($query) =>
                $query
                    ->where('section_id', $section->id)
                    ->where('is_active', true)
        )
        ->distinct()
        ->orderBy('stone_name')
        ->pluck('stone_name');

    return view(
        'store.section',
        compact(
            'section',
            'products',
            'stones'
        )
    );

    }

    public function category(
    Request $request,
    string $sectionSlug,
    string $categorySlug
): View {
    $section = Section::query()
        ->where('slug', $sectionSlug)
        ->where('is_active', true)
        ->firstOrFail();

    $category = Category::query()
        ->where('section_id', $section->id)
        ->where('slug', $categorySlug)
        ->where('is_active', true)
        ->firstOrFail();

    $productsQuery = Product::query()
        ->with([
            'category.section',
            'primaryImage',
        ])
        ->where('category_id', $category->id)
        ->where('is_active', true);

    if ($request->filled('q')) {
        $search = $request->string('q')->toString();

        $productsQuery->where(function ($query) use ($search) {
            $query
                ->where('name_ar', 'like', "%{$search}%")
                ->orWhere('stone_name', 'like', "%{$search}%")
                ->orWhere('silver_purity', 'like', "%{$search}%");
        });
    }

    if ($request->filled('stone')) {
        $productsQuery->where(
            'stone_name',
            $request->string('stone')->toString()
        );
    }

    if ($request->availability === 'available') {
        $productsQuery->whereColumn(
            'stock_quantity',
            '>',
            'reserved_quantity'
        );
    }

    if ($request->availability === 'sold_out') {
        $productsQuery->whereColumn(
            'stock_quantity',
            '<=',
            'reserved_quantity'
        );
    }

    match ($request->sort) {
        'price_asc' =>
            $productsQuery->orderBy('price'),

        'price_desc' =>
            $productsQuery->orderByDesc('price'),

        default =>
            $productsQuery->latest(),
    };

    $products = $productsQuery
        ->paginate(12)
        ->withQueryString();

    $stones = Product::query()
        ->where('category_id', $category->id)
        ->where('is_active', true)
        ->whereNotNull('stone_name')
        ->where('stone_name', '!=', '')
        ->distinct()
        ->orderBy('stone_name')
        ->pluck('stone_name');

    return view(
        'store.category',
        compact(
            'section',
            'category',
            'products',
            'stones'
        )
    );
}

    public function product(
        string $sectionSlug,
        string $productSlug
    ): View {
        $product = Product::query()
            ->with([
                'category.section',
                'images',
            ])
            ->where('slug', $productSlug)
            ->where('is_active', true)
            ->whereHas(
                'category.section',
                fn ($query) =>
                    $query
                        ->where('slug', $sectionSlug)
                        ->where('is_active', true)
            )
            ->firstOrFail();

        return view(
            'store.product',
            compact('product')
        );
    }
}