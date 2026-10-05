# Laravel 12 + PHP 8.2 — Render-ready (Docker runtime)
FROM php:8.2-apache

# Render injects $PORT at runtime (defaults to 10000). We bind Apache to it on boot.
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/tmp/composer \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    LOG_LEVEL=info \
    SESSION_DRIVER=file \
    CACHE_STORE=file \
    QUEUE_CONNECTION=sync

WORKDIR /var/www/html

# 1) System deps + PHP extensions Laravel needs (MySQL + PgSQL + SQLite covered)
RUN apt-get update && apt-get install -y --no-install-recommends \
        git curl zip unzip \
        libpng-dev libonig-dev libxml2-dev libzip-dev \
        sqlite3 libsqlite3-dev \
    && docker-php-ext-install -j$(nproc) \
        pdo pdo_mysql pdo_pgsql pdo_sqlite \
        mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2) Apache: rewrite + point DocumentRoot to Laravel's public dir
RUN a2enmod rewrite \
    && sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf \
    && printf '<Directory /var/www/html/public>\n\tAllowOverride All\n\tRequire all granted\n</Directory>\n' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# 3) Node 20 (for `npm run build`) + Composer
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && node -v && npm -v
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4) App code + PHP deps + frontend build
COPY . /var/www/html
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && (npm ci || npm install) \
    && (npm run build || echo "Frontend build skipped") \
    && rm -rf node_modules /tmp/composer \
    && mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache database \
    && ([ -f database/database.sqlite ] || touch database/database.sqlite) \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 10000

# 5) Runtime boot: bind Apache to $PORT, fix perms, link storage, migrate, cache, serve
CMD ["bash", "-c", "set -x; PORT=${PORT:-10000}; sed -i \"s/Listen 80/Listen $PORT/\" /etc/apache2/ports.conf; sed -i \"s/:80/:$PORT/g\" /etc/apache2/sites-enabled/000-default.conf; mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache database; [ -f .env ] || cp .env.example .env; [ -f database/database.sqlite ] || touch database/database.sqlite; chown -R www-data:www-data storage bootstrap/cache database; chmod -R 775 storage bootstrap/cache; php artisan storage:link || true; php artisan migrate --force || echo \"MIGRATION FAILED - check DB_* env vars on Render\"; php artisan optimize || true; apache2-foreground"]
