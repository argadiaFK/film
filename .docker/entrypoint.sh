#!/bin/bash
set -e

# Clear Laravel caches
php artisan optimize:clear

# Run FrankenPHP
exec frankenphp run --config /etc/caddy/Caddyfile
