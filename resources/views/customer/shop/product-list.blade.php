@extends('layouts.customer.fullLayoutMaster')

@section('title', 'Poly Crispy Wings')

@section('content')
<style>
    .product-list-wrapper {
        /* Thiết lập chung */
        background-color: #f8f8f8;
        font-family: 'Montserrat', sans-serif;
        color: #626262;
    }

    .product-list-wrapper .pl-container-fluid {
        padding: 2rem;
    }

    /* Sidebar */
    .product-list-wrapper .pl-sidebar-shop {
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 4px 25px 0 rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .product-list-wrapper .pl-sidebar-shop .pl-filter-heading {
        font-weight: 600;
        margin-bottom: 1.5rem;
        font-size: 1.2rem;
    }

    .product-list-wrapper .pl-sidebar-shop .pl-filter-title {
        font-weight: 600;
        margin-bottom: 1rem;
        font-size: 1rem;
        color: #333;
    }

    .product-list-wrapper .pl-sidebar-shop .pl-categories-list, 
    .product-list-wrapper .pl-sidebar-shop .pl-brands-list,
    .product-list-wrapper .pl-sidebar-shop .pl-ratings-list,
    .product-list-wrapper .pl-sidebar-shop .pl-price-range {
        margin-bottom: 1.5rem;
    }

    .product-list-wrapper .pl-sidebar-shop .pl-custom-control {
        margin-bottom: 0.5rem;
    }

    .product-list-wrapper .pl-sidebar-shop .pl-custom-control-label {
        cursor: pointer;
        font-size: 0.9rem;
    }

    .product-list-wrapper .pl-sidebar-shop .pl-ratings-list-item {
        color: #ffc107;
        cursor: pointer;
        font-size: 1.2rem;
    }

    /* Featured Products */
    .featured-products {
        padding: 4rem 0;
        background-color: var(--bg-gray);
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .product-card {
        background-color: var(--bg-light);
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        box-shadow: 0 4px 6px var(--shadow-color);
        transition: all var(--transition-medium) ease;
    }

    .product-card:hover {
        transform: translateY(-0.625rem);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .product-image {
        position: relative;
        aspect-ratio: 1 / 1;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-overlay {
        position: absolute;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity var(--transition-medium) ease;
    }

    .product-card:hover .product-overlay {
        opacity: 1;
    }

    .product-actions {
        display: flex;
        gap: 0.75rem;
    }

    .action-btn {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--bg-light);
        color: var(--text-dark);
        border: none;
        cursor: pointer;
        transition: all var(--transition-fast) ease;
        box-shadow: 0 2px 5px var(--shadow-color);
    }

    .action-btn:hover {
        transform: scale(1.1);
    }

    .action-btn.cart-btn {
        background-color: var(--jollibee-red);
        color: var(--text-light);
    }

    .discount-badge {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        background-color: var(--jollibee-yellow);
        color: var(--jollibee-red);
        font-weight: 700;
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: var(--border-radius-xl);
        z-index: 1;
    }

    .product-info {
        padding: 1rem;
    }

    .product-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--jollibee-red);
        margin-bottom: 0.25rem;
    }

    .product-description {
        font-size: 0.875rem;
        color: var(--text-gray);
        margin-bottom: 0.75rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-price-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .product-price {
        display: flex;
        align-items: flex-end;
        gap: 0.5rem;
    }

    .current-price {
        font-size: 1.125rem;
        font-weight: 700;
    }

    .original-price {
        font-size: 0.875rem;
        color: var(--text-gray);
        text-decoration: line-through;
    }

    .add-to-cart-btn {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--jollibee-red);
        color: var(--text-light);
        border: none;
        cursor: pointer;
        transition: all var(--transition-fast) ease;
    }

    .add-to-cart-btn:hover {
        background-color: var(--jollibee-dark-red);
    }

    .view-all-container {
        text-align: center;
    }

    /* Header */
    .product-list-wrapper .pl-ecommerce-header {
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 4px 25px 0 rgba(0, 0, 0, 0.1);
        padding: 1rem;
        margin-bottom: 2rem;
    }

    .product-list-wrapper .pl-ecommerce-header-items {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .product-list-wrapper .pl-result-toggler {
        display: flex;
        align-items: center;
    }

    .product-list-wrapper .pl-search-results {
        font-weight: 600;
        margin-left: 1rem;
    }

    .product-list-wrapper .pl-view-options {
        display: flex;
        align-items: center;
    }

    .product-list-wrapper .pl-view-btn {
        background-color: #fff;
        border: none;
        box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.14);
        margin-right: 0.5rem;
        padding: 0.5rem 0.75rem;
        border-radius: 5px;
        cursor: pointer;
    }

    .product-list-wrapper .pl-view-btn.active {
        color: #7367f0;
    }

    .product-list-wrapper .pl-sort-dropdown {
        margin-left: 0.5rem;
    }

    /* Search */
    .product-list-wrapper .pl-search-product {
        position: relative;
        margin-bottom: 2rem;
    }

    .product-list-wrapper .pl-search-product input {
        height: 48px;
        border: none;
        border-radius: 5px;
        box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.14);
        padding-left: 1rem;
        width: 100%;
    }

    .product-list-wrapper .pl-search-product .pl-search-icon {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #626262;
    }

    /* Product Grid */
    .product-list-wrapper .pl-grid-view {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
    }

    /* Product List */
    .product-list-wrapper .pl-list-view {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .product-list-wrapper .pl-list-view .pl-ecommerce-card {
        display: grid;
        grid-template-columns: 1fr 2fr 1fr;
    }

    .product-list-wrapper .pl-list-view .pl-ecommerce-card .pl-item-img {
        height: 100%;
        padding: 1rem;
    }

    .product-list-wrapper .pl-list-view .pl-ecommerce-card .pl-card-body {
        border-right: 1px solid #dae1e7;
        padding: 1.5rem;
    }

    .product-list-wrapper .pl-list-view .pl-ecommerce-card .pl-item-options {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .product-list-wrapper .pl-list-view .pl-item-price {
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .product-list-wrapper .pl-list-view .pl-item-company {
        margin-bottom: 1rem;
    }

    .product-list-wrapper .pl-list-view .pl-item-company .pl-company-name {
        color: #7367f0;
    }

    /* Product Card */
    .product-list-wrapper .pl-ecommerce-card {
        background-color: #fff;
        border-radius: 5px;
        overflow: hidden;
        box-shadow: 0 4px 25px 0 rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        position: relative;
    }

    .product-list-wrapper .pl-ecommerce-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 25px 0 rgba(0, 0, 0, 0.25);
    }

    .product-list-wrapper .pl-item-img {
        padding: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 250px;
        background-color: #fff;
    }

    .product-list-wrapper .pl-img-fluid {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }

    .product-list-wrapper .pl-card-body {
        padding: 1.5rem;
    }

    .product-list-wrapper .pl-item-rating {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #7367f0;
        color: white;
        border-radius: 4px;
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .product-list-wrapper .pl-item-rating i {
        margin-right: 0.25rem;
    }

    .product-list-wrapper .pl-item-price {
        font-weight: 700;
        font-size: 1.25rem;
        color: #333;
        margin-bottom: 0.75rem;
    }

    .product-list-wrapper .pl-item-name {
        font-weight: 600;
        font-size: 1rem;
        color: #2c2c2c;
        margin-bottom: 0.5rem;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .product-list-wrapper .pl-item-description {
        font-size: 0.875rem;
        color: #626262;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-list-wrapper .pl-item-options {
        display: flex;
        justify-content: space-between;
        margin-top: 1rem;
    }

    .product-list-wrapper .pl-wishlist, 
    .product-list-wrapper .pl-cart {
        padding: 0.8rem 1rem;
        border-radius: 5px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        text-transform: uppercase;
    }

    .product-list-wrapper .pl-wishlist {
        background-color: #f6f6f6;
        color: #2c2c2c;
        border: none;
    }

    .product-list-wrapper .pl-wishlist:hover {
        background-color: #eeeeee;
    }

    .product-list-wrapper .pl-wishlist.active {
        color: #ea5455;
    }

    .product-list-wrapper .pl-cart {
        background-color: #7367f0;
        color: white;
        border: none;
        flex-grow: 1;
        margin-left: 0.5rem;
    }

    .product-list-wrapper .pl-cart:hover {
        background-color: #5e50ee;
    }

    .product-list-wrapper .pl-wishlist i, 
    .product-list-wrapper .pl-cart i {
        margin-right: 0.5rem;
    }

    /* Pagination */
    .product-list-wrapper .pl-pagination {
        margin-top: 2rem;
        justify-content: center;
    }

    .product-list-wrapper .pl-page-item.active .pl-page-link {
        background-color: #7367f0;
        border-color: #7367f0;
    }

    .product-list-wrapper .pl-page-link {
        color: #7367f0;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .product-list-wrapper .pl-grid-view {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .product-list-wrapper .pl-grid-view {
            grid-template-columns: 1fr;
        }
        
        .product-list-wrapper .pl-list-view .pl-ecommerce-card {
            grid-template-columns: 1fr;
        }
        
        .product-list-wrapper .pl-list-view .pl-ecommerce-card .pl-card-body {
            border-right: none;
        }
    }
</style>
<div class="product-list-wrapper">
    <div class="container-fluid pl-container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="pl-sidebar-shop">
                    <h6 class="pl-filter-heading d-none d-lg-block">Bộ lọc</h6>
                    
                    <!-- Danh mục -->
                    <div class="pl-categories-list">
                        <h6 class="pl-filter-title">Danh mục</h6>
                        <div class="form-check pl-custom-control">
                            <input type="checkbox" class="form-check-input" id="category1">
                            <label class="form-check-label pl-custom-control-label" for="category1">Đồ ăn nhanh</label>
                        </div>
                        <div class="form-check pl-custom-control">
                            <input type="checkbox" class="form-check-input" id="category2">
                            <label class="form-check-label pl-custom-control-label" for="category2">Đồ uống</label>
                        </div>
                        <div class="form-check pl-custom-control">
                            <input type="checkbox" class="form-check-input" id="category3">
                            <label class="form-check-label pl-custom-control-label" for="category3">Món chính</label>
                        </div>
                        <div class="form-check pl-custom-control">
                            <input type="checkbox" class="form-check-input" id="category4">
                            <label class="form-check-label pl-custom-control-label" for="category4">Tráng miệng</label>
                        </div>
                    </div>
                    
                    <!-- Thương hiệu -->
                    <div class="pl-brands-list">
                        <h6 class="pl-filter-title">Thương hiệu</h6>
                        <div class="form-check pl-custom-control">
                            <input type="checkbox" class="form-check-input" id="brand1">
                            <label class="form-check-label pl-custom-control-label" for="brand1">DevFoods</label>
                        </div>
                        <div class="form-check pl-custom-control">
                            <input type="checkbox" class="form-check-input" id="brand2">
                            <label class="form-check-label pl-custom-control-label" for="brand2">FoodMaster</label>
                        </div>
                        <div class="form-check pl-custom-control">
                            <input type="checkbox" class="form-check-input" id="brand3">
                            <label class="form-check-label pl-custom-control-label" for="brand3">GourmetViet</label>
                        </div>
                    </div>
                    
                    <!-- Khoảng giá -->
                    <div class="pl-price-range">
                        <h6 class="pl-filter-title">Khoảng giá</h6>
                        <div id="price-slider"></div>
                        <div class="d-flex justify-content-between mt-2">
                            <span id="min-price">0đ</span>
                            <span id="max-price">500.000đ</span>
                        </div>
                    </div>
                    
                    <!-- Đánh giá -->
                    <div class="pl-ratings-list">
                        <h6 class="pl-filter-title">Đánh giá</h6>
                        <div class="pl-ratings-list-item mb-1">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <span class="ml-1">(5)</span>
                        </div>
                        <div class="pl-ratings-list-item mb-1">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <span class="ml-1">(4)</span>
                        </div>
                        <div class="pl-ratings-list-item mb-1">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <i class="far fa-star"></i>
                            <span class="ml-1">(3)</span>
                        </div>
                        <div class="pl-ratings-list-item mb-1">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <i class="far fa-star"></i>
                            <i class="far fa-star"></i>
                            <span class="ml-1">(2)</span>
                        </div>
                        <div class="pl-ratings-list-item">
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <i class="far fa-star"></i>
                            <i class="far fa-star"></i>
                            <i class="far fa-star"></i>
                            <span class="ml-1">(1)</span>
                        </div>
                    </div>
                    
                    <!-- Nút lọc -->
                    <button class="btn btn-primary w-100 mt-2">Áp dụng bộ lọc</button>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Header -->
                <div class="pl-ecommerce-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="pl-ecommerce-header-items">
                                <div class="pl-result-toggler">
                                    <button class="navbar-toggler shop-sidebar-toggler d-block d-lg-none" type="button">
                                        <i class="fas fa-sliders-h"></i>
                                    </button>
                                    <div class="pl-search-results">
                                        <span>16 sản phẩm được tìm thấy</span>
                                    </div>
                                </div>
                                <div class="pl-view-options d-flex">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="pl-view-btn pl-grid-view-btn active">
                                            <i class="fas fa-th-large"></i>
                                        </button>
                                        <button type="button" class="pl-view-btn pl-list-view-btn">
                                            <i class="fas fa-list"></i>
                                        </button>
                                    </div>
                                    <div class="pl-sort-dropdown ml-2">
                                        <select class="form-select">
                                            <option value="featured">Nổi bật</option>
                                            <option value="lowest">Giá: Thấp đến cao</option>
                                            <option value="highest">Giá: Cao đến thấp</option>
                                            <option value="rating">Đánh giá</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Search -->
                <div class="pl-search-product">
                    <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm...">
                    <div class="pl-search-icon">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                
                <!-- Products Grid View -->
                <div class="pl-grid-view">
                <div class="product-card" data-product-id="1">
                        <div class="product-image">
                            <img src="images/product-1.jpg" alt="Gà Giòn Vui Vẻ (1 miếng)">
                            <div class="product-overlay">
                                <div class="product-actions">
                                    <button class="action-btn favorite-btn">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <button class="action-btn cart-btn">
                                        <i class="fas fa-shopping-bag"></i>
                                    </button>
                                    <button class="action-btn info-btn">
                                        <i class="fas fa-info"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title">Gà Giòn Vui Vẻ (1 miếng)</h3>
                            <p class="product-description">Gà rán giòn thơm ngon, hương vị đặc trưng của Jollibee với
                                lớp bột chiên giòn rụm và thịt gà mềm, thơm ngon.</p>
                            <div class="product-price-actions">
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
                            <img src="images/product-2.jpg" alt="Gà Sốt Cay (1 miếng)">
                            <div class="product-overlay">
                                <div class="product-actions">
                                    <button class="action-btn favorite-btn">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <button class="action-btn cart-btn">
                                        <i class="fas fa-shopping-bag"></i>
                                    </button>
                                    <button class="action-btn info-btn">
                                        <i class="fas fa-info"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title">Gà Sốt Cay (1 miếng)</h3>
                            <p class="product-description">Gà rán phủ sốt cay đặc biệt, cay nồng hấp dẫn, thịt gà mềm,
                                thơm ngon.</p>
                            <div class="product-price-actions">
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
                            <img src="images/product-3.jpg" alt="Burger Gà Giòn">
                            <div class="product-overlay">
                                <div class="product-actions">
                                    <button class="action-btn favorite-btn">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <button class="action-btn cart-btn">
                                        <i class="fas fa-shopping-bag"></i>
                                    </button>
                                    <button class="action-btn info-btn">
                                        <i class="fas fa-info"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title">Burger Gà Giòn</h3>
                            <p class="product-description">Burger với lớp thịt gà giòn, rau tươi và sốt mayonnaise đặc
                                biệt, đậm đà hương vị.</p>
                            <div class="product-price-actions">
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
                            <img src="images/product-4.jpg" alt="Mỳ Ý Sốt Bò Bằm">
                            <div class="product-overlay">
                                <div class="product-actions">
                                    <button class="action-btn favorite-btn">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <button class="action-btn cart-btn">
                                        <i class="fas fa-shopping-bag"></i>
                                    </button>
                                    <button class="action-btn info-btn">
                                        <i class="fas fa-info"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="discount-badge">Giảm giá</div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title">Mỳ Ý Sốt Bò Bằm</h3>
                            <p class="product-description">Mỳ Ý với sốt bò bằm đậm đà, thơm ngon, kết hợp với phô mai và
                                gia vị đặc biệt.</p>
                            <div class="product-price-actions">
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

                    <div class="product-card" data-product-id="5">
                        <div class="product-image">
                            <img src="images/product-4.jpg" alt="Mỳ Ý Sốt Bò Bằm">
                            <div class="product-overlay">
                                <div class="product-actions">
                                    <button class="action-btn favorite-btn">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <button class="action-btn cart-btn">
                                        <i class="fas fa-shopping-bag"></i>
                                    </button>
                                    <button class="action-btn info-btn">
                                        <i class="fas fa-info"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="discount-badge">Giảm giá</div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title">Mỳ Ý Sốt Bò Bằm</h3>
                            <p class="product-description">Mỳ Ý với sốt bò bằm đậm đà, thơm ngon, kết hợp với phô mai và
                                gia vị đặc biệt.</p>
                            <div class="product-price-actions">
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

                    <div class="product-card" data-product-id="6">
                        <div class="product-image">
                            <img src="images/product-4.jpg" alt="Mỳ Ý Sốt Bò Bằm">
                            <div class="product-overlay">
                                <div class="product-actions">
                                    <button class="action-btn favorite-btn">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <button class="action-btn cart-btn">
                                        <i class="fas fa-shopping-bag"></i>
                                    </button>
                                    <button class="action-btn info-btn">
                                        <i class="fas fa-info"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="discount-badge">Giảm giá</div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title">Mỳ Ý Sốt Bò Bằm</h3>
                            <p class="product-description">Mỳ Ý với sốt bò bằm đậm đà, thơm ngon, kết hợp với phô mai và
                                gia vị đặc biệt.</p>
                            <div class="product-price-actions">
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
                
                <!-- Pagination -->
                <nav aria-label="Page navigation">
                    <ul class="pagination pl-pagination">
                        <li class="page-item pl-page-item disabled">
                            <a class="page-link pl-page-link" href="#" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <li class="page-item pl-page-item active"><a class="page-link pl-page-link" href="#">1</a></li>
                        <li class="page-item pl-page-item"><a class="page-link pl-page-link" href="#">2</a></li>
                        <li class="page-item pl-page-item"><a class="page-link pl-page-link" href="#">3</a></li>
                        <li class="page-item pl-page-item">
                            <a class="page-link pl-page-link" href="#" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/nouislider@15.6.1/dist/nouislider.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chuyển đổi giữa chế độ xem lưới và danh sách
            const gridViewBtn = document.querySelector('.pl-grid-view-btn');
            const listViewBtn = document.querySelector('.pl-list-view-btn');
            const productsContainer = document.querySelector('.pl-grid-view');
            
            if (gridViewBtn && listViewBtn && productsContainer) {
                // Lưu trữ trạng thái ban đầu của các thẻ product-card
                const productCards = document.querySelectorAll('.product-card');
                const originalStyles = [];
                
                // Lưu lại style ban đầu của mỗi thẻ
                productCards.forEach((card, index) => {
                    originalStyles[index] = {
                        display: card.style.display || 'block',
                        gridTemplateColumns: '',
                        image: {
                            height: card.querySelector('.product-image')?.style.height || '',
                            aspectRatio: card.querySelector('.product-image')?.style.aspectRatio || '1 / 1'
                        },
                        info: {
                            display: card.querySelector('.product-info')?.style.display || '',
                            flexDirection: card.querySelector('.product-info')?.style.flexDirection || '',
                            justifyContent: card.querySelector('.product-info')?.style.justifyContent || ''
                        }
                    };
                });
                
                // Xử lý khi click vào nút hiển thị dạng lưới
                gridViewBtn.addEventListener('click', function() {
                    productsContainer.className = 'pl-grid-view';
                    gridViewBtn.classList.add('active');
                    listViewBtn.classList.remove('active');
                    
                    // Khôi phục lại style ban đầu cho các thẻ product-card
                    productCards.forEach((card, index) => {
                        card.style.display = originalStyles[index].display;
                        card.style.gridTemplateColumns = '';
                        
                        const productImage = card.querySelector('.product-image');
                        if (productImage) {
                            productImage.style.height = '';
                            productImage.style.aspectRatio = '1 / 1';
                        }
                        
                        const productInfo = card.querySelector('.product-info');
                        if (productInfo) {
                            productInfo.style.display = '';
                            productInfo.style.flexDirection = '';
                            productInfo.style.justifyContent = '';
                            productInfo.style.padding = '1rem';
                        }
                        
                        // Khôi phục hiển thị cho product-overlay và product-actions
                        const productOverlay = card.querySelector('.product-overlay');
                        if (productOverlay) {
                            productOverlay.style.display = 'flex';
                        }
                    });
                });
                
                // Xử lý khi click vào nút hiển thị dạng danh sách
                listViewBtn.addEventListener('click', function() {
                    productsContainer.className = 'pl-list-view';
                    listViewBtn.classList.add('active');
                    gridViewBtn.classList.remove('active');
                    
                    // Điều chỉnh hiển thị cho các product-card khi ở chế độ danh sách
                    productCards.forEach(card => {
                        // Thay đổi cách hiển thị cho chế độ danh sách
                        card.style.display = 'grid';
                        card.style.gridTemplateColumns = '1fr 2fr 1fr';
                        card.style.alignItems = 'center';
                        
                        // Tìm và điều chỉnh các phần tử con
                        const productImage = card.querySelector('.product-image');
                        const productInfo = card.querySelector('.product-info');
                        const productActions = card.querySelector('.product-price-actions');
                        
                        if (productImage) {
                            productImage.style.height = '100%';
                            productImage.style.aspectRatio = 'auto';
                        }
                        
                        if (productInfo) {
                            productInfo.style.display = 'flex';
                            productInfo.style.flexDirection = 'column';
                            productInfo.style.justifyContent = 'center';
                            productInfo.style.padding = '1rem 1.5rem';
                        }
                        
                        // Điều chỉnh hiển thị của product-overlay trong chế độ danh sách
                        const productOverlay = card.querySelector('.product-overlay');
                        if (productOverlay) {
                            productOverlay.style.display = 'none';
                        }
                    });
                });
            }
            
            // Xử lý nút yêu thích trong product-card
            const favoriteButtons = document.querySelectorAll('.favorite-btn');
            favoriteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const icon = this.querySelector('i');
                    if (icon.classList.contains('far')) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        this.classList.add('active');
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        this.classList.remove('active');
                    }
                });
            });
            
            // Xử lý nút thêm vào giỏ hàng trong product-card
            const addToCartButtons = document.querySelectorAll('.add-to-cart-btn, .cart-btn');
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Lưu lại màu ban đầu
                    const originalColor = this.style.backgroundColor;
                    const originalText = this.innerHTML;
                    
                    // Hiệu ứng khi click vào nút thêm vào giỏ
                    this.style.backgroundColor = '#28a745';
                    
                    // Nếu có icon, thay đổi icon
                    const icon = this.querySelector('i');
                    if (icon) {
                        const originalIcon = icon.className;
                        icon.className = 'fas fa-check';
                        
                        // Khôi phục sau 1 giây
                        setTimeout(() => {
                            icon.className = originalIcon;
                            this.style.backgroundColor = originalColor;
                        }, 1000);
                    } else {
                        // Nếu là nút text, thay đổi text
                        this.innerHTML = '<i class="fas fa-check"></i> Đã thêm';
                        
                        // Khôi phục sau 1 giây
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.style.backgroundColor = originalColor;
                        }, 1000);
                    }
                    
                    // Ở đây có thể thêm code để thêm sản phẩm vào giỏ hàng thực tế
                    const productCard = this.closest('.product-card');
                    if (productCard) {
                        const productId = productCard.getAttribute('data-product-id');
                        const productTitle = productCard.querySelector('.product-title').textContent;
                        const productPrice = productCard.querySelector('.current-price').textContent;
                        
                        console.log(`Đã thêm sản phẩm vào giỏ hàng: ID=${productId}, Tên=${productTitle}, Giá=${productPrice}`);
                        // Thêm code xử lý thêm vào giỏ hàng ở đây
                    }
                });
            });
            
            // Xử lý nút thông tin trong product-card
            const infoButtons = document.querySelectorAll('.info-btn');
            infoButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productCard = this.closest('.product-card');
                    if (productCard) {
                        const productId = productCard.getAttribute('data-product-id');
                        // Điều hướng đến trang chi tiết sản phẩm
                        window.location.href = `product/product-detail?id=${productId}`;
                    }
                });
            });
            
            // Khởi tạo thanh trượt giá
            const priceSlider = document.getElementById('price-slider');
            if (priceSlider && typeof noUiSlider !== 'undefined') {
                noUiSlider.create(priceSlider, {
                    start: [0, 500000],
                    connect: true,
                    range: {
                        'min': 0,
                        'max': 500000
                    },
                    format: {
                        to: function(value) {
                            return Math.round(value);
                        },
                        from: function(value) {
                            return value;
                        }
                    },
                    step: 1000
                });
                
                const minPrice = document.getElementById('min-price');
                const maxPrice = document.getElementById('max-price');
                
                priceSlider.noUiSlider.on('update', function(values, handle) {
                    const value = Math.round(values[handle]);
                    if (handle === 0) {
                        minPrice.textContent = value.toLocaleString('vi-VN') + 'đ';
                    } else {
                        maxPrice.textContent = value.toLocaleString('vi-VN') + 'đ';
                    }
                });
            } else if (priceSlider) {
                console.warn('noUiSlider chưa được tải. Vui lòng kiểm tra thư viện.');
            }
            
            // Hiển thị sidebar trên mobile
            const sidebarToggler = document.querySelector('.shop-sidebar-toggler');
            const sidebar = document.querySelector('.pl-sidebar-shop');
            
            if (sidebarToggler && sidebar) {
                // Thêm class để ẩn sidebar trên mobile ban đầu
                if (window.innerWidth < 992) {
                    sidebar.classList.add('d-none');
                }
                
                // Xử lý sự kiện click vào nút toggler
                sidebarToggler.addEventListener('click', function() {
                    sidebar.classList.toggle('d-none');
                    
                    // Thêm overlay khi sidebar hiển thị
                    if (!sidebar.classList.contains('d-none')) {
                        const overlay = document.createElement('div');
                        overlay.className = 'pl-sidebar-overlay';
                        overlay.style.position = 'fixed';
                        overlay.style.top = '0';
                        overlay.style.left = '0';
                        overlay.style.width = '100%';
                        overlay.style.height = '100%';
                        overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                        overlay.style.zIndex = '999';
                        document.body.appendChild(overlay);
                        
                        // Đặt sidebar lên trên overlay
                        sidebar.style.position = 'fixed';
                        sidebar.style.top = '0';
                        sidebar.style.left = '0';
                        sidebar.style.width = '80%';
                        sidebar.style.height = '100%';
                        sidebar.style.zIndex = '1000';
                        sidebar.style.overflowY = 'auto';
                        sidebar.style.backgroundColor = '#fff';
                        
                        // Xử lý sự kiện click vào overlay để đóng sidebar
                        overlay.addEventListener('click', function() {
                            sidebar.classList.add('d-none');
                            document.body.removeChild(overlay);
                            resetSidebarStyle();
                        });
                    } else {
                        // Xóa overlay khi đóng sidebar
                        const overlay = document.querySelector('.pl-sidebar-overlay');
                        if (overlay) {
                            document.body.removeChild(overlay);
                        }
                        resetSidebarStyle();
                    }
                });
                
                // Hàm reset style của sidebar khi đóng
                function resetSidebarStyle() {
                    sidebar.style.position = '';
                    sidebar.style.top = '';
                    sidebar.style.left = '';
                    sidebar.style.width = '';
                    sidebar.style.height = '';
                    sidebar.style.zIndex = '';
                    sidebar.style.overflowY = '';
                    sidebar.style.backgroundColor = '';
                }
                
                // Xử lý sự kiện resize cửa sổ
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 992) {
                        sidebar.classList.remove('d-none');
                        resetSidebarStyle();
                        
                        // Xóa overlay nếu có
                        const overlay = document.querySelector('.pl-sidebar-overlay');
                        if (overlay) {
                            document.body.removeChild(overlay);
                        }
                    } else {
                        // Ẩn sidebar khi chuyển về mobile nếu không đang mở
                        if (!document.querySelector('.pl-sidebar-overlay')) {
                            sidebar.classList.add('d-none');
                        }
                    }
                });
            }
        });
    </script>
        
    <!-- Thêm script cho noUiSlider -->
    <script src="https://cdn.jsdelivr.net/npm/nouislider@15.6.1/dist/nouislider.min.js"></script>

    <!-- Core JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/jollibee-main.js') }}"></script>
    
    <!-- Page specific scripts -->
    @yield('page-scripts')
@endsection