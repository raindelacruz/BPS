# Production Rules

This document records the production business rules currently enforced by the application. It is intended to prevent accidental drift from the live database model.

For maintenance-window procedure and smoke checks, see `MAINTENANCE_RELEASE.md`.

## Database Model

- The production workflow uses `parent_procurement` as the parent record.
- Posted procurement documents live in dedicated document tables such as `bid_notices`, `rfqs`, `resolutions`, `awards`, `contracts`, `contract_or_purchase_orders`, and `notices_to_proceed`.
- The legacy single `notices` table is not part of the production runtime.
- Do not run `database/schema.sql` against production. It is a destructive fresh-install script.

## Public Visibility

- Public procurement records are visible only when their computed `posting_status` is configured as public.
- The default public statuses are `open` and `closed`.
- `scheduled` records are not public.
- `archived` records are not public.
- This can be adjusted with `PUBLIC_VISIBLE_STATUSES`, using a comma-separated list such as `open,closed`.

## Posting And Immutability

- Posted procurement documents are immutable.
- Procurement posting deletion is disabled in the posting module.
- Corrections should be made through subsequent official records, not by replacing or deleting posted records.

## Archive Policy

- Only administrators may archive procurement records.
- Archive reason and approval reference are required.
- Competitive Bidding records may be archived only after a Notice to Proceed is posted.
- SVP records may be archived only after an Award is posted.
- Archived procurement records cannot be restored by the production workflow.

## Upload Policy

- Procurement document uploads must be PDFs.
- Default maximum upload size is 20 MB.
- The application validates extension, MIME type, and PDF file signature before storing uploads.
