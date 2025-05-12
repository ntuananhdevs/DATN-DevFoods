
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

    // Cải thiện xử lý biến thể sản phẩm
    let selectedVariantId = null;
    let selectedOptions = {};
    let basePrice = {{ $product->base_price }};
    let variantCombinations = {};
    
    // Khởi tạo dữ liệu biến thể
    @if(isset($product->variants) && count($product->variants) > 0)
        @foreach($product->variants as $variant)
            variantCombinations[{{ $variant->id }}] = {
                price: {{ $variant->price }},
                image: "{{ $variant->image ?? $product->image }}",
                attributes: {},
                name: "{{ $variant->name ?? '' }}"
            };
            
            @if(isset($variant->variantValues))
                @foreach($variant->variantValues as $value)
                    @if(isset($value->variantAttribute))
                        variantCombinations[{{ $variant->id }}].attributes["{{ $value->variantAttribute->name }}"] = "{{ $value->value }}";
                    @endif
                @endforeach
            @endif
        @endforeach
    @endif
    
    // Hiển thị tên biến thể đầy đủ
    function getFullVariantName(variantId) {
        if (variantCombinations[variantId] && variantCombinations[variantId].name) {
            return variantCombinations[variantId].name;
        }
        
        // Nếu không có tên, tạo tên từ các thuộc tính
        if (variantCombinations[variantId] && variantCombinations[variantId].attributes) {
            const attrs = variantCombinations[variantId].attributes;
            return Object.values(attrs).join(' - ');
        }
        
        return '';
    }
    
    // Tìm biến thể phù hợp với các tùy chọn đã chọn
    function findMatchingVariant(selectedOptions) {
        for (const variantId in variantCombinations) {
            const variant = variantCombinations[variantId];
            let isMatch = true;
            
            // Kiểm tra từng thuộc tính
            for (const attrName in selectedOptions) {
                if (variant.attributes[attrName] !== selectedOptions[attrName]) {
                    isMatch = false;
                    break;
                }
            }
            
            // Nếu tất cả thuộc tính khớp, trả về biến thể này
            if (isMatch && Object.keys(selectedOptions).length === Object.keys(variant.attributes).length) {
                return {
                    id: variantId,
                    price: variant.price,
                    image: variant.image,
                    name: getFullVariantName(variantId)
                };
            }
        }
        
        return null;
    }
    
    // Cập nhật hiển thị biến thể đã chọn
    function updateSelectedVariantDisplay(variant) {
        const variantNameElement = document.getElementById('selected-variant-name');
        if (variantNameElement) {
            variantNameElement.textContent = variant ? variant.name : '';
        }
    }
    
    // Xử lý khi chọn tùy chọn
    document.querySelectorAll('.option-item').forEach(item => {
        item.addEventListener('click', () => {
            // Xóa lớp active từ các nút cùng nhóm
            item.parentElement.querySelectorAll('.option-item').forEach(i => i.classList.remove('active'));
            item.classList.add('active');
            
            // Lưu thuộc tính được chọn
            const attributeName = item.closest('.option-group').querySelector('.option-title').textContent;
            selectedOptions[attributeName] = item.textContent.trim();
            
            // Tìm biến thể phù hợp
            const matchingVariant = findMatchingVariant(selectedOptions);
            
            if (matchingVariant) {
                selectedVariantId = matchingVariant.id;
                
                // Cập nhật giá hiển thị
                document.querySelector('.current-price').textContent = new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(matchingVariant.price);
                
                // Cập nhật hình ảnh
                document.getElementById('main-product-image').src = matchingVariant.image;
                
                // Cập nhật tên biến thể đã chọn
                updateSelectedVariantDisplay(matchingVariant);
                
                // Cập nhật input hidden cho form thêm vào giỏ hàng
                if (document.getElementById('variant-id-input')) {
                    document.getElementById('variant-id-input').value = matchingVariant.id;
                }
            } else {
                // Nếu không tìm thấy biến thể phù hợp, sử dụng giá cơ bản
                selectedVariantId = null;
                document.querySelector('.current-price').textContent = new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(basePrice);
                document.getElementById('main-product-image').src = '{{ $product->image ?? "https://via.placeholder.com/600x600" }}';
                
                // Cập nhật tên biến thể đã chọn
                updateSelectedVariantDisplay(null);
                
                // Xóa giá trị input hidden
                if (document.getElementById('variant-id-input')) {
                    document.getElementById('variant-id-input').value = '';
                }
            }
        });
    });

    // Tự động chọn biến thể khi trang được tải
    document.addEventListener('DOMContentLoaded', function() {
        // Chọn tùy chọn đầu tiên của mỗi nhóm thuộc tính
        const optionGroups = document.querySelectorAll('.option-group');
        if (optionGroups.length > 0) {
            optionGroups.forEach(group => {
                const firstOption = group.querySelector('.option-item');
                if (firstOption) {
                    // Kích hoạt sự kiện click cho tùy chọn đầu tiên
                    firstOption.click();
                }
            });
        }
    });
    
    // Thêm vào giỏ hàng
    document.querySelector('.add-to-cart-btn').addEventListener('click', function(e) {
        e.preventDefault();
        
        const quantity = parseInt(document.querySelector('.quantity-input').value);
        const productId = {{ $product->id }};
        
        // Gửi yêu cầu AJAX để thêm vào giỏ hàng
        $.ajax({
            url: '{{ route("cart.add") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                variant_id: selectedVariantId,
                quantity: quantity
            },
            success: function(response) {
                // Hiển thị thông báo thành công
                alert('Đã thêm sản phẩm vào giỏ hàng!');
                
                // Cập nhật số lượng sản phẩm trong giỏ hàng (nếu có)
                if (document.querySelector('.cart-count')) {
                    document.querySelector('.cart-count').textContent = response.cartCount;
                }
            },
            error: function(xhr) {
                // Hiển thị thông báo lỗi
                alert('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng.');
            }
        });
    });
