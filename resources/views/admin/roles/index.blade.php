@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Dashboard Ecommerce')

@section('content')
    <div class="container">
        <h1 class="my-4">Danh sách Roles</h1>

        @if (session('success'))
            <div class="modal show" style="display:block;">
                <div class="modal-dialog">
                    <div class="modal-content bg-success text-white p-3">
                        {{ session('success') }}
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="modal show" style="display:block;">
                <div class="modal-dialog">
                    <div class="modal-content bg-danger text-white p-3">
                        {{ session('error') }}
                    </div>
                </div>
            </div>
        @endif

        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary mb-3">Thêm Role</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tên Role</th>
                    <th>Quyền</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <td>{{ $role->name }}</td>
                        <td>{{ implode(', ', json_decode($role->permissions)) }}</td>
                        <td>
                            <a href="{{ route('admin.roles.show', $role->id) }}" class="btn btn-info btn-sm">Chi tiết</a>
                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-warning btn-sm">Chỉnh
                                sửa</a>
                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $roles->links() }}
    </div>
@endsection
