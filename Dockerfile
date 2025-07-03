# Gunakan image PHP 8.3 dengan Apache sebagai dasar
FROM php:8.3-apache

# Set environment variables
ENV APP_ENV=${APP_ENV:-production}
ENV APP_DEBUG=${APP_DEBUG:-false}
ENV APP_KEY=${APP_KEY}
ENV APP_URL=${APP_URL:-https://laravelcms.anonimak.my.id}
# Add these new environment variables for HTTPS behind proxy
ENV ASSET_URL=${APP_URL}
ENV VITE_SERVER_HTTPS=true
ENV TRUST_PROXY=true
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

# --- Konfigurasi Apache & PHP ---
# Salin file konfigurasi VirtualHost kustom (lebih bersih daripada 'sed' atau 'echo')
COPY docker/apache/laravel.conf /etc/apache2/sites-available/000-default.conf

# Aktifkan modul Apache yang diperlukan
RUN a2enmod rewrite headers proxy proxy_http ssl

# --- Instalasi Dependensi Sistem ---
# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    nodejs \
    npm \
    nano \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install \
    pdo_mysql \
    pdo_sqlite \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    ctype \
    fileinfo

# --- Instalasi Ekstensi PHP ---
# Instal ekstensi PHP yang umum untuk Laravel
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .


# Install Composer dependencies
RUN composer install --no-dev --no-interaction



# Install Node.js dependencies and build assets
RUN npm install && npm run build

# Set ownership
RUN chown -R www-data:www-data /var/www/html


# Create necessary directories and set permissions
RUN mkdir -p /var/www/html/storage/logs /var/www/html/storage/framework/cache /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views /var/www/html/storage/app/public/media /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache



# --- Konfigurasi Entrypoint ---
# Salin skrip entrypoint eksternal dan buat agar bisa dieksekusi
COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint.sh

# Make the entrypoint script executable
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 80
EXPOSE 80

# Health check
# HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    # CMD curl -f http://localhost/api/posts || exit 1

# Tetapkan entrypoint dan command default
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]