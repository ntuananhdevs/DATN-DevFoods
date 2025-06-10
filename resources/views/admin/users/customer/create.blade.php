@extends('layouts.admin.contentLayoutMaster')

@section('content')
<div class="min-h-screen bg-gray-50 p-3">
    <div class="w-full">
        <!-- Main Header -->
        <div class="flex items-center gap-4 mb-8">
            <div class="p-3 bg-blue-100 rounded-lg">
                <i class="fas fa-user-plus text-blue-600 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Thêm người dùng mới</h1>
        </div>

        <!-- Main Content -->
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-semibold mb-6 text-gray-700">Thông tin người dùng</h2>
                <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" id="userForm">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập</label>
                            <input type="text" name="user_name" value="{{ old('user_name') }}"
                                   class="w-full px-3 py-2 border rounded-lg @error('user_name') border-red-500 @enderror"
                                   placeholder="Nhập tên đăng nhập">
                            @error('user_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}"
                                   class="w-full px-3 py-2 border rounded-lg @error('full_name') border-red-500 @enderror"
                                   placeholder="Nhập họ và tên">
                            @error('full_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="w-full px-3 py-2 border rounded-lg @error('email') border-red-500 @enderror"
                                   placeholder="Nhập email">
                            @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                   class="w-full px-3 py-2 border rounded-lg @error('phone') border-red-500 @enderror"
                                   placeholder="Nhập số điện thoại">
                            @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu</label>
                            <input type="password" name="password"
                                   class="w-full px-3 py-2 border rounded-lg @error('password') border-red-500 @enderror"
                                   placeholder="Nhập mật khẩu">
                            @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu</label>
                            <input type="password" name="password_confirmation"
                                   class="w-full px-3 py-2 border rounded-lg"
                                   placeholder="Nhập lại mật khẩu">
                        </div>

                        <!-- Hidden role selection -->
                        <div class="hidden">
                            <select name="role_ids[]">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ $role->name === 'customer' ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Hidden avatar input -->
                        <input type="file" id="avatar-input" name="avatar" class="hidden">
                    </div>

                    <div class="flex justify-end gap-3 mt-8">
                        <button type="button" onclick="window.history.back()"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Hủy bỏ
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                            Tạo quản lý
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Column -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="cursor-pointer group" onclick="document.getElementById('avatar').click()">
                    <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-xl p-6
                              hover:border-blue-500 hover:bg-blue-50 transition-colors">
                        <div id="avatar-preview" class="text-center">
                            <div class="mx-auto bg-blue-100 w-16 h-16 rounded-xl flex items-center justify-center mb-4">
                                <i class="ri-image-line text-blue-500 text-2xl"></i>
                            </div>
                            <p class="text-blue-600 font-medium mb-1">Tải lên ảnh đại diện</p>
                            <p class="text-gray-500 text-sm">Định dạng: JPEG, PNG (Khuyến nghị: 600x600)</p>
                        </div>
                    </div>
                    @error('avatar')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <input type="file" id="avatar" class="hidden" accept="image/*" onchange="previewAndTransferAvatar(this)">
            </div>
        </div>
    </div>
</div>

<script>
function previewAndTransferAvatar(input) {
    if (input.files && input.files[0]) {
        const hiddenInput = document.getElementById('avatar-input');
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(input.files[0]);
        hiddenInput.files = dataTransfer.files;

        const reader = new FileReader();
        const previewContainer = document.getElementById('avatar-preview');

        reader.onload = function(e) {
            previewContainer.innerHTML = `
                <img src="${e.target.result}" alt="Preview" class="w-32 h-32 rounded-xl object-cover mx-auto mb-4">
                <p class="text-blue-600 font-medium mb-1">Ảnh đã chọn</p>
                <p class="text-gray-500 text-sm">Nhấp để chọn ảnh khác</p>
            `;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection

