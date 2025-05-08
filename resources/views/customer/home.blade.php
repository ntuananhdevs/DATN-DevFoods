<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevFood Vietnam</title>
    <meta name="description" content="Thực đơn DevFood đa dạng và phong phú, có rất nhiều sự lựa chọn cho bạn, gia đình và bạn bè.">
    <link rel="stylesheet" href="{{ asset('css/Home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components-customer.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<!-- Header -->
<header class="header">
    <!-- Top Navigation -->
    <div class="top-nav">
        <div class="container">
            <div class="top-nav-content">
                <div class="top-nav-links">
                    <a href="#" class="top-nav-link">Về Jollibee</a>
                    <a href="#" class="top-nav-link">Khuyến Mãi</a>
                    <a href="#" class="top-nav-link">Cửa Hàng</a>
                    <a href="#" class="top-nav-link">Tuyển Dụng</a>
                </div>
                <div class="top-nav-actions">
                    <div class="language-selector">
                        <button class="language-btn active">VN</button>
                        <span>|</span>
                        <button class="language-btn">EN</button>
                    </div>
                    <button class="location-btn">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Chọn địa điểm</span>
                    </button>
                    <button class="user-btn">
                        <i class="fas fa-user"></i>
                        <span>Đăng ký / Đăng nhập</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <div class="main-nav" id="main-nav">
        <div class="container">
            <div class="main-nav-content">
                <div class="logo-menu">
                    <a href="index.html" class="logo">
                        <img src="images/jollibee-logo.png" alt="Jollibee Logo">
                    </a>
                    <nav class="main-menu">
                        <ul class="menu-items">
                            <li class="menu-item active"><a href="index.html">TRANG CHỦ</a></li>
                            <li class="menu-item dropdown">
                                <a href="#" class="dropdown-toggle">VỀ JOLLIBEE <i class="fas fa-chevron-down"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Câu chuyện thương hiệu</a></li>
                                    <li><a href="#">Lịch sử phát triển</a></li>
                                    <li><a href="#">Giá trị cốt lõi</a></li>
                                    <li><a href="#">Đội ngũ lãnh đạo</a></li>
                                </ul>
                            </li>
                            <li class="menu-item dropdown">
                                <a href="menu.html" class="dropdown-toggle">THỰC ĐƠN <i class="fas fa-chevron-down"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="menu.html">Gà Giòn Vui Vẻ</a></li>
                                    <li><a href="menu.html">Gà Sốt Cay</a></li>
                                    <li><a href="menu.html">Burger & Sandwich</a></li>
                                    <li><a href="menu.html">Mỳ Ý & Cơm</a></li>
                                    <li><a href="menu.html">Món tráng miệng</a></li>
                                </ul>
                            </li>
                            <li class="menu-item"><a href="#">KHUYẾN MÃI</a></li>
                            <li class="menu-item"><a href="#">DỊCH VỤ</a></li>
                            <li class="menu-item"><a href="#">TIN TỨC</a></li>
                            <li class="menu-item"><a href="#">CỬA HÀNG</a></li>
                            <li class="menu-item"><a href="#">LIÊN HỆ</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="nav-actions">
                    <button class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                    <div class="cart-btn-container">
                        <button class="cart-btn">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                        <span class="cart-count">3</span>
                    </div>
                    <button class="pickup-btn">PICK UP</button>
                    <div class="delivery-info">
                        <div class="delivery-phone">
                            <i class="fas fa-phone"></i>
                            <span>1900-1533</span>
                        </div>
                        <span class="delivery-text">GIAO HÀNG TẬN NƠI</span>
                    </div>
                    <button class="mobile-menu-btn" id="mobile-menu-btn">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobile-menu">
        <div class="container">
            <button class="mobile-menu-close" id="mobile-menu-close">
                <i class="fas fa-times"></i>
            </button>
            <ul class="mobile-menu-items">
                <li class="mobile-menu-item"><a href="index.html">TRANG CHỦ</a></li>
                <li class="mobile-menu-item"><a href="#">VỀ JOLLIBEE</a></li>
                <li class="mobile-menu-item"><a href="menu.html">THỰC ĐƠN</a></li>
                <li class="mobile-menu-item"><a href="#">KHUYẾN MÃI</a></li>
                <li class="mobile-menu-item"><a href="#">DỊCH VỤ</a></li>
                <li class="mobile-menu-item"><a href="#">TIN TỨC</a></li>
                <li class="mobile-menu-item"><a href="#">CỬA HÀNG</a></li>
                <li class="mobile-menu-item"><a href="#">LIÊN HỆ</a></li>
            </ul>
        </div>
    </div>
