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
| Savita's copy edits (15 Things, 7 Facts, Top 10) | Post content (DB) | **No — live only** | **Mirror to local before any content deploy, or they are lost** |
| `/category/germany-travel-guide/` → `/blog/` 301 | Redirection plugin (DB) | No — plugin data | Re-create if Redirection is ever reset |
| Related-post category link removed | `templates/related-post.php` | Yes — committed | Already reconciled |

---

## 2026-08-26 to 2026-08-28 — Client correction list (Savita)

**Made by:** Claude, via wp-admin (Chrome extension), at the client's direction
("all this work should be done on the live site").

Six items raised by the client after reviewing the V8/V9 release. Four were
changed, one was investigated and **contradicted the assumption behind it**, one
is an open question for the client. Three of the four changes exist **only in
the live database** and are listed in the reconciliation table above.

### 1. The "Germany Travel Guide" category page — done

> "if you press Travel guide it actually takes you to a separate page with a
> girl jumping into a swimming pool !!! and then Germany stuff — we need this
> page to disappear please!"

The link was in the **related-posts cards at the foot of every post** — the
parent theme printed the post's category under each card title. Every post sits
in the one category, so the same link appeared twice on all eight posts, leading
to `/category/germany-travel-guide/`: an archive nobody designed, with a stock
header image belonging to no article.

Fixed in two halves, because either alone leaves a way in:

- **The link** — removed by overriding the parent's `templates/related-post.php`
  in the child theme, dropping only its `<ul class="related-post-categories">`
  block. **This is in Git** and needs no reconciliation. The category itself is
  untouched — Yoast and wp-admin both rely on it; only its display is dropped.
- **The URL** — a 301 to `/blog/` added in the Redirection plugin, covering
  anyone arriving from a search result or an old bookmark. **Plugin data, not in
  Git.**

**A trap worth recording:** the redirect appeared not to work for some time
after it was saved. It was correct all along — **WP Rocket was serving the
archive from cache, so the request never reached WordPress and Redirection never
ran.** Clearing the cache made it fire immediately. Redirection also shows a
"database needs updating" notice (4.1 → 4.2); that is unrelated, was **not** the
cause, and was deliberately **not** run, because it migrates 86 live redirects,
one of them carrying over 1,000 hits.

**Verified:** all eight posts render zero category links and zero
`related-post-categories` blocks; the related cards still render with their
images and titles; `/category/germany-travel-guide/` returns `301 → /blog/`.

### 2. Blurred pictures on the Travel Blogs page — the assumption was wrong

The client was about to be told the images cannot be improved "because that is
the size and quality of the photos themselves". **That is not the main cause and
the message should not be sent as it stands.**

Measured on the live index at a 1440px viewport, where each card is displayed at
473 CSS px and so needs ~946px to be sharp on a 2× screen:

| Featured image | Served | Original | Verdict |
| -------------- | ------ | -------- | ------- |
| `Colditz_Castle_2011` | 490px | **1200px** | can be sharpened |
| `cd380ad3-…` | 490px | **1067px** | can be sharpened |
| `ac-almelor-…unsplash` | 490px | 848px | improvable, not to full sharpness |
| `img-0457-1_orig` | 490px | 640px | improvable, not to full sharpness |
| `9132dd68-…` | 490px | 551px | improvable, not to full sharpness |
| `neuschwanstein-castle-christies` | 490px | 540px | improvable, not to full sharpness |
| `berlin-new` | 490px | 540px | improvable, not to full sharpness |
| `beer` | 441px | 441px | genuinely at its ceiling |

**The theme requests a fixed 490×300 crop for every card and offers no larger
srcset candidate**, so even a 1200px original is thrown away. Seven of the eight
originals are larger than what is being served. Two are large enough to go fully
sharp; five would visibly improve without reaching it; only `beer` is truly
limited by its source file.

**Fixed 2026-08-28, and more cheaply than expected.** No new image size and no
media regeneration were needed: `tmpl-timeline.php` now asks for `large` rather
than `liquid-timeline-blog`, and WordPress falls back to the full file by itself
for the six images that never had a `large` generated. Measured afterwards on
live: **two cards fully sharp, five visibly improved, one unchanged** — `beer`,
which is at its ceiling. In Git, deployed.

Two consequences worth recording:

- **The cards no longer share a height.** The hidden `<img>` is what gives the
  figure its height, so each card now takes the shape of its own photograph.
  The uniform height could be restored with `aspect-ratio`, but only by cropping
  to fit, and the client's instruction was the opposite — *"we dont want the
  photos cropped at all they should show all the image as best as possible"*.
  Letterboxing to a fixed box was tried and rejected: black bars down the sides.
  The listing is a masonry grid, so it absorbs the varying heights.
- **The page carries about 520KB more image weight.** The cards are lazy-loaded,
  so it is spread down the scroll. Two images are worth replacing at source
  rather than re-encoding here: `berlin-new.png` is a photograph saved as a PNG
  (325KB) and `cd380ad3…` is a poorly compressed JPEG (277KB at 1024px). Both
  would fall a long way with no visible loss. There is no WebP delivery on this
  site — checked, the server ignores an `Accept: image/webp` header.

Separately, a genuine sharpness bug **was** found and fixed in this window: the
in-article images were being **upscaled by up to 224%** by `width: 100%` in
`blog.css`. That is now `width: auto; max-width: 100%`, so no article image
renders above its natural size. In Git, deployed.

### 3. Footer "blog" vs "blogs" — open question for the client

Both links go to `/blog/`. The **footer says "Blog"** (singular) and the
**homepage button says "See all Blogs"** (plural). The footer follows the normal
convention; the homepage button is the inconsistent one. Not changed — the
client asked which was right rather than asking for a change.

### 4. "15 Things to Know" (post 5235) — done

Section 3 replaced with the client's supplied copy: the heading is now
**"3. Sundays are for Culture, Not Shopping"**, followed by three new
paragraphs. **Live only — mirror to local.**

### 5. "7 Facts" (post 5244) — done

The repeated photograph was removed, keeping the top one. The article carried
`beer-1.jpeg`, byte-identical (md5 `a933bafcc10b…`) to the featured image
`beer.jpeg`. Remaining article images: `wine.jpeg`, `spa.jpeg`.
**Live only — mirror to local.**

### 6. "Top 10 Best Places" (post 5255) — done

The repeated photograph was removed, keeping the top one — the same
Mauerspringer mural as the featured image. **Live only — mirror to local.**

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
