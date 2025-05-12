@extends('layouts.customer.fullLayoutMaster')

@section('title', 'Chỉnh sửa thông tin tài khoản')

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
    .profile-title {
        color: #e31837;
        margin-bottom: 0.5rem;
        font-size: 1.8rem;
    }
    .profile-subtitle {
        color: #6c757d;
        font-size: 1rem;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
        display: block;
    }
    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 1rem;
    }
    .form-control:focus {
        border-color: #e31837;
        box-shadow: 0 0 0 0.2rem rgba(227, 24, 55, 0.25);
    }
    .btn-update {
        background-color: #e31837;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-update:hover {
        background-color: #c01530;
    }
    .btn-cancel {
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    .btn-cancel:hover {
        background-color: #5a6268;
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
    .avatar-preview {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto 1rem;
    }
    .avatar-preview img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #e31837;
    }
    .avatar-edit {
        position: absolute;
        right: 0;
        bottom: 0;
        width: 40px;
        height: 40px;
        background-color: #e31837;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: white;
    }
    .avatar-edit input {
        display: none;
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
                </div>
                
                <div class="mt-4">
                    <h5 class="section-title">Menu</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('customer.profile') }}" class="nav-link">
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
                    <h3 class="profile-title">Chỉnh sửa thông tin cá nhân</h3>
                </div>
                
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="avatar-preview mb-4">
                        <img id="avatar-preview-img" src="{{ $user->avatar ? asset('storage/avatars/'.$user->avatar) : asset('images/avatar-placeholder.jpg') }}" alt="Avatar">
                        <label for="avatar-upload" class="avatar-edit">
                            <i class="fas fa-camera"></i>
                            <input type="file" id="avatar-upload" name="avatar" accept="image/*" onchange="previewAvatar(this)">
                        </label>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Họ và tên</label>
                                <input type="text" name="full_name" class="form-control" value="{{ old('name', $user->full_name) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                                <small class="text-muted">Email không thể thay đổi</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                            </div>
                        </div>
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('customer.profile') }}" class="btn-cancel">
                            <i class="fas fa-arrow-left me-2"></i> Quay lại
                        </a>
                        <button type="submit" class="btn-update">
                            <i class="fas fa-save me-2"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('avatar-preview-img').src = e.target.result;
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection