<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ứng dụng Tài xế Giao hàng - @yield('title', 'Dashboard')</title>
    <!-- Mapbox GL JS CSS -->
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet" />
    <!-- Compiled Tailwind CSS (assuming you're using Laravel Mix) -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('head_scripts')
</head>
<body class="min-h-screen bg-muted/40 flex flex-col">
    <div id="app-root" class="flex-1 pb-16 md:pb-0">
        @yield('content')
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-[100] space-y-2"></div>

    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white bg-background border-t h-16 flex md:hidden z-50 shadow-lg">
        <a href="{{ route('driver.dashboard') }}" class="nav-item flex-1 flex flex-col items-center justify-center text-xs gap-1 text-gray-500 hover:text-blue-600 {{ request()->routeIs('driver.dashboard') ? 'text-primary font-semibold' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>Tổng quan</span>
        </a>
        <a href="{{ route('driver.orders.index') }}" class="nav-item flex-1 flex flex-col items-center justify-center text-xs gap-1 text-gray-500 hover:text-blue-600 {{ request()->routeIs('driver.orders.*') ? 'text-primary font-semibold' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M7 21h10"/><path d="M7 7v14"/><path d="M17 7v14"/><path d="M17 3H7v4h10V3z"/></svg>
            <span>Đơn hàng</span>
        </a>
        <a href="{{ route('driver.history') }}" class="nav-item flex-1 flex flex-col items-center justify-center text-xs gap-1 text-gray-500 hover:text-blue-600 {{ request()->routeIs('driver.history') ? 'text-primary font-semibold' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M3 3v5h5"/><path d="M3.05 13A9 9 0 1 0 6 18.34"/><path d="M12 12v6"/><path d="M12 12h4"/></svg>
            <span>Lịch sử</span>
        </a>
        <a href="{{ route('driver.notifications') }}" class="nav-item flex-1 flex flex-col items-center justify-center text-xs gap-1 text-gray-500 hover:text-blue-600 {{ request()->routeIs('driver.notifications') ? 'text-primary font-semibold' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
            <span>Thông báo</span>
        </a>
        <a href="{{ route('driver.profile') }}" class="nav-item flex-1 flex flex-col items-center justify-center text-xs gap-1 text-gray-500 hover:text-blue-600 {{ request()->routeIs('driver.profile') ? 'text-primary font-semibold' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>Cá nhân</span>
        </a>
    </nav>

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Mapbox GL JS Script -->
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
    <!-- Main JavaScript Logic -->
    <script src="{{ asset('js/script.js') }}"></script>
    @yield('page_scripts')
</body>
</html>