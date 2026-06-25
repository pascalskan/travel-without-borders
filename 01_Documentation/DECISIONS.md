# Decision Log

A record of significant decisions, the context behind them, and their
consequences. Newest first. Keep entries short and factual.

---

## 2026-06-25 — Preserve WPBakery and the Ave theme (no migration yet)

**Context:** The customisation audit found all 87 pages are built with WPBakery
using theme-bundled `ld_*` elements, with no reusable templates and heavy per-page
inline CSS. Migrating the builder or theme would require rebuilding every page.

**Decision:** Redevelopment proceeds as incremental enhancement on the existing
Ave child theme and WPBakery. No builder or theme migration unless separately
scoped with a full content-rebuild and data-export plan.

**Consequences:** Low risk to existing content; constrained to the current stack.
See [Customisation Audit](CUSTOMISATION_AUDIT.md).

---

## 2026-06-25 — Treat GTM in header.php as a tracked, migration-critical dependency

**Context:** Google Tag Manager (`GTM-TCZM2CR`, loading UA `UA-135755883-1` and
GA4 `G-484B6GT6CW`) is hardcoded in the child theme's `header.php`, not managed by
a plugin or theme option. A theme/template change would silently break tracking.

**Decision:** Bring the child theme under version control in `07_Source/Themes/`,
document the tracking IDs in [TECH_STACK.md](TECH_STACK.md), and explicitly
re-add GTM on any template change. Review retiring deprecated UA with the client.

**Consequences:** Tracking is no longer invisible/at-risk. Adds a child-theme
versioning task. See [Customisation Audit](CUSTOMISATION_AUDIT.md).

---

## 2026-06-25 — Export migration-critical data before any deactivation

**Context:** Quform holds 190 live enquiry entries; Yoast holds per-page SEO
metadata; Redirection manages 301s (DB pending upgrade); Slider Revolution has 12
modules with no active licence key.

**Decision:** Before deactivating or migrating any of these, export their data
and store it outside the live database.

**Consequences:** Protects live business data and SEO. See
[Customisation Audit](CUSTOMISATION_AUDIT.md).

---

## 2026-06-25 — Backup-first, child-theme-only workflow

**Context:** The site is live and built on the Ave theme with WPBakery. Direct
edits to the theme or plugins would be lost on update and risk the live site.

**Decision:** All custom work is delivered through an Ave child theme (or
`07_Source/`), and no change reaches production without a current, verified
backup.

**Consequences:** Updates remain safe; recovery is always possible. Adds a
child-theme setup step in Phase 2.

---

## 2026-06-25 — Target PHP 7.4

**Context:** The live host runs PHP 7.4.33, which is end-of-life.

**Decision:** All custom code must be PHP 7.4-compatible until the host is
upgraded.

**Consequences:** Newer PHP 8 syntax/features are off-limits for now. Revisit if
the host is upgraded.

---

## 2026-06-25 — Backups excluded from Git

**Context:** UpdraftPlus backup archives are large binary files.

**Decision:** Exclude `00_Backups/*` from Git (keeping only `.gitkeep`); retain
archives locally.

**Consequences:** Repository stays lightweight; backups must be managed and
stored outside version control. See [Backup Strategy](BACKUP_STRATEGY.md).
