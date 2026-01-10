# ============================================================
# 🟦 STAGE 1 — FRONTEND BUILD (Vite / Node)
# ============================================================
FROM node:20-alpine AS node_builder

WORKDIR /app

# 📦 Instala dependências JS (cacheável)
COPY package*.json ./
RUN echo "📦 Instalando dependências frontend..." \
 && npm ci --no-audit --no-fund

# 🏗️ Copia somente o necessário pro build do Vite
COPY vite.config.* postcss.config.* tailwind.config.* ./
COPY resources ./resources
COPY public ./public

RUN echo "🏗️ Buildando assets frontend..." \
 && npm run build


# ============================================================
# 🟩 STAGE 2 — PHP DEPENDENCIES (Composer + Vendor)
# ============================================================
FROM php:8.2-cli-alpine AS composer_builder

WORKDIR /app

# 🔧 Pacotes + build deps (pra compilar extensões) + extensões PHP
RUN echo "🔧 Instalando dependências do sistema e extensões PHP (builder)..." \
 && apk add --no-cache \
      bash git unzip curl \
      icu-libs libzip postgresql-libs oniguruma \
 && apk add --no-cache --virtual .build-deps \
      $PHPIZE_DEPS icu-dev libzip-dev postgresql-dev oniguruma-dev \
 && docker-php-ext-install \
      intl zip pdo_pgsql mbstring \
 && apk del .build-deps

# 🎼 Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# ✅ IMPORTANTE:
# Copie o código (incluindo artisan) ANTES do composer install,
# senão o post-autoload-dump tenta rodar "php artisan ..." e falha.
COPY . .

# 📦 Instala dependências PHP (gera vendor/)
RUN echo "📦 Instalando dependências PHP (composer)..." \
 && composer install \
      --no-dev \
      --optimize-autoloader \
      --no-interaction \
      --prefer-dist \
      --no-progress

# (Opcional, mas ajuda em algumas imagens) garante cache dirs existirem
RUN mkdir -p storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache || true


# ============================================================
# 🟨 STAGE 3 — RUNTIME (PHP 8.2)
# ============================================================
FROM php:8.2-cli-alpine

WORKDIR /var/www/html

# 🔧 Runtime libs + build deps temporários pra compilar extensões (e remover depois)
RUN echo "🔧 Instalando extensões PHP em runtime..." \
 && apk add --no-cache \
      bash unzip \
      icu-libs libzip postgresql-libs oniguruma \
 && apk add --no-cache --virtual .build-deps \
      $PHPIZE_DEPS icu-dev libzip-dev postgresql-dev oniguruma-dev \
 && docker-php-ext-install \
      intl zip pdo_pgsql mbstring \
 && apk del .build-deps

# 📁 Copia app já com vendor pronto do builder
COPY --from=composer_builder /app /var/www/html

# ✅ Copia assets compilados do Vite
COPY --from=node_builder /app/public/build ./public/build

# 🔐 Permissões (sem quebrar build se não existir algo)
RUN echo "🔐 Ajustando permississões..." \
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
