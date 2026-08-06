@extends('layouts.store')

@section(
    'title',
    $product->name_ar . ' | ' . $product->category->section->name_ar
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
                href="{{ route(
                    'store.section',
                    $product->category->section->slug
                ) }}"
                class="hover:text-stone-900"
            >
                {{ $product->category->section->name_ar }}
            </a>

            <span class="mx-2">/</span>

            <a
                href="{{ route(
                    'store.category',
                    [
                        $product->category->section->slug,
                        $product->category->slug
                    ]
                ) }}"
                class="hover:text-stone-900"
            >
                {{ $product->category->name_ar }}
            </a>

            <span class="mx-2">/</span>

            <span class="text-stone-900">
                {{ $product->name_ar }}
            </span>

        </div>

    </section>


    <section class="mx-auto max-w-7xl px-4 py-12">

        <div class="grid gap-12 lg:grid-cols-2">

            {{-- IMAGES --}}
            <div>

                @php
                    $primaryImage =
                        $product->images
                            ->firstWhere('is_primary', true)
                        ?? $product->images->first();
                @endphp


                @if ($primaryImage)

                    <div
                        class="overflow-hidden rounded-3xl bg-stone-100"
                    >
                        <img
                            src="{{ $primaryImage->url }}"
                            alt="{{ $product->name_ar }}"
                            class="aspect-square w-full object-cover"
                        >
                    </div>

                @else

                    <div
                        class="flex aspect-square items-center justify-center rounded-3xl bg-stone-100 text-stone-400"
                    >
                        لا توجد صورة
                    </div>

                @endif


                {{-- OTHER IMAGES --}}
                @if ($product->images->count() > 1)

                    <div class="mt-4 grid grid-cols-4 gap-3">

                        @foreach ($product->images as $image)

                            <a
                                href="{{ $image->url }}"
                                target="_blank"
                                class="overflow-hidden rounded-xl border border-stone-200"
                            >

                                <img
                                    src="{{ $image->url }}"
                                    alt="{{ $product->name_ar }}"
                                    class="aspect-square w-full object-cover"
                                >

                            </a>

                        @endforeach

                    </div>

                @endif

            </div>


            {{-- PRODUCT INFORMATION --}}
            <div>

                <p class="text-sm text-stone-500">
                    {{ $product->category->section->name_ar }}
                    /
                    {{ $product->category->name_ar }}
                </p>


                <h1 class="mt-3 text-4xl font-bold leading-tight">
                    {{ $product->name_ar }}
                </h1>


                @if ($product->stone_name)

                    <p class="mt-3 text-lg text-stone-600">
                        {{ $product->stone_name }}
                    </p>

                @endif


                <div class="mt-6">

                    <span class="text-3xl font-bold">
                        ${{ number_format((float) $product->price, 2) }}
                    </span>

                </div>


                {{-- STOCK --}}
                <div class="mt-6">

                    @if ($product->available_quantity > 0)

                        <span
                            class="inline-flex rounded-full bg-green-50 px-4 py-2 text-sm font-semibold text-green-700"
                        >
                            متوفر
                        </span>

                    @else

                        <span
                            class="inline-flex rounded-full bg-red-50 px-4 py-2 text-sm font-semibold text-red-700"
                        >
                            غير متوفر حاليًا
                        </span>

                    @endif

                </div>


                {{-- DETAILS --}}
                <div
                    class="mt-8 overflow-hidden rounded-2xl border border-stone-200 bg-white"
                >

                    <div
                        class="flex justify-between border-b border-stone-100 p-4"
                    >
                        <span class="text-stone-500">
                            عيار الفضة
                        </span>

                        <strong>
                            {{ $product->silver_purity }}
                        </strong>
                    </div>


                    @if ($product->size)

                        <div
                            class="flex justify-between border-b border-stone-100 p-4"
                        >
                            <span class="text-stone-500">
                                المقاس
                            </span>

                            <strong>
                                {{ $product->size }}
                            </strong>
                        </div>

                    @endif


                    @if ($product->stone_name)

                        <div
                            class="flex justify-between border-b border-stone-100 p-4"
                        >
                            <span class="text-stone-500">
                                الحجر
                            </span>

                            <strong>
                                {{ $product->stone_name }}
                            </strong>
                        </div>

                    @endif


                    @if ($product->stone_weight)

                        <div
                            class="flex justify-between p-4"
                        >
                            <span class="text-stone-500">
                                وزن الحجر
                            </span>

                            <strong>
                                {{ $product->stone_weight }}
                            </strong>
                        </div>

                    @endif

                </div>


                {{-- DESCRIPTION --}}
                @if ($product->description_ar)

                    <div class="mt-8">

                        <h2 class="text-xl font-bold">
                            وصف المنتج
                        </h2>

                        <p class="mt-3 whitespace-pre-line leading-8 text-stone-600">
                            {{ $product->description_ar }}
                        </p>

                    </div>

                @endif


                {{-- ORDER --}}
                <div class="mt-10">

                    @if ($product->available_quantity > 0)

                        <button
                            type="button"
                            class="w-full rounded-xl bg-stone-900 px-6 py-4 text-lg font-semibold text-white"
                        >
                            اطلب الآن
                        </button>

                        <p class="mt-3 text-center text-sm text-stone-500">
                            لا تحتاج إلى إنشاء حساب لإتمام الطلب.
                        </p>

                    @else

                        <button
                            type="button"
                            disabled
                            class="w-full cursor-not-allowed rounded-xl bg-stone-300 px-6 py-4 text-lg font-semibold text-stone-500"
                        >
                            المنتج غير متوفر
                        </button>

                    @endif

                </div>

            </div>

        </div>

    </section>

@endsection