#!/bin/sh
# Database work that needs a reachable database. Runs once under supervisor,
# after nginx/PHP-FPM are already serving, so a slow or unreachable database
# can never stop the container from passing its health check.

ARTISAN="php /var/www/html/artisan"
export HOME=/tmp

if [ "${DB_CONNECTION:-sqlite}" != "sqlite" ]; then
    MAX_RETRIES=30
    COUNT=0
    echo "[bootstrap] Waiting for ${DB_CONNECTION} at ${DB_HOST:-127.0.0.1}:${DB_PORT:-5432}..."
    until php -r "try { new PDO(getenv('DB_CONNECTION').':host='.getenv('DB_HOST').';port='.getenv('DB_PORT').';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), [PDO::ATTR_TIMEOUT => 3, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); exit(0); } catch (Exception \$e) { fwrite(STDERR, \$e->getMessage().PHP_EOL); exit(1); }"; do
        COUNT=$((COUNT+1))
        if [ $COUNT -ge $MAX_RETRIES ]; then
            echo "[bootstrap] ERROR: database unreachable after $MAX_RETRIES attempts. Check DB_HOST/DB_PORT/credentials. Skipping migrations and seeding."
            exit 0
        fi
        sleep 3
    done
    echo "[bootstrap] Database connection verified."
fi

echo "[bootstrap] Running migrations..."
timeout 300 $ARTISAN migrate --force || echo "[bootstrap] WARNING: migrations failed or timed out."

timeout 120 $ARTISAN chapel:secure-uploads || echo "[bootstrap] WARNING: could not secure legacy uploads."

USER_COUNT=$(timeout 60 $ARTISAN tinker --execute="echo \App\Models\User::count();" 2>/dev/null | tr -dc '0-9')
if [ "${SEED_ON_DEPLOY}" = "true" ] || [ "${USER_COUNT:-0}" = "0" ]; then
    echo "[bootstrap] Seeding (current users: ${USER_COUNT:-0})..."
    timeout 600 $ARTISAN db:seed --force || echo "[bootstrap] WARNING: seeding failed or timed out."
fi

echo "[bootstrap] Done."
