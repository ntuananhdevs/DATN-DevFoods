@extends('layouts.admin.contentLayoutMaster')

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

@push('scripts')
    {{-- Include Pusher for real-time updates --}}
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    
    {{-- Include order show real-time script --}}
    <script src="{{ asset('js/admin/order-show-realtime.js') }}"></script>
    
    <script>
        // Set Pusher configuration for real-time updates
        window.pusherKey = '{{ config('broadcasting.connections.pusher.key') }}';
        window.pusherCluster = '{{ config('broadcasting.connections.pusher.options.cluster') }}';

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize real-time order status updates
            if (typeof AdminOrderShowRealtime !== 'undefined') {
                const orderId = {{ $order->id }};
                window.orderShowRealtime = new AdminOrderShowRealtime(orderId);
            }
        });
    </script>
@endpush
