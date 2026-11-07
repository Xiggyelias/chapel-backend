#!/bin/sh
set -e

echo "=========================================================="
echo "  Africa University - Kwang-Lim Chapel Ministry Portal   "
echo "  Production Container Bootstrapping on Dokploy          "
echo "=========================================================="

export ENABLE_QUEUE_WORKER=${ENABLE_QUEUE_WORKER:-true}

# 1. Ensure required storage and log directories exist
echo "Step 1: Preparing directories..."
mkdir -p /var/log/supervisor \
         /var/log/nginx \
         /var/www/html/storage/app/public \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache \
         /var/www/html/database

# 1b. Refresh migrations and seeders from the image. A volume mounted over
#     /var/www/html/database would otherwise keep serving an old copy and new
#     migrations would never run. Data files (e.g. database.sqlite) are untouched.
if [ -d /opt/chapel-database-dist ]; then
    for dir in migrations seeders factories; do
        if [ -d "/opt/chapel-database-dist/$dir" ]; then
            mkdir -p "/var/www/html/database/$dir"
            cp -a "/opt/chapel-database-dist/$dir/." "/var/www/html/database/$dir/"
        fi
    done
fi

# 2. SQLite Database initialization (fallback mode)
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    # Keep the SQLite file on a persistent volume (e.g. storage/database), never over the code's database/ folder
    DB_PATH="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    DB_DIR="$(dirname "$DB_PATH")"
    mkdir -p "$DB_DIR"
    if [ ! -f "$DB_PATH" ]; then
        echo "Creating initial SQLite database file at $DB_PATH..."
        touch "$DB_PATH"
    fi
    chown -R www-data:www-data "$DB_DIR"
    chmod 775 "$DB_DIR"
    chmod 664 "$DB_PATH"
fi

# 3. Fix storage and bootstrap cache permissions
echo "Step 2: Fixing file permissions for www-data..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 4. Create public storage symlink
echo "Step 3: Linking public storage..."
php /var/www/html/artisan storage:link --force || true

# 5. Clear stale caches before running migrations
echo "Step 4: Clearing any stale runtime caches..."
php /var/www/html/artisan config:clear || true
php /var/www/html/artisan route:clear || true
php /var/www/html/artisan view:clear || true

# 6. Production caching (no database needed)
if [ "${APP_ENV:-production}" = "production" ] && [ "${APP_DEBUG:-false}" != "true" ]; then
    echo "Step 5: Caching configuration, routes, and views..."
    php /var/www/html/artisan config:cache || true
    php /var/www/html/artisan route:cache || true
    php /var/www/html/artisan view:cache || true
fi

# Anything the root-run artisan calls above wrote must stay writable by PHP-FPM
chown -R www-data:www-data /var/www/html/storage/logs /var/www/html/storage/framework /var/www/html/bootstrap/cache

# Migrations and seeding run under supervisor (docker/bootstrap.sh) once the web server is up
echo "Step 6: Database migrations and seeding will run in the background (see [bootstrap] log lines)."

echo "=========================================================="
echo "  Kwang-Lim Chapel Portal Ready - Starting Supervisor   "
echo "=========================================================="

exec "$@"
