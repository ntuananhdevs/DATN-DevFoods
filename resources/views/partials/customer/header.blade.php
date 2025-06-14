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