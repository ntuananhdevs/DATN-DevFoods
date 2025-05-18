<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>beDriver - Ứng dụng tài xế</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/driver.css') }}">
    @yield('head')
</head>
<body>
    <!-- Desktop Header -->
    @include('panels.driver.header')
    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>
    <!-- Bottom Navigation -->
    @include('panels.driver.bottomnav')
    @yield('modal')
    <script src="{{ asset('js/driver.js') }}"></script>
</body>
</html>