</header>

<!-- Hero Banner -->
<section class="hero-banner">
    <div class="hero-container">
        <div class="hero-content">
            <h1 class="hero-title">
                Thưởng Thức<br>
                <span class="highlight">Hương Vị Đặc Trưng</span>
            </h1>
            <p class="hero-description">
                Khám phá thực đơn đa dạng và phong phú của Jollibee, với nhiều lựa chọn cho bạn, gia đình và bạn bè.
            </p>
            <div class="hero-buttons">
                <button class="btn btn-primary">Đặt Hàng Ngay</button>
                <button class="btn btn-secondary">Xem Thực Đơn</button>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number" data-count="150">0</div>
                <p class="stat-text">Cửa hàng</p>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-count="1000">0</div>
                <p class="stat-text">Khách hàng</p>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-count="100">0</div>
                <p class="stat-text">Món ăn</p>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-count="25">0</div>
                <p class="stat-text">Năm kinh nghiệm</p>
            </div>
        </div>
    </div>
</section>

<!-- Promotional Carousel -->
<section class="promo-carousel">
    <div class="carousel-container">
        <div class="carousel-slides" id="promo-slides">
            <div class="carousel-slide active">
                <img src="images/promo-1.jpg" alt="Combo Gia Đình Vui Vẻ">
                <div class="slide-content">
                    <h2>Combo Gia Đình Vui Vẻ</h2>
                    <p>Tiết kiệm đến 15% với combo dành cho gia đình</p>
                </div>
            </div>
            <div class="carousel-slide">
                <img src="images/promo-2.jpg" alt="Mua 1 Tặng 1">
                <div class="slide-content">
                    <h2>Mua 1 Tặng 1</h2>
                    <p>Thứ 2 hàng tuần - Mua 1 gà giòn tặng 1 mỳ Ý</p>
                </div>
            </div>
            <div class="carousel-slide">
                <img src="images/promo-3.jpg" alt="Sinh Nhật Vui Vẻ">
                <div class="slide-content">
                    <h2>Sinh Nhật Vui Vẻ</h2>
                    <p>Đặt tiệc sinh nhật tại Jollibee - Nhận quà hấp dẫn</p>
                </div>
            </div>
        </div>
        <button class="carousel-control prev" id="promo-prev">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="carousel-control next" id="promo-next">
            <i class="fas fa-chevron-right"></i>
        </button>
        <div class="carousel-indicators" id="promo-indicators"></div>
    </div>
</section>

