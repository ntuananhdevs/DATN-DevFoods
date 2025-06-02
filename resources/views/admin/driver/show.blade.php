@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Chi tiết đơn đăng ký tài xế')

@section('content')
<div class="data-table-wrapper">
    <!-- Header -->
    <div class="data-table-main-header">
        <div class="data-table-brand">
            <div class="data-table-logo">
                <i class="fas fa-user-check"></i>
            </div>
            <h1 class="data-table-title">Chi tiết đơn đăng ký tài xế #{{ $application->id }}</h1>
        </div>
        <div class="data-table-header-actions">
            <a href="{{ route('admin.drivers.applications.index') }}" class="data-table-btn data-table-btn-outline">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- Status Update Form -->
    @if($application->status === 'pending')
    <div class="data-table-card mb-4">
        <div class="data-table-header">
            <h2 class="data-table-card-title">Cập nhật trạng thái</h2>
        </div>
        <div class="p-4">
            <form action="{{ route('admin.drivers.applications.update-status', $application) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Trạng thái:</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="approved">Duyệt</option>
                                <option value="rejected">Từ chối</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="notes">Ghi chú:</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Ghi chú về quyết định..."></textarea>
                        </div>
                    </div>
                </div>
                <button type="submit" class="data-table-btn data-table-btn-primary">
                    <i class="fas fa-save"></i> Cập nhật
                </button>
            </form>
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Thông tin cá nhân -->
        <div class="col-md-6">
            <div class="data-table-card">
                <div class="data-table-header">
                    <h2 class="data-table-card-title">Thông tin cá nhân</h2>
                </div>
                <div class="p-4">
                    <div class="text-center mb-4">
                        @if($imageUrls['profile_image'])
                            <img src="{{ $imageUrls['profile_image'] }}" alt="Ảnh chân dung" class="profile-image mb-3" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #007bff;">
                        @else
                            <div class="profile-placeholder mb-3" style="width: 120px; height: 120px; border-radius: 50%; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 3px solid #dee2e6;">
                                <i class="fas fa-user fa-3x text-muted"></i>
                            </div>
                        @endif
                        <h4>{{ $application->full_name }}</h4>
                        <span class="data-table-status 
                            @if($application->status === 'approved') data-table-status-success
                            @elseif($application->status === 'rejected') data-table-status-failed
                            @else data-table-status-warning @endif">
                            @if($application->status === 'approved') 
                                <i class="fas fa-check"></i> Đã duyệt
                            @elseif($application->status === 'rejected') 
                                <i class="fas fa-times"></i> Đã từ chối
                            @else 
                                <i class="fas fa-clock"></i> Chờ xử lý
                            @endif
                        </span>
@section('title', 'Chi tiết tài xế')
@section('description', 'Xem thông tin chi tiết tài xế')

@section('content')
<style>
    .profile-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        color: white;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid rgba(255, 255, 255, 0.3);
        object-fit: cover;
    }
    
    .info-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
    }
    
    .info-card h5 {
        color: #374151;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #3b82f6;
        display: inline-block;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        border-radius: 10px;
        padding: 1.5rem;
        text-align: center;
        transition: transform 0.2s ease;
        height: 100%;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        font-size: 0.875rem;
        opacity: 0.9;
    }
    
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 500;
        font-size: 0.875rem;
    }
    
    .status-active {
        background-color: #dcfce7;
        color: #15803d;
    }
    
    .status-inactive {
        background-color: #fee2e2;
        color: #b91c1c;
    }
    
    .status-locked {
        background-color: #fef3c7;
        color: #d97706;
    }
    
    .available-badge {
        background-color: #d1fae5;
        color: #059669;
    }
    
    .unavailable-badge {
        background-color: #fef3c7;
        color: #d97706;
    }
    
    .info-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .info-table th {
        background-color: #f9fafb;
        padding: 0.75rem;
        font-weight: 500;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
        width: 30%;
    }
    
    .info-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e5e7eb;
        color: #6b7280;
    }
    
    .document-image {
        max-width: 200px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    
    .document-image:hover {
        transform: scale(1.05);
    }
    
    .rating-stars {
        color: #fbbf24;
        margin-left: 0.5rem;
    }
    
    .back-btn {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    
    .back-btn:hover {
        background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }
    
    .action-btn {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0.25rem;
        border: none;
        cursor: pointer;
    }
    
    .btn-edit {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
    }
    
    .btn-edit:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }
    
    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    
    .btn-danger:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        color: white;
        transform: translateY(-1px);
    }
    
    .btn-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    
    .btn-warning:hover {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        color: white;
        transform: translateY(-1px);
    }
    
    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .btn-success:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
        transform: translateY(-1px);
    }
    
    .btn-secondary {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        color: white;
    }
    
    .btn-secondary:hover {
        background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
        color: white;
        transform: translateY(-1px);
    }
    
    .timeline {
        position: relative;
        padding-left: 2rem;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 0.5rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e5e7eb;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
        background: white;
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -1.75rem;
        top: 1.25rem;
        width: 0.75rem;
        height: 0.75rem;
        background: #3b82f6;
        border-radius: 50%;
        border: 2px solid white;
    }
    
    .timeline-date {
        font-size: 0.75rem;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }
    
    .timeline-title {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.25rem;
    }
    
    .timeline-content {
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        border-radius: 0;
        color: #6b7280;
        padding: 0.75rem 1rem;
    }
    
    .nav-tabs .nav-link.active {
        border-bottom-color: #3b82f6;
        color: #3b82f6;
        background: none;
    }
    
    .tab-content {
        padding: 1.5rem 0;
    }
    
    .modal-header {
        border-bottom: 1px solid #e5e7eb;
    }
    
    .modal-footer {
        border-top: 1px solid #e5e7eb;
    }
    
    .violation-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .violation-low {
        background-color: #fef3c7;
        color: #d97706;
    }
    
    .violation-medium {
        background-color: #fed7d7;
        color: #d69e2e;
    }
    
    .violation-high {
        background-color: #fee2e2;
        color: #e53e3e;
    }
    
    .violation-critical {
        background-color: #fed7d7;
        color: #b91c1c;
    }
    
    .order-status {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .order-status.completed {
        background-color: #dcfce7;
        color: #15803d;
    }
    
    .order-status.cancelled {
        background-color: #fee2e2;
        color: #b91c1c;
    }
    
    .order-status.pending {
        background-color: #fef3c7;
        color: #d97706;
    }
    
    .order-status.in_delivery {
        background-color: #ddd6fe;
        color: #7c3aed;
    }
    
    .quick-action-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .quick-action-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease;
        cursor: pointer;
    }
    
    .quick-action-card:hover {
        transform: translateY(-2px);
    }
    
    .quick-action-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.5rem;
    }
    
    .progress-bar-custom {
        height: 8px;
        border-radius: 4px;
        background: #e5e7eb;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.3s ease;
    }
