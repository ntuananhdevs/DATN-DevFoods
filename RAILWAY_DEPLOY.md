# 🚀 Hướng Dẫn Deploy DevFoods lên Railway

Hướng dẫn chi tiết để deploy dự án Laravel DevFoods lên Railway.

## 📋 Yêu Cầu

- Tài khoản Railway (đăng ký tại [railway.app](https://railway.app))
- GitHub account (để kết nối repository)
- Các API keys và credentials cần thiết (Pusher, Firebase, AWS, VNPay, etc.)

## 🎯 Các Bước Deploy

### Bước 1: Chuẩn Bị Repository

1. Đảm bảo code đã được push lên GitHub/GitLab
2. Kiểm tra file `.gitignore` đã có các file không cần thiết:
   ```
   /vendor
   /node_modules
   .env
   .env.backup
   ```

### Bước 2: Tạo Project trên Railway

1. Đăng nhập vào [Railway Dashboard](https://railway.app/dashboard)
2. Click **"New Project"**
3. Chọn **"Deploy from GitHub repo"**
4. Chọn repository của bạn
5. Railway sẽ tự động detect Laravel và cấu hình

### Bước 3: Cấu Hình Database

1. Trong Railway project, click **"+ New"** → **"Database"** → **"Add MySQL"**
2. Railway sẽ tự động tạo MySQL database
3. Copy `DATABASE_URL` từ database service (hoặc các biến `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)

### Bước 4: Cấu Hình Environment Variables

Vào **Settings** → **Variables** và thêm các biến môi trường sau:

#### Biến Bắt Buộc:
```env
APP_NAME=DevFoods
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.railway.app
APP_KEY=base64:... (sẽ được generate tự động)

# Database (hoặc dùng DATABASE_URL từ Railway)
DB_CONNECTION=mysql
DATABASE_URL=mysql://user:password@host:port/database

# Session & Cache
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mail_username
MAIL_PASSWORD=your_mail_password
MAIL_FROM_ADDRESS=noreply@devfoods.com
MAIL_FROM_NAME=DevFoods
```

#### Biến Tùy Chọn (tùy theo tính năng bạn sử dụng):
```env
# Pusher (cho realtime)
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_app_key
PUSHER_APP_SECRET=your_pusher_app_secret
PUSHER_APP_CLUSTER=ap1

# AWS S3 (cho file storage)
FILESYSTEM_DRIVER=s3
AWS_ACCESS_KEY_ID=your_aws_access_key
AWS_SECRET_ACCESS_KEY=your_aws_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket_name

# Firebase
FIREBASE_PROJECT_ID=your_firebase_project_id
FIREBASE_API_KEY=your_firebase_api_key
FIREBASE_AUTH_DOMAIN=your_project.firebaseapp.com
FIREBASE_STORAGE_BUCKET=your_project.appspot.com
FIREBASE_MESSAGING_SENDER_ID=your_sender_id
FIREBASE_APP_ID=your_app_id

# VNPay
VNPAY_TMN_CODE=your_tmn_code
VNPAY_HASH_SECRET=your_hash_secret
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_RETURN_URL=https://your-app-name.railway.app/vnpay/return

# Cloudflare Turnstile
TURNSTILE_SITE_KEY=your_turnstile_site_key
TURNSTILE_SECRET_KEY=your_turnstile_secret_key
```

### Bước 5: Generate Application Key

1. Vào **Settings** → **Variables**
2. Thêm biến `APP_KEY` hoặc Railway sẽ tự động generate
3. Nếu cần generate thủ công, chạy trong Railway CLI:
   ```bash
   railway run php artisan key:generate
   ```

### Bước 6: Chạy Migrations

Railway sẽ tự động chạy migrations khi deploy nếu bạn đã cấu hình trong `railway.json`.

Hoặc chạy thủ công:
1. Vào service của bạn trên Railway
2. Click **"Deployments"** → **"View Logs"**
3. Chạy command: `railway run php artisan migrate --force`

### Bước 7: Cấu Hình Build & Start Commands

Railway sẽ tự động detect Laravel và sử dụng Nixpacks. Bạn có thể customize trong file `railway.json`:

```json
{
  "build": {
    "builder": "NIXPACKS",
    "buildCommand": "composer install --no-dev --optimize-autoloader && npm ci && npm run production"
  },
  "deploy": {
    "startCommand": "php artisan migrate --force && php artisan storage:link && php artisan config:cache && php artisan route:cache && php artisan view:cache && php -S 0.0.0.0:$PORT -t public"
  }
}
```

### Bước 8: Deploy

1. Railway sẽ tự động deploy khi bạn push code lên GitHub
2. Hoặc click **"Deploy"** trong Railway dashboard
3. Đợi build hoàn tất (thường mất 3-5 phút)
4. Kiểm tra logs để đảm bảo không có lỗi

### Bước 9: Cấu Hình Custom Domain (Tùy Chọn)

1. Vào **Settings** → **Networking**
2. Click **"Generate Domain"** để có domain miễn phí
3. Hoặc thêm custom domain của bạn
4. Cập nhật `APP_URL` trong environment variables

## 🔧 Cấu Hình Queue Worker (Nếu Cần)

Nếu bạn sử dụng queue workers, cần tạo service riêng:

1. Trong Railway project, click **"+ New"** → **"Empty Service"**
2. Kết nối với cùng repository
3. Trong **Settings** → **Variables**, thêm:
   ```env
   START_COMMAND=php artisan queue:work --tries=3 --timeout=90
   ```
4. Đảm bảo `QUEUE_CONNECTION=database` đã được set

## 📝 Lưu Ý Quan Trọng

1. **Storage**: Railway không lưu trữ file vĩnh viễn. Nên sử dụng S3 hoặc storage service khác cho file uploads.

2. **Database Migrations**: Chạy migrations tự động khi deploy có thể gây rủi ro. Nên chạy thủ công trong production.

3. **Logs**: Xem logs trong Railway dashboard → **"Deployments"** → **"View Logs"**

4. **Environment**: Luôn set `APP_ENV=production` và `APP_DEBUG=false` trong production

5. **Assets**: Đảm bảo đã build assets (`npm run production`) trước khi deploy

6. **Permissions**: Railway tự động set permissions cho storage và cache folders

## 🐛 Troubleshooting

### Lỗi "No application encryption key"

Giải pháp: Thêm `APP_KEY` vào environment variables hoặc chạy:
```bash
railway run php artisan key:generate --force
```

### Lỗi Database Connection

Giải pháp: 
- Kiểm tra `DATABASE_URL` hoặc các biến `DB_*` đã được set đúng
- Đảm bảo database service đã được tạo và running

### Lỗi "Storage link not found"

Giải pháp: Chạy:
```bash
railway run php artisan storage:link
```

### Lỗi "Permission denied" cho storage

Giải pháp: Railway tự động set permissions, nhưng nếu vẫn lỗi, thêm vào start command:
```bash
chmod -R 775 storage bootstrap/cache
```

### Build fails

Giải pháp:
- Kiểm tra `composer.json` và `package.json` có đúng không
- Xem build logs để tìm lỗi cụ thể
- Đảm bảo PHP version phù hợp (8.2)

## 📚 Tài Liệu Tham Khảo

- [Railway Documentation](https://docs.railway.app)
- [Laravel Deployment Guide](https://laravel.com/docs/deployment)
- [Railway Environment Variables](https://docs.railway.app/develop/variables)

## ✅ Checklist Trước Khi Deploy

- [ ] Code đã được push lên GitHub
- [ ] Tất cả environment variables đã được set
- [ ] Database đã được tạo và kết nối
- [ ] APP_KEY đã được generate
- [ ] Assets đã được build (npm run production)
- [ ] Mail service đã được cấu hình
- [ ] Pusher/Firebase/AWS credentials đã được set
- [ ] APP_URL đã được set đúng với Railway domain
- [ ] APP_DEBUG=false và APP_ENV=production

---

Chúc bạn deploy thành công! 🎉

