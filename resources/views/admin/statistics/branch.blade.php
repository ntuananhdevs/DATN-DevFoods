@extends('layouts.admin.contentLayoutMaster')
@section('title', 'Store Analytics')
@section('description', 'Phân tích chi tiết theo cửa hàng và chi nhánh')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50 p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Thống kê chi nhánh</h1>
                <p class="text-gray-600 text-lg">Phân tích chi tiết theo từng chi nhánh</p>
    </div>

            <!-- Branch Selector -->
            <div class="flex items-center gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-1">
                    <form id="branch-filter-form" method="GET" action="" class="flex items-center">
                        <div class="flex items-center gap-2 px-3 py-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <select name="branch_id" 
                                    class="border-0 bg-transparent text-sm font-medium text-gray-700 focus:ring-0 focus:outline-none cursor-pointer" 
                                    id="branch-select" 
                                    onchange="document.getElementById('branch-filter-form').submit()">
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" @if($selectedBranch && $selectedBranch->id == $branch->id) selected @endif>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                
                <!-- Current Branch Info -->
                @if($selectedBranch)
                <div class="bg-indigo-100 text-indigo-800 px-4 py-2 rounded-lg text-sm font-medium">
                    📍 {{ $selectedBranch->name }}
                </div>
                @endif
                        </div>
                        </div>
                    </div>

    <!-- Stats Cards Grid -->
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
            <div class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($ordersToday) }}</div>
            <p class="text-sm text-gray-600">Tổng đơn hàng hôm nay</p>
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
            <div class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($ordersMonth) }}</div>
            <p class="text-sm text-gray-600">Tổng đơn hàng tháng này</p>
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
            <div class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($ordersYear) }}</div>
            <p class="text-sm text-gray-600">Tổng đơn hàng năm nay</p>
            <div class="mt-3 flex items-center text-xs text-orange-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                Hiệu suất tốt
                        </div>
                    </div>
                </div>
                
    <!-- Revenue Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Today Revenue -->
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                                </div>
                <span class="text-xs bg-white bg-opacity-20 px-2 py-1 rounded-full">Hôm nay</span>
                                </div>
            <div class="text-2xl font-bold mb-1">{{ number_format($revenue['today'], 0, ',', '.') }}đ</div>
            <p class="text-emerald-100 text-sm">Doanh thu hôm nay</p>
            <div class="mt-3 flex items-center text-xs text-emerald-100">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                {{ $ordersToday > 0 ? number_format($revenue['today'] / $ordersToday, 0, ',', '.') : 0 }}đ/đơn
                                </div>
                            </div>
                            
        <!-- Month Revenue -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                                </div>
                <span class="text-xs bg-white bg-opacity-20 px-2 py-1 rounded-full">Tháng này</span>
                                </div>
            <div class="text-2xl font-bold mb-1">{{ number_format($revenue['month'], 0, ',', '.') }}đ</div>
            <p class="text-blue-100 text-sm">Doanh thu tháng này</p>
            <div class="mt-3 flex items-center text-xs text-blue-100">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                {{ $ordersMonth > 0 ? number_format($revenue['month'] / $ordersMonth, 0, ',', '.') : 0 }}đ/đơn
                                </div>
                            </div>
                            
        <!-- Year Revenue -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <span class="text-xs bg-white bg-opacity-20 px-2 py-1 rounded-full">Năm nay</span>
                                </div>
            <div class="text-2xl font-bold mb-1">{{ number_format($revenue['year'], 0, ',', '.') }}đ</div>
            <p class="text-purple-100 text-sm">Doanh thu năm nay</p>
            <div class="mt-3 flex items-center text-xs text-purple-100">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                {{ $ordersYear > 0 ? number_format($revenue['year'] / $ordersYear, 0, ',', '.') : 0 }}đ/đơn
                                </div>
                                </div>
                            </div>
                            
    <!-- Charts and Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Orders by Day Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Biểu đồ đơn hàng theo ngày</h3>
                <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full">Tháng này</span>
                                </div>
            <div class="relative h-64">
                <canvas id="ordersByDayChart"></canvas>
            </div>
        </div>
        
        <!-- Top Product Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Món ăn bán chạy nhất</h3>
                <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Top seller</span>
            </div>
            
            <div class="text-center">
                <div class="w-20 h-20 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                </div>
                
                <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $topProductName }}</h4>
                <div class="flex items-center justify-center gap-2 mb-4">
                    <span class="text-2xl font-bold text-orange-600">{{ number_format($topProductTotal) }}</span>
                    <span class="text-sm text-gray-600">đã bán</span>
                            </div>
                            
                <div class="bg-orange-50 rounded-lg p-3">
                    <div class="flex items-center justify-center text-sm text-orange-800">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                        </svg>
                        Sản phẩm được yêu thích nhất
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Cancellation Rate -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Tỉ lệ đơn bị hủy</h3>
                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Cần cải thiện</span>
        </div>
        
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-red-600">{{ $cancelledRate }}%</div>
                        <div class="text-sm text-gray-600">Tỉ lệ hủy đơn</div>
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                <div class="bg-red-500 h-2 rounded-full transition-all duration-500" style="width: {{ min($cancelledRate, 100) }}%"></div>
                </div>
                
            <div class="text-xs text-gray-500">
                @if($cancelledRate < 5)
                    ✅ Tỉ lệ hủy đơn trong mức chấp nhận được
                @elseif($cancelledRate < 10)
                    ⚠️ Tỉ lệ hủy đơn hơi cao, cần theo dõi
                @else
                    🚨 Tỉ lệ hủy đơn cao, cần cải thiện ngay
                @endif
            </div>
        </div>
        
        <!-- Late Delivery Rate -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Tỉ lệ đơn giao trễ</h3>
                <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Theo dõi</span>
                        </div>
                        
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                        <div class="text-3xl font-bold text-yellow-600">{{ $lateRate }}%</div>
                        <div class="text-sm text-gray-600">Tỉ lệ giao trễ</div>
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                <div class="bg-yellow-500 h-2 rounded-full transition-all duration-500" style="width: {{ min($lateRate, 100) }}%"></div>
                </div>
                
            <div class="text-xs text-gray-500">
                @if($lateRate < 10)
                    ✅ Tỉ lệ giao trễ trong mức chấp nhận được
                @elseif($lateRate < 20)
                    ⚠️ Tỉ lệ giao trễ hơi cao, cần cải thiện
                @else
                    🚨 Tỉ lệ giao trễ cao, cần xem xét lại quy trình
                @endif
            </div>
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

    // Create gradient
    const ctx = document.getElementById('ordersByDayChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.8)');
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.1)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ordersByDayLabels,
            datasets: [{
                label: 'Số đơn',
                data: ordersByDayData,
                backgroundColor: gradient,
                borderColor: '#6366F1',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                hoverBackgroundColor: '#4F46E5',
                hoverBorderColor: '#4338CA'
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
                    displayColors: false,
                    callbacks: {
                        title: function(context) {
                            return context[0].label;
                        },
                        label: function(context) {
                            return `Số đơn: ${context.parsed.y}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { 
                        color: '#6B7280',
                        font: { size: 12 }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { 
                        color: '#F3F4F6',
                        drawBorder: false
                    },
                    ticks: { 
                        color: '#6B7280',
                        font: { size: 12 }
                    }
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

        // Animate progress bars
        setTimeout(() => {
            const progressBars = document.querySelectorAll('.bg-red-500, .bg-yellow-500');
            progressBars.forEach(bar => {
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.transition = 'width 1s ease-out';
                    bar.style.width = bar.getAttribute('style').match(/width:\s*([^;]+)/)[1];
                }, 500);
            });
        }, 1000);
    });

    // Add hover effect to branch selector
    document.getElementById('branch-select').addEventListener('change', function() {
        const form = document.getElementById('branch-filter-form');
        form.style.opacity = '0.7';
        setTimeout(() => {
            form.submit();
        }, 200);
    });
</script>
@endpush
