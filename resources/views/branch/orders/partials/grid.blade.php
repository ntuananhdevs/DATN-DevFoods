<div id="ordersGrid" class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @forelse($orders as $order)
        <div class="order-card bg-white rounded-lg shadow-sm border border-gray-200 h-full flex flex-col relative pb-16" data-order-id="{{ $order->id }}">
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
                                    'awaiting_confirmation' => '#f59e0b',
                                    'awaiting_driver' => '#3b82f6',
                                    'driver_picked_up' => '#8b5cf6',
                                    'in_transit' => '#06b6d4',
                                    'delivered' => '#10b981',
                                    'item_received' => '#059669',
                                    'cancelled' => '#ef4444',
                                    'refunded' => '#6b7280',
                                ];
                                $statusTexts = [
                                    'awaiting_confirmation' => 'Chờ xác nhận',
                                    'awaiting_driver' => 'Đang chờ tài xế',
                                    'driver_picked_up' => 'Tài xế đã nhận',
                                    'in_transit' => 'Đang giao',
                                    'delivered' => 'Đã giao',
                                    'item_received' => 'Đã nhận',
                                    'cancelled' => 'Đã hủy',
                                    'refunded' => 'Đã hoàn tiền',
                                ];
                                $statusColor = $statusColors[$order->status] ?? '#6b7280';
                                $statusText = $statusTexts[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status));
                            @endphp
                            <span class="status-badge text-white rounded-lg px-2 py-1 text-xs font-medium" style="background-color: {{ $statusColor }}">
                                {{ $statusText }}
                            </span>
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
                        <span class="text-xs font-medium text-blue-700 bg-blue-50 rounded px-2 py-1 break-words" style="max-height:2rem;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;white-space:normal;">
                            {{ $order->notes }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            <div class="absolute left-0 bottom-0 w-full px-4 pb-3">
                <div class="flex gap-2 items-end">
                    @if($order->status == 'awaiting_confirmation')
                        <button data-quick-action="confirm" data-order-id="{{ $order->id }}" class="px-3 py-2 text-sm rounded-md bg-black text-white hover:bg-gray-800">
                            Xác nhận
                        </button>
                        <button data-quick-action="cancel" data-order-id="{{ $order->id }}" class="px-3 py-2 text-sm rounded-md bg-red-500 text-white hover:bg-red-600">
                            Hủy
                        </button>
                    @endif
                    <a href="{{ route('branch.orders.show', $order->id) }}" class="flex-1">
                        <button class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Chi tiết
                        </button>
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-3">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-8">
                <div class="p-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h3 class="text-lg font-medium mb-2 text-gray-900">Không có đơn hàng</h3>
                    <p class="text-gray-500">Không tìm thấy đơn hàng phù hợp với bộ lọc hiện tại</p>
                </div>
            </div>
        </div>
    @endforelse
</div>
<div id="ordersPagination">
    @if($orders->hasPages())
        <div class="mt-6">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    @endif
</div> 