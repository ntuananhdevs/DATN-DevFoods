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
                                            @if($order->driver_id && $order->driver)
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
                                <img src="{{ Storage::disk('s3')->url($item->productVariant->product->images->first()->img) }}"
                                    alt="{{ $item->product_name_snapshot ?? $item->productVariant->product->name }}"
                                    class="w-full h-full object-cover">
                            @elseif ($item->combo && $item->combo->image)
                                <img src="{{ Storage::disk('s3')->url($item->combo->image) }}"
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



            <!-- Driver Search Map -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Bản đồ tài xế</h3>
                        <div class="flex items-center space-x-4">
                            @if(in_array($order->status, ['awaiting_driver', 'driver_assigned', 'driver_confirmed', 'waiting_driver_pick_up', 'driver_picked_up', 'in_transit']))
                                <div class="flex items-center text-sm text-green-600">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                                    <span>Đang cập nhật vị trí</span>
                                </div>
                            @endif
                            <button id="refresh-map-btn" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i class="fas fa-sync-alt mr-1"></i>
                                Làm mới
                            </button>
                        </div>
                    </div>
                    
                    <!-- Map Container -->
                    <div id="driver-search-map" class="w-full h-96 rounded-lg border border-gray-200 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gray-100 flex items-center justify-center">
                            <div class="text-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500 mx-auto mb-2"></div>
                                <p class="text-gray-600 text-sm">Đang tải bản đồ...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Map Legend -->
                    <div class="mt-4 flex flex-wrap gap-4 text-sm">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-500 rounded-full mr-2"></div>
                            <span class="text-gray-600">Nhà hàng</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
                            <span class="text-gray-600">Khách hàng</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-gray-600">Tài xế khả dụng</span>
                        </div>
                        @if($order->driver_id)
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-yellow-500 rounded-full mr-2"></div>
                            <span class="text-gray-600">Tài xế được gán</span>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Driver Search Status -->
                    @if(in_array($order->status, ['awaiting_driver']))
                    <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                        <div class="flex items-center">
                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-orange-500 mr-3"></div>
                            <div>
                                <p class="text-orange-800 font-medium">Đang tìm kiếm tài xế...</p>
                                <p class="text-orange-600 text-sm mt-1">Hệ thống đang tự động tìm kiếm tài xế phù hợp trong khu vực</p>
                            </div>
                        </div>
                    </div>
                    @endif
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

                <!-- Order Action Buttons -->
                @if($order->status == 'awaiting_confirmation')
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-lg border border-blue-100">
                    <div class="p-6">
                        <div class="text-center mb-4">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Thao tác đơn hàng</h3>
                            <p class="text-sm text-gray-600">Vui lòng chọn hành động cho đơn hàng này</p>
                        </div>
                        <div class="flex gap-4 justify-center max-w-md mx-auto">
                            <button id="confirm-order-btn" 
                                    data-quick-action="confirm"
                                    data-order-id="{{ $order->id }}"
                                    class="group relative bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-3 px-8 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center min-w-[120px]">
                                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="relative z-10">Xác nhận</span>
                                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 rounded-xl transition-opacity duration-300"></div>
                            </button>
                            <button id="cancel-order-btn" 
                                    data-quick-action="cancel"
                                    data-order-id="{{ $order->id }}"
                                    class="group relative bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold py-3 px-8 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center min-w-[120px]">
                                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span class="relative z-10">Hủy bỏ</span>
                                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 rounded-xl transition-opacity duration-300"></div>
                            </button>
                        </div>
                    </div>
                </div>
                @endif

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
        
        /* Driver marker animations - REMOVED */
        
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
        

        
        /* Marker pulse effect - REMOVED */
        
        /* Enhanced driver list styling - REMOVED */
        .driver-item {
            border-left: 4px solid transparent;
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
        


        document.addEventListener('DOMContentLoaded', function() {
            // Initialize real-time order status updates
            if (typeof AdminOrderShowRealtime !== 'undefined') {
                const orderId = {{ $order->id }};
                window.orderShowRealtime = new AdminOrderShowRealtime(orderId);
            }
            
            // Initialize Driver Search Map
            initDriverSearchMap();
        });
        
        // Driver Search Map Implementation
        let driverSearchMap = null;
        let driverMarkers = [];
        let restaurantMarker = null;
        let customerMarker = null;
        let assignedDriverMarker = null;
        
        // Order coordinates - Global variable
        const orderData = {
            id: {{ $order->id }},
            status: '{{ $order->status }}',
            restaurant: {
                lat: {{ $order->branch->latitude ?? 21.0285 }},
                lng: {{ $order->branch->longitude ?? 105.8542 }},
                name: '{{ $order->branch->name ?? "Nhà hàng" }}'
            },
            customer: {
                lat: {{ $order->address->latitude ?? $order->guest_latitude ?? 21.0285 }},
                lng: {{ $order->address->longitude ?? $order->guest_longitude ?? 105.8542 }},
                address: '{{ $order->address->full_address ?? $order->guest_address ?? "Địa chỉ khách hàng" }}'
            },
            @if($order->driver_id)
            assignedDriver: {
                id: {{ $order->driver_id }},
                name: '{{ $order->driver->full_name ?? "Tài xế" }}',
                phone: '{{ $order->driver->phone_number ?? "" }}'
            }
            @else
            assignedDriver: null
            @endif
        };
        
        function initDriverSearchMap() {
            // Mapbox access token from config
            mapboxgl.accessToken = '{{ config('services.mapbox.access_token') }}';
            
            // Use global orderData
            
            // Initialize map
            driverSearchMap = new mapboxgl.Map({
                container: 'driver-search-map',
                style: 'mapbox://styles/mapbox/streets-v12',
                center: [orderData.restaurant.lng, orderData.restaurant.lat],
                zoom: 13,
                attributionControl: false
            });
            
            driverSearchMap.on('load', function() {
                // Add restaurant marker
                addRestaurantMarker(orderData.restaurant);
                
                // Add customer marker
                addCustomerMarker(orderData.customer);
                
                // Always show route from restaurant to customer
                drawRestaurantToCustomerRoute();
                
                // Load and display available drivers
                loadAvailableDrivers();
                
                // If there's an assigned driver, show it
                if (orderData.assignedDriver) {
                    loadAssignedDriver(orderData.assignedDriver.id);
                }
                
                // Fit map to show all markers
                fitMapToMarkers();
            });
            
            // Refresh button handler
            document.getElementById('refresh-map-btn').addEventListener('click', function() {
                refreshDriverLocations();
            });
            
            // Auto refresh every 30 seconds for active orders
            if (['awaiting_driver', 'driver_assigned', 'driver_confirmed', 'waiting_driver_pick_up', 'driver_picked_up', 'in_transit'].includes(orderData.status)) {
                setInterval(refreshDriverLocations, 30000);
            }
        }
        
        function addRestaurantMarker(restaurant) {
            const el = document.createElement('div');
            el.className = 'marker-pulse';
            el.innerHTML = '🏪';
            el.style.cssText = `
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                background-color: white;
                border: 2px solid #ef4444;
                border-radius: 50%;
                box-shadow: 0 2px 10px rgba(0,0,0,0.3);
                cursor: pointer;
            `;
            
            restaurantMarker = new mapboxgl.Marker(el)
                .setLngLat([restaurant.lng, restaurant.lat])
                .setPopup(new mapboxgl.Popup({ offset: 25 })
                    .setHTML(`
                        <div class="p-2">
                            <h3 class="font-bold text-red-600">🏪 ${restaurant.name}</h3>
                            <p class="text-sm text-gray-600">Điểm lấy hàng</p>
                        </div>
                    `))
                .addTo(driverSearchMap);
        }
        
        function addCustomerMarker(customer) {
            const el = document.createElement('div');
            el.className = 'marker-pulse';
            el.style.cssText = `
                width: 30px;
                height: 30px;
                background-color: #3b82f6;
                border: 3px solid white;
                border-radius: 50%;
                box-shadow: 0 2px 10px rgba(0,0,0,0.3);
                cursor: pointer;
            `;
            
            customerMarker = new mapboxgl.Marker(el)
                .setLngLat([customer.lng, customer.lat])
                .setPopup(new mapboxgl.Popup({ offset: 25 })
                    .setHTML(`
                        <div class="p-2">
                            <h3 class="font-bold text-blue-600">📍 Khách hàng</h3>
                            <p class="text-sm text-gray-600">${customer.address}</p>
                        </div>
                    `))
                .addTo(driverSearchMap);
        }
        
        function loadAvailableDrivers() {
            fetch(`/branch/orders/{{ $order->id }}/available-drivers`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayAvailableDrivers(data.drivers);
                    }
                })
                .catch(error => {
                    console.error('Error loading drivers:', error);
                });
        }
        
        function loadAssignedDriver(driverId) {
            fetch(`/branch/orders/{{ $order->id }}/assigned-driver/${driverId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.driver) {
                        displayAssignedDriver(data.driver);
                    }
                })
                .catch(error => {
                    console.error('Error loading assigned driver:', error);
                });
        }
        
        function displayAvailableDrivers(drivers) {
            // Clear existing driver markers
            driverMarkers.forEach(marker => marker.remove());
            driverMarkers = [];
            
            // Hide loading if drivers are found and no assigned driver
            if (drivers.length > 0 && !orderData.assignedDriver) {
                hideDriverSearchLoading();
            }
            
            drivers.forEach(driver => {
                // API already returns current location from driver_locations table
                const lat = driver.latitude;
                const lng = driver.longitude;
                
                if (lat && lng) {
                    const el = document.createElement('div');
                    el.style.cssText = `
                        width: 25px;
                        height: 25px;
                        background-color: #10b981;
                        border: 2px solid white;
                        border-radius: 50%;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                        cursor: pointer;

                    `;
                    

                    
                    const marker = new mapboxgl.Marker(el)
                        .setLngLat([lng, lat])
                        .setPopup(new mapboxgl.Popup({ offset: 25 })
                            .setHTML(`
                                <div class="p-3">
                                    <h3 class="font-bold text-green-600">🚗 ${driver.full_name}</h3>
                                    <p class="text-sm text-gray-600">📞 ${driver.phone_number || 'N/A'}</p>
                                    <p class="text-sm text-gray-600">📍 Cách ${driver.distance ? driver.distance.toFixed(1) : 'N/A'} km</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        ${driver.is_available ? '✅ Sẵn sàng' : '⏳ Bận'}
                                    </p>
                                    <p class="text-xs text-blue-600 mt-1">
                                        📍 Vị trí hiện tại
                                    </p>
                                </div>
                            `))
                        .addTo(driverSearchMap);
                    
                    driverMarkers.push(marker);
                }
            });
        }
        
        function displayAssignedDriver(driver) {
            if (assignedDriverMarker) {
                assignedDriverMarker.remove();
            }
            
            // Clear delivery route if exists (keep restaurant-customer route)
            if (driverSearchMap.getSource('delivery-route')) {
                driverSearchMap.removeLayer('delivery-route');
                driverSearchMap.removeSource('delivery-route');
            }
            
            // Hide driver search loading when assigned driver is displayed
            hideDriverSearchLoading();
            
            // API already returns current location from driver_locations table
            const lat = driver.latitude;
            const lng = driver.longitude;
            
            if (lat && lng) {
                const el = document.createElement('div');
                el.style.cssText = `
                    width: 35px;
                    height: 35px;
                    background-color: #f59e0b;
                    border: 3px solid white;
                    border-radius: 50%;
                    box-shadow: 0 3px 12px rgba(0,0,0,0.3);
                    cursor: pointer;
                    position: relative;
                `;
                
                // Add crown icon for assigned driver
                const crown = document.createElement('div');
                crown.innerHTML = '👑';
                crown.style.cssText = `
                    position: absolute;
                    top: -8px;
                    right: -8px;
                    font-size: 12px;
                `;
                el.appendChild(crown);
                
                assignedDriverMarker = new mapboxgl.Marker(el)
                    .setLngLat([lng, lat])
                    .setPopup(new mapboxgl.Popup({ offset: 25 })
                        .setHTML(`
                            <div class="p-3">
                                <h3 class="font-bold text-yellow-600">👑 ${driver.full_name}</h3>
                                <p class="text-sm text-gray-600">📞 ${driver.phone_number || 'N/A'}</p>
                                <p class="text-sm text-gray-600">📍 Cách ${driver.distance ? driver.distance.toFixed(1) : 'N/A'} km</p>
                                <p class="text-xs text-yellow-600 mt-1 font-medium">
                                    ⭐ Tài xế được gán
                                </p>
                                <p class="text-xs text-blue-600 mt-1">
                                    📍 Vị trí hiện tại
                                </p>
                            </div>
                        `))
                    .addTo(driverSearchMap);
                
                // Draw route: Driver -> Restaurant -> Customer
                drawDeliveryRoute(lng, lat);
            }
        }
        
        function fitMapToMarkers() {
            const bounds = new mapboxgl.LngLatBounds();
            
            // Add restaurant and customer to bounds
            if (restaurantMarker) bounds.extend(restaurantMarker.getLngLat());
            if (customerMarker) bounds.extend(customerMarker.getLngLat());
            
            // Add driver markers to bounds
            driverMarkers.forEach(marker => {
                bounds.extend(marker.getLngLat());
            });
            
            if (assignedDriverMarker) {
                bounds.extend(assignedDriverMarker.getLngLat());
            }
            
            if (!bounds.isEmpty()) {
                driverSearchMap.fitBounds(bounds, {
                    padding: 50,
                    maxZoom: 15
                });
            }
        }
        
        function refreshDriverLocations() {
            const refreshBtn = document.getElementById('refresh-map-btn');
            const icon = refreshBtn.querySelector('i');
            
            // Add spinning animation
            icon.classList.add('fa-spin');
            
            // Reload drivers
            loadAvailableDrivers();
            
            // If there's an assigned driver, reload its location
            @if($order->driver_id)
            loadAssignedDriver({{ $order->driver_id }});
            @endif
            
            // Remove spinning animation after 1 second
            setTimeout(() => {
                icon.classList.remove('fa-spin');
            }, 1000);
        }
        
        function hideDriverSearchLoading() {
            const loadingElement = document.querySelector('.bg-orange-50');
            if (loadingElement) {
                loadingElement.style.display = 'none';
            }
        }
        
        function drawRestaurantToCustomerRoute() {
            const restaurantLng = orderData.restaurant.lng;
            const restaurantLat = orderData.restaurant.lat;
            const customerLng = orderData.customer.lng;
            const customerLat = orderData.customer.lat;
            
            // Remove existing route if any
            if (driverSearchMap.getLayer('restaurant-customer-route')) {
                driverSearchMap.removeLayer('restaurant-customer-route');
            }
            if (driverSearchMap.getSource('restaurant-customer-route')) {
                driverSearchMap.removeSource('restaurant-customer-route');
            }
            
            // Create waypoints for the route: Restaurant -> Customer
            const waypoints = `${restaurantLng},${restaurantLat};${customerLng},${customerLat}`;
            
            // Use Mapbox Directions API to get actual route
            const directionsUrl = `https://api.mapbox.com/directions/v5/mapbox/driving/${waypoints}?geometries=geojson&access_token=${mapboxgl.accessToken}`;
            
            fetch(directionsUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.routes && data.routes.length > 0) {
                        const route = data.routes[0];
                        
                        // Add route source and layer
                        driverSearchMap.addSource('restaurant-customer-route', {
                            'type': 'geojson',
                            'data': {
                                'type': 'Feature',
                                'properties': {},
                                'geometry': route.geometry
                            }
                        });
                        
                        driverSearchMap.addLayer({
                            'id': 'restaurant-customer-route',
                            'type': 'line',
                            'source': 'restaurant-customer-route',
                            'layout': {
                                'line-join': 'round',
                                'line-cap': 'round'
                            },
                            'paint': {
                                'line-color': '#10b981',
                                'line-width': 4,
                                'line-opacity': 0.8
                            }
                        });
                    } else {
                        console.error('No route found');
                        // Fallback to straight line if API fails
                        drawStraightLineRoute(restaurantLng, restaurantLat, customerLng, customerLat);
                    }
                })
                .catch(error => {
                    console.error('Error fetching route:', error);
                    // Fallback to straight line if API fails
                    drawStraightLineRoute(restaurantLng, restaurantLat, customerLng, customerLat);
                });
        }
        
        function drawStraightLineRoute(restaurantLng, restaurantLat, customerLng, customerLat) {
            const routeCoordinates = [
                [restaurantLng, restaurantLat],
                [customerLng, customerLat]
            ];
            
            driverSearchMap.addSource('restaurant-customer-route', {
                'type': 'geojson',
                'data': {
                    'type': 'Feature',
                    'properties': {},
                    'geometry': {
                        'type': 'LineString',
                        'coordinates': routeCoordinates
                    }
                }
            });
            
            driverSearchMap.addLayer({
                'id': 'restaurant-customer-route',
                'type': 'line',
                'source': 'restaurant-customer-route',
                'layout': {
                    'line-join': 'round',
                    'line-cap': 'round'
                },
                'paint': {
                    'line-color': '#10b981',
                    'line-width': 4,
                    'line-opacity': 0.8
                }
            });
        }
        
        function drawDeliveryRoute(driverLng, driverLat) {
            const restaurantLng = orderData.restaurant.lng;
            const restaurantLat = orderData.restaurant.lat;
            const customerLng = orderData.customer.lng;
            const customerLat = orderData.customer.lat;
            
            // Remove existing delivery route if any
            if (driverSearchMap.getLayer('delivery-route')) {
                driverSearchMap.removeLayer('delivery-route');
            }
            if (driverSearchMap.getSource('delivery-route')) {
                driverSearchMap.removeSource('delivery-route');
            }
            
            // Create waypoints for the route: Driver -> Restaurant -> Customer
            const waypoints = `${driverLng},${driverLat};${restaurantLng},${restaurantLat};${customerLng},${customerLat}`;
            
            // Use Mapbox Directions API to get actual route
            const directionsUrl = `https://api.mapbox.com/directions/v5/mapbox/driving/${waypoints}?geometries=geojson&access_token=${mapboxgl.accessToken}`;
            
            fetch(directionsUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.routes && data.routes.length > 0) {
                        const route = data.routes[0];
                        
                        // Add delivery route source and layer
                        driverSearchMap.addSource('delivery-route', {
                            'type': 'geojson',
                            'data': {
                                'type': 'Feature',
                                'properties': {},
                                'geometry': route.geometry
                            }
                        });
                        
                        driverSearchMap.addLayer({
                            'id': 'delivery-route',
                            'type': 'line',
                            'source': 'delivery-route',
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
                        
                        // Fit map to show the route
                        const coordinates = route.geometry.coordinates;
                        const bounds = coordinates.reduce((bounds, coord) => {
                            return bounds.extend(coord);
                        }, new mapboxgl.LngLatBounds(coordinates[0], coordinates[0]));
                        
                        driverSearchMap.fitBounds(bounds, {
                            padding: 50
                        });
                    } else {
                        console.error('No route found');
                    }
                })
                .catch(error => {
                    console.error('Error fetching route:', error);
                    // Fallback to straight line if API fails
                    const routeCoordinates = [
                        [driverLng, driverLat],
                        [restaurantLng, restaurantLat],
                        [customerLng, customerLat]
                    ];
                    
                    driverSearchMap.addSource('delivery-route', {
                        'type': 'geojson',
                        'data': {
                            'type': 'Feature',
                            'properties': {},
                            'geometry': {
                                'type': 'LineString',
                                'coordinates': routeCoordinates
                            }
                        }
                    });
                    
                    driverSearchMap.addLayer({
                        'id': 'delivery-route',
                        'type': 'line',
                        'source': 'delivery-route',
                        'layout': {
                            'line-join': 'round',
                            'line-cap': 'round'
                        },
                        'paint': {
                            'line-color': '#ef4444',
                            'line-width': 3,
                            'line-opacity': 0.7
                        }
                    });
                });
        }
        
        // Handle order action buttons
        document.addEventListener('DOMContentLoaded', function() {
            const confirmBtn = document.getElementById('confirm-order-btn');
            const cancelBtn = document.getElementById('cancel-order-btn');
            
            if (confirmBtn) {
                confirmBtn.addEventListener('click', function() {
                    const orderId = this.getAttribute('data-order-id');
                    confirmOrder(orderId);
                });
            }
            
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    const orderId = this.getAttribute('data-order-id');
                    // Show cancel modal or directly cancel
                    if (typeof openCancelModal === 'function') {
                        openCancelModal(orderId);
                    } else {
                        cancelOrder(orderId);
                    }
                });
            }
        });
        
        function confirmOrder(orderId) {
            if (confirm('Bạn có chắc chắn muốn xác nhận đơn hàng này?')) {
                fetch(`/branch/orders/${orderId}/confirm`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi khi kết nối!');
                });
            }
        }
        
        function cancelOrder(orderId) {
            if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
                fetch(`/branch/orders/${orderId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        reason: 'Hủy từ giao diện quản lý'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi khi kết nối!');
                });
            }
        }

    </script>
    <script src="{{ asset('js/branch/orders-realtime-simple.js') }}" defer></script>
@endpush
