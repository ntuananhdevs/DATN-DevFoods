@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Theo dõi tài xế')

@section('content')
<div x-data="driverTracking({{ json_encode($stats) }})" x-init="init()">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Theo dõi tài xế</h1>
                <p class="text-gray-600 mt-1">Quản lý và theo dõi vị trí tài xế trong thời gian thực</p>
            </div>
            <div class="flex items-center gap-3">
                <button @click="refreshDrivers()" :disabled="isLoading" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                    <svg :class="isLoading ? 'animate-spin' : ''" class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Làm mới
                </button>
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button @click="viewMode = 'map'" :class="viewMode === 'map' ? 'bg-white shadow-sm' : ''" class="px-3 py-1 text-sm font-medium rounded-md transition-colors">
                        <svg class="h-4 w-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        Bản đồ
                    </button>
                    <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-white shadow-sm' : ''" class="px-3 py-1 text-sm font-medium rounded-md transition-colors">
                        <svg class="h-4 w-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        Danh sách
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Tổng tài xế</dt>
                            <dd class="text-lg font-medium text-gray-900" x-text="stats.total"></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-6 w-6 bg-green-500 rounded-full"></div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Sẵn sàng nhận đơn</dt>
                            <dd class="text-lg font-medium text-green-600" x-text="stats.available"></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-6 w-6 bg-amber-500 rounded-full"></div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Đang giao</dt>
                            <dd class="text-lg font-medium text-amber-600" x-text="stats.delivering"></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-6 w-6 bg-gray-500 rounded-full"></div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Offline</dt>
                            <dd class="text-lg font-medium text-gray-600" x-text="stats.offline"></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <!-- Search -->
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input x-model="searchTerm" type="text" placeholder="Tìm kiếm tài xế theo tên..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <!-- Status Filter -->
                <div class="sm:w-48">
                    <select x-model="statusFilter" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="all">Tất cả trạng thái</option>
                        <option value="available">Sẵn sàng nhận đơn</option>
                        <option value="delivering">Đang giao</option>
                        <option value="offline">Offline</option>
                    </select>
                </div>
                


                <!-- Results count -->
                <div class="flex items-center text-sm text-gray-500 whitespace-nowrap">
                    Hiển thị <span x-text="filteredDrivers.length"></span> / <span x-text="drivers.length"></span> tài xế
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Driver List Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg h-[600px] flex flex-col">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Danh sách tài xế</h3>
                </div>
                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <template x-if="filteredDrivers.length === 0">
                        <div class="p-6 text-center text-gray-500">
                            <svg class="h-12 w-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            <p>Không tìm thấy tài xế nào</p>
                        </div>
                    </template>
                    
                    <div class="p-4 space-y-3">
                        <template x-for="driver in filteredDrivers" :key="driver.id">
                            <div @click="selectDriver(driver)" 
                                 :class="selectedDriver && selectedDriver.id === driver.id ? 'border-blue-500 bg-blue-50 shadow-md' : 'border-gray-200 bg-white hover:border-gray-300'"
                                 class="p-4 rounded-lg border cursor-pointer transition-all hover:shadow-md">
                                
                                <!-- Driver Header -->
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-semibold text-sm" x-text="driver.name.split(' ').map(n => n[0]).join('').toUpperCase()"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 truncate" x-text="driver.name"></h3>
                                        <div class="flex items-center gap-1 mt-1" x-show="driver.rating">
                                            <svg class="h-3 w-3 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            <span class="text-xs text-gray-600" x-text="driver.rating"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <div class="mb-3 flex gap-2">
                                    <span :class="getStatusBadgeClass(driver.status)" class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium">
                                        <div :class="getStatusDotClass(driver.status)" class="w-2 h-2 rounded-full"></div>
                                        <span x-text="getStatusLabel(driver.status)"></span>
                                    </span>
                                </div>

                                <!-- Driver Info -->
                                <div class="space-y-2 text-xs text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-3 w-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <span class="truncate" x-text="driver.phone"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="h-3 w-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        </svg>
                                        <span class="truncate" x-text="`${driver.lat.toFixed(4)}, ${driver.lng.toFixed(4)}`"></span>
                                    </div>
                                    <div x-show="driver.totalOrders" class="text-xs text-gray-500">
                                        Tổng đơn: <span x-text="driver.totalOrders"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map/List View -->
        <div class="lg:col-span-3">
            <div class="bg-white shadow rounded-lg h-[600px]">
                <div class="h-full" x-show="viewMode === 'map'">
                    <div id="map" class="w-full h-full"></div>
                </div>
                <div class="p-6" x-show="viewMode === 'list'">
                    @include('admin.driver.partials.driver-stats')
                </div>
            </div>
        </div>
    </div>

    <!-- Selected Driver Details -->
    <div x-show="selectedDriver" class="mt-6 bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Chi tiết tài xế: <span x-text="selectedDriver ? selectedDriver.name : ''"></span>
            </h3>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <!-- Thông tin cơ bản -->
            <div class="mb-6">
                <h4 class="text-base font-medium text-gray-900 mb-3">Thông tin cơ bản</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4" x-show="selectedDriver">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Số điện thoại</label>
                        <p class="font-medium mt-1" x-text="selectedDriver ? selectedDriver.phone : ''"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Vị trí hiện tại</label>
                        <p class="font-medium mt-1" x-text="selectedDriver ? `${selectedDriver.lat.toFixed(4)}, ${selectedDriver.lng.toFixed(4)}` : ''"></p>
                    </div>
                </div>
            </div>
            
            <!-- Thông tin giấy tờ -->
            <div class="mb-6">
                <h4 class="text-base font-medium text-gray-900 mb-3">Thông tin giấy tờ</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4" x-show="selectedDriver">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Số GPLX</label>
                        <p class="font-medium mt-1" x-text="selectedDriver && selectedDriver.documents ? selectedDriver.documents.license_number : 'Không có'"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Hạng GPLX</label>
                        <p class="font-medium mt-1" x-text="selectedDriver && selectedDriver.documents ? selectedDriver.documents.license_class : 'Không có'"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Ngày hết hạn GPLX</label>
                        <p class="font-medium mt-1" x-text="selectedDriver && selectedDriver.documents ? selectedDriver.documents.license_expiry : 'Không có'"></p>
                    </div>
                </div>
            </div>
            
            <!-- Thông tin phương tiện -->
            <div>
                <h4 class="text-base font-medium text-gray-900 mb-3">Thông tin phương tiện</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4" x-show="selectedDriver">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Loại phương tiện</label>
                        <p class="font-medium mt-1" x-text="selectedDriver && selectedDriver.documents ? selectedDriver.documents.vehicle_type : 'Không có'"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Màu xe</label>
                        <p class="font-medium mt-1" x-text="selectedDriver && selectedDriver.documents ? selectedDriver.documents.vehicle_color : 'Không có'"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Biển số xe</label>
                        <p class="font-medium mt-1" x-text="selectedDriver && selectedDriver.documents ? selectedDriver.documents.license_plate : 'Không có'"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Mapbox API -->
