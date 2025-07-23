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
                        <p class="text-sm text-gray-600">{{ optional($order->branch)->name ?? 'N/A' }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <p class="text-sm text-gray-500">
                            <i class="far fa-calendar-alt mr-1"></i>
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
                        <i class="fas fa-credit-card text-gray-400"></i> Thanh toán:
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
                            <i class="fas fa-user text-gray-400"></i>
                            <span class="font-medium">{{ $order->customer_name }}</span>
                        </span>
                        <span class="flex items-center gap-2">
                            <i class="fas fa-phone text-gray-400"></i>
                            {{ $order->customer_phone }}
                        </span>
                    </div>
                    <div class="flex items-start gap-2 ml-1">
                        <i class="fas fa-map-marker-alt text-gray-400 mt-1"></i>
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
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium px-4 py-2 bg-gray-100 text-gray-800 hover:bg-gray-200">
                            <i class="fas fa-eye mr-1"></i> Chi tiết
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
                                <i class="fas fa-star mr-2"></i> Đánh giá
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
