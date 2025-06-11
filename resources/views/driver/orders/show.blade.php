@extends('layouts.driver.masterLayout')

@section('title', 'Chi tiết đơn hàng')
@section('page-title', 'Chi tiết đơn hàng #' . $orderId)

@section('content')
<div class="pt-4 p-4 space-y-4">
    <!-- Order Status -->
    <div class="bg-white rounded-lg p-4 shadow-sm">
        <div class="flex items-center space-x-3">
            @if($orderId == 1)
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-box text-white"></i>
                </div>
                <div>
                    <h2 class="font-semibold">Đã nhận đơn</h2>
                    <p class="text-sm text-gray-500">Cập nhật lúc: 11:45</p>
                </div>
                <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm ml-auto">#1</span>
            @elseif($orderId == 2)
                <div class="w-12 h-12 bg-orange-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-truck text-white"></i>
                </div>
                <div>
                    <h2 class="font-semibold">Đã lấy hàng</h2>
                    <p class="text-sm text-gray-500">Cập nhật lúc: 12:15</p>
                </div>
                <span class="bg-orange-600 text-white px-3 py-1 rounded-full text-sm ml-auto">#2</span>
            @elseif($orderId == 3)
                <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-white"></i>
                </div>
                <div>
                    <h2 class="font-semibold">Đã giao</h2>
                    <p class="text-sm text-gray-500">Cập nhật lúc: 14:25</p>
                </div>
                <span class="bg-green-600 text-white px-3 py-1 rounded-full text-sm ml-auto">#3</span>
            @else
                <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-shipping-fast text-white"></i>
                </div>
                <div>
                    <h2 class="font-semibold">Đang giao</h2>
                    <p class="text-sm text-gray-500">Cập nhật lúc: 14:20</p>
                </div>
                <span class="bg-purple-600 text-white px-3 py-1 rounded-full text-sm ml-auto">#4</span>
            @endif
        </div>
    </div>

    <!-- Map Section -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div id="orderMap" class="h-48 w-full"></div>
    </div>

    <!-- Customer Info -->
    <div class="bg-white rounded-lg p-4 shadow-sm">
        <h3 class="font-semibold mb-3">Thông tin khách hàng</h3>
        
        <div class="space-y-3">
            <div class="flex items-center space-x-3">
                <i class="fas fa-user text-gray-400"></i>
                <span>
                    @if($orderId == 1) Nguyễn Văn A
                    @elseif($orderId == 2) Trần Thị B
                    @elseif($orderId == 3) Lê Văn C
                    @else Phạm Thị D
                    @endif
                </span>
                <button class="ml-auto text-green-600 bg-green-50 px-3 py-1 rounded-full text-sm">
                    <i class="fas fa-phone mr-1"></i>Gọi
                </button>
            </div>
            
            <div class="flex items-start space-x-3">
                <i class="fas fa-map-marker-alt text-gray-400 mt-1"></i>
                <div>
                    <p class="font-medium">Địa chỉ giao hàng</p>
                    <p class="text-sm text-gray-600">
                        @if($orderId == 1) 123 Đường Láng, Đống Đa, Hà Nội<br>Láng Thượng, Đống Đa, Hà Nội
                        @elseif($orderId == 2) 456 Phố Huế, Hai Bà Trưng, Hà Nội<br>Phố Huế, Hai Bà Trưng, Hà Nội
                        @elseif($orderId == 3) 789 Đường Giải Phóng, Hoàng Mai, Hà Nội<br>Hoàng Liệt, Hoàng Mai, Hà Nội
                        @else 321 Đường Cầu Giấy, Cầu Giấy, Hà Nội<br>Dịch Vọng, Cầu Giấy, Hà Nội
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <i class="fas fa-clock text-gray-400"></i>
                <div>
                    <span class="font-medium">Thời gian giao hàng</span>
                    <p class="text-sm text-gray-600">
                        @if($orderId == 1) 12:30 - 15/01/2024
                        @elseif($orderId == 2) 13:00 - 15/01/2024
                        @elseif($orderId == 3) 14:30 - 15/01/2024
                        @else 15:00 - 15/01/2024
                        @endif
                    </p>
                </div>
            </div>
            
            @if($orderId == 2)
                <div class="bg-yellow-50 p-3 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        <strong>Ghi chú:</strong><br>
                        Để ở bàn bảo vệ tầng 1
                    </p>
                </div>
            @elseif($orderId == 4)
                <div class="bg-yellow-50 p-3 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        <strong>Ghi chú:</strong><br>
                        Giao tại cổng chính tòa nhà
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Order Details -->
    <div class="bg-white rounded-lg p-4 shadow-sm">
        <h3 class="font-semibold mb-3">Chi tiết đơn hàng</h3>
        
        <div class="space-y-3">
            @if($orderId == 1)
                <div class="flex justify-between">
                    <span>2x Phở Bò Tái</span>
                    <span>80.000 đ</span>
                </div>
                <div class="flex justify-between">
                    <span>1x Chả cá Lã Vọng</span>
                    <span>100.000 đ</span>
                </div>
            @elseif($orderId == 2)
                <div class="flex justify-between">
                    <span>1x Bún chả Hà Nội</span>
                    <span>70.000 đ</span>
                </div>
                <div class="flex justify-between">
                    <span>1x Nem rán</span>
                    <span>50.000 đ</span>
                </div>
            @elseif($orderId == 3)
                <div class="flex justify-between">
                    <span>2x Cơm tấm sườn nướng</span>
                    <span>85.000 đ</span>
                </div>
                <div class="flex justify-between">
                    <span>2x Chè ba màu</span>
                    <span>40.000 đ</span>
                </div>
            @else
                <div class="flex justify-between">
                    <span>3x Bánh mì thịt nướng</span>
                    <span>35.000 đ</span>
                </div>
                <div class="flex justify-between">
                    <span>2x Nước cam tươi</span>
                    <span>25.000 đ</span>
                </div>
            @endif
            
            <hr class="my-3">
            
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span>Tổng tiền hàng</span>
                    <span>
                        @if($orderId == 1) 180.000 đ
                        @elseif($orderId == 2) 120.000 đ
                        @elseif($orderId == 3) 250.000 đ
                        @else 160.000 đ
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span>Phí giao hàng</span>
                    <span>
                        @if($orderId == 1) 25.000 đ
                        @elseif($orderId == 2) 20.000 đ
                        @elseif($orderId == 3) 30.000 đ
                        @else 22.000 đ
                        @endif
                    </span>
                </div>
                <div class="flex justify-between text-green-600">
                    <span>Giảm giá</span>
                    <span>
                        @if($orderId == 1) -5.000 đ
                        @elseif($orderId == 2) 0 đ
                        @elseif($orderId == 3) -10.000 đ
                        @else 0 đ
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span>Thuế</span>
                    <span>
                        @if($orderId == 1) 2.000 đ
                        @elseif($orderId == 2) 1.500 đ
                        @elseif($orderId == 3) 3.000 đ
                        @else 1.800 đ
                        @endif
                    </span>
                </div>
            </div>
            
            <hr class="my-3">
            
            <div class="flex justify-between font-semibold text-lg">
                <span>Tổng thanh toán</span>
                <span class="text-green-600">
                    @if($orderId == 1) 202.000 đ
                    @elseif($orderId == 2) 141.500 đ
                    @elseif($orderId == 3) 273.000 đ
                    @else 183.800 đ
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="space-y-3">
        @if($orderId == 1)
            <button onclick="confirmPickup()" class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium">
                <i class="fas fa-check mr-2"></i>Xác nhận đã lấy hàng
            </button>
        @elseif($orderId == 2)
            <button onclick="startDelivery()" class="w-full bg-orange-600 text-white py-3 rounded-lg font-medium">
                <i class="fas fa-route mr-2"></i>Bắt đầu giao hàng
            </button>
        @elseif($orderId == 3)
            <div class="bg-green-50 p-4 rounded-lg text-center">
                <i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i>
                <p class="text-green-800 font-medium">Đơn hàng đã được giao thành công</p>
                <p class="text-sm text-green-600">Đã giao lúc: 14:25</p>
            </div>
        @else
            <button onclick="confirmDelivery()" class="w-full bg-green-600 text-white py-3 rounded-lg font-medium">
                <i class="fas fa-check mr-2"></i>Xác nhận đã giao hàng
            </button>
            <button onclick="continueNavigation()" class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium">
                <i class="fas fa-route mr-2"></i>Tiếp tục điều hướng
            </button>
        @endif
        
        <button onclick="callCustomer()" class="w-full border border-gray-300 text-gray-700 py-3 rounded-lg font-medium">
            <i class="fas fa-phone mr-2"></i>Gọi cho khách hàng
        </button>
    </div>
