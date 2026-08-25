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
