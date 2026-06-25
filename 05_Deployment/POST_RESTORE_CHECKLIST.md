# Post-Restore Checklist

Verification to run **after** the local restore (see
[RESTORE_CHECKLIST.md](RESTORE_CHECKLIST.md)). Tick each item. If something fails,
consult Troubleshooting in [LOCAL_SETUP.md](LOCAL_SETUP.md). Behaviour that is
*meant* to differ locally is listed in
[KNOWN_LOCAL_DIFFERENCES.md](KNOWN_LOCAL_DIFFERENCES.md) — don't "fix" those.

## Core load

- [ ] **Homepage loads** at `http://travelwithoutborders.local` without errors
- [ ] **No redirect loop** and no redirect to `travelwithoutborders.co.uk`
- [ ] Home and inner pages return HTTP 200

## Authentication

- [ ] **Login works** at `/wp-admin` (imported or locally created admin)
- [ ] Admin dashboard loads without fatal errors

## Theme

- [ ] **Parent theme present** (`ave`)
- [ ] **Child theme active** (`ave-child`) — confirm via Appearance → Themes or
      `wp theme list --status=active`
- [ ] Header renders (note: GTM is in the child `header.php`)

## Page builder & content

- [ ] **WPBakery works** — open a page in the backend editor; front-end renders
      WPBakery sections/`ld_*` elements correctly
- [ ] Homepage layout matches the baseline screenshots (`04_Testing/Baseline/`)
- [ ] A sample destination page renders (e.g. Berlin, Munich)

## Permalinks & routing

- [ ] **Permalinks work** — inner pages load (not just the homepage)
- [ ] Re-saved permalinks / `wp rewrite flush` applied
- [ ] No site-wide 404s

## Media

- [ ] **Media loads** — homepage images, logos, and icons display
- [ ] Media Library shows uploads (years 2018–2026)
- [ ] No broken image URLs pointing at production

## Errors & console

- [ ] **No PHP errors** — check `...\app\public\wp-content\debug.log` and
      LocalWP logs (enable `WP_DEBUG` locally if needed)
- [ ] **No JS errors** in the browser console on the homepage
- [ ] **No mixed-content / SSL warnings** (local is `http`)

## Caching & security state

- [ ] **No cache issues** — caching plugins inactive; `advanced-cache.php` removed
- [ ] **No redirect loops** from Really Simple Security (confirmed inactive)
- [ ] Wordfence / Post SMTP / UpdraftPlus confirmed inactive

## Final

- [ ] Snapshot the working restored site in LocalWP
- [ ] Temporary `twb.sql` working copy deleted
- [ ] Note any anomalies in [Development Log](../01_Documentation/DEVELOPMENT_LOG.md)
