<header class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 shadow-lg sticky top-0 z-50 backdrop-blur-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('driver.dashboard') }}" class="flex items-center group">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/30 group-hover:bg-white/30 transition-all">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <span class="text-xl font-bold text-white">DevFoods</span>
                        <div class="text-xs text-blue-100">Driver App</div>
                    </div>
                </a>
            </div>

            <!-- Center - Status Indicator -->
            <div class="hidden md:flex items-center space-x-4">
                <div class="flex items-center bg-white/10 backdrop-blur-sm rounded-lg px-3 py-1.5 border border-white/20">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse mr-2"></div>
                    <span class="text-sm text-white font-medium">Đang hoạt động</span>
                </div>
            </div>

            <!-- Right side -->
            <div class="flex items-center space-x-3">
                <!-- Notifications -->
                <a href="{{ route('driver.notifications') }}" class="relative p-2.5 text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200 group">
                    <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-2H4v2zM4 15h8v-2H4v2zM4 11h8V9H4v2z"></path>
                    </svg>
                    @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold shadow-lg animate-bounce" data-notification-count>
                            {{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}
                        </span>
                    @endif
                </a>

                <!-- User dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-3 p-2 rounded-xl hover:bg-white/10 transition-all duration-200 group">
                        <div class="w-9 h-9 bg-gradient-to-br from-white/20 to-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center text-white text-sm font-bold border border-white/30 group-hover:scale-105 transition-transform">
                            {{ strtoupper(substr($driver['name'] ?? 'D', 0, 1)) }}
                        </div>
                        <div class="hidden lg:block text-left">
                            <div class="text-sm font-semibold text-white">{{ $driver['name'] ?? 'Driver' }}</div>
                            <div class="text-xs text-blue-100">Tài xế giao hàng</div>
                        </div>
                        <svg class="w-4 h-4 text-white/70 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Dropdown menu -->
                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-xl border border-gray-200 py-2 z-50 backdrop-blur-sm">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white text-sm font-bold">
                                    {{ strtoupper(substr($driver['name'] ?? 'D', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $driver['name'] ?? 'Driver' }}</div>
                                    <div class="text-xs text-gray-500">{{ $driver['phone_number'] ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <a href="{{ route('driver.profile') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors group">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-200 transition-colors">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium">Hồ sơ cá nhân</div>
                                <div class="text-xs text-gray-500">Thông tin & cài đặt</div>
                            </div>
                        </a>
                        
                        <a href="{{ route('driver.history') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors group">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-200 transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium">Lịch sử giao hàng</div>
                                <div class="text-xs text-gray-500">Xem các đơn đã giao</div>
                            </div>
                        </a>
                        
                        <a href="{{ route('driver.earnings') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors group">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-purple-200 transition-colors">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium">Thu nhập</div>
                                <div class="text-xs text-gray-500">Thống kê & báo cáo</div>
                            </div>
                        </a>
                        
                        <div class="border-t border-gray-100 my-2"></div>
                        
                        <form method="POST" action="{{ route('driver.logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors group">
                                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-red-200 transition-colors">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium">Đăng xuất</div>
                                    <div class="text-xs text-red-500">Thoát khỏi ứng dụng</div>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
