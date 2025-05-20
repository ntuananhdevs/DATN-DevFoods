@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Hồ Sơ Cá Nhân')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-3xl font-bold mb-8">Hồ Sơ Cá Nhân</h1>
            
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-orange-500 to-red-500 py-6 px-6">
                    <div class="flex flex-col md:flex-row items-center">
                        <div class="w-24 h-24 bg-white rounded-full overflow-hidden border-4 border-white flex-shrink-0 mb-4 md:mb-0 md:mr-6">
                            <img src="/placeholder.svg?height=200&width=200" alt="Avatar" class="w-full h-full object-cover">
                        </div>
                        <div class="text-white text-center md:text-left">
                            <h2 class="text-2xl font-bold">Nguyễn Văn A</h2>
                            <p class="opacity-90">Thành viên từ: Tháng 6, 2023</p>
                            <div class="mt-2 flex items-center justify-center md:justify-start">
                                <div class="bg-yellow-500 text-white text-xs px-2 py-1 rounded-full flex items-center">
                                    <i class="fas fa-crown mr-1"></i>
                                    <span>Thành viên Vàng</span>
                                </div>
                                <div class="ml-2 text-sm">
                                    <i class="fas fa-star"></i>
                                    <span>2500 điểm</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-orange-50 p-4 rounded-lg text-center">
                            <div class="text-3xl font-bold text-orange-500 mb-1">12</div>
                            <div class="text-gray-600">Đơn hàng</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg text-center">
                            <div class="text-3xl font-bold text-green-500 mb-1">5</div>
                            <div class="text-gray-600">Đánh giá</div>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg text-center">
                            <div class="text-3xl font-bold text-blue-500 mb-1">3</div>
                            <div class="text-gray-600">Voucher khả dụng</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabs -->
            <div class="mb-6 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="profile-tabs" role="tablist">
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 border-orange-500 rounded-t-lg active" id="info-tab" data-tab="info" type="button" role="tab" aria-controls="info" aria-selected="true">
                            Thông tin cá nhân
                        </button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="orders-tab" data-tab="orders" type="button" role="tab" aria-controls="orders" aria-selected="false">
                            Lịch sử đơn hàng
                        </button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="addresses-tab" data-tab="addresses" type="button" role="tab" aria-controls="addresses" aria-selected="false">
                            Địa chỉ giao hàng
                        </button>
                    </li>
                    <li role="presentation">
                        <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="vouchers-tab" data-tab="vouchers" type="button" role="tab" aria-controls="vouchers" aria-selected="false">
                            Voucher của tôi
                        </button>
                    </li>
                </ul>
            </div>
            
            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Personal Information Tab -->
                <div class="tab-pane active" id="info">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold">Thông Tin Cá Nhân</h3>
                            <button id="edit-info-btn" class="text-orange-500 hover:text-orange-600 flex items-center">
                                <i class="fas fa-edit mr-1"></i>
                                <span>Chỉnh sửa</span>
                            </button>
                        </div>
                        
                        <form id="profile-form" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Họ
                                    </label>
                                    <input type="text" id="first_name" name="first_name" value="Nguyễn" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500" disabled>
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Tên
                                    </label>
                                    <input type="text" id="last_name" name="last_name" value="Văn A" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500" disabled>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                        Email
                                    </label>
                                    <input type="email" id="email" name="email" value="nguyenvana@example.com" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500" disabled>
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                        Số điện thoại
                                    </label>
                                    <input type="tel" id="phone" name="phone" value="0912345678" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500" disabled>
                                </div>
                            </div>
                            
                            <div>
                                <label for="dob" class="block text-sm font-medium text-gray-700 mb-1">
                                    Ngày sinh
                                </label>
                                <input type="date" id="dob" name="dob" value="1990-01-01" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500" disabled>
                            </div>
                            
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">
                                    Giới tính
                                </label>
                                <select id="gender" name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500" disabled>
                                    <option value="male" selected>Nam</option>
                                    <option value="female">Nữ</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>
                            
                            <div class="hidden" id="save-buttons">
                                <div class="flex justify-end space-x-3">
                                    <button type="button" id="cancel-edit" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                                        Hủy
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-md transition-colors">
                                        Lưu thay đổi
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <hr class="my-6 border-gray-200">
                        
                        <div>
                            <h3 class="text-xl font-bold mb-4">Đổi Mật Khẩu</h3>
                            <form id="password-form" class="space-y-4">
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                                        Mật khẩu hiện tại
                                    </label>
                                    <input type="password" id="current_password" name="current_password" placeholder="Nhập mật khẩu hiện tại" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                </div>
                                
                                <div>
                                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">
                                        Mật khẩu mới
                                    </label>
                                    <input type="password" id="new_password" name="new_password" placeholder="Nhập mật khẩu mới" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                </div>
                                
                                <div>
                                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">
                                        Xác nhận mật khẩu mới
                                    </label>
                                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu mới" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                </div>
                                
                                <div>
                                    <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-md transition-colors">
                                        Đổi mật khẩu
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Order History Tab -->
                <div class="tab-pane" id="orders">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-xl font-bold mb-6">Lịch Sử Đơn Hàng</h3>
                        
                        <div class="space-y-6">
                            <!-- Order 1 -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <div class="bg-gray-50 p-4 flex flex-col md:flex-row justify-between items-start md:items-center">
                                    <div>
                                        <div class="flex items-center">
                                            <span class="font-medium">Mã đơn hàng: #FF12345</span>
                                            <span class="ml-3 bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Đã giao hàng</span>
                                        </div>
                                        <div class="text-gray-500 text-sm mt-1">Ngày đặt: 15/05/2023</div>
                                    </div>
                                    <div class="mt-2 md:mt-0">
                                        <a href="/order-tracking" class="text-orange-500 hover:text-orange-600 text-sm flex items-center">
                                            <i class="fas fa-search mr-1"></i>
                                            <span>Xem chi tiết</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="space-y-4">
                                        <div class="flex items-center">
                                            <div class="w-16 h-16 rounded-md overflow-hidden flex-shrink-0">
                                                <img src="/placeholder.svg?height=100&width=100" alt="Burger" class="w-full h-full object-cover">
                                            </div>
                                            <div class="ml-4">
                                                <div class="font-medium">Burger Bò Phô Mai</div>
                                                <div class="text-gray-500 text-sm">Số lượng: 2</div>
                                            </div>
                                            <div class="ml-auto font-medium">120.000₫</div>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-16 h-16 rounded-md overflow-hidden flex-shrink-0">
                                                <img src="/placeholder.svg?height=100&width=100" alt="Khoai tây chiên" class="w-full h-full object-cover">
                                            </div>
                                            <div class="ml-4">
                                                <div class="font-medium">Khoai Tây Chiên (L)</div>
                                                <div class="text-gray-500 text-sm">Số lượng: 1</div>
                                            </div>
                                            <div class="ml-auto font-medium">40.000₫</div>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between">
                                        <div>
                                            <div class="text-gray-500">Tổng cộng:</div>
                                            <div class="font-bold text-lg">160.000₫</div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button class="px-3 py-1 border border-orange-500 text-orange-500 rounded-md hover:bg-orange-50 transition-colors text-sm">
                                                Đặt lại
                                            </button>
                                            <button class="px-3 py-1 bg-orange-500 text-white rounded-md hover:bg-orange-600 transition-colors text-sm">
                                                Đánh giá
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Order 2 -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <div class="bg-gray-50 p-4 flex flex-col md:flex-row justify-between items-start md:items-center">
                                    <div>
                                        <div class="flex items-center">
                                            <span class="font-medium">Mã đơn hàng: #FF12346</span>
                                            <span class="ml-3 bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Đã giao hàng</span>
                                        </div>
                                        <div class="text-gray-500 text-sm mt-1">Ngày đặt: 10/05/2023</div>
                                    </div>
                                    <div class="mt-2 md:mt-0">
                                        <a href="/order-tracking" class="text-orange-500 hover:text-orange-600 text-sm flex items-center">
                                            <i class="fas fa-search mr-1"></i>
                                            <span>Xem chi tiết</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="space-y-4">
                                        <div class="flex items-center">
                                            <div class="w-16 h-16 rounded-md overflow-hidden flex-shrink-0">
                                                <img src="/placeholder.svg?height=100&width=100" alt="Pizza" class="w-full h-full object-cover">
                                            </div>
                                            <div class="ml-4">
                                                <div class="font-medium">Pizza Hải Sản (L)</div>
                                                <div class="text-gray-500 text-sm">Số lượng: 1</div>
                                            </div>
                                            <div class="ml-auto font-medium">180.000₫</div>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-16 h-16 rounded-md overflow-hidden flex-shrink-0">
                                                <img src="/placeholder.svg?height=100&width=100" alt="Nước ngọt" class="w-full h-full object-cover">
                                            </div>
                                            <div class="ml-4">
                                                <div class="font-medium">Coca Cola (L)</div>
                                                <div class="text-gray-500 text-sm">Số lượng: 2</div>
                                            </div>
                                            <div class="ml-auto font-medium">50.000₫</div>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between">
                                        <div>
                                            <div class="text-gray-500">Tổng cộng:</div>
                                            <div class="font-bold text-lg">230.000₫</div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button class="px-3 py-1 border border-orange-500 text-orange-500 rounded-md hover:bg-orange-50 transition-colors text-sm">
                                                Đặt lại
                                            </button>
                                            <button class="px-3 py-1 bg-orange-500 text-white rounded-md hover:bg-orange-600 transition-colors text-sm">
                                                Đánh giá
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Addresses Tab -->
                <div class="tab-pane" id="addresses">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold">Địa Chỉ Giao Hàng</h3>
                            <button id="add-address-btn" class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md transition-colors flex items-center text-sm">
                                <i class="fas fa-plus mr-1"></i>
                                <span>Thêm địa chỉ mới</span>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Address 1 -->
                            <div class="border border-gray-200 rounded-lg p-4 relative">
                                <div class="absolute top-4 right-4 flex space-x-2">
                                    <button class="text-gray-500 hover:text-gray-700">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-gray-500 hover:text-red-500">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                <div class="mb-2 flex items-center">
                                    <span class="font-bold">Nhà riêng</span>
                                    <span class="ml-2 bg-orange-100 text-orange-800 text-xs px-2 py-0.5 rounded-full">Mặc định</span>
                                </div>
                                <div class="text-gray-700 mb-1">Nguyễn Văn A</div>
                                <div class="text-gray-700 mb-1">0912345678</div>
                                <div class="text-gray-700">
                                    123 Đường ABC, Phường XYZ, Quận 1, TP. Hồ Chí Minh
                                </div>
                            </div>
                            
                            <!-- Address 2 -->
                            <div class="border border-gray-200 rounded-lg p-4 relative">
                                <div class="absolute top-4 right-4 flex space-x-2">
                                    <button class="text-gray-500 hover:text-gray-700">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-gray-500 hover:text-red-500">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                <div class="mb-2 font-bold">Văn phòng</div>
                                <div class="text-gray-700 mb-1">Nguyễn Văn A</div>
                                <div class="text-gray-700 mb-1">0912345678</div>
                                <div class="text-gray-700">
                                    456 Đường DEF, Phường UVW, Quận 3, TP. Hồ Chí Minh
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Vouchers Tab -->
                <div class="tab-pane" id="vouchers">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-xl font-bold mb-6">Voucher Của Tôi</h3>
                        
                        <div class="space-y-4">
                            <!-- Voucher 1 -->
                            <div class="border border-dashed border-orange-500 rounded-lg overflow-hidden flex flex-col md:flex-row">
                                <div class="bg-orange-500 text-white p-4 md:w-1/4 flex flex-col items-center justify-center">
                                    <div class="text-2xl font-bold">GIẢM 50K</div>
                                    <div class="text-sm">Đơn tối thiểu 200K</div>
                                </div>
                                <div class="p-4 md:w-3/4 flex flex-col md:flex-row justify-between">
                                    <div>
                                        <div class="font-medium mb-1">Giảm 50.000₫ cho đơn hàng từ 200.000₫</div>
                                        <div class="text-gray-500 text-sm mb-2">Áp dụng cho tất cả các món ăn</div>
                                        <div class="flex items-center">
                                            <div class="text-sm bg-gray-100 px-2 py-1 rounded-full">
                                                <i class="far fa-clock mr-1"></i>
                                                <span>HSD: 30/06/2023</span>
                                            </div>
                                            <button class="ml-2 text-sm text-orange-500 hover:text-orange-600 flex items-center">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                <span>Điều kiện</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-4 md:mt-0 flex items-center">
                                        <button class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-md transition-colors text-sm">
                                            Sử dụng ngay
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Voucher 2 -->
                            <div class="border border-dashed border-green-500 rounded-lg overflow-hidden flex flex-col md:flex-row">
                                <div class="bg-green-500 text-white p-4 md:w-1/4 flex flex-col items-center justify-center">
                                    <div class="text-2xl font-bold">FREESHIP</div>
                                    <div class="text-sm">Đơn tối thiểu 150K</div>
                                </div>
                                <div class="p-4 md:w-3/4 flex flex-col md:flex-row justify-between">
                                    <div>
                                        <div class="font-medium mb-1">Miễn phí giao hàng cho đơn từ 150.000₫</div>
                                        <div class="text-gray-500 text-sm mb-2">Áp dụng trong bán kính 5km</div>
                                        <div class="flex items-center">
                                            <div class="text-sm bg-gray-100 px-2 py-1 rounded-full">
                                                <i class="far fa-clock mr-1"></i>
                                                <span>HSD: 15/07/2023</span>
                                            </div>
                                            <button class="ml-2 text-sm text-orange-500 hover:text-orange-600 flex items-center">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                <span>Điều kiện</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-4 md:mt-0 flex items-center">
                                        <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition-colors text-sm">
                                            Sử dụng ngay
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Voucher 3 -->
                            <div class="border border-dashed border-blue-500 rounded-lg overflow-hidden flex flex-col md:flex-row">
                                <div class="bg-blue-500 text-white p-4 md:w-1/4 flex flex-col items-center justify-center">
                                    <div class="text-2xl font-bold">GIẢM 15%</div>
                                    <div class="text-sm">Đơn tối thiểu 300K</div>
                                </div>
                                <div class="p-4 md:w-3/4 flex flex-col md:flex-row justify-between">
                                    <div>
                                        <div class="font-medium mb-1">Giảm 15% cho đơn hàng từ 300.000₫</div>
                                        <div class="text-gray-500 text-sm mb-2">Tối đa 100.000₫</div>
                                        <div class="flex items-center">
                                            <div class="text-sm bg-gray-100 px-2 py-1 rounded-full">
                                                <i class="far fa-clock mr-1"></i>
                                                <span>HSD: 31/07/2023</span>
                                            </div>
                                            <button class="ml-2 text-sm text-orange-500 hover:text-orange-600 flex items-center">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                <span>Điều kiện</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-4 md:mt-0 flex items-center">
                                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors text-sm">
                                            Sử dụng ngay
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 text-center">
                            <a href="/promotions" class="text-orange-500 hover:text-orange-600 font-medium inline-flex items-center">
                                <span>Xem thêm khuyến mãi</span>
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Address Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center" id="address-modal">
    <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Thêm Địa Chỉ Mới</h3>
            <button id="close-address-modal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="address-form" class="space-y-4">
            <div>
                <label for="address_label" class="block text-sm font-medium text-gray-700 mb-1">
                    Nhãn địa chỉ
                </label>
                <input type="text" id="address_label" name="address_label" placeholder="Ví dụ: Nhà, Văn phòng, ..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            
            <div>
                <label for="recipient_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Tên người nhận
                </label>
                <input type="text" id="recipient_name" name="recipient_name" placeholder="Nhập tên người nhận" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            
            <div>
                <label for="recipient_phone" class="block text-sm font-medium text-gray-700 mb-1">
                    Số điện thoại
                </label>
                <input type="tel" id="recipient_phone" name="recipient_phone" placeholder="Nhập số điện thoại" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                    Địa chỉ chi tiết
                </label>
                <textarea id="address" name="address" rows="3" placeholder="Số nhà, đường, phường/xã, ..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                        Tỉnh/Thành phố
                    </label>
                    <select id="city" name="city" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Chọn tỉnh/thành phố</option>
                        <option value="hcm">TP. Hồ Chí Minh</option>
                        <option value="hn">Hà Nội</option>
                        <option value="dn">Đà Nẵng</option>
                    </select>
                </div>
                <div>
                    <label for="district" class="block text-sm font-medium text-gray-700 mb-1">
                        Quận/Huyện
                    </label>
                    <select id="district" name="district" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Chọn quận/huyện</option>
                    </select>
                </div>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" id="default_address" name="default_address" class="h-4 w-4 text-orange-500 focus:ring-orange-500 border-gray-300 rounded">
                <label for="default_address" class="ml-2 block text-sm text-gray-700">
                    Đặt làm địa chỉ mặc định
                </label>
            </div>
            
            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" id="cancel-address" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Hủy
                </button>
                <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-md transition-colors">
                    Lưu địa chỉ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    const tabs = document.querySelectorAll('#profile-tabs button');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all tabs and tab panes
            tabs.forEach(t => t.classList.remove('border-orange-500', 'active'));
            tabs.forEach(t => t.classList.add('border-transparent'));
            tabPanes.forEach(p => p.classList.remove('active'));
            
            // Add active class to current tab and tab pane
            this.classList.add('border-orange-500', 'active');
            this.classList.remove('border-transparent');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Edit profile information
    const editInfoBtn = document.getElementById('edit-info-btn');
    const cancelEditBtn = document.getElementById('cancel-edit');
    const saveButtons = document.getElementById('save-buttons');
    const profileForm = document.getElementById('profile-form');
    const formInputs = profileForm.querySelectorAll('input, select');
    
    if (editInfoBtn && cancelEditBtn && saveButtons) {
        editInfoBtn.addEventListener('click', function() {
            // Enable form inputs
            formInputs.forEach(input => {
                input.disabled = false;
            });
            
            // Show save buttons
            saveButtons.classList.remove('hidden');
            
            // Hide edit button
            editInfoBtn.classList.add('hidden');
        });
        
        cancelEditBtn.addEventListener('click', function() {
            // Disable form inputs
            formInputs.forEach(input => {
                input.disabled = true;
            });
            
            // Hide save buttons
            saveButtons.classList.add('hidden');
            
            // Show edit button
            editInfoBtn.classList.remove('hidden');
            
            // Reset form
            profileForm.reset();
        });
        
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Simulate form submission
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang lưu...';
            
            setTimeout(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
                
                // Disable form inputs
                formInputs.forEach(input => {
                    input.disabled = true;
                });
                
                // Hide save buttons
                saveButtons.classList.add('hidden');
                
                // Show edit button
                editInfoBtn.classList.remove('hidden');
                
                // Show success message
                showToast('Thông tin cá nhân đã được cập nhật thành công');
            }, 1500);
        });
    }
    
    // Password form
    const passwordForm = document.getElementById('password-form');
    
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Basic validation
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (!currentPassword || !newPassword || !confirmPassword) {
                showToast('Vui lòng điền đầy đủ thông tin');
                return;
            }
            
            if (newPassword.length < 6) {
                showToast('Mật khẩu mới phải có ít nhất 6 ký tự');
                return;
            }
            
            if (newPassword !== confirmPassword) {
                showToast('Xác nhận mật khẩu không khớp');
                return;
            }
            
            // Simulate form submission
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang xử lý...';
            
            setTimeout(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
                
                // Reset form
                passwordForm.reset();
                
                // Show success message
                showToast('Mật khẩu đã được thay đổi thành công');
            }, 1500);
        });
    }
    
    // Address modal
    const addAddressBtn = document.getElementById('add-address-btn');
    const addressModal = document.getElementById('address-modal');
    const closeAddressModal = document.getElementById('close-address-modal');
    const cancelAddressBtn = document.getElementById('cancel-address');
    const addressForm = document.getElementById('address-form');
    
    if (addAddressBtn && addressModal && closeAddressModal && cancelAddressBtn) {
        addAddressBtn.addEventListener('click', function() {
            addressModal.classList.remove('hidden');
        });
        
        closeAddressModal.addEventListener('click', function() {
            addressModal.classList.add('hidden');
        });
        
        cancelAddressBtn.addEventListener('click', function() {
            addressModal.classList.add('hidden');
        });
        
        // Close modal when clicking outside
        addressModal.addEventListener('click', function(e) {
            if (e.target === addressModal) {
                addressModal.classList.add('hidden');
            }
        });
        
        addressForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Basic validation
            const label = document.getElementById('address_label').value;
            const name = document.getElementById('recipient_name').value;
            const phone = document.getElementById('recipient_phone').value;
            const address = document.getElementById('address').value;
            const city = document.getElementById('city').value;
            const district = document.getElementById('district').value;
            
            if (!label || !name || !phone || !address || !city || !district) {
                showToast('Vui lòng điền đầy đủ thông tin địa chỉ');
                return;
            }
            
            // Simulate form submission
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang lưu...';
            
            setTimeout(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
                
                // Hide modal
                addressModal.classList.add('hidden');
                
                // Reset form
                addressForm.reset();
                
                // Show success message
                showToast('Địa chỉ mới đã được thêm thành công');
            }, 1500);
        });
    }
    
    // City-district dependency
    const citySelect = document.getElementById('city');
    if (citySelect) {
        citySelect.addEventListener('change', function() {
            const cityId = this.value;
            const districtSelect = document.getElementById('district');
            
            // Clear current options
            districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
            
            if (!cityId) return;
            
            // Simulate loading districts based on city
            if (cityId === 'hcm') {
                const hcmDistricts = [
                    {id: 'q1', name: 'Quận 1'},
                    {id: 'q3', name: 'Quận 3'},
                    {id: 'q4', name: 'Quận 4'},
                    {id: 'q5', name: 'Quận 5'},
                    {id: 'q7', name: 'Quận 7'},
                    {id: 'tb', name: 'Quận Tân Bình'},
                    {id: 'pn', name: 'Quận Phú Nhuận'}
                ];
                
                hcmDistricts.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.id;
                    option.textContent = district.name;
                    districtSelect.appendChild(option);
                });
            } else if (cityId === 'hn') {
                const hnDistricts = [
                    {id: 'hbt', name: 'Quận Hai Bà Trưng'},
                    {id: 'hk', name: 'Quận Hoàn Kiếm'},
                    {id: 'cg', name: 'Quận Cầu Giấy'},
                    {id: 'dd', name: 'Quận Đống Đa'},
                    {id: 'tx', name: 'Quận Thanh Xuân'}
                ];
                
                hnDistricts.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.id;
                    option.textContent = district.name;
                    districtSelect.appendChild(option);
                });
            } else if (cityId === 'dn') {
                const dnDistricts = [
                    {id: 'hs', name: 'Quận Hải Châu'},
                    {id: 'tt', name: 'Quận Thanh Khê'},
                    {id: 'lc', name: 'Quận Liên Chiểu'},
                    {id: 'st', name: 'Quận Sơn Trà'},
                    {id: 'ng', name: 'Quận Ngũ Hành Sơn'}
                ];
                
                dnDistricts.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.id;
                    option.textContent = district.name;
                    districtSelect.appendChild(option);
                });
            }
        });
    }
    
    // Toast notification function
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