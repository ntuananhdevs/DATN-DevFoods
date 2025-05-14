@extends('layouts.admin.contentLayoutMaster')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <!-- Header -->
            <div class="card-header d-flex justify-content-between align-items-center mb-2">
                <h4 class="mb-0">
                    <i class="fas fa-shield-alt text-primary me-2"></i> Chi tiết Role
                </h4>
                <span class="badge bg-secondary text-uppercase ">
                    {{ $role->name }}
                </span>
            </div>

            <!-- Body -->
            <div class="card-footer bg-white">
                <!-- Tên Role -->
                <div class="mb-3 d-flex">
                    <strong class="w-25"><i class="fas fa-id-badge me-2"></i> Tên Role:</strong>
                    <span class="fw-bold">{{ $role->name }}</span>
                </div>

                <!-- Quyền -->
                <div class="mb-3 d-flex">
                    <strong class="w-25"><i class="fas fa-key me-2"></i> Quyền:</strong>
                    <div>
                        @php
                            $permissionsMap = [
                                'create' => '<i class="fas fa-plus-circle text-success me-2"></i> Tạo',
                                'edit' => '<i class="fas fa-edit text-warning me-2"></i> Chỉnh sửa',
                                'view' => '<i class="fas fa-eye text-info me-2"></i> Xem',
                                'delete' => '<i class="fas fa-trash-alt text-danger me-2"></i> Xóa',
                                '*' =>
                                    '<i class="fas fa-star text-primary me-2"></i> Quyền truy cập đầy đủ vào tất cả các tính năng của hệ thống',
                            ];
                        @endphp

                        @foreach ($role->permissions as $permission)
                            <div class="mb-1">
                                {!! $permissionsMap[$permission] ?? $permission !!}
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-warning" style="margin-right:10px">
                        <i class="fas fa-edit me-1"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary text-dark">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>

                </div>

            </div>


        </div>
    </div>
@endsection
