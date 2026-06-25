# Coding Standards

Conventions for all custom code added to the Travel Without Borders project.
The goal is code that is safe for production, compatible with the live
environment, and easy for another developer to maintain.

## Golden Rules

1. **Child theme only.** Never edit the parent Ave theme or any plugin. All
   customisation goes in the Ave child theme or under `07_Source/`.
2. **PHP 7.4 compatible.** The live host runs PHP 7.4.33. Do not use syntax or
   features newer than PHP 7.4.
3. **No destructive changes without a backup.** See [Workflow](WORKFLOW.md).

## PHP

- Follow the
  [WordPress PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/).
- Prefix custom functions to avoid collisions (e.g. `twb_`).
- Enqueue scripts and styles via `wp_enqueue_script` / `wp_enqueue_style` — never
  hard-code them into templates.
- Escape output and sanitise input.

## CSS

- Keep custom CSS in dedicated child-theme stylesheets under `07_Source/CSS/`.
- Prefer specific, well-scoped selectors over `!important`.
- Group and comment rules by section/component.

## JavaScript

- Keep custom scripts under `07_Source/JavaScript/`.
- Enqueue properly with dependencies declared.
- Avoid inline scripts; avoid global namespace pollution.

## Files & Naming

- Use clear, descriptive file names.
- Keep brand/media assets in `02_Assets/` and custom source in `07_Source/`.
- One concern per file where practical.

## Documentation

- Document non-obvious decisions in [DECISIONS.md](DECISIONS.md).
- Update the [Development Log](DEVELOPMENT_LOG.md) and
  [Changelog](../CHANGELOG.md) as part of the work.
