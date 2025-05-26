@extends('layouts.customer.fullLayoutMaster')

@section('title', $product->name)

@section('content')
<style>
    .container {
      max-width: 1280px;
      margin: 0 auto;
    }
    .variant-option {
        transition: all 0.2s ease;
    }
    .variant-option:hover {
        background-color: #f3f4f6;
    }
    .variant-option.selected {
        background-color: #f97316;
        color: white;
        border-color: #ea580c;
    }
    .variant-option.unavailable {
        opacity: 0.5;
        cursor: not-allowed;
        text-decoration: line-through;
        display: none !important;
    }
</style>
<div class="container mx-auto px-4 py-8">
    <div class="grid md:grid-cols-2 gap-8 mb-12">
        <div class="space-y-4">
            <div class="relative h-[300px] sm:h-[400px] rounded-lg overflow-hidden border">
                @php
                    $primaryImage = $product->images->where('is_primary', true)->first();
                    $imageUrl = $primaryImage ? asset('storage/' . $primaryImage->img) : '/placeholder.svg?height=600&width=600';
                @endphp
                <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="object-cover w-full h-full" id="main-product-image">
                @if($product->created_at->diffInDays() <= 7)
                    <span class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">Mới</span>
                @endif
            </div>

            <div class="flex gap-2 overflow-x-auto pb-2">
                @foreach($product->images as $image)
                <button class="relative w-20 h-20 rounded border-2 {{ $image->is_primary ? 'border-orange-500' : 'border-transparent' }} overflow-hidden flex-shrink-0 product-thumbnail">
                    <img src="{{ asset('storage/' . $image->img) }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
                </button>
                @endforeach
            </div>
        </div>

        <div class="space-y-6">
            <h1 class="text-2xl sm:text-3xl font-bold">{{ $product->name }}</h1>

            @if($product->reviews->count() > 0)
            <div class="flex items-center gap-2">
                <div class="flex items-center">
                    @php
                        $averageRating = $product->reviews->avg('rating');
                    @endphp
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($averageRating))
                            <i class="fas fa-star text-yellow-400"></i>
                        @elseif($i <= ceil($averageRating))
                            <i class="fas fa-star-half-alt text-yellow-400"></i>
                        @else
                            <i class="far fa-star text-yellow-400"></i>
                        @endif
                    @endfor
                </div>
                <span class="text-gray-500">({{ $product->reviews->count() }} đánh giá)</span>
            </div>
            @endif

            <div class="flex items-center gap-3">
                <span class="text-3xl font-bold text-orange-500" id="current-price">{{ number_format($product->base_price, 0, ',', '.') }}₫</span>
            </div>

            <p class="text-gray-600">{{ $product->short_description ?? $product->description }}</p>

            @if($product->variants->count() > 0)
            <div class="space-y-4" id="variant-selection">
                @php
                    // Nhóm các giá trị biến thể theo thuộc tính
                    $attributeGroups = [];
                    $variantData = [];
                    
                    foreach($product->variants as $variant) {
                        $variantCombination = [];
                        $totalPriceAdjustment = 0;
                        foreach($variant->variantValues as $variantValue) {
                            $attributeName = $variantValue->attribute->name;
                            $variantCombination[$variantValue->attribute->id] = $variantValue->id;
                            $totalPriceAdjustment += $variantValue->price_adjustment;
                            
                            if (!isset($attributeGroups[$attributeName])) {
                                $attributeGroups[$attributeName] = [
                                    'attribute_id' => $variantValue->attribute->id,
                                    'values' => []
                                ];
                            }
                            
                            // Chỉ thêm giá trị nếu chưa tồn tại trong nhóm
                            $exists = false;
                            foreach($attributeGroups[$attributeName]['values'] as $existingValue) {
                                if ($existingValue->id === $variantValue->id) {
                                    $exists = true;
                                    break;
                                }
                            }
                            if (!$exists) {
                                $attributeGroups[$attributeName]['values'][] = $variantValue;
                            }
                        }
                        
                        // Lưu thông tin variant để sử dụng trong JavaScript
                        $variantData[] = [
                            'id' => $variant->id,
                            'combination' => $variantCombination,
                            'price' => $product->base_price + $totalPriceAdjustment,
                            'image' => $variant->image ? asset('storage/' . $variant->image) : null
                        ];
                    }
                @endphp
                
                @foreach($attributeGroups as $attributeName => $attributeInfo)
                <div>
                    <h3 class="font-medium mb-2">{{ $attributeName }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($attributeInfo['values'] as $value)
                        <button 
                            type="button" 
                            class="variant-option px-3 py-1.5 border rounded-md cursor-pointer transition-all duration-200"
                            data-attribute-id="{{ $value->attribute->id }}"
                            data-value-id="{{ $value->id }}"
                            data-price-adjustment="{{ $value->price_adjustment }}"
                            onclick="selectVariantValue({{ $value->attribute->id }}, {{ $value->id }}, this)"
                        >
                            {{ $value->value }}
                            @if($value->price_adjustment > 0)
                                (+{{ number_format($value->price_adjustment, 0, ',', '.') }}₫)
                            @endif
                        </button>
                        @endforeach
                    </div>
                </div>
                @endforeach
                
                <div id="variant-warning" class="text-red-500 text-sm hidden">
                    Vui lòng chọn đầy đủ các thông tin sản phẩm.
                </div>
            </div>
            @endif

            @if($product->toppings->count() > 0)
            <div>
                <h3 class="font-medium mb-2">Topping</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($product->toppings as $topping)
                    <label class="flex px-3 py-1.5 border rounded-md cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="checkbox" name="toppings[]" value="{{ $topping->id }}" class="sr-only topping-checkbox" onchange="updateTotalPrice()">
                        <span class="topping-label">{{ $topping->name }} (+{{ number_format($topping->price, 0, ',', '.') }}₫)</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="space-y-4 py-4 border-y">
                <div class="flex items-center gap-4">
                    <span class="font-medium">Số lượng:</span>
                    <div class="flex items-center">
                        <button class="h-8 w-8 rounded-r-none border border-gray-300 flex items-center justify-center hover:bg-gray-100" onclick="updateQuantity(-1)">
                            <i class="fas fa-minus h-3 w-3"></i>
                        </button>
                        <input type="number" id="quantity" value="1" min="1" class="h-8 w-16 border-y border-gray-300 text-center" readonly>
                        <button class="h-8 w-8 rounded-l-none border border-gray-300 flex items-center justify-center hover:bg-gray-100" onclick="updateQuantity(1)">
                            <i class="fas fa-plus h-3 w-3"></i>
                        </button>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button onclick="addToCart()" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-md font-medium transition-colors flex-1 flex items-center justify-center">
                        <i class="fas fa-shopping-cart h-5 w-5 mr-2"></i>
                        Thêm vào giỏ hàng
                    </button>
                    <button onclick="buyNow()" class="border border-gray-300 hover:bg-gray-50 px-6 py-3 rounded-md font-medium transition-colors flex-1">
                        Mua ngay
                    </button>
                    <button onclick="toggleFavorite()" class="border border-gray-300 hover:bg-gray-50 h-11 w-11 rounded-md flex items-center justify-center">
                        <i class="far fa-heart h-5 w-5"></i>
                    </button>
                    <button onclick="shareProduct()" class="border border-gray-300 hover:bg-gray-50 h-11 w-11 rounded-md flex items-center justify-center">
                        <i class="fas fa-share-alt h-5 w-5"></i>
                    </button>
                </div>
            </div>

            <div class="border-b">
                <div class="flex border-b">
                    <button class="px-4 py-2 font-medium border-b-2 border-orange-500 text-orange-500" onclick="switchTab('description')">Mô tả</button>
                    <button class="px-4 py-2 font-medium border-b-2 border-transparent" onclick="switchTab('reviews')">Đánh giá</button>
                </div>
                
                <div class="py-4" id="tab-description">
                    <p class="text-gray-600">{{ $product->description }}</p>
                </div>
                
                <div class="py-4 hidden" id="tab-reviews">
                    @if($product->reviews->count() > 0)
                        <div class="space-y-4">
                            @foreach($product->reviews as $review)
                            <div class="border-b pb-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="fas fa-star text-yellow-400"></i>
                                            @else
                                                <i class="far fa-star text-yellow-400"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="font-medium">{{ $review->user->name }}</span>
                                    <span class="text-gray-500 text-sm">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-600">{{ $review->content }}</p>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">Chưa có đánh giá nào cho sản phẩm này.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($relatedProducts->count() > 0)
    <div class="mb-12">
        <h2 class="text-2xl font-bold mb-6">Sản Phẩm Liên Quan</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relatedProduct)
            <div class="group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <a href="{{ route('products.show', $relatedProduct->id) }}" class="block relative h-48 overflow-hidden">
                    @php
                        $primaryImage = $relatedProduct->images->where('is_primary', true)->first();
                        $imageUrl = $primaryImage ? asset('storage/' . $primaryImage->img) : '/placeholder.svg?height=400&width=400';
                    @endphp
                    <img src="{{ $imageUrl }}" alt="{{ $relatedProduct->name }}" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                    @if($relatedProduct->created_at->diffInDays() <= 7)
                        <span class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">Mới</span>
                    @endif
                </a>

                <div class="p-4">
                    @if($relatedProduct->reviews->count() > 0)
                    <div class="flex items-center gap-1 mb-2">
                        <div class="flex items-center">
                            @php
                                $averageRating = $relatedProduct->reviews->avg('rating');
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($averageRating))
                                    <i class="fas fa-star text-yellow-400"></i>
                                @elseif($i <= ceil($averageRating))
                                    <i class="fas fa-star-half-alt text-yellow-400"></i>
                                @else
                                    <i class="far fa-star text-yellow-400"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="text-xs text-gray-500 ml-1">({{ $relatedProduct->reviews->count() }})</span>
                    </div>
                    @endif

                    <a href="{{ route('products.show', $relatedProduct->id) }}">
                        <h3 class="font-medium text-lg mb-1 hover:text-orange-500 transition-colors line-clamp-1">
                            {{ $relatedProduct->name }}
                        </h3>
                    </a>

                    <p class="text-gray-500 text-sm mb-3 line-clamp-2">{{ $relatedProduct->short_description ?? Str::limit($relatedProduct->description, 80) }}</p>

                    <div class="flex items-center justify-between">
                        <span class="font-bold text-lg">{{ number_format($relatedProduct->base_price, 0, ',', '.') }}₫</span>

                        <button onclick="addToCart({{ $relatedProduct->id }})" class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md text-sm flex items-center transition-colors">
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
// Dữ liệu variant từ PHP
const variantData = @json($variantData ?? []);
const basePrice = {{ $product->base_price }};
const toppings = @json($product->toppings);

