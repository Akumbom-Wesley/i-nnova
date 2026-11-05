#!/usr/bin/env bash
#
# Deploy whatever is on origin/main to the live site.
#
# Run it from a developer machine, not from the server:
#
#     ./deploy.sh
#
# Assets are built here and shipped, because the box has about 1 GB of RAM
# and Vite will not fit in it. Everything else happens over SSH.
#
set -euo pipefail

HOST="${INNOVA_HOST:-ubuntu@138.2.210.63}"
KEY="${INNOVA_KEY:-$HOME/OneDrive/Desktop/Duke/I-NNOVA/keys/ssh-key-2026-09-28.key}"
APP="/var/www/innova"

# The site needs PHP 8.4 or newer. The box's default php is 8.3, which other
# apps on it use, so always name the version.
PHP="php8.4"

ssh_run() { ssh -i "$KEY" -o BatchMode=yes "$HOST" "$@"; }

say() { printf '\n\033[1m==> %s\033[0m\n' "$1"; }

# ---------------------------------------------------------------------------
say "Checking the tree is clean and up to date"
# ---------------------------------------------------------------------------
if [ -n "$(git status --porcelain)" ]; then
    echo "Working tree is dirty. Commit or stash first." >&2
    exit 1
fi

git fetch --quiet origin main
if [ "$(git rev-parse HEAD)" != "$(git rev-parse origin/main)" ]; then
    echo "HEAD is not origin/main. Deploy what is reviewed, not what is local." >&2
    exit 1
fi

# ---------------------------------------------------------------------------
say "Running the tests"
# ---------------------------------------------------------------------------
# A deploy that skips this is a deploy that finds out in production.
php artisan test --quiet

# ---------------------------------------------------------------------------
say "Building assets here"
# ---------------------------------------------------------------------------
npm run build

# ---------------------------------------------------------------------------
say "Recording what is live now, so a rollback knows where to go back to"
# ---------------------------------------------------------------------------
PREVIOUS=$(ssh_run "cd $APP && git rev-parse --short HEAD")
echo "    currently live: $PREVIOUS"
echo "    deploying:      $(git rev-parse --short HEAD)"

# ---------------------------------------------------------------------------
say "Shipping assets"
# ---------------------------------------------------------------------------
tar czf - public/build | ssh_run "tar xzf - -C $APP"

# ---------------------------------------------------------------------------
say "Deploying"
# ---------------------------------------------------------------------------
ssh_run "bash -s" <<REMOTE
set -euo pipefail
cd $APP

git fetch --quiet origin main
git reset --hard --quiet origin/main

# niced, because two cores are shared with other apps
nice -n 15 $PHP /usr/bin/composer install --no-dev --optimize-autoloader --no-interaction --no-progress --quiet

$PHP artisan migrate --force --no-interaction

# Any new __('...') string in a view becomes editable in the admin.
$PHP artisan site:sync-texts

$PHP artisan optimize

# php-fpm runs as www-data and must keep being able to write uploads and caches.
sudo chown -R ubuntu:www-data $APP
sudo find $APP/storage $APP/bootstrap/cache -type d -exec chmod 2775 {} +
sudo find $APP/storage $APP/bootstrap/cache -type f -exec chmod 664 {} +
sudo chmod 640 $APP/.env

echo "    now at \$(git rev-parse --short HEAD)"
REMOTE

# ---------------------------------------------------------------------------
say "Checking the site answers"
# ---------------------------------------------------------------------------
ssh_run "bash -s" <<'REMOTE'
for p in /en /fr /admin/login; do
    printf '    %-14s %s\n' "$p" \
        "$(curl -s -o /dev/null -w '%{http_code}' -k --max-time 15 -H 'Host: i-nnovacmr.com' "https://127.0.0.1$p")"
done
REMOTE

echo
echo "    Expected: 200 for each"
echo
echo "    To roll back:"
echo "      ssh -i \"\$KEY\" $HOST 'cd $APP && git reset --hard $PREVIOUS && $PHP artisan optimize'"
echo
