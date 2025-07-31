<header class="sticky top-0 z-10 flex shrink-0 items-center justify-between border-b bg-background px-4 shadow-sm"
    style="height:59px;min-height:59px;max-height:59px;">
    <div class="flex items-center gap-2">
        <!-- Mobile menu button -->
        <button id="mobile-menu-btn"
            class="lg:hidden flex items-center justify-center h-9 w-9 rounded-md hover:bg-accent hover:text-accent-foreground">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-menu">
                <line x1="4" x2="20" y1="12" y2="12"></line>
                <line x1="4" x2="20" y1="6" y2="6"></line>
                <line x1="4" x2="20" y1="18" y2="18"></line>
            </svg>
            <span class="sr-only">Toggle Menu</span>
        </button>

        <!-- Toggle sidebar button -->
        <button id="toggle-sidebar"
            class="hidden lg:flex items-center justify-center h-7 w-7 rounded-md hover:bg-accent hover:text-accent-foreground">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-panel-left">
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
                                @yield('title', 'Dashboard')
                            </a>
                        </div>
                    </li>
                    @if (isset($breadcrumbs))
                        @foreach ($breadcrumbs as $breadcrumb)
                            <li>
                                <div class="flex items-center">
                                    <span class="mx-2 text-muted-foreground">/</span>
                                    <a href="{{ $breadcrumb['url'] }}"
                                        class="text-sm font-medium {{ $loop->last ? 'text-foreground' : 'text-muted-foreground hover:text-foreground' }}">
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
        <button id="theme-toggle"
            class="flex items-center justify-center h-9 w-9 rounded-full border border-input bg-background hover:bg-accent hover:text-accent-foreground">
            <!-- Sun icon (shown in dark mode) -->
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sun">
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
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="moon">
                <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
            </svg>

            <span class="sr-only">Toggle theme</span>
        </button>

        <!-- Notifications -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                class="flex items-center justify-center h-8 w-8 rounded-full hover:bg-accent hover:text-accent-foreground relative mt-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-bell animate-bell admin-header-bell-icon">
                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
                    <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
                </svg>
                @if (isset($adminUnreadCount) && $adminUnreadCount > 0)
                    <span
                        class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] text-primary-foreground animate-badge notification-unread-count">
                        <span
                            class="absolute inline-flex h-full w-full rounded-full bg-primary opacity-75 animate-ping"></span>
                        <span class="relative">{{ $adminUnreadCount > 99 ? '99+' : $adminUnreadCount }}</span>
                    </span>
                @endif
            </button>
            <!-- Dropdown menu -->
            <div x-show="open" @click.away="open = false"
                class="absolute right-0 mt-2 w-80 rounded-md border bg-popover text-popover-foreground shadow-md overflow-hidden z-50"
                style="display: none;">
                <div class="p-2 max-h-[calc(100vh-100px)] overflow-y-auto custom-scrollbar flex flex-col"
                    style="height:400px;">
                    <div class="px-2 py-1.5 mb-1">
                        <h3 class="font-semibold text-sm">Thông báo</h3>
                        <p class="text-xs text-muted-foreground">Bạn có <span
                                class="notification-unread-count">{{ ($adminUnreadCount ?? 0) > 99 ? '99+' : $adminUnreadCount ?? 0 }}</span>
                            thông báo chưa
                            đọc</p>
                    </div>
                    <div class="h-px my-1 bg-muted"></div>
                    <!-- Notification items -->
                    <div class="space-y-1 flex-1 overflow-y-auto" id="admin-notification-list">
                        @include('partials.admin._notification_items', [
                            'adminNotifications' => $adminNotifications,
                        ])
                    </div>
                    <div class="h-px my-1 bg-muted"></div>
                    <a href="{{ route('admin.notifications.index') }}"
                        class="block px-2 py-1.5 text-sm text-center text-muted-foreground hover:text-foreground mt-2"
                        style="display: block !important;">
                        Xem tất cả thông báo
                    </a>
                </div>
            </div>
        </div>

        <!-- User dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                class="flex items-center gap-2 rounded-full hover:bg-accent hover:text-accent-foreground mr-8">
                <div class="relative h-9 w-9 rounded-full bg-muted">
                    @if (Auth::user()->avatar)
                        <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="{{ Auth::user()->full_name }}"
                            class="h-full w-full rounded-full object-cover">
                    @else
                        @php
                            $colors = ['bg-blue-500', 'bg-green-500', 'bg-red-500', 'bg-yellow-500', 'bg-purple-500', 'bg-indigo-500', 'bg-teal-500', 'bg-orange-500', 'bg-cyan-500'];
                            $colorIndex = ord(strtoupper(substr(Auth::user()->full_name, 0, 1))) % count($colors);
                            $selectedColor = $colors[$colorIndex];
                        @endphp
                        <div class="h-full w-full rounded-full {{ $selectedColor }} flex items-center justify-center text-white font-medium text-sm">
                            {{ strtoupper(substr(Auth::user()->full_name, 0, 1)) }}
                        </div>
                    @endif
                </div>
            </button>

            <!-- Dropdown menu -->
            <div x-show="open" @click.away="open = false"
                class="absolute right-0 mt-2 w-56 rounded-md border bg-popover text-popover-foreground shadow-md"
                style="display: none;">
                <div class="p-2">
                    <div class="px-2 py-1.5">
                        <div class="flex flex-col space-y-1">
                            <p class="text-sm font-medium leading-none">{{ Auth::user()->full_name }}</p>
                            <p class="text-xs leading-none text-muted-foreground">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <div class="h-px my-1 bg-muted"></div>
                    <div class="space-y-1">
                        <a href="#"
                            class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-2 mr-2">
                                <circle cx="12" cy="8" r="5"></circle>
                                <path d="M20 21a8 8 0 1 0-16 0"></path>
                            </svg>
                            <span>Hồ sơ cá nhân</span>
                        </a>
                        <a href="#"
                            class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings mr-2">
                                <path
                                    d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z">
                                </path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <span>Cài đặt</span>
                        </a>
                    </div>
                    <div class="h-px my-1 bg-muted"></div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex w-full items-center rounded-md px-2 py-1.5 text-sm text-red-600 hover:bg-accent hover:text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out mr-2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" x2="9" y1="12" y2="12"></line>
                            </svg>
                            <span>Đăng xuất</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="{{ asset('js/modal.js') }}"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    window.currentAdminId = "{{ auth('admin')->id() }}";
    document.addEventListener('DOMContentLoaded', function() {
        if (window.PUSHER_APP_KEY && window.PUSHER_APP_CLUSTER) {
            const pusher = new Pusher(window.PUSHER_APP_KEY, {
                cluster: window.PUSHER_APP_CLUSTER,
                encrypted: true
            });
            // Lắng nghe kênh tổng admin
            const channel = pusher.subscribe('admin.conversations');
            channel.bind('new-message', function(data) {

                fetchNotifications();
            });
        }
    });

    function fetchNotifications() {
        fetch("{{ route('admin.notifications.index') }}?ajax=1", {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {

                document.querySelectorAll('.notification-unread-count').forEach(el => {
                    el.textContent = data.unreadCount > 99 ? '99+' : data.unreadCount;
                });
                // Cập nhật danh sách notification trong modal (chỉ update từng item)
                let container = document.getElementById('admin-notification-list');
                if (container && data.html) {
                    let tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data.html;
                    let newNotis = tempDiv.querySelectorAll('[id^="notification-item-"]');
                    let newIds = new Set();
                    newNotis.forEach(newNoti => {
                        let id = newNoti.id;
                        newIds.add(id);
                        let oldNoti = document.getElementById(id);
                        if (oldNoti) {
                            oldNoti.outerHTML = newNoti.outerHTML;
                        } else {
                            container.prepend(newNoti);
                        }
                    });
                    // Xóa notification cũ không còn trong danh sách mới
                    container.querySelectorAll('[id^="notification-item-"]').forEach(oldNoti => {
                        if (!newIds.has(oldNoti.id)) {
                            oldNoti.remove();
                        }
                    });
                }
                // Cập nhật badge
                document.querySelectorAll('.notification-unread-count').forEach(el => {
                    el.textContent = data.unreadCount > 99 ? '99+' : data.unreadCount;
                });
                // Cập nhật dòng text trong dropdown (nếu có)
                const dropdownText = document.querySelector(
                    'p.text-xs.text-muted-foreground .notification-unread-count');
                if (dropdownText) {
                    dropdownText.textContent = data.unreadCount > 99 ? '99+' : data.unreadCount;
                }
            });
    }
</script>
<style>
    .custom-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.3) transparent;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 20px;
        border: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: rgba(0, 0, 0, 0.3);
    }

    /* For dark mode */
    .dark .custom-scrollbar {
        scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
    }

    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: rgba(255, 255, 255, 0.2);
    }

    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: rgba(255, 255, 255, 0.3);
    }



    @keyframes badge-pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    .animate-badge {
        animation: badge-pulse 2s ease infinite;
    }

    .animate-ping {
        animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
    }

    @keyframes ping {

        75%,
        100% {
            transform: scale(1.5);
            opacity: 0;
        }
    }

    /* Admin header bell animation styles */
    .admin-header-bell-shake {
        animation: adminHeaderBellShake 0.6s ease-in-out 2, adminHeaderBellGlow 0.6s ease-in-out 2;
        transform-origin: top center;
    }

    @keyframes adminHeaderBellShake {

        0%,
        100% {
            transform: rotate(0deg) scale(1);
        }

        10%,
        30%,
        50%,
        70%,
        90% {
            transform: rotate(-8deg) scale(1.1);
        }

        20%,
        40%,
        60%,
        80% {
            transform: rotate(8deg) scale(1.1);
        }
    }

    @keyframes adminHeaderBellGlow {

        0%,
        100% {
            filter: drop-shadow(0 0 0px currentColor);
        }

        50% {
            filter: drop-shadow(0 0 6px currentColor) drop-shadow(0 0 10px rgba(59, 130, 246, 0.2));
        }
    }

    .admin-header-bell-icon {
        transition: all 0.3s ease;
    }

    .admin-header-bell-icon:hover {
        transform: scale(1.1);
    }

    .admin-notification-count-update {
        animation: adminCountPulse 0.6s ease-in-out;
    }

    @keyframes adminCountPulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.3);
            background-color: #2563eb;
        }

        100% {
            transform: scale(1);
        }
    }

    /* Notification layout fixes */
    .notification-item {
        transition: all 0.2s ease;
    }

    .notification-item:hover {
        background-color: hsl(var(--accent));
    }

    .notification-text {
        word-break: break-word;
        overflow-wrap: break-word;
    }

    .truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
