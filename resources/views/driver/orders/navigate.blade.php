@extends('layouts.driver.masterLayout')

@section('title', 'Điều hướng')
@section('page-title', 'Điều hướng đến khách hàng')

@section('content')
<div class="pt-4 relative">
    <!-- Full screen map -->
    <div id="navigationMap" style="height: calc(100vh - 4rem); width: 100%;"></div>
    
    <!-- Navigation info overlay -->
    <div class="absolute top-20 left-4 right-4 bg-white rounded-lg shadow-lg p-4 z-10">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center space-x-2">
                <i class="fas fa-route text-blue-600"></i>
                <span class="font-medium">Đang điều hướng</span>
            </div>
            <button onclick="toggleVoiceNavigation()" class="text-blue-600">
                <i class="fas fa-volume-up" id="voiceIcon"></i>
            </button>
        </div>
        
        <div class="grid grid-cols-3 gap-4 text-center text-sm">
            <div>
                <div class="font-bold text-blue-600" id="distance">Đang tính...</div>
                <div class="text-gray-500">Khoảng cách</div>
            </div>
            <div>
                <div class="font-bold text-green-600" id="duration">Đang tính...</div>
                <div class="text-gray-500">Thời gian</div>
            </div>
            <div>
                <div class="font-bold text-orange-600" id="arrival">--:--</div>
                <div class="text-gray-500">Dự kiến đến</div>
            </div>
        </div>
    </div>
    
    <!-- Navigation instructions -->
    <div class="absolute bottom-32 left-4 right-4 bg-white rounded-lg shadow-lg p-4 z-10">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                <i class="fas fa-arrow-right text-white" id="directionIcon"></i>
            </div>
            <div>
                <div class="font-medium" id="instruction">Đang tính toán đường đi...</div>
                <div class="text-sm text-gray-500" id="nextInstruction">Vui lòng đợi</div>
            </div>
        </div>
    </div>
    
    <!-- Action buttons -->
    <div class="absolute bottom-4 left-4 right-4 z-10 space-y-2">
        <div class="flex space-x-2">
            <button onclick="callCustomer()" class="flex-1 bg-green-600 text-white py-3 rounded-lg font-medium">
                <i class="fas fa-phone mr-2"></i>Gọi khách
            </button>
            <button onclick="showCustomerInfo()" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-medium">
                <i class="fas fa-info mr-2"></i>Thông tin
            </button>
        </div>
        
        <button onclick="confirmArrival()" class="w-full bg-orange-600 text-white py-3 rounded-lg font-medium">
            <i class="fas fa-map-marker-alt mr-2"></i>Đã đến nơi
        </button>
    </div>
</div>

@push('scripts')
<script>
let navigationMap;
let route;
let driverMarker;
let customerMarker;
let voiceEnabled = true;

// Customer coordinates based on order ID
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

// Mock driver location (Hà Nội center)
const mockDriverCoords = {
    lat: 21.0227, lng: 105.8194
};

function initNavigationMap() {
    try {
        // Check if Mapbox is loaded
        if (typeof mapboxgl === 'undefined') {
            console.error('Mapbox GL JS not loaded');
            showError('Không thể tải bản đồ. Vui lòng kiểm tra kết nối internet.');
            return;
        }

        // Initialize map
        navigationMap = new mapboxgl.Map({
            container: 'navigationMap',
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [customerCoords.lng, customerCoords.lat],
            zoom: 14,
            pitch: 0,
            bearing: 0
        });

        navigationMap.on('load', function() {
            console.log('Map loaded successfully');
            
            // Add customer marker (red)
            customerMarker = new mapboxgl.Marker({ color: 'red' })
                .setLngLat([customerCoords.lng, customerCoords.lat])
                .setPopup(new mapboxgl.Popup().setHTML('<p><strong>Địa chỉ giao hàng</strong><br>' + getCustomerAddress() + '</p>'))
                .addTo(navigationMap);

            // Try to get real location, fallback to mock
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const driverCoords = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        setupDriverLocation(driverCoords);
                    },
                    function(error) {
                        console.warn('GPS Error, using mock location:', error);
                        setupDriverLocation(mockDriverCoords);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000
                    }
                );
            } else {
                console.warn('Geolocation not supported, using mock location');
                setupDriverLocation(mockDriverCoords);
            }
        });

        navigationMap.on('error', function(e) {
            console.error('Map error:', e);
            showError('Lỗi tải bản đồ: ' + e.error.message);
        });

    } catch (error) {
        console.error('Error initializing map:', error);
        showError('Không thể khởi tạo bản đồ: ' + error.message);
    }
}

