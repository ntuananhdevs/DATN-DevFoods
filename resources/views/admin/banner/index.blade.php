@extends('layouts.admin.contentLayoutMaster')

@section('content')
    <div class="data-table-wrapper">
        <!-- Main Header -->
        <div class="data-table-main-header">
            <div class="data-table-brand">
                <div class="data-table-logo">
                    <i class="fas fa-image"></i>
                </div>
                <h1 class="data-table-title">Quản lý banner</h1>
            </div>
            <div class="data-table-header-actions">
                <div class="btn-group mr-2">
                    {{-- <button type="button" class="data-table-btn data-table-btn-outline dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-download"></i> Xuất
                                </button> --}}
                    {{-- <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('admin.banners.export', ['type' => 'excel']) }}">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </a>
                                    <a class="dropdown-item" href="{{ route('admin.banners.export', ['type' => 'pdf']) }}">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                    <a class="dropdown-item" href="{{ route('admin.banners.export', ['type' => 'csv']) }}">
                                        <i class="fas fa-file-csv"></i> CSV
                                    </a>
                                </div> --}}
                </div>

                <a href="{{ route('admin.banners.create') }}" class="data-table-btn data-table-btn-primary">
                    <i class="fas fa-plus"></i> Thêm mới
                </a>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="data-table-card">
            <!-- Table Header -->
            <div class="data-table-header">
                <h2 class="data-table-card-title">Danh sách banner</h2>
            </div>

            <!-- Controls -->
            <div class="data-table-controls">
                <div class="data-table-search">
                    <i class="fas fa-search data-table-search-icon"></i>
                    <input type="text" placeholder="Tìm kiếm theo tiêu đề, mô tả ..." id="dataTableSearch"
                        value="{{ request('search') }}" onkeyup="handleSearch(event)">
                </div>
                <div class="data-table-actions">
                    <div class="d-flex align-items-center">
                        <div class="data-table-actions">
                            <div class="d-flex align-items-center">
                                <button class="data-table-btn data-table-btn-outline mr-2" onclick="toggleSelectAll()">
                                    <i class="fas fa-check-square"></i> Chọn tất cả
                                </button>
                                <div class="btn-group mr-2">
                                    <button type="button" class="data-table-btn data-table-btn-outline dropdown-toggle"
                                        data-toggle="dropdown">
                                        <i class="fas fa-tasks"></i> Thao tác
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="#" class="dropdown-item" onclick="updateSelectedBannerStatus(1)">
                                            <i class="fas fa-check-circle text-success"></i> Kích hoạt đã chọn
                                        </a>
                                        <a href="#" class="dropdown-item" onclick="updateSelectedBannerStatus(0)">
                                            <i class="fas fa-times-circle text-danger"></i> Vô hiệu hóa đã chọn
                                        </a>
                                    </div>
                                </div>


                                <button class="data-table-btn data-table-btn-outline">
                                    <i class="fas fa-columns"></i> Cột
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="data-table-container">
                <table class="data-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>
                                <div>
                                    <input type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <th data-sort="id" class="active-sort">
                                ID <i class="fas fa-arrow-up data-table-sort-icon"></i>
                            </th>
                            <th>Hình ảnh</th>
                            <th data-sort="title">
                                Tiêu đề <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th>Trạng thái</th>
                            <th>Ngày bắt đầu</th>
                            <th>Ngày kết thúc</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="dataTableBody">
                        @forelse($banners as $banner)
                            <tr>
                                <td>
                                    <input type="checkbox" class="row-checkbox" value="{{ $banner->id }}">
                                </td>
                                <td>
                                    <div class="data-table-id">
                                        {{ $banner->id }}
                                    </div>
                                </td>
                                <td>
                                    <div class="data-table-banner-image">
                                        <img src="{{ $banner->image_path ? asset('storage/' . $banner->image_path) : asset('images/default-banner.png') }}"
                                            alt="{{ $banner->title }}"
                                            style="width: 100px; height: auto; object-fit: cover;">
                                    </div>
                                </td>
                                <td>
                                    <div class="data-table-banner-title">{{ $banner->title }}</div>
                                </td>

                                <td>
                                    <form action="{{ route('admin.banners.toggle-status', $banner->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button"
                                            class="data-table-status {{ $banner->is_active ? 'data-table-status-success' : 'data-table-status-failed' }}"
                                            style="border: none; cursor: pointer; width: 100px;"
                                            onclick="dtmodalHandleStatusToggle({
                                        button: this,
                                        bannerTitle: '{{ $banner->title }}',
                                        currentStatus: {{ $banner->is_active ? 'true' : 'false' }},
                                        confirmTitle: 'Xác nhận thay đổi trạng thái',
                                        confirmSubtitle: 'Bạn có chắc chắn muốn thay đổi trạng thái của banner này?',
                                        confirmMessage: 'Hành động này sẽ thay đổi trạng thái hoạt động của banner.',
                                        successMessage: 'Đã thay đổi trạng thái banner thành công',
                                        errorMessage: 'Có lỗi xảy ra khi thay đổi trạng thái banner'
                                    })">
                                            @if ($banner->is_active)
                                                <i class="fas fa-check"></i> Hoạt động
                                            @else
                                                <i class="fas fa-times"></i> Vô hiệu hóa
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td>{{ $banner->start_at->format('d/m/Y') }}</td>
                                <td>{{ $banner->end_at->format('d/m/Y') }}</td>

                                <td>
                                    <div class="data-table-action-buttons">
                                        <a href="{{ route('admin.banners.show', $banner->id) }}"
                                            class="data-table-action-btn data-table-tooltip" data-tooltip="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.banners.edit', $banner->id) }}"
                                            class="data-table-action-btn edit" title="Chỉnh sửa">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                    </div>
                                    <form id="bulkStatusForm" action="{{ route('admin.banners.bulk-status-update') }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="ids" id="ids">
                                        <input type="hidden" name="status" id="status">
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center">
                                    <div class="data-table-empty">
                                        <div class="data-table-empty-icon">
                                            <i class="fas fa-image"></i>
                                        </div>
                                        <h3>Không có banner nào</h3>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="data-table-footer">
                <div class="data-table-pagination-info">
                    Hiển thị <span id="startRecord">{{ ($banners->currentPage() - 1) * $banners->perPage() + 1 }}</span>
                    đến <span
                        id="endRecord">{{ min($banners->currentPage() * $banners->perPage(), $banners->total()) }}</span>
                    của <span id="totalRecords">{{ $banners->total() }}</span> mục
                </div>
                @if ($banners->lastPage() > 1)
                    <div class="data-table-pagination-controls">
                        @if (!$banners->onFirstPage())
                            <a href="{{ $banners->previousPageUrl() }}&search={{ request('search') }}"
                                class="data-table-pagination-btn" id="prevBtn">
                                <i class="fas fa-chevron-left"></i> Trước
                            </a>
                        @endif

                        @php
                            $start = max(1, $banners->currentPage() - 2);
                            $end = min($banners->lastPage(), $banners->currentPage() + 2);

                            if ($start > 1) {
                                echo '<a href="' .
                                    $banners->url(1) .
                                    '&search=' .
                                    request('search') .
                                    '"
                    class="data-table-pagination-btn">1</a>';
                                if ($start > 2) {
                                    echo '<span class="data-table-pagination-dots">...</span>';
                                }
                            }
                        @endphp

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $banners->url($i) }}&search={{ request('search') }}"
                                class="data-table-pagination-btn {{ $banners->currentPage() == $i ? 'active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @php
                            if ($end < $banners->lastPage()) {
                                if ($end < $banners->lastPage() - 1) {
                                    echo '<span class="data-table-pagination-dots">...</span>';
                                }
                                echo '<a href="' .
                                    $banners->url($banners->lastPage()) .
                                    '&search=' .
                                    request('search') .
                                    '"
                                class="data-table-pagination-btn">' .
                                    $banners->lastPage() .
                                    '</a>';
                            }
                        @endphp

                        @if ($banners->hasMorePages())
                            <a href="{{ $banners->nextPageUrl() }}&search={{ request('search') }}"
                                class="data-table-pagination-btn" id="nextBtn">
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

        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const rowCheckboxes = document.getElementsByClassName('row-checkbox');

            selectAllCheckbox.checked = !selectAllCheckbox.checked;
            for (let checkbox of rowCheckboxes) {
                checkbox.checked = selectAllCheckbox.checked;
            }
        }

        document.getElementById('selectAll').addEventListener('change', function() {
            const rowCheckboxes = document.getElementsByClassName('row-checkbox');
            for (let checkbox of rowCheckboxes) {
                checkbox.checked = this.checked;
            }
        });
    </script>
@endsection
