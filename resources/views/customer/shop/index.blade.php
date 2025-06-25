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

    /* Animation for discount badges */
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
        $menuBanner = app('App\\Http\\Controllers\\Customer\\BannerController')->getBannersByPosition('menu');
        $comboCategory = $categories->first(function($cat) { return Str::lower($cat->name) === 'combo'; });
        $selectedCats = isset($selectedCategoryIds) && count($selectedCategoryIds) > 0
            ? $categories->whereIn('id', $selectedCategoryIds)
            : $categories;
        $comboFirstCats = collect();
        if ($comboCategory && $selectedCats->contains('id', $comboCategory->id)) {
            $comboFirstCats->push($comboCategory);
            $comboFirstCats = $comboFirstCats->merge($selectedCats->filter(function($cat) use ($comboCategory) {
                return $cat->id != $comboCategory->id;
            }));
        } else {
            $comboFirstCats = $selectedCats;
        }
    @endphp
    @include('components.banner', ['banners' => $menuBanner])


<div class="container mx-auto px-4 py-12">
    <div class="mb-8">
        <!-- Live search input -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-2 relative">
            <div class="flex-1 relative">
                <input type="text" id="live-search-input" placeholder="Tìm kiếm món ăn..." class="w-full md:w-80 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500" autocomplete="off">
                <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            <div class="w-full md:w-auto mt-2 md:mt-0">
                <select id="sort-select" class="border rounded-md px-3 py-2 focus:ring-2 focus:ring-orange-500 w-full md:w-auto">
                    <option value="popular">Phổ biến nhất</option>
                    <option value="price-asc">Giá: Thấp đến cao</option>
                    <option value="price-desc">Giá: Cao đến thấp</option>
                    <option value="name-asc">Tên: A-Z</option>
                </select>
            </div>
        </div>
        <!-- Thông báo không tìm thấy sản phẩm -->
        <div id="no-search-result-message" class="hidden text-center py-16">
            <i class="fas fa-search text-gray-400 text-5xl mb-4"></i>
            <div class="text-xl font-bold text-gray-700 mb-2">Chúng tôi không thể tìm thấy sản phẩm nào phù hợp với từ khóa <span id="no-search-keyword" class="font-semibold"></span>.</div>
        </div>
        <!-- Danh sách category dạng block -->
        <div id="category-blocks">
            @foreach($comboFirstCats as $category)
                <div class="category-block mb-12" data-category-id="{{ $category->id }}">
                    <h2 class="text-2xl font-bold mb-4">{{ $category->name }}</h2>
                    <div class="product-list" id="product-list-{{ $category->id }}">
                        @php
                            $catProducts = $products->where('category_id', $category->id);
                        @endphp
                        @if($catProducts->count())
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                @foreach($catProducts as $product)
                                    @include('customer.shop._product_card', ['product' => $product])
                                @endforeach
                            </div>
                        @else
                            <div class="loading-placeholder text-gray-400 py-8 text-center">Đang tải sản phẩm...</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
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
<script src="{{ asset('js/Customer/discount-updates.js') }}"></script>
@include('partials.customer.branch-check')
<!-- Branch Selector Modal -->
@endsection

@include('components.modal')