<div class="order-card bg-white rounded-lg shadow-sm border border-gray-200 h-full flex flex-col relative pb-16" data-order-id="{{ $order->id }}" data-order-date="{{ $order->created_at->toISOString() }}">
    <div class="p-2 flex flex-col h-full pb-2">
        <div class="flex items-start gap-3 mb-2">
            <input type="checkbox" class="order-checkbox mt-1 rounded" data-order-id="{{ $order->id }}">
            <div class="flex-1">
                <div class="flex justify-between items-center mb-1">
                    <div class="flex items-center gap-2">
                        <h3 class="font-semibold text-lg text-gray-900">#{{ $order->order_code ?? $order->id }}</h3>
                        @if($order->points_earned > 0)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                +{{ $order->points_earned }} điểm
                            </span>
                        @endif
                    </div>
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
                        $statusColor = $statusColors[$order->status] ?? '#6b7280';
                        $statusText = $statusTexts[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status));
                    @endphp
                    <div class="flex items-center gap-2">
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
                    </div>
                </div>
                <div class="flex items-center gap-2 mb-1">
                    <div class="flex items-center justify-center w-11 h-11 rounded-full bg-blue-100 text-blue-700 font-bold text-sm">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-100 my-2"></div>
        <div class="flex flex-col gap-1 text-sm flex-1">
            <div class="flex justify-between">
                <span class="text-gray-500">Tổng tiền:</span>
                <span class="font-semibold text-gray-900">{{ number_format($order->total_amount) }}₫</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Sản phẩm:</span>
                <span class="text-gray-700">
                    {{ $order->orderItems->sum('quantity') ?? 0 }} sản phẩm
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Thời gian:</span>
                <span class="text-gray-700">{{ $order->order_date->format('H:i') }}</span>
            </div>
            @if($order->estimated_delivery_time)
            <div class="flex justify-between">
                <span class="text-gray-500">Dự kiến giao:</span>
                <span class="font-medium text-green-600">{{ $order->estimated_delivery_time->diffForHumans() }}</span>
            </div>
            @endif
            <div class="flex justify-between">
                <span class="text-gray-500">Thanh toán:</span>
                @php $pm = strtolower($order->payment?->payment_method ?? ''); @endphp
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
            @if($order->notes)
            <div class="flex justify-between">
                <span class="text-gray-500">Note:</span>
                <span class="text-xs font-medium text-blue-700 bg-blue-50 rounded px-2 py-1 break-words max-w-[150px] truncate" title="{{ $order->notes }}">
                    {{ $order->notes }}
                </span>
            </div>
            @endif
        </div>
    </div>
    <div class="absolute left-0 bottom-0 w-full px-4 pb-3">
        <div class="flex gap-2 items-end">
            @if($order->status == 'awaiting_confirmation')
                <button data-quick-action="confirm" data-order-id="{{ $order->id }}" class="px-3 py-2 text-sm rounded-md bg-black text-white hover:bg-gray-800 confirm-btn" @if($order->status !== 'awaiting_confirmation') disabled @endif>
                    Xác nhận
                </button>
                <button data-quick-action="cancel" data-order-id="{{ $order->id }}" class="px-3 py-2 text-sm rounded-md bg-red-500 text-white hover:bg-red-600">
                    Hủy
                </button>
                <a href="{{ route('branch.orders.show', $order->id) }}" class="flex-1 px-3 py-2 text-sm rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 text-center">Chi tiết</a>
            @elseif($order->status === 'confirmed')
                <div class="flex w-full gap-2">
                    <button type="button" class="flex-1 px-3 py-2 text-sm rounded-md bg-gray-200 text-gray-700 flex items-center gap-2 cursor-default" disabled>
                        <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M12 2a10 10 0 0 1 10 10h-4a6 6 0 0 0-6-6V2z"></path>
                        </svg>
                        Đang tìm tài xế
                    </button>
                    <a href="{{ route('branch.orders.show', $order->id) }}" class="flex-1 px-3 py-2 text-sm rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 text-center">Chi tiết</a>
                </div>
            @else
                <a href="{{ route('branch.orders.show', $order->id) }}" class="px-3 py-2 text-sm rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100">Chi tiết</a>
            @endif
        </div>
    </div>
</div> 