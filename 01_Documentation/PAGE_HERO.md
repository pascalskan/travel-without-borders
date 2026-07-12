# Page Hero / Split Band (`twb_page_hero`)

The site's signature **split section**: a photo on one side and a dark charcoal
panel (heading + intro + optional yellow underline link) on the other. This is
the established TWB inner-page pattern — every destination/region page opens with
it, and pages like Groups reuse it as a closing CTA band ("Our professionalism is
our main asset"). It is **not** the homepage hero carousel.

- **Status:** built; used for the Testimonials page hero **and** its closing CTA.
- **Type:** WPBakery (`vc_map`) element; token-based; **no JavaScript**.
- **Source:** child theme `07_Source/Themes/ave-child/`.

## Files
| File | Role |
| ---- | ---- |
| `inc/page-hero.php` | Registers `[twb_page_hero]` + render; registers/enqueues its stylesheet on render |
| `assets/css/page-hero.css` | All styling; references the shared token layer (incl. `--twb-charcoal`) |

Loaded via `inc/loader.php`. `functions.php` is not touched.

## Params (client / no code)
- **Eyebrow** — small yellow label above the heading (optional).
- **Heading** + **Heading level** — `H1` (page hero, once per page) or `H2` (CTA band).
- **Introduction** — short paragraph (optional).
- **Image** + **Image side** — Left (default) or Right on desktop.
- **Link text** + **Link URL** — optional **yellow underline link** (the site's CTA
  idiom, e.g. "Read the reviews", "Start your enquiry"); falls back to `#`.

## Behaviour
- Two columns (image ~55% / charcoal panel ~45%) on desktop/tablet.
- **Panel is first in the DOM** so the H1 leads (SEO/screen-readers) and text shows
  first when **stacked on mobile** (≤767px: panel on top, full-width image below);
  flex `order` places the image left/right on desktop.
- Charcoal `--twb-charcoal (#242424)` panel, white heading, muted-white intro,
  `--twb-yellow` eyebrow + underline link (all AA on charcoal).
- Image is the LCP element: loaded **eagerly** (Ave's forced lazy-load bypassed
  for it); `object-fit: cover`.

## Related
- [Testimonials Component](TESTIMONIALS.md) · [Hero Carousel](HERO_CAROUSEL.md) ·
  [Design System Audit](DESIGN_SYSTEM_AUDIT.md)
