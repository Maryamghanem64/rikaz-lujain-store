@extends('layouts.store')
@section('title', $product->name_ar . ' | ' . $product->category->section->name_ar)
@section('content')
@php
    $section = $product->category->section;
    $storefrontImages = $product->images->filter(fn ($image) => $image->isDisplayableOnStorefront())->values();
    $primaryImage = $storefrontImages->firstWhere('is_primary', true) ?? $storefrontImages->first();
@endphp
<nav class="border-b border-line-200 bg-white" aria-label="مسار الصفحة"><div class="container-shell flex items-center gap-2 overflow-hidden py-3 text-[11px] text-muted-600"><a href="{{ route('store.home') }}">الرئيسية</a><span>/</span><a href="{{ route('store.section', $section->slug) }}">{{ $section->name_ar }}</a><span>/</span><span class="truncate text-ink-900">{{ $product->name_ar }}</span></div></nav>
<section class="container-shell py-7 sm:py-10">
    @if ($errors->any())<div class="alert-error mb-6">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
    <div class="grid gap-7 lg:grid-cols-[1.12fr_.88fr] lg:gap-12">
        <div x-data="{ image: @js($primaryImage?->displayUrl()) }">
            <div class="flex aspect-[4/5] items-center justify-center rounded-sm border border-line-200 bg-[#efebe5] p-5 sm:p-9">
                @if ($primaryImage)
                    <img :src="image" data-gallery-main alt="{{ $product->name_ar }}" class="h-full w-full object-contain">
                @else
                    <div class="text-center text-stone-400"><span class="mx-auto block h-px w-12 bg-stone-300"></span><p class="mt-4 text-xs">الصورة قيد الإضافة</p></div>
                @endif
            </div>
            @if ($storefrontImages->count() > 1)<div class="mt-3 flex gap-3 overflow-x-auto pb-2">@foreach ($storefrontImages as $image)<button type="button" @click="image = @js($image->displayUrl())" class="size-20 shrink-0 rounded-sm border border-line-200 bg-white p-1 transition hover:border-lujain-700 sm:size-24"><img src="{{ $image->displayUrl() }}" alt="صورة إضافية لـ {{ $product->name_ar }}" class="h-full w-full object-cover"></button>@endforeach</div>@endif
        </div>

        <div class="rounded-sm border border-line-200 bg-white p-5 sm:p-7 lg:sticky lg:top-28 lg:self-start">
            <p class="text-[11px] tracking-[.16em] text-lujain-700">{{ strtoupper($section->slug) }} / {{ $product->category->name_ar }}</p>
            <h1 class="mt-3 break-words text-3xl font-semibold leading-tight sm:text-4xl">{{ $product->name_ar }}</h1>
            @if ($product->stone_name)<p class="mt-2 text-sm text-muted-600">{{ $product->stone_name }} · فضة {{ $product->silver_purity }}</p>@endif
            <div class="mt-5 flex items-center justify-between gap-4 border-y border-line-200 py-4"><p dir="ltr" class="w-fit text-2xl font-semibold">&#36;{{ number_format((float) $product->price, 2) }}</p><p class="text-xs {{ $product->available_quantity > 0 ? 'text-lujain-700' : 'text-muted-600' }}">{{ $product->available_quantity > 0 ? '● متوفر للطلب' : '○ غير متوفر حاليًا' }}</p></div>

            <dl class="mt-5 grid grid-cols-2 gap-x-5 text-sm">
                <div class="border-b border-line-200 py-3"><dt class="text-xs text-muted-600">عيار الفضة</dt><dd class="mt-1 font-medium">{{ $product->silver_purity }}</dd></div>
                @if ($product->size)<div class="border-b border-line-200 py-3"><dt class="text-xs text-muted-600">المقاس</dt><dd class="mt-1 font-medium">{{ $product->size }}</dd></div>@endif
                @if ($product->stone_name)<div class="border-b border-line-200 py-3"><dt class="text-xs text-muted-600">الحجر</dt><dd class="mt-1 font-medium">{{ $product->stone_name }}</dd></div>@endif
                @if ($product->stone_weight)<div class="border-b border-line-200 py-3"><dt class="text-xs text-muted-600">وزن الحجر</dt><dd class="mt-1 font-medium">{{ $product->stone_weight }}</dd></div>@endif
            </dl>

            @if ($product->description_ar)<div class="mt-6"><h2 class="text-lg font-semibold">عن القطعة</h2><p class="mt-2 whitespace-pre-line text-sm leading-7 text-muted-600">{{ $product->description_ar }}</p></div>@endif

            <div class="mt-7 border-t border-line-200 pt-5">
                @if ($product->available_quantity > 0)
                <form method="POST" action="{{ route('cart.store', $product) }}">@csrf<div class="flex items-end gap-3"><div class="w-24"><label for="quantity" class="mb-1.5 block text-xs text-muted-600">الكمية</label><input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->available_quantity }}" class="min-h-12 w-full rounded-sm border border-line-200 bg-ivory-50 px-3 text-center" required></div><button type="submit" class="min-h-12 flex-1 rounded-sm bg-lujain-800 px-6 text-sm font-semibold text-white transition hover:bg-lujain-700">أضف إلى السلة</button></div></form><p class="mt-3 text-center text-[11px] text-muted-600">طلب مباشر من دون إنشاء حساب.</p>
                @else <button type="button" disabled class="min-h-12 w-full cursor-not-allowed rounded-sm bg-stone-300 px-6 text-sm text-stone-600">القطعة غير متوفرة</button> @endif
            </div>
        </div>
    </div>
</section>
@endsection
