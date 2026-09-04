# Pending Deployment — Local Work Not Yet Live

> **STATUS: DELIVERED 2026-08-25.** Everything in this manifest is now live —
> the child theme, Augsburg, the five Special Interest pages, all eight blog
> posts and the index, and the Customizer reconciliation in §5. See
> [Release Notes](RELEASE_NOTES.md) for the outcome and the problems met along
> the way. The document is kept as the record of what was deployed and as the
> template for the next content release; §6 and §7 remain open.

Everything built locally since **2026-08-20** that has not reached production.
Work the client asked to be done *directly on live* is a separate list — see
[Live Site Changes](LIVE_SITE_CHANGES.md), and read that first, because one item
there collides with this deployment.

Use this as the manifest. Work top to bottom; every item has a verification step
because content deployments are hard to spot-check by eye.

---

## 1. Scope

**In scope — 15 items**

| Area | Items |
| ---- | ----- |
| Special Interest | Colditz, Eagle's Nest, Royal Heritage, Fairy Tale Castles, Motorcar |
| Blogs | All 8 posts **plus the blog index page** |
| Destinations | Augsburg |

**Explicitly NOT in scope**

- **Wine and Dine** — not rewritten; leave as it is on live.
- **Football / Augsburg Football Tour** — the CW Sports Travel link is *already
  live* (added 2026-08-20). Do not redeploy this page: the local copy has no
  changes the live one lacks, and touching it risks disturbing a link that is
  working.

---

## 2. Child theme files

Live's child theme currently **matches the repository** — verified 2026-08-24 by
comparing the served CSS against the repo. Selector counts were identical in
`style.css` (25), `twb-tokens.css` (14), `cookie-consent.css` (17) and
`header-strapline.css` (7); the only differences were whitespace introduced by
WP Rocket's minifier.

**This matters: it means deploying the child theme from Git will not revert any
hand-edit made on live.** Re-check this if anyone edits live's theme between now
and the deployment.

Deploy the whole `07_Source/Themes/ave-child/` directory. New and changed since
the migration:

| File | Status | Purpose |
| ---- | ------ | ------- |
| `assets/css/blog.css` | new | Article typography, share row, reading measure |
| `assets/css/content-layout.css` | new | Carousel heights, text wrap, `.twb-vmiddle`, subheading |
| `assets/css/hero-colours.css` | new | Hero link colours — **see §5, conflicts with live** |
| `assets/js/outbound-tracking.js` | new | Partner-link clicks — **inert**, see §6 |
| `inc/blog.php` | new | Share row, comments off, 2× image size |
| `inc/outbound-tracking.php` | new | Ditto — **inert** |
| `inc/loader.php` | modified | Requires the two new `inc/` files |
| `functions.php` | modified | Enqueues `hero-colours.css` and `content-layout.css` |
| `templates/blog/tmpl-timeline.php` | new | Index cards — removes the date |
| `templates/blog/single/default.php` | new | Drops author bio, swaps share row |
| `templates/blog/single/part-meta.php` | new | Published / Originally published logic |
| `templates/blog/single/navigation.php` | new | Prev/next follow reading order |

Already live, no action: `header-strapline.css`, `inc/language-switch.php`,
`inc/testimonials.php`.

**Verify after deploying:** load any blog post and confirm
`blog.css`, `content-layout.css` and `hero-colours.css` all appear in the page
source. If WP Rocket is serving a stale combined file, purge its cache — this
has bitten before (see commit `f32f099`).

---

## 3. Database — pages and posts

WPBakery stores each page's generated CSS in the `_wpb_shortcodes_custom_css`
post meta, and **it is only rebuilt when an editor saves through wp-admin**. If
content is moved by any other route, that meta must be carried across too, or
the page loses every background colour, padding value and custom rule. The
grey/white banding on the Special Interest pages depends entirely on it.

### Special Interest — 5 pages

| ID | Page | What changed |
| -- | ---- | ------------ |
| 4501 | Colditz Castle Experience | New copy; "Planning Your Colditz Castle Trip" centred |
| 5535 | Eagle's Nest | New copy; tinted column cards replaced by full-width grey/white banding; Guided Visit vertically centred; two headings spaced |
| 5283 | British-German Royal Heritage Route | New copy; joined heading split; banding restarted from the first row |
| 5305 | Fairy Tale Castles | New copy; banding restarted; "Castles and Palaces" demoted to a subheading; text wraps the Southern Bavaria photo |
| 6564 | Motorcar Enthusiasts | New copy; white card removed from its grey row; three closing sections centred; "Get Behind the Wheel" rebuilt with the photo floated right |

