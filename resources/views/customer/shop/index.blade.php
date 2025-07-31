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
@if(Auth::check() || true)
    <script>
        window.csrfToken = '{{ csrf_token() }}';
        </script>
@endif
<style>
    
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

    /* Style cho sản phẩm hết hàng */
    .product-card.out-of-stock {
        position: relative;
        opacity: 0.7;
        pointer-events: none;
    }

    .product-card.out-of-stock .product-image {
        filter: blur(2px);
    }

    .product-card.out-of-stock .product-title,
    .product-card.out-of-stock .product-price,
    .product-card.out-of-stock .product-original-price,
    .product-card.out-of-stock .add-to-cart-btn {
        filter: blur(1px);
    }

    .out-of-stock-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(220, 38, 38, 0.9);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        z-index: 20;
        white-space: nowrap;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(4px);
        filter: none !important; /* Đảm bảo overlay không bị blur */
    }

    .out-of-stock-overlay::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(45deg, #dc2626, #ef4444);
        border-radius: 22px;
        z-index: -1;
    }

    /* Animation cho overlay */

    /* Đảm bảo sản phẩm hết hàng vẫn hiển thị nhưng bị blur */
    .product-card.out-of-stock {
        display: block !important;
        visibility: visible !important;
    }

    /* Style cho combo card hết hàng */
    .product-card[data-combo-id].out-of-stock {
        position: relative;
        opacity: 0.7;
        pointer-events: none;
    }
    
    .product-card[data-combo-id].out-of-stock .product-image {
        filter: blur(2px);
    }
    
    .product-card[data-combo-id].out-of-stock .product-title,
    .product-card[data-combo-id].out-of-stock .product-price,
    .product-card[data-combo-id].out-of-stock .product-original-price,
    .product-card[data-combo-id].out-of-stock .add-to-cart-btn {
        filter: blur(1px);
    }
    .commons {
        color: #000;
        font-size: 0.8rem;
        font-weight: 400;
    }
    .fade-in-card {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInCard 0.5s forwards;
    }
    @keyframes fadeInCard {
        to {
            opacity: 1;
            transform: none;
        }
    }
    .skeleton-card {
        background: linear-gradient(90deg, #f3f3f3 25%, #ecebeb 50%, #f3f3f3 75%);
        background-size: 200% 100%;
        animation: skeleton-loading 1.2s infinite linear;
        border-radius: 12px;
        min-height: 270px;
        width: 100%;
        margin-bottom: 0;
    }
    @keyframes skeleton-loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    /* Loading state styles */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 100;
    }
    
    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #F97316;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Highlight animation for updated products */
    .highlight-update {
        animation: highlightPulse 1s ease-in-out;
    }
    
    @keyframes highlightPulse {
        0% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.7); }
        50% { box-shadow: 0 0 0 10px rgba(249, 115, 22, 0.3); }
        100% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0); }
    }
    </style>

@php
$menuBanner = app('App\Http\Controllers\Customer\BannerController')->getBannersByPosition('menu');
@endphp
@include('components.banner', ['banners' => $menuBanner])
<x-customer-container>

<div class="container mx-auto px-4 py-12">


    <!-- Bộ lọc và tìm kiếm -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            <div class="relative w-full md:w-auto">
                <div class="flex">
                    <input type="text" name="search" id="search-input" value="{{ request('search') }}" placeholder="Tìm kiếm món ăn..." class="w-full md:w-80 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <span class="text-gray-600">Sắp xếp theo:</span>
                <select name="sort" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Phổ biến nhất</option>
                    <option value="price-asc" {{ request('sort') == 'price-asc' ? 'selected' : '' }}>Giá: Thấp đến cao</option>
                    <option value="price-desc" {{ request('sort') == 'price-desc' ? 'selected' : '' }}>Giá: Cao đến thấp</option>
                    <option value="name-asc" {{ request('sort') == 'name-asc' ? 'selected' : '' }}>Tên: A-Z</option>
                </select>
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

    <!-- Lazy load từng danh mục và sản phẩm -->
    @php
        $comboCategory = $categories->first(function($cat) {
            return stripos($cat->name, 'combo') !== false;
        });
        $otherCategories = $categories->filter(function($cat) use ($comboCategory) {
            return !$comboCategory || $cat->id !== $comboCategory->id;
        });
        $sectionIndex = 0;
    @endphp
    <div id="category-sections">
        @if($comboCategory && $comboCategory->combos->count() > 0)
            <section class="category-section" id="category-section-{{ $comboCategory->id }}" data-section-index="{{ $sectionIndex }}" style="display: block; margin-bottom: 48px;">
                <h2 class="text-2xl font-bold mb-4 text-orange-600 flex items-center gap-2">
                    <span>{{ $comboCategory->name }}</span>
                </h2>
                <div class="skeletons-container" style="display:none;">
                    @for($i = 0; $i < $comboCategory->combos->count(); $i++)
                        <div class="skeleton-card"></div>
                    @endfor
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 product-cards-container">
                    @foreach($comboCategory->combos as $combo)
                        @include('customer.shop._combo_card', ['combo' => $combo])
                    @endforeach
                </div>
            </section>
            @php $sectionIndex++; @endphp
        @endif
        @foreach($otherCategories as $category)
            @if($category->products->count() > 0)
            <section class="category-section" id="category-section-{{ $category->id }}" data-section-index="{{ $sectionIndex }}" style="display: {{ $sectionIndex === 0 ? 'block' : 'none' }}; margin-bottom: 48px;">
                <h2 class="text-2xl font-bold mb-4 text-orange-600 flex items-center gap-2">
                    <span>{{ $category->name }}</span>
                </h2>
                <div class="skeletons-container" style="display:none;">
                    @for($i = 0; $i < $category->products->count(); $i++)
                        <div class="skeleton-card"></div>
                    @endfor
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 product-cards-container">
                    @foreach($category->products as $product)
                        @include('customer.shop._product_card', ['product' => $product])
                    @endforeach
                </div>
            </section>
            @php $sectionIndex++; @endphp
            @endif
        @endforeach
    </div>
    
    @php
        $hasAnyProducts = false;
        foreach($categories as $category) {
            if (stripos($category->name, 'combo') !== false) {
                if ($category->combos->count() > 0) {
                    $hasAnyProducts = true;
                    break;
                }
            } else {
                if ($category->products->count() > 0) {
                    $hasAnyProducts = true;
                    break;
                }
            }
        }
    @endphp
    
    @if(!$hasAnyProducts)
        <div class="text-center py-12">
            <div class="text-gray-500 text-lg mb-4">
                <i class="fas fa-box-open text-4xl mb-4"></i>
                <p>Không có sản phẩm nào có sẵn tại chi nhánh này.</p>
                <p class="text-sm text-gray-400 mt-2">Vui lòng thử chọn chi nhánh khác hoặc quay lại sau.</p>
            </div>
        </div>
    @endif
</x-customer-container>

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
    window.pusherKey = '{{ config("broadcasting.connections.pusher.key") }}';
    window.pusherCluster = '{{ config("broadcasting.connections.pusher.options.cluster") }}';
</script>
<script src="{{ asset('js/Customer/Shop/index.js') }}"></script>
@include('partials.customer.branch-check')
<!-- Branch Selector Modal -->
@endsection
@include('components.modal')
