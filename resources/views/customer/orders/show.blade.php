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

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{-- Nút Quay Lại --}}
            <div class="mb-6">
                <a href="{{ route('customer.orders.index') }}"
                    class="inline-flex items-center text-orange-600 hover:text-orange-700 font-medium p-2 -ml-2">
                    <i class="fas fa-arrow-left mr-2 h-4 w-4"></i>
                    Quay lại danh sách
                </a>
            </div>

            {{-- Thẻ Card chính chứa toàn bộ thông tin đơn hàng --}}
            <div id="order-details-card" class="bg-white rounded-xl shadow-md" data-order-id="{{ $order->id }}">
                {{-- Header --}}
                <div class="p-6 border-b">
                    <div class="flex flex-col sm:flex-row justify-between sm:items-start">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Đơn hàng #{{ $order->order_code ?? $order->id }}
                            </h1>
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
                                style="background-color: {{ $order->status_color['bg'] }}; color: {{ $order->status_color['text'] }};">
                                {{ $order->status_text }}
                            </span>
                        </div>
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
                                    style="width: {{ ($currentStepIndex / (count($progressSteps) - 1)) * 100 }}%;"></div>
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
                                            $variant &&
                                            $variant->attributeValues &&
                                            $variant->attributeValues->isNotEmpty()
                                                ? $variant->attributeValues->map(fn($av) => $av->value)->all()
                                                : [];
                                        $toppings =
                                            $item->toppings && $item->toppings->isNotEmpty()
                                                ? $item->toppings->pluck('name')->all()
                                                : [];
                                        $modalData = [
                                            'name' => $product->name ?? (optional($item->combo)->name ?? 'Sản phẩm'),
                                            'image' =>
                                                optional($product->primaryImage)->url ??
                                                asset('images/default-product.png'),
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
                                        <img src="{{ $modalData['image'] }}"
                                            class="w-20 h-20 rounded-lg object-cover flex-shrink-0" />
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
                                                        <span
                                                            class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded-md">+
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
                        </div>

                        {{-- Ghi chú (giống mẫu) --}}
                        @if ($order->notes)
                            <hr class="border-gray-100" />
                            <div>
                                <h3 class="font-semibold mb-2">Ghi chú</h3>
                                <p class="text-gray-700 bg-gray-50 p-4 rounded-lg text-sm italic">"{{ $order->notes }}"
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="p-6 border-t bg-gray-50/50 rounded-b-xl">
                    <div id="action-buttons" class="flex flex-wrap gap-3 justify-end">
                        {{-- Case 1: Order is waiting for the restaurant to confirm --}}
                        @if ($order->status == 'awaiting_confirmation')
                            <form action="{{ route('customer.orders.updateStatus', $order) }}" method="POST"
                                class="cancel-order-form">
                                @csrf
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium h-9 px-4 py-2 border border-red-500 text-red-600 hover:bg-red-50">Hủy
                                    đơn</button>
                            </form>

                            {{-- Case 2: Order has been delivered by the driver --}}
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

                            {{-- Case 3: Customer has confirmed they received the item --}}
                        @elseif($order->status == 'item_received')
                            <a href="#"
                                class="inline-flex items-center justify-center rounded-md text-sm font-medium text-white h-10 px-4 py-2 bg-yellow-500 hover:bg-yellow-600">
                                <i class="fas fa-star mr-2"></i>Đánh giá
                            </a>
                        @endif

                        {{-- This button can be a link to your re-ordering logic --}}
                        <button
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium h-10 px-4 py-2 bg-gray-100 text-gray-800 hover:bg-gray-200">Đặt
                            lại đơn hàng</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal chi tiết sản phẩm --}}
        <div id="product-details-modal"
            class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 p-4 transition-opacity duration-300 opacity-0 pointer-events-none">
            <div id="modal-content"
                class="bg-white rounded-lg max-w-md w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300">
                {{-- Nội dung modal sẽ được Javascript chèn vào đây --}}
            </div>
        </div>

        {{-- THÊM ĐOẠN HTML NÀY VÀO CUỐI FILE --}}
        <!-- Modal xác nhận chung -->
        <div id="action-confirmation-modal"
            class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div id="modal-icon-container"
                        class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <i id="modal-icon" class="fas fa-times text-red-600 text-xl"></i>
                    </div>
                    <h3 id="action-modal-title" class="text-lg leading-6 font-medium text-gray-900 mt-4">Xác nhận hành
                        động</h3>
                    <div class="mt-2 px-7 py-3">
                        <p id="action-modal-message" class="text-sm text-gray-500">
                            Bạn có chắc chắn thực hiện thao tác này không?
                        </p>
                    </div>
                    <div class="items-center px-4 py-3 flex gap-3">
                        <button id="action-abort-btn"
                            class="w-full px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                            Không
                        </button>
                        <button id="action-confirm-btn"
                            class="w-full px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                            Đồng ý
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- KẾT THÚC PHẦN THÊM MỚI --}}
        <div id="toast-message"
            class="fixed top-6 right-6 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50 hidden"></div>

    </div>

