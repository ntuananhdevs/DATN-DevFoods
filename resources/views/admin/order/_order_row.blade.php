<tr class="order-row border-b" data-order-id="{{ $order->id }}">
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
                'cash' => 'Tiền mặt',
                'cod' => 'COD',
                'vnpay' => 'VNPay',
                'momo' => 'MoMo',
                'bank_transfer' => 'Chuyển khoản',
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
        @elseif($pm === 'momo')
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-pink-100 text-pink-800 text-xs font-semibold">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
                MoMo
            </span>
        @elseif($pm === 'cod' || $pm === 'cash')
            <span class="inline-block px-2 py-0.5 rounded bg-green-100 text-green-800 text-xs font-semibold">{{ $pmLabel }}</span>
        @elseif($pm === 'bank_transfer')
            <span class="inline-block px-2 py-0.5 rounded bg-purple-100 text-purple-800 text-xs font-semibold">{{ $pmLabel }}</span>
        @elseif($pm === 'balance')
            <span class="inline-block px-2 py-0.5 rounded bg-yellow-100 text-yellow-800 text-xs font-semibold">{{ $pmLabel }}</span>
        @else
            <span class="inline-block px-2 py-0.5 rounded bg-gray-100 text-gray-800 text-xs font-semibold">{{ $pmLabel }}</span>
        @endif
    </td>
    <td class="py-3 px-4">
        @php
            $paymentStatus = $order->payment->payment_status ?? 'pending';
        @endphp
        <span class="order-payment-status payment-status-badge {{ $paymentStatus }}">
            {{ $order->payment->payment_status_text ?? 'Chưa thanh toán' }}
        </span>
    </td>
    <td class="py-3 px-4">
        @include('admin.order._status_badge', ['status' => $order->status])
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
        </div>
    </td>
</tr>