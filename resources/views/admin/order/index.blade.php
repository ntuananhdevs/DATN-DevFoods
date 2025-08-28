@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Danh sách đơn hàng')
@section('description', 'Quản lý danh sách đơn hàng của bạn')

@section('content')
<style>
    /* Badge style giống product index */
    .order-status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1.25rem;
        transition: all 0.2s ease;
    }
    .order-status-badge.awaiting_confirmation {
        background-color: #fef3c7;
        color: #d97706;
    }
    .order-status-badge.order_confirmed {
        background-color: #dbeafe;
        color: #2563eb;
    }
    .order-status-badge.in_transit {
        background-color: #ede9fe;
        color: #7c3aed;
    }
    .order-status-badge.delivered {
        background-color: #dcfce7;
        color: #15803d;
    }
    .order-status-badge.cancelled {
        background-color: #fee2e2;
        color: #dc2626;
    }
    .order-status-badge.refunded {
        background-color: #e0e7ef;
        color: #334155;
    }
    
    /* Payment status badge styles */
    .payment-status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1.25rem;
        transition: all 0.2s ease;
    }
    .payment-status-badge.pending {
        background-color: #fef3c7;
        color: #d97706;
    }
    .payment-status-badge.completed {
        background-color: #dcfce7;
        color: #15803d;
    }
    .payment-status-badge.failed {
        background-color: #fee2e2;
        color: #dc2626;
    }
    .payment-status-badge.refunded {
        background-color: #e0e7ef;
        color: #334155;
    }
    
    /* Status tab style từ branch */
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
    
    /* Animation for real-time updates */
    @keyframes pulse {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
        }
        50% {
            transform: scale(1.05);
            box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
        }
    }
    
    .pulse-animation {
        animation: pulse 2s infinite;
    }
