<article
    class="overflow-hidden rounded-2xl border border-stone-200 bg-white"
>

    <a
        href="{{ route(
            'store.product',
            [
                $product->category->section->slug,
                $product->slug
            ]
        ) }}"
    >

        <div class="aspect-square overflow-hidden bg-stone-100">

            @if ($product->primaryImage)

                <img
                    src="{{ $product->primaryImage->url }}"
                    alt="{{ $product->name_ar }}"
                    class="h-full w-full object-cover transition duration-300 hover:scale-105"
                >

            @else

                <div
                    class="flex h-full items-center justify-center text-stone-400"
                >
                    لا توجد صورة
                </div>

            @endif

        </div>

    </a>


    <div class="p-4">

        <p class="mb-1 text-sm text-stone-500">
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
            <h3 class="font-semibold">
                {{ $product->name_ar }}
            </h3>
        </a>


        @if ($product->stone_name)

            <p class="mt-1 text-sm text-stone-500">
                {{ $product->stone_name }}
            </p>

        @endif


        <div class="mt-4 flex items-center justify-between">

            <strong class="text-lg">
                ${{ number_format((float) $product->price, 2) }}
            </strong>


            @if ($product->available_quantity > 0)

                <span
                    class="rounded-full bg-green-50 px-3 py-1 text-xs text-green-700"
                >
                    متوفر
                </span>

            @else

                <span
                    class="rounded-full bg-red-50 px-3 py-1 text-xs text-red-700"
                >
                    غير متوفر
                </span>

            @endif

        </div>

    </div>

</article>