<!-- Category Showcase -->
<section class="category-showcase">
    <div class="container">
        <h2 class="section-title">DANH MỤC MÓN ĂN</h2>
        <p class="section-subtitle">KHÁM PHÁ CÁC DANH MỤC MÓN ĂN PHONG PHÚ CỦA JOLLIBEE</p>

        <div class="category-grid">
            <div class="category-card">
                <div class="category-image">
                    <img src="images/category-1.jpg" alt="Gà Giòn Vui Vẻ">
                    <div class="category-overlay"></div>
                    <div class="category-info">
                        <h3>Gà Giòn Vui Vẻ</h3>
                        <p>8 món</p>
                    </div>
                </div>
            </div>
            <div class="category-card">
                <div class="category-image">
                    <img src="images/category-2.jpg" alt="Gà Sốt Cay">
                    <div class="category-overlay"></div>
                    <div class="category-info">
                        <h3>Gà Sốt Cay</h3>
                        <p>6 món</p>
                    </div>
                </div>
            </div>
            <div class="category-card">
                <div class="category-image">
                    <img src="images/category-3.jpg" alt="Burger & Sandwich">
                    <div class="category-overlay"></div>
                    <div class="category-info">
                        <h3>Burger & Sandwich</h3>
                        <p>10 món</p>
                    </div>
                </div>
            </div>
            <div class="category-card">
                <div class="category-image">
                    <img src="images/category-4.jpg" alt="Mỳ Ý & Cơm">
                    <div class="category-overlay"></div>
                    <div class="category-info">
                        <h3>Mỳ Ý & Cơm</h3>
                        <p>12 món</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="featured-products">
    <div class="container">
        <h2 class="section-title">SẢN PHẨM NỔI BẬT</h2>
        <p class="section-subtitle">KHÁM PHÁ CÁC MÓN ĂN ĐƯỢC YÊU THÍCH NHẤT TẠI JOLLIBEE</p>

        <div class="products-grid">
            <div class="product-card" data-product-id="1">
                <div class="product-image">
                    <img src="images/products/ga-gion-1-mieng.jpg" alt="Gà Giòn Vui Vẻ (1 miếng)">
                    <div class="product-overlay">
                        <div class="product-actions">
                            <button class="product-action-btn favorite">
                                <i class="fas fa-heart"></i>
                            </button>
                            <button class="product-action-btn cart">
                                <i class="fas fa-shopping-bag"></i>
                            </button>
                            <button class="product-action-btn info">
                                <i class="fas fa-info"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Gà Giòn Vui Vẻ (1 miếng)</h3>
                    <p class="product-description">Gà rán giòn thơm ngon, hương vị đặc trưng của Jollibee với lớp bột chiên giòn rụm và thịt gà mềm, thơm ngon.</p>
                    <div class="product-price-cart">
                        <div class="product-price">
                            <span class="current-price">40.000đ</span>
                        </div>
                        <button class="add-to-cart-btn">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="product-card" data-product-id="2">
                <div class="product-image">
                    <img src="images/products/ga-sot-cay-1-mieng.jpg" alt="Gà Sốt Cay (1 miếng)">
                    <div class="product-overlay">
                        <div class="product-actions">
                            <button class="product-action-btn favorite">
                                <i class="fas fa-heart"></i>
                            </button>
                            <button class="product-action-btn cart">
                                <i class="fas fa-shopping-bag"></i>
                            </button>
                            <button class="product-action-btn info">
                                <i class="fas fa-info"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Gà Sốt Cay (1 miếng)</h3>
                    <p class="product-description">Gà rán phủ sốt cay đặc biệt, cay nồng hấp dẫn, thịt gà mềm, thơm ngon.</p>
                    <div class="product-price-cart">
                        <div class="product-price">
                            <span class="current-price">45.000đ</span>
                        </div>
                        <button class="add-to-cart-btn">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="product-card" data-product-id="3">
                <div class="product-image">
                    <img src="images/products/burger-ga-gion.jpg" alt="Burger Gà Giòn">
                    <div class="product-overlay">
                        <div class="product-actions">
                            <button class="product-action-btn favorite">
                                <i class="fas fa-heart"></i>
                            </button>
                            <button class="product-action-btn cart">
                                <i class="fas fa-shopping-bag"></i>
                            </button>
                            <button class="product-action-btn info">
                                <i class="fas fa-info"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Burger Gà Giòn</h3>
                    <p class="product-description">Burger với lớp thịt gà giòn, rau tươi và sốt mayonnaise đặc biệt, đậm đà hương vị.</p>
                    <div class="product-price-cart">
                        <div class="product-price">
                            <span class="current-price">50.000đ</span>
                        </div>
                        <button class="add-to-cart-btn">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="product-card" data-product-id="4">
                <div class="product-image">
                    <img src="images/products/my-y-sot-bo-bam.jpg" alt="Mỳ Ý Sốt Bò Bằm">
                    <div class="product-overlay">
                        <div class="product-actions">
                            <button class="product-action-btn favorite">
                                <i class="fas fa-heart"></i>
                            </button>
                            <button class="product-action-btn cart">
                                <i class="fas fa-shopping-bag"></i>
                            </button>
                            <button class="product-action-btn info">
                                <i class="fas fa-info"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-tag">Giảm giá</div>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Mỳ Ý Sốt Bò Bằm</h3>
                    <p class="product-description">Mỳ Ý với sốt bò bằm đậm đà, thơm ngon, kết hợp với phô mai và gia vị đặc biệt.</p>
                    <div class="product-price-cart">
                        <div class="product-price">
                            <span class="current-price">45.000đ</span>
                            <span class="original-price">55.000đ</span>
                        </div>
                        <button class="add-to-cart-btn">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="view-all-container">
            <a href="menu.html" class="btn btn-primary">XEM TẤT CẢ SẢN PHẨM</a>
        </div>
    </div>
