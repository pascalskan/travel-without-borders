# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).
Because this project tracks a website rather than a versioned software release,
entries are grouped by project phase and dated.

## [Unreleased]

### Added

- **Dedicated Testimonials page (`/testimonials/`).** Assembled from reusable
  components (no bespoke CSS/JS/PHP): a new **`[twb_page_hero]`** inner-page hero
  (panel + image; see [Page Hero](01_Documentation/PAGE_HERO.md)); the
  **testimonials element extended with a `layout` param** (carousel | grid — grid
  loads no JavaScript); and shared `twb-tokens` utilities (`.twb-bg-green`,
  `.twb-prose`, `.twb-btn`). The homepage keeps a curated carousel; the page shows
  the full collection as a grid and ends with an enquiry CTA. See
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
