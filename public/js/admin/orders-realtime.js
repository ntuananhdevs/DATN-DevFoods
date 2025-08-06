if (window.adminOrdersRealtimeInitialized) {
    // Đã khởi tạo, không làm gì nữa
} else {
    window.adminOrdersRealtimeInitialized = true;

    // Đảm bảo chỉ khai báo class AdminOrdersRealtime nếu chưa tồn tại
    if (typeof window.AdminOrdersRealtime === 'undefined') {
        class AdminOrdersRealtime {
            constructor() {
                this.pusherKey = window.pusherKey;
                this.pusherCluster = window.pusherCluster;
                this.pusher = null;
                this.channel = null;
                this.processedOrders = new Map(); // Để tránh duplicate với timestamp
                this.notificationInterval = null; // Để lặp lại thông báo
                this.hasNewOrder = false; // Flag để biết có đơn hàng mới
                this.latestOrder = null; // Lưu thông tin đơn hàng mới nhất
                
                this.init();
            }

            async init() {
                // Đăng ký Service Worker để nhận thông báo khi ở trang khác
                await this.registerServiceWorker();
                
                // Always bind events
                this.bindEvents();
                
                // Initialize Pusher
                if (this.pusherKey && this.pusherCluster) {
                    this.initializePusher();
                }
            }

            async registerServiceWorker() {
                if ('serviceWorker' in navigator && 'Notification' in window) {
                    try {
                        // Đăng ký Service Worker
                        const registration = await navigator.serviceWorker.register('/sw.js');
                        
                        // Yêu cầu quyền thông báo
                        if (Notification.permission === 'default') {
                            const permission = await Notification.requestPermission();
                            if (permission === 'granted') {
                                // Lưu subscription để nhận push notifications
                                this.saveSubscription(registration);
                            }
                        } else if (Notification.permission === 'granted') {
                            this.saveSubscription(registration);
                        }
                    } catch (error) {
                        // Service Worker không khả dụng, sử dụng fallback
                    }
                }
            }

            async saveSubscription(registration) {
                try {
                    const subscription = await registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: this.urlBase64ToUint8Array('YOUR_VAPID_PUBLIC_KEY') // Thay bằng VAPID key thực
                    });
                    
                    // Gửi subscription lên server để lưu
                    await fetch('/admin/notification-subscription', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({
                            subscription: subscription,
                            user_type: 'admin'
                        })
                    });
                } catch (error) {
                    // Không thể đăng ký push notification
                }
            }

            urlBase64ToUint8Array(base64String) {
                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                const base64 = (base64String + padding)
                    .replace(/-/g, '+')
                    .replace(/_/g, '/');

                const rawData = window.atob(base64);
                const outputArray = new Uint8Array(rawData.length);

                for (let i = 0; i < rawData.length; ++i) {
                    outputArray[i] = rawData.charCodeAt(i);
                }
                return outputArray;
            }

            showNotification(title, message) {
                // Hiển thị toast notification
                if (typeof dtmodalShowToast === 'function') {
                    dtmodalShowToast('notification', {
                        title: title,
                        message: message
                    });
                }
                
                // Hiển thị browser notification nếu có quyền
                if ('Notification' in window && Notification.permission === 'granted') {
                    new Notification(title, {
                        body: message,
                        icon: '/favicon.ico',
                        tag: 'new-order-notification'
                    });
                }
            }

            initializePusher() {
                try {
                    console.log('🚀 Initializing Pusher with key:', this.pusherKey, 'cluster:', this.pusherCluster);
                    
                    this.pusher = new Pusher(this.pusherKey, {
                        cluster: this.pusherCluster,
                        encrypted: true,
                        authEndpoint: '/broadcasting/auth'
                    });

                    // Connection events
                    this.pusher.connection.bind('connected', () => {
                        console.log('✅ Pusher connected successfully');
                    });

                    this.pusher.connection.bind('error', (err) => {
                        console.error('❌ Pusher connection error:', err);
                        this.showNotification('Lỗi kết nối', 'Không thể kết nối Pusher');
                    });

                    this.pusher.connection.bind('disconnected', () => {
                        console.log('⚠️ Pusher disconnected');
                        this.showNotification('Mất kết nối', 'Kết nối Pusher đã bị ngắt');
                    });

                    // Subscribe to public channel
                    this.subscribeToPublicChannel();

                } catch (error) {
                    console.error('❌ Error initializing Pusher:', error);
                    this.showNotification('Lỗi khởi tạo', 'Không thể khởi tạo Pusher');
                }
            }

            subscribeToPublicChannel() {
                // Subscribe to branch orders channel for new orders
                this.publicChannel = this.pusher.subscribe('branch-orders-channel');
                
                this.publicChannel.bind('pusher:subscription_succeeded', () => {
                    console.log('✅ Successfully subscribed to branch-orders-channel');
                });

                this.publicChannel.bind('pusher:subscription_error', (status) => {
                    console.error('❌ Failed to subscribe to branch-orders-channel:', status);
                    this.showNotification('Lỗi kết nối', 'Không thể kết nối kênh thông báo');
                });

                this.publicChannel.bind('new-order-received', (data) => {
                    // Admin nhận tất cả đơn hàng từ mọi branch
                    console.log('📦 New order received:', data);
                    this.hasNewOrder = true;
                    this.startNotificationLoop();
                    this.handleNewOrder(data);
                });

                // Subscribe to admin orders channel for status updates
                this.subscribeToAdminChannel();
                
                // Subscribe to individual order channels for status updates
                this.subscribeToOrderStatusUpdates();
            }

            subscribeToAdminChannel() {
                // Subscribe to admin orders channel for real-time status updates
                console.log('🔔 Subscribing to admin-orders-channel');
                this.adminChannel = this.pusher.subscribe('admin-orders-channel');
                
                this.adminChannel.bind('pusher:subscription_succeeded', () => {
                    console.log('✅ Successfully subscribed to admin-orders-channel');
                });

                this.adminChannel.bind('pusher:subscription_error', (status) => {
                    console.error('❌ Failed to subscribe to admin-orders-channel:', status);
                    this.showNotification('Lỗi kết nối', 'Không thể kết nối kênh admin');
                });

                // Listen for order status updates from admin channel
                this.adminChannel.bind('order-status-updated', (data) => {
                    console.log('📦 Admin channel - order-status-updated event received:', data);
                    this.handleOrderStatusUpdate(data);
                });

                // Listen for OrderStatusUpdated events
                this.adminChannel.bind('OrderStatusUpdated', (data) => {
                    console.log('📦 Admin channel - OrderStatusUpdated event received:', data);
                    this.handleOrderStatusUpdate(data);
                });
            }

            subscribeToOrderStatusUpdates() {
                // Subscribe to all order status update channels
                // We'll use a general channel for all order status updates
                console.log('🔔 Subscribing to order-status-updates channel');
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
                    console.log('📊 Event data details:', {
                        order_id: data.order_id,
                        old_status: data.old_status,
                        new_status: data.new_status || data.status,
                        status_text: data.status_text,
                        order_code: data.order_code
                    });
                    this.handleOrderStatusUpdate(data);
                });

                // Also listen for any other status update events that might be broadcasted
                this.orderStatusChannel.bind('order-status-updated', (data) => {
                    console.log('📦 order-status-updated event received:', data);
                    this.handleOrderStatusUpdate(data);
                });
            }

            handleOrderStatusUpdate(data) {
                console.log('🔄 Handling order status update:', data);
                console.log('📊 Old status:', data.old_status, '→ New status:', data.status || data.new_status);
                
                // Check if we need to move the order to a different tab
                const currentTab = this.getCurrentActiveTab();
                const orderShouldBeInCurrentTab = this.shouldOrderBeInTab(data.status || data.new_status, currentTab);
                
                console.log('📋 Current tab:', currentTab, '| Order should be in tab:', orderShouldBeInCurrentTab);
                
                if (!orderShouldBeInCurrentTab) {
                    // Remove order from current view since it no longer belongs here
                    console.log('🗑️ Removing order from current view');
                    this.removeOrderFromCurrentView(data.order_id);
                } else {
                    // Update the order row in the table
                    console.log('🔄 Updating order row status');
                    this.updateOrderRowStatus(data.order_id, data);
                }
                
                // Update status counts for both old and new status
                console.log('📊 Updating status counts');
                this.updateStatusCountsForStatusChange(data.old_status, data.status || data.new_status);
                
                // Show notification
                console.log('🔔 Showing status update notification');
                this.showStatusUpdateNotification(data);
            }

            getCurrentActiveTab() {
                // Get current status from URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get('status') || 'all';
            }

            shouldOrderBeInTab(orderStatus, tabStatus) {
                // If it's the "all" tab, all orders should be shown
                if (tabStatus === 'all' || !tabStatus) {
                    return true;
                }
                
                // For specific status tabs, only show orders with matching status
                return orderStatus === tabStatus;
            }

            removeOrderFromCurrentView(orderId) {
                const orderRow = document.querySelector(`tr[data-order-id="${orderId}"]`);
                if (orderRow) {
                    // Add fade out animation
                    orderRow.style.transition = 'opacity 0.5s ease';
                    orderRow.style.opacity = '0';
                    
                    // Remove after animation
                    setTimeout(() => {
                        orderRow.remove();
                        
                        // Check if table is empty and show empty message if needed
                        this.checkAndShowEmptyMessage();
                    }, 500);
                }
            }

            checkAndShowEmptyMessage() {
                const tableBody = document.querySelector('#orders-table tbody');
                if (tableBody && tableBody.children.length === 0) {
                    const emptyRow = document.createElement('tr');
                    emptyRow.innerHTML = `
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-900 mb-1">Không có đơn hàng</p>
                                <p class="text-gray-500">Chưa có đơn hàng nào trong trạng thái này.</p>
                            </div>
                        </td>
                    `;
                    tableBody.appendChild(emptyRow);
                }
            }

            updateOrderRowStatus(orderId, statusData) {
                // Find the order row in the table
                const orderRow = document.querySelector(`tr[data-order-id="${orderId}"]`);
                if (!orderRow) {
                    console.log('Order row not found for ID:', orderId);
                    return;
                }

                const status = statusData.status || statusData.new_status;
                console.log('🔄 Updating row for order:', orderId, 'to status:', status);

                // Update status badge
                const statusBadge = orderRow.querySelector('.order-status-badge');
                if (statusBadge) {
                    // Get status mapping
                    const statusMap = {
                        'awaiting_confirmation': ['Chờ xác nhận', 'bg-yellow-100 text-yellow-800'],
                        'confirmed': ['Đã xác nhận', 'bg-blue-100 text-blue-800'],
                        'order_confirmed': ['Đã xác nhận', 'bg-blue-100 text-blue-800'],
                        'awaiting_driver': ['Chờ tài xế', 'bg-blue-200 text-blue-900'],
                        'driver_confirmed': ['Tài xế đã xác nhận', 'bg-indigo-100 text-indigo-800'],
                        'waiting_driver_pick_up': ['Chờ tài xế lấy hàng', 'bg-purple-100 text-purple-800'],
                        'driver_picked_up': ['Tài xế đã lấy hàng', 'bg-purple-200 text-purple-900'],
                        'in_transit': ['Đang giao', 'bg-cyan-100 text-cyan-800'],
                        'delivered': ['Đã giao', 'bg-green-100 text-green-800'],
                        'item_received': ['Khách đã nhận hàng', 'bg-green-200 text-green-900'],
                        'cancelled': ['Đã hủy', 'bg-red-100 text-red-800'],
                        'refunded': ['Đã hoàn tiền', 'bg-pink-100 text-pink-800'],
                        'payment_failed': ['Thanh toán thất bại', 'bg-red-200 text-red-900'],
                        'payment_received': ['Đã nhận thanh toán', 'bg-lime-100 text-lime-800'],
                        'order_failed': ['Đơn thất bại', 'bg-gray-300 text-gray-900'],
                        'unpaid': ['Chưa thanh toán', 'bg-orange-100 text-orange-800'],
                    };
                    
                    const [label, cssClasses] = statusMap[status] || [
                        statusData.status_text || this.getStatusText(status),
                        'bg-gray-100 text-gray-800'
                    ];
                    
                    // Remove all existing classes and set new ones
                    statusBadge.className = `order-status-badge ${status} inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${cssClasses}`;
                    
                    // Update status text
                    statusBadge.textContent = label;
                    
                    // Add highlight effect
                    statusBadge.style.animation = 'pulse 1s ease-in-out';
                    setTimeout(() => {
                        statusBadge.style.animation = '';
                    }, 1000);
                }

                // Update payment status if exists
                const paymentStatusBadge = orderRow.querySelector('.payment-status-badge');
                if (paymentStatusBadge && statusData.payment_status) {
                    // Get payment status mapping
                    const paymentStatusMap = {
                        'pending': ['Chưa thanh toán', 'bg-yellow-100 text-yellow-800'],
                        'completed': ['Đã thanh toán', 'bg-green-100 text-green-800'],
                        'failed': ['Thất bại', 'bg-red-100 text-red-800'],
                        'refunded': ['Đã hoàn tiền', 'bg-pink-100 text-pink-800']
                    };
                    
                    const [paymentLabel, paymentCssClasses] = paymentStatusMap[statusData.payment_status] || [
                        statusData.payment_status_text || this.getPaymentStatusText(statusData.payment_status),
                        'bg-gray-100 text-gray-800'
                    ];
                    
                    // Update payment status badge with proper classes
                    paymentStatusBadge.className = `order-payment-status payment-status-badge ${statusData.payment_status} inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${paymentCssClasses}`;
                    
                    // Update payment status text
                    paymentStatusBadge.textContent = paymentLabel;
                }

                // Update delivery time if provided
                if (statusData.actual_delivery_time) {
                    const timeCell = orderRow.querySelector('td:nth-child(8)'); // Assuming time is in 8th column
                    if (timeCell) {
                        const deliveryTimeDiv = document.createElement('div');
                        deliveryTimeDiv.className = 'text-xs text-green-600 font-medium';
                        deliveryTimeDiv.textContent = `Giao: ${statusData.actual_delivery_time}`;
                        timeCell.appendChild(deliveryTimeDiv);
                    }
                }

                // Add row highlight effect
                orderRow.style.backgroundColor = '#e0f2fe';
                setTimeout(() => {
                    orderRow.style.backgroundColor = '';
                    orderRow.style.transition = 'background-color 0.5s ease';
                }, 2000);
            }

            getStatusText(status) {
                const statusMap = {
                    'awaiting_confirmation': 'Chờ xác nhận',
                    'confirmed': 'Đã xác nhận',
                    'awaiting_driver': 'Chờ tài xế nhận đơn',
                    'driver_confirmed': 'Tài xế đã xác nhận đơn',
                    'waiting_driver_pick_up': 'Tài xế đang chờ đơn',
                    'driver_picked_up': 'Tài xế đã nhận đơn',
                    'in_transit': 'Đang trong quá trình giao hàng',
                    'delivered': 'Đã giao thành công',
                    'item_received': 'Khách hàng đã nhận hàng',
                    'cancelled': 'Đã bị hủy',
                    'refunded': 'Đã được hoàn tiền',
                    'payment_failed': 'Thanh toán thất bại',
                    'payment_received': 'Thanh toán đã nhận',
                    'order_failed': 'Đơn hàng đã thất bại'
                };
                return statusMap[status] || status;
            }

            getPaymentStatusText(paymentStatus) {
                const paymentStatusMap = {
                    'pending': 'Chưa thanh toán',
                    'completed': 'Đã thanh toán',
                    'failed': 'Thất bại',
                    'refunded': 'Đã hoàn tiền'
                };
                return paymentStatusMap[paymentStatus] || paymentStatus;
            }

            showStatusUpdateNotification(data) {
                const status = data.status || data.new_status;
                const statusText = this.getStatusText(status);
                console.log('🔔 Notification for status:', status, '→', statusText);
                this.showNotification('Cập nhật đơn hàng', `Đơn hàng #${data.order_code || data.order_id} đã chuyển sang: ${statusText}`);
            }

            updateStatusCounts() {
                // Refresh status counts by making an AJAX call
                if (!window.location.pathname.includes('/admin/orders')) {
                    return;
                }

                fetch('/admin/orders/counts', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update each tab count
                        Object.keys(data.counts).forEach(status => {
                            const count = data.counts[status];
                            const statusTabElement = document.querySelector(`[data-status="${status}"]`);
                            if (statusTabElement) {
                                const tabCountElement = statusTabElement.querySelector('.tab-count');
                                if (tabCountElement) {
                                    tabCountElement.textContent = count;
                                    console.log(`✅ Updated ${status} count to ${count}`);
                                }
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error updating status counts:', error);
                });
            }

            updateStatusCountsForStatusChange(oldStatus, newStatus) {
                // Update counts for status change by adjusting the specific tabs
                if (!window.location.pathname.includes('/admin/orders')) {
                    return;
                }

                console.log('🔄 Updating status counts:', { oldStatus, newStatus });

                // Status text mapping
                const statusTexts = {
                    'awaiting_confirmation': 'Chờ xác nhận',
                    'confirmed': 'Đã xác nhận',
                    'awaiting_driver': 'Chờ tài xế',
                    'driver_confirmed': 'Tài xế đã xác nhận',
                    'waiting_driver_pick_up': 'Chờ tài xế lấy hàng',
                    'driver_picked_up': 'Tài xế đã lấy hàng',
                    'in_transit': 'Đang giao',
                    'delivered': 'Đã giao',
                    'item_received': 'Đã nhận hàng',
                    'cancelled': 'Đã hủy',
                    'refunded': 'Đã hoàn tiền',
                    'payment_failed': 'Thanh toán thất bại',
                    'payment_received': 'Đã thanh toán',
                    'order_failed': 'Đơn hàng thất bại',
                    'unpaid': 'Chưa thanh toán'
                };

                // Decrease count for old status tab
                if (oldStatus && statusTexts[oldStatus]) {
                    const oldStatusTabElement = document.querySelector(`[data-status="${oldStatus}"]`);
                    if (oldStatusTabElement) {
                        const oldStatusTab = oldStatusTabElement.querySelector('.tab-count');
                        if (oldStatusTab) {
                            const currentCount = parseInt(oldStatusTab.textContent) || 0;
                            const newCount = Math.max(0, currentCount - 1);
                            oldStatusTab.textContent = newCount;
                            console.log(`📉 Decreased ${oldStatus} count to ${newCount}`);
                        }
                    }
                }

                // Increase count for new status tab
                if (newStatus && statusTexts[newStatus]) {
                    const newStatusTabElement = document.querySelector(`[data-status="${newStatus}"]`);
                    if (newStatusTabElement) {
                        const newStatusTab = newStatusTabElement.querySelector('.tab-count');
                        if (newStatusTab) {
                            const currentCount = parseInt(newStatusTab.textContent) || 0;
                            const newCount = currentCount + 1;
                            newStatusTab.textContent = newCount;
                            console.log(`📈 Increased ${newStatus} count to ${newCount}`);
                        }
                    }
                }

                // Add visual feedback for the updated tabs
                [oldStatus, newStatus].forEach(status => {
                    if (status && statusTexts[status]) {
                        const tabElement = document.querySelector(`[data-status="${status}"]`);
                        if (tabElement) {
                            tabElement.style.backgroundColor = '#e0f2fe';
                            setTimeout(() => {
                                tabElement.style.backgroundColor = '';
                                tabElement.style.transition = 'background-color 0.5s ease';
                            }, 1000);
                        }
                    }
                });
            }

            startNotificationLoop() {
                // Dừng interval cũ nếu có
                if (this.notificationInterval) {
                    clearInterval(this.notificationInterval);
                }
                
                // Hiển thị thông báo đầu tiên với tên chi nhánh
                let branchName = 'chi nhánh';
                if (this.latestOrder?.branch_name) {
                    branchName = this.latestOrder.branch_name;
                } else if (this.latestOrder?.branch?.name) {
                    branchName = this.latestOrder.branch.name;
                }
                this.showNotification('Đơn hàng mới', `Có đơn hàng mới từ ${branchName}`);
                
                // Lặp lại thông báo mỗi 6 giây
                this.notificationInterval = setInterval(() => {
                    if (this.hasNewOrder) {
                        let branchName = 'chi nhánh';
                        if (this.latestOrder?.branch_name) {
                            branchName = this.latestOrder.branch_name;
                        } else if (this.latestOrder?.branch?.name) {
                            branchName = this.latestOrder.branch.name;
                        }
                        this.showNotification('Đơn hàng mới', `Có đơn hàng mới từ ${branchName}`);
                    }
                }, 6000);
            }

            stopNotificationLoop() {
                this.hasNewOrder = false;
                if (this.notificationInterval) {
                    clearInterval(this.notificationInterval);
                    this.notificationInterval = null;
                }
            }

            handleNewOrder(data) {
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
                
                // Save latest order info with branch_name
                this.latestOrder = {
                    ...data.order,
                    branch_name: data.order.branch?.name || 'Chi nhánh không xác định'
                };
                
                // Add notification to admin bell
                this.addNotificationToBell(data.order);
                
                // Clean up old entries (older than 5 minutes)
                for (const [id, timestamp] of this.processedOrders.entries()) {
                    if (now - timestamp > 300000) { // 5 minutes
                        this.processedOrders.delete(id);
                    }
                }
                
                // Add order row to table
                this.addOrderRow(data.order);
                
                // Update order count
                this.updateOrderCount(data.order.status);
            }

            updateOrderCount(orderStatus = 'awaiting_confirmation') {
                // Chỉ cập nhật count nếu đang ở trang quản lý đơn hàng
                if (!window.location.pathname.includes('/admin/orders')) {
                    return;
                }
                
                console.log('🔄 Updating order count for status:', orderStatus);
                
                // Cập nhật tab "all" - tìm tab có data-status rỗng (tab "Tất cả")
                const allTab = document.querySelector('[data-status=""]');
                if (allTab) {
                    const allTabCount = allTab.querySelector('.tab-count');
                    if (allTabCount) {
                        const currentCount = parseInt(allTabCount.textContent) || 0;
                        const newCount = currentCount + 1;
                        allTabCount.textContent = newCount;
                        console.log(`📈 Updated "Tất cả" count to ${newCount}`);
                    }
                }
                
                // Cập nhật tab tương ứng với status của đơn hàng
                const statusTabElement = document.querySelector(`[data-status="${orderStatus}"]`);
                if (statusTabElement) {
                    const statusTabCount = statusTabElement.querySelector('.tab-count');
                    if (statusTabCount) {
                        const currentCount = parseInt(statusTabCount.textContent) || 0;
                        const newCount = currentCount + 1;
                        statusTabCount.textContent = newCount;
                        console.log(`📈 Updated ${orderStatus} count to ${newCount}`);
                    }
                }
            }

            addOrderRow(order) {
                // Chỉ thêm row nếu đang ở trang quản lý đơn hàng
                if (!window.location.pathname.includes('/admin/orders')) {
                    return;
                }
                
                // Lấy status tab hiện tại
                const urlParams = new URLSearchParams(window.location.search);
                const currentStatus = urlParams.get('status') || '';
                
                // Map trạng thái đơn hàng với tab
                const statusTabMap = {
                    'awaiting_confirmation': 'awaiting_confirmation',
                    'confirmed': 'confirmed',
                    'awaiting_driver': 'awaiting_driver',
                    'driver_confirmed': 'driver_confirmed',
                    'waiting_driver_pick_up': 'waiting_driver_pick_up',
                    'driver_picked_up': 'driver_picked_up',
                    'in_transit': 'in_transit',
                    'delivered': 'delivered',
                    'item_received': 'item_received',
                    'cancelled': 'cancelled',
                    'refunded': 'refunded',
                    'payment_failed': 'payment_failed',
                    'payment_received': 'payment_received',
                    'order_failed': 'order_failed'
                };
                const orderTab = statusTabMap[order.status] || '';
                
                // Chỉ thêm row nếu đơn hàng thuộc tab hiện tại hoặc tab 'all'
                if (currentStatus !== '' && currentStatus !== orderTab) {
                    return;
                }
                
                // Gọi AJAX lấy HTML partial row từ server
                fetch(`/admin/orders/${order.id}/row`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.text();
                    })
                    .then(html => {
                        // Chỉ tìm tbody trong table đơn hàng, không phải tất cả tbody
                        const ordersTable = document.querySelector('#ordersTable tbody') || 
                                          document.querySelector('.orders-table tbody') ||
                                          document.querySelector('table[data-table="orders"] tbody');
                        const tableBody = ordersTable || document.querySelector('tbody');
                        if (!tableBody) {
                            return;
                        }
                        
                        // Check for duplicate row
                        if (tableBody.querySelector(`[data-order-id="${order.id}"]`)) {
                            return;
                        }
                        
                        // Tạo element từ HTML - sử dụng table wrapper để parse tr đúng cách
                        const tableWrapper = document.createElement('table');
                        tableWrapper.innerHTML = html.trim();
                        
                        // Tìm tr element trong tableWrapper
                        let row = tableWrapper.querySelector('tr');
                        
                        // Nếu không tìm thấy bằng querySelector, thử cách khác
                        if (!row) {
                            // Tìm trong tất cả children
                            for (let child of tableWrapper.children) {
                                if (child.tagName === 'TR') {
                                    row = child;
                                    break;
                                }
                            }
                        }
                        
                        // Nếu vẫn không tìm thấy, thử parse trực tiếp
                        if (!row) {
                            try {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(`<table>${html.trim()}</table>`, 'text/html');
                                row = doc.querySelector('tr');
                            } catch (e) {
                                // Fallback: Tạo row trực tiếp bằng JavaScript
                                const manualRow = this.createOrderRowManually(order);
                                if (manualRow) {
                                    if (tableBody.firstChild) {
                                        tableBody.insertBefore(manualRow, tableBody.firstChild);
                                    } else {
                                        tableBody.appendChild(manualRow);
                                    }
                                }
                                return;
                            }
                        }
                        
                        // Kiểm tra row có tồn tại không
                        if (!row) {
                            // Fallback: Tạo row trực tiếp bằng JavaScript
                            const manualRow = this.createOrderRowManually(order);
                            if (manualRow) {
                                if (tableBody.firstChild) {
                                    tableBody.insertBefore(manualRow, tableBody.firstChild);
                                } else {
                                    tableBody.appendChild(manualRow);
                                }
                            }
                            return;
                        }
                        
                        // Thêm vào đầu bảng
                        if (tableBody.firstChild) {
                            tableBody.insertBefore(row, tableBody.firstChild);
                        } else {
                            tableBody.appendChild(row);
                        }
                        
                        // Thêm hiệu ứng highlight
                        if (row && row.style) {
                            row.style.backgroundColor = '#fef3c7';
                            setTimeout(() => {
                                if (row && row.style) {
                                    row.style.backgroundColor = '';
                                    row.style.transition = 'background-color 0.5s ease';
                                }
                            }, 2000);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching order row:', error);
                    });
            }

            createOrderRowManually(order) {
                const row = document.createElement('tr');
                row.className = 'border-b';
                row.setAttribute('data-order-id', order.id);
                
                // Map status text
                const statusMap = {
                    'awaiting_confirmation': 'Chờ xác nhận',
                    'confirmed': 'Đã xác nhận',
                    'awaiting_driver': 'Chờ tài xế nhận đơn',
                    'driver_confirmed': 'Tài xế đã xác nhận đơn',
                    'waiting_driver_pick_up': 'Tài xế đang chờ đơn',
                    'driver_picked_up': 'Tài xế đã nhận đơn',
                    'in_transit': 'Đang trong quá trình giao hàng',
                    'delivered': 'Đã giao thành công',
                    'item_received': 'Khách hàng đã nhận hàng',
                    'cancelled': 'Đã bị hủy',
                    'refunded': 'Đã được hoàn tiền',
                    'payment_failed': 'Thanh toán thất bại',
                    'payment_received': 'Thanh toán đã nhận',
                    'order_failed': 'Đơn hàng đã thất bại'
                };
                
                const statusText = statusMap[order.status] || 'Không xác định';
                const customerName = order.customer ? order.customer.name : 'Khách hàng';
                const customerPhone = order.customer ? order.customer.phone : '';
                const branchName = order.branch ? order.branch.name : '';
                const customerAvatar = order.customer && order.customer.avatar_url ? order.customer.avatar_url : '/images/default-avatar.png';
                
                // Đảm bảo order.id tồn tại
                const orderId = order.id || order.order_id || 'unknown';
                
                row.innerHTML = `
                    <td class="py-3 px-4 font-medium">#${order.order_code}</td>
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                            <img src="${customerAvatar}" alt="avatar" class="w-8 h-8 rounded-full border object-cover">
                            <div>
                                <span class="font-medium">${customerName}</span><br>
                                <span class="text-xs text-gray-400">${customerPhone}</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4">${branchName}</td>
                    <td class="py-3 px-4 text-right font-bold">${new Intl.NumberFormat('vi-VN').format(order.total_amount)}đ</td>
                    <td class="py-3 px-4">
                        <span class="order-status-badge ${order.status}">
                            ${statusText}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="text-sm text-gray-900">${new Date(order.created_at).toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'})}</div>
                        <div class="text-xs text-gray-500">${new Date(order.created_at).toLocaleDateString('vi-VN')}</div>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <div class="flex gap-2 items-center justify-center">
                            <a href="/admin/orders/show/${orderId}" class="flex items-center gap-1 px-3 py-1 border border-gray-300 rounded-lg text-primary bg-white hover:bg-gray-100 transition text-sm font-medium">
                                <i class="fa fa-eye"></i> Chi tiết
                            </a>
                            ${order.status === 'awaiting_confirmation' ? `
                            <div class="relative">
                                <button class="px-3 py-1 border border-gray-300 rounded-lg bg-white text-gray-700 hover:bg-gray-100 transition text-sm font-medium flex items-center gap-1">Cập nhật <i class="fa fa-chevron-down text-xs"></i></button>
                            </div>
                            ` : ''}
                        </div>
                    </td>
                `;
                
                return row;
            }

            addNotificationToBell(order) {
                const notificationList = document.getElementById('admin-notification-list');
                if (!notificationList) return;

                // Gọi AJAX lấy partial HTML từ server
                fetch(`/admin/notifications/item/${order.id}`)
                    .then(response => response.text())
                    .then(html => {
                        // Remove empty state nếu có
                        const emptyState = notificationList.querySelector('.text-center.text-xs.text-muted-foreground.py-4');
                        if (emptyState) emptyState.remove();

                        notificationList.insertAdjacentHTML('afterbegin', html);

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
                    });
            }

            updateNotificationCount(increment = 0) {
                // Update the notification count badge
                const countElements = document.querySelectorAll('.notification-unread-count');
                countElements.forEach(element => {
                    const currentCount = parseInt(element.textContent) || 0;
                    const newCount = currentCount + increment;
                    element.textContent = newCount;
                    
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

                // Update the count text in the dropdown
                const countTextElements = document.querySelectorAll('.notification-unread-count');
                countTextElements.forEach(element => {
                    if (!element.closest('.absolute')) { // Not the badge
                        const currentCount = parseInt(element.textContent) || 0;
                        const newCount = currentCount + increment;
                        element.textContent = newCount;
                    }
                });
            }

            bindEvents() {
                // Dừng thông báo khi click vào trang
                document.addEventListener('click', () => {
                    this.stopNotificationLoop();
                });

                // Dừng thông báo khi chuyển trang
                window.addEventListener('beforeunload', () => {
                    this.stopNotificationLoop();
                });

                // Dừng thông báo khi focus vào trang
                window.addEventListener('focus', () => {
                    this.stopNotificationLoop();
                });
            }
        }
        window.AdminOrdersRealtime = AdminOrdersRealtime;
    }

    // Initialize immediately or when DOM is loaded
    function initializeAdminOrdersRealtime() {
        console.log('🚀 Initializing AdminOrdersRealtime...');
        window.adminOrdersRealtime = new AdminOrdersRealtime();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeAdminOrdersRealtime);
    } else {
        // DOM is already loaded
        initializeAdminOrdersRealtime();
    }
}