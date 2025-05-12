@extends('layouts.customer.fullLayoutMaster')

@section('title', 'Thông tin tài khoản')

@section('styles')
<style>
    .profile-container {
        padding: 3rem 0;
        background-color: #f8f9fa;
    }
    .profile-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .profile-header {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
        border-bottom: 1px solid #eee;
        padding-bottom: 1rem;
    }
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 1.5rem;
        border: 3px solid #e31837;
    }
    .profile-info {
        flex: 1;
    }
    .profile-title {
        color: #e31837;
        margin-bottom: 0.5rem;
        font-size: 1.8rem;
    }
    .profile-subtitle {
        color: #6c757d;
        font-size: 1rem;
    }
    .info-group {
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 1rem;
    }
    .info-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    .info-value {
        color: #212529;
        font-size: 1.1rem;
    }
    .btn-edit {
        background-color: #e31837;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    .btn-edit:hover {
        background-color: #c01530;
        color: white;
    }
    .section-title {
        color: #e31837;
        margin-bottom: 1.5rem;
        font-size: 1.5rem;
        border-bottom: 2px solid #e31837;
        padding-bottom: 0.5rem;
        display: inline-block;
    }
</style>
@endsection

@section('content')
<div class="container profile-container">
    <div class="row">
        <div class="col-md-4">
            <div class="profile-card">
                <div class="text-center">
                    <img src="{{ $user->avatar ? asset('storage/avatars/'.$user->avatar) : asset('images/avatar-placeholder.jpg') }}" alt="Avatar" class="profile-avatar mx-auto d-block">
                    <h4 class="mt-3">{{ $user->name }}</h4>
                    <p class="text-muted">Thành viên từ {{ $user->created_at->format('d/m/Y') }}</p>
                    <a href="{{ route('customer.profile.form') }}" class="btn-edit mt-3">
                        <i class="fas fa-edit me-2"></i> Chỉnh sửa thông tin
                    </a>
                </div>
                
                <div class="mt-4">
                    <h5 class="section-title">Menu</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('customer.profile') }}" class="nav-link active">
                                <i class="fas fa-user me-2"></i> Thông tin cá nhân
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-shopping-bag me-2"></i> Đơn hàng của tôi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-heart me-2"></i> Sản phẩm yêu thích
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-map-marker-alt me-2"></i> Địa chỉ giao hàng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-bell me-2"></i> Thông báo
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="profile-card">
                <div class="profile-header">
                    <h3 class="profile-title">Thông tin cá nhân</h3>
                </div>
                
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-group">
                            <div class="info-label">Họ và tên</div>
                            <div class="info-value">{{ $user->full_name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <div class="info-label">Email</div>
                            <div class="info-value">{{ $user->email }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <div class="info-label">Số điện thoại</div>
                            <div class="info-value">{{ $user->phone ?? 'Chưa cập nhật' }}</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-group">
                            <div class="info-label">Ngày tham gia</div>
                            <div class="info-value">{{ $user->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h5 class="section-title">Bảo mật</h5>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <div class="info-label">Mật khẩu</div>
                            <div class="info-value">********</div>
                        </div>
                        <a href="#" class="btn-edit btn-sm">
                            <i class="fas fa-key me-2"></i> Đổi mật khẩu
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="profile-card">
                <div class="profile-header">
                    <h3 class="profile-title">Đơn hàng gần đây</h3>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mã đơn hàng</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($orders) && count($orders) > 0)
                                @foreach($orders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                    <td>{{ number_format($order->total_amount) }}đ</td>
                                    <td>
                                        @if($order->status == 'pending')
                                            <span class="badge bg-warning">Đang xử lý</span>
                                        @elseif($order->status == 'processing')
                                            <span class="badge bg-info">Đang giao hàng</span>
                                        @elseif($order->status == 'completed')
                                            <span class="badge bg-success">Đã giao hàng</span>
                                        @elseif($order->status == 'cancelled')
                                            <span class="badge bg-danger">Đã hủy</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary">Chi tiết</a>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center">Bạn chưa có đơn hàng nào</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
                <div class="text-center mt-3">
                    <a href="#" class="btn-edit">Xem tất cả đơn hàng</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection