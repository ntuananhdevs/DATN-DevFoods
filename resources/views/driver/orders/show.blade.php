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
            <button onclick="history.back()" class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-full hover:bg-gray-200">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </button>
            <h1 class="text-lg font-bold">Chi tiết Đơn hàng #{{ $order->id }}</h1>
            <div class="w-10"></div> {{-- Placeholder để giữ cho tiêu đề ở giữa --}}
        </div>
        
        {{-- Order Status Card --}}
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl" style="background-color: {{ $order->status_color['bg'] }}; color: {{ $order->status_color['text'] }};">
                    <i class="{{ $order->status_icon }}"></i>
                </div>
                <div>
                    <h2 class="font-semibold">{{ $order->status_text }}</h2>
                    <p class="text-sm text-gray-500">Cập nhật lúc: {{ $order->updated_at->format('H:i') }}</p>
                </div>
                {{-- SỬA Ở ĐÂY --}}
                <span class="px-3 py-1 rounded-full text-sm ml-auto" style="background-color: {{ $order->status_color['bg'] }}; color: {{ $order->status_color['text'] }};">#{{ $order->id }}</span>
            </div>
        </div>

        {{-- Map Section --}}
        @if(in_array($order->status, ['delivering', 'shipping']))
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div id="orderMap" class="h-48 w-full"></div>
            </div>
        @endif

        {{-- Các card thông tin còn lại giữ nguyên --}}
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <h3 class="font-semibold mb-3">Thông tin khách hàng</h3>
            <div class="space-y-3">
                {{-- CẬP NHẬT: Xử lý cả khách hàng (user) và khách vãng lai (guest) --}}
                <div class="flex items-center space-x-3">
                    <i class="fas fa-user text-gray-400"></i>
                    <span>{{ $order->customer->full_name ?? $order->guest_name }}</span>
                    <button onclick="callCustomer()" class="ml-auto text-green-600 bg-green-50 px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-phone mr-1"></i>Gọi
                    </button>
                </div>
                <div class="flex items-start space-x-3">
                    <i class="fas fa-map-marker-alt text-gray-400 mt-1"></i>
                    <div>
                        <p class="font-medium">Địa chỉ giao hàng</p>
                        {{-- CẬP NHẬT: Sử dụng cột delivery_address đã được tạo sẵn --}}
                        <p class="text-sm text-gray-600">{{ $order->delivery_address }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <i class="fas fa-clock text-gray-400"></i>
                    <div>
                        <span class="font-medium">Thời gian giao hàng</span>
                        {{-- CẬP NHẬT: Sử dụng cột estimated_delivery_time --}}
                        <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($order->estimated_delivery_time)->format('H:i - d/m/Y') }}</p>
                    </div>
                </div>
                {{-- CẬP NHẬT: Sử dụng cột 'notes' (số nhiều) --}}
                @if($order->notes)
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
                {{-- CẬP NHẬT: Lặp qua orderItems, giả định relation là orderItems --}}
                @foreach($order->orderItems as $item)
                    <div class="flex justify-between">
                        <span>{{ $item->quantity }}x {{ $item->productVariant->product->name ?? 'Sản phẩm' }}</span>
                        {{-- CẬP NHẬT: Dùng cột total_price đã tính sẵn trong DB --}}
                        <span>{{ number_format($item->total_price, 0, ',', '.') }} đ</span>
                    </div>
                @endforeach
                <hr class="my-3">
                {{-- CẬP NHẬT: Thay đổi tên các cột cho khớp với migration --}}
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

        {{-- Action Buttons --}}
        <div class="space-y-3">
            @switch($order->status)
                @case('awaiting_driver')
                    <button onclick="acceptOrder()" class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-blue-700">
                        <i class="fas fa-check mr-2"></i>Chấp nhận đơn hàng
                    </button>
                    @break
                @case('driver_picked_up')
                    <button onclick="confirmPickup()" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-indigo-700">
                        <i class="fas fa-shopping-bag mr-2"></i>Xác nhận đã lấy hàng
                    </button>
                    @break
                @case('in_transit')
                    <button onclick="confirmDelivery()" class="w-full bg-green-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-green-700">
                        <i class="fas fa-check-double mr-2"></i>Xác nhận đã giao hàng
                    </button>
                    <button onclick="startDelivery()" class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-purple-700">
                        <i class="fas fa-route mr-2"></i>Xem bản đồ lớn
                    </button>
                    @break
                @case('delivered')
                @case('item_received')
                    <div class="bg-green-50 p-4 rounded-lg text-center">
                        <i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i>
                        <p class="text-green-800 font-medium">Đơn hàng đã được giao thành công</p>
                        <p class="text-sm text-green-600">Lúc: {{ optional($order->actual_delivery_time)->format('H:i d/m/Y') }}</p>
                    </div>
                    @break
                @case('cancelled')
                    <div class="bg-red-50 p-4 rounded-lg text-center">
                        <i class="fas fa-times-circle text-red-600 text-2xl mb-2"></i>
                        <p class="text-red-800 font-medium">Đơn hàng đã bị hủy</p>
                    </div>
                    @break
            @endswitch
            
            @if(!in_array($order->status, ['delivered', 'item_received', 'cancelled']))
                <button onclick="callCustomer()" class="w-full border border-gray-300 text-gray-700 py-3 rounded-lg font-medium bg-white hover:bg-gray-50">
                    <i class="fas fa-phone mr-2"></i>Gọi cho khách hàng
                </button>
            @endif
        </div>
    </div>

    {{-- CẬP NHẬT: Modal xác nhận --}}
    <div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-sm">
            <div class="p-6 text-center">
                <div id="modalIcon" class="mx-auto w-12 h-12 rounded-full flex items-center justify-center bg-blue-100 text-blue-600 mb-4">
                    <i class="fas fa-question text-2xl"></i>
                </div>
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Tiêu đề Modal</h3>
                <p id="modalMessage" class="text-sm text-gray-500 mt-2">Nội dung modal.</p>
            </div>
            <div class="flex items-center bg-gray-50 px-6 py-4 gap-3 rounded-b-lg">
                <button id="modalCancel" type="button" class="w-full py-2 px-4 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">Hủy bỏ</button>
                <button id="modalConfirm" type="button" class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">Xác nhận</button>
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
// ======================================================================
// PHẦN 1: CÁC HÀM TIỆN ÍCH TỰ CHỨA (MODAL & TOAST - KHÔNG CẦN FILE NGOÀI)
// ======================================================================