// Lưu trữ lựa chọn hiện tại
let selectedVariantValues = {};
let currentVariantId = null;

function updateQuantity(change) {
    const quantityInput = document.getElementById('quantity');
    const newValue = Math.max(1, parseInt(quantityInput.value) + change);
    quantityInput.value = newValue;
}

function switchTab(tabName) {
    const tabs = ['description', 'reviews'];
    tabs.forEach(tab => {
        const tabButton = document.querySelector(`[onclick="switchTab('${tab}')"]`);
        const tabContent = document.getElementById(`tab-${tab}`);
        
        if (tab === tabName) {
            tabButton.classList.add('border-orange-500', 'text-orange-500');
            tabContent.classList.remove('hidden');
        } else {
            tabButton.classList.remove('border-orange-500', 'text-orange-500');
            tabContent.classList.add('hidden');
        }
    });
}

function selectVariantValue(attributeId, valueId, element) {
    // Kiểm tra xem option này đã được chọn chưa
    if (element.classList.contains('selected')) {
        // Nếu đã chọn thì hủy chọn
        element.classList.remove('selected');
        delete selectedVariantValues[attributeId];
    } else {
        // Bỏ chọn các option khác của cùng thuộc tính
        document.querySelectorAll(`[data-attribute-id="${attributeId}"]`).forEach(option => {
            option.classList.remove('selected');
        });
        
        // Chọn option hiện tại
        element.classList.add('selected');
        selectedVariantValues[attributeId] = valueId;
    }
    
    // Kiểm tra xem đã chọn đủ thuộc tính chưa và tìm variant phù hợp
    updateVariantSelection();
    updateAvailableOptions();
}

