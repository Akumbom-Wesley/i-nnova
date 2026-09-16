# I-NNOVA CM — Website Rebuild

Working plan and sprint checklist. Tick items as they land. One branch per sprint, merged to `main` when the sprint's checklist is complete.

---

## Locked decisions

| Decision | Choice |
|---|---|
| Stack | Laravel 13 + Blade + Tailwind 4 + Alpine.js |
| Admin / CMS | Filament v5 (self-hosted, no custom CMS build) |
| Rendering | Server-rendered HTML — no SPA |
| Direction | Institutional / editorial (veridyl-style restraint) |
| Languages | Bilingual EN / FR |
| Blog | **None** |
| Tagline | **"Make It Happen"** |
| Positioning | Institutional & education software, led by real deployments (PAXHI, SAHIK) |
| Kickstarter | Main site sells, hands off to `innovakickstarter.com` |
| Partner logos | **Removed** — not real relationships |

### Narrative spine

> We don't just build software — we build the builders.

Values and people carry equal weight with products. Three CMS-driven sections do that work: **Values**, **How We Work**, **Team**.

---

## Design system

| Token | Value | Use |
|---|---|---|
| `ink` | `#0A1A2F` | Dark ground sections |
| `bone` | `#F7F5F2` | Light ground (warm, never pure white) |
| `primary` | `#0B5FB0` | Logo blue — links, headings on light |
| `accent` | `#F26A1B` | Logo orange — CTAs and active states only, ≈5% of any screen |
| Display font | Instrument Serif / Fraunces | Headlines — this is what buys "classy" |
| Body font | Inter | Everything else |
| Motion | Restrained scroll reveals only | No parallax, no gradient meshes |

---

## Site structure

**Nav: Products · Work · Kickstarter · About · Contact**

| Page | Contents |
|---|---|
| Home | Hero · live products · deployments · values · team teaser · Kickstarter teaser · testimonials · CTA |
| Products | Index + detail per product. Live products lead; "Coming soon" products show limited info + image + optional launch date |
| Work | Case studies — PAXHI, SAHIK and others. The closer |
| Kickstarter | Tracks, Career Capital, mentors, alumni outcomes → hands off to platform |
| About | Story · values · how we work · **full team section** (`/about#team`) |
| Contact | Form · WhatsApp · real phone and email |

---

## Sprint 0 — Foundation & tooling ✅

- [x] Decide final project location — moved out of OneDrive to `C:\dev\innova-website`
- [x] Enable `extension=intl` in `C:\tools\php85\php.ini` — required by Filament
- [x] Enable `extension=pdo_mysql` for production MySQL
- [x] Enable `extension=exif` — required by Spatie Media Library
- [x] `git init`, `.gitignore`, initial commit on `main`
- [x] Remote added and pushed — `github.com/Akumbom-Wesley/i-nnova`
- [x] Install Laravel **13.32.0** (not 12 — 13 is current stable)
- [x] Tailwind 4 + Vite 8 (ship with Laravel 13) + Alpine.js installed and wired
- [x] Install Filament **v5.8** — panel scaffolded at `/admin`
- [x] Admin user created
- [x] Spatie Media Library 11.23 + `media` table migrated
- [x] Spatie Translatable 6.14
- [x] spatie/laravel-sitemap 8.2
- [x] Design tokens in `resources/css/app.css` (`@theme`), fonts self-hosted via Bunny
- [x] Base layout — header, nav, mobile nav, footer, language switcher shell
- [x] Verified: `/` and `/admin/login` both return HTTP 200
- [ ] Brand the Filament panel (logo, colours)
- [ ] Choose hosting target and PHP version parity

**Local dev credentials** — `admin@i-nnovacmr.com` / `innova-dev-2026`.
Dev only. Must be changed before anything is deployed.

## Sprint 1 — Content model & admin

Everything below editable in Filament with zero code changes.

- [ ] **Products** — name, slug, tagline, description, sector, features, screenshots, logo, order, featured, `status: live | coming_soon`, `launch_date` (nullable)
- [ ] **Case Studies** — institution, logo, sector, challenge, solution, results, images, linked product, quote
- [ ] **Team Members** — photo, name, role, credentials, bio, socials, department, order
- [ ] **Values** — title, body, image
- [ ] **Testimonials** — quote, person, role, organisation, **photo**, linked product
- [ ] **Kickstarter** — mentors (photo, name, title, credentials), tracks, alumni outcomes, program stats
- [ ] **Clients** — logos (real only)
- [ ] **Site Settings** — hero copy, stats, contact details, socials, SEO defaults
- [ ] **Leads** — contact submissions land in admin inbox
- [ ] Translatable fields wired on every content model
- [ ] Seeders with placeholder content so layouts can be built before real assets arrive

## Sprint 2 — Design system & homepage

- [ ] Typography scale and spacing rhythm
- [ ] Component library: buttons, cards, section headers, stat blocks, quote blocks
- [ ] Hero
- [ ] Live products section
- [ ] Deployments / proof section
- [ ] Values section
- [ ] Team teaser row
- [ ] Kickstarter teaser
- [ ] Testimonials
- [ ] Closing CTA
- [ ] Responsive pass — phone first, real mobile-data budget

## Sprint 3 — Inner pages

- [ ] Products index (live vs coming-soon treatment)
- [ ] Product detail
- [ ] Coming-soon detail state — limited info, image, optional launch date
- [ ] Work index
- [ ] Case study detail
- [ ] About — story, values, how we work
- [ ] Team section, full
- [ ] Kickstarter page + handoff to platform
- [ ] Contact — form, validation, WhatsApp, leads to admin
- [ ] 404 and error pages

## Sprint 4 — Bilingual, SEO & performance

- [ ] EN/FR routing + language switcher
- [ ] Translate all static UI strings
- [ ] Meta tags, OG images **that actually resolve** (current site 404s)
- [ ] sitemap.xml, robots.txt
- [ ] schema.org — Organization, Product, Person
- [ ] Image optimization, lazy loading, responsive srcsets
- [ ] Lighthouse pass — target 90+ across the board
- [ ] Accessibility pass — contrast, focus states, keyboard nav, alt text

## Sprint 5 — Content load & launch

- [ ] Load real images via CMS — team, products, institutions, internships
- [ ] Real case studies (PAXHI, SAHIK)
- [ ] Real testimonials with photos
- [ ] **Correct contact details** — no placeholder phone, email domain matching the site
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

## Carried over from the old site — do not repeat

- Zero images across all six pages
- Placeholder phone `+237 670 000 000`
- Email domain mismatch (`innovacm.com` vs `i-nnovacmr.com`)
- Unverified partner logos
- Fabricated-looking testimonials
- Dead Privacy / Terms links
- Stock Material palette instead of brand colours
- Unlaunched products presented as shipped
