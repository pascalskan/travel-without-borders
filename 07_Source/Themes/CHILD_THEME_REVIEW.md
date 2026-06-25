# Ave Child Theme — Review

Engineering review of the **Ave Child** theme extracted from the 2026-06-25
backup and imported to [`ave-child/`](ave-child/). This is the only
project-owned theme code on the site.

**Parent theme:** `ave` (Liquid Themes) — vendor product, not committed.
**Child theme version:** 1.0

## Files

| File | Size | Purpose | Customised? |
| ---- | ---- | ------- | ----------- |
| `style.css` | 415 B | Theme header only (Theme Name, `Template: ave`, version). **Contains no CSS rules.** | No — boilerplate |
| `functions.php` | 333 B | Enqueues stylesheets | Minor (see below) |
| `header.php` | 1.5 KB | Template override of the parent header | **Yes — GTM injected** |
| `screenshot.jpg` | 10 KB | Theme thumbnail | No |

## Template overrides

- **`header.php`** is the only template override. It is a copy of the parent
  Ave header with the **Google Tag Manager** container (`GTM-TCZM2CR`) added in
  two places: the `<script>` immediately after `<head>`, and the
  `<noscript><iframe>` immediately after `<body>`. Everything else mirrors the
  parent header (`liquid_helper()`, `liquid_action()` hooks, `wp_head()`).

No other parent templates are overridden in the child theme.

## Customisations

1. **GTM tracking in `header.php`** — the single substantive customisation.
   Extracted to [`../Tracking/gtm-container.html`](../Tracking/gtm-container.html).
2. **`functions.php` enqueues** — note a latent issue: it defines
   `liquid_parent_theme_scripts()` (enqueues the parent style) but **never hooks
   it**; only `liquid_child_theme_style()` is hooked (on `wp_enqueue_scripts`,
   priority 99). In practice the parent theme enqueues its own styles, so this is
   dormant dead code rather than a live bug — but it should be cleaned up or
   correctly hooked when the theme is next touched.
3. **No custom CSS/JS in the theme** — `style.css` is empty of rules. The site's
   hand-written CSS lives in Theme Options and the Customizer (now extracted to
   [`../CSS/`](../CSS/)), not in the child theme.

## Deployment implications

- **GTM is invisible outside this file.** Any new theme, or regeneration of
  `header.php`, drops all analytics with no error. Re-apply
  [`../Tracking/gtm-container.html`](../Tracking/gtm-container.html) on any
  template change, or migrate GTM to a plugin/theme option.
- **`style.css` carrying no rules is expected** — do not assume custom styling is
  missing; it is database-held (Theme Options / Customizer).
- **Child theme is safe to version and extend.** It is small, standard, and
  correctly declared (`Template: ave`). New custom CSS/JS should be added here as
  properly enqueued files (see
  [Coding Standards](../../01_Documentation/CODING_STANDARDS.md)).
- **Fix the unhooked enqueue** opportunistically; verify parent styles still load
  after any change.

> Full site-wide context is in the
> [Customisation Audit](../../01_Documentation/CUSTOMISATION_AUDIT.md).
