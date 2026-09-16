# I-NNOVA Website Rebuild

Working plan and sprint checklist. Tick items as they land. One branch per sprint, merged to `main` when the sprint's checklist is complete.

---

## Locked decisions

| Decision | Choice |
|---|---|
| Company name | **I-NNOVA**, no longer "I-NNOVA CM" / "I-NNOVAcmr" |
| Stack | Laravel 13 + Blade + Tailwind 4 + Alpine.js |
| Admin / CMS | Filament v5 (self-hosted, no custom CMS build) |
| Rendering | Server-rendered HTML, no SPA |
| Direction | Institutional restraint, in brand colours: blue ground, orange rationed to CTAs |
| Languages | Bilingual EN / FR |
| Blog | **None** |
| Tagline | **"Build Your Creativity"**, confirmed |
| Positioning | Institutional & education software, led by real deployments (PAXHI, SAHIK) |
| Kickstarter | Main site sells, hands off to `innovakickstarter.com` |
| Partner logos | **Removed**, not real relationships |

### Narrative spine

> We don’t just build software. We build the builders.

Values and people carry equal weight with products. Three CMS-driven sections do that work: **Values**, **How We Work**, **Team**.

---

## Design system

Source of truth: **`docs/brand-guide.pdf`**, except on colour, where client direction overrides it (see below).

### Core colours: blue + orange on white

Client direction **overrides the brand guide here**: the guide's red `#EA2A34` and black grounds
are not used on the website. Values are sampled from the logo artwork itself
(`public/images/logo-mark.png`), so the UI matches the mark it sits beside.

| Token | Hex | Use | Contrast |
|---|---|---|---|
| `primary` | `#1157B6` | Blue, institutional ground, links, headings | 6.86:1 both ways ✅ |
| `accent` | `#E85D0C` | Orange, for CTAs, active states, rules. Rationed, never a field | 3.50:1 w/ white |
| `accent-dark` | `#C74F0C` | Hover state | 4.61:1 w/ white ✅ |
| `accent-text` | `#B8460A` | Orange *as text* on white | 5.36:1 ✅ |
| `ink` | `#0A1A33` | Deep navy, not black; stays in the blue family | 17.38:1 ✅ |
| `paper` | `#FFFFFF` | Light ground | n/a |

**One accessibility note:** white text on `accent` is 3.50:1, which clears AA for UI components and
large text, but not for body copy. Orange CTA labels are therefore set at 16px semibold minimum.
Anywhere orange needs to carry small text, use `accent-dark` or `accent-text` instead.

### Visual direction: decided

**Institutional restraint, in blue and orange.** veridyl-style whitespace and calm hierarchy.

- White fields, generous margins
- Blue `#1157B6` as the institutional ground and band colour
- Orange `#E85D0C` rationed to CTAs and active states, roughly 5% of any screen
- Deep navy `#0A1A33` for type and dark grounds; no pure black, no red

### Logo rules, from brand guide Section 3

Do not crop · do not change transparency · do not invert · do not change colours · do not rotate.

Variants: main horizontal lockup (posters, flyers, socials), stand-alone mark (merch),
cubed logo (documents, files).

### Brand voice, from brand guide Section 2

Persona: collaborative and approachable · open, team-driven, inclusive · encourages shared
thinking and open dialogue · organised and proactive · driven and accountable · confident but grounded.

Tone: confident · collaborative · structured · proactive · professional · clear, concise, intentional.

This is the raw material for the Values section, so it does not need inventing.

### Still outstanding

**Official logo files.** The supplied PNGs are blue/orange, so they now sit consistently with the
site palette. They are still low-resolution raster and do not match the guide's monochrome
variants. Official SVG / high-resolution assets are wanted before launch.

## Site structure

**Nav: Products · Work · Kickstarter · About · Contact**

