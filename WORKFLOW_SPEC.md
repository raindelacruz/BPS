# Workflow Specification

## Overview
This document defines the current eBPS business workflow as implemented in the existing system. It is written as an implementation-ready specification for future MVC development.

## Core Workflow Principle
- `bid` is the root notice type
- related notices are separate rows in `notices`
- related notices are linked to the root workflow using the same `reference_code`
- workflow progression is enforced in application logic through prerequisite checks

## User Workflow

### 1. Registration
1. User submits registration form.
2. System validates:
- `.gov.ph` email
- password confirmation
- username/email uniqueness
3. System creates user as `author`.
4. System generates and emails verification code.
5. User enters code on verification page.
6. If valid and not expired, user becomes verified.

### 2. Login
1. User submits username/password.
2. System checks account exists.
3. System checks `is_active = 1`.
4. System verifies password hash.
5. On success, session is created and dashboard opens.

### 3. Dashboard Access
1. Authenticated user accesses dashboard.
2. Section access is role-based.
3. Notice statuses are recalculated on dashboard load.

## Notice Workflow Chain

### Root Chain
`bid -> resolution -> award -> contract -> proceed`

### Supplemental Type
`sbb` is attached to a bid but is not part of the hard prerequisite chain.

## Notice Type Specifications

### `bid`
#### Creation
- created directly
- user manually enters `reference_code`

#### Required Inputs
- title
- reference_code
- procurement_type
- region
- start_date
- end_date
- description
- PDF

#### Prerequisites
- none

#### Relationship
- defines the `reference_code` for downstream notices

#### Status Behavior
- `pending` if start date is future
- `active` if start date is current/past

#### Downstream Effect
- root anchor for all related notices

### `sbb`
#### Creation
- created by selecting an existing active bid

#### Required Inputs
- selected bid
- title
- start_date
- end_date
- description
- PDF

#### Prerequisites
- referenced bid must exist and be active

#### Uniqueness
- multiple allowed for same `reference_code`

#### Relationship
- inherits selected bid's `reference_code`

#### Downstream Effect
- no hard gate for later workflow stages

### `resolution`
#### Creation
- created by selecting an existing active bid

#### Required Inputs
- selected bid
- title
- start_date
- end_date
- description
- PDF

#### Prerequisites
- referenced active bid must exist

#### Uniqueness
- one active/non-archived resolution per reference/region

#### Relationship
- inherits selected bid's `reference_code`

#### Downstream Effect
- unlocks `award`

### `award`
#### Creation
- created by selecting an eligible bid

#### Required Inputs
- selected bid
- title
- start_date
- end_date
- description
- PDF

#### Prerequisites
- `resolution` must already exist for same `reference_code`

#### Uniqueness
- one active/non-archived award per `reference_code`

#### Relationship
- inherits selected bid's `reference_code`

#### Downstream Effect
- unlocks `contract`

### `contract`
#### Creation
- created by selecting an eligible bid

#### Required Inputs
- selected bid
- title
- start_date
- end_date
- description
- PDF

#### Prerequisites
- `award` must already exist for same `reference_code`

#### Uniqueness
- one active/non-archived contract per `reference_code`

#### Relationship
- inherits selected bid's `reference_code`

#### Downstream Effect
- unlocks `proceed`

### `proceed`
#### Creation
- created by selecting an eligible bid

#### Required Inputs
- selected bid
- title
- start_date
- end_date
- description
- PDF

#### Prerequisites
- `contract` must already exist for same `reference_code`

#### Uniqueness
- one active/non-archived proceed per `reference_code`

#### Relationship
- inherits selected bid's `reference_code`

#### Downstream Effect
- makes grouped archive eligible

## Prerequisite Rules

| Target Type | Required Existing Type | Duplicate Limit |
|---|---|---|
| `sbb` | active `bid` | multiple allowed |
| `resolution` | active `bid` | one per reference/region |
| `award` | `resolution` | one per reference |
| `contract` | `award` | one per reference |
| `proceed` | `contract` | one per reference |

## State Rules

### `pending`
- assigned when new notice start date is in the future
- posted record remains read-only; any pre-posting edits must happen in a separate draft workflow
- not publicly visible as active bid

### `active`
- assigned when start date is current/past
- publicly visible for bids
- not editable

### `expired`
- assigned by runtime date logic when end date has passed
- not editable
- may still be archived

### `archived`
- assigned by archive action
- excluded from active/public workflows

## State Transitions

| From | To | Trigger |
|---|---|---|
| new | `pending` | future start date |
| new | `active` | current/past start date |
| `pending` | `active` | start date reached |
| `active` | `expired` | end date passed |
| `expired` | `active` | date logic recalculates after extension |
| `pending` / `active` / `expired` | `archived` | archive action |
| `archived` | recalculated state | unarchive action |

## Allowed Actions by State

| State | View | Edit | Delete | Archive | Public Bid Display |
|---|---|---|---|---|---|
| `pending` | yes | no | yes | possible only if archive rules pass | no |
| `active` | yes | no | yes | possible only if archive rules pass | yes for bids |
| `expired` | yes | no | yes | yes | no |
| `archived` | yes in archive list | no | not part of active flow | unarchive instead | no |

## Editing Rules
- posted procurement records are immutable
- no edit, reopen, unlock, or file replacement is allowed after posting
- upstream records must remain unchanged after downstream records are posted
- corrections must be handled through separate subsequent or corrective records, not by modifying the original row

## Deletion Rules
- only uploader may delete a notice
- delete permanently removes database row
- delete also removes file from disk if it exists
- delete is not constrained by downstream workflow relationships in current code

## Archive / Unarchive Rules

### Archive Allowed When
- current user is uploader
- and either:
  - an active `proceed` exists for same `reference_code`, or
  - the notice is expired

### Archive Behavior
- all notices with the same `reference_code` are updated
- `is_archived = 1`
- `status = archived`
- `archived_at` set

### Unarchive Behavior
- all notices with same `reference_code` are restored
- `is_archived = 0`
- `archived_at = null`
- status recalculated based on dates

## Public Display Rules
- public page primarily lists only `bid` notices
- public primary notice conditions:
  - `type = bid`
  - `status = active`
  - `is_archived = 0`
- related notices are loaded by matching same `reference_code`
- public related notices are displayed as grouped attachments/supporting documents

## Validation Matrix

| Action | Allowed When | Blocked When |
|---|---|---|
| register | valid email, unique identity, matching passwords | invalid email, duplicate user, password mismatch |
| login | active account, valid password | inactive account, invalid credentials |
| create bid | authenticated user, valid fields/PDF | missing fields, bad PDF |
| create resolution | active bid exists, no same-ref resolution in region | no bid, duplicate resolution |
| create award | resolution exists, no same-ref award | missing resolution, duplicate award |
| create contract | award exists, no same-ref contract | missing award, duplicate contract |
| create proceed | contract exists, no same-ref proceed | missing contract, duplicate proceed |
| edit notice | not supported for posted procurement records | all posted records are immutable |
| delete notice | user is uploader | not uploader |
| archive set | uploader and (`proceed` exists or notice expired) | uploader rule fails or archive prerequisites fail |
| public bid display | active, non-archived bid | pending, expired, archived, non-bid |

## Summary
The current workflow is a grouped notice lifecycle centered on `bid` records and linked related records. The workflow chain is enforced in code, while grouping is driven by `reference_code`.
