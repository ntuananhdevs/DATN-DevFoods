@extends('layouts.customer.fullLayoutMaster')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
        <link rel="stylesheet" href="{{ asset('css/customer/home.css') }}">
    </head>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <body>
        <main>
            <!-- Hero Banner -->
            <section class="hero-banner">
                <div class="hero-background"></div>
                <div class="container">
                    <div class="hero-content">
                        <div class="hero-text">
                            <h1 class="hero-title">Thưởng Thức <br><span>Hương Vị Đặc Trưng</span></h1>
                            <p class="hero-description">Khám phá thực đơn đa dạng và phong phú của DevFood, với nhiều lựa
                                chọn cho bạn, gia đình và bạn bè.</p>
                            <div class="hero-buttons">
                                <button class="btn btn-primary">ĐẶT HÀNG NGAY</button>
                                <button class="btn btn-outline">XEM THỰC ĐƠN</button>
                            </div>
                        </div>
                        <div class="hero-image">
                            <img src="{{ asset('images/banner/banner01.png') }}" alt="DevFood Featured Product">
                        </div>
                    </div>
                </div>
                <div class="hero-gradient"></div>
            </section>

            <!-- Stats Section -->
            <section class="stats-section">
                <div class="container">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number" data-count="150">0</div>
                            <p class="stat-label">Cửa hàng</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" data-count="1000">0</div>
                            <p class="stat-label">Khách hàng (K+)</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" data-count="100">0</div>
                            <p class="stat-label">Món ăn</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" data-count="25">0</div>
                            <p class="stat-label">Năm kinh nghiệm</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Promotional Carousel -->
            @include('panels.customer.banner')

            <!-- Category Showcase -->
            <section class="category-showcase">
                <div class="container">
                    <div class="section-title">
                        <h2>DANH MỤC MÓN ĂN</h2>
                        <p>KHÁM PHÁ CÁC DANH MỤC MÓN ĂN PHONG PHÚ CỦA DevFood</p>
                    </div>

                    <div class="category-grid">
                        @foreach ($categories->take(4) as $category)
                            <div class="category-card">
                                <a href="">
                                    <div class="category-image">
                                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                                        <div class="category-overlay"></div>
                                        <div class="category-info">
                                            <h3>{{ $category->name }}</h3>
                                            <p>{{ $category->products_count }} món</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="featured-products">
                <div class="container">
                    <div class="section-title">
                        <h2>SẢN PHẨM NỔI BẬT</h2>
                        <p>KHÁM PHÁ CÁC MÓN ĂN ĐƯỢC YÊU THÍCH NHẤT TẠI DevFood</p>
                    </div>

                    <div class="products-grid">
                        @foreach ($products->take(4) as $product)
                            <div class="product-card" data-product-id="{{ $product->id }}">
                                <div class="product-image">
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                    <div class="product-overlay">
                                        <div class="product-actions">
                                            <button class="action-btn favorite-btn">
                                                <i class="fas fa-heart"></i>
                                            </button>
                                            <button class="action-btn cart-btn"
                                                onclick="window.location.href='{{ url('/shop/product/product-detail/' . $product->id) }}'">
                                                <i class="fas fa-shopping-bag"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <h3 class="product-title">{{ $product->name }}</h3>
                                    <p class="product-description">{{ $product->description }}</p>
                                    <div class="product-price-actions">
                                        <div class="product-price">
                                            <span
                                                class="current-price">{{ number_format($product->base_price, 0, ',', '.') }}đ</span>
                                        </div>
                                        <button class="add-to-cart-btn"
                                            onclick="window.location.href='{{ url('/shop/product/product-detail/' . $product->id) }}'">
                                            <i class="fas fa-shopping-bag"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="view-all-container">
                        <a href="{{ asset('shop/product') }}" class="btn btn-primary">XEM TẤT CẢ SẢN PHẨM</a>
                    </div>
                </div>
            </section>

            <!-- Delivery Banner -->
            <section class="order-section">
                <div class="container">
                    <div class="order-card">
                        <div class="order-content">
                            <h2 class="order-title">ĐẶT HÀNG NGAY!</h2>
                            <p class="order-description">Thưởng thức món ăn DevFood yêu thích tại nhà hoặc đặt trước và
                                đến lấy tại cửa hàng.</p>

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
                                        <option value="1">DevFood Lê Văn Sỹ</option>
                                        <option value="2">DevFood Nguyễn Huệ</option>
                                        <option value="3">DevFood Royal City</option>
                                        <option value="4">DevFood Times City</option>
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
                            <img src="{{ asset('images/banner/appstore.png') }}" alt="DevFood Delivery">
                        </div>
                    </div>
                </div>
            </section>

            <!-- Menu Section -->
            <section class="menu-section">
                <!-- Menu content will be here -->
            </section>

            <!-- Combo Section -->
            <section class="combo-section">
                <!-- Combo content will be here -->
            </section>

            <!-- Parallax Section -->
            {{-- <section class="parallax-section">
                <div class="parallax-background"></div>
                <div class="container">
                    <div class="parallax-content">
                        <h2>DevFood VIETNAM</h2>
                        <p>Thưởng thức hương vị đặc trưng của DevFood - Nơi mang đến niềm vui và những khoảnh khắc ấm áp
                            cho mọi gia đình Việt Nam.</p>
                        <button class="btn btn-primary">ĐẶT HÀNG NGAY</button>
                    </div>
                </div>
            </section> --}}


            <section class="delivery-section">

            </section>

            <!-- Kids Club Banner -->
            <section class="kids-club-banner">
                <div class="container">
                    <div class="banner-content">
                        <div class="banner-text">
                            <h2>DevFood KIDS CLUB</h2>
                            <p>Tham gia DevFood Kids Club ngay hôm nay để nhận được nhiều ưu đãi đặc biệt, quà tặng sinh
                                nhật và các hoạt động thú vị dành riêng cho các bé!</p>
                            <div class="banner-buttons">
                                <button class="btn btn-primary">ĐĂNG KÝ NGAY</button>
                                <button class="btn btn-outline">TÌM HIỂU THÊM</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


            <!-- Services Section -->
            <section class="services-section">
                <div class="container">
                    <div class="section-title">
                        <h2>DỊCH VỤ</h2>
                        <p>TẬN HƯỞNG NHỮNG KHOẢNH KHẮC TRỌN VẸN CÙNG DevFood</p>
                    </div>

                    <div class="services-grid">
                        <div class="service-item">
                            <div class="service-image">
                                <img src="images/service-1.jpg" alt="Đặt Hàng Online">
                            </div>
                            <h3>Đặt Hàng Online</h3>
                        </div>
                        <div class="service-item">
                            <div class="service-image">
                                <img src="images/service-2.jpg" alt="Tiệc Sinh Nhật">
                            </div>
                            <h3>Tiệc Sinh Nhật</h3>
                        </div>
                        <div class="service-item">
                            <div class="service-image">
                                <img src="images/service-3.jpg" alt="DevFood Kids Club">
                            </div>
                            <h3>DevFood Kids Club</h3>
                        </div>
                        <div class="service-item">
                            <div class="service-image">
                                <img src="images/service-4.jpg" alt="Đơn Hàng Lớn">
                            </div>
                            <h3>Đơn Hàng Lớn</h3>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Store Locator -->
            <section class="store-locator">
                <!-- Store locator content will be here -->
            </section>

            <!-- App Download Section -->
            <section class="app-download">
                <!-- App download content will be here -->
            </section>
        </main>

        <div class="message-button-container">
            <button class="btn btn-primary message-btn" onclick="window.location.href='{{ route('chat.customer') }}'">
                <i class="fas fa-comments"></i> Tin nhắn
            </button>
        </div>


        <div class="product-modal" id="productModal">
            <div class="modal-content">
                <button class="modal-close">
                    <i class="fas fa-times"></i>
                </button>
                <div class="modal-body">
                    <div class="product-detail">
                        <div class="product-image">
                            <img src="images/product-1.jpg" alt="Product Image" id="modalProductImage">
                        </div>
                        <div class="product-info">
                            <h2 id="modalProductTitle">Gà Giòn Vui Vẻ (1 miếng)</h2>
                            <div class="product-price">
                                <span class="current-price" id="modalProductPrice">40.000đ</span>
                                <span class="original-price" id="modalProductOriginalPrice"></span>
                            </div>
                            <p class="product-description" id="modalProductDescription">Gà rán giòn thơm ngon, hương vị
                                đặc
                                trưng của DevFood với lớp bột chiên giòn rụm và thịt gà mềm, thơm ngon.</p>

                            <div class="quantity-selector">
                                <span>Số lượng</span>
                                <div class="quantity-controls">
                                    <button class="quantity-btn decrease">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <span class="quantity-value">1</span>
                                    <button class="quantity-btn increase">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="product-options">
                                <h3>Tùy chọn</h3>
                                <div class="options-list" id="modalProductOptions">
                                    <!-- Options will be dynamically added here -->
                                </div>
                            </div>

                            <div class="product-addons">
                                <h3>Thêm món</h3>
                                <div class="addons-list" id="modalProductAddons">
                                    <!-- Addons will be dynamically added here -->
                                </div>
                            </div>

                            <div class="product-total">
                                <span>Tổng cộng:</span>
                                <span class="total-price" id="modalTotalPrice">40.000đ</span>
                            </div>

                            <div class="product-actions">
                                <button class="btn btn-outline">
                                    <i class="fas fa-heart"></i>
                                    <span>Yêu thích</span>
                                </button>
                                <button class="btn btn-primary">
                                    <i class="fas fa-shopping-bag"></i>
                                    <span>Thêm vào giỏ</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="product-tabs">
                        <div class="tabs-header">
                            <button class="tab-btn active" data-tab="details">Chi tiết sản phẩm</button>
                            <button class="tab-btn" data-tab="nutrition">Thông tin dinh dưỡng</button>
                        </div>
                        <div class="tabs-content">
                            <div class="tab-panel active" id="details">
                                <h3>Thành phần:</h3>
                                <ul class="ingredients-list" id="modalIngredients">
                                    <!-- Ingredients will be dynamically added here -->
                                </ul>

                                <div class="allergens" id="modalAllergensContainer">
                                    <h3>Chứa dị ứng:</h3>
                                    <div class="allergens-list" id="modalAllergens">
                                        <!-- Allergens will be dynamically added here -->
                                    </div>
                                </div>
                            </div>
                            <div class="tab-panel" id="nutrition">
                                <table class="nutrition-table">
                                    <thead>
                                        <tr>
                                            <th>Thông tin dinh dưỡng</th>
                                            <th>Giá trị</th>
                                        </tr>
                                    </thead>
                                    <tbody id="modalNutrition">
                                        <!-- Nutrition info will be dynamically added here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <style>
        .message-button-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }

        .message-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 50px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .message-btn i {
            margin-right: 8px;
        }
    </style>

    </html>
@endsection
