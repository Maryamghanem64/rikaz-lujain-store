<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة الإدارة')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-shell min-h-screen bg-ivory-100 antialiased">
<div x-data="{ navOpen: false }" class="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
    <aside class="hidden border-l border-line-200 bg-[#fffdf9] text-ink-900 lg:flex lg:min-h-screen lg:flex-col lg:p-5">
        <a href="{{ route('admin.dashboard') }}" class="border-b border-line-200 px-3 pb-6">
            <span class="block text-lg font-semibold">ركاز × لجين</span><span class="mt-1 block text-xs text-stone-400">لوحة إدارة المتجر</span>
        </a>
        @include('layouts.partials.admin-nav')
    </aside>

    <div class="min-w-0">
        <header class="sticky top-0 z-40 flex min-h-17 items-center justify-between border-b border-line-200 bg-white/95 px-4 py-3 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <button @click="navOpen = true" type="button" class="grid size-11 place-items-center rounded-xl border bg-white lg:hidden" aria-label="فتح قائمة الإدارة"><svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16"/></svg></button>
                <div><p class="text-sm font-semibold">{{ auth()->user()->name }}</p><p class="text-xs text-muted-600">مدير {{ auth()->user()->section?->name_ar }}</p></div>
            </div>
            <a href="{{ route('store.home') }}" target="_blank" class="btn-secondary hidden sm:inline-flex">عرض المتجر</a>
        </header>

        <div x-cloak x-show="navOpen" class="fixed inset-0 z-50 lg:hidden">
            <button @click="navOpen = false" class="absolute inset-0 bg-[#3a1018]/25" aria-label="إغلاق القائمة"></button>
            <aside x-transition class="relative ml-auto flex h-full w-[min(86vw,320px)] flex-col border-l border-line-200 bg-[#fffdf9] p-5 text-ink-900 shadow-xl">
                <div class="flex items-center justify-between border-b border-line-200 pb-5"><strong class="text-rikaz-800">إدارة ركاز × لجين</strong><button @click="navOpen = false" class="grid size-10 place-items-center rounded-lg bg-ivory-200" aria-label="إغلاق">×</button></div>
                @include('layouts.partials.admin-nav')
            </aside>
        </div>

        <main class="admin-content mx-auto w-full max-w-[1500px] p-4 sm:p-6 lg:p-8">@yield('content')</main>
    </div>
</div>
</body>
</html>
