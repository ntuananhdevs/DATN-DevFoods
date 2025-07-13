// Service Worker for order notifications
const CACHE_NAME = 'order-notifications-v1';

self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                return cache.addAll([
                    '/images/default-avatar.png'
                ]);
            })
    );
});

self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

self.addEventListener('push', function(event) {
    if (event.data) {
        const data = event.data.json();
        
        const options = {
            body: data.body || 'Bạn có đơn hàng mới',
            icon: '/images/default-avatar.png',
            badge: '/images/default-avatar.png',
            tag: 'new-order',
            requireInteraction: true,
            actions: [
                {
                    action: 'view',
                    title: 'Xem đơn hàng',
                    icon: '/images/default-avatar.png'
                },
                {
                    action: 'close',
                    title: 'Đóng',
                    icon: '/images/default-avatar.png'
                }
            ]
        };

        event.waitUntil(
            self.registration.showNotification('Đơn hàng mới', options)
        );
    }
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    if (event.action === 'view' || !event.action) {
        event.waitUntil(
            clients.openWindow('/branch/orders')
        );
    }
});

self.addEventListener('message', function(event) {
    if (event.data && event.data.type === 'NEW_ORDER') {
        const order = event.data.order;
        
        const options = {
            body: `Đơn hàng #${order.code} - ${order.payment?.method || 'Chưa xác định'} - ${order.items_count || 0} món`,
            icon: '/images/default-avatar.png',
            badge: '/images/default-avatar.png',
            tag: 'new-order',
            requireInteraction: true,
            actions: [
                {
                    action: 'view',
                    title: 'Xem đơn hàng',
                    icon: '/images/default-avatar.png'
                },
                {
                    action: 'close',
                    title: 'Đóng',
                    icon: '/images/default-avatar.png'
                }
            ]
        };

        event.waitUntil(
            self.registration.showNotification('Đơn hàng mới', options)
        );
    }
}); 