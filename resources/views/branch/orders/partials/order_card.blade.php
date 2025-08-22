@php
    $statusColors = [
        'awaiting_confirmation' => 'bg-yellow-500 text-white',
        'confirmed' => 'bg-blue-500 text-white',
        'awaiting_driver' => 'bg-blue-400 text-white',
        'driver_assigned' => 'bg-indigo-500 text-white',
        'driver_confirmed' => 'bg-indigo-600 text-white',
        'waiting_driver_pick_up' => 'bg-purple-400 text-white',
        'driver_picked_up' => 'bg-purple-500 text-white',
        'in_transit' => 'bg-orange-500 text-white',
        'delivered' => 'bg-green-500 text-white',
        'item_received' => 'bg-green-600 text-white',
        'cancelled' => 'bg-gray-400 text-white',
        'refunded' => 'bg-pink-500 text-white',
        'payment_failed' => 'bg-red-500 text-white',
        'payment_received' => 'bg-green-700 text-white',
        'order_failed' => 'bg-red-600 text-white',
    ];
    $statusTexts = [
        'awaiting_confirmation' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'awaiting_driver' => 'Chờ tài xế',
        'driver_assigned' => 'Đã gán tài xế',
        'driver_confirmed' => 'Tài xế đã xác nhận',
        'waiting_driver_pick_up' => 'Chờ tài xế lấy hàng',
        'driver_picked_up' => 'Tài xế đã nhận đơn',
        'in_transit' => 'Đang giao',
        'delivered' => 'Đã giao',
        'item_received' => 'Đã nhận hàng',
        'cancelled' => 'Đã hủy',
        'refunded' => 'Đã hoàn tiền',
        'payment_failed' => 'Thanh toán thất bại',
        'payment_received' => 'Đã nhận thanh toán',
        'order_failed' => 'Đơn thất bại',
    ];
    $pm = strtolower($order->payment?->payment_method ?? '');
    $ps = $order->payment?->payment_status ?? 'pending';
    $paymentStatusColors = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'completed' => 'bg-green-100 text-green-800',
        'failed' => 'bg-red-100 text-red-800',
        'refunded' => 'bg-pink-100 text-pink-800'
    ];
    $paymentStatusText = [
        'pending' => 'Chờ xử lý',
        'completed' => 'Thành công',
        'failed' => 'Thất bại',
        'refunded' => 'Đã hoàn tiền'
    ];
@endphp

<tr class="order-row bg-white border-b border-gray-200 hover:bg-gray-50" data-order-id="{{ $order->id }}" data-order-date="{{ $order->created_at->toISOString() }}">
    <!-- Checkbox -->
    <td class="px-4 py-3">
        <input type="checkbox" class="order-checkbox rounded" data-order-id="{{ $order->id }}">
    </td>
    
    <!-- Mã đơn hàng -->
    <td class="px-4 py-3">
        <div class="flex items-center gap-2">
            <span class="font-semibold text-gray-900">#{{ $order->order_code ?? $order->id }}</span>
           
        </div>
    </td>
    
    <!-- Khách hàng -->
    <td class="px-4 py-3">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-700 font-bold text-sm">
                {{ strtoupper(mb_substr($order->customerName ?? 'U', 0, 1)) }}
            </div>
            <div>
                <div class="font-semibold text-gray-900">{{ $order->customerName }}</div>
                <div class="text-sm text-gray-500">{{ $order->customerPhone }}</div>
            </div>
        </div>
    </td>
    
    <!-- Trạng thái -->
    <td class="px-4 py-3">
        @if($order->status === 'confirmed')
            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-md bg-blue-100 text-blue-700">
                <svg class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M12 2a10 10 0 0 1 10 10h-4a6 6 0 0 0-6-6V2z"></path>
                </svg>
                Đang tìm tài xế
            </span>
        @else
            <span class="px-2 py-1 text-xs font-medium rounded-md status-badge {{ $statusColors[$order->status] ?? 'bg-gray-300 text-gray-700' }}">
                {{ $statusTexts[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status)) }}
            </span>
        @endif
    </td>
    
    <!-- Tổng tiền -->
    <td class="px-4 py-3">
        <span class="font-semibold text-gray-900">{{ number_format($order->total_amount) }}₫</span>
    </td>
    
    <!-- Sản phẩm -->
    <td class="px-4 py-3 text-center">
        <span class="text-gray-700">{{ $order->orderItems->sum('quantity') ?? 0 }}</span>
    </td>
    
    <!-- Thời gian -->
    <td class="px-4 py-3">
        <div class="text-sm">
            <div class="text-gray-900">{{ $order->order_date->format('H:i') }}</div>
            <div class="text-gray-500">{{ $order->order_date->format('d/m/Y') }}</div>
        </div>
    </td>
    

    
    <!-- Thanh toán -->
    <td class="px-4 py-3">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-1">
                @if($pm === 'cod')
                    <span class="inline-block px-2 py-0.5 rounded bg-green-700 text-white text-xs font-semibold">COD</span>
                @elseif($pm === 'vnpay')
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-blue-100 text-blue-800 text-xs font-semibold">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 16" style="height:1em;width:auto;display:inline;vertical-align:middle;" aria-label="VNPAY Icon">
                            <text x="0" y="12" font-size="12" font-family="Arial, Helvetica, sans-serif" font-weight="bold" fill="#e30613">VN</text>
                            <text x="18" y="12" font-size="12" font-family="Arial, Helvetica, sans-serif" font-weight="bold" fill="#0072bc">PAY</text>
                        </svg>
                    </span>
                @elseif($pm === 'balance')
                    <span class="inline-block px-2 py-1 rounded bg-purple-100 text-purple-700 text-xs font-semibold">Số dư</span>
                @endif
            </div>
            <span class="payment-status-badge inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $paymentStatusColors[$ps] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $paymentStatusText[$ps] ?? ucfirst($ps) }}
            </span>
        </div>
    </td>

    <!-- Thao tác -->
    <td class="px-4 py-3">
        <div class="flex gap-2">
            @if($order->status == 'awaiting_confirmation')
                <button data-quick-action="confirm" data-order-id="{{ $order->id }}" class="px-3 py-1 text-xs rounded-md bg-black text-white hover:bg-gray-800 confirm-btn" @if($order->status !== 'awaiting_confirmation') disabled @endif>
                    Xác nhận
                </button>
                <button data-quick-action="cancel" data-order-id="{{ $order->id }}" class="px-3 py-1 text-xs rounded-md bg-red-500 text-white hover:bg-red-600">
                    Hủy
                </button>
            @elseif($order->status === 'confirmed')
                <button type="button" class="px-3 py-1 text-xs rounded-md bg-gray-200 text-gray-700 cursor-default" disabled>
                    Đang tìm tài xế
                </button>
            @endif
            <a href="{{ route('branch.orders.show', $order->id) }}" class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100">
                Chi tiết
            </a>
        </div>
    </td>
</tr>