# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).
Because this project tracks a website rather than a versioned software release,
entries are grouped by project phase and dated.

## [Unreleased]

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
