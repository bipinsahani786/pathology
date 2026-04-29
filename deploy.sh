#!/bin/bash

# Exit immediately if a command exits with a non-zero status.
set -e

echo "======================================"
echo "Starting Production Deployment Process"
echo "======================================"

# Optional: Ensure we are on the main branch and pull latest code
# echo "Pulling latest code from repository..."
# git checkout main
# git pull origin main

echo "1. Building Docker images..."
docker compose -f docker-compose.prod.yml build

echo "2. Starting Docker containers..."
docker compose -f docker-compose.prod.yml up -d

# Wait a few seconds for the database to be ready
echo "Waiting for database to be ready..."
sleep 5

echo "3. Running database migrations..."
docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force

echo "4. Caching configuration and routes..."
docker compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan view:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan event:cache

# Set permissions for storage and bootstrap/cache in the volume if needed
# The Dockerfile handles this internally, but when using shared volumes, we might need to ensure
# the host volume has correct permissions for the www-data user (uid 33 in alpine/debian).
echo "5. Ensuring correct permissions for storage and bootstrap/cache..."
docker compose -f docker-compose.prod.yml exec -T -u root app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
docker compose -f docker-compose.prod.yml exec -T -u root app chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo "6. Restarting background workers (scheduler & queue)..."
docker compose -f docker-compose.prod.yml restart scheduler
# docker compose -f docker-compose.prod.yml exec -T app php artisan queue:restart

echo "======================================"
echo "Deployment completed successfully!"
echo "======================================"
