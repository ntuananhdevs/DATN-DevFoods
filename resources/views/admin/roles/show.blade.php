@extends('layouts.admin.contentLayoutMaster')
@section('title', 'Chi tiết Role')
@section('content')
    <div style="padding-bottom: 100px;" class="min-h-screen bg-gradient-to-br flex items-center justify-center ">
        <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl p-12 animate-fadeIn">
            <div class="flex items-center gap-4 mb-8">
                <div
                    class="flex aspect-square w-14 h-14 items-center justify-center rounded-xl bg-primary text-primary-foreground">
                    <i class="fas fa-user-shield text-white text-3xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Chi tiết Role</h2>
                    <p class="text-muted-foreground text-lg">Thông tin chi tiết về quyền truy cập</p>
                </div>
            </div>
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4 text-lg">
                    <span class="font-semibold text-gray-700">Tên Role:</span>
                    <span class="text-gray-900">{{ $role->name }}</span>
                </div>
                <div class="flex items-start gap-3 mb-4 text-lg">
                    <span class="font-semibold text-gray-700">Quyền:</span>
                    <div class="flex flex-col gap-2">
                        @php
                            $permissionsMap = [
                                'create' => '<i class="fas fa-plus-circle text-success mr-1"></i> Tạo',
                                'edit' => '<i class="fas fa-edit text-warning mr-1"></i> Chỉnh sửa',
                                'view' => '<i class="fas fa-eye text-info mr-1"></i> Xem',
                                'delete' => '<i class="fas fa-trash-alt text-danger mr-1"></i> Xóa',
                                '*' =>
                                    '<i class="fas fa-star text-primary mr-1"></i> Quyền truy cập đầy đủ vào tất cả các tính năng của hệ thống',
                            ];
                        @endphp
                        @foreach ($role->permissions as $permission)
                            <div class="mb-1">{!! $permissionsMap[$permission] ?? $permission !!}</div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-4 mt-8">
                <a href="{{ route('admin.roles.edit', $role->id) }}"
                    class="btn btn-warning flex items-center gap-2 text-lg px-6 py-2">
                    <i class="fas fa-edit"></i> Chỉnh sửa
                </a>
                <a href="{{ route('admin.roles.index') }}"
                    class="btn btn-outline-secondary flex items-center gap-2 text-lg px-6 py-2">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
        <style>
            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            .animate-fadeIn {
                animation: fadeIn 0.6s ease-out;
            }
        </style>
    </div>
@endsection
