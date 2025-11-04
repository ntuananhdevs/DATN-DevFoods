# Sử dụng PHP 8.2-FPM làm base image
FROM php:8.2-fpm

# Cài các thư viện cần thiết cho GD, ZIP và MySQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Đặt thư mục làm việc
WORKDIR /var/www/html

# Copy toàn bộ code vào container
COPY . .

# Tạm thời tắt broadcast để tránh lỗi Pusher khi build
ENV BROADCAST_DRIVER=log

# Cài đặt Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Cài đặt các dependencies của Laravel
RUN composer install --no-dev --optimize-autoloader

# Tạo quyền ghi cho storage và bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache && chmod -R 775 storage bootstrap/cache

# Mở port 9000 cho PHP-FPM
EXPOSE 9000

# Khi container khởi động, tự động thực hiện setup Laravel
CMD php artisan key:generate --force && \
    php artisan migrate --force && \
    php artisan storage:link && \
    php artisan config:cache && \
    php-fpm
