@extends('layouts.store')

@section('title', 'سلة التسوق | ركاز × لجين')

@section('content')

<section class="mx-auto max-w-7xl px-4 py-12">

    <div class="mb-8">

        <h1 class="text-3xl font-bold">
            سلة التسوق
        </h1>

        <p class="mt-2 text-stone-500">
            راجع المنتجات قبل متابعة الطلب.
        </p>

    </div>


    @if (session('success'))

        <div class="mb-6 rounded-xl bg-green-50 p-4 text-green-700">
            {{ session('success') }}
        </div>

    @endif


    @if ($errors->any())

        <div class="mb-6 rounded-xl bg-red-50 p-4 text-red-700">

            @foreach ($errors->all() as $error)

                <p>{{ $error }}</p>

            @endforeach

        </div>

    @endif


    @if ($items->isEmpty())

        <div
            class="rounded-2xl border border-stone-200 bg-white p-12 text-center"
        >

            <h2 class="text-xl font-bold">
                السلة فارغة
            </h2>

            <p class="mt-2 text-stone-500">
                لم تتم إضافة أي منتجات بعد.
            </p>

            <a
                href="{{ route('store.home') }}"
                class="mt-6 inline-block rounded-xl bg-stone-900 px-6 py-3 text-white"
            >
                متابعة التسوق
            </a>

        </div>

    @else

        <div class="grid gap-8 lg:grid-cols-[1fr_340px]">

            {{-- CART ITEMS --}}
            <div class="space-y-5">

                @foreach ($items as $item)

                    @php
                        $product = $item['product'];
                    @endphp

                    <article
                        class="flex flex-col gap-5 rounded-2xl border border-stone-200 bg-white p-5 sm:flex-row"
                    >

                        {{-- IMAGE --}}
                        <div class="w-full sm:w-36">

                            @if ($product->primaryImage)

                                <img
                                    src="{{ $product->primaryImage->url }}"
                                    alt="{{ $product->name_ar }}"
                                    class="aspect-square w-full rounded-xl object-cover"
                                >

                            @else

                                <div
                                    class="flex aspect-square items-center justify-center rounded-xl bg-stone-100 text-sm text-stone-400"
                                >
                                    لا توجد صورة
                                </div>

                            @endif

                        </div>


                        {{-- INFO --}}
                        <div class="flex flex-1 flex-col justify-between">

                            <div>

                                <p class="text-sm text-stone-500">
                                    {{ $product->category->section->name_ar }}
                                    /
                                    {{ $product->category->name_ar }}
                                </p>

                                <a
                                    href="{{ route(
                                        'store.product',
                                        [
                                            $product->category->section->slug,
                                            $product->slug
                                        ]
                                    ) }}"
                                >
                                    <h2 class="mt-1 text-lg font-bold">
                                        {{ $product->name_ar }}
                                    </h2>
                                </a>


                                @if ($product->size)

                                    <p class="mt-2 text-sm text-stone-500">
                                        المقاس:
                                        {{ $product->size }}
                                    </p>

                                @endif


                                <p class="mt-2 font-semibold">
                                    ${{ number_format($item['unit_price'], 2) }}
                                </p>

                            </div>


                            <div
                                class="mt-5 flex flex-wrap items-end justify-between gap-4"
                            >

                                {{-- QUANTITY --}}
                                <form
                                    method="POST"
                                    action="{{ route('cart.update', $product) }}"
                                    class="flex items-end gap-2"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <div>

                                        <label
                                            for="quantity-{{ $product->id }}"
                                            class="mb-1 block text-sm"
                                        >
                                            الكمية
                                        </label>

                                        <input
                                            type="number"
                                            id="quantity-{{ $product->id }}"
                                            name="quantity"
                                            value="{{ $item['quantity'] }}"
                                            min="1"
                                            max="{{ $product->available_quantity }}"
                                            class="w-20 rounded-lg border border-stone-300 px-3 py-2"
                                            required
                                        >

                                    </div>

                                    <button
                                        type="submit"
                                        class="rounded-lg border border-stone-300 px-4 py-2"
                                    >
                                        تحديث
                                    </button>

                                </form>


                                {{-- REMOVE --}}
                                <form
                                    method="POST"
                                    action="{{ route('cart.destroy', $product) }}"
                                    onsubmit="return confirm('حذف المنتج من السلة؟');"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="text-sm text-red-600"
                                    >
                                        حذف
                                    </button>

                                </form>

                            </div>

                        </div>


                        {{-- SUBTOTAL --}}
                        <div class="sm:text-left">

                            <p class="text-sm text-stone-500">
                                المجموع
                            </p>

                            <strong class="text-lg">
                                ${{ number_format($item['subtotal'], 2) }}
                            </strong>

                        </div>

                    </article>

                @endforeach


                {{-- CLEAR CART --}}
                <form
                    method="POST"
                    action="{{ route('cart.clear') }}"
                    onsubmit="return confirm('هل تريد إفراغ السلة بالكامل؟');"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="text-sm text-red-600"
                    >
                        إفراغ السلة
                    </button>

                </form>

            </div>


            {{-- SUMMARY --}}
            <aside>

                <div
                    class="rounded-2xl border border-stone-200 bg-white p-6"
                >

                    <h2 class="text-xl font-bold">
                        ملخص الطلب
                    </h2>


                    <div
                        class="mt-6 flex justify-between border-b border-stone-100 pb-4"
                    >
                        <span class="text-stone-500">
                            المنتجات
                        </span>

                        <span>
                            {{ $items->sum('quantity') }}
                        </span>
                    </div>


                    <div
                        class="mt-4 flex justify-between"
                    >
                        <span class="font-semibold">
                            المجموع
                        </span>

                        <strong class="text-xl">
                            ${{ number_format($subtotal, 2) }}
                        </strong>
                    </div>


                    <p class="mt-3 text-sm text-stone-500">
                        رسوم التوصيل ستُحسب أثناء إتمام الطلب حسب المنطقة.
                    </p>


                    <button
                        type="button"
                        class="mt-6 w-full rounded-xl bg-stone-900 px-6 py-4 font-semibold text-white"
                    >
                        متابعة الطلب
                    </button>

                </div>

            </aside>

        </div>

    @endif

</section>

@endsection