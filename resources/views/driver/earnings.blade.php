@extends('layouts..master')

@section('title', 'Thu nhập - FoodDriver')

@section('content')
<div class="pb-16" x-data="earningsData()">
    <!-- Header Stats -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white">
        <div class="max-w-lg mx-auto px-4 py-6">
            <div class="text-center">
                <h1 class="text-2xl font-bold mb-2">Thu nhập</h1>
                <div class="text-3xl font-bold mb-1" x-text="formatCurrency(currentEarnings.total)"></div>
                <div class="text-green-100" x-text="currentPeriodLabel"></div>
            </div>
        </div>
    </div>

    <!-- Period Tabs -->
    <div class="bg-white shadow-sm sticky top-16 z-20">
        <div class="max-w-lg mx-auto px-4">
            <div class="flex border-b border-gray-200">
                <template x-for="period in periods" :key="period.key">
                    <button @click="activePeriod = period.key; updateCurrentEarnings()"
                            :class="activePeriod === period.key ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500'"
                            class="flex-1 py-3 px-1 border-b-2 font-medium text-sm transition-colors"
                            x-text="period.label">
                    </button>
                </template>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-lg mx-auto px-4 py-4 space-y-4">
        <!-- Stats Grid -->
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-box text-blue-600"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900" x-text="currentEarnings.orders"></div>
                        <div class="text-sm text-gray-500">Đơn hàng</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-coins text-green-600"></i>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-gray-900" x-text="formatCurrency(currentEarnings.tips || 0)"></div>
                        <div class="text-sm text-gray-500">Tips</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-purple-600"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900" x-text="(currentEarnings.hours || 0) + 'h'"></div>
                        <div class="text-sm text-gray-500">Giờ làm</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-line text-orange-600"></i>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-gray-900" x-text="formatCurrency(getHourlyRate())"></div>
                        <div class="text-sm text-gray-500">Theo giờ</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings Chart -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Biểu đồ thu nhập</h3>
            <div class="h-48 flex items-end justify-between gap-2">
                <template x-for="(day, index) in chartData" :key="index">
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-full bg-green-500 rounded-t transition-all duration-500 hover:bg-green-600 cursor-pointer"
                             :style="'height: ' + (day.amount / maxAmount * 100) + '%'"
                             :title="formatCurrency(day.amount)"></div>
                        <div class="text-xs text-gray-500 mt-2" x-text="day.label"></div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Giao dịch gần đây</h3>
            </div>
            <div class="divide-y divide-gray-100">
                <template x-for="transaction in recentTransactions" :key="transaction.id">
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                     :class="transaction.type === 'earning' ? 'bg-green-100' : 'bg-red-100'">
                                    <i :class="transaction.type === 'earning' ? 'fas fa-plus text-green-600' : 'fas fa-minus text-red-600'"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900" x-text="transaction.description"></div>
                                    <div class="text-sm text-gray-500" x-text="formatDateTime(transaction.date)"></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold"
                                     :class="transaction.type === 'earning' ? 'text-green-600' : 'text-red-600'"
                                     x-text="(transaction.type === 'earning' ? '+' : '-') + formatCurrency(transaction.amount)"></div>
                                <div class="text-xs text-gray-500" x-text="'Đơn #' + transaction.orderId"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tóm tắt</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Trung bình mỗi đơn:</span>
                    <span class="font-medium" x-text="formatCurrency(getAveragePerOrder())"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Hiệu suất:</span>
                    <span class="font-medium text-green-600" x-text="getPerformanceRating()"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Xếp hạng:</span>
                    <span class="font-medium text-blue-600">Top 20%</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tỷ lệ hoàn thành:</span>
                    <span class="font-medium text-green-600">98%</span>
                </div>
            </div>
        </div>

        <!-- Export Button -->
        <button @click="exportEarnings()"
                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
            <i class="fas fa-download"></i>
            Xuất báo cáo
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script src="assets/js/earnings.js"></script>
@endpush
