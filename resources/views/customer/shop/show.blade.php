@extends('layouts.customer.fullLayoutMaster')

@section('title', $product->name)

@section('content')
<style>
    .container {
      max-width: 1280px;
      margin: 0 auto;
   }
   
   /* Discount code styles */
   .discount-code-animation {
       animation: pulse 2s infinite;
   }
   
   @keyframes pulse {
       0%, 100% {
           opacity: 1;
       }
       50% {
           opacity: 0.8;
       }
   }
   
   /* Style for public discount codes */
   .public-code {
       background-color: rgba(34, 197, 94, 0.9) !important;
       border: 1px dashed rgba(255, 255, 255, 0.5);
   }
   
   .copy-code:active {
       transform: scale(0.95);
   }
   
   /* Discount pill badge styles */
   .discount-pill {
       position: relative;
       overflow: hidden;
   }
   
   .discount-pill::after {
       content: '';
       position: absolute;
       top: 0;
       right: 0;
       width: 30%;
       height: 100%;
       background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.3));
       transform: skewX(-20deg);
       animation: shine 3s infinite;
   }
   
   @keyframes shine {
       0% {
           transform: translateX(-100%) skewX(-20deg);
       }
       100% {
           transform: translateX(200%) skewX(-20deg);
       }
   }
   
   @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes priceUpdate {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    
    .animate-price-update {
        animation: priceUpdate 0.5s ease-out;
    }
    
    .price-updated {
        color: #f97316 !important;
        font-weight: bold;
    }
    
    /* Variant price update animation */
    @keyframes variantPriceUpdate {
        0% { 
            opacity: 1; 
            background-color: rgba(249, 115, 22, 0.1);
            border-color: #f97316;
        }
        50% { 
            opacity: 0.8; 
            background-color: rgba(249, 115, 22, 0.2);
            border-color: #ea580c;
        }
        100% { 
            opacity: 1; 
            background-color: rgba(249, 115, 22, 0.1);
            border-color: #f97316;
        }
    }
    
    .variant-price-updated {
        animation: variantPriceUpdate 2s ease-in-out;
    }
    
    .topping-price-updated {
        animation: toppingPriceUpdate 2s ease-in-out;
    }
    
    /* Variant operation animations */
    @keyframes variantCreated {
        0% { 
            opacity: 0; 
            transform: scale(0.8);
            background-color: rgba(34, 197, 94, 0.1);
            border-color: #22c55e;
        }
        50% { 
            opacity: 0.8; 
            transform: scale(1.05);
            background-color: rgba(34, 197, 94, 0.2);
            border-color: #16a34a;
        }
        100% { 
            opacity: 1; 
            transform: scale(1);
            background-color: transparent;
            border-color: #d1d5db;
        }
    }
    
    @keyframes variantUpdated {
        0% { 
            opacity: 1; 
            background-color: rgba(59, 130, 246, 0.1);
            border-color: #3b82f6;
        }
        50% { 
            opacity: 0.8; 
            background-color: rgba(59, 130, 246, 0.2);
            border-color: #2563eb;
        }
        100% { 
            opacity: 1; 
            background-color: transparent;
            border-color: #d1d5db;
        }
    }
    
    @keyframes variantDeleted {
        0% { 
            opacity: 1; 
            transform: scale(1);
            background-color: rgba(239, 68, 68, 0.1);
            border-color: #ef4444;
        }
        100% { 
            opacity: 0; 
            transform: scale(0.8);
            background-color: rgba(239, 68, 68, 0.2);
            border-color: #dc2626;
        }
    }
    
    .variant-created {
        animation: variantCreated 2s ease-out;
    }
    
    .variant-updated {
        animation: variantUpdated 2s ease-out;
    }
    
    .variant-deleted {
        animation: variantDeleted 0.5s ease-out;
    }

    /* CSS cho badge */
    .custom-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        z-index: 10;
    }
    .badge-sale {
        background-color: #FF3B30;
        color: white;
    }
    .badge-new {
        background-color: #34C759;
        color: white;
        border-radius: 100px;
        font-size: 10px;
        z-index: 10;
    }
    /* Discount code styles */
    .discount-tag {
        margin-top: 8px;
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }
    .discount-badge, .quality {
        display: inline-flex;
        align-items: center;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
        color: white;
        margin-bottom: 2px;
    }
    .quality {
        display: inline-flex;
        align-items: center;
        padding: 2px 6px;
        border-radius: 2px;
        font-size: 10px;
        font-weight: 600;
        color: #F97316;
        margin-bottom: 2px;
        border: solid 1px #F97316;
    }
    .discount-badge i {
        margin-right: 3px;
        font-size: 9px;
    }
    .discount-badge.percentage {
        background-color: #F97316;
    }
    .discount-badge.fixed-amount {
        background-color: #8B5CF6;
    }
    .discount-badge.free-shipping {
        background-color: #0EA5E9;
    }
    .discount-badge.fade-out {
        opacity: 0;
        transform: scale(0.8);
        transition: opacity 0.5s ease, transform 0.5s ease;
    }
    .discount-badge.fade-in {
        opacity: 1;
        transform: scale(1);
        transition: opacity 0.5s ease, transform 0.5s ease;
    }
    .product-card {
        border: 1px solid #E5E7EB;
        transition: all 0.3s ease;
    }
    .product-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transform: translateY(-2px);
    }
    .product-image {
        height: 170px;
        object-fit: cover;
        width: 100%;
    }
    .product-title {
        color: #1F2937;
        font-weight: 600;
        font-size: 1.1rem;
        line-height: 1.5rem;
        margin-bottom: 0.1rem;
        transition: color 0.2s ease;
    }
    .product-title:hover {
        color: #F97316;
    }
    .product-price {
        font-size: 1.1rem;
        font-weight: 500;
        color: #F97316;
    }
    .product-original-price {
        font-size: 0.875rem;
        color: #6B7280;
        text-decoration: line-through;
    }
    .add-to-cart-btn {
        display: flex;
        align-items: center;
        padding: 0.5rem 1rem;
        background-color: #F97316;
        color: white;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.875rem;
        transition: background-color 0.2s ease;
    }
    .add-to-cart-btn i {
        margin-right: 4px;
    }
    .add-to-cart-btn:hover {
        background-color: #EA580C;
    }
    .rating-stars {
        color: #F97316;
    }
    .rating-count {
        color: #6B7280;
        font-size: 0.875rem;
    }
    .no-image-placeholder {
        height: 170px;
        width: 100%;
        background-color: #F3F4F6;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .no-image-placeholder::before {
        content: '';
        position: absolute;
        width: 40px;
        height: 40px;
        background-color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .no-image-placeholder i {
        position: relative;
        z-index: 1;
        color: #9CA3AF;
        font-size: 1.25rem;
    }
    .no-image-placeholder span {
        font-size: 0.875rem;
        font-weight: 500;
    }
    .add-to-cart-btn.disabled {
        background-color: #9CA3AF !important;
        cursor: not-allowed !important;
        opacity: 0.7;
        pointer-events: none;
    }
    .add-to-cart-btn.disabled:hover {
        background-color: #9CA3AF !important;
    }
    .commons {
        color: #000;
        font-size: 0.8rem;
        font-weight: 400;
    }
    /* Modern square anonymous checkbox */
    .custom-checkbox {
        appearance: none;
        width: 22px;
        height: 22px;
        border: 2px solid #f97316;
        border-radius: 6px;
        background: #fff;
        outline: none;
        cursor: pointer;
        position: relative;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-shadow: 0 1px 3px rgba(249,115,22,0.08);
        vertical-align: middle;
        display: inline-block;
    }
    .custom-checkbox:checked {
        background: #f97316;
        border-color: #f97316;
    }
    .custom-checkbox:checked:after {
        content: '';
        display: block;
        position: absolute;
        left: 6px;
        top: 2px;
        width: 6px;
        height: 12px;
        border: solid #fff;
        border-width: 0 3px 3px 0;
        transform: rotate(45deg);
    }
    .custom-checkbox:hover {
        border-color: #ea580c;
        box-shadow: 0 2px 8px rgba(249,115,22,0.15);
    }
    .anonymous-label {
        margin-left: 10px;
        font-weight: 600;
        color: #374151;
        font-size: 1rem;
        cursor: pointer;
        user-select: none;
        letter-spacing: 0.01em;
        transition: color 0.2s;
    }
    .custom-checkbox:checked + .anonymous-label {
        color: #f97316;
    }
    .reply-item {
        position: relative;
    }
    .reply-arrow {
        width: 24px;
        flex-shrink: 0;
        display: flex;
        align-items: flex-start;
        margin-top: 8px;
    }
    .reply-item .bg-blue-50 {
        position: relative;
    }
</style>
<div class="container mx-auto px-4 py-8">
    <!-- Product Info Section -->
    <div class="grid lg:grid-cols-2 gap-8 mb-12">
        <!-- Left column: Images -->
        <div class="space-y-4">
            @php
                $primaryImage = $product->images->where('is_primary', 1)->first() ?? $product->images->first();
            @endphp
            <div class="relative h-[300px] sm:h-[400px] rounded-lg overflow-hidden border">
                <img src="{{ $primaryImage ? Storage::disk('s3')->url($primaryImage->img) : '/placeholder.svg?height=600&width=600' }}"
                     alt="{{ $product->name }}"
                     class="object-cover w-full h-full"
                     id="main-product-image">
                @if($product->release_at && $product->release_at->diffInDays(now()) <= 7)
                    <span class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">Mới</span>
                @endif
            </div>

            <div class="flex gap-2 overflow-x-auto pb-2">
                @foreach($product->images as $image)
                <button class="relative w-20 h-20 rounded border-2 {{ ($primaryImage && $image->id === $primaryImage->id) ? 'border-orange-500' : 'border-transparent' }} overflow-hidden flex-shrink-0 product-thumbnail">
                    <img src="{{ Storage::disk('s3')->url($image->img) }}"
                         alt="{{ $product->name }} - Hình {{ $loop->iteration }}"
                         class="object-cover w-full h-full">
                </button>
                @endforeach
            </div>
        </div>

        <!-- Right column: Product Info -->
        <div class="space-y-6">
            @php
                $freeshipCode = null;
                $otherDiscounts = [];
                if(isset($product->applicable_discount_codes)) {
                    foreach($product->applicable_discount_codes as $discountCode) {
                        if($discountCode->discount_type === 'free_shipping') {
                            $freeshipCode = $discountCode;
                        } else {
                            $otherDiscounts[] = $discountCode;
                        }
                    }
                }
                // Tìm mã giảm giá trừ nhiều nhất
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
                
                // Giá gốc
                $originPrice = $product->discount_price && $product->base_price > $product->discount_price
                    ? $product->discount_price
                    : $product->min_price;
                // Giá sau giảm
                $finalPrice = $originPrice;
                if($maxDiscount) {
                    if($maxDiscount->discount_type === 'fixed_amount') {
                        $finalPrice = max(0, $originPrice - $maxDiscount->discount_value);
                    } elseif($maxDiscount->discount_type === 'percentage') {
                        $finalPrice = max(0, $originPrice * (1 - $maxDiscount->discount_value / 100));
                    }
                }
            @endphp
            <h1 class="text-2xl sm:text-3xl font-bold">{{ $product->name }}</h1>

            <!-- Price Display -->
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <span class="text-3xl font-bold text-orange-500 transition-all duration-300" id="current-price">
                        {{ number_format($finalPrice, 0, '', '.') }} đ
                    </span>
                    @if($finalPrice < $originPrice)
                    <span class="text-lg text-gray-400 line-through" id="base-price">
                        {{ number_format($originPrice, 0, '', '.') }} đ
                    </span>
                    @else
                    <span class="text-lg text-gray-400 line-through hidden" id="base-price">
                        {{ number_format($originPrice, 0, '', '.') }} đ
                    </span>
                    @endif
                </div>
                
                <!-- Price Update Notification -->
                <div id="price-update-notification" class="hidden">
                    <div class="flex items-center gap-2 text-sm text-orange-600 bg-orange-50 px-3 py-2 rounded-md border border-orange-200 animate-fade-in">
                        <i class="fas fa-info-circle"></i>
                        <span>Giá sản phẩm vừa được cập nhật</span>
                    </div>
                </div>
                
                <!-- Variant Price Update Notification -->
                <div id="variant-price-update-notification" class="hidden">
                    <div class="flex items-center gap-2 text-sm text-blue-600 bg-blue-50 px-3 py-2 rounded-md border border-blue-200 animate-fade-in">
                        <i class="fas fa-tags"></i>
                        <span>Giá biến thể vừa được cập nhật</span>
                    </div>
                </div>
                
                <!-- Topping Price Update Notification -->
                <div id="topping-price-update-notification" class="hidden">
                    <div class="flex items-center gap-2 text-sm text-purple-600 bg-purple-50 px-3 py-2 rounded-md border border-purple-200 animate-fade-in">
                        <i class="fas fa-utensils"></i>
                        <span>Giá topping vừa được cập nhật</span>
                    </div>
                </div>
                
                <!-- Variant Update Notification -->
                <div id="variant-update-notification" class="hidden">
                    <div class="flex items-center gap-2 text-sm text-blue-600 bg-blue-50 px-3 py-2 rounded-md border border-blue-200 animate-fade-in">
                        <i class="fas fa-tags"></i>
                        <span>Biến thể đã được cập nhật</span>
                    </div>
                </div>
            </div>

            <!-- Discount Codes Section -->
            @php
                $discountBadges = [];
                if(isset($product->applicable_discount_codes)) {
                    foreach($product->applicable_discount_codes as $discountCode) {
                        if($discountCode->discount_type !== 'free_shipping') {
                            $label = '';
                            if($discountCode->discount_type === 'percentage') {
                                $label = 'Giảm ' . $discountCode->discount_value . '%';
                            } elseif($discountCode->discount_type === 'fixed_amount') {
                                $label = 'Giảm đ' . number_format($discountCode->discount_value/1000,0).'k';
                            }
                            $discountBadges[] = $label;
                        }
                    }
                }
            @endphp
            @if(count($discountBadges) > 0)
            <div class="flex items-center gap-3 mb-2">
                <span class="font-medium mb-2">Voucher Của Shop</span>
                @foreach($product->applicable_discount_codes as $discountCode)
                    @if($discountCode->discount_type !== 'free_shipping')
                        <span class="px-2 py-1 rounded bg-red-50 text-red-500 text-sm font-medium border border-red-100" style="display:inline-block;">
                            @if($discountCode->discount_type === 'percentage')
                                Giảm {{ $discountCode->discount_value }}%
                            @elseif($discountCode->discount_type === 'fixed_amount')
                                Giảm đ{{ number_format($discountCode->discount_value/1000,0) }}k
                            @endif
                        </span>
                    @endif
                @endforeach
            </div>
            @endif

            @if($freeshipCode)
                <div class="flex items-center gap-3">
                    <span class="font-medium mb-2">Vận Chuyển</span>
                    <img src="{{ asset('images/free-shipping.png') }}" alt="Free Shipping" style="height: 22px;">
                    <span class="text-gray-700 text-sm">
                        <div class="text-sm text-gray-700">Giao Trong {{ now()->format('d \\T\\h m') }}</div>
                        <div class="text-sm text-gray-700">Phí ship 0đ</div>
                    </span>
                </div>
            @endif

            <!-- Get selected branch information for JS -->
            @php
                $selectedBranchId = $currentBranch ? $currentBranch->id : null;
                $isAvailable = false;
                
                if ($selectedBranchId) {
                    $variantCount = \App\Models\BranchStock::whereHas('productVariant', function($query) use ($product) {
                            $query->where('product_id', $product->id);
                        })
                        ->where('branch_id', $selectedBranchId)
                        ->where('stock_quantity', '>', 0)
                        ->distinct('product_variant_id')
                        ->count('product_variant_id');
                    
                    $isAvailable = $variantCount > 0;
                }
            @endphp
            
            <!-- Hidden branch_id input for JS -->
            <input type="hidden" id="branch-select" value="{{ $selectedBranchId }}">

            @if($selectedBranchId && !$isAvailable)
                <div id="out-of-stock-message" class="p-3 mb-4 bg-red-50 rounded-md text-red-700 text-sm border border-red-200">
                    <p>Sản phẩm hiện đang hết hàng tại chi nhánh của bạn. Vui lòng chọn chi nhánh khác.</p>
                </div>
            @endif

            @php
                // Debug log for selected branch and product
                \Log::debug('Product and Branch Info:', [
                    'product_id' => $product->id,
                    'selected_branch_id' => $selectedBranchId
                ]);
            @endphp

            <!-- Product variants -->
            <div class="space-y-4" id="variants-container">
                @foreach($variantAttributes as $attribute)
                @if(count($attribute->values) > 0)
                <div>
                    <h3 class="font-medium mb-2">{{ $attribute->name }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($attribute->values as $value)
                        @php
                            $variantStock = $selectedBranchId ? \App\Models\BranchStock::whereHas('productVariant', function($query) use ($product, $value) {
                                    $query->where('product_id', $product->id)
                                          ->whereHas('variantValues', function($q) use ($value) {
                                              $q->where('variant_value_id', $value->id);
                                          });
                                })
                                ->where('branch_id', $selectedBranchId)
                                ->first() : null;
                            
                            $stockQuantity = $variantStock ? $variantStock->stock_quantity : 0;
                            $productVariantId = $variantStock ? $variantStock->product_variant_id : null;
                        @endphp
                        <label class="relative flex items-center">
                            <input type="radio" 
                                   name="attribute_{{ $attribute->id }}" 
                                   value="{{ $value->id }}" 
                                   data-attribute-id="{{ $attribute->id }}"
                                   data-price-adjustment="{{ $value->price_adjustment }}"
                                   data-variant-id="{{ $productVariantId }}"
                                   data-stock-quantity="{{ $stockQuantity }}"
                                   data-branch-id="{{ $selectedBranchId }}"
                                   class="sr-only variant-input"
                                   {{ $loop->first ? 'checked' : '' }}
                                   {{ $stockQuantity <= 0 ? 'disabled' : '' }}>
                            <span class="px-4 py-2 rounded-md border cursor-pointer variant-label {{ $loop->first ? 'bg-orange-100 border-orange-500 text-orange-600' : '' }} hover:bg-gray-50 {{ $stockQuantity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}">
                                {{ $value->value }}
                                @if($value->price_adjustment != 0)
                                    <span class="text-sm ml-1 {{ $value->price_adjustment > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $value->price_adjustment > 0 ? '+' : '' }}{{ number_format($value->price_adjustment, 0, '', '.') }} đ
                                    </span>
                                @endif
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif
                @endforeach
            </div>

            <!-- Toppings Section -->
            @if(count($product->toppings) > 0)
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="font-medium">Toppings</h3>
                    <span class="text-sm text-gray-500">Chọn nhiều</span>
                </div>
                <div class="relative">
                    <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-orange-200 scrollbar-track-gray-100 hover:scrollbar-thumb-orange-300">
                        @foreach($product->toppings as $topping)
                        <label class="relative flex-shrink-0 w-24 cursor-pointer group {{ isset($selectedBranch) && $selectedBranch && !$isAvailable ? 'opacity-50 cursor-not-allowed' : '' }}">
                            <input type="checkbox" 
                                   name="toppings[]" 
                                   value="{{ $topping->id }}"
                                   class="sr-only topping-input"
                                   data-price="{{ $topping->price }}"
                                   data-topping-id="{{ $topping->id }}"
                                   data-branch-id="{{ $selectedBranchId }}"
                                   data-stock-quantity="{{ $topping->toppingStocks->first() ? $topping->toppingStocks->first()->stock_quantity : 0 }}"
                                   {{ isset($selectedBranch) && $selectedBranch && !$isAvailable ? 'disabled' : '' }}>
                            <div class="relative aspect-square rounded-lg overflow-hidden border group-hover:border-orange-500 transition-colors">
                                @if($topping->image)
                                    <img src="{{ Storage::disk('s3')->url($topping->image) }}" 
                                         alt="{{ $topping->name }}" 
                                         class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                        <i class="fas fa-utensils text-gray-400 text-xl"></i>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity"></div>
                                <div class="absolute top-1 right-1 w-4 h-4 border-2 border-white rounded-full bg-white/50 backdrop-blur-sm">
                                    <div class="w-full h-full rounded-full bg-orange-500 scale-0 group-hover:scale-100 transition-transform duration-200"></div>
                                </div>
                                @if($selectedBranchId)
                                    @php
                                        $toppingStock = $topping->toppingStocks->first();
                                        $stockQuantity = $toppingStock ? $toppingStock->stock_quantity : 0;
                                    @endphp
                                    @if($stockQuantity < 5)
                                        <div class="absolute bottom-0 left-0 right-0 bg-orange-500 bg-opacity-80 text-white text-xs text-center py-1 stock-display">
                                            Còn {{ $stockQuantity }}
                                        </div>
                                    @endif
                                @endif
                            </div>
                            <div class="mt-1 text-center">
                                <p class="text-xs font-medium truncate">{{ $topping->name }}</p>
                                <p class="text-xs text-orange-500 font-medium">
                                    +{{ number_format($topping->price, 0, '', '.') }} đ
                                </p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Quantity Selection -->
            <div class="flex items-center gap-4">
                <span class="font-medium">Số lượng:</span>
                <div class="flex items-center">
                    <button class="h-8 w-8 rounded-l-md border border-gray-300 flex items-center justify-center hover:bg-gray-100 {{ isset($selectedBranch) && $selectedBranch && !$isAvailable ? 'opacity-50 cursor-not-allowed' : '' }}" 
                            id="decrease-quantity"
                            {{ isset($selectedBranch) && $selectedBranch && !$isAvailable ? 'disabled' : '' }}>
                        <i class="fas fa-minus h-3 w-3"></i>
                    </button>
                    <div class="h-8 px-3 flex items-center justify-center border-y border-gray-300" id="quantity">1</div>
                    <button class="h-8 w-8 rounded-r-md border border-gray-300 flex items-center justify-center hover:bg-gray-100 {{ isset($selectedBranch) && $selectedBranch && !$isAvailable ? 'opacity-50 cursor-not-allowed' : '' }}" 
                            id="increase-quantity"
                            {{ isset($selectedBranch) && $selectedBranch && !$isAvailable ? 'disabled' : '' }}>
                        <i class="fas fa-plus h-3 w-3"></i>
                    </button>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button id="add-to-cart" 
                        data-product-id="{{ $product->id }}"
                        class="w-full sm:flex-1 {{ isset($product->has_stock) && $product->has_stock ? 'bg-orange-500 hover:bg-orange-600' : 'bg-gray-400' }} text-white px-6 py-3 rounded-md font-medium transition-colors flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ isset($product->has_stock) && !$product->has_stock ? 'disabled' : '' }}>
                    <i class="fas {{ isset($product->has_stock) && $product->has_stock ? 'fa-shopping-cart' : 'fa-ban' }} h-5 w-5 mr-2"></i>
                    <span>{{ isset($product->has_stock) && !$product->has_stock ? 'Hết hàng' : 'Thêm vào giỏ hàng' }}</span>
                </button>
                <button id="buy-now" 
                        class="w-full sm:flex-1 border border-gray-300 hover:bg-gray-50 px-6 py-3 rounded-md font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ isset($product->has_stock) && !$product->has_stock ? 'disabled' : '' }}>
                    Mua ngay
                </button>
                <div class="flex gap-3 justify-center sm:justify-start">
                    @auth
                    <button class="border border-gray-300 hover:bg-gray-50 h-12 w-12 rounded-md flex items-center justify-center favorite-btn" data-product-id="{{ $product->id }}">
                        @if(isset($product->is_favorite) && $product->is_favorite)
                            <i class="fas fa-heart text-red-500 h-5 w-5"></i>
                        @else
                            <i class="far fa-heart h-5 w-5"></i>
                        @endif
                        <span class="sr-only">Yêu thích</span>
                    </button>
                    @else
                    <button class="border border-gray-300 hover:bg-gray-50 h-12 w-12 rounded-md flex items-center justify-center" id="login-prompt-btn">
                        <i class="far fa-heart h-5 w-5"></i>
                        <span class="sr-only">Yêu thích</span>
                    </button>
                    @endauth
                    <button class="border border-gray-300 hover:bg-gray-50 h-12 w-12 rounded-md flex items-center justify-center">
                        <i class="fas fa-share-alt h-5 w-5"></i>
                        <span class="sr-only">Chia sẻ</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Details Section -->
    <div class="border rounded-lg overflow-hidden bg-white">
        <div class="grid grid-cols-3 border-b">
            <button class="py-4 text-center font-medium border-b-2 border-orange-500 text-orange-500" id="tab-description" data-tab="description">
                Mô tả
            </button>
            <button class="py-4 text-center font-medium border-b-2 border-transparent hover:text-orange-500" id="tab-ingredients" data-tab="ingredients">
                Thành phần
            </button>
            <button class="py-4 text-center font-medium border-b-2 border-transparent hover:text-orange-500" id="tab-reviews" data-tab="reviews">
                Đánh giá
            </button>
        </div>
        
        <div class="p-6">
            <!-- Description Tab -->
            <div class="tab-content" id="content-description">
                <p class="text-gray-600 leading-relaxed">{{ $product->description }}</p>
            </div>
            
            <!-- Ingredients Tab -->
            <div class="tab-content hidden" id="content-ingredients">
                @if(!empty($product->ingredients))
                    @if(is_array($product->ingredients) && !empty($product->ingredients))
                        <div class="space-y-4">
                            <h4 class="font-medium mb-3 text-gray-900">Thành phần:</h4>
                            <ul class="space-y-2">
                                @foreach($product->ingredients as $ingredient)
                                    <li class="flex items-center space-x-2 text-gray-700">
                                        <span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span>
                                        <span class="flex-1">{{ $ingredient }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="text-gray-600">{{ $product->ingredients }}</p>
                    @endif
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clipboard-list text-3xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 font-medium">Không có thông tin thành phần</p>
                    </div>
                @endif
            </div>

            <!-- Reviews Tab -->
            <div class="tab-content hidden" id="content-reviews">
                <div class="bg-white rounded-lg">
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold flex items-center gap-2">
                                <i class="fas fa-star text-yellow-400"></i>
                                Đánh giá sản phẩm
                                <span class="text-gray-500 text-sm">({{ $product->reviews_count }} đánh giá)</span>
                            </h3>
                            <div class="flex items-center gap-2">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($product->average_rating))
                                            <i class="fas fa-star text-yellow-400"></i>
                                        @elseif($i - 0.5 <= $product->average_rating)
                                            <i class="fas fa-star-half-alt text-yellow-400"></i>
                                        @else
                                            <i class="far fa-star text-yellow-400"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-sm font-medium">{{ number_format($product->average_rating, 1) }}/5</span>
                            </div>
                        </div>
                    </div>

                    <div class="divide-y max-h-[600px] overflow-y-auto scrollbar-thin scrollbar-thumb-orange-200 scrollbar-track-gray-100 hover:scrollbar-thumb-orange-300">
                        @forelse($product->reviews as $review)
                        <div class="p-6 hover:bg-gray-50/50 transition-colors" data-review-id="{{ $review->id }}">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-semibold text-lg">
                                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-medium text-gray-900">{{ $review->is_anonymous ? 'Ẩn danh' : $review->user->name }}</span>
                                            @if($review->is_verified_purchase)
                                                <span class="inline-flex items-center gap-1 text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                                                    <i class="fas fa-check-circle"></i>
                                                    Đã mua hàng
                                                </span>
                                            @endif
                                            @if($review->is_featured)
                                                <span class="inline-flex items-center gap-1 text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">
                                                    <i class="fas fa-award"></i>
                                                    Đánh giá nổi bật
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500 mt-1 space-x-2">
                                            <span>{{ $review->review_date->format('d/m/Y H:i') }}</span>
                                            @if($review->branch)
                                                <span>•</span>
                                                <span>{{ $review->branch->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <div class="flex items-center gap-1 bg-yellow-50 px-2 py-1 rounded">
                                        <span class="font-medium text-yellow-700">{{ $review->rating }}.0</span>
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <i class="fas fa-star text-yellow-400"></i>
                                                @else
                                                    <i class="far fa-star text-yellow-400"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 space-y-3">
                                <p class="text-gray-700 leading-relaxed">{{ $review->review }}</p>
                                
                                @if($review->review_image)
                                    <div class="mt-3">
                                        <img src="{{ Storage::disk('s3')->url($review->review_image) }}" 
                                             alt="Review image" 
                                             class="rounded-lg max-h-48 object-cover hover:opacity-95 transition-opacity cursor-pointer">
                                    </div>
                                @endif

                                <div class="flex items-center gap-6 pt-2">
                                    @php
                                        $userHelpful = auth()->check() ? \App\Models\ReviewHelpful::where('user_id', auth()->id())->where('review_id', $review->id)->exists() : false;
                                    @endphp
                                    <button class="inline-flex items-center gap-2 text-sm helpful-btn {{ $userHelpful ? 'helpful-active text-sky-600' : '' }}"
                                            data-review-id="{{ $review->id }}"
                                            data-helpful="{{ $userHelpful ? '1' : '0' }}">
                                        <i class="{{ $userHelpful ? 'fas' : 'far' }} fa-thumbs-up {{ $userHelpful ? 'text-sky-600' : '' }}"></i>
                                        <span>Hữu ích (<span class="helpful-count">{{ $review->helpful_count }}</span>)</span>
                                    </button>
                                    @if($review->report_count > 0)
                                        <span class="inline-flex items-center gap-1 text-xs text-red-500">
                                            <i class="fas fa-flag"></i>
                                            {{ $review->report_count }} báo cáo
                                        </span>
                                    @endif
                                    @auth
                                        <button class="inline-flex items-center gap-2 text-sm text-blue-500 hover:text-blue-700 transition-colors reply-review-btn"
                                            data-review-id="{{ $review->id }}"
                                            data-user-name="{{ $review->is_anonymous ? 'Ẩn danh' : $review->user->name }}"
                                            data-route-reply="{{ route('reviews.reply', ['review' => $review->id]) }}">
                                            <i class="fas fa-reply"></i>
                                            <span>Phản hồi</span>
                                        </button>
                                        @if($review->user_id === auth()->id() || (auth()->user()->is_admin ?? false))
                                            <button class="inline-flex items-center gap-2 text-sm text-red-500 hover:text-red-700 transition-colors delete-review-btn" data-review-id="{{ $review->id }}">
                                                <i class="fas fa-trash-alt"></i>
                                                <span>Xóa</span>
                                            </button>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        </div>
                        <!-- Hiển thị các reply -->
                        @foreach($review->replies as $reply)
                            <div class="reply-item flex items-start gap-2 ml-8 mt-2 relative" data-reply-id="{{ $reply->id }}">
                                <div class="reply-arrow">
                                    <svg width="24" height="24" viewBox="0 0 24 24" class="text-blue-400"><path d="M2 12h16M18 12l-4-4m4 4l-4 4" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-2 flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-semibold text-blue-700">{{ $reply->user->name ?? 'Ẩn danh' }}</span>
                                        <span class="text-xs text-gray-400">{{ $reply->reply_date ? \Carbon\Carbon::parse($reply->reply_date)->format('d/m/Y H:i') : '' }}</span>
                                        @auth
                                            @if($reply->user_id === auth()->id() || (auth()->user()->is_admin ?? false))
                                                <button class="inline-flex items-center gap-1 text-xs text-red-500 hover:text-red-700 transition-colors delete-reply-btn" data-reply-id="{{ $reply->id }}">
                                                    <i class="fas fa-trash-alt"></i> Xóa
                                                </button>
                                            @endif
                                        @endauth
                                    </div>
                                    <div class="text-gray-700">{{ $reply->reply }}</div>
                                </div>
                            </div>
                        @endforeach
                        @empty
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="far fa-comment-alt text-3xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 font-medium">Chưa có đánh giá nào cho sản phẩm này.</p>
                            <p class="text-gray-400 text-sm mt-1">Hãy là người đầu tiên đánh giá sản phẩm!</p>
                        </div>
                        @endforelse
                    </div>

                    {{-- Form gửi đánh giá hoặc phản hồi --}}
                    <div id="review-reply-form-container" class="mt-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
                        <form id="review-reply-form" action="{{ route('products.review', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4" data-default-action="{{ route('products.review', $product->id) }}">
                            @csrf
                            <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
                            <input type="hidden" name="reply_review_id" id="reply_review_id" value="">
                            <div id="replying-to" class="mb-2 hidden">
                                <span class="text-sm text-blue-600">Phản hồi cho <b id="replying-to-user"></b></span>
                                <button type="button" id="cancel-reply" class="ml-2 text-xs text-gray-500 hover:text-red-500">Hủy</button>
                            </div>
                            <div class="flex items-center justify-between mb-4 gap-2 flex-wrap" id="rating-row">
                                <h4 class="font-semibold text-lg" id="form-title" data-default-title="Gửi đánh giá của bạn">Gửi đánh giá của bạn</h4>
                                <div class="flex items-center" id="rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" class="sr-only">
                                        <label for="star{{ $i }}" class="cursor-pointer text-2xl text-yellow-400" style="position: relative;">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    @endfor
                                </div>
                            </div>
                            <div id="review-message" class="mb-4 text-center"></div>
                            <div>
                                <textarea name="review" id="review-textarea" rows="3" class="w-full border rounded p-2" placeholder="Chia sẻ cảm nhận của bạn..." data-default-placeholder="Chia sẻ cảm nhận của bạn..."></textarea>
                            </div>
                            <div>
                                <label class="block font-medium mb-1">Ảnh minh họa (tùy chọn):</label>
                                <div class="flex items-center justify-between gap-4 flex-wrap">
                                    <div>
                                        <input type="file" name="review_image" id="review_image" accept="image/*" class="hidden">
                                        <label for="review_image" class="w-20 h-20 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer hover:border-orange-400 transition-colors relative">
                                            <i class="fas fa-camera text-3xl text-orange-500"></i>
                                            <img id="preview_image" src="#" alt="Preview" class="absolute inset-0 w-full h-full object-cover rounded-lg hidden" />
                                        </label>
                                    </div>
                                    <div class="flex items-center ml-auto">
                                        <input type="checkbox" name="is_anonymous" id="is_anonymous" value="1" class="custom-checkbox" {{ old('is_anonymous') ? 'checked' : '' }}>
                                        <label for="is_anonymous" class="anonymous-label">Ẩn danh</label>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" id="review-submit-btn" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded font-medium" data-default-text="Gửi đánh giá">Gửi đánh giá</button>
                        </form>
                    </div>

                    @if($product->reviews->count() > 0)
                    <div class="mt-6 flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Hiển thị {{ $product->reviews->count() }} đánh giá
                        </div>
                        <button class="inline-flex items-center gap-1 text-orange-500 hover:text-orange-600 font-medium text-sm transition-colors">
                            <span>Xem tất cả</span>
                            <i class="fas fa-chevron-right text-xs"></i>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="mt-12">
        <h2 class="text-2xl font-bold mb-6">Sản Phẩm Liên Quan</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            @foreach($relatedProducts as $relatedProduct)
            @php
                $freeship = null;
                $otherDiscounts = [];
                if(isset($relatedProduct->applicable_discount_codes)) {
                    foreach($relatedProduct->applicable_discount_codes as $discountCode) {
                        if($discountCode->discount_type === 'free_shipping') {
                            $freeship = $discountCode;
                        } else {
                            $otherDiscounts[] = $discountCode;
                        }
                    }
                }
                $relatedMaxDiscount = null;
                $relatedMaxValue = 0;
                foreach($otherDiscounts as $discountCode) {
                    if($discountCode->discount_type === 'fixed_amount') {
                        $value = $discountCode->discount_value;
                    } elseif($discountCode->discount_type === 'percentage') {
                        $value = isset($relatedProduct->min_price) ? ($relatedProduct->min_price * $discountCode->discount_value / 100) : 0;
                    } else {
                        $value = 0;
                    }
                    if($value > $relatedMaxValue) {
                        $relatedMaxValue = $value;
                        $relatedMaxDiscount = $discountCode;
                    }
                }
                $originPrice = $relatedProduct->discount_price && $relatedProduct->base_price > $relatedProduct->discount_price
                    ? $relatedProduct->discount_price
                    : $relatedProduct->min_price;
                $finalPrice = $originPrice;
                if($relatedMaxDiscount) {
                    if($relatedMaxDiscount->discount_type === 'fixed_amount') {
                        $finalPrice = max(0, $originPrice - $relatedMaxDiscount->discount_value);
                    } elseif($relatedMaxDiscount->discount_type === 'percentage') {
                        $finalPrice = max(0, $originPrice * (1 - $relatedMaxDiscount->discount_value / 100));
                    }
                }
            @endphp
            <div class="product-card bg-white rounded-lg overflow-hidden" data-product-id="{{ $relatedProduct->id }}">
                <div class="relative">
                    <a href="{{ route('products.show', $relatedProduct->id) }}" class="block">
                        @if($relatedProduct->primary_image)
                            <img src="{{ $relatedProduct->primary_image->s3_url }}" alt="{{ $relatedProduct->name }}" class="product-image">
                        @else
                            <div class="no-image-placeholder">
                                <i class="far fa-image"></i>
                            </div>
                        @endif
                    </a>
                    @if($relatedProduct->discount_price && $relatedProduct->base_price > $relatedProduct->discount_price)
                        @php
                            $discountPercent = round((($relatedProduct->base_price - $relatedProduct->discount_price) / $relatedProduct->base_price) * 100);
                        @endphp
                        <span class="custom-badge badge-sale">-{{ $discountPercent }}%</span>
                    @elseif($relatedProduct->created_at->diffInDays(now()) <= 7)
                        <span class="custom-badge badge-new">Mới</span>
                    @endif
                </div>
                <div class="px-4 py-2">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('products.show', $relatedProduct->id) }}" class="block">
                            <h3 class="product-title">{{ $relatedProduct->name }}</h3>
                        </a>
                    </div>
                    <div>
                        <div class="flex flex-col">
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="product-price">{{ number_format($finalPrice, 0, '', '.') }}đ</span>
                                    @if($finalPrice < $originPrice)
                                        <span class="product-original-price">{{ number_format($originPrice, 0, '', '.') }}đ</span>
                                    @endif
                                </div>
                                @if($freeship)
                                    <img src="{{ asset('images/free-shipping.png') }}" alt="Free Shipping" style="height: 16px;">
                                @endif
                            </div>
                            <div class="discount-tag">
                                <span class="text-xs font-semibold text-orange-500 mr-2 px-1 py-1 quality">Rẻ vô địch</span>
                                @if($relatedMaxDiscount)
                                    @php
                                        $badgeClass = 'discount-badge';
                                        $icon = 'fa-percent';
                                        if($relatedMaxDiscount->discount_type === 'fixed_amount') {
                                            $badgeClass .= ' fixed-amount';
                                            $icon = 'fa-money-bill-wave';
                                        } else {
                                            $badgeClass .= ' percentage';
                                        }
                                    @endphp
                                    <div class="{{ $badgeClass }}" title="{{ $relatedMaxDiscount->name }}" data-discount-code="{{ $relatedMaxDiscount->code }}">
                                        <i class="fas {{ $icon }}"></i>
                                        @if($relatedMaxDiscount->discount_type === 'percentage')
                                            Giảm {{ $relatedMaxDiscount->discount_value }}%
                                        @elseif($relatedMaxDiscount->discount_type === 'fixed_amount')
                                            Giảm {{ number_format($relatedMaxDiscount->discount_value, 0, '', '.') }}đ
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-start items-center">
                        <div class="flex items-center mr-4">
                            <i class="fas fa-star text-yellow-400 text-xs"></i>
                            <span class="commons rating-count ml-1">{{ $relatedProduct->reviews_count }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="commons">Đã bán 46k</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Login Popup Modal -->
<div id="login-popup" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4 transform transition-transform">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-900">Đăng nhập</h3>
            <button id="close-login-popup" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-lock text-orange-500 text-2xl"></i>
            </div>
            <p class="text-gray-700">Vui lòng đăng nhập để thêm sản phẩm vào danh sách yêu thích</p>
        </div>
        <div class="space-y-4">
            <a href="{{ route('customer.login') }}" class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-center px-6 py-3 rounded-md font-medium transition-colors">
                Đăng nhập
            </a>
            <a href="{{ route('customer.register') }}" class="block w-full border border-gray-300 hover:bg-gray-50 text-center px-6 py-3 rounded-md font-medium transition-colors">
                Đăng ký
            </a>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
    window.selectedBranchId = {{ $selectedBranchId ? $selectedBranchId : 'null' }};
    window.productId = {{ $product->id }};
    window.basePrice = {{ $product->base_price }};
    window.csrfToken = '{{ csrf_token() }}';
    window.pusherKey = '{{ config('broadcasting.connections.pusher.key') }}';
    window.pusherCluster = '{{ config('broadcasting.connections.pusher.options.cluster') }}';
    window.bestDiscountCode = @json($maxDiscount);
    window.bestDiscountAmount = {{ $maxValue }};
    window.variantCombinations = @json(
        \App\Models\ProductVariant::where('product_id', $product->id)
            ->with('variantValues')
            ->get()
            ->mapWithKeys(function($variant) {
                $ids = $variant->variantValues->pluck('id')->sort()->values()->toArray();
                return [implode('_', $ids) => $variant->id];
            })
    );
    window.currentUserId = {{ auth()->check() ? auth()->id() : 'null' }};
    window.isAdmin = {{ (auth()->user()->is_admin ?? false) ? 'true' : 'false' }};
</script>
<script src="{{ asset('js/Customer/Shop/shop.js') }}"></script>
@include('partials.customer.branch-check')
<script>
    document.querySelectorAll('.reply-review-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const reviewId = this.getAttribute('data-review-id');
            const form = document.getElementById('reply-form-' + reviewId);
            if (form) {
                form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
            }
        });
    });
</script>
@endsection