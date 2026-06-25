# Tracking

Source for **tracking and analytics snippets** used on the site.

## What belongs here

- The **Google Tag Manager** snippet currently hardcoded in the Ave Child
  `header.php` (`GTM-TCZM2CR`), kept here as reviewable source so it can be
  re-applied deliberately after any template change.
- Notes on what each container loads:
  - Universal Analytics `UA-135755883-1` (deprecated)
  - Google Analytics 4 `G-484B6GT6CW`
- Any future tracking/consent snippets.

## What does not belong here

- Tracking IDs as live secrets — these are public client-side IDs, but treat any
  credentials (e.g. API keys) as out of scope and never commit them.
- Vendor library code (GA/GTM scripts are loaded remotely, not stored here).

> Tracking is currently **not** managed by a plugin or theme option — losing the
> `header.php` snippet on a theme change would silently break analytics. See the
> Asset Audit in the
> [Customisation Audit](../../01_Documentation/CUSTOMISATION_AUDIT.md) and the
> IDs recorded in [TECH_STACK.md](../../01_Documentation/TECH_STACK.md).