function setupDriverLocation(driverCoords) {
    // Add driver marker (blue)
    driverMarker = new mapboxgl.Marker({ color: 'blue' })
        .setLngLat([driverCoords.lng, driverCoords.lat])
        .setPopup(new mapboxgl.Popup().setHTML('<p><strong>Vị trí của bạn</strong></p>'))
        .addTo(navigationMap);

    // Fit map to show both markers
    const bounds = new mapboxgl.LngLatBounds()
        .extend([customerCoords.lng, customerCoords.lat])
        .extend([driverCoords.lng, driverCoords.lat]);
    
    navigationMap.fitBounds(bounds, { 
        padding: { top: 150, bottom: 200, left: 50, right: 50 }
    });

    // Get route
    getRoute(driverCoords, customerCoords);
    
    // Start location tracking if available
    startLocationTracking();
}

function getRoute(start, end) {
    // Simple route calculation (straight line for demo)
    const distance = calculateDistance(start.lat, start.lng, end.lat, end.lng);
    const duration = distance * 2; // Assume 2 minutes per km
    
    // Create simple route line
    const routeCoordinates = [
        [start.lng, start.lat],
        [end.lng, end.lat]
    ];

    // Add route to map
    if (navigationMap.getSource('route')) {
        navigationMap.removeLayer('route');
        navigationMap.removeSource('route');
    }

    navigationMap.addSource('route', {
        type: 'geojson',
        data: {
            type: 'Feature',
            properties: {},
            geometry: {
                type: 'LineString',
                coordinates: routeCoordinates
            }
        }
    });

    navigationMap.addLayer({
        id: 'route',
        type: 'line',
        source: 'route',
        layout: {
            'line-join': 'round',
            'line-cap': 'round'
        },
        paint: {
            'line-color': '#3b82f6',
            'line-width': 6
        }
    });

    // Update navigation info
    updateNavigationInfo(distance, duration);
}

function calculateDistance(lat1, lng1, lat2, lng2) {
    const R = 6371; // Earth's radius in km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLng/2) * Math.sin(dLng/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

function updateNavigationInfo(distance, durationMinutes) {
    document.getElementById('distance').textContent = distance.toFixed(1) + ' km';
    document.getElementById('duration').textContent = Math.round(durationMinutes) + ' phút';
    
    const arrival = new Date(Date.now() + durationMinutes * 60000);
    document.getElementById('arrival').textContent = arrival.toLocaleTimeString('vi-VN', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
    
    // Update instruction
    document.getElementById('instruction').textContent = 'Đi thẳng đến ' + getCustomerAddress();
    document.getElementById('nextInstruction').textContent = 'Khoảng cách: ' + distance.toFixed(1) + 'km';
}

function startLocationTracking() {
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(
            function(position) {
                const newPos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                // Update driver marker position
                if (driverMarker) {
                    driverMarker.setLngLat([newPos.lng, newPos.lat]);
                }
                
                // Recalculate route
                getRoute(newPos, customerCoords);
            },
            function(error) {
                console.warn('GPS tracking error:', error);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 30000
            }
        );
    }
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-20';
    errorDiv.innerHTML = `
        <div class="text-center">
            <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
            <p class="font-medium">${message}</p>
            <button onclick="location.reload()" class="mt-2 bg-red-600 text-white px-4 py-2 rounded">
                Thử lại
            </button>
        </div>
    `;
    document.getElementById('navigationMap').appendChild(errorDiv);
}

function toggleVoiceNavigation() {
    voiceEnabled = !voiceEnabled;
    const icon = document.getElementById('voiceIcon');
    icon.className = voiceEnabled ? 'fas fa-volume-up' : 'fas fa-volume-mute';
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

function showCustomerInfo() {
    alert(`Khách hàng: ${getCustomerName()}\nĐịa chỉ: ${getCustomerAddress()}\nSĐT: ${getCustomerPhone()}`);
}

function getCustomerName() {
    const names = {
        1: 'Nguyễn Văn A',
        2: 'Trần Thị B',
        3: 'Lê Văn C',
        4: 'Phạm Thị D'
    };
    return names[{{ $orderId }}];
}

function getCustomerAddress() {
    const addresses = {
        1: '123 Đường Láng, Đống Đa, Hà Nội',
        2: '456 Phố Huế, Hai Bà Trưng, Hà Nội',
        3: '789 Đường Giải Phóng, Hoàng Mai, Hà Nội',
        4: '321 Đường Cầu Giấy, Cầu Giấy, Hà Nội'
    };
    return addresses[{{ $orderId }}];
}

function getCustomerPhone() {
    const phones = {
        1: '0987654321',
        2: '0912345678',
        3: '0901234567',
        4: '0934567890'
    };
    return phones[{{ $orderId }}];
}

function confirmArrival() {
    if (confirm('Xác nhận đã đến địa chỉ giao hàng?')) {
        // Update order status and redirect back to order detail
        fetch(`/api/orders/{{ $orderId }}/arrive`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(() => {
            window.location.href = `/orders/{{ $orderId }}`;
        }).catch(error => {
            console.error('Error:', error);
            window.location.href = `/orders/{{ $orderId }}`;
        });
    }
}

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Add small delay to ensure DOM is fully ready
    setTimeout(initNavigationMap, 100);
});
</script>
@endsection
