@extends('layouts.store')

@section('title', 'تم استلام الطلب | ركاز × لجين')

@section('content')

<section class="mx-auto max-w-3xl px-4 py-16">

    <div
        class="rounded-3xl border border-stone-200 bg-white p-8 text-center"
    >

        <div class="text-5xl">
            ✓
        </div>


        <h1 class="mt-5 text-3xl font-bold">
            تم استلام طلبك
        </h1>


        <p class="mt-3 text-stone-500">
            احتفظ برقم الطلب لمتابعته لاحقًا.
        </p>


        <div
            class="mt-8 rounded-2xl bg-stone-100 p-6"
        >

            <p class="text-sm text-stone-500">
                رقم الطلب
            </p>

            <strong class="mt-1 block text-2xl">
                {{ $order->order_number }}
            </strong>

        </div>


        <div class="mt-8 space-y-3 text-right">

            <div class="flex justify-between">

                <span>
                    المجموع
                </span>

                <strong>
                    ${{ number_format((float) $order->total, 2) }}
                </strong>

            </div>


            <div class="flex justify-between">

                <span>
                    طريقة الدفع
                </span>

                <strong>
                    {{ $order->payment_method === 'whish'
                        ? 'Whish Money'
                        : 'نقدًا' }}
                </strong>

            </div>


            <div class="flex justify-between">

                <span>
                    الحالة
                </span>

                <strong>

                    @if (
                        $order->status
                            === 'awaiting_payment_verification'
                    )

                        بانتظار التحقق من الدفع

                    @else

                        طلب جديد بانتظار التواصل

                    @endif

                </strong>

            </div>

        </div>


        @if ($order->payment_method === 'whish')

            <div
                class="mt-8 rounded-xl bg-amber-50 p-4 text-sm text-amber-800"
            >
                تم استلام صورة إيصال Whish.
                سيتم التحقق من وصول المبلغ قبل تأكيد البيع.
            </div>

        @else

            <div
                class="mt-8 rounded-xl bg-stone-50 p-4 text-sm text-stone-600"
            >
                سيتواصل المتجر معك لتأكيد الطلب
                ومعلومات التوصيل.
            </div>

        @endif


        <a
            href="{{ route('store.home') }}"
            class="mt-8 inline-block rounded-xl bg-stone-900 px-7 py-3 text-white"
        >
            العودة للمتجر
        </a>

    </div>

</section>

@endsection