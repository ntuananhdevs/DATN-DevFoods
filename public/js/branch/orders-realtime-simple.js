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
                console.log('📋 Config:', { branchId: this.branchId, pusherKey: this.pusherKey, pusherCluster: this.pusherCluster });
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
                        applicationServerKey: this.urlBase64ToUint8Array('YOUR_VAPID_PUBLIC_KEY') // Thay bằng VAPID key thực
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
                    console.log('✅ Đăng ký kênh thành công');
                });

                this.publicChannel.bind('pusher:subscription_error', (status) => {
                    console.log('❌ Lỗi đăng ký kênh:', status);
                    this.showNotification('Lỗi kết nối', 'Không thể kết nối kênh thông báo');
                });

                this.publicChannel.bind('new-order-received', (data) => {
                    console.log('📦 Nhận được sự kiện new-order-received:', data);
                    // Chỉ xử lý nếu đơn hàng thuộc về branch hiện tại
                    if (data.branch_id == this.branchId) {
                        console.log('✅ Đơn hàng thuộc branch hiện tại, xử lý...');
                        this.hasNewOrder = true;
                        this.startNotificationLoop();
                        this.handleNewOrder(data);
                    } else {
                        console.log('❌ Đơn hàng không thuộc branch hiện tại, bỏ qua');
                    }
                });

                // Lắng nghe sự kiện cập nhật trạng thái đơn hàng
                this.publicChannel.bind('order-status-updated', (data) => {
                    console.log('🔄 Nhận được sự kiện order-status-updated:', data);
                    if (data.branch_id == this.branchId) {
                        console.log('✅ Cập nhật trạng thái cho branch hiện tại');
                        this.handleOrderStatusUpdate(data);
                    } else {
                        console.log('❌ Cập nhật trạng thái không thuộc branch hiện tại, bỏ qua');
                    }
                });

                // Lắng nghe sự kiện khách hàng hủy đơn hàng
                this.publicChannel.bind('order-cancelled-by-customer', (data) => {
                    console.log('❌ Nhận được sự kiện order-cancelled-by-customer:', data);
                    if (data.branch_id == this.branchId) {
                        console.log('✅ Đơn hàng bị hủy thuộc branch hiện tại, xử lý...');
                        this.handleOrderCancelledByCustomer(data);
                    } else {
                        console.log('❌ Đơn hàng bị hủy không thuộc branch hiện tại, bỏ qua');
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
                const statusBadge = orderCard.querySelector('.status-badge');
                const statusContainer = orderCard.querySelector('.order-status-container');

                console.log('🎯 Status container:', statusContainer ? 'Tìm thấy' : 'Không tìm thấy');
                if (!statusContainer) {
                    console.log('❌ Không tìm thấy status container');
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
                    // Hiển thị trạng thái bình thường
                    statusContainer.innerHTML = `
                        <span class="px-2 py-1 text-xs font-medium rounded-md status-badge ${statusColor}">
                            ${statusText}
                        </span>
                    `;
                }
            }

            updateOrderCardActions(orderCard, newStatus) {
                const actionsContainer = orderCard.querySelector('.absolute.left-0.bottom-0 .flex.gap-2');

                if (!actionsContainer) return;

                const orderId = orderCard.getAttribute('data-order-id');

                if (newStatus === 'awaiting_confirmation') {
                    actionsContainer.innerHTML = `
                        <button data-quick-action="confirm" data-order-id="${orderId}" class="px-3 py-2 text-sm rounded-md bg-black text-white hover:bg-gray-800 confirm-btn">
                            Xác nhận
                        </button>
                        <button data-quick-action="cancel" data-order-id="${orderId}" class="px-3 py-2 text-sm rounded-md bg-red-500 text-white hover:bg-red-600">
                            Hủy
                        </button>
                        <a href="/branch/orders/${orderId}" class="flex-1 px-3 py-2 text-sm rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 text-center">Chi tiết</a>
                    `;
                } else if (newStatus === 'confirmed') {
                    actionsContainer.innerHTML = `
                        <button type="button" class="flex-1 px-3 py-2 text-sm rounded-md bg-gray-200 text-gray-700 flex items-center gap-2 cursor-default" disabled>
                            <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M12 2a10 10 0 0 1 10 10h-4a6 6 0 0 0-6-6V2z"></path>
                            </svg>
                            Đang tìm tài xế
                        </button>
                        <a href="/branch/orders/${orderId}" class="flex-1 px-3 py-2 text-sm rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 text-center">Chi tiết</a>
                    `;
                } else {
                    actionsContainer.innerHTML = `
                        <a href="/branch/orders/${orderId}" class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 text-center">Chi tiết</a>
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
                // Gọi AJAX lấy HTML partial card từ server
                fetch(`/branch/orders/${order.id}/card`)
                    .then(response => response.text())
                    .then(html => {
                        const ordersContainer = document.getElementById('ordersGrid');
                        if (!ordersContainer) return;
                        // Check for duplicate card
                        if (ordersContainer.querySelector(`[data-order-id="${order.id}"]`)) return;
                        // Tạo element từ HTML
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = html.trim();
                        const card = tempDiv.firstChild;
                        // Thêm vào đầu danh sách
                        if (ordersContainer.firstChild) {
                            ordersContainer.insertBefore(card, ordersContainer.firstChild);
                        } else {
                            ordersContainer.appendChild(card);
                        }
                    });
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
                document.querySelectorAll(`[data-order-id="${orderId}"] [data-quick-action="confirm"]`).forEach(btn => {
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                });

                // Remove card khỏi DOM NGAY LẬP TỨC
                document.querySelectorAll(`[data-order-id="${orderId}"]`).forEach(card => card.remove());

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
    document.addEventListener('DOMContentLoaded', function() {
        window.simpleBranchOrdersRealtime = new SimpleBranchOrdersRealtime();
    });
}