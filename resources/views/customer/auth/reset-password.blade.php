@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Đặt Lại Mật Khẩu')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">FastFood</h1>
            <p class="text-orange-500 font-medium">Đặt lại mật khẩu</p>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-center mb-2 text-gray-900">Đặt Lại Mật Khẩu</h2>
                <p class="text-center text-gray-500 text-sm mb-6">Tạo mật khẩu mới cho tài khoản của bạn</p>
                
                <!-- Reset Password Form -->
                <form id="resetPasswordForm" class="space-y-4" action="{{ route('customer.password.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Mật khẩu mới
                        </label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="new-password"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900"
                            placeholder="••••••••"
                        />
                        @error('password')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @else
                            <div class="text-red-500 text-sm mt-1 hidden" id="passwordError"></div>
                        @enderror
                    </div>
                
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                            Xác nhận mật khẩu
                        </label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900"
                            placeholder="••••••••"
                        />
                        @error('password_confirmation')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @else
                            <div class="text-red-500 text-sm mt-1 hidden" id="confirmPasswordError"></div>
                        @enderror
                    </div>
                
                    <button
                        type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                        id="resetBtn"
                    >
                        <span id="resetBtnText">Đặt lại mật khẩu</span>
                        <span id="resetBtnLoading" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Đang xử lý...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const resetPasswordForm = document.getElementById('resetPasswordForm');
    const resetBtn = document.getElementById('resetBtn');
    const resetBtnText = document.getElementById('resetBtnText');
    const resetBtnLoading = document.getElementById('resetBtnLoading');

    resetPasswordForm.addEventListener('submit', function(e) {
        // Kiểm tra mật khẩu trước khi submit
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        
        // Clear previous errors
        document.getElementById('passwordError').classList.add('hidden');
        document.getElementById('confirmPasswordError').classList.add('hidden');
        
        if (password !== confirmPassword) {
            e.preventDefault(); // Ngăn form submit nếu mật khẩu không khớp
            document.getElementById('confirmPasswordError').textContent = 'Mật khẩu không khớp';
            document.getElementById('confirmPasswordError').classList.remove('hidden');
            return;
        }
        
        // Hiển thị trạng thái loading
        resetBtn.disabled = true;
        resetBtnText.classList.add('hidden');
        resetBtnLoading.classList.remove('hidden');
    });
});
</script>
@endsection
