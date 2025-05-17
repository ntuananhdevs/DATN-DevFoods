@extends('layouts.admin.contentLayoutMaster')

@section('content')
<div class="data-table-wrapper">
    <!-- Main Header -->
    <div class="data-table-main-header">
        <div class="data-table-brand">
            <div class="data-table-logo">
                <i class="fas fa-code-branch"></i>
            </div>
            <h1 class="data-table-title">Quản lý chi nhánh</h1>
        </div>
        <div class="data-table-header-actions">
            <a href="{{ route('admin.branches.create') }}" class="data-table-btn data-table-btn-primary">
                <i class="fas fa-plus"></i> Thêm mới
            </a>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="data-table-card">
        <!-- Table Header -->
        <div class="data-table-header">
            <h2 class="data-table-card-title">Danh sách chi nhánh</h2>
        </div>

        <!-- Controls -->
        <div class="data-table-controls">
            <div class="data-table-search">
                <i class="fas fa-search data-table-search-icon"></i>
                <input type="text" 
                    placeholder="Tìm kiếm theo tên, địa chỉ, email..." 
                    id="dataTableSearch"
                    value="{{ request('search') }}"
                    onkeyup="handleSearch(event)">
            </div>
            <div class="data-table-actions">
                <button class="data-table-btn data-table-btn-outline" 
                        onclick="document.getElementById('selectAllCheckbox').click()" 
                        style="margin-right: 10px;">
                    <i class="fas fa-check-square"></i> Chọn tất cả
                </button>
                <div class="data-table-header-actions">
                    <div class="btn-group mr-2">
                        <button type="button" class="data-table-btn data-table-btn-outline dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-tasks"></i> Thao tác
                        </button>
                        <div class="dropdown-menu">
                            <a href="#" class="dropdown-item" onclick="handleBulkAction('activate')">
                                <i class="fas fa-check-circle text-success"></i> Kích hoạt đã chọn
                            </a>
                            <a href="#" class="dropdown-item" onclick="handleBulkAction('deactivate')">
                                <i class="fas fa-times-circle text-danger"></i> Vô hiệu hóa đã chọn
                            </a>
                        </div>
                    </div>
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
                        <th class="checkbox-column">
                            <div class="data-table-checkbox">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)">
                                <label for="selectAllCheckbox"></label>
                            </div>
                        </th>
                        <th data-sort="id" class="active-sort">
                            ID <i class="fas fa-arrow-up data-table-sort-icon"></i>
                        </th>
                        <th data-sort="name">
                            Tên <i class="fas fa-sort data-table-sort-icon"></i>
                        </th>
                        <th>Địa chỉ</th>
                        <th data-sort="phone">
                            Liên hệ <i class="fas fa-sort data-table-sort-icon"></i>
                        </th>
                        <th data-sort="manager">
                            Quản lý <i class="fas fa-sort data-table-sort-icon"></i>
                        </th>
                        <th>Giờ làm việc</th>
                        <th data-sort="rating">
                            Đánh giá <i class="fas fa-sort data-table-sort-icon"></i>
                        </th>
                     
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="dataTableBody">
                    @forelse($branches as $branch)
                    <tr data-branch-id="{{ $branch->id }}">
                        <td class="checkbox-column">
                            <div class="data-table-checkbox">
                                <input type="checkbox" class="branch-checkbox" id="branch-{{ $branch->id }}" value="{{ $branch->id }}">
                                <label for="branch-{{ $branch->id }}"></label>
                            </div>
                        </td>
                        <td>
                            <div class="data-table-id">
                                {{ $branch->id }}
                            </div>
                        </td>
                        <td>{{ $branch->name }}</td>
                        <td>{{ Str::limit($branch->address, 50) }}</td>
                        <td>
                            <div>
                                <div><i class="fas fa-phone"></i> {{ $branch->phone }}</div>
                                @if($branch->email)
                                <div><i class="fas fa-envelope"></i> {{ $branch->email }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($branch->manager)
                                {{ $branch->manager->name }}
                            @else
                                <span class="text-muted">Chưa phân công</span>
                            @endif
                        </td>
                        <td>{{ $branch->opening_hour }} - {{ $branch->closing_hour }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($branch->rating))
                                            <i class="fas fa-star text-warning"></i>
                                        @elseif($i - 0.5 <= $branch->rating)
                                            <i class="fas fa-star-half-alt text-warning"></i>
                                        @else
                                            <i class="far fa-star text-warning"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="ml-1">{{ number_format($branch->rating, 1) }}</span>
                            </div>
                        </td>
                        
                        <td>
                            <button type="button" 
                                class="data-table-status {{ $branch->active ? 'data-table-status-success' : 'data-table-status-failed' }}"
                                style="border: none; cursor: pointer; width: 100px;"
                                onclick="toggleBranchStatus(this, {{ $branch->id }}, '{{ $branch->name }}', {{ $branch->active ? 'true' : 'false' }})">
                                @if($branch->active)
                                    <i class="fas fa-check"></i> Hoạt động
                                @else
                                    <i class="fas fa-times"></i> Vô hiệu hóa
                                @endif
                            </button>
                        </td>
                        <td>
                            <div class="data-table-action-buttons">
                                <a href="{{ route('admin.branches.show', $branch->id) }}" 
                                   class="data-table-action-btn data-table-tooltip"
                                   data-tooltip="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                              
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center">
                            <div class="data-table-empty">
                                <div class="data-table-empty-icon">
                                    <i class="fas fa-store-slash"></i>
                                </div>
                                <h3>Không có chi nhánh nào</h3>
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
                Hiển thị <span id="startRecord">{{ ($branches->currentPage() - 1) * $branches->perPage() + 1 }}</span>
                đến <span id="endRecord">{{ min($branches->currentPage() * $branches->perPage(), $branches->total()) }}</span>
                của <span id="totalRecords">{{ $branches->total() }}</span> mục
            </div>
            @if($branches->lastPage() > 1)
            <div class="data-table-pagination-controls">
                @if(!$branches->onFirstPage())
                <a href="javascript:void(0)" onclick="loadBranches({{ $branches->currentPage() - 1 }})"
                    class="data-table-pagination-btn"
                    id="prevBtn">
                    <i class="fas fa-chevron-left"></i> Trước
                </a>
                @endif

                @php
                $start = max(1, $branches->currentPage() - 2);
                $end = min($branches->lastPage(), $branches->currentPage() + 2);
                @endphp

                @for ($i = $start; $i <= $end; $i++)
                    <a href="javascript:void(0)" onclick="loadBranches({{ $i }})"
                        class="data-table-pagination-btn {{ $branches->currentPage() == $i ? 'active' : '' }}">
                        {{ $i }}
                    </a>
                @endfor

                @if($branches->hasMorePages())
                <a href="javascript:void(0)" onclick="loadBranches({{ $branches->currentPage() + 1 }})"
                    class="data-table-pagination-btn"
                    id="nextBtn">
                    Tiếp <i class="fas fa-chevron-right"></i>
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
let searchTimeout = null;
let currentPage = {{ $branches->currentPage() }};
let currentSearch = '{{ request('search') }}';

// Hàm tải dữ liệu chi nhánh
function loadBranches(page = 1, search = currentSearch) {
    currentPage = page;
    currentSearch = search;
    
    $.ajax({
        url: '{{ route("admin.branches.index") }}',
        type: 'GET',
        data: {
            page: page,
            search: search
        },
        success: function(response) {
            if (response.success) {
                updateTable(response.branches);
                updatePagination(response.pagination);
                
                // Cập nhật URL mà không reload trang
                const url = new URL(window.location);
                url.searchParams.set('page', page);
                if (search) url.searchParams.set('search', search);
                else url.searchParams.delete('search');
                window.history.pushState({}, '', url);
            }
        },
        error: function(xhr) {
            dtmodalShowToast('error', {
                title: 'Lỗi',
                message: 'Có lỗi xảy ra khi tải dữ liệu chi nhánh'
            });
        }
    });
}

// Xử lý tìm kiếm
function handleSearch(event) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const searchValue = event.target.value;
        loadBranches(1, searchValue);
    }, 500);
}

