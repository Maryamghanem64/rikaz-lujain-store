@extends('layouts.admin')

@section('title', 'لوحة التحكم')

@section('content')

<div class="space-y-8">

    {{-- HEADER --}}
    <div>

        <h2 class="text-2xl font-bold">
            لوحة التحكم
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            نظرة سريعة على المتجر والطلبات والمخزون
        </p>

    </div>


    {{-- MAIN STATS --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

        <div class="rounded-xl border bg-white p-5">

            <p class="text-sm text-gray-500">
                جميع الطلبات
            </p>

            <strong class="mt-2 block text-3xl">
                {{ $stats['total_orders'] }}
            </strong>

        </div>


        <div class="rounded-xl border bg-white p-5">

            <p class="text-sm text-gray-500">
                طلبات نقدية جديدة
            </p>

            <strong class="mt-2 block text-3xl text-blue-700">
                {{ $stats['new_cash'] }}
            </strong>

            <a
                href="{{ route(
                    'admin.orders.index',
                    ['status' => 'new_cash']
                ) }}"
                class="mt-3 inline-block text-sm underline"
            >
                عرض الطلبات
            </a>

        </div>


        <div class="rounded-xl border bg-white p-5">

            <p class="text-sm text-gray-500">
                Whish بانتظار التحقق
            </p>

            <strong class="mt-2 block text-3xl text-amber-600">
                {{ $stats['whish_pending'] }}
            </strong>

            <a
                href="{{ route(
                    'admin.orders.index',
                    [
                        'status' =>
                            'awaiting_payment_verification'
                    ]
                ) }}"
                class="mt-3 inline-block text-sm underline"
            >
                مراجعة الدفعات
            </a>

        </div>


        <div class="rounded-xl border bg-white p-5">

            <p class="text-sm text-gray-500">
                طلبات قيد التنفيذ
            </p>

            <strong class="mt-2 block text-3xl text-purple-700">
                {{ $stats['active_orders'] }}
            </strong>

        </div>

    </div>


    {{-- SECONDARY STATS --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

        <div class="rounded-xl border bg-white p-5">

            <p class="text-sm text-gray-500">
                الطلبات المسلّمة
            </p>

            <strong class="mt-2 block text-2xl text-green-700">
                {{ $stats['delivered_orders'] }}
            </strong>

        </div>


        <div class="rounded-xl border bg-white p-5">

            <p class="text-sm text-gray-500">
                إجمالي المبيعات المسلّمة
            </p>

            <strong class="mt-2 block text-2xl">
                ${{ number_format(
                    (float) $stats['total_sales'],
                    2
                ) }}
            </strong>

        </div>


        <div class="rounded-xl border bg-white p-5">

            <p class="text-sm text-gray-500">
                مخزون منخفض
            </p>

            <strong class="mt-2 block text-2xl text-amber-600">
                {{ $stats['low_stock_products'] }}
            </strong>

        </div>


        <div class="rounded-xl border bg-white p-5">

            <p class="text-sm text-gray-500">
                منتجات غير متوفرة
            </p>

            <strong class="mt-2 block text-2xl text-red-600">
                {{ $stats['out_of_stock_products'] }}
            </strong>

        </div>

    </div>


    <div class="grid gap-8 lg:grid-cols-2">


        {{-- RECENT ORDERS --}}
        <section class="rounded-xl border bg-white p-6">

            <div class="mb-5 flex items-center justify-between">

                <h3 class="text-lg font-bold">
                    أحدث الطلبات
                </h3>

                <a
                    href="{{ route('admin.orders.index') }}"
                    class="text-sm underline"
                >
                    جميع الطلبات
                </a>

            </div>


            <div class="space-y-4">

                @forelse ($recentOrders as $order)

                    <a
                        href="{{ route(
                            'admin.orders.show',
                            $order
                        ) }}"
                        class="flex items-center justify-between rounded-lg border p-4 hover:bg-gray-50"
                    >

                        <div>

                            <p class="font-semibold">
                                {{ $order->order_number }}
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ $order->customer_name }}
                            </p>

                            <p class="text-xs text-gray-400">
                                {{ $order->created_at->format(
                                    'Y-m-d H:i'
                                ) }}
                            </p>

                        </div>


                        <div class="text-left">

                            <strong>
                                ${{ number_format(
                                    (float) $order->total,
                                    2
                                ) }}
                            </strong>


                            <p class="mt-1 text-xs">

                                @switch($order->status)

                                    @case('new_cash')
                                        طلب نقدي جديد
                                        @break

                                    @case('awaiting_payment_verification')
                                        بانتظار التحقق
                                        @break

                                    @case('payment_rejected')
                                        الدفع مرفوض
                                        @break

                                    @case('confirmed')
                                        مؤكد
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
                                        ملغى
                                        @break

                                    @default
                                        {{ $order->status }}

                                @endswitch

                            </p>

                        </div>

                    </a>

                @empty

                    <p class="text-gray-500">
                        لا توجد طلبات بعد.
                    </p>

                @endforelse

            </div>

        </section>



        {{-- LOW STOCK --}}
        <section class="rounded-xl border bg-white p-6">

            <div class="mb-5 flex items-center justify-between">

                <h3 class="text-lg font-bold">
                    تنبيه المخزون
                </h3>

                <a
                    href="{{ route('admin.products.index') }}"
                    class="text-sm underline"
                >
                    إدارة المنتجات
                </a>

            </div>


            <div class="space-y-4">

                @forelse ($lowStockProducts as $product)

                    @php
                        $available =
                            max(
                                0,
                                $product->stock_quantity -
                                $product->reserved_quantity
                            );
                    @endphp


                    <a
                        href="{{ route(
                            'admin.products.edit',
                            $product
                        ) }}"
                        class="flex items-center gap-4 rounded-lg border p-4 hover:bg-gray-50"
                    >

                        <div class="h-16 w-16 shrink-0">

                            @if ($product->primaryImage)

                                <img
                                    src="{{ $product->primaryImage->url }}"
                                    alt="{{ $product->name_ar }}"
                                    class="h-full w-full rounded-lg object-cover"
                                >

                            @else

                                <div
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-gray-100 text-xs text-gray-400"
                                >
                                    لا صورة
                                </div>

                            @endif

                        </div>


                        <div class="flex-1">

                            <p class="font-semibold">
                                {{ $product->name_ar }}
                            </p>

                            <p class="mt-1 text-xs text-gray-500">

                                {{ $product->category->section->name_ar }}

                                /

                                {{ $product->category->name_ar }}

                            </p>

                        </div>


                        <div class="text-left">

                            <p class="text-xs text-gray-500">
                                المتوفر
                            </p>

                            <strong
                                class="{{ $available === 0
                                    ? 'text-red-600'
                                    : 'text-amber-600' }}"
                            >
                                {{ $available }}
                            </strong>

                        </div>

                    </a>

                @empty

                    <div class="rounded-lg bg-green-50 p-4 text-green-700">
                        ✓ لا يوجد نقص بالمخزون حاليًا.
                    </div>

                @endforelse

            </div>

        </section>

    </div>


    {{-- CANCELLED INFO --}}
    <div class="text-sm text-gray-400">
        الطلبات الملغاة:
        {{ $stats['cancelled_orders'] }}
    </div>

</div>

@endsection