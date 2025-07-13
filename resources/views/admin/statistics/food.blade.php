@extends('layouts.admin.contentLayoutMaster')
@section('title', 'Thống kê món ăn')
@section('description', 'Báo cáo tổng quan và chi tiết các món ăn trong hệ thống đặt đồ ăn')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-orange-50 p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Thống kê món ăn</h1>
                <p class="text-gray-600 text-lg">Báo cáo tổng quan và chi tiết các món ăn trong hệ thống</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-white rounded-lg px-4 py-2 shadow-sm border">
                    <span class="text-sm text-gray-500">Tổng món:</span>
                    <span class="text-sm font-medium text-gray-900 ml-1">{{ count($foods) }}</span>
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
            <h3 class="text-lg font-semibold text-gray-900">Bộ lọc thống kê</h3>
        </div>
        
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <!-- Branch Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Chi nhánh
                </label>
                <select name="branch_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                    <option value="">Tất cả chi nhánh</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @if($branchId == $branch->id) selected @endif>
                            {{ $branch->name }}
                        </option>
                    @endforeach
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
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
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
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
            </div>

            <!-- Sort Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                    </svg>
                    Sắp xếp
                </label>
                <select name="sort" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                    <option value="revenue" @if($sort == 'revenue') selected @endif>Theo doanh thu</option>
                    <option value="quantity" @if($sort == 'quantity') selected @endif>Theo số lượng</option>
                </select>
            </div>

            <!-- Filter Button -->
            <div>
                <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-medium py-2 px-6 rounded-lg transition-all duration-200 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Lọc dữ liệu
                </button>
            </div>
        </form>
    </div>

    <!-- Top Performers Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Best Selling Food -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Món bán chạy nhất</h3>
                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full font-medium">🏆 Top seller</span>
            </div>
            
            @if($bestFood)
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-400 to-green-500 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xl font-bold text-gray-900 mb-1">{{ $bestFood['name'] }}</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Đã bán:</span>
                                <span class="font-semibold text-green-600">{{ number_format($bestFood['total_quantity']) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Doanh thu:</span>
                                <span class="font-semibold text-green-600">{{ number_format($bestFood['total_revenue'], 0, ',', '.') }}đ</span>
                            </div>
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

        <!-- Least Selling Food -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Món ít được đặt nhất</h3>
                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full font-medium">⚠️ Cần cải thiện</span>
            </div>
            
            @if($worstFood)
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-red-400 to-red-500 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xl font-bold text-gray-900 mb-1">{{ $worstFood['name'] }}</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Đã bán:</span>
                                <span class="font-semibold text-red-600">{{ number_format($worstFood['total_quantity']) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Doanh thu:</span>
                                <span class="font-semibold text-red-600">{{ number_format($worstFood['total_revenue'], 0, ',', '.') }}đ</span>
                            </div>
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

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Foods by Day Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Số lượng món bán theo ngày</h3>
                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Xu hướng</span>
            </div>
            <div class="relative h-64">
                <canvas id="foodsByDayChart"></canvas>
            </div>
        </div>

        <!-- Foods by Hour Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Số lượng món bán theo khung giờ</h3>
                <span class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded-full">Thời gian</span>
            </div>
            <div class="relative h-64">
                <canvas id="foodsByHourChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Food List Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Chi tiết món ăn</h3>
                <span class="text-sm text-gray-500">{{ count($foods) }} món ăn</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Món ăn</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biến thể</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Doanh thu</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Hiệu suất</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($foods as $index => $food)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <!-- Food Name -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-r from-orange-400 to-red-500 rounded-lg flex items-center justify-center text-white text-sm font-medium mr-3">
                                        {{ substr($food['name'], 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $food['name'] }}</div>
                                        <div class="text-xs text-gray-500">#{{ $index + 1 }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Variant -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $food['variant'] ?: 'Mặc định' }}
                                </span>
                            </td>

                            <!-- Quantity -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $food['total_quantity'] >= 100 ? 'bg-green-100 text-green-800' : ($food['total_quantity'] >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ number_format($food['total_quantity']) }}
                                </span>
                            </td>

                            <!-- Revenue -->
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ number_format($food['total_revenue'], 0, ',', '.') }}đ
                                </div>
                                @if($food['total_quantity'] > 0)
                                    <div class="text-xs text-gray-500">
                                        {{ number_format($food['total_revenue'] / $food['total_quantity'], 0, ',', '.') }}đ/món
                                    </div>
                                @endif
                            </td>

                            <!-- Performance -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $maxRevenue = collect($foods)->max('total_revenue');
                                    $performance = $maxRevenue > 0 ? ($food['total_revenue'] / $maxRevenue) * 100 : 0;
                                @endphp
                                <div class="flex items-center justify-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="h-2 rounded-full transition-all duration-500 
                                            {{ $performance >= 80 ? 'bg-green-500' : ($performance >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                            style="width: {{ $performance }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium 
                                        {{ $performance >= 80 ? 'text-green-600' : ($performance >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ number_format($performance, 0) }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">Không có dữ liệu</h3>
                                    <p class="text-gray-500">Không tìm thấy món ăn nào với bộ lọc hiện tại</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Branch Comparison Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">So sánh món ăn giữa các chi nhánh</h3>
                <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full">Phân tích</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên món</th>
                        @foreach($branches as $branch)
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $branch->name }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($foods as $food)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-500 rounded-lg flex items-center justify-center text-white text-xs font-medium mr-3">
                                        {{ substr($food['name'], 0, 1) }}
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">{{ $food['name'] }}</div>
                                </div>
                            </td>
                            @foreach($branches as $branch)
                                @php
                                    $branchTotal = $compareByBranch->where('product_variant_id', $food['product_id'])
                                        ->where('branch_id', $branch->id)
                                        ->sum('total');
                                @endphp
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $branchTotal >= 50 ? 'bg-green-100 text-green-800' : ($branchTotal >= 20 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ number_format($branchTotal) }}
                                    </span>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
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

    // Foods by Day Chart
    const foodsByDay = @json($foodsByDay);
    const foodsByDayLabels = foodsByDay.map(item => `Ngày ${item.day}`);
    const foodsByDayData = foodsByDay.map(item => item.total);

    // Create gradient for bar chart
    const ctx1 = document.getElementById('foodsByDayChart').getContext('2d');
    const gradient1 = ctx1.createLinearGradient(0, 0, 0, 400);
    gradient1.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
    gradient1.addColorStop(1, 'rgba(59, 130, 246, 0.1)');

    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: foodsByDayLabels,
            datasets: [{
                label: 'Số lượng',
                data: foodsByDayData,
                backgroundColor: gradient1,
                borderColor: '#3B82F6',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                hoverBackgroundColor: '#2563EB',
                hoverBorderColor: '#1D4ED8'
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
            }
        }
    });

    // Foods by Hour Chart
    const foodsByHour = @json($foodsByHour);
    const foodsByHourLabels = foodsByHour.map(item => (item.hour < 10 ? '0' : '') + item.hour + ':00');
    const foodsByHourData = foodsByHour.map(item => item.total);

    // Create gradient for line chart
    const ctx2 = document.getElementById('foodsByHourChart').getContext('2d');
    const gradient2 = ctx2.createLinearGradient(0, 0, 0, 400);
    gradient2.addColorStop(0, 'rgba(245, 158, 66, 0.8)');
    gradient2.addColorStop(1, 'rgba(245, 158, 66, 0.1)');

    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: foodsByHourLabels,
            datasets: [{
                label: 'Số lượng',
                data: foodsByHourData,
                borderColor: '#F59E0B',
                backgroundColor: gradient2,
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointBackgroundColor: '#F59E0B',
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

    // Add loading animation
    document.addEventListener('DOMContentLoaded', function() {
        const elements = document.querySelectorAll('.bg-white');
        elements.forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            setTimeout(() => {
                el.style.transition = 'all 0.6s ease-out';
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, index * 100);
        });

        // Animate progress bars
        setTimeout(() => {
            const progressBars = document.querySelectorAll('.bg-green-500, .bg-yellow-500, .bg-red-500');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.transition = 'width 1s ease-out';
                    bar.style.width = width;
                }, 200);
            });
        }, 1000);
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
