# CI/CD Deployment (GitHub Actions) ⚙️

This repository includes a GitHub Actions workflow that automates deploys when commits are merged to `main`. The workflow will SSH into your server and run the pull + migrations.

## What the workflow does ✅
- Runs on push to `main` (and can be triggered manually via `workflow_dispatch`).
- SSHes to your server and executes:
  - `git pull origin main`
  - `composer install --no-interaction --prefer-dist --optimize-autoloader` (if composer exists)
  - `php artisan migrate --force`
  - `php artisan config:cache` and `php artisan route:cache`

## Required GitHub Secrets 🔒
Add the following secrets to your repository (Settings → Secrets & variables → Actions):
- `SSH_HOST` — server hostname or IP (e.g., `1.2.3.4`)
- `SSH_USER` — SSH user (e.g., `deploy`)
- `SSH_PRIVATE_KEY` — private key content for the SSH user (the matching public key must be on the server in `~/.ssh/authorized_keys`)
- `SSH_PORT` — optional (defaults to 22)
- `DEPLOY_PATH` — optional path to your application on the server (defaults to `/var/www/html`)

Optional: `GIT_BRANCH` if you want to use a different deploy branch (workflow currently deploys `main`).

## Server setup checklist 🔧
1. Create a deploy user on the server (or use existing) and place the public key in `~/.ssh/authorized_keys` for that user.
2. Ensure the deploy user has permission to read/write the repo path and run artisan (or use sudo where appropriate).
3. Make sure `git` and `php` and `composer` are available on the server.
4. The repo must already exist and have the correct remote `origin` configured in the deploy path.

## How to test 🧪
- Add your secrets, then merge a test commit into `main` and watch the Actions tab for the `Deploy to Server` workflow.
- You can also trigger the workflow manually from Actions → the workflow → `Run workflow`.

## Troubleshooting tips ⚠️
- If SSH fails: ensure the key, host, and user are correct and that the server allows SSH from GitHub's action runners.
- Add `Known hosts` to avoid interactive confirmation, or ensure the server's host key is accepted for the deploy user.
- Check action logs in GitHub Actions for detailed errors, then SSH to the server and run commands manually to debug.

---
If you want, I can:
- add a webhook-style deployment (e.g., using a small receiver on the server), or
- modify the workflow to run extra tasks (npm build, queue restart), or
- add a `maintenance` toggle before migrations.

Tell me which (if any) additional steps you'd like me to take. 🎯