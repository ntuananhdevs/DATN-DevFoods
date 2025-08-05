// Branch Orders Realtime Management
class BranchOrdersRealtime {
    constructor() {
        this.branchId = window.branchId;
        this.pusherKey = window.pusherKey;
        this.pusherCluster = window.pusherCluster;
        this.pusher = null;
        this.channel = null;
        this.pollingInterval = null;
        
        this.init();
    }

    updateOrderCardStatus(orderCard, newStatus) {
        console.log('🎨 Cập nhật status card:', newStatus);
        const statusBadge = orderCard.querySelector('.status-badge');
        const statusContainer = orderCard.querySelector('.order-status-container');

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
            'order_failed': 'Đơn thất bại'
        };

        const statusColors = {
            'awaiting_confirmation': '#f59e0b',
            'confirmed': '#3b82f6',
            'awaiting_driver': '#60a5fa',
            'driver_assigned': '#6366f1',
            'driver_confirmed': '#2563eb',
            'waiting_driver_pick_up': '#a78bfa',
            'driver_picked_up': '#8b5cf6',
            'in_transit': '#06b6d4',
            'delivered': '#10b981',
            'item_received': '#059669',
            'cancelled': '#ef4444',
            'refunded': '#6b7280',
            'payment_failed': '#ef4444',
            'payment_received': '#84cc16',
            'order_failed': '#dc2626'
        };

        const statusText = statusTexts[newStatus] || newStatus;
        const statusColor = statusColors[newStatus] || '#6b7280';

        // Cập nhật text và màu sắc của status badge
        if (statusBadge) {
            statusBadge.textContent = statusText;
            statusBadge.style.backgroundColor = statusColor;
        }

