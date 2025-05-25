@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Đăng Ký')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center px-4">
    <div class="w-full max-w-xl">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900">FastFood</h1>
            <p class="text-orange-500 font-medium">Đăng ký tài khoản</p>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <!-- Register Form -->
                <form id="registerForm" class="space-y-4" action="{{ route('customer.register.post') }}" method="POST">
                    @csrf
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Họ và tên
                        </label>
                        <input
                            id="full_name"
                            name="full_name"
                            type="text"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900"
                            placeholder="Nguyễn Văn A"
                            value="{{ old('full_name') }}"
                        />
                        @error('full_name')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

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
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Số điện thoại
                        </label>
                        <input
                            id="phone"
                            name="phone"
                            type="tel"
                            autocomplete="tel"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900"
                            placeholder="0912345678"
                            value="{{ old('phone') }}"
                        />
                        @error('phone')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Mật khẩu
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="new-password"
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

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                            Xác nhận mật khẩu
                        </label>
                        <div class="relative">
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900"
                                placeholder="••••••••"
                            />
                            <button type="button" id="toggle-password-confirm" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <div class="text-red-500 text-sm mt-1" id="confirmPasswordError">{{ $message }}</div>
                        @else
                            <div class="text-red-500 text-sm mt-1 hidden" id="confirmPasswordError"></div>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input
                            id="terms"
                            name="terms"
                            type="checkbox"
                            required
                            class="h-4 w-4 text-orange-500 focus:ring-orange-500 border-gray-300 rounded"
                        />
                        <label for="terms" class="ml-2 block text-sm text-gray-700">
                            Tôi đồng ý với
                            <a href="/terms" class="text-orange-500 hover:text-orange-600">
                                điều khoản
                            </a>
                            và
                            <a href="/privacy" class="text-orange-500 hover:text-orange-600">
                                chính sách bảo mật
                            </a>
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                        id="registerBtn"
                    >
                        <span id="registerBtnText">Đăng ký</span>
                        <span id="registerBtnLoading" class="hidden">
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
                        <span class="bg-white px-2 text-gray-500">Hoặc đăng ký với</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-6">
                    <button
                        type="button"
                        class="flex justify-center items-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                    >
                        <i class="fab fa-google text-red-500 mr-2"></i>
                        Google
                    </button>
                    <button
                        type="button"
                        class="flex justify-center items-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                    >
                        <i class="fab fa-facebook text-blue-600 mr-2"></i>
                        Facebook
                    </button>
                </div>
            </div>
        </div>

        <p class="mt-6 text-center text-sm text-gray-500">
            Đã có tài khoản?
            <a href="{{ route('customer.login') }}" class="text-orange-500 hover:text-orange-600 underline underline-offset-2">
                Đăng nhập
            </a>
        </p>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const registerBtn = document.getElementById('registerBtn');
    const registerBtnText = document.getElementById('registerBtnText');
    const registerBtnLoading = document.getElementById('registerBtnLoading');
    const togglePasswordButton = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    const togglePasswordConfirmButton = document.getElementById('toggle-password-confirm');
    const passwordConfirmInput = document.getElementById('password_confirmation');

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

    // Toggle confirm password visibility
    if (togglePasswordConfirmButton && passwordConfirmInput) {
        togglePasswordConfirmButton.addEventListener('click', function() {
            const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmInput.setAttribute('type', type);
            
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
    registerForm.addEventListener('submit', function(e) {
        // Clear previous errors
        document.getElementById('emailError').classList.add('hidden');
        document.getElementById('passwordError').classList.add('hidden');
        document.getElementById('confirmPasswordError').classList.add('hidden');
        
        // Validate passwords match
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        
        if (password !== confirmPassword) {
            e.preventDefault(); // Chỉ ngăn chặn khi mật khẩu không khớp
            document.getElementById('confirmPasswordError').textContent = 'Mật khẩu không khớp';
            document.getElementById('confirmPasswordError').classList.remove('hidden');
            return;
        }
        
        // Show loading state
        registerBtn.disabled = true;
        registerBtnText.classList.add('hidden');
        registerBtnLoading.classList.remove('hidden');
        
        // Form sẽ tự submit nếu không có e.preventDefault()
    });
});
</script>
@endsection