# ---------- Frontend build ----------
FROM node:20-alpine AS node_builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---------- Composer deps (PHP 8.2) ----------
FROM php:8.2-cli-alpine AS composer_builder
WORKDIR /app

RUN apk add --no-cache git unzip curl
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress --no-scripts

COPY . .

RUN php artisan package:discover --ansi \
  && composer dump-autoload --optimize

# ---------- Runtime (PHP 8.2) ----------
FROM php:8.2-cli-alpine
WORKDIR /var/www/html

RUN apk add --no-cache bash unzip libzip-dev icu-dev oniguruma-dev postgresql-dev \
  && docker-php-ext-install pdo_pgsql intl zip

COPY . .
COPY --from=composer_builder /app/vendor ./vendor
COPY --from=node_builder /app/public/build ./public/build

RUN chmod -R 775 storage bootstrap/cache || true

CMD sh -c "php artisan migrate:fresh --force --no-interaction \
  && php artisan optimize \
  && php -S 0.0.0.0:${PORT:-10000} -t public"
