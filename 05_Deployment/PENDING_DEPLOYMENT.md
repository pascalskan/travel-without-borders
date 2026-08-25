# Pending Deployment — Local Work Not Yet Live

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
| 5247 | Why Use a Bespoke Travel Planner | New post. **Re-dated to 2026-08-01**, `post_modified` set to match. Stray "Published: August 2026Germany Travel Guide" line removed. SEO title corrected. See §4 |
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
| `post_date` / `post_date_gmt` | `2026-08-01 09:00:00` | Puts it first on the date-ordered index |
| `post_modified` / `post_modified_gmt` | same as `post_date` | A new article, not a revision — stops an "Updated" line appearing under an identical published date |
| `post-style` | `cover-spaced` | Its layout differs from the other seven by design |
| `_yoast_wpseo_title` | `Why Use a Bespoke Travel Planner for Your Germany Holiday? \| Travel without Borders` | Replaced a title left over from the article this post supersedes |

**Yoast caches the computed title in its own `wp_yoast_indexable` table.**
Updating the postmeta alone does not change what the page renders. If the title
still shows the old wording after deployment, clear that post's indexable row
(or re-save the post in wp-admin, which rebuilds it).

**Verify:** the browser tab reads "Why Use a Bespoke Travel Planner…", the post
shows "Published: 01.08.2026" with no "Updated" line, and it is first on the index.

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

- **Six blog featured images are soft on high-resolution screens.** Each already
  serves the largest crop its original supports; the originals are 441–779px
  against the ~946px needed. Needs higher-resolution source files from the
  client. "7 Facts" (441×441, square) cannot be improved at all.
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
