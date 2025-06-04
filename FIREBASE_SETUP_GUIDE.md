# Firebase Google Authentication Setup Guide

## 1. Cấu hình Firebase Project

### Bước 1: Tạo Firebase Project
1. Truy cập [Firebase Console](https://console.firebase.google.com/)
2. Tạo một project mới hoặc chọn project hiện có
3. Kích hoạt Authentication và chọn Google làm provider

### Bước 2: Lấy Web App Configuration
1. Vào Project Settings > General > Your apps
2. Chọn Web app hoặc tạo mới
3. Copy Firebase configuration object

### Bước 3: Cấu hình Google Authentication
1. Vào Authentication > Sign-in method
2. Enable Google provider
3. Nhập Google OAuth 2.0 client IDs

## 2. Cấu hình Laravel Backend

### Bước 1: Thêm biến môi trường vào file .env
```env
# Firebase Configuration
FIREBASE_PROJECT_ID=your-firebase-project-id
FIREBASE_API_KEY=your-firebase-api-key
FIREBASE_AUTH_DOMAIN=your-firebase-project-id.firebaseapp.com
FIREBASE_STORAGE_BUCKET=your-firebase-project-id.appspot.com
FIREBASE_MESSAGING_SENDER_ID=your-messaging-sender-id
FIREBASE_APP_ID=your-firebase-app-id
FIREBASE_MEASUREMENT_ID=your-measurement-id

# Firebase Features
FIREBASE_AUTH_ENABLED=true
FIREBASE_GOOGLE_AUTH_ENABLED=true
```

### Bước 2: Chạy Migration
```bash
php artisan migrate
```

### Bước 3: Clear Cache (nếu cần)
```bash
php artisan config:cache
php artisan cache:clear
```

## 3. Cấu hình Frontend

### Firebase SDK đã được tích hợp qua CDN:
- Firebase App: `https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js`
- Firebase Auth: `https://www.gstatic.com/firebasejs/9.0.0/firebase-auth-compat.js`

### File cấu hình JavaScript: `public/js/firebase-config.js`
- Tự động load cấu hình từ Laravel backend
- Khởi tạo Firebase khi trang load
- Xử lý Google Sign In/Out

## 4. Cách sử dụng

### Trong view login/register:
1. Thêm Firebase scripts vào section scripts
2. Gọi `handleGoogleLogin()` khi người dùng click nút Google

### API endpoints:
- `GET /api/firebase/config` - Lấy cấu hình Firebase
- `POST /api/auth/google` - Xử lý đăng nhập Google
- `GET /api/auth/status` - Kiểm tra trạng thái đăng nhập

## 5. Security Notes

1. **Domain Restriction**: Giới hạn domains trong Firebase Console
2. **API Key Restriction**: Giới hạn API key cho domain cụ thể
3. **CORS Configuration**: Đảm bảo CORS được cấu hình đúng
4. **CSRF Protection**: Sử dụng CSRF token trong AJAX requests

## 6. Testing

### Test Firebase Connection:
1. Mở Developer Tools
2. Check Console logs cho "Firebase initialized successfully"
3. Test Google login button

### Common Issues:
1. **CORS Error**: Kiểm tra domain trong Firebase Console
2. **Invalid API Key**: Xác minh API key trong .env
3. **Unauthorized Domain**: Thêm domain vào Authorized domains

## 7. Production Deployment

1. Cập nhật domains trong Firebase Console
2. Thiết lập SSL certificate
3. Cấu hình environment variables trong production
4. Test thoroughly trước khi deploy

## 8. File Structure

```
config/
├── firebase.php              # Firebase configuration

app/Http/Controllers/
├── FirebaseConfigController.php  # API controller cho config

public/js/
├── firebase-config.js        # Frontend Firebase logic

resources/views/customer/auth/
├── login.blade.php          # Login page với Google button
├── register.blade.php       # Register page với Google button

database/migrations/
├── *_add_social_auth_fields_to_users_table.php  # Database migration
``` 