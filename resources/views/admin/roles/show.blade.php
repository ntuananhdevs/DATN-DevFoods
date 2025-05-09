@extends('layouts/admin/contentLayoutMaster')

@section('content')
    <div class="container">
        <h1 class="my-4 text-center" style="font-size: 1.5rem;">Chi tiết Role</h1>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($role)
            <div class="card" style="font-size: 1.2rem;">
                <div class="card-header">
                    <h4>{{ $role->name }}</h4>
                </div>
                <div class="card-body">
                    <p><strong>Tên Role:</strong> {{ $role->name }}</p>
                    <p><strong>Quyền:</strong>
                        @if (!empty($role->permissions) && is_array($role->permissions))
                            {{ implode(', ', $role->permissions) }}
                        @else
                            Không có quyền
                        @endif
                    </p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-warning"
                        style="font-size: 1.1rem;">Chỉnh sửa</a>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary" style="font-size: 1.1rem;">Quay
                        lại</a>
                </div>
            </div>
        @else
            <div class="alert alert-warning">Role không tồn tại.</div>
        @endif
    </div>
@endsection
