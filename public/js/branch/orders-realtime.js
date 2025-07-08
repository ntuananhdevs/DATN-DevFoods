// Branch Orders Realtime Management
class BranchOrdersRealtime {
    constructor() {
        this.branchId = window.branchId;
        this.pusherKey = window.pusherKey;
        this.pusherCluster = window.pusherCluster;
        this.pusher = null;
        this.channel = null;
        
        this.init();
    }

    init() {
        // Luôn bind events để xử lý click
        this.bindEvents();
        
        // Đồng bộ số đếm với server
        this.syncStatusCounts();
        
        // Chỉ khởi tạo Pusher khi có đủ điều kiện
        if (this.branchId && this.pusherKey && this.pusherCluster) {
            this.initializePusher();
        } else {
            console.log('Pusher configuration not available, realtime features disabled');
        }
    }

    initializePusher() {
        this.pusher = new Pusher(this.pusherKey, {
            cluster: this.pusherCluster,
            encrypted: true
        });

        this.channel = this.pusher.subscribe(`private-branch.${this.branchId}.orders`);

        this.channel.bind('new-order-received', (data) => {
            this.handleNewOrder(data);
        });

        this.channel.bind('order-status-updated', (data) => {
            this.handleStatusUpdate(data);
        });
    }

    bindEvents() {
        console.log('Binding events...');
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-quick-action]')) {
                console.log('Quick action clicked:', e.target.dataset);
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
        console.log('New order received:', data);
        
        // Create new order card
        const orderCard = this.createOrderCard(data.order);
        
        // Add to the beginning of the grid with animation
        const ordersGrid = document.getElementById('ordersGrid');
        if (ordersGrid) {
            // Remove empty state if exists
            const emptyState = ordersGrid.querySelector('.col-span-2');
            if (emptyState) {
                emptyState.remove();
            }

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

        // Update status counts - cập nhật cả tab "Tất cả" và tab trạng thái cụ thể
        this.updateStatusCount('all', 1);
        this.updateStatusCount(data.order.status, 1);

        // Show notification using existing modal component
        dtmodalShowToast('notification', {
            title: 'Đơn hàng mới',
            message: `Đơn hàng #${data.order.order_code || data.order.id} vừa được đặt`
        });
        
        // Play notification sound
        this.playNotificationSound();
    }

