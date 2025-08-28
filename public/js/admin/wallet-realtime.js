/**
 * Admin Wallet Real-time Notifications
 * Handles real-time updates for wallet transactions
 */

// Initialize Pusher connection
if (typeof Pusher !== 'undefined' && window.pusherKey) {
    const pusher = new Pusher(window.pusherKey, {
        cluster: window.pusherCluster,
        encrypted: true,
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }
    });

    // Subscribe to admin wallet channel
    const adminWalletChannel = pusher.subscribe('admin-wallet-channel');

    // Listen for new wallet transactions
    adminWalletChannel.bind('new-wallet-transaction', function(data) {
        console.log('New wallet transaction received:', data);
        
        // Show notification
        showWalletNotification(data.transaction, data.message, 'new');
        
        // Update UI counters if present
        updateWalletCounters('new', data.transaction);
        
        // Play notification sound
        playNotificationSound();
    });

    // Listen for wallet transaction status updates
    adminWalletChannel.bind('wallet-transaction-status-updated', function(data) {
        console.log('Wallet transaction status updated:', data);
        
        // Show notification
        showWalletNotification(data.transaction, data.message, 'status_updated');
        
        // Update UI counters if present
        updateWalletCounters('status_updated', data.transaction);
        
        // Update transaction row if on wallet pages
        updateTransactionRow(data.transaction);
        
        // Play notification sound for important status changes
        if (['completed', 'failed'].includes(data.new_status)) {
            playNotificationSound();
        }
    });

    console.log('Wallet real-time notifications initialized');
} else {
    console.warn('Pusher not available or not configured properly for wallet notifications');
}

/**
 * Show wallet notification
 */
function showWalletNotification(transaction, message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `
        fixed top-4 right-4 z-50 max-w-sm w-full bg-white border border-gray-200 rounded-lg shadow-lg 
        transform translate-x-full transition-transform duration-300 ease-in-out
    `;
    
    // Get notification icon and color based on transaction type and status
    const { icon, color } = getWalletNotificationStyle(transaction, type);
    
    notification.innerHTML = `
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 rounded-full ${color} flex items-center justify-center">
                        <i class="${icon} text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900">
                        ${getWalletNotificationTitle(transaction, type)}
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                        ${message}
                    </p>
                    <div class="mt-2 flex items-center text-xs text-gray-400">
                        <i class="fas fa-user mr-1"></i>
                        <span>${transaction.user.name}</span>
                        <span class="mx-2">•</span>
                        <span>${transaction.formatted_amount}</span>
                    </div>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button onclick="closeWalletNotification(this)" 
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <!-- Action buttons -->
            <div class="mt-3 flex space-x-2">
                ${getWalletNotificationActions(transaction, type)}
            </div>
        </div>
    `;
    
    // Add to DOM
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 8 seconds
    setTimeout(() => {
        closeWalletNotification(notification.querySelector('button'));
    }, 8000);
}

/**
 * Close wallet notification
 */
function closeWalletNotification(button) {
    const notification = button.closest('.fixed');
    notification.classList.add('translate-x-full');
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 300);
}

/**
 * Get notification style based on transaction
 */
function getWalletNotificationStyle(transaction, type) {
    if (transaction.type === 'deposit') {
        return {
            icon: 'fas fa-plus-circle',
            color: transaction.status === 'completed' ? 'bg-green-500' : 'bg-blue-500'
        };
    } else if (transaction.type === 'withdraw') {
        switch (transaction.status) {
            case 'pending':
                return { icon: 'fas fa-clock', color: 'bg-orange-500' };
            case 'completed':
                return { icon: 'fas fa-check-circle', color: 'bg-green-500' };
            case 'failed':
                return { icon: 'fas fa-times-circle', color: 'bg-red-500' };
            default:
                return { icon: 'fas fa-minus-circle', color: 'bg-gray-500' };
        }
    }
    
    return { icon: 'fas fa-wallet', color: 'bg-purple-500' };
}

/**
 * Get notification title
 */
function getWalletNotificationTitle(transaction, type) {
    if (type === 'new') {
        return transaction.type === 'deposit' ? 'Yêu cầu nạp tiền mới' : 'Yêu cầu rút tiền mới';
    } else {
        switch (transaction.status) {
            case 'completed':
                return transaction.type === 'deposit' ? 'Nạp tiền thành công' : 'Rút tiền được duyệt';
            case 'failed':
                return transaction.type === 'deposit' ? 'Nạp tiền thất bại' : 'Rút tiền bị từ chối';
            case 'cancelled':
                return 'Giao dịch đã hủy';
            default:
                return 'Cập nhật giao dịch';
        }
    }
}

/**
 * Get notification action buttons
 */
