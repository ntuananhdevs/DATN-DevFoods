@extends('layouts.driver.masterLayout')

@section('title', 'Chi tiết đơn hàng')

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <div class="pt-4 p-4 space-y-4">
        {{-- Header --}}
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
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-xl"
                    style="background-color: {{ $order->status_color['bg'] }};">
                    <i class="{{ $order->status_icon }}"></i>
                </div>
                <div>
                    <h2 class="font-semibold" style="color: {{ $order->status_color['text'] }};">{{ $order->status_text }}
                    </h2>
                    <p class="text-sm text-gray-500">Trạng thái hiện tại của đơn hàng</p>
                </div>
            </div>
        </div>

        {{-- Action Buttons (Conditional) --}}
        <div class="bg-white rounded-lg p-4 shadow-sm space-y-2">
            <h3 class="font-semibold text-lg mb-3">Hành động</h3>
            @if ($order->status === 'awaiting_driver')
                <button id="accept-order-btn" data-order-id="{{ $order->id }}"
                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    <i class="fas fa-check-circle mr-2"></i> Nhận đơn hàng
                </button>
            @elseif ($order->status === 'driver_assigned')
                <button id="confirm-assigned-btn" data-order-id="{{ $order->id }}"
                    class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                    <i class="fas fa-handshake mr-2"></i> Xác nhận đã nhận giao
                </button>
            @elseif ($order->status === 'driver_confirmed')
                <button id="confirm-pickup-btn" data-order-id="{{ $order->id }}"
                    class="w-full bg-orange-600 text-white py-3 rounded-lg font-semibold hover:bg-orange-700 transition">
                    <i class="fas fa-box-open mr-2"></i> Xác nhận đã lấy hàng
                </button>
            @elseif ($order->status === 'driver_picked_up')
                <button id="start-transit-btn" data-order-id="{{ $order->id }}"
                    class="w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                    <i class="fas fa-truck-moving mr-2"></i> Bắt đầu giao hàng
                </button>
            @elseif ($order->status === 'in_transit')
                <button id="confirm-delivery-btn" data-order-id="{{ $order->id }}"
                    class="w-full bg-green-700 text-white py-3 rounded-lg font-semibold hover:bg-green-800 transition">
                    <i class="fas fa-check-double mr-2"></i> Xác nhận đã giao hàng
                </button>
            @endif

            @if (in_array($order->status, [
                    'awaiting_driver',
                    'driver_assigned',
                    'driver_confirmed',
                    'driver_picked_up',
                    'in_transit',
                ]))
                <button id="fail-order-btn" data-order-id="{{ $order->id }}"
                    class="w-full bg-red-500 text-white py-3 rounded-lg font-semibold hover:bg-red-600 transition">
                    <i class="fas fa-times-circle mr-2"></i> Báo cáo đơn hàng thất bại
                </button>
            @endif

            {{-- Call Customer Button --}}
            <button onclick="callCustomer()"
                class="w-full bg-gray-200 text-gray-800 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                <i class="fas fa-phone-alt mr-2"></i> Gọi cho khách hàng
            </button>
        </div>

        {{-- Order Details --}}
        <div class="bg-white rounded-lg p-4 shadow-sm space-y-3">
            <h3 class="font-semibold text-lg mb-3">Thông tin đơn hàng</h3>
            <div class="flex justify-between py-1 border-b border-gray-100">
                <span class="text-gray-600">Mã đơn hàng:</span>
                <span class="font-medium">{{ $order->order_code }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-gray-100">
                <span class="text-gray-600">Ngày đặt hàng:</span>
                <span class="font-medium">{{ $order->order_date->format('H:i, d/m/Y') }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-gray-100">
                <span class="text-gray-600">Khách hàng:</span>
                <span class="font-medium">{{ $order->customer->name ?? $order->guest_name }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-gray-100">
                <span class="text-gray-600">Số điện thoại:</span>
                <span class="font-medium">{{ $order->customer->phone ?? ($order->guest_phone ?? 'Không có') }}</span>
            </div>
            <div class="py-1">
                <p class="text-gray-600">Địa chỉ giao hàng:</p>
                <p class="font-medium">{{ $order->delivery_address }}</p>
            </div>
            @if ($order->notes)
                <div class="py-1">
                    <p class="text-gray-600">Ghi chú:</p>
                    <p class="font-medium text-red-500">{{ $order->notes }}</p>
                </div>
            @endif
            @if ($order->estimated_delivery_time)
                <div class="flex justify-between py-1 border-b border-gray-100">
                    <span class="text-gray-600">Dự kiến giao hàng:</span>
                    <span class="font-medium">{{ $order->estimated_delivery_time->format('H:i, d/m/Y') }}</span>
                </div>
            @endif
            @if ($order->actual_delivery_time)
                <div class="flex justify-between py-1 border-b border-gray-100">
                    <span class="text-gray-600">Thời gian giao thực tế:</span>
                    <span class="font-medium">{{ $order->actual_delivery_time->format('H:i, d/m/Y') }}</span>
                </div>
            @endif
        </div>

        {{-- Order Items --}}
        <div class="bg-white rounded-lg p-4 shadow-sm space-y-3">
            <h3 class="font-semibold text-lg mb-3">Chi tiết sản phẩm</h3>
            @foreach ($order->orderItems as $item)
                <div class="flex justify-between items-start py-2 border-b border-gray-100 last:border-b-0">
                    <div>
                        <p class="font-medium">{{ $item->productVariant->product->name ?? $item->combo->name }}</p>
                        @if ($item->productVariant)
                            <p class="text-sm text-gray-600">- {{ $item->productVariant->variant_name }}</p>
                        @endif
                        @if ($item->toppings->count() > 0)
                            <p class="text-sm text-gray-600">Toppings:
                                @foreach ($item->toppings as $topping)
                                    {{ $topping->topping->name }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </p>
                        @endif
                        <p class="text-sm text-gray-600">Số lượng: {{ $item->quantity }}</p>
                    </div>
                    <span class="font-medium">{{ number_format($item->price, 0, ',', '.') }} đ</span>
                </div>
            @endforeach
            <div class="flex justify-between font-bold text-lg pt-2">
                <span>Tổng tiền:</span>
                <span>{{ number_format($order->total_amount, 0, ',', '.') }} đ</span>
            </div>
        </div>

        {{-- Status History (Optional - if you have this relationship) --}}
        @if ($order->statusHistory->isNotEmpty())
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <h3 class="font-semibold text-lg mb-3">Lịch sử trạng thái</h3>
                <div class="relative pl-6">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gray-200 rounded-full"></div>
                    @foreach ($order->statusHistory->sortByDesc('changed_at') as $history)
                        <div class="mb-4 relative">
                            <div
                                class="absolute -left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 bg-blue-500 rounded-full border-2 border-white">
                            </div>
                            <p class="font-medium text-gray-800">{{ $history->new_status_text }}</p>
                            <p class="text-sm text-gray-600">{{ $history->changed_at->format('H:i, d/m/Y') }}</p>
                            @if ($history->note)
                                <p class="text-xs text-gray-500 italic">{{ $history->note }}</p>
                            @endif
                            <p class="text-xs text-gray-500">Bởi: {{ $history->changedBy->full_name ?? 'Hệ thống' }}
                                ({{ $history->changed_by_role }})</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Toast Container --}}
        <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const orderId = "{{ $order->id }}";

            function sendStatusUpdate(url, confirmationMessage, successMessage) {
                if (confirmationMessage && !confirm(confirmationMessage)) {
                    return; // User cancelled
                }
                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast(successMessage, 'success');
                            setTimeout(() => window.location.reload(),
                            1000); // Reload to show updated status/buttons
                        } else {
                            showToast(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error updating status:', error);
                        showToast('Có lỗi xảy ra khi cập nhật trạng thái.', 'error');
                    });
            }

            // Event Listeners for action buttons
            const acceptBtn = document.getElementById('accept-order-btn');
            if (acceptBtn) {
                acceptBtn.addEventListener('click', () => {
                    sendStatusUpdate(
                        `{{ route('driver.orders.accept', $order->id) }}`,
                        'Bạn có chắc chắn muốn nhận đơn hàng này?',
                        'Đã nhận đơn hàng thành công!'
                    );
                });
            }

            const confirmAssignedBtn = document.getElementById('confirm-assigned-btn');
            if (confirmAssignedBtn) {
                confirmAssignedBtn.addEventListener('click', () => {
                    sendStatusUpdate(
                        `{{ route('driver.orders.confirmAssigned', $order->id) }}`,
                        'Xác nhận bạn đã được giao đơn hàng này?',
                        'Đã xác nhận đơn hàng thành công!'
                    );
                });
            }

            const confirmPickupBtn = document.getElementById('confirm-pickup-btn');
            if (confirmPickupBtn) {
                confirmPickupBtn.addEventListener('click', () => {
                    sendStatusUpdate(
                        `{{ route('driver.orders.confirmPickup', $order->id) }}`,
                        'Xác nhận đã lấy hàng và sẵn sàng giao?',
                        'Đã xác nhận lấy hàng thành công!'
                    );
                });
            }

            const startTransitBtn = document.getElementById('start-transit-btn');
            if (startTransitBtn) {
                startTransitBtn.addEventListener('click', () => {
                    sendStatusUpdate(
                        `{{ route('driver.orders.startTransit', $order->id) }}`,
                        'Xác nhận bạn đã bắt đầu giao hàng?',
                        'Đơn hàng đang trên đường giao!'
                    );
                });
            }

            const confirmDeliveryBtn = document.getElementById('confirm-delivery-btn');
            if (confirmDeliveryBtn) {
                confirmDeliveryBtn.addEventListener('click', () => {
                    sendStatusUpdate(
                        `{{ route('driver.orders.confirmDelivery', $order->id) }}`,
                        'Xác nhận bạn đã giao hàng thành công?',
                        'Đã giao hàng thành công!'
                    );
                });
            }

            const failOrderBtn = document.getElementById('fail-order-btn');
            if (failOrderBtn) {
                failOrderBtn.addEventListener('click', () => {
                    const reason = prompt('Vui lòng nhập lý do đơn hàng thất bại:');
                    if (reason !== null && reason.trim() !== '') {
                        fetch(`{{ route('driver.orders.failOrder', $order->id) }}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({
                                    reason: reason
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    showToast(data.message, 'success');
                                    setTimeout(() => window.location.reload(), 1000);
                                } else {
                                    showToast(data.message, 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error failing order:', error);
                                showToast('Có lỗi xảy ra khi báo cáo đơn hàng thất bại.', 'error');
                            });
                    } else if (reason !== null) { // User entered empty string
                        showToast('Lý do thất bại không được để trống.', 'error');
                    }
                });
            }

            // Global function for calling customer (using existing modal logic)
            window.callCustomer = function() {
                const customerPhone = "{{ $order->customer->phone ?? ($order->guest_phone ?? '') }}";

                if (customerPhone && customerPhone !== '') {
                    showConfirmationModal(
                        'Xác nhận cuộc gọi',
                        `Bạn có muốn thực hiện cuộc gọi đến số ${customerPhone} không?`,
                        () => {
                            window.location.href = `tel:${customerPhone}`;
                        }, {
                            confirmText: 'Gọi ngay',
                            confirmColor: 'green',
                            icon: 'fas fa-phone-alt',
                            iconColor: 'green'
                        }
                    );
                } else {
                    showConfirmationModal(
                        'Không tìm thấy SĐT',
                        'Không có thông tin số điện thoại của khách hàng cho đơn hàng này.',
                        () => {}, {
                            confirmText: 'Đã hiểu',
                            confirmColor: 'gray',
                            icon: 'fas fa-exclamation-circle',
                            iconColor: 'gray'
                        }
                    );
                }
            };

            // Toast Notification Helper (copy from dashboard.blade.php or use a shared JS file)
            function showToast(message, type = 'info', duration = 3000) {
                let toastContainer = document.getElementById('toast-container');
                if (!toastContainer) {
                    toastContainer = document.createElement('div');
                    toastContainer.id = 'toast-container';
                    toastContainer.className = 'fixed bottom-4 right-4 z-50 space-y-2';
                    document.body.appendChild(toastContainer);
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

            // Universal Confirmation Modal Helper (copy from your existing JS or create a shared one)
            function showConfirmationModal(title, message, onConfirm, options = {}) {
                // This is a placeholder. You should replace this with your actual modal implementation (e.g., using a library like SweetAlert2 or a custom modal).
                // For demonstration, a simple prompt/confirm is used.
                // In a real application, you'd have a reusable modal component.

                // Example of a simple alert-based confirmation
                if (confirm(`${title}\n\n${message}`)) {
                    onConfirm();
                }
            }
        });
    </script>
@endpush
