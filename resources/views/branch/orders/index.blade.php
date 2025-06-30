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
            <div class="flex flex-col gap-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input
                                type="text"
                                id="searchInput"
                                placeholder="Tìm theo mã đơn, tên khách hàng hoặc số điện thoại..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        <button id="dateRangeBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Thời gian
                        </button>

                        <select id="priorityFilter" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <option value="all">Tất cả</option>
                            <option value="high">Ưu tiên</option>
                            <option value="normal">Bình thường</option>
                        </select>

                        <select id="customerTypeFilter" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <option value="all">Tất cả</option>
                            <option value="vip">VIP</option>
                            <option value="new">Mới</option>
                            <option value="regular">Thường</option>
                        </select>

                        <select id="paymentFilter" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <option value="all">Tất cả</option>
                            <option value="Tiền mặt">Tiền mặt</option>
                            <option value="MOMO">MOMO</option>
                            <option value="ZaloPay">ZaloPay</option>
                        </select>

                        <select id="sortFilter" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <option value="createdAt-desc">Mới nhất</option>
                            <option value="createdAt-asc">Cũ nhất</option>
                            <option value="total-desc">Giá cao</option>
                            <option value="total-asc">Giá thấp</option>
                            <option value="estimatedDelivery-asc">Giao sớm</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <div class="flex gap-2">
                        <button id="refreshBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Làm mới
                        </button>
                        <button id="exportBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Xuất báo cáo
                        </button>
                        <button id="selectAllBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <input type="checkbox" id="selectAllCheckbox" class="mr-2 rounded">
                            Chọn tất cả
                        </button>
                    </div>

                    <button id="clearFiltersBtn" class="text-gray-500 hover:text-gray-700 text-sm hidden">
                        Xóa bộ lọc
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 overflow-x-auto">
                <button class="status-tab active whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm" data-status="all">
                    Tất cả (<span id="count-all">5</span>)
                </button>
                <button class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm" data-status="pending">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Chờ (<span id="count-pending">1</span>)
                </button>
                <button class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm" data-status="preparing">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Chuẩn bị (<span id="count-preparing">1</span>)
                </button>
                <button class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm" data-status="delivering">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Giao (<span id="count-delivering">1</span>)
                </button>
                <button class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm" data-status="completed">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Hoàn thành (<span id="count-completed">1</span>)
                </button>
                <button class="status-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm" data-status="cancelled">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Hủy (<span id="count-cancelled">1</span>)
                </button>
            </nav>
        </div>
    </div>

    <!-- Orders Grid -->
    <div id="ordersGrid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Initial orders will be rendered here by JavaScript -->
        
        <!-- Sample Order Card 1 - Pending -->
        <div class="order-card bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-4">
                <div class="flex items-start gap-3 mb-3">
                    <input type="checkbox" class="order-checkbox mt-1 rounded" data-order-id="ORD001">
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-lg text-gray-900">#ORD001</h3>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"></path>
                                    </svg>
                                    Ưu tiên
                                </span>
                            </div>
                            <span class="status-badge bg-yellow-500 text-white rounded-lg px-1">Chờ xác nhận</span>
                        </div>

                        <div class="flex items-center gap-2 mb-2">
                            <div class="tooltip flex items-center gap-1 cursor-help">
                                <span class="text-sm font-medium text-gray-900">Nguyễn Văn A</span>
                                <svg class="w-3 h-3 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732L14.146 12.8l-1.179 4.456a1 1 0 01-1.934 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732L9.854 7.2l1.179-4.456A1 1 0 0112 2z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-xs text-gray-500">(15 đơn)</span>
                                <div class="tooltip-content">
                                    <div class="text-xs space-y-1">
                                        <p>📞 0901234567</p>
                                        <p>📍 2.5km</p>
                                        <p>📦 Tổng đơn: 15</p>
                                        <p>📅 Đơn gần nhất: 2024-01-10</p>
                                        <p>❌ Tỷ lệ hủy: 5%</p>
                                        <p>👑 Khách hàng VIP</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tổng tiền:</span>
                                <span class="font-medium text-gray-900">215.000₫</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Thời gian:</span>
                                <span class="text-gray-700">15/01/2024 10:30</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Dự kiến giao:</span>
                                <span class="font-medium text-green-600">45 phút nữa</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Thanh toán:</span>
                                <span class="text-gray-700">Tiền mặt</span>
                            </div>
                            <div class="flex items-start gap-1">
                                <svg class="w-3 h-3 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span class="text-xs text-gray-500 line-clamp-2">Không hành, ít muối</span>
                            </div>
                        </div>

                        <div class="flex gap-2 mb-3">
                            <button onclick="handleQuickAction('ORD001', 'confirm')" class="px-2 py-1 text-xs rounded bg-blue-500 text-white hover:bg-blue-600">
                                ✅ Xác nhận
                            </button>
                            <button onclick="handleQuickAction('ORD001', 'cancel')" class="px-2 py-1 text-xs rounded bg-red-500 text-white hover:bg-red-600">
                                ❌ Hủy
                            </button>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('branch.orders.show') }}" class="flex-1">
                                <button class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Chi tiết
                                </button>
                            </a>
                            <a href="tel:0901234567" class="px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sample Order Card 2 - Preparing -->
        <div class="order-card bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-4">
                <div class="flex items-start gap-3 mb-3">
                    <input type="checkbox" class="order-checkbox mt-1 rounded" data-order-id="ORD002">
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-lg text-gray-900">#ORD002</h3>
                            </div>
                            <span class="status-badge bg-orange-500 text-white rounded-lg px-1">Đang chuẩn bị</span>
                        </div>

                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-sm font-medium text-gray-900">Trần Thị B</span>
                            <span class="text-xs text-gray-500">(3 đơn)</span>
                        </div>

                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tổng tiền:</span>
                                <span class="font-medium text-gray-900">145.000₫</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Thời gian:</span>
                                <span class="text-gray-700">15/01/2024 11:15</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Dự kiến giao:</span>
                                <span class="font-medium text-green-600">45 phút nữa</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Thanh toán:</span>
                                <span class="text-gray-700">MOMO</span>
                            </div>
                        </div>

                        <div class="flex gap-2 mb-3">
                            <button onclick="handleQuickAction('ORD002', 'deliver')" class="px-2 py-1 text-xs rounded bg-blue-500 text-white hover:bg-blue-600">
                                🚚 Giao hàng
                            </button>
                        </div>

                        <div class="flex gap-2">
                            <a href="/branch/orders/ORD002" class="flex-1">
                                <button class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Chi tiết
                                </button>
                            </a>
                            <a href="tel:0912345678" class="px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sample Order Card 3 - Delivering -->
        <div class="order-card bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-4">
                <div class="flex items-start gap-3 mb-3">
                    <input type="checkbox" class="order-checkbox mt-1 rounded" data-order-id="ORD003">
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-lg text-gray-900">#ORD003</h3>
                            </div>
                            <span class="status-badge bg-blue-500 text-white rounded-lg px-1">Đang giao</span>
                        </div>

                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-sm font-medium text-gray-900">Lê Văn C</span>
                            <span class="text-xs text-gray-500">(8 đơn)</span>
                        </div>

                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tổng tiền:</span>
                                <span class="font-medium text-gray-900">45.000₫</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Thời gian:</span>
                                <span class="text-gray-700">15/01/2024 09:45</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Dự kiến giao:</span>
                                <span class="font-medium text-red-500">Trễ 15 phút</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Thanh toán:</span>
                                <span class="text-gray-700">ZaloPay</span>
                            </div>
                            <div class="flex items-start gap-1">
                                <svg class="w-3 h-3 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span class="text-xs text-gray-500 line-clamp-2">Giao trước 12h</span>
                            </div>
                        </div>

                        <div class="flex gap-2 mb-3">
                            <button onclick="handleQuickAction('ORD003', 'complete')" class="px-2 py-1 text-xs rounded bg-blue-500 text-white hover:bg-blue-600">
                                ✅ Hoàn thành
                            </button>
                        </div>

                        <div class="flex gap-2">
                            <a href="/branch/orders/ORD003" class="flex-1">
                                <button class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Chi tiết
                                </button>
                            </a>
                            <a href="tel:0923456789" class="px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sample Order Card 4 - Completed -->
        <div class="order-card bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-4">
                <div class="flex items-start gap-3 mb-3">
                    <input type="checkbox" class="order-checkbox mt-1 rounded" data-order-id="ORD004">
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-lg text-gray-900">#ORD004</h3>
                            </div>
                            <span class="status-badge bg-green-500 text-white rounded-lg px-1">Đã hoàn thành</span>
                        </div>

                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-sm font-medium text-gray-900">Phạm Thị D</span>
                            <span class="text-xs text-gray-500">(25 đơn)</span>
                        </div>

                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tổng tiền:</span>
                                <span class="font-medium text-gray-900">115.000₫</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Thời gian:</span>
                                <span class="text-gray-700">15/01/2024 08:20</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Đã giao:</span>
                                <span class="font-medium text-green-600">Hoàn thành</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Thanh toán:</span>
                                <span class="text-gray-700">Tiền mặt</span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="/branch/orders/ORD004" class="flex-1">
                                <button class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Chi tiết
                                </button>
                            </a>
                            <a href="tel:0934567890" class="px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sample Order Card 5 - Cancelled -->
        <div class="order-card bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-4">
                <div class="flex items-start gap-3 mb-3">
                    <input type="checkbox" class="order-checkbox mt-1 rounded" data-order-id="ORD005">
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-lg text-gray-900">#ORD005</h3>
                            </div>
                            <span class="status-badge bg-red-500 text-white rounded-lg px-1">Đã hủy</span>
                        </div>

                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-sm font-medium text-gray-900">Hoàng Văn E</span>
                            <span class="text-xs text-gray-500">(1 đơn)</span>
                        </div>

                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tổng tiền:</span>
                                <span class="font-medium text-gray-900">180.000₫</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Thời gian:</span>
                                <span class="text-gray-700">15/01/2024 07:30</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Trạng thái:</span>
                                <span class="font-medium text-red-500">Đã hủy</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Thanh toán:</span>
                                <span class="text-gray-700">MOMO</span>
                            </div>
                            <div class="flex items-start gap-1">
                                <svg class="w-3 h-3 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span class="text-xs text-gray-500 line-clamp-2">Khách hủy do thay đổi kế hoạch</span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="/branch/orders/ORD005" class="flex-1">
                                <button class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Chi tiết
                                </button>
                            </a>
                            <a href="tel:0945678901" class="px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty State (Hidden by default) -->
    <div id="emptyState" class="hidden">
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
// Mock data
const mockOrders = [
    {
        id: "ORD001",
        customerName: "Nguyễn Văn A",
        customerPhone: "0901234567",
        customerAddress: "123 Đường ABC, Quận 1, TP.HCM",
        customerOrderCount: 15,
        customerLastOrder: "2024-01-10",
        customerCancelRate: 5,
        customerIsVip: true,
        items: [
            { name: "Phở Bò Tái", quantity: 2, price: 65000 },
            { name: "Chả Cá Lã Vọng", quantity: 1, price: 85000 }
        ],
        total: 215000,
        paymentMethod: "Tiền mặt",
        status: "pending",
        priority: "high",
        createdAt: "2024-01-15T10:30:00Z",
        estimatedDelivery: "2024-01-15T11:15:00Z",
        note: "Không hành, ít muối",
        distance: 2.5
    },
    {
        id: "ORD002",
        customerName: "Trần Thị B",
        customerPhone: "0912345678",
        customerAddress: "456 Đường XYZ, Quận 3, TP.HCM",
        customerOrderCount: 3,
        customerLastOrder: "2024-01-12",
        customerCancelRate: 0,
        customerIsVip: false,
        items: [
            { name: "Bún Bò Huế", quantity: 1, price: 55000 },
            { name: "Nem Nướng", quantity: 2, price: 45000 }
        ],
        total: 145000,
        paymentMethod: "MOMO",
        status: "preparing",
        priority: "normal",
        createdAt: "2024-01-15T11:15:00Z",
        estimatedDelivery: "2024-01-15T12:00:00Z",
        note: "",
        distance: 4.2
    },
    {
        id: "ORD003",
        customerName: "Lê Văn C",
        customerPhone: "0923456789",
        customerAddress: "789 Đường DEF, Quận 5, TP.HCM",
        customerOrderCount: 8,
        customerLastOrder: "2024-01-14",
        customerCancelRate: 12,
        customerIsVip: false,
        items: [{ name: "Cơm Tấm Sườn", quantity: 1, price: 45000 }],
        total: 45000,
        paymentMethod: "ZaloPay",
        status: "delivering",
        priority: "normal",
        createdAt: "2024-01-15T09:45:00Z",
        estimatedDelivery: "2024-01-15T10:30:00Z",
        note: "Giao trước 12h",
        distance: 6.1
    },
    {
        id: "ORD004",
        customerName: "Phạm Thị D",
        customerPhone: "0934567890",
        customerAddress: "321 Đường GHI, Quận 7, TP.HCM",
        customerOrderCount: 25,
        customerLastOrder: "2024-01-13",
        customerCancelRate: 2,
        customerIsVip: true,
        items: [
            { name: "Bánh Mì Thịt", quantity: 3, price: 25000 },
            { name: "Cà Phê Sữa", quantity: 2, price: 20000 }
        ],
        total: 115000,
        paymentMethod: "Tiền mặt",
        status: "completed",
        priority: "normal",
        createdAt: "2024-01-15T08:20:00Z",
        estimatedDelivery: "2024-01-15T09:05:00Z",
        note: "",
        distance: 3.8
    },
    {
        id: "ORD005",
        customerName: "Hoàng Văn E",
        customerPhone: "0945678901",
        customerAddress: "654 Đường JKL, Quận 10, TP.HCM",
        customerOrderCount: 1,
        customerLastOrder: "2024-01-15",
        customerCancelRate: 100,
        customerIsVip: false,
        items: [{ name: "Lẩu Thái", quantity: 1, price: 180000 }],
        total: 180000,
        paymentMethod: "MOMO",
        status: "cancelled",
        priority: "normal",
        createdAt: "2024-01-15T07:30:00Z",
        estimatedDelivery: "2024-01-15T08:15:00Z",
        note: "Khách hủy do thay đổi kế hoạch",
        distance: 5.5
    }
];

