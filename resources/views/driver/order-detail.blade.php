@extends('layout.master')

@section('title', 'Chi tiết đơn hàng ' . $order['id'] . ' - Ứng dụng Tài xế')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('driver.orders') }}" class="mr-4 p-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7l9 11h-7z"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Chi tiết đơn hàng: {{ $order['id'] }}</h1>
    </div>

    <!-- Map -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div id="map" class="map-container rounded-lg"></div>
    </div>

    <!-- Order Information -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900">Thông tin đơn hàng</h2>
                <div class="flex items-center space-x-4">
                    <span>Trạng thái: 
                        <span class="font-bold {{ $order['status'] === 'Chờ nhận' ? 'text-blue-600' : ($order['status'] === 'Đang giao' ? 'text-yellow-600' : ($order['status'] === 'Đã hoàn thành' ? 'text-green-600' : 'text-red-600')) }}" data-order-status>
                            {{ $order['status'] }}
                        </span>
                    </span>
                    @if($order['status'] === 'Đang giao' && isset($order['estimated_delivery_time']))
                        <span class="text-sm text-gray-500 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Dự kiến giao: {{ $order['estimated_delivery_time'] }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <div>
                            <strong>Lấy hàng tại:</strong> {{ $order['pickup_branch'] }}
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-3 mt-0.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div>
                            <strong>Giao đến:</strong> {{ $order['delivery_address'] }}
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <div>
                            <strong>Người nhận:</strong> {{ $order['customer_name'] }}
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <div>
                            <strong>SĐT:</strong> 
                            <a href="tel:{{ $order['customer_phone'] }}" class="text-blue-600 hover:text-blue-800">{{ $order['customer_phone'] }}</a>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <strong>Đặt lúc:</strong> {{ $order['order_time'] }}
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                        <div>
                            <strong>Khoảng cách:</strong> {{ $order['distance'] }} km
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t pt-6 mt-6">
                <h3 class="font-semibold text-gray-900 mb-4">Chi tiết món:</h3>
                <ul class="space-y-2">
                    @foreach($order['items'] as $item)
                    <li class="flex items-center">
                        <svg class="w-2 h-2 mr-3 text-gray-400" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3"/>
                        </svg>
                        {{ $item['name'] }} (x{{ $item['quantity'] }}) - {{ number_format($item['price'] * $item['quantity']) }}đ
                    </li>
                    @endforeach
                </ul>
            </div>

            @if(isset($order['notes']) && $order['notes'])
            <div class="border-t pt-6 mt-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-2 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="font-semibold text-blue-900">Ghi chú của khách:</h4>
                            <p class="text-blue-800">{{ $order['notes'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="border-t pt-6 mt-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>Tổng tiền hàng:</span>
                            <span class="font-semibold">{{ number_format($order['total_amount']) }}đ</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Phí vận chuyển:</span>
                            <span class="font-semibold">{{ number_format($order['shipping_fee']) }}đ</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t pt-2">
                            <span>Khách trả:</span>
                            <span>{{ number_format($order['final_total']) }}đ</span>
                        </div>
                    </div>
                    <div class="md:text-right">
                        <div class="text-lg font-bold text-green-600">
                            Thu nhập tài xế: {{ number_format($order['driver_earnings']) }}đ
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex flex-col md:flex-row gap-3">
                @if($order['status'] === 'Chờ nhận')
                    <button onclick="updateOrderStatus('{{ $order['id'] }}', 'Đang giao')" class="flex-1 bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Đã nhận hàng & Bắt đầu giao
                    </button>
                @elseif($order['status'] === 'Đang giao')
                    <button onclick="updateOrderStatus('{{ $order['id'] }}', 'Đã hoàn thành')" class="flex-1 bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Đã giao thành công
                    </button>
                @elseif($order['status'] === 'Đã hoàn thành')
                    <div class="flex-1 bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="font-semibold text-green-900">Đơn hàng đã hoàn thành!</h4>
                                <p class="text-green-800">Thu nhập {{ number_format($order['driver_earnings']) }}đ đã được ghi nhận.</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if(in_array($order['status'], ['Chờ nhận', 'Đang giao']))
                    <button onclick="reportIssue('{{ $order['id'] }}')" class="md:w-auto bg-yellow-600 text-white py-3 px-4 rounded-lg hover:bg-yellow-700 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        Báo cáo sự cố / Hủy đơn
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Initialize map
const map = window.driverApp.initializeMap('map', {
    center: [{{ $order['pickup_coordinates']['lng'] }}, {{ $order['pickup_coordinates']['lat'] }}],
    zoom: 13
});

const pickupCoords = [{{ $order['pickup_coordinates']['lng'] }}, {{ $order['pickup_coordinates']['lat'] }}];
const deliveryCoords = [{{ $order['delivery_coordinates']['lng'] }}, {{ $order['delivery_coordinates']['lat'] }}];

map.on('load', function() {
    // Add markers
    new mapboxgl.Marker({ color: 'blue' })
        .setLngLat(pickupCoords)
        .setPopup(new mapboxgl.Popup().setText('Điểm lấy hàng'))
        .addTo(map);
        
    new mapboxgl.Marker({ color: 'red' })
        .setLngLat(deliveryCoords)
        .setPopup(new mapboxgl.Popup().setText('Điểm giao hàng'))
        .addTo(map);

    // Get and display route
    getRoute(pickupCoords, deliveryCoords);
});

async function getRoute(start, end) {
    const url = `https://api.mapbox.com/directions/v5/mapbox/driving-traffic/${start.join(',')};${end.join(',')}?steps=true&geometries=geojson&access_token=${mapboxgl.accessToken}&overview=full`;
    
    try {
        const response = await fetch(url);
        const data = await response.json();
        const route = data.routes[0].geometry.coordinates;
        
        const geojson = {
            type: 'Feature',
            properties: {},
            geometry: {
                type: 'LineString',
                coordinates: route
            }
        };

        if (map.getSource('route')) {
            map.getSource('route').setData(geojson);
        } else {
            map.addLayer({
                id: 'route',
                type: 'line',
                source: {
                    type: 'geojson',
                    data: geojson
                },
                layout: {
                    'line-join': 'round',
                    'line-cap': 'round'
                },
                paint: {
                    'line-color': '#3b82f6',
                    'line-width': 6,
                    'line-opacity': 0.8
                }
            });
        }

        // Fit map to route
        const bounds = route.reduce((bounds, coord) => {
            return bounds.extend(coord);
        }, new mapboxgl.LngLatBounds(route[0], route[0]));
        
        map.fitBounds(bounds, { padding: 50 });
    } catch (error) {
        console.error('Error fetching route:', error);
        window.driverApp.showToast('Lỗi!', 'Không thể tải lộ trình.', 'error');
    }
}

function reportIssue(orderId) {
    const reason = prompt('Vui lòng mô tả sự cố:');
    if (reason && reason.trim()) {
        // In real app, make AJAX call to report issue
        window.driverApp.showToast('Thành công!', 'Báo cáo sự cố đã được gửi.', 'success');
    }
}
</script>
@endpush
@endsection
