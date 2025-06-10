@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Quản lý mã giảm giá')
@section('description', 'Quản lý các mã giảm giá và ưu đãi')

@section('content')
<style>
    /* Basic styles */
    .card {
        background: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    /* Status badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .status-badge.active {
        background-color: #dcfce7;
        color: #15803d;
    }
    
    .status-badge.inactive {
        background-color: #fee2e2;
        color: #dc2626;
    }
    
    .status-badge.expired {
        background-color: #f3f4f6;
        color: #6b7280;
    }
    
    /* Discount type styling */
    .discount-type {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .discount-type.percentage {
        background-color: #dbeafe;
        color: #1e40af;
    }
    
    .discount-type.fixed-amount {
        background-color: #dcfce7;
        color: #15803d;
    }
    
    .discount-type.free-shipping {
        background-color: #fef3c7;
        color: #d97706;
    }
    
    /* Value display styling */
    .value-display {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 14px;
        font-weight: 500;
    }
    
    .value-display.percentage {
        background-color: #dbeafe;
        color: #1e40af;
    }
    
    .value-display.amount {
        background-color: #fef3c7;
        color: #d97706;
    }
    
    /* Date range styling */
    .date-range {
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .date-range .start-date {
        font-weight: 600;
        color: #374151;
    }
    
    /* Filter modal styling */
    .filter-modal {
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .filter-modal.hidden {
        display: none;
    }
    
    .filter-modal-content {
        background: white;
        border-radius: 8px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 32rem;
        margin: 1rem;
    }
    
    /* Statistics cards */
    .stat-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 1rem;
        transition: all 0.2s ease;
    }
    
    .stat-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .stat-icon {
        width: 16px;
        height: 16px;
        margin-right: 8px;
    }
</style>

<div class="fade-in p-4">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2Z"></path>
                    <path d="M13 5v2"></path>
                    <path d="M13 17v2"></path>
                    <path d="M13 11v2"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Quản lý mã giảm giá</h2>
                <p class="text-muted-foreground">Quản lý các mã giảm giá và ưu đãi</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div class="dropdown relative">
                <button class="btn btn-outline flex items-center dropdown-toggle" id="exportDropdown" data-dropdown="exportMenu">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Xuất
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2">
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                </button>
                <div id="exportMenu" class="hidden absolute right-0 mt-2 w-48 rounded-md border bg-popover text-popover-foreground shadow-md z-10">
                    <div class="p-2">
                        <a href="{{ route('admin.discount_codes.export') }}" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <path d="M8 13h2"></path>
                                <path d="M8 17h2"></path>
                                <path d="M14 13h2"></path>
                                <path d="M14 17h2"></path>
                            </svg>
                            Xuất Excel
                        </a>
                        <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            Xuất PDF
                        </a>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.discount_codes.create') }}" class="btn btn-primary flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
                Tạo mã giảm giá mới
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <div class="stat-card">
            <div class="flex items-center gap-2 mb-2">
                <svg class="stat-icon text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2Z"></path>
                    <path d="M13 5v2"></path>
                    <path d="M13 17v2"></path>
                    <path d="M13 11v2"></path>
                </svg>
                <span class="text-sm font-medium text-muted-foreground">Tổng mã giảm giá</span>
            </div>
            <div class="text-2xl font-bold">{{ number_format($totalCodes) }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 mb-2">
                <svg class="stat-icon text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="m9 12 2 2 4-4"></path>
                </svg>
                <span class="text-sm font-medium text-muted-foreground">Đang hoạt động</span>
            </div>
            <div class="text-2xl font-bold">{{ $activeCodes }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 mb-2">
                <svg class="stat-icon text-orange-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span class="text-sm font-medium text-muted-foreground">Sắp hết hạn</span>
            </div>
            <div class="text-2xl font-bold">{{ $expiringSoon }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 mb-2">
                <svg class="stat-icon text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="m15 9-6 6"></path>
                    <path d="m9 9 6 6"></path>
                </svg>
                <span class="text-sm font-medium text-muted-foreground">Đã hết hạn</span>
            </div>
            <div class="text-2xl font-bold">{{ $expiredCodes }}</div>
        </div>
    </div>

    <!-- Filter and Search Bar -->
    <div class="card border rounded-lg mb-4">
        <div class="p-4 flex flex-col sm:flex-row justify-between gap-4 border-b">
            <div class="relative w-full sm:w-auto sm:min-w-[300px]">
                <form id="searchForm" action="{{ route('admin.discount_codes.index') }}" method="GET" class="flex items-center">
                    <div class="relative flex-grow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </svg>
                        <input type="text" id="searchInput" name="search" placeholder="Tìm kiếm theo mã hoặc tên..." class="border rounded-md px-3 py-2 bg-background text-sm w-full pl-9" value="{{ request('search') }}">
                    </div>
                </form>
            </div>
            <div class="flex items-center gap-2">
                <button class="btn btn-outline flex items-center" id="selectAllButton">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                        <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                        <path d="m9 12 2 2 4-4"></path>
                    </svg>
                    <span>Chọn tất cả</span>
                </button>
                <div class="dropdown relative">
                    <button class="btn btn-outline flex items-center dropdown-toggle" id="actionsDropdown" data-dropdown="actionsMenu">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                            <circle cx="12" cy="12" r="2"></circle>
                            <circle cx="12" cy="5" r="2"></circle>
                            <circle cx="12" cy="19" r="2"></circle>
                        </svg>
                        Thao tác
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2">
                            <path d="m6 9 6 6 6-6"></path>
                        </svg>
                    </button>
                    <div id="actionsMenu" class="hidden absolute right-0 mt-2 w-48 rounded-md border bg-popover text-popover-foreground shadow-md z-10">
                        <div class="p-2">
                            <form action="{{ route('admin.discount_codes.bulk-status-update') }}" method="POST" id="activateForm" class="bulk-form">
                                @csrf
                                <input type="hidden" name="is_active" value="1">
                                <button type="button" class="flex w-full items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground bulk-action-btn" data-form-id="activateForm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-green-500">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <path d="m9 11 3 3L22 4"></path>
                                    </svg>
                                    Kích hoạt đã chọn
                                </button>
                            </form>
                            <form action="{{ route('admin.discount_codes.bulk-status-update') }}" method="POST" id="deactivateForm" class="bulk-form">
                                @csrf
                                <input type="hidden" name="is_active" value="0">
                                <button type="button" class="flex w-full items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground bulk-action-btn" data-form-id="deactivateForm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-red-500">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="m15 9-6 6"></path>
                                        <path d="m9 9 6 6"></path>
                                    </svg>
                                    Vô hiệu hóa đã chọn
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline flex items-center" id="filterBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    Lọc
                </button>
            </div>
        </div>
    </div>
    
    <!-- Table Section -->
    <div class="card border rounded-lg overflow-hidden">
        <div class="p-6 border-b">
            <h3 class="text-lg font-medium">Danh sách mã giảm giá</h3>
        </div>
        
        <div class="overflow-x-auto" id="discount-codes-table-container">
            @include('admin.discount_codes.partials.discount_codes_table', ['discountCodes' => $discountCodes])
        </div>

        <!-- Pagination -->
        <div id="pagination-container">
            @include('admin.discount_codes.partials.pagination', ['discountCodes' => $discountCodes])
        </div>
    </div>
    
    <!-- Filter Modal -->
    <div id="filterModal" class="filter-modal hidden">
        <div class="filter-modal-content">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-lg font-medium">Lọc mã giảm giá</h3>
                <button type="button" class="text-muted-foreground hover:text-foreground" id="closeFilterBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <form method="GET" action="{{ route('admin.discount_codes.index') }}" id="filterForm">
                <div class="p-4 space-y-6">
                    <!-- Search -->
                    <div class="space-y-2">
                        <label for="filter_search" class="text-sm font-medium">Tìm kiếm</label>
                        <input type="text" id="filter_search" name="search" value="{{ request('search') }}" class="w-full border rounded-md px-3 py-2 bg-background text-sm" placeholder="Mã hoặc tên...">
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Trạng thái</label>
                        <div class="flex flex-col gap-2">
                            <label class="flex items-center">
                                <input type="radio" name="status" value="" class="rounded border-gray-300 mr-2" {{ request('status') === '' ? 'checked' : '' }}>
                                Tất cả
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="status" value="active" class="rounded border-gray-300 mr-2" {{ request('status') === 'active' ? 'checked' : '' }}>
                                Hoạt động
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="status" value="inactive" class="rounded border-gray-300 mr-2" {{ request('status') === 'inactive' ? 'checked' : '' }}>
                                Không hoạt động
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="status" value="expired" class="rounded border-gray-300 mr-2" {{ request('status') === 'expired' ? 'checked' : '' }}>
                                Đã hết hạn
                            </label>
                        </div>
                    </div>

                    <!-- Discount Type -->
                    <div class="space-y-2">
                        <label for="filter_type" class="text-sm font-medium">Loại giảm giá</label>
                        <select id="filter_type" name="discount_type" class="w-full border rounded-md px-3 py-2 bg-background text-sm">
                            <option value="">Tất cả loại</option>
                            <option value="percentage" {{ request('discount_type') === 'percentage' ? 'selected' : '' }}>Phần trăm</option>
                            <option value="fixed_amount" {{ request('discount_type') === 'fixed_amount' ? 'selected' : '' }}>Số tiền cố định</option>
                            <option value="free_shipping" {{ request('discount_type') === 'free_shipping' ? 'selected' : '' }}>Miễn phí vận chuyển</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Khoảng thời gian</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="date_from" class="text-xs text-muted-foreground">Từ ngày</label>
                                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="w-full border rounded-md px-3 py-2 bg-background text-sm">
                            </div>
                            <div>
                                <label for="date_to" class="text-xs text-muted-foreground">Đến ngày</label>
                                <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="w-full border rounded-md px-3 py-2 bg-background text-sm">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end p-4 border-t space-x-2">
                    <button type="button" class="btn btn-outline" id="resetFilterModalBtn">Xóa bộ lọc</button>
                    <button type="button" class="btn btn-outline" id="closeFilterModalBtn">Đóng</button>
                    <button type="submit" class="btn btn-primary">Áp dụng</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Khởi tạo các animation và styles
    document.addEventListener('DOMContentLoaded', function() {
        
        // Thêm CSS cho animation
        const style = document.createElement('style');
        style.textContent = `
            .fade-out {
                opacity: 0;
                transition: opacity 0.3s ease-out;
            }
            #toast-notification {
                transition: transform 0.3s ease-out;
            }
        `;
        document.head.appendChild(style);
        
        // Kiểm tra và hiển thị toast từ localStorage nếu có
        const toastMessage = localStorage.getItem('toast_message');
        const toastType = localStorage.getItem('toast_type');
        
        if (toastMessage && toastType) {
            // Thêm một timeout nhỏ để đảm bảo DOM đã hoàn toàn tải xong
            setTimeout(() => {
                dtmodalShowToast(toastType, {
                    title: toastType === 'success' ? 'Thành công' : 'Lỗi',
                    message: toastMessage
                });
                
                // Xóa thông báo sau khi hiển thị
                localStorage.removeItem('toast_message');
                localStorage.removeItem('toast_type');
            }, 300);
        }
        
        // Tìm kiếm Ajax với debounce
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        let searchTimeout;
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(performSearch, 500);
            });
            
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                performSearch();
            });
        }
        
        // Xử lý tìm kiếm Ajax
        function performSearch() {
            const searchTerm = searchInput.value.trim();
            const tableContainer = document.getElementById('discount-codes-table-container');
            const paginationContainer = document.getElementById('pagination-container');
            
            // Hiển thị hiệu ứng loading
            tableContainer.innerHTML = `
                <div class="flex justify-center items-center py-8">
                    <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            `;
            
            // Lấy các tham số từ form
            const formData = new FormData(searchForm);
            const queryParams = new URLSearchParams(formData);
            
            // Thêm tham số Ajax
            queryParams.append('_ajax', '1');
            
            // Gửi yêu cầu tìm kiếm
            fetch(`${searchForm.action}?${queryParams.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cập nhật nội dung bảng
                    tableContainer.innerHTML = data.html;
                    paginationContainer.innerHTML = data.pagination;
                    
                    // Cập nhật lại các event listener cho các nút trong bảng
                    setupDeleteButtons();
                    setupToggleStatusButtons();
                    setupCheckboxHandlers();
                    setupBulkActionButtons();
                    setupUiEventHandlers();
                    
                    // Cập nhật URL với tham số tìm kiếm
                    const url = new URL(window.location);
                    url.searchParams.set('search', searchTerm);
                    window.history.pushState({}, '', url);
                    
                    // Hiển thị thông báo thành công nếu có
                    if (data.message) {
                        setTimeout(() => {
                            dtmodalShowToast('success', {
                                title: 'Thành công',
                                message: data.message
                            });
                        }, 300);
                    }
                } else {
                    // Hiển thị thông báo lỗi
                    dtmodalShowToast('error', {
                        title: 'Lỗi',
                        message: data.message || 'Có lỗi xảy ra khi tìm kiếm'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                dtmodalShowToast('error', {
                    title: 'Lỗi',
                    message: 'Có lỗi xảy ra khi xử lý yêu cầu'
                });
                tableContainer.innerHTML = '<div class="p-4 text-center">Có lỗi xảy ra khi tải dữ liệu</div>';
            });
        }
        
        // Xử lý sự kiện cho nút Delete
        function setupDeleteButtons() {
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const codeName = this.getAttribute('data-code');
                    confirmDelete(codeName, this);
                });
            });
        }
        
        // Xử lý sự kiện cho nút Toggle Status
        function setupToggleStatusButtons() {
            document.querySelectorAll('.toggle-status-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    toggleStatus(id, this);
                });
            });
        }
        
        // Xử lý sự kiện cho nút Bulk Action
        function setupBulkActionButtons() {
            console.log('Setting up bulk action buttons');
            document.querySelectorAll('.bulk-action-btn').forEach(button => {
                console.log('Found bulk action button:', button);
                
                // Thêm event listener mới
                button.addEventListener('click', function(e) {
                    console.log('Bulk action button clicked', this);
                    const formId = this.getAttribute('data-form-id');
                    submitBulkAction(formId);
                });
            });
        }
        
        // Khởi tạo các bulk action buttons
        setupBulkActionButtons();
        
        // Khởi tạo sự kiện cho các nút
        setupDeleteButtons();
        setupToggleStatusButtons();
        
        // ----- Dropdown Toggle -----
        // Định nghĩa toggleDropdown trong global scope để có thể gọi từ attribute onclick
        window.toggleDropdown = function(id) {
            console.log('Toggle dropdown called for', id);
            const dropdown = document.getElementById(id);
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }

        // ----- Modal Toggle -----
        window.toggleModal = function(id) {
            console.log('Toggle modal called for', id);
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.toggle('hidden');
            }
        }

        // ----- Reset Filters -----
        window.resetFilters = function() {
            const form = document.getElementById('filterForm');
            form.reset();
            window.toggleModal('filterModal');
            window.location.href = '{{ route("admin.discount_codes.index") }}';
        }
        
        // Thêm sự kiện cho các nút trong modal lọc
        const resetFilterModalBtn = document.getElementById('resetFilterModalBtn');
        const closeFilterModalBtn = document.getElementById('closeFilterBtn');
        
        if (resetFilterModalBtn) {
            resetFilterModalBtn.addEventListener('click', function() {
                window.resetFilters();
            });
        }
        
        if (closeFilterModalBtn) {
            closeFilterModalBtn.addEventListener('click', function() {
                window.toggleModal('filterModal');
            });
        }

        // Sử dụng dtmodalShowToast từ modal.js thay vì tự tạo hàm showToast

        // ----- Create Empty Row Function -----
        function createEmptyRow() {
            const tr = document.createElement('tr');
            tr.className = 'empty-row';
            tr.innerHTML = `
                <td colspan="9" class="text-center py-4">
                    <div class="flex flex-col items-center justify-center text-muted-foreground py-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mb-2">
                            <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2Z"></path>
                            <path d="M13 5v2"></path>
                            <path d="M13 17v2"></path>
                            <path d="M13 11v2"></path>
                        </svg>
                        <h3 class="text-lg font-medium">Không có mã giảm giá nào</h3>
                        <p class="text-sm">Hãy tạo mã giảm giá mới để bắt đầu</p>
                        <a href="{{ route('admin.discount_codes.create') }}" class="btn btn-primary mt-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2">
                                <path d="M5 12h14"></path>
                                <path d="M12 5v14"></path>
                            </svg>
                            Tạo mã giảm giá mới
                        </a>
                    </div>
                </td>
            `;
            return tr;
        }

        // ----- Delete Functionality -----
        function confirmDelete(codeName, button) {
            const form = button.closest('form');
            const url = form.action;
            
            // Hiển thị xác nhận bằng modal
            dtmodalConfirmIndex({
                title: "Xác nhận xóa mã giảm giá",
                subtitle: `Bạn có chắc chắn muốn xóa mã giảm giá "${codeName}"?`,
                message: "Hành động này không thể hoàn tác và sẽ xóa tất cả dữ liệu liên quan.",
                itemName: codeName,
                confirmText: "Xác nhận xóa",
                cancelText: "Hủy",
                onConfirm: function() {
                    // Hiển thị loading state
                    button.disabled = true;
                    button.innerHTML = '<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                    
                    // Gửi yêu cầu AJAX để xóa
                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Nếu thành công, xóa hàng khỏi bảng
                            const row = button.closest('tr');
                            row.classList.add('fade-out');
                            
                            // Thêm một khoảng thời gian nhỏ để hiệu ứng fade-out hoàn thành
                            setTimeout(() => {
                                row.remove();
                                
                                // Kiểm tra nếu không còn hàng nào, hiển thị thông báo trống
                                const tableBody = document.querySelector('table tbody');
                                if (tableBody.querySelectorAll('tr:not(.empty-row)').length === 0) {
                                    const emptyRow = createEmptyRow();
                                    tableBody.appendChild(emptyRow);
                                }
                                
                                // Hiển thị thông báo thành công
                                dtmodalShowToast('success', {
                                    title: 'Thành công',
                                    message: data.message
                                });
                            }, 300);
                        } else {
                            // Nếu có lỗi, hiển thị thông báo lỗi
                            dtmodalShowToast('error', {
                                title: 'Lỗi',
                                message: data.message || 'Có lỗi xảy ra'
                            });
                            button.disabled = false;
                            button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        dtmodalShowToast('error', {
                            title: 'Lỗi',
                            message: 'Có lỗi xảy ra khi xử lý yêu cầu'
                        });
                        button.disabled = false;
                        button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg>';
                    });
                }
            });
        }
        
        // ----- Submit Bulk Actions -----
        function submitBulkAction(formId) {
            const selectedCheckboxes = document.querySelectorAll('.discount-checkbox:checked');
            
            if (selectedCheckboxes.length === 0) {
                dtmodalShowToast('error', {
                    title: 'Lỗi',
                    message: 'Vui lòng chọn ít nhất một mã giảm giá'
                });
                return;
            }
            
            const form = document.getElementById(formId);
            const url = form.action;
            const formData = new FormData(form);
            
            // Thêm IDs vào formData
            selectedCheckboxes.forEach(checkbox => {
                formData.append('ids[]', checkbox.value);
            });
            
            // Xác định loại hành động
            const actionText = formId === 'activateForm' ? 'kích hoạt' : 'vô hiệu hóa';
            
            // Hiển thị xác nhận bằng modal
            dtmodalConfirmIndex({
                title: `Xác nhận ${actionText} mã giảm giá`,
                subtitle: `Bạn có chắc chắn muốn ${actionText} các mã giảm giá đã chọn?`,
                message: "Hành động này sẽ thay đổi trạng thái của các mã giảm giá.",
                itemName: `${selectedCheckboxes.length} mã giảm giá`,
                confirmText: "Xác nhận",
                cancelText: "Hủy",
                onConfirm: function() {
                    // Hiển thị loading state
                    const button = form.querySelector('button[type="button"]');
                    const originalButtonContent = button.innerHTML;
                    button.disabled = true;
                    button.innerHTML = '<svg class="animate-spin h-4 w-4 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                    
                    // Gửi yêu cầu AJAX
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Nếu thành công, cập nhật UI
                            if (formId === 'activateForm' || formId === 'deactivateForm') {
                                // Lưu thông báo vào localStorage để hiển thị sau khi tải lại trang
                                localStorage.setItem('toast_message', data.message);
                                localStorage.setItem('toast_type', 'success');
                                // Thêm một khoảng thời gian nhỏ để tránh vấn đề UI
                                setTimeout(() => {
                                    // Tải lại trang để cập nhật UI
                                    window.location.reload();
                                }, 300);
                            } else if (formId.includes('delete')) {
                                // Đối với xóa hàng loạt, xóa các hàng khỏi bảng
                                selectedCheckboxes.forEach(checkbox => {
                                    const row = checkbox.closest('tr');
                                    row.classList.add('fade-out');
                                    setTimeout(() => {
                                        row.remove();
                                    }, 300);
                                });
                                
                                // Kiểm tra nếu không còn hàng nào, hiển thị thông báo trống
                                setTimeout(() => {
                                    const tableBody = document.querySelector('table tbody');
                                    if (tableBody.querySelectorAll('tr:not(.empty-row)').length === 0) {
                                        const emptyRow = createEmptyRow();
                                        tableBody.appendChild(emptyRow);
                                    }
                                    
                                    // Reset checkbox chọn tất cả
                                    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
                                    if (selectAllCheckbox) {
                                        selectAllCheckbox.checked = false;
                                    }
                                    
                                    // Hiển thị thông báo thành công
                                    dtmodalShowToast('success', {
                                        title: 'Thành công',
                                        message: data.message
                                    });
                                }, 400);
                            }
                        } else {
                            // Nếu có lỗi, hiển thị thông báo lỗi
                            dtmodalShowToast('error', {
                                title: 'Lỗi',
                                message: data.message || 'Có lỗi xảy ra'
                            });
                        }
                        
                        // Khôi phục trạng thái nút
                        button.disabled = false;
                        button.innerHTML = originalButtonContent;
                        
                        // Đóng dropdown
                        document.getElementById('actionsMenu').classList.add('hidden');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        dtmodalShowToast('error', {
                            title: 'Lỗi',
                            message: 'Có lỗi xảy ra khi xử lý yêu cầu'
                        });
                        button.disabled = false;
                        button.innerHTML = originalButtonContent;
                    });
                }
            });
        }
        
        // ----- Toggle Status Function -----
        function toggleStatus(id, button) {
            // Hiển thị loading state
            const originalHTML = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<svg class="animate-spin h-4 w-4 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            
            // Gửi yêu cầu AJAX
            fetch(`{{ url('admin/discount-codes') }}/${id}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cập nhật badge trạng thái
                    const statusCell = button.closest('tr').querySelector('td:nth-child(8)');
                    statusCell.innerHTML = data.status_html;
                    
                    // Hiển thị thông báo thành công sau khi cập nhật UI
                    setTimeout(() => {
                        dtmodalShowToast('success', {
                            title: 'Thành công',
                            message: data.message
                        });
                    }, 300);
                } else {
                    // Hiển thị thông báo lỗi
                    dtmodalShowToast('error', {
                        title: 'Lỗi',
                        message: data.message || 'Có lỗi xảy ra'
                    });
                }
                
                // Khôi phục trạng thái nút
                button.disabled = false;
                button.innerHTML = originalHTML;
            })
            .catch(error => {
                console.error('Error:', error);
                dtmodalShowToast('error', {
                    title: 'Lỗi',
                    message: 'Có lỗi xảy ra khi xử lý yêu cầu'
                });
                button.disabled = false;
                button.innerHTML = originalHTML;
            });
        }
        
        // ----- Checkbox Functionality -----
        function setupCheckboxHandlers() {
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const discountCheckboxes = document.querySelectorAll('.discount-checkbox');
            
            // Handle select all checkbox
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const isChecked = this.checked;
                    discountCheckboxes.forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });
                });
            }
            
            // Handle individual checkboxes
            discountCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = Array.from(discountCheckboxes).every(cb => cb.checked);
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = allChecked;
                    }
                });
            });
        }
        
        // Initial setup
        setupCheckboxHandlers();
        
        // Handle select all button click
        const selectAllButton = document.getElementById('selectAllButton');
        if (selectAllButton) {
            selectAllButton.addEventListener('click', function() {
                const discountCheckboxes = document.querySelectorAll('.discount-checkbox');
                const selectAllCheckbox = document.getElementById('selectAllCheckbox');
                const isAllChecked = Array.from(discountCheckboxes).every(cb => cb.checked);
                
                discountCheckboxes.forEach(checkbox => {
                    checkbox.checked = !isAllChecked;
                });
                
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = !isAllChecked;
                }
            });
        }

        // Xử lý phân trang Ajax
        function setupPaginationLinks() {
            const paginationContainer = document.getElementById('pagination-container');
            if (paginationContainer) {
                paginationContainer.addEventListener('click', function(e) {
                    // Nếu là liên kết phân trang
                    if (e.target.tagName === 'A' || e.target.closest('a')) {
                        e.preventDefault();
                        const link = e.target.tagName === 'A' ? e.target : e.target.closest('a');
                        const url = link.getAttribute('href');
                        
                        if (url) {
                            // Lấy nội dung từ trang mới
                            fetchPage(url);
                        }
                    }
                });
            }
        }
        
        // Lấy nội dung trang qua Ajax
        function fetchPage(url) {
            const tableContainer = document.getElementById('discount-codes-table-container');
            const paginationContainer = document.getElementById('pagination-container');
            
            // Hiển thị hiệu ứng loading
            tableContainer.innerHTML = `
                <div class="flex justify-center items-center py-8">
                    <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            `;
            
            // Thêm tham số Ajax vào URL
            const ajaxUrl = new URL(url, window.location.origin);
            ajaxUrl.searchParams.append('_ajax', '1');
            
            // Gửi yêu cầu
            fetch(ajaxUrl.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cập nhật nội dung bảng
                    tableContainer.innerHTML = data.html;
                    paginationContainer.innerHTML = data.pagination;
                    
                    // Cập nhật lại các event listener cho các nút trong bảng
                    setupDeleteButtons();
                    setupToggleStatusButtons();
                    setupCheckboxHandlers();
                    setupBulkActionButtons();
                    setupUiEventHandlers();
                    
                    // Cập nhật URL
                    window.history.pushState({}, '', url);
                } else {
                    // Hiển thị thông báo lỗi
                    dtmodalShowToast('error', {
                        title: 'Lỗi',
                        message: data.message || 'Có lỗi xảy ra khi tải trang'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                dtmodalShowToast('error', {
                    title: 'Lỗi',
                    message: 'Có lỗi xảy ra khi xử lý yêu cầu'
                });
                tableContainer.innerHTML = '<div class="p-4 text-center">Có lỗi xảy ra khi tải dữ liệu</div>';
            });
        }
        
        // Khởi tạo sự kiện phân trang
        setupPaginationLinks();
        
        // Xử lý form lọc AJAX
        const filterForm = document.getElementById('filterForm');
        const resetFilterBtn = document.getElementById('resetFilterBtn');
        
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Lấy các tham số từ form
                const formData = new FormData(filterForm);
                
                // Thêm tham số Ajax
                formData.append('_ajax', '1');
                
                // Hiển thị hiệu ứng loading
                const tableContainer = document.getElementById('discount-codes-table-container');
                tableContainer.innerHTML = `
                    <div class="flex justify-center items-center py-8">
                        <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                `;
                
                // Xây dựng URL với các tham số lọc
                const queryParams = new URLSearchParams(formData);
                const url = `${filterForm.action}?${queryParams.toString()}`;
                
                // Gửi yêu cầu
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cập nhật nội dung bảng và phân trang
                        tableContainer.innerHTML = data.html;
                        document.getElementById('pagination-container').innerHTML = data.pagination;
                        
                        // Cập nhật URL với các tham số lọc
                        window.history.pushState({}, '', url);
                        
                        // Cập nhật lại các event listener
                        setupDeleteButtons();
                        setupToggleStatusButtons();
                        setupCheckboxHandlers();
                        setupBulkActionButtons();
                        setupUiEventHandlers();
                        
                        // Đóng modal lọc nếu đang mở
                        const filterModal = document.getElementById('filterModal');
                        if (filterModal && !filterModal.classList.contains('hidden')) {
                            toggleModal('filterModal');
                        }
                        
                        // Hiển thị thông báo thành công nếu có
                        if (data.message) {
                            setTimeout(() => {
                                dtmodalShowToast('success', {
                                    title: 'Thành công',
                                    message: data.message
                                });
                            }, 300);
                        }
                    } else {
                        dtmodalShowToast('error', {
                            title: 'Lỗi',
                            message: data.message || 'Có lỗi xảy ra khi áp dụng bộ lọc'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    dtmodalShowToast('error', {
                        title: 'Lỗi',
                        message: 'Có lỗi xảy ra khi xử lý yêu cầu'
                    });
                });
            });
        }
        
        // Nút reset bộ lọc
        if (resetFilterBtn) {
            resetFilterBtn.addEventListener('click', function() {
                const url = '{{ route("admin.discount_codes.index") }}';
                
                // Gửi yêu cầu AJAX để lấy dữ liệu không lọc
                fetch(`${url}?_ajax=1`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cập nhật bảng và phân trang
                        document.getElementById('discount-codes-table-container').innerHTML = data.html;
                        document.getElementById('pagination-container').innerHTML = data.pagination;
                        
                        // Cập nhật URL
                        window.history.pushState({}, '', url);
                        
                        // Cập nhật lại các event listener
                        setupDeleteButtons();
                        setupToggleStatusButtons();
                        setupCheckboxHandlers();
                        setupBulkActionButtons();
                        setupUiEventHandlers();
                        
                        // Reset form lọc
                        filterForm.reset();
                        
                        // Đóng dropdown lọc
                        const dropdownMenu = filterForm.closest('.dropdown-menu');
                        if (dropdownMenu) {
                            dropdownMenu.classList.add('hidden');
                        }
                        
                        // Hiển thị thông báo thành công
                        setTimeout(() => {
                            dtmodalShowToast('success', {
                                title: 'Thành công',
                                message: 'Đã đặt lại bộ lọc thành công'
                            });
                        }, 300);
                    } else {
                        dtmodalShowToast('error', {
                            title: 'Lỗi',
                            message: data.message || 'Có lỗi xảy ra khi đặt lại bộ lọc'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Có lỗi xảy ra khi xử lý yêu cầu');
                });
            });
        }
        
        // Xử lý các tham số lọc từ URL khi tải trang
        const initializeFiltersFromURL = () => {
            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);
            
            // Đặt giá trị cho form tìm kiếm
            if (params.has('search')) {
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.value = params.get('search');
                }
            }
            
            // Đặt giá trị cho form lọc (có thể ở cả dropdown và modal)
            if (filterForm) {
                // Đặt trạng thái
                if (params.has('status')) {
                    const statusInput = filterForm.querySelector('select[name="status"]');
                    if (statusInput) {
                        statusInput.value = params.get('status');
                    }
                }
                
                // Đặt loại giảm giá
                if (params.has('discount_type')) {
                    const discountTypeInput = filterForm.querySelector('select[name="discount_type"]');
                    if (discountTypeInput) {
                        discountTypeInput.value = params.get('discount_type');
                    }
                }
                
                // Đặt ngày từ
                if (params.has('date_from')) {
                    const dateFromInput = filterForm.querySelector('input[name="date_from"]');
                    if (dateFromInput) {
                        dateFromInput.value = params.get('date_from');
                    }
                }
                
                // Đặt ngày đến
                if (params.has('date_to')) {
                    const dateToInput = filterForm.querySelector('input[name="date_to"]');
                    if (dateToInput) {
                        dateToInput.value = params.get('date_to');
                    }
                }
            }
        };
        
        // Khởi tạo các giá trị lọc từ URL
        initializeFiltersFromURL();

        // Define helper functions for event handlers
        function filterButtonClickHandler(e) {
            e.stopPropagation(); // Ngăn sự kiện lan ra ngoài
            console.log('Filter button clicked');
            window.toggleModal('filterModal');
        }
        
        function exportDropdownClickHandler(e) {
            e.stopPropagation(); // Ngăn sự kiện lan ra ngoài
            console.log('Export dropdown clicked');
            window.toggleDropdown('exportMenu');
        }
        
        function actionsDropdownClickHandler(e) {
            e.stopPropagation(); // Ngăn sự kiện lan ra ngoài
            console.log('Actions dropdown clicked');
            window.toggleDropdown('actionsMenu');
        }
        
        function closeFilterButtonClickHandler(e) {
            e.stopPropagation(); // Ngăn sự kiện lan ra ngoài
            console.log('Close filter button clicked');
            window.toggleModal('filterModal');
        }
        
        // Khởi tạo các event listeners
        function setupUiEventHandlers() {
            // Nút lọc
            const filterBtn = document.getElementById('filterBtn');
            if (filterBtn) {
                filterBtn.removeEventListener('click', filterButtonClickHandler);
                filterBtn.addEventListener('click', filterButtonClickHandler);
            }
            
            // Export dropdown
            const exportDropdown = document.getElementById('exportDropdown');
            if (exportDropdown) {
                exportDropdown.removeEventListener('click', exportDropdownClickHandler);
                exportDropdown.addEventListener('click', exportDropdownClickHandler);
            }
            
            // Actions dropdown
            const actionsDropdown = document.getElementById('actionsDropdown');
            if (actionsDropdown) {
                actionsDropdown.removeEventListener('click', actionsDropdownClickHandler);
                actionsDropdown.addEventListener('click', actionsDropdownClickHandler);
            }
            
            // Nút đóng modal
            const closeFilterBtn = document.getElementById('closeFilterBtn');
            if (closeFilterBtn) {
                closeFilterBtn.removeEventListener('click', closeFilterButtonClickHandler);
                closeFilterBtn.addEventListener('click', closeFilterButtonClickHandler);
            }
        }
        
        // Khởi tạo tất cả sự kiện UI
        setupUiEventHandlers();
    });
</script>
@endsection