@extends('layouts.admin.contentLayoutMaster')

@section('content')
<div class="min-h-screen bg-gray-50 p-3">
    <div class="w-full">
        <!-- Main Header -->
        <div class="flex items-center gap-4 mb-8">
            <div class="p-3 bg-blue-100 rounded-lg">
                <i class="fas fa-user-plus text-blue-600 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Thêm người dùng mới</h1>
        </div>

        <!-- Main Content -->
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-semibold mb-6 text-gray-700">Thông tin người dùng</h2>
                <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" id="userForm">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập</label>
                            <input type="text" name="user_name" value="{{ old('user_name') }}"
                                   class="w-full px-3 py-2 border rounded-lg @error('user_name') border-red-500 @enderror"
                                   placeholder="Nhập tên đăng nhập">
                            @error('user_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}"
                                   class="w-full px-3 py-2 border rounded-lg @error('full_name') border-red-500 @enderror"
                                   placeholder="Nhập họ và tên">
                            @error('full_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="w-full px-3 py-2 border rounded-lg @error('email') border-red-500 @enderror"
                                   placeholder="Nhập email">
                            @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                   class="w-full px-3 py-2 border rounded-lg @error('phone') border-red-500 @enderror"
                                   placeholder="Nhập số điện thoại">
                            @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu</label>
                            <input type="password" name="password"
                                   class="w-full px-3 py-2 border rounded-lg @error('password') border-red-500 @enderror"
                                   placeholder="Nhập mật khẩu">
                            @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu</label>
                            <input type="password" name="password_confirmation"
                                   class="w-full px-3 py-2 border rounded-lg"
                                   placeholder="Nhập lại mật khẩu">
                        </div>

                        <!-- Hidden role input with default customer role -->
                        <input type="hidden" name="role_id" value="{{ $roles->where('name', 'customer')->first()->id }}">
                        @error('role_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror

                        <!-- Address fields -->
                        <div class="border-t pt-4 mt-6">
                            <h3 class="text-md font-medium text-gray-700 mb-4">Thông tin địa chỉ (tùy chọn)</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ chi tiết</label>
                                <input type="text" name="address_line" id="address_line" value="{{ old('address_line') }}"
                                       class="w-full px-3 py-2 border rounded-lg @error('address_line') border-red-500 @enderror"
                                       placeholder="Số nhà, tên đường..." onchange="updateMap()">
                                @error('address_line')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-3 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phường/Xã</label>
                                    <input type="text" name="ward" id="ward" value="{{ old('ward') }}"
                                           class="w-full px-3 py-2 border rounded-lg @error('ward') border-red-500 @enderror"
                                           placeholder="Phường/Xã" onchange="updateMap()">
                                    @error('ward')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quận/Huyện</label>
                                    <input type="text" name="district" id="district" value="{{ old('district') }}"
                                           class="w-full px-3 py-2 border rounded-lg @error('district') border-red-500 @enderror"
                                           placeholder="Quận/Huyện" onchange="updateMap()">
                                    @error('district')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Thành phố/Tỉnh</label>
                                    <input type="text" name="city" id="city" value="{{ old('city') }}"
                                           class="w-full px-3 py-2 border rounded-lg @error('city') border-red-500 @enderror"
                                           placeholder="Thành phố/Tỉnh" onchange="updateMap()">
                                    @error('city')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 mt-4">Số điện thoại địa chỉ</label>
                                <input type="tel" name="address_phone" value="{{ old('address_phone') }}"
                                       class="w-full px-3 py-2 border rounded-lg @error('address_phone') border-red-500 @enderror"
                                       placeholder="Số điện thoại cho địa chỉ này">
                                @error('address_phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Coordinates -->
                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Vĩ độ (Latitude)</label>
                                    <input type="number" name="latitude" id="latitude" value="{{ old('latitude') }}" step="any"
                                           class="w-full px-3 py-2 border rounded-lg @error('latitude') border-red-500 @enderror"
                                           placeholder="Vĩ độ" readonly>
                                    @error('latitude')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kinh độ (Longitude)</label>
                                    <input type="number" name="longitude" id="longitude" value="{{ old('longitude') }}" step="any"
                                           class="w-full px-3 py-2 border rounded-lg @error('longitude') border-red-500 @enderror"
                                           placeholder="Kinh độ" readonly>
                                    @error('longitude')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Map Container -->
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Vị trí trên bản đồ</label>
                                <div id="map" class="w-full h-64 rounded-lg border"></div>
                                <p class="text-sm text-gray-500 mt-1">Nhấp vào bản đồ để chọn vị trí chính xác</p>
                            </div>

                            <div class="flex items-center mt-4">
                                <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : 'checked' }}
                                       class="mr-2" id="is_default">
                                <label for="is_default" class="text-sm text-gray-700">Đặt làm địa chỉ mặc định</label>
                            </div>
                        </div>

                        <!-- Hidden avatar input -->
                        <input type="file" id="avatar-input" name="avatar" class="hidden">
                    </div>

                    <div class="flex justify-end gap-3 mt-8">
                        <button type="button" onclick="window.history.back()"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Hủy bỏ
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                            Tạo người dùng
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Column -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="cursor-pointer group" onclick="document.getElementById('avatar').click()">
                    <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-xl p-6
                              hover:border-blue-500 hover:bg-blue-50 transition-colors">
                        <div id="avatar-preview" class="text-center">
                            <div class="mx-auto bg-blue-100 w-16 h-16 rounded-xl flex items-center justify-center mb-4">
                                <i class="ri-image-line text-blue-500 text-2xl"></i>
                            </div>
                            <p class="text-blue-600 font-medium mb-1">Tải lên ảnh đại diện</p>
                            <p class="text-gray-500 text-sm">Định dạng: JPEG, PNG (Khuyến nghị: 600x600)</p>
                        </div>
                    </div>
                    @error('avatar')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <input type="file" id="avatar" class="hidden" accept="image/*" onchange="previewAndTransferAvatar(this)">
            </div>
        </div>
    </div>
</div>

<!-- Mapbox CSS -->
<link href='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css' rel='stylesheet' />

<!-- Mapbox JS -->
<script src='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js'></script>

<script>
// Mapbox Access Token - Bạn cần thay thế bằng token của mình
mapboxgl.accessToken ='{{ config('services.mapbox.access_token') }}';

let map;
let marker;

// Khởi tạo bản đồ
function initMap() {
    map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [105.8342, 21.0278], // Tọa độ Hà Nội mặc định
        zoom: 10
    });

    // Thêm marker
    marker = new mapboxgl.Marker({
        draggable: true
    })
    .setLngLat([105.8342, 21.0278])
    .addTo(map);

    // Xử lý khi kéo thả marker
    marker.on('dragend', function() {
        const lngLat = marker.getLngLat();
        document.getElementById('latitude').value = lngLat.lat.toFixed(6);
        document.getElementById('longitude').value = lngLat.lng.toFixed(6);
    });

    // Xử lý khi click vào bản đồ
    map.on('click', function(e) {
        const lngLat = e.lngLat;
        marker.setLngLat([lngLat.lng, lngLat.lat]);
        document.getElementById('latitude').value = lngLat.lat.toFixed(6);
        document.getElementById('longitude').value = lngLat.lng.toFixed(6);
    });
}

