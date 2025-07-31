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
            <span class="custom-badge badge-sale" style="background:#dc2626;top:36px;">Hết hàng</span>
        @endif
        <a href="{{ route('combos.show', $combo->slug) }}" class="block">
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
            <a href="{{ route('combos.show', $combo->slug) }}" class="block">
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
        </div>
    </div>
</div>
 