<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực mã OTP</title>
    <link rel="stylesheet" href="{{ asset('css/drivers/Auth.css') }}">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <a href="forgot-password.html" class="back-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m12 19-7-7 7-7"></path>
                    <path d="M19 12H5"></path>
                </svg>
                Quay lại
            </a>
            
            <div class="header">
                <h1>Xác thực mã OTP</h1>
                <p>Nhập mã xác thực 6 chữ số đã được gửi đến email của bạn</p>
            </div>
            
            <form id="otpVerificationForm" class="form">
                <div class="otp-container">
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" required>
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" required>
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" required>
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" required>
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" required>
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" required>
                </div>
                
                <p class="resend-text">
                    Không nhận được mã? 
                    <button type="button" id="resendOtpButton" class="resend-button">Gửi lại</button>
                </p>
                
                <button type="submit" class="btn btn-primary" id="verifyOtpButton">
                    <span class="btn-text">Xác nhận</span>
                    <span class="btn-loading hidden">
                        <svg class="spinner" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="4"></circle>
                        </svg>
                        Đang xác thực...
                    </span>
                </button>
            </form>
        </div>
    </div>

    <!-- Toast thông báo -->
    <div class="toast hidden" id="toast">
        <div class="toast-content">
            <div class="toast-icon success">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div class="toast-icon error hidden">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
            const otpVerificationForm = document.getElementById('otpVerificationForm');
            const otpInputs = document.querySelectorAll('.otp-input');
            const verifyOtpButton = document.getElementById('verifyOtpButton');
            const resendOtpButton = document.getElementById('resendOtpButton');
            
            // Lấy email từ URL
            const urlParams = new URLSearchParams(window.location.search);
            const email = urlParams.get('email') || '';
            
            // Focus vào ô đầu tiên
            otpInputs[0].focus();
            
            // Xử lý nhập OTP
            otpInputs.forEach((input, index) => {
                // Chỉ cho phép nhập số
                input.addEventListener('input', function(e) {
                    const value = e.target.value;
                    
                    // Chỉ giữ lại ký tự số
                    if (!/^\d*$/.test(value)) {
                        e.target.value = '';
                        return;
                    }
                    
                    // Nếu đã nhập, chuyển focus sang ô tiếp theo
                    if (value && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                });
                
                // Xử lý phím Backspace
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace') {
                        if (!e.target.value && index > 0) {
                            otpInputs[index - 1].focus();
                        }
                    }
                });
            });
            
            // Xử lý dán OTP
            otpInputs[0].addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text');
                
                // Chỉ xử lý nếu dữ liệu dán là số
                if (!/^\d+$/.test(pastedData)) return;
                
                // Điền vào các ô OTP
                const digits = pastedData.slice(0, 6).split('');
                digits.forEach((digit, index) => {
                    if (index < otpInputs.length) {
                        otpInputs[index].value = digit;
                    }
                });
                
                // Focus vào ô cuối cùng hoặc ô tiếp theo
                const focusIndex = Math.min(digits.length, otpInputs.length - 1);
                otpInputs[focusIndex].focus();
            });
            
            // Xử lý gửi form xác thực OTP
            otpVerificationForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Lấy mã OTP từ các ô input
                let otpValue = '';
                otpInputs.forEach(input => {
                    otpValue += input.value;
                });
                
                // Kiểm tra OTP đã đủ 6 chữ số chưa
                if (otpValue.length !== 6) {
                    showToast('Mã OTP không hợp lệ', 'Vui lòng nhập đầy đủ 6 chữ số', 'error');
                    return;
                }
                
                // Hiển thị trạng thái loading
                verifyOtpButton.querySelector('.btn-text').classList.add('hidden');
                verifyOtpButton.querySelector('.btn-loading').classList.remove('hidden');
                verifyOtpButton.disabled = true;
                
                // Giả lập xác thực OTP
                setTimeout(function() {
                    // Chuyển hướng đến trang đặt lại mật khẩu
                    window.location.href = 'reset-password.html?email=' + encodeURIComponent(email) + '&token=' + otpValue;
                }, 1000);
            });
            
            // Xử lý gửi lại OTP
            resendOtpButton.addEventListener('click', function() {
                if (!email) {
                    showToast('Không thể gửi lại mã', 'Email không hợp lệ, vui lòng thử lại', 'error');
                    return;
                }
                
                // Vô hiệu hóa nút gửi lại
                resendOtpButton.disabled = true;
                
                // Giả lập gửi lại OTP
                setTimeout(function() {
                    showToast('Đã gửi lại mã OTP', 'Vui lòng kiểm tra email của bạn', 'success');
                    resendOtpButton.disabled = false;
                }, 1000);
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