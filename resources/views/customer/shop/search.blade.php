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
        accent-color: #f97316;
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
        padding: 3rem 0;
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
    }

    .no-results p {
        color: #6b7280;
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
        <div class="header-content">
            <h1>Tìm kiếm món ăn</h1>

            <form action="{{ route('customer.search') }}" method="GET" id="searchForm">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchInput" name="search" class="search-input" placeholder="Tìm kiếm món ăn, nhà hàng..." value="{{ $search ?? '' }}">
                    <button type="submit" class="search-submit-btn" style="position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #f97316; cursor: pointer; padding: 0.5rem;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <div class="header-controls">
                <div class="results-count">
                    Tìm thấy <strong id="resultsCount">{{ isset($products) ? $products->total() : 0 }}</strong> kết quả
                </div>
                <select id="sortSelect" class="sort-select">
                    <option value="rating" {{ request('sort', 'rating') == 'rating' ? 'selected' : '' }}>Đánh giá cao nhất</option>
                    <option value="reviews" {{ request('sort') == 'reviews' ? 'selected' : '' }}>Nhiều đánh giá nhất</option>
                    <option value="price-low" {{ request('sort') == 'price-low' ? 'selected' : '' }}>Giá thấp đến cao</option>
                    <option value="price-high" {{ request('sort') == 'price-high' ? 'selected' : '' }}>Giá cao đến thấp</option>
                </select>
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
                            <input type="radio" id="all" name="category" value="Tất cả" {{ !request('category') || request('category') == 'Tất cả' ? 'checked' : '' }}>
                            <label for="all">Tất cả</label>
                        </div>
                        @foreach($categories as $category)
                        <div class="checkbox-item">
                            <input type="radio" id="category_{{ $category->id }}" name="category" value="{{ $category->id }}" {{ request('category') == $category->id ? 'checked' : '' }}>
                            <label for="category_{{ $category->id }}">{{ $category->name }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Price Range -->
                <div class="filter-section">
                    <label class="filter-label">Khoảng giá</label>
                    <div class="price-range">
                        <input type="range" id="priceRange" class="price-slider" min="0" max="200000" step="5000" value="{{ request('max_price', 200000) }}">
                        <div class="price-display">
                            <span class="price-tag">0₫</span>
                            <span class="price-tag" id="maxPrice">200.000₫</span>
                        </div>
                    </div>
                </div>

                <!-- Rating Filter -->
                <div class="filter-section">
                    <label class="filter-label">Đánh giá tối thiểu</label>
                                            <select id="ratingFilter" class="rating-select">
                            <option value="0" {{ request('min_rating', 0) == 0 ? 'selected' : '' }}>Tất cả</option>
                            <option value="3" {{ request('min_rating') == 3 ? 'selected' : '' }}>3+ sao</option>
                            <option value="4" {{ request('min_rating') == 4 ? 'selected' : '' }}>4+ sao</option>
                            <option value="4.5" {{ request('min_rating') == 4.5 ? 'selected' : '' }}>4.5+ sao</option>
                        </select>
                </div>

                <button class="reset-btn" onclick="resetFilters()">Xóa bộ lọc</button>
            </div>
        </aside>

        <!-- Results -->
        <div class="results-container">
            <div class="food-grid" id="foodGrid">
                @if(isset($products) && $products->count() > 0)
                    @foreach($products as $product)
                    <div class="product-card group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow"
                    data-product-id="{{ $product->id }}"
                    data-variant-id="{{ $product->first_variant ? $product->first_variant->id : '' }}"
                    data-has-stock="{{ $product->has_stock ? 'true' : 'false' }}">

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
                            <h3 class="font-medium text-lg mb-1 hover:text-orange-500 transition-colors line-clamp-1">
                                {{ $product->name }}
                            </h3>
                        </a>

                        <p class="text-gray-500 text-sm mb-3 line-clamp-2">
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
                @endif
            </div>

            <div class="no-results" id="noResults" style="display: none;">
                <i class="fas fa-search no-results-icon"></i>
                <h3>Không tìm thấy kết quả</h3>
                <p>Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc</p>
            </div>
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

        // Apply filters
        function applyFilters() {
            const searchQuery = document.getElementById('searchInput').value.toLowerCase();
            const selectedCategory = document.querySelector('input[name="category"]:checked').value;
            const maxPrice = parseInt(document.getElementById('priceRange').value);
            const minRating = parseFloat(document.getElementById('ratingFilter').value);
            const sortBy = document.getElementById('sortSelect').value;

            const foodCards = document.querySelectorAll('.food-card');
            let visibleCount = 0;

            foodCards.forEach(card => {
                const productName = card.querySelector('.card-title').textContent.toLowerCase();
                const productDesc = card.querySelector('.card-description').textContent.toLowerCase();
                const category = card.dataset.category;
                const price = parseInt(card.dataset.price);
                const rating = parseFloat(card.dataset.rating);

                const matchesSearch =
                    productName.includes(searchQuery) ||
                    productDesc.includes(searchQuery);
                const matchesCategory = selectedCategory === 'Tất cả' || category === selectedCategory;
                const matchesPrice = price <= maxPrice;
                const matchesRating = rating >= minRating;

                if (matchesSearch && matchesCategory && matchesPrice && matchesRating) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Update results count
            document.getElementById('resultsCount').textContent = visibleCount;

            // Show/hide no results message
            const noResults = document.getElementById('noResults');
            const foodGrid = document.getElementById('foodGrid');

            if (visibleCount === 0) {
                foodGrid.style.display = 'none';
                noResults.style.display = 'block';
            } else {
                foodGrid.style.display = 'grid';
                noResults.style.display = 'none';
            }
        }

        // Reset filters
        function resetFilters() {
            // Redirect to search page without filters
            window.location.href = "{{ route('customer.search') }}";
        }

        // Update price display
        function updatePriceDisplay() {
            const value = parseInt(document.getElementById('priceRange').value);
            document.getElementById('maxPrice').textContent = value.toLocaleString('vi-VN') + '₫';
            applyFilters();
        }

        // Initialize price display on page load
        function initializePriceDisplay() {
            const value = parseInt(document.getElementById('priceRange').value);
            document.getElementById('maxPrice').textContent = value.toLocaleString('vi-VN') + '₫';
        }

        // Submit form function
        function submitSearchForm() {
            const form = document.getElementById('searchForm');
            const searchInput = document.getElementById('searchInput');
            const selectedCategory = document.querySelector('input[name="category"]:checked').value;
            const maxPrice = document.getElementById('priceRange').value;
            const minRating = document.getElementById('ratingFilter').value;
            const sortBy = document.getElementById('sortSelect').value;

            // Add hidden inputs for filters
            let existingInputs = form.querySelectorAll('input[name="category"], input[name="max_price"], input[name="min_rating"], input[name="sort"]');
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
            if (maxPrice !== '200000') {
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

            // Submit form
            form.submit();
        }

        // Event listeners
        document.getElementById('searchInput').addEventListener('input', applyFilters);
        document.getElementById('sortSelect').addEventListener('change', function() {
            // Use AJAX for sorting
            loadProductsWithAjax();
        });
        document.getElementById('ratingFilter').addEventListener('change', applyFilters);
        document.getElementById('priceRange').addEventListener('input', updatePriceDisplay);

        // Category filter event listeners
        document.querySelectorAll('input[name="category"]').forEach(radio => {
            radio.addEventListener('change', applyFilters);
        });

        // Form submit event
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            submitSearchForm();
        });

        // Enter key press on search input
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                submitSearchForm();
            }
        });

        // Search button click
        document.querySelector('.search-submit-btn').addEventListener('click', function(e) {
            e.preventDefault();
            submitSearchForm();
        });

        // Initialize on page load
        initializePriceDisplay();

        // Load products with AJAX
        function loadProductsWithAjax() {
            const searchQuery = document.getElementById('searchInput').value;
            const selectedCategory = document.querySelector('input[name="category"]:checked').value;
            const maxPrice = document.getElementById('priceRange').value;
            const minRating = document.getElementById('ratingFilter').value;
            const sortBy = document.getElementById('sortSelect').value;

            // Debug: log the parameters
            console.log('AJAX Parameters:', {
                search: searchQuery,
                category: selectedCategory,
                maxPrice: maxPrice,
                minRating: minRating,
                sortBy: sortBy
            });

            // Show loading state
            const foodGrid = document.getElementById('foodGrid');
            const noResults = document.getElementById('noResults');

            // Create loading element
            const loadingHtml = `
                <div style="grid-column: 1 / -1; text-align: center; padding: 2rem;">
                    <i class="fas fa-spinner fa-spin fa-2x text-orange-500"></i>
                    <p style="margin-top: 1rem; color: #6b7280;">Đang tải...</p>
                </div>
            `;

            foodGrid.innerHTML = loadingHtml;
            noResults.style.display = 'none';

            // Prepare form data
            const formData = new FormData();
            formData.append('search', searchQuery);
            formData.append('category', selectedCategory);
            formData.append('max_price', maxPrice);
            formData.append('min_rating', minRating);
            formData.append('sort', sortBy);
            formData.append('_token', '{{ csrf_token() }}');

            // Make AJAX request
            fetch('{{ route("customer.search.ajax") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Debug: log the response
                    console.log('AJAX Response:', data);

                    // Update results count
                    document.getElementById('resultsCount').textContent = data.total;
                    console.log('Updated results count to:', data.total);

                    // Update product grid
                    foodGrid.innerHTML = data.html;

                    // Show/hide no results message
                    if (data.total === 0) {
                        foodGrid.style.display = 'none';
                        noResults.style.display = 'block';
                    } else {
                        foodGrid.style.display = 'grid';
                        noResults.style.display = 'none';
                    }
                } else {
                    console.error('Error loading products:', data.message);
                    foodGrid.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: #ef4444;">Có lỗi xảy ra khi tải dữ liệu</div>';
                }
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                foodGrid.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: #ef4444;">Có lỗi xảy ra khi tải dữ liệu</div>';
            });
        }

        // Make resetFilters globally available
        window.resetFilters = resetFilters;
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
