// Admin Notifications - Only for notification bell, not for table manipulation
class AdminNotifications {
    constructor() {
        this.pusherKey = window.pusherKey;
        this.pusherCluster = window.pusherCluster;
        this.pusher = null;
        this.channel = null;
        this.processedOrders = new Map();
        
        this.init();
    }

    init() {
        // Initialize Pusher for notifications only
        if (this.pusherKey && this.pusherCluster) {
            this.initializePusher();
        }
    }

    initializePusher() {
        try {
            this.pusher = new Pusher(this.pusherKey, {
                cluster: this.pusherCluster,
                encrypted: true
            });

            this.subscribeToPublicChannel();
        } catch (error) {
            console.error('Error initializing Pusher for admin notifications:', error);
        }
    }

    subscribeToPublicChannel() {
        try {
            // Subscribe to public order channel
            this.channel = this.pusher.subscribe('orders');
            
            // Listen for new orders
            this.channel.bind('order.created', (data) => {
                this.handleNewOrderNotification(data);
            });

        } catch (error) {
            console.error('Error subscribing to channel:', error);
        }
    }

    handleNewOrderNotification(data) {
        // Check if this is a duplicate event
        const orderId = data.order.id;
        const now = Date.now();
        
        if (this.processedOrders.has(orderId)) {
            const lastProcessed = this.processedOrders.get(orderId);
            if (now - lastProcessed < 30000) { // 30 seconds
                return;
            }
        }
        
        // Mark as processed
        this.processedOrders.set(orderId, now);
        
        // Clean up old entries (older than 5 minutes)
        for (const [id, timestamp] of this.processedOrders.entries()) {
            if (now - timestamp > 300000) { // 5 minutes
                this.processedOrders.delete(id);
            }
        }
        
        // Add notification to admin bell
        this.addNotificationToBell(data.order);
        
        // Show browser notification
        this.showNotification('Đơn hàng mới', `Đơn hàng #${data.order.order_code || data.order.id} từ ${data.order.branch?.name || 'Chi nhánh'}`);
    }

    addNotificationToBell(order) {
        const notificationList = document.getElementById('admin-notification-list');
        if (!notificationList) return;

        // Create notification HTML
        const notificationHtml = `
            <div class="notification-item p-2 rounded-md hover:bg-accent cursor-pointer transition-colors duration-200 bg-primary/10 text-primary font-semibold"
                 data-notification-id="new-order-${order.id}"
                 onclick="markAdminNotificationAsRead('new-order-${order.id}', '/admin/orders?status=awaiting_confirmation')">
                <div class="flex items-start gap-2">
                    <div class="w-2 h-2 bg-primary rounded-full mt-1.5 flex-shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">Đơn hàng mới #${order.order_code || order.id}</p>
                        <p class="text-xs text-muted-foreground truncate">Từ ${order.branch?.name || 'Chi nhánh'}</p>
                        <p class="text-xs text-muted-foreground">${this.formatDateTime(order.created_at)}</p>
                    </div>
                </div>
            </div>
        `;

        // Remove empty state if exists
        const emptyState = notificationList.querySelector('.text-center.text-xs.text-muted-foreground.py-4');
        if (emptyState) emptyState.remove();

        notificationList.insertAdjacentHTML('afterbegin', notificationHtml);

        // Update notification count
        this.updateNotificationCount(1);

        // Animation
        const newNotification = notificationList.firstElementChild;
        if (newNotification) {
            newNotification.style.opacity = '0';
            newNotification.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                newNotification.style.transition = 'all 0.3s ease';
                newNotification.style.opacity = '1';
                newNotification.style.transform = 'translateY(0)';
            }, 10);
        }
    }

    updateNotificationCount(increment = 0) {
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

        // Trigger bell shake animation
        if (increment > 0) {
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
    }

    showNotification(title, message) {
        // Show browser notification if permission granted
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(title, {
                body: message,
                icon: '/favicon.ico',
                tag: 'new-order-notification'
            });
        }
    }

    formatDateTime(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const now = new Date();
        const diffInMinutes = Math.floor((now - date) / (1000 * 60));
        
        if (diffInMinutes < 1) {
            return 'Vừa xong';
        } else if (diffInMinutes < 60) {
            return `${diffInMinutes} phút trước`;
        } else if (diffInMinutes < 1440) {
            const hours = Math.floor(diffInMinutes / 60);
            return `${hours} giờ trước`;
        } else {
            return date.toLocaleDateString('vi-VN', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }

    destroy() {
        if (this.channel) {
            this.channel.unbind_all();
            this.pusher.unsubscribe('orders');
        }
        if (this.pusher) {
            this.pusher.disconnect();
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if not on orders page (to avoid conflict)
    if (!window.location.pathname.includes('/admin/orders')) {
        window.adminNotifications = new AdminNotifications();
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (window.adminNotifications) {
                window.adminNotifications.destroy();
            }
        });
    }
});