@extends('layouts.driver.masterLayout')

@section('title', 'Tổng quan - Ứng dụng Tài xế')

@section('content')
<div class="p-4 md:p-6 space-y-6">
    <!-- Driver Profile Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-4">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold">
                    {{ strtoupper(substr($driverData['name'], 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $driverData['name'] }}</h2>
                    <p class="text-gray-600">{{ $driverData['vehicle_type'] ?? 'Xe máy' }} - {{ $driverData['license_plate'] ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-500">{{ $driverData['phone_number'] ?? '' }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $driverData['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $driverData['is_active'] ? 'Đang hoạt động' : 'Nghỉ' }}
                </span>
                <div class="mt-2">
                    <span class="text-xs text-gray-500">Đánh giá: </span>
                    <span class="text-sm font-semibold text-yellow-600">{{ number_format($driverData['rating'], 1) }}/5.0 ⭐</span>
                </div>
            </div>
        </div>
        
        <div class="border-t pt-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div>
                    <div class="text-2xl font-bold text-blue-600">{{ $todayStats['orders_count'] }}</div>
                    <div class="text-sm text-gray-500">Đơn hôm nay</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-green-600">{{ number_format($todayStats['earnings']) }}đ</div>
                    <div class="text-sm text-gray-500">Thu nhập hôm nay</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-purple-600">{{ number_format($todayStats['distance'], 1) }}km</div>
                    <div class="text-sm text-gray-500">Quãng đường</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-orange-600">{{ $todayStats['completion_rate'] }}%</div>
                    <div class="text-sm text-gray-500">Tỷ lệ hoàn thành</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Orders Status -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100">Đơn chờ nhận</p>
                    <p class="text-2xl font-bold">{{ $orderCounts['pending'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-400 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100">Đang giao</p>
                    <p class="text-2xl font-bold">{{ $orderCounts['delivering'] ?? 0 }}</p>
                </div>
                <div class="bg-yellow-400 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100">Hoàn thành hôm nay</p>
                    <p class="text-2xl font-bold">{{ $orderCounts['completed_today'] ?? 0 }}</p>
                </div>
                <div class="bg-green-400 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Orders -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Đơn hàng mới chờ nhận</h3>
                <a href="{{ route('driver.orders', ['status' => 'pending']) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Xem tất cả
                </a>
            </div>
        </div>
        <div class="p-6">
            @if(isset($pendingOrders) && count($pendingOrders) > 0)
                <div class="space-y-4">
                    @foreach($pendingOrders as $order)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-semibold text-gray-900">#{{ $order['id'] }}</h4>
                                    <p class="text-sm text-gray-500 flex items-center mt-1">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $order['order_time'] }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                        {{ $order['status_text'] ?? 'Chờ nhận' }}
                                    </span>
                                    <p class="text-sm font-bold text-green-600 mt-1">{{ number_format($order['shipping_fee']) }}đ</p>
                                </div>
                            </div>
                            
                            <div class="space-y-2 text-sm">
                                <div class="flex items-start">
                                    <svg class="w-4 h-4 mr-2 mt-0.5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <div>
                                        <span class="font-medium">Lấy hàng:</span> {{ $order['pickup_branch'] }}
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-4 h-4 mr-2 mt-0.5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <div>
                                        <span class="font-medium">Giao đến:</span> {{ $order['delivery_address'] }}
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $order['customer_name'] }} - {{ $order['customer_phone'] }}
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center mt-4 pt-3 border-t border-gray-200">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                    {{ number_format($order['distance'], 1) }} km
                                </div>
                                <a href="{{ route('driver.orders.detail', $order['id']) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Không có đơn hàng mới</h3>
                    <p class="text-gray-500">Hiện tại không có đơn hàng nào cần nhận. Hãy kiểm tra lại sau!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Thao tác nhanh</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('driver.orders', ['status' => 'pending']) }}" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mb-2">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-900">Đơn chờ nhận</span>
                <span class="text-xs text-gray-500">({{ $orderCounts['pending'] ?? 0 }} đơn)</span>
            </a>

            <a href="{{ route('driver.orders', ['status' => 'delivering']) }}" class="flex flex-col items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mb-2">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-900">Đang giao</span>
                <span class="text-xs text-gray-500">({{ $orderCounts['delivering'] ?? 0 }} đơn)</span>
            </a>

            <a href="{{ route('driver.history') }}" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mb-2">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-900">Lịch sử</span>
                <span class="text-xs text-gray-500">Xem tất cả</span>
            </a>

            <a href="{{ route('driver.earnings') }}" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center mb-2">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-900">Thu nhập</span>
                <span class="text-xs text-gray-500">{{ number_format($todayStats['earnings']) }}đ</span>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto refresh page every 30 seconds to get new orders
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            window.location.reload();
        }
    }, 30000);
    
    // Show notification for new orders
    @if(isset($orderCounts['pending']) && $orderCounts['pending'] > 0)
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('Đơn hàng mới!', {
                body: 'Bạn có {{ $orderCounts["pending"] }} đơn hàng mới cần nhận.',
                icon: '/favicon.ico'
            });
        }
    @endif
</script>
@endpush
@endsection

