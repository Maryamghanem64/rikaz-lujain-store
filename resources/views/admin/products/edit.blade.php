@extends('layouts.admin')
@section('title', 'تعديل المنتج')
@section('content')
<div class="mx-auto max-w-5xl space-y-7">
    <div><a href="{{ route('admin.products.index') }}" class="text-sm text-muted-600">← العودة إلى المنتجات</a><h1 class="mt-2 text-2xl font-semibold">تعديل {{ $product->name_ar }}</h1><p class="mt-1 text-sm text-muted-600">حدّث معلومات القطعة وصورها ومخزونها.</p></div>
    @if (session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="alert-error">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
    <form method="POST" action="{{ route('admin.products.update', $product) }}" class="space-y-6">@csrf @method('PUT') @include('admin.products._form')<div class="flex justify-end"><button type="submit" class="btn-primary min-w-40">حفظ التعديلات</button></div></form>

    <section class="admin-card">
        <div class="mb-6"><h2 class="text-lg font-semibold">صور المنتج</h2><p class="mt-1 text-sm text-muted-600">اختر صورة رئيسية ورتّب بقية الصور. الحد الأقصى للصورة 5MB.</p></div>
        <form method="POST" action="{{ route('admin.products.images.store', $product) }}" enctype="multipart/form-data" class="rounded-xl border border-dashed border-stone-300 bg-ivory-50 p-5">@csrf<label for="images" class="field-label">إضافة صور جديدة</label><input type="file" id="images" name="images[]" accept=".jpg,.jpeg,.png,.webp" multiple required class="block w-full text-sm file:ml-4 file:rounded-lg file:border-0 file:bg-ink-900 file:px-4 file:py-2.5 file:text-white"><button type="submit" class="btn-secondary mt-4">رفع الصور</button></form>

        @if ($product->images->isEmpty())<div class="mt-6 rounded-xl bg-ivory-200 p-8 text-center text-sm text-muted-600">لا توجد صور للمنتج حتى الآن.</div>@else
        <form method="POST" action="{{ route('admin.products.images.order', $product) }}" class="mt-6">@csrf @method('PATCH')<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">@foreach ($product->images as $image)<article class="overflow-hidden rounded-2xl border border-line-200"><div class="relative aspect-square bg-ivory-200"><img src="{{ $image->url }}" alt="{{ $product->name_ar }}" class="h-full w-full object-cover">@if ($image->is_primary)<span class="status-badge absolute right-3 top-3 bg-ink-900 text-white">الصورة الرئيسية</span>@endif</div><div class="p-4"><label for="order-{{ $image->id }}" class="field-label">الترتيب</label><input id="order-{{ $image->id }}" type="number" name="orders[{{ $image->id }}]" value="{{ $image->sort_order }}" min="0" class="field-control"></div></article>@endforeach</div><button type="submit" class="btn-secondary mt-4">حفظ ترتيب الصور</button></form>
        <div class="mt-6 grid gap-3 sm:grid-cols-2">@foreach ($product->images as $image)<div class="flex items-center justify-between gap-3 rounded-xl border p-3"><span class="truncate text-sm">صورة #{{ $image->id }}</span><div class="flex items-center gap-2">@unless ($image->is_primary)<form method="POST" action="{{ route('admin.products.images.primary', [$product, $image]) }}">@csrf @method('PATCH')<button type="submit" class="text-xs font-medium text-rikaz-700">تعيين كرئيسية</button></form>@endunless<form method="POST" action="{{ route('admin.products.images.destroy', [$product, $image]) }}" onsubmit="return confirm('هل أنت متأكد من حذف الصورة؟');">@csrf @method('DELETE')<button type="submit" class="text-xs font-medium text-red-700">حذف</button></form></div></div>@endforeach</div>
        @endif
    </section>
</div>
@endsection
