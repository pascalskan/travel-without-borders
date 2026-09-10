# Error Backstory — 13 August to 10 September 2026

Requested in `A.S tasks.md` V10: *"Complete a full backstory of all errors we
have encountered since we completed the last deployment."*

**Scope.** The last deployment is the initial live migration of **13 August
2026**. This covers everything from that date to **10 September 2026** — the V7
post-live list, V8, V9 and V10, and the client correction rounds in between.

**What counts as an error here.** Four different things, kept apart on purpose,
because they need four different responses:

| Class | What it means | Response |
| ----- | ------------- | -------- |
| **A. Shipped** | A defect that reached visitors, caused by this work | Fix, and add a verification step that would have caught it |
| **B. Claimed** | Something I told you that was not true | Correct it in the open; find why the reasoning failed |
| **C. Measured** | The check itself lied — false positives and false negatives | Distrust the method, not just the result |
| **D. Platform** | WordPress, the theme, or a plugin behaving unlike its own UI | Write it down so it costs an hour once, not every time |

Nothing here is hidden and nothing is softened. Several of these were caught
only because a measurement was repeated after it had already produced an answer
I liked.

---

## Headline count

| | Count |
| --- | ----- |
| Reached live and were visible to visitors | **5** |
| Caught before shipping (reverted or reworked) | **4** |
| Wrong claims I made and later corrected | **11** |
| Measurements that produced a false result | **10** |
| Platform traps that cost time but broke nothing | **17** |

**One of the five that reached live was still visible when you found it.** The
other four were found and fixed by our own sweeps, three of them within the same
working session.

---

# A. Errors that reached the live site

## A1. The sticky logo ran off the right edge of every phone

**Date:** 5 September · **Live for:** about two hours · **Severity:** high —
overlapped the menu button, so the nav was partly unusable on a phone.

The logo enlargement shipped at 400×83. What the deployment sweep never looked
at is that **a phone swaps to the *desktop* sticky mark as soon as you scroll**.
Measured on live afterwards at four widths, the scrolled logo ran from x25 to
**x425** — past the right edge of a 320, 360, 390 and 414px screen, and **20px
over the menu button**. At its old 337px it had cleared that button by 43px, so
this was ours, not pre-existing.

Fixed by pinning `img.logo-sticky` to the same `min(200px, 52vw)` as the phone
mark. After a second theme upload and purge:

| viewport | sticky logo | clear of the button |
| -------- | ----------- | ------------------- |
| 320 | 166px | 14px |
| 360 | 187px | 19px |
| 390 | 200px | 27px |
| 414 | 200px | 43px |

**Why the sweep missed it:** it only ever looked at the top of the page. The
resting state and the scrolled state use **different image files**, and only one
of them was checked.

> **Rule.** For any header change, verify the scrolled state as well as the
> resting one, on a phone as well as a desktop.

## A2. A heading rule reached into the site footer

**Date:** 4 September · **Live for:** minutes · **Severity:** medium — every
page's footer.

A first attempt at the inner-page title scale used `.wpb_column h3`. **The
footer is built with WPBakery too**, so it is inside `.wpb_column` — footer
headings went from 14px to 22px site-wide, and an icon box from 18px to 24px.

Scoped to `#content .ld-fancy-heading`. That immediately produced the opposite
error: it missed the Privacy Policy and T&Cs entirely, which write their 14 and
15 headings as plain markup inside `.wpb_text_column`. Both components are now
named explicitly.

> **Rule.** Before scoping by a page-builder class, check what else on the page
> is built with the same builder. On this site that is the footer, the header
> and the cookie bar.

## A3. Renaming two region slugs 404'd 21 town pages

**Date:** 5 September · **Severity:** high — 21 published pages unreachable.

The Redirection plugin runs with `monitor_post: 1` and
`monitor_types: ["post","page"]`, so it writes a 301 automatically when a slug
changes. **It does not follow child pages.** Renaming
`holidays-to-the-rhine-valley` and `germany-city-holidays` moved 21 towns
underneath them, and every one of those URLs 404'd.

Fixed with two regex redirects:

