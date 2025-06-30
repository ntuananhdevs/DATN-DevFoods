@extends('layouts.branch.contentLayoutMaster')

@section('title', 'Chi tiết đơn hàng #ORD001')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
@endsection

@section('page-style')
<style>
.status-timeline {
    position: relative;
}
.status-timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e5e7eb;
}
.timeline-item {
    position: relative;
    padding-left: 40px;
    margin-bottom: 24px;
}
.timeline-dot {
    position: absolute;
    left: 8px;
    top: 4px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #e5e7eb;
    background: white;
}
.timeline-dot.active {
    border-color: #3b82f6;
    background: #3b82f6;
}
.timeline-dot.completed {
    border-color: #10b981;
    background: #10b981;
}
.order-item-card {
    transition: all 0.2s ease;
}
.order-item-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 600;
}
.action-button {
    transition: all 0.2s ease;
}
.action-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.info-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.customer-card {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
.payment-card {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}
.delivery-card {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}
@media print {
    .no-print { display: none !important; }
    .print-only { display: block !important; }
    body { background: white !important; }
}
</style>
@endsection

@section('content')
<div class="mx-auto p-4">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-6">
        <div class="flex items-center gap-4 mb-4 lg:mb-0">
            <a href="{{ route('branch.orders.index') }}" class="no-print inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Quay lại
            </a>
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Đơn hàng #<span id="orderId">ORD001</span></h1>
                <p class="text-gray-600">Chi tiết và theo dõi đơn hàng</p>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-2 no-print">
            <button id="printBtn" class="action-button inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H3a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                In đơn hàng
            </button>
            <button id="editBtn" class="action-button inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg font-medium hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Chỉnh sửa
            </button>
            <div class="relative">
                <button id="moreActionsBtn" class="action-button inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                    </svg>
                    Thêm
                </button>
                <div id="moreActionsMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                    <div class="py-1">
                        <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">📧 Gửi email khách hàng</button>
                        <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">📱 Gửi SMS</button>
                        <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">📋 Sao chép đơn hàng</button>
                        <button class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">🗑️ Xóa đơn hàng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status and Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="p-6">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <span id="statusBadge" class="status-badge bg-yellow-500 text-white flex items-center px-1 rounded-lg">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Chờ xác nhận
                    </span>
                    <div class="text-sm text-gray-600">
                        <span>Đặt lúc: </span>
                        <span class="font-medium" id="orderTime">15/01/2024 10:30</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <span>Dự kiến giao: </span>
                        <span class="font-medium text-green-600" id="deliveryTime">45 phút nữa</span>
                    </div>
                </div>
                
                <div id="quickActions" class="flex flex-wrap gap-2">
                    <button onclick="updateOrderStatus('preparing')" class="action-button px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">
                        ✅ Xác nhận đơn
                    </button>
                    <button onclick="updateOrderStatus('cancelled')" class="action-button px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700">
                        ❌ Hủy đơn
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Sản phẩm đã đặt
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4" id="orderItems">
                        <!-- Order Item 1 -->
                        <div class="order-item-card flex items-center gap-4 p-4 border border-gray-200 rounded-lg">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900">Phở Bò Tái</h3>
                                <p class="text-sm text-gray-600">Phở truyền thống với thịt bò tái</p>
                                <div class="flex items-center gap-4 mt-2">
                                    <span class="text-sm text-gray-500">Số lượng: <span class="font-medium">2</span></span>
                                    <span class="text-sm text-gray-500">Đơn giá: <span class="font-medium">65.000₫</span></span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-semibold text-gray-900">130.000₫</div>
                            </div>
                        </div>

                        <!-- Order Item 2 -->
                        <div class="order-item-card flex items-center gap-4 p-4 border border-gray-200 rounded-lg">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900">Chả Cá Lã Vọng</h3>
                                <p class="text-sm text-gray-600">Chả cá truyền thống Hà Nội</p>
                                <div class="flex items-center gap-4 mt-2">
                                    <span class="text-sm text-gray-500">Số lượng: <span class="font-medium">1</span></span>
                                    <span class="text-sm text-gray-500">Đơn giá: <span class="font-medium">85.000₫</span></span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-semibold text-gray-900">85.000₫</div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tạm tính:</span>
                                <span class="font-medium">215.000₫</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Phí giao hàng:</span>
                                <span class="font-medium">0₫</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Giảm giá:</span>
                                <span class="font-medium text-green-600">-0₫</span>
                            </div>
                            <div class="flex justify-between text-lg font-semibold pt-3 border-t border-gray-200">
                                <span>Tổng cộng:</span>
                                <span class="text-blue-600" id="totalAmount">215.000₫</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Lịch sử đơn hàng
                    </h2>
                </div>
                <div class="p-6">
                    <div class="status-timeline" id="orderTimeline">
                        <div class="timeline-item">
                            <div class="timeline-dot completed"></div>
                            <div>
                                <div class="font-medium text-gray-900">Đơn hàng được tạo</div>
                                <div class="text-sm text-gray-600">15/01/2024 10:30</div>
                                <div class="text-sm text-gray-500 mt-1">Khách hàng đã đặt đơn hàng thành công</div>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-dot active"></div>
                            <div>
                                <div class="font-medium text-gray-900">Chờ xác nhận</div>
                                <div class="text-sm text-gray-600">Hiện tại</div>
                                <div class="text-sm text-gray-500 mt-1">Đơn hàng đang chờ nhà hàng xác nhận</div>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div>
                                <div class="font-medium text-gray-400">Chuẩn bị món</div>
                                <div class="text-sm text-gray-400">Chưa thực hiện</div>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div>
                                <div class="font-medium text-gray-400">Đang giao hàng</div>
                                <div class="text-sm text-gray-400">Chưa thực hiện</div>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div>
                                <div class="font-medium text-gray-400">Hoàn thành</div>
                                <div class="text-sm text-gray-400">Chưa thực hiện</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Internal Notes -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Ghi chú nội bộ
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4" id="internalNotes">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-yellow-600 font-medium text-sm">NV</span>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">Nhân viên bếp</div>
                                    <div class="text-sm text-gray-600 mt-1">Khách yêu cầu không hành, ít muối</div>
                                    <div class="text-xs text-gray-500 mt-2">15/01/2024 10:35</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <textarea id="newNote" placeholder="Thêm ghi chú nội bộ..." class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="3"></textarea>
                        <button onclick="addInternalNote()" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                            Thêm ghi chú
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Customer Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="customer-card p-6">
                    <h3 class="text-lg font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Thông tin khách hàng
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-semibold">
                                NA
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 flex items-center gap-2">
                                    <span id="customerName">Nguyễn Văn A</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732L14.146 12.8l-1.179 4.456a1 1 0 01-1.934 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732L9.854 7.2l1.179-4.456A1 1 0 0112 2z" clip-rule="evenodd"></path>
                                        </svg>
                                        VIP
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600">Khách hàng thân thiết</div>
                            </div>
                        </div>
                        
                        <div class="space-y-3 pt-4 border-t border-gray-200">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span class="text-sm text-gray-600" id="customerPhone">0901234567</span>
                                <a href="tel:0901234567" class="text-blue-600 hover:text-blue-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </a>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="text-sm text-gray-600" id="customerAddress">123 Đường ABC, Quận 1, TP.HCM</span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                            <div class="text-center">
                                <div class="text-lg font-semibold text-gray-900">15</div>
                                <div class="text-xs text-gray-600">Tổng đơn</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-semibold text-gray-900">5%</div>
                                <div class="text-xs text-gray-600">Tỷ lệ hủy</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="payment-card p-6">
                    <h3 class="text-lg font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Thanh toán
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phương thức:</span>
                            <span class="font-medium" id="paymentMethod">Tiền mặt</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Trạng thái:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Chưa thanh toán
                            </span>
                        </div>
                        <div class="flex justify-between text-lg font-semibold pt-4 border-t border-gray-200">
                            <span>Tổng tiền:</span>
                            <span class="text-blue-600">215.000₫</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="delivery-card p-6">
                    <h3 class="text-lg font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Giao hàng
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Khoảng cách:</span>
                            <span class="font-medium">2.5 km</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Thời gian dự kiến:</span>
                            <span class="font-medium text-green-600">45 phút</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phí giao hàng:</span>
                            <span class="font-medium">Miễn phí</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tài xế:</span>
                            <span class="font-medium text-gray-400">Chưa phân công</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Thao tác nhanh</h3>
                </div>
                <div class="p-6 space-y-3">
                    <button onclick="callCustomer()" class="w-full flex items-center gap-3 px-4 py-3 text-left border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span>Gọi khách hàng</span>
                    </button>
                    
                    <button onclick="sendSMS()" class="w-full flex items-center gap-3 px-4 py-3 text-left border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span>Gửi tin nhắn</span>
                    </button>
                    
                    <button onclick="assignDriver()" class="w-full flex items-center gap-3 px-4 py-3 text-left border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Phân công tài xế</span>
                    </button>
                </div>
            </div>
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
// Order data
const orderData = {
    id: "ORD001",
    customerName: "Nguyễn Văn A",
    customerPhone: "0901234567",
    customerAddress: "123 Đường ABC, Quận 1, TP.HCM",
    customerIsVip: true,
    customerOrderCount: 15,
    customerCancelRate: 5,
    items: [
        { name: "Phở Bò Tái", quantity: 2, price: 65000, description: "Phở truyền thống với thịt bò tái" },
        { name: "Chả Cá Lã Vọng", quantity: 1, price: 85000, description: "Chả cá truyền thống Hà Nội" }
    ],
    total: 215000,
    paymentMethod: "Tiền mặt",
    paymentStatus: "pending",
    status: "pending",
    priority: "high",
    createdAt: "2024-01-15T10:30:00Z",
    estimatedDelivery: "2024-01-15T11:15:00Z",
    note: "Không hành, ít muối",
    distance: 2.5,
    deliveryFee: 0,
    discount: 0
};

// Status configuration
const statusConfig = {
    pending: { 
        label: "Chờ xác nhận", 
        color: "bg-yellow-500", 
        icon: "M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z",
        actions: [
            { label: "✅ Xác nhận đơn", action: "preparing", color: "bg-green-600 hover:bg-green-700" },
            { label: "❌ Hủy đơn", action: "cancelled", color: "bg-red-600 hover:bg-red-700" }
        ]
    },
    preparing: { 
        label: "Đang chuẩn bị", 
        color: "bg-orange-500", 
        icon: "M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4",
        actions: [
            { label: "🚚 Giao hàng", action: "delivering", color: "bg-blue-600 hover:bg-blue-700" }
        ]
    },
    delivering: { 
        label: "Đang giao", 
        color: "bg-blue-500", 
        icon: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z",
        actions: [
            { label: "✅ Hoàn thành", action: "completed", color: "bg-green-600 hover:bg-green-700" }
        ]
    },
    completed: { 
        label: "Đã hoàn thành", 
        color: "bg-green-500", 
        icon: "M5 13l4 4L19 7",
        actions: []
    },
    cancelled: { 
        label: "Đã hủy", 
        color: "bg-red-500", 
        icon: "M6 18L18 6M6 6l12 12",
        actions: []
    }
};

// Timeline configuration
const timelineSteps = [
    { key: "created", label: "Đơn hàng được tạo", description: "Khách hàng đã đặt đơn hàng thành công" },
    { key: "pending", label: "Chờ xác nhận", description: "Đơn hàng đang chờ nhà hàng xác nhận" },
    { key: "preparing", label: "Chuẩn bị món", description: "Nhà hàng đang chuẩn bị món ăn" },
    { key: "delivering", label: "Đang giao hàng", description: "Tài xế đang giao hàng đến khách" },
    { key: "completed", label: "Hoàn thành", description: "Đơn hàng đã được giao thành công" }
];

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
}

