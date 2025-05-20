<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <link rel="stylesheet" href="{{ asset('css/drivers/Auth.css') }}">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <a href="index.html" class="back-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <div class="alert-content">
                    <h4>Mã OTP đã được gửi!</h4>
                    <p>Chúng tôi đã gửi mã xác thực (OTP) đến email <span id="emailSent"></span>. Vui lòng kiểm tra hộp thư của bạn và nhập mã để tiếp tục.</p>
                </div>
            </div>
            
            <form id="forgotPasswordForm" method="POST" action="{{ route('driver.send_otp') }}" class="form">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" placeholder="Nhập email đăng ký của bạn" required>
                </div>
                
                <button type="submit" class="btn btn-primary" id="sendOtpButton">
                    <span class="btn-text">Gửi mã xác thực OTP</span>
                    <span class="btn-loading hidden">
                        <svg class="spinner" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="4"></circle>
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
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forgotPasswordForm = document.getElementById('forgotPasswordForm');
            const emailInput = document.getElementById('email');
            const sendOtpButton = document.getElementById('sendOtpButton');
            const successAlert = document.getElementById('successAlert');
            const emailSent = document.getElementById('emailSent');
            
            forgotPasswordForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const email = emailInput.value.trim();
                if (!email) return;
                
                // Hiển thị trạng thái loading
                sendOtpButton.querySelector('.btn-text').classList.add('hidden');
                sendOtpButton.querySelector('.btn-loading').classList.remove('hidden');
                sendOtpButton.disabled = true;
                
                // Gửi yêu cầu OTP thực tế
                // Thêm kiểm tra null
                const element = document.querySelector('#element');
                if(element) {
                  // Thao tác với element
                }
                
                // Sửa lại fetch API
                fetch('/driver/forgot-password', {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                  },
                  body: JSON.stringify({email: email, _token: document.querySelector('meta[name="csrf-token"]').content})
                })
                .then(response => {
                  if(!response.ok) throw new Error('Network error');
                  return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Hiển thị thông báo thành công
                        emailSent.textContent = email;
                        successAlert.classList.remove('hidden');
                        forgotPasswordForm.classList.add('hidden');
                        
                        // Chuyển hướng đến trang xác thực OTP sau 2 giây
                        setTimeout(function() {
                            window.location.href = '/driver/verify-otp?email=' + encodeURIComponent(email);
                        }, 2000);
                    } else {
                        // Hiển thị thông báo lỗi
                        showToast('Lỗi', data.message || 'Gửi OTP thất bại', 'error');
                        sendOtpButton.querySelector('.btn-text').classList.remove('hidden');
                        sendOtpButton.querySelector('.btn-loading').classList.add('hidden');
                        sendOtpButton.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error details:', error);
                    showToast('Lỗi', error.message || 'Có lỗi xảy ra khi gửi OTP', 'error');
                    sendOtpButton.querySelector('.btn-text').classList.remove('hidden');
                    sendOtpButton.querySelector('.btn-loading').classList.add('hidden');
                    sendOtpButton.disabled = false;
                });
            });
        });
    </script> --}}
</body>
</html>