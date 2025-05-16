@extends('layouts/admin/contentLayoutMaster')
@section('content')
    <div class="data-table-wrapper">
        <!-- Header chính -->
        <div class="data-table-main-header">
            <div class="data-table-brand">
                <div class="data-table-logo">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h1 class="data-table-title">Quản lý đơn đăng ký tài xế</h1>
            </div>
            <div class="data-table-header-actions">
                <div class="dropdown d-inline">
                    <button class="data-table-btn data-table-btn-outline dropdown-toggle" type="button" id="exportDropdown"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-download"></i> Xuất
                    </button>
                    <div class="dropdown-menu" aria-labelledby="exportDropdown">
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-file-excel"></i> Xuất Excel
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-file-pdf"></i> Xuất PDF
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-file-csv"></i> Xuất CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card chứa bảng -->
        <div class="data-table-card">
            <!-- Tiêu đề bảng -->
            <div class="data-table-header">
                <h2 class="data-table-card-title">Đơn đăng ký đang chờ xử lý</h2>
            </div>

            <!-- Thanh công cụ -->
            <div class="data-table-controls">
                <div class="data-table-search">
                    <i class="fas fa-search data-table-search-icon"></i>
                    <input type="text" placeholder="Tìm kiếm theo tên, số điện thoại..." id="dataTableSearch">
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
                            <th data-sort="name">
                                Họ và tên <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="phone">
                                Số điện thoại <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="license">
                                Biển số xe <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="date">
                                Ngày nộp đơn <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="date">
                                Ngày cập nhật <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="dataTableBody">
                        @forelse($pendingApplications as $application)
                            <tr>
                                <td>
                                    <div class="data-table-id">
                                        {{ $application->id }}
                                    </div>
                                </td>
                                <td>
                                    <div class="data-table-product-name">{{ $application->full_name }}</div>
                                </td>
                                <td>{{ $application->phone_number }}</td>
                                <td>{{ $application->license_plate }}</td>
                                <td>{{ $application->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $application->updated_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="data-table-action-buttons">
                                        <a href="{{ route('admin.drivers.applications.show', ['application' => $application->id]) }}"
                                            class="data-table-action-btn data-table-tooltip" data-tooltip="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="data-table-empty" id="dataTableEmpty">
                                        <div class="data-table-empty-icon">
                                            </i>
                                        </div>
                                        <h3>Không có đơn đăng ký nào đang chờ xử lý</h3>
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
                    Hiển thị <span
                        id="startRecord">{{ ($pendingApplications->currentPage() - 1) * $pendingApplications->perPage() + 1 }}</span>
                    đến <span
                        id="endRecord">{{ min($pendingApplications->currentPage() * $pendingApplications->perPage(), $pendingApplications->total()) }}</span>
                    của <span id="totalRecords">{{ $pendingApplications->total() }}</span> mục
                </div>
                <div class="data-table-pagination-controls">
                    @if (!$pendingApplications->onFirstPage())
                        <a href="{{ $pendingApplications->previousPageUrl() }}" class="data-table-pagination-btn"
                            id="prevBtn">
                            <i class="fas fa-chevron-left"></i> Trước
                        </a>
                    @endif

                    @for ($i = 1; $i <= $pendingApplications->lastPage(); $i++)
                        <a href="{{ $pendingApplications->url($i) }}"
                            class="data-table-pagination-btn {{ $pendingApplications->currentPage() == $i ? 'active' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor

                    @if ($pendingApplications->hasMorePages())
                        <a href="{{ $pendingApplications->nextPageUrl() }}" class="data-table-pagination-btn"
                            id="nextBtn">
                            Tiếp <i class="fas fa-chevron-right"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card chứa bảng đã xử lý -->
        <div class="data-table-card mt-4">
            <!-- Tiêu đề bảng -->
            <div class="data-table-header">
                <h2 class="data-table-card-title">Đơn đăng ký đã xử lý</h2>
            </div>

            <!-- Container bảng -->
            <div class="data-table-container">
                <table class="data-table" id="processedTable">
                    <thead>
                        <tr>
                            <th data-sort="id" class="active-sort">
                                ID <i class="fas fa-arrow-up data-table-sort-icon"></i>
                            </th>
                            <th data-sort="name">
                                Họ và tên <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="phone">
                                Số điện thoại <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="license">
                                Biển số xe <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="status">
                                Trạng thái <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="created_date">
                                Ngày nộp đơn <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="updated_date">
                                Ngày cập nhật <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($processedApplications as $application)
                            <tr>
                                <td>
                                    <div class="data-table-id">{{ $application->id }}</div>
                                </td>
                                <td>
                                    <div class="data-table-product-name">{{ $application->full_name }}</div>
                                </td>
                                <td>{{ $application->phone_number }}</td>
                                <td>{{ $application->license_plate }}</td>
                                <td>
                                    <span
                                        class="data-table-status {{ $application->status === 'approved' ? 'data-table-status-success' : 'data-table-status-failed' }}">
                                        <i
                                            class="fas fa-{{ $application->status === 'approved' ? 'check' : 'times' }}"></i>
                                        {{ $application->status === 'approved' ? 'Đã duyệt' : 'Đã từ chối' }}
                                    </span>
                                </td>
                                <td>{{ $application->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $application->updated_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="data-table-action-buttons">
                                        <a href="{{ route('admin.drivers.applications.show', $application) }}"
                                            class="data-table-action-btn data-table-tooltip" data-tooltip="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="data-table-empty">
                                        <div class="data-table-empty-icon">
                                            <i class="fas fa-inbox"></i>
                                        </div>
                                        <h3>Không có đơn đăng ký nào đã xử lý</h3>
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
                    Hiển thị
                    <span>{{ ($processedApplications->currentPage() - 1) * $processedApplications->perPage() + 1 }}</span>
                    đến
                    <span>{{ min($processedApplications->currentPage() * $processedApplications->perPage(), $processedApplications->total()) }}</span>
                    của <span>{{ $processedApplications->total() }}</span> mục
                </div>
                <div class="data-table-pagination-controls">
                    @if (!$processedApplications->onFirstPage())
                        <a href="{{ $processedApplications->previousPageUrl() }}" class="data-table-pagination-btn">
                            <i class="fas fa-chevron-left"></i> Trước
                        </a>
                    @endif

                    @for ($i = 1; $i <= $processedApplications->lastPage(); $i++)
                        <a href="{{ $processedApplications->url($i) }}"
                            class="data-table-pagination-btn {{ $processedApplications->currentPage() == $i ? 'active' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor

                    @if ($processedApplications->hasMorePages())
                        <a href="{{ $processedApplications->nextPageUrl() }}" class="data-table-pagination-btn">
                            Tiếp <i class="fas fa-chevron-right"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