<link href="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js"></script>

<script>
let map;
let markers = {};

// Khởi tạo bản đồ Mapbox
function initMap() {
    console.log('Initializing map...');
    // Kiểm tra xem phần tử map có tồn tại không
    const mapElement = document.getElementById('map');
    if (!mapElement) {
        console.error('Map container not found!');
        return;
    }
    console.log('Map container found:', mapElement);
    
    // Tọa độ mặc định (Hà Nội)
    const defaultLocation = [105.8342, 21.0278];
    
    mapboxgl.accessToken = 'pk.eyJ1IjoibmhhdG5ndXllbnF2IiwiYSI6ImNtYjZydDNnZDAwY24ybm9qcTdxcTNocG8ifQ.u7X_0DfN7d52xZ8cGFbWyQ';
    
    try {
        map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v12',
            center: defaultLocation,
            zoom: 12
        });
        console.log('Map initialized successfully');
    } catch (error) {
        console.error('Error initializing map:', error);
    }
    
    // Thêm điều khiển zoom và xoay
    map.addControl(new mapboxgl.NavigationControl());
    
    // Đảm bảo map đã load xong
    map.on('load', function() {
        // Cập nhật markers nếu đã có dữ liệu
        if (window.Alpine) {
            const driverTrackingComponent = document.querySelector('[x-data="driverTracking"]').__x.$data;
            if (driverTrackingComponent && driverTrackingComponent.filteredDrivers.length > 0) {
                driverTrackingComponent.updateMapMarkers();
            }
        }
    });
}

