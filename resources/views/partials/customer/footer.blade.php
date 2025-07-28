<footer class="bg-gray-900 text-gray-300">
    <!-- Top Footer -->
    <style>
        .container-ft {
            max-width: 1240px;
        }
    </style>
    <div class="container-ft mx-auto px-4 pt-16 pb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- About -->
            <div>
                <div class="flex items-center gap-2 mb-6">
                    <div class="relative h-10 w-10">
                        <img src="/placeholder.svg?height=40&width=40" alt="FastFood Logo" class="object-contain">
                    </div>
                    <h3 class="text-xl font-bold text-white">FastFood</h3>
                </div>
                <p class="mb-6">
                    Chúng tôi cung cấp những món ăn nhanh ngon, chất lượng với giá cả hợp lý. Sứ mệnh của chúng tôi là mang
                    đến trải nghiệm ẩm thực tuyệt vời cho mọi khách hàng.
                </p>
                <div class="flex gap-4">
                    <button class="text-gray-300 hover:text-white hover:bg-gray-800 rounded-full p-2">
                        <i class="fab fa-facebook-f h-5 w-5"></i>
                        <span class="sr-only">Facebook</span>
                    </button>
                    <button class="text-gray-300 hover:text-white hover:bg-gray-800 rounded-full p-2">
                        <i class="fab fa-instagram h-5 w-5"></i>
                        <span class="sr-only">Instagram</span>
                    </button>
                    <button class="text-gray-300 hover:text-white hover:bg-gray-800 rounded-full p-2">
                        <i class="fab fa-twitter h-5 w-5"></i>
                        <span class="sr-only">Twitter</span>
                    </button>
                    <button class="text-gray-300 hover:text-white hover:bg-gray-800 rounded-full p-2">
                        <i class="fab fa-youtube h-5 w-5"></i>
                        <span class="sr-only">Youtube</span>
                    </button>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-lg font-bold text-white mb-6">Liên Kết Nhanh</h3>
                <ul class="space-y-3">
                    <li>
                        <a href="/" class="flex items-center {{ request()->is('/') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                            <i class="fas fa-chevron-right h-4 w-4 mr-2 text-orange-500"></i>
                            Trang Chủ
                        </a>
                    </li>
                    <li>
                        <a href="/products" class="flex items-center {{ request()->is('products*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                            <i class="fas fa-chevron-right h-4 w-4 mr-2 text-orange-500"></i>
                            Thực Đơn
                        </a>
                    </li>
                    <li>
                        <a href="/promotions" class="flex items-center {{ request()->is('promotions*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                            <i class="fas fa-chevron-right h-4 w-4 mr-2 text-orange-500"></i>
                            Khuyến Mãi
                        </a>
                    </li>
                    <li>
                        <a href="/about" class="flex items-center {{ request()->is('about*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                            <i class="fas fa-chevron-right h-4 w-4 mr-2 text-orange-500"></i>
                            Về Chúng Tôi
                        </a>
                    </li>
                    <li>
                        <a href="/blog" class="flex items-center {{ request()->is('blog*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                            <i class="fas fa-chevron-right h-4 w-4 mr-2 text-orange-500"></i>
                            Blog
                        </a>
                    </li>
                    <li>
                        <a href="/stores" class="flex items-center {{ request()->is('stores*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                            <i class="fas fa-chevron-right h-4 w-4 mr-2 text-orange-500"></i>
                            Cửa Hàng
                        </a>
                    </li>
                    <li>
                        <a href="/contact" class="flex items-center {{ request()->is('contact*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                            <i class="fas fa-chevron-right h-4 w-4 mr-2 text-orange-500"></i>
                            Liên Hệ
                        </a>
                    </li>
                    <li>
                        <a href="/recruitment" class="flex items-center {{ request()->is('recruitment*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                            <i class="fas fa-chevron-right h-4 w-4 mr-2 text-orange-500"></i>
                            Tuyển dụng
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h3 class="text-lg font-bold text-white mb-6">Liên Hệ</h3>
                <ul class="space-y-4">
                    <li class="flex gap-3">
                        <i class="fas fa-map-marker-alt h-5 w-5 text-orange-500 flex-shrink-0"></i>
                        <span>123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh</span>
                    </li>
                    <li class="flex gap-3">
                        <i class="fas fa-phone h-5 w-5 text-orange-500 flex-shrink-0"></i>
                        <span>1900 1234</span>
                    </li>
                    <li class="flex gap-3">
                        <i class="fas fa-envelope h-5 w-5 text-orange-500 flex-shrink-0"></i>
                        <span>info@fastfood.com</span>
                    </li>
                    <li class="flex gap-3">
                        <i class="fas fa-clock h-5 w-5 text-orange-500 flex-shrink-0"></i>
                        <div>
                            <p>Thứ 2 - Thứ 6: 7:00 - 22:00</p>
                            <p>Thứ 7 - Chủ Nhật: 8:00 - 23:00</p>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div>
                <h3 class="text-lg font-bold text-white mb-6">Đăng Ký Nhận Tin</h3>
                <p class="mb-4">Đăng ký để nhận thông tin khuyến mãi mới nhất từ chúng tôi.</p>
                <div class="space-y-3">
                    <input type="email" placeholder="Email của bạn" class="w-full px-3 py-2 bg-gray-800 border-gray-700 text-white rounded-md focus:ring-orange-500">
                    <button class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-md transition-colors">Đăng Ký</button>
                </div>
            </div>
        </div>

    <!-- Middle Footer - Popular Locations -->
    <div class="border-t border-gray-800 py-8">
        <div class="container mx-auto px-4">
            <h3 class="text-lg font-bold text-white mb-4">Chi Nhánh Phổ Biến</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">
                <a href="#" class="text-sm hover:text-orange-500 transition-colors">TP. Hồ Chí Minh</a>
                <a href="#" class="text-sm hover:text-orange-500 transition-colors">Hà Nội</a>
                <a href="#" class="text-sm hover:text-orange-500 transition-colors">Đà Nẵng</a>
                <a href="#" class="text-sm hover:text-orange-500 transition-colors">Nha Trang</a>
                <a href="#" class="text-sm hover:text-orange-500 transition-colors">Cần Thơ</a>
                <a href="#" class="text-sm hover:text-orange-500 transition-colors">Hải Phòng</a>
                <a href="#" class="text-sm hover:text-orange-500 transition-colors">Huế</a>
                <a href="#" class="text-sm hover:text-orange-500 transition-colors">Vũng Tàu</a>
                <a href="#" class="text-sm hover:text-orange-500 transition-colors">Đà Lạt</a>
                <a href="#" class="text-sm hover:text-orange-500 transition-colors">Phan Thiết</a>
                <a href="#" class="text-sm hover:text-orange-500 transition-colors">Biên Hòa</a>
                <a href="#" class="text-sm hover:text-orange-500 transition-colors">Long Xuyên</a>
            </div>
        </div>
    </div>

    <!-- Bottom Footer -->
    <div class="border-t border-gray-800 py-6">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm mb-4 md:mb-0">
                    © {{ date('Y') }} FastFood. Tất cả các quyền được bảo lưu.
                </p>
                <div class="flex gap-4">
                    <a href="/terms" class="text-sm {{ request()->is('terms*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                        Điều khoản sử dụng
                    </a>
                    <a href="/privacy" class="text-sm {{ request()->is('privacy*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                        Chính sách bảo mật
                    </a>
                    <a href="/faq" class="text-sm {{ request()->is('faq*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                        FAQ
                    </a>
                </div>
            </div>
        </div>
    </div>
        </div>

</footer>