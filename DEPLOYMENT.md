# Deployment

The live site is **https://i-nnovacmr.com**, served from an Oracle Cloud VPS.
This file is the runbook: what is where, how to ship a change, and what to
check when something looks wrong.

**No secrets live in this file.** The repository is public. See
[Where the secrets are](#where-the-secrets-are).

## History

Until November 2026 the site ran on an AWS EC2 instance (`3.217.74.87`). That
instance was taken down with no backup of any kind, and the live database and
uploads went with it. The site was rebuilt on the current server on
5 November 2026 from a developer's local copy of the content, last edited on
22 September. Anything changed on the live site after that date was lost.

That is why the server now backs itself up every night, and why the copies
must also be pulled off the server. See [Backups](#backups).

## The box

| | |
| --- | --- |
| Host | `138.2.210.63` |
| Access | `ssh -i ssh-key-2026-09-28.key ubuntu@138.2.210.63` |
| OS | Ubuntu 24.04 LTS |
| Resources | 2 vCPU, about 1 GB RAM, 2 GB swap, 45 GB disk |
| Web server | nginx 1.24 |
| PHP | 8.4 via php8.4-fpm, in its own pool, for this site. 8.3 is the box default |
| Database | MySQL 8.0 |
| Also installed | Composer, git, certbot |

`ubuntu` has passwordless sudo.

### This box is shared

Other apps run here. **Do not assume you can restart, reconfigure or clear
anything without checking what else it affects.**

| App | Path | Notes |
| --- | --- | --- |
| i-nnovacmr.com | `/var/www/innova` | This site. Database `innovadb` |
| E-Ride | `/var/www/e-ride` | Served on the bare IP over HTTP, plus a Reverb websocket service (`reverb.service`, port 6001). Uses PHP 8.3 |
| EduTrust | `/var/www/edutrustshsm` | Files only, no nginx site enabled |

### Two PHP versions, on purpose

The site needs PHP 8.4 or newer (Symfony 8). The box's default `php` is 8.3,
which E-Ride uses, and it stays that way. So:

- **Always run this site's commands with `php8.4`**, for example
  `php8.4 artisan migrate`, and Composer as `php8.4 /usr/bin/composer`.
  Plain `php artisan` runs on 8.3 and fails.
- PHP 8.4 comes from the `ppa:ondrej/php` repository.
- The site has its own php-fpm pool, `[innova]`, on `/run/php/innova.sock`.
  Its settings (upload limits, memory, worker count) live in that pool file,
  so changing them affects nothing else. The stock 8.4 `www` pool is disabled.
- After `update-alternatives` or a PHP package install, check that `php -v`
  still says 8.3, or E-Ride's Reverb service breaks on its next restart.

### Memory is the binding constraint

About 1 GB of RAM, shared. **Do not run `npm install` or `npm run build` on
this box.** Assets are built locally and shipped by `deploy.sh`.

The pool is `pm = ondemand` with at most 4 workers, so an idle site costs only
its master process.

## Oracle Cloud networking

Oracle Cloud filters traffic **before** it reaches the server, in the VCN's
Security List (or a Network Security Group). Ports 80 and 443 must be open
there to `0.0.0.0/0` as well as in `ufw` on the box. When they are closed,
the site times out from outside while `ufw` and nginx look perfectly healthy.

## Application layout

```
/var/www/innova
  .env                  production config, mode 640
  public/               nginx document root
  public/build/         built CSS and JS, shipped from a developer machine
  public/storage        symlink to storage/app/public
  storage/app/public/   uploaded media
```

Ownership is `ubuntu:www-data` throughout. `storage` and `bootstrap/cache` are
`2775` on directories and `664` on files, so php-fpm (running as `www-data`)
can keep writing uploads and caches.

### Environment

`APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://i-nnovacmr.com`,
`SESSION_SECURE_COOKIE=true`. Database, session, cache and queue all use MySQL.
`MAIL_MAILER=log`, because nothing sends mail yet (see
[Known gaps](#known-gaps)).

No queue worker is needed: image conversions are set to run during the upload
request.

The `innova` database user has rights on `innovadb` only.

## Server configuration in the repo

The previous server's nginx config was never saved anywhere, so it went with
the server. Copies of everything set up by hand on this one are in
`docs/server/`:

| File | Installed at |
| --- | --- |
| `innova.conf` | `/etc/nginx/sites-available/innova.conf`, symlinked into `sites-enabled` |
| `innova-fpm.conf` | `/etc/php/8.4/fpm/pool.d/innova.conf` |
| `innova-backup.sh` | `/usr/local/bin/innova-backup`, run from `/etc/cron.d/innova-backup` |

The live nginx file also holds the HTTPS lines certbot added. If you change
one of these on the server, change the copy here too.

## Deploying a change

From a developer machine, on an up to date `main`:

```bash
./deploy.sh
```

It refuses to run on a dirty tree or anything other than `origin/main`, runs
the tests, builds assets locally, ships them, then on the server pulls,
installs, migrates, syncs the site texts, caches and fixes permissions. It
prints the commit to roll back to.

## nginx

Sites live in `/etc/nginx/sites-available/` and are enabled by symlink in
`sites-enabled/`.

**Always validate before reloading. A reload with a bad config takes every
site on the box down.**

```bash
sudo nginx -t && sudo systemctl reload nginx
```

### Health check

```bash
for p in /en /fr /admin/login; do
  printf '%-14s %s\n' $p \
    "$(curl -s -o /dev/null -w '%{http_code}' -k -H 'Host: i-nnovacmr.com' https://127.0.0.1$p)"
done
```

Expected: `200` for each.

## Certificates

Let's Encrypt via certbot (`/usr/bin/certbot`, the Ubuntu package), covering
`i-nnovacmr.com` and `www.i-nnovacmr.com`. Ubuntu's own `certbot.timer`
renews every certificate on the box twice a day:

```bash
systemctl list-timers certbot.timer
sudo certbot renew --dry-run
```

## DNS

`i-nnovacmr.com` is registered through **Hostinger**, and its DNS is managed
there. Both records point at the box:

| Type | Name | Value |
| --- | --- | --- |
| A | `@` | `138.2.210.63` |
| A | `www` | `138.2.210.63` |

The site from before the rebuild still exists on Hostinger's own hosting at
`185.77.97.29`. It is the much older design, but pointing the A records there
is a last resort if this server is unavailable.

`i-nnova.com` is a different company's site on Wix. It is not ours.

## Backups

Every night at 02:30 UTC, `/usr/local/bin/innova-backup` writes the database
and the uploaded media to `/var/backups/innova`, keeping 14 days. A night costs
about 75 MB, almost all of it media.

**Those copies live on the same server.** They cover a bad migration or a
mistaken delete, not losing the server, which is exactly what happened last
time. Pull the latest set to a machine you control regularly, at least after
any real content change:

```bash
KEY=~/OneDrive/Desktop/Duke/I-NNOVA/keys/ssh-key-2026-09-28.key
DEST=~/OneDrive/Desktop/Duke/I-NNOVA/backups
ssh -i "$KEY" ubuntu@138.2.210.63 \
  'sudo sh -c "cd /var/backups/innova && tar cf - \$(ls -t innovadb-* | head -1) \$(ls -t media-* | head -1)"' \
  | tar xf - -C "$DEST"
```

To restore the database from a dump:

```bash
gunzip -c innovadb-YYYYMMDD-HHMM.sql.gz | sudo mysql innovadb
```

And the media:

```bash
sudo tar xzf media-YYYYMMDD-HHMM.tar.gz -C /var/www/innova/storage/app
sudo chown -R ubuntu:www-data /var/www/innova/storage/app/public
```

## The admin

`https://i-nnovacmr.com/admin/login`

Every user row can sign in and edit everything. There are no roles, so only
create accounts for people who should have full access.

```bash
cd /var/www/innova && php8.4 artisan make:filament-user
```

Everything on the public site is editable from here, including the fixed
strings in the templates, which are synced into the `site_texts` table by
`php8.4 artisan site:sync-texts`.

## Where the secrets are

Nothing secret is committed to this repository, and nothing should be.

| Secret | Where it lives |
| --- | --- |
| SSH private key | `keys/ssh-key-2026-09-28.key`, on the maintainer's machine. Never commit it |
| Database password | `/var/www/innova/.env` on the server, mode 640 |
| `APP_KEY` | Same file. Losing it logs everyone out and makes encrypted values unreadable, so keep a copy off the server |
| Admin login | Keep it in a password manager |

## Known gaps

Ordered by how much they matter.

- **Backups do not leave the server on their own.** See [Backups](#backups).
  An automatic off-site copy (object storage, or a scheduled pull) is the next
  step.
- **Nobody is told when an enquiry arrives.** The contact form writes a `Lead`
  row and sends no mail, so someone has to open the admin and look.
- **Contact details are missing** from the footer. The placeholders were
  deliberately excluded and a test keeps them out, so the real phone number and
  email address need entering in the admin.
- **No Privacy or Terms pages.** Nothing links to them, so nothing is broken,
  but a public site collecting enquiries should have both.
- **The photography is still stand-ins.** Real photographs replace them by
  upload in the admin, with no code change.