</section>

<!-- Delivery Banner -->
<section class="delivery-banner">
    <div class="delivery-background"></div>
    <div class="container">
        <div class="delivery-content">
            <div class="delivery-text">
                <h2>GIAO HÀNG TẬN NƠI</h2>
                <p>Đặt hàng Jollibee trực tuyến và nhận giao hàng tận nơi nhanh chóng. Thưởng thức món ăn yêu thích của bạn mà không cần rời khỏi nhà!</p>
                <button class="btn btn-yellow">ĐẶT HÀNG NGAY</button>
            </div>
            <div class="delivery-image">
                <img src="images/delivery.png" alt="Jollibee Delivery">
                <div class="floating-image top-right">
                    <img src="images/decoration-3.png" alt="Decoration">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Menu Section -->
<section class="menu-section">
    <div class="container">
        <h2 class="section-title">THỰC ĐƠN</h2>
        <p class="section-subtitle">KHÁM PHÁ HƯƠNG VỊ ĐẶC TRƯNG CỦA JOLLIBEE</p>

        <div class="menu-categories">
            <div class="menu-category">
                <div class="menu-category-image">
                    <img src="images/menu-category-1.jpg" alt="Gà Giòn Vui Vẻ">
                </div>
                <div class="menu-category-content">
                    <h3 class="menu-category-title">Gà Giòn Vui Vẻ</h3>
                    <p class="menu-category-description">Gà rán giòn thơm ngon, hương vị đặc trưng của Jollibee</p>
                    <ul class="menu-items-list">
                        <li class="menu-item-row">
                            <span class="menu-item-name">1 Miếng Gà Giòn</span>
                            <span class="menu-item-price">40.000đ</span>
                        </li>
                        <li class="menu-item-row">
                            <span class="menu-item-name">2 Miếng Gà Giòn</span>
                            <span class="menu-item-price">75.000đ</span>
                        </li>
                        <li class="menu-item-row">
                            <span class="menu-item-name">3 Miếng Gà Giòn</span>
                            <span class="menu-item-price">109.000đ</span>
                        </li>
                        <li class="menu-item-row">
                            <span class="menu-item-name">6 Miếng Gà Giòn</span>
                            <span class="menu-item-price">209.000đ</span>
                        </li>
                    </ul>
                    <a href="#" class="view-all-link">Xem tất cả <i class="fas fa-chevron-right"></i></a>
                </div>
            </div>

            <div class="menu-category">
                <div class="menu-category-image">
                    <img src="images/menu-category-2.jpg" alt="Gà Sốt Cay">
                </div>
                <div class="menu-category-content">
                    <h3 class="menu-category-title">Gà Sốt Cay</h3>
                    <p class="menu-category-description">Gà rán phủ sốt cay đặc biệt, cay nồng hấp dẫn</p>
                    <ul class="menu-items-list">
                        <li class="menu-item-row">
                            <span class="menu-item-name">1 Miếng Gà Sốt Cay</span>
                            <span class="menu-item-price">45.000đ</span>
                        </li>
                        <li class="menu-item-row">
                            <span class="menu-item-name">2 Miếng Gà Sốt Cay</span>
                            <span class="menu-item-price">85.000đ</span>
                        </li>
                        <li class="menu-item-row">
                            <span class="menu-item-name">3 Miếng Gà Sốt Cay</span>
                            <span class="menu-item-price">119.000đ</span>
                        </li>
                        <li class="menu-item-row">
                            <span class="menu-item-name">6 Miếng Gà Sốt Cay</span>
                            <span class="menu-item-price">229.000đ</span>
                        </li>
                    </ul>
                    <a href="#" class="view-all-link">Xem tất cả <i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        </div>

        <div class="view-all-container">
            <a href="menu.html" class="btn btn-primary">XEM TOÀN BỘ THỰC ĐƠN</a>
        </div>
    </div>
