<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FastFood')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        orange: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        },
                    },
                },
            },
        }
    </script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />
    <!-- Notification Styles -->
    <link rel="stylesheet" href="{{ asset('css/customer/app.css') }}">

    <!-- Custom CSS -->
    @yield('styles')
</head>

<body class="min-h-screen">
    <!-- Notification Container -->
    <div id="notificationContainer" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-md px-4">
        @if (session('success'))
            <div class="notification-alert bg-white border-l-4 border-green-500 rounded-lg overflow-hidden mb-4 transition-all duration-300"
                data-type="success" id="successNotification">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div
                                class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center notification-icon">
                                <i class="fas fa-check text-green-500 text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-bold text-gray-900">Thành công!</h3>
                                <button
                                    class="close-notification text-gray-400 hover:text-gray-600 transition-colors ml-2"
                                    data-target="successNotification">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                            <p class="mt-1 text-sm text-gray-600">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
                <!-- Progress bar -->
                <div class="h-1 bg-gray-100">
                    <div class="h-full bg-green-500 progress-bar" style="animation: progressBar 5s linear forwards;">
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="notification-alert bg-white border-l-4 border-red-500 rounded-lg overflow-hidden mb-4 transition-all duration-300"
                data-type="error" id="errorNotification">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div
                                class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center notification-icon">
                                <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-bold text-gray-900">Có lỗi xảy ra!</h3>
                                <button
                                    class="close-notification text-gray-400 hover:text-gray-600 transition-colors ml-2"
                                    data-target="errorNotification">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                            <p class="mt-1 text-sm text-gray-600">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
                <!-- Progress bar -->
                <div class="h-1 bg-gray-100">
                    <div class="h-full bg-red-500 progress-bar" style="animation: progressBar 5s linear forwards;">
                    </div>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="notification-alert bg-white border-l-4 border-orange-500 rounded-lg overflow-hidden mb-4 transition-all duration-300"
                data-type="warning" id="warningNotification">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div
                                class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center notification-icon">
                                <i class="fas fa-exclamation text-orange-500 text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-bold text-gray-900">Cảnh báo!</h3>
                                <button
                                    class="close-notification text-gray-400 hover:text-gray-600 transition-colors ml-2"
                                    data-target="warningNotification">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                            <p class="mt-1 text-sm text-gray-600">{{ session('warning') }}</p>
                        </div>
                    </div>
                </div>
                <!-- Progress bar -->
                <div class="h-1 bg-gray-100">
                    <div class="h-full bg-orange-500 progress-bar" style="animation: progressBar 5s linear forwards;">
                    </div>
                </div>
            </div>
        @endif

        @if (session('info'))
            <div class="notification-alert bg-white border-l-4 border-blue-500 rounded-lg overflow-hidden mb-4 transition-all duration-300"
                data-type="info" id="infoNotification">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div
                                class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center notification-icon">
                                <i class="fas fa-info text-blue-500 text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-bold text-gray-900">Thông tin!</h3>
                                <button
                                    class="close-notification text-gray-400 hover:text-gray-600 transition-colors ml-2"
                                    data-target="infoNotification">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                            <p class="mt-1 text-sm text-gray-600">{{ session('info') }}</p>
                        </div>
                    </div>
                </div>
                <!-- Progress bar -->
                <div class="h-1 bg-gray-100">
                    <div class="h-full bg-blue-500 progress-bar" style="animation: progressBar 5s linear forwards;">
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Navbar -->
    @include('partials.customer.header')
    <!-- Main Content -->
    <main>
        @yield('content')

        <!-- Chat Widget -->
        @include('partials.customer.chat-widget')
    </main>

    <!-- Footer -->
    @include('partials.customer.footer')

    <!-- JavaScript -->
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script>
        // Function to show notifications programmatically
        function showToast(message, type = 'info', duration = 5000) {
            const container = document.getElementById('notificationContainer');
            const notificationId = 'notification_' + Date.now();

            const colors = {
                success: {
                    bg: 'green',
                    icon: 'check'
                },
                error: {
                    bg: 'red',
                    icon: 'exclamation-triangle'
                },
                warning: {
                    bg: 'orange',
                    icon: 'exclamation'
                },
                info: {
                    bg: 'blue',
                    icon: 'info'
                }
            };

            const color = colors[type] || colors.info;

            const notificationHTML = `
                <div class="notification-alert bg-white border-l-4 border-${color.bg}-500 rounded-lg overflow-hidden mb-4 transition-all duration-300" 
                     data-type="${type}" 
                     id="${notificationId}">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-${color.bg}-100 rounded-full flex items-center justify-center notification-icon">
                                    <i class="fas fa-${color.icon} text-${color.bg}-500 text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-bold text-gray-900">${type.charAt(0).toUpperCase() + type.slice(1)}</h3>
                                    <button class="close-notification text-gray-400 hover:text-gray-600 transition-colors ml-2" 
                                            data-target="${notificationId}">
                                        <i class="fas fa-times text-sm"></i>
                                    </button>
                                </div>
                                <p class="mt-1 text-sm text-gray-600">${message}</p>
                            </div>
                        </div>
                    </div>
                    <div class="h-1 bg-gray-100">
                        <div class="h-full bg-${color.bg}-500 progress-bar" style="animation: progressBar ${duration}ms linear forwards;"></div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', notificationHTML);

            const notification = document.getElementById(notificationId);

            // Auto dismiss
            setTimeout(() => {
                dismissNotification(notification);
            }, duration);

            // Add event listeners
            notification.querySelector('.close-notification').addEventListener('click', function() {
                dismissNotification(notification);
            });

            notification.addEventListener('click', function(e) {
                if (!e.target.closest('.close-notification')) {
                    dismissNotification(this);
                }
            });

            notification.addEventListener('mouseenter', function() {
                const progressBar = this.querySelector('.progress-bar');
                if (progressBar) {
                    progressBar.style.animationPlayState = 'paused';
                }
            });

            notification.addEventListener('mouseleave', function() {
                const progressBar = this.querySelector('.progress-bar');
                if (progressBar) {
                    progressBar.style.animationPlayState = 'running';
                }
            });

            function dismissNotification(notif) {
                if (notif) {
                    notif.classList.add('hide');
                    setTimeout(() => notif.remove(), 500);
                }
            }
        }

        // Make showToast globally available
        window.showToast = showToast;
    </script>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute(
            'content');
    </script>

    <!-- Firebase SDK -->
    <script type="module">
        // Import Firebase modules
        import {
            initializeApp
        } from 'https://www.gstatic.com/firebasejs/9.23.0/firebase-app.js';
        import {
            getAuth,
            GoogleAuthProvider,
            signInWithPopup,
            signOut,
            onAuthStateChanged
        } from 'https://www.gstatic.com/firebasejs/9.23.0/firebase-auth.js';

        // Firebase configuration
        const firebaseConfig = {
            apiKey: "{{ env('FIREBASE_API_KEY') }}",
            authDomain: "{{ env('FIREBASE_AUTH_DOMAIN') }}",
            projectId: "{{ env('FIREBASE_PROJECT_ID') }}",
            storageBucket: "{{ env('FIREBASE_STORAGE_BUCKET') }}",
            messagingSenderId: "{{ env('FIREBASE_MESSAGING_SENDER_ID') }}",
            appId: "{{ env('FIREBASE_APP_ID') }}"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const provider = new GoogleAuthProvider();

        // Configure Google provider
        provider.addScope('profile');
        provider.addScope('email');

        // Make Firebase auth available globally
        window.firebaseAuth = auth;
        window.googleProvider = provider;
        window.signInWithPopup = signInWithPopup;
        window.firebaseSignOut = signOut;
        window.onAuthStateChanged = onAuthStateChanged;
        window.GoogleAuthProvider = GoogleAuthProvider;

        // Listen for auth state changes
        onAuthStateChanged(auth, (user) => {
            if (user) {
                // User is signed in with Firebase
                console.log('Firebase user signed in:', user);
                window.firebaseUser = user;
            } else {
                // User is signed out
                console.log('Firebase user signed out');
                window.firebaseUser = null;
            }
        });
    </script>

    <!-- Scripts -->
    <script>
        // Global function to update the cart counter
        window.updateCartCount = function(count) {
            // Save the cart count in localStorage for consistency between pages
            localStorage.setItem('cart_count', count);

            // Update all cart counter elements on the page
            const counters = document.querySelectorAll('#cart-counter');
            counters.forEach(counter => {
                // Update the counter with animation
                counter.textContent = count;

                // Add animation class
                counter.classList.add('animate-bounce', 'bg-green-500');
                setTimeout(() => {
                    counter.classList.remove('animate-bounce', 'bg-green-500');
                    counter.classList.add('bg-orange-500');
                }, 1000);
            });
        };

        // Initialize Pusher on every page to listen for cart updates
        document.addEventListener('DOMContentLoaded', function() {
            // Check if we should restore cart count from localStorage
            const savedCount = localStorage.getItem('cart_count');
            if (savedCount) {
                const sessionCount = {{ session('cart_count', 0) }};
                // Only use localStorage if it has a newer value than the session
                if (parseInt(savedCount) > sessionCount) {
                    window.updateCartCount(savedCount);
                }
            }

            // Set up Pusher if the script is loaded
            if (typeof Pusher !== 'undefined') {
                const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                    cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                    encrypted: true,
                    enabledTransports: ['ws', 'wss'],
                    debug: true
                });

                // Subscribe to cart channel
                const cartChannel = pusher.subscribe('user-cart-channel.{{ auth()->id() }}');

                // Listen for cart updates
                cartChannel.bind('cart-updated', function(data) {
                    window.updateCartCount(data.count);
                });
            }
        });
    </script>

    <!-- Dropdown Menu JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownButton = document.getElementById('user-dropdown-button');
            const dropdownMenu = document.getElementById('user-dropdown-menu');

            if (dropdownButton && dropdownMenu) {
                // Toggle dropdown khi click button
                dropdownButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('hidden');
                });

                // Đóng dropdown khi click bên ngoài
                document.addEventListener('click', function(e) {
                    if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                        dropdownMenu.classList.add('hidden');
                    }
                });

                // Đóng dropdown khi nhấn ESC
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        dropdownMenu.classList.add('hidden');
                    }
                });
            }
        });
    </script>

    @yield('scripts')
    @stack('scripts')

    <script>
        // Global function to update the wishlist counter
        window.updateWishlistCount = function(count) {
            // Save the wishlist count in localStorage for consistency between pages
            localStorage.setItem('wishlist_count', count);

            // Update all wishlist counter elements on the page
            const counters = document.querySelectorAll('#wishlist-container span');
            counters.forEach(counter => {
                // Update the counter with animation
                counter.textContent = count;

                // Add animation class
                counter.classList.add('animate-bounce', 'bg-green-500');
                setTimeout(() => {
                    counter.classList.remove('animate-bounce', 'bg-green-500');
                    counter.classList.add('bg-red-500');
                }, 1000);
            });
        };

        // Initialize Pusher on every page to listen for wishlist updates
        document.addEventListener('DOMContentLoaded', function() {
            // Check if we should restore wishlist count from localStorage
            const savedCount = localStorage.getItem('wishlist_count');
            if (savedCount) {
                const sessionCount = {{ auth()->check() ? auth()->user()->wishlist->count() : 0 }};
                // Only use localStorage if it has a newer value than the session
                if (parseInt(savedCount) > sessionCount) {
                    window.updateWishlistCount(savedCount);
                }
            }

            // Set up Pusher if the script is loaded and user is authenticated
            if (typeof Pusher !== 'undefined' && {{ auth()->check() ? 'true' : 'false' }}) {
                console.log('Initializing Pusher for wishlist updates...');

                const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                    cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                    encrypted: true,
                    enabledTransports: ['ws', 'wss'],
                    debug: true
                });

                // Subscribe to wishlist channel
                const channelName = 'user-wishlist-channel.{{ auth()->id() }}';
                console.log('Subscribing to channel:', channelName);

                const wishlistChannel = pusher.subscribe(channelName);

                // Log connection status
                pusher.connection.bind('connected', () => {
                    console.log('Pusher connected successfully');
                });

                pusher.connection.bind('error', (err) => {
                    console.error('Pusher connection error:', err);
                });

                // Listen for wishlist updates
                wishlistChannel.bind('favorite-updated', function(data) {
                    console.log('Received favorite-updated event:', data);

                    window.updateWishlistCount(data.count);

                    // Update favorite button state if on product page
                    const favoriteBtn = document.querySelector(
                        `.favorite-btn[data-product-id="${data.product_id}"]`);
                    if (favoriteBtn) {
                        console.log('Updating favorite button state for product:', data.product_id);
                        const icon = favoriteBtn.querySelector('i');
                        if (data.is_favorite) {
                            icon.classList.remove('far');
                            icon.classList.add('fas', 'text-red-500');
                        } else {
                            icon.classList.remove('fas', 'text-red-500');
                            icon.classList.add('far');
                        }
                    }
                });

                // Log subscription status
                wishlistChannel.bind('subscription_succeeded', () => {
                    console.log('Successfully subscribed to wishlist channel');
                });

                wishlistChannel.bind('subscription_error', (error) => {
                    console.error('Error subscribing to wishlist channel:', error);
                });
            } else {
                console.log('Pusher not initialized because:', {
                    pusherLoaded: typeof Pusher !== 'undefined',
                    userAuthenticated: {{ auth()->check() ? 'true' : 'false' }}
                });
            }
        });
    </script>
</body>

</html>
