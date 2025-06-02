<!-- <link href="{{ asset('css/customer/banner.css') }}" rel="stylesheet">
<style>
    .carousel-slide .slide-content {
        display: block;
        width: 100%;
        height: 100%;
        position: relative;
    }
    .carousel-slide[data-link]:hover .slide-content {
        opacity: 0.95;
    }
    .no-banners-message {
        text-align: center;
        padding: 2rem;
        font-size: 1.2rem;
        color: #666;
    }
</style>
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

            // Xử lý sự kiện click vào banner để chuyển trang
            slides.forEach(slide => {
                slide.querySelector('.slide-content').addEventListener('click', () => {
                    const link = slide.getAttribute('data-link');
                    if (link && link.trim() !== '') {
                        window.location.href = link;
                    }
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
                
                @if(count($banners) === 0)
                    <div class="no-banners-message">
                        <p>Banner chưa được setup</p>
                    </div>
                @else
               
                @foreach($banners as $key => $banner)
                <div class="carousel-slide {{ $key === 0 ? 'active' : '' }}" data-link="{{ $banner->link }}">
                    <div class="slide-content" style="cursor: {{ $banner->link ? 'pointer' : 'default' }}">
                        <img src="{{ asset('storage/' . $banner->image_path) }}" alt="{{ $banner->title }}">
                        <div class="carousel-caption">
                            <h2>{{ $banner->title }}</h2>
                            <p>{{ $banner->description }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
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
    </section> -->