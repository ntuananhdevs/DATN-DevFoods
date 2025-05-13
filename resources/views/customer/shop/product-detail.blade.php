@extends('layouts.customer.fullLayoutMaster')

@section('title', $product->name)

@section('styles')
<link rel="stylesheet" href="{{ asset('css/product-detail.css') }}">
<style>
    .variant-selection-container {
        margin-bottom: 20px;
    }
    
    .option-group {
        margin-bottom: 15px;
    }
    
    .option-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 8px;
    }
    
    .option-items {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .option-item {
        padding: 8px 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
        background-color: #fff;
        transition: all 0.3s;
    }
    
    .option-item:hover {
        border-color: #ff6b6b;
    }
    
    .option-item.active {
        background-color: #ff6b6b;
        color: white;
        border-color: #ff6b6b;
    }
    
    .option-item.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        text-decoration: line-through;
    }
    
    .selected-variant {
        background-color: #f8f9fa;
        border-radius: 6px;
        padding: 12px;
        margin-top: 15px;
    }
    
    .selected-variant-header {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
    }
    
    .selected-variant-header i {
        color: #28a745;
        margin-right: 8px;
    }
    
    .selected-variant-header h4 {
        margin: 0;
        font-size: 16px;
    }
    
    .selected-variant-content {
        font-size: 14px;
    }
    
    .no-variants-message {
        display: flex;
        align-items: center;
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
    }
    
    .no-variants-message i {
        color: #17a2b8;
        margin-right: 10px;
        font-size: 18px;
    }
