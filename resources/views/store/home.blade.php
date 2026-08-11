@extends('layouts.store')
@section('title', 'ركاز × لجين | مجوهرات فضة')
@section('content')

<section class="editorial-hero">
    <div class="container-shell grid md:min-h-[650px] md:grid-cols-[.9fr_1.1fr]">
        <div class="flex flex-col justify-center py-10 md:py-12 lg:py-16 lg:pl-14">
            <p class="eyebrow text-lujain-700">ركاز × لجين · متجر مجوهرات فضة</p>
            <h1 class="editorial-title mt-5 max-w-[10ch] text-[3.25rem] leading-[1.08] sm:text-7xl lg:text-[5.25rem]">قطعةٌ تختارها<br><em class="font-normal text-lujain-800">لتبقى معك.</em></h1>
            <p class="mt-6 max-w-[34rem] text-sm leading-8 text-muted-600 sm:text-base">هويتان لبنانيتان في مساحة واحدة: مجوهرات فضية وأحجار مختارة، مقدّمة بصور حقيقية وتفاصيل واضحة لكل قطعة.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('store.section', 'rikaz') }}" class="store-btn-primary">اكتشف ركاز</a>
                <a href="{{ route('store.section', 'lujain') }}" class="store-btn-secondary">اكتشف لجين</a>
            </div>
        </div>
        <div class="hero-media -mx-4 pb-10 sm:-mx-6 md:mx-0 md:flex md:items-center md:justify-start md:py-10 lg:pr-8">
            <div class="hero-video-shell media-frame media-frame-hero relative aspect-[3/4] w-full bg-[#efe7dc] md:max-h-[570px] md:max-w-[428px] lg:max-h-[610px] lg:max-w-[458px]">
                <video class="hero-video h-full w-full object-cover" autoplay muted loop playsinline preload="metadata" poster="{{ asset('images/editorial/home-hero-video-poster.jpg') }}" aria-hidden="true" tabindex="-1">
                    <source src="{{ asset('media/home-jewelry-hero.mp4') }}" type="video/mp4">
                </video>
                <img src="{{ asset('images/editorial/home-hero-video-poster.jpg') }}" alt="" class="hero-video-poster h-full w-full object-cover" aria-hidden="true">
            </div>
        </div>
    </div>
</section>

<section class="border-y border-line-200 bg-white">
    <div class="container-shell grid grid-cols-2 divide-x divide-x-reverse divide-line-200 md:grid-cols-4">
        @foreach ([['فضة مختارة','تفاصيل واضحة للقطعة'],['قطع محدودة','بحسب المخزون الحقيقي'],['توصيل داخل لبنان','بحسب المنطقة'],['Cash أو Whish Money','وفق النظام الحالي']] as [$title,$copy])
            <div class="px-4 py-6 text-center"><p class="text-sm font-semibold">{{ $title }}</p><p class="mt-1 text-[11px] leading-5 text-muted-600">{{ $copy }}</p></div>
        @endforeach
    </div>
</section>

<section class="container-shell py-16 sm:py-20">
    <div class="mx-auto max-w-2xl text-center"><p class="eyebrow text-lujain-700">هويتان، متجر واحد</p><h2 class="editorial-title mt-4 text-4xl sm:text-5xl">اختر العلامة</h2></div>
    <div class="mt-10 grid gap-11 md:grid-cols-2 md:gap-0">
        @foreach ([
            ['rikaz','ركاز','كنزٌ يختبئ في جوف الأرض، لا يظهر إلا لمن يستحقه.','rikaz-logo.jpg'],
            ['lujain','لجين','مجوهرات فضية نسائية بتفاصيل أنيقة.','lujain-logo.jpg']
        ] as [$slug,$name,$copy,$logo])
            <article class="brand-signature flex flex-col items-center px-6 text-center md:px-12">
                <div class="brand-logo-circle"><img src="{{ asset('images/branding/'.$logo) }}" alt="شعار {{ $name }} الرسمي" class="h-full w-full rounded-full object-contain"></div>
                <h3 class="editorial-title mt-6 text-4xl">{{ $name }}</h3>
                <p class="mt-3 max-w-md text-sm leading-7 text-muted-600">{{ $copy }}</p>
                <a href="{{ route('store.section', $slug) }}" class="store-text-link mt-5">تسوّق المجموعة</a>
            </article>
        @endforeach
    </div>
</section>

