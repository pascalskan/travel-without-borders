# Trade (Business-to-Business) Page

How to create the new **Trade** page by cloning the **Testimonials** page's
layout, stripping its content down to placeholders, and keeping the top/bottom
photos, the email section, and the footer. Also covers replacing **Blog** with
**Trade** in the main navigation.

> **Why this lives in a doc, not in code.** Every page on this site is built with
> **WPBakery**, and the page's content lives in the WordPress **database**
> (`post_content`) — not in this repo, which version-controls only the child
> theme and docs. The nav menu is a `wp_nav_menu` (Appearance → Menus), also in
> the DB. So the page is built in WP-admin, and this file is the reproducible,
> reviewable record of exactly what to do. See [Tech Stack](TECH_STACK.md) and
> the [Customisation Audit](CUSTOMISATION_AUDIT.md).

Related task: `A.S tasks.md` → *Business to Business page*.
Related: the trade **form** that will eventually live here is documented in
[Trade / Business Enquiry Form](TRADE_ENQUIRY_FORM.md).

---

## Objective

- A new page that **reuses the Testimonials page layout** exactly — the same
  band structure, spacing, top photo, bottom photo, the **email section above
  the footer**, and the **footer**.
- **No Testimonials content carried over:** remove the intro text, the trust
  stats, the featured testimonials carousel, and the testimonial cards/grid.
- Every content slot filled with **placeholder copy** for now (final content
  comes later, section by section).
- Nav: the **Blog** menu item is **replaced by "Trade"**, linking to this page.

Scope note: this is the layout shell only. The actual header/intro/contact
content and any B2B form embed are follow-up tasks the client (A.S) will guide.

---

## Before you start

- [ ] Do this on **staging/Local first** if available
      ([Local Dev Workflow](LOCAL_DEV_WORKFLOW.md)), then repeat on production.
- [ ] Know which page is the current **Testimonials** page (Pages → Testimonials).
- [ ] Clear the **WP Rocket** cache after publishing and after the menu change.

---

## Step 1 — Clone the Testimonials page

Use the installed **Yoast Duplicate Post** plugin so the entire WPBakery layout
(rows, the top/bottom photos, the email section and footer context) is copied
exactly — nothing is hand-rebuilt.

1. WP-admin → **Pages**.
2. Hover the **Testimonials** page → **Clone** (or **New Draft**). This creates a
   draft copy with identical WPBakery content.
3. Open the clone for editing.

---

## Step 2 — Rename and set the slug

1. **Title:** `Trade`.
2. **Permalink / slug:** `trade` → final URL `…/trade/`.
3. Leave as **Draft** until content is stripped and placeholders are in
   (Step 3–4), then **Publish**.

---

## Step 3 — Keep vs strip

Edit the cloned page in WPBakery. Work top to bottom:

| Section (from Testimonials layout) | Action |
| ---------------------------------- | ------ |
| **Top photo / banner** (top of page) | **KEEP** — unchanged |
| Page hero heading + intro text | **STRIP** → replace with placeholder heading + paragraph (Step 4) |
| Trust Band / stats (e.g. "40+ years…") | **STRIP** → replace with placeholder stat row or remove |
| Featured testimonials carousel (`[twb_testimonials…]`) | **REMOVE** the element entirely |
| Testimonials grid / cards | **REMOVE** the element/row entirely |
| Any "read all reviews" / testimonial CTA | **STRIP** → placeholder CTA or remove |
| **Email section** (above the footer) | **KEEP** — unchanged, complete |
| **Bottom photo** (bottom of page) | **KEEP** — unchanged |
| **Footer** | **KEEP** — it is global anyway |

> If a band becomes empty after removing a testimonials element, keep the **row**
> (so the page's band rhythm/spacing is preserved) and drop a placeholder Text
> block into it. This keeps the "split-band grammar" intact.

---

## Step 4 — Placeholder copy

Drop these into the stripped slots. They are deliberately generic and clearly
provisional — replace when A.S provides final content.

**Hero heading:**
```
Trade & Partnerships — [placeholder heading]
```

**Hero intro paragraph:**
```
[Placeholder intro] A short introduction to Travel Without Borders for trade and
business partners will go here — who we work with and how to partner with us.
Final copy to be provided.
```

**Where the trust band was (optional placeholder stat row):**
```
[Stat 1]   ·   [Stat 2]   ·   [Stat 3]
```

**Where the featured/grid testimonials were (placeholder body block):**
```
[Placeholder section]
Main body content for the Trade page will go here. This block is a placeholder
standing in for the removed testimonials content and preserves the page's band
layout until final content is added.
```

**Placeholder CTA (if a CTA band is kept):**
```
[Placeholder call to action]
```

---

## Step 5 — Navigation: replace "Blog" with "Trade"

The main menu is a `wp_nav_menu`, managed in the DB.

1. WP-admin → **Appearance → Menus**.
2. Select the **primary/main menu** (the one with Home / Destinations / Special
   Interest / Special Events / Tailor-Made / Groups / **Blog**).
3. **Remove** the **Blog** menu item (or repoint it — see note).
4. Add the new **Trade** page (Pages panel → tick **Trade** → **Add to Menu**),
   drag it into **Blog's former position**, label it **`Trade`**.
5. **Save Menu.**

> Note: removing the Blog item only removes it from the menu; the Blog page/posts
> still exist. If the client wants Blog fully retired later, handle redirects
> separately (Redirection plugin) — out of scope here.

---

## Test checklist

- [ ] `…/trade/` loads with the Testimonials layout: top photo, bands, email
      section, bottom photo, footer all present.
- [ ] No testimonial quotes, cards, carousels, or trust-stat figures remain.
- [ ] Placeholder heading/intro/body render in the right bands; spacing/band
      alternation is unchanged from Testimonials.
- [ ] Main nav shows **Trade** (not Blog) in the same position, linking to
      `…/trade/`.
- [ ] Email section above the footer works exactly as on other pages.
- [ ] Mobile: bands stack the same way the Testimonials page does.
- [ ] WP Rocket cache cleared.

---

## Rollback

- Delete/trash the **Trade** page draft (the original Testimonials page is
  untouched — it was cloned, not moved).
- Re-add the **Blog** item to the menu and Save to restore the previous nav.