function driverTracking(initialStats = null) {
    return {
        drivers: [],
        filteredDrivers: [],
        selectedDriver: null,
        searchTerm: '',
        statusFilter: 'all',
        viewMode: 'map',
        isLoading: false,
        stats: initialStats || {
            total: 0,
            available: 0,
            delivering: 0,
            offline: 0
        },

        init() {
            this.fetchDrivers();
            // Khởi tạo bản đồ
            initMap();
            // Auto refresh every 10 seconds
            setInterval(() => {
                this.fetchDrivers();
            }, 10000);

            // Watch for filter changes
            this.$watch('searchTerm', () => this.filterDrivers());
            this.$watch('statusFilter', () => this.filterDrivers());
            this.$watch('filteredDrivers', () => {
                if (this.viewMode === 'map' && map) {
                    this.updateMapMarkers();
                }
            });
            this.$watch('viewMode', (value) => {
                if (value === 'map' && map) {
                    // Đảm bảo bản đồ được render đúng khi chuyển tab
                    setTimeout(() => {
                        map.resize();
                        this.updateMapMarkers();
                    }, 100);
                }
            });
        },

        async fetchDrivers() {
            try {
                this.isLoading = true;
                console.log('Fetching drivers from API...');
                const response = await fetch('/api/drivers/locations');
                const data = await response.json();
                console.log('Received driver data:', data);
                this.drivers = data;
                console.log('Updated drivers array, length:', this.drivers.length);
                
                // Cập nhật thống kê
                this.updateStats();
                
                this.filterDrivers();
            } catch (error) {
                console.error('Error fetching drivers:', error);
            } finally {
                this.isLoading = false;
            }
        },
        
        updateStats() {
            // Tính toán số lượng tài xế theo trạng thái
            const total = this.drivers.length;
            const available = this.drivers.filter(driver => driver.status === 'available').length;
            const delivering = this.drivers.filter(driver => driver.status === 'delivering').length;
            const offline = this.drivers.filter(driver => driver.status === 'offline').length;
            
            // Cập nhật thống kê
            this.stats = {
                total,
                available,
                delivering,
                offline
            };
            
            console.log('Updated driver stats:', this.stats);
        },

        filterDrivers() {
            console.log('Filtering drivers, total drivers:', this.drivers.length);
            let filtered = this.drivers;

            if (this.searchTerm) {
                const searchLower = this.searchTerm.toLowerCase();
                filtered = filtered.filter(driver => 
                    driver.name.toLowerCase().includes(searchLower) ||
                    (driver.phone && driver.phone.includes(this.searchTerm))
                );
            }

            if (this.statusFilter !== 'all') {
                filtered = filtered.filter(driver => driver.status === this.statusFilter);
            }

            this.filteredDrivers = filtered;
            console.log('Filtered drivers count:', this.filteredDrivers.length);
            
            // Cập nhật markers trên bản đồ nếu đang ở chế độ xem bản đồ
            if (this.viewMode === 'map') {
                console.log('View mode is map, updating map markers');
                this.updateMapMarkers();
            }
        },

        updateMapMarkers() {
            if (!map) return;
            
            console.log('Updating map markers for', this.filteredDrivers.length, 'drivers');
            
            // Xóa tất cả marker hiện tại
            Object.values(markers).forEach(marker => marker.remove());
            markers = {};
            
            // Thêm marker mới cho mỗi tài xế
            this.filteredDrivers.forEach(driver => {
                console.log('Creating marker for driver:', driver.name, 'ID:', driver.id, 'Position:', [driver.lng, driver.lat], 'Status:', driver.status);
                
                // Tạo element cho marker
                const el = document.createElement('div');
                el.className = 'driver-marker';
                el.style.width = '30px';
                el.style.height = '30px';
                el.style.borderRadius = '50%';
                el.style.backgroundColor = this.getMarkerFillColor(driver.status);
                el.style.border = '2px solid white';
                el.style.boxShadow = '0 2px 4px rgba(0,0,0,0.3)';
                el.style.cursor = 'pointer';
                
                // Thêm hiệu ứng cho marker được chọn
                if (this.selectedDriver && this.selectedDriver.id === driver.id) {
                    console.log('Highlighting selected driver marker:', driver.name);
                    el.style.boxShadow = '0 0 0 4px rgba(59, 130, 246, 0.5), 0 2px 4px rgba(0,0,0,0.3)';
                    el.style.transform = 'scale(1.2)';
                }
                
                // Tạo popup cho marker
                const popup = new mapboxgl.Popup({ offset: 25 })
                    .setHTML(`
                        <div class="p-2">
                            <h3 class="font-bold text-gray-900">${driver.name}</h3>
                            <div class="flex gap-2 mt-1">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium ${this.getStatusBadgeClass(driver.status).replace('border ', '')}">
                                    <div class="w-2 h-2 rounded-full ${this.getStatusDotClass(driver.status)}"></div>
                                    ${this.getStatusLabel(driver.status)}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">${driver.phone}</p>
                            ${driver.documents ? `
                            <p class="text-sm text-gray-600 mt-1">Xe: ${driver.documents.vehicle_type || ''} ${driver.documents.vehicle_color || ''}</p>
                            <p class="text-sm text-gray-600">BKS: ${driver.documents.license_plate || ''}</p>
                            ` : ''}
                        </div>
                    `);
                
                // Tạo marker
                const marker = new mapboxgl.Marker(el)
                    .setLngLat([driver.lng, driver.lat])
                    .setPopup(popup)
                    .addTo(map);
                
                // Thêm sự kiện click cho marker
                el.addEventListener('click', () => {
                    this.selectDriver(driver);
                });
                
                // Lưu marker vào object markers với key là id của driver
                markers[driver.id] = marker;
                console.log('Marker added for driver:', driver.name);
            });
            
            // Nếu có tài xế được chọn, di chuyển bản đồ đến vị trí của tài xế đó
            if (this.selectedDriver) {
                console.log('Flying to selected driver:', this.selectedDriver.name);
                map.flyTo({
                    center: [this.selectedDriver.lng, this.selectedDriver.lat],
                    zoom: 15,
                    essential: true
                });
            } 
            // Nếu có nhiều tài xế, điều chỉnh bản đồ để hiển thị tất cả
            else if (this.filteredDrivers.length > 0) {
                console.log('Fitting map to show all drivers');
                this.fitMapToMarkers();
            }
        },
        
        fitMapToMarkers() {
            if (!map || this.filteredDrivers.length === 0) return;
            
            const bounds = new mapboxgl.LngLatBounds();
            this.filteredDrivers.forEach(driver => {
                bounds.extend([driver.lng, driver.lat]);
            });
            
            map.fitBounds(bounds, {
                padding: 50,
                maxZoom: 15
            });
        },

        selectDriver(driver) {
            this.selectedDriver = driver;
            
            if (map && driver) {
                // Di chuyển bản đồ đến vị trí của tài xế được chọn
                map.flyTo({
                    center: [driver.lng, driver.lat],
                    zoom: 15,
                    essential: true
                });
                
                // Cập nhật hiệu ứng cho tất cả marker
                this.updateMapMarkers();
                
                // Hiển thị popup cho marker được chọn
                if (markers[driver.id]) {
                    markers[driver.id].togglePopup();
                }
            }
        },

        refreshDrivers() {
            this.fetchDrivers();
        },

        getStatusLabel(status) {
            const labels = {
                'available': 'Sẵn sàng nhận đơn',
                'delivering': 'Đang giao',
                'offline': 'Offline'
            };
            return labels[status] || status;
        },

        getStatusBadgeClass(status) {
            const classes = {
                'available': 'bg-green-100 text-green-800 border-green-200',
                'delivering': 'bg-amber-100 text-amber-800 border-amber-200',
                'offline': 'bg-gray-100 text-gray-800 border-gray-200'
            };
            return `border ${classes[status] || classes.offline}`;
        },

        getStatusDotClass(status) {
            const classes = {
                'available': 'bg-green-500',
                'delivering': 'bg-amber-500',
                'offline': 'bg-gray-500'
            };
            return classes[status] || classes.offline;
        },

        getMarkerColor(status) {
            const colors = {
                'available': 'bg-green-500',
                'delivering': 'bg-amber-500',
                'offline': 'bg-gray-500'
            };
            return colors[status] || colors.offline;
        },
        
        getMarkerFillColor(status) {
            const colors = {
                'available': '#10B981', // green-500
                'delivering': '#F59E0B', // amber-500
                'offline': '#6B7280'     // gray-500
            };
            return colors[status] || colors.offline;
        },
        
        // Kết thúc các phương thức của component
    }
}

// Mobile sidebar functionality
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileSidebar = document.getElementById('mobile-sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const closeSidebar = document.getElementById('close-sidebar');

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileSidebar.classList.remove('hidden');
            sidebarOverlay.classList.remove('hidden');
        });
    }

    if (closeSidebar) {
        closeSidebar.addEventListener('click', function() {
            mobileSidebar.classList.add('hidden');
            sidebarOverlay.classList.add('hidden');
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            mobileSidebar.classList.add('hidden');
            sidebarOverlay.classList.add('hidden');
        });
    }
});
</script>
@endsection
