@extends('layouts.admin.contentLayoutMaster')

@section('content')
<style>
    /* Kế thừa toàn bộ style từ trang products */
    @stack('product-styles')

    /* Custom styles cho users */
    .user-status-tag {
        @apply px-3 py-1 rounded-full text-sm font-medium transition-colors;
    }

    .status-active {
        @apply bg-green-100 text-green-800;
    }

    .status-inactive {
        @apply bg-red-100 text-red-800;
    }
</style>

<div class="fade-in flex flex-col gap-4 pb-4">
    <!-- Main Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Quản lý người dùng</h2>
                <p class="text-muted-foreground">Quản lý tài khoản người dùng hệ thống</p>
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
                        <a href="{{ route('admin.users.export', ['type' => 'excel']) }}" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
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
                        <a href="{{ route('admin.users.export', ['type' => 'pdf']) }}" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            Xuất PDF
                        </a>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
                Thêm mới
            </a>
        </div>
    </div>

    <!-- Card containing table -->
    <div class="card border rounded-lg overflow-hidden">
        <!-- Table header -->
        <div class="p-6 border-b">
            <h3 class="text-lg font-medium">Danh sách người dùng</h3>
        </div>

        <!-- Toolbar -->
        <div class="p-4 border-b flex flex-col sm:flex-row justify-between gap-4">
            <div class="relative w-full sm:w-auto sm:min-w-[300px]">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                <input type="text" placeholder="Tìm kiếm theo tên, email..." class="border rounded-md px-3 py-2 bg-background text-sm w-full pl-9" id="searchInput">
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
                            <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground" onclick="updateSelectedStatus(1)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-green-500">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <path d="m9 11 3 3L22 4"></path>
                                </svg>
                                Kích hoạt đã chọn
                            </a>
                            <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground" onclick="updateSelectedStatus(0)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-red-500">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="m15 9-6 6"></path>
                                    <path d="m9 9 6 6"></path>
                                </svg>
                                Vô hiệu hóa đã chọn
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table container -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b bg-muted/50">
                        <th class="py-3 px-4 text-left">
                            <div class="flex items-center">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                            </div>
                        </th>
                        <th class="py-3 px-4 text-left font-medium">
                            <div class="flex items-center">
                                ID
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2">
                                    <path d="m18 8-6 6-6-6"></path>
                                </svg>
                            </div>
                        </th>
                        <th class="py-3 px-4 text-left font-medium">Avatar</th>
                        <th class="py-3 px-4 text-left font-medium">
                            <div class="flex items-center">
                                Họ tên
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2">
                                    <path d="m18 8-6 6-6-6"></path>
                                </svg>
                            </div>
                        </th>
                        <th class="py-3 px-4 text-left font-medium">Email</th>
                        <th class="py-3 px-4 text-left font-medium">Điện thoại</th>
                        <th class="py-3 px-4 text-left font-medium">Trạng thái</th>
                        <th class="py-3 px-4 text-center font-medium">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="py-3 px-4">
                            <input type="checkbox" class="user-checkbox" value="{{ $user->id }}">
                        </td>
                        <td class="py-3 px-4">{{ $user->id }}</td>
                        <td class="py-3 px-4">
                            <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover">
                        </td>
                        <td class="py-3 px-4 font-medium">{{ $user->full_name }}</td>
                        <td class="py-3 px-4">{{ $user->email }}</td>
                        <td class="py-3 px-4">{{ $user->phone ?? 'N/A' }}</td>
                        <td class="py-3 px-4">
                            <span class="user-status-tag {{ $user->active ? 'status-active' : 'status-inactive' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    @if($user->active)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    @endif
                                </svg>
                                {{ $user->active ? 'Hoạt động' : 'Vô hiệu' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="text-primary hover:text-primary-dark">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-6 text-center text-muted-foreground">
                            Không tìm thấy người dùng nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t">
            {{ $users->links() }}
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

    .data-table-checkbox input[type="checkbox"]:checked+label {
        background-color: #4361ee;
        border-color: #4361ee;
    }

    .data-table-checkbox input[type="checkbox"]:checked+label:after {
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
    let currentPage = {
        {
            $users - > currentPage()
        }
    };
    let currentSearch = '{{ request('
    search ') }}';

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