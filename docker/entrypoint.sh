#!/bin/sh
set -e

echo "=== Kwang-Lim Chapel Portal Bootstrapping on Dokploy ==="

# Set default queue worker environment variable for supervisord
export ENABLE_QUEUE_WORKER=${ENABLE_QUEUE_WORKER:-false}

# 1. Ensure required storage and log directories exist
mkdir -p /var/log/supervisor \
         /var/log/nginx \
         /var/www/html/storage/app/public \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache \
         /var/www/html/database

# 2. SQLite Database initialization
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    DB_PATH="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    if [ ! -f "$DB_PATH" ]; then
        echo "Creating initial SQLite database file at $DB_PATH..."
        touch "$DB_PATH"
    fi
    chown -R www-data:www-data /var/www/html/database
    chmod -R 775 /var/www/html/database
    if [ -f "$DB_PATH" ]; then
        chmod 666 "$DB_PATH"
    fi
fi

# 3. Fix storage and bootstrap cache permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 4. Create public storage symlink
echo "Linking storage..."
php /var/www/html/artisan storage:link --force || true

# 5. Run Database Migrations
echo "Running database migrations..."
php /var/www/html/artisan migrate --force

# 6. Automated seed if database is empty or SEED_ON_DEPLOY=true
USER_COUNT=$(php /var/www/html/artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null || echo "0")
if [ "${SEED_ON_DEPLOY}" = "true" ] || [ "$USER_COUNT" = "0" ]; then
    echo "Seeding initial Africa University chapel data and hymns (users: $USER_COUNT)..."
    php /var/www/html/artisan db:seed --force
fi

# 7. Production Caching Optimizations
if [ "${APP_ENV:-production}" = "production" ] && [ "${APP_DEBUG:-false}" != "true" ]; then
    echo "Optimizing configuration, routes, and views for production..."
    php /var/www/html/artisan config:cache
    php /var/www/html/artisan route:cache
    php /var/www/html/artisan view:cache
    php /var/www/html/artisan event:cache
else
    echo "Clearing caches for non-production environment..."
    php /var/www/html/artisan config:clear
    php /var/www/html/artisan route:clear
    php /var/www/html/artisan view:clear
fi

echo "=== Kwang-Lim Chapel Portal Ready - Starting Web Server ==="

# Execute CMD
exec "$@"
