@php
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Trang Chủ')

@section('content')
<!-- Add meta tag for selected branch -->
@if(isset($selectedBranch))
<meta name="selected-branch" content="{{ $selectedBranch->id }}">
@endif

<style>
   /* Example for badges - adjust to your styling system */
    .custom-badge {
        font-size: 0.75rem; /* 12px */
        padding: 0.25rem 0.5rem; /* py-1 px-2 */
        border-radius: 0.25rem; /* rounded */
        color: white;
        font-weight: bold;
    }
    .badge-sale {
        background-color: #EF4444; /* bg-red-500 */
    }
    .badge-new {
        background-color: #22C55E; /* bg-green-500 */
    }
    .product-card .no-image-placeholder { /* If you use this style for placeholder */
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%; /* Or specific height like h-48 */
        background-color: #f3f4f6; /* bg-gray-100 */
    }
    .product-card .no-image-placeholder i {
        font-size: 2rem; /* text-3xl */
        color: #9ca3af; /* text-gray-400 */
    }
</style>
<!-- Banner/Slider -->
<div class="relative h-[300px] sm:h-[400px] md:h-[500px] overflow-hidden" id="banner-slider">
    {{-- @foreach ($banners as $index => $banner)
        <div class="banner-slide absolute inset-0 transition-opacity duration-1000 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}">
            <div class="relative h-full w-full">
                <img src="{{ Str::startsWith($banner->image_path, ['http://', 'https://']) ? $banner->image_path : Storage::disk('s3')->url($banner->image_path) }}" alt="{{ $banner->title }}" class="object-cover w-full h-full">
                <div class="absolute inset-0 bg-black/30"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center text-center text-white p-4">
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-2 sm:mb-4">{{ $banner->title }}</h2>
                    <p class="text-sm sm:text-base md:text-lg mb-4 sm:mb-6 max-w-md">{{ $banner->description }}</p>
                    <a href="{{ $banner->link }}" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-md font-medium transition-colors">
                        Xem Thêm
                    </a>
                </div>
            </div>
        </div>
    @endforeach --}}

        {{-- @php
            $banners = app('App\Http\Controllers\Customer\BannerController')->getBannersByPosition('homepage');
        @endphp --}}
        {{-- tam thoi cmt lai --}}
        @include('components.banner', ['banners' => $banners])

        <button
            class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/30 text-white hover:bg-black/50 z-10 p-2 rounded-full"
            id="prev-slide">
            <i class="fas fa-chevron-left h-6 w-6"></i>
        </button>

        <button
            class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/30 text-white hover:bg-black/50 z-10 p-2 rounded-full"
            id="next-slide">
            <i class="fas fa-chevron-right h-6 w-6"></i>
        </button>

        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10" id="slider-dots">
            @foreach ($banners as $index => $banner)
                <button class="w-2 h-2 rounded-full {{ $index === 0 ? 'bg-white' : 'bg-white/50' }}"
                    data-index="{{ $index }}"></button>
            @endforeach
        </div>
    </div>

    <div class="max-w-[1240px] mx-auto w-full"> 

    <div class="container mx-auto px-4 py-8">
        <!-- Categories Section -->
        <section class="py-10">
            <h2 class="text-2xl md:text-3xl font-bold mb-6 text-center">Danh Mục Món Ăn</h2>

        <div class="swiper category-slider">
            <div class="swiper-wrapper">
                @foreach ($categories as $category)
                    <div class="swiper-slide">
                        <a href="{{ url('/shop/products?category=' . $category->id ) }}" class="group flex flex-col items-center text-center transition-transform hover:scale-105">
                            <div class="relative w-24 h-24 mb-3 rounded-full overflow-hidden border-2 border-orange-500 p-1">
                                <div class="w-full h-full rounded-full overflow-hidden">
                                    @php
                                    $imagePath = $category->image ?? 'categories/default-logo.avif';
                                    @endphp
                                    <img src="{{ Storage::disk('s3')->url($imagePath) }}" alt="{{ $category->name }}" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                                </div>
                            </div>
                            <h3 class="font-medium text-sm">{{ $category->name }}</h3>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Combo Nổi Bật Section -->
    @if(isset($featuredCombos) && $featuredCombos->count() > 0)
    <section class="py-10">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl md:text-3xl font-bold">Combo Nổi Bật</h2>
            <a href="{{ route('customer.search', ['type' => 'combos']) }}" class="text-orange-500 hover:text-orange-600 flex items-center"> Xem tất cả
                <i class="fas fa-arrow-right h-4 w-4 ml-1"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach ($featuredCombos as $combo)
                <div class="product-card group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow"
                    data-combo-id="{{ $combo->id }}"
                    data-has-stock="{{ $combo->has_stock ? 'true' : 'false' }}">
                    <div class="relative">
                        <a href="{{ route('combos.show', $combo->slug) }}">
                            <img src="{{ $combo->image_url }}" alt="{{ $combo->name }}" class="object-cover w-full h-48 group-hover:scale-110 transition-transform duration-300">
                            <div class="absolute top-2 left-2">
                                @if($combo->discount_percent > 0)
                                    <span class="custom-badge badge-sale text-xs bg-red-500 text-white px-2 py-1 rounded">-{{ $combo->discount_percent }}%</span>
                                @elseif($combo->created_at->diffInDays(now()) <= 7)
                                    <span class="custom-badge badge-new text-xs bg-green-500 text-white px-2 py-1 rounded">Mới</span>
                                @endif
                            </div>
                        </a>
                    </div>
                    <div class="p-4">
                        <a href="{{ route('combos.show', $combo->slug) }}">
                            <h3 class="font-medium text-lg mb-1 hover:text-orange-500 transition-colors line-clamp-1">{{ $combo->name }}</h3>
                        </a>
                        <p class="text-gray-500 text-sm mb-3 line-clamp-2">{{ Illuminate\Support\Str::limit($combo->description, 80) }}</p>
                        <div class="flex items-center justify-between">
                            <div class="flex flex-col">
                                @if($combo->original_price && $combo->original_price > $combo->price)
                                    <span class="font-bold text-lg text-black-600">{{ number_format($combo->price, 0, ',', '.') }}đ</span>
                                    <span class="text-sm text-gray-500 line-through">{{ number_format($combo->original_price, 0, ',', '.') }}đ</span>
                                @else
                                    <span class="font-bold text-lg">{{ number_format($combo->price, 0, ',', '.') }}đ</span>
                                @endif
                            </div>
                            @if($combo->has_stock)
                                <button class="add-to-cart-btn bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md text-sm flex items-center transition-colors" data-combo-id="{{ $combo->id }}">
                                    <i class="fas fa-shopping-cart h-4 w-4 mr-1"></i>
                                    Thêm
                                </button>
                            @else
                                <span class="add-to-cart-btn bg-gray-400 text-white px-3 py-1 rounded-md text-sm flex items-center transition-colors cursor-not-allowed" disabled>
                                    <i class="fas fa-ban h-4 w-4 mr-1"></i>
                                    Hết hàng
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif


    <section class="py-10">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl md:text-3xl font-bold">Sản Phẩm Nổi Bật</h2>
            <a href="{{ route('products.index') }}" class="text-orange-500 hover:text-orange-600 flex items-center"> Xem tất cả
                <i class="fas fa-arrow-right h-4 w-4 ml-1"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse ($featuredProducts as $product)
                <div class="product-card group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow"
                    data-product-id="{{ $product->id }}"
                    data-variant-id="{{ $product->first_variant ? $product->first_variant->id : '' }}"
                    data-has-stock="{{ $product->has_stock ? 'true' : 'false' }}">

                    <div class="relative">
                        <a href="{{ route('products.show', $product->slug) }}" class="block relative h-48 overflow-hidden">
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

                        <a href="{{ route('products.show', $product->slug) }}">
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
            @empty
                <div class="col-span-full text-center py-8">
                    <i class="fas fa-box-open text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Chưa có sản phẩm nổi bật</h3>
                    <p class="text-gray-500">Vui lòng quay lại sau để xem các sản phẩm nổi bật nhé!</p>
                </div>
            @endforelse
        </div>
    </section>


    <section class="py-10">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl md:text-3xl font-bold">Sản Phẩm Được Yêu Thích Nhất</h2>
            <a href="{{ route('products.index') }}" class="text-orange-500 hover:text-orange-600 flex items-center"> Xem tất cả
                <i class="fas fa-arrow-right h-4 w-4 ml-1"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse ($topRatedProducts as $product)
                <div class="product-card group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow"
                    data-product-id="{{ $product->id }}"
                    data-variant-id="{{ $product->first_variant ? $product->first_variant->id : '' }}"
                    data-has-stock="{{ $product->has_stock ? 'true' : 'false' }}">

                    <div class="relative">
                        <a href="{{ route('products.show', $product->slug) }}" class="block relative h-48 overflow-hidden">
                                <img src="{{ $product->primary_image_url ?? asset('images/default-placeholder.png') }}"
                                alt="{{ $product->name }}" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                        </a>
                    </div>

                    <div class="p-4">
                        <div class="flex items-center gap-1 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($product->average_rating))
                                    <i class="fas fa-star text-yellow-400"></i>
                                @elseif($i - 0.5 <= $product->average_rating)
                                    <i class="fas fa-star-half-alt text-yellow-400"></i>
                                @else
                                    <i class="far fa-star text-yellow-400"></i>
                                @endif
                            @endfor
                            <span class="text-xs text-gray-500 ml-1">({{ $product->reviews_count }})</span>
                        </div>

                        <a href="{{ route('products.show', $product->slug) }}">
                            <h3 class="font-medium text-lg mb-1 hover:text-orange-500 transition-colors line-clamp-1">
                                {{ $product->name }}
                            </h3>
                        </a>

                        <p class="text-gray-500 text-sm mb-3 line-clamp-2">
                            {{ $product->short_description ?? Illuminate\Support\Str::limit($product->description, 80) }}
                        </p>

                        <div class="flex items-center justify-between">
                            <span class="font-bold text-lg">{{ number_format($product->base_price, 0, ',', '.') }}đ</span>
                            <button class="add-to-cart-btn bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md text-sm flex items-center transition-colors">
                                <i class="fas fa-shopping-cart h-4 w-4 mr-1"></i>
                                Thêm
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-8">
                    <i class="fas fa-box-open text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Chưa có sản phẩm yêu thích</h3>
                    <p class="text-gray-500">Vui lòng quay lại sau để xem các sản phẩm được yêu thích nhé!</p>
                </div>
            @endforelse
        </div>
    </div>
    </div>
    <div class="max-w-[1240px] mx-auto w-full">

    <!-- Order Now Section -->
        <div class="rounded-xl overflow-hidden bg-gradient-to-r from-orange-500 to-red-500 mb-8">
            <div class="grid md:grid-cols-2 gap-6">
                <div class="p-8 md:p-12 flex flex-col justify-center">
                    <h2 class="text-2xl md:text-3xl font-bold mb-4 text-white">Đặt Hàng Ngay!</h2>
                    <p class="text-white/90 mb-6">
                        Đặt hàng trực tuyến và nhận ưu đãi độc quyền. Giao hàng nhanh chóng trong vòng 30 phút.
                    </p>

                    <ul class="space-y-2 mb-6">
                        <li class="flex items-center gap-2 text-white">
                            <i class="fas fa-check h-5 w-5 text-white"></i>
                            <span>Miễn phí giao hàng cho đơn từ 100.000đ</span>
                        </li>
                        <li class="flex items-center gap-2 text-white">
                            <i class="fas fa-check h-5 w-5 text-white"></i>
                            <span>Tích điểm đổi quà hấp dẫn</span>
                        </li>
                        <li class="flex items-center gap-2 text-white">
                            <i class="fas fa-check h-5 w-5 text-white"></i>
                            <span>Nhiều combo tiết kiệm</span>
                        </li>
                        <li class="flex items-center gap-2 text-white">
                            <i class="fas fa-check h-5 w-5 text-white"></i>
                            <span>Đảm bảo chất lượng</span>
                        </li>
                    </ul>

                    <div class="flex flex-wrap gap-4">
                        <a href="/products"
                            class="bg-white text-orange-500 hover:bg-white/90 px-6 py-3 rounded-md font-medium transition-colors">
                            Đặt Hàng Ngay
                        </a>
                        <a href="/products"
                            class="text-white border border-white hover:bg-white/10 px-6 py-3 rounded-md font-medium transition-colors">
                            Xem Thực Đơn
                        </a>
                    </div>
                </div>

                <div class="relative h-60 md:h-auto">
                    <img src="https://marketingai.mediacdn.vn/thumb_w/784/603488451643117568/2024/8/7/thumb-1280-x-800-px-61-1723017058646933002606.png"
                        alt="Đặt hàng ngay" class="object-cover w-full h-full">
                </div>
            </div>
        </div>
    </div>
    
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Banner slider functionality
            const slides = document.querySelectorAll('.banner-slide');
            const dots = document.querySelectorAll('#slider-dots button');
            const prevButton = document.getElementById('prev-slide');
            const nextButton = document.getElementById('next-slide');
            let currentSlide = 0;
            let slideInterval;

            function showSlide(index) {
                // Hide all slides and reset z-index
                slides.forEach((slide, i) => {
                    slide.classList.remove('opacity-100');
                    slide.classList.add('opacity-0');
                    slide.style.zIndex = '1';
                });

                // Show the selected slide and set higher z-index
                slides[index].classList.remove('opacity-0');
                slides[index].classList.add('opacity-100');
                slides[index].style.zIndex = '10';

                // Update dots
                dots.forEach((dot, i) => {
                    if (i === index) {
                        dot.classList.remove('bg-white/50');
                        dot.classList.add('bg-white');
                    } else {
                        dot.classList.remove('bg-white');
                        dot.classList.add('bg-white/50');
                    }
                });

                currentSlide = index;
                
                // Debug log
                console.log('Showing slide:', index, 'Banner ID:', slides[index].dataset.bannerId, 'Link:', slides[index].dataset.bannerLink);
            }

            function nextSlide() {
                let next = currentSlide + 1;
                if (next >= slides.length) next = 0;
                showSlide(next);
            }

            function prevSlide() {
                let prev = currentSlide - 1;
                if (prev < 0) prev = slides.length - 1;
                showSlide(prev);
            }

            // Initialize slider
            function startSlider() {
                slideInterval = setInterval(nextSlide, 3000);
            }

            function stopSlider() {
                clearInterval(slideInterval);
            }

            // Event listeners
            prevButton.addEventListener('click', function() {
                stopSlider();
                prevSlide();
                startSlider();
            });

            nextButton.addEventListener('click', function() {
                stopSlider();
                nextSlide();
                startSlider();
            });

            dots.forEach((dot, index) => {
                dot.addEventListener('click', function() {
                    stopSlider();
                    showSlide(index);
                    startSlider();
                });
            });

        // Start the slider
        startSlider();
    });
    </script>
<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
<script>
    var swiper = new Swiper(".category-slider", {
    slidesPerView: "auto",
    spaceBetween: 10,
    loop: true,
    autoplay: {
        delay: 0, // Loại bỏ thời gian chờ
        disableOnInteraction: false
    },
    speed: 5000, // Tốc độ trượt chậm để tạo cảm giác mượt mà
    grabCursor: true,
    freeMode: true, // Cho phép trượt liên tục, không có điểm dừng
    freeModeMomentum: false, // Giữ tốc độ đều đặn, không có gia tốc
    breakpoints: {
        640: { slidesPerView: 3 },
        768: { slidesPerView: 4 },
        1024: { slidesPerView: 6 }
    }
});
</script>

{{-- Include branch checking logic --}}
@include('partials.customer.branch-check')
<!-- Branch Selector Modal -->

<!-- Thêm biến Pusher cho JS -->
<script>
    window.pusherKey = "{{ config('broadcasting.connections.pusher.key') }}";
    window.pusherCluster = "{{ config('broadcasting.connections.pusher.options.cluster') }}";
    window.csrfToken = '{{ csrf_token() }}';
</script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="/js/chat-realtime.js"></script>
<script src="{{ asset('js/Customer/add-to-cart-direct.js') }}"></script>
@endsection
