@extends('layouts.admin')

@section('title', 'تفاصيل الطلب')

@section('content')

<div class="space-y-8">

    {{-- HEADER --}}
    <div class="flex flex-wrap items-start justify-between gap-4">

        <div>

            <a
                href="{{ route('admin.orders.index') }}"
                class="text-sm text-gray-500 underline"
            >
                العودة إلى الطلبات
            </a>

            <h2 class="mt-2 text-2xl font-bold">
                الطلب {{ $order->order_number }}
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                {{ $order->created_at->format('Y-m-d H:i') }}
            </p>

        </div>


        {{-- CURRENT STATUS --}}
        <div>

            @switch($order->status)

                @case('new_cash')
                    <span class="rounded-full bg-blue-50 px-4 py-2 text-blue-700">
                        طلب نقدي جديد
                    </span>
                    @break

                @case('awaiting_payment_verification')
                    <span class="rounded-full bg-amber-50 px-4 py-2 text-amber-700">
                        بانتظار التحقق من الدفع
                    </span>
                    @break

                @case('payment_rejected')
                    <span class="rounded-full bg-red-50 px-4 py-2 text-red-700">
                        الدفع مرفوض
                    </span>
                    @break

                @case('confirmed')
                    <span class="rounded-full bg-green-50 px-4 py-2 text-green-700">
                        مؤكد
                    </span>
                    @break

                @case('preparing')
                    <span class="rounded-full bg-purple-50 px-4 py-2 text-purple-700">
                        قيد التحضير
                    </span>
                    @break

                @case('shipped')
                    <span class="rounded-full bg-indigo-50 px-4 py-2 text-indigo-700">
                        تم الشحن
                    </span>
                    @break

                @case('delivered')
                    <span class="rounded-full bg-green-100 px-4 py-2 text-green-800">
                        تم التسليم
                    </span>
                    @break

                @case('cancelled')
                    <span class="rounded-full bg-gray-100 px-4 py-2 text-gray-600">
                        ملغى
                    </span>
                    @break

                @default
                    <span class="rounded-full bg-gray-100 px-4 py-2">
                        {{ $order->status }}
                    </span>

            @endswitch

        </div>

    </div>


    {{-- SUCCESS --}}
    @if (session('success'))

        <div class="rounded-xl bg-green-50 p-4 text-green-700">
            {{ session('success') }}
        </div>

    @endif


    {{-- ERRORS --}}
    @if ($errors->any())

        <div class="rounded-xl bg-red-50 p-4 text-red-700">

            @foreach ($errors->all() as $error)

                <p>
                    {{ $error }}
                </p>

            @endforeach

        </div>

    @endif


    <div class="grid gap-8 lg:grid-cols-[1fr_360px]">

        {{-- LEFT SIDE --}}
        <div class="space-y-8">


            {{-- CUSTOMER INFORMATION --}}
            <section class="rounded-xl border bg-white p-6">

                <h3 class="mb-5 text-lg font-bold">
                    معلومات العميل
                </h3>


                <div class="grid gap-4 md:grid-cols-2">

                    <div>

                        <p class="text-sm text-gray-500">
                            الاسم
                        </p>

                        <strong>
                            {{ $order->customer_name }}
                        </strong>

                    </div>


                    <div>

                        <p class="text-sm text-gray-500">
                            رقم الهاتف
                        </p>

                        <strong>
                            {{ $order->customer_phone }}
                        </strong>

                    </div>


                    <div>

                        <p class="text-sm text-gray-500">
                            WhatsApp
                        </p>

                        <strong>
                            {{ $order->customer_whatsapp ?: '-' }}
                        </strong>

                    </div>


                    <div>

                        <p class="text-sm text-gray-500">
                            منطقة التوصيل
                        </p>

                        <strong>
                            {{ $order->delivery_zone_name }}
                        </strong>

                    </div>

                </div>


                <div class="mt-5">

                    <p class="text-sm text-gray-500">
                        العنوان
                    </p>

                    <p class="mt-1">
                        {{ $order->address }}
                    </p>

                </div>


                @if ($order->notes)

                    <div class="mt-5">

                        <p class="text-sm text-gray-500">
                            ملاحظات العميل
                        </p>

                        <p class="mt-1 whitespace-pre-line">
                            {{ $order->notes }}
                        </p>

                    </div>

                @endif

            </section>



            {{-- PRODUCTS --}}
            @php
                $orderBrands = $order->items
                    ->map(fn ($item) => $item->product?->category?->section?->name_ar)
                    ->filter()
                    ->unique()
                    ->values();
            @endphp
            <section class="rounded-xl border bg-white p-6">

                <h3 class="mb-5 text-lg font-bold">
                    المنتجات
                </h3>

                <div class="mb-5 flex flex-wrap gap-2">
                    @foreach ($orderBrands as $brand)
                        <span class="status-badge bg-[#f4ecee] text-rikaz-800">{{ $brand }}</span>
                    @endforeach
                    @if ($orderBrands->count() > 1)
                        <span class="status-badge border border-rikaz-700/20 bg-white text-rikaz-800">طلب مختلط العلامات</span>
                    @endif
                </div>


                <div class="space-y-5">

                    @forelse ($order->items as $item)

                        <div class="flex justify-between gap-5 border-b pb-5 last:border-0">

                            <div>

                                <p class="font-semibold">
                                    {{ $item->product_name_ar }}
                                </p>

                                @if ($item->product?->category?->section)
                                    <span class="mt-2 inline-flex rounded-full bg-[#f4ecee] px-2.5 py-1 text-[11px] font-semibold text-rikaz-800">{{ $item->product->category->section->name_ar }}</span>
                                @endif


                                @if ($item->stone_name)

                                    <p class="mt-1 text-sm text-gray-500">
                                        الحجر:
                                        {{ $item->stone_name }}
                                    </p>

                                @endif


                                @if ($item->stone_weight)

                                    <p class="mt-1 text-sm text-gray-500">
                                        وزن الحجر:
                                        {{ $item->stone_weight }}
                                    </p>

                                @endif


                                @if ($item->silver_purity)

                                    <p class="mt-1 text-sm text-gray-500">
                                        عيار الفضة:
                                        {{ $item->silver_purity }}
                                    </p>

                                @endif


                                @if ($item->size)

                                    <p class="mt-1 text-sm text-gray-500">
                                        المقاس:
                                        {{ $item->size }}
                                    </p>

                                @endif


                                <p class="mt-1 text-sm text-gray-500">
                                    الكمية:
                                    {{ $item->quantity }}
                                </p>

                            </div>


                            <div class="text-left">

                                <p class="text-sm text-gray-500">
                                    سعر القطعة
                                </p>

                                <p>
                                    ${{ number_format(
                                        (float) $item->unit_price,
                                        2
                                    ) }}
                                </p>

                                <strong class="mt-2 block">
                                    ${{ number_format(
                                        (float) $item->subtotal,
                                        2
                                    ) }}
                                </strong>

                            </div>

                        </div>

                    @empty

                        <p class="text-gray-500">
                            لا توجد منتجات.
                        </p>

                    @endforelse

                </div>

            </section>



            {{-- WHISH PAYMENT PROOFS --}}
            @if ($order->payment_method === 'whish')

                <section class="rounded-xl border bg-white p-6">

                    <h3 class="mb-5 text-lg font-bold">
                        إثبات دفع Whish
                    </h3>


                    @forelse ($order->paymentProofs as $proof)

                        <div class="mb-6 rounded-xl border p-4 last:mb-0">


                            {{-- RECEIPT IMAGE --}}
                            <a
                                href="{{ route('admin.orders.payment-proofs.file', [$order, $proof]) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="block"
                            >

                                <img
                                    src="{{ route('admin.orders.payment-proofs.file', [$order, $proof]) }}"
                                    alt="إيصال Whish"
                                    class="mx-auto max-h-96 max-w-full rounded-lg object-contain"
                                >

                            </a>


                            {{-- PROOF INFORMATION --}}
                            <div class="mt-4 space-y-1">

                                <p>

                                    الحالة:

                                    <strong>

                                        @switch($proof->review_status)

                                            @case('pending')
                                                <span class="text-amber-600">
                                                    بانتظار المراجعة
                                                </span>
                                                @break

                                            @case('verified')
                                                <span class="text-green-700">
                                                    تم التحقق
                                                </span>
                                                @break

                                            @case('rejected')
                                                <span class="text-red-600">
                                                    مرفوض
                                                </span>
                                                @break

                                            @default
                                                {{ $proof->review_status }}

                                        @endswitch

                                    </strong>

                                </p>


                                @if ($proof->reviewed_at)

                                    <p class="text-sm text-gray-500">
                                        تاريخ المراجعة:
                                        {{ $proof->reviewed_at->format('Y-m-d H:i') }}
                                    </p>

                                @endif


                                @if ($proof->reviewer)

                                    <p class="text-sm text-gray-500">
                                        تمت المراجعة بواسطة:
                                        {{ $proof->reviewer->name }}
                                    </p>

                                @endif


                                @if ($proof->rejection_reason)

                                    <div class="mt-3 rounded-lg bg-red-50 p-3 text-red-700">

                                        <strong>
                                            سبب الرفض:
                                        </strong>

                                        {{ $proof->rejection_reason }}

                                    </div>

                                @endif

                            </div>



                            {{-- PAYMENT REVIEW BUTTONS --}}
                            @if (
                                $proof->review_status === 'pending' &&
                                $order->status === 'awaiting_payment_verification'
                            )

                                <div class="mt-6 space-y-4">


                                    {{-- ACCEPT / VERIFY --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.orders.payment-proofs.verify',
                                            [$order, $proof]
                                        ) }}"
                                        onsubmit="return confirm('هل تأكدت من وصول المبلغ على Whish؟');"
                                    >
                                        @csrf

                                        <button
                                            type="submit"
                                            class="w-full rounded-lg bg-rikaz-700 px-4 py-3 font-semibold text-white hover:bg-rikaz-800"
                                        >
                                            ✓ قبول الإيصال وتأكيد الدفع
                                        </button>

                                    </form>



                                    {{-- REJECT --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.orders.payment-proofs.reject',
                                            [$order, $proof]
                                        ) }}"
                                    >
                                        @csrf


                                        <label
                                            for="rejection_reason_{{ $proof->id }}"
                                            class="mb-2 block text-sm font-medium"
                                        >
                                            سبب الرفض
                                        </label>


                                        <textarea
                                            id="rejection_reason_{{ $proof->id }}"
                                            name="rejection_reason"
                                            rows="3"
                                            placeholder="مثال: المبلغ لم يصل أو الإيصال غير واضح"
                                            class="w-full rounded-lg border p-3"
                                            required
                                        ></textarea>


                                        <button
                                            type="submit"
                                            class="mt-2 w-full rounded-lg bg-red-700 px-4 py-3 font-semibold text-white hover:bg-red-800"
                                        >
                                            ✕ رفض الإيصال
                                        </button>

                                    </form>

                                </div>

                            @endif

                        </div>

                    @empty

                        <p class="text-gray-500">
                            لا يوجد إثبات دفع لهذا الطلب.
                        </p>

                    @endforelse

                </section>

            @endif



            {{-- ORDER STATUS HISTORY --}}
            <section class="rounded-xl border bg-white p-6">

                <h3 class="mb-5 text-lg font-bold">
                    سجل حالة الطلب
                </h3>


                <div class="space-y-5">

                    @forelse (
                        $order->statusHistory->sortByDesc('created_at')
                        as $history
                    )

                        <div class="border-r-2 border-gray-300 pr-4">

                            <p class="font-semibold">

                                @switch($history->status)

                                    @case('new_cash')
                                        طلب نقدي جديد
                                        @break

                                    @case('awaiting_payment_verification')
                                        بانتظار التحقق من الدفع
                                        @break

                                    @case('payment_rejected')
                                        الدفع مرفوض
                                        @break

                                    @case('confirmed')
                                        تم تأكيد الطلب
                                        @break

                                    @case('preparing')
                                        قيد التحضير
                                        @break

                                    @case('shipped')
                                        تم الشحن
                                        @break

                                    @case('delivered')
                                        تم التسليم
                                        @break

                                    @case('cancelled')
                                        تم الإلغاء
                                        @break

                                    @default
                                        {{ $history->status }}

                                @endswitch

                            </p>


                            <p class="mt-1 text-sm text-gray-500">
                                {{ $history->created_at->format(
                                    'Y-m-d H:i'
                                ) }}
                            </p>


                            @if ($history->changedBy)

                                <p class="text-sm text-gray-500">
                                    بواسطة:
                                    {{ $history->changedBy->name }}
                                </p>

                            @endif


                            @if ($history->note)

                                <p class="mt-2 text-sm">
                                    {{ $history->note }}
                                </p>

                            @endif

                        </div>

                    @empty

                        <p class="text-gray-500">
                            لا يوجد سجل بعد.
                        </p>

                    @endforelse

                </div>

            </section>

        </div>



        {{-- RIGHT SIDE --}}
        <aside class="space-y-6">


            {{-- ORDER SUMMARY --}}
            <section class="rounded-xl border bg-white p-6">

                <h3 class="mb-5 text-lg font-bold">
                    ملخص الطلب
                </h3>


                <div class="flex justify-between py-2">

                    <span>
                        المنتجات
                    </span>

                    <span>
                        ${{ number_format(
                            (float) $order->subtotal,
                            2
                        ) }}
                    </span>

                </div>


                <div class="flex justify-between py-2">

                    <span>
                        التوصيل
                    </span>

                    <span>
                        ${{ number_format(
                            (float) $order->delivery_fee,
                            2
                        ) }}
                    </span>

                </div>


                <div class="mt-3 flex justify-between border-t pt-4 text-lg">

                    <strong>
                        الإجمالي
                    </strong>

                    <strong>
                        ${{ number_format(
                            (float) $order->total,
                            2
                        ) }}
                    </strong>

                </div>

            </section>



            {{-- PAYMENT INFORMATION --}}
            <section class="rounded-xl border bg-white p-6">

                <h3 class="mb-4 font-bold">
                    معلومات الدفع
                </h3>


                <p>

                    الطريقة:

                    <strong>

                        {{ $order->payment_method === 'whish'
                            ? 'Whish Money'
                            : 'نقدًا عند الاستلام' }}

                    </strong>

                </p>


                <p class="mt-2">

                    حالة الدفع:

                    <strong>

                        @switch($order->payment_status)

                            @case('cash_pending')
                                بانتظار الدفع عند الاستلام
                                @break

                            @case('pending_verification')
                                بانتظار التحقق
                                @break

                            @case('verified')
                                تم التحقق
                                @break

                            @case('rejected')
                                مرفوض
                                @break

                            @case('paid_on_delivery')
                                مدفوع عند الاستلام
                                @break

                            @default
                                {{ $order->payment_status }}

                        @endswitch

                    </strong>

                </p>


                @if ($order->reservation_expires_at)

                    <p class="mt-3 text-sm text-gray-500">
                        انتهاء الحجز:
                        {{ $order->reservation_expires_at->format(
                            'Y-m-d H:i'
                        ) }}
                    </p>

                @endif

            </section>



            {{-- ORDER ACTIONS --}}
            <section class="rounded-xl border bg-white p-6">

                <h3 class="mb-5 font-bold">
                    إدارة الطلب
                </h3>



                {{-- CASH CONFIRM --}}
                @if (
                    $order->payment_method === 'cash' &&
                    $order->status === 'new_cash'
                )

                    <form
                        method="POST"
                        action="{{ route(
                            'admin.orders.confirm-cash',
                            $order
                        ) }}"
                    >
                        @csrf


                        <textarea
                            name="note"
                            rows="2"
                            placeholder="ملاحظة اختيارية"
                            class="w-full rounded-lg border p-3"
                        ></textarea>


                        <button
                            type="submit"
                            class="mt-3 w-full rounded-lg bg-rikaz-700 px-4 py-3 font-semibold text-white"
                            onclick="return confirm('تأكيد هذا الطلب؟');"
                        >
                            ✓ تأكيد الطلب النقدي
                        </button>

                    </form>

                @endif



                {{-- WHISH WAITING MESSAGE --}}
                @if (
                    $order->payment_method === 'whish' &&
                    $order->status === 'awaiting_payment_verification'
                )

                    <div class="rounded-lg bg-amber-50 p-4 text-sm text-amber-800">

                        يجب مراجعة إيصال Whish أولًا.

                        <p class="mt-1">
                            استخدمي زر
                            <strong>
                                قبول الإيصال وتأكيد الدفع
                            </strong>
                            الموجود بجانب صورة الإيصال.
                        </p>

                    </div>

                @endif



                {{-- CONFIRMED → PREPARING --}}
                @if ($order->status === 'confirmed')

                    <form
                        method="POST"
                        action="{{ route(
                            'admin.orders.advance',
                            $order
                        ) }}"
                    >
                        @csrf
                        @method('PATCH')


                        <input
                            type="hidden"
                            name="status"
                            value="preparing"
                        >


                        <button
                            type="submit"
                            class="w-full rounded-lg bg-purple-700 px-4 py-3 text-white"
                        >
                            بدء التحضير
                        </button>

                    </form>

                @endif



                {{-- PREPARING → SHIPPED --}}
                @if ($order->status === 'preparing')

                    <form
                        method="POST"
                        action="{{ route(
                            'admin.orders.advance',
                            $order
                        ) }}"
                    >
                        @csrf
                        @method('PATCH')


                        <input
                            type="hidden"
                            name="status"
                            value="shipped"
                        >


                        <button
                            type="submit"
                            class="w-full rounded-lg bg-indigo-700 px-4 py-3 text-white"
                        >
                            تم الشحن
                        </button>

                    </form>

                @endif



                {{-- SHIPPED → DELIVERED --}}
                @if ($order->status === 'shipped')

                    <form
                        method="POST"
                        action="{{ route(
                            'admin.orders.advance',
                            $order
                        ) }}"
                    >
                        @csrf
                        @method('PATCH')


                        <input
                            type="hidden"
                            name="status"
                            value="delivered"
                        >


                        <button
                            type="submit"
                            class="w-full rounded-lg bg-rikaz-700 px-4 py-3 text-white hover:bg-rikaz-800"
                        >
                            ✓ تم التسليم
                        </button>

                    </form>

                @endif
