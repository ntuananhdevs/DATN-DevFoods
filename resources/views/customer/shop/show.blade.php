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
                <img src="{{ $product->images->first() ? Storage::url($product->images->first()->path) : '/placeholder.svg?height=600&width=600' }}" 
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
                    <img src="{{ Storage::url($image->path) }}" 
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

            <!-- Available branches -->
            <div class="bg-orange-50 p-4 rounded-lg">
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-map-marker-alt h-4 w-4 text-orange-500"></i>
                    <span class="font-medium">Có sẵn tại {{ $branches->count() }} chi nhánh</span>
                </div>
                <select class="w-full p-2.5 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white"
                        id="branch-select">
                    <option value="">Chọn chi nhánh</option>
                    @foreach($branches as $branch)
                        @php
                            // Tính tổng số lượng tồn kho của tất cả biến thể tại chi nhánh này
                            $totalStock = $branch->stocks()
                                ->whereHas('productVariant', function($query) use ($product) {
                                    $query->where('product_id', $product->id);
                                })
                                ->sum('stock_quantity');
                        @endphp
                        <option value="{{ $branch->id }}" 
                                data-address="{{ $branch->address }}"
                                data-stock="{{ $totalStock }}">
                            {{ $branch->name }} (Còn {{ number_format($totalStock, 0, ',', '.') }} sản phẩm)
                        </option>
                    @endforeach
                </select>
                <div class="mt-2 space-y-2 hidden" id="branch-info">
                    <p class="text-sm text-gray-600" id="branch-address"></p>
                    <div class="flex items-center gap-2 text-sm" id="stock-status">
                        <i class="fas"></i>
                        <span></span>
                    </div>
                </div>
            </div>

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
                                   {{ $loop->first ? 'checked' : '' }}>
                            <span class="px-4 py-2 rounded-md border cursor-pointer variant-label {{ $loop->first ? 'bg-orange-100 border-orange-500 text-orange-600' : '' }} hover:bg-gray-50">
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

            <!-- Quantity Selection -->
            <div class="flex items-center gap-4">
                <span class="font-medium">Số lượng:</span>
                <div class="flex items-center">
                    <button class="h-8 w-8 rounded-l-md border border-gray-300 flex items-center justify-center hover:bg-gray-100" 
                            id="decrease-quantity"
                            :disabled="!selectedBranch || parseInt($el.querySelector(`option[value='${selectedBranch}']`)?.dataset?.stock) === 0"
                            :class="{'opacity-50 cursor-not-allowed': !selectedBranch || parseInt($el.querySelector(`option[value='${selectedBranch}']`)?.dataset?.stock) === 0}">
                        <i class="fas fa-minus h-3 w-3"></i>
                    </button>
                    <div class="h-8 px-3 flex items-center justify-center border-y border-gray-300" id="quantity">1</div>
                    <button class="h-8 w-8 rounded-r-md border border-gray-300 flex items-center justify-center hover:bg-gray-100" 
                            id="increase-quantity"
                            :disabled="!selectedBranch || parseInt($el.querySelector(`option[value='${selectedBranch}']`)?.dataset?.stock) === 0"
                            :class="{'opacity-50 cursor-not-allowed': !selectedBranch || parseInt($el.querySelector(`option[value='${selectedBranch}']`)?.dataset?.stock) === 0}">
                        <i class="fas fa-plus h-3 w-3"></i>
                    </button>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button id="add-to-cart" 
                        class="w-full sm:flex-1 bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-md font-medium transition-colors flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="!selectedBranch || parseInt($el.querySelector(`option[value='${selectedBranch}']`)?.dataset?.stock) === 0"
                        @click="$store.cart.addItem({
                            id: {{ $product->id }},
                            variants: $store.variants.selectedVariants,
                            branch_id: selectedBranch,
                            quantity: parseInt(document.getElementById('quantity').textContent),
                            price: $store.variants.calculateTotalPrice()
                        })">
                    <i class="fas fa-shopping-cart h-5 w-5 mr-2"></i>
                    <span x-text="!selectedBranch ? 'Vui lòng chọn chi nhánh' : (parseInt($el.querySelector(`option[value='${selectedBranch}']`)?.dataset?.stock) === 0 ? 'Hết hàng' : 'Thêm vào giỏ hàng')"></span>
                </button>
                <button class="w-full sm:flex-1 border border-gray-300 hover:bg-gray-50 px-6 py-3 rounded-md font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="!selectedBranch || parseInt($el.querySelector(`option[value='${selectedBranch}']`)?.dataset?.stock) === 0">
                    Mua ngay
                </button>
                <div class="flex gap-3 justify-center sm:justify-start">
                    <button class="border border-gray-300 hover:bg-gray-50 h-11 w-11 rounded-md flex items-center justify-center">
                        <i class="far fa-heart h-5 w-5"></i>
                        <span class="sr-only">Yêu thích</span>
                    </button>
                    <button class="border border-gray-300 hover:bg-gray-50 h-11 w-11 rounded-md flex items-center justify-center">
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
                    <img src="{{ $relatedProduct->images->first() ? Storage::url($relatedProduct->images->first()->path) : '/placeholder.svg?height=400&width=400' }}" 
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
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Product price handling
        const basePrice = {{ $product->base_price }};
        const currentPriceElement = document.getElementById('current-price');
        const basePriceElement = document.getElementById('base-price');
        const variantInputs = document.querySelectorAll('.variant-input');
        const variantLabels = document.querySelectorAll('.variant-label');
        
        let selectedVariants = {};
        
        // Format price function
        function formatPrice(price) {
            return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
        }
        
        // Calculate total price
        function calculateTotalPrice() {
            console.log('Calculating total price...');
            let total = basePrice;
            let adjustments = [];
            
            // Get all selected variants
            variantInputs.forEach(input => {
                if (input.checked) {
                    const adjustment = parseFloat(input.dataset.priceAdjustment) || 0;
                    total += adjustment;
                    
                    adjustments.push({
                        attributeId: input.dataset.attributeId,
                        adjustment: adjustment
                    });
                    
                    console.log('Added adjustment:', {
                        attributeId: input.dataset.attributeId,
                        adjustment: adjustment
                    });
                }
            });
            
            console.log('Price adjustments:', adjustments);
            console.log('New total price:', total);
            
            // Update price display
            currentPriceElement.textContent = formatPrice(total);
            
            // Show/hide base price
            if (total > basePrice) {
                basePriceElement.classList.remove('hidden');
            } else {
                basePriceElement.classList.add('hidden');
            }
            
            return total;
        }
        
        // Handle variant selection
        variantInputs.forEach(input => {
            input.addEventListener('change', function() {
                console.log('Variant selected:', {
                    attributeId: this.dataset.attributeId,
                    adjustment: this.dataset.priceAdjustment
                });
                
                // Update selected variants
                selectedVariants[this.dataset.attributeId] = this.value;
                
                // Update variant styling
                const labels = document.querySelectorAll(`[name="attribute_${this.dataset.attributeId}"] + .variant-label`);
                labels.forEach(label => {
                    label.classList.remove('bg-orange-100', 'border-orange-500', 'text-orange-600');
                });
                this.nextElementSibling.classList.add('bg-orange-100', 'border-orange-500', 'text-orange-600');
                
                // Recalculate price
                calculateTotalPrice();
            });
        });
        
        // Initialize price calculation
        console.log('Initializing price calculation...');
        console.log('Base price:', basePrice);
        calculateTotalPrice();
        
        // Product image gallery
        const mainImage = document.getElementById('main-product-image');
        const thumbnails = document.querySelectorAll('.product-thumbnail');
        
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                // Update main image
                const imgSrc = this.querySelector('img').src;
                mainImage.src = imgSrc;
                
                // Update active thumbnail
                thumbnails.forEach(thumb => {
                    thumb.classList.remove('border-orange-500');
                    thumb.classList.add('border-transparent');
                });
                this.classList.remove('border-transparent');
                this.classList.add('border-orange-500');
            });
        });
        
        // Quantity controls
        const quantityElement = document.getElementById('quantity');
        const decreaseButton = document.getElementById('decrease-quantity');
        const increaseButton = document.getElementById('increase-quantity');
        let quantity = 1;
        
        decreaseButton.addEventListener('click', function() {
            if (quantity > 1) {
                quantity--;
                quantityElement.textContent = quantity;
            }
        });
        
        increaseButton.addEventListener('click', function() {
            quantity++;
            quantityElement.textContent = quantity;
        });
        
        // Tab functionality
        const tabButtons = document.querySelectorAll('[data-tab]');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                
                // Update active tab button
                tabButtons.forEach(btn => {
                    btn.classList.remove('border-orange-500', 'text-orange-500');
                    btn.classList.add('border-transparent');
                });
                this.classList.remove('border-transparent');
                this.classList.add('border-orange-500', 'text-orange-500');
                
                // Show active tab content
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                document.getElementById('content-' + tabId).classList.remove('hidden');
            });
        });
        
        // Branch selection
        const branchSelect = document.getElementById('branch-select');
        const branchInfo = document.getElementById('branch-info');
        const branchAddress = document.getElementById('branch-address');
        const stockStatus = document.getElementById('stock-status');

        function updateBranchInfo() {
            const selectedOption = branchSelect.options[branchSelect.selectedIndex];
            
            if (branchSelect.value) {
                const stock = parseInt(selectedOption.dataset.stock);
                
                // Hiển thị địa chỉ
                branchAddress.textContent = selectedOption.dataset.address;
                
                // Cập nhật trạng thái tồn kho
                const icon = stockStatus.querySelector('i');
                const text = stockStatus.querySelector('span');
                
                if (stock > 0) {
                    stockStatus.className = 'flex items-center gap-2 text-sm text-green-600';
                    icon.className = 'fas fa-check-circle';
                    text.textContent = 'Có sẵn để giao hàng';
                } else {
                    stockStatus.className = 'flex items-center gap-2 text-sm text-red-600';
                    icon.className = 'fas fa-times-circle';
                    text.textContent = 'Hết hàng';
                }
                
                branchInfo.classList.remove('hidden');
                console.log('Branch selected:', {
                    name: selectedOption.text,
                    address: selectedOption.dataset.address,
                    stock: stock
                });
            } else {
                branchInfo.classList.add('hidden');
            }
        }

        branchSelect.addEventListener('change', updateBranchInfo);
        
        // Add to cart functionality
        const addToCartButton = document.getElementById('add-to-cart');
        
        addToCartButton.addEventListener('click', function() {
            // Get selected options
            const variantValues = [];
            document.querySelectorAll('[name^="attribute_"]').forEach(input => {
                if (input.checked) {
                    variantValues.push(input.value);
                }
            });
            
            // Show toast notification
            showToast(`Đã thêm ${quantity} ${@json($product->name)} vào giỏ hàng`);
            
            // You would typically update cart count and send data to server here
        });
        
        // Simple toast notification function
        function showToast(message) {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300 opacity-0';
            toast.textContent = message;
            
            // Add to DOM
            document.body.appendChild(toast);
            
            // Show toast
            setTimeout(() => {
                toast.classList.remove('opacity-0');
                toast.classList.add('opacity-100');
            }, 10);
            
            // Hide and remove toast after 3 seconds
            setTimeout(() => {
                toast.classList.remove('opacity-100');
                toast.classList.add('opacity-0');
                
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }
    });
</script>
@endsection