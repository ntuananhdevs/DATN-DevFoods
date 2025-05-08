<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Jollibee Vietnam')</title>
    
    <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('fonts/feather/style.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/noui-slider@15.6.1/dist/nouislider.min.css">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('css/jollibee-styles.css') }}">
</head>
<body>
    <!-- Scroll Progress Bar -->
    <div class="scroll-progress-bar"></div>

    <!-- Header -->
    <header class="header">
        <!-- Top Navigation -->
        <div class="top-nav">
            <div class="container">
                <div class="top-nav-content">
                    <div class="top-nav-links">
                        <a href="#" class="top-nav-link">Về Jollibee</a>
                        <a href="#" class="top-nav-link">Khuyến Mãi</a>
                        <a href="#" class="top-nav-link">Cửa Hàng</a>
                        <a href="#" class="top-nav-link">Tuyển Dụng</a>
                    </div>
                    <div class="top-nav-actions">
                        <div class="language-selector">
                            <button class="language-btn active">VN</button>
                            <span>|</span>
                            <button class="language-btn">EN</button>
                        </div>
                        <button class="location-btn">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Chọn địa điểm</span>
                        </button>
                        <button class="account-btn">
                            <i class="fas fa-user"></i>
                            <span>Đăng ký / Đăng nhập</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Navigation -->
        <div class="main-nav">
            <div class="container">
                <div class="main-nav-content">
                    <div class="logo">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('images/logo.png') }}" alt="Jollibee Logo">
                        </a>
                    </div>
                    <nav class="desktop-menu">
                        <ul class="nav-list">
                            <li class="nav-item active"><a href="{{ url('/') }}">TRANG CHỦ</a></li>
                            <li class="nav-item dropdown">
                                <a href="#" class="dropdown-toggle">VỀ JOLLIBEE <i class="fas fa-chevron-down"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Câu chuyện thương hiệu</a></li>
                                    <li><a href="#">Lịch sử phát triển</a></li>
                                    <li><a href="#">Giá trị cốt lõi</a></li>
                                    <li><a href="#">Đội ngũ lãnh đạo</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a href="#" class="dropdown-toggle">THỰC ĐƠN <i class="fas fa-chevron-down"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Gà Giòn Vui Vẻ</a></li>
                                    <li><a href="#">Gà Sốt Cay</a></li>
                                    <li><a href="#">Burger & Sandwich</a></li>
                                    <li><a href="#">Mỳ Ý & Cơm</a></li>
                                    <li><a href="#">Món tráng miệng</a></li>
                                </ul>
                            </li>
                            <li class="nav-item"><a href="#">KHUYẾN MÃI</a></li>
                            <li class="nav-item"><a href="#">DỊCH VỤ</a></li>
                            <li class="nav-item"><a href="#">TIN TỨC</a></li>
                            <li class="nav-item"><a href="#">CỬA HÀNG</a></li>
                            <li class="nav-item"><a href="#">LIÊN HỆ</a></li>
                        </ul>
                    </nav>
                    <div class="nav-actions">
                        <button class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                        <div class="cart-btn">
                            <i class="fas fa-shopping-bag"></i>
                            <span class="cart-count">3</span>
                        </div>
                        <button class="pickup-btn">PICK UP</button>
                        <div class="hotline">
                            <i class="fas fa-phone"></i>
                            <div>
                                <span class="hotline-number">1900-1533</span>
                                <span class="hotline-text">GIAO HÀNG TẬN NƠI</span>
                            </div>
                        </div>
                        <button class="mobile-menu-btn">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu">
            <div class="container">
                <button class="mobile-menu-close">
                    <i class="fas fa-times"></i>
                </button>
                <ul class="mobile-nav-list">
                    <li><a href="{{ url('/') }}">TRANG CHỦ</a></li>
                    <li><a href="#">VỀ JOLLIBEE</a></li>
                    <li><a href="#">THỰC ĐƠN</a></li>
                    <li><a href="#">KHUYẾN MÃI</a></li>
                    <li><a href="#">DỊCH VỤ</a></li>
                    <li><a href="#">TIN TỨC</a></li>
                    <li><a href="#">CỬA HÀNG</a></li>
                    <li><a href="#">LIÊN HỆ</a></li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <div class="footer-logo">
                        <img src="{{ asset('images/logo-white.png') }}" alt="Jollibee Logo">
                    </div>
                    <p class="footer-description">
                        Jollibee là thương hiệu đồ ăn nhanh nổi tiếng với các món gà giòn, mỳ Ý, burger và nhiều món ăn
                        hấp dẫn khác.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>

                <div class="footer-column">
                    <h3 class="footer-title">Về Jollibee</h3>
                    <ul class="footer-links">
                        <li><a href="#">Giới thiệu</a></li>
                        <li><a href="#">Lịch sử phát triển</a></li>
                        <li><a href="#">Tin tức & Sự kiện</a></li>
                        <li><a href="#">Tuyển dụng</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3 class="footer-title">Dịch vụ</h3>
                    <ul class="footer-links">
                        <li><a href="#">Đặt hàng trực tuyến</a></li>
                        <li><a href="#">Tiệc sinh nhật</a></li>
                        <li><a href="#">Jollibee Kids Club</a></li>
                        <li><a href="#">Đơn hàng lớn</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3 class="footer-title">Liên hệ</h3>
                    <ul class="contact-info">
                        <li>Hotline: 1900-1533</li>
                        <li>Email: info@jollibee.com.vn</li>
                        <li>Địa chỉ: Tầng 26, Tòa nhà CII Tower, 152 Điện Biên Phủ, Phường 25, Quận Bình Thạnh, TP. Hồ
                            Chí Minh</li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="copyright">© 2024 Jollibee Vietnam. Tất cả các quyền được bảo lưu.</p>
                <div class="footer-legal">
                    <a href="#">Điều khoản sử dụng</a>
                    <a href="#">Chính sách bảo mật</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Core JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/jollibee-main.js') }}"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Scroll Progress Bar
            const scrollProgressBar = document.querySelector(".scroll-progress-bar")

            window.addEventListener("scroll", () => {
                const scrollTop = window.scrollY
                const scrollHeight = document.documentElement.scrollHeight - window.innerHeight
                const scrollPercentage = (scrollTop / scrollHeight) * 100
                scrollProgressBar.style.width = scrollPercentage + "%"
            })

            // Mobile Menu Toggle
            const mobileMenuBtn = document.querySelector(".mobile-menu-btn")
            const mobileMenu = document.querySelector(".mobile-menu")
            const mobileMenuClose = document.querySelector(".mobile-menu-close")

            if (mobileMenuBtn && mobileMenu && mobileMenuClose) {
                mobileMenuBtn.addEventListener("click", () => {
                    mobileMenu.classList.add("active")
                    document.body.style.overflow = "hidden"
                })

                mobileMenuClose.addEventListener("click", () => {
                    mobileMenu.classList.remove("active")
                    document.body.style.overflow = ""
                })
            }

            // Header Scroll Effect
            const mainNav = document.querySelector(".main-nav")
            const logo = document.querySelector(".logo img")

            window.addEventListener("scroll", () => {
                if (window.scrollY > 50) {
                    mainNav.style.padding = "0.5rem 0"
                    if (logo) logo.style.height = "3rem"
                } else {
                    mainNav.style.padding = "0.75rem 0"
                    if (logo) logo.style.height = "3.5rem"
                }
            })

            // Carousel
            const slides = document.querySelectorAll(".carousel-slide")
            const indicators = document.querySelectorAll(".indicator")
            const prevBtn = document.querySelector(".carousel-control.prev")
            const nextBtn = document.querySelector(".carousel-control.next")

            if (slides.length > 0) {
                let currentSlide = 0
                let slideInterval

                const startSlideshow = () => {
                    slideInterval = setInterval(() => {
                        goToSlide((currentSlide + 1) % slides.length)
                    }, 5000)
                }

                const stopSlideshow = () => {
                    clearInterval(slideInterval)
                }

                const goToSlide = (n) => {
                    slides[currentSlide].classList.remove("active")
                    indicators[currentSlide].classList.remove("active")

                    currentSlide = n

                    slides[currentSlide].classList.add("active")
                    indicators[currentSlide].classList.add("active")
                }

                if (prevBtn && nextBtn) {
                    prevBtn.addEventListener("click", () => {
                        stopSlideshow()
                        goToSlide(currentSlide === 0 ? slides.length - 1 : currentSlide - 1)
                        startSlideshow()
                    })

                    nextBtn.addEventListener("click", () => {
                        stopSlideshow()
                        goToSlide((currentSlide + 1) % slides.length)
                        startSlideshow()
                    })
                }

                indicators.forEach((indicator, index) => {
                    indicator.addEventListener("click", () => {
                        stopSlideshow()
                        goToSlide(index)
                        startSlideshow()
                    })
                })

                startSlideshow()
            }

            // Stats Counter Animation
            const statNumbers = document.querySelectorAll(".stat-number")

            if (statNumbers.length > 0) {
                const animateCounter = (element, target) => {
                    let current = 0
                    const increment = target > 1000 ? 50 : target > 100 ? 5 : 1
                    const duration = 2000
                    const steps = Math.ceil(target / increment)
                    const stepTime = Math.floor(duration / steps)

                    const timer = setInterval(() => {
                        current += increment
                        if (current >= target) {
                            element.textContent = target + "+"
                            clearInterval(timer)
                        } else {
                            element.textContent = current + "+"
                        }
                    }, stepTime)
                }

                const observer = new IntersectionObserver(
                    (entries) => {
                        entries.forEach((entry) => {
                            if (entry.isIntersecting) {
                                const target = Number.parseInt(entry.target.getAttribute("data-count"))
                                animateCounter(entry.target, target)
                                observer.unobserve(entry.target)
                            }
                        })
                    },
                    { threshold: 0.5 },
                )

                statNumbers.forEach((stat) => {
                    observer.observe(stat)
                })
            }
        })

    </script>
</body>
</html>
