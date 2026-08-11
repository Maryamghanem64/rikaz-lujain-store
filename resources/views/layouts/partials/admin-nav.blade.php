@php
    $adminLinks = [
        ['admin.dashboard', 'لوحة التحكم'],
        ['admin.orders.index', 'الطلبات'],
        ['admin.products.index', 'المنتجات'],
        ['admin.categories.index', 'الفئات'],
        ['admin.delivery-zones.index', 'مناطق التوصيل'],
        ['admin.settings.edit', 'الإعدادات'],
    ];
@endphp
<nav class="mt-6 flex flex-1 flex-col gap-1 text-sm">
    @foreach ($adminLinks as [$routeName, $label])
        <a href="{{ route($routeName) }}" class="rounded-lg border-r-2 px-4 py-3 transition {{ request()->routeIs(str_replace('.index', '.*', $routeName)) || request()->routeIs($routeName) ? 'border-rikaz-700 bg-[#f4ecee] font-semibold text-rikaz-800' : 'border-transparent text-muted-600 hover:bg-ivory-200 hover:text-ink-900' }}">{{ $label }}</a>
    @endforeach
    <form method="POST" action="{{ route('admin.logout') }}" class="mt-auto pt-6">@csrf<button type="submit" class="w-full rounded-lg border border-line-200 px-4 py-3 text-right text-muted-600 transition hover:border-rikaz-700/30 hover:bg-[#f4ecee] hover:text-rikaz-800">تسجيل الخروج</button></form>
</nav>