### Blogs — 8 posts + the index

| ID | Post | What changed |
| -- | ---- | ------------ |
| 5247 | Why Use a Bespoke Travel Planner | New post. **Re-dated to 2026-08-25**, `post_modified` set to match. Stray "Published: August 2026Germany Travel Guide" line removed. SEO title corrected. See §4 |
| 6635 | John Sergeant / Colditz | New copy |
| 5241 | 10 Reasons Why Germany Travel | New copy |
| 5231 | Romantic Road | New copy |
| 5235 | 15 Things to Know | New copy |
| 5244 | 7 Facts | New copy |
| 5238 | Fairytale Castles | New copy |
| 5255 | Top 10 Best Places | New copy |

**Blog index page** — the page content is unchanged; its appearance changes
entirely through `tmpl-timeline.php`. Nothing to migrate, but it must be checked
after deployment (see below).

### Destinations — 1 page

| ID | Page | What changed |
| -- | ---- | ------------ |
| 5384 | Augsburg | New header and body copy, replacement photography, English sights map beside "What to do", partnership image resized and moved near the top. "the oldest figurative stained-glass cycle in the world" → "some of the world's oldest surviving figurative stained-glass windows" (client request, 2026-08-24) |

**Verify after deploying, in this order:**

1. **Banding** — each Special Interest page alternates grey/white from its first
   content row with no two adjacent rows the same colour. This is the single
   most likely thing to break, because it lives in the WPBakery meta.
2. **Blog index** — no dates on the cards; "Why Use a Bespoke Travel Planner"
   sits first.
3. **Post navigation** — on the top post there is no "Previous Article"; on the
   last there is no "Next Article"; every link in between moves one place down
   the index.
4. **Augsburg** — the stained-glass sentence reads as the corrected wording.

---

## 4. Post meta that travels with post 5247

Easy to miss, because none of it is in the post content:

| Meta | Value | Why |
| ---- | ----- | --- |
| `post_date` / `post_date_gmt` | `2026-08-25 09:00:00` | Puts it first on the date-ordered index |
| `post_modified` / `post_modified_gmt` | same as `post_date` | A new article, not a revision — stops an "Updated" line appearing. **wp-admin cannot set this**: WordPress stamps it on every save, so the publish date was moved to the deployment date to make both fall on the same day |
| `post-style` | `cover-spaced` | Its layout differs from the other seven by design |
| `_yoast_wpseo_title` | `Why Use a Bespoke Travel Planner for Your Germany Holiday? \| Travel without Borders` | Replaced a title left over from the article this post supersedes |

**Yoast caches the computed title in its own `wp_yoast_indexable` table.**
Updating the postmeta alone does not change what the page renders. If the title
still shows the old wording after deployment, clear that post's indexable row
(or re-save the post in wp-admin, which rebuilds it).

**Verify:** the browser tab reads "Why Use a Bespoke Travel Planner…", the post
shows "Published: 25.08.2026" with no "Updated" line, and it is first on the index.

---

## 5. Blocker — resolve before or during deployment

**The hero band colours exist in two places.**

The yellow hero links and red "Learn More" are live now as **Customizer →
Additional CSS**, stored in the database, marked
`/* TWB V9: hero band - links yellow, Learn More red */`. This deployment adds
the same rules as `assets/css/hero-colours.css`.

**Delete the Customizer block as part of this deployment.** Leaving both is not
visually broken — the rules are equivalent — but it puts one design decision in
two places, and the Customizer copy is invisible to Git, so the next person to
change the hero colours will change one and not the other.

**Verify:** after removing it, hero links still render `#fed700` and "Learn
More" still renders `#ff4d4d`. If they revert to green, `hero-colours.css` did
not deploy or is being cached — do not restore the Customizer block, fix the
stylesheet.

---

## 6. Known-incomplete — ship as is, do not present as working

**Partner-link click tracking does not work.** `inc/outbound-tracking.php` and
`assets/js/outbound-tracking.js` deploy with the theme, but they push to a
dataLayer no container reads, so no click reaches GA4.

The cause is not in this code: a legacy Universal Analytics tag on the property
prevents Enhanced Measurement from recording outbound clicks. The fix is to
remove that tag, which needs GTM access that has not been granted. Detail in
[Partner Link Tracking](../01_Documentation/PARTNER_LINK_TRACKING.md).

**Do not report tracking as delivered after this deployment.** It is code in
place awaiting an account change.

