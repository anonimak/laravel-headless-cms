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

# Set working directory
WORKDIR /var/www/html

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
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# --- Instalasi Ekstensi PHP ---
# Instal ekstensi PHP yang umum untuk Laravel
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd

# --- Instalasi Composer ---
# Gunakan multi-stage build untuk mendapatkan Composer tanpa meninggalkan jejak di image akhir
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy only dependency files first
COPY composer.json composer.lock ./

# Install Composer dependencies
RUN if [ "$APP_ENV" = "production" ] ; then \
    composer install --optimize-autoloader --no-dev ; \
    else \
    composer install ; \
    fi

# Copy Node.js dependency files
COPY package.json package-lock.json ./

# Install Node.js dependencies and build assets
RUN npm install && npm run build


# Copy application files
COPY . .

# Set ownership
RUN chown -R www-data:www-data /var/www/html


# Create necessary directories and set permissions
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views storage/app/public/media bootstrap/cache database \
    && touch database/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache \
    && chmod 664 database/database.sqlite


# --- Konfigurasi Entrypoint ---
# Salin skrip entrypoint eksternal dan buat agar bisa dieksekusi
COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 80
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/api/posts || exit 1

# Tetapkan entrypoint dan command default
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]