// Hàm xử lý thay đổi trạng thái chi nhánh
function toggleBranchStatus(button, branchId, branchName, currentStatus) {
    // Configuration object for messages
    const messages = {
        confirmTitle: 'Xác nhận thay đổi trạng thái',
        confirmSubtitle: 'Bạn có chắc chắn muốn thay đổi trạng thái của chi nhánh này?',
        confirmMessage: 'Hành động này sẽ thay đổi trạng thái hoạt động của chi nhánh.',
        successMessage: 'Đã thay đổi trạng thái chi nhánh thành công',
        errorMessage: 'Có lỗi xảy ra khi thay đổi trạng thái chi nhánh'
    };

    // Sử dụng modal thay vì confirm
    dtmodalCreateModal({
        type: 'warning',
        title: messages.confirmTitle,
        subtitle: messages.confirmSubtitle,
        message: `Bạn đang thay đổi trạng thái của: <strong>"${branchName}"</strong><br>${messages.confirmMessage}`,
        confirmText: 'Xác nhận thay đổi',
        cancelText: 'Hủy bỏ',
        onConfirm: function() {
            // Send AJAX request to toggle status
            $.ajax({
                url: `{{ url('admin/branches') }}/${branchId}/toggle-status`,
                type: 'PATCH',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'PATCH'
                },
                success: function(response) {
                    if (response.success) {
                        // Update UI
                        const newStatus = !currentStatus;
                        const statusButton = $(button);
                        
                        statusButton
                            .removeClass(currentStatus ? 'data-table-status-success' : 'data-table-status-failed')
                            .addClass(newStatus ? 'data-table-status-success' : 'data-table-status-failed');
                        
                        statusButton.html(
                            newStatus ? 
                            '<i class="fas fa-check"></i> Hoạt động' :
                            '<i class="fas fa-times"></i> Vô hiệu hóa'
                        );

                        // Update onclick handler with new status
                        statusButton.attr('onclick', `toggleBranchStatus(this, ${branchId}, '${branchName}', ${newStatus})`);
                        
                        // Show success toast message instead of alert
                        dtmodalShowToast('success', {
                            title: 'Thành công',
                            message: messages.successMessage
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = messages.errorMessage;
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 404) {
                        errorMessage = 'Không tìm thấy chi nhánh';
                    } else if (xhr.status === 403) {
                        errorMessage = 'Bạn không có quyền thực hiện thao tác này';
                    } else if (xhr.status === 422) {
                        errorMessage = 'Dữ liệu không hợp lệ';
                    }

                    // Show error toast message instead of alert
                    dtmodalShowToast('error', {
                        title: 'Lỗi',
                        message: errorMessage
                    });
                }
            });
        }
    });
}

// Hàm cập nhật bảng
function updateTable(branches) {
    const tbody = $('#dataTableBody');
    let html = '';

    if (branches.length === 0) {
        html = `
            <tr>
                <td colspan="11" class="text-center">
                    <div class="data-table-empty">
                        <div class="data-table-empty-icon">
                            <i class="fas fa-store-slash"></i>
                        </div>
                        <h3>Không có chi nhánh nào</h3>
                    </div>
                </td>
            </tr>
        `;
    } else {
        branches.forEach(branch => {
            // Tạo hiển thị đánh giá sao
            let ratingStars = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= Math.floor(branch.rating)) {
                    ratingStars += '<i class="fas fa-star text-warning"></i>';
                } else if (i - 0.5 <= branch.rating) {
                    ratingStars += '<i class="fas fa-star-half-alt text-warning"></i>';
                } else {
                    ratingStars += '<i class="far fa-star text-warning"></i>';
                }
            }

            // Xác định màu cho thanh độ tin cậy
            let reliabilityClass = 'bg-danger';
            if (branch.reliability_score >= 90) {
                reliabilityClass = 'bg-success';
            } else if (branch.reliability_score >= 70) {
                reliabilityClass = 'bg-info';
            } else if (branch.reliability_score >= 50) {
                reliabilityClass = 'bg-warning';
            }

            html += `
                <tr data-branch-id="${branch.id}">
                    <td class="checkbox-column">
                        <div class="data-table-checkbox">
                            <input type="checkbox" class="branch-checkbox" id="branch-${branch.id}" value="${branch.id}">
                            <label for="branch-${branch.id}"></label>
                        </div>
                    </td>
                    <td>
                        <div class="data-table-id">${branch.id}</div>
                    </td>
                    <td>${branch.name}</td>
                    <td>${branch.address ? branch.address.substring(0, 50) + (branch.address.length > 50 ? '...' : '') : 'N/A'}</td>
                    <td>
                        <div>
                            <div><i class="fas fa-phone"></i> ${branch.phone || 'N/A'}</div>
                            ${branch.email ? `<div><i class="fas fa-envelope"></i> ${branch.email}</div>` : ''}
                        </div>
                    </td>
                    <td>${branch.manager ? branch.manager.name : '<span class="text-muted">Chưa phân công</span>'}</td>
                    <td>${branch.opening_hour} - ${branch.closing_hour}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="rating-stars">
                                ${ratingStars}
                            </div>
                            <span class="ml-1">${parseFloat(branch.rating).toFixed(1)}</span>
                        </div>
                    </td>
                    <td>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar ${reliabilityClass}" 
                                role="progressbar" 
                                style="width: ${branch.reliability_score}%;" 
                                aria-valuenow="${branch.reliability_score}" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                            </div>
                        </div>
                        <small class="text-center d-block">${branch.reliability_score}%</small>
                    </td>
                    <td>
                        <button type="button"
                            class="data-table-status ${branch.active ? 'data-table-status-success' : 'data-table-status-failed'}"
                            style="border: none; cursor: pointer; width: 100px;"
                            onclick="toggleBranchStatus(this, ${branch.id}, '${branch.name}', ${branch.active})">
                            ${branch.active 
                                ? '<i class="fas fa-check"></i> Hoạt động' 
                                : '<i class="fas fa-times"></i> Vô hiệu hóa'
                            }
                        </button>
                    </td>
                    <td>
                        <div class="data-table-action-buttons">
                            <a href="/admin/branches/${branch.id}" 
                               class="data-table-action-btn data-table-tooltip"
                               data-tooltip="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/admin/branches/${branch.id}/edit" 
                               class="data-table-action-btn edit data-table-tooltip"
                               data-tooltip="Chỉnh sửa">
                                <i class="fas fa-pen"></i>
                            </a>
                            <button type="button" 
                                class="data-table-action-btn delete data-table-tooltip"
                                data-tooltip="Xóa"
                                onclick="deleteBranch(${branch.id}, '${branch.name}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    tbody.html(html);
}

// Hàm xóa chi nhánh
function deleteBranch(branchId, branchName) {
    dtmodalConfirmDelete({
        itemName: branchName,
        onConfirm: () => {
            $.ajax({
                url: `/admin/branches/${branchId}`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'DELETE'
                },
                success: function(response) {
                    if (response.success) {
                        dtmodalShowToast('success', {
                            title: 'Thành công',
                            message: 'Đã xóa chi nhánh thành công'
                        });
                        loadBranches(currentPage, currentSearch);
                    }
                },
                error: function(xhr) {
                    dtmodalShowToast('error', {
                        title: 'Lỗi',
                        message: 'Có lỗi xảy ra khi xóa chi nhánh'
                    });
                }
            });
        }
    });
}

// Hàm cập nhật phân trang
function updatePagination(pagination) {
    const paginationInfo = $('.data-table-pagination-info');
    const paginationControls = $('.data-table-pagination-controls');
    
    // Cập nhật thông tin phân trang
    const start = (pagination.current_page - 1) * pagination.per_page + 1;
    const end = Math.min(pagination.current_page * pagination.per_page, pagination.total);
    
    paginationInfo.html(`
        Hiển thị <span id="startRecord">${start}</span>
        đến <span id="endRecord">${end}</span>
        của <span id="totalRecords">${pagination.total}</span> mục
    `);

    // Tạo nút phân trang
    if (pagination.last_page > 1) {
        let html = '';
        
        // Nút Previous
        if (pagination.current_page > 1) {
            html += `
                <a href="javascript:void(0)" 
                   onclick="loadBranches(${pagination.current_page - 1})"
                   class="data-table-pagination-btn">
                    <i class="fas fa-chevron-left"></i> Trước
                </a>
            `;
        }

        // Các nút số trang
        const start = Math.max(1, pagination.current_page - 2);
        const end = Math.min(pagination.last_page, pagination.current_page + 2);

        for (let i = start; i <= end; i++) {
            html += `
                <a href="javascript:void(0)"
                   onclick="loadBranches(${i})"
                 
                    ${i}
                </a>
            `;
        }

        // Nút Next
        if (pagination.current_page < pagination.last_page) {
            html += `
                <a href="javascript:void(0)"
                   onclick="loadBranches(${pagination.current_page + 1})"
                   class="data-table-pagination-btn">
                    Tiếp <i class="fas fa-chevron-right"></i>
                </a>
            `;
        }

        paginationControls.html(html);
    } else {
        paginationControls.empty();
    }
}

// Hàm xử lý chọn tất cả
function toggleSelectAll(checkbox) {
    const isChecked = checkbox.checked;
    $('.branch-checkbox').prop('checked', isChecked);
    updateBulkActionsVisibility();
}

// Hàm cập nhật hiển thị các nút hành động hàng loạt
function updateBulkActionsVisibility() {
    const checkedCount = $('.branch-checkbox:checked').length;
    if (checkedCount > 0) {
        $('#bulkActionsContainer').show();
    } else {
        $('#bulkActionsContainer').hide();
    }
}

// Hàm xử lý hành động hàng loạt
function handleBulkAction(action) {
    const selectedIds = [];
    $('.branch-checkbox:checked').each(function() {
        selectedIds.push($(this).val());
    });

    if (selectedIds.length === 0) {
        dtmodalShowToast('error', {
            title: 'Lỗi',
            message: 'Vui lòng chọn ít nhất một chi nhánh'
        });
        return;
    }

    let confirmMessage = '';
    const actionUrl = '{{ route("admin.branches.bulk-status-update") }}';

    switch (action) {
        case 'activate':
            confirmMessage = 'Bạn có chắc chắn muốn kích hoạt các chi nhánh đã chọn?';
            break;
        case 'deactivate':
            confirmMessage = 'Bạn có chắc chắn muốn vô hiệu hóa các chi nhánh đã chọn?';
            break;
        default:
            return;
    }

    dtmodalCreateModal({
        type: 'warning',
        title: 'Xác nhận hành động hàng loạt',
        message: `${confirmMessage}<br>Số lượng: <strong>${selectedIds.length}</strong> chi nhánh`,
        confirmText: 'Xác nhận',
        onConfirm: () => {
            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PATCH',
                    branch_ids: selectedIds,
                    action: action
                },
                success: function(response) {
                    if (response.success) {
                        dtmodalShowToast('success', {
                            title: 'Thành công',
                            message: response.message || 'Đã cập nhật trạng thái các chi nhánh thành công'
                        });
                        loadBranches(currentPage, currentSearch);
                    } else {
                        dtmodalShowToast('error', {
                            title: 'Lỗi',
                            message: response.message || 'Có lỗi xảy ra khi cập nhật trạng thái'
                        });
                    }
                },
                error: function(xhr) {
                    dtmodalShowToast('error', {
                        title: 'Lỗi',
                        message: 'Có lỗi xảy ra khi cập nhật trạng thái'
                    });
                }
            });
        }
    });
}

// Khởi tạo khi trang tải xong
$(document).ready(function() {
    // Khởi tạo tooltip
    $('[data-tooltip]').each(function() {
        new bootstrap.Tooltip(this, {
            title: $(this).data('tooltip'),
            placement: 'top'
        });
    });
});
</script>
@endsection