# ============================================================
# 🟦 STAGE 1 — FRONTEND BUILD (Vite / Node)
# ============================================================
FROM node:20-alpine AS node_builder

WORKDIR /app

# 1. Copia apenas arquivos de dependência primeiro (Cache Layer)
COPY package*.json ./
RUN npm ci --no-audit --no-fund

# 2. Copia o resto e builda
COPY . .
RUN npm run build


# ============================================================
# 🟩 STAGE 2 — BACKEND BUILD (Composer)
# ============================================================
FROM php:8.2-cli-alpine AS composer_builder

WORKDIR /app

# 1. Instala dependências do sistema (CORRIGIDO: oniguruma-dev)
RUN apk add --no-cache \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    oniguruma-dev \
    icu-dev

# 2. Instala extensões necessárias para o build
RUN docker-php-ext-install \
    pdo_pgsql \
    bcmath \
    gd \
    intl \
    zip \
    mbstring \
    pcntl

# 3. Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. Copia APENAS composer.json/lock
COPY composer.json composer.lock ./

# 5. Instala dependências
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction

# 6. Copia o código fonte
COPY . .

# 7. Gera o autoloader final otimizado
RUN composer dump-autoload --optimize


# ============================================================
# 🟨 STAGE 3 — RUNTIME (Imagem Final Leve)
# ============================================================
FROM php:8.2-cli-alpine

WORKDIR /var/www/html

# 1. Instala libs de runtime (CORRIGIDO: oniguruma)
RUN apk add --no-cache \
    bash \
    libpq \
    libzip \
    libpng \
    oniguruma \
    icu-libs

# 2. Instala as extensões PHP na imagem final
# (Instala deps de build temporariamente e remove depois para economizar espaço)
RUN apk add --no-cache --virtual .build-deps \
    libpq-dev libzip-dev libpng-dev oniguruma-dev icu-dev \
    && docker-php-ext-install pdo_pgsql bcmath gd intl zip mbstring opcache \
    && apk del .build-deps

# 3. Copia o código pronto dos estágios anteriores
COPY --from=composer_builder /app /var/www/html
COPY --from=node_builder /app/public/build ./public/build

# 4. Ajusta permissões e cria pastas de cache
RUN mkdir -p storage/framework/{sessions,views,cache} bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# 5. Configuração do PHP (Opcional)
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini
RUN echo "upload_max_filesize=50M" > /usr/local/etc/php/conf.d/uploads.ini

# ============================================================
# 🚀 STARTUP SCRIPT (NEON DB LOGIC)
# ============================================================
CMD sh -c '\
    set -e; \
    echo "🚀 Inicializando container..."; \
    \
    if [ -n "$DATABASE_URL_DIRECT" ]; then \
        echo "🔄 Trocando para conexão DIRECT para rodar Migrations..."; \
        export DATABASE_URL="$DATABASE_URL_DIRECT"; \
    fi; \
    \
    echo "📂 Executando Migrations..."; \
    php artisan migrate --force --no-interaction; \
    php artisan seo:generate-sitemap; \
    \
    echo "⚡ Otimizando caches..."; \
    php artisan optimize; \
    php artisan config:cache; \
    php artisan route:cache; \
    php artisan view:cache; \
    \
    if [ -n "$DATABASE_URL_POOLER" ]; then \
        echo "✅ Voltando para conexão POOLER para o Servidor..."; \
        export DATABASE_URL="$DATABASE_URL_POOLER"; \
    fi; \
    \
    echo "🌍 Servidor iniciando na porta ${PORT:-10000}"; \
    php -S 0.0.0.0:${PORT:-10000} -t public \
'