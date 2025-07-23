@extends('layouts.driver.masterLayout')

@section('title', 'Thu nhập')
@section('page-title', 'Thu nhập')

@section('content')
<div class="pt-4 p-4 space-y-4">
    <!-- Period Selector -->
    <div id="period-buttons" class="flex space-x-2 mb-4">
        <button data-period="today" 
            class="px-4 py-2 {{ $filter == 'today' ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-600' }} rounded-full text-sm font-medium transition-all">
            Hôm nay
        </button>
        <button data-period="week" 
            class="px-4 py-2 {{ $filter == 'week' ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-600' }} rounded-full text-sm font-medium transition-all">
            Tuần này
        </button>
        <button data-period="month" 
            class="px-4 py-2 {{ $filter == 'month' ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-600' }} rounded-full text-sm font-medium transition-all">
            Tháng này
        </button>
    </div>

    <!-- Total Earnings -->
    <div class="bg-white rounded-lg p-6 shadow-sm text-center">
        <div id="earnings-value" class="text-3xl font-bold text-green-600 mb-2">
            {{ number_format($stats['total_earnings'], 0, ',', '.') }} đ
        </div>
        <div id="earnings-label" class="text-gray-500">Tổng thu nhập {{ $label }}</div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-list text-blue-600"></i>
                </div>
                <div>
                    <div id="total-orders" class="text-2xl font-bold">{{ $stats['total_orders'] }}</div>
                    <div class="text-sm text-gray-500">Đơn hàng</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-green-600"></i>
                </div>
                <div>
                    <div id="total-tips" class="text-2xl font-bold">{{ number_format($stats['total_tips'], 0, ',', '.') }} đ</div>
                    <div class="text-sm text-gray-500">Tips</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600"></i>
                </div>
                <div>
                    <div id="avg-per-order" class="text-2xl font-bold">{{ number_format($stats['avg_per_order'], 0, ',', '.') }} đ</div>
                    <div class="text-sm text-gray-500">TB/đơn</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-percentage text-orange-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold">
                        @if($stats['total_orders'] > 0)
                            {{ number_format(($stats['total_tips'] / $stats['total_earnings']) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </div>
                    <div class="text-sm text-gray-500">Tỷ lệ tip</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div class="bg-white rounded-lg p-4 shadow-sm">
        <h3 class="font-semibold mb-3">Tóm tắt {{ $label }}</h3>
        
        <div class="space-y-3">
            <div class="flex justify-between">
                <span>Trung bình mỗi đơn:</span>
                <span id="summary-avg" class="font-medium">{{ number_format($stats['avg_per_order'], 0, ',', '.') }} đ</span>
            </div>
            <div class="flex justify-between">
                <span>Tổng đơn hàng:</span>
                <span id="summary-orders" class="font-medium">{{ $stats['total_orders'] }} đơn</span>
            </div>
            <div class="flex justify-between">
                <span>Hiệu suất:</span>
                <span class="font-medium {{ $stats['total_orders'] >= 5 ? 'text-green-600' : ($stats['total_orders'] >= 2 ? 'text-yellow-600' : 'text-red-600') }}">
                    @if($stats['total_orders'] >= 5)
                        Tốt
                    @elseif($stats['total_orders'] >= 2)
                        Trung bình
                    @else
                        Cần cải thiện
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const periodButtons = document.querySelectorAll('#period-buttons button');
    
    periodButtons.forEach(button => {
        button.addEventListener('click', function() {
            const period = this.dataset.period;
            
            // Update URL and reload page with new filter
            const url = new URL(window.location);
            url.searchParams.set('filter', period);
            window.location.href = url.toString();
        });
    });
});
</script>
@endpush
