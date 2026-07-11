# Documentation Index

Central index of all documentation in this repository.

## Project

| Document | Description |
| -------- | ----------- |
| [Project Overview](PROJECT_OVERVIEW.md) | Goals, scope, and context |
| [Tech Stack](TECH_STACK.md) | Platform, theme, plugins, and versions |
| [Customisation Audit](CUSTOMISATION_AUDIT.md) | How the site is customised; technical debt, risks, safe areas |
| [Design System Audit](DESIGN_SYSTEM_AUDIT.md) | Current visual language: typography, colour, spacing, components — reference for all future design work |
| [UX & Information Architecture Audit](UX_INFORMATION_ARCHITECTURE_AUDIT.md) | User journey, IA/sitemap, trust, CTAs, content gaps, prioritised roadmap — strategic reference |
| [Component Architecture Audit](COMPONENT_ARCHITECTURE_AUDIT.md) | Reusable component inventory, future library, WPBakery/data strategy, dev standards — architectural reference |
| [Testimonials System Specification](TESTIMONIALS_SYSTEM_SPECIFICATION.md) | Complete implementation spec for the Testimonials feature — single source of truth for the build |
| [Testimonials Implementation Plan](TESTIMONIALS_IMPLEMENTATION_PLAN.md) | Milestones, dependencies, risks, testing, Git workflow & release checklist for building Testimonials |
| [Roadmap](ROADMAP.md) | Planned phases and milestones |
| [Decisions](DECISIONS.md) | Log of significant project decisions |

## Architecture Decision Records

| Document | Description |
| -------- | ----------- |
| [ADR-001 — Testimonials Architecture](Architecture/ADR-001-Testimonials-Architecture.md) | Why CPT, shared taxonomies, WPBakery elements, Flickity reuse, `filemtime`, shared renderer, contextual queries, and the loader — with rejected alternatives |

## Implemented Features

| Document | Description |
| -------- | ----------- |
| [Homepage Hero Carousel](HERO_CAROUSEL.md) | The `twb_hero_carousel` slideshow: config, files, and key gotchas (e.g. why `flickity-fade` is dequeued and slide mode is used) |
| [Testimonials Component](TESTIMONIALS.md) | The `twb_testimonials` carousel: WPBakery params, files, behaviour, and why CPT migration is recorded as a future enhancement |

## Process

| Document | Description |
| -------- | ----------- |
| [Workflow](WORKFLOW.md) | Step-by-step development process |
| [Local Development Workflow](LOCAL_DEV_WORKFLOW.md) | How the child theme is version-controlled and linked into LocalWP |
| [Coding Standards](CODING_STANDARDS.md) | Code conventions for custom work |
| [Backup Strategy](BACKUP_STRATEGY.md) | Backup, retention, and recovery policy |
| [Contributing](../CONTRIBUTING.md) | How to work in this repository |

## Tracking

| Document | Description |
| -------- | ----------- |
| [Project Status](../PROJECT_STATUS.md) | Live progress dashboard |
| [Changelog](../CHANGELOG.md) | Dated history of changes |
| [Development Log](DEVELOPMENT_LOG.md) | Running session-by-session work log |

## Deployment

| Document | Description |
| -------- | ----------- |
| [Deployment Checklist](../05_Deployment/DEPLOYMENT_CHECKLIST.md) | Pre/post deployment steps |
| [Rollback Checklist](../05_Deployment/ROLLBACK_CHECKLIST.md) | Emergency recovery steps |
| [Release Notes](../05_Deployment/RELEASE_NOTES.md) | Notes per release |
| [Local Setup](../05_Deployment/LOCAL_SETUP.md) | Reconstruct the site locally (LocalWP) |
| [Restore Checklist](../05_Deployment/RESTORE_CHECKLIST.md) | Ordered local restore steps + WP-CLI commands |
| [Post-Restore Checklist](../05_Deployment/POST_RESTORE_CHECKLIST.md) | Verify the local restore |
| [Known Local Differences](../05_Deployment/KNOWN_LOCAL_DIFFERENCES.md) | Expected local vs production differences |

## Reference

| Location | Description |
| -------- | ----------- |
| [Source overview](../07_Source/README.md) | Repository philosophy; what is and isn't version controlled |
| [Asset Register](../07_Source/Inventory/ASSET_REGISTER.md) | Custom assets: location, VC status, export status |
| [Restore Notes](../00_Backups/2026-06-25_Pre-Development/RestoreNotes.md) | How to restore the baseline backup |
| `06_References/` | Audit reports, client notes, vendor docs |
