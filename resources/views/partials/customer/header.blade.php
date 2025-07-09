<header class="sticky top-0 z-40 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
    <div class="container mx-auto px-4">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center">
                <button id="mobile-menu-button" class="md:hidden">
                    <i class="fas fa-bars h-5 w-5"></i>
                    <span class="sr-only">Mở menu</span>
                </button>

                <a href="/" class="flex items-center gap-2 ml-4 md:ml-0">
                    <span class="font-bold text-xl text-orange-500">PolyCrispyWings</span>
                </a>

                <nav class="hidden md:flex items-center gap-6 ml-10">
                    <a href="/" class="text-sm font-medium {{ request()->is('/') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                        Trang Chủ
                    </a>
                    <a href="{{ asset('/shop/products') }}" class="text-sm font-medium {{ request()->is('shop/products*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                        Thực Đơn
                    </a>
                    <a href="{{ asset('/promotions') }}" class="text-sm font-medium {{ request()->is('promotions*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                        Khuyến Mãi
                    </a>
                    <a href="{{ asset('/branches') }}" class="text-sm font-medium {{ request()->is('branchs*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                        Cửa Hàng
                    </a>
                    <a href="{{ asset('/about') }}" class="text-sm font-medium {{ request()->is('about*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                        Về Chúng Tôi
                    </a>
                    <a href="{{ asset('/support') }}" class="text-sm font-medium {{ request()->is('support*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                        Hỗ Trợ
                    </a>
                    <a href="{{ asset('/contact') }}" class="text-sm font-medium {{ request()->is('contact*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                        Liên Hệ
                    </a>
                    <a href="{{ asset('/hiring-driver') }}" class="text-sm font-medium {{ request()->is('hiring-driver*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                        Tuyển dụng
                    </a>
                </nav>
            </div>

            <div class="flex items-center gap-4">
                <!-- Nút tìm kiếm bằng icon (kính lúp) -->
                <button class="icon-btn" id="searchBtn" title="Tìm kiếm" aria-label="Tìm kiếm">
                    <ion-icon class="h-6 w-6" name="search-outline"></ion-icon>
                </button>

                <div id="wishlist-container" class="relative">
                    <a href="{{ route('wishlist.index') }}" class="relative">
                        <ion-icon class="h-6 w-6" name="heart-outline"></ion-icon>
                        <span class="absolute bottom-4 left-3 bg-red-500 text-white rounded-full h-4 w-4 text-xs flex items-center justify-center">
                            {{ auth()->check() ? auth()->user()->wishlist->count() : 0 }}
                        </span>
                    </a>
                </div>

                @auth
                    <div class="relative" id="user-dropdown-container">
                        <button class="flex items-center p-2" id="user-dropdown-button">
                            <ion-icon class="h-6 w-6" name="person-outline"></ion-icon>
                            <span class="ml-2 text-sm">{{ Auth::user()->full_name }}</span>
                            <ion-icon class="h-4 w-4 ml-1" name="chevron-down-outline"></ion-icon>
                        </button>
                        <div class="absolute right-0 top-full mt-1 w-48 bg-white shadow-lg rounded-lg py-2 z-50 hidden dropdown-menu" id="user-dropdown-menu">
                            <a href="{{ route('customer.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Tài khoản của tôi
                            </a>
                            <a href="{{ route('customer.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Chỉnh sửa hồ sơ
                            </a>
                            <a href="{{ route('customer.profile.setting') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Cài đặt
                            </a>
                            <form action="{{ route('customer.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('customer.login') }}" class="p-2 flex items-center">
                        <ion-icon class="h-6 w-6" name="person-outline"></ion-icon>
                        <span class="ml-2 text-sm">Đăng nhập</span>
                    </a>

                @endauth

                <a href="{{ asset('/cart') }}" class="relative p-2">
                    <ion-icon class="h-6 w-6" name="cart-outline"></ion-icon>
                    <span id="cart-counter" class="absolute -top-0 -right-1 h-5 w-5 flex items-center justify-center p-0 bg-orange-500 text-white text-xs rounded-full">{{ session('cart_count', 0) }}</span>
                    <span class="sr-only">Giỏ hàng</span>
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Search Section (Apple style, drop from header, white background) -->
<div class="search-section absolute left-0 right-0 bg-white border-b border-gray-200 z-50 transition-all duration-500 ease-in-out max-h-0 opacity-0 overflow-hidden" id="searchSection" style="top: 64px;">
    <div class="search-container max-w-7xl mx-auto py-8 px-4">
        <div class="search-input-container mb-6 relative">
            <form action="{{ route('customer.search') }}" method="GET" class="search-input-wrapper flex items-center gap-2 w-full" style="position:relative;">
                <ion-icon class="h-6 w-6 opacity-50 text-gray-950" name="search-outline"></ion-icon>
                <input
                  type="text"
                  name="search"
                  id="searchInput"
                  class="search-input flex-1 bg-transparent border-none text-gray-950 text-lg focus:outline-none focus:ring-0"
                  placeholder="Tìm kiếm sản phẩm..."
                >
                <div id="search-ajax-dropdown" style="position:absolute;top:100%;left:0;width:100%;background:white;border:none;border-radius:0 0 0.5rem 0.5rem;box-shadow:0 2px 8px rgba(0,0,0,0.08);z-index:100;display:none;max-height:250px;overflow-y:auto;"></div>
            </form>
        </div>
        <div id="search-results-container" class="mt-6">
            <div id="search-loader" style="display:none;text-align:center;padding:20px;">
                <i class="fas fa-spinner fa-spin fa-2x text-orange-500"></i>
            </div>
            <div id="search-results"></div>
        </div>
        <div class="quick-links mt-4" id="quick-links">
            <h2 class="quick-links-title text-gray-500 text-sm mb-4">Liên Kết Nhanh</h2>
            <div class="flex flex-col gap-2">
                <a href="/shop/products" class="quick-link flex items-center gap-2 cursor-pointer hover:text-orange-500">
                    <span class="arrow text-gray-400">→</span>
                    <span class="link-text text-gray-700">Tất cả sản phẩm</span>
                </a>
                <a href="/promotions" class="quick-link flex items-center gap-2 cursor-pointer hover:text-orange-500">
                    <span class="arrow text-gray-400">→</span>
                    <span class="link-text text-gray-700">Khuyến mãi</span>
                </a>
                <a href="/branches" class="quick-link flex items-center gap-2 cursor-pointer hover:text-orange-500">
                    <span class="arrow text-gray-400">→</span>
                    <span class="link-text text-gray-700">Cửa hàng gần bạn</span>
                </a>
                <a href="/about" class="quick-link flex items-center gap-2 cursor-pointer hover:text-orange-500">
                    <span class="arrow text-gray-400">→</span>
                    <span class="link-text text-gray-700">Về chúng tôi</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="overlay fixed inset-0 bg-black bg-opacity-40 z-40 hidden" id="overlay"></div>

