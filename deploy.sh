#!/bin/bash

# Deploy script for the live (Linux) server
# Usage:
#   ./deploy.sh            # code-only deploy (no image rebuild)
#   ./deploy.sh --rebuild  # also rebuild the PHP image (use when docker/php/Dockerfile changed)

set -euo pipefail

cd "$(dirname "$0")"

BRANCH="main"
REBUILD=false
[[ "${1:-}" == "--rebuild" ]] && REBUILD=true

echo "==> Pulling latest code ($BRANCH)"
git pull origin "$BRANCH"

if $REBUILD; then
    # Build while the old containers keep serving; downtime is only the swap in `up -d`
    echo "==> Building image"
    docker compose build
    echo "==> Recreating containers"
    docker compose up -d
    docker image prune -f
fi

echo "==> Installing Composer dependencies"
docker compose exec -T app composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Running migrations"
docker compose exec -T app php artisan migrate --force

echo "==> Building frontend assets"
if command -v npm >/dev/null 2>&1; then
    npm ci
    npm run build
else
    echo "    WARNING: npm not found on host, skipping asset build (public/build is not in git)"
fi

echo "==> Caching config, routes, views"
docker compose exec -T app php artisan optimize

echo "==> Restarting background workers"
# Workers finish their current job, then exit; `restart: unless-stopped` brings them back with new code
docker compose exec -T app php artisan queue:restart
docker compose restart reverb

echo "==> Deploy complete"
