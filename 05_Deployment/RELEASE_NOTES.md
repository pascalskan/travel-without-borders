# Release Notes

A record of what was deployed to the live site and when. Add a new entry per
deployment, newest first. For the full change history see the
[Changelog](../CHANGELOG.md).

## Template

```markdown
## YYYY-MM-DD — <short title>

**Deployed by:** <name>
**Backup taken:** <yes/no — location>

### Changes
- ...

### Verification
- ...

### Notes / Issues
- ...
```

---

> **Note.** Not everything on production arrived through a deployment. A large
> amount of work between 15 and 23 August 2026 was typed straight into wp-admin
> at the client's direction. That is recorded separately in
> [Live Site Changes](LIVE_SITE_CHANGES.md), which also lists what still has to
> be reconciled before the next deploy.

## 2026-08-25 — V8/V9 content release

**Deployed by:** Claude, via wp-admin (Chrome extension), with the client
**Backup taken:** WordPress revisions per page/post; live theme verified against
the repository before replacement

### Changes

**Child theme** — `ave-child` replaced via Appearance → Themes → Upload,
adding the 10 files live was missing: `blog.css`, `content-layout.css`,
`hero-colours.css`, `outbound-tracking.{php,js}`, `inc/blog.php` and the four
blog templates. Checked first that the repository was a strict superset, so
nothing on live was lost.

**Pages** — Augsburg, and the five Special Interest pages (Colditz, Eagle's
Nest, Royal Heritage, Fairy Tale Castles, Motorcar). 15 new media files
uploaded (11 Augsburg, 4 Motorcar).

**Posts** — all eight blog posts plus the index.

**Reconciliation** — the duplicate hero-colours block was removed from
Customizer → Additional CSS, now that `hero-colours.css` ships with the theme.
The `.hideMob` rules sharing that block were preserved.

### Verification
- Every page and post compared against local: text, headings, images, links,
  row backgrounds and rendered height.
- All five Special Interest pages and Augsburg: **0px height difference**.
- Seven of eight posts: article text **100% identical**; the eighth matched
  once its publish date was aligned (see below).
- Blog index: same eight posts, same order, no dates.
- Zero broken images across all deployed pages.

### Notes / Issues
- **The Post Style dropdown displays a theme default, not the post's stored
  value.** Saving a post therefore writes `cover-spaced` in and flips it to a
  cover layout. This silently changed one post's layout before it was spotted;
  the dropdown must be cleared before saving any post that should use the
  default. Local was correct throughout — live was the side that drifted.
- **`post_modified` cannot be set from wp-admin.** WordPress stamps it on every
  save, which made post 5247 show an "Updated" line that local did not have.
  Resolved by publishing it on the current date so both fall on the same day.
- `img_size="large"` resolved differently per environment (local strips
  registered sizes, live had generated them), so Augsburg's map was being served
  at 682px instead of 1023px. Both sides now say `img_size="full"`, removing the
  environment dependency.
- Attachment ids were remapped **by filename**: the Augsburg sights map and
  representative panel came back from upload in the opposite order to their
  local ids, so sequential mapping would have swapped two images.

---

## 2026-08-13 — Initial live migration

**Deployed by:** Claude, with the client
**Backup taken:** yes — UpdraftPlus database backup, per Phase 0 of the plan

### Changes
- First push of the rebuild to production, executed per the
  [Live Migration Plan](LIVE_MIGRATION_PLAN.md).
- Destination pages migrated in batches.
- The "504" page unpublished — it was not meant to exist any more. Unpublished
  rather than deleted, so it stays recoverable.
- New pages created as drafts first, confirmed, then published.

### Verification
- Page-by-page check against the local rebuild as each batch landed.

### Notes / Issues
- From this date the live site and the local rebuild are both working copies.
  Divergence between them is now possible and has to be tracked deliberately —
  hence [Live Site Changes](LIVE_SITE_CHANGES.md).
- The local work completed since 20 August 2026 (blogs, Special Interest pages,
  Augsburg) has **not** been deployed. The full manifest is in
  [Pending Deployment](PENDING_DEPLOYMENT.md).

---

_Earlier: the live site was unchanged from the 2026-06-25 pre-development
baseline until the migration above._