function updateVariantSelection() {
    // Nếu không có lựa chọn nào, reset về trạng thái ban đầu
    if (Object.keys(selectedVariantValues).length === 0) {
        currentVariantId = null;
        updateTotalPrice();
        
        // Reset hình ảnh về ảnh chính
        const primaryImage = document.querySelector('.product-thumbnail.border-orange-500 img');
        if (primaryImage) {
            document.getElementById('main-product-image').src = primaryImage.src;
        }
        
        document.getElementById('variant-warning').classList.add('hidden');
        return;
    }
    
    // Tìm variant phù hợp với lựa chọn hiện tại
    const matchingVariant = variantData.find(variant => {
        return Object.keys(selectedVariantValues).every(attributeId => {
            return variant.combination[attributeId] == selectedVariantValues[attributeId];
        });
    });
    
    if (matchingVariant && Object.keys(selectedVariantValues).length === getUniqueAttributeCount()) {
        currentVariantId = matchingVariant.id;
        
        // Cập nhật giá
        updateTotalPrice();
        
        // Cập nhật hình ảnh nếu có
        if (matchingVariant.image) {
            const mainImage = document.getElementById('main-product-image');
            mainImage.src = matchingVariant.image;
        }
        
        // Ẩn cảnh báo
        document.getElementById('variant-warning').classList.add('hidden');
    } else {
        currentVariantId = null;
        // Hiện cảnh báo nếu cần (chỉ khi đã chọn một số thuộc tính nhưng chưa đủ)
        const attributeCount = Object.keys(selectedVariantValues).length;
        if (attributeCount > 0 && attributeCount < getUniqueAttributeCount()) {
            document.getElementById('variant-warning').classList.remove('hidden');
        } else {
            document.getElementById('variant-warning').classList.add('hidden');
        }
    }
}

