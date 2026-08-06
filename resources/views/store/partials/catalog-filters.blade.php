<form
    method="GET"
    action="{{ url()->current() }}"
    class="mb-10 grid gap-4 rounded-2xl border border-stone-200 bg-white p-5 md:grid-cols-4"
>

    {{-- SEARCH --}}
    <div>
        <label
            for="q"
            class="mb-2 block text-sm font-medium"
        >
            بحث
        </label>

        <input
            type="text"
            name="q"
            id="q"
            value="{{ request('q') }}"
            placeholder="اسم المنتج أو الحجر..."
            class="w-full rounded-xl border border-stone-300 px-4 py-3"
        >
    </div>


    {{-- STONE --}}
    <div>
        <label
            for="stone"
            class="mb-2 block text-sm font-medium"
        >
            الحجر
        </label>

        <select
            name="stone"
            id="stone"
            class="w-full rounded-xl border border-stone-300 px-4 py-3"
        >

            <option value="">
                كل الأحجار
            </option>

            @foreach ($stones as $stone)

                <option
                    value="{{ $stone }}"
                    @selected(request('stone') === $stone)
                >
                    {{ $stone }}
                </option>

            @endforeach

        </select>
    </div>


    {{-- AVAILABILITY --}}
    <div>
        <label
            for="availability"
            class="mb-2 block text-sm font-medium"
        >
            التوفر
        </label>

        <select
            name="availability"
            id="availability"
            class="w-full rounded-xl border border-stone-300 px-4 py-3"
        >

            <option value="">
                الكل
            </option>

            <option
                value="available"
                @selected(request('availability') === 'available')
            >
                متوفر
            </option>

            <option
                value="sold_out"
                @selected(request('availability') === 'sold_out')
            >
                غير متوفر
            </option>

        </select>
    </div>


    {{-- SORT --}}
    <div>
        <label
            for="sort"
            class="mb-2 block text-sm font-medium"
        >
            الترتيب
        </label>

        <select
            name="sort"
            id="sort"
            class="w-full rounded-xl border border-stone-300 px-4 py-3"
        >

            <option value="newest">
                الأحدث
            </option>

            <option
                value="price_asc"
                @selected(request('sort') === 'price_asc')
            >
                السعر: من الأقل للأعلى
            </option>

            <option
                value="price_desc"
                @selected(request('sort') === 'price_desc')
            >
                السعر: من الأعلى للأقل
            </option>

        </select>
    </div>


    <div class="flex gap-3 md:col-span-4">

        <button
            type="submit"
            class="rounded-xl bg-stone-900 px-6 py-3 text-white"
        >
            تطبيق
        </button>

        <a
            href="{{ url()->current() }}"
            class="rounded-xl border border-stone-300 px-6 py-3"
        >
            إعادة ضبط
        </a>

    </div>

</form>