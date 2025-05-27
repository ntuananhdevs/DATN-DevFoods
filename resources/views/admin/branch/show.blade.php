@extends('layouts.admin.contentLayoutMaster')

@section('content')
<!-- Main Container -->
<div class="branch-details-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="header-text">
                    <h1>Chi tiết chi nhánh</h1>
                    <p>Quản lý thông tin chi nhánh {{ $branch->name }}</p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.branches.edit', $branch->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i>
                    <span>Chỉnh sửa</span>
                </a>
                <a href="{{ route('admin.branches.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    <span>Quay lại</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Branch Overview Card -->
    <div class="card branch-overview">
        <div class="branch-banner">
            <div class="branch-status">
                @if($branch->active)
                    <span class="status-badge active">
                        <i class="fas fa-check-circle"></i>
                        Đang hoạt động
                    </span>
                @else
                    <span class="status-badge inactive">
                        <i class="fas fa-times-circle"></i>
                        Ngưng hoạt động
                    </span>
                @endif
            </div>
        </div>
        <div class="branch-overview-content">
            <div class="branch-overview-left">
                <div class="branch-avatar">
                    <i class="fas fa-store-alt"></i>
                </div>
                <div class="branch-info">
                    <h2>{{ $branch->name }}</h2>
                    <div class="branch-address">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ $branch->address }}</span>
                    </div>
                    <div class="branch-contact">
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <a href="tel:{{ $branch->phone }}">{{ $branch->phone }}</a>
                        </div>
                        @if($branch->email)
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:{{ $branch->email }}">{{ $branch->email }}</a>
                        </div>
                        @endif
                    </div>
                    @if($branch->rating)
                    <div class="branch-rating">
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $branch->rating)
                                    <i class="fas fa-star"></i>
                                @elseif($i - 0.5 <= $branch->rating)
                                    <i class="fas fa-star-half-alt"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
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
                        <div class="hours-icon">
                            <i class="fas fa-sun"></i>
                        </div>
                        <div class="hours-info">
                            <span class="hours-label">Giờ mở cửa</span>
                            <span class="hours-value">{{ $branch->opening_hour }}</span>
                        </div>
                    </div>
                    <div class="hours-item closing">
                        <div class="hours-icon">
                            <i class="fas fa-moon"></i>
                        </div>
                        <div class="hours-info">
                            <span class="hours-label">Giờ đóng cửa</span>
                            <span class="hours-value">{{ $branch->closing_hour }}</span>
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
            <!-- Branch Images Gallery -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-images"></i>
                    </div>
                    <h3>Hình ảnh chi nhánh</h3>
                    <div class="card-actions">
                        <button type="button" class="btn btn-sm btn-outline" id="uploadImagesBtn">
                            <i class="fas fa-upload"></i>
                            <span>Tải lên</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="uploadImageForm" action="{{ route('admin.branches.upload-image', $branch->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" id="imageUpload" name="image" accept="image/*" style="display: none;">
    </form>
    
    @if(isset($branch->images) && count($branch->images) > 0)
                    <div class="gallery-grid">
                        @foreach($branch->images as $index => $image)
                        <div class="gallery-item">
                            <img src="{{ asset('storage/' . $image->image_path) }}" 
                                 alt="{{ $branch->name }} - Hình {{ $index + 1 }}" 
                                 class="gallery-img">
                            <div class="gallery-overlay">
                                <div class="gallery-actions">
                                    <a href="{{ asset('storage/' . $image->path) }}" 
                                       class="gallery-btn view-btn" 
                                       data-fancybox="branch-gallery"
                                       data-caption="{{ $branch->name }} - {{ $image->caption ?? 'Hình ' . ($index + 1) }}">
                                        <i class="fas fa-search-plus"></i>
                                    </a>
                                    @if($image->is_featured)
                                        <span class="gallery-btn featured-btn">
                                            <i class="fas fa-star"></i>
                                        </span>
                                    @else
                                        <button type="button" 
                                                class="gallery-btn set-featured-btn"
                                                data-image-id="{{ $image->id }}"
                                               
                                            <i class="far fa-star"></i>
                                        </button>
                                    @endif
                                 
                                </div>
                            </div>
                            @if($image->caption)
                                <div class="gallery-caption">
                                    {{ $image->caption }}
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-images"></i>
                        </div>
                        <h4>Chưa có hình ảnh</h4>
                        <p>Chi nhánh này chưa có hình ảnh nào</p>
                        <button type="button" class="btn btn-primary" id="emptyStateUploadBtn">
                            <i class="fas fa-upload"></i>
                            <span>Tải lên hình ảnh</span>
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Basic Information -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3>Thông tin cơ bản</h3>
                </div>
                <div class="card-body p-0">
                    <div class="info-table">
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-hashtag"></i>
                                <span>ID Chi nhánh</span>
                            </div>
                            <div class="info-value">
                                <span class="id-badge">#{{ $branch->id }}</span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-building"></i>
                                <span>Tên chi nhánh</span>
                            </div>
                            <div class="info-value">
                                <span class="fw-bold">{{ $branch->name }}</span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Địa chỉ</span>
                            </div>
                            <div class="info-value">
                                {{ $branch->address }}
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-phone"></i>
                                <span>Số điện thoại</span>
                            </div>
                            <div class="info-value">
                                <a href="tel:{{ $branch->phone }}" class="link-hover">
                                    {{ $branch->phone }}
                                </a>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-envelope"></i>
                                <span>Email</span>
                            </div>
                            <div class="info-value">
                                @if($branch->email)
                                    <a href="mailto:{{ $branch->email }}" class="link-hover">
                                        {{ $branch->email }}
                                    </a>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-minus"></i> Chưa cập nhật
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-calendar-plus"></i>
                                <span>Ngày tạo</span>
                            </div>
                            <div class="info-value">
                                <div class="date-time">
                                    <span class="date">
                                        <i class="far fa-calendar-alt"></i>
                                        {{ $branch->created_at->format('d/m/Y') }}
                                    </span>
                                    <span class="time">
                                        <i class="far fa-clock"></i>
                                        {{ $branch->created_at->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Operating Hours -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Giờ hoạt động</h3>
                </div>
                <div class="card-body">
                    <div class="hours-grid">
                        <div class="hours-card opening-hours">
                            <div class="hours-card-icon">
                                <i class="fas fa-sun"></i>
                            </div>
                            <div class="hours-card-content">
                                <span class="hours-card-label">Giờ mở cửa</span>
                                <span class="hours-card-value">{{ $branch->opening_hour }}</span>
                            </div>
                        </div>
                        <div class="hours-card closing-hours">
                            <div class="hours-card-icon">
                                <i class="fas fa-moon"></i>
                            </div>
                            <div class="hours-card-content">
                                <span class="hours-card-label">Giờ đóng cửa</span>
                                <span class="hours-card-value">{{ $branch->closing_hour }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="side-column">
            <!-- Manager Information -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3>Quản lý chi nhánh</h3>
                </div>
                <div class="card-body">
                    @if($branch->manager)
                    <div class="manager-profile">
                        <div class="manager-cover"></div>
                        <div class="manager-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="manager-info">
                            <h4>{{ $branch->manager->full_name }}</h4>
                            <div class="manager-role">
                                <i class="fas fa-briefcase"></i>
                                <span>Quản lý chi nhánh</span>
                            </div>
                            <div class="manager-actions">
                                <div class="action-row">
                                    <a href="mailto:{{ $branch->manager->email }}" class="btn btn-outline btn-sm">
                                        <i class="fas fa-envelope"></i>
                                        <span>Gửi email</span>
                                    </a>
                                    <a href="tel:{{ $branch->manager->phone }}" class="btn btn-outline btn-sm">
                                        <i class="fas fa-phone"></i>
                                        <span>Liên hệ</span>
                                    </a>
                                </div>
                                <a href="{{ route('admin.branches.assign-manager', $branch->id) }}" class="btn btn-outline btn-block btn-sm">
                                    <i class="fas fa-exchange-alt"></i>
                                    <span>Thay đổi quản lý</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-user-slash"></i>
                        </div>
                        <h4>Chưa phân công quản lý</h4>
                        <p>Chi nhánh này chưa có người quản lý</p>
                        <a href="{{ route('admin.branches.assign-manager', $branch->id) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            <span>Phân công quản lý</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Rating & Reviews -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-star"></i>
                    </div>
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
                                @if($i <= $branch->rating)
                                    <i class="fas fa-star"></i>
                                @elseif($i - 0.5 <= $branch->rating)
                                    <i class="fas fa-star-half-alt"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <p class="rating-caption">Dựa trên đánh giá của khách hàng</p>
                    </div>
                    @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <h4>Chưa có đánh giá</h4>
                        <p>Chi nhánh này chưa nhận được đánh giá nào</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Thao tác nhanh</h3>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <div class="action-item" data-action="report">
                            <div class="action-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <span class="action-label">Báo cáo</span>
                        </div>
                        <div class="action-item" data-action="staff">
                            <div class="action-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <span class="action-label">Nhân viên</span>
                        </div>
                        <div class="action-item" data-action="schedule">
                            <div class="action-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <span class="action-label">Lịch làm việc</span>
                        </div>
                        <div class="action-item" data-action="settings">
                            <div class="action-icon">
                                <i class="fas fa-cog"></i>
                            </div>
                            <span class="action-label">Cài đặt</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Images Modal -->
<div class="modal" id="uploadImagesModal">
    <div class="modal-backdrop"></div>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tải lên hình ảnh chi nhánh</h3>
                <button type="button" class="modal-close" id="closeUploadModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadImagesForm" action="{{ route('admin.branches.upload-image', $branch->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="branchImages">Chọn hình ảnh</label>
                        <input type="file" id="branchImages" name="images[]" multiple accept="image/*" required>
                        <div class="form-hint">Bạn có thể chọn nhiều hình ảnh cùng lúc. Định dạng hỗ trợ: JPG, PNG, GIF.</div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" id="setAsFeatured" name="set_as_featured">
                            <label for="setAsFeatured">
                                Đặt hình ảnh đầu tiên làm ảnh đại diện
                            </label>
                        </div>
                    </div>
                    
                    <div class="image-preview-container hidden">
                        <h4>Xem trước</h4>
                        <div class="image-preview-grid" id="imagePreviewGrid"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" id="cancelUploadBtn">Hủy</button>
                <button type="submit" form="uploadImagesForm" class="btn btn-primary">
                    <i class="fas fa-upload"></i>
                    <span>Tải lên</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Image Confirmation Modal -->
<div class="modal" id="deleteImageModal">
    <div class="modal-backdrop"></div>
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Xác nhận xóa</h3>
                <button type="button" class="modal-close" id="closeDeleteModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa hình ảnh này?</p>
                <p class="text-danger">Lưu ý: Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" id="cancelDeleteBtn">Hủy</button>
                <form id="deleteImageForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Variables */
:root {
    --primary: #4361ee;
    --primary-light: #4895ef;
    --primary-dark: #3f37c9;
    --secondary: #4cc9f0;
    --success: #4ade80;
    --danger: #f43f5e;
    --warning: #f59e0b;
    --info: #3b82f6;
    --light: #f9fafb;
    --dark: #1f2937;
    --gray: #6b7280;
    --gray-light: #e5e7eb;
    --gray-dark: #4b5563;
    --white: #ffffff;
    --black: #000000;
    
    --border-radius: 12px;
    --border-radius-sm: 8px;
    --border-radius-lg: 16px;
    --border-radius-xl: 24px;
    --border-radius-full: 9999px;
    
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    
    --transition: all 0.3s ease;
    --transition-fast: all 0.15s ease;
    --transition-slow: all 0.5s ease;
}

/* Base Styles */
.branch-details-container {
    font-family: 'Inter', 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, sans-serif;
    color: var(--dark);
    max-width: 1280px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

/* Typography */
h1, h2, h3, h4, h5, h6 {
    margin: 0;
    font-weight: 600;
    line-height: 1.2;
}

h1 {
    font-size: 1.5rem;
}

h2 {
    font-size: 1.25rem;
}

h3 {
    font-size: 1.125rem;
}

h4 {
    font-size: 1rem;
}

p {
    margin: 0;
    line-height: 1.5;
}

a {
    color: var(--primary);
    text-decoration: none;
    transition: var(--transition-fast);
}

a:hover {
    color: var(--primary-dark);
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius);
    font-weight: 500;
    cursor: pointer;
    transition: var(--transition-fast);
    border: none;
    font-size: 0.875rem;
    gap: 0.5rem;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
}

.btn-block {
    width: 100%;
}

.btn-primary {
    background-color: var(--primary);
    color: var(--white);
}

.btn-primary:hover {
    background-color: var(--primary-dark);
    color: var(--white);
}

.btn-outline {
    background-color: transparent;
    color: var(--gray-dark);
    border: 1px solid var(--gray-light);
}

.btn-outline:hover {
    background-color: var(--gray-light);
    color: var(--dark);
}

.btn-danger {
    background-color: var(--danger);
    color: var(--white);
}

.btn-danger:hover {
    background-color: #e11d48;
    color: var(--white);
}

/* Page Header */
.page-header {
    margin-bottom: 1.5rem;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 1rem;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-icon {
    width: 3rem;
    height: 3rem;
    background-color: rgba(67, 97, 238, 0.1);
    color: var(--primary);
    border-radius: var(--border-radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.header-text p {
    color: var(--gray);
    margin-top: 0.25rem;
}

.header-actions {
    display: flex;
    gap: 0.75rem;
}

/* Cards */
.card {
    background-color: var(--white);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow);
    overflow: hidden;
    transition: var(--transition);
    margin-bottom: 1.5rem;
}

.card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.card-header {
    display: flex;
    align-items: center;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--gray-light);
    gap: 0.75rem;
}

.card-icon {
    width: 2.5rem;
    height: 2.5rem;
    background-color: rgba(67, 97, 238, 0.1);
    color: var(--primary);
    border-radius: var(--border-radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.card-header h3 {
    flex-grow: 1;
}

.card-actions {
    display: flex;
    gap: 0.5rem;
}

.card-body {
    padding: 1.5rem;
}

.card-body.p-0 {
    padding: 0;
}

/* Branch Overview */
.branch-overview {
    margin-bottom: 2rem;
}

.branch-banner {
    height: 8rem;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    position: relative;
}

.branch-status {
    position: absolute;
    top: 1rem;
    right: 1rem;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius-full);
    font-weight: 500;
    font-size: 0.875rem;
    gap: 0.5rem;
}

.status-badge.active {
    background-color: rgba(74, 222, 128, 0.2);
    color: var(--success);
}

.status-badge.inactive {
    background-color: rgba(244, 63, 94, 0.2);
    color: var(--danger);
}

.branch-overview-content {
    display: flex;
    flex-wrap: wrap;
    margin-top: -3rem;
    padding: 0 1.5rem 1.5rem;
    z-index: 1000;
}

.branch-overview-left {
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
    flex: 1;
    min-width: 0;
}

.branch-avatar {
    width: 6rem;
    height: 6rem;
    background-color: var(--primary);
    color: var(--white);
    border-radius: var(--border-radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    box-shadow: var(--shadow);
    border: 4px solid var(--white);
    flex-shrink: 0;
}

.branch-info {
    padding-top: 1rem;
}

.branch-address {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--gray);
    margin: 0.5rem 0 1rem;
}

.branch-address i {
    color: var(--danger);
}

.branch-contact {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.contact-item i {
    color: var(--primary);
}

.branch-rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.stars {
    color: var(--warning);
    display: flex;
    gap: 0.125rem;
}

.rating-value {
    font-weight: 600;
}

.branch-overview-right {
    margin-top: 1rem;
    flex-basis: 100%;
}

@media (min-width: 768px) {
    .branch-overview-right {
        flex-basis: auto;
        margin-top: 0;
        margin-left: auto;
    }
}

.hours-container {
    display: flex;
    gap: 1rem;
    background-color: var(--light);
    border-radius: var(--border-radius);
    padding: 1rem;
    box-shadow: var(--shadow-sm);
}

.hours-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: var(--border-radius);
    flex: 1;
}

.hours-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: var(--border-radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.hours-item.opening .hours-icon {
    background-color: rgba(74, 222, 128, 0.2);
    color: var(--success);
}

.hours-item.closing .hours-icon {
    background-color: rgba(244, 63, 94, 0.2);
    color: var(--danger);
}

.hours-info {
    display: flex;
    flex-direction: column;
}

.hours-label {
    font-size: 0.75rem;
    color: var(--gray);
}

.hours-value {
    font-weight: 600;
    font-size: 1.125rem;
}

.hours-item.opening .hours-value {
    color: var(--success);
}

.hours-item.closing .hours-value {
    color: var(--danger);
}

/* Content Grid */
.content-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 992px) {
    .content-grid {
        grid-template-columns: 2fr 1fr;
    }
}

/* Gallery */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}

.gallery-item {
    position: relative;
    border-radius: var(--border-radius);
    overflow: hidden;
    aspect-ratio: 4/3;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
}

.gallery-item:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
}

.gallery-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.gallery-overlay {
    position: absolute;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: var(--transition);
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}

.gallery-actions {
    display: flex;
    gap: 0.5rem;
}

.gallery-btn {
    width: 2.25rem;
    height: 2.25rem;
    border-radius: var(--border-radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--white);
    color: var(--dark);
    border: none;
    cursor: pointer;
    transition: var(--transition-fast);
}

.gallery-btn:hover {
    transform: scale(1.1);
}

.gallery-btn.view-btn:hover {
    background-color: var(--primary);
    color: var(--white);
}

.gallery-btn.featured-btn {
    background-color: var(--warning);
    color: var(--white);
}

.gallery-btn.set-featured-btn:hover {
    background-color: var(--warning);
    color: var(--white);
}

.gallery-btn.delete-btn:hover {
    background-color: var(--danger);
    color: var(--white);
}

.gallery-caption {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.7);
    color: var(--white);
    padding: 0.5rem;
    font-size: 0.75rem;
}

/* Info Table */
.info-table {
    display: flex;
    flex-direction: column;
}

.info-row {
    display: flex;
    border-bottom: 1px solid var(--gray-light);
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    font-weight: 500;
    flex: 0 0 200px;
}

.info-label i {
    color: var(--primary);
}

.info-value {
    padding: 1rem 1.5rem;
    flex: 1;
}

.id-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    background-color: var(--gray-light);
    border-radius: var(--border-radius);
    font-size: 0.875rem;
}

.fw-bold {
    font-weight: 600;
}

.text-muted {
    color: var(--gray);
}

.link-hover:hover {
    text-decoration: underline;
}

.date-time {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.date {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.25rem 0.75rem;
    background-color: var(--gray-light);
    border-radius: var(--border-radius-full);
    font-size: 0.875rem;
}

.time {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    color: var(--gray);
    font-size: 0.875rem;
}

/* Hours Grid */
.hours-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

@media (min-width: 576px) {
    .hours-grid {
        grid-template-columns: 1fr 1fr;
    }
}

.hours-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    border-radius: var(--border-radius);
    background-color: var(--light);
    transition: var(--transition);
}

.hours-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow);
}

