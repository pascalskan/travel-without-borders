# Development Log

A running, session-by-session record of work. Newest entries first. For the
formal version history see the [Changelog](../CHANGELOG.md); for current
progress see the [Project Status](../PROJECT_STATUS.md).

---

## 2026-07-12 (later) — Dedicated Testimonials page (Milestone 4)

Built on a new branch `feature/testimonials-page` (off `main`, which now contains
the merged homepage work).

**Completed**

- **Grid layout for `[twb_testimonials]`.** Added a `layout` param
  (`carousel` | `grid`) to the existing element — same file, same card renderer,
  same CSS (no duplication). Grid is a static responsive grid (3 → 2 → 1) that
  loads **no Flickity/JS**. Grid CSS added to `testimonials.css`.
- **New `[twb_page_hero]` element** (`inc/page-hero.php`, `assets/css/page-hero.css`):
  a reusable inner-page hero — green text panel (H1 + intro) beside a cover image,
  stacks on mobile; eager LCP image; token-based; no JS. Registered via the loader.
  See [Page Hero](PAGE_HERO.md).
- **Shared utilities** added to `twb-tokens.css`: `.twb-bg-green` (green band),
  `.twb-prose` (centred copy), `.twb-btn` (gold pill CTA button) — used by the
  page's intro and CTA rows.
- **Assembled `/testimonials/`** (local DB): page hero → intro → featured carousel
  → full 9-card grid → green CTA band (button → `/contact/`). Nine placeholder
  testimonials, each with a German destination photo linking to its destination
  page.
- **Verified** with Playwright at 1440 / 768 / 390: single H1; featured carousel
  (Flickity, keyboard) and grid (3/2/1) render; grid images lazy-load; CTA →
  `/contact/`; **homepage testimonials unchanged** (carousel, 4 cards) and its CTA
  now navigates to `/testimonials/`; no horizontal scroll; 0 console errors; no PHP
  warnings; PHP 7.4 lint clean.

**Not yet done**

- Real testimonial content + photos (deferred, as agreed).
- Optional: add the Testimonials page to the primary navigation (local menu).

---

## 2026-07-12

**Completed — Testimonials system (Milestones 1–3)**

- **M1 — Infrastructure.** Added `inc/loader.php` (explicit, ordered component
  loader per [ADR-001](Architecture/ADR-001-Testimonials-Architecture.md) D9);
  routed `functions.php` through it; registered the shared `twb-tokens` style
  handle. Hero carousel untouched.
- **M2 — Reusable component.** Built the `[twb_testimonials]` WPBakery carousel
  (`inc/testimonials.php`, `assets/css/testimonials.css`, `assets/js/testimonials.js`)
  on Ave's bundled Flickity, using the shared token layer. Decision: content is
  stored as WPBakery element params (not a CPT); CPT recorded as a future
  enhancement (ADR-001 superseded for the current implementation). See
  [Testimonials Component](TESTIMONIALS.md).
- **M3 — Homepage integration + polish.** Placed the band between the
  introduction and Special Interest Holidays (local DB; original homepage
  `post_content` backed up to post meta `_twb_homepage_pre_testimonials_backup`).
  Wired the CTA to `/testimonials/`. Polish: AA contrast fixes (eyebrow + trip
  badge → olive-dark), carousel `aria-label`, removed the on-click focus
  outline, single centred card with the arrows in the gutter (no half-card
  "peek"). Added an **optional destination image column that links to the
  mentioned place** (lazy-loaded; stacks on mobile with the arrows over the
  photo).
- **Verified** with Playwright at 1440 / 768 / 390: hero unchanged, homepage
  layout intact, no horizontal scroll, 0 console errors, no PHP warnings from
  `twb_*`, keyboard navigation, responsive behaviour.

**Not yet done**

- Real testimonial content + photos (to be added alongside the dedicated page).
- The dedicated `/testimonials/` page (the CTA target; 404 until it exists).
- Commit of the M3 working-tree changes (`inc/testimonials.php`,
  `assets/css/testimonials.css`, docs).

**Next**

- Build the dedicated Testimonials page, then populate real content.

---

## 2026-06-26

**Completed**

- Established the source-controlled local dev workflow (see
  [Local Development Workflow](LOCAL_DEV_WORKFLOW.md)):
  - Made `07_Source/Themes/ave-child/` the single source of truth.
  - Synced the hero-carousel code (`inc/`, `assets/`) that had been developed
    inside LocalWP into the repo (SHA-256 verified identical).
  - Replaced LocalWP's `themes/ave-child` with a directory **junction** to the
    repo copy (no admin / Developer Mode required; chosen over symlink).
  - Added `scripts/link-child-theme.ps1` (idempotent junction recovery).
  - Backed up the pre-junction live theme to `…\_theme-backups\`.
  - Verified: site serves 200 through the junction; Git detects code changes;
    repo→LocalWP round-trip works; nothing outside the child theme is tracked.

**Next**

- Resume feature development with all changes now version-controlled.

---

## 2026-06-25

**Completed**

- Discovery phase
- Mobile audit
- Slideshow audit
- Homepage baseline screenshots
- Full UpdraftPlus backup created and verified
- GitHub repository created
- Repository housekeeping and documentation (Phase 1)

**Next**

- Set up local development environment
- Bring existing Ave Child theme under version control
- Begin slideshow / homepage work
