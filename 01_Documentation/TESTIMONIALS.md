# Testimonials Component (`twb_testimonials`)

Implementation notes for the reusable testimonials carousel. Read this before
changing it — the data-storage approach is a deliberate, recorded decision (see
**Architecture decision** and **Future enhancements**).

- **Status:** built and verified locally (WPBakery element; not yet placed on any production page).
- **Type:** custom WPBakery element built on Ave's bundled **Flickity**, following the `twb_hero_carousel` pattern.
- **Source of truth:** child theme `07_Source/Themes/ave-child/` (junctioned into LocalWP — see [Local Development Workflow](LOCAL_DEV_WORKFLOW.md)).

---

## Files

| File | Role |
| ---- | ---- |
| `inc/testimonials.php` | Registers the `[twb_testimonials]` WPBakery element (`vc_map`) + render callback + single card renderer; registers and enqueues its own assets on demand |
| `assets/css/testimonials.css` | All component styling; references the shared token layer (`var(--twb-*)`) |
| `assets/js/testimonials.js` | Initialises Flickity (one instance, guarded) from a `data-twb-testimonials` options attribute; respects `prefers-reduced-motion` |
| `assets/css/twb-tokens.css` | Shared design tokens the component inherits (colour, type, spacing, radius, shadow, motion, container) |

The element is loaded via `inc/loader.php` (the deterministic child-theme
loader). **`functions.php` is not modified to add this component** — components
own their own asset registration.

---

## Architecture decision — WPBakery params (not a CPT), for now

Testimonial content is entered as **repeatable WPBakery element params**, exactly
like the hero carousel's slides. This keeps the whole component editable in the
builder with no code and matches the established hero pattern.

This **intentionally differs** from [ADR-001 §D3](Architecture/ADR-001-Testimonials-Architecture.md),
which specifies a `twb_testimonial` Custom Post Type. The CPT is deferred (see
**Future enhancements**); the current WPBakery implementation is the **production
implementation** for this milestone. The data model is simple enough that a later
migration to a CPT can reuse the same card renderer and CSS/JS unchanged.

---

## How it behaves

| Aspect | Setting / behaviour |
| ------ | ------------------- |
| Transition | **Slide** (Flickity default). *Not* fade — `flickity-fade` is dequeued site-wide (see [Hero Carousel](HERO_CAROUSEL.md) Gotcha 1). Do **not** add `fade: true`. |
| Autoplay | Optional (element param), default 6000 ms; pauses on hover/interaction; **disabled automatically under `prefers-reduced-motion`** |
| Loop | Infinite (`wrapAround`) |
| Controls | Prev/next arrows (white circles, hero interaction language) + pagination dots; keyboard arrows; draggable |
| Card | Square, hairline border, shadow-lift on hover; semantic `figure` > `blockquote` > `figcaption` (`cite` for the name) |
| Rating | Optional 1–5 stars (yellow), shown only when set; accessible label "Rated X out of 5" |
| Badges | Optional Region (green) + Trip type (sage) uppercase pills |
| Avatar | Initial-letter monogram fallback (no images stored in this element) |
| Accessibility | `:focus-visible` yellow ring, keyboard nav, reduced-motion, AA-oriented colours, semantic quote markup |
| Performance | Assets enqueued only when the element renders; bundled Flickity reused (no new library); `filemtime` cache-busting; no CLS (un-initialised cards hidden until Flickity is ready) |
| Fallback | Empty quotes are skipped; a block with no usable quotes renders nothing (no empty box) |

---

## Element params (client / no code)

In WPBakery, add the **TWB Testimonials** element (under the **Travel Without
Borders** category):

- **Eyebrow** — small label above the heading (optional).
- **Heading** — section H2 (optional).
- **Testimonials** (repeater) — per item: **Quote** (required), Author name,
  Author location, Trip type (badge), Region (badge), Rating (0–5), Travel date.
- **Auto-rotate** + **interval (ms)**.
- **Show rating stars** / **Show badges** toggles.
- **Background** — Surface (light grey) or White.

No HTML/CSS required; fully reusable and configurable across pages.

---

## Future enhancements

| Enhancement | Notes |
| ----------- | ----- |
| **Custom Post Type migration** | Move storage to a `twb_testimonial` CPT + shared taxonomies (`twb_region`, `twb_holiday_type`) per [ADR-001](Architecture/ADR-001-Testimonials-Architecture.md) §D3/D4 and the [Testimonials System Specification](TESTIMONIALS_SYSTEM_SPECIFICATION.md). Enables enter-once / reuse-anywhere, contextual queries, filtering and pagination. The current card renderer and styles are designed to be reused by a CPT-backed element with no visual change. **Not implemented this milestone — recorded as the planned next step.** |
| Contextual queries + fallback chain | Per ADR-001 §D8 (contextual → featured → suppress) — depends on the CPT. |
| Grid + filtering + load-more | The dedicated Testimonials page (carousel here is the homepage/contextual surface). |
| Optional avatar image | The element currently uses a monogram fallback; an image field can be added. |

---

## Verification (local, this milestone)

Verified with Playwright on a scratch page (homepage left untouched): element
renders, Flickity initialises, assets load on demand with `filemtime` versions,
keyboard navigation advances slides, 0 console errors, responsive at desktop/
mobile with 44px touch targets and no horizontal scroll, empty-field fallback,
reduced-motion handling, and **hero carousel regression-clean**.

---

## Related
- [Homepage Hero Carousel](HERO_CAROUSEL.md) · [Component Architecture Audit](COMPONENT_ARCHITECTURE_AUDIT.md) · [Testimonials System Specification](TESTIMONIALS_SYSTEM_SPECIFICATION.md) · [ADR-001 — Testimonials Architecture](Architecture/ADR-001-Testimonials-Architecture.md)
