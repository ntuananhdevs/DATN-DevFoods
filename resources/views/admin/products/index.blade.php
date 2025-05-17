@extends('layouts/admin/contentLayoutMaster')
@section('content')
<div class="data-table-wrapper">
    <!-- Header chính -->
    <div class="data-table-main-header">
        <div class="data-table-brand">
            <div class="data-table-logo">
                <i class="fas fa-layer-group"></i>
            </div>
            <h1 class=" data-table-title">Quản lý sản phẩm</h1>
        </div>
        <div class="data-table-header-actions">
            <!-- Đã xóa nút lọc ở đây -->
            <div class="dropdown d-inline">
                <button class="data-table-btn data-table-btn-outline dropdown-toggle" type="button" id="exportDropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-download"></i> Xuất
                </button>
                <div class="dropdown-menu" aria-labelledby="exportDropdown">
                    <a class="dropdown-item" href="{{ route('admin.products.export', ['type' => 'excel']) }}">
                        <i class="fas fa-file-excel"></i> Xuất Excel
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.products.export', ['type' => 'pdf']) }}">
                        <i class="fas fa-file-pdf"></i> Xuất PDF
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.products.export', ['type' => 'csv']) }}">
                        <i class="fas fa-file-csv"></i> Xuất CSV
                    </a>
                </div>
            </div>
            <a href="{{ route('admin.products.create') }}" class="data-table-btn data-table-btn-primary">
                <i class="fas fa-plus"></i> Thêm mới
            </a>
        </div>
    </div>

    <!-- Card chứa bảng -->
    <div class="data-table-card">
        <!-- Tiêu đề bảng -->
        <div class="data-table-header">
            <h2 class="data-table-card-title">Danh sách sản phẩm</h2>
        </div>

        <!-- Thanh công cụ -->
        <div class="data-table-controls">
            <div class="data-table-search">
                <i class="fas fa-search data-table-search-icon"></i>
                <input type="text" placeholder="Tìm kiếm theo tên, mã sản phẩm..." id="dataTableSearch">
            </div>
            <div class="data-table-actions">
                <button class="data-table-btn data-table-btn-outline">
                    <i class="fas fa-sliders"></i> Cột
                </button>
                <button class="data-table-btn data-table-btn-outline" data-toggle="modal" data-target="#filterModal">
                    <i class="fas fa-filter"></i> Lọc
                </button>
            </div>
        </div>

        <!-- Container bảng -->
        <div class="data-table-container">
            <table class="data-table" id="dataTable">
                <thead>
                    <tr>
                        <th data-sort="id" class="active-sort">
                            ID <i class="fas fa-arrow-up data-table-sort-icon"></i>
                        </th>
                        <th data-sort="image">
                            Hình ảnh <i class="fas fa-sort data-table-sort-icon"></i>
                        </th>
                        <th data-sort="name">
                            Tên sản phẩm <i class="fas fa-sort data-table-sort-icon"></i>
                        </th>
                        <th data-sort="category">
                            Danh mục <i class="fas fa-sort data-table-sort-icon"></i>
                        </th>
                        <th data-sort="price">
                            Giá <i class="fas fa-sort data-table-sort-icon"></i>
                        </th>
                        <th data-sort="stock">
                            Tồn kho <i class="fas fa-sort data-table-sort-icon"></i>
                        </th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="dataTableBody">
                    @forelse($products as $product)
                    <tr>
                        <td>
                            <div class="data-table-id">
                                {{ $product->id }}
                            </div>
                        </td>
                        <td>
                            <div class="data-table-product-image">
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}">
                            </div>
                        </td>
                        <td>
                            <div class="data-table-product-name">{{ $product->name }}</div>
                        </td>
                        <td>
                            {{ $product->category->name ?? 'N/A' }}
                        </td>
                        <td>
                            <div class="data-table-amount">{{ number_format($product->base_price, 0, ',', '.') }} đ
                            </div>
                        </td>
                        <td>
                            @if ($product->stock)
                            <span class="data-table-status data-table-status-success">
                                <i class="fas fa-check"></i> Còn hàng
                            </span>
                            @else
                            <span class="data-table-status data-table-status-failed">
                                <i class="fas fa-times"></i> Hết hàng
                            </span>
                            @endif
                        </td>
                        <td>
                            <div class="data-table-action-buttons">
                                <a href="{{ route('admin.products.show', $product->id) }}"
                                    class="data-table-action-btn" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                    class="data-table-action-btn edit" title="Chỉnh sửa">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="data-table-action-btn delete" title="Xóa"
                                        onclick="dtmodalConfirmDelete({
                                                    itemName: '{{ $product->name }}',
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
                        <td colspan="7" class="text-center">
                            <div class="data-table-empty" id="dataTableEmpty">
                                <div class="data-table-empty-icon">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <h3>Không có sản phẩm nào</h3>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Phân trang và thông tin -->
        <div class="data-table-footer">
            <div class="data-table-pagination-info">
                Hiển thị <span id="startRecord">{{ ($products->currentPage() - 1) * $products->perPage() + 1 }}</span>
                đến <span
                    id="endRecord">{{ min($products->currentPage() * $products->perPage(), $products->total()) }}</span>
                của <span id="totalRecords">{{ $products->total() }}</span> mục
            </div>
            <div class="data-table-pagination-controls">
                @if (!$products->onFirstPage())
                <a href="{{ $products->previousPageUrl() }}" class="data-table-pagination-btn" id="prevBtn">
                    <i class="fas fa-chevron-left"></i> Trước
                </a>
                @endif

                @for ($i = 1; $i <= $products->lastPage(); $i++)
                    <a href="{{ $products->url($i) }}"
                        class="data-table-pagination-btn {{ $products->currentPage() == $i ? 'active' : '' }}">
                        {{ $i }}
                    </a>
                    @endfor

                    @if ($products->hasMorePages())
                    <a href="{{ $products->nextPageUrl() }}" class="data-table-pagination-btn" id="nextBtn">
                        Tiếp <i class="fas fa-chevron-right"></i>
                    </a>
                    @endif
            </div>
        </div>
    </div>
</div>
<!-- Modal Lọc -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Lọc sản phẩm</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.products.index') }}" method="GET">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="filter_category">Danh mục</label>
                        <select class="form-control" id="filter_category" name="category_id">
                            <option value="">Tất cả danh mục</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filter_price_min">Giá tối thiểu</label>
                        <input type="number" class="form-control" id="filter_price_min" name="price_min"
                            value="{{ request('price_min') }}">
                    </div>
                    <div class="form-group">
                        <label for="filter_price_max">Giá tối đa</label>
                        <input type="number" class="form-control" id="filter_price_max" name="price_max"
                            value="{{ request('price_max') }}">
                    </div>
                    <div class="form-group">
                        <label for="filter_stock">Tình trạng</label>
                        <select class="form-control" id="filter_stock" name="stock_status">
                            <option value="">Tất cả</option>
                            <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>Còn
                                hàng</option>
                            <option value="out_of_stock"
                                {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Hết hàng</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Áp dụng</button>
                </div>
            </form>
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