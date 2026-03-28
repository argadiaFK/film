#!/bin/bash
set -e

# Clear Laravel caches
php artisan optimize:clear

# Create storage link if not exists
php artisan storage:link --force 2>/dev/null || true

# Create necessary storage directories
mkdir -p /app/storage/app/public/films/posters
mkdir -p /app/storage/app/public/films/backdrops
mkdir -p /app/storage/app/public/series/posters
mkdir -p /app/storage/app/public/series/backdrops
mkdir -p /app/storage/app/public/episodes/thumbnails
chown -R www-data:www-data /app/storage/app/public

# Run FrankenPHP
exec frankenphp run --config /etc/caddy/Caddyfile
