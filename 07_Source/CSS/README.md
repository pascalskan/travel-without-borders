# CSS

Hand-written **custom stylesheets** that we author and maintain.

## What belongs here

- Custom CSS authored for the site, intended to be enqueued via the child theme.
- Consolidated versions of CSS currently held in admin-only fields, brought into
  version control:
  - Ave Theme Options custom CSS (~16 lines targeting `.main-nav`, `.fancy-box-tour`)
  - Customizer Additional CSS (5 lines, `.hideMob` utility)

## What does not belong here

- **WPBakery-generated inline CSS** (`vc_custom_*` blocks) — machine-generated,
  per-page, and held in the database; it cannot be meaningfully file-tracked.
- Theme/plugin stylesheets shipped by vendors.
- Compiled output from a build step (track the source, not the build).

> See the CSS Audit in the
> [Customisation Audit](../../01_Documentation/CUSTOMISATION_AUDIT.md) and the
> [Coding Standards](../../01_Documentation/CODING_STANDARDS.md).
