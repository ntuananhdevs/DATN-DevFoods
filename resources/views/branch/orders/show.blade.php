@extends('layouts.branch.contentLayoutMaster')

@section('title', 'Chi tiết đơn hàng #' . $order->order_code)

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
@endsection

@section('page-style')
<style>
.status-timeline {
    position: relative;
}
.status-timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e5e7eb;
}
.timeline-item {
    position: relative;
    padding-left: 40px;
    margin-bottom: 24px;
}
.timeline-dot {
    position: absolute;
    left: 8px;
    top: 4px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #e5e7eb;
    background: white;
}
.timeline-dot.active {
    border-color: #3b82f6;
    background: #3b82f6;
}
.timeline-dot.completed {
    border-color: #10b981;
    background: #10b981;
}
.order-item-card {
    transition: all 0.2s ease;
}
.order-item-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 600;
}
.action-button {
    transition: all 0.2s ease;
    cursor: pointer;
    pointer-events: auto;
    user-select: none;
}
.action-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.action-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}
.animate-spin {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.info-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.customer-card {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
.payment-card {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}
.delivery-card {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}
@media print {
    .no-print { display: none !important; }
    .print-only { display: block !important; }
    body { background: white !important; }
}
</style>
@endsection

@section('content')
<div class="mx-auto p-4">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold mb-2 text-gray-900">Chi tiết đơn hàng</h1>
            <p class="text-gray-600">Thông tin chi tiết đơn hàng #{{ $order->order_code ?? $order->id }}</p>
        </div>
        <div class="flex items-center gap-2 mt-4 md:mt-0">
            <a href="{{ route('branch.orders.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Quay lại
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Status -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Trạng thái đơn hàng</h2>
                    {{-- <span class="status-badge {{ $order->statusColor }} text-white rounded-lg px-3 py-1">{{ $order->statusText }}</span> --}}
                </div>
                
                @php
                    $mainStatusTextMap = [
                        'awaiting_confirmation' => 'Chờ xác nhận',
                        'confirmed' => 'Đã xác nhận',
                        'awaiting_driver' => 'Chờ tài xế',
                        'driver_confirmed' => 'Chờ tài xế',
                        'waiting_driver_pick_up' => 'Chờ tài xế',
                        'driver_picked_up' => 'Đang giao',
                        'in_transit' => 'Đang giao',
                        'delivered' => 'Hoàn thành',
                        'item_received' => 'Hoàn thành',
                    ];
                    $mainStatusList = ['Chờ xác nhận', 'Đã xác nhận', 'Chờ tài xế', 'Đang giao', 'Hoàn thành'];
                @endphp
                @if($order->statusHistory->count() > 0)
                    <div class="space-y-3">
                        @foreach($order->statusHistory->sortByDesc('changed_at') as $history)
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $history->old_status ? ($mainStatusTextMap[$history->old_status] ?? 'Khác') . ' → ' : '' }}{{ $mainStatusTextMap[$history->new_status] ?? 'Khác' }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ $history->changed_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @if($history->changedBy)
                                        <p class="text-xs text-gray-600">Thay đổi bởi: {{ $history->changedBy->name }} ({{ $history->changed_by_role }})</p>
                                    @endif
                                    @if($history->note)
                                        <p class="text-xs text-gray-600 mt-1">{{ $history->note }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Chưa có lịch sử thay đổi trạng thái</p>
                @endif
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Chi tiết sản phẩm</h2>
                
                <div class="space-y-4">
                    @foreach($order->orderItems as $item)
                        <div class="flex items-start gap-4 p-4 border border-gray-200 rounded-lg">
                            <div class="flex-1">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h3 class="font-medium text-gray-900">
                                            @if($item->productVariant)
                                                {{ $item->productVariant->product->name }}
                                                @if($item->productVariant->name)
                                                    - {{ $item->productVariant->name }}
                                                @endif
                                            @elseif($item->combo)
                                                {{ $item->combo->name }}
                                            @endif
                                        </h3>
                                        <p class="text-sm text-gray-500">Số lượng: {{ $item->quantity }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-gray-900">{{ number_format($item->unit_price) }}₫</p>
                                        <p class="text-sm text-gray-500">Tổng: {{ number_format($item->total_price) }}₫</p>
                                    </div>
                                </div>
                                
                                @if($item->toppings->count() > 0)
                                    <div class="mt-2">
                                        <p class="text-xs text-gray-500 mb-1">Topping:</p>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($item->toppings as $topping)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                                    {{ $topping->topping->name }} ({{ $topping->quantity }})
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Thông tin khách hàng</h2>
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex items-center justify-center w-11 h-11 rounded-full bg-blue-100 text-blue-700 font-bold text-lg">
                        {{ strtoupper(mb_substr($order->customerName ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold text-base text-gray-900">{{ $order->customerName }}</span>
                        <div class="flex items-center gap-2 text-gray-500 text-sm">
                            <span class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 16.92v3a2 2 0 01-2.18 2A19.72 19.72 0 013 5.18 2 2 0 015 3h3a2 2 0 012 1.72c.13.81.36 1.6.68 2.34a2 2 0 01-.45 2.11l-1.27 1.27a16 16 0 006.29 6.29l1.27-1.27a2 2 0 012.11-.45c.74.32 1.53.55 2.34.68A2 2 0 0122 16.92z"/>
                                </svg>
                                {{ $order->customerPhone }}
                            </span>
                            @if(isset($order->distance_km))
                                <span class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ number_format($order->distance_km, 1) }} km
                                </span>
                            @endif
                            @php
                                $statusColors = [
                                    'awaiting_confirmation' => 'bg-gray-200 text-gray-800',
                                    'awaiting_driver' => 'bg-blue-100 text-blue-800',
                                    'in_transit' => 'bg-blue-500 text-white',
                                    'delivered' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    'refunded' => 'bg-yellow-100 text-yellow-800',
                                ];
                                $statusText = $order->statusText ?? ucfirst($order->status);
                                $statusColor = $statusColors[$order->status] ?? 'bg-gray-200 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                {{ $statusText }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Tên khách hàng</p>
                        <p class="font-medium text-gray-900">{{ $order->customerName }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Số điện thoại</p>
                        <p class="font-medium text-gray-900">{{ $order->customerPhone }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">Địa chỉ giao hàng</p>
                        <p class="font-medium text-gray-900">
                            @if($order->address)
                                {{ $order->address->full_address }}
                            @else
                                {{ $order->delivery_address ?? 'Không có thông tin địa chỉ' }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="space-y-6">
            <!-- Order Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Thông tin đơn hàng</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Mã đơn hàng:</span>
                        <span class="font-medium">{{ $order->order_code ?? $order->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Ngày đặt:</span>
                        <span class="font-medium">{{ $order->order_date->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($order->estimated_delivery_time)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Dự kiến giao:</span>
                            <span class="font-medium">{{ $order->estimated_delivery_time->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    @if($order->actual_delivery_time)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Thời gian giao:</span>
                            <span class="font-medium">{{ $order->actual_delivery_time->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    @php
                        $paymentStatusMap = [
                            'pending' => ['Chờ xử lý', 'bg-yellow-100 text-yellow-800'],
                            'completed' => ['Thành công', 'bg-green-100 text-green-800'],
                            'failed' => ['Thất bại', 'bg-red-100 text-red-800'],
                            'refunded' => ['Đã hoàn tiền', 'bg-pink-100 text-pink-800'],
                        ];
                        $ps = $order->payment->payment_status ?? null;
                        [$psLabel, $psClass] = $paymentStatusMap[$ps] ?? ['Không xác định (' . ($ps ?? 'null') . ')', 'bg-gray-100 text-gray-800'];
                    @endphp
                    <div class="flex justify-between">
                        <span class="text-gray-500">Trạng thái thanh toán:</span>
                        @if($order->payment)
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $psClass }}">{{ $psLabel }}</span>
                        @else
                            <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-800">Không có</span>
                        @endif
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Phương thức thanh toán:</span>
                        @if($order->payment)
                            @php
                                $paymentMethodMap = [
                                    'cod' => 'Thanh toán khi nhận hàng',
                                    'vnpay' => 'VNPay',
                                    'balance' => 'Số dư tài khoản',
                                ];
                                $pm = $order->payment->payment_method ?? null;
                                $pmLabel = $paymentMethodMap[$pm] ?? ucfirst($pm ?? 'Không xác định');
                            @endphp
                            @if($pm === 'vnpay')
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-blue-100 text-blue-800 text-xs font-semibold">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 16" style="height:1em;width:auto;display:inline;vertical-align:middle;" aria-label="VNPAY Icon">
                                    <text x="0" y="12" font-size="12" font-family="Arial, Helvetica, sans-serif" font-weight="bold" fill="#e30613">VN</text>
                                    <text x="18" y="12" font-size="12" font-family="Arial, Helvetica, sans-serif" font-weight="bold" fill="#0072bc">PAY</text>
                                </svg>
                            </span>
                            @elseif($pm === 'cod')
                                 <span class="inline-block px-2 py-0.5 rounded bg-green-700 text-white text-xs font-semibold">COD</span>
                            @else
                                <span class="font-medium">{{ $pmLabel }}</span>
                            @endif
                        @else
                            <span class="font-medium">Không có</span>
                        @endif
                    </div>
                    @if($order->driver)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tài xế:</span>
                            <span class="font-medium">{{ $order->driver->name }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Tổng thanh toán</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tạm tính:</span>
                        <span class="font-medium">{{ number_format($order->subtotal) }}₫</span>
                    </div>
                    @if($order->delivery_fee > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Phí giao hàng:</span>
                            <span class="font-medium">{{ number_format($order->delivery_fee) }}₫</span>
                        </div>
                    @endif
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Giảm giá:</span>
                            <span class="font-medium text-red-600">-{{ number_format($order->discount_amount) }}₫</span>
                        </div>
                    @endif
                    @if($order->tax_amount > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Thuế:</span>
                            <span class="font-medium">{{ number_format($order->tax_amount) }}₫</span>
                        </div>
                    @endif
                    <hr class="my-3">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Tổng cộng:</span>
                        <span>{{ number_format($order->total_amount) }}₫</span>
                    </div>
                    @if($order->points_earned > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Điểm tích lũy:</span>
                            <span class="font-medium text-green-600">+{{ $order->points_earned }} điểm</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            @if($order->status == 'awaiting_confirmation')
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Thao tác nhanh</h2>
                    <div class="space-y-3">
                        <button onclick="handleQuickAction({{ $order->id }}, 'confirm', this)" class="action-button w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            <span>✅ Xác nhận đơn hàng</span>
                        </button>
                        <button onclick="handleQuickAction({{ $order->id }}, 'cancel', this)" class="action-button w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                            <span>❌ Hủy đơn hàng</span>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Cancellation Info -->
            @if($order->cancellation)
                <div class="bg-white rounded-lg shadow-sm border border-red-200 p-6">
                    <h2 class="text-lg font-semibold text-red-900 mb-4">Thông tin hủy đơn</h2>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Lý do hủy:</p>
                            <p class="font-medium text-gray-900">{{ $order->cancellation->reason }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Người hủy:</p>
                            <p class="font-medium text-gray-900">{{ $order->cancellation->cancelledBy?->name ?? 'Không xác định' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Thời gian hủy:</p>
                            <p class="font-medium text-gray-900">{{ $order->cancellation->cancellation_date->format('d/m/Y H:i') }}</p>
                        </div>
                        @if($order->cancellation->notes)
                            <div>
                                <p class="text-sm text-gray-500">Ghi chú:</p>
                                <p class="font-medium text-gray-900">{{ $order->cancellation->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 z-50 hidden">
    <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-4 max-w-sm">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg id="toastIcon" class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div class="ml-3 w-0 flex-1">
                <p id="toastTitle" class="text-sm font-medium text-gray-900"></p>
                <p id="toastMessage" class="mt-1 text-sm text-gray-500"></p>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
                <button id="closeToast" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="{{ asset('vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
@endsection

@section('scripts')
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
    // Enable pusher logging - remove in production!
    Pusher.logToConsole = true;

    var pusher = new Pusher('2a1310e928036cd9f6d5', {
        cluster: 'ap1',
        encrypted: true
    });

    var orderId = @json($order->id);
    var channel = pusher.subscribe('private-order.' + orderId);
    channel.bind('OrderStatusUpdated', function(data) {
        showToast('🔄 Đơn hàng cập nhật', 'Trạng thái đơn hàng đã thay đổi. Đang tải lại...');
        setTimeout(function() {
            window.location.reload();
        }, 1200);
    });

function showToast(title, message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastIcon = document.getElementById('toastIcon');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');

    toastTitle.textContent = title;
    toastMessage.textContent = message;

    if (type === 'success') {
        toastIcon.className = 'text-green-500';
        toastIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
    } else if (type === 'error') {
        toastIcon.className = 'text-red-500';
        toastIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
    }

    toast.classList.remove('hidden');
    setTimeout(() => {
        toast.classList.add('hidden');
    }, 5000);
}

function handleQuickAction(orderId, action, buttonElement) {
    const statusMap = {
        'confirm': 'awaiting_driver',
        'in_transit': 'in_transit',
        'delivered': 'delivered',
        'cancel': 'cancelled'
    };
    const newStatus = statusMap[action];
    if (!newStatus) {
        showToast('❌ Lỗi', 'Hành động không hợp lệ', 'error');
        return;
    }

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('CSRF token not found');
        showToast('❌ Lỗi', 'Không tìm thấy CSRF token', 'error');
        return;
    }

    // Disable all buttons during processing
    const buttons = document.querySelectorAll('.action-button');
    console.log('Found buttons:', buttons.length);
    buttons.forEach(button => {
        button.disabled = true;
        button.classList.add('opacity-50', 'cursor-not-allowed');
    });

    // Show loading state
    const originalText = buttonElement.innerHTML;
    buttonElement.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Đang xử lý...';

    const requestBody = {
        status: newStatus,
        note: getActionNote(action)
    };

    console.log('Sending request:', {
        url: `/branch/orders/${orderId}/status`,
        method: 'POST',
        body: requestBody
    });

    fetch(`/branch/orders/${orderId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        },
        body: JSON.stringify(requestBody)
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            showToast('✅ Thành công', data.message);
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast('❌ Lỗi', data.message || 'Có lỗi xảy ra', 'error');
            // Re-enable buttons on error
            buttons.forEach(button => {
                button.disabled = false;
                button.classList.remove('opacity-50', 'cursor-not-allowed');
            });
            buttonElement.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showToast('❌ Lỗi', 'Có lỗi xảy ra khi cập nhật trạng thái: ' + error.message, 'error');
        // Re-enable buttons on error
        buttons.forEach(button => {
            button.disabled = false;
            button.classList.remove('opacity-50', 'cursor-not-allowed');
        });
        buttonElement.innerHTML = originalText;
    });
}

function getActionNote(action) {
    const notes = {
        'confirm': 'Xác nhận đơn hàng từ thao tác nhanh',
        'in_transit': 'Chuyển sang đang giao hàng từ thao tác nhanh',
        'delivered': 'Đã giao hàng từ thao tác nhanh',
        'cancel': 'Hủy đơn hàng từ thao tác nhanh'
    };
    return notes[action] || 'Thay đổi trạng thái từ thao tác nhanh';
}

function testButton() {
    console.log('Test button function called');
    showToast('🧪 Test', 'Button test successful!', 'success');
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, checking buttons...');
    
    // Check if buttons exist
    const buttons = document.querySelectorAll('.action-button');
    console.log('Found action buttons:', buttons.length);
    
    buttons.forEach((button, index) => {
        console.log(`Button ${index}:`, button.textContent.trim());
    });
    
    // Check CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    console.log('CSRF token found:', !!csrfToken);
    
    // Toast close functionality
    const closeToastBtn = document.getElementById('closeToast');
    if (closeToastBtn) {
        closeToastBtn.addEventListener('click', function() {
            document.getElementById('toast').classList.add('hidden');
        });
    }
});
</script>
@endsection