@extends('layouts.admin')
@section('title', 'إدارة الفئات')
@section('content')
<div class="space-y-6">
    <div><p class="eyebrow">كتالوج {{ auth()->user()->section->name_ar }}</p><h1 class="mt-2 text-2xl font-semibold">إدارة الفئات</h1><p class="mt-1 text-sm text-muted-600">تظهر هنا فئات العلامة المرتبطة بحسابك فقط.</p></div>
    @if (session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="alert-error">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif

    <section class="admin-card">
        <div class="mb-5 flex items-center justify-between gap-4"><h2 class="text-lg font-semibold">إضافة فئة جديدة</h2><span class="status-badge bg-[#f4ecee] text-rikaz-800">{{ auth()->user()->section->name_ar }}</span></div>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @csrf
            <div><label for="name_ar" class="field-label">اسم الفئة</label><input type="text" id="name_ar" name="name_ar" value="{{ old('name_ar') }}" required></div>
            <div><label for="slug" class="field-label">Slug</label><input type="text" id="slug" name="slug" value="{{ old('slug') }}" dir="ltr" required></div>
            <div><label for="sort_order" class="field-label">الترتيب</label><input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"></div>
            <div class="flex items-end"><label class="flex min-h-12 items-center gap-3"><input type="checkbox" name="is_active" value="1" checked> فعّالة</label></div>
            <div class="md:col-span-2 xl:col-span-4 xl:text-left"><button type="submit" class="btn-primary">إضافة الفئة</button></div>
        </form>
    </section>

    <section><h2 class="mb-4 text-lg font-semibold">الفئات الحالية</h2><div class="grid gap-4 xl:grid-cols-2">
        @forelse ($categories as $category)
            <article class="admin-card">
                <div class="mb-4 flex items-center justify-between gap-3"><div><p class="text-xs text-muted-600">{{ $category->section->name_ar }}</p><h3 class="font-semibold">{{ $category->name_ar }}</h3></div><span class="status-badge {{ $category->is_active ? 'bg-[#f2f7f3] text-[#356044]' : 'bg-stone-100 text-stone-600' }}">{{ $category->is_active ? 'فعّالة' : 'مخفية' }}</span></div>
                <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="grid gap-4 sm:grid-cols-2">@csrf @method('PUT')
                    <div><label class="field-label">اسم الفئة</label><input type="text" name="name_ar" value="{{ $category->name_ar }}" required></div>
                    <div><label class="field-label">Slug</label><input type="text" name="slug" value="{{ $category->slug }}" dir="ltr" required></div>
                    <div><label class="field-label">الترتيب</label><input type="number" name="sort_order" value="{{ $category->sort_order }}" min="0"></div>
                    <label class="flex items-center gap-3"><input type="checkbox" name="is_active" value="1" @checked($category->is_active)> فعّالة</label>
                    <div class="sm:col-span-2 text-left"><button type="submit" class="btn-secondary">حفظ التعديلات</button></div>
                </form>
                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفئة؟');" class="mt-4 border-t border-line-200 pt-4">@csrf @method('DELETE')<button type="submit" class="text-sm font-medium text-red-700">حذف الفئة</button></form>
            </article>
        @empty
            <div class="admin-card col-span-full text-center text-muted-600">لا توجد فئات لهذه العلامة.</div>
        @endforelse
    </div></section>
</div>
@endsection
