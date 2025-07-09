if (window.ordersRealtimeInitialized) {
    // Đã khởi tạo, không làm gì nữa
} else {
    window.ordersRealtimeInitialized = true;

    // Đảm bảo chỉ khai báo class SimpleBranchOrdersRealtime nếu chưa tồn tại
    if (typeof window.SimpleBranchOrdersRealtime === 'undefined') {
        class SimpleBranchOrdersRealtime {
            constructor() {
                this.branchId = window.branchId;
                this.pusherKey = window.pusherKey;
                this.pusherCluster = window.pusherCluster;
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
                try {
                    this.pusher = new Pusher(this.pusherKey, {
                        cluster: this.pusherCluster,
                        encrypted: true,
                        authEndpoint: '/broadcasting/auth'
                    });

                    // Connection events
                    this.pusher.connection.bind('connected', () => {
                        // Connected successfully
                    });

                    this.pusher.connection.bind('error', (err) => {
                        this.showNotification('Lỗi kết nối', 'Không thể kết nối Pusher');
                    });

                    this.pusher.connection.bind('disconnected', () => {
                        this.showNotification('Mất kết nối', 'Kết nối Pusher đã bị ngắt');
                    });

                    // Subscribe to public channel
                    this.subscribeToPublicChannel();

                } catch (error) {
                    this.showNotification('Lỗi khởi tạo', 'Không thể khởi tạo Pusher');
                }
            }

            subscribeToPublicChannel() {
                this.publicChannel = this.pusher.subscribe('branch-orders-channel');
                
                this.publicChannel.bind('pusher:subscription_succeeded', () => {
                    // Successfully subscribed
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
                            iziToast.success({ title: 'Thành công', message: successMsg });
                        } else if (typeof dtmodalShowToast === 'function') {
                            dtmodalShowToast('success', {title: 'Thành công', message: successMsg});
                        } else {
                            alert(successMsg);
                        }
                        // Cập nhật lại số lượng trên tab status
                        if (typeof updateOrderCounts === 'function') {
                            updateOrderCounts();
                        } else if (window.fetchOrderCounts) {
                            window.fetchOrderCounts();
                        }
                    } else {
                        if (typeof dtmodalShowToast === 'function') {
                            dtmodalShowToast('error', {title: 'Lỗi', message: data.message || 'Xác nhận đơn hàng lỗi'});
                        } else if (window.iziToast) {
                            iziToast.error({ title: 'Lỗi', message: data.message || 'Xác nhận đơn hàng lỗi' });
                        } else {
                            alert(data.message || 'Xác nhận đơn hàng lỗi');
                        }
                        console.error('Xác nhận đơn hàng lỗi:', { status: response.status, statusText: response.statusText, data });
                    }
                })
                .catch(error => {
                    if (typeof dtmodalShowToast === 'function') {
                        dtmodalShowToast('error', {title: 'Lỗi', message: 'Lỗi xác nhận đơn hàng'});
                    } else if (window.iziToast) {
                        iziToast.error({ title: 'Lỗi', message: 'Lỗi xác nhận đơn hàng' });
                    } else {
                        alert('Lỗi xác nhận đơn hàng');
                    }
                    console.error('Xác nhận đơn hàng lỗi:', error);
                });
            }
        }
        window.SimpleBranchOrdersRealtime = SimpleBranchOrdersRealtime;
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        window.simpleBranchOrdersRealtime = new SimpleBranchOrdersRealtime();
    }); 
} 