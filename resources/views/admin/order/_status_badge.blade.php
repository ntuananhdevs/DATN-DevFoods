@php
    $map = [
        'awaiting_confirmation' => ['Chờ xác nhận', 'bg-yellow-100 text-yellow-800'],
        'confirmed' => ['Đã xác nhận', 'bg-blue-100 text-blue-800'],
        'awaiting_driver' => ['Chờ tài xế', 'bg-blue-200 text-blue-900'],
        'driver_confirmed' => ['Tài xế đã xác nhận', 'bg-indigo-100 text-indigo-800'],
        'waiting_driver_pick_up' => ['Tài xế đang chờ đơn', 'bg-purple-100 text-purple-800'],
        'driver_picked_up' => ['Tài xế đã nhận đơn', 'bg-purple-200 text-purple-900'],
        'in_transit' => ['Đang giao', 'bg-cyan-100 text-cyan-800'],
        'delivered' => ['Đã giao', 'bg-green-100 text-green-800'],
        'item_received' => ['Khách đã nhận hàng', 'bg-green-200 text-green-900'],
        'cancelled' => ['Đã hủy', 'bg-red-100 text-red-800'],
        'refunded' => ['Đã hoàn tiền', 'bg-pink-100 text-pink-800'],
        'payment_failed' => ['Thanh toán thất bại', 'bg-red-200 text-red-900'],
        'payment_received' => ['Đã nhận thanh toán', 'bg-lime-100 text-lime-800'],
        'order_failed' => ['Đơn thất bại', 'bg-gray-300 text-gray-900'],
    ];
    [$label, $class] = $map[$status] ?? [
        'Không xác định (' . ($status ?? 'null') . ')',
        'bg-gray-100 text-gray-800'
    ];
@endphp
<span class="order-status-badge {{ $status }} inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $class }}">
    {{ $label }}
</span>