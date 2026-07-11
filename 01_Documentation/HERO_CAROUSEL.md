# Homepage Hero Carousel (`twb_hero_carousel`)

Implementation notes for the custom homepage hero slideshow that replaced the
Slider Revolution dependency. Read this before changing the hero — several
behaviours are deliberate and were hard-won (see **Gotchas**).

- **Status:** built and in use on the local site (homepage / front page, page ID 30).
- **Type:** custom WPBakery element built on Ave's bundled **Flickity**.
- **Source of truth:** child theme `07_Source/Themes/ave-child/` (junctioned into LocalWP — see [Local Development Workflow](LOCAL_DEV_WORKFLOW.md)).

---

## Files

| File | Role |
| ---- | ---- |
| `inc/hero-carousel.php` | Registers the `[twb_hero_carousel]` WPBakery element (`vc_map`) + render callback; enqueues assets on demand |
| `assets/css/hero-carousel.css` | All hero styling |
| `assets/js/hero-carousel.js` | Initialises Flickity (one instance, guarded) from a `data-twb-hero` options attribute |
| `functions.php` | Registers/`filemtime`-versions the assets **and dequeues `flickity-fade`** (critical — see Gotchas) |

The element is loaded via `inc/loader.php` (the deterministic child-theme loader).

---

## How it behaves (current, intended config)

| Aspect | Setting / behaviour |
| ------ | ------------------- |
| Transition | **Slide** (Flickity default). *Not* fade — see Gotcha 1 |
| Autoplay | On, 5000 ms; pauses on hover/interaction (`pauseAutoPlayOnHover`) |
| Loop | Infinite (`wrapAround`) |
| Controls | Prev/next arrows + pagination dots; keyboard arrows; draggable |
| Image fit | **`object-fit: contain`** — every photo shows in **full, never cropped**; off-ratio margins blend into the grey band |
| Arrows | Pinned in **reserved side gutters** (`--twb-gutter`) so they always sit on the grey sides, **never over any image** (narrow or wide) |
| Layout | **Boxed** to the content width (sits in a normal WPBakery row, not full-bleed); the row background is **grey `#f7f7f7`** so the page alternates grey→white→grey |
| Caption | Title + description shown **below** the image, for the active slide only (no overlay) |
| Per slide | Image, Image alt text, Title, Description, Destination URL, "Open in new tab", + Button text/URL (stored, not yet rendered) |
| Accessibility | `aria-label` arrows, focus-visible yellow ring, keyboard nav, `prefers-reduced-motion` |
| Performance | First slide eager + `fetchpriority=high`; rest lazy; assets enqueued only when the element renders; `filemtime` cache-busting |

---

## Gotchas (read before editing)

### 1. `flickity-fade` is dequeued on purpose — do **not** re-enable fade
Ave loads a **flickity-fade** plugin site-wide that **globally patches Flickity's
cell positioning**. With it active, the hero's slides **desync** — the slide index
advances but the visible image lags/sticks/ghosts, which makes **autoplay look
broken** and produces double-exposed frames. This caused a very long debugging
loop.

`functions.php` therefore dequeues it (`twb_remove_flickity_fade`, priority 100)
and the hero runs in **slide** mode. Verified safe: **no Ave carousel uses fade**.

> If you set `fade: true` again, you reintroduce the desync/ghosting and the
> "autoplay doesn't work" symptom. Don't. If a cross-fade is ever truly required,
> it needs a fade engine that doesn't patch Flickity globally.

### 2. `contain` + arrows = reserved gutters, not edge-tracking
Because `contain` makes each image a different width, arrows pinned to the frame
edges would float over grey on narrow images and sit on top of wide ones. The fix
is **fixed side gutters** (`--twb-gutter`, via `.twb-hero__media` horizontal
padding) that the image is kept within, with the arrows living in those gutters.
This keeps arrows off every image at every width. (An earlier JS approach that
tracked each image's edge was removed in favour of this simpler, reliable one.)

### 3. The grey media background must match the band
`.twb-hero__media` has `background-color: #f7f7f7` so the `contain` margins blend
invisibly into the grey hero row. If the row background is ever changed, update
this to match.

### 4. Boxed vs full-bleed is controlled by the WPBakery row, not the component
`.twb-hero { width: 100% }` — it fills whatever row it's in. It's currently in a
normal (boxed) grey row. A "Stretch row and content (no paddings)" row would make
it edge-to-edge again.

---

## Editing the slides (client / no code)

Edit the homepage in WPBakery → open the **TWB Hero Carousel** element → the
**Slides** repeater. Each slide has Image, Alt text, Title, Description, optional
Destination URL (+ open-in-new-tab). Add/remove/reorder freely. No code needed.

**Image guidance:** landscape, ideally ~1600×900+; they're shown in full (contain),
centred, with grey margins for off-ratio shots. Compress to keep the hero fast.

---

## Current content (local only — not version-controlled)

- **9 slides**, in the original Slider Revolution order, using real photos imported
  from the backup uploads: Neuschwanstein, Rothenburg, Heidelberg, Dresden,
  Zell-Mosel, Augsburg, Lindau, Berchtesgadener Land, Colditz Castle.
- Descriptions are brief placeholders — expected to be replaced with final copy.
- Media Library attachments and the page layout live in the **local DB**, so they
  must be recreated/migrated on production (code deploys with the theme; content
  does not).

### Restore points (homepage `post_content` backups in post meta on page 30)
| Meta key | What it holds |
| -------- | ------------- |
| `_twb_hero_content_backup` | Original homepage content before the carousel was inserted |
| `_twb_static_hero_backup` | The original static intro row (removed then restored beneath the carousel) |
| `_twb_precontent_restore_backup` / `_twb_hero_prephotos_backup` / `_twb_hero_full_backup` / `_twb_hero_boxed_backup` | Successive content snapshots from later edits (photos, 9-slide build, boxing) |

A pre-junction copy of the live theme is also at
`…\Local Sites\travelwithoutborders\_theme-backups\`.

---

## Known trade-offs / possible future tweaks
- **Mobile image size:** the reserved gutters make images a little narrower on
  small screens (so the arrows have grey to sit in). Shrink `--twb-gutter` on
  mobile, or allow slight arrow overlap there, if larger mobile images are wanted.
- **Multi-slide jumps:** with the slide transition this is fine; the previous fade
  engine was the thing that glitched on big jumps.
- **Future CTA button:** the per-slide Button text/URL fields are stored but not
  rendered yet — wire them up when a slide CTA is needed.

---

## Related
- [Local Development Workflow](LOCAL_DEV_WORKFLOW.md) · [Design System Audit](DESIGN_SYSTEM_AUDIT.md) · [Component Architecture Audit](COMPONENT_ARCHITECTURE_AUDIT.md)
