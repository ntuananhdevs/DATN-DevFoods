@extends('layouts.customer.fullLayoutMaster')

@section('title', 'Chi tiết đơn hàng #' . ($order->order_code ?? $order->id))

@section('content')
    @php
        // =================================================================
        // LOGIC XỬ LÝ THANH TRẠNG THÁI (Đã cập nhật theo quy trình mới)
        // =================================================================
        // 1. Định nghĩa các bước chính trên thanh tiến trình
        $progressSteps = [
            'confirmed' => ['text' => 'Xác nhận', 'icon' => 'fas fa-check-circle'],
            'driver_picked_up' => ['text' => 'Lấy hàng', 'icon' => 'fas fa-shopping-bag'],
            'in_transit' => ['text' => 'Đang giao', 'icon' => 'fas fa-truck'],
            'item_received' => ['text' => 'Đã nhận', 'icon' => 'fas fa-box-check'],
        ];

        // 2. Ánh xạ các trạng thái chi tiết vào các bước chính
        $statusMapToStep = [
            'awaiting_confirmation' => 'confirmed',
            'confirmed' => 'confirmed',
            'awaiting_driver' => 'driver_picked_up',
            'driver_picked_up' => 'driver_picked_up',
            'in_transit' => 'in_transit',
            'delivered' => 'item_received',
            'item_received' => 'item_received',
        ];

        // 3. Xác định bước hiện tại
        $currentStepKey = $statusMapToStep[$order->status] ?? null;
        $currentStepIndex = $currentStepKey ? array_search($currentStepKey, array_keys($progressSteps)) : -1;

        // 4. Xử lý trường hợp đặc biệt (đã hủy)
        if ($order->status == 'cancelled') {
            $currentStepIndex = -1; // -1 để ẩn thanh tiến trình
        }
    @endphp

    <div class=" bg-gradient-to-br from-orange-500 via-red-500 to-pink-500 py-12 relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-32 translate-x-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-24 -translate-x-24"></div>
        <div class="container-ft mx-auto px-4 relative z-10">
            <div class="flex items-center mb-4">
                <a href="{{ route('customer.orders.index') }}"
                    class="text-white hover:text-white/80 mr-4 p-2 rounded-full hover:bg-white/10 transition-all duration-200">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Chi tiết đơn hàng</h1>
                    <p class="text-white/90 text-lg">#{{ $order->order_code ?? $order->id }}</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-4 text-white/90">
                <div class="flex items-center bg-white/10 px-3 py-1 rounded-full">
                    <i class="far fa-calendar-alt mr-2"></i>
                    <span>{{ $order->order_date->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex items-center bg-white/10 px-3 py-1 rounded-full">
                    <i class="fas fa-store mr-2"></i>
                    <span>{{ $order->branch->name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container-ft mx-auto px-4 py-8 ">
        <div class="flex flex-col gap-8">
            <section class="mb-10">
                <div id="order-details-card"
                    class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden backdrop-blur-sm"
                    data-order-id="{{ $order->id }}">
                    <div class="p-8 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex flex-col lg:flex-row justify-between lg:items-start gap-6">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-receipt text-orange-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900">Đơn hàng
                                            #{{ $order->order_code ?? $order->id }}</h2>
                                        <p class="text-gray-500">{{ $order->orderItems->count() }} sản phẩm</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex items-center gap-3 p-3 bg-white rounded-lg border">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="far fa-calendar-alt text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide">Đặt lúc</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ $order->order_date->format('H:i - d/m/Y') }}</p>
                                        </div>
                                    </div>
                                    @if (in_array($order->status, ['delivered', 'item_received']))
                                        <div
                                            class="flex items-center gap-3 p-3 bg-green-50 rounded-lg border border-green-200">
                                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-check-circle text-green-600"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-green-600 uppercase tracking-wide">Hoàn thành</p>
                                                <p class="font-semibold text-green-800">
                                                    {{ optional($order->actual_delivery_time)->format('H:i - d/m/Y') }}</p>
                                            </div>
                                        </div>
                                    @elseif($order->status != 'cancelled')
                                        <div
                                            class="flex items-center gap-3 p-3 bg-amber-50 rounded-lg border border-amber-200">
                                            <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                                <i class="far fa-clock text-amber-600"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-amber-600 uppercase tracking-wide">Dự kiến giao</p>
                                                <p class="font-semibold text-amber-800">
                                                    {{ $order->estimated_delivery_time ? date('H:i', strtotime($order->estimated_delivery_time)) : 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div id="order-status-badge" class="flex-shrink-0">
                                <div class="text-center">
                                    <span
                                        class="inline-flex items-center gap-2 text-sm font-semibold px-6 py-3 rounded-xl shadow-sm border"
                                        style="background-color: {{ $order->status_color }}; color: {{ $order->status_text_color }}; border-color: {{ $order->status_color }}20;">
                                        <i class="fas fa-circle text-xs"></i>
                                        {{ $order->status_text }}
                                    </span>
                                    <p class="text-xs text-gray-500 mt-2">Trạng thái đơn hàng</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-8 space-y-8">
                        {{-- Thanh trạng thái (Progress Tracker) --}}
                        @if ($currentStepIndex > -1)
                            <div id="progress-tracker" class="mb-8">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-route text-blue-600"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900">Theo dõi đơn hàng</h3>
                                </div>
                                <div class="relative bg-gray-50 rounded-2xl p-6">
                                    <!-- Progress Line -->
                                    <div class="absolute top-10 left-12 right-12 h-1 bg-gray-200 rounded-full">
                                        <div class="h-full bg-gradient-to-r from-orange-400 to-orange-600 rounded-full transition-all duration-1000 ease-out"
                                            style="width: {{ $currentStepIndex >= 0 ? ($currentStepIndex / (count($progressSteps) - 1)) * 100 : 0 }}%">
                                        </div>
                                    </div>

                                    <div class="relative flex justify-between">
                                        @foreach ($progressSteps as $key => $stepInfo)
                                            @php
                                                $stepIndex = array_search($key, array_keys($progressSteps));
                                                $isCompleted = $stepIndex <= $currentStepIndex;
                                                $isCurrent = $stepIndex === $currentStepIndex;
                                            @endphp
                                            <div class="flex flex-col items-center relative z-10"
                                                data-step-key="{{ $key }}">
                                                <div class="relative">
                                                    <div
                                                        class="w-12 h-12 rounded-2xl flex items-center justify-center text-sm font-bold transition-all duration-500 shadow-lg
                                                        {{ $isCompleted ? 'bg-gradient-to-br from-orange-400 to-orange-600 text-white transform scale-110' : 'bg-white text-gray-400 border-2 border-gray-200' }}
                                                        {{ $isCurrent ? 'ring-4 ring-orange-200 animate-pulse' : '' }}">
                                                        <i
                                                            class="{{ $stepInfo['icon'] }} {{ $isCompleted ? 'text-white' : 'text-gray-400' }}"></i>
                                                    </div>
                                                    @if ($isCurrent)
                                                        <div
                                                            class="absolute -top-1 -right-1 w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center">
                                                            <div class="w-2 h-2 bg-white rounded-full animate-ping"></div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="mt-3 text-center max-w-20">
                                                    <span
                                                        class="text-sm font-semibold block {{ $isCompleted ? 'text-orange-700' : 'text-gray-500' }}">{{ $stepInfo['text'] }}</span>
                                                    @if ($isCurrent)
                                                        <span class="text-xs text-blue-600 font-medium mt-1 block">Hiện
                                                            tại</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <hr class="border-gray-100" />
                        @endif

                        {{-- Các khối thông tin chi tiết --}}
                        <div class="space-y-6">
                            {{-- Địa chỉ giao hàng (giống mẫu) --}}
                            <div>
                                <h3 class="font-semibold mb-3 flex items-center text-gray-800"><i
                                        class="fas fa-map-marker-alt mr-2 h-5 w-5 text-orange-500"></i>Địa chỉ giao hàng
                                </h3>
                                <div class="bg-gray-50 p-4 rounded-lg text-sm space-y-1">
                                    <p class="font-medium text-gray-800">{{ $order->displayRecipientName }}</p>
                                    <p class="text-gray-600">{{ $order->displayDeliveryPhone }}</p>
                                    <p class="text-gray-600 mt-1">{{ $order->displayFullDeliveryAddress }}</p>
                                </div>
                            </div>

                            {{-- Thông tin tài xế (giống mẫu) --}}
                            @if ($order->driver)
                                <hr class="border-gray-100" /><!-- Separator -->
                                <div>
                                    <h3 class="font-semibold mb-3 flex items-center text-gray-800"><i
                                            class="fas fa-motorcycle mr-2 h-5 w-5 text-orange-500"></i>Thông tin tài xế</h3>
                                    <div class="bg-gray-50 p-4 rounded-lg text-sm">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="flex items-center space-x-3">
                                                <img src="{{ asset('images/default-avatar.png') }}"
                                                    class="w-12 h-12 rounded-full object-cover" />
                                                <div>
                                                    <p class="font-medium text-gray-800">{{ $order->driver->full_name }}
                                                    </p>
                                                    <div class="flex items-center text-gray-600"><i
                                                            class="fas fa-star text-yellow-400 mr-1"></i><span>{{ $order->driver->rating ?? 'N/A' }}
                                                            sao</span></div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <span class="text-gray-500">Số điện thoại:</span>
                                                    <p class="font-medium text-gray-800">{{ $order->driver->phone_number }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500">Biển số xe:</span>
                                                    <p class="font-medium text-gray-800">
                                                        {{ $order->driver->vehicle_number ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        @if (in_array($order->status, ['driver_picked_up', 'in_transit']))
                                            <div class="flex space-x-2 mt-4 border-t pt-3">
                                                <a href="tel:{{ $order->driver->phone_number }}"
                                                    class="flex-1 inline-flex items-center justify-center rounded-md font-medium h-9 px-3 bg-white border hover:bg-gray-100"><i
                                                        class="fas fa-phone-alt mr-2 h-4 w-4"></i>Gọi tài xế</a>
                                                <a href="#"
                                                    class="flex-1 inline-flex items-center justify-center rounded-md font-medium h-9 px-3 bg-white border hover:bg-gray-100"><i
                                                        class="fas fa-route mr-2 h-4 w-4"></i>Theo dõi</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Chi tiết sản phẩm (cải thiện design) --}}
                            <div>
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-shopping-bag text-green-600"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900">Sản phẩm đã đặt</h3>
                                    <span
                                        class="bg-gray-100 text-gray-600 text-sm font-medium px-3 py-1 rounded-full">{{ $order->orderItems->count() }}
                                        món</span>
                                </div>
                                <div class="space-y-4">
                                    @foreach ($order->orderItems as $item)
                                        @php
                                            $product = optional(optional($item->productVariant)->product);
                                            $variant = $item->productVariant;
                                            // Sửa lỗi: Kiểm tra collection trước khi gọi map/pluck
                                            $options = json_decode($item->variant_attributes_snapshot, true) ?? [];
                                            $toppings =
                                                $item->toppings && $item->toppings->isNotEmpty()
                                                    ? $item->toppings
                                                        ->map(fn($t) => $t->topping_name_snapshot ?? $t->name)
                                                        ->all()
                                                    : [];
                                            $modalData = [
                                                'name' =>
                                                    $item->product_name_snapshot ??
                                                    ($item->combo_name_snapshot ?? 'Sản phẩm'),
                                                'image' =>
                                                    optional($product->primaryImage)->url ??
                                                    asset('images/default-product.png'),
                                                'description' =>
                                                    $product->description ?? 'Chưa có mô tả cho sản phẩm này.',
                                                'ingredients' => $product->ingredients ?? [],
                                                'options' => $options,
                                                'toppings' => $toppings,
                                                'price' => $item->unit_price,
                                                'quantity' => $item->quantity,
                                            ];
                                        @endphp
                                        {{-- Card sản phẩm với design hiện đại --}}
                                        <div
                                            class="group bg-white border border-gray-200 rounded-2xl p-6 hover:shadow-lg transition-all duration-300">
                                            <div class="flex items-start gap-4">
                                                <div class="relative flex-shrink-0">
                                                    <img src="{{ $modalData['image'] }}"
                                                        class="w-20 h-20 object-cover rounded-xl shadow-sm group-hover:scale-105 transition-transform duration-300" />
                                                    <div
                                                        class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">
                                                        {{ $item->quantity }}
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-start justify-between mb-3">
                                                        <div class="flex-1">
                                                            <h4 class="font-bold text-gray-900 text-lg mb-1">
                                                                {{ $modalData['name'] }}</h4>
                                                            <div
                                                                class="flex items-center gap-4 text-sm text-gray-500 mb-2">
                                                                <div class="flex items-center gap-1">
                                                                    <i class="fas fa-calculator text-xs"></i>
                                                                    <span>{{ $item->quantity }} ×
                                                                        {{ number_format($item->display_price, 0, ',', '.') }}đ</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="text-right">
                                                            <p class="font-bold text-xl text-orange-600">
                                                                {{ number_format($item->total_price_with_toppings, 0, ',', '.') }}đ
                                                            </p>
                                                        </div>
                                                    </div>
                                                    @if (!empty($modalData['options']))
                                                        <div class="flex items-center flex-wrap gap-1.5 mb-2">
                                                            <i class="fas fa-tags text-gray-400 text-xs"></i>
                                                            @foreach ($modalData['options'] as $option)
                                                                <span
                                                                    class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-lg font-medium">{{ $option }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    @if (!empty($modalData['toppings']))
                                                        <div class="flex items-center flex-wrap gap-1.5 mb-3">
                                                            <i class="fas fa-plus text-gray-400 text-xs"></i>
                                                            @foreach ($modalData['toppings'] as $topping)
                                                                <span
                                                                    class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded-lg font-medium">{{ $topping }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    <div class="flex justify-end">
                                                        <button type="button"
                                                            class="view-product-details-btn inline-flex items-center justify-center rounded-lg text-sm font-medium h-9 px-4 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 hover:from-gray-200 hover:to-gray-300 transition-all duration-200 shadow-sm"
                                                            data-details='@json($modalData)'>
                                                            <i class="fas fa-info-circle mr-2 text-xs"></i>
                                                            Xem chi tiết
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Thông tin người nhận (giống mẫu) --}}
                            <hr class="border-gray-100" />
                            <div>
                                <h3 class="font-semibold mb-3 flex items-center text-gray-800"><i
                                        class="fas fa-user mr-2 h-5 w-5 text-orange-500"></i>Thông tin người nhận</h3>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div><span class="text-gray-500">Tên người nhận:</span>
                                            <p class="font-semibold">{{ $order->customerName }}</p>
                                        </div>
                                        <div><span class="text-gray-500">Số điện thoại:</span>
                                            <p class="font-semibold">{{ $order->customerPhone }}</p>
                                        </div>
                                        <div class="md:col-span-2"><span class="text-gray-500">Email:</span>
                                            <p class="font-semibold">{{ $order->customer->email ?? 'Không có' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Thanh toán & Chi nhánh --}}
                            <hr class="border-gray-100" />
                            <div class="grid lg:grid-cols-2 gap-8">
                                <div>
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                                            <i class="fas fa-wallet text-emerald-600"></i>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900">Thông tin thanh toán</h3>
                                    </div>
                                    <div
                                        class="bg-gradient-to-br from-emerald-50 to-white rounded-2xl p-6 border border-emerald-100">
                                        <div class="space-y-4">
                                            <div class="flex justify-between items-center py-2">
                                                <span class="text-gray-600">Tạm tính:</span>
                                                <span
                                                    class="font-semibold text-gray-900">{{ number_format($order->subtotal, 0, ',', '.') }}đ</span>
                                            </div>
                                            <div class="flex justify-between items-center py-2">
                                                <span class="text-gray-600">Phí giao hàng:</span>
                                                <span
                                                    class="font-semibold text-gray-900">{{ number_format($order->delivery_fee, 0, ',', '.') }}đ</span>
                                            </div>
                                            @if ($order->discount_amount > 0)
                                                <div class="flex justify-between items-center py-2 text-green-600">
                                                    <span>Giảm giá ({{ optional($order->discountCode)->code }}):</span>
                                                    <span
                                                        class="font-semibold">-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</span>
                                                </div>
                                            @endif
                                            <div class="border-t border-dashed border-gray-300 pt-4 mt-4">
                                                <div class="flex justify-between items-center">
                                                    <span class="text-lg font-bold text-gray-900">Tổng cộng:</span>
                                                    <span
                                                        class="text-2xl font-bold text-orange-600">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                            <i class="fas fa-info-circle text-blue-600"></i>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900">Chi tiết đơn hàng</h3>
                                    </div>
                                    <div
                                        class="bg-gradient-to-br from-blue-50 to-white rounded-2xl p-6 border border-blue-100">
                                        <div class="space-y-4">
                                            <div class="flex items-center gap-3 p-3 bg-white rounded-xl border">
                                                <div
                                                    class="w-6 h-6 bg-gray-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-hashtag text-gray-600 text-xs"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Mã đơn hàng
                                                    </p>
                                                    <p class="font-semibold text-gray-900">#{{ $order->order_code }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3 p-3 bg-white rounded-xl border">
                                                <div
                                                    class="w-6 h-6 bg-orange-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-store text-orange-600 text-xs"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Chi nhánh</p>
                                                    <p class="font-semibold text-gray-900">
                                                        {{ $order->branch->name ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3 p-3 bg-white rounded-xl border">
                                                <div
                                                    class="w-6 h-6 bg-purple-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-credit-card text-purple-600 text-xs"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Phương thức
                                                        thanh toán</p>
                                                    <p class="font-semibold text-gray-900">
                                                        @php
                                                            $paymentMethods = [
                                                                'cod' => 'Tiền mặt',
                                                                'vnpay' => 'VNPay',
                                                                'balance' => 'Số dư tài khoản',
                                                            ];
                                                            $paymentMethod =
                                                                optional($order->payment)->payment_method ?? 'cod';
                                                        @endphp
                                                        {{ $paymentMethods[$paymentMethod] ?? 'Tiền mặt' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between p-3 bg-white rounded-xl border">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center">
                                                        <i class="fas fa-check-circle text-green-600 text-xs"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Trạng thái
                                                            thanh toán</p>
                                                    </div>
                                                </div>
                                                <span
                                                    class="font-medium px-3 py-1.5 rounded-full text-xs {{ optional($order->payment)->payment_status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">{{ optional($order->payment)->payment_status == 'completed' ? 'Đã thanh toán' : 'Chưa thanh toán' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Ghi chú --}}
                                @if ($order->notes)
                                    <hr class="border-gray-100" />
                                    <div>
                                        <h3 class="font-semibold mb-2">Ghi chú</h3>
                                        <p class="text-gray-700 bg-gray-50 p-4 rounded-lg text-sm italic">
                                            "{{ $order->notes }}"</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        {{-- Action Buttons --}}
                        <div class="p-6 border-t bg-gray-50/50 rounded-b-xl">
                            <div id="action-buttons" class="flex flex-wrap gap-3 justify-end">
                                @if ($order->status == 'awaiting_confirmation')
                                    <form action="{{ route('customer.orders.updateStatus', $order) }}" method="POST"
                                        class="cancel-order-form">
                                        @csrf
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit"
                                            class="inline-flex items-center justify-center rounded-md text-sm font-medium h-9 px-4 py-2 border border-red-500 text-red-600 hover:bg-red-50">Hủy
                                            đơn</button>
                                    </form>
                                @elseif($order->status == 'delivered')
                                    <a href="{{ route('customer.orders.updateStatus', $order) }}"
                                        class="inline-flex items-center justify-center rounded-md text-sm font-medium h-10 px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200">Chưa
                                        nhận được hàng</a>
                                    <form class="receive-order-form"
                                        action="{{ route('customer.orders.updateStatus', $order) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="item_received">
                                        <button type="submit"
                                            class="inline-flex items-center justify-center rounded-md text-sm font-medium text-white h-10 px-4 py-2 bg-orange-500 hover:bg-orange-600">Xác
                                            nhận đã nhận hàng</button>
                                    </form>
                                @elseif($order->status == 'item_received')
                                    <a href="#"
                                        class="inline-flex items-center justify-center rounded-md text-sm font-medium text-white h-10 px-4 py-2 bg-yellow-500 hover:bg-yellow-600"><i
                                            class="fas fa-star mr-2"></i>Đánh giá</a>
                                @endif
                                <button
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium h-10 px-4 py-2 bg-gray-100 text-gray-800 hover:bg-gray-200">Đặt
                                    lại đơn hàng</button>
                            </div>
                        </div>
                    </div>
            </section>
        </div>
    </div>
    {{-- Modal chi tiết sản phẩm, Modal xác nhận, Toast giữ nguyên --}}
    <div id="product-details-modal"
        class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 p-4 transition-opacity duration-300 opacity-0 pointer-events-none">
        <div id="modal-content"
            class="bg-white rounded-lg max-w-md w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300">
        </div>
    </div>
    <div id="action-confirmation-modal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div id="modal-icon-container"
                    class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i id="modal-icon" class="fas fa-times text-red-600 text-xl"></i>
                </div>
                <h3 id="action-modal-title" class="text-lg leading-6 font-medium text-gray-900 mt-4">Xác nhận hành động
                </h3>
                <div class="mt-2 px-7 py-3">
                    <p id="action-modal-message" class="text-sm text-gray-500">Bạn có chắc chắn thực hiện thao tác này
                        không?</p>
                </div>
                <div class="items-center px-4 py-3 flex gap-3">
                    <button id="action-abort-btn"
                        class="w-full px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Không</button>
                    <button id="action-confirm-btn"
                        class="w-full px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">Đồng ý</button>
                </div>
            </div>
        </div>
    </div>
    <div id="toast-message" class="fixed top-20 right-6 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50 hidden">
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
