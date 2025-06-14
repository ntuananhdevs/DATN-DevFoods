// State variables
let searchTimeout = null;
let currentPage = 1;
let currentSearch = '';
let hasMore = true;
let isLoading = false;
let currentView = 'table';
let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeViewToggle();
    initializeSelectAll();
    initializeSearch();
    initializeBulkActions();
    initializeInfiniteScroll();
    attachStatusButtonEvents();
});

// Initialize view toggle functionality
function initializeViewToggle() {
    const tableViewBtn = document.getElementById('tableViewBtn');
    const gridViewBtn = document.getElementById('gridViewBtn');
    const tableView = document.getElementById('tableView');
    const gridView = document.getElementById('gridView');

    tableViewBtn.addEventListener('click', function() {
        currentView = 'table';
        tableViewBtn.classList.add('active');
        gridViewBtn.classList.remove('active');
        tableView.style.display = 'block';
        gridView.style.display = 'none';
        loadBranches(currentPage, currentSearch, false);
    });

    gridViewBtn.addEventListener('click', function() {
        currentView = 'grid';
        gridViewBtn.classList.add('active');
        tableViewBtn.classList.remove('active');
        tableView.style.display = 'none';
        gridView.style.display = 'block';
        loadBranches(currentPage, currentSearch, false);
    });
}

// Initialize select all functionality
function initializeSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectAllButton = document.getElementById('selectAllButton');

    selectAllCheckbox.addEventListener('change', function() {
        const branchCheckboxes = document.querySelectorAll('.branch-checkbox');
        branchCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActionsVisibility();
        updateSelectAllButtonText();
    });

    selectAllButton.addEventListener('click', function() {
        const branchCheckboxes = document.querySelectorAll('.branch-checkbox');
        const allChecked = Array.from(branchCheckboxes).every(checkbox => checkbox.checked);
        
        branchCheckboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
        
        selectAllCheckbox.checked = !allChecked;
        selectAllCheckbox.indeterminate = false;
        updateBulkActionsVisibility();
        updateSelectAllButtonText();
    });

    // Handle individual checkbox changes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('branch-checkbox')) {
            const branchCheckboxes = document.querySelectorAll('.branch-checkbox');
            const allChecked = Array.from(branchCheckboxes).every(checkbox => checkbox.checked);
            const someChecked = Array.from(branchCheckboxes).some(checkbox => checkbox.checked);
            
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
            updateBulkActionsVisibility();
            updateSelectAllButtonText();
        }
    });
}

// Initialize search functionality
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');

    // Debounce function
    function debounce(func, delay) {
        return function(...args) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Search handler
    const handleSearch = debounce(async (searchTerm) => {
        searchInput.classList.add('search-loading');
        currentPage = 1;
        hasMore = true;
        await loadBranches(1, searchTerm.trim(), false);
        searchInput.classList.remove('search-loading');
    }, 500);

    searchInput.addEventListener('input', function(e) {
        handleSearch(e.target.value);
    });

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            clearTimeout(searchTimeout);
            handleSearch(e.target.value);
        }
    });
}

// Initialize bulk actions
function initializeBulkActions() {
    // Toggle dropdown actions
    window.toggleDropdown = function(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        dropdown.classList.toggle('hidden');
    };

    // Update selected status
    window.updateSelectedStatus = function(status) {
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
            successMessage: `Đã ${statusText} thành công ${selectedIds.length} chi nhánh`,
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
                    const response = await fetch('/admin/branches/bulk-update', {
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
    };
}

// Initialize infinite scroll
function initializeInfiniteScroll() {
    const viewContainer = document.querySelector('.card');
    const observerTarget = document.querySelector('.loading-spinner');
    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting && hasMore && !isLoading) {
            loadBranches(currentPage + 1, currentSearch, true);
        }
    }, {
        root: viewContainer,
        threshold: 0.1
    });

    observer.observe(observerTarget);
}

// Attach status button events
function attachStatusButtonEvents() {
    document.querySelectorAll('button[data-branch-id]').forEach(button => {
        button.removeEventListener('click', handleStatusButtonClick);
        button.addEventListener('click', handleStatusButtonClick);
    });
}

// Handle status button click
function handleStatusButtonClick() {
    const branchId = this.getAttribute('data-branch-id');
    const branchName = this.getAttribute('data-branch-name');
    const currentStatus = this.getAttribute('data-branch-active') === 'true';
    window.toggleBranchStatus(this, branchId, branchName, currentStatus);
}

