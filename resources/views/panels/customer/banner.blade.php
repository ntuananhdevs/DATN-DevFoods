<link href="{{ asset('css/customer/banner.css') }}" rel="stylesheet">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.carousel-slide');
            const indicators = document.querySelectorAll('.indicator');
            const prevBtn = document.querySelector('.carousel-control.prev');
            const nextBtn = document.querySelector('.carousel-control.next');
            let currentIndex = 0;
            let autoSlideInterval;

            function updateCarousel() {
                slides.forEach((slide, index) => {
                    if (index === currentIndex) {
                        slide.classList.add('active');
                        indicators[index].classList.add('active');
                    } else {
                        slide.classList.remove('active');
                        indicators[index].classList.remove('active');
                    }
                });
            }

            function startAutoSlide() {
                autoSlideInterval = setInterval(() => {
                    currentIndex = (currentIndex + 1) % slides.length;
                    updateCarousel();
                }, 5000);
            }

            function resetAutoSlide() {
                clearInterval(autoSlideInterval);
                startAutoSlide();
            }

            nextBtn.addEventListener('click', () => {
                currentIndex = (currentIndex + 1) % slides.length;
                updateCarousel();
                resetAutoSlide();
            });

            prevBtn.addEventListener('click', () => {
                currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                updateCarousel();
                resetAutoSlide();
            });

            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => {
                    currentIndex = index;
                    updateCarousel();
                    resetAutoSlide();
                });
            });

            startAutoSlide();
        });
    </script>
    <section class="promo-carousel">
        <div class="carousel-container">
            <div class="carousel-slides">
                @inject('bannerService', 'App\Services\BannerService')
                @php
                    $banners = $bannerService->getActiveBanners();
                @endphp
               
                @foreach($banners as $banner)
                <div class="carousel-slide {{ $banner === 0 ? 'active' : '' }}">
                    <img src="{{ asset('storage/' . $banner->image_path) }}" alt="{{ $banner->title }}">
                    <div class="carousel-caption">
                        <h2>{{ $banner->title }}</h2>
                        <p>{{ $banner->description }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <button class="carousel-control prev">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="carousel-control next">
                <i class="fas fa-chevron-right"></i>
            </button>
            <div class="carousel-indicators">
                <button class="indicator active" data-slide="0"></button>
                <button class="indicator" data-slide="1"></button>
                <button class="indicator" data-slide="2"></button>
            </div>
        </div>
    </section>