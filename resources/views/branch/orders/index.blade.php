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

    <!-- Status Tabs (đặt ngoài form) -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav id="orderStatusTabs" class="-mb-px flex space-x-8 overflow-x-auto">
                <a href="{{ route('branch.orders.index') }}" class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ !request('status') || request('status') == 'all' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}" data-status="all">
                    Tất cả ({{ $statusCounts['all'] ?? 0 }})
                </a>
                <a href="{{ route('branch.orders.index', ['status' => 'awaiting_confirmation']) }}" class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'awaiting_confirmation' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}" data-status="awaiting_confirmation">
                    Chờ xác nhận ({{ $statusCounts['awaiting_confirmation'] ?? 0 }})
                </a>
                <a href="{{ route('branch.orders.index', ['status' => 'awaiting_driver']) }}" class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'awaiting_driver' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}" data-status="awaiting_driver">
                    Chờ tài xế ({{ $statusCounts['awaiting_driver'] ?? 0 }})
                </a>
                <a href="{{ route('branch.orders.index', ['status' => 'in_transit']) }}" class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'in_transit' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}" data-status="in_transit">
                    Đang giao ({{ $statusCounts['in_transit'] ?? 0 }})
                </a>
                <a href="{{ route('branch.orders.index', ['status' => 'delivered']) }}" class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'delivered' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}" data-status="delivered">
                    Đã giao ({{ $statusCounts['delivered'] ?? 0 }})
                </a>
                <a href="{{ route('branch.orders.index', ['status' => 'cancelled']) }}" class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'cancelled' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}" data-status="cancelled">
                    Đã hủy ({{ $statusCounts['cancelled'] ?? 0 }})
                </a>
                <a href="{{ route('branch.orders.index', ['status' => 'refunded']) }}" class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'refunded' ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}" data-status="refunded">
                    Đã hoàn tiền ({{ $statusCounts['refunded'] ?? 0 }})
                </a>
            </nav>
        </div>
    </div>

    <!-- Orders Grid + Pagination -->
    <div id="ordersGrid" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse($orders as $order)
            <div class="order-card bg-white rounded-lg shadow-sm border border-gray-200 h-full flex flex-col relative pb-16" data-order-id="{{ $order->id }}">
                <div class="p-2 flex flex-col h-full pb-2">
                    <div class="flex items-start gap-3 mb-2">
                        <input type="checkbox" class="order-checkbox mt-1 rounded" data-order-id="{{ $order->id }}">
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-semibold text-lg text-gray-900">#{{ $order->order_code ?? $order->id }}</h3>
                                    @if($order->points_earned > 0)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            +{{ $order->points_earned }} điểm
                                        </span>
                                    @endif
                                </div>
                                <span class="status-badge {{ $order->statusColor }} text-white rounded-lg px-2">{{ $order->statusText }}</span>
                            </div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-gray-900 font-medium">{{ $order->customerName }}</span>
                                <span class="text-gray-500 text-xs">{{ $order->customerPhone }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 my-2"></div>
                    <div class="flex flex-col gap-1 text-sm flex-1">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tổng tiền:</span>
                            <span class="font-semibold text-gray-900">{{ number_format($order->total_amount) }}₫</span>
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
                            @php $pm = strtolower($order->payment?->paymentMethod?->name ?? ''); @endphp
                            @if($pm === 'cod' || $pm === 'ship cod')
                                <span class="inline-block px-2 py-1 rounded bg-green-500 text-white text-xs font-semibold">COD</span>
                            @elseif($pm === 'vnpay')
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-blue-100 text-blue-800 text-xs font-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 16" style="height:1em;width:auto;display:inline;vertical-align:middle;" aria-label="VNPAY Icon">
                                        <text x="0" y="12" font-size="12" font-family="Arial, Helvetica, sans-serif" font-weight="bold" fill="#e30613">VN</text>
                                        <text x="18" y="12" font-size="12" font-family="Arial, Helvetica, sans-serif" font-weight="bold" fill="#0072bc">PAY</text>
                                    </svg>
                                </span>
                            @else
                                <span class="text-gray-700">{{ $order->payment?->paymentMethod?->name ?? 'Chưa thanh toán' }}</span>
                            @endif
                        </div>
                        @if($order->notes)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Note:</span>
                            <span class="text-xs font-medium text-blue-700 bg-blue-50 rounded px-2 py-1 break-words" style="max-height:2rem;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;white-space:normal;">
                                {{ $order->notes }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="absolute left-0 bottom-0 w-full px-4 pb-3">
                    <div class="flex gap-2 items-end">
                        @if($order->status == 'awaiting_confirmation')
                            <button data-quick-action="confirm" data-order-id="{{ $order->id }}" class="px-3 py-2 text-sm rounded-md bg-black text-white hover:bg-gray-800">
                                Xác nhận
                            </button>
                            <button data-quick-action="cancel" data-order-id="{{ $order->id }}" class="px-3 py-2 text-sm rounded-md bg-red-500 text-white hover:bg-red-600">
                                Hủy
                            </button>
                        @endif
                        <a href="{{ route('branch.orders.show', $order->id) }}" class="flex-1">
                            <button class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Chi tiết
                            </button>
                        </a>
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
    <div id="ordersPagination">
        @if($orders->hasPages())
            <div class="mt-6">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- Bulk Actions Bar -->
    <div id="bulkActionsBar" class="bulk-actions-bar mt-4">
        <div class="flex items-center gap-4">
            <span id="selectedCount" class="font-medium">0 đơn đã chọn</span>
            <button id="bulkConfirmBtn" class="px-3 py-1 bg-white text-blue-600 rounded text-sm font-medium hover:bg-gray-100">
                Xác nhận tất cả
            </button>
            <button id="bulkPrintBtn" class="px-3 py-1 bg-white text-blue-600 rounded text-sm font-medium hover:bg-gray-100">
                In tất cả
            </button>
            <button id="bulkCancelBtn" class="px-3 py-1 bg-red-500 text-white rounded text-sm font-medium hover:bg-red-600">
                Hủy tất cả
            </button>
            <button id="closeBulkActions" class="px-2 py-1 text-white hover:bg-blue-700 rounded">
                Đóng
            </button>
        </div>
    </div>
</div>

@endsection

@section('vendor-script')
<script src="{{ asset('vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
@endsection

<script>
window.branchId = @json(Auth::guard('manager')->user()->branch->id);
window.pusherKey = @json(config('broadcasting.connections.pusher.key'));
window.pusherCluster = @json(config('broadcasting.connections.pusher.options.cluster'));
</script>
@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('#orderStatusTabs .status-tab');
    const ordersGrid = document.getElementById('ordersGrid');
    const ordersPagination = document.getElementById('ordersPagination');
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Tab clicked:', tab.getAttribute('data-status'), tab.getAttribute('href'));
            tabs.forEach(t => t.classList.remove('active', 'border-blue-500', 'text-blue-600'));
            tab.classList.add('active', 'border-blue-500', 'text-blue-600');
            const url = tab.getAttribute('href');
            history.replaceState(null, '', url);
            fetchOrdersByUrl(url);
        });
    });
    function fetchOrdersByUrl(url) {
        console.log('Fetching orders by URL:', url);
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.text())
        .then(html => {
            console.log('Fetched HTML:', html.substring(0, 200));
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newGrid = doc.getElementById('ordersGrid');
            const newPagination = doc.getElementById('ordersPagination');
            if (newGrid) {
                ordersGrid.innerHTML = newGrid.innerHTML;
                console.log('Updated ordersGrid');
            }
            if (newPagination) {
                ordersPagination.innerHTML = newPagination.innerHTML;
                console.log('Updated ordersPagination');
            }
        })
        .catch(err => {
            console.error('AJAX error:', err);
        });
    }
});
</script>
<script src="{{ asset('js/branch/orders-realtime.js') }}"></script>
@endsection