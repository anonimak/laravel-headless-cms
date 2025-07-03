#!/bin/bash
set -e
echo "🚀 Starting Laravel Headless CMS..."

if [ ! -f .env ]; then
    echo "📋 Creating .env file..."
    cp .env.example .env
fi

if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

echo "🗄️ Running database migrations..."
# --no-dev is not a valid flag for migrate, so it's removed.
php artisan migrate --force

echo "🔗 Creating storage symlink..."
php artisan storage:link || true
if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Optimizing for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi
echo "🔍 Indexing content for search..."
php artisan scout:import "App\\Models\\Post" || true
php artisan scout:import "App\\Models\\Page" || true
php artisan scout:import "App\\Models\\Category" || true
echo "✅ Laravel Headless CMS is ready!"
echo "🌐 API available at: http://localhost/api"
exec "$@"