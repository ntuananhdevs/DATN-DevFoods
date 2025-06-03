<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực mã OTP</title>
    <link rel="stylesheet" href="{{ asset('css/drivers/Auth.css') }}">
</head>
<style>
    .otp-input.is-invalid {
        border: 2px solid #f44336;
        background-color: #ffe6e6;
    }

    .error-message {
        color: #f44336;
        margin-top: 8px;
        font-size: 14px;
        text-align: center;
    }
</style>

<body>
    <div class="container">
        <div class="login-container">
            <a href="{{ route('driver.forgot_password') }}" class="back-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m12 19-7-7 7-7"></path>
                    <path d="M19 12H5"></path>
                </svg>
                Quay lại
            </a>

            <div class="header">
                <h1>Xác thực mã OTP</h1>
                <p>Nhập mã xác thực 6 chữ số đã được gửi đến email của bạn</p>
            </div>

            <form id="otpVerificationForm" method="POST" action="{{ route('driver.verify_otp.submit') }}"
                class="form">
                @csrf
                <input type="hidden" name="driver_id" value="{{ $driver_id }}">
                <input type="hidden" name="otp" id="otpInput">

                <div class="otp-container">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="text" name="otp_digits[]" class="otp-input @error('otp') is-invalid @enderror"
                            maxlength="1" inputmode="numeric">
                    @endfor
                </div>
                @error('otp')
                    <p class="error-message">{{ $message }}</p>
                @enderror

                <p class="resend-text">
                    Không nhận được mã?
                    <button type="button" id="resendOtpButton" class="resend-button">Gửi lại</button>
                </p>

                <button type="submit" class="btn btn-primary" id="verifyOtpButton">
                    <span class="btn-text">Xác nhận</span>
                    <span class="btn-loading hidden">
                        <svg class="spinner" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor"
                                stroke-width="4"></circle>
                        </svg>
                        Đang xác thực...
                    </span>
                </button>
            </form>
        </div>
    </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (session('toast'))
                showToast('{{ session('toast.title') }}', '{{ session('toast.message') }}', '{{ session('toast.type') }}');
            @endif
    
            const otpInputs = document.querySelectorAll('.otp-input');
            const otpHiddenInput = document.getElementById('otpInput');
            const otpForm = document.getElementById('otpVerificationForm');
            const verifyBtn = document.getElementById('verifyOtpButton');
            const resendBtn = document.getElementById('resendOtpButton');
    
            const RESEND_INTERVAL = 60; // giây
            const STORAGE_KEY = 'lastOtpSentTime';
    
            otpInputs[0].focus();
    
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    if (!/^\d$/.test(e.target.value)) {
                        e.target.value = '';
                        return;
                    }
                    if (index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                    otpInputs.forEach(inp => inp.classList.remove('is-invalid'));
                });
    
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });
            });
    
            otpInputs[0].addEventListener('paste', (e) => {
                e.preventDefault();
                const pasted = e.clipboardData.getData('text').replace(/\D/g, '');
                pasted.split('').forEach((char, i) => {
                    if (i < otpInputs.length) otpInputs[i].value = char;
                });
            });
    
            otpForm.addEventListener('submit', function (e) {
                let otp = '';
                otpInputs.forEach(input => otp += input.value);
    
                if (otp.length !== 6) {
                    e.preventDefault();
                    showToast('Mã OTP không hợp lệ', 'Vui lòng nhập đầy đủ 6 chữ số', 'error');
                    return;
                }
    
                otpHiddenInput.value = otp;
                verifyBtn.querySelector('.btn-text').classList.add('hidden');
                verifyBtn.querySelector('.btn-loading').classList.remove('hidden');
                verifyBtn.disabled = true;
            });
    
            // ======= Countdown xử lý reload ========
            const lastSentTime = localStorage.getItem(STORAGE_KEY);
            if (lastSentTime) {
                const elapsed = Math.floor((Date.now() - parseInt(lastSentTime)) / 1000);
                if (elapsed < RESEND_INTERVAL) {
                    startCountdown(RESEND_INTERVAL - elapsed);
                }
            }
    
            resendBtn.addEventListener('click', function () {
                resendBtn.disabled = true;
                localStorage.setItem(STORAGE_KEY, Date.now().toString());
                startCountdown(RESEND_INTERVAL);
    
                fetch('{{ route('driver.resend_otp') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        driver_id: {{ $driver_id }}
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('Đã gửi lại mã OTP', 'Vui lòng kiểm tra email', 'success');
                        } else {
                            showToast('Lỗi', data.message || 'Không thể gửi lại mã OTP', 'error');
                        }
                    })
                    .catch(() => {
                        showToast('Lỗi', 'Không thể kết nối tới máy chủ.', 'error');
                    });
            });
    
            function startCountdown(seconds) {
                resendBtn.disabled = true;
                let countdown = seconds;
                resendBtn.textContent = `Gửi lại (${countdown}s)`;
    
                const timer = setInterval(() => {
                    countdown--;
                    resendBtn.textContent = `Gửi lại (${countdown}s)`;
                    if (countdown <= 0) {
                        clearInterval(timer);
                        resendBtn.disabled = false;
                        resendBtn.textContent = 'Gửi lại';
                        localStorage.removeItem(STORAGE_KEY);
                    }
                }, 1000);
            }
    
            function showToast(title, desc, type) {
                const toast = document.getElementById('toast');
                toast.querySelector('#toastTitle').textContent = title;
                toast.querySelector('#toastDescription').textContent = desc;
    
                toast.querySelector('.toast-icon.success').classList.toggle('hidden', type !== 'success');
                toast.querySelector('.toast-icon.error').classList.toggle('hidden', type !== 'error');
                toast.classList.remove('hidden');
                toast.classList.toggle('error', type === 'error');
                toast.classList.toggle('success', type === 'success');
    
                setTimeout(() => toast.classList.add('hidden'), 3000);
            }
        });
    </script>    
</body>

</html>
