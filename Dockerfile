# syntax=docker/dockerfile:1

# ==========================================
# Stage 1: Composer Dependencies
# ==========================================
FROM composer:2 AS composer-build

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-scripts \
    --no-autoloader \
    --ignore-platform-reqs

COPY . .

RUN composer dump-autoload \
    --optimize \
    --no-dev \
    --classmap-authoritative

# ==========================================
# Stage 2: Node.js Frontend Assets (Vite)
# ==========================================
FROM node:22-alpine AS frontend-build

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --prefer-offline --no-audit

COPY resources ./resources
COPY vite.config.js ./
COPY public ./public

RUN npm run build

# ==========================================
# Stage 3: Production Runtime (PHP 8.3 + Nginx)
# ==========================================
FROM php:8.3-fpm-alpine AS runtime

LABEL maintainer="Africa University Kwang-Lim Chapel"
LABEL description="Dokploy Ready Backend Service for Kwang-Lim Chapel Portal"

# Install production system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    sqlite \
    sqlite-dev \
    libpq-dev \
    postgresql-client \
    mariadb-client \
    tzdata

# Configure & install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_sqlite \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        bcmath \
        zip \
        intl \
        opcache \
        pcntl \
        gd

# Copy PHP, Nginx and Supervisor configs
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set working directory
WORKDIR /var/www/html

# Copy application from build stages
COPY --from=composer-build /app /var/www/html
COPY --from=frontend-build /app/public/build /var/www/html/public/build

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Ensure storage and cache directories exist with correct permissions
RUN mkdir -p \
    /var/www/html/storage/app/public \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache \
    /var/www/html/database \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -f http://127.0.0.1:80/up || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
