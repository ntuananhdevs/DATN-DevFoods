@extends('layouts.customer.fullLayoutMaster')

@php
    if (!isset($search)) {
        $search = null;
    }
@endphp

@section('title', 'FastFood - Tìm kiếm')

@section('content')
<style>
    .container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    /* Header */
    .header {
        background: white;
        border-bottom: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .header-content {
        padding: 1.5rem 0;
    }

    .header h1 {
        color: #1f2937;
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 1.5rem;
    }

    .search-container {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        font-size: 1.1rem;
        border: 2px solid #d1d5db;
        border-radius: 0.5rem;
        outline: none;
        transition: border-color 0.2s;
    }

    .search-input:focus {
        border-color: #f97316;
    }

    .search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #f97316;
    }

    .header-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .results-count {
        color: #6b7280;
    }

    .results-count strong {
        color: #1f2937;
    }

    .sort-select {
        padding: 0.5rem 1rem;
        border: 2px solid #d1d5db;
        border-radius: 0.5rem;
        background: white;
        color: #1f2937;
        outline: none;
    }

    /* Main Layout */
    .main-content {
        display: flex;
        gap: 2rem;
        padding: 2rem 0;
    }

    /* Sidebar */
    .sidebar {
        width: 320px;
        flex-shrink: 0;
    }

    .filter-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        position: sticky;
        top: 2rem;
    }

    .filter-title {
        color: #1f2937;
        font-size: 1.25rem;
        font-weight: bold;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-section {
        margin-bottom: 1.5rem;
    }

    .filter-section:last-child {
        margin-bottom: 0;
    }

    .filter-label {
        display: block;
        color: #1f2937;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }

    .checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .checkbox-item input[type="checkbox"] {
        width: 1rem;
        height: 1rem;
        accent-color: #f97316;
    }

    .checkbox-item label {
        color: #4b5563;
        cursor: pointer;
        font-size: 0.875rem;
    }

    .price-range {
        margin-top: 0.75rem;
    }

    .price-slider {
        width: 100%;
        margin-bottom: 0.5rem;
        accent-color: #dd6b20;
        background: #e4e4e4;
    }

    .price-display {
        display: flex;
        justify-content: space-between;
        gap: 0.5rem;
    }

    .price-tag {
        background: #f3f4f6;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        color: #1f2937;
    }

    .rating-select {
        width: 100%;
        padding: 0.5rem;
        border: 2px solid #d1d5db;
        border-radius: 0.25rem;
        background: white;
        color: #1f2937;
    }

    .reset-btn {
        width: 100%;
        padding: 0.75rem;
        background: transparent;
        border: 2px solid #d1d5db;
        border-radius: 0.5rem;
        color: #4b5563;
        cursor: pointer;
        transition: all 0.2s;
    }

    .reset-btn:hover {
        background: #f9fafb;
        color: #1f2937;
    }

    /* Results Grid */
    .results-container {
        flex: 1;
    }

    .food-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .food-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }

    .food-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-color: #d1d5db;
    }

    .card-image {
        position: relative;
        height: 180px;
        overflow: hidden;
        background: #f3f4f6;
    }

    .card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .new-badge {
        position: absolute;
        top: 0.5rem;
        left: 0.5rem;
        background: #10b981;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .favorite-btn {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: rgba(255,255,255,0.9);
        border: none;
        border-radius: 0.25rem;
        padding: 0.5rem;
        cursor: pointer;
        color: #6b7280;
    }

    .card-content {
        padding: 1rem;
    }

    .rating-stars {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        margin-bottom: 0.5rem;
    }

    .star {
        color: #fbbf24;
        font-size: 0.875rem;
    }

    .star.empty {
        color: #d1d5db;
    }

    .review-count {
        color: #6b7280;
        font-size: 0.75rem;
        margin-left: 0.5rem;
    }

    .card-title {
        color: #1f2937;
        font-size: 1.125rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .card-description {
        color: #6b7280;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }

    .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 1rem 1rem;
    }

    .price {
        font-size: 1.25rem;
        font-weight: bold;
        color: #1f2937;
    }

    .add-btn {
        background: #f97316;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        cursor: pointer;
        font-weight: 600;
        transition: background-color 0.2s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .add-btn:hover {
        background: #ea580c;
    }

    .no-results {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin: 2rem 0;
        grid-column: 1 / -1;
        width: 100%;
    }

    .no-results-icon {
        color: #d1d5db;
        font-size: 4rem;
        margin-bottom: 1rem;
    }

    .no-results h3 {
        color: #4b5563;
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .no-results p {
        color: #6b7280;
        font-size: 1rem;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .main-content {
            flex-direction: column;
        }

        .sidebar {
            width: 100%;
        }

        .filter-card {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .food-grid {
            grid-template-columns: 1fr;
        }

        .header-controls {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<!-- Header -->
<header class="header">
    <div class="container">
        <div id="searchHeaderWrapper">
            <div class="header-content">
                <h1>Tìm kiếm món ăn</h1>
                <div id="searchFormWrapper">
                    <form action="{{ route('customer.search') }}" method="GET" id="searchForm">
                        <div class="search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="searchInput" name="search" class="search-input" placeholder="Tìm kiếm món ăn, nhà hàng..." value="{{ $search ?? '' }}">
                            <button type="submit" class="search-submit-btn" style="position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #f97316; cursor: pointer; padding: 0.5rem;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="header-controls">
                    <div class="results-count">
                        Tìm thấy <strong id="resultsCount">{{ (isset($products) ? $products->count() : 0) + (isset($combos) ? $combos->count() : 0) }}</strong> kết quả
                    </div>
                    <div class="flex items-center gap-4">
                        <select id="sortSelect" class="sort-select">
                            <option value="rating">Đánh giá cao nhất</option>
                            <option value="reviews">Nhiều đánh giá nhất</option>
                            <option value="price-low">Giá thấp đến cao</option>
                            <option value="price-high">Giá cao đến thấp</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="container">
    <div class="main-content">
        <!-- Sidebar Filters -->
        <aside class="sidebar">
            <div class="filter-card">
                <h2 class="filter-title">
                    <i class="fas fa-filter"></i>
                    Bộ lọc tìm kiếm
                </h2>

                <!-- Category Filter -->
                <div class="filter-section">
                    <label class="filter-label">Danh mục</label>
                    <div class="checkbox-group" id="categoryFilters">
                        <div class="checkbox-item">
                            <input type="radio" id="all" name="category" value="Tất cả" checked>
                            <label for="all">Tất cả</label>
                        </div>
                        @foreach($categories as $category)
                        <div class="checkbox-item">
                            <input type="radio" id="category_{{ $category->id }}" name="category" value="{{ $category->id }}">
                            <label for="category_{{ $category->id }}">{{ $category->name }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Price Range -->
                <div class="filter-section">
                    <label class="filter-label">Khoảng giá</label>
                    <div class="price-range">
                        <input type="range" id="priceRange" class="price-slider" min="0" max="500" step="10" value="500">
                        <div class="price-display">
                            <span class="price-tag">0₫</span>
                            <span class="price-tag" id="maxPrice">500.000₫</span>
                        </div>
                    </div>
                </div>

                <!-- Rating Filter -->
                <div class="filter-section">
                    <label class="filter-label">Đánh giá tối thiểu</label>
                    <select id="ratingFilter" class="rating-select">
                        <option value="0">Tất cả</option>
                        <option value="3">3+ sao</option>
                        <option value="4">4+ sao</option>
                        <option value="4.5">4.5+ sao</option>
                    </select>
                </div>

                <button class="reset-btn" onclick="resetFilters()">Xóa bộ lọc</button>
            </div>
        </aside>

        <!-- Results -->
        <div class="results-container">
            @if(isset($products) && $products->count() == 0 && isset($notice))
                <div id="categoryNotice" class="text-orange-600 text-base font-semibold block mb-2">
                    {!! $notice !!}
                </div>
            @endif
            @if(isset($combos) && $combos->count() > 0)
                <h2 id="comboTitle" class="text-xl font-bold mb-4">Combo</h2>
                <div class="food-grid mb-8" id="comboGrid" style="grid-template-columns: repeat(2, 1fr);">
                    @foreach($combos->chunk(2) as $comboRow)
                        @foreach($comboRow as $combo)
                        <div class="food-card combo-card group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow"
                            data-combo-id="{{ $combo->id }}"
                            data-type="combo"
                            data-price="{{ $combo->price }}"
                            data-rating="0"
                            data-category="{{ $combo->category_id }}">
                            <div class="relative">
                                <a href="{{ route('combos.show', $combo->id) }}" class="block relative h-48 overflow-hidden">
                                    @if($combo->primary_image)
                                        <img src="{{ $combo->primary_image }}" alt="{{ $combo->name }}" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                                    @else
                                        <img src="{{ asset('images/default-placeholder.png') }}" alt="{{ $combo->name }}" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                                    @endif
                                </a>
                                @if(isset($combo->base_price) && $combo->base_price > $combo->price)
                                    @php
                                        $discountPercent = round((($combo->base_price - $combo->price) / $combo->base_price) * 100);
                                    @endphp
                                    <span class="custom-badge badge-sale text-xs bg-red-500 text-white px-2 py-1 rounded absolute top-2 left-2">-{{ $discountPercent }}%</span>
                                @endif
                            </div>
                            <div class="p-4">
                                <a href="">
                                    <h3 class="card-title font-medium text-lg mb-1 hover:text-orange-500 transition-colors line-clamp-1">
                                        <a href="{{ route('combos.show', $combo->id) }}">{{ $combo->name }}</a>
                                    </h3>
                                </a>
                                <p class="card-description text-gray-500 text-sm mb-3 line-clamp-2">
                                    {{ $combo->description }}
                                </p>
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @foreach($combo->products as $product)
                                        <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $product->name }}</span>
                                    @endforeach
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-lg">{{ number_format($combo->price, 0, ',', '.') }}đ</span>
                                    <button class="add-to-cart-btn bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md text-sm flex items-center transition-colors" data-combo-id="{{ $combo->id }}">
                                        <i class="fas fa-shopping-cart h-4 w-4 mr-1"></i>
                                        Thêm
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endforeach
                </div>
            @endif
            @if(isset($products) && $products->count() > 0)
                <h2 id="productTitle" class="text-xl font-bold mb-4">Sản phẩm</h2>
                <div class="food-grid" id="foodGrid">
                    @foreach($products as $product)
                    <div class="food-card product-card group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow"
                    data-product-id="{{ $product->id }}"
                    data-type="product"
                    data-variant-id="{{ $product->first_variant ? $product->first_variant->id : '' }}"
                    data-has-stock="{{ $product->has_stock ? 'true' : 'false' }}"
                    data-category="{{ $product->category ? $product->category->id : '' }}"
                    data-price="{{ $product->discount_price ? $product->discount_price : $product->base_price }}"
                    data-rating="{{ $product->average_rating ?? 0 }}">

                    <div class="relative">
                        <a href="{{ route('products.show', $product->id) }}" class="block relative h-48 overflow-hidden">
                            @if($product->primary_image && $product->primary_image->s3_url)
                                <img src="{{ $product->primary_image->s3_url }}"
                                    alt="{{ $product->name }}" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                            @else
                                <img src="{{ asset('images/default-placeholder.png') }}"
                                    alt="{{ $product->name }}" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                                {{-- Alternative placeholder like your other list:
                                <div class="no-image-placeholder flex items-center justify-center h-full bg-gray-100">
                                    <i class="far fa-image text-3xl text-gray-400"></i>
                                </div>
                                --}}
                            @endif
                        </a>

                        {{-- Favorite Button --}}
                        {{-- <div class="absolute top-2 right-2">
                            @auth
                            <button class="favorite-btn bg-white p-1.5 rounded-full shadow text-gray-600 hover:text-red-500 focus:outline-none" data-product-id="{{ $product->id }}">
                                @if($product->is_favorite)
                                    <i class="fas fa-heart text-red-500"></i>
                                @else
                                    <i class="far fa-heart"></i>
                                @endif
                            </button>
                            @else
                            <button class="favorite-btn login-prompt-btn bg-white p-1.5 rounded-full shadow text-gray-600 hover:text-red-500 focus:outline-none">
                                <i class="far fa-heart"></i>
                            </button>
                            @endauth
                        </div> --}}

                        {{-- Badges (Sale/New) --}}
                        <div class="absolute top-2 left-2">
                            @if($product->discount_price && $product->base_price > $product->discount_price)
                                @php
                                    $discountPercent = round((($product->base_price - $product->discount_price) / $product->base_price) * 100);
                                @endphp
                                <span class="custom-badge badge-sale text-xs bg-red-500 text-white px-2 py-1 rounded">-{{ $discountPercent }}%</span>
                            @elseif($product->created_at->diffInDays(now()) <= 7)
                                <span class="custom-badge badge-new text-xs bg-green-500 text-white px-2 py-1 rounded">Mới</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="flex items-center gap-1 mb-2">
                            {{-- Rating Stars --}}
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($product->average_rating))
                                    <i class="fas fa-star text-yellow-400"></i>
                                @elseif($i - 0.5 <= $product->average_rating)
                                    <i class="fas fa-star-half-alt text-yellow-400"></i>
                                @else
                                    <i class="far fa-star text-yellow-400"></i> {{-- or text-gray-300 for empty --}}
                                @endif
                            @endfor
                            <span class="text-xs text-gray-500 ml-1">({{ $product->reviews_count }})</span>
                        </div>

                        <a href="{{ route('products.show', $product->id) }}">
                            <h3 class="card-title font-medium text-lg mb-1 hover:text-orange-500 transition-colors line-clamp-1">
                                {{ $product->name }}
                            </h3>
                        </a>

                        <p class="card-description text-gray-500 text-sm mb-3 line-clamp-2">
                            {{ $product->short_description ?? Illuminate\Support\Str::limit($product->description, 80) }}
                        </p>

                        <div class="flex items-center justify-between">
                            {{-- Price --}}
                            <div class="flex flex-col">
                                @if($product->discount_price && $product->base_price > $product->discount_price)
                                    <span class="font-bold text-lg text-red-600">{{ number_format($product->discount_price, 0, ',', '.') }}đ</span>
                                    <span class="text-sm text-gray-500 line-through">{{ number_format($product->base_price, 0, ',', '.') }}đ</span>
                                @else
                                    <span class="font-bold text-lg">{{ number_format($product->base_price, 0, ',', '.') }}đ</span>
                                @endif
                            </div>

                            {{-- Add to Cart Button --}}
                            @if(isset($product->has_stock) && $product->has_stock)
                                <button class="add-to-cart-btn bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md text-sm flex items-center transition-colors">
                                    <i class="fas fa-shopping-cart h-4 w-4 mr-1"></i>
                                    Thêm
                                </button>
                            @else
                                <button class="add-to-cart-btn bg-gray-400 text-white px-3 py-1 rounded-md text-sm flex items-center transition-colors cursor-not-allowed" disabled>
                                    <i class="fas fa-ban h-4 w-4 mr-1"></i>
                                    Hết hàng
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                    @endforeach
                </div>
            @endif
            @if(isset($products) && $products instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-8 flex justify-center">
                {{ $products->appends(request()->query())->links() }}
            </div>

            @endif
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Format price function
        function formatPrice(price) {
            return price.toLocaleString('vi-VN') + 'đ';
        }

        // Apply filters and sorting
        function applyFilters() {
            const searchQuery = document.getElementById('searchInput').value.toLowerCase();
            const selectedCategory = document.querySelector('input[name="category"]:checked')?.value || 'Tất cả';
            const maxPrice = parseInt(document.getElementById('priceRange').value) * 1000;
            const minRating = parseFloat(document.getElementById('ratingFilter').value);
            const sortBy = document.getElementById('sortSelect').value;
            const selectedType = document.getElementById('typeSelect')?.value || 'all';

            const foodCards = document.querySelectorAll('.food-card');
            let visibleCount = 0;

            // First, filter the cards
            foodCards.forEach(card => {
                // Skip the no-results card
                if (card.classList.contains('no-results')) return;

                const cardTitle = card.querySelector('.card-title');
                const cardDesc = card.querySelector('.card-description');

                if (!cardTitle || !cardDesc) return;

                const productName = cardTitle.textContent.toLowerCase();
                const productDesc = cardDesc.textContent.toLowerCase();
                const category = card.dataset.category || '';
                const price = parseInt(card.dataset.price) || 0;
                const rating = parseFloat(card.dataset.rating) || 0;
                const cardType = card.dataset.type || 'product';

                // Check if it's a combo and search in products within combo
                let matchesSearch = productName.includes(searchQuery) || productDesc.includes(searchQuery);

                // For combos, also search in products within the combo
                if (cardType === 'combo' && searchQuery) {
                    const productsInCombo = card.querySelectorAll('.text-xs.bg-gray-100');
                    productsInCombo.forEach(productSpan => {
                        if (productSpan.textContent.toLowerCase().includes(searchQuery)) {
                            matchesSearch = true;
                        }
                    });
                }
                const matchesCategory = selectedCategory === 'Tất cả' || category === selectedCategory;
                const matchesPrice = price <= maxPrice;
                // For combos, skip rating filter since they don't have ratings
                const matchesRating = cardType === 'combo' ? true : rating >= minRating;
                const matchesType = selectedType === 'all' || cardType === selectedType;

                if (matchesSearch && matchesCategory && matchesPrice && matchesRating && matchesType) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Update results count
            const resultsCountElement = document.getElementById('resultsCount');
            if (resultsCountElement) {
                resultsCountElement.textContent = visibleCount;
            }

            // Show/hide no results message and combo section
            const noResults = document.getElementById('noResults');
            const comboSection = document.getElementById('comboSection');
            const comboCards = document.querySelectorAll('.combo-card');
            const productCards = document.querySelectorAll('.product-card:not(.combo-card)');

            // Count visible products and combos separately
            let visibleProducts = 0;
            let visibleCombos = 0;

            productCards.forEach(card => {
                if (card.style.display !== 'none') visibleProducts++;
            });

            comboCards.forEach(card => {
                if (card.style.display !== 'none') visibleCombos++;
            });

            // Show/hide combo section based on type filter and visibility
            if (comboSection) {
                if (selectedType === 'products') {
                    comboSection.style.display = 'none';
                } else if (selectedType === 'combos') {
                    comboSection.style.display = visibleCombos > 0 ? 'block' : 'none';
                } else {
                    comboSection.style.display = visibleCombos > 0 ? 'block' : 'none';
                }
            }

            if (visibleCount === 0) {
                if (selectedCategory !== 'Tất cả') {
                    // Hiện toàn bộ sản phẩm, giữ nguyên radio
                    foodCards.forEach(card => {
                        if (!card.classList.contains('no-results')) {
                            card.style.display = 'block';
                        }
                    });
                    // Hiện thông báo phía trên grid
                    let notice = document.getElementById('categoryNotice');
                    if (!notice) {
                        notice = document.createElement('span');
                        notice.id = 'categoryNotice';
                        notice.className = 'text-orange-600 text-base font-semibold block mb-2';
                    }
                    const comboTitle = document.getElementById('comboTitle');
                    const resultsContainer = document.querySelector('.results-container');
                    if (comboTitle && comboTitle.parentNode) {
                        comboTitle.parentNode.insertBefore(notice, comboTitle);
                    } else if (resultsContainer) {
                        resultsContainer.insertBefore(notice, resultsContainer.firstChild);
                    }
                    const searchValue = document.getElementById('searchInput')?.value || '';
                    const safeSearch = searchValue.replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    if (safeSearch.trim()) {
                        notice.innerHTML = 'Không có sản phẩm thuộc danh mục này, bạn hãy tiếp tục tham khảo những món liên quan đến <span class="font-bold">' + safeSearch + '</span>';
                    } else {
                        notice.textContent = 'Không có sản phẩm thuộc danh mục này, bạn hãy tiếp tục tham khảo các món khác.';
                    }
                    // Cập nhật lại số lượng kết quả
                    const resultsCountElement = document.getElementById('resultsCount');
                    if (resultsCountElement) {
                        resultsCountElement.textContent = foodCards.length;
                    }
                    return;
                } else {
                    // Nếu là "Tất cả" mà vẫn không có sản phẩm, ẩn notice nếu có
                    const notice = document.getElementById('categoryNotice');
                    if (notice) notice.remove();
                }
                if (noResults) noResults.style.display = 'block';
            } else {
                // Ẩn notice nếu có
                const notice = document.getElementById('categoryNotice');
                if (notice) notice.remove();
                if (noResults) noResults.style.display = 'none';
            }

            // Ẩn/hiện phân trang dựa vào số sản phẩm hiển thị
            const perPage = 15; // Số sản phẩm mỗi trang, chỉnh theo paginate trong controller
            const pagination = document.querySelector('.mt-8.flex.justify-center');
            if (pagination) {
                if (visibleCount < perPage) {
                    pagination.style.display = 'none';
                } else {
                    pagination.style.display = 'flex';
                }
            }

            // Apply sorting if needed
            if (sortBy !== 'rating') {
                applySorting(sortBy);
            }

            updateComboTitleVisibility();
            moveCategoryNoticeAboveComboTitle();

            const productTitle = document.getElementById('productTitle');
            if (productTitle) {
                if (selectedType === 'combos') {
                    productTitle.style.display = 'none';
                } else {
                    productTitle.style.display = visibleProducts > 0 ? 'block' : 'none';
                }
            }
        }

        // Apply sorting function
        function applySorting(sortBy) {
            const foodGrid = document.getElementById('foodGrid');
            const comboGrid = document.getElementById('comboGrid');

            if (!foodGrid) return;

            // Get all visible cards
            const visibleCards = [];
            const allCards = document.querySelectorAll('.food-card:not(.no-results)');

            allCards.forEach(card => {
                if (card.style.display !== 'none') {
                    const price = parseInt(card.dataset.price) || 0;
                    const rating = parseFloat(card.dataset.rating) || 0;
                    const reviews = parseInt(card.querySelector('.text-xs.text-gray-500')?.textContent.match(/\((\d+)\)/)?.[1] || 0);
                    const cardType = card.dataset.type || 'product';

                    visibleCards.push({
                        element: card,
                        price: price,
                        rating: rating,
                        reviews: reviews,
                        cardType: cardType
                    });
                }
            });

            // Sort the cards
            visibleCards.sort((a, b) => {
                switch (sortBy) {
                    case 'price-low':
                        return a.price - b.price;
                    case 'price-high':
                        return b.price - a.price;
                    case 'rating':
                        // For combos, put them at the end since they don't have ratings
                        if (a.cardType === 'combo' && b.cardType !== 'combo') return 1;
                        if (a.cardType !== 'combo' && b.cardType === 'combo') return -1;
                        if (a.cardType === 'combo' && b.cardType === 'combo') return 0;
                        return b.rating - a.rating;
                    case 'reviews':
                        // For combos, put them at the end since they don't have reviews
                        if (a.cardType === 'combo' && b.cardType !== 'combo') return 1;
                        if (a.cardType !== 'combo' && b.cardType === 'combo') return -1;
                        if (a.cardType === 'combo' && b.cardType === 'combo') return 0;
                        return b.reviews - a.reviews;
                    default:
                        return 0;
                }
            });

            // Reorder cards in DOM
            const productCards = visibleCards.filter(card => card.cardType !== 'combo');
            const comboCards = visibleCards.filter(card => card.cardType === 'combo');

            // Reorder product cards
            productCards.forEach(card => {
                foodGrid.appendChild(card.element);
            });

            // Reorder combo cards
            if (comboGrid && comboCards.length > 0) {
                comboCards.forEach(card => {
                    comboGrid.appendChild(card.element);
                });
            }
        }

        // Reset filters
        function resetFilters() {
            const searchInput = document.getElementById('searchInput');
            const allRadio = document.getElementById('all');
            const priceRange = document.getElementById('priceRange');
            const maxPriceElement = document.getElementById('maxPrice');
            const ratingFilter = document.getElementById('ratingFilter');
            const typeSelect = document.getElementById('typeSelect');
            const sortSelect = document.getElementById('sortSelect');

            if (searchInput) searchInput.value = '';
            if (allRadio) allRadio.checked = true;
            if (priceRange) priceRange.value = 500;
            if (maxPriceElement) maxPriceElement.textContent = '500.000₫';
            if (ratingFilter) ratingFilter.value = '0';
            if (typeSelect) typeSelect.value = 'all';
            if (sortSelect) sortSelect.value = 'rating';

            // Show all cards first
            const foodCards = document.querySelectorAll('.food-card:not(.no-results)');
            foodCards.forEach(card => card.style.display = 'block');

            // Show combo section if it exists
            const comboSection = document.getElementById('comboSection');
            if (comboSection) comboSection.style.display = 'block';

            // Apply filters to restore original state
            applyFilters();
        }

        // Update price display
        function updatePriceDisplay() {
            const priceRange = document.getElementById('priceRange');
            const maxPriceElement = document.getElementById('maxPrice');

            if (priceRange && maxPriceElement) {
                const value = parseInt(priceRange.value);
                maxPriceElement.textContent = value.toLocaleString('vi-VN') + '.000₫';
                applyFilters();
            }
        }

        // Submit form function
        function submitSearchForm() {
            const form = document.getElementById('searchForm');
            const searchInput = document.getElementById('searchInput');
            const selectedCategoryRadio = document.querySelector('input[name="category"]:checked');
            const priceRange = document.getElementById('priceRange');
            const ratingFilter = document.getElementById('ratingFilter');
            const sortSelect = document.getElementById('sortSelect');
            const typeSelect = document.getElementById('typeSelect');

            if (!form) return;

            const selectedCategory = selectedCategoryRadio?.value || 'Tất cả';
            const maxPrice = priceRange?.value || '500';
            const minRating = ratingFilter?.value || '0';
            const sortBy = sortSelect?.value || 'rating';
            const selectedType = typeSelect?.value || 'all';

            // Add hidden inputs for filters
            let existingInputs = form.querySelectorAll('input[name="category"], input[name="max_price"], input[name="min_rating"], input[name="sort"], input[name="type"]');
            existingInputs.forEach(input => input.remove());

            // Add category filter
            if (selectedCategory !== 'Tất cả') {
                const categoryInput = document.createElement('input');
                categoryInput.type = 'hidden';
                categoryInput.name = 'category';
                categoryInput.value = selectedCategory;
                form.appendChild(categoryInput);
            }

            // Add price filter
            if (maxPrice !== '500') {
                const priceInput = document.createElement('input');
                priceInput.type = 'hidden';
                priceInput.name = 'max_price';
                priceInput.value = maxPrice;
                form.appendChild(priceInput);
            }

            // Add rating filter
            if (minRating !== '0') {
                const ratingInput = document.createElement('input');
                ratingInput.type = 'hidden';
                ratingInput.name = 'min_rating';
                ratingInput.value = minRating;
                form.appendChild(ratingInput);
            }

            // Add sort filter
            if (sortBy !== 'rating') {
                const sortInput = document.createElement('input');
                sortInput.type = 'hidden';
                sortInput.name = 'sort';
                sortInput.value = sortBy;
                form.appendChild(sortInput);
            }

            // Add type filter
            if (selectedType !== 'all') {
                const typeInput = document.createElement('input');
                typeInput.type = 'hidden';
                typeInput.name = 'type';
                typeInput.value = selectedType;
                form.appendChild(typeInput);
            }

            form.submit();
        }

        // Event listeners
        const searchInput = document.getElementById('searchInput');
        const sortSelect = document.getElementById('sortSelect');
        const typeSelect = document.getElementById('typeSelect');
        const ratingFilter = document.getElementById('ratingFilter');
        const priceRange = document.getElementById('priceRange');
        const searchForm = document.getElementById('searchForm');
        const searchSubmitBtn = document.querySelector('.search-submit-btn');

        if (searchInput) {
            searchInput.addEventListener('input', applyFilters);
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    submitSearchForm();
                }
            });
        }

        if (sortSelect) {
            sortSelect.addEventListener('change', applyFilters);
        }

        if (typeSelect) {
            typeSelect.addEventListener('change', applyFilters);
        }

        if (ratingFilter) {
            ratingFilter.addEventListener('change', applyFilters);
        }

        if (priceRange) {
            priceRange.addEventListener('input', updatePriceDisplay);
        }

        // Category filter event listeners
        document.querySelectorAll('input[name="category"]').forEach(radio => {
            radio.addEventListener('change', applyFilters);
        });

        // Form submit event
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitSearchForm();
            });
        }

        // Search button click
        if (searchSubmitBtn) {
            searchSubmitBtn.addEventListener('click', function(e) {
                e.preventDefault();
                submitSearchForm();
            });
        }

        // Make resetFilters globally available
        window.resetFilters = resetFilters;

        function updateComboTitleVisibility() {
            const comboCards = document.querySelectorAll('.combo-card');
            let visibleCombos = 0;
            comboCards.forEach(card => {
                if (card.style.display !== 'none') visibleCombos++;
            });
            const comboTitle = document.getElementById('comboTitle');
            if (comboTitle) {
                comboTitle.style.display = visibleCombos > 0 ? 'block' : 'none';
            }
        }

        function moveCategoryNoticeAboveComboTitle() {
            const notice = document.getElementById('categoryNotice');
            const comboTitle = document.getElementById('comboTitle');
            if (notice && comboTitle && notice.nextSibling !== comboTitle) {
                comboTitle.parentNode.insertBefore(notice, comboTitle);
            }
        }

        // Toggle toàn bộ header tìm kiếm
        const toggleBtn = document.getElementById('toggleSearchHeader');
        const searchHeaderWrapper = document.getElementById('searchHeaderWrapper');
        let isVisible = true;
        if (toggleBtn && searchHeaderWrapper) {
            toggleBtn.addEventListener('click', function() {
                isVisible = !isVisible;
                searchHeaderWrapper.style.display = isVisible ? 'block' : 'none';
                toggleBtn.textContent = isVisible ? 'Ẩn tìm kiếm' : 'Hiện tìm kiếm';
            });
        }
    });
</script>

{{-- Include branch checking logic --}}
@include('partials.customer.branch-check')

<!-- Thêm biến Pusher cho JS -->
<script>
    window.pusherKey = "{{ config('broadcasting.connections.pusher.key') }}";
    window.pusherCluster = "{{ config('broadcasting.connections.pusher.options.cluster') }}";
</script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="/js/chat-realtime.js"></script>
@endsection
