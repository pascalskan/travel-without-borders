# Decision Log

A record of significant decisions, the context behind them, and their
consequences. Newest first. Keep entries short and factual.

---

## 2026-07-15 — Build the B2B "Trade Enquiry" form as a labelled Quform duplicate

**Context:** The Business-to-Business page needs its own enquiry form
(Business name, Email, Phone, Trade Enquiry) that reaches the same business inbox
as the current contact form but is never confused with individual enquiries. The
existing contact form is a Quform form whose definition and notification settings
live in the WordPress database, not in this repo. Post SMTP already delivers mail.

**Decision:** Do not build a second, parallel email path in the theme. Instead
duplicate the existing Quform contact form in WP-admin, swap in the business
fields, and label submissions three ways: a distinct form name, a
`[TRADE ENQUIRY]` notification-subject prefix, and a fixed `Enquiry Type: Trade`
value in the entry/email body. The reproducible procedure is version-controlled
in [Trade / Business Enquiry Form](TRADE_ENQUIRY_FORM.md).

**Consequences:** Reuses the working, verified email pipeline (no new sender to
authenticate in Post SMTP); the original form and its 190 entries are untouched;
trade entries file separately. The front-end Individual/Business slider toggle
remains a separate task. Config lives in the DB, so the doc — not code — is the
source of truth for the form.

---

## 2026-07-12 — Accept the desktop menu overflow at ~1200–1520px (fix in the optimisation milestone)

**Context:** Adding **Testimonials** to the header menu (after Tailor-made
Holidays, before Groups) made the site's pre-existing desktop menu overflow
worse: at ~1200–1520px viewports the last items (Groups, Blog) are clipped off
the right edge. Two fixes were attempted and measured:

1. *Tighten nav spacing (child CSS):* fits the 9 menu links, but the header's
   fixed logo + centred-menu + Contact-pill column structure still pushes the
   Contact pill off-screen — the row's minimum width exceeds ~1550px.
2. *Lower the hamburger breakpoint:* Ave stores it as `media-mobile-nav`
   (theme option, regenerates responsive CSS), but the option is **capped at
   1199** (`theme/theme-options/liquid-responsive.php`, `'max' => 1199`) and the
   mobile header is rebuilt by JS hard-wired to that breakpoint. Setting 1500
   left the 1200–1500px range with **no functioning header**; reverted.

**Decision:** Leave the overflow as-is for now. It is a parent-theme
(Ave) header-layout defect that predates the Testimonials work; Groups and Blog
remain reachable via the footer and other pages. Fix it properly in the
optimisation milestone (Phase 5), where header/menu work is already scoped by
the Design System Audit (§9/§12).

**Consequences:** At ~1200–1520px the header hides Groups/Blog; ≥ ~1520px and
≤ 1100px (hamburger) are unaffected. Do **not** re-attempt the breakpoint
override without also addressing the theme's JS breakpoint coupling.

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

**Consequences:** Protects live business data and SEO. Phase 2 confirmed this is
essential: the 2026-06-25 backup holds only **1** Quform entry and **1** slider
(not 190/12), so these must be exported from the **live** site, not recovered
from the backup. See [Customisation Audit](CUSTOMISATION_AUDIT.md) and the
[Acquisition Report](../07_Source/Inventory/ACQUISITION_REPORT.md).

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
