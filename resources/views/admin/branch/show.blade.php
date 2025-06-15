@extends('layouts.admin.contentLayoutMaster')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/branchs/branch-show.css') }}">
@endsection
@section('content')
<div class="branch-details-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-left">
                <i class="fas fa-building header-icon"></i>
                <div class="header-text">
                    <h1>Chi tiết chi nhánh</h1>
                    <p>Quản lý thông tin chi nhánh {{ $branch->name }}</p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.branches.edit', $branch->id) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Chỉnh sửa</a>
                <a href="{{ route('admin.branches.index') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Quay lại</a>
            </div>
        </div>
    </div>

    <!-- Branch Overview -->
    <div class="card branch-overview">
        <div class="branch-banner">
            <span class="status-badge {{ $branch->active ? 'active' : 'inactive' }}">
                <i class="fas {{ $branch->active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                {{ $branch->active ? 'Đang hoạt động' : 'Ngưng hoạt động' }}
            </span>
        </div>
        <div class="branch-overview-content">
            <div class="branch-overview-left">
                <i class="fas fa-store-alt branch-avatar"></i>
                <div class="branch-info">
                    <h2>{{ $branch->name }}</h2>
                    <div class="branch-address"><i class="fas fa-map-marker-alt"></i> {{ $branch->address }}</div>
                    <div class="branch-contact">
                        <div class="contact-item"><i class="fas fa-phone"></i> <a href="tel:{{ $branch->phone }}">{{ $branch->phone }}</a></div>
                        @if($branch->email)
                        <div class="contact-item"><i class="fas fa-envelope"></i> <a href="mailto:{{ $branch->email }}">{{ $branch->email }}</a></div>
                        @endif
                    </div>
                    @if($branch->rating)
                    <div class="branch-rating">
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa{{ $i <= $branch->rating ? 's fa-star' : ($i - 0.5 <= $branch->rating ? 's fa-star-half-alt' : 'r fa-star') }}"></i>
                                @endfor
                        </div>
                        <span class="rating-value">{{ number_format($branch->rating, 1) }}</span>
                    </div>
                    @endif
                </div>
            </div>
            <div class="branch-overview-right">
                <div class="hours-container">
                    <div class="hours-item opening">
                        <i class="fas fa-sun hours-icon" style="color: #47d46b;"></i>
                        <div class="hours-info">
                            <span class="hours-label">Giờ mở cửa</span><br>
                            <span class="hours-value">{{ date('H:i', strtotime($branch->opening_hour)) }}</span>
                        </div>
                    </div>
                    <div class="hours-item closing">
                        <i class="fas fa-moon hours-icon" style="color: #db5757;"></i>
                        <div class="hours-info">
                            <span class="hours-label">Giờ đóng cửa</span>
                            <span class="hours-value">{{ date('H:i', strtotime($branch->closing_hour)) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="main-column">
            <!-- Branch Images -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-images card-icon" style="color: #2563eb;"></i>
                    <h3>Hình ảnh chi nhánh</h3>
                    <button class="btn btn-sm btn-outline" id="uploadImagesBtn"><i class="fas fa-upload"></i> Tải lên</button>
                </div>
                <div class="card-body">
                    @if($branch->images->count())
                    <div class="gallery-grid">
                        @foreach($branch->images as $index => $image)
                        <div class="gallery-item" data-image-id="{{ $image->id }}">
                            <img src="{{ Storage::disk('s3')->url($image->image_path) }}" alt="{{ $branch->name }} - Hình {{ $index + 1 }}" class="gallery-img">
                            @if($image->is_primary)
                            <div class="primary-badge">
                                <i class="fas fa-star"></i>
                                <span>Ảnh đại diện</span>
                            </div>
                            @endif
                            <div class="gallery-overlay">
                                <a href="{{ Storage::disk('s3')->url($image->image_path) }}" class="gallery-btn view-btn" data-fancybox="branch-gallery" data-caption="{{ $branch->name }} - {{ $image->caption ?? 'Hình ' . ($index + 1) }}"><i class="fas fa-search-plus"></i></a>
                                <button class="gallery-btn {{ $image->is_primary ? 'featured-btn' : 'set-featured-btn' }}" data-image-id="{{ $image->id }}"><i class="fa{{ $image->is_primary ? 's' : 'r' }} fa-star"></i></button>
                                <button class="gallery-btn delete-btn" data-image-id="{{ $image->id }}" data-branch-id="{{ $branch->id }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            @if($image->caption)
                            <div class="gallery-caption">{{ $image->caption }}</div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state" id="emptyState">
                        <i class="fas fa-images empty-icon"></i>
                        <h4>Chưa có hình ảnh</h4>
                        <p>Chi nhánh này chưa có hình ảnh nào</p>
                        <button class="btn btn-primary" id="emptyStateUploadBtn"><i class="fas fa-upload"></i> Tải lên hình ảnh</button>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Basic Information -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-info-circle card-icon" style="color: #2563eb;"></i>
                    <h3>Thông tin cơ bản</h3>
                </div>
                <div class="card-body p-0">
                    <div class="info-table">
                        <div class="info-row">
                            <div class="info-label"><i class="fas fa-hashtag"></i> Mã Chi nhánh</div>
                            <div class="info-value"><span class="id-badge">#{{ $branch->branch_code }}</span></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label"><i class="fas fa-building"></i> Tên chi nhánh</div>
                            <div class="info-value fw-bold">{{ $branch->name }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label"><i class="fas fa-map-marker-alt"></i> Địa chỉ</div>
                            <div class="info-value">{{ $branch->address }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label"><i class="fas fa-phone"></i> Số điện thoại</div>
                            <div class="info-value"><a href="tel:{{ $branch->phone }}" class="link-hover">{{ $branch->phone }}</a></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label"><i class="fas fa-envelope"></i> Email</div>
                            <div class="info-value">
                                @if($branch->email)
                                <a href="mailto:{{ $branch->email }}" class="link-hover">{{ $branch->email }}</a>
                                @else
                                <span class="text-muted"><i class="fas fa-minus"></i> Chưa cập nhật</span>
                                @endif
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label"><i class="fas fa-calendar-plus"></i> Ngày tạo</div>
                            <div class="info-value">
                                <div class="date-time">
                                    <span class="date"><i class="far fa-calendar-alt"></i> {{ $branch->created_at->format('d/m/Y') }}</span>
                                    <span class="time"><i class="far fa-clock"></i> {{ $branch->created_at->format('H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Operating Hours -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-clock card-icon" style="color: #2563eb;"></i>
                    <h3>Giờ hoạt động</h3>
                </div>
                <div class="card-body">
                    <div class="hours-grid">
                        <div class="hours-card opening-hours">
                            <i class="fas fa-sun hours-card-icon" style="color: #84d973;"></i>
                            <div class="hours-card-content">
                                <span class="hours-card-label">Giờ mở cửa</span>
                                <span class="hours-card-value">{{ date('H:i', strtotime($branch->opening_hour)) }}</span>
                            </div>
                        </div>
                        <div class="hours-card closing-hours">
                            <i class="fas fa-moon hours-card-icon" style="color: #d86565;"></i>
                            <div class="hours-card-content">
                                <span class="hours-card-label">Giờ đóng cửa</span>
                                <span class="hours-card-value">{{ date('H:i', strtotime($branch->closing_hour)) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="side-column">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user-tie card-icon" style="color: #2563eb;"></i>
                    <h3>Quản lý chi nhánh</h3>
                </div>
                <div class="card-body">
                    @if($branch->manager)
                    <div class="manager-profile">
                        <div class="manager-cover"></div>
                        <i class="fas fa-user manager-avatar"></i>
                        <div class="manager-info">
                            <h4>{{ $branch->manager->full_name }}</h4>
                            <div class="manager-role"><i class="fas fa-briefcase" style="color: #2563eb;"></i> Quản lý chi nhánh</div>
                            <div class="manager-actions">
                                <div class="action-row">
                                    <a href="mailto:{{ $branch->manager->email }}" class="btn btn-outline btn-sm"><i class="fas fa-envelope"></i> Gửi email</a>
                                    <a href="tel:{{ $branch->manager->phone }}" class="btn btn-outline btn-sm"><i class="fas fa-phone"></i> Liên hệ</a>
                                </div>
                                @if($branch->active)
                                <a href="{{ route('admin.branches.assign-manager', $branch->id) }}" class="btn btn-outline btn-block btn-sm"><i class="fas fa-exchange-alt"></i> Thay đổi quản lý</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-user-slash empty-icon"></i>
                        <h4>Chưa phân công quản lý</h4>
                        @if($branch->active)
                        <p>Chi nhánh này chưa có người quản lý</p>
                        <a href="{{ route('admin.branches.assign-manager', $branch->id) }}" class="btn btn-primary"><i class="fas fa-plus"></i> Phân công quản lý</a>
                        @else
                        <p>Chi nhánh đã bị vô hiệu hóa</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-star card-icon" style="color: #2563eb;"></i>
                    <h3>Đánh giá khách hàng</h3>
                </div>
                <div class="card-body">
                    @if($branch->rating)
                    <div class="rating-summary">
                        <div class="rating-circle">
                            <div class="rating-value">{{ number_format($branch->rating, 1) }}</div>
                            <div class="rating-max">/ 5.0</div>
                        </div>
                        <div class="rating-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa{{ $i <= $branch->rating ? 's fa-star' : ($i - 0.5 <= $branch->rating ? 's fa-star-half-alt' : 'r fa-star') }}"></i>
                                @endfor
                        </div>
                        <p class="rating-caption">Dựa trên đánh giá của khách hàng</p>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-star-half-alt empty-icon"></i>
                        <h4>Chưa có đánh giá</h4>
                        <p>Chi nhánh này chưa nhận được đánh giá nào</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-bolt card-icon" style="color: #2563eb;"></i>
                    <h3>Thao tác nhanh</h3>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <div class="action-item" data-action="report"><i class="fas fa-chart-bar action-icon"></i> Báo cáo</div>
                        <div class="action-item" data-action="staff"><i class="fas fa-users action-icon"></i> Nhân viên</div>
                        <div class="action-item" data-action="schedule"><i class="fas fa-calendar-alt action-icon"></i> Lịch làm việc</div>
                        <div class="action-item" data-action="settings"><i class="fas fa-cog action-icon"></i> Cài đặt</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Images Modal -->
<div class="modal" id="uploadImagesModal">
    <div class="modal-backdrop"></div>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <h3>Tải lên hình ảnh chi nhánh</h3>
                </div>
                <button class="modal-close" id="closeUploadModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadImagesForm" action="{{ route('admin.branches.upload-image', $branch->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="branchImages" class="form-label">
                            <i class="fas fa-images"></i>
                            Chọn hình ảnh
                        </label>
                        <div class="file-upload-wrapper enhanced">
                            <input type="file" id="branchImages" name="images[]" multiple accept="image/*" required class="file-upload-input">
                            <div class="file-upload-content">
                                <div class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="upload-text">
                                    <h4>Kéo thả hoặc click để chọn ảnh</h4>
                                    <p>Hỗ trợ định dạng: JPG, PNG, GIF</p>
                                </div>
                                <div class="upload-button">
                                    <span class="btn btn-primary btn-sm">
                                        <i class="fas fa-folder-open"></i>
                                        Chọn tệp
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-hint enhanced">
                            <div class="hint-item">
                                <i class="fas fa-info-circle"></i>
                                <span>Tối đa 5MB/ảnh</span>
                            </div>
                            <div class="hint-item">
                                <i class="fas fa-images"></i>
                                <span>Có thể chọn nhiều ảnh cùng lúc</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="featured-option">
                            <label class="form-check enhanced">
                                <input type="checkbox" id="setAsFeatured" name="set_as_featured" class="form-check-input">
                                <span class="form-check-label">
                                    <div class="check-icon">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="check-text">
                                        <strong>Đặt ảnh đầu làm ảnh đại diện</strong>
                                        <small>Ảnh đầu tiên sẽ được hiển thị làm ảnh đại diện của chi nhánh</small>
                                    </div>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="image-preview-container hidden">
                        <div class="preview-header">
                            <h4><i class="fas fa-eye"></i> Xem trước</h4>
                            <button type="button" class="btn btn-link btn-sm clear-preview">
                                <i class="fas fa-trash-alt"></i> Xóa tất cả
                            </button>
                        </div>
                        <div class="image-preview-grid enhanced" id="imagePreviewGrid"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" id="cancelUploadBtn">
                    <i class="fas fa-times"></i> Hủy
                </button>
                <button type="submit" form="uploadImagesForm" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Tải lên
                </button>
            </div>
        </div>
    </div>
</div>


@section('scripts')
    <script>
        // Đặt branchId vào window object để JavaScript có thể sử dụng
        window.branchId = {{ $branch->id }};
    </script>
    <script src="{{ asset('js/admin/branchs/branch-show.js') }}"></script>
@endsection
@endsection