</section>

<!-- Combo Section -->
<section class="combo-section">
    <div class="container">
        <h2 class="section-title">COMBO TIẾT KIỆM</h2>
        <p class="section-subtitle">THƯỞNG THỨC NHIỀU MÓN NGON VỚI GIÁ HẤP DẪN</p>

        <div class="combo-grid">
            <div class="combo-card">
                <div class="combo-image">
                    <img src="images/combo-1.jpg" alt="Combo Gia Đình Vui Vẻ">
                    <div class="combo-tag">Tiết kiệm 10%</div>
                </div>
                <div class="combo-info">
                    <h3 class="combo-title">Combo Gia Đình Vui Vẻ</h3>
                    <p class="combo-description">6 miếng gà giòn, 1 mỳ Ý lớn, 2 khoai tây chiên lớn, 4 ly nước ngọt</p>
                    <div class="combo-price">
                        <span class="current-price">359.000đ</span>
                        <span class="original-price">399.000đ</span>
                    </div>
                    <button class="btn btn-primary btn-full">ĐẶT NGAY</button>
                </div>
            </div>

            <div class="combo-card">
                <div class="combo-image">
                    <img src="images/combo-2.jpg" alt="Combo Nhóm Bạn">
                    <div class="combo-tag">Tiết kiệm 11%</div>
                </div>
                <div class="combo-info">
                    <h3 class="combo-title">Combo Nhóm Bạn</h3>
                    <p class="combo-description">4 miếng gà giòn, 2 burger gà, 2 khoai tây chiên vừa, 4 ly nước ngọt</p>
                    <div class="combo-price">
                        <span class="current-price">329.000đ</span>
                        <span class="original-price">369.000đ</span>
                    </div>
                    <button class="btn btn-primary btn-full">ĐẶT NGAY</button>
                </div>
            </div>

            <div class="combo-card">
                <div class="combo-image">
                    <img src="images/combo-3.jpg" alt="Combo Cặp Đôi">
                    <div class="combo-tag">Tiết kiệm 13%</div>
                </div>
                <div class="combo-info">
                    <h3 class="combo-title">Combo Cặp Đôi</h3>
                    <p class="combo-description">2 miếng gà giòn, 2 burger gà, 1 khoai tây chiên lớn, 2 ly nước ngọt</p>
                    <div class="combo-price">
                        <span class="current-price">199.000đ</span>
                        <span class="original-price">229.000đ</span>
                    </div>
                    <button class="btn btn-primary btn-full">ĐẶT NGAY</button>
                </div>
            </div>
        </div>

        <div class="view-all-container">
            <a href="#" class="btn btn-yellow">XEM TẤT CẢ COMBO</a>
        </div>
    </div>
</section>

<!-- Parallax Section -->
<section class="parallax-section">
    <div class="parallax-background"></div>
    <div class="parallax-overlay"></div>
    <div class="container">
        <div class="parallax-content">
            <h2 class="parallax-title">JOLLIBEE VIETNAM</h2>
            <p class="parallax-description">Thưởng thức hương vị đặc trưng của Jollibee - Nơi mang đến niềm vui và những khoảnh khắc ấm áp cho mọi gia đình Việt Nam.</p>
            <button class="btn btn-yellow">ĐẶT HÀNG NGAY</button>
        </div>
    </div>
</section>

