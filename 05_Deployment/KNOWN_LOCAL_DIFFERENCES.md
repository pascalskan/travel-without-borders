# Known Local Differences

Behaviour that is **expected** to differ between the local rebuild and
production. These are normal consequences of running offline with security and
caching disabled — **do not try to "fix" them locally**, and account for them
when interpreting test results.

## Summary

| Area | Production | Local | Action |
| ---- | ---------- | ----- | ------ |
| SSL / protocol | HTTPS (forced by Really Simple SSL) | HTTP (`.local`) | Expected; RSSSL disabled locally |
| Analytics / GTM | GTM `GTM-TCZM2CR` → UA + GA4 live | Tag fires but data is junk/local | Ignore or block; don't pollute live analytics |
| Email | Post SMTP sends real mail | Disabled; mail not sent | Use a mail catcher if email must be tested |
| Caching | WP Rocket + Breeze active | Disabled; no optimisation | Expected; test uncached behaviour |
| Forms (Quform) | Live submissions stored/emailed | Submit works but no email; data local only | Don't expect live notifications |
| Redirects | Full Redirection ruleset | Only the rules present in the backup DB | Local redirect set is incomplete |
| Security/WAF | Wordfence active | Disabled | Expected; do not enable locally |
| CDN / image opt | Smush / host CDN | Serves raw local files | Expected |

## Detail

### SSL
Production forces HTTPS; the local site runs on `http://…​.local`. Really Simple
Security is intentionally disabled to avoid redirect loops. Mixed-content notices
on local are expected.

### Analytics (GTM / UA / GA4)
The GTM container is hardcoded in the child theme `header.php`, so it will load
locally too. Any hits are **local noise** — either ignore them, block
`googletagmanager.com` in the browser, or comment the snippet out *locally only*
(never commit that change). See
[Customisation Audit](../01_Documentation/CUSTOMISATION_AUDIT.md).

### Email
Post SMTP is disabled, so no outbound email (form notifications, password resets)
is sent. If email flows must be tested, install a local mail catcher (e.g.
MailHog) — do **not** configure real SMTP credentials locally.

### Caching
WP Rocket, Breeze, and the `advanced-cache.php` drop-in are removed/disabled.
Pages render uncached and unoptimised; load times and asset bundling will differ
from production. This is expected and makes debugging easier.

### Forms (Quform)
Forms render and validate, but submissions stay in the local DB and send no
email. Note the backup DB contains only **1** historical entry (not the ~190 on
live) — see the
[Acquisition Report](../07_Source/Inventory/ACQUISITION_REPORT.md).

### Redirects / Slider Revolution / SEO data
The local DB reflects the backup, which is **incomplete** versus live: ~1
redirect (not the full set), 1 Slider Revolution module (not 12), and Yoast data
as captured. Missing items are not local faults — they require live re-export
(see [Phase 2 Verification](../07_Source/Inventory/PHASE2_VERIFICATION.md)).

### Security / WAF
Wordfence and related rules are disabled locally. Expect no firewall, login
limiting, or scan behaviour. Do not enable these on the local site.

## Rule of thumb

If a difference traces back to **SSL, analytics, email, caching, security, CDN,
or incomplete backup data**, it is expected. Only investigate differences in
**content, layout, theme, page-builder rendering, or PHP errors** — those reflect
the actual site and matter for development.