        // Cập nhật action buttons
        this.updateOrderCardActions(orderCard, newStatus);
    }

    updateOrderCardActions(orderCard, newStatus) {
        const actionsContainer = orderCard.querySelector('.quick-actions');
        if (!actionsContainer) return;

        const orderId = orderCard.getAttribute('data-order-id');
        const quickActionsHtml = this.getQuickActionsHTML(orderId, newStatus);
        actionsContainer.innerHTML = quickActionsHtml;
    }

    init() {
        // Luôn bind events để xử lý click
        this.bindEvents();
        
        // Đồng bộ số đếm với server
        this.syncStatusCounts();
        
        // Chỉ khởi tạo Pusher khi có đủ điều kiện
        if (this.branchId && this.pusherKey && this.pusherCluster) {
            this.initializePusher();
        }
    }

    initializePusher() {
        try {
            this.pusher = new Pusher(this.pusherKey, {
                cluster: this.pusherCluster,
                encrypted: true,
                authEndpoint: '/branch/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }
            });

            // Add connection event listeners
            this.pusher.connection.bind('connected', () => {
                // Connected successfully
            });

            this.pusher.connection.bind('error', (err) => {
                this.handlePusherError(err);
            });

            this.pusher.connection.bind('disconnected', () => {
                // Disconnected
            });

            // Subscribe to private branch channel for new orders
            const channelName = `private-branch.${this.branchId}.orders`;
            this.channel = this.pusher.subscribe(channelName);

            this.channel.bind('pusher:subscription_succeeded', () => {
                // Successfully subscribed to private branch channel
            });

            this.channel.bind('pusher:subscription_error', (status) => {
                this.handleSubscriptionError(status);
            });

            this.channel.bind('new-order-received', (data) => {
                this.handleNewOrder(data);
            });

            // Subscribe to public branch orders channel for status updates
            this.branchOrdersChannel = this.pusher.subscribe('branch-orders-channel');
            
            this.branchOrdersChannel.bind('pusher:subscription_succeeded', () => {
                // Successfully subscribed to branch orders channel
            });

            this.branchOrdersChannel.bind('order-status-updated', (data) => {
                // Only handle updates for this branch
                if (data.branch_id == this.branchId) {
                    this.handleStatusUpdate(data);
                }
            });

            // Subscribe to order status updates channel for additional coverage
            this.orderStatusChannel = this.pusher.subscribe('order-status-updates');
            
            this.orderStatusChannel.bind('pusher:subscription_succeeded', () => {
                // Successfully subscribed to order status updates channel
            });

            this.orderStatusChannel.bind('order-status-updated', (data) => {
                // Only handle updates for this branch
                if (data.branch_id == this.branchId) {
                    this.handleStatusUpdate(data);
                }
            });

        } catch (error) {
            this.handlePusherError(error);
        }
    }

    handlePusherError(error) {
        // Show user-friendly error message
        if (typeof dtmodalShowToast === 'function') {
            dtmodalShowToast('warning', {
                title: 'Kết nối realtime',
                message: 'Không thể kết nối realtime. Các thay đổi sẽ cần tải lại trang.'
            });
        }
        
        // Fallback: Set up polling for updates
        this.setupPollingFallback();
    }

    handleSubscriptionError(status) {
        if (status === 403) {
            if (typeof dtmodalShowToast === 'function') {
                dtmodalShowToast('error', {
                    title: 'Lỗi quyền truy cập',
                    message: 'Không có quyền truy cập kênh realtime.'
                });
            }
        }
    }

    setupPollingFallback() {
        // Poll for new orders every 30 seconds
        this.pollingInterval = setInterval(() => {
            this.checkForNewOrders();
        }, 30000);
    }

    async checkForNewOrders() {
        try {
            // Get the timestamp of the last order on the page
            const lastOrderElement = document.querySelector('[data-order-date]');
            let lastOrderTime = null;
            
            if (lastOrderElement) {
                lastOrderTime = lastOrderElement.getAttribute('data-order-date');
            }
            
            const params = new URLSearchParams({
                check_new: 'true'
            });
            
            if (lastOrderTime) {
                params.append('last_order_time', lastOrderTime);
            }
            
            const response = await fetch(`/branch/orders?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.hasNewOrders) {
                    // Show notification before reloading
                    if (typeof dtmodalShowToast === 'function') {
                        dtmodalShowToast('info', {
                            title: 'Đơn hàng mới',
                            message: 'Bạn có đơn hàng mới'
                        });
                    }
                    
                    // Reload the page to show new orders
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            }
        } catch (error) {
            // Error checking for new orders
        }
    }

    bindEvents() {
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-quick-action]')) {
                e.preventDefault();
                const orderId = e.target.dataset.orderId;
                const action = e.target.dataset.quickAction;
                this.handleQuickAction(orderId, action);
            }
        });

        // Bind bulk action buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('#bulkConfirmBtn')) {
                this.handleBulkAction('confirm');
            } else if (e.target.matches('#bulkCancelBtn')) {
                this.handleBulkAction('cancel');
            }
        });

        // Bind checkbox events for bulk actions
        document.addEventListener('change', (e) => {
            if (e.target.matches('.order-checkbox')) {
                this.updateBulkActionsBar();
            }
        });

        // Close bulk actions bar
        document.addEventListener('click', (e) => {
            if (e.target.matches('#closeBulkActions')) {
                this.hideBulkActionsBar();
            }
        });
    }

    handleNewOrder(data) {
        // Kiểm tra tab hiện tại
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status') || 'all';
        if (status === 'all' || status === 'awaiting_confirmation') {
            // Create new order card HTML
            const orderCardHtml = this.createOrderCard(data.order);
            const ordersGrid = document.getElementById('ordersGrid');
            if (ordersGrid) {
                // Remove empty state if exists
                const emptyState = ordersGrid.querySelector('.col-span-3');
                if (emptyState) {
                    emptyState.remove();
                }
                // Create temporary container to parse HTML
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = orderCardHtml;
                const orderCard = tempDiv.firstElementChild;
                // Add new order at the top
                ordersGrid.insertBefore(orderCard, ordersGrid.firstChild);
                // Add animation
                orderCard.style.opacity = '0';
                orderCard.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    orderCard.style.transition = 'all 0.3s ease';
                    orderCard.style.opacity = '1';
                    orderCard.style.transform = 'translateY(0)';
                }, 10);
            }
        }
        // Update status counts - cập nhật cả tab "Tất cả" và tab trạng thái cụ thể
        this.updateStatusCount('all', 1);
        this.updateStatusCount(data.order.status, 1);
        // Show notification using existing modal component
        if (typeof dtmodalShowToast === 'function') {
            dtmodalShowToast('notification', {
                title: 'Đơn hàng mới',
                message: 'Bạn có đơn hàng mới'
            });
        }
        // Play notification sound
        this.playNotificationSound();
    }

    handleStatusUpdate(data) {
        const orderCard = document.querySelector(`[data-order-id="${data.order.id}"]`);
        if (orderCard) {
            const statusBadge = orderCard.querySelector('.status-badge');
            if (statusBadge) {
                statusBadge.textContent = data.order.status_text;
                statusBadge.style.backgroundColor = data.order.status_color;
            }
        }
        
        // Cập nhật số đếm: giảm trạng thái cũ, tăng trạng thái mới
        this.updateStatusCount(data.old_status, -1);
        this.updateStatusCount(data.new_status, 1);
        
        // Di chuyển card sang tab mới nếu cần
        this.moveOrderCardToNewStatus(data.order.id, data.new_status);
        
        dtmodalShowToast('info', {
            title: 'Cập nhật trạng thái',
            message: `Đơn hàng #${data.order.order_code || data.order.id} đã chuyển sang ${data.order.status_text}`
        });
    }

    async handleQuickAction(orderId, action) {
        const statusMap = {
            'confirm': 'awaiting_driver',
            'ready': 'in_transit',
            'deliver': 'delivered',
            'complete': 'item_received',
            'cancel': 'cancelled'
        };

        const newStatus = statusMap[action];
        if (!newStatus) {
            dtmodalShowToast('error', {
                title: 'Lỗi',
                message: 'Hành động không hợp lệ'
            });
            return;
        }

        try {
            const response = await fetch(`/branch/orders/${orderId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: newStatus,
                    note: `Chuyển trạng thái sang ${this.getStatusText(newStatus)}`
                })
            });

            const result = await response.json();
            
            if (result.success) {
                dtmodalShowToast('success', {
                    title: 'Thành công',
                    message: result.message
                });
                
                // Cập nhật status counts trước khi di chuyển card
                if (action === 'confirm') {
                    this.updateStatusCountAfterConfirm();
                }
                
                this.moveOrderCardToNewStatus(orderId, newStatus);
            } else {
                dtmodalShowToast('error', {
                    title: 'Lỗi',
                    message: result.message
                });
            }
        } catch (error) {
            dtmodalShowToast('error', {
                title: 'Lỗi',
                message: 'Không thể cập nhật trạng thái đơn hàng'
            });
        }
    }

    async handleBulkAction(action) {
        const selectedOrders = Array.from(document.querySelectorAll('.order-checkbox:checked'))
            .map(checkbox => checkbox.dataset.orderId);

        if (selectedOrders.length === 0) {
            dtmodalShowToast('warning', {
                title: 'Thông báo',
                message: 'Vui lòng chọn ít nhất một đơn hàng'
            });
            return;
        }

        const statusMap = {
            'confirm': 'processing',
            'cancel': 'cancelled'
        };

        const newStatus = statusMap[action];
        if (!newStatus) {
            dtmodalShowToast('error', {
                title: 'Lỗi',
                message: 'Hành động không hợp lệ'
            });
            return;
        }

        try {
            const promises = selectedOrders.map(orderId => 
                fetch(`/branch/orders/${orderId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        status: newStatus,
                        note: `Bulk action: Chuyển trạng thái sang ${this.getStatusText(newStatus)}`
                    })
                })
            );

            const results = await Promise.all(promises);
            const successCount = results.filter(r => r.ok).length;

            dtmodalShowToast('success', {
                title: 'Thành công',
                message: `Đã cập nhật ${successCount}/${selectedOrders.length} đơn hàng`
            });
            
            // Uncheck all checkboxes
            document.querySelectorAll('.order-checkbox:checked').forEach(cb => cb.checked = false);
            this.hideBulkActionsBar();
        } catch (error) {
            dtmodalShowToast('error', {
                title: 'Lỗi',
                message: 'Không thể thực hiện hành động hàng loạt'
            });
        }
    }

    updateBulkActionsBar() {
        const selectedCount = document.querySelectorAll('.order-checkbox:checked').length;
        const bulkBar = document.getElementById('bulkActionsBar');
        const selectedCountSpan = document.getElementById('selectedCount');

        if (selectedCount > 0) {
            selectedCountSpan.textContent = `${selectedCount} đơn đã chọn`;
            bulkBar.style.display = 'block';
        } else {
            bulkBar.style.display = 'none';
        }
    }

    hideBulkActionsBar() {
        const bulkBar = document.getElementById('bulkActionsBar');
        bulkBar.style.display = 'none';
    }

    updateStatusCount(status, change) {
        const statusTabs = document.querySelectorAll('.status-tab');
        statusTabs.forEach(tab => {
            const href = tab.getAttribute('href');
            const tabStatus = tab.dataset.status;
            
            // Cập nhật tab "Tất cả" nếu thay đổi bất kỳ trạng thái nào
            if (tabStatus === 'all') {
                const countSpan = tab.querySelector('span') || tab;
                const currentText = countSpan.textContent;
                const match = currentText.match(/\((\d+)\)/);
                if (match) {
                    const currentCount = parseInt(match[1]) || 0;
                    const newCount = Math.max(0, currentCount + change);
                    countSpan.textContent = currentText.replace(/\(\d+\)/, `(${newCount})`);
                }
            }
            // Cập nhật tab cụ thể nếu trạng thái khớp
            else if (tabStatus === status) {
                const countSpan = tab.querySelector('span') || tab;
                const currentText = countSpan.textContent;
                const match = currentText.match(/\((\d+)\)/);
                if (match) {
                    const currentCount = parseInt(match[1]) || 0;
                    const newCount = Math.max(0, currentCount + change);
                    countSpan.textContent = currentText.replace(/\(\d+\)/, `(${newCount})`);
                }
            }
        });
    }

    updateStatusCountAfterConfirm() {
        // Giảm số lượng tab "Chờ xác nhận"
        this.updateStatusCount('awaiting_confirmation', -1);
        // Tăng số lượng tab "Chờ tài xế"
        this.updateStatusCount('awaiting_driver', 1);
    }

    moveOrderCardToNewStatus(orderId, newStatus) {
        const orderCard = document.querySelector(`[data-order-id="${orderId}"]`);
        if (!orderCard) return;
        
        // Lấy trạng thái cũ từ status badge
        const statusBadge = orderCard.querySelector('.status-badge');
        let oldStatus = null;
        
        if (statusBadge) {
            const statusText = statusBadge.textContent?.trim();
            // Map text status sang status code
            const statusMap = {
                'Chờ xác nhận': 'awaiting_confirmation',
                'Chờ tài xế': 'awaiting_driver', 
                'Tài xế đã nhận': 'driver_picked_up',
                'Đang giao': 'in_transit',
                'Đã giao': 'delivered',
                'Đã nhận': 'item_received',
                'Đã hủy': 'cancelled',
                'Đã hoàn tiền': 'refunded'
            };
            oldStatus = statusMap[statusText] || statusText?.toLowerCase().replace(/\s/g, '_');
        }
        
        // Kiểm tra tab hiện tại và chỉ ẩn đơn hàng nếu cần thiết
        const urlParams = new URLSearchParams(window.location.search);
        const currentTabStatus = urlParams.get('status') || 'all';
        
        // Nếu đang ở tab "Tất cả", cập nhật trạng thái thay vì xóa
        if (currentTabStatus === 'all') {
            // Cập nhật trạng thái của đơn hàng thay vì xóa
            this.updateOrderCardStatus(orderCard, newStatus);
            return;
        }
        
        // Nếu đang ở tab cụ thể và đơn hàng không còn thuộc tab này, thì ẩn
        if (currentTabStatus !== 'all' && currentTabStatus !== newStatus) {
            orderCard.style.display = 'none';
        }
        
        // Cập nhật URL nếu đang ở tab cụ thể và không còn đơn hàng nào
        const currentTab = document.querySelector('.status-tab.active');
        if (currentTab && currentTab.dataset.status === oldStatus) {
            const remainingCards = document.querySelectorAll('.order-card');
            if (remainingCards.length === 0) {
                // Chuyển về tab "Tất cả" nếu không còn đơn hàng nào
                const allTab = document.querySelector('.status-tab[data-status="all"]');
                if (allTab) {
                    allTab.click();
                }
            }
        }
    }

    createOrderCard(order) {
        const statusColors = {
            'awaiting_confirmation': '#f59e0b',
            'confirmed': '#3b82f6',
            'awaiting_driver': '#60a5fa',
            'driver_assigned': '#6366f1',
            'driver_confirmed': '#2563eb',
            'waiting_driver_pick_up': '#a78bfa',
            'driver_picked_up': '#8b5cf6',
            'in_transit': '#06b6d4',
            'delivered': '#10b981',
            'item_received': '#059669',
            'cancelled': '#ef4444',
            'refunded': '#6b7280',
            'payment_failed': '#ef4444',
            'payment_received': '#84cc16',
            'order_failed': '#dc2626',
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

        const statusColor = statusColors[order.status] || '#6b7280';
        const statusText = statusTexts[order.status] || order.status;

        const customerName = order.customer_name || order.customerName || 'Khách hàng';
        const customerPhone = order.customer_phone || order.customerPhone || '';
        const orderCode = order.order_code || order.id;
        const totalAmount = this.formatCurrency(order.total_amount);
        const orderDate = this.formatDateTime(order.created_at || order.order_date);
        const estimatedTime = order.estimated_delivery_time ? this.formatRelativeTime(order.estimated_delivery_time) : '';
        const notes = order.notes || '';
        const pointsEarned = order.points_earned || 0;
        const distanceKm = order.distance_km || '';
        const itemCount = order.order_items_count || order.orderItems?.length || 0;

        // Payment method display
        let paymentMethodHtml = '';
        const pm = (order.payment?.payment_method || '').toLowerCase();
        if (pm === 'cod') {
            paymentMethodHtml = '<span class="inline-block px-2 py-0.5 rounded bg-green-700 text-white text-xs font-semibold">COD</span>';
        } else if (pm === 'vnpay') {
            paymentMethodHtml = '<span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-blue-100 text-blue-800 text-xs font-semibold"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 16" style="height:1em;width:auto;display:inline;vertical-align:middle;" aria-label="VNPAY Icon"><text x="0" y="12" font-size="12" font-family="Arial, Helvetica, sans-serif" font-weight="bold" fill="#e30613">VN</text><text x="18" y="12" font-size="12" font-family="Arial, Helvetica, sans-serif" font-weight="bold" fill="#0072bc">PAY</text></svg></span>';
        } else if (pm === 'balance') {
            paymentMethodHtml = '<span class="inline-block px-2 py-1 rounded bg-purple-100 text-purple-700 text-xs font-semibold">Số dư</span>';
        }

        const quickActionsHtml = this.getQuickActionsHTML(order.id, order.status);

        return `
            <div class="order-card bg-white rounded-lg shadow-sm border border-gray-200 h-full flex flex-col relative pb-16" data-order-id="${order.id}">
                <div class="p-2 flex flex-col h-full pb-2">
                    <div class="flex items-start gap-3 mb-2">
                        <input type="checkbox" class="order-checkbox mt-1 rounded" data-order-id="${order.id}">
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-semibold text-lg text-gray-900">#${orderCode}</h3>
                                    ${pointsEarned > 0 ? `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">+${pointsEarned} điểm</span>` : ''}
                                </div>
                                <span class="status-badge text-white rounded-lg px-2 py-1 text-xs font-medium" style="background-color: ${statusColor}">
                                    ${statusText}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 mb-1">
                                <div class="flex items-center justify-center w-11 h-11 rounded-full bg-blue-100 text-blue-700 font-bold text-sm">
                                    ${customerName.charAt(0).toUpperCase()}
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-semibold text-base text-gray-900">${customerName}</span>
                                    <div class="flex items-center gap-2 text-gray-500 text-sm">
                                        <span class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M22 16.92v3a2 2 0 01-2.18 2A19.72 19.72 0 013 5.18 2 2 0 015 3h3a2 2 0 012 1.72c.13.81.36 1.6.68 2.34a2 2 0 01-.45 2.11l-1.27 1.27a16 16 0 006.29 6.29l1.27-1.27a2 2 0 012.11-.45c.74.32 1.53.55 2.34.68A2 2 0 0122 16.92z"/>
                                            </svg>
                                            ${customerPhone}
                                        </span>
                                        ${distanceKm ? `<span class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            ${parseFloat(distanceKm).toFixed(1)} km
                                        </span>` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 my-2"></div>
                    <div class="flex flex-col gap-1 text-sm flex-1">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tổng tiền:</span>
                            <span class="font-semibold text-gray-900">${totalAmount}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Sản phẩm:</span>
                            <span class="text-gray-700">${itemCount} sản phẩm</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Thời gian:</span>
                            <span class="text-gray-700">${orderDate}</span>
                        </div>
                        ${estimatedTime ? `<div class="flex justify-between">
                            <span class="text-gray-500">Dự kiến giao:</span>
                            <span class="font-medium text-green-600">${estimatedTime}</span>
                        </div>` : ''}
                        <div class="flex justify-between">
                            <span class="text-gray-500">Thanh toán:</span>
                            ${paymentMethodHtml}
                        </div>
                        ${notes ? `<div class="flex justify-between">
                            <span class="text-gray-500">Note:</span>
                            <span class="text-xs font-medium text-blue-700 bg-blue-50 rounded px-2 py-1 break-words" style="max-height:2rem;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;white-space:normal;">
                                ${notes}
                            </span>
                        </div>` : ''}
                    </div>
                </div>
                <div class="absolute left-0 bottom-0 w-full px-4 pb-3">
                    <div class="flex gap-2 items-end">
                        ${quickActionsHtml}
                        <a href="/branch/orders/${order.id}" class="flex-1">
                            <button class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Chi tiết
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        `;
    }

    getQuickActionsHTML(orderId, status) {
        if (['item_received', 'cancelled', 'refunded'].includes(status)) {
            return '';
        }

        let actions = '';
        if (status === 'awaiting_confirmation') {
            actions = `
                <button data-quick-action="confirm" data-order-id="${orderId}" class="px-3 py-2 text-sm rounded-md bg-black text-white hover:bg-gray-800">
                    Xác nhận
                </button>
                <button data-quick-action="cancel" data-order-id="${orderId}" class="px-3 py-2 text-sm rounded-md bg-red-500 text-white hover:bg-red-600">
                    Hủy
                </button>
            `;
        } else if (status === 'awaiting_driver') {
            actions = `
                <button data-quick-action="ready" data-order-id="${orderId}" class="px-3 py-2 text-sm rounded-md bg-blue-500 text-white hover:bg-blue-600">
                    Sẵn sàng
                </button>
            `;
        } else if (status === 'driver_picked_up') {
            actions = `
                <button data-quick-action="deliver" data-order-id="${orderId}" class="px-3 py-2 text-sm rounded-md bg-blue-500 text-white hover:bg-blue-600">
                    Giao hàng
                </button>
            `;
        } else if (status === 'in_transit') {
            actions = `
                <button data-quick-action="complete" data-order-id="${orderId}" class="px-3 py-2 text-sm rounded-md bg-blue-500 text-white hover:bg-blue-600">
                    Hoàn thành
                </button>
            `;
        }

        return actions;
    }

    playNotificationSound() {
        // Create audio context for notification sound
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);

            gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.2);
        } catch (error) {
            // Could not play notification sound
        }
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + '₫';
    }

    formatDateTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString('vi-VN', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    formatRelativeTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInMinutes = Math.floor((date - now) / (1000 * 60));
        
        if (diffInMinutes < 0) {
            return 'Đã quá hạn';
        } else if (diffInMinutes < 60) {
            return `${diffInMinutes} phút nữa`;
        } else {
            const hours = Math.floor(diffInMinutes / 60);
            const minutes = diffInMinutes % 60;
            return `${hours}h${minutes > 0 ? ` ${minutes}p` : ''} nữa`;
        }
    }

    getStatusText(status) {
        const statusTexts = {
            'pending': 'Chờ xác nhận',
            'processing': 'Đang chuẩn bị',
            'ready': 'Sẵn sàng giao',
            'delivery': 'Đang giao hàng',
            'completed': 'Hoàn thành',
            'cancelled': 'Đã hủy'
        };
        return statusTexts[status] || status;
    }

    // Thêm hàm đồng bộ số đếm với server
    async syncStatusCounts() {
        // Tạm thời bỏ qua vì route chưa có
        return;
        
        try {
            const response = await fetch('/branch/orders/status-counts', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                // Cập nhật số đếm từ server
                Object.keys(data).forEach(status => {
                    const tab = document.querySelector(`.status-tab[data-status="${status}"]`);
                    if (tab) {
                        const currentText = tab.textContent;
                        const newText = currentText.replace(/\(\d+\)/, `(${data[status]})`);
                        tab.textContent = newText;
                    }
                });
            }
        } catch (error) {
            // Could not sync status counts
        }
    }

    // Cleanup method
    destroy() {
        // Clear polling interval
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
        
        // Unsubscribe from all channels
        if (this.pusher) {
            if (this.channel) {
                this.pusher.unsubscribe(`private-branch.${this.branchId}.orders`);
            }
            if (this.branchOrdersChannel) {
                this.pusher.unsubscribe('branch-orders-channel');
            }
            if (this.orderStatusChannel) {
                this.pusher.unsubscribe('order-status-updates');
            }
            
            // Disconnect Pusher
            this.pusher.disconnect();
            this.pusher = null;
        }
        
        // Clear channel references
        this.channel = null;
        this.branchOrdersChannel = null;
        this.orderStatusChannel = null;
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Luôn khởi tạo để xử lý sự kiện click, ngay cả khi không có Pusher
    window.branchOrdersRealtime = new BranchOrdersRealtime();
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (window.branchOrdersRealtime) {
            window.branchOrdersRealtime.destroy();
        }
    });
});