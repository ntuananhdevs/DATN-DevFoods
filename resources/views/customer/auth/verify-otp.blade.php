@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Xác Thực OTP')

@section('content')
<style>
    .container {
      max-width: 1280px;
      margin: 0 auto;
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
    const verifyBtn = document.getElementById('verifyBtn');
    const verifyBtnText = document.getElementById('verifyBtnText');
    const verifyBtnLoading = document.getElementById('verifyBtnLoading');
    const resendBtn = document.getElementById('resendBtn');
    const resendBtnText = document.getElementById('resendBtnText');
    const resendBtnLoading = document.getElementById('resendBtnLoading');
    const timerText = document.getElementById('timerText');
    const countdown = document.getElementById('countdown');
    
    let timer = 60;
    let timerInterval;

    // Focus first input
    otpInputs[0].focus();

    // Start countdown
    startCountdown();

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

    function startCountdown() {
        timerInterval = setInterval(() => {
            timer--;
            countdown.textContent = timer;
            
            if (timer <= 0) {
                clearInterval(timerInterval);
                timerText.classList.add('hidden');
                resendBtn.classList.remove('hidden');
            }
        }, 1000);
    }

    // Form submission
    otpForm.addEventListener('submit', function(e) {
        verifyBtn.disabled = true;
        verifyBtnText.classList.add('hidden');
        verifyBtnLoading.classList.remove('hidden');
    });

    // Resend OTP
    resendBtn.addEventListener('click', function() {
        resendBtnText.classList.add('hidden');
        resendBtnLoading.classList.remove('hidden');
        resendBtn.disabled = true;
        
        // Send AJAX request to resend OTP
        fetch('{{ route("customer.resend.otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            resendBtnText.classList.remove('hidden');
            resendBtnLoading.classList.add('hidden');
            resendBtn.disabled = false;
            resendBtn.classList.add('hidden');
            timerText.classList.remove('hidden');
            
            // Reset timer
            timer = 60;
            countdown.textContent = timer;
            startCountdown();
        })
        .catch(error => {
            console.error('Error:', error);
            resendBtnText.classList.remove('hidden');
            resendBtnLoading.classList.add('hidden');
            resendBtn.disabled = false;
        });
    });
});
</script>
@endsection
