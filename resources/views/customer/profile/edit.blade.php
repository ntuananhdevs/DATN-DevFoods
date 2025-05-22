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
            <a href="/profile" class="text-white hover:text-white/80 mr-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl md:text-3xl font-bold text-white">Chỉnh Sửa Hồ Sơ</h1>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8">
            <form id="edit-profile-form" class="p-6">
                <!-- Avatar Section -->
                <div class="flex flex-col items-center mb-8">
                    <div class="relative mb-4">
                        <div class="w-32 h-32 rounded-full bg-gray-200 overflow-hidden border-4 border-white shadow-md">
                            <img src="/placeholder.svg?height=200&width=200" alt="Ảnh đại diện" class="w-full h-full object-cover">
                        </div>
                        <label for="avatar-upload" class="absolute bottom-0 right-0 bg-orange-500 hover:bg-orange-600 text-white rounded-full w-10 h-10 flex items-center justify-center shadow-md transition-colors cursor-pointer">
                            <i class="fas fa-camera"></i>
                            <input type="file" id="avatar-upload" name="avatar" class="hidden" accept="image/*">
                        </label>
                    </div>
                    <p class="text-sm text-gray-500">Nhấn vào biểu tượng máy ảnh để thay đổi ảnh đại diện</p>
                    <p class="text-xs text-gray-400 mt-1">Kích thước tối đa: 5MB. Định dạng: JPG, PNG</p>
                </div>

                <!-- Personal Information -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold mb-4 pb-2 border-b border-gray-100">Thông Tin Cá Nhân</h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium mb-2">Họ <span class="text-red-500">*</span></label>
                            <input type="text" id="first_name" name="first_name" value="Nguyễn" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        
                        <div>
                            <label for="last_name" class="block text-sm font-medium mb-2">Tên <span class="text-red-500">*</span></label>
                            <input type="text" id="last_name" name="last_name" value="Văn A" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium mb-2">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" value="nguyenvana@example.com" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                            <input type="tel" id="phone" name="phone" value="0987654321" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        
                        <div>
                            <label for="birthday" class="block text-sm font-medium mb-2">Ngày sinh</label>
                            <input type="date" id="birthday" name="birthday" value="1990-01-01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        
                        <div>
                            <label for="gender" class="block text-sm font-medium mb-2">Giới tính</label>
                            <select id="gender" name="gender" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="male" selected>Nam</option>
                                <option value="female">Nữ</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold mb-4 pb-2 border-b border-gray-100">Thông Tin Bổ Sung</h2>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="bio" class="block text-sm font-medium mb-2">Giới thiệu bản thân</label>
                            <textarea id="bio" name="bio" rows="4" placeholder="Viết một vài điều về bản thân..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">Tôi là một người yêu thích ẩm thực và thường xuyên sử dụng dịch vụ của FastFood.</textarea>
                            <p class="text-xs text-gray-500 mt-1">Tối đa 200 ký tự</p>
                        </div>
                        
                        <div>
                            <label for="dietary_preferences" class="block text-sm font-medium mb-2">Sở thích ẩm thực</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="dietary_preferences[]" value="vegetarian" class="mr-2">
                                    <span>Ăn chay</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="dietary_preferences[]" value="vegan" class="mr-2">
                                    <span>Ăn thuần chay</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="dietary_preferences[]" value="gluten_free" class="mr-2">
                                    <span>Không gluten</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="dietary_preferences[]" value="dairy_free" class="mr-2">
                                    <span>Không sữa</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="dietary_preferences[]" value="spicy" class="mr-2" checked>
                                    <span>Thích đồ cay</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="dietary_preferences[]" value="seafood" class="mr-2" checked>
                                    <span>Thích hải sản</span>
                                </label>
                            </div>
                        </div>
                        
                        <div>
                            <label for="favorite_cuisine" class="block text-sm font-medium mb-2">Ẩm thực yêu thích</label>
                            <select id="favorite_cuisine" name="favorite_cuisine" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="">Chọn ẩm thực yêu thích</option>
                                <option value="vietnamese" selected>Việt Nam</option>
                                <option value="italian">Ý</option>
                                <option value="japanese">Nhật Bản</option>
                                <option value="korean">Hàn Quốc</option>
                                <option value="chinese">Trung Quốc</option>
                                <option value="thai">Thái Lan</option>
                                <option value="american">Mỹ</option>
                                <option value="mexican">Mexico</option>
                                <option value="indian">Ấn Độ</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Social Media Links -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold mb-4 pb-2 border-b border-gray-100">Liên Kết Mạng Xã Hội</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fab fa-facebook-f text-blue-600"></i>
                            </div>
                            <input type="text" name="facebook" placeholder="Liên kết Facebook của bạn" value="https://facebook.com/nguyenvana" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-pink-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fab fa-instagram text-pink-600"></i>
                            </div>
                            <input type="text" name="instagram" placeholder="Liên kết Instagram của bạn" value="" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fab fa-twitter text-blue-400"></i>
                            </div>
                            <input type="text" name="twitter" placeholder="Liên kết Twitter của bạn" value="" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col md:flex-row gap-4 pt-4 border-t border-gray-100">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 px-6 rounded-lg transition-colors flex-1 flex items-center justify-center">
                        <i class="fas fa-save mr-2"></i>
                        Lưu thay đổi
                    </button>
                    <a href="/profile" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-6 rounded-lg transition-colors flex-1 flex items-center justify-center">
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
    const avatarPreview = avatarUpload.closest('.relative').querySelector('img');
    
    avatarUpload.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                avatarPreview.src = e.target.result;
            }
            
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Form submission
    const editProfileForm = document.getElementById('edit-profile-form');
    const successModal = document.getElementById('success-modal');
    const closeModalButton = document.getElementById('close-modal');
    
    editProfileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Basic form validation
        const firstName = document.getElementById('first_name').value;
        const lastName = document.getElementById('last_name').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        
        if (!firstName || !lastName || !email || !phone) {
            showToast('Vui lòng điền đầy đủ thông tin bắt buộc');
            return;
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showToast('Email không hợp lệ');
            return;
        }
        
        // Phone validation
        const phoneRegex = /^[0-9]{10,11}$/;
        if (!phoneRegex.test(phone.replace(/\s/g, ''))) {
            showToast('Số điện thoại không hợp lệ');
            return;
        }
        
        // Show success modal
        successModal.classList.remove('hidden');
    });
    
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