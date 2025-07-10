@php
    $map = [
        'awaiting_confirmation' => ['Chờ xác nhận', 'bg-yellow-100 text-yellow-800'],
        'order_confirmed' => ['Đang chuẩn bị', 'bg-blue-100 text-blue-800'],
        'in_transit' => ['Đang giao', 'bg-purple-100 text-purple-800'],
        'delivered' => ['Hoàn thành', 'bg-green-100 text-green-800'],
        'cancelled' => ['Đã hủy', 'bg-red-100 text-red-800'],
    ];
    [$label, $class] = $map[$status] ?? ['Không xác định', 'bg-gray-100 text-gray-800'];
@endphp
<span class="px-2 py-1 rounded text-xs font-semibold {{ $class }}">
    {{ $label }}
</span> 