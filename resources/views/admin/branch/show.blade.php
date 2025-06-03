@extends('layouts.admin.contentLayoutMaster')

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
                                    <div class="gallery-overlay">
                                        <a href="{{ Storage::disk('s3')->url($image->image_path) }}" class="gallery-btn view-btn" data-fancybox="branch-gallery" data-caption="{{ $branch->name }} - {{ $image->caption ?? 'Hình ' . ($index + 1) }}"><i class="fas fa-search-plus"></i></a>
                                        <button class="gallery-btn {{ $image->is_featured ? 'featured-btn' : 'set-featured-btn' }}" data-image-id="{{ $image->id }}"><i class="fa{{ $image->is_featured ? 's' : 'r' }} fa-star"></i></button>
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
                                    <a href="{{ route('admin.branches.assign-manager', $branch->id) }}" class="btn btn-outline btn-block btn-sm"><i class="fas fa-exchange-alt"></i> Thay đổi quản lý</a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-user-slash empty-icon"></i>
                            <h4>Chưa phân công quản lý</h4>
                            <p>Chi nhánh này chưa có người quản lý</p>
                            <a href="{{ route('admin.branches.assign-manager', $branch->id) }}" class="btn btn-primary"><i class="fas fa-plus"></i> Phân công quản lý</a>
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
                        <div class="file-upload-wrapper">
                            <input type="file" id="branchImages" name="images[]" multiple accept="image/*" required class="file-upload-input">
                            <div class="file-upload-text">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Kéo thả hoặc click để chọn ảnh</span>
                            </div>
                        </div>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            Hỗ trợ định dạng: JPG, PNG, GIF (Tối đa 5MB/ảnh)
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" id="setAsFeatured" name="set_as_featured" class="form-check-input">
                            <span class="form-check-label">
                                <i class="far fa-star"></i>
                                Đặt ảnh đầu làm ảnh đại diện
                            </span>
                        </label>
                    </div>

                    <div class="image-preview-container hidden">
                        <div class="preview-header">
                            <h4><i class="fas fa-eye"></i> Xem trước</h4>
                            <button type="button" class="btn btn-link btn-sm clear-preview">
                                <i class="fas fa-trash-alt"></i> Xóa tất cả
                            </button>
                        </div>
                        <div class="image-preview-grid" id="imagePreviewGrid"></div>
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

<style>
.branch-details-container {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    max-width: 100%;
    margin: 0 auto;
}

h1 { font-size: 1.875rem; font-weight: 700; line-height: 1.2; }
h2 { font-size: 1.5rem; font-weight: 600; }
h3 { font-size: 1.25rem; font-weight: 600; }
h4 { font-size: 1rem; font-weight: 500; }

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    text-decoration: none;
}

