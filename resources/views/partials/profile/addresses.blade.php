<section id="addresses" class="mb-10">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Thông Tin Cá Nhân</h2>
        <button id="addNewAddressBtn"
            class="text-orange-500 hover:underline text-sm font-medium"
            style="background: none; border: none; box-shadow: none;">
            Thêm địa chỉ mới
        </button>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div id="address-list"></div>
    </div>

    <!-- Modal cập nhật/thêm địa chỉ -->
    <div id="updateAddressModal" class="fixed inset-0 z-60 flex items-center justify-center bg-black bg-opacity-40 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl">
            <div class="max-h-[80vh] overflow-y-auto scrollbar-none" style="scrollbar-width: none; -ms-overflow-style: none;">
                <div class="px-6 py-4 border-b">
                    <span class="text-lg font-semibold" id="modalTitle">Cập nhật địa chỉ</span>
                </div>
                <form class="px-6 py-4" id="addressForm">
                    <div class="flex gap-3 mb-3">
                        <div class="flex-1">
                            <label class="block text-xs text-gray-500 mb-1">Họ và tên người nhận</label>
                            <input type="text" id="recipientName" class="w-full border rounded px-3 py-2">
                            <div class="text-red-500 text-xs mt-1" id="error_recipientName"></div>
                        </div>

                        <div class="flex-1">
                            <label class="block text-xs text-gray-500 mb-1">Số điện thoại</label>
                            <input type="text" id="phoneNumber" class="w-full border rounded px-3 py-2">
                            <div class="text-red-500 text-xs mt-1" id="error_phoneNumber"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="block text-xs text-gray-500 mb-1">Tỉnh/Thành phố</label>
                        <select id="city" class="w-full border rounded px-3 py-2"></select>
                        <div class="text-red-500 text-xs mt-1" id="error_city"></div>
                    </div>
                    <div class="mb-3">
                        <label class="block text-xs text-gray-500 mb-1">Quận/Huyện</label>
                        <select id="district" class="w-full border rounded px-3 py-2"></select>
                        <div class="text-red-500 text-xs mt-1" id="error_district"></div>
                    </div>
                    <div class="mb-3">
                        <label class="block text-xs text-gray-500 mb-1">Phường/Xã</label>
                        <select id="ward" class="w-full border rounded px-3 py-2"></select>
                        <div class="text-red-500 text-xs mt-1" id="error_ward"></div>
                    </div>
                    <div class="mb-3">
                        <label class="block text-xs text-gray-500 mb-1">Địa chỉ cụ thể</label>
                        <textarea id="addressLine" class="w-full border rounded px-3 py-2" rows="2"></textarea>
                        <div class="text-red-500 text-xs mt-1" id="error_addressLine"></div>
                    </div>
                    <div class="mb-3">
                        <label class="block text-xs text-gray-500 mb-1">Chọn vị trí trên bản đồ</label>
                        <div id="map" style="height: 200px; width: 100%; border-radius: 8px;"></div>
                        <input type="hidden" id="latitude" name="latitude">
                        <input type="hidden" id="longitude" name="longitude">
                    </div>
                    <div class="mb-3 flex items-center">
                        <input type="checkbox" id="setDefaultAddress" class="mr-2">
                        <label for="setDefaultAddress" class="text-xs text-gray-400 select-none">Đặt làm địa chỉ mặc định</label>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" id="updateAddressBack" class="px-5 py-2 rounded border border-gray-300 text-gray-700 bg-white hover:bg-gray-100">Trở Lại</button>
                        <button type="submit" class="px-5 py-2 rounded bg-orange-500 text-white font-semibold hover:bg-orange-600">Hoàn thành</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script src='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js'></script>
<link href='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css' rel='stylesheet' />

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

<!-- Include modal.js for confirmation dialogs -->
<script src="{{ asset('js/modal.js') }}"></script>

