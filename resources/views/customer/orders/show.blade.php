@extends('layouts.customer.fullLayoutMaster')

@section('title', 'Chi tiết đơn hàng #' . ($order->order_code ?? $order->id))

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
            'awaiting_driver' => 'driver_picked_up',
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
                        @elseif($order->status == 'item_received')
                            <a href="#"
                                class="px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors font-medium inline-block">
                                <i class="fas fa-star mr-2"></i>Đánh giá đơn hàng
                            </a>
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
            <div class="mt-3 text-center">
                <div id="modal-icon-container"
                    class="mx-auto flex items-center justify-center h-12 w-12 bg-red-100 rounded-full">
                    <i id="modal-icon" class="fas fa-times text-red-600 text-xl"></i>
                </div>
                <h3 id="action-modal-title" class="text-lg font-medium text-gray-900 mt-4">Xác nhận hành động</h3>
                <div class="mt-2 px-7 py-3">
                    <p id="action-modal-message" class="text-sm text-gray-500">Bạn có chắc chắn thực hiện thao tác này
                        không?</p>
                </div>
                <div class="items-center px-4 py-3 flex gap-3">
                    <button id="action-abort-btn"
                        class="w-full px-4 py-2 bg-gray-200 text-gray-800 hover:bg-gray-300 rounded-lg transition-colors">Không</button>
                    <button id="action-confirm-btn"
                        class="w-full px-4 py-2 bg-orange-600 text-white hover:bg-orange-700 rounded-lg transition-colors">Đồng
                        ý</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast-message"
        class="fixed top-20 right-6 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50 hidden transition-all duration-300">
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

        // Định nghĩa hàm openActionModal
        function openActionModal(form, actionType) {
            const modal = document.getElementById('action-confirmation-modal');
            const title = document.getElementById('action-modal-title');
            const message = document.getElementById('action-modal-message');
            const confirmBtn = document.getElementById('action-confirm-btn');
            const abortBtn = document.getElementById('action-abort-btn');
            const modalIconContainer = document.getElementById('modal-icon-container');
            const modalIcon = document.getElementById('modal-icon');

            // Reset icon và màu nền của icon
            modalIcon.className = ''; // Xóa tất cả các class
            modalIconContainer.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full';

            if (actionType === 'receive') {
                title.textContent = 'Xác nhận đã nhận hàng';
                message.textContent =
                    'Bạn có chắc chắn muốn xác nhận đã nhận đơn hàng này không? Hành động này không thể hoàn tác.';
                confirmBtn.textContent = 'Xác nhận';
                confirmBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
                confirmBtn.classList.add('bg-orange-600', 'hover:bg-orange-700');
                modalIconContainer.classList.add('bg-green-100');
                modalIcon.classList.add('fas', 'fa-check-circle', 'text-green-600', 'text-xl');
            } else if (actionType === 'cancel') {
                title.textContent = 'Xác nhận hủy đơn hàng';
                message.textContent = 'Bạn có chắc chắn muốn hủy đơn hàng này không? Hành động này không thể hoàn tác.';
                confirmBtn.textContent = 'Hủy đơn';
                confirmBtn.classList.remove('bg-orange-600', 'hover:bg-orange-700');
                confirmBtn.classList.add('bg-red-600', 'hover:bg-red-700');
                modalIconContainer.classList.add('bg-red-100');
                modalIcon.classList.add('fas', 'fa-times-circle', 'text-red-600', 'text-xl');
            } else {
                title.textContent = 'Xác nhận hành động';
                message.textContent = 'Bạn có chắc chắn thực hiện thao tác này không?';
                confirmBtn.textContent = 'Đồng ý';
                confirmBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
                confirmBtn.classList.add('bg-orange-600', 'hover:bg-orange-700');
                modalIconContainer.classList.add('bg-gray-100');
                modalIcon.classList.add('fas', 'fa-question-circle', 'text-gray-600', 'text-xl');
            }

            modal.classList.remove('hidden'); // Hiển thị modal

            // Xử lý khi nhấn nút "Đồng ý"
            confirmBtn.onclick = function() {
                modal.classList.add('hidden'); // Ẩn modal ngay lập tức

                // Lấy dữ liệu form
                const formData = new FormData(form);

                // Gửi yêu cầu AJAX
                fetch(form.action, {
                        method: form.method,
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest', // Để backend biết đây là AJAX request
                            'Accept': 'application/json' // Yêu cầu phản hồi JSON
                        }
                    })
                    .then(response => response.json()) // Chuyển phản hồi sang JSON
                    .then(data => {
                        if (data.success) {
                            showToast(data.message, 'success');
                            // Cập nhật UI của trạng thái đơn hàng
                            const statusElement = document.getElementById(
                                'order-status-display'); // Thêm ID này vào span hiển thị trạng thái
                            if (statusElement && data.order) {
                                statusElement.textContent = data.order.status_text;
                                statusElement.style.backgroundColor = data.order.status_color;
                                statusElement.style.color = data.order.status_text_color;
                                const statusIcon = statusElement.querySelector('i');
                                if (statusIcon) {
                                    statusIcon.className = ''; // Xóa class cũ
                                    statusIcon.classList.add(...data.order.status_icon.split(
                                        ' ')); // Thêm class mới
                                }

                                // Vô hiệu hóa hoặc ẩn form "Đã nhận hàng" hoặc "Hủy đơn hàng"
                                if (actionType === 'receive') {
                                    form.remove(); // Xóa form "Đã nhận hàng" sau khi xác nhận
                                    // Hoặc: form.style.display = 'none';
                                } else if (actionType === 'cancel') {
                                    form.remove(); // Xóa form "Hủy đơn hàng"
                                }

                                // Nếu có timeline trạng thái, bạn có thể cân nhắc cập nhật nó qua AJAX cũng
                                // Tuy nhiên, việc này phức tạp hơn và có thể yêu cầu partial reload hoặc logic render lại phức tạp.
                                // Tạm thời, chúng ta chỉ cập nhật phần trạng thái chính.
                            }
                        } else {
                            showToast(data.message, 'error');
                        }
                    })
                    .then(() => {
                        // Tự động tải lại trang sau khi hoàn thành
                        location.reload(); // Nếu bạn muốn tải lại toàn bộ trang
                    })
                    .catch(error => {
                        console.error('Lỗi khi gửi yêu cầu:', error);
                        showToast('Đã xảy ra lỗi khi thực hiện thao tác.', 'error');
                    });
            };

            // Xử lý khi nhấn nút "Không" hoặc click bên ngoài modal
            abortBtn.onclick = function() {
                modal.classList.add('hidden'); // Ẩn modal
            };

            // Ẩn modal khi nhấn phím Esc
            document.onkeydown = function(event) {
                if (event.key === 'Escape') {
                    modal.classList.add('hidden');
                }
            };
        }

        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
@endpush
