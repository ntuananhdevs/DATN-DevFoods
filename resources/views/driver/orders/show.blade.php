@extends('layouts.driver.masterLayout')

@section('title', 'Chi tiết đơn hàng')
{{-- Bỏ page-title ở đây để đưa vào header mới --}}

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
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
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-xl" style="background-color: {{ $order->status_color }};">
                <i class="{{ $order->status_icon }}"></i>
            </div>
            <div>
                <h2 class="font-semibold">{{ $order->status_text }}</h2>
                <p class="text-sm text-gray-500">Cập nhật lúc: {{ $order->updated_at->format('H:i') }}</p>
            </div>
            <span class="text-white px-3 py-1 rounded-full text-sm ml-auto" style="background-color: {{ $order->status_color }};">#{{ $order->id }}</span>
        </div>
    </div>

            {{-- Order Status Card --}}
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl"
                        style="background-color: {{ $order->status_color['bg'] ?? '#f0f0f0' }}; color: {{ $order->status_color['text'] ?? '#333' }};">
                        {!! $order->status_svg_icon !!} {{-- Sử dụng thuộc tính mới và nhớ dùng {!! !!} để render HTML --}}
                    </div>
                    <div>
                        <h2 class="font-semibold">{{ $order->status_text }}</h2>
                        <p class="text-sm text-gray-500">Cập nhật lúc: {{ $order->updated_at->format('H:i') }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm ml-auto"
                        style="background-color: {{ $order->status_color['bg'] ?? '#f0f0f0' }}; color: {{ $order->status_color['text'] ?? '#333' }};">#{{ $order->order_code }}</span>
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
                        {{-- Nút "Chấp nhận đơn hàng" --}}
                        <button data-action="accept-order"
                            class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-blue-700 flex items-center justify-center">
                            {{-- Icon tương ứng với 'accepted' hoặc 'awaiting_driver' (package icon) trong index --}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-package w-4 h-4 mr-2">
                                <path d="m7.5 4.27 9 5.15"></path>
                                <path
                                    d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z">
                                </path>
                                <path d="m3.3 7 8.7 5 8.7-5"></path>
                                <path d="M12 22V12"></path>
                            </svg>
                            Chấp nhận đơn hàng
                        </button>
                    @break

                    @case('driver_picked_up')
                        {{-- Nút "Xác nhận đã lấy hàng" --}}
                        <button data-action="confirm-pickup"
                            class="w-full bg-indigo-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-indigo-700 flex items-center justify-center">
                            {{-- Icon 'truck' (driver_picked_up, in_transit) trong index --}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-truck w-4 h-4 mr-2">
                                <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path>
                                <path d="M15 18H9"></path>
                                <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 2 0 0 0 17.52 8H14"></path>
                                <circle cx="17" cy="18" r="2"></circle>
                                <circle cx="7" cy="18" r="2"></circle>
                            </svg>
                            Xác nhận đã lấy hàng
                        </button>
                    @break

                    @case('in_transit')
                        {{-- Nút "Xác nhận đã giao hàng" --}}
                        <button data-action="confirm-delivery"
                            class="w-full bg-green-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-green-700 flex items-center justify-center mb-2">
                            {{-- Icon 'circle-check-big' (delivered) trong index --}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-circle-check-big w-4 h-4 mr-2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <path d="m9 11 3 3L22 4"></path>
                            </svg>
                            Xác nhận đã giao hàng
                        </button>
                        {{-- Nút "Xem bản đồ lớn" --}}
                        <button data-action="start-delivery"
                            class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-purple-700 flex items-center justify-center">
                            {{-- Icon 'map-pin' (đã dùng cho địa chỉ trong index) hoặc 'route' (thích hợp hơn cho bản đồ) --}}
                            {{-- Tôi dùng icon 'route' vì nó phù hợp hơn với chức năng "Xem bản đồ lớn" --}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-route w-4 h-4 mr-2">
                                <path d="M3 17h3L10 3h4l4 14h3" />
                                <path d="M14 17h7" />
                                <path d="M3 21h18" />
                            </svg>
                            Xem bản đồ lớn
                        </button>
                    @break

                    @case('delivered')
                    @case('item_received')
                        <div class="bg-green-50 p-4 rounded-lg text-center">
                            {{-- Icon 'circle-check-big' (delivered) trong index --}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-circle-check-big text-green-600 w-8 h-8 mx-auto mb-2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <path d="m9 11 3 3L22 4"></path>
                            </svg>
                            <p class="text-green-800 font-medium">Đơn hàng đã được giao thành công</p>
                            <p class="text-sm text-green-600">Lúc:
                                {{ optional($order->actual_delivery_time)->format('H:i d/m/Y') }}</p>
                        </div>
                    @break

                    @case('cancelled')
                        <div class="bg-red-50 p-4 rounded-lg text-center">
                            {{-- Icon 'x-circle' (cancelled) trong index --}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-x-circle text-red-600 w-8 h-8 mx-auto mb-2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="m15 9-6 6"></path>
                                <path d="m9 9 6 6"></path>
                            </svg>
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
            @case('processing')
                <button onclick="confirmPickup()" class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-blue-700">
                    <i class="fas fa-check mr-2"></i>Xác nhận đã lấy hàng
                </button>
                @break
            @case('delivering')
            @case('shipping')
                <button onclick="confirmDelivery()" class="w-full bg-green-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-green-700">
                    <i class="fas fa-check-double mr-2"></i>Xác nhận đã giao hàng
                </button>
                <button onclick="startDelivery()" class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium shadow-sm hover:bg-purple-700">
                    <i class="fas fa-route mr-2"></i>Xem bản đồ lớn
                </button>
                @break
            @case('delivered')
                <div class="bg-green-50 p-4 rounded-lg text-center"><i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i><p class="text-green-800 font-medium">Đơn hàng đã được giao thành công</p><p class="text-sm text-green-600">Lúc: {{ \Carbon\Carbon::parse($order->delivery_date)->format('H:i d/m/Y') }}</p></div>
                @break
        @endswitch
        
        @if(!in_array($order->status, ['delivered', 'cancelled']))
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
@endsection


@push('scripts')
<script>
// CẬP NHẬT: Toàn bộ script để điều khiển Modal
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('confirmationModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const modalConfirm = document.getElementById('modalConfirm');
    const modalCancel = document.getElementById('modalCancel');
    const modalIcon = document.getElementById('modalIcon');
    let confirmAction = () => {};

    // Hàm hiển thị modal chung
    window.showConfirmationModal = function(title, message, onConfirm,
        config = {
            confirmText: 'Xác nhận',
            confirmColor: 'blue',
            icon: 'fas fa-question',
            iconColor: 'blue'
        }) {
        modalTitle.textContent = title;
        modalMessage.textContent = message;

        // Cấu hình nút xác nhận
        modalConfirm.textContent = config.confirmText;
        modalConfirm.className = `w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-${config.confirmColor}-600 hover:bg-${config.confirmColor}-700`;

        // Cấu hình icon
        modalIcon.className = `mx-auto w-12 h-12 rounded-full flex items-center justify-center bg-${config.iconColor}-100 text-${config.iconColor}-600 mb-4`;
        modalIcon.firstElementChild.className = `${config.icon} text-2xl`;

        confirmAction = onConfirm;
        modal.classList.remove('hidden');
    }

    // Hàm ẩn modal
    function hideModal() {
        modal.classList.add('hidden');
    }

    // Gán sự kiện cho các nút
    modalConfirm.addEventListener('click', () => {
        confirmAction();
        hideModal();
    });
    modalCancel.addEventListener('click', hideModal);
    modal.addEventListener('click', (e) => { // Click ra ngoài để tắt
        if (e.target === modal) {
            hideModal();
        }
    });

    // Mapbox Script
    @if(in_array($order->status, ['delivering', 'shipping']))
        if (typeof mapboxgl !== 'undefined') {
            mapboxgl.accessToken = '{{ env("MAPBOX_API_KEY") }}';
            initMap();
        } else {
            console.error('Lỗi: Thư viện Mapbox GL JS chưa được tải.');
        }
    @endif
});

// Hàm khởi tạo map
function initMap() {
    const customerCoords = { lat: {{ $order->address->latitude ?? $order->guest_latitude ?? 21.0285 }}, lng: {{ $order->address->longitude ?? $order->guest_longitude ?? 105.8542 }} };
    const map = new mapboxgl.Map({ container: 'orderMap', style: 'mapbox://styles/mapbox/streets-v11', center: [customerCoords.lng, customerCoords.lat], zoom: 13 });
    new mapboxgl.Marker({ color: 'red' }).setLngLat([customerCoords.lng, customerCoords.lat]).setPopup(new mapboxgl.Popup().setHTML('<strong>Điểm giao hàng</strong>')).addTo(map);
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            const driverCoords = [pos.coords.longitude, pos.coords.latitude];
            new mapboxgl.Marker({ color: 'blue' }).setLngLat(driverCoords).setPopup(new mapboxgl.Popup().setHTML('<strong>Vị trí của bạn</strong>')).addTo(map);
            const bounds = new mapboxgl.LngLatBounds().extend(driverCoords).extend([customerCoords.lng, customerCoords.lat]);
            map.fitBounds(bounds, { padding: 60 });
        });
    }
}

// CẬP NHẬT: Các hàm hành động để gọi modal
function confirmPickup() {
    showConfirmationModal(
        'Xác nhận lấy hàng?',
        'Bạn có chắc chắn đã nhận hàng từ chi nhánh và sẵn sàng để đi giao không?',
        () => {
            // Đảm bảo URL được tạo bằng hàm route() của Blade
            fetch("{{ route('driver.orders.confirm_pickup', $order->id) }}", { 
                method: 'POST', 
                headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')}
            })
            .then(res => res.json()).then(data => {
                if (data.success) {
                    // CẬP NHẬT: Chuyển hướng về trang danh sách, tab "Đang giao"
                    window.location.href = "{{ route('driver.orders.index', ['status' => 'delivering']) }}";
                } else {
                    alert(data.message);
                }
            });
        },
        { confirmText: 'Đã lấy hàng', confirmColor: 'blue', icon: 'fas fa-check', iconColor: 'blue' }
    );
}

function confirmDelivery() {
    showConfirmationModal(
        'Xác nhận giao hàng?',
        'Bạn có chắc chắn đã giao đơn hàng này thành công cho khách hàng không?',
        () => {
            // Đảm bảo URL được tạo bằng hàm route() của Blade
            fetch("{{ route('driver.orders.confirm_delivery', $order->id) }}", { 
                method: 'POST', 
                headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')}
            })
            .then(res => res.json()).then(data => {
                if (data.success) {
                    // CẬP NHẬT: Chuyển hướng về trang danh sách, tab "Đã giao"
                    window.location.href = "{{ route('driver.orders.index', ['status' => 'delivered']) }}";
                } else {
                    alert(data.message);
                }
            });
        },
        { confirmText: 'Đã giao xong', confirmColor: 'green', icon: 'fas fa-check-double', iconColor: 'green' }
    );
}

// Các hàm khác không cần modal
function startDelivery() { window.location.href = `/driver/orders/{{ $order->id }}/navigate`; }
function callCustomer() {
    // Lấy số điện thoại từ accessor như cũ
    const phone = '{{ $order->customer_phone ?? "" }}';

    // Kiểm tra xem số điện thoại có hợp lệ không
    if (phone && phone !== 'Không có') {
        // CẬP NHẬT: Gọi modal xác nhận thay vì gọi trực tiếp
        showConfirmationModal(
            'Xác nhận cuộc gọi', // Tiêu đề modal
            `Bạn có muốn thực hiện cuộc gọi đến số ${phone} không?`, // Nội dung, có hiển thị SĐT
            () => { // Hàm sẽ chạy khi người dùng bấm "Gọi ngay"
                window.location.href = `tel:${phone}`;
            },
            { // Cấu hình cho modal
                confirmText: 'Gọi ngay',
                confirmColor: 'green',
                icon: 'fas fa-phone-alt',
                iconColor: 'green'
            }
        );
    } else {
        // CẬP NHẬT: Hiển thị lỗi bằng modal thay vì alert()
        // Chúng ta sẽ dùng lại hàm modal cũ với một chút thay đổi
        showConfirmationModal(
            'Không tìm thấy SĐT',
            'Không có thông tin số điện thoại của khách hàng cho đơn hàng này.',
            () => {}, // Không làm gì khi bấm xác nhận
            {
                confirmText: 'Đã hiểu',
                confirmColor: 'gray',
                icon: 'fas fa-exclamation-circle',
                iconColor: 'gray',
                // Chúng ta sẽ cần một chút logic để ẩn nút "Hủy bỏ" nếu muốn
            }
        );
        // Để đơn giản hơn, bạn có thể chỉ cần dùng alert như cũ:
        // alert('Không tìm thấy số điện thoại khách hàng.');
    }
}
</script>

@endpush