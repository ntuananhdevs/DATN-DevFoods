@extends('layouts.customer.fullLayoutMaster')

@section('title', $product->name)

@section('content')
<style>
    .container {
      max-width: 1280px;
      margin: 0 auto;
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

            <p class="text-gray-600">{{ $product->short_description }}</p>

            <!-- Get selected branch information for JS -->
            @php
                $selectedBranchId = session('selected_branch');
                $isAvailable = false;
                
                if ($selectedBranchId) {
                    $selectedBranch = $branches->where('id', $selectedBranchId)->first();
                    if ($selectedBranch) {
                        $variantCount = $selectedBranch->stocks()
                            ->whereHas('productVariant', function($query) use ($product) {
                                $query->where('product_id', $product->id);
                            })
                            ->where('stock_quantity', '>', 0)
                            ->distinct('product_variant_id')
                            ->count('product_variant_id');
                        
                        $isAvailable = $variantCount > 0;
                    }
                }
            @endphp
            
            <!-- Hidden branch_id input for JS -->
            <input type="hidden" id="branch-select" value="{{ $selectedBranchId }}">
            
            @if($selectedBranchId && !$isAvailable)
                <div class="p-3 mb-4 bg-red-50 rounded-md text-red-700 text-sm border border-red-200">
                    <p>Sản phẩm hiện đang hết hàng tại chi nhánh của bạn. Vui lòng chọn chi nhánh khác.</p>
                </div>
            @endif

            <!-- Product variants -->
            <div class="space-y-4" id="variants-container">
                @foreach($variantAttributes as $attribute)
                <div>
                    <h3 class="font-medium mb-2">{{ $attribute->name }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($attribute->values as $value)
                        <label class="relative flex items-center">
                            <input type="radio" 
                                   name="attribute_{{ $attribute->id }}" 
                                   value="{{ $value->id }}" 
                                   data-attribute-id="{{ $attribute->id }}"
                                   data-price-adjustment="{{ $value->price_adjustment }}"
                                   class="sr-only variant-input"
                                   {{ $loop->first ? 'checked' : '' }}
                                   {{ $selectedBranch && !$isAvailable ? 'disabled' : '' }}>
                            <span class="px-4 py-2 rounded-md border cursor-pointer variant-label {{ $loop->first ? 'bg-orange-100 border-orange-500 text-orange-600' : '' }} hover:bg-gray-50 {{ $selectedBranch && !$isAvailable ? 'opacity-50 cursor-not-allowed' : '' }}">
                                {{ $value->value }}
                                @if($value->price_adjustment != 0)
                                    <span class="text-sm ml-1 {{ $value->price_adjustment > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $value->price_adjustment > 0 ? '+' : '' }}{{ number_format($value->price_adjustment, 0, ',', '.') }}đ
                                    </span>
                                @endif
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Toppings Section -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="font-medium">Toppings</h3>
                    <span class="text-sm text-gray-500">Chọn nhiều</span>
                </div>
                <div class="relative">
                    <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-orange-200 scrollbar-track-gray-100 hover:scrollbar-thumb-orange-300">
                        @foreach($product->toppings as $topping)
                        <label class="relative flex-shrink-0 w-24 cursor-pointer group {{ $selectedBranch && !$isAvailable ? 'opacity-50 cursor-not-allowed' : '' }}">
                            <input type="checkbox" 
                                   name="toppings[]" 
                                   value="{{ $topping->id }}"
                                   class="sr-only topping-input"
                                   data-price="{{ $topping->price }}"
                                   {{ $selectedBranch && !$isAvailable ? 'disabled' : '' }}>
                            <div class="relative aspect-square rounded-lg overflow-hidden border group-hover:border-orange-500 transition-colors">
                                <img src="{{ $topping->image_url }}" 
                                     alt="{{ $topping->name }}" 
                                     class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity"></div>
                                <div class="absolute top-1 right-1 w-4 h-4 border-2 border-white rounded-full bg-white/50 backdrop-blur-sm">
                                    <div class="w-full h-full rounded-full bg-orange-500 scale-0 group-hover:scale-100 transition-transform duration-200"></div>
                                </div>
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

            <!-- Quantity Selection -->
            <div class="flex items-center gap-4">
                <span class="font-medium">Số lượng:</span>
                <div class="flex items-center">
                    <button class="h-8 w-8 rounded-l-md border border-gray-300 flex items-center justify-center hover:bg-gray-100 {{ $selectedBranch && !$isAvailable ? 'opacity-50 cursor-not-allowed' : '' }}" 
                            id="decrease-quantity"
                            {{ $selectedBranch && !$isAvailable ? 'disabled' : '' }}>
                        <i class="fas fa-minus h-3 w-3"></i>
                    </button>
                    <div class="h-8 px-3 flex items-center justify-center border-y border-gray-300" id="quantity">1</div>
                    <button class="h-8 w-8 rounded-r-md border border-gray-300 flex items-center justify-center hover:bg-gray-100 {{ $selectedBranch && !$isAvailable ? 'opacity-50 cursor-not-allowed' : '' }}" 
                            id="increase-quantity"
                            {{ $selectedBranch && !$isAvailable ? 'disabled' : '' }}>
                        <i class="fas fa-plus h-3 w-3"></i>
                    </button>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button id="add-to-cart" 
                        class="w-full sm:flex-1 bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-md font-medium transition-colors flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ $selectedBranch && !$isAvailable ? 'disabled' : '' }}>
                    <i class="fas fa-shopping-cart h-5 w-5 mr-2"></i>
                    <span>{{ $selectedBranch && !$isAvailable ? 'Hết hàng' : 'Thêm vào giỏ hàng' }}</span>
                </button>
                <button class="w-full sm:flex-1 border border-gray-300 hover:bg-gray-50 px-6 py-3 rounded-md font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ $selectedBranch && !$isAvailable ? 'disabled' : '' }}>
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
    document.addEventListener("DOMContentLoaded", function() {
        // Show toast function
        window.showToast = function(message, type = 'info') {
            if (typeof showNotification === 'function') {
                const titles = {
                    success: 'Thành công!',
                    error: 'Lỗi!',
                    warning: 'Cảnh báo!', 
                    info: 'Thông báo!'
                };
                showNotification(type, titles[type], message);
            } else {
                alert(message);
            }
        };
        
        // Product image gallery
        const mainImage = document.getElementById('main-product-image');
        const thumbnails = document.querySelectorAll('.product-thumbnail');
        
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function() {
                // Update main image
                mainImage.src = this.querySelector('img').src;
                
                // Update active state
                thumbnails.forEach(t => t.classList.remove('border-orange-500'));
                this.classList.add('border-orange-500');
            });
        });
        
        // Price calculation
        const basePrice = {{ $product->base_price }};
        const basePriceDisplay = document.getElementById('base-price');
        const currentPriceDisplay = document.getElementById('current-price');
        const variantInputs = document.querySelectorAll('.variant-input');
        const toppingInputs = document.querySelectorAll('.topping-input');
        const quantityDisplay = document.getElementById('quantity');
        let quantity = 1;
        let totalPrice = basePrice;
        
        function updatePrice() {
            // Calculate variant price adjustments
            let variantAdjustment = 0;
            document.querySelectorAll('.variant-input:checked').forEach(input => {
                variantAdjustment += parseFloat(input.dataset.priceAdjustment || 0);
            });
            
            // Calculate topping price
            let toppingPrice = 0;
            document.querySelectorAll('.topping-input:checked').forEach(input => {
                toppingPrice += parseFloat(input.dataset.price || 0);
            });
            
            // Single item price (variant price + toppings)
            const singleItemPrice = basePrice + variantAdjustment;
            const totalItemPrice = singleItemPrice + toppingPrice;
            
            // Apply quantity
            totalPrice = totalItemPrice * quantity;
            
            // Update display
            if (variantAdjustment !== 0 || toppingPrice !== 0) {
                basePriceDisplay.textContent = `${basePrice.toLocaleString('vi-VN')}đ`;
                basePriceDisplay.classList.remove('hidden');
            } else {
                basePriceDisplay.classList.add('hidden');
            }
            
            currentPriceDisplay.textContent = `${totalPrice.toLocaleString('vi-VN')}đ`;
            
            // Highlight current price if different from base
            if (totalPrice !== basePrice) {
                currentPriceDisplay.classList.add('text-green-500');
                currentPriceDisplay.classList.remove('text-orange-500');
            } else {
                currentPriceDisplay.classList.add('text-orange-500');
                currentPriceDisplay.classList.remove('text-green-500');
            }
        }
        
        // Variant selection
        variantInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Update visual state of labels
                const attributeId = this.dataset.attributeId;
                document.querySelectorAll(`[data-attribute-id="${attributeId}"] + .variant-label`).forEach(label => {
                    label.classList.remove('bg-orange-100', 'border-orange-500', 'text-orange-600');
                });
                
                this.nextElementSibling.classList.add('bg-orange-100', 'border-orange-500', 'text-orange-600');
                updatePrice();
            });
        });
        
        // Topping selection
        toppingInputs.forEach(input => {
            input.addEventListener('change', function() {
                const toppingContainer = this.closest('label');
                if (this.checked) {
                    toppingContainer.classList.add('topping-checked');
                    toppingContainer.querySelector('.w-full').classList.add('scale-110');
                    toppingContainer.querySelector('.bg-orange-500').classList.add('scale-100');
                } else {
                    toppingContainer.classList.remove('topping-checked');
                    toppingContainer.querySelector('.w-full').classList.remove('scale-110');
                    toppingContainer.querySelector('.bg-orange-500').classList.remove('scale-100');
                }
                updatePrice();
            });
        });
        
        // Quantity controls
        const decreaseBtn = document.getElementById('decrease-quantity');
        const increaseBtn = document.getElementById('increase-quantity');
        
        decreaseBtn.addEventListener('click', function() {
            if (quantity > 1) {
                quantity--;
                quantityDisplay.textContent = quantity;
                updatePrice();
            }
        });
        
        increaseBtn.addEventListener('click', function() {
            quantity++;
            quantityDisplay.textContent = quantity;
            updatePrice();
        });
        
        // Add to cart functionality
        const addToCartBtn = document.getElementById('add-to-cart');
        
        addToCartBtn.addEventListener('click', function() {
            // Get selected branch
            const branchId = document.getElementById('branch-select').value;
            if (!branchId) {
                showToast('Vui lòng chọn chi nhánh trước khi thêm vào giỏ hàng', 'warning');
                return;
            }
            
            // Get all selected variant values
            const selectedVariantValueIds = [];
            const variantGroups = document.querySelectorAll('#variants-container > div');
            
            variantGroups.forEach(group => {
                const checkedInput = group.querySelector('input:checked');
                if (checkedInput) {
                    selectedVariantValueIds.push(parseInt(checkedInput.value));
                }
            });
            
            // Get selected toppings
            const selectedToppings = Array.from(document.querySelectorAll('.topping-input:checked'))
                .map(input => parseInt(input.value));
            
            // API call to get actual product variant ID
            console.log('Selected variant values:', selectedVariantValueIds);
            
            // First, fetch the actual product variant ID based on the selected variant values
            fetch('/api/customer/products/get-variant', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: {{ $product->id }},
                    variant_values: selectedVariantValueIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const variantId = data.variant_id;
                    
                    // Now add to cart with the retrieved variant ID
                    return fetch('/api/customer/cart/add', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            product_id: {{ $product->id }},
                            variant_id: variantId,
                            branch_id: branchId,
                            quantity: quantity,
                            toppings: selectedToppings
                        })
                    });
                } else {
                    throw new Error(data.message || 'Không tìm thấy biến thể sản phẩm');
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Sản phẩm đã được thêm vào giỏ hàng!', 'success');
                    
                    // Update cart counter if needed
                    if (typeof window.updateCartCount === 'function' && data.count) {
                        window.updateCartCount(data.count);
                    }
                } else {
                    showToast(data.message || 'Có lỗi khi thêm sản phẩm vào giỏ hàng', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(error.message || 'Có lỗi xảy ra', 'error');
            });
        });

        // Initialize
        updatePrice();
    });
</script>
@endsection