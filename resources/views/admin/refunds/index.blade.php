@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Quản lý yêu cầu hoàn tiền')

@section('content')
<div class="">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4 sm:mb-0">Quản lý yêu cầu hoàn tiền</h1>
        <div class="flex space-x-2">
            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="refreshData()">
                <i class="fas fa-sync-alt mr-2"></i> Làm mới
            </button>
            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="showStatistics()">
                <i class="fas fa-chart-bar mr-2"></i> Thống kê
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Bộ lọc</h3>
        </div>
        <div class="p-4">
            <form id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" id="status" name="status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending">Chờ xử lý</option>
                        <option value="processing">Đang xử lý</option>
                        <option value="approved">Đã duyệt</option>
                        <option value="completed">Hoàn thành</option>
                        <option value="rejected">Từ chối</option>
                    </select>
                </div>
                <div>
                    <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-1">Chi nhánh</label>
                    <select class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" id="branch_id" name="branch_id">
                        <option value="">Tất cả chi nhánh</option>
                        <!-- Branches will be loaded via AJAX -->
                    </select>
                </div>
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                    <input type="text" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" id="search" name="search" 
                           placeholder="Mã hoàn tiền, tên khách hàng, email...">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-search mr-2"></i> Lọc
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Refund Requests Table -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Danh sách yêu cầu hoàn tiền</h3>
        </div>
        <div class="p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="refundTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã hoàn tiền</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đơn hàng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chi nhánh</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tiền</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if($refundRequests && $refundRequests->count() > 0)
                            @foreach($refundRequests as $refund)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $refund->refund_code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $refund->customer->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $refund->customer->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $refund->order->order_code }}</div>
                                        <div class="text-sm text-gray-500">{{ number_format($refund->order->total_amount, 0, ',', '.') }} VNĐ</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $refund->branch ? $refund->branch->name : 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">{{ number_format($refund->refund_amount, 0, ',', '.') }} VNĐ</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @switch($refund->status)
                                            @case('pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Chờ xử lý</span>
                                                @break
                                            @case('processing')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Đang xử lý</span>
                                                @break
                                            @case('approved')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Đã duyệt</span>
                                                @break
                                            @case('completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">Hoàn thành</span>
                                                @break
                                            @case('rejected')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Từ chối</span>
                                                @break
                                            @default
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Không xác định</span>
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $refund->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.refunds.show', $refund->id) }}" class="inline-flex items-center px-2 py-1 border border-transparent text-xs leading-4 font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(in_array($refund->status, ['pending', 'processing']))
                                                <button class="inline-flex items-center px-2 py-1 border border-transparent text-xs leading-4 font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" onclick="processRefund({{ $refund->id }}, 'approve')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="inline-flex items-center px-2 py-1 border border-transparent text-xs leading-4 font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="processRefund({{ $refund->id }}, 'reject')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                            @if($refund->status === 'approved')
                                                <button class="inline-flex items-center px-2 py-1 border border-transparent text-xs leading-4 font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="processRefund({{ $refund->id }}, 'complete')">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">Không có dữ liệu</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="flex justify-center mt-4">
                @if($refundRequests && $refundRequests->hasPages())
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        {{-- Previous Page Link --}}
                        @if ($refundRequests->onFirstPage())
                            <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-not-allowed">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        @else
                            <a href="{{ $refundRequests->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($refundRequests->getUrlRange(1, $refundRequests->lastPage()) as $page => $url)
                            @if ($page == $refundRequests->currentPage())
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue-50 text-sm font-medium text-blue-600">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($refundRequests->hasMorePages())
                            <a href="{{ $refundRequests->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        @else
                            <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-not-allowed">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        @endif
                    </nav>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Refund Detail Modal -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" id="refundDetailModal">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('refundDetailModal')"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="refundDetailModalLabel">Chi tiết yêu cầu hoàn tiền</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('refundDetailModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="refundDetailContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Process Refund Modal -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" id="processRefundModal">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('processRefundModal')"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="processRefundForm">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900" id="processRefundModalLabel">Xử lý yêu cầu hoàn tiền</h3>
                        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('processRefundModal')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <input type="hidden" id="refundId" name="refund_id">
                    <input type="hidden" id="actionType" name="action_type">
                    
                    <div class="mb-4">
                        <label for="refundAmount" class="block text-sm font-medium text-gray-700 mb-1">Số tiền hoàn lại</label>
                        <input type="number" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" id="refundAmount" name="refund_amount" 
                               step="0.01" min="0">
                        <p class="mt-1 text-sm text-gray-500">Để trống để sử dụng số tiền mặc định</p>
                    </div>
                    
                    <div class="mb-4">
                        <label for="adminNote" class="block text-sm font-medium text-gray-700 mb-1">Ghi chú của admin</label>
                        <textarea class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" id="adminNote" name="admin_note" rows="3" 
                                  placeholder="Nhập ghi chú về quyết định xử lý..."></textarea>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm" id="processRefundBtn">Xử lý</button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal('processRefundModal')">Hủy</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Statistics Modal -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" id="statisticsModal">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('statisticsModal')"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="statisticsModalLabel">Thống kê hoàn tiền</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('statisticsModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="statisticsContent">
                    <!-- Statistics content will be loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    loadRefundRequests();
    loadBranches();
    
    // Filter form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadRefundRequests();
    });
});

