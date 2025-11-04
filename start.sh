#!/bin/bash
set -e

# Create storage link
php artisan storage:link || true

# Start PHP built-in server
php -S 0.0.0.0:$PORT -t public

