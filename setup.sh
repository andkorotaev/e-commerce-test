#!/usr/bin/env bash
set -e

cd "$(dirname "$0")"

echo "==> Installing PHP dependencies..."
composer install

if [ ! -f .env ]; then
    echo "==> Creating .env from .env.example..."
    cp .env.example .env
fi

echo "==> Starting Docker containers (Sail)..."
./vendor/bin/sail up -d

if ! grep -q '^APP_KEY=base64:' .env; then
    echo "==> Generating application key..."
    ./vendor/bin/sail artisan key:generate --ansi
fi

echo "==> Installing and building frontend assets..."
./vendor/bin/sail npm install
./vendor/bin/sail npm run build

echo "==> Running database migrations..."
./vendor/bin/sail artisan migrate

echo "==> Done. App is running at ${APP_URL:-http://localhost:8000}"
