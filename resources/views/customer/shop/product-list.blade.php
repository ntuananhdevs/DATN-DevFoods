<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm - DevFoods</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('fonts/feather/style.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/noui-slider@15.6.1/dist/nouislider.min.css">
</head>
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

    /* Sidebar */
    .sidebar-shop {
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 4px 25px 0 rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .sidebar-shop .filter-heading {
        font-weight: 600;
        margin-bottom: 1.5rem;
        font-size: 1.2rem;
    }

    .sidebar-shop .filter-title {
        font-weight: 600;
        margin-bottom: 1rem;
        font-size: 1rem;
        color: #333;
    }

    .sidebar-shop .categories-list, 
    .sidebar-shop .brands-list,
    .sidebar-shop .ratings-list,
    .sidebar-shop .price-range {
        margin-bottom: 1.5rem;
    }

    .sidebar-shop .custom-control {
        margin-bottom: 0.5rem;
    }

    .sidebar-shop .custom-control-label {
        cursor: pointer;
        font-size: 0.9rem;
    }

    .sidebar-shop .ratings-list-item {
        color: #ffc107;
        cursor: pointer;
        font-size: 1.2rem;
    }

    /* Header */
    .ecommerce-header {
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 4px 25px 0 rgba(0, 0, 0, 0.1);
        padding: 1rem;
        margin-bottom: 2rem;
    }

    .ecommerce-header-items {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .result-toggler {
        display: flex;
        align-items: center;
    }

    .search-results {
        font-weight: 600;
        margin-left: 1rem;
    }

    .view-options {
        display: flex;
        align-items: center;
    }

    .view-btn {
        background-color: #fff;
        border: none;
        box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.14);
        margin-right: 0.5rem;
        padding: 0.5rem 0.75rem;
        border-radius: 5px;
        cursor: pointer;
    }

    .view-btn.active {
        color: #7367f0;
    }

    .sort-dropdown {
        margin-left: 0.5rem;
    }

    /* Search */
    .search-product {
        position: relative;
        margin-bottom: 2rem;
    }

    .search-product input {
        height: 48px;
        border: none;
        border-radius: 5px;
        box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.14);
        padding-left: 1rem;
        width: 100%;
    }

    .search-product .search-icon {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #626262;
    }

    /* Product Grid */
    .grid-view {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
    }

    /* Product List */
    .list-view {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .list-view .ecommerce-card {
        display: grid;
        grid-template-columns: 1fr 2fr 1fr;
    }

    .list-view .ecommerce-card .item-img {
        height: 100%;
        padding: 1rem;
    }

    .list-view .ecommerce-card .card-body {
        border-right: 1px solid #dae1e7;
        padding: 1.5rem;
    }

    .list-view .ecommerce-card .item-options {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .list-view .item-price {
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .list-view .item-company {
        margin-bottom: 1rem;
    }

    .list-view .item-company .company-name {
        color: #7367f0;
    }

    /* Product Card */
    .ecommerce-card {
        background-color: #fff;
        border-radius: 5px;
        overflow: hidden;
        box-shadow: 0 4px 25px 0 rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        position: relative;
    }

    .ecommerce-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 25px 0 rgba(0, 0, 0, 0.25);
    }

    .item-img {
        padding: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 250px;
        background-color: #fff;
    }

    .img-fluid {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }

    .card-body {
        padding: 1.5rem;
    }

    .item-rating {
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

    .item-rating i {
        margin-right: 0.25rem;
    }

    .item-price {
        font-weight: 700;
        font-size: 1.25rem;
        color: #333;
        margin-bottom: 0.75rem;
    }

    .item-name {
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

    .item-description {
        font-size: 0.875rem;
        color: #626262;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .item-options {
        display: flex;
        justify-content: space-between;
        margin-top: 1rem;
    }

    .wishlist, .cart {
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

    .wishlist {
        background-color: #f6f6f6;
        color: #2c2c2c;
        border: none;
    }

    .wishlist:hover {
        background-color: #eeeeee;
    }

    .wishlist.active {
        color: #ea5455;
    }

    .cart {
        background-color: #7367f0;
        color: white;
        border: none;
        flex-grow: 1;
        margin-left: 0.5rem;
    }

    .cart:hover {
        background-color: #5e50ee;
    }

    .wishlist i, .cart i {
        margin-right: 0.5rem;
    }

    /* Pagination */
    .pagination {
        margin-top: 2rem;
        justify-content: center;
    }

    .page-item.active .page-link {
        background-color: #7367f0;
        border-color: #7367f0;
    }

    .page-link {
        color: #7367f0;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .grid-view {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .grid-view {
            grid-template-columns: 1fr;
        }
        
        .list-view .ecommerce-card {
            grid-template-columns: 1fr;
        }
        
        .list-view .ecommerce-card .card-body {
            border-right: none;
        }
    }
</style>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="sidebar-shop">
                    <h6 class="filter-heading d-none d-lg-block">Bộ lọc</h6>
                    
                    <!-- Danh mục -->
                    <div class="categories-list">
                        <h6 class="filter-title">Danh mục</h6>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="category1">
                            <label class="form-check-label" for="category1">Đồ ăn nhanh</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="category2">
                            <label class="form-check-label" for="category2">Đồ uống</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="category3">
                            <label class="form-check-label" for="category3">Món chính</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="category4">
                            <label class="form-check-label" for="category4">Tráng miệng</label>
                        </div>
                    </div>
                    
                    <!-- Thương hiệu -->
                    <div class="brands-list">
                        <h6 class="filter-title">Thương hiệu</h6>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="brand1">
                            <label class="form-check-label" for="brand1">DevFoods</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="brand2">
                            <label class="form-check-label" for="brand2">FoodMaster</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="brand3">
                            <label class="form-check-label" for="brand3">GourmetViet</label>
                        </div>
                    </div>
                    
                    <!-- Khoảng giá -->
                    <div class="price-range">
                        <h6 class="filter-title">Khoảng giá</h6>
                        <div id="price-slider"></div>
                        <div class="d-flex justify-content-between mt-2">
                            <span id="min-price">0đ</span>
                            <span id="max-price">500.000đ</span>
                        </div>
                    </div>
                    
                    <!-- Đánh giá -->
                    <div class="ratings-list">
                        <h6 class="filter-title">Đánh giá</h6>
                        <div class="ratings-list-item mb-1">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <span class="ml-1">(5)</span>
                        </div>
                        <div class="ratings-list-item mb-1">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <span class="ml-1">(4)</span>
                        </div>
                        <div class="ratings-list-item mb-1">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <i class="far fa-star"></i>
                            <span class="ml-1">(3)</span>
                        </div>
                        <div class="ratings-list-item mb-1">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <i class="far fa-star"></i>
                            <i class="far fa-star"></i>
                            <span class="ml-1">(2)</span>
                        </div>
                        <div class="ratings-list-item">
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
                <div class="ecommerce-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="ecommerce-header-items">
                                <div class="result-toggler">
                                    <button class="navbar-toggler shop-sidebar-toggler d-block d-lg-none" type="button">
                                        <i class="fas fa-sliders-h"></i>
                                    </button>
                                    <div class="search-results">
                                        <span>16 sản phẩm được tìm thấy</span>
                                    </div>
                                </div>
                                <div class="view-options d-flex">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="view-btn grid-view-btn active">
                                            <i class="fas fa-th-large"></i>
                                        </button>
                                        <button type="button" class="view-btn list-view-btn">
                                            <i class="fas fa-list"></i>
                                        </button>
                                    </div>
                                    <div class="sort-dropdown ml-2">
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
                <div class="search-product">
                    <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm...">
                    <div class="search-icon">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                
                <!-- Products Grid View -->
                <div class="grid-view">
                    <!-- Product 1 -->
                    <a href="product/product-detail" class="ecommerce-card">
                        <div class="item-rating">
                            <i class="fas fa-star"></i> 4.5
                        </div>
                        <div class="item-img">
                            <img src="https://via.placeholder.com/300x200" alt="Sản phẩm 1" class="img-fluid">
                        </div>
                        <div class="card-body">
                            <h6 class="item-price">89.000đ</h6>
                            <h5 class="item-name">Hamburger Bò Phô Mai Đặc Biệt</h5>
                            <p class="item-description">Hamburger bò với phô mai tan chảy, rau tươi và sốt đặc biệt.</p>
                            <div class="item-options">
                                <button class="wishlist">
                                    <i class="far fa-heart"></i> Yêu thích
                                </button>
                                <button class="cart">
                                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </a>
                    
                    <!-- Product 2 -->
                    <div class="ecommerce-card">
                        <div class="item-rating">
                            <i class="fas fa-star"></i> 4.8
                        </div>
                        <div class="item-img">
                            <img src="https://via.placeholder.com/300x200" alt="Sản phẩm 2" class="img-fluid">
                        </div>
                        <div class="card-body">
                            <h6 class="item-price">65.000đ</h6>
                            <h5 class="item-name">Pizza Hải Sản Đặc Biệt</h5>
                            <p class="item-description">Pizza với đế giòn, phủ hải sản tươi ngon và phô mai Mozzarella.</p>
                            <div class="item-options">
                                <button class="wishlist">
                                    <i class="far fa-heart"></i> Yêu thích
                                </button>
                                <button class="cart">
                                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 3 -->
                    <div class="ecommerce-card">
                        <div class="item-rating">
                            <i class="fas fa-star"></i> 4.2
                        </div>
                        <div class="item-img">
                            <img src="https://via.placeholder.com/300x200" alt="Sản phẩm 3" class="img-fluid">
                        </div>
                        <div class="card-body">
                            <h6 class="item-price">45.000đ</h6>
                            <h5 class="item-name">Mì Ý Sốt Bò Bằm</h5>
                            <p class="item-description">Mì Ý với sốt bò bằm đậm đà, phô mai Parmesan và rau thơm.</p>
                            <div class="item-options">
                                <button class="wishlist">
                                    <i class="far fa-heart"></i> Yêu thích
                                </button>
                                <button class="cart">
                                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 4 -->
                    <div class="ecommerce-card">
                        <div class="item-rating">
                            <i class="fas fa-star"></i> 4.7
                        </div>
                        <div class="item-img">
                            <img src="https://via.placeholder.com/300x200" alt="Sản phẩm 4" class="img-fluid">
                        </div>
                        <div class="card-body">
                            <h6 class="item-price">35.000đ</h6>
                            <h5 class="item-name">Trà Sữa Trân Châu Đường Đen</h5>
                            <p class="item-description">Trà sữa thơm ngon với trân châu đường đen mềm dẻo.</p>
                            <div class="item-options">
                                <button class="wishlist">
                                    <i class="far fa-heart"></i> Yêu thích
                                </button>
                                <button class="cart">
                                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 5 -->
                    <div class="ecommerce-card">
                        <div class="item-rating">
                            <i class="fas fa-star"></i> 4.6
                        </div>
                        <div class="item-img">
                            <img src="https://via.placeholder.com/300x200" alt="Sản phẩm 5" class="img-fluid">
                        </div>
                        <div class="card-body">
                            <h6 class="item-price">55.000đ</h6>
                            <h5 class="item-name">Gà Rán Sốt Cay Hàn Quốc</h5>
                            <p class="item-description">Gà rán giòn với sốt cay Hàn Quốc đậm đà, kèm rau sống.</p>
                            <div class="item-options">
                                <button class="wishlist">
                                    <i class="far fa-heart"></i> Yêu thích
                                </button>
                                <button class="cart">
                                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 6 -->
                    <div class="ecommerce-card">
                        <div class="item-rating">
                            <i class="fas fa-star"></i> 4.3
                        </div>
                        <div class="item-img">
                            <img src="https://via.placeholder.com/300x200" alt="Sản phẩm 6" class="img-fluid">
                        </div>
                        <div class="card-body">
                            <h6 class="item-price">75.000đ</h6>
                            <h5 class="item-name">Sushi Cá Hồi Tươi</h5>
                            <p class="item-description">Sushi cá hồi tươi ngon, kèm wasabi và gừng hồng.</p>
                            <div class="item-options">
                                <button class="wishlist">
                                    <i class="far fa-heart"></i> Yêu thích
                                </button>
                                <button class="cart">
                                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/nouislider@15.6.1/dist/nouislider.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chuyển đổi giữa chế độ xem lưới và danh sách
            const gridViewBtn = document.querySelector('.grid-view-btn');
            const listViewBtn = document.querySelector('.list-view-btn');
            const productsContainer = document.querySelector('.grid-view');
            
            if (gridViewBtn && listViewBtn && productsContainer) {
                gridViewBtn.addEventListener('click', function() {
                    productsContainer.className = 'grid-view';
                    gridViewBtn.classList.add('active');
                    listViewBtn.classList.remove('active');
                });
                
                listViewBtn.addEventListener('click', function() {
                    productsContainer.className = 'list-view';
                    listViewBtn.classList.add('active');
                    gridViewBtn.classList.remove('active');
                });
            }
            
            // Khởi tạo thanh trượt giá
            const priceSlider = document.getElementById('price-slider');
            if (priceSlider) {
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
                    }
                });
                
                const minPrice = document.getElementById('min-price');
                const maxPrice = document.getElementById('max-price');
                
                priceSlider.noUiSlider.on('update', function(values, handle) {
                    if (handle === 0) {
                        minPrice.innerHTML = values[0] + 'đ';
                    } else {
                        maxPrice.innerHTML = values[1] + 'đ';
                    }
                });
            }
            
            // Xử lý nút yêu thích
            const wishlistButtons = document.querySelectorAll('.wishlist');
            wishlistButtons.forEach(button => {
                button.addEventListener('click', function() {
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
        });
            // Hiển thị sidebar trên mobile
            const sidebarToggler = document.querySelector('.shop-sidebar-toggler');
            const sidebar = document.querySelector('.sidebar-shop');
            
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
                        overlay.className = 'sidebar-overlay';
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
                        const overlay = document.querySelector('.sidebar-overlay');
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
                        const overlay = document.querySelector('.sidebar-overlay');
                        if (overlay) {
                            document.body.removeChild(overlay);
                        }
                    } else {
                        // Ẩn sidebar khi chuyển về mobile nếu không đang mở
                        if (!document.querySelector('.sidebar-overlay')) {
                            sidebar.classList.add('d-none');
                        }
                    }
                });
            }
            
            // Xử lý chuyển đổi giữa chế độ xem lưới và danh sách
            const gridViewBtn = document.querySelector('.grid-view-btn');
            const listViewBtn = document.querySelector('.list-view-btn');
            const productsContainer = document.querySelector('.grid-view');
            
            if (gridViewBtn && listViewBtn && productsContainer) {
                gridViewBtn.addEventListener('click', function() {
                    this.classList.add('active');
                    listViewBtn.classList.remove('active');
                    productsContainer.className = 'grid-view';
                });
                
                listViewBtn.addEventListener('click', function() {
                    this.classList.add('active');
                    gridViewBtn.classList.remove('active');
                    productsContainer.className = 'list-view';
                });
            }
            
            // Xử lý wishlist
            const wishlistBtns = document.querySelectorAll('.wishlist');
            
            wishlistBtns.forEach(btn => {
                btn.addEventListener('click', function() {
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
            
            // Khởi tạo slider cho khoảng giá
            if (document.getElementById('price-slider')) {
                // Kiểm tra xem noUiSlider đã được tải chưa
                if (typeof noUiSlider !== 'undefined') {
                    const slider = document.getElementById('price-slider');
                    const minPrice = document.getElementById('min-price');
                    const maxPrice = document.getElementById('max-price');
                    
                    noUiSlider.create(slider, {
                        start: [0, 500000],
                        connect: true,
                        range: {
                            'min': 0,
                            'max': 500000
                        },
                        step: 1000
                    });
                    
                    slider.noUiSlider.on('update', function(values, handle) {
                        const value = Math.round(values[handle]);
                        if (handle === 0) {
                            minPrice.textContent = value.toLocaleString('vi-VN') + 'đ';
                        } else {
                            maxPrice.textContent = value.toLocaleString('vi-VN') + 'đ';
                        }
                    });
                } else {
                    console.warn('noUiSlider chưa được tải. Vui lòng kiểm tra thư viện.');
                }
            }
        </script>
        
        <!-- Thêm script cho noUiSlider -->
        <script src="https://cdn.jsdelivr.net/npm/nouislider@15.6.1/dist/nouislider.min.js"></script>
    </body>
</html>