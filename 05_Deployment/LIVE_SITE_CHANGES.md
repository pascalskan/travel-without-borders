# Live Site Changes

Changes made **directly on the production site** (travelwithoutborders.co.uk),
outside the normal local → Git → deploy flow. Newest first.

## Why this file exists

The repository is the record for anything built locally. It is **not** a record
for work typed straight into wp-admin, and between 15 and 23 August 2026 a
substantial amount of work was done that way — at the client's direction, so
that fixes reached visitors immediately rather than waiting for the next
deployment.

Without this file that work exists only in the live database. That matters in
three ways:

1. **A restore from backup would silently undo it.** The Phase 0 backup in the
   [Live Migration Plan](LIVE_MIGRATION_PLAN.md) predates all of it.
2. **The next content deployment could overwrite it**, because the local copy
   these pages would be pushed from does not always contain the same change.
3. **Two implementations of the same change can collide** — see the hero band
   colours below, which exist as Customizer CSS on live *and* as a theme
   stylesheet in this repository.

Each entry therefore records not just what changed, but **where the change
lives** and **what has to happen at the next deployment**.

## Reconciliation status — read this before deploying

| Change | Lives on production as | In this repo? | Action at deploy |
| ------ | ---------------------- | ------------- | ---------------- |
| Hero band link colours (V9) | ~~Customizer → Additional CSS~~ → now `hero-colours.css` | Yes | **RESOLVED 2026-08-25** — theme deployed, Customizer block removed |
| CW Sports Travel partner link | Page content (DB), page `augsburg-football-tour` | Yes, in the local DB copy | Content deploy carries it; verify it survives |
| V8 text/label changes | Page content, menus, Yoast titles (DB) | Local DB matches | Verify after any content deploy |
| V7 image replacements | Media library + page content (DB) | Local DB matches | Verify after any content deploy |
| Child theme CSS fixes | Theme files | Yes — committed | Already reconciled |

---

## 2026-08-23 — Hero band link colours (V9)

**Made by:** Claude, via wp-admin (Chrome extension), at the client's direction
("start this one and do it live, this is a site wide change").
**Scope:** every inner page — the shared dark hero row `vc_custom_1545203367366`.

### What changed
Text links inside the hero headings are now yellow (`#fed700`) and underlined;
the hero "Learn More" button is red (`#ff4d4d` at rest, `#cc0000` as the hover
wipe).

The hero band is `#242424`. The theme's link green `#1e5630` measures **1.80:1**
against it — effectively invisible, and the reason the CW Sports Travel link
could not be read when it was first added. The brand red `#cc0000` sampled from
the logo scores only **2.64:1**, so using it literally would have repeated the
mistake; `#ff4d4d` is the same hue lightened to the first value clearing 4.5:1
(it measures **4.75:1**).

### Where it lives — important
**Customizer → Additional CSS**, stored in the database as a `wp_custom_css`
post, appended after the pre-existing `.hideMob` rules and marked with the
comment `/* TWB V9: hero band - links yellow, Learn More red */`.

It is **not** in the live child theme's files. The live child `style.css`
carries only the white-heading rule for that row.

### The duplicate — resolved 2026-08-25

The child theme was deployed on 2026-08-25 and the Customizer block removed, so
these rules now live only in `hero-colours.css`. Verified afterwards that hero
links still render `#fed700` and "Learn More" `#ff4d4d`. The `.hideMob` rules
that shared that Customizer block were preserved.

The original problem, for the record:
The same rules exist in this repository as
`07_Source/Themes/ave-child/assets/css/hero-colours.css`, enqueued from
`functions.php`. That file has **not** been deployed — verified on 23 Aug 2026:
live loads `twb-tokens.css` and `header-strapline.css` from the child theme, but
not `hero-colours.css`.

When the child theme is next deployed, **delete the Customizer block**. Leaving
both is not fatal (the rules are equivalent) but it puts one visual decision in
two places, and the Customizer copy is invisible to Git.

### Verification
Confirmed on the live football page: hero link renders `rgb(254, 215, 0)`,
"Learn More" renders `rgb(255, 77, 77)`.

---

## 2026-08-20 — CW Sports Travel partner link

**Made by:** Claude, via wp-admin, at the client's direction ("lets put the link
into the live site so we can test this").
**Page:** `/special-interest-holidays/augsburg-football-tour/`

