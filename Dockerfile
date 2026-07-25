# syntax=docker/dockerfile:1
#
# No Node/Vite build stage: this app has no product UI of its own (mobile
# talks to /api/v1/* directly, the admin panel is Filament and ships its own
# bundled assets — see routes/web.php).

FROM php:8.3-cli AS app
WORKDIR /var/www/html

# System deps + PHP extensions required by Laravel 13 / Filament 3 /
# spatie/laravel-permission / Postgres (Render has no managed MySQL, see
# render.yaml — the app already degrades gracefully on non-MySQL, see
# App\Services\Ai\SimpleRagRetriever).
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libpq-dev libzip-dev libpng-dev libjpeg-dev libonig-dev \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql bcmath gd zip mbstring \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --optimize-autoloader --no-interaction

COPY . .

# --no-scripts: composer.json's post-autoload-dump runs `artisan
# package:discover`/`filament:upgrade`, which would execute during the
# build, before any runtime env vars (DB_URL, APP_KEY, ...) exist. Those run
# instead from docker/entrypoint.sh, once Render has injected real env vars.
RUN composer dump-autoload --optimize --no-dev --no-scripts \
    && mkdir -p storage/framework/{cache,sessions,testing,views} storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 10000
ENTRYPOINT ["entrypoint.sh"]
