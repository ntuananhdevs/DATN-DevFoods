@extends('layouts.admin.contentLayoutMaster')

@section('content')
    <div class="data-table-wrapper">

        {{-- Header chính --}}
        <div class="data-table-main-header">
            <div class="data-table-brand">
                <div class="data-table-logo"><i class="fas fa-list-alt"></i></div>
                <h1 class="data-table-title">Quản lý danh mục</h1>
            </div>
            <div class="data-table-header-actions">
                <a href="{{ route('admin.categories.create') }}" class="data-table-btn data-table-btn-primary">
                    <i class="fas fa-plus"></i> Thêm danh mục
                </a>
            </div>
        </div>

        {{-- Card bảng --}}
        <div class="data-table-card">
            <div class="data-table-header">
                <h2 class="data-table-card-title">Danh sách danh mục</h2>
            </div>

            {{-- Bảng danh mục --}}
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Mô tả</th>
                            <th>Ảnh</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->name }}</td>
                                <td>{{ Str::limit($category->description, 50) }}</td>
                                <td>
                                    @if ($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}" width="60">
                                    @else
                                        <span class="text-muted">Không có</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $category->status ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $category->status ? 'Hiển thị' : 'Ẩn' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="data-table-action-buttons">
                                        <a href="{{ route('admin.categories.show', $category->id) }}"
                                            class="data-table-action-btn" data-tooltip="Xem">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.categories.edit', $category->id) }}"
                                            class="data-table-action-btn edit" data-tooltip="Sửa">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="data-table-action-btn delete" data-tooltip="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Không có danh mục nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Phân trang --}}
            <div class="data-table-footer">
                <div class="data-table-pagination-info">
                    Hiển thị {{ $categories->firstItem() }} đến {{ $categories->lastItem() }} / tổng số
                    {{ $categories->total() }}
                </div>
                <div class="data-table-pagination-controls">
                    {{ $categories->links('pagination::bootstrap-5') }}
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
@endsection
