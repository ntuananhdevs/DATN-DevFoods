@extends('layouts.branch.contentLayoutMaster')
@section('title', 'Thống kê đơn hàng chi nhánh')
@section('description', 'Báo cáo tổng quan và chi tiết các đơn hàng tại chi nhánh')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50 p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Thống kê đơn hàng</h1>
                <p class="text-gray-600 text-lg">Báo cáo tổng quan và chi tiết các đơn hàng tại chi nhánh</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-white rounded-lg px-4 py-2 shadow-sm border">
                    <span class="text-sm text-gray-500">Cập nhật:</span>
                    <span class="text-sm font-medium text-gray-900 ml-1">{{ now()->format('H:i d/m/Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="flex items-center mb-4">
            <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900">Bộ lọc báo cáo</h3>
        </div>
        
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Trạng thái
                </label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="">Tất cả trạng thái</option>
                    <option value="processing" @if($status == 'processing') selected @endif>Đang xử lý</option>
                    <option value="delivered" @if($status == 'delivered') selected @endif>Đã giao</option>
                    <option value="cancelled" @if($status == 'cancelled') selected @endif>Đã hủy</option>
                </select>
            </div>

            <!-- From Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Từ ngày
                </label>
                <input type="date" name="from" value="{{ $from }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
            </div>

            <!-- To Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Đến ngày
                </label>
                <input type="date" name="to" value="{{ $to }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
            </div>

            <!-- Filter Button -->
            <div>
                <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium py-2 px-6 rounded-lg transition-all duration-200 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Lọc dữ liệu
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Today Orders -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors duration-300">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full font-medium">Hôm nay</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($totalToday) }}</div>
            <p class="text-sm text-gray-600">Tổng số đơn hôm nay</p>
            <div class="mt-3 flex items-center text-xs text-green-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                Cập nhật realtime
            </div>
        </div>

        <!-- Month Orders -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition-colors duration-300">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-medium">Tháng này</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($totalMonth) }}</div>
            <p class="text-sm text-gray-600">Tổng số đơn tháng này</p>
            <div class="mt-3 flex items-center text-xs text-blue-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Tăng trưởng ổn định
            </div>
        </div>

        <!-- Year Orders -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-orange-100 rounded-lg group-hover:bg-orange-200 transition-colors duration-300">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
                <span class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded-full font-medium">Năm nay</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($totalYear) }}</div>
            <p class="text-sm text-gray-600">Tổng số đơn năm nay</p>
            <div class="mt-3 flex items-center text-xs text-orange-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                Hiệu suất tốt
            </div>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Processing Orders -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-xs bg-white bg-opacity-20 px-2 py-1 rounded-full">Đang xử lý</span>
            </div>
            <div class="text-2xl font-bold mb-1">{{ number_format($orderStatusCount['processing']) }}</div>
            <p class="text-yellow-100 text-sm">Đơn hàng đang xử lý</p>
            @php
                $processingRate = $totalToday > 0 ? ($orderStatusCount['processing'] / $totalToday) * 100 : 0;
            @endphp
            <div class="mt-3 flex items-center text-xs text-yellow-100">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                {{ number_format($processingRate, 1) }}% tổng đơn hôm nay
            </div>
        </div>

        <!-- Delivered Orders -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-xs bg-white bg-opacity-20 px-2 py-1 rounded-full">Đã giao</span>
            </div>
            <div class="text-2xl font-bold mb-1">{{ number_format($orderStatusCount['delivered']) }}</div>
            <p class="text-green-100 text-sm">Đơn hàng đã giao</p>
            @php
                $deliveredRate = $totalToday > 0 ? ($orderStatusCount['delivered'] / $totalToday) * 100 : 0;
            @endphp
            <div class="mt-3 flex items-center text-xs text-green-100">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                {{ number_format($deliveredRate, 1) }}% tỷ lệ thành công
            </div>
        </div>

        <!-- Cancelled Orders -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <span class="text-xs bg-white bg-opacity-20 px-2 py-1 rounded-full">Đã hủy</span>
            </div>
            <div class="text-2xl font-bold mb-1">{{ number_format($orderStatusCount['cancelled']) }}</div>
            <p class="text-red-100 text-sm">Đơn hàng đã hủy</p>
            @php
                $cancelledRate = $totalToday > 0 ? ($orderStatusCount['cancelled'] / $totalToday) * 100 : 0;
            @endphp
            <div class="mt-3 flex items-center text-xs text-red-100">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                {{ number_format($cancelledRate, 1) }}% tỷ lệ hủy
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Orders by Day Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Biểu đồ số đơn theo ngày</h3>
                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Xu hướng</span>
            </div>
            <div class="relative h-64">
                <canvas id="ordersByDayChart"></canvas>
            </div>
        </div>

        <!-- Orders by Payment Method Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Phương thức thanh toán</h3>
                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Phân bố</span>
            </div>
            <div class="relative h-64">
                <canvas id="ordersByPaymentChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Insights Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Largest Order -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Đơn hàng lớn nhất</h3>
                <span class="text-xs bg-emerald-100 text-emerald-800 px-2 py-1 rounded-full">Cao nhất</span>
            </div>
            
            @if($largestOrder)
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-emerald-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $largestOrder->order_code }}</div>
                                <div class="text-sm text-gray-600">{{ $largestOrder->customer?->full_name ?? $largestOrder->guest_name }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-emerald-600">{{ number_format($largestOrder->total_amount, 0, ',', '.') }}đ</div>
                            <div class="text-xs text-gray-500">{{ $largestOrder->branch?->name ?? 'Không xác định' }}</div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500">Không có dữ liệu</p>
                </div>
            @endif
        </div>

        <!-- Longest Delivery Order -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Đơn giao lâu nhất</h3>
                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Cần cải thiện</span>
            </div>
            
            @if($longestOrder)
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $longestOrder->order_code }}</div>
                                <div class="text-sm text-gray-600">{{ $longestOrder->customer?->full_name ?? $longestOrder->guest_name }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-red-600">
                                {{ $longestOrder->actual_delivery_time?->diffForHumans($longestOrder->estimated_delivery_time, true) }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $longestOrder->branch?->name ?? 'Không xác định' }}</div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500">Không có dữ liệu</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart.js default configuration
    Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
    Chart.defaults.color = '#6B7280';
    Chart.defaults.borderColor = '#E5E7EB';

    // Orders by Day Chart
    const ordersByDay = @json($ordersByDay);
    const ordersByDayLabels = ordersByDay.map(item => `Ngày ${item.day}`);
    const ordersByDayData = ordersByDay.map(item => item.total);

    // Create gradient for line chart
    const ctx1 = document.getElementById('ordersByDayChart').getContext('2d');
    const gradient1 = ctx1.createLinearGradient(0, 0, 0, 400);
    gradient1.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
    gradient1.addColorStop(1, 'rgba(59, 130, 246, 0.1)');

    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ordersByDayLabels,
            datasets: [{
                label: 'Số đơn',
                data: ordersByDayData,
                borderColor: '#3B82F6',
                backgroundColor: gradient1,
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointBackgroundColor: '#3B82F6',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#6B7280' }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#F3F4F6' },
                    ticks: { color: '#6B7280' }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Orders by Payment Method Chart
    const ordersByPayment = @json($ordersByPayment);
    const paymentLabels = Object.values(@json($paymentMethods));
    const paymentData = Object.values(ordersByPayment);

    new Chart(document.getElementById('ordersByPaymentChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: paymentLabels,
            datasets: [{
                data: paymentData,
                backgroundColor: [
                    '#3B82F6',
                    '#F59E0B',
                    '#10B981',
                    '#EF4444',
                    '#8B5CF6'
                ],
                borderWidth: 0,
                hoverBorderWidth: 2,
                hoverBorderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    cornerRadius: 8
                }
            },
            cutout: '60%'
        }
    });

    // Add loading animation
    document.addEventListener('DOMContentLoaded', function() {
        const elements = document.querySelectorAll('.bg-white, .bg-gradient-to-r');
        elements.forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            setTimeout(() => {
                el.style.transition = 'all 0.6s ease-out';
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });

    // Add form submission feedback
    document.querySelector('form').addEventListener('submit', function() {
        const button = this.querySelector('button[type="submit"]');
        const originalText = button.innerHTML;
        button.innerHTML = `
            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Đang tải...
        `;
        button.disabled = true;
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        }, 2000);
    });
</script>
@endpush
