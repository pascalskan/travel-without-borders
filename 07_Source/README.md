# 07_Source

The home for **custom source assets** — the things this project authors or owns
and must be able to reproduce, review, and safely re-apply to the WordPress site.

## Repository philosophy

This repository is **not** a copy of the WordPress installation. It is the
source of truth for the *custom* layer that sits on top of WordPress: bespoke
theme code, custom CSS/JS, tracking code, and exports of business-critical data.

The guiding rule:

> **If we wrote it, configured it by hand, or would lose it in a rebuild — it is
> tracked here. If WordPress, a theme, or a plugin generates and owns it — it is
> not.**

## Why only custom assets are version controlled

- **WordPress core, themes, and plugins** are third-party products with their own
  release channels. Committing them adds gigabytes, leaks licensed code, and
  creates merge noise we gain nothing from. They are restored from their vendors
  or from a backup, not from Git.
- **Database-held configuration** (Ave Theme Options, WPBakery page content,
  Customizer settings) cannot be meaningfully version-controlled as files. The
  authoritative copy of that state is the **verified backup**
  (see [Backup Strategy](../01_Documentation/BACKUP_STRATEGY.md)).
- **Custom code and exported data** are small, ours, and fragile across
  migrations. These belong in Git so they are reviewable and recoverable.

## Relationship to WordPress

```text
Live WordPress site
├── core / themes / plugins      → vendor-owned, NOT in this repo
├── database configuration       → captured by backups, NOT file-tracked
└── custom layer                 → authored/exported here in 07_Source/
        ├── child theme code      → Themes/
        ├── custom CSS / JS       → CSS/, JavaScript/
        ├── tracking snippets     → Tracking/
        └── exported plugin data  → Exports/
```

Code placed here is **staged source**, not a live deployment. Applying it to the
site follows the [Workflow](../01_Documentation/WORKFLOW.md) and
[Coding Standards](../01_Documentation/CODING_STANDARDS.md).

## Structure

| Folder | Holds |
| ------ | ----- |
| `Themes/` | Custom/child theme source (e.g. Ave Child) |
| `Plugins/` | Any genuinely custom plugin source (none today) |
| `CSS/` | Hand-written custom stylesheets |
| `JavaScript/` | Hand-written custom scripts |
| `Tracking/` | Tracking/analytics snippets (e.g. GTM) |
| `Exports/` | Exported plugin data (Quform, Slider Revolution, Yoast, Redirects) |
| `Inventory/` | The [Asset Register](Inventory/ASSET_REGISTER.md) and related inventories |

> Supersedes the former `03_Development/` folder, which is retired so there is a
> single home for custom code.
