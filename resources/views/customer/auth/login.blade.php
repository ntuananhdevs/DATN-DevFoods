@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Đăng Nhập')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<style>
    .container {
      max-width: 1280px;
      margin: 0 auto;
   }
</style>
<div class="min-h-screen flex flex-col items-center justify-center px-4">
    <div class="w-full max-w-lg">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900">FastFood</h1>
            <p class="text-orange-500 font-medium">Đăng nhập tài khoản</p>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <!-- Login Form -->
                <form id="loginForm" class="space-y-4" action="{{ route('customer.login.post') }}" method="POST">
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
                            placeholder="example@email.com"
                            value="{{ old('email') }}"
                        />
                        @error('email')
                            <div class="text-red-500 text-sm mt-1" id="emailError">{{ $message }}</div>
                        @else
                            <div class="text-red-500 text-sm mt-1 hidden" id="emailError"></div>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Mật khẩu
                            </label>
                            <a href="{{ route('customer.password.request') }}" class="text-sm text-orange-500 hover:text-orange-600">
                                Quên mật khẩu?
                            </a>
                        </div>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900"
                                placeholder="••••••••"
                            />
                            <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-red-500 text-sm mt-1" id="passwordError">{{ $message }}</div>
                        @else
                            <div class="text-red-500 text-sm mt-1 hidden" id="passwordError"></div>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            class="h-4 w-4 text-orange-500 focus:ring-orange-500 border-gray-300 rounded"
                        />
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                        id="loginBtn"
                    >
                        <span id="loginBtnText">Đăng nhập</span>
                        <span id="loginBtnLoading" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Đang xử lý...
                        </span>
                    </button>
                </form>

                <div class="relative mt-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-white px-2 text-gray-500">Hoặc tiếp tục với</span>
                    </div>
                </div>

                <div class="mt-6">
                    <button
                        type="button"
                        id="googleLoginBtn"
                        class="w-full flex justify-center items-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <i class="fab fa-google text-red-500 mr-2"></i>
                        <span id="googleBtnText">Đăng nhập với Google</span>
                        <span id="googleBtnLoading" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Đang xử lý...
                        </span>
                    </button>
                </div>

                <div class="text-center text-sm mt-6">
                    Chưa có tài khoản?
                    <a href="{{ route('customer.register') }}" class="text-orange-500 hover:text-orange-600 font-medium">
                        Đăng ký ngay
                    </a>
                </div>
            </div>
        </div>

        <p class="mt-6 text-center text-sm text-gray-500">
            Bằng cách tiếp tục, bạn đồng ý với
            <a href="/terms" class="text-orange-500 hover:text-orange-600 underline underline-offset-2">
                Điều khoản dịch vụ
            </a>
            và
            <a href="/privacy" class="text-orange-500 hover:text-orange-600 underline underline-offset-2">
                Chính sách bảo mật
            </a>
            của chúng tôi.
        </p>
    </div>
</div>
@endsection

@section('scripts')
<!-- Firebase CDN -->
<script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-auth-compat.js"></script>
<!-- Firebase Config -->
<script src="{{ asset('js/firebase-config.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    const loginBtnText = document.getElementById('loginBtnText');
    const loginBtnLoading = document.getElementById('loginBtnLoading');
    const togglePasswordButton = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    const googleLoginBtn = document.getElementById('googleLoginBtn');
    const googleBtnText = document.getElementById('googleBtnText');
    const googleBtnLoading = document.getElementById('googleBtnLoading');

    // Toggle password visibility
    if (togglePasswordButton && passwordInput) {
        togglePasswordButton.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon
            const icon = this.querySelector('i');
            if (type === 'text') {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }

    // Form submission handling
    loginForm.addEventListener('submit', function(e) {
        // Clear previous errors
        document.getElementById('emailError').classList.add('hidden');
        document.getElementById('passwordError').classList.add('hidden');
        
        // Show loading state
        loginBtn.disabled = true;
        loginBtnText.classList.add('hidden');
        loginBtnLoading.classList.remove('hidden');
    });

    // Google Login Button Handler
    if (googleLoginBtn) {
        googleLoginBtn.addEventListener('click', async function() {
            // Show loading state
            googleLoginBtn.disabled = true;
            googleBtnText.classList.add('hidden');
            googleBtnLoading.classList.remove('hidden');

            try {
                await handleGoogleLogin();
            } catch (error) {
                console.error('Google login error:', error);
                alert('Đã xảy ra lỗi trong quá trình đăng nhập Google');
            } finally {
                // Reset button state
                googleLoginBtn.disabled = false;
                googleBtnText.classList.remove('hidden');
                googleBtnLoading.classList.add('hidden');
            }
        });
    }
});
</script>
@endsection