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

run_composer_with_retry() {
  attempt=1
  max_attempts=5

  while [ "$attempt" -le "$max_attempts" ]; do
    echo "[docker] Composer attempt ${attempt}/${max_attempts}..."

    if [ -f composer.lock ]; then
      if composer install --no-interaction --prefer-dist --no-progress; then
        return 0
      fi
    else
      if composer update --no-interaction --prefer-dist --no-progress; then
        return 0
      fi
    fi

    if [ "$attempt" -eq "$max_attempts" ]; then
      echo "[docker] Composer failed after ${max_attempts} attempts." >&2
      return 1
    fi

    attempt=$((attempt + 1))
    sleep 5
  done
}

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

  run_composer_with_retry

  # These commands use file cache and are safe before framework DB tables exist.
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
