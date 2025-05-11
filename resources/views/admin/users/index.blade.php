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
            <div class="header-actions">
                <div class="btn-group">
                    <button type="button" class="data-table-btn data-table-btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-download"></i> Xuất
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('admin.users.export', ['type' => 'excel']) }}">
                            <i class="fas fa-file-excel"></i> Xuất Excel
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.users.export', ['type' => 'pdf']) }}">
                            <i class="fas fa-file-pdf"></i>PDF</a>
                        <a class="dropdown-item" href="{{ route('admin.users.export', ['type' => 'csv']) }}">
                            <i class="fas fa-file-csv"></i> CSV</a>
                    </div>
                </div>
                <a href="{{ route('admin.users.trash') }}" class="data-table-btn data-table-btn-danger ml-2">
                    <i class="fas fa-trash"></i> Thùng rác
                </a>
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
                <form method="GET" action="{{ route('admin.users.index') }}">
                    <i class="fas fa-search data-table-search-icon"></i>
                    <input type="text"
                        placeholder="Tìm kiếm theo tên, email..."
                        name="search"
                        value="{{ request('search') }}"
                        id="dataTableSearch">
                    <button type="submit" hidden></button>
                </form>
            </div>
            <div class="data-table-actions">
                <button class="data-table-btn data-table-btn-outline">
                    <i class="fas fa-sliders"></i> Cột
                </button>

            </div>
        </div>

        <!-- Table Container -->
        <div class="data-table-container">
            <table class="data-table" id="dataTable">
                <thead>
                    <tr>
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
                            @if($user->active)
                            <span class="data-table-status data-table-status-success">
                                <i class="fas fa-check"></i> Hoạt động
                            </span>
                            @else
                            <span class="data-table-status data-table-status-failed">
                                <i class="fas fa-times"></i> Vô hiệu hóa
                            </span>
                            @endif
                        </td>
                        <td>
                            <div class="data-table-action-buttons">
                                <a href="{{ route('admin.users.show', $user->id) }}"
                                    class="data-table-action-btn data-table-tooltip"
                                    data-tooltip="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                    class="data-table-action-btn edit data-table-tooltip"
                                    data-tooltip="Chỉnh sửa">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}"
                                    method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="data-table-action-btn delete data-table-tooltip"
                                        data-tooltip="Xóa"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
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
@endsection

@section('vendor-script')
{{-- vendor files --}}
<script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
@endsection

@section('page-script')
{{-- Page js files --}}
<script src="{{ asset(mix('js/scripts/pages/dashboard-ecommerce.js')) }}"></script>
<script src="{{ asset(mix('js/scripts/extensions/toastr.js')) }}"></script>
<script>
    // Hiển thị thông báo tự động ẩn sau 3 giây
    $(document).ready(function() {
        setTimeout(function() {
            $('.alert').alert('close');
        }, 3000);
    });
</script>
@endsection