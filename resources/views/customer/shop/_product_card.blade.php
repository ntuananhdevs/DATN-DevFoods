@php
    // Lấy combos (nếu có)
    $combos = $product->relationLoaded('combos') ? $product->combos : collect();
@endphp
@if($combos && $combos->count() > 0)
    <div class="flex flex-wrap gap-1 px-4 pt-2 pb-1">
        @foreach($combos->take(2) as $combo)
            <a href="{{ route('combos.show', $combo->slug) }}" class="inline-flex items-center text-xs bg-orange-100 text-orange-600 px-2 py-1 rounded font-semibold hover:bg-orange-200 transition" title="{{ $combo->name }}">
                <i class="fas fa-layer-group mr-1"></i> {{ \Illuminate\Support\Str::limit($combo->name, 18) }}
            </a>
        @endforeach
        @if($combos->count() > 2)
            <span class="inline-flex items-center text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded">+{{ $combos->count() - 2 }} combo khác</span>
        @endif
    </div>
@endif
<div class="product-card bg-white rounded-lg overflow-hidden {{ !$product->has_stock ? 'out-of-stock' : '' }}"
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
        @if(!$product->has_stock)
            <div class="out-of-stock-overlay">
                <span>Hết hàng</span>
            </div>
            {{-- <span class="custom-badge badge-sale" style="background:#dc2626;top:36px;">Hết hàng</span> --}}
        @endif
        <a href="{{ route('products.show', $product->slug) }}" class="block">
            @if($product->primary_image)
                <img src="{{ Storage::disk('s3')->url($product->primary_image->img) }}"
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
    <div class="px-4 py-2">
        @php
            $freeship = null;
            $otherDiscounts = [];
            if(isset($product->applicable_discount_codes)) {
                foreach($product->applicable_discount_codes as $discountCode) {
                    if($discountCode->discount_type === 'free_shipping') {
                        $freeship = $discountCode;
                    } else {
                        $otherDiscounts[] = $discountCode;
                    }
                }
            }
            $maxDiscount = null;
            $maxValue = 0;
            foreach($otherDiscounts as $discountCode) {
                if($discountCode->discount_type === 'fixed_amount') {
                    $value = $discountCode->discount_value;
                } elseif($discountCode->discount_type === 'percentage') {
                    $value = isset($product->min_price) ? ($product->min_price * $discountCode->discount_value / 100) : 0;
                } else {
                    $value = 0;
                }
                if($value > $maxValue) {
                    $maxValue = $value;
                    $maxDiscount = $discountCode;
                }
            }
            $originPrice = $product->discount_price && $product->base_price > $product->discount_price
                ? $product->discount_price
                : $product->min_price;
            $finalPrice = $originPrice;
            if($maxDiscount) {
                if($maxDiscount->discount_type === 'fixed_amount') {
                    $finalPrice = max(0, $originPrice - $maxDiscount->discount_value);
                } elseif($maxDiscount->discount_type === 'percentage') {
                    $finalPrice = max(0, $originPrice * (1 - $maxDiscount->discount_value / 100));
                }
            }
        @endphp
        <div class="flex items-center justify-between">
            <a href="{{ route('products.show', $product->slug) }}" class="block">
                <h3 class="product-title">{{ $product->name }}</h3>
            </a>
        </div>
        <div>
            <div class="flex flex-col">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="product-price">{{ number_format($finalPrice) }}đ</span>
                        @if($finalPrice < $originPrice)
                            <span class="product-original-price">{{ number_format($originPrice) }}đ</span>
                        @endif
                    </div>
                    @if($freeship)
                        <img src="{{ asset('images/free-shipping.png') }}" alt="Free Shipping" style="height: 16px;">
                    @endif
                </div>
                <div class="discount-tag">
                    <span class="text-xs font-semibold text-orange-500 mr-2 px-1 py-1 quality">Rẻ vô địch</span>
                    @if($maxDiscount)
                        @php
                            $badgeClass = 'discount-badge';
                            $icon = 'fa-percent';
                            if($maxDiscount->discount_type === 'fixed_amount') {
                                $badgeClass .= ' fixed-amount';
                                $icon = 'fa-money-bill-wave';
                            } else {
                                $badgeClass .= ' percentage';
                            }
                        @endphp
                        <div class="{{ $badgeClass }}" title="{{ $maxDiscount->name }}" data-discount-code="{{ $maxDiscount->code }}">
                            <i class="fas {{ $icon }}"></i>
                            @if($maxDiscount->discount_type === 'percentage')
                                Giảm {{ $maxDiscount->discount_value }}%
                            @elseif($maxDiscount->discount_type === 'fixed_amount')
                                Giảm {{ number_format($maxDiscount->discount_value) }}đ
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            {{-- XÓA NÚT MUA HÀNG/HẾT HÀNG --}}
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($product->average_rating ?? 0))
                            <i class="fas fa-star text-yellow-400 text-xs"></i>
                        @elseif($i - 0.5 <= ($product->average_rating ?? 0))
                            <i class="fas fa-star-half-alt text-yellow-400 text-xs"></i>
                        @else
                            <i class="far fa-star text-yellow-400 text-xs"></i>
                        @endif
                    @endfor
                    <span class="commons rating-count ml-1">({{ number_format($product->average_rating ?? 0, 1) }})</span>
                </div>
                @if(isset($product->sold_count))
                <div class="flex items-center">
                    <span class="commons">Đã bán {{ $product->sold_count > 1000 ? number_format($product->sold_count/1000, 1) . 'k' : $product->sold_count }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
 