<!-- Delivery Section -->
<section class="order-section">
    <div class="container">
        <div class="order-card">
            <div class="order-content">
                <h2 class="order-title">ĐẶT HÀNG NGAY!</h2>
                <p class="order-description">Thưởng thức món ăn Jollibee yêu thích tại nhà hoặc đặt trước và đến lấy tại cửa hàng.</p>

                <div class="order-tabs">
                    <button class="order-tab active" data-tab="delivery">GIAO HÀNG TẬN NƠI</button>
                    <button class="order-tab" data-tab="pickup">ĐẶT TRƯỚC & LẤY ĐI</button>
                </div>

                <div class="order-tab-content active" id="delivery-content">
                    <div class="form-group">
                        <label for="address">Địa chỉ giao hàng</label>
                        <div class="input-with-button">
                            <input type="text" id="address" placeholder="Nhập địa chỉ của bạn">
                            <button class="input-button"><i class="fas fa-map-marker-alt"></i></button>
                        </div>
                    </div>

                    <div class="delivery-info-row">
                        <div class="delivery-info-item">
                            <i class="fas fa-clock"></i>
                            <span>30-45 phút</span>
                        </div>
                        <div class="delivery-info-item">
                            <i class="fas fa-phone"></i>
                            <span>Hotline: 1900-1533</span>
                        </div>
                    </div>

                    <button class="btn btn-yellow btn-full">ĐẶT HÀNG NGAY</button>
                </div>

                <div class="order-tab-content" id="pickup-content">
                    <div class="form-group">
                        <label for="store">Chọn cửa hàng</label>
                        <select id="store">
                            <option value="">Chọn cửa hàng gần bạn</option>
                            <option value="1">Jollibee Lê Văn Sỹ</option>
                            <option value="2">Jollibee Nguyễn Huệ</option>
                            <option value="3">Jollibee Royal City</option>
                            <option value="4">Jollibee Times City</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="pickup-time">Thời gian lấy hàng</label>
                        <select id="pickup-time">
                            <option value="">Chọn thời gian</option>
                            <option value="1">Càng sớm càng tốt</option>
                            <option value="2">Trong vòng 30 phút</option>
                            <option value="3">Trong vòng 1 giờ</option>
                            <option value="4">Chọn thời gian cụ thể</option>
                        </select>
                    </div>

                    <button class="btn btn-yellow btn-full">ĐẶT HÀNG & LẤY ĐI</button>
                </div>
            </div>
            <div class="order-image">
                <img src="images/delivery-order.jpg" alt="Jollibee Delivery">
            </div>
        </div>
    </div>
</section>

<!-- Kids Club Section -->
<section class="kids-club-section">
    <div class="container">
        <div class="kids-club-content">
            <div class="kids-club-text">
                <h2 class="kids-club-title">JOLLIBEE KIDS CLUB</h2>
                <p class="kids-club-description">Tham gia Jollibee Kids Club ngay hôm nay để nhận được nhiều ưu đãi đặc biệt, quà tặng sinh nhật và các hoạt động thú vị dành riêng cho các bé!</p>
                <div class="kids-club-buttons">
                    <button class="btn btn-primary">ĐĂNG KÝ NGAY</button>
                    <button class="btn btn-outline-red">TÌM HIỂU THÊM</button>
                </div>
            </div>
            <div class="kids-club-image">
                <img src="images/kids-club.png" alt="Jollibee Kids Club">
                <div class="rotating-star">
                    <img src="images/star.png" alt="Jollibee Star">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="testimonials-section">
    <div class="container">
        <h2 class="section-title">KHÁCH HÀNG NÓI GÌ</h2>
        <p class="section-subtitle">NHỮNG TRẢI NGHIỆM TUYỆT VỜI TỪ KHÁCH HÀNG CỦA JOLLIBEE</p>

        <div class="testimonials-carousel">
            <div class="testimonials-container" id="testimonials-container">
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar">
                            <img src="images/avatar-1.jpg" alt="Nguyễn Văn A">
                        </div>
                        <div class="testimonial-author">
                            <h3>Nguyễn Văn A</h3>
                            <p>TP. Hồ Chí Minh</p>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="testimonial-text">Gà rán Jollibee là món ăn yêu thích của cả gia đình tôi. Thịt gà mềm, lớp vỏ giòn rụm và gia vị đậm đà. Dịch vụ giao hàng nhanh chóng và nhân viên rất thân thiện.</p>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar">
                            <img src="images/avatar-2.jpg" alt="Trần Thị B">
                        </div>
                        <div class="testimonial-author">
                            <h3>Trần Thị B</h3>
                            <p>Hà Nội</p>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="testimonial-text">Mỳ Ý sốt bò bằm của Jollibee rất ngon, sốt đậm đà và thịt bò nhiều. Tôi thường đặt combo gia đình vào cuối tuần và cả nhà đều rất thích.</p>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar">
                            <img src="images/avatar-3.jpg" alt="Lê Văn C">
                        </div>
                        <div class="testimonial-author">
                            <h3>Lê Văn C</h3>
                            <p>Đà Nẵng</p>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="testimonial-text">Tổ chức sinh nhật cho con tại Jollibee là lựa chọn tuyệt vời. Các bé rất thích không gian vui nhộn và đồ ăn ngon. Nhân viên nhiệt tình hỗ trợ từ đầu đến cuối.</p>
                </div>
            </div>

            <button class="testimonial-control prev" id="testimonial-prev">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="testimonial-control next" id="testimonial-next">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>