// AJAX load branches
async function loadBranches(page = 1, search = currentSearch, append = false) {
    if (isLoading || !hasMore) return;

    isLoading = true;
    document.querySelector('.loading-spinner').classList.add('active');

    try {
        const response = await fetch(
            `/admin/branches?page=${page}&search=${encodeURIComponent(search)}`, {
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
            updateContent(data.branches.data, append);
            currentPage = page;
            hasMore = page < data.branches.last_page;
            updateURL(page, search);

            attachStatusButtonEvents();
            document.querySelectorAll('.branch-checkbox').forEach(checkbox => {
                checkbox.removeEventListener('change', updateBulkActionsVisibility);
                checkbox.addEventListener('change', updateBulkActionsVisibility);
            });

            const selectAllCheckbox = document.getElementById('selectAll');
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
        showToast('error', 'Có lỗi xảy ra khi tải dữ liệu');
    } finally {
        isLoading = false;
        document.querySelector('.loading-spinner').classList.remove('active');
    }
}

// Update both table and grid content
function updateContent(branches, append = false) {
    updateTable(branches, append);
    updateGrid(branches, append);
}

// Update table
function updateTable(branches, append = false) {
    const tbody = document.getElementById('tableBody');
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
                    <a href="/admin/branches/show/${branch.id}" class="btn btn-ghost btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
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

// Update grid
function updateGrid(branches, append = false) {
    const gridContainer = document.getElementById('gridContainer');
    let html = branches.length > 0 ?
        branches.map(branch => `
            <div class="branch-card">
                <input type="checkbox" class="branch-checkbox branch-card-checkbox" value="${branch.id}">
                <div class="branch-card-header">
                    <div>
                        <div class="branch-card-title">${branch.name}</div>
                        <div class="branch-card-id">ID: ${branch.id}</div>
                    </div>
                </div>
                <div class="branch-card-content">
                    <div class="branch-info-item">
                        <i class="fas fa-map-marker-alt branch-info-icon"></i>
                        <span>${branch.address}</span>
                    </div>
                    <div class="branch-info-item">
                        <i class="fas fa-phone branch-info-icon"></i>
                        <span>${branch.phone}</span>
                    </div>
                    ${branch.email ? `
                        <div class="branch-info-item">
                            <i class="fas fa-envelope branch-info-icon"></i>
                            <span>${branch.email}</span>
                        </div>` : ''}
                    <div class="branch-info-item">
                        <i class="fas fa-clock branch-info-icon"></i>
                        <span>${formatTime(branch.opening_hour)} - ${formatTime(branch.closing_hour)}</span>
                    </div>
                </div>
                <div class="branch-card-actions">
                    <button type="button"
                        class="px-3 py-1.5 rounded-full text-xs ${branch.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'} hover:opacity-80 transition-opacity duration-200"
                        data-branch-id="${branch.id}"
                        data-branch-name="${branch.name}"
                        data-branch-active="${branch.active}">
                        ${branch.active ?
                            '<i class="fas fa-check mr-1"></i> Hoạt động' :
                            '<i class="fas fa-times mr-1"></i> Vô hiệu hóa'}
                    </button>
                    <a href="/admin/branches/show/${branch.id}" class="btn btn-ghost btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </a>
                </div>
            </div>
        `).join('') :
        `<div class="col-span-full py-12 text-center text-muted-foreground">
            <i class="fas fa-store-slash mr-2 text-2xl"></i>
            <p class="mt-2">Không có chi nhánh nào</p>
        </div>`;

    if (append) {
        gridContainer.innerHTML += html;
    } else {
        gridContainer.innerHTML = html;
    }
}

// Toggle branch status
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
                button.disabled = true;
                button.classList.add('opacity-50');

                const response = await fetch(`/admin/branches/${branchId}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        _method: 'PATCH'
                    })
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
                    button.classList.remove('bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700');
                    button.classList.add(data.data.active ? 'bg-green-100' : 'bg-red-100', data.data.active ? 'text-green-700' : 'text-red-700');
                    button.innerHTML = `<i class="fas ${data.data.active ? 'fa-check' : 'fa-times'} mr-1"></i> ${data.data.status_text}`;
                    button.setAttribute('data-branch-active', data.data.active);

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
                button.disabled = false;
                button.classList.remove('opacity-50');
            }
        }
    });
};

// Utility functions
function updateBulkActionsVisibility() {
    const checkedCount = document.querySelectorAll('.branch-checkbox:checked').length;
    const actionsMenu = document.getElementById('actionsMenu');
    actionsMenu.classList.toggle('hidden', checkedCount === 0);
}

function updateSelectAllButtonText() {
    const checkedCount = document.querySelectorAll('.branch-checkbox:checked').length;
    const selectAllButton = document.getElementById('selectAllButton');
    const buttonText = selectAllButton.querySelector('span');
    buttonText.textContent = checkedCount > 0 ? 'Bỏ chọn tất cả' : 'Chọn tất cả';
}

function formatTime(timeString) {
    if (!timeString) return 'N/A';
    const date = new Date(`2000-01-01T${timeString}`);
    return date.toLocaleTimeString('vi-VN', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

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