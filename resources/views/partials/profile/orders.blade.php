<section id="orders" class="mb-10">
    <h3 class="text-2xl font-bold mb-6">Đơn Hàng Gần Đây</h3>
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex justify-end items-center mb-1 mr-1">
            <a href="{{ route('customer.orders.index') }}" class="text-orange-500 hover:underline text-sm font-medium">Xem
                tất cả</a>
        </div>
        @forelse($recentOrders as $order)
            <div class="border border-orange-200 rounded-lg p-4 transition-shadow hover:shadow-md mb-2">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h4 class="font-semibold text-gray-800">
                            #{{ $order->order_code ?? $order->id }}</h4>
                        <p class="text-sm text-gray-500">{{ $order->order_date->diffForHumans() }}</p>
                    </div>
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full"
                        style="background-color: {{ $order->status_color }}; color: {{ $order->status_text_color }};">
                        {{ $order->status_text }}
                    </span>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-gray-500 mb-1 font-medium">
                        {{ optional($order->branch)->name ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-700">
                        {{ $order->orderItems->map(fn($item) => (optional(optional($item->productVariant)->product)->name ?? 'Sản phẩm') . ' x' . $item->quantity)->implode(', ') }}
                    </p>
                </div>
                <div class="flex justify-between items-center">
                    <span
                        class="font-semibold text-lg text-orange-600">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                    <div class="flex space-x-2">
                        <a href="{{ route('customer.orders.show', $order) }}"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium h-9 px-4 py-2 bg-gray-100 text-gray-800 hover:bg-gray-200">Chi
                            tiết</a>
                        @if ($order->status == 'awaiting_confirmation')
                            <form action="{{ route('customer.orders.updateStatus', $order) }}" method="POST"
                                class="cancel-order-form">
                                @csrf
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium h-9 px-4 py-2 border border-red-500 text-red-600 hover:bg-red-50">Hủy
                                    đơn</button>
                            </form>
                        @elseif($order->status == 'delivered')
                            <a href="{{ route('customer.orders.updateStatus', $order) }}"
                                class="inline-flex items-center justify-center rounded-md text-sm font-medium h-10 px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200">Chưa
                                nhận được hàng</a>
                            <form class="receive-order-form"
                                action="{{ route('customer.orders.updateStatus', $order) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="item_received">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium text-white h-10 px-4 py-2 bg-orange-500 hover:bg-orange-600">Xác
                                    nhận đã nhận hàng</button>
                            </form>
                        @elseif($order->status == 'item_received')
                            <a href="#"
                                class="inline-flex items-center justify-center rounded-md text-sm font-medium text-white h-10 px-4 py-2 bg-yellow-500 hover:bg-yellow-600">
                                <i class="fas fa-star mr-2"></i>Đánh giá
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
