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
                    <i class="fas fa-plus"></i> Thêm mới
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
                            <th data-sort="id" class="active-sort">
                                ID <i class="fas fa-arrow-up data-table-sort-icon"></i>
                            </th>
                            <th>Tên Role</th>
                            <th>Quyền</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td>
                                    <div class="data-table-id">
                                        {{ $role->id }}
                                    </div>
                                </td>
                                <td>{{ $role->name ?? 'Không xác định' }}</td>
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
                                            (array) ($role->permissions ?? []),
                                        );
                                    @endphp
                                    {{ implode(', ', $translatedPermissions) ?: 'Không có quyền' }}
                                </td>
                                <td>
                                    <div class="data-table-action-buttons">
                                        <a href="{{ route('admin.roles.show', $role->id) }}" class="data-table-action-btn"
                                            title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.roles.edit', $role->id) }}"
                                            class="data-table-action-btn edit" title="Sửa">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="data-table-action-btn delete " title="Xóa"
                                                onclick="dtmodalConfirmDelete({
                                             itemName: '{{ $role->name }}',
                                             onConfirm: () => this.closest('form').submit()
                                            })">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>

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
                    Hiển thị
                    <span id="startRecord">{{ ($roles->currentPage() - 1) * $roles->perPage() + 1 }}</span>
                    đến
                    <span id="endRecord">{{ min($roles->currentPage() * $roles->perPage(), $roles->total()) }}</span>
                    của
                    <span id="totalRecords">{{ $roles->total() }}</span> mục
                </div>
                <div class="data-table-pagination-controls">
                    @if (!$roles->onFirstPage())
                        <a href="{{ $roles->previousPageUrl() }}" class="data-table-pagination-btn" id="prevBtn">
                            <i class="fas fa-chevron-left"></i> Trước
                        </a>
                    @endif
                    @for ($i = 1; $i <= $roles->lastPage(); $i++)
                        <a href="{{ $roles->url($i) }}"
                            class="data-table-pagination-btn {{ $roles->currentPage() == $i ? 'active' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor
                    @if ($roles->hasMorePages())
                        <a href="{{ $roles->nextPageUrl() }}" class="data-table-pagination-btn" id="nextBtn">
                            Tiếp <i class="fas fa-chevron-right"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>


    {{-- Scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
