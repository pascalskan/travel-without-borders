# Partner Link Click Tracking

Tracks clicks on the **CW Sports Travel** link in the header text of the
Augsburg Football Camp page.

**Status 2026-09-10: SOLVED, on our own server rather than in GA4.**
`/go/cw-sports-travel/` counts every click and forwards to the partner.
Visible at **Tools → Partner Link Clicks** and on the dashboard. Verified
end to end on live. See "The counter" at the foot of this document.

The GA4 route was rebuilt first and is left in place — the tag is installed
directly and provably transmitting — but it never reported an outbound click
and is no longer what answers the client's question. See "2026-09-10 rebuild".

Earlier status, for the record: configured in GA4 but not working; no click
event reached GA4. See "Test results".

The link itself is live on the football page and verified visible to anonymous
visitors.

## How it works

Everything runs inside GA4. Two pieces:

**1. Enhanced Measurement → Outbound clicks** *(enabled 2026-08-20)*

GA4's own tag now records a `click` event whenever a visitor follows any link
that leaves the site, carrying `link_url`, `link_domain`, `link_text` and
`outbound`. This was switched **off** on the property — only Page views were
being measured — so no outbound click has ever been recorded before now.

**2. A derived event: `cw_sports_travel_click`**

GA4 creates a named event from those clicks whenever both conditions match:

| Parameter | Operator | Value |
|---|---|---|
| `event_name` | equals | `click` |
| `link_domain` | contains (ignore case) | `cwsportstravel.com` |

"Copy parameters from the source event" is on, so `link_url` and `link_text`
carry through. It is marked as a **key event**, matching the convention of the
property's existing `booking_forms`, `email_click` and `phone_clicks` events.

### Why this rather than Google Tag Manager

The obvious approach — a GTM trigger firing a GA4 event tag — was not
available: **the account has no access to the GTM container** (`GTM-TCZM2CR`).
Tag Manager lists no accounts for `pskannavis@gmail.com`, so no trigger or tag
could be created.

Two alternatives were tested and rejected before settling on the GA4-only
approach:

- **Calling `gtag('event', …)` from the page.** `window.gtag` is undefined even
  after GTM loads — GTM keeps its GA4 instance private and does not expose a
  global.
- **Pushing gtag-format commands onto `dataLayer`.** Verified against the live
  container: gtag.js loaded via GTM does not process them. The event never
  reached GA4.

The GA4-only route needs no container access, no site code, and cannot be
broken by a GTM change.

## Test results (2026-08-20, against the live site)

The link was published to the live football page and tested end to end. The
page was loaded, consent given exactly as the banner does it, and the link
clicked. Findings:

| Check | Result |
|---|---|
| Link visible to anonymous visitors | yes |
| GTM loads after consent | yes (`GTM-TCZM2CR`) |
| GA4 tag registers | yes (`G-484B6GT6CW` present in `google_tag_manager`) |
| GA cookies set (`_ga`, `_ga_484B6GT6CW`) | yes |
| Consent Mode blocking? | no — all entries `implicit: true` |
| Page view reaches GA4 | yes |
| **Outbound click reaches GA4** | **no — nothing sent at all** |

Three delivery methods were tried and none produced a hit:

1. GA4 Enhanced Measurement outbound clicks (the configured route)
2. gtag-format commands pushed onto `dataLayer`
3. A separate gtag instance with `send_page_view: false`

`window.gtag` is undefined, and inspecting `google_tag_manager
.autoEventsSettings` shows GTM's own click/link/form listeners (`cl`, `lcl`,
`fsl`) but **no GA4 enhanced-measurement configuration at all**.

### Cause — confirmed on retest

Enhanced Measurement is **switched on in the GA4 interface but is not reaching
the tag**, and this is not a propagation delay.

The proof came from clicking the page's `mailto:` link, which *did* produce two
GA4 events — `click` and `email_click`. Both carried a single parameter,
`event_category = email`. That is a **GTM tag** firing off the container's own
link-click trigger. Enhanced Measurement would instead send `link_url`,
`link_domain`, `link_text` and `outbound`; **none of those parameters appear
anywhere on the property.**

So GA4 event delivery works fine — it is Enhanced Measurement specifically that
is inert.

### The actual cause — Google's own diagnostics say it outright

Tag Manager → **Google tags** → **TWB** → *Tag quality: Needs Attention* →
**View 1 issue** reports:

> ⚠️ **You are using legacy Universal Analytics tags**
>
> Legacy UA tags with connected site tags could lead to inconsistent data
> reports and **could prevent access to Google tag features like Enhanced
> Measurement events** and limited API commands. To leverage the full set of
> Google tag features, install the Google tag directly on your site or use
> Google Tag Manager.

