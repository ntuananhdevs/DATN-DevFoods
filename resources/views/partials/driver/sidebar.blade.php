<aside class="hidden lg:flex lg:flex-shrink-0">
    <div class="flex flex-col w-72">
        <div class="flex flex-col h-0 flex-1 bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 shadow-2xl">
            <!-- Logo Section -->
            <div class="flex-shrink-0 px-6 py-6 border-b border-slate-700/50">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-xl font-bold text-white">DevFoods</h1>
                        <p class="text-sm text-slate-400">Driver Dashboard</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="flex-1 flex flex-col pt-6 pb-4 overflow-y-auto">
                <nav class="flex-1 px-4 space-y-2">
                    <!-- Dashboard -->
                    <a href="{{ route('driver.dashboard') }}" class="{{ request()->routeIs('driver.dashboard') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }} group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200">
                        <div class="{{ request()->routeIs('driver.dashboard') ? 'bg-white/20' : 'bg-slate-700 group-hover:bg-slate-600' }} p-2 rounded-lg mr-4 transition-colors">
                            <svg class="{{ request()->routeIs('driver.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-white' }} h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold">Dashboard</div>
                            <div class="text-xs opacity-75">Tổng quan hoạt động</div>
                        </div>
                    </a>

                    <!-- Orders -->
                    <a href="{{ route('driver.orders') }}" class="{{ request()->routeIs('driver.orders*') ? 'bg-gradient-to-r from-orange-600 to-orange-700 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }} group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200">
                        <div class="{{ request()->routeIs('driver.orders*') ? 'bg-white/20' : 'bg-slate-700 group-hover:bg-slate-600' }} p-2 rounded-lg mr-4 transition-colors">
                            <svg class="{{ request()->routeIs('driver.orders*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }} h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold">Đơn hàng</div>
                            <div class="text-xs opacity-75">Quản lý giao hàng</div>
                        </div>
                        @if(isset($pendingOrdersCount) && $pendingOrdersCount > 0)
                            <span class="bg-red-500 text-white text-xs font-bold rounded-full px-2.5 py-1 shadow-lg animate-pulse">{{ $pendingOrdersCount }}</span>
                        @endif
                    </a>

                    <!-- History -->
                    <a href="{{ route('driver.history') }}" class="{{ request()->routeIs('driver.history') ? 'bg-gradient-to-r from-green-600 to-green-700 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }} group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200">
                        <div class="{{ request()->routeIs('driver.history') ? 'bg-white/20' : 'bg-slate-700 group-hover:bg-slate-600' }} p-2 rounded-lg mr-4 transition-colors">
                            <svg class="{{ request()->routeIs('driver.history') ? 'text-white' : 'text-slate-400 group-hover:text-white' }} h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold">Lịch sử</div>
                            <div class="text-xs opacity-75">Đơn đã giao</div>
                        </div>
                    </a>

                    <!-- Notifications -->
                    <a href="{{ route('driver.notifications') }}" class="{{ request()->routeIs('driver.notifications') ? 'bg-gradient-to-r from-purple-600 to-purple-700 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }} group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200">
                        <div class="{{ request()->routeIs('driver.notifications') ? 'bg-white/20' : 'bg-slate-700 group-hover:bg-slate-600' }} p-2 rounded-lg mr-4 transition-colors">
                            <svg class="{{ request()->routeIs('driver.notifications') ? 'text-white' : 'text-slate-400 group-hover:text-white' }} h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-2H4v2zM4 15h8v-2H4v2zM4 11h8V9H4v2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold">Thông báo</div>
                            <div class="text-xs opacity-75">Tin nhắn mới</div>
                        </div>
                        @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                            <span class="bg-red-500 text-white text-xs font-bold rounded-full px-2.5 py-1 shadow-lg animate-bounce" data-notification-count>{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
                        @endif
                    </a>

                    <!-- Profile -->
                    <a href="{{ route('driver.profile') }}" class="{{ request()->routeIs('driver.profile') ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }} group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200">
                        <div class="{{ request()->routeIs('driver.profile') ? 'bg-white/20' : 'bg-slate-700 group-hover:bg-slate-600' }} p-2 rounded-lg mr-4 transition-colors">
                            <svg class="{{ request()->routeIs('driver.profile') ? 'text-white' : 'text-slate-400 group-hover:text-white' }} h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold">Cá nhân</div>
                            <div class="text-xs opacity-75">Thông tin cá nhân</div>
                        </div>
                    </a>
                </nav>
            </div>

            <!-- Status Toggle Section -->
            <div class="flex-shrink-0 border-t border-slate-700/50 p-6">
                <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-xl p-4 border border-slate-600/50">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-sm font-semibold text-white">Trạng thái hoạt động</h3>
                            <p class="text-xs text-slate-400">Bật/tắt nhận đơn hàng</p>
                        </div>
                        <div class="flex items-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" {{ ($driver['is_active'] ?? true) ? 'checked' : '' }} data-toggle="driver-status" id="driver-status-toggle">
                                <div class="relative w-11 h-6 bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                            </label>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-xs text-green-400 font-medium">{{ ($driver['is_active'] ?? true) ? 'Đang sẵn sàng nhận đơn' : 'Đang nghỉ' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>
