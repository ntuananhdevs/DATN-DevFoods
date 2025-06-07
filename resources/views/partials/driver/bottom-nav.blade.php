<nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-gray-200/50 z-50 shadow-2xl">
    <div class="flex justify-around items-center h-20 px-2">
        <!-- Dashboard -->
        <a href="{{ route('driver.dashboard') }}" class="flex flex-col items-center justify-center flex-1 py-2 px-1 rounded-xl transition-all duration-200 {{ request()->routeIs('driver.dashboard') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50/50' }}">
            <div class="{{ request()->routeIs('driver.dashboard') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600' }} p-2 rounded-xl mb-1 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                </svg>
            </div>
            <span class="text-xs font-semibold">Tổng quan</span>
        </a>

        <!-- Orders -->
        <a href="{{ route('driver.orders') }}" class="flex flex-col items-center justify-center flex-1 py-2 px-1 rounded-xl transition-all duration-200 relative {{ request()->routeIs('driver.orders*') ? 'text-orange-600 bg-orange-50' : 'text-gray-600 hover:text-orange-600 hover:bg-orange-50/50' }}">
            <div class="{{ request()->routeIs('driver.orders*') ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-600' }} p-2 rounded-xl mb-1 transition-all duration-200 relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                @if(isset($pendingOrdersCount) && $pendingOrdersCount > 0)
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center shadow-lg animate-pulse">{{ $pendingOrdersCount > 9 ? '9+' : $pendingOrdersCount }}</span>
                @endif
            </div>
            <span class="text-xs font-semibold">Đơn hàng</span>
        </a>

        <!-- History -->
        <a href="{{ route('driver.history') }}" class="flex flex-col items-center justify-center flex-1 py-2 px-1 rounded-xl transition-all duration-200 {{ request()->routeIs('driver.history') ? 'text-green-600 bg-green-50' : 'text-gray-600 hover:text-green-600 hover:bg-green-50/50' }}">
            <div class="{{ request()->routeIs('driver.history') ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-600' }} p-2 rounded-xl mb-1 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <span class="text-xs font-semibold">Lịch sử</span>
        </a>

        <!-- Notifications -->
        <a href="{{ route('driver.notifications') }}" class="flex flex-col items-center justify-center flex-1 py-2 px-1 rounded-xl transition-all duration-200 relative {{ request()->routeIs('driver.notifications') ? 'text-purple-600 bg-purple-50' : 'text-gray-600 hover:text-purple-600 hover:bg-purple-50/50' }}">
            <div class="{{ request()->routeIs('driver.notifications') ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-600' }} p-2 rounded-xl mb-1 transition-all duration-200 relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h10a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                    <span class="absolute -top-2 -right-2 bg-yellow-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center shadow-lg animate-bounce" data-notification-count>{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
                @endif
            </div>
            <span class="text-xs font-semibold">Thông báo</span>
        </a>

        <!-- Profile -->
        <a href="{{ route('driver.profile') }}" class="flex flex-col items-center justify-center flex-1 py-2 px-1 rounded-xl transition-all duration-200 {{ request()->routeIs('driver.profile') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50/50' }}">
            <div class="{{ request()->routeIs('driver.profile') ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600' }} p-2 rounded-xl mb-1 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <span class="text-xs font-semibold">Cá nhân</span>
        </a>
    </div>
</nav>
