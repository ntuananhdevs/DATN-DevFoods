@extends('layouts.customer.fullLayoutMaster')

@section('content')
<link rel="stylesheet" href="{{ asset('css/customer/register.css') }}">
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
            <div class="login-form-box" style="padding: 2rem;">
                <h2 id="form-title" style="font-size: 1.5rem;">Đăng Ký</h2>
                
                <form id="auth-form" method="POST" action="{{ route('customer.register.submit') }}">
                    @csrf
                    <div class="login-input-box">
                        <input type="text" id="user_name" name="user_name" value="{{ old('user_name') }}" placeholder=" " />
                        <label class="placeholder-label">Tên Người Dùng</label>
                        <div class="dots-animation"></div>
                        @error('user_name')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    
                    <div class="login-input-box">
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" placeholder=" " />
                        <label class="placeholder-label">Họ và Tên</label>
                        <div class="dots-animation"></div>
                        @error('full_name')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="login-input-box">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder=" " />
                        <label class="placeholder-label">Email</label>
                        <div class="dots-animation"></div>
                        @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="login-input-box">
                        <input type="password" id="password" name="password" placeholder=" " />
                        <label class="placeholder-label">Mật Khẩu</label>
                        <div class="dots-animation"></div>
                        @error('password')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="login-input-box">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder=" " />
                        <label class="placeholder-label">Xác Nhận Mật Khẩu</label>
                        <div class="dots-animation"></div>
                    </div>
                    <button type="submit" class="login-btn">Đăng Ký</button>
                </form>
            </div>
            <div class="login-image-box" style="padding: 1rem;">
                <img src="{{ asset('images/register-side-image.jpg') }}" alt="Register Image" style="max-height: 400px;">
            </div>
        </div>
    </div>
@endsection