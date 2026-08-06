<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'ركاز × لجين')
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="bg-stone-50 text-stone-900">
@php
    $cartCount = app(\App\Services\CartService::class)->count();
@endphp
<header class="border-b bg-white">

    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4">

        <a
            href="{{ route('store.home') }}"
            class="text-2xl font-bold"
        >
            ركاز × لجين
        </a>

        <nav class="flex items-center gap-6">

            <a
                href="{{ route('store.home') }}"
                class="hover:opacity-70"
            >
                الرئيسية
            </a>

            <a
                href="{{ route('store.section', 'rikaz') }}"
                class="hover:opacity-70"
            >
                ركاز
            </a>

            <a
                href="{{ route('store.section', 'lujain') }}"
                class="hover:opacity-70"
            >
                لجين
            </a>
<a
    href="{{ route('cart.index') }}"
    class="hover:opacity-70"
>
    السلة

    @if ($cartCount > 0)

        <span
            class="mr-1 rounded-full bg-stone-900 px-2 py-1 text-xs text-white"
        >
            {{ $cartCount }}
        </span>

    @endif
</a>
        </nav>

    </div>

</header>


<main>
    @yield('content')
</main>


<footer class="mt-16 border-t bg-white">

    <div class="mx-auto max-w-7xl px-4 py-8 text-center">

        <p class="font-semibold">
            ركاز × لجين
        </p>

        <p class="mt-2 text-sm text-stone-500">
            مجوهرات فضة مختارة بعناية
        </p>

        <p class="mt-4 text-xs text-stone-400">
            © {{ date('Y') }} جميع الحقوق محفوظة
        </p>

    </div>

</footer>

</body>
</html>