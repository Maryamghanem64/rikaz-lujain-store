@php
    $sectionSlug = $product->category->section->slug;
    $storefrontImage = $product->images->first(fn ($image) => $image->isDisplayableOnStorefront());
@endphp
<article class="product-card group min-w-0 overflow-hidden border border-line-200 bg-white">
    <a href="{{ route('store.product', [$sectionSlug, $product->slug]) }}" class="product-media relative block aspect-[4/5] overflow-hidden bg-[#efebe5]">
        @if ($storefrontImage)
            <img src="{{ $storefrontImage->displayUrl() }}" alt="{{ $product->name_ar }}" loading="lazy" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.025]">
        @else
            <div class="flex h-full flex-col items-center justify-center px-5 text-center text-stone-400">
                <span class="block h-px w-10 bg-stone-300"></span>
                <span class="mt-4 text-[11px]">الصورة قيد الإضافة</span>
            </div>
        @endif
        @if ($product->available_quantity <= 0)<span class="absolute left-2 top-2 rounded-sm bg-ink-900 px-2 py-1 text-[9px] text-white">نفدت الكمية</span>@endif
    </a>
    <div class="p-3 sm:p-4">
        <p class="truncate text-[9px] text-muted-600 sm:text-[10px]">{{ $product->category->section->name_ar }} / {{ $product->category->name_ar }}</p>
        <a href="{{ route('store.product', [$sectionSlug, $product->slug]) }}"><h3 class="mt-1.5 min-h-12 break-words line-clamp-2 text-sm font-semibold leading-6 transition group-hover:text-lujain-700 sm:text-base">{{ $product->name_ar }}</h3></a>
        <p class="mt-1 truncate text-[11px] text-muted-600">{{ $product->stone_name ? $product->stone_name . ' · ' : '' }}فضة {{ $product->silver_purity }}</p>
        <div class="mt-3 flex items-end justify-between gap-2 border-t border-line-200 pt-3">
            <strong dir="ltr" class="text-sm font-semibold sm:text-base">&#36;{{ number_format((float) $product->price, 2) }}</strong>
            <span class="text-[10px] {{ $product->available_quantity > 0 ? 'text-lujain-700' : 'text-muted-600' }}">{{ $product->available_quantity > 0 ? 'متوفر' : 'غير متوفر' }}</span>
        </div>
    </div>
</article>
