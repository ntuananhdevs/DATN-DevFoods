@php
    $configData = Helper::applClasses();
@endphp

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="#" class="logo">
            <div class="logo-icon">
                <i class="ri-cloud-line"></i>
            </div>
            <span class="logo-text">DEVFOODS</span>
        </a>
    </div>

    <div class="sidebar-content">
        <div class="sidebar-section">
            <ul class="sidebar-menu">
                @if (isset($menuData[0]) && isset($menuData[0]->menu))
                    @foreach ($menuData[0]->menu as $menu)
                        @if (isset($menu->navheader))
                            <div class="divider"></div>
                            <h3 class="sidebar-section-title">{{ $menu->navheader }}</h3>
                        @else
                            @php
                                $custom_classes = optional($menu)->classlist;
                                $translation = optional($menu)->i18n;
                                
                                $menuUrl = optional($menu)->url;
                                $menuUrlWithoutSlash = ltrim($menuUrl, '/');
                                $activeClass =
                                    request()->is($menuUrl) ||
                                    request()->is($menuUrlWithoutSlash) ||
                                    request()->is('admin/' . $menuUrlWithoutSlash)
                                        ? 'active'
                                        : '';
                            @endphp

                            <li class="sidebar-menu-item">
                                <a href="{{ optional($menu)->url }}" class="sidebar-menu-link {{ $activeClass }}">
                                    <span class="sidebar-menu-icon"><i class="{{ optional($menu)->icon }}"></i></span>
                                    <span class="sidebar-menu-text">{{ __(optional($menu)->name) }}</span>
                                    
                                    @if (isset($menu->badge))
                                        <span class="sidebar-menu-badge">{{ $menu->badge }}</span>
                                    @endif
                                    
                                    @if (isset($menu->submenu) && is_array($menu->submenu))
                                        <i class="ri-arrow-right-s-line sidebar-menu-arrow"></i>
                                    @endif
                                </a>

                                @if (isset($menu->submenu) && is_array($menu->submenu))
                                    <ul class="sidebar-submenu" id="{{ strtolower(str_replace(' ', '-', optional($menu)->name)) }}">
                                        @foreach($menu->submenu as $submenu)
                                            @php
                                                $submenuUrlWithoutSlash = ltrim($submenu->url, '/');
                                                $submenuActiveClass = request()->is($submenu->url) || 
                                                                    request()->is($submenuUrlWithoutSlash) || 
                                                                    request()->is('admin/'.$submenuUrlWithoutSlash) ? 'active' : '';
                                            @endphp
                                            <li class="sidebar-submenu-item">
                                                <a href="{{ $submenu->url }}" class="sidebar-submenu-link {{ $submenuActiveClass }}">
                                                    {{ __('locale.'.$submenu->name) }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endif
                    @endforeach
                @endif
            </ul>
        </div>
    </div>
</div>
<style>
    :root {
        --primary: #6366f1;
        --primary-hover: #4f46e5;
        --primary-light: #eef2ff;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --bg-sidebar: #ffffff;
        --bg-main: #f8fafc;
        --border-color: #e2e8f0;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --radius: 8px;
        --transition: all 0.2s ease;
    }

    /* Sidebar Styles */
    .sidebar {
        width: 260px;
        background-color: var(--bg-sidebar);
        border-right: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        height: 100vh;
        position: fixed;
        overflow-y: auto;
        transition: var(--transition);
        box-shadow: var(--shadow-sm);
        padding: 0;
        z-index: 1000;
    }

    .sidebar-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        color: var(--text-primary);
    }

    .logo-icon {
        width: 36px;
        height: 36px;
        background-color: var(--primary);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .logo-text {
        font-weight: 700;
        font-size: 1.25rem;
        letter-spacing: -0.025em;
    }

    .sidebar-content {
        flex: 1;
        padding: 1rem 0;
        width: 100%;
    }

    .sidebar-section {
        margin-bottom: 1.5rem;
        width: 100%;
    }

    .sidebar-section-title {
        padding: 0 1.5rem;
        margin-bottom: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--text-secondary);
        letter-spacing: 0.05em;
        width: 100%;
    }

    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
        width: 100%;
    }

    .sidebar-menu-item {
        position: relative;
        width: 100%;
    }

    .sidebar-menu-link {
        display: flex;
        align-items: center;
        padding: 0.75rem 0;
        color: var(--text-primary);
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
        position: relative;
        width: 100%;
        margin: 0;
    }

    .sidebar-menu-link:hover {
        background-color: var(--primary-light);
        color: var(--primary);
    }

    .sidebar-menu-link.active {
        background-color: var(--primary-light);
        color: var(--primary);
        font-weight: 600;
    }

    .sidebar-menu-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background-color: var(--primary);
        border-radius: 0 4px 4px 0;
        z-index: 1;
    }

    .sidebar-menu-icon {
        margin-left: 1.5rem;
        margin-right: 0.75rem;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 1.5rem;
    }

    .sidebar-menu-text {
        flex: 1;
    }

    .sidebar-menu-badge {
        background-color: var(--primary-light);
        color: var(--primary);
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.125rem 0.5rem;
        border-radius: 9999px;
    }

    .sidebar-menu-arrow {
        margin-left: 0.5rem;
        margin-right: 1.5rem;
        font-size: 1rem;
        transition: var(--transition);
    }

    .sidebar-submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        background-color: rgba(0, 0, 0, 0.02);
        width: 100%;
    }

    .sidebar-submenu.open {
        max-height: 500px;
    }

    .sidebar-submenu-item {
        list-style: none;
        width: 100%;
    }

    .sidebar-submenu-link {
        display: flex;
        align-items: center;
        padding: 0.625rem 1.5rem 0.625rem 3.5rem;
        color: var(--text-secondary);
        text-decoration: none;
        font-weight: 400;
        font-size: 0.875rem;
        transition: var(--transition);
        width: 100%;
    }

    .sidebar-submenu-link:hover {
        color: var(--primary);
    }

    .sidebar-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border-color);
        width: 100%;
    }

    .sidebar-footer-button {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 0.625rem;
        background-color: transparent;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        color: var(--text-primary);
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
    }

    .sidebar-footer-button:hover {
        background-color: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    .sidebar-footer-icon {
        margin-right: 0.5rem;
    }

    /* Divider */
    .divider {
        height: 1px;
        background-color: var(--border-color);
        margin: 1rem 0;
        width: 100%;
    }

    /* Main Content */
    .app-content {
        margin-left: 260px;
        flex: 1;
        padding: 2rem;
        transition: var(--transition);
    }

    /* Toggle Button */
    .sidebar-toggle {
        position: fixed;
        top: 1rem;
        left: 1rem;
        z-index: 1001;
        background-color: var(--primary);
        color: white;
        border: none;
        border-radius: var(--radius);
        width: 40px;
        height: 40px;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: var(--shadow-md);
        transition: var(--transition);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            z-index: 1000;
            width: 240px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            transition: transform 0.3s ease;
        }

        .sidebar.open {
            transform: translateX(0) !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            z-index: 1001 !important;
        }

        .app-content {
            margin-left: 0 !important;
            width: 100% !important;
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        .sidebar-toggle {
            display: flex;
        }
        
        /* Overlay when sidebar is open on mobile */
        body.sidebar-open::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 1;
            transition: opacity 0.3s ease;
        }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const appContent = document.querySelector('.app-content');
        // Chọn tất cả các nút toggle có thể có
        const menuToggleBtn = document.querySelector('.menu-toggle');
        const navMenuToggleBtn = document.querySelector('.nav-menu-main.menu-toggle, .nav-link.nav-menu-main.menu-toggle');
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        
        // Thêm console.log để debug các nút toggle
        console.log('Menu Toggle Button:', menuToggleBtn);
        console.log('Nav Menu Toggle Button:', navMenuToggleBtn);
        console.log('Sidebar Toggle:', sidebarToggle);
        
        // Hàm điều chỉnh chiều rộng của content và bảng
        function adjustContentAndTables(sidebarOpen) {
            if (window.innerWidth > 768) {
                if (sidebarOpen) {
                    // Khi sidebar mở
                    appContent.style.marginLeft = '260px';
                    appContent.style.width = 'calc(100% - 260px)';
                    sidebar.style.transform = 'translateX(0)';
                } else {
                    // Khi sidebar đóng
                    appContent.style.marginLeft = '0';
                    appContent.style.width = '100%';
                    sidebar.style.transform = 'translateX(-100%)';
                }
                
                // Điều chỉnh chiều rộng của tất cả các bảng và container bảng
                const tableContainers = document.querySelectorAll('.data-table-wrapper, .data-table-container, .data-table-card, .data-table');
                tableContainers.forEach(container => {
                    container.style.width = '100%';
                    container.style.maxWidth = '100%';
                });
            } else {
                // Trên mobile, luôn reset về mặc định
                appContent.style.marginLeft = '0';
                appContent.style.width = '100%';
                
                if (sidebarOpen) {
                    sidebar.style.transform = 'translateX(0)';
                    document.body.classList.add('sidebar-open');
                } else {
                    sidebar.style.transform = 'translateX(-100%)';
                    document.body.classList.remove('sidebar-open');
                }
            }
        }
        
        // Kiểm tra trạng thái ban đầu của sidebar
        if (window.innerWidth <= 768) {
            // Trên mobile, sidebar mặc định đóng
            sidebar.classList.remove('open');
            adjustContentAndTables(false);
        } else {
            // Trên desktop, sidebar mặc định mở
            sidebar.classList.add('open');
            adjustContentAndTables(true);
        }
        
        // Tạo nút toggle cho mobile nếu chưa có
        if (!sidebarToggle && window.innerWidth <= 768) {
            const toggle = document.createElement('button');
            toggle.className = 'sidebar-toggle';
            toggle.innerHTML = '<i class="ri-menu-line"></i>';
            document.body.appendChild(toggle);
            
            toggle.addEventListener('click', function() {
                console.log('Sidebar toggle button clicked');
                sidebar.classList.toggle('open');
                document.body.classList.toggle('sidebar-open');
                adjustContentAndTables(sidebar.classList.contains('open'));
            });
        } else if (sidebarToggle) {
            // Đảm bảo sidebarToggle có event listener nếu đã tồn tại
            sidebarToggle.addEventListener('click', function() {
                console.log('Existing sidebar toggle clicked');
                sidebar.classList.toggle('open');
                document.body.classList.toggle('sidebar-open');
                adjustContentAndTables(sidebar.classList.contains('open'));
            });
        }
        
        // Tạo một hàm để đảm bảo tất cả các nút toggle đều có event listener
        function ensureToggleButtonsWork() {
            // Sử dụng selector rộng hơn để bắt tất cả các nút toggle có thể có
            const allToggleButtons = document.querySelectorAll('.menu-toggle, .nav-menu-main.menu-toggle, .nav-link.nav-menu-main.menu-toggle, .sidebar-toggle, .mobile-menu .nav-link, .nav-item.mobile-menu a');
            
            allToggleButtons.forEach(btn => {
                // Thêm một thuộc tính để đánh dấu đã thêm event listener
                if (!btn.hasAttribute('data-toggle-initialized')) {
                    btn.setAttribute('data-toggle-initialized', 'true');
                    
                    // Thêm event listener mới
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        console.log('Toggle button clicked:', this);
                        sidebar.classList.toggle('open');
                        document.body.classList.toggle('sidebar-open');
                        adjustContentAndTables(sidebar.classList.contains('open'));
                    });
                }
            });
        }
        
        // Gọi hàm để đảm bảo tất cả các nút toggle đều hoạt động
        ensureToggleButtonsWork();
        
        // Không cần thêm event listener riêng cho các nút toggle này nữa
        // vì chúng đã được xử lý trong hàm ensureToggleButtonsWork
        // Giữ lại comment để dễ hiểu code
        
        // Không cần thêm event listener riêng cho các nút toggle này nữa
        // vì chúng đã được xử lý trong hàm ensureToggleButtonsWork
        
        // Xử lý khi click bên ngoài sidebar để đóng sidebar trên thiết bị di động
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768 && 
                sidebar.classList.contains('open') && 
                !sidebar.contains(e.target) && 
                (menuToggleBtn ? !menuToggleBtn.contains(e.target) : true) &&
                (sidebarToggle ? !sidebarToggle.contains(e.target) : true)) {
                sidebar.classList.remove('open');
                document.body.classList.remove('sidebar-open');
                
                // Điều chỉnh chiều rộng của content và bảng
                adjustContentAndTables(false);
            }
        });
        
        // Xử lý khi thay đổi kích thước màn hình
        window.addEventListener('resize', function() {
            // Điều chỉnh chiều rộng của content và bảng
            const isMobile = window.innerWidth <= 768;
            
            if (isMobile) {
                // Trên mobile, đóng sidebar khi resize xuống kích thước mobile
                sidebar.classList.remove('open');
                document.body.classList.remove('sidebar-open');
                adjustContentAndTables(false);
            } else {
                // Trên desktop, mở sidebar khi resize lên kích thước desktop
                sidebar.classList.add('open');
                adjustContentAndTables(true);
            }
            
            // Đảm bảo các nút toggle vẫn hoạt động sau khi resize
            setTimeout(ensureToggleButtonsWork, 300);
        });
        
        // Thêm một MutationObserver để theo dõi các thay đổi trong DOM
        // Điều này giúp đảm bảo các nút toggle được thêm vào sau khi trang tải cũng hoạt động
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    // Nếu có node mới được thêm vào, kiểm tra xem có nút toggle nào không
                    setTimeout(ensureToggleButtonsWork, 300);
                }
            });
        });
        
        // Bắt đầu theo dõi thay đổi trong body
        observer.observe(document.body, { childList: true, subtree: true });
        
        
        // Toggle submenu
        document.querySelectorAll('.sidebar-menu-link').forEach(link => {
            if (link.nextElementSibling && link.nextElementSibling.classList.contains('sidebar-submenu')) {
                link.addEventListener('click', function(e) {
                    // Nếu link có submenu, ngăn chặn hành vi mặc định
                    if (link.querySelector('.sidebar-menu-arrow')) {
                        e.preventDefault();
                        
                        // Toggle submenu
                        const submenu = link.nextElementSibling;
                        submenu.classList.toggle('open');
                        
                        // Toggle arrow direction
                        const arrow = link.querySelector('.sidebar-menu-arrow');
                        if (submenu.classList.contains('open')) {
                            arrow.style.transform = 'rotate(90deg)';
                        } else {
                            arrow.style.transform = 'rotate(0)';
                        }
                    }
                });
            }
        });
        
        // Đồng bộ với các sự kiện từ navbar và các thành phần khác
        // Lắng nghe sự kiện từ các nút đóng sidebar khác
        const sidebarCloseIcons = document.querySelectorAll('.sidebar-close-icon');
        if (sidebarCloseIcons.length > 0) {
            sidebarCloseIcons.forEach(icon => {
                icon.addEventListener('click', function() {
                    sidebar.classList.remove('open');
                    document.body.classList.remove('sidebar-open');
                    
                    // Điều chỉnh chiều rộng của content và bảng
                    adjustContentAndTables(false);
                });
            });
        }
        
        // Đảm bảo sidebar đóng khi click vào overlay
        document.body.addEventListener('click', function(e) {
            // Kiểm tra nếu click vào overlay (body.sidebar-open::before)
            if (document.body.classList.contains('sidebar-open')) {
                const sidebarRect = sidebar.getBoundingClientRect();
                if (e.clientX < sidebarRect.left || 
                    e.clientX > sidebarRect.right || 
                    e.clientY < sidebarRect.top || 
                    e.clientY > sidebarRect.bottom) {
                    // Click nằm ngoài sidebar
                    if ((!menuToggleBtn || !menuToggleBtn.contains(e.target)) && 
                        (!sidebarToggle || !sidebarToggle.contains(e.target))) {
                        sidebar.classList.remove('open');
                        document.body.classList.remove('sidebar-open');
                        
                        // Điều chỉnh chiều rộng của content và bảng
                        adjustContentAndTables(false);
                    }
                }
            }
        });
        
        // Kích hoạt sự kiện resize để đảm bảo tất cả các phần tử được điều chỉnh đúng
        window.dispatchEvent(new Event('resize'));
    });
</script>
