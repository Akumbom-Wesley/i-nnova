#!/usr/bin/env bash
#
# Deploy whatever is on origin/main to the live site.
#
# Run it from a developer machine, not from the server:
#
#     ./deploy.sh
#
# Assets are built here and shipped, because the box has 916 MB of RAM and
# Vite will not fit in it. Everything else happens over SSH.
#
set -euo pipefail

HOST="${INNOVA_HOST:-ec2-user@3.217.74.87}"
KEY="${INNOVA_KEY:-$HOME/OneDrive/Desktop/Duke/I-NNOVA/i-nnova_key.pem}"
APP="/var/www/innova"

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
ssh_run "bash -s" <<EOF
set -euo pipefail
cd $APP

git fetch --quiet origin main
git reset --hard --quiet origin/main

# niced, because two cores are shared with three other live sites
nice -n 15 composer install --no-dev --optimize-autoloader --no-interaction --no-progress --quiet

php artisan migrate --force --no-interaction

# Any new __('...') string in a view becomes editable in the admin.
php artisan site:sync-texts

php artisan optimize

# php-fpm runs as apache and must keep being able to write uploads and caches.
sudo chown -R ec2-user:apache $APP
sudo find $APP/storage $APP/bootstrap/cache -type d -exec chmod 2775 {} +
sudo find $APP/storage $APP/bootstrap/cache -type f -exec chmod 664 {} +
sudo chmod 640 $APP/.env

echo "    now at \$(git rev-parse --short HEAD)"
EOF

# ---------------------------------------------------------------------------
say "Checking every site on the box, not just this one"
# ---------------------------------------------------------------------------
ssh_run "bash -s" <<'EOF'
for t in "i-nnovacmr.com|https" "nailblissbyfaith.com|https" "3.217.74.87|http" "tontine.com|http"; do
    n=${t%%|*}; s=${t##*|}
    printf '    %-24s %s\n' "$n" \
        "$(curl -s -o /dev/null -w '%{http_code}' -k --max-time 15 -H "Host: $n" "$s://127.0.0.1/")"
done
EOF

echo
echo "    Expected: 302 i-nnovacmr, 200 nailbliss, 302 edutrust, 200 tontine"
echo
echo "    To roll back:"
echo "      ssh -i \"\$KEY\" $HOST 'cd $APP && git reset --hard $PREVIOUS && php artisan optimize'"
echo
