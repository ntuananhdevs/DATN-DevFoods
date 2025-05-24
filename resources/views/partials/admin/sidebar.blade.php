<div class="sidebar-header p-3 border-b border-sidebar-border flex items-center">
    <a href="" class="flex items-center gap-3">
        <div class="flex aspect-square w-8 h-8 items-center justify-center rounded-lg bg-primary text-primary-foreground">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings">
                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        </div>
        <div class="flex flex-col gap-0.5 leading-none sidebar-logo-text">
            <span class="font-semibold">Admin Panel</span>
            <span class="text-xs text-muted-foreground">v1.0.0</span>
        </div>
    </a>
</div>

<div class="sidebar-content p-4 overflow-y-auto">
    <div class="space-y-6">
        <div>
            <h3 class="text-xs font-medium text-sidebar-foreground/70 mb-2 px-2 sidebar-group-label">Main Menu</h3>
            <div class="space-y-1">
                <!-- Dashboard Dropdown -->
                <div class="sidebar-dropdown sidebar-tooltip" data-tooltip="Dashboard">
                    <button type="button" class="sidebar-dropdown-trigger flex items-center w-full rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.analytics') || request()->routeIs('admin.ecommerce') || request()->routeIs('admin.store_analytics') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                        <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-home">
                                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                        </span>
                        <span class="sidebar-text">Dashboard</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down ml-auto transition-transform sidebar-dropdown-icon">
                            <path d="m6 9 6 6 6-6"></path>
                        </svg>
                    </button>
                    <div class="sidebar-dropdown-content ml-6 pl-2 border-l border-sidebar-border mt-1 space-y-1 hidden">
                        <a href="{{ asset('admin') }}" class="flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.dashboard') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                            <span class="sidebar-text">Tổng quan</span>
                        </a>
                        <a href="{{ asset('admin/analytics') }}" class="flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.analytics') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                            <span class="sidebar-text">Analytics</span>
                        </a>
                        <a href="{{ asset('admin/ecommerce') }}" class="flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.ecommerce') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                            <span class="sidebar-text">eCommerce</span>
                        </a>
                        <a href="{{ asset('admin/store_analytics') }}" class="flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.store_analytics') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                            <span class="sidebar-text">Thống kê cửa hàng</span>
                        </a>
                    </div>
                </div>
                
                <!-- Banner -->
                <a href="{{ asset('admin/banners') }}" class="sidebar-menu-item flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.banners.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }} sidebar-tooltip" data-tooltip="Banners">
                    <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-boxes">
                            <path d="M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z"></path>
                            <path d="m7 16.5-4.74-2.85"></path>
                            <path d="m7 16.5 5-3"></path>
                            <path d="M7 16.5v5.17"></path>
                            <path d="M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z"></path>
                            <path d="m17 16.5-5-3"></path>
                            <path d="m17 16.5 4.74-2.85"></path>
                            <path d="M17 16.5v5.17"></path>
                            <path d="M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z"></path>
                            <path d="M12 8 7.26 5.15"></path>
                            <path d="m12 8 4.74-2.85"></path>
                            <path d="M12 13.5V8"></path>
                        </svg>
                    </span>
                    <span class="sidebar-text">Banners</span>
                </a>

                <!-- Products -->
                <a href="{{ asset('admin/products') }}" class="sidebar-menu-item flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.products.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }} sidebar-tooltip" data-tooltip="Products">
                    <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-boxes">
                            <path d="M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z"></path>
                            <path d="m7 16.5-4.74-2.85"></path>
                            <path d="m7 16.5 5-3"></path>
                            <path d="M7 16.5v5.17"></path>
                            <path d="M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z"></path>
                            <path d="m17 16.5-5-3"></path>
                            <path d="m17 16.5 4.74-2.85"></path>
                            <path d="M17 16.5v5.17"></path>
                            <path d="M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z"></path>
                            <path d="M12 8 7.26 5.15"></path>
                            <path d="m12 8 4.74-2.85"></path>
                            <path d="M12 13.5V8"></path>
                        </svg>
                    </span>
                    <span class="sidebar-text">Products</span>
                </a>
                
                <!-- Orders -->
                <a href="{{ asset('admin/orders') }}" class="sidebar-menu-item flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.orders.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }} sidebar-tooltip" data-tooltip="Orders">
                    <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-package">
                            <path d="m7.5 4.27 9 5.15"></path>
                            <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                            <path d="m3.3 7 8.7 5 8.7-5"></path>
                            <path d="M12 22V12"></path>
                        </svg>
                    </span>
                    <span class="sidebar-text">Orders</span>
                </a>
                
                <!-- Customers -->
                <a href="{{ asset('admin/customers') }}" class="sidebar-menu-item flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.customers.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }} sidebar-tooltip" data-tooltip="Customers">
                    <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </span>
                    <span class="sidebar-text">Customers</span>
                </a>
            <!-- driver -->
            <div class="sidebar-dropdown">
                <button class="sidebar-dropdown-trigger flex w-full items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.driver.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                    <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </span>
                    <span class="sidebar-text">Tài xế</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-auto lucide lucide-chevron-down">
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                </button>
                <div class="sidebar-dropdown-content hidden pl-4">
                    <a href="{{ asset('admin/drivers') }}" class="sidebar-menu-item flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.driver.index') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                        <span class="sidebar-text">Tất cả tài xế</span>
                    </a>
                    <a href="{{ asset('admin/drivers/applications') }}" class="sidebar-menu-item flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.driver.applications') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                        <span class="sidebar-text">Đơn ứng tuyển</span>
                    </a>
                </div>
            </div>
                
                <!-- Analytics Reports -->
                <a href="{{ asset('admin/analytics-reports') }}" class="sidebar-menu-item flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.analytics-reports.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }} sidebar-tooltip" data-tooltip="Analytics">
                    <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bar-chart-3">
                            <path d="M3 3v18h18"></path>
                            <path d="M18 17V9"></path>
                            <path d="M13 17V5"></path>
                            <path d="M8 17v-3"></path>
                        </svg>
                    </span>
                    <span class="sidebar-text">Analytics</span>
                </a>
                
                <!-- Stores -->
                <a href="{{ asset('admin/stores') }}" class="sidebar-menu-item flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.stores.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }} sidebar-tooltip" data-tooltip="Stores">
                    <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-store">
                            <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"></path>
                            <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
                            <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"></path>
                            <path d="M2 7h20"></path>
                            <path d="M22 7v3a2 2 0 0 1-2 2v0a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12v0a2 2 0 0 1-2-2V7"></path>
                        </svg>
                    </span>
                    <span class="sidebar-text">Stores</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="sidebar-footer p-4 mt-auto border-t border-sidebar-border">
    <a href="{{ asset('admin/settings') }}" class="sidebar-menu-item flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.settings.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }} sidebar-tooltip" data-tooltip="Settings">
        <span class="sidebar-icon-container mr-2 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings">
                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        </span>
        <span class="sidebar-text">Settings</span>
    </a>