// Load refund requests
function loadRefundRequests(page = 1) {
    const formData = new FormData($('#filterForm')[0]);
    const params = new URLSearchParams(formData);
    params.append('page', page);
    
    $.ajax({
        url: '{{ route("admin.refunds.index") }}?' + params.toString(),
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                renderRefundTable(response.data);
            } else {
                showAlert('error', response.message);
            }
        },
        error: function(xhr) {
            showAlert('error', 'Có lỗi xảy ra khi tải dữ liệu');
        }
    });
}

// Render refund table
function renderRefundTable(data) {
    let html = '';
    
    if (data.data && data.data.length > 0) {
        data.data.forEach(function(refund) {
            html += `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${refund.refund_code}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${refund.customer.full_name}</div>
                        <div class="text-sm text-gray-500">${refund.customer.email}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${refund.order.order_code}</div>
                        <div class="text-sm text-gray-500">${formatCurrency(refund.order.total_amount)}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${refund.branch ? refund.branch.name : 'N/A'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">${formatCurrency(refund.refund_amount)}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${getStatusBadge(refund.status)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatDateTime(refund.created_at)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button class="inline-flex items-center px-2 py-1 border border-transparent text-xs leading-4 font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="viewRefundDetail(${refund.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    } else {
        html = '<tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">Không có dữ liệu</td></tr>';
    }
    
    $('#refundTableBody').html(html);
    renderPagination(data);
}

// Get status badge
function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Chờ xử lý</span>',
        'processing': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Đang xử lý</span>',
        'approved': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Đã duyệt</span>',
        'completed': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">Hoàn thành</span>',
        'rejected': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Từ chối</span>'
    };
    return badges[status] || '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Không xác định</span>';
}

// Get action buttons based on status
function getActionButtons(refund) {
    let buttons = '';
    
    if (refund.status === 'pending' || refund.status === 'processing') {
        buttons += `
            <button class="inline-flex items-center px-2 py-1 border border-transparent text-xs leading-4 font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" onclick="processRefund(${refund.id}, 'approve')">
                <i class="fas fa-check"></i>
            </button>
            <button class="inline-flex items-center px-2 py-1 border border-transparent text-xs leading-4 font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="processRefund(${refund.id}, 'reject')">
                <i class="fas fa-times"></i>
            </button>
        `;
    }
    
    if (refund.status === 'approved') {
        buttons += `
            <button class="inline-flex items-center px-2 py-1 border border-transparent text-xs leading-4 font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="processRefund(${refund.id}, 'complete')">
                <i class="fas fa-check-double"></i>
            </button>
        `;
    }
    
    return buttons;
}

// View refund detail
function viewRefundDetail(refundId) {
    $.ajax({
        url: '{{ url("admin/refunds") }}/' + refundId,
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                renderRefundDetail(response.data);
                openModal('refundDetailModal');
            } else {
                showAlert('error', response.message);
            }
        },
        error: function(xhr) {
            showAlert('error', 'Có lỗi xảy ra khi tải chi tiết');
        }
    });
}

// Process refund (approve/reject/complete)
function processRefund(refundId, action) {
    $('#refundId').val(refundId);
    $('#actionType').val(action);
    
    // Set modal title and button text based on action
    const titles = {
        'approve': 'Duyệt yêu cầu hoàn tiền',
        'reject': 'Từ chối yêu cầu hoàn tiền',
        'complete': 'Hoàn thành yêu cầu hoàn tiền'
    };
    
    const buttonTexts = {
        'approve': 'Duyệt',
        'reject': 'Từ chối',
        'complete': 'Hoàn thành'
    };
    
    $('#processRefundModalLabel').text(titles[action]);
    $('#processRefundBtn').text(buttonTexts[action]);
    
    // Make admin note required for reject
    if (action === 'reject') {
        $('#adminNote').attr('required', true);
        $('#adminNote').attr('placeholder', 'Vui lòng nhập lý do từ chối...');
    } else {
        $('#adminNote').removeAttr('required');
        $('#adminNote').attr('placeholder', 'Nhập ghi chú về quyết định xử lý...');
    }
    
    openModal('processRefundModal');
}

// Handle process refund form submission
$('#processRefundForm').on('submit', function(e) {
    e.preventDefault();
    
    const refundId = $('#refundId').val();
    const action = $('#actionType').val();
    const formData = {
        refund_amount: $('#refundAmount').val(),
        admin_note: $('#adminNote').val()
    };
    
    const routes = {
        'approve': '{{ url("admin/refunds") }}/' + refundId + '/approve',
        'reject': '{{ url("admin/refunds") }}/' + refundId + '/reject',
        'complete': '{{ url("admin/refunds") }}/' + refundId + '/complete'
    };
    
    $.ajax({
        url: routes[action],
        method: 'PATCH',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                closeModal('processRefundModal');
                $('#processRefundForm')[0].reset();
                loadRefundRequests();
            } else {
                showAlert('error', response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            if (response && response.errors) {
                let errorMessage = 'Dữ liệu không hợp lệ:\n';
                Object.values(response.errors).forEach(function(errors) {
                    errors.forEach(function(error) {
                        errorMessage += '- ' + error + '\n';
                    });
                });
                showAlert('error', errorMessage);
            } else {
                showAlert('error', 'Có lỗi xảy ra khi xử lý yêu cầu');
            }
        }
    });
});

// Load branches for filter
function loadBranches() {
    // This would typically load from a branches API endpoint
    // For now, we'll leave it empty and populate manually if needed
}

// Show statistics
function showStatistics() {
    const formData = new FormData($('#filterForm')[0]);
    const params = new URLSearchParams(formData);
    
    $.ajax({
        url: '{{ route("admin.refunds.statistics") }}?' + params.toString(),
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                renderStatistics(response.data);
                openModal('statisticsModal');
            } else {
                showAlert('error', response.message);
            }
        },
        error: function(xhr) {
            showAlert('error', 'Có lỗi xảy ra khi tải thống kê');
        }
    });
}

// Render statistics
function renderStatistics(stats) {
    const html = `
        <div class="row">
            <div class="col-md-6">
                <div class="card border-left-primary">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng yêu cầu</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">${stats.total_requests}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-left-warning">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Chờ xử lý</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">${stats.pending_requests}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-3">
                <div class="card border-left-success">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Đã hoàn thành</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">${stats.completed_requests}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-3">
                <div class="card border-left-danger">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Từ chối</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">${stats.rejected_requests}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-3">
                <div class="card border-left-info">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tổng số tiền đã hoàn</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">${formatCurrency(stats.total_refunded_amount || 0)}</div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#statisticsContent').html(html);
}

// Refresh data
function refreshData() {
    loadRefundRequests();
}

// Render pagination
function renderPagination(data) {
    let html = '';
    if (data.last_page > 1) {
        html += '<nav class="flex justify-center"><div class="flex space-x-1">';
        
        // Previous page
        if (data.current_page > 1) {
            html += `<button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50" onclick="loadRefundRequests(${data.current_page - 1})">Trước</button>`;
        }
        
        // Page numbers
        for (let i = 1; i <= data.last_page; i++) {
            const active = i === data.current_page ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-50';
            html += `<button class="px-3 py-2 text-sm font-medium border border-gray-300 ${active}" onclick="loadRefundRequests(${i})">${i}</button>`;
        }
        
        // Next page
        if (data.current_page < data.last_page) {
            html += `<button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50" onclick="loadRefundRequests(${data.current_page + 1})">Sau</button>`;
        }
        
        html += '</div></nav>';
    }
    
    $('#pagination').html(html);
}

// Render refund detail
function renderRefundDetail(refund) {
    const html = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h6 class="text-lg font-semibold text-gray-900 mb-3">Thông tin yêu cầu</h6>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="space-y-2">
                        <div class="flex justify-between"><span class="font-medium text-gray-700">Mã hoàn tiền:</span><span class="text-gray-900">${refund.refund_code}</span></div>
                        <div class="flex justify-between"><span class="font-medium text-gray-700">Trạng thái:</span><span>${getStatusBadge(refund.status)}</span></div>
                        <div class="flex justify-between"><span class="font-medium text-gray-700">Số tiền hoàn:</span><span class="font-bold text-green-600">${formatCurrency(refund.refund_amount)}</span></div>
                        <div class="flex justify-between"><span class="font-medium text-gray-700">Ngày tạo:</span><span class="text-gray-900">${formatDateTime(refund.created_at)}</span></div>
                        ${refund.processed_at ? `<div class="flex justify-between"><span class="font-medium text-gray-700">Ngày xử lý:</span><span class="text-gray-900">${formatDateTime(refund.processed_at)}</span></div>` : ''}
                        ${refund.completed_at ? `<div class="flex justify-between"><span class="font-medium text-gray-700">Ngày hoàn thành:</span><span class="text-gray-900">${formatDateTime(refund.completed_at)}</span></div>` : ''}
                    </div>
                </div>
            </div>
            <div>
                <h6 class="text-lg font-semibold text-gray-900 mb-3">Thông tin khách hàng</h6>
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="space-y-2">
                        <div class="flex justify-between"><span class="font-medium text-gray-700">Tên:</span><span class="text-gray-900">${refund.customer.full_name}</span></div>
                        <div class="flex justify-between"><span class="font-medium text-gray-700">Email:</span><span class="text-gray-900">${refund.customer.email}</span></div>
                        <div class="flex justify-between"><span class="font-medium text-gray-700">Số dư hiện tại:</span><span class="text-gray-900">${formatCurrency(refund.customer.balance || 0)}</span></div>
                    </div>
                </div>
                
                <h6 class="text-lg font-semibold text-gray-900 mb-3">Thông tin đơn hàng</h6>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="space-y-2">
                        <div class="flex justify-between"><span class="font-medium text-gray-700">Mã đơn:</span><span class="text-gray-900">${refund.order.order_code}</span></div>
                        <div class="flex justify-between"><span class="font-medium text-gray-700">Tổng tiền:</span><span class="text-gray-900">${formatCurrency(refund.order.total_amount)}</span></div>
                        <div class="flex justify-between"><span class="font-medium text-gray-700">Trạng thái:</span><span class="text-gray-900">${refund.order.status}</span></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6">
            <h6 class="text-lg font-semibold text-gray-900 mb-3">Lý do hoàn tiền</h6>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <p class="text-gray-700">${refund.reason || 'Không có lý do cụ thể'}</p>
            </div>
            
            ${refund.admin_note ? `
                <h6 class="text-lg font-semibold text-gray-900 mb-3 mt-4">Ghi chú của admin</h6>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-yellow-800">${refund.admin_note}</p>
                </div>
            ` : ''}
            
            ${refund.processed_by ? `
                <h6 class="text-lg font-semibold text-gray-900 mb-3 mt-4">Người xử lý</h6>
                <p class="text-gray-700">${refund.processed_by.full_name} (${refund.processed_by.email})</p>
            ` : ''}
        </div>
        
        ${refund.images && refund.images.length > 0 ? `
            <div class="mt-6">
                <h6 class="text-lg font-semibold text-gray-900 mb-3">Hình ảnh đính kèm</h6>
                <div class="flex flex-wrap gap-2">
                    ${refund.images.map(img => `<img src="${img}" class="rounded-lg border border-gray-200 object-cover" style="max-width: 150px; max-height: 150px;">`).join('')}
                </div>
            </div>
        ` : ''}
    `;
    
    $('#refundDetailContent').html(html);
}

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount || 0);
}

function formatDateTime(dateString) {
    return new Date(dateString).toLocaleString('vi-VN');
}

function showAlert(type, message) {
    // Implementation depends on your alert system
    // This is a basic example using browser alert
    if (type === 'success') {
        alert('Thành công: ' + message);
    } else {
        alert('Lỗi: ' + message);
    }
}

// Modal management functions for Tailwind modals
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('bg-opacity-75')) {
        const modal = event.target.closest('[id$="Modal"]');
        if (modal) {
            closeModal(modal.id);
        }
    }
});
</script>
@endpush