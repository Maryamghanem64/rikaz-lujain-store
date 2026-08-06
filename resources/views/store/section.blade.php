@extends('layouts.store')

@section('title', $section->name_ar . ' | ركاز × لجين')

@section('content')

    {{-- HERO --}}
    <section class="bg-stone-900 text-white">

        <div class="mx-auto max-w-7xl px-4 py-16">

            <p class="mb-3 text-sm tracking-widest text-stone-400">
                {{ strtoupper($section->slug) }}
            </p>

            <h1 class="text-4xl font-bold md:text-6xl">
                {{ $section->name_ar }}
            </h1>

            @if ($section->slug === 'rikaz')

                <p class="mt-5 max-w-2xl leading-8 text-stone-300">
                    مجموعة ركاز للخواتم الرجالية المصنوعة من الفضة
                    مع أحجار مختارة وتفاصيل مميزة.
                </p>

            @else

                <p class="mt-5 max-w-2xl leading-8 text-stone-300">
                    مجموعة لجين النسائية من الخواتم والسلاسل
                    والأساور والأطقم الفضية.
                </p>

            @endif

        </div>

    </section>


    {{-- CATEGORIES --}}
    @if ($section->categories->isNotEmpty())

        <section class="mx-auto max-w-7xl px-4 py-12">

            <div class="mb-6">

                <h2 class="text-2xl font-bold">
                    الفئات
                </h2>

            </div>

            <div class="flex flex-wrap gap-3">

                @foreach ($section->categories as $category)

                    <a
                        href="{{ route(
                            'store.category',
                            [
                                $section->slug,
                                $category->slug
                            ]
                        ) }}"
                        class="rounded-full border border-stone-300 bg-white px-5 py-2 transition hover:bg-stone-900 hover:text-white"
                    >
                        {{ $category->name_ar }}
                    </a>

                @endforeach

            </div>

        </section>

    @endif


    {{-- PRODUCTS --}}
    <section class="mx-auto max-w-7xl px-4 pb-16">

        <div class="mb-8 flex items-center justify-between">

            <div>

                <h2 class="text-3xl font-bold">
                    منتجات {{ $section->name_ar }}
                </h2>

                <p class="mt-2 text-stone-500">
                    {{ $products->total() }} منتج
                </p>

            </div>

        </div>

@include('store.partials.catalog-filters')
        @if ($products->isNotEmpty())

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

                @foreach ($products as $product)

                    @include(
                        'store.partials.product-card',
                        ['product' => $product]
                    )

                @endforeach

            </div>


            <div class="mt-10">

                {{ $products->links() }}

            </div>

        @else

            <div class="rounded-2xl border border-stone-200 bg-white p-10 text-center">

                <h3 class="font-bold">
                    لا توجد منتجات حاليًا
                </h3>

                <p class="mt-2 text-sm text-stone-500">
                    سيتم إضافة منتجات جديدة قريبًا.
                </p>

            </div>

        @endif

    </section>

@endsection