function updateAvailableOptions() {
    // Tìm các option có thể chọn dựa trên lựa chọn hiện tại
    const allOptions = document.querySelectorAll('.variant-option');
    
    allOptions.forEach(option => {
        const attributeId = option.dataset.attributeId;
        const valueId = option.dataset.valueId;
        
        // Nếu option này đã được chọn, luôn hiển thị và enable
        if (selectedVariantValues[attributeId] == valueId) {
            option.classList.remove('unavailable');
            option.style.display = 'block';
            return;
        }
        
        // Tạo một bản sao của lựa chọn hiện tại và thêm option này
        const testSelection = {...selectedVariantValues, [attributeId]: valueId};
        
        // Kiểm tra xem có variant nào phù hợp không
        const hasMatchingVariant = variantData.some(variant => {
            return Object.keys(testSelection).every(attr => {
                return variant.combination[attr] == testSelection[attr];
            });
        });
        
        if (hasMatchingVariant) {
            option.classList.remove('unavailable');
            option.style.display = 'block';
        } else {
            // Nếu không có lựa chọn nào được chọn, hiển thị tất cả
            if (Object.keys(selectedVariantValues).length === 0) {
                option.classList.remove('unavailable');
                option.style.display = 'block';
            } else {
                // Ẩn hoàn toàn các option không có sẵn
                option.style.display = 'none';
            }
        }
    });
}

function getUniqueAttributeCount() {
    const attributes = new Set();
    document.querySelectorAll('[data-attribute-id]').forEach(option => {
        attributes.add(option.dataset.attributeId);
    });
    return attributes.size;
}

