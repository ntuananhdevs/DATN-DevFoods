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
</style>
<div class="container mx-auto px-4 py-8">
    <!-- Product Info Section -->
    <div class="grid lg:grid-cols-2 gap-8 mb-12">
        <!-- Left column: Images -->
        <div class="space-y-4">
            <div class="relative h-[300px] sm:h-[400px] rounded-lg overflow-hidden border">
                <img src="{{ $product->images->first() ? $product->images->first()->s3_url : '/placeholder.svg?height=600&width=600' }}" 
                     alt="{{ $product->name }}" 
                     class="object-cover w-full h-full" 
                     id="main-product-image">
                @if($product->release_at && $product->release_at->diffInDays(now()) <= 7)
                    <span class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">Mới</span>
                @endif
            </div>

            <div class="flex gap-2 overflow-x-auto pb-2">
                @foreach($product->images as $image)
                <button class="relative w-20 h-20 rounded border-2 {{ $loop->first ? 'border-orange-500' : 'border-transparent' }} overflow-hidden flex-shrink-0 product-thumbnail">
                    <img src="{{ $image->s3_url }}" 
                         alt="{{ $product->name }} - Hình {{ $loop->iteration }}" 
                         class="object-cover w-full h-full">
                </button>
                @endforeach
            </div>
        </div>

        <!-- Right column: Product Info -->
        <div class="space-y-6">
            <h1 class="text-2xl sm:text-3xl font-bold">{{ $product->name }}</h1>

            <!-- Price Display -->
            <div class="flex items-center gap-3">
                <span class="text-3xl font-bold text-orange-500" id="current-price">
                    {{ number_format($product->base_price, 0, ',', '.') }}đ
                </span>
                <span class="text-lg text-gray-400 line-through hidden" id="base-price">
                    {{ number_format($product->base_price, 0, ',', '.') }}đ
                </span>
            </div>

            <!-- Discount Codes Section -->
            @if(isset($product->applicable_discount_codes) && $product->applicable_discount_codes->count() > 0)
            <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg p-4 border border-orange-200 shadow-sm">
                <h3 class="text-sm font-semibold flex items-center gap-2 mb-3 text-orange-800">
                    <i class="fas fa-tags text-orange-500"></i>
                    Mã giảm giá áp dụng
                </h3>
                <div class="space-y-2">
                    @foreach($product->applicable_discount_codes as $discountCode)
                        @php
                            $bgColor = 'bg-orange-500';
                            $icon = 'fa-percent';
                            
                            if($discountCode->discount_type === 'fixed_amount') {
                                $bgColor = 'bg-purple-500';
                                $icon = 'fa-money-bill-wave';
                            } elseif($discountCode->discount_type === 'free_shipping') {
                                $bgColor = 'bg-blue-500';
                                $icon = 'fa-shipping-fast';
                            }
                        @endphp
                        <div class="flex items-center gap-3 p-2 rounded-md bg-white hover:bg-orange-50 transition-colors cursor-pointer relative group shadow-sm">
                            <div class="flex-shrink-0 w-10 h-10 {{ $bgColor }} rounded-full flex items-center justify-center discount-code-animation">
                                <i class="fas {{ $icon }} text-white"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-900">{{ $discountCode->code }}</span>
                                    @if($discountCode->end_date->diffInDays(now()) <= 3)
                                        <span class="text-xs px-1.5 py-0.5 bg-red-100 text-red-700 rounded-sm">
                                            Sắp hết hạn
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 truncate">
                                    @if($discountCode->discount_type === 'percentage')
                                        Giảm {{ $discountCode->discount_value }}% 
                                    @elseif($discountCode->discount_type === 'fixed_amount')
                                        Giảm {{ number_format($discountCode->discount_value) }}đ
                                    @else
                                        Miễn phí vận chuyển
                                    @endif
                                    @if($discountCode->min_order_amount > 0)
                                        cho đơn từ {{ number_format($discountCode->min_order_amount) }}đ
                                    @endif
                                </p>
                                <div class="text-xs text-gray-500 mt-1">
                                    HSD: {{ $discountCode->end_date->format('d/m/Y') }}
                                </div>
                            </div>
                            <button class="copy-code opacity-0 group-hover:opacity-100 transition-opacity px-3 py-1 bg-orange-500 hover:bg-orange-600 text-white rounded-md text-xs font-medium" data-code="{{ $discountCode->code }}">
                                Sao chép
                            </button>
                            <div class="absolute inset-0 bg-white pointer-events-none opacity-0 group-hover:opacity-10 transition-opacity rounded-md"></div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <p class="text-gray-600">{{ $product->short_description }}</p>

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
                            
                            // Debug log for each variant
                            \Log::debug('Variant Stock Info:', [
                                'attribute' => $attribute->name,
                                'value' => $value->value,
                                'variant_stock' => $variantStock ? [
                                    'id' => $variantStock->id,
                                    'product_variant_id' => $variantStock->product_variant_id,
                                    'stock_quantity' => $variantStock->stock_quantity
                                ] : null
                            ]);
                            
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
                                        {{ $value->price_adjustment > 0 ? '+' : '' }}{{ number_format($value->price_adjustment, 0, ',', '.') }}đ
                                    </span>
                                @endif
                                <span class="text-xs ml-1 {{ $stockQuantity <= 5 ? 'text-orange-500' : 'text-gray-500' }} stock-display">
                                    @if($stockQuantity > 0)
                                        (Còn {{ $stockQuantity }})
                                    @else
                                        (Hết hàng)
                                    @endif
                                </span>
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
                                        <div class="absolute bottom-0 left-0 right-0 bg-orange-500 bg-opacity-80 text-white text-xs text-center py-1">
                                            Còn {{ $stockQuantity }}
                                        </div>
                                    @endif
                                @endif
                            </div>
                            <div class="mt-1 text-center">
                                <p class="text-xs font-medium truncate">{{ $topping->name }}</p>
                                <p class="text-xs text-orange-500 font-medium">
                                    +{{ number_format($topping->price, 0, ',', '.') }}đ
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
                    @php
                        $ingredients = is_string($product->ingredients) ? json_decode($product->ingredients, true) : $product->ingredients;
                    @endphp
                    
                    @if(is_array($ingredients))
                        <div class="space-y-6">
                            @if(isset($ingredients['base']))
                                <div>
                                    <h4 class="font-medium mb-3 text-gray-900">Nguyên liệu cơ bản:</h4>
                                    <ul class="space-y-2">
                                        @foreach((array)$ingredients['base'] as $item)
                                            <li class="flex items-center space-x-2 text-gray-700">
                                                <span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span>
                                                <span class="flex-1">{{ is_array($item) ? ($item['name'] ?? '') : $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(isset($ingredients['vegetables']))
                                <div>
                                    <h4 class="font-medium mb-3 text-gray-900">Rau củ:</h4>
                                    <ul class="space-y-2">
                                        @foreach((array)$ingredients['vegetables'] as $item)
                                            <li class="flex items-center space-x-2 text-gray-700">
                                                <span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span>
                                                <span class="flex-1">{{ is_array($item) ? ($item['name'] ?? '') : $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(isset($ingredients['meat']))
                                <div>
                                    <h4 class="font-medium mb-3 text-gray-900">Thịt:</h4>
                                    <ul class="space-y-2">
                                        @foreach((array)$ingredients['meat'] as $item)
                                            <li class="flex items-center space-x-2 text-gray-700">
                                                <span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span>
                                                <span class="flex-1">{{ is_array($item) ? ($item['name'] ?? '') : $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(isset($ingredients['sauces']))
                                <div>
                                    <h4 class="font-medium mb-3 text-gray-900">Sốt:</h4>
                                    <ul class="space-y-2">
                                        @foreach((array)$ingredients['sauces'] as $item)
                                            <li class="flex items-center space-x-2 text-gray-700">
                                                <span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span>
                                                <span class="flex-1">{{ is_array($item) ? ($item['name'] ?? '') : $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(isset($ingredients['cheese']))
                                <div>
                                    <h4 class="font-medium mb-3 text-gray-900">Phô mai:</h4>
                                    <ul class="space-y-2">
                                        @foreach((array)$ingredients['cheese'] as $item)
                                            <li class="flex items-center space-x-2 text-gray-700">
                                                <span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span>
                                                <span class="flex-1">{{ is_array($item) ? ($item['name'] ?? '') : $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @foreach($ingredients as $key => $items)
                                @if(!in_array($key, ['base', 'vegetables', 'meat', 'sauces', 'cheese']) && is_array($items))
                                    <div>
                                        <h4 class="font-medium mb-3 text-gray-900">{{ ucfirst(str_replace('_', ' ', $key)) }}:</h4>
                                        <ul class="space-y-2">
                                            @foreach($items as $item)
                                                <li class="flex items-center space-x-2 text-gray-700">
                                                    <span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span>
                                                    <span class="flex-1">{{ is_array($item) ? ($item['name'] ?? '') : $item }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            @endforeach
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
                        <div class="p-6 hover:bg-gray-50/50 transition-colors">
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
                                        <img src="{{ Storage::url($review->review_image) }}" 
                                             alt="Review image" 
                                             class="rounded-lg max-h-48 object-cover hover:opacity-95 transition-opacity cursor-pointer">
                                    </div>
                                @endif

                                <div class="flex items-center gap-6 pt-2">
                                    <button class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                                        <i class="far fa-thumbs-up"></i>
                                        <span>Hữu ích ({{ $review->helpful_count }})</span>
                                    </button>
                                    @if($review->report_count > 0)
                                        <span class="inline-flex items-center gap-1 text-xs text-red-500">
                                            <i class="fas fa-flag"></i>
                                            {{ $review->report_count }} báo cáo
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
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
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relatedProduct)
            <div class="group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <a href="{{ route('products.show', $relatedProduct->id) }}" class="block relative h-48 overflow-hidden">
                    <img src="{{ $relatedProduct->primary_image ? $relatedProduct->primary_image->s3_url : '/placeholder.svg?height=400&width=400' }}" 
                         alt="{{ $relatedProduct->name }}" 
                         class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                    @if($relatedProduct->release_at && $relatedProduct->release_at->diffInDays(now()) <= 7)
                        <span class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">Mới</span>
                    @endif
                    
                    @if(isset($relatedProduct->applicable_discount_codes) && $relatedProduct->applicable_discount_codes->count() > 0)
                        <div class="absolute bottom-2 left-2 right-2 flex flex-col gap-1 z-10">
                            @foreach($relatedProduct->applicable_discount_codes as $discountCode)
                                @php
                                    $bgColor = 'bg-orange-500';
                                    $icon = 'fa-percent';
                                    
                                    if($discountCode->discount_type === 'fixed_amount') {
                                        $bgColor = 'bg-purple-500';
                                        $icon = 'fa-money-bill-wave';
                                    } elseif($discountCode->discount_type === 'free_shipping') {
                                        $bgColor = 'bg-blue-500';
                                        $icon = 'fa-shipping-fast';
                                    }
                                @endphp
                                <div class="inline-flex items-center {{ $bgColor }} bg-opacity-90 text-white text-xs px-2 py-1 rounded-full backdrop-blur-sm discount-pill">
                                    <i class="fas {{ $icon }} mr-1 text-xs"></i>
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
                </a>

                <div class="p-4">
                    <div class="flex items-center gap-1 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($relatedProduct->average_rating))
                                <i class="fas fa-star text-yellow-400"></i>
                            @elseif($i - 0.5 <= $relatedProduct->average_rating)
                                <i class="fas fa-star-half-alt text-yellow-400"></i>
                            @else
                                <i class="far fa-star text-yellow-400"></i>
                            @endif
                        @endfor
                        <span class="text-xs text-gray-500 ml-1">({{ $relatedProduct->reviews_count }})</span>
                    </div>

                    <a href="{{ route('products.show', $relatedProduct->id) }}">
                        <h3 class="font-medium text-lg mb-1 hover:text-orange-500 transition-colors line-clamp-1">
                            {{ $relatedProduct->name }}
                        </h3>
                    </a>

                    <p class="text-gray-500 text-sm mb-3 line-clamp-2">{{ $relatedProduct->short_description }}</p>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-lg">{{ number_format($relatedProduct->base_price, 0, ',', '.') }}đ</span>
                        </div>

                        <button class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md text-sm flex items-center transition-colors">
                            <i class="fas fa-shopping-cart h-4 w-4 mr-1"></i>
                            Thêm
                        </button>
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
</script>
<script src="{{ asset('js/Customer/Shop/shop.js') }}"></script>
@include('partials.customer.branch-check')
@endsection
