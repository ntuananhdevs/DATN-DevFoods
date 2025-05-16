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
                {{-- <div class="dropdown d-inline">
                    <button class="data-table-btn data-table-btn-outline dropdown-toggle" type="button" id="exportDropdown"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-download"></i> Xuất
                    </button>
                    <div class="dropdown-menu" aria-labelledby="exportDropdown">
                        <a class="dropdown-item" href=" ">
                            <i class="fas fa-file-excel"></i> Xuất Excel
                        </a>
                        <a class="dropdown-item" href=" ">
                            <i class="fas fa-file-pdf"></i> Xuất PDF
                        </a>
                        <a class="dropdown-item" href=" ">
                            <i class="fas fa-file-csv"></i> Xuất CSV
                        </a>
                    </div>
                </div> --}}
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

            <!-- Controls -->
        <div class="data-table-controls">
            <div class="data-table-search">
                <i class="fas fa-search data-table-search-icon"></i>
                <input type="text"
                    placeholder="Tìm kiếm theo tên danh mục ..."
                    id="dataTableSearch"
                    value="{{ request('search') }}"
                    onkeyup="handleSearch(event)">
            </div>
            <div class="data-table-actions">
                <div class="d-flex align-items-center">
                    <div class="data-table-actions">
                        <div class="d-flex align-items-center">
                            <button class="data-table-btn data-table-btn-outline mr-2" onclick="toggleSelectAllCategories()">
                                <i class="fas fa-check-square"></i> Chọn tất cả
                            </button>
                            <div class="btn-group mr-2">
                                <button type="button" class="data-table-btn data-table-btn-outline dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-tasks"></i> Thao tác
                                </button>
                                <div class="dropdown-menu">
                                    <a href="#" class="dropdown-item"  onclick="updateSelectedCategoryStatus(1)">
                                        <i class="fas fa-check-circle text-success"></i> Hiển thị danh mục đã chọn
                                    </a>
                                    <a href="#" class="dropdown-item" onclick="updateSelectedCategoryStatus(0)">
                                        <i class="fas fa-times-circle text-danger"></i> Ẩn danh mục đã chọn
                                    </a>
                                </div>
                            </div>
                            <form id="bulkCategoryStatusForm" action="{{ route('admin.categories.bulk-status-update') }}" method="POST" style="display: none;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="category_ids" id="selectedCategoryIds">
                                <input type="hidden" name="status" id="selectedCategoryStatus">
                            </form>
                            <button class="data-table-btn data-table-btn-outline">
                                <i class="fas fa-columns"></i> Cột
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>



            {{-- Bảng danh mục --}}
            <div class="data-table-container">
                <table class="data-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>
                            <div>
                                <input type="checkbox" id="selectAllCategories">
                            </div>
                        </th>
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
                                    <input type="checkbox" class="category-row-checkbox" value="{{ $category->id }}">
                                </td>
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
                                                alt="{{ $category->name }}" width="50%">
                                        @else
                                            <span class="text-muted">Không có</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <form action="{{ route('admin.categories.toggle-status', $category->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button"
                                            class="data-table-status {{ $category->status ? 'data-table-status-success' : 'data-table-status-failed' }}"
                                            style="border: none; cursor: pointer; width: 110px;"
                                            onclick="dtmodalHandleStatusToggle({
                                                button: this,
                                                itemName: '{{ $category->name }}',
                                                currentStatus: {{ $category->status ? 'true' : 'false' }},
                                                confirmTitle: 'Xác nhận thay đổi trạng thái',
                                                confirmSubtitle: 'Bạn có chắc chắn muốn thay đổi trạng thái danh mục?',
                                                confirmMessage: 'Thao tác này sẽ thay đổi trạng thái hiển thị của danh mục.',
                                                successMessage: 'Trạng thái danh mục đã được cập nhật.',
                                                errorMessage: 'Có lỗi xảy ra khi cập nhật trạng thái.'
                                            })">
                                            @if ($category->status)
                                                <i class="fas fa-check"></i> Hiển thị
                                            @else
                                                <i class="fas fa-times"></i> Ẩn
                                            @endif
                                        </button>
                                    </form>
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
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                                            class="d-inline">
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
                Hiển thị <span id="startRecord">{{ ($categories->currentPage() - 1) * $categories->perPage() + 1 }}</span>
                đến <span id="endRecord">{{ min($categories->currentPage() * $categories->perPage(), $categories->total()) }}</span>
                của <span id="totalRecords">{{ $categories->total() }}</span> mục
            </div>
            @if($categories->lastPage() > 1)
            <div class="data-table-pagination-controls">
                @if(!$categories->onFirstPage())
                <a href="{{ $categories->previousPageUrl() }}&search={{ request('search') }}"
                    class="data-table-pagination-btn"
                    id="prevBtn">
                    <i class="fas fa-chevron-left"></i> Trước
                </a>
                @endif

                @php
                $start = max(1, $categories->currentPage() - 2);
                $end = min($categories->lastPage(), $categories->currentPage() + 2);

                if ($start > 1) {
                echo '<a href="'.$categories->url(1).'&search='.request('search').'"
                    class="data-table-pagination-btn">1</a>';
                if ($start > 2) {
                echo '<span class="data-table-pagination-dots">...</span>';
                }
                }
                @endphp

                @for ($i = $start; $i <= $end; $i++)
                    <a href="{{ $categories->url($i) }}&search={{ request('search') }}"
                    class="data-table-pagination-btn {{ $categories->currentPage() == $i ? 'active' : '' }}">
                    {{ $i }}
                    </a>
                    @endfor

                    @php
                    if ($end < $categories->lastPage()) {
                        if ($end < $categories->lastPage() - 1) {
                            echo '<span class="data-table-pagination-dots">...</span>';
                            }
                            echo '<a href="'.$categories->url($categories->lastPage()).'&search='.request('search').'"
                                class="data-table-pagination-btn">'.$categories->lastPage().'</a>';
                            }
                            @endphp

                            @if($categories->hasMorePages())
                            <a href="{{ $categories->nextPageUrl() }}&search={{ request('search') }}"
                                class="data-table-pagination-btn"
                                id="nextBtn">
                                Tiếp <i class="fas fa-chevron-right"></i>
                            </a>
                            @endif
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    function handleSearch(event) {
        const searchValue = event.target.value.trim();
        const currentUrl = new URL(window.location.href);

        if (searchValue) {
            currentUrl.searchParams.set('search', searchValue);
        } else {
            currentUrl.searchParams.delete('search');
        }

        if (event.key === 'Enter') {
            window.location.href = currentUrl.toString();
        }
    }

    function toggleSelectAllCategories() {
        const selectAllCheckbox = document.getElementById('selectAllCategories');
        const rowCheckboxes = document.getElementsByClassName('category-row-checkbox');

        selectAllCheckbox.checked = !selectAllCheckbox.checked;
        for (let checkbox of rowCheckboxes) {
            checkbox.checked = selectAllCheckbox.checked;
        }
    }

    document.getElementById('selectAllCategories').addEventListener('change', function () {
        const rowCheckboxes = document.getElementsByClassName('category-row-checkbox');
        for (let checkbox of rowCheckboxes) {
            checkbox.checked = this.checked;
        }
    });

</script>
@endsection
