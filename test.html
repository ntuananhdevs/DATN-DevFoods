@extends('layouts.driver.master')

@section('title', 'Đơn hàng - FoodDriver')

@section('content')
<div class="pb-16" x-data="ordersData()">
    <!-- Search and Filter -->
    <div class="bg-white shadow-sm sticky top-16 z-20">
        <div class="max-w-lg mx-auto px-4 py-4">
            <!-- Search -->
            <div class="relative mb-4">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                <input type="text"
                       x-model="searchTerm"
                       @input="filterOrders"
                       placeholder="Tìm kiếm đơn hàng..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button x-show="searchTerm"
                        @click="searchTerm = ''; filterOrders()"
                        class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Filter Tabs -->
            <div class="flex border-b border-gray-200">
                <template x-for="tab in filterTabs" :key="tab.key">
                    <button @click="activeFilter = tab.key; filterOrders()"
                            :class="activeFilter === tab.key ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'"
                            class="flex-1 py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                            x-text="tab.label + (tab.count > 0 ? ' (' + tab.count + ')' : '')">
                    </button>
                </template>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <div class="max-w-lg mx-auto px-4 py-4">
        <template x-if="filteredOrders.length > 0">
            <div class="space-y-3">
                <template x-for="order in filteredOrders" :key="order.id">
                    <div @click="viewOrder(order.id)"
                         class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden cursor-pointer hover:shadow-md transition-shadow">
                        <!-- Order Header -->
                        <div class="p-4 border-b border-gray-100">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                                          :class="getStatusClass(order.status)"
                                          x-text="getStatusLabel(order.status)"></span>
                                    <span class="text-sm text-gray-500" x-text="'#' + order.id"></span>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-green-600" x-text="formatCurrency(order.total_amount)"></div>
                                    <div class="text-xs text-gray-500" x-text="'Phí ship: ' + formatCurrency(order.delivery_fee)"></div>
                                </div>
                            </div>

                            <!-- Customer Info -->
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user text-gray-400 text-sm"></i>
                                    <span class="font-medium text-gray-900" x-text="getCustomerName(order)"></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-phone text-gray-400 text-sm"></i>
                                    <span class="text-sm text-gray-600" x-text="getCustomerPhone(order)"></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-gray-400 text-sm"></i>
                                    <span class="text-sm text-gray-600 line-clamp-1" x-text="order.delivery_address"></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-clock text-gray-400 text-sm"></i>
                                    <span class="text-sm text-gray-600" x-text="'Giao lúc: ' + formatTime(order.estimated_delivery_time)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="p-4">
                            <div class="text-sm font-medium text-gray-700 mb-2">Món ăn:</div>
                            <div class="space-y-1">
                                <template x-for="item in order.items.slice(0, 2)" :key="item.name">
                                    <div class="flex justify-between text-sm">
                                        <span x-text="item.quantity + 'x ' + item.name"></span>
                                        <span x-text="formatCurrency(item.price)"></span>
                                    </div>
                                </template>
                                <div x-show="order.items.length > 2" class="text-sm text-gray-500">
                                    <span x-text="'và ' + (order.items.length - 2) + ' món khác...'"></span>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div x-show="order.notes" class="mt-3 p-2 bg-yellow-50 border border-yellow-200 rounded text-sm">
                                <div class="font-medium text-yellow-800 mb-1">Ghi chú:</div>
                                <div class="text-yellow-700" x-text="order.notes"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <!-- Empty State -->
        <template x-if="filteredOrders.length === 0">
            <div class="text-center py-12">
                <i class="fas fa-box-open text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-600 mb-2">Không tìm thấy đơn hàng</h3>
                <p class="text-gray-500" x-text="searchTerm ? 'Thử tìm kiếm với từ khóa khác' : 'Chưa có đơn hàng nào'"></p>
            </div>
        </template>
    </div>

    <!-- Floating Action Button -->
    <button @click="refreshOrders()"
            class="fixed bottom-20 right-4 w-14 h-14 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
        <i class="fas fa-sync-alt" :class="{ 'animate-spin': refreshing }"></i>
    </button>
</div>
@endsection

@push('scripts')
<script src="assets/js/orders.js"></script>
@endpush
