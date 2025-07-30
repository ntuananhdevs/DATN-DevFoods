<section id="orders" class="mb-10">
    <h3 class="text-2xl font-bold mb-2">Đơn Hàng Gần Đây</h3>
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex justify-end items-center mb-1 mr-1">
            <a href="{{ route('customer.orders.index') }}" class="text-orange-500 hover:underline text-sm font-medium">Xem
                tất cả</a>
        </div>

        {{-- Debug: Kiểm tra số lượng orders --}}
        @php
            $orderCount = $recentOrders ? $recentOrders->count() : 0;
        @endphp
        <script>
            console.log('📊 Số lượng orders từ backend:', {{ $orderCount }});
        </script>
        
        @forelse($recentOrders as $order)
            <div class="border border-gray-200 rounded-lg p-4 transition-shadow hover:shadow-sm mb-4" data-order-id="{{ $order->id }}">
                {{-- Header --}}
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center gap-4">
                        <h4 class="font-bold text-orange-600 text-lg">#{{ $order->order_code ?? $order->id }}</h4>
                        <p class="text-sm text-gray-600 flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            {{ optional($order->branch)->name ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <p class="text-sm text-gray-500 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            {{ $order->order_date->format('d/m/Y H:i') }}
                        </p>

                        {{-- Sử dụng optional() và format() để xử lý ngày tháng tốt hơn --}}
                        <p class="delivery-time text-sm text-gray-500 flex items-center">
                            Dự kiến giao:
                            {{ optional($order->estimated_delivery_time)->format('H:i') ?? 'N/A' }}
                        </p>

                        <span class="status-badge text-xs font-medium px-2 py-1 rounded-full"
                            style="background-color: {{ $order->status_color }}; color: {{ $order->status_text_color }};">
                            {{ $order->status_text }}
                        </span>
                    </div>
                </div>

                {{-- Trạng thái đơn & thanh toán --}}
                <div class="flex flex-wrap justify-between items-center gap-4 mb-3 text-sm">
                    <div class="flex items-center gap-1">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                </path>
                            </svg> Thanh toán:
                            @if ($order->payment_status === 'completed')
                                <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded">Thành
                                    công</span>
                            @elseif ($order->payment_status === 'pending')
                                <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-1 rounded">Chờ
                                    xử
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

                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-1">
                            <span class="text-gray-600">Dự kiến giao:</span>
                            <span class="font-semibold text-blue-600">
                                @if($order->estimated_delivery_time)
                                    @php
                                        $orderTime = \Carbon\Carbon::parse($order->created_at);
                                        $estimatedTime = \Carbon\Carbon::parse($order->estimated_delivery_time);
                                        $deliveryDurationMinutes = $orderTime->diffInMinutes($estimatedTime);
                                    @endphp
                                    {{ $deliveryDurationMinutes }} phút
                                @else
                                    Đang xử lý
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="text-gray-600">Phí giao hàng:</span>
                            <span class="font-semibold text-gray-900">
                                {{ number_format($order->delivery_fee, 0, ',', '.') }}đ
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Thông tin người nhận --}}
                <div class="text-sm text-gray-700 mb-3">
                    <div class="flex flex-wrap items-center gap-6 mb-1">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="font-medium">{{ $order->display_recipient_name }}</span>
                        </span>
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            {{ $order->display_delivery_phone }}
                        </span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-gray-400 mt-1" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>{{ $order->display_full_delivery_address ?? 'Không có địa chỉ' }}</span>
                    </div>
                </div>

                {{-- Sản phẩm --}}
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Sản phẩm đã đặt:</h3>
                    
                    <div class="space-y-3">
                        @foreach ($order->orderItems as $item)
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                {{-- Thông tin sản phẩm --}}
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-start gap-3 flex-1">
                                        {{-- Hình ảnh sản phẩm --}}
                                        <div class="w-11 h-11 bg-gray-200 rounded-md overflow-hidden flex-shrink-0">
                                            @if ($item->productVariant && $item->productVariant->product && $item->productVariant->product->images->count() > 0)
                                                <img src="{{ asset('images/products/' . $item->productVariant->product->images->first()->image_url) }}" 
                                                     alt="{{ $item->product_name_snapshot ?? $item->productVariant->product->name }}" 
                                                     class="w-full h-full object-cover">
                                            @elseif ($item->combo && $item->combo->image)
                                                <img src="{{ asset('images/combos/' . $item->combo->image) }}" 
                                                     alt="{{ $item->combo_name_snapshot ?? $item->combo->name }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-gray-300 flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        {{-- Thông tin sản phẩm --}}
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 text-sm">
                                                @if ($item->product_name_snapshot)
                                                    {{ $item->product_name_snapshot }}
                                                    @if ($item->variant_name_snapshot)
                                                        <span class="text-gray-600">({{ $item->variant_name_snapshot }})</span>
                                                    @endif
                                                @elseif ($item->combo_name_snapshot)
                                                    {{ $item->combo_name_snapshot }}
                                                @else
                                                    {{ optional(optional($item->productVariant)->product)->name ?? (optional($item->combo)->name ?? 'Sản phẩm') }}
                                                @endif
                                            </h4>
                                            <div class="text-sm text-gray-600 mt-1">
                                                Số lượng: {{ $item->quantity }} | Đơn giá: {{ number_format($item->unit_price, 0, ',', '.') }}đ
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-orange-600">
                                            @php
                                                $totalItemPrice = $item->unit_price * $item->quantity;
                                                $totalToppingPrice = 0;
                                                foreach ($item->toppings as $topping) {
                                                    $totalToppingPrice += ($topping->topping_unit_price_snapshot ?? $topping->unit_price) * $item->quantity;
                                                }
                                                $finalPrice = $totalItemPrice + $totalToppingPrice;
                                            @endphp
                                            {{ number_format($finalPrice, 0, ',', '.') }}đ
                                        </p>
                                        @if ($item->toppings->count() > 0)
                                            <p class="text-xs text-gray-500 mt-1">
                                                (Đã bao gồm topping)
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Topping --}}
                                @if ($item->toppings->count() > 0)
                                    <div class="mt-3 pt-2 border-t border-gray-300">
                                        <p class="text-xs font-medium text-orange-600 mb-2">Topping:</p>
                                        <div class="ms-3 space-y-1">
                                            @foreach ($item->toppings as $topping)
                                                <div class="flex justify-between items-center text-xs">
                                                    <span class="text-gray-600">
                                                        • {{ $topping->topping_name_snapshot ?? optional($topping->topping)->name }}
                                                        @if ($item->quantity > 1)
                                                            (x{{ $item->quantity }})
                                                        @endif
                                                    </span>
                                                    <span class="font-medium text-green-600">
                                                        +{{ number_format($topping->topping_unit_price_snapshot ?? $topping->unit_price, 0, ',', '.') }}đ
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Tổng tiền + hành động --}}
                <div class="flex justify-between items-center border-t pt-3 border-gray-300">
                    <div class="text-md font-medium">
                        <span class="text-gray-700">Tổng đơn hàng (x{{ $order->orderItems->sum('quantity') }} sản phẩm) :</span>
                        <span class="text-orange-600">{{ number_format($order->total_amount, 0, ',', '.') }} đ</span>
                    </div>

                    <div class="order-actions flex items-center gap-2">
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
                            <a href="#" {{-- Cân nhắc tạo một route thực tế cho việc đánh giá --}}
                                class="inline-flex items-center justify-center rounded-md text-sm font-medium text-white px-4 py-2 bg-yellow-500 hover:bg-yellow-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                                    </path>
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

