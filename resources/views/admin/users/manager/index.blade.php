@extends('layouts.admin.contentLayoutMaster')

@section('content')
<div class="data-table-wrapper">
    <!-- Main Header -->
    <div class="data-table-main-header">
        <div class="data-table-brand">
            <div class="data-table-logo">
                <i class="fas fa-users"></i>
            </div>
            <h1 class="data-table-title">Tài khoản Quản Lý</h1>
        </div>
        <a href="{{ route('admin.users.managers.create') }}" class="data-table-btn data-table-btn-primary">
                <i class="fas fa-plus"></i> Thêm mới
            </a>
    </div>

    <!-- Data Table Card -->
    <div class="data-table-card">
        <!-- Table Header -->
        <div class="data-table-header">
            <h2 class="data-table-card-title">Danh sách quản lý </h2>
            
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
                <button class="data-table-btn data-table-btn-outline" 
                        onclick="document.getElementById('selectAllCheckbox').click()" 
                        style="margin-right: 10px;">
                    <i class="fas fa-check-square"></i> Chọn tất cả
                  
                </button>
                <div class="data-table-header-actions">
            <div class="btn-group mr-2">
               
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
                        <th>Avatar</th>
                        <th data-sort="name">
                            Tên <i class="fas fa-sort data-table-sort-icon"></i>
                        </th>
                        <th data-sort="email">
                            Email <i class="fas fa-sort data-table-sort-icon"></i>
                        </th>
                        <th data-sort="phone">
                            Điện thoại <i class="fas fa-sort data-table-sort-icon"></i>
                        </th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="dataTableBody">
                    @forelse($users as $user)
                    <tr data-user-id="{{ $user->id }}">
                        <td class="checkbox-column">
                            <div class="data-table-checkbox">
                                <input type="checkbox" class="user-checkbox" id="user-{{ $user->id }}" value="{{ $user->id }}">
                                <label for="user-{{ $user->id }}"></label>
                            </div>
                        </td>
                        <td>
                            <div class="data-table-id">
                                {{ $user->id }}
                            </div>
                        </td>
                        <td>
                            <div class="data-table-user-avatar">
                                <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : asset('images/default-avatar.png') }}"
                                    alt="{{ $user->full_name }}"
                                    class="rounded-circle"
                                    style="width: 40px; height: 40px; object-fit: cover;">
                            </div>
                        </td>
                        <td>
                            <div class="data-table-user-name">{{ $user->full_name }}</div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? 'N/A' }}</td>
                        <td>
                            <button type="button"
                                class="data-table-status {{ $user->active ? 'data-table-status-success' : 'data-table-status-failed' }}"
                                 style="border: none; cursor: pointer; width: 100px;"
                                onclick="toggleUserStatus(this, {{ $user->id }}, '{{ $user->full_name }}', {{ $user->active ? 'true' : 'false' }} ,
                                )">
                                @if($user->active)
                                <i class="fas fa-check"></i> Hoạt động
                                @else
                                <i class="fas fa-times"></i> Vô hiệu hóa
                                @endif
                            </button>
                        </td>
                        <td>
                            <div class="data-table-action-buttons">
                                <a href="{{ route('admin.users.show', $user->id) }}"
                                    class="data-table-action-btn data-table-tooltip"
                                    data-tooltip="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="data-table-empty">
                                <div class="data-table-empty-icon">
                                    <i class="fas fa-user-slash"></i>
                                </div>
                                <h3>Không có người dùng nào</h3>
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
                Hiển thị <span id="startRecord">{{ ($users->currentPage() - 1) * $users->perPage() + 1 }}</span>
                đến <span id="endRecord">{{ min($users->currentPage() * $users->perPage(), $users->total()) }}</span>
                của <span id="totalRecords">{{ $users->total() }}</span> mục
            </div>
            @if($users->lastPage() > 1)
            <div class="data-table-pagination-controls">
                @if(!$users->onFirstPage())
                <a href="javascript:void(0)" onclick="loadUsers({{ $users->currentPage() - 1 }})"
                    class="data-table-pagination-btn"
                    id="prevBtn">
                    <i class="fas fa-chevron-left"></i> Trước
                </a>
                @endif

                @php
                $start = max(1, $users->currentPage() - 2);
                $end = min($users->lastPage(), $users->currentPage() + 2);
                @endphp

                @for ($i = $start; $i <= $end; $i++)
                    <a href="javascript:void(0)" onclick="loadUsers({{ $i }})"
                        class="data-table-pagination-btn {{ $users->currentPage() == $i ? 'active' : '' }}">
                        {{ $i }}
                    </a>
                @endfor

                @if($users->hasMorePages())
                <a href="javascript:void(0)" onclick="loadUsers({{ $users->currentPage() + 1 }})"
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


