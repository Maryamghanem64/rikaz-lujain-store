@extends('layouts.store')
@section('title', 'تم استلام الطلب | ركاز × لجين')
@section('content')
<section class="container-shell py-16 sm:py-20">
    <div class="mx-auto max-w-2xl text-center">
        <p class="text-2xl text-lujain-700">✓</p>
        <p class="mt-5 text-xs tracking-[.22em] text-muted-600">ORDER RECEIVED</p>
        <h1 class="editorial-title mt-4 text-5xl sm:text-6xl">تم استلام طلبك</h1>
        <p class="mt-5 text-sm leading-7 text-muted-600">شكرًا لاختيارك ركاز × لجين. احتفظ برقم الطلب.</p>
        <div class="mt-12 border-y border-line-200 py-8"><p class="text-xs text-muted-600">رقم الطلب</p><strong dir="ltr" class="mt-2 block text-2xl tracking-wider">{{ $order->order_number }}</strong></div>
        <dl class="mx-auto mt-6 max-w-lg divide-y divide-line-200 text-sm">
            <div class="flex justify-between py-4"><dt class="text-muted-600">المجموع</dt><dd dir="ltr">&#36;{{ number_format((float) $order->total, 2) }}</dd></div>
            <div class="flex justify-between py-4"><dt class="text-muted-600">طريقة الدفع</dt><dd>{{ $order->payment_method === 'whish' ? 'Whish Money' : 'نقدًا عند الاستلام' }}</dd></div>
            <div class="flex justify-between gap-5 py-4"><dt class="text-muted-600">الحالة</dt><dd>{{ $order->status === 'awaiting_payment_verification' ? 'بانتظار التحقق من الدفع' : 'بانتظار التواصل' }}</dd></div>
        </dl>
        <p class="mx-auto mt-8 max-w-lg text-sm leading-7 text-muted-600">{{ $order->payment_method === 'whish' ? 'استلمنا إيصال Whish وسيتم التحقق من وصول المبلغ قبل التأكيد.' : 'سيتواصل المتجر معك لتأكيد الطلب ومعلومات التوصيل.' }}</p>
        <a href="{{ route('store.home') }}" class="mt-10 inline-flex min-h-12 items-center justify-center rounded-sm bg-lujain-800 px-8 text-sm text-white transition hover:bg-lujain-700">العودة إلى المتجر</a>
    </div>
</section>
@endsection
