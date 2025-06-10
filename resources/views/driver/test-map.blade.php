@extends('layouts.driver.masterLayout')

@section('title', 'Test Map')
@section('page-title', 'Test Map')

@section('content')
<div class="pt-16 p-4">
    <div class="bg-white rounded-lg p-4 shadow-sm mb-4">
        <h2 class="text-lg font-semibold mb-2">Test Mapbox Integration</h2>
        <p class="text-sm text-gray-600">Kiểm tra xem Mapbox có hoạt động không</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div id="testMap" style="height: 400px; width: 100%;"></div>
    </div>
    
    <div class="mt-4 bg-white rounded-lg p-4 shadow-sm">
        <h3 class="font-semibold mb-2">Debug Info:</h3>
        <div id="debugInfo" class="text-sm text-gray-600"></div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const debugInfo = document.getElementById('debugInfo');
    
    // Check if Mapbox is loaded
    if (typeof mapboxgl === 'undefined') {
        debugInfo.innerHTML = '<span class="text-red-600">❌ Mapbox GL JS không được tải</span>';
        return;
    }
    
    debugInfo.innerHTML += '<span class="text-green-600">✅ Mapbox GL JS đã được tải</span><br>';
    
    // Check access token
    if (!mapboxgl.accessToken) {
        debugInfo.innerHTML += '<span class="text-red-600">❌ Mapbox access token chưa được cấu hình</span><br>';
        return;
    }
    
    debugInfo.innerHTML += '<span class="text-green-600">✅ Mapbox access token đã được cấu hình</span><br>';
    
    try {
        // Initialize test map
        const map = new mapboxgl.Map({
            container: 'testMap',
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [105.8194, 21.0227], // Hanoi
            zoom: 13
        });
        
        map.on('load', function() {
            debugInfo.innerHTML += '<span class="text-green-600">✅ Map đã được tải thành công</span><br>';
            
            // Add a marker
            new mapboxgl.Marker()
                .setLngLat([105.8194, 21.0227])
                .setPopup(new mapboxgl.Popup().setHTML('<p>Hà Nội, Việt Nam</p>'))
                .addTo(map);
                
            debugInfo.innerHTML += '<span class="text-green-600">✅ Marker đã được thêm</span><br>';
        });
        
        map.on('error', function(e) {
            debugInfo.innerHTML += '<span class="text-red-600">❌ Map error: ' + e.error.message + '</span><br>';
        });
        
    } catch (error) {
        debugInfo.innerHTML += '<span class="text-red-600">❌ Lỗi khởi tạo map: ' + error.message + '</span><br>';
    }
});
</script>
@endsection
