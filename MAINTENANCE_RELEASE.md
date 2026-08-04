# Maintenance Release Runbook

Use this runbook for small production maintenance releases. It assumes the current live database and uploaded procurement documents are official records.

## Rules

- Do not run `database/schema.sql` against production.
- Do not drop, truncate, rewrite, or reseed production tables during a maintenance release.
- Do not delete files from `storage/uploads/notices`.
- Treat archived and posted procurement records as immutable.
- Keep a current Phase 2 baseline or newer secured backup before every release.

## Pre-Release

1. Confirm the maintenance window and rollback owner.
2. Export a fresh BPS-only database backup.
3. Back up `storage/uploads/notices`.
4. Confirm server environment values are set, especially `DB_PASSWORD`, `APP_DEBUG=false`, and `APP_ENV=production`.
5. Run:

```powershell
php scripts\production_smoke_check.php
php tests\ProcurementPostingServiceTest.php
php tests\SvpWorkflowServiceTest.php
```

Warnings may be accepted only when documented by the release owner. Failures must be resolved before proceeding.

## Release

1. Place the application in a low-traffic maintenance window.
2. Deploy only the reviewed source changes.
3. Do not apply database changes unless a separate approved additive migration plan exists.
4. Reload Apache/PHP if required by the deployment method.

## Post-Release

1. Run:

```powershell
php scripts\production_smoke_check.php
```

2. Check login, dashboard access, public listing, and one representative procurement detail page.
3. Check `storage/logs/app.log` for new errors.
4. Confirm no new missing-file warnings appeared beyond the known baseline.

## Rollback

1. Restore the prior source copy.
2. Restore database only if the release included an approved database migration.
3. Restore uploaded files only if the release modified uploaded-file handling and a file-level issue is confirmed.
4. Re-run `php scripts\production_smoke_check.php`.
