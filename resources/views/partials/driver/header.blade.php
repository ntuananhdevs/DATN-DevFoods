<header class="bg-white shadow-sm" x-data="{ showNotifications: false }">
    <div class="max-w-lg mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-blue-600 font-semibold" x-text="driverName.charAt(0)"></span>
                </div>
                <div>
                    <h2 class="font-medium" x-text="'Xin chào, ' + driverName"></h2>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 text-xs rounded-full"
                              :class="isOnline ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                              x-text="isOnline ? 'Online' : 'Offline'"></span>
                        <span class="text-xs text-gray-500">ID: TX12345</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button @click="showNotifications = !showNotifications"
                        class="p-2 text-gray-600 hover:text-gray-800 relative">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center"
                          x-show="unreadNotifications > 0" x-text="unreadNotifications"></span>
                </button>
                <button onclick="window.location.href='settings.html'"
                        class="p-2 text-gray-600 hover:text-gray-800">
                    <i class="fas fa-cog text-lg"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Notifications Dropdown -->
    {{-- <div x-show="showNotifications"
         x-transition
         @click.away="showNotifications = false"
         class="absolute top-full left-0 right-0 bg-white shadow-lg border-t z-50">
        <div class="max-w-lg mx-auto p-4">
            <h3 class="font-medium mb-3">Thông báo</h3>
            <div class="space-y-2">
                <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-box text-blue-600 mt-1"></i>
                        <div>
                            <h4 class="font-medium text-sm">Đơn hàng mới</h4>
                            <p class="text-xs text-gray-600">Bạn có đơn hàng mới #12345</p>
                            <p class="text-xs text-gray-500 mt-1">5 phút trước</p>
                        </div>
                    </div>
                </div>
            </div>
            <button onclick="window.location.href='notifications.html'"
                    class="w-full mt-3 text-center text-blue-600 text-sm hover:underline">
                Xem tất cả
            </button>
        </div>
    </div> --}}
</header>
