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
                    // Admin nhận tất cả đơn hàng từ mọi branch
                    this.hasNewOrder = true;
                    this.startNotificationLoop();
                    this.handleNewOrder(data);
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
                // Cập nhật tab "all" vì đơn hàng mới thuộc về tất cả
                const allTab = document.querySelector('a[href*="status="]');
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
                const statusTexts = {
                    'awaiting_confirmation': 'Chờ xác nhận',
                    'awaiting_driver': 'Chờ tài xế',
                    'in_transit': 'Đang giao',
                    'delivered': 'Đã giao',
                    'cancelled': 'Đã hủy',
                    'refunded': 'Đã hoàn tiền'
                };
                
                const statusText = statusTexts[orderStatus] || orderStatus;
                const statusTab = document.querySelector(`a[href*="status=${orderStatus}"]`);
                if (statusTab) {
                    const currentText = statusTab.textContent;
                    const regex = new RegExp(`${statusText} \\((\\d+)\\)`);
                    const match = currentText.match(regex);
                    
                    if (match) {
                        const currentCount = parseInt(match[1]) || 0;
                        const newCount = currentCount + 1;
                        statusTab.textContent = `${statusText} (${newCount})`;
                    }
                }
            }

            addOrderRow(order) {
                // Lấy status tab hiện tại
                const urlParams = new URLSearchParams(window.location.search);
                const currentStatus = urlParams.get('status') || '';
                
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
                        const tableBody = document.querySelector('tbody');
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
                    'order_confirmed': 'Đang chuẩn bị',
                    'in_transit': 'Đang giao',
                    'delivered': 'Hoàn thành',
                    'cancelled': 'Đã hủy',
                    'refunded': 'Đã hoàn tiền',
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

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        window.adminOrdersRealtime = new AdminOrdersRealtime();
    }); 
} 