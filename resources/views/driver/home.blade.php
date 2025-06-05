@extends('layouts.driver.master')

@section('title', 'Dashboard - FoodDriver')

@section('content')
<div class="pb-16" x-data="dashboardData()">
    <!-- Main Content -->
    <div class="max-w-lg mx-auto px-4 py-4 space-y-4">
        <!-- Online/Offline Toggle -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-medium">Trạng thái làm việc</h3>
                    <p class="text-sm text-gray-500" x-text="isOnline ? 'Bạn đang online và có thể nhận đơn' : 'Bạn đang offline'"></p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm" x-text="isOnline ? 'Online' : 'Offline'"></span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="isOnline" @change="handleStatusChange" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Earnings Summary -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium">Thu nhập</h3>
                    <a href="earnings.html" class="text-blue-600 text-sm hover:underline flex items-center gap-1">
                        Chi tiết
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="p-4">
                <!-- Tabs -->
                <div class="flex border-b border-gray-200 mb-4">
                    <button @click="earningsTab = 'today'" 
                            :class="earningsTab === 'today' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'"
                            class="flex-1 py-2 px-1 border-b-2 font-medium text-sm">
                        Hôm nay
                    </button>
                    <button @click="earningsTab = 'week'" 
                            :class="earningsTab === 'week' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'"
                            class="flex-1 py-2 px-1 border-b-2 font-medium text-sm">
                        Tuần này
                    </button>
                    <button @click="earningsTab = 'month'" 
                            :class="earningsTab === 'month' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'"
                            class="flex-1 py-2 px-1 border-b-2 font-medium text-sm">
                        Tháng này
                    </button>
                </div>

                <!-- Earnings Content -->
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600" x-text="formatCurrency(earnings[earningsTab].total)"></div>
                    <div class="text-sm text-gray-500 mt-1" x-text="earnings[earningsTab].orders + ' đơn hàng'"></div>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium">Đơn hàng đang xử lý</h3>
                    <a href="orders.html" class="text-blue-600 text-sm hover:underline flex items-center gap-1">
                        Xem tất cả
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="p-4">
                <template x-if="pendingOrders.length > 0">
                    <div class="space-y-3">
                        <template x-for="order in pendingOrders" :key="order.id">
                            <div @click="viewOrder(order.id)" 
                                 class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100 cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-box text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium" x-text="'Đơn #' + order.id"></div>
                                        <div class="text-sm text-gray-500 flex items-center gap-1">
                                            <i class="fas fa-map-marker-alt text-xs"></i>
                                            <span x-text="order.delivery_address.substring(0, 20) + '...'"></span>
                                        </div>
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full"
                                      :class="order.status === 'assigned' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800'"
                                      x-text="order.status === 'assigned' ? 'Chờ lấy hàng' : 'Đã lấy hàng'"></span>
                            </div>
                        </template>
                    </div>
                </template>
                
                <template x-if="pendingOrders.length === 0">
                    <div class="text-center py-6">
                        <i class="fas fa-box text-4xl text-gray-300 mb-2"></i>
                        <h3 class="font-medium text-gray-600">Không có đơn hàng</h3>
                        <p class="text-sm text-gray-500 mt-1" x-text="isOnline ? 'Đơn hàng mới sẽ xuất hiện ở đây' : 'Bạn đang offline. Chuyển sang online để nhận đơn'"></p>
                    </div>
                </template>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-lg font-medium">Truy cập nhanh</h3>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-4 gap-4">
                    <a href="orders.html" class="flex flex-col items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-list text-lg mb-1 text-gray-600"></i>
                        <span class="text-xs text-gray-600">Đơn hàng</span>
                    </a>
                    <a href="earnings.html" class="flex flex-col items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-wallet text-lg mb-1 text-gray-600"></i>
                        <span class="text-xs text-gray-600">Thu nhập</span>
                    </a>
                    <a href="schedule.html" class="flex flex-col items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-calendar text-lg mb-1 text-gray-600"></i>
                        <span class="text-xs text-gray-600">Lịch làm</span>
                    </a>
                    <a href="profile.html" class="flex flex-col items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-user text-lg mb-1 text-gray-600"></i>
                        <span class="text-xs text-gray-600">Hồ sơ</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Today's Schedule -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-lg font-medium">Lịch làm việc hôm nay</h3>
            </div>
            <div class="p-4">
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-blue-600"></i>
                        </div>
                        <div>
                            <div class="font-medium">Ca chiều</div>
                            <div class="text-sm text-gray-500">14:00 - 22:00</div>
                        </div>
                    </div>
                    <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full">Đang làm việc</span>
                </div>
            </div>
        </div>

        <!-- Logout Button -->
        <button @click="handleLogout" 
                class="w-full flex items-center justify-center gap-2 py-3 px-4 border border-red-200 text-red-600 rounded-lg hover:bg-red-50">
            <i class="fas fa-sign-out-alt"></i>
            Đăng xuất
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script src="assets/js/dashboard.js"></script>
@endpush
