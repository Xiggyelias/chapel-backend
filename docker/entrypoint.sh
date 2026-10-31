#!/bin/sh
set -e

echo "=========================================================="
echo "  Africa University - Kwang-Lim Chapel Ministry Portal   "
echo "  Production Container Bootstrapping on Dokploy          "
echo "=========================================================="

export ENABLE_QUEUE_WORKER=${ENABLE_QUEUE_WORKER:-false}

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

# 2. SQLite Database initialization (fallback mode)
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

# 6. Database Connectivity Check & Auto-Migration
echo "Step 5: Verifying database connectivity..."
if [ "${DB_CONNECTION:-sqlite}" != "sqlite" ]; then
    MAX_RETRIES=20
    COUNT=0
    echo "Connecting to ${DB_CONNECTION} database at ${DB_HOST:-127.0.0.1}:${DB_PORT:-5432}..."
    until php -r "try { new PDO('${DB_CONNECTION}:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}', [PDO::ATTR_TIMEOUT => 2, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); exit(0); } catch (Exception \$e) { exit(1); }" 2>/dev/null || [ $COUNT -eq $MAX_RETRIES ]; do
        COUNT=$((COUNT+1))
        echo "Waiting for database to be ready ($COUNT/$MAX_RETRIES)..."
        sleep 2
    done

    if [ $COUNT -eq $MAX_RETRIES ]; then
        echo "WARNING: Could not connect to database after $MAX_RETRIES attempts. Check DB_HOST, DB_PORT, and credentials."
    else
        echo "Database connection verified successfully!"
    fi
fi

echo "Step 6: Running database migrations..."
php /var/www/html/artisan migrate --force || echo "Warning: Migration command encountered an issue, proceeding..."

# Move any uploads still on the public disk into private storage (idempotent)
php /var/www/html/artisan chapel:secure-uploads || echo "Warning: Could not secure legacy uploads, proceeding..."

# 7. Automated seed if database is empty or SEED_ON_DEPLOY=true
USER_COUNT=$(php /var/www/html/artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null || echo "0")
if [ "${SEED_ON_DEPLOY}" = "true" ] || [ "$USER_COUNT" = "0" ]; then
    echo "Step 7: Seeding initial Africa University chapel data and hymns (current users: $USER_COUNT)..."
    php /var/www/html/artisan db:seed --force || echo "Warning: Database seeder encountered an issue, proceeding..."
fi

# 8. Production Caching Optimizations
if [ "${APP_ENV:-production}" = "production" ] && [ "${APP_DEBUG:-false}" != "true" ]; then
    echo "Step 8: Caching configuration, routes, and views for optimal production performance..."
    php /var/www/html/artisan config:cache || true
    php /var/www/html/artisan route:cache || true
    php /var/www/html/artisan view:cache || true
fi

echo "=========================================================="
echo "  Kwang-Lim Chapel Portal Ready - Starting Supervisor   "
echo "=========================================================="

exec "$@"
