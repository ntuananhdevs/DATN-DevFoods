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
        margin-top: 50px;
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
    
    /* CSS cho toast notification */
    .toast-container {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 9999;
    }
    
    .toast {
        display: flex;
        align-items: center;
        background-color: #fff;
        border-left: 4px solid #4CAF50;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-radius: 4px;
        padding: 16px;
        margin-bottom: 16px;
        min-width: 300px;
        max-width: 400px;
        animation: slideIn 0.3s ease-out forwards;
        opacity: 0;
        transform: translateX(100%);
    }
    
    @keyframes slideIn {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideOut {
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }
    
    .toast.hide {
        animation: slideOut 0.3s ease-out forwards;
    }
    
    .toast-icon {
        margin-right: 12px;
        color: #4CAF50;
        font-size: 20px;
    }
    
    .toast-content {
        flex: 1;
    }
    
    .toast-close {
        color: #aaa;
        font-size: 16px;
        cursor: pointer;
        padding: 0 4px;
    }
    
    .toast-close:hover {
        color: #555;
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
                                    <h4 class="option-title" data-attribute-id="{{ $attribute->id }}">{{ $attribute->name }}</h4>
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
                        <input type="number" class="quantity-input" value="1" min="1" max="99" id="product-quantity">
                        <button class="quantity-btn plus-btn">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <button class="add-to-cart-btn" id="add-to-cart-btn">
                        <i class="fas fa-shopping-cart"></i>
                        Thêm vào giỏ hàng
                    </button>
                </div>

                <div class="action-buttons">
                    <input type="hidden" id="variant-id-input" name="variant_id" value="">
                    <button class="wishlist-btn">
                        <i class="far fa-heart"></i>
                    </button>
                    <button class="share-btn">
                        <i class="fas fa-share-alt"></i>
                    </button>
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

<!-- Toast Container -->
<div class="toast-container" id="toast-container"></div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@6.8.4/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo Swiper cho gallery
    var thumbnailSwiper = new Swiper('.thumbnail-swiper', {
        slidesPerView: 4,
        spaceBetween: 10,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });
    
    // Xử lý chọn thumbnail
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.getElementById('main-product-image');
    
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            // Xóa class active từ tất cả thumbnails
            thumbnails.forEach(t => t.classList.remove('active'));
            // Thêm class active vào thumbnail được chọn
            this.classList.add('active');
            // Cập nhật ảnh chính
            mainImage.src = this.getAttribute('data-img');
        });
    });

    // Hàm hiển thị toast
    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toast-container');
        
        // Tạo toast element
        const toast = document.createElement('div');
        toast.className = 'toast';
        
        // Icon dựa vào loại toast
        let icon = 'fa-check-circle';
        let color = '#4CAF50';
        
        if (type === 'error') {
            icon = 'fa-exclamation-circle';
            color = '#F44336';
            toast.style.borderLeftColor = color;
        } else if (type === 'warning') {
            icon = 'fa-exclamation-triangle';
            color = '#FF9800';
            toast.style.borderLeftColor = color;
        } else if (type === 'info') {
            icon = 'fa-info-circle';
            color = '#2196F3';
            toast.style.borderLeftColor = color;
        }
        
        // Nội dung toast
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="fas ${icon}" style="color: ${color}"></i>
            </div>
            <div class="toast-content">${message}</div>
            <div class="toast-close">&times;</div>
        `;
        
        // Thêm toast vào container
        toastContainer.appendChild(toast);
        
        // Xử lý nút đóng
        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', function() {
            toast.classList.add('hide');
            setTimeout(() => {
                toast.remove();
            }, 300);
        });
        
        // Tự động đóng sau 5 giây
        setTimeout(() => {
            toast.classList.add('hide');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 5000);
    }

    
    
    // Xử lý nút tăng/giảm số lượng
    const minusBtn = document.querySelector('.minus-btn');
    const plusBtn = document.querySelector('.plus-btn');
    const quantityInput = document.querySelector('.quantity-input');
    
    minusBtn.addEventListener('click', function() {
        let value = parseInt(quantityInput.value);
        if (value > 1) {
            quantityInput.value = value - 1;
        }
    });
    
    plusBtn.addEventListener('click', function() {
        let value = parseInt(quantityInput.value);
        if (value < 99) {
            quantityInput.value = value + 1;
        }
    });
    
    // Xử lý chọn biến thể
    const optionItems = document.querySelectorAll('.option-item');
    let selectedAttributes = {};
    let selectedVariant = null;
    
    // Lấy dữ liệu biến thể từ PHP
    const variantsData = @json($variantsData);
    
    // Khởi tạo giá trị mặc định cho selectedAttributes
    optionItems.forEach(item => {
        if (item.classList.contains('active')) {
            const attributeId = item.getAttribute('data-attribute-id');
            const valueId = item.getAttribute('data-value-id');
            selectedAttributes[attributeId] = valueId;
        }
    });
    
    // Cập nhật hiển thị biến thể đã chọn ban đầu
    updateSelectedVariantDisplay();
    
    // Xử lý khi click vào option item
    optionItems.forEach(item => {
        item.addEventListener('click', function() {
            const attributeId = this.getAttribute('data-attribute-id');
            const valueId = this.getAttribute('data-value-id');
            
            // Xóa class active từ tất cả option items cùng attribute
            document.querySelectorAll(`.option-item[data-attribute-id="${attributeId}"]`).forEach(el => {
                el.classList.remove('active');
            });
            
            // Thêm class active vào option item được chọn
            this.classList.add('active');
            
            // Cập nhật selectedAttributes
            selectedAttributes[attributeId] = valueId;
            
            // Tìm biến thể phù hợp
            findMatchingVariant();
            
            // Cập nhật hiển thị biến thể đã chọn
            updateSelectedVariantDisplay();
        });
    });
    
    // Hàm tìm biến thể phù hợp
    function findMatchingVariant() {
        selectedVariant = null;
        
        // Kiểm tra từng biến thể
        for (const variantId in variantsData) {
            const variant = variantsData[variantId];
            let isMatch = true;
            
            // Kiểm tra từng thuộc tính
            for (const attributeId in selectedAttributes) {
                if (variant.attributes[attributeId] != selectedAttributes[attributeId]) {
                    isMatch = false;
                    break;
                }
            }
            
            if (isMatch) {
                selectedVariant = variant;
                break;
            }
        }
        
        // Cập nhật thông tin sản phẩm nếu tìm thấy biến thể phù hợp
        if (selectedVariant) {
            // Cập nhật giá
            document.getElementById('product-price').textContent = new Intl.NumberFormat('vi-VN').format(selectedVariant.price) + ' ₫';
            
            // Cập nhật trạng thái tồn kho
            const stockStatus = document.getElementById('stock-status');
            if (selectedVariant.stock_quantity > 0) {
                stockStatus.textContent = 'Còn hàng';
                stockStatus.className = 'stock-status in-stock';
                document.getElementById('add-to-cart-btn').disabled = false;
            } else {
                stockStatus.textContent = 'Hết hàng';
                stockStatus.className = 'stock-status out-of-stock';
                document.getElementById('add-to-cart-btn').disabled = true;
            }
            
            // Cập nhật ảnh nếu có
            if (selectedVariant.image) {
                mainImage.src = selectedVariant.image;
                
                // Tìm và active thumbnail tương ứng
                thumbnails.forEach(thumbnail => {
                    if (thumbnail.getAttribute('data-img') === selectedVariant.image) {
                        thumbnails.forEach(t => t.classList.remove('active'));
                        thumbnail.classList.add('active');
                    }
                });
            }
            
            // Cập nhật variant_id cho form
            document.getElementById('variant-id-input').value = selectedVariant.id;
        }
    }
    
    // Hàm cập nhật hiển thị biến thể đã chọn
    function updateSelectedVariantDisplay() {
        const selectedVariantInfo = document.querySelector('.selected-variant-info');
        
        if (selectedVariant) {
            // Tạo danh sách các thuộc tính đã chọn
            let attributesList = [];
            
            // Lặp qua các thuộc tính đã chọn
            for (const attributeId in selectedAttributes) {
                const valueId = selectedAttributes[attributeId];
                
                // Tìm tên thuộc tính
                const attributeElement = document.querySelector(`.option-title[data-attribute-id="${attributeId}"]`);
                const attributeName = attributeElement ? attributeElement.textContent : '';
                
                // Tìm giá trị thuộc tính
                const valueElement = document.querySelector(`.option-item[data-attribute-id="${attributeId}"][data-value-id="${valueId}"]`);
                const valueName = valueElement ? valueElement.textContent.trim() : '';
                
                if (attributeName && valueName) {
                    attributesList.push(`<strong>${attributeName}:</strong> ${valueName}`);
                }
            }
            
            // Hiển thị thông tin biến thể
            selectedVariantInfo.innerHTML = `
                ${attributesList.join(' | ')}
                <br>
                <strong>Giá:</strong> ${new Intl.NumberFormat('vi-VN').format(selectedVariant.price)} ₫
                <strong>Tồn kho:</strong> ${selectedVariant.stock_quantity}
            `;
        } else {
            selectedVariantInfo.textContent = 'Không tìm thấy biến thể phù hợp với lựa chọn của bạn';
        }
    }
    
    // Xử lý nút thêm vào giỏ hàng
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    
    addToCartBtn.addEventListener('click', function() {
        // Kiểm tra xem đã chọn biến thể chưa
        if (!selectedVariant && document.querySelectorAll('.option-group').length > 0) {
            dtmodalShowToast('warning', {
                title: 'Chưa chọn biến thể',
                message: 'Vui lòng chọn biến thể sản phẩm trước khi thêm vào giỏ hàng'
            });
            return;
        }
        
        // Lấy số lượng
        const quantity = parseInt(document.getElementById('product-quantity').value);
        
        // Lấy variant_id (nếu có)
        const variantId = selectedVariant ? selectedVariant.id : null;
        
        // Gửi request thêm vào giỏ hàng
        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: {{ $product->id }},
                variant_id: variantId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hiển thị toast thành công
                dtmodalShowToast('Đã thêm ' + quantity + ' ' + (selectedVariant ? selectedVariant.name || '{{ $product->name }}' : '{{ $product->name }}') + ' vào giỏ hàng', 'success');
                
                // Cập nhật số lượng sản phẩm trong giỏ hàng (nếu có)
                if (data.cartCount) {
                    const cartCountElements = document.querySelectorAll('.cart-count');
                    if (cartCountElements && cartCountElements.length > 0) {
                        cartCountElements.forEach(element => {
                            element.textContent = data.cartCount;
                        });
                    }
                }
            } else {
                // Hiển thị thông báo lỗi
                dtmodalShowToast(data.message || 'Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            dtmodalShowToast('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng', 'error');
        });
    });
    
    // Xử lý tabs
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanels = document.querySelectorAll('.tab-panel');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Xóa class active từ tất cả tab buttons và panels
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanels.forEach(p => p.classList.remove('active'));
            
            // Thêm class active vào tab button được chọn
            this.classList.add('active');
            
            // Hiển thị tab panel tương ứng
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Xử lý zoom ảnh
    const zoomBtn = document.querySelector('.image-zoom-btn');
    const zoomModal = document.getElementById('zoom-modal');
    const zoomedImage = document.getElementById('zoomed-image');
    const zoomClose = document.querySelector('.zoom-close');
    
    zoomBtn.addEventListener('click', function() {
        zoomedImage.src = mainImage.src;
        zoomModal.style.display = 'flex';
    });
    
    zoomClose.addEventListener('click', function() {
        zoomModal.style.display = 'none';
    });
    
    // Đóng modal khi click bên ngoài
    window.addEventListener('click', function(event) {
        if (event.target === zoomModal) {
            zoomModal.style.display = 'none';
        }
    });
    
    // Tìm biến thể phù hợp ban đầu
    findMatchingVariant();
});
</script>
@endsection
