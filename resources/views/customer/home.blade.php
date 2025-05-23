@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Trang Chủ')

@section('content')
<style>
    .container {
      max-width: 1280px;
      margin: 0 auto;
   }
</style>
<!-- Banner/Slider -->
<div class="relative h-[300px] sm:h-[400px] md:h-[500px] overflow-hidden" id="banner-slider">
    @foreach ($banners as $index => $banner)
        <div class="banner-slide absolute inset-0 transition-opacity duration-1000 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}">
            <div class="relative h-full w-full">
                <img src="{{ asset('storage/' . $banner->image_path) }}" alt="{{ $banner->title }}" class="object-cover w-full h-full">
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
    @endforeach

    <button class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/30 text-white hover:bg-black/50 z-10 p-2 rounded-full" id="prev-slide">
        <i class="fas fa-chevron-left h-6 w-6"></i>
    </button>

    <button class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/30 text-white hover:bg-black/50 z-10 p-2 rounded-full" id="next-slide">
        <i class="fas fa-chevron-right h-6 w-6"></i>
    </button>

    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10" id="slider-dots">
        @foreach ($banners as $index => $banner)
            <button class="w-2 h-2 rounded-full {{ $index === 0 ? 'bg-white' : 'bg-white/50' }}" data-index="{{ $index }}"></button>
        @endforeach
    </div>
</div>


