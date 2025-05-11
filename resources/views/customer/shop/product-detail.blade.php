
@extends('layouts.customer.fullLayoutMaster')

@section('title', $product->name)

@section('styles')
<link rel="stylesheet" href="{{ asset('css/product-detail.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('fonts/feather/style.min.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@6.8.4/swiper-bundle.min.css">
@endsection

@section('scripts')
<script src="{{ asset('js/script/product-detail.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@6.8.4/swiper-bundle.min.js"></script>
<script>
    // Initialize Swiper for thumbnail gallery
    var thumbnailSwiper = new Swiper('.thumbnail-swiper', {
        slidesPerView: 4,
        spaceBetween: 10,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });

    // Thumbnail click to change main image
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.addEventListener('click', () => {
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
            document.getElementById('main-product-image').src = thumb.dataset.img;
        });
    });

    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));

            tab.classList.add('active');
            document.getElementById(tab.dataset.tab).classList.add('active');
        });
    });

    // Quantity selector
    document.querySelector('.minus-btn').addEventListener('click', () => {
        let input = document.querySelector('.quantity-input');
        let value = parseInt(input.value);
        if (value > 1) input.value = value - 1;
    });

    document.querySelector('.plus-btn').addEventListener('click', () => {
        let input = document.querySelector('.quantity-input');
        let value = parseInt(input.value);
        if (value < 99) input.value = value + 1;
    });

    // Option selection
    document.querySelectorAll('.option-item').forEach(item => {
        item.addEventListener('click', () => {
            item.parentElement.querySelectorAll('.option-item').forEach(i => i.classList.remove('active'));
            item.classList.add('active');
        });
    });
