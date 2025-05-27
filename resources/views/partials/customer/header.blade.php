<header class="bg-white shadow">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center">
                <a href="{{ route('customer.home') }}" class="text-xl font-bold text-indigo-600">DevFoods</a>
                <nav class="ml-10 hidden md:flex space-x-6">
                    <a href="{{ route('customer.home') }}" class="text-gray-700 hover:text-indigo-600">Trang chủ</a>
                    <a href="#" class="text-gray-700 hover:text-indigo-600">Thực đơn</a>
                    <a href="#" class="text-gray-700 hover:text-indigo-600">Khuyến mãi</a>
                    <a href="#" class="text-gray-700 hover:text-indigo-600">Liên hệ</a>
                </nav>
            </div>
            
            <div class="flex items-center space-x-4">
                @auth
                    <a href="#" class="text-gray-700 hover:text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </a>
                    
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center text-gray-700 hover:text-indigo-600">
                            <span class="mr-2">{{ Auth::user()->full_name }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">Hồ sơ cá nhân</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">Đơn hàng của tôi</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">Địa chỉ giao hàng</a>
                            <hr class="my-1">
                            <form method="POST" action="{{ route('customer.logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">Đăng xuất</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('customer.login') }}" class="text-gray-700 hover:text-indigo-600">Đăng nhập</a>
                    <a href="{{ route('customer.register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Đăng ký</a>
                @endauth
            </div>
        </div>
    </div>
</header>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-orange" href="{{ url('/') }}">PolyCrispyWings</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}">Trang Chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('shop/products') }}">Thực Đơn</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/store') }}">Cửa Hàng</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/promotions') }}">Khuyến Mãi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/blog') }}">Bài Viết</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/about') }}">Về Chúng Tôi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/contact') }}">Liên Hệ</a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center">
                <div class="search-box me-3">
                    <form class="d-flex" action="{{ url('/search') }}" method="GET">
                        <input class="form-control me-2" type="search" name="query" placeholder="Tìm kiếm..." aria-label="Search">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                
                <div class="d-flex align-items-center">
                    <a href="{{ url('/profile') }}" class="btn btn-link text-dark me-2">
                        <i class="fas fa-user"></i>
                    </a>
                    <a href="{{ url('/cart') }}" class="btn btn-link text-dark position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="cart-counter" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-orange">
                            {{ session('cart_count', 0) }}
                        </span>
                    </a>
                    <div class="d-none d-lg-flex align-items-center ms-3">
                        <i class="fas fa-phone text-orange me-1"></i>
                        <span class="fw-medium">1900 1234</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>