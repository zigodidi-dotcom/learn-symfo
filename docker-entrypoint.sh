#!/bin/sh
set -e

echo "[boot] Clearing and rebuilding cache..."
php bin/console cache:clear

echo "[boot] Installing bundle assets..."
php bin/console assets:install

echo "[boot] Compiling asset map..."
php bin/console asset-map:compile

echo "[boot] Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

echo "[boot] Starting PHP server on :${PORT:-8000}"
exec php -S 0.0.0.0:${PORT:-8000} -t public/
