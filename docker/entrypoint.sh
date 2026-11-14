#!/bin/sh
set -e

PORT="${PORT:-8080}"

# Aponta o Apache para a porta fornecida pelo PaaS
sed -ri "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Cache de configuração/rotas/views com as envs do ambiente atual
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migrações sob demanda (ex.: defina RUN_MIGRATIONS=true no primeiro deploy)
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

exec "$@"
