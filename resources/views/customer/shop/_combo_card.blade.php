@php
    $hasStock = $combo->has_stock ?? true;
    $discountPercent = $combo->discount_percent ?? 0;
    $imageUrl = $combo->image_url ?? asset('images/default-combo.png');
@endphp
<div class="product-card bg-white rounded-lg overflow-hidden {{ !$hasStock ? 'out-of-stock blurred' : '' }}" data-combo-id="{{ $combo->id }}">
    <div class="relative">
        @if(!$hasStock)
            <div class="out-of-stock-overlay">
                <span>Combo đã hết hàng</span>
            </div>
        @endif
        <a href="{{ route('combos.show', $combo->id) }}" class="block">
            <img src="{{ $imageUrl }}" alt="{{ $combo->name }}" class="product-image">
        </a>
        @if($discountPercent > 0)
            <span class="custom-badge badge-sale">-{{ $discountPercent }}%</span>
        @elseif(isset($combo->created_at) && $combo->created_at->diffInDays(now()) <= 7)
            <span class="custom-badge badge-new">Mới</span>
        @endif
    </div>
    <div class="px-4 py-2">
        <div class="flex items-center justify-between">
            <a href="{{ route('combos.show', $combo->id) }}" class="block">
                <h3 class="product-title">{{ $combo->name }}</h3>
            </a>
        </div>
        <div>
            <div class="flex flex-col">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="product-price">{{ number_format($combo->price) }}đ</span>
                        @if(isset($combo->original_price) && $combo->original_price > $combo->price)
                            <span class="product-original-price">{{ number_format($combo->original_price) }}đ</span>
                        @endif
                    </div>
                </div>
                <div class="text-xs text-gray-500 mt-1 line-clamp-2">{{ \Illuminate\Support\Str::limit($combo->description, 60) }}</div>
            </div>
            <div class="flex justify-between items-center mt-2">
                <a href="{{ route('combos.show', $combo->id) }}" class="add-to-cart-btn bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md text-sm flex items-center transition-colors">
                    <i class="fas fa-eye h-4 w-4 mr-1"></i>
                    Xem chi tiết
                </a>
            </div>
        </div>
    </div>
</div>
<style>
.out-of-stock {
    opacity: 0.5;
    pointer-events: none;
    position: relative;
}
.out-of-stock-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0,0,0,0.7);
    color: #fff;
    padding: 10px 24px;
    border-radius: 24px;
    font-size: 1rem;
    font-weight: 600;
    z-index: 20;
    text-align: center;
    pointer-events: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.blurred {
    filter: blur(3px);
    transition: filter 0.3s;
}
</style> 