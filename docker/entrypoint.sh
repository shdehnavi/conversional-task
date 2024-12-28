#!/bin/bash

# Wait for PostgreSQL to be ready
echo "Checking PostgreSQL readiness..."
until pg_isready -h postgres -p 5432 -U root; do
  echo "Waiting for PostgreSQL to be ready..."
  sleep 2
done

echo "PostgreSQL is ready. Proceeding with application setup..."

# Migrate and seed database
php "/var/www/html/artisan" migrate --seed --force

# Refresh caches
php "/var/www/html/artisan" optimize:clear
php "/var/www/html/artisan" optimize

# Create storage symlinks
php "/var/www/html/artisan" storage:link

# Start Supervisor
exec supervisord -c /etc/supervisor/supervisord.conf