<div class="container mx-auto px-4 py-8">
    <!-- Categories Section -->
    <section class="py-10">
        <h2 class="text-2xl md:text-3xl font-bold mb-6 text-center">Danh Mục Món Ăn</h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <a href="/products?category=burgers" class="group flex flex-col items-center text-center transition-transform hover:scale-105">
                <div class="relative w-24 h-24 mb-3 rounded-full overflow-hidden border-2 border-orange-500 p-1">
                    <div class="w-full h-full rounded-full overflow-hidden">
                        <img src="/placeholder.svg?height=200&width=200" alt="Burger" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                    </div>
                </div>
                <h3 class="font-medium text-sm">Burger</h3>
            </a>
            <a href="/products?category=pizza" class="group flex flex-col items-center text-center transition-transform hover:scale-105">
                <div class="relative w-24 h-24 mb-3 rounded-full overflow-hidden border-2 border-orange-500 p-1">
                    <div class="w-full h-full rounded-full overflow-hidden">
                        <img src="/placeholder.svg?height=200&width=200" alt="Pizza" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                    </div>
                </div>
                <h3 class="font-medium text-sm">Pizza</h3>
            </a>
            <a href="/products?category=chicken" class="group flex flex-col items-center text-center transition-transform hover:scale-105">
                <div class="relative w-24 h-24 mb-3 rounded-full overflow-hidden border-2 border-orange-500 p-1">
                    <div class="w-full h-full rounded-full overflow-hidden">
                        <img src="/placeholder.svg?height=200&width=200" alt="Gà Rán" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                    </div>
                </div>
                <h3 class="font-medium text-sm">Gà Rán</h3>
            </a>
            <a href="/products?category=rice" class="group flex flex-col items-center text-center transition-transform hover:scale-105">
                <div class="relative w-24 h-24 mb-3 rounded-full overflow-hidden border-2 border-orange-500 p-1">
                    <div class="w-full h-full rounded-full overflow-hidden">
                        <img src="/placeholder.svg?height=200&width=200" alt="Cơm" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                    </div>
                </div>
                <h3 class="font-medium text-sm">Cơm</h3>
            </a>
            <a href="/products?category=noodles" class="group flex flex-col items-center text-center transition-transform hover:scale-105">
                <div class="relative w-24 h-24 mb-3 rounded-full overflow-hidden border-2 border-orange-500 p-1">
                    <div class="w-full h-full rounded-full overflow-hidden">
                        <img src="/placeholder.svg?height=200&width=200" alt="Mì" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                    </div>
                </div>
                <h3 class="font-medium text-sm">Mì</h3>
            </a>
            <a href="/products?category=drinks" class="group flex flex-col items-center text-center transition-transform hover:scale-105">
                <div class="relative w-24 h-24 mb-3 rounded-full overflow-hidden border-2 border-orange-500 p-1">
                    <div class="w-full h-full rounded-full overflow-hidden">
                        <img src="/placeholder.svg?height=200&width=200" alt="Đồ Uống" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                    </div>
                </div>
                <h3 class="font-medium text-sm">Đồ Uống</h3>
            </a>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="py-10">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl md:text-3xl font-bold">Sản Phẩm Nổi Bật</h2>
            <a href="/products" class="text-orange-500 hover:text-orange-600 flex items-center">
                Xem tất cả
                <i class="fas fa-arrow-right h-4 w-4 ml-1"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <!-- Product Card 1 -->
            <div class="group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <a href="/products/burger-classic" class="block relative h-48 overflow-hidden">
                    <img src="/placeholder.svg?height=400&width=400" alt="Burger Bò Cổ Điển" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                    <span class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">Mới</span>
                </a>

                <div class="p-4">
                    <div class="flex items-center gap-1 mb-2">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star-half-alt text-yellow-400"></i>
                        <span class="text-xs text-gray-500 ml-1">(120)</span>
                    </div>

                    <a href="/products/burger-classic">
                        <h3 class="font-medium text-lg mb-1 hover:text-orange-500 transition-colors line-clamp-1">
                            Burger Bò Cổ Điển
                        </h3>
                    </a>

                    <p class="text-gray-500 text-sm mb-3 line-clamp-2">Burger bò với phô mai, rau xà lách, cà chua và sốt đặc biệt</p>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-lg">59.000đ</span>
                        </div>

                        <button class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md text-sm flex items-center transition-colors">
                            <i class="fas fa-shopping-cart h-4 w-4 mr-1"></i>
                            Thêm
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Card 2 -->
            <div class="group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <a href="/products/burger-cheese" class="block relative h-48 overflow-hidden">
                    <img src="/placeholder.svg?height=400&width=400" alt="Burger Phô Mai Đặc Biệt" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                    <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">-10%</span>
                </a>

                <div class="p-4">
                    <div class="flex items-center gap-1 mb-2">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star-half-alt text-yellow-400"></i>
                        <span class="text-xs text-gray-500 ml-1">(95)</span>
                    </div>

                    <a href="/products/burger-cheese">
                        <h3 class="font-medium text-lg mb-1 hover:text-orange-500 transition-colors line-clamp-1">
                            Burger Phô Mai Đặc Biệt
                        </h3>
                    </a>

                    <p class="text-gray-500 text-sm mb-3 line-clamp-2">Burger với 2 lớp phô mai, thịt bò và sốt BBQ</p>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-lg">69.000đ</span>
                            <span class="text-gray-500 text-sm line-through">76.000đ</span>
                        </div>

                        <button class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md text-sm flex items-center transition-colors">
                            <i class="fas fa-shopping-cart h-4 w-4 mr-1"></i>
                            Thêm
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Card 3 -->
            <div class="group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <a href="/products/chicken-fried" class="block relative h-48 overflow-hidden">
                    <img src="/placeholder.svg?height=400&width=400" alt="Gà Rán Giòn Cay" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                    <span class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">Mới</span>
                </a>

                <div class="p-4">
                    <div class="flex items-center gap-1 mb-2">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-gray-200"></i>
                        <span class="text-xs text-gray-500 ml-1">(78)</span>
                    </div>

                    <a href="/products/chicken-fried">
                        <h3 class="font-medium text-lg mb-1 hover:text-orange-500 transition-colors line-clamp-1">
                            Gà Rán Giòn Cay
                        </h3>
                    </a>

                    <p class="text-gray-500 text-sm mb-3 line-clamp-2">Gà rán với lớp vỏ giòn và gia vị cay đặc biệt</p>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-lg">55.000đ</span>
                        </div>

                        <button class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md text-sm flex items-center transition-colors">
                            <i class="fas fa-shopping-cart h-4 w-4 mr-1"></i>
                            Thêm
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Card 4 -->
            <div class="group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <a href="/products/pizza-seafood" class="block relative h-48 overflow-hidden">
                    <img src="/placeholder.svg?height=400&width=400" alt="Pizza Hải Sản Đặc Biệt" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                    <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">-15%</span>
                </a>

                <div class="p-4">
                    <div class="flex items-center gap-1 mb-2">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <span class="text-xs text-gray-500 ml-1">(112)</span>
                    </div>

                    <a href="/products/pizza-seafood">
                        <h3 class="font-medium text-lg mb-1 hover:text-orange-500 transition-colors line-clamp-1">
                            Pizza Hải Sản Đặc Biệt
                        </h3>
                    </a>

                    <p class="text-gray-500 text-sm mb-3 line-clamp-2">Pizza với tôm, mực, sò điệp và rau củ tươi ngon</p>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-lg">159.000đ</span>
                            <span class="text-gray-500 text-sm line-through">187.000đ</span>
                        </div>

                        <button class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md text-sm flex items-center transition-colors">
                            <i class="fas fa-shopping-cart h-4 w-4 mr-1"></i>
                            Thêm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Order Now Section -->
    <section class="py-10">
        <div class="rounded-xl overflow-hidden bg-gradient-to-r from-orange-500 to-red-500">
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
                        <a href="/products" class="bg-white text-orange-500 hover:bg-white/90 px-6 py-3 rounded-md font-medium transition-colors">
                            Đặt Hàng Ngay
                        </a>
                        <a href="/products" class="text-white border border-white hover:bg-white/10 px-6 py-3 rounded-md font-medium transition-colors">
                            Xem Thực Đơn
                        </a>
                    </div>
                </div>

                <div class="relative h-60 md:h-auto">
                    <img src="https://marketingai.mediacdn.vn/thumb_w/784/603488451643117568/2024/8/7/thumb-1280-x-800-px-61-1723017058646933002606.png" alt="Đặt hàng ngay" class="object-cover w-full h-full">
                </div>
            </div>
        </div>
    </section>
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
            // Hide all slides
            slides.forEach(slide => {
                slide.classList.remove('opacity-100');
                slide.classList.add('opacity-0');
            });
            
            // Show the selected slide
            slides[index].classList.remove('opacity-0');
            slides[index].classList.add('opacity-100');
            
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
            slideInterval = setInterval(nextSlide, 5000);
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
@endsection