```
^/destinations/holidays-to-the-rhine-valley/(.+)$  ->  …/holidays-to-the-rhine-mosel-and-eifel/$1
^/destinations/germany-city-holidays/(.+)$         ->  …/major-cities-in-germany/$1
```

Spot-checked Trier, Cochem, Berlin and Munich: all 301.

> **Rule.** Any slug change on a page **with children** needs the child redirect
> added by hand. The plugin's automatic 301 covers the parent only.

## A4. A photo credit was lost in a re-upload

**Date:** August · **Severity:** low, but it is someone else's copyright line.

Four images with decomposed-Unicode filenames were broken on live and were
re-uploaded. I carried the **alt text** across but not the **caption**, so the
Stuttgart wine-village photo lost *"Weindorf 0216 © Stuttgart Marketing GmbH
Christoph Düpper"*. Restored once found. The other three had no captions to
lose.

> **Rule.** A replacement attachment carries alt text, caption, description and
> title. Check all four, not the one you happen to be looking at.

## A5. The image-weight fix made the site fetch *more* files

**Date:** 5 September · **Severity:** low (nothing looked wrong), but it was
shipped and measured as a win before it was one.

The obvious fix for oversized card images was to correct `sizes`. Deployed and
measured:

| | before | `sizes` only |
| --- | ------ | ------------ |
| desktop | 5,162 KB / 30 files | 4,481 KB / **37 files** |
| phone | 2,399 KB / 21 files | 2,399 KB / 21 files |

Bytes fell 13%, **requests rose from 30 to 37**, and phones did not move at all.
Smush's lazyload writes `src` first and `srcset` a moment later, so the browser
fetched the full-size original for `src` *and* a 1024w candidate once the srcset
arrived — two requests where there had been one.

The real fix is to stop the card asking for `full`. Resolving it to `large`
makes `src`, `srcset` and `sizes` all describe the same picture:

| | before | after |
| --- | ------ | ----- |
| desktop | 5,162 KB | **2,802 KB** (−46%) |
| phone | 2,399 KB | **1,998 KB** (−17%) |

**Had I stopped at the first measurement I would have reported a win while
leaving the site making more requests than before.**

A related version of the same mistake, earlier: a `sizes` fix that stripped
`auto` inside the render function. WordPress adds `auto` later, in
`wp_filter_content_tags()`, so the replace ran before the text existed. The
value landed but `auto` was still in front of it and nothing improved. Hooking
`wp_content_img_tag`, which runs after, is what worked — **and it cost you a
second theme upload.**

> **Rule.** Measure the thing the user experiences (bytes *and* requests, on
> both viewports), not the attribute you changed.

---

# Caught before shipping

Four changes were built, measured, found worse, and reverted rather than
deployed. They are recorded in the CSS so nobody retries them:

1. **`flex: 1 1 0` on the Plärrer cards** — perfect on desktop, broke mobile
   into 166 / 166 / 346px. Needed a breakpoint split.
2. **Cutting the hero gutter** to widen the photo made it *narrower* (918px).
3. **Raising the mobile-nav breakpoint above 1199px** — the theme caps
   `media-mobile-nav` at 1199 in `liquid-responsive.php`. Forcing it higher
   breaks the JS-built mobile header. The theme is *designed* not to support it.
4. **Letting each blog card follow its own photograph** staggered the columns;
   plain letterbox bars read as a mistake. What shipped fills the leftover area
   with a blurred copy of the photograph itself.

---

# B. Things I told you that were wrong

These are the ones that matter most, because a wrong answer confidently given
costs more than a bug — you make decisions on it.

## B1. "The logo already renders 1:1, so scaling it up would blur it"

**True only at device pixel ratio 1.** The `header-logo-retina` slot was empty,
so the logo was *already* blurry at its existing size on every retina screen —
which is most phones and most modern laptops. The recommendation not to enlarge
it was based on a measurement taken at the one ratio where the problem does not
appear.

## B2. "The CW Sports tracking is configured and complete"

It was configured. **It had never been tested.** Tested against live, **no click
event reaches GA4 at all**. It now ships explicitly marked inert — the child
theme carries `inc/outbound-tracking.php` and `assets/js/outbound-tracking.js`
but they push to a dataLayer that no container reads.

This is the single item still outstanding on the whole project.

