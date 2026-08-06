@extends('layouts.store')

@section(
    'title',
    $category->name_ar . ' | ' . $section->name_ar
)

@section('content')

    {{-- BREADCRUMB --}}
    <section class="border-b bg-white">

        <div class="mx-auto max-w-7xl px-4 py-4 text-sm text-stone-500">

            <a
                href="{{ route('store.home') }}"
                class="hover:text-stone-900"
            >
                الرئيسية
            </a>

            <span class="mx-2">/</span>

            <a
                href="{{ route('store.section', $section->slug) }}"
                class="hover:text-stone-900"
            >
                {{ $section->name_ar }}
            </a>

            <span class="mx-2">/</span>

            <span class="text-stone-900">
                {{ $category->name_ar }}
            </span>

        </div>

    </section>


    {{-- HEADER --}}
    <section class="bg-stone-100">

        <div class="mx-auto max-w-7xl px-4 py-12">

            <p class="mb-2 text-sm text-stone-500">
                {{ $section->name_ar }}
            </p>

            <h1 class="text-4xl font-bold">
                {{ $category->name_ar }}
            </h1>

            <p class="mt-3 text-stone-500">
                {{ $products->total() }} منتج
            </p>

        </div>

    </section>


    {{-- PRODUCTS --}}
    <section class="mx-auto max-w-7xl px-4 py-14">
@include('store.partials.catalog-filters')
        @if ($products->isNotEmpty())

            <div
                class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >

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

            <div
                class="rounded-2xl border border-stone-200 bg-white p-12 text-center"
            >

                <h2 class="text-xl font-bold">
                    لا توجد منتجات حاليًا
                </h2>

                <p class="mt-2 text-stone-500">
                    سيتم إضافة منتجات جديدة قريبًا.
                </p>

                <a
                    href="{{ route('store.section', $section->slug) }}"
                    class="mt-6 inline-block rounded-xl bg-stone-900 px-6 py-3 text-white"
                >
                    العودة إلى {{ $section->name_ar }}
                </a>

            </div>

        @endif

    </section>

@endsection