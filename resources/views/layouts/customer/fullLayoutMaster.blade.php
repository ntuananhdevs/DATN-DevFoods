<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FastFood')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css"/>
    <!-- Notification Styles -->
    <style>
        @keyframes slideInDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideOutUp {
            from {
                transform: translateY(0);
                opacity: 1;
            }
            to {
                transform: translateY(-100%);
                opacity: 0;
            }
        }

        @keyframes progressBar {
            from {
                width: 100%;
            }
            to {
                width: 0%;
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .notification-alert {
            animation: slideInDown 0.5s ease-out forwards;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .notification-alert.hide {
            animation: slideOutUp 0.5s ease-in forwards;
        }

        .notification-alert:hover .progress-bar {
            animation-play-state: paused;
        }

        .notification-icon {
            animation: pulse 2s infinite;
        }

        .notification-alert:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% {
                transform: translate3d(0,0,0);
            }
            40%, 43% {
                transform: translate3d(0, -30px, 0);
            }
            70% {
                transform: translate3d(0, -15px, 0);
            }
            90% {
                transform: translate3d(0, -4px, 0);
            }
        }
        
        @keyframes ping {
            75%, 100% {
                transform: scale(2);
                opacity: 0;
            }
        }

        #chatToggleBtn {
            z-index: 1000;
        }
        
        .animate-bounce {
            animation: bounce 1s infinite;
        }
        
        .animate-ping {
            animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
        
        .typing-dot {
            animation: bounce 1.4s infinite ease-in-out both;
        }
        
        .typing-dot:nth-child(1) { animation-delay: -0.32s; }
        .typing-dot:nth-child(2) { animation-delay: -0.16s; }
        
        .chat-popup {
            transform: translateY(100%);
            opacity: 0;
            transition: all 0.3s ease-in-out;
        }
        
        .chat-popup.show {
            transform: translateY(0);
            opacity: 1;
        }
        
        .message-enter {
            animation: messageSlideIn 0.3s ease-out;
        }
        
        @keyframes messageSlideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .emoji-picker {
            transform: scale(0.8);
            opacity: 0;
            transition: all 0.2s ease-in-out;
        }
        
        .emoji-picker.show {
            transform: scale(1);
            opacity: 1;
        }
        
        .line-clamp-1 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
        }
        
        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }
        
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
    
    <!-- Custom CSS -->
    @yield('styles')