---

## 7. Carried forward — not blockers

- **The blog index featured images are soft on high-resolution screens — and
  the cause is the theme, not the source files.** An earlier note here said each
  image "already serves the largest crop its original supports". That was wrong,
  and the correction matters because it was about to be passed to the client as
  a reason nothing could be done. The theme requests a fixed **490×300** crop for
  every card and offers no larger `srcset` candidate, so the crop is thrown away
  regardless of what was uploaded. Measured 2026-08-28 against the ~946px a card
  needs at 1440px/2×: **seven of the eight originals are larger than the 490px
  being served** — two (1200px and 1067px) are big enough to go fully sharp, five
  (848, 640, 551, 540, 540) would visibly improve without reaching it, and only
  "7 Facts" (441×441) is genuinely limited by its source file. **"15 Things to
  Know" was the other genuinely-stuck one at 640×480, and it is now resolved** —
  the client supplied a 1920×1440 replacement on 2026-09-04 and it is live. That
  leaves "7 Facts" as the single image still needing new source material.
  **FIXED 2026-08-28** — `tmpl-timeline.php` now asks for `large` instead, which
  needed no regeneration and no new image size. Two cards are now fully sharp,
  five visibly improved, one unchanged at its ceiling. Full measurements in
  [Live Site Changes](LIVE_SITE_CHANGES.md), 2026-08-26 entry.
- **`content_placement="middle"` is broken theme-wide.** 242 rows across 84
  pages carry the attribute and none of them get it — Ave nests columns two
  levels below where WPBakery expects them. Deliberately not fixed globally;
  rows opt in with `el_class="twb-vmiddle"`. Worth its own piece of work after
  launch, not during.
- **The blog pages report a horizontal scroll width of 1771px against a 1440px
  viewport.** Pre-existing, caused by the header navigation, present on every
  post and unrelated to this work.
