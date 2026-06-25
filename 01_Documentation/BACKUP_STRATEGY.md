# Backup Strategy

How backups are created, stored, retained, and used to recover the Travel
Without Borders website.

## Principles

1. **Backup first.** No change reaches production without a current, verified
   backup.
2. **Backups are never committed to Git.** They are large and are retained
   locally only (see [.gitignore](../.gitignore)).
3. **The pre-development baseline is permanent.** It is the primary recovery
   point for the entire project.

## Tooling

- **UpdraftPlus (Free)** is used to create full-site backups.
- A backup consists of five archives plus restore notes:
  database, plugins, themes, uploads, and other WordPress files.

## Storage & Structure

Backups live under `00_Backups/`, one dated folder per backup:

```text
00_Backups/
├── .gitkeep                       # keeps the folder in Git (only tracked file)
└── 2026-06-25_Pre-Development/    # permanent baseline
    ├── *-db.gz
    ├── *-plugins.zip
    ├── *-themes.zip
    ├── *-uploads.zip
    ├── *-others.zip
    └── RestoreNotes.md
```

The `.gitignore` rule keeps the folder structure in Git while excluding the
archives themselves:

```gitignore
00_Backups/*
!00_Backups/.gitkeep
```

> Do **not** change this ignore behaviour.

## Retention

| Backup | Retention |
| ------ | --------- |
| Pre-development baseline | Permanent |
| Pre-phase / pre-deployment backups | Until the phase is verified live, then archive |
| Routine backups | Rolling, per host policy |

## Recovery

To restore a backup, follow the restore notes stored beside it — for the
baseline see
[RestoreNotes.md](../00_Backups/2026-06-25_Pre-Development/RestoreNotes.md).
Summary:

1. Install/activate UpdraftPlus on the target site.
2. Upload all five archives.
3. Restore database, plugins, themes, uploads, and others.
4. Verify the site against baseline screenshots.

For deployment-time recovery, see the
[Rollback Checklist](../05_Deployment/ROLLBACK_CHECKLIST.md).