## B3. "Enhanced Measurement is disabled at tag level" — twice

Told to you as the cause of B2, then repeated. Both wrong: outbound click
detection is switched **on in both places it exists** — the stream level and the
Google tag level. The actual blocker is a legacy Universal Analytics tag on the
property, and removing it needs GTM access.

There is a caveat I should have raised sooner: **every test used an automated
headless browser.** The page view and GTM's own click tag fired in it, so it is
not blocked wholesale — but GA4's Enhanced Measurement listeners are exactly the
sort of thing that can behave differently under automation.

## B4. "The Learn More link is dead"

Reported as `href="#"` going nowhere, and you asked for it to be removed. It
carries `data-localscroll` — the theme's scroll-to-next-section feature — and
`href="#"` is simply how that is wired. A real mouse click scrolls the page on
every page tested (Eagle's Nest 0→565, Colditz 0→491, Bavaria 0→476, homepage
0→1363).

**An earlier programmatic `.click()` did not fire the theme's jQuery handler**,
which is what made it look broken. It was not removed — doing so would have
stripped working navigation from 13 pages.

## B5. "The title descriptions are all complete"

Marked done on the basis that the flagged pages "already carry a full 3–5 line
intro". They did not. **You caught this and wrote "(false completion)" in the
task file**, and it was redone page by page. This is the only error in this
document that you found rather than we did, before the V10 round.

## B6. "The meta question is settled"

A canary page (`rhine-in-flames`) reported 16/16 clean, so I concluded the
WPBakery stale-meta problem was resolved. **The canary was a false positive** —
that page's content changes introduced no *new* `vc_custom` classes, so the
stale meta happened to still cover them. Five pages were affected, and the plan
changed: every pushed page needs an admin re-save.

## B7. "The header difference is the template's row nesting"

Based on the rendered DOM. Wrong — both templates had the same 3-row structure.
The real cause was an **unbalanced `<div>`**. Reading live's actual stored
content is what settled it.

## B8. "Local needs its `post-style` values cleared to match live"

Backwards. **Local was right the whole time** — it was live that was wrong, and
what I had done was bring live into line with local. Clearing anything locally
would have been a no-op at best.

## B9. "The captions were preserved" (they should not have been)

You asked me to verify. I had preserved captions that should have been replaced.

## B10. "The Rhine and Northern sections have lost their headings"

Misread from a **509px-wide thumbnail** where the headings were too small to
see. Checked against the markup and at full size: both intact.

## B11. A CSS comment that asserted the header is not sticky

Wrong, and worse than a wrong sentence in chat — a wrong comment in the code
misleads whoever reads it next. Corrected in place.

---

# C. When the measurement lied

Ten cases where the check produced a confident, wrong answer. These are the most
dangerous category, because they do not feel like errors at the time.

## C1. `naturalWidth` is meaningless with `w`-descriptor srcsets

Used to judge whether images were sharp enough. With a `w`-descriptor srcset the
intrinsic width **equals the layout width**, so everything scored 1.0 and **13
images looked under-resolved that were not**. Comparing real file widths against
CSS width × device ratio is the only reading that means anything.

## C2. The carousel arrows are not in the DOM until you scroll to them

A selector was checked and reported 2 matches — Special Interest only, exactly
as intended. **That check was luck.** The homepage only has all eight arrow
buttons in the DOM once every carousel row has been scrolled into view.
Re-checked properly after scrolling every row, the original selector turned out
to be safe — but not by design.

## C3. The content regex matched `<div>`, but the page uses `<main>`

`<main id="content">`, not `<div id="content">`. The regex required a `div`, so
extraction silently fell back to **the whole page including the nav** — and the
nav legitimately differs between the two installs. Every page looked different.

## C4. A systematic +26px header offset read as 80 regressions

Every page scored ~0.2 with a consistent +26px height on live. That is the
signature of one offset, not 80 independent regressions. Re-run excluding the
header, the real differences surfaced.

## C5–C7. Three flavours of screenshot false positive

- **Parallax** background captured at a different scroll offset — identical
  content, different pixels.
- **A lazy image the screenshot never triggered** — live rendered it correctly;
  the capture just did not scroll far enough.
- **HTML entities** — `&#038;` versus `&` flagged as a text difference.

