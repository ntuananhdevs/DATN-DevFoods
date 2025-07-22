document.addEventListener('DOMContentLoaded', function () {
    const userId = window.currentUserId; // Biến này cần được định nghĩa trong view của bạn

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
    function fetchNotifications() {
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

    // --- Hàm để cập nhật UI ---
    function updateNotificationUI(html, unreadCount) {
        const notificationList = document.getElementById('customer-notification-list');
        const notificationBadge = document.getElementById('customer-notification-badge');

        if (notificationList) {
            notificationList.innerHTML = html;
        }

        if (notificationBadge) {
            if (unreadCount > 0) {
                notificationBadge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                notificationBadge.classList.remove('hidden');
            } else {
                notificationBadge.classList.add('hidden');
            }
        }
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
                element.dataset.isRead = 'true';
                
                // Cập nhật lại số lượng chưa đọc
                const notificationBadge = document.getElementById('customer-notification-badge');
                if(notificationBadge) {
                    let currentCount = parseInt(notificationBadge.textContent, 10);
                    if (!isNaN(currentCount) && currentCount > 0) {
                        currentCount--;
                        if (currentCount > 0) {
                             notificationBadge.textContent = currentCount > 9 ? '9+' : currentCount;
                        } else {
                            notificationBadge.classList.add('hidden');
                        }
                    }
                }
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
    }
}); 