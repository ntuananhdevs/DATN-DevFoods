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




document.addEventListener('DOMContentLoaded', function () {
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
        toggleSidebarBtn.addEventListener('click', function () {
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
        mobileMenuBtn.addEventListener('click', function () {
            sidebar.classList.toggle('mobile-open');
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function (event) {
        const isMobile = window.innerWidth < 1024;
        if (isMobile && !sidebar.contains(event.target) && !mobileMenuBtn.contains(event.target) &&
            sidebar.classList.contains('mobile-open')) {
            sidebar.classList.remove('mobile-open');
        }
    });

    // Theme toggle
    const themeToggleBtn = document.getElementById('theme-toggle');
    const html = document.documentElement;

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function () {
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
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 1024 && sidebar.classList.contains('mobile-open')) {
            sidebar.classList.remove('mobile-open');
        }
    });
});