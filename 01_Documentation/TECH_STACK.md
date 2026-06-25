# Technology Stack

Platform and dependency inventory for the Travel Without Borders website, as
recorded directly from the live site.

**Captured:** 25 June 2026
**Homepage:** <https://travelwithoutborders.co.uk>

## Platform

| Component     | Detail                                   |
| ------------- | ---------------------------------------- |
| CMS           | WordPress 7.0                            |
| Theme         | Ave (parent) + **Ave Child** v1.0 by Liquid Themes |
| Theme add-ons | Ave Core v2.9.2, Ave Portfolio v1.0      |
| Page builder  | WPBakery / Visual Composer (all 87 pages) |
| PHP           | 7.4.33                                   |

## Theme

- **Active theme:** Ave Child v1.0 (child of Ave). Used correctly; parent not
  edited directly.
- **Custom code:** Child `functions.php` is a standard stylesheet enqueue only.
  Child `header.php` is a modified copy of the parent header with Google Tag
  Manager hardcoded in. See [Customisation Audit](CUSTOMISATION_AUDIT.md).
- **Configuration:** Typography (Body: Muli, Headings: Muli Bold, single post
  title: Poppins), Wide layout at 1170px, plus extensive Ave Theme Options
  (Redux Framework) — held in the database, not version-controlled.

## Tracking & Analytics

| Service | ID | Loaded via |
| ------- | -- | ---------- |
| Google Tag Manager | `GTM-TCZM2CR` | Hardcoded in child `header.php` |
| Universal Analytics (deprecated) | `UA-135755883-1` | GTM |
| Google Analytics 4 | `G-484B6GT6CW` | GTM |

> Tracking is **not** managed via a plugin or the Ave `google_analytics` /
> `space_head` / `space_body` fields (all empty). It must be migrated manually if
> the theme/template changes, or tracking will break. See
> [Customisation Audit](CUSTOMISATION_AUDIT.md).

## Active Plugins (18 listed)

> **Note:** the 18 below are the **active** plugins. The Phase 2 backup showed
> **22 plugin directories installed**, including ones inactive at audit time
> (Breeze, Flamingo, UpdraftPlus) — which explains the earlier "18 vs 19"
> ambiguity (more installed than active). All are commercial or theme-bundled —
> there are **no custom plugins**. See the
> [Acquisition Report](../07_Source/Inventory/ACQUISITION_REPORT.md).

| Plugin | Version |
| ------ | ------- |
| Akismet Anti-spam | 5.7 |
| Ave Core | 2.9.2 |
| Ave Portfolio | 1.0 |
| CAPTCHA 4WP | 7.6.0 |
| Contact Form 7 | 6.1.6 |
| Disable XML-RPC | 1.0.1 |
| Post SMTP | 3.9.4 |
| Quform | 2.9.3 |
| Really Simple Security | 9.6.0 |
| Redirection | 5.8.0 |
| Slider Revolution | 6.1.5 |
| Smush | 4.1.2 |
| Visual Composer Clipboard | 4.1.1 |
| Wordfence Security | 8.1.4 |
| WP Rocket | 3.18.2 |
| WPBakery Page Builder | 8.7.2 |
| Yoast Duplicate Post | 4.7 |
| Yoast SEO | 27.9 |

## Notes

- PHP 7.4.33 is end-of-life; custom code must remain compatible with it until
  the host is upgraded. See [DECISIONS.md](DECISIONS.md).
- Caching (WP Rocket) and image optimisation (Smush) are active — account for
  cache clearing during testing and deployment.
- Several plugins are structurally load-bearing (Ave Core, Ave Portfolio, Slider
  Revolution, Quform, WPBakery, Post SMTP, Redirection, Yoast SEO). See the
  [Customisation Audit](CUSTOMISATION_AUDIT.md) for coupling and migration risk.
- Quform holds **190 live enquiry/booking entries** — preserve before any
  migration or deactivation.
- This inventory should be re-verified before each major phase.
