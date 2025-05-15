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
            <div class="login-container" style="max-width: 800px; height: auto;">
                <div class="login-form-box" style="padding: 2rem;">
                    <h2 id="form-title" style="font-size: 1.5rem;">Đăng Nhập</h2>

                    <div class="login-input-box">
                        <input type="email" name="email" id="email" value="{{ old('email') }}" 
                               placeholder=" " required />
                        <label class="placeholder-label">Địa chỉ Email</label>
                        <div class="dots-animation"></div>
                        @error('email')
                            <div class="text-danger" style="color:red;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="login-input-box">
                        <input type="password" name="password" id="password" 
                               placeholder=" " required />
                        <label class="placeholder-label">Mật Khẩu</label>
                        <div class="dots-animation"></div>
                        @error('password')
                            <div class="text-danger" style="color:red;">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="login-btn">Đăng Nhập</button>
                    <p class="login-toggle-text">
                        Bạn chưa có tài khoản?
                        <a href="/register" id="toggle-btn">Đăng Kí Ngay</a>
                    </p>

                    <div class="social-login-icons">
                        <a href="" class="social-icon google">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 488 512"><path fill="currentColor" d="M488 261.8C488 403.3 391.1 504 248 504 110.8 504 0 393.2 0 256S110.8 8 248 8c66.8 0 123 24.5 166.3 64.9l-67.5 64.9C258.5 52.6 94.3 116.6 94.3 256c0 86.5 69.1 156.6 153.7 156.6 98.2 0 135-70.4 140.8-106.9H248v-85.3h236.1c2.3 12.7 3.9 24.9 3.9 41.4z"/></svg>
                        </a>
                        <a href="" class="social-icon facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.24 0 225.39 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/></svg>
                        </a>
                        <a href="" class="social-icon instagram">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.5 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7 2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>
                        </a>
                    </div>
                </div>
                <div class="login-image-box" style="padding: 1rem;">
                    <img src="{{ asset('images/login-side-image.jpg') }}" alt="Login Image" style="max-height: 400px;">
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
