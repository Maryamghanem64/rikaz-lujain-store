@extends('layouts.store')
@section('title', 'سلة التسوق | ركاز × لجين')
@section('content')
<section class="container-shell py-9 sm:py-12">
    <div class="store-section-heading mb-7"><div><p class="eyebrow">طلبك</p><h1 class="section-title mt-2">سلة التسوق</h1></div>@if ($items->isNotEmpty())<p class="text-xs text-muted-600">{{ $items->sum('quantity') }} قطعة</p>@endif</div>
    @if (session('success'))<div class="alert-success mb-6">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="alert-error mb-6">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
    @if ($items->isEmpty())
        <div class="rounded-sm border border-line-200 bg-white py-16 text-center"><h2 class="text-2xl font-semibold">سلتك فارغة</h2><p class="mt-3 text-sm text-muted-600">اختر قطعة من مجموعتي ركاز ولجين لتبدأ طلبك.</p><a href="{{ route('store.home') }}" class="store-btn-primary mt-6">العودة إلى المتجر</a></div>
    @else
        <div class="grid gap-7 lg:grid-cols-[1fr_350px] lg:items-start">
            <div class="overflow-hidden rounded-sm border border-line-200 bg-white">
                <div class="divide-y divide-line-200">
                @foreach ($items as $item)
                    @php
                        $product = $item['product'];
                        $cartImage = $product->images->first(fn ($image) => $image->isDisplayableOnStorefront());
                    @endphp
                    <article class="grid grid-cols-[88px_1fr] gap-4 p-4 sm:grid-cols-[110px_1fr_auto] sm:gap-5 sm:p-5">
                        <a href="{{ route('store.product', [$product->category->section->slug, $product->slug]) }}" class="aspect-[4/5] overflow-hidden rounded-sm bg-[#efebe5]">@if ($cartImage)<img src="{{ $cartImage->url }}" alt="{{ $product->name_ar }}" class="h-full w-full object-cover">@else<div class="flex h-full items-center justify-center p-2 text-center text-[9px] text-stone-400">ركاز × لجين</div>@endif</a>
                        <div class="min-w-0"><p class="text-[10px] text-muted-600">{{ $product->category->section->name_ar }} / {{ $product->category->name_ar }}</p><a href="{{ route('store.product', [$product->category->section->slug, $product->slug]) }}"><h2 class="mt-1 text-base font-semibold sm:text-lg">{{ $product->name_ar }}</h2></a>@if ($product->size)<p class="mt-1 text-xs text-muted-600">المقاس: {{ $product->size }}</p>@endif<p dir="ltr" class="mt-2 w-fit text-sm font-medium">&#36;{{ number_format($item['unit_price'], 2) }}</p>
                            <div class="mt-4 flex flex-wrap items-end gap-3"><form method="POST" action="{{ route('cart.update', $product) }}" class="flex items-end gap-2">@csrf @method('PATCH')<div><label for="quantity-{{ $product->id }}" class="mb-1 block text-[10px] text-muted-600">الكمية</label><input type="number" id="quantity-{{ $product->id }}" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $product->available_quantity }}" class="h-9 w-16 rounded-sm border border-line-200 bg-ivory-50 px-2 text-center" required></div><button type="submit" class="h-9 rounded-sm border border-line-200 px-3 text-xs text-lujain-700">تحديث</button></form><form method="POST" action="{{ route('cart.destroy', $product) }}" onsubmit="return confirm('حذف المنتج من السلة؟');">@csrf @method('DELETE')<button type="submit" class="h-9 text-xs text-red-700">حذف</button></form></div>
                        </div>
                        <div class="col-span-2 flex justify-between border-t border-line-200 pt-3 text-sm sm:col-span-1 sm:block sm:border-0 sm:pt-0 sm:text-left"><span class="text-muted-600">المجموع</span><strong dir="ltr" class="sm:mt-1 sm:block">&#36;{{ number_format($item['subtotal'], 2) }}</strong></div>
                    </article>
                @endforeach
                </div>
                <form method="POST" action="{{ route('cart.clear') }}" onsubmit="return confirm('هل تريد إفراغ السلة بالكامل؟');" class="border-t border-line-200 px-5 py-4">@csrf @method('DELETE')<button type="submit" class="text-xs text-red-700 underline underline-offset-4">إفراغ السلة</button></form>
            </div>
            <aside class="rounded-sm border border-line-200 bg-white p-5 lg:sticky lg:top-28"><h2 class="text-xl font-semibold">ملخص الطلب</h2><div class="mt-5 flex justify-between border-b border-line-200 pb-4 text-sm"><span class="text-muted-600">عدد القطع</span><span>{{ $items->sum('quantity') }}</span></div><div class="mt-4 flex items-end justify-between"><span class="text-sm">المجموع المبدئي</span><strong dir="ltr" class="text-xl">&#36;{{ number_format($subtotal, 2) }}</strong></div><p class="mt-4 text-xs leading-6 text-muted-600">تُحتسب رسوم التوصيل في الخطوة التالية حسب المنطقة.</p><a href="{{ route('checkout.show') }}" class="store-btn-primary mt-6 flex w-full">متابعة الطلب</a><a href="{{ route('store.home') }}" class="mt-4 block text-center text-xs text-muted-600 underline underline-offset-4">متابعة التسوق</a></aside>
        </div>
    @endif
</section>
@endsection
