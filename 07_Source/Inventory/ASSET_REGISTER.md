# Asset Register

Master inventory of the project's **custom assets** — what exists, where it came
from, whether it is now version-controlled, and its acquisition status.

**Updated:** 2026-06-25 (after Phase 2 automated acquisition from the extracted backup)
**Backup searched:** `00_Backups/2026-06-25_Pre-Development/`
**Status legend:** ✅ acquired · 🟡 partial · ⏳ pending · ⛔ blocked (manual/live-only)

## Columns

`Asset` · `Location` (in repo, or live origin) · `Version Controlled` ·
`Source` (where it was acquired from) · `Status` · `Notes`

## Custom code

| Asset | Location | Version Controlled | Source | Status | Notes |
| ----- | -------- | ------------------ | ------ | ------ | ----- |
| Ave Child theme | `07_Source/Themes/ave-child/` | ✅ | Backup `themes/ave-child/` | ✅ | 4 files; reviewed in [CHILD_THEME_REVIEW.md](../Themes/CHILD_THEME_REVIEW.md) |
| GTM tracking snippet | `07_Source/Tracking/gtm-container.html` | ✅ | Backup `header.php` | ✅ | `GTM-TCZM2CR`; loads UA + GA4 |
| Theme Options custom CSS | `07_Source/CSS/theme-options-custom.css` | ✅ | Compiled `liquid-css-global.css` | ✅ | `.main-nav`, `.fancy-box-tour`; DB is authoritative |
| Customizer Additional CSS | `07_Source/CSS/customizer-additional.css` | ✅ | SQL (`custom_css` / `ave`) | ✅ | `.hideMob` utility |
| Stray `.iconbox-heading-xs` rule | — | ❌ | Not located in backup | ⛔ | Origin still unverified; likely DB/page-level |
| Custom JavaScript | `07_Source/JavaScript/` | ➖ | — | ➖ | None exists (confirmed) |
| Custom plugins | `07_Source/Plugins/` | ➖ | — | ➖ | None exists (confirmed) |

## Exported / database data

| Asset | Location | Version Controlled | Source | Status | Notes |
| ----- | -------- | ------------------ | ------ | ------ | ----- |
| Quform form definitions | Live DB / SQL `quform_forms` | ❌ | SQL (base64-serialized) | 🟡 | 2 forms present; serialized blob — re-export via Quform for a clean import |
| Quform entries | Live DB | ❌ | SQL `quform_entries` | ⛔ | **Backup holds only 1 entry (2020), not the audited 190.** Personal data — must not be committed. Re-export from live |
| Redirection rules | `07_Source/Exports/Redirects/redirects-from-backup.csv` | ✅ | SQL `redirection_items` | 🟡 | Only **1 rule** in backup; re-export live to confirm full set |
| Slider Revolution modules | Live DB / SQL `revslider_sliders` | ❌ | SQL | ⛔ | Backup holds **1 slider (`homepage`), not the audited 12.** Export via plugin (no licence key may limit) |
| Yoast SEO config/metadata | Live DB / SQL `yoast_*` | ❌ | SQL | ⏳ | Present in SQL; export via Yoast import/export tool for portability |
| Theme Options (full) | Live DB / SQL option `ave` | 🟡 | SQL (serialized) | 🟡 | Serialized in backup; custom CSS portion extracted. Re-apply via Redux import |
| WPBakery page content | Live DB / SQL `posts` | ❌ | SQL | ⏳ | 87 pages; export via WordPress WXR, not raw SQL |

## Brand & design assets

| Asset | Location | Version Controlled | Source | Status | Notes |
| ----- | -------- | ------------------ | ------ | ------ | ----- |
| TWB logos (black/white, 160/280/337px) | `02_Assets/Logos/` | ✅ | `uploads/2020/01/` | ✅ | Originals only; WP thumbnails skipped |
| Legacy logo (`logo@1x.png`) | `02_Assets/Logos/` | ✅ | `uploads/2018/06/` | ✅ | Older brand mark |
| Travel Aware logo | `02_Assets/Logos/` | ✅ | `uploads/2018/08/` | ✅ | Partner/trust logo |
| Custom UI icons (10 SVG) | `02_Assets/Icons/` | ✅ | `uploads/2018/06/` | ✅ | backpack, bus, mountain, phone, sofa, etc. |
| Ave demo logos (SVG) | — | ➖ | `uploads/2018/11/` | ➖ | Vendor theme demo assets — intentionally skipped |
| Custom fonts | — | ➖ | — | ➖ | None; Muli/Poppins load via Google Fonts |
| Destination photography (~3,000 jpg) | Live `uploads/` | ➖ | Backup uploads | ➖ | Site content media — inventoried, not duplicated |

## Reference assets

| Asset | Location | Version Controlled | Source | Status | Notes |
| ----- | -------- | ------------------ | ------ | ------ | ----- |
| Plugin inventory | `01_Documentation/TECH_STACK.md` | ✅ | Backup `Plugins/` (22 dirs) | ✅ | 22 installed (incl. inactive); 18 active per audit |
| Baseline screenshots | `04_Testing/Baseline/` | ✅ | Phase 0 capture | ✅ | Homepage (7 images) |
| Full UpdraftPlus backup | `00_Backups/2026-06-25_Pre-Development/` | ➖ (ignored) | UpdraftPlus | ✅ | Permanent recovery point; excluded from Git |
| Cache configs (WP Rocket, Breeze) | Live `Others/` | ➖ | Backup | ➖ | Environment config; not project source |
