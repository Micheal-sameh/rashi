#!/bin/bash

# Deploy script for the live (Linux) server
# Usage:
#   ./deploy.sh            # code-only deploy (no image rebuild)
#   ./deploy.sh --rebuild  # also rebuild the PHP image (use when docker/php/Dockerfile changed)
#   ./deploy.sh --rollback # switch containers back to the previous image build (code is not touched)

set -euo pipefail

cd "$(dirname "$0")"

BRANCH="main"
IMAGE="rashi-php"
KEEP_BUILDS=2
REBUILD=false
[[ "${1:-}" == "--rebuild" ]] && REBUILD=true

if [[ "${1:-}" == "--rollback" ]]; then
    PREVIOUS=$(docker image ls "$IMAGE" --format '{{.Tag}}' | grep -E '^[0-9]{14}$' | sort -r | sed -n 2p)
    if [[ -z "$PREVIOUS" ]]; then
        echo "No previous build found to roll back to"
        exit 1
    fi
    echo "==> Rolling back to $IMAGE:$PREVIOUS"
    docker tag "$IMAGE:$PREVIOUS" "$IMAGE:latest"
    docker compose up -d
    echo "==> Rollback complete"
    exit 0
fi

echo "==> Pulling latest code ($BRANCH)"
git pull origin "$BRANCH"

if $REBUILD; then
    # Build while the old containers keep serving; downtime is only the swap in `up -d`
    echo "==> Building image"
    docker compose build
    # Also tag the build with a timestamp so older builds stay available for rollback
    docker tag "$IMAGE:latest" "$IMAGE:$(date +%Y%m%d%H%M%S)"
    echo "==> Recreating containers"
    docker compose up -d

    echo "==> Keeping last $KEEP_BUILDS builds, removing older ones"
    docker image ls "$IMAGE" --format '{{.Tag}}' \
        | grep -E '^[0-9]{14}$' \
        | sort -r \
        | tail -n +$((KEEP_BUILDS + 1)) \
        | xargs -r -I{} docker rmi "$IMAGE:{}"
    docker image prune -f
    docker builder prune -f --filter until=168h
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
