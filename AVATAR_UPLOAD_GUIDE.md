# Google Avatar Upload to S3 - Setup Guide

## 🎯 Tính năng

Hệ thống tự động tải avatar từ Google và upload lên AWS S3 khi người dùng đăng nhập bằng Google.

## 📋 Workflow

### 1. Đăng nhập Google
- User click "Đăng nhập với Google"
- Firebase xác thực và trả về user data + avatar URL
- Laravel AuthController nhận data

### 2. Xử lý Avatar
- **Immediate**: Lưu Google avatar URL vào database ngay lập tức
- **Background**: Dispatch job để tải và upload lên S3
- **Update**: Sau khi upload S3 thành công, cập nhật avatar URL

### 3. Background Processing
- Job download avatar từ Google (chất lượng cao)
- Upload lên S3 với visibility public
- Cập nhật user avatar URL
- Xóa avatar cũ nếu cần

## 🛠️ Components

### Files Created/Updated:

1. **`app/Services/AvatarUploadService.php`**
   - Download image từ Google URL
   - Upload lên S3 với tên file unique
   - Delete avatar cũ
   - Handle lỗi và fallback

2. **`app/Jobs/UploadGoogleAvatarJob.php`**
   - Background job xử lý upload
   - Retry mechanism (3 lần)
   - Timeout 2 phút
   - Comprehensive logging

3. **`app/Http/Controllers/Customer/Auth/AuthController.php`**
   - Updated handleGoogleAuth method
   - Immediate avatar save + background upload
   - Proper error handling

## ⚙️ Configuration

### AWS S3 Setup (.env):
```env
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=your-region
AWS_BUCKET=your-bucket-name
AWS_URL=https://your-bucket.s3.amazonaws.com (optional)
```

### Queue Configuration:
```env
QUEUE_CONNECTION=database  # or redis
```

## 🚀 Usage

### For New Users:
1. User đăng nhập Google lần đầu
2. Avatar được lưu tạm thời với URL Google
3. Background job upload lên S3
4. Database được cập nhật với S3 URL

### For Existing Users:
1. Kiểm tra avatar có thay đổi không
2. Nếu có, cập nhật ngay lập tức
3. Background job upload phiên bản mới lên S3

## 📁 File Structure

```
app/
├── Services/
│   └── AvatarUploadService.php
├── Jobs/
│   └── UploadGoogleAvatarJob.php
└── Http/Controllers/Customer/Auth/
    └── AuthController.php (updated)

storage/
└── app/
    └── avatars/
        └── google/
            └── avatar_[hash]_[timestamp]_[random].jpg
```

## 🔍 Monitoring & Debugging

### Logs to Check:
```bash
# Avatar upload logs
tail -f storage/logs/laravel.log | grep "avatar"

# Job processing
tail -f storage/logs/laravel.log | grep "UploadGoogleAvatarJob"

# Google auth logs
tail -f storage/logs/laravel.log | grep "Google auth"
```

### Database Tables:
- `users.avatar` - Current avatar URL
- `jobs` - Background job queue
- `failed_jobs` - Failed uploads for debugging

## 🐛 Troubleshooting

### Common Issues:

1. **S3 Upload Failed**
   - Check AWS credentials
   - Verify bucket permissions
   - Check network connectivity

2. **Image Download Failed**
   - Google photo URL might be expired
   - Network timeout
   - Invalid URL format

3. **Job Failed**
   - Queue worker not running
   - Timeout exceeded
   - Memory limit

### Solutions:

```bash
# Start queue worker
php artisan queue:work

# Clear failed jobs
php artisan queue:flush

# Restart queue workers
php artisan queue:restart

# Test S3 connection
php artisan tinker
Storage::disk('s3')->put('test.txt', 'Hello S3');
```

## 📊 Performance

### Optimization Features:
- **Async Processing**: Upload doesn't block login
- **High Quality Images**: 400x400 resolution from Google
- **Retry Mechanism**: 3 attempts for failed uploads
- **Cleanup**: Old avatars are automatically deleted
- **Fallback**: Uses Google URL if S3 upload fails

### Expected Performance:
- Login response: < 1 second
- Avatar upload: 5-30 seconds (background)
- Image quality: 400x400 pixels
- File size: 20-100KB typically

## 🔐 Security

### Measures Implemented:
- Public S3 objects for direct browser access
- Unique file names prevent conflicts
- Content-Type validation
- Timeout limits prevent hanging
- Error logging for monitoring

### File Naming:
```
avatar_{md5(email)}_{timestamp}_{random8chars}.{extension}
```

## 🧪 Testing

### Manual Testing:
1. Login with Google (new user)
2. Check immediate avatar display
3. Wait for background job completion
4. Verify S3 URL in database
5. Login again (existing user)
6. Verify avatar update if changed

### Commands for Testing:
```bash
# Test Firebase config
php artisan firebase:test-config

# Test avatar upload directly
php artisan tinker
$service = app(App\Services\AvatarUploadService::class);
$url = $service->uploadGoogleAvatar('google-photo-url', 'test@email.com');

# Monitor job queue
php artisan queue:monitor
```

## 📈 Metrics to Track

- Avatar upload success rate
- Average upload time
- Failed job count
- S3 storage usage
- User satisfaction with avatar quality 