if (window.ordersRealtimeInitialized) {
    // Đã khởi tạo, không làm gì nữa
} else {
    window.ordersRealtimeInitialized = true;

    // Đảm bảo chỉ khai báo class SimpleBranchOrdersRealtime nếu chưa tồn tại
    if (typeof window.SimpleBranchOrdersRealtime === 'undefined') {
        class SimpleBranchOrdersRealtime {
            constructor() {
                console.log('🚀 Khởi tạo SimpleBranchOrdersRealtime');
                this.branchId = window.branchId;
                this.pusherKey = window.pusherKey;
                this.pusherCluster = window.pusherCluster;
                console.log('📋 Config:', {
                    branchId: this.branchId,
                    pusherKey: this.pusherKey,
                    pusherCluster: this.pusherCluster
                });
                this.pusher = null;
                this.channel = null;
                this.processedOrders = new Map(); // Để tránh duplicate với timestamp
                this.notificationInterval = null; // Để lặp lại thông báo
                this.hasNewOrder = false; // Flag để biết có đơn hàng mới

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
                        applicationServerKey: this.urlBase64ToUint8Array(this.pusherKey) // Thay bằng VAPID key thực
                    });

                    // Gửi subscription lên server để lưu
                    await fetch('/branch/notification-subscription', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({
                            subscription: subscription,
                            branch_id: this.branchId
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
                console.log('🔧 Khởi tạo Pusher...');
                if (!this.pusherKey || !this.pusherCluster) {
                    console.log('❌ Thiếu pusherKey hoặc pusherCluster');
                    return;
                }

                try {
                    this.pusher = new Pusher(this.pusherKey, {
                        cluster: this.pusherCluster,
                        encrypted: true,
                        authEndpoint: '/broadcasting/auth'
                    });

                    console.log('✅ Pusher đã khởi tạo thành công');

                    // Connection events
                    this.pusher.connection.bind('connected', () => {
                        console.log('🔗 Pusher connected successfully');
                    });

                    this.pusher.connection.bind('error', (err) => {
                        console.log('❌ Pusher connection error:', err);
                        this.showNotification('Lỗi kết nối', 'Không thể kết nối Pusher');
                    });

                    this.pusher.connection.bind('disconnected', () => {
                        console.log('🔌 Pusher disconnected');
                        this.showNotification('Mất kết nối', 'Kết nối Pusher đã bị ngắt');
                    });

                    // Subscribe to public channel
                    this.subscribeToPublicChannel();

                } catch (error) {
                    console.log('❌ Lỗi khởi tạo Pusher:', error);
                    this.showNotification('Lỗi khởi tạo', 'Không thể khởi tạo Pusher');
                }
            }

            subscribeToPublicChannel() {
                console.log('📡 Đăng ký kênh branch-orders-channel...');
                this.publicChannel = this.pusher.subscribe('branch-orders-channel');

                this.publicChannel.bind('pusher:subscription_succeeded', () => {
                    // Kênh đã kết nối thành công
                });

                this.publicChannel.bind('pusher:subscription_error', (status) => {
                    this.showNotification('Lỗi kết nối', 'Không thể kết nối kênh thông báo');
                });

                this.publicChannel.bind('new-order-received', (data) => {
                    // Chỉ xử lý nếu đơn hàng thuộc về branch hiện tại
                    if (data.branch_id == this.branchId) {
                        this.hasNewOrder = true;
                        this.startNotificationLoop();
                        this.handleNewOrder(data);
                    }
                });

                // Lắng nghe sự kiện cập nhật trạng thái đơn hàng
                this.publicChannel.bind('order-status-updated', (data) => {
                    if (data.branch_id == this.branchId) {
                        this.handleOrderStatusUpdate(data);
                    }
                });

                // Lắng nghe sự kiện khách hàng hủy đơn hàng
                this.publicChannel.bind('order-cancelled-by-customer', (data) => {
                    if (data.branch_id == this.branchId) {
                        this.handleOrderCancelledByCustomer(data);
                    }
                });
            }

            startNotificationLoop() {
                // Dừng interval cũ nếu có
                if (this.notificationInterval) {
                    clearInterval(this.notificationInterval);
                }

                // Hiển thị thông báo đầu tiên
                this.showNotification('Đơn hàng mới', 'Bạn có đơn hàng mới');

                // Lặp lại thông báo mỗi 5 giây
                this.notificationInterval = setInterval(() => {
                    if (this.hasNewOrder) {
                        this.showNotification('Đơn hàng mới', 'Bạn có đơn hàng mới');
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
                console.log('🆕 handleNewOrder called with data:', data);

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

                // Add order card to grid
                this.addOrderCard(data.order);

                // Update order count
                this.updateOrderCount(data.order.status);
            }

            updateOrderCount(orderStatus = 'awaiting_confirmation') {
                // Luôn cập nhật tab "all" vì đơn hàng mới thuộc về tất cả
                const allTab = document.querySelector('[data-status="all"]');
                if (allTab) {
                    const currentText = allTab.textContent;
                    const match = currentText.match(/Tất cả \((\d+)\)/);
                    if (match) {
                        const currentCount = parseInt(match[1]) || 0;
                        const newCount = currentCount + 1;
                        allTab.textContent = `Tất cả (${newCount})`;
                    }
                }

                // Cập nhật tab tương ứng với status của đơn hàng
                const statusTab = document.querySelector(`[data-status="${orderStatus}"]`);
                if (statusTab) {
                    const currentText = statusTab.textContent;
                    const statusTexts = {
                        'awaiting_confirmation': 'Chờ xác nhận',
                        'awaiting_driver': 'Chờ tài xế',
                        'in_transit': 'Đang giao',
                        'delivered': 'Đã giao',
                        'cancelled': 'Đã hủy',
                        'refunded': 'Đã hoàn tiền'
                    };

                    const statusText = statusTexts[orderStatus] || orderStatus;
                    const regex = new RegExp(`${statusText} \\((\\d+)\\)`);
                    const match = currentText.match(regex);

                    if (match) {
                        const currentCount = parseInt(match[1]) || 0;
                        const newCount = currentCount + 1;
                        statusTab.textContent = `${statusText} (${newCount})`;
                    }
                }
            }

            handleOrderStatusUpdate(data) {
                console.log('🔄 Nhận được cập nhật trạng thái:', data);
                const orderId = data.order.id;
                const newStatus = data.order.status;
                const orderCard = document.querySelector(`[data-order-id="${orderId}"]`);

                console.log('🔍 Tìm order card:', orderId, orderCard ? 'Tìm thấy' : 'Không tìm thấy');
                if (!orderCard) {
                    console.log('❌ Không tìm thấy order card với ID:', orderId);
                    return; // Card không tồn tại trên trang hiện tại
                }

                // Cập nhật trạng thái badge
                this.updateOrderCardStatus(orderCard, newStatus);

                // Cập nhật nút action
                this.updateOrderCardActions(orderCard, newStatus);

                // Hiển thị thông báo nếu cần
                if (newStatus === 'awaiting_driver') {
                    this.showNotification('Cập nhật đơn hàng', 'Đã tìm được tài xế cho đơn hàng');
                }
            }

            handleOrderCancelledByCustomer(data) {
                console.log('❌ Xử lý đơn hàng bị hủy bởi khách hàng:', data);
                const orderId = data.order.id;
                const orderCard = document.querySelector(`[data-order-id="${orderId}"]`);

                if (!orderCard) {
                    console.log('❌ Không tìm thấy order card với ID:', orderId);
                    return;
                }

                // Cập nhật trạng thái thành 'cancelled'
                this.updateOrderCardStatus(orderCard, 'cancelled');

                // Cập nhật nút action
                this.updateOrderCardActions(orderCard, 'cancelled');

                // Hiển thị thông báo
                this.showNotification('Đơn hàng bị hủy', `Khách hàng đã hủy đơn hàng #${data.order.order_code || orderId}`);

                // Cập nhật số lượng đơn hàng trong các tab
                this.updateOrderCountAfterCancel();
            }

            updateOrderCardStatus(orderCard, newStatus) {
                console.log('🎨 Cập nhật status card:', newStatus);
                // Tìm cột trạng thái trong table (cột thứ 4, index 3)
                const statusCell = orderCard.children[3];

                console.log('🎯 Status cell:', statusCell ? 'Tìm thấy' : 'Không tìm thấy');
                if (!statusCell) {
                    console.log('❌ Không tìm thấy status cell');
                    return;
                }

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
                    'unpaid': 'Chưa thanh toán'
                };

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
                    'unpaid': 'bg-orange-400 text-white'
                };

                const statusText = statusTexts[newStatus] || newStatus;
                const statusColor = statusColors[newStatus] || 'bg-gray-100 text-gray-700';

                if (newStatus === 'confirmed') {
                    // Hiển thị trạng thái "Đang tìm tài xế" với spinner
                    statusCell.innerHTML = `
                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-md bg-blue-100 text-blue-700">
                            <svg class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M12 2a10 10 0 0 1 10 10h-4a6 6 0 0 0-6-6V2z"></path>
                            </svg>
                            Đang tìm tài xế
                        </span>
                    `;
                } else {
                    // Hiển thị trạng thái bình thường
                    statusCell.innerHTML = `
                        <span class="px-2 py-1 text-xs font-medium rounded-md status-badge ${statusColor}">
                            ${statusText}
                        </span>
                    `;
                }
            }

            updateOrderCardActions(orderCard, newStatus) {
                // Tìm cột thao tác trong table (cột cuối cùng)
                const actionsCell = orderCard.children[orderCard.children.length - 1];
                const actionsContainer = actionsCell ? actionsCell.querySelector('.flex.gap-2') : null;

                if (!actionsContainer) return;

                const orderId = orderCard.getAttribute('data-order-id');

                if (newStatus === 'awaiting_confirmation') {
                    actionsContainer.innerHTML = `
                        <button data-quick-action="confirm" data-order-id="${orderId}" class="px-3 py-1 text-xs rounded-md bg-black text-white hover:bg-gray-800 confirm-btn">
                            Xác nhận
                        </button>
                        <button data-quick-action="cancel" data-order-id="${orderId}" class="px-3 py-1 text-xs rounded-md bg-red-500 text-white hover:bg-red-600">
                            Hủy
                        </button>
                        <a href="/branch/orders/${orderId}" class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100">Chi tiết</a>
                    `;
                } else if (newStatus === 'confirmed') {
                    actionsContainer.innerHTML = `
                        <a href="/branch/orders/${orderId}" class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100">Chi tiết</a>
                    `;
                } else {
                    actionsContainer.innerHTML = `
                        <a href="/branch/orders/${orderId}" class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100">Chi tiết</a>
                    `;
                }

                // Rebind events cho các nút mới
                this.bindEvents();
            }

            addOrderCard(order) {
                // Lấy status tab hiện tại
                const urlParams = new URLSearchParams(window.location.search);
                const currentStatus = urlParams.get('status') || 'all';
                // Map trạng thái đơn hàng với tab
                const statusTabMap = {
                    'awaiting_confirmation': 'awaiting_confirmation',
                    'confirmed': 'awaiting_driver',
                    'awaiting_driver': 'awaiting_driver',
                    'driver_assigned': 'awaiting_driver',
                    'driver_confirmed': 'awaiting_driver',
                    'driver_picked_up': 'awaiting_driver',
                    'in_transit': 'in_transit',
                    'delivered': 'delivered',
                    'cancelled': 'cancelled',
                    'refunded': 'refunded'
                };
                const orderTab = statusTabMap[order.status] || 'all';
                // Chỉ thêm card nếu đơn hàng thuộc tab hiện tại hoặc tab 'all'
                if (currentStatus !== 'all' && currentStatus !== orderTab) return;

                const ordersGrid = document.getElementById('ordersGrid');
                if (!ordersGrid) {
                    console.error('❌ ordersGrid not found!');
                    return;
                }

                // Tìm tbody trong table
                const tbody = ordersGrid.querySelector('tbody');
                if (!tbody) {
                    console.error('❌ tbody not found!');
                    return;
                }

                // Check for duplicate card
                if (tbody.querySelector(`[data-order-id="${order.id}"]`)) {
                    return;
                }

                // Xóa empty state nếu có
                const emptyState = tbody.querySelector('tr td[colspan]');
                if (emptyState) {
                    emptyState.closest('tr').remove();
                }

                // Create order row HTML directly
                const orderRow = this.createOrderRowHTML(order);
                if (!orderRow) {
                    console.error('❌ Failed to create order row HTML');
                    return;
                }

                // Thêm animation class
                orderRow.style.opacity = '0';
                orderRow.style.transform = 'translateY(-10px)';

                // Thêm vào đầu tbody
                tbody.insertBefore(orderRow, tbody.firstChild);

                // Animate in
                setTimeout(() => {
                    orderRow.style.transition = 'all 0.3s ease';
                    orderRow.style.opacity = '1';
                    orderRow.style.transform = 'translateY(0)';
                }, 10);
            }

            createOrderRowHTML(order) {
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
                    'unpaid': 'bg-orange-400 text-white'
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
                    'unpaid': 'Chưa thanh toán'
                };

                // Safe date formatting - prioritize order_date over created_at
                let createdAt = '';
                let orderDateForAttribute = '';
                try {
                    const dateSource = order.order_date || order.created_at;
                    const date = new Date(dateSource);
                    if (!isNaN(date.getTime())) {
                        createdAt = date.toLocaleString('vi-VN');
                        orderDateForAttribute = date.toISOString();
                    } else {
                        createdAt = new Date().toLocaleString('vi-VN'); // fallback to current date
                        orderDateForAttribute = new Date().toISOString();
                    }
                } catch (e) {
                    createdAt = new Date().toLocaleString('vi-VN'); // fallback to current date
                    orderDateForAttribute = new Date().toISOString();
                }

                const tr = document.createElement('tr');
                tr.className = 'order-row bg-white border-b border-gray-200 hover:bg-gray-50';
                tr.setAttribute('data-order-id', order.id);
                tr.setAttribute('data-order-date', orderDateForAttribute);

                const customerName = order.customer_name || order.customerName || 'Khách hàng';
                const customerPhone = order.customer_phone || order.customerPhone || 'Chưa có SĐT';
                const customerInitial = customerName.charAt(0).toUpperCase();
                const statusClass = statusColors[order.status] || 'bg-gray-300 text-gray-700';
                const statusText = statusTexts[order.status] || order.status;
                const orderCode = order.order_code || order.code || order.id;
                const totalAmount = order.total_amount || 0;

                // Payment method and status
                const paymentMethod = order.payment?.payment_method || 'cod';
                const paymentStatus = order.payment?.payment_status || 'pending';

                // Get total quantity from items_count or calculate from orderItems
                const totalQuantity = order.items_count ||
                    (order.order_items || order.orderItems || []).reduce((sum, item) => sum + (item.quantity || 0), 0);

                // Payment method display
                let paymentMethodHtml = '';
                if (paymentMethod.toLowerCase() === 'cod') {
                    paymentMethodHtml = '<span class="inline-block px-2 py-0.5 rounded bg-green-700 text-white text-xs font-semibold">COD</span>';
                } else if (paymentMethod.toLowerCase() === 'vnpay') {
                    paymentMethodHtml = `<span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-blue-100 text-blue-800 text-xs font-semibold">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 16" style="height:1em;width:auto;display:inline;vertical-align:middle;" aria-label="VNPAY Icon">
                            <text x="0" y="12" font-size="12" font-family="Arial, Helvetica, sans-serif" font-weight="bold" fill="#e30613">VN</text>
                            <text x="18" y="12" font-size="12" font-family="Arial, Helvetica, sans-serif" font-weight="bold" fill="#0072bc">PAY</text>
                        </svg>
                    </span>`;
                } else if (paymentMethod.toLowerCase() === 'balance') {
                    paymentMethodHtml = '<span class="inline-block px-2 py-1 rounded bg-purple-100 text-purple-700 text-xs font-semibold">Số dư</span>';
                }

                // Payment status colors and text
                const paymentStatusColors = {
                    'pending': 'bg-yellow-100 text-yellow-800',
                    'completed': 'bg-green-100 text-green-800',
                    'failed': 'bg-red-100 text-red-800',
                    'refunded': 'bg-pink-100 text-pink-800'
                };
                const paymentStatusText = {
                    'pending': 'Chờ xử lý',
                    'completed': 'Thành công',
                    'failed': 'Thất bại',
                    'refunded': 'Đã hoàn tiền'
                };

                const paymentStatusClass = paymentStatusColors[paymentStatus] || 'bg-gray-100 text-gray-800';
                const paymentStatusLabel = paymentStatusText[paymentStatus] || paymentStatus;

                // Format time
                let timeHtml = '';
                try {
                    const orderDate = new Date(order.order_date || order.created_at);
                    if (!isNaN(orderDate.getTime())) {
                        const timeStr = orderDate.toLocaleTimeString('vi-VN', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: false
                        });
                        const dateStr = orderDate.toLocaleDateString('vi-VN', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric'
                        });
                        timeHtml = `<div class="text-sm">
                            <div class="text-gray-900">${timeStr}</div>
                            <div class="text-gray-500">${dateStr}</div>
                        </div>`;
                    } else {
                        timeHtml = '<div class="text-sm text-gray-500">Chưa có thời gian</div>';
                    }
                } catch (e) {
                    timeHtml = '<div class="text-sm text-gray-500">Chưa có thời gian</div>';
                }

                // Status display with special handling for 'confirmed' status
                let statusHtml = '';
                if (order.status === 'confirmed') {
                    statusHtml = `<span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-md bg-blue-100 text-blue-700">
                        <svg class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M12 2a10 10 0 0 1 10 10h-4a6 6 0 0 0-6-6V2z"></path>
                        </svg>
                        Đang tìm tài xế
                    </span>`;
                } else {
                    statusHtml = `<span class="px-2 py-1 text-xs font-medium rounded-md status-badge ${statusClass}">
                        ${statusText}
                    </span>`;
                }

                tr.innerHTML = `
                    <!-- Checkbox -->
                    <td class="px-4 py-3">
                        <input type="checkbox" class="order-checkbox rounded" data-order-id="${order.id}">
                    </td>
                    
                    <!-- Mã đơn hàng -->
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-900">#${orderCode}</span>
                        </div>
                    </td>
                    
                    <!-- Khách hàng -->
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-700 font-bold text-sm">
                                ${customerInitial}
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">${customerName}</div>
                                <div class="text-sm text-gray-500">${customerPhone}</div>
                            </div>
                        </div>
                    </td>
                    
                    <!-- Trạng thái -->
                    <td class="px-4 py-3">
                        ${statusHtml}
                    </td>
                    
                    <!-- Tổng tiền -->
                    <td class="px-4 py-3">
                        <span class="font-semibold text-gray-900">${Number(totalAmount).toLocaleString('vi-VN')}₫</span>
                    </td>
                    
                    <!-- Sản phẩm -->
                    <td class="px-4 py-3 text-center">
                        <span class="text-gray-700">${totalQuantity}</span>
                    </td>
                    
                    <!-- Thời gian -->
                    <td class="px-4 py-3">
                        ${timeHtml}
                    </td>
                    
                    <!-- Thanh toán -->
                    <td class="px-4 py-3">
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-1">
                                ${paymentMethodHtml}
                            </div>
                            <span class="payment-status-badge inline-block px-2 py-0.5 rounded text-xs font-semibold ${paymentStatusClass}">
                                ${paymentStatusLabel}
                            </span>
                        </div>
                    </td>
                    
                    <!-- Thao tác -->
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            ${order.status === 'awaiting_confirmation' ? `
                                <button data-quick-action="confirm" data-order-id="${order.id}" class="px-3 py-1 text-xs rounded-md bg-black text-white hover:bg-gray-800 confirm-btn">
                                    Xác nhận
                                </button>
                                <button data-quick-action="cancel" data-order-id="${order.id}" class="px-3 py-1 text-xs rounded-md bg-red-500 text-white hover:bg-red-600">
                                    Hủy
                                </button>
                            ` : order.status === 'confirmed' ? `
                                <button type="button" class="px-3 py-1 text-xs rounded-md bg-gray-200 text-gray-700 cursor-default" disabled>
                                    Đang tìm tài xế
                                </button>
                            ` : ''}
                            <a href="/branch/orders/${order.id}" class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100">Chi tiết</a>
                        </div>
                    </td>
                `;

                return tr;
            }

            bindEvents() {
                // Đảm bảo chỉ gán event click 1 lần duy nhất
                if (!window.ordersRealtimeClickBound) {
                    document.addEventListener('click', (e) => {
                        if (e.target.matches('[data-quick-action="confirm"]')) {
                            e.preventDefault();
                            if (e.target.disabled) {
                                console.warn('Nút xác nhận đã disabled nhưng vẫn bị click!', e.target);
                                return;
                            }
                            console.log('CLICK CONFIRM', e.target, new Date().toISOString());
                            console.trace();
                            const orderId = e.target.getAttribute('data-order-id');
                            this.confirmOrder(orderId);
                        }
                        if (e.target.matches('[data-quick-action="cancel"]')) {
                            e.preventDefault();
                            const orderId = e.target.getAttribute('data-order-id');
                            // Gọi hàm cancelOrder(orderId) nếu có
                        }
                    });
                    window.ordersRealtimeClickBound = true;
                }

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

            confirmOrder(orderId) {
                // Disable tất cả nút xác nhận cho order này NGAY LẬP TỨC
                document.querySelectorAll(`[data-quick-action="confirm"][data-order-id="${orderId}"]`).forEach(btn => {
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                });

                // Chỉ ẩn đơn hàng nếu đang ở tab "Chờ xác nhận", không xóa hoàn toàn
                const currentTab = new URLSearchParams(window.location.search).get('status');
                if (currentTab === 'awaiting_confirmation') {
                    document.querySelectorAll(`tr[data-order-id="${orderId}"]`).forEach(row => {
                        row.style.display = 'none';
                    });
                }

                fetch(`/branch/orders/${orderId}/confirm`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (response.ok && data.success) {
                            // Lấy order_code từ response, fallback về orderId nếu không có
                            const orderCode = data.order_code;
                            const successMsg = `Xác nhận đơn hàng ${orderCode} thành công`;
                            // Hiển thị toast thành công (chỉ dùng success)
                            if (window.iziToast) {
                                iziToast.success({
                                    title: 'Thành công',
                                    message: successMsg
                                });
                            } else if (typeof dtmodalShowToast === 'function') {
                                dtmodalShowToast('success', {
                                    title: 'Thành công',
                                    message: successMsg
                                });
                            } else {
                                alert(successMsg);
                            }
                            // Cập nhật số lượng trên status tabs
                            this.updateStatusCountAfterConfirm();
                        } else {
                            // Re-enable button on error
                            document.querySelectorAll(`[data-quick-action="confirm"][data-order-id="${orderId}"]`).forEach(btn => {
                                btn.disabled = false;
                                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                            });

                            // Show order again if hidden
                            if (currentTab === 'awaiting_confirmation') {
                                document.querySelectorAll(`tr[data-order-id="${orderId}"]`).forEach(row => {
                                    row.style.display = '';
                                });
                            }

                            if (typeof dtmodalShowToast === 'function') {
                                dtmodalShowToast('error', {
                                    title: 'Lỗi',
                                    message: data.message || 'Xác nhận đơn hàng lỗi'
                                });
                            } else if (window.iziToast) {
                                iziToast.error({
                                    title: 'Lỗi',
                                    message: data.message || 'Xác nhận đơn hàng lỗi'
                                });
                            } else {
                                alert(data.message || 'Xác nhận đơn hàng lỗi');
                            }
                            console.error('Xác nhận đơn hàng lỗi:', {
                                status: response.status,
                                statusText: response.statusText,
                                data
                            });
                        }
                    })
                    .catch(error => {
                        // Re-enable button on error
                        document.querySelectorAll(`[data-quick-action="confirm"][data-order-id="${orderId}"]`).forEach(btn => {
                            btn.disabled = false;
                            btn.classList.remove('opacity-50', 'cursor-not-allowed');
                        });

                        // Show order again if hidden
                        if (currentTab === 'awaiting_confirmation') {
                            document.querySelectorAll(`tr[data-order-id="${orderId}"]`).forEach(row => {
                                row.style.display = '';
                            });
                        }

                        if (typeof dtmodalShowToast === 'function') {
                            dtmodalShowToast('error', {
                                title: 'Lỗi',
                                message: 'Lỗi xác nhận đơn hàng'
                            });
                        } else if (window.iziToast) {
                            iziToast.error({
                                title: 'Lỗi',
                                message: 'Lỗi xác nhận đơn hàng'
                            });
                        } else {
                            alert('Lỗi xác nhận đơn hàng');
                        }
                        console.error('Xác nhận đơn hàng lỗi:', error);
                    });
            }

            updateStatusCountAfterConfirm() {
                // Giảm số lượng tab "Chờ xác nhận"
                const awaitingTab = document.querySelector('[data-status="awaiting_confirmation"]');
                if (awaitingTab) {
                    const currentText = awaitingTab.textContent;
                    const match = currentText.match(/Chờ xác nhận \((\d+)\)/);
                    if (match) {
                        const currentCount = parseInt(match[1]) || 0;
                        const newCount = Math.max(0, currentCount - 1);
                        awaitingTab.textContent = `Chờ xác nhận (${newCount})`;
                    }
                }

                // Tăng số lượng tab "Chờ tài xế"
                const driverTab = document.querySelector('[data-status="awaiting_driver"]');
                if (driverTab) {
                    const currentText = driverTab.textContent;
                    const match = currentText.match(/Chờ tài xế \((\d+)\)/);
                    if (match) {
                        const currentCount = parseInt(match[1]) || 0;
                        const newCount = currentCount + 1;
                        driverTab.textContent = `Chờ tài xế (${newCount})`;
                    }
                }

                // Tab "Tất cả" không thay đổi vì tổng số đơn hàng không đổi
            }

            updateOrderCountAfterCancel() {
                // Giảm số lượng tab "Chờ xác nhận" (vì đơn hàng bị hủy thường ở trạng thái này)
                const awaitingTab = document.querySelector('[data-status="awaiting_confirmation"]');
                if (awaitingTab) {
                    const currentText = awaitingTab.textContent;
                    const match = currentText.match(/Chờ xác nhận \((\d+)\)/);
                    if (match) {
                        const currentCount = parseInt(match[1]) || 0;
                        const newCount = Math.max(0, currentCount - 1);
                        awaitingTab.textContent = `Chờ xác nhận (${newCount})`;
                    }
                }

                // Tăng số lượng tab "Đã hủy"
                const cancelledTab = document.querySelector('[data-status="cancelled"]');
                if (cancelledTab) {
                    const currentText = cancelledTab.textContent;
                    const match = currentText.match(/Đã hủy \((\d+)\)/);
                    if (match) {
                        const currentCount = parseInt(match[1]) || 0;
                        const newCount = currentCount + 1;
                        cancelledTab.textContent = `Đã hủy (${newCount})`;
                    }
                }

                // Tab "Tất cả" không thay đổi vì tổng số đơn hàng không đổi
            }
        }
        window.SimpleBranchOrdersRealtime = SimpleBranchOrdersRealtime;
    }

    // Initialize when DOM is loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            window.simpleBranchOrdersRealtime = new SimpleBranchOrdersRealtime();
        });
    } else {
        // DOM is already loaded
        window.simpleBranchOrdersRealtime = new SimpleBranchOrdersRealtime();
    }
}