@isset($pageConfigs)
{!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

<!DOCTYPE html>
{{-- {!! Helper::applClasses() !!} --}}
@php
$configData = Helper::applClasses();
@endphp
<html lang="@if(session()->has('locale')){{session()->get('locale')}}@else{{$configData['defaultLanguage']}}@endif"
    data-textdirection="{{ env('MIX_CONTENT_DIRECTION') === 'rtl' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - Vuexy Vuejs, HTML & Laravel Admin Dashboard Template</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/logo/favicon.ico">

    {{-- Include core + vendor Styles --}}
    @include('panels/admin/styles')

</head>

<body
    class="horizontal-layout horizontal-menu {{$configData['horizontalMenuType']}} {{ $configData['blankPageClass'] }} {{ $configData['bodyClass'] }}  {{($configData['theme'] === 'dark') ? 'dark-layout' :'light' }} {{ $configData['sidebarClass'] }} {{ $configData['verticalMenuNavbarType'] }} {{ $configData['footerType'] }}  footer-light"
    data-menu="horizontal-menu" data-col="2-columns" data-open="hover" data-layout="{{ $configData['theme'] }}">

    {{-- Include Sidebar --}}
    @include('panels.admin.sidebar')

    <!-- BEGIN: Header-->
    {{-- Include Navbar --}}
    @include('panels.admin.navbar')

    {{-- Include Sidebar --}}
    @include('panels.admin.horizontalMenu')

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <!-- BEGIN: Header-->
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>

        <div class="content-wrapper">
            {{-- Include Breadcrumb --}}
            @if($configData['pageHeader'] == true && isset($configData['pageHeader']))
            @include('panels.breadcrumb')
            @endif
            <div class="{{ $configData['sidebarPositionClass'] }}">
                <div class="sidebar">
                    {{-- Include Sidebar Content --}}
                    @yield('content-sidebar')
                </div>
            </div>
            <div class="{{ $configData['contentsidebarClass'] }}">
                <div class="content-body">
                    {{-- Include Page Content --}}
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    <!-- End: Content-->

    @if($configData['blankPage'] == false)
    @include('adminadmin/customizer')
    @endif

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    {{-- include footer --}}
    @include('panels/admin/footer')

    {{-- include default scripts --}}
    @include('panels/admin/scripts')

</body>

</html>