#!/bin/sh
set -eu

cd /app

if [ ! -f package.json ]; then
  echo "[storefront] package.json not found in /app"
  exit 1
fi

attempt=1
max_attempts=5

while true; do
  echo "[storefront] Installing npm dependencies (attempt ${attempt}/${max_attempts})..."
  if npm install --no-audit --no-fund --legacy-peer-deps; then
    break
  fi

  if [ "$attempt" -ge "$max_attempts" ]; then
    echo "[storefront] npm install failed after ${max_attempts} attempts."
    exit 1
  fi

  attempt=$((attempt + 1))
  echo "[storefront] npm install failed; retrying in 5 seconds..."
  sleep 5
done

exec "$@"
