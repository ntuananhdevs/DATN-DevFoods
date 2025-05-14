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
                <div class="carousel-slide active">
                    <img src="{{ asset('images/banner/promo-1.png') }}" alt="Combo Gia Đình Vui Vẻ">
                    <div class="carousel-caption">
                        <h2>Combo Gia Đình Vui Vẻ</h2>
                        <p>Tiết kiệm đến 15% với combo dành cho gia đình</p>
                    </div>
                </div>
                <div class="carousel-slide">
                    <img src="{{ asset('images/banner/promo-2.png') }}" alt="Mua 1 Tặng 1">
                    <div class="carousel-caption">
                        <h2>Mua 1 Tặng 1</h2>
                        <p>Thứ 2 hàng tuần - Mua 1 gà giòn tặng 1 mỳ Ý</p>
                    </div>
                </div>
                <div class="carousel-slide">
                    <img src="{{ asset('images/banner/promo-3.png') }}" alt="Sinh Nhật Vui Vẻ">
                    <div class="carousel-caption">
                        <h2>Sinh Nhật Vui Vẻ</h2>
                        <p>Đặt tiệc sinh nhật tại DevFood - Nhận quà hấp dẫn</p>
                    </div>
                </div>
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