### What changed
In the hero text "Travel without Borders in Partnership with CW Sports Travel",
the words "CW Sports Travel" became a link to `https://cwsportstravel.com/clubs/`,
opening in a new tab with `rel="noopener noreferrer"`.

### Verification
Confirmed live: `href="https://cwsportstravel.com/clubs/"`, `target="_blank"`,
`rel="noopener noreferrer"`.

### Open item — click tracking is NOT working
Tracking this link was the point of putting it live early, and it does not yet
work. Clicks reach the site but never reach GA4. The cause is recorded in
[Partner Link Tracking](../01_Documentation/PARTNER_LINK_TRACKING.md): a legacy
Universal Analytics tag on the property prevents Enhanced Measurement from
recording outbound clicks. The fix is to remove that tag, which needs GTM access
that had not been granted at the time of writing.

The child theme carries `inc/outbound-tracking.php` and
`assets/js/outbound-tracking.js` for this, but they are **inert** — they push to
a dataLayer that no container is currently reading.

---

## 2026-08-19 to 2026-08-20 — V8 text, labels and Special Events

**Made by:** Claude, via wp-admin, at the client's direction ("lets start on what
we can do directly on the live version").

### What changed
- **Strapline, every page:** "Bespoke Holidays planned by the Germany Specialist"
  → **"Bespoke Holiday Planning by the Germany Specialist"**, and made visible on
  mobile, where it had previously been hidden.
- **Main navigation:** "Planning Bespoke Holidays" → **"Bespoke Holiday Planning"**
  (links to `/tailor-made-holidays-to-germany/`).
- **Homepage "How can we help?"** — full copy replacement, with the capitalised
  terms (BAVARIA, BLACK FOREST, CHRISTMAS MARKET, MOSEL, COLDITZ CASTLE, WINE
  TASTINGS, SPECIAL INTEREST HOLIDAYS) linked to their pages.
- **Homepage testimonials heading:** → **"What our travellers say about our
  bespoke Germany holiday planning"**.
- **Testimonials page intro:** → "Bespoke holiday planning for Germany – Loved by
  our travellers".
- **About page browser title:** → "About Travel without Borders | Germany Holiday
  Planning".
- **Special Events pages** — all updated to the client's supplied replacement copy.

### Verification
Confirmed live on 23 Aug 2026: strapline shows the new wording; the navigation
carries "Bespoke Holiday Planning" and no longer carries "Planning Bespoke
Holidays"; the homepage testimonials heading shows the new wording.

### Related theme commits
Two child theme fixes from this window are in Git and need no reconciliation:

- `50f174c` — sharpen testimonial photos, show strapline on mobile
- `f32f099` — move strapline CSS out of `style.css` so it reaches visitors
  (WP Rocket was serving a stale minified `style.css`, so edits to it never
  appeared for visitors — the reason strapline CSS now lives in its own file)

---

## 2026-08-15 — V7 post-migration fixes

**Made by:** Claude, via wp-admin, at the client's direction ("all work should be
done on the live site via chrome extension").

### What changed
- **Homepage testimonial photos** — replaced with higher-resolution originals;
  they had been visibly soft.
- **Homepage, first picture below the slideshow** — replaced with the photograph
  used for the Karen testimonial.
- **Homepage Augsburg slideshow image** — replaced with `Slideshow- augsburg.jpg`.
- **Testimonials page, Karen's photo** — replaced with
  `jodose-city-of-colditz-1610445.jpg`.
- **Planning your bespoke holiday page** — section heading "Travel Insurance and
  Financial Protection" → **"IMPORTANT INFORMATION"**.
- **Contact forms** — audited so that marketing consent is reported in the
  notification email whichever form is used and whether or not the box is
  ticked; consent removed from the Business form.

---

## 2026-08-13 — Initial live migration

The first push of the rebuild to production. Planned in and executed per the
[Live Migration Plan](LIVE_MIGRATION_PLAN.md).

Included the destination page batches, the removal of a page that was no longer
meant to exist (the "504" page, unpublished rather than deleted), and new pages
created as drafts and then published once confirmed.

From this date onward the live site and the local rebuild are **both** live
working copies, which is what makes this file necessary.