function updateTotalPrice() {
    let total = basePrice;
    
    // Cộng thêm giá từ variant đã chọn
    if (currentVariantId) {
        const selectedVariant = variantData.find(v => v.id === currentVariantId);
        if (selectedVariant) {
            total = selectedVariant.price;
        }
    }
    
    // Cộng thêm giá từ toppings
    const selectedToppings = document.querySelectorAll('.topping-checkbox:checked');
    selectedToppings.forEach(topping => {
        const toppingId = parseInt(topping.value);
        const toppingData = toppings.find(t => t.id === toppingId);
        if (toppingData) {
            total += toppingData.price;
        }
    });
    
    // Cập nhật hiển thị giá
    const quantity = parseInt(document.getElementById('quantity').value);
    document.getElementById('current-price').textContent = 
        new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' })
            .format(total * quantity)
            .replace('₫', '');
}

function addToCart() {
    const quantity = document.getElementById('quantity').value;
    const selectedToppings = [];
    
    // Kiểm tra xem đã chọn variant đầy đủ chưa (nếu có variant)
    if (variantData.length > 0 && !currentVariantId) {
        document.getElementById('variant-warning').classList.remove('hidden');
        document.getElementById('variant-warning').textContent = 'Vui lòng chọn đầy đủ các thuộc tính sản phẩm.';
        return;
    }
    
    // Collect selected toppings
    document.querySelectorAll('.topping-checkbox:checked').forEach(checkbox => {
        selectedToppings.push(checkbox.value);
    });
    
    const cartData = {
        productId: {{ $product->id }},
        variantId: currentVariantId,
        quantity: parseInt(quantity),
        toppings: selectedToppings
    };
    
    console.log('Adding to cart:', cartData);
    
    // TODO: Implement actual cart functionality here
    // Có thể gửi AJAX request để thêm vào giỏ hàng
    
    // Hiển thị thông báo thành công (tạm thời)
    alert('Đã thêm sản phẩm vào giỏ hàng!');
}

function buyNow() {
    addToCart();
    // TODO: Redirect to checkout
    // window.location.href = '/checkout';
}

function toggleFavorite() {
    console.log('Toggle favorite for product:', {{ $product->id }});
    // TODO: Implement favorite functionality here
}

function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $product->name }}',
            text: '{{ $product->short_description }}',
            url: window.location.href
        });
    }
}

// Image gallery
document.querySelectorAll('.product-thumbnail').forEach(thumbnail => {
    thumbnail.addEventListener('click', function() {
        const mainImage = document.getElementById('main-product-image');
        const newSrc = this.querySelector('img').src;
        mainImage.src = newSrc;
        
        // Update active thumbnail
        document.querySelectorAll('.product-thumbnail').forEach(thumb => {
            thumb.classList.remove('border-orange-500');
            thumb.classList.add('border-transparent');
        });
        this.classList.remove('border-transparent');
        this.classList.add('border-orange-500');
    });
});

// Update topping selection styles
document.querySelectorAll('.topping-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const label = this.closest('label');
        if (this.checked) {
            label.classList.add('bg-orange-500', 'text-white', 'border-orange-600');
            label.classList.remove('hover:bg-gray-50');
        } else {
            label.classList.remove('bg-orange-500', 'text-white', 'border-orange-600');
            label.classList.add('hover:bg-gray-50');
        }
        updateTotalPrice();
    });
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateAvailableOptions();
});

function toggleFavorite() {
    const productId = {{ $product->id }};
    const variantId = currentVariantId; // Từ biến đã định nghĩa trong script
    const heartIcon = document.querySelector('.fa-heart');
    
    fetch('/whishlist', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({
            product_id: productId,
            product_variant_id: variantId,
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.message.includes('thêm')) {
            heartIcon.classList.remove('far');
            heartIcon.classList.add('fas', 'text-red-500');
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra, vui lòng thử lại!', 'error');
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white transform translate-x-full transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>
@endsection