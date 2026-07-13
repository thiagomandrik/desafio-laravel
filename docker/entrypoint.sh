#!/bin/sh
set -e

if [ ! -d vendor ]; then
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ ! -f .env ]; then
    cp .env.example .env
fi

if ! grep -q "^APP_KEY=base64" .env 2>/dev/null; then
    php artisan key:generate --ansi
fi

php artisan migrate --force
php artisan l5-swagger:generate

exec php artisan serve --host=0.0.0.0 --port=8000
