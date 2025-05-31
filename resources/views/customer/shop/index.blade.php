@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Thực Đơn')

@section('content')
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
                <form id="search-form" class="flex">
                    <input type="text" name="search" id="search-input" value="{{ request('search') }}" placeholder="Tìm kiếm món ăn..." class="w-full md:w-80 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </form>
            </div>
            
            <div class="flex items-center gap-2 w-full md:w-auto">
                <span class="text-gray-600">Sắp xếp theo:</span>
                <select id="sort-select" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Phổ biến nhất</option>
                    <option value="price-asc" {{ request('sort') == 'price-asc' ? 'selected' : '' }}>Giá: Thấp đến cao</option>
                    <option value="price-desc" {{ request('sort') == 'price-desc' ? 'selected' : '' }}>Giá: Cao đến thấp</option>
                    <option value="name-asc" {{ request('sort') == 'name-asc' ? 'selected' : '' }}>Tên: A-Z</option>
                </select>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <a href="javascript:void(0)" class="category-btn px-4 py-2 {{ !request('category') ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700' }} rounded-full hover:bg-orange-600 hover:text-white transition-colors" data-category="">
                Tất cả
            </a>
            @foreach($categories as $category)
                <a href="javascript:void(0)" class="category-btn px-4 py-2 {{ request('category') == $category->id ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700' }} rounded-full hover:bg-orange-600 hover:text-white transition-colors" data-category="{{ $category->id }}">
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
                data-variant-id="{{ $product->first_variant ? $product->first_variant->id : '' }}">
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
                        <button class="add-to-cart-btn">
                            <i class="fas fa-shopping-cart"></i>
                            Thêm
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-4 text-center py-8">
                <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-xl font-bold text-gray-700 mb-2">Không tìm thấy sản phẩm</h3>
                <p class="text-gray-500">Không có sản phẩm nào phù hợp với tiêu chí tìm kiếm của bạn.</p>
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
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Pusher
    const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
        cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
        encrypted: true
    });
    
    // Login popup handling
    const loginPopup = document.getElementById('login-popup');
    const closeLoginPopup = document.getElementById('close-login-popup');
    
    // Close popup when close button is clicked
    if (closeLoginPopup) {
        closeLoginPopup.addEventListener('click', function() {
            loginPopup.classList.add('hidden');
        });
    }
    
    // Close popup when clicking outside the modal
    if (loginPopup) {
        loginPopup.addEventListener('click', function(e) {
            if (e.target === loginPopup) {
                loginPopup.classList.add('hidden');
            }
        });
    }
    
    // Show login popup when login-prompt-btn is clicked
    document.querySelectorAll('.login-prompt-btn').forEach(button => {
        button.addEventListener('click', function() {
            loginPopup.classList.remove('hidden');
        });
    });
    
    // Subscribe to channels
    const productsChannel = pusher.subscribe('products-channel');
    const favoritesChannel = pusher.subscribe('user-wishlist-channel');
    const cartChannel = pusher.subscribe('user-cart-channel');
    
    // Listen for product update events
    productsChannel.bind('product-updated', function(data) {
        updateProductCard(data.product);
    });
    
    productsChannel.bind('product-created', function() {
        // Reload the page to show the new product
        window.location.reload();
    });
    
    // Listen for favorite updates
    favoritesChannel.bind('favorite-updated', function(data) {
        if (data.product_id) {
            const favoriteButtons = document.querySelectorAll(`.favorite-btn[data-product-id="${data.product_id}"]`);
            favoriteButtons.forEach(button => {
                const icon = button.querySelector('i');
                if (data.is_favorite) {
                    icon.classList.remove('far');
                    icon.classList.add('fas', 'text-red-500');
                } else {
                    icon.classList.remove('fas', 'text-red-500');
                    icon.classList.add('far');
                }
            });
        }
    });
    
    // Listen for cart events
    cartChannel.bind('cart-updated', function(data) {
        updateCartCount(data.count);
    });
    
    // Function to update product card
    function updateProductCard(product) {
        const productCard = document.querySelector(`.product-card[data-product-id="${product.id}"]`);
        if (!productCard) return;
        
        // Update price
        const priceElement = productCard.querySelector('.product-price');
        if (product.discount_price && product.base_price > product.discount_price) {
            priceElement.textContent = `${new Intl.NumberFormat('vi-VN').format(product.discount_price)}đ`;
            
            // Update original price if exists
            let originalPriceElement = productCard.querySelector('.product-original-price');
            if (!originalPriceElement) {
                originalPriceElement = document.createElement('span');
                originalPriceElement.className = 'product-original-price';
                priceElement.parentNode.appendChild(originalPriceElement);
            }
            originalPriceElement.textContent = `${new Intl.NumberFormat('vi-VN').format(product.base_price)}đ`;
            
            // Update discount badge
            const discountPercent = Math.round(((product.base_price - product.discount_price) / product.base_price) * 100);
            let badgeElement = productCard.querySelector('.badge-sale');
            if (!badgeElement) {
                badgeElement = document.createElement('span');
                badgeElement.className = 'custom-badge badge-sale';
                productCard.querySelector('.relative').appendChild(badgeElement);
            }
            badgeElement.textContent = `-${discountPercent}%`;
        } else {
            priceElement.textContent = `${new Intl.NumberFormat('vi-VN').format(product.base_price)}đ`;
            
            // Remove original price if exists
            const originalPriceElement = productCard.querySelector('.product-original-price');
            if (originalPriceElement) {
                originalPriceElement.remove();
            }
            
            // Remove discount badge if exists
            const badgeElement = productCard.querySelector('.badge-sale');
            if (badgeElement) {
                badgeElement.remove();
            }
        }
    }
    
    // Function to update cart counter
    function updateCartCount(count) {
        const cartCounter = document.querySelector('#cart-counter');
        if (cartCounter) {
            cartCounter.textContent = count;
            
            // Animation to highlight the change
            cartCounter.classList.add('animate-bounce');
            setTimeout(() => {
                cartCounter.classList.remove('animate-bounce');
            }, 1000);
        }
    }

    // Sort handling with AJAX
    const sortSelect = document.getElementById('sort-select');
    const productsGrid = document.querySelector('.grid.grid-cols-1.sm\\:grid-cols-2.md\\:grid-cols-3.lg\\:grid-cols-4.gap-6');
    const paginationContainer = document.querySelector('.pagination-container');
    
    if (sortSelect && productsGrid) {
        sortSelect.addEventListener('change', function() {
            // Show loading state
            productsGrid.innerHTML = '<div class="col-span-4 flex justify-center py-12"><i class="fas fa-spinner fa-spin fa-3x text-orange-500"></i></div>';
            
            // Get current filters
            const currentUrl = new URL(window.location.href);
            const category = currentUrl.searchParams.get('category') || '';
            const search = currentUrl.searchParams.get('search') || '';
            
            // Fetch products via AJAX
            axios.get('/api/products', {
                params: {
                    sort: this.value,
                    category: category,
                    search: search,
                    page: 1 // Reset to first page when sorting changes
                }
            })
            .then(response => {
                if (response.data.success) {
                    // Update URL without reloading the page
            currentUrl.searchParams.set('sort', this.value);
                    window.history.pushState({}, '', currentUrl.toString());
                    
                    // Render products
                    renderProducts(response.data.products);
                    
                    // Update pagination
                    renderPagination(response.data.pagination);
                }
            })
            .catch(error => {
                console.error('Error fetching products:', error);
                productsGrid.innerHTML = `
                    <div class="col-span-4 text-center py-8">
                        <i class="fas fa-exclamation-circle text-red-500 text-4xl mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">Đã xảy ra lỗi</h3>
                        <p class="text-gray-500">Không thể tải sản phẩm. Vui lòng thử lại sau.</p>
                    </div>
                `;
            });
        });
    }
    
    // Function to render products
    function renderProducts(products) {
        if (products.length === 0) {
            productsGrid.innerHTML = `
                <div class="col-span-4 text-center py-8">
                    <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Không tìm thấy sản phẩm</h3>
                    <p class="text-gray-500">Không có sản phẩm nào phù hợp với tiêu chí tìm kiếm của bạn.</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        
        products.forEach(product => {
            const imageUrl = product.primary_image ? product.primary_image.s3_url : '';
            const isFavorite = product.is_favorite ? 'fas fa-heart text-red-500' : 'far fa-heart';
            const hasDiscount = product.discount_price && product.base_price > product.discount_price;
            const discountPercent = hasDiscount ? Math.round(((product.base_price - product.discount_price) / product.base_price) * 100) : 0;
            const isNew = new Date(product.created_at).getTime() > new Date().getTime() - (7 * 24 * 60 * 60 * 1000);
            
            // Check if user is authenticated
            const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
            
            html += `
                <div class="product-card bg-white rounded-lg overflow-hidden" 
                    data-product-id="${product.id}"
                    data-variant-id="${product.first_variant ? product.first_variant.id : ''}">
                    <div class="relative">
                        <a href="/shop/products/${product.id}" class="block">
                            ${imageUrl ? 
                                `<img src="${imageUrl}" alt="${product.name}" class="product-image">` : 
                                `<div class="no-image-placeholder">
                                    <i class="far fa-image"></i>
                                </div>`
                            }
                        </a>

                        ${isAuthenticated ? 
                            `<button class="favorite-btn" data-product-id="${product.id}">
                                <i class="${isFavorite}"></i>
                            </button>` : 
                            `<button class="favorite-btn login-prompt-btn">
                                <i class="far fa-heart"></i>
                            </button>`
                        }

                        ${hasDiscount ? 
                            `<span class="custom-badge badge-sale">-${discountPercent}%</span>` : 
                            (isNew ? `<span class="custom-badge badge-new">Mới</span>` : '')
                        }
                    </div>

                    <div class="p-4">
                        <div class="flex items-center mb-2">
                            <div class="rating-stars flex">
                                ${renderStars(product.average_rating)}
                            </div>
                            <span class="rating-count ml-2">(${product.reviews_count})</span>
                        </div>

                        <a href="/shop/products/${product.id}" class="block">
                            <h3 class="product-title">${product.name}</h3>
                        </a>

                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            ${product.short_description || ''}
                        </p>

                        <div class="flex justify-between items-center">
                            <div class="flex flex-col">
                                ${hasDiscount ? 
                                    `<span class="product-price">${new Intl.NumberFormat('vi-VN').format(product.discount_price)}đ</span>
                                     <span class="product-original-price">${new Intl.NumberFormat('vi-VN').format(product.base_price)}đ</span>` :
                                    `<span class="product-price">${new Intl.NumberFormat('vi-VN').format(product.base_price)}đ</span>`
                                }
                            </div>
                            <button class="add-to-cart-btn">
                                <i class="fas fa-shopping-cart"></i>
                                Thêm
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        productsGrid.innerHTML = html;
        
        // Reattach event listeners
        attachEventListeners();
    }
    
    // Function to render stars
    function renderStars(rating) {
        let starsHtml = '';
        for(let i = 1; i <= 5; i++) {
            if(i <= Math.floor(rating)) {
                starsHtml += '<i class="fas fa-star"></i>';
            } else if(i - 0.5 <= rating) {
                starsHtml += '<i class="fas fa-star-half-alt"></i>';
            } else {
                starsHtml += '<i class="far fa-star"></i>';
            }
        }
        return starsHtml;
    }
    
    // Function to render pagination
    function renderPagination(pagination) {
        if (!paginationContainer) return;
        
        if (pagination.last_page <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }
        
        const currentUrl = new URL(window.location.href);
        let html = '';
        
        // Previous page link
        if (pagination.current_page === 1) {
            html += `
                <span class="pagination-item disabled">
                    <i class="fas fa-chevron-left"></i>
                </span>
            `;
        } else {
            const prevUrl = new URL(currentUrl);
            prevUrl.searchParams.set('page', pagination.current_page - 1);
            html += `
                <a href="${prevUrl.toString()}" class="pagination-item pagination-link" data-page="${pagination.current_page - 1}">
                    <i class="fas fa-chevron-left"></i>
                </a>
            `;
        }
        
        // Page numbers
        for (let i = 1; i <= pagination.last_page; i++) {
            const pageUrl = new URL(currentUrl);
            pageUrl.searchParams.set('page', i);
            
            if (i === pagination.current_page) {
                html += `<span class="pagination-item active">${i}</span>`;
            } else {
                html += `<a href="${pageUrl.toString()}" class="pagination-item pagination-link" data-page="${i}">${i}</a>`;
            }
        }
        
        // Next page link
        if (pagination.current_page === pagination.last_page) {
            html += `
                <span class="pagination-item disabled">
                    <i class="fas fa-chevron-right"></i>
                </span>
            `;
        } else {
            const nextUrl = new URL(currentUrl);
            nextUrl.searchParams.set('page', pagination.current_page + 1);
            html += `
                <a href="${nextUrl.toString()}" class="pagination-item pagination-link" data-page="${pagination.current_page + 1}">
                    <i class="fas fa-chevron-right"></i>
                </a>
            `;
        }
        
        paginationContainer.innerHTML = html;
        
        // Add event listeners to pagination links
        document.querySelectorAll('.pagination-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Get current filters
                const currentUrl = new URL(window.location.href);
                const category = currentUrl.searchParams.get('category') || '';
                const search = currentUrl.searchParams.get('search') || '';
                const sort = currentUrl.searchParams.get('sort') || 'popular';
                const page = this.dataset.page;
                
                // Show loading state
                productsGrid.innerHTML = '<div class="col-span-4 flex justify-center py-12"><i class="fas fa-spinner fa-spin fa-3x text-orange-500"></i></div>';
                
                // Fetch products via AJAX
                axios.get('/api/products', {
                    params: {
                        sort: sort,
                        category: category,
                        search: search,
                        page: page
                    }
                })
                .then(response => {
                    if (response.data.success) {
                        // Update URL without reloading the page
                        currentUrl.searchParams.set('page', page);
                        window.history.pushState({}, '', currentUrl.toString());
                        
                        // Render products
                        renderProducts(response.data.products);
                        
                        // Update pagination
                        renderPagination(response.data.pagination);
                        
                        // Scroll to top of products
                        window.scrollTo({
                            top: productsGrid.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching products:', error);
                });
            });
        });
    }
    
    // Function to attach event listeners to newly rendered products
    function attachEventListeners() {
        // Favorite button handling
        document.querySelectorAll('.favorite-btn:not(.login-prompt-btn)').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const productCard = this.closest('.product-card');
                const productId = productCard.dataset.productId;
                const icon = this.querySelector('i');
                const isFavorite = icon.classList.contains('far');
                
                // Immediate visual effect
                if (isFavorite) {
                    icon.classList.remove('far');
                    icon.classList.add('fas', 'text-red-500');
                } else {
                    icon.classList.remove('fas', 'text-red-500');
                    icon.classList.add('far');
                }
                
                // AJAX call to update favorites
                axios.post('/api/favorites/toggle', {
                    product_id: productId,
                    is_favorite: isFavorite
                })
                .then(response => {
                    if (response.data.success) {
                        showToast(response.data.message);
                    }
                })
                .catch(error => {
                    // Revert visual change if error
                    if (isFavorite) {
                        icon.classList.remove('fas', 'text-red-500');
                        icon.classList.add('far');
                    } else {
                        icon.classList.remove('far');
                        icon.classList.add('fas', 'text-red-500');
                    }
                    console.error('Error updating favorites:', error);
                    showToast('Đã xảy ra lỗi. Vui lòng thử lại.');
                });
            });
        });
        
        // Login prompt button handling
        document.querySelectorAll('.login-prompt-btn').forEach(button => {
            button.addEventListener('click', function() {
                const loginPopup = document.getElementById('login-popup');
                loginPopup.classList.remove('hidden');
            });
        });
        
        // Add to cart button handling
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productCard = this.closest('.product-card');
                const productId = productCard.dataset.productId;
                const variantId = productCard.dataset.variantId;
                const productName = productCard.querySelector('h3').textContent;
                
                // Check if a variant is available
                if (!variantId) {
                    showToast('Sản phẩm này tạm hết hàng');
                    return;
                }
                
                // Add immediate visual effect
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                
                // AJAX call to add to cart
                axios.post('/api/cart/add', {
                    product_id: productId,
                    variant_id: variantId,
                    quantity: 1
                })
                .then(response => {
                    if (response.data.success) {
                        // Update cart counter using global function
                        if (window.updateCartCount) {
                            window.updateCartCount(response.data.cart_count);
                        }
                        
                        this.innerHTML = '<i class="fas fa-check"></i>';
                        showToast(`Đã thêm ${productName} vào giỏ hàng`);
                        
                        setTimeout(() => {
                            this.innerHTML = '<i class="fas fa-shopping-cart"></i> Thêm';
                        }, 1500);
                    }
                })
                .catch(error => {
                    console.error('Error adding to cart:', error);
                    
                    // Show error details if available
                    if (error.response && error.response.data) {
                        console.error('Error details:', error.response.data);
                        if (error.response.data.message) {
                            showToast(error.response.data.message);
                        }
                    }
                    
                    this.innerHTML = '<i class="fas fa-exclamation-circle"></i>';
                    showToast('Đã xảy ra lỗi. Vui lòng thử lại.');
            
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-shopping-cart"></i> Thêm';
            }, 1500);
                });
            });
        });
    }
    
    // Attach initial event listeners
    attachEventListeners();
    
    // Category filter handling with AJAX
    const categoryButtons = document.querySelectorAll('.category-btn');
    
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active state
            categoryButtons.forEach(btn => {
                btn.classList.remove('bg-orange-500', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-700');
            });
            this.classList.remove('bg-gray-100', 'text-gray-700');
            this.classList.add('bg-orange-500', 'text-white');
            
            // Show loading state
            productsGrid.innerHTML = '<div class="col-span-4 flex justify-center py-12"><i class="fas fa-spinner fa-spin fa-3x text-orange-500"></i></div>';
            
            // Get current filters
            const currentUrl = new URL(window.location.href);
            const search = currentUrl.searchParams.get('search') || '';
            const sort = currentUrl.searchParams.get('sort') || 'popular';
            const category = this.dataset.category;
            
            // Fetch products via AJAX
            axios.get('/api/products', {
                params: {
                    sort: sort,
                    category: category,
                    search: search,
                    page: 1 // Reset to first page when category changes
                }
            })
            .then(response => {
                if (response.data.success) {
                    // Update URL without reloading the page
                    if (category) {
                        currentUrl.searchParams.set('category', category);
                    } else {
                        currentUrl.searchParams.delete('category');
                    }
                    currentUrl.searchParams.set('page', 1);
                    window.history.pushState({}, '', currentUrl.toString());
                    
                    // Render products
                    renderProducts(response.data.products);
                    
                    // Update pagination
                    renderPagination(response.data.pagination);
                    
                    // Scroll to top of products if needed
                    if (window.scrollY > productsGrid.offsetTop) {
                        window.scrollTo({
                            top: productsGrid.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching products:', error);
                productsGrid.innerHTML = `
                    <div class="col-span-4 text-center py-8">
                        <i class="fas fa-exclamation-circle text-red-500 text-4xl mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">Đã xảy ra lỗi</h3>
                        <p class="text-gray-500">Không thể tải sản phẩm. Vui lòng thử lại sau.</p>
                    </div>
                `;
            });
        });
    });
    
    // Toast notification function
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

    // Search form handling with AJAX
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search-input');
    
    if (searchForm && searchInput) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const searchValue = searchInput.value.trim();
            
            // Show loading state
            productsGrid.innerHTML = '<div class="col-span-4 flex justify-center py-12"><i class="fas fa-spinner fa-spin fa-3x text-orange-500"></i></div>';
            
            // Get current filters
            const currentUrl = new URL(window.location.href);
            const category = currentUrl.searchParams.get('category') || '';
            const sort = currentUrl.searchParams.get('sort') || 'popular';
            
            // Fetch products via AJAX
            axios.get('/api/products', {
                params: {
                    sort: sort,
                    category: category,
                    search: searchValue,
                    page: 1 // Reset to first page when search changes
                }
            })
            .then(response => {
                if (response.data.success) {
                    // Update URL without reloading the page
                    if (searchValue) {
                        currentUrl.searchParams.set('search', searchValue);
                    } else {
                        currentUrl.searchParams.delete('search');
                    }
                    currentUrl.searchParams.set('page', 1);
                    window.history.pushState({}, '', currentUrl.toString());
                    
                    // Render products
                    renderProducts(response.data.products);
                    
                    // Update pagination
                    renderPagination(response.data.pagination);
                }
            })
            .catch(error => {
                console.error('Error fetching products:', error);
                productsGrid.innerHTML = `
                    <div class="col-span-4 text-center py-8">
                        <i class="fas fa-exclamation-circle text-red-500 text-4xl mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">Đã xảy ra lỗi</h3>
                        <p class="text-gray-500">Không thể tải sản phẩm. Vui lòng thử lại sau.</p>
                    </div>
                `;
            });
        });
        
        // Also handle "Enter" key press
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchForm.dispatchEvent(new Event('submit'));
            }
        });
    }
});
</script>
@endsection