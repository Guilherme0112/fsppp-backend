#!/bin/sh
set -e

# Porta fixa 8080 (a Railway roteia o domínio para o Target Port configurado —
# mantenha o Target Port como 8080 no painel).
sed -ri "s/^Listen .*/Listen 8080/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:8080>/" /etc/apache2/sites-available/000-default.conf

# Cache de configuração/rotas/views com as envs do ambiente atual
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migrações sob demanda (ex.: defina RUN_MIGRATIONS=true no primeiro deploy)
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

exec "$@"
