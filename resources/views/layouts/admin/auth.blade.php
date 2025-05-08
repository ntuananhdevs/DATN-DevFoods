<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang quản trị - Đăng nhập</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap CSS CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Optional: Custom styles --}}
    <style>
        body {
            background-color: #f5f5f5;
        }
        .container {
            max-width: 500px;
        }
    </style>
</head>
<body>
    <main class="d-flex justify-content-center align-items-center min-vh-100">
        @yield('content')
    </main>

    {{-- Bootstrap JS (optional) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
