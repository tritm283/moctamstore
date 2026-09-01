#!/usr/bin/env sh
set -eu

cd /var/www/html

mkdir -p \
  bootstrap/cache \
  storage/app \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs

chmod -R ug+rwX bootstrap/cache storage 2>/dev/null || true

if [ "${APP_INIT:-1}" = "1" ]; then
  echo "[docker] Waiting for PostgreSQL at ${DB_HOST:-postgres}:${DB_PORT:-5432}..."
  ATTEMPT=0
  until php -r '
    $host=getenv("DB_HOST") ?: "postgres";
    $port=getenv("DB_PORT") ?: "5432";
    $db=getenv("DB_DATABASE") ?: "ecommerce_cms";
    $user=getenv("DB_USERNAME") ?: "postgres";
    $pass=getenv("DB_PASSWORD") ?: "postgres";
    try { new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass); exit(0); }
    catch (Throwable $e) { exit(1); }
  '; do
    ATTEMPT=$((ATTEMPT + 1))
    if [ "$ATTEMPT" -ge 60 ]; then
      echo "[docker] PostgreSQL is still unavailable after 60 attempts." >&2
      exit 1
    fi
    sleep 1
  done

  if [ ! -f vendor/autoload.php ]; then
    if [ -f composer.lock ]; then
      echo "[docker] Installing Composer dependencies from composer.lock..."
      composer install --no-interaction --prefer-dist
    else
      echo "[docker] composer.lock not found; resolving dependencies and creating lock file..."
      composer update --no-interaction --prefer-dist
    fi
  fi

  # Clear file-based Laravel caches without touching the database cache table
  # before the first migration has created it.
  php artisan config:clear
  php artisan route:clear
  php artisan view:clear

  if [ "${AUTO_MIGRATE:-true}" = "true" ]; then
    echo "[docker] Running migrations..."
    php artisan migrate --force
  fi

  if [ "${AUTO_SEED:-true}" = "true" ]; then
    echo "[docker] Seeding development data..."
    php artisan db:seed --force
  fi
fi

exec "$@"
