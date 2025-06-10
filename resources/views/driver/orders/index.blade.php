@extends('layouts.driver.masterLayout')

@section('title', 'Đơn hàng')
@section('page-title', 'Đơn hàng')

@section('content')
<div class="pt-16 p-4">
    <!-- Search -->
    <div class="mb-4">
        <div class="relative">
            <input type="text" placeholder="Tìm kiếm đơn hàng..." class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex space-x-2 mb-4 overflow-x-auto">
        <button class="px-4 py-2 bg-blue-600 text-white rounded-full text-sm whitespace-nowrap">Tất cả</button>
        <button class="px-4 py-2 bg-gray-100 text-gray-600 rounded-full text-sm whitespace-nowrap">Chờ lấy</button>
        <button class="px-4 py-2 bg-gray-100 text-gray-600 rounded-full text-sm whitespace-nowrap">Đã lấy</button>
        <button class="px-4 py-2 bg-gray-100 text-gray-600 rounded-full text-sm whitespace-nowrap">Đã giao</button>
    </div>

    <!-- Orders List -->
    <div class="space-y-4">
        <!-- Order 1 -->
        <a href="{{ route('driver.orders.show', ['orderId' => 1]) }}" class="block bg-white rounded-lg p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-2">
                    <span class="bg-blue-600 text-white px-2 py-1 rounded text-xs">#1</span>
                    <span class="text-sm text-gray-500">Đã nhận đơn</span>
                </div>
                <div class="text-right">
                    <div class="font-bold text-green-600">202.000 đ</div>
                    <div class="text-xs text-gray-500">Phí ship: 25.000 đ</div>
                </div>
            </div>
            <div class="space-y-1">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-user text-gray-400 text-sm"></i>
                    <span class="text-sm">Nguyễn Văn A</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-phone text-gray-400 text-sm"></i>
                    <span class="text-sm">0987654321</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-map-marker-alt text-gray-400 text-sm"></i>
                    <span class="text-sm">123 Đường Láng, Đống Đa, Hà Nội</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-clock text-gray-400 text-sm"></i>
                    <span class="text-sm">Giao lúc: 12:30</span>
                </div>
            </div>
        </a>

        <!-- Order 2 -->
        <a href="{{ route('driver.orders.show', 2) }}" class="block bg-white rounded-lg p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-2">
                    <span class="bg-orange-600 text-white px-2 py-1 rounded text-xs">#2</span>
                    <span class="text-sm text-gray-500">Đã lấy hàng</span>
                </div>
                <div class="text-right">
                    <div class="font-bold text-green-600">141.500 đ</div>
                    <div class="text-xs text-gray-500">Phí ship: 20.000 đ</div>
                </div>
            </div>
            <div class="space-y-1">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-user text-gray-400 text-sm"></i>
                    <span class="text-sm">Trần Thị B</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-phone text-gray-400 text-sm"></i>
                    <span class="text-sm">0912345678</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-map-marker-alt text-gray-400 text-sm"></i>
                    <span class="text-sm">456 Phố Huế, Hai Bà Trưng, Hà Nội</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-clock text-gray-400 text-sm"></i>
                    <span class="text-sm">Giao lúc: 13:00</span>
                </div>
            </div>
        </a>

        <!-- Order 3 -->
        <a href="{{ route('driver.orders.show', 3) }}" class="block bg-white rounded-lg p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-2">
                    <span class="bg-green-600 text-white px-2 py-1 rounded text-xs">#3</span>
                    <span class="text-sm text-gray-500">Đã giao</span>
                </div>
                <div class="text-right">
                    <div class="font-bold text-green-600">273.000 đ</div>
                    <div class="text-xs text-gray-500">Phí ship: 30.000 đ</div>
                </div>
            </div>
            <div class="space-y-1">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-user text-gray-400 text-sm"></i>
                    <span class="text-sm">Lê Văn C</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-phone text-gray-400 text-sm"></i>
                    <span class="text-sm">0901234567</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-map-marker-alt text-gray-400 text-sm"></i>
                    <span class="text-sm">789 Đường Giải Phóng, Hoàng Mai, Hà Nội</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-clock text-gray-400 text-sm"></i>
                    <span class="text-sm">Giao lúc: 14:30</span>
                </div>
            </div>
        </a>

        <!-- Order 4 -->
        <a href="{{ route('driver.orders.show', 4) }}" class="block bg-white rounded-lg p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-2">
                    <span class="bg-purple-600 text-white px-2 py-1 rounded text-xs">#4</span>
                    <span class="text-sm text-gray-500">Đang giao</span>
                </div>
                <div class="text-right">
                    <div class="font-bold text-green-600">183.800 đ</div>
                    <div class="text-xs text-gray-500">Phí ship: 22.000 đ</div>
                </div>
            </div>
            <div class="space-y-1">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-user text-gray-400 text-sm"></i>
                    <span class="text-sm">Phạm Thị D</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-phone text-gray-400 text-sm"></i>
                    <span class="text-sm">0934567890</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-map-marker-alt text-gray-400 text-sm"></i>
                    <span class="text-sm">321 Đường Cầu Giấy, Cầu Giấy, Hà Nội</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-clock text-gray-400 text-sm"></i>
                    <span class="text-sm">Giao lúc: 15:00</span>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
