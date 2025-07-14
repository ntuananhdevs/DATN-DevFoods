@php
    $user = Auth::guard('manager')->user();
@endphp

@if ($user && $user->hasRole('manager'))
    <div class="sidebar-header p-3 border-b border-sidebar-border flex justify-center items-center">
        <a href="{{ route('branch.dashboard') }}" class="flex items-center gap-3">
            <div
                class="flex aspect-square w-8 h-8 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings">
                    <path
                        d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z">
                    </path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            </div>
            <div class="flex flex-col gap-0.5 leading-none sidebar-logo-text">
                <span class="font-semibold">PolyCrispyWings</span>
                <span class="text-xs text-muted-foreground">v1.0.0</span>
            </div>
        </a>
    </div>
    <div class="sidebar-content p-4 overflow-y-auto custom-scrollbar">
        <div class="space-y-6">
            <div>
            <div class="sidebar-dropdown sidebar-tooltip" data-tooltip="Dashboard">
                    <button type="button"
                        class="sidebar-dropdown-trigger flex items-center w-full rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('branch.dashboard') || request()->routeIs('branch.drivers-statistics') || request()->routeIs('branch.order-statistics') || request()->routeIs('branch.food-statistics') || request()->routeIs('branch.customer-statistics') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                        <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-home">
                                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                        </span>
                        <span class="sidebar-text">Dashboard</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-chevron-down ml-auto transition-transform sidebar-dropdown-icon">
                            <path d="m6 9 6 6 6-6"></path>
                        </svg>
                    </button>
                    <div
                        class="sidebar-dropdown-content ml-6 pl-2 border-l border-sidebar-border mt-1 space-y-1 hidden">
                        <a href="{{ route('branch.dashboard') }}"
                            class="flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('branch.dashboard') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                            <span class="sidebar-text">Tổng quan</span>
                        </a>
                        <a href="{{ route('branch.driver-statistics') }}"
                            class="flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('branch.driver-statistics') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                            <span class="sidebar-text">Tài xế</span>
                        </a>
                        <a href="{{ route('branch.order-statistics') }}"
                            class="flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('branch.order-statistics') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                            <span class="sidebar-text">Đơn hàng</span>
                        </a>
                        <a href="{{ route('branch.food-statistics') }}"
                            class="flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('branch.food-statistics') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                            <span class="sidebar-text">Món ăn</span>
                        </a>
                        <a href="{{ route('branch.customer-statistics') }}"
                            class="flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('branch.customer-statistics') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                            <span class="sidebar-text">Khách hàng</span>
                        </a>
                    </div>
                </div>

                <a href="{{ route('branch.categories') }}"
                    class="sidebar-menu-item flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('branch.categories') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }} sidebar-tooltip"
                    data-tooltip="Danh mục">
                    <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-grid-3x3">
                            <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                            <path d="M9 3v18"></path>
                            <path d="M15 3v18"></path>
                            <path d="M3 9h18"></path>
                            <path d="M3 15h18"></path>
                        </svg>
                    </span>
                    <span class="sidebar-text">Danh mục</span>
                </a>
                <a href="{{ route('branch.orders.index') }}"
                    class="sidebar-menu-item flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('branch.orders.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }} sidebar-tooltip"
                    data-tooltip="Đơn hàng">
                    <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-package">
                            <path d="m7.5 4.27 9 5.15"></path>
                            <path
                                d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z">
                            </path>
                            <path d="m3.3 7 8.7 5 8.7-5"></path>
                            <path d="M12 22V12"></path>
                        </svg>
                    </span>
                    <span class="sidebar-text">Đơn hàng</span>
                </a>
                <a href="{{ route('branch.staff') }}"
                    class="sidebar-menu-item flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('branch.staff') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }} sidebar-tooltip"
                    data-tooltip="Nhân viên">
                    <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-users">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </span>
                    <span class="sidebar-text">Nhân viên</span>
                </a>
                <div class="sidebar-dropdown sidebar-tooltip" data-tooltip="Thực đơn">
                    <button type="button"
                        class="sidebar-dropdown-trigger flex items-center w-full rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('branch.products.*') || request()->routeIs('branch.combos.*') || request()->routeIs('branch.toppings.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">
                        <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag">
                                <path d="M6 2L3 6v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                                <path d="M3 6h18"></path>
                                <path d="M16 10a4 4 0 0 1-8 0"></path>
                            </svg>
                        </span>
                        <span class="sidebar-text">Thực đơn</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-chevron-down ml-auto transition-transform sidebar-dropdown-icon">
                            <path d="m6 9 6 6 6-6"></path>
                        </svg>
                    </button>
                    <div
                        class="sidebar-dropdown-content ml-6 pl-2 border-l border-sidebar-border mt-1 space-y-1 {{ request()->routeIs('branch.products.*') || request()->routeIs('branch.combos.*') || request()->routeIs('branch.toppings.*') ? '' : 'hidden' }}">
                        <a href="{{ route('branch.products') }}"
                            class="flex items-center rounded-md p-2 text-sm {{ request()->routeIs('branch.products.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">Món ăn</a>
                        <a href="{{ route('branch.combos') }}"
                            class="flex items-center rounded-md p-2 text-sm {{ request()->routeIs('branch.combos.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">Combo</a>
                        <a href="{{ route('branch.toppings') }}"
                            class="flex items-center rounded-md p-2 text-sm {{ request()->routeIs('branch.toppings.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }}">Topping</a>
                    </div>
                </div>

                <a href="{{ route('branch.chat.index') }}"
                    class="sidebar-menu-item flex items-center rounded-md p-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground {{ request()->routeIs('branch.chat.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-medium' : '' }} sidebar-tooltip"
                    data-tooltip="Chat">
                    <span class="sidebar-icon-container mr-2 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-message-circle">
                            <path
                                d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                    </svg>
                    </span>
                    <span class="sidebar-text">Chat</span>
                </a>


            </div>
        </div>
    </div>