<script>
    function markNotificationAsRead(id, redirectUrl) {
        fetch("{{ url('admin/notifications') }}/" + id + "/read", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            }
        }).then(res => res.json()).then(data => {
            if (redirectUrl) {
                window.location.href = redirectUrl;
            } else {
                // Cập nhật UI thay vì reload trang
                fetchAdminNotifications();
            }
        });
    }

    function markAdminNotificationAsRead(id, redirectUrl = null) {
        // Nếu là notification từ realtime (có prefix 'new-order-')
        if (id.startsWith('new-order-')) {
            // Xóa notification khỏi UI
            const notificationElement = document.querySelector(`[data-notification-id="${id}"]`);
            if (notificationElement) {
                notificationElement.remove();

                // Cập nhật số lượng notification
                updateNotificationCount(-1);

                // Kiểm tra nếu không còn notification nào thì hiển thị empty state
                const notificationList = document.getElementById('admin-notification-list');
                if (notificationList && notificationList.children.length === 0) {
                    notificationList.innerHTML =
                        '<div class="text-center text-xs text-muted-foreground py-4">Không có thông báo nào</div>';
                }
            }

            // Chuyển hướng đến trang orders nếu có redirect URL
            if (redirectUrl) {
                window.location.href = redirectUrl;
            }
            return;
        }

        // Nếu là notification từ database, gọi API
        fetch("{{ url('admin/notifications') }}/" + id + "/read", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json",
                "Content-Type": "application/json"
            }
        }).then(res => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        }).then(data => {


            // Chỉ chuyển hướng nếu có redirect URL
            if (redirectUrl) {
                window.location.href = redirectUrl;
            } else {
                // Thay vì reload, chỉ cập nhật UI
                const notificationElement = document.querySelector(
                    `[onclick*="markAdminNotificationAsRead('${id}")]`);
                if (notificationElement) {
                    // Thêm class để đánh dấu đã đọc
                    notificationElement.classList.remove('bg-primary/10', 'text-primary', 'font-semibold');
                    notificationElement.classList.add('bg-transparent');

                    // Cập nhật số lượng notification
                    updateNotificationCount(-1);
                }
            }
        }).catch(error => {

            // Nếu có lỗi và có redirect URL, vẫn chuyển hướng
            if (redirectUrl) {
                window.location.href = redirectUrl;
            }
        });
    }

    function updateNotificationCount(increment = 0) {
        // Update the notification count badge
        const countElements = document.querySelectorAll('.notification-unread-count');
        countElements.forEach(element => {
            const currentCount = parseInt(element.textContent) || 0;
            const newCount = Math.max(0, currentCount + increment);
            element.textContent = newCount > 99 ? '99+' : newCount;

            // Show/hide badge based on count
            const badge = element.closest('.absolute.-right-1.-top-1');
            if (badge) {
                if (newCount > 0) {
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        });
    }

    // Real-time notification handling for admin header
    function fetchAdminNotifications() {
        fetch("{{ route('admin.notifications.index') }}?ajax=1", {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                const oldCount = parseInt(document.querySelector('.notification-unread-count')?.textContent || '0');
                const newCount = data.unreadCount || 0;

                document.querySelectorAll('.notification-unread-count').forEach(el => {
                    el.textContent = newCount > 99 ? '99+' : newCount;

                    // Trigger count update animation if count increased
                    if (newCount > oldCount) {
                        el.classList.add('admin-notification-count-update');
                        setTimeout(() => {
                            el.classList.remove('admin-notification-count-update');
                        }, 600);
                    }
                });

                // Trigger bell shake animation if new notifications
                if (newCount > oldCount) {
                    const bellIcon = document.querySelector('.admin-header-bell-icon');
                    if (bellIcon) {
                        bellIcon.classList.remove('admin-header-bell-shake');
                        bellIcon.offsetHeight; // Force reflow
                        bellIcon.classList.add('admin-header-bell-shake');
                        setTimeout(() => {
                            bellIcon.classList.remove('admin-header-bell-shake');
                        }, 1200);
                    }
                }

                // Update notification list if provided
                let container = document.getElementById('admin-notification-list');
                if (container && data.html) {
                    let tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data.html;
                    let newNotis = tempDiv.querySelectorAll('[id^="notification-item-"]');
                    let newIds = new Set();
                    newNotis.forEach(newNoti => {
                        let id = newNoti.id;
                        newIds.add(id);
                        let oldNoti = document.getElementById(id);
                        if (oldNoti) {
                            oldNoti.outerHTML = newNoti.outerHTML;
                        } else {
                            container.prepend(newNoti);
                        }
                    });
                    // Remove old notifications not in new list
                    container.querySelectorAll('[id^="notification-item-"]').forEach(oldNoti => {
                        if (!newIds.has(oldNoti.id)) {
                            oldNoti.remove();
                        }
                    });
                }
            })
            .catch(error => console.error('Error fetching admin notifications:', error));
    }

    // Initialize real-time notifications for admin header
    if (typeof Pusher !== 'undefined') {
        try {
            const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                encrypted: true,
                authEndpoint: '/admin/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content') || ''
                    }
                }
            });

            // Connection status logging
            pusher.connection.bind('connected', function() {
                // Connected successfully
            });

            pusher.connection.bind('error', function(err) {
                console.error('❌ Admin Header: Pusher connection error:', err);
            });

            // Subscribe to admin's private notification channel
            const channel = pusher.subscribe('private-App.Models.User.{{ Auth::id() }}');

            channel.bind('pusher:subscription_succeeded', function() {
                // Successfully subscribed
            });

            channel.bind('pusher:subscription_error', function(error) {
                console.error('❌ Admin Header: Subscription error:', error);
            });

            channel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', function(data) {
                // Check if it's a chat message notification
                if (data.type === 'App\\Notifications\\NewChatMessageNotification') {
                    // Immediately increment count for instant feedback
                    const countElements = document.querySelectorAll('.notification-unread-count');
                    countElements.forEach(el => {
                        const currentCount = parseInt(el.textContent) || 0;
                        const newCount = currentCount + 1;
                        el.textContent = newCount > 99 ? '99+' : newCount;

                        // Trigger count update animation
                        el.classList.add('admin-notification-count-update');
                        setTimeout(() => {
                            el.classList.remove('admin-notification-count-update');
                        }, 600);
                    });

                    // Trigger bell shake animation
                    const bellIcon = document.querySelector('.admin-header-bell-icon');
                    if (bellIcon) {
                        bellIcon.classList.remove('admin-header-bell-shake');
                        bellIcon.offsetHeight; // Force reflow
                        bellIcon.classList.add('admin-header-bell-shake');
                        setTimeout(() => {
                            bellIcon.classList.remove('admin-header-bell-shake');
                        }, 1200);
                    }

                    // Fetch updated notifications from server for accuracy
                    setTimeout(() => {
                        fetchAdminNotifications();
                    }, 500);
                }
            });
        } catch (error) {
            console.error('❌ Admin Header: Error initializing Pusher:', error);
        }
    }
</script>