<!-- News Section -->
<section class="news-section">
    <div class="container">
        <h2 class="section-title">TIN TỨC</h2>
        <p class="section-subtitle">CẬP NHẬT THÔNG TIN MỚI NHẤT TỪ JOLLIBEE</p>

        <div class="news-grid">
            <div class="news-card">
                <div class="news-image">
                    <img src="images/news-1.jpg" alt="Jollibee khai trương cửa hàng mới tại Hà Nội">
                </div>
                <div class="news-content">
                    <div class="news-date">15/04/2024</div>
                    <h3 class="news-title">Jollibee khai trương cửa hàng mới tại Hà Nội</h3>
                    <p class="news-excerpt">Jollibee vừa khai trương cửa hàng thứ 150 tại Việt Nam, đánh dấu cột mốc phát triển mới...</p>
                    <a href="#" class="news-link">Xem thêm</a>
                </div>
            </div>

            <div class="news-card">
                <div class="news-image">
                    <img src="images/news-2.jpg" alt="Món mới: Gà Sốt Phô Mai đã có mặt tại Jollibee">
                </div>
                <div class="news-content">
                    <div class="news-date">10/04/2024</div>
                    <h3 class="news-title">Món mới: Gà Sốt Phô Mai đã có mặt tại Jollibee</h3>
                    <p class="news-excerpt">Hương vị mới lạ với lớp phô mai béo ngậy phủ trên miếng gà giòn rụm đã có mặt tại Jollibee...</p>
                    <a href="#" class="news-link">Xem thêm</a>
                </div>
            </div>

            <div class="news-card">
                <div class="news-image">
                    <img src="images/news-3.jpg" alt="Chương trình khuyến mãi tháng 4: Mua 1 tặng 1">
                </div>
                <div class="news-content">
                    <div class="news-date">01/04/2024</div>
                    <h3 class="news-title">Chương trình khuyến mãi tháng 4: Mua 1 tặng 1</h3>
                    <p class="news-excerpt">Trong tháng 4 này, Jollibee mang đến chương trình khuyến mãi đặc biệt dành cho khách hàng...</p>
                    <a href="#" class="news-link">Xem thêm</a>
                </div>
            </div>
        </div>

        <div class="view-all-container">
            <a href="#" class="btn btn-primary">XEM TẤT CẢ TIN TỨC</a>
        </div>
    </div>
</section>