@if (
    in_array(
        $order->status,
        [
            'new_cash',
            'awaiting_payment_verification',
            'payment_rejected',
        ],
        true
    )
)

    <div class="mt-4 border-t pt-4">

        <form
            method="POST"
            action="{{ route(
                'admin.orders.release-reservation',
                $order
            ) }}"
            onsubmit="return confirm(
                'سيتم تحرير المنتجات المحجوزة وإلغاء الطلب. هل تريد المتابعة؟'
            );"
        >
            @csrf

            <textarea
                name="note"
                rows="2"
                placeholder="سبب تحرير الحجز - اختياري"
                class="w-full rounded-lg border p-3"
            ></textarea>

            <button
                type="submit"
                class="mt-2 w-full rounded-lg bg-amber-600 px-4 py-3 font-semibold text-white"
            >
                تحرير حجز المنتجات
            </button>

        </form>

    </div>

@endif


                {{-- CANCEL --}}
                @if (
                    in_array(
                        $order->status,
                        [
                            'new_cash',
                            'awaiting_payment_verification',
                            'payment_rejected',
                            'confirmed',
                            'preparing',
                        ],
                        true
                    )
                )

                    <form
                        method="POST"
                        action="{{ route(
                            'admin.orders.cancel',
                            $order
                        ) }}"
                        class="mt-4"
                        onsubmit="return confirm('هل أنت متأكد من إلغاء الطلب؟');"
                    >
                        @csrf


                        <textarea
                            name="note"
                            rows="2"
                            placeholder="سبب الإلغاء أو ملاحظة"
                            class="w-full rounded-lg border p-3"
                        ></textarea>


                        <button
                            type="submit"
                            class="mt-2 w-full rounded-lg border border-red-600 px-4 py-3 text-red-600"
                        >
                            إلغاء الطلب
                        </button>

                    </form>

                @endif



                {{-- COMPLETED --}}
                @if ($order->status === 'delivered')

                    <div class="rounded-lg bg-green-50 p-4 text-center text-green-700">
                        ✓ تم إكمال الطلب وتسليمه
                    </div>

                @endif



                {{-- CANCELLED --}}
                @if ($order->status === 'cancelled')

                    <div class="rounded-lg bg-gray-100 p-4 text-center text-gray-600">
                        تم إلغاء هذا الطلب
                    </div>

                @endif

            </section>

        </aside>

    </div>

</div>

@endsection
