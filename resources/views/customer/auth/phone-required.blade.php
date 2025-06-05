@extends('layouts.customer.fullLayoutMaster')

@section('title', 'Cập nhật số điện thoại - FastFood')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-orange-100 rounded-full flex items-center justify-center">
                <i class="fas fa-phone text-orange-500 text-2xl"></i>
            </div>
            <h2 class="mt-6 text-3xl font-bold text-gray-900">
                Cập nhật số điện thoại
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Để hoàn tất quá trình đăng ký, vui lòng cung cấp số điện thoại của bạn
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-8">
            <form id="phone-form" class="space-y-6">
                @csrf
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Số điện thoại <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-gray-400"></i>
                        </div>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            class="appearance-none relative block w-full pl-10 pr-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500 focus:z-10 sm:text-sm" 
                            placeholder="Nhập số điện thoại của bạn"
                            required
                            pattern="[0-9]{10,11}"
                            maxlength="11"
                        >
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Ví dụ: 0901234567 hoặc 84901234567
                    </p>
                </div>

                <div id="error-message" class="hidden bg-red-50 border border-red-200 rounded-md p-3">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-red-400 flex-shrink-0 mt-0.5"></i>
                        <div class="ml-3">
                            <p class="text-sm text-red-800" id="error-text"></p>
                        </div>
                    </div>
                </div>

                <div>
                    <button 
                        type="submit" 
                        id="submit-btn"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-check text-orange-500 group-hover:text-orange-400" aria-hidden="true"></i>
                        </span>
                        <span id="btn-text">Cập nhật số điện thoại</span>
                        <div id="loading-spinner" class="hidden ml-2">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </button>
                </div>
            </form>
        </div>

        <div class="text-center">
            <p class="text-sm text-gray-600">
                Số điện thoại sẽ được sử dụng để liên hệ về đơn hàng của bạn
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('phone-form');
    const phoneInput = document.getElementById('phone');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const loadingSpinner = document.getElementById('loading-spinner');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');

    // Format phone number while typing
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
        if (value.length > 11) {
            value = value.substring(0, 11);
        }
        e.target.value = value;
        
        // Hide error when user starts typing
        hideError();
    });

    function showError(message) {
        errorText.textContent = message;
        errorMessage.classList.remove('hidden');
        phoneInput.classList.add('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
        phoneInput.classList.remove('border-gray-300', 'focus:border-orange-500', 'focus:ring-orange-500');
    }

    function hideError() {
        errorMessage.classList.add('hidden');
        phoneInput.classList.remove('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
        phoneInput.classList.add('border-gray-300', 'focus:border-orange-500', 'focus:ring-orange-500');
    }

    function setLoading(isLoading) {
        submitBtn.disabled = isLoading;
        if (isLoading) {
            btnText.textContent = 'Đang cập nhật...';
            loadingSpinner.classList.remove('hidden');
        } else {
            btnText.textContent = 'Cập nhật số điện thoại';
            loadingSpinner.classList.add('hidden');
        }
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const phone = phoneInput.value.trim();
        
        // Validate phone number
        if (!phone) {
            showError('Vui lòng nhập số điện thoại');
            return;
        }
        
        if (phone.length < 10 || phone.length > 11) {
            showError('Số điện thoại phải có 10-11 chữ số');
            return;
        }
        
        if (!phone.match(/^[0-9]+$/)) {
            showError('Số điện thoại chỉ được chứa các chữ số');
            return;
        }

        setLoading(true);
        hideError();

        // Send request
        fetch('{{ route("customer.update-phone") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                phone: phone
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message briefly then redirect
                btnText.textContent = 'Thành công!';
                setTimeout(() => {
                    window.location.href = '/';
                }, 1000);
            } else {
                setLoading(false);
                showError(data.message || 'Có lỗi xảy ra, vui lòng thử lại');
            }
        })
        .catch(error => {
            setLoading(false);
            console.error('Error:', error);
            showError('Có lỗi xảy ra, vui lòng thử lại');
        });
    });
});
</script>
@endsection 