| Page | Contents |
|---|---|
| Home | Hero · live products · deployments · values · team teaser · Kickstarter teaser · testimonials · CTA |
| Products | Index + detail per product. Live products lead; "Coming soon" products show limited info + image + optional launch date |
| Work | Case studies: PAXHI, SAHIK and others. The closer |
| Kickstarter | Tracks, Career Capital, mentors, alumni outcomes → hands off to platform |
| About | Story · values · how we work · **full team section** (`/about#team`) |
| Contact | Form · WhatsApp · real phone and email |

---

## Sprint 0: Foundation & tooling ✅

- [x] Decide final project location: moved out of OneDrive to `C:\dev\innova-website`
- [x] Enable `extension=intl` in `C:\tools\php85\php.ini`, required by Filament
- [x] Enable `extension=pdo_mysql` for production MySQL
- [x] Enable `extension=exif`, required by Spatie Media Library
- [x] `git init`, `.gitignore`, initial commit on `main`
- [x] Remote added and pushed to `github.com/Akumbom-Wesley/i-nnova`
- [x] Install Laravel **13.32.0** (not 12, since 13 is current stable)
- [x] Tailwind 4 + Vite 8 (ship with Laravel 13) + Alpine.js installed and wired
- [x] Install Filament **v5.8**, panel scaffolded at `/admin`
- [x] Admin user created
- [x] Spatie Media Library 11.23 + `media` table migrated
- [x] Spatie Translatable 6.14
- [x] spatie/laravel-sitemap 8.2
- [x] Design tokens in `resources/css/app.css` (`@theme`), fonts self-hosted via Bunny
- [x] Base layout: header, nav, mobile nav, footer, language switcher shell
- [x] Verified: `/` and `/admin/login` both return HTTP 200
- [x] Brand the Filament panel: logo, favicon, blue palette, Figtree, dark mode off
- [ ] Choose hosting target and PHP version parity

**Local dev credentials**: `admin@i-nnovacmr.com` / `innova-dev-2026`.
Dev only. Must be changed before anything is deployed.

## Sprint 1: Content model & admin

Everything below editable in Filament with zero code changes.

- [x] **Products**: name, slug, tagline, description, sector, features, screenshots, logo, order, featured, `status: live | coming_soon`, `launch_date` (nullable)
- [x] **Case Studies**: institution, logo, sector, challenge, solution, results, images, linked product, quote
- [x] **Team Members**: photo, name, role, credentials, bio, socials, department, order
- [x] **Values**: title, body, image
- [x] **Testimonials**: quote, person, role, organisation, **photo**, linked product
- [x] **Kickstarter**: mentors (photo, name, title, credentials), tracks, alumni outcomes, program stats
- [x] **Clients**: logos (real only)
- [x] **Site Settings**: hero copy, stats, contact details, socials, SEO defaults
- [x] **Leads**: contact submissions land in admin inbox
- [x] Translatable fields wired on every content model
- [x] Seeders with placeholder content so layouts can be built before real assets arrive

**Decisions taken while building this, and why:**

- **Sectors** are their own table rather than free text repeated on products and
  case studies, so "Education" cannot drift into "education" across two models
  and the Work index can filter on something stable.
- **Stats** is a single table serving both the site stat blocks and the
  Kickstarter programme stats, kept apart by a `context` column.
- **Translations** bind straight to Spatie's translation array through
  `App\Filament\Support\LocaleTabs`. The official Filament translatable plugin
  is abandoned, so nothing depends on it. Adding a third language means editing
  `config/site.php` and nothing else.
- **`User` now implements `FilamentUser`.** Without it Filament rejects every
  user as soon as `APP_ENV` stops being `local`, which would have locked
  everyone out of the deployed admin.
- **Placeholder images are generated, not skipped.** The seeder writes real PNGs
  through Media Library, so Sprint 2 builds against filled image slots and a
  working conversion pipeline rather than meeting both at Sprint 5.
- Placeholder seeding refuses to run in production, and seeded clients are
  unverified, so neither can reach a live page.
- `php artisan storage:link` is now part of `composer setup`; without it no
  uploaded image resolves.

## Sprint 2: Design system & homepage

