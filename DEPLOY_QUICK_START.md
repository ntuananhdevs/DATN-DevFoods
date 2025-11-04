# ⚡ Quick Start - Deploy lên Railway

## Bước Nhanh (5 phút)

### 1. Push code lên GitHub
```bash
git add .
git commit -m "Prepare for Railway deployment"
git push origin main
```

### 2. Tạo Project trên Railway
1. Vào [railway.app](https://railway.app) → Đăng nhập
2. **New Project** → **Deploy from GitHub repo**
3. Chọn repository của bạn

### 3. Thêm Database
1. Trong project → **+ New** → **Database** → **Add MySQL**
2. Copy `DATABASE_URL` từ database service

### 4. Set Environment Variables
Vào **Settings** → **Variables**, thêm:

```env
APP_NAME=DevFoods
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.railway.app
DATABASE_URL=mysql://user:pass@host:port/db
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database
```

### 5. Deploy
Railway sẽ tự động deploy khi bạn push code. Hoặc click **Deploy** trong dashboard.

### 6. Generate APP_KEY
Sau khi deploy, chạy:
```bash
railway run php artisan key:generate --force
```

### 7. Chạy Migrations
```bash
railway run php artisan migrate --force
```

## ✅ Xong!

Xem hướng dẫn chi tiết trong `RAILWAY_DEPLOY.md`

