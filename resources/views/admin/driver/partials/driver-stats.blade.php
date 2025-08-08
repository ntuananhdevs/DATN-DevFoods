<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Thống kê tài xế</h2>
        <p class="text-gray-600">Tổng quan về trạng thái hoạt động của tài xế</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Status Distribution -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
                Phân bố trạng thái
            </h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Sẵn sàng</span>
                        <span><span x-text="stats.available"></span> (<span x-text="stats.total > 0 ? ((stats.available / stats.total) * 100).toFixed(1) : 0"></span>%)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full transition-all duration-300" :style="`width: ${stats.total > 0 ? (stats.available / stats.total) * 100 : 0}%`"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Đang giao</span>
                        <span><span x-text="stats.delivering"></span> (<span x-text="stats.total > 0 ? ((stats.delivering / stats.total) * 100).toFixed(1) : 0"></span>%)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-amber-500 h-2 rounded-full transition-all duration-300" :style="`width: ${stats.total > 0 ? (stats.delivering / stats.total) * 100 : 0}%`"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Offline</span>
                        <span><span x-text="stats.offline"></span> (<span x-text="stats.total > 0 ? ((stats.offline / stats.total) * 100).toFixed(1) : 0"></span>%)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gray-500 h-2 rounded-full transition-all duration-300" :style="`width: ${stats.total > 0 ? (stats.offline / stats.total) * 100 : 0}%`"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                Hiệu suất
            </h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm">Thời gian phản hồi trung bình</span>
                    </div>
                    <span class="font-semibold">2.5 phút</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        <span class="text-sm">Khu vực phủ sóng</span>
                    </div>
                    <span class="font-semibold">12 quận</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        <span class="text-sm">Tỷ lệ hoạt động</span>
                    </div>
                    <span class="font-semibold" x-text="stats.total > 0 ? (((stats.available + stats.delivering) / stats.total) * 100).toFixed(1) + '%' : '0%'"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Driver List Table -->
    <div class="bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Chi tiết tài xế</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên tài xế</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số điện thoại</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vị trí</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="driver in filteredDrivers" :key="driver.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="driver.name"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getStatusBadgeClass(driver.status)" class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium">
                                    <div :class="getStatusDotClass(driver.status)" class="w-2 h-2 rounded-full"></div>
                                    <span x-text="getStatusLabel(driver.status)"></span>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="driver.phone"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="`${driver.lat.toFixed(4)}, ${driver.lng.toFixed(4)}`"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
