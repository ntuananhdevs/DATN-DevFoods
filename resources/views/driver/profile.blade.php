@extends('layouts.master')

@section('title', 'Hồ sơ - FoodDriver')

@section('content')
<div class="pb-16" x-data="profileData()">
    <!-- Profile Header -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white">
        <div class="max-w-lg mx-auto px-4 py-6">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-2xl font-bold">
                        <span x-text="driverName.charAt(0).toUpperCase()"></span>
                    </div>
                    <button @click="showImagePicker = true"
                            class="absolute bottom-0 right-0 w-6 h-6 bg-blue-700 rounded-full flex items-center justify-center">
                        <i class="fas fa-camera text-xs"></i>
                    </button>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-semibold" x-text="driverName"></h2>
                    <div class="flex items-center gap-2 mt-1">
                        <i class="fas fa-phone text-sm"></i>
                        <span x-text="driverPhone"></span>
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full flex items-center gap-1">
                            <i class="fas fa-star"></i>
                            <span x-text="rating"></span>
                        </span>
                        <span class="px-2 py-1 bg-white bg-opacity-20 text-xs rounded-full" x-text="'ID: ' + driverId"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="bg-white shadow-sm">
        <div class="max-w-lg mx-auto px-4 py-4">
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600" x-text="stats.totalOrders"></div>
                    <div class="text-sm text-gray-500">Đơn giao</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600" x-text="stats.successRate + '%'"></div>
                    <div class="text-sm text-gray-500">Thành công</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600" x-text="stats.experienceMonths"></div>
                    <div class="text-sm text-gray-500">Tháng</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-lg mx-auto px-4 py-4 space-y-4">
        <!-- Personal Info -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Thông tin cá nhân</h3>
            </div>
            <div class="divide-y divide-gray-100">
                <template x-for="item in personalInfo" :key="item.key">
                    <div @click="editField(item.key)"
                         class="p-4 hover:bg-gray-50 cursor-pointer flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i :class="item.icon" class="text-gray-400 w-5"></i>
                            <div>
                                <div class="font-medium text-gray-900" x-text="item.label"></div>
                                <div class="text-sm text-gray-600" x-text="item.value"></div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                </template>
            </div>
        </div>

        <!-- Vehicle Info -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Thông tin xe</h3>
            </div>
            <div class="divide-y divide-gray-100">
                <template x-for="item in vehicleInfo" :key="item.key">
                    <div @click="editField(item.key)"
                         class="p-4 hover:bg-gray-50 cursor-pointer flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i :class="item.icon" class="text-gray-400 w-5"></i>
                            <div>
                                <div class="font-medium text-gray-900" x-text="item.label"></div>
                                <div class="text-sm text-gray-600" x-text="item.value"></div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                </template>
            </div>
        </div>

        <!-- Menu Items -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="divide-y divide-gray-100">
                <template x-for="item in menuItems" :key="item.key">
                    <div @click="handleMenuClick(item.key)"
                         class="p-4 hover:bg-gray-50 cursor-pointer flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                 :class="item.bgColor">
                                <i :class="item.icon + ' ' + item.textColor"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900" x-text="item.title"></div>
                                <div class="text-sm text-gray-500" x-text="item.subtitle"></div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                </template>
            </div>
        </div>

        <!-- App Info -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Thông tin ứng dụng</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Phiên bản:</span>
                    <span class="font-medium">1.2.0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Cập nhật cuối:</span>
                    <span class="font-medium">15/01/2024</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Kích thước:</span>
                    <span class="font-medium">28.4 MB</span>
                </div>
            </div>
        </div>

        <!-- Logout Button -->
        <button @click="handleLogout"
                class="w-full flex items-center justify-center gap-2 py-3 px-4 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition-colors">
            <i class="fas fa-sign-out-alt"></i>
            Đăng xuất
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script src="assets/js/profile.js"></script>
@endpush