@endsection

@push('scripts')
    {{-- Script xử lý modal và real-time --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // =========================================================
            // MODAL LOGIC (Plain Javascript, no dependencies)
            // =========================================================
            const modalContainer = document.getElementById('product-details-modal');
            const modalContent = document.getElementById('modal-content');
            const viewButtons = document.querySelectorAll('.view-product-details-btn');

            const openModal = (details) => {
                let optionsHtml = '';
                if (details.options && details.options.length > 0) {
                    optionsHtml = `
                    <div>
                        <h4 class="font-semibold mb-2 text-gray-800">Tùy chọn</h4>
                        <div class="flex flex-wrap gap-2">
                            ${details.options.map(option => `<span class="text-sm bg-blue-100 text-blue-800 px-3 py-1 rounded-full">${option}</span>`).join('')}
                        </div>
                    </div>`;
                }

                let toppingsHtml = '';
                if (details.toppings && details.toppings.length > 0) {
                    toppingsHtml = `
                    <div>
                        <h4 class="font-semibold mb-2 text-gray-800">Topping</h4>
                        <div class="flex flex-wrap gap-2">
                            ${details.toppings.map(topping => `<span class="text-sm bg-orange-100 text-orange-700 px-3 py-1 rounded-full">${topping}</span>`).join('')}
                        </div>
                    </div>`;
                }

                let ingredientsHtml = '';
                if (details.ingredients && details.ingredients.length > 0) {
                    ingredientsHtml = `
                    <div>
                        <h4 class="font-semibold mb-2 text-gray-800">Thành phần</h4>
                        <div class="flex flex-wrap gap-2">
                            ${details.ingredients.map(ing => `<span class="text-sm bg-gray-100 px-3 py-1 rounded-full">${ing}</span>`).join('')}
                        </div>
                    </div>`;
                }

                modalContent.innerHTML = `
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl font-bold">${details.name}</h3>
                        <button type="button" class="close-modal-btn text-gray-400 hover:text-gray-600">&times;</button>
                    </div>
                    <img src="${details.image}" alt="${details.name}" class="w-full h-48 rounded-lg object-cover mb-4 border" />
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-semibold mb-2 text-gray-800">Mô tả</h4>
                            <p class="text-gray-700 text-sm">${details.description}</p>
                        </div>
                        ${ingredientsHtml}
                        ${optionsHtml}
                        ${toppingsHtml}
                        <div class="pt-4 border-t">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-800">Giá:</span>
                                <span class="text-xl font-bold text-orange-600">${new Intl.NumberFormat('vi-VN').format(details.price)}đ</span>
                            </div>
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-gray-600">Số lượng:</span>
                                <span class="font-semibold">${details.quantity}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 pt-4 border-t">
                        <button type="button" class="close-modal-btn w-full h-10 inline-flex items-center justify-center rounded-md text-sm font-medium bg-orange-600 text-white hover:bg-orange-700">Đóng</button>
                    </div>
                </div>`;

                modalContainer.style.display = 'flex';
                setTimeout(() => {
                    modalContainer.classList.remove('opacity-0', 'pointer-events-none');
                    modalContent.classList.remove('scale-95');
                }, 10);
            };

            const closeModal = () => {
                modalContainer.classList.add('opacity-0');
                modalContent.classList.add('scale-95');
                setTimeout(() => {
                    modalContainer.style.display = 'none';
                    modalContainer.classList.add('pointer-events-none');
                }, 300);
            };

            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Sử dụng dataset.details thay vì getAttribute để tương thích tốt hơn
                    const details = JSON.parse(this.dataset.details);
                    openModal(details);
                });
            });

            modalContainer.addEventListener('click', function(e) {
                if (e.target === modalContainer) {
                    closeModal();
                }
            });

            // Cần delegate event cho nút đóng vì nó được tạo động
            document.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('close-modal-btn')) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (e) => (e.key === "Escape") && closeModal());


            // =========================================================
            // REAL-TIME LOGIC (with Laravel Echo & Pusher)
            // =========================================================
            const orderId = document.getElementById('order-details-card').dataset.orderId;
            if (window.Echo) {
                window.Echo.private(`order.${orderId}`)
                    .listen('.OrderStatusUpdated', (event) => {
                        // Toast đã được xử lý chung ở layout, nhưng ta vẫn có thể thêm một thông báo ở đây nếu muốn
                        console.log('OrderStatusUpdated event received:', event);

                        // Chỉ cần tải lại trang, toast sẽ tự động hiển thị
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    });
            }

            // TOAST
            function showToast(message, color = "bg-green-600") {
                const toast = document.getElementById('toast-message');
                toast.textContent = message;
                toast.className = `fixed top-6 right-6 ${color} text-white px-4 py-2 rounded shadow-lg z-50`;
                toast.style.display = 'block';
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 3000);
            }

            // ================= PHẦN 3: LOGIC XÁC NHẬN HỦY ĐƠN ======================
            let formToSubmit = null;
            let modalAction = 'cancel'; // hoặc 'receive'

            const modal = document.getElementById('action-confirmation-modal');
            const modalIcon = document.getElementById('modal-icon');
            const modalIconContainer = document.getElementById('modal-icon-container');
            const modalTitle = document.getElementById('action-modal-title');
            const modalMessage = document.getElementById('action-modal-message');
            const confirmBtn = document.getElementById('action-confirm-btn');
            const abortBtn = document.getElementById('action-abort-btn');

            function openActionModal(form, actionType) {
                formToSubmit = form;
                modalAction = actionType;

                if (actionType === 'cancel') {
                    modalIcon.className = "fas fa-times text-red-600 text-xl";
                    modalIconContainer.className =
                        "mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100";
                    modalTitle.textContent = "Xác nhận hủy đơn hàng";
                    modalMessage.textContent =
                        "Bạn có chắc chắn muốn hủy đơn hàng này không? Hành động này không thể hoàn tác.";
                    confirmBtn.className = "w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700";
                    confirmBtn.textContent = "Đồng ý hủy";
                } else if (actionType === 'receive') {
                    modalIcon.className = "fas fa-check text-green-600 text-xl";
                    modalIconContainer.className =
                        "mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100";
                    modalTitle.textContent = "Xác nhận đã nhận hàng";
                    modalMessage.textContent =
                        "Bạn xác nhận đã nhận được hàng? Vui lòng kiểm tra kỹ trước khi xác nhận.";
                    confirmBtn.className = "w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700";
                    confirmBtn.textContent = "Đã nhận";
                }
                modal.classList.remove('hidden');
            }

            function closeActionModal() {
                formToSubmit = null;
                modal.classList.add('hidden');
            }

            if (confirmBtn) {
                confirmBtn.addEventListener('click', function() {
                    if (formToSubmit) {
                        const form = formToSubmit;
                        const action = form.getAttribute('action');
                        const methodInput = form.querySelector('input[name="_method"]');
                        const csrf = form.querySelector('input[name="_token"]').value;
                        const status = form.querySelector('input[name="status"]').value;
                        const method = methodInput ? methodInput.value : form.method;
                        const formData = new FormData();
                        formData.append('_token', csrf);
                        formData.append('status', status);
                        if (methodInput) formData.append('_method', method);

                        fetch(action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                closeActionModal();
                                if (data.success) {
                                    showToast(
                                        modalAction === 'cancel' ? 'Hủy đơn hàng thành công!' :
                                        'Đã nhận hàng thành công!'
                                    );
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1300);
                                } else {
                                    showToast(data.message || 'Có lỗi xảy ra!', "bg-red-600");
                                }
                            })
                            .catch(() => {
                                closeActionModal();
                                showToast('Có lỗi khi kết nối!', "bg-red-600");
                            });
                    } else {
                        closeActionModal();
                    }
                });
            }
            if (abortBtn) {
                abortBtn.addEventListener('click', function() {
                    closeActionModal();
                });
            }

            // Nút hủy đơn hàng
            document.querySelectorAll('.cancel-order-form button[type="submit"]').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const form = this.closest('form');
                    openActionModal(form, 'cancel');
                });
            });

            // Nút xác nhận đã nhận hàng
            document.querySelectorAll('.receive-order-form button[type="submit"]').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const form = this.closest('form');
                    openActionModal(form, 'receive');
                });
            });
        });
    </script>
@endpush
