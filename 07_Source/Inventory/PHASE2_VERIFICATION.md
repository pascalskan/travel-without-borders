# Phase 2 Verification

Final verification of Phase 2 (repository preparation + automated asset
acquisition). Every conclusion below was checked against the source files and the
extracted backup, not assumed.

**Date:** 2026-06-25
**Scope:** repository structure, documentation consistency, child theme, imported
CSS, Asset Register, and acquisition conclusions.

---

## Verified assets

| Asset | Check performed | Result |
| ----- | --------------- | ------ |
| Ave Child theme (4 files) | Byte-for-byte `diff` vs backup `themes/ave-child/` | ✅ All match |
| `header.php` | Confirmed only override; GTM present (head + noscript) | ✅ |
| `functions.php` | Reviewed; dormant unhooked `liquid_parent_theme_scripts()` noted | ✅ (documented) |
| `style.css` | Confirmed theme header only, no rules | ✅ (expected) |
| `screenshot.jpg` | Present (Step 3 said `.png`; `.jpg` is the actual/standard file) | ✅ |
| Theme Options custom CSS | Selectors `.main-nav`, `.fancy-box-tour` (3 rules) preserved | ✅ Complete |
| Customizer CSS | `.hideMob` utility with `!important` preserved | ✅ Complete |
| GTM snippet | `GTM-TCZM2CR` matches `header.php` | ✅ |
| Brand logos (7) + UI icons (10 SVG) | Present in `02_Assets/`; originals only | ✅ No duplication |
| Backup isolation | Nothing under `00_Backups/` is staged | ✅ Still ignored |

## Verified documentation

| Check | Result |
| ----- | ------ |
| Relative links across all Markdown | ✅ 0 broken |
| Numbered-folder convention (`00–07`) | ✅ Consistent; `07_Source` follows scheme |
| Asset Register — each asset appears once | ✅ No duplicates |
| Asset Register — plugin dir count | ✅ Corrected 25 → **22** |
| Cross-ref: audit ↔ acquisition ↔ status ↔ tech stack ↔ decisions | ✅ Reconciled |
| Plugin count "18 vs 19" | ✅ Explained: 22 installed (incl. inactive), 18 active |
| Audit unknowns (template overrides, plugin count) | ✅ Resolved/clarified in Phase 2 |

## Repository structure decision (Step 6)

No `04_Source/` ever existed — the `04_` slot is `04_Testing/`. `07_Source/` was
introduced deliberately (Phase 2.1) to avoid a collision and **already follows
the existing numbered convention**. Renaming would reintroduce the collision and
break working links. **Left unchanged — stability over churn.** The only
artefact of retiring `03_Development/` is a vacant `03` slot (cosmetic).

## Remaining manual work

See [the manual task list](#manual-task-list) below. All six items exist only in
the live database / live admin and cannot be extracted from the backup.

## Risks

| Risk | Severity | Mitigation |
| ---- | -------- | ---------- |
| Backup is **incomplete** (1 Quform entry, 1 slider, 1 redirect vs audited 190/12/many) | High | Re-export from live before any migration; do not trust backup for this data |
| GTM lives only in `header.php` | High | Snippet now version-controlled in `07_Source/Tracking/`; re-apply on theme change |
| Quform entries contain personal data | High | Keep out of Git; export to a secure store only |
| Slider Revolution export limited by missing licence | Medium | Verify export works before relying on it |
| Vacant `03` folder slot | Low | Cosmetic only; accepted |

## Unknowns

Still unverifiable from the backup (carried forward from the audit):

- Origin of the stray `.iconbox-heading-xs h3` CSS rule — not found anywhere in
  the backup.
- Why `analytics.js` loads alongside GTM.
- Visual Composer Clipboard contents (server-side user meta).
- Exact **live** counts for Quform entries / Slider Revolution modules (backup
  cannot confirm the audited 190 / 12).

## Repository readiness

**Phase 2 is verified and complete for everything automatable.**

- Structure, documentation, and naming are consistent (0 broken links).
- All project-owned code, CSS, tracking, and branding that exists in the backup
  is imported, reviewed, and registered.
- Cross-document facts agree.
- The only outstanding work is live-only data that genuinely cannot be automated.

**Readiness: ready to proceed**, pending the six manual exports below (none block
repository integrity; they block a future content migration).

---

## Manual task list

The shortest possible list — only work that **cannot** be automated from the
backup. All require interactive access to the live WordPress admin.

| # | Task | Why not automatable | Est. time | Impact if skipped |
| - | ---- | ------------------- | --------- | ----------------- |
| 1 | Export **Quform entries** from live | Only 1 of ~190 is in the backup; contains personal data that must not be committed to Git | 15 min | Loss of live enquiry/booking data on migration |
| 2 | Export **Slider Revolution modules** from live (plugin export) | Only 1 of ~12 in backup; needs the plugin UI; licence key may be required | 20 min | 11 sliders lost; homepage/visual content broken on rebuild |
| 3 | Export **full Redirection rule set** from live | Only 1 rule in backup; live set unknown | 10 min | Broken links / lost SEO on go-live |
| 4 | Export **Yoast SEO config** via Yoast import/export | Lives in DB; portable file needs the plugin tool | 10 min | SEO titles/meta/schema lost; ranking risk |
| 5 | Export **WPBakery page content** via WordPress WXR | 87 pages in DB; WXR needs the admin exporter | 10 min | No portable copy of page content |
| 6 | Identify origin of stray **`.iconbox-heading-xs h3`** rule | Not present in backup; requires live inspection | 10 min | Minor untracked styling unknown |

**Total: ~75 minutes of live-admin work.** Tasks 1–3 are the priority (live
business data and links).
