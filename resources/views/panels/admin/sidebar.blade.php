@php
    $configData = Helper::applClasses();
@endphp

<div class="main-menu menu-fixed {{ $configData['theme'] === 'light' ? 'menu-light' : 'menu-dark' }} menu-accordion menu-shadow"
    data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto">
                <a class="navbar-brand" href="dashboard-analytics">
                    <div class="brand-logo"></div>
                    <h2 class="brand-text mb-0">Vuexy</h2>
                </a>
            </li>
            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                    <i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i>
                </a>
            </li>
        </ul>
    </div>

    <div class="shadow-bottom"></div>

    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            @if (isset($menuData[0]) && isset($menuData[0]->menu))
                @foreach ($menuData[0]->menu as $menu)
                    @if (isset($menu->navheader))
                        <li class="navigation-header">
                            <span>{{ $menu->navheader }}</span>
                        </li>
                    @else
                        @php
                            $custom_classes = optional($menu)->classlist;
                            $translation = optional($menu)->i18n;
                            $badgeClasses = 'badge badge-pill badge-primary float-right';

                            $menuUrl = optional($menu)->url;
                            $menuUrlWithoutSlash = ltrim($menuUrl, '/');
                            $activeClass =
                                request()->is($menuUrl) ||
                                request()->is($menuUrlWithoutSlash) ||
                                request()->is('admin/' . $menuUrlWithoutSlash)
                                    ? 'active'
                                    : '';
                        @endphp

                        <li class="nav-item {{ $activeClass }} {{ $custom_classes }}">
                            <a href="{{ optional($menu)->url }}">
                                <i class="{{ optional($menu)->icon }}"></i>
                                <span class="menu-title"
                                    data-i18n="{{ $translation }}">{{ __(optional($menu)->name) }}</span>

                                @if (isset($menu->badge))
                                    <span
                                        class="{{ isset($menu->badgeClass) ? $menu->badgeClass : $badgeClasses }}">{{ $menu->badge }}</span>
                                @endif
                            </a>

                            @if (isset($menu->submenu) && is_array($menu->submenu))
                                @include('panels.admin.submenu', ['menu' => $menu->submenu])
                            @endif
                        </li>
                    @endif
                @endforeach
            @endif
        </ul>
    </div>
</div>

