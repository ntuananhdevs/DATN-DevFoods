<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ứng dụng Tài xế Giao hàng - @yield('title', 'Dashboard')</title>
    <!-- Mapbox GL JS CSS -->
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet" />
    <!-- Tailwind CSS with full utilities -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
    <script>
        if (typeof mapboxgl !== 'undefined') {
            mapboxgl.accessToken = "{{ config('services.mapbox.access_token') }}";
        }
    </script>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="{{ mix('js/app.js') }}" defer></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-50">
    {{-- @include('partials.driver.header') --}}

    <main class="pb-20">
        @yield('content')
    </main>

    @include('partials.driver.bottom-nav')

    <script>
        // Mapbox access token - thay bằng token thực của bạn
        mapboxgl.accessToken = "{{ config('services.mapbox.access_token') }}"

        // GPS tracking
        let currentPosition = null;
        let watchId = null;

        function startLocationTracking() {
            if (navigator.geolocation) {
                watchId = navigator.geolocation.watchPosition(
                    function(position) {
                        currentPosition = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        updateLocationOnServer(currentPosition);
                    },
                    function(error) {
                        console.error('GPS Error:', error);
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000
                    }
                );
            }
        }

        function updateLocationOnServer(position) {
            fetch('/api/update-location', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(position)
            });
        }

        // Start tracking when page loads
        document.addEventListener('DOMContentLoaded', function() {
            startLocationTracking();
        });
    </script>

    @stack('scripts')
</body>

</html>
