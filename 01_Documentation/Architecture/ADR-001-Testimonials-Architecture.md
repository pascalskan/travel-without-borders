# ADR-001 — Testimonials Architecture

> **Architecture Decision Record.** Captures the major architectural decisions for
> the Testimonials system **before implementation begins**, with the rationale and
> the alternatives that were rejected. This is the *why*; the
> [Testimonials System Specification](../TESTIMONIALS_SYSTEM_SPECIFICATION.md) is
> the *what*; the [Implementation Plan](../TESTIMONIALS_IMPLEMENTATION_PLAN.md) is
> the *how*.

| | |
| --- | --- |
| **Status** | Accepted (pre-implementation) |
| **Date** | 2026-06-26 |
| **Context** | Building the first fully-architected reusable component for the TWB child theme. Testimonials is the **canonical template** for all future components (FAQs, Guides, CTA/Trust Bands, Holiday Types, Featured Destinations, Offers, Customer Stories). |
| **Drivers** | Reuse, maintainability, client-editability, scalability to hundreds of records, accessibility & performance to the hero standard, and the established LocalWP + junction + Git workflow. |
| **Supersedes** | — |

ADR format: each decision lists **Decision · Why · Alternatives rejected**.

---

## D1 — WPBakery custom elements for presentation

**Decision:** present every component as a registered WPBakery (`vc_map`) element
(e.g. `[twb_testimonials_carousel]`), following the existing `[twb_hero_carousel]`.

**Why:** the whole site is built in WPBakery; the client edits pages there.
Elements keep components **client-editable** (drop a block, choose params) with no
HTML/CSS knowledge, while the logic stays in the child theme.

**Alternatives rejected:**
- *Gutenberg blocks* — the site is WPBakery, not the block editor; would create two editing paradigms.
- *Hardcoded page templates* — not composable by the client; every placement needs a developer.
- *Raw shortcodes only* — work, but no visual editor UI, params or discoverability.

---

## D2 — Child theme, not a plugin

**Decision:** implement entirely within the **Ave child theme**
(`07_Source/Themes/ave-child/`).

**Why:** the established workflow makes the child theme the **single source of
truth**, junctioned into LocalWP and version-controlled in Git. The components are
presentation tightly coupled to the theme's design language (tokens, Flickity,
WPBakery category). Keeping them in the child theme means one deployable unit and
one place to look.

**Alternatives rejected:**
- *Standalone plugin* — splits the codebase and deployment, duplicates the asset/token plumbing, and risks "active theme vs active plugin" drift; no benefit here since the site isn't multi-theme.
- *Must-use plugin* — same downsides; harder for the client to manage.

> Trade-off acknowledged: theme-coupled content is less portable than a plugin if
> the theme is ever replaced. Accepted because a theme migration would be a full
> redesign anyway, and the data layer (CPT/taxonomies) is theme-independent code
> that could be lifted to a plugin later if needed.

---

## D3 — Custom Post Type for testimonial storage

**Decision:** store testimonials as a `twb_testimonial` **Custom Post Type** with
post meta for structured fields.

**Why:** testimonials are **repeating, queryable records** reused across many
pages. A CPT gives a native admin UI, revisions, REST exposure, and standard
`WP_Query` access — enter once, surface anywhere.

**Alternatives rejected:**
- *WPBakery content per page* — the same quote re-entered on every page; impossible to filter, sort or scale.
- *Options/ACF repeater in Theme Options* — not paginatable, weak admin UX at scale, no per-record taxonomy.
- *Hardcoded array/JSON* — not client-editable.

---

## D4 — Shared taxonomies (Region, Holiday Type)

**Decision:** register **shared** taxonomies `twb_region`, `twb_holiday_type`
(+ optional `twb_interest`) — designed to be reused by future CPTs (Guides,
Offers, Events), not testimonial-only.

**Why:** they enable **contextual reuse** — a quote tagged `Bavaria` +
`Tailor-made` surfaces on the Bavaria page, the Tailor-made page, the homepage and
the filtered testimonials page, entered once. Defining the vocabulary **once**
prevents future redesign churn and gives the site a second navigation axis.

**Alternatives rejected:**
- *Testimonial-only categories* — would be duplicated for every future content type; no cross-content filtering.
- *Free-text meta tags* — no controlled vocabulary, no clean filtering, typo-prone.

---

## D5 — Reuse Ave's bundled Flickity

**Decision:** build carousels on the **Flickity** library already bundled with the
Ave parent theme (as the hero does); add **no new JS library**.

**Why:** zero new dependencies/bytes; visual + interaction consistency with the
hero (the design benchmark); already registered handles to enqueue on demand.

**Alternatives rejected:**
- *Swiper / Glide / a new carousel lib* — duplicate capability, extra payload, inconsistent feel, more maintenance.
- *Hand-rolled carousel* — reinvents accessibility/touch/edge-cases Flickity already solves.