@if ($sections->isNotEmpty())
<section class="border-y border-line-200 bg-[#f4efe8] py-16 sm:py-20">
    <div class="container-shell">
        <div class="store-section-heading"><div><p class="eyebrow">دليل المجموعات</p><h2 class="section-title mt-2">تسوّق حسب الفئة</h2></div></div>
        <div class="category-grid mt-8">
            @foreach ($sections as $section)
                @foreach ($section->categories as $category)
                    @php
                        $categoryImage = $category->products()->with('images')->storefrontAvailable()->latest()->take(12)->get()->flatMap->images->first(fn ($image) => $image->isDisplayableOnStorefront());
                        $categoryEditorialImages = [
                            'rikaz.rings' => 'images/editorial/rikaz-heritage-ring.jpg',
                            'lujain.rings' => 'images/editorial/category-lujain-rings-natural.jpg',
                            'lujain.chains' => 'images/editorial/category-lujain-chains.jpg',
                            'lujain.bracelets' => 'images/editorial/category-lujain-bracelets.jpg',
                            'lujain.sets' => 'images/editorial/category-lujain-sets.jpg',
                        ];
                        $categoryKey = $section->slug.'.'.$category->slug;
                        $categoryEditorialImage = $categoryEditorialImages[$categoryKey] ?? null;
                        $useEditorialImage = $categoryKey === 'lujain.rings' || ! $categoryImage;
                    @endphp
                    <a href="{{ route('store.category', [$section->slug,$category->slug]) }}" class="category-tile group">
                        <div class="category-media media-frame aspect-square bg-[#e5e1dc]">
                            @if($useEditorialImage && $categoryEditorialImage)
                                <img src="{{ asset($categoryEditorialImage) }}" alt="{{ $category->name_ar }}" @class(['h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]', 'lujain-ring-category-image' => $categoryKey === 'lujain.rings'])>
                            @elseif($categoryImage)
                                <img src="{{ $categoryImage->displayUrl() }}" alt="{{ $category->name_ar }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
                            @elseif($categoryEditorialImage)
                                <img src="{{ asset($categoryEditorialImage) }}" alt="صورة تحريرية لفئة {{ $category->name_ar }} من {{ $section->name_ar }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
                            @else
                                <div class="grid h-full place-items-center text-xs tracking-[.18em] text-muted-600">SILVER</div>
                            @endif
                        </div>
                        <div class="category-label"><p class="text-[10px] text-muted-600">{{ $section->name_ar }}</p><h3 class="mt-1 text-lg font-semibold">{{ $category->name_ar }}</h3></div>
                    </a>
                @endforeach
            @endforeach
        </div>
    </div>
</section>
@endif

@if ($featuredProducts->isNotEmpty() || $newProducts->isNotEmpty())
@php
    $curatedProducts = ($featuredProducts->isNotEmpty() ? $featuredProducts : $newProducts)->take(8);
    $curatedCount = $curatedProducts->count();
@endphp
<section class="container-shell py-16 sm:py-24">
    @if($curatedCount === 1)
        <div class="grid gap-9 border-y border-line-200 py-10 lg:grid-cols-[.72fr_1.28fr] lg:items-center lg:py-14">
            <div><p class="eyebrow text-lujain-700">من القطع المتوفرة</p><h2 class="editorial-title mt-3 text-4xl sm:text-5xl">مختارات من المتجر</h2><p class="mt-4 max-w-sm text-sm leading-7 text-muted-600">قطعة متوفرة حاليًا ضمن المخزون.</p></div>
            <div class="w-full max-w-sm lg:justify-self-center">@include('store.partials.product-card',['product'=>$curatedProducts->first()])</div>
        </div>
    @else
        <div class="store-section-heading"><div><p class="eyebrow text-lujain-700">من القطع المتوفرة</p><h2 class="section-title mt-2">مختارات من المتجر</h2></div></div>
        <div @class(['mt-9 grid gap-x-3 gap-y-10 sm:gap-x-5','grid-cols-2 md:grid-cols-2 max-w-4xl mx-auto'=>$curatedCount===2,'grid-cols-2 md:grid-cols-3 max-w-6xl mx-auto'=>$curatedCount===3,'grid-cols-2 md:grid-cols-3 lg:grid-cols-4'=>$curatedCount>=4])>@foreach($curatedProducts as $product) @include('store.partials.product-card',['product'=>$product]) @endforeach</div>
    @endif
</section>
@endif

<section class="editorial-break border-y border-line-200 bg-white">
    <div class="container-shell grid items-center gap-10 py-12 sm:py-16 md:grid-cols-[.68fr_1.32fr] md:gap-12 lg:gap-16 lg:py-20">
        <div class="md:self-end md:pb-10 lg:pr-6"><p class="eyebrow text-lujain-700">صور من المجموعتين</p><h2 class="editorial-title mt-4 text-4xl sm:text-5xl">تفاصيل القطع<br>كما هي.</h2><p class="mt-5 max-w-xs text-sm leading-7 text-muted-600">لقطات حقيقية للمجوهرات في الضوء الطبيعي.</p></div>
        <div class="grid grid-cols-1 items-start gap-3 sm:grid-cols-2 sm:gap-5">
            <figure class="aspect-[4/5] overflow-hidden bg-[#b6a9a0]"><img src="{{ asset('images/editorial/home-rikaz-green-rings.png') }}" alt="مجموعة خواتم ركاز الفضية المنقوشة بأحجار خضراء" loading="lazy" class="h-full w-full object-cover object-[50%_48%]"></figure>
            <figure class="aspect-[4/5] overflow-hidden bg-[#d5d3d1]"><img src="{{ asset('images/editorial/home-lujain-silver-rings.jpg') }}" alt="مجموعة خواتم لجين الفضية المرصعة على صينية رمادية" loading="lazy" class="h-full w-full object-cover object-center"></figure>
        </div>
    </div>
</section>

<section class="container-shell py-16 text-center sm:py-20"><p class="eyebrow text-lujain-700">طلب مباشر وواضح</p><h2 class="editorial-title mt-3 text-4xl">كيف يتم الطلب؟</h2><div class="mx-auto mt-9 grid max-w-4xl gap-8 sm:grid-cols-3">@foreach([['01','اختيار القطعة'],['02','تأكيد الطلب والدفع'],['03','التوصيل']] as [$n,$label])<div><span class="editorial-title text-4xl text-lujain-800">{{ $n }}</span><p class="mt-2 text-sm text-muted-600">{{ $label }}</p></div>@endforeach</div></section>
@endsection