## C8. "There is a seventh form on live's contact page"

A false positive from page markup. Live has exactly the two Quform forms it
should — form 1 (individual) and form 3 (trade), each rendered with a random
unique-id suffix that looked like a third form id.

## C9. The final parity audit's last three failures

The site-wide desktop/mobile content audit finished at **3 pages flagged out of
97**. All three were false positives, and each needed a different check to
prove it:

- **Royal Heritage** — two attachments of the *same photograph*, one an edited
  copy, so the filenames differed while the picture did not.
- **`/tours/`** — a third-party widget that had loaded on one pass and not the
  other.
- **Homepage** — five files present on both viewports and invisible on both
  (carousel slides not currently in view).

## C10. PHP casts numeric array keys to int

`'337' === $name` never matched, because PHP had already turned the array key
`'337'` into the integer `337`. The logo build script silently skipped its
control size — **and had already overwritten the reference 337px files** before
this was noticed. Restored with `git checkout`. Fixed with `(string) $name`.

> **The pattern across all ten:** every one produced an answer that was
> *plausible*. The ones that were caught were caught by re-running the
> measurement a second way, not by looking harder at the first result.

---

# D. Platform traps

None of these were mistakes. All of them cost time, and all are written down so
they cost it once.

## The admin UI lies about saving — twice, two different ways

- **Redux "Settings Saved!" saved nothing.** The theme options panel accepted
  six new values, showed its success notice, and a reload brought back the old
  ones. Twice. Its save path drops values written into the fields rather than
  picked through its media modal. Submitting the ordinary settings form to
  `options.php` saved all six.
- **The footer logo save failed silently.** After submitting, I read the page's
  own textarea, saw the new value, and believed it. The textarea still held what
  I had typed — the submit had not gone through. A fresh load an hour later
  showed the old attachment.

> **Rule.** Never trust a success notice, and never read back the form you just
> edited. **Reload, then read.**

## WP Rocket serves pages before WordPress runs

A redirect looked broken for some time and was correct the whole time — WP
Rocket was serving that page from cache, so the request never reached
WordPress. The same thing made a deployment verification report a stale result.

> **Rule.** Purge ("Clear and preload"), then **re-verify with a cache-busting
> query string.**

## The theme uploader

- **"Install now" did nothing** through several ordinary clicks. Posting the
  form's own `FormData` to `update.php?action=upload-theme` went straight
  through.
- **A truncated upload reports "missing style.css"** on a provably valid
  archive. Retrying sends the full bytes.
- **PowerShell's `Compress-Archive` writes backslashes** into the zip's path
  entries, which WordPress cannot read — producing the *same* "missing
  style.css" message from a completely different cause. Built with `mkzip.py`
  since.

## The two installs disagree about ids

- **The Trade pages are 7263 and 7539 locally but 7338 and 7339 on production.**
  Every other page in that set shares an id, which is why it was easy to miss —
  the stylesheet loaded everywhere except the two pages it was written for.
- **Attachment ids mean different things.** On the Planning page the two
  installs disagree on 14 of 17 rows, and local `7569` holds the "Designed
  Around You" image while local's `7257` is a Testimonials post — and on live
  `7257` **is** that image. Content pushed either way would break pictures.

> **Rule.** Check page ids against production before scoping CSS by `page-id`,
> and never push content between installs where attachment ids appear.

## WPBakery

- **`vc_custom_<digits>` classes regenerate on save.** Never key CSS off them.
- **Stale post meta** needs an admin **Update** click per page to rebuild.
- **`vc_hidden-lg/md/sm/xs`** hide by `display`, and can be set on all four at
  once — which is how two content blocks came to be visible on no device at all.
- **`content_placement="middle"` is broken across 242 rows site-wide.**
  Pre-existing; deliberately deferred as a post-launch job.

## CSS and the DOM

- **`visibility` inherits.** An ancestor walk looking for what was hiding an
  element reported *the element itself* as the culprit, because it had inherited
  the value. The real cause was `.wpb_wrapper`, and one level above that,
  `.wpb_column`.
- **`parent-pageid-N` names only the immediate parent.** Augsburg's parent is
  Bavaria, not Destinations — which is why **53 of 59 destination pages never
  received the stylesheet written for them**, and then still failed after the
  enqueue was fixed, because the *selectors* had the same assumption.

