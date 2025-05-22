<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <link rel="stylesheet" href="{{ asset('css/drivers/Auth.css') }}">
</head>
<style>
    .input-error,
    .in-invaid {
        border: 1px solid red;
        background-color: #ffe6e6;
    }

    /* Thông báo lỗi dưới input */
    .error-message {
        color: red;
        font-size: 0.875rem;
        margin-top: 4px;
        display: block;
    }
</style>

<body>
    <div class="container">
        <div class="login-container">
            <a href="/driver/login" class="back-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m12 19-7-7 7-7"></path>
                    <path d="M19 12H5"></path>
                </svg>
                Quay lại trang đăng nhập
            </a>

            <div class="header">
                <h1>Quên mật khẩu</h1>
                <p>Nhập email đăng ký của bạn và chúng tôi sẽ gửi mã OTP để đặt lại mật khẩu.</p>
            </div>

            <div id="successAlert" class="alert success hidden">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <div class="alert-content">
                    <h4>Mã OTP đã được gửi!</h4>
                    <p>Chúng tôi đã gửi mã xác thực (OTP) đến email <span id="emailSent"></span>. Vui lòng kiểm tra hộp
                        thư của bạn và nhập mã để tiếp tục.</p>
                </div>
            </div>

            <form id="forgotPasswordForm" method="POST" action="{{ route('driver.send_otp') }}" class="form">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="@error('email') in-invaid @enderror"
                        value="{{ old('email') }}" autofocus placeholder="Nhập email đăng ký của bạn">
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" id="sendOtpButton">
                    <span class="btn-text">Gửi mã xác thực OTP</span>
                    <span class="btn-loading hidden">
                        <svg class="spinner" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor"
                                stroke-width="4"></circle>
                        </svg>
                        Đang gửi...
                    </span>
                </button>
            </form>
        </div>
    </div>

    <!-- Toast thông báo -->
    <div class="toast hidden" id="toast">
        <div class="toast-content">
            <div class="toast-icon success">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div class="toast-icon error hidden">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
            </div>
            <div class="toast-message">
                <h4 id="toastTitle">Thành công</h4>
                <p id="toastDescription">Thao tác đã được thực hiện thành công.</p>
            </div>
        </div>
    </div>

    {{-- <script src="{{ asset('js/Driver/main.js') }}"></script> --}}
</body>

</html>
