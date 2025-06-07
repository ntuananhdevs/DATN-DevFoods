@extends('layout.master')

@section('title', 'Đơn hàng - Ứng dụng Tài xế')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Đơn hàng</h1>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 form-input" placeholder="Tìm kiếm đơn hàng (mã, tên, địa chỉ)..." data-search="orders">
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button data-tab="pending" class="tab-active py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                    Chờ nhận
                    <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs">{{ count($orders['pending']) }}</span>
                </button>
                <button data-tab="delivering" class="tab-inactive py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                    Đang giao
                    <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs">{{ count($orders['delivering']) }}</span>
                </button>
                <button data-tab="completed" class="tab-inactive py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                    Đã hoàn thành
                    <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs">{{ count($orders['completed']) }}</span>
                </button>
                <button data-tab="cancelled" class="tab-inactive py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                    Đã hủy
                    <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs">{{ count($orders['cancelled']) }}</span>
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        @foreach(['pending' => 'Chờ nhận', 'delivering' => 'Đang giao', 'completed' => 'Đã hoàn thành', 'cancelled' => 'Đã hủy'] as $status => $statusLabel)
        <div data-tab-content="{{ $status }}" class="p-6 {{ $status !== 'pending' ? 'hidden' : '' }}">
            @if(count($orders[$status]) > 0)
                <div class="space-y-4">
                    @foreach($orders[$status] as $order)
                        <div class="border border-gray-200 rounded-lg p-4 card-hover" data-searchable="orders">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-semibold text-gray-900">Mã đơn: {{ $order['id'] }}</h4>
                                    <p class="text-sm text-gray-500 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $order['order_time'] }}
                                    </p>
                                </div>
                                <span class="status-{{ $status }} px-2 py-1 rounded-full text-xs font-medium">{{ $order['status'] }}</span>
                            </div>
                            
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <strong>Lấy hàng:</strong>&nbsp;{{ $order['pickup_branch'] }}
                                </div>
                                <div class="flex items-start">
                                    <svg class="w-4 h-4 mr-2 mt-0.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <div>
                                        <strong>Giao đến:</strong>&nbsp;{{ $order['delivery_address'] }}
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    {{ $order['customer_name'] }} - {{ $order['customer_phone'] }}
                                </div>
                            </div>
                            
                            <div class="border-t pt-3 mt-3">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-green-600">Phí ship: {{ number_format($order['shipping_fee']) }}đ</span>
                                    <span class="text-sm text-gray-500">{{ $order['distance'] }} km</span>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <a href="{{ route('driver.orders.detail', $order['id']) }}" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                                    Xem chi tiết
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-gray-500">Không có đơn hàng nào.</p>
                </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endsection

