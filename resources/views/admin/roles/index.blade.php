@extends('layouts.admin.contentLayoutMaster')
@section('title', 'Quản lý Role')
@section('content')
    <div class="min-h-screen bg-gradient-to-br">
        <div class="fade-in flex flex-col gap-4 pb-4 animate-slideInUp delay-200 duration-700 ease-in-out">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                        <i class="fas fa-user-shield text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">Quản lý Role</h2>
                        <p class="text-muted-foreground">Quản lý danh sách quyền hệ thống</p>
                    </div>
                </div>
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary flex items-center">
                    <i class="fas fa-plus mr-2"></i> Thêm mới
                </a>
            </div>

            <div
                class="bg-white rounded-xl shadow-lg overflow-hidden transform transition-all duration-500 hover:shadow-2xl animate-slideInUp delay-200 duration-700 ease-in-out">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Danh sách Role</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">
                                    ID</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-3/12">
                                    Tên Role</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-5/12">
                                    Quyền</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-3/12">
                                    Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($roles as $role)
                                <tr>
                                    <td class="px-4 py-3 w-1/12">{{ $role->id }}</td>
                                    <td class="px-4 py-3 w-3/12 font-medium text-gray-900">
                                        {{ $role->name ?? 'Không xác định' }}</td>
                                    <td class="px-4 py-3 w-5/12">
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
                                                (array) ($role->permissions ?? []),
                                            );
                                        @endphp
                                        {{ implode(', ', $translatedPermissions) ?: 'Không có quyền' }}
                                    </td>
                                    <td class="px-4 py-3 w-3/12">
                                        <div class="flex justify-start space-x-2">
                                            <a href="{{ route('admin.roles.show', $role->id) }}"
                                                class="btn btn-sm btn-secondary" title="Xem">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-info"
                                                title="Sửa">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                                    </path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z">
                                                    </path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                                                class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger" title="Xoá"
                                                    onclick="dtmodalConfirmDelete({
                                                        title: 'Xác nhận xóa role',
                                                        subtitle: 'Bạn có chắc chắn muốn xóa role này?',
                                                        message: 'Hành động này không thể hoàn tác.',
                                                        itemName: '{{ $role->name }}',
                                                        onConfirm: () => this.closest('form').submit()
                                                    })">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M3 6h18"></path>
                                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">Không có Role nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-white px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Hiển thị
                            <span
                                class="font-medium text-gray-900">{{ ($roles->currentPage() - 1) * $roles->perPage() + 1 }}</span>
                            đến
                            <span
                                class="font-medium text-gray-900">{{ min($roles->currentPage() * $roles->perPage(), $roles->total()) }}</span>
                            của
                            <span class="font-medium text-gray-900">{{ $roles->total() }}</span>
                            mục
                        </div>
                        @if ($roles->lastPage() > 1)
                            <div class="flex items-center space-x-2">
                                @if (!$roles->onFirstPage())
                                    <a href="{{ $roles->previousPageUrl() }}"
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transform transition-all duration-200 hover:scale-105">
                                        <i class="fas fa-chevron-left mr-1"></i>
                                        Trước
                                    </a>
                                @endif
                                @php
                                    $start = max(1, $roles->currentPage() - 2);
                                    $end = min($roles->lastPage(), $roles->currentPage() + 2);
                                    if ($start > 1) {
                                        echo '<a href="' .
                                            $roles->url(1) .
                                            '" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transform transition-all duration-200 hover:scale-105">1</a>';
                                        if ($start > 2) {
                                            echo '<span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">...</span>';
                                        }
                                    }
                                @endphp
                                @for ($i = $start; $i <= $end; $i++)
                                    <a href="{{ $roles->url($i) }}"
                                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transform transition-all duration-200 hover:scale-105 {{ $roles->currentPage() == $i ? 'bg-blue-500 text-white border-blue-500' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 hover:text-gray-700' }}">
                                        {{ $i }}
                                    </a>
                                @endfor
                                @php
                                    if ($end < $roles->lastPage()) {
                                        if ($end < $roles->lastPage() - 1) {
                                            echo '<span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">...</span>';
                                        }
                                        echo '<a href="' .
                                            $roles->url($roles->lastPage()) .
                                            '" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transform transition-all duration-200 hover:scale-105">' .
                                            $roles->lastPage() .
                                            '</a>';
                                    }
                                @endphp
                                @if ($roles->hasMorePages())
                                    <a href="{{ $roles->nextPageUrl() }}"
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transform transition-all duration-200 hover:scale-105">
                                        Tiếp
                                        <i class="fas fa-chevron-right ml-1"></i>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
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

        @keyframes slideInUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out;
        }

        .animate-slideInUp {
            animation: slideInUp 0.6s ease-out 0.2s both;
        }
    </style>
@endsection
