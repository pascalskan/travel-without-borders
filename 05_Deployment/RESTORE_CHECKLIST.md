# Restore Checklist

Ordered, tickable steps to reconstruct the site locally. Read
[LOCAL_SETUP.md](LOCAL_SETUP.md) first. **Do not run commands blindly** — adjust
paths/URLs to your machine. Nothing here has been executed.

**Conventions used below:**
- Local URL: `http://travelwithoutborders.local` (confirm yours in LocalWP)
- Production URL: `https://travelwithoutborders.co.uk`
- Table prefix: `Gv9IkG4cZ_`
- Run WP-CLI from *Local → right-click site → Open site shell*.

---

## A. Pre-flight

- [ ] Confirm the backup is intact at `00_Backups/2026-06-25_Pre-Development/`
- [ ] Confirm backup folders are **read-only** (do not edit originals)
- [ ] Decompress a **working copy** of the SQL (never modify the original `.gz`):
      ```bash
      gzip -dk "00_Backups/2026-06-25_Pre-Development/backup_2026-06-25-0105_Travel_without_Borders__059db134161b-db.gz"
      ```
- [ ] Read [KNOWN_LOCAL_DIFFERENCES.md](KNOWN_LOCAL_DIFFERENCES.md)

## B. Create the LocalWP site

- [ ] Create site (e.g. name `travelwithoutborders`)
- [ ] Set **PHP 7.4.x** (not the PHP 8 default)
- [ ] Choose WordPress 7.0 (or closest available)
- [ ] Note the local URL and web root path
- [ ] Start the site; confirm the default WP install loads
- [ ] **Take a LocalWP snapshot** ("clean install") for rollback

## C. Neutralise security & caching (BEFORE overlay/import)

In `...\app\public\wp-content\plugins\`, rename so they cannot run:

- [ ] `really-simple-ssl` → `really-simple-ssl.off`
- [ ] `wp-rocket` → `wp-rocket.off`
- [ ] `breeze` → `breeze.off`
- [ ] `wordfence` → `wordfence.off`
- [ ] `post-smtp` → `post-smtp.off`
- [ ] `updraftplus` → `updraftplus.off`
- [ ] Delete the caching drop-in if present: `wp-content/advanced-cache.php`
- [ ] Delete `wp-content/object-cache.php` if present

> These renames happen after the overlay (Step D) if the backup plugins are
> copied first; if you prefer, do the overlay then rename. Either order is fine —
> just ensure they are `.off` before the **first** authenticated page load.

## D. Overlay wp-content from the backup

Copy from backup → Local web root (merge, keep WP core untouched):

- [ ] themes: `...\00_Backups\2026-06-25_Pre-Development\themes\*` → `...\app\public\wp-content\themes\`
- [ ] plugins: `...\00_Backups\2026-06-25_Pre-Development\Plugins\*` → `...\app\public\wp-content\plugins\`  *(capital P source → lowercase plugins)*
- [ ] uploads: `...\00_Backups\2026-06-25_Pre-Development\uploads\*` → `...\app\public\wp-content\uploads\`
- [ ] Do **not** copy `Others/` runtime files (maintenance.php, wflogs, *-config, upgrade-temp-backup, mu-plugins.bk)
- [ ] Re-apply the renames in Step C if the copy overwrote them

## E. Point wp-config at the real prefix

- [ ] Edit `...\app\public\wp-config.php`
- [ ] Set: `$table_prefix = 'Gv9IkG4cZ_';`
- [ ] (Optional, forces correct URLs early)
      ```php
      define( 'WP_HOME', 'http://travelwithoutborders.local' );
      define( 'WP_SITEURL', 'http://travelwithoutborders.local' );
      ```

## F. Import the database

- [ ] (Recommended) reset the DB so only imported tables exist:
      ```bash
      wp db reset --yes
      ```
- [ ] Import the decompressed dump:
      ```bash
      wp db import "twb.sql"
      ```
- [ ] Confirm tables imported with the right prefix:
      ```bash
      wp db tables 'Gv9IkG4cZ_*' --all-tables
      ```

## G. Search-replace URLs (serialize-safe)

- [ ] Dry run first:
      ```bash
      wp search-replace 'https://travelwithoutborders.co.uk' 'http://travelwithoutborders.local' --all-tables --precise --recurse-objects --dry-run
      ```
- [ ] Apply:
      ```bash
      wp search-replace 'https://travelwithoutborders.co.uk' 'http://travelwithoutborders.local' --all-tables --precise --recurse-objects
      ```
- [ ] Catch protocol-relative / non-https / www variants:
      ```bash
      wp search-replace '//travelwithoutborders.co.uk' '//travelwithoutborders.local' --all-tables --precise --recurse-objects
      wp search-replace 'http://travelwithoutborders.co.uk' 'http://travelwithoutborders.local' --all-tables --precise --recurse-objects
      ```

## H. Flush & finalise

- [ ] Verify/confirm core URLs:
      ```bash
      wp option get siteurl
      wp option get home
      ```
- [ ] Confirm the active theme is the child theme:
      ```bash
      wp theme list --status=active
      wp theme activate ave-child
      ```
- [ ] Flush rewrite rules and set permalinks:
      ```bash
      wp rewrite structure '/%postname%/' --hard
      wp rewrite flush --hard
      ```
- [ ] Flush object/page cache:
      ```bash
      wp cache flush
      ```
- [ ] Keep the hostile plugins inactive (verify):
      ```bash
      wp plugin list --status=active
      ```
      Deactivate any that slipped through:
      ```bash
      wp plugin deactivate really-simple-ssl wp-rocket breeze wordfence post-smtp updraftplus
      ```

## I. Admin access

- [ ] List users (existing live accounts are imported):
      ```bash
      wp user list --fields=ID,user_login,user_email,roles
      ```
- [ ] If needed, create a local admin:
      ```bash
      wp user create localadmin localadmin@example.test --role=administrator --user_pass='ChangeMe!Local2026'
      ```
- [ ] Or reset an existing admin's password locally:
      ```bash
      wp user update <ID> --user_pass='ChangeMe!Local2026'
      ```

## J. Verify

- [ ] Work through [POST_RESTORE_CHECKLIST.md](POST_RESTORE_CHECKLIST.md)
- [ ] Take a LocalWP snapshot of the working restored site
- [ ] Delete the temporary `twb.sql` working copy

---

## WP-CLI command index (reference)

| Purpose | Command |
| ------- | ------- |
| Reset DB | `wp db reset --yes` |
| Import DB | `wp db import "twb.sql"` |
| List prefixed tables | `wp db tables 'Gv9IkG4cZ_*' --all-tables` |
| URL search-replace (dry) | `wp search-replace 'https://travelwithoutborders.co.uk' 'http://travelwithoutborders.local' --all-tables --precise --recurse-objects --dry-run` |
| URL search-replace (apply) | same without `--dry-run` |
| Get siteurl/home | `wp option get siteurl` / `wp option get home` |
| Activate child theme | `wp theme activate ave-child` |
| Set permalinks | `wp rewrite structure '/%postname%/' --hard` |
| Flush rewrites | `wp rewrite flush --hard` |
| Flush cache | `wp cache flush` |
| List/deactivate plugins | `wp plugin list` / `wp plugin deactivate <slugs>` |
| List users | `wp user list` |
| Create admin | `wp user create localadmin localadmin@example.test --role=administrator --user_pass='...'` |
| Reset password | `wp user update <ID> --user_pass='...'` |
| Sanity check errors | `wp eval 'echo "WP loaded OK\n";'` |
