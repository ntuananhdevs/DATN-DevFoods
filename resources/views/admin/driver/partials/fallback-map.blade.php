<div class="relative h-full bg-gradient-to-br from-blue-50 to-green-50 overflow-hidden">
    <!-- Map Background -->
    <div class="absolute inset-0">
        <div id="map" class="w-full h-full"></div>
    </div>

    <!-- Driver Markers -->
    <div class="absolute inset-0">
        <template x-for="(driver, index) in filteredDrivers" :key="driver.id">
            <div class="absolute transform -translate-x-1/2 -translate-y-1/2 cursor-pointer transition-all duration-200 hover:scale-110"
                 :style="`left: ${filteredDrivers.length > 1 ? ((driver.lng - 105.7) / 0.2) * 80 + 10 : 50}%; top: ${filteredDrivers.length > 1 ? ((21.1 - driver.lat) / 0.2) * 80 + 10 : 50}%; z-index: ${selectedDriver && selectedDriver.id === driver.id ? 20 : 10};`"
                 @click="selectDriver(driver)">
                
                <!-- Marker -->
                <div :class="`w-8 h-8 rounded-full border-3 border-white shadow-lg flex items-center justify-center text-white text-xs font-bold ${getMarkerColor(driver.status)} ${selectedDriver && selectedDriver.id === driver.id ? 'ring-4 ring-blue-300 scale-125' : ''}`">
                    <template x-if="driver.status === 'available'">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </template>
                    <template x-if="driver.status === 'delivering'">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </template>
                    <template x-if="driver.status === 'offline'">
                        <div class="w-3 h-3 rounded-full bg-current"></div>
                    </template>
                </div>

                <!-- Pulse animation for selected -->
                <div x-show="selectedDriver && selectedDriver.id === driver.id" class="absolute inset-0 rounded-full bg-blue-400 animate-ping opacity-30 -z-10"></div>

                <!-- Driver name label -->
                <div class="absolute top-10 left-1/2 transform -translate-x-1/2 whitespace-nowrap">
                    <div class="bg-white px-2 py-1 rounded shadow-md text-xs font-medium text-gray-800 border" x-text="driver.name"></div>
                </div>
            </div>
        </template>
    </div>

    <!-- Map Controls -->
    <div class="absolute top-4 left-4 bg-white rounded-lg shadow-lg p-3 z-30">
        <div class="space-y-2 text-sm">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                <span>Sẵn sàng: <span x-text="stats.available"></span></span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
                <span>Đang giao: <span x-text="stats.delivering"></span></span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-gray-500 rounded-full"></div>
                <span>Offline: <span x-text="stats.offline"></span></span>
            </div>
        </div>
    </div>

    <!-- Map Info -->
    <div class="absolute top-4 right-4 bg-white rounded-lg shadow-lg p-3 z-30">
        <div class="text-sm text-gray-600">
            <div class="flex items-center gap-2 mb-1">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                </svg>
                <span class="font-medium">Khu vực: Hà Nội</span>
            </div>
            <div class="text-xs text-gray-500">
                Tọa độ: 21.0278, 105.8342
            </div>
        </div>
    </div>

    <!-- Selected Driver Details -->
    <div x-show="selectedDriver" class="absolute bottom-4 left-4 bg-white rounded-lg shadow-lg p-4 z-30 max-w-xs">
        <div class="flex items-center gap-3 mb-3">
            <div :class="`w-6 h-6 rounded-full flex items-center justify-center text-white ${selectedDriver ? getMarkerColor(selectedDriver.status) : ''}`">
                <template x-if="selectedDriver && selectedDriver.status === 'available'">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </template>
                <template x-if="selectedDriver && selectedDriver.status === 'delivering'">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </template>
                <template x-if="selectedDriver && selectedDriver.status === 'offline'">
                    <div class="w-3 h-3 rounded-full bg-current"></div>
                </template>
            </div>
            <div>
                <h3 class="font-bold text-gray-900" x-text="selectedDriver ? selectedDriver.name : ''"></h3>
                <p class="text-sm text-gray-600" x-text="selectedDriver ? getStatusLabel(selectedDriver.status) : ''"></p>
            </div>
        </div>
        <div class="space-y-2 text-sm text-gray-600">
            <div class="flex justify-between">
                <span>SĐT:</span>
                <span class="font-medium" x-text="selectedDriver ? selectedDriver.phone : ''"></span>
            </div>
            <div class="flex justify-between">
                <span>Vị trí:</span>
                <span class="font-medium" x-text="selectedDriver ? `${selectedDriver.lat.toFixed(4)}, ${selectedDriver.lng.toFixed(4)}` : ''"></span>
            </div>
            <div x-show="selectedDriver && selectedDriver.totalOrders" class="flex justify-between">
                <span>Tổng đơn:</span>
                <span class="font-medium" x-text="selectedDriver ? selectedDriver.totalOrders : ''"></span>
            </div>
            <div x-show="selectedDriver && selectedDriver.rating" class="flex justify-between">
                <span>Đánh giá:</span>
                <span class="font-medium">⭐ <span x-text="selectedDriver ? selectedDriver.rating : ''"></span></span>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="absolute bottom-4 right-4 bg-white rounded-lg shadow-lg p-3 z-30">
        <h4 class="text-sm font-medium text-gray-900 mb-2">Chú thích</h4>
        <div class="space-y-1 text-xs">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-green-500 rounded-full flex items-center justify-center text-white">
                    <svg class="h-2 w-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span>Sẵn sàng nhận đơn</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-amber-500 rounded-full flex items-center justify-center text-white">
                    <svg class="h-2 w-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </div>
                <span>Đang giao hàng</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-gray-500 rounded-full"></div>
                <span>Không hoạt động</span>
            </div>
        </div>
    </div>

    <!-- Fallback Notice -->
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 pointer-events-none">
        <div class="bg-white/90 backdrop-blur-sm rounded-lg p-4 shadow-lg text-center max-w-sm">
            <svg class="h-8 w-8 text-blue-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            </svg>
            <h3 class="font-semibold text-gray-900 mb-1">Bản đồ tương tác</h3>
            <p class="text-sm text-gray-600">Hiển thị <span x-text="filteredDrivers.length"></span> tài xế trong khu vực Hà Nội</p>
            <p class="text-xs text-gray-500 mt-2">Click vào marker để xem chi tiết tài xế</p>
        </div>
    </div>
</div>
