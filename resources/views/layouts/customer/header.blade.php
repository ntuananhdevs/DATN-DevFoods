@php
$currentRoute = request()->route()->getName();
@endphp

<!-- Header -->
<header class="header">
    <!-- Main Navigation -->
    <div class="main-nav">
        <div class="container">
            <div class="main-nav-content">
                <nav class="desktop-menu">
                    <ul class="nav-list">
                        <div class="logo">
                            <a href="{{ url('/') }}">
                                <img src="{{ asset('images/logo/Logo-DevFood.png') }}" alt="DevFood Logo">
                            </a>
                        </div>
                        <li class="nav-item {{ $currentRoute === 'customer.home' ? 'active' : '' }}"><a href="{{ url('/') }}" data-text="TRANG CHỦ"><span>TRANG CHỦ</span></a></li>
                        <li class="nav-item dropdown">
                            <a href="#" class="dropdown-toggle" data-text="VỀ DevFood"><span>VỀ DevFood</span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Câu chuyện thương hiệu</a></li>
                                <li><a href="#">Lịch sử phát triển</a></li>
                                <li><a href="#">Giá trị cốt lõi</a></li>
                                <li><a href="#">Đội ngũ lãnh đạo</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="{{ asset('shop/product') }}" class="dropdown-toggle" data-text="THỰC ĐƠN"><span>THỰC ĐƠN</span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Gà Giòn Vui Vẻ</a></li>
                                <li><a href="#">Gà Sốt Cay</a></li>
                                <li><a href="#">Burger & Sandwich</a></li>
                                <li><a href="#">Mỳ Ý & Cơm</a></li>
                                <li><a href="#">Món tráng miệng</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a href="#" data-text="KHUYẾN MÃI"><span>KHUYẾN MÃI</span></a></li>
                        <li class="nav-item"><a href="#" data-text="DỊCH VỤ"><span>DỊCH VỤ</span></a></li>
                        <li class="nav-item"><a href="#" data-text="CỬA HÀNG"><span>CỬA HÀNG</span></a></li>
                        <li class="nav-item"><a href="#" data-text="LIÊN HỆ"><span>LIÊN HỆ</span></a></li>
                        <li class="nav-item dropdown">
                            @guest
                                <a href="{{ route('customer.login') }}" class="nav-link">Đăng Nhập</a>
                            @else
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Xin chào, {{ Auth::user()->full_name ?? Auth::user()->email }}
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('customer.profile') }}">
                                            Trang Cá Nhân
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('customer.logout') }}"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            Đăng Xuất
                                        </a>
                                    </li>
                                </ul>
                                <form id="logout-form" action="{{ route('customer.logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            @endguest
                        </li>                            
                        <div class="nav-actions">
                            <button class="search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                            <div class="notification-btn">
                                <a href="#"><i class="fa-solid fa-bell"></i></a>
                                <span class="notification-count">5</span>
                            </div>
                            <div class="cart-btn">
                                <a href="{{ asset('cart') }}"><i class="fas fa-shopping-bag"></i></a>
                                <span class="cart-count">3</span>
                            </div>
                        </div>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu">
        <div class="container">
            <button class="mobile-menu-close">
                <i class="fas fa-times"></i>
            </button>
            <ul class="mobile-nav-list">
                <li><a href="{{ url('/') }}">TRANG CHỦ</a></li>
                <li><a href="#">VỀ DevFood</a></li>
                <li><a href="#">THỰC ĐƠN</a></li>
                <li><a href="#">KHUYẾN MÃI</a></li>
                <li><a href="#">DỊCH VỤ</a></li>
                <li><a href="#">CỬA HÀNG</a></li>
                <li><a href="#">LIÊN HỆ</a></li>
                <li><a href="#">Đăng Nhập</a></li>
            </ul>
        </div>
    </div>
</header>