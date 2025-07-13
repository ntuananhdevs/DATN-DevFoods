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

    <style>
        .container {
            max-width: 1280px;
            margin: 0 auto;
        }
    </style>
    <div class="bg-gradient-to-r from-orange-500 to-red-500 py-8">
        <div class="container mx-auto px-4">
            <div class="flex items-center">
                <a href="{{ route('customer.orders.index') }}" class="text-white hover:text-white/80 mr-2">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-bold text-white">Chi tiết đơn hàng
                    #{{ $order->order_code ?? $order->id }}</h1>
            </div>
        </div>
    </div>
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col gap-8">
            <section class="mb-10">
                <div id="order-details-card" class="bg-white rounded-xl shadow-sm overflow-hidden"
                    data-order-id="{{ $order->id }}">
                    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between sm:items-start">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Đơn hàng #{{ $order->order_code ?? $order->id }}
                            </h2>
                            <div class="flex items-center mt-2 space-x-4 text-gray-500 text-sm">
                                <div class="flex items-center"><i class="far fa-calendar-alt mr-1.5"></i><span>Đặt lúc:
                                        {{ $order->order_date->format('H:i - d/m/Y') }}</span></div>
                                @if (in_array($order->status, ['delivered', 'item_received']))
                                    <div id="completed-time" class="flex items-center text-green-600"><i
                                            class="fas fa-check-circle mr-1.5"></i><span>Hoàn thành:
                                            {{ optional($order->actual_delivery_time)->format('H:i - d/m/Y') }}</span></div>
                                @elseif($order->status != 'cancelled')
                                    <div id="estimated-time" class="flex items-center"><i
                                            class="far fa-clock mr-1.5"></i><span>Dự kiến:
                                            {{ $order->estimated_delivery_time ? date('H:i', strtotime($order->estimated_delivery_time)) : 'N/A' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div id="order-status-badge" class="mt-4 sm:mt-0">
                            <span class="text-sm font-semibold px-4 py-2 rounded-full capitalize"
                                style="background-color: {{ $order->status_color }}; color: {{ $order->status_text_color }};">
                                {{ $order->status_text }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6 space-y-8">
                        {{-- Thanh trạng thái (Progress Tracker) --}}
                        @if ($currentStepIndex > -1)
                            <div id="progress-tracker">
                                <div class="relative">
                                    <div class="absolute top-4 left-0 w-full h-0.5 bg-gray-200"></div>
                                    <div id="progress-bar"
                                        class="absolute top-4 left-0 h-0.5 bg-orange-500 transition-all duration-500"
                                        style="width: {{ ($currentStepIndex / (count($progressSteps) - 1)) * 100 }}%;">
                                    </div>
                                    <div class="relative flex justify-between">
                                        @foreach ($progressSteps as $key => $stepInfo)
                                            @php
                                                $stepIndex = array_search($key, array_keys($progressSteps));
                                                $isCompleted = $stepIndex <= $currentStepIndex;
                                            @endphp
                                            <div class="flex flex-col items-center w-1/4 z-10"
                                                data-step-key="{{ $key }}">
                                                <div
                                                    class="progress-icon w-8 h-8 rounded-full flex items-center justify-center text-white text-base {{ $isCompleted ? 'bg-orange-500' : 'bg-gray-300' }}">
                                                    <i class="{{ $stepInfo['icon'] }}"></i>
                                                </div>
                                                <p
                                                    class="progress-text text-xs text-center mt-2 font-medium {{ $isCompleted ? 'text-gray-800' : 'text-gray-500' }}">
                                                    {{ $stepInfo['text'] }}</p>
                                            </div>
                                            <p
                                                class="progress-text text-xs text-center mt-2 font-medium {{ $isCompleted ? 'text-gray-800' : 'text-gray-500' }}">
                                                {{ $stepInfo['text'] }}</p>
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
                        class="fas fa-map-marker-alt mr-2 h-5 w-5 text-orange-500"></i>Địa chỉ giao hàng</h3>
                <div class="bg-gray-50 p-4 rounded-lg text-sm space-y-1">
                    <p class="font-medium text-gray-800">{{ $order->customerName }}</p>
                    <p class="text-gray-600">{{ $order->customerPhone }}</p>
                    <p class="text-gray-600 mt-1">{{ $order->delivery_address }}</p>
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
                                    <p class="font-medium text-gray-800">{{ $order->driver->full_name }}</p>
                                    <div class="flex items-center text-gray-600"><i
                                            class="fas fa-star text-yellow-400 mr-1"></i><span>{{ $order->driver->rating ?? 'N/A' }}
                                            sao</span></div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-gray-500">Số điện thoại:</span>
                                    <p class="font-medium text-gray-800">{{ $order->driver->phone_number }}</p>
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

            {{-- Chi tiết sản phẩm (giữ nguyên, đã fix lỗi) --}}
            <div>
                <h3 class="font-semibold mb-4">Sản phẩm đã đặt</h3>
                <div class="space-y-4">
                    @foreach ($order->orderItems as $item)
                        @php
                            $product = optional(optional($item->productVariant)->product);
                            $variant = $item->productVariant;
                            // Sửa lỗi: Kiểm tra collection trước khi gọi map/pluck
                            $options =
                                $variant && $variant->attributeValues && $variant->attributeValues->isNotEmpty()
                                    ? $variant->attributeValues->map(fn($av) => $av->value)->all()
                                    : [];
                            $toppings =
                                $item->toppings && $item->toppings->isNotEmpty()
                                    ? $item->toppings->pluck('name')->all()
                                    : [];
                            $modalData = [
                                'name' => $product->name ?? (optional($item->combo)->name ?? 'Sản phẩm'),
                                'image' => optional($product->primaryImage)->url ?? asset('images/default-product.png'),
                                'description' => $product->description ?? 'Chưa có mô tả cho sản phẩm này.',
                                'ingredients' => $product->ingredients ?? [],
                                'options' => $options,
                                'toppings' => $toppings,
                                'price' => $item->unit_price,
                                'quantity' => $item->quantity,
                            ];
                        @endphp
                        {{-- Khắc phục lỗi bằng @json() --}}
                        <div class="flex items-start space-x-4 p-4 border rounded-lg bg-white hover:shadow-sm">
                            <img src="{{ $modalData['image'] }}" class="w-20 h-20 rounded-lg object-cover flex-shrink-0" />
                            <div class="flex-1">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-medium text-lg text-gray-800">{{ $modalData['name'] }}</h4>
                                    <p class="font-semibold text-orange-600 text-lg text-right">
                                        {{ number_format($item->total_price, 0, ',', '.') }}đ</p>
                                </div>
                                <div class="text-sm text-gray-500 mb-2">Đơn giá:
                                    {{ number_format($item->unit_price, 0, ',', '.') }}đ x
                                    {{ $item->quantity }}</div>
                                @if (!empty($modalData['options']))
                                    <div class="flex items-center flex-wrap gap-1.5 mt-2">
                                        @foreach ($modalData['options'] as $option)
                                            <span
                                                class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-md">{{ $option }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                @if (!empty($modalData['toppings']))
                                    <div class="flex items-center flex-wrap gap-1.5 mt-2">
                                        @foreach ($modalData['toppings'] as $topping)
                                            <span class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded-md">+
                                                {{ $topping }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="flex justify-end mt-3">
                                    <button type="button"
                                        class="view-product-details-btn inline-flex items-center justify-center rounded-md text-sm font-medium h-8 px-3 bg-gray-100 text-gray-800 hover:bg-gray-200"
                                        data-details='@json($modalData)'>
                                        Xem chi tiết
                                    </button>
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

            {{-- Thanh toán & Chi nhánh (giống mẫu) --}}
            <hr class="border-gray-100" />
            <div class="grid md:grid-cols-2 gap-x-8 gap-y-6">
                <div>
                    <h3 class="font-semibold mb-3 flex items-center"><i
                            class="fas fa-wallet mr-2 h-5 w-5 text-orange-500"></i>Thông tin thanh toán</h3>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-3 text-sm">
                        <div class="flex justify-between"><span>Tạm
                                tính:</span><span>{{ number_format($order->subtotal, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="flex justify-between"><span>Phí giao
                                hàng:</span><span>{{ number_format($order->delivery_fee, 0, ',', '.') }}đ</span>
                        </div>
                        @if ($order->discount_amount > 0)
                            <div class="flex justify-between text-green-600"><span>Giảm giá
                                    ({{ optional($order->discountCode)->code }}):</span><span>-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</span>
                            </div>
                        @endif
                        <hr class="border-dashed my-2" />
                        <div class="flex justify-between font-bold text-lg"><span>Tổng cộng:</span><span
                                class="text-orange-600">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="font-semibold mb-3 flex items-center"><i
                            class="fas fa-building mr-2 h-5 w-5 text-orange-500"></i>Chi tiết đơn hàng</h3>
                    <div class="bg-gray-50 p-4 rounded-lg text-sm space-y-3">
                        <div class="flex justify-between"><span class="text-gray-500">Mã đơn hàng:</span>
                            <p class="font-semibold">#{{ $order->order_code }}</p>
                        </div>
                        <div class="flex justify-between"><span class="text-gray-500">Chi nhánh:</span>
                            <p class="font-semibold">{{ $order->branch->name ?? 'N/A' }}</p>
                        </div>
                        <div class="flex justify-between"><span class="text-gray-500">Thanh toán:</span>
                            <p class="font-semibold">
                                {{ optional(optional($order->payment)->paymentMethod)->name ?? 'Tiền mặt' }}
                            </p>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-dashed">
                            <span class="text-gray-500">Trạng thái:</span>
                            <span
                                class="font-medium px-2 py-0.5 rounded-full text-xs {{ optional($order->payment)->payment_status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">{{ optional($order->payment)->payment_status == 'completed' ? 'Đã thanh toán' : 'Chưa thanh toán' }}</span>
                        </div>
                    </div>
                </div>
                {{-- Ghi chú --}}
                @if ($order->notes)
                    <hr class="border-gray-100" />
                    <div>
                        <h3 class="font-semibold mb-2">Ghi chú</h3>
                        <p class="text-gray-700 bg-gray-50 p-4 rounded-lg text-sm italic">"{{ $order->notes }}"</p>
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
                    <form class="receive-order-form" action="{{ route('customer.orders.updateStatus', $order) }}"
                        method="POST">
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
    <div id="toast-message" class="fixed top-6 right-6 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50 hidden">
    </div>
@endsection

@push('scripts')
    {{-- Script xử lý modal và real-time giữ nguyên --}}
@endpush
