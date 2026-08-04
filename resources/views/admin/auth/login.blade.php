@extends('layouts.admin-guest')

@section('title', 'تسجيل دخول الإدارة')

@section('content')
    <section>
        <h1>تسجيل دخول الإدارة</h1>

        @if ($errors->any())
            <div>
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <div>
                <label for="email">البريد الإلكتروني</label>

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                >
            </div>

            <div>
                <label for="password">كلمة المرور</label>

                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                >
            </div>

            <button type="submit">
                تسجيل الدخول
            </button>
        </form>
    </section>
@endsection