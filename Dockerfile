# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# Backend Laravel 12 (PHP 8.4 + php artisan serve) — pronto para PaaS (Railway)
#
# O Apache gerava "More than one MPM loaded" (AH00534) independentemente da
# limpeza dos módulos, então o backend passou a rodar com o servidor embutido
# do PHP, sem Apache — solução determinística e suficiente para a carga deste app.
#
# O app escuta na porta fixa 8080; na Railway, mantenha o Target Port 8080.
#
# Build:
#   docker build -t fsppp-backend ./fsppp-backend
#
# Run:
#   docker run --rm -p 8080:8080 \
#     -e APP_KEY=base64:... \
#     -e DB_CONNECTION=pgsql -e DB_HOST=... -e DB_PORT=5432 \
#     -e DB_DATABASE=fsppp -e DB_USERNAME=... -e DB_PASSWORD=... \
#     fsppp-backend
# ---------------------------------------------------------------------------

FROM php:8.4-cli-bookworm

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
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Defaults de produção (o painel do PaaS sobrescreve com as variáveis dele)
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    LOG_LEVEL=info \
    PHP_CLI_SERVER_WORKERS=4

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

EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -fsS "http://127.0.0.1:8080/up" || exit 1

ENTRYPOINT ["entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