That is exactly the observed behaviour: page views work, Enhanced Measurement
events do not.

The dead Universal Analytics tag (`UA-135755883-1`) noted at the bottom of this
document is therefore not merely wasted bandwidth — **it is actively
suppressing Enhanced Measurement**. Removing it from the GTM container should
make outbound click tracking start working, at which point
`cw_sports_travel_click` populates with no further changes.

That removal requires container access. Note this page — the Google tag admin
under the **Google tags** tab — *is* reachable without container access, which
is how the diagnostic was found.

**It is not a settings problem.** Outbound click detection was checked and is
switched **on in both places it can be set**:

- Data stream → Enhanced measurement → Outbound clicks
- Google tag (`GT-5NXKKJR`) → Configure tag settings → Manage automatic event
  detection → Outbound clicks

An earlier note in this file guessed the GTM tag had it disabled at tag level.
That was wrong and has been corrected: the tag-level toggle is on.

Event batching was also ruled out — the click was followed by a forced page
unload and navigation, and still nothing was sent.

A separate gtag instance was also tried as a workaround: the script loads
successfully, but sends nothing, because GTM already owns the measurement ID
and a second instance defers to it.

**Every route to GA4 from the website is therefore closed.** The remaining
options are in "Where this leaves it" below.

### Consequence for the custom event

`cw_sports_travel_click` is configured to match `event_name = click` **and**
`link_domain contains cwsportstravel.com`. Since `link_domain` is only ever
produced by Enhanced Measurement, **this event cannot currently match
anything.** It is harmless and becomes correct the moment Enhanced Measurement
is enabled — no rework needed then.

### Automation ruled out — confirmed with a real click

The earlier tests used a headless browser, which could in principle have
suppressed Enhanced Measurement. That has now been eliminated: the link was
clicked twice in a real, signed-in Chrome session with GTM loaded and consent
already granted, while watching GA4 Realtime.

| Realtime event | Before the clicks | After |
|---|---|---|
| `page_view` | 8 | **9** ← the test page load registered |
| `click` | 3 | **3** ← unchanged |
| `email_click` | 2 | 2 |

The page view incremented, proving the session was being counted live. The two
outbound clicks produced nothing, even after a refresh and 30 seconds' grace.

Note the contrast that makes this conclusive: the `click` count of 3 and
`email_click` count of 2 came from **automated** `mailto:` clicks earlier.
Automated clicks reach GA4 fine when a GTM tag fires them. Real clicks on an
outbound link never arrive. The variable is not automation — it is Enhanced
Measurement.

## Where this leaves it

**Option 1 — get GTM access (recommended).** Whoever administers container
`GTM-TCZM2CR` needs to either tick Enhanced Measurement on the GA4 tag, or add
a trigger for `cwsportstravel.com` link clicks. The container already has a
link-click listener and an established pattern for exactly this (`email_click`,
`phone_clicks`), so it is small, familiar work. Everything on the GA4 side is
already configured and waiting.

**Option 2 — a server-side redirect counter.** Point the link at a URL on this
site (`/go/cw-sports-travel/`) that records the hit and redirects onward. This
works today, needs no GTM and no GA4, and counts every click including from
visitors who declined cookies — so it is actually a more complete number. The
trade-off is a custom component to maintain, no GA4 segmentation, and the
visible URL changes.

**Not viable:** the Measurement Protocol from the browser, which would require
publishing an API secret in client-side JavaScript where anyone could use it to
inject data into the property.

## What you can see

Once the page is live: **Reports → Engagement → Events → `cw_sports_travel_click`**,
and in **Realtime** immediately while testing. Because it is a key event it
also appears in key-event reporting and can be used in comparisons.

Available breakdowns: date range, landing page, device, country, channel, new
vs returning — any standard GA4 dimension.

## What you cannot see

**Who** clicked. GA4 does not expose individual identities, and configuring it
to would breach both Google's terms and UK GDPR. Counts and segments only.

Clicks by visitors who **declined cookies** are not counted either — GTM (and
therefore GA4) never loads for them. The figure is a floor, not a total. That
applies equally to every other number in the property.

## Site-side code — currently inert

| Piece | Path |
|---|---|
| Script | `07_Source/Themes/ave-child/assets/js/outbound-tracking.js` |
| Loader | `07_Source/Themes/ave-child/inc/outbound-tracking.php` |

This pushes a richer `partner_link_click` event (with `partner_name`) onto the
dataLayer, and the link carries `data-twb-track` / `data-twb-partner`
attributes to drive it.

