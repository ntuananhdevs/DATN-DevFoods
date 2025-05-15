@extends('layouts.admin.contentLayoutMaster')

@section('content')
    <div class="data-table-wrapper">
        {{-- Header chính --}}
        <div class="data-table-main-header">
            <div class="data-table-brand">
                <div class="data-table-logo">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h1 class="data-table-title">Quản lý danh mục</h1>
            </div>

            <div class="data-table-header-actions">
                <a href="{{ route('admin.categories.create') }}" class="data-table-btn data-table-btn-primary">
                    <i class="fas fa-plus"></i> Thêm mới
                </a>
            </div>
        </div>

        {{-- Card bảng --}}
        <div class="data-table-card">
            <div class="data-table-header">
                <h2 class="data-table-card-title">Danh sách danh mục</h2>
            </div>

            <!-- Thanh công cụ -->
            {{-- <form method="GET" action="{{ route('admin.categories.index') }}">
                <div class="data-table-controls">
                    <div class="data-table-search">
                        <i class="fas fa-search data-table-search-icon"></i>
                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                            placeholder="Tìm kiếm theo tên, mã danh mục..." id="dataTableSearch">
                    </div>
                    <div class="data-table-actions">
                        <button class="data-table-btn data-table-btn-outline" type="submit">
                            <i class="fas fa-sliders"></i> Cột
                        </button>
                        <button class="data-table-btn data-table-btn-outline" data-bs-toggle="modal" data-bs-target="#filterModal" type="button">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                    </div>
                </div>
            </form> --}}

            <!-- Modal Lọc -->
            {{-- <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('admin.categories.index') }}" method="GET">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Lọc danh mục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <label>Trạng thái</label>
                        <select name="status" class="form-control">
                            <option value="">-- Tất cả --</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hiển thị</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Ẩn</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Áp dụng</button>
                </div>
            </div>
        </form>
    </div>
</div> --}}



            {{-- Bảng danh mục --}}
            <div class="data-table-container">
                <table class="data-table" id="dataTable">
                    <thead>
                        <tr>
                            <th data-sort="id" class="active-sort">
                                ID <i class="fas fa-arrow-up data-table-sort-icon"></i>
                            </th>
                            <th data-sort="category">
                                Tên danh mục <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            {{-- <th data-sort="description">
                            Mô tả <i class="fas fa-sort data-table-sort-icon"></i>
                        </th> --}}
                            <th data-sort="image">
                                Hình ảnh <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="status">
                                Trạng thái <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>
                                    <div class="data-table-id">
                                        {{ $category->id }}
                                    </div>
                                </td>
                                <td>
                                    <div class="data-table-product-name">{{ $category->name }}</div>
                                </td>
                                {{-- <td>{{ Str::limit($category->description, 50) }}</td> --}}
                                <td>
                                    <div class="data-table-product-image">
                                        @if ($category->image)
                                            <img src="{{ asset('storage/' . $category->image) }}"
                                                alt="{{ $category->name }}" width="200px">
                                        @else
                                            <span class="data-table-status data-table-status-failed">
                                                <i class="fas fa-times"></i> Ẩn
                                            </span>
                                        @endif
                                </td>
                                <td>
                                    <div class="data-table-action-buttons">
                                        <a href="{{ route('admin.categories.show', $category->id) }}"
                                            class="data-table-action-btn" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.categories.edit', $category->id) }}"
                                            class="data-table-action-btn edit" title="Chỉnh sửa">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="data-table-action-btn delete" title="Xóa"
                                                onclick="dtmodalConfirmDelete({
                                                itemName: '{{ $category->name }}',
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
                                <td colspan="6" class="text-center">
                                    <div class="data-table-empty" id="dataTableEmpty">
                                        <div class="data-table-empty-icon">
                                            <i class="fas fa-box-open"></i>
                                        </div>
                                        <h3>Không có danh mục nào</h3>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Phân trang --}}
            <div class="data-table-footer">
                <div class="data-table-pagination-info">
                    Hiển thị <span
                        id="startRecord">{{ ($categories->currentPage() - 1) * $categories->perPage() + 1 }}</span>
                    đến <span
                        id="endRecord">{{ min($categories->currentPage() * $categories->perPage(), $categories->total()) }}</span>
                    của <span id="totalRecords">{{ $categories->total() }}</span> mục
                </div>
                <div class="data-table-pagination-controls">
                    @if (!$categories->onFirstPage())
                        <a href="{{ $categories->previousPageUrl() }}" class="data-table-pagination-btn" id="prevBtn">
                            <i class="fas fa-chevron-left"></i> Trước
                        </a>
                    @endif

                    @for ($i = 1; $i <= $categories->lastPage(); $i++)
                        <a href="{{ $categories->url($i) }}"
                            class="data-table-pagination-btn {{ $categories->currentPage() == $i ? 'active' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor

                    @if ($categories->hasMorePages())
                        <a href="{{ $categories->nextPageUrl() }}" class="data-table-pagination-btn" id="nextBtn">
                            Tiếp <i class="fas fa-chevron-right"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