/**
 * Hiển thị một thông báo toast động, tự hủy sau vài giây.
 * @param {string} type - Loại toast ('success' hoặc 'error')
 * @param {string} message - Nội dung thông báo
 */
function showLocalToast(type, message) {
    // Tạo phần tử toast
    const toast = document.createElement('div');
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-times-circle';
    const color = type === 'success' ? '#4CAF50' : '#F44336';

    // Áp dụng CSS trực tiếp
    Object.assign(toast.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        backgroundColor: color,
        color: 'white',
        padding: '15px 20px',
        borderRadius: '8px',
        boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
        display: 'flex',
        alignItems: 'center',
        zIndex: '9999',
        opacity: '0',
        transform: 'translateX(100%)',
        transition: 'opacity 0.3s ease, transform 0.3s ease'
    });
    
    toast.innerHTML = `<i class="${icon}" style="margin-right: 10px; font-size: 1.2em;"></i> ${message}`;

    // Thêm vào trang
    document.body.appendChild(toast);

    // Animation hiển thị
    setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    }, 100);

    // Tự động xóa sau 3 giây
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}


// --- Các hàm điều khiển Modal Xác nhận có sẵn của bạn ---
// Đoạn code này đã tốt, chúng ta giữ lại và sử dụng
const modal = document.getElementById('confirmationModal');
const modalTitle = document.getElementById('modalTitle');
const modalMessage = document.getElementById('modalMessage');
const modalConfirm = document.getElementById('modalConfirm');
const modalCancel = document.getElementById('modalCancel');
const modalIcon = document.getElementById('modalIcon');
let confirmAction = () => {};

window.showConfirmationModal = function(title, message, onConfirm, config = { /* ... */ }) {
    if(!modal) return;
    modalTitle.textContent = title;
    modalMessage.textContent = message;
    modalConfirm.textContent = config.confirmText;
    modalConfirm.className = `w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-${config.confirmColor}-600 hover:bg-${config.confirmColor}-700`;
    modalIcon.className = `mx-auto w-12 h-12 rounded-full flex items-center justify-center bg-${config.iconColor}-100 text-${config.iconColor}-600 mb-4`;
    modalIcon.firstElementChild.className = `${config.icon} text-2xl`;
    confirmAction = onConfirm;
    modal.classList.remove('hidden');
}
function hideModal() {
    if(modal) modal.classList.add('hidden');
}


