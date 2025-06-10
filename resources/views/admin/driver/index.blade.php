@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Quản lý tài xế')
@section('description', 'Quản lý danh sách tài xế của bạn')

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
        flex-wrap: wrap;
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
    }

    .data-table-search input {
        width: 100%;
        padding: 0.5rem 0.75rem 0.5rem 2.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    .data-table-container {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
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
        flex-wrap: wrap;
    }

    .data-table-pagination-controls {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
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
        text-decoration: none;
        color: #374151;
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
        border: 1px solid transparent;
    }

    .data-table-action-btn:hover {
        background-color: #f3f4f6;
        color: #374151;
        text-decoration: none;
    }

    .data-table-action-btn.edit:hover {
        background-color: #dbeafe;
        color: #1d4ed8;
    }

    .data-table-action-btn.delete:hover {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .data-table-status {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        transition: all 0.2s ease;
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
        flex-wrap: wrap;
        gap: 1rem;
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
        text-decoration: none;
        color: #374151;
    }

    .data-table-btn-primary {
        background-color: #3b82f6;
        color: white;
        border: 1px solid #3b82f6;
    }

    .data-table-btn-primary:hover {
        background-color: #2563eb;
        border-color: #2563eb;
        text-decoration: none;
        color: white;
    }

    .data-table-header-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .data-table-card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
        margin: 0;
    }

    .data-table-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .data-table-driver-image img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #f3f4f6;
    }

    .data-table-driver-name {
        font-weight: 600;
        color: #111827;
    }

    .data-table-vehicle-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 0.375rem;
    }

    .badge-info {
        background-color: #dbeafe;
        color: #1d4ed8;
    }

    .badge-secondary {
        background-color: #f3f4f6;
        color: #374151;
    }

    .data-table-rating {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-weight: 600;
    }

    .data-table-id {
        font-weight: 600;
        color: #3b82f6;
    }

    /* Dropdown improvements */
    .dropdown-menu {
        border-radius: 0.5rem;
        border: none;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        padding: 0.5rem;
    }

    .dropdown-item {
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: #f3f4f6;
    }

    @media (max-width: 768px) {
        .data-table-main-header {
            flex-direction: column;
            align-items: stretch;
        }

        .data-table-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .data-table-search {
            min-width: auto;
            width: 100%;
        }

        .data-table-actions {
            justify-content: flex-start;
        }

        .data-table-footer {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
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
                <h1 class="data-table-title">Quản lý tài xế</h1>
                <p class="text-muted-foreground">Quản lý danh sách tài xế của bạn</p>
            </div>
        </div>
        <div class="data-table-header-actions">
            <div class="dropdown">
                <button type="button" class="data-table-btn data-table-btn-outline dropdown-toggle" data-toggle="dropdown">
                    <i class="fas fa-download"></i> Xuất
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('admin.drivers.export', ['type' => 'excel']) }}">
                        <i class="fas fa-file-excel text-success"></i> Xuất Excel
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.drivers.export', ['type' => 'pdf']) }}">
                        <i class="fas fa-file-pdf text-danger"></i> Xuất PDF
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.drivers.export', ['type' => 'csv']) }}">
                        <i class="fas fa-file-csv text-info"></i> Xuất CSV
                    </a>
                </div>
            </div>
            <a href="{{ route('admin.drivers.applications.index') }}" class="data-table-btn data-table-btn-primary">
                <i class="fas fa-list"></i> Đơn ứng tuyển
            </a>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="data-table-card">
        <!-- Table Header -->
        <div class="data-table-header">
            <h2 class="data-table-card-title">Danh sách tài xế</h2>
        </div>

        <!-- Controls -->
        <div class="data-table-controls">
            <div class="data-table-search">
                <i class="fas fa-search data-table-search-icon"></i>
                <input type="text" 
                    placeholder="Tìm kiếm theo tên, email, số điện thoại..." 
                    id="dataTableSearch"
                    value="{{ request('search') }}"
                    onkeyup="handleSearch(event)">
            </div>
            <div class="data-table-actions">
                <button class="data-table-btn data-table-btn-outline" onclick="toggleSelectAll()">
                    <i class="fas fa-check-square"></i> Chọn tất cả
                </button>
                <div class="dropdown">
                    <button type="button" class="data-table-btn data-table-btn-outline dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-tasks"></i>Thao tác
                    </button>
                </div>
                <button class="data-table-btn data-table-btn-outline">
                    <i class="fas fa-columns"></i> Cột
                </button>
            </div>
        </div>

        <!-- Table Container -->
        <div class="data-table-container">
            <table class="data-table" id="dataTable">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th data-sort="id" class="active-sort">
                            ID <i class="fas fa-arrow-up data-table-sort-icon"></i>
                        </th>
                        <th>Ảnh đại diện</th>
                        <th data-sort="name">
                            Tên tài xế <i class="fas fa-sort data-table-sort-icon"></i>
                        </th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Phương tiện</th>
                        <th>Đánh giá</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="dataTableBody">
                    @forelse($drivers as $driver)
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" value="{{ $driver->id }}">
                            </td>
                            <td>
                                <div class="data-table-id">
                                    #{{ $driver->id }}
                                </div>
                            </td>
                            <td>
                                <div class="data-table-driver-image">
                                    @if($driver->profile_image)
                                        <img src="{{ asset($driver->profile_image) }}" alt="{{ $driver->full_name }}">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($driver->full_name) }}&background=eee&color=555&size=50" 
                                            alt="{{ $driver->full_name }}">
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="data-table-driver-name">{{ $driver->full_name }}</div>
                            </td>
                            <td>{{ $driver->email }}</td>
                            <td>{{ $driver->phone_number }}</td>
                            <td>
                                <div class="data-table-vehicle-info">
                                    <span class="badge badge-info">{{ $driver->vehicle_type }}</span>
                                    <span class="badge badge-secondary">{{ $driver->vehicle_color }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="data-table-rating">
                                    <i class="fas fa-star text-warning"></i>
                                    {{ number_format($driver->rating, 1) }}
                                </div>
                            </td>
                            <td>
                                <form action="" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="button"
                                        class="data-table-status {{ $driver->status === 'active' ? 'data-table-status-success' : 'data-table-status-failed' }}"
                                        style="border: none; cursor: pointer;"
                                        onclick="dtmodalHandleStatusToggle({
                                            button: this,
                                            driverName: '{{ $driver->full_name }}',
                                            currentStatus: {{ $driver->status === 'active' ? 'true' : 'false' }},
                                            confirmTitle: 'Xác nhận thay đổi trạng thái',
                                            confirmSubtitle: 'Bạn có chắc chắn muốn thay đổi trạng thái của tài xế này?',
                                            confirmMessage: 'Hành động này sẽ thay đổi trạng thái hoạt động của tài xế.',
                                            successMessage: 'Đã thay đổi trạng thái tài xế thành công',
                                            errorMessage: 'Có lỗi xảy ra khi thay đổi trạng thái tài xế'
                                        })">
                                        @if ($driver->status === 'active')
                                            Hoạt động
                                        @else
                                             Tạm nghỉ
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="data-table-action-buttons">
                                    <a href="{{ route('admin.drivers.show', $driver) }}"
                                        class="data-table-action-btn" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.drivers.edit', $driver) }}"
                                        class="data-table-action-btn edit" title="Chỉnh sửa">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.drivers.destroy', $driver) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="data-table-action-btn delete" title="Xóa"
                                            onclick="dtmodalConfirmDelete({
                                                itemName: '{{ $driver->full_name }}',
                                                onConfirm: () => this.closest('form').submit()
                                            })">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">
                                <div class="data-table-empty">
                                    <div class="data-table-empty-icon">
                                        <i class="fas fa-user-slash"></i>
                                    </div>
                                    <h3>Không có tài xế nào</h3>
                                    <p class="text-muted">Chưa có tài xế nào được thêm vào hệ thống.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="data-table-footer">
            <div class="data-table-pagination-info">
                Hiển thị <span id="startRecord">{{ ($drivers->currentPage() - 1) * $drivers->perPage() + 1 }}</span>
                đến <span id="endRecord">{{ min($drivers->currentPage() * $drivers->perPage(), $drivers->total()) }}</span>
                của <span id="totalRecords">{{ $drivers->total() }}</span> mục
            </div>
            @if ($drivers->lastPage() > 1)
                <div class="data-table-pagination-controls">
                    @if (!$drivers->onFirstPage())
                        <a href="{{ $drivers->previousPageUrl() }}&search={{ request('search') }}"
                            class="data-table-pagination-btn" id="prevBtn">
                            <i class="fas fa-chevron-left"></i> Trước
                        </a>
                    @endif

                    @php
                        $start = max(1, $drivers->currentPage() - 2);
                        $end = min($drivers->lastPage(), $drivers->currentPage() + 2);

                        if ($start > 1) {
                            echo '<a href="' . $drivers->url(1) . '&search=' . request('search') . '" class="data-table-pagination-btn">1</a>';
                            if ($start > 2) {
                                echo '<span class="data-table-pagination-dots">...</span>';
                            }
                        }
                    @endphp

                    @for ($i = $start; $i <= $end; $i++)
                        <a href="{{ $drivers->url($i) }}&search={{ request('search') }}"
                            class="data-table-pagination-btn {{ $drivers->currentPage() == $i ? 'active' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor

                    @php
                        if ($end < $drivers->lastPage()) {
                            if ($end < $drivers->lastPage() - 1) {
                                echo '<span class="data-table-pagination-dots">...</span>';
                            }
                            echo '<a href="' . $drivers->url($drivers->lastPage()) . '&search=' . request('search') . '" class="data-table-pagination-btn">' . $drivers->lastPage() . '</a>';
                        }
                    @endphp

                    @if ($drivers->hasMorePages())
                        <a href="{{ $drivers->nextPageUrl() }}&search={{ request('search') }}"
                            class="data-table-pagination-btn" id="nextBtn">
                            Tiếp <i class="fas fa-chevron-right"></i>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function handleSearch(event) {
        const searchValue = event.target.value.trim();
        const currentUrl = new URL(window.location.href);

        if (searchValue) {
            currentUrl.searchParams.set('search', searchValue);
        } else {
            currentUrl.searchParams.delete('search');
        }

        if (event.key === 'Enter') {
            window.location.href = currentUrl.toString();
        }
    }

    function toggleSelectAll() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const rowCheckboxes = document.getElementsByClassName('row-checkbox');

        selectAllCheckbox.checked = !selectAllCheckbox.checked;
        for (let checkbox of rowCheckboxes) {
            checkbox.checked = selectAllCheckbox.checked;
        }
    }

    document.getElementById('selectAll').addEventListener('change', function() {
        const rowCheckboxes = document.getElementsByClassName('row-checkbox');
        for (let checkbox of rowCheckboxes) {
            checkbox.checked = this.checked;
        }
    });

    function updateSelectedStatus(status) {
        const selectedIds = [];
        const rowCheckboxes = document.getElementsByClassName('row-checkbox');
        
        for (let checkbox of rowCheckboxes) {
            if (checkbox.checked) {
                selectedIds.push(checkbox.value);
            }
        }

        if (selectedIds.length === 0) {
            alert('Vui lòng chọn ít nhất một tài xế');
            return;
        }

        // Implement status update logic here
        console.log('Updating status to:', status, 'for drivers:', selectedIds);
    }
</script>
@endsection 