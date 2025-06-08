@extends('layouts.driver.masterLayout')

@section('title', 'Trang chủ - DevFoods Driver')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-white text-xl font-bold border-2 border-white/30">
                            {{ strtoupper(substr($driverData['name'], 0, 1)) }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full {{ $driverData['is_active'] ? 'bg-green-400' : 'bg-gray-400' }} border-2 border-white flex items-center justify-center">
                            @if($driverData['is_active'])
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">Xin chào, {{ $driverData['name'] }}!</h1>
                        <p class="text-blue-100 text-sm">{{ $driverData['vehicle_type'] ?? 'Xe máy' }} • {{ $driverData['license_plate'] ?? 'N/A' }}</p>
                        <div class="flex items-center mt-1">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= floor($driverData['rating']) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endfor
                                <span class="ml-2 text-sm text-blue-100">{{ number_format($driverData['rating'], 1) }}/5.0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg px-4 py-2 border border-white/20">
                        <div class="text-2xl font-bold">{{ number_format($todayStats['earnings']) }}đ</div>
                        <div class="text-xs text-blue-100">Thu nhập hôm nay</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-2xl font-bold text-blue-600">{{ $todayStats['orders_count'] }}</div>
                        <div class="text-sm text-gray-500">Đơn hôm nay</div>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-2xl font-bold text-purple-600">{{ number_format($todayStats['distance'], 1) }}km</div>
                        <div class="text-sm text-gray-500">Quãng đường</div>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-2xl font-bold text-orange-600">{{ $todayStats['completion_rate'] }}%</div>
                        <div class="text-sm text-gray-500">Tỷ lệ hoàn thành</div>
                    </div>
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-2xl font-bold text-green-600">{{ $orderCounts['completed_today'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Hoàn thành</div>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Orders Status -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Trạng thái đơn hàng</h3>
                <button class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center">
                    Xem tất cả
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
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
                
                <div class="relative overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-5 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-lg font-semibold text-blue-800 mb-1">Đang giao</h4>
                            <p class="text-3xl font-bold text-blue-600">{{ $orderCounts['delivering'] ?? 0 }}</p>
                            <p class="text-xs text-blue-600 mt-1">đơn hàng</p>
                        </div>
                        <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="absolute top-0 right-0 w-20 h-20 bg-blue-200/30 rounded-full -mr-10 -mt-10"></div>
                </div>
                
                <div class="relative overflow-hidden bg-gradient-to-br from-emerald-50 to-green-50 border border-emerald-200 rounded-xl p-5 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-lg font-semibold text-emerald-800 mb-1">Hoàn thành</h4>
                            <p class="text-3xl font-bold text-emerald-600">{{ $orderCounts['completed_today'] ?? 0 }}</p>
                            <p class="text-xs text-emerald-600 mt-1">hôm nay</p>
                        </div>
                        <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-200/30 rounded-full -mr-10 -mt-10"></div>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        @if(isset($pendingOrders) && count($pendingOrders) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Đơn hàng chờ nhận</h3>
                <div class="flex items-center space-x-2">
                    <span class="bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-medium px-3 py-1 rounded-full">
                        {{ count($pendingOrders) }} đơn
                    </span>
                    <button class="text-blue-600 hover:text-blue-700 p-1 rounded-lg hover:bg-blue-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
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
                            <div>
                                <h4 class="font-bold text-gray-900 text-lg">#{{ $order['id'] }}</h4>
                                <p class="text-sm text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $order['order_time'] }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-green-600 text-xl">{{ number_format($order['shipping_fee']) }}đ</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ $order['status_text'] ?? 'Chờ nhận' }}
                            </span>
                        </div>
                    </div>
                </template>
                
                <template x-if="pendingOrders.length === 0">
                    <div class="text-center py-6">
                        <i class="fas fa-box text-4xl text-gray-300 mb-2"></i>
                        <h3 class="font-medium text-gray-600">Không có đơn hàng</h3>
                        <p class="text-sm text-gray-500 mt-1" x-text="isOnline ? 'Đơn hàng mới sẽ xuất hiện ở đây' : 'Bạn đang offline. Chuyển sang online để nhận đơn'"></p>
                    </div>
                    
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-1 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                <span class="font-medium">{{ number_format($order['distance'], 1) }}km</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                {{ $order['customer_name'] }} - {{ $order['customer_phone'] }}
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('driver.orders.detail', $order['id']) }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Chi tiết
                            </a>
                            <button class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Nhận đơn
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <div class="text-center py-8">
                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Không có đơn hàng mới</h3>
                <p class="text-gray-500 mb-4">Hiện tại không có đơn hàng nào cần nhận. Hãy kiểm tra lại sau!</p>
                <button class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Làm mới
                </button>
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Thao tác nhanh</h3>
                <span class="text-sm text-gray-500">Truy cập nhanh các chức năng</span>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('driver.orders', ['status' => 'pending']) }}" class="group flex flex-col items-center p-5 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl hover:from-blue-100 hover:to-blue-200 transition-all duration-200 border border-blue-200 hover:shadow-lg">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-blue-900 group-hover:text-blue-800">Đơn chờ nhận</span>
                    <span class="text-xs text-blue-600 mt-1">({{ $orderCounts['pending'] ?? 0 }} đơn)</span>
                </a>

                <a href="{{ route('driver.orders', ['status' => 'delivering']) }}" class="group flex flex-col items-center p-5 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl hover:from-yellow-100 hover:to-yellow-200 transition-all duration-200 border border-yellow-200 hover:shadow-lg">
                    <div class="w-14 h-14 bg-gradient-to-br from-yellow-600 to-yellow-700 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-yellow-900 group-hover:text-yellow-800">Đang giao</span>
                    <span class="text-xs text-yellow-600 mt-1">({{ $orderCounts['delivering'] ?? 0 }} đơn)</span>
                </a>

                <a href="{{ route('driver.history') }}" class="group flex flex-col items-center p-5 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl hover:from-emerald-100 hover:to-emerald-200 transition-all duration-200 border border-emerald-200 hover:shadow-lg">
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-emerald-900 group-hover:text-emerald-800">Lịch sử</span>
                    <span class="text-xs text-emerald-600 mt-1">Xem tất cả</span>
                </a>

                <a href="{{ route('driver.earnings') }}" class="group flex flex-col items-center p-5 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl hover:from-purple-100 hover:to-purple-200 transition-all duration-200 border border-purple-200 hover:shadow-lg">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-purple-900 group-hover:text-purple-800">Thu nhập</span>
                    <span class="text-xs text-purple-600 mt-1">{{ number_format($todayStats['earnings']) }}đ</span>
                </a>
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

@push('scripts')
<script>
    // Auto refresh page every 30 seconds to get new orders
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            window.location.reload();
        }
    }, 30000);
    
    // Show notification for new orders
    @if(isset($orderCounts['pending']) && $orderCounts['pending'] > 0)
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('Đơn hàng mới!', {
                body: 'Bạn có {{ $orderCounts["pending"] }} đơn hàng mới cần nhận.',
                icon: '/favicon.ico'
            });
        }
    @endif
</script>
@endpush
@endsection

