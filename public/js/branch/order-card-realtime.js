// Real-time order status updates for branch order cards
class BranchOrderCardRealtime {
    constructor() {
        this.pusher = null;
        this.channels = new Map();
        this.pollingInterval = null;
        this.initializePusher();
        this.subscribeToOrderChannels();
    }

    initializePusher() {
        try {
            // Get config from window object
            const pusherKey = window.pusherKey;
            const pusherCluster = window.pusherCluster;

            if (!pusherKey || !pusherCluster) {
                this.setupPollingFallback();
                return;
            }

            this.pusher = new Pusher(pusherKey, {
                cluster: pusherCluster,
                encrypted: true,
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }
            });

            this.pusher.connection.bind('error', (err) => {
                console.error('Pusher connection error:', err);
                this.setupPollingFallback();
            });

        } catch (error) {
            console.error('Pusher initialization error:', error);
            this.setupPollingFallback();
        }
    }

    subscribeToOrderChannels() {
        // Subscribe to each order's private channel
        const orderCards = document.querySelectorAll('.order-card[data-order-id]');
        orderCards.forEach(card => {
            const orderId = card.dataset.orderId;
            if (orderId) {
                this.subscribeToOrderChannel(orderId);
            }
        });

        // Subscribe to branch orders channel for general updates
        this.subscribeToBranchOrdersChannel();

        // Subscribe to order status updates channel for additional coverage
        this.subscribeToOrderStatusUpdatesChannel();
    }

    subscribeToOrderChannel(orderId) {
        if (!this.pusher) return;

        const channelName = `private-order.${orderId}`;

        try {
            const channel = this.pusher.subscribe(channelName);
            this.channels.set(orderId, channel);

            channel.bind('order-status-updated', (data) => {
                console.log('Received order status update:', data);
                this.handleOrderStatusUpdate(orderId, data);
            });
        } catch (error) {
            console.error('Error subscribing to channel:', error);
            // Fallback to polling if subscription fails
        }
    }

    subscribeToBranchOrdersChannel() {
        if (!this.pusher) return;

        try {
            const branchChannel = this.pusher.subscribe('branch-orders-channel');
            this.channels.set('branch-orders', branchChannel);

            branchChannel.bind('order-status-updated', (data) => {
                console.log('Received branch order status update:', data);
                if (data.order && data.order.id) {
                    this.handleOrderStatusUpdate(data.order.id, data);
                }
            });
        } catch (error) {
            console.error('Error subscribing to branch orders channel:', error);
        }
    }

    subscribeToOrderStatusUpdatesChannel() {
        if (!this.pusher) return;

        try {
            const statusUpdatesChannel = this.pusher.subscribe('order-status-updates');
            this.channels.set('order-status-updates', statusUpdatesChannel);

            statusUpdatesChannel.bind('order-status-updated', (data) => {
                console.log('Received order status update from status updates channel:', data);
                if (data.order && data.order.id) {
                    this.handleOrderStatusUpdate(data.order.id, data);
                }
            });
        } catch (error) {
            console.error('Error subscribing to order status updates channel:', error);
        }
    }

    handleOrderStatusUpdate(orderId, data) {
        // Find the order card element
        const orderCard = document.querySelector(`.order-card[data-order-id="${orderId}"]`);
        if (!orderCard) {
            console.warn(`Order card not found for order ID: ${orderId}`);
            return;
        }

        // Update status in the order-status-container
        const statusContainer = orderCard.querySelector('.order-status-container');
        if (statusContainer && data.status) {
            this.updateStatusBadge(statusContainer, data);
        }

        // Update action buttons based on new status
        this.updateActionButtons(orderCard, data.status);

        // Update delivery time if available
        if (data.actual_delivery_time) {
            this.updateDeliveryTime(orderCard, data.actual_delivery_time);
        }

        // Show notification
        this.showNotification(orderId, data);
    }

    updateStatusBadge(statusContainer, data) {
        // Define status colors and texts as fallback
        const statusColors = {
            'awaiting_confirmation': 'bg-yellow-500 text-white',
            'confirmed': 'bg-blue-500 text-white',
            'awaiting_driver': 'bg-blue-400 text-white',
            'driver_assigned': 'bg-indigo-500 text-white',
            'driver_confirmed': 'bg-indigo-600 text-white',
            'waiting_driver_pick_up': 'bg-purple-400 text-white',
            'driver_picked_up': 'bg-purple-500 text-white',
            'in_transit': 'bg-orange-500 text-white',
            'delivered': 'bg-green-500 text-white',
            'item_received': 'bg-green-600 text-white',
            'cancelled': 'bg-gray-400 text-white',
            'refunded': 'bg-pink-500 text-white',
            'payment_failed': 'bg-red-500 text-white',
            'payment_received': 'bg-green-700 text-white',
            'order_failed': 'bg-red-600 text-white',
        };

        const statusTexts = {
            'awaiting_confirmation': 'Chờ xác nhận',
            'confirmed': 'Đã xác nhận',
            'awaiting_driver': 'Chờ tài xế',
            'driver_assigned': 'Đã gán tài xế',
            'driver_confirmed': 'Tài xế đã xác nhận',
            'waiting_driver_pick_up': 'Chờ tài xế lấy hàng',
            'driver_picked_up': 'Tài xế đã nhận đơn',
            'in_transit': 'Đang giao',
            'delivered': 'Đã giao',
            'item_received': 'Đã nhận hàng',
            'cancelled': 'Đã hủy',
            'refunded': 'Đã hoàn tiền',
            'payment_failed': 'Thanh toán thất bại',
            'payment_received': 'Đã nhận thanh toán',
            'order_failed': 'Đơn thất bại',
        };

        // Clear existing content
        statusContainer.innerHTML = '';

        // Use data from event if available, otherwise fallback to local definitions
        const statusClass = data.status_color || statusColors[data.status] || 'bg-gray-300 text-gray-700';
        const statusText = data.status_text || statusTexts[data.status] || data.status;
        const statusIcon = data.status_icon || '';

        // Handle special case for 'confirmed' status with spinner
        if (data.status === 'confirmed') {
            statusContainer.innerHTML = `
                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-md bg-blue-100 text-blue-700">
                    <svg class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M12 2a10 10 0 0 1 10 10h-4a6 6 0 0 0-6-6V2z"></path>
                    </svg>
                    Đang tìm tài xế
                </span>
            `;
        } else {
            // Use regular status badge with optional icon
            const iconHtml = statusIcon ? `${statusIcon} ` : '';

            statusContainer.innerHTML = `
                <span class="px-2 py-1 text-xs font-medium rounded-md status-badge ${statusClass}">
                    ${iconHtml}${statusText}
                </span>
            `;
        }
    }

    updateActionButtons(orderCard, newStatus) {
        const actionContainer = orderCard.querySelector('.absolute.left-0.bottom-0 .flex.gap-2');
        if (!actionContainer) return;

        // Clear existing buttons
        actionContainer.innerHTML = '';

        // Add appropriate buttons based on new status
        if (newStatus === 'awaiting_confirmation') {
            actionContainer.innerHTML = `
                <button data-quick-action="confirm" data-order-id="${orderCard.dataset.orderId}" class="px-3 py-2 text-sm rounded-md bg-black text-white hover:bg-gray-800 confirm-btn">
                    Xác nhận
                </button>
                <button data-quick-action="cancel" data-order-id="${orderCard.dataset.orderId}" class="px-3 py-2 text-sm rounded-md bg-red-500 text-white hover:bg-red-600">
                    Hủy
                </button>
                <a href="/branch/orders/${orderCard.dataset.orderId}" class="flex-1 px-3 py-2 text-sm rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 text-center">Chi tiết</a>
            `;
        } else if (newStatus === 'confirmed') {
            actionContainer.innerHTML = `
                <a href="/branch/orders/${orderCard.dataset.orderId}" class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 text-center">Chi tiết</a>
            `;
        } else {
            actionContainer.innerHTML = `
                <a href="/branch/orders/${orderCard.dataset.orderId}" class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 text-center">Chi tiết</a>
            `;
        }
    }

    updateDeliveryTime(orderCard, actualDeliveryTime) {
        // Find the delivery time element in the order details
        const deliveryTimeElements = orderCard.querySelectorAll('.flex.justify-between');

        deliveryTimeElements.forEach(element => {
            const label = element.querySelector('.text-gray-500');
            if (label && (label.textContent.includes('Dự kiến giao') || label.textContent.includes('Thực tế giao'))) {
                const valueElement = element.querySelector('.font-medium, .text-gray-700');
                if (valueElement) {
                    // Update label to show actual delivery time
                    label.textContent = 'Thực tế giao:';
                    valueElement.textContent = actualDeliveryTime;
                    valueElement.className = 'font-medium text-green-600';
                }
            }
        });
    }

    showNotification(orderId, data) {
        // Simple notification - you can enhance this
        if (typeof toastr !== 'undefined') {
            toastr.info(`Đơn hàng #${orderId} đã cập nhật trạng thái: ${data.status_text || data.status}`);
        } else {
            console.log(`Order #${orderId} status updated:`, data);
        }
    }

    setupPollingFallback() {
        // Poll for order status updates every 30 seconds as fallback
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }

        this.pollingInterval = setInterval(() => {
            // Simple polling implementation - could be enhanced with AJAX calls
            // to check for order updates from server
            console.log('Polling for order updates...');
        }, 30000);
    }

    destroy() {
        // Unsubscribe from all channels
        this.channels.forEach((channel, channelKey) => {
            if (this.pusher) {
                if (channelKey === 'branch-orders') {
                    this.pusher.unsubscribe('branch-orders-channel');
                } else if (channelKey === 'order-status-updates') {
                    this.pusher.unsubscribe('order-status-updates');
                } else {
                    this.pusher.unsubscribe(`private-order.${channelKey}`);
                }
            }
        });
        this.channels.clear();

        // Disconnect Pusher
        if (this.pusher) {
            this.pusher.disconnect();
            this.pusher = null;
        }

        // Clear polling interval
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize realtime order updates if Pusher is available and there are order cards
    if (typeof Pusher !== 'undefined' && document.querySelectorAll('.order-card[data-order-id]').length > 0 && !window.branchOrderCardRealtime) {
        window.branchOrderCardRealtime = new BranchOrderCardRealtime();
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (window.branchOrderCardRealtime) {
        window.branchOrderCardRealtime.destroy();
    }
});