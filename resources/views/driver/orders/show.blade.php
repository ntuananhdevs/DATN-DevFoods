@extends('layouts.driver.masterLayout')

@section('title', 'Chi tiết đơn hàng')
@php
    use App\Models\Order;
@endphp
@section('content')
    <div id="order-details-card" data-order-id="{{ $order->id }}" data-order-status="{{ $order->status }}"
        data-customer-latitude="{{ $order->guest_latitude }}" {{-- THÊM DÒNG NÀY --}}
        data-customer-longitude="{{ $order->guest_longitude }}" {{-- THÊM DÒNG NÀY --}}
        data-customer-phone="{{ $order->customer->phone ?? ($order->guest_phone ?? '') }}"> {{-- THÊM DÒNG NÀY --}}
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
                    <div id="order-status-icon" class="w-12 h-12 rounded-full flex items-center justify-center text-xl"
                        style="background-color: {{ $order->status_color ?? '#f0f0f0' }}; color: {{ $order->status_text_color ?? '#ffffff' }};">
                        <i class="{{ $order->status_icon }}"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold">{{ $order->status_text }}</h2>
                        <p class="text-sm text-gray-500">Cập nhật lúc: {{ $order->updated_at->format('H:i') }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm ml-auto"
                        style="background-color: {{ $order->status_color ?? '#f0f0f0' }}; color: {{ $order->status_text_color ?? '#ffffff' }};">#{{ $order->order_code }}</span>
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
                            <span>{{ $item->quantity }}x
                                {{ $item->productVariant->product->name ?? 'Sản phẩm' }}</span>
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
            <div class="space-y-3" id="action-buttons-container">
                @switch($order->status)
                    @case('awaiting_driver')
                        {{-- Nút Xác nhận nhận đơn --}}
                        <button data-action="confirm-order"
                            class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-blue-700 flex items-center justify-center">
                            <i class="fas fa-check w-4 h-4 mr-2"></i>
                            Xác nhận nhận đơn
                        </button>

                        {{-- Nút Từ chối --}}
                        <button data-action="reject-order"
                            class="w-full bg-red-600 text-white mt-2 py-3 rounded-lg font-medium shadow-sm hover:bg-red-700 flex items-center justify-center">
                            <i class="fas fa-times w-4 h-4 mr-2"></i>
                            Từ chối nhận đơn
                        </button>
                    @break

                    @case('driver_confirmed')
                        {{-- Nút bắt đầu di chuyển đến điểm nhận --}}
                        <button data-action="start-pickup"
                            class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-blue-700 flex items-center justify-center">
                            <i class="fas fa-location-arrow w-4 h-4 mr-2"></i>
                            Bắt đầu di chuyển đến điểm lấy hàng
                        </button>
                    @break

                    @case('waiting_driver_pick_up')
                        {{-- Nút đã lấy hàng --}}
                        <button data-action="confirm-pickup"
                            class="w-full bg-green-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-green-700 flex items-center justify-center">
                            <i class="fas fa-box w-4 h-4 mr-2"></i>
                            Xác nhận đã lấy hàng
                        </button>
                    @break

                    @case('driver_picked_up')
                        {{-- Nút bắt đầu giao hàng --}}
                        <button data-action="start-delivery"
                            class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-blue-700 flex items-center justify-center">
                            <i class="fas fa-truck w-4 h-4 mr-2"></i>
                            Bắt đầu giao hàng
                        </button>
                    @break

                    @case('in_transit')
                        {{-- Nút đã giao hàng thành công --}}
                        <button data-action="confirm-delivery"
                            class="w-full bg-green-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-green-700 flex items-center justify-center">
                            <i class="fas fa-check-circle w-4 h-4 mr-2"></i>
                            Đã giao hàng thành công
                        </button>
                        {{-- Nút "Xem bản đồ lớn" --}}
                        <button data-action="navigate"
                            class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-purple-700 flex items-center justify-center">
                            <i class="fas fa-route w-4 h-4 mr-2"></i> {{-- Using a direct fas icon for route map --}}
                            Xem bản đồ lớn
                        </button>
                    @break

                    @default
                        {{-- Các trạng thái delivered/item_received/... không hiển thị nút --}}
                @endSwitch

                @if (!in_array($order->status, ['delivered', 'item_received', 'cancelled']))
                    <button data-action="call-customer"
                        class="w-full border border-gray-300 text-gray-700 py-3 rounded-lg font-medium bg-white hover:bg-gray-50">
                        <i class="fas fa-phone mr-2"></i>Gọi cho khách hàng
                    </button>
                @endif
            </div>

        </div>
        {{-- Toast Container --}}
        <div id="dtmodal-toast-container" class="fixed top-4 right-4 z-[100] space-y-2"></div>

        {{-- CẬP NHẬT: Modal xác nhận --}}
        <div id="confirmationModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm">
                <div class="p-6 text-center">
                    <div id="modalIcon"
                        class="mx-auto w-12 h-12 rounded-full flex items-center justify-center bg-blue-100 text-blue-600
                        mb-4">
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
    {{-- Mapbox GL JS --}}
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.4.0/mapbox-gl.js"></script>
    <script>
        // Global utility function for showing toasts
        window.showToast = function(type, options) {
            const toastContainer = document.getElementById('dtmodal-toast-container');
            if (!toastContainer) {
                console.error('Toast container not found!');
                return;
            }

            const toastId = 'toast-' + Date.now();
            const toastElement = document.createElement('div');
            toastElement.id = toastId;
            toastElement.className =
                `relative flex items-center w-full max-w-xs p-4 rounded-lg shadow-md mt-2 text-white transform transition-all ease-out duration-300 translate-x-full opacity-0`;
            let bgColor = '';
            let iconClass = '';
            switch (type) {
                case 'success':
                    bgColor = 'bg-green-500';
                    iconClass = 'fas fa-check-circle';
                    break;
                case 'error':
                    bgColor = 'bg-red-500';
                    iconClass = 'fas fa-times-circle';
                    break;
                case 'warning':
                    bgColor = 'bg-yellow-500';
                    iconClass = 'fas fa-exclamation-triangle';
                    break;
                case 'info':
                    bgColor = 'bg-blue-500';
                    iconClass = 'fas fa-info-circle';
                    break;
                default:
                    bgColor = 'bg-gray-700';
                    iconClass = 'fas fa-bell';
            }

            toastElement.classList.add(bgColor);
            toastElement.innerHTML = `
                <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg">
                    <i class="${iconClass}"></i>
                </div>
                <div class="ml-3 text-sm font-normal">
                    ${options.title ? `<p class="font-bold">${options.title}</p>` : ''}
                    ${options.message}
                </div>
                <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-white rounded-lg p-1.5 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#${toastId}" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            `;

            toastContainer.appendChild(toastElement);
            // Animate in
            setTimeout(() => {
                toastElement.classList.remove('translate-x-full', 'opacity-0');
                toastElement.classList.add('translate-x-0', 'opacity-100');
            }, 100);
            // Auto-dismiss
            const duration = options.duration || 5000;
            setTimeout(() => {
                toastElement.classList.remove('translate-x-0', 'opacity-100');
                toastElement.classList.add('translate-x-full', 'opacity-0');
                toastElement.addEventListener('transitionend', () => toastElement.remove());
            }, duration);
            // Manual dismiss
            toastElement.querySelector('[data-dismiss-target]').addEventListener('click', () => {
                toastElement.classList.remove('translate-x-0', 'opacity-100');
                toastElement.classList.add('translate-x-full', 'opacity-0');
                toastElement.addEventListener('transitionend', () => toastElement.remove());
            });
        };

        // Global utility function for showing modals
        window.showModal = function(title, message, onConfirm, options = {}) {
            const modal = document.getElementById('confirmationModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const modalIcon = document.getElementById('modalIcon');
            const modalConfirmBtn = document.getElementById('modalConfirm');
            const modalCancelBtn = document.getElementById('modalCancel');
            if (!modal || !modalTitle || !modalMessage || !modalConfirmBtn || !modalCancelBtn || !modalIcon) {
                console.error('Modal elements not found!');
                return;
            }

            modalTitle.textContent = title;
            modalMessage.textContent = message;
            // Set icon and colors
            modalIcon.className = 'mx-auto w-12 h-12 rounded-full flex items-center justify-center mb-4';
            modalIcon.innerHTML = `<i class="${options.icon || 'fas fa-question'} text-2xl"></i>`;
            modalIcon.style.backgroundColor = options.iconBgColor || 'bg-blue-100';
            modalIcon.style.color = options.iconColor || 'text-blue-600';
            modalConfirmBtn.textContent = options.confirmText || 'Đồng ý';
            modalConfirmBtn.className =
                `w-full py-2 px-4 rounded-md shadow-sm text-sm font-medium transition ${options.confirmColor ?
                `bg-${options.confirmColor}-600 hover:bg-${options.confirmColor}-700 text-white` : 'bg-blue-600 hover:bg-blue-700 text-white'}`;


            modalCancelBtn.textContent = options.cancelText || 'Hủy bỏ';
            modalCancelBtn.className =
                `w-full py-2 px-4 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50`;
            const closeModal = () => {
                modal.classList.add('hidden');
                modalConfirmBtn.removeEventListener('click', confirmHandler);
                modalCancelBtn.removeEventListener('click', cancelHandler);
            };

            const confirmHandler = () => {
                if (onConfirm && typeof onConfirm === 'function') {
                    onConfirm();
                }
                closeModal();
            };
            const cancelHandler = () => {
                closeModal();
            };

            modalConfirmBtn.addEventListener('click', confirmHandler);
            modalCancelBtn.addEventListener('click', cancelHandler);

            modal.classList.remove('hidden');
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Centralized event listener for action buttons
            document.getElementById('action-buttons-container').addEventListener('click', function(event) {
                const targetButton = event.target.closest('button[data-action]');
                if (targetButton) {
                    const action = targetButton.dataset.action;
                    // Ensure orderId is correctly retrieved from the data attribute on the main card
                    const orderIdElement = document.getElementById('order-details-card');
                    const orderId = orderIdElement ? orderIdElement.dataset.orderId : null;

                    if (!orderId) {
                        console.error('Order ID not found!');
                        showToast('error', {
                            message: 'Không thể xác định đơn hàng.'
                        });
                        return;
                    }

                    switch (action) {
                        case 'confirm-order':
                            DriverOrderDetailPage.confirmAction(orderId);
                            break;
                        case 'start-pickup':
                            DriverOrderDetailPage.startPickupAction(orderId);
                            break;
                        case 'confirm-pickup':
                            DriverOrderDetailPage.confirmPickupAction(orderId);
                            break;
                        case 'start-delivery':
                            DriverOrderDetailPage.startDeliveryAction(orderId);
                            break;
                        case 'confirm-delivery':
                            DriverOrderDetailPage.confirmDeliveryAction(orderId);
                            break;
                        case 'reject-order':
                            DriverOrderDetailPage.rejectAction(orderId);
                            break;
                        case 'navigate':
                            DriverOrderDetailPage.navigateAction();
                            break;
                        case 'call-customer':
                            DriverOrderDetailPage.callCustomerAction();
                            break;
                    }

                }
            });

            // Mapbox initialization
            let map;
            let marker;
            let routeLayerId = 'route';
            let routeSourceId = 'route-source';

            function initializeMap(customerLat, customerLng) {
                const mapboxContainer = document.getElementById('orderMap');
                if (!mapboxContainer) {
                    console.warn('Mapbox container not found.');
                    return;
                }

                if (map) {
                    map.remove(); // Remove existing map if any
                }

                // Ensure the access token is correctly set here from Laravel's environment
                // This assumes the Blade syntax `{{ config('services.mapbox.access_token') }}`
                // is correctly processed on the server to embed the actual token.
                mapboxgl.accessToken = '{{ config('services.mapbox.access_token') }}';

                // VỊ TRÍ ẢO ĐỂ KIỂM TRA:
                // Tắt dòng bên dưới sau khi kiểm tra xong hoặc khi bạn có dữ liệu thật
                customerLat = 20.4403; // Ví dụ: Vĩ độ giả (Nam Định)
                customerLng = 106.1706; // Ví dụ: Kinh độ giả (Nam Định)
                // HÀM KIỂM TRA TỌA ĐỘ NÀY SẼ CHẠY KHI ĐƠN HÀNG Ở TRẠNG THÁI `in_transit`

                map = new mapboxgl.Map({
                    container: 'orderMap',
                    style: 'mapbox://styles/mapbox/streets-v11', // Consider a light style for better visibility
                    center: [customerLng, customerLat], // Customer location
                    zoom: 14
                });

                map.on('load', () => {
                    // Add customer marker
                    marker = new mapboxgl.Marker()
                        .setLngLat([customerLng, customerLat])
                        .addTo(map);

                    // Attempt to get driver's current location for route calculation
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            const driverLat = position.coords.latitude;
                            const driverLng = position.coords.longitude;

                            // Add driver marker (optional)
                            new mapboxgl.Marker({
                                    color: 'red'
                                })
                                .setLngLat([driverLng, driverLat])
                                .addTo(map);

                            getRoute(driverLng, driverLat, customerLng, customerLat);
                        }, function(error) {
                            console.error('Error getting driver location:', error);
                            showToast('error', {
                                message: 'Không thể lấy vị trí hiện tại của bạn để hiển thị đường đi.'
                            });
                        }, {
                            enableHighAccuracy: true,
                            timeout: 5000,
                            maximumAge: 0
                        });
                    } else {
                        showToast('info', {
                            message: 'Thiết bị không hỗ trợ định vị GPS.'
                        });
                    }
                });

                // Add resize observer for the map container
                const resizeObserver = new ResizeObserver(() => {
                    if (map) {
                        map.resize();
                    }
                });
                resizeObserver.observe(mapboxContainer);
            }

            async function getRoute(startLng, startLat, endLng, endLat) {
                // Ensure access token is available
                if (!mapboxgl.accessToken) {
                    console.error('Mapbox access token is not set.');
                    showToast('error', {
                        message: 'Mapbox API key không khả dụng.'
                    });
                    return;
                }
                const url =
                    `https://api.mapbox.com/directions/v5/mapbox/driving/${startLng},${startLat};${endLng},${endLat}?steps=true&geometries=geojson&access_token=${mapboxgl.accessToken}`;
                try {
                    const response = await fetch(url);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();

                    if (!data.routes || data.routes.length === 0) {
                        showToast('info', {
                            message: 'Không tìm thấy tuyến đường.'
                        });
                        return;
                    }

                    const route = data.routes[0].geometry;

                    if (map.getSource(routeSourceId)) {
                        map.getSource(routeSourceId).setData({
                            type: 'Feature',
                            properties: {},
                            geometry: route
                        });
                    } else {
                        map.addSource(routeSourceId, {
                            type: 'geojson',
                            data: {
                                type: 'Feature',
                                properties: {},
                                geometry: route
                            }
                        });
                        map.addLayer({
                            id: routeLayerId,
                            type: 'line',
                            source: routeSourceId,
                            layout: {
                                'line-join': 'round',
                                'line-cap': 'round'
                            },
                            paint: {
                                'line-color': '#3b82f6', // Blue color for the route
                                'line-width': 6,
                                'line-opacity': 0.75
                            }
                        });
                    }

                    // Fit map to route bounds
                    const coordinates = route.coordinates;
                    const bounds = new mapboxgl.LngLatBounds();
                    for (const coord of coordinates) {
                        bounds.extend(coord);
                    }
                    map.fitBounds(bounds, {
                        padding: 50
                    });
                } catch (error) {
                    console.error('Error calculating route:', error);
                    showToast('error', {
                        message: 'Không thể tính toán tuyến đường. Vui lòng thử lại.'
                    });
                }
            }


            const DriverOrderDetailPage = {
                // orderId is now passed via action functions

                sendRequest: async function(orderId, actionUrl, successMessage) {
                    try {
                        const response = await fetch(actionUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({})
                        });

                        if (!response.ok) {
                            const text = await response.text();
                            console.error('Server error:', text);
                            showToast('error', {
                                message: 'Lỗi server: ' + response.status
                            });
                            return;
                        }

                        const data = await response.json();

                        if (data.success) {
                            showToast('success', {
                                message: successMessage
                            });
                            // Nếu có redirect_url thì chuyển trang
                            if (data.redirect_url) {
                                setTimeout(() => {
                                    window.location.href = data.redirect_url;
                                }, 1500);
                            } else {
                                // Nếu không có, thì reload trang
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                            }
                        } else {
                            showToast('error', {
                                message: data.message || 'Có lỗi xảy ra khi xử lý yêu cầu.'
                            });
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        showToast('error', {
                            message: 'Không thể kết nối đến máy chủ.'
                        });
                    }
                },


                confirmAction: function(orderId) {
                    showModal('Xác nhận nhận đơn hàng', 'Bạn có chắc chắn muốn nhận đơn hàng này?',
                        () => this.sendRequest(orderId,
                            `/driver/orders/${orderId}/confirm`, // Đã sửa URL
                            'Đã nhận đơn hàng thành công và đang đến điểm lấy hàng!'), {
                            confirmText: 'Xác nhận',
                            confirmColor: 'blue',
                            icon: 'fas fa-check',
                            iconColor: 'blue'
                        }
                    );
                },

                startPickupAction: function(orderId) {
                    showModal(
                        'Bắt đầu di chuyển',
                        'Xác nhận bạn đang bắt đầu di chuyển đến điểm lấy hàng?',
                        () => this.sendRequest(
                            orderId,
                            `/driver/orders/${orderId}/start-pickup`,
                            'Bạn đang trên đường đến điểm lấy hàng!'
                        ), {
                            confirmText: 'Xác nhận',
                            confirmColor: 'blue',
                            icon: 'fas fa-location-arrow',
                            iconColor: 'blue'
                        }
                    );
                },

                confirmPickupAction: function(orderId) {
                    showModal('Xác nhận lấy hàng', 'Bạn có chắc chắn đã lấy hàng thành công?',
                        () => this.sendRequest(orderId,
                            `/driver/orders/${orderId}/confirm-pickup`, // Đã sửa URL
                            'Đã xác nhận lấy hàng! Đang giao hàng.'), {
                            confirmText: 'Xác nhận',
                            confirmColor: 'green',
                            icon: 'fas fa-box-open',
                            iconColor: 'green'
                        }
                    );
                },

                startDeliveryAction: function(orderId) {
                    showModal(
                        'Bắt đầu giao hàng',
                        'Xác nhận bạn đã lấy hàng và bắt đầu giao?',
                        () => this.sendRequest(
                            orderId,
                            `/driver/orders/${orderId}/start-delivery`,
                            'Bạn đang giao hàng!'
                        ), {
                            confirmText: 'Xác nhận',
                            confirmColor: 'blue',
                            icon: 'fas fa-truck',
                            iconColor: 'blue'
                        }
                    );
                },

                confirmDeliveryAction: function(orderId) {
                    showModal('Xác nhận giao hàng', 'Bạn có chắc chắn đã giao hàng thành công?',
                        () => this.sendRequest(orderId,
                            `/driver/orders/${orderId}/confirm-delivery`, // Đã sửa URL
                            'Đã giao hàng thành công!'), {
                            confirmText: 'Xác nhận',
                            confirmColor: 'purple',
                            icon: 'fas fa-truck',
                            iconColor: 'purple'
                        }
                    );
                },

                rejectAction: function(orderId) {
                    showModal(
                        'Từ chối đơn hàng',
                        'Bạn chắc chắn muốn từ chối đơn hàng này?',
                        () => this.sendRequest(
                            orderId,
                            `/driver/orders/${orderId}/reject`,
                            'Bạn đã từ chối đơn hàng.'
                        ), {
                            confirmText: 'Từ chối',
                            confirmColor: 'red',
                            icon: 'fas fa-times',
                            iconColor: 'red'
                        }
                    );
                },

                navigateAction: function() {
                    // Ensure the order data is available in the HTML for these values
                    const orderDetailsCard = document.getElementById('order-details-card');
                    const customerLat = parseFloat(orderDetailsCard.dataset.customerLatitude);
                    const customerLng = parseFloat(orderDetailsCard.dataset.customerLongitude);

                    if (!isNaN(customerLat) && !isNaN(customerLng)) {
                        // Sửa URL Google Maps
                        const googleMapsUrl =
                            `http://maps.google.com/maps?q=${customerLat},${customerLng}&travelmode=driving`;
                        window.open(googleMapsUrl, '_blank');
                    } else {
                        showToast('error', {
                            message: 'Không có tọa độ khách hàng để chỉ đường.'
                        });
                    }
                },

                callCustomerAction: function() {
                    // Get phone from HTML attribute if available, or from a JS variable set by Blade
                    const orderDetailsCard = document.getElementById('order-details-card');
                    const phone = orderDetailsCard.dataset.customerPhone;

                    if (phone) {
                        showModal('Xác nhận cuộc gọi', `Bạn muốn gọi đến số ${phone}?`,
                            () => window.location.href = `tel:${phone}`, {
                                confirmText: 'Gọi ngay',
                                confirmColor: 'green',
                                icon: 'fas fa-phone-alt',
                                iconColor: 'green'
                            }
                        );
                    } else {
                        showToast('error', {
                            message: 'Không có số điện thoại khách hàng.'
                        });
                    }
                }
            };

            // Initialize the page logic
            // Check initial status for map visibility
            const orderDetailsCard = document.getElementById('order-details-card');
            const initialOrderStatus = orderDetailsCard ? orderDetailsCard.dataset.orderStatus :
                null; // Get status from dataset
            const mapboxContainer = document.getElementById('orderMap');

            // Mapbox chỉ nên khởi tạo khi trạng thái đơn hàng là 'in_transit'
            if (initialOrderStatus === 'in_transit') {
                // Ensure customer latitude and longitude are available in the dataset
                let customerLat = parseFloat(orderDetailsCard.dataset.customerLatitude);
                let customerLng = parseFloat(orderDetailsCard.dataset.customerLongitude);

                // === THÊM ĐOẠN NÀY ĐỂ SỬ DỤNG TỌA ĐỘ ẢO KHI CẦN THỬ NGHIỆM ===
                if (isNaN(customerLat) || isNaN(customerLng) || (customerLat === 0 && customerLng === 0)) {
                    console.warn(
                        'Customer coordinates are invalid or missing. Using fallback virtual coordinates.');
                    customerLat = 20.4403; // Vĩ độ của Nam Định, Việt Nam
                    customerLng = 106.1706; // Kinh độ của Nam Định, Việt Nam
                    showToast('warning', {
                        message: 'Sử dụng tọa độ giả cho bản đồ do thiếu dữ liệu khách hàng.',
                        duration: 7000
                    });
                }
                // =========================================================

                if (!isNaN(customerLat) && !isNaN(customerLng)) {
                    initializeMap(customerLat, customerLng);
                } else {
                    console.warn('Customer coordinates not found for map initialization, even after fallback.');
                    if (mapboxContainer) {
                        mapboxContainer.classList.add('hidden'); // Ẩn bản đồ nếu không có tọa độ hợp lệ
                    }
                    showToast('error', {
                        message: 'Không có tọa độ hợp lệ để hiển thị bản đồ.'
                    });
                }
            } else {
                if (mapboxContainer) {
                    mapboxContainer.classList.add('hidden'); // Ẩn bản đồ nếu không phải trạng thái 'in_transit'
                }
            }
        });
    </script>
@endpush
