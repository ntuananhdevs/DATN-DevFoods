<header class="sticky-top">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-orange" href="{{ url('/') }}">FastFood</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">Trang Chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('shop/products') }}">Thực Đơn</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/store') }}">Cửa Hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/promotions') }}">Khuyến Mãi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/blog') }}">Bài Viết</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/about') }}">Về Chúng Tôi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/contact') }}">Liên Hệ</a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    <div class="search-box me-3">
                        <form class="d-flex" action="{{ url('/search') }}" method="GET">
                            <input class="form-control me-2" type="search" name="query" placeholder="Tìm kiếm..." aria-label="Search">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <a href="{{ url('/profile') }}" class="btn btn-link text-dark me-2">
                            <i class="fas fa-user"></i>
                        </a>
                        <a href="{{ url('/cart') }}" class="btn btn-link text-dark position-relative">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-orange">
                                {{ session('cart_count', 0) }}
                            </span>
                        </a>
                        <div class="d-none d-lg-flex align-items-center ms-3">
                            <i class="fas fa-phone text-orange me-1"></i>
                            <span class="fw-medium">1900 1234</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>