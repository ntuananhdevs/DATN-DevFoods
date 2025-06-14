<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-4 py-2">
    <div class="flex justify-around">
        <a href="{{ route('driver.dashboard') }}" class="flex flex-col items-center py-2 {{ request()->routeIs('driver.dashboard') ? 'text-blue-600' : 'text-gray-500' }}">
            <i class="fas fa-home text-xl mb-1"></i>
            <span class="text-xs">Trang chủ</span>
        </a>
        
        <a href="{{ route('driver.orders.index') }}" class="flex flex-col items-center py-2 {{ request()->routeIs('driver.orders.*') ? 'text-blue-600' : 'text-gray-500' }}">
            <i class="fas fa-list text-xl mb-1"></i>
            <span class="text-xs">Đơn hàng</span>
        </a>
        
        <a href="{{ route('driver.earnings') }}" class="flex flex-col items-center py-2 {{ request()->routeIs('driver.earnings') ? 'text-blue-600' : 'text-gray-500' }}">
            <i class="fas fa-chart-line text-xl mb-1"></i>
            <span class="text-xs">Thu nhập</span>
        </a>
        
        <a href="{{ route('driver.profile') }}" class="flex flex-col items-center py-2 {{ request()->routeIs('driver.profile') ? 'text-blue-600' : 'text-gray-500' }}">
            <i class="fas fa-user text-xl mb-1"></i>
            <span class="text-xs">Hồ sơ</span>
        </a>
    </div>
</nav>
