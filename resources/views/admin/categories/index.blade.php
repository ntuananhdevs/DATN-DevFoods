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

            {{-- Card bảng --}}
            <div class="data-table-card">
                <div class="data-table-header">
                    <h2 class="data-table-card-title">Danh sách danh mục</h2>
                </div>

                <div class="data-table-controls">
                    <form method="GET" action="{{ route('admin.categories.index') }}" class="data-table-search">
                        <i class="fas fa-search data-table-search-icon"></i>
                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                            placeholder="Tìm kiếm theo tên danh mục, hoặc id..." id="dataTableSearch">
                    </form>
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
                                            <form action="{{ route('admin.categories.destroy', $category->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="data-table-action-btn delete"
                                                    data-tooltip="Xóa">
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
                            <th data-sort="description">
                                Mô tả <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
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
                                        <span class="data-table-id-icon"><i class="fas fa-tag"></i></span>
                                        {{ $category->id }}
                                    </div>
                                </td>
                                <td>
                                    <div class="data-table-product-name">{{ $category->name }}</div>
                                </td>
                                <td>{{ Str::limit($category->description, 50) }}</td>
                                <td>
                                    <div class="data-table-product-image">
                                        @if ($category->image)
                                            <img src="{{ asset('storage/' . $category->image) }}"
                                                alt="{{ $category->name }}" width="50%">
                                        @else
                                            <span class="text-muted">Không có</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if ($category->status)
                                        <span class="data-table-status data-table-status-success">
                                            <i class="fas fa-check"></i> Hiển thị
                                        </span>
                                    @else
                                        <span class="data-table-status data-table-status-failed">
                                            <i class="fas fa-times"></i> Ẩn
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="data-table-action-buttons">
                                        <a href="{{ route('admin.categories.show', $category->id) }}"
                                            class="data-table-action-btn data-table-tooltip" data-tooltip="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.categories.edit', $category->id) }}"
                                            class="data-table-action-btn edit data-table-tooltip" data-tooltip="Chỉnh sửa">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="data-table-action-btn delete data-table-tooltip"
                                                data-tooltip="Xóa"
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
