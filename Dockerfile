# Use PHP 8.3 with Apache
FROM php:8.3-apache

# Set environment variables
ENV APP_ENV=${APP_ENV:-production}
ENV APP_DEBUG=${APP_DEBUG:-false}
ENV APP_KEY=${APP_KEY}
ENV APP_URL=${APP_URL:-http://localhost}
ENV DB_CONNECTION=${DB_CONNECTION:-sqlite}
ENV DB_HOST=${DB_HOST}
ENV DB_PORT=${DB_PORT}
ENV DB_DATABASE=${DB_DATABASE:-/var/www/html/database/database.sqlite}
ENV DB_USERNAME=${DB_USERNAME}
ENV DB_PASSWORD=${DB_PASSWORD}
ENV SCOUT_DRIVER=${SCOUT_DRIVER:-collection}
ENV CACHE_DRIVER=${CACHE_DRIVER:-file}
ENV SESSION_DRIVER=${SESSION_DRIVER:-file}
ENV QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}
ENV FILESYSTEM_DISK=${FILESYSTEM_DISK:-public}
ENV REDIS_HOST=${REDIS_HOST}
ENV REDIS_PORT=${REDIS_PORT:-6379}
ENV MAIL_MAILER=${MAIL_MAILER:-log}
ENV MAIL_HOST=${MAIL_HOST}
ENV MAIL_PORT=${MAIL_PORT}
ENV MAIL_USERNAME=${MAIL_USERNAME}
ENV MAIL_PASSWORD=${MAIL_PASSWORD}

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd

# Enable Apache modules
RUN a2enmod rewrite headers

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . /var/www/html

# Set ownership
RUN chown -R www-data:www-data /var/www/html

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Install Node.js dependencies and build assets
RUN npm install && npm run build

# Create necessary directories and set permissions
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views storage/app/public/media bootstrap/cache database \
    && touch database/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache \
    && chmod 664 database/database.sqlite

# Configure Apache DocumentRoot
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Create Apache virtual host configuration
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
        Options Indexes FollowSymLinks\n\
        RewriteEngine On\n\
        RewriteCond %{REQUEST_FILENAME} !-f\n\
        RewriteCond %{REQUEST_FILENAME} !-d\n\
        RewriteRule ^(.*)$ index.php [QSA,L]\n\
    </Directory>\n\
    Header always set Access-Control-Allow-Origin "*"\n\
    Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"\n\
    Header always set Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With"\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Setup initialization script
RUN echo '#!/bin/bash\n\
set -e\n\
echo "🚀 Starting Laravel Headless CMS..."\n\
if [ ! -f .env ]; then\n\
    echo "📋 Creating .env file..."\n\
    cp .env.example .env\n\
fi\n\
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then\n\
    echo "🔑 Generating application key..."\n\
    php artisan key:generate --force\n\
fi\n\
echo "🗄️ Running database migrations..."\n\
php artisan migrate --force\n\
echo "🔗 Creating storage symlink..."\n\
php artisan storage:link || true\n\
if [ "$APP_ENV" = "production" ]; then\n\
    echo "⚡ Optimizing for production..."\n\
    php artisan config:cache\n\
    php artisan route:cache\n\
    php artisan view:cache\n\
fi\n\
echo "🔍 Indexing content for search..."\n\
php artisan scout:import "App\\Models\\Post" || true\n\
php artisan scout:import "App\\Models\\Page" || true\n\
php artisan scout:import "App\\Models\\Category" || true\n\
echo "✅ Laravel Headless CMS is ready!"\n\
echo "🌐 API available at: http://localhost/api"\n\
exec "$@"' > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 80
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/api/posts || exit 1

# Set entrypoint and command
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
