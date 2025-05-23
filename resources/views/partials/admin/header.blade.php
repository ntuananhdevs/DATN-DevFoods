<header class="sticky top-0 z-10 flex h-16 shrink-0 items-center justify-between border-b bg-background px-4 shadow-sm">
    <div class="flex items-center gap-2">
        <!-- Mobile menu button -->
        <button id="mobile-menu-btn" class="lg:hidden flex items-center justify-center h-9 w-9 rounded-md hover:bg-accent hover:text-accent-foreground">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu">
                <line x1="4" x2="20" y1="12" y2="12"></line>
                <line x1="4" x2="20" y1="6" y2="6"></line>
                <line x1="4" x2="20" y1="18" y2="18"></line>
            </svg>
            <span class="sr-only">Toggle Menu</span>
        </button>
        
        <!-- Toggle sidebar button -->
        <button id="toggle-sidebar" class="hidden lg:flex items-center justify-center h-7 w-7 rounded-md hover:bg-accent hover:text-accent-foreground">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-panel-left">
                <rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect>
                <line x1="9" x2="9" y1="3" y2="21"></line>
            </svg>
            <span class="sr-only">Toggle Sidebar</span>
        </button>
        
        <!-- Breadcrumb -->
        <div class="hidden md:block">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li>
                        <div class="flex items-center">
                            <a href="" class="text-sm font-medium text-muted-foreground hover:text-foreground">
                                Dashboard
                            </a>
                        </div>
                    </li>
                    
                    @if(isset($breadcrumbs))
                        @foreach($breadcrumbs as $breadcrumb)
                            <li>
                                <div class="flex items-center">
                                    <span class="mx-2 text-muted-foreground">/</span>
                                    <a href="{{ $breadcrumb['url'] }}" class="text-sm font-medium {{ $loop->last ? 'text-foreground' : 'text-muted-foreground hover:text-foreground' }}">
                                        {{ $breadcrumb['name'] }}
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    @endif
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="flex items-center gap-4">
        <!-- Theme toggle -->
        <button id="theme-toggle" class="flex items-center justify-center h-8 w-8 rounded-full border border-input bg-background hover:bg-accent hover:text-accent-foreground">
            <!-- Sun icon (shown in dark mode) -->
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sun">
                <circle cx="12" cy="12" r="4"></circle>
                <path d="M12 2v2"></path>
                <path d="M12 20v2"></path>
                <path d="m4.93 4.93 1.41 1.41"></path>
                <path d="m17.66 17.66 1.41 1.41"></path>
                <path d="M2 12h2"></path>
                <path d="M20 12h2"></path>
                <path d="m6.34 17.66-1.41 1.41"></path>
                <path d="m19.07 4.93-1.41 1.41"></path>
            </svg>
            
            <!-- Moon icon (shown in light mode) -->
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="moon">
                <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
            </svg>
            
            <span class="sr-only">Toggle theme</span>
        </button>
        
        <!-- Notifications -->
        <div class="relative">
            <button class="flex items-center justify-center h-8 w-8 rounded-full hover:bg-accent hover:text-accent-foreground relative">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell">
                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
                    <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
                </svg>
                <span class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] text-primary-foreground">
                    5
                </span>
            </button>
        </div>
        
        <!-- User dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2 rounded-full hover:bg-accent hover:text-accent-foreground">
                <div class="relative h-8 w-8 rounded-full bg-muted">
                    <img src="{{ asset('images/placeholder.svg') }}" alt="@admin" class="h-full w-full rounded-full object-cover">
                </div>
            </button>
            
            <!-- Dropdown menu -->
            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-56 rounded-md border bg-popover text-popover-foreground shadow-md" style="display: none;">
                <div class="p-2">
                    <div class="px-2 py-1.5">
                        <div class="flex flex-col space-y-1">
                            <p class="text-sm font-medium leading-none">Admin User</p>
                            <p class="text-xs leading-none text-muted-foreground">admin@example.com</p>
                        </div>
                    </div>
                    <div class="h-px my-1 bg-muted"></div>
                    <div class="space-y-1">
                        <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-2 mr-2">
                                <circle cx="12" cy="8" r="5"></circle>
                                <path d="M20 21a8 8 0 1 0-16 0"></path>
                            </svg>
                            <span>Hồ sơ cá nhân</span>
                        </a>
                        <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings mr-2">
                                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <span>Cài đặt</span>
                        </a>
                    </div>
                    <div class="h-px my-1 bg-muted"></div>
                    <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm text-red-600 hover:bg-accent hover:text-red-700">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out mr-2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" x2="9" y1="12" y2="12"></line>
                        </svg>
                        <span>Đăng xuất</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="public/js/modal.js"></script>