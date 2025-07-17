<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng nhập tài xế</title>
    <link rel="stylesheet" href="{{ asset('css/drivers/Auth.css') }}">
</head>
<style>
    .input-error {
        border: 1px solid red;
    }

    .error-message {
        color: red;
        font-size: 0.9em;
        margin-top: 5px;
        display: block;
    }

    .alert-danger {
        background-color: #fff2f2;
        border: 1px solid #ffcccc;
        color: #cc0000;
        padding: 10px 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    .alert-danger ul {
        margin: 0;
        padding-left: 20px;
    }
    .alert-danger li {
        margin-bottom: 5px;
    }
</style>

<body>
    <div class="container">
        <div class="login-container">
            <div class="header">
                <div class="logo">
                    <img src="{{ asset('DriverLogo.png') }}" alt="Logo">
                </div>
                <h1>PolyCrispyWings Bike</h1>
                {{-- <p>Vui lòng đăng nhập bằng số điện thoại và mật khẩu đã được cung cấp qua email</p> --}}
            </div>

            <form id="loginForm" action="{{ route('driver.login.submit') }}" method="POST" class="form">
                @csrf
                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input type="tel" id="phone" class="@error('phone_number') input-error @enderror"
                        name="phone_number" placeholder="Nhập số điện thoại đăng ký" value="{{ old('phone_number') }}">
                    @error('phone_number')
                        <small class="error-message">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="label-row">
                        <label for="password">Mật khẩu</label>
                        <a href="{{ route('driver.forgot_password') }}" class="forgot-link">Quên mật khẩu?</a>
                    </div>
                    <div class="password-input">
                        <input type="password" name="password" id="password"
                            class="@error('password') input-error @enderror" placeholder="Nhập mật khẩu">
                        <button type="button" class="toggle-password" aria-label="Hiện mật khẩu">
                            <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg class="eye-off-icon hidden" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                                <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68">
                                </path>
                                <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                                <line x1="2" x2="22" y1="2" y2="22"></line>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <small class="error-message">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" id="loginButton">
                    <span class="btn-text">Đăng nhập</span>
                    <span class="btn-loading hidden">
                        <svg class="spinner" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor"
                                stroke-width="4"></circle>
                        </svg>
                        Đang đăng nhập...
                    </span>
                </button>
            </form>
        </div>
    </div>

    <!-- Dialog đổi mật khẩu -->
    <div class="dialog-overlay hidden" id="passwordChangeDialog">
        <div class="dialog">
            <div class="dialog-header">
                <h2>Đổi mật khẩu</h2>
                <p>Đây là lần đầu tiên bạn đăng nhập. Vui lòng đổi mật khẩu để đảm bảo an toàn cho tài khoản.</p>
            </div>
            <form id="passwordChangeForm" class="form">
                @csrf
                <input type="hidden" id="driverPhoneInput" name="phone_number"
                    value="{{ $driver->phone_number ?? '' }}">
                <div class="form-group">
                    <label for="newPassword">Mật khẩu mới</label>
                    <div class="password-input">
                        <input type="password" id="newPassword" placeholder="Nhập mật khẩu mới">
                        <button type="button" class="toggle-password" aria-label="Hiện mật khẩu">
                            <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg class="eye-off-icon hidden" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                                <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68">
                                </path>
                                <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                                <line x1="2" x2="22" y1="2" y2="22"></line>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Xác nhận mật khẩu</label>
                    <div class="password-input">
                        <input type="password" id="confirmPassword" placeholder="Nhập lại mật khẩu mới">
                        <button type="button" class="toggle-password" aria-label="Hiện mật khẩu">
                            <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg class="eye-off-icon hidden" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                                <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68">
                                </path>
                                <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                                <line x1="2" x2="22" y1="2" y2="22"></line>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="dialog-footer">
                    <button type="submit" class="btn btn-primary" id="changePasswordButton">
                        <span class="btn-text">Đổi mật khẩu</span>
                        <span class="btn-loading hidden">
                            <svg class="spinner" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor"
                                    stroke-width="4"></circle>
                            </svg>
                            Đang xử lý...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast thông báo -->
    <div class="toast hidden" id="toast">
        <div class="toast-content">
            <div class="toast-icon success">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div class="toast-icon error hidden">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
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
    
    <script src="{{ asset('js/Driver/main.js') }}"></script>

    <!-- Hiển thị toast từ session nếu có -->
    @if (session('toast'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Đảm bảo DOM đã tải xong
                setTimeout(function() {
                    // Hiển thị toast từ session data
                    showToast(
                        "{{ session('toast.title') }}",
                        "{{ session('toast.message') }}",
                        "{{ session('toast.type') }}"
                    );
                }, 100); // Đợi 100ms để đảm bảo các script khác đã tải xong
            });
        </script>
    @endif
</body>

</html>