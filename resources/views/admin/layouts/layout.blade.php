<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #ffffff; /* Đổi thành trắng */
            color: #5e5873; /* Đổi màu chữ thành xám đậm */
            padding: 20px 0;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1); /* Thêm bóng nhẹ */
        }

        .logo {
            display: flex;
            align-items: center;
            padding: 0 20px;
            font-size: 24px;
            font-weight: bold;
            color: #5e50ee; /* Đổi màu logo thành tím sáng */
            margin-bottom: 20px;
        }

        .logo i {
            margin-right: 10px;
        }

        .menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .menu-header {
            padding: 10px 20px;
            font-size: 12px;
            text-transform: uppercase;
            color: #a1a1a1; /* Màu nhạt hơn cho header */
            margin-top: 10px;
        }

        .menu-item {
            margin: 5px 0;
        }

        .menu-item a {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            color: #5e5873; /* Màu chữ xám đậm */
            text-decoration: none;
            font-size: 14px;
            transition: background 0.3s;
        }

        .menu-item a:hover {
            background-color: #f1f3f5; /* Màu nền khi hover */
        }

        .menu-item.active a {
            background-color: #5e50ee; /* Màu tím sáng cho mục active */
            color: #ffffff;
            border-radius: 5px;
        }

        .menu-item a i {
            margin-right: 10px;
            font-size: 16px;
        }

        .arrow {
            margin-left: auto;
            font-size: 12px;
        }

        .badge {
            margin-left: auto;
            background-color: #ff9f43;
            color: #fff;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
        }

        .badge.new {
            background-color: #5e50ee; /* Đổi màu badge "NEW" */
        }

        /* Header (Navbar) Styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #ffffff; /* Đổi thành trắng */
            padding: 10px 20px;
            margin: 20px;
            color: #5e5873; /* Màu chữ xám đậm */
            height: 60px;
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Thêm bóng nhẹ */
            z-index: 1000;
        }

        .navbar-left, .navbar-right {
            display: flex;
            align-items: center;
        }

        .navbar-left .icon, .navbar-right .icon {
            margin: 0 10px;
            font-size: 18px;
            color: #5e5873; /* Màu icon xám đậm */
            position: relative;
            cursor: pointer;
        }

        .navbar-left .icon.star {
            color: #ff9f43;
        }

        .navbar-right .icon {
            margin: 0 5px;
        }

        .navbar-right .language {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }

        .navbar-right .language img {
            width: 20px;
            margin-right: 5px;
        }

        .navbar-right .user {
            display: flex;
            align-items: center;
        }

        .navbar-right .user img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-left: 10px;
        }

        .navbar-right .user .status {
            display: flex;
            align-items: center;
            font-size: 14px;
        }

        .navbar-right .user .status .dot {
            width: 10px;
            height: 10px;
            background-color: #28c76f;
            border-radius: 50%;
            margin-right: 5px;
        }

        .navbar-right .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #5e50ee; /* Đổi màu badge */
            color: #fff;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
        }

        /* Main Content Area */
        .main-content {
            margin-left: 250px;
            margin-top: 60px;
            padding: 20px;
            background-color: #ffffff; /* Đổi thành trắng */
            min-height: calc(100vh - 60px);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-v"></i> Vuexy
        </div>
        <ul class="menu">
            <li class="menu-item active">
                <a href="#">
                    <i class="fas fa-home"></i> Dashboard
                    <span class="badge">2</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="#">
                    <i class="fas fa-chart-line"></i> Analytics
                </a>
            </li>
            <li class="menu-item">
                <a href="#">
                    <i class="fas fa-shopping-cart"></i> eCommerce
                </a>
            </li>
            <li class="menu-header">APPS</li>
            <li class="menu-item">
                <a href="#">
                    <i class="fas fa-envelope"></i> Email
                </a>
            </li>
            <li class="menu-item">
                <a href="#">
                    <i class="fas fa-comment-alt"></i> Chat
                </a>
            </li>
            <li class="menu-item">
                <a href="#">
                    <i class="fas fa-check-square"></i> Todo
                </a>
            </li>
            <li class="menu-item">
                <a href="#">
                    <i class="fas fa-calendar-alt"></i> Calendar
                </a>
            </ymology>
            <li class="menu-item">
                <a href="#">
                    <i class="fas fa-shopping-cart"></i> Ecommerce
                    <i class="fas fa-chevron-right arrow"></i>
                </a>
            </li>
            <li class="menu-item">
                <a href="#">
                    <i class="fas fa-user"></i> Product
                    <i class="fas fa-chevron-right arrow"></i>
                </a>
            </li>
            <li class="menu-item">
                <a href="#">
                    <i class="fas fa-user"></i> Driver
                    <i class="fas fa-chevron-right arrow"></i>
                </a>
            </li>
            <li class="menu-item">
                <a href="#">
                    <i class="fas fa-user"></i> User
                    <i class="fas fa-chevron-right arrow"></i>
                </a>
            </li>
            <li class="menu-header">UI ELEMENTS</li>
            <li class="menu-item">
                <a href="#">
                    <i class="fas fa-list"></i> Data List
                    <span class="badge new">NEW</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="#">
                    <i class="fas fa-th-large"></i> Content
                    <i class="fas fa-chevron-right arrow"></i>
                </a>
            </li>
        </ul>
    </div>

    <!-- Header (Navbar) -->
    <div class="navbar">
        <div class="navbar-left">
            <div class="icon"><i class="fas fa-th-large"></i></div>
            <div class="icon"><i class="fas fa-comment-alt"></i></div>
            <div class="icon"><i class="fas fa-envelope"></i></div>
            <div class="icon"><i class="fas fa-calendar-alt"></i></div>
            <div class="icon star"><i class="fas fa-star"></i></div>
        </div>
        <div class="navbar-right">
            <div class="icon"><i class="fas fa-search"></i></div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="badge">6</span>
            </div>
            <div class="icon">
                <i class="fas fa-bell"></i>
                <span class="badge">5</span>
            </div>
            <div class="language">
                <img src="https://flagcdn.com/16x12/us.png" alt="US Flag">
                <span>English</span>
            </div>
            <div class="user">
                <div class="status">
                    <span class="dot"></span>
                    <span>Available</span>
                </div>
                <span style="margin-left: 10px;">John Doe</span>
                <img src="https://randomuser.me/api/portraits/men/1.jpg" alt="User Avatar">
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        @yield('content')
    </div>
</body>
</html>