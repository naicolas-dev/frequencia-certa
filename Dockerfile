# ============================================================
# 🟦 STAGE 1 — FRONTEND BUILD (Vite / Node)
# ============================================================
FROM node:20-alpine AS node_builder

WORKDIR /app

# 📦 Instala dependências JS
COPY package*.json ./
RUN echo "📦 Instalando dependências frontend..." \
 && npm ci

# 🏗️ Build dos assets
COPY . .
RUN echo "🏗️ Buildando assets frontend..." \
 && npm run build


# ============================================================
# 🟩 STAGE 2 — PHP DEPENDENCIES (Composer)
# ============================================================
FROM php:8.2-cli-alpine AS composer_builder

WORKDIR /app

# 🔧 Dependências do sistema + extensões PHP exigidas
RUN echo "🔧 Instalando dependências PHP e extensões..." \
 && apk add --no-cache \
    git unzip curl \
    libzip-dev icu-dev oniguruma-dev postgresql-dev \
 && docker-php-ext-install \
    intl zip pdo_pgsql

# 🎼 Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# 📦 Instala dependências PHP
COPY composer.json composer.lock ./
RUN echo "📦 Instalando dependências PHP (composer)..." \
 && composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist \
    --no-progress

# 📁 Copia código para autoload e discovery
COPY . .
RUN echo "🔍 Descobrindo packages Laravel..." \
 && php artisan package:discover --ansi \
 && composer dump-autoload --optimize


# ============================================================
# 🟨 STAGE 3 — RUNTIME (PHP 8.2)
# ============================================================
FROM php:8.2-cli-alpine

WORKDIR /var/www/html

# 🔧 Extensões PHP em runtime
RUN echo "🔧 Instalando extensões PHP em runtime..." \
 && apk add --no-cache \
    bash unzip \
    libzip-dev icu-dev oniguruma-dev postgresql-dev \
 && docker-php-ext-install \
    intl zip pdo_pgsql

# 📁 Código da aplicação
COPY . .

# 📦 Dependências PHP + assets compilados
COPY --from=composer_builder /app/vendor ./vendor
COPY --from=node_builder /app/public/build ./public/build

# 🔐 Permissões
RUN echo "🔐 Ajustando permissões..." \
 && chmod -R 775 storage bootstrap/cache || true


# ============================================================
# 🚀 STARTUP — MIGRATIONS + OPTIMIZE + SERVER
# ============================================================
CMD sh -c '\
  set -e; \
  echo "🚀 Inicializando aplicação Laravel"; \
  echo "➡️ Usando Neon DIRECT para migrations"; \
  export DATABASE_URL="${DATABASE_URL_DIRECT:-$DATABASE_URL}"; \
  php artisan migrate --force --no-interaction; \
  echo "⚡ Otimizando Laravel"; \
  php artisan optimize; \
  echo "➡️ Subindo aplicação com Neon POOLER"; \
  export DATABASE_URL="${DATABASE_URL_POOLER:-$DATABASE_URL}"; \
  echo "🌍 Servidor disponível na porta ${PORT:-10000}"; \
  php -S 0.0.0.0:${PORT:-10000} -t public \
'
