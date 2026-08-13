# Live Migration Plan

How the rebuilt local site gets onto **https://travelwithoutborders.co.uk**.

This plan is specific to the one-off migration of the V2–V6 programme. For
routine releases afterwards use the
[Deployment Checklist](DEPLOYMENT_CHECKLIST.md).

**Analysis date:** 2026-08-13 (live admin inspected directly)
**Local backup fork point:** 2026-06-25

---

## 1. The core decision

**Do not import the local database over live.**

It is the obvious move and it would destroy live business data. Live holds
records the local copy does not:

| Live-only data | Live | Local | Why it matters |
| -------------- | ---- | ----- | -------------- |
| **Quform entries** | **193** (181 unread) | 191 | Customer enquiries. Personal data. |
| Redirection rules | full set | ~1 | SEO / inbound links |
| Slider Revolution modules | 12 | 1 | Only `homepage` is in the backup |
| Comments | 4 pending | — | |
| Post SMTP logs, Wordfence data | live | — | |

The migration is therefore **selective**: theme code, media, page content,
forms, and menus are pushed deliberately. Everything else on live is left
alone.

---

## 2. What the analysis found

### 2.1 Content has not diverged — this is the thing that makes it safe

The newest content edit on live across **all pages and posts** is
**2026-06-04** (Contact, id 4014). The local backup was taken **2026-06-25**.

Nothing on live has been edited since we forked. There is no three-way merge
and nothing to reconcile — *provided live stays frozen until cutover*.

### 2.2 Version parity is exact where it matters

| Component | Local | Live | |
| --------- | ----- | ---- | - |
| WPBakery Page Builder | 8.7.2 | **8.7.2** | ✅ page content is WPBakery shortcodes |
| Quform | 2.9.3 | **2.9.3** | ✅ form config is serialised Quform data |
| Ave Core / Ave theme | 2.9.2 | 2.9.2 | ✅ |
| Slider Revolution | 6.1.5 | 6.1.5 | ✅ |
| WordPress core | 7.0.x | 7.0.4 | ✅ |

Live runs newer versions of Yoast (28.2 vs 27.9), Redirection (5.9 vs 5.8),
Smush (4.3 vs 4.1.2), Post SMTP, Breeze and Really Simple Security. None of
those affect content or form transfer.

### 2.3 Media reconciles exactly

| | Count |
| - | ----- |
| Live attachments | 827 |
| Local attachments | 879 |
| **Difference** | **52** — matches the count added since the backup |

Of those 52: **34 are referenced** by page content and must be migrated;
**18 are unused** (hero placeholders superseded by real photos, duplicate
Augsburg shots, the replaced goalkeeper photo). Total new payload **21.2 MB**.

> Note: 16 of the 34 are only discoverable after `rawurldecode()`, because
> WPBakery `param_group` elements (hero carousel slides, testimonial cards)
> store attachment ids inside a URL-encoded JSON blob. A naive
> `image="123"` grep misses them — the first pass of this analysis did, and
> reported 15 instead of 34.

### 2.4 Scope of change

- **84 pages** modified locally since the backup
- **3 new pages** that do not exist on live: Testimonials (7254), Trade (7263),
  Trade Deutsch (7539)
- **1 page deleted locally** but still live: Groups (4596)
- **1 stray local draft** to exclude: id 7207 (untitled Special Interest)
- **169 local-domain URLs** embedded across 98 pages/posts — these need
  rewriting to the live domain

### 2.5 Forms

| | Live | Local |
| - | ---- | ----- |
| 1 — Tailor-made Holidays Form | ✅ (193 entries) | ✅ + marketing-consent fields |
| 2 — General Contact Form | ✅ | ✅ |
| 3 — Trade Enquiry — Business | **absent** | ✅ created 2026-07-17 |

Live form 1 still shows `Last modified 14/07/2020`, confirming it does not yet
carry the consent checkbox (element 8) or hidden status field (element 9).

### 2.6 Navigation

| Live | Local (target) |
| ---- | -------------- |
| Home, Destinations, Special Interest, Special Events, **Tailor-made Holidays**, **Groups**, Blog, Contact | Home, Destinations, Special Interest, Special Events, **Planning Bespoke Holidays**, **Testimonials**, **Trade**, Contact |

---

## 3. Risks and dependencies

### 3.1 Attachment-ID remapping — the main technical risk

Page content references images by id (`image="7544"`). Uploading those files to
live assigns **different** ids, so migrated pages would point at the wrong
image or none at all.

**Order matters:** upload media first, record the id mapping, rewrite the
content, *then* import it. The rewrite must cover both raw attributes and
URL-encoded `param_group` blobs (see 2.3).

### 3.2 One hardcoded id in the theme

Most hardcoded ids already match live and need no change:

| Location | ID | On live? |
| -------- | -- | -------- |
| `functions.php` `is_page( 4014 )` | Contact | ✅ |
| `functions.php` `is_page( 4476 )` | Special Interest | ✅ |
| `style.css` `page-id-4476 / 5433 / 5440` | Special Interest, Terms, Privacy | ✅ |
| **`inc/language-switch.php`** `array( 7263 => 7539 )` | Trade EN → DE | ❌ **local-only ids** |

`twb_language_switch_pairs()` **must** be updated to the real Trade page ids
once they exist on live, or the EN/DE switch will not render.

### 3.3 Freeze the pending plugin updates

Live is offering four updates. Two would break the parity this plan depends on:

- **WPBakery 8.7.2 → 9.0.1** — major version, marked *"Not tested"* with WP 7.0.4
- **Quform 2.9.3 → 2.23.1** — very large jump
- UpdraftPlus 1.26.6, Wordfence 9.0.0 (lower risk)

**Do not run these before the migration.** Schedule them afterwards, separately,
with a fresh backup.

### 3.4 Environment

- Live PHP is **7.4.33** — end of life, flagged in the dashboard. Not a blocker
  for this migration; raise with the host separately.
- WP Rocket and Breeze caching are active on live and must be cleared at cutover.
- Really Simple Security forces HTTPS — all migrated URLs must be `https://`.

---

## 4. Migration sequence

### Phase 0 — Safety

- [ ] UpdraftPlus: full backup (database **and** files), **downloaded off-server**
- [ ] Verify the backup actually restores (or at least that the archive opens)
- [ ] Agree a change freeze on live — no edits, no plugin updates
- [ ] Export live Quform entries as a second safety copy (personal data — store
      securely, **never** commit to Git)
- [ ] Note current live state for rollback: page count 88, media 827, entries 193

### Phase 1 — Theme code (low risk, independently reversible)

- [ ] Deploy `07_Source/Themes/ave-child/` from `main`
- [ ] Confirm the new files land: `inc/`, `assets/css/`, `assets/js/`
- [ ] Clear WP Rocket
- [ ] Smoke-test an unchanged page — nothing should look different yet

### Phase 2 — Media

- [ ] Upload the **34 referenced** new attachments
- [ ] Record the `local id → live id` mapping
- [ ] Decide on the 18 unused ones (recommend: skip)
- [ ] Confirm files land in the expected `uploads/YYYY/MM/` paths

### Phase 3 — Page content

- [ ] Rewrite the 84 pages' content: remap attachment ids, rewrite
      `travelwithoutborders.local` → `https://travelwithoutborders.co.uk`
- [ ] Import the modified pages (ids already align)
- [ ] Create the 3 new pages; **record their live ids**
- [ ] Remove/redirect the Groups page (4596)
- [ ] Exclude the stray draft 7207

### Phase 4 — Forms, menus, code follow-ups

- [ ] Patch live form 1 with the consent checkbox + hidden status field
- [ ] Create form 3 (Trade Enquiry — Business) and embed it on Contact
- [ ] Update the nav: rename Tailor-made → Planning Bespoke Holidays, drop
      Groups, add Testimonials and Trade
- [ ] **Update `twb_language_switch_pairs()`** with the live Trade page ids,
      commit, redeploy
- [ ] Check Yoast titles/descriptions and the Yoast indexables table for the
      renamed Wine and Dine page

### Phase 5 — Verify and release

- [ ] Clear WP Rocket **and** Breeze
- [ ] Walk the changed pages on desktop and mobile
- [ ] Submit a test enquiry on **both** contact forms; confirm the notification
      email states the marketing-consent outcome
- [ ] Confirm the EN/DE Trade switch works both ways
- [ ] Check no `.local` URLs survive in rendered HTML
- [ ] Confirm the 193 existing Quform entries are still present
- [ ] Confirm redirects and Slider Revolution modules are untouched
- [ ] Lift the freeze; record in [Release Notes](RELEASE_NOTES.md)

---

## 5. Rollback

If content lands badly, restore the Phase 0 UpdraftPlus database backup —
but note that any enquiries submitted between backup and rollback would be
lost, which is why the freeze and the separate entries export matter.

Theme-only problems can be reverted independently by redeploying the previous
child theme from Git, without touching the database.

See [Rollback Checklist](ROLLBACK_CHECKLIST.md).

---

## 6. Open decisions

1. **Access method.** With SSH/WP-CLI on the host this can be scripted end to
   end (media import returning ids, programmatic content rewrite, verifiable
   dry runs). Without it, it is a slower manual pass through wp-admin and the
   id remapping has to be done by hand — materially more error-prone.
2. **The Groups page.** Delete, or keep and 301 it to Planning Bespoke
   Holidays? A redirect is the safer SEO choice.
3. **Wine and Dine URL.** The page was renamed but the slug is still
   `/special-interest-holidays/fine-wine-and-dine/`. Changing it needs a 301;
   leaving it means the URL no longer matches the title.
4. **The 18 unused attachments** — skip, or upload for completeness?
5. **Staging.** If the host offers a staging environment, rehearse the whole
   sequence there first. Strongly recommended given the 84-page scope.
