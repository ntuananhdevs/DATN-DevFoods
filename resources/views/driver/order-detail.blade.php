@extends('layouts.driver.masterLayout')

@section('title', 'Chi tiết Đơn hàng - DevFoods Driver')

@push('styles')
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.css" rel="stylesheet">
    <style>
        #map {
            height: 400px;
            border-radius: 0.5rem;
        }
        .mapboxgl-ctrl-logo, .mapboxgl-ctrl-attrib {
            display: none !important;
        }
    </style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-100 via-gray-50 to-slate-100 p-4 md:p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('driver.orders.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Quay lại danh sách đơn hàng
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Chi tiết Đơn hàng <span class="text-blue-600">#{{ $order->id }}</span></h1>
            <p class="text-gray-600 mt-1">Xem thông tin chi tiết và lộ trình giao hàng.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Order Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Information -->
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-3">Thông tin đơn hàng</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Mã đơn hàng:</span>
                            <span class="font-medium text-gray-800">#{{ $order->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Trạng thái:</span>
                            @php
                                $statusConfig = [
                                    'pending' => ['text' => 'Chờ nhận', 'color' => 'blue'],
                                    'delivering' => ['text' => 'Đang giao', 'color' => 'yellow'],
                                    'completed' => ['text' => 'Hoàn thành', 'color' => 'green'],
                                    'cancelled' => ['text' => 'Đã hủy', 'color' => 'red'],
                                    'default' => ['text' => 'Không xác định', 'color' => 'gray']
                                ];
                                $currentStatus = $statusConfig[$order->status_key] ?? $statusConfig['default'];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-{{ $currentStatus['color'] }}-100 text-{{ $currentStatus['color'] }}-700">{{ $currentStatus['text'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Thời gian đặt:</span>
                            <span class="font-medium text-gray-800">{{ $order->order_time }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tổng tiền:</span>
                            <span class="font-bold text-xl text-green-600">{{ number_format($order->total_amount) }}đ</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phí ship:</span>
                            <span class="font-medium text-gray-800">{{ number_format($order->shipping_fee) }}đ</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phương thức thanh toán:</span>
                            <span class="font-medium text-gray-800">{{ $order->payment_method }}</span>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-3">Thông tin khách hàng</h2>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path></svg>
                            <span class="font-medium text-gray-800">{{ $order->customer_name }}</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg>
                            <a href="tel:{{ $order->customer_phone }}" class="text-blue-600 hover:underline">{{ $order->customer_phone }}</a>
                        </div>
                    </div>
                </div>

                <!-- Delivery Information -->
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-3">Thông tin giao hàng</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-medium text-gray-700 mb-1">Điểm lấy hàng (Cửa hàng):</h3>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 mr-3 mt-1 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <p class="text-gray-800">{{ $order->pickup_branch_name }}<br>{{ $order->pickup_branch_address }}</p>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-700 mb-1">Điểm giao hàng (Khách hàng):</h3>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 mr-3 mt-1 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <p class="text-gray-800">{{ $order->delivery_address }}</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t mt-3">
                            <span class="text-gray-600">Khoảng cách dự kiến:</span>
                            <span class="font-medium text-gray-800">{{ $order->distance }} km</span>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-3">Chi tiết sản phẩm</h2>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100 last:border-b-0">
                            <div class="flex items-center">
                                <img src="{{ $item->image_url ?? asset('images/default-product.png') }}" alt="{{ $item->name }}" class="w-16 h-16 rounded-md object-cover mr-4">
                                <div>
                                    <h4 class="font-medium text-gray-800">{{ $item->name }}</h4>
                                    <p class="text-sm text-gray-500">Số lượng: {{ $item->quantity }}</p>
                                </div>
                            </div>
                            <span class="font-semibold text-gray-700">{{ number_format($item->price * $item->quantity) }}đ</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Column: Map and Actions -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Bản đồ lộ trình</h2>
                    <div id="map"></div>
                    <p class="text-xs text-gray-500 mt-2 text-center">Bản đồ chỉ mang tính chất tham khảo.</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Hành động</h2>
                    <div class="space-y-3">
                        @if($order->status_key === 'pending')
                            <button class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white py-3 px-4 rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-300 flex items-center justify-center font-semibold shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Chấp nhận đơn hàng
                            </button>
                        @elseif($order->status_key === 'delivering')
                            <button class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3 px-4 rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 flex items-center justify-center font-semibold shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Đánh dấu đã giao hàng
                            </button>
                            <button class="w-full bg-gradient-to-r from-red-500 to-pink-600 text-white py-3 px-4 rounded-lg hover:from-red-600 hover:to-pink-700 transition-all duration-300 flex items-center justify-center font-semibold shadow-md hover:shadow-lg mt-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Báo cáo sự cố
                            </button>
                        @endif
                        <a href="tel:{{ $order->customer_phone }}" class="w-full bg-gray-200 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-300 transition-colors flex items-center justify-center font-semibold border border-gray-300">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg>
                            Gọi cho khách hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.js"></script>
<script>
    mapboxgl.accessToken = '{{ env("MAPBOX_ACCESS_TOKEN") }}'; // Key lấy từ biến môi trường
    const pickupLocation = [{{ $order->pickup_branch_longitude ?? '106.660172' }}, {{ $order->pickup_branch_latitude ?? '10.762622' }}]; // Default to HCM City center if not available
    const deliveryLocation = [{{ $order->delivery_longitude ?? '106.660172' }}, {{ $order->delivery_latitude ?? '10.762622' }}]; // Default to HCM City center if not available
    let driverMarker = null;

    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11', // Kiểu bản đồ
        center: pickupLocation, // Trung tâm bản đồ ban đầu
        zoom: 12 // Mức zoom ban đầu
    });

    function showDriverPosition(position) {
        const driverCoords = [position.coords.longitude, position.coords.latitude];
        if (driverMarker) {
            driverMarker.setLngLat(driverCoords);
        } else {
            driverMarker = new mapboxgl.Marker({ color: '#007bff' })
                .setLngLat(driverCoords)
                .setPopup(new mapboxgl.Popup().setText('Vị trí của bạn'))
                .addTo(map);
        }
        // Only recenter and get route if driver position is significantly different or first time
        // This simple check might need refinement for real-world usage
        if (!map.getSource('route')) { 
            map.setCenter(driverCoords);
            getRoute(driverCoords, deliveryLocation); // Get route from driver to customer
        } else {
             // Optionally, update route if driver moves significantly
        }
    }

    function showError(error) {
        console.warn(`ERROR(${error.code}): ${error.message}`);
        // Fallback if geolocation fails or is denied: Show route from pickup to delivery
        addMarkersAndRoute(pickupLocation, deliveryLocation, '{{ $order->pickup_branch_name }}', '{{ $order->customer_name }}');
    }
    
    function addMarkersAndRoute(startCoords, endCoords, startLabel, endLabel) {
        // Marker for start location
        new mapboxgl.Marker({ color: '#28a745' })
            .setLngLat(startCoords)
            .setPopup(new mapboxgl.Popup().setText(startLabel))
            .addTo(map);

        // Marker for end location
        new mapboxgl.Marker({ color: '#dc3545' })
            .setLngLat(endCoords)
            .setPopup(new mapboxgl.Popup().setText(endLabel))
            .addTo(map);
        getRoute(startCoords, endCoords);
    }

    // Get route using Mapbox Directions API
    async function getRoute(start, end) {
        const url = `https://api.mapbox.com/directions/v5/mapbox/driving/${start[0]},${start[1]};${end[0]},${end[1]}?steps=true&geometries=geojson&access_token=${mapboxgl.accessToken}`;
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const data = await response.json();
            
            if (data.routes && data.routes.length) {
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
                            'line-color': '#3887be',
                            'line-width': 5,
                            'line-opacity': 0.75
                        }
                    });
                }
                // Fit map to route bounds
                const bounds = route.reduce(function (bounds, coord) {
                    return bounds.extend(coord);
                }, new mapboxgl.LngLatBounds(route[0], route[0]));
                map.fitBounds(bounds, { padding: 50 });
            } else {
                console.error("Không tìm thấy lộ trình.");
            }
        } catch (e) {
            console.error("Lỗi khi lấy lộ trình: ", e);
        }
    }

    map.on('load', () => {
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(showDriverPosition, showError, {
                enableHighAccuracy: true,
                timeout: 10000, // 10 seconds
                maximumAge: 0 // Force fresh location
            });
        } else {
            // Geolocation is not supported by this browser.
            // Show default route from pickup to delivery
            addMarkersAndRoute(pickupLocation, deliveryLocation, 'Điểm lấy hàng: {{ $order->pickup_branch_name }}', 'Điểm giao hàng: {{ $order->customer_name }}');
        }
    });

</script>
@endpush
