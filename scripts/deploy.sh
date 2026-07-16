#!/usr/bin/env bash
set -euo pipefail

# Runs on the production server, invoked by .github/workflows/ci-cd.yml over SSH.
# Assumes docker-compose.prod.yml + a real .env already exist at this path
# (checked out/created once by hand during server provisioning), and that the
# caller has already `docker login ghcr.io` with pull access to the image.
# IMAGE/TAG must be exported in the environment (the workflow does this).

cd "$(dirname "$0")/.."

docker compose -f docker-compose.prod.yml pull
docker compose -f docker-compose.prod.yml up -d --remove-orphans

docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force
docker compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan view:cache

docker image prune -f