@section('page-style')
<style>
    .checkbox-column {
        width: 40px;
        text-align: center;
    }
    
    .data-table-checkbox {
        position: relative;
        display: inline-block;
    }
    
    .data-table-checkbox input[type="checkbox"] {
        opacity: 0;
        position: absolute;
    }
    
    .data-table-checkbox label {
        position: relative;
        display: inline-block;
        width: 18px;
        height: 18px;
        border: 2px solid #ddd;
        border-radius: 3px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .data-table-checkbox input[type="checkbox"]:checked + label {
        background-color: #4361ee;
        border-color: #4361ee;
    }
    
    .data-table-checkbox input[type="checkbox"]:checked + label:after {
        content: '';
        position: absolute;
        left: 5px;
        top: 2px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }
    
    .data-table-bulk-actions {
        display: flex;
        gap: 8px;
    }
</style>
@endsection

@section('page-script')
<script>
let searchTimeout = null;
let currentPage = {{ $users->currentPage() }};
let currentSearch = '{{ request('search') }}';

// Hàm tải dữ liệu người dùng
function loadUsers(page = 1, search = currentSearch) {
    currentPage = page;
    currentSearch = search;
    
    // Hiển thị loading
    $('#dataTableBody').addClass('loading');
    
    $.ajax({
        url: '{{ route("admin.users.index") }}',
        type: 'GET',
        data: {
            page: page,
            search: search,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                updateTable(response.users);
                updatePagination(response.pagination);
                updateURL(page, search);
            }
        },
        error: function(xhr) {
            showErrorAlert('Lỗi tải dữ liệu', xhr.responseJSON?.message || 'Vui lòng thử lại sau');
        },
        complete: function() {
            // Ẩn loading khi hoàn thành
            $('#dataTableBody').removeClass('loading');
        }
    });
}

function updateURL(page, search) {
    const url = new URL(window.location);
    url.searchParams.set('page', page);
    search ? url.searchParams.set('search', search) : url.searchParams.delete('search');
    window.history.replaceState({}, '', url);
}

function showErrorAlert(title, message) {
    Swal.fire({
        icon: 'error',
        title: title,
        text: message,
        confirmButtonColor: '#4361ee',
    });
}

// Xử lý tìm kiếm
function handleSearch(event) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const searchValue = event.target.value;
        loadUsers(1, searchValue);
    }, 500);
}

