@extends('layouts.admin.contentLayoutMaster')

@section('content')
    <div class="data-table-wrapper">
        <!-- Main Header -->
        <div class="data-table-main-header">
            <div class="data-table-brand">
                <div class="data-table-logo">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h1 class="data-table-title">Quản lý tài xế</h1>
            </div>
            <div class="data-table-header-actions">
                <div class="btn-group mr-2">
                    <button type="button" class="data-table-btn data-table-btn-outline dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-download"></i> Xuất
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>
                        <a class="dropdown-item" href="">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                        <a class="dropdown-item" href="">
                            <i class="fas fa-file-csv"></i> CSV
                        </a>
                    </div>
                </div>
                <a href="{{ route('admin.drivers.applications.index') }}" class="data-table-btn data-table-btn-primary">
                    <i class="fas fa-list"></i> Đơn ứng tuyển
                </a>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="data-table-card">
            <!-- Table Header -->
            <div class="data-table-header">
                <h2 class="data-table-card-title">Danh sách tài xế</h2>
            </div>

            <!-- Controls -->
            <div class="data-table-controls">
                <div class="data-table-search">
                    <i class="fas fa-search data-table-search-icon"></i>
                    <input type="text" 
                        placeholder="Tìm kiếm theo tên, email, số điện thoại..." 
                        id="dataTableSearch"
                        value="{{ request('search') }}"
                        onkeyup="handleSearch(event)">
                </div>
                <div class="data-table-actions">
                    <div class="d-flex align-items-center">
                        <div class="data-table-actions">
                            <div class="d-flex align-items-center">
                                <button class="data-table-btn data-table-btn-outline mr-2" onclick="toggleSelectAll()">
                                    <i class="fas fa-check-square"></i> Chọn tất cả
                                </button>
                                <div class="btn-group mr-2">
                                    <button type="button" class="data-table-btn data-table-btn-outline dropdown-toggle" data-toggle="dropdown">
                                        <i class="fas fa-tasks"></i> Thao tác
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="#" class="dropdown-item" onclick="updateSelectedStatus(1)">
                                            <i class="fas fa-check-circle text-success"></i> Kích hoạt đã chọn
                                        </a>
                                        <a href="#" class="dropdown-item" onclick="updateSelectedStatus(0)">
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
                            <th>Ảnh đại diện</th>
                            <th data-sort="name">
                                Tên tài xế <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Phương tiện</th>
                            <th>Đánh giá</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="dataTableBody">
                        @forelse($drivers as $driver)
                            <tr>
                                <td>
                                    <input type="checkbox" class="row-checkbox" value="{{ $driver->id }}">
                                </td>
                                <td>
                                    <div class="data-table-id">
                                        {{ $driver->id }}
                                    </div>
                                </td>
                                <td>
                                    <div class="data-table-driver-image">
                                        @if($driver->profile_image)
                                            <img src="{{ asset($driver->profile_image) }}" alt="{{ $driver->name }}"
                                                style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($driver->name) }}&background=eee&color=555" 
                                                alt="{{ $driver->name }}"
                                                style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="data-table-driver-name">{{ $driver->full_name }}</div>
                                </td>
                                <td>{{ $driver->email }}</td>
                                <td>{{ $driver->phone_number }}</td>
                                <td>
                                    <div class="data-table-vehicle-info">
                                        <span class="badge badge-info">{{ $driver->vehicle_type }}</span>
                                        <span class="badge badge-secondary">{{ $driver->vehicle_color }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="data-table-rating">
                                        <i class="fas fa-star text-warning"></i>
                                        {{ number_format($driver->rating, 1) }}
                                    </div>
                                </td>
                                <td>
                                    <form action="" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button"
                                            class="data-table-status {{ $driver->status === 'active' ? 'data-table-status-success' : 'data-table-status-failed' }}"
                                            style="border: none; cursor: pointer; width: 120px;"
                                            onclick="dtmodalHandleStatusToggle({
                                                button: this,
                                                driverName: '{{ $driver->name }}',
                                                currentStatus: {{ $driver->status === 'active' ? 'true' : 'false' }},
                                                confirmTitle: 'Xác nhận thay đổi trạng thái',
                                                confirmSubtitle: 'Bạn có chắc chắn muốn thay đổi trạng thái của tài xế này?',
                                                confirmMessage: 'Hành động này sẽ thay đổi trạng thái hoạt động của tài xế.',
                                                successMessage: 'Đã thay đổi trạng thái tài xế thành công',
                                                errorMessage: 'Có lỗi xảy ra khi thay đổi trạng thái tài xế'
                                            })"
                                            @if ($driver->status === 'active')
                                                <i class="fas fa-check"></i> Đang hoạt động
                                            @else
                                                <i class="fas fa-times"></i> Không hoạt động
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="data-table-action-buttons">
                                        <a href=""
                                            class="data-table-action-btn data-table-tooltip" data-tooltip="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href=""
                                            class="data-table-action-btn edit" title="Chỉnh sửa">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form action="" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="data-table-action-btn delete data-table-tooltip"
                                                data-tooltip="Xóa"
                                                onclick="dtmodalConfirmDelete({
                                                    itemName: '{{ $driver->name }}',
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
                                <td colspan="10" class="text-center">
                                    <div class="data-table-empty">
                                        <div class="data-table-empty-icon">
                                            <i class="fas fa-user-slash"></i>
                                        </div>
                                        <h3>Không có tài xế nào</h3>
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
                    Hiển thị <span id="startRecord">{{ ($drivers->currentPage() - 1) * $drivers->perPage() + 1 }}</span>
                    đến <span id="endRecord">{{ min($drivers->currentPage() * $drivers->perPage(), $drivers->total()) }}</span>
                    của <span id="totalRecords">{{ $drivers->total() }}</span> mục
                </div>
                @if ($drivers->lastPage() > 1)
                    <div class="data-table-pagination-controls">
                        @if (!$drivers->onFirstPage())
                            <a href="{{ $drivers->previousPageUrl() }}&search={{ request('search') }}"
                                class="data-table-pagination-btn" id="prevBtn">
                                <i class="fas fa-chevron-left"></i> Trước
                            </a>
                        @endif

                        @php
                            $start = max(1, $drivers->currentPage() - 2);
                            $end = min($drivers->lastPage(), $drivers->currentPage() + 2);

                            if ($start > 1) {
                                echo '<a href="' . $drivers->url(1) . '&search=' . request('search') . '" class="data-table-pagination-btn">1</a>';
                                if ($start > 2) {
                                    echo '<span class="data-table-pagination-dots">...</span>';
                                }
                            }
                        @endphp

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $drivers->url($i) }}&search={{ request('search') }}"
                                class="data-table-pagination-btn {{ $drivers->currentPage() == $i ? 'active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @php
                            if ($end < $drivers->lastPage()) {
                                if ($end < $drivers->lastPage() - 1) {
                                    echo '<span class="data-table-pagination-dots">...</span>';
                                }
                                echo '<a href="' . $drivers->url($drivers->lastPage()) . '&search=' . request('search') . '" class="data-table-pagination-btn">' . $drivers->lastPage() . '</a>';
                            }
                        @endphp

                        @if ($drivers->hasMorePages())
                            <a href="{{ $drivers->nextPageUrl() }}&search={{ request('search') }}"
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