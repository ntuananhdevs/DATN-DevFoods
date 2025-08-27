@extends('layouts.customer.fullLayoutMaster')

@section('title', 'Chi tiết đơn hàng #' . ($order->order_code ?? $order->id))

@push('styles')
<style>
    /* CSS để giảm thiểu hiệu ứng nháy màn hình */
    .status-bar, .driver-info-container, .action-buttons-container {
        transition: opacity 0.3s ease-in-out;
        will-change: opacity;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
    }
    
    /* Hiệu ứng mượt mà cho các phần tử được cập nhật */
    .smooth-update {
        transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
    }
    
    .smooth-update.updating {
        opacity: 0.7;
        transform: translateY(-2px);
    }
    
    /* Tối ưu hóa hiệu suất rendering */
    .gpu-accelerated {
        transform: translateZ(0);
        -webkit-transform: translateZ(0);
        will-change: transform, opacity;
    }
    
    /* Ngăn chặn layout shift */
    .order-content {
        min-height: 100vh;
    }
    
    /* Hiệu ứng loading mượt mà */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
        z-index: 10;
    }
    
    .loading-overlay.show {
        opacity: 1;
        visibility: visible;
    }
</style>
@endpush

@section('content')
    @php
        // Logic xử lý thanh trạng thái
        $progressSteps = [
            'confirmed' => ['text' => 'Chờ xác nhận', 'icon' => 'fas fa-check-circle'],
            'driver_picked_up' => ['text' => 'Chờ lấy hàng', 'icon' => 'fas fa-shopping-bag'],
            'in_transit' => ['text' => 'Đang giao', 'icon' => 'fas fa-truck'],
            'item_received' => ['text' => 'Đã nhận', 'icon' => 'fas fa-box-check'],
        ];

        $statusMapToStep = [
            'awaiting_confirmation' => 'confirmed',
            'confirmed' => 'confirmed',
            'waiting_for_driver' => 'driver_picked_up',
            'finding_driver' => 'driver_picked_up',
            'awaiting_driver' => 'driver_picked_up',
            'driver_confirmed' => 'driver_picked_up',
            'waiting_driver_pick_up' => 'driver_picked_up',
            'driver_picked_up' => 'driver_picked_up',
            'in_transit' => 'in_transit',
            'delivered' => 'item_received',
            'item_received' => 'item_received',
        ];

        $currentStepKey = $statusMapToStep[$order->status] ?? null;
        $currentStepIndex = $currentStepKey ? array_search($currentStepKey, array_keys($progressSteps)) : -1;

        if ($order->status == 'cancelled') {
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
                    <button onclick="history.back()" class="text-white hover:text-gray-200 mr-4">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </button>
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
                                        <div>
                                            <span class="text-gray-500">Dự kiến giao:</span>
                                            <span class="font-medium ml-2">
                                                {{ $order->estimated_delivery_time ? date('H:i', strtotime($order->estimated_delivery_time)) : 'N/A' }}
                                            </span>
                                        </div>
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
                                            'waiting_for_driver',
                                            'finding_driver',
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

                                            @if (in_array($order->status, ['confirmed', 'awaiting_driver']) && !$order->driver_id)
                                                <!-- Hiệu ứng loading khi đang tìm tài xế -->
                                                <div class="flex items-center space-x-3">
                                                    <div
                                                        class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600">
                                                    </div>
                                                    <div class="text-sm text-gray-600">
                                                        <div class="font-medium">Đang tìm tài xế...</div>
                                                        <div class="text-xs text-gray-500 mt-1">Vui lòng chờ trong giây lát
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Hiệu ứng dots loading -->
                                                <div class="mt-3 flex space-x-1">
                                                    <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce"
                                                        style="animation-delay: 0ms"></div>
                                                    <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce"
                                                        style="animation-delay: 150ms"></div>
                                                    <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce"
                                                        style="animation-delay: 300ms"></div>
                                                </div>
                                            @elseif($order->driver_id && $order->driver)
                                                <!-- Thông tin tài xế đầy đủ -->
                                                <div class="space-y-3">
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
                                                            <div class="font-semibold text-gray-900">
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
                                                                    class="font-medium text-blue-600 hover:text-blue-800 flex items-center">
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
                                                                    class="font-medium text-gray-900 font-mono bg-white px-2 py-1 rounded border">{{ $order->driver->license_plate }}</span>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Trạng thái giao hàng -->
                                                    <div class="mt-3 p-2 bg-white rounded border-l-4 border-green-500">
                                                        <div class="text-sm">
                                                            <span class="text-gray-600">Trạng thái:</span>
                                                            <span class="font-medium text-green-600 ml-1">
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
                                                    
                                                    <!-- Đánh giá của bạn về tài xế -->
                                                    @php
                                                        $userRating = \App\Models\DriverRating::where('order_id', $order->id)
                                                            ->where('user_id', Auth::id())
                                                            ->first();
                                                    @endphp
                                                    
                                                    @if ($userRating)
                                                    <div class="mt-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                                        <h4 class="text-sm font-medium text-gray-800 mb-2">Đánh giá của bạn về tài xế</h4>
                                                        <div class="flex items-center mb-2">
                                                            <div class="flex">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    @if ($i <= $userRating->rating)
                                                                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                                                        </svg>
                                                                    @else
                                                                        <svg class="w-4 h-4 text-gray-300" viewBox="0 0 20 20">
                                                                            <path fill="currentColor" d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                                                        </svg>
                                                                    @endif
                                                                @endfor
                                                            </div>
                                                            <span class="ml-2 text-sm text-gray-600">
                                                                @if ($userRating->rating == 1)
                                                                    Rất không hài lòng
                                                                @elseif ($userRating->rating == 2)
                                                                    Không hài lòng
                                                                @elseif ($userRating->rating == 3)
                                                                    Bình thường
                                                                @elseif ($userRating->rating == 4)
                                                                    Hài lòng
                                                                @elseif ($userRating->rating == 5)
                                                                    Rất hài lòng
                                                                @endif
                                                            </span>
                                                        </div>
                                                        @if ($userRating->comment)
                                                            <p class="text-sm text-gray-700 italic">"{{ $userRating->comment }}"</p>
                                                        @endif
                                                        <div class="text-xs text-gray-500 mt-1">Đánh giá vào {{ $userRating->rated_at->format('d/m/Y H:i') }}</div>
                                                    </div>
                                                    @endif
                                                    
                                                    <!-- Nút đánh giá tài xế (chỉ hiển thị khi đơn hàng đã nhận và chưa đánh giá) -->
                                                    @if ($order->status == 'item_received' && !$userRating)
                                                        <div class="mt-3">
                                                            <button type="button" id="rate-driver-btn"
                                                                class="w-full py-2 px-4 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors flex items-center justify-center gap-2">
                                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                                </svg>
                                                                Đánh giá tài xế
                                                            </button>
                                                        </div>
                                                    @elseif ($order->status == 'item_received' && $userRating)
                                                        <div class="mt-3">
                                                            <button type="button" id="rate-driver-btn"
                                                                class="w-full py-2 px-4 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors flex items-center justify-center gap-2">
                                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                                </svg>
                                                                Chỉnh sửa đánh giá
                                                            </button>
                                                        </div>
                                                    @endif
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
                                    @endif
                                </div>
                            </div>


                        </div>
                        <div class="flex-shrink-0">
                            <div class="text-center">
                                <span class="inline-block px-4 py-2 text-sm font-medium rounded-lg"
                                    style="background-color: {{ $order->status_color }}; color: {{ $order->status_text_color }};">
                                    {{ $order->status_text }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">Trạng thái hiện tại</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Tracker -->
            @if ($currentStepIndex > -1)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Theo dõi đơn hàng</h3>
                        <div class="relative">
                            <!-- Steps -->
                            <div class="relative flex justify-between">
                                <!-- Progress Bar -->
                                <div class="absolute top-4 left-4 right-4 h-1 bg-gray-200 rounded-full">
                                    <div class="h-full bg-orange-500 rounded-full transition-all duration-500"
                                        style="width: {{ $currentStepIndex >= 0 ? (($currentStepIndex + 1) / count($progressSteps)) * 100 : 0 }}%">
                                    </div>
                                </div>
                                @foreach ($progressSteps as $key => $stepInfo)
                                    @php
                                        $stepIndex = array_search($key, array_keys($progressSteps));
                                        $isCompleted = $stepIndex <= $currentStepIndex;
                                        $isCurrent = $stepIndex === $currentStepIndex;
                                    @endphp
                                    <div class="flex flex-col items-center" data-step-key="{{ $key }}">
                                        <!-- Step Circle -->
                                        <div
                                            class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold z-10
                                            {{ $isCompleted ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                                            @if ($isCompleted)
                                                <i class="fas fa-check"></i>
                                            @else
                                                {{ $stepIndex + 1 }}
                                            @endif
                                        </div>

                                        <!-- Step Label -->
                                        <div class="mt-3 text-center">
                                            <span
                                                class="text-sm font-medium {{ $isCompleted ? 'text-orange-600' : 'text-gray-500' }}">
                                                {{ $stepInfo['text'] }}
                                            </span>
                                            @if ($isCurrent)
                                                <span class="text-xs text-blue-600 font-medium block mt-1">Hiện tại</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
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
                                            @if ($item->productVariant && $item->productVariant->product && $item->productVariant->product->primaryImage)
                                                <img src="{{ $item->productVariant->product->primaryImage->url }}"
                                                    alt="{{ $item->product_name_snapshot ?? $item->productVariant->product->name }}"
                                                    class="w-full h-full object-cover">
                                            @elseif ($item->combo && $item->combo->url)
                                                <img src="{{ $item->combo->url }}"
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
                                        
                                        {{-- Nút đánh giá cho từng sản phẩm --}}
                                        @if ($order->status === 'item_received')
                                            <div class="mt-2">
                                                @if ($item->productVariant && $item->productVariant->product)
                                                    <a href="{{ route('products.show', $item->productVariant->product->slug) }}#review-reply-form-container"
                                                        class="inline-flex items-center px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors font-medium text-xs">
                                                        <i class="fas fa-star mr-1"></i>Đánh giá
                                                    </a>
                                                @elseif ($item->combo)
                                                    <a href="{{ route('combos.show', $item->combo->slug) }}#review-reply-form-container"
                                                        class="inline-flex items-center px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors font-medium text-xs">
                                                        <i class="fas fa-star mr-1"></i>Đánh giá
                                                    </a>
                                                @endif
                                            </div>
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
                                            'cod' => 'Tiền mặt',
                                            'vnpay' => 'VNPay',
                                            'balance' => 'Số dư tài khoản',
                                        ];
                                        $paymentMethod = optional($order->payment)->payment_method ?? 'cod';
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

            <!-- Action Buttons -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Hành động</h3>
                    <div id="action-buttons" class="flex flex-wrap gap-3 justify-center">
                        @if ($order->status == 'awaiting_confirmation')
                            <form action="{{ route('customer.orders.updateStatus', $order) }}" method="POST"
                                class="cancel-order-form">
                                @csrf
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit"
                                    class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                                    <i class="fas fa-times mr-2"></i>Hủy đơn hàng
                                </button>
                            </form>
                        @elseif($order->status == 'delivered')
                            <a href="{{ route('customer.orders.updateStatus', $order) }}"
                                class="px-6 py-3 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg transition-colors font-medium inline-block">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Chưa nhận được hàng
                            </a>
                            <form class="receive-order-form" action="{{ route('customer.orders.updateStatus', $order) }}"
                                method="POST">
                                @csrf
                                <input type="hidden" name="status" value="item_received">
                                <button type="submit"
                                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                                    <i class="fas fa-check mr-2"></i>Xác nhận đã nhận hàng
                                </button>
                            </form>
                        @endif

                        @if (in_array($order->status, ['item_received', 'cancelled']))
                            <button
                                class="px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors font-medium">
                                <i class="fas fa-redo mr-2"></i>Đặt lại đơn hàng
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Product Detail Modal -->
    <div id="product-details-modal"
        class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 p-4 transition-opacity duration-300 opacity-0 pointer-events-none">
        <div id="modal-content"
            class="bg-white rounded-lg max-w-md w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300 shadow-xl">
        </div>
    </div>

    <!-- Action Confirmation Modal -->
    <div id="action-confirmation-modal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="relative mx-auto p-5 border w-96 bg-white rounded-lg shadow-xl">
            <!-- Close button -->
            <button type="button" id="action-close-btn" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="text-center">
                <h3 id="action-modal-title" class="text-xl font-medium text-gray-900">Hủy đơn hàng</h3>
                <p id="action-modal-message" class="text-sm text-gray-500 mt-2">Vui lòng cho chúng tôi biết lý do bạn muốn hủy đơn hàng này.</p>
                
                <!-- Phần chọn lý do hủy đơn -->
                <div id="cancel-reason-section" class="mt-4 text-left">
                    <p class="text-sm font-medium text-gray-700 mb-2">Lý do hủy đơn hàng</p>
                    <div class="space-y-2">
                        <div>
                            <input type="radio" id="reason-changed-mind" name="cancel_reason" value="Tôi đã thay đổi ý định" class="mr-2">
                            <label for="reason-changed-mind" class="text-sm text-gray-600">Tôi đã thay đổi ý định</label>
                        </div>
                        <div>
                            <input type="radio" id="reason-better-price" name="cancel_reason" value="Tìm thấy giá tốt hơn ở nơi khác" class="mr-2">
                            <label for="reason-better-price" class="text-sm text-gray-600">Tìm thấy giá tốt hơn ở nơi khác</label>
                        </div>
                        <div>
                            <input type="radio" id="reason-delivery-time" name="cancel_reason" value="Thời gian giao hàng quá lâu" class="mr-2">
                            <label for="reason-delivery-time" class="text-sm text-gray-600">Thời gian giao hàng quá lâu</label>
                        </div>
                        <div>
                            <input type="radio" id="reason-wrong-product" name="cancel_reason" value="Đặt nhầm sản phẩm" class="mr-2">
                            <label for="reason-wrong-product" class="text-sm text-gray-600">Đặt nhầm sản phẩm</label>
                        </div>
                        <div>
                            <input type="radio" id="reason-financial" name="cancel_reason" value="Vấn đề tài chính" class="mr-2">
                            <label for="reason-financial" class="text-sm text-gray-600">Vấn đề tài chính</label>
                        </div>
                        <div>
                            <input type="radio" id="reason-duplicate" name="cancel_reason" value="Đặt trùng đơn hàng" class="mr-2">
                            <label for="reason-duplicate" class="text-sm text-gray-600">Đặt trùng đơn hàng</label>
                        </div>
                        <div>
                            <input type="radio" id="reason-other" name="cancel_reason" value="Khác" class="mr-2">
                            <label for="reason-other" class="text-sm text-gray-600">Khác</label>
                        </div>
                        <div id="other-reason-container" class="mt-2">
                            <textarea id="other-reason-text" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-orange-500" placeholder="Nhập lý do cụ thể..."></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-between gap-3">
                    <button id="action-abort-btn"
                        class="px-4 py-2 bg-gray-200 text-gray-800 hover:bg-gray-300 rounded-lg transition-colors">Quay lại</button>
                    <button id="action-confirm-btn"
                        class="px-4 py-2 bg-red-500 text-white hover:bg-red-600 rounded-lg transition-colors">Xác nhận hủy</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast-message"
        class="fixed top-20 right-6 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50 hidden transition-all duration-300">
    </div>

    <!-- Modal đánh giá tài xế -->
    <div id="rate-driver-modal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="relative mx-auto p-5 border w-96 bg-white rounded-lg shadow-xl">
            <!-- Close button -->
            <button type="button" id="rate-driver-close-btn" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="text-center">
                <h3 class="text-xl font-medium text-gray-900">Đánh giá tài xế</h3>
                <p class="text-sm text-gray-500 mt-2">Hãy đánh giá trải nghiệm giao hàng của bạn với tài xế</p>
                
                <div class="mt-6">
                    <form id="driver-rating-form" method="POST" action="{{ route('driver.rating.submit', $order->id) }}">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <input type="hidden" name="driver_id" value="{{ $order->driver_id }}">
                        
                        <!-- Star Rating -->
                        <div class="mb-6">
                            <div class="flex justify-center space-x-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <label for="star-{{ $i }}" class="cursor-pointer">
                                        <input type="radio" id="star-{{ $i }}" name="rating" value="{{ $i }}" class="hidden">
                                        <svg class="w-10 h-10 star-rating" data-rating="{{ $i }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                    </label>
                                @endfor
                            </div>
                            <div class="text-sm text-gray-500 mt-2" id="rating-text">Chọn số sao để đánh giá</div>
                        </div>
                        
                        <!-- Comment -->
                        <div class="mb-4">
                            <label for="comment" class="block text-sm font-medium text-gray-700 mb-1 text-left">Nhận xét (không bắt buộc)</label>
                            <textarea id="comment" name="comment" rows="3" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-orange-500" placeholder="Chia sẻ trải nghiệm của bạn với tài xế..."></textarea>
                        </div>
                        
                        <!-- Anonymous Rating -->
                        <div class="mb-6 flex items-center">
                            <input type="checkbox" id="is_anonymous" name="is_anonymous" class="h-4 w-4 text-orange-500 focus:ring-orange-400 border-gray-300 rounded">
                            <label for="is_anonymous" class="ml-2 block text-sm text-gray-700">Đánh giá ẩn danh</label>
                        </div>
                        
                        <div class="flex justify-between gap-3">
                            <button type="button" id="rate-driver-cancel-btn" class="flex-1 py-2 px-4 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                                Hủy
                            </button>
                            <button type="submit" id="rate-driver-submit-btn" class="flex-1 py-2 px-4 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors" disabled>
                                Gửi đánh giá
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Script xử lý modal và real-time giữ nguyên --}}
    <script>
            // Định nghĩa hàm showToast để hiển thị thông báo
            function showToast(message, type = 'success') {
                const toast = document.getElementById('toast-message');
                toast.textContent = message;
                toast.classList.remove('bg-green-600', 'bg-red-600', 'hidden');
                if (type === 'success') {
                    toast.classList.add('bg-green-600');
                } else if (type === 'error') {
                    toast.classList.add('bg-red-600');
                }
                toast.classList.add('animate-slideIn'); // Thêm animation nếu có
                toast.classList.remove('hidden');
            
                setTimeout(() => {
                    toast.classList.add('hidden');
                    toast.classList.remove('animate-slideIn');
                }, 3000); // Ẩn sau 3 giây
            }
        
            // Biến toàn cục để lưu form và loại hành động
            let formToSubmit = null;
            let modalAction = null;
            let selectedRating = 0; // Biến lưu số sao đã chọn
            
            // Hàm cập nhật trạng thái đơn hàng bằng AJAX với hiệu ứng mượt mà
            function updateOrderStatusDisplay() {
                console.log('🔄 Updating order status via AJAX...');
                const orderId = {{ $order->id }};
                
                // Thêm loading indicator nhẹ
                const statusElements = document.querySelectorAll('.status-bar, .driver-info-container, .action-buttons-container');
                statusElements.forEach(el => {
                    if (el) {
                        el.classList.add('smooth-update', 'gpu-accelerated');
                        el.classList.add('updating');
                    }
                });
                
                fetch(`/customer/orders/${orderId}/partial`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(html => {
                    // Tạo một DOM parser để parse HTML response
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Hàm helper để cập nhật element với hiệu ứng mượt mà
                    function updateElementSmoothly(selector, newElement) {
                        const currentElement = document.querySelector(selector);
                        if (!currentElement || !newElement) return;
                        
                        // Kiểm tra xem nội dung có thay đổi không
                        if (currentElement.innerHTML === newElement.innerHTML) {
                            // Vẫn cần remove updating class
                            currentElement.classList.remove('updating');
                            return; // Không cập nhật nếu nội dung giống nhau
                        }
                        
                        // Sử dụng requestAnimationFrame để tối ưu hóa rendering
                        requestAnimationFrame(() => {
                            // Cập nhật nội dung
                            currentElement.innerHTML = newElement.innerHTML;
                            
                            // Remove updating class để trigger transition
                            currentElement.classList.remove('updating');
                            
                            // Cleanup sau khi animation hoàn thành
                            setTimeout(() => {
                                currentElement.classList.remove('gpu-accelerated');
                            }, 300);
                        });
                    }
                    
                    // Cập nhật thanh trạng thái với hiệu ứng mượt mà
                    const newStatusBar = doc.querySelector('.status-bar');
                    updateElementSmoothly('.status-bar', newStatusBar);
                    
                    // Cập nhật thông tin tài xế với hiệu ứng mượt mà
                    const newDriverInfo = doc.querySelector('.driver-info-container');
                    updateElementSmoothly('.driver-info-container', newDriverInfo);
                    
                    // Cập nhật các nút hành động với hiệu ứng mượt mà
                    const newActionButtons = doc.querySelector('.action-buttons-container');
                    const currentActionButtons = document.querySelector('.action-buttons-container');
                    if (newActionButtons && currentActionButtons) {
                        // Kiểm tra xem nội dung có thay đổi không
                        if (currentActionButtons.innerHTML !== newActionButtons.innerHTML) {
                            requestAnimationFrame(() => {
                                currentActionButtons.innerHTML = newActionButtons.innerHTML;
                                currentActionButtons.classList.remove('updating');
                                
                                // Gắn lại các sự kiện cho các nút mới
                                rebindActionButtons();
                                
                                setTimeout(() => {
                                    currentActionButtons.classList.remove('gpu-accelerated');
                                }, 300);
                            });
                        } else {
                            currentActionButtons.classList.remove('updating');
                        }
                    }
                    
                    console.log('✅ Order status updated successfully via AJAX');
                })
                .catch(error => {
                    console.error('❌ Error updating order status:', error);
                    
                    // Remove updating classes on error
                    statusElements.forEach(el => {
                        if (el) {
                            el.classList.remove('updating', 'gpu-accelerated');
                        }
                    });
                    
                    // Fallback to page reload if AJAX fails
                    console.log('🔄 Falling back to page reload...');
                    window.location.reload();
                });
            }
            
            // Hàm gắn lại sự kiện cho các nút hành động
            function rebindActionButtons() {
                // Gắn lại sự kiện cho nút hủy đơn hàng
                const cancelBtn = document.getElementById('cancelOrderButton');
                if (cancelBtn) {
                    cancelBtn.addEventListener('click', function() {
                        document.getElementById('cancelOrderModal').classList.remove('hidden');
                    });
                }
                
                // Gắn lại sự kiện cho nút xác nhận đã nhận hàng
                const receiveBtn = document.getElementById('receiveOrderButton');
                if (receiveBtn) {
                    receiveBtn.addEventListener('click', function() {
                        document.getElementById('receiveOrderModal').classList.remove('hidden');
                    });
                }
                
                // Gắn lại sự kiện cho nút đánh giá tài xế
                const rateBtn = document.getElementById('rateDriverButton');
                if (rateBtn) {
                    rateBtn.addEventListener('click', function() {
                        document.getElementById('rateDriverModal').classList.remove('hidden');
                    });
                }
            }
        
            // Định nghĩa hàm openActionModal
            function openActionModal(form, actionType) {
                const modal = document.getElementById('action-confirmation-modal');
                const title = document.getElementById('action-modal-title');
                const message = document.getElementById('action-modal-message');
                const confirmBtn = document.getElementById('action-confirm-btn');
                const abortBtn = document.getElementById('action-abort-btn');
                const cancelReasonSection = document.getElementById('cancel-reason-section');
                const otherReasonContainer = document.getElementById('other-reason-container');
                const otherReasonText = document.getElementById('other-reason-text');
                
                formToSubmit = form;
                modalAction = actionType;
            
                if (actionType === 'receive') {
                    title.textContent = 'Xác nhận đã nhận hàng';
                    message.textContent = 'Bạn xác nhận đã nhận được hàng? Vui lòng kiểm tra kỹ trước khi xác nhận.';
                    confirmBtn.textContent = 'Đã nhận';
                    confirmBtn.className = 'px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors';
                    cancelReasonSection.classList.add('hidden');
                } else if (actionType === 'cancel') {
                    title.textContent = 'Hủy đơn hàng';
                    message.textContent = 'Vui lòng cho chúng tôi biết lý do bạn muốn hủy đơn hàng này.';
                    confirmBtn.textContent = 'Xác nhận hủy';
                    confirmBtn.className = 'px-4 py-2 bg-red-500 text-white hover:bg-red-600 rounded-lg transition-colors';
                    abortBtn.textContent = 'Quay lại';
                    cancelReasonSection.classList.remove('hidden');
                    
                    // Reset radio buttons và textarea
                    document.querySelectorAll('input[name="cancel_reason"]').forEach(radio => {
                        radio.checked = false;
                    });
                    // Xóa nội dung textarea
                    otherReasonText.value = '';
                    // Ẩn container lý do khác
                    otherReasonContainer.classList.add('hidden');
                } else {
                    title.textContent = 'Xác nhận hành động';
                    message.textContent = 'Bạn có chắc chắn thực hiện thao tác này không?';
                    confirmBtn.textContent = 'Đồng ý';
                    confirmBtn.className = 'px-4 py-2 bg-orange-600 text-white hover:bg-orange-700 rounded-lg transition-colors';
                    cancelReasonSection.classList.add('hidden');
                }
            
            modal.classList.remove('hidden'); // Hiển thị modal
            }
        
            document.addEventListener('DOMContentLoaded', function() {
                // Xử lý hiển thị textarea khi chọn lý do "Khác"
                document.querySelectorAll('input[name="cancel_reason"]').forEach(radio => {
                    radio.addEventListener('change', function() {
                        const otherReasonContainer = document.getElementById('other-reason-container');
                        const otherReasonText = document.getElementById('other-reason-text');
                        if (this.value === 'Khác') {
                            otherReasonContainer.classList.remove('hidden');
                            otherReasonText.focus();
                        } else {
                            otherReasonContainer.classList.add('hidden');
                            otherReasonText.value = '';
                        }
                    });
                });
        
                const modal = document.getElementById('action-confirmation-modal');
                const confirmBtn = document.getElementById('action-confirm-btn');
                const abortBtn = document.getElementById('action-abort-btn');
                const closeBtn = document.getElementById('action-close-btn');
                const otherReasonContainer = document.getElementById('other-reason-container');
                const otherReasonText = document.getElementById('other-reason-text');
                
                // Xử lý nút đóng modal
                if (closeBtn) {
                    closeBtn.addEventListener('click', function() {
                        modal.classList.add('hidden');
                        // Reset radio buttons
                        document.querySelectorAll('input[name="cancel_reason"]').forEach(radio => {
                            radio.checked = false;
                        });
                        otherReasonContainer.classList.add('hidden');
                        otherReasonText.value = '';
                    });
                }
        
                // Xử lý khi nhấn nút "Đồng ý"
                if (confirmBtn) {
                    confirmBtn.addEventListener('click', function() {
                        if (formToSubmit) {
                            const form = formToSubmit;
                            
                            // Xử lý khi hủy đơn hàng
                            if (modalAction === 'cancel') {
                                const selectedReason = document.querySelector('input[name="cancel_reason"]:checked');
                                if (!selectedReason) {
                                    showToast('Vui lòng chọn lý do hủy đơn hàng', 'error');
                                    return;
                                }
                                
                                let reason = selectedReason.value;
                                if (reason === 'Khác') {
                                    const otherReasonValue = otherReasonText.value.trim();
                                    if (!otherReasonValue) {
                                        showToast('Vui lòng nhập lý do hủy đơn hàng', 'error');
                                        return;
                                    }
                                    reason = otherReasonValue;
                                }
                                
                                // Thêm lý do vào form data
                                const formData = new FormData(form);
                                formData.append('reason', reason);
                                
                                // Gửi yêu cầu AJAX
                                fetch(form.action, {
                                        method: form.method,
                                        body: formData,
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json'
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        modal.classList.add('hidden'); // Ẩn modal
                                        if (data.success) {
                                            showToast(data.message || 'Hủy đơn hàng thành công!', 'success');
                                            // Cập nhật UI bằng AJAX thay vì reload trang
                                            setTimeout(() => {
                                                updateOrderStatusDisplay();
                                            }, 1300);
                                        } else {
                                            showToast(data.message || 'Có lỗi xảy ra!', 'error');
                                        }
                                    })
                                    .catch(error => {
                                        modal.classList.add('hidden');
                                        showToast('Có lỗi khi kết nối!', 'error');
                                        console.error('Lỗi khi gửi yêu cầu:', error);
                                    });
                            } else {
                                // Xử lý các action khác (như receive)
                                const formData = new FormData(form);
                                
                                fetch(form.action, {
                                        method: form.method,
                                        body: formData,
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json'
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        modal.classList.add('hidden');
                                        if (data.success) {
                                            showToast(data.message || 'Đã nhận hàng thành công!', 'success');
                                            setTimeout(() => {
                                                updateOrderStatusDisplay();
                                            }, 1300);
                                        } else {
                                            showToast(data.message || 'Có lỗi xảy ra!', 'error');
                                        }
                                    })
                                    .catch(error => {
                                        modal.classList.add('hidden');
                                        showToast('Có lỗi khi kết nối!', 'error');
                                        console.error('Lỗi khi gửi yêu cầu:', error);
                                    });
                            }
                        }
                    });
                }
        
                // Xử lý khi nhấn nút "Không" hoặc click bên ngoài modal
                if (abortBtn) {
                    abortBtn.addEventListener('click', function() {
                        modal.classList.add('hidden');
                        // Reset radio buttons
                        document.querySelectorAll('input[name="cancel_reason"]').forEach(radio => {
                            radio.checked = false;
                        });
                        otherReasonContainer.classList.add('hidden');
                        otherReasonText.value = '';
                    });
                }
        
                // Xử lý nút "Đã nhận hàng"
                const receiveOrderButton = document.querySelector('.receive-order-form button[type="submit"]');
                if (receiveOrderButton) {
                    receiveOrderButton.addEventListener('click', function(event) {
                        event.preventDefault();
                        const form = this.closest('form');
                        openActionModal(form, 'receive');
                    });
                }
        
                // Xử lý nút "Hủy đơn hàng" (nếu có)
                const cancelOrderButton = document.querySelector('.cancel-order-form button[type="submit"]');
                if (cancelOrderButton) {
                    cancelOrderButton.addEventListener('click', function(event) {
                        event.preventDefault();
                        const form = this.closest('form');
                        openActionModal(form, 'cancel');
                    });
                }
                
                // Xử lý modal đánh giá tài xế
                const rateDriverBtn = document.getElementById('rate-driver-btn');
                const rateDriverModal = document.getElementById('rate-driver-modal');
                const rateDriverCloseBtn = document.getElementById('rate-driver-close-btn');
                const rateDriverCancelBtn = document.getElementById('rate-driver-cancel-btn');
                const rateDriverSubmitBtn = document.getElementById('rate-driver-submit-btn');
                const ratingForm = document.getElementById('driver-rating-form');
                const starRatings = document.querySelectorAll('.star-rating');
                const ratingText = document.getElementById('rating-text');
                
                // Mở modal đánh giá tài xế
                if (rateDriverBtn) {
                    rateDriverBtn.addEventListener('click', function() {
                        rateDriverModal.classList.remove('hidden');
                    });
                }
                
                // Đóng modal đánh giá tài xế
                if (rateDriverCloseBtn) {
                    rateDriverCloseBtn.addEventListener('click', function() {
                        rateDriverModal.classList.add('hidden');
                        resetRatingForm();
                    });
                }
                
                if (rateDriverCancelBtn) {
                    rateDriverCancelBtn.addEventListener('click', function() {
                        rateDriverModal.classList.add('hidden');
                        resetRatingForm();
                    });
                }
                
                // Xử lý chọn số sao
                if (starRatings.length > 0) {
                    starRatings.forEach(star => {
                        star.addEventListener('click', function() {
                            const rating = parseInt(this.dataset.rating);
                            selectedRating = rating;
                            updateStarDisplay(rating);
                            rateDriverSubmitBtn.disabled = false;
                        });
                        
                        // Hiệu ứng hover
                        star.addEventListener('mouseenter', function() {
                            const rating = parseInt(this.dataset.rating);
                            highlightStars(rating);
                        });
                        
                        star.addEventListener('mouseleave', function() {
                            resetStarHighlight();
                            if (selectedRating > 0) {
                                updateStarDisplay(selectedRating);
                            }
                        });
                    });
                }
                
                // Xử lý gửi form đánh giá
                if (ratingForm) {
                    ratingForm.addEventListener('submit', function(event) {
                        event.preventDefault();
                        
                        if (selectedRating === 0) {
                            showToast('Vui lòng chọn số sao đánh giá', 'error');
                            return;
                        }
                        
                        const formData = new FormData(this);
                        
                        fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            rateDriverModal.classList.add('hidden');
                            if (data.success) {
                                showToast(data.message || 'Đánh giá tài xế thành công!', 'success');
                                // Cập nhật UI bằng AJAX thay vì reload trang
                                setTimeout(() => {
                                    updateOrderStatusDisplay();
                                }, 1300);
                            } else {
                                showToast(data.message || 'Có lỗi xảy ra!', 'error');
                            }
                        })
                        .catch(error => {
                            rateDriverModal.classList.add('hidden');
                            showToast('Có lỗi khi gửi đánh giá!', 'error');
                            console.error('Lỗi khi gửi đánh giá:', error);
                        });
                    });
                }
                
                // Hàm cập nhật hiển thị sao
                function updateStarDisplay(rating) {
                    starRatings.forEach(star => {
                        const starRating = parseInt(star.dataset.rating);
                        if (starRating <= rating) {
                            star.classList.add('text-yellow-400');
                            star.classList.add('fill-current');
                        } else {
                            star.classList.remove('text-yellow-400');
                            star.classList.remove('fill-current');
                        }
                    });
                    
                    // Cập nhật text hiển thị
                    const ratingTexts = {
                        1: 'Rất không hài lòng',
                        2: 'Không hài lòng',
                        3: 'Bình thường',
                        4: 'Hài lòng',
                        5: 'Rất hài lòng'
                    };
                    
                    ratingText.textContent = ratingTexts[rating] || 'Chọn số sao để đánh giá';
                }
                
                // Hàm highlight sao khi hover
                function highlightStars(rating) {
                    starRatings.forEach(star => {
                        const starRating = parseInt(star.dataset.rating);
                        if (starRating <= rating) {
                            star.classList.add('text-yellow-400');
                        } else {
                            star.classList.remove('text-yellow-400');
                        }
                    });
                }
                
                // Hàm reset highlight sao
                function resetStarHighlight() {
                    starRatings.forEach(star => {
                        star.classList.remove('text-yellow-400');
                        star.classList.remove('fill-current');
                    });
                }
                
                // Hàm reset form đánh giá
                function resetRatingForm() {
                    if (ratingForm) {
                        ratingForm.reset();
                    }
                    selectedRating = 0;
                    resetStarHighlight();
                    ratingText.textContent = 'Chọn số sao để đánh giá';
                    rateDriverSubmitBtn.disabled = true;
                }
            });
        </script>
        
        <!-- Pusher Real-time Order Status Updates -->
        <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
        <script>
            Pusher.logToConsole = true;
            var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                encrypted: true,
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                }
            });
            
            // Add Pusher debugging
            pusher.connection.bind('connected', function() {
                console.log('✅ Pusher connected successfully');
            });

            pusher.connection.bind('error', function(err) {
                console.error('❌ Pusher connection error:', err);
            });

            pusher.connection.bind('disconnected', function() {
                console.log('⚠️ Pusher disconnected');
            });
            
            // Subscribe to order-specific channel
            var orderId = {{ $order->id }};
            var channel = pusher.subscribe('private-order.' + orderId);
            
            channel.bind('pusher:subscription_succeeded', function() {
                console.log('✅ Subscribed to order channel:', 'private-order.' + orderId);
            });
            
            channel.bind('pusher:subscription_error', function(error) {
                console.error('❌ Failed to subscribe to order channel:', 'private-order.' + orderId, error);
            });
            

            // Biến để debounce các cập nhật liên tiếp
            let updateTimeout = null;
            
            channel.bind('order-status-updated', function(data) {
                console.log('🔄 Pusher event order-status-updated received for order', orderId, data);
                
                // Show notification
                if (typeof showToast === 'function') {
                    showToast('🔄 Đơn hàng của bạn vừa được cập nhật trạng thái!', 'success');
                }
                
                // Debounce để tránh cập nhật quá nhiều lần liên tiếp
                if (updateTimeout) {
                    clearTimeout(updateTimeout);
                }
                
                updateTimeout = setTimeout(() => {
                    // Cập nhật trạng thái bằng AJAX thay vì reload trang
                    updateOrderStatusDisplay();
                }, 500); // Đợi 500ms trước khi cập nhật
            });
        </script>
@endpush
