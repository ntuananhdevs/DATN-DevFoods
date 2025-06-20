@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Chỉnh Sửa Hồ Sơ')

@section('content')
<style>
    .container {
      max-width: 1280px;
      margin: 0 auto;
   }
</style>
<div class="bg-gradient-to-r from-orange-500 to-red-500 py-8">
    <div class="container mx-auto px-4">
        <div class="flex items-center">
            <a href="{{ route('customer.profile') }}" class="text-white hover:text-white/80 mr-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl md:text-3xl font-bold text-white">Chỉnh Sửa Hồ Sơ</h1>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8">
            <form id="edit-profile-form" action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PATCH')

                <div class="flex flex-col items-center mb-8">
                    <div class="relative mb-4">
                        <div class="w-32 h-32 rounded-full bg-gray-200 overflow-hidden border-4 border-white shadow-md">
                            {{-- LẤY AVATAR TỪ USER --}}
                            <img src="{{ Storage::disk('s3')->url($user->avatar ?? 'avatars/default.jpg') }}" alt="Ảnh đại diện" class="w-full h-full object-cover" id="avatar-preview">
                        </div>
                        <label for="avatar-upload" class="absolute bottom-0 right-0 bg-orange-500 hover:bg-orange-600 text-white rounded-full w-10 h-10 flex items-center justify-center shadow-md transition-colors cursor-pointer">
                            <i class="fas fa-camera"></i>
                            <input type="file" id="avatar-upload" name="avatar" class="hidden" accept="image/*">
                        </label>
                    </div>
                    <p class="text-sm text-gray-500">Nhấn vào biểu tượng máy ảnh để thay đổi ảnh đại diện</p>
                    <p class="text-xs text-gray-400 mt-1">Kích thước tối đa: 5MB. Định dạng: JPG, PNG</p>
                </div>

                <div class="mb-8">
                    <h2 class="text-xl font-bold mb-4 pb-2 border-b border-gray-100">Thông Tin Cá Nhân</h2>
                    
                    {{-- TÁCH HỌ VÀ TÊN TỪ full_name --}}
                    @php
                        $nameParts = explode(' ', $user->full_name, 2);
                        $firstName = $nameParts[0];
                        $lastName = $nameParts[1] ?? '';
                    @endphp

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium mb-2">Họ <span class="text-red-500">*</span></label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $firstName) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        
                        <div>
                            <label for="last_name" class="block text-sm font-medium mb-2">Tên <span class="text-red-500">*</span></label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $lastName) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium mb-2">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        
                        <div>
                            <label for="birthday" class="block text-sm font-medium mb-2">Ngày sinh</label>
                            {{-- Định dạng ngày tháng cho input type="date" --}}
                            <input type="date" id="birthday" name="birthday" value="{{ old('birthday', $user->birthday ? $user->birthday->format('Y-m-d') : '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        
                        <div>
                            <label for="gender" class="block text-sm font-medium mb-2">Giới tính</label>
                            <select id="gender" name="gender" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                {{-- Lựa chọn giới tính động --}}
                                <option value="male" @if($user->gender == 'male') selected @endif>Nam</option>
                                <option value="female" @if($user->gender == 'female') selected @endif>Nữ</option>
                                <option value="other" @if($user->gender == 'other') selected @endif>Khác</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Các phần thông tin bổ sung, mạng xã hội có thể làm tương tự nếu bạn đã lưu trong DB --}}
                {{-- Ví dụ: --}}
                {{-- <textarea ...>{{ old('bio', $user->bio) }}</textarea> --}}

                <div class="flex flex-col md:flex-row gap-4 pt-4 border-t border-gray-100">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 px-6 rounded-lg transition-colors flex-1 flex items-center justify-center">
                        <i class="fas fa-save mr-2"></i>
                        Lưu thay đổi
                    </button>
                    {{-- SỬA LẠI LINK ĐỂ DÙNG ROUTE --}}
                    <a href="{{ route('customer.profile') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-6 rounded-lg transition-colors flex-1 flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i>
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center" id="success-modal">
    <div class="bg-white rounded-lg p-8 max-w-md w-full">
        <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-green-500 text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">Cập Nhật Thành Công!</h2>
            <p class="text-gray-600 mb-6">
                Thông tin hồ sơ của bạn đã được cập nhật thành công.
            </p>
            <div class="flex justify-center gap-4">
                <a href="/profile" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-md font-medium transition-colors">
                    Xem hồ sơ
                </a>
                <button id="close-modal" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-md font-medium transition-colors">
                    Đóng
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Avatar preview
    const avatarUpload = document.getElementById('avatar-upload');
    const avatarPreview = document.getElementById('avatar-preview');
    
    if (avatarUpload && avatarPreview) {
        avatarUpload.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    // Form submission
    
    const editProfileForm = document.getElementById('edit-profile-form');
    const successModal = document.getElementById('success-modal');
    const closeModalButton = document.getElementById('close-modal');
    
    if (editProfileForm) {
        editProfileForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const firstName = document.getElementById('first_name').value;
            const lastName = document.getElementById('last_name').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            
            if (!firstName || !lastName || !email || !phone) {
                showToast('Vui lòng điền đầy đủ các trường bắt buộc.');
                return; 
            }
            
            const formData = new FormData(this);
            const actionUrl = this.action;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            showToast('Đang cập nhật...');

            fetch(actionUrl, {
                method: 'POST', 
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => {
                return response.json().then(data => ({ status: response.status, body: data }));
            })
            .then(({ status, body }) => {
                if (status === 200 && body.success) {
                    showToast(body.message || 'Cập nhật thành công!');
                    successModal.classList.remove('hidden');

                    if (body.avatar_url) {
                        avatarPreview.src = body.avatar_url;
                    }
                } else {
                    if (body.errors) {
                        let firstError = Object.values(body.errors)[0][0];
                        showToast(firstError);
                    } else {
                        showToast(body.message || 'Đã có lỗi xảy ra.');
                    }
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                showToast('Lỗi kết nối. Vui lòng kiểm tra lại mạng.');
            });
        });
    }
    
    // Close modal
    closeModalButton.addEventListener('click', function() {
        successModal.classList.add('hidden');
    });
    
    // Close modal when clicking outside
    successModal.addEventListener('click', function(e) {
        if (e.target === successModal) {
            successModal.classList.add('hidden');
        }
    });
    
    // Simple toast notification function
    function showToast(message) {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300 opacity-0';
        toast.textContent = message;
        
        // Add to DOM
        document.body.appendChild(toast);
        
        // Show toast
        setTimeout(() => {
            toast.classList.remove('opacity-0');
            toast.classList.add('opacity-100');
        }, 10);
        
        // Hide and remove toast after 3 seconds
        setTimeout(() => {
            toast.classList.remove('opacity-100');
            toast.classList.add('opacity-0');
            
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
});
</script>
@endsection