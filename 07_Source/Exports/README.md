# Exports

Exported **plugin and configuration data** that lives in the WordPress database
but must be preserved, reviewed, and re-importable across migrations.

Each subfolder holds the export for one system. Store the export files the plugin
produces, plus a short note recording when and how they were exported.

## What belongs here

| Subfolder | Holds |
| --------- | ----- |
| [Quform/](Quform/README.md) | Form definitions and the 190 live enquiry entries |
| [Slider-Revolution/](Slider-Revolution/README.md) | The 12 slider modules |
| [Yoast/](Yoast/README.md) | Per-page SEO metadata and settings export |
| [Redirects/](Redirects/README.md) | Redirection rules export |

## What does not belong here

- Live database dumps of the whole site — those are **backups**
  (`00_Backups/`), not selective exports.
- Custom code (that lives in `Themes/`, `CSS/`, `JavaScript/`, `Tracking/`).
- Any personal data beyond what is required to preserve and restore the export —
  handle enquiry data per data-protection obligations.

> These exports protect business-critical data identified in the
> [Customisation Audit](../../01_Documentation/CUSTOMISATION_AUDIT.md). Capture
> them **before** any migration or plugin deactivation.
