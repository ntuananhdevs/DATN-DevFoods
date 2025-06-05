# Avatar Upload Service Documentation

## Tổng quan

`AvatarUploadService` được cải tiến để tải avatar từ Google về local storage trước, sau đó upload lên S3. Điều này giúp tránh các vấn đề với việc download trực tiếp từ Google và cải thiện độ tin cậy.

## Cách hoạt động

### 1. Quá trình Upload Avatar từ Google

```php
$avatarUploadService = new AvatarUploadService();
$s3Url = $avatarUploadService->uploadGoogleAvatar($googlePhotoURL, $userEmail);
```

**Các bước thực hiện:**

1. **Download về Local**: Tải ảnh từ Google URL về temporary file trong `storage/app/temp/avatars/`
2. **Validation**: Kiểm tra file có phải là ảnh hợp lệ không
3. **Upload lên S3**: Đọc file local và upload lên S3 bucket
4. **Cleanup**: Xóa file temporary local
5. **Return URL**: Trả về S3 URL hoặc fallback về Google URL nếu thất bại

### 2. Cải tiến trong Download

- **User Agent**: Sử dụng Chrome user agent để tránh block
- **Headers**: Thêm headers tương thích với browser
- **Timeout**: Tăng timeout cho ảnh lớn (60s download, 15s connect)
- **SSL**: Tắt SSL verification cho development
- **Quality**: Tự động tối ưu chất lượng ảnh (400px)

### 3. Validation Ảnh

Service kiểm tra file signature để đảm bảo là ảnh hợp lệ:
- JPEG: `\xFF\xD8\xFF`
- PNG: `\x89PNG\r\n\x1a\n`
- GIF: `GIF87a` hoặc `GIF89a`
- WebP: `RIFF...WEBP`

### 4. Cleanup Temporary Files

Automatic cleanup được thực hiện qua:
- **Command**: `php artisan avatar:cleanup-temp`
- **Schedule**: Chạy mỗi giờ tự động
- **Retention**: Xóa files cũ hơn 1 giờ

## Configuration

### S3 Settings (.env)

```env
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket_name
AWS_URL=https://your-bucket.s3.amazonaws.com
AWS_SSL_VERIFY=false  # For development
```

### Filesystems Config

File `config/filesystems.php` đã được cấu hình với timeout và SSL settings phù hợp.

## Commands

### Manual Cleanup

```bash
# Cleanup with confirmation
php artisan avatar:cleanup-temp

# Force cleanup without confirmation
php artisan avatar:cleanup-temp --force
```

### Scheduled Tasks

Trong `app/Console/Kernel.php`:
```php
$schedule->command('avatar:cleanup-temp --force')->hourly();
```

## Error Handling

- **Download Fails**: Fallback về Google URL gốc
- **S3 Upload Fails**: Log error và fallback về Google URL
- **Validation Fails**: Log warning và reject file
- **Cleanup Errors**: Log error nhưng không crash app

## Logging

Service log chi tiết tất cả các bước:
- Download progress và errors
- File validation results
- S3 upload status
- Cleanup operations

## Testing

Chạy tests:
```bash
php artisan test tests/Unit/AvatarUploadServiceTest.php
```

## Monitoring

Monitor qua Laravel logs:
- `storage/logs/laravel.log`: Tất cả logs
- Tìm `AvatarUploadService` để filter logs liên quan

## Performance Notes

- **Async**: Sử dụng qua Job queue để không block request
- **Memory**: Xử lý file từng phần, không load toàn bộ vào memory
- **Cleanup**: Tự động dọn dẹp tránh đầy disk
- **Fallback**: Luôn có fallback URL để đảm bảo UX

## Troubleshooting

### Common Issues:

1. **Download timeout**: Tăng `CURLOPT_TIMEOUT` trong service
2. **S3 upload fails**: Kiểm tra AWS credentials và permissions
3. **Temp files accumulate**: Đảm bảo cron job chạy được
4. **SSL errors**: Set `AWS_SSL_VERIFY=false` trong .env 