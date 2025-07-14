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
                        <div id="map" style="height: 200px; border-radius: 8px;"></div>
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
    fetch('https://provinces.open-api.vn/api/p/')
        .then(res => res.json())
        .then(data => {
            citySelect.innerHTML = '<option value="">Chọn tỉnh/thành</option>' + data.map(c => `<option value="${c.name}">${c.name}</option>`).join('');
            if (selected) citySelect.value = selected;
        });
}
// Lấy quận/huyện
function fetchDistricts(cityName, selected = null) {
    fetch('https://provinces.open-api.vn/api/p/').then(res => res.json()).then(provinces => {
        const province = provinces.find(p => p.name === cityName);
        if (!province) return districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
        fetch(`https://provinces.open-api.vn/api/p/${province.code}?depth=2`)
            .then(res => res.json())
            .then(data => {
                districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>' + data.districts.map(d => `<option value="${d.name}">${d.name}</option>`).join('');
                if (selected) districtSelect.value = selected;
                // Gọi fetchWards nếu có selected (khi edit)
                if (selected) fetchWards(cityName, selected);
            });
    });
}
// Lấy phường/xã
function fetchWards(cityName, districtName, selected = null) {
    fetch('https://provinces.open-api.vn/api/p/').then(res => res.json()).then(provinces => {
        const province = provinces.find(p => p.name === cityName);
        if (!province) return wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
        fetch(`https://provinces.open-api.vn/api/p/${province.code}?depth=2`)
            .then(res => res.json())
            .then(data => {
                const district = data.districts.find(d => d.name === districtName);
                if (!district) return wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
                fetch(`https://provinces.open-api.vn/api/d/${district.code}?depth=2`)
                    .then(res => res.json())
                    .then(districtData => {
                        wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>' + districtData.wards.map(w => `<option value="${w.name}">${w.name}</option>`).join('');
                        if (selected) wardSelect.value = selected;
                    });
            });
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
    fetchDistricts(this.value);
    wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
});
districtSelect.addEventListener('change', function() {
    fetchWards(citySelect.value, this.value);
    wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
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

// Đổi style map sang tông cam và marker cam
let map, marker;
function initMap(lat = 21.028511, lng = 105.804817) {
    mapboxgl.accessToken = MAPBOX_TOKEN;
    if (!map) {
        map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v12', // giống Google Maps
            center: [lng, lat],
            zoom: 14
        });
        map.on('click', function(e) {
            setMarker(e.lngLat.lat, e.lngLat.lng);
        });
    } else {
        map.setCenter([lng, lat]);
        map.setZoom(14);
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
    setTimeout(() => initMap(), 300); // Đợi modal render xong mới khởi tạo map
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
                fetchCities(addr.city);
                setTimeout(() => {
                    fetchDistricts(addr.city, addr.district);
                    setTimeout(() => {
                        fetchWards(addr.city, addr.district, addr.ward);
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
        if (confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')) {
            fetch(`/profile/addresses/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(res => res.json())
            .then(() => fetchAddresses());
        }
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
    .then(() => {
        modal.classList.add('hidden');
        fetchAddresses();
    });
};

// Khởi tạo
fetchAddresses();
fetchCities();
</script>
