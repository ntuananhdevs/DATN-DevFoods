@extends('layouts.admin.contentLayoutMaster')

@section('content')
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
                <h2 class="text-3xl font-bold tracking-tight">Quản lý tài khoản người dùng  </h2>
                <p class="text-muted-foreground">Quản lý tài khoản người dùng của hệ thống</p>
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
    <!-- Data Table Card -->
    <div class="bg-white rounded-lg shadow-md">
        <!-- Table Header -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Danh sách người dùng </h2>
        </div>

        <!-- Controls -->
        <div class="p-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="relative w-full md:w-1/3">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text"
                       placeholder="Tìm kiếm theo tên, email, số điện thoại..."
                       id="dataTableSearch"
                       value="{{ request('search') }}"
                       onkeyup="handleSearch(event)"
                       class="w-full pl-10 pr-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex items-center space-x-2">
                <button class="border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-100 flex items-center space-x-2 transition-colors duration-200"
                        onclick="document.getElementById('selectAllCheckbox').click()">
                    <i class="fas fa-check-square"></i>
                    <span>Chọn tất cả</span>
                </button>
                <div class="relative" x-data="{ open: false }" @click.away="open = false" @click.outside="open = false">
                    <button type="button"
                            @click="open = !open; $event.stopPropagation();"
                            class="border border-gray-300 px-3 py-1.5 rounded-md hover:bg-gray-100 flex items-center space-x-2 transition-colors duration-200">
                        <i class="fas fa-tasks"></i>
                        <span>Thao tác</span>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute right-0 mt-2 w-50 bg-white border rounded-md shadow-lg z-10 whitespace-nowrap"
                         @click="open = false">
                        <a href="#" class="block px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-2"
                           @click.prevent="handleBulkAction('activate'); open = false">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span class="whitespace-nowrap">Kích hoạt đã chọn</span>
                        </a>
                        <a href="#" class="block px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-2"
                           @click.prevent="handleBulkAction('deactivate'); open = false">
                            <i class="fas fa-times-circle text-red-500"></i>
                            <span class="whitespace-nowrap">Vô hiệu hóa đã chọn</span>
                        </a>
                    </div>
                </div>
                <button class="border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-100 flex items-center space-x-2 transition-colors duration-200">
                    <i class="fas fa-columns"></i>
                    <span>Cột</span>
                </button>
            </div>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto">
            <table class="w-full" id="dataTable">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="w-12 text-center py-3">
                            <div class="flex items-center justify-center">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)"
                                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </div>
                        </th>
                        <th data-sort="id" class="px-4 py-3 text-left text-gray-600 font-medium">
                            ID <i class="fas fa-arrow-up ml-1"></i>
                        </th>
                        <th class="px-4 py-3 text-left text-gray-600 font-medium">Avatar</th>
                        <th data-sort="name" class="px-4 py-3 text-left text-gray-600 font-medium">
                            Tên <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th data-sort="email" class="px-4 py-3 text-left text-gray-600 font-medium">
                            Email <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th data-sort="phone" class="px-4 py-3 text-left text-gray-600 font-medium">
                            Điện thoại <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="px-4 py-3 text-left text-gray-600 font-medium">Trạng thái</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-medium">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="dataTableBody">
                    @forelse($users as $user)
                    <tr data-user-id="{{ $user->id }}" class="border-b hover:bg-gray-50 transition-colors duration-200">
                        <td class="w-12 text-center py-4">
                            <div class="flex items-center justify-center">
                                <input type="checkbox" class="user-checkbox h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       id="user-{{ $user->id }}" value="{{ $user->id }}">
                            </div>
                        </td>
                        <td class="px-4 py-4">{{ $user->id }}</td>
                        <td class="px-4 py-4">
                            <img src="{{ $user->avatar ? Storage::disk('s3')->url($user->avatar) : asset('images/default-avatar.png') }}"
                                 alt="{{ $user->full_name }}"
                                 class="rounded-full w-10 h-10 object-cover">
                        </td>
                        <td class="px-4 py-4">{{ $user->full_name }}</td>
                        <td class="px-4 py-4">{{ $user->email }}</td>
                        <td class="px-4 py-4">{{ $user->phone ?? 'N/A' }}</td>
                        <td class="px-4 py-4">
                            <button type="button"
                                    class="px-3 py-1.5 rounded-full text-xs whitespace-nowrap overflow-hidden {{ $user->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} hover:opacity-80 w-24 transition-opacity duration-200 flex items-center"
                                    onclick="toggleUserStatus(this, {{ $user->id }}, '{{ $user->full_name }}', {{ $user->active ? 'true' : 'false' }})">
                                @if($user->active)
                                <i class="fas fa-check mr-1"></i> <span class="inline-block">Hoạt động</span>
                                @else
                                <i class="fas fa-times mr-1"></i> <span class="inline-block">Vô hiệu hóa</span>
                                @endif
                            </button>
                        </td>
                        <td class="px-4 py-4">
                            <a href="{{ route('admin.users.show', $user->id) }}"
                               class="flex items-center justify-center rounded-md hover:bg-accent p-2"
                               title="Xem chi tiết">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-user-slash text-4xl text-gray-400 mb-2"></i>
                                <h3 class="text-lg text-gray-600">Không có người dùng nào</h3>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-gray-600">
                Hiển thị <span id="startRecord">{{ ($users->currentPage() - 1) * $users->perPage() + 1 }}</span>
                đến <span id="endRecord">{{ min($users->currentPage() * $users->perPage(), $users->total()) }}</span>
                của <span id="totalRecords">{{ $users->total() }}</span> mục
            </div>
            @if($users->lastPage() > 1)
            <div class="flex items-center space-x-2">
                @if(!$users->onFirstPage())
                <a href="javascript:void(0)" onclick="loadUsers({{ $users->currentPage() - 1 }})"
                   class="px-3 py-2 border rounded-md text-gray-600 hover:bg-gray-100 flex items-center transition-colors duration-200">
                    <i class="fas fa-chevron-left mr-2"></i> Trước
                </a>
                @endif

                @php
                $start = max(1, $users->currentPage() - 2);
                $end = min($users->lastPage(), $users->currentPage() + 2);
                @endphp

                @for ($i = $start; $i <= $end; $i++)
                    <a href="javascript:void(0)" onclick="loadUsers({{ $i }})"
                       class="px-3 py-2 border rounded-md {{ $users->currentPage() == $i ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }} transition-colors duration-200">
                        {{ $i }}
                    </a>
                @endfor

                @if($users->hasMorePages())
                <a href="javascript:void(0)" onclick="loadUsers({{ $users->currentPage() + 1 }})"
                   class="px-3 py-2 border rounded-md text-gray-600 hover:bg-gray-100 flex items-center transition-colors duration-200">
                    Tiếp <i class="fas fa-chevron-right ml-2"></i>
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

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
                url: `/admin/users/${userId}/toggle-status`,
                type: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Update UI
                        const newStatus = !currentStatus;
                        const statusButton = $(button);

                        statusButton
                            .removeClass(currentStatus ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700')
                            .addClass(newStatus ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700');

                        statusButton.html(
                            newStatus ?
                            '<i class="fas fa-check mr-1"></i> Hoạt động' :
                            '<i class="fas fa-times mr-1"></i> Vô hiệu hóa'
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
                <tr data-user-id="${user.id}" class="border-b hover:bg-gray-50 transition-colors duration-200">
                    <td class="w-12 text-center py-4">
                        <div class="flex items-center justify-center">
                            <input type="checkbox"
                                   class="user-checkbox h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                   id="user-${user.id}"
                                   value="${user.id}">
                        </div>
                    </td>
                    <td class="px-4 py-4 text-gray-700">
                        ${user.id}
                    </td>
                    <td class="px-4 py-4">
                        <img src="${avatarUrl}"
                             alt="${user.full_name}"
                             class="rounded-full w-10 h-10 object-cover">
                    </td>
                    <td class="px-4 py-4 text-gray-700">
                        ${user.full_name}
                    </td>
                    <td class="px-4 py-4 text-gray-700">
                        ${user.email}
                    </td>
                    <td class="px-4 py-4 text-gray-700">
                        ${user.phone || 'N/A'}
                    </td>
                    <td class="px-4 py-4">
                        <button type="button"
                                class="px-3 py-1.5 rounded-full text-xs ${user.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'} hover:opacity-80 w-24 transition-opacity duration-200"
                                onclick="toggleUserStatus(this, ${user.id}, '${user.full_name}', ${user.active ? 'true' : 'false'})">
                            ${user.active
                                ? '<i class="fas fa-check mr-1"></i> Hoạt động'
                                : '<i class="fas fa-times mr-1"></i> Vô hiệu hóa'
                            }
                        </button>
                    </td>
                    <td class="px-4 py-4">
                        <a href="/admin/users/show/${user.id}"
                           class="flex items-center justify-center rounded-md hover:bg-accent p-2"
                           title="Xem chi tiết">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </a>
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

// Thêm sự kiện cho dropdown
$(document).on('click', '[data-toggle="dropdown"]', function() {
    $(this).next('.dropdown-menu').toggleClass('show');
});

// Đóng dropdown khi click bên ngoài
$(document).on('click', function(e) {
    if (!$(e.target).closest('[data-toggle="dropdown"]').length && !$(e.target).closest('.dropdown-menu').length) {
        $('.dropdown-menu').removeClass('show');
    }
});
</script>
@endsection
@section('page-style')
<style>
    /* Custom styles for dropdown and checkbox */
    .dropdown-menu {
        display: none;
    }
    .dropdown-menu.show {
        display: block;
    }
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
</style>
@endsection
