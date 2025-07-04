#!/bin/sh
set -e

echo "🚀 Starting Laravel Headless CMS..."

# Pastikan direktori penting ada dan permission benar
echo "🔧 Preparing folders..."
mkdir -p \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/framework/testing \
    /var/www/html/storage/framework/livewire-tmp \
    /var/www/html/storage/app/public

# Fix permissions (pakai root)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R ug+rwX /var/www/html/storage /var/www/html/bootstrap/cache

# Buat symlink public/storage kalau belum ada
if [ ! -L /var/www/html/public/storage ]; then
    echo "🔗 Creating symlink for public/storage"
    ln -s /var/www/html/storage/app/public /var/www/html/public/storage
fi

# Tunggu DB (opsional)
sleep 2

# Artisan commands as www-data
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "🔑 Generating application key..."
    gosu www-data php artisan key:generate --force
fi

echo "🗄️ Running database migrations..."
gosu www-data php artisan migrate --force

if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Optimizing for production..."
    gosu www-data php artisan config:cache
    gosu www-data php artisan route:cache
    gosu www-data php artisan view:cache
fi

echo "🔍 Indexing content for search..."
gosu www-data php artisan scout:import "App\\Models\\Post" || true
gosu www-data php artisan scout:import "App\\Models\\Page" || true
gosu www-data php artisan scout:import "App\\Models\\Category" || true

echo "✅ Laravel Headless CMS is ready!"

# Jalankan perintah utama (supervisor/nginx/php-fpm) tetap sebagai root
exec "$@"
