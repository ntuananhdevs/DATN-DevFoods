@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Đăng Ký')

@section('content')
<style>
    .container {
        max-width: 1280px;
        margin: 0 auto;
    }
</style>
<div class="min-h-screen flex flex-col items-center justify-center px-4">
    <div class="w-full max-w-xl">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900">FastFood</h1>
            <p class="text-orange-500 font-medium">Đăng ký tài khoản</p>
        </div>

        @if (session('status'))
        <div class="mt-4 text-center text-sm text-green-600">
            {{ session('status') }}
        </div>
        @endif

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
                            value="{{ old('full_name') }}" />
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
                            value="{{ old('email') }}" />
                        @error('email')
                        <div class="text-red-500 text-sm mt-1" id="emailError">{{ $message }}</div>
                        @else
                        <div class="text-red-500 text-sm mt-1 hidden" id="emailError"></div>
                        @endif
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
                            value="{{ old('phone') }}" />
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
                                placeholder="••••••••" />
                            <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                        <div class="text-red-500 text-sm mt-1" id="passwordError">{{ $message }}</div>
                        @else
                        <div class="text-red-500 text-sm mt-1 hidden" id="passwordError"></div>
                        @endif
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
                                placeholder="••••••••" />
                            <button type="button" id="toggle-password-confirm" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                        <div class="text-red-500 text-sm mt-1" id="confirmPasswordError">{{ $message }}</div>
                        @else
                        <div class="text-red-500 text-sm mt-1 hidden" id="confirmPasswordError"></div>
                        @endif
                    </div>

                    <!-- Cloudflare Turnstile CAPTCHA -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Xác minh captcha
                        </label>
                        <div class="cf-turnstile"
                            data-sitekey="{{ config('turnstile.site_key') }}"
                            data-theme="{{ config('turnstile.theme') }}"
                            data-size="{{ config('turnstile.size') }}"
                            data-callback="onTurnstileCallback">
                        </div>
                        @error('cf-turnstile-response')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input
                            id="terms"
                            name="terms"
                            type="checkbox"
                            required
                            class="h-4 w-4 text-orange-500 focus:ring-orange-500 border-gray-300 rounded" />
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
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        id="registerBtn"
                        disabled>
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

                <div class="mt-6">
                    <button
                        type="button"
                        id="googleRegisterBtn"
                        class="w-full flex justify-center items-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fab fa-google text-red-500 mr-2"></i>
                        <span id="googleRegBtnText">Đăng ký với Google</span>
                        <span id="googleRegBtnLoading" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Đang xử lý...
                        </span>
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

<!-- Cloudflare Turnstile Script -->
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endsection

@section('scripts')
<!-- Firebase CDN -->
<script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-auth-compat.js"></script>
<!-- Firebase Config -->
<script src="{{ asset('js/firebase-config.js') }}"></script>

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
        const emailError = document.getElementById('emailError');
        const passwordError = document.getElementById('passwordError');
        const confirmPasswordError = document.getElementById('confirmPasswordError');
        const googleRegisterBtn = document.getElementById('googleRegisterBtn');
        const googleRegBtnText = document.getElementById('googleRegBtnText');
        const googleRegBtnLoading = document.getElementById('googleRegBtnLoading');

        let turnstileToken = null;

        // Cloudflare Turnstile callback
        window.onTurnstileCallback = function(token) {
            turnstileToken = token;
            registerBtn.disabled = false;
            registerBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        };

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

        // Form submission handling with AJAX
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            // Clear previous errors
            document.getElementById('emailError').classList.add('hidden');
            document.getElementById('passwordError').classList.add('hidden');
            document.getElementById('confirmPasswordError').classList.add('hidden');

            // Check if Turnstile token exists
            if (!turnstileToken) {
                e.preventDefault();
                alert('Vui lòng hoàn thành xác minh bảo mật');
                return;
            }

            // Validate passwords match
            const password = passwordInput.value;
            const confirmPassword = passwordConfirmInput.value;

            if (password !== confirmPassword) {
                confirmPasswordError.textContent = 'Mật khẩu không khớp';
                confirmPasswordError.classList.remove('hidden');
                return;
            }

            // Show loading state
            registerBtn.disabled = true;
            registerBtnText.classList.add('hidden');
            registerBtnLoading.classList.remove('hidden');

            // Collect form data
            const formData = new FormData(registerForm);

            // Send AJAX request
            fetch(registerForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Reset button state
                    registerBtn.disabled = false;
                    registerBtnText.classList.remove('hidden');
                    registerBtnLoading.classList.add('hidden');

                    if (data.success) {
                        window.location.href = '{{ route("customer.verify.otp.show") }}?email=' + encodeURIComponent(document.getElementById('email').value);
                    } else {
                        // Handle validation errors
                        if (data.errors) {
                            if (data.errors.email) {
                                emailError.textContent = data.errors.email[0];
                                emailError.classList.remove('hidden');
                            }
                            if (data.errors.password) {
                                passwordError.textContent = data.errors.password[0];
                                passwordError.classList.remove('hidden');
                            }
                            if (data.errors.full_name) {
                                document.querySelector('#full_name ~ .text-red-500').textContent = data.errors.full_name[0];
                            }
                            if (data.errors.phone) {
                                document.querySelector('#phone ~ .text-red-500').textContent = data.errors.phone[0];
                            }
                        }
                    }
                })
                .catch(error => {
                    // Reset button state on error
                    registerBtn.disabled = false;
                    registerBtnText.classList.remove('hidden');
                    registerBtnLoading.classList.add('hidden');

                    console.error('Error:', error);
                    emailError.textContent = 'Đã xảy ra lỗi. Vui lòng thử lại.';
                    emailError.classList.remove('hidden');
                });
        });

        // Google Register Button Handler
        if (googleRegisterBtn) {
            googleRegisterBtn.addEventListener('click', async function() {
                // Show loading state
                googleRegisterBtn.disabled = true;
                googleRegBtnText.classList.add('hidden');
                googleRegBtnLoading.classList.remove('hidden');

                try {
                    await handleGoogleLogin();
                } catch (error) {
                    console.error('Google register error:', error);
                    alert('Đã xảy ra lỗi trong quá trình đăng ký Google');
                } finally {
                    // Reset button state
                    googleRegisterBtn.disabled = false;
                    googleRegBtnText.classList.remove('hidden');
                    googleRegBtnLoading.classList.add('hidden');
                }
            });
        }

        // Handle Turnstile error or expiration
        window.onTurnstileError = function() {
            turnstileToken = null;
            registerBtn.disabled = true;
            registerBtn.classList.add('opacity-50', 'cursor-not-allowed');
            alert('Xác minh bảo mật thất bại. Vui lòng thử lại.');
        };

        window.onTurnstileExpired = function() {
            turnstileToken = null;
            registerBtn.disabled = true;
            registerBtn.classList.add('opacity-50', 'cursor-not-allowed');
            alert('Xác minh bảo mật đã hết hạn. Vui lòng thực hiện lại.');
        };
    });
</script>
@endsection