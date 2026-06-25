# Decision Log

A record of significant decisions, the context behind them, and their
consequences. Newest first. Keep entries short and factual.

---

## 2026-06-25 — Backup-first, child-theme-only workflow

**Context:** The site is live and built on the Ave theme with WPBakery. Direct
edits to the theme or plugins would be lost on update and risk the live site.

**Decision:** All custom work is delivered through an Ave child theme (or
`03_Development/`), and no change reaches production without a current, verified
backup.

**Consequences:** Updates remain safe; recovery is always possible. Adds a
child-theme setup step in Phase 2.

---

## 2026-06-25 — Target PHP 7.4

**Context:** The live host runs PHP 7.4.33, which is end-of-life.

**Decision:** All custom code must be PHP 7.4-compatible until the host is
upgraded.

**Consequences:** Newer PHP 8 syntax/features are off-limits for now. Revisit if
the host is upgraded.

---

## 2026-06-25 — Backups excluded from Git

**Context:** UpdraftPlus backup archives are large binary files.

**Decision:** Exclude `00_Backups/*` from Git (keeping only `.gitkeep`); retain
archives locally.

**Consequences:** Repository stays lightweight; backups must be managed and
stored outside version control. See [Backup Strategy](BACKUP_STRATEGY.md).
