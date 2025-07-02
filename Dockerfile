# Gunakan image PHP 8.3 dengan Apache sebagai dasar
FROM php:8.3-apache

# Set working directory
WORKDIR /var/www/html

# --- Konfigurasi Apache & PHP ---
# Salin file konfigurasi VirtualHost kustom (lebih bersih daripada 'sed' atau 'echo')
COPY docker/apache/laravel.conf /etc/apache2/sites-available/000-default.conf

# Aktifkan modul Apache yang diperlukan
RUN a2enmod rewrite headers

# --- Instalasi Dependensi Sistem ---
# Instal dependensi yang dibutuhkan oleh sistem dan ekstensi PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libsqlite3-dev \
    # Hapus cache apt untuk memperkecil ukuran image
    && rm -rf /var/lib/apt/lists/*

# --- Instalasi Ekstensi PHP ---
# Instal ekstensi PHP yang umum untuk Laravel
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd

# --- Instalasi Composer ---
# Gunakan multi-stage build untuk mendapatkan Composer tanpa meninggalkan jejak di image akhir
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# --- Instalasi Dependensi Composer (Optimalisasi Cache) ---
# Salin hanya file dependensi, lalu instal.
# Ini akan membuat layer cache untuk 'vendor' yang hanya akan di-rebuild jika composer.json/lock berubah.
COPY database/ database/
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-scripts

# --- Instalasi Dependensi Node.js & Build Aset (Optimalisasi Cache) ---
# Lakukan hal yang sama untuk dependensi NPM
COPY package.json package-lock.json* vite.config.js* ./
# Cek apakah file package.json ada sebelum menjalankan npm
RUN if [ -f package.json ]; then \
    apt-get update && apt-get install -y nodejs npm && rm -rf /var/lib/apt/lists/* && \
    npm install && npm run build; \
    fi

# --- Salin Kode Aplikasi & Atur Izin ---
# Salin sisa kode aplikasi setelah dependensi diinstal
COPY . .

# Atur kepemilikan agar Apache dapat mengakses file
# Ini dilakukan setelah semua file disalin dan dibuat
RUN chown -R www-data:www-data /var/www/html

# --- Konfigurasi Entrypoint ---
# Salin skrip entrypoint eksternal dan buat agar bisa dieksekusi
COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 80
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/api/health || exit 1 # Ganti dengan endpoint health check Anda

# Tetapkan entrypoint dan command default
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]