// Global variables
let orders = [...mockOrders];
let filteredOrders = [...orders];
let selectedOrders = [];
let currentStatus = 'all';

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
}

function formatTime(dateString) {
    return new Date(dateString).toLocaleString('vi-VN');
}

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
    const order = orders.find(o => o.id === orderId);
    if (!order) return;

    switch (action) {
        case 'confirm':
            order.status = 'preparing';
            showToast('✅ Đã xác nhận đơn hàng', `Đơn hàng #${orderId} đã được xác nhận`);
            break;
        case 'deliver':
            order.status = 'delivering';
            showToast('🚚 Đã giao cho shipper', `Đơn hàng #${orderId} đang được giao`);
            break;
        case 'complete':
            order.status = 'completed';
            showToast('✅ Đã hoàn thành', `Đơn hàng #${orderId} đã hoàn thành`);
            break;
        case 'cancel':
            order.status = 'cancelled';
            showToast('❌ Đã hủy đơn hàng', `Đơn hàng #${orderId} đã được hủy`, 'error');
            break;
    }

    // Update the UI
    updateStatusCounts();
    
    // Update the specific order card
    const orderCard = document.querySelector(`[data-order-id="${orderId}"]`).closest('.order-card');
    if (orderCard) {
        // Update status badge
        const statusBadge = orderCard.querySelector('.status-badge');
        const statusConfig = {
            pending: { label: "Chờ xác nhận", color: "bg-yellow-500" },
            preparing: { label: "Đang chuẩn bị", color: "bg-orange-500" },
            delivering: { label: "Đang giao", color: "bg-blue-500" },
            completed: { label: "Đã hoàn thành", color: "bg-green-500" },
            cancelled: { label: "Đã hủy", color: "bg-red-500" }
        };
        
        const newStatus = statusConfig[order.status];
        statusBadge.className = `status-badge ${newStatus.color} text-white`;
        statusBadge.textContent = newStatus.label;
        
        // Remove quick action buttons for completed/cancelled orders
        if (order.status === 'completed' || order.status === 'cancelled') {
            const quickActionsDiv = orderCard.querySelector('.flex.gap-2.mb-3');
            if (quickActionsDiv) {
                quickActionsDiv.remove();
            }
        }
    }
}

