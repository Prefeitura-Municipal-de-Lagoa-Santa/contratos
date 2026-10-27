#!/bin/bash
set -e

# Railpack caches config at build time; clear it so migrations use runtime env vars.
php artisan optimize:clear

if [ "$RAILPACK_SKIP_MIGRATIONS" != "true" ]; then
  echo "Running migrations ..."
  php artisan migrate --force
fi

php artisan storage:link || true
php artisan optimize

echo "Starting Laravel server ..."
docker-php-entrypoint --config /Caddyfile --adapter caddyfile 2>&1