.hours-card-icon {
    width: 3rem;
    height: 3rem;
    border-radius: var(--border-radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.opening-hours .hours-card-icon {
    background-color: rgba(74, 222, 128, 0.2);
    color: var(--success);
}

.closing-hours .hours-card-icon {
    background-color: rgba(244, 63, 94, 0.2);
    color: var(--danger);
}

.hours-card-content {
    display: flex;
    flex-direction: column;
}

.hours-card-label {
    font-size: 0.875rem;
    color: var(--gray);
}

.hours-card-value {
    font-weight: 600;
    font-size: 1.25rem;
}

.opening-hours .hours-card-value {
    color: var(--success);
}

.closing-hours .hours-card-value {
    color: var(--danger);
}

/* Manager Profile */
.manager-profile {
    text-align: center;
}

.manager-cover {
    height: 5rem;
    background-color: rgba(59, 130, 246, 0.1);
    border-radius: var(--border-radius) var(--border-radius) 0 0;
}

.manager-avatar {
    width: 5rem;
    height: 5rem;
    background-color: var(--info);
    color: var(--white);
    border-radius: var(--border-radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: -2.5rem auto 0.75rem;
    border: 4px solid var(--white);
    box-shadow: var(--shadow);
}

.manager-info h4 {
    margin-bottom: 0.5rem;
}

.manager-role {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    background-color: rgba(59, 130, 246, 0.1);
    color: var(--info);
    border-radius: var(--border-radius-full);
    font-size: 0.75rem;
    margin-bottom: 1.25rem;
}

.manager-actions {
    padding: 0 0.5rem;
}

.action-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}

/* Rating Summary */
.rating-summary {
    text-align: center;
}

.rating-circle {
    width: 6rem;
    height: 6rem;
    background-color: var(--white);
    border-radius: var(--border-radius-full);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    box-shadow: var(--shadow);
    border: 4px solid rgba(245, 158, 11, 0.1);
}

.rating-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--warning);
    line-height: 1;
}

