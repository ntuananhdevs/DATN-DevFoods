@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Quên Mật Khẩu')

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
            <p class="text-orange-500 font-medium">Quên mật khẩu</p>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-center mb-2 text-gray-900">Quên Mật Khẩu</h2>
                <p class="text-center text-gray-500 text-sm mb-6">
                    Nhập email của bạn và chúng tôi sẽ gửi hướng dẫn đặt lại mật khẩu
                </p>
                
                <!-- Success Message -->
                <div id="successMessage" class="bg-green-50 border border-green-200 rounded-md p-4 mb-4 hidden">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-800">
                            Hãy kiểm tra hộp thư email <span id="emailSent"></span>, bạn sẽ nhận được hướng dẫn đặt lại mật khẩu. Vui lòng kiểm tra hộp thư của bạn.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Forgot Password Form -->
                <form id="forgotPasswordForm" class="space-y-4" action="{{ route('customer.password.email') }}" method="POST">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900"
                            placeholder="name@example.com"
                            value="{{ old('email') }}"
                        />
                        @error('email')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @else
                            <div class="text-red-500 text-sm mt-1 hidden" id="emailError"></div>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                        id="submitBtn"
                    >
                        <span id="submitBtnText">Gửi hướng dẫn đặt lại</span>
                        <span id="submitBtnLoading" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Đang xử lý...
                        </span>
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('customer.login') }}" class="text-orange-500 hover:text-orange-600 text-sm underline underline-offset-2">
                Quay lại đăng nhập
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitBtnText = document.getElementById('submitBtnText');
    const submitBtnLoading = document.getElementById('submitBtnLoading');
    
    // Hiển thị thông báo thành công nếu có từ session
    @if(session('status'))
        document.getElementById('emailSent').textContent = "{{ old('email') }}";
        document.getElementById('successMessage').classList.remove('hidden');
        document.getElementById('forgotPasswordForm').classList.add('hidden');
    @endif

    forgotPasswordForm.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtnText.classList.add('hidden');
        submitBtnLoading.classList.remove('hidden');
    });
});
</script>
@endsection