---

## D6 — `filemtime()` asset versioning

**Decision:** version component CSS/JS by **file modification time** (already in
`functions.php`).

**Why:** every edit automatically busts the browser cache during development and
after deploys — no manual version bumps, no stale assets for editors/clients. This
was added precisely because the static theme version caused stale CSS.

**Alternatives rejected:**
- *Static theme version string* — caused stale-asset bugs; easy to forget to bump.
- *Manual query-string versioning* — error-prone, per-asset busywork.
- *Build-tool content hashing* — no build pipeline in this project; unnecessary complexity.

---

## D7 — Single shared card renderer

**Decision:** one **shared card renderer** (`inc/helpers-card.php` →
`twb_render_testimonial_card()`) used by every element (Quote Block, Carousel,
Grid).

**Why:** **never duplicate component markup** (Principle 2). One renderer means one
place to change card structure, accessibility semantics and fallbacks — every
surface updates together; no drift between carousel cards and grid cards.

**Alternatives rejected:**
- *Per-element markup* — three copies of card HTML to keep in sync; guaranteed drift and inconsistent a11y.
- *A full template engine* — overkill for one card; adds indirection.

---

## D8 — Contextual queries with a fallback chain

**Decision:** a query/helper layer (`inc/testimonials-query.php`) resolves content
by **source mode** (featured / region / type / **context** / manual) with a
**contextual → featured → empty** fallback, and **transient caching**.

**Why:** the UX audit requires *relevant* proof deep in the journey. Context mode
auto-detects the current page's region/holiday-type term and shows matching
quotes; the fallback prevents empty boxes; caching protects performance at scale.

**Alternatives rejected:**
- *Manual selection only* — every page hand-curated; unmaintainable at scale; no automation.
- *Always-featured everywhere* — not contextual; weaker conversion.
- *No fallback* — empty sections on pages without a match (poor UX).
- *Uncached queries* — repeated `WP_Query` cost as records grow.

---

## D9 — Loader architecture: `inc/loader.php` with explicit ordered requires

**Decision:** `functions.php` requires a single **`inc/loader.php`** that lists
**explicit `require_once` statements in dependency order** — not a glob/loop over
`inc/`.

**Why:**
- **Deterministic loading** — order is guaranteed across OS/filesystems.
- **Easier debugging** — the boot sequence is one readable file; fatals are traceable.
- **Predictable dependencies** — dependency order is encoded and visible.
- **Reduced merge conflicts** — adding a component is one placed line; `functions.php` is touched only once (at infrastructure setup).
- **Easier onboarding** — a contributor sees the whole component graph and boot order at a glance.

**Alternatives rejected:**
- *`glob()`/loop over `inc/*.php`* — non-deterministic order, hidden/implicit dependencies, risk of including stray or half-written files, harder to debug a fatal.
- *Many `require_once` lines directly in `functions.php`* — bloats and churns `functions.php`, causing repeated merge conflicts as components are added.
- *PSR-4 autoloader / Composer* — these components are WordPress hook-and-render files, not classes; an autoloader adds tooling without benefit here.

---

## Decision summary

| ID | Decision | One-line rationale |
| -- | -------- | ------------------ |
| D1 | WPBakery custom elements | Client-editable presentation in the site's existing builder |
| D2 | Child theme, not a plugin | Single source of truth; matches the workflow; theme-coupled design |
| D3 | Custom Post Type | Queryable, admin-managed, scalable record storage |
| D4 | Shared taxonomies | Contextual reuse + future second nav axis; define vocabulary once |
| D5 | Reuse bundled Flickity | No new dependency; consistent with the hero benchmark |
| D6 | `filemtime` versioning | Automatic cache-busting; no stale assets |
| D7 | Single shared card renderer | One markup source; no duplication or a11y drift |
| D8 | Contextual queries + fallback + cache | Relevant proof deep in the journey, performant, no empty states |
| D9 | `inc/loader.php`, explicit ordered requires | Deterministic, debuggable, low-merge-conflict boot |

## Consequences

- Establishes the **canonical component pattern** for the whole future library —
  new components follow D1–D9 or raise a new ADR.
- The data layer (D3/D4) is theme-independent code, leaving a future
  plugin-extraction path open if the theme is ever replaced.
- Phase-1 scope is bounded; search/analytics/REST UIs are deferred (see the
  Implementation Plan §16).

## Related
- [Component Architecture Audit](../COMPONENT_ARCHITECTURE_AUDIT.md)
- [Testimonials System Specification](../TESTIMONIALS_SYSTEM_SPECIFICATION.md)
- [Testimonials Implementation Plan](../TESTIMONIALS_IMPLEMENTATION_PLAN.md)
- [Local Development Workflow](../LOCAL_DEV_WORKFLOW.md)
