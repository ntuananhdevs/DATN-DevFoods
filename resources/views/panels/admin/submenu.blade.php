{{-- For submenu --}}
<ul class="menu-content">
    @if(isset($menu))
    @foreach($menu as $submenu)
    @php
    $submenuTranslation = "";
    if(isset($submenu->i18n)){
        $submenuTranslation = $submenu->i18n;
    }
    
    // Loại bỏ dấu / ở đầu URL nếu có
    $submenuUrlWithoutSlash = ltrim($submenu->url, '/');
    // Kiểm tra nhiều trường hợp
    $activeClass = request()->is($submenu->url) || 
                  request()->is($submenuUrlWithoutSlash) || 
                  request()->is('admin/' . $submenuUrlWithoutSlash) ? 'active' : '';
    @endphp
    <li class="{{ $activeClass }}">
        <a href="{{ $submenu->url }}">
            <i class="{{ isset($submenu->icon) ? $submenu->icon : "" }}"></i>
            <span class="menu-item">{{ $submenu->name }}</span>
        </a>
        @if (isset($submenu->submenu))
        @include('panels/admin/submenu', ['menu' => $submenu->submenu])
        @endif
    </li>
    @endforeach
    @endif
</ul>