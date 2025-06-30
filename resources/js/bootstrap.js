import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Xác định endpoint xác thực dựa trên URL của trang
let authEndpoint = '/broadcasting/auth'; // Mặc định cho customer
if (window.location.pathname.startsWith('/driver')) {
    authEndpoint = '/driver/broadcasting/auth'; // Dùng endpoint riêng cho driver
}

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true,
    // **THÊM DÒNG QUAN TRỌNG NÀY VÀO**
    authEndpoint: authEndpoint,
});

// === THÊM DÒNG QUAN TRỌNG NÀY VÀO ===
// Dòng này sẽ bắn ra một sự kiện tùy chỉnh trên toàn bộ trang
// để thông báo rằng "Echo đã sẵn sàng để sử dụng!"
document.dispatchEvent(new Event('echo:ready'));