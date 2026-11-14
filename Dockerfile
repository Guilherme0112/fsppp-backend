# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# Backend Laravel 12 (PHP 8.4 + Apache) — pronto para PaaS (Render/Railway/Fly)
#
# Build:
#   docker build -t fsppp-backend ./fsppp-backend
#
# O Apache escuta na porta fixa 8080; na Railway, aponte o Target Port do
# domínio para 8080. Rode localmente:
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
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Garante um único MPM: dependendo da versão da imagem, mpm_event e
# mpm_prefork ficam ambos habilitados e o Apache não sobe (AH00534: More
# than one MPM loaded). Remove os symlinks dos MPMs indesejados diretamente.
RUN rm -f /etc/apache2/mods-enabled/mpm_event.* \
           /etc/apache2/mods-enabled/mpm_worker.* \
    && a2enmod mpm_prefork rewrite \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && apache2ctl -M 2>&1 | grep -i mpm || true

# Apache: servir o Laravel a partir de /public com .htaccess habilitado
RUN sed -ri "s!/var/www/html!/var/www/html/public!g" /etc/apache2/sites-available/000-default.conf
COPY docker/apache-public.conf /etc/apache2/conf-available/apache-public.conf
RUN a2enconf apache-public

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Defaults de produção (o PaaS sobrescreve via variáveis de ambiente)
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    LOG_LEVEL=info

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

# A Railway roteia o domínio para o Target Port configurado (mantenha 8080).
EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -fsS "http://127.0.0.1:8080/up" || exit 1

ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
