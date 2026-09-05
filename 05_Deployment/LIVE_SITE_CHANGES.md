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
| V8 text/label changes | Page content, menus, Yoast titles (DB) | **Local was NOT in sync — reconciled 2026-09-03** | Homepage, tagline and menus pulled live→local; verify after any content deploy |
| V7 image replacements | Media library + page content (DB) | Local DB matches | Verify after any content deploy |
| Child theme CSS fixes | Theme files | Yes — committed | Already reconciled |
| Savita's copy edits (15 Things, 7 Facts, Top 10) | Post content (DB) | **No — live only** | **Mirror to local before any content deploy, or they are lost** |
| `/category/germany-travel-guide/` → `/blog/` 301 | Redirection plugin (DB) | No — plugin data | Re-create if Redirection is ever reset |
| Related-post category link removed | `templates/related-post.php` | Yes — committed | Already reconciled |

---

## 2026-09-05 — Header logo and strapline

Deployed through wp-admin. Four PNGs to the media library, six theme options,
one child-theme upload, one cache purge.

### What went live

| | before | after |
|---|---|---|
| desktop logo | 337×70, no retina source | **400×83**, `logo-black-800px.png` at 2× |
| sticky logo | 337×70, no retina source | **400×83**, `logo-white-800px.png` at 2× |
| phone logo | 169×35 from a 337px file | **200×42** (fluid), `logo-white-800px.png` |
| strapline, phone | 13.5px, one line | **17.9px**, two balanced lines |
| strapline, 1200–1365 | 18px | **19px** |
| strapline, 1366+ | 18px | **21px** |
| header, phone | 166px | **166px** — unchanged |
| menu column padding | 5% a side (~82px at 1440) | **15px**, matching its neighbours |

Live attachment ids: **7443** black-400, **7444** white-400, **7445** black-800,
**7446** white-800. They differ from local (7604–7607) — pick files in the media
picker, never by id.

Theme options changed: `header-logo`, `header-logo-retina`,
`header-sticky-logo`, `header-sticky-logo-retina`, `menu-logo`,
`menu-logo-retina`. Their previous values are recorded at
[`02_Assets/Logos/master/rollback-logo-options-LIVE.json`](../02_Assets/Logos/master/rollback-logo-options-LIVE.json).

### Two things that would have wasted an hour next time

**Redux reported "Settings Saved!" and saved nothing.** The theme options panel
accepted the six new values, showed its success notice, and a reload brought
back the old ones — twice. Its own save path drops values written into the
fields rather than picked through its media modal. Submitting the form the
ordinary way instead (it is a standard settings form posting to `options.php`
with `option_page=liquid_one_opt_group`) saved all six, confirmed by reload.
**Never trust that notice — reload and re-read the fields.**

**The theme upload needed the submit driven, not clicked.** The uploader's
`Install now` did nothing through several ordinary clicks; posting the form's
own `FormData` to `update.php?action=upload-theme` went straight through to
"Destination folder already exists", then `Replace installed with uploaded`.
This is separate from the truncated-upload problem recorded on 2026-09-04 — the
file reached the input intact at 104,921 bytes both times.

### Verified on live

Eight pages at 390px (3×) and 1440px (2×), after the purge:

- Phone: logo 200×42 fetching `logo-white-800px.png`, strapline 17.94px over two
  lines, header 166px, **no horizontal scroll**.
- Desktop: menu column padding 15px, logo 400×83, strapline 21px, **only
  "Trade" clipped — the same single item as before this work**, call-to-action
  button at 1511px against 1575px before.
- Document scroll width 1696px against **1761px before**: the pre-existing menu
  overflow is 65px less bad despite a logo 63px wider.
- **Zero page errors, zero failed requests.**

Every measurement matches the local build row for row.


## 2026-09-04 — Special Interest, Special Events and the standalone pages (live)

**Made by:** Claude, child theme via wp-admin plus two content edits over the
REST API, at the client's direction ("we will deploy everything apart from
destinations at once").

The rest of the client's page-by-page review. Nearly all of it turned out to be
**the same three faults** repeating across every page family, so one stylesheet
(`inner-pages.css`, formerly `destination-pages.css`) now serves them all:

| Family | Pages | Faults found |
| ------ | ----- | ------------ |
| Special Interest | 8 | 390×120 banner, banner **below** the copy, one grey paragraph each |
| Special Events | 6 | identical |
| About, Privacy, T&Cs | 3 | banner, and H2 38px against a 40px H1 |
| Trade EN + DE, Planning | 3 | H2 38px against a 40px H1 |

Section headings are now a uniform 40 / 32 / 24 on desktop and 40 / 28 / 22 on a
phone across all six standalone pages.

### Two things that were not what they looked like

