document.addEventListener('DOMContentLoaded', function () {
    const userId = window.currentUserId || window.currentCustomerId; // Biến này cần được định nghĩa trong view của bạn

    if (!userId) {
        console.log("User not logged in, skipping notification setup.");
        return;
    }

    // --- 1. Fetch notifications on page load ---
    fetchNotifications();

    // --- 2. Listen for realtime notifications with Laravel Echo ---
    if (window.Echo) {
        window.Echo.private(`App.Models.User.${userId}`)
            .notification((notification) => {
                console.log('New notification received:', notification);
                showToastNotification(notification.message);
                fetchNotifications(); // Fetch lại để cập nhật danh sách
            });
        console.log(`Subscribed to notification channel: App.Models.User.${userId}`);
    } else {
        console.error('Laravel Echo not found. Real-time notifications will not work.');
        // Fallback: Lấy thông báo mỗi 30 giây nếu Echo không hoạt động
        setInterval(fetchNotifications, 30000);
    }

    // --- Hàm để lấy và cập nhật giao diện thông báo ---
    // Định nghĩa fetchNotifications như một hàm toàn cục để có thể gọi từ các file khác
    window.fetchNotifications = function() {
        fetch('/customer/notifications', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            updateNotificationUI(data.html, data.unreadCount);
        })
        .catch(error => console.error('Error fetching notifications:', error));
    }
    
    // Gọi hàm toàn cục để tương thích với code hiện tại
    function fetchNotifications() {
        window.fetchNotifications();
    }

    // --- Hàm để cập nhật UI ---
    function updateNotificationUI(html, unreadCount) {
        const notificationList = document.getElementById('customer-notification-list');
        const notificationBadges = document.querySelectorAll('.notification-unread-count');

        if (notificationList) {
            notificationList.innerHTML = html;
        }

        if (notificationBadges.length > 0) {
            notificationBadges.forEach(badge => {
                if (unreadCount > 0) {
                    badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                    badge.classList.remove('hidden');
                } else {
                    badge.textContent = '0';
                    if (!badge.classList.contains('always-show')) {
                        badge.classList.add('hidden');
                    }
                }
            });
        }

        // Gọi hiệu ứng rung chuông nếu số lượng thông báo tăng
        const lastUnreadCount = parseInt(localStorage.getItem('lastUnreadCount') || '0');
        if (unreadCount > lastUnreadCount) {
            if (typeof window.triggerBellShake === 'function') {
                window.triggerBellShake();
            } else if (typeof triggerBellShake === 'function') {
                triggerBellShake();
            }
        }
        localStorage.setItem('lastUnreadCount', unreadCount);
    }

    // --- Hàm hiển thị toast notification đơn giản ---
    function showToastNotification(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed top-5 right-5 bg-green-500 text-white py-2 px-4 rounded-lg shadow-lg animate-fade-in-down';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.addEventListener('transitionend', () => toast.remove());
        }, 3000);
    }

    // --- Đánh dấu đã đọc khi click ---
    document.addEventListener('click', function(e) {
        // Tìm element cha gần nhất có data-notification-id
        const notificationItem = e.target.closest('[data-notification-id]');
        if (notificationItem) {
            const notificationId = notificationItem.dataset.notificationId;
            const isRead = notificationItem.dataset.isRead === 'true';

            if (notificationId && !isRead) {
                markAsRead(notificationId, notificationItem);
            }
        }
    });

    function markAsRead(notificationId, element) {
        fetch(`/customer/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                element.classList.remove('bg-blue-50'); // Hoặc class cho trạng thái chưa đọc
                element.classList.add('read');
                element.dataset.isRead = 'true';
                
                // Cập nhật lại số lượng chưa đọc
                if (typeof window.fetchNotifications === 'function') {
                    window.fetchNotifications(); // Fetch lại để cập nhật chính xác
                } else {
                    // Fallback nếu không có hàm fetchNotifications
                    const notificationBadges = document.querySelectorAll('.notification-unread-count');
                    if(notificationBadges.length > 0) {
                        notificationBadges.forEach(badge => {
                            let currentCount = parseInt(badge.textContent, 10);
                            if (!isNaN(currentCount) && currentCount > 0) {
                                currentCount--;
                                if (currentCount > 0) {
                                    badge.textContent = currentCount > 99 ? '99+' : currentCount;
                                } else {
                                    badge.textContent = '0';
                                    if (!badge.classList.contains('always-show')) {
                                        badge.classList.add('hidden');
                                    }
                                }
                            }
                        });
                    }
                }
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
    }
});