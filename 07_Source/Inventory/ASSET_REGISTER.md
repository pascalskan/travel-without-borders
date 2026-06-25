# Asset Register

Master inventory of the project's **custom assets** — what exists, where it lives
on the live site, where it belongs in this repository, and whether it is yet
version-controlled or exported.

**Source:** [Customisation Audit](../../01_Documentation/CUSTOMISATION_AUDIT.md)
**Last updated:** 2026-06-25
**Status legend:** ✅ done · ⏳ pending · ➖ not applicable

> "Version Controlled" and "Exported" describe the **current** state. Most assets
> are pending because Phase 2 brings them into the repository; this register is
> the checklist for that work.

## Custom code

| Asset | Current Location | Repository Location | Version Controlled | Exported | Notes |
| ----- | ---------------- | ------------------- | ------------------ | -------- | ----- |
| Ave Child theme (`functions.php`, `header.php`, `style.css`) | Live site `wp-content/themes/ave-child/` | `07_Source/Themes/` | ⏳ | ➖ | Only substantive code customisation; `header.php` carries GTM |
| GTM tracking snippet (`GTM-TCZM2CR`) | Hardcoded in child `header.php` | `07_Source/Tracking/` | ⏳ | ➖ | Not plugin-managed; must be re-applied on any template change |
| Theme Options custom CSS (~16 lines) | Ave Theme Options (DB) | `07_Source/CSS/` | ⏳ | ⏳ | Targets `.main-nav`, `.fancy-box-tour`; consolidate into a tracked stylesheet |
| Customizer Additional CSS (5 lines) | WordPress Customizer (DB) | `07_Source/CSS/` | ⏳ | ⏳ | `.hideMob` responsive utility |
| Stray `.iconbox-heading-xs h3` rule | Origin unclear (inline) | `07_Source/CSS/` | ⏳ | ➖ | Identify origin before tracking — *unable to verify with current access* |
| Custom JavaScript | None found | `07_Source/JavaScript/` | ➖ | ➖ | No custom JS exists; clean area for new work |
| Custom plugins | None | `07_Source/Plugins/` | ➖ | ➖ | All active plugins are commercial/theme-bundled |

## Exported data (business-critical)

| Asset | Current Location | Repository Location | Version Controlled | Exported | Notes |
| ----- | ---------------- | ------------------- | ------------------ | -------- | ----- |
| Quform forms + 190 entries | Quform (DB) | `07_Source/Exports/Quform/` | ➖ (data) | ⏳ | Live enquiry/booking data — export before any change; contains personal data |
| Slider Revolution — 12 modules | Slider Revolution (DB) | `07_Source/Exports/Slider-Revolution/` | ➖ (data) | ⏳ | No active licence key may limit export |
| Yoast SEO per-page metadata | Yoast (DB) | `07_Source/Exports/Yoast/` | ➖ (data) | ⏳ | Use Yoast import/export to preserve rankings |
| Redirection rules | Redirection (DB) | `07_Source/Exports/Redirects/` | ➖ (data) | ⏳ | DB pending upgrade; re-import before go-live |

## Database-held configuration (not file-trackable)

| Asset | Current Location | Repository Location | Version Controlled | Exported | Notes |
| ----- | ---------------- | ------------------- | ------------------ | -------- | ----- |
| Ave Theme Options (Redux) | Live site (DB) | ➖ | ➖ | ➖ | Captured by backup; backup is the system of record |
| WPBakery page content (87 pages) | Live site (DB) | ➖ | ➖ | ➖ | Inline `vc_custom_*` styling lost on builder migration |
| WPBakery inline/element CSS | Per-page (DB) | ➖ | ➖ | ➖ | Machine-generated; rebuilt per page, not trackable |
| Menus / Widgets / Sidebars | Live site (DB) | ➖ | ➖ | ➖ | Captured by backup |

## Reference assets

| Asset | Current Location | Repository Location | Version Controlled | Exported | Notes |
| ----- | ---------------- | ------------------- | ------------------ | -------- | ----- |
| Plugin inventory (18 listed; count to re-verify) | TECH_STACK / live admin | `01_Documentation/TECH_STACK.md` | ✅ | ➖ | Notes referenced "19 active"; re-count |
| Baseline screenshots | Captured | `04_Testing/Baseline/` | ✅ | ➖ | Homepage baseline (7 images) |
| Full UpdraftPlus backup | Local | `00_Backups/2026-06-25_Pre-Development/` | ➖ (ignored) | ✅ | Permanent recovery point; excluded from Git by design |
| Brand/media assets (logos, icons, slideshow) | Live site uploads | `02_Assets/` | ⏳ | ➖ | Default/sticky/mobile logos uploaded in Theme Options |
