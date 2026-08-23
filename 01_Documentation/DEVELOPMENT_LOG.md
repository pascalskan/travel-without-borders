# Development Log

A running, session-by-session record of work. Newest entries first. For the
formal version history see the [Changelog](../CHANGELOG.md); for current
progress see the [Project Status](../PROJECT_STATUS.md).

---

## 2026-08-23 (local) — Blog index, post navigation and article typography

All local. Nothing in this entry has been deployed.

- **Blog index dates removed.** Child override of `templates/blog/tmpl-timeline.php`
  (the parent loads it through `locate_template()`, so the child copy wins). The
  posts are evergreen guides, and a 2019 date made current advice look stale
  before it was read. The date still exists on the post and still drives ordering.
- **"Why Use a Bespoke Travel Planner" moved to the top of the index** by
  re-dating it to 1 August 2026. It is a new article, so `post_modified` was set
  to match `post_date` — otherwise the meta partial would print an "Updated"
  line under an identical published date.
- **Its index description was wrong** because the body opened with a stray
  `<p>Published: August 2026Germany Travel Guide</p>`, which was the first thing
  the excerpt generator saw. Removing it fixed the body and the description at once.
- **Previous/next links were backwards.** The parent wired them chronologically,
  so "Next Article" went to a *newer* post — meaning the top post had no "Next"
  at all. Child override of `templates/blog/single/navigation.php` swaps them to
  follow reading order down the index. The inversion against
  `get_adjacent_post()`'s `$previous` argument is deliberate and commented.
- **"Published:" vs "Originally published:"** is now conditional in
  `part-meta.php` — "Originally" only appears when an Updated line is actually
  shown. On a post that has never been revised it implied a history that did not exist.
- **Article typography was scoped to the wrong thing.** `blog.css` was anchored
  on `.twb-blog-article`, a class added by the child's own `default.php`. That
  tied all article typography to one layout, so "Why Use a Bespoke Travel
  Planner" — which uses `cover-spaced` — matched none of it and fell through to
  the per-post generated stylesheet's bare `h2 { font-size: 38px }`, the same
  size as the page title. Re-anchored on `.blog-single-content`, which every one
  of Ave's single-post templates emits and nothing else does. Verified across all
  eight posts: h2 25px, lead 17.5px, 672px measure.

### Blog featured images — blocked, not fixed
Six of the eight are soft on retina displays. The plumbing is correct: each
image already serves the largest crop its original supports. The originals are
simply too small (441–779px against the 946px needed). Needs higher-resolution
source files; "7 Facts" at 441×441 cannot be fixed at all.

Worth recording a measurement trap here: `naturalWidth` is **density-corrected**
for `srcset` images, so it reports the 1x figure even when the 2x file loaded.
Judging sharpness by it produces false failures — measure `currentSrc` instead.

---

## 2026-08-23 (local) — Special Interest pages: banding and layout

All local. Five pages: Colditz, Eagle's Nest, Royal Heritage, Fairy Tale
Castles, Motorcar Enthusiasts.

- **Grey/white banding restarted from the first content row** on Royal Heritage
  and Fairy Tale Castles; Eagle's Nest converted from tinted column *cards* to
  the site's full-width bands; Motorcar's white card inside a grey row removed.
- **Sections centred** where asked: Colditz "Planning Your Colditz Castle Trip",
  Motorcar's three closing sections, Eagle's Nest "Sample Itineraries".
  Lists inside a centred block are wrapped in an inline-block reset to
  `text-align: left`, so bullets line up instead of each item centring around a
  ragged edge.
- **"Get Behind the Wheel"** rebuilt on the Southern Bavaria pattern — one
  full-width column with the photograph floated right inside the copy.

### Two bugs found, both worth knowing
1. **WPBakery keeps one CSS rule per class**, but several rows on these pages
   were built *sharing* a `vc_custom_*` class. Giving two such rows different
   backgrounds means only the last declaration survives and they all render in
   that colour — this is what turned the whole of Eagle's Nest grey. Row classes
   are now minted unique per row in the tooling.
2. **`content_placement="middle"` does not work in this theme.** WPBakery puts
   `vc_row-o-content-middle` on the row and expects the columns to be its flex
   children, but Ave wraps them two levels deeper
   (`section.vc_row > div.ld-container > div.row.ld-row > .vc_column_container`),
   so `align-items` lands on a wrapper. **242 rows across 84 pages carry that
   attribute and none of them are getting it.** Deliberately *not* fixed
   globally — that would reflow most of the site. It is opt-in via
   `el_class="twb-vmiddle"` in `content-layout.css`, currently on one row.
   **This is worth revisiting as its own piece of work after launch.**

---

## 2026-08-20 (local) — Blogs rebuilt and the single-post template redesigned

All eight posts replaced with the client's new copy; images unchanged. Template
work in the child theme: `inc/blog.php`, `assets/css/blog.css`, and overrides of
`templates/blog/single/default.php` and `part-meta.php`.

- Twitter replaced with X (Font Awesome 4.7 has no X glyph, so an inline SVG),
  Instagram added, comments removed, author byline and "Published in" dropped.
