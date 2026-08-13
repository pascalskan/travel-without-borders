# Project Status Dashboard

A living overview of progress on the Travel Without Borders redevelopment.

**Last updated:** 2026-07-12
**Current phase:** Phase 4 — Testimonials (in progress)
**Development started:** Yes

---

## At a Glance

| Phase | Description | Status |
| ----- | ----------- | ------ |
| 0 | Discovery & Recovery | ✅ Complete |
| 1 | Repository Housekeeping | 🟡 In progress |
| 2 | Local Environment & Child Theme | 🟡 In progress |
| 3 | Homepage Redesign | 🟡 In progress |
| 4 | Testimonials Redesign | 🟡 In progress |
| 5 | Optimisation (performance, mobile, a11y) | ⬜ Not started |
| 6 | Deployment | ⬜ Not started |

Legend: ✅ complete · 🟡 in progress · ⬜ not started

---

## Phase 0 — Discovery & Recovery ✅

### Discovery

- [x] Website audit
- [x] Plugin audit (~18–19 active — count to re-verify)
- [x] Mobile audit
- [x] Theme investigation
- [x] Customisation audit (theme, plugins, WPBakery, CSS/JS/PHP, tracking)

### Recovery

- [x] GitHub repository created
- [x] Baseline screenshots captured
- [x] Full UpdraftPlus backup created
- [x] Backup verified
- [x] Backup excluded from Git

---

## Phase 1 — Repository Housekeeping 🟡

- [x] Standardise folder naming
- [x] Rewrite README, CHANGELOG, PROJECT_STATUS
- [x] Add core documentation (tech stack, workflow, standards, backup, roadmap, decisions)
- [x] Add customisation audit
- [x] Create documentation index
- [ ] Final review and commit

---

## Phase 2 — Local Environment & Child Theme 🟡

### 2.1 Repository preparation for production assets ✅

- [x] Create the `07_Source/` source structure (Themes, Plugins, CSS, JavaScript, Tracking, Exports, Inventory)
- [x] Add a README to every source folder (what belongs / what does not)
- [x] Create the [Asset Register](07_Source/Inventory/ASSET_REGISTER.md) from the customisation audit
- [x] Retire `03_Development/`; make `07_Source/` the single home for custom code
- [x] Repoint all documentation references to `07_Source/`

### 2.2 Automated asset acquisition from backup ✅

- [x] Search the entire extracted backup (themes, plugins, uploads, SQL, configs)
- [x] Import the Ave Child theme to `07_Source/Themes/ave-child/` and review it
- [x] Extract custom CSS (Theme Options + Customizer) to `07_Source/CSS/`
- [x] Extract the GTM snippet to `07_Source/Tracking/`
- [x] Extract the redirect rule(s) found in the SQL to `07_Source/Exports/Redirects/`
- [x] Import brand logos and custom SVG icons to `02_Assets/`
- [x] Inventory uploads/media and database data (see [Acquisition Report](07_Source/Inventory/ACQUISITION_REPORT.md))
- [x] Update the [Asset Register](07_Source/Inventory/ASSET_REGISTER.md)

### 2.3 Local environment & live-only data ⬜

- [ ] Set up local WordPress environment
- [ ] Mirror plugins and theme locally
- [ ] **Re-export from live** (backup is incomplete): Quform entries, Slider Revolution modules, full Redirection set, Yoast config
- [ ] Verify local matches production

---

## Phase 3 — Homepage Redesign ⬜

- [ ] Define design goals
- [ ] Implement via child theme / WPBakery
- [ ] Compare against baseline screenshots
- [ ] Client review

---

## Phase 4 — Testimonials Redesign 🟡

Built as a reusable WPBakery component (see
[Testimonials Component](01_Documentation/TESTIMONIALS.md)). Content is stored as
element params; the CPT is a documented future enhancement
([ADR-001](01_Documentation/Architecture/ADR-001-Testimonials-Architecture.md)).

- [x] M1 — Loader infrastructure (`inc/loader.php`) + shared token layer
- [x] M2 — Reusable `[twb_testimonials]` carousel component (a11y + perf to hero standard)
- [x] M3 — Homepage integration (full-bleed band + CTA → `/testimonials/`), polish, optional linked destination images
- [x] M4 — Dedicated `/testimonials/` page: `[twb_page_hero]` + grid layout for `[twb_testimonials]` + shared utilities; homepage CTA now resolves
- [ ] Real testimonial content + photos
- [ ] Client review

> Verified locally (Playwright, 1440/768/390): homepage testimonials unchanged,
> dedicated page responsive, no horizontal scroll, 0 console errors, no PHP
> warnings. Homepage work merged to `main` (PR #1); the dedicated page is on
> `feature/testimonials-page`.

---

## Phase 5 — Optimisation ⬜

- [ ] Performance (caching, assets, images)
- [ ] Mobile responsiveness
- [ ] Accessibility
- [ ] Re-test against baselines

---

## Phase 6 — Deployment ⬜

- [ ] Pre-deployment backup
- [ ] Follow deployment checklist
- [ ] Post-deployment verification
- [ ] Update release notes

---

See the [Roadmap](01_Documentation/ROADMAP.md) for phase detail and the
[Changelog](CHANGELOG.md) for completed history.
