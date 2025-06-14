@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Bổ sung thông tin')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center px-4">
    <div class="w-full max-w-lg">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Hoàn tất đăng ký</h1>
            <p class="text-orange-500 font-medium">Vui lòng bổ sung số điện thoại để hoàn tất quá trình đăng nhập</p>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <form id="phoneForm" class="space-y-4">
                    @csrf
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Số điện thoại <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="phone"
                            name="phone"
                            type="tel"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900"
                            placeholder="0987654321"
                            pattern="0[0-9]{9}"
                            maxlength="10"
                        />
                        <div class="text-red-500 text-sm mt-1 hidden" id="phoneError"></div>
                        <div class="text-gray-500 text-xs mt-1">Nhập số điện thoại 10 số, bắt đầu bằng số 0</div>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        id="submitBtn"
                    >
                        <span id="submitBtnText">Hoàn tất</span>
                        <span id="submitBtnLoading" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Đang xử lý...
                        </span>
                    </button>
                </form>

                <div class="text-center text-sm mt-4 text-gray-500">
                    Số điện thoại sẽ được sử dụng để giao hàng và liên hệ khi cần thiết
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneForm = document.getElementById('phoneForm');
    const phoneInput = document.getElementById('phone');
    const phoneError = document.getElementById('phoneError');
    const submitBtn = document.getElementById('submitBtn');
    const submitBtnText = document.getElementById('submitBtnText');
    const submitBtnLoading = document.getElementById('submitBtnLoading');

    // Phone input formatting
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
        if (value.length > 10) {
            value = value.substring(0, 10);
        }
        e.target.value = value;
        
        // Clear error when user starts typing
        phoneError.classList.add('hidden');
        phoneError.textContent = '';
    });

    // Form submission
    phoneForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const phone = phoneInput.value.trim();
        
        // Reset error state
        phoneError.classList.add('hidden');
        phoneError.textContent = '';
        
        // Validate phone
        if (!phone) {
            showError('Vui lòng nhập số điện thoại');
            return;
        }
        
        if (!/^0\d{9}$/.test(phone)) {
            showError('Số điện thoại phải là 10 số và bắt đầu bằng số 0');
            return;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtnText.classList.add('hidden');
        submitBtnLoading.classList.remove('hidden');
        
        try {
            const response = await fetch('{{ route("customer.phone-required.post") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ phone: phone })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Redirect to home page
                window.location.href = '{{ route("home") }}';
            } else {
                if (data.errors && data.errors.phone) {
                    showError(data.errors.phone[0]);
                } else {
                    showError(data.message || 'Đã xảy ra lỗi. Vui lòng thử lại.');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showError('Đã xảy ra lỗi. Vui lòng thử lại.');
        } finally {
            // Reset button state
            submitBtn.disabled = false;
            submitBtnText.classList.remove('hidden');
            submitBtnLoading.classList.add('hidden');
        }
    });
    
    function showError(message) {
        phoneError.textContent = message;
        phoneError.classList.remove('hidden');
    }
});
</script>
@endsection 