- A 2× image size was registered for the timeline cards. The parent theme's
  `liquid_media_prevent_resize_on_upload` strips every registered size except
  thumbnail/medium/large, so a filter re-adds just this one.

**Mistake recorded deliberately:** regenerating those thumbnails was first run
under PHP CLI *without the GD extension*. `wp_generate_attachment_metadata()`
returned an empty `sizes` array rather than failing, and that empty result was
saved — wiping the thumbnail metadata for all eight blog images. Caught and
restored by re-running with `-d extension=gd`. Any image work through PHP CLI
needs GD loaded explicitly.

---

## 2026-08-20 (local) — Augsburg page rebuild

New header copy and body text, the client's replacement photographs throughout
the carousel, the English sights map placed beside "What to do", and the Regio
Augsburg partnership image reduced and moved near the top.

---

## 2026-08-20 (local) — Partner link click tracking

`inc/outbound-tracking.php` and `assets/js/outbound-tracking.js` added to the
child theme. **Currently inert** — they push to a dataLayer that no container
reads. Full write-up, including the root cause of why clicks never reach GA4,
is in [Partner Link Tracking](PARTNER_LINK_TRACKING.md).

---

## 2026-07-12 (fixes) — Homepage band alternation

- The testimonials band sits between a white section and the grey Special
  Interest section, so a single band can't alternate with both neighbours.
  Restored the band to **grey** and **flipped every following row** to keep a
  clean grey/white swap: white(intro) → grey(testimonials) → white(Special
  Interest) → grey(decorative) → white(Berlin) → grey(Special Events) →
  white(Blog) → green(email).
