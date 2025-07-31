/**
 * Customer Order Realtime Updates
 * Handles real-time order status updates for customers
 */

class CustomerOrderRealtime {
    constructor() {
        this.pusher = null;
        this.channels = {};
        this.isInitialized = false;
        this.userId = window.currentUserId;

        // Bind methods to preserve context
        this.init = this.init.bind(this);
        this.setupOrderChannel = this.setupOrderChannel.bind(this);
        this.setupNotificationChannel = this.setupNotificationChannel.bind(this);
        this.setupCustomNotificationChannel = this.setupCustomNotificationChannel.bind(this);
        this.handleOrderStatusUpdate = this.handleOrderStatusUpdate.bind(this);
        this.handleNotification = this.handleNotification.bind(this);
        this.handleCustomNotification = this.handleCustomNotification.bind(this);
        this.cleanup = this.cleanup.bind(this);

        console.log('🚀 CustomerOrderRealtime initialized for user:', this.userId);
    }

    init() {
        if (this.isInitialized) {
            console.log('⚠️ CustomerOrderRealtime already initialized');
            return;
        }

        if (!this.userId) {
            console.log('⚠️ No user ID found, skipping realtime initialization');
            return;
        }

        if (typeof Pusher === 'undefined') {
            console.error('❌ Pusher not loaded');
            return;
        }

        try {
            // Initialize Pusher if not already done by layout
            if (!window.pusher) {
                window.pusher = new Pusher(window.pusherKey || '', {
                    cluster: window.pusherCluster || 'ap1',
                    encrypted: true,
                    authEndpoint: '/broadcasting/auth',
                    auth: {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? .getAttribute('content') || ''
                        }
                    }
                });

                // Add connection event listeners
                window.pusher.connection.bind('connected', () => {
                    console.log('✅ Pusher connected successfully');
                });

                window.pusher.connection.bind('error', (err) => {
                    console.error('❌ Pusher connection error:', err);
                });

                window.pusher.connection.bind('disconnected', () => {
                    console.log('⚠️ Pusher disconnected');
                });
            }

            this.pusher = window.pusher;

            // Setup channels
            this.setupOrderChannel();
            this.setupNotificationChannel();
            this.setupCustomNotificationChannel();

            this.isInitialized = true;
            console.log('✅ CustomerOrderRealtime initialized successfully');

        } catch (error) {
            console.error('❌ Failed to initialize CustomerOrderRealtime:', error);
        }
    }

    setupOrderChannel() {
        const channelName = `private-customer.${this.userId}.orders`;

        try {
            this.channels.orders = this.pusher.subscribe(channelName);

            this.channels.orders.bind('OrderStatusUpdated', this.handleOrderStatusUpdate);

            this.channels.orders.bind('pusher:subscription_succeeded', () => {
                console.log('✅ Subscribed to order updates channel for customer', this.userId);
            });

            this.channels.orders.bind('pusher:subscription_error', (error) => {
                console.error('❌ Failed to subscribe to order updates channel:', error);
            });

        } catch (error) {
            console.error('❌ Failed to setup order channel:', error);
        }
    }

    setupNotificationChannel() {
        const channelName = `private-App.Models.User.${this.userId}`;

        try {
            this.channels.notifications = this.pusher.subscribe(channelName);

            this.channels.notifications.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', this.handleNotification);

            this.channels.notifications.bind('pusher:subscription_succeeded', () => {
                console.log('✅ Subscribed to Laravel notifications channel for user', this.userId);
            });

            this.channels.notifications.bind('pusher:subscription_error', (error) => {
                console.error('❌ Failed to subscribe to Laravel notifications channel:', error);
            });

        } catch (error) {
            console.error('❌ Failed to setup notification channel:', error);
        }
    }

    setupCustomNotificationChannel() {
        const channelName = `customer.${this.userId}.notifications`;

        try {
            this.channels.customNotifications = this.pusher.subscribe(channelName);

            this.channels.customNotifications.bind('new-message', this.handleCustomNotification);

            this.channels.customNotifications.bind('pusher:subscription_succeeded', () => {
                console.log('✅ Subscribed to custom notifications channel for user', this.userId);
            });

            this.channels.customNotifications.bind('pusher:subscription_error', (error) => {
                console.error('❌ Failed to subscribe to custom notifications channel:', error);
            });

        } catch (error) {
            console.error('❌ Failed to setup custom notification channel:', error);
        }
    }

    handleOrderStatusUpdate(data) {
        console.log('🛍️ Order status updated:', data);
        console.log('🔍 Debug - data.order exists:', !!data.order);
        console.log('🔍 Debug - showToast function exists:', typeof window.showToast);

        // Show notification using the global showToast function
        if (typeof window.showToast === 'function') {
            let orderId, orderData;

            // Check if data has order property or if data itself is the order
            if (data.order) {
                orderId = data.order.id;
                orderData = {
                    status: data.order.status,
                    status_text: data.status_text || data.order.status_text || this.getStatusText(data.order.status),
                    actual_delivery_time: data.actual_delivery_time || data.order.actual_delivery_time
                };
            } else if (data.id && data.status) {
                // Data itself might be the order object
                orderId = data.id;
                orderData = {
                    status: data.status,
                    status_text: data.status_text || this.getStatusText(data.status),
                    actual_delivery_time: data.actual_delivery_time
                };
            } else {
                console.error('❌ Invalid order data structure:', data);
                return;
            }

            console.log('📋 Calling showOrderNotification with:', {
                orderId,
                orderData
            });

            // Cập nhật UI nếu đang ở trang chi tiết đơn hàng
            this.updateOrderUI(orderId, orderData);

            // Use the same notification logic as in orders.blade.php
            this.showOrderNotification(orderId, orderData);
        } else {
            console.error('❌ showToast function not available');
        }
    }

    handleNotification(data) {
        console.log('🔔 Laravel Notification received:', data);

        // Chỉ xử lý notification list và bell shake, không hiển thị toast
        // Toast sẽ được xử lý bởi OrderStatusUpdated event để tránh trùng lặp

        // Gọi hàm có sẵn để fetch lại toàn bộ list noti từ server
        if (typeof window.fetchNotifications === 'function') {
            window.fetchNotifications();
        } else if (typeof fetchNotifications === 'function') {
            fetchNotifications();
        }

        // Gọi hiệu ứng rung chuông (nếu có)
        if (typeof window.triggerBellShake === 'function') {
            window.triggerBellShake();
        } else if (typeof triggerBellShake === 'function') {
            triggerBellShake();
        }
    }

    handleCustomNotification(data) {
        console.log('📢 Custom notification received:', data);

        if (typeof window.fetchNotifications === 'function') {
            window.fetchNotifications();
        } else if (typeof fetchNotifications === 'function') {
            fetchNotifications();
        }

        if (typeof window.triggerBellShake === 'function') {
            window.triggerBellShake();
        } else if (typeof triggerBellShake === 'function') {
            triggerBellShake();
        }
    }

    showOrderNotification(orderId, data) {
        // Use the global showToast function from fullLayoutMaster.blade.php
        if (typeof window.showToast === 'function') {
            // Handle special case for 'confirmed' status - show 2 notifications
            if (data.status === 'confirmed') {
                // First notification: Order confirmed by restaurant
                const message1 = `Đơn hàng đã được xác nhận`;
                window.showToast(message1, 'success', 5000);

                // Second notification: Looking for driver (delayed by 2 seconds)
                setTimeout(() => {
                    const message2 = `Đang tìm tài xế cho đơn hàng của bạn`;
                    window.showToast(message2, 'info', 5000);
                }, 2000);
            } else if (data.status === 'awaiting_driver') {
                // Special notification for driver found
                const message = `Đã tìm được tài xế cho đơn hàng của bạn`;
                window.showToast(message, 'success', 5000);
            } else {
                // Regular single notification for other statuses
                const message = `Đơn hàng #${orderId} đã chuyển sang ${data.status_text}`;

                // Determine notification type based on status
                let notificationType = 'info';
                if (data.status === 'delivered' || data.status === 'item_received') {
                    notificationType = 'success';
                } else if (data.status === 'cancelled' || data.status === 'failed') {
                    notificationType = 'error';
                } else if (data.status === 'preparing' || data.status === 'shipping') {
                    notificationType = 'warning';
                }

                window.showToast(message, notificationType, 5000);
            }
        } else {
            // Fallback to console if showToast is not available
            if (data.status === 'confirmed') {
                console.log(`Cập nhật đơn hàng: Đơn hàng đã được xác nhận`);
                console.log(`Cập nhật đơn hàng: Đang tìm tài xế cho đơn hàng của bạn`);
            } else if (data.status === 'awaiting_driver') {
                console.log(`Cập nhật đơn hàng: Đã tìm được tài xế cho đơn hàng của bạn`);
            } else {
                console.log(`Cập nhật đơn hàng: Đơn hàng #${orderId} đã chuyển sang ${data.status_text}`);
            }
        }
    }

    getStatusText(status) {
        const statusTexts = {
            'pending': 'Chờ xác nhận',
            'confirmed': 'Đã xác nhận',
            'awaiting_driver': 'Chờ tài xế',
            'driver_found': 'Đã tìm được tài xế',
            'preparing': 'Đang chuẩn bị',
            'ready': 'Sẵn sàng giao',
            'shipping': 'Đang giao hàng',
            'delivered': 'Đã giao hàng',
            'item_received': 'Đã nhận hàng',
            'cancelled': 'Đã hủy',
            'failed': 'Thất bại',
            'pending_payment': 'Chưa thanh toán'
        };
        return statusTexts[status] || status;
    }

    getStatusColor(status) {
        const statusColors = {
            'pending': 'bg-yellow-100 text-yellow-700',
            'confirmed': 'bg-blue-100 text-blue-700',
            'awaiting_driver': 'bg-indigo-100 text-indigo-700',
            'driver_found': 'bg-indigo-100 text-indigo-700',
            'preparing': 'bg-orange-100 text-orange-700',
            'ready': 'bg-teal-100 text-teal-700',
            'shipping': 'bg-purple-100 text-purple-700',
            'delivered': 'bg-green-100 text-green-700',
            'item_received': 'bg-green-100 text-green-700',
            'cancelled': 'bg-red-100 text-red-700',
            'failed': 'bg-red-100 text-red-700',
            'pending_payment': 'bg-orange-100 text-orange-700'
        };
        return statusColors[status] || 'bg-gray-100 text-gray-700';
    }

    updateOrderUI(orderId, orderData) {
        // Tìm tất cả các card đơn hàng có order-id tương ứng
        const orderCards = document.querySelectorAll(`[data-order-id="${orderId}"]`);

        if (orderCards.length === 0) {
            console.log(`Không tìm thấy card đơn hàng #${orderId} trên trang hiện tại`);
            return;
        }

        console.log(`Cập nhật UI cho ${orderCards.length} card đơn hàng #${orderId}`);

        orderCards.forEach(card => {
            // Cập nhật trạng thái đơn hàng
            const statusBadge = card.querySelector('.order-status-badge');
            if (statusBadge) {
                // Xóa tất cả các class hiện tại
                statusBadge.className = 'order-status-badge text-xs font-semibold px-2 py-1 rounded';
                // Thêm class mới dựa trên trạng thái
                statusBadge.classList.add(...this.getStatusColor(orderData.status).split(' '));
                // Cập nhật text
                statusBadge.textContent = orderData.status_text || this.getStatusText(orderData.status);
            }

            // Cập nhật thời gian giao hàng thực tế nếu có
            if (orderData.actual_delivery_time) {
                const deliveryTimeElement = card.querySelector('.delivery-time');
                if (deliveryTimeElement) {
                    deliveryTimeElement.textContent = orderData.actual_delivery_time;
                }
            }

            // Cập nhật trang chi tiết đơn hàng nếu đang ở trang đó
            if (window.location.pathname.includes('/customer/orders/') && window.location.pathname.includes(`/${orderId}`)) {
                const detailStatusBadge = document.querySelector('#order-detail-status');
                if (detailStatusBadge) {
                    // Xóa tất cả các class hiện tại
                    detailStatusBadge.className = 'text-sm font-semibold px-3 py-1 rounded';
                    // Thêm class mới dựa trên trạng thái
                    detailStatusBadge.classList.add(...this.getStatusColor(orderData.status).split(' '));
                    // Cập nhật text
                    detailStatusBadge.textContent = orderData.status_text || this.getStatusText(orderData.status);
                }

                // Cập nhật timeline nếu có
                this.updateOrderTimeline(orderData.status);
            }
        });
    }

    updateOrderTimeline(status) {
        // Tìm tất cả các bước trong timeline
        const timelineSteps = document.querySelectorAll('.order-timeline-step');
        if (timelineSteps.length === 0) return;

        // Mapping trạng thái đơn hàng với các bước trong timeline
        const statusToStep = {
            'pending': 0,
            'confirmed': 1,
            'awaiting_driver': 2,
            'driver_found': 2,
            'preparing': 3,
            'ready': 3,
            'shipping': 4,
            'delivered': 5,
            'item_received': 5,
            'cancelled': -1,
            'failed': -1
        };

        const currentStep = statusToStep[status] || 0;

        // Nếu đơn hàng bị hủy hoặc thất bại, hiển thị thông báo đặc biệt
        if (currentStep === -1) {
            timelineSteps.forEach(step => {
                step.classList.remove('active', 'completed');
                step.classList.add('cancelled');
            });
            return;
        }

        // Cập nhật các bước trong timeline
        timelineSteps.forEach((step, index) => {
            if (index < currentStep) {
                // Các bước đã hoàn thành
                step.classList.remove('active', 'cancelled');
                step.classList.add('completed');
            } else if (index === currentStep) {
                // Bước hiện tại
                step.classList.remove('completed', 'cancelled');
                step.classList.add('active');
            } else {
                // Các bước chưa hoàn thành
                step.classList.remove('active', 'completed', 'cancelled');
            }
        });
    }

    cleanup() {
        console.log('🧹 Cleaning up CustomerOrderRealtime...');

        // Unsubscribe from all channels
        Object.keys(this.channels).forEach(channelKey => {
            const channel = this.channels[channelKey];
            if (channel && this.pusher) {
                try {
                    this.pusher.unsubscribe(channel.name);
                    console.log(`✅ Unsubscribed from ${channel.name}`);
                } catch (error) {
                    console.error(`❌ Failed to unsubscribe from ${channel.name}:`, error);
                }
            }
        });

        this.channels = {};
        this.isInitialized = false;

        console.log('✅ CustomerOrderRealtime cleanup completed');
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (window.currentUserId && window.pusherKey && window.pusherCluster) {
        window.customerOrderRealtime = new CustomerOrderRealtime(window.currentUserId, {
            key: window.pusherKey,
            cluster: window.pusherCluster,
            encrypted: true,
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }
        });
    }
});

// Clean up when page is unloaded
window.addEventListener('beforeunload', function() {
    if (window.customerOrderRealtime) {
        window.customerOrderRealtime.cleanup();
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (window.customerOrderRealtime) {
        window.customerOrderRealtime.cleanup();
    }
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CustomerOrderRealtime;
}