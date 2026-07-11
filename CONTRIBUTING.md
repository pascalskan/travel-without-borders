# Contributing

How to work in the Travel Without Borders repository. Read this before making
any change.

## Before You Start

1. Read the [Documentation Index](01_Documentation/INDEX.md) and
   [Project Overview](01_Documentation/PROJECT_OVERVIEW.md).
2. Confirm a current, verified backup exists
   ([Backup Strategy](01_Documentation/BACKUP_STRATEGY.md)).
3. Review the [Workflow](01_Documentation/WORKFLOW.md) and
   [Coding Standards](01_Documentation/CODING_STANDARDS.md).
4. Review the [Local Development Workflow](01_Documentation/LOCAL_DEV_WORKFLOW.md)
   — how the child theme is version-controlled and linked into LocalWP.

## Ground Rules

- **Edit the child theme in the repo** at `07_Source/Themes/ave-child/`; LocalWP
  runs it through a junction (see
  [Local Development Workflow](01_Documentation/LOCAL_DEV_WORKFLOW.md)).
- **Never edit the parent theme or plugins** — use the Ave child theme.
- **Never commit backups** — `00_Backups/` is git-ignored by design.
- **Keep custom code PHP 7.4-compatible.**
- **Document as you go** — update the relevant docs in the same change.

## Where Things Go

| Content | Location |
| ------- | -------- |
| Documentation | `01_Documentation/` |
| Brand/media assets (logos, icons, images) | `02_Assets/` |
| Custom source (child theme, CSS, JS, tracking, exports) | `07_Source/` |
| Test evidence / screenshots | `04_Testing/` |
| Deployment & rollback docs | `05_Deployment/` |
| Audits, client notes, vendor docs | `06_References/` |
| Backups (local only) | `00_Backups/` |

## Commits & Branches

- Work on a branch per change; keep commits small and descriptive.
- Reference the relevant phase where useful.
- Update the [Changelog](CHANGELOG.md), [Development Log](01_Documentation/DEVELOPMENT_LOG.md),
  and [Project Status](PROJECT_STATUS.md) as part of the work.

## Markdown Conventions

- One `# H1` title per document, followed by a short description.
- Use tables, checklists, and fenced code blocks where they aid clarity.
- Link between documents with relative paths.
