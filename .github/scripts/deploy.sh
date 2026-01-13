#!/usr/bin/env bash
set -euo pipefail

DEPLOY_PATH="${DEPLOY_PATH:-/var/www/html}"
GIT_BRANCH="${GIT_BRANCH:-main}"

if [ ! -d "$DEPLOY_PATH" ]; then
  echo "Error: deploy path $DEPLOY_PATH does not exist"
  exit 1
fi

cd "$DEPLOY_PATH"

echo "Deploying branch $GIT_BRANCH to $DEPLOY_PATH"

# Make sure we're on the right branch and up-to-date
git fetch --all
git reset --hard "origin/$GIT_BRANCH"

git pull origin "$GIT_BRANCH"

# Composer and Laravel tasks
if command -v composer >/dev/null 2>&1; then
  composer install --no-interaction --prefer-dist --optimize-autoloader
else
  echo "composer not found, skipping composer install"
fi

# Run migrations
php artisan migrate --force

# Cache configs
php artisan config:cache || true
php artisan route:cache || true

echo "Deploy complete"