.rating-max {
    font-size: 0.75rem;
    color: var(--gray);
}

.rating-stars {
    color: var(--warning);
    font-size: 1.5rem;
    margin-bottom: 0.75rem;
    display: flex;
    justify-content: center;
    gap: 0.25rem;
}

.rating-caption {
    color: var(--gray);
    font-size: 0.875rem;
}

/* Quick Actions */
.quick-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.action-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.25rem 1rem;
    border-radius: var(--border-radius);
    transition: var(--transition);
    cursor: pointer;
}

.action-item:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow);
}

.action-icon {
    width: 3rem;
    height: 3rem;
    border-radius: var(--border-radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    margin-bottom: 0.75rem;
    transition: var(--transition);
}

.action-item:hover .action-icon {
    transform: scale(1.1);
}

.action-item[data-action="report"] .action-icon {
    background-color: rgba(67, 97, 238, 0.1);
    color: var(--primary);
}

.action-item[data-action="staff"] .action-icon {
    background-color: rgba(59, 130, 246, 0.1);
    color: var(--info);
}

.action-item[data-action="schedule"] .action-icon {
    background-color: rgba(74, 222, 128, 0.1);
    color: var(--success);
}

.action-item[data-action="settings"] .action-icon {
    background-color: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.action-item[data-action="report"]:hover {
    background-color: var(--primary);
    color: var(--white);
}

.action-item[data-action="staff"]:hover {
    background-color: var(--info);
    color: var(--white);
}

.action-item[data-action="schedule"]:hover {
    background-color: var(--success);
    color: var(--white);
}

.action-item[data-action="settings"]:hover {
    background-color: var(--warning);
    color: var(--white);
}

.action-item:hover .action-icon {
    background-color: rgba(255, 255, 255, 0.2);
    color: var(--white);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 2rem 1rem;
}

.empty-icon {
    width: 5rem;
    height: 5rem;
    background-color: var(--gray-light);
    color: var(--gray);
    border-radius: var(--border-radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    margin: 0 auto 1rem;
    transition: var(--transition);
}

.empty-state:hover .empty-icon {
    transform: scale(1.05);
}

.empty-state h4 {
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: var(--gray);
    margin-bottom: 1.25rem;
}

/* Modal */
.modal {
    position: fixed;
    inset: 0;
    z-index: 1000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.modal.show {
    display: flex;
}

.modal-backdrop {
    position: fixed;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

.modal-dialog {
    position: relative;
    width: 100%;
    max-width: 32rem;
    max-height: calc(100vh - 2rem);
    overflow-y: auto;
}

.modal-dialog.modal-sm {
    max-width: 24rem;
}

.modal-content {
    background-color: var(--white);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--gray-light);
}

.modal-close {
    width: 2rem;
    height: 2rem;
    border-radius: var(--border-radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: transparent;
    color: var(--gray);
    border: none;
    cursor: pointer;
    transition: var(--transition-fast);
}

.modal-close:hover {
    background-color: var(--gray-light);
    color: var(--dark);
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.75rem;
    padding: 1.25rem 1.5rem;
    border-top: 1px solid var(--gray-light);
}

/* Form Elements */
.form-group {
    margin-bottom: 1.25rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-hint {
    margin-top: 0.375rem;
    font-size: 0.75rem;
    color: var(--gray);
}

input[type="file"] {
    display: block;
    width: 100%;
    padding: 0.5rem;
    border: 1px solid var(--gray-light);
    border-radius: var(--border-radius);
    background-color: var(--light);
}

.form-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-check input[type="checkbox"] {
    width: 1rem;
    height: 1rem;
}

/* Image Preview */
.image-preview-container {
    margin-top: 1.5rem;
}

.image-preview-container.hidden {
    display: none;
}

.image-preview-container h4 {
    margin-bottom: 1rem;
    font-size: 1rem;
}

.image-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 0.75rem;
}

.preview-item {
    position: relative;
    border-radius: var(--border-radius);
    overflow: hidden;
    aspect-ratio: 1;
}

.preview-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-remove {
    position: absolute;
    top: 0.25rem;
    right: 0.25rem;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: var(--border-radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(255, 255, 255, 0.8);
    color: var(--dark);
    border: none;
    cursor: pointer;
    transition: var(--transition-fast);
}

.preview-remove:hover {
    background-color: var(--danger);
    color: var(--white);
}

/* Utilities */
.hidden {
    display: none;
}

.text-danger {
    color: var(--danger);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal functionality
    const uploadImagesBtn = document.getElementById('uploadImagesBtn');
    const emptyStateUploadBtn = document.getElementById('emptyStateUploadBtn');
    const closeUploadModal = document.getElementById('closeUploadModal');
    const cancelUploadBtn = document.getElementById('cancelUploadBtn');
    const uploadImagesModal = document.getElementById('uploadImagesModal');
    
    const deleteImageModal = document.getElementById('deleteImageModal');
    const closeDeleteModal = document.getElementById('closeDeleteModal');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    
    // Open upload modal
    function openUploadModal() {
        uploadImagesModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    
    // Close upload modal
    function closeUploadModal() {
        uploadImagesModal.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    // Open delete modal
    function openDeleteModal() {
        deleteImageModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    
    // Close delete modal
    function closeDeleteModal() {
        deleteImageModal.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    if (uploadImagesBtn) {
        uploadImagesBtn.addEventListener('click', openUploadModal);
    }
    
    if (emptyStateUploadBtn) {
        emptyStateUploadBtn.addEventListener('click', openUploadModal);
    }
    
    if (closeUploadModal) {
        closeUploadModal.addEventListener('click', closeUploadModal);
    }
    
    if (cancelUploadBtn) {
        cancelUploadBtn.addEventListener('click', closeUploadModal);
    }
    
    if (closeDeleteModal) {
        closeDeleteModal.addEventListener('click', closeDeleteModal);
    }
    
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', closeDeleteModal);
    }
    
    // Close modals when clicking on backdrop
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal-backdrop')) {
            closeUploadModal();
            closeDeleteModal();
        }
    });
    
    // Image upload preview
    const imageInput = document.getElementById('branchImages');
    const previewContainer = document.querySelector('.image-preview-container');
    const previewGrid = document.getElementById('imagePreviewGrid');
    
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            previewGrid.innerHTML = '';
            
            if (this.files.length > 0) {
                previewContainer.classList.remove('hidden');
                
                Array.from(this.files).forEach((file, index) => {
                    if (!file.type.match('image.*')) return;
                    
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const previewItem = document.createElement('div');
                        previewItem.className = 'preview-item';
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = `Preview ${index + 1}`;
                        img.className = 'preview-img';
                        
                        const removeBtn = document.createElement('button');
                        removeBtn.className = 'preview-remove';
                        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                        removeBtn.addEventListener('click', function() {
                            previewItem.remove();
                            
                            // If no previews left, hide the container
                            if (previewGrid.children.length === 0) {
                                previewContainer.classList.add('hidden');
                            }
                        });
                        
                        previewItem.appendChild(img);
                        previewItem.appendChild(removeBtn);
                        previewGrid.appendChild(previewItem);
                    };
                    
                    reader.readAsDataURL(file);
                });
            } else {
                previewContainer.classList.add('hidden');
            }
        });
    }

    // Single image upload handling
    document.getElementById('uploadImagesBtn').addEventListener('click', function() {
        document.getElementById('imageUpload').click();
    });

    document.getElementById('imageUpload').addEventListener('change', function(e) {
        const formData = new FormData(document.getElementById('uploadImageForm'));
        
        fetch("{{ route('admin.branches.upload-image', $branch->id) }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                window.location.reload();
            } else {
                alert('Upload thất bại: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi upload ảnh');
        });
    });
    
    // Delete image functionality
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const deleteForm = document.getElementById('deleteImageForm');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const imageId = this.getAttribute('data-image-id');
            deleteForm.action = `/admin/branch-images/${imageId}`;
            openDeleteModal();
        });
    });
    
    // Set featured image functionality
    const featuredButtons = document.querySelectorAll('.set-featured-btn');
    
    featuredButtons.forEach(button => {
        button.addEventListener('click', function() {
            const imageId = this.getAttribute('data-image-id');
            
            // Send AJAX request to set as featured
            fetch(`/admin/branch-images/${imageId}/set-featured`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    branch_id: {{ $branch->id }}
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to reflect changes
                    window.location.reload();
                } else {
                    alert('Có lỗi xảy ra khi đặt ảnh đại diện');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi đặt ảnh đại diện');
            });
        });
    });
    
    // Quick action items
    const actionItems = document.querySelectorAll('.action-item');
    
    actionItems.forEach(item => {
        item.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            
            // Handle different actions
            switch (action) {
                case 'report':
                    console.log('Opening reports');
                    // Add your report action here
                    break;
                case 'staff':
                    console.log('Managing staff');
                    // Add your staff management action here
                    break;
                case 'schedule':
                    console.log('Viewing schedule');
                    // Add your schedule action here
                    break;
                case 'settings':
                    console.log('Opening settings');
                    // Add your settings action here
                    break;
            }
        });
    });
    
    // Initialize Fancybox for gallery
    if (typeof Fancybox !== 'undefined') {
        Fancybox.bind("[data-fancybox]", {
            // Options here
        });
    }
    
    // Add animation to cards on scroll
    const cards = document.querySelectorAll('.card');
    
    function animateOnScroll() {
        cards.forEach(card => {
            const cardTop = card.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            
            if (cardTop < windowHeight * 0.9) {
                card.classList.add('animate-in');
            }
        });
    }
    
    // Add initial animation class
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    });
    
    // Add animation class on scroll
    window.addEventListener('scroll', animateOnScroll);
    
    // Trigger initial animation
    setTimeout(animateOnScroll, 100);
});
</script>
@endsection