@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Quản lý đơn đăng ký tài xế')
@section('description', 'Quản lý danh sách đơn đăng ký tài xế của bạn')

@section('content')
<style>
    /* Custom input styles */
    input[type="text"],
    input[type="number"],
    input[type="date"],
    select {
        transition: all 0.2s ease;
    }

    input[type="text"]:hover,
    input[type="number"]:hover,
    input[type="date"]:hover,
    select:hover {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }

    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="date"]:focus,
    select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        outline: none;
    }

    /* Enhanced status styling */
    .status-tag {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1.25rem;
        transition: all 0.2s ease;
    }

    .status-tag.success {
        background-color: #dcfce7;
        color: #15803d;
    }

    .status-tag.failed {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    .status-tag:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Data table improvements */
    .data-table-wrapper {
        max-width: 100%;
        margin: 0 auto;
    }

    .data-table-card {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .data-table-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .data-table-controls {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }

    .data-table-search {
        position: relative;
        min-width: 300px;
    }

    .data-table-search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        pointer-events: none;
        transition: color 0.2s ease;
    }

    .data-table-search input {
        width: 100%;
        padding: 0.5rem 0.75rem 0.5rem 2.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .data-table-search input:focus + .data-table-search-icon {
        color: #3b82f6;
    }

    /* Loading states */
    .data-table-search input.loading {
        background-color: #f3f4f6;
        background-image: url("data:image/svg+xml,%3csvg width='20' height='20' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3e%3cpath fill='%236b7280' d='M10 4a6 6 0 00-6 6h2a4 4 0 014-4v-2z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: calc(100% - 0.75rem) center;
        background-size: 1rem 1rem;
        animation: spin 1s linear infinite;
    }

    .data-table-search input.loading:focus {
        background-color: #ffffff;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .data-table-container {
        overflow-x: auto;
        position: relative;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        transition: opacity 0.2s ease;
    }

    .data-table th,
    .data-table td {
        padding: 0.75rem 1rem;
        text-align: left;
        border-bottom: 1px solid #f3f4f6;
    }

    .data-table th {
        background-color: #f9fafb;
        font-weight: 500;
        color: #374151;
        cursor: pointer;
        user-select: none;
    }

    .data-table th:hover {
        background-color: #f3f4f6;
    }

    .data-table tbody tr {
        transition: all 0.2s ease;
    }

    .data-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .data-table-empty {
        padding: 3rem;
        text-align: center;
        color: #6b7280;
    }

    .data-table-empty-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .data-table-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        transition: opacity 0.2s ease;
    }

    .data-table-pagination-controls {
        display: flex;
        gap: 0.5rem;
    }

    .data-table-pagination-btn {
        padding: 0.375rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        color: #374151;
        text-decoration: none;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .data-table-pagination-btn:hover {
        background-color: #f3f4f6;
        border-color: #9ca3af;
    }

    .data-table-pagination-btn.active {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }

    .data-table-action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .data-table-action-btn {
        padding: 0.375rem;
        border-radius: 0.375rem;
        color: #6b7280;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .data-table-action-btn:hover {
        background-color: #f3f4f6;
        color: #374151;
    }

    .data-table-status {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .data-table-status-success {
        background-color: #dcfce7;
        color: #15803d;
    }

    .data-table-status-failed {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    /* Main header improvements */
    .data-table-main-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .data-table-brand {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .data-table-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        background-color: #3b82f6;
        color: white;
        border-radius: 0.5rem;
    }

    .data-table-title {
        font-size: 1.875rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .data-table-btn {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
    }

    .data-table-btn-outline {
        background-color: white;
        border: 1px solid #d1d5db;
        color: #374151;
    }

    .data-table-btn-outline:hover {
        background-color: #f9fafb;
        border-color: #9ca3af;
    }

    /* Search improvements */
    .data-table-search input::placeholder {
        color: #9ca3af;
        transition: color 0.2s ease;
    }

    .data-table-search input:focus::placeholder {
        color: #d1d5db;
    }

    /* Clear search button */
    .data-table-search-clear {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        cursor: pointer;
        opacity: 0;
        transition: all 0.2s ease;
        z-index: 10;
    }

    .data-table-search-clear:hover {
        color: #374151;
    }

    .data-table-search input:not(:placeholder-shown) + .data-table-search-icon + .data-table-search-clear {
        opacity: 1;
    }

    /* Loading overlay for tables */
    .data-table-loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease;
    }

    .data-table-loading-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .data-table-spinner {
        width: 2rem;
        height: 2rem;
        border: 2px solid #e5e7eb;
        border-top: 2px solid #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    /* No results animation */
    .data-table-empty {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .data-table-search {
            min-width: auto;
            width: 100%;
        }
        
        .data-table-controls {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<div class="fade-in flex flex-col gap-4 pb-4">
    <!-- Main Header -->
    <div class="data-table-main-header">
        <div class="data-table-brand">
            <div class="data-table-logo">
                <i class="fas fa-user-tie"></i>
            </div>
            <div>
                <h1 class="data-table-title">Quản lý đơn đăng ký tài xế</h1>
                <p class="text-muted-foreground">Quản lý danh sách đơn đăng ký tài xế của bạn</p>
            </div>
        </div>
        <div class="data-table-header-actions">
            <div class="dropdown relative">
                <button class="data-table-btn data-table-btn-outline" id="exportDropdown" onclick="toggleDropdown('exportMenu')">
                    <i class="fas fa-download"></i> Xuất
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div id="exportMenu" class="hidden absolute right-0 mt-2 w-48 rounded-md border bg-white text-gray-700 shadow-md z-10">
                    <div class="p-2">
                        <a href="{{ route('admin.drivers.applications.export', ['type' => 'excel']) }}" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-gray-100">
                            <i class="fas fa-file-excel mr-2"></i> Xuất Excel
                        </a>
                        <a href="{{ route('admin.drivers.applications.export', ['type' => 'pdf']) }}" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-gray-100">
                            <i class="fas fa-file-pdf mr-2"></i> Xuất PDF
                        </a>
                        <a href="{{ route('admin.drivers.applications.export', ['type' => 'csv']) }}" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-gray-100">
                            <i class="fas fa-file-csv mr-2"></i> Xuất CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card chứa bảng đơn chờ xử lý -->
    <div class="data-table-card">
        <!-- Tiêu đề bảng -->
        <div class="data-table-header">
            <h2 class="text-lg font-medium">Đơn đăng ký đang chờ xử lý</h2>
        </div>

        <!-- Thanh công cụ -->
        <div class="data-table-controls">
            <div class="data-table-search">
                <i class="fas fa-search data-table-search-icon"></i>
                <input type="text" name="pending_search" placeholder="Tìm kiếm theo tên, số điện thoại, biển số xe..." 
                       value="{{ $pendingSearch ?? '' }}" id="pendingTableSearch">
            </div>
            <div class="flex gap-2">
                <button id="resetPendingBtn" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm" onclick="resetPendingSearch()">
                    <i class="fas fa-times mr-1"></i> Xóa bộ lọc
                </button>
            </div>
        </div>

            <!-- Container bảng -->
            <div class="data-table-container">
                <table class="data-table" id="dataTable">
                    <thead>
                        <tr>
                            <th data-sort="id" class="active-sort">
                                ID <i class="fas fa-arrow-up data-table-sort-icon"></i>
                            </th>
                            <th data-sort="name">
                                Họ và tên <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="phone">
                                Số điện thoại <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="license">
                                Biển số xe <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="date">
                                Ngày nộp đơn <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="date">
                                Ngày cập nhật <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="pendingTableBody">
                        @forelse($pendingApplications as $application)
                            <tr>
                                <td>
                                    <div class="data-table-id">
                                        {{ $application->id }}
                                    </div>
                                </td>
                                <td>
                                    <div class="data-table-product-name">{{ $application->full_name }}</div>
                                </td>
                                <td>{{ $application->phone_number }}</td>
                                <td>{{ $application->license_plate }}</td>
                                <td>{{ $application->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $application->updated_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="data-table-action-buttons">
                                        <a href="{{ route('admin.drivers.applications.show', $application) }}"
                                            class="data-table-action-btn data-table-tooltip" data-tooltip="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="data-table-empty" id="pendingTableEmpty">
                                        <div class="data-table-empty-icon">
                                            <i class="fas fa-inbox"></i>
                                        </div>
                                        <h3>Không có đơn đăng ký nào đang chờ xử lý</h3>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        <!-- Container bảng -->
        <!-- Phân trang và thông tin -->
        <div class="data-table-footer" id="pendingPagination">
            <div class="data-table-pagination-info">
                Hiển thị <span
                    id="pendingStartRecord">{{ ($pendingApplications->currentPage() - 1) * $pendingApplications->perPage() + 1 }}</span>
                đến <span
                    id="pendingEndRecord">{{ min($pendingApplications->currentPage() * $pendingApplications->perPage(), $pendingApplications->total()) }}</span>
                của <span id="pendingTotalRecords">{{ $pendingApplications->total() }}</span> mục
            </div>
            <div class="data-table-pagination-controls">
                @if (!$pendingApplications->onFirstPage())
                    <a href="{{ $pendingApplications->previousPageUrl() }}" class="data-table-pagination-btn"
                        id="pendingPrevBtn">
                        <i class="fas fa-chevron-left"></i> Trước
                    </a>
                @endif

                @for ($i = 1; $i <= $pendingApplications->lastPage(); $i++)
                    <a href="{{ $pendingApplications->url($i) }}"
                        class="data-table-pagination-btn {{ $pendingApplications->currentPage() == $i ? 'active' : '' }}">
                        {{ $i }}
                    </a>
                @endfor

                @if ($pendingApplications->hasMorePages())
                    <a href="{{ $pendingApplications->nextPageUrl() }}" class="data-table-pagination-btn"
                        id="pendingNextBtn">
                        Tiếp <i class="fas fa-chevron-right"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Card chứa bảng đã xử lý -->
    <div class="data-table-card">
        <!-- Tiêu đề bảng -->
        <div class="data-table-header">
            <h2 class="text-lg font-medium">Đơn đăng ký đã xử lý</h2>
        </div>

        <!-- Thanh công cụ cho bảng đã xử lý -->
        <div class="data-table-controls">
            <div class="data-table-search">
                <i class="fas fa-search data-table-search-icon"></i>
                <input type="text" name="processed_search" placeholder="Tìm kiếm theo tên, số điện thoại, biển số xe..." 
                       value="{{ $processedSearch ?? '' }}" id="processedTableSearch">
            </div>
            <div class="flex gap-2">
                <button id="resetProcessedBtn" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">
                    <i class="fas fa-times mr-1"></i> Xóa bộ lọc
                </button>
            </div>
        </div>

        <!-- Container bảng -->
        <div class="data-table-container">
            <table class="data-table" id="processedTable">
                <thead>
                    <tr>
                        <th data-sort="id" class="active-sort">
                            ID <i class="fas fa-arrow-up data-table-sort-icon"></i>
                        </th>
                        <th data-sort="name">
                            Họ và tên <i class="fas fa-sort data-table-sort-icon"></i>
                        </th>
                        <th data-sort="phone">
                            Số điện thoại <i class="fas fa-sort data-table-sort-icon"></i>
                        </th>
                        <th data-sort="license">
                            Biển số xe <i class="fas fa-sort data-table-sort-icon"></i>
                        </th>
                        <th data-sort="status">
                            Trạng thái <i class="fas fa-sort data-table-sort-icon"></i>
                        </th>
                        <th data-sort="created_date">
                            Ngày nộp đơn <i class="fas fa-sort data-table-sort-icon"></i>
                        </th>
                        <th data-sort="updated_date">
                            Ngày cập nhật <i class="fas fa-sort data-table-sort-icon"></i>
                        </th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="processedTableBody">
                    @forelse($processedApplications as $application)
                        <tr>
                            <td>
                                <div class="font-medium text-blue-600">
                                    #{{ $application->id }}
                                </div>
                            </td>
                            <td>
                                <div class="font-medium">{{ $application->full_name }}</div>
                            </td>
                            <td>{{ $application->phone_number }}</td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $application->license_plate }}
                                </span>
                            </td>
                            <td>
                                <span
                                    class="data-table-status {{ $application->status === 'approved' ? 'data-table-status-success' : 'data-table-status-failed' }}">
                                    <i
                                        class="fas fa-{{ $application->status === 'approved' ? 'check' : 'times' }}"></i>
                                    {{ $application->status === 'approved' ? 'Đã duyệt' : 'Đã từ chối' }}
                                </span>
                            </td>
                            <td>{{ $application->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $application->updated_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="data-table-action-buttons">
                                    <a href="{{ route('admin.drivers.applications.show', $application) }}"
                                        class="data-table-action-btn" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                <div class="data-table-empty">
                                    <div class="data-table-empty-icon">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <h3>Không có đơn đăng ký nào đã xử lý</h3>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Phân trang và thông tin -->
        <div class="data-table-footer" id="processedPagination">
            <div class="data-table-pagination-info">
                Hiển thị
                <span id="processedStartRecord">{{ ($processedApplications->currentPage() - 1) * $processedApplications->perPage() + 1 }}</span>
                đến
                <span id="processedEndRecord">{{ min($processedApplications->currentPage() * $processedApplications->perPage(), $processedApplications->total()) }}</span>
                của <span id="processedTotalRecords">{{ $processedApplications->total() }}</span> mục
            </div>
            <div class="data-table-pagination-controls">
                @if (!$processedApplications->onFirstPage())
                    <a href="{{ $processedApplications->previousPageUrl() }}" class="data-table-pagination-btn">
                        <i class="fas fa-chevron-left"></i> Trước
                    </a>
                @endif

                @for ($i = 1; $i <= $processedApplications->lastPage(); $i++)
                    <a href="{{ $processedApplications->url($i) }}"
                        class="data-table-pagination-btn {{ $processedApplications->currentPage() == $i ? 'active' : '' }}">
                        {{ $i }}
                    </a>
                @endfor

                @if ($processedApplications->hasMorePages())
                    <a href="{{ $processedApplications->nextPageUrl() }}" class="data-table-pagination-btn">
                        Tiếp <i class="fas fa-chevron-right"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Toggle dropdown function - global scope
    function toggleDropdown(id) {
        console.log('🔽 Toggle dropdown called for:', id);
        const dropdown = document.getElementById(id);
        if (dropdown) {
            dropdown.classList.toggle('hidden');
            console.log('✅ Dropdown toggled');
        } else {
            console.error('❌ Dropdown not found:', id);
        }
    }
</script>
@endpush

<!-- Search functionality script -->
<script>
// Simple search functionality with URL management
window.addEventListener('load', function() {
    const baseUrl = "{{ route('admin.drivers.applications.index') }}";
    let pendingTimeout, processedTimeout;
    
    // Get elements
    const pendingInput = document.getElementById('pendingTableSearch');
    const processedInput = document.getElementById('processedTableSearch');
    const pendingTableBody = document.getElementById('pendingTableBody');
    const processedTableBody = document.getElementById('processedTableBody');
    const resetPendingBtn = document.getElementById('resetPendingBtn');
    const resetProcessedBtn = document.getElementById('resetProcessedBtn');
    const resetAllBtn = document.getElementById('resetAllBtn');
    
    // Read initial search values from URL
    const urlParams = new URLSearchParams(window.location.search);
    const initialPendingSearch = urlParams.get('pending_search') || '';
    const initialProcessedSearch = urlParams.get('processed_search') || '';
    
    if (pendingInput && initialPendingSearch) {
        pendingInput.value = initialPendingSearch;
    }
    
    if (processedInput && initialProcessedSearch) {
        processedInput.value = initialProcessedSearch;
    }
    
    // Update URL with search parameters
    function updateURL(pendingSearch, processedSearch) {
        const params = new URLSearchParams();
        
        if (pendingSearch.trim()) {
            params.set('pending_search', pendingSearch.trim());
        }
        
        if (processedSearch.trim()) {
            params.set('processed_search', processedSearch.trim());
        }
        
        const newUrl = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;
        
        // Only update if URL is different
        if (window.location.href !== newUrl) {
            window.history.pushState({}, '', newUrl);
        }
    }
    
    // Search function
    function performSearch() {
        const pendingSearch = pendingInput?.value || '';
        const processedSearch = processedInput?.value || '';
        
        // Update URL first
        updateURL(pendingSearch, processedSearch);
        
        const params = new URLSearchParams();
        if (pendingSearch.trim()) params.set('pending_search', pendingSearch.trim());
        if (processedSearch.trim()) params.set('processed_search', processedSearch.trim());
        
        const searchUrl = `${baseUrl}?${params.toString()}`;
        
        fetch(searchUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updatePendingTable(data.pendingApplications);
                updateProcessedTable(data.processedApplications);
            }
        })
        .catch(error => {
            console.error('Search error:', error);
        });
    }
    
    // Reset functions
    function resetPendingSearch() {
        if (pendingInput) {
            pendingInput.value = '';
            performSearch();
        }
    }
    
    function resetProcessedSearch() {
        if (processedInput) {
            processedInput.value = '';
            performSearch();
        }
    }
    
    function resetAllSearches() {
        if (pendingInput) pendingInput.value = '';
        if (processedInput) processedInput.value = '';
        performSearch();
    }
    
    // Update pending table
    function updatePendingTable(data) {
        if (!pendingTableBody) return;
        
        pendingTableBody.innerHTML = '';
        
        if (data.data.length === 0) {
            pendingTableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-8">
                        <div class="text-gray-500">
                            <i class="fas fa-search text-3xl mb-2"></i>
                            <p>Không tìm thấy đơn nào</p>
                            <small class="text-xs">Thử thay đổi từ khóa tìm kiếm</small>
                        </div>
                    </td>
                </tr>
            `;
        } else {
            data.data.forEach(app => {
                const row = `
                    <tr>
                        <td><div class="font-medium text-blue-600">#${app.id}</div></td>
                        <td><div class="font-medium">${escapeHtml(app.full_name)}</div></td>
                        <td>${escapeHtml(app.phone_number)}</td>
                        <td><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${escapeHtml(app.license_plate)}</span></td>
                        <td>${formatDate(app.created_at)}</td>
                        <td>${formatDate(app.updated_at)}</td>
                        <td>
                            <div class="data-table-action-buttons">
                                <a href="/admin/drivers/applications/${app.id}" class="data-table-action-btn" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
                pendingTableBody.innerHTML += row;
            });
        }
        
        updatePaginationInfo('pending', data);
    }
    
    // Update processed table
    function updateProcessedTable(data) {
        if (!processedTableBody) return;
        
        processedTableBody.innerHTML = '';
        
        if (data.data.length === 0) {
            processedTableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-8">
                        <div class="text-gray-500">
                            <i class="fas fa-search text-3xl mb-2"></i>
                            <p>Không tìm thấy đơn nào</p>
                            <small class="text-xs">Thử thay đổi từ khóa tìm kiếm</small>
                        </div>
                    </td>
                </tr>
            `;
        } else {
            data.data.forEach(app => {
                const statusClass = app.status === 'approved' ? 'data-table-status-success' : 'data-table-status-failed';
                const statusIcon = app.status === 'approved' ? 'check' : 'times';
                const statusText = app.status === 'approved' ? 'Đã duyệt' : 'Đã từ chối';
                
                const row = `
                    <tr>
                        <td><div class="font-medium text-blue-600">#${app.id}</div></td>
                        <td><div class="font-medium">${escapeHtml(app.full_name)}</div></td>
                        <td>${escapeHtml(app.phone_number)}</td>
                        <td><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${escapeHtml(app.license_plate)}</span></td>
                        <td><span class="data-table-status ${statusClass}"><i class="fas fa-${statusIcon}"></i> ${statusText}</span></td>
                        <td>${formatDate(app.created_at)}</td>
                        <td>${formatDate(app.updated_at)}</td>
                        <td>
                            <div class="data-table-action-buttons">
                                <a href="/admin/drivers/applications/${app.id}" class="data-table-action-btn" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
                processedTableBody.innerHTML += row;
            });
        }
        
        updatePaginationInfo('processed', data);
    }
    
    // Update pagination info
    function updatePaginationInfo(type, data) {
        const startElement = document.getElementById(`${type}StartRecord`);
        const endElement = document.getElementById(`${type}EndRecord`);
        const totalElement = document.getElementById(`${type}TotalRecords`);
        
        if (startElement) startElement.textContent = data.from || 0;
        if (endElement) endElement.textContent = data.to || 0;
        if (totalElement) totalElement.textContent = data.total || 0;
    }
    
    // Format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN') + ' ' + date.toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'});
    }
    
    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Setup search listeners
    if (pendingInput) {
        pendingInput.addEventListener('input', function(e) {
            clearTimeout(pendingTimeout);
            pendingTimeout = setTimeout(performSearch, 500);
        });
        
        // Clear on ESC key
        pendingInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                resetPendingSearch();
            }
        });
    }
    
    if (processedInput) {
        processedInput.addEventListener('input', function(e) {
            clearTimeout(processedTimeout);
            processedTimeout = setTimeout(performSearch, 500);
        });
        
        // Clear on ESC key
        processedInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                resetProcessedSearch();
            }
        });
    }
    
    // Setup reset button listeners
    if (resetPendingBtn) {
        resetPendingBtn.addEventListener('click', resetPendingSearch);
    }
    
    if (resetProcessedBtn) {
        resetProcessedBtn.addEventListener('click', resetProcessedSearch);
    }
    
    if (resetAllBtn) {
        resetAllBtn.addEventListener('click', function() {
            if (confirm('Bạn có chắc muốn xóa tất cả bộ lọc?')) {
                resetAllSearches();
            }
        });
    }
    
    // Handle browser back/forward navigation
    window.addEventListener('popstate', function(event) {
        location.reload();
    });
});
</script>
@endsection
