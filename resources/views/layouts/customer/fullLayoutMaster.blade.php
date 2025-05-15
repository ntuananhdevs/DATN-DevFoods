<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DevFood Vietnam')</title>

    <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('fonts/feather/style.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/noui-slider@15.6.1/dist/nouislider.min.css">
`   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@6.8.4/swiper-bundle.min.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('css/customer/layout.css') }}">
    @yield('styles')
</head>

<body>
    <!-- Scroll Progress Bar -->
    <div class="scroll-progress-bar"></div>

    <!-- Header -->
    <header class="header">
        <!-- Top Navigation -->
        {{--    <div class="top-nav"> --}}
        {{--        <div class="container"> --}}
        {{--            <div class="top-nav-content"> --}}
        {{--                <div class="top-nav-links"> --}}
        {{--                    <a href="#" class="top-nav-link">Về DevFood</a> --}}
        {{--                    <a href="#" class="top-nav-link">Khuyến Mãi</a> --}}
        {{--                    <a href="#" class="top-nav-link">Cửa Hàng</a> --}}
        {{--                    <a href="#" class="top-nav-link">Tuyển Dụng</a> --}}
        {{--                </div> --}}
        {{--                <div class="top-nav-actions"> --}}
        {{--                    <div class="language-selector"> --}}
        {{--                        <button class="language-btn active">VN</button> --}}
        {{--                        <span>|</span> --}}
        {{--                        <button class="language-btn">EN</button> --}}
        {{--                    </div> --}}
        {{--                    <button class="location-btn"> --}}
        {{--                        <i class="fas fa-map-marker-alt"></i> --}}
        {{--                        <span>Chọn địa điểm</span> --}}
        {{--                    </button> --}}
        {{--                    <button class="account-btn"> --}}
        {{--                        <i class="fas fa-user"></i> --}}
        {{--                        <span>Đăng ký / Đăng nhập</span> --}}
        {{--                    </button> --}}
        {{--                </div> --}}
        {{--            </div> --}}
        {{--        </div> --}}
        {{--    </div> --}}

        <!-- Main Navigation -->
        <div class="main-nav">
            <div class="container">
                <div class="main-nav-content">
                    <div class="logo">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('images/logo/Logo-DevFood.png') }}" alt="DevFood Logo">
                        </a>
                    </div>
                    <nav class="desktop-menu">
                        <ul class="nav-list">
                            <li class="nav-item active"><a href="{{ url('/') }}">TRANG CHỦ</a></li>
                            <li class="nav-item dropdown">
                                <a href="#" class="dropdown-toggle">VỀ DevFood</a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Câu chuyện thương hiệu</a></li>
                                    <li><a href="#">Lịch sử phát triển</a></li>
                                    <li><a href="#">Giá trị cốt lõi</a></li>
                                    <li><a href="#">Đội ngũ lãnh đạo</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a href="{{ asset('shop/product') }}" class="dropdown-toggle">THỰC ĐƠN</a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Gà Giòn Vui Vẻ</a></li>
                                    <li><a href="#">Gà Sốt Cay</a></li>
                                    <li><a href="#">Burger & Sandwich</a></li>
                                    <li><a href="#">Mỳ Ý & Cơm</a></li>
                                    <li><a href="#">Món tráng miệng</a></li>
                                </ul>
                            </li>
                            <li class="nav-item"><a href="#">KHUYẾN MÃI</a></li>
                            <li class="nav-item"><a href="#">DỊCH VỤ</a></li>
                            <li class="nav-item"><a href="#">CỬA HÀNG</a></li>
                            <li class="nav-item"><a href="#">LIÊN HỆ</a></li>
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
                        </ul>
                    </nav>
                    <div class="nav-actions">
                        <button class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                        <div class="notification-btn">
                            <a href="#"><i class="fa-solid fa-bell"></i></a>
                            <span class="notification-count">{{ $notificationCount ?? '0' }}</span>
                        </div>
                        <div class="cart-btn">
                            <a href="{{ asset('cart') }}"><i class="fas fa-shopping-bag"></i></a>
                            <span class="cart-count">{{ session()->has('cart') ? count(session('cart')) : '0' }}</span>
                            <span class="cart-count">{{ count(Session::get('cart', [])) }}</span>
                        </div>
                        <!-- <button class="pickup-btn">PICK UP</button>
                    <div class="hotline">
                        <i class="fas fa-phone"></i>
                        <div>
                            <span class="hotline-number">1900-1533</span>
                            <span class="hotline-text">GIAO HÀNG TẬN NƠI</span>
                        </div>
                    </div>
                    <button class="mobile-menu-btn">
                        <i class="fas fa-bars"></i>
                    </button> -->
                    </div>
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

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <div class="footer-logo">
                        <img src="{{ asset('images/logo-white.png') }}" alt="DevFood Logo">
                    </div>
                    <p class="footer-description">
                        DevFood là thương hiệu đồ ăn nhanh nổi tiếng với các món gà giòn, mỳ Ý, burger và nhiều món ăn
                        hấp dẫn khác.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>

                <div class="footer-column">
                    <h3 class="footer-title">Về DevFood</h3>
                    <ul class="footer-links">
                        <li><a href="#">Giới thiệu</a></li>
                        <li><a href="#">Lịch sử phát triển</a></li>
                        <li><a href="#">Tin tức & Sự kiện</a></li>
                        <li><a href="#">Tuyển dụng</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3 class="footer-title">Dịch vụ</h3>
                    <ul class="footer-links">
                        <li><a href="#">Đặt hàng trực tuyến</a></li>
                        <li><a href="#">Tiệc sinh nhật</a></li>
                        <li><a href="#">DevFood Kids Club</a></li>
                        <li><a href="#">Đơn hàng lớn</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3 class="footer-title">Liên hệ</h3>
                    <ul class="contact-info">
                        <li>Hotline: 1900-1533</li>
                        <li>Email: info@DevFood.com.vn</li>
                        <li>Địa chỉ: Tầng 26, Tòa nhà CII Tower, 152 Điện Biên Phủ, Phường 25, Quận Bình Thạnh, TP. Hồ
                            Chí Minh</li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="copyright">© 2024 DevFood Vietnam. Tất cả các quyền được bảo lưu.</p>
                <div class="footer-legal">
                    <a href="#">Điều khoản sử dụng</a>
                    <a href="#">Chính sách bảo mật</a>
                </div>
            </div>
        </div>
    </footer>

    @yield('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/Customer/main.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@6.8.4/swiper-bundle.min.js"></script>
    <!-- <script>
        $(document).ready(function() {
            // Cập nhật số lượng sản phẩm trong giỏ hàng từ session
            const cartCount = {{ count(Session::get('cart', [])) }};
            $('.cart-count').text(cartCount);
        });
    </script> -->
</body>

</html>
