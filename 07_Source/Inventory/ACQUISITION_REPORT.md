# Acquisition Report — Phase 2

Result of the automated search of the fully extracted UpdraftPlus backup and the
import of every project-owned asset that could be acquired safely.

**Backup searched:** `00_Backups/2026-06-25_Pre-Development/`
**Contents:** `themes/`, `Plugins/`, `uploads/`, `Others/`, and the SQL dump
`*-db.gz` (table prefix `Gv9IkG4cZ_`).
**Date:** 2026-06-25

---

## 1. Everything found (inventory)

### Themes (`themes/`)
- `ave` (parent, vendor), **`ave-child` (project-owned)**, plus default
  `twentynineteen…twentytwentyfive` (WordPress defaults).

### Plugins (`Plugins/` — ~22 plugin dirs)
- Active/known: akismet, ave-core, ave-portfolio, contact-form-7,
  disable-xml-rpc, js_composer (WPBakery), post-smtp, quform, really-simple-ssl,
  redirection, redux-framework, revslider, vc_clipboard, wordfence,
  wordpress-seo (Yoast), wp-rocket, wp-smushit, duplicate-post,
  advanced-nocaptcha-recaptcha (CAPTCHA 4WP).
- Also present but **not** on the active-18 list: **breeze** (cache),
  **flamingo** (CF7 store), **updraftplus**. Explains the 18-vs-19 ambiguity:
  more plugins are *installed* than *active*.
- No custom plugins. No plugin-bundled export files (`.json/.xml/.csv/.zip`) of
  user configuration were found inside plugin folders — their config lives in
  the database.

### Uploads (`uploads/`) — file-type tally
| Type | Count | Type | Count |
| ---- | ----- | ---- | ----- |
| jpg | 3,078 | mp4 | 15 |
| css (generated `liquid-styles`) | 113 | gif | 15 |
| jpeg | 94 | svg | 12 |
| png | 93 | webp | 8 |
| php (redux field templates) | 28 | json/xml/pdf/txt | few |

- **Branding:** `logo-black/white-{160,280,337}px.png`, `logo@1x.png`,
  `travel-aware-logo.jpg`, partner `logos.jpg`.
- **Custom UI icons:** 10 SVGs in `2018/06/` (backpack, bus, mountain, phone,
  sofa, star-rating, treasure-map, etc.).
- **Vendor demo:** `ave-logo-*.svg` (Ave theme demo) — skipped.
- **Generated CSS:** `uploads/liquid-styles/` — Ave's compiled per-page CSS
  (incl. `liquid-css-global.css` carrying the custom rules).
- **No custom fonts** (Muli/Poppins via Google Fonts).
- ~3,000 destination photos = site content media.

### SQL (`*-db.gz`)
- Custom data tables present: `quform_*`, `redirection_*`, `revslider_*`,
  `yoast_*`, plus `options` (theme options `ave`, Customizer `custom_css`) and
  `posts` (WPBakery content).

### Other (`Others/`)
- Cache/runtime config: `wp-rocket-config/`, `breeze-config/`, `advanced-cache.php`,
  Wordfence `wflogs/` — environment config, not project source.

---

## 2. Everything imported (acquired automatically)

| Asset | From | To |
| ----- | ---- | -- |
| Ave Child theme (4 files) | `themes/ave-child/` | `07_Source/Themes/ave-child/` |
| GTM tracking snippet | `header.php` | `07_Source/Tracking/gtm-container.html` |
| Theme Options custom CSS | `liquid-css-global.css` | `07_Source/CSS/theme-options-custom.css` |
| Customizer Additional CSS | SQL `custom_css` | `07_Source/CSS/customizer-additional.css` |
| Redirect rule (1) | SQL `redirection_items` | `07_Source/Exports/Redirects/redirects-from-backup.csv` |
| Brand logos (7) | `uploads/2020/01`, `2018/06`, `2018/08` | `02_Assets/Logos/` |
| Custom UI icons (10 SVG) | `uploads/2018/06` | `02_Assets/Icons/` |

Originals only — WordPress-generated thumbnail sizes were not duplicated.

---

## 3. Everything documented

- [Child Theme Review](../Themes/CHILD_THEME_REVIEW.md) — every file, the single
  `header.php` override, the GTM customisation, the dormant unhooked
  `liquid_parent_theme_scripts()` enqueue, and deployment implications.
- [Asset Register](ASSET_REGISTER.md) — updated with real acquisition status.
- [PROJECT_STATUS](../../PROJECT_STATUS.md) — Phase 2.2 marked complete.

---

## 4. Everything intentionally skipped

| Skipped | Why |
| ------- | --- |
| Parent `ave` theme + WP default themes | Vendor/core; restore from source or backup |
| All plugins | Commercial/bundled; no custom plugins |
| `ave-logo-*.svg` | Ave theme **demo** assets, not TWB brand |
| ~3,000 destination photos | Site content media; inventoried, not duplicated |
| `liquid-styles/` compiled CSS (113 files) | Machine-generated; only the custom portion was extracted |
| Cache/Wordfence/runtime configs | Environment config, not project source |
| WP thumbnail logo sizes | Regenerable from originals |

---

## 5. Key discrepancies found (backup vs. Customisation Audit)

> These are the most important findings — the backup does **not** contain some
> data the audit reported from the live admin.

| Item | Audit (live) | This backup | Implication |
| ---- | ------------ | ----------- | ----------- |
| Quform entries | 190 | **1** (dated 2020-07-03) | The 190 live entries are **not in this backup** |
| Slider Revolution | 12 modules | **1** (`homepage`) | 11 modules are **not in this backup** |
| Redirection rules | "manages 301s" | **1** rule | Full redirect set **not in this backup** |

Likely cause: UpdraftPlus Free may have been configured to exclude large/log
tables, or these accumulated on live after this backup. Either way they cannot be
acquired from the backup.

---

## 6. Everything missing / requiring manual intervention

These exist only in the **live database** and cannot be safely or completely
extracted from this backup:

1. **Quform entries (≈190)** — only 1 in backup; contains personal data, so must
   not be committed to Git regardless. Export from live Quform to a secure store.
2. **Slider Revolution modules (≈12)** — only 1 in backup. Export via the plugin
   (note: no active licence key may limit export).
3. **Full Redirection rule set** — only 1 in backup. Export from live Redirection.
4. **Yoast SEO configuration/metadata** — present in SQL but best exported via
   Yoast's import/export tool for a portable, re-importable file.
5. **WPBakery page content (87 pages)** — export via WordPress WXR, not raw SQL.
6. **Stray `.iconbox-heading-xs h3` CSS rule** — not located anywhere in the
   backup; origin remains unverified.

Theme Options (`ave`) and Quform form *definitions* exist in the SQL as
serialized blobs; the useful human-readable parts (custom CSS) were extracted,
and the rest is re-importable from the backup if needed — no manual action
required for those.
