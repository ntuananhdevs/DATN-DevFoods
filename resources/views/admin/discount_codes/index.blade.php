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
                <button class="btn btn-outline flex items-center" id="exportDropdown" onclick="toggleDropdown('exportMenu')">
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

    <!-- Success Message -->
    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <path d="m9 11 3 3L22 4"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

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
    <div class="card border rounded-lg overflow-hidden mb-4">
        <div class="p-4 flex flex-col sm:flex-row justify-between gap-4 border-b">
            <div class="relative w-full sm:w-auto sm:min-w-[300px]">
                <form action="{{ route('admin.discount_codes.index') }}" method="GET" class="flex items-center">
                    <div class="relative flex-grow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </svg>
                        <input type="text" name="search" placeholder="Tìm kiếm theo mã hoặc tên..." class="border rounded-md px-3 py-2 bg-background text-sm w-full pl-9" value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="ml-2 btn btn-default">Tìm</button>
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
                    <button class="btn btn-outline flex items-center" id="actionsDropdown" onclick="toggleDropdown('actionsMenu')">
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
                                <button type="button" onclick="submitBulkAction('activateForm')" class="flex w-full items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
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
                                <button type="button" onclick="submitBulkAction('deactivateForm')" class="flex w-full items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
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
                <button class="btn btn-outline flex items-center" onclick="toggleModal('filterModal')">
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
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b bg-muted/50">
                        <th class="py-3 px-4 text-left font-medium">
                            <input type="checkbox" id="selectAllCheckbox" class="rounded border-gray-300">
                        </th>
                        <th class="py-3 px-4 text-left font-medium">Mã</th>
                        <th class="py-3 px-4 text-left font-medium">Tên</th>
                        <th class="py-3 px-4 text-left font-medium">Loại giảm giá</th>
                        <th class="py-3 px-4 text-center font-medium">Giá trị</th>
                        <th class="py-3 px-4 text-center font-medium">Hiệu lực</th>
                        <th class="py-3 px-4 text-center font-medium">Loại sử dụng</th>
                        <th class="py-3 px-4 text-left font-medium">Trạng thái</th>
                        <th class="py-3 px-4 text-center font-medium">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($discountCodes as $code)
                    <tr class="border-b hover:bg-muted/20">
                        <td class="py-3 px-4">
                            <input type="checkbox" name="ids[]" value="{{ $code->id }}" class="discount-checkbox rounded border-gray-300">
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-mono font-medium">{{ $code->code }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <div>
                                <div class="font-medium">{{ $code->name }}</div>
                                <div class="text-sm text-muted-foreground">{{ Str::limit($code->description ?? '', 50) }}</div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            @php
                                $typeClass = 'percentage';
                                $typeText = 'Phần trăm';
                                switch($code->discount_type) {
                                    case 'fixed_amount':
                                        $typeClass = 'fixed-amount';
                                        $typeText = 'Số tiền cố định';
                                        break;
                                    case 'free_shipping':
                                        $typeClass = 'free-shipping';
                                        $typeText = 'Miễn phí vận chuyển';
                                        break;
                                }
                            @endphp
                            <span class="discount-type {{ $typeClass }}">{{ $typeText }}</span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($code->discount_type == 'percentage')
                                <span class="value-display percentage">{{ $code->discount_value }}%</span>
                            @elseif($code->discount_type == 'fixed_amount')
                                <span class="value-display amount">{{ number_format($code->discount_value) }} đ</span>
                            @else
                                <span class="value-display">Miễn phí ship</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="date-range">
                                <div class="start-date">{{ $code->start_date->format('d/m/Y') }}</div>
                                <div>đến {{ $code->end_date->format('d/m/Y') }}</div>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-1 rounded-full text-xs {{ $code->usage_type === 'public' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $code->usage_type === 'public' ? 'Công khai' : 'Cá nhân' }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            @php
                                $now = now();
                                if (!$code->is_active) {
                                    $status = 'inactive';
                                    $statusText = 'Không hoạt động';
                                } elseif ($now->gt($code->end_date)) {
                                    $status = 'expired';
                                    $statusText = 'Đã hết hạn';
                                } else {
                                    $status = 'active';
                                    $statusText = 'Hoạt động';
                                }
                            @endphp
                            <span class="status-badge {{ $status }}">{{ $statusText }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex justify-center space-x-1">
                                <a href="{{ route('admin.discount_codes.show', $code->id) }}"
                                    class="flex items-center justify-center rounded-md hover:bg-accent p-2"
                                    title="Xem chi tiết">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.discount_codes.edit', $code->id) }}"
                                    class="flex items-center justify-center rounded-md hover:bg-accent p-2"
                                    title="Chỉnh sửa">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.discount_codes.destroy', $code->id) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="h-8 w-8 p-0 flex items-center justify-center rounded-md hover:bg-accent"
                                        onclick="confirmDelete('{{ $code->code }}', this)"
                                        title="Xóa">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18"></path>
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
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
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($discountCodes->hasPages())
        <div class="flex items-center justify-between px-4 py-4 border-t">
            <div class="text-sm text-muted-foreground">
                Hiển thị {{ $discountCodes->firstItem() }} đến {{ $discountCodes->lastItem() }} của {{ $discountCodes->total() }} mục
            </div>
            <div class="flex items-center space-x-2">
                {{ $discountCodes->links() }}
            </div>
        </div>
        @endif
    </div>
    
    <!-- Filter Modal -->
    <div id="filterModal" class="filter-modal hidden">
        <div class="filter-modal-content">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-lg font-medium">Lọc mã giảm giá</h3>
                <button type="button" class="text-muted-foreground hover:text-foreground" onclick="toggleModal('filterModal')">
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
                    <button type="button" class="btn btn-outline" onclick="resetFilters()">Xóa bộ lọc</button>
                    <button type="button" class="btn btn-outline" onclick="toggleModal('filterModal')">Đóng</button>
                    <button type="submit" class="btn btn-primary">Áp dụng</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // ----- Dropdown Toggle -----
    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }
    }

    // ----- Modal Toggle -----
    function toggleModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.toggle('hidden');
        }
    }

    // ----- Reset Filters -----
    function resetFilters() {
        const form = document.getElementById('filterForm');
        form.reset();
        toggleModal('filterModal');
        window.location.href = '{{ route("admin.discount_codes.index") }}';
    }

    // ----- Confirm Delete -----
    function confirmDelete(codeName, button) {
        if (confirm(`Bạn có chắc chắn muốn xóa mã giảm giá "${codeName}"?`)) {
            button.closest('form').submit();
        }
    }
    
    // ----- Submit Bulk Actions -----
    function submitBulkAction(formId) {
        const selectedCheckboxes = document.querySelectorAll('.discount-checkbox:checked');
        
        if (selectedCheckboxes.length === 0) {
            alert('Vui lòng chọn ít nhất một mã giảm giá');
            return;
        }
        
        const form = document.getElementById(formId);
        
        // Clear any existing hidden inputs for IDs
        const existingInputs = form.querySelectorAll('input[name="ids[]"]');
        existingInputs.forEach(input => input.remove());
        
        // Add hidden inputs for each selected ID
        selectedCheckboxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = checkbox.value;
            form.appendChild(input);
        });
        
        // Submit the form
        form.submit();
    }
    
    // ----- Initialize on DOM Ready -----
    document.addEventListener('DOMContentLoaded', function() {
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('.dropdown > div:not(.hidden)');
            dropdowns.forEach(dropdown => {
                const isClickInside = dropdown.contains(event.target) || 
                                     dropdown.previousElementSibling.contains(event.target);
                
                if (!isClickInside) {
                    dropdown.classList.add('hidden');
                }
            });
        });

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
    });
</script>
@endsection

@endsection