</script>
@endsection

@section('content')
<div class="container-detail">
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
                    <span class="current-price">{{ number_format($product->base_price, 0, ',', '.') }} ₫</span>
                    @if($product->original_price)
                        <span class="old-price">{{ number_format($product->original_price, 0, ',', '.') }} ₫</span>
                        <span class="discount-badge">-{{ round(($product->original_price - $product->base_price) / $product->original_price * 100) }}%</span>
                    @endif
                </div>

                <div class="product-description">
                    <p>{{ $product->description ?? 'Không có mô tả.' }}</p>
                </div>

                <!-- Product Options -->
                <div class="product-options">
                    @php
                        // Lấy tất cả thuộc tính biến thể của sản phẩm
                        $variantAttributes = [];
                        
                        // Kiểm tra xem sản phẩm có biến thể không
                        if(isset($product->variants) && count($product->variants) > 0) {
                            // Nhóm các thuộc tính biến thể theo tên thuộc tính
                            foreach($product->variants as $variant) {
                                if(isset($variant->variantValues)) {
                                    foreach($variant->variantValues as $value) {
                                        if(isset($value->variantAttribute)) {
                                            $attributeName = $value->variantAttribute->name;
                                            if(!isset($variantAttributes[$attributeName])) {
                                                $variantAttributes[$attributeName] = [];
                                            }
                                            
                                            // Thêm giá trị vào mảng nếu chưa tồn tại
                                            $valueData = [
                                                'value' => $value->value,
                                                'variant_id' => $variant->id,
                                                'price' => $variant->price,
                                                'image' => $variant->image ?? $product->image
                                            ];
                                            
                                            // Kiểm tra xem giá trị đã tồn tại chưa
                                            $exists = false;
                                            foreach($variantAttributes[$attributeName] as $existingValue) {
                                                if($existingValue['value'] === $value->value) {
                                                    $exists = true;
                                                    break;
                                                }
                                            }
                                            
                                            if(!$exists) {
                                                $variantAttributes[$attributeName][] = $valueData;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    @endphp

                    <!-- Hiển thị biến thể đã chọn -->
                    <div class="selected-variant">
                        <h3>Biến thể đã chọn: <span id="selected-variant-name">{{ isset($product->variants) && count($product->variants) > 0 ? '' : 'Không có biến thể' }}</span></h3>
                    </div>

                    @if(count($variantAttributes) > 0)
                        @foreach($variantAttributes as $attributeName => $values)
                            <div class="option-group">
                                <h3 class="option-title">{{ $attributeName }}</h3>
                                <div class="option-items">
                                    @foreach($values as $index => $valueData)
                                        <button class="option-item {{ $index === 0 ? 'active' : '' }}"
                                                data-variant-id="{{ $valueData['variant_id'] }}"
                                                data-price="{{ $valueData['price'] }}"
                                                data-image="{{ $valueData['image'] }}">
                                            {{ $valueData['value'] }}
                                        </button>









                                        
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="option-group">
                            <h3 class="option-title">Kích cỡ</h3>
                            <div class="option-items">
                                <button class="option-item active" data-price="{{ $product->base_price }}">Mặc định</button>
                            </div>
                        </div>
                    @endif
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
                        <input type="hidden" id="variant-id-input" name="variant_id" value="">
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