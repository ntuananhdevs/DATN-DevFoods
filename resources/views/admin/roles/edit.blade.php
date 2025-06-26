@extends('layouts.admin.contentLayoutMaster')
@section('title', 'Cập nhật Role')
@section('content')
    <div style="padding-bottom: 100px;" class="min-h-screen bg-gradient-to-br flex items-center justify-center">
        <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl p-12 animate-fadeIn">
            <div class="flex items-center gap-4 mb-8">
                <div
                    class="flex aspect-square w-14 h-14 items-center justify-center rounded-xl bg-primary text-primary-foreground">
                    <i class="fas fa-user-shield text-white text-3xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Cập nhật Role</h2>
                    <p class="text-muted-foreground text-lg">Chỉnh sửa quyền truy cập hệ thống</p>
                </div>
            </div>
            @if (session('error'))
                <div class="alert alert-danger mb-6 text-lg">{{ session('error') }}</div>
            @endif
            @if ($role)
                @include('admin.roles.form', [
                    'action' => route('admin.roles.update', $role->id),
                    'isEdit' => true,
                    'role' => $role,
                ])
            @else
                <div class="alert alert-warning text-lg">Role không tồn tại.</div>
            @endif
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