**The "dead Learn More" is not dead.** It was reported as an `href="#"` going
nowhere and the client asked for it to be removed. It carries
`data-localscroll` — the theme's scroll-to-next-section feature — and `href="#"`
is simply how that is wired. A real mouse click scrolls the page on every page
tested (Eagle's Nest 0→565, Colditz 0→491, Bavaria 0→476, homepage 0→1363); an
earlier programmatic `.click()` did not fire the theme's jQuery handler, which
is what made it look broken. **It was not removed** — doing so would have
stripped working navigation from 13 pages.

**The Colditz "comments are closed" was a pingback.** The John Sergeant blog
post links to the Colditz page, so publishing it made WordPress record a
pingback against the page — stored as a comment and rendered like one. It
existed only on live, which is why it could not be reproduced locally. Comments
and pings are now closed on pages as well as posts, fixing that page and any
other page a future post links to.

### Content edits — applied to LIVE's own copy, never pushed from local

Page 5535 (the Eagle's Nest): "About The Eagle's Nest" moved from row 9 to row 1,
and `twb-notes` added to the Important Notes row.
Page 4580 (Planning): `germany.jpg` moved above the "How We Can Help?" heading,
which also separates it from `traditional.jpg`, and `twb-notes` added to the
Important Information row.

**Both were transformed in place on live rather than pushed from local, and that
is not optional.** On page 4580 the two installs disagree on 14 of 17 rows, and
the attachment ids do not merely differ — they *mean different things*: local
`7569` holds the "Designed Around You" image while local's `7257` is a
Testimonials post, and on live `7257` is that image. Content pushed either way
would break pictures.

Rollback revisions: **7440** (Eagle's Nest), **7441** (Planning).

### Two mistakes caught during the work

- A first heading selector used `.wpb_column h3`, which pushed the **site
  footer** from 14px to 22px and an icon-box from 18px to 24px — the footer is
  built with WPBakery too. Scoped to `#content .ld-fancy-heading`.
- That then missed the Privacy Policy and T&Cs entirely, which write their 14
  and 15 headings as plain markup in `.wpb_text_column`. Both components named.

### A trap for next time: the Trade pages have different ids per install

After the first deployment the stylesheet loaded on every page except the two
Trade pages. They are **7263 and 7539 locally but 7338 and 7339 on production** —
the only pages in this set whose ids differ. Every other page shares an id, which
is why it was easy to miss. Both ids are now listed in the CSS and the enqueue.
**Check page ids against production before scoping CSS by `page-id`.**

### Verified on live at 390px

All 13 pages: stylesheet loaded, banner 300px and above the H1 where the page has
one, no grey paragraphs, footer still 14px, no "Comments are closed", no PHP
errors. Planning reads picture → "How We Can Help?" → …, with the two
photographs no longer touching. Notes render 15px italic on both pages.

### Nothing to fix: the blog "arrow"

Reported as "there is an arrow on the first blog, but the rest are just lines".
All eight cards are byte-identical in markup and behave identically: at rest a
line then the text, on hover the text then an arrow, the line sliding across to
become it. Measured on all eight — `[30, 0]` at rest, `[0, 30]` hovered, without
exception. What was seen was the first card in its hover state.

---

## 2026-09-04 — Destination pages: banner, intro colour, stacking order (live)

**Made by:** Claude, child theme uploaded via wp-admin, at the client's
direction ("make it live"). Theme only — no content changed.

Three faults the client reported on "Germany Holiday Destinations" and the
region pages beneath it. Two of the three turned out to be **mobile-only**,
which the report did not say and which matters for anyone re-testing:

| Reported | What it actually was |
| -------- | -------------------- |
| "Picture at the top of the page is cut off" | Fine on desktop. On a phone the banner collapsed to **390×120** — a square 1500×1500 photograph through a slot of aspect 3.25, so a third was visible and the castle was sliced through. Now 300px. |
| "Half the writing is white and half grey" | Both paragraphs sit in the same `.ld-fancy-heading`, which **is** set white. The first is wrapped in the element's own `<span class="ld-fh-txt">`; the second is a bare `<p>` that never got it, so it fell back to the theme's `#888`. Paragraphs now inherit their block's colour. |
| "Two pictures following each other" | Mobile stacking. These rows are picture-beside-copy on desktop and stack in source order on a phone, so a picture column written second lands against the next section's picture. A trailing picture column is now pulled above its copy. |

Fixed once in `assets/css/destination-pages.css` for the whole layout rather
than page by page, scoped through `body.page-id-4654` and
`body.parent-pageid-4654`.

**A trap worth recording.** The obvious selector for the banner — the first
column's `.wpb_wrapper` — also matches the **text** column's wrapper, and
setting a height there squashes the intro copy from 1077px to 300px and clips
it. It was caught before shipping by listing what the selector actually matched;
the tell was the H1 moving *up* when the banner was made taller. The banner is
now picked out as the wrapper in that position carrying a `vc_custom_`
background and containing no text.

### Verified live at 390px

| Page | stylesheet | banner | grey paragraphs | intro copy height |
| ---- | ---------- | ------ | --------------- | ----------------- |
| Destinations | yes | 300px | 0 | 1077px |
| Bavaria | yes | 300px | 0 | 802px |
| The Black Forest | yes | 300px | 0 | 760px |
| Rhine/Mosel/Eifel | yes | 300px | 0 | 745px |
| Northern Germany | yes | 300px | 0 | 571px |
| Eastern Germany | yes | 300px | 0 | 418px |

No PHP errors on any of them, the copy column is intact everywhere, and the
homepage was re-checked for regressions and is unaffected.

### Open — the city pages have the same two faults

Berlin, Munich, Hamburg and the other city pages sit **below** the regions, so
`parent-pageid-4654` does not reach them and they were deliberately left alone —
the brief named the regions. Checked on live: Berlin still shows a **120px
banner and one grey paragraph**, so the same two faults are there. Extending the
scoping to cover them is a small change if the client wants it; their picture
rows would need re-checking separately, as they use a different layout.

---

## 2026-09-04 — Homepage proportion work deployed to live

**Made by:** Claude, via wp-admin, at the client's direction ("make them live
and we can ask savita to check it all as confirmation").

The work built locally on 2026-09-03 (see [Pending Deployment](PENDING_DEPLOYMENT.md)
§9) went out in two parts.

**1. Child theme**, uploaded as a ZIP: `homepage-proportions.css` (new),
`header-strapline.css` and `functions.php`.

**2. Page 30 content** — the testimonials row moved to sit after the holiday
sections and before the blogs, with the banded rows re-alternated.

### The content change was applied to LIVE's own copy, not pushed from local

This matters and is worth repeating next time. Local and live had diverged in
ways that do not show in rendered text — live carries an extra 81-byte empty row
after the hero that local does not, and live's content is wrapped in `<p>…</p>`
where local's is not. Pushing local's copy wholesale would have carried local's
attachment IDs and dropped live's row.

Instead live's raw content was read over the REST API, the same reorder applied
to it in place, and the result written back. Live keeps everything of its own;
only the row order and two `el_class` values changed.

**Two things the verification caught before anything was written**, both worth
keeping as checks:

- The first transform **silently dropped the leading `<p>`** — it sliced from the
  first `[vc_row]` and lost the 3 characters before it. Caught by comparing the
  sorted characters of the old and new content, which is a cheap way to prove a
  reorder only reordered.
- The first theme upload **failed with "the theme is missing the style.css
  stylesheet"** even though the archive was valid — the upload had been truncated
  (WordPress was running translation updates at the time). The retry sent the
  full 95,648 bytes and installed cleanly. If that error appears with a ZIP that
  checks out locally, re-upload before rebuilding it.

### Verified on live, desktop and mobile

| | desktop | mobile |
| ---- | ------- | ------ |
| Page title | 44px → **34px** | 44px → **26px** |
| Section titles | 34/38px mixed → **28px, all equal** | → **22px** |
| Strapline | 16px → **18px** | 12px → **13.5px**, one line |
| Header height | — | 193px → **164px** |
| Blog thumbnail spread | 49px → **0px** | 68px → **0px** |
| Plärrer cards | inset → fill the row | staggered 67/60/53 → **aligned** |
| Hero photograph | taller | 256px → **344px** wide |

Section order now reads Wide choice → Special Interest → Special Events →
Augsburg Plärrer → **testimonials** → From Our Blogs, and the banding alternates
grey/white with no two adjacent rows the same. No PHP errors on the page.

**Rollback:** page 30 revision **7436** holds the previous content. The theme
rolls back by re-uploading the previous commit's ZIP.

**Still open** (unchanged by this deployment): the logo cannot be enlarged
without a vector original, the hero is at its ceiling inside a boxed row, and
"From Our Blogs" keeps its own 26px.

---

## 2026-09-04 — "15 Things to Know" featured image replaced (live)

**Made by:** Claude, via wp-admin on live, at the client's direction ("this
change should be done live").

The one blog image that genuinely could not be sharpened now can be, because the
client supplied a better one.

`img-0457-1_orig.jpg` was **640x480** — the smallest original on the site after
"7 Facts", and the card wants about 946px. Every copy that existed was 640px
(checked the repo, the pre-development uploads backup and the media library on
2026-08-28), so it was the one case where "the photograph itself is the limit"
was the true answer. The client's reply was to send a replacement: a royalty-free
photograph of a European map with a pin in Germany, **1920x1440**.

| | before | after |
| ---- | ------ | ----- |
| Attachment | 5236 `img-0457-1_orig.jpg` | **7433** `map-of-germany-travel-planning.jpg` |
| Original | 640×480 | **1920×1440** |
| Blog index card | 640px into a 473px slot — soft | 1920px — **sharp** |
| Homepage card | 640px into a 261px slot | 740px — **sharp** |
| Post cover | 640px into a 672px slot | 1200px (89% of ideal, not perceptible) |
| Alt text | none | "Map of Europe with a red pin marking Germany" |

Two things done deliberately rather than by default:

- **The file was renamed before upload.** It arrived as
  `15 things tyto know about germany.jpg` — the typo would have been baked into
  the URL permanently, since WordPress slugifies the filename and the URL cannot
  be changed later without breaking links.
- **The post was updated over the REST API, not through the editor.** Saving
  post 5235 in wp-admin re-runs the WPBakery editor and the Post Style dropdown,
  which has previously flipped a post's layout on save (see the 5247 note in
  [Pending Deployment](PENDING_DEPLOYMENT.md) §4). Setting `featured_media`
  through the API touches that one field and nothing else.

**Rollback:** the previous featured image is attachment **5236**, still in the
media library and unchanged. Setting post 5235's featured image back to it
restores the old state exactly.

**Mirrored to local 2026-09-04** (LocalWP's database was down at the time of the
live change, so this was done once it came back). Local attachment **7601**,
same 1920×1440 file, same alt text, set as the featured image on post 5235.
Note the attachment IDs differ between the two — 7433 live, 7601 local — which
is normal and is exactly why page content should never be pushed between them
without checking attachment references.

---

## 2026-09-03 — Homepage reconciled live → local

**Made by:** Claude, locally, before starting the homepage proportion work.

The reconciliation table above claimed the V8 text changes were already mirrored
locally. **That was wrong for the homepage**, and it would have bitten: the next
content deploy of page 30 from local would have silently reverted a week of live
edits. Found by diffing the two rendered homepages rather than trusting the note.

Live was ahead of local in four places. All four were pulled **live → local**, so
local is now the source of truth and safe to work from:

| What | Local had | Live had (now local too) |
| ---- | --------- | ------------------------ |
| Tagline / strapline | "Bespoke Holidays planned by the Germany Specialist" | "Bespoke Holiday Planning by the Germany Specialist" |
| Homepage intro copy | 3 paragraphs, "How can we help?" | 7 paragraphs, "From inspiration and itinerary design…" |
| Testimonials heading | "Bespoke Germany Planning Holiday Feedback" | "What our travellers say about our bespoke Germany holiday planning" |
| Menu label ×3 | "Planning Bespoke Holidays" | "Bespoke Holiday Planning" |
| Footer Nav ‑ 4 | included "Testimonials" | item removed |

Notes for anyone repeating this:

- The strapline is **not** only the `blogdescription` option. It is also
  hard-coded in the `liquid-header` post **4357** ("Main Header - Colour"), which
  is what actually renders. Changing the option alone does nothing.
- Page content was written with a direct `$wpdb->update`, not `wp_update_post`.
  In CLI there is no current user, so `content_save_pre` runs kses and strips the
  inline `style` attributes these blocks depend on for their green links.
- Rollbacks are kept outside the repository: the previous page 30 content, the
  previous header 4357 content, and a JSON record of the deleted menu item.

**Verified:** the rendered text of both homepages is now identical line for line
(134 lines each, empty diff), the intro block renders 7 paragraphs with all 7
green links pointing at the right pages. One cosmetic difference remains and was
deliberately not copied: live carries an extra **1px-high empty row** below the
hero, a leftover from the V8 editing.

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

**Checked 2026-08-28 for better source files and there are none.** The repo, the
`00_Backups/2026-06-25_Pre-Development/uploads` snapshot and the live media
library were compared for all eight. Only `Colditz_Castle_2011` has a larger
original anywhere (1920px in the backup against 1200px live), and that card is
already sharp, so there is nothing to gain. In particular **"15 Things to Know"
(`img-0457-1_orig`) is 640x480 in every copy that exists** — it improved from
490px to 640px but cannot reach the ~946px the card wants, and is the one image
where "the photograph itself is the limit" is the true answer. A replacement
photograph is the only fix for it.

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

- **The cards keep a uniform height and the photographs are still not cropped.**
  Two instructions that pull against each other — *"we dont want the photos
  cropped at all they should show all the image as best as possible"* and
  *"the blog sections are not structured symmetrically"* — and a card cannot
  satisfy both alone, the images being 16:9, 3:2, 4:3 and one perfect square.
  Letting each card follow its photograph was tried first and staggered the
  columns; plain letterbox bars were tried next and read as a mistake. What
  shipped fixes the card to the old 490x300 proportion and **fills the leftover
  area with a blurred, scaled copy of the photograph itself**, with the whole
  photograph sharp and complete on top of it. Verified uniform (0px spread
  across all eight cards) at 1440px, 768px and 390px.
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
