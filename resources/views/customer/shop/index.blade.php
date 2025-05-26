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
        left: 8px;
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
                <form action="{{ route('products.index') }}" method="GET" class="flex">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm món ăn..." class="w-full md:w-80 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
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
            <a href="{{ route('products.index') }}" class="category-btn px-4 py-2 {{ !request('category') ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700' }} rounded-full hover:bg-orange-600 hover:text-white transition-colors">
                Tất cả
            </a>
            @foreach($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->id]) }}" class="category-btn px-4 py-2 {{ request('category') == $category->id ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700' }} rounded-full hover:bg-orange-600 hover:text-white transition-colors">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>
    
    <!-- Danh sách sản phẩm -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($products as $product)
            <div class="product-card bg-white rounded-lg overflow-hidden">
                <div class="relative">
                    <a href="{{ route('products.show', $product->id) }}" class="block">
                        @if($product->primary_image)
                            <img src="{{ Storage::disk('s3')->url($product->primary_image->img) }}" 
                                 alt="{{ $product->name }}" 
                                 class="product-image">
                        @else
                            <div class="no-image-placeholder">
                                <i class="far fa-image"></i>
                            </div>
                        @endif
                    </a>

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
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý sắp xếp
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('sort', this.value);
            window.location.href = currentUrl.toString();
        });
    }
    
    // Xử lý nút yêu thích
    const favoriteButtons = document.querySelectorAll('.favorite-btn');
    
    favoriteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const icon = this.querySelector('i');
            
            // Đảo trạng thái yêu thích
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas', 'text-red-500');
                
                // Hiển thị thông báo
                showToast('Đã thêm vào danh sách yêu thích');
            } else {
                icon.classList.remove('fas', 'text-red-500');
                icon.classList.add('far');
                
                // Hiển thị thông báo
                showToast('Đã xóa khỏi danh sách yêu thích');
            }
        });
    });
    
    // Xử lý nút thêm vào giỏ hàng
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productCard = this.closest('.product-card');
            const productName = productCard.querySelector('h3').textContent;
            
            // Hiển thị thông báo
            showToast(`Đã thêm ${productName} vào giỏ hàng`);
            
            // Hiệu ứng khi thêm vào giỏ hàng
            this.innerHTML = '<i class="fas fa-check"></i>';
            
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-shopping-cart"></i> Thêm';
            }, 1500);
        });
    });
    
    // Hàm hiển thị thông báo
    function showToast(message) {
        // Tạo element thông báo
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300 opacity-0';
        toast.textContent = message;
        
        // Thêm vào DOM
        document.body.appendChild(toast);
        
        // Hiển thị thông báo
        setTimeout(() => {
            toast.classList.remove('opacity-0');
            toast.classList.add('opacity-100');
        }, 10);
        
        // Ẩn và xóa thông báo sau 3 giây
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