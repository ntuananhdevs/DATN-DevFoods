@php
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Danh Sách Yêu Thích')

@section('content')
<x-customer-container>
<style>
    .wishlist-item {
        transition: all 0.3s ease;
    }
    
    .wishlist-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .heart-icon {
        transition: all 0.3s ease;
    }
    
    .heart-icon.active {
        color: #ef4444;
        transform: scale(1.1);
    }
    
    .empty-wishlist {
        min-height: 400px;
    }
    
    .product-actions {
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.3s ease;
    }
    
    .wishlist-item:hover .product-actions {
        opacity: 1;
        transform: translateY(0);
    }
    
    .filter-btn {
        transition: all 0.3s ease;
    }
    
    .filter-btn.active {
        background-color: #f97316;
        color: white;
        transform: translateY(-1px);
    }
    
    .wishlist-stats {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    }
    
    .animate-bounce-slow {
        animation: bounce 2s infinite;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }
</style>

<!-- Breadcrumb -->
<div class="bg-gray-50 py-4">
    <div class="container mx-auto px-4">
        <nav class="flex items-center space-x-2 text-sm">
            <a href="/" class="text-gray-500 hover:text-orange-500 transition-colors">Trang chủ</a>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            <span class="text-gray-900 font-medium">Danh sách yêu thích</span>
        </nav>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-100 rounded-full mb-4">
            <i class="fas fa-heart text-2xl text-orange-500"></i>
        </div>
        <h1 class="text-3xl md:text-4xl font-bold mb-2">Danh Sách Yêu Thích</h1>
        <p class="text-gray-600 max-w-md mx-auto">Những món ăn bạn đã lưu để thưởng thức sau</p>
    </div>

    <!-- Wishlist Stats -->
    <div class="wishlist-stats rounded-xl p-6 mb-8 text-white">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold mb-1" id="total-items">{{ $stats['total_items'] ?? 0 }}</div>
                <div class="text-orange-100">Món yêu thích</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold mb-1" id="total-value">{{ $stats['total_value'] ?? '0đ' }}</div>
                <div class="text-orange-100">Tổng giá trị</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold mb-1" id="categories-count">{{ $stats['categories_count'] ?? 0 }}</div>
                <div class="text-orange-100">Danh mục</div>
            </div>
        </div>
    </div>

    <!-- Filter and Sort -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div class="flex flex-wrap gap-2">
            <button class="filter-btn active px-4 py-2 rounded-full border border-gray-300 text-sm font-medium" data-category="all">
                Tất cả
            </button>
            <button class="filter-btn px-4 py-2 rounded-full border border-gray-300 text-sm font-medium" data-category="burger">
                Burger
            </button>
            <button class="filter-btn px-4 py-2 rounded-full border border-gray-300 text-sm font-medium" data-category="pizza">
                Pizza
            </button>
            <button class="filter-btn px-4 py-2 rounded-full border border-gray-300 text-sm font-medium" data-category="chicken">
                Gà rán
            </button>
            <button class="filter-btn px-4 py-2 rounded-full border border-gray-300 text-sm font-medium" data-category="drinks">
                Đồ uống
            </button>
        </div>
        
        <div class="flex items-center gap-4">
            <select class="border border-gray-300 rounded-md px-3 py-2 text-sm" id="sort-select">
                <option value="newest">Mới nhất</option>
                <option value="price-low">Giá thấp đến cao</option>
                <option value="price-high">Giá cao đến thấp</option>
                <option value="name">Tên A-Z</option>
            </select>
            
            <div class="flex border border-gray-300 rounded-md overflow-hidden">
                <button class="view-btn active px-3 py-2 text-sm" data-view="grid">
                    <i class="fas fa-th-large"></i>
                </button>
                <button class="view-btn px-3 py-2 text-sm border-l border-gray-300" data-view="list">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Wishlist Items -->
    <div id="wishlist-container">
        <div id="grid-view" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($wishlistItems as $item)
                @php
                    if ($item->product) {
                        $isCombo = $item->product instanceof \App\Models\Combo;
                        $image = $isCombo
                            ? ($item->product->image_url ?? asset('images/default-combo.png'))
                            : (
                                $item->product->images->where('is_primary', true)->first()
                                    ? Storage::disk('s3')->url($item->product->images->where('is_primary', true)->first()->img)
                                    : '/placeholder.svg?height=400&width=400'
                            );
                        $name = $item->product->name;
                        $slug = $item->product->slug;
                        $url = $isCombo ? route('combos.show', $slug) : route('products.show', $slug);
                    } elseif ($item->combo) {
                        $image = $item->combo->image_url ?? asset('images/default-combo.png');
                        $name = $item->combo->name;
                        $slug = $item->combo->slug;
                        $url = route('combos.show', $slug);
                    } else {
                        $image = '/placeholder.svg?height=400&width=400';
                        $name = 'Không xác định';
                        $url = '#';
                    }
                @endphp
                <div class="wishlist-item bg-white rounded-lg overflow-hidden shadow-md" 
                    data-category="{{ $item->product->category->name ?? ($item->combo ? 'Combo' : 'other') }}" 
                    data-price="{{ $item->product->base_price ?? $item->combo->price ?? 0 }}"
                    data-name="{{ $name }}"
                    data-date="{{ $item->added_at ? $item->added_at->toDateString() : '' }}"
                    data-product-id="{{ $item->product->id ?? $item->combo->id ?? '' }}"
                    data-variant-values="{{ json_encode($item->variant_values ?? []) }}">
                    <div class="relative">
                        <a href="{{ $url }}" class="block relative h-48 overflow-hidden">
                            <img src="{{ $image }}" alt="{{ $name }}" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                        </a>
                        
                        <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-md heart-icon active" 
                                data-product-id="{{ $item->product->id ?? $item->combo->id ?? '' }}">
                            <i class="fas fa-heart text-sm"></i>
                        </button>
                        
                        @if(($item->product && $item->product->created_at && $item->product->created_at->diffInDays() <= 7) || ($item->combo && $item->combo->created_at && $item->combo->created_at->diffInDays() <= 7))
                            <div class="absolute top-3 left-3">
                                <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">Mới</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-4">
                        <div class="flex items-center gap-1 mb-2">
                            @php
                                if ($item->product && $item->product->reviews) {
                                    $averageRating = $item->product->reviews->avg('rating') ?? 0;
                                    $reviewCount = $item->product->reviews->count();
                                } elseif ($item->combo && $item->combo->reviews) {
                                    $averageRating = $item->combo->reviews->avg('rating') ?? 0;
                                    $reviewCount = $item->combo->reviews->count();
                                } else {
                                    $averageRating = 0;
                                    $reviewCount = 0;
                                }
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
                            <span class="text-xs text-gray-500 ml-1">({{ $reviewCount }})</span>
                        </div>

                        <a href="{{ $url }}">
                            <h3 class="font-medium text-lg mb-1 hover:text-orange-500 transition-colors line-clamp-1">
                                {{ $name }}
                            </h3>
                        </a>

                        <p class="text-gray-500 text-sm mb-3 line-clamp-2">
                            @if($item->product)
                                {{ $item->product->short_description ?? Str::limit($item->product->description, 80) }}
                            @elseif($item->combo)
                                {{ Str::limit($item->combo->description, 80) }}
                            @else
                                Không có mô tả
                            @endif
                        </p>

                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-lg text-orange-500">
                                    @if($item->product)
                                        {{ number_format($item->product->discount_price ?? $item->product->base_price, 0, ',', '.') }}₫
                                    @elseif($item->combo)
                                        {{ number_format($item->combo->price ?? 0, 0, ',', '.') }}₫
                                    @else
                                        Liên hệ
                                    @endif
                                </span>
                            </div>
                            <span class="text-xs text-gray-400">Đã lưu: {{ $item->created_at ? $item->created_at->diffForHumans() : 'N/A' }}</span>
                        </div>

                        <div class="product-actions flex gap-2">
                            <button class="flex-1 bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded-md text-sm flex items-center justify-center transition-colors">
                                <i class="fas fa-shopping-cart mr-1"></i>
                                Thêm vào giỏ
                            </button>
                            <button class="px-3 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50 transition-colors" title="Xem nhanh">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div id="empty-wishlist" class="text-center empty-wishlist flex flex-col items-center justify-center">
                    <div class="w-32 h-32 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-heart-broken text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Danh sách yêu thích trống</h3>
                    <p class="text-gray-600 mb-6 max-w-md">Bạn chưa có món ăn yêu thích nào. Hãy khám phá thực đơn và lưu những món ăn bạn thích!</p>
                    <a href="/products" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-md font-medium transition-colors">
                        Khám phá thực đơn
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Empty State -->
    <div id="empty-wishlist" class="hidden text-center empty-wishlist flex flex-col items-center justify-center">
        <div class="w-32 h-32 bg-gray-100 rounded-full flex items-center justify-center mb-6">
            <i class="fas fa-heart-broken text-4xl text-gray-400"></i>
        </div>
        <h3 class="text-2xl font-bold mb-2">Danh sách yêu thích trống</h3>
        <p class="text-gray-600 mb-6 max-w-md">Bạn chưa có món ăn yêu thích nào. Hãy khám phá thực đơn và lưu những món ăn bạn thích!</p>
        <a href="/products" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-md font-medium transition-colors">
            Khám phá thực đơn
        </a>
    </div>

    <!-- Bulk Actions -->
    <div class="fixed bottom-6 right-6 flex flex-col gap-3">
        <button class="bg-orange-500 hover:bg-orange-600 text-white w-12 h-12 rounded-full flex items-center justify-center shadow-lg transition-all animate-bounce-slow" title="Thêm tất cả vào giỏ hàng">
            <i class="fas fa-shopping-cart"></i>
        </button>
        <button class="bg-red-500 hover:bg-red-600 text-white w-12 h-12 rounded-full flex items-center justify-center shadow-lg transition-all" title="Xóa tất cả">
            <i class="fas fa-trash"></i>
        </button>
    </div>

    <!-- Recommendations Section -->
    <section class="py-10 mt-12 border-t border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl md:text-3xl font-bold">Có thể bạn sẽ thích</h2>
            <a href="/products" class="text-orange-500 hover:text-orange-600 flex items-center">
                Xem tất cả
                <i class="fas fa-arrow-right h-4 w-4 ml-1"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <!-- Recommendation Item 1 -->
            <div class="group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <a href="/products/combo-family" class="block relative h-48 overflow-hidden">
                    <img src="/placeholder.svg?height=400&width=400" alt="Combo Gia Đình" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                    <span class="absolute top-2 right-2 bg-blue-500 text-white text-xs px-2 py-1 rounded-full">Combo</span>
                </a>

                <div class="p-4">
                    <div class="flex items-center gap-1 mb-2">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <span class="text-xs text-gray-500 ml-1">(156)</span>
                    </div>

                    <a href="/products/combo-family">
                        <h3 class="font-medium text-lg mb-1 hover:text-orange-500 transition-colors line-clamp-1">
                            Combo Gia Đình Tiết Kiệm
                        </h3>
                    </a>

                    <p class="text-gray-500 text-sm mb-3 line-clamp-2">2 Burger + 1 Gà rán + 2 Nước ngọt + Khoai tây chiên</p>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-lg">199.000đ</span>
                            <span class="text-gray-500 text-sm line-through">250.000đ</span>
                        </div>

                        <div class="flex gap-1">
                            <button class="w-8 h-8 bg-gray-100 hover:bg-orange-100 rounded-full flex items-center justify-center transition-colors heart-icon" data-product-id="5">
                                <i class="fas fa-heart text-sm text-gray-400"></i>
                            </button>
                            <button class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md text-sm flex items-center transition-colors">
                                <i class="fas fa-shopping-cart h-4 w-4 mr-1"></i>
                                Thêm
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- More recommendation items... -->
        </div>
    </section>
