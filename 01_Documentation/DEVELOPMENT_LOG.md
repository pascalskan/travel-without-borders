# Development Log

A running, session-by-session record of work. Newest entries first. For the
formal version history see the [Changelog](../CHANGELOG.md); for current
progress see the [Project Status](../PROJECT_STATUS.md).

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
