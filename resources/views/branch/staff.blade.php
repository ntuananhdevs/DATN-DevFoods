@extends('layouts.branch.contentLayoutMaster')

@section('title', 'Quản lý Nhân viên')
@section('description', 'Quản lý nhân viên của chi nhánh')

@section('content')
    <div class="fade-in flex flex-col gap-4 pb-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="7" r="4" />
                        <path d="M5.5 21h13a2 2 0 0 0 2-2v-2a7 7 0 0 0-7-7h-1a7 7 0 0 0-7 7v2a2 2 0 0 0 2 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Quản lý Nhân viên</h2>
                    <p class="text-muted-foreground">Danh sách nhân viên của chi nhánh</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="#" class="btn btn-primary flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="mr-2">
                        <path d="M5 12h14"></path>
                        <path d="M12 5v14"></path>
                    </svg>
                    Thêm mới
                </a>
            </div>
        </div>
        <div class="card border rounded-lg overflow-hidden">
            <div class="p-6 border-b">
                <h3 class="text-lg font-medium">Danh sách nhân viên</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b">
                        <tr class="text-left">
                            <th class="p-4 font-medium text-muted-foreground">Tên nhân viên</th>
                            <th class="p-4 font-medium text-muted-foreground">Email</th>
                            <th class="p-4 font-medium text-muted-foreground">Số điện thoại</th>
                            <th class="p-4 font-medium text-muted-foreground">Chức vụ</th>
                            <th class="p-4 font-medium text-muted-foreground">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff as $user)
                            <tr class="border-b hover:bg-muted/50 transition-colors">
                                <td class="p-4 font-semibold">{{ $user->name }}</td>
                                <td class="p-4">{{ $user->email }}</td>
                                <td class="p-4">{{ $user->phone }}</td>
                                <td class="p-4">{{ $user->role_name ?? 'Nhân viên' }}</td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        <a href="#" class="btn btn-outline btn-sm" title="Sửa">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                                <path d="m15 5 4 4"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-500">Không có nhân viên nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