</script>
<script src="js/script/product-detail.js"></script>
@endsection

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="/">Trang chủ</a>
        <span class="separator">/</span>
        <a href="/shop">Cửa hàng</a>
        <span class="separator">/</span>
        <span class="current">{{ $product->category->name }}</span>
        <span class="separator">/</span>
        <span class="current">{{ $product->name }}</span>
    </div>

    <!-- Product Detail Main Section -->
    <div class="product-detail-card">
        <div class="product-detail-grid">
            <!-- Product Images Section -->
            <div class="product-images">
                <div class="main-image">
                    <img src="{{ $product->image ?? 'https://via.placeholder.com/600x600' }}" alt="{{ $product->name }}" id="main-product-image">
                    <div class="image-badges">
                        @if($product->created_at->diffInDays(now()) < 7)
                            <span class="badge new-badge">Mới</span>
                        @endif
                        @if($product->discount_amount > 0)
                            <span class="badge sale-badge">-{{ $product->discount_amount }}%</span>
                        @endif
                    </div>
                    <button class="image-zoom-btn">
                        <i class="fas fa-search-plus"></i>
                    </button>
                </div>

                <!-- Thumbnail Gallery -->
                <div class="thumbnail-gallery">
                    <div class="swiper thumbnail-swiper">
                        <div class="swiper-wrapper">
                            @if($product->images && count($product->images) > 0)
                                @foreach($product->images as $index => $image)
                                    <div class="swiper-slide thumbnail {{ $index === 0 ? 'active' : '' }}" data-img="{{ $image->url ?? 'https://via.placeholder.com/600x600' }}">
                                        <img src="{{ $image->thumbnail_url ?? 'https://via.placeholder.com/150x150' }}" alt="Thumbnail {{ $index + 1 }}">
                                    </div>
                                @endforeach
                            @else
                                <div class="swiper-slide thumbnail active" data-img="{{ $product->image ?? 'https://via.placeholder.com/600x600' }}">
                                    <img src="{{ $product->image ?? 'https://via.placeholder.com/150x150' }}" alt="Thumbnail 1">
                                </div>
                            @endif
                        </div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                </div>
            </div>

            <!-- Product Info Section -->
            <div class="product-info">
                <h1 class="product-title">{{ $product->name }}</h1>

                <div class="product-meta">
                    <div class="product-ratings">
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $product->average_rating ? '' : 'far' }}"></i>
                            @endfor
                        </div>
                        <span class="rating-count">({{ number_format($product->average_rating, 1) }} - {{ $product->reviews_count }} đánh giá)</span>
                    </div>
                    <div class="product-sku">
                        <span>Mã SP: </span>
                        <span class="sku-value">{{ $product->sku ?? 'GA-RAN-' . str_pad($product->id, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>

                <div class="product-price">
                    <span class="current-price">{{ number_format($product->base_price, 0, ',', '.') }}đ</span>
                    @if($product->original_price)
                        <span class="old-price">{{ number_format($product->original_price, 0, ',', '.') }}đ</span>
                        <span class="discount-badge">-{{ round(($product->original_price - $product->base_price) / $product->original_price * 100) }}%</span>
                    @endif
                </div>

                <div class="product-description">
                    <p>{{ $product->description ?? 'Không có mô tả.' }}</p>
                </div>

                <div class="product-options">
                    <div class="option-group">
                        <h3 class="option-title">Kích cỡ</h3>
                        <div class="option-items">
                            @if($product->variants && count($product->variants) > 0)
                                @foreach($product->variants as $variant)
                                    <button class="option-item {{ $loop->first ? 'active' : '' }}" data-price="{{ $variant->price }}">{{ $variant->name }}</button>
                                @endforeach
                            @else
                                <button class="option-item active">Mặc định</button>
                            @endif
                        </div>
                    </div>

                    <div class="option-group">
                        <h3 class="option-title">Độ cay</h3>
                        <div class="option-items">
                            @foreach(['Nhẹ', 'Vừa', 'Cay', 'Siêu cay'] as $spice)
                                <button class="option-item {{ $spice == 'Vừa' ? 'active' : '' }}">{{ $spice }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="product-actions">
                    <div class="quantity-selector">
                        <button class="quantity-btn minus-btn">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" class="quantity-input" value="1" min="1" max="99">
                        <button class="quantity-btn plus-btn">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>

                    <div class="action-buttons">
                        <button class="add-to-cart-btn">
                            <i class="fas fa-shopping-cart"></i>
                            Thêm vào giỏ hàng
                        </button>
                        <button class="wishlist-btn">
                            <i class="far fa-heart"></i>
                        </button>
                        <button class="share-btn">
                            <i class="fas fa-share-alt"></i>
                        </button>
                    </div>
                </div>

                <div class="product-features">
                    <div class="feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Thịt gà tươi ngon</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Sốt cay đặc biệt</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Không chất bảo quản</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Chế biến khi đặt hàng</span>
                    </div>
                </div>

                <div class="product-delivery">
                    <div class="delivery-option">
                        <i class="fas fa-truck"></i>
                        <div class="delivery-info">
                            <h4>Giao hàng miễn phí</h4>
                            <p>Cho đơn hàng từ 200.000đ</p>
                        </div>
                    </div>
                    <div class="delivery-option">
                        <i class="fas fa-undo"></i>
                        <div class="delivery-info">
                            <h4>Đổi trả dễ dàng</h4>
                            <p>Trong vòng 24 giờ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Tabs -->
    <div class="product-tabs">
        <div class="tabs-header">
            <button class="tab-btn active" data-tab="description">Mô tả</button>
            <button class="tab-btn" data-tab="nutrition">Dinh dưỡng</button>
            <button class="tab-btn" data-tab="reviews">Đánh giá ({{ $product->reviews_count }})</button>
        </div>

        <div class="tabs-content">
            <div class="tab-panel active" id="description">
                <div class="tab-content">
                    <h3>Thông tin chi tiết</h3>
                    <p>{{ $product->description ?? 'Không có mô tả.' }}</p>
                    @if($product->preparation_steps)
                        <p>Quy trình chế biến gồm nhiều bước:</p>
                        <ul>
                            @foreach($product->preparation_steps as $step)
                                <li>{{ $step }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="tab-panel" id="nutrition">
                <div class="tab-content">
                    <h3>Thông tin dinh dưỡng</h3>
                    <div class="nutrition-table">
                        @if($product->nutrition && count($product->nutrition) > 0)
                            @foreach($product->nutrition as $nutrient)
                                <div class="nutrition-row">
                                    <div class="nutrition-label">{{ $nutrient->name }}</div>
                                    <div class="nutrition-value">{{ $nutrient->value }}</div>
                                </div>
                            @endforeach
                        @else
                            <p>Không có thông tin dinh dưỡng.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="tab-panel" id="reviews">
                <div class="tab-content">
                    <div class="reviews-summary">
                        <div class="rating-summary">
                            <div class="average-rating">
                                <span class="rating-number">{{ number_format($product->average_rating, 1) }}</span>
                                <div class="stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $product->average_rating ? '' : 'far' }}"></i>
                                    @endfor
                                </div>
                                <span class="rating-count">{{ $product->reviews_count }} đánh giá</span>
                            </div>

                            <div class="rating-bars">
                                @foreach([5, 4, 3, 2, 1] as $star)
                                    @php
                                        $percentage = $product->reviews_count > 0 ? ($product->reviews()->where('rating', $star)->count() / $product->reviews_count * 100) : 0;
                                    @endphp
                                    <div class="rating-bar-item">
                                        <div class="rating-label">{{ $star }} sao</div>
                                        <div class="rating-bar">
                                            <div class="rating-fill"></div>
                                        </div>
                                        <div class="rating-percent">{{ round($percentage) }}%</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="review-action">
                            <button class="write-review-btn">Viết đánh giá</button>
                        </div>
                    </div>

                    <div class="reviews-list">
                        @if($product->reviews && count($product->reviews) > 0)
                            @foreach($product->reviews as $review)
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="reviewer-info">
                                            <div class="reviewer-avatar">
                                                <img src="{{ $review->user->avatar ?? 'https://via.placeholder.com/50' }}" alt="Avatar">
                                            </div>
                                            <div class="reviewer-details">
                                                <h4 class="reviewer-name">{{ $review->is_anonymous ? 'Ẩn danh' : $review->user->full_name }}</h4>
                                                <div class="review-date">{{ $review->review_date->format('d/m/Y') }}</div>
                                            </div>
                                        </div>
                                        <div class="review-rating">
                                            <div class="stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $review->rating ? '' : 'far' }}"></i>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                    <div class="review-content">
                                        <p>{{ $review->review }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p>Chưa có đánh giá nào cho sản phẩm này.</p>
                        @endif
                    </div>

                    @if($product->reviews && count($product->reviews) > 0)
                        <div class="reviews-pagination">
                            <!-- Pagination logic can be added here -->
                            <button class="pagination-btn active">1</button>
                            <button class="pagination-btn">2</button>
                            <button class="pagination-btn">3</button>
                            <button class="pagination-btn">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <div class="related-products">
        <h2 class="section-title">Sản phẩm liên quan</h2>

        <div class="related-products-grid">
            @foreach($relatedProducts as $related)
                <div class="card">
                    <span class="like"><i class='bx bx-heart'></i></span>
                    <span class="cart"><i class='bx bx-cart-alt'></i></span>
                    <div class="card__img">
                        <img src="{{ $related->image ?? 'https://via.placeholder.com/300' }}" alt="{{ $related->name }}" />
                    </div>
                    <h2 class="card__title">{{ $related->name }}</h2>
                    <p class="card__price">{{ number_format($related->base_price, 0, ',', '.') }}đ</p>
                    <div class="card__action">
                        <button class="action-btn favorite-btn">
                            <i class="fas fa-heart"></i>
                        </button>
                        <button class="action-btn cart-btn">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                        <a class="action-btn" href="{{ url('shop/product/product-detail/' . $related->id) }}"><i class="fas fa-info"></i></a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Zoom Modal -->
<div class="zoom-modal" id="zoom-modal">
    <div class="zoom-modal-content">
        <span class="zoom-close">&times;</span>
        <img src="/placeholder.svg" alt="Zoomed Image" id="zoomed-image">
    </div>
</div>
@endsection