@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Chi Nhánh')

@section('content')

@php
    $branchBanner = app('App\Http\Controllers\Customer\BannerController')->getBannersByPosition('branch');
@endphp
@include('components.banner', ['banners' => $branchBanner])
<div class="max-w-[1240px] mx-auto w-full">

<div class="container mx-auto px-4 py-12">
    <!-- Tìm kiếm chi nhánh -->
    <div class="max-w-4xl mx-auto mb-12">
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-2xl font-bold mb-4">Tìm Chi Nhánh</h2>

            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label for="city" class="block text-sm font-medium mb-1">Tỉnh/Thành phố</label>
                    <select id="city" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Tất cả</option>
                        <option value="hcm">TP. Hồ Chí Minh</option>
                        <option value="hn">Hà Nội</option>
                        <option value="dn">Đà Nẵng</option>
                        <option value="ct">Cần Thơ</option>
                        <option value="other">Khác</option>
                    </select>
                </div>

                <div>
                    <label for="district" class="block text-sm font-medium mb-1">Quận/Huyện</label>
                    <select id="district" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Tất cả</option>
                    </select>
                </div>

                <div>
                    <label for="service" class="block text-sm font-medium mb-1">Dịch vụ</label>
                    <select id="service" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Tất cả</option>
                        <option value="dine-in">Ăn tại chỗ</option>
                        <option value="takeaway">Mang đi</option>
                        <option value="delivery">Giao hàng</option>
                        <option value="drive-thru">Drive-thru</option>
                        <option value="24h">Mở cửa 24h</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button id="search-branch" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-6 rounded-md transition-colors inline-flex items-center justify-center">
                    <i class="fas fa-search mr-2"></i>
                    Tìm kiếm
                </button>
            </div>
        </div>
    </div>

    <!-- Bản đồ và danh sách chi nhánh -->
    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Danh sách chi nhánh -->
        <div class="lg:col-span-1">
            <h2 class="text-2xl font-bold mb-4">Danh Sách Chi Nhánh</h2>

            <div class="space-y-4 h-[600px] overflow-y-auto pr-2" id="branch-list">
                @foreach($branches as $branch)
                    <div class="branch-item bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow cursor-pointer"
                         data-id="{{ $branch->id }}"
                         data-lat="{{ $branch->latitude }}"
                         data-lng="{{ $branch->longitude }}"
                         data-images='@json($branch->images)'>
                        <h3 class="font-bold text-lg mb-1">{{ $branch->name }}</h3>
                        <p class="text-gray-600 mb-2">
                            <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                            {{ $branch->address }}
                        </p>
                        <p class="text-gray-600 mb-2">
                            <i class="fas fa-phone-alt text-orange-500 mr-2"></i>
                            {{ $branch->phone }}
                        </p>
                        <p class="text-gray-600 mb-2">
                            <i class="fas fa-envelope text-orange-500 mr-3"></i>
                            {{ $branch->email }}
                        </p>
                        <p class="text-gray-600 mb-3">
                            <i class="fas fa-clock text-orange-500 mr-2"></i>
                            {{ date('H:i', strtotime($branch->opening_hour)) }} - {{ date('H:i', strtotime($branch->closing_hour)) }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Bản đồ -->
        <div class="lg:col-span-2">
            <h2 class="text-2xl font-bold mb-4">Bản Đồ</h2>

            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="relative h-[600px]">
                    <div id="map" class="w-full h-full"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thông tin chi nhánh chi tiết -->
    <div id="branch-detail" class="mt-12 bg-white rounded-lg shadow-sm p-6 hidden">
        <div class="flex justify-between items-start mb-6">
            <h2 class="text-2xl font-bold" id="detail-title">FastFood - Quận 1</h2>
            <button id="close-detail" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <div>
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3">Thông Tin Liên Hệ</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-orange-500 mt-1 mr-3"></i>
                            <span id="detail-address"></span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt text-orange-500 mr-3"></i>
                            <span id="detail-phone"></span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope text-orange-500 mr-3"></i>
                            <span id="detail-email"></span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock text-orange-500 mr-3"></i>
                            <span id="detail-hours"></span>
                        </li>
                    </ul>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3">Dịch Vụ</h3>
                    <div class="flex flex-wrap gap-2" id="detail-services">
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full">Ăn tại chỗ</span>
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full">Mang đi</span>
                        <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full">Giao hàng</span>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-bold mb-3">Tiện Ích</h3>
                    <ul class="grid grid-cols-2 gap-2">
                        <li class="flex items-center">
                            <i class="fas fa-wifi text-orange-500 mr-2"></i>
                            <span>Wi-Fi miễn phí</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-parking text-orange-500 mr-2"></i>
                            <span>Bãi đậu xe</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-baby text-orange-500 mr-2"></i>
                            <span>Ghế trẻ em</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-credit-card text-orange-500 mr-2"></i>
                            <span>Thanh toán thẻ</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-wheelchair text-orange-500 mr-2"></i>
                            <span>Lối đi cho người khuyết tật</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-air-conditioner text-orange-500 mr-2"></i>
                            <span>Máy lạnh</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div>
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3">Hình Ảnh</h3>
                    <div class="branch-images">
                        <!-- Images will be dynamically inserted here -->
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-bold mb-3">Đánh Giá</h3>
                    <div class="flex items-center mb-2">
                        <div class="flex text-orange-400 mr-2">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <span class="font-bold">4.5/5</span>
                        <span class="text-gray-500 ml-2">(120 đánh giá)</span>
                    </div>

                    <div class="space-y-4 mt-4">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="flex justify-between items-center mb-1">
                                <div class="font-medium">Nguyễn Văn A</div>
                                <div class="flex text-orange-400 text-sm">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <p class="text-gray-600 text-sm">
                                Thức ăn ngon, nhân viên phục vụ nhiệt tình, không gian thoáng mát và sạch sẽ.
                            </p>
                        </div>

                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="flex justify-between items-center mb-1">
                                <div class="font-medium">Trần Thị B</div>
                                <div class="flex text-orange-400 text-sm">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                            </div>
                            <p class="text-gray-600 text-sm">
                                Đồ ăn ngon, giá cả hợp lý. Tuy nhiên thời gian phục vụ hơi lâu vào giờ cao điểm.
                            </p>
                        </div>
                    </div>

                    <a href="#" class="text-orange-500 hover:text-orange-600 font-medium inline-flex items-center mt-3">
                        Xem tất cả đánh giá
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-8 flex gap-4">
            <a href="/menu" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-6 rounded-md transition-colors inline-flex items-center justify-center">
                <i class="fas fa-utensils mr-2"></i>
                Đặt hàng
            </a>
            <a href="https://maps.google.com" target="_blank" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-md transition-colors inline-flex items-center justify-center">
                <i class="fas fa-directions mr-2"></i>
                Chỉ đường
            </a>
        </div>
    </div>
</div>

<!-- Mapbox CSS -->
<link href='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css' rel='stylesheet' />
@endsection

@section('scripts')
<!-- Mapbox JS -->
<script src='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mapbox configuration
    mapboxgl.accessToken = '{{ env("MAPBOX_API_KEY") }}';
    
    // Initialize map
    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v12',
        center: [107.9902, 14.0583], // Approximate center of Vietnam
        zoom: 4 // Adjust zoom level to show more of Vietnam
    });

    // Store markers for later reference
    let markers = [];
    let currentActiveMarker = null;

    // Add navigation controls
    map.addControl(new mapboxgl.NavigationControl());

    // Function to clear all markers
    function clearMarkers() {
        markers.forEach(marker => marker.remove());
        markers = [];
        currentActiveMarker = null;
    }

    // Function to add markers for all branches
    function addAllBranchMarkers() {
        const branchItems = document.querySelectorAll('.branch-item');
        const bounds = new mapboxgl.LngLatBounds();

        branchItems.forEach(item => {
            const lat = parseFloat(item.getAttribute('data-lat'));
            const lng = parseFloat(item.getAttribute('data-lng'));
            const branchName = item.querySelector('h3').textContent;
            const branchAddress = item.querySelector('p:nth-child(2)').textContent.replace(/^\s*/, '').replace(/^[^a-zA-Z0-9À-ỹ]*/, '');

            if (!isNaN(lat) && !isNaN(lng)) {
                // Create custom marker element
                const markerElement = document.createElement('div');
                markerElement.className = 'branch-marker';
                markerElement.innerHTML = `
                    <div class="w-8 h-8 bg-orange-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center cursor-pointer hover:bg-orange-600 transition-colors">
                        <i class="fas fa-utensils text-white text-xs"></i>
                    </div>
                `;

                // Create popup
                const popup = new mapboxgl.Popup({
                    offset: 25,
                    closeButton: true,
                    closeOnClick: false
                }).setHTML(`
                    <div class="p-2">
                        <h3 class="font-bold text-sm mb-1">${branchName}</h3>
                        <p class="text-xs text-gray-600 mb-2">${branchAddress}</p>
                        <button class="bg-orange-500 hover:bg-orange-600 text-white text-xs px-2 py-1 rounded view-branch-detail" data-branch-id="${item.getAttribute('data-id')}">
                            Xem chi tiết
                        </button>
                    </div>
                `);

                // Create marker
                const marker = new mapboxgl.Marker(markerElement)
                    .setLngLat([lng, lat])
                    .setPopup(popup)
                    .addTo(map);

                markers.push(marker);
                bounds.extend([lng, lat]);

                // Add click event to marker
                markerElement.addEventListener('click', () => {
                    // Reset all markers to default state
                    markers.forEach(m => {
                        const el = m.getElement().querySelector('div');
                        el.classList.remove('bg-red-500', 'scale-110');
                        el.classList.add('bg-orange-500');
                    });

                    // Highlight current marker
                    const currentMarkerEl = markerElement.querySelector('div');
                    currentMarkerEl.classList.remove('bg-orange-500');
                    currentMarkerEl.classList.add('bg-red-500', 'scale-110');
                    
                    currentActiveMarker = marker;

                    // Show popup
                    marker.togglePopup();
                });
            }
        });

        // Fit map to show all markers
        if (!bounds.isEmpty()) {
            map.fitBounds(bounds, {
                padding: 50,
                maxZoom: 15
            });
        }

        // Add event listeners to popup buttons
        map.on('click', function(e) {
            // Handle view detail button clicks in popups
            setTimeout(() => {
                const detailButtons = document.querySelectorAll('.view-branch-detail');
                detailButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const branchId = this.getAttribute('data-branch-id');
                        const branchItem = document.querySelector(`[data-id="${branchId}"]`);
                        if (branchItem) {
                            branchItem.click();
                        }
                    });
                });
            }, 100);
        });
    }

    // Function to focus on specific branch
    function focusOnBranch(lat, lng, branchName) {
        if (!isNaN(lat) && !isNaN(lng)) {
            // Clear existing markers
            clearMarkers();

            // Create highlighted marker for selected branch
            const markerElement = document.createElement('div');
            markerElement.innerHTML = `
                <div class="w-10 h-10 bg-red-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center animate-pulse">
                    <i class="fas fa-utensils text-white"></i>
                </div>
            `;

            const popup = new mapboxgl.Popup({
                offset: 25,
                closeButton: false
            }).setHTML(`
                <div class="p-2">
                    <h3 class="font-bold text-sm">${branchName}</h3>
                    <p class="text-xs text-gray-600">Chi nhánh được chọn</p>
                </div>
            `);

            const marker = new mapboxgl.Marker(markerElement)
                .setLngLat([lng, lat])
                .setPopup(popup)
                .addTo(map);

            markers.push(marker);
            currentActiveMarker = marker;

            // Center map on branch
            map.flyTo({
                center: [lng, lat],
                zoom: 16,
                duration: 1500
            });

            // Show popup
            marker.togglePopup();
        }
    }

    // Initialize map with all branches when loaded
    map.on('load', function() {
        addAllBranchMarkers();
    });

    // Add a resize event listener to the window to ensure the map re-renders correctly
    window.addEventListener('resize', function() {
        map.resize();
    });

    // Dữ liệu mẫu cho quận/huyện
    const districtData = {
        'hcm': ['Quận 1', 'Quận 3', 'Quận 7', 'Quận Tân Bình', 'Quận Bình Thạnh'],
        'hn': ['Quận Ba Đình', 'Quận Hoàn Kiếm', 'Quận Hai Bà Trưng', 'Quận Cầu Giấy'],
        'dn': ['Quận Hải Châu', 'Quận Thanh Khê', 'Quận Sơn Trà', 'Quận Ngũ Hành Sơn'],
        'ct': ['Quận Ninh Kiều', 'Quận Bình Thủy', 'Quận Cái Răng', 'Quận Ô Môn']
    };

    // Cập nhật danh sách quận/huyện khi chọn tỉnh/thành phố
    const citySelect = document.getElementById('city');
    const districtSelect = document.getElementById('district');

    if (citySelect && districtSelect) {
        citySelect.addEventListener('change', function() {
            const cityValue = this.value;

            // Xóa các option cũ
            districtSelect.innerHTML = '<option value="">Tất cả</option>';

            if (cityValue && districtData[cityValue]) {
                const districts = districtData[cityValue];

                districts.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.toLowerCase().replace(/\s+/g, '-');
                    option.textContent = district;
                    districtSelect.appendChild(option);
                });
            }
        });
    }

    // Xử lý tìm kiếm chi nhánh
    const searchButton = document.getElementById('search-branch');

    if (searchButton) {
        searchButton.addEventListener('click', function() {
            // Show all branches again after search
            clearMarkers();
            addAllBranchMarkers();

            const submitButton = this;
            const originalText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang tìm kiếm...';

            setTimeout(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
                showToast('Đã tìm thấy các chi nhánh phù hợp');
            }, 1000);
        });
    }

    // Xử lý hiển thị chi tiết chi nhánh
    const branchItems = document.querySelectorAll('.branch-item');
    const branchDetail = document.getElementById('branch-detail');
    const closeDetail = document.getElementById('close-detail');

    if (branchItems.length > 0 && branchDetail && closeDetail) {
        branchItems.forEach(item => {
            item.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent default scroll behavior

                // Get branch coordinates
                const lat = parseFloat(this.getAttribute('data-lat'));
                const lng = parseFloat(this.getAttribute('data-lng'));
                const branchName = this.querySelector('h3').textContent;

                // Focus map on selected branch
                focusOnBranch(lat, lng, branchName);

                // Lấy ID chi nhánh
                const branchId = this.getAttribute('data-id');

                // Cập nhật thông tin chi tiết
                const branchAddress = this.querySelector('p:nth-child(2)').textContent.replace(/^\s*/, '').replace(/^[^a-zA-Z0-9À-ỹ]*/, '');
                const branchPhone = this.querySelector('p:nth-child(3)').textContent.replace(/^\s*/, '').replace(/^[^0-9+]*/, '');
                const branchEmail = this.querySelector('p:nth-child(4)').textContent.replace(/^\s*/, '').replace(/^[^a-zA-Z0-9@]*/, '');
                const branchHours = this.querySelector('p:nth-child(5)').textContent.replace(/^\s*/, '').replace(/^[^0-9]*/, '');

                // Cập nhật giao diện
                document.getElementById('detail-title').textContent = branchName;
                document.getElementById('detail-address').textContent = branchAddress;
                document.getElementById('detail-phone').textContent = branchPhone;
                document.getElementById('detail-email').textContent = branchEmail;
                document.getElementById('detail-hours').textContent = branchHours;

                // Hiển thị hình ảnh chi nhánh
                const detailImagesContainer = document.querySelector('.branch-images');
                detailImagesContainer.innerHTML = '';

                try {
                    const branchImages = JSON.parse(this.getAttribute('data-images'));
                    if (branchImages && branchImages.length > 0) {
                        const imageGrid = document.createElement('div');
                        imageGrid.className = 'grid grid-cols-3 gap-2';

                        branchImages.forEach(image => {
                            const imgContainer = document.createElement('div');
                            imgContainer.className = 'aspect-square bg-gray-200 rounded-lg overflow-hidden relative group cursor-pointer';

                            const img = document.createElement('img');
                            img.src = image.image_path.startsWith('http')
                                ? image.image_path
                                : `${window.location.origin}/storage/${image.image_path}`;
                            img.alt = branchName;
                            img.className = 'w-full h-full object-cover transition-transform duration-300 group-hover:scale-110';

                            img.addEventListener('load', () => {
                                imgContainer.classList.remove('bg-gray-200');
                            });

                            img.addEventListener('error', () => {
                                imgContainer.innerHTML = '<div class="absolute inset-0 flex items-center justify-center text-gray-500"><i class="fas fa-image text-3xl"></i></div>';
                            });

                            imgContainer.addEventListener('click', () => {
                                const modal = document.createElement('div');
                                modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75';
                                modal.innerHTML = `
                                    <div class="relative max-w-4xl max-h-[90vh] mx-4">
                                        <img src="${img.src}" alt="${img.alt}" class="max-w-full max-h-[90vh] object-contain">
                                        <button class="absolute top-4 right-4 text-white hover:text-gray-300">
                                            <i class="fas fa-times text-2xl"></i>
                                        </button>
                                    </div>
                                `;

                                modal.addEventListener('click', (e) => {
                                    if (e.target === modal || e.target.closest('button')) {
                                        modal.remove();
                                    }
                                });

                                document.body.appendChild(modal);
                            });

                            imgContainer.appendChild(img);
                            imageGrid.appendChild(imgContainer);
                        });

                        detailImagesContainer.appendChild(imageGrid);
                    } else {
                        detailImagesContainer.innerHTML = '<p class="text-gray-500">Chưa có hình ảnh</p>';
                    }
                } catch (error) {
                    console.error('Error parsing branch images:', error);
                    detailImagesContainer.innerHTML = '<p class="text-gray-500">Không thể tải hình ảnh</p>';
                }

                // Hiển thị chi tiết
                branchDetail.classList.remove('hidden');

                // Cuộn đến chi tiết
                // branchDetail.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        closeDetail.addEventListener('click', function() {
            branchDetail.classList.add('hidden');
            // Show all branches again when closing detail
            clearMarkers();
            addAllBranchMarkers();
        });
    }

    // Hàm hiển thị thông báo
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300 opacity-0';
        toast.textContent = message;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove('opacity-0');
            toast.classList.add('opacity-100');
        }, 10);

        setTimeout(() => {
            toast.classList.remove('opacity-100');
            toast.classList.add('opacity-0');

            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }
});
</script>

<style>
.branch-marker {
    cursor: pointer;
    transition: transform 0.2s ease;
}

.branch-marker:hover {
    transform: scale(1.1);
}

.mapboxgl-popup-content {
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.mapboxgl-popup-tip {
    border-top-color: white;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

.animate-pulse {
    animation: pulse 2s infinite;
}

/* Custom scrollbar for branch list */
#branch-list::-webkit-scrollbar {
    width: 6px;
}

#branch-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#branch-list::-webkit-scrollbar-thumb {
    background: #f97316;
    border-radius: 3px;
}

#branch-list::-webkit-scrollbar-thumb:hover {
    background: #ea580c;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .grid.lg\\:grid-cols-3 {
        grid-template-columns: 1fr;
    }
    
    .lg\\:col-span-1, .lg\\:col-span-2 {
        grid-column: span 1;
    }
    
    #branch-list {
        height: 400px;
        margin-bottom: 2rem;
    }
}
</style>
@endsection