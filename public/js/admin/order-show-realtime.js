if (window.adminOrderShowRealtimeInitialized) {
    // Đã khởi tạo, không làm gì nữa
    console.log('⚠️ AdminOrderShowRealtime already initialized, skipping...');
} else {
    window.adminOrderShowRealtimeInitialized = true;
    console.log('🚀 Initializing AdminOrderShowRealtime for the first time');

    // Đảm bảo chỉ khai báo class AdminOrderShowRealtime nếu chưa tồn tại
    if (typeof window.AdminOrderShowRealtime === 'undefined') {
        console.log('📝 Defining AdminOrderShowRealtime class');
        
        class AdminOrderShowRealtime {
            constructor(orderId) {
                console.log('🏗️ Creating AdminOrderShowRealtime instance for order:', orderId);
                
                this.orderId = orderId;
                this.pusherKey = window.pusherKey;
                this.pusherCluster = window.pusherCluster;
                this.pusher = null;
                this.channel = null;
                this.retryCount = 0;
                this.maxRetries = 3;
                
                console.log('🔑 Using Pusher Key:', this.pusherKey);
                console.log('🌐 Using Pusher Cluster:', this.pusherCluster);
                
                this.init();
                
                // Check initial driver status immediately
                this.checkInitialDriverStatus();
            }

            async init() {
                console.log('🔄 Initializing AdminOrderShowRealtime...');
                
                // Validate Pusher configuration
                if (!this.pusherKey || !this.pusherCluster) {
                    console.error('❌ Pusher configuration missing:', {
                        key: this.pusherKey,
                        cluster: this.pusherCluster
                    });
                    this.showNotification('Lỗi cấu hình', 'Thiếu cấu hình Pusher', 'error');
                    return;
                }
                
                // Check if Pusher library is loaded
                if (typeof Pusher === 'undefined') {
                    console.error('❌ Pusher library not loaded');
                    this.showNotification('Lỗi thư viện', 'Thư viện Pusher chưa được tải', 'error');
                    return;
                }
                
                // Initialize Pusher
                this.initializePusher();
            }

            showNotification(title, message, type = 'success') {
                // Hiển thị toast notification
                if (typeof showToast === 'function') {
                    showToast(message, type);
                } else {
                    // Fallback notification
                    const toast = document.getElementById('toast-message');
                    if (toast) {
                        toast.textContent = message;
                        toast.classList.remove('bg-green-600', 'bg-red-600', 'bg-blue-600', 'hidden');
                        if (type === 'success') {
                            toast.classList.add('bg-green-600');
                        } else if (type === 'error') {
                            toast.classList.add('bg-red-600');
                        } else {
                            toast.classList.add('bg-blue-600');
                        }
                        toast.classList.remove('hidden');

                        setTimeout(() => {
                            toast.classList.add('hidden');
                        }, 3000);
                    }
                }
                
                // Hiển thị browser notification nếu có quyền
                if ('Notification' in window && Notification.permission === 'granted') {
                    new Notification(title, {
                        body: message,
                        icon: '/favicon.ico',
                        tag: 'order-status-notification'
                    });
                }
            }

            showRealtimeNotification(newStatus, oldStatus, statusText) {
                console.log('🔔 Showing real-time notification for status change:', oldStatus, '->', newStatus);
                
                // Create notification container if it doesn't exist
                this.createNotificationContainer();
                
                // Get status icon and color
                const statusInfo = this.getStatusNotificationInfo(newStatus);
                
                // Create notification element
                const notification = document.createElement('div');
                notification.className = `realtime-notification fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-lg border-l-4 ${statusInfo.borderColor} transform translate-x-full transition-transform duration-300 ease-in-out`;
                
                notification.innerHTML = `
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full ${statusInfo.bgColor} flex items-center justify-center">
                                    <i class="${statusInfo.icon} text-white text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <div class="text-sm font-medium text-gray-900">
                                    Cập nhật trạng thái đơn hàng
                                </div>
                                <div class="mt-1 text-sm text-gray-600">
                                    ${statusText}
                                </div>
                                <div class="mt-1 text-xs text-gray-400">
                                    ${new Date().toLocaleTimeString('vi-VN')}
                                </div>
                            </div>
                            <div class="ml-4 flex-shrink-0 flex">
                                <button class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                // Add to page
                document.body.appendChild(notification);
                
                // Animate in
                setTimeout(() => {
                    notification.classList.remove('translate-x-full');
                }, 100);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.classList.add('translate-x-full');
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.remove();
                            }
                        }, 300);
                    }
                }, 5000);
                
                // Play notification sound if available
                this.playNotificationSound();
            }

            createNotificationContainer() {
                // Add CSS for notifications if not already present
                if (!document.querySelector('#realtime-notification-styles')) {
                    const style = document.createElement('style');
                    style.id = 'realtime-notification-styles';
                    style.textContent = `
                        .realtime-notification {
                            animation: slideInRight 0.3s ease-out;
                        }
                        
                        @keyframes slideInRight {
                            from {
                                transform: translateX(100%);
                                opacity: 0;
                            }
                            to {
                                transform: translateX(0);
                                opacity: 1;
                            }
                        }
                        
                        .realtime-notification:hover {
                            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
                        }
                    `;
                    document.head.appendChild(style);
                }
            }

            getStatusNotificationInfo(status) {
                const statusMap = {
                    'awaiting_confirmation': {
                        icon: 'fas fa-clock',
                        bgColor: 'bg-yellow-500',
                        borderColor: 'border-yellow-500'
                    },
                    'confirmed': {
                        icon: 'fas fa-check',
                        bgColor: 'bg-blue-500',
                        borderColor: 'border-blue-500'
                    },
                    'order_confirmed': {
                        icon: 'fas fa-check',
                        bgColor: 'bg-blue-500',
                        borderColor: 'border-blue-500'
                    },
                    'awaiting_driver': {
                        icon: 'fas fa-search',
                        bgColor: 'bg-blue-600',
                        borderColor: 'border-blue-600'
                    },
                    'driver_confirmed': {
                        icon: 'fas fa-user-check',
                        bgColor: 'bg-indigo-500',
                        borderColor: 'border-indigo-500'
                    },
                    'waiting_driver_pick_up': {
                        icon: 'fas fa-hand-paper',
                        bgColor: 'bg-purple-500',
                        borderColor: 'border-purple-500'
                    },
                    'driver_picked_up': {
                        icon: 'fas fa-box',
                        bgColor: 'bg-purple-600',
                        borderColor: 'border-purple-600'
                    },
                    'in_transit': {
                        icon: 'fas fa-truck',
                        bgColor: 'bg-cyan-500',
                        borderColor: 'border-cyan-500'
                    },
                    'delivered': {
                        icon: 'fas fa-check-circle',
                        bgColor: 'bg-green-500',
                        borderColor: 'border-green-500'
                    },
                    'item_received': {
                        icon: 'fas fa-thumbs-up',
                        bgColor: 'bg-green-600',
                        borderColor: 'border-green-600'
                    },
                    'cancelled': {
                        icon: 'fas fa-times-circle',
                        bgColor: 'bg-red-500',
                        borderColor: 'border-red-500'
                    },
                    'refunded': {
                        icon: 'fas fa-undo',
                        bgColor: 'bg-pink-500',
                        borderColor: 'border-pink-500'
                    }
                };
                
                return statusMap[status] || {
                    icon: 'fas fa-info-circle',
                    bgColor: 'bg-gray-500',
                    borderColor: 'border-gray-500'
                };
            }

            playNotificationSound() {
                try {
                    // Create a subtle notification sound
                    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();
                    
                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);
                    
                    oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                    oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
                    
                    gainNode.gain.setValueAtTime(0, audioContext.currentTime);
                    gainNode.gain.linearRampToValueAtTime(0.1, audioContext.currentTime + 0.01);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
                    
                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.2);
                } catch (error) {
                    // Ignore audio errors
                    console.log('Could not play notification sound:', error);
                }
            }

            initializePusher() {
                try {
                    console.log('🚀 Initializing Pusher for order show page with key:', this.pusherKey, 'cluster:', this.pusherCluster);
                    
                    this.pusher = new Pusher(this.pusherKey, {
                        cluster: this.pusherCluster,
                        encrypted: true,
                        authEndpoint: '/broadcasting/auth'
                    });

                    // Connection events
                    this.pusher.connection.bind('connected', () => {
                        console.log('✅ Pusher connected successfully for order show');
                    });

                    this.pusher.connection.bind('error', (err) => {
                        console.error('❌ Pusher connection error:', err);
                        this.showNotification('Lỗi kết nối', 'Không thể kết nối Pusher', 'error');
                    });

                    this.pusher.connection.bind('disconnected', () => {
                        console.log('⚠️ Pusher disconnected');
                        this.showNotification('Mất kết nối', 'Kết nối Pusher đã bị ngắt', 'error');
                    });

                    // Subscribe to order status updates
                    this.subscribeToOrderStatusUpdates();

                } catch (error) {
                    console.error('❌ Error initializing Pusher:', error);
                    this.showNotification('Lỗi khởi tạo', 'Không thể khởi tạo Pusher', 'error');
                }
            }

            subscribeToOrderStatusUpdates() {
                // Subscribe to order status update channel
                console.log('🔔 Subscribing to order-status-updates channel for order:', this.orderId);
                this.orderStatusChannel = this.pusher.subscribe('order-status-updates');
                
                this.orderStatusChannel.bind('pusher:subscription_succeeded', () => {
                    console.log('✅ Successfully subscribed to order status updates');
                });

                this.orderStatusChannel.bind('pusher:subscription_error', (status) => {
                    console.error('❌ Failed to subscribe to order status updates:', status);
                });

                // Listen for order status updates
                this.orderStatusChannel.bind('OrderStatusUpdated', (data) => {
                    console.log('📦 OrderStatusUpdated event received:', data);
                    // Only handle updates for this specific order
                    if (data.order_id == this.orderId) {
                        this.handleOrderStatusUpdate(data);
                    }
                });

                // Also listen for any other status update events that might be broadcasted
                this.orderStatusChannel.bind('order-status-updated', (data) => {
                    console.log('📦 order-status-updated event received:', data);
                    // Only handle updates for this specific order
                    if (data.order_id == this.orderId) {
                        this.handleOrderStatusUpdate(data);
                    }
                });

                // Subscribe to admin orders channel for additional coverage
                this.subscribeToAdminChannel();
            }

            subscribeToAdminChannel() {
                // Subscribe to admin orders channel for real-time status updates
                console.log('🔔 Subscribing to admin-orders-channel for order:', this.orderId);
                this.adminChannel = this.pusher.subscribe('admin-orders-channel');
                
                this.adminChannel.bind('pusher:subscription_succeeded', () => {
                    console.log('✅ Successfully subscribed to admin-orders-channel');
                });

                this.adminChannel.bind('pusher:subscription_error', (status) => {
                    console.error('❌ Failed to subscribe to admin-orders-channel:', status);
                });

                // Listen for order status updates from admin channel
                this.adminChannel.bind('order-status-updated', (data) => {
                    console.log('📦 Admin channel - order-status-updated event received:', data);
                    // Only handle updates for this specific order
                    if (data.order_id == this.orderId) {
                        this.handleOrderStatusUpdate(data);
                    }
                });

                // Listen for OrderStatusUpdated events
                this.adminChannel.bind('OrderStatusUpdated', (data) => {
                    console.log('📦 Admin channel - OrderStatusUpdated event received:', data);
                    // Only handle updates for this specific order
                    if (data.order_id == this.orderId) {
                        this.handleOrderStatusUpdate(data);
                    }
                });
            }

            handleOrderStatusUpdate(data) {
                console.log('🔄 Handling order status update for order:', this.orderId, data);
                
                const newStatus = data.status || data.new_status;
                const statusText = data.status_text || this.getStatusText(newStatus);
                
                // Show real-time notification first
                this.showRealtimeNotification(newStatus, data.old_status, statusText);
                
                // Update the order status display
                this.updateOrderStatusDisplay(newStatus, statusText, data);
                
                // Update progress tracker
                this.updateProgressTracker(newStatus, data);
                
                // Update driver information if provided
                if (data.driver_info) {
                    this.updateDriverInfo(data.driver_info, newStatus);
                } else {
                    // Even if no driver_info, update driver status if driver exists
                    this.updateDriverStatus(newStatus);
                }
                
                // Update delivery time if provided
                if (data.actual_delivery_time || data.estimated_delivery_time) {
                    this.updateDeliveryTime(data);
                }
                
                // Show notification
                this.showNotification(
                    'Cập nhật đơn hàng', 
                    `Đơn hàng đã chuyển sang: ${statusText}`,
                    'success'
                );
            }

            updateOrderStatusDisplay(status, statusText, data) {
                // Update main status display
                const statusElement = document.getElementById('order-status-display');
                if (statusElement) {
                    statusElement.textContent = statusText;
                    
                    // Update status styling based on status
                    const statusColors = this.getStatusColors(status);
                    statusElement.className = `inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${statusColors}`;
                    
                    // Add animation effect
                    statusElement.style.animation = 'pulse 1s ease-in-out';
                    setTimeout(() => {
                        statusElement.style.animation = '';
                    }, 1000);
                }

                // Update any other status displays on the page
                const allStatusElements = document.querySelectorAll('.order-status-text');
                allStatusElements.forEach(element => {
                    element.textContent = statusText;
                });
            }

            updateProgressTracker(status, data) {
                console.log('🔄 updateProgressTracker called with status:', status);
                
                // Update progress steps
                const progressSteps = document.querySelectorAll('.progress-step');
                const progressBar = document.querySelector('.progress-bar-fill');
                
                console.log('📊 Found progress steps:', progressSteps.length);
                console.log('📊 Found progress bar:', !!progressBar);
                
                if (progressSteps.length === 0) {
                    console.warn('⚠️ No progress steps found');
                    return;
                }

                // Handle special statuses like cancelled, refunded
                if (['cancelled', 'refunded', 'payment_failed', 'order_failed'].includes(status)) {
                    console.log('❌ Handling cancelled status:', status);
                    this.showCancelledStatus(status);
                    return;
                }

                // Status mapping to step keys (matching the view logic)
                const statusMapToStep = {
                    'awaiting_confirmation': 'awaiting_confirmation',
                    'confirmed': 'confirmed',
                    'awaiting_driver': 'awaiting_driver',
                    'driver_confirmed': 'driver_assigned',
                    'waiting_driver_pick_up': 'driver_assigned',
                    'driver_picked_up': 'driver_assigned',
                    'in_transit': 'in_transit',
                    'delivered': 'delivered',
                    'item_received': 'delivered'
                };

                // Find current step based on data-step-key attribute
                const currentStepKey = statusMapToStep[status] || status;
                let currentStepIndex = -1;
                
                console.log('🔍 Current status:', status);
                console.log('🔍 Mapped to step key:', currentStepKey);
                
                // Create array of step keys in order
                const stepKeys = [];
                progressSteps.forEach((step) => {
                    const stepKey = step.getAttribute('data-step-key');
                    stepKeys.push(stepKey);
                    console.log('📋 Step found:', stepKey);
                });
                
                console.log('📋 All step keys:', stepKeys);
                
                // Find the index of current step
                currentStepIndex = stepKeys.indexOf(currentStepKey);
                
                console.log('📍 Current step index:', currentStepIndex);
                
                if (currentStepIndex === -1) {
                    console.warn('⚠️ Current step key not found in progress steps:', currentStepKey);
                    return;
                }

                // Update step classes and styles
                progressSteps.forEach((step, index) => {
                    const stepIcon = step.querySelector('.step-icon');
                    const stepText = step.querySelector('.step-text');
                    
                    // Remove all existing classes first
                    step.classList.remove('completed', 'current', 'pending');
                    
                    if (index < currentStepIndex) {
                        // Completed step
                        step.classList.add('completed');
                        if (stepIcon) {
                            stepIcon.innerHTML = '<i class="fas fa-check text-xs"></i>';
                            stepIcon.className = 'step-icon w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold z-10 transition-all duration-300 bg-green-500 text-white shadow-lg';
                        }
                        if (stepText) {
                            stepText.className = 'step-text text-sm font-medium block text-green-600';
                        }
                    } else if (index === currentStepIndex) {
                        // Current step
                        step.classList.add('current');
                        if (stepIcon) {
                            // Keep the original icon from HTML or use a default
                            const originalIcon = step.querySelector('i') ? step.querySelector('i').className : 'fas fa-clock';
                            stepIcon.innerHTML = `<i class="${originalIcon} text-xs"></i>`;
                            stepIcon.className = 'step-icon w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold z-10 transition-all duration-300 bg-orange-500 text-white shadow-lg ring-4 ring-orange-200 animate-pulse';
                        }
                        if (stepText) {
                            stepText.className = 'step-text text-sm font-medium block text-orange-600';
                        }
                    } else {
                        // Pending step
                        step.classList.add('pending');
                        if (stepIcon) {
                            stepIcon.innerHTML = index + 1;
                            stepIcon.className = 'step-icon w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold z-10 transition-all duration-300 bg-gray-200 text-gray-500';
                        }
                        if (stepText) {
                            stepText.className = 'step-text text-sm font-medium block text-gray-500';
                        }
                    }
                });

                // Update progress bar
                if (progressBar && currentStepIndex >= 0) {
                    const progressPercentage = ((currentStepIndex + 1) / progressSteps.length) * 100;
                    progressBar.style.width = `${progressPercentage}%`;
                    progressBar.style.transition = 'width 0.5s ease-in-out';
                }
            }

            showCancelledStatus(status) {
                console.log('❌ Showing cancelled status:', status);
                
                // Hide the progress tracker section
                const progressTrackerSection = document.querySelector('.progress-tracker');
                if (progressTrackerSection) {
                    // Find the parent container with bg-white class
                    const parentContainer = progressTrackerSection.closest('.bg-white');
                    if (parentContainer) {
                        parentContainer.style.display = 'none';
                    }
                }
                
                // Update the order status display
                const orderStatusDisplay = document.getElementById('order-status-display');
                if (orderStatusDisplay) {
                    let statusText = '';
                    let statusClass = '';
                    
                    switch(status) {
                        case 'cancelled':
                            statusText = 'Đã hủy';
                            statusClass = 'bg-red-100 text-red-800';
                            break;
                        case 'refunded':
                            statusText = 'Đã hoàn tiền';
                            statusClass = 'bg-pink-100 text-pink-800';
                            break;
                        case 'payment_failed':
                        case 'order_failed':
                            statusText = 'Thất bại';
                            statusClass = 'bg-gray-300 text-gray-900';
                            break;
                        default:
                            statusText = this.getStatusText(status);
                            statusClass = this.getStatusColors(status);
                    }
                    
                    orderStatusDisplay.textContent = statusText;
                    orderStatusDisplay.className = `inline-block px-4 py-2 text-sm font-medium rounded-lg ${statusClass}`;
                }
            }

            updateDriverStatus(orderStatus) {
                const driverStatusElement = document.querySelector('.driver-status');
                const driverStatusContainer = document.querySelector('.driver-status-container');
                
                if (driverStatusElement && orderStatus) {
                    const statusText = this.getStatusText(orderStatus);
                    const statusColor = this.getDriverStatusColor(orderStatus);
                    
                    // Update status text
                    driverStatusElement.textContent = statusText;
                    
                    // Update status color classes - remove old color classes first
                    driverStatusElement.className = driverStatusElement.className.replace(/text-\w+-\d+/g, '');
                    driverStatusElement.classList.add(`text-${statusColor}-600`);
                    
                    // Update border color of parent container
                    if (driverStatusContainer) {
                        driverStatusContainer.className = driverStatusContainer.className.replace(/border-\w+-\d+/g, '');
                        driverStatusContainer.classList.add(`border-${statusColor}-500`);
                    }
                    
                    console.log('✅ Updated driver status to:', statusText, 'with color:', statusColor);
                }
            }

            getDriverStatusColor(status) {
                const colorMap = {
                    'awaiting_confirmation': 'yellow',
                    'confirmed': 'blue',
                    'order_confirmed': 'blue',
                    'awaiting_driver': 'blue',
                    'driver_confirmed': 'indigo',
                    'waiting_driver_pick_up': 'purple',
                    'driver_picked_up': 'purple',
                    'in_transit': 'cyan',
                    'delivered': 'green',
                    'item_received': 'green',
                    'cancelled': 'red',
                    'refunded': 'pink',
                    'payment_failed': 'red',
                    'payment_received': 'lime',
                    'order_failed': 'gray'
                };
                return colorMap[status] || 'gray';
            }

            updateDriverInfo(driverInfo, orderStatus = null) {
                console.log('🚗 Updating driver info:', driverInfo);
                
                // Find the driver container
                const driverContainer = document.querySelector('.driver-container');
                if (!driverContainer) {
                    console.warn('Driver container not found');
                    return;
                }

                // If we have driver info, show it
                if (driverInfo && (driverInfo.full_name || driverInfo.name)) {
                    // Clear the container and create new driver info section
                    this.createDriverInfoSection(driverInfo, orderStatus);
                } else {
                    // No driver info - show waiting state
                    driverContainer.innerHTML = `
                        <div class="flex items-center space-x-3">
                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                            <div class="text-sm text-gray-600">
                                <div class="font-medium">Đang tìm tài xế...</div>
                                <div class="text-xs text-gray-500 mt-1">Vui lòng chờ trong giây lát</div>
                            </div>
                        </div>
                        <div class="mt-3 flex space-x-1">
                            <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                            <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                            <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                        </div>
                    `;
                }
            }

            createDriverInfoSection(driverInfo, orderStatus = null) {
                // Find the container where driver info should be displayed
                const driverContainer = document.querySelector('.driver-container');
                if (driverContainer) {
                    // Get current order status if not provided
                    if (!orderStatus) {
                        const statusElement = document.getElementById('order-status-display');
                        if (statusElement) {
                            const statusText = statusElement.textContent.trim();
                            // Map status text back to status code
                            const statusMap = {
                                'Đã xác nhận': 'confirmed',
                                'Chờ tài xế': 'awaiting_driver',
                                'Tài xế đã xác nhận': 'driver_confirmed',
                                'Chờ tài xế lấy hàng': 'waiting_driver_pick_up',
                                'Tài xế đã lấy hàng': 'driver_picked_up',
                                'Đang giao': 'in_transit',
                                'Đã giao': 'delivered',
                                'Khách đã nhận hàng': 'item_received'
                            };
                            orderStatus = Object.keys(statusMap).find(key => statusText.includes(key));
                            orderStatus = statusMap[orderStatus] || 'driver_confirmed';
                        } else {
                            orderStatus = 'driver_confirmed';
                        }
                    }
                    
                    // Get appropriate status text and color based on order status
                    let statusText = 'Tài xế đã được phân công';
                    let statusColor = 'green';
                    
                    switch(orderStatus) {
                        case 'confirmed':
                            statusText = 'Đã xác nhận đơn hàng';
                            statusColor = 'blue';
                            break;
                        case 'awaiting_driver':
                            statusText = 'Chờ tài xế nhận đơn';
                            statusColor = 'yellow';
                            break;
                        case 'driver_confirmed':
                            statusText = 'Tài xế đã xác nhận';
                            statusColor = 'green';
                            break;
                        case 'waiting_driver_pick_up':
                            statusText = 'Tài xế đang chờ lấy đơn';
                            statusColor = 'purple';
                            break;
                        case 'driver_picked_up':
                            statusText = 'Tài xế đã nhận đơn';
                            statusColor = 'indigo';
                            break;
                        case 'in_transit':
                            statusText = 'Đang giao hàng';
                            statusColor = 'cyan';
                            break;
                        case 'delivered':
                            statusText = 'Đã giao hàng';
                            statusColor = 'green';
                            break;
                        case 'item_received':
                            statusText = 'Khách hàng đã nhận hàng';
                            statusColor = 'emerald';
                            break;
                    }
                    
                    driverContainer.innerHTML = `
                        <div class="space-y-3 driver-info">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 driver-name">${driverInfo.full_name || driverInfo.name}</div>
                                    <div class="text-sm text-gray-600">Tài xế giao hàng</div>
                                </div>
                            </div>
                            <div class="space-y-2 text-sm">
                                ${driverInfo.phone_number ? `
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600">Số điện thoại:</span>
                                        <a href="tel:${driverInfo.phone_number}" class="font-medium text-blue-600 hover:text-blue-800 flex items-center driver-phone">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                            ${driverInfo.phone_number}
                                        </a>
                                    </div>
                                ` : ''}
                                ${driverInfo.license_plate ? `
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600">Biển số xe:</span>
                                        <span class="font-medium text-gray-900 font-mono bg-white px-2 py-1 rounded border driver-vehicle">${driverInfo.license_plate}</span>
                                    </div>
                                ` : ''}
                            </div>
                            
                            <!-- Trạng thái giao hàng -->
                            <div class="mt-3 p-2 bg-white rounded border-l-4 border-${statusColor}-500">
                                <div class="text-sm">
                                    <span class="text-gray-600">Trạng thái:</span>
                                    <span class="font-medium text-${statusColor}-600 ml-1 driver-status">
                                        ${statusText}
                                    </span>
                                </div>
                            </div>
                        </div>
                    `;
                }
            }

            updateDeliveryTime(data) {
                console.log('🕒 Updating delivery time with data:', data);
                
                const newStatus = data.status || data.new_status;
                
                // Find the delivery time container
                const estimatedDeliveryContainer = document.querySelector('.estimated-delivery-time');
                const actualDeliveryContainer = document.querySelector('.actual-delivery-time');
                
                console.log('📍 Found estimated container:', !!estimatedDeliveryContainer);
                console.log('📍 Found actual container:', !!actualDeliveryContainer);
                
                // If order is delivered or item_received, show actual delivery time
                if (['delivered', 'item_received'].includes(newStatus)) {
                    console.log('✅ Order delivered, updating to actual delivery time');
                    
                    // Hide estimated delivery time container if it exists
                    if (estimatedDeliveryContainer) {
                        estimatedDeliveryContainer.style.display = 'none';
                    }
                    
                    // Create or update actual delivery time container
                    let actualContainer = actualDeliveryContainer;
                    if (!actualContainer) {
                        // Create new actual delivery time container
                        actualContainer = document.createElement('div');
                        actualContainer.className = 'mt-6 p-4 bg-green-50 rounded-lg border border-green-200 actual-delivery-time';
                        
                        // Insert after estimated container or at appropriate location
                        const insertLocation = estimatedDeliveryContainer || 
                                             document.querySelector('.progress-tracker')?.parentElement ||
                                             document.querySelector('.order-timeline');
                        
                        if (insertLocation) {
                            insertLocation.parentNode.insertBefore(actualContainer, insertLocation.nextSibling);
                        }
                    }
                    
                    // Update actual delivery time content
                    const actualTime = data.actual_delivery_time || new Date().toLocaleString('vi-VN', {
                        hour: '2-digit',
                        minute: '2-digit',
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    }).replace(',', ' -');
                    
                    actualContainer.innerHTML = `
                        <div class="flex items-center text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span class="font-medium">Đã giao thành công lúc: </span>
                            <span class="ml-1 delivery-time-value">${actualTime}</span>
                        </div>
                    `;
                    
                    // Show the actual container
                    actualContainer.style.display = 'block';
                    
                    // Add animation effect
                    actualContainer.style.animation = 'fadeIn 0.5s ease-in-out';
                    
                } else if (data.estimated_delivery_time && estimatedDeliveryContainer) {
                    // Update estimated delivery time
                    console.log('⏰ Updating estimated delivery time');
                    
                    const timeValueElement = estimatedDeliveryContainer.querySelector('.delivery-time-value');
                    if (timeValueElement) {
                        timeValueElement.textContent = data.estimated_delivery_time;
                    }
                    
                    // Ensure estimated container is visible
                    estimatedDeliveryContainer.style.display = 'block';
                    
                    // Hide actual container if it exists
                    if (actualDeliveryContainer) {
                        actualDeliveryContainer.style.display = 'none';
                    }
                }
                
                // Add CSS animation if not already present
                if (!document.querySelector('#delivery-time-animations')) {
                    const style = document.createElement('style');
                    style.id = 'delivery-time-animations';
                    style.textContent = `
                        @keyframes fadeIn {
                            from { opacity: 0; transform: translateY(-10px); }
                            to { opacity: 1; transform: translateY(0); }
                        }
                    `;
                    document.head.appendChild(style);
                }
            }

            getStatusText(status) {
                const statusMap = {
                    'awaiting_confirmation': 'Chờ xác nhận',
                    'confirmed': 'Đã xác nhận đơn hàng',
                    'order_confirmed': 'Đã xác nhận đơn hàng',
                    'awaiting_driver': 'Chờ tài xế nhận đơn',
                    'driver_confirmed': 'Tài xế đã xác nhận',
                    'waiting_driver_pick_up': 'Tài xế đang chờ lấy đơn',
                    'driver_picked_up': 'Tài xế đã nhận đơn',
                    'in_transit': 'Đang giao hàng',
                    'delivered': 'Đã giao hàng',
                    'item_received': 'Khách hàng đã nhận hàng',
                    'cancelled': 'Đã hủy',
                    'refunded': 'Đã hoàn tiền',
                    'payment_failed': 'Thanh toán thất bại',
                    'payment_received': 'Đã nhận thanh toán',
                    'order_failed': 'Đơn thất bại'
                };
                return statusMap[status] || status;
            }

            getStatusColors(status) {
                const colorMap = {
                    'awaiting_confirmation': 'bg-yellow-100 text-yellow-800',
                    'confirmed': 'bg-blue-100 text-blue-800',
                    'order_confirmed': 'bg-blue-100 text-blue-800',
                    'awaiting_driver': 'bg-blue-200 text-blue-900',
                    'driver_confirmed': 'bg-indigo-100 text-indigo-800',
                    'waiting_driver_pick_up': 'bg-purple-100 text-purple-800',
                    'driver_picked_up': 'bg-purple-200 text-purple-900',
                    'in_transit': 'bg-cyan-100 text-cyan-800',
                    'delivered': 'bg-green-100 text-green-800',
                    'item_received': 'bg-green-200 text-green-900',
                    'cancelled': 'bg-red-100 text-red-800',
                    'refunded': 'bg-pink-100 text-pink-800',
                    'payment_failed': 'bg-red-200 text-red-900',
                    'payment_received': 'bg-lime-100 text-lime-800',
                    'order_failed': 'bg-gray-300 text-gray-900'
                };
                return colorMap[status] || 'bg-gray-100 text-gray-800';
            }

            getStatusCodeFromText(statusText) {
                const textToCodeMap = {
                    'Chờ xác nhận': 'awaiting_confirmation',
                    'Đã xác nhận đơn hàng': 'confirmed',
                    'Chờ tài xế nhận đơn': 'awaiting_driver',
                    'Tài xế đã xác nhận': 'driver_confirmed',
                    'Tài xế đang chờ lấy đơn': 'waiting_driver_pick_up',
                    'Tài xế đã nhận đơn': 'driver_picked_up',
                    'Đang giao hàng': 'in_transit',
                    'Đã giao hàng': 'delivered',
                    'Khách hàng đã nhận hàng': 'item_received',
                    'Đã hủy': 'cancelled',
                    'Đã hoàn tiền': 'refunded',
                    'Thanh toán thất bại': 'payment_failed',
                    'Đã nhận thanh toán': 'payment_received',
                    'Đơn thất bại': 'order_failed'
                };
                return textToCodeMap[statusText] || null;
            }

            checkInitialDriverStatus() {
                 console.log('🔍 Checking initial driver status...');
                 
                 // Check if we're in a state where driver should be assigned but UI shows waiting
                 const driverContainer = document.querySelector('.driver-container');
                 if (!driverContainer) {
                     console.log('❌ Driver container not found');
                     return;
                 }
                 
                 console.log('📋 Driver container HTML:', driverContainer.innerHTML.substring(0, 200) + '...');
                 
                 // Get current order status from multiple possible sources
                 let statusElement = document.getElementById('order-status-display');
                 if (!statusElement) {
                     // Try alternative selectors
                     statusElement = document.querySelector('.order-status');
                     if (!statusElement) {
                         statusElement = document.querySelector('[class*="status"]');
                     }
                 }
                 
                 if (!statusElement) {
                     console.log('❌ Status element not found');
                     return;
                 }
                 
                 const currentStatusText = statusElement.textContent.trim();
                 console.log('📊 Current status text:', currentStatusText);
                 
                 // Check if we're showing a loading state but should have driver info
                 const isShowingLoadingState = driverContainer.innerHTML.includes('Đang tìm tài xế') || 
                                             driverContainer.innerHTML.includes('animate-spin') ||
                                             driverContainer.innerHTML.includes('animate-bounce');
                 
                 // Check if we're showing "no driver assigned" but status indicates driver should be there
                 const isShowingNoDriver = driverContainer.innerHTML.includes('Chưa có tài xế được phân công');
                 
                 console.log('🔄 Loading state:', isShowingLoadingState);
                 console.log('❌ No driver state:', isShowingNoDriver);
                 
                 // Statuses that should have driver assigned
                 const driverRequiredStatuses = [
                     'Tài xế đã xác nhận', 
                     'Chờ tài xế lấy hàng', 
                     'Tài xế đã lấy hàng', 
                     'Đang giao',
                     'Đã giao',
                     'Khách đã nhận hàng'
                 ];
                 
                 const shouldHaveDriver = driverRequiredStatuses.includes(currentStatusText);
                 console.log('✅ Should have driver:', shouldHaveDriver);
                 
                 // If status indicates driver should be assigned but UI shows loading/no driver
                 if (shouldHaveDriver && (isShowingLoadingState || isShowingNoDriver)) {
                     console.log('🔄 Status indicates driver should be assigned, fetching current data...');
                     this.fetchCurrentOrderData();
                 } else if (shouldHaveDriver) {
                     // Even if not showing loading state, check if driver status needs updating
                     const driverStatusElement = driverContainer.querySelector('.driver-status');
                     if (driverStatusElement && driverStatusElement.textContent.trim() !== currentStatusText) {
                         console.log('🔄 Driver status text needs updating, fetching current data...');
                         this.fetchCurrentOrderData();
                     } else if (driverStatusElement) {
                         // Try to update status directly based on current order status
                         const statusFromText = this.getStatusCodeFromText(currentStatusText);
                         if (statusFromText) {
                             console.log('🔄 Updating driver status directly to:', statusFromText);
                             this.updateDriverStatus(statusFromText);
                         }
                     }
                 } else {
                     console.log('✅ Driver status appears to be correct or no action needed');
                 }
             }
            
            async fetchCurrentOrderData() {
                 try {
                     const response = await fetch(`/admin/orders/${this.orderId}/refresh-status`, {
                         method: 'GET',
                         headers: {
                             'X-Requested-With': 'XMLHttpRequest',
                             'Accept': 'application/json'
                         }
                     });
                     
                     if (response.ok) {
                         const data = await response.json();
                         if (data.success && data.order && data.order.driver) {
                             this.updateDriverInfo(data.order.driver, data.order.status);
                         }
                     }
                 } catch (error) {
                     console.log('Could not fetch current order data:', error);
                 }
             }

            destroy() {
                // Clean up Pusher connections
                if (this.orderStatusChannel) {
                    this.pusher.unsubscribe('order-status-updates');
                }
                if (this.adminChannel) {
                    this.pusher.unsubscribe('admin-orders-channel');
                }
                if (this.pusher) {
                    this.pusher.disconnect();
                }
            }
        }

        // Export class to global scope
        window.AdminOrderShowRealtime = AdminOrderShowRealtime;
    }


}