# Deployment

The live site is **https://i-nnovacmr.com**, served from an AWS EC2 instance in
`us-east-1`. This file is the runbook: what is where, how to ship a change, and
what to check when something looks wrong.

**No secrets live in this file.** The repository is public. See
[Where the secrets are](#where-the-secrets-are).

## The box

| | |
| --- | --- |
| Host | `3.217.74.87` |
| Access | `ssh -i i-nnova_key.pem ec2-user@3.217.74.87` |
| OS | Amazon Linux 2023 |
| Resources | 2 vCPU, 916 MB RAM, 8 GB disk |
| Web server | nginx 1.28.3 |
| PHP | 8.5.4 via php-fpm, running as user `apache` on `/run/php-fpm/www.sock` |
| Database | MariaDB 10.5.29 |
| Also installed | Composer 2.10.2, Node 20.20.2, git 2.50.1, certbot 4.2.0 |

`ec2-user` has passwordless sudo.

### This box is shared

Three other things run here. **Do not assume you can restart, reconfigure or
clear anything without checking what else it affects.**

| Site | Path | Database | State |
| --- | --- | --- | --- |
| i-nnovacmr.com | `/var/www/innova` | `innovadb` | Live, HTTPS |
| nailblissbyfaith.com | `/var/www/Mrs-Muki-Site` | none (static) | Live, HTTPS. A third party's site |
| EduTrust | `/var/www/edutrustshsm` | `edutrustshsmdb` | Reachable only at `http://3.217.74.87`, no TLS |
| Tontine | `/var/www/tontine` | `tontinedb` | **Unreachable.** Its `server_name` is `tontine.com`, which points at Cloudflare and belongs to someone else |

php-fpm is a single shared pool. A change to `/etc/php-fpm.d/` or `php.ini`
affects all four. Per-site PHP settings go in the nginx block instead, via
`fastcgi_param PHP_VALUE` (this site already does that for upload limits).

### Memory is the binding constraint

916 MB of RAM with a 1 GB swap file at `/swapfile` (`vm.swappiness=10`). Before
the swap existed there was no margin at all, and an OOM kill would have taken
the other three sites down with it.

**Do not run `npm install` or `npm run build` on this box.** Assets are built
locally and shipped. `node_modules` is never installed there, which also keeps
roughly 300 MB off a disk that has about 2.9 GB free.

`pm.max_children` is set to 50, which is far more workers than this much memory
can hold. It has not been changed because it is shared with the other sites,
but it should come down to 8 to 10.

## Application layout

```
/var/www/innova
  .env                  production config, mode 640, owner ec2-user:apache
  public/               nginx document root
  public/build/         built CSS and JS, shipped from a developer machine
  public/storage        symlink to storage/app/public
  storage/app/public/   uploaded media, 2775 so php-fpm can write
```

Ownership is `ec2-user:apache` throughout. `storage` and `bootstrap/cache` are
`2775` on directories and `664` on files; the setgid bit keeps admin uploads in
the `apache` group so php-fpm can keep writing to them.

### Environment

`APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://i-nnovacmr.com`,
`SESSION_SECURE_COOKIE=true`. Database, session, cache and queue all use MySQL.
`MAIL_MAILER=log`, because nothing sends mail yet (see
[Known gaps](#known-gaps)).

The `innova` database user is granted rights on `innovadb` only and cannot see
`edutrustshsmdb` or `tontinedb`.

## Deploying a change

Assets are built locally because Vite will not fit in this much memory.

**1. Locally, build and ship the assets:**

```bash
npm run build
tar czf - public/build | ssh -i i-nnova_key.pem ec2-user@3.217.74.87 \
  'tar xzf - -C /var/www/innova'
```

**2. On the box:**

```bash
cd /var/www/innova
git fetch origin main && git reset --hard origin/main
nice -n 15 composer install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan optimize

sudo chown -R ec2-user:apache /var/www/innova
sudo find /var/www/innova/storage /var/www/innova/bootstrap/cache -type d -exec chmod 2775 {} +
sudo find /var/www/innova/storage /var/www/innova/bootstrap/cache -type f -exec chmod 664 {} +
sudo chmod 640 /var/www/innova/.env
```

**3. After adding any new `__('...')` string to a view**, so it stays editable
in the admin:

```bash
php artisan site:sync-texts
```

`nice -n 15` matters: it keeps Composer yielding to the live sites on a box
with two cores.

## nginx

Config lives in `/etc/nginx/conf.d/`, loaded alphabetically as `*.conf`.

| File | Purpose |
| --- | --- |
| `000-default-reject.conf` | Explicit HTTPS default. Sorts first so an unknown hostname gets the connection closed rather than whichever real site happens to come first |
| `innova.conf` | This site. Apex serves the app, `www` 301s to the apex |
| `nailblissbyfaith.com.conf` | Third party static site |
| `edutrustshsm.conf` | EduTrust, on the bare IP |
| `tontine.conf` | Tontine, currently matching nothing |

**Always validate before reloading. A reload with a bad config takes all four
sites down.**

```bash
sudo nginx -t && sudo systemctl reload nginx
```

### Health check

Run this after any change. It checks every site by name without needing DNS:

```bash
for t in "i-nnovacmr.com|https" "nailblissbyfaith.com|https" \
         "3.217.74.87|http" "tontine.com|http"; do
  n=${t%%|*}; s=${t##*|}
  printf '%-26s %s\n' "$n" \
    "$(curl -s -o /dev/null -w '%{http_code}' -k -H "Host: $n" "$s://127.0.0.1/")"
done
```

Expected: `302` for i-nnovacmr.com (the locale redirect to `/en`), `200` for
nailbliss, `302` for EduTrust (its login redirect), `200` for Tontine.

## Certificates

Let's Encrypt via certbot, covering `i-nnovacmr.com` and `www.i-nnovacmr.com`,
issued 19 September 2026 and valid to 18 December 2026.

Renewal runs from a systemd timer, twice daily with an hour of jitter:

```bash
systemctl list-timers certbot-renew.timer
sudo certbot renew --dry-run
```

The unit is `/etc/systemd/system/certbot-renew.{service,timer}` and it reloads
nginx only when a certificate actually changed. It covers **every** certificate
on the host, including nailblissbyfaith.com.

Note that certbot is at `/usr/local/bin/certbot`, not `/usr/bin/certbot`. The
unit file needs the full path or it fails with `203/EXEC`.

**Before this timer existed there was no renewal of any kind on this box.** The
nailblissbyfaith.com certificate would have expired on 14 November 2026 and
that site would have gone dark.

## DNS

`i-nnovacmr.com` is registered through **Hostinger**, and its DNS is managed
there. Both records point at the box:

| Type | Name | Value |
| --- | --- | --- |
| A | `@` | `3.217.74.87` |
| A | `www` | `3.217.74.87` |

The previous site still exists on Hostinger's own hosting. It is no longer
reachable, because DNS points here, but the files are still there. That makes
**rollback a DNS change**: point the A records back at Hostinger and the old
site returns.

`i-nnova.com` is a different company's site on Wix. It is not ours.

## The admin

`https://i-nnovacmr.com/admin/login`

Every user row can sign in and edit everything. There are no roles, so only
create accounts for people who should have full access.

```bash
cd /var/www/innova && php artisan make:filament-user
```

Everything on the public site is editable from here, including the fixed
strings in the templates, which are synced into the `site_texts` table by
`php artisan site:sync-texts`.

## Where the secrets are

Nothing secret is committed to this repository, and nothing should be.

| Secret | Where it lives |
| --- | --- |
| SSH private key | `i-nnova_key.pem`, on the maintainer's machine. Never commit it |
| Database password | `/var/www/innova/.env` on the server, mode 640 |
| `APP_KEY` | Same file. Losing it makes existing encrypted values unreadable |
| Admin login | Set at deploy time, then changed by the owner. Keep it in a password manager |

To read the database password when you need it:

```bash
ssh -i i-nnova_key.pem ec2-user@3.217.74.87 'sudo grep DB_PASSWORD /var/www/innova/.env'
```

## Known gaps

Ordered by how much they matter.

- **There are no backups.** Nothing on this box is backed up, this site
  included, and neither are EduTrust or Tontine. The database is about 1.1 MB
  and media about 34 MB, so this is cheap to fix and has simply not been done.
  Backups on the same EBS volume would protect against a bad migration or a
  mistaken delete but not against losing the instance; copies belong in S3.
- **Nobody is told when an enquiry arrives.** The contact form writes a `Lead`
  row and sends no mail, so someone has to open the admin and look.
- **EduTrust has no TLS.** It answers on a bare IP over plain HTTP, so staff
  passwords for a school management system cross the network unencrypted. It
  needs a hostname before it can have a certificate.
- **Tontine is unreachable** and has been for some time. Either give it a
  hostname or remove it and reclaim 361 MB.
- **Contact details are missing** from the footer. The placeholders were
  deliberately excluded and a test keeps them out, so the real phone number and
  email address need entering in the admin.
- **No Privacy or Terms pages.** Nothing links to them, so nothing is broken,
  but a public site collecting enquiries should have both.
- **The photography is still stand-ins.** Real photographs replace them by
  upload in the admin, with no code change.
- **`pm.max_children = 50`** on a 916 MB box, as above.
