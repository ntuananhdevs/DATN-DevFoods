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
                @case('driver_accepted')
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
document.addEventListener('DOMContentLoaded', function() {

    /**
     * ===================================================================
     * MODULE: DRIVER ORDER DETAIL PAGE LOGIC
     * Gói toàn bộ logic của trang vào một object để đồng nhất với Customer
     * ===================================================================
     */
    const DriverOrderDetailPage = {
        
        //-------------------------------------------------
        // PHẦN 1: KHỞI TẠO VÀ CẤU HÌNH
        //-------------------------------------------------
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
            actionButtonContainer: document.querySelector('.space-y-3')
        },
        
        state: {
            orderId: null,
            onConfirmAction: () => {} // Lưu trữ hành động sẽ thực hiện khi bấm nút confirm
        },
        
        init() {
            if (!this.elements.card) {
                console.error('[DriverPage] Không tìm thấy card chi tiết đơn hàng.');
                return;
            }
            this.state.orderId = this.elements.card.dataset.orderId;
            this.setupEventListeners();
            this.initRealtimeListener();
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
        },

        //-------------------------------------------------
        // PHẦN 2: CÁC HÀM TIỆN ÍCH (MODAL & TOAST)
        //-------------------------------------------------

        showToast(message, type = 'success') {
            // Đảm bảo message luôn là một chuỗi có thể hiển thị
            const displayMessage = message || (type === 'error' ? 'Có lỗi xảy ra' : 'Thao tác thành công');

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

            toast.className = `fixed top-5 right-5 text-white px-4 py-3 rounded-lg shadow-lg z-[101] transition-all duration-300 opacity-0 transform translate-x-full ${bgColor}`;
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
            this.elements.modalConfirm.className = `w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-${config.confirmColor}-600 hover:bg-${config.confirmColor}-700`;
            this.elements.modalIcon.className = `mx-auto w-12 h-12 rounded-full flex items-center justify-center bg-${config.iconColor}-100 text-${config.iconColor}-600 mb-4`;
            this.elements.modalIcon.firstElementChild.className = `${config.icon} text-2xl`;
            this.state.onConfirmAction = onConfirm;
            this.elements.modal.classList.remove('hidden');
        },

        closeModal() {
            this.elements.modal.classList.add('hidden');
        },

        //-------------------------------------------------
        // PHẦN 3: XỬ LÝ HÀNH ĐỘNG VÀ REAL-TIME
        //-------------------------------------------------

        performAction(url, successMessage, button) {
            const originalContent = button?.innerHTML;
            if(button) {
                button.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Đang xử lý...`;
                button.disabled = true;
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (ok && data.success) {
                    // Ưu tiên message từ server, nếu không có thì dùng message mặc định
                    this.showToast(data.message || successMessage, 'success');
                    
                    // Cập nhật giao diện không reload (bạn có thể thay đổi lại ở đây nếu muốn)
                    // this.updateUIAfterAction(data.order);
                    // setTimeout(() => window.location.reload(), 1000);

                } else {
                    // Ném lỗi với message từ server hoặc message mặc định
                    throw new Error(data.message || 'Thao tác thất bại.');
                }
            })
            .catch(error => {
                // `error.message` sẽ luôn có giá trị ở đây
                this.showToast(error.message, 'error');
                if(button) {
                    button.innerHTML = originalContent;
                    button.disabled = false;
                }
            });
        },

        updateUIAfterAction(orderData) {
            if(!orderData) return;

            // 1. Cập nhật khối trạng thái ở trên cùng
            const statusColor = orderData.status_color;
            this.elements.statusIcon.className = orderData.status_icon;
            this.elements.statusIcon.parentElement.style.backgroundColor = statusColor.bg;
            this.elements.statusIcon.parentElement.style.color = statusColor.text;
            this.elements.statusText.textContent = orderData.status_text;
            this.elements.statusTime.textContent = `Cập nhật lúc: ${new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })}`;
            this.elements.statusBadge.style.backgroundColor = statusColor.bg;
            this.elements.statusBadge.style.color = statusColor.text;
            
            // 2. Cập nhật khu vực nút bấm hành động ở dưới cùng
            let newButtonHTML = '';
            switch(orderData.status) {
                case 'driver_accepted': // Giả sử trạng thái sau khi accept là đây
                    newButtonHTML = `<button onclick="DriverOrderDetailPage.confirmPickupAction(this)" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-indigo-700"><i class="fas fa-shopping-bag mr-2"></i>Xác nhận đã lấy hàng</button>`;
                    break;
                case 'in_transit': // Giả sử trạng thái sau khi pickup là đây
                    newButtonHTML = `<button onclick="DriverOrderDetailPage.confirmDeliveryAction(this)" class="w-full bg-green-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-green-700"><i class="fas fa-check-double mr-2"></i>Xác nhận đã giao hàng</button>
                                     <button onclick="DriverOrderDetailPage.navigateAction()" class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-purple-700 mt-3"><i class="fas fa-route mr-2"></i>Xem bản đồ lớn</button>`;
                    break;
                case 'delivered':
                case 'item_received':
                    newButtonHTML = `<div class="bg-green-50 p-4 rounded-lg text-center"><i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i><p class="text-green-800 font-medium">Đơn hàng đã được giao thành công</p><p class="text-sm text-green-600">Lúc: ${orderData.actual_delivery_time}</p></div>`;
                    break;
                default:
                    newButtonHTML = '<p class="text-center text-gray-500">Không có hành động nào.</p>';
            }
            this.elements.actionButtonContainer.innerHTML = newButtonHTML;
        },

        initRealtimeListener() {
            if (window.Echo) {
                window.Echo.private(`order.${this.state.orderId}`)
                    .listen('.OrderStatusUpdated', (event) => {
                        // Gọi hàm showToast đã được sửa lỗi
                        this.showToast(`Trạng thái cập nhật: ${event.status_text}`, 'info');
                        setTimeout(() => window.location.reload(), 2500);
                    });
            }
        },

        //-------------------------------------------------
        // PHẦN 4: CÁC HÀM GỌI HÀNH ĐỘNG TỪ VIEW
        //-------------------------------------------------

        acceptAction(button) {
            this.showModal('Chấp nhận đơn hàng?', 'Bạn có chắc chắn muốn nhận đơn hàng này không?', 
                () => this.performAction("{{ route('driver.orders.accept', $order->id) }}", 'Đã chấp nhận đơn hàng!', button),
                { confirmText: 'Nhận đơn', confirmColor: 'blue', icon: 'fas fa-check', iconColor: 'blue' }
            );
        },

        confirmPickupAction(button) {
            this.showModal('Xác nhận lấy hàng?', 'Bạn có chắc chắn đã nhận hàng từ chi nhánh?',
                () => this.performAction("{{ route('driver.orders.confirm_pickup', $order->id) }}", 'Đã xác nhận lấy hàng!', button),
                { confirmText: 'Đã lấy hàng', confirmColor: 'indigo', icon: 'fas fa-shopping-bag', iconColor: 'indigo' }
            );
        },

        confirmDeliveryAction(button) {
            this.showModal('Xác nhận giao hàng?', 'Bạn có chắc chắn đã giao hàng thành công?',
                () => this.performAction("{{ route('driver.orders.confirm_delivery', $order->id) }}", 'Đã giao hàng thành công!', button),
                { confirmText: 'Đã giao xong', confirmColor: 'green', icon: 'fas fa-check-double', iconColor: 'green' }
            );
        },

        navigateAction() {
            window.open(`/driver/orders/{{ $order->id }}/navigate`, '_blank');
        },

        callCustomerAction() {
            const phone = '{{ $order->customer_phone ?? "" }}';
            if (phone) {
                this.showModal('Xác nhận cuộc gọi', `Bạn muốn gọi đến số ${phone}?`, 
                    () => window.location.href = `tel:${phone}`,
                    { confirmText: 'Gọi ngay', confirmColor: 'green', icon: 'fas fa-phone-alt', iconColor: 'green' }
                );
            } else {
                this.showToast('Không có số điện thoại khách hàng.', 'error');
            }
        }
    };

    // Khởi chạy logic
    DriverOrderDetailPage.init();

    // Để có thể gọi từ HTML, chúng ta cần đưa các hàm action ra ngoài scope
    window.acceptOrder = (btn) => DriverOrderDetailPage.acceptAction(btn);
    window.confirmPickup = (btn) => DriverOrderDetailPage.confirmPickupAction(btn);
    window.confirmDelivery = (btn) => DriverOrderDetailPage.confirmDeliveryAction(btn);
    window.startDelivery = () => DriverOrderDetailPage.navigateAction();
    window.callCustomer = () => DriverOrderDetailPage.callCustomerAction();

});
</script>
@endpush