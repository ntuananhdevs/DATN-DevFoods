#!/bin/bash
set -e

echo "Starting application..."
echo "PORT: $PORT"

# Check if APP_KEY exists
if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY is not set. Generating..."
    php artisan key:generate --force || true
fi

# Create storage link
echo "Creating storage link..."
php artisan storage:link || true

# Clear caches to avoid issues
echo "Clearing caches..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true

# Set error reporting
export PHP_IDE_CONFIG="serverName=railway"
export APP_DEBUG=false

# Start PHP built-in server with error logging
echo "Starting PHP server on port $PORT..."
php -S 0.0.0.0:$PORT -t public 2>&1 | tee /tmp/php-server.log