## Toolchain

- **GD was present but not loaded** by default in the PHP CLI, which wiped
  generated image sizes before it was noticed.
- **The MySQL client interprets backslashes in Windows paths** — `C:\Users\…`
  makes `\U` an unknown command. Forward slashes work.
- **A Git-Bash path handed to Windows Python** looks like a real failure and is
  not.
- **LocalWP went down mid-project and stayed down**, which is why the agreed
  local-first workflow was replaced by pre-flighting changes on live by CSS
  injection.

## Pre-existing faults found, not caused

- **A pingback rendered as "comments are closed".** The John Sergeant blog post
  links to the Colditz page, so publishing it made WordPress record a pingback
  against that page — stored as a comment and rendered like one. It existed only
  on live, which is why it could never be reproduced locally.
- **Three attachment records point at files missing from the live server.**
  Confirmed against the rollback snapshots as pre-existing, not caused by this
  work.
- **Blog article images were being upscaled up to 224%** by `width: 100%` in
  `blog.css`.

---

# The one that was live longest

**The scroll animation was hiding published content — and it took three
attempts to fully stop it.**

The theme's `data-custom-animations` writes inline `opacity: 0` and
`transform: translateY(30px)` onto headings, text and image groups, then
animates them back as each scrolls into view. **When the animation does not
fire, the element simply stays invisible.**

Surveyed across fifteen live pages: **fifteen elements were stuck at opacity 0
after scrolling the entire page.** Among them, on Bespoke Holiday Planning, the
**"Planning a Group Visit?"** heading with its paragraph and **both photographs**.
That is published copy no visitor could read, and there is no way to know how
long it had been that way.

The fix took three passes because the animation hides content at **four**
levels, and each pass reached one more:

| pass | reached | still hidden |
| ---- | ------- | ------------ |
| 1 | text columns, headings, figures | the wrappers around them |
| 2 | `.wpb_wrapper`, and the `<img>` itself | the column |
| 3 | `.wpb_column` | — |

Passes 1 and 2 each looked complete when they shipped. What exposed pass 2 was
**your report** — *"Wine & Dine seemed lacking in photos"* — which was not a
mobile/desktop content difference at all. On a phone that plate-of-food
photograph sat at **`opacity: 1e-08`**. Not zero, so nothing looking for
`display: none` would call it hidden, and invisible all the same.

Trade was worse: **5 headings lost on mobile**, 1 on desktop, in both languages —
Professional Services, UK Destination Representation, Tourism Projects, Business
Events & Conference Support, and Who We Support.

**This is the strongest argument in the document for measuring the rendered
page rather than reading the code.** Every one of those elements was present,
correct and published. Nothing in the database was wrong.

---

# What actually prevents these

Not resolutions — the specific checks that would have caught the specific
failures above.

1. **Verify the state you did not change.** Scrolled as well as resting; mobile
   as well as desktop; the footer as well as the content. (A1, A2)
2. **Reload before believing a save.** Never read back the form you just
   edited. (D, twice)
3. **Purge, then re-verify with a cache-buster.** (D)
4. **Measure what the visitor gets** — bytes *and* requests, both viewports —
   not the attribute you edited. (A5)
5. **Re-run a passing check a second way** before calling it settled. Every
   false positive in section C survived the first check. (C1–C10)
6. **Check ids against production** before scoping by `page-id`, and never move
   content between installs where attachment ids appear. (D)
7. **A slug change on a page with children needs the child redirect by hand.**
   (A3)
8. **Say when something is untested.** B2 and B3 were both "configured"
   presented as "working". (B)
9. **`visibility` inherits, and page builders build the footer too.** Two DOM
   facts that produced three separate errors between them. (A2, D)

---

## Still open

**One item.** CW Sports Travel click tracking (B2/B3): a legacy Universal
Analytics tag on the GA4 property prevents Enhanced Measurement from recording
outbound clicks. Removing it needs GTM access. The code ships inert and is
documented in [Partner Link Tracking](PARTNER_LINK_TRACKING.md).

Everything else in this document is fixed, reverted, or superseded.
