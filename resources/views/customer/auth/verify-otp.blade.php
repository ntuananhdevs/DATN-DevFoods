@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Xác Thực OTP')

@section('content')
<style>
    .container {
      max-width: 1280px;
      margin: 0 auto;
   }

   button:disabled, button[disabled] {
    background-color: #fbbf24 !important; /* màu cam nhạt hơn */
    color: #fff !important;
    opacity: 0.6 !important;
    cursor: not-allowed !important;
    border: none !important;
}
</style>
<div class="min-h-screen flex flex-col items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">FastFood</h1>
            <p class="text-orange-500 font-medium">Xác thực tài khoản</p>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-center mb-2 text-gray-900">Xác Thực OTP</h2>
                <p class="text-center text-gray-500 text-sm mb-6">
                    Chúng tôi đã gửi mã OTP đến email của bạn. Vui lòng nhập mã để xác thực tài khoản.
                </p>
                
                <!-- OTP Form -->
                <form id="otpForm" class="space-y-6" action="{{ route('customer.verify.otp.post') }}" method="POST">
                    @csrf
                    <input type="hidden" name="email" id="emailField" value="{{ $email ?? '' }}">
                    <input type="hidden" name="otp" id="otpValue">
                    
                    @error('otp')
                        <div class="text-red-500 text-sm text-center">{{ $message }}</div>
                    @enderror
                    
                    <div class="flex justify-center">
                        <div class="flex gap-2" id="otpInputs">
                            <input type="text" maxlength="1" class="w-10 h-12 text-center text-lg border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900" data-index="0">
                            <input type="text" maxlength="1" class="w-10 h-12 text-center text-lg border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900" data-index="1">
                            <input type="text" maxlength="1" class="w-10 h-12 text-center text-lg border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900" data-index="2">
                            <input type="text" maxlength="1" class="w-10 h-12 text-center text-lg border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900" data-index="3">
                            <input type="text" maxlength="1" class="w-10 h-12 text-center text-lg border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900" data-index="4">
                            <input type="text" maxlength="1" class="w-10 h-12 text-center text-lg border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900" data-index="5">
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                        id="verifyBtn"
                        disabled
                    >
                        <span id="verifyBtnText">Xác thực</span>
                        <span id="verifyBtnLoading" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Đang xử lý...
                        </span>
                    </button>
                </form>

                <div class="text-center mt-6">
                    <p class="text-sm text-gray-500">
                        Không nhận được mã?
                        <span id="timerText">Gửi lại sau <span id="countdown">60</span> giây</span>
                        <button
                            id="resendBtn"
                            class="text-orange-500 hover:text-orange-600 font-medium focus:outline-none hidden"
                        >
                            <span id="resendBtnText">Gửi lại mã</span>
                            <span id="resendBtnLoading" class="hidden">
                                <i class="fas fa-spinner fa-spin mr-1"></i>
                                Đang gửi lại
                            </span>
                        </button>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const otpInputs = document.querySelectorAll('#otpInputs input');
    const otpForm = document.getElementById('otpForm');
    const otpValue = document.getElementById('otpValue');
    const emailField = document.getElementById('emailField');
    const verifyBtn = document.getElementById('verifyBtn');
    const verifyBtnText = document.getElementById('verifyBtnText');
    const verifyBtnLoading = document.getElementById('verifyBtnLoading');
    const resendBtn = document.getElementById('resendBtn');
    const resendBtnText = document.getElementById('resendBtnText');
    const resendBtnLoading = document.getElementById('resendBtnLoading');
    const timerText = document.getElementById('timerText');
    const countdown = document.getElementById('countdown');
    
    // Tạo key lưu trữ duy nhất cho từng email
    const resendCooldownKey = 'otpResendDeadline_' + (emailField.value || 'default');
    let timerInterval;

    // Set email from URL parameter or session
    const urlParams = new URLSearchParams(window.location.search);
    const emailFromUrl = urlParams.get('email');
    if (emailFromUrl) {
        emailField.value = emailFromUrl;
    }

    // Focus first input
    otpInputs[0].focus();

    // OTP input handling
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            const value = e.target.value;
            
            // Only allow numbers
            if (value && !/^\d+$/.test(value)) {
                e.target.value = '';
                return;
            }

            // Move to next input
            if (value && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }

            // Check if all inputs are filled
            checkOtpComplete();
        });

        input.addEventListener('keydown', function(e) {
            // Move to previous input on backspace
            if (e.key === 'Backspace' && !input.value && index > 0) {
                otpInputs[index - 1].focus();
            }
        });

        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text/plain').trim();
            
            if (/^\d{6}$/.test(pastedData)) {
                const digits = pastedData.split('');
                otpInputs.forEach((inp, i) => {
                    inp.value = digits[i] || '';
                });
                otpInputs[5].focus();
                checkOtpComplete();
            }
        });
    });

    function checkOtpComplete() {
        const allFilled = Array.from(otpInputs).every(input => input.value !== '');
        verifyBtn.disabled = !allFilled;
        
        if (allFilled) {
            // Set the combined OTP value to the hidden input
            otpValue.value = Array.from(otpInputs).map(input => input.value).join('');
        }
    }

    // Hàm quản lý trạng thái và hiển thị của bộ đếm ngược
    function manageResendCooldown() {
        const deadline = localStorage.getItem(resendCooldownKey);

        if (deadline && Date.now() < deadline) {
            // Nếu đang trong thời gian chờ
            timerText.classList.remove('hidden');
            resendBtn.classList.add('hidden');
            
            if (timerInterval) clearInterval(timerInterval); // Xóa bộ đếm cũ nếu có

            timerInterval = setInterval(() => {
                const remaining = deadline - Date.now();
                if (remaining <= 0) {
                    clearInterval(timerInterval);
                    timerText.classList.add('hidden');
                    resendBtn.classList.remove('hidden');
                    localStorage.removeItem(resendCooldownKey);
                } else {
                    countdown.textContent = Math.ceil(remaining / 1000);
                }
            }, 1000);
        } else {
            // Nếu không trong thời gian chờ (hoặc đã hết hạn)
            timerText.classList.add('hidden');
            resendBtn.classList.remove('hidden');
            localStorage.removeItem(resendCooldownKey); // Dọn dẹp key đã hết hạn
        }
    }

    // Form submission
    otpForm.addEventListener('submit', function(e) {
        verifyBtn.disabled = true;
        verifyBtnText.classList.add('hidden');
        verifyBtnLoading.classList.remove('hidden');
    });

    // Xử lý khi bấm nút "Gửi lại OTP"
    resendBtn.addEventListener('click', function() {
        resendBtnText.classList.add('hidden');
        resendBtnLoading.classList.remove('hidden');
        resendBtn.disabled = true;
        
        fetch('{{ route("customer.resend.otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                email: emailField.value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Đặt mốc hết hạn mới và bắt đầu đếm ngược
                localStorage.setItem(resendCooldownKey, Date.now() + 60000);
                manageResendCooldown();
            } else {
                // Failure: show error and re-enable button
                alert(data.message || 'Không thể gửi lại OTP. Vui lòng thử lại sau.');
                resendBtnText.classList.remove('hidden');
                resendBtnLoading.classList.add('hidden');
                resendBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Đã xảy ra lỗi khi gửi lại OTP. Vui lòng kiểm tra kết nối và thử lại.');
            resendBtnText.classList.remove('hidden');
            resendBtnLoading.classList.add('hidden');
            resendBtn.disabled = false;
        });
    });

    // Gọi hàm quản lý khi trang được tải lần đầu
    manageResendCooldown();

    // Sau khi DOMContentLoaded
    const otpErrorDiv = document.querySelector('.text-red-500.text-sm.text-center');
    if (otpErrorDiv && otpErrorDiv.textContent.includes('Vui lòng thử lại sau')) {
        let minutes = 1;
        const match = otpErrorDiv.textContent.match(/sau (\d+) phút/);
        if (match) {
            minutes = parseInt(match[1]);
        } else if (otpErrorDiv.textContent.match(/sau 1 phút/)) {
            minutes = 1;
        } else if (otpErrorDiv.textContent.match(/sau 3 phút/)) {
            minutes = 3;
        }
        let seconds = minutes * 60;
        verifyBtn.disabled = true;
        otpErrorDiv.innerHTML += `<br><span id="otp-timer">(${seconds}s)</span>`;
        const timerSpan = document.getElementById('otp-timer');
        const timerInterval = setInterval(() => {
            seconds--;
            timerSpan.textContent = `(${seconds}s)`;
            if (seconds <= 0) {
                clearInterval(timerInterval);
                // AJAX check lock status
                fetch('{{ route("customer.check.otp.lock") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email: emailField.value })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.locked) {
                        verifyBtn.disabled = false;
                        timerSpan.textContent = '';
                    } else {
                        otpErrorDiv.textContent = data.message;
                    }
                });
            }
        }, 1000);
    }
});
</script>
@endsection
