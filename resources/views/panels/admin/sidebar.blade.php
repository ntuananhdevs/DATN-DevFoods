@php
    // Lấy cấu hình từ Helper
    $configData = Helper::applClasses();
@endphp

<div class="main-menu menu-fixed {{ $configData['theme'] === 'light' ? 'menu-light' : 'menu-dark' }} menu-accordion menu-shadow" data-scroll-to-active="true">
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
                    <i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block primary collapse-toggle-icon" data-ticon="icon-disc"></i>
                </a>
            </li>
        </ul>
    </div>

    <div class="shadow-bottom"></div>

    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            {{-- Kiểm tra xem có menu dữ liệu không --}}
            @if(isset($menuData[0]) && isset($menuData[0]->menu))
                @foreach($menuData[0]->menu as $menu)
                    {{-- Nếu menu có header --}}
                    @if(isset($menu->navheader))
                        <li class="navigation-header">
                            <span>{{ $menu->navheader }}</span>
                        </li>
                    @else
                        {{-- Xử lý Custom Class và i18n --}}
                        @php
                            $custom_classes = optional($menu)->classlist;
                            $translation = optional($menu)->i18n;
                            $badgeClasses = "badge badge-pill badge-primary float-right";
                            
                            $menuUrl = optional($menu)->url;
                            $menuUrlWithoutSlash = ltrim($menuUrl, '/');
                            $activeClass = request()->is($menuUrl) || 
                                          request()->is($menuUrlWithoutSlash) || 
                                          request()->is('admin/'.$menuUrlWithoutSlash) ? 'active' : '';
                        @endphp

                        <li class="nav-item {{ $activeClass }} {{ $custom_classes }}">
                            <a href="{{ optional($menu)->url }}">
                                <i class="{{ optional($menu)->icon }}"></i>
                                <span class="menu-title" data-i18n="{{ $translation }}">{{ __( optional($menu)->name) }}</span>

                                {{-- Kiểm tra và hiển thị badge nếu có --}}
                                @if(isset($menu->badge))
                                    <span class="{{ isset($menu->badgeClass) ? $menu->badgeClass : $badgeClasses }}">{{ $menu->badge }}</span>
                                @endif
                            </a>

                            {{-- Kiểm tra submenu và hiển thị nếu có --}}
                            @if(isset($menu->submenu) && is_array($menu->submenu))
                                @include('panels.admin.submenu', ['menu' => $menu->submenu])
                            @endif
                        </li>
                    @endif
                @endforeach
            @endif
        </ul>
    </div>
</div>
<!-- END: Main Menu -->