@endif

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

    /* Active menu item styles */
    .sidebar-menu-item.bg-sidebar-accent,
    .sidebar-dropdown-trigger.bg-sidebar-accent {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        transform: translateX(2px);
        transition: all 0.3s ease;
    }

    .sidebar-menu-item.bg-sidebar-accent:hover,
    .sidebar-dropdown-trigger.bg-sidebar-accent:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%) !important;
        transform: translateX(4px);
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
    }

    .sidebar-menu-item.bg-sidebar-accent .sidebar-icon-container,
    .sidebar-dropdown-trigger.bg-sidebar-accent .sidebar-icon-container {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 6px;
        padding: 2px;
    }

    /* Active dropdown content items */
    .sidebar-dropdown-content a.bg-sidebar-accent {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
        color: white !important;
        border-left: 3px solid #ffffff;
        margin-left: -3px;
        padding-left: 12px;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
    }

    .sidebar-dropdown-content a.bg-sidebar-accent:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%) !important;
    }

    /* Hover effects for non-active items */
    .sidebar-menu-item:hover:not(.bg-sidebar-accent),
    .sidebar-dropdown-trigger:hover:not(.bg-sidebar-accent) {
        background: rgba(59, 130, 246, 0.1) !important;
        color: #3b82f6 !important;
        transform: translateX(2px);
        transition: all 0.2s ease;
    }

    .sidebar-dropdown-content a:hover:not(.bg-sidebar-accent) {
        background: rgba(59, 130, 246, 0.1) !important;
        color: #3b82f6 !important;
        border-left: 2px solid #3b82f6;
        margin-left: -2px;
        padding-left: 11px;
    }

    /* Animation for active state */
    @keyframes activePulse {
        0% { box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
        50% { box-shadow: 0 4px 20px rgba(59, 130, 246, 0.5); }
        100% { box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
    }

    .sidebar-menu-item.bg-sidebar-accent,
    .sidebar-dropdown-trigger.bg-sidebar-accent,
    .sidebar-dropdown-content a.bg-sidebar-accent {
        animation: activePulse 2s ease-in-out infinite;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownTriggers = document.querySelectorAll('.sidebar-dropdown-trigger');

        dropdownTriggers.forEach(function(trigger) {
            const content = trigger.nextElementSibling;
            const hasActiveChild = content && content.querySelector('.bg-sidebar-accent');

            // Open dropdown if it contains an active child
            if (hasActiveChild && content) {
                content.classList.remove('hidden');
                const icon = trigger.querySelector('.sidebar-dropdown-icon');
                if (icon) {
                    icon.style.transform = 'rotate(180deg)';
                }
                trigger.classList.add('bg-sidebar-accent', 'text-sidebar-accent-foreground',
                    'font-medium');
            }

            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                const content = this.nextElementSibling;
                if (content) {
                    content.classList.toggle('hidden');
                    const icon = this.querySelector('.sidebar-dropdown-icon');
                    if (icon) {
                        icon.style.transform = content.classList.contains('hidden') ?
                            'rotate(0deg)' : 'rotate(180deg)';
                    }
                }
            });
        });
    });
</script> 