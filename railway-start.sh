#!/bin/bash
set -e

# Chờ database sẵn sàng (nếu cần)
echo "Waiting for database..."

# Generate application key nếu chưa có
php artisan key:generate --force || true

# Chạy migrations
php artisan migrate --force || true

# Tạo symbolic link cho storage
php artisan storage:link || true

# Cache config, routes, views
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Khởi động Apache với port từ Railway
PORT=${PORT:-8080}
echo "Starting Apache on port $PORT"

# Cấu hình Apache để listen trên port từ Railway
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/:80/:$PORT/g" /etc/apache2/sites-available/*.conf

# Khởi động Apache
apache2-foreground

