<nav class="md:hidden fixed bottom-0 left-0 right-0 bottom-nav z-50">
    <div class="flex h-16">
        <a href="{{ route('driver.dashboard') }}" class="flex-1 flex flex-col items-center justify-center text-xs {{ request()->routeIs('driver.dashboard') ? 'text-blue-600 font-semibold' : 'text-gray-500' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
            </svg>
            <span>Tổng quan</span>
        </a>

        <a href="{{ route('driver.orders') }}" class="flex-1 flex flex-col items-center justify-center text-xs relative {{ request()->routeIs('driver.orders*') ? 'text-blue-600 font-semibold' : 'text-gray-500' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <span>Đơn hàng</span>
            @if(isset($pendingOrdersCount) && $pendingOrdersCount > 0)
                <span class="absolute -top-1 left-1/2 transform -translate-x-1/2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                    {{ $pendingOrdersCount }}
                </span>
            @endif
        </a>

        <a href="{{ route('driver.history') }}" class="flex-1 flex flex-col items-center justify-center text-xs {{ request()->routeIs('driver.history') ? 'text-blue-600 font-semibold' : 'text-gray-500' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Lịch sử</span>
        </a>

        <a href="{{ route('driver.notifications') }}" class="flex-1 flex flex-col items-center justify-center text-xs relative {{ request()->routeIs('driver.notifications') ? 'text-blue-600 font-semibold' : 'text-gray-500' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h10a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            <span>Thông báo</span>
            @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                <span class="absolute -top-1 left-1/2 transform -translate-x-1/2 bg-yellow-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center" data-notification-count>
                    {{ $unreadNotificationsCount }}
                </span>
            @endif
        </a>

        <a href="{{ route('driver.profile') }}" class="flex-1 flex flex-col items-center justify-center text-xs {{ request()->routeIs('driver.profile') ? 'text-blue-600 font-semibold' : 'text-gray-500' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span>Cá nhân</span>
        </a>
    </div>
</nav>
