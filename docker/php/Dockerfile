# Production image: Vite build + composer deps baked in, nginx + php-fpm
# run together via supervisord. Built and pushed to GHCR by
# .github/workflows/ci-cd.yml, pulled as-is on the server by docker-compose.prod.yml.

FROM php:8.5-fpm-alpine AS php-base

RUN apk add --no-cache nginx supervisor icu-libs libzip libpng oniguruma \
    && apk add --no-cache --virtual .build-deps icu-dev libzip-dev libpng-dev oniguruma-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Vendor deps in their own stage, split from the full app copy below, so
# `composer install` only reruns when composer.json/lock actually change —
# not on every code change.
FROM php-base AS vendor
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-scripts --no-autoloader

# Tailwind's @source in resources/css/app.css scans
# vendor/laravel/framework/.../Pagination/resources/views/*.blade.php for the
# classes Laravel's pagination links use — vendor/ has to exist here or those
# utility classes never make it into the compiled CSS (pagination renders
# unstyled in prod even though it looks fine locally, since local Sail always
# has vendor/ on disk already).
FROM node:22-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY resources resources
COPY vite.config.js ./
COPY --from=vendor /var/www/html/vendor vendor
RUN npm run build

FROM php-base
WORKDIR /var/www/html
COPY . .
COPY --from=vendor /var/www/html/vendor vendor
COPY --from=assets /app/public/build public/build

RUN composer dump-autoload --no-dev --optimize \
    && php artisan package:discover --ansi \
    && php artisan storage:link \
    && chown -R www-data:www-data storage bootstrap/cache public/storage

COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

EXPOSE 80

CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
