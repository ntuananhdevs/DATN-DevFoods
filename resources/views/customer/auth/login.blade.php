@extends('layouts.customer.fullLayoutMaster')

@section('content')
<link rel="stylesheet" href="{{ asset('css/customer/login.css') }}">
<script src="{{ asset('js/Customer/login.js') }}"></script>
<script src="https://accounts.google.com/gsi/client" async defer></script>

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div>
    <form action="{{ route('customer.login.submit') }}" method="post">
        @csrf
        <div class="auth-page-wrapper">
            <div class="login-container">
                <div class="login-form-box">
                    <h2 id="form-title">Đăng Nhập</h2>

                    <div class="login-input-box">
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required />
                        <label>Địa chỉ Email</label>
                        @error('email')
                            <div class="text-danger" style="color:red;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="login-input-box">
                        <input type="password" name="password" id="password" required />
                        <label>Mật Khẩu</label>
                        @error('password')
                            <div class="text-danger" style="color:red;">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="login-btn">Đăng Nhập</button>
                    <p class="login-toggle-text">
                        Bạn chưa có tài khoản?
                        <a href="/register" id="toggle-btn">Đăng Kí Ngay</a>
                    </p>

                    <div id="g_id_onload"
                         data-client_id="YOUR_GOOGLE_CLIENT_ID"
                         data-callback="handleCredentialResponse"
                         data-auto_prompt="false">
                    </div>
                    <div class="g_id_signin"
                         data-type="standard"
                         data-size="large"
                         data-theme="outline"
                         data-text="sign_in_with"
                         data-shape="rectangular"
                         data-logo_alignment="left">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