**It does nothing at present** — it needs a GTM trigger and tag to be picked
up, and there is no GTM access. It was written before that constraint was
discovered. It is harmless (it only appends to an array, and sends nothing if
consent is declined) and is left in place as a ready-made upgrade: if GTM
access is obtained later, the named event with partner attribution works by
adding a trigger, with no site changes.

**If you would rather not carry unused code, it can be removed** — delete both
files, the `require_once` line in `inc/loader.php`, and the two `data-twb-*`
attributes on the link. The tracking described above is unaffected.

## Separate finding: Universal Analytics is still firing

While testing the container, the page was observed loading
`analytics.js` and sending hits to `/j/collect?v=1` — that is **Universal
Analytics**, which Google stopped processing in **July 2023**. Those requests
have been going nowhere useful for over three years.

It is not causing harm beyond a wasted request on every page load, but the tag
should be removed from the GTM container when someone has access. Worth
raising with whoever administers it.


---

## 2026-09-10 rebuild — the tag installed directly

### What the container actually contains

Fetching the published container settles what three rounds of testing could
only infer. **`GTM-TCZM2CR` contains no GA4 tag at all.** Its seven tags are:

| type | count | what |
| ---- | ----- | ---- |
| `__ua` | 4 | pageview, and events for form / phone / email |
| `__fsl` | 1 | form-submit listener |
| `__cl` | 2 | click listeners |

No `__gaawc` (GA4 config), no `__gaawe` (GA4 event), no `__googtag`. All four
measurement tags are Universal Analytics, dead since July 2023.

**This corrects an inference in this document.** It recorded that `click` and
`email_click` reaching GA4 proved "GA4 event delivery works fine — it is
Enhanced Measurement specifically that is inert." The observation was right;
the attributed mechanism was wrong. Those events are not from a GA4 tag,
because there isn't one — they are legacy UA hits forwarded by Google's
**connected site tags** relay, which is precisely what Google's diagnostic
names. The relay carries `event_category` and nothing else, which is why
`link_url`, `link_domain`, `link_text` and `outbound` have never existed on
the property.

### The new stream

Deleting the UA tags needs container access we do not have. Google's remedy
names two routes and the other is open to us: install the Google tag directly.

| | |
| --- | --- |
| Stream | **TWB Direct (gtag)** |
| Stream ID | 15755605316 |
| Measurement ID | **G-HY9C6Z86W9** |
| Property | 386233391 (account 135755883) |
| Enhanced measurement | on; **Outbound clicks** confirmed enabled before creation |
| **Connected site tags** | **0 connected** |

That last row is the point of the exercise: no UA relay attached to this tag,
so nothing to suppress Enhanced Measurement.

Site side is `inc/analytics.php`, gated behind the same consent call as GTM
(both loaders are invoked from one function in `cookie-consent.js`, so consent
cannot apply to one and not the other). Deployed and verified on live.

### What is proven, and what is not

**Proven, on the wire**, via Resource Timing on the live football page:

```
region1.google-analytics.com/g/collect   tid=G-HY9C6Z86W9   en=page_view   v=2
```

- `window.gtag` is now **defined**. It never was before — GTM kept its
  instance private, which this document records as blocking two earlier
  workarounds.
- `google_tag_manager` registers `G-HY9C6Z86W9` as its own container.
- The collect endpoint is reachable from the test browser; nothing is blocking it.
- The hits carry **no `traffic_type` parameter**, so the property's
  "Internal Traffic → Exclude" data filter — which excludes only where
  `traffic_type` exactly matches `internal` — does not apply to them.

**Not proven.** GA4 has reported none of it: Realtime shows 0 active users,
DebugView is empty, and the stream still reads "No data received".

**The observation that matters most:** the long-established stream
`G-484B6GT6CW` shows the *same* `en=page_view` hits on the wire from the same
page loads, and is *equally* absent from Realtime. Since GA4 itself reports
that stream as "receiving traffic in the past 48 hours", its pipeline plainly
works. **No browser-side measurement distinguishes the new tag from the
working one.** That points at reporting latency on a stream created minutes
earlier, not at a transmission failure — but it is an inference, not a
verified result, and it is recorded as such.

### Measurements that could not answer the question

Four were tried and discarded rather than read into:

- **Patching `navigator.sendBeacon` / `fetch` / `Image`** caught nothing, not
  even a page view. gtag.js captures its own reference to `sendBeacon` at load
  time, before any later patch.
- **The extension's network log** captured no requests at all on that tab.
- **Resource Timing cannot see `sendBeacon`**, which is exactly what GA4 uses
  for outbound clicks — so it can prove `page_view` and can neither prove nor
  disprove `click`.
