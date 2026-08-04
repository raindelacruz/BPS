# Database Reference

## Overview
The current eBPS workflow is backed by three core tables:
- `users`
- `notices`
- `email_change_requests`

The main workflow table is `notices`. Both bid notices and downstream related notices are stored in the same table. Relationships between notices are inferred by shared `reference_code`.

## Table: `users`

### Purpose
Stores internal system users for authentication, role control, ownership, and region context.

### Columns

| Column | Type | Meaning |
|---|---|---|
| `id` | int | Primary key |
| `username` | varchar(50) | Login identifier |
| `firstname` | varchar(255) | User first name |
| `middle_initial` | varchar(1) | Optional middle initial |
| `lastname` | varchar(255) | User last name |
| `region` | varchar(50) | User's assigned region |
| `password` | varchar(255) | Hashed password |
| `role` | varchar(50) | Typically `author` or `admin` |
| `email` | varchar(255) | Account email |
| `verification_token` | varchar(64) | Present in schema, not central to current registration flow |
| `verification_code` | varchar(6) | Registration email verification code |
| `token_expiry` | datetime | Expiry for verification code |
| `is_verified` | tinyint(1) | Registration verification flag |
| `is_active` | tinyint(1) | Account activation flag required for login |
| `created_at` | timestamp | Creation timestamp |
| `updated_at` | timestamp | Last update timestamp |

### Workflow Meaning
- `is_verified` tracks registration code verification.
- `is_active` controls whether login is allowed.
- `region` is used in dashboard context and some workflow checks.
- `role` determines access to admin-only functions.

## Table: `notices`

### Purpose
Stores all notice records:
- root bid notices
- related notices such as `sbb`, `resolution`, `award`, `contract`, and `proceed`

### Columns

| Column | Type | Meaning |
|---|---|---|
| `id` | int | Primary key |
| `title` | varchar(255) | Notice title |
| `reference_code` | varchar(50) | Logical grouping key across related notices |
| `type` | enum | Notice type |
| `file_path` | varchar(255) | Stored PDF path |
| `upload_date` | timestamp | Upload timestamp |
| `start_date` | datetime | When notice becomes visible/active |
| `end_date` | datetime | End of notice validity window |
| `uploaded_by` | int | FK-like link to `users.id` |
| `description` | text | Notice body/summary |
| `is_archived` | tinyint(1) | Archive flag |
| `status` | enum | Status used in workflow |
| `region` | varchar(20) | Region tied to notice |
| `procurement_type` | enum | Mode of procurement |
| `archived_at` | datetime | Archive timestamp |

### Supported Notice Types
Current schema/code indicates these active workflow types:
- `bid`
- `sbb`
- `award`
- `resolution`
- `proceed`
- `contract`
- `rfq`

### Workflow Meaning of Important Columns

#### `reference_code`
- Core logical relationship key
- All notices in the same workflow set share the same `reference_code`
- Used for:
  - prerequisite checks
  - related notice lookup
  - grouped archive/unarchive
  - public and dashboard grouped display

#### `type`
Determines role in workflow:
- `bid`: root notice
- `sbb`: supplemental related notice
- `resolution`: required before award
- `award`: required before contract
- `contract`: required before proceed
- `proceed`: final related notice that enables archive eligibility

#### `status`
Used by current code for lifecycle state:
- `pending`
- `active`
- `expired` appears in runtime logic
- `archived`

Note:
- current dump schema enum does not clearly match all runtime values used in code

#### `is_archived`
- separate archive flag
- used together with `status`

#### `procurement_type`
Categorizes procurement mode, such as:
- `public_bidding`
- `shopping`
- `small_value`
- `agency_to_agency`
- `direct_contracting`
- `pol`
- `emergency`
- `repeat_order`
- `leased_property`

## Table: `email_change_requests`

### Purpose
Tracks requested user email changes until confirmed by token verification.

### Columns

| Column | Type | Meaning |
|---|---|---|
| `id` | int | Primary key |
| `user_id` | int | User reference |
| `current_email` | varchar(255) | Old email |
| `new_email` | varchar(255) | Requested new email |
| `token` | varchar(64) | Verification token |
| `status` | enum | `pending`, `completed`, `cancelled` |
| `created_at` | timestamp | Request creation time |
| `expires_at` | timestamp | Request expiry |

### Workflow Meaning
- Admin can update user details and trigger an email change request.
- Actual email change is only finalized after token verification.

## Table Relationships

### `users` -> `notices`
- Relationship:
  - one user uploads many notices
- Join key:
  - `notices.uploaded_by = users.id`
- Workflow meaning:
  - ownership
  - uploader display
  - delete and archive permission checks

### `users` -> `email_change_requests`
- Relationship:
  - one user may have many email change requests
- Join key:
  - `email_change_requests.user_id = users.id`

## Notice Relationship Model

### Current Relationship Mechanism
Notice-to-notice relationships are not explicitly normalized through parent/child IDs.

Instead, current logic infers relationships by:
- same `reference_code`
- different `type`

### Example Workflow Set

| Type | Reference Code |
|---|---|
| `bid` | `ABC-2026-001` |
| `resolution` | `ABC-2026-001` |
| `award` | `ABC-2026-001` |
| `contract` | `ABC-2026-001` |
| `proceed` | `ABC-2026-001` |

These rows are treated as one grouped workflow set.

### Effects of `reference_code` Grouping
- related notices are displayed together in dashboard/public pages
- workflow prerequisites are checked against same `reference_code`
- archive/unarchive updates all notices with same `reference_code`
- uniqueness checks for some notice types are scoped to same `reference_code`

## Database Support for Workflow

### Registration and Login
- `users` supports:
  - credentials
  - activation state
  - verification state
  - role
  - region

### Notice Posting
- `notices` stores:
  - bid records
  - related workflow records
  - scheduling dates
  - file location
  - state
  - archive information

### Public Display
- `notices` supports public selection by:
  - `type = bid`
  - `status = active`
  - `is_archived = 0`

### Archive Logic
- `notices` supports archive grouping through `reference_code`
- `archived_at` records grouped archive timestamp

## Current Design Limitations
- no explicit foreign key from related notice to parent bid notice
- no dedicated workflow table
- no amendment/history/version table
- status handling is partly application-driven, not fully aligned to schema dump
- some workflow constraints exist only in code, not database constraints

## Summary
The database currently supports the workflow by keeping all procurement notice records in one table and using `reference_code` as the logical key that binds related notices into a single workflow set.