</style>

<div class="fade-in">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Chi tiết tài xế</h1>
            <p class="text-muted">Thông tin chi tiết về tài xế #{{ $driver->id }}</p>
        </div>
        <a href="{{ route('admin.drivers.index') }}" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>

    <!-- Profile Card -->
    <div class="profile-card">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($driver->full_name) }}&background=ffffff&color=667eea&size=120" 
                     class="profile-avatar" alt="Avatar">
            </div>
            <div class="col-md-6">
                <h2 class="mb-2">{{ $driver->full_name }}</h2>
                <p class="mb-1"><i class="fas fa-envelope me-2"></i>{{ $driver->email }}</p>
                <p class="mb-1"><i class="fas fa-phone me-2"></i>{{ $driver->phone_number ?? 'Chưa cập nhật' }}</p>
                <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i>{{ $driver->address ?? 'Chưa cập nhật' }}</p>
                <p class="mb-0">
                    <i class="fas fa-star rating-stars"></i>
                    <span class="ms-1">{{ number_format($driver->rating, 1) }}/5.0</span>
                    <span class="ms-2 text-sm opacity-75">({{ $stats['total_orders'] }} đơn hàng)</span>
                </p>
            </div>
            <div class="col-md-3 text-center">
                <div class="mb-2">
                    <span class="status-badge {{ $driver->status === 'active' ? 'status-active' : ($driver->status === 'locked' ? 'status-locked' : 'status-inactive') }}">
                        <i class="fas fa-{{ $driver->status === 'active' ? 'check-circle' : ($driver->status === 'locked' ? 'lock' : 'times-circle') }} me-1"></i>
                        {{ $driver->status === 'active' ? 'Đang hoạt động' : ($driver->status === 'locked' ? 'Bị khóa' : 'Không hoạt động') }}
                    </span>
                </div>
                <div class="mb-2">
                    <span class="status-badge {{ $driver->is_available ? 'available-badge' : 'unavailable-badge' }}">
                        <i class="fas fa-{{ $driver->is_available ? 'check' : 'pause' }} me-1"></i>
                        {{ $driver->is_available ? 'Sẵn sàng' : 'Bận' }}
                    </span>
                </div>
                <div class="small text-muted">
                    Điểm tin cậy: {{ $driver->reliability_score }}/100
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-action-cards">
        <div class="quick-action-card" onclick="showEditModal()">
            <div class="quick-action-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white;">
                <i class="fas fa-edit"></i>
            </div>
            <h6>Chỉnh sửa</h6>
            <p class="text-muted small mb-0">Cập nhật thông tin</p>
        </div>
        
        <div class="quick-action-card" onclick="showStatusModal()">
            <div class="quick-action-icon" style="background: linear-gradient(135deg, {{ $driver->status === 'active' ? '#ef4444, #dc2626' : '#10b981, #059669' }} ); color: white;">
                <i class="fas fa-{{ $driver->status === 'active' ? 'pause' : 'play' }}"></i>
            </div>
            <h6>{{ $driver->status === 'active' ? 'Vô hiệu hóa' : 'Kích hoạt' }}</h6>
            <p class="text-muted small mb-0">Thay đổi trạng thái</p>
        </div>
        
        <div class="quick-action-card" onclick="showLockModal()">
            <div class="quick-action-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                <i class="fas fa-{{ $driver->status === 'locked' ? 'unlock' : 'lock' }}"></i>
            </div>
            <h6>{{ $driver->status === 'locked' ? 'Mở khóa' : 'Khóa tài khoản' }}</h6>
            <p class="text-muted small mb-0">Khóa tạm thời</p>
        </div>
        
        <div class="quick-action-card" onclick="showResetPasswordModal()">
            <div class="quick-action-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white;">
                <i class="fas fa-key"></i>
            </div>
            <h6>Reset mật khẩu</h6>
            <p class="text-muted small mb-0">Đặt lại mật khẩu</p>
        </div>
        
        <div class="quick-action-card" onclick="showViolationModal()">
            <div class="quick-action-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h6>Thêm vi phạm</h6>
            <p class="text-muted small mb-0">Ghi nhận vi phạm</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_orders'] }}</div>
                <div class="stat-label">Tổng đơn hàng</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="stat-number">{{ $stats['completed_orders'] }}</div>
                <div class="stat-label">Hoàn thành</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                <div class="stat-number">{{ $stats['cancelled_orders'] }}</div>
                <div class="stat-label">Đã hủy</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="stat-number">{{ number_format($stats['total_earnings']) }}đ</div>
                <div class="stat-label">Tổng thu nhập</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                <div class="stat-number">{{ $stats['total_violations'] }}</div>
                <div class="stat-label">Vi phạm</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
                <div class="stat-number">{{ $driver->reliability_score }}/100</div>
                <div class="stat-label">Điểm tin cậy</div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs" id="driverDetailTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button">
                <i class="fas fa-user me-2"></i>Tổng quan
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button">
                <i class="fas fa-file-alt me-2"></i>Giấy tờ
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button">
                <i class="fas fa-chart-line me-2"></i>Hoạt động
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button">
                <i class="fas fa-history me-2"></i>Lịch sử
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="violations-tab" data-bs-toggle="tab" data-bs-target="#violations" type="button">
                <i class="fas fa-exclamation-triangle me-2"></i>Vi phạm
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="driverDetailTabsContent">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-card">
                        <h5><i class="fas fa-user me-2"></i>Thông tin cá nhân</h5>
                        <table class="info-table">
                            <tr>
                                <th>Họ và tên</th>
                                <td>{{ $driver->full_name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $driver->email }}</td>
                            </tr>
                            <tr>
                                <th>Số điện thoại</th>
                                <td>{{ $driver->phone_number ?? 'Chưa cập nhật' }}</td>
                            </tr>
                            <tr>
                                <th>Địa chỉ</th>
                                <td>{{ $driver->address ?? 'Chưa cập nhật' }}</td>
                            </tr>
                            <tr>
                                <th>Ngày tham gia</th>
                                <td>{{ $driver->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Cập nhật cuối</th>
                                <td>{{ $driver->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="info-card">
                        <h5><i class="fas fa-car me-2"></i>Thông tin phương tiện</h5>
                        <table class="info-table">
                            <tr>
                                <th>Loại xe</th>
                                <td>{{ $driver->vehicle_type ?? 'Chưa cập nhật' }}</td>
                            </tr>
                            <tr>
                                <th>Màu xe</th>
                                <td>{{ $driver->vehicle_color ?? 'Chưa cập nhật' }}</td>
                            </tr>
                            <tr>
                                <th>Biển số</th>
                                <td>{{ $driver->license_plate ?? 'Chưa cập nhật' }}</td>
                            </tr>
                            <tr>
                                <th>Đăng ký xe</th>
                                <td>{{ $driver->vehicle_registration ?? 'Chưa cập nhật' }}</td>
                            </tr>
                            <tr>
                                <th>Số bằng lái</th>
                                <td>{{ $driver->license_number ?? 'Chưa cập nhật' }}</td>
                            </tr>
                            <tr>
                                <th>Hạng bằng lái</th>
                                <td>{{ $driver->license_class ?? 'Chưa cập nhật' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="info-card">
                        <h5><i class="fas fa-chart-bar me-2"></i>Thống kê hoạt động</h5>
                        <table class="info-table">
                            <tr>
                                <th>Đánh giá trung bình</th>
                                <td>
                                    {{ number_format($driver->rating, 1) }}/5.0
                                    <div class="progress-bar-custom mt-1">
                                        <div class="progress-fill" style="width: {{ ($driver->rating / 5) * 100 }}%; background: linear-gradient(90deg, #fbbf24, #f59e0b);"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Điểm tin cậy</th>
                                <td>
                                    {{ $driver->reliability_score }}/100
                                    <div class="progress-bar-custom mt-1">
                                        <div class="progress-fill" style="width: {{ $driver->reliability_score }}%; background: linear-gradient(90deg, #10b981, #059669);"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Số đơn đã hủy</th>
                                <td>{{ $driver->cancellation_count ?? 0 }}</td>
                            </tr>
                            <tr>
                                <th>Số lần vi phạm</th>
                                <td>{{ $driver->penalty_count ?? 0 }}</td>
                            </tr>
                            <tr>
                                <th>Số dư tài khoản</th>
                                <td>{{ number_format($driver->balance ?? 0) }}đ</td>
                            </tr>
                            <tr>
                                <th>Tự động nạp tiền</th>
                                <td>{{ $driver->auto_deposit_earnings ? 'Có' : 'Không' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="info-card">
                        <h5><i class="fas fa-shield-alt me-2"></i>Trạng thái tài khoản</h5>
                        <table class="info-table">
                            <tr>
                                <th>Trạng thái</th>
                                <td>
                                    <span class="status-badge {{ $driver->status === 'active' ? 'status-active' : ($driver->status === 'locked' ? 'status-locked' : 'status-inactive') }}">
                                        {{ $driver->status === 'active' ? 'Đang hoạt động' : ($driver->status === 'locked' ? 'Bị khóa' : 'Không hoạt động') }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Sẵn sàng nhận đơn</th>
                                <td>
                                    <span class="status-badge {{ $driver->is_available ? 'available-badge' : 'unavailable-badge' }}">
                                        {{ $driver->is_available ? 'Sẵn sàng' : 'Bận' }}
                                    </span>
                                </td>
                            </tr>
                            @if($driver->locked_at)
                            <tr>
                                <th>Thời gian khóa</th>
                                <td>{{ $driver->locked_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endif
                            @if($driver->locked_until)
                            <tr>
                                <th>Khóa đến</th>
                                <td>{{ $driver->locked_until->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endif
                            @if($driver->lock_reason)
                            <tr>
                                <th>Lý do khóa</th>
                                <td>{{ $driver->lock_reason }}</td>
                            </tr>
                            @endif
                            @if($driver->password_reset_at)
                            <tr>
                                <th>Reset mật khẩu lần cuối</th>
                                <td>{{ $driver->password_reset_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            
            @if($driver->admin_notes)
            <div class="info-card">
                <h5><i class="fas fa-sticky-note me-2"></i>Ghi chú của admin</h5>
                <p class="mb-0">{{ $driver->admin_notes }}</p>
            </div>
            @endif
        </div>

                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $application->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Số điện thoại:</strong></td>
                            <td>{{ $application->phone_number }}</td>
                        </tr>
                        <tr>
                            <td><strong>Ngày sinh:</strong></td>
                            <td>{{ Carbon\Carbon::parse($application->date_of_birth)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Giới tính:</strong></td>
                            <td>
                                @if($application->gender === 'male') Nam
                                @elseif($application->gender === 'female') Nữ
                                @else Khác @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Địa chỉ:</strong></td>
                            <td>{{ $application->address }}</td>
                        </tr>
                        <tr>
                            <td><strong>Thành phố:</strong></td>
                            <td>{{ $application->city }}</td>
                        </tr>
                        <tr>
                            <td><strong>Quận/Huyện:</strong></td>
                            <td>{{ $application->district }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Thông tin CMND/CCCD -->
        <div class="col-md-6">
            <div class="data-table-card">
                <div class="data-table-header">
                    <h2 class="data-table-card-title">Thông tin CMND/CCCD</h2>
                </div>
                <div class="p-4">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Số CMND/CCCD:</strong></td>
                            <td>{{ $application->id_card_number }}</td>
                        </tr>
                        <tr>
                            <td><strong>Ngày cấp:</strong></td>
                            <td>{{ Carbon\Carbon::parse($application->id_card_issue_date)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Nơi cấp:</strong></td>
                            <td>{{ $application->id_card_issue_place }}</td>
                        </tr>
                    </table>

                    <div class="row mt-3">
                        <div class="col-6">
                            <h6>Mặt trước CMND/CCCD:</h6>
                            @if($imageUrls['id_card_front_image'])
                                <img src="{{ $imageUrls['id_card_front_image'] }}" alt="CMND/CCCD mặt trước" class="img-thumbnail document-image" data-toggle="modal" data-target="#imageModal" data-image-src="{{ $imageUrls['id_card_front_image'] }}" data-image-title="CMND/CCCD mặt trước">
                            @else
                                <div class="document-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>Không có ảnh</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-6">
                            <h6>Mặt sau CMND/CCCD:</h6>
                            @if($imageUrls['id_card_back_image'])
                                <img src="{{ $imageUrls['id_card_back_image'] }}" alt="CMND/CCCD mặt sau" class="img-thumbnail document-image" data-toggle="modal" data-target="#imageModal" data-image-src="{{ $imageUrls['id_card_back_image'] }}" data-image-title="CMND/CCCD mặt sau">
                            @else
                                <div class="document-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>Không có ảnh</p>
                                </div>
                            @endif
        <!-- Documents Tab -->
        <div class="tab-pane fade" id="documents" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-card">
                        <h5><i class="fas fa-id-card me-2"></i>Bằng lái xe</h5>
                        <table class="info-table">
                            <tr>
                                <th>Số bằng lái</th>
                                <td>{{ $driver->license_number ?? 'Chưa cập nhật' }}</td>
                            </tr>
                            <tr>
                                <th>Hạng bằng lái</th>
                                <td>{{ $driver->license_class ?? 'Chưa cập nhật' }}</td>
                            </tr>
                            <tr>
                                <th>Ngày hết hạn</th>
                                <td>
                                    @if($driver->license_expiry)
                                        {{ \Carbon\Carbon::parse($driver->license_expiry)->format('d/m/Y') }}
                                        @if(\Carbon\Carbon::parse($driver->license_expiry)->isPast())
                                            <span class="badge bg-danger ms-2">Đã hết hạn</span>
                                        @elseif(\Carbon\Carbon::parse($driver->license_expiry)->diffInDays() <= 30)
                                            <span class="badge bg-warning ms-2">Sắp hết hạn</span>
                                        @endif
                                    @else
                                        Chưa cập nhật
                                    @endif
                                </td>
                            </tr>
                        </table>
                        
                        @if($driver->license_image)
                        <div class="mt-3">
                            <h6>Hình ảnh bằng lái:</h6>
                            <img src="{{ $driver->license_image }}" class="document-image" alt="Bằng lái xe" onclick="showImageModal(this.src)">
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="info-card">
                        <h5><i class="fas fa-car-alt me-2"></i>Đăng ký xe</h5>
                        <table class="info-table">
                            <tr>
                                <th>Số đăng ký</th>
                                <td>{{ $driver->vehicle_registration ?? 'Chưa cập nhật' }}</td>
                            </tr>
                            <tr>
                                <th>Biển số xe</th>
                                <td>{{ $driver->license_plate ?? 'Chưa cập nhật' }}</td>
                            </tr>
                            <tr>
                                <th>Loại xe</th>
                                <td>{{ $driver->vehicle_type ?? 'Chưa cập nhật' }}</td>
                            </tr>
                            <tr>
                                <th>Màu xe</th>
                                <td>{{ $driver->vehicle_color ?? 'Chưa cập nhật' }}</td>
                            </tr>
                        </table>
                        
                        @if($driver->vehicle_registration_image)
                        <div class="mt-3">
                            <h6>Hình ảnh đăng ký xe:</h6>
                            <img src="{{ $driver->vehicle_registration_image }}" class="document-image" alt="Đăng ký xe" onclick="showImageModal(this.src)">
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="info-card">
                        <h5><i class="fas fa-id-card-alt me-2"></i>CMND/CCCD</h5>
                        @if($driver->identity_card_number)
                        <table class="info-table">
                            <tr>
                                <th>Số CMND/CCCD</th>
                                <td>{{ $driver->identity_card_number }}</td>
                            </tr>
                            <tr>
                                <th>Ngày cấp</th>
                                <td>{{ $driver->identity_card_date ? \Carbon\Carbon::parse($driver->identity_card_date)->format('d/m/Y') : 'Chưa cập nhật' }}</td>
                            </tr>
                            <tr>
                                <th>Nơi cấp</th>
                                <td>{{ $driver->identity_card_place ?? 'Chưa cập nhật' }}</td>
                            </tr>
                        </table>
                        @else
                        <p class="text-muted">Chưa cập nhật thông tin CMND/CCCD</p>
                        @endif
                        
                        @if($driver->identity_card_front_image || $driver->identity_card_back_image)
                        <div class="mt-3">
                            <h6>Hình ảnh CMND/CCCD:</h6>
                            <div class="row">
                                @if($driver->identity_card_front_image)
                                <div class="col-6">
                                    <p class="small text-muted">Mặt trước:</p>
                                    <img src="{{ $driver->identity_card_front_image }}" class="document-image w-100" alt="CMND mặt trước" onclick="showImageModal(this.src)">
                                </div>
                                @endif
                                @if($driver->identity_card_back_image)
                                <div class="col-6">
                                    <p class="small text-muted">Mặt sau:</p>
                                    <img src="{{ $driver->identity_card_back_image }}" class="document-image w-100" alt="CMND mặt sau" onclick="showImageModal(this.src)">
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="info-card">
                        <h5><i class="fas fa-camera me-2"></i>Ảnh chân dung</h5>
                        @if($driver->profile_image)
                        <div class="text-center">
                            <img src="{{ $driver->profile_image }}" class="document-image" alt="Ảnh chân dung" onclick="showImageModal(this.src)" style="max-width: 250px;">
                        </div>
                        @else
                        <p class="text-muted">Chưa có ảnh chân dung</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Tab -->
        <div class="tab-pane fade" id="activity" role="tabpanel">
            <div class="row">
                <div class="col-md-8">
                    <div class="info-card">
                        <h5><i class="fas fa-shopping-bag me-2"></i>Đơn hàng gần đây</h5>
                        @if($stats['recent_orders']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Khách hàng</th>
                                        <th>Trạng thái</th>
                                        <th>Thu nhập</th>
                                        <th>Thời gian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['recent_orders'] as $order)
                                    <tr>
                                        <td><strong>#{{ $order->id }}</strong></td>
                                        <td>{{ $order->customer_name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="order-status {{ $order->status }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($order->driver_earning ?? 0) }}đ</td>
                                        <td>{{ $order->created_at->format('d/m H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-muted">Chưa có đơn hàng nào</p>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="info-card">
                        <h5><i class="fas fa-chart-pie me-2"></i>Thống kê đơn hàng</h5>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Hoàn thành:</span>
                                <strong>{{ $stats['completed_orders'] }}</strong>
                            </div>
                            <div class="progress-bar-custom">
                                <div class="progress-fill" style="width: {{ $stats['total_orders'] > 0 ? ($stats['completed_orders'] / $stats['total_orders']) * 100 : 0 }}%; background: linear-gradient(90deg, #10b981, #059669);"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Đã hủy:</span>
                                <strong>{{ $stats['cancelled_orders'] }}</strong>
                            </div>
                            <div class="progress-bar-custom">
                                <div class="progress-fill" style="width: {{ $stats['total_orders'] > 0 ? ($stats['cancelled_orders'] / $stats['total_orders']) * 100 : 0 }}%; background: linear-gradient(90deg, #ef4444, #dc2626);"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Tỷ lệ hoàn thành:</span>
                                <strong>{{ $stats['total_orders'] > 0 ? number_format(($stats['completed_orders'] / $stats['total_orders']) * 100, 1) : 0 }}%</strong>
                            </div>
                        </div>
                        
                        <div class="mb-0">
                            <div class="d-flex justify-content-between">
                                <span>Tổng thu nhập:</span>
                                <strong class="text-success">{{ number_format($stats['total_earnings']) }}đ</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Tab -->
        <div class="tab-pane fade" id="history" role="tabpanel">
            <div class="info-card">
                <h5><i class="fas fa-cogs me-2"></i>Lịch sử thay đổi tài khoản</h5>
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-date">{{ $driver->created_at->format('d/m/Y H:i:s') }}</div>
                        <div class="timeline-title">Tài khoản được tạo</div>
                        <div class="timeline-content">Tài khoản tài xế được tạo trong hệ thống</div>
                    </div>
                    
                    @if($driver->password_reset_at)
                    <div class="timeline-item">
                        <div class="timeline-date">{{ $driver->password_reset_at->format('d/m/Y H:i:s') }}</div>
                        <div class="timeline-title">Reset mật khẩu</div>
                        <div class="timeline-content">Mật khẩu được reset bởi admin</div>
                    </div>
                    @endif
                    
                    @if($driver->locked_at)
                    <div class="timeline-item">
                        <div class="timeline-date">{{ $driver->locked_at->format('d/m/Y H:i:s') }}</div>
                        <div class="timeline-title">Tài khoản bị khóa</div>
                        <div class="timeline-content">{{ $driver->lock_reason ?? 'Không có lý do cụ thể' }}</div>
                    </div>
                    @endif
                    
                    <div class="timeline-item">
                        <div class="timeline-date">{{ $driver->updated_at->format('d/m/Y H:i:s') }}</div>
                        <div class="timeline-title">Cập nhật thông tin</div>
                        <div class="timeline-content">Thông tin tài xế được cập nhật lần cuối</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Violations Tab -->
        <div class="tab-pane fade" id="violations" role="tabpanel">
            <div class="info-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Lịch sử vi phạm</h5>
                    <button type="button" class="btn btn-sm btn-danger" onclick="showViolationModal()">
                        <i class="fas fa-plus me-1"></i> Thêm vi phạm
                    </button>
                </div>
                
                @if($stats['recent_violations']->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Loại vi phạm</th>
                                <th>Mô tả</th>
                                <th>Mức độ</th>
                                <th>Phạt tiền</th>
                                <th>Người báo cáo</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['recent_violations'] as $violation)
                            <tr>
                                <td><strong>{{ $violation->violation_type }}</strong></td>
                                <td>{{ Str::limit($violation->description, 50) }}</td>
                                <td>
                                    <span class="violation-badge violation-{{ $violation->severity }}">
                                        {{ ucfirst($violation->severity) }}
                                    </span>
                                </td>
                                <td>{{ $violation->penalty_amount ? number_format($violation->penalty_amount) . 'đ' : '-' }}</td>
                                <td>{{ $violation->reporter->name ?? 'Hệ thống' }}</td>
                                <td>{{ $violation->reported_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-shield-check text-success" style="font-size: 3rem;"></i>
                    <h6 class="mt-3">Không có vi phạm nào</h6>
                    <p class="text-muted">Tài xế này chưa có lịch sử vi phạm</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Admin Actions -->
    <div class="info-card">
        <h5><i class="fas fa-cog me-2"></i>Thao tác quản trị</h5>
        <div class="text-center">
            <a href="{{ route('admin.drivers.edit', $driver) }}" class="action-btn btn-edit">
                <i class="fas fa-edit"></i> Chỉnh sửa
            </a>
            
            @if($driver->status === 'active')
                <button type="button" class="action-btn btn-warning" onclick="toggleDriverStatus('inactive')">
                    <i class="fas fa-pause"></i> Vô hiệu hóa
                </button>
            @else
                <button type="button" class="action-btn btn-success" onclick="toggleDriverStatus('active')">
                    <i class="fas fa-play"></i> Kích hoạt
                </button>
            @endif
            
            @if($driver->status !== 'locked')
                <button type="button" class="action-btn btn-warning" onclick="lockDriver()">
                    <i class="fas fa-lock"></i> Khóa tạm thời
                </button>
            @else
                <button type="button" class="action-btn btn-success" onclick="unlockDriver()">
                    <i class="fas fa-unlock"></i> Mở khóa
                </button>
            @endif
            
            <button type="button" class="action-btn btn-secondary" onclick="resetPassword()">
                <i class="fas fa-key"></i> Reset mật khẩu
            </button>
            
            <button type="button" class="action-btn btn-danger" onclick="confirmDelete()">
                <i class="fas fa-trash"></i> Xóa tài xế
            </button>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Thông tin phương tiện -->
        <div class="col-md-6">
            <div class="data-table-card">
                <div class="data-table-header">
                    <h2 class="data-table-card-title">Thông tin phương tiện</h2>
                </div>
                <div class="p-4">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Loại phương tiện:</strong></td>
                            <td>
                                @if($application->vehicle_type === 'motorcycle') Xe máy
                                @elseif($application->vehicle_type === 'car') Ô tô
                                @else Xe đạp @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Dòng xe:</strong></td>
                            <td>{{ $application->vehicle_model }}</td>
                        </tr>
                        <tr>
                            <td><strong>Màu xe:</strong></td>
                            <td>{{ $application->vehicle_color }}</td>
                        </tr>
                        <tr>
                            <td><strong>Biển số xe:</strong></td>
                            <td><strong class="text-primary">{{ $application->license_plate }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Số GPLX:</strong></td>
                            <td>{{ $application->driver_license_number }}</td>
                        </tr>
                    </table>

                    <div class="row mt-3">
                        <div class="col-6">
                            <h6>Giấy phép lái xe:</h6>
                            @if($imageUrls['driver_license_image'])
                                <img src="{{ $imageUrls['driver_license_image'] }}" alt="Giấy phép lái xe" class="img-thumbnail document-image" data-toggle="modal" data-target="#imageModal" data-image-src="{{ $imageUrls['driver_license_image'] }}" data-image-title="Giấy phép lái xe">
                            @else
                                <div class="document-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>Không có ảnh</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-6">
                            <h6>Đăng ký xe:</h6>
                            @if($imageUrls['vehicle_registration_image'])
                                <img src="{{ $imageUrls['vehicle_registration_image'] }}" alt="Đăng ký xe" class="img-thumbnail document-image" data-toggle="modal" data-target="#imageModal" data-image-src="{{ $imageUrls['vehicle_registration_image'] }}" data-image-title="Đăng ký xe">
                            @else
                                <div class="document-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>Không có ảnh</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin ngân hàng và liên hệ khẩn cấp -->
        <div class="col-md-6">
            <div class="data-table-card">
                <div class="data-table-header">
                    <h2 class="data-table-card-title">Thông tin ngân hàng</h2>
                </div>
                <div class="p-4">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Ngân hàng:</strong></td>
                            <td>{{ $application->bank_name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Số tài khoản:</strong></td>
                            <td>{{ $application->bank_account_number }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tên chủ tài khoản:</strong></td>
                            <td>{{ $application->bank_account_name }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="data-table-card mt-3">
                <div class="data-table-header">
                    <h2 class="data-table-card-title">Liên hệ khẩn cấp</h2>
                </div>
                <div class="p-4">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Tên:</strong></td>
                            <td>{{ $application->emergency_contact_name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Số điện thoại:</strong></td>
                            <td>{{ $application->emergency_contact_phone }}</td>
                        </tr>
                        <tr>
                            <td><strong>Mối quan hệ:</strong></td>
                            <td>{{ $application->emergency_contact_relationship }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Thông tin admin notes nếu có -->
            @if($application->admin_notes)
            <div class="data-table-card mt-3">
                <div class="data-table-header">
                    <h2 class="data-table-card-title">Ghi chú của Admin</h2>
                </div>
                <div class="p-4">
                    <p class="mb-0">{{ $application->admin_notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Timeline -->
    <div class="data-table-card mt-4">
        <div class="data-table-header">
            <h2 class="data-table-card-title">Lịch sử xử lý</h2>
        </div>
        <div class="p-4">
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h6>Nộp đơn đăng ký</h6>
                        <p class="text-muted">{{ $application->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
                
                @if($application->reviewed_at)
                <div class="timeline-item">
                    <div class="timeline-marker timeline-marker-success"></div>
                    <div class="timeline-content">
                        <h6>
                            @if($application->status === 'approved') Đã duyệt đơn
                            @else Đã từ chối đơn @endif
                        </h6>
                        <p class="text-muted">{{ Carbon\Carbon::parse($application->reviewed_at)->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Xem ảnh</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="" class="img-fluid">
<!-- Modals -->
<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa thông tin tài xế</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn sẽ được chuyển đến trang chỉnh sửa thông tin tài xế.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <a href="{{ route('admin.drivers.edit', $driver) }}" class="btn btn-primary">Chỉnh sửa</a>
            </div>
        </div>
    </div>
</div>

<style>
.document-image {
    width: 100%;
    height: 120px;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.2s;
}

.document-image:hover {
    transform: scale(1.05);
}

.document-placeholder {
    width: 100%;
    height: 120px;
    background-color: #f8f9fa;
    border: 2px dashed #dee2e6;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.document-placeholder i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
}

.timeline-marker {
    position: absolute;
    left: -2.25rem;
    top: 0.25rem;
    width: 1rem;
    height: 1rem;
    background-color: #007bff;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 3px #e9ecef;
}

.timeline-marker-success {
    background-color: #28a745;
}

.timeline::before {
    content: '';
    position: absolute;
    left: -1.75rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e9ecef;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle image modal
    $('#imageModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var imageSrc = button.data('image-src');
        var imageTitle = button.data('image-title');
        
        var modal = $(this);
        modal.find('.modal-title').text(imageTitle);
        modal.find('#modalImage').attr('src', imageSrc);
    });
});
</script>
@endpush
<!-- Status Toggle Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $driver->status === 'active' ? 'Vô hiệu hóa' : 'Kích hoạt' }} tài khoản</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusForm">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Bạn có chắc muốn {{ $driver->status === 'active' ? 'vô hiệu hóa' : 'kích hoạt' }} tài khoản này?
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lý do <span class="required">*</span></label>
                        <textarea class="form-control" name="reason" rows="3" required 
                                  placeholder="Nhập lý do {{ $driver->status === 'active' ? 'vô hiệu hóa' : 'kích hoạt' }}..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn {{ $driver->status === 'active' ? 'btn-danger' : 'btn-success' }}">
                        {{ $driver->status === 'active' ? 'Vô hiệu hóa' : 'Kích hoạt' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lock Account Modal -->
<div class="modal fade" id="lockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $driver->status === 'locked' ? 'Mở khóa' : 'Khóa' }} tài khoản</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="lockForm">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ $driver->status === 'locked' ? 'Mở khóa tài khoản sẽ cho phép tài xế đăng nhập lại.' : 'Khóa tài khoản sẽ ngăn tài xế đăng nhập vào hệ thống.' }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lý do <span class="required">*</span></label>
                        <textarea class="form-control" name="reason" rows="3" required 
                                  placeholder="Nhập lý do {{ $driver->status === 'locked' ? 'mở khóa' : 'khóa' }}..."></textarea>
                    </div>
                    @if($driver->status !== 'locked')
                    <div class="mb-3">
                        <label class="form-label">Khóa đến (tùy chọn)</label>
                        <input type="datetime-local" class="form-control" name="lock_until" 
                               min="{{ now()->format('Y-m-d\TH:i') }}">
                        <small class="form-text text-muted">Để trống nếu khóa vô thời hạn</small>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn {{ $driver->status === 'locked' ? 'btn-success' : 'btn-warning' }}">
                        {{ $driver->status === 'locked' ? 'Mở khóa' : 'Khóa tài khoản' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset mật khẩu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="resetPasswordForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Mật khẩu mới sẽ được tạo tự động và gửi qua email cho tài xế.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lý do reset mật khẩu <span class="required">*</span></label>
                        <textarea class="form-control" name="reason" rows="3" required 
                                  placeholder="Nhập lý do reset mật khẩu..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Reset mật khẩu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Violation Modal -->
<div class="modal fade" id="violationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm vi phạm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="violationForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Loại vi phạm <span class="required">*</span></label>
                        <select class="form-control" name="violation_type" required>
                            <option value="">Chọn loại vi phạm</option>
                            <option value="late_delivery">Giao hàng trễ</option>
                            <option value="customer_complaint">Khiếu nại của khách hàng</option>
                            <option value="traffic_violation">Vi phạm giao thông</option>
                            <option value="inappropriate_behavior">Hành vi không phù hợp</option>
                            <option value="fraud">Gian lận</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả chi tiết <span class="required">*</span></label>
                        <textarea class="form-control" name="description" rows="4" required 
                                  placeholder="Mô tả chi tiết về vi phạm..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mức độ nghiêm trọng <span class="required">*</span></label>
                        <select class="form-control" name="severity" required>
                            <option value="">Chọn mức độ</option>
                            <option value="low">Nhẹ</option>
                            <option value="medium">Trung bình</option>
                            <option value="high">Nghiêm trọng</option>
                            <option value="critical">Rất nghiêm trọng</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số tiền phạt (VNĐ)</label>
                        <input type="number" class="form-control" name="penalty_amount" min="0" 
                               placeholder="Nhập số tiền phạt (nếu có)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Thêm vi phạm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalTitle">Xem ảnh</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="imageModalImg" src="" class="img-fluid" alt="Image">
            </div>
        </div>
    </div>
</div>

<script>
function showImageModal(imageSrc, title = 'Xem ảnh') {
    document.getElementById('imageModalImg').src = imageSrc;
    document.getElementById('imageModalTitle').textContent = title;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

function showEditModal() {
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function showStatusModal() {
    new bootstrap.Modal(document.getElementById('statusModal')).show();
}

function showLockModal() {
    new bootstrap.Modal(document.getElementById('lockModal')).show();
}

function showResetPasswordModal() {
    new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
}

function showViolationModal() {
    new bootstrap.Modal(document.getElementById('violationModal')).show();
}

// Status toggle form submission
document.getElementById('statusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
    
    fetch('{{ route("admin.drivers.toggle-status", $driver) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            toastr.error(data.message || 'Có lỗi xảy ra');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('Có lỗi xảy ra khi xử lý yêu cầu');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});

// Lock account form submission
document.getElementById('lockForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    const isLocked = {{ $driver->status === 'locked' ? 'true' : 'false' }};
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
    
    const url = isLocked ? 
        '{{ route("admin.drivers.unlock-account", $driver) }}' : 
        '{{ route("admin.drivers.lock-account", $driver) }}';
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            toastr.error(data.message || 'Có lỗi xảy ra');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('Có lỗi xảy ra khi xử lý yêu cầu');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});

// Reset password form submission
document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
    
    fetch('{{ route("admin.drivers.reset-password", $driver) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal')).hide();
            this.reset();
        } else {
            toastr.error(data.message || 'Có lỗi xảy ra');
        }
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('Có lỗi xảy ra khi xử lý yêu cầu');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});

// Add violation form submission
document.getElementById('violationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
    
    fetch('{{ route("admin.drivers.add-violation", $driver) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            toastr.error(data.message || 'Có lỗi xảy ra');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('Có lỗi xảy ra khi xử lý yêu cầu');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize Bootstrap tabs
    const tabTriggerEl = document.querySelector('#driverDetailTabs button[data-bs-toggle="tab"]');
    if (tabTriggerEl) {
        bootstrap.Tab.getOrCreateInstance(tabTriggerEl);
    }
});
</script>

@endsection
