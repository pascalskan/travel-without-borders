# Customisation Audit

A consolidated analysis of how the Travel Without Borders website is customised,
based on factual inspection of the live WordPress Admin. The purpose is to
identify what is custom, what is fragile, and where redevelopment can proceed
safely.

**Source:** Live WordPress Admin inspection notes
**Date of inspection:** 25 June 2026
**Status:** Pre-development baseline — no development work has started.

> This document records findings only. It does not authorise or perform any
> change. See [Workflow](WORKFLOW.md) and [Coding Standards](CODING_STANDARDS.md)
> before acting on anything here.

---

## Executive Summary

The site is a mature, content-heavy WordPress build (87 published pages) running
on the **Ave** theme via a thin **Ave Child** theme, with **WPBakery** as the
page builder on every page. Customisation is concentrated in three places:
theme-options configuration, WPBakery page content, and a small amount of
hardcoded code in the child theme.

Key characteristics:

- **Low custom-code surface, high configuration surface.** Almost no bespoke PHP
  or JavaScript exists, but the site depends heavily on theme-bundled WPBakery
  elements (`ld_*`), Slider Revolution modules, and per-page WPBakery inline CSS.
- **One material code customisation:** Google Tag Manager is hardcoded into the
  child theme's `header.php`, not managed by a plugin or theme option.
- **Tight plugin coupling.** Several plugins (Ave Core, Ave Portfolio, Slider
  Revolution, Quform, WPBakery) are structurally embedded; removing any would
  break content or functionality.
- **Migration risk is high for visual content.** WPBakery inline styling and
  Slider Revolution modules will not survive a page-builder or theme migration
  without manual rebuilding.
- **Live data exists.** Quform holds 190 real enquiry/booking entries that must
  be preserved.

The safest path is incremental enhancement via the existing child theme, not a
platform or builder migration, unless a migration is explicitly scoped with a
content-rebuild plan.

---

## Theme Architecture

| Item | Finding |
| ---- | ------- |
| Active theme | Ave Child |
| Child theme version | 1.0 |
| Parent theme | Ave (by Liquid Themes) |
| Child theme correctly used? | Yes |
| Parent theme edited directly? | No |
| `functions.php` | Standard child-theme stylesheet enqueue only — no custom logic |
| `header.php` | Modified copy of the parent header with GTM injected — the only substantive template customisation |
| Other template overrides | Unable to verify with current level of access |

**Assessment:** The child theme is used correctly and is very thin. The only
meaningful template-level customisation is the GTM injection in `header.php`.

---

## Plugin Architecture

All active plugins are commercially published or theme-bundled products. **No
custom plugins exist.**

### Heavily configured

| Plugin | Configuration |
| ------ | ------------- |
| Slider Revolution | 12 slider modules built and in active use |
| WPBakery Page Builder | Used on all 87 pages with extensive `ld_` element integration |
| Quform | 2 forms; 190 stored entries; actively receiving submissions |
| Yoast SEO | Sitemaps, Open Graph, schema, per-page SEO meta |
| WP Rocket | Caching and file optimisation with exclusion rules |
| Ave Theme Options (Redux Framework) | Typography, logo, header, footer, colours, CSS |

### Tightly integrated (removal would break the site)

| Plugin | Why it is critical |
| ------ | ------------------ |
| Ave Core | Provides all `ld_` WPBakery elements used on every page |
| Ave Portfolio | Powers the portfolio post type |
| Slider Revolution | 12 embedded modules; content lost if removed |
| Quform | Primary enquiry/booking form with 190 live entries |
| Post SMTP | Handles all outbound email (forms, WP notifications) |
| Redirection | Manages site 301 redirects (DB pending upgrade) |
| WPBakery Page Builder | Underpins all page content |
| Yoast SEO | Per-page SEO titles, meta, schema |

**Assessment:** The plugin stack is deeply load-bearing. Several plugins are not
"features" but structural dependencies of the content itself.

---

## WPBakery Analysis

| Item | Finding |
| ---- | ------- |
| Pages using WPBakery | All 87 published pages |
| Reusable templates | None present |
| Custom elements | Yes — theme-bundled `ld_*` elements (e.g. `ld_blog`, `ld_button`, `ld_carousel`, `ld_content_box`, `ld_fancy_heading`, `ld_icon_box`, `ld_images_group_container`, `ld_spacer`, `ld_newsletter`, `ld_subscribe_form`) |
| Element origin | Registered by Ave/Liquid theme (Ave Core), **not** user-created via Shortcode Mapper |
| Custom JS field | Empty |
| Visual Composer Clipboard in use? | Unknown — clipboard is stored server-side as user meta and was not accessible via available endpoints |

**Migration implication:** Because every page is built with WPBakery and depends
on theme-bundled `ld_*` elements, moving to another builder or theme means
**rebuilding all 87 pages** and replacing every `ld_*` element with an
equivalent. There are no reusable templates to ease this.

---

## Ave Theme Analysis

The Ave theme is configured extensively through its options (Redux Framework) and
the Customizer.

