#!/bin/bash
# Script sederhana untuk Optimasi Laravel di Production setelah Pull kode baru atau awal Setup

echo "🚀 Starting production deployment process..."

# Jalankan command optimize untuk kecepatan maksimal Laravel
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan event:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache

# Filament specific optimizations
docker-compose -f docker-compose.prod.yml exec app php artisan filament:cache-components
docker-compose -f docker-compose.prod.yml exec app php artisan filament:optimize

# Force run migrations
echo "📦 Running Database migrations..."
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

echo "✅ Deployment optimizations finished successfully!"
