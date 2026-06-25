# Redirects Export

## What belongs here

- Exported **Redirection plugin rules** (301s and any other managed redirects).
- A short note recording the export date, plugin version, and method.

## What does not belong here

- The Redirection plugin itself (vendor product).
- Server-level redirects (`.htaccess`, Nginx config) — note their existence here
  if relevant, but they are managed at the host, not in this plugin export.

> The audit flagged the Redirection **database as pending upgrade**; resolve that
> and export the rules so they can be re-imported before go-live, otherwise links
> and SEO break. See the
> [Customisation Audit](../../../01_Documentation/CUSTOMISATION_AUDIT.md).