    handleStatusUpdate(data) {
        console.log('Order status updated:', data);
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
        console.log('handleQuickAction called:', orderId, action);
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
                this.moveOrderCardToNewStatus(orderId, newStatus);
            } else {
                dtmodalShowToast('error', {
                    title: 'Lỗi',
                    message: result.message
                });
            }
        } catch (error) {
            console.error('Error updating order status:', error);
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
            console.error('Error in bulk action:', error);
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
        console.log('Updating status count:', status, change);
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
        
        console.log('Moving order card:', orderId, 'from', oldStatus, 'to', newStatus);
        
        // Xóa thẻ khỏi tab hiện tại
        orderCard.remove();
        
        // Cập nhật số đếm tab
        if (oldStatus && oldStatus !== newStatus) {
            this.updateStatusCount(oldStatus, -1);
        }
        this.updateStatusCount(newStatus, 1);
        
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
        const card = document.createElement('div');
        card.className = 'order-card bg-white rounded-lg shadow-sm border border-gray-200';
        card.setAttribute('data-order-id', order.id);
        
        const customerInfo = order.customer ? 
            `<p>📦 Tổng đơn: ${order.customer.orders_count}</p>
             <p>📅 Đơn gần nhất: ${order.customer.last_order_date || 'N/A'}</p>` : '';

        const paymentInfo = order.payment ? order.payment.method_name : 'Chưa thanh toán';
        
        const estimatedTime = order.estimated_delivery_time ? 
            `<div class="flex justify-between">
                <span class="text-gray-500">Dự kiến giao:</span>
                <span class="font-medium text-green-600">${this.formatRelativeTime(order.estimated_delivery_time)}</span>
            </div>` : '';

        const pointsBadge = order.points_earned > 0 ? 
            `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                +${order.points_earned} điểm
            </span>` : '';

        const notes = order.notes ? 
            `<div class="flex items-start gap-1">
                <span class="text-xs text-gray-500 line-clamp-2">${order.notes}</span>
            </div>` : '';

        const quickActions = this.getQuickActionsHTML(order.id, order.status);

        card.innerHTML = `
            <div class="p-4">
                <div class="flex items-start gap-3 mb-3">
                    <input type="checkbox" class="order-checkbox mt-1 rounded" data-order-id="${order.id}">
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-lg text-gray-900">#${order.order_code || order.id}</h3>
                                ${pointsBadge}
                            </div>
                            <span class="status-badge text-white rounded-lg px-1" style="background-color: ${order.status_color}">${order.status_text}</span>
                        </div>

                        <div class="flex items-center gap-2 mb-2">
                            <div class="tooltip flex items-center gap-1 cursor-help">
                                <span class="text-sm font-medium text-gray-900">${order.customer_name}</span>
                                <div class="tooltip-content">
                                    <div class="text-xs space-y-1">
                                        <p>📞 ${order.customer_phone}</p>
                                        ${customerInfo}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tổng tiền:</span>
                                <span class="font-medium text-gray-900">${this.formatCurrency(order.total_amount)}₫</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Thời gian:</span>
                                <span class="text-gray-700">${this.formatDateTime(order.order_date)}</span>
                            </div>
                            ${estimatedTime}
                            <div class="flex justify-between">
                                <span class="text-gray-500">Thanh toán:</span>
                                <span class="text-gray-700">${paymentInfo}</span>
                            </div>
                            ${notes}
                        </div>

                        ${quickActions}

                        <div class="flex gap-2">
                            <a href="/branch/orders/${order.id}" class="flex-1">
                                <button class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Chi tiết
                                </button>
                            </a>
                            <a href="tel:${order.customer_phone}" class="px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Gọi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;

        return card;
    }

    getQuickActionsHTML(orderId, status) {
        if (['item_received', 'cancelled', 'refunded'].includes(status)) {
            return '';
        }

        let actions = '';
        if (status === 'awaiting_confirmation') {
            actions = `
                <button data-quick-action="confirm" data-order-id="${orderId}" class="px-2 py-1 text-xs rounded bg-blue-500 text-white hover:bg-blue-600">
                    Xác nhận
                </button>
                <button data-quick-action="cancel" data-order-id="${orderId}" class="px-2 py-1 text-xs rounded bg-red-500 text-white hover:bg-red-600">
                    Hủy
                </button>
            `;
        } else if (status === 'awaiting_driver') {
            actions = `
                <button data-quick-action="ready" data-order-id="${orderId}" class="px-2 py-1 text-xs rounded bg-blue-500 text-white hover:bg-blue-600">
                    Sẵn sàng
                </button>
            `;
        } else if (status === 'driver_picked_up') {
            actions = `
                <button data-quick-action="deliver" data-order-id="${orderId}" class="px-2 py-1 text-xs rounded bg-blue-500 text-white hover:bg-blue-600">
                    Giao hàng
                </button>
            `;
        } else if (status === 'in_transit') {
            actions = `
                <button data-quick-action="complete" data-order-id="${orderId}" class="px-2 py-1 text-xs rounded bg-blue-500 text-white hover:bg-blue-600">
                    Hoàn thành
                </button>
            `;
        }

        return `
            <div class="flex gap-2 mb-3">
                ${actions}
            </div>
        `;
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
            console.log('Could not play notification sound:', error);
        }
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount);
    }

    formatDateTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
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
        console.log('Status counts sync disabled - route not implemented yet');
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
            console.log('Could not sync status counts:', error);
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Luôn khởi tạo để xử lý sự kiện click, ngay cả khi không có Pusher
        window.branchOrdersRealtime = new BranchOrdersRealtime();
}); 