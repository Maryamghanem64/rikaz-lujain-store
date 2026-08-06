@extends('layouts.store')

@section('title', 'ركاز × لجين | مجوهرات فضة')

@section('content')


{{-- HERO --}}
<section class="bg-stone-900 text-white">

    <div
        class="mx-auto grid max-w-7xl gap-10 px-4 py-20 lg:grid-cols-2 lg:items-center"
    >

        <div>

            <p class="mb-3 text-sm tracking-widest text-stone-300">
                RIKAZ × LUJAIN
            </p>

            <h1 class="text-4xl font-bold leading-tight md:text-6xl">
                فضة بتفاصيل
                <br>
                لها طابعها الخاص
            </h1>

            <p class="mt-6 max-w-xl leading-8 text-stone-300">
                اكتشفوا مجموعة ركاز للرجال ومجموعة لجين للنساء،
                مع قطع فضة مختارة وأحجار مميزة.
            </p>


            <div class="mt-8 flex flex-wrap gap-3">

                <a
                    href="{{ route('store.section', 'rikaz') }}"
                    class="rounded-xl bg-white px-6 py-3 font-semibold text-stone-900"
                >
                    اكتشف ركاز
                </a>

                <a
                    href="{{ route('store.section', 'lujain') }}"
                    class="rounded-xl border border-white px-6 py-3 font-semibold"
                >
                    اكتشف لجين
                </a>

            </div>

        </div>


        <div
            class="flex min-h-[350px] items-center justify-center rounded-3xl bg-stone-800"
        >

            <div class="text-center">

                <p class="text-5xl font-bold">
                    ركاز
                </p>

                <span class="my-4 block text-stone-400">
                    ×
                </span>

                <p class="text-5xl font-bold">
                    لجين
                </p>

            </div>

        </div>

    </div>

</section>



{{-- BRANDS --}}
<section class="mx-auto max-w-7xl px-4 py-16">

    <div class="mb-10 text-center">

        <h2 class="text-3xl font-bold">
            اختر مجموعتك
        </h2>

        <p class="mt-2 text-stone-500">
            مجموعتان، لكل واحدة هويتها الخاصة
        </p>

    </div>


    <div class="grid gap-6 md:grid-cols-2">


        {{-- RIKAZ --}}
        <a
            href="{{ route('store.section', 'rikaz') }}"
            class="group rounded-3xl bg-stone-900 p-10 text-white"
        >

            <p class="text-sm text-stone-400">
                MEN'S COLLECTION
            </p>

            <h3 class="mt-3 text-4xl font-bold">
                ركاز
            </h3>

            <p class="mt-4 text-stone-300">
                خواتم فضة رجالية وأحجار مختارة.
            </p>

            <p class="mt-8 font-semibold">
                استكشف المجموعة ←
            </p>

        </a>


        {{-- LUJAIN --}}
        <a
            href="{{ route('store.section', 'lujain') }}"
            class="group rounded-3xl border border-stone-300 bg-white p-10"
        >

            <p class="text-sm text-stone-500">
                WOMEN'S COLLECTION
            </p>

            <h3 class="mt-3 text-4xl font-bold">
                لجين
            </h3>

            <p class="mt-4 text-stone-500">
                خواتم، سلاسل، أساور وأطقم فضة.
            </p>

            <p class="mt-8 font-semibold">
                استكشف المجموعة ←
            </p>

        </a>

    </div>

</section>



{{-- FEATURED PRODUCTS --}}
@if ($featuredProducts->isNotEmpty())

<section class="bg-white py-16">

    <div class="mx-auto max-w-7xl px-4">

        <div class="mb-8 flex items-end justify-between">

            <div>

                <h2 class="text-3xl font-bold">
                    قطع مميزة
                </h2>

                <p class="mt-2 text-stone-500">
                    مختارات من ركاز ولجين
                </p>

            </div>

        </div>


        <div
            class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4"
        >

            @foreach ($featuredProducts as $product)

                @include(
                    'store.partials.product-card',
                    ['product' => $product]
                )

            @endforeach

        </div>

    </div>

</section>

@endif



{{-- NEW PRODUCTS --}}
@if ($newProducts->isNotEmpty())

<section class="mx-auto max-w-7xl px-4 py-16">

    <div class="mb-8">

        <h2 class="text-3xl font-bold">
            أحدث المنتجات
        </h2>

        <p class="mt-2 text-stone-500">
            آخر القطع المضافة إلى المتجر
        </p>

    </div>


    <div
        class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4"
    >

        @foreach ($newProducts as $product)

            @include(
                'store.partials.product-card',
                ['product' => $product]
            )

        @endforeach

    </div>

</section>

@endif



{{-- DELIVERY --}}
<section class="bg-stone-100">

    <div
        class="mx-auto grid max-w-7xl gap-6 px-4 py-12 text-center md:grid-cols-3"
    >

        <div>
            <h3 class="font-bold">
                توصيل لكل لبنان
            </h3>

            <p class="mt-2 text-sm text-stone-500">
                رسوم التوصيل تُحدد حسب المنطقة.
            </p>
        </div>


        <div>
            <h3 class="font-bold">
                قطع فضة مختارة
            </h3>

            <p class="mt-2 text-sm text-stone-500">
                تفاصيل واضحة لكل منتج ومقاسه.
            </p>
        </div>


        <div>
            <h3 class="font-bold">
                طلب بسيط
            </h3>

            <p class="mt-2 text-sm text-stone-500">
                بدون الحاجة لإنشاء حساب.
            </p>
        </div>

    </div>

</section>


@endsection