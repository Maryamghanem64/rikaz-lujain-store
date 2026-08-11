@extends('layouts.admin')
@section('title', 'لوحة التحكم')
@section('content')
@php($brandName = $section?->name_ar ?? 'المتجر')
<div class="space-y-8">
    <div class="flex flex-wrap items-end justify-between gap-4"><div><p class="eyebrow">{{ $brandName }} · الإدارة</p><h1 class="mt-2 text-3xl font-semibold text-rikaz-800">إدارة {{ $brandName }}</h1><p class="mt-1 text-sm text-muted-600">مخزون علامتك مع نظرة تشغيلية مشتركة على طلبات المتجر.</p></div><span class="status-badge bg-[#f4ecee] text-rikaz-800">{{ auth()->user()->name }}</span></div>

    @unless ($section)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-7 text-amber-900">هذا حساب إداري قديم وغير مرتبط بعلامة بعد. الطلبات والإعدادات المشتركة متاحة، لكن إدارة المنتجات والفئات تحتاج ربط الحساب بقسم ركاز أو لجين.</div>
    @endunless

    <section class="space-y-4"><div><p class="text-xs font-semibold tracking-[.16em] text-rikaz-700">مخزون {{ $brandName }}</p><h2 class="mt-1 text-xl font-semibold">الكتالوج الخاص بعلامتك</h2></div><div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([['إجمالي المنتجات',$stats['total_products']],['الفئات',$stats['total_categories']],['مخزون منخفض',$stats['low_stock_products']],['نفد من المخزون',$stats['out_of_stock_products']]] as [$label,$value])
            <article class="admin-card"><p class="text-sm text-muted-600">{{ $label }}</p><strong class="mt-3 block text-3xl text-rikaz-800">{{ $value }}</strong></article>
        @endforeach
    </div></section>

    <section class="space-y-4 border-t border-line-200 pt-8"><div><p class="text-xs font-semibold tracking-[.16em] text-rikaz-700">طلبات المتجر</p><h2 class="mt-1 text-xl font-semibold">تشغيل مشترك لركاز ولجين</h2></div><div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([['Cash جديد',$stats['new_cash'],'new_cash'],['Whish بانتظار التحقق',$stats['whish_pending'],'awaiting_payment_verification'],['طلبات نشطة',$stats['active_orders'],null],['تم التسليم',$stats['delivered_orders'],'delivered']] as [$label,$value,$status])
            <article class="admin-card"><p class="text-sm text-muted-600">{{ $label }}</p><strong class="mt-3 block text-3xl">{{ $value }}</strong>@if($status)<a href="{{ route('admin.orders.index',['status'=>$status]) }}" class="mt-3 inline-flex text-xs font-semibold text-rikaz-700">عرض الطلبات</a>@endif</article>
        @endforeach
    </div></section>

    <div class="grid gap-6 xl:grid-cols-2">
        <section class="admin-card"><div class="mb-5 flex items-center justify-between"><h2 class="text-lg font-semibold">أحدث طلبات المتجر</h2><a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-rikaz-700">عرض الكل</a></div><div class="divide-y divide-line-200">@forelse($recentOrders as $order)<a href="{{ route('admin.orders.show',$order) }}" class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0"><div><p class="font-semibold">{{ $order->order_number }}</p><p class="text-xs text-muted-600">{{ $order->customer_name }}</p></div><div class="text-left"><p dir="ltr" class="font-semibold">${{ number_format((float)$order->total,2) }}</p><p class="text-xs text-muted-600">{{ $order->status }}</p></div></a>@empty<p class="text-sm text-muted-600">لا توجد طلبات.</p>@endforelse</div></section>
        <section class="admin-card"><div class="mb-5 flex items-center justify-between"><h2 class="text-lg font-semibold">تنبيه مخزون {{ $brandName }}</h2><a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-rikaz-700">إدارة المنتجات</a></div><div class="divide-y divide-line-200">@forelse($lowStockProducts as $product)<a href="{{ route('admin.products.edit',$product) }}" class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0"><div><p class="font-semibold">{{ $product->name_ar }}</p><p class="text-xs text-muted-600">{{ $product->category->name_ar }}</p></div><span class="status-badge {{ $product->available_quantity <= 0 ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-800' }}">متاح {{ $product->available_quantity }}</span></a>@empty<p class="text-sm text-muted-600">لا توجد تنبيهات مخزون.</p>@endforelse</div></section>
    </div>
</div>
@endsection
