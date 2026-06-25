# Deployment Checklist

Run through this checklist for every deployment to the live site. See the
[Workflow](../01_Documentation/WORKFLOW.md) for the wider process.

## Pre-Deployment

- [ ] All changes tested locally and compared against baselines
      (`04_Testing/`).
- [ ] Desktop and mobile checked.
- [ ] A current, verified backup exists
      ([Backup Strategy](../01_Documentation/BACKUP_STRATEGY.md)).
- [ ] A fresh pre-deployment backup has been created.
- [ ] Changes committed and pushed.
- [ ] Deployment window agreed (low-traffic if possible).

## Deployment

- [ ] Put the site in maintenance mode (if applicable).
- [ ] Apply the child-theme / custom-code changes.
- [ ] Clear caches (WP Rocket) and regenerate assets if needed.

## Post-Deployment Verification

- [ ] Homepage loads correctly (compare to baseline).
- [ ] Redesigned/changed sections render as expected.
- [ ] Forms and interactive elements work.
- [ ] Mobile layout verified.
- [ ] No console or PHP errors.
- [ ] Take maintenance mode off.

## Wrap-Up

- [ ] Record the deployment in [Release Notes](RELEASE_NOTES.md).
- [ ] Update the [Changelog](../CHANGELOG.md) and
      [Project Status](../PROJECT_STATUS.md).
- [ ] If issues arise, follow the [Rollback Checklist](ROLLBACK_CHECKLIST.md).