function formatTime(dateString) {
    return new Date(dateString).toLocaleString('vi-VN');
}

function formatDeliveryTime(dateString) {
    const now = new Date();
    const deliveryTime = new Date(dateString);
    const diffMinutes = Math.ceil((deliveryTime.getTime() - now.getTime()) / (1000 * 60));

    if (diffMinutes < 0) {
        return `Trễ ${Math.abs(diffMinutes)} phút`;
    } else if (diffMinutes < 60) {
        return `${diffMinutes} phút nữa`;
    } else {
        return `${Math.ceil(diffMinutes / 60)} giờ nữa`;
    }
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

// Update order status
function updateOrderStatus(newStatus) {
    const oldStatus = orderData.status;
    orderData.status = newStatus;
    
    // Update status badge
    updateStatusBadge();
    
    // Update quick actions
    updateQuickActions();
    
    // Update timeline
    updateTimeline();
    
    // Show notification
    const statusInfo = statusConfig[newStatus];
    showToast('✅ Cập nhật thành công', `Đơn hàng đã chuyển sang trạng thái: ${statusInfo.label}`);
    
    // Add timeline entry
    addTimelineEntry(newStatus);
}

function updateStatusBadge() {
    const statusBadge = document.getElementById('statusBadge');
    const statusInfo = statusConfig[orderData.status];
    
    statusBadge.className = `status-badge ${statusInfo.color} text-white`;
    statusBadge.innerHTML = `
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${statusInfo.icon}"></path>
        </svg>
        ${statusInfo.label}
    `;
}

function updateQuickActions() {
    const quickActions = document.getElementById('quickActions');
    const statusInfo = statusConfig[orderData.status];
    
    if (statusInfo.actions.length === 0) {
        quickActions.innerHTML = '<span class="text-gray-500 text-sm">Không có thao tác khả dụng</span>';
        return;
    }
    
    quickActions.innerHTML = statusInfo.actions.map(action => `
        <button onclick="updateOrderStatus('${action.action}')" class="action-button px-4 py-2 ${action.color} text-white rounded-lg text-sm font-medium">
            ${action.label}
        </button>
    `).join('');
}

function updateTimeline() {
    const timeline = document.getElementById('orderTimeline');
    const currentStatusIndex = timelineSteps.findIndex(step => step.key === orderData.status);
    
    timeline.innerHTML = timelineSteps.map((step, index) => {
        let dotClass = 'timeline-dot';
        let textClass = 'text-gray-400';
        let timeText = 'Chưa thực hiện';
        
        if (index === 0) {
            dotClass += ' completed';
            textClass = 'text-gray-900';
            timeText = formatTime(orderData.createdAt);
        } else if (index <= currentStatusIndex) {
            if (index === currentStatusIndex) {
                dotClass += ' active';
                textClass = 'text-gray-900';
                timeText = 'Hiện tại';
            } else {
                dotClass += ' completed';
                textClass = 'text-gray-900';
                timeText = 'Đã hoàn thành';
            }
        }
        
        return `
            <div class="timeline-item">
                <div class="${dotClass}"></div>
                <div>
                    <div class="font-medium ${textClass}">${step.label}</div>
                    <div class="text-sm ${textClass === 'text-gray-400' ? 'text-gray-400' : 'text-gray-600'}">${timeText}</div>
                    ${step.description ? `<div class="text-sm text-gray-500 mt-1">${step.description}</div>` : ''}
                </div>
            </div>
        `;
    }).join('');
}

function addTimelineEntry(status) {
    // This would typically add a new entry to the timeline
    // For demo purposes, we'll just update the existing timeline
    updateTimeline();
}

// Internal notes functions
function addInternalNote() {
    const noteText = document.getElementById('newNote').value.trim();
    if (!noteText) return;
    
    const notesContainer = document.getElementById('internalNotes');
    const noteElement = document.createElement('div');
    noteElement.className = 'bg-blue-50 border border-blue-200 rounded-lg p-4';
    noteElement.innerHTML = `
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-blue-600 font-medium text-sm">QL</span>
            </div>
            <div class="flex-1">
                <div class="font-medium text-gray-900">Quản lý</div>
                <div class="text-sm text-gray-600 mt-1">${noteText}</div>
                <div class="text-xs text-gray-500 mt-2">${formatTime(new Date().toISOString())}</div>
            </div>
        </div>
    `;
    
    notesContainer.appendChild(noteElement);
    document.getElementById('newNote').value = '';
    
    showToast('📝 Đã thêm ghi chú', 'Ghi chú nội bộ đã được thêm thành công');
}

// Quick action functions
function callCustomer() {
    window.location.href = `tel:${orderData.customerPhone}`;
    showToast('📞 Đang gọi', `Đang gọi cho ${orderData.customerName}`);
}

function sendSMS() {
    showToast('📱 Gửi tin nhắn', 'Tính năng gửi SMS sẽ được triển khai');
}

function assignDriver() {
    showToast('🚚 Phân công tài xế', 'Tính năng phân công tài xế sẽ được triển khai');
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Update initial display
    updateStatusBadge();
    updateQuickActions();
    updateTimeline();
    
    // Set delivery time
    document.getElementById('deliveryTime').textContent = formatDeliveryTime(orderData.estimatedDelivery);
    
    // Print button
    document.getElementById('printBtn').addEventListener('click', function() {
        window.print();
        showToast('🖨️ In đơn hàng', 'Đang chuẩn bị in đơn hàng');
    });
    
    // Edit button
    document.getElementById('editBtn').addEventListener('click', function() {
        showToast('✏️ Chỉnh sửa', 'Tính năng chỉnh sửa sẽ được triển khai');
    });
    
    // More actions menu
    document.getElementById('moreActionsBtn').addEventListener('click', function() {
        const menu = document.getElementById('moreActionsMenu');
        menu.classList.toggle('hidden');
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('moreActionsMenu');
        const button = document.getElementById('moreActionsBtn');
        
        if (!menu.contains(event.target) && !button.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });
    
    // Toast close button
    document.getElementById('closeToast').addEventListener('click', function() {
        document.getElementById('toast').classList.add('hidden');
    });
    
    // Enter key for adding notes
    document.getElementById('newNote').addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && e.ctrlKey) {
            addInternalNote();
        }
    });
});
</script>
@endsection