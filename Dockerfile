# Stage 1: Install Composer dependencies
FROM composer:2.7 as composer

WORKDIR /app
COPY database/ database/
COPY composer.* ./
# Install dependencies without running scripts, ideal for production
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist


# Stage 2: Build frontend assets
FROM node:20-alpine as node

WORKDIR /app

# Copy composer dependencies from the 'composer' stage
COPY --from=composer /app/vendor/ ./vendor/

# Copy only necessary files for npm install
COPY package*.json ./
COPY vite.config.js ./
# COPY tailwind.config.js ./
COPY postcss.config.js ./
# Copy asset source files
COPY resources/ resources/
# Install dependencies and build assets for production
RUN npm install && npm run build


# Stage 3: Final application image
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# Install system dependencies
# - Nginx as the web server
# - Supervisor to manage processes (nginx, php-fpm)
# - Dependencies for common PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    libzip-dev \
    libpng-dev \
    jpeg-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    oniguruma-dev \
    libxml2-dev \
    curl \
    unzip \
    git \
    gosu

# Install PHP extensions required by Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    bcmath \
    exif \
    pcntl \
    gd \
    zip \
    mbstring \
    xml

# Set working directory
WORKDIR /var/www/html

# Copy application code and build artifacts
COPY . .
COPY --from=composer /app/vendor/ ./vendor/
COPY --from=node /app/public/build/ ./public/build/

# Copy config files
COPY docker/nginx/default.conf /etc/nginx/nginx.conf
COPY docker/supervisord/default.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Make entrypoint executable
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose web port
EXPOSE 80

# Entrypoint and command
ENTRYPOINT ["entrypoint.sh"]

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]