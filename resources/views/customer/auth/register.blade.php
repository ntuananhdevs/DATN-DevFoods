@extends('layouts.customer.fullLayoutMaster')
@section('content')
@if (session('error'))
    <div class="alert alert-danger" style="color: red; margin-bottom: 15px;">
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success" style="color: green; margin-bottom: 15px;">
        {{ session('success') }}
    </div>
@endif
    <link rel="stylesheet" href="{{ asset('css/customer/login.css') }}">
    <script src="{{ asset('js/Customer/login.js') }}"></script>
    <div class="auth-page-wrapper">
        <div class="login-container">
            <div class="login-form-box">
                <h2 id="form-title">Đăng Ký</h2>
                
                <form id="auth-form" method="POST" action="{{ route('customer.register.submit') }}">
                    @csrf
                    <div class="login-input-box">
                        <input type="text" id="user_name" name="user_name" value="{{ old('user_name') }}" />
                        <label>Tên Người Dùng</label>
                        @error('user_name')<span style="color:red;">{{ $message }}</span>@enderror
                    </div>
                    
                    <div class="login-input-box">
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" />
                        <label>Họ và Tên</label>
                        @error('full_name')<span style="color:red;">{{ $message }}</span>@enderror
                    </div>
                    <div class="login-input-box">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" />
                        <label>Email</label>
                        @error('email')
                            <span style="color: red; font-size: 12px; display: block; margin-top: 5px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="login-input-box">
                        <input type="password" id="password" name="password" />
                        <label>Mật Khẩu</label>
                        @error('password')
                            <span style="color: red; font-size: 12px; display: block; margin-top: 5px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="login-input-box">
                        <input type="password" id="password_confirmation" name="password_confirmation" />
                        <label>Xác Nhận Mật Khẩu</label>
                    </div>
                    <button type="submit" class="login-btn">Đăng Ký</button>
                    <p class="login-toggle-text">Bạn đã có tài khoản? <a href="{{ route('customer.login') }}" id="toggle-btn">Đăng Nhập Ngay</a></p>
                </form>
            </div>
        </div>
    </div>
@endsection