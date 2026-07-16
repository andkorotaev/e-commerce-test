# Production image: Vite build + composer deps baked in, nginx + php-fpm
# run together via supervisord. Built and pushed to GHCR by
# .github/workflows/ci-cd.yml, pulled as-is on the server by docker-compose.prod.yml.

FROM node:22-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY resources resources
COPY vite.config.js ./
RUN npm run build

FROM php:8.5-fpm-alpine

RUN apk add --no-cache nginx supervisor icu-libs libzip libpng oniguruma \
    && apk add --no-cache --virtual .build-deps icu-dev libzip-dev libpng-dev oniguruma-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .
COPY --from=assets /app/public/build public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-scripts \
    && php artisan package:discover --ansi \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

EXPOSE 80

CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
