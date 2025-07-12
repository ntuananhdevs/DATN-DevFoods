@php
    $discountPercent = $combo->discount_percent ?? 0;
    $imageUrl = $combo->image_url ?? asset('images/default-combo.png');
@endphp
<div class="product-card bg-white rounded-lg overflow-hidden {{ isset($combo->has_stock) && !$combo->has_stock ? 'out-of-stock' : '' }}" data-combo-id="{{ $combo->id }}">
    <div class="relative">
        @if(isset($combo->has_stock) && !$combo->has_stock)
            <div class="out-of-stock-overlay">
                <span>Hết hàng</span>
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
 