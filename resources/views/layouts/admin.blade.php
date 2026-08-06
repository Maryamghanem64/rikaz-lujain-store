<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'لوحة الإدارة')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <header>
        <h1>إدارة متجر ركاز × لجين</h1>
<nav>
    <a href="{{ route('admin.dashboard') }}">
        لوحة التحكم
    </a>

    <a href="{{ route('admin.categories.index') }}">
        الفئات
    </a>

    <a href="{{ route('admin.products.index') }}">
        المنتجات
    </a>
</nav> <div>
            <span>{{ auth()->user()->name }}</span>

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf

                <button type="submit">
                    تسجيل الخروج
                </button>
            </form>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>