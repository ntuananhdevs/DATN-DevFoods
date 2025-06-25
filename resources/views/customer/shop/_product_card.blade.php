<div class="product-card bg-white rounded-lg overflow-hidden" 
    data-product-id="{{ $product->id }}"
    data-variants="{{ json_encode($product->variants->map(function($variant) {
        return [
            'id' => $variant->id,
            'stock' => $variant->stock_quantity,
            'branch_id' => $variant->branch_id
        ];
    })) }}"
    data-has-stock="{{ $product->has_stock ? 'true' : 'false' }}">
    <div class="relative">
        <a href="{{ route('products.show', $product->id) }}" class="block">
            @if($product->primary_image)
                <img src="{{ $product->primary_image->s3_url }}" 
                     alt="{{ $product->name }}" 
                     class="product-image">
            @else
                <div class="no-image-placeholder">
                    <i class="far fa-image"></i>
                </div>
            @endif
        </a>

        @auth
        <button class="favorite-btn" data-product-id="{{ $product->id }}">
            @if($product->is_favorite)
                <i class="fas fa-heart text-red-500"></i>
            @else
                <i class="far fa-heart"></i>
            @endif
        </button>
        @else
        <button class="favorite-btn login-prompt-btn">
            <i class="far fa-heart"></i>
        </button>
        @endauth

        @if($product->discount_price && $product->base_price > $product->discount_price)
            @php
                $discountPercent = round((($product->base_price - $product->discount_price) / $product->base_price) * 100);
            @endphp
            <span class="custom-badge badge-sale">-{{ $discountPercent }}%</span>
        @elseif($product->created_at->diffInDays(now()) <= 7)
            <span class="custom-badge badge-new">Mới</span>
        @endif
    </div>

    <div class="p-4">
        <div class="flex items-center mb-2">
            <div class="rating-stars flex">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= floor($product->average_rating))
                        <i class="fas fa-star"></i>
                    @elseif($i - 0.5 <= $product->average_rating)
                        <i class="fas fa-star-half-alt"></i>
                    @else
                        <i class="far fa-star"></i>
                    @endif
                @endfor
            </div>
            <span class="rating-count ml-2">({{ $product->reviews_count }})</span>
        </div>

        <a href="{{ route('products.show', $product->id) }}" class="block">
            <h3 class="product-title">{{ $product->name }}</h3>
        </a>

        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
            {{ $product->short_description ?? Str::limit($product->description, 80) }}
        </p>

        <div class="flex justify-between items-center">
            <div class="flex flex-col">
                @if($product->discount_price && $product->base_price > $product->discount_price)
                    <span class="product-price">{{ number_format($product->discount_price) }}đ</span>
                    <span class="product-original-price">{{ number_format($product->base_price) }}đ</span>
                @else
                    @if($product->min_price != $product->max_price)
                        <span class="product-price">{{ number_format($product->min_price) }}đ</span>
                    @endif
                @endif
            </div>
            @if(isset($product->has_stock) && $product->has_stock)
                <a href="{{ route('products.show', ['id' => $product->id]) }}" class="add-to-cart-btn">
                    <i class="fas fa-shopping-cart"></i>
                    Mua hàng
                </a>
            @else
                <button class="add-to-cart-btn disabled" disabled>
                    <i class="fas fa-ban"></i>
                    Hết hàng
                </button>
            @endif
        </div>
        
        @if(isset($product->applicable_discount_codes) && $product->applicable_discount_codes->count() > 0)
            <div class="discount-tag">
                @foreach($product->applicable_discount_codes as $discountCode)
                    @php
                        $badgeClass = 'discount-badge';
                        $icon = 'fa-percent';
                        $minText = '';
                        if($discountCode->discount_type === 'fixed_amount') {
                            $badgeClass .= ' fixed-amount';
                            $icon = 'fa-money-bill-wave';
                        } elseif($discountCode->discount_type === 'free_shipping') {
                            $badgeClass .= ' free-shipping';
                            $icon = 'fa-shipping-fast';
                        } else {
                            $badgeClass .= ' percentage';
                        }
                        if(isset($discountCode->min_requirement_type) && $discountCode->min_requirement_value > 0) {
                            if($discountCode->min_requirement_type === 'order_amount') {
                                $minText = 'Đơn từ '.number_format($discountCode->min_requirement_value/1000,0).'K';
                            } elseif($discountCode->min_requirement_type === 'product_price') {
                                $minText = 'Sản phẩm từ '.number_format($discountCode->min_requirement_value/1000,0).'K';
                            }
                        }
                    @endphp
                    <div class="{{ $badgeClass }}" title="{{ $discountCode->name }}" data-discount-code="{{ $discountCode->code }}">
                        <i class="fas {{ $icon }}"></i>
                        @if($discountCode->discount_type === 'percentage')
                            Giảm {{ $discountCode->discount_value }}%
                        @elseif($discountCode->discount_type === 'fixed_amount')
                            Giảm {{ number_format($discountCode->discount_value) }}đ
                        @else
                            Miễn phí vận chuyển
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div> 