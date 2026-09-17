# I-NNOVA

The company website for I-NNOVA, a software company in Bamenda, Cameroon. It
presents the products, the client work, the team and the I-NNOVA Kickstarter
accelerator, in English and French, and every word of it is editable from an
admin panel rather than a template.

## What it is built on

| Layer | Choice |
| --- | --- |
| Framework | Laravel 13 on PHP 8.3+ |
| Admin | Filament 5 |
| Front end | Blade, Tailwind 4, Alpine.js, Vite 8 |
| Content | `spatie/laravel-translatable`, `spatie/laravel-medialibrary` |
| SEO | `spatie/laravel-sitemap` |
| Tests | PHPUnit 12 |

There is no JavaScript framework and no API layer. Pages are server rendered,
Alpine handles the handful of things that need state in the browser (the menu,
the theme toggle, the hero slideshow, scroll reveals), and Vite builds one CSS
file and one JS file.

## Getting started

```bash
composer setup          # install, .env, key, migrate, storage link, npm, build
php artisan migrate --seed
```

The seeder creates a development admin and fills the database with placeholder
content so the site has something to render. It refuses to run in production.

Then run the two processes. On most machines one command does it:

```bash
composer dev            # serve + vite + queue + logs, together
```

On Windows, `php artisan dev` needs `C:\Windows\System32` on your `PATH` to
spawn its child processes. If you get `spawn cmd.exe ENOENT`, either add that
directory to `PATH` or run the two processes yourself in separate terminals:

```bash
php artisan serve       # http://localhost:8000
npm run dev             # Vite, with hot reload
```

### Signing in

The site is at `http://localhost:8000`, the admin at `http://localhost:8000/admin`.

The development seeder creates `admin@i-nnovacmr.com` with the password
`innova-dev-2026`. That account exists for local work only. On a real
deployment, create the first account with:

```bash
php artisan make:filament-user
```

There are no roles. Every user row can sign in to the panel and edit
everything, so only create accounts for people who should have that.

## Everything is editable

The guiding rule of this build: nobody should need a developer to change a
word, a photograph or a phone number.

### Content models

Each of these is a Filament resource under `/admin`, with drag to reorder
where order matters and a per locale tab on every translatable field.

| Resource | What it drives |
| --- | --- |
| Products | The products grid and each product page |
| Case studies | The work index and each case study page |
| Clients, Partners, Sectors | Logos, sectors served, the trust bands |
| Testimonials | Quotes across the site |
| Team members, Process steps | The About page team and how we work |
| Company values, Milestones | What we stand for, and the timeline |
| Kickstarter tracks, Mentors, Alumni outcomes | The Kickstarter page |
| Gallery images | Photographs and video, placed per section |
| Stats | The counted figures on the home and Kickstarter pages |
| Leads | Contact form submissions, read only |
| Site texts | Every fixed string in the templates |

### Site texts

Strings written directly in a template are still editable. `SiteText` rows
override the translator at runtime, and:

```bash
php artisan site:sync-texts            # add a row for every string in the views
php artisan site:sync-texts --prune    # and drop rows for strings no longer used
```

scans `resources/views` for `__('...')` calls, creates a row for anything new,
seeds it with the current wording in each locale and groups it by the page or
section it came from. Run it after adding strings to a view. The site currently
has 154 of them.

### Settings

`/admin/manage-site-settings` is a single record holding the hero copy, the
About story, the mission and vision, contact details, social links, the
Kickstarter URL, SEO defaults, the founding year and the map coordinates.

### Gallery and placement

A gallery row carries either an uploaded file or a remote URL, and an upload
always wins. That is how the stand in photographs work: they are rows with an
`external_url`, and replacing one is a matter of dropping a file on the record.
The admin marks which rows are still stand ins. `GalleryPlacement` decides
where a row appears: the home hero slideshow, the home strip, the About band,
the Kickstarter gallery or the cluster beside the Kickstarter heading. A row
can hold a YouTube or Vimeo link or an uploaded video file, and the gallery
renders a player instead of an image.

### Stats