- **Post 5247's meta description is still the old article's** ("No one size fits
  all…"). It shows under the search result. Awaiting replacement copy.

---

## 9. Homepage proportion work — September 2026 — **DELIVERED 2026-09-04**

> Deployed and verified on live. Kept as the record of what went out and as the
> template for the next content release. The three "needs a decision" items at
> the end remain open.

From the client's email review of the homepage. Built locally on 2026-09-03,
**after** reconciling the homepage live → local (see
[Live Site Changes](LIVE_SITE_CHANGES.md), 2026-09-03) — do that reconciliation
first if this is ever rebuilt, or a week of live edits gets reverted.

### Theme files — travel with the child theme

| File | Change |
| ---- | ------ |
| `assets/css/homepage-proportions.css` | **new** — title scale, Special Events cards, blog thumbnails, hero size |
| `assets/css/header-strapline.css` | strapline sized fluidly so it cannot take a second line |
| `functions.php` | enqueues the new stylesheet, front page only |

### Database — page 30 (Homepage), content only

**The testimonials row was moved** to sit after the holiday sections and before
"From Our Blogs", which the client asked for. The four rows between "Wide choice
of destinations" and "From Our Blogs" were re-banded at the same time, because
moving the row alone left two greys and two whites adjacent. Nothing else in the
content changed — the reordered content is character-for-character the same,
only rearranged.

**This is a DB change and is not in Git.** It has to travel as page content, and
`_wpb_shortcodes_custom_css` travels with it as usual.

### Verify after deploying

1. Section order reads: Wide choice → Special Interest → Special Events →
   Augsburg Plärrer → **testimonials** → From Our Blogs.
2. Banding still alternates grey/white with no two adjacent rows the same.
3. Page title is 34px on desktop and 26px on mobile; "Germany", "Wide choice of
   destinations", "Special Interest Holidays" and "Special Events" are all 28px
   (22px mobile).
4. The strapline sits on **one line** at 320, 360 and 390px, and the header is
   164px tall rather than 193px.
5. The eight homepage blog thumbnails are all the same height.
6. The three Special Events cards share a left edge on mobile.

### Not done, needs a decision

- **The logo was not enlarged**, though the client asked. Every asset was 70px
  tall (337×70, 417×70 and 472×70 are three different lockups, not one at three
  resolutions) and it already rendered 1:1, so scaling it up would have blurred
  it. **Resolved 2026-09-04** — the client supplied a 1747×356 master. See
  section 10; the sharpness half is built, the size increase still needs a
  decision because it grows the header bar.
- **The hero is at its ceiling for a boxed row** (~972px wide). Going properly
  full-bleed is a "Stretch row and content (no paddings)" row setting on the
  page, which changes the homepage from a boxed layout to an edge-to-edge one.
  Left for the client.
- **"From Our Blogs" stays 26px** rather than 28px — it is an H3 sharing its
  classes with 14px and 18px headings elsewhere, so no safe selector reaches it.

---

## 10. Header logo — the retina fix — September 2026

### Why the logo looked soft

Not because it was too small on the page. `logo-black-337px.png` is 337×70 and
the header draws it in a 337×70 box, which is correct at 1× — but on any 2×
display (most phones, and every Retina or 4K laptop) the browser has to paint
that box with 674×140 physical pixels and has only 337×70 to work from. The
theme has a `header-logo-retina` slot for exactly this and it was **empty**, so
no `srcset` was emitted at all. Mobile was already covered — `menu-logo-retina`
points at the 337px file for a 160px base — which is why the blur showed on
desktop and not on a phone.

So there were two separate problems wearing one coat: **no 2× asset** (fixable
the moment a bigger master existed) and **no bigger master** (only the client
could supply it).

### The master

`twblogos.zip`, 2026-09-04. Four files, three of them the same wordmark at
three sizes and one a stray FrontPage metadata stub from 2008 that is not an
image at all. The usable one is **1747×356 at 300dpi** — the same artwork as
`logo-black-337px.png`, at 5.2×. Kept as
[`02_Assets/Logos/master/twb-logo-master-1747x356.jpg`](../02_Assets/Logos/master/twb-logo-master-1747x356.jpg).

It is a JPEG on white with no alpha, and the header needs transparency, so it
cannot be used as-is.

### How the 2× files were built

[`02_Assets/Logos/master/build-2x-from-master.php`](../02_Assets/Logos/master/build-2x-from-master.php)
— re-runnable, byte-reproducible.

1. **Resample first, key second.** The master is resampled while still
   composited on white, because "composited on white" is already premultiplied
   and gives clean edges; keying to alpha first and then resampling produces
   fringing.
2. **White → alpha, then unpremultiply.** Every colour in this logo — black,
   flag red, flag gold — has at least one channel at zero, so `alpha =
   255 − min(r,g,b)` recovers the alpha exactly and the colour divides back out.
   This also swallows the JPEG ringing around the white.
3. **Fitted to the existing artwork, not to the canvas.** The master's own
   margins differ from the in-use file's, so the artwork bounding box is scaled
   to exactly 2× the in-use bounding box (334×67 at x0 y2) and placed at exactly
   2× its offset. Verified: the new file's box is `0,4,667,137`, precisely
   double.
4. **The white variant** turns pure black to pure white everywhere except
   columns 562–637, the flag block, which keeps its own black band. That
   column range was measured off the existing black/white pair rather than
   guessed — the only difference between them is `000000 → FFFFFF` with alpha
   untouched, over columns 0–280 and 319–333 of 337.

Output: `logo-black-674px.png` and `logo-white-674px.png`, 674×140 RGBA.

### Wired up locally

Attachments 7602 and 7603, set into `liquid_one_opt` as `header-logo-retina`
and `header-sticky-logo-retina`. No code change — `liquid-header-image.php`
emits `srcset="… 2x"` as soon as those options are filled.

### Verified — the layout does not move

Measured in Chromium at 1440×900, `device_scale_factor=2`, live against local:

| | rendered | intrinsic | file fetched | header bar |
|---|---|---|---|---|
| Live today | 337×70 | 337×70 | `logo-black-337px.png` | 149px |
| Local with retina | 337×70 | 337×70 | **`logo-black-674px.png`** | 149px |

Same box, same header height, twice the pixels. This is a pure sharpness
change with no layout risk.

### To deploy

Nothing to push — no code, no database transfer. In wp-admin on live:

1. Media → Add New → upload `02_Assets/Logos/logo-black-674px.png` and
   `logo-white-674px.png` (or the 400/800 pair if the enlargement is approved).
2. Theme Options → Logo → set **Retina Logo** to the black file and **Retina
   Sticky Logo** to the white one. Leave the two base logos alone.
3. Purge WP Rocket ("Clear and preload"), then confirm on the front end that
   `img.logo-default` carries `srcset="…logo-black-674px.png 2x"`.

Attachment IDs on live will differ from the local 7602/7603 — pick the files
in the media picker, never by ID.

### Making it bigger — built, not deployed

Built from the same master by
[`build-from-master.php`](../02_Assets/Logos/master/build-from-master.php),
which now emits every size in one pass. Each output is an exact integer
multiple of the one above it, so a 2× file is double its base in every number:

| file | canvas | artwork | role |
|---|---|---|---|
| `logo-black-400px.png` / white | 400×83 | 397×80 at 0,2 | new base |
| `logo-black-800px.png` / white | 800×166 | 794×160 at 0,4 | new retina |

The script also writes a **control build** at 337px into `master/control/`,
which is not used by the site — it exists so the pipeline can be checked
against the file already shipped. Diffed composited-on-grey, the control and
the shipped `logo-black-337px.png` agree to a mean of **3.4/255**, and the only
row with a real difference is **y46**, the red-to-gold band boundary in the
flag, where the two land the transition a fraction of a pixel apart. The white
variants differ from the black ones identically, which proves the recolour adds
no error of its own.

The flag block is found by colour rather than by a hard-coded column, so the
white variant stays correct at any size. Detection deliberately accepts a
column carrying red **or** gold, not both: the flag's outermost columns are
antialiased and one band can register there before the other starts. Requiring
both narrowed the range by a pixel each side and would have whitened a hairline
of the flag's own black band.

Local theme options now point at the 400/800 set. Previous values are saved at
[`rollback-logo-options.json`](../02_Assets/Logos/master/rollback-logo-options.json).

Measured at 1440×900, `device_scale_factor=2`:

| | logo | header bar | sticky bar |
|---|---|---|---|
| Live today | 337×70 | 149px | 107px |
| Enlarged | 400×83 | 162px | 120px |

Mobile is untouched — 169×35 in an 80px bar in both, because the phone header
uses `mobile-logo-default` and its own `menu-logo` option, which were not
changed.

### The finding that blocks it — the desktop menu already overflows

Checking the enlargement turned up a fault that is **already live** and has
nothing to do with the logo. The main menu runs past the right edge of the
window, and `html { overflow-x: hidden }` hides the evidence, so items are
simply unreachable:

| viewport | hidden on live today |
|---|---|
| 1366px | **TESTIMONIALS and TRADE** |
| 1440px | **TRADE** |
| 1536px and up | none |

The header's call-to-action button is off-screen too — its left edge sits at
**1575px** in a 1440px window.

A bigger logo pushes everything 66px further right, so on its own it would cost
**TESTIMONIALS** at 1440 as well. The cause is dead space, not a shortage of
room: the column holding the menu carries `padding: 5%` on both sides — about
**82px a side** at 1440 — while the columns either side of it use 15px.

Bringing that column into line with its neighbours **exactly cancels the
enlargement**. Measured, not estimated:

| | menu starts | last item ends | hidden | CTA left |
|---|---|---|---|---|
| Live today | 475 | 1496 | TRADE | 1575 |
| Bigger logo alone | 541 | 1563 | TESTIMONIALS, TRADE | 1644 |
| Bigger logo + padding | **474** | **1496** | **TRADE** | **1511** |

The third row is the first row's menu position to within a pixel, with a logo
63px wider and the CTA 64px closer to being visible. The rule is desktop-only —
below 992px the column already computes to 15px, so phones are unaffected:

```css
@media ( min-width: 992px ) {
	#header .mainbar-row > .col { padding-left: 15px; padding-right: 15px; }
}
```

**Not applied.** The instruction on this engagement is not to touch the nav bar,
and this is a nav-bar rule, so it waits for a decision. Without it the
enlargement is a regression; with it the menu is where it is today and the
pre-existing overflow is 66px less bad.

Going further — trimming the menu links from 15px to 11px of side padding —
would close the gap entirely at 1440 (last item at 1442, a 2px overrun) and cut
1366 from 129px over to 75px. That is a separate change to the menu's own type
and spacing and is **not** recommended without the client seeing it.


---

## 8. Before you start

- **Take the backup.** Phase 0 of the [Live Migration Plan](LIVE_MIGRATION_PLAN.md).
  The live database now contains work that exists nowhere else — every direct
  edit in [Live Site Changes](LIVE_SITE_CHANGES.md), and the live Quform entries.
- **Never overwrite the Quform tables.** Live enquiries are real customer data
  and exist only there.
- Local rollbacks for every page and post touched are kept outside the
  repository, keyed by post ID and timestamp.
- Scratch PHP helpers used during this work were removed from the local
  WordPress root on 2026-08-24. They executed database writes with no
  authentication check and must never reach a public server — if any `tw_*.php`
  file appears in the webroot, delete it.
