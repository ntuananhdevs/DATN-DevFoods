{{-- For submenu --}}
<ul class="menu-content">
    @if(isset($menu))
    @foreach($menu as $submenu)
    @php
    $submenuTranslation = "";
    if(isset($menu->i18n)){
    $submenuTranslation = $menu->i18n;
    }
    @endphp
    <li class="{{ (request()->is($submenu->url)) ? 'active' : '' }}">
        <a href="{{ $submenu->url }}">
            <i class="{{ isset($submenu->icon) ? $submenu->icon : "" }}"></i>
            <span class="menu-title" data-i18n="{{ $submenuTranslation }}">{{ __('locale.'.$submenu->name) }}</span>
        </a>
        @if (isset($submenu->submenu))
        @include('panels/admin/submenu', ['menu' => $submenu->submenu])
        @endif
    </li>
    @endforeach
    @endif
</ul>