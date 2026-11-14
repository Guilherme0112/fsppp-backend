# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# Backend Laravel 12 (PHP 8.4 + Apache) — pronto para PaaS (Render/Railway/Fly)
#
# Build:
#   docker build -t fsppp-backend ./fsppp-backend
#
# Run (o PaaS injeta PORT; localmente cai para 8080):
#   docker run --rm -p 8080:8080 \
#     -e APP_KEY=base64:... \
#     -e DB_CONNECTION=pgsql -e DB_HOST=... -e DB_PORT=5432 \
#     -e DB_DATABASE=fsppp -e DB_USERNAME=... -e DB_PASSWORD=... \
#     fsppp-backend
#
# Defina RUN_MIGRATIONS=true para rodar `migrate --force` na subida.
# ---------------------------------------------------------------------------

FROM php:8.4-apache-bookworm

# Dependências de sistema e extensões PHP usadas pelo Laravel + PostgreSQL
RUN apt-get update && apt-get install -y --no-install-recommends \
        curl \
        git \
        unzip \
        libpq-dev \
        libzip-dev \
        libonig-dev \
        libxml2-dev \
        libicu-dev \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_pgsql \
        pgsql \
        mbstring \
        bcmath \
        intl \
        zip \
        opcache \
    && a2enmod rewrite \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Apache: servir o Laravel a partir de /public com .htaccess habilitado
RUN sed -ri "s!/var/www/html!/var/www/html/public!g" /etc/apache2/sites-available/000-default.conf
COPY docker/apache-public.conf /etc/apache2/conf-available/apache-public.conf
RUN a2enconf apache-public

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Defaults de produção (o PaaS sobrescreve via variáveis de ambiente)
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    LOG_LEVEL=info \
    PORT=8080

WORKDIR /var/www/html

# Dependências primeiro para aproveitar cache de camadas
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts

COPY . .

RUN composer dump-autoload --optimize --no-dev \
    && php artisan package:discover --ansi \
    && mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache

COPY --chmod=755 docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Render/Railway injetam PORT; Apache é reconfigurado no entrypoint.
# Fly.io usa 8080 por padrão (coincide com o default).
EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -fsS "http://127.0.0.1:${PORT}/up" || exit 1

ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
