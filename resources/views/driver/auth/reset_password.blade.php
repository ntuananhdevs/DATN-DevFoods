<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <link rel="stylesheet" href="{{ asset('css/drivers/Auth.css') }}">
</head>
<style>
    .input-error {
        border: 1px solid #ef4444 !important;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2) !important;
    }

    .error-message {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
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
                <h1>Đặt lại mật khẩu</h1>
                <p>Tạo mật khẩu mới cho tài khoản của bạn</p>
            </div>

            <div id="invalidTokenAlert" class="alert error hidden">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
                <div class="alert-content">
                    <h4>Thông tin không hợp lệ</h4>
                    <p>Thông tin đặt lại mật khẩu không hợp lệ hoặc đã hết hạn. Vui lòng yêu cầu mã OTP mới.</p>
                    <button class="btn btn-outline" onclick="window.location.href='forgot-password.html'">Yêu cầu mã OTP
                        mới</button>
                </div>
            </div>

            <form method="POST" action="{{ route('driver.reset_password.submit', ['driver_id' => $driver_id]) }}">
                @csrf
                <div class="form-group">
                    <label for="newPassword">Mật khẩu mới</label>
                    <div class="password-input">
                        <input type="password" id="newPassword" placeholder="Nhập mật khẩu mới" name="password"
                            class="@error('password') input-error @enderror">
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
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Xác nhận mật khẩu</label>
                    <div class="password-input">
                        <input type="password" name="password_confirmation" id="confirmPassword"
                            placeholder="Nhập lại mật khẩu mới"
                            class="@error('password_confirmation') input-error @enderror">
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
                    @error('password_confirmation')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" id="resetPasswordButton">
                    <span class="btn-text">Đặt lại mật khẩu</span>
                    <span class="btn-loading hidden">
                        <svg class="spinner" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor"
                                stroke-width="4"></circle>
                        </svg>
                        Đang xử lý...
                    </span>
                </button>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const resetPasswordForm = document.getElementById('resetPasswordForm');
            const newPasswordInput = document.getElementById('newPassword');
            const confirmPasswordInput = document.getElementById('confirmPassword');
            const resetPasswordButton = document.getElementById('resetPasswordButton');
            const invalidTokenAlert = document.getElementById('invalidTokenAlert');

            // Lấy thông tin từ URL
            const urlParams = new URLSearchParams(window.location.search);
            const token = urlParams.get('token');
            const email = urlParams.get('email');

            // Kiểm tra token và email
            if (!token || !email) {
                resetPasswordForm.classList.add('hidden');
                invalidTokenAlert.classList.remove('hidden');
            }

            // Xử lý hiện/ẩn mật khẩu
            const togglePasswordButtons = document.querySelectorAll('.toggle-password');
            togglePasswordButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.parentElement.querySelector('input');
                    const eyeIcon = this.querySelector('.eye-icon');
                    const eyeOffIcon = this.querySelector('.eye-off-icon');

                    if (input.type === 'password') {
                        input.type = 'text';
                        eyeIcon.classList.add('hidden');
                        eyeOffIcon.classList.remove('hidden');
                    } else {
                        input.type = 'password';
                        eyeIcon.classList.remove('hidden');
                        eyeOffIcon.classList.add('hidden');
                    }
                });
            });

            // Hàm hiển thị thông báo
            function showToast(title, description, type = 'success') {
                const toast = document.getElementById('toast');
                const toastTitle = document.getElementById('toastTitle');
                const toastDescription = document.getElementById('toastDescription');
                const successIcon = toast.querySelector('.toast-icon.success');
                const errorIcon = toast.querySelector('.toast-icon.error');

                toastTitle.textContent = title;
                toastDescription.textContent = description;

                if (type === 'error') {
                    successIcon.classList.add('hidden');
                    errorIcon.classList.remove('hidden');
                    toast.classList.add('error');
                    toast.classList.remove('success');
                } else {
                    successIcon.classList.remove('hidden');
                    errorIcon.classList.add('hidden');
                    toast.classList.add('success');
                    toast.classList.remove('error');
                }

                toast.classList.remove('hidden');

                // Tự động ẩn toast sau 3 giây
                setTimeout(function() {
                    toast.classList.add('hidden');
                }, 3000);
            }
        });
    </script>
</body>

</html>
