@extends('layouts.customer.fullLayoutMaster')

@section('title', 'Danh Sách Sản Phẩm')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{asset('css/product-list.css')}}">
    <link rel="stylesheet" href="{{ asset('css/table-style.css') }}">
@endsection

@section('scripts')
    <script src="js/scripts/modern_sidebar"></script>
@endsection

@section('content')
<div class="product-list-wrapper">
    <div class="container-fluid pl-container-fluid">
        <div class="row">
            <div class="col-lg-3">
                <!-- Modern Sidebar -->
                <div class="sidebar-card">
                    <div class="sidebar-header">
                        <h2 class="sidebar-title">
                            <i class="fas fa-sliders"></i>
                            Bộ lọc
                        </h2>
                        <button class="sidebar-toggle-btn">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>

                    <div class="sidebar-content">
                        <!-- Search -->
                        <div class="search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="search" class="search-input" placeholder="Tìm kiếm...">
                        </div>

                        <!-- Categories -->
                        <div class="filter-section">
                            <div class="collapsible-header">
                                <h3 class="filter-title">Danh mục</h3>
                                <i class="fas fa-chevron-down collapse-icon"></i>
                            </div>
                            <div class="separator"></div>
                            <div class="collapsible-content">
                                <div class="checkbox-item">
                                    <input type="checkbox" id="category-1" class="custom-checkbox">
                                    <label for="category-1" class="checkbox-label">Thực phẩm tươi sống</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="category-2" class="custom-checkbox">
                                    <label for="category-2" class="checkbox-label">Đồ uống</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="category-3" class="custom-checkbox">
                                    <label for="category-3" class="checkbox-label">Bánh kẹo</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="category-4" class="custom-checkbox">
                                    <label for="category-4" class="checkbox-label">Thực phẩm đông lạnh</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="category-5" class="custom-checkbox">
                                    <label for="category-5" class="checkbox-label">Gia vị</label>
                                </div>
                            </div>
                        </div>

                        <!-- Brands -->
                        <div class="filter-section">
                            <div class="collapsible-header">
                                <h3 class="filter-title">Thương hiệu</h3>
                                <i class="fas fa-chevron-down collapse-icon"></i>
                            </div>
                            <div class="separator"></div>
                            <div class="collapsible-content">
                                <div class="checkbox-item">
                                    <input type="checkbox" id="brand-1" class="custom-checkbox">
                                    <label for="brand-1" class="checkbox-label">DevFoods</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="brand-2" class="custom-checkbox">
                                    <label for="brand-2" class="checkbox-label">FoodMaster</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="brand-3" class="custom-checkbox">
                                    <label for="brand-3" class="checkbox-label">GourmetViet</label>
                                </div>
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div class="filter-section">
                            <div class="collapsible-header">
                                <h3 class="filter-title">Khoảng giá</h3>
                                <i class="fas fa-chevron-down collapse-icon"></i>
                            </div>
                            <div class="separator"></div>
                            <div class="collapsible-content">
                                <div class="price-slider-container">
                                    <div class="slider-container">
                                        <div class="slider-track">
                                            <div class="slider-track-highlight"></div>
                                            <div class="slider-thumb" id="min-thumb"></div>
                                            <div class="slider-thumb" id="max-thumb"></div>
                                        </div>
                                    </div>
                                    <div class="price-range-values">
                                        <span id="min-price-value">0đ</span>
                                        <span id="max-price-value">500.000đ</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ratings -->
                        <div class="filter-section">
                            <div class="collapsible-header">
                                <h3 class="filter-title">Đánh giá</h3>
                                <i class="fas fa-chevron-down collapse-icon"></i>
                            </div>
                            <div class="separator"></div>
                            <div class="collapsible-content">
                                <div class="rating-item">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <span class="rating-count">(5)</span>
                                </div>
                                <div class="rating-item">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                    <span class="rating-count">(4)</span>
                                </div>
                                <div class="rating-item">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                    <span class="rating-count">(3)</span>
                                </div>
                                <div class="rating-item">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                    <span class="rating-count">(2)</span>
                                </div>
                                <div class="rating-item">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                    <span class="rating-count">(1)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Active Filters -->
                        <div class="active-filters">
                            <h3 class="filter-title">Bộ lọc đã chọn</h3>
                            <div class="filter-badges">
                                <div class="filter-badge">
                                    DevFoods
                                    <button class="badge-remove-btn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="filter-badge">
                                    0đ - 500.000đ
                                    <button class="badge-remove-btn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Apply Button -->
                        <button class="apply-filter-btn">Áp dụng bộ lọc</button>
                    </div>
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
                                        <span>{{ $products->total() }} sản phẩm được tìm thấy</span>
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
                    @foreach ($products as $product)
                        <div class="card" data-product-id="{{ $product->id }}">
                            <span class="like"><i class='bx bx-heart'></i></span>
                            <span class="cart"><i class='bx bx-cart-alt' ></i></span>
                            <div class="card__img">
                                <img src="{{ asset($product->image ?? 'images/product-default.jpg') }}" alt="" />
                            </div>
                            <h2 class="card__title">{{ $product->name }}</h2>
                            <p class="card__price">{{ number_format($product->base_price, 0, ',', '.') }}đ</p>
                            <div class="card__action">
                                <button class="action-btn favorite-btn">
                                    <i class="fas fa-heart"></i>
                                </button>
                                <button class="action-btn cart-btn">
                                    <i class="fas fa-shopping-bag"></i>
                                </button>
                                    <a class="action-btn" href="{{ url('shop/product/product-detail/' . $product->id) }}"><i class="fas fa-info"></i></a>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Phân trang và thông tin -->
                <div class="data-table-footer">
                    <div class="data-table-pagination-info">
                        Hiển thị <span id="startRecord">{{ ($products->currentPage() - 1) * $products->perPage() + 1 }}</span>
                        đến <span
                            id="endRecord">{{ min($products->currentPage() * $products->perPage(), $products->total()) }}</span>
                        của <span id="totalRecords">{{ $products->total() }}</span> mục
                    </div>
                    <div class="data-table-pagination-controls">
                        @if (!$products->onFirstPage())
                            <a href="{{ $products->previousPageUrl() }}" class="data-table-pagination-btn" id="prevBtn">
                                <i class="fas fa-chevron-left"></i> Trước
                            </a>
                        @endif

                        @for ($i = 1; $i <= $products->lastPage(); $i++)
                            <a href="{{ $products->url($i) }}"
                                class="data-table-pagination-btn {{ $products->currentPage() == $i ? 'active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @if ($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}" class="data-table-pagination-btn" id="nextBtn">
                                Tiếp <i class="fas fa-chevron-right"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/nouislider@15.6.1/dist/nouislider.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // // Chuyển đổi giữa chế độ xem lưới và danh sách
        // const gridViewBtn = document.querySelector('.pl-grid-view-btn');
        // const listViewBtn = document.querySelector('.pl-list-view-btn');
        // const productsContainer = document.querySelector('.pl-grid-view');
        
        // if (gridViewBtn && listViewBtn && productsContainer) {
        //     // Lưu trữ trạng thái ban đầu của các thẻ product-card
        //     const productCards = document.querySelectorAll('.product-card');
        //     const originalStyles = [];
            
        //     // Lưu lại style ban đầu của mỗi thẻ
        //     productCards.forEach((card, index) => {
        //         originalStyles[index] = {
        //             display: card.style.display || 'block',
        //             gridTemplateColumns: '',
        //             image: {
        //                 height: card.querySelector('.product-image')?.style.height || '',
        //                 aspectRatio: card.querySelector('.product-image')?.style.aspectRatio || '1 / 1'
        //             },
        //             info: {
        //                 display: card.querySelector('.product-info')?.style.display || '',
        //                 flexDirection: card.querySelector('.product-info')?.style.flexDirection || '',
        //                 justifyContent: card.querySelector('.product-info')?.style.justifyContent || ''
        //             }
        //         };
        //     });
            
        //     // Xử lý khi click vào nút hiển thị dạng lưới
        //     gridViewBtn.addEventListener('click', function() {
        //         productsContainer.className = 'pl-grid-view';
        //         gridViewBtn.classList.add('active');
        //         listViewBtn.classList.remove('active');
                
        //         // Khôi phục lại style ban đầu cho các thẻ product-card
        //         productCards.forEach((card, index) => {
        //             card.style.display = originalStyles[index].display;
        //             card.style.gridTemplateColumns = '';
                    
        //             const productImage = card.querySelector('.product-image');
        //             if (productImage) {
        //                 productImage.style.height = '';
        //                 productImage.style.aspectRatio = '1 / 1';
        //             }
                    
        //             const productInfo = card.querySelector('.product-info');
        //             if (productInfo) {
        //                 productInfo.style.display = '';
        //                 productInfo.style.flexDirection = '';
        //                 productInfo.style.justifyContent = '';
        //                 productInfo.style.padding = '1rem';
        //             }
                    
        //             // Khôi phục hiển thị cho product-overlay và product-actions
        //             const productOverlay = card.querySelector('.product-overlay');
        //             if (productOverlay) {
        //                 productOverlay.style.display = 'flex';
        //             }
        //         });
        //     });
            
        //     // Xử lý khi click vào nút hiển thị dạng danh sách
        //     listViewBtn.addEventListener('click', function() {
        //         productsContainer.className = 'pl-list-view';
        //         listViewBtn.classList.add('active');
        //         gridViewBtn.classList.remove('active');
                
        //         // Điều chỉnh hiển thị cho các product-card khi ở chế độ danh sách
        //         productCards.forEach(card => {
        //             // Thay đổi cách hiển thị cho chế độ danh sách
        //             card.style.display = 'grid';
        //             card.style.gridTemplateColumns = '1fr 2fr 1fr';
        //             card.style.alignItems = 'center';
                    
        //             // Tìm và điều chỉnh các phần tử con
        //             const productImage = card.querySelector('.product-image');
        //             const productInfo = card.querySelector('.product-info');
        //             const productActions = card.querySelector('.product-price-actions');
                    
        //             if (productImage) {
        //                 productImage.style.height = '100%';
        //                 productImage.style.aspectRatio = 'auto';
        //             }
                    
        //             if (productInfo) {
        //                 productInfo.style.display = 'flex';
        //                 productInfo.style.flexDirection = 'column';
        //                 productInfo.style.justifyContent = 'center';
        //                 productInfo.style.padding = '1rem 1.5rem';
        //             }
                    
        //             // Điều chỉnh hiển thị của product-overlay trong chế độ danh sách
        //             const productOverlay = card.querySelector('.product-overlay');
        //             if (productOverlay) {
        //                 productOverlay.style.display = 'none';
        //             }
        //         });
        //     });
        // }
        
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
        
        // Xử lý nút thông tin trong product-card
        const infoButtons = document.querySelectorAll('.info-btn');
        infoButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const productCard = this.closest('.product-card');
                if (productCard) {
                    const productId = productCard.getAttribute('data-product-id');
                    // Điều hướng đến trang chi tiết sản phẩm
                    window.location.href = `/shop/product/product-detail/${productId}`;
                }
            });
        });
    });
</script>

<!-- Core JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/jollibee-main.js') }}"></script>

<!-- Page specific scripts -->
@yield('page-scripts')
@endsection