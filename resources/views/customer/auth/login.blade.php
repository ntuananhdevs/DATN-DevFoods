@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Đăng Nhập')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-red-500 py-4 px-6">
                <h1 class="text-2xl font-bold text-white text-center">Đăng Nhập</h1>
            </div>
            
            <div class="p-6">
                <div class="mb-6">
                    <p class="text-gray-600 text-center">
                        Đăng nhập để theo dõi đơn hàng, lưu địa chỉ giao hàng và nhận các ưu đãi đặc biệt
                    </p>
                </div>
                
                <!-- Social Login Buttons -->
                <div class="space-y-3 mb-6">
                    <button class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md transition-colors">
                        <i class="fab fa-facebook-f"></i>
                        <span>Đăng nhập với Facebook</span>
                    </button>
                    <button class="w-full flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md transition-colors">
                        <i class="fab fa-google"></i>
                        <span>Đăng nhập với Google</span>
                    </button>
                </div>
                
                <div class="relative flex items-center justify-center mb-6">
                    <div class="border-t border-gray-300 flex-grow"></div>
                    <span class="mx-4 text-gray-500 text-sm">Hoặc đăng nhập với email</span>
                    <div class="border-t border-gray-300 flex-grow"></div>
                </div>
                
                <!-- Login Form -->
                <form id="login-form" class="space-y-4" method="POST" action="">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input type="email" id="email" name="email" placeholder="Nhập email của bạn" 
                                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 @error('email') border-red-500 @enderror" 
                                required autocomplete="email" autofocus>
                        </div>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Mật khẩu
                            </label>
                            <a href="" class="text-sm text-orange-600 hover:text-orange-500">
                                Quên mật khẩu?
                            </a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="password" name="password" placeholder="Nhập mật khẩu của bạn" 
                                class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 @error('password') border-red-500 @enderror" 
                                required autocomplete="current-password">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button type="button" id="toggle-password" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-orange-500 focus:ring-orange-500 border-gray-300 rounded" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>
                    
                    <div>
                        <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-md font-medium transition-colors flex items-center justify-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Đăng Nhập
                        </button>
                    </div>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-gray-600">
                        Chưa có tài khoản? 
                        <a href="{{ asset('/register') }}" class="text-orange-600 hover:text-orange-500 font-medium">
                            Đăng ký ngay
                        </a>
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
    const togglePasswordButton = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    
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
});
</script>
@endsection