# Page Hero Component (`twb_page_hero`)

A lightweight, reusable **inner-page** hero (not the homepage carousel): a
brand-green text panel — eyebrow + H1 + intro — beside a cover image. Mirrors the
site's destination-landing pattern (text *beside* the image, not over it — see the
[Design System Audit](DESIGN_SYSTEM_AUDIT.md)).

- **Status:** built; first used on the dedicated Testimonials page.
- **Type:** WPBakery (`vc_map`) element; token-based; **no JavaScript**.
- **Source:** child theme `07_Source/Themes/ave-child/`.

## Files
| File | Role |
| ---- | ---- |
| `inc/page-hero.php` | Registers `[twb_page_hero]` + render; registers/enqueues its stylesheet on render |
| `assets/css/page-hero.css` | All styling; references the shared token layer (`var(--twb-*)`) |

Loaded via `inc/loader.php`. `functions.php` is not touched (the component owns
its asset registration).

## Params (client / no code)
- **Eyebrow** — small label above the heading (optional).
- **Heading** — the page **H1**.
- **Introduction** — short paragraph (optional).
- **Image** — shown beside the panel on desktop/tablet, **below it on mobile**.

## Behaviour
- Two columns (panel ~42% / image ~58%) on desktop & tablet; **stacks** at ≤767px
  (panel on top, full-width image below).
- Heading is the page's single **H1**; testimonial sections below use `H2`.
- Colours from tokens: green panel, white heading/intro, `--twb-yellow` eyebrow
  (all AA on green).
- Image is the LCP element: loaded **eagerly** (Ave's forced lazy-load is bypassed
  for it, as the hero carousel does for its first slide); `object-fit: cover`.

## Related
- [Testimonials Component](TESTIMONIALS.md) · [Hero Carousel](HERO_CAROUSEL.md) ·
  [Component Architecture Audit](COMPONENT_ARCHITECTURE_AUDIT.md)
