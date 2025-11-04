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

# Test Laravel bootstrap
echo "Testing Laravel bootstrap..."
php -r "require 'vendor/autoload.php'; \$app = require 'bootstrap/app.php'; echo 'Laravel bootstrap: OK\n';" || echo "WARNING: Laravel bootstrap test failed"

# Set error reporting
export PHP_IDE_CONFIG="serverName=railway"
export APP_DEBUG=false

# Start PHP built-in server with error logging
echo "Starting PHP server on port $PORT..."
php -S 0.0.0.0:$PORT -t public 2>&1 | tee /tmp/php-server.log

