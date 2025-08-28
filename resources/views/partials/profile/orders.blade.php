<section id="orders" class="mb-10">
    <h3 class="text-2xl font-bold mb-2">Đơn Hàng Gần Đây</h3>
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex justify-end items-center mb-1 mr-1">
            <a href="{{ route('customer.orders.index') }}" class="text-orange-500 hover:underline text-sm font-medium">Xem
                tất cả</a>
        </div>

        @forelse($recentOrders as $order)
            <div class="border border-gray-200 rounded-lg p-4 transition-shadow hover:shadow-sm mb-4" data-order-id="{{ $order->id }}">
                {{-- Header --}}
                <div class="flex justify-between items-start mb-1">
                    <div class="flex items-center gap-4">
                        <h4 class="font-bold text-orange-600 text-lg">#{{ $order->order_code ?? $order->id }}</h4>
                        <p class="text-sm text-gray-600 flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            {{ optional($order->branch)->name ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <p class="text-sm text-gray-500 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            {{ $order->order_date->format('d/m/Y H:i') }}
                        </p>

                        {{-- Sử dụng optional() và format() để xử lý ngày tháng tốt hơn --}}
                        {{-- <p class="delivery-time text-sm text-gray-500 flex items-center">
                            Dự kiến giao:
                            {{ optional($order->estimated_delivery_time)->format('H:i') ?? 'N/A' }}
                        </p> --}}

                        <span class="text-xs font-medium px-2 py-1 rounded-full status-badge"
                            style="background-color: {{ $order->status_color }}; color: {{ $order->status_text_color }};">
                            @if($order->status == 'confirmed')
                                Đang tìm tài xế
                            @else
                                {{ $order->status_text }}
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Trạng thái đơn & thanh toán --}}
                <div class="flex flex-wrap justify-between items-center gap-4 mb-1 text-sm">
                    <div class="flex items-center gap-1">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                </path>
                            </svg> Thanh toán:
                            @if ($order->payment_status === 'completed')
                                <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded">Thành
                                    công</span>
                            @elseif ($order->payment_status === 'pending')
                                <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-1 rounded">Chờ
                                    xử
                                    lý</span>
                            @elseif ($order->payment_status === 'failed')
                                <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-1 rounded">Thất
                                    bại</span>
                            @elseif ($order->payment_status === 'refunded')
                                <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded">Đã hoàn
                                    tiền</span>
                            @else
                                <span class="text-gray-500">Không rõ</span>
                            @endif
                        </span>
                    </div>

                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-1">
                            <span class="text-gray-600">Dự kiến giao:</span>
                            <span class="font-semibold text-blue-600">
                                @if ($order->estimated_delivery_time)
                                    @php
                                        $orderTime = \Carbon\Carbon::parse($order->created_at);
                                        $estimatedTime = \Carbon\Carbon::parse($order->estimated_delivery_time);
                                        $deliveryDurationMinutes = $orderTime->diffInMinutes($estimatedTime);
                                    @endphp
                                    {{ $deliveryDurationMinutes }} phút
                                @else
                                    Đang xử lý
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Thông tin người nhận --}}
                <div class="flex flex-wrap justify-between items-center gap-4 text-sm">
                    <div class="text-sm text-gray-700 mb-1">
                        <div class="flex flex-wrap items-center gap-6 mb-1">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="font-medium">{{ $order->display_recipient_name }}</span>
                            </span>
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                    </path>
                                </svg>
                                {{ $order->display_delivery_phone }}
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
                            <span>{{ $order->display_full_delivery_address ?? 'Không có địa chỉ' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="text-gray-600">Phí giao hàng:</span>
                        <span class="font-semibold text-gray-900">
                            {{ number_format($order->delivery_fee, 0, ',', '.') }}đ
                        </span>
                    </div>
                </div>

                {{-- Sản phẩm --}}
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Sản phẩm đã đặt:</h3>

                    <div class="space-y-3">
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

                {{-- Tổng tiền + hành động --}}
                <div class="border-t pt-3 border-gray-300">
                    {{-- Chi tiết tổng tiền --}}
                    <div class="space-y-2 mb-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Tạm tính (x{{ $order->orderItems->sum('quantity') }} sản phẩm)</span>
                            <span class="font-medium text-gray-900">{{ number_format($order->subtotal, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Phí giao hàng</span>
                            <span class="font-medium text-gray-900">{{ number_format($order->delivery_fee, 0, ',', '.') }}đ</span>
                        </div>
                        @if ($order->discount_amount > 0 && $order->discountCode)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 flex items-center gap-1">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    Giảm giá ({{ $order->discountCode->code }})
                                </span>
                                <span class="font-medium text-green-600">-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                            <span class="text-md font-bold text-gray-700">Tổng đơn hàng:</span>
                            <span class="text-lg font-bold text-orange-600">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 order-actions">
                        <a href="{{ route('customer.orders.show', $order) }}"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium px-4 py-2 bg-gray-100 text-gray-800 hover:bg-gray-200 border border-gray-300">
                            Chi tiết
                        </a>

                        @if ($order->status == 'pending_payment')
                            <a href="{{ route('checkout.continuePayment', $order) }}"
                                class="inline-flex items-center justify-center rounded-md text-sm font-medium px-4 py-2 bg-orange-500 text-white hover:bg-orange-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3 3v8a3 3 0 003 3z">
                                    </path>
                                </svg>
                                Tiếp tục thanh toán
                            </a>
                            <button type="button" data-order-id="{{ $order->id }}"
                                class="cancel-order-btn inline-flex items-center justify-center rounded-md text-sm font-medium px-4 py-2 border border-red-500 text-red-600 hover:bg-red-50">
                                Hủy đơn
                            </button>
                        @elseif ($order->status == 'awaiting_confirmation')
                            <button type="button" data-order-id="{{ $order->id }}"
                                class="cancel-order-btn inline-flex items-center justify-center rounded-md text-sm font-medium px-4 py-2 border border-red-500 text-red-600 hover:bg-red-50">
                                Hủy đơn
                            </button>
                        @elseif ($order->status == 'confirmed')
                            {{-- Không hiển thị nút hủy cho trạng thái confirmed (đã được branch xác nhận) --}}
                        @elseif ($order->status == 'delivered')
                            <form action="{{ route('customer.orders.updateStatus', $order) }}" method="POST"
                                class="receive-order-form flex gap-2">
                                @csrf
                                <input type="hidden" name="status" value="item_received">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium text-white px-4 py-2 bg-orange-500 hover:bg-orange-600">
                                    Xác nhận đã nhận hàng
                                </button>
                            </form>
                        @elseif ($order->status == 'item_received')
                            @php
                                // Lấy sản phẩm đầu tiên trong đơn hàng để chuyển đến trang đánh giá
                                $firstItem = $order->orderItems->first();
                                $reviewUrl = '#';
                                
                                if ($firstItem) {
                                    if ($firstItem->productVariant && $firstItem->productVariant->product) {
                                        // Nếu là sản phẩm thường
                                        $reviewUrl = route('products.show', $firstItem->productVariant->product->slug) . '#review-reply-form-container';
                                    } elseif ($firstItem->combo) {
                                        // Nếu là combo
                                        $reviewUrl = route('combos.show', $firstItem->combo->slug) . '#review-reply-form-container';
                                    }
                                }
                                
                                // Kiểm tra xem đã có yêu cầu hoàn tiền chưa
                                $hasRefundRequest = $order->refundRequests()->whereNotIn('status', ['cancelled', 'rejected'])->exists();
                            @endphp
                            <div class="flex gap-2">
                                <a href="{{ $reviewUrl }}"
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium text-white px-4 py-2 bg-yellow-500 hover:bg-yellow-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                                        </path>
                                    </svg> Đánh giá
                                </a>
                                
                                @if (!$hasRefundRequest)
                                    <button type="button" 
                                        class="request-refund-btn inline-flex items-center justify-center rounded-md text-sm font-medium text-white px-4 py-2 bg-red-500 hover:bg-red-600"
                                        data-order-id="{{ $order->id }}"
                                        data-order-code="{{ $order->order_code }}"
                                        data-total-amount="{{ $order->total_amount }}">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z">
                                            </path>
                                        </svg> Yêu cầu hoàn tiền
                                    </button>
                                @elseif ($hasRefundRequest)
                                    <span class="inline-flex items-center justify-center rounded-md text-sm font-medium px-4 py-2 bg-gray-100 text-gray-600 border border-gray-300">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg> Đã yêu cầu hoàn tiền
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 py-8">Bạn chưa có đơn hàng nào.</p>
        @endforelse
    </div>
</section>

@include('partials.profile.action-confirmation-modal')

{{-- Modal yêu cầu hoàn tiền --}}
<div id="refundRequestModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            {{-- Header --}}
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-lg font-bold text-gray-900">Yêu cầu hoàn tiền</h3>
                <button type="button" id="closeRefundModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Form --}}
            <form id="refundRequestForm" class="mt-4" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="refundOrderId" name="order_id">
                
                {{-- Thông tin đơn hàng --}}
                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                    <h4 class="font-semibold text-gray-700 mb-2">Thông tin đơn hàng</h4>
                    <div class="text-sm text-gray-600">
                        <p><span class="font-medium">Mã đơn hàng:</span> <span id="refundOrderCode"></span></p>
                        <p><span class="font-medium">Tổng tiền:</span> <span id="refundTotalAmount"></span></p>
                    </div>
                </div>

                {{-- Số tiền hoàn --}}
                <div class="mb-4">
                    <label for="refundAmount" class="block text-sm font-medium text-gray-700 mb-2">Số tiền yêu cầu hoàn *</label>
                    <div class="relative">
                        <input type="number" id="refundAmount" name="refund_amount" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                            placeholder="Nhập số tiền muốn hoàn" min="1000" required>
                        <span class="absolute right-3 top-2 text-gray-500">VNĐ</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Số tiền tối thiểu: 1,000 VNĐ</p>
                </div>

                {{-- Loại hoàn tiền --}}
                <div class="mb-4">
                    <label for="refundType" class="block text-sm font-medium text-gray-700 mb-2">Loại hoàn tiền *</label>
                    <select id="refundType" name="refund_type" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" required>
                        <option value="">Chọn loại hoàn tiền</option>
                        <option value="full">Hoàn toàn bộ</option>
                        <option value="partial">Hoàn một phần</option>
                    </select>
                </div>

                {{-- Lý do hoàn tiền --}}
                <div class="mb-4">
                    <label for="refundReason" class="block text-sm font-medium text-gray-700 mb-2">Lý do hoàn tiền *</label>
                    <select id="refundReason" name="reason" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" required>
                        <option value="">Chọn lý do</option>
                        <option value="product_defective">Sản phẩm bị lỗi/hỏng</option>
                        <option value="wrong_product">Giao sai sản phẩm</option>
                        <option value="late_delivery">Giao hàng quá muộn</option>
                        <option value="poor_quality">Chất lượng không như mong đợi</option>
                        <option value="change_mind">Thay đổi ý định</option>
                        <option value="other">Lý do khác</option>
                    </select>
                </div>

                {{-- Mô tả chi tiết --}}
                <div class="mb-4">
                    <label for="customerMessage" class="block text-sm font-medium text-gray-700 mb-2">Mô tả chi tiết *</label>
                    <textarea id="customerMessage" name="customer_message" rows="4" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        placeholder="Vui lòng mô tả chi tiết vấn đề bạn gặp phải..." required></textarea>
                    <p class="text-xs text-gray-500 mt-1">Tối thiểu 10 ký tự</p>
                </div>

                {{-- Upload hình ảnh --}}
                <div class="mb-6">
                    <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh minh chứng</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-orange-400 transition-colors">
                        <input type="file" id="attachments" name="attachments[]" multiple accept="image/*" class="hidden">
                        <div id="uploadArea" class="cursor-pointer">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">
                                <span class="font-medium text-orange-600">Nhấp để chọn ảnh</span> hoặc kéo thả vào đây
                            </p>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF tối đa 5MB mỗi ảnh (tối đa 5 ảnh)</p>
                        </div>
                    </div>
                    
                    {{-- Preview ảnh --}}
                    <div id="imagePreview" class="mt-3 grid grid-cols-2 md:grid-cols-3 gap-3 hidden"></div>
                </div>

                {{-- Buttons --}}
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" id="cancelRefundBtn" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Hủy
                    </button>
                    <button type="submit" id="submitRefundBtn" 
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <span class="submit-text">Gửi yêu cầu</span>
                        <span class="loading-text hidden">Đang gửi...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Real-time order status updates
class CustomerOrderRealtime {
    constructor() {
        this.pusher = null;
        this.channels = new Map();
        this.pollingInterval = null;
        this.initializePusher();
        this.subscribeToOrderChannels();
    }

    initializePusher() {
        try {
            // Use Laravel config with proper syntax
            const pusherKey = @json(config('broadcasting.connections.pusher.key'));
            const pusherCluster = @json(config('broadcasting.connections.pusher.options.cluster'));
            
            if (!pusherKey || !pusherCluster) {
                this.setupPollingFallback();
                return;
            }

            this.pusher = new Pusher(pusherKey, {
                cluster: pusherCluster,
                encrypted: true,
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }
            });

            this.pusher.connection.bind('error', (err) => {
                this.setupPollingFallback();
            });

        } catch (error) {
            this.setupPollingFallback();
        }
    }

    subscribeToOrderChannels() {
        // Subscribe to each order's private channel
        @foreach($recentOrders as $order)
            this.subscribeToOrderChannel({{ $order->id }});
        @endforeach
        
        // Subscribe to branch orders channel for general updates
        this.subscribeToBranchOrdersChannel();
    }

    subscribeToOrderChannel(orderId) {
        const channelName = `private-order.${orderId}`;
        
        try {
            const channel = this.pusher.subscribe(channelName);
            this.channels.set(orderId, channel);

            channel.bind('order-status-updated', (data) => {
                this.handleOrderStatusUpdate(orderId, data);
            });
        } catch (error) {
            // Fallback to polling if subscription fails
        }
    }

    subscribeToBranchOrdersChannel() {
        try {
            const branchChannel = this.pusher.subscribe('branch-orders-channel');
            this.channels.set('branch-orders', branchChannel);

            branchChannel.bind('order-status-updated', (data) => {
                if (data.order_id) {
                    this.handleOrderStatusUpdate(data.order_id, data);
                }
            });
        } catch (error) {
            // Fallback to polling if subscription fails
        }
    }

    handleOrderStatusUpdate(orderId, data) {
        console.log('Handling order status update:', orderId, data);
        
        // Find the order element
        const orderElement = document.querySelector(`[data-order-id="${orderId}"]`);
        if (!orderElement) {
            console.log('Order element not found for ID:', orderId);
            return;
        }

        // Update status badge
        const statusBadge = orderElement.querySelector('.status-badge');
        if (statusBadge) {
            // Handle special case for 'confirmed' status
            if (data.status === 'confirmed') {
                statusBadge.textContent = 'Đang tìm tài xế';
            } else if (data.status_text) {
                statusBadge.textContent = data.status_text;
            }
            
            // Use colors from event data if available
            if (data.status_color) {
                statusBadge.style.backgroundColor = data.status_color;
            }
            if (data.status_text_color) {
                statusBadge.style.color = data.status_text_color;
            }
            
            console.log('Updated status badge:', statusBadge.textContent);
        }

        // Update delivery time if provided
        if (data.actual_delivery_time) {
            this.updateDeliveryTime(orderElement, data.actual_delivery_time);
        }

        // Show notification
        this.showNotification(orderId, data);

        // Update action buttons based on new status
        this.updateActionButtons(orderElement, data.status);
        
        console.log('Order status update completed for:', orderId);
    }

    updateDeliveryTime(orderElement, actualDeliveryTime) {
        // Find delivery time elements in the order card
        const deliveryTimeElements = orderElement.querySelectorAll('.delivery-time, .font-semibold.text-blue-600');
        
        deliveryTimeElements.forEach(element => {
            if (element.textContent.includes('phút') || element.textContent.includes('Đang xử lý')) {
                // Update to show actual delivery time
                element.textContent = 'Thực tế giao';
                element.className = 'font-semibold text-green-600';
                
                // Add the actual time next to it
                const timeSpan = document.createElement('span');
                timeSpan.textContent = ` ${actualDeliveryTime}`;
                timeSpan.className = 'ml-1';
                element.appendChild(timeSpan);
            }
        });
    }

    updateActionButtons(orderElement, newStatus) {
        const actionContainer = orderElement.querySelector('.order-actions');
        if (!actionContainer) return;

        // Remove existing action buttons except detail button
        const existingForms = actionContainer.querySelectorAll('form');
        existingForms.forEach(form => form.remove());
        
        // Remove existing cancel buttons
        const existingCancelBtns = actionContainer.querySelectorAll('.cancel-order-btn');
        existingCancelBtns.forEach(btn => btn.remove());
        
        // Remove existing review button if any
        const existingReviewBtn = actionContainer.querySelector('a[href="#"]');
        if (existingReviewBtn && existingReviewBtn.textContent.includes('Đánh giá')) {
            existingReviewBtn.remove();
        }

        // Add appropriate buttons based on new status
        if (newStatus === 'pending_payment') {
            // Add continue payment button
            const continuePaymentBtn = this.createContinuePaymentButton(orderElement.dataset.orderId);
            actionContainer.appendChild(continuePaymentBtn);
            // Add cancel button
            const cancelButton = this.createCancelButton(orderElement.dataset.orderId);
            actionContainer.appendChild(cancelButton);
        } else if (newStatus === 'awaiting_confirmation') {
            // Add cancel button for awaiting confirmation orders
            const cancelButton = this.createCancelButton(orderElement.dataset.orderId);
            actionContainer.appendChild(cancelButton);
        } else if (newStatus === 'confirmed' || newStatus === 'waiting_for_driver' || newStatus === 'finding_driver') {
            // Không hiển thị nút hủy cho các trạng thái sau khi branch đã xác nhận
            // Nút hủy sẽ biến mất ngay khi branch nhấn xác nhận
        } else if (newStatus === 'delivered') {
            // Add "Xác nhận đã nhận hàng" button
            const receiveForm = this.createReceiveOrderForm(orderElement.dataset.orderId);
            actionContainer.appendChild(receiveForm);
        } else if (newStatus === 'item_received') {
            // Add "Đánh giá" button
            const reviewButton = this.createReviewButton();
            actionContainer.appendChild(reviewButton);
        }
    }

    createReceiveOrderForm(orderId) {
        const form = document.createElement('form');
        form.className = 'receive-order-form flex gap-2';
        form.action = `/customer/orders/${orderId}/status`;
        form.method = 'POST';
        
        // Create CSRF token input
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Create status input
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = 'item_received';
        
        // Create button
        const button = document.createElement('button');
        button.type = 'submit';
        button.className = 'inline-flex items-center justify-center rounded-md text-sm font-medium text-white px-4 py-2 bg-orange-500 hover:bg-orange-600';
        button.textContent = 'Xác nhận đã nhận hàng';
        
        form.appendChild(csrfInput);
        form.appendChild(statusInput);
        form.appendChild(button);
        
        // Add event listener for the dynamically created form
        this.attachReceiveFormListener(form);
        
        return form;
    }
    
    attachReceiveFormListener(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const actionUrl = this.action;
            
            // Gửi yêu cầu xác nhận nhận hàng
            fetch(actionUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show toast message
                    const toastMessage = document.getElementById('toast-message');
                    if (toastMessage) {
                        toastMessage.textContent = data.message || 'Đã xác nhận nhận hàng thành công';
                        toastMessage.classList.remove('hidden', 'bg-green-600', 'bg-red-600');
                        toastMessage.classList.add('bg-green-600');
                        setTimeout(() => {
                            toastMessage.classList.add('hidden');
                        }, 3000);
                    }
                    
                    // Cập nhật DOM trực tiếp
                    const orderElement = this.closest('[data-order-id]');
                    if (orderElement && data.order) {
                        // Cập nhật status badge
                        const statusBadge = orderElement.querySelector('.status-badge');
                        if (statusBadge) {
                            statusBadge.textContent = data.order.status_text || 'Đã nhận hàng';
                            if (data.order.status_color) {
                                statusBadge.style.backgroundColor = data.order.status_color;
                            }
                            if (data.order.status_text_color) {
                                statusBadge.style.color = data.order.status_text_color;
                            }
                        }
                        
                        // Ẩn form và thêm nút đánh giá
                        this.style.display = 'none';
                        
                        const actionContainer = orderElement.querySelector('.order-actions');
                        if (actionContainer) {
                            const reviewButton = document.createElement('a');
                            reviewButton.href = '#';
                            reviewButton.className = 'inline-flex items-center justify-center rounded-md text-sm font-medium text-white px-4 py-2 bg-yellow-500 hover:bg-yellow-600';
                            reviewButton.innerHTML = `
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg> Đánh giá
                            `;
                            actionContainer.appendChild(reviewButton);
                        }
                    }
                } else {
                    // Show error toast
                    const toastMessage = document.getElementById('toast-message');
                    if (toastMessage) {
                        toastMessage.textContent = data.message || 'Có lỗi xảy ra khi xác nhận nhận hàng';
                        toastMessage.classList.remove('hidden', 'bg-green-600', 'bg-red-600');
                        toastMessage.classList.add('bg-red-600');
                        setTimeout(() => {
                            toastMessage.classList.add('hidden');
                        }, 3000);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Show error toast
                const toastMessage = document.getElementById('toast-message');
                if (toastMessage) {
                    toastMessage.textContent = 'Có lỗi xảy ra khi xác nhận nhận hàng';
                    toastMessage.classList.remove('hidden', 'bg-green-600', 'bg-red-600');
                    toastMessage.classList.add('bg-red-600');
                    setTimeout(() => {
                        toastMessage.classList.add('hidden');
                    }, 3000);
                }
            });
        });
    }

    createContinuePaymentButton(orderId) {
        const button = document.createElement('a');
        button.href = `/checkout/continue-payment/${orderId}`;
        button.className = 'inline-flex items-center justify-center rounded-md text-sm font-medium px-4 py-2 bg-orange-500 text-white hover:bg-orange-600';
        button.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
            Tiếp tục thanh toán
        `;
        
        return button;
    }

    createCancelButton(orderId) {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'cancel-order-btn inline-flex items-center justify-center rounded-md text-sm font-medium px-4 py-2 border border-red-500 text-red-600 hover:bg-red-50';
        button.setAttribute('data-order-id', orderId);
        button.textContent = 'Hủy đơn';
        
        return button;
    }

    createReviewButton() {
        const button = document.createElement('a');
        button.href = '#';
        button.className = 'inline-flex items-center justify-center rounded-md text-sm font-medium text-white px-4 py-2 bg-yellow-500 hover:bg-yellow-600';
        button.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
            </svg> Đánh giá
        `;
        
        return button;
    }

    createStatusIndicator(statusText) {
        const statusDiv = document.createElement('div');
        statusDiv.className = 'inline-flex items-center justify-center rounded-md text-sm font-medium px-4 py-2 bg-blue-100 text-blue-600 border border-blue-300';
        statusDiv.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            ${statusText}
        `;
        
        return statusDiv;
    }

    showNotification(orderId, data) {
        // Use the global showOrderNotification function from fullLayoutMaster.blade.php
        if (typeof window.showOrderNotification === 'function') {
            window.showOrderNotification(orderId, data);
        } else {
            console.log(`Order #${orderId} status updated to ${data.status_text} - notification function not available`);
        }
    }

    setupPollingFallback() {
        // Poll for order status updates every 30 seconds as fallback
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }
        
        const REFRESH_INTERVAL = 100; // 0.1 seconds
        this.pollingInterval = setInterval(() => {
            // Simple polling implementation - could be enhanced with AJAX calls
            // to check for order updates from server
        }, REFRESH_INTERVAL);
    }

    destroy() {
        // Unsubscribe from all channels
        this.channels.forEach((channel, channelKey) => {
            if (channelKey === 'branch-orders') {
                this.pusher.unsubscribe('branch-orders-channel');
            } else {
                this.pusher.unsubscribe(`private-order.${channelKey}`);
            }
        });
        this.channels.clear();
        
        // Disconnect Pusher
        if (this.pusher) {
            this.pusher.disconnect();
            this.pusher = null;
        }
        
        // Clear polling interval
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize realtime order updates if Pusher is available and there are orders
    if (typeof Pusher !== 'undefined' && document.querySelectorAll('[data-order-id]').length > 0) {
        window.customerOrderRealtime = new CustomerOrderRealtime();
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (window.customerOrderRealtime) {
        window.customerOrderRealtime.destroy();
    }
});
</script>

<script>
// Xử lý modal hủy đơn hàng
document.addEventListener('DOMContentLoaded', function() {
    
    // Các biến cho modal
    const modal = document.getElementById('action-confirmation-modal');
    const closeBtn = document.getElementById('action-close-btn');
    const abortBtn = document.getElementById('action-abort-btn');
    const confirmBtn = document.getElementById('action-confirm-btn');
    const modalTitle = document.getElementById('action-modal-title');
    const modalMessage = document.getElementById('action-modal-message');
    const cancelReasonSection = document.getElementById('cancel-reason-section');
    const otherReasonContainer = document.getElementById('other-reason-container');
    const otherReasonText = document.getElementById('other-reason-text');
    const toastMessage = document.getElementById('toast-message');
    
    // Lấy tất cả các nút hủy đơn và form nhận hàng
    const cancelOrderBtns = document.querySelectorAll('.cancel-order-btn');
    const receiveOrderForms = document.querySelectorAll('.receive-order-form');
    
    // Hàm hiển thị toast message
    function showToast(message, isSuccess = true) {
        toastMessage.textContent = message;
        toastMessage.classList.remove('hidden', 'bg-green-600', 'bg-red-600');
        toastMessage.classList.add(isSuccess ? 'bg-green-600' : 'bg-red-600');
        
        setTimeout(() => {
            toastMessage.classList.add('hidden');
        }, 3000);
    }
    
    // Hàm mở modal
    function openActionModal(orderId) {
        // Thiết lập nội dung modal cho hành động hủy đơn
        modalTitle.textContent = 'Hủy đơn hàng';
        modalMessage.textContent = 'Vui lòng cho chúng tôi biết lý do bạn muốn hủy đơn hàng này.';
        cancelReasonSection.classList.remove('hidden');
        
        // Reset các radio button và ẩn phần lý do khác
        const radioButtons = document.querySelectorAll('input[name="cancel_reason"]');
        radioButtons.forEach(radio => radio.checked = false);
        otherReasonContainer.classList.add('hidden');
        otherReasonText.value = '';
        
        // Lưu orderId vào nút xác nhận để sử dụng khi gửi yêu cầu
        confirmBtn.dataset.orderId = orderId;
        
        // Hiển thị modal
        modal.classList.remove('hidden');
    }
    
    // Xử lý sự kiện click cho các nút hủy đơn
    cancelOrderBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            openActionModal(orderId);
        });
    });
    
    // Xử lý sự kiện submit cho form nhận hàng - để form submit bình thường
    // Không cần preventDefault, để form submit bình thường và trang sẽ reload với thông báo
    
    // Xử lý sự kiện khi chọn lý do "Khác"
    document.querySelectorAll('input[name="cancel_reason"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'Khác') {
                otherReasonContainer.classList.remove('hidden');
                otherReasonText.focus();
            } else {
                otherReasonContainer.classList.add('hidden');
                otherReasonText.value = '';
            }
        });
    });
    
    // Xử lý sự kiện đóng modal
    function closeModal() {
        modal.classList.add('hidden');
        // Reset form
        const radioButtons = document.querySelectorAll('input[name="cancel_reason"]');
        radioButtons.forEach(radio => radio.checked = false);
        otherReasonContainer.classList.add('hidden');
        otherReasonText.value = '';
    }
    
    // Gán sự kiện cho các nút đóng
    closeBtn.addEventListener('click', closeModal);
    abortBtn.addEventListener('click', closeModal);
    
    // Xử lý sự kiện khi nhấn nút xác nhận hủy
    confirmBtn.addEventListener('click', function() {
        const orderId = this.dataset.orderId;
        let selectedReason = document.querySelector('input[name="cancel_reason"]:checked');
        
        if (!selectedReason) {
            showToast('Vui lòng chọn lý do hủy đơn hàng', false);
            return;
        }
        
        let reason = selectedReason.value;
        
        // Nếu chọn lý do khác, kiểm tra và lấy nội dung từ textarea
        if (reason === 'Khác') {
            if (!otherReasonText.value.trim()) {
                showToast('Vui lòng nhập lý do cụ thể', false);
                otherReasonText.focus();
                return;
            }
            reason = otherReasonText.value.trim();
        }
        
        // Tạo form data để gửi
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('status', 'cancelled');
        formData.append('reason', reason);
        
        // Gửi yêu cầu hủy đơn
        const updateStatusUrl = '{{ route('customer.orders.updateStatus', ['order' => ':order_id']) }}'.replace(':order_id', orderId);
        fetch(updateStatusUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Đơn hàng đã được hủy thành công');
                closeModal();
                
                // Reload trang sau khi hủy đơn thành công
                setTimeout(() => {
                    window.location.reload();
                }, 1500); // Đợi 1.5 giây để hiển thị toast message
            } else {
                showToast(data.message || 'Có lỗi xảy ra khi hủy đơn hàng', false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Có lỗi xảy ra khi hủy đơn hàng', false);
        });
    });

    // Xử lý modal yêu cầu hoàn tiền
    const refundModal = document.getElementById('refundRequestModal');
    const refundForm = document.getElementById('refundRequestForm');
    const closeRefundModalBtn = document.getElementById('closeRefundModal');
    const cancelRefundBtn = document.getElementById('cancelRefundBtn');
    const submitRefundBtn = document.getElementById('submitRefundBtn');
    const attachmentsInput = document.getElementById('attachments');
    const uploadArea = document.getElementById('uploadArea');
    const imagePreview = document.getElementById('imagePreview');
    const refundAmountInput = document.getElementById('refundAmount');
    const refundTypeSelect = document.getElementById('refundType');
    
    let selectedFiles = [];
    let maxTotalAmount = 0;

    // Mở modal khi click nút yêu cầu hoàn tiền
    document.addEventListener('click', function(e) {
        if (e.target.closest('.request-refund-btn')) {
            const btn = e.target.closest('.request-refund-btn');
            const orderId = btn.dataset.orderId;
            const orderCode = btn.dataset.orderCode;
            const totalAmount = parseInt(btn.dataset.totalAmount);
            
            // Cập nhật thông tin đơn hàng trong modal
            document.getElementById('refundOrderId').value = orderId;
            document.getElementById('refundOrderCode').textContent = orderCode;
            document.getElementById('refundTotalAmount').textContent = new Intl.NumberFormat('vi-VN').format(totalAmount) + ' VNĐ';
            
            // Set max amount và default value
            maxTotalAmount = totalAmount;
            refundAmountInput.max = totalAmount;
            refundAmountInput.value = totalAmount;
            
            // Reset form
            refundForm.reset();
            document.getElementById('refundOrderId').value = orderId;
            refundAmountInput.value = totalAmount;
            selectedFiles = [];
            updateImagePreview();
            
            // Hiển thị modal
            refundModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    });

    // Đóng modal
    function closeRefundModal() {
        refundModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        refundForm.reset();
        selectedFiles = [];
        updateImagePreview();
    }

    closeRefundModalBtn.addEventListener('click', closeRefundModal);
    cancelRefundBtn.addEventListener('click', closeRefundModal);

    // Đóng modal khi click outside
    refundModal.addEventListener('click', function(e) {
        if (e.target === refundModal) {
            closeRefundModal();
        }
    });

    // Xử lý thay đổi loại hoàn tiền
    refundTypeSelect.addEventListener('change', function() {
        if (this.value === 'full') {
            refundAmountInput.value = maxTotalAmount;
            refundAmountInput.readOnly = true;
        } else {
            refundAmountInput.readOnly = false;
            if (this.value === 'partial') {
                refundAmountInput.value = '';
            }
        }
    });

    // Xử lý upload file
    uploadArea.addEventListener('click', () => attachmentsInput.click());
    
    // Drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('border-orange-400');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('border-orange-400');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-orange-400');
        const files = Array.from(e.dataTransfer.files);
        handleFileSelection(files);
    });

    attachmentsInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        handleFileSelection(files);
    });

    function handleFileSelection(files) {
        const validFiles = files.filter(file => {
            // Kiểm tra loại file
            if (!file.type.startsWith('image/')) {
                showToast('Chỉ được chọn file hình ảnh', false);
                return false;
            }
            // Kiểm tra kích thước file (5MB)
            if (file.size > 5 * 1024 * 1024) {
                showToast(`File ${file.name} quá lớn (tối đa 5MB)`, false);
                return false;
            }
            return true;
        });

        // Kiểm tra tổng số file (tối đa 5)
        if (selectedFiles.length + validFiles.length > 5) {
            showToast('Chỉ được chọn tối đa 5 hình ảnh', false);
            return;
        }

        selectedFiles = selectedFiles.concat(validFiles);
        updateImagePreview();
    }

    function updateImagePreview() {
        if (selectedFiles.length === 0) {
            imagePreview.classList.add('hidden');
            return;
        }

        imagePreview.classList.remove('hidden');
        imagePreview.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative group';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" class="w-full h-24 object-cover rounded-lg border">
                    <button type="button" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity" onclick="removeImage(${index})">
                        ×
                    </button>
                    <div class="absolute bottom-1 left-1 bg-black bg-opacity-50 text-white text-xs px-1 rounded">
                        ${(file.size / 1024 / 1024).toFixed(1)}MB
                    </div>
                `;
                imagePreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

    // Xóa hình ảnh
    window.removeImage = function(index) {
        selectedFiles.splice(index, 1);
        updateImagePreview();
    };

    // Xử lý submit form
    refundForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validation
        const refundAmount = parseInt(refundAmountInput.value);
        const customerMessage = document.getElementById('customerMessage').value.trim();
        
        if (refundAmount < 1000) {
            showToast('Số tiền hoàn tối thiểu là 1,000 VNĐ', false);
            return;
        }
        
        if (refundAmount > maxTotalAmount) {
            showToast('Số tiền hoàn không được vượt quá tổng đơn hàng', false);
            return;
        }
        
        if (customerMessage.length < 10) {
            showToast('Mô tả chi tiết phải có ít nhất 10 ký tự', false);
            return;
        }

        // Hiển thị loading
        const submitText = submitRefundBtn.querySelector('.submit-text');
        const loadingText = submitRefundBtn.querySelector('.loading-text');
        submitText.classList.add('hidden');
        loadingText.classList.remove('hidden');
        submitRefundBtn.disabled = true;

        // Tạo FormData
        const formData = new FormData(refundForm);
        
        // Thêm files vào FormData
        selectedFiles.forEach((file, index) => {
            formData.append(`attachments[${index}]`, file);
        });

        // Gửi request
        fetch('{{ route("customer.refunds.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Yêu cầu hoàn tiền đã được gửi thành công', true);
                closeRefundModal();
                // Reload trang để cập nhật trạng thái
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showToast(data.message || 'Có lỗi xảy ra khi gửi yêu cầu', false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Có lỗi xảy ra khi gửi yêu cầu hoàn tiền', false);
        })
        .finally(() => {
            // Ẩn loading
            submitText.classList.remove('hidden');
            loadingText.classList.add('hidden');
            submitRefundBtn.disabled = false;
        });
    });
});
</script>
@endpush