</div>

@push('scripts')
<script>
let map;
let customerMarker;
let driverMarker;

// Initialize map
function initMap() {
    // Customer coordinates (mock data)
    const customerCoords = {
        @if($orderId == 1)
            lat: 21.0285, lng: 105.8542  // Đường Láng
        @elseif($orderId == 2)
            lat: 21.0245, lng: 105.8412  // Phố Huế
        @elseif($orderId == 3)
            lat: 20.9967, lng: 105.8441  // Giải Phóng
        @else
            lat: 21.0313, lng: 105.7981  // Cầu Giấy
        @endif
    };
    
    // Driver current location (mock data)
    const driverCoords = {
        lat: 21.0227, lng: 105.8194  // Hà Nội center
    };

    map = new mapboxgl.Map({
        container: 'orderMap',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [customerCoords.lng, customerCoords.lat],
        zoom: 13
    });

    // Add customer marker
    customerMarker = new mapboxgl.Marker({ color: 'red' })
        .setLngLat([customerCoords.lng, customerCoords.lat])
        .setPopup(new mapboxgl.Popup().setHTML('<p>Địa chỉ giao hàng</p>'))
        .addTo(map);

    // Add driver marker
    driverMarker = new mapboxgl.Marker({ color: 'blue' })
        .setLngLat([driverCoords.lng, driverCoords.lat])
        .setPopup(new mapboxgl.Popup().setHTML('<p>Vị trí của bạn</p>'))
        .addTo(map);

    // Fit map to show both markers
    const bounds = new mapboxgl.LngLatBounds()
        .extend([customerCoords.lng, customerCoords.lat])
        .extend([driverCoords.lng, driverCoords.lat]);
    
    map.fitBounds(bounds, { padding: 50 });
}

function confirmPickup() {
    if (confirm('Xác nhận đã lấy hàng?')) {
        // Update order status
        fetch(`/api/orders/{{ $orderId }}/pickup`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(() => {
            window.location.reload();
        });
    }
}

function startDelivery() {
    // Redirect to full map view for navigation
    window.location.href = `/driver/orders/{{ $orderId }}/navigate`;
}

function confirmDelivery() {
    if (confirm('Xác nhận đã giao hàng thành công?')) {
        fetch(`/api/orders/{{ $orderId }}/deliver`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(() => {
            window.location.reload();
        });
    }
}

function continueNavigation() {
    window.location.href = `driver/orders/{{ $orderId }}/navigate`;
}

function callCustomer() {
    const phoneNumbers = {
        1: '0987654321',
        2: '0912345678', 
        3: '0901234567',
        4: '0934567890'
    };
    
    const phone = phoneNumbers[{{ $orderId }}];
    window.location.href = `tel:${phone}`;
}

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    initMap();
});
</script>
@endsection
