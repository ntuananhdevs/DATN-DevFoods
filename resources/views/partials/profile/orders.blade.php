<section id="orders" class="mb-10">
    <h3 class="text-2xl font-bold mb-6">Đơn Hàng Gần Đây</h3>
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex justify-end items-center mb-1 mr-1">
            <a href="{{ route('customer.orders.index') }}" class="text-orange-500 hover:underline text-sm font-medium">Xem
                tất cả</a>
        </div>

        @forelse($recentOrders as $order)
            <div class="border border-gray-200 rounded-lg p-4 transition-shadow hover:shadow-sm mb-4">
                {{-- Header --}}
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center gap-4">
                        <h4 class="font-bold text-gray-900 text-lg">#{{ $order->order_code ?? $order->id }}</h4>
                        <p class="text-sm text-gray-600 flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            {{ optional($order->branch)->name ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <p class="text-sm text-gray-500 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ $order->order_date->format('d/m/Y H:i') }}
                        </p>
                        <span class="text-xs font-medium px-2 py-1 rounded-full"
                            style="background-color: {{ $order->status_color }}; color: {{ $order->status_text_color }};">
                            {{ $order->status_text }}
                        </span>
                    </div>
                </div>

                {{-- Trạng thái đơn & thanh toán --}}
                <div class="flex flex-wrap items-center gap-4 mb-3 text-sm">
                    {{-- <span>Trạng thái đặt hàng:
                        <span class="text-xs font-medium px-2 py-1 rounded-full"
                            style="background-color: {{ $order->status_color }}; color: {{ $order->status_text_color }};">
                            {{ $order->status_text }}
                        </span>
                    </span> --}}
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg> Thanh toán:
                        @if ($order->payment_status === 'completed')
                            <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded">Thành
                                công</span>
                        @elseif ($order->payment_status === 'pending')
                            <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-1 rounded">Chờ xử
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

                {{-- Thông tin người nhận --}}
                <div class="text-sm text-gray-700 mb-3">
                    <div class="flex flex-wrap items-center gap-6 mb-1">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="font-medium">{{ $order->customer_name }}</span>
                        </span>
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            {{ $order->customer_phone }}
                        </span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>{{ $order->delivery_address ?? 'Không có địa chỉ' }}</span>
                    </div>
                </div>

                {{-- Sản phẩm --}}
                <div class="mb-4">
                    <p class="text-sm font-semibold text-gray-800 mb-1">Sản phẩm:</p>
                    <ul class="list-disc ml-6 text-sm text-gray-700 space-y-1">
                        @foreach ($order->orderItems as $item)
                            <li>
                                {{ optional(optional($item->productVariant)->product)->name ?? (optional($item->combo)->name ?? 'Sản phẩm') }}
                                x{{ $item->quantity }} -
                                {{ number_format($item->unit_price, 0, ',', '.') }}đ
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Tổng tiền + hành động --}}
                <div class="flex justify-between items-center border-t pt-3">
                    <div class="text-xl font-bold text-orange-600">
                        {{ number_format($order->total_amount, 0, ',', '.') }}đ
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('customer.orders.show', $order) }}"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium px-4 py-2 bg-gray-100 text-gray-800 hover:bg-gray-200 border border-gray-300">
                            Chi tiết
                        </a>

                        @if ($order->status == 'awaiting_confirmation')
                            <form action="{{ route('customer.orders.updateStatus', $order) }}" method="POST"
                                class="cancel-order-form">
                                @csrf
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium px-4 py-2 border border-red-500 text-red-600 hover:bg-red-50">
                                    Hủy đơn
                                </button>
                            </form>
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
                            <a href="#"
                                class="inline-flex items-center justify-center rounded-md text-sm font-medium text-white px-4 py-2 bg-yellow-500 hover:bg-yellow-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg> Đánh giá
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 py-8">Bạn chưa có đơn hàng nào.</p>
        @endforelse
    </div>
</section>
