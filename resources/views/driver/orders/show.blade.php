@extends('layouts.driver.masterLayout')

@section('title', 'Chi tiết đơn hàng')
{{-- Bỏ page-title ở đây để đưa vào header mới --}}

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <div id="order-details-card" data-order-id="{{ $order->id }}">
        <div class="pt-4 p-4 space-y-4">
            {{-- CẬP NHẬT: Thêm Header --}}
            <div class="flex items-center justify-between">
                <button onclick="history.back()"
                    class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-full hover:bg-gray-200">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </button>
                <h1 class="text-lg font-bold">Chi tiết Đơn hàng #{{ $order->order_code }}</h1>
                <div class="w-10"></div> {{-- Placeholder để giữ cho tiêu đề ở giữa --}}
            </div>

            {{-- Order Status Card --}}
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl"
                        style="background-color: {{ $order->status_color['bg'] ?? '#f0f0f0' }}; color: {{ $order->status_color['text'] ?? '#333' }};">
                        <i class="{{ $order->status_icon }}"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold">{{ $order->status_text }}</h2>
                        <p class="text-sm text-gray-500">Cập nhật lúc: {{ $order->updated_at->format('H:i') }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm ml-auto"
                        style="background-color: {{ $order->status_color['bg'] ?? '#f0f0f0' }}; color: {{ $order->status_color['text'] ?? '#333' }};">#{{ $order->order_code }}</span>
                </div>
            </div>

            {{-- Map Section --}}
            @if ($order->status == 'in_transit')
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div id="orderMap" class="h-48 w-full"></div>
                </div>
            @endif

            <div class="bg-white rounded-lg p-4 shadow-sm">
                <h3 class="font-semibold mb-3">Thông tin khách hàng</h3>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-user text-gray-400"></i>
                        <span>{{ $order->customer->full_name ?? $order->guest_name }}</span>
                        {{-- Add data-action attribute to identify the action --}}
                        <button data-action="call-customer"
                            class="ml-auto text-green-600 bg-green-50 px-3 py-1 rounded-full text-sm">
                            <i class="fas fa-phone mr-1"></i>Gọi
                        </button>
                    </div>
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-map-marker-alt text-gray-400 mt-1"></i>
                        <div>
                            <p class="font-medium">Địa chỉ giao hàng</p>
                            <p class="text-sm text-gray-600">{{ $order->delivery_address }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-clock text-gray-400"></i>
                        <div>
                            <span class="font-medium">Thời gian giao hàng</span>
                            <p class="text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($order->estimated_delivery_time)->format('H:i - d/m/Y') }}</p>
                        </div>
                    </div>
                    @if ($order->notes)
                        <div class="bg-yellow-50 p-3 rounded-lg">
                            <p class="text-sm text-yellow-800">
                                <strong>Ghi chú:</strong><br>
                                {{ $order->notes }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg p-4 shadow-sm">
                <h3 class="font-semibold mb-3">Chi tiết đơn hàng</h3>
                <div class="space-y-3">
                    @foreach ($order->orderItems as $item)
                        <div class="flex justify-between">
                            <span>{{ $item->quantity }}x {{ $item->productVariant->product->name ?? 'Sản phẩm' }}</span>
                            <span>{{ number_format($item->total_price, 0, ',', '.') }} đ</span>
                        </div>
                    @endforeach
                    <hr class="my-3">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>Tổng tiền hàng</span>
                            <span>{{ number_format($order->subtotal, 0, ',', '.') }} đ</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Phí giao hàng</span>
                            <span>{{ number_format($order->delivery_fee, 0, ',', '.') }} đ</span>
                        </div>
                        <div class="flex justify-between text-green-600">
                            <span>Giảm giá</span>
                            <span>-{{ number_format($order->discount_amount, 0, ',', '.') }} đ</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Thuế</span>
                            <span>{{ number_format($order->tax_amount, 0, ',', '.') }} đ</span>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="flex justify-between font-semibold text-lg">
                        <span>Tổng thanh toán</span>
                        <span class="text-green-600">{{ number_format($order->total_amount, 0, ',', '.') }} đ</span>
                    </div>
                </div>
            </div>

            {{-- Action Buttons (Crucial change here) --}}
            <div class="space-y-3" id="action-buttons-container"> {{-- Add an ID here for easier targeting --}}
                @switch($order->status)
                    @case('awaiting_driver')
                        {{-- Use data-action attribute instead of onclick --}}
                        <button data-action="accept-order"
                            class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-blue-700">
                            <i class="fas fa-check mr-2"></i>Chấp nhận đơn hàng
                        </button>
                    @break

                    @case('driver_picked_up')
                        <button data-action="confirm-pickup"
                            class="w-full bg-indigo-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-indigo-700">
                            <i class="fas fa-shopping-bag mr-2"></i>Xác nhận đã lấy hàng
                        </button>
                    @break

                    @case('in_transit')
                        <button data-action="confirm-delivery"
                            class="w-full bg-green-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-green-700">
                            <i class="fas fa-check-double mr-2"></i>Xác nhận đã giao hàng
                        </button>
                        <button data-action="start-delivery"
                            class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-purple-700">
                            <i class="fas fa-route mr-2"></i>Xem bản đồ lớn
                        </button>
                    @break

                    @case('delivered')
                    @case('item_received')
                        <div class="bg-green-50 p-4 rounded-lg text-center">
                            <i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i>
                            <p class="text-green-800 font-medium">Đơn hàng đã được giao thành công</p>
                            <p class="text-sm text-green-600">Lúc:
                                {{ optional($order->actual_delivery_time)->format('H:i d/m/Y') }}</p>
                        </div>
                    @break

                    @case('cancelled')
                        <div class="bg-red-50 p-4 rounded-lg text-center">
                            <i class="fas fa-times-circle text-red-600 text-2xl mb-2"></i>
                            <p class="text-red-800 font-medium">Đơn hàng đã bị hủy</p>
                        </div>
                    @break
                @endswitch

                @if (!in_array($order->status, ['delivered', 'item_received', 'cancelled']))
                    <button data-action="call-customer"
                        class="w-full border border-gray-300 text-gray-700 py-3 rounded-lg font-medium bg-white hover:bg-gray-50">
                        <i class="fas fa-phone mr-2"></i>Gọi cho khách hàng
                    </button>
                @endif
            </div>

        </div>

        {{-- CẬP NHẬT: Modal xác nhận --}}
        <div id="confirmationModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm">
                <div class="p-6 text-center">
                    <div id="modalIcon"
                        class="mx-auto w-12 h-12 rounded-full flex items-center justify-center bg-blue-100 text-blue-600 mb-4">
                        <i class="fas fa-question text-2xl"></i>
                    </div>
                    <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Tiêu đề Modal</h3>
                    <p id="modalMessage" class="text-sm text-gray-500 mt-2">Nội dung modal.</p>
                </div>
                <div class="flex items-center bg-gray-50 px-6 py-4 gap-3 rounded-b-lg">
                    <button id="modalCancel" type="button"
                        class="w-full py-2 px-4 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">Hủy
                        bỏ</button>
                    <button id="modalConfirm" type="button"
                        class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">Xác
                        nhận</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const DriverOrderDetailPage = {

                elements: {
                    card: document.getElementById('order-details-card'),
                    modal: document.getElementById('confirmationModal'),
                    modalTitle: document.getElementById('modalTitle'),
                    modalMessage: document.getElementById('modalMessage'),
                    modalConfirm: document.getElementById('modalConfirm'),
                    modalCancel: document.getElementById('modalCancel'),
                    modalIcon: document.getElementById('modalIcon'),
                    statusIcon: document.querySelector('.flex.items-center.space-x-3 .text-xl i'),
                    statusText: document.querySelector('.flex.items-center.space-x-3 h2.font-semibold'),
                    statusTime: document.querySelector('.flex.items-center.space-x-3 p.text-sm'),
                    statusBadge: document.querySelector('.flex.items-center.space-x-3 span.ml-auto'),
                    // Get the action button container by its new ID
                    actionButtonContainer: document.getElementById('action-buttons-container')
                },

                state: {
                    orderId: null,
                    onConfirmAction: () => {}
                },

                init() {
                    if (!this.elements.card) {
                        console.error('[DriverPage] Không tìm thấy card chi tiết đơn hàng.');
                        return;
                    }
                    this.state.orderId = this.elements.card.dataset.orderId;
                    this.setupEventListeners();
                    this.initRealtimeListener();
                    this.initMap(); // Ensure map initialization is also controlled
                    console.log('[DriverPage] Initialized successfully.');
                },

                setupEventListeners() {
                    this.elements.modalConfirm?.addEventListener('click', () => {
                        this.state.onConfirmAction();
                        this.closeModal();
                    });
                    this.elements.modalCancel?.addEventListener('click', () => this.closeModal());
                    this.elements.modal?.addEventListener('click', e => {
                        if (e.target === this.elements.modal) this.closeModal();
                    });
                    document.addEventListener('keydown', (e) => {
                        if (e.key === "Escape" && !this.elements.modal.classList.contains('hidden')) {
                            this.closeModal();
                        }
                    });

                    // --- Event Delegation for Action Buttons ---
                    if (this.elements.actionButtonContainer) {
                        this.elements.actionButtonContainer.addEventListener('click', (event) => {
                            const button = event.target.closest('button[data-action]');
                            if (!button) return; // Not a button with a data-action

                            const action = button.dataset.action;

                            switch (action) {
                                case 'accept-order':
                                    this.acceptAction(button);
                                    break;
                                case 'confirm-pickup':
                                    this.confirmPickupAction(button);
                                    break;
                                case 'confirm-delivery':
                                    this.confirmDeliveryAction(button);
                                    break;
                                case 'start-delivery':
                                    this.navigateAction(button);
                                    break;
                                case 'call-customer':
                                    this.callCustomerAction(button);
                                    break;
                                default:
                                    console.warn(`[DriverPage] Unknown action: ${action}`);
                            }
                        });
                    }
                    // --- End Event Delegation ---

                    // For the "back" button outside the action-buttons-container
                    const backButton = document.querySelector('button[onclick="history.back()"]');
                    if (backButton) {
                        backButton.addEventListener('click', () => history.back());
                        backButton.removeAttribute('onclick'); // Remove inline onclick
                    }

                    // For the "call customer" button outside the action-buttons-container at the top
                    const topCallCustomerButton = document.querySelector(
                        '.flex.items-center.space-x-3 button[onclick="callCustomer()"]');
                    if (topCallCustomerButton) {
                        topCallCustomerButton.addEventListener('click', (event) => {
                            this.callCustomerAction(event.currentTarget);
                        });
                        topCallCustomerButton.removeAttribute('onclick'); // Remove inline onclick
                    }

                },

                showToast(message, type = 'success') {
                    const displayMessage = message || (type === 'error' ? 'Có lỗi xảy ra' :
                        'Thao tác thành công');

                    const toast = document.createElement('div');
                    let bgColor, iconClass;

                    switch (type) {
                        case 'error':
                            bgColor = 'bg-red-600';
                            iconClass = 'fa-times-circle';
                            break;
                        case 'info':
                            bgColor = 'bg-blue-600';
                            iconClass = 'fa-info-circle';
                            break;
                        case 'success':
                        default:
                            bgColor = 'bg-green-600';
                            iconClass = 'fa-check-circle';
                            break;
                    }

                    toast.className =
                        `fixed top-5 right-5 text-white px-4 py-3 rounded-lg shadow-lg z-[101] transition-all duration-300 opacity-0 transform translate-x-full ${bgColor}`;
                    toast.innerHTML = `<i class="fas ${iconClass} mr-2"></i> ${displayMessage}`;

                    document.body.appendChild(toast);

                    setTimeout(() => {
                        toast.classList.remove('opacity-0', 'translate-x-full');
                    }, 10);

                    setTimeout(() => {
                        toast.classList.add('opacity-0', 'translate-x-full');
                        setTimeout(() => document.body.removeChild(toast), 300);
                    }, 3500);
                },

                showModal(title, message, onConfirm, config) {
                    this.elements.modalTitle.textContent = title;
                    this.elements.modalMessage.textContent = message;
                    this.elements.modalConfirm.textContent = config.confirmText;
                    this.elements.modalConfirm.className =
                        `w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-${config.confirmColor}-600 hover:bg-${config.confirmColor}-700`;
                    this.elements.modalIcon.className =
                        `mx-auto w-12 h-12 rounded-full flex items-center justify-center bg-${config.iconColor}-100 text-${config.iconColor}-600 mb-4`;
                    this.elements.modalIcon.firstElementChild.className = `${config.icon} text-2xl`;
                    this.state.onConfirmAction = onConfirm;
                    this.elements.modal.classList.remove('hidden');
                },

                closeModal() {
                    this.elements.modal.classList.add('hidden');
                },

                performAction(url, successMessage, button) {
                    const originalContent = button?.innerHTML;
                    if (button) {
                        button.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Đang xử lý...`;
                        button.disabled = true;
                    }

                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json().then(data => ({
                            ok: res.ok,
                            data
                        })))
                        .then(({
                            ok,
                            data
                        }) => {
                            if (ok && data.success) {
                                this.showToast(data.message || successMessage, 'success');
                                // Reload the page after a short delay to reflect status changes and buttons
                                setTimeout(() => window.location.reload(), 1000);

                            } else {
                                throw new Error(data.message || 'Thao tác thất bại.');
                            }
                        })
                        .catch(error => {
                            this.showToast(error.message, 'error');
                            if (button) {
                                button.innerHTML = originalContent;
                                button.disabled = false;
                            }
                        });
                },

                // This function is less critical with page reloads, but good to have for partial updates
                updateUIAfterAction(orderData) {
                    if (!orderData) return;

                    // 1. Cập nhật khối trạng thái ở trên cùng
                    const statusColor = orderData.status_color;
                    this.elements.statusIcon.className = orderData.status_icon;
                    this.elements.statusIcon.parentElement.style.backgroundColor = statusColor.bg;
                    this.elements.statusIcon.parentElement.style.color = statusColor.text;
                    this.elements.statusText.textContent = orderData.status_text;
                    this.elements.statusTime.textContent =
                        `Cập nhật lúc: ${new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })}`;
                    this.elements.statusBadge.style.backgroundColor = statusColor.bg;
                    this.elements.statusBadge.style.color = statusColor.text;

                    // 2. Cập nhật khu vực nút bấm hành động ở dưới cùng
                    // This part will now be less relevant as we'll reload the page, but keeping for reference
                    // if you decide against full page reloads for some actions later.
                    let newButtonHTML = '';
                    switch (orderData.status) {
                        case 'driver_picked_up':
                            newButtonHTML =
                                `<button data-action="confirm-pickup" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-indigo-700"><i class="fas fa-shopping-bag mr-2"></i>Xác nhận đã lấy hàng</button>`;
                            break;
                        case 'in_transit':
                            newButtonHTML =
                                `<button data-action="confirm-delivery" class="w-full bg-green-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-green-700"><i class="fas fa-check-double mr-2"></i>Xác nhận đã giao hàng</button>
                                   <button data-action="start-delivery" class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-purple-700 mt-3"><i class="fas fa-route mr-2"></i>Xem bản đồ lớn</button>`;
                            break;
                        case 'delivered':
                        case 'item_received':
                            newButtonHTML =
                                `<div class="bg-green-50 p-4 rounded-lg text-center"><i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i><p class="text-green-800 font-medium">Đơn hàng đã được giao thành công</p><p class="text-sm text-green-600">Lúc: ${orderData.actual_delivery_time}</p></div>`;
                            break;
                        default:
                            newButtonHTML = '<p class="text-center text-gray-500">Không có hành động nào.</p>';
                    }
                    // This line should ideally be updated to completely re-render the action buttons section
                    // or, better yet, rely on a full page reload after a successful action for simplicity.
                    // If you keep it, make sure to also update the "call customer" button if applicable.
                    this.elements.actionButtonContainer.innerHTML = newButtonHTML;
                },

                initRealtimeListener() {
                    if (window.Echo) {
                        window.Echo.private(`order.${this.state.orderId}`)
                            .listen('.OrderStatusUpdated', (event) => {
                                this.showToast(`Trạng thái cập nhật: ${event.status_text}`, 'info');
                                setTimeout(() => window.location.reload(), 2500);
                            });
                    }
                },

                initMap() {
                    // Only initialize map if the map container exists
                    if (document.getElementById('orderMap') && typeof mapboxgl !== 'undefined') {
                        const customerCoords = {
                            lat: {{ $order->address->latitude ?? ($order->guest_latitude ?? 21.0285) }},
                            lng: {{ $order->address->longitude ?? ($order->guest_longitude ?? 105.8542) }}
                        };
                        const map = new mapboxgl.Map({
                            container: 'orderMap',
                            style: 'mapbox://styles/mapbox/streets-v11',
                            center: [customerCoords.lng, customerCoords.lat],
                            zoom: 13
                        });

                        new mapboxgl.Marker({
                                color: 'red'
                            })
                            .setLngLat([customerCoords.lng, customerCoords.lat])
                            .setPopup(new mapboxgl.Popup().setHTML('<strong>Điểm giao hàng</strong>'))
                            .addTo(map);

                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(pos => {
                                const driverCoords = [pos.coords.longitude, pos.coords.latitude];
                                new mapboxgl.Marker({
                                        color: 'blue'
                                    })
                                    .setLngLat(driverCoords)
                                    .setPopup(new mapboxgl.Popup().setHTML(
                                        '<strong>Vị trí của bạn</strong>'))
                                    .addTo(map);

                                const bounds = new mapboxgl.LngLatBounds()
                                    .extend(driverCoords)
                                    .extend([customerCoords.lng, customerCoords.lat]);
                                map.fitBounds(bounds, {
                                    padding: 60
                                });
                            }, (error) => {
                                console.warn('Geolocation error:', error.message);
                                // Fallback to centering on customer if driver location fails
                                map.setCenter([customerCoords.lng, customerCoords.lat]);
                                map.setZoom(13);
                            });
                        }
                    } else if (document.getElementById('orderMap')) {
                        console.error('Lỗi: Thư viện Mapbox GL JS chưa được tải hoặc orderMap không tồn tại.');
                    }
                },

                acceptAction(button) {
                    this.showModal('Chấp nhận đơn hàng?', 'Bạn có chắc chắn muốn nhận đơn hàng này không?',
                        () => this.performAction("{{ route('driver.orders.accept', $order->id) }}",
                            'Đã chấp nhận đơn hàng!', button), {
                            confirmText: 'Nhận đơn',
                            confirmColor: 'blue',
                            icon: 'fas fa-check',
                            iconColor: 'blue'
                        }
                    );
                },

                confirmPickupAction(button) {
                    this.showModal('Xác nhận lấy hàng?', 'Bạn có chắc chắn đã nhận hàng từ chi nhánh?',
                        () => this.performAction("{{ route('driver.orders.confirm_pickup', $order->id) }}",
                            'Đã xác nhận lấy hàng!', button), {
                            confirmText: 'Đã lấy hàng',
                            confirmColor: 'indigo',
                            icon: 'fas fa-shopping-bag',
                            iconColor: 'indigo'
                        }
                    );
                },

                confirmDeliveryAction(button) {
                    this.showModal('Xác nhận giao hàng?', 'Bạn có chắc chắn đã giao hàng thành công?',
                        () => this.performAction(
                            "{{ route('driver.orders.confirm_delivery', $order->id) }}",
                            'Đã giao hàng thành công!', button), {
                            confirmText: 'Đã giao xong',
                            confirmColor: 'green',
                            icon: 'fas fa-check-double',
                            iconColor: 'green'
                        }
                    );
                },

                navigateAction(button) {
                    window.open(`/driver/orders/{{ $order->id }}/navigate`, '_blank');
                },

                callCustomerAction(button) {
                    const phone = '{{ $order->customer_phone ?? '' }}';
                    if (phone) {
                        this.showModal('Xác nhận cuộc gọi', `Bạn muốn gọi đến số ${phone}?`,
                            () => window.location.href = `tel:${phone}`, {
                                confirmText: 'Gọi ngay',
                                confirmColor: 'green',
                                icon: 'fas fa-phone-alt',
                                iconColor: 'green'
                            }
                        );
                    } else {
                        this.showToast('Không có số điện thoại khách hàng.', 'error');
                    }
                }
            };

            // Initialize the page logic
            DriverOrderDetailPage.init();

            // IMPORTANT: Remove the global exposures if you're using event delegation
            // These lines are no longer needed and can cause confusion or even subtle bugs
            // if you strictly use event delegation.
            // window.acceptOrder = (btn) => DriverOrderDetailPage.acceptAction(btn);
            // window.confirmPickup = (btn) => DriverOrderDetailPage.confirmPickupAction(btn);
            // window.confirmDelivery = (btn) => DriverOrderDetailPage.confirmDeliveryAction(btn);
            // window.startDelivery = (btn) => DriverOrderDetailPage.navigateAction(btn);
            // window.callCustomer = (btn) => DriverOrderDetailPage.callCustomerAction(btn);

        });
    </script>
@endpush
