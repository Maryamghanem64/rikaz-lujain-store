@extends('layouts.admin-guest')
@section('title', 'تسجيل دخول الإدارة')
@section('content')
<section class="surface-card overflow-hidden">
    <div class="border-b border-line-200 bg-[#f4ecee] px-6 py-8 text-center text-rikaz-800">
        <div class="mx-auto grid size-12 place-items-center rounded-full border border-rikaz-700/25 bg-white text-sm font-semibold">ر × ل</div>
        <h1 class="mt-4 text-2xl font-semibold">تسجيل دخول الإدارة</h1>
        <p class="mt-2 text-sm text-muted-600">إدارة متجر ركاز × لجين</p>
    </div>
    <div class="p-6 sm:p-8">
        @if ($errors->any())<div class="alert-error mb-5">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
        <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
            @csrf
            <div><label for="email" class="field-label">البريد الإلكتروني</label><input id="email" type="email" name="email" value="{{ old('email') }}" class="field-control" autocomplete="email" required autofocus></div>
            <div><label for="password" class="field-label">كلمة المرور</label><input id="password" type="password" name="password" class="field-control" autocomplete="current-password" required></div>
            <button type="submit" class="btn-rikaz w-full">تسجيل الدخول</button>
        </form>
    </div>
</section>
@endsection
