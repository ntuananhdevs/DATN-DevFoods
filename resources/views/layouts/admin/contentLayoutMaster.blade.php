<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FastFood Admin')</title>
    <meta name="description" content="@yield('description', 'Admin dashboard')">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link href='https://api.mapbox.com/mapbox-gl-js/v3.1.0/mapbox-gl.css' rel='stylesheet' />
    <script src='https://api.mapbox.com/mapbox-gl-js/v3.1.0/mapbox-gl.js'></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/nouislider@15.6.1/dist/nouislider.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/nouislider@15.6.1/dist/nouislider.min.js"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/app.css') }}">
    <!-- Scripts -->
    <script src="{{ asset('js/admin/app.js') }}" defer></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    @yield('styles')
    @yield('page-style-prd-edit')
    @yield('style-prd-stock')
    @yield('vendor-style')
</head>

<body class="bg-background text-foreground">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar">
            @include('partials.admin.sidebar')
        </aside>

        <!-- Main Content -->
        <main id="main-content" class="main-content">
            <!-- Header -->
            @include('partials.admin.header')

            <!-- Page Content -->
            @hasSection('hide_footer')
                <div class="flex-1 overflow-auto">
                    @yield('content')
                </div>
            @else
                <div class="flex-1 p-4 md:p-6 overflow-auto">
                    @yield('content')
                </div>
            @endif

            <!-- Footer -->
            @hasSection('hide_footer')
                @if (!trim($__env->yieldContent('hide_footer')))
                    @include('partials.admin.footer')
                @endif
            @else
                @include('partials.admin.footer')
            @endif
        </main>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/modal.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
    @stack('scripts')
    @include('components.modal')
    <script>
        // Giữ lại các hàm cũ để tương thích ngược
        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        function updateCsrfToken(newToken) {
            document.querySelector('meta[name="csrf-token"]').setAttribute('content', newToken);
            if (window.jQuery) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': newToken
                    }
                });
            }
            if (window.axios) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken;
            }
        }
    </script>

    {{-- Thêm component CSRF Auto-Refresh --}}
    @include('partials.csrf-refresh')

    <!-- Pusher for realtime -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <!-- Order notifications for all admin pages -->
    <script>
        // Pusher configuration
        window.pusherKey = '{{ config('broadcasting.connections.pusher.key') }}';
        window.pusherCluster = '{{ config('broadcasting.connections.pusher.options.cluster') }}';
    </script>
    <script src="{{ asset('js/admin/orders-realtime.js') }}"></script>

</body>

</html>