- [x] Typography scale and spacing rhythm
- [x] Component library: buttons, cards, section headers, stat blocks, quote blocks
- [x] Hero
- [x] Live products section
- [x] Deployments / proof section
- [x] Values section
- [x] Team teaser row
- [x] Kickstarter teaser
- [x] Testimonials
- [x] Closing CTA
- [x] Responsive pass: phone first, real mobile-data budget

**Added a STEM band.** STEM is core positioning, so it gets its own full-width
section rather than a mention. The hero names it directly under the headline
and the band carries the four accelerator tracks.

**The motion system.** Nothing animates until the reader scrolls to it, via one
IntersectionObserver in `app.js` that sets `[data-revealed]`. That single hook
also starts the drawn rules and the STEM traces nested inside. Every animation
collapses to its finished state under `prefers-reduced-motion`, so the page is
complete without motion. The STEM motifs are drawn from the artwork's own
vocabulary: circuit traces that draw themselves, an orbiting atom, a turning
gear and rising flask bubbles.

**Corrected from the roll-up artwork.** PAXHI and SAHIK are client institutions,
not products, and are now case studies carrying their real crests. The product
line is the real one: EduTrust Schools, I-NNOVA Integrated Hospital Software,
Hotel Booking System and I-NNOVA POS. Accelerator tracks, the four value
pillars and the contact details are likewise the real ones.

**A Blade bug worth remembering.** `@section('name', $value)` calls `ob_start()`
when `$value` is null, treating it as a block section and leaking an output
buffer on every render. Site settings start empty, so the SEO description hit
this. Pages must pass a string, never a nullable attribute. There is a
regression test for it.

**Page weight:** 65 KB HTML, 94 KB images, 39 KB CSS, 53 KB JS. CSS and JS
gzip to roughly 8 KB and 19 KB. Institution crests always render through a
Media Library conversion; the SAHIK original is 1.4 MB and the thumb is 39 KB.

## Sprint 3: Inner pages

- [ ] Products index (live vs coming-soon treatment)
- [ ] Product detail
- [ ] Coming-soon detail state: limited info, image, optional launch date
- [ ] Work index
- [ ] Case study detail
- [ ] About: story, values, how we work
- [ ] Team section, full
- [ ] Kickstarter page + handoff to platform
- [ ] Contact: form, validation, WhatsApp, leads to admin
- [ ] 404 and error pages

## Sprint 4: Bilingual, SEO & performance

- [ ] EN/FR routing + language switcher
- [ ] Translate all static UI strings
- [ ] Meta tags, OG images **that actually resolve** (current site 404s)
- [ ] sitemap.xml, robots.txt
- [ ] schema.org: Organization, Product, Person
- [ ] Image optimization, lazy loading, responsive srcsets
- [ ] Lighthouse pass: target 90+ across the board
- [ ] Accessibility pass: contrast, focus states, keyboard nav, alt text

## Sprint 5: Content load & launch

- [ ] Load real images via CMS: team, products, institutions, internships
- [ ] Real case studies (PAXHI, SAHIK)
- [ ] Real testimonials with photos
- [ ] **Correct contact details**: no placeholder phone, email domain matching the site
- [ ] Privacy Policy and Terms (currently dead `#` links)
- [ ] Confirm no unverified partner logos anywhere
- [ ] Analytics
- [ ] Deploy, DNS, SSL
- [ ] 301 redirects from old URLs
- [ ] Post-launch smoke test

---

## Version control

- `main` is always deployable.
- One branch per sprint: `sprint-0/foundation`, `sprint-1/content-model`, …
- Conventional commits: `feat:`, `fix:`, `chore:`, `docs:`, `style:`, `refactor:`.
- Merge to `main` only when the sprint checklist is fully ticked.
- Tag releases: `v0.1.0` at the end of each sprint.

## Carried over from the old site: do not repeat

- Zero images across all six pages
- Placeholder phone `+237 670 000 000`
- Email domain mismatch (`innovacm.com` vs `i-nnovacmr.com`)
- Unverified partner logos
- Fabricated-looking testimonials
- Dead Privacy / Terms links
- Stock Material palette instead of brand colours
- Unlaunched products presented as shipped
