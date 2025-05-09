@extends('layouts.admin.contentLayoutMaster')

@section('content')
    <div class="data-table-wrapper">

        {{-- Header chính --}}
        <div class="data-table-main-header">
            <div class="data-table-brand">
                <div class="data-table-logo"><i class="fas fa-user-shield"></i></div>
                <h1 class="data-table-title">Quản lý Role</h1>
            </div>
            <div class="data-table-header-actions">
                <a href="{{ route('admin.roles.create') }}" class="data-table-btn data-table-btn-primary">
                    <i class="fas fa-plus"></i> Thêm Role
                </a>
            </div>
        </div>

        {{-- Card bảng --}}
        <div class="data-table-card">
            <div class="data-table-header">
                <h2 class="data-table-card-title">Danh sách Role</h2>
            </div>

            {{-- Bảng --}}
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Role</th>
                            <th>Quyền</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td>{{ $role->id }}</td>
                                <td>{{ $role->name }}</td>
                                <td>
                                    @php
                                        $permissionsMap = [
                                            'create' => 'Tạo',
                                            'edit' => 'Chỉnh sửa',
                                            'view' => 'Xem',
                                            'delete' => 'Xóa',
                                            '*' => 'Tất cả quyền',
                                        ];
                                        $translatedPermissions = array_map(
                                            fn($permission) => $permissionsMap[$permission] ?? $permission,
                                            (array) $role->permissions,
                                        );
                                    @endphp
                                    {{ implode(', ', $translatedPermissions) ?: 'Không có quyền' }}
                                </td>
                                <td>
                                    <div class="data-table-action-buttons">
                                        <a href="{{ route('admin.roles.show', $role->id) }}" class="data-table-action-btn"
                                            data-tooltip="Xem">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.roles.edit', $role->id) }}"
                                            class="data-table-action-btn edit" data-tooltip="Sửa">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <button type="button" onclick="confirmDelete({{ $role->id }})"
                                            class="data-table-action-btn delete" data-tooltip="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Không có Role nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Phân trang --}}
            <div class="data-table-footer">
                <div class="data-table-pagination-info">
                    Hiển thị {{ $roles->firstItem() }} đến {{ $roles->lastItem() }} / tổng số {{ $roles->total() }}
                </div>
                <div class="data-table-pagination-controls">
                    {{ $roles->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal flash message --}}
    @if (session('success') || session('error'))
        <div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header {{ session('success') ? 'bg-success' : 'bg-danger' }} text-white">
                        <h5 class="modal-title">Thông báo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        {{ session('success') ?? session('error') }}
                    </div>
                </div>
            </div>
        </div>

        <script>
            window.addEventListener('load', function() {
                const modal = new bootstrap.Modal(document.getElementById('messageModal'));
                modal.show();
            });
        </script>
    @endif

    {{-- Modal xác nhận xóa --}}
    <!-- Modal Xác nhận Xóa - Bootstrap 4 style -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Xác nhận xóa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa Role này?</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger">Xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- Script xử lý xóa --}}
    <script>
        function confirmDelete(id) {
            const form = document.getElementById('deleteForm');
            form.action = "{{ route('admin.roles.destroy', ':id') }}".replace(':id', id);
            const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            modal.show();
        }
    </script>
@endsection
