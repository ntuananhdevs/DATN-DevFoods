@extends('layouts.admin.contentLayoutMaster')



@section('content')
<div class="data-table-wrapper">
    <!-- Main Header -->
    <div class="data-table-main-header">
        <div class="data-table-brand">
            <div class="data-table-logo">
                <i class="fas fa-trash"></i>
            </div>
            <h1 class="data-table-title">Thùng rác người dùng</h1>
        </div>

        <div class="data-table-header-actions">
            <a href="{{ route('admin.users.index') }}" class="data-table-btn data-table-btn-primary">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="data-table-card">
        <!-- Table Header -->
        <div class="data-table-header">
            <h2 class="data-table-card-title">Danh sách đã xóa</h2>
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

                        <th>Thời gian xóa </th>
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
                        <td>{{ $user->user_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? 'N/A' }}</td>
                        <td>{{ $user->deleted_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="data-table-action-buttons">
                                <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="data-table-action-btn restore data-table-tooltip"
                                        data-tooltip="Khôi phục">
                                        <i class="fas fa-trash-restore"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.users.force-delete', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="data-table-action-btn delete data-table-tooltip"
                                        data-tooltip="Xóa vĩnh viễn"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn người dùng này?')">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </form>
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
                                <h3>Thùng rác trống</h3>
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
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection