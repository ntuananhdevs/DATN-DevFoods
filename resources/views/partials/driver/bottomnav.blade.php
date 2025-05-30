<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-30 safe-area-pb">
    <div class="max-w-lg mx-auto px-2 py-1">
        <div class="grid grid-cols-4 gap-1">
            <!-- Dashboard -->
            <a href="dashboard.html" class="nav-item" data-page="dashboard">
                <div class="flex flex-col items-center py-2 px-1 rounded-lg transition-all duration-200">
                    <i class="fas fa-home text-lg mb-1"></i>
                    <span class="text-xs font-medium">Trang chủ</span>
                </div>
            </a>

            <!-- Orders -->
            <a href="orders.html" class="nav-item" data-page="orders">
                <div class="flex flex-col items-center py-2 px-1 rounded-lg transition-all duration-200 relative">
                    <i class="fas fa-list text-lg mb-1"></i>
                    <span class="text-xs font-medium">Đơn hàng</span>
                    <span x-show="pendingOrdersCount > 0"
                          class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center"
                          x-text="pendingOrdersCount"></span>
                </div>
            </a>

            <!-- Earnings -->
            <a href="earnings.html" class="nav-item" data-page="earnings">
                <div class="flex flex-col items-center py-2 px-1 rounded-lg transition-all duration-200">
                    <i class="fas fa-wallet text-lg mb-1"></i>
                    <span class="text-xs font-medium">Thu nhập</span>
                </div>
            </a>

            <!-- Profile -->
            <a href="profile.html" class="nav-item" data-page="profile">
                <div class="flex flex-col items-center py-2 px-1 rounded-lg transition-all duration-200">
                    <i class="fas fa-user text-lg mb-1"></i>
                    <span class="text-xs font-medium">Hồ sơ</span>
                </div>
            </a>
        </div>
    </div>
</nav>
