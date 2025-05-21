<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FastFood')</title>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        orange: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        },
                    },
                },
            },
        }
    </script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    @yield('styles')
</head>
<body class="min-h-screen">
    <!-- Navbar -->
    <header class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
        <div class="container mx-auto px-4">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center">
                    <button id="mobile-menu-button" class="md:hidden">
                        <i class="fas fa-bars h-5 w-5"></i>
                        <span class="sr-only">Mở menu</span>
                    </button>

                    <a href="/" class="flex items-center gap-2 ml-4 md:ml-0">
                        <span class="font-bold text-xl text-orange-500">FastFood</span>
                    </a>

                    <nav class="hidden md:flex items-center gap-6 ml-10">
                        <a href="/" class="text-sm font-medium hover:text-orange-500 transition-colors">
                            Trang Chủ
                        </a>
                        <a href="{{ asset('/shop/products') }}" class="text-sm font-medium hover:text-orange-500 transition-colors">
                            Thực Đơn
                        </a>
                        <a href="{{ asset('/promotions') }}" class="text-sm font-medium hover:text-orange-500 transition-colors">
                            Khuyến Mãi
                        </a>
                        <a href="{{ asset('/branchs') }}" class="text-sm font-medium hover:text-orange-500 transition-colors">
                            Cửa Hàng
                        </a>
                        <a href="{{ asset('/about') }}" class="text-sm font-medium hover:text-orange-500 transition-colors">
                            Về Chúng Tôi
                        </a>
                        <a href="{{ asset('/support') }}" class="text-sm font-medium hover:text-orange-500 transition-colors">
                            Hỗ Trợ
                        </a>
                        <a href="{{ asset('/contact') }}" class="text-sm font-medium hover:text-orange-500 transition-colors">
                            Liên Hệ
                        </a>
                    </nav>
                </div>

                <div class="flex items-center gap-4">
                    <div id="search-container" class="relative">
                        <button id="search-button" class="p-2">
                            <ion-icon class="h-6 w-6" name="search-outline"></ion-icon>
                            <span class="sr-only">Tìm kiếm</span>
                        </button>
                        <div id="search-input-container" class="hidden absolute right-0 top-full mt-1 w-64 bg-white shadow-lg rounded-lg p-2 z-50">
                            <div class="flex items-center">
                                <input type="text" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="Tìm kiếm...">
                                <button id="close-search" class="ml-2">
                                    <i class="fas fa-times h-4 w-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <a href="{{ asset('/profile') }}" class="p-2">
                        <ion-icon class="h-6 w-6" name="person-outline"></ion-icon>
                        <span class="sr-only">Tài khoản</span>
                    </a>

                    <a href="{{ asset('/cart') }}" class="relative p-2">
                        <ion-icon class="h-6 w-6" name="cart-outline"></ion-icon>
                        <span class="absolute -top-0 -right-1 h-5 w-5 flex items-center justify-center p-0 bg-orange-500 text-white text-xs rounded-full">3</span>
                        <span class="sr-only">Giỏ hàng</span>
                    </a>

                    <div class="hidden md:flex items-center gap-2">
                        <i class="fas fa-phone h-4 w-4 text-orange-500"></i>
                        <span class="text-sm font-medium">1900 1234</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Menu Sidebar -->
    <div id="mobile-menu" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="bg-white h-full w-[300px] p-4 transform -translate-x-full transition-transform duration-300" id="mobile-menu-content">
            <div class="flex justify-between items-center mb-6">
                <span class="font-bold text-xl text-orange-500">FastFood</span>
                <button id="close-mobile-menu">
                    <i class="fas fa-times h-5 w-5"></i>
                </button>
            </div>
            <nav class="flex flex-col gap-4">
                <a href="/" class="text-lg font-medium hover:text-orange-500 transition-colors">
                    Trang Chủ
                </a>
                <a href="/products" class="text-lg font-medium hover:text-orange-500 transition-colors">
                    Thực Đơn
                </a>
                <a href="/promotions" class="text-lg font-medium hover:text-orange-500 transition-colors">
                    Khuyến Mãi
                </a>
                <a href="/stores" class="text-lg font-medium hover:text-orange-500 transition-colors">
                    Cửa Hàng
                </a>
                <a href="/about" class="text-lg font-medium hover:text-orange-500 transition-colors">
                    Về Chúng Tôi
                </a>
                <a href="/contact" class="text-lg font-medium hover:text-orange-500 transition-colors">
                    Liên Hệ
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300">
        <!-- Top Footer -->
        <div class="container mx-auto px-4 pt-16 pb-8">
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
                            <a href="/" class="flex items-center hover:text-orange-500 transition-colors">
                                <i class="fas fa-chevron-right h-4 w-4 mr-2 text-orange-500"></i>
                                Trang Chủ
                            </a>
                        </li>
                        <li>
                            <a href="/products" class="flex items-center hover:text-orange-500 transition-colors">
                                <i class="fas fa-chevron-right h-4 w-4 mr-2 text-orange-500"></i>
                                Thực Đơn
                            </a>
                        </li>
                        <li>
                            <a href="/promotions" class="flex items-center hover:text-orange-500 transition-colors">
                                <i class="fas fa-chevron-right h-4 w-4 mr-2 text-orange-500"></i>
                                Khuyến Mãi
                            </a>
                        </li>
                        <li>
                            <a href="/about" class="flex items-center hover:text-orange-500 transition-colors">
                                <i class="fas fa-chevron-right h-4 w-4 mr-2 text-orange-500"></i>
                                Về Chúng Tôi
                            </a>
                        </li>
                        <li>
                            <a href="/blog" class="flex items-center hover:text-orange-500 transition-colors">
                                <i class="fas fa-chevron-right h-4 w-4 mr-2 text-orange-500"></i>
                                Blog
                            </a>
                        </li>
                        <li>
                            <a href="/stores" class="flex items-center hover:text-orange-500 transition-colors">
                                <i class="fas fa-chevron-right h-4 w-4 mr-2 text-orange-500"></i>
                                Cửa Hàng
                            </a>
                        </li>
                        <li>
                            <a href="/contact" class="flex items-center hover:text-orange-500 transition-colors">
                                <i class="fas fa-chevron-right h-4 w-4 mr-2 text-orange-500"></i>
                                Liên Hệ
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
                    <div class="mt-6">
                        <h4 class="font-medium text-white mb-2">Tải Ứng Dụng</h4>
                        <div class="flex gap-2">
                            <a href="#">
                                <img src="/placeholder.svg?height=40&width=120" alt="App Store" class="rounded h-10">
                            </a>
                            <a href="#">
                                <img src="/placeholder.svg?height=40&width=120" alt="Google Play" class="rounded h-10">
                            </a>
                        </div>
                    </div>
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
                        <a href="/terms" class="text-sm hover:text-orange-500 transition-colors">
                            Điều khoản sử dụng
                        </a>
                        <a href="/privacy" class="text-sm hover:text-orange-500 transition-colors">
                            Chính sách bảo mật
                        </a>
                        <a href="/faq" class="text-sm hover:text-orange-500 transition-colors">
                            FAQ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const closeMobileMenuButton = document.getElementById('close-mobile-menu');
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuContent = document.getElementById('mobile-menu-content');
            
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.remove('hidden');
                setTimeout(() => {
                    mobileMenuContent.classList.remove('-translate-x-full');
                }, 10);
            });
            
            function closeMobileMenu() {
                mobileMenuContent.classList.add('-translate-x-full');
                setTimeout(() => {
                    mobileMenu.classList.add('hidden');
                }, 300);
            }
            
            closeMobileMenuButton.addEventListener('click', closeMobileMenu);
            
            mobileMenu.addEventListener('click', function(e) {
                if (e.target === mobileMenu) {
                    closeMobileMenu();
                }
            });

            // Search functionality
            const searchButton = document.getElementById('search-button');
            const searchInputContainer = document.getElementById('search-input-container');
            const closeSearchButton = document.getElementById('close-search');
            
            searchButton.addEventListener('click', function() {
                searchInputContainer.classList.toggle('hidden');
                if (!searchInputContainer.classList.contains('hidden')) {
                    searchInputContainer.querySelector('input').focus();
                }
            });
            
            closeSearchButton.addEventListener('click', function() {
                searchInputContainer.classList.add('hidden');
            });

            // Close search when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInputContainer.contains(e.target) && e.target !== searchButton) {
                    searchInputContainer.classList.add('hidden');
                }
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>