// ======================================================================
// PHẦN 2: LOGIC CHÍNH KHI TRANG ĐƯỢC TẢI
// ======================================================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('[SHOW] DOM đã tải. Bắt đầu chạy script.');

    // Gán sự kiện cho các nút trong modal (nếu modal tồn tại)
    if(modal) {
        modalConfirm.addEventListener('click', () => {
            confirmAction();
            hideModal();
        });
        modalCancel.addEventListener('click', hideModal);
        modal.addEventListener('click', (e) => { if (e.target === modal) { hideModal(); } });
    }

    // --- REAL-TIME LOGIC (PUSHER) ---
    const orderDetailsCard = document.getElementById('order-details-card');
    if (orderDetailsCard) {
        const orderId = orderDetailsCard.dataset.orderId;
        console.log(`[SHOW] Tìm thấy orderId: ${orderId}`);

        if (window.Echo && orderId) {
            console.log(`[SHOW] Đang kết nối tới Echo channel: private-order.${orderId}`);
            
            const privateChannel = window.Echo.private(`order.${orderId}`);

            // SỬA LỖI Ở ĐÂY: Dùng .subscribed() và .error() để gỡ lỗi kết nối
            privateChannel
                .subscribed(() => {
                    console.log(`[SHOW] Đã đăng ký thành công vào kênh: private-order.${orderId}`);
                })
                .error((error) => {
                    console.error(`[SHOW] LỖI khi đăng ký vào kênh private! Status: ${error.status}`, error);
                })
                .listen('.OrderStatusUpdated', (event) => {
                    console.log('[SHOW] ĐÃ NHẬN ĐƯỢC SỰ KIỆN REAL-TIME!', event);
                    
                    // Thay thế bằng hàm toast có sẵn của bạn
                    if(typeof dtmodalShowToast === 'function') {
                            dtmodalShowToast('info', { title: 'Trạng thái cập nhật', message: `Đơn hàng vừa được cập nhật: ${event.status_text}` });
                    } else {
                        // Hoặc dùng hàm toast tự chứa nếu dtmodal không tồn tại
                        showLocalToast('info', `Đơn hàng vừa được cập nhật: ${event.status_text}`);
                    }

                    setTimeout(() => window.location.reload(), 2000);
                });

        } else {
            console.error('[SHOW] Lỗi: window.Echo chưa được khởi tạo hoặc không tìm thấy orderId.');
        }
    } else {
        console.error('[SHOW] Lỗi: Không tìm thấy element có ID "order-details-card".');
    }
});


// ======================================================================
// PHẦN 3: CÁC HÀM HÀNH ĐỘNG (ACTION FUNCTIONS)
// ======================================================================

function handleApiResponse(response) {
    console.log('[DEBUG] Nhận được phản hồi từ server.');
    if (response.ok) return response.json();
    throw new Error('Lỗi mạng hoặc server.');
}

function handleSuccess(data) {
    console.log('[DEBUG] Xử lý thành công:', data);
    if (data.success) {
        // Gọi hàm toast tự chứa của chúng ta
        showLocalToast('success', data.message || 'Thao tác thành công!');
        // Chờ 2 giây để user thấy toast rồi mới reload
        setTimeout(() => window.location.reload(), 2000);
    } else {
        showErrorModal(data.message || 'Thao tác không thành công.');
    }
}

function handleError(error) {
    console.error('[DEBUG] Lỗi API:', error);
    showErrorModal('Đã có lỗi xảy ra. Vui lòng thử lại.');
}

// Hàm tiện ích để hiển thị lỗi bằng modal
function showErrorModal(message) {
    if(typeof showConfirmationModal === 'function') {
        showConfirmationModal('Thao tác thất bại', message, () => {}, { confirmText: 'Đã hiểu', confirmColor: 'red', icon: 'fas fa-exclamation-circle', iconColor: 'red' });
    }
}

// 1. Chấp nhận đơn hàng
function acceptOrder() {
    showConfirmationModal('Chấp nhận đơn hàng?', 'Bạn có chắc chắn muốn nhận đơn hàng này không?', () => {
        fetch("{{ route('driver.orders.accept', $order->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(handleApiResponse)
        .then(handleSuccess)
        .catch(handleError);
    }, { confirmText: 'Nhận đơn', confirmColor: 'blue', icon: 'fas fa-check', iconColor: 'blue' });
}

// 2. Xác nhận đã lấy hàng
function confirmPickup() {
    showConfirmationModal('Xác nhận lấy hàng?', 'Bạn có chắc chắn đã nhận hàng từ chi nhánh?', () => {
        fetch("{{ route('driver.orders.confirm_pickup', $order->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(handleApiResponse)
        .then(handleSuccess)
        .catch(handleError);
    }, { confirmText: 'Đã lấy hàng', confirmColor: 'indigo', icon: 'fas fa-shopping-bag', iconColor: 'indigo' });
}

// 3. Xác nhận đã giao hàng
function confirmDelivery() {
    showConfirmationModal('Xác nhận giao hàng?', 'Bạn có chắc chắn đã giao hàng thành công?', () => {
        fetch("{{ route('driver.orders.confirm_delivery', $order->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(handleApiResponse)
        .then(handleSuccess)
        .catch(handleError);
    }, { confirmText: 'Đã giao xong', confirmColor: 'green', icon: 'fas fa-check-double', iconColor: 'green' });
}

// Các hàm khác (giữ nguyên)
function startDelivery() { window.location.href = `/driver/orders/{{ $order->id }}/navigate`; }
function callCustomer() {
    const phone = '{{ $order->customer_phone ?? "" }}';
    if (phone && phone !== 'Không có') {
        showConfirmationModal('Xác nhận cuộc gọi', `Bạn có muốn thực hiện cuộc gọi đến số ${phone} không?`, () => {
            window.location.href = `tel:${phone}`;
        }, { confirmText: 'Gọi ngay', confirmColor: 'green', icon: 'fas fa-phone-alt', iconColor: 'green' });
    } else {
        showErrorModal('Không có thông tin số điện thoại của khách hàng cho đơn hàng này.');
    }
}
</script>
@endpush