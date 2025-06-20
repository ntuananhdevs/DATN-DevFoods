@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Chi tiết tài xế')
@section('description', 'Xem thông tin chi tiết tài xế')

@section('content')
<style>
    /* Import Google Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    /* Base styles with consistent font */
    * {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    
    .profile-card {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        border-radius: 16px;
        color: white;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.2);
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid rgba(255, 255, 255, 0.2);
        object-fit: cover;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    
    .info-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #f1f5f9;
        transition: box-shadow 0.2s ease;
    }
    
    .info-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }
    
    .info-card h5 {
        color: #1e293b;
        font-weight: 600;
        font-size: 1.125rem;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #4f46e5;
        display: inline-block;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
        box-shadow: 0 4px 16px rgba(79, 70, 229, 0.2);
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        line-height: 1;
    }
    
    .stat-label {
        font-size: 0.875rem;
        opacity: 0.9;
        font-weight: 500;
    }
    
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .status-active {
        background-color: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    
    .status-inactive {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    
    .status-locked {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    
    .available-badge {
        background-color: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    
    .unavailable-badge {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    
    .info-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }
    
    .info-table th {
        background-color: #f8fafc;
        padding: 0.875rem;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e2e8f0;
        width: 35%;
        text-align: left;
    }
    
    .info-table td {
        padding: 0.875rem;
        border-bottom: 1px solid #f1f5f9;
        color: #64748b;
        font-weight: 400;
    }
    
    .info-table tr:last-child th,
    .info-table tr:last-child td {
        border-bottom: none;
    }
    
    .document-image {
        max-width: 200px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid #f1f5f9;
    }
    
    .document-image:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        border-color: #4f46e5;
    }
    
    .rating-stars {
        color: #f59e0b;
        margin-left: 0.5rem;
    }
    
    .back-btn {
        background: linear-gradient(135deg, #64748b 0%, #475569 100%);
        color: white;
        padding: 0.625rem 1.25rem;
        border-radius: 10px;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 2px 8px rgba(100, 116, 139, 0.2);
    }
    
    .back-btn:hover {
        background: linear-gradient(135deg, #475569 0%, #334155 100%);
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(100, 116, 139, 0.3);
    }
    
    .action-btn {
        padding: 0.625rem 1.25rem;
        border-radius: 10px;
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
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .btn-edit {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }
    
    .btn-edit:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    
    .btn-danger:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
    
    .btn-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    
    .btn-warning:hover {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }
    
    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .btn-success:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .btn-secondary {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        color: white;
    }
    
    .btn-secondary:hover {
        background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
    }
    
    .quick-action-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .quick-action-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        cursor: pointer;
        border: 1px solid #f1f5f9;
    }
    
    .quick-action-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        border-color: #e2e8f0;
    }
    
    .quick-action-card h6 {
        color: #1e293b;
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }
    
    .quick-action-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.25rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .nav-tabs {
        border-bottom: 2px solid #f1f5f9;
        margin-bottom: 0;
    }
    
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        border-radius: 0;
        color: #64748b;
        padding: 1rem 1.5rem;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }
    
    .nav-tabs .nav-link:hover {
        color: #4f46e5;
        border-bottom-color: #c7d2fe;
    }
    
    .nav-tabs .nav-link.active {
        border-bottom-color: #4f46e5;
        color: #4f46e5;
        background: none;
        font-weight: 600;
    }
    
    .tab-content {
        padding: 2rem 0;
    }
    
    .violation-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    
    .violation-low {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    
    .violation-medium {
        background-color: #fed7d7;
        color: #c53030;
        border: 1px solid #feb2b2;
    }
    
    .violation-high {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    
    .violation-critical {
        background-color: #fecaca;
        color: #7f1d1d;
        border: 1px solid #f87171;
    }
    
    .order-status {
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    
    .order-status.completed {
        background-color: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    
    .order-status.cancelled {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    
    .order-status.pending {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    
    .order-status.in_delivery {
        background-color: #e0e7ff;
        color: #3730a3;
        border: 1px solid #c7d2fe;
    }
    
    /* Typography improvements */
    h1, h2, h3, h4, h5, h6 {
        font-family: 'Inter', sans-serif;
        font-weight: 600;
        line-height: 1.2;
        color: #1e293b;
    }
    
    .text-muted {
        color: #64748b !important;
        font-weight: 400;
    }
    
    .small {
        font-size: 0.875rem;
        line-height: 1.4;
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .profile-card {
            padding: 1.5rem;
        }
        
        .quick-action-cards {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0.75rem;
        }
        
        .stat-card {
            padding: 1rem;
        }
        
        .stat-number {
            font-size: 1.5rem;
        }
        
        .nav-tabs .nav-link {
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
        }
    }
    
    /* Animation for fade-in */
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
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
                    <span class="ms-1">{{ number_format($driver->rating ?? 0, 1) }}/5.0</span>
                    <span class="ms-2 text-sm opacity-75">({{ $stats['total_orders'] ?? 0 }} đơn hàng)</span>
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
                    Điểm tin cậy: {{ $driver->reliability_score ?? 0 }}/100
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
                <div class="stat-number">{{ $stats['total_orders'] ?? 0 }}</div>
                <div class="stat-label">Tổng đơn hàng</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="stat-number">{{ $stats['completed_orders'] ?? 0 }}</div>
                <div class="stat-label">Hoàn thành</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                <div class="stat-number">{{ $stats['cancelled_orders'] ?? 0 }}</div>
                <div class="stat-label">Đã hủy</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="stat-number">{{ number_format($stats['total_earnings'] ?? 0) }}đ</div>
                <div class="stat-label">Tổng thu nhập</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                <div class="stat-number">{{ $stats['total_violations'] ?? 0 }}</div>
                <div class="stat-label">Vi phạm</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
                <div class="stat-number">{{ $driver->reliability_score ?? 0 }}/100</div>
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

                    <!-- Thông tin vị trí -->
                    @if($driver->location)
                        <div class="info-card">
                            <h5><i class="fas fa-map-marker-alt me-2"></i>Vị trí hiện tại</h5>
                            <table class="info-table">
                                <tr><th>Latitude</th><td>{{ $driver->location->latitude }}</td></tr>
                                <tr><th>Longitude</th><td>{{ $driver->location->longitude }}</td></tr>
                                <tr><th>Địa chỉ</th><td>{{ $driver->location->address ?? $driver->address }}</td></tr>
                            </table>
                        </div>
                    @endif

                    <!-- Thông tin vi phạm -->
                    @if($driver->violations && $driver->violations->count())
                        <div class="info-card">
                            <h5><i class="fas fa-exclamation-triangle me-2"></i>Vi phạm</h5>
                            <table class="info-table">
                                <tr><th>Ngày</th><th>Nội dung</th><th>Mức độ</th></tr>
                                @foreach($driver->violations as $violation)
                                    <tr>
                                        <td>{{ $violation->created_at->format('d/m/Y') }}</td>
                                        <td>{{ $violation->content }}</td>
                                        <td><span class="violation-badge violation-{{ $violation->level }}">{{ ucfirst($violation->level) }}</span></td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @endif
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

                    @if($driver->admin_notes)
                    <div class="info-card">
                        <h5><i class="fas fa-sticky-note me-2"></i>Ghi chú của admin</h5>
                        <p class="mb-0">{{ $driver->admin_notes }}</p>
                    </div>
                    @endif

                    <div class="info-card">
                        <h5><i class="fas fa-cog me-2"></i>Trạng thái tài khoản</h5>
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
                            <tr>
                                <th>Điểm tin cậy</th>
                                <td>{{ $driver->reliability_score ?? 0 }}/100</td>
                            </tr>
                            <tr>
                                <th>Đánh giá</th>
                                <td>{{ number_format($driver->rating ?? 0, 1) }}/5.0</td>
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
        </div>

        <!-- Documents Tab -->
        <div class="tab-pane fade" id="documents" role="tabpanel">
            <div class="info-card text-center">
                <a href="{{ route('admin.drivers.edit', ['driver' => $driver->id]) }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-file-alt me-2"></i> Xem đầy đủ giấy tờ & chỉnh sửa
                </a>
            </div>
        </div>

        <!-- Activity Tab -->
        <div class="tab-pane fade" id="activity" role="tabpanel">
            <div class="row">
                <div class="col-md-12">
                    <div class="info-card">
                        <h5><i class="fas fa-chart-bar me-2"></i>Thống kê hoạt động</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-primary">{{ $stats['total_orders'] ?? 0 }}</h3>
                                    <p class="text-muted">Tổng đơn hàng</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-success">{{ $stats['completed_orders'] ?? 0 }}</h3>
                                    <p class="text-muted">Đơn hoàn thành</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-danger">{{ $stats['cancelled_orders'] ?? 0 }}</h3>
                                    <p class="text-muted">Đơn hủy</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-warning">{{ number_format($stats['total_earnings'] ?? 0) }}đ</h3>
                                    <p class="text-muted">Tổng thu nhập</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Tab -->
        <div class="tab-pane fade" id="history" role="tabpanel">
            <div class="info-card">
                <h5><i class="fas fa-history me-2"></i>Lịch sử đơn hàng gần đây</h5>
                @if(isset($recentOrders) && $recentOrders->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Trạng thái</th>
                                <th>Tổng tiền</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->customer_name ?? 'N/A' }}</td>
                                <td>
                                    <span class="order-status {{ $order->status }}">
                                        {{ $order->status_text ?? $order->status }}
                                    </span>
                                </td>
                                <td>{{ number_format($order->total_amount) }}đ</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-box-open text-muted" style="font-size: 3rem;"></i>
                    <h6 class="mt-3">Chưa có đơn hàng nào</h6>
                    <p class="text-muted">Tài xế này chưa thực hiện đơn hàng nào</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Violations Tab -->
        <div class="tab-pane fade" id="violations" role="tabpanel">
            <div class="info-card">
                <h5><i class="fas fa-exclamation-triangle me-2"></i>Lịch sử vi phạm</h5>
                @if(isset($violations) && $violations->count() > 0)
                <div class="table-responsive">
                    <table class="table">
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
                            @foreach($violations as $violation)
                            <tr>
                                <td>{{ $violation->type }}</td>
                                <td>{{ $violation->description }}</td>
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
</div>

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

<!-- Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thay đổi trạng thái tài xế</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn thay đổi trạng thái của tài xế này?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="confirmStatusChange()">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<!-- Lock Modal -->
<div class="modal fade" id="lockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Khóa/Mở khóa tài xế</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="lockReason" class="form-label">Lý do khóa:</label>
                    <textarea class="form-control" id="lockReason" rows="3" placeholder="Nhập lý do khóa tài khoản..."></textarea>
                </div>
                <div class="mb-3">
                    <label for="lockUntil" class="form-label">Khóa đến ngày (tùy chọn):</label>
                    <input type="datetime-local" class="form-control" id="lockUntil">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-warning" onclick="confirmLockAction()">Xác nhận</button>
            </div>
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
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn reset mật khẩu cho tài xế này? Mật khẩu mới sẽ được gửi qua email.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="confirmResetPassword()">Reset mật khẩu</button>
            </div>
        </div>
    </div>
</div>

<!-- Violation Modal -->
<div class="modal fade" id="violationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm vi phạm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="violationForm">
                    <div class="mb-3">
                        <label for="violationType" class="form-label">Loại vi phạm:</label>
                        <select class="form-control" id="violationType" required>
                            <option value="">Chọn loại vi phạm</option>
                            <option value="late_delivery">Giao hàng trễ</option>
                            <option value="customer_complaint">Khiếu nại từ khách hàng</option>
                            <option value="policy_violation">Vi phạm chính sách</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="violationSeverity" class="form-label">Mức độ:</label>
                        <select class="form-control" id="violationSeverity" required>
                            <option value="">Chọn mức độ</option>
                            <option value="low">Thấp</option>
                            <option value="medium">Trung bình</option>
                            <option value="high">Cao</option>
                            <option value="critical">Nghiêm trọng</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="violationDescription" class="form-label">Mô tả:</label>
                        <textarea class="form-control" id="violationDescription" rows="3" required placeholder="Mô tả chi tiết vi phạm..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="penaltyAmount" class="form-label">Số tiền phạt (VNĐ):</label>
                        <input type="number" class="form-control" id="penaltyAmount" min="0" placeholder="0">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" onclick="confirmAddViolation()">Thêm vi phạm</button>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xem ảnh</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<script>
// Modal functions
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

function showImageModal(src) {
    document.getElementById('modalImage').src = src;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

// Action functions
function toggleDriverStatus(status) {
    // Implementation for status toggle
    console.log('Toggle status to:', status);
}

function confirmStatusChange() {
    // Implementation for status change confirmation
    console.log('Status change confirmed');
}

function lockDriver() {
    showLockModal();
}

function unlockDriver() {
    // Implementation for unlock
    console.log('Unlock driver');
}

function confirmLockAction() {
    // Implementation for lock action
    console.log('Lock action confirmed');
}

function resetPassword() {
    showResetPasswordModal();
}

function confirmResetPassword() {
    // Implementation for reset password
    console.log('Reset password confirmed');
}

function confirmDelete() {
    if (confirm('Bạn có chắc chắn muốn xóa tài xế này? Hành động này không thể hoàn tác.')) {
        // Implementation for delete
        console.log('Delete confirmed');
    }
}

function confirmAddViolation() {
    // Implementation for add violation
    console.log('Add violation confirmed');
}
</script>
@endsection