A stat is either a number somebody typed or one the site counts for itself.
`StatSource` decides which: products live, products total, businesses served,
years building, sectors served, team members or accelerator tracks. Years
building counts from the founding year in Settings, and businesses served
merges the institutions named in case studies with the verified client list.

## Localisation

`config/site.php` holds the locale list and the default. Adding a third
language means changing that array and nothing else: the admin grows a tab, the
language switcher grows an option, and the routes follow.

Every public route is prefixed with the locale (`/en/about`, `/fr/about`). The
prefix is bound as a route default, so `route('about')` gives the current
locale without being passed one, and the parameter is stripped before it
reaches a controller so model binding still works.

Translated content lives in JSON columns via `spatie/laravel-translatable`.
Filament's translatable plugin is abandoned, so locale tabs are a small custom
component binding to `field.locale` paths.

## Front end

### Design tokens

`resources/css/app.css` holds the whole visual system: the brand palette, the
type scale, the spacing bands, the motion vocabulary, the section seams and the
tech motifs. Templates use semantic tokens (`bg-paper`, `text-content`,
`border-hairline`) rather than raw colours.

### Dark mode

Dark mode is a redefinition of those tokens and nothing else. There is not a
single `dark:` variant in any template. The toggle sets `data-theme` on the
root and remembers the choice; with no choice stored the system preference
wins, applied by an inline script before first paint so there is no flash.

Four tokens carry the weight:

- `--color-content` is the foreground, and flips.
- `--color-hairline` is borders, and flips.
- `--color-ink` is the dark contrast band, and stays dark in both modes.
- `--color-shade` is shadows, and never flips.

Every text and non text pair was checked against WCAG AA (4.5:1 and 3:1) in
both modes. `DarkModeTest` and `AccessibilityTest` keep it that way.

One trap worth knowing: Tailwind 4 prunes custom declarations inside
`@layer theme`, so the dark token block sits outside it. Moving it in makes
dark mode silently disappear from the build.

### Motion

Scroll reveals, counted stats, the hero slideshow, drawn rules and card lifts,
all built on a shared easing and delay vocabulary. Everything respects
`prefers-reduced-motion`. A progress line runs across the top of the page while
a link is loading.

### Navigation

`app/Support/Navigation.php` builds the header menu. Products fills itself from
what is published, so a new product reaches the menu without a template change.
Anchor items (the tracks, the gallery, how we work, the team) are only offered
when the section they point at is actually rendered, because every one of those
sections is conditional on having content.

## Project layout

```
app/
  Enums/              GalleryPlacement, ProductStatus, StatSource and friends
  Filament/           Resources, the settings page, the dashboard widgets
  Http/Controllers/   One controller per public page
  Models/             The content models
  Support/            Navigation
  Translation/        The loader that lets SiteText override the translator
resources/
  css/app.css         The entire design system
  views/
    components/       brand, form, seo, tech, ui
    layouts/          The page shell
    pages/            One per route
    partials/         Header, footer
    sections/         Reusable page bands
database/
  seeders/Placeholder/  The placeholder content, split by area
tests/Feature/        One file per concern
```

## Testing

```bash
composer test         # clears config, then runs the suite
php artisan test
php artisan test tests/Feature/DarkModeTest.php
```

230 tests. They cover more than the happy path: contrast ratios in both
themes, the query count per page (so an added record cannot quietly become an
N+1), that placeholder contact details never reappear, that every anchor in
the menu points at a section that exists, and that admin pages load for a user
who can reach the panel.

`phpunit.xml` raises `memory_limit` to 1G, because seeding runs image
conversions and the default limit kills the process partway through.

## Deploying

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize
php artisan make:filament-user
php artisan site:sync-texts
```

Set `APP_ENV=production` and `APP_DEBUG=false`. The database seeder is a no op
in production by design, so the site starts empty and is filled through the
admin. Point `FILESYSTEM_DISK` at wherever uploads should live and make sure
the queue is running if you want media conversions off the request cycle.

`sitemap.xml` is generated from the published content and served at the root.

## Conventions

- No em dashes anywhere, in code, comments, content or commits.
- Templates use semantic tokens, never raw colours and never `dark:`.
- Anything a non developer might want to change belongs in the admin.
- Run `php artisan site:sync-texts` after adding a string to a view.
