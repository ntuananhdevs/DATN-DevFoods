@extends('layouts.driver.masterLayout')

@section('title', 'Thông tin cá nhân - Ứng dụng Tài xế')

@section('content')
<div class="p-4 md:p-6 space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Thông tin cá nhân</h1>

    <!-- Driver Profile -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <img src="https://via.placeholder.com/80x80" alt="Avatar" class="w-20 h-20 rounded-full border-4 border-blue-500">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $driver['name'] }}</h2>
                        <p class="text-gray-600">{{ $driver['phone'] }}</p>
                    </div>
                </div>
                <button id="editProfileBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Chỉnh sửa
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-4">
                <div class="flex items-center">
                    <span class="font-semibold text-gray-900">Trạng thái hoạt động</span>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" data-toggle="driver-status" {{ $driver['is_active'] ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <p class="text-sm text-gray-600">
                {{ $driver['is_active'] ? 'Bạn đang sẵn sàng nhận đơn hàng mới.' : 'Bạn đang nghỉ. Bật để tiếp tục nhận đơn.' }}
            </p>
        </div>
    </div>

    <!-- Personal Information -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Thông tin cá nhân
            </h3>
        </div>
        <div class="p-6">
            <form id="profileForm" class="space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Họ và tên</label>
                        <input type="text" id="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 form-input" value="{{ $driver['name'] }}" disabled>
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
                        <input type="tel" id="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 form-input" value="{{ $driver['phone'] }}" disabled>
                    </div>
                </div>
                <div>
                    <label for="idCard" class="block text-sm font-medium text-gray-700 mb-2">Số CCCD</label>
                    <input type="text" id="idCard" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" value="{{ $driver['id_card_number'] }}" disabled>
                    <p class="text-sm text-gray-500 mt-1">Thông tin này không thể thay đổi trực tiếp.</p>
                </div>
            </form>
        </div>
    </div>

    <!-- Vehicle Information -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Thông tin phương tiện
            </h3>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Phương tiện</p>
                    <p class="font-semibold text-gray-900">{{ $driver['vehicle'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Biển số xe</p>
                    <p class="font-semibold text-gray-900">{{ $driver['license_plate'] }}</p>
                </div>
            </div>
            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-blue-800">Để thay đổi thông tin phương tiện, vui lòng liên hệ hỗ trợ.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Account -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                Tài khoản ngân hàng
            </h3>
            <p class="text-sm text-gray-600">Dùng để nhận tiền ship hàng ngày/tuần.</p>
        </div>
        <div class="p-6">
            <form id="bankForm" class="space-y-4">
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label for="bankName" class="block text-sm font-medium text-gray-700 mb-2">Tên ngân hàng</label>
                        <input type="text" id="bankName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 form-input" value="{{ $driver['bank_account']['bank_name'] }}" disabled>
                    </div>
                    <div>
                        <label for="accountNumber" class="block text-sm font-medium text-gray-700 mb-2">Số tài khoản</label>
                        <input type="text" id="accountNumber" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 form-input" value="{{ $driver['bank_account']['account_number'] }}" disabled>
                    </div>
                    <div>
                        <label for="accountHolderName" class="block text-sm font-medium text-gray-700 mb-2">Chủ tài khoản</label>
                        <input type="text" id="accountHolderName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 form-input" value="{{ $driver['bank_account']['account_holder_name'] }}" disabled>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Save Changes Button (hidden by default) -->
    <button id="saveChangesBtn" class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors hidden flex items-center justify-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Lưu thay đổi
    </button>

    <!-- Logout Button -->
    <button class="w-full bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
        </svg>
        Đăng xuất
    </button>
</div>

@push('scripts')
<script>
document.getElementById('editProfileBtn').addEventListener('click', function() {
    const isEditing = this.textContent.includes('Chỉnh sửa');
    const inputs = document.querySelectorAll('#profileForm input:not(#idCard), #bankForm input');
    const saveBtn = document.getElementById('saveChangesBtn');
    
    if (isEditing) {
        // Enable editing
        inputs.forEach(input => {
            input.disabled = false;
            input.classList.remove('bg-gray-50');
        });
        this.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            Hủy
        `;
        this.className = 'bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors flex items-center';
        saveBtn.classList.remove('hidden');
    } else {
        // Disable editing
        inputs.forEach(input => {
            input.disabled = true;
            input.classList.add('bg-gray-50');
        });
        this.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Chỉnh sửa
        `;
        this.className = 'bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center';
        saveBtn.classList.add('hidden');
    }
});

document.getElementById('saveChangesBtn').addEventListener('click', function() {
    // Show loading state
    this.classList.add('btn-loading');
    
    // Simulate API call
    setTimeout(() => {
        window.driverApp.showToast('Thành công!', 'Thông tin đã được cập nhật.', 'success');
        
        // Reset edit mode
        const editBtn = document.getElementById('editProfileBtn');
        const inputs = document.querySelectorAll('#profileForm input:not(#idCard), #bankForm input');
        
        inputs.forEach(input => {
            input.disabled = true;
            input.classList.add('bg-gray-50');
        });
        editBtn.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Chỉnh sửa
        `;
        editBtn.className = 'bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center';
        this.classList.add('hidden');
        this.classList.remove('btn-loading');
    }, 1000);
});
</script>
@endpush
@endsection

