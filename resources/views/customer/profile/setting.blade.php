@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Cài Đặt Tài Khoản')

@section('content')
<div class="bg-gradient-to-r from-orange-500 to-red-500 py-8">
    <div class="container mx-auto px-4">
        <div class="flex items-center">
            <a href="{{ route('customer.profile') }}" class="text-white hover:text-white/80 mr-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl md:text-3xl font-bold text-white">Cài Đặt Tài Khoản</h1>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Navigation -->
        <div class="lg:w-1/4">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden sticky top-24">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="font-bold text-lg">Cài đặt</h2>
                </div>
                <nav class="p-2">
                    <ul class="space-y-1">
                        <li>
                            <a href="#password" class="flex items-center px-4 py-3 rounded-lg bg-orange-50 text-orange-500 font-medium">
                                <i class="fas fa-lock mr-3 w-5 text-center"></i>
                                Mật khẩu & Bảo mật
                            </a>
                        </li>
                        <li>
                            <a href="#notifications" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-bell mr-3 w-5 text-center"></i>
                                Thông báo
                            </a>
                        </li>
                        <li>
                            <a href="#privacy" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-shield-alt mr-3 w-5 text-center"></i>
                                Quyền riêng tư
                            </a>
                        </li>
                        <li>
                            <a href="#payment-methods" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-credit-card mr-3 w-5 text-center"></i>
                                Phương thức thanh toán
                            </a>
                        </li>
                        <li>
                            <a href="#language" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-globe mr-3 w-5 text-center"></i>
                                Ngôn ngữ & Khu vực
                            </a>
                        </li>
                        <li class="border-t border-gray-100 mt-2 pt-2">
                            <a href="#delete-account" class="flex items-center px-4 py-3 rounded-lg text-red-500 hover:bg-red-50 transition-colors">
                                <i class="fas fa-trash-alt mr-3 w-5 text-center"></i>
                                Xóa tài khoản
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:w-3/4">
            <section id="password" class="mb-10">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-xl font-bold">Mật Khẩu & Bảo Mật</h2>
                    </div>
                    
                    <div class="p-6">
                        <form id="password-form" action="{{ route('customer.password.update') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="current_password" class="block text-sm font-medium mb-2">Mật khẩu hiện tại <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="password" id="current_password" name="current_password" placeholder="Nhập mật khẩu hiện tại" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                                </div>
                            </div>
                            
                            <div>
                                <label for="new_password" class="block text-sm font-medium mb-2">Mật khẩu mới <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="password" id="new_password" name="password" placeholder="Nhập mật khẩu mới" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                                    <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 toggle-password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-1">Mật khẩu phải có:</p>
                                    <ul class="text-xs text-gray-500 space-y-1 ml-5 list-disc">
                                        <li>Ít nhất 8 ký tự</li>
                                        <li>Ít nhất 1 chữ cái viết hoa</li>
                                        <li>Ít nhất 1 chữ số</li>
                                        <li>Ít nhất 1 ký tự đặc biệt</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div>
                                <label for="confirm_password" class="block text-sm font-medium mb-2">Xác nhận mật khẩu mới <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="password" id="confirm_password" name="password_confirmation" placeholder="Nhập lại mật khẩu mới" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                                    <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 toggle-password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div>
                                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                                    Cập nhật mật khẩu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold">Xác Thực Hai Yếu Tố</h3>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <p class="font-medium mb-1">Bảo vệ tài khoản của bạn với xác thực hai yếu tố</p>
                                <p class="text-sm text-gray-500">
                                    Thêm một lớp bảo mật bổ sung cho tài khoản của bạn bằng cách yêu cầu nhiều hơn một phương thức xác thực.
                                </p>
                            </div>
                            <div class="relative inline-block w-12 h-6 ml-4">
                                <input type="checkbox" id="toggle-2fa" class="peer opacity-0 w-0 h-0">
                                <label for="toggle-2fa" class="absolute cursor-pointer inset-0 bg-gray-300 rounded-full transition-all duration-300 before:absolute before:content-[''] before:h-4 before:w-4 before:left-1 before:bottom-1 before:bg-white before:rounded-full before:transition-all before:duration-300 peer-checked:bg-orange-500 peer-checked:before:translate-x-6"></label>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg hidden" id="2fa-setup">
                            <p class="text-sm mb-4">Để thiết lập xác thực hai yếu tố, bạn cần:</p>
                            <ol class="text-sm space-y-2 ml-5 list-decimal">
                                <li>Tải ứng dụng xác thực như Google Authenticator hoặc Authy</li>
                                <li>Quét mã QR bên dưới bằng ứng dụng xác thực</li>
                                <li>Nhập mã xác thực từ ứng dụng để hoàn tất thiết lập</li>
                            </ol>
                            
                            <div class="flex flex-col md:flex-row items-center gap-6 mt-4">
                                <div class="bg-white p-2 border border-gray-200 rounded-lg">
                                    <img src="/placeholder.svg?height=200&width=200" alt="QR Code" class="w-32 h-32">
                                </div>
                                
                                <div class="flex-1">
                                    <label for="verification_code" class="block text-sm font-medium mb-2">Mã xác thực</label>
                                    <div class="flex">
                                        <input type="text" id="verification_code" name="verification_code" placeholder="Nhập mã 6 chữ số" class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                        <button type="button" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-r-lg transition-colors">
                                            Xác nhận
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold">Phiên Đăng Nhập</h3>
                    </div>
                    
                    <div class="p-6">
                        <p class="text-sm text-gray-500 mb-4">
                            Danh sách các thiết bị đang đăng nhập vào tài khoản của bạn. Bạn có thể đăng xuất khỏi các thiết bị không sử dụng.
                        </p>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 border border-gray-100 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-laptop text-gray-500"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium">MacBook Pro</p>
                                        <p class="text-xs text-gray-500">TP. Hồ Chí Minh, Việt Nam • Hiện tại</p>
                                    </div>
                                </div>
                                <span class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded-full">Thiết bị hiện tại</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 border border-gray-100 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-mobile-alt text-gray-500"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium">iPhone 13</p>
                                        <p class="text-xs text-gray-500">TP. Hồ Chí Minh, Việt Nam • 2 giờ trước</p>
                                    </div>
                                </div>
                                <button class="text-sm text-red-500 hover:text-red-600">Đăng xuất</button>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 border border-gray-100 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-tablet-alt text-gray-500"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium">iPad Air</p>
                                        <p class="text-xs text-gray-500">Hà Nội, Việt Nam • 3 ngày trước</p>
                                    </div>
                                </div>
                                <button class="text-sm text-red-500 hover:text-red-600">Đăng xuất</button>
                            </div>
                        </div>
                        
                        <button class="mt-4 text-orange-500 hover:text-orange-600 text-sm font-medium">
                            Đăng xuất khỏi tất cả các thiết bị khác
                        </button>
                    </div>
                </div>
            </section>

            <!-- Notifications Section -->
            <section id="notifications" class="mb-10">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-xl font-bold">Thông Báo</h2>
                    </div>
                    
                    <div class="p-6">
                        <p class="text-sm text-gray-500 mb-6">
                            Quản lý cách bạn nhận thông báo từ FastFood. Bạn có thể tắt hoặc bật các loại thông báo khác nhau.
                        </p>
                        
                        <div class="space-y-6">
                            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                                <div>
                                    <h3 class="font-medium mb-1">Thông báo qua Email</h3>
                                    <p class="text-sm text-gray-500">Nhận thông báo qua email</p>
                                </div>
                                <div class="relative inline-block w-12 h-6">
                                    <input type="checkbox" id="toggle-email" class="peer opacity-0 w-0 h-0" checked>
                                    <label for="toggle-email" class="absolute cursor-pointer inset-0 bg-gray-300 rounded-full transition-all duration-300 before:absolute before:content-[''] before:h-4 before:w-4 before:left-1 before:bottom-1 before:bg-white before:rounded-full before:transition-all before:duration-300 peer-checked:bg-orange-500 peer-checked:before:translate-x-6"></label>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                                <div>
                                    <h3 class="font-medium mb-1">Thông báo qua SMS</h3>
                                    <p class="text-sm text-gray-500">Nhận thông báo qua tin nhắn SMS</p>
                                </div>
                                <div class="relative inline-block w-12 h-6">
                                    <input type="checkbox" id="toggle-sms" class="peer opacity-0 w-0 h-0" checked>
                                    <label for="toggle-sms" class="absolute cursor-pointer inset-0 bg-gray-300 rounded-full transition-all duration-300 before:absolute before:content-[''] before:h-4 before:w-4 before:left-1 before:bottom-1 before:bg-white before:rounded-full before:transition-all before:duration-300 peer-checked:bg-orange-500 peer-checked:before:translate-x-6"></label>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                                <div>
                                    <h3 class="font-medium mb-1">Thông báo đẩy</h3>
                                    <p class="text-sm text-gray-500">Nhận thông báo đẩy trên thiết bị di động</p>
                                </div>
                                <div class="relative inline-block w-12 h-6">
                                    <input type="checkbox" id="toggle-push" class="peer opacity-0 w-0 h-0" checked>
                                    <label for="toggle-push" class="absolute cursor-pointer inset-0 bg-gray-300 rounded-full transition-all duration-300 before:absolute before:content-[''] before:h-4 before:w-4 before:left-1 before:bottom-1 before:bg-white before:rounded-full before:transition-all before:duration-300 peer-checked:bg-orange-500 peer-checked:before:translate-x-6"></label>
                                </div>
                            </div>
                        </div>
                        
                        <h3 class="font-bold mt-8 mb-4">Loại thông báo</h3>
                        
                        <div class="space-y-6">
                            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                                <div>
                                    <h3 class="font-medium mb-1">Cập nhật đơn hàng</h3>
                                    <p class="text-sm text-gray-500">Thông báo về trạng thái đơn hàng</p>
                                </div>
                                <div class="relative inline-block w-12 h-6">
                                    <input type="checkbox" id="toggle-order-updates" class="peer opacity-0 w-0 h-0" checked>
                                    <label for="toggle-order-updates" class="absolute cursor-pointer inset-0 bg-gray-300 rounded-full transition-all duration-300 before:absolute before:content-[''] before:h-4 before:w-4 before:left-1 before:bottom-1 before:bg-white before:rounded-full before:transition-all before:duration-300 peer-checked:bg-orange-500 peer-checked:before:translate-x-6"></label>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                                <div>
                                    <h3 class="font-medium mb-1">Khuyến mãi & Ưu đãi</h3>
                                    <p class="text-sm text-gray-500">Thông báo về khuyến mãi và ưu đãi mới</p>
                                </div>
                                <div class="relative inline-block w-12 h-6">
                                    <input type="checkbox" id="toggle-promotions" class="peer opacity-0 w-0 h-0" checked>
                                    <label for="toggle-promotions" class="absolute cursor-pointer inset-0 bg-gray-300 rounded-full transition-all duration-300 before:absolute before:content-[''] before:h-4 before:w-4 before:left-1 before:bottom-1 before:bg-white before:rounded-full before:transition-all before:duration-300 peer-checked:bg-orange-500 peer-checked:before:translate-x-6"></label>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                                <div>
                                    <h3 class="font-medium mb-1">Cập nhật tài khoản</h3>
                                    <p class="text-sm text-gray-500">Thông báo về thay đổi tài khoản</p>
                                </div>
                                <div class="relative inline-block w-12 h-6">
                                    <input type="checkbox" id="toggle-account-updates" class="peer opacity-0 w-0 h-0" checked>
                                    <label for="toggle-account-updates" class="absolute cursor-pointer inset-0 bg-gray-300 rounded-full transition-all duration-300 before:absolute before:content-[''] before:h-4 before:w-4 before:left-1 before:bottom-1 before:bg-white before:rounded-full before:transition-all before:duration-300 peer-checked:bg-orange-500 peer-checked:before:translate-x-6"></label>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                                <div>
                                    <h3 class="font-medium mb-1">Bản tin & Cập nhật</h3>
                                    <p class="text-sm text-gray-500">Thông báo về tin tức và cập nhật từ FastFood</p>
                                </div>
                                <div class="relative inline-block w-12 h-6">
                                    <input type="checkbox" id="toggle-newsletter" class="peer opacity-0 w-0 h-0">
                                    <label for="toggle-newsletter" class="absolute cursor-pointer inset-0 bg-gray-300 rounded-full transition-all duration-300 before:absolute before:content-[''] before:h-4 before:w-4 before:left-1 before:bottom-1 before:bg-white before:rounded-full before:transition-all before:duration-300 peer-checked:bg-orange-500 peer-checked:before:translate-x-6"></label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button type="button" class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                Lưu thay đổi
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Privacy Section -->
            <section id="privacy" class="mb-10">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-xl font-bold">Quyền Riêng Tư</h2>
                    </div>
                    
                    <div class="p-6">
                        <p class="text-sm text-gray-500 mb-6">
                            Quản lý cài đặt quyền riêng tư của bạn và kiểm soát dữ liệu cá nhân của bạn.
                        </p>
                        
                        <div class="space-y-6">
                            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                                <div>
                                    <h3 class="font-medium mb-1">Lịch sử đơn hàng</h3>
                                    <p class="text-sm text-gray-500">Cho phép FastFood lưu trữ lịch sử đơn hàng của bạn để đề xuất món ăn phù hợp</p>
                                </div>
                                <div class="relative inline-block w-12 h-6">
                                    <input type="checkbox" id="toggle-order-history" class="peer opacity-0 w-0 h-0" checked>
                                    <label for="toggle-order-history" class="absolute cursor-pointer inset-0 bg-gray-300 rounded-full transition-all duration-300 before:absolute before:content-[''] before:h-4 before:w-4 before:left-1 before:bottom-1 before:bg-white before:rounded-full before:transition-all before:duration-300 peer-checked:bg-orange-500 peer-checked:before:translate-x-6"></label>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                                <div>
                                    <h3 class="font-medium mb-1">Vị trí</h3>
                                    <p class="text-sm text-gray-500">Cho phép FastFood sử dụng vị trí của bạn để tìm cửa hàng gần nhất</p>
                                </div>
                                <div class="relative inline-block w-12 h-6">
                                    <input type="checkbox" id="toggle-location" class="peer opacity-0 w-0 h-0" checked>
                                    <label for="toggle-location" class="absolute cursor-pointer inset-0 bg-gray-300 rounded-full transition-all duration-300 before:absolute before:content-[''] before:h-4 before:w-4 before:left-1 before:bottom-1 before:bg-white before:rounded-full before:transition-all before:duration-300 peer-checked:bg-orange-500 peer-checked:before:translate-x-6"></label>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                                <div>
                                    <h3 class="font-medium mb-1">Cookie</h3>
                                    <p class="text-sm text-gray-500">Cho phép FastFood sử dụng cookie để cải thiện trải nghiệm của bạn</p>
                                </div>
                                <div class="relative inline-block w-12 h-6">
                                    <input type="checkbox" id="toggle-cookies" class="peer opacity-0 w-0 h-0" checked>
                                    <label for="toggle-cookies" class="absolute cursor-pointer inset-0 bg-gray-300 rounded-full transition-all duration-300 before:absolute before:content-[''] before:h-4 before:w-4 before:left-1 before:bottom-1 before:bg-white before:rounded-full before:transition-all before:duration-300 peer-checked:bg-orange-500 peer-checked:before:translate-x-6"></label>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                                <div>
                                    <h3 class="font-medium mb-1">Dữ liệu phân tích</h3>
                                    <p class="text-sm text-gray-500">Cho phép FastFood thu thập dữ liệu phân tích để cải thiện dịch vụ</p>
                                </div>
                                <div class="relative inline-block w-12 h-6">
                                    <input type="checkbox" id="toggle-analytics" class="peer opacity-0 w-0 h-0" checked>
                                    <label for="toggle-analytics" class="absolute cursor-pointer inset-0 bg-gray-300 rounded-full transition-all duration-300 before:absolute before:content-[''] before:h-4 before:w-4 before:left-1 before:bottom-1 before:bg-white before:rounded-full before:transition-all before:duration-300 peer-checked:bg-orange-500 peer-checked:before:translate-x-6"></label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-8">
                            <h3 class="font-bold mb-4">Dữ liệu cá nhân</h3>
                            
                            <div class="space-y-4">
                                <button class="flex items-center text-gray-700 hover:text-orange-500 transition-colors">
                                    <i class="fas fa-download mr-2"></i>
                                    <span>Tải xuống dữ liệu cá nhân</span>
                                </button>
                                
                                <button class="flex items-center text-gray-700 hover:text-orange-500 transition-colors">
                                    <i class="fas fa-eraser mr-2"></i>
                                    <span>Yêu cầu xóa dữ liệu cá nhân</span>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button type="button" class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                Lưu thay đổi
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Payment Methods Section -->
            <section id="payment-methods" class="mb-10">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h2 class="text-xl font-bold">Phương Thức Thanh Toán</h2>
                        <button class="bg-orange-500 hover:bg-orange-600 text-white text-sm px-4 py-2 rounded-lg transition-colors flex items-center">
                            <i class="fas fa-plus mr-2"></i> Thêm mới
                        </button>
                    </div>
                    
                    <div class="p-6">
                        <p class="text-sm text-gray-500 mb-6">
                            Quản lý các phương thức thanh toán của bạn. Bạn có thể thêm, chỉnh sửa hoặc xóa các phương thức thanh toán.
                        </p>
                        
                        <div class="space-y-4">
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-12 h-8 bg-blue-100 rounded flex items-center justify-center mr-4">
                                            <i class="fab fa-cc-visa text-blue-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Visa •••• 4242</p>
                                            <p class="text-xs text-gray-500">Hết hạn: 12/2025</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="bg-green-100 text-green-600 text-xs px-2 py-1 rounded-full mr-4">Mặc định</span>
                                        <div class="flex space-x-2">
                                            <button class="text-gray-500 hover:text-orange-500 transition-colors">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-gray-500 hover:text-red-500 transition-colors">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-12 h-8 bg-red-100 rounded flex items-center justify-center mr-4">
                                            <i class="fab fa-cc-mastercard text-red-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Mastercard •••• 5678</p>
                                            <p class="text-xs text-gray-500">Hết hạn: 08/2024</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <button class="text-xs text-orange-500 hover:text-orange-600 mr-4">
                                            Đặt làm mặc định
                                        </button>
                                        <div class="flex space-x-2">
                                            <button class="text-gray-500 hover:text-orange-500 transition-colors">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-gray-500 hover:text-red-500 transition-colors">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-12 h-8 bg-purple-100 rounded flex items-center justify-center mr-4">
                                            <i class="fas fa-wallet text-purple-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Ví MoMo</p>
                                            <p class="text-xs text-gray-500">Liên kết: 098****321</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <button class="text-xs text-orange-500 hover:text-orange-600 mr-4">
                                            Đặt làm mặc định
                                        </button>
                                        <div class="flex space-x-2">
                                            <button class="text-gray-500 hover:text-orange-500 transition-colors">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-gray-500 hover:text-red-500 transition-colors">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Language & Region Section -->
            <section id="language" class="mb-10">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-xl font-bold">Ngôn Ngữ & Khu Vực</h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-6">
                            <div>
                                <label for="language" class="block text-sm font-medium mb-2">Ngôn ngữ</label>
                                <select id="language" name="language" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="vi" selected>Tiếng Việt</option>
                                    <option value="en">English</option>
                                    <option value="ko">한국어</option>
                                    <option value="ja">日本語</option>
                                    <option value="zh">中文</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="region" class="block text-sm font-medium mb-2">Khu vực</label>
                                <select id="region" name="region" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="vn" selected>Việt Nam</option>
                                    <option value="us">United States</option>
                                    <option value="kr">South Korea</option>
                                    <option value="jp">Japan</option>
                                    <option value="cn">China</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="timezone" class="block text-sm font-medium mb-2">Múi giờ</label>
                                <select id="timezone" name="timezone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="Asia/Ho_Chi_Minh" selected>(GMT+7) Hồ Chí Minh</option>
                                    <option value="Asia/Bangkok">(GMT+7) Bangkok</option>
                                    <option value="Asia/Singapore">(GMT+8) Singapore</option>
                                    <option value="Asia/Tokyo">(GMT+9) Tokyo</option>
                                    <option value="America/New_York">(GMT-5) New York</option>
                                    <option value="Europe/London">(GMT+0) London</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="currency" class="block text-sm font-medium mb-2">Đơn vị tiền tệ</label>
                                <select id="currency" name="currency" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="VND" selected>VND - Việt Nam Đồng</option>
                                    <option value="USD">USD - US Dollar</option>
                                    <option value="EUR">EUR - Euro</option>
                                    <option value="JPY">JPY - Japanese Yen</option>
                                    <option value="KRW">KRW - Korean Won</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button type="button" class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                Lưu thay đổi
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Delete Account Section -->
            <section id="delete-account" class="mb-10">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-xl font-bold text-red-500">Xóa Tài Khoản</h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-red-500 mt-0.5 mr-3"></i>
                                <div>
                                    <h3 class="font-bold text-red-500 mb-1">Cảnh báo: Hành động không thể hoàn tác</h3>
                                    <p class="text-sm text-gray-700">
                                        Khi bạn xóa tài khoản, tất cả dữ liệu của bạn sẽ bị xóa vĩnh viễn và không thể khôi phục. Điều này bao gồm:
                                    </p>
                                    <ul class="text-sm text-gray-700 mt-2 space-y-1 ml-5 list-disc">
                                        <li>Thông tin cá nhân</li>
                                        <li>Lịch sử đơn hàng</li>
                                        <li>Điểm thưởng và ưu đãi</li>
                                        <li>Địa chỉ đã lưu</li>
                                        <li>Phương thức thanh toán</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <form id="delete-account-form" class="space-y-6">
                            <div>
                                <label for="delete_reason" class="block text-sm font-medium mb-2">Lý do xóa tài khoản</label>
                                <select id="delete_reason" name="delete_reason" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="">Chọn lý do</option>
                                    <option value="not_useful">Tôi không thấy hữu ích</option>
                                    <option value="too_expensive">Dịch vụ quá đắt</option>
                                    <option value="bad_experience">Tôi có trải nghiệm không tốt</option>
                                    <option value="found_alternative">Tôi đã tìm thấy dịch vụ thay thế</option>
                                    <option value="privacy_concerns">Tôi lo ngại về quyền riêng tư</option>
                                    <option value="other">Lý do khác</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="delete_feedback" class="block text-sm font-medium mb-2">Phản hồi thêm (không bắt buộc)</label>
                                <textarea id="delete_feedback" name="delete_feedback" rows="4" placeholder="Chia sẻ thêm về lý do bạn muốn xóa tài khoản..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                            </div>
                            
                            <div>
                                <label for="current_password_delete" class="block text-sm font-medium mb-2">Mật khẩu hiện tại <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="password" id="current_password_delete" name="current_password_delete" placeholder="Nhập mật khẩu hiện tại để xác nhận" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 toggle-password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <input type="checkbox" id="confirm_delete" name="confirm_delete" class="mt-1">
                                <label for="confirm_delete" class="ml-2 text-sm text-gray-600">
                                    Tôi hiểu rằng hành động này không thể hoàn tác và tất cả dữ liệu của tôi sẽ bị xóa vĩnh viễn.
                                </label>
                            </div>
                            
                            <div>
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-medium py-3 px-6 rounded-lg transition-colors" disabled id="delete-account-button">
                                    Xóa tài khoản vĩnh viễn
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center" id="confirmation-modal">
    <div class="bg-white rounded-lg p-8 max-w-md w-full">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">Xác nhận xóa tài khoản</h2>
            <p class="text-gray-600 mb-6">
                Bạn có chắc chắn muốn xóa tài khoản? Hành động này không thể hoàn tác và tất cả dữ liệu của bạn sẽ bị xóa vĩnh viễn.
            </p>
            <div class="flex justify-center gap-4">
                <button id="confirm-delete" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-md font-medium transition-colors">
                    Xác nhận xóa
                </button>
                <button id="cancel-delete" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-md font-medium transition-colors">
                    Hủy
                </button>
            </div>
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
                Cài đặt tài khoản của bạn đã được cập nhật thành công.
            </p>
            <button id="close-modal" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-md font-medium transition-colors">
                Đóng
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const passwordInput = this.parentElement.querySelector('input');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
    
    // Password form submission
    const passwordForm = document.getElementById('password-form');
    
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (!currentPassword || !newPassword || !confirmPassword) {
                showToast('Vui lòng điền đầy đủ thông tin');
                return;
            }
            if (newPassword !== confirmPassword) {
                showToast('Mật khẩu mới không khớp');
                return;
            }
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            if (!passwordRegex.test(newPassword)) {
                showToast('Mật khẩu mới không đủ mạnh. Hãy đảm bảo mật khẩu có đủ 8 ký tự, chữ hoa, chữ thường, số và ký tự đặc biệt.');
                return;
            }
            
            const formData = new FormData(this);
            const actionUrl = this.action;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(({ status, body }) => {
                if (status === 200) {
                    showToast(body.message);
                    document.getElementById('success-modal').classList.remove('hidden');
                    passwordForm.reset();
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
                showToast('Lỗi kết nối. Vui lòng thử lại.');
            });
        });
    }
    
    // 2FA toggle
    const toggle2FA = document.getElementById('toggle-2fa');
    const setup2FA = document.getElementById('2fa-setup');
    
    if (toggle2FA && setup2FA) {
        toggle2FA.addEventListener('change', function() {
            if (this.checked) {
                setup2FA.classList.remove('hidden');
            } else {
                setup2FA.classList.add('hidden');
            }
        });
    }
    
    // Delete account form
    const deleteAccountForm = document.getElementById('delete-account-form');
    const confirmDeleteCheckbox = document.getElementById('confirm_delete');
    const deleteAccountButton = document.getElementById('delete-account-button');
    const confirmationModal = document.getElementById('confirmation-modal');
    const confirmDeleteButton = document.getElementById('confirm-delete');
    const cancelDeleteButton = document.getElementById('cancel-delete');
    
    if (confirmDeleteCheckbox && deleteAccountButton) {
        confirmDeleteCheckbox.addEventListener('change', function() {
            deleteAccountButton.disabled = !this.checked;
        });
    }
    
    if (deleteAccountForm && confirmationModal) {
        deleteAccountForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show confirmation modal
            confirmationModal.classList.remove('hidden');
        });
    }
    
    if (confirmDeleteButton) {
        confirmDeleteButton.addEventListener('click', function() {
            // Here you would normally send the request to delete the account
            // For now, we'll just redirect to the login page
            window.location.href = '/login';
        });
    }
    
    if (cancelDeleteButton) {
        cancelDeleteButton.addEventListener('click', function() {
            confirmationModal.classList.add('hidden');
        });
    }
    
    // Close modal
    const closeModalButton = document.getElementById('close-modal');
    const successModal = document.getElementById('success-modal');
    
    if (closeModalButton && successModal) {
        closeModalButton.addEventListener('click', function() {
            successModal.classList.add('hidden');
        });
        
        // Close modal when clicking outside
        successModal.addEventListener('click', function(e) {
            if (e.target === successModal) {
                successModal.classList.add('hidden');
            }
        });
    }
    
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
    
    // Sidebar navigation
    const navLinks = document.querySelectorAll('nav a');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Remove active class from all links
            navLinks.forEach(l => {
                l.classList.remove('bg-orange-50', 'text-orange-500');
                l.classList.add('hover:bg-gray-50');
            });
            
            // Add active class to clicked link
            this.classList.add('bg-orange-50', 'text-orange-500');
            this.classList.remove('hover:bg-gray-50');
        });
    });
});
</script>
@endsection