<!-- Store Locator -->
<section class="store-locator">
    <div class="container">
        <h2 class="section-title">CỬA HÀNG</h2>
        <p class="section-subtitle">TÌM CỬA HÀNG JOLLIBEE GẦN BẠN NHẤT</p>

        <div class="store-search">
            <div class="search-form">
                <div class="search-input">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Tìm kiếm cửa hàng..." id="store-search-input">
                </div>
                <select id="city-filter">
                    <option value="all">Tất cả thành phố</option>
                    <option value="Hà Nội">Hà Nội</option>
                    <option value="TP. Hồ Chí Minh">TP. Hồ Chí Minh</option>
                    <option value="Đà Nẵng">Đà Nẵng</option>
                    <option value="Hải Phòng">Hải Phòng</option>
                    <option value="Cần Thơ">Cần Thơ</option>
                </select>
            </div>

            <div class="store-results" id="store-results">
                <div class="store-item">
                    <div class="store-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="store-details">
                        <h3>Jollibee Lê Văn Sỹ</h3>
                        <p>300 Lê Văn Sỹ, Phường 1, Quận Tân Bình, TP. Hồ Chí Minh</p>
                        <div class="store-contact">
                            <p><strong>Điện thoại:</strong> 028 3991 2345</p>
                            <p><strong>Giờ mở cửa:</strong> 09:00 - 22:00</p>
                        </div>
                    </div>
                </div>

                <div class="store-item">
                    <div class="store-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="store-details">
                        <h3>Jollibee Nguyễn Huệ</h3>
                        <p>26 Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh</p>
                        <div class="store-contact">
                            <p><strong>Điện thoại:</strong> 028 3821 3456</p>
                            <p><strong>Giờ mở cửa:</strong> 08:00 - 22:00</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- App Download -->
<section class="app-download">
    <div class="container">
        <div class="app-content">
            <div class="app-text">
                <h2 class="app-title">TẢI ỨNG DỤNG JOLLIBEE</h2>
                <p class="app-description">Đặt hàng nhanh chóng, theo dõi đơn hàng dễ dàng và nhận nhiều ưu đãi hấp dẫn khi sử dụng ứng dụng Jollibee.</p>
                <div class="app-buttons">
                    <a href="#" class="app-button">
                        <img src="images/app-store.png" alt="Download on App Store">
                    </a>
                    <a href="#" class="app-button">
                        <img src="images/google-play.png" alt="Get it on Google Play">
                    </a>
                </div>
            </div>
            <div class="app-image">
                <img src="images/mobile-app.png" alt="Jollibee Mobile App">
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-column">
                <div class="footer-logo">
                    <img src="images/jollibee-logo-white.png" alt="Jollibee Logo">
                </div>
                <p class="footer-description">Jollibee là thương hiệu đồ ăn nhanh nổi tiếng với các món gà giòn, mỳ Ý, burger và nhiều món ăn hấp dẫn khác.</p>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                </div>
            </div>

            <div class="footer-column">
                <h3 class="footer-title">Về Jollibee</h3>
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
                    <li><a href="#">Jollibee Kids Club</a></li>
                    <li><a href="#">Đơn hàng lớn</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h3 class="footer-title">Liên hệ</h3>
                <ul class="contact-info">
                    <li>Hotline: 1900-1533</li>
                    <li>Email: info@jollibee.com.vn</li>
                    <li>Địa chỉ: Tầng 26, Tòa nhà CII Tower, 152 Điện Biên Phủ, Phường 25, Quận Bình Thạnh, TP. Hồ Chí Minh</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p class="copyright">© 2024 Jollibee Vietnam. Tất cả các quyền được bảo lưu.</p>
            <div class="footer-legal">
                <a href="#">Điều khoản sử dụng</a>
                <a href="#">Chính sách bảo mật</a>
            </div>
        </div>
    </div>
</footer>

<!-- Product Modal -->
<div id="product-modal" class="modal-backdrop" style="display: none;">
    <!-- Modal content will be dynamically inserted here -->
</div>

<!-- JavaScript -->
<script src="{{ asset('js/Customer/main.js') }}"></script>
<script src="{{ asset('js/Customer/slider.js') }}"></script>
<script src="{{ asset('js/Customer/product-modal.js') }}"></script>
</body>
</html>
