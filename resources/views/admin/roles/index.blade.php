@extends('layouts/admin/contentLayoutMaster')

@section('content')
    <div class="container">
        <h1 class="mb-3 text-center">Danh sách Roles</h1>

        @if (session('success'))
            <div class="modal show" id="successModal" style="display:block;">
                <div class="modal-dialog">
                    <div class="modal-content bg-success text-white p-3">
                        {{ session('success') }}
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="modal show" id="errorModal" style="display:block;">
                <div class="modal-dialog">
                    <div class="modal-content bg-danger text-white p-3">
                        {{ session('error') }}
                    </div>
                </div>
            </div>
        @endif

        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary mb-3" style="font-size: 1rem;">Thêm Role</a>

        <table class="table container table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Tên Role</th>
                    <th>Quyền</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    @if ($role)
                        <tr>
                            <td>{{ $role->name ?? 'N/A' }}</td>
                            <td>
                                @if (!empty($role->permissions))
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
                                    {{ implode(', ', $translatedPermissions) }}
                                @else
                                    Không có quyền
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.roles.show', $role->id) }}" class="btn btn-info btn-sm"
                                    style="font-size: 1.1rem;">Chi tiết</a>
                                <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-warning btn-sm"
                                    style="font-size: 1.1rem;">Chỉnh sửa</a>
                                <button type="button" class="btn btn-danger btn-sm" style="font-size: 1.1rem;"
                                    onclick="confirmDelete({{ $role->id }})">Xóa</button>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        {{ $roles->links() }}
    </div>

    <!-- Modal Confirm Delete -->
    <div class="modal" tabindex="-1" role="dialog" id="confirmDeleteModal" style="display: none;">
        <div class="modal-dialog" role="document">
            <div class="modal-content ">
                <div class="modal-header btn-secondary ">
                    <h5 class="modal-title text-white">Xác nhận xóa</h5>
                    <button type="button" class="close" onclick="closeModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa role này?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
                    <form id="deleteForm" action="" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(roleId) {
            var modal = document.getElementById('confirmDeleteModal');
            var form = document.getElementById('deleteForm');
            form.action = "{{ route('admin.roles.destroy', ':id') }}".replace(':id', roleId); // Sửa lỗi thiếu tham số id
            modal.style.display = 'block';
        }

        function closeModal() {
            document.getElementById('confirmDeleteModal').style.display = 'none';
        }

        // Tự động ẩn modal thông báo sau 3 giây
        setTimeout(function() {
            var successModal = document.getElementById('successModal');
            var errorModal = document.getElementById('errorModal');
            if (successModal) {
                successModal.style.display = 'none';
            }
            if (errorModal) {
                errorModal.style.display = 'none';
            }
        }, 2000);
    </script>
@endsection
