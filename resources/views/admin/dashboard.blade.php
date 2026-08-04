<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>لوحة الإدارة</title>
</head>

<body>
    <main>
        <h1>لوحة الإدارة</h1>

        <p>
            مرحبًا {{ auth()->user()->name }}
        </p>

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf

            <button type="submit">
                تسجيل الخروج
            </button>
        </form>
    </main>
</body>
</html>