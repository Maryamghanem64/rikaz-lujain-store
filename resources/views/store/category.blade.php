@extends('layouts.store')
@section('title', $category->name_ar . ' | ' . $section->name_ar)
@section('content')
<nav class="border-b border-line-200 bg-white" aria-label="مسار الصفحة"><div class="container-shell flex items-center gap-2 overflow-hidden py-3 text-[11px] text-muted-600"><a href="{{ route('store.home') }}">الرئيسية</a><span>/</span><a href="{{ route('store.section', $section->slug) }}">{{ $section->name_ar }}</a><span>/</span><span class="truncate text-ink-900">{{ $category->name_ar }}</span></div></nav>
<header class="border-b border-line-200 bg-ivory-100"><div class="container-shell flex min-h-44 items-end justify-between gap-6 py-8"><div><p class="text-xs tracking-[.2em] text-lujain-700">{{ strtoupper($section->slug) }}</p><h1 class="mt-3 text-3xl font-semibold sm:text-4xl">{{ $category->name_ar }}</h1></div><p class="pb-1 text-xs text-muted-600">{{ $products->total() }} قطعة</p></div></header>
<section class="container-shell py-9 sm:py-12">
    @include('store.partials.catalog-filters')
    @if ($products->isNotEmpty())<div class="grid grid-cols-2 gap-3 sm:gap-5 md:grid-cols-3 lg:grid-cols-4">@foreach ($products as $product) @include('store.partials.product-card', ['product' => $product]) @endforeach</div><div class="mt-10">{{ $products->links() }}</div>@else<div class="rounded-sm border border-line-200 bg-white py-14 text-center"><h2 class="text-xl font-semibold">لم نجد قطعًا مطابقة</h2><p class="mt-2 text-sm text-muted-600">جرّب إعادة ضبط البحث والتصفية.</p><a href="{{ route('store.section', $section->slug) }}" class="store-text-link mt-5">العودة إلى {{ $section->name_ar }}</a></div>@endif
</section>
@endsection
