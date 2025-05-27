@extends('layouts.admin.contentLayoutMaster')

@section('content')
<style>
    .status-tag {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
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

    .search-loading::after {
        content: '';
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #ccc;
        border-top-color: #333;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }

    @keyframes spin {
        to { transform: translateY(-50%) rotate(360deg); }
    }
</style>

<div class="fade-in flex flex-col gap-4 pb-4">
    <!-- Main Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                <i class="fas fa-code-branch"></i>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Quản lý chi nhánh</h2>
                <p class="text-muted-foreground">Danh sách và thông tin các chi nhánh</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.branches.create') }}" class="btn btn-primary flex items-center">
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
            <h3 class="text-lg font-medium">Danh sách chi nhánh</h3>
        </div>

        <!-- Toolbar -->
        <div class="p-4 border-b flex flex-col sm:flex-row justify-between gap-4">
            <div class="relative w-full sm:w-auto sm:min-w-[300px]">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                <input type="text"
                    placeholder="Tìm kiếm theo tên, địa chỉ..."
                    class="border rounded-md px-3 py-2 bg-background text-sm w-full pl-9"
                    id="searchInput"
                    value="{{ request('search') }}"
                    autocomplete="off">
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
                            <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-green-50 hover:text-green-700 transition-colors duration-200" onclick="updateSelectedStatus(1)">
                                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                <span class="text-green-700">Kích hoạt đã chọn</span>
                            </a>
                            <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-red-50 hover:text-red-700 transition-colors duration-200" onclick="updateSelectedStatus(0)">
                                <i class="fas fa-times-circle text-red-600 mr-2"></i>
                                <span class="text-red-700">Vô hiệu hóa đã chọn</span>
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
                        <th class="py-3 px-4 text-left font-medium">ID</th>
                        <th class="py-3 px-4 text-left font-medium">Tên</th>
                        <th class="py-3 px-4 text-left font-medium">Địa chỉ</th>
                        <th class="py-3 px-4 text-left font-medium">Liên hệ</th>
                        <th class="py-3 px-4 text-left font-medium">Giờ làm việc</th>
                        <th class="py-3 px-4 text-left font-medium">Trạng thái</th>
                        <th class="py-3 px-4 text-left font-medium">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branches as $branch)
                    <tr class="border-b">
                        <td class="py-3 px-4">
                            <input type="checkbox" class="branch-checkbox" value="{{ $branch->id }}">
                        </td>
                        <td class="py-3 px-4">{{ $branch->id }}</td>
                        <td class="py-3 px-4">{{ $branch->name }}</td>
                        <td class="py-3 px-4">{{ Str::limit($branch->address, 40) }}</td>
                        <td class="py-3 px-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-phone text-sm text-muted-foreground"></i>
                                    <span>{{ $branch->phone }}</span>
                                </div>
                                @if($branch->email)
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-envelope text-sm text-muted-foreground"></i>
                                    <span>{{ $branch->email }}</span>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 px-4">{{ date('H:i', strtotime($branch->opening_hour)) }} - {{ date('H:i', strtotime($branch->closing_hour)) }}</td>
                        <td class="py-3 px-4">
                            <button type="button"
                                class="px-3 py-1.5 rounded-full text-xs {{ $branch->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} hover:opacity-80 w-24 transition-opacity duration-200"
                                data-branch-id="{{ $branch->id }}"
                                data-branch-name="{{ $branch->name }}"
                                data-branch-active="{{ $branch->active ? 'true' : 'false' }}">
                                @if($branch->active)
                                <i class="fas fa-check mr-1"></i> Hoạt động
                                @else
                                <i class="fas fa-times mr-1"></i> Vô hiệu hóa
                                @endif
                            </button>
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('admin.branches.show', $branch->id) }}" class="btn btn-ghost btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-6 text-center text-muted-foreground">
                            <i class="fas fa-store-slash mr-2"></i>
                            Không có chi nhánh nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    /* Thêm style cho loading spinner khi tải thêm dữ liệu */
    .loading-spinner {
        display: none;
        text-align: center;
        padding: 1rem;
        color: #666;
    }
    .loading-spinner.active {
        display: block;
    }
    .loading-spinner::after {
        content: '';
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid #ccc;
        border-top-color: #333;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<script>
    // Khai báo biến trạng thái
    let searchTimeout = null;
    let currentPage = {{ request('page', 1) }};
    let currentSearch = '{{ request('search') }}';
    let isLoading = false;
    let hasMore = true;

    // Ensure CSRF token is available
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        console.error('CSRF token not found');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý chọn tất cả
        const selectAllCheckbox = document.getElementById('selectAll');
        const selectAllButton = document.getElementById('selectAllButton');

        // Hàm gắn sự kiện cho checkbox và nút "Chọn tất cả"
        function attachSelectAllEvents() {
            // Sự kiện cho checkbox "Chọn tất cả"
            selectAllCheckbox.addEventListener('change', function() {
                const branchCheckboxes = document.querySelectorAll('.branch-checkbox');
                branchCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActionsVisibility();
                updateSelectAllButtonText();
            });

            // Sự kiện cho nút "Chọn tất cả"
            selectAllButton.addEventListener('click', function() {
                selectAllCheckbox.checked = !selectAllCheckbox.checked;
                selectAllCheckbox.dispatchEvent(new Event('change'));
            });
        }

        // Hàm cập nhật văn bản của nút "Chọn tất cả"
        function updateSelectAllButtonText() {
            const branchCheckboxes = document.querySelectorAll('.branch-checkbox');
            const allChecked = Array.from(branchCheckboxes).every(checkbox => checkbox.checked);
            selectAllButton.querySelector('span').textContent = allChecked ? 'Bỏ chọn tất cả' : 'Chọn tất cả';
        }

        // Gắn sự kiện ban đầu
        attachSelectAllEvents();

        // Theo dõi sự kiện thay đổi của các checkbox chi nhánh
        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('branch-checkbox')) {
                updateBulkActionsVisibility();
                // Cập nhật trạng thái của "Chọn tất cả" nếu tất cả checkbox được chọn
                const branchCheckboxes = document.querySelectorAll('.branch-checkbox');
                const allChecked = Array.from(branchCheckboxes).every(checkbox => checkbox.checked);
                const someChecked = Array.from(branchCheckboxes).some(checkbox => checkbox.checked);
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
                updateSelectAllButtonText();
            }
        });

        // Xử lý tìm kiếm
        const searchInput = document.getElementById('searchInput');

        // Hàm debounce
        function debounce(func, delay) {
            return function(...args) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => func.apply(this, args), delay);
            };
        }

        // Hàm tìm kiếm
        const handleSearch = debounce(async (searchTerm) => {
            searchInput.classList.add('search-loading');
            currentPage = 1; // Reset về trang 1 khi tìm kiếm
            hasMore = true; // Reset hasMore
            await loadBranches(1, searchTerm.trim(), false); // Không nối dữ liệu khi tìm kiếm
            searchInput.classList.remove('search-loading');
        }, 500);

        // Sự kiện input
        searchInput.addEventListener('input', function(e) {
            handleSearch(e.target.value);
        });

        // Sự kiện nhấn Enter
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                clearTimeout(searchTimeout);
                handleSearch(e.target.value);
            }
        });

        // Gắn sự kiện cho các nút trạng thái
        attachStatusButtonEvents();

        // Toggle dropdown actions
        window.toggleDropdown = function(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            dropdown.classList.toggle('hidden');
        };

        // Thiết lập IntersectionObserver để tải dữ liệu khi cuộn đến cuối
        const tableContainer = document.querySelector('.overflow-x-auto');
        const observerTarget = document.createElement('div');
        observerTarget.className = 'loading-spinner';
        tableContainer.appendChild(observerTarget);

        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && hasMore && !isLoading) {
                loadBranches(currentPage + 1, currentSearch, true);
            }
        }, {
            root: tableContainer,
            threshold: 0.1
        });

        observer.observe(observerTarget);
    });

    // Hàm gắn sự kiện cho các nút trạng thái
    function attachStatusButtonEvents() {
        document.querySelectorAll('button[data-branch-id]').forEach(button => {
            // Xóa sự kiện cũ để tránh trùng lặp
            button.removeEventListener('click', handleStatusButtonClick);
            button.addEventListener('click', handleStatusButtonClick);
        });
    }

    // Hàm xử lý sự kiện click cho nút trạng thái
    function handleStatusButtonClick() {
        const branchId = this.getAttribute('data-branch-id');
        const branchName = this.getAttribute('data-branch-name');
        const currentStatus = this.getAttribute('data-branch-active') === 'true';
        window.toggleBranchStatus(this, branchId, branchName, currentStatus);
    }

    // AJAX load dữ liệu
    async function loadBranches(page = 1, search = currentSearch, append = false) {
        if (isLoading || !hasMore) return;

        isLoading = true;
        document.querySelector('.loading-spinner').classList.add('active');

        try {
            const response = await fetch(`{{ route('admin.branches.index') }}?page=${page}&search=${encodeURIComponent(search)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                updateTable(data.branches.data, append);
                currentPage = page;
                hasMore = page < data.branches.last_page;
                updateURL(page, search);

                // Gắn lại sự kiện cho các nút trạng thái sau khi cập nhật bảng
                attachStatusButtonEvents();

                // Gắn lại sự kiện cho các checkbox chi nhánh
                document.querySelectorAll('.branch-checkbox').forEach(checkbox => {
                    checkbox.removeEventListener('change', updateBulkActionsVisibility);
                    checkbox.addEventListener('change', updateBulkActionsVisibility);
                });

                // Cập nhật trạng thái của "Chọn tất cả"
                const selectAllCheckbox = document.getElementById('selectAll');
                const selectAllButton = document.getElementById('selectAllButton');
                const branchCheckboxes = document.querySelectorAll('.branch-checkbox');
                const allChecked = Array.from(branchCheckboxes).every(checkbox => checkbox.checked);
                const someChecked = Array.from(branchCheckboxes).some(checkbox => checkbox.checked);
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
                updateSelectAllButtonText();
            } else {
                showToast('error', data.message || 'Không tìm thấy kết quả phù hợp');
            }
        } catch (error) {
            console.error('Error:', error);
          
        } finally {
            isLoading = false;
            document.querySelector('.loading-spinner').classList.remove('active');
        }
    }

    // Cập nhật bảng
    function updateTable(branches, append = false) {
        const tbody = document.querySelector('tbody');
        let html = branches.length > 0 ?
            branches.map(branch => `
                <tr class="border-b">
                    <td class="py-3 px-4">
                        <input type="checkbox" class="branch-checkbox" value="${branch.id}">
                    </td>
                    <td class="py-3 px-4">${branch.id}</td>
                    <td class="py-3 px-4">${branch.name}</td>
                    <td class="py-3 px-4">${branch.address.substring(0, 40)}${branch.address.length > 40 ? '...' : ''}</td>
                    <td class="py-3 px-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-1">
                                <i class="fas fa-phone text-sm text-muted-foreground"></i>
                                <span>${branch.phone}</span>
                            </div>
                            ${branch.email ? `
                            <div class="flex items-center gap-1">
                                <i class="fas fa-envelope text-sm text-muted-foreground"></i>
                                <span>${branch.email}</span>
                            </div>` : ''}
                        </div>
                    </td>
                    <td class="py-3 px-4">${formatTime(branch.opening_hour)} - ${formatTime(branch.closing_hour)}</td>
                    <td class="py-3 px-4">
                        <button type="button"
                            class="px-3 py-1.5 rounded-full text-xs ${branch.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'} hover:opacity-80 w-24 transition-opacity duration-200"
                            data-branch-id="${branch.id}"
                            data-branch-name="${branch.name}"
                            data-branch-active="${branch.active}">
                            ${branch.active ? 
                                '<i class="fas fa-check mr-1"></i> Hoạt động' : 
                                '<i class="fas fa-times mr-1"></i> Vô hiệu hóa'}
                        </button>
                    </td>
                    <td class="py-3 px-4">
                        <a href="/admin/branches/${branch.id}" class="btn btn-ghost btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
            `).join('') :
            `<tr>
                <td colspan="8" class="py-6 text-center text-muted-foreground">
                    <i class="fas fa-store-slash mr-2"></i>
                    Không có chi nhánh nào
                </td>
            </tr>`;

        if (append) {
            tbody.innerHTML += html;
        } else {
            tbody.innerHTML = html;
        }
    }

    // Hàm định dạng thời gian
    function formatTime(timeString) {
        if (!timeString) return 'N/A';
        const date = new Date(`2000-01-01T${timeString}`);
        return date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    }

    // Toggle trạng thái chi nhánh
    window.toggleBranchStatus = async function(button, branchId, branchName, currentStatus) {
        const messages = {
            confirmTitle: 'Xác nhận thay đổi trạng thái',
            confirmSubtitle: 'Bạn có chắc chắn muốn thay đổi trạng thái của chi nhánh này?',
            confirmMessage: 'Hành động này sẽ thay đổi trạng thái hoạt động của chi nhánh.',
            successMessage: 'Đã thay đổi trạng thái chi nhánh thành công',
            errorMessage: 'Có lỗi xảy ra khi thay đổi trạng thái chi nhánh'
        };

        if (!csrfToken) {
            dtmodalShowToast('error', {
                title: 'Lỗi',
                message: 'CSRF token không tồn tại. Vui lòng tải lại trang.'
            });
            return;
        }

        dtmodalCreateModal({
            type: 'warning',
            title: messages.confirmTitle,
            subtitle: messages.confirmSubtitle,
            message: `Bạn đang thay đổi trạng thái của: <strong>"${branchName}"</strong><br>${messages.confirmMessage}`,
            confirmText: 'Xác nhận thay đổi',
            cancelText: 'Hủy bỏ',
            onConfirm: async () => {
                try {
                    // Vô hiệu hóa nút trong khi xử lý
                    button.disabled = true;
                    button.classList.add('opacity-50');

                    const response = await fetch(`/admin/branches/${branchId}/toggle-status`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ _method: 'PATCH' })
                    });

                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({}));
                        let errorMessage = errorData.message || messages.errorMessage;

                        if (response.status === 404) {
                            errorMessage = 'Không tìm thấy chi nhánh';
                        } else if (response.status === 403) {
                            errorMessage = 'Bạn không có quyền thực hiện thao tác này';
                        } else if (response.status === 422) {
                            errorMessage = errorData.message || 'Dữ liệu không hợp lệ';
                        } else if (response.status === 500) {
                            if (errorData.message.includes('quản lý HOẠT ĐỘNG')) {
                                errorMessage = 'Không thể thay đổi trạng thái chi nhánh vì chi nhánh này có quản lý đang hoạt động';
                            }
                        }

                        throw new Error(errorMessage);
                    }

                    const data = await response.json();

                    if (data.success) {
                        // Cập nhật nút trạng thái
                        button.classList.remove('bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700');
                        button.classList.add(data.data.active ? 'bg-green-100' : 'bg-red-100', data.data.active ? 'text-green-700' : 'text-red-700');
                        button.innerHTML = `<i class="fas ${data.data.active ? 'fa-check' : 'fa-times'} mr-1"></i> ${data.data.status_text}`;
                        button.setAttribute('data-branch-active', data.data.active);

                        // Gắn lại sự kiện click cho nút
                        button.removeEventListener('click', handleStatusButtonClick);
                        button.addEventListener('click', handleStatusButtonClick);

                        dtmodalShowToast('success', {
                            title: 'Thành công',
                            message: data.message || messages.successMessage
                        });
                    } else {
                        throw new Error(data.message || messages.errorMessage);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    dtmodalShowToast('error', {
                        title: 'Lỗi',
                        message: error.message || messages.errorMessage
                    });
                } finally {
                    // Bật lại nút
                    button.disabled = false;
                    button.classList.remove('opacity-50');
                }
            }
        });
    };

    // Hàm xử lý hành động hàng loạt
    function updateSelectedStatus(status) {
        const selectedIds = [];
        document.querySelectorAll('.branch-checkbox:checked').forEach(checkbox => {
            selectedIds.push(checkbox.value);
        });

        if (selectedIds.length === 0) {
            dtmodalShowToast('error', {
                title: 'Lỗi',
                message: 'Vui lòng chọn ít nhất một chi nhánh'
            });
            return;
        }

        if (!csrfToken) {
            dtmodalShowToast('error', {
                title: 'Lỗi',
                message: 'CSRF token không tồn tại. Vui lòng tải lại trang.'
            });
            return;
        }

        const statusText = status ? 'kích hoạt' : 'vô hiệu hóa';
        const messages = {
            confirmTitle: 'Xác nhận hành động hàng loạt',
            confirmMessage: `Bạn có chắc chắn muốn ${statusText} ${selectedIds.length} chi nhánh đã chọn?`,
            successMessage: `Đã ${statusText} ${selectedIds.length} chi nhánh thành công`,
            errorMessage: 'Có lỗi xảy ra khi cập nhật trạng thái'
        };

        dtmodalCreateModal({
            type: 'warning',
            title: messages.confirmTitle,
            message: messages.confirmMessage,
            confirmText: 'Xác nhận',
            cancelText: 'Hủy bỏ',
            onConfirm: async () => {
                try {
                    const response = await fetch('{{ route("admin.branches.bulk-update") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            ids: selectedIds,
                            status: status
                        })
                    });

                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({}));
                        let errorMessage = messages.errorMessage;

                        if (response.status === 404) {
                            errorMessage = 'Không tìm thấy chi nhánh';
                        } else if (response.status === 403) {
                            errorMessage = 'Bạn không có quyền thực hiện thao tác này';
                        } else if (response.status === 422) {
                            errorMessage = errorData.message || 'Dữ liệu không hợp lệ';
                        } else {
                            errorMessage = errorData.message || 'Lỗi hệ thống';
                        }

                        throw new Error(errorMessage);
                    }

                    const data = await response.json();

                    if (data.success) {
                        dtmodalShowToast('success', {
                            title: 'Thành công',
                            message: data.message || messages.successMessage
                        });

                        // Tải lại toàn bộ bảng để đảm bảo dữ liệu đồng bộ
                        currentPage = 1;
                        hasMore = true;
                        await loadBranches(1, currentSearch, false);
                        document.getElementById('selectAll').checked = false;
                        updateBulkActionsVisibility();
                        document.getElementById('selectAllButton').querySelector('span').textContent = 'Chọn tất cả';
                    } else {
                        throw new Error(data.message || messages.errorMessage);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    dtmodalShowToast('error', {
                        title: 'Lỗi',
                        message: error.message || messages.errorMessage
                    });
                }
            }
        });
    }

    // Hàm cập nhật hiển thị các nút hành động hàng loạt
    function updateBulkActionsVisibility() {
        const checkedCount = document.querySelectorAll('.branch-checkbox:checked').length;
        const actionsMenu = document.getElementById('actionsMenu');
        
        if (checkedCount > 0) {
            actionsMenu.classList.remove('hidden');
        } else {
            actionsMenu.classList.add('hidden');
        }
    }

    // Helper functions
    function showToast(type, message) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type} fixed bottom-4 right-4 p-4 rounded-md shadow-md bg-${type === 'success' ? 'green-500' : 'red-500'} text-white`;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => toast.remove(), 3000);
    }

    function updateURL(page, search) {
        const url = new URL(window.location);
        url.searchParams.set('page', page);
        search ? url.searchParams.set('search', search) : url.searchParams.delete('search');
        window.history.pushState({}, '', url);
    }
</script>
@endsection