- Mechanism: added **`.wpb_row.twb-band-white` / `.wpb_row.twb-band-grey`**
  utilities to `twb-tokens.css` (filemtime-versioned; `.wpb_row` + `!important`
  beats each row's `.vc_custom_*` background) and applied them to the rows via
  `el_class` (homepage content = local DB, recreate on production). An earlier
  attempt to edit the rows' WPBakery `css` attributes directly failed (added
  properties don't get the generated class applied); `el_class` is reliable.

---

## 2026-07-12 (fixes) — Trust stats: centring + accuracy

- Fixed the trust-stats row centring: `.twb-trust-stats__list { margin: 0 }`
  was overriding the `.twb-container` auto margins, pushing the row flush-left on
  wide viewports. Now uses `margin-top/bottom: 0` only, so the container stays
  centred.
- Corrected the stats for accuracy: removed the "100% financially protected"
  claim; the destinations figure is now **50+** (the site offers **53**
  individual destinations across **6** regions — "60+" overstated it).

---

## 2026-07-12 (redesign) — Testimonials page realigned to the site's page grammar

After a structural audit of the other templates (Destinations, Bavaria,
Zell-Mosel, About, Groups, Tailor-made…), redesigned the page to speak the
site's dialect instead of "generic premium":

- **Restyled `[twb_page_hero]`** into the site's **split section** — photo one
  side, **charcoal `#242424` panel** the other, white H1 + muted intro + **yellow
  underline link** (added `heading_tag`, `image_side`, and link params; added
  `--twb-charcoal` token). Reused for both the page hero (H1) and the closing CTA
  band (H2). See [Page Hero](PAGE_HERO.md).
- **New `[twb_trust_stats]`** credibility strip (30+ / 100% / 60+).
- **New `[twb_email_strip]`** — token-based recreation of the site's standard
  green "e-mail us" closing band (the ported WPBakery row lost its `vc_custom`
  green background, so a clean reusable element is used instead).
- **Rebuilt `/testimonials/`**: split hero → trust stats → featured carousel
  (`el_id="reviews"`) → full grid → closing split CTA → email strip. Bands
  alternate charcoal/white/surface/white/charcoal/green.
- **Verified** (Playwright 1440/390): one H1, charcoal split hero (image left,
  "Read the reviews" → #reviews), stats, carousel + grid, H2 closing CTA, green
  email strip renders; hero/stats stack on mobile; no horizontal scroll; 0
  console errors; no PHP warnings; PHP 7.4 lint clean.

---

## 2026-07-12 (navigation) — Testimonials added to the main menu

**Completed**

- Added **Testimonials** (→ `/testimonials/`, page 7254) to the header menu
  (Main Menu, #15) **after Tailor-made Holidays and before Groups** (menu item
  #7255; Groups/Blog/Contact reordered +1). Local DB change — must be recreated
  on production. Verified on desktop and in the mobile hamburger menu.
- Investigated the desktop menu overflow the 9th item worsens (~1200–1520px
  clips Groups/Blog). Spacing trim alone can't fix it (Contact pill still
  overflows) and Ave caps the hamburger breakpoint at 1199 with JS hard-wired
  to it — raising it broke the header and was reverted. **Decision: accept for
  now, fix in the optimisation milestone** — see
  [DECISIONS.md](DECISIONS.md).

---

## 2026-07-12 (later) — Dedicated Testimonials page (Milestone 4)

Built on a new branch `feature/testimonials-page` (off `main`, which now contains
the merged homepage work).

**Completed**

- **Grid layout for `[twb_testimonials]`.** Added a `layout` param
  (`carousel` | `grid`) to the existing element — same file, same card renderer,
  same CSS (no duplication). Grid is a static responsive grid (3 → 2 → 1) that
  loads **no Flickity/JS**. Grid CSS added to `testimonials.css`.
- **New `[twb_page_hero]` element** (`inc/page-hero.php`, `assets/css/page-hero.css`):
  a reusable inner-page hero — green text panel (H1 + intro) beside a cover image,
  stacks on mobile; eager LCP image; token-based; no JS. Registered via the loader.
  See [Page Hero](PAGE_HERO.md).
- **Shared utilities** added to `twb-tokens.css`: `.twb-bg-green` (green band),
  `.twb-prose` (centred copy), `.twb-btn` (gold pill CTA button) — used by the
  page's intro and CTA rows.
- **Assembled `/testimonials/`** (local DB): page hero → intro → featured carousel
  → full 9-card grid → green CTA band (button → `/contact/`). Nine placeholder
  testimonials, each with a German destination photo linking to its destination
  page.
- **Verified** with Playwright at 1440 / 768 / 390: single H1; featured carousel
  (Flickity, keyboard) and grid (3/2/1) render; grid images lazy-load; CTA →
  `/contact/`; **homepage testimonials unchanged** (carousel, 4 cards) and its CTA
  now navigates to `/testimonials/`; no horizontal scroll; 0 console errors; no PHP
  warnings; PHP 7.4 lint clean.

**Not yet done**

- Real testimonial content + photos (deferred, as agreed).
- Optional: add the Testimonials page to the primary navigation (local menu).

---

## 2026-07-12

**Completed — Testimonials system (Milestones 1–3)**

- **M1 — Infrastructure.** Added `inc/loader.php` (explicit, ordered component
  loader per [ADR-001](Architecture/ADR-001-Testimonials-Architecture.md) D9);
  routed `functions.php` through it; registered the shared `twb-tokens` style
  handle. Hero carousel untouched.
- **M2 — Reusable component.** Built the `[twb_testimonials]` WPBakery carousel
  (`inc/testimonials.php`, `assets/css/testimonials.css`, `assets/js/testimonials.js`)
  on Ave's bundled Flickity, using the shared token layer. Decision: content is
  stored as WPBakery element params (not a CPT); CPT recorded as a future
  enhancement (ADR-001 superseded for the current implementation). See
  [Testimonials Component](TESTIMONIALS.md).
- **M3 — Homepage integration + polish.** Placed the band between the
  introduction and Special Interest Holidays (local DB; original homepage
  `post_content` backed up to post meta `_twb_homepage_pre_testimonials_backup`).
  Wired the CTA to `/testimonials/`. Polish: AA contrast fixes (eyebrow + trip
  badge → olive-dark), carousel `aria-label`, removed the on-click focus
  outline, single centred card with the arrows in the gutter (no half-card
  "peek"). Added an **optional destination image column that links to the
  mentioned place** (lazy-loaded; stacks on mobile with the arrows over the
  photo).
- **Verified** with Playwright at 1440 / 768 / 390: hero unchanged, homepage
  layout intact, no horizontal scroll, 0 console errors, no PHP warnings from
  `twb_*`, keyboard navigation, responsive behaviour.

**Not yet done**

- Real testimonial content + photos (to be added alongside the dedicated page).
- The dedicated `/testimonials/` page (the CTA target; 404 until it exists).
- Commit of the M3 working-tree changes (`inc/testimonials.php`,
  `assets/css/testimonials.css`, docs).

**Next**

- Build the dedicated Testimonials page, then populate real content.

---

## 2026-06-26

**Completed**

- Established the source-controlled local dev workflow (see
  [Local Development Workflow](LOCAL_DEV_WORKFLOW.md)):
  - Made `07_Source/Themes/ave-child/` the single source of truth.
  - Synced the hero-carousel code (`inc/`, `assets/`) that had been developed
    inside LocalWP into the repo (SHA-256 verified identical).
  - Replaced LocalWP's `themes/ave-child` with a directory **junction** to the
    repo copy (no admin / Developer Mode required; chosen over symlink).
  - Added `scripts/link-child-theme.ps1` (idempotent junction recovery).
  - Backed up the pre-junction live theme to `…\_theme-backups\`.
  - Verified: site serves 200 through the junction; Git detects code changes;
    repo→LocalWP round-trip works; nothing outside the child theme is tracked.

**Next**

- Resume feature development with all changes now version-controlled.

---

## 2026-06-25

**Completed**

- Discovery phase
- Mobile audit
- Slideshow audit
- Homepage baseline screenshots
- Full UpdraftPlus backup created and verified
- GitHub repository created
- Repository housekeeping and documentation (Phase 1)

**Next**

- Set up local development environment
- Bring existing Ave Child theme under version control
- Begin slideshow / homepage work