</x-customer-container>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize wishlist functionality
    const wishlistItems = document.querySelectorAll('.wishlist-item');
    const filterBtns = document.querySelectorAll('.filter-btn');
    const viewBtns = document.querySelectorAll('.view-btn');
    const sortSelect = document.getElementById('sort-select');
    const gridView = document.getElementById('grid-view');
    const listView = document.getElementById('list-view');
    const emptyWishlist = document.getElementById('empty-wishlist');
    const wishlistContainer = document.getElementById('wishlist-container');

    // Heart icon functionality
    document.querySelectorAll('.heart-icon').forEach(heart => {
        heart.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const productId = this.dataset.productId;
            if (!productId) return;
            fetch('/wishlist', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                const wishlistItem = this.closest('.wishlist-item');
                wishlistItem.style.transform = 'scale(0.8)';
                wishlistItem.style.opacity = '0';
                setTimeout(() => {
                    wishlistItem.remove();
                    updateWishlistStats();
                    checkEmptyState();
                    if (window.dtmodalShowToast) dtmodalShowToast('success', { title: 'Thành công', message: data.message });
                }, 300);
            })
            .catch(error => {
                console.error('Error:', error);
                if (window.dtmodalShowToast) dtmodalShowToast('error', { title: 'Lỗi', message: 'Có lỗi xảy ra, vui lòng thử lại!' });
            });
        });
    });

    // Filter functionality
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active filter
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const category = this.dataset.category;
            filterItems(category);
        });
    });

    // View toggle functionality
    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            viewBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const view = this.dataset.view;
            toggleView(view);
        });
    });

    // Sort functionality
    sortSelect.addEventListener('change', function() {
        const sortBy = this.value;
        sortItems(sortBy);
    });

    // Add to cart functionality
    document.querySelectorAll('button:has(.fa-shopping-cart)').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const wishlistItem = this.closest('.wishlist-item');
            const productId = wishlistItem.dataset.productId;
            // Lấy variant_values từ data-variant-values nếu có, ngược lại để []
            let variantValues = [];
            if (wishlistItem.dataset.variantValues) {
                try {
                    variantValues = JSON.parse(wishlistItem.dataset.variantValues);
                    if (!Array.isArray(variantValues)) variantValues = [];
                } catch (err) {
                    variantValues = [];
                }
            }
            // Lấy branch_id từ window.currentBranchId hoặc mặc định 1
            const branchId = window.currentBranchId || 1;
            const quantity = 1;

            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    product_id: productId,
                    variant_values: variantValues,
                    branch_id: branchId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (window.dtmodalShowToast) dtmodalShowToast('success', { title: 'Thành công', message: data.message });
                } else {
                    if (window.dtmodalShowToast) dtmodalShowToast('error', { title: 'Lỗi', message: data.message || 'Không thể thêm vào giỏ hàng' });
                }
            })
            .catch(error => {
                if (window.dtmodalShowToast) dtmodalShowToast('error', { title: 'Lỗi', message: 'Có lỗi xảy ra, vui lòng thử lại!' });
            });

            // Add cart animation
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    });

    // Bulk actions
    document.querySelector('button[title="Thêm tất cả vào giỏ hàng"]').addEventListener('click', function() {
        const visibleItems = document.querySelectorAll('.wishlist-item:not(.hidden)');
        if (visibleItems.length > 0) {
            if (window.dtmodalShowToast) dtmodalShowToast('success', { title: 'Thành công', message: `Đã thêm ${visibleItems.length} món vào giỏ hàng` });
        }
    });

    document.querySelector('button[title="Xóa tất cả"]').addEventListener('click', function() {
        if (confirm('Bạn có chắc chắn muốn xóa tất cả món ăn khỏi danh sách yêu thích?')) {
            const visibleItems = document.querySelectorAll('.wishlist-item:not(.hidden)');
            visibleItems.forEach((item, index) => {
                setTimeout(() => {
                    item.style.transform = 'scale(0.8)';
                    item.style.opacity = '0';
                    setTimeout(() => {
                        item.remove();
                        if (index === visibleItems.length - 1) {
                            updateWishlistStats();
                            checkEmptyState();
                        }
                    }, 300);
                }, index * 100);
            });
            if (window.dtmodalShowToast) dtmodalShowToast('success', { title: 'Thành công', message: 'Đã xóa tất cả khỏi danh sách yêu thích' });
        }
    });

    // Functions
    function filterItems(category) {
        wishlistItems.forEach(item => {
            const itemCategory = item.dataset.category;
            if (category === 'all' || itemCategory === category) {
                item.classList.remove('hidden');
                item.classList.add('fade-in-up');
            } else {
                item.classList.add('hidden');
            }
        });
        
        updateWishlistStats();
        checkEmptyState();
    }

    function toggleView(view) {
        if (view === 'grid') {
            gridView.classList.remove('hidden');
            listView.classList.add('hidden');
        } else {
            gridView.classList.add('hidden');
            listView.classList.remove('hidden');
        }
    }

    function sortItems(sortBy) {
        const container = document.querySelector('#grid-view, #list-view:not(.hidden)');
        const items = Array.from(container.querySelectorAll('.wishlist-item'));
        
        items.sort((a, b) => {
            switch (sortBy) {
                case 'price-low':
                    return parseInt(a.dataset.price) - parseInt(b.dataset.price);
                case 'price-high':
                    return parseInt(b.dataset.price) - parseInt(a.dataset.price);
                case 'name':
                    return a.dataset.name.localeCompare(b.dataset.name);
                case 'newest':
                default:
                    return new Date(b.dataset.date) - new Date(a.dataset.date);
            }
        });
        
        items.forEach(item => container.appendChild(item));
    }

    function updateWishlistStats() {
        const visibleItems = document.querySelectorAll('.wishlist-item:not(.hidden)');
        const totalItems = visibleItems.length;
        
        let totalValue = 0;
        const categories = new Set();
        
        visibleItems.forEach(item => {
            totalValue += parseInt(item.dataset.price);
            categories.add(item.dataset.category);
        });
        
        document.getElementById('total-items').textContent = totalItems;
        document.getElementById('total-value').textContent = new Intl.NumberFormat('vi-VN').format(totalValue) + 'đ';
        document.getElementById('categories-count').textContent = categories.size;
    }

    function checkEmptyState() {
        const visibleItems = document.querySelectorAll('.wishlist-item:not(.hidden)');
        if (visibleItems.length === 0) {
            wishlistContainer.classList.add('hidden');
            emptyWishlist.classList.remove('hidden');
        } else {
            wishlistContainer.classList.remove('hidden');
            emptyWishlist.classList.add('hidden');
        }
    }

    // Initialize stats
    updateWishlistStats();
});
</script>
@endsection