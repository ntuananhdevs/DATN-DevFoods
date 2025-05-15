@extends('layouts.admin.contentLayoutMaster')

@section('content')
<div class="data-table-wrapper">
    <!-- Main Header -->
    <div class="data-table-main-header">
        <div class="data-table-brand">
            <div class="data-table-logo">
                <i class="fas fa-users"></i>
            </div>
            <h1 class="data-table-title">Quản lý người dùng</h1>
        </div>
        <div class="data-table-header-actions">
        <div class="btn-group mr-2">
                                <button type="button" class="data-table-btn data-table-btn-outline dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-download"></i> Xuất
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('admin.users.export', ['type' => 'excel']) }}">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </a>
                                    <a class="dropdown-item" href="{{ route('admin.users.export', ['type' => 'pdf']) }}">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                    <a class="dropdown-item" href="{{ route('admin.users.export', ['type' => 'csv']) }}">
                                        <i class="fas fa-file-csv"></i> CSV
                                    </a>
                                </div>
                            </div>

            <a href="{{ route('admin.users.create') }}" class="data-table-btn data-table-btn-primary">
                <i class="fas fa-plus"></i> Thêm mới
            </a>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="data-table-card">
        <!-- Table Header -->
        <div class="data-table-header">
            <h2 class="data-table-card-title">Danh sách người dùng</h2>
        </div>

        <!-- Controls -->
        <div class="data-table-controls">
            <div class="data-table-search">
                <i class="fas fa-search data-table-search-icon"></i>
                <input type="text"
                    placeholder="Tìm kiếm theo tên, mail người dùng ..."
                    id="dataTableSearch"
                    value="{{ request('search') }}"
                    onkeyup="handleSearch(event)">
            </div>
            <div class="data-table-actions">
                <div class="d-flex align-items-center">
                    <div class="data-table-actions">
                        <div class="d-flex align-items-center">
                            <button class="data-table-btn data-table-btn-outline mr-2" onclick="toggleSelectAll()">
                                <i class="fas fa-check-square"></i> Chọn tất cả
                            </button>
                            <div class="btn-group mr-2">
                                <button type="button" class="data-table-btn data-table-btn-outline dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-tasks"></i> Thao tác
                                </button>
                                <div class="dropdown-menu">
                                    <a href="#" class="dropdown-item" onclick="updateSelectedStatus(1)">
                                        <i class="fas fa-check-circle text-success"></i> Kích hoạt đã chọn
                                    </a>
                                    <a href="#" class="dropdown-item" onclick="updateSelectedStatus(0)">
                                        <i class="fas fa-times-circle text-danger"></i> Vô hiệu hóa đã chọn
                                    </a>
                                </div>
                            </div>

                          
                            <button class="data-table-btn data-table-btn-outline">
                                <i class="fas fa-columns"></i> Cột
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="data-table-container">
            <table class="data-table" id="dataTable">
                <thead>
                    <tr>
                        <th>
                            <div>
                                <input type="checkbox" id="selectAll">

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
                    <tr>
                        <td>
                            <input type="checkbox" class="row-checkbox" value="{{ $user->id }}">
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
                            <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="button"
                                    class="data-table-status {{ $user->active ? 'data-table-status-success' : 'data-table-status-failed' }}"
                                    style="border: none; cursor: pointer; width: 100px;"
                                    onclick="dtmodalHandleStatusToggle({
                                        button: this,
                                        userName: '{{ $user->full_name }}',
                                        currentStatus: {{ $user->active ? 'true' : 'false' }},
                                        confirmTitle: 'Xác nhận thay đổi trạng thái',
                                        confirmSubtitle: 'Bạn có chắc chắn muốn thay đổi trạng thái của người dùng này?',
                                        confirmMessage: 'Hành động này sẽ thay đổi trạng thái hoạt động của người dùng.',
                                        successMessage: 'Đã thay đổi trạng thái người dùng thành công',
                                        errorMessage: 'Có lỗi xảy ra khi thay đổi trạng thái người dùng'
                                    })">
                                    @if($user->active)
                                    <i class="fas fa-check"></i> Hoạt động
                                    @else
                                    <i class="fas fa-times"></i> Vô hiệu hóa
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="data-table-action-buttons">
                                <a href="{{ route('admin.users.show', $user->id) }}"
                                    class="data-table-action-btn data-table-tooltip"
                                    data-tooltip="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                            <form id="bulkStatusForm" action="{{ route('admin.users.bulk-status-update') }}" method="POST" style="display: none;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="user_ids" id="selectedUserIds">
                                <input type="hidden" name="status" id="selectedStatus">
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">
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
                <a href="{{ $users->previousPageUrl() }}&search={{ request('search') }}"
                    class="data-table-pagination-btn"
                    id="prevBtn">
                    <i class="fas fa-chevron-left"></i> Trước
                </a>
                @endif

                @php
                $start = max(1, $users->currentPage() - 2);
                $end = min($users->lastPage(), $users->currentPage() + 2);

                if ($start > 1) {
                echo '<a href="'.$users->url(1).'&search='.request('search').'"
                    class="data-table-pagination-btn">1</a>';
                if ($start > 2) {
                echo '<span class="data-table-pagination-dots">...</span>';
                }
                }
                @endphp

                @for ($i = $start; $i <= $end; $i++)
                    <a href="{{ $users->url($i) }}&search={{ request('search') }}"
                    class="data-table-pagination-btn {{ $users->currentPage() == $i ? 'active' : '' }}">
                    {{ $i }}
                    </a>
                    @endfor

                    @php
                    if ($end < $users->lastPage()) {
                        if ($end < $users->lastPage() - 1) {
                            echo '<span class="data-table-pagination-dots">...</span>';
                            }
                            echo '<a href="'.$users->url($users->lastPage()).'&search='.request('search').'"
                                class="data-table-pagination-btn">'.$users->lastPage().'</a>';
                            }
                            @endphp

                            @if($users->hasMorePages())
                            <a href="{{ $users->nextPageUrl() }}&search={{ request('search') }}"
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
</script>
@endsection