<style>
    /* Modern Sidebar Styling */
    .main-menu {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-right: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .menu-dark.main-menu {
        background: rgba(40, 48, 70, 0.95);
        border-right: 1px solid rgba(255, 255, 255, 0.08);
    }

    .navbar-header {
        padding: 1rem;
        background: rgba(255, 255, 255, 0.1);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }

    .navbar-header .navbar-brand {
        display: flex;
        align-items: center;
        padding: 0.5rem;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .navbar-header .navbar-brand:hover {
        background: rgba(115, 103, 240, 0.08);
        transform: translateY(-2px);
    }

    .navbar-header .brand-logo {
        height: 32px;
        width: 32px;
        filter: drop-shadow(0 4px 6px rgba(115, 103, 240, 0.15));
        transition: all 0.3s ease;
    }

    .navbar-header .brand-text {
        font-size: 1.6rem;
        font-weight: 700;
        background: linear-gradient(45deg, #7367f0, #9e95f5);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-left: 0.8rem;
    }

    .navigation-header {
        padding: 1.2rem 1.5rem 0.8rem !important;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1rem;
        color: #7367f0;
    }

    .navigation li.nav-item {
        margin: 0.4rem 0.8rem;
    }

    .navigation li.nav-item a {
        padding: 0.8rem 1rem;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        color: #626262;
    }

    .menu-dark .navigation li.nav-item a {
        color: #c2c2c2;
    }

    .navigation li.nav-item a:hover {
        background: rgba(115, 103, 240, 0.08);
        transform: translateX(5px);
    }

    .menu-dark .navigation li.nav-item a:hover {
        background: rgba(115, 103, 240, 0.15);
    }

    .navigation li.nav-item.active a {
        background: linear-gradient(118deg, #7367f0, rgba(115, 103, 240, 0.7));
        box-shadow: 0 0 10px rgba(115, 103, 240, 0.4);
        color: #fff;
    }

    .navigation li.nav-item a i {
        font-size: 1.3rem;
        margin-right: 1rem;
        transition: all 0.3s ease;
        color: inherit;
    }

    .navigation li.nav-item a .menu-title {
        font-size: 0.95rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .badge-glow {
        box-shadow: 0 0 10px rgba(115, 103, 240, 0.5);
        background: linear-gradient(45deg, #7367f0, #9e95f5);
        border: none;
        padding: 0.35rem 0.7rem;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .navigation li.nav-item ul.menu-content {
        padding-left: 2.5rem;
        margin-top: 0.2rem;
    }

    .navigation li.nav-item ul.menu-content li a {
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
    }

    .shadow-bottom {
        background: linear-gradient(180deg, rgba(248, 248, 248, 0.9) 0%, rgba(248, 248, 248, 0.6) 60%, transparent 100%);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        height: 2rem;
    }

    .menu-dark .shadow-bottom {
        background: linear-gradient(180deg, rgba(40, 48, 70, 0.9) 0%, rgba(40, 48, 70, 0.6) 60%, transparent 100%);
    }

    /* Hiệu ứng hover cho toggle icon */
    .nav-toggle .nav-link {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .nav-toggle .nav-link:hover {
        background: rgba(115, 103, 240, 0.1);
        transform: rotate(90deg);
    }

    .nav-toggle .nav-link i {
        font-size: 1.4rem;
        color: #7367f0;
        transition: all 0.3s ease;
    }

    /* Hiệu ứng ripple cho các menu item */
    .navigation li.nav-item a {
        position: relative;
        overflow: hidden;
    }

    .navigation li.nav-item a::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        pointer-events: none;
        background-image: radial-gradient(circle, rgba(115, 103, 240, 0.2) 10%, transparent 10.01%);
        background-repeat: no-repeat;
        background-position: 50%;
        transform: scale(10, 10);
        opacity: 0;
        transition: transform 0.5s, opacity 1s;
    }

    .navigation li.nav-item a:active::after {
        transform: scale(0, 0);
        opacity: 0.3;
        transition: 0s;
    }
</style>
<!-- END: Main Menu -->

<style>
    /* CSS cải tiến cho sidebar */
    .main-menu {
        box-shadow: 0 0 15px 0 rgba(0, 0, 0, 0.05) !important;
        transition: all 0.3s ease;
    }

    .navbar-header {
        padding: 0.85rem 1.3rem 0.85rem 1.3rem;
        transition: all 0.3s ease;
    }

    .navbar-header .navbar-brand {
        display: flex;
        align-items: center;
        margin-top: 1.35rem;
        margin-bottom: 1.35rem;
        transition: all 0.3s ease;
    }

    .navbar-header .navbar-brand .brand-logo {
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        height: 28px;
        width: 28px;
        margin-right: 0.8rem;
        filter: drop-shadow(0px 0px 4px rgba(115, 103, 240, 0.15));
        transition: all 0.3s ease;
    }

    .navbar-header .navbar-brand .brand-text {
        font-weight: 600;
        letter-spacing: 0.01rem;
        font-size: 1.5rem;
        transition: all 0.3s ease;
    }

    .main-menu-content {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }

    .navigation-header {
        padding: 1rem 1.3rem 0.5rem 1.3rem !important;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05rem;
    }

    .navigation-header span {
        opacity: 0.8;
    }

    .navigation li.nav-item {
        margin: 0.2rem 0;
    }

    .navigation li.nav-item a {
        padding: 0.8rem 1.3rem;
        border-radius: 0.25rem;
        transition: all 0.3s ease;
        margin: 0 0.7rem;
    }

    .navigation li.nav-item a:hover {
        background: rgba(115, 103, 240, 0.12) !important;
        transform: translateX(5px);
    }

    .navigation li.nav-item.active a {
        background: linear-gradient(118deg, #7367f0, rgba(115, 103, 240, 0.7)) !important;
        box-shadow: 0 0 10px 1px rgba(115, 103, 240, 0.3) !important;
        color: #fff !important;
        font-weight: 500;
    }

    .navigation li.nav-item a i {
        margin-right: 0.8rem;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }

    .navigation li.nav-item a .menu-title {
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .navigation li.nav-item a .badge {
        margin-left: auto;
        transition: all 0.3s ease;
    }

    .navigation li.nav-item ul.menu-content {
        padding-left: 2.2rem;
    }

    .navigation li.nav-item ul.menu-content li a {
        padding: 0.65rem 1.3rem;
        transition: all 0.3s ease;
    }

    .shadow-bottom {
        background: linear-gradient(180deg, rgba(248, 248, 248, 0.95) 44%, rgba(248, 248, 248, 0.46) 73%, rgba(255, 255, 255, 0) 100%);
        height: 1.5rem;
    }

    /* Chế độ tối */
    .menu-dark .navigation li.nav-item a:hover {
        background: rgba(255, 255, 255, 0.1) !important;
    }

    .menu-dark .shadow-bottom {
        background: linear-gradient(180deg, rgba(37, 43, 71, 0.95) 44%, rgba(37, 43, 71, 0.46) 73%, rgba(37, 43, 71, 0) 100%);
    }

    /* Hiệu ứng hover cho toggle icon */
    .nav-toggle .nav-link {
        border-radius: 50%;
        padding: 0.5rem;
        transition: all 0.3s ease;
    }

    .nav-toggle .nav-link:hover {
        background: rgba(115, 103, 240, 0.12);
        transform: rotate(180deg);
    }

    /* Hiệu ứng khi hover vào brand */
    .navbar-header .navbar-brand:hover .brand-text {
        color: #7367f0;
    }

    .navbar-header .navbar-brand:hover .brand-logo {
        transform: scale(1.1);
    }
</style>
