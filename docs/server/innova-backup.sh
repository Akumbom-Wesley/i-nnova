#!/usr/bin/env bash
#
# Nightly backup of i-nnovacmr.com: the database and the uploaded media.
#
# Kept in the repo at docs/server/. Installed at /usr/local/bin/innova-backup and run as root from
# /etc/cron.d/innova-backup. Keeps 14 days in /var/backups/innova.
#
# These copies sit on the same server as the site, so they protect against a
# bad migration or a mistaken delete, not against losing the server. That is
# how the previous server's content was lost. Pull a copy off the box
# regularly; DEPLOYMENT.md has the command.
#
set -euo pipefail

DEST=/var/backups/innova
APP=/var/www/innova
STAMP=$(date +%Y%m%d-%H%M)

mkdir -p "$DEST"
chmod 700 "$DEST"

mysqldump --single-transaction --quick innovadb | gzip > "$DEST/innovadb-$STAMP.sql.gz"
tar czf "$DEST/media-$STAMP.tar.gz" -C "$APP/storage/app" public

find "$DEST" -type f -mtime +14 -delete
