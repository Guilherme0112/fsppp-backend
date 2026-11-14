#!/bin/sh
set -e

# Cache de configuração/rotas/views com as envs do ambiente atual
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migrações sob demanda (defina RUN_MIGRATIONS=true no primeiro deploy)
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

exec "$@"