- **`responseStatus: 0`** on the GA4 hits is the ordinary opaque cross-origin
  response, not a failure. The UA hit reads 200 only because `/j/collect`
  exposes CORS headers.

### Next step

Re-check Realtime and DebugView after a few hours, and confirm with a real
outbound click. GA4 documents up to 48 hours before a new stream reports.
**Do not change anything else until that has been observed** — the site side
is complete and transmitting, and further changes would only add variables.

Once events do arrive, `cw_sports_travel_click` should begin matching with no
further work: it keys on `link_domain`, which Enhanced Measurement produces
and the UA relay never did.


---

## The counter — what actually shipped

`07_Source/Themes/ave-child/inc/partner-links.php`.

The client asked "any way we can view the amount of people clicking the link
is okay". This is that way, and it needs nothing outside this site — no
account, no tag, no container, and no cooperation from a property whose
Enhanced Measurement is suppressed by tags we cannot delete.

### How it works

`/go/<slug>/` records a click, then forwards to the partner. The link is
pointed at it by filtering rendered content, not by editing the page.

### Four decisions worth keeping

**302, never 301.** A permanent redirect is cached by the browser, so the
second and every later click would go straight to the partner without
reaching this site. The counter would sit at 1 forever — and would look
broken rather than wrong, which is worse.

**The destination comes only from the registry, never from the request.** A
`/go/` endpoint that accepts a target URL in a query string is an **open
redirect**, and those are used for phishing on the borrowed credibility of
the domain hosting them.

**The link is routed by a `the_content` filter, not by editing the page.**
The anchor lives in an `ld_fancy_heading` shortcode, so a filter at priority
20 (after `do_shortcode` at 11) sees the finished markup. A href edited into
the page content could be undone by a WPBakery re-save, and tracking would
stop silently.

**Only the exact registered URL is matched**, normalised for scheme, `www.`
and trailing slash. A link to a *different* page on the partner's domain is
left alone rather than being quietly redirected somewhere the author did not
choose.

### What is recorded

A count and a date. No cookie, no IP address, no user agent, no identifier —
so no visitor is distinguishable from another.

Two consequences, both good:

- **It needs no consent**, and therefore **counts the visitors who decline
  cookies**. Every number in the GA4 property is a floor; this one is not.
- **It measures clicks, not people.** De-duplicating into people would need a
  cookie or a stored IP hash, which would pull the feature back under the
  consent rules it currently sits outside. The admin screen says so plainly
  rather than implying otherwise.

Not counted: HEAD requests, prefetch/prerender (`Sec-Purpose`, `Purpose`,
`X-Purpose`, `X-Moz`), obvious bots by user agent, requests with no user
agent, and logged-in users who can edit pages — so testing the link does not
pad the client's numbers. **Every one of those still redirects**; the visitor
is never made to suffer for a counting decision.

### Verified on live, 2026-09-10

| check | result |
| ----- | ------ |
| Link on the page | `href="https://travelwithoutborders.co.uk/go/cw-sports-travel/"`, `target`/`rel`/`data-*` preserved |
| Raw partner URLs left in the page | 0 |
| `/go/cw-sports-travel/` | **302** → `https://cwsportstravel.com/clubs/` |
| Unknown slug | 302 → home page, rather than a 404 |
| 3 real clicks | counted **3** |
| bot, prefetch, preview, HEAD, no-UA | **all excluded** (would have read 8) |

**Reset to zero after testing, so the client's figures start clean.**

### Viewing and resetting

**wp-admin → Tools → Partner Link Clicks**, plus a summary widget on the
Dashboard. Both need the `edit_pages` capability, so any Editor or
Administrator sees them.

Each row carries a **Reset to zero** button. It is a form POST rather than a
link, because a GET that destroys data can be fired by anything that follows
links on a page — a prefetch, a link checker, a browser extension warming the
cache — and the nonce survives all of them, since the browser sends it
willingly. None of those is an intention to reset. It clears the daily history
along with the total; "reset to zero" that left yesterday's rows behind would
be a lie.

Re-verified after resetting: a subsequent click counted 1, confirming the
reset does not disable counting. Cleared again afterwards.

### Known limitation

The count is a read-modify-write on a single option, so two clicks in the same
millisecond could lose one. At this link's volume that is not worth a custom
table, and the figure is a measure of interest rather than an invoice.
Recorded so it is known rather than discovered.

### Adding another partner

Add a row to `twb_partner_links_registry()` — `label`, `target`, `where` — and
put the partner's real URL in the page as normal. The filter routes it and the
admin screen picks it up. No other change.
