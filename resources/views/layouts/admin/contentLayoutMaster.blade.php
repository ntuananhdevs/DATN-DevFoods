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
    <link rel="stylesheet" href="{{ asset('css/custom-realtime.css') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/nouislider@15.6.1/dist/nouislider.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/nouislider@15.6.1/dist/nouislider.min.js"></script>
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        border: "hsl(var(--border))",
                        input: "hsl(var(--input))",
                        ring: "hsl(var(--ring))",
                        background: "hsl(var(--background))",
                        foreground: "hsl(var(--foreground))",
                        primary: {
                            DEFAULT: "hsl(var(--primary))",
                            foreground: "hsl(var(--primary-foreground))",
                        },
                        secondary: {
                            DEFAULT: "hsl(var(--secondary))",
                            foreground: "hsl(var(--secondary-foreground))",
                        },
                        destructive: {
                            DEFAULT: "hsl(var(--destructive))",
                            foreground: "hsl(var(--destructive-foreground))",
                        },
                        muted: {
                            DEFAULT: "hsl(var(--muted))",
                            foreground: "hsl(var(--muted-foreground))",
                        },
                        accent: {
                            DEFAULT: "hsl(var(--accent))",
                            foreground: "hsl(var(--accent-foreground))",
                        },
                        popover: {
                            DEFAULT: "hsl(var(--popover))",
                            foreground: "hsl(var(--popover-foreground))",
                        },
                        card: {
                            DEFAULT: "hsl(var(--card))",
                            foreground: "hsl(var(--card-foreground))",
                        },
                        sidebar: {
                            DEFAULT: "hsl(var(--sidebar-background))",
                            foreground: "hsl(var(--sidebar-foreground))",
                            border: "hsl(var(--sidebar-border))",
                            primary: "hsl(var(--sidebar-primary))",
                            "primary-foreground": "hsl(var(--sidebar-primary-foreground))",
                            accent: "hsl(var(--sidebar-accent))",
                            "accent-foreground": "hsl(var(--sidebar-accent-foreground))",
                        },
                    },
                    borderRadius: {
                        lg: "var(--radius)",
                        md: "calc(var(--radius) - 2px)",
                        sm: "calc(var(--radius) - 4px)",
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    
    <style>
        :root {
            --background: 0 0% 100%;
            --foreground: 222.2 84% 4.9%;
            --card: 0 0% 100%;
            --card-foreground: 222.2 84% 4.9%;
            --popover: 0 0% 100%;
            --popover-foreground: 222.2 84% 4.9%;
            --primary: 221.2 83.2% 53.3%;
            --primary-foreground: 210 40% 98%;
            --secondary: 210 40% 96.1%;
            --secondary-foreground: 222.2 47.4% 11.2%;
            --muted: 210 40% 96.1%;
            --muted-foreground: 215.4 16.3% 46.9%;
            --accent: 210 40% 96.1%;
            --accent-foreground: 222.2 47.4% 11.2%;
            --destructive: 0 84.2% 60.2%;
            --destructive-foreground: 210 40% 98%;
            --border: 214.3 31.8% 91.4%;
            --input: 214.3 31.8% 91.4%;
            --ring: 221.2 83.2% 53.3%;
            --radius: 0.5rem;
            
            /* Scrollbar variables */
            --scrollbar-width: 5px;
            --scrollbar-track: transparent;
            --scrollbar-thumb: rgba(156, 163, 175, 0.5);
            --scrollbar-thumb-hover: rgba(156, 163, 175, 0.8);
            
            /* Sidebar variables */
            --sidebar-background: 0 0% 98%;
            --sidebar-foreground: 240 5.3% 26.1%;
            --sidebar-primary: 240 5.9% 10%;
            --sidebar-primary-foreground: 0 0% 98%;
            --sidebar-accent: 240 4.8% 95.9%;
            --sidebar-accent-foreground: 240 5.9% 10%;
            --sidebar-border: 220 13% 91%;
            --sidebar-ring: 217.2 91.2% 59.8%;
            
            /* Sidebar width variables */
            --sidebar-width: 16rem;
            --sidebar-width-collapsed: 5rem;
        }

        .dark {
            --background: 222.2 84% 4.9%;
            --foreground: 210 40% 98%;
            --card: 222.2 84% 4.9%;
            --card-foreground: 210 40% 98%;
            --popover: 222.2 84% 4.9%;
            --popover-foreground: 210 40% 98%;
            --primary: 217.2 91.2% 59.8%;
            --primary-foreground: 222.2 47.4% 11.2%;
            --secondary: 217.2 32.6% 17.5%;
            --secondary-foreground: 210 40% 98%;
            --muted: 217.2 32.6% 17.5%;
            --muted-foreground: 215 20.2% 65.1%;
            --accent: 217.2 32.6% 17.5%;
            --accent-foreground: 210 40% 98%;
            --destructive: 0 62.8% 30.6%;
            --destructive-foreground: 210 40% 98%;
            --border: 217.2 32.6% 17.5%;
            --input: 217.2 32.6% 17.5%;
            --ring: 224.3 76.3% 48%;
            
            /* Sidebar variables */
            --sidebar-background: 240 5.9% 10%;
            --sidebar-foreground: 240 4.8% 95.9%;
            --sidebar-primary: 224.3 76.3% 48%;
            --sidebar-primary-foreground: 0 0% 100%;
            --sidebar-accent: 240 3.7% 15.9%;
            --sidebar-accent-foreground: 240 4.8% 95.9%;
            --sidebar-border: 240 3.7% 15.9%;
            --sidebar-ring: 217.2 91.2% 59.8%;

            /* Dark mode scrollbar */
            --scrollbar-thumb: rgba(75, 85, 99, 0.5);
            --scrollbar-thumb-hover: rgba(75, 85, 99, 0.8);
        }

        /* Global scrollbar styles */
        * {
            scrollbar-width: 5px;
            -ms-overflow-style: auto;
        }

        *::-webkit-scrollbar {
            width: var(--scrollbar-width);
        }

        *::-webkit-scrollbar-track {
            background: var(--scrollbar-track);
        }

        *::-webkit-scrollbar-thumb {
            background-color: var(--scrollbar-thumb);
            border-radius: 0;
        }

        *::-webkit-scrollbar-thumb:hover {
            background-color: var(--scrollbar-thumb-hover);
        }

        *::-webkit-scrollbar-button {
            display: none;
        }

        body {
            font-family: 'Inter', sans-serif;
        }
        
        /* Sidebar styles */
        .sidebar {
            width: var(--sidebar-width);
            background-color: hsl(var(--sidebar-background));
            color: hsl(var(--sidebar-foreground));
            border-right: 1px solid hsl(var(--sidebar-border));
            transition: width 0.3s ease;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 40;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar.collapsed {
            width: var(--sidebar-width-collapsed);
        }
        
        .sidebar-content {
            flex: 1;
            overflow-y: auto;
            scrollbar-width: thin;
            -ms-overflow-style: auto;
        }

        .sidebar-content::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-content::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-content::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 0;
        }

        .sidebar-content::-webkit-scrollbar-thumb:hover {
            background-color: rgba(156, 163, 175, 0.8);
        }

        .sidebar-content::-webkit-scrollbar-button {
            display: none;
        }

        /* Dark mode scrollbar */
        .dark .sidebar-content::-webkit-scrollbar-thumb {
            background-color: rgba(75, 85, 99, 0.5);
        }

        .dark .sidebar-content::-webkit-scrollbar-thumb:hover {
            background-color: rgba(75, 85, 99, 0.8);
        }
        
        /* Sidebar text hiding when collapsed */
        .sidebar.collapsed .sidebar-text {
            display: none;
        }
        
        .sidebar.collapsed .sidebar-logo-text {
            display: none;
        }
        
        .sidebar.collapsed .sidebar-dropdown-icon {
            display: none;
        }
        
        .sidebar.collapsed .sidebar-dropdown-content {
            display: none !important;
        }
        
        .sidebar.collapsed .sidebar-group-label {
            display: none;
        }
        
        /* Center icons when collapsed */
        .sidebar.collapsed .sidebar-menu-item {
            display: flex;
            justify-content: center;
        }
        
        .sidebar.collapsed .sidebar-icon-container {
            margin-right: 0 !important;
        }
        
        /* Main content styles */
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            transition: margin-left 0.3s ease, width 0.3s ease;
            min-height: 100vh;
        }
        
        .main-content.expanded {
            margin-left: var(--sidebar-width-collapsed);
            width: calc(100% - var(--sidebar-width-collapsed));
        }
        
        /* Card styles */
        .card {
            background-color: hsl(var(--card));
            color: hsl(var(--card-foreground));
            border: 1px solid hsl(var(--border));
            border-radius: var(--radius);
        }
        
        /* Button styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius);
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }
        
        .btn-primary {
            background-color: hsl(var(--primary));
            color: hsl(var(--primary-foreground));
        }
        
        .btn-primary:hover {
            opacity: 0.9;
        }
        
        .btn-outline {
            background-color: transparent;
            border: 1px solid hsl(var(--border));
            color: hsl(var(--foreground));
        }
        
        .btn-outline:hover {
            background-color: hsl(var(--accent));
            color: hsl(var(--accent-foreground));
        }
        
        /* Badge styles */
        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-primary {
            background-color: hsl(var(--primary));
            color: hsl(var(--primary-foreground));
        }
        
        .badge-outline {
            background-color: transparent;
            border: 1px solid hsl(var(--border));
            color: hsl(var(--foreground));
        }
        
        /* Progress bar */
        .progress {
            height: 0.5rem;
            width: 100%;
            background-color: hsl(var(--secondary));
            border-radius: 9999px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            background-color: hsl(var(--primary));
            transition: width 0.3s ease;
        }
        
        /* Animation classes */
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Tooltip for collapsed sidebar */
        .sidebar-tooltip {
            position: relative;
        }
        
        .sidebar.collapsed .sidebar-tooltip:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            background-color: hsl(var(--background));
            color: hsl(var(--foreground));
            padding: 0.5rem;
            border-radius: var(--radius);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            white-space: nowrap;
            z-index: 50;
            margin-left: 10px;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
                width: var(--sidebar-width) !important;
            }
            
            .sidebar.mobile-open .sidebar-text,
            .sidebar.mobile-open .sidebar-logo-text,
            .sidebar.mobile-open .sidebar-dropdown-icon,
            .sidebar.mobile-open .sidebar-group-label {
                display: block;
            }
            
            .sidebar.mobile-open .sidebar-icon-container {
                margin-right: 0.5rem;
            }
            
            .sidebar.mobile-open .sidebar-menu-item {
                justify-content: flex-start;
            }
            
            .main-content {
                width: 100%;
                margin-left: 0;
            }
            
            .main-content.expanded {
                width: 100%;
                margin-left: 0;
            }
        }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .chat-item.active {
            background-color: #fff7ed;
            border-left: 4px solid #f97316;
        }

        .message-enter {
            animation: messageSlideIn 0.3s ease-out;
        }
        
        @keyframes messageSlideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    
    @yield('styles')
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
            <div class="flex-1 p-4 md:p-6 overflow-auto">
                @yield('content')
            </div>
            
            <!-- Footer -->
            @include('partials.admin.footer')
        </main>
    </div>
    
    <script>
        // Toggle sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const toggleSidebarBtn = document.getElementById('toggle-sidebar');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            
            // Check for saved sidebar state
            const savedSidebarState = localStorage.getItem('sidebar-state');
            if (savedSidebarState === 'collapsed') {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
            }
            
            // Desktop sidebar toggle
            if (toggleSidebarBtn) {
                toggleSidebarBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                    
                    // Save sidebar state to localStorage
                    if (sidebar.classList.contains('collapsed')) {
                        localStorage.setItem('sidebar-state', 'collapsed');
                    } else {
                        localStorage.setItem('sidebar-state', 'expanded');
                    }
                });
            }
            
            // Mobile sidebar toggle
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('mobile-open');
                });
            }
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const isMobile = window.innerWidth < 1024;
                if (isMobile && !sidebar.contains(event.target) && !mobileMenuBtn.contains(event.target) && sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.remove('mobile-open');
                }
            });
            
            // Theme toggle
            const themeToggleBtn = document.getElementById('theme-toggle');
            const html = document.documentElement;
            
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function() {
                    html.classList.toggle('dark');
                    
                    // Save preference to localStorage
                    if (html.classList.contains('dark')) {
                        localStorage.setItem('theme', 'dark');
                    } else {
                        localStorage.setItem('theme', 'light');
                    }
                });
            }
            
            // Check for saved theme preference
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                html.classList.add('dark');
            }
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024 && sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.remove('mobile-open');
                }
            });
        });
    </script>
    
    <script src="{{ asset('js/modal.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
    @include('components.modal')

</body>
</html>
