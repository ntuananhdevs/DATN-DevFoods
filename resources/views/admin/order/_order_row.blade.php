<tr class="border-b" data-order-id="{{ $order->id }}">
    <td class="py-3 px-4 font-medium">#{{ $order->order_code }}</td>
    <td class="py-3 px-4">
        <div class="flex items-center gap-2">
            <img src="{{ $order->customer->avatar_url ?? '/images/default-avatar.png' }}" alt="avatar" class="w-8 h-8 rounded-full border object-cover">
            <div>
                <span class="font-medium">{{ $order->customer->name ?? 'Khách lẻ' }}</span><br>
                <span class="text-xs text-gray-400">{{ $order->customer->phone ?? '' }}</span>
            </div>
        </div>
    </td>
    <td class="py-3 px-4">{{ $order->branch->name ?? 'Không có chi nhánh' }}</td>
    <td class="py-3 px-4 text-right font-bold">{{ number_format($order->total_amount) }}đ</td>
    <td class="py-3 px-4">
        <span class="order-status-badge {{ $order->status }}">
            @php
                $map = [
                    'awaiting_confirmation' => 'Chờ xác nhận',
                    'order_confirmed' => 'Đang chuẩn bị',
                    'in_transit' => 'Đang giao',
                    'delivered' => 'Hoàn thành',
                    'cancelled' => 'Đã hủy',
                    'refunded' => 'Đã hoàn tiền',
                ];
                echo $map[$order->status] ?? 'Không xác định';
            @endphp
        </span>
    </td>
    <td class="py-3 px-4">
        <div class="text-sm text-gray-900">{{ $order->created_at ? $order->created_at->format('h:i A') : 'N/A' }}</div>
        <div class="text-xs text-gray-500">{{ $order->created_at ? $order->created_at->format('d/m/Y') : 'N/A' }}</div>
    </td>
    <td class="py-3 px-4 text-center">
        <div class="flex gap-2 items-center justify-center">
            <a href="{{ route('admin.orders.show', $order->id) }}" class="flex items-center gap-1 px-3 py-1 border border-gray-300 rounded-lg text-primary bg-white hover:bg-gray-100 transition text-sm font-medium">
                <i class="fa fa-eye"></i> Chi tiết
            </a>
            @if($order->status === 'awaiting_confirmation')
            <div class="relative">
                <button class="px-3 py-1 border border-gray-300 rounded-lg bg-white text-gray-700 hover:bg-gray-100 transition text-sm font-medium flex items-center gap-1">Cập nhật <i class="fa fa-chevron-down text-xs"></i></button>
                {{-- Dropdown cập nhật trạng thái (nếu cần) --}}
            </div>
            @endif
        </div>
    </td>
</tr>