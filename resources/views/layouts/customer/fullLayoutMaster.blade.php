<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DevFood Vietnam')</title>

    <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('fonts/feather/style.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/noui-slider@15.6.1/dist/nouislider.min.css">

    <!-- Main CSS -->
    {{-- <link rel="stylesheet" href="{{ asset('css/customer/layout.css') }} --}}">
    <link rel="stylesheet" href="{{ asset('css/customer/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/footer.css') }}">
    @yield('styles')
</head>

<body>
    <!-- Scroll Progress Bar -->
    <div class="scroll-progress-bar"></div>
    
    <!-- Header Component -->
    @include('layouts.customer.header')
    <main>
        @yield('content')
    </main>

    @include('layouts.customer.footer')

    @yield('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/Customer/main.js') }}"></script>
</body>

</html>
