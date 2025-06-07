<aside class="hidden md:block fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg">
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 bg-blue-600 text-white">
            <svg class="w-8 h-8 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
            </svg>
            <span class="text-lg font-bold">Driver App</span>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="{{ route('driver.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->routeIs('driver.dashboard') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                </svg>
                Tổng quan
            </a>

            <a href="{{ route('driver.orders') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->routeIs('driver.orders*') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Đơn hàng
                @if(isset($pendingOrdersCount) && $pendingOrdersCount > 0)
                    <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1">{{ $pendingOrdersCount }}</span>
                @endif
            </a>

            <a href="{{ route('driver.history') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->routeIs('driver.history') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Lịch sử
            </a>

            <a href="{{ route('driver.notifications') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->routeIs('driver.notifications') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h10a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Thông báo
                @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                    <span class="ml-auto bg-yellow-500 text-white text-xs rounded-full px-2 py-1" data-notification-count>{{ $unreadNotificationsCount }}</span>
                @endif
            </a>

            <a href="{{ route('driver.profile') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->routeIs('driver.profile') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Cá nhân
            </a>
        </nav>

        <!-- Driver Status -->
        <div class="p-4 border-t border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Trạng thái hoạt động</span>
                <label class="toggle-switch">
                    <input type="checkbox" data-toggle="driver-status" {{ ($driver['is_active'] ?? true) ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <p class="text-xs text-gray-500">
                {{ ($driver['is_active'] ?? true) ? 'Đang sẵn sàng nhận đơn' : 'Đang nghỉ' }}
            </p>
        </div>
    </div>
</aside>