// Hàm xử lý thay đổi trạng thái người dùng
function toggleUserStatus(button, userId, userName, currentStatus) {
    // Configuration object for messages
    const messages = {
        confirmTitle: 'Xác nhận thay đổi trạng thái',
        confirmSubtitle: 'Bạn có chắc chắn muốn thay đổi trạng thái của người dùng này?',
        confirmMessage: 'Hành động này sẽ thay đổi trạng thái hoạt động của người dùng.',
        successMessage: 'Đã thay đổi trạng thái người dùng thành công',
        errorMessage: 'Có lỗi xảy ra khi thay đổi trạng thái người dùng'
    };

    // Sử dụng modal thay vì confirm
    dtmodalCreateModal({
        type: 'warning',
        title: messages.confirmTitle,
        subtitle: messages.confirmSubtitle,
        message: `Bạn đang thay đổi trạng thái của: <strong>"${userName}"</strong><br>${messages.confirmMessage}`,
        confirmText: 'Xác nhận thay đổi',
        cancelText: 'Hủy bỏ',
        onConfirm: function() {
            // Send AJAX request to toggle status
            $.ajax({
                url: `{{ url('admin/users') }}/${userId}/toggle-status`,
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
                        statusButton.attr('onclick', `toggleUserStatus(this, ${userId}, '${userName}', ${newStatus})`);
                        
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
                        errorMessage = 'Không tìm thấy người dùng';
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
function updateTable(users) {
    const tbody = $('#dataTableBody');
    let html = '';

    if (users.length === 0) {
        html = `
            <tr>
                <td colspan="8" class="text-center">
                    <div class="data-table-empty">
                        <div class="data-table-empty-icon">
                            <i class="fas fa-user-slash"></i>
                        </div>
                        <h3>Không có người dùng nào</h3>
                    </div>
                </td>
            </tr>
        `;
    } else {
        users.forEach(user => {
            const avatarUrl = user.avatar ? `/storage/${user.avatar}` : '/images/default-avatar.png';
            html += `
                <tr data-user-id="${user.id}">
                    <td class="checkbox-column">
                        <div class="data-table-checkbox">
                            <input type="checkbox" class="user-checkbox" id="user-${user.id}" value="${user.id}">
                            <label for="user-${user.id}"></label>
                        </div>
                    </td>
                    <td>
                        <div class="data-table-id">${user.id}</div>
                    </td>
                    <td>
                        <div class="data-table-user-avatar">
                            <img src="${avatarUrl}" 
                                 alt="${user.full_name}"
                                 class="rounded-circle"
                                 style="width: 40px; height: 40px; object-fit: cover;">
                        </div>
                    </td>
                    <td>
                        <div class="data-table-user-name">${user.full_name}</div>
                    </td>
                    <td>${user.email}</td>
                    <td>${user.phone || 'N/A'}</td>
                    <td>
                        <button type="button"
                            class="data-table-status ${user.active ? 'data-table-status-success' : 'data-table-status-failed'}"
                            style="border: none; cursor: pointer; width: 100px;"
                            onclick="toggleUserStatus(this, ${user.id}, '${user.full_name}', ${user.active})">
                            ${user.active 
                                ? '<i class="fas fa-check"></i> Hoạt động' 
                                : '<i class="fas fa-times"></i> Vô hiệu hóa'
                            }
                        </button>
                    </td>
                    <td>
                        <div class="data-table-action-buttons">
                            <a href="/admin/users/show/${user.id}" 
                               class="data-table-action-btn data-table-tooltip"
                               data-tooltip="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    tbody.html(html);
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
                   onclick="loadUsers(${pagination.current_page - 1})"
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
                   onclick="loadUsers(${i})"
                   class="data-table-pagination-btn ${pagination.current_page === i ? 'active' : ''}">
                    ${i}
                </a>
            `;
        }

        // Nút Next
        if (pagination.current_page < pagination.last_page) {
            html += `
                <a href="javascript:void(0)"
                   onclick="loadUsers(${pagination.current_page + 1})"
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
    $('.user-checkbox').prop('checked', isChecked);
    updateBulkActionsVisibility();
}

// Hàm cập nhật hiển thị các nút hành động hàng loạt
function updateBulkActionsVisibility() {
    const checkedCount = $('.user-checkbox:checked').length;
    if (checkedCount > 0) {
        $('#bulkActionsContainer').show();
    } else {
        $('#bulkActionsContainer').hide();
    }
}

// Hàm xử lý hành động hàng loạt
function handleBulkAction(action) {
    const selectedIds = [];
    $('.user-checkbox:checked').each(function() {
        selectedIds.push($(this).val());
    });

    if (selectedIds.length === 0) {
        dtmodalShowToast('error', {
            title: 'Lỗi',
            message: 'Vui lòng chọn ít nhất một người dùng'
        });
        return;
    }

    let confirmMessage = '';
    const actionUrl = '{{ route("admin.users.bulk-status-update") }}';

    switch (action) {
        case 'activate':
            confirmMessage = 'Bạn có chắc chắn muốn kích hoạt các người dùng đã chọn?';
            break;
        case 'deactivate':
            confirmMessage = 'Bạn có chắc chắn muốn vô hiệu hóa các người dùng đã chọn?';
            break;
        default:
            return;
    }

    dtmodalCreateModal({
        type: 'warning',
        title: 'Xác nhận hành động hàng loạt',
        message: `${confirmMessage}<br>Số lượng: <strong>${selectedIds.length}</strong> người dùng`,
        confirmText: 'Xác nhận',
        onConfirm: () => {
            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PATCH',
                    action: action,
                    ids: selectedIds
                },
                success: (response) => {
                    if (response.success) {
                        dtmodalShowToast('success', {
                            title: 'Thành công',
                            message: response.message
                        });
                        loadUsers(currentPage, currentSearch);
                        $('#selectAllCheckbox').prop('checked', false);
                    }
                },
                error: (xhr) => {
                    const errorMessage = xhr.responseJSON?.message || 'Lỗi hệ thống';
                    dtmodalShowToast('error', {
                        title: 'Lỗi',
                        message: errorMessage
                    });
                }
            });
        }
    });
}

// Thêm sự kiện lắng nghe cho các checkbox
$(document).on('change', '.user-checkbox', function() {
    updateBulkActionsVisibility();
    
    // Cập nhật trạng thái của checkbox "Chọn tất cả"
    const totalCheckboxes = $('.user-checkbox').length;
    const checkedCheckboxes = $('.user-checkbox:checked').length;
    
    $('#selectAllCheckbox').prop('checked', totalCheckboxes === checkedCheckboxes);
});
</script>
@endsection