<script>
var MAPBOX_TOKEN = "{{ config('services.mapbox.access_token') }}";
const addressList = document.getElementById('address-list');
const modal = document.getElementById('updateAddressModal');
const addressForm = document.getElementById('addressForm');
const addNewAddressBtn = document.getElementById('addNewAddressBtn');
const backBtn = document.getElementById('updateAddressBack');
const modalTitle = document.getElementById('modalTitle');
let editingAddressId = null;

// Các trường trong form
const recipientNameInput = document.getElementById('recipientName');
const phoneNumberInput = document.getElementById('phoneNumber');
const citySelect = document.getElementById('city');
const districtSelect = document.getElementById('district');
const wardSelect = document.getElementById('ward');
const addressLineInput = document.getElementById('addressLine');
const setDefaultCheckbox = document.getElementById('setDefaultAddress');

// Lấy danh sách địa chỉ
function fetchAddresses() {
    fetch('/profile/addresses')
        .then(res => res.json())
        .then(addresses => {
            addressList.innerHTML = addresses.map(addr => `
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex items-center border border-orange-200 relative">
                    <div class="flex-1">
                        <div class="font-semibold text-base mb-1 flex items-center">
                            <span class="text-orange-500 mr-2 text-xl align-middle">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <span class="font-bold">${addr.recipient_name || ''}</span>
                            <span class="ml-2">${addr.phone_number}</span>
                            ${addr.is_default ? `<span class="ml-2 align-middle">
                                <span class="border border-orange-500 text-orange-500 px-2 py-0.5 rounded text-xs font-medium bg-white">Mặc Định</span>
                            </span>` : ''}
                        </div>
                        <div class="text-gray-800 text-sm">
                            ${addr.address_line}, ${addr.ward}, ${addr.district}, ${addr.city}
                        </div>
                    </div>
                    <div class="flex items-center gap-3 ml-4">
                        <a href="#" class="text-blue-600 hover:underline font-medium text-sm edit-address" data-id="${addr.id}">Thay Đổi</a>
                        <button class="text-red-500 hover:underline font-medium text-sm delete-address" data-id="${addr.id}" style="background:none;border:none;padding:0;">Xóa</button>
                    </div>
                </div>
            `).join('');
        });
}

// Lấy tỉnh/thành phố
function fetchCities(selected = null) {
    // Chỉ cho phép chọn Hà Nội
    citySelect.innerHTML = '<option value="Hà Nội">Hà Nội</option>';
    citySelect.value = 'Hà Nội';
}
// Lấy quận/huyện
function fetchDistricts(cityName = null, selected = null) {
    fetch('/data/hanoi-districts.json')
        .then(res => res.json())
        .then(data => {
            if (!data.districts || !Array.isArray(data.districts)) {
                districtSelect.innerHTML = '<option value="">Không có quận/huyện</option>';
                return;
            }
            districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>' + data.districts.map(d => `<option value="${d.name}">${d.name}</option>`).join('');
            if (selected) districtSelect.value = selected;
        })
        .catch(err => {
            districtSelect.innerHTML = '<option value="">Lỗi tải quận/huyện</option>';
            console.error('Lỗi fetchDistricts:', err);
        });
}
// Lấy phường/xã
function fetchWards(cityName, districtName, selected = null) {
    fetch('/data/hanoi-districts.json')
        .then(res => res.json())
        .then(data => {
            const district = data.districts && Array.isArray(data.districts)
                ? data.districts.find(d => d.name === districtName)
                : null;
            if (!district) {
                wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
                return;
            }
            
            if (!district.wards || !Array.isArray(district.wards)) {
                wardSelect.innerHTML = '<option value="">Không có phường/xã</option>';
                return;
            }
            
            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>' + district.wards.map(w => `<option value="${w.name}">${w.name}</option>`).join('');
            if (selected) wardSelect.value = selected;
        })
        .catch(err => {
            wardSelect.innerHTML = '<option value="">Lỗi tải phường/xã</option>';
            console.error('Lỗi fetchWards:', err);
        });
}

