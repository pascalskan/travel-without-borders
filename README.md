# Travel Without Borders

Redevelopment and maintenance of the **Travel Without Borders** WordPress website
([travelwithoutborders.co.uk](https://travelwithoutborders.co.uk)).

This repository is the single source of truth for the project's documentation,
custom assets, custom code, testing evidence, and deployment process. It does
**not** contain the full WordPress installation — only the artefacts needed to
plan, build, test, and safely ship changes to the live site.

---

## Overview

The live site runs on WordPress with the Ave theme and the WPBakery page
builder. The goal of this project is to modernise and optimise the site
incrementally — without risking the production environment — by working through
custom code in a child theme, validating every change against captured
baselines, and keeping a complete, verified backup as a permanent recovery
point.

## Objectives

- Redesign the homepage and testimonials sections.
- Improve performance, accessibility, and mobile experience.
- Introduce a maintainable child-theme-based workflow for all custom code.
- Keep the live site recoverable at every step via verified backups.
- Document every decision so another developer can pick up the work cold.

## Technology Stack

| Layer         | Technology              |
| ------------- | ----------------------- |
| CMS           | WordPress 7.0           |
| Theme         | Ave (+ child theme)     |
| Page builder  | WPBakery / Visual Composer |
| Language       | PHP 7.4.33             |
| Backups       | UpdraftPlus (Free)      |

See [TECH_STACK.md](01_Documentation/TECH_STACK.md) for the full plugin
inventory and version details.

## Repository Structure

```text
travel-without-borders/
├── 00_Backups/        # Full site backups (git-ignored, retained locally)
├── 01_Documentation/  # Project documentation
├── 02_Assets/         # Brand/media assets (logos, icons, images)
├── 04_Testing/        # Baseline & comparison screenshots, test evidence
├── 05_Deployment/     # Deployment, release, and rollback checklists
├── 06_References/     # Audit reports, client notes, vendor documentation
├── 07_Source/         # Custom source: themes, CSS, JS, tracking, exports
├── CHANGELOG.md       # Keep a Changelog history
├── PROJECT_STATUS.md  # Project dashboard
├── CONTRIBUTING.md    # How to work in this repository
└── README.md          # This file
```

## Workflow

The project follows a strict, backup-first workflow for every change:

1. **Backup** — confirm a current, verified backup exists.
2. **Research** — investigate the change against audits and baselines.
3. **Develop** — implement via the child theme / custom code.
4. **Test** — compare against baseline screenshots and checks.
5. **Deploy** — ship using the deployment checklist.

The full process is documented in [WORKFLOW.md](01_Documentation/WORKFLOW.md).

## Current Status

**Phase 0 (Discovery & Recovery) complete. Development not yet started.**

- ✅ Website, plugin, mobile, and customisation audits complete
- ✅ GitHub repository created and structured
- ✅ Baseline screenshots captured
- ✅ Full UpdraftPlus backup created and verified
- ⏳ Local development environment — pending

The live, full project dashboard lives in
[PROJECT_STATUS.md](PROJECT_STATUS.md).

## Getting Started

This repository documents and supports the project; it is not a runnable
application. To work on it:

1. **Clone the repository.**
   ```bash
   git clone <repository-url>
   cd travel-without-borders
   ```
2. **Read the documentation**, starting with the
   [Documentation Index](01_Documentation/INDEX.md).
3. **Confirm a verified backup exists** under `00_Backups/` before any work
   that could affect the live site (see
   [BACKUP_STRATEGY.md](01_Documentation/BACKUP_STRATEGY.md)).
4. **Review the workflow and standards** before making changes
   ([CONTRIBUTING.md](CONTRIBUTING.md),
   [CODING_STANDARDS.md](01_Documentation/CODING_STANDARDS.md)).

> **Note:** `00_Backups/` is intentionally excluded from Git. Backup archives
> are large and are retained locally only. Never commit them.

## Documentation Index

The complete, organised list of documents is maintained in
**[01_Documentation/INDEX.md](01_Documentation/INDEX.md)**.

Key documents:

| Document | Purpose |
| -------- | ------- |
| [Project Overview](01_Documentation/PROJECT_OVERVIEW.md) | Goals, scope, and context |
| [Tech Stack](01_Documentation/TECH_STACK.md) | Platform, plugins, versions |
| [Customisation Audit](01_Documentation/CUSTOMISATION_AUDIT.md) | How the site is customised; risks & safe areas |
| [Roadmap](01_Documentation/ROADMAP.md) | Planned phases and milestones |
| [Workflow](01_Documentation/WORKFLOW.md) | Day-to-day development process |
| [Coding Standards](01_Documentation/CODING_STANDARDS.md) | Code conventions |
| [Backup Strategy](01_Documentation/BACKUP_STRATEGY.md) | Backup & recovery policy |
| [Decisions](01_Documentation/DECISIONS.md) | Key decision log |
| [Development Log](01_Documentation/DEVELOPMENT_LOG.md) | Running work log |
| [Project Status](PROJECT_STATUS.md) | Live progress dashboard |
| [Changelog](CHANGELOG.md) | Version history |
