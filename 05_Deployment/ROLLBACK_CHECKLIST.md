# Rollback Checklist

Emergency steps to return the live site to a known-good state if a deployment
causes problems.

## When to Roll Back

- The site is broken or significantly degraded after a deployment.
- A critical feature (forms, navigation, key pages) is failing.
- The cause cannot be fixed quickly and safely in place.

## Quick Rollback (custom code)

- [ ] Revert the child-theme / custom-code change (Git revert or remove files).
- [ ] Clear caches (WP Rocket).
- [ ] Verify the site against baseline screenshots.

## Full Restore (from backup)

Use when a quick revert is not enough.

- [ ] Identify the correct backup under `00_Backups/` (most recent good state,
      or the permanent baseline `2026-06-25_Pre-Development/`).
- [ ] Follow the restore notes beside the backup
      ([RestoreNotes.md](../00_Backups/2026-06-25_Pre-Development/RestoreNotes.md)).
- [ ] In UpdraftPlus, restore database, plugins, themes, uploads, and others as
      required.
- [ ] Clear caches.
- [ ] Verify the site against baseline screenshots.

## After Rollback

- [ ] Confirm the site is fully functional (desktop + mobile).
- [ ] Record what happened in [Release Notes](RELEASE_NOTES.md) and the
      [Development Log](../01_Documentation/DEVELOPMENT_LOG.md).
- [ ] Diagnose the root cause before re-attempting the deployment.