// Khi chọn xã/phường, tự động geocode địa chỉ và nhảy marker
wardSelect.addEventListener('change', function() {
    const addressText = `${addressLineInput.value}, ${wardSelect.value}, ${districtSelect.value}, ${citySelect.value}`;
    if (wardSelect.value && districtSelect.value && citySelect.value) {
        geocodeAddress(addressText, function(lat, lng) {
            if (lat && lng) {
                setMapCenterAndMarker(lat, lng);
            }
        });
    }
});

// Khi chọn quận/huyện, reset xã
citySelect.addEventListener('change', function() {
    fetchDistricts(this.value, null);
    wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
});
districtSelect.addEventListener('change', function() {
    fetchWards(citySelect.value, this.value);
});

// Geocode địa chỉ bằng Mapbox
function geocodeAddress(address, callback) {
    fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(address)}.json?access_token=${MAPBOX_TOKEN}`)
        .then(res => res.json())
        .then(data => {
            if (data.features && data.features.length > 0) {
                const [lng, lat] = data.features[0].center;
                callback(lat, lng);
            } else {
                callback(null, null);
            }
        });
}

// Thêm hàm tạo icon cửa hàng
function createStoreIcon() {
    const el = document.createElement('div');
    el.className = 'store-marker';
    el.style.width = '35px';
    el.style.height = '35px';
    el.style.display = 'flex';
    el.style.alignItems = 'center';
    el.style.justifyContent = 'center';
    el.style.boxShadow = '0 2px 8px rgba(0,0,0,0.08)';
    el.innerHTML = '<ion-icon name="storefront-sharp" style="font-size:28px;color:#ff8800;"></ion-icon>';
    return el;
}

// Thêm hàm vẽ marker và vòng tròn cho các branch
function drawBranchMarkersAndCircles(map) {
    fetch('/customer/profile/branches-map') // endpoint mới
        .then(res => res.json())
        .then(branches => {
            branches.forEach(branch => {
                if (!branch.latitude || !branch.longitude) return;
                // Marker
                new mapboxgl.Marker({ element: createStoreIcon() })
                    .setLngLat([branch.longitude, branch.latitude])
                    .setPopup(new mapboxgl.Popup().setText(branch.name))
                    .addTo(map);
                // Circle
                map.addSource('circle-' + branch.id, {
                    type: 'geojson',
                    data: {
                        type: 'FeatureCollection',
                        features: [{
                            type: 'Feature',
                            geometry: {
                                type: 'Point',
                                coordinates: [branch.longitude, branch.latitude]
                            }
                        }]
                    }
                });
                map.addLayer({
                    id: 'circle-fill-' + branch.id,
                    type: 'circle',
                    source: 'circle-' + branch.id,
                    paint: {
                        'circle-radius': {
                            stops: [
                                [0, 0],
                                [20, 10000 / 0.075]
                            ],
                            base: 2
                        },
                        'circle-color': '#ff8800',
                        'circle-opacity': 0.06, // nhạt hơn
                        'circle-stroke-width': 2,
                        'circle-stroke-color': '#ff8800'
                    }
                });
            });
        });
}

let map = null;
let marker = null;

function initMap(lat = 21.028511, lng = 105.804817) {
    mapboxgl.accessToken = MAPBOX_TOKEN;
    if (!map) {
        map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v12',
            center: [lng, lat],
            zoom: 14
        });
        map.on('load', function () {
            drawBranchMarkersAndCircles(map);
        });
        map.on('click', function(e) {
            setMarker(e.lngLat.lat, e.lngLat.lng);
        });
    } else {
        // Nếu map đã tồn tại, chỉ set lại center và zoom
        if (typeof map.setCenter === 'function') {
            map.setCenter([lng, lat]);
            map.setZoom(14);
        }
        if (marker) marker.setLngLat([lng, lat]);
    }
    setMarker(lat, lng);
}

function setMarker(lat, lng) {
    if (marker) {
        marker.setLngLat([lng, lat]);
    } else {
        marker = new mapboxgl.Marker({
            color: '#ff8800', // cam
            draggable: true
        })
        .setLngLat([lng, lat])
        .addTo(map);
        marker.on('dragend', function() {
            const lngLat = marker.getLngLat();
            document.getElementById('latitude').value = lngLat.lat;
            document.getElementById('longitude').value = lngLat.lng;
            console.log('Marker moved:', { lat: lngLat.lat, lng: lngLat.lng });
        });
    }
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
}

function setMapCenterAndMarker(lat, lng) {
    if (map) {
        map.setCenter([lng, lat]);
        setMarker(lat, lng);
    }
}

// Mở modal thêm/sửa
addNewAddressBtn.onclick = function() {
    editingAddressId = null;
    modalTitle.textContent = 'Thêm địa chỉ mới';
    addressForm.reset();
    setDefaultCheckbox.checked = false;
    fetchCities();
    districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
    wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
    fetchDistricts('Hà Nội', null); // Sửa lại để truyền đúng tham số
    setTimeout(() => initMap(), 300);
    modal.classList.remove('hidden');
};
addressList.addEventListener('click', function(e) {
    if (e.target.classList.contains('edit-address')) {
        e.preventDefault();
        editingAddressId = e.target.dataset.id;
        fetch('/profile/addresses')
            .then(res => res.json())
            .then(addresses => {
                const addr = addresses.find(a => a.id == editingAddressId);
                if (!addr) return;
                modalTitle.textContent = 'Cập nhật địa chỉ';
                recipientNameInput.value = addr.recipient_name || '';
                phoneNumberInput.value = addr.phone_number || '';
                addressLineInput.value = addr.address_line || '';
                setDefaultCheckbox.checked = !!addr.is_default;

                // Đảm bảo load đúng dữ liệu dropdown
                fetchCities('Hà Nội');
                setTimeout(() => {
                    fetchDistricts('Hà Nội', addr.district);
                    setTimeout(() => {
                        fetchWards('Hà Nội', addr.district, addr.ward);
                    }, 300);
                }, 300);

                setTimeout(() => {
                    const lat = addr.latitude;
                    const lng = addr.longitude;
                    initMap(lat, lng);
                }, 900);
                modal.classList.remove('hidden');
            });
    }
});

// Thêm sự kiện xóa địa chỉ
addressList.addEventListener('click', function(e) {
    if (e.target.classList.contains('delete-address')) {
        e.preventDefault();
        const id = e.target.dataset.id;
        
        // Lấy thông tin địa chỉ để hiển thị trong modal
        fetch('/profile/addresses')
            .then(res => res.json())
            .then(addresses => {
                const addr = addresses.find(a => a.id == id);
                const addressName = addr ? `${addr.recipient_name} - ${addr.address_line}, ${addr.ward}, ${addr.district}, ${addr.city}` : 'địa chỉ này';
                
                // Sử dụng modal xác nhận từ modal.js
                dtmodalConfirmDelete({
                    title: 'Xác nhận xóa địa chỉ',
                    subtitle: 'Bạn có chắc chắn muốn xóa địa chỉ này?',
                    message: 'Hành động này không thể hoàn tác.',
                    itemName: addressName,
                    onConfirm: function() {
                        // Thực hiện xóa địa chỉ
                        fetch(`/profile/addresses/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success || res.ok) {
                                // Hiển thị toast thành công
                                dtmodalShowToast('success', {
                                    title: 'Thành công!',
                                    message: 'Địa chỉ đã được xóa thành công.'
                                });
                                fetchAddresses(); // Refresh danh sách địa chỉ
                            } else {
                                // Hiển thị toast lỗi
                                dtmodalShowToast('error', {
                                    title: 'Lỗi!',
                                    message: data.message || 'Có lỗi xảy ra khi xóa địa chỉ.'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            dtmodalShowToast('error', {
                                title: 'Lỗi!',
                                message: 'Có lỗi xảy ra khi xóa địa chỉ.'
                            });
                        });
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching addresses:', error);
                // Fallback nếu không lấy được thông tin địa chỉ
                dtmodalConfirmDelete({
                    title: 'Xác nhận xóa địa chỉ',
                    subtitle: 'Bạn có chắc chắn muốn xóa địa chỉ này?',
                    message: 'Hành động này không thể hoàn tác.',
                    onConfirm: function() {
                        fetch(`/profile/addresses/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(res => res.json())
                        .then(() => {
                            dtmodalShowToast('success', {
                                title: 'Thành công!',
                                message: 'Địa chỉ đã được xóa thành công.'
                            });
                            fetchAddresses();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            dtmodalShowToast('error', {
                                title: 'Lỗi!',
                                message: 'Có lỗi xảy ra khi xóa địa chỉ.'
                            });
                        });
                    }
                });
            });
    }
});

// Đóng modal
backBtn.onclick = function() {
    modal.classList.add('hidden');
};
modal.addEventListener('click', function(e) {
    if (e.target === modal) modal.classList.add('hidden');
});

// Submit form
addressForm.onsubmit = function(e) {
    e.preventDefault();
    // Xóa lỗi cũ
    ["recipientName","phoneNumber","city","district","ward","addressLine"].forEach(function(f){
        var err = document.getElementById('error_' + f);
        if (err) err.textContent = '';
    });
    let hasError = false;
    if (!recipientNameInput.value.trim()) {
        document.getElementById('error_recipientName').textContent = 'Vui lòng nhập họ và tên người nhận';
        hasError = true;
    }
    if (!phoneNumberInput.value.trim()) {
        document.getElementById('error_phoneNumber').textContent = 'Vui lòng nhập số điện thoại';
        hasError = true;
    } else {
        // Regex: bắt đầu bằng + hoặc số, chỉ chứa số, độ dài 9-15
        const phone = phoneNumberInput.value.trim();
        const phoneRegex = /^(\+?\d{9,15})$/;
        if (!phoneRegex.test(phone)) {
            document.getElementById('error_phoneNumber').textContent = 'Số điện thoại không hợp lệ';
            hasError = true;
        }
    }
    if (!citySelect.value) {
        document.getElementById('error_city').textContent = 'Vui lòng chọn tỉnh/thành phố';
        hasError = true;
    }
    if (!districtSelect.value) {
        document.getElementById('error_district').textContent = 'Vui lòng chọn quận/huyện';
        hasError = true;
    }
    if (!wardSelect.value) {
        document.getElementById('error_ward').textContent = 'Vui lòng chọn phường/xã';
        hasError = true;
    }
    if (!addressLineInput.value.trim()) {
        document.getElementById('error_addressLine').textContent = 'Vui lòng nhập địa chỉ cụ thể';
        hasError = true;
    }
    if (hasError) return;
    const data = {
        recipient_name: recipientNameInput.value,
        phone_number: phoneNumberInput.value,
        address_line: addressLineInput.value,
        city: citySelect.value,
        district: districtSelect.value,
        ward: wardSelect.value,
        is_default: setDefaultCheckbox.checked ? 1 : 0,
        latitude: document.getElementById('latitude').value,
        longitude: document.getElementById('longitude').value
    };
    const method = editingAddressId ? 'PUT' : 'POST';
    const url = editingAddressId ? `/profile/addresses/${editingAddressId}` : '/profile/addresses';
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success || data.id) {
            modal.classList.add('hidden');
            fetchAddresses();
            
            // Hiển thị toast thành công
            const actionText = editingAddressId ? 'cập nhật' : 'thêm';
            dtmodalShowToast('success', {
                title: 'Thành công!',
                message: `Địa chỉ đã được ${actionText} thành công.`
            });
        } else {
            // Hiển thị toast lỗi
            dtmodalShowToast('error', {
                title: 'Lỗi!',
                message: data.message || 'Có lỗi xảy ra khi lưu địa chỉ.'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        dtmodalShowToast('error', {
            title: 'Lỗi!',
            message: 'Có lỗi xảy ra khi lưu địa chỉ.'
        });
    });
};

// Khởi tạo
fetchAddresses();
fetchCities();
</script>
