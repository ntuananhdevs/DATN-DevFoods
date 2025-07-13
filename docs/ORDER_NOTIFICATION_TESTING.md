# Hướng dẫn test thông báo đơn hàng mới

## Cài đặt

1. **Service Worker**: Đã được tạo tại `public/js/branch/order-notification-sw.js`
2. **JavaScript**: Đã được thêm vào layout branch tại `public/js/branch/orders-realtime-simple.js`
3. **Layout**: Đã được cập nhật để include JavaScript và meta tags

## Cách test

### 1. Test đơn hàng mới
1. Mở trang `/branch/orders` trong trình duyệt
2. Chạy script test: `php test-order-notification.php`
3. Kiểm tra:
   - Toast notification xuất hiện
   - Order card mới được thêm vào grid
   - Console log hiển thị thông tin

### 2. Test tìm tài xế
1. Mở trang `/branch/orders` trong trình duyệt
2. Bấm nút "Xác nhận" trên order card
3. Kiểm tra:
   - Nút chuyển thành "Đang tìm tài xế..." với loading state
   - Card chuyển sang tab "Chờ tài xế"
   - Toast thông báo "Đã xác nhận đơn hàng, đang tìm tài xế..."

### 3. Test tài xế nhận đơn
1. Sau khi bấm xác nhận, chạy script test: `php test-driver-finding.php`
2. Kiểm tra:
   - Nút chuyển thành "Tài xế đã nhận" (màu xanh)
   - Hiển thị thông tin tài xế (tên, SĐT)
   - Toast thông báo "Tài xế [tên] đã nhận đơn hàng #[mã]"

### 4. Test trên các trang khác
1. Mở bất kỳ trang branch nào khác (ví dụ: `/branch/dashboard`, `/branch/categories`)
2. Chạy script test: `php test-order-notification.php`
3. Kiểm tra:
   - Browser notification xuất hiện
   - Click vào notification sẽ chuyển đến trang orders

### 5. Test Service Worker
1. Mở Developer Tools > Application > Service Workers
2. Kiểm tra Service Worker đã được đăng ký
3. Test notification click để chuyển trang

## Troubleshooting

### Không có thông báo
1. Kiểm tra console log có lỗi gì không
2. Kiểm tra quyền notification trong browser
3. Kiểm tra Pusher connection
4. Kiểm tra branch ID trong meta tag

### Lỗi 403 Forbidden
1. Kiểm tra CSRF token
2. Kiểm tra user authentication
3. Kiểm tra channel authorization

### Lỗi 404 Not Found
1. Kiểm tra broadcasting auth route đã được enable
2. Kiểm tra Pusher configuration

## Cấu hình

### Pusher Config
```php
// config/broadcasting.php
'pusher' => [
    'driver' => 'pusher',
    'key' => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'encrypted' => true,
    ],
],
```

### Channel Authorization
```php
// routes/channels.php
Broadcast::channel('private-branch.{branchId}', function ($user, $branchId) {
    return $user->branch_id == $branchId;
});
```

## Files đã được cập nhật

1. `resources/views/layouts/branch/contentLayoutMaster.blade.php` - Thêm meta tags và JavaScript
2. `public/js/branch/orders-realtime-simple.js` - Logic xử lý thông báo và tìm tài xế
3. `public/js/branch/order-notification-sw.js` - Service Worker
4. `app/Events/Branch/NewOrderReceived.php` - Event đơn hàng mới với distance_km
5. `app/Events/Branch/DriverFound.php` - Event tài xế nhận đơn
6. `app/Http/Controllers/Branch/BranchOrderController.php` - Method confirmOrder và findNearestDriver
7. `routes/branch.php` - Route POST /branch/orders/{id}/confirm
8. `test-order-notification.php` - Script test đơn hàng mới
9. `test-driver-finding.php` - Script test tài xế nhận đơn

## Lưu ý

- Đảm bảo user đã đăng nhập với role manager
- Đảm bảo branch ID đúng với user hiện tại
- Test trên HTTPS để Service Worker hoạt động tốt
- Clear cache browser nếu có vấn đề với Service Worker 