@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Thực Đơn')

@section('content')
<!-- Add meta tag for selected branch -->
@if(isset($currentBranch))
<meta name="selected-branch" content="{{ $currentBranch->id }}">
@endif

@if(Auth::check())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('user-authenticated');
        });
    </script>
@endif
<style>
    .container {
      max-width: 1280px;
      margin: 0 auto;
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
    
    .discount-badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
        color: white;
        margin-bottom: 2px;
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

    /* Product card styling */
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
        margin-bottom: 0.5rem;
        transition: color 0.2s ease;
    }

    .product-title:hover {
        color: #F97316;
    }

    .product-price {
        font-size: 1.25rem;
        font-weight: 700;
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

    /* Rating stars */
    .rating-stars {
        color: #F97316;
    }

    .rating-count {
        color: #6B7280;
        font-size: 0.875rem;
    }

    /* No image placeholder */
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
    
    /* Pagination custom styles */
    .pagination-container {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 3rem;
    }

    .pagination-item {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 2.5rem;
        height: 2.5rem;
        padding: 0 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .pagination-item:not(.active):hover {
        background-color: #F3F4F6;
    }

    .pagination-item.active {
        background-color: #F97316;
        color: white;
    }

    .pagination-item.disabled {
        color: #9CA3AF;
        cursor: not-allowed;
    }

    .favorite-btn {
        position: absolute;
        top: 8px;
        left: 8px;
        background-color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.2s;
    }

    .favorite-btn:hover {
        transform: scale(1.1);
    }

    .favorite-btn:hover i {
        color: #FF3B30;
    }

    /* Thêm style cho nút disabled */
    .add-to-cart-btn.disabled {
        background-color: #9CA3AF !important; /* Màu xám */
        cursor: not-allowed !important;
        opacity: 0.7;
        pointer-events: none;
    }
    
    .add-to-cart-btn.disabled:hover {
        background-color: #9CA3AF !important;
    }
</style>

    @php
        $menuBanner = app('App\Http\Controllers\Customer\BannerController')->getBannersByPosition('menu');
    @endphp
    @include('components.banner', ['banners' => $menuBanner])


<div class="container mx-auto px-4 py-12">

    
    <!-- Bộ lọc và tìm kiếm -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            <div class="relative w-full md:w-auto">
                <form action="{{ route('products.index') }}" method="GET" id="search-form" class="flex">
                    <input type="text" name="search" id="search-input" value="{{ request('search') }}" placeholder="Tìm kiếm món ăn..." class="w-full md:w-80 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <input type="hidden" name="sort" value="{{ request('sort', 'popular') }}">
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </form>
            </div>
            
            <div class="flex items-center gap-2 w-full md:w-auto">
                <span class="text-gray-600">Sắp xếp theo:</span>
                <form action="{{ route('products.index') }}" method="GET" id="sort-form" class="m-0">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                    <select name="sort" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Phổ biến nhất</option>
                        <option value="price-asc" {{ request('sort') == 'price-asc' ? 'selected' : '' }}>Giá: Thấp đến cao</option>
                        <option value="price-desc" {{ request('sort') == 'price-desc' ? 'selected' : '' }}>Giá: Cao đến thấp</option>
                        <option value="name-asc" {{ request('sort') == 'name-asc' ? 'selected' : '' }}>Tên: A-Z</option>
                    </select>
                </form>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('products.index', array_merge(request()->except('category'), ['category' => ''])) }}" 
               class="category-btn px-4 py-2 {{ !request('category') ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700' }} rounded-full hover:bg-orange-600 hover:text-white transition-colors">
                Tất cả
            </a>
            @foreach($categories as $category)
                <a href="{{ route('products.index', array_merge(request()->except('category'), ['category' => $category->id])) }}" 
                   class="category-btn px-4 py-2 {{ request('category') == $category->id ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700' }} rounded-full hover:bg-orange-600 hover:text-white transition-colors">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>
    

    
    <!-- Danh sách sản phẩm -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($products as $product)
            <div class="product-card bg-white rounded-lg overflow-hidden" 
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
                    <a href="{{ route('products.show', $product->id) }}" class="block">
                        @if($product->primary_image)
                            <img src="{{ $product->primary_image->s3_url }}" 
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

                <div class="p-4">
                    <div class="flex items-center mb-2">
                        <div class="rating-stars flex">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($product->average_rating))
                                    <i class="fas fa-star"></i>
                                @elseif($i - 0.5 <= $product->average_rating)
                                    <i class="fas fa-star-half-alt"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="rating-count ml-2">({{ $product->reviews_count }})</span>
                    </div>

                    <a href="{{ route('products.show', $product->id) }}" class="block">
                        <h3 class="product-title">{{ $product->name }}</h3>
                    </a>

                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                        {{ $product->short_description ?? Str::limit($product->description, 80) }}
                    </p>

                    <div class="flex justify-between items-center">
                        <div class="flex flex-col">
                            @if($product->discount_price && $product->base_price > $product->discount_price)
                                <span class="product-price">{{ number_format($product->discount_price) }}đ</span>
                                <span class="product-original-price">{{ number_format($product->base_price) }}đ</span>
                            @else
                                <span class="product-price">{{ number_format($product->base_price) }}đ</span>
                            @endif
                        </div>
                        @if(isset($product->has_stock) && $product->has_stock)
                            <a href="{{ route('products.show', ['id' => $product->id]) }}" class="add-to-cart-btn">
                                <i class="fas fa-shopping-cart"></i>
                                Mua hàng
                            </a>
                        @else
                            <button class="add-to-cart-btn disabled" disabled>
                                <i class="fas fa-ban"></i>
                                Hết hàng
                            </button>
                        @endif
                    </div>
                    
                    @if(isset($product->applicable_discount_codes) && $product->applicable_discount_codes->count() > 0)
                        <div class="discount-tag">
                            @foreach($product->applicable_discount_codes as $discountCode)
                                @php
                                    $badgeClass = 'discount-badge';
                                    $icon = 'fa-percent';
                                    $minText = '';
                                    if($discountCode->discount_type === 'fixed_amount') {
                                        $badgeClass .= ' fixed-amount';
                                        $icon = 'fa-money-bill-wave';
                                    } elseif($discountCode->discount_type === 'free_shipping') {
                                        $badgeClass .= ' free-shipping';
                                        $icon = 'fa-shipping-fast';
                                    } else {
                                        $badgeClass .= ' percentage';
                                    }
                                    if(isset($discountCode->min_requirement_type) && $discountCode->min_requirement_value > 0) {
                                        if($discountCode->min_requirement_type === 'order_amount') {
                                            $minText = 'Đơn từ '.number_format($discountCode->min_requirement_value/1000,0).'K';
                                        } elseif($discountCode->min_requirement_type === 'product_price') {
                                            $minText = 'Sản phẩm từ '.number_format($discountCode->min_requirement_value/1000,0).'K';
                                        }
                                    }
                                @endphp
                                <div class="{{ $badgeClass }}" title="{{ $discountCode->name }}">
                                    <i class="fas {{ $icon }}"></i>
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
                </div>
            </div>
        @empty
            <div class="col-span-4 text-center py-8">
                <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-xl font-bold text-gray-700 mb-2">Không tìm thấy sản phẩm</h3>
                @if($currentBranch)
                    @php
                        $branch = $currentBranch;
                    @endphp
                    @if($branch)
                        <p class="text-gray-500">Không tìm thấy sản phẩm nào tại chi nhánh {{ $branch->name }}.</p>
                        <button id="change-branch-empty" class="mt-4 px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 transition-colors">
                            <i class="fas fa-exchange-alt mr-2"></i>Đổi chi nhánh khác
                        </button>
                    @else
                        <p class="text-gray-500">Không có sản phẩm nào phù hợp với tiêu chí tìm kiếm của bạn.</p>
                    @endif
                @else
                    <p class="text-gray-500">Không có sản phẩm nào phù hợp với tiêu chí tìm kiếm của bạn.</p>
                @endif
            </div>
        @endforelse
    </div>
    
    <!-- Phân trang -->
    <div class="pagination-container">
        @if ($products->hasPages())
            {{-- Previous Page Link --}}
            @if ($products->onFirstPage())
                <span class="pagination-item disabled">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $products->previousPageUrl() }}" class="pagination-item">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                @if ($page == $products->currentPage())
                    <span class="pagination-item active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="pagination-item">{{ $page }}</a>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" class="pagination-item">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span class="pagination-item disabled">
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        @endif
    </div>
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
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
    // Pusher configuration
    window.pusherKey = '{{ config('broadcasting.connections.pusher.key') }}';
    window.pusherCluster = '{{ config('broadcasting.connections.pusher.options.cluster') }}';
</script>
<script src="{{ asset('js/Customer/Shop/index.js') }}"></script>
@include('partials.customer.branch-check')
<!-- Branch Selector Modal -->
@endsection