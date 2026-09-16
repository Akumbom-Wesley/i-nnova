# I-NNOVA CM — Website Rebuild

Working plan and sprint checklist. Tick items as they land. One branch per sprint, merged to `main` when the sprint's checklist is complete.

---

## Locked decisions

| Decision | Choice |
|---|---|
| Stack | Laravel 12 + Blade + Tailwind + Alpine.js |
| Admin / CMS | Filament v4 (self-hosted, no custom CMS build) |
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

## Sprint 0 — Foundation & tooling

- [ ] Decide final project location (**move out of OneDrive** — `vendor/` and `node_modules/` cause sync conflicts and file locks)
- [ ] Enable `extension=intl` in `C:\tools\php85\php.ini` (line 927) — required by Filament
- [ ] Enable `extension=pdo_mysql` (line 934) if using MySQL; otherwise SQLite for dev
- [ ] `git init`, `.gitignore`, initial commit on `main`
- [ ] Install Laravel 12
- [ ] Install Tailwind + Alpine.js, wire Vite
- [ ] Install Filament v4, create admin user, brand the panel
- [ ] Install Spatie Media Library (image conversions, WebP, responsive srcsets)
- [ ] Install Spatie Translatable (EN/FR per-field)
- [ ] Install spatie/laravel-sitemap
- [ ] Encode design tokens in `tailwind.config`, load fonts
- [ ] Base layout: header, nav, footer, language switcher shell
- [ ] Choose hosting target and PHP version parity

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
