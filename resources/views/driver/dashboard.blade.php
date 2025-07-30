@extends('layouts.driver.masterLayout')

@section('title', 'Dashboard Tài xế')

@section('content')
    <div class="pt-4 p-4 space-y-4">
        {{-- Card Header --}}
        <div class="bg-white rounded-lg p-4 shadow-sm">
            {{-- CẬP NHẬT: Thêm 'flex-wrap' để các phần tử có thể xuống dòng trên màn hình siêu nhỏ --}}
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center space-x-3">
                    <div
                        class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden flex-shrink-0">
                        @if ($driver->avatar)
                            <img src="{{ Storage::disk('s3')->url($driver->avatar) }}" alt="Avatar"
                                class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-user text-gray-600 text-xl"></i>
                        @endif
                    </div>
                    <div>
                        <h2 class="font-semibold text-lg">{{ $driver->full_name }}</h2>
                        <div class="flex items-center space-x-2">
                            <span
                                class="driver-status-text text-sm text-gray-500">{{ $driver->is_available ? 'Bạn đang Online' : 'Bạn đang Offline' }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="#" class="relative text-gray-500 hover:text-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-bell animate-bell">
                            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
                            <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
                        </svg>
                        {{-- Ví dụ về notification badge --}}
                        {{-- <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] rounded-full w-4 h-4 flex items-center justify-center border-2 border-white">5</span> --}}
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-800"><svg xmlns="http://www.w3.org/2000/svg"
                            width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings">
                            <path
                                d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z">
                            </path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg></a>
                </div>
            </div>
        </div>

        {{-- Card Trạng thái làm việc (giữ nguyên, đã ổn) --}}
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-medium">Trạng thái làm việc</h3>
                    <p class="driver-status-text text-sm text-gray-500">
                        {{ $driver->is_available ? 'Bạn đang Online' : 'Bạn đang Offline' }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer" id="statusToggle"
                        @if ($driver->is_available) checked @endif>
                    <div
                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600">
                    </div>
                </label>
            </div>
        </div>

        {{-- Card Thu nhập --}}
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-medium">Thu nhập hôm nay</h3><a href="{{ route('driver.earnings') }}"
                    class="text-blue-600 text-sm font-medium">Chi tiết</a>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600">
                    {{ number_format($totalEarnedToday, 0, ',', '.') }} đ</div>
                <div class="text-sm text-gray-500">{{ $deliveredOrdersCountToday }} đơn đã giao</div>
                <div class="text-xs text-gray-400 mt-1">
                    Trung bình:
                    {{ $deliveredOrdersCountToday > 0 ? number_format($totalEarnedToday / $deliveredOrdersCountToday, 0, ',', '.') : '0' }}
                    đ/đơn
                </div>
            </div>
        </div>

        {{-- Card Đơn hàng đang xử lý --}}
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-medium">Đơn hàng đang xử lý</h3><a href="{{ route('driver.orders.index') }}"
                    class="text-blue-600 text-sm font-medium">Xem tất cả</a>
            </div>
            <div class="space-y-3">
                @forelse($processingOrders as $order)
                    <a href="{{ route('driver.orders.show', $order->id) }}"
                        class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        {{-- CẬP NHẬT: Icon và màu nền động theo trạng thái --}}
                        <div id="order-status-icon" class="w-12 h-12 rounded-full flex items-center justify-center text-xl"
                            style="background-color: {{ $order->status_color ?? '#f0f0f0' }}; color: {{ $order->status_text_color ?? '#ffffff' }};">
                            <i class="{{ $order->status_icon }}"></i>
                        </div>
                        {{-- CẬP NHẬT: Thêm min-w-0 để truncate hoạt động tốt --}}
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-800">Đơn #{{ $order->order_code }}</div>
                            <div class="text-sm text-gray-500 truncate">{{ $order->delivery_address }}</div>
                        </div>
                        {{-- CẬP NHẬT: Badge trạng thái cũng dùng màu động --}}
                        <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-hidden focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent hover:bg-primary/80"
                            style="background-color: {{ $order->status_color }}; color: {{ $order->status_text_color ?? '#ffffff' }};">
                            {{ $order->status_text }}
                        </div>
                    </a>
                @empty
                    <p class="text-center text-sm text-gray-500 py-3">Không có đơn hàng nào đang xử lý.</p>
                @endforelse
            </div>
        </div>

        {{-- Card Đơn hàng mới có sẵn --}}
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-medium">Đơn hàng mới có sẵn</h3>
            </div>
            <div id="available-orders-list" class="space-y-3">
                @forelse($availableOrders as $order)
                    {{-- Thêm data-order-id để dễ dàng quản lý bằng JS --}}
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg" data-order-id="{{ $order->id }}">
                        {{-- CẬP NHẬT: Icon và màu nền động theo trạng thái --}}
                        <div id="order-status-icon" class="w-12 h-12 rounded-full flex items-center justify-center text-xl"
                            style="background-color: {{ $order->status_color ?? '#f0f0f0' }}; color: {{ $order->status_text_color ?? '#ffffff' }};">
                            <i class="{{ $order->status_icon }}"></i>
                        </div>
                        {{-- CẬP NHẬT: Thêm min-w-0 để truncate hoạt động tốt --}}
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-800">Đơn #{{ $order->order_code }}</div>
                            <div class="text-sm text-gray-500 truncate">{{ $order->delivery_address }}</div>
                        </div>
                        <a href="{{ route('driver.orders.show', $order->id) }}"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 flex-shrink-0">Xem
                            đơn</a>
                    </div>
                @empty
                    <p class="text-center text-sm text-gray-500 py-3 no-order-message">Hiện không có đơn hàng mới.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Giữ nguyên JS, chỉ cần thay đổi CSS và HTML --}}
    <style>
        @keyframes pulse-fade-in {
            0% {
                background-color: #eff6ff;
                opacity: 0;
            }

            50% {
                background-color: #dbeafe;
                opacity: 1;
            }

            100% {
                background-color: #f9fafb;
                opacity: 1;
            }
        }

        .animate-pulse-fade-in {
            animation: pulse-fade-in 1.5s ease-in-out;
        }
    </style>

    <script src="https://js.pusher.com/8.0/pusher.min.js"></script> {{-- Đảm bảo đã include Pusher JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // --- Logic cho bật/tắt trạng thái ---
            const statusToggle = document.getElementById('statusToggle');
            const driverStatusTextElements = document.querySelectorAll(
                '.driver-status-text'); // Lấy tất cả các element có class này

            if (statusToggle && driverStatusTextElements.length > 0) {
                statusToggle.addEventListener('change', function() {
                    const isChecked = this.checked; // Lấy trạng thái hiện tại của toggle
                    fetch("{{ route('driver.status.setAvailability') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                is_available: isChecked
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                driverStatusTextElements.forEach(el => {
                                    el.textContent = data.is_available ? 'Bạn đang Online' :
                                        'Bạn đang Offline';
                                });
                                showToast(data.message, 'success');
                            } else {
                                // Nếu cập nhật thất bại, khôi phục trạng thái của toggle
                                statusToggle.checked = !isChecked;
                                showToast(data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Status Toggle Error:', error);
                            // Khôi phục trạng thái toggle nếu có lỗi mạng/server
                            statusToggle.checked = !isChecked;
                            showToast('Có lỗi xảy ra khi cập nhật trạng thái.', 'error');
                        });
                });
            }

            // --- Logic cho Pusher ---
            const availableOrdersList = document.getElementById('available-orders-list');

            // Khởi tạo Pusher nếu chưa có Echo hoặc Pusher
            if (typeof Pusher === 'undefined' && typeof window.Echo === 'undefined') {
                console.error('Pusher hoặc Laravel Echo chưa được tải. Tính năng real-time sẽ không hoạt động.');
                return;
            }

            // Kiểm tra và cấu hình Pusher nếu Echo chưa tự động cấu hình
            if (!window.Echo) {
                // Đây là fallback nếu Laravel Echo không được setup chuẩn
                // Trong môi trường Laravel, Laravel Echo thường đã cấu hình Pusher sẵn
                const pusherAppKey = "{{ config('broadcasting.connections.pusher.key') }}";
                const pusherCluster = "{{ config('broadcasting.connections.pusher.options.cluster') }}";

                if (pusherAppKey && pusherCluster) {
                    window.Pusher = new Pusher(pusherAppKey, {
                        cluster: pusherCluster,
                        encrypted: true,
                        // Auth cho kênh private, nếu bạn dùng auth riêng của Pusher
                        // (thường Laravel Echo đã xử lý phần này qua /broadcasting/auth)
                        authorizer: function(channel, options) {
                            return {
                                authorize: function(socketId, callback) {
                                    fetch('/driver/broadcasting/auth', { // Đảm bảo route này có middleware 'auth:driver'
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': csrfToken
                                            },
                                            body: JSON.stringify({
                                                socket_id: socketId,
                                                channel_name: channel.name
                                            })
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            callback(null, data);
                                        })
                                        .catch(error => {
                                            console.error('Pusher authorization error:', error);
                                            callback(new Error('Pusher authorization failed'),
                                                null);
                                        });
                                }
                            };
                        }
                    });
                    // Gán kênh cho window.Echo nếu nó tồn tại để tương thích
                    window.Echo = new(class {
                        private(channelName) {
                            return window.Pusher.subscribe(`private-${channelName}`);
                        }
                    })();
                } else {
                    console.warn('Pusher credentials not set. Real-time features might be limited.');
                    return; // Thoát nếu không có thông tin Pusher
                }
            }

            // Đảm bảo bạn có biến driverId chứa ID tài xế đang đăng nhập
            window.driverId = {{ auth('driver')->id() }};
            if (typeof driverId === 'undefined') {
                console.error('driverId chưa được khai báo!');
            } else if (availableOrdersList) {
                window.Echo.private(`driver.${driverId}`)
                    .listen('.DriverAssigned', (eventData) => {
                        console.log('Bạn vừa được gán đơn hàng:', eventData.order);

                        const order = eventData.order;

                        // Xóa thông báo "Không có đơn hàng"
                        const noOrderMsg = availableOrdersList.querySelector('.no-order-message');
                        if (noOrderMsg) {
                            noOrderMsg.remove();
                        }

                        // Tạo link tới trang xem chi tiết đơn hàng
                        const orderShowUrl = `/driver/orders/${order.id}/show`;

                        const newOrderHtml = `
                <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg border border-blue-200 animate-pulse-fade-in" data-order-id="${order.id}">
                    <div id="order-status-icon" class="w-12 h-12 rounded-full flex items-center justify-center text-xl"
                        style="background-color: #fcd5ce; color: #7c2d12;">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-gray-800">Đơn #${order.order_code}</div>
                        <div class="text-sm text-gray-500 truncate">${order.delivery_address}</div>
                    </div>
                    <a href="${orderShowUrl}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 flex-shrink-0">Xem đơn</a>
                </div>
            `;

                        availableOrdersList.insertAdjacentHTML('afterbegin', newOrderHtml);
                        showToast('Bạn vừa được gán một đơn hàng mới!', 'info');
                    })
                    .listen('.order-cancelled-event', (eventData) => {
                        console.log('Đơn hàng đã bị hủy:', eventData.order_id);

                        const cancelledOrderId = eventData.order_id;
                        const orderElementToRemove = availableOrdersList.querySelector(
                            `[data-order-id="${cancelledOrderId}"]`
                        );

                        if (orderElementToRemove) {
                            orderElementToRemove.remove();
                            showToast(`Đơn hàng #${cancelledOrderId} đã bị hủy.`, 'warning');

                            // Nếu không còn đơn hàng nào, hiển thị lại thông báo
                            if (availableOrdersList.children.length === 0) {
                                availableOrdersList.innerHTML = `
                        <p class="text-center text-sm text-gray-500 py-3 no-order-message">Hiện không có đơn hàng mới.</p>
                    `;
                            }
                        } else {
                            // Nếu đơn hàng không nằm trong danh sách (ví dụ tài xế đang xem chi tiết)
                            showToast(`Đơn hàng #${cancelledOrderId} đã bị hủy.`, 'warning');
                        }
                    });
            } else {
                console.warn('Phần tử #available-orders-list không tồn tại, không thể lắng nghe đơn hàng.');
            }


            // --- Helper for Toast Notifications ---
            function showToast(message, type = 'info', duration = 3000) {
                let toastContainer = document.getElementById('toast-container');
                if (!toastContainer) {
                    const div = document.createElement('div');
                    div.id = 'toast-container';
                    div.className = 'fixed top-4 right-4 z-50 space-y-2';
                    document.body.appendChild(div);
                    toastContainer = div;
                }

                const toast = document.createElement('div');
                toast.className = `p-3 rounded-lg shadow-md text-white flex items-center space-x-2 ` +
                    (type === 'success' ? 'bg-green-500' :
                        type === 'error' ? 'bg-red-500' :
                        type === 'warning' ? 'bg-orange-500' : 'bg-blue-500');
                toast.innerHTML = `
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-times-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle'}"></i>
                    <span>${message}</span>
                `;
                toastContainer.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, duration);
            }
        });
    </script>
@endpush
