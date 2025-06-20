<div class="sidebar-header p-3 border-b border-sidebar-border flex items-center">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
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

<div class="sidebar-content p-4 overflow-y-auto custom-scrollbar">
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
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.dashboard') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                            <span class="sidebar-text">Tổng quan</span>
                        </a>
                        <a href="{{ route('admin.analytics') }}" class="flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.analytics') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                            <span class="sidebar-text">Analytics</span>
                        </a>
                        <a href="{{ route('admin.ecommerce') }}" class="flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.ecommerce') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                            <span class="sidebar-text">eCommerce</span>
                        </a>
                        <a href="{{ route('admin.store_analytics') }}" class="flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.store_analytics') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                            <span class="sidebar-text">Thống kê cửa hàng</span>
                        </a>
                    </div>
                </div>
                <!-- Customers -->
                <div class="sidebar-dropdown sidebar-tooltip" data-tooltip="Account Management">
                    <button type="button" class="sidebar-dropdown-trigger flex items-center w-full rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                        <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </span>
                        <span class="sidebar-text">Quản lý tài khoản</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down ml-auto transition-transform sidebar-dropdown-icon">
                            <path d="m6 9 6 6 6-6"></path>
                        </svg>
                    </button>
                    <div class="sidebar-dropdown-content ml-6 pl-2 border-l border-sidebar-border mt-1 space-y-1 hidden">
                        <a href="{{ route('admin.users.index') }}" class="flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.users.index') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                            <span class="sidebar-text">Khách hàng</span>
                        </a>
                        
                        <a href="{{ ('admin.permissions.index') }}" class="flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.permissions.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                            <span class="sidebar-text">Quyền hạn</span>
                        </a>
                    </div>
                </div>

                
                <!-- Categories -->
                <a href="{{ asset('admin/categories') }}" class="sidebar-menu-item flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->is('products') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }} sidebar-tooltip" data-tooltip="Products">
                    <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="lucide lucide-grid">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                        </svg>
                    </span>
                    <span class="sidebar-text">Danh mục</span>
                </a>

                <!-- Products -->
                <div class="sidebar-dropdown sidebar-tooltip" data-tooltip="Menu Management">
                    <button type="button" class="sidebar-dropdown-trigger flex items-center w-full rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.combos.*') || request()->routeIs('admin.toppings.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                        <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag">
                                <path d="M6 2L3 6v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                                <path d="M3 6h18"></path>
                                <path d="M16 10a4 4 0 0 1-8 0"></path>
                            </svg>
                        </span>
                        <span class="sidebar-text">Thực đơn</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down ml-auto transition-transform sidebar-dropdown-icon">
                            <path d="m6 9 6 6 6-6"></path>
                        </svg>
                    </button>
                    <div class="sidebar-dropdown-content ml-6 pl-2 border-l border-sidebar-border mt-1 space-y-1 hidden">
                        <a href="{{ route('admin.products.index') }}" class="flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.products.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                            <span class="sidebar-text">Món ăn</span>
                        </a>
                        <a href="{{ route('admin.combos.index') }}" class="flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.combos.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                            <span class="sidebar-text">Combo</span>
                        </a>
                        <a href="{{ route('admin.toppings.index') }}" class="flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.toppings.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                            <span class="sidebar-text">Topping</span>
                        </a>
                    </div>
                </div>
                <!-- Banner -->
                <a href="{{ route('admin.banners.index') }}" class="sidebar-menu-item flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.banners.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }} sidebar-tooltip" data-tooltip="Banners">
                    <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-image">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                    </span>
                    <span class="sidebar-text">Banners</span>
                </a>
                 <!-- Chat -->
                <a href="{{ route('admin.chat') }}" class="sidebar-menu-item flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.branches.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }} sidebar-tooltip" data-tooltip="Branches">
                    <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-circle">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                        </svg>
                    </span>
                    <span class="sidebar-text">Chat</span>
                </a>
                <!-- Orders -->
                <a href="{{ ('admin.orders.index') }}" class="sidebar-menu-item flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.orders.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }} sidebar-tooltip" data-tooltip="Orders">
                    <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-package">
                            <path d="m7.5 4.27 9 5.15"></path>
                            <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                            <path d="m3.3 7 8.7 5 8.7-5"></path>
                            <path d="M12 22V12"></path>
                        </svg>
                    </span>
                    <span class="sidebar-text">Đơn hàng</span>
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
                <a href="{{ ('admin.analytics-reports') }}" class="sidebar-menu-item flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.analytics-reports.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }} sidebar-tooltip" data-tooltip="Analytics">
                    <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bar-chart-3">
                            <path d="M3 3v18h18"></path>
                            <path d="M18 17V9"></path>
                            <path d="M13 17V5"></path>
                            <path d="M8 17v-3"></path>
                        </svg>
                    </span>
                    <span class="sidebar-text">Báo cáo</span>
                </a>

                <!-- Branches -->
                <a href="{{ route('admin.branches.index') }}" class="sidebar-menu-item flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.branches.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }} sidebar-tooltip" data-tooltip="Branches">
                    <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-store">
                            <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"></path>
                            <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
                            <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"></path>
                            <path d="M2 7h20"></path>
                            <path d="M22 7v3a2 2 0 0 1-2 2v0a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12v0a2 2 0 0 1-2-2V7"></path>
                        </svg>
                    </span>
                    <span class="sidebar-text">Chi nhánh</span>
                </a>

               
            </div>
        </div>
    </div>
</div>

<div class="sidebar-footer p-4 mt-auto border-t border-sidebar-border">
    <a href="{{ ('admin.settings') }}" class="sidebar-menu-item flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('admin.settings.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }} sidebar-tooltip" data-tooltip="Settings">
        <span class="sidebar-icon-container mr-2 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings">
                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        </span>
        <span class="sidebar-text">Cài đặt</span>
    </a>
</div>

<style>
    .custom-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #d1d5db transparent;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 2px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #d1d5db;
        border-radius: 20px;
        border: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: #9ca3af;
    }

    /* Dark mode */
    .dark .custom-scrollbar {
        scrollbar-color: #4b5563 transparent;
    }

    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #4b5563;
    }

    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: #6b7280;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownTriggers = document.querySelectorAll('.sidebar-dropdown-trigger');

        dropdownTriggers.forEach(function(trigger) {
            const content = trigger.nextElementSibling;
            const hasActiveChild = content && content.querySelector('.bg-sidebar-accent');

            if (hasActiveChild && content) {
                content.classList.remove('hidden');
                const icon = trigger.querySelector('.sidebar-dropdown-icon');
                if (icon) {
                    icon.style.transform = 'rotate(180deg)';
                }
                trigger.classList.add('bg-sidebar-accent', 'text-sidebar-accent-foreground', 'font-medium');
            }

            trigger.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();

                const content = this.nextElementSibling;
                if (content) {
                    content.classList.toggle('hidden');
                    const icon = this.querySelector('.sidebar-dropdown-icon');
                    if (icon) {
                        icon.style.transform = content.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
                    }
                }
            };
        });
    });
</script>