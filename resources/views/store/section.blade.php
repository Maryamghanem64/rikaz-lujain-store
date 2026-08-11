@extends('layouts.store')
@section('title', $section->name_ar.' | ركاز × لجين')
@section('content')
@php $isRikaz = $section->slug === 'rikaz'; @endphp

@if($isRikaz)
<section class="overflow-hidden border-b border-line-200 bg-[#FAF8F4]">
    <div class="rikaz-brand-grid container-shell grid gap-x-10 lg:min-h-[720px]">
        <div class="rikaz-brand-intro order-1 flex items-center py-10 sm:py-14 lg:py-16">
            <div class="max-w-lg">
                <div class="flex items-center gap-5">
                    <div class="brand-logo-circle brand-logo-circle-page shrink-0"><img src="{{ asset('images/branding/rikaz-logo.jpg') }}" alt="شعار ركاز الرسمي" class="h-full w-full rounded-full object-contain"></div>
                    <div><p class="eyebrow text-lujain-700">RIKAZ · COLLECTION</p><h1 class="editorial-title mt-2 text-5xl sm:text-6xl">ركاز</h1></div>
                </div>
                <p class="editorial-title mt-8 max-w-[19ch] text-2xl leading-[1.75] text-[#3A1018] sm:text-[1.8rem]">كنزٌ يختبئ في جوف الأرض، لا يظهر إلا لمن يستحقه.</p>
                <a href="#collection-products" class="store-btn-primary mt-8">تسوّق المجموعة</a>
            </div>
        </div>

        <figure class="rikaz-brand-main collection-media collection-media-hero order-2 relative -mx-4 h-[470px] sm:-mx-6 sm:h-[560px] md:h-[620px] lg:mx-0 lg:h-auto lg:min-h-0">
            <img src="{{ asset('images/editorial/rikaz-red-stone.jpg') }}" alt="خاتم ركاز الفضي بحجر أحمر في بيئة تراثية" fetchpriority="high" class="h-full w-full object-cover object-[50%_52%]">
            <figcaption class="image-caption">ركاز · خاتم فضة وحجر</figcaption>
        </figure>

        <div class="rikaz-brand-detail order-3 grid grid-cols-[132px_1fr] items-center gap-5 py-7 sm:grid-cols-[180px_1fr] lg:border-t lg:border-line-200 lg:py-6">
            <figure class="aspect-square overflow-hidden bg-[#e8e2dc]"><img src="{{ asset('images/editorial/rikaz-engraving-detail.jpg') }}" alt="تفصيل نقش على خاتم من ركاز" loading="lazy" class="h-full w-full object-cover object-center"></figure>
            <div><p class="text-xs font-semibold text-lujain-700">تفاصيل القطعة</p><p class="mt-2 max-w-xs text-sm leading-7 text-muted-600">حجر، فضة ونقش في لقطة قريبة.</p></div>
        </div>
    </div>
</section>
@else
<section class="border-b border-line-200 bg-[#FAF8F4]">
    <div class="container-shell py-10 sm:py-14 lg:py-16">
        <header class="grid items-end gap-7 border-b border-line-200 pb-9 md:grid-cols-[1fr_auto]">
            <div class="flex items-center gap-5">
                <div class="brand-logo-circle brand-logo-circle-page shrink-0"><img src="{{ asset('images/branding/lujain-logo.jpg') }}" alt="شعار لجين الرسمي" class="h-full w-full rounded-full object-contain"></div>
                <div><p class="eyebrow text-lujain-700">LUJAIN · COLLECTION</p><h1 class="editorial-title mt-2 text-5xl sm:text-6xl">لجين</h1><p class="mt-3 max-w-md text-sm leading-7 text-muted-600">مجوهرات فضية نسائية بتفاصيل أنيقة.</p></div>
            </div>
            <a href="#collection-products" class="store-btn-secondary w-fit">تسوّق المجموعة</a>
        </header>

        <div class="lujain-brand-gallery mt-8 grid grid-cols-2 gap-3 lg:gap-4">
            <figure class="lujain-brand-main collection-media collection-media-hero relative col-span-2 min-h-[360px] bg-[#e7e5e2] sm:min-h-[430px] lg:col-span-1 lg:min-h-[560px]">
                <img src="{{ asset('images/editorial/category-lujain-sets.jpg') }}" alt="طقم مجوهرات فضية من لجين بحجر أحمر" fetchpriority="high" class="h-full w-full object-cover object-center">
                <figcaption class="image-caption image-caption-light">لجين · طقم فضة</figcaption>
            </figure>
            <figure class="lujain-brand-chain relative aspect-square overflow-hidden bg-[#e7e5e2] sm:aspect-[4/5] lg:mt-16 lg:aspect-auto lg:min-h-[440px]"><img src="{{ asset('images/editorial/category-lujain-chains.jpg') }}" alt="سلسلة فضية من لجين" loading="lazy" class="h-full w-full object-cover object-center"><figcaption class="image-caption image-caption-light">السلاسل</figcaption></figure>
            <figure class="lujain-brand-bracelet relative aspect-square overflow-hidden bg-[#e7e5e2] sm:aspect-[4/5] lg:mb-16 lg:aspect-auto lg:min-h-[440px]"><img src="{{ asset('images/editorial/category-lujain-bracelets.jpg') }}" alt="سوار فضي من لجين" loading="lazy" class="h-full w-full object-cover object-center"><figcaption class="image-caption image-caption-light">الأساور</figcaption></figure>
        </div>
    </div>
</section>
@endif

@if($section->categories->isNotEmpty())
<nav class="border-b border-line-200 bg-white" aria-label="فئات {{ $section->name_ar }}">
    <div class="container-shell flex gap-3 overflow-x-auto py-4 [scrollbar-width:none]">
        @foreach($section->categories as $category)
            <a href="{{ route('store.category', [$section->slug, $category->slug]) }}" class="shrink-0 border-b border-transparent px-3 py-2 text-xs font-medium transition hover:border-lujain-700 hover:text-lujain-700">{{ $category->name_ar }}</a>
        @endforeach
    </div>
</nav>
@endif

<section id="collection-products" class="container-shell scroll-mt-24 py-14 sm:py-20">
    <div class="store-section-heading"><div><p class="eyebrow">تسوّق المجموعة</p><h2 class="section-title mt-2">منتجات {{ $section->name_ar }}</h2></div><p class="text-xs text-muted-600">{{ $products->total() }} قطعة</p></div>
    <div class="mt-7">@include('store.partials.catalog-filters')</div>
    @if($products->isNotEmpty())
        <div class="grid grid-cols-2 gap-x-3 gap-y-10 sm:gap-x-5 md:grid-cols-3 lg:grid-cols-4">@foreach($products as $product) @include('store.partials.product-card', ['product' => $product]) @endforeach</div>
        <div class="mt-10">{{ $products->links() }}</div>
    @else
        <div class="border border-line-200 bg-white py-14 text-center"><h3 class="text-xl font-semibold">لا توجد قطع مطابقة</h3><p class="mt-2 text-sm text-muted-600">جرّب تعديل البحث أو إعادة ضبط التصفية.</p></div>
    @endif
</section>
@endsection