</style>
<div class="flex flex-col gap-4 pb-4">
    <!-- Main Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text">
                    <path d="M4 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h7l7 7v13a2 2 0 0 1-2 2Z"></path>
                    <polyline points="14 3 14 8 19 8"></polyline>
                </svg>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Quản lý đơn hàng</h2>
                <p class="text-muted-foreground">Quản lý danh sách đơn hàng của bạn</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button class="btn btn-outline flex items-center" onclick="window.location.href='{{ route('admin.orders.export') }}'">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Xuất báo cáo
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border rounded-lg overflow-hidden">
        <div class="p-6 border-b">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium">Bộ lọc tìm kiếm</h3>
                <button type="button" id="toggle-advanced-filter" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                    <span>Bộ lọc nâng cao</span>
                    <svg class="ml-1 w-4 h-4 transition-transform" id="filter-arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>
            
            <form id="filter-form" method="GET" class="space-y-4">
                <!-- Basic Filters Row -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mã đơn hàng</label>
                        <input type="text" id="order_code" name="order_code" value="{{ request('order_code') }}" 
                               placeholder="Nhập mã đơn hàng..." 
                               class="w-full border border-gray-200 rounded-xl px-4 py-2 text-[15px] bg-white text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition outline-none" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Khách hàng</label>
                        <input type="text" id="customer_name" name="customer_name" value="{{ request('customer_name') }}" 
                               placeholder="Tên, email hoặc SĐT..." 
                               class="w-full border border-gray-200 rounded-xl px-4 py-2 text-[15px] bg-white text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition outline-none" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chi nhánh</label>
                        <select id="branch_id" name="branch_id" class="w-full rounded-xl border border-gray-200 px-4 py-2 text-[15px] bg-white text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition outline-none">
                            <option value="">Tất cả chi nhánh</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Tìm kiếm
                        </button>
                        <button type="button" id="reset-filter" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset
                        </button>
                    </div>
                </div>

                <!-- Advanced Filters -->
                <div id="advanced-filters" class="hidden border-t pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Date Range -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Khoảng thời gian</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <input type="text" id="date_from" name="date_from" value="{{ request('date_from') }}" 
                                           placeholder="Từ ngày (dd/mm/yyyy)" 
                                           class="w-full border border-gray-200 rounded-xl px-4 py-2 text-[15px] bg-white text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition outline-none" />
                                </div>
                                <div>
                                    <input type="text" id="date_to" name="date_to" value="{{ request('date_to') }}" 
                                           placeholder="Đến ngày (dd/mm/yyyy)" 
                                           class="w-full border border-gray-200 rounded-xl px-4 py-2 text-[15px] bg-white text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition outline-none" />
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Method -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phương thức thanh toán</label>
                            <select id="payment_method" name="payment_method" class="w-full rounded-xl border border-gray-200 px-4 py-2 text-[15px] bg-white text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition outline-none">
                                <option value="">Tất cả phương thức</option>
                                <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Tiền mặt</option>
                                <option value="vnpay" {{ request('payment_method') == 'vnpay' ? 'selected' : '' }}>VNPay</option>
                                <option value="momo" {{ request('payment_method') == 'momo' ? 'selected' : '' }}>MoMo</option>
                                <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Chuyển khoản</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <!-- Total Amount Range -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Khoảng giá trị đơn hàng (VNĐ)</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <input type="number" id="total_from" name="total_from" value="{{ request('total_from') }}" 
                                           placeholder="Từ" min="0" step="1000"
                                           class="w-full border border-gray-200 rounded-xl px-4 py-2 text-[15px] bg-white text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition outline-none" />
                                </div>
                                <div>
                                    <input type="number" id="total_to" name="total_to" value="{{ request('total_to') }}" 
                                           placeholder="Đến" min="0" step="1000"
                                           class="w-full border border-gray-200 rounded-xl px-4 py-2 text-[15px] bg-white text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition outline-none" />
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sort Options -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sắp xếp theo</label>
                            <div class="flex gap-2">
                                <select id="sort_by" name="sort_by" class="flex-1 rounded-xl border border-gray-200 px-4 py-2 text-[15px] bg-white text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition outline-none">
                                    <option value="created_at" {{ request('sort_by', 'created_at') == 'created_at' ? 'selected' : '' }}>Thời gian</option>
                                    <option value="total_amount" {{ request('sort_by') == 'total_amount' ? 'selected' : '' }}>Giá trị</option>
                                    <option value="order_code" {{ request('sort_by') == 'order_code' ? 'selected' : '' }}>Mã đơn</option>
                                </select>
                                <select id="sort_order" name="sort_order" class="rounded-xl border border-gray-200 px-4 py-2 text-[15px] bg-white text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition outline-none">
                                    <option value="desc" {{ request('sort_order', 'desc') == 'desc' ? 'selected' : '' }}>Giảm dần</option>
                                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Tăng dần</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Status Tabs (căn giữa) -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex  space-x-8 overflow-x-auto">
                @php
                    $tabs = [
                        ['label' => 'Tất cả', 'key' => '', 'count' => $counts['all'] ?? 0],
                        ['label' => 'Chờ xác nhận', 'key' => 'awaiting_confirmation', 'count' => $counts['awaiting_confirmation'] ?? 0],
                        ['label' => 'Đã xác nhận', 'key' => 'confirmed', 'count' => $counts['confirmed'] ?? 0],
                        ['label' => 'Chờ tài xế', 'key' => 'awaiting_driver', 'count' => $counts['awaiting_driver'] ?? 0],
                        ['label' => 'Tài xế đã xác nhận', 'key' => 'driver_confirmed', 'count' => $counts['driver_confirmed'] ?? 0],
                        ['label' => 'Chờ tài xế lấy hàng', 'key' => 'waiting_driver_pick_up', 'count' => $counts['waiting_driver_pick_up'] ?? 0],
                        ['label' => 'Tài xế đã lấy hàng', 'key' => 'driver_picked_up', 'count' => $counts['driver_picked_up'] ?? 0],
                        ['label' => 'Đang giao', 'key' => 'in_transit', 'count' => $counts['in_transit'] ?? 0],
                        ['label' => 'Đã giao', 'key' => 'delivered', 'count' => $counts['delivered'] ?? 0],
                        ['label' => 'Đã nhận hàng', 'key' => 'item_received', 'count' => $counts['item_received'] ?? 0],
                        ['label' => 'Đã hủy', 'key' => 'cancelled', 'count' => $counts['cancelled'] ?? 0],
                        ['label' => 'Đã hoàn tiền', 'key' => 'refunded', 'count' => $counts['refunded'] ?? 0],
                    ];
                @endphp
                @foreach($tabs as $tab)
                    <a href="#" data-status="{{ $tab['key'] }}"
                       class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ (request('status') == $tab['key'] || (!request('status') && $tab['key'] === '')) ? 'active border-blue-500 text-blue-600' : 'border-transparent text-gray-500' }}">
                        {{ $tab['label'] }} (<span class="tab-count">{{ $tab['count'] }}</span>)
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border rounded-lg overflow-hidden">
        <!-- Table header -->
        <div class="p-6 border-b">
            <h3 class="text-lg font-medium">Danh sách đơn hàng</h3>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto" id="orders-table-container">
            <div id="loading-spinner" class="hidden flex justify-center items-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>
            <table class="w-full" id="orders-table">
                <thead>
                    <tr class="border-b bg-muted/50">
                        <th class="py-3 px-4 text-left font-medium">Mã đơn hàng</th>
                        <th class="py-3 px-4 text-left font-medium">Khách hàng</th>
                        <th class="py-3 px-4 text-left font-medium">Chi nhánh</th>
                        <th class="py-3 px-4 text-right font-medium">Tổng tiền</th>
                        <th class="py-3 px-4 text-left font-medium">Thanh toán</th>
                        <th class="py-3 px-4 text-left font-medium">TT Thanh toán</th>
                        <th class="py-3 px-4 text-left font-medium">Trạng thái</th>
                        <th class="py-3 px-4 text-left font-medium">Thời gian</th>
                        <th class="py-3 px-4 text-center font-medium">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="orders-tbody">
                    @forelse($orders as $order)
                        @include('admin.order._order_row', ['order' => $order])
                    @empty
                    <tr id="empty-state">
                        <td colspan="9" class="text-center py-8">
                            <div class="flex flex-col items-center justify-center text-muted-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2">
                                    <path d="M6 2L3 6v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                                    <path d="M3 6h18"></path>
                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                </svg>
                                <h3 class="text-lg font-medium">Không có đơn hàng nào</h3>
                                <p class="text-sm">Hãy thêm đơn hàng mới để bắt đầu</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-container flex items-center justify-between px-4 py-4 border-t">
            <div class="text-sm text-muted-foreground">
                Hiển thị <span id="paginationStart">{{ $orders->firstItem() }}</span> đến <span id="paginationEnd">{{ $orders->lastItem() }}</span> của <span id="paginationTotal">{{ $orders->total() }}</span> mục
            </div>
            <div class="flex items-center justify-end space-x-2 ml-auto" id="paginationControls">
                {{ $orders->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Pusher for real-time updates -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    window.pusherKey = '{{ config('broadcasting.connections.pusher.key') }}';
    window.pusherCluster = '{{ config('broadcasting.connections.pusher.options.cluster') }}';
</script>
<script src="{{ asset('js/admin/orders-realtime.js') }}"></script>
<script>
// AJAX Tab Switching
document.addEventListener('DOMContentLoaded', function() {
    const statusTabs = document.querySelectorAll('.status-tab');
    const ordersTableContainer = document.getElementById('orders-table-container');
    const loadingSpinner = document.getElementById('loading-spinner');
    const ordersTable = document.getElementById('orders-table');
    const ordersTbody = document.getElementById('orders-tbody');
    const paginationContainer = document.querySelector('.pagination-container');
    
    // Advanced filter toggle
    const toggleAdvancedFilter = document.getElementById('toggle-advanced-filter');
    const advancedFilters = document.getElementById('advanced-filters');
    const filterArrow = document.getElementById('filter-arrow');
    
    // Check if advanced filters should be shown (if any advanced filter has value)
    const hasAdvancedFilters = {{ 
        request('customer_name') || request('date_from') || request('date_to') || 
        request('payment_method') || request('total_from') || request('total_to') || 
        request('sort_by') || request('sort_order') ? 'true' : 'false' 
    }};
    
    if (hasAdvancedFilters) {
        advancedFilters.classList.remove('hidden');
        filterArrow.style.transform = 'rotate(180deg)';
    }
    
    toggleAdvancedFilter.addEventListener('click', function() {
        advancedFilters.classList.toggle('hidden');
        if (advancedFilters.classList.contains('hidden')) {
            filterArrow.style.transform = 'rotate(0deg)';
        } else {
            filterArrow.style.transform = 'rotate(180deg)';
        }
    });
    
    // Reset filter functionality
    const resetFilterBtn = document.getElementById('reset-filter');
    resetFilterBtn.addEventListener('click', function() {
        // Clear all form inputs
        document.getElementById('filter-form').reset();
        
        // Clear current filters
        currentFilters = {
            order_code: '',
            customer_name: '',
            branch_id: '',
            date_from: '',
            date_to: '',
            payment_method: '',
            total_from: '',
            total_to: '',
            sort_by: '',
            sort_order: ''
        };
        
        // Get current active tab
        const activeTab = document.querySelector('.status-tab.active');
        const currentStatus = activeTab ? activeTab.getAttribute('data-status') : '';
        
        // Load orders without filters via AJAX
        loadOrders(currentStatus, 1);
        
        // Update URL without page reload
        const newUrl = '{{ route("admin.orders.index") }}' + (currentStatus ? '?status=' + currentStatus : '');
        window.history.pushState({}, '', newUrl);
    });
    
    // Current filters
    let currentFilters = {
        order_code: '{{ request("order_code") }}',
        customer_name: '{{ request("customer_name") }}',
        branch_id: '{{ request("branch_id") }}',
        date_from: '{{ request("date_from") }}',
        date_to: '{{ request("date_to") }}',
        payment_method: '{{ request("payment_method") }}',
        total_from: '{{ request("total_from") }}',
        total_to: '{{ request("total_to") }}',
        sort_by: '{{ request("sort_by") }}',
        sort_order: '{{ request("sort_order") }}'
    };

    // Initialize form elements and event listeners
    function initializeFormElements() {
        // Filter form handling
        const filterForm = document.getElementById('filter-form');
        const orderCodeInput = document.getElementById('order_code');
        const customerNameInput = document.getElementById('customer_name');
        const branchIdSelect = document.getElementById('branch_id');
        const dateFromInput = document.getElementById('date_from');
        const dateToInput = document.getElementById('date_to');
        const paymentMethodSelect = document.getElementById('payment_method');
        const totalFromInput = document.getElementById('total_from');
        const totalToInput = document.getElementById('total_to');
        const sortBySelect = document.getElementById('sort_by');
        const sortOrderSelect = document.getElementById('sort_order');
        
        // Date input formatting
        function formatDateInput(input) {
            if (!input) return;
            // Remove existing listeners to avoid duplicates
            input.removeEventListener('input', input._formatHandler);
            input._formatHandler = function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2);
                }
                if (value.length >= 5) {
                    value = value.substring(0, 5) + '/' + value.substring(5, 9);
                }
                e.target.value = value;
            };
            input.addEventListener('input', input._formatHandler);
        }
        
        if (dateFromInput) formatDateInput(dateFromInput);
        if (dateToInput) formatDateInput(dateToInput);
        
        // Number input formatting for total amount
        function formatNumberInput(input) {
            if (!input) return;
            // Remove existing listeners to avoid duplicates
            input.removeEventListener('input', input._formatInputHandler);
            input.removeEventListener('blur', input._formatBlurHandler);
            
            input._formatInputHandler = function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value) {
                    e.target.value = parseInt(value).toLocaleString('vi-VN');
                }
            };
            
            input._formatBlurHandler = function(e) {
                let value = e.target.value.replace(/\D/g, '');
                e.target.value = value;
            };
            
            input.addEventListener('input', input._formatInputHandler);
            input.addEventListener('blur', input._formatBlurHandler);
        }
        
        if (totalFromInput) formatNumberInput(totalFromInput);
        if (totalToInput) formatNumberInput(totalToInput);
        
        if (filterForm) {
            // Remove existing listener to avoid duplicates
            filterForm.removeEventListener('submit', filterForm._submitHandler);
            filterForm._submitHandler = function(e) {
                e.preventDefault();
                
                // Update current filters
                currentFilters.order_code = orderCodeInput ? orderCodeInput.value : '';
                currentFilters.customer_name = customerNameInput ? customerNameInput.value : '';
                currentFilters.branch_id = branchIdSelect ? branchIdSelect.value : '';
                currentFilters.date_from = dateFromInput ? dateFromInput.value : '';
                currentFilters.date_to = dateToInput ? dateToInput.value : '';
                currentFilters.payment_method = paymentMethodSelect ? paymentMethodSelect.value : '';
                currentFilters.total_from = totalFromInput ? totalFromInput.value.replace(/\D/g, '') : '';
                currentFilters.total_to = totalToInput ? totalToInput.value.replace(/\D/g, '') : '';
                currentFilters.sort_by = sortBySelect ? sortBySelect.value : '';
                currentFilters.sort_order = sortOrderSelect ? sortOrderSelect.value : '';
                
                // Get current active tab
                const activeTab = document.querySelector('.status-tab.active');
                const currentStatus = activeTab ? activeTab.getAttribute('data-status') : '';
                
                // Load orders with new filters
                loadOrders(currentStatus, 1);
            };
            filterForm.addEventListener('submit', filterForm._submitHandler);
        }
        
        // Auto-submit on sort change
        if (sortBySelect) {
            sortBySelect.removeEventListener('change', sortBySelect._changeHandler);
            sortBySelect._changeHandler = function() {
                if (filterForm) {
                    filterForm.dispatchEvent(new Event('submit'));
                }
            };
            sortBySelect.addEventListener('change', sortBySelect._changeHandler);
        }
        
        if (sortOrderSelect) {
            sortOrderSelect.removeEventListener('change', sortOrderSelect._changeHandler);
            sortOrderSelect._changeHandler = function() {
                if (filterForm) {
                    filterForm.dispatchEvent(new Event('submit'));
                }
            };
            sortOrderSelect.addEventListener('change', sortOrderSelect._changeHandler);
        }
    }

    // Initialize form elements on page load
    initializeFormElements();
    
    // Re-initialize form elements when DOM changes (after new order is added)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                // Check if any added node affects our form
                for (let node of mutation.addedNodes) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        // Re-initialize if form elements might be affected
                        setTimeout(() => {
                            initializeFormElements();
                        }, 100);
                        break;
                    }
                }
            }
        });
    });
    
    // Observe changes to the orders table container
    const ordersContainer = document.getElementById('orders-table-container');
    if (ordersContainer) {
        observer.observe(ordersContainer, {
            childList: true,
            subtree: true
        });
    }
    
    statusTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            const status = this.getAttribute('data-status');
            
            // Update active tab
            statusTabs.forEach(t => {
                t.classList.remove('active', 'border-blue-500', 'text-blue-600');
                t.classList.add('border-transparent', 'text-gray-500');
            });
            
            this.classList.add('active', 'border-blue-500', 'text-blue-600');
            this.classList.remove('border-transparent', 'text-gray-500');
            
            // Load orders via AJAX
            loadOrders(status, 1);
        });
    });
    
    function loadOrders(status = '', page = 1) {
        // Show loading
        loadingSpinner.classList.remove('hidden');
        ordersTable.style.opacity = '0.5';
        
        // Build URL with filters
        const params = new URLSearchParams();
        if (status) params.append('status', status);
        if (currentFilters.order_code) params.append('order_code', currentFilters.order_code);
        if (currentFilters.customer_name) params.append('customer_name', currentFilters.customer_name);
        if (currentFilters.branch_id) params.append('branch_id', currentFilters.branch_id);
        if (currentFilters.date_from) params.append('date_from', currentFilters.date_from);
        if (currentFilters.date_to) params.append('date_to', currentFilters.date_to);
        if (currentFilters.payment_method) params.append('payment_method', currentFilters.payment_method);
        if (currentFilters.total_from) params.append('total_from', currentFilters.total_from);
        if (currentFilters.total_to) params.append('total_to', currentFilters.total_to);
        if (currentFilters.sort_by) params.append('sort_by', currentFilters.sort_by);
        if (currentFilters.sort_order) params.append('sort_order', currentFilters.sort_order);
        if (page > 1) params.append('page', page);
        params.append('ajax', '1');
        
        const url = '{{ route("admin.orders.index") }}?' + params.toString();
        
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            // Update table content
            ordersTbody.innerHTML = data.html;
            
            // Update pagination
            if (paginationContainer) {
                const paginationStart = document.getElementById('paginationStart');
                const paginationEnd = document.getElementById('paginationEnd');
                const paginationTotal = document.getElementById('paginationTotal');
                const paginationControls = document.getElementById('paginationControls');
                
                if (paginationStart) paginationStart.textContent = data.pagination.from || 0;
                if (paginationEnd) paginationEnd.textContent = data.pagination.to || 0;
                if (paginationTotal) paginationTotal.textContent = data.pagination.total || 0;
                if (paginationControls) paginationControls.innerHTML = data.pagination.links || '';
            }
            
            // Update tab counts
            if (data.counts) {
                statusTabs.forEach(tab => {
                    const tabStatus = tab.getAttribute('data-status');
                    const countSpan = tab.querySelector('.tab-count');
                    if (countSpan) {
                        if (tabStatus === '') {
                            countSpan.textContent = data.counts.all || 0;
                        } else {
                            countSpan.textContent = data.counts[tabStatus] || 0;
                        }
                    }
                });
            }
            
            // Update URL without page reload
            const newUrl = '{{ route("admin.orders.index") }}' + (params.toString().replace('&ajax=1', '').replace('ajax=1&', '').replace('ajax=1', '') ? '?' + params.toString().replace('&ajax=1', '').replace('ajax=1&', '').replace('ajax=1', '') : '');
            window.history.pushState({}, '', newUrl);
        })
        .catch(error => {
            console.error('Error loading orders:', error);
            // Show error message
            ordersTbody.innerHTML = '<tr><td colspan="9" class="text-center py-8 text-red-500">Có lỗi xảy ra khi tải dữ liệu. Vui lòng thử lại.</td></tr>';
        })
        .finally(() => {
            // Hide loading
            loadingSpinner.classList.add('hidden');
            ordersTable.style.opacity = '1';
        });
    }
    
    // Handle pagination clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const link = e.target.closest('.pagination a');
            const url = new URL(link.href);
            const page = url.searchParams.get('page') || 1;
            const currentStatus = document.querySelector('.status-tab.active')?.getAttribute('data-status') || '';
            
            loadOrders(currentStatus, page);
        }
    });
});
</script>
@endsection