</head>
<body class="min-h-screen">
    <!-- Notification Container -->
    <div id="notificationContainer" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-md px-4">
        @if(session('success'))
        <div class="notification-alert bg-white border-l-4 border-green-500 rounded-lg overflow-hidden mb-4 transition-all duration-300" 
             data-type="success" 
             id="successNotification">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center notification-icon">
                            <i class="fas fa-check text-green-500 text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-900">Thành công!</h3>
                            <button class="close-notification text-gray-400 hover:text-gray-600 transition-colors ml-2" 
                                    data-target="successNotification">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
            <!-- Progress bar -->
            <div class="h-1 bg-gray-100">
                <div class="h-full bg-green-500 progress-bar" style="animation: progressBar 5s linear forwards;"></div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="notification-alert bg-white border-l-4 border-red-500 rounded-lg overflow-hidden mb-4 transition-all duration-300" 
             data-type="error" 
             id="errorNotification">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center notification-icon">
                            <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-900">Có lỗi xảy ra!</h3>
                            <button class="close-notification text-gray-400 hover:text-gray-600 transition-colors ml-2" 
                                    data-target="errorNotification">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
            <!-- Progress bar -->
            <div class="h-1 bg-gray-100">
                <div class="h-full bg-red-500 progress-bar" style="animation: progressBar 5s linear forwards;"></div>
            </div>
        </div>
        @endif

        @if(session('warning'))
        <div class="notification-alert bg-white border-l-4 border-orange-500 rounded-lg overflow-hidden mb-4 transition-all duration-300" 
             data-type="warning" 
             id="warningNotification">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center notification-icon">
                            <i class="fas fa-exclamation text-orange-500 text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-900">Cảnh báo!</h3>
                            <button class="close-notification text-gray-400 hover:text-gray-600 transition-colors ml-2" 
                                    data-target="warningNotification">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">{{ session('warning') }}</p>
                    </div>
                </div>
            </div>
            <!-- Progress bar -->
            <div class="h-1 bg-gray-100">
                <div class="h-full bg-orange-500 progress-bar" style="animation: progressBar 5s linear forwards;"></div>
            </div>
        </div>
        @endif

        @if(session('info'))
        <div class="notification-alert bg-white border-l-4 border-blue-500 rounded-lg overflow-hidden mb-4 transition-all duration-300" 
             data-type="info" 
             id="infoNotification">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center notification-icon">
                            <i class="fas fa-info text-blue-500 text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-900">Thông tin!</h3>
                            <button class="close-notification text-gray-400 hover:text-gray-600 transition-colors ml-2" 
                                    data-target="infoNotification">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">{{ session('info') }}</p>
                    </div>
                </div>
            </div>
            <!-- Progress bar -->
            <div class="h-1 bg-gray-100">
                <div class="h-full bg-blue-500 progress-bar" style="animation: progressBar 5s linear forwards;"></div>
            </div>
        </div>
        @endif
    </div>

    <!-- Navbar -->
    <header class="sticky top-0 z-40 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
        <div class="container mx-auto px-4">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center">
                    <button id="mobile-menu-button" class="md:hidden">
                        <i class="fas fa-bars h-5 w-5"></i>
                        <span class="sr-only">Mở menu</span>
                    </button>

                    <a href="/" class="flex items-center gap-2 ml-4 md:ml-0">
                        <span class="font-bold text-xl text-orange-500">PolyCrispyWings</span>
                    </a>

                    <nav class="hidden md:flex items-center gap-6 ml-10">
                        <a href="/" class="text-sm font-medium {{ request()->is('/') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                            Trang Chủ
                        </a>
                        <a href="{{ asset('/shop/products') }}" class="text-sm font-medium {{ request()->is('shop/products*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                            Thực Đơn
                        </a>
                        <a href="{{ asset('/promotions') }}" class="text-sm font-medium {{ request()->is('promotions*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                            Khuyến Mãi
                        </a>
                        <a href="{{ asset('/branchs') }}" class="text-sm font-medium {{ request()->is('branchs*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                            Cửa Hàng
                        </a>
                        <a href="{{ asset('/about') }}" class="text-sm font-medium {{ request()->is('about*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                            Về Chúng Tôi
                        </a>
                        <a href="{{ asset('/support') }}" class="text-sm font-medium {{ request()->is('support*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                            Hỗ Trợ
                        </a>
                        <a href="{{ asset('/contact') }}" class="text-sm font-medium {{ request()->is('contact*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                            Liên Hệ
                        </a>
                        <a href="{{ asset('/hiring-driver') }}" class="text-sm font-medium {{ request()->is('hiring-driver*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                            Tuyển dụng
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

                    <div id="wishlist-container" class="relative">
                        <a href="{{ route('wishlist.index') }}" class="relative">
                            <ion-icon class="h-6 w-6" name="heart-outline"></ion-icon>
                            <span class="absolute bottom-4 left-3 bg-red-500 text-white rounded-full h-4 w-4 text-xs flex items-center justify-center">
                                {{ auth()->check() ? auth()->user()->wishlist->count() : 0 }}
                            </span>
                        </a>
                    </div>

                    @auth
                        <div class="relative" id="user-dropdown-container">
                            <button class="flex items-center p-2" id="user-dropdown-button">
                                <ion-icon class="h-6 w-6" name="person-outline"></ion-icon>
                                <span class="ml-2 text-sm">{{ Auth::user()->full_name }}</span>
                                <ion-icon class="h-4 w-4 ml-1" name="chevron-down-outline"></ion-icon>
                            </button>
                            <div class="absolute right-0 top-full mt-1 w-48 bg-white shadow-lg rounded-lg py-2 z-50 hidden dropdown-menu" id="user-dropdown-menu">
                                <a href="{{ route('customer.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Tài khoản của tôi
                                </a>
                                <a href="{{ route('customer.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Chỉnh sửa hồ sơ
                                </a>
                                <a href="{{ route('customer.profile.setting') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Cài đặt
                                </a>
                                <form action="{{ route('customer.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('customer.login') }}" class="p-2 flex items-center">
                            <ion-icon class="h-6 w-6" name="person-outline"></ion-icon>
                            <span class="ml-2 text-sm">Đăng nhập</span>
                        </a>
                        
                    @endauth

                    <a href="{{ asset('/cart') }}" class="relative p-2">
                        <ion-icon class="h-6 w-6" name="cart-outline"></ion-icon>
                        <span id="cart-counter" class="absolute -top-0 -right-1 h-5 w-5 flex items-center justify-center p-0 bg-orange-500 text-white text-xs rounded-full">{{ session('cart_count', 0) }}</span>
                        <span class="sr-only">Giỏ hàng</span>
                    </a>

                    
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
                <a href="/" class="text-lg font-medium {{ request()->is('/') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                    Trang Chủ
                </a>
                <a href="/products" class="text-lg font-medium {{ request()->is('products*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                    Thực Đơn
                </a>
                <a href="/promotions" class="text-lg font-medium {{ request()->is('promotions*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                    Khuyến Mãi
                </a>
                <a href="/stores" class="text-lg font-medium {{ request()->is('stores*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                    Cửa Hàng
                </a>
                <a href="/about" class="text-lg font-medium {{ request()->is('about*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                    Về Chúng Tôi
                </a>
                <a href="/contact" class="text-lg font-medium {{ request()->is('contact*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                    Liên Hệ
                </a>
                <a href="/recruitment" class="text-lg font-medium {{ request()->is('recruitment*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                    Tuyển dụng
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <main>
        @yield('content')

        <!-- Chat Widget -->
        @include('partials.customer.chat-widget')
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
    </footer>

    <!-- JavaScript -->
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script>
        // Fixed JavaScript for dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Show branch selection modal automatically if no branch is selected
            const branchModal = document.getElementById('branch-selector-modal');
            
            if (branchModal) {
                // Check if there's no selected branch in session
                const hasSelectedBranch = {{ session()->has('selected_branch') ? 'true' : 'false' }};
                
                if (!hasSelectedBranch) {
                    // Show the modal automatically
                    branchModal.style.display = 'flex';
                    document.body.classList.add('overflow-hidden'); // Prevent body scrolling
                }
            }

            // Notification functionality
            const notifications = document.querySelectorAll('.notification-alert');
            
            notifications.forEach((notification, index) => {
                // Auto dismiss after 5 seconds
                setTimeout(() => {
                    dismissNotification(notification);
                }, 5000);
            });
            
            // Close button functionality
            document.querySelectorAll('.close-notification').forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const notification = document.getElementById(targetId);
                    dismissNotification(notification);
                });
            });
            
            // Pause auto-dismiss on hover
            notifications.forEach(notification => {
                notification.addEventListener('mouseenter', function() {
                    const progressBar = this.querySelector('.progress-bar');
                    if (progressBar) {
                        progressBar.style.animationPlayState = 'paused';
                    }
                });
                
                notification.addEventListener('mouseleave', function() {
                    const progressBar = this.querySelector('.progress-bar');
                    if (progressBar) {
                        progressBar.style.animationPlayState = 'running';
                    }
                });
            });
            
            function dismissNotification(notification) {
                if (notification) {
                    notification.classList.add('hide');
                    setTimeout(() => {
                        notification.remove();
                    }, 500);
                }
            }
            
            // Click to dismiss functionality
            notifications.forEach(notification => {
                notification.addEventListener('click', function(e) {
                    if (!e.target.closest('.close-notification')) {
                        dismissNotification(this);
                    }
                });
            });

            // Mobile menu functionality - FIXED
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const closeMobileMenuButton = document.getElementById('close-mobile-menu');
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuContent = document.getElementById('mobile-menu-content');
            
            if (mobileMenuButton && mobileMenu && mobileMenuContent) {
                mobileMenuButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
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
                
                if (closeMobileMenuButton) {
                    closeMobileMenuButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        closeMobileMenu();
                    });
                }
                
                mobileMenu.addEventListener('click', function(e) {
                    if (e.target === mobileMenu) {
                        closeMobileMenu();
                    }
                });
            }
            
            // User dropdown functionality - FIXED
            const userDropdownButton = document.getElementById('user-dropdown-button');
            const userDropdownMenu = document.getElementById('user-dropdown-menu');
            
            if (userDropdownButton && userDropdownMenu) {
                // Remove any existing event listeners by cloning the button
                const newUserDropdownButton = userDropdownButton.cloneNode(true);
                userDropdownButton.parentNode.replaceChild(newUserDropdownButton, userDropdownButton);
                
                newUserDropdownButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Close all other dropdowns first
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        if (menu !== userDropdownMenu) {
                            menu.classList.add('hidden');
                        }
                    });
                    
                    // Close search dropdown
                    const searchContainer = document.getElementById('search-input-container');
                    if (searchContainer) {
                        searchContainer.classList.add('hidden');
                    }
                    
                    // Toggle current dropdown
                    userDropdownMenu.classList.toggle('hidden');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!newUserDropdownButton.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                        userDropdownMenu.classList.add('hidden');
                    }
                });
                
                // Prevent dropdown from closing when clicking inside
                userDropdownMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
            
            // Search functionality - FIXED
            const searchButton = document.getElementById('search-button');
            const searchInputContainer = document.getElementById('search-input-container');
            const closeSearchButton = document.getElementById('close-search');
            
            if (searchButton && searchInputContainer) {
                // Remove any existing event listeners by cloning the button
                const newSearchButton = searchButton.cloneNode(true);
                searchButton.parentNode.replaceChild(newSearchButton, searchButton);
                
                newSearchButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Close other dropdowns
                    const userDropdown = document.getElementById('user-dropdown-menu');
                    if (userDropdown) {
                        userDropdown.classList.add('hidden');
                    }
                    
                    // Toggle search container
                    searchInputContainer.classList.toggle('hidden');
                    if (!searchInputContainer.classList.contains('hidden')) {
                        const searchInput = searchInputContainer.querySelector('input');
                        if (searchInput) {
                            setTimeout(() => searchInput.focus(), 100);
                        }
                    }
                });
                
                if (closeSearchButton) {
                    closeSearchButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        searchInputContainer.classList.add('hidden');
                    });
                }

                // Close search when clicking outside
                document.addEventListener('click', function(e) {
                    if (!searchInputContainer.contains(e.target) && !newSearchButton.contains(e.target)) {
                        searchInputContainer.classList.add('hidden');
                    }
                });
                
                // Prevent search container from closing when clicking inside
                searchInputContainer.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }

            // Branch selector button - FIXED
            const branchSelectorButton = document.getElementById('branch-selector-button');
            
            if (branchSelectorButton && branchModal) {
                branchSelectorButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    branchModal.style.display = 'flex';
                    document.body.classList.add('overflow-hidden');
                });
            }

            // General dropdown close functionality
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    // Close all dropdowns on Escape key
                    document.querySelectorAll('.dropdown-menu, #search-input-container').forEach(element => {
                        element.classList.add('hidden');
                    });
                }
            });
        });

        // Function to show notifications programmatically
        function showToast(message, type = 'info', duration = 5000) {
            const container = document.getElementById('notificationContainer');
            const notificationId = 'notification_' + Date.now();
            
            const colors = {
                success: { bg: 'green', icon: 'check' },
                error: { bg: 'red', icon: 'exclamation-triangle' },
                warning: { bg: 'orange', icon: 'exclamation' },
                info: { bg: 'blue', icon: 'info' }
            };
            
            const color = colors[type] || colors.info;
            
            const notificationHTML = `
                <div class="notification-alert bg-white border-l-4 border-${color.bg}-500 rounded-lg overflow-hidden mb-4 transition-all duration-300" 
                     data-type="${type}" 
                     id="${notificationId}">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-${color.bg}-100 rounded-full flex items-center justify-center notification-icon">
                                    <i class="fas fa-${color.icon} text-${color.bg}-500 text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-bold text-gray-900">${type.charAt(0).toUpperCase() + type.slice(1)}</h3>
                                    <button class="close-notification text-gray-400 hover:text-gray-600 transition-colors ml-2" 
                                            data-target="${notificationId}">
                                        <i class="fas fa-times text-sm"></i>
                                    </button>
                                </div>
                                <p class="mt-1 text-sm text-gray-600">${message}</p>
                            </div>
                        </div>
                    </div>
                    <div class="h-1 bg-gray-100">
                        <div class="h-full bg-${color.bg}-500 progress-bar" style="animation: progressBar ${duration}ms linear forwards;"></div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', notificationHTML);
            
            const notification = document.getElementById(notificationId);
            
            // Auto dismiss
            setTimeout(() => {
                dismissNotification(notification);
            }, duration);
            
            // Add event listeners
            notification.querySelector('.close-notification').addEventListener('click', function() {
                dismissNotification(notification);
            });
            
            notification.addEventListener('click', function(e) {
                if (!e.target.closest('.close-notification')) {
                    dismissNotification(this);
                }
            });
            
            notification.addEventListener('mouseenter', function() {
                const progressBar = this.querySelector('.progress-bar');
                if (progressBar) {
                    progressBar.style.animationPlayState = 'paused';
                }
            });
            
            notification.addEventListener('mouseleave', function() {
                const progressBar = this.querySelector('.progress-bar');
                if (progressBar) {
                    progressBar.style.animationPlayState = 'running';
                }
            });
            
            function dismissNotification(notif) {
                if (notif) {
                    notif.classList.add('hide');
                    setTimeout(() => notif.remove(), 500);
                }
            }
        }
        
        // Make showToast globally available
        window.showToast = showToast;
    </script>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        // CSRF Token setup for AJAX
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    
    <!-- Scripts -->
    <script>
        // Global function to update the cart counter
        window.updateCartCount = function(count) {
            // Save the cart count in localStorage for consistency between pages
            localStorage.setItem('cart_count', count);
            
            // Update all cart counter elements on the page
            const counters = document.querySelectorAll('#cart-counter');
            counters.forEach(counter => {
                // Update the counter with animation
                counter.textContent = count;
                
                // Add animation class
                counter.classList.add('animate-bounce', 'bg-green-500');
                setTimeout(() => {
                    counter.classList.remove('animate-bounce', 'bg-green-500');
                    counter.classList.add('bg-orange-500');
                }, 1000);
            });
        };

        // Initialize Pusher on every page to listen for cart updates
        document.addEventListener('DOMContentLoaded', function() {
            // Check if we should restore cart count from localStorage
            const savedCount = localStorage.getItem('cart_count');
            if (savedCount) {
                const sessionCount = {{ session('cart_count', 0) }};
                // Only use localStorage if it has a newer value than the session
                if (parseInt(savedCount) > sessionCount) {
                    window.updateCartCount(savedCount);
                }
            }
            
            // Set up Pusher if the script is loaded
            if (typeof Pusher !== 'undefined') {
                const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                    cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                    encrypted: true,
                    enabledTransports: ['ws', 'wss'],
                    debug: true
                });
                
                // Subscribe to cart channel
                const cartChannel = pusher.subscribe('user-cart-channel.{{ auth()->id() }}');
                
                // Listen for cart updates
                cartChannel.bind('cart-updated', function(data) {
                    window.updateCartCount(data.count);
                });
            }
        });
    </script>
    
    @yield('scripts')
    
    @stack('scripts')
    
    <!-- Branch Selector Modal -->
    @include('partials.customer.branch-selector-modal')
</body>
</html>