</div>

<script>
    // Đảm bảo script này chạy sau khi DOM đã được tải
    document.addEventListener('DOMContentLoaded', function() {
        // Lấy tất cả các dropdown trigger
        const dropdownTriggers = document.querySelectorAll('.sidebar-dropdown-trigger');
        
        // Kiểm tra xem có submenu nào cần được mở không
        dropdownTriggers.forEach(function(trigger) {
            const content = trigger.nextElementSibling;
            const hasActiveChild = content && content.querySelector('.bg-sidebar-accent');
            
            // Nếu có item con active, mở submenu
            if (hasActiveChild && content) {
                content.classList.remove('hidden');
                
                // Xoay icon
                const icon = trigger.querySelector('.sidebar-dropdown-icon');
                if (icon) {
                    icon.style.transform = 'rotate(180deg)';
                }
                
                // Thêm class active cho trigger
                trigger.classList.add('bg-sidebar-accent', 'text-sidebar-accent-foreground', 'font-medium');
            }
            
            // Thêm event listener cho mỗi dropdown trigger
            trigger.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Lấy dropdown content tương ứng
                const content = this.nextElementSibling;
                
                // Toggle class hidden
                if (content) {
                    content.classList.toggle('hidden');
                    
                    // Xoay icon nếu có
                    const icon = this.querySelector('.sidebar-dropdown-icon');
                    if (icon) {
                        icon.style.transform = content.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
                    }
                }
            };
        });
    });
</script>