.btn-sm { padding: 0.5rem 1rem; font-size: 0.875rem; }
.btn-primary {
    background: #2563eb;
    color: #ffffff;
    border: none;
}
.btn-primary:hover { background: #1d4ed8; }
.btn-outline {
    border: 1px solid #6b7280;
    color: #6b7280;
    background: transparent;
}
.btn-outline:hover {
    background: #f9fafb;
    border-color: #2563eb;
    color: #2563eb;
}
.btn-danger { background: #ef4444; color: #ffffff; border: none; }
.btn-danger:hover { background: #dc2626; }

.btn-loading {
    position: relative;
    pointer-events: none;
    opacity: 0.7;
}

.btn-loading::after {
    content: '';
    width: 16px;
    height: 16px;
    border: 2px solid #ffffff;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin-left: 8px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.page-header {
    margin-bottom: 2rem;
    background: #ffffff;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}
.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}
.header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.header-icon, .card-icon, .hours-icon, .hours-card-icon, .empty-icon, .action-icon {
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.125rem;
}
.header-icon {
    background: rgba(37, 99, 235, 0.1);
    color: #2563eb;
}
.header-text p {
    color: #6b7280;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}
.header-actions {
    display: flex;
    gap: 0.75rem;
}

.card {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    margin-bottom: 1.5rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.card-header {
    display: flex;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    gap: 0.75rem;
}
.card-body {
    padding: 1.5rem;
}
.card-body.p-0 {
    padding: 0;
}

.branch-banner {
    height: 7rem;
    background: linear-gradient(135deg, #2563eb, #60a5fa);
    position: relative;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}
.status-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.status-badge.active {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
}
.status-badge.inactive {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
}

.branch-overview-content {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    padding: 1.5rem;
    background: #ffffff;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
}
.branch-overview-left {
    display: flex;
    gap: 1.25rem;
    flex: 1;
}
.branch-avatar {
    width: 4.5rem;
    height: 4.5rem;
    background: #2563eb;
    color: #ffffff;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    border: 4px solid #ffffff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}
.branch-address {
    display: flex;
    gap: 0.5rem;
    color: #6b7280;
    margin: 0.75rem 0;
    font-size: 0.875rem;
}
.branch-address i {
    color: #ef4444;
}
.branch-contact {
    display: flex;
    gap: 1.25rem;
    margin-bottom: 1rem;
}
.contact-item {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    font-size: 0.875rem;
}
.contact-item i {
    color: #2563eb;
}
.branch-rating {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}
.stars {
    color: #f59e0b;
    display: flex;
    gap: 0.25rem;
    font-size: 1rem;
}
.rating-value {
    font-weight: 600;
    font-size: 1rem;
}

.hours-container {
    display: flex;
    gap: 1rem;
    background: #f9fafb;
    border-radius: 8px;
    padding: 1rem;
}
.hours-item {
    display: flex;
    gap: 0.75rem;
    flex: 1;
    padding: 0.75rem;
    border-radius: 8px;
}
.hours-item.opening {
    background: rgba(34, 197, 94, 0.1);
}
.hours-item.closing {
    background: rgba(239, 68, 68, 0.1);
}
.hours-label {
    font-size: 0.875rem;
    color: #6b7280;
}
.hours-value {
    font-weight: 600;
    font-size: 1.125rem;
}
.hours-item.opening .hours-value {
    color: #22c55e;
}
.hours-item.closing .hours-value {
    color: #ef4444;
}

.content-grid {
    display: grid;
    gap: 2rem;
}
@media (min-width: 1024px) {
    .content-grid {
        grid-template-columns: 3fr 1fr;
    }
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}
.gallery-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    aspect-ratio: 4/3;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}
.gallery-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.gallery-item:hover .gallery-img {
    transform: scale(1.05);
}
.gallery-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    opacity: 0;
    transition: all 0.2s ease-in-out;
}
.gallery-item:hover .gallery-overlay {
    opacity: 1;
}
.gallery-btn {
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 50%;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}
.gallery-btn:hover {
    transform: scale(1.1);
}
.gallery-btn.view-btn:hover {
    background: #2563eb;
    color: #ffffff;
}
.gallery-btn.featured-btn {
    background: #f59e0b;
    color: #ffffff;
}
.gallery-btn.set-featured-btn:hover {
    background: #f59e0b;
    color: #ffffff;
}
.gallery-btn.delete-btn:hover {
    background: #ef4444;
    color: #ffffff;
}
.gallery-caption {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.75);
    color: #ffffff;
    padding: 0.5rem;
    font-size: 0.875rem;
    text-align: center;
}

.info-table {
    display: flex;
    flex-direction: column;
}
.info-row {
    display: flex;
    border-bottom: 1px solid #e5e7eb;
    align-items: center;
}
.info-label {
    display: flex;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    font-weight: 500;
    flex: 0 0 200px;
    background: #f9fafb;
}
.info-label i {
    color: #2563eb;
}
.info-value {
    padding: 1rem 1.5rem;
    flex: 1;
}
.id-badge {
    padding: 0.375rem 0.75rem;
    background: #f9fafb;
    border-radius: 8px;
    font-size: 0.875rem;
}
.text-muted {
    color: #6b7280;
}
.link-hover:hover {
    color: #2563eb;
    text-decoration: underline;
}
.date-time่วย { display: flex; gap: 0.75rem; }
.date, .time { display: flex; gap: 0.5rem; font-size: 0.875rem; }
.date { background: #f9fafb; border-radius: 9999px; padding: 0.375rem 0.75rem; }
.time { color: #6b7280; }

.hours-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}
@media (min-width: 640px) {
    .hours-grid {
        grid-template-columns: 1fr 1fr;
    }
}
.hours-card {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 8px;
    transition: all 0.2s ease-in-out;
}
.hours-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}
.hours-card-icon {
    font-size: 1.5rem;
}
.opening-hours .hours-card-icon {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
}
.closing-hours .hours-card-icon {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
}
.hours-card-label {
    font-size: 0.875rem;
    color: #6b7280;
}
.hours-card-value {
    font-weight: 600;
    font-size: 1.25rem;
}
.opening-hours .hours-card-value {
    color: #22c55e;
}
.closing-hours .hours-card-value {
    color: #ef4444;
}

.manager-profile {
    text-align: center;
}
.manager-cover {
    height: 5rem;
    background: linear-gradient(135deg, #3b82f6, rgba(59, 130, 246, 0.1));
}
.manager-avatar {
    width: 4rem;
    height: 4rem;
    background: #3b82f6;
    color: #ffffff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: -2rem auto 0.75rem;
    border: 4px solid #ffffff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}
.manager-role {
    display: inline-flex;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    border-radius: 9999px;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}
.action-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.rating-summary {
    text-align: center;
}
.rating-circle {
    width: 5.5rem;
    height: 5.5rem;
    background: #ffffff;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    border: 3px solid rgba(245, 158, 11, 0.2);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}
.rating-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #f59e0b;
}
.rating-max {
    font-size: 0.875rem;
    color: #6b7280;
}
.rating-stars {
    color: #f59e0b;
    font-size: 1.25rem;
    display: flex;
    justify-content: center;
    gap: 0.25rem;
}
.rating-caption {
    color: #6b7280;
    font-size: 0.875rem;
}

.quick-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}
.action-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.25rem;
    border-radius: 8px;
    cursor: pointer;
    background: #f9fafb;
    transition: all 0.2s ease-in-out;
}
.action-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    background: #ffffff;
}
.action-icon {
    font-size: 1.5rem;
    margin-bottom: 0.75rem;
}
.action-item[data-action="report"] .action-icon {
    background: rgba(37, 99, 235, 0.1);
    color: #2563eb;
}
.action-item[data-action="staff"] .action-icon {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}
.action-item[data-action="schedule"] .action-icon {
    background: rgba(34, 197, 94, 0.1);
    color: #22c55e;
}
.action-item[data-action="settings"] .action-icon {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}
.action-item:hover .action-icon {
    background: #2563eb;
    color: #ffffff;
}

.empty-state {
    text-align: center;
    padding: 2rem;
}
.empty-icon {
    font-size: 2rem;
    background: rgba(107, 114, 128, 0.1);
    color: #6b7280;
    padding: 0.75rem;
    border-radius: 50%;
}
.empty-state p {
    color: #6b7280;
    margin-bottom: 1rem;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1050;
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
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1040;
}
.modal-dialog {
    max-width: 32rem;
    max-height: calc(100vh - 2rem);
    overflow-y: auto;
    position: relative;
    z-index: 1051;
}
.modal-content {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}
.modal-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.modal-close {
    width: 1.75rem;
    height: 1.75rem;
    border-radius: 50%;
    background: #f9fafb;
    color: #6b7280;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}
.modal-close:hover {
    background: #2563eb;
    color: #ffffff;
}
.modal-body {
    padding: 1.5rem;
}
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.form-group {
    margin-bottom: 1.5rem;
}
.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.form-hint {
    font-size: 0.75rem;
    color: #6b7280;
    margin-top: 0.5rem;
}
.form-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.form-check-input {
    width: 1rem;
    height: 1rem;
}
.form-check-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.file-upload-wrapper {
    position: relative;
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    padding: 1.5rem;
    text-align: center;
    background: #f9fafb;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}
.file-upload-wrapper:hover {
    border-color: #2563eb;
}
.file-upload-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}
.file-upload-text {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    color: #6b7280;
}
.file-upload-text:hover {
    color: #2563eb;
}
.image-preview-container {
    margin-top: 1.5rem;
}
.image-preview-container.hidden {
    display: none;
}
.image-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 0.75rem;
}
.preview-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    aspect-ratio: 1;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}
.preview-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.preview-remove {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}
.preview-remove:hover {
    background: #ef4444;
    color: #ffffff;
}

.text-danger {
    color: #ef4444;
}
.hidden {
    display: none;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Modal handling
    const toggleModal = (modal, show) => {
        modal.classList.toggle('show', show);
        modal.style.display = show ? 'flex' : 'none';
        document.body.style.overflow = show ? 'hidden' : 'auto';
    };

    const uploadModal = document.getElementById('uploadImagesModal');

    // Open upload modal
    ['uploadImagesBtn', 'emptyStateUploadBtn'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) {
            btn.addEventListener('click', () => toggleModal(uploadModal, true));
        }
    });

    // Close upload modal
    ['closeUploadModal', 'cancelUploadBtn'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) {
            btn.addEventListener('click', () => {
                toggleModal(uploadModal, false);
                document.getElementById('branchImages').value = '';
                document.getElementById('imagePreviewGrid').innerHTML = '';
                document.querySelector('.image-preview-container').classList.add('hidden');
            });
        }
    });

    // Close modals on backdrop click
    window.addEventListener('click', e => {
        if (e.target.classList.contains('modal-backdrop')) {
            toggleModal(uploadModal, false);
            document.getElementById('branchImages').value = '';
            document.getElementById('imagePreviewGrid').innerHTML = '';
            document.querySelector('.image-preview-container').classList.add('hidden');
        }
    });

    // Image upload preview
    const imageInput = document.getElementById('branchImages');
    const previewContainer = document.querySelector('.image-preview-container');
    const previewGrid = document.getElementById('imagePreviewGrid');
    if (imageInput) {
        imageInput.addEventListener('change', () => {
            previewGrid.innerHTML = '';
            const files = imageInput.files;
            if (files.length) {
                previewContainer.classList.remove('hidden');
                Array.from(files).forEach((file, idx) => {
                    if (!file.type.match('image.*')) return;
                    const reader = new FileReader();
                    reader.onload = e => {
                        const item = document.createElement('div');
                        item.className = 'preview-item';
                        item.innerHTML = `<img src="${e.target.result}" alt="Preview" class="preview-img"><button class="preview-remove"><i class="fas fa-times"></i></button>`;
                        const removeBtn = item.querySelector('.preview-remove');
                        removeBtn.addEventListener('click', () => {
                            item.remove();
                            const dt = new DataTransfer();
                            Array.from(files).forEach((f, i) => {
                                if (i !== idx) dt.items.add(f);
                            });
                            imageInput.files = dt.files;
                            if (!previewGrid.children.length) {
                                previewContainer.classList.add('hidden');
                            }
                        });
                        previewGrid.appendChild(item);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                previewContainer.classList.add('hidden');
            }
        });
    }

    // Clear preview button
    const clearPreviewBtn = document.querySelector('.clear-preview');
    if (clearPreviewBtn) {
        clearPreviewBtn.addEventListener('click', () => {
            previewGrid.innerHTML = '';
            previewContainer.classList.add('hidden');
            imageInput.value = '';
        });
    }

    // Delete image with AJAX
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const imageId = btn.dataset.imageId;
            const branchId = btn.dataset.branchId;
            dtmodalConfirmDelete({
                itemName: 'hình ảnh',
                onConfirm: () => {
                    btn.classList.add('btn-loading');
                    fetch(`/admin/branches/${branchId}/images/${imageId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(res => {
                        if (res.headers.get('content-type')?.includes('application/json')) {
                            return res.json();
                        } else {
                            throw new Error('Server did not return JSON.');
                        }
                    })
                    .then(data => {
                        btn.classList.remove('btn-loading');
                        if (data.success) {
                            const galleryItem = document.querySelector(`.gallery-item[data-image-id="${imageId}"]`);
                            if (galleryItem) {
                                galleryItem.style.animation = 'fadeOut 0.3s ease forwards';
                                setTimeout(() => galleryItem.remove(), 300);
                            }
                            const galleryGrid = document.querySelector('.gallery-grid');
                            if (galleryGrid && !galleryGrid.children.length) {
                                const cardBody = galleryGrid.closest('.card-body');
                                cardBody.innerHTML = `
                                    <div class="empty-state" id="emptyState">
                                        <i class="fas fa-images empty-icon"></i>
                                        <h4>Chưa có hình ảnh</h4>
                                        <p>Chi nhánh này chưa có hình ảnh nào</p>
                                        <button class="btn btn-primary" id="emptyStateUploadBtn"><i class="fas fa-upload"></i> Tải lên hình ảnh</button>
                                    </div>`;
                                const newUploadBtn = document.getElementById('emptyStateUploadBtn');
                                if (newUploadBtn) {
                                    newUploadBtn.addEventListener('click', () => toggleModal(uploadModal, true));
                                }
                            }
                            dtmodalShowToast('success', { message: 'Xóa hình ảnh thành công' });
                        } else {
                            dtmodalShowToast('error', { message: 'Có lỗi khi xóa hình ảnh: ' + (data.message || 'Unknown error') });
                        }
                    })
                    .catch(err => {
                        btn.classList.remove('btn-loading');
                        dtmodalShowToast('error', { message: 'Có lỗi khi xóa hình ảnh: ' + err.message });
                    });
                }
            });
        });
    });

    // Set featured image
    document.querySelectorAll('.set-featured-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.classList.add('btn-loading');
            fetch(`/admin/branch/branches-images/${btn.dataset.imageId}/set-featured`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ branch_id: {{ $branch->id }} })
            })
            .then(res => res.json())
            .then(data => {
                btn.classList.remove('btn-loading');
                if (data.success) {
                    location.reload();
                } else {
                    dtmodalShowToast('error', { message: 'Có lỗi khi đặt ảnh đại diện: ' + (data.message || 'Unknown error') });
                }
            })
            .catch(err => {
                btn.classList.remove('btn-loading');
                dtmodalShowToast('error', { message: 'Có lỗi khi đặt ảnh đại diện: ' + err.message });
            });
        });
    });

    // Quick click actions with feedback
    document.querySelectorAll('.action-item').forEach(item => {
        item.addEventListener('click', () => {
            item.style.animation = 'pulse 0.2s ease';
            setTimeout(() => item.style.animation = '', 200);
            console.log(`Action clicked: ${item.dataset.action}`);
        });
    });

    // Card animations
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    });
    const animateCards = () => {
        cards.forEach(card => {
            if (card.getBoundingClientRect().top < window.innerHeight * 0.85) {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }
        });
    };
    window.addEventListener('scroll', animateCards);
    setTimeout(animateCards, 100);

    // Fancybox bind
    if (typeof Fancybox !== 'undefined') {
        Fancybox.bind("[data-fancybox]", {
            Thumbs: { autoStart: false }
        });
    }
});

// Animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; transform: scale(0.95); }
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
`;
document.head.appendChild(style);
</script>
@endsection