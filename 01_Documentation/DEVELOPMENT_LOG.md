# Development Log

A running, session-by-session record of work. Newest entries first. For the
formal version history see the [Changelog](../CHANGELOG.md); for current
progress see the [Project Status](../PROJECT_STATUS.md).

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
