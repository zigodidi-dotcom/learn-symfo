#!/bin/sh
set -e

# Start server immediately so Railway's healthcheck passes
php -S 0.0.0.0:${PORT:-8000} -t public/ &
SERVER_PID=$!

# Give the server 2 s to bind to the port before running migrations
sleep 2

echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction \
  && echo "Migrations OK" \
  || echo "Migration failed — check logs"

# Keep container alive until the server process exits
wait $SERVER_PID
