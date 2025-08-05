@extends('layouts.branch.contentLayoutMaster')

@section('title', 'Chi tiết đơn hàng #' . ($order->order_code ?? $order->id))

@section('content')
    @php
        // Logic xử lý thanh trạng thái - Cải thiện để phù hợp với flow thực tế
        $progressSteps = [
            'awaiting_confirmation' => ['text' => 'Chờ xác nhận', 'icon' => 'fas fa-hourglass-half', 'description' => 'Đơn hàng đang chờ xác nhận từ cửa hàng'],
            'confirmed' => ['text' => 'Đã xác nhận', 'icon' => 'fas fa-check-circle', 'description' => 'Đơn hàng đã được xác nhận và đang chuẩn bị'],
            'awaiting_driver' => ['text' => 'Tìm tài xế', 'icon' => 'fas fa-user-clock', 'description' => 'Đang tìm tài xế giao hàng'],
            'driver_assigned' => ['text' => 'Có tài xế', 'icon' => 'fas fa-user-check', 'description' => 'Đã có tài xế nhận đơn'],
            'in_transit' => ['text' => 'Đang giao', 'icon' => 'fas fa-truck', 'description' => 'Đơn hàng đang được giao đến bạn'],
            'delivered' => ['text' => 'Đã giao', 'icon' => 'fas fa-box-check', 'description' => 'Đơn hàng đã được giao thành công'],
        ];

        // Mapping trạng thái thực tế với các bước theo dõi
        $statusMapToStep = [
            'awaiting_confirmation' => 'awaiting_confirmation',
            'confirmed' => 'confirmed',
            'awaiting_driver' => 'awaiting_driver',
            'driver_confirmed' => 'driver_assigned',
            'waiting_driver_pick_up' => 'driver_assigned',
            'driver_picked_up' => 'driver_assigned',
            'in_transit' => 'in_transit',
            'delivered' => 'delivered',
            'item_received' => 'delivered', // Khách đã nhận hàng cũng hiển thị là đã giao
        ];

        $currentStepKey = $statusMapToStep[$order->status] ?? null;
        $currentStepIndex = $currentStepKey ? array_search($currentStepKey, array_keys($progressSteps)) : -1;

        // Đơn hàng bị hủy hoặc hoàn tiền không hiển thị progress
        if (in_array($order->status, ['cancelled', 'refunded', 'payment_failed', 'order_failed'])) {
            $currentStepIndex = -1;
        }
    @endphp

    <div class="bg-gradient-to-br from-orange-500 via-red-500 to-pink-500 py-12 relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-32 translate-x-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-24 -translate-x-24"></div>
        <div class="container-ft mx-auto px-4 relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <a href="{{ route('customer.orders.index') }}" class="text-white hover:text-gray-200 mr-4">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Chi tiết đơn hàng #{{ $order->order_code ?? $order->id }}
                        </h1>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-white/90">Đặt lúc {{ $order->order_date->format('H:i - d/m/Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-ft mx-auto px-4 py-8">
        <div class="space-y-8">
            <!-- Order Summary Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row justify-between lg:items-start gap-6">
                        <div class="flex-1">
                            <div class="mb-6">
                                <h2 class="text-xl font-bold text-gray-900 mb-2">Thông tin đơn hàng</h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">Mã đơn hàng:</span>
                                        <span class="font-medium ml-2">#{{ $order->order_code ?? $order->id }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Số lượng:</span>
                                        <span class="font-medium ml-2">{{ $order->orderItems->count() }} sản phẩm</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Chi nhánh:</span>
                                        <span class="font-medium ml-2">{{ $order->branch->name ?? 'N/A' }}</span>
                                    </div>
                                    @if (in_array($order->status, ['delivered', 'item_received']))
                                        <div>
                                            <span class="text-gray-500">Hoàn thành:</span>
                                            <span class="font-medium text-green-600 ml-2">
                                                {{ optional($order->actual_delivery_time)->format('H:i - d/m/Y') }}
                                            </span>
                                        </div>
                                    @elseif($order->status != 'cancelled')
                                    
                                    @endif
                                </div>
                            </div>



                            <!-- Thông tin tài xế giao hàng -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <!-- Delivery Address -->
                                    <div class="">
                                        <h3 class="text-lg font-bold text-gray-900 mb-4">Địa chỉ giao hàng</h3>
                                        <div class="text-sm text-gray-700 space-y-3">
                                            <div class="flex flex-wrap items-center gap-6">
                                                <span class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                        </path>
                                                    </svg>
                                                    <span
                                                        class="font-medium text-gray-900">{{ $order->displayRecipientName }}</span>
                                                </span>
                                                <span class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                        </path>
                                                    </svg>
                                                    <span class="text-gray-900">{{ $order->displayDeliveryPhone }}</span>
                                                </span>
                                            </div>
                                            <div class="flex items-start gap-2">
                                                <svg class="w-4 h-4 text-gray-400 mt-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                <span class="text-gray-900">{{ $order->displayFullDeliveryAddress }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    @if (in_array($order->status, [
                                            'confirmed',
                                            'awaiting_driver',
                                            'driver_confirmed',
                                            'waiting_driver_pick_up',
                                            'driver_picked_up',
                                            'in_transit',
                                            'delivered',
                                            'item_received',
                                        ]))
                                        <!-- Thông tin tài xế -->
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <div class="flex items-center justify-between mb-3">
                                                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                        </path>
                                                    </svg>
                                                    Tài xế giao hàng
                                                </h3>
                                                @if ($order->driver_id && $order->driver && $order->driver->rating)
                                                    <div class="flex items-center">
                                                        <span
                                                            class="font-medium text-yellow-600 mr-1 text-sm">{{ number_format($order->driver->rating, 1) }}</span>
                                                        <div class="flex">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                @if ($i <= floor($order->driver->rating))
                                                                    <svg class="w-4 h-4 text-yellow-400 fill-current"
                                                                        viewBox="0 0 20 20">
                                                                        <path
                                                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                                                    </svg>
                                                                @elseif($i == ceil($order->driver->rating) && $order->driver->rating - floor($order->driver->rating) >= 0.5)
                                                                    <svg class="w-4 h-4 text-yellow-400"
                                                                        viewBox="0 0 20 20">
                                                                        <defs>
                                                                            <linearGradient id="half-fill">
                                                                                <stop offset="50%"
                                                                                    stop-color="currentColor" />
                                                                                <stop offset="50%"
                                                                                    stop-color="transparent" />
                                                                            </linearGradient>
                                                                        </defs>
                                                                        <path fill="url(#half-fill)"
                                                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                                                    </svg>
                                                                @else
                                                                    <svg class="w-4 h-4 text-gray-300" viewBox="0 0 20 20">
                                                                        <path fill="currentColor"
                                                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                                                    </svg>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                        <div class="driver-container">
                                            @if (in_array($order->status, ['confirmed', 'awaiting_driver']) && !$order->driver_id)
                                                <!-- Hiệu ứng loading nâng cao khi đang tìm tài xế -->
                                                <div id="driver-search-progress" class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                                                    <!-- Header với icon và tiêu đề -->
                                                    <div class="flex items-center space-x-3 mb-4">
                                                        <div class="relative">
                                                            <div class="animate-spin rounded-full h-8 w-8 border-3 border-blue-200 border-t-blue-600"></div>
                                                            <div class="absolute inset-0 flex items-center justify-center">
                                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="font-semibold text-blue-900">Đang tìm tài xế giao hàng</div>
                                                            <div class="text-sm text-blue-700" id="search-status-text">Khởi tạo quá trình tìm kiếm...</div>
                                                        </div>
                                                    </div>

                                                    <!-- Thông tin chi tiết quá trình tìm kiếm -->
                                                    <div class="space-y-3">
                                                        <!-- Thanh tiến trình -->
                                                        <div class="bg-white rounded-lg p-3 border border-blue-100">
                                                            <div class="flex justify-between items-center mb-2">
                                                                <span class="text-sm font-medium text-gray-700">Tiến trình tìm kiếm</span>
                                                                <span class="text-xs text-gray-500" id="attempt-counter">Lần thử: 1/60</span>
                                                            </div>
                                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                                <div id="search-progress-bar" class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-500" style="width: 2%"></div>
                                                            </div>
                                                        </div>

                                                        <!-- Thông tin bán kính tìm kiếm -->
                                                        <div class="grid grid-cols-2 gap-3">
                                                            <div class="bg-white rounded-lg p-3 border border-blue-100">
                                                                <div class="flex items-center space-x-2">
                                                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    </svg>
                                                                    <span class="text-xs font-medium text-gray-700">Bán kính</span>
                                                                </div>
                                                                <div class="mt-1">
                                                                    <span class="text-lg font-bold text-blue-600" id="search-radius">2</span>
                                                                    <span class="text-sm text-gray-500">km</span>
                                                                </div>
                                                            </div>
                                                            <div class="bg-white rounded-lg p-3 border border-blue-100">
                                                                <div class="flex items-center space-x-2">
                                                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                                    </svg>
                                                                    <span class="text-xs font-medium text-gray-700">Tài xế</span>
                                                                </div>
                                                                <div class="mt-1">
                                                                    <span class="text-lg font-bold text-green-600" id="drivers-found">0</span>
                                                                    <span class="text-sm text-gray-500">người</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Log hoạt động -->
                                                        <div class="bg-white rounded-lg p-3 border border-blue-100">
                                                            <div class="flex items-center space-x-2 mb-2">
                                                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                                </svg>
                                                                <span class="text-xs font-medium text-gray-700">Nhật ký tìm kiếm</span>
                                                            </div>
                                                            <div id="search-log" class="space-y-1 max-h-24 overflow-y-auto text-xs text-gray-600">
                                                                <div class="flex items-center space-x-2">
                                                                    <div class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                                                                    <span>Bắt đầu tìm kiếm tài xế...</span>
                                                                    <span class="text-gray-400 ml-auto">{{ now()->format('H:i:s') }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Nút điều khiển và hiệu ứng dots loading -->
                                                    <div class="mt-4 space-y-3">
                                                        <!-- Nút điều khiển -->
                                                        <div class="flex justify-center space-x-3">
                                                            <button id="stop-search-btn" onclick="stopDriverSearch()" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center space-x-2">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h6v4H9z"></path>
                                                                </svg>
                                                                <span>Dừng tìm kiếm</span>
                                                            </button>
                                                            <button id="restart-search-btn" onclick="restartDriverSearch()" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center space-x-2">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                                </svg>
                                                                <span>Khởi động lại</span>
                                                            </button>
                                                        </div>
                                                        
                                                        <!-- Hiệu ứng dots loading -->
                                                        <div class="flex justify-center space-x-1">
                                                            <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                                                            <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                                                            <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif($order->driver_id && $order->driver)
                                                <!-- Thông tin tài xế đầy đủ -->
                                                <div class="space-y-3 driver-info">
                                                    <div class="flex items-center space-x-3">
                                                        <div
                                                            class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                                            <svg class="w-6 h-6 text-green-600" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                                </path>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <div class="font-semibold text-gray-900 driver-name">
                                                                {{ $order->driver->full_name ?? $order->driver->name }}
                                                            </div>
                                                            <div class="text-sm text-gray-600">Tài xế giao hàng</div>
                                                        </div>
                                                    </div>

                                                    <div class="space-y-2 text-sm">
                                                        @if ($order->driver->phone_number)
                                                            <div class="flex items-center justify-between">
                                                                <span class="text-gray-600">Số điện thoại:</span>
                                                                <a href="tel:{{ $order->driver->phone_number }}"
                                                                    class="font-medium text-blue-600 hover:text-blue-800 flex items-center driver-phone">
                                                                    <svg class="w-4 h-4 mr-1" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                                        </path>
                                                                    </svg>
                                                                    {{ $order->driver->phone_number }}
                                                                </a>
                                                            </div>
                                                        @endif

                                                        @if ($order->driver->license_plate)
                                                            <div class="flex items-center justify-between">
                                                                <span class="text-gray-600">Biển số xe:</span>
                                                                <span
                                                                    class="font-medium text-gray-900 font-mono bg-white px-2 py-1 rounded border driver-vehicle">{{ $order->driver->license_plate }}</span>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Trạng thái giao hàng -->
                                                    <div class="mt-3 p-2 bg-white rounded border-l-4 border-green-500 driver-status-container">
                                                        <div class="text-sm">
                                                            <span class="text-gray-600">Trạng thái:</span>
                                                            <span class="font-medium text-green-600 ml-1 driver-status">
                                                                @if ($order->status == 'confirmed')
                                                                    Đã xác nhận đơn hàng
                                                                @elseif($order->status == 'awaiting_driver')
                                                                    Chờ tài xế nhận đơn
                                                                @elseif($order->status == 'driver_confirmed')
                                                                    Tài xế đã xác nhận
                                                                @elseif($order->status == 'waiting_driver_pick_up')
                                                                    Tài xế đang chờ lấy đơn
                                                                @elseif($order->status == 'driver_picked_up')
                                                                    Tài xế đã nhận đơn
                                                                @elseif($order->status == 'in_transit')
                                                                    Đang giao hàng
                                                                @elseif($order->status == 'delivered')
                                                                    Đã giao hàng
                                                                @elseif($order->status == 'item_received')
                                                                    Khách hàng đã nhận hàng
                                                                @else
                                                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <!-- Trường hợp chưa có tài xế -->
                                                <div class="text-center py-4">
                                                    <div class="text-gray-500 text-sm">
                                                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Chưa có tài xế được phân công
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        </div>
                                    @endif
                                </div>
                            </div>


                        </div>
                        <div class="flex-shrink-0">
                            <div class="text-center">
                                <span id="order-status-display" class="inline-block px-4 py-2 text-sm font-medium rounded-lg"
                                    style="background-color: {{ $order->status_color }}; color: {{ $order->status_text_color }};">
                                    {{ $order->status_text }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">Trạng thái hiện tại</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Action Buttons -->
            @if($order->status == 'awaiting_confirmation')
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Thao tác đơn hàng</h3>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button id="confirm-order-btn" 
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Xác nhận đơn hàng
                        </button>
                        <button id="cancel-order-btn" 
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Hủy đơn hàng
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Driver Search Map -->
            @if(in_array($order->status, ['confirmed', 'awaiting_driver']))
            <div id="driver-search-section" class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Tìm tài xế giao hàng
                        </h3>
                        <div id="search-status" class="flex items-center text-sm">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-2"></div>
                            <span class="text-blue-600 font-medium">Đang quét vị trí tài xế...</span>
                        </div>
                    </div>
                    
                    <!-- Map Container -->
                    <div class="relative">
                        <div id="driver-map" class="w-full h-96 bg-gray-100 rounded-lg border border-gray-300 relative overflow-hidden">
                            <!-- Mapbox Map Container -->
                            <div id="mapbox-container" class="w-full h-full rounded-lg"></div>
                            
                            <!-- Scanning Effect Overlay -->
                            <div id="scanning-effect" class="absolute inset-0 z-10 pointer-events-none">
                                <div class="scanning-wave absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-16 h-16 border-2 border-blue-500 rounded-full opacity-75"></div>
                                <div class="scanning-wave absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-32 h-32 border-2 border-blue-400 rounded-full opacity-50"></div>
                                <div class="scanning-wave absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-48 h-48 border-2 border-blue-300 rounded-full opacity-25"></div>
                            </div>
                        </div>
                        
                        <!-- Map Legend -->
                        <div class="absolute bottom-4 left-4 bg-white rounded-lg shadow-lg p-3 text-xs">
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-red-600 rounded-full mr-2"></div>
                                    <span>Nhà hàng</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-green-600 rounded-full mr-2"></div>
                                    <span>Khách hàng</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-blue-600 rounded-full mr-2"></div>
                                    <span>Tài xế</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Route Controls -->
                        <div class="absolute top-4 right-4 bg-white rounded-lg shadow-lg p-3">
                            <button id="show-route-btn" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                                Hiển thị đường đi
                            </button>
                        </div>
                    </div>
                    
                    <!-- Route Information -->
                    <div id="route-info" class="mt-4">
                        <!-- Route information will be displayed here -->
                    </div>
                    
                    <!-- Driver List -->
                    <div id="driver-list" class="mt-6 hidden">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">Tài xế khả dụng</h4>
                        <div id="available-drivers" class="space-y-3">
                            <!-- Driver cards will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Progress Tracker -->
            @if ($currentStepIndex > -1)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-900">Theo dõi đơn hàng</h3>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-clock mr-1"></i>
                                <span>Cập nhật: {{ $order->updated_at->format('H:i - d/m/Y') }}</span>
                            </div>
                        </div>
                        
                      
                        <div class="relative progress-tracker">
                            <!-- Steps -->
                            <div class="relative flex justify-between">
                                <!-- Progress Bar -->
                                <div class="absolute top-4 left-4 right-4 h-1 bg-gray-200 rounded-full">
                                    <div class="progress-bar-fill h-full bg-gradient-to-r from-orange-400 to-orange-600 rounded-full transition-all duration-700 ease-in-out"
                                        style="width: {{ $currentStepIndex >= 0 ? (($currentStepIndex + 1) / count($progressSteps)) * 100 : 0 }}%">
                                    </div>
                                </div>
                                @foreach ($progressSteps as $key => $stepInfo)
                                    @php
                                        $stepIndex = array_search($key, array_keys($progressSteps));
                                        $isCompleted = $stepIndex <= $currentStepIndex;
                                        $isCurrent = $stepIndex === $currentStepIndex;
                                        $isPending = $stepIndex > $currentStepIndex;
                                    @endphp
                                    <div class="progress-step flex flex-col items-center relative {{ $isCompleted ? 'completed' : ($isCurrent ? 'current' : 'pending') }}" data-step-key="{{ $key }}">
                                        <!-- Step Circle -->
                                        <div class="step-icon w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold z-10 transition-all duration-300
                                            @if($isCompleted && !$isCurrent)
                                                bg-green-500 text-white shadow-lg
                                            @elseif($isCurrent)
                                                bg-orange-500 text-white shadow-lg ring-4 ring-orange-200 animate-pulse
                                            @else
                                                bg-gray-200 text-gray-500
                                            @endif">
                                            @if ($isCompleted && !$isCurrent)
                                                <i class="fas fa-check text-xs"></i>
                                            @elseif($isCurrent)
                                                <i class="{{ $stepInfo['icon'] }} text-xs"></i>
                                            @else
                                                {{ $stepIndex + 1 }}
                                            @endif
                                        </div>

                                        <!-- Step Label -->
                                        <div class="mt-3 text-center max-w-20">
                                            <span class="step-text text-sm font-medium block
                                                @if($isCompleted)
                                                    text-green-600
                                                @elseif($isCurrent)
                                                    text-orange-600
                                                @else
                                                    text-gray-500
                                                @endif">
                                                {{ $stepInfo['text'] }}
                                            </span>
                                            @if ($isCurrent)
                                                <span class="text-xs text-blue-600 font-medium block mt-1 animate-bounce">
                                                    <i class="fas fa-arrow-up mr-1"></i>Hiện tại
                                                </span>
                                            @elseif($isCompleted && !$isCurrent)
                                                <span class="text-xs text-green-600 block mt-1">
                                                    <i class="fas fa-check-circle mr-1"></i>Hoàn thành
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Delivery Time Information -->
                        <div class="delivery-time">
                        @if(!in_array($order->status, ['delivered', 'item_received', 'cancelled', 'refunded', 'payment_failed', 'order_failed']))
                            @if($order->estimated_delivery_time)
                                <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200 estimated-delivery-time">
                                    <div class="flex items-center text-yellow-800">
                                        <i class="fas fa-clock mr-2"></i>
                                        <span class="font-medium">Dự kiến giao hàng: </span>
                                        <span class="ml-1 delivery-time-value">{{ \Carbon\Carbon::parse($order->estimated_delivery_time)->format('H:i - d/m/Y') }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200 estimated-delivery-time">
                                    <div class="flex items-center text-blue-800">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        <span class="font-medium">Thời gian giao hàng sẽ được cập nhật sau khi xác nhận đơn hàng</span>
                                    </div>
                                </div>
                            @endif
                        @elseif(in_array($order->status, ['delivered', 'item_received']))
                            @if($order->actual_delivery_time)
                                <div class="mt-6 p-4 bg-green-50 rounded-lg border border-green-200 actual-delivery-time">
                                    <div class="flex items-center text-green-800">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        <span class="font-medium">Đã giao thành công lúc: </span>
                                        <span class="ml-1 delivery-time-value">{{ \Carbon\Carbon::parse($order->actual_delivery_time)->format('H:i - d/m/Y') }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="mt-6 p-4 bg-green-50 rounded-lg border border-green-200 actual-delivery-time">
                                    <div class="flex items-center text-green-800">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        <span class="font-medium">Đơn hàng đã được giao thành công</span>
                                    </div>
                                </div>
                            @endif
                        @endif
                        </div>
                    </div>
                </div>
            @elseif(in_array($order->status, ['cancelled', 'refunded']))
                <!-- Cancelled/Refunded Status -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Trạng thái đơn hàng</h3>
                        <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center mr-4">
                                    <i class="{{ $order->statusIcon }} text-white"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-red-900">{{ $order->statusText }}</h4>
                                    <p class="text-sm text-red-700">
                                        @if($order->status == 'cancelled')
                                            Đơn hàng đã bị hủy. Nếu bạn đã thanh toán, số tiền sẽ được hoàn lại.
                                        @elseif($order->status == 'refunded')
                                            Đơn hàng đã được hoàn tiền thành công.
                                        @endif
                                    </p>
                                    <p class="text-xs text-red-600 mt-1">
                                        Cập nhật: {{ $order->updated_at->format('H:i - d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif



            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Sản phẩm đã đặt ({{ $order->orderItems->count() }}
                        món)</h3>

                    <div
                        class="{{ $order->orderItems->count() == 1 ? 'space-y-3' : 'grid grid-cols-1 md:grid-cols-2 gap-3' }}">
                        @foreach ($order->orderItems as $item)
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                {{-- Thông tin sản phẩm --}}
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-start gap-3 flex-1">
                                        {{-- Hình ảnh sản phẩm --}}
                                        <div class="w-11 h-11 bg-gray-200 rounded-md overflow-hidden flex-shrink-0">
                                            @if ($item->productVariant && $item->productVariant->product && $item->productVariant->product->images->count() > 0)
                                                <img src="{{ asset('images/products/' . $item->productVariant->product->images->first()->image_url) }}"
                                                    alt="{{ $item->product_name_snapshot ?? $item->productVariant->product->name }}"
                                                    class="w-full h-full object-cover">
                                            @elseif ($item->combo && $item->combo->image)
                                                <img src="{{ asset('images/combos/' . $item->combo->image) }}"
                                                    alt="{{ $item->combo_name_snapshot ?? $item->combo->name }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-gray-300 flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Thông tin sản phẩm --}}
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 text-sm">
                                                @if ($item->product_name_snapshot)
                                                    {{ $item->product_name_snapshot }}
                                                    @if ($item->variant_name_snapshot)
                                                        <span
                                                            class="text-gray-600">({{ $item->variant_name_snapshot }})</span>
                                                    @endif
                                                @elseif ($item->combo_name_snapshot)
                                                    {{ $item->combo_name_snapshot }}
                                                @else
                                                    {{ optional(optional($item->productVariant)->product)->name ?? (optional($item->combo)->name ?? 'Sản phẩm') }}
                                                @endif
                                            </h4>
                                            <div class="text-sm text-gray-600 mt-1">
                                                Số lượng: {{ $item->quantity }} | Đơn giá:
                                                {{ number_format($item->unit_price, 0, ',', '.') }}đ
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-orange-600">
                                            @php
                                                $totalItemPrice = $item->unit_price * $item->quantity;
                                                $totalToppingPrice = 0;
                                                foreach ($item->toppings as $topping) {
                                                    $totalToppingPrice +=
                                                        ($topping->topping_unit_price_snapshot ??
                                                            $topping->unit_price) *
                                                        $item->quantity;
                                                }
                                                $finalPrice = $totalItemPrice + $totalToppingPrice;
                                            @endphp
                                            {{ number_format($finalPrice, 0, ',', '.') }}đ
                                        </p>
                                        @if ($item->toppings->count() > 0)
                                            <p class="text-xs text-gray-500 mt-1">
                                                (Đã bao gồm topping)
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Topping --}}
                                @if ($item->toppings->count() > 0)
                                    <div class="mt-3 pt-2 border-t border-gray-300">
                                        <p class="text-xs font-medium text-orange-600 mb-2">Topping:</p>
                                        <div class="ms-3 space-y-1">
                                            @foreach ($item->toppings as $topping)
                                                <div class="flex justify-between items-center text-xs">
                                                    <span class="text-gray-600">
                                                        •
                                                        {{ $topping->topping_name_snapshot ?? optional($topping->topping)->name }}
                                                        @if ($item->quantity > 1)
                                                            (x{{ $item->quantity }})
                                                        @endif
                                                    </span>
                                                    <span class="font-medium text-green-600">
                                                        +{{ number_format($topping->topping_unit_price_snapshot ?? $topping->unit_price, 0, ',', '.') }}đ
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>



            <!-- Payment & Order Details -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Payment Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Thông tin thanh toán</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600">Tạm tính</span>
                                <span
                                    class="font-semibold text-gray-900">{{ number_format($order->subtotal, 0, ',', '.') }}đ</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-t border-gray-100">
                                <span class="text-gray-600">Phí giao hàng</span>
                                <span
                                    class="font-semibold text-gray-900">{{ number_format($order->delivery_fee, 0, ',', '.') }}đ</span>
                            </div>
                            @if ($order->discount_amount > 0)
                                <div class="flex justify-between items-center py-2 border-t border-gray-100">
                                    <span class="text-gray-600">Giảm giá
                                        ({{ optional($order->discountCode)->code }})</span>
                                    <span
                                        class="font-semibold text-green-600">-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</span>
                                </div>
                            @endif
                            <div class="border-t border-gray-300 pt-4 mt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-gray-900">Tổng cộng</span>
                                    <span
                                        class="text-xl font-bold text-red-600">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Chi tiết đơn hàng</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600">Mã đơn hàng</span>
                                <span class="font-semibold text-gray-900">#{{ $order->order_code }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-t border-gray-100">
                                <span class="text-gray-600">Chi nhánh</span>
                                <span class="font-semibold text-gray-900">{{ $order->branch->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-t border-gray-100">
                                <span class="text-gray-600">Phương thức thanh toán</span>
                                <span class="font-semibold text-gray-900">
                                    @php
                                        $paymentMethods = [
                                            'cash' => 'Tiền mặt',
                                            'cod' => 'Tiền mặt', // Backward compatibility
                                            'vnpay' => 'VNPay',
                                            'balance' => 'Số dư tài khoản',
                                        ];
                                        $paymentMethod = optional($order->payment)->payment_method ?? 'cash';
                                    @endphp
                                    {{ $paymentMethods[$paymentMethod] ?? 'Tiền mặt' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Trạng thái thanh toán:</span>
                                <span
                                    class="font-medium {{ optional($order->payment)->payment_status == 'completed' ? 'text-green-600' : 'text-yellow-600' }}">{{ optional($order->payment)->payment_status == 'completed' ? 'Đã thanh toán' : 'Chưa thanh toán' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Notes -->
            @if ($order->notes)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Ghi chú</h3>
                        <p class="text-gray-700 bg-gray-50 p-4 rounded-lg text-sm italic">
                            "{{ $order->notes }}"
                        </p>
                    </div>
                </div>
            @endif

        </div>
    </div>
    <!-- Product Detail Modal -->
    <div id="product-details-modal"
        class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 p-4 transition-opacity duration-300 opacity-0 pointer-events-none">
        <div id="modal-content"
            class="bg-white rounded-lg max-w-md w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300 shadow-xl">
        </div>
    </div>
@endsection

@push('styles')
    <!-- Mapbox GL CSS -->
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css' rel='stylesheet' />
    
    <style>
        /* Custom animations for driver search map */
        @keyframes scanning-pulse {
            0% {
                transform: scale(0.5);
                opacity: 0.8;
            }
            50% {
                transform: scale(1.5);
                opacity: 0.4;
            }
            100% {
                transform: scale(3);
                opacity: 0;
            }
        }
        
        .scanning-wave {
            animation: scanning-pulse 3s ease-out infinite !important;
            transform-origin: center center !important;
        }
        
        .scanning-wave:nth-child(1) {
            animation-delay: 0s !important;
        }
        
        .scanning-wave:nth-child(2) {
            animation-delay: 1s !important;
        }
        
        .scanning-wave:nth-child(3) {
            animation-delay: 2s !important;
        }
        
        /* Driver marker animations */
        .driver-marker {
            animation: fadeInBounce 0.6s ease-out;
        }
        
        @keyframes fadeInBounce {
            0% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.3) translateY(-20px);
            }
            50% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.1) translateY(-5px);
            }
            100% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1) translateY(0);
            }
        }
        
        /* Route animation */
        #delivery-route {
            stroke-dasharray: 10, 5;
            animation: routeFlow 2s linear infinite;
        }
        
        @keyframes routeFlow {
            0% {
                stroke-dashoffset: 0;
            }
            100% {
                stroke-dashoffset: -15;
            }
        }
        
        /* Map grid enhancement */
        .map-grid {
            background-image: 
                linear-gradient(rgba(59, 130, 246, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59, 130, 246, 0.1) 1px, transparent 1px);
            background-size: 20px 20px;
        }
        
        /* Hover effects for interactive elements */
        .custom-marker:hover {
            transform: scale(1.1) !important;
            z-index: 30 !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3) !important;
        }
        
        .restaurant-marker:hover {
            transform: rotate(-45deg) scale(1.1) !important;
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.6) !important;
        }
        
        .customer-marker:hover {
            transform: rotate(-45deg) scale(1.1) !important;
            box-shadow: 0 8px 25px rgba(22, 163, 74, 0.6) !important;
        }
        
        .driver-marker:hover {
            transform: scale(1.2) !important;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.6) !important;
        }
        
        /* Marker pulse effect */
        .marker-pulse {
            position: relative;
        }
        
        .marker-pulse::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: inherit;
            transform: translate(-50%, -50%);
            animation: markerPulse 2s infinite;
            opacity: 0.6;
            z-index: -1;
        }
        
        @keyframes markerPulse {
            0% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 0.6;
            }
            70% {
                transform: translate(-50%, -50%) scale(1.8);
                opacity: 0;
            }
            100% {
                transform: translate(-50%, -50%) scale(1.8);
                opacity: 0;
            }
        }
        
        /* Enhanced driver list styling */
        .driver-item {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .driver-item:hover {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            border-left-color: #3b82f6;
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        /* Status indicator animations */
        .status-searching {
            animation: pulse 1.5s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }
    </style>
@endpush

@push('scripts')
    {{-- Include Pusher for real-time updates --}}
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    
    {{-- Include Mapbox GL JS --}}
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js'></script>
    
    {{-- Include order show real-time script --}}
    <script src="{{ asset('js/admin/order-show-realtime.js') }}"></script>
    
    <script>
        // Set Pusher configuration for real-time updates
        window.pusherKey = '{{ config('broadcasting.connections.pusher.key') }}';
        window.pusherCluster = '{{ config('broadcasting.connections.pusher.options.cluster') }}';
        
        // Hàm global để điều khiển quá trình tìm kiếm từ nút bấm
        function stopDriverSearch() {
            if (window.driverSearchMap && typeof window.driverSearchMap.stopScanning === 'function') {
                window.driverSearchMap.stopScanning();
                
                // Cập nhật giao diện
                const statusText = document.getElementById('search-status-text');
                if (statusText) {
                    statusText.textContent = 'Đã dừng tìm kiếm theo yêu cầu';
                }
                
                // Thêm log
                if (window.driverSearchMap && typeof window.driverSearchMap.addSearchLog === 'function') {
                    window.driverSearchMap.addSearchLog('Người dùng đã dừng quá trình tìm kiếm');
                }
            }
        }
        
        function restartDriverSearch() {
            if (window.driverSearchMap && typeof window.driverSearchMap.simulateDriverSearch === 'function') {
                // Dừng quá trình hiện tại nếu có
                if (window.driverSearchMap.searchInterval) {
                    clearInterval(window.driverSearchMap.searchInterval);
                }
                
                // Reset giao diện
                const progressBar = document.getElementById('search-progress-bar');
                if (progressBar) {
                    progressBar.style.width = '2%';
                }
                
                const searchLog = document.getElementById('search-log');
                if (searchLog) {
                    const currentTime = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    searchLog.innerHTML = '<div class="flex items-center space-x-2">' +
                        '<div class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>' +
                        '<span>Khởi động lại quá trình tìm kiếm...</span>' +
                        '<span class="text-gray-400 ml-auto">' + currentTime + '</span>' +
                        '</div>';
                }
                
                // Khởi động lại sau 500ms
                setTimeout(() => {
                    window.driverSearchMap.simulateDriverSearch();
                }, 500);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize real-time order status updates
            if (typeof AdminOrderShowRealtime !== 'undefined') {
                const orderId = {{ $order->id }};
                window.orderShowRealtime = new AdminOrderShowRealtime(orderId);
            }
            
            // Initialize driver search map
            if (document.getElementById('driver-search-section')) {
                window.driverSearchMap = new DriverSearchMap();
                
                // Tự động khởi động mô phỏng tìm kiếm tài xế nếu đơn hàng đang cần tìm tài xế
                @if (in_array($order->status, ['confirmed', 'awaiting_driver']) && !$order->driver_id)
                    // Khởi động mô phỏng sau 1 giây để đảm bảo giao diện đã được render
                    setTimeout(() => {
                        if (window.driverSearchMap && typeof window.driverSearchMap.simulateDriverSearch === 'function') {
                            window.driverSearchMap.simulateDriverSearch();
                        }
                    }, 1000);
                @endif
            }
        });
        
        // Driver Search and Map functionality
        class DriverSearchMap {
            constructor() {
                this.drivers = [];
                this.searchActive = false;
                this.scanningInterval = null;
                this.map = null;
                this.driverMarkers = [];
                this.restaurantMarker = null;
                this.customerMarker = null;
                this.routeSource = null;
                this.routeVisible = false;
                this.init();
            }
            
            init() {
                this.initializeMap();
                this.bindEvents();
                this.startDriverSearch();
            }
            
            initializeMap() {
                // Set Mapbox access token from Laravel config
                mapboxgl.accessToken = '{{ config("services.mapbox.access_token") }}';
                
                // Use actual branch coordinates or fallback to Ho Chi Minh City
                const centerLng = {{ $branchLng ?? 106.6297 }};
                const centerLat = {{ $branchLat ?? 10.8231 }};
                
                // Initialize map centered on actual branch location
                this.map = new mapboxgl.Map({
                    container: 'mapbox-container',
                    style: 'mapbox://styles/mapbox/streets-v12',
                    center: [centerLng, centerLat],
                    zoom: 13
                });
                
                // Add map controls
                this.map.addControl(new mapboxgl.NavigationControl());
                
                // Wait for map to load before adding markers
                this.map.on('load', () => {
                    this.addRestaurantMarker();
                    this.addCustomerMarker();
                    this.setupRouteSource();
                    
                    // Automatically show route after map is loaded
                    setTimeout(() => {
                        this.showRouteToCustomer();
                    }, 1500);
                });
            }
            
            addRestaurantMarker() {
                // Restaurant marker (red)
                const restaurantEl = document.createElement('div');
                restaurantEl.className = 'custom-marker restaurant-marker marker-pulse';
                restaurantEl.style.cssText = `
                    background: linear-gradient(135deg, #dc2626, #b91c1c);
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    border: 3px solid white;
                    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                `;
                restaurantEl.innerHTML = `
                    <div style="color: white; font-size: 18px;">
                        🏪
                    </div>
                `;
                
                this.restaurantMarker = new mapboxgl.Marker(restaurantEl)
                    .setLngLat([{{ $branchLng ?? 106.6297 }}, {{ $branchLat ?? 10.8231 }}]) // Restaurant coordinates
                    .setPopup(new mapboxgl.Popup().setHTML('<h3>{{ $order->branch->name ?? "Nhà hàng" }}</h3>'))
                    .addTo(this.map);
            }
            
            addCustomerMarker() {
                // Customer marker (green)
                const customerEl = document.createElement('div');
                customerEl.className = 'custom-marker customer-marker marker-pulse';
                customerEl.style.cssText = `
                    background: linear-gradient(135deg, #16a34a, #15803d);
                    width: 35px;
                    height: 35px;
                    border-radius: 50%;
                    border: 3px solid white;
                    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.4);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                `;
                customerEl.innerHTML = `
                    <div style="color: white; font-size: 16px;">
                        🏠
                    </div>
                `;
                
                this.customerMarker = new mapboxgl.Marker(customerEl)
                    .setLngLat([{{ $customerLng ?? 106.6397 }}, {{ $customerLat ?? 10.8131 }}]) // Customer coordinates
                    .setPopup(new mapboxgl.Popup().setHTML('<h3>Địa chỉ giao hàng</h3>'))
                    .addTo(this.map);
            }
            
            setupRouteSource() {
                // Add route source for drawing delivery routes
                this.map.addSource('route', {
                    'type': 'geojson',
                    'data': {
                        'type': 'Feature',
                        'properties': {},
                        'geometry': {
                            'type': 'LineString',
                            'coordinates': []
                        }
                    }
                });
                
                // Add route layer
                this.map.addLayer({
                    'id': 'route',
                    'type': 'line',
                    'source': 'route',
                    'layout': {
                        'line-join': 'round',
                        'line-cap': 'round'
                    },
                    'paint': {
                        'line-color': '#3b82f6',
                        'line-width': 4,
                        'line-opacity': 0.8
                    }
                });
            }
            
            bindEvents() {
                // Confirm order button
                const confirmBtn = document.getElementById('confirm-order-btn');
                if (confirmBtn) {
                    confirmBtn.addEventListener('click', () => {
                        this.confirmOrder();
                    });
                }
                
                // Cancel order button
                const cancelBtn = document.getElementById('cancel-order-btn');
                if (cancelBtn) {
                    cancelBtn.addEventListener('click', () => {
                        this.cancelOrder();
                    });
                }
                
                // Show/Hide route button
                const showRouteBtn = document.getElementById('show-route-btn');
                if (showRouteBtn) {
                    showRouteBtn.addEventListener('click', () => {
                        this.toggleRoute();
                    });
                }
            }
            
            confirmOrder() {
                if (confirm('Bạn có chắc chắn muốn xác nhận đơn hàng này?')) {
                    // Update order status via AJAX
                    fetch('/branch/orders/{{ $order->id }}/confirm', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Có lỗi xảy ra khi xác nhận đơn hàng');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi xác nhận đơn hàng');
                    });
                }
            }
            
            cancelOrder() {
                const reason = prompt('Vui lòng nhập lý do hủy đơn:');
                if (reason && reason.trim()) {
                    // Update order status via AJAX
                    fetch('/branch/orders/{{ $order->id }}/cancel', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ reason: reason.trim() })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Có lỗi xảy ra khi hủy đơn hàng');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi hủy đơn hàng');
                    });
                }
            }
            
            startDriverSearch() {
                if (!document.getElementById('driver-search-section')) return;
                
                this.searchActive = true;
                this.updateSearchStatus('Đang quét vị trí tài xế...');
                
                // Fetch real driver data from API
                fetch('/branch/orders/{{ $order->id }}/nearby-drivers')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.drivers.length > 0) {
                            this.loadRealDrivers(data.drivers);
                            this.displayDriversOnMap();
                            this.showDriverList();
                            this.updateSearchStatus('Đã tìm thấy ' + data.drivers.length + ' tài xế khả dụng');
                        } else {
                            this.updateSearchStatus('Không tìm thấy tài xế nào gần đó');
                        }
                        this.stopScanning();
                    })
                    .catch(error => {
                        console.error('Error fetching drivers:', error);
                        this.updateSearchStatus('Có lỗi xảy ra khi tìm kiếm tài xế');
                        this.stopScanning();
                    });
            }
            
            loadRealDrivers(driversData) {
                // Load real driver data from API response
                this.drivers = driversData.map(driver => {
                    // Calculate ETA based on distance (assuming 30km/h average speed)
                    const eta = Math.ceil(driver.distance * 2); // minutes
                    
                    return {
                        id: driver.id,
                        name: driver.name,
                        phone: driver.phone,
                        rating: parseFloat(driver.rating) || 4.5,
                        vehicle: driver.vehicle_type || 'Xe máy',
                        license_plate: driver.license_plate,
                        distance: parseFloat(driver.distance).toFixed(1),
                        eta: eta,
                        coordinates: [parseFloat(driver.longitude), parseFloat(driver.latitude)],
                        current_orders: driver.current_orders || 0
                    };
                });
            }
            
            fitMapToMarkers() {
                if (!this.map) return;
                
                // Collect all marker coordinates
                const coordinates = [];
                
                // Add restaurant coordinates
                if (this.restaurantMarker) {
                    coordinates.push(this.restaurantMarker.getLngLat().toArray());
                }
                
                // Add customer coordinates
                if (this.customerMarker) {
                    coordinates.push(this.customerMarker.getLngLat().toArray());
                }
                
                // Add driver coordinates
                this.drivers.forEach(driver => {
                    coordinates.push(driver.coordinates);
                });
                
                if (coordinates.length > 0) {
                    // Create bounds that include all coordinates
                    const bounds = coordinates.reduce((bounds, coord) => {
                        return bounds.extend(coord);
                    }, new mapboxgl.LngLatBounds(coordinates[0], coordinates[0]));
                    
                    // Fit map to bounds with padding
                    this.map.fitBounds(bounds, {
                        padding: 50,
                        maxZoom: 15
                    });
                }
            }
            
            displayDriversOnMap() {
                if (!this.map) return;
                
                // Clear existing driver markers
                this.driverMarkers.forEach(marker => marker.remove());
                this.driverMarkers = [];
                
                this.drivers.forEach((driver, index) => {
                    setTimeout(() => {
                        // Create driver marker element
                        const driverEl = document.createElement('div');
                        driverEl.className = 'custom-marker driver-marker marker-pulse';
                        driverEl.style.cssText = `
                            background: linear-gradient(135deg, #2563eb, #1d4ed8);
                            width: 32px;
                            height: 32px;
                            border-radius: 50%;
                            border: 3px solid white;
                            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            cursor: pointer;
                            animation: fadeInBounce 0.6s ease-out;
                            position: relative;
                        `;
                        driverEl.innerHTML = `
                            <div style="color: white; font-size: 16px; display: flex; align-items: center; justify-content: center;">
                                🏍️
                            </div>
                            <div style="
                                position: absolute;
                                top: -8px;
                                right: -8px;
                                background: #10b981;
                                color: white;
                                border-radius: 50%;
                                width: 16px;
                                height: 16px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-size: 10px;
                                font-weight: bold;
                                border: 2px solid white;
                            ">${driver.id}</div>
                        `;
                        
                        // Create popup content
                        const popupContent = `
                            <div class="p-2">
                                <h4 class="font-semibold">${driver.name}</h4>
                                <p class="text-sm text-gray-600">${driver.vehicle}</p>
                                <p class="text-sm text-gray-600">${driver.distance} km • ${driver.eta} phút</p>
                                <div class="flex items-center mt-1">
                                    <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="text-sm">${driver.rating}</span>
                                </div>
                                <button class="mt-2 bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded" onclick="window.driverSearchMap.selectDriver(${JSON.stringify(driver).replace(/"/g, '&quot;')})">
                                    Chọn tài xế
                                </button>
                            </div>
                        `;
                        
                        // Create marker
                        const marker = new mapboxgl.Marker(driverEl)
                            .setLngLat(driver.coordinates)
                            .setPopup(new mapboxgl.Popup().setHTML(popupContent))
                            .addTo(this.map);
                        
                        // Add click event
                        driverEl.addEventListener('click', () => {
                            this.selectDriver(driver);
                        });
                        
                        this.driverMarkers.push(marker);
                        
                        // Fit map to show all markers after the last driver is added
                        if (index === this.drivers.length - 1) {
                            setTimeout(() => {
                                this.fitMapToMarkers();
                            }, 500);
                        }
                    }, index * 500);
                });
            }
            
            showDriverList() {
                const driverList = document.getElementById('driver-list');
                const availableDrivers = document.getElementById('available-drivers');
                
                if (!driverList || !availableDrivers) return;
                
                availableDrivers.innerHTML = '';
                
                this.drivers.forEach(driver => {
                    const driverCard = document.createElement('div');
                    driverCard.className = 'driver-item bg-white rounded-lg p-4 border border-gray-200 cursor-pointer shadow-sm hover:shadow-md';
                    driverCard.innerHTML = `
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                                    <span class="text-white text-lg">🏍️</span>
                                </div>
                                <div>
                                    <h5 class="font-medium text-gray-900">${driver.name}</h5>
                                    <p class="text-sm text-gray-600">${driver.vehicle} • ${driver.license_plate || 'N/A'}</p>
                                    <p class="text-sm text-gray-600">${driver.phone}</p>
                                    <div class="flex items-center justify-between mt-1">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <span class="text-sm text-gray-600">${driver.rating}</span>
                                        </div>
                                        <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">${driver.current_orders}/3 đơn</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">${driver.distance} km</p>
                                <p class="text-sm text-gray-600">${driver.eta} phút</p>
                                <button class="mt-2 bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded transition-colors select-driver-btn" data-driver='${JSON.stringify(driver)}'>
                                    Chọn tài xế
                                </button>
                            </div>
                        </div>
                    `;
                    
                    // Add event listener for select driver button
                    const selectBtn = driverCard.querySelector('.select-driver-btn');
                    selectBtn.addEventListener('click', () => {
                        this.selectDriver(driver);
                    });
                    
                    availableDrivers.appendChild(driverCard);
                });
                
                driverList.classList.remove('hidden');
            }
            
            selectDriver(driver) {
                if (confirm('Bạn có muốn chọn tài xế ' + driver.name + ' để giao đơn hàng này?')) {
                    this.assignDriver(driver);
                    this.showRoute(driver);
                }
            }
            
            assignDriver(driver) {
                // Send driver assignment to server
                fetch('/branch/orders/{{ $order->id }}/assign-driver', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ driver_id: driver.id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.updateSearchStatus('Đã phân công tài xế: ' + driver.name);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
            
            async getDirections(start, end) {
                const url = 'https://api.mapbox.com/directions/v5/mapbox/driving/' + start[0] + ',' + start[1] + ';' + end[0] + ',' + end[1] + '?steps=true&geometries=geojson&access_token=' + mapboxgl.accessToken;
                
                try {
                    const response = await fetch(url);
                    const data = await response.json();
                    
                    if (data.routes && data.routes.length > 0) {
                        return data.routes[0];
                    }
                    return null;
                } catch (error) {
                    console.error('Error fetching directions:', error);
                    return null;
                }
            }
            
            async showRouteToCustomer() {
                if (!this.map) return;
                
                // Get actual coordinates from server data
                const restaurantCoords = [{{ $branchLng ?? 106.6297 }}, {{ $branchLat ?? 10.8231 }}];
                const customerCoords = [{{ $customerLng ?? 106.6397 }}, {{ $customerLat ?? 10.8131 }}];
                
                // Validate coordinates
                if (!restaurantCoords[0] || !restaurantCoords[1] || !customerCoords[0] || !customerCoords[1]) {
                    alert('Không thể hiển thị đường đi do thiếu thông tin tọa độ.');
                    return;
                }
                
                // Get actual route from Mapbox Directions API
                const route = await this.getDirections(restaurantCoords, customerCoords);
                
                if (route) {
                    // Update route source with actual route geometry
                    this.map.getSource('route').setData({
                        'type': 'Feature',
                        'properties': {
                            'distance': route.distance,
                            'duration': route.duration
                        },
                        'geometry': route.geometry
                    });
                    
                    // Fit map to show the route
                    const coordinates = route.geometry.coordinates;
                    const bounds = new mapboxgl.LngLatBounds();
                    coordinates.forEach(coord => bounds.extend(coord));
                    this.map.fitBounds(bounds, { padding: 50 });
                    
                    // Show route info
                    this.showRouteInfo(route);
                } else {
                    // Fallback to simple line if API fails
                    this.map.getSource('route').setData({
                        'type': 'Feature',
                        'properties': {},
                        'geometry': {
                            'type': 'LineString',
                            'coordinates': [restaurantCoords, customerCoords]
                        }
                    });
                    
                    const bounds = new mapboxgl.LngLatBounds();
                    [restaurantCoords, customerCoords].forEach(coord => bounds.extend(coord));
                    this.map.fitBounds(bounds, { padding: 50 });
                }
                
                // Update route visibility state and button text
                this.routeVisible = true;
                this.updateRouteButton();
            }
            
            hideRoute() {
                if (!this.map) return;
                
                // Clear route data
                this.map.getSource('route').setData({
                    'type': 'Feature',
                    'properties': {},
                    'geometry': {
                        'type': 'LineString',
                        'coordinates': []
                    }
                });
                
                // Clear route info
                const routeInfoElement = document.getElementById('route-info');
                if (routeInfoElement) {
                    routeInfoElement.innerHTML = '';
                }
                
                // Update route visibility state and button text
                this.routeVisible = false;
                this.updateRouteButton();
            }
            
            toggleRoute() {
                if (this.routeVisible) {
                    this.hideRoute();
                } else {
                    this.showRouteToCustomer();
                }
            }
            
            updateRouteButton() {
                const showRouteBtn = document.getElementById('show-route-btn');
                if (showRouteBtn) {
                    if (this.routeVisible) {
                        showRouteBtn.innerHTML = `
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                            </svg>
                            Ẩn đường đi
                        `;
                        showRouteBtn.className = 'bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center text-sm';
                    } else {
                        showRouteBtn.innerHTML = `
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                            Hiển thị đường đi
                        `;
                        showRouteBtn.className = 'bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center text-sm';
                    }
                }
            }
            
            showRouteInfo(route) {
                const distance = (route.distance / 1000).toFixed(1); // Convert to km
                const duration = Math.round(route.duration / 60); // Convert to minutes
                
                const routeInfoElement = document.getElementById('route-info');
                if (routeInfoElement) {
                    routeInfoElement.innerHTML = `
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                            <div class="flex items-center text-blue-800">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                                <span class="font-semibold">Thông tin đường đi</span>
                            </div>
                            <div class="mt-2 text-sm text-blue-700">
                                <p><strong>Khoảng cách:</strong> ${distance} km</p>
                                <p><strong>Thời gian dự kiến:</strong> ${duration} phút</p>
                            </div>
                        </div>
                    `;
                }
            }
            
            showRoute(driver) {
                if (!this.map) return;
                
                // Create route coordinates from restaurant to customer via driver
                const restaurantCoords = [{{ $branchLng ?? 106.6297 }}, {{ $branchLat ?? 10.8231 }}];
                const customerCoords = [{{ $customerLng ?? 106.6397 }}, {{ $customerLat ?? 10.8131 }}];
                const driverCoords = driver.coordinates;
                
                // Simple route: restaurant -> driver -> customer
                const routeCoordinates = [
                    restaurantCoords,
                    driverCoords,
                    customerCoords
                ];
                
                // Update route source
                this.map.getSource('route').setData({
                    'type': 'Feature',
                    'properties': {},
                    'geometry': {
                        'type': 'LineString',
                        'coordinates': routeCoordinates
                    }
                });
                
                // Fit map to show the route
                const bounds = new mapboxgl.LngLatBounds();
                routeCoordinates.forEach(coord => bounds.extend(coord));
                this.map.fitBounds(bounds, { padding: 50 });
                
                this.stopScanning();
            }
            
            // Cập nhật giao diện tìm kiếm tài xế theo thời gian thực
            updateDriverSearchProgress(data) {
                // Cập nhật text trạng thái
                const statusText = document.getElementById('search-status-text');
                if (statusText && data.status) {
                    statusText.textContent = data.status;
                }
                
                // Cập nhật số lần thử
                const attemptCounter = document.getElementById('attempt-counter');
                if (attemptCounter && data.attempt) {
                    attemptCounter.textContent = 'Lần thử: ' + data.attempt + '/60';
                    
                    // Cập nhật thanh tiến trình
                    const progressBar = document.getElementById('search-progress-bar');
                    if (progressBar) {
                        const percentage = Math.min((data.attempt / 60) * 100, 100);
                        progressBar.style.width = percentage + '%';
                    }
                }
                
                // Cập nhật bán kính tìm kiếm
                const searchRadius = document.getElementById('search-radius');
                if (searchRadius && data.radius) {
                    searchRadius.textContent = data.radius;
                }
                
                // Cập nhật số lượng tài xế tìm thấy
                const driversFound = document.getElementById('drivers-found');
                if (driversFound && data.driversCount !== undefined) {
                    driversFound.textContent = data.driversCount;
                }
                
                // Thêm log hoạt động
                if (data.logMessage) {
                    this.addSearchLog(data.logMessage);
                }
            }
            
            // Thêm log vào nhật ký tìm kiếm
            addSearchLog(message) {
                const searchLog = document.getElementById('search-log');
                if (searchLog) {
                    const now = new Date();
                    const timeString = now.toLocaleTimeString('vi-VN', { 
                        hour: '2-digit', 
                        minute: '2-digit', 
                        second: '2-digit' 
                    });
                    
                    const logEntry = document.createElement('div');
                    logEntry.className = 'flex items-center space-x-2';
                    logEntry.innerHTML = `
                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                        <span>${message}</span>
                        <span class="text-gray-400 ml-auto">${timeString}</span>
                    `;
                    
                    // Thêm vào đầu danh sách
                    searchLog.insertBefore(logEntry, searchLog.firstChild);
                    
                    // Giới hạn số lượng log hiển thị (tối đa 10 dòng)
                    while (searchLog.children.length > 10) {
                        searchLog.removeChild(searchLog.lastChild);
                    }
                    
                    // Cuộn xuống để hiển thị log mới nhất
                    searchLog.scrollTop = 0;
                }
            }
            
            // Mô phỏng quá trình tìm kiếm tài xế theo logic FindDriverForOrderJob
            simulateDriverSearch() {
                let attempt = 1;
                let radius = 2;
                const maxAttempts = 60;
                const maxRadius = 20;
                
                // Khởi tạo trạng thái ban đầu
                this.updateDriverSearchProgress({
                    status: 'Bắt đầu tìm kiếm tài xế...',
                    attempt: attempt,
                    radius: radius,
                    driversCount: 0,
                    logMessage: 'Khởi tạo quá trình tìm kiếm'
                });
                
                const searchInterval = setInterval(() => {
                    attempt++;
                    
                    // Mở rộng bán kính sau mỗi 10 lần thử (theo logic FindDriverForOrderJob)
                    if (attempt % 10 === 0 && radius < maxRadius) {
                        radius += 2;
                        this.addSearchLog('Mở rộng bán kính tìm kiếm lên ' + radius + 'km');
                    }
                    
                    // Mô phỏng tìm kiếm tài xế
                    const driversFound = Math.floor(Math.random() * 4);
                    
                    // Cập nhật trạng thái
                    this.updateDriverSearchProgress({
                        status: 'Đang tìm kiếm trong bán kính ' + radius + 'km...',
                        attempt: attempt,
                        radius: radius,
                        driversCount: driversFound,
                        logMessage: 'Quét lần ' + attempt + ' - Tìm thấy ' + driversFound + ' tài xế trong bán kính ' + radius + 'km'
                    });
                    
                    // Mô phỏng tìm thấy tài xế phù hợp
                    if (driversFound > 0 && Math.random() > 0.7) {
                        clearInterval(searchInterval);
                        this.updateDriverSearchProgress({
                            status: 'Đã tìm thấy tài xế phù hợp!',
                            attempt: attempt,
                            radius: radius,
                            driversCount: driversFound,
                            logMessage: 'Tìm thấy tài xế và đang gửi thông báo'
                        });
                        
                        // Ẩn giao diện tìm kiếm sau 3 giây
                        setTimeout(() => {
                            const searchProgress = document.getElementById('driver-search-progress');
                            if (searchProgress) {
                                searchProgress.style.display = 'none';
                            }
                        }, 3000);
                        return;
                    }
                    
                    // Dừng khi đạt số lần thử tối đa
                    if (attempt >= maxAttempts) {
                        clearInterval(searchInterval);
                        this.updateDriverSearchProgress({
                            status: 'Không tìm thấy tài xế khả dụng',
                            attempt: attempt,
                            radius: radius,
                            driversCount: 0,
                            logMessage: 'Kết thúc quá trình tìm kiếm - Không có tài xế khả dụng'
                        });
                    }
                }, 2000); // Cập nhật mỗi 2 giây
                
                // Lưu interval để có thể dừng khi cần
                this.searchInterval = searchInterval;
            }

            updateSearchStatus(message) {
                const statusElement = document.getElementById('search-status');
                if (statusElement) {
                    statusElement.innerHTML = `
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-2"></div>
                        <span class="text-blue-600 font-medium">${message}</span>
                    `;
                }
            }
            
            stopScanning() {
                const scanningEffect = document.getElementById('scanning-effect');
                if (scanningEffect) {
                    scanningEffect.style.display = 'none';
                }
                
                const statusElement = document.getElementById('search-status');
                if (statusElement) {
                    statusElement.innerHTML = `
                        <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-green-600 font-medium">Đã tìm thấy ${this.drivers.length} tài xế</span>
                    `;
                }
                
                // Dừng quá trình tìm kiếm mô phỏng
                if (this.searchInterval) {
                    clearInterval(this.searchInterval);
                    this.searchInterval = null;
                    this.addSearchLog('Đã dừng quá trình tìm kiếm');
                }
            }
        }
    </script>
@endpush
