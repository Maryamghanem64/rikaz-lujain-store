@extends('layouts.admin')

@section('title', 'إدارة الطلبات')

@section('content')

<div class="space-y-6">

    <div>
        <h2 class="text-2xl font-bold">
            إدارة الطلبات
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            متابعة جميع طلبات ركاز × لجين
        </p>
    </div>


    {{-- SUCCESS --}}
    @if (session('success'))

        <div class="rounded-lg bg-green-50 p-4 text-green-700">
            {{ session('success') }}
        </div>

    @endif


    {{-- ERRORS --}}
    @if ($errors->any())

        <div class="rounded-lg bg-red-50 p-4 text-red-700">

            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach

        </div>

    @endif


    {{-- FILTERS --}}
    <form
        method="GET"
        action="{{ route('admin.orders.index') }}"
        class="grid gap-4 rounded-xl border bg-white p-5 md:grid-cols-4"
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
                placeholder="رقم الطلب، الاسم أو الهاتف"
                class="w-full rounded-lg border px-3 py-2"
            >

        </div>


        {{-- STATUS --}}
        <div>

            <label
                for="status"
                class="mb-2 block text-sm font-medium"
            >
                حالة الطلب
            </label>

            <select
                name="status"
                id="status"
                class="w-full rounded-lg border px-3 py-2"
            >

                <option value="">
                    كل الحالات
                </option>

                <option
                    value="new_cash"
                    @selected(request('status') === 'new_cash')
                >
                    طلب نقدي جديد
                </option>

                <option
                    value="awaiting_payment_verification"
                    @selected(
                        request('status')
                        === 'awaiting_payment_verification'
                    )
                >
                    بانتظار التحقق من الدفع
                </option>

                <option
                    value="payment_rejected"
                    @selected(
                        request('status')
                        === 'payment_rejected'
                    )
                >
                    الدفع مرفوض
                </option>

                <option
                    value="confirmed"
                    @selected(request('status') === 'confirmed')
                >
                    مؤكد
                </option>

                <option
                    value="preparing"
                    @selected(request('status') === 'preparing')
                >
                    قيد التحضير
                </option>

                <option
                    value="shipped"
                    @selected(request('status') === 'shipped')
                >
                    تم الشحن
                </option>

                <option
                    value="delivered"
                    @selected(request('status') === 'delivered')
                >
                    تم التسليم
                </option>

                <option
                    value="cancelled"
                    @selected(request('status') === 'cancelled')
                >
                    ملغى
                </option>

            </select>

        </div>


        {{-- PAYMENT --}}
        <div>

            <label
                for="payment_method"
                class="mb-2 block text-sm font-medium"
            >
                طريقة الدفع
            </label>

            <select
                name="payment_method"
                id="payment_method"
                class="w-full rounded-lg border px-3 py-2"
            >

                <option value="">
                    الكل
                </option>

                <option
                    value="cash"
                    @selected(request('payment_method') === 'cash')
                >
                    نقدًا
                </option>

                <option
                    value="whish"
                    @selected(request('payment_method') === 'whish')
                >
                    Whish Money
                </option>

            </select>

        </div>


        {{-- BUTTONS --}}
        <div class="flex items-end gap-2">

            <button
                type="submit"
                class="rounded-lg bg-gray-900 px-5 py-2 text-white"
            >
                تطبيق
            </button>

            <a
                href="{{ route('admin.orders.index') }}"
                class="rounded-lg border px-5 py-2"
            >
                إعادة ضبط
            </a>

        </div>

    </form>


    {{-- ORDERS --}}
    <div class="overflow-x-auto rounded-xl border bg-white">

        <table class="min-w-full text-right text-sm">

            <thead class="border-b bg-gray-50">

                <tr>

                    <th class="px-4 py-3">
                        رقم الطلب
                    </th>

                    <th class="px-4 py-3">
                        العميل
                    </th>

                    <th class="px-4 py-3">
                        الدفع
                    </th>

                    <th class="px-4 py-3">
                        المجموع
                    </th>

                    <th class="px-4 py-3">
                        الحالة
                    </th>

                    <th class="px-4 py-3">
                        التاريخ
                    </th>

                    <th class="px-4 py-3">
                        تفاصيل
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse ($orders as $order)

                    <tr class="border-b">

                        {{-- NUMBER --}}
                        <td class="px-4 py-4 font-semibold">

                            {{ $order->order_number }}

                        </td>


                        {{-- CUSTOMER --}}
                        <td class="px-4 py-4">

                            <p class="font-medium">
                                {{ $order->customer_name }}
                            </p>

                            <p class="text-xs text-gray-500">
                                {{ $order->customer_phone }}
                            </p>

                        </td>


                        {{-- PAYMENT --}}
                        <td class="px-4 py-4">

                            @if ($order->payment_method === 'whish')

                                <span class="font-medium">
                                    Whish
                                </span>

                                @if (
                                    $order->latestPaymentProof &&
                                    $order->latestPaymentProof
                                        ->review_status === 'pending'
                                )

                                    <p class="mt-1 text-xs text-amber-600">
                                        إيصال بانتظار المراجعة
                                    </p>

                                @elseif (
                                    $order->payment_status === 'verified'
                                )

                                    <p class="mt-1 text-xs text-green-600">
                                        تم التحقق
                                    </p>

                                @elseif (
                                    $order->payment_status === 'rejected'
                                )

                                    <p class="mt-1 text-xs text-red-600">
                                        مرفوض
                                    </p>

                                @endif

                            @else

                                نقدًا

                            @endif

                        </td>


                        {{-- TOTAL --}}
                        <td class="px-4 py-4 font-semibold">

                            ${{ number_format(
                                (float) $order->total,
                                2
                            ) }}

                        </td>


                        {{-- STATUS --}}
                        <td class="px-4 py-4">

                            @switch($order->status)

                                @case('new_cash')
                                    <span>
                                        طلب نقدي جديد
                                    </span>
                                    @break


                                @case('awaiting_payment_verification')
                                    <span class="text-amber-600">
                                        بانتظار التحقق
                                    </span>
                                    @break


                                @case('payment_rejected')
                                    <span class="text-red-600">
                                        الدفع مرفوض
                                    </span>
                                    @break


                                @case('confirmed')
                                    <span class="text-green-600">
                                        مؤكد
                                    </span>
                                    @break


                                @case('preparing')
                                    <span>
                                        قيد التحضير
                                    </span>
                                    @break


                                @case('shipped')
                                    <span>
                                        تم الشحن
                                    </span>
                                    @break


                                @case('delivered')
                                    <span class="text-green-700">
                                        تم التسليم
                                    </span>
                                    @break


                                @case('cancelled')
                                    <span class="text-gray-500">
                                        ملغى
                                    </span>
                                    @break


                                @default
                                    {{ $order->status }}

                            @endswitch

                        </td>


                        {{-- DATE --}}
                        <td class="px-4 py-4 text-gray-500">

                            {{ $order->created_at->format(
                                'Y-m-d H:i'
                            ) }}

                        </td>


                        {{-- DETAILS --}}
                        <td class="px-4 py-4">

                            <a
                                href="{{ route(
                                    'admin.orders.show',
                                    $order
                                ) }}"
                                class="font-semibold underline"
                            >
                                فتح الطلب
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="px-4 py-12 text-center text-gray-500"
                        >
                            لا توجد طلبات حاليًا.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- PAGINATION --}}
    <div>

        {{ $orders->links() }}

    </div>

</div>

@endsection