**Configured Theme Options sections:** General (Wide layout, 1170px width,
primary/secondary colours), Logo (default/sticky/mobile), Header, Title Bar
(padding, colour scheme, background image, parallax, overlay, breadcrumbs),
Footer, Sidebars (pages/blog/portfolio), Typography (Body: Muli, Headings: Muli
Bold, single post title: Poppins), Blog, Portfolio, Extras (back-to-top, lazy
load, CSS compiler, DB cache toggles), CSS Code (~16 lines), Tracking Code.

**Customizer sections present:** Layout, Logo, Header, Page Title Bar, Footer,
Sidebars, Typography, Blog, Portfolio, Extras, Menus, Widgets, Additional CSS.

| Item | Finding |
| ---- | ------- |
| Typography | Muli (body), Muli Bold (headings), Poppins (single post title) |
| Layout | Wide, 1170px site width |
| Theme Options custom CSS | ~16 lines targeting `.main-nav` and `.fancy-box-tour` |
| `google_analytics` option field | Empty (tracking handled in `header.php` instead) |
| `space_head` / `space_body` injection fields | Empty |

**Assessment:** A large share of the site's appearance lives in theme options,
not in code. This configuration is **not version-controlled** and would not
transfer automatically to a new theme.

---

## CSS Audit

| Source | Volume | Nature | Survives migration? |
| ------ | ------ | ------ | ------------------- |
| WPBakery per-page inline `vc_custom_TIMESTAMP{}` blocks | ~29 on a sample page, ~35 on homepage | Auto-generated by WPBakery spacing/colour controls | ❌ No — rebuilt per page |
| Inline `style=""` on elements | 113 elements on homepage | Generated by WPBakery element settings | ❌ No |
| Ave Theme Options CSS field | ~16 lines | Hand-written, targets `.main-nav`, `.fancy-box-tour` | ⚠️ Manual re-entry |
| Customizer Additional CSS | 5 lines | `.hideMob` responsive utility | ⚠️ Manual re-entry |
| Stray inline block | 1 rule | `.iconbox-heading-xs h3 { font-weight: 500; }` — origin unclear | ⚠️ Unable to verify origin |

Customizer Additional CSS (verbatim):

```css
.hideMob {display: none !important;}

@media (max-width: 1100px) {
    .hideMob {display: block !important;}
}
```

**Assessment:** There is **no centralised custom stylesheet.** CSS is scattered
across WPBakery output, theme options, and the Customizer. The bulk (WPBakery
inline) is machine-generated and migration-fragile; the hand-written portion is
small but lives outside version control.

---

## JavaScript Audit

| Source | Finding |
| ------ | ------- |
| Custom JS files in child theme | None |
| WPBakery Custom JS field | Empty |
| Inline custom JS | None found |
| Tracking JS | Loaded via GTM (see Asset Audit) |

**Assessment:** There is effectively **no custom JavaScript** beyond what plugins
and the theme load. This is a low-risk area.

---

## PHP Audit

| File | Finding |
| ---- | ------- |
| `functions.php` (child) | Standard child-theme stylesheet enqueue only — no custom logic |
| `header.php` (child) | Modified parent header with GTM `<script>` (head) and `<noscript><iframe>` (body) injected |
| Other custom PHP | None found |
| Custom plugins | None |

**Assessment:** The custom PHP footprint is essentially a single file
(`header.php`). This is good for maintainability but means the **GTM dependency
is invisible** unless someone knows to look in the template.

---

## Asset Audit

| Asset / integration | Detail | Managed by |
| ------------------- | ------ | ---------- |
| Google Tag Manager | `GTM-TCZM2CR` | Hardcoded in child `header.php` |
| Universal Analytics | `UA-135755883-1` (named `gtm4`) | Loaded via GTM |
| Google Analytics 4 | `G-484B6GT6CW` | Loaded via GTM |
| `analytics.js` | Loads directly alongside GTM | Unable to verify why both load |
| Google reCAPTCHA | Via CAPTCHA 4WP | Plugin |
| Akismet | Spam filtering | Plugin |
| Logos | Default, sticky, mobile uploaded | Theme Options |
| Slider Revolution media | 12 modules | Plugin / DB |

**Assessment:** All analytics flow through a **hardcoded** GTM container. Both UA
(deprecated) and GA4 run simultaneously. The theme's own analytics/header-injection
fields are empty, so the only tracking mechanism is the template file.

---

## Technical Debt

| # | Item | Impact |
| - | ---- | ------ |
| 1 | GTM hardcoded in `header.php` instead of a plugin/option | Tracking silently breaks on theme change |
| 2 | Dual UA + GA4 tracking; UA is deprecated | Redundant/legacy tracking |
| 3 | `analytics.js` loading alongside GTM | Possible duplicate/legacy tag |
| 4 | CSS scattered across 3+ locations, none version-controlled | Hard to maintain; migration-fragile |
| 5 | 87 pages of WPBakery inline styling, no central stylesheet | Massive rebuild cost on migration |
| 6 | Theme configuration not version-controlled | Cannot reproduce look from the repo alone |
| 7 | Slider Revolution has no active licence key | Export/update may be limited |
| 8 | Redirection database pending upgrade | Operational risk; redirects fragile |
| 9 | Stray `.iconbox-heading-xs` inline rule of unknown origin | Untracked styling |
| 10 | PHP 7.4.33 is end-of-life | Security/compatibility ceiling |

