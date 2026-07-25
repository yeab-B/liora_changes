#!/usr/bin/env bash
set -euo pipefail

# Skipped during the Docker build (composer install/dump-autoload run with
# --no-scripts there) since env vars aren't available yet at build time.
php artisan package:discover --ansi

# Config/route caching must happen here (container start), NOT during the
# Docker build: env vars (DB_URL, APP_KEY, API keys) only exist at runtime on
# Render, so caching them at build time would bake in empty/wrong values.
php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan migrate --force

php artisan db:seed --class=Database\\Seeders\\DemoSeeder --force || true

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
