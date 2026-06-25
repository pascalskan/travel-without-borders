# Project Status Dashboard

A living overview of progress on the Travel Without Borders redevelopment.

**Last updated:** 2026-06-25
**Current phase:** Phase 0 — Discovery & Recovery (complete)
**Development started:** No

---

## At a Glance

| Phase | Description | Status |
| ----- | ----------- | ------ |
| 0 | Discovery & Recovery | ✅ Complete |
| 1 | Repository Housekeeping | 🟡 In progress |
| 2 | Local Environment & Child Theme | 🟡 In progress |
| 3 | Homepage Redesign | ⬜ Not started |
| 4 | Testimonials Redesign | ⬜ Not started |
| 5 | Optimisation (performance, mobile, a11y) | ⬜ Not started |
| 6 | Deployment | ⬜ Not started |

Legend: ✅ complete · 🟡 in progress · ⬜ not started

---

## Phase 0 — Discovery & Recovery ✅

### Discovery

- [x] Website audit
- [x] Plugin audit (~18–19 active — count to re-verify)
- [x] Mobile audit
- [x] Theme investigation
- [x] Customisation audit (theme, plugins, WPBakery, CSS/JS/PHP, tracking)

### Recovery

- [x] GitHub repository created
- [x] Baseline screenshots captured
- [x] Full UpdraftPlus backup created
- [x] Backup verified
- [x] Backup excluded from Git

---

## Phase 1 — Repository Housekeeping 🟡

- [x] Standardise folder naming
- [x] Rewrite README, CHANGELOG, PROJECT_STATUS
- [x] Add core documentation (tech stack, workflow, standards, backup, roadmap, decisions)
- [x] Add customisation audit
- [x] Create documentation index
- [ ] Final review and commit

---

## Phase 2 — Local Environment & Child Theme 🟡

### 2.1 Repository preparation for production assets ✅

- [x] Create the `07_Source/` source structure (Themes, Plugins, CSS, JavaScript, Tracking, Exports, Inventory)
- [x] Add a README to every source folder (what belongs / what does not)
- [x] Create the [Asset Register](07_Source/Inventory/ASSET_REGISTER.md) from the customisation audit
- [x] Retire `03_Development/`; make `07_Source/` the single home for custom code
- [x] Repoint all documentation references to `07_Source/`

### 2.2 Automated asset acquisition from backup ✅

- [x] Search the entire extracted backup (themes, plugins, uploads, SQL, configs)
- [x] Import the Ave Child theme to `07_Source/Themes/ave-child/` and review it
- [x] Extract custom CSS (Theme Options + Customizer) to `07_Source/CSS/`
- [x] Extract the GTM snippet to `07_Source/Tracking/`
- [x] Extract the redirect rule(s) found in the SQL to `07_Source/Exports/Redirects/`
- [x] Import brand logos and custom SVG icons to `02_Assets/`
- [x] Inventory uploads/media and database data (see [Acquisition Report](07_Source/Inventory/ACQUISITION_REPORT.md))
- [x] Update the [Asset Register](07_Source/Inventory/ASSET_REGISTER.md)

### 2.3 Local environment & live-only data ⬜

- [ ] Set up local WordPress environment
- [ ] Mirror plugins and theme locally
- [ ] **Re-export from live** (backup is incomplete): Quform entries, Slider Revolution modules, full Redirection set, Yoast config
- [ ] Verify local matches production

---

## Phase 3 — Homepage Redesign ⬜

- [ ] Define design goals
- [ ] Implement via child theme / WPBakery
- [ ] Compare against baseline screenshots
- [ ] Client review

---

## Phase 4 — Testimonials Redesign ⬜

- [ ] Define design goals
- [ ] Implement
- [ ] Test
- [ ] Client review

---

## Phase 5 — Optimisation ⬜

- [ ] Performance (caching, assets, images)
- [ ] Mobile responsiveness
- [ ] Accessibility
- [ ] Re-test against baselines

---

## Phase 6 — Deployment ⬜

- [ ] Pre-deployment backup
- [ ] Follow deployment checklist
- [ ] Post-deployment verification
- [ ] Update release notes

---

See the [Roadmap](01_Documentation/ROADMAP.md) for phase detail and the
[Changelog](CHANGELOG.md) for completed history.