// Cập nhật bản đồ dựa trên địa chỉ
function updateMap() {
    const addressLine = document.getElementById('address_line').value;
    const ward = document.getElementById('ward').value;
    const district = document.getElementById('district').value;
    const city = document.getElementById('city').value;
    
    // Tạo địa chỉ đầy đủ
    const fullAddress = [addressLine, ward, district, city]
        .filter(part => part && part.trim() !== '')
        .join(', ');
    
    if (fullAddress.trim() === '') return;
    
    // Sử dụng Mapbox Geocoding API để tìm tọa độ
    const geocodingUrl = `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(fullAddress)}.json?access_token=${mapboxgl.accessToken}&country=VN&limit=1`;
    
    fetch(geocodingUrl)
        .then(response => response.json())
        .then(data => {
            if (data.features && data.features.length > 0) {
                const coordinates = data.features[0].center;
                const lng = coordinates[0];
                const lat = coordinates[1];
                
                // Cập nhật bản đồ và marker
                map.setCenter([lng, lat]);
                map.setZoom(15);
                marker.setLngLat([lng, lat]);
                
                // Cập nhật input tọa độ
                document.getElementById('latitude').value = lat.toFixed(6);
                document.getElementById('longitude').value = lng.toFixed(6);
            }
        })
        .catch(error => {
            console.error('Lỗi geocoding:', error);
        });
}

// Avatar preview function
function previewAndTransferAvatar(input) {
    if (input.files && input.files[0]) {
        const hiddenInput = document.getElementById('avatar-input');
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(input.files[0]);
        hiddenInput.files = dataTransfer.files;

        const reader = new FileReader();
        const previewContainer = document.getElementById('avatar-preview');

        reader.onload = function(e) {
            previewContainer.innerHTML = `
                <img src="${e.target.result}" alt="Preview" class="w-32 h-32 rounded-xl object-cover mx-auto mb-4">
                <p class="text-blue-600 font-medium mb-1">Ảnh đã chọn</p>
                <p class="text-gray-500 text-sm">Nhấp để chọn ảnh khác</p>
            `;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Khởi tạo bản đồ khi trang được tải
document.addEventListener('DOMContentLoaded', function() {
    initMap();
    
    // Nếu có dữ liệu cũ, cập nhật bản đồ
    const oldLat = '{{ old("latitude") }}';
    const oldLng = '{{ old("longitude") }}';
    
    if (oldLat && oldLng) {
        map.setCenter([parseFloat(oldLng), parseFloat(oldLat)]);
        map.setZoom(15);
        marker.setLngLat([parseFloat(oldLng), parseFloat(oldLat)]);
    }
});
</script>
@endsection

