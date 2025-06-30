@extends('layouts.branch.contentLayoutMaster')

@section('title', 'Quản lý đơn hàng')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
@endsection

@section('page-style')
<style>
.order-card {
    transition: all 0.3s ease;
}
.order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}
.bulk-actions-bar {
    position: fixed;
    bottom: 1rem;
    left: 50%;
    transform: translateX(-50%);
    z-index: 50;
    background: #3b82f6;
    color: white;
    padding: 1rem;
    border-radius: 0.5rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    display: none;
}
.tooltip {
    position: relative;
    cursor: help;
}
.tooltip .tooltip-content {
    visibility: hidden;
    opacity: 0;
    position: absolute;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    background: #1f2937;
    color: white;
    padding: 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    white-space: nowrap;
    transition: all 0.3s;
    z-index: 1000;
    min-width: 200px;
}
.tooltip:hover .tooltip-content {
    visibility: visible;
    opacity: 1;
}
.status-tab.active {
    border-bottom-color: #3b82f6 !important;
    color: #3b82f6 !important;
}
.status-tab {
    border-bottom-color: transparent;
    color: #6b7280;
    cursor: pointer;
}
.status-tab:hover {
    color: #3b82f6;
}
</style>
@endsection

@section('content')
<div class="mx-auto p-4">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold mb-2 text-gray-900">Quản lý đơn hàng</h1>
            <p class="text-gray-600">Theo dõi và xử lý đơn hàng của chi nhánh</p>
        </div>
        <div class="flex items-center gap-2 mt-4 md:mt-0">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-12"></path>
            </svg>
            <span class="text-sm text-gray-500">Thông báo tự động</span>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-4">
            <form id="filterForm" method="GET" action="{{ route('branch.orders.index') }}">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input
                                    type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Tìm theo mã đơn, tên khách hàng hoặc số điện thoại..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                        </div>
                        <div class="flex gap-2 flex-wrap">
                            <button type="button" id="dateRangeBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Thời gian
                            </button>

                            <select name="payment_method" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                                <option value="all">Tất cả thanh toán</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method->name }}" {{ request('payment_method') == $method->name ? 'selected' : '' }}>
                                        {{ $method->name }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="sort" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                                <option value="order_date-desc" {{ request('sort') == 'order_date-desc' ? 'selected' : '' }}>Mới nhất</option>
                                <option value="order_date-asc" {{ request('sort') == 'order_date-asc' ? 'selected' : '' }}>Cũ nhất</option>
                                <option value="total_amount-desc" {{ request('sort') == 'total_amount-desc' ? 'selected' : '' }}>Giá cao</option>
                                <option value="total_amount-asc" {{ request('sort') == 'total_amount-asc' ? 'selected' : '' }}>Giá thấp</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <div class="flex gap-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Lọc
                            </button>
                            <a href="{{ route('branch.orders.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Làm mới
                            </a>
                            <button type="button" id="exportBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Xuất báo cáo
                            </button>
                        </div>

                        @if(request('search') || request('status') || request('date_from') || request('date_to') || request('payment_method'))
                            <a href="{{ route('branch.orders.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">
                                Xóa bộ lọc
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 overflow-x-auto">
                <a href="{{ route('branch.orders.index', array_merge(request()->query(), ['status' => 'all'])) }}" 
                   class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status', 'all') == 'all' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}">
                    Tất cả (<span>{{ $statusCounts['all'] }}</span>)
                </a>
                <a href="{{ route('branch.orders.index', array_merge(request()->query(), ['status' => 'pending'])) }}" 
                   class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'pending' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Chờ (<span>{{ $statusCounts['pending'] }}</span>)
                </a>
                <a href="{{ route('branch.orders.index', array_merge(request()->query(), ['status' => 'processing'])) }}" 
                   class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'processing' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Chuẩn bị (<span>{{ $statusCounts['processing'] }}</span>)
                </a>
                <a href="{{ route('branch.orders.index', array_merge(request()->query(), ['status' => 'ready'])) }}" 
                   class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'ready' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Sẵn sàng (<span>{{ $statusCounts['ready'] }}</span>)
                </a>
                <a href="{{ route('branch.orders.index', array_merge(request()->query(), ['status' => 'delivery'])) }}" 
                   class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'delivery' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Giao (<span>{{ $statusCounts['delivery'] }}</span>)
                </a>
                <a href="{{ route('branch.orders.index', array_merge(request()->query(), ['status' => 'completed'])) }}" 
                   class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'completed' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Hoàn thành (<span>{{ $statusCounts['completed'] }}</span>)
                </a>
                <a href="{{ route('branch.orders.index', array_merge(request()->query(), ['status' => 'cancelled'])) }}" 
                   class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'cancelled' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Hủy (<span>{{ $statusCounts['cancelled'] }}</span>)
                </a>
            </nav>
        </div>
    </div>

    <!-- Orders Grid -->
    <div id="ordersGrid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($orders as $order)
            <div class="order-card bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-4">
                    <div class="flex items-start gap-3 mb-3">
                        <input type="checkbox" class="order-checkbox mt-1 rounded" data-order-id="{{ $order->id }}">
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-semibold text-lg text-gray-900">#{{ $order->order_code ?? $order->id }}</h3>
                                    @if($order->points_earned > 0)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"></path>
                                            </svg>
                                            +{{ $order->points_earned }} điểm
                                        </span>
                                    @endif
                                </div>
                                <span class="status-badge {{ $order->statusColor }} text-white rounded-lg px-1">{{ $order->statusText }}</span>
                            </div>

                            <div class="flex items-center gap-2 mb-2">
                                <div class="tooltip flex items-center gap-1 cursor-help">
                                    <span class="text-sm font-medium text-gray-900">{{ $order->customerName }}</span>
                                    @if($order->customer)
                                        <svg class="w-3 h-3 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732L14.146 12.8l-1.179 4.456a1 1 0 01-1.934 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732L9.854 7.2l1.179-4.456A1 1 0 0112 2z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                    <div class="tooltip-content">
                                        <div class="text-xs space-y-1">
                                            <p>📞 {{ $order->customerPhone }}</p>
                                            @if($order->customer)
                                                <p>📦 Tổng đơn: {{ $order->customer->orders()->count() }}</p>
                                                <p>📅 Đơn gần nhất: {{ $order->customer->orders()->latest()->first()?->order_date?->format('Y-m-d') ?? 'N/A' }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2 mb-4 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Tổng tiền:</span>
                                    <span class="font-medium text-gray-900">{{ number_format($order->total_amount) }}₫</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Thời gian:</span>
                                    <span class="text-gray-700">{{ $order->order_date->format('d/m/Y H:i') }}</span>
                                </div>
                                @if($order->estimated_delivery_time)
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Dự kiến giao:</span>
                                        <span class="font-medium text-green-600">{{ $order->estimated_delivery_time->diffForHumans() }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Thanh toán:</span>
                                    <span class="text-gray-700">{{ $order->payment?->paymentMethod?->name ?? 'Chưa thanh toán' }}</span>
                                </div>
                                @if($order->notes)
                                    <div class="flex items-start gap-1">
                                        <svg class="w-3 h-3 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        <span class="text-xs text-gray-500 line-clamp-2">{{ $order->notes }}</span>
                                    </div>
                                @endif
                            </div>

                            @if(!in_array($order->status, ['completed', 'cancelled']))
                                <div class="flex gap-2 mb-3">
                                    @if($order->status == 'pending')
                                        <button onclick="handleQuickAction({{ $order->id }}, 'confirm')" class="px-2 py-1 text-xs rounded bg-blue-500 text-white hover:bg-blue-600">
                                            ✅ Xác nhận
                                        </button>
                                        <button onclick="handleQuickAction({{ $order->id }}, 'cancel')" class="px-2 py-1 text-xs rounded bg-red-500 text-white hover:bg-red-600">
                                            ❌ Hủy
                                        </button>
                                    @elseif($order->status == 'processing')
                                        <button onclick="handleQuickAction({{ $order->id }}, 'ready')" class="px-2 py-1 text-xs rounded bg-blue-500 text-white hover:bg-blue-600">
                                            ✅ Sẵn sàng
                                        </button>
                                    @elseif($order->status == 'ready')
                                        <button onclick="handleQuickAction({{ $order->id }}, 'deliver')" class="px-2 py-1 text-xs rounded bg-blue-500 text-white hover:bg-blue-600">
                                            🚚 Giao hàng
                                        </button>
                                    @elseif($order->status == 'delivery')
                                        <button onclick="handleQuickAction({{ $order->id }}, 'complete')" class="px-2 py-1 text-xs rounded bg-blue-500 text-white hover:bg-blue-600">
                                            ✅ Hoàn thành
                                        </button>
                                    @endif
                                </div>
                            @endif

                            <div class="flex gap-2">
                                <a href="{{ route('branch.orders.show', $order->id) }}" class="flex-1">
                                    <button class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Chi tiết
                                    </button>
                                </a>
                                <a href="tel:{{ $order->customerPhone }}" class="px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-8">
                    <div class="p-8 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <h3 class="text-lg font-medium mb-2 text-gray-900">Không có đơn hàng</h3>
                        <p class="text-gray-500">Không tìm thấy đơn hàng phù hợp với bộ lọc hiện tại</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($orders->hasPages())
        <div class="mt-6">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    @endif

    <!-- Bulk Actions Bar -->
    <div id="bulkActionsBar" class="bulk-actions-bar">
        <div class="flex items-center gap-4">
            <span id="selectedCount" class="font-medium">0 đơn đã chọn</span>
            <button id="bulkConfirmBtn" class="px-3 py-1 bg-white text-blue-600 rounded text-sm font-medium hover:bg-gray-100">
                ✅ Xác nhận tất cả
            </button>
            <button id="bulkPrintBtn" class="px-3 py-1 bg-white text-blue-600 rounded text-sm font-medium hover:bg-gray-100">
                🖨️ In tất cả
            </button>
            <button id="bulkCancelBtn" class="px-3 py-1 bg-red-500 text-white rounded text-sm font-medium hover:bg-red-600">
                ❌ Hủy tất cả
            </button>
            <button id="closeBulkActions" class="px-2 py-1 text-white hover:bg-blue-700 rounded">
                ✕
            </button>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 z-50 hidden">
    <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-4 max-w-sm">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg id="toastIcon" class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div class="ml-3 w-0 flex-1">
                <p id="toastTitle" class="text-sm font-medium text-gray-900"></p>
                <p id="toastMessage" class="mt-1 text-sm text-gray-500"></p>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
                <button id="closeToast" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="{{ asset('vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
@endsection

@section('page-script')
<script>
// Global variables
let selectedOrders = [];

// Utility functions
function showToast(title, message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastIcon = document.getElementById('toastIcon');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');

    toastTitle.textContent = title;
    toastMessage.textContent = message;

    // Set icon based on type
    if (type === 'success') {
        toastIcon.className = 'w-5 h-5 text-green-500';
        toastIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
    } else if (type === 'error') {
        toastIcon.className = 'w-5 h-5 text-red-500';
        toastIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
    }

    toast.classList.remove('hidden');
    setTimeout(() => {
        toast.classList.add('hidden');
    }, 5000);
}

// Quick action handler
function handleQuickAction(orderId, action) {
    const statusMap = {
        'confirm': 'processing',
        'ready': 'ready',
        'deliver': 'delivery',
        'complete': 'completed',
        'cancel': 'cancelled'
    };

    const newStatus = statusMap[action];
    if (!newStatus) return;

    // Send AJAX request
    fetch(`/branch/orders/${orderId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status: newStatus,
            note: `Thay đổi trạng thái từ ${action}`
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('✅ Thành công', data.message);
            // Reload page to reflect changes
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast('❌ Lỗi', data.message || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('❌ Lỗi', 'Có lỗi xảy ra khi cập nhật trạng thái', 'error');
    });
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to checkboxes
    document.querySelectorAll('.order-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const orderId = this.dataset.orderId;
            const isChecked = this.checked;

            if (isChecked) {
                if (!selectedOrders.includes(orderId)) {
                    selectedOrders.push(orderId);
                }
            } else {
                selectedOrders = selectedOrders.filter(id => id !== orderId);
            }

            updateBulkActionsBar();
        });
    });

    // Toast close button
    document.getElementById('closeToast').addEventListener('click', function() {
        document.getElementById('toast').classList.add('hidden');
    });

    // Bulk actions
    document.getElementById('closeBulkActions').addEventListener('click', function() {
        selectedOrders = [];
        document.querySelectorAll('.order-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        updateBulkActionsBar();
    });

    // Export button
    document.getElementById('exportBtn').addEventListener('click', function() {
        showToast('📊 Xuất báo cáo thành công', 'File CSV đã được tải xuống');
    });
});

function updateBulkActionsBar() {
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    const selectedCount = document.getElementById('selectedCount');

    if (selectedOrders.length > 0) {
        bulkActionsBar.style.display = 'block';
        selectedCount.textContent = `${selectedOrders.length} đơn đã chọn`;
    } else {
        bulkActionsBar.style.display = 'none';
    }
}

// Simulate new order notifications
setInterval(() => {
    if (Math.random() < 0.05) { // 5% chance every 30 seconds
        showToast('🔔 Đơn hàng mới!', 'Có đơn hàng mới cần xử lý');
    }
}, 30000);
</script>
@endsection