function updateStatusCounts() {
    const counts = {
        all: orders.length,
        pending: orders.filter(o => o.status === 'pending').length,
        preparing: orders.filter(o => o.status === 'preparing').length,
        delivering: orders.filter(o => o.status === 'delivering').length,
        completed: orders.filter(o => o.status === 'completed').length,
        cancelled: orders.filter(o => o.status === 'cancelled').length
    };

    Object.keys(counts).forEach(status => {
        const countElement = document.getElementById(`count-${status}`);
        if (countElement) {
            countElement.textContent = counts[status];
        }
    });
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Update initial counts
    updateStatusCounts();

    // Add event listeners to existing checkboxes
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

    // Status tabs
    document.querySelectorAll('.status-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            document.querySelectorAll('.status-tab').forEach(t => {
                t.classList.remove('active', 'border-blue-500', 'text-blue-600');
                t.classList.add('border-transparent', 'text-gray-500');
            });

            // Add active class to clicked tab
            this.classList.add('active', 'border-blue-500', 'text-blue-600');
            this.classList.remove('border-transparent', 'text-gray-500');

            currentStatus = this.dataset.status;
            filterOrdersByStatus();
        });
    });

    // Other event listeners
    document.getElementById('refreshBtn').addEventListener('click', function() {
        const btn = this;
        const icon = btn.querySelector('svg');
        icon.classList.add('animate-spin');
        
        setTimeout(() => {
            icon.classList.remove('animate-spin');
            showToast('🔄 Đã cập nhật', 'Danh sách đơn hàng đã được làm mới');
        }, 1000);
    });

    document.getElementById('exportBtn').addEventListener('click', function() {
        showToast('📊 Xuất báo cáo thành công', 'File CSV đã được tải xuống');
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

function filterOrdersByStatus() {
    const allCards = document.querySelectorAll('.order-card');
    
    allCards.forEach(card => {
        const checkbox = card.querySelector('.order-checkbox');
        const orderId = checkbox.dataset.orderId;
        const order = orders.find(o => o.id === orderId);
        
        if (currentStatus === 'all' || order.status === currentStatus) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Simulate new order notifications
setInterval(() => {
    if (Math.random() < 0.1) { // 10% chance every 30 seconds
        showToast('🔔 Đơn hàng mới!', 'Có đơn hàng mới cần xử lý');
    }
}, 30000);
</script>
@endsection