function getWalletNotificationActions(transaction, type) {
    let actions = '';
    
    // View details button
    if (transaction.type === 'withdraw' && transaction.status === 'pending') {
        actions += `
            <a href="/admin/wallet/withdrawals/pending" 
               class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-orange-600 hover:bg-orange-700 rounded transition-colors">
                <i class="fas fa-eye mr-1"></i>
                Xem chi tiết
            </a>
        `;
    } else if (transaction.type === 'deposit') {
        actions += `
            <a href="/admin/wallet/deposits/history" 
               class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded transition-colors">
                <i class="fas fa-history mr-1"></i>
                Lịch sử nạp
            </a>
        `;
    } else {
        actions += `
            <a href="/admin/wallet/withdrawals/history" 
               class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-gray-600 hover:bg-gray-700 rounded transition-colors">
                <i class="fas fa-history mr-1"></i>
                Lịch sử rút
            </a>
        `;
    }
    
    // Dashboard button
    actions += `
        <a href="/admin/wallet" 
           class="inline-flex items-center px-3 py-1 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded transition-colors">
            <i class="fas fa-tachometer-alt mr-1"></i>
            Dashboard
        </a>
    `;
    
    return actions;
}

/**
 * Update wallet counters in UI
 */
function updateWalletCounters(eventType, transaction) {
    // Update pending withdrawals counter
    const pendingCounter = document.querySelector('[data-wallet-counter="pending-withdrawals"]');
    if (pendingCounter && transaction.type === 'withdraw') {
        const currentCount = parseInt(pendingCounter.textContent) || 0;
        
        if (eventType === 'new' && transaction.status === 'pending') {
            pendingCounter.textContent = currentCount + 1;
        } else if (eventType === 'status_updated' && transaction.status !== 'pending') {
            pendingCounter.textContent = Math.max(0, currentCount - 1);
        }
    }
    
    // Update total transactions counter
    const totalCounter = document.querySelector('[data-wallet-counter="total-transactions"]');
    if (totalCounter && eventType === 'new') {
        const currentCount = parseInt(totalCounter.textContent) || 0;
        totalCounter.textContent = currentCount + 1;
    }
    
    // Update amount displays
    updateAmountDisplays(eventType, transaction);
}

/**
 * Update amount displays
 */
function updateAmountDisplays(eventType, transaction) {
    // Update total amount counters based on transaction type
    const depositAmountEl = document.querySelector('[data-wallet-amount="total-deposits"]');
    const withdrawAmountEl = document.querySelector('[data-wallet-amount="total-withdrawals"]');
    
    if (eventType === 'status_updated' && transaction.status === 'completed') {
        if (transaction.type === 'deposit' && depositAmountEl) {
            const currentAmount = parseFloat(depositAmountEl.dataset.rawAmount) || 0;
            const newAmount = currentAmount + transaction.amount;
            depositAmountEl.dataset.rawAmount = newAmount;
            depositAmountEl.textContent = formatCurrency(newAmount);
        } else if (transaction.type === 'withdraw' && withdrawAmountEl) {
            const currentAmount = parseFloat(withdrawAmountEl.dataset.rawAmount) || 0;
            const newAmount = currentAmount + transaction.amount;
            withdrawAmountEl.dataset.rawAmount = newAmount;
            withdrawAmountEl.textContent = formatCurrency(newAmount);
        }
    }
}

/**
 * Update transaction row in tables
 */
function updateTransactionRow(transaction) {
    // Find transaction row by ID
    const row = document.querySelector(`[data-transaction-id="${transaction.id}"]`);
    if (row) {
        // Update status badge
        const statusBadge = row.querySelector('.status-badge');
        if (statusBadge) {
            statusBadge.className = `status-badge ${getStatusBadgeClass(transaction.status)}`;
            statusBadge.innerHTML = `<i class="${getStatusIcon(transaction.status)} mr-1"></i>${getStatusText(transaction.status)}`;
        }
        
        // Update processed time
        const processedTimeEl = row.querySelector('.processed-time');
        if (processedTimeEl && transaction.processed_at) {
            processedTimeEl.textContent = transaction.processed_at;
        }
        
        // Add animation to highlight the update
        row.classList.add('bg-yellow-50');
        setTimeout(() => {
            row.classList.remove('bg-yellow-50');
        }, 2000);
    }
}

/**
 * Play notification sound
 */
function playNotificationSound() {
    // Create audio element and play notification sound
    try {
        const audio = new Audio('/sounds/notification.mp3');
        audio.volume = 0.3;
        audio.play().catch(e => console.log('Could not play notification sound:', e));
    } catch (e) {
        console.log('Notification sound not available');
    }
}

/**
 * Helper functions
 */
function getStatusBadgeClass(status) {
    const classes = {
        'pending': 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800',
        'completed': 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800',
        'failed': 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800',
        'cancelled': 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800'
    };
    return classes[status] || classes['pending'];
}

function getStatusIcon(status) {
    const icons = {
        'pending': 'fas fa-clock',
        'completed': 'fas fa-check',
        'failed': 'fas fa-times',
        'cancelled': 'fas fa-ban'
    };
    return icons[status] || 'fas fa-question';
}

function getStatusText(status) {
    const texts = {
        'pending': 'Chờ duyệt',
        'completed': 'Đã duyệt',
        'failed': 'Bị từ chối',
        'cancelled': 'Đã hủy'
    };
    return texts[status] || 'Không xác định';
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}
