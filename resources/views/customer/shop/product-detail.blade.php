@extends('layouts.customer.fullLayoutMaster')

@section('title', 'Poly Crispy Wings')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('fonts/feather/style.min.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@6.8.4/swiper-bundle.min.css">
<link rel="stylesheet" href="{{ asset('css/pages/app-ecommerce-details.css') }}">
<style>
    /* Thiết lập chung */
    body {
        background-color: #f8f8f8;
        font-family: 'Montserrat', sans-serif;
        color: #626262;
        margin: 0;
        padding: 0;
    }

    .container-fluid {
        padding: 2rem;
    }

    /* Breadcrumb */
    .breadcrumb {
        background-color: transparent;
        padding: 0;
        margin-bottom: 1.5rem;
    }

    .breadcrumb-item a {
        color: #7367f0;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: #626262;
    }

    /* Product Details */
    .card {
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 4px 25px 0 rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    .product-img {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    .product-img img {
        max-width: 100%;
        max-height: 400px;
        object-fit: contain;
    }

    .product-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: #2c2c2c;
        margin-bottom: 0.5rem;
    }

    .product-ratings {
        margin-bottom: 1rem;
    }

    .product-ratings i {
        color: #ffc107;
        font-size: 1.2rem;
    }

    .product-price {
        font-size: 1.5rem;
        font-weight: 600;
        color: #7367f0;
        margin-bottom: 1rem;
    }

    .product-price .old-price {
        text-decoration: line-through;
        color: #b8c2cc;
        font-size: 1.2rem;
        margin-left: 0.5rem;
    }

    .product-description {
        margin-bottom: 1.5rem;
    }

    .product-features {
        margin-bottom: 1.5rem;
    }

    .product-features ul {
        padding-left: 1.5rem;
    }

    .product-features li {
        margin-bottom: 0.5rem;
    }

    .item-quantity {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .input-group {
        width: 150px;
        margin-right: 1rem;
    }

    .btn-cart {
        background-color: #7367f0;
        color: white;
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 5px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-cart:hover {
        background-color: #5e50ee;
    }

    .btn-wishlist {
        background-color: #f6f6f6;
        color: #2c2c2c;
        border: none;
        padding: 0.8rem 1.5rem;
        border-radius: 5px;
        font-weight: 600;
        margin-left: 1rem;
        transition: all 0.3s ease;
    }

    .btn-wishlist:hover {
        background-color: #eeeeee;
    }

    .btn-wishlist.active {
        color: #ea5455;
    }

    .product-color-options {
        margin-bottom: 1.5rem;
    }

    .product-color-options .color-option {
        display: inline-block;
        margin-right: 0.5rem;
        cursor: pointer;
    }

    .product-color-options .color-option .filloption {
        height: 30px;
        width: 30px;
        border-radius: 50%;
        display: inline-block;
    }

    .product-color-options .selected {
        border: 2px solid #7367f0;
        border-radius: 50%;
        padding: 3px;
    }

    .product-color-options .b-primary .filloption {
        background-color: #7367f0;
    }

    .product-color-options .b-success .filloption {
        background-color: #28c76f;
    }

    .product-color-options .b-danger .filloption {
        background-color: #ea5455;
    }

    .product-color-options .b-warning .filloption {
        background-color: #ff9f43;
    }

    .product-color-options .b-black .filloption {
        background-color: #22292f;
    }

    /* Swiper */
    .swiper-container {
        margin-top: 2rem;
    }

    .swiper-slide {
        text-align: center;
    }

    .swiper-slide img {
        max-width: 100%;
        height: 100px;
        object-fit: contain;
    }

    /* Related Products */
    .related-products {
        margin-top: 3rem;
    }

    .related-products .title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    .related-product-card {
        background-color: #fff;
        border-radius: 5px;
        overflow: hidden;
        box-shadow: 0 4px 25px 0 rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
    }

    .related-product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 25px 0 rgba(0, 0, 0, 0.25);
    }

    .related-product-img {
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .related-product-img img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .related-product-info {
        padding: 1rem;
    }

    .related-product-title {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.5rem;
        color: #2c2c2c;
    }

    .related-product-price {
        font-weight: 600;
        color: #7367f0;
        margin-bottom: 1rem;
    }

    .related-product-rating {
        color: #ffc107;
        margin-bottom: 1rem;
    }

    .related-product-actions {
        display: flex;
        justify-content: space-between;
    }

    .related-product-actions .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    /* Item Features */
    .item-features {
        background-color: #f7f7f7;
        padding: 2rem;
        border-radius: 5px;
        margin-top: 2rem;
    }

    .item-features .title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    .feature-text {
        margin-bottom: 1rem;
    }

    .feature-icon {
        font-size: 2rem;
        color: #7367f0;
        margin-bottom: 1rem;
    }
</style>
<body>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="#">Cửa hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chi tiết sản phẩm</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <!-- Product Image -->
                    <div class="col-12 col-md-5">
                        <div class="product-img">
                            <img src="https://via.placeholder.com/500" alt="Product Image">
                        </div>
                        <!-- Swiper -->
                        <div class="swiper-container swiper-responsive-breakpoints">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <img src="https://via.placeholder.com/100" alt="Product Image">
                                </div>
                                <div class="swiper-slide">
                                    <img src="https://via.placeholder.com/100" alt="Product Image">
                                </div>
                                <div class="swiper-slide">
                                    <img src="https://via.placeholder.com/100" alt="Product Image">
                                </div>
                                <div class="swiper-slide">
                                    <img src="https://via.placeholder.com/100" alt="Product Image">
                                </div>
                                <div class="swiper-slide">
                                    <img src="https://via.placeholder.com/100" alt="Product Image">
                                </div>
                            </div>
                            <!-- Add Arrows -->
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                    </div>

                    <!-- Product Details -->
                    <div class="col-12 col-md-7">
                        <h1 class="product-title">Bánh Mì Thịt Nướng</h1>
                        <div class="product-ratings">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <span class="ml-2">(4.0)</span>
                        </div>
                        <div class="product-price">
                            <span>35.000đ</span>
                            <span class="old-price">45.000đ</span>
                        </div>
                        <div class="product-description">
                            <p>Bánh mì thịt nướng là một món ăn đường phố phổ biến của Việt Nam, bao gồm bánh mì giòn với nhân thịt heo nướng, rau sống, đồ chua và sốt đặc biệt.</p>
                        </div>
                        <div class="product-features">
                            <h6>Đặc điểm:</h6>
                            <ul>
                                <li>Thịt heo nướng thơm ngon</li>
                                <li>Bánh mì giòn rụm</li>
                                <li>Rau sống tươi ngon</li>
                                <li>Đồ chua đặc trưng</li>
                            </ul>
                        </div>
                        <div class="product-color-options">
                            <h6>Tùy chọn:</h6>
                            <ul class="list-unstyled">
                                <li class="color-option b-primary selected">
                                    <div class="filloption"></div>
                                </li>
                                <li class="color-option b-success">
                                    <div class="filloption"></div>
                                </li>
                                <li class="color-option b-danger">
                                    <div class="filloption"></div>
                                </li>
                                <li class="color-option b-warning">
                                    <div class="filloption"></div>
                                </li>
                                <li class="color-option b-black">
                                    <div class="filloption"></div>
                                </li>
                            </ul>
                        </div>
                        <div class="item-quantity">
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="button" id="button-minus">-</button>
                                <input type="text" class="form-control text-center" value="1" id="quantity">
                                <button class="btn btn-outline-secondary" type="button" id="button-plus">+</button>
                            </div>
                            <button class="btn-cart">
                                <i class="feather icon-shopping-cart"></i>
                                <span>Thêm vào giỏ hàng</span>
                            </button>
                            <button class="btn-wishlist">
                                <i class="feather icon-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Item Features -->
        <div class="item-features">
            <div class="row text-center">
                <div class="col-12 col-md-4">
                    <div class="feature-icon">
                        <i class="feather icon-truck"></i>
                    </div>
                    <h6 class="font-weight-bold">Giao hàng miễn phí</h6>
                    <p class="feature-text">Giao hàng miễn phí cho đơn hàng trên 200.000đ</p>
                </div>
                <div class="col-12 col-md-4">
                    <div class="feature-icon">
                        <i class="feather icon-refresh-cw"></i>
                    </div>
                    <h6 class="font-weight-bold">Đổi trả dễ dàng</h6>
                    <p class="feature-text">Đổi trả sản phẩm trong vòng 24 giờ</p>
                </div>
                <div class="col-12 col-md-4">
                    <div class="feature-icon">
                        <i class="feather icon-shield"></i>
                    </div>
                    <h6 class="font-weight-bold">Bảo đảm chất lượng</h6>
                    <p class="feature-text">Cam kết chất lượng sản phẩm</p>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="related-products">
            <h2 class="title">Sản phẩm liên quan</h2>
            <div class="row">
                <!-- Related Product 1 -->
                <div class="col-12 col-md-3">
                    <div class="related-product-card">
                        <div class="related-product-img">
                            <img src="https://via.placeholder.com/200" alt="Related Product">
                        </div>
                        <div class="related-product-info">
                            <h5 class="related-product-title">Bánh Mì Chả Lụa</h5>
                            <div class="related-product-price">30.000đ</div>
                            <div class="related-product-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                            <div class="related-product-actions">
                                <button class="btn btn-primary btn-sm">Thêm vào giỏ</button>
                                <button class="btn btn-outline-secondary btn-sm">
                                    <i class="feather icon-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Related Product 2 -->
                <div class="col-12 col-md-3">
                    <div class="related-product-card">
                        <div class="related-product-img">
                            <img src="https://via.placeholder.com/200" alt="Related Product">
                        </div>
                        <div class="related-product-info">
                            <h5 class="related-product-title">Bánh Mì Gà Nướng</h5>
                            <div class="related-product-price">32.000đ</div>
                            <div class="related-product-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="related-product-actions">
                                <button class="btn btn-primary btn-sm">Thêm vào giỏ</button>
                                <button class="btn btn-outline-secondary btn-sm">
                                    <i class="feather icon-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Related Product 3 -->
                <div class="col-12 col-md-3">
                    <div class="related-product-card">
                        <div class="related-product-img">
                            <img src="https://via.placeholder.com/200" alt="Related Product">
                        </div>
                        <div class="related-product-info">
                            <h5 class="related-product-title">Bánh Mì Xíu Mại</h5>
                            <div class="related-product-price">28.000đ</div>
                            <div class="related-product-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                            <div class="related-product-actions">
                                <button class="btn btn-primary btn-sm">Thêm vào giỏ</button>
                                <button class="btn btn-outline-secondary btn-sm">
                                    <i class="feather icon-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Related Product 4 -->
                <div class="col-12 col-md-3">
                    <div class="related-product-card">
                        <div class="related-product-img">
                            <img src="https://via.placeholder.com/200" alt="Related Product">
                        </div>
                        <div class="related-product-info">
                            <h5 class="related-product-title">Bánh Mì Pate</h5>
                            <div class="related-product-price">25.000đ</div>
                            <div class="related-product-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                            <div class="related-product-actions">
                                <button class="btn btn-primary btn-sm">Thêm vào giỏ</button>
                                <button class="btn btn-outline-secondary btn-sm">
                                    <i class="feather icon-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@6.8.4/swiper-bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Swiper initialization
            var mySwiper = new Swiper('.swiper-responsive-breakpoints', {
                slidesPerView: 5,
                spaceBetween: 10,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    1600: {
                        slidesPerView: 4,
                        spaceBetween: 10,
                    },
                    1300: {
                        slidesPerView: 3,
                        spaceBetween: 10,
                    },
                    900: {
                        slidesPerView: 2,
                        spaceBetween: 10,
                    },
                    768: {
                        slidesPerView: 1,
                        spaceBetween: 10,
                    }
                }
            });

            // Product color options
            $(".product-color-options li").on("click", function () {
                $(this).addClass('selected').siblings().removeClass('selected');
            });

            // Quantity buttons
            $("#button-plus").on("click", function() {
                var quantity = parseInt($("#quantity").val());
                $("#quantity").val(quantity + 1);
            });

            $("#button-minus").on("click", function() {
                var quantity = parseInt($("#quantity").val());
                if (quantity > 1) {
                    $("#quantity").val(quantity - 1);
                }
            });

            // Wishlist button
            $(".btn-wishlist").on("click", function() {
                $(this).toggleClass('active');
            });
        });
    </script>
@endsection