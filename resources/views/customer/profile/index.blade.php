@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Tài Khoản Của Tôi')

@section('content')
<style>
    .container {
      max-width: 1280px;
      margin: 0 auto;
   }
</style>
<div class="bg-gradient-to-r from-orange-500 to-red-500 py-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center">
            <div class="relative mb-6 md:mb-0 md:mr-8">
                <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-white p-1 shadow-lg">
                    <img src="/placeholder.svg?height=200&width=200" alt="Ảnh đại diện" class="w-full h-full rounded-full object-cover">
                </div>
                <button class="absolute bottom-0 right-0 bg-orange-600 hover:bg-orange-700 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-md transition-colors">
                    <i class="fas fa-camera"></i>
                </button>
            </div>
            <div class="text-center md:text-left text-white">
                <h1 class="text-3xl md:text-4xl font-bold mb-2">Nguyễn Văn A</h1>
                <p class="text-white/80 mb-4">Thành viên từ Tháng 6, 2023</p>
                <div class="flex flex-wrap justify-center md:justify-start gap-3">
                    <div class="bg-white/20 backdrop-blur-sm px-4 py-1 rounded-full flex items-center">
                        <i class="fas fa-star text-yellow-300 mr-2"></i>
                        <span>Thành viên Vàng</span>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm px-4 py-1 rounded-full flex items-center">
                        <i class="fas fa-medal text-yellow-300 mr-2"></i>
                        <span>120 điểm</span>
                    </div>
                </div>
            </div>
            <div class="mt-6 md:mt-0 md:ml-auto">
                <a href="{{ asset('profile/edit') }}" class="bg-white text-orange-500 hover:bg-orange-50 px-6 py-2 rounded-lg shadow-md transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Chỉnh sửa hồ sơ
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Navigation -->
        <div class="lg:w-1/4">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden sticky top-24">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="font-bold text-lg">Tài khoản của tôi</h2>
                </div>
                <nav class="p-2">
                    <ul class="space-y-1">
                        <li>
                            <a href="#overview" class="flex items-center px-4 py-3 rounded-lg bg-orange-50 text-orange-500 font-medium">
                                <i class="fas fa-home mr-3 w-5 text-center"></i>
                                Tổng quan
                            </a>
                        </li>
                        <li>
                            <a href="#orders" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-shopping-bag mr-3 w-5 text-center"></i>
                                Đơn hàng của tôi
                            </a>
                        </li>
                        <li>
                            <a href="#addresses" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-map-marker-alt mr-3 w-5 text-center"></i>
                                Địa chỉ đã lưu
                            </a>
                        </li>
                        <li>
                            <a href="#favorites" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-heart mr-3 w-5 text-center"></i>
                                Món ăn yêu thích
                            </a>
                        </li>
                        <li>
                            <a href="#rewards" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-gift mr-3 w-5 text-center"></i>
                                Điểm thưởng & Ưu đãi
                            </a>
                        </li>
                        <li>
                            <a href="#payment" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-credit-card mr-3 w-5 text-center"></i>
                                Phương thức thanh toán
                            </a>
                        </li>
                        <li>
                            <a href="{{ asset('profile/setting') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-cog mr-3 w-5 text-center"></i>
                                Cài đặt tài khoản
                            </a>
                        </li>
                        <li class="border-t border-gray-100 mt-2 pt-2">
                            <a href="/logout" class="flex items-center px-4 py-3 rounded-lg text-red-500 hover:bg-red-50 transition-colors">
                                <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>
                                Đăng xuất
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:w-3/4">
            <!-- Overview Section -->
            <section id="overview" class="mb-10">
                <h2 class="text-2xl font-bold mb-6">Tổng Quan</h2>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-shopping-bag text-blue-500"></i>
                        </div>
                        <h3 class="text-3xl font-bold mb-1">12</h3>
                        <p class="text-gray-500 text-sm">Đơn hàng</p>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-medal text-green-500"></i>
                        </div>
                        <h3 class="text-3xl font-bold mb-1">120</h3>
                        <p class="text-gray-500 text-sm">Điểm thưởng</p>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-ticket-alt text-purple-500"></i>
                        </div>
                        <h3 class="text-3xl font-bold mb-1">3</h3>
                        <p class="text-gray-500 text-sm">Voucher</p>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-heart text-red-500"></i>
                        </div>
                        <h3 class="text-3xl font-bold mb-1">8</h3>
                        <p class="text-gray-500 text-sm">Yêu thích</p>
                    </div>
                </div>
                
                <!-- Membership Progress -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold mb-1">Thành viên Vàng</h3>
                            <p class="text-gray-500 text-sm">Còn 80 điểm nữa để lên hạng Bạch Kim</p>
                        </div>
                        <div class="mt-2 md:mt-0">
                            <span class="text-sm font-medium">120/200 điểm</span>
                        </div>
                    </div>
                    
                    <div class="relative h-4 bg-gray-100 rounded-full overflow-hidden mb-2">
                        <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-yellow-400 to-yellow-500" style="width: 60%"></div>
                    </div>
                    
                    <div class="flex justify-between text-xs text-gray-500">
                        <div class="flex flex-col items-center">
                            <div class="w-4 h-4 rounded-full bg-gray-200 mb-1 flex items-center justify-center">
                                <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                            </div>
                            <span>Bạc</span>
                            <span>0</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-4 h-4 rounded-full bg-yellow-200 mb-1 flex items-center justify-center">
                                <div class="w-2 h-2 rounded-full bg-yellow-400"></div>
                            </div>
                            <span>Vàng</span>
                            <span>100</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-4 h-4 rounded-full bg-gray-200 mb-1 flex items-center justify-center">
                                <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                            </div>
                            <span>Bạch Kim</span>
                            <span>200</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-4 h-4 rounded-full bg-gray-200 mb-1 flex items-center justify-center">
                                <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                            </div>
                            <span>Kim Cương</span>
                            <span>500</span>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Orders -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold">Đơn Hàng Gần Đây</h3>
                        <a href="#orders" class="text-orange-500 hover:underline text-sm font-medium">Xem tất cả</a>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="border border-gray-100 rounded-lg p-4 hover:shadow-sm transition-shadow">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <span class="text-sm text-gray-500">Mã đơn hàng: #FF12345</span>
                                    <h4 class="font-medium">2 x Burger Gà Cay + 1 x Khoai Tây Chiên</h4>
                                </div>
                                <span class="bg-green-100 text-green-600 text-xs font-medium px-2 py-1 rounded-full">Đã giao</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="text-gray-500 text-sm">
                                    <i class="far fa-calendar-alt mr-1"></i> 15/05/2023
                                </div>
                                <div class="font-medium">120.000đ</div>
                            </div>
                        </div>
                        
                        <div class="border border-gray-100 rounded-lg p-4 hover:shadow-sm transition-shadow">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <span class="text-sm text-gray-500">Mã đơn hàng: #FF12344</span>
                                    <h4 class="font-medium">1 x Pizza Hải Sản + 2 x Coca Cola</h4>
                                </div>
                                <span class="bg-green-100 text-green-600 text-xs font-medium px-2 py-1 rounded-full">Đã giao</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="text-gray-500 text-sm">
                                    <i class="far fa-calendar-alt mr-1"></i> 10/05/2023
                                </div>
                                <div class="font-medium">185.000đ</div>
                            </div>
                        </div>
                        
                        <div class="border border-gray-100 rounded-lg p-4 hover:shadow-sm transition-shadow">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <span class="text-sm text-gray-500">Mã đơn hàng: #FF12343</span>
                                    <h4 class="font-medium">2 x Mì Ý Sốt Bò Bằm + 1 x Salad</h4>
                                </div>
                                <span class="bg-green-100 text-green-600 text-xs font-medium px-2 py-1 rounded-full">Đã giao</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="text-gray-500 text-sm">
                                    <i class="far fa-calendar-alt mr-1"></i> 05/05/2023
                                </div>
                                <div class="font-medium">150.000đ</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Personal Information Section -->
            <section id="personal-info" class="mb-10">
                <h2 class="text-2xl font-bold mb-6">Thông Tin Cá Nhân</h2>
                
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold">Thông tin chi tiết</h3>
                        <button class="text-orange-500 hover:underline text-sm font-medium flex items-center">
                            <i class="fas fa-edit mr-1"></i> Chỉnh sửa
                        </button>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm text-gray-500 mb-1">Họ và tên</h4>
                            <p class="font-medium">Nguyễn Văn A</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm text-gray-500 mb-1">Email</h4>
                            <p class="font-medium">nguyenvana@example.com</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm text-gray-500 mb-1">Số điện thoại</h4>
                            <p class="font-medium">0987654321</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm text-gray-500 mb-1">Ngày sinh</h4>
                            <p class="font-medium">01/01/1990</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm text-gray-500 mb-1">Giới tính</h4>
                            <p class="font-medium">Nam</p>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Saved Addresses Section -->
            <section id="addresses" class="mb-10">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Địa Chỉ Đã Lưu</h2>
                    <button class="bg-orange-500 hover:bg-orange-600 text-white text-sm px-4 py-2 rounded-lg transition-colors flex items-center">
                        <i class="fas fa-plus mr-2"></i> Thêm địa chỉ mới
                    </button>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="font-bold mb-1">Nhà riêng</h3>
                                <p class="text-gray-500 text-sm">Mặc định</p>
                            </div>
                            <div class="flex space-x-2">
                                <button class="text-gray-500 hover:text-orange-500 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-gray-500 hover:text-red-500 transition-colors">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <p class="font-medium">Nguyễn Văn A</p>
                            <p class="text-gray-600">0987654321</p>
                            <p class="text-gray-600">123 Đường ABC, Phường XYZ, Quận 1, TP. Hồ Chí Minh</p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="font-bold mb-1">Văn phòng</h3>
                            </div>
                            <div class="flex space-x-2">
                                <button class="text-gray-500 hover:text-orange-500 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-gray-500 hover:text-red-500 transition-colors">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <p class="font-medium">Nguyễn Văn A</p>
                            <p class="text-gray-600">0987654321</p>
                            <p class="text-gray-600">456 Đường DEF, Phường UVW, Quận 2, TP. Hồ Chí Minh</p>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Favorite Items Section -->
            <section id="favorites" class="mb-10">
                <h2 class="text-2xl font-bold mb-6">Món Ăn Yêu Thích</h2>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden group">
                        <div class="relative h-48">
                            <img src="/placeholder.svg?height=400&width=600" alt="Burger Gà Cay" class="w-full h-full object-cover">
                            <button class="absolute top-3 right-3 bg-white rounded-full w-8 h-8 flex items-center justify-center text-red-500 shadow-md">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold mb-1">Burger Gà Cay</h3>
                            <p class="text-gray-500 text-sm mb-2">Burger với thịt gà cay, rau xà lách và sốt đặc biệt</p>
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-orange-500">65.000đ</span>
                                <button class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-lg text-sm transition-colors">
                                    Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden group">
                        <div class="relative h-48">
                            <img src="/placeholder.svg?height=400&width=600" alt="Pizza Hải Sản" class="w-full h-full object-cover">
                            <button class="absolute top-3 right-3 bg-white rounded-full w-8 h-8 flex items-center justify-center text-red-500 shadow-md">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold mb-1">Pizza Hải Sản</h3>
                            <p class="text-gray-500 text-sm mb-2">Pizza với hải sản tươi ngon, phô mai và sốt cà chua</p>
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-orange-500">150.000đ</span>
                                <button class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-lg text-sm transition-colors">
                                    Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden group">
                        <div class="relative h-48">
                            <img src="/placeholder.svg?height=400&width=600" alt="Mì Ý Sốt Bò Bằm" class="w-full h-full object-cover">
                            <button class="absolute top-3 right-3 bg-white rounded-full w-8 h-8 flex items-center justify-center text-red-500 shadow-md">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold mb-1">Mì Ý Sốt Bò Bằm</h3>
                            <p class="text-gray-500 text-sm mb-2">Mì Ý với sốt bò bằm đậm đà và phô mai Parmesan</p>
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-orange-500">85.000đ</span>
                                <button class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-lg text-sm transition-colors">
                                    Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Rewards Section -->
            <section id="rewards" class="mb-10">
                <h2 class="text-2xl font-bold mb-6">Điểm Thưởng & Ưu Đãi</h2>
                
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-bold mb-1">Điểm thưởng của bạn</h3>
                            <p class="text-gray-500">Sử dụng điểm để đổi lấy ưu đãi hấp dẫn</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <div class="flex items-center bg-orange-50 px-4 py-2 rounded-lg">
                                <i class="fas fa-medal text-orange-500 mr-2"></i>
                                <span class="text-2xl font-bold text-orange-500">120</span>
                                <span class="text-gray-500 ml-2">điểm</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-100 pt-6">
                        <h4 class="font-bold mb-4">Lịch sử điểm thưởng</h4>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium">Đơn hàng #FF12345</p>
                                    <p class="text-sm text-gray-500">15/05/2023</p>
                                </div>
                                <span class="text-green-500 font-medium">+12 điểm</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium">Đơn hàng #FF12344</p>
                                    <p class="text-sm text-gray-500">10/05/2023</p>
                                </div>
                                <span class="text-green-500 font-medium">+18 điểm</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium">Đổi voucher giảm giá 50.000đ</p>
                                    <p class="text-sm text-gray-500">01/05/2023</p>
                                </div>
                                <span class="text-red-500 font-medium">-50 điểm</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-bold mb-6">Voucher của bạn</h3>
                    
                    <div class="space-y-4">
                        <div class="border border-dashed border-orange-200 rounded-lg p-4 bg-orange-50">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <h4 class="font-bold text-lg mb-1">Giảm 50.000đ</h4>
                                    <p class="text-gray-600 text-sm">Áp dụng cho đơn hàng từ 200.000đ</p>
                                    <p class="text-gray-500 text-xs mt-2">Hết hạn: 30/06/2023</p>
                                </div>
                                <div class="flex flex-col items-center">
                                    <span class="bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full mb-2">FAST50K</span>
                                    <button class="text-orange-500 border border-orange-500 hover:bg-orange-50 px-4 py-1 rounded-lg text-sm transition-colors">
                                        Sử dụng ngay
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border border-dashed border-orange-200 rounded-lg p-4 bg-orange-50">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <h4 class="font-bold text-lg mb-1">Freeship</h4>
                                    <p class="text-gray-600 text-sm">Miễn phí giao hàng cho đơn từ 100.000đ</p>
                                    <p class="text-gray-500 text-xs mt-2">Hết hạn: 15/06/2023</p>
                                </div>
                                <div class="flex flex-col items-center">
                                    <span class="bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full mb-2">FREESHIP</span>
                                    <button class="text-orange-500 border border-orange-500 hover:bg-orange-50 px-4 py-1 rounded-lg text-sm transition-colors">
                                        Sử dụng ngay
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border border-dashed border-orange-200 rounded-lg p-4 bg-orange-50">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <h4 class="font-bold text-lg mb-1">Giảm 15%</h4>
                                    <p class="text-gray-600 text-sm">Áp dụng cho tất cả các loại pizza</p>
                                    <p class="text-gray-500 text-xs mt-2">Hết hạn: 20/06/2023</p>
                                </div>
                                <div class="flex flex-col items-center">
                                    <span class="bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full mb-2">PIZZA15</span>
                                    <button class="text-orange-500 border border-orange-500 hover:bg-orange-50 px-4 py-1 rounded-lg text-sm transition-colors">
                                        Sử dụng ngay
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center" id="edit-profile-modal">
    <div class="bg-white rounded-lg p-8 max-w-2xl w-full">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Chỉnh Sửa Hồ Sơ</h2>
            <button id="close-modal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="edit-profile-form" class="space-y-6">
            <div class="flex flex-col items-center mb-6">
                <div class="relative mb-4">
                    <div class="w-24 h-24 rounded-full bg-gray-200 overflow-hidden">
                        <img src="/placeholder.svg?height=200&width=200" alt="Ảnh đại diện" class="w-full h-full object-cover">
                    </div>
                    <button class="absolute bottom-0 right-0 bg-orange-500 hover:bg-orange-600 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-md transition-colors">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
                <p class="text-sm text-gray-500">Nhấn vào biểu tượng máy ảnh để thay đổi ảnh đại diện</p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="full_name" class="block text-sm font-medium mb-2">Họ và tên</label>
                    <input type="text" id="full_name" name="full_name" value="Nguyễn Văn A" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium mb-2">Email</label>
                    <input type="email" id="email" name="email" value="nguyenvana@example.com" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium mb-2">Số điện thoại</label>
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
            
            <div class="border-t border-gray-200 pt-6">
                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit profile button
    const editProfileButtons = document.querySelectorAll('button:contains("Chỉnh sửa hồ sơ")');
    const editProfileModal = document.getElementById('edit-profile-modal');
    const closeModalButton = document.getElementById('close-modal');
    const editProfileForm = document.getElementById('edit-profile-form');
    
    if (editProfileButtons.length > 0) {
        editProfileButtons.forEach(button => {
            button.addEventListener('click', function() {
                editProfileModal.classList.remove('hidden');
            });
        });
    }
    
    // Close modal
    if (closeModalButton) {
        closeModalButton.addEventListener('click', function() {
            editProfileModal.classList.add('hidden');
        });
    }
    
    // Close modal when clicking outside
    if (editProfileModal) {
        editProfileModal.addEventListener('click', function(e) {
            if (e.target === editProfileModal) {
                editProfileModal.classList.add('hidden');
            }
        });
    }
    
    // Form submission
    if (editProfileForm) {
        editProfileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Here you would normally send the data to the server
            // For now, we'll just close the modal
            editProfileModal.classList.add('hidden');
            
            // Show success message
            showToast('Thông tin hồ sơ đã được cập nhật');
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
    
    // Fix for :contains selector which is not standard
    if (!Element.prototype.matches) {
        Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
    }
    
    if (!NodeList.prototype.forEach) {
        NodeList.prototype.forEach = Array.prototype.forEach;
    }
    
    // Helper function to find elements containing text
    function getElementsContainingText(selector, text) {
        const elements = document.querySelectorAll(selector);
        return Array.from(elements).filter(element => element.textContent.includes(text));
    }
    
    // Use this instead of the :contains selector
    const editButtons = getElementsContainingText('button', 'Chỉnh sửa hồ sơ');
    if (editButtons.length > 0) {
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (editProfileModal) {
                    editProfileModal.classList.remove('hidden');
                }
            });
        });
    }
});
</script>
@endsection