</style>
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
                    </div>
                    <button class="image-zoom-btn">
                        <i class="fas fa-search-plus"></i>
                    </button>
                </div>

                <!-- Thumbnail Gallery -->
                <div class="thumbnail-gallery">
                    <div class="swiper thumbnail-swiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide thumbnail active" data-img="{{ $product->image ?? 'https://via.placeholder.com/600x600' }}">
                                <img src="{{ $product->image ?? 'https://via.placeholder.com/150x150' }}" alt="Thumbnail 1">
                            </div>
                            @php
                            // Tạo dữ liệu biến thể cho JavaScript
                            $variantsData = [];
                            foreach ($product->variants as $variant) {
                                $attributes = [];
                                foreach ($variant->attributeValues as $attributeValue) {
                                    $attributes[$attributeValue->attribute->id] = $attributeValue->id;
                                }
                                
                                $variantsData[$variant->id] = [
                                    'id' => $variant->id,
                                    'name' => $variant->name, 
                                    'price' => $variant->price,
                                    'image' => $variant->image ?? $product->image,
                                    'stock_quantity' => $variant->stock_quantity ?? 0,
                                    'attributes' => $attributes
                                ];
                            }
                            @endphp
                            @foreach($product->variants as $variant)
                                @if($variant->image)
                                    <div class="swiper-slide thumbnail" data-img="{{ $variant->image }}">
                                        <img src="{{ $variant->image }}" alt="Thumbnail Variant">
                                    </div>
                                @endif
                            @endforeach
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
                                <i class="fas fa-star {{ $i <= ($product->reviews->avg('rating') ?? 0) ? '' : 'far' }}"></i>
                            @endfor
                        </div>
                        <span class="rating-count">({{ number_format($product->reviews->avg('rating') ?? 0, 1) }} - {{ $product->reviews->count() }} đánh giá)</span>
                    </div>
                    <div class="product-sku">
                        <span>Mã SP: </span>
                        <span class="sku-value" id="product-sku">{{ $product->sku ?? 'PRD-' . str_pad($product->id, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>

                <div class="product-price">
                    <span class="current-price" id="product-price">{{ number_format($product->base_price, 0, ',', '.') }} ₫</span>
                </div>

                <div class="stock-status {{ $product->stock ? 'in-stock' : 'out-of-stock' }}" id="stock-status">
                    {{ $product->stock ? 'Còn hàng' : 'Hết hàng' }}
                </div>

                <div class="product-description">
                    <p>{{ $product->description ?? 'Không có mô tả.' }}</p>
                </div>

                <!-- Product Variants -->
                <div class="product-variants">
                    @php
                        $productAttributes = $product->attributes()->with('values')->get();
                    @endphp
                    @if($productAttributes->count() > 0)
                        <div class="variant-selection-container">
                            @foreach($productAttributes as $attribute)
                                <div class="option-group">
                                    <h4 class="option-title">{{ $attribute->name }}</h4>
                                    <div class="option-items">
                                        @php
                                            $attributeValues = $attribute->values()
                                                ->whereHas('productVariantValues', function($query) use ($product) {
                                                    $query->whereIn('product_variant_id', $product->variants->pluck('id'));
                                                })
                                                ->get();
                                        @endphp
                                        @if($attributeValues->count() > 0)
                                            @foreach($attributeValues as $index => $value)
                                                <button class="option-item {{ $index === 0 ? 'active' : '' }}"
                                                        data-attribute-id="{{ $attribute->id }}"
                                                        data-value-id="{{ $value->id }}">
                                                    {{ $value->value }}
                                                </button>
                                            @endforeach
                                        @else
                                            <p>Không có giá trị cho thuộc tính này</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="selected-variant">
                            <div class="selected-variant-header">
                                <i class="fas fa-check-circle"></i>
                                <h4>Biến thể đã chọn</h4>
                            </div>
                            <div class="selected-variant-content">
                                <span class="selected-variant-info">Vui lòng chọn biến thể</span>
                            </div>
                        </div>
                    @else
                        <div class="no-variants-message">
                            <i class="fas fa-info-circle"></i>
                            <p>Sản phẩm này không có biến thể.</p>
                        </div>
                    @endif
                </div>

                <!-- Product Actions -->
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
                        <button class="add-to-cart-btn" {{ $product->stock ? '' : 'disabled' }}>
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

                <!-- Product Features -->
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

                <!-- Product Delivery -->
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
            <button class="tab-btn" data-tab="reviews">Đánh giá ({{ $product->reviews->count() }})</button>
        </div>

        <div class="tabs-content">
            <div class="tab-panel active" id="description">
                <div class="tab-content">
                    <h3>Thông tin chi tiết</h3>
                    <p>{{ $product->description ?? 'Không có mô tả.' }}</p>
                </div>
            </div>

            <div class="tab-panel" id="nutrition">
                <div class="tab-content">
                    <h3>Thông tin dinh dưỡng</h3>
                    <p>Không có thông tin dinh dưỡng.</p>
                </div>
            </div>

            <div class="tab-panel" id="reviews">
                <div class="tab-content">
                    <div class="reviews-summary">
                        <div class="rating-summary">
                            <div class="average-rating">
                                <span class="rating-number">{{ number_format($product->reviews->avg('rating') ?? 0, 1) }}</span>
                                <div class="stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= ($product->reviews->avg('rating') ?? 0) ? '' : 'far' }}"></i>
                                    @endfor
                                </div>
                                <span class="rating-count">{{ $product->reviews->count() }} đánh giá</span>
                            </div>

                            <div class="rating-bars">
                                @foreach([5, 4, 3, 2, 1] as $star)
                                    @php
                                        $percentage = $product->reviews->count() > 0 ? ($product->reviews->where('rating', $star)->count() / $product->reviews->count() * 100) : 0;
                                    @endphp
                                    <div class="rating-bar-item">
                                        <div class="rating-label">{{ $star }} sao</div>
                                        <div class="rating-bar">
                                            <div class="rating-fill" style="width: {{ $percentage }}%"></div>
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
                        @if($product->reviews->count() > 0)
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

@section('scripts')
<script src="{{ asset('js/script/product-detail.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dữ liệu biến thể từ controller
        const variantsData = @json($variantsData);
        
        // Các biến DOM
        const optionItems = document.querySelectorAll('.option-item');
        const variantIdInput = document.getElementById('variant-id-input');
        const productPrice = document.getElementById('product-price');
        const productSku = document.getElementById('product-sku');
        const stockStatus = document.getElementById('stock-status');
        const mainProductImage = document.getElementById('main-product-image');
        const selectedVariantInfo = document.querySelector('.selected-variant-info');
        const addToCartBtn = document.querySelector('.add-to-cart-btn');
        
        // Lưu trữ các thuộc tính đã chọn
        let selectedAttributes = {};
        
        // Khởi tạo các thuộc tính đã chọn ban đầu (chọn các option active mặc định)
        document.querySelectorAll('.option-item.active').forEach(item => {
            const attributeId = item.getAttribute('data-attribute-id');
            const valueId = item.getAttribute('data-value-id');
            selectedAttributes[attributeId] = parseInt(valueId);
        });
        
        // Cập nhật biến thể đã chọn ban đầu
        updateSelectedVariant();
        
        // Thêm sự kiện click cho các option
        optionItems.forEach(item => {
            item.addEventListener('click', function() {
                // Bỏ qua nếu option bị disabled
                if (this.classList.contains('disabled')) {
                    return;
                }
                
                // Lấy thông tin thuộc tính và giá trị
                const attributeId = this.getAttribute('data-attribute-id');
                const valueId = this.getAttribute('data-value-id');
                
                // Bỏ active tất cả các option cùng nhóm
                document.querySelectorAll(`.option-item[data-attribute-id="${attributeId}"]`).forEach(el => {
                    el.classList.remove('active');
                });
                
                // Thêm active cho option được chọn
                this.classList.add('active');
                
                // Cập nhật thuộc tính đã chọn
                selectedAttributes[attributeId] = parseInt(valueId);
                
                // Cập nhật biến thể đã chọn
                updateSelectedVariant();
                
                // Cập nhật trạng thái các option
                updateOptionAvailability();
            });
        });
        
        // Hàm tìm biến thể phù hợp với các thuộc tính đã chọn
        function findMatchingVariant(selectedAttrs) {
            console.log('Tìm biến thể với thuộc tính:', selectedAttrs);
            console.log('Dữ liệu biến thể:', variantsData);
            
            for (const variantId in variantsData) {
                const variant = variantsData[variantId];
                let isMatch = true;
                
                // Kiểm tra từng thuộc tính đã chọn
                for (const attrId in selectedAttrs) {
                    // Nếu biến thể không có thuộc tính này hoặc giá trị không khớp
                    if (!variant.attributes[attrId] || variant.attributes[attrId] !== selectedAttrs[attrId]) {
                        isMatch = false;
                        break;
                    }
                }
                
                if (isMatch) {
                    return variant;
                }
            }
            
            return null;
        }
        
        // Hàm cập nhật thông tin biến thể đã chọn
        function updateSelectedVariant() {
            const variant = findMatchingVariant(selectedAttributes);
            
            if (variant) {
                console.log('Đã tìm thấy biến thể:', variant);
                
                // Cập nhật ID biến thể
                variantIdInput.value = variant.id;
                
                // Cập nhật giá
                productPrice.textContent = new Intl.NumberFormat('vi-VN').format(variant.price) + ' ₫';
                
                // Cập nhật trạng thái tồn kho
                if (variant.stock_quantity > 0) {
                    stockStatus.textContent = 'Còn hàng';
                    stockStatus.className = 'stock-status in-stock';
                    addToCartBtn.disabled = false;
                } else {
                    stockStatus.textContent = 'Hết hàng';
                    stockStatus.className = 'stock-status out-of-stock';
                    addToCartBtn.disabled = true;
                }
                
                // Cập nhật hình ảnh nếu biến thể có hình
                if (variant.image) {
                    mainProductImage.src = variant.image;
                }
                
                // Cập nhật thông tin biến thể đã chọn
                selectedVariantInfo.textContent = variant.name;
            } else {
                console.log('Không tìm thấy biến thể phù hợp');
                
                // Xóa ID biến thể
                variantIdInput.value = '';
                
                // Cập nhật thông tin biến thể đã chọn
                selectedVariantInfo.textContent = 'Không tìm thấy biến thể phù hợp';
                
                // Vô hiệu hóa nút thêm vào giỏ hàng
                addToCartBtn.disabled = true;
            }
        }
        
        // Hàm cập nhật trạng thái khả dụng của các option
        function updateOptionAvailability() {
            // Lấy tất cả các thuộc tính
            const attributes = {};
            document.querySelectorAll('.option-group').forEach(group => {
                const attributeId = group.querySelector('.option-item').getAttribute('data-attribute-id');
                attributes[attributeId] = [];
            });
            
            // Với mỗi thuộc tính, kiểm tra xem giá trị nào khả dụng
            for (const attributeId in attributes) {
                // Tạo bản sao của selectedAttributes nhưng bỏ thuộc tính hiện tại
                const otherAttributes = {...selectedAttributes};
                delete otherAttributes[attributeId];
                
                // Lấy tất cả các option của thuộc tính này
                const options = document.querySelectorAll(`.option-item[data-attribute-id="${attributeId}"]`);
                
                // Kiểm tra từng option
                options.forEach(option => {
                    const valueId = parseInt(option.getAttribute('data-value-id'));
                    const tempAttributes = {...otherAttributes, [attributeId]: valueId};
                    
                    // Kiểm tra xem có biến thể nào phù hợp với bộ thuộc tính này không
                    const hasMatchingVariant = Object.values(variantsData).some(variant => {
                        let isMatch = true;
                        for (const attrId in tempAttributes) {
                            if (!variant.attributes[attrId] || variant.attributes[attrId] !== tempAttributes[attrId]) {
                                isMatch = false;
                                break;
                            }
                        }
                        return isMatch;
                    });
                    
                    // Cập nhật trạng thái của option
                    if (hasMatchingVariant) {
                        option.classList.remove('disabled');
                    } else {
                        option.classList.add('disabled');
                    }
                });
            }
        }
        
        // Khởi tạo trạng thái các option
        updateOptionAvailability();
    });
</script>
@endsection