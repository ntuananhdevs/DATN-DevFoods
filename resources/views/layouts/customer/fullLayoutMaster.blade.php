<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DevFood Vietnam')</title>

    <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('fonts/feather/style.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/noui-slider@15.6.1/dist/nouislider.min.css">
`   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@6.8.4/swiper-bundle.min.css">
    <!-- Main CSS -->
    {{-- <link rel="stylesheet" href="{{ asset('css/customer/layout.css') }} --}}">
    <link rel="stylesheet" href="{{ asset('css/customer/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/footer.css') }}">
    @yield('styles')
</head>

<body>
    <div class="scroll-progress-bar"></div>
    @include('panels.customer.header')
    <main>
        @yield('content')
    </main>
    @include('panels.customer.footer')
    @yield('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@6.8.4/swiper-bundle.min.js"></script>
</body>

</html>
