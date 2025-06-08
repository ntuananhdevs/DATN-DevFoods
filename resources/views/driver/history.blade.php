@extends('layouts.driver.masterLayout')

@section('title', 'Lịch sử giao hàng - Ứng dụng Tài xế')

@section('content')
<div class="p-4 md:p-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Lịch sử giao hàng</h1>

    <!-- Summary Stats -->
    <div class="grid md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
            <h3 class="text-3xl font-bold text-green-600 mb-2">{{ number_format($summary['total_earnings']) }}đ</h3>
            <p class="text-gray-600">Tổng thu nhập</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-3xl font-bold text-blue-600 mb-2">{{ $summary['total_orders'] }}</h3>
            <p class="text-gray-600">Tổng số đơn</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
            </div>
            <h3 class="text-3xl font-bold text-yellow-600 mb-2">{{ $summary['average_rating'] }}</h3>
            <p class="text-gray-600">Đánh giá trung bình</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="filterPeriod" class="block text-sm font-medium text-gray-700 mb-2">Lọc theo thời gian</label>
                <select id="filterPeriod" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">Toàn bộ</option>
                    <option value="today">Hôm nay</option>
                    <option value="week">Tuần này</option>
                    <option value="month">Tháng này</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label for="searchHistory" class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" id="searchHistory" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Tìm theo mã đơn, tên khách hàng..." data-search="history">
                </div>
            </div>
            <div>
                <button onclick="applyFilter()" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                    </svg>
                    Lọc
                </button>
            </div>
        </div>
    </div>

    <!-- History List -->
    <div id="historyList">
        @if(count($deliveryHistory) > 0)
            <div class="space-y-4">
                @foreach($deliveryHistory as $entry)
                <div class="bg-white rounded-lg shadow-md card-hover" data-searchable="history">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-gray-900">Đơn hàng: {{ $entry['id'] }}</h4>
                                <p class="text-sm text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $entry['order_time'] }}
                                </p>
                            </div>
                            <span class="text-lg font-bold text-green-600">+{{ number_format($entry['driver_earnings']) }}đ</span>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <div>
                                    <span class="font-medium text-gray-900">Khách hàng:</span>
                                    <span class="text-gray-700">{{ $entry['customer_name'] }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-900">Địa chỉ giao:</span>
                                    <span class="text-gray-700">{{ $entry['delivery_address'] }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-900">Khoảng cách:</span>
                                    <span class="text-gray-700">{{ $entry['distance'] }} km</span>
                                </div>
                                
                                @if(isset($entry['rating']))
                                <div class="flex items-center">
                                    <span class="font-medium text-gray-900 mr-2">Đánh giá:</span>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $entry['rating'] ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                            </svg>
                                        @endfor
                                        <span class="ml-1 text-gray-600">({{ $entry['rating'] }})</span>
                                    </div>
                                </div>
                                @endif
                                
                                @if(isset($entry['customer_feedback']))
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 mr-2 text-gray-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        <em class="text-gray-700">"{{ $entry['customer_feedback'] }}"</em>
                                    </div>
                                </div>
                                @endif
                            </div>
                            
                            <div class="text-right space-y-2">
                                <div>
                                    <p class="text-sm text-gray-600">Tổng tiền hàng</p>
                                    <p class="font-bold text-gray-900">{{ number_format($entry['total_amount']) }}đ</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Phí ship</p>
                                    <p class="font-bold text-green-600">{{ number_format($entry['shipping_fee']) }}đ</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-6 py-3 border-t border-gray-200">
                        <a href="{{ route('driver.orders.detail', $entry['id']) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Xem chi tiết đơn
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-gray-500">Không có lịch sử giao hàng nào.</p>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if(isset($pagination) && $pagination['total_pages'] > 1)
    <div class="mt-6">
        <nav class="flex items-center justify-center space-x-2">
            <a href="?page={{ $pagination['current_page'] - 1 }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 {{ $pagination['current_page'] == 1 ? 'opacity-50 cursor-not-allowed' : '' }}">
                Trước
            </a>
            
            @for($i = 1; $i <= $pagination['total_pages']; $i++)
            <a href="?page={{ $i }}" class="px-3 py-2 text-sm font-medium {{ $pagination['current_page'] == $i ? 'text-blue-600 bg-blue-50 border-blue-500' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50' }} border rounded-lg">
                {{ $i }}
            </a>
            @endfor
            
            <a href="?page={{ $pagination['current_page'] + 1 }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 {{ $pagination['current_page'] == $pagination['total_pages'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                Sau
            </a>
        </nav>
    </div>
    @endif
</div>

@push('scripts')
<script>
function applyFilter() {
    const period = document.getElementById('filterPeriod').value;
    const search = document.getElementById('searchHistory').value;
    
    // In real app, make AJAX call or redirect with parameters
    let url = new URL(window.location);
    url.searchParams.set('period', period);
    if (search) {
        url.searchParams.set('search', search);
    } else {
        url.searchParams.delete('search');
    }
    window.location.href = url.toString();
}

document.getElementById('searchHistory').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilter();
    }
});
</script>
@endpush
@endsection