@push('scripts')
<script>
// Real-time order status updates
class CustomerOrderRealtime {
    constructor() {
        this.pusher = null;
        this.channels = new Map();
        this.pollingInterval = null;
        this.initializePusher();
        this.subscribeToOrderChannels();
    }

    initializePusher() {
        console.log('🚀 initializePusher() được gọi');
        alert('Debug: initializePusher được chạy');
        
        try {
            // Use Laravel config with proper syntax
            const pusherKey = @json(config('broadcasting.connections.pusher.key'));
            const pusherCluster = @json(config('broadcasting.connections.pusher.options.cluster'));
            
            console.log('🔑 Pusher Key:', pusherKey);
            console.log('🌐 Pusher Cluster:', pusherCluster);
            console.log('📋 Full config:', { key: pusherKey, cluster: pusherCluster });
            
            if (!pusherKey || !pusherCluster) {
                console.error('Pusher configuration missing');
                this.setupPollingFallback();
                return;
            }

            this.pusher = new Pusher(pusherKey, {
                cluster: pusherCluster,
                encrypted: true,
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }
            });

            this.pusher.connection.bind('connected', () => {
                console.log('✅ Connected to Pusher successfully');
            });

            this.pusher.connection.bind('error', (err) => {
                console.error('❌ Pusher connection error:', err);
                this.setupPollingFallback();
            });

            this.pusher.connection.bind('disconnected', () => {
                console.log('⚠️ Pusher disconnected');
            });
        } catch (error) {
            console.error('Failed to initialize Pusher:', error);
            this.setupPollingFallback();
        }
    }

    subscribeToOrderChannels() {
        // Subscribe to each order's private channel
        @foreach($recentOrders as $order)
            this.subscribeToOrderChannel({{ $order->id }});
        @endforeach
    }

    subscribeToOrderChannel(orderId) {
        const channelName = `private-order.${orderId}`;
        
        try {
            const channel = this.pusher.subscribe(channelName);
            this.channels.set(orderId, channel);

            channel.bind('pusher:subscription_succeeded', () => {
                console.log(`Subscribed to order ${orderId} channel`);
            });

            channel.bind('pusher:subscription_error', (status) => {
                console.error(`Failed to subscribe to order ${orderId} channel:`, status);
            });

            channel.bind('OrderStatusUpdated', (data) => {
                this.handleOrderStatusUpdate(orderId, data);
            });
        } catch (error) {
            console.error(`Error subscribing to order ${orderId} channel:`, error);
        }
    }

    handleOrderStatusUpdate(orderId, data) {
        console.log('Order status updated:', orderId, data);
        
        // Find the order element
        const orderElement = document.querySelector(`[data-order-id="${orderId}"]`);
        if (!orderElement) {
            console.warn(`Order element not found for order ${orderId}`);
            return;
        }

        // Update status badge
        const statusBadge = orderElement.querySelector('.status-badge');
        if (statusBadge && data.status_text) {
            statusBadge.textContent = data.status_text;
            if (data.status_color) {
                statusBadge.style.backgroundColor = data.status_color;
            }
        }

        // Update delivery time if provided
        if (data.actual_delivery_time) {
            const deliveryTimeElement = orderElement.querySelector('.delivery-time');
            if (deliveryTimeElement) {
                deliveryTimeElement.textContent = data.actual_delivery_time;
            }
        }

        // Show notification
        this.showNotification(orderId, data);

        // Update action buttons based on new status
        this.updateActionButtons(orderElement, data.status);
    }

    updateActionButtons(orderElement, newStatus) {
        const actionContainer = orderElement.querySelector('.order-actions');
        if (!actionContainer) return;

        // Remove existing action buttons except detail button
        const existingForms = actionContainer.querySelectorAll('form');
        existingForms.forEach(form => form.remove());
        
        // Remove existing review button if any
        const existingReviewBtn = actionContainer.querySelector('a[href="#"]');
        if (existingReviewBtn && existingReviewBtn.textContent.includes('Đánh giá')) {
            existingReviewBtn.remove();
        }

        // Add appropriate buttons based on new status
        if (newStatus === 'delivered') {
            // Add "Xác nhận đã nhận hàng" button
            const receiveForm = this.createReceiveOrderForm(orderElement.dataset.orderId);
            actionContainer.appendChild(receiveForm);
        } else if (newStatus === 'item_received') {
            // Add "Đánh giá" button
            const reviewButton = this.createReviewButton();
            actionContainer.appendChild(reviewButton);
        }
    }

    createReceiveOrderForm(orderId) {
        const form = document.createElement('form');
        form.className = 'receive-order-form flex gap-2';
        form.action = `/customer/orders/${orderId}/status`;
        form.method = 'POST';
        
        // Create CSRF token input
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Create status input
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = 'item_received';
        
        // Create button
        const button = document.createElement('button');
        button.type = 'submit';
        button.className = 'inline-flex items-center justify-center rounded-md text-sm font-medium text-white px-4 py-2 bg-orange-500 hover:bg-orange-600';
        button.textContent = 'Xác nhận đã nhận hàng';
        
        form.appendChild(csrfInput);
        form.appendChild(statusInput);
        form.appendChild(button);
        
        return form;
    }

    createReviewButton() {
        const button = document.createElement('a');
        button.href = '#';
        button.className = 'inline-flex items-center justify-center rounded-md text-sm font-medium text-white px-4 py-2 bg-yellow-500 hover:bg-yellow-600';
        button.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
            </svg> Đánh giá
        `;
        
        return button;
    }

    showNotification(orderId, data) {
        // Create a simple notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300';
        notification.innerHTML = `
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <div class="font-medium">Cập nhật đơn hàng</div>
                    <div class="text-sm opacity-90">Đơn hàng #${orderId} đã chuyển sang ${data.status_text}</div>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }

    setupPollingFallback() {
        console.log('Setting up polling fallback for order updates');
        // Poll for order status updates every 30 seconds as fallback
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }
        
        this.pollingInterval = setInterval(() => {
            // You can implement a simple AJAX call to check for order updates
            // For now, just log that polling is active
            console.log('Polling for order updates...');
        }, 30000);
    }

    destroy() {
        // Unsubscribe from all channels
        this.channels.forEach((channel, orderId) => {
            this.pusher.unsubscribe(`private-order.${orderId}`);
        });
        this.channels.clear();
        
        // Disconnect Pusher
        if (this.pusher) {
            this.pusher.disconnect();
            this.pusher = null;
        }
        
        // Clear polling interval
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 DOM Content Loaded');
    console.log('📦 Pusher available:', typeof Pusher !== 'undefined');
    console.log('🔍 Order elements found:', document.querySelectorAll('[data-order-id]').length);
    
    // Test Pusher initialization (tạm thời bỏ điều kiện kiểm tra orders)
    if (typeof Pusher !== 'undefined') {
        console.log('✅ Khởi tạo CustomerOrderRealtime');
        window.customerOrderRealtime = new CustomerOrderRealtime();
    } else {
        console.log('❌ Không thể khởi tạo CustomerOrderRealtime - Pusher không có sẵn');
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (window.customerOrderRealtime) {
        window.customerOrderRealtime.destroy();
    }
});
</script>
@endpush
