# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).
Because this project tracks a website rather than a versioned software release,
entries are grouped by project phase and dated.

## [Unreleased]

### Deployed to production — 2026-08-13 and after

The rebuild went live on **2026-08-13**. Work done *directly on production*
after that date is listed in
[Live Site Changes](05_Deployment/LIVE_SITE_CHANGES.md), not here — including
the V7/V8 copy and image changes, the CW Sports Travel partner link, and the
hero band link colours. That file also records what must be reconciled before
the next deployment.

**Everything below this line is local and undeployed.**

### Added

- **Blog single-post redesign.** Share row with X (inline SVG — Font Awesome 4.7
  has no X glyph) and Instagram; comments, author byline and "Published in"
  removed; a 2× timeline image size registered past the parent theme's size
  stripping. `inc/blog.php`, `assets/css/blog.css`, and child overrides of
  `templates/blog/single/default.php` and `part-meta.php`.

- **Blog index and navigation fixes.** Dates removed from the index cards
  (`templates/blog/tmpl-timeline.php`); previous/next links re-wired to follow
  reading order down the index rather than chronology
  (`templates/blog/single/navigation.php`).

- **Content layout helpers** (`assets/css/content-layout.css`): uniform carousel
  slide heights, `.twb-wrap-right` text wrap, an opt-in `.twb-vmiddle` fix for
  WPBakery's `content_placement="middle"` (which does not work in this theme),
  and a subordinate-heading style.

- **Hero band link colours** (`assets/css/hero-colours.css`) — the repository
  copy of the change currently live as Customizer CSS. See Live Site Changes.

- **Partner link click tracking** (`inc/outbound-tracking.php`,
  `assets/js/outbound-tracking.js`) — present but **inert**; no container reads
  the dataLayer yet. See
  [Partner Link Tracking](01_Documentation/PARTNER_LINK_TRACKING.md).

### Changed (local, undeployed)

- **All eight blog posts** rewritten to the client's new copy; images unchanged.
- **Five Special Interest pages** rewritten and re-laid out — Colditz, Eagle's
  Nest, Royal Heritage, Fairy Tale Castles, Motorcar Enthusiasts. Grey/white
  banding restarted from the first content row on each; Eagle's Nest converted
  from tinted column cards to full-width bands.
- **Augsburg page** rebuilt with new copy, replacement photography, the English
  sights map beside "What to do", and the partnership image resized and moved.


- **Dedicated Testimonials page (`/testimonials/`).** Assembled from reusable
  components (no bespoke CSS/JS/PHP) and aligned to the site's own page grammar:
  a **`[twb_page_hero]`** split section (photo + charcoal panel + yellow underline
  link — see [Page Hero](01_Documentation/PAGE_HERO.md)), reused as both the H1
  hero and the H2 closing CTA band; a **`[twb_trust_stats]`** credibility strip; a
  **`[twb_email_strip]`** standard green contact band; and the **testimonials
  element extended with a `layout` param** (carousel | grid — grid loads no
  JavaScript). Page order: split hero → trust stats → featured carousel → full
  grid → closing CTA → email strip. The homepage keeps a curated carousel; the
  page shows the full collection. See
  [Testimonials Component](01_Documentation/TESTIMONIALS.md).

- **Testimonials system (reusable WPBakery component).** Introduced a
  `[twb_testimonials]` carousel element in the Ave child theme, following the
  hero-carousel pattern (Ave's bundled Flickity reused — no new library;
  on-demand assets; `filemtime` versioning; shared design tokens). Delivered in
  three milestones:
  - **M1 — Infrastructure:** a deterministic component loader
    (`inc/loader.php`, explicit ordered `require_once` per ADR-001 D9) and a
    shared design-token layer (`assets/css/twb-tokens.css`); `functions.php`
    kept thin.
  - **M2 — Component:** client-editable quotes with author, location,
    trip-type/region badges, optional star rating, an optional **destination
    image that links to the place it mentions**, and a centred CTA. Built to the
    hero's accessibility bar (keyboard, focus rings, reduced-motion, WCAG-AA
    colours, semantic `figure`/`blockquote`/`cite`) and performance bar (no CLS,
    lazy images). Content is stored as WPBakery element params; the Custom Post
    Type is recorded as a **future enhancement** (ADR-001 superseded for the
    current implementation).
  - **M3 — Homepage integration:** placed as a full-bleed band between the
    introduction and Special Interest Holidays, with the CTA pointing at the
    future `/testimonials/` page.
  See [Testimonials Component](01_Documentation/TESTIMONIALS.md).

### Changed

- **Established the source-controlled local development workflow.** The Ave
  child theme is now version-controlled in the repo at
  `07_Source/Themes/ave-child/`, and LocalWP consumes it through a Windows
  directory junction. Synced the previously out-of-Git development code
  (hero carousel: `inc/`, `assets/`) into the repo, added
  `scripts/link-child-theme.ps1` to (re)create the junction, and documented the
  workflow in `01_Documentation/LOCAL_DEV_WORKFLOW.md`. The live site, database,
  plugins, and parent theme were not touched.

### Planned

- Set up local development environment.
- Create Ave child theme for all custom code.
- Homepage redesign.
- Testimonials redesign.
- Performance and mobile optimisation.

## [Phase 0] — 2026-06-25

Discovery and recovery. Established a safe, documented foundation before any
development work.

### Added

- Created the GitHub repository and numbered folder structure.
- Added project documentation (README, project overview, tech stack, roadmap,
  workflow, coding standards, backup strategy, decisions, development log).
- Captured baseline homepage screenshots under `04_Testing/Baseline/`.
- Created a full UpdraftPlus backup (database, plugins, themes, uploads, others)
  and verified it.
- Added restore notes alongside the backup
  (`00_Backups/2026-06-25_Pre-Development/RestoreNotes.md`).
- Configured `.gitignore`, excluding backup archives from version control.

### Completed

- Website audit.
- Plugin audit (~18–19 active plugins inventoried; exact count to re-verify).
- Mobile audit.
- Theme investigation (Ave Child v1.0, Ave Core v2.9.2, Ave Portfolio v1.0).
- Customisation audit (theme, plugins, WPBakery, CSS/JS/PHP, assets, tracking).

[Unreleased]: #unreleased
[Phase 0]: #phase-0--2026-06-25
