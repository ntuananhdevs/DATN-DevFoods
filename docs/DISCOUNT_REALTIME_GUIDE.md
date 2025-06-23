# Discount Codes Realtime Updates Guide

## Tổng quan

Hệ thống realtime updates cho discount codes sử dụng Pusher để cập nhật thông tin mã giảm giá theo thời gian thực từ admin panel đến customer pages.

## Các tính năng

### 1. Realtime Notifications
- Thông báo khi tạo mã giảm giá mới
- Thông báo khi cập nhật mã giảm giá
- Thông báo khi xóa mã giảm giá
- Thông báo khi thay đổi trạng thái mã giảm giá

### 2. Visual Updates
- Animation cho discount codes container
- Animation cho product cards có discount codes
- Auto-refresh discount information

## Cấu trúc Files

### Backend
- `app/Events/DiscountUpdated.php` - Event class cho discount updates
- `app/Http/Controllers/Admin/DiscountCodeController.php` - Controller với broadcast events
- `routes/channels.php` - Channel authorization cho discounts

### Frontend
- `public/js/Customer/discount-updates.js` - JavaScript listener cho discount updates
- `resources/views/customer/shop/index.blade.php` - Include script
- `resources/views/customer/shop/show.blade.php` - Include script

## Cách hoạt động

### 1. Admin Actions
Khi admin thực hiện các action sau, hệ thống sẽ broadcast event:

- **Create**: `broadcast(new DiscountUpdated($discountCode, 'created'))`
- **Update**: `broadcast(new DiscountUpdated($discountCode, 'updated'))`
- **Delete**: `broadcast(new DiscountUpdated($discountCode, 'deleted'))`
- **Toggle Status**: `broadcast(new DiscountUpdated($discountCode, 'updated'))`
- **Bulk Operations**: Broadcast cho từng discount code

### 2. Client Listening
JavaScript listener sẽ:
- Kết nối đến Pusher channel `discounts`
- Lắng nghe event `discount-updated`
- Hiển thị notification
- Cập nhật UI với animation

## Cấu hình

### 1. Pusher Configuration
Đảm bảo Pusher đã được cấu hình trong `.env`:
```env
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=your_cluster
```

### 2. Broadcasting Configuration
Trong `config/broadcasting.php`, đảm bảo Pusher driver được cấu hình:
```php
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

## Testing

### 1. Test Pusher Connection
Sử dụng file `public/js/test-pusher.html` để test kết nối Pusher.

### 2. Test Events
1. Mở admin panel
2. Tạo/cập nhật/xóa discount code
3. Kiểm tra console log ở customer page
4. Xem notification và animation

## Troubleshooting

### 1. Events không được broadcast
- Kiểm tra Pusher configuration
- Kiểm tra BroadcastServiceProvider đã được register
- Kiểm tra queue worker đang chạy

### 2. Client không nhận được events
- Kiểm tra Pusher key và cluster
- Kiểm tra channel authorization
- Kiểm tra console errors

### 3. Performance Issues
- Sử dụng `->toOthers()` để tránh broadcast cho chính user đang thực hiện action
- Cân nhắc sử dụng queue cho broadcast events

## Security

- Channel `discounts` là public channel, cho phép tất cả authenticated users
- Events chỉ chứa thông tin cần thiết, không expose sensitive data
- Sử dụng `->toOthers()` để tránh broadcast cho chính user

## Future Enhancements

1. **Selective Updates**: Chỉ update discount codes liên quan đến sản phẩm hiện tại
2. **Optimistic Updates**: Update UI trước khi nhận confirmation từ server
3. **Offline Support**: Cache discount codes và sync khi online
4. **Analytics**: Track discount code usage và performance 
 