<!-- Mobile Menu Sidebar -->
<div id="mobile-menu" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="bg-white h-full w-[300px] p-4 transform -translate-x-full transition-transform duration-300" id="mobile-menu-content">
        <div class="flex justify-between items-center mb-6">
            <span class="font-bold text-xl text-orange-500">FastFood</span>
            <button id="close-mobile-menu">
                <i class="fas fa-times h-5 w-5"></i>
            </button>
        </div>
        <nav class="flex flex-col gap-4">
            <a href="/" class="text-lg font-medium {{ request()->is('/') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                Trang Chủ
            </a>
            <a href="/products" class="text-lg font-medium {{ request()->is('products*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                Thực Đơn
            </a>
            <a href="/promotions" class="text-lg font-medium {{ request()->is('promotions*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                Khuyến Mãi
            </a>
            <a href="/stores" class="text-lg font-medium {{ request()->is('stores*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                Cửa Hàng
            </a>
            <a href="/about" class="text-lg font-medium {{ request()->is('about*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                Về Chúng Tôi
            </a>
            <a href="/contact" class="text-lg font-medium {{ request()->is('contact*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                Liên Hệ
            </a>
            <a href="/recruitment" class="text-lg font-medium {{ request()->is('recruitment*') ? 'text-orange-500' : 'hover:text-orange-500' }} transition-colors">
                Tuyển dụng
            </a>
        </nav>
    </div>
