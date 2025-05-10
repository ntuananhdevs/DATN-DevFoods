@extends('layouts.admin.contentLayoutMaster')

@section('content')
    <div class="data-table-wrapper">
        <!-- Header chính -->
        <div class="data-table-main-header">
            <div class="data-table-brand">
                <div class="data-table-logo">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h1 class="data-table-title">Quản lý đơn ứng tuyển tài xế</h1>
            </div>
            <div class="data-table-header-actions">
                <a href="{{ route('admin.drivers.applications.index') }}" class="data-table-btn data-table-btn-outline">
                    <i class="fas fa-list"></i> Xem tất cả
                </a>
            </div>
        </div>

        <!-- Card chứa bảng -->
        <div class="data-table-card">
            <!-- Tiêu đề bảng -->
            <div class="data-table-header">
                <h2 class="data-table-card-title">Danh sách đơn ứng tuyển đang chờ duyệt</h2>
            </div>

            <!-- Thanh công cụ -->
            <div class="data-table-controls">
                <div class="data-table-search">
                    <i class="fas fa-search data-table-search-icon"></i>
                    <input type="text" placeholder="Tìm kiếm theo tên, số điện thoại hoặc biển số xe..." id="dataTableSearch">
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
                            <th data-sort="license_plate">
                                Biển số xe <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="vehicle_type">
                                Loại xe <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="created_at">
                                Ngày ứng tuyển <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="dataTableBody">
                        @forelse($applications as $application)
                            <tr>
                                <td>
                                    <div class="data-table-id">
                                        <span class="data-table-id-icon"><i class="fas fa-file-alt"></i></span>
                                        {{ $application->id }}
                                    </div>
                                </td>
                                <td>
                                    <div class="data-table-product-name">{{ $application->full_name }}</div>
                                </td>
                                <td>{{ $application->phone_number }}</td>
                                <td>{{ $application->license_plate }}</td>
                                <td>{{ ucfirst($application->vehicle_type) }}</td>
                                <td>{{ $application->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="data-table-action-buttons">
                                        <a href="{{ route('admin.drivers.applications.show', $application) }}"
                                            class="data-table-action-btn data-table-tooltip" data-tooltip="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.drivers.applications.approve', $application) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="data-table-action-btn edit data-table-tooltip" data-tooltip="Phê duyệt"
                                                onclick="return confirm('Bạn có chắc chắn muốn phê duyệt đơn ứng tuyển này?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="data-table-action-btn delete data-table-tooltip" data-tooltip="Từ chối"
                                            data-toggle="modal" data-target="#rejectModal{{ $application->id }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Reject Modal -->
                            <div class="modal fade" id="rejectModal{{ $application->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel{{ $application->id }}" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.drivers.applications.reject', $application) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="rejectModalLabel{{ $application->id }}">Từ chối đơn ứng tuyển</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="admin_notes">Lý do từ chối</label>
                                                    <textarea name="admin_notes" id="admin_notes" class="form-control" rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                                                <button type="submit" class="btn btn-danger">Từ chối</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="data-table-empty" id="dataTableEmpty">
                                        <div class="data-table-empty-icon">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <h3>Không có đơn ứng tuyển nào đang chờ duyệt</h3>
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
                    Hiển thị <span id="startRecord">{{ ($applications->currentPage() - 1) * $applications->perPage() + 1 }}</span>
                    đến <span id="endRecord">{{ min($applications->currentPage() * $applications->perPage(), $applications->total()) }}</span>
                    của <span id="totalRecords">{{ $applications->total() }}</span> mục
                </div>
                <div class="data-table-pagination-controls">
                    @if (!$applications->onFirstPage())
                        <a href="{{ $applications->previousPageUrl() }}" class="data-table-pagination-btn" id="prevBtn">
                            <i class="fas fa-chevron-left"></i> Trước
                        </a>
                    @endif

                    @for ($i = 1; $i <= $applications->lastPage(); $i++)
                        <a href="{{ $applications->url($i) }}"
                            class="data-table-pagination-btn {{ $applications->currentPage() == $i ? 'active' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor

                    @if ($applications->hasMorePages())
                        <a href="{{ $applications->nextPageUrl() }}" class="data-table-pagination-btn" id="nextBtn">
                            Tiếp <i class="fas fa-chevron-right"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 