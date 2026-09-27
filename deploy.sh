#!/bin/bash
set -e
cd ~/Smart_agro_backend_laravel_new
git pull origin main
docker compose build
docker compose up -d
docker compose exec -T app composer install --no-dev --optimize-autoloader --no-interaction
docker compose exec -T app php artisan migrate --force
docker compose exec -T app php artisan optimize:clear
docker compose restart queue
docker image prune -f
echo "Deploy complete: $(date)"
