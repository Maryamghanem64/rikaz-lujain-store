<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><meta name="theme-color" content="#3A1018">
    <link rel="icon" href="data:,">
    <title>@yield('title','ركاز × لجين')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600&family=Noto+Naskh+Arabic:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="store-body min-h-screen antialiased">
@php $cartCount=app(\App\Services\CartService::class)->count(); @endphp
<div class="bg-[#3A1018] text-white"><div class="container-shell flex min-h-9 items-center justify-center py-2 text-center text-[11px]">توصيل إلى جميع المناطق اللبنانية</div></div>
<header x-data="{open:false}" class="sticky top-0 z-50 border-b border-line-200 bg-[#faf8f4]/95 backdrop-blur-md">
    <div class="container-shell flex h-20 items-center justify-between gap-5">
        <a href="{{ route('store.home') }}" class="min-w-0" aria-label="العودة إلى الرئيسية"><strong class="editorial-title block text-2xl leading-none text-[#3A1018]">ركاز × لجين</strong><small class="mt-1 block text-[8px] tracking-[.22em] text-muted-600">SILVER JEWELRY STORE</small></a>
        <nav class="hidden items-center gap-12 text-sm font-medium md:flex" aria-label="التنقل الرئيسي"><a href="{{ route('store.home') }}" class="store-nav-link">الرئيسية</a><a href="{{ route('store.section','rikaz') }}" class="store-nav-link">ركاز</a><a href="{{ route('store.section','lujain') }}" class="store-nav-link">لجين</a></nav>
        <div class="flex items-center gap-1"><a href="{{ route('cart.index') }}" class="relative inline-flex min-h-11 items-center gap-2 px-2 text-sm" aria-label="سلة التسوق، {{ $cartCount }} منتجات"><svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M3 3h2l2.4 10.2a2 2 0 0 0 2 1.55h7.7a2 2 0 0 0 1.95-1.55L21 6H6M10 20a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm9 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z"/></svg><span class="hidden sm:inline">السلة</span><span class="grid min-w-5 place-items-center bg-[#641D2A] px-1.5 py-0.5 text-[10px] text-white">{{ $cartCount }}</span></a><button @click="open=!open" type="button" class="grid size-11 place-items-center md:hidden" aria-label="فتح القائمة"><svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="1.7" d="M4 7h16M4 12h16M4 17h16"/></svg></button></div>
    </div>
    <nav x-cloak x-show="open" @click.outside="open=false" class="border-t border-line-200 bg-[#FAF8F4] md:hidden"><div class="container-shell grid py-3 text-sm"><a href="{{ route('store.home') }}" class="border-b border-line-200 py-3">الرئيسية</a><a href="{{ route('store.section','rikaz') }}" class="border-b border-line-200 py-3">مجموعة ركاز</a><a href="{{ route('store.section','lujain') }}" class="py-3">مجموعة لجين</a></div></nav>
</header>
<main>@yield('content')</main>
<footer class="border-t border-[#641D2A]/20 bg-[#f4efe8]">
    <div class="container-shell grid gap-10 py-14 md:grid-cols-[1.3fr_.8fr_1fr_1fr]">
        <div><p class="editorial-title text-3xl text-[#3A1018]">ركاز × لجين</p><p class="mt-4 max-w-sm text-sm leading-7 text-muted-600">هويتان للمجوهرات الفضية، في متجر لبناني واحد.</p></div>
        <div><p class="text-sm font-semibold">تسوّق</p><div class="mt-4 grid gap-3 text-sm text-muted-600"><a href="{{ route('store.section','rikaz') }}">ركاز</a><a href="{{ route('store.section','lujain') }}">لجين</a><a href="{{ route('cart.index') }}">السلة</a></div></div>
        <div><p class="text-sm font-semibold">ركاز</p><div class="mt-4 grid gap-3 text-sm text-muted-600"><a dir="ltr" class="w-fit" href="tel:76842148">76 842 148</a><a dir="ltr" class="w-fit" href="https://www.instagram.com/rekaz1448?igsh=b2I3bDV5eXM1czgz" target="_blank" rel="noopener noreferrer">@rekaz1448</a></div></div>
        <div><p class="text-sm font-semibold">لجين</p><div class="mt-4 grid gap-3 text-sm text-muted-600"><a dir="ltr" class="w-fit" href="tel:+96181467027">+961 81 467 027</a><a dir="ltr" class="w-fit" href="https://www.instagram.com/lujain_jewelry_lb1?igsh=MXMxcTUzc2VzeTdybA==" target="_blank" rel="noopener noreferrer">@lujain_jewelry_lb1</a></div></div>
    </div><div class="border-t border-[#641D2A]/10 py-4 text-center text-[11px] text-muted-600">© {{ date('Y') }} ركاز × لجين. جميع الحقوق محفوظة.</div>
</footer>
</body></html>