---

## Risks

| Risk | Likelihood | Severity | Notes |
| ---- | ---------- | -------- | ----- |
| Tracking breaks on theme/template change | High | High | GTM lives only in `header.php` |
| Visual content lost in builder/theme migration | High | High | WPBakery inline CSS + `ld_*` elements + sliders |
| Loss of 190 Quform enquiry entries | Medium | High | Live business data; must be exported first |
| Redirects break (Redirection removed/not migrated) | Medium | High | SEO and link integrity impact |
| SEO metadata loss (Yoast not migrated) | Medium | High | Per-page meta and schema |
| Email delivery breaks (Post SMTP not reconfigured) | Medium | High | Form submissions fail silently |
| Slider Revolution export limited by missing licence | Medium | Medium | 12 modules at risk |
| Visual Composer Clipboard contents unknown | Low | Low | Verify manually before deactivation |

---

## Safe Development Areas

These areas can be worked on with low risk, provided the standard
[Workflow](WORKFLOW.md) (backup → test → deploy) is followed:

- **Child-theme CSS** added as a properly enqueued stylesheet (consolidating the
  scattered custom CSS into version control).
- **Child-theme `functions.php`** additions using correct hooks and the `twb_`
  prefix (see [Coding Standards](CODING_STANDARDS.md)).
- **New custom JavaScript** enqueued via the child theme (the current JS surface
  is empty, so additions are isolated).
- **Migrating GTM** out of `header.php` into a documented, version-controlled
  mechanism (template partial or tag-manager plugin) — to be scoped carefully.
- **Per-page WPBakery content edits** on individual pages, tested against
  baseline screenshots.

**Avoid without an explicit migration plan:** replacing WPBakery, replacing the
Ave theme, removing Ave Core/Portfolio, or removing any tightly integrated
plugin.

---

## Version Control Recommendations

- Bring the **child theme** (`functions.php`, `header.php`, stylesheets) under
  version control in `03_Development/Child Theme/` so the one real code
  customisation (GTM) is tracked.
- Consolidate hand-written CSS (Theme Options ~16 lines, Customizer 5 lines, the
  stray `.iconbox-heading-xs` rule) into a **single tracked child-theme
  stylesheet**, rather than admin-only fields.
- Record GTM/analytics IDs in documentation (see [TECH_STACK.md](TECH_STACK.md))
  so tracking can be reinstated deliberately after any template change.
- Export and store, outside the live DB, the **migration-critical data**: Quform
  entries, Yoast settings, Redirection rules, and Slider Revolution modules.
- Continue excluding backups (`00_Backups/*`) from Git (see
  [Backup Strategy](BACKUP_STRATEGY.md)).

> Theme-options/Customizer configuration and WPBakery page content live in the
> database and **cannot** be version-controlled as files. Treat verified backups
> as the system of record for those.

---

## Priority Actions

| Priority | Action | Reason |
| -------- | ------ | ------ |
| P1 | Export Quform entries (190), Yoast settings, Redirection rules, Slider Revolution modules | Protect live data before any work |
| P1 | Document GTM/UA/GA4 IDs and the `header.php` injection | Prevent silent tracking loss |
| P1 | Place the child theme under version control in `03_Development/` | Track the only real code customisation |
| P2 | Consolidate hand-written CSS into one tracked child-theme stylesheet | Maintainability |
| P2 | Resolve Redirection "database pending upgrade" | Operational stability |
| P2 | Review dual UA + GA4 with the client; plan UA retirement | Remove deprecated tracking |
| P3 | Identify origin of the stray `.iconbox-heading-xs` rule | Remove untracked styling unknowns |
| P3 | Manually verify Visual Composer Clipboard contents before any deactivation | Close an open unknown |

---

## Unknowns / Unable to Verify

The following could not be confirmed with the current level of access:

- **Visual Composer Clipboard contents** — stored server-side as user meta; not
  accessible via available endpoints. *Unable to verify with current level of
  access.*
- **Why `analytics.js` loads alongside GTM** — *Unable to verify with current
  level of access.*
- **Origin of the `.iconbox-heading-xs h3` inline rule** — *Unable to verify with
  current level of access.*
- **Template overrides in the child theme beyond `header.php`** — *Unable to
  verify with current level of access.*
- **Exact active plugin count** — inspection notes reference "19 active plugins"
  while [TECH_STACK.md](TECH_STACK.md) lists 18. The plugin list should be
  re-counted. *Unable to verify with current level of access.*

---

*Related: [Project Overview](PROJECT_OVERVIEW.md) ·
[Tech Stack](TECH_STACK.md) · [Decisions](DECISIONS.md) ·
[Roadmap](ROADMAP.md) · [Project Status](../PROJECT_STATUS.md)*
