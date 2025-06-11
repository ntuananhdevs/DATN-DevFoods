@extends('layouts.driver.masterLayout')

@section('title', 'Dashboard')

@section('content')
<div class="pt-4 p-4 space-y-4">
    <!-- User Info -->
    <div class="bg-white rounded-lg p-4 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-gray-600"></i>
                </div>
                <div>
                    <h2 class="font-semibold">Xin chào, Nguyễn Văn A</h2>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">Offline</span>
                        {{-- <span class="text-xs text-gray-400">ID: TH2345</span> --}}
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-4">
            <button class="relative">
                <i class="fas fa-bell text-xl"></i>
                <span class="absolute -top-1 -right-1 bg-red-500 text-xs rounded-full w-4 h-4 flex items-center justify-center">3</span>
            </button>
            <button>
                <i class="fas fa-cog text-xl"></i>
            </button>
        </div>
        </div>
    </div>

    <!-- Status Toggle -->
    <div class="bg-white rounded-lg p-4 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-medium">Trạng thái làm việc</h3>
                <p class="text-sm text-gray-500">Bạn đang offline</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" class="sr-only peer" id="statusToggle">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
            </label>
        </div>
    </div>

    <!-- Earnings -->
    <div class="bg-white rounded-lg p-4 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <h3 class="font-medium">Thu nhập</h3>
            <a href="{{ route('driver.earnings') }}" class="text-blue-600 text-sm">Chi tiết</a>
        </div>
        <div class="flex justify-between space-x-4 mb-4">
            <button class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-sm font-medium">Hôm nay</button>
            <button class="px-3 py-1 text-gray-500 text-sm">Tuần này</button>
            <button class="px-3 py-1 text-gray-500 text-sm">Tháng này</button>
        </div>
        <div class="text-center">
            <div class="text-3xl font-bold text-green-600">250.000 đ</div>
            <div class="text-sm text-gray-500">5 đơn hàng</div>
        </div>
    </div>

    <!-- Orders in Progress -->
    <div class="bg-white rounded-lg p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-medium">Đơn hàng đang xử lý</h3>
            <a href="{{ route('driver.orders.index') }}" class="text-blue-600 text-sm">Xem tất cả</a>
        </div>
        
        <div class="space-y-3">
            <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg">
                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-box text-white text-sm"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium">Đơn #1</div>
                    <div class="text-sm text-gray-500">123 Đường Láng, Đống Đa</div>
                </div>
                <span class="bg-blue-600 text-white px-2 py-1 rounded text-xs">Chờ lấy hàng</span>
            </div>
            
            <div class="flex items-center space-x-3 p-3 bg-orange-50 rounded-lg">
                <div class="w-8 h-8 bg-orange-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-box text-white text-sm"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium">Đơn #2</div>
                    <div class="text-sm text-gray-500">456 Phố Huế, Hai Bà Trưng</div>
                </div>
                <span class="bg-orange-600 text-white px-2 py-1 rounded text-xs">Đã lấy hàng</span>
            </div>
        </div>
    </div>

    <!-- Quick Access -->
    {{-- <div class="bg-white rounded-lg p-4 shadow-sm">
        <h3 class="font-medium mb-3">Truy cập nhanh</h3>
        <div class="grid grid-cols-4 gap-4">
            <a href="{{ route('driver.orders.index') }}" class="flex flex-col items-center p-3 rounded-lg bg-gray-50">
                <i class="fas fa-list text-2xl text-gray-600 mb-2"></i>
                <span class="text-xs text-center">Đơn hàng</span>
            </a>
            <a href="{{ route('driver.earnings') }}" class="flex flex-col items-center p-3 rounded-lg bg-gray-50">
                <i class="fas fa-chart-line text-2xl text-gray-600 mb-2"></i>
                <span class="text-xs text-center">Thu nhập</span>
            </a>
            <a href="#" class="flex flex-col items-center p-3 rounded-lg bg-gray-50">
                <i class="fas fa-history text-2xl text-gray-600 mb-2"></i>
                <span class="text-xs text-center">Lịch làm</span>
            </a>
            <a href="{{ route('driver.profile') }}" class="flex flex-col items-center p-3 rounded-lg bg-gray-50">
                <i class="fas fa-user text-2xl text-gray-600 mb-2"></i>
                <span class="text-xs text-center">Hồ sơ</span>
            </a>
        </div>
    </div> --}}

    <!-- Today's Schedule -->
    {{-- <div class="bg-white rounded-lg p-4 shadow-sm">
        <h3 class="font-medium mb-3">Lịch làm việc hôm nay</h3>
        <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg">
            <i class="fas fa-clock text-green-600"></i>
            <div>
                <div class="font-medium">Ca chiều</div>
                <div class="text-sm text-gray-500">14:00 - 22:00</div>
            </div>
            <span class="bg-green-600 text-white px-2 py-1 rounded text-xs ml-auto">Đang làm việc</span>
        </div>
    </div> --}}

    <!-- Logout Button -->
    {{-- <div class="bg-white rounded-lg p-4 shadow-sm">
        <button class="w-full text-red-600 font-medium py-2">
            <i class="fas fa-sign-out-alt mr-2"></i>
            Đăng xuất
        </button>
    </div> --}}
</div>

<script>
document.getElementById('statusToggle').addEventListener('change', function() {
    const isOnline = this.checked;
    // Update status on server
    fetch('/api/update-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ online: isOnline })
    });
});
</script>
@endsection
