# Hướng Dẫn Xử Lý Đa Luồng cho FindDriverForOrderJob

## Tổng Quan
Hệ thống đã được tối ưu hóa để xử lý nhiều đơn hàng đồng thời thông qua queue workers và database locking.

## Cấu Hình Đa Luồng

### 1. Queue Configuration
- **Queue Driver**: Database (đã cấu hình trong .env)
- **Connection**: `QUEUE_CONNECTION=database`
- **Jobs Table**: Đã có sẵn trong database

### 2. Job Configuration
```php
// Trong FindDriverForOrderJob.php
public $timeout = 120;           // Timeout cho mỗi job
public $tries = 3;               // Số lần thử lại
public $backoff = [10, 30, 60];  // Delay giữa các retry
```

### 3. Tối Ưu Hóa Hiệu Suất
- **Delay giảm**: 10s → 3s giữa các lần thử
- **Số lần thử tăng**: 30 → 60 lần
- **Cache**: 30 giây cho kết quả tìm kiếm driver
- **Database locks**: Tránh duplicate assignment

## Cách Chạy Đa Luồng

### Option 1: Sử dụng Batch File (Windows)
```bash
# Chạy file batch
.\start-queue-workers.bat
```

### Option 2: Sử dụng PowerShell Script
```powershell
# Chạy PowerShell script
.\start-queue-workers.ps1
```

### Option 3: Manual Commands
```bash
# Mở 4 terminal và chạy từng worker
php artisan queue:work database --queue=default --sleep=1 --tries=3 --max-time=3600 --memory=512
```

## Lợi Ích Của Đa Luồng

1. **Xử Lý Đồng Thời**: Nhiều đơn hàng có thể tìm driver cùng lúc
2. **Tăng Throughput**: Xử lý được nhiều đơn hàng hơn trong cùng thời gian
3. **Giảm Latency**: Thời gian chờ tìm driver giảm đáng kể
4. **Fault Tolerance**: Nếu 1 worker gặp lỗi, các worker khác vẫn hoạt động

## Monitoring & Debugging

### Kiểm Tra Queue Status
```bash
# Xem số job đang chờ
php artisan queue:monitor database

# Xem failed jobs
php artisan queue:failed

# Restart failed jobs
php artisan queue:retry all
```

### Log Files
- **Application logs**: `storage/logs/laravel.log`
- **Job logs**: Tìm kiếm "FindDriverForOrderJob" trong logs

## Best Practices

1. **Số Workers**: Khuyến nghị 4-8 workers tùy theo server capacity
2. **Memory Limit**: 512MB per worker
3. **Max Time**: 3600s (1 hour) per worker session
4. **Sleep Time**: 1s giữa các job để tránh overload

## Troubleshooting

### Nếu Jobs Không Chạy
1. Kiểm tra `QUEUE_CONNECTION=database` trong .env
2. Đảm bảo jobs table tồn tại: `php artisan migrate`
3. Restart queue workers

### Nếu Có Duplicate Assignments
1. Kiểm tra database locks trong transaction
2. Xem logs để trace race conditions
3. Tăng cache time nếu cần

### Performance Issues
1. Tăng số workers nếu có nhiều đơn hàng
2. Giảm cache time nếu data thay đổi nhanh
3. Monitor database connection pool

## Kết Luận
Hệ thống hiện tại có thể xử lý hàng trăm đơn hàng đồng thời với hiệu suất cao và độ tin cậy tốt.