</div>

<style>
header.search-active {
    background: #fff !important;
    transition: background 0.2s;
}

/* Đảm bảo dropdown luôn hiển thị trên tất cả các element khác */
#search-ajax-dropdown {
    position: absolute !important;
    z-index: 100 !important;
    width: 100% !important;
    max-width: 100% !important;
    left: 0 !important;
    transform: none !important;
    border: none !important;
    border-top: none !important;
    background: #fff !important;
}

/* Style cho dropdown items */
#search-ajax-dropdown .dropdown-item {
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    transition: background-color 0.2s;
}

#search-ajax-dropdown .dropdown-item:hover {
    background-color: #f8fafc;
}

#search-ajax-dropdown .dropdown-item:last-child {
    border-bottom: none;
}
</style>

<script>
window.LaravelRoutes = {
    productShow: '{{ route('products.show', ['id' => 'REPLACE_ID']) }}',
    comboShow: '{{ route('combos.show', ['id' => 'REPLACE_ID']) }}'
};
</script>

<script>
// JavaScript để xử lý vị trí của dropdown
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const dropdown = document.getElementById('search-ajax-dropdown');
    const searchSection = document.getElementById('searchSection');

    function updateDropdownPosition() {
        if (searchInput && dropdown) {
            const inputRect = searchInput.getBoundingClientRect();
            const searchSectionRect = searchSection.getBoundingClientRect();

            // Đặt dropdown cách input 5px
            dropdown.style.top = (inputRect.bottom + window.scrollY + 5) + 'px';
            // Dropdown nằm trong form, sát dưới input
            dropdown.style.left = '0px';
            dropdown.style.width = searchInput.offsetWidth + 'px';
            dropdown.style.maxWidth = '100%';
        }
    }

    // Cập nhật vị trí khi search section mở/đóng
    const searchBtn = document.getElementById('searchBtn');
    if (searchBtn) {
        searchBtn.addEventListener('click', function() {
            setTimeout(updateDropdownPosition, 100); // Delay để đảm bảo animation hoàn thành
        });
    }

    // Cập nhật vị trí khi window resize
    window.addEventListener('resize', updateDropdownPosition);

    // Cập nhật vị trí khi scroll
    window.addEventListener('scroll', updateDropdownPosition);

    // Disable scroll when dropdown is visible, enable when hidden
    const observer = new MutationObserver(() => {
        if (dropdown.style.display !== 'none' && dropdown.innerHTML.trim() !== '') {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    });
    observer.observe(dropdown, { attributes: true, childList: true, subtree: true });

    // Ẩn quick-links khi có kết quả live search
    const quickLinks = document.getElementById('quick-links');
    const dropdown = document.getElementById('search-ajax-dropdown');
    if (dropdown && quickLinks) {
        const observerQuickLinks = new MutationObserver(() => {
            if (dropdown.style.display !== 'none' && dropdown.innerHTML.trim() !== '') {
                quickLinks.style.display = 'none';
            } else {
                quickLinks.style.display = '';
            }
        });
        observerQuickLinks.observe(dropdown, { attributes: true, childList: true, subtree: true });
    }
});
</script>

<link rel="stylesheet" href="{{ asset('css/customer-search.css') }}">
<script src="{{ asset('js/customer-search.js') }}"></script>
