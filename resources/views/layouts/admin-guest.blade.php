<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'دخول الإدارة')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <main>
        @yield('content')
    </main>
</body>
</html>