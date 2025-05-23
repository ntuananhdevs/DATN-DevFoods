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
                    value="{{ request('search') }}">
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
                                <i class="fas fa-check-circle text-success mr-2"></i>
                                Kích hoạt đã chọn
                            </a>
                            <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground" onclick="updateSelectedStatus(0)">
                                <i class="fas fa-times-circle text-danger mr-2"></i>
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
                            <span class="status-tag {{ $branch->active ? 'success' : 'failed' }}">
                                {{ $branch->active ? 'Hoạt động' : 'Vô hiệu hóa' }}
                            </span>
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

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý chọn tất cả
    const selectAllCheckbox = document.getElementById('selectAll');
    const branchCheckboxes = document.querySelectorAll('.branch-checkbox');
    
    selectAllCheckbox.addEventListener('change', function() {
        branchCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Xử lý tìm kiếm với debounce
    const searchInput = document.getElementById('searchInput');
    let searchTimeout = null;
    
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadBranches(1, e.target.value);
        }, 500);
    });

    // Toggle dropdown actions
    window.toggleDropdown = function(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        dropdown.classList.toggle('hidden');
    };
});

// AJAX load dữ liệu
async function loadBranches(page = 1, search = '') {
    try {
        const response = await fetch(`{{ route('admin.branches.index') }}?page=${page}&search=${encodeURIComponent(search)}`);
        const data = await response.json();
        
        if (data.success) {
            updateTable(data.branches.data);
            updatePagination(data.branches);
            updateURL(page, search);
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('error', 'Lỗi tải dữ liệu');
    }
}

// Cập nhật bảng
function updateTable(branches) {
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = branches.length > 0 
        ? branches.map(branch => `
            <tr>
                <td class="py-3 px-4">
                    <input type="checkbox" class="branch-checkbox" value="${branch.id}">
                </td>
                <td class="py-3 px-4">${branch.id}</td>
                <td class="py-3 px-4">${branch.name}</td>
                <td class="py-3 px-4">${branch.address.substring(0, 40)}${branch.address.length > 40 ? '...' : ''}</td>
                <td class="py-3 px-4">
                    <div class="space-y-1">
                        <div>📞 ${branch.phone}</div>
                        ${branch.email ? `<div>📧 ${branch.email}</div>` : ''}
                    </div>
                </td>
                <td class="py-3 px-4">${branch.opening_hour} - ${branch.closing_hour}</td>
                <td class="py-3 px-4">
                    <span class="status-tag ${branch.active ? 'success' : 'failed'}">
                        ${branch.active ? 'Hoạt động' : 'Vô hiệu hóa'}
                    </span>
                </td>
                <td class="py-3 px-4">
                    <a href="/admin/branches/${branch.id}" class="btn btn-ghost btn-sm">
                        👁️ Xem
                    </a>
                </td>
            </tr>
        `).join('')
        : `<tr>
            <td colspan="8" class="py-6 text-center text-muted-foreground">
                🏪 Không có chi nhánh nào
            </td>
           </tr>`;
}

// Helper functions
function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type} fixed bottom-4 right-4`;
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