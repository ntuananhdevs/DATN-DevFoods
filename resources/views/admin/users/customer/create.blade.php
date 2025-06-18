@extends('layouts.admin.contentLayoutMaster')

@section('content')
<div class="min-h-screen bg-gray-50 p-3">
    <div class="w-full">
        <!-- Main Header -->
        <div class="flex items-center gap-4 mb-8">
            <div class="p-3 bg-blue-100 rounded-lg">
                <i class="fas fa-user-plus text-blue-600 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Thêm khách hàng mới</h1>
        </div>

        <!-- Form bao gồm tất cả nội dung -->
        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" id="userForm">
            @csrf
            
            <!-- Main Content -->
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <!-- Left Column -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-6 text-gray-700">Thông tin người dùng</h2>
                    
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

                        <!-- Hidden avatar input -->
                        <input type="file" id="avatar-input" name="avatar" class="hidden">
                    </div>
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

            <!-- Addresses Section - Now inside the form -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Thông tin địa chỉ (tùy chọn)</h3>
                    <button type="button" onclick="addAddress()" 
                            class="px-3 py-1 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition">
                        <i class="fas fa-plus mr-1"></i> Thêm địa chỉ
                    </button>
                </div>
                
                <div id="addresses-container" class="space-y-4">
                    <!-- Địa chỉ đầu tiên sẽ được thêm bằng JavaScript -->
                </div>
            </div>

            <!-- Submit buttons -->
            <div class="flex justify-end gap-3">
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
</div>

<!-- Mapbox CSS -->
<link href='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css' rel='stylesheet' />

<!-- Mapbox JS -->
<script src='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js'></script>

<script>
// Kiểm tra và thiết lập Mapbox Access Token
const mapboxToken = '{{ config("services.mapbox.access_token") }}';

if (!mapboxToken || mapboxToken === '' || mapboxToken === 'null') {
    // Token không được cấu hình
} else {
    mapboxgl.accessToken = mapboxToken;
}

let addressCount = 1;
let maps = {};
let markers = {};

// Thêm địa chỉ mới
function addAddress() {
    const container = document.getElementById('addresses-container');
    const currentIndex = addressCount;
    const addressHtml = createAddressForm(currentIndex);
    container.insertAdjacentHTML('beforeend', addressHtml);
    
    addressCount++;
    
    // Khởi tạo bản đồ với delay để đảm bảo DOM đã render
    setTimeout(() => {
        initMapForAddress(currentIndex);
    }, 500);
}

// Tạo form địa chỉ
function createAddressForm(index) {
    return `
        <div class="address-form border border-gray-200 rounded-lg p-4 mb-4" id="address-${index}">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-sm font-medium text-gray-700">Địa chỉ ${index}</h4>
                <button type="button" onclick="removeAddress(${index})" 
                        class="text-red-500 hover:text-red-700 transition">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Left side - Address inputs -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ chi tiết</label>
                        <input type="text" name="addresses[${index}][address_line]" id="address_line_${index}"
                               class="w-full px-3 py-2 border rounded-lg"
                               placeholder="Số nhà, tên đường..." onchange="updateMapForAddress(${index})">
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phường/Xã</label>
                            <input type="text" name="addresses[${index}][ward]" id="ward_${index}"
                                   class="w-full px-3 py-2 border rounded-lg"
                                   placeholder="Phường/Xã" onchange="updateMapForAddress(${index})">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quận/Huyện</label>
                            <input type="text" name="addresses[${index}][district]" id="district_${index}"
                                   class="w-full px-3 py-2 border rounded-lg"
                                   placeholder="Quận/Huyện" onchange="updateMapForAddress(${index})">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Thành phố/Tỉnh</label>
                            <input type="text" name="addresses[${index}][city]" id="city_${index}"
                                   class="w-full px-3 py-2 border rounded-lg"
                                   placeholder="Thành phố/Tỉnh" onchange="updateMapForAddress(${index})">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại địa chỉ</label>
                        <input type="tel" name="addresses[${index}][phone]"
                               class="w-full px-3 py-2 border rounded-lg"
                               placeholder="Số điện thoại cho địa chỉ này">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vĩ độ (Latitude)</label>
                            <input type="number" name="addresses[${index}][latitude]" id="latitude_${index}" step="any"
                                   class="w-full px-3 py-2 border rounded-lg"
                                   placeholder="Vĩ độ" readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kinh độ (Longitude)</label>
                            <input type="number" name="addresses[${index}][longitude]" id="longitude_${index}" step="any"
                                   class="w-full px-3 py-2 border rounded-lg"
                                   placeholder="Kinh độ" readonly>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="addresses[${index}][is_default]" value="1"
                               class="mr-2" id="is_default_${index}" onchange="handleDefaultAddress(${index})">
                        <label for="is_default_${index}" class="text-sm text-gray-700">Đặt làm địa chỉ mặc định</label>
                    </div>
                </div>

                <!-- Right side - Map -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vị trí trên bản đồ</label>
                    <div id="map_${index}" class="w-full h-64 rounded-lg border bg-gray-100" style="min-height: 250px;"></div>
                    <p class="text-sm text-gray-500 mt-1">Nhấp vào bản đồ để chọn vị trí chính xác</p>
                </div>
            </div>
        </div>
    `;
}

// Xóa địa chỉ
function removeAddress(index) {
    const addressElement = document.getElementById(`address-${index}`);
    if (addressElement) {
        // Hủy bản đồ
        if (maps[index]) {
            maps[index].remove();
            delete maps[index];
            delete markers[index];
        }
        addressElement.remove();
    }
}

// Khởi tạo bản đồ cho địa chỉ
function initMapForAddress(index) {
    const mapContainer = document.getElementById(`map_${index}`);
    if (!mapContainer) {
        return;
    }

    // Kiểm tra token
    if (!mapboxToken || mapboxToken === '' || mapboxToken === 'null') {
        mapContainer.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full bg-yellow-50 text-yellow-700 p-4 rounded">
                <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                <p class="text-sm font-medium">Mapbox chưa được cấu hình</p>
                <p class="text-xs mt-1">Vui lòng thêm MAPBOX_API_KEY vào file .env</p>
            </div>
        `;
        return;
    }

    // Kiểm tra Mapbox GL JS đã load chưa
    if (typeof mapboxgl === 'undefined') {
        mapContainer.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full bg-red-50 text-red-700 p-4 rounded">
                <i class="fas fa-times-circle text-2xl mb-2"></i>
                <p class="text-sm font-medium">Lỗi tải Mapbox</p>
                <p class="text-xs mt-1">Kiểm tra kết nối internet</p>
            </div>
        `;
        return;
    }

    try {
        const map = new mapboxgl.Map({
            container: `map_${index}`,
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [105.8342, 21.0278], // Tọa độ Hà Nội
            zoom: 10,
            attributionControl: false
        });

        const marker = new mapboxgl.Marker({
            draggable: true,
            color: '#3B82F6'
        })
        .setLngLat([105.8342, 21.0278])
        .addTo(map);

        // Lưu trữ map và marker
        maps[index] = map;
        markers[index] = marker;

        // Xử lý khi map load thành công
        map.on('load', function() {
            // Cập nhật tọa độ ban đầu
            const lngLat = marker.getLngLat();
            const latInput = document.getElementById(`latitude_${index}`);
            const lngInput = document.getElementById(`longitude_${index}`);
            
            if (latInput && lngInput) {
                latInput.value = lngLat.lat.toFixed(6);
                lngInput.value = lngLat.lng.toFixed(6);
            }
        });

        // Xử lý lỗi map
        map.on('error', function(e) {
            mapContainer.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full bg-red-50 text-red-700 p-4 rounded">
                    <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                    <p class="text-sm font-medium">Lỗi tải bản đồ</p>
                    <p class="text-xs mt-1">Kiểm tra token Mapbox</p>
                </div>
            `;
        });

        // Xử lý khi kéo thả marker
        marker.on('dragend', function() {
            const lngLat = marker.getLngLat();
            const latInput = document.getElementById(`latitude_${index}`);
            const lngInput = document.getElementById(`longitude_${index}`);
            
            if (latInput && lngInput) {
                latInput.value = lngLat.lat.toFixed(6);
                lngInput.value = lngLat.lng.toFixed(6);
            }
        });

        // Xử lý khi click vào bản đồ
        map.on('click', function(e) {
            const lngLat = e.lngLat;
            marker.setLngLat([lngLat.lng, lngLat.lat]);
            
            const latInput = document.getElementById(`latitude_${index}`);
            const lngInput = document.getElementById(`longitude_${index}`);
            
            if (latInput && lngInput) {
                latInput.value = lngLat.lat.toFixed(6);
                lngInput.value = lngLat.lng.toFixed(6);
            }
        });

        // Thêm navigation controls
        map.addControl(new mapboxgl.NavigationControl(), 'top-right');

    } catch (error) {
        mapContainer.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full bg-red-50 text-red-700 p-4 rounded">
                <i class="fas fa-bug text-2xl mb-2"></i>
                <p class="text-sm font-medium">Lỗi khởi tạo bản đồ</p>
                <p class="text-xs mt-1">${error.message}</p>
            </div>
        `;
    }
}

// Cập nhật bản đồ dựa trên địa chỉ
function updateMapForAddress(index) {
    const addressLine = document.getElementById(`address_line_${index}`)?.value || '';
    const ward = document.getElementById(`ward_${index}`)?.value || '';
    const district = document.getElementById(`district_${index}`)?.value || '';
    const city = document.getElementById(`city_${index}`)?.value || '';
    
    const fullAddress = [addressLine, ward, district, city]
        .filter(part => part && part.trim() !== '')
        .join(', ');
    
    if (fullAddress.trim() === '' || !maps[index] || !mapboxToken) {
        return;
    }
    
    const geocodingUrl = `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(fullAddress)}.json?access_token=${mapboxToken}&country=VN&limit=1`;
    
    fetch(geocodingUrl)
        .then(response => response.json())
        .then(data => {
            if (data.features && data.features.length > 0) {
                const coordinates = data.features[0].center;
                const [lng, lat] = coordinates;
                
                // Cập nhật vị trí marker và map
                markers[index].setLngLat([lng, lat]);
                maps[index].flyTo({
                    center: [lng, lat],
                    zoom: 15
                });
                
                // Cập nhật input tọa độ
                const latInput = document.getElementById(`latitude_${index}`);
                const lngInput = document.getElementById(`longitude_${index}`);
                
                if (latInput && lngInput) {
                    latInput.value = lat.toFixed(6);
                    lngInput.value = lng.toFixed(6);
                }
            }
        })
        .catch(error => {
            // Xử lý lỗi geocoding
        });
}

// Xử lý địa chỉ mặc định
function handleDefaultAddress(selectedIndex) {
    const checkboxes = document.querySelectorAll('input[name*="[is_default]"]');
    checkboxes.forEach((checkbox) => {
        if (!checkbox.name.includes(`[${selectedIndex}]`)) {
            checkbox.checked = false;
        }
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

// Khởi tạo khi trang được tải
document.addEventListener('DOMContentLoaded', function() {
    // Kiểm tra các thư viện cần thiết
    if (typeof mapboxgl === 'undefined') {
        alert('Lỗi: Không thể tải Mapbox. Vui lòng kiểm tra kết nối internet.');
        return;
    }
    
    // Thêm địa chỉ đầu tiên
    setTimeout(() => {
        addAddress();
    }, 200);
});
</script>
@endsection

