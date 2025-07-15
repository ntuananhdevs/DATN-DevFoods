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
    <td class="py-3 px-4 text-left">
        @php
            $payment = $order->payment;
            $pm = $payment->payment_method ?? null;
            $paymentMethodMap = [
                'cod' => 'COD',
                'vnpay' => 'VNPay',
                'balance' => 'Số dư',
            ];
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
        @elseif($pm === 'balance')
            <span class="inline-block px-2 py-0.5 rounded bg-yellow-100 text-yellow-800 text-xs font-semibold">Số dư</span>
        @else
            <span class="inline-block px-2 py-0.5 rounded bg-gray-100 text-gray-800 text-xs font-semibold">{{ $pmLabel }}</span>
        @endif
    </td>
    <td class="py-3 px-4">
        <span class="order-status-badge {{ $order->status }}">
            @php
                $map = [
                    'awaiting_confirmation' => 'Chờ xác nhận',
                    'confirmed' => 'Đã xác nhận',
                    'awaiting_driver' => 'Chờ tài xế',
                    'driver_confirmed' => 'Tài xế đã xác nhận',
                    'waiting_driver_pick_up' => 'Tài xế đang chờ đơn',
                    'driver_picked_up' => 'Tài xế đã nhận đơn',
                    'in_transit' => 'Đang giao',
                    'delivered' => 'Đã giao',
                    'item_received' => 'Khách đã nhận hàng',
                    'cancelled' => 'Đã hủy',
                    'refunded' => 'Đã hoàn tiền',
                    'payment_failed' => 'Thanh toán thất bại',
                    'payment_received' => 'Đã nhận thanh toán',
                    'order_failed' => 'Đơn thất bại',
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