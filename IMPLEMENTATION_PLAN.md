# Implementation Plan

## Purpose
This document breaks the future MVC implementation into phased work units while staying aligned to the current eBPS behavior. It is a planning document only and does not represent code changes.

## Implementation Principles
- preserve current business workflow semantics
- move direct page logic into MVC layers
- centralize duplicate workflow and status rules
- keep SQL out of views
- keep file handling and validation reusable
- implement incrementally to reduce regression risk

## Phase 1: Project Structure and Bootstrap

### Goals
- establish MVC folder layout
- create front controller and routing
- set up config/bootstrap process
- define base conventions for controllers/models/services/helpers

### Deliverables
- `public/index.php`
- `routes/web.php`
- `routes/api.php`
- `app/config/app.php`
- `app/config/database.php`
- `app/config/mail.php`
- base controller/service/helper classes if needed

### Dependencies
- none

### Risks
- preserving existing entry behavior while introducing routing
- deciding migration path from page-based endpoints to route/controller actions

### Testing Points
- route resolution
- config loading
- DB connectivity
- basic view rendering

## Phase 2: Authentication and Session Module

### Goals
- implement registration, login, logout, verification
- preserve current activation and verification behavior

### Deliverables
- `app/controllers/AuthController.php`
- `app/controllers/VerificationController.php`
- `app/models/User.php`
- `app/models/EmailChangeRequest.php`
- `app/services/AuthService.php`
- `app/services/EmailService.php`
- `app/helpers/SessionHelper.php`
- `app/helpers/SecurityHelper.php`
- `app/views/auth/login.php`
- `app/views/auth/register.php`
- `app/views/auth/verify.php`

### Functional Coverage
- registration with `.gov.ph` validation
- username/email uniqueness
- verification code generation and validation
- login with `is_active` check
- logout/session destroy
- email change verification flow

### Dependencies
- Phase 1 routing/bootstrap

### Risks
- current code checks `is_active` on login but not clearly `is_verified`
- mail configuration and delivery differences

### Testing Points
- successful register
- duplicate register rejection
- invalid email rejection
- verification code success/failure/expiry
- active vs inactive login
- session creation/destruction

## Phase 3: Core Notice Model and Status Foundation

### Goals
- implement core notice persistence and date/status behavior
- support shared queries used by dashboard and public pages

### Deliverables
- `app/models/Notice.php`
- `app/services/DateStatusService.php`
- `app/services/FileUploadService.php`
- `app/services/NoticeValidationService.php`

### Functional Coverage
- create notice row
- update notice row
- delete notice row
- grouped queries by `reference_code`
- status calculation:
  - pending
  - active
  - expired
  - archived

### Dependencies
- Phase 1 and 2

### Risks
- current schema/runtime mismatch for `expired`
- inconsistent current upload paths

### Testing Points
- status assignment on insert
- status transition by dates
- file save/delete behavior
- grouped notice lookup by reference code

## Phase 4: Bid Notice Management

### Goals
- implement direct creation and management of `bid` notices
- support pending edit flow and delete flow

### Deliverables
- `app/controllers/NoticeController.php`
- `app/views/notice/create.php`
- `app/views/notice/edit.php`
- `app/views/notice/pending-list.php`

### Functional Coverage
- create bid
- validate bid inputs
- store PDF
- edit pending bid
- replace pending bid PDF
- delete bid by uploader

### Dependencies
- Phase 3 notice foundation

### Risks
- preserving current permission behavior exactly
- preserving current pending-only edit restriction

### Testing Points
- create bid success/failure
- pending edit allowed
- active edit blocked
- delete by uploader allowed
- delete by non-uploader blocked

## Phase 5: Related Notice Workflow

### Goals
- implement related notice creation and workflow chain rules

### Deliverables
- `app/services/NoticeWorkflowService.php`
- `app/services/PrerequisiteService.php`
- `app/controllers/WorkflowValidationController.php`
- reference selection support in notice views

### Functional Coverage
- create `sbb`
- create `resolution`
- create `award`
- create `contract`
- create `proceed`
- fetch eligible bid references
- prerequisite validation
- duplicate restriction checks
- derived title/reference/procurement-type behavior

### Dependencies
- Phases 3 and 4

### Risks
- current rules exist in multiple places and must be centralized carefully
- preserving same-reference grouping behavior

### Testing Points
- `award` blocked without `resolution`
- `contract` blocked without `award`
- `proceed` blocked without `contract`
- duplicate `award`, `contract`, `proceed` blocked
- multiple `sbb` allowed
- same-reference grouping preserved

## Phase 6: Dashboard and Internal Lists

### Goals
- implement internal dashboard pages and queries
- replicate pending/all/archived notice views

### Deliverables
- `app/controllers/DashboardController.php`
- `app/services/DashboardQueryService.php`
- `app/views/dashboard/index.php`
- `app/views/dashboard/overview.php`
- `app/views/notice/all-list.php`
- `app/views/notice/archive-list.php`

### Functional Coverage
- dashboard overview data
- pending notice list
- active/expired bid list
- grouped related notice tabs
- archived grouped notice list

### Dependencies
- Phases 3 to 5

### Risks
- current dashboard page is highly mixed and query-heavy
- chart/activity logic may depend on repeated queries

### Testing Points
- pending notices appear correctly
- active/expired internal notices group correctly
- archived listing groups correctly
- role-based section visibility

## Phase 7: Archive Module

### Goals
- implement grouped archive/unarchive behavior

### Deliverables
- `app/controllers/ArchiveController.php`
- `app/services/ArchiveService.php`

### Functional Coverage
- archive eligibility checks
- grouped archive by `reference_code`
- grouped unarchive with status recalculation

### Dependencies
- Phases 3 to 6

### Risks
- archive logic depends on `proceed` existence and date-based expiration
- current ownership rule is uploader-based, not admin override

### Testing Points
- archive allowed when proceed exists
- archive allowed when expired
- archive blocked otherwise
- all same-reference notices updated together
- unarchive recalculates states correctly

## Phase 8: Public Notice Module

### Goals
- implement public bid listing and related notice display

### Deliverables
- `app/controllers/PublicNoticeController.php`
- `app/services/PublicNoticeQueryService.php`
- `app/views/public/index.php`

### Functional Coverage
- public active bid listing
- search/filter behavior
- related notice grouping under each bid

### Dependencies
- Phases 3 to 6

### Risks
- preserving current public-only bid visibility rules
- reproducing grouped related display accurately

### Testing Points
- only active non-archived bids appear
- related notices load by same reference code
- archived/pending bids excluded from main public list

## Phase 9: User Administration Module

### Goals
- implement admin user management

### Deliverables
- `app/controllers/UserController.php`
- `app/services/UserManagementService.php`
- `app/views/user/index.php`

### Functional Coverage
- list users
- update username/email/role/region
- toggle active/inactive
- delete user
- reassign deleted user's notices to current admin before delete

### Dependencies
- Phase 2 auth foundation
- Phase 3 notice foundation

### Risks
- preserve self-protection rules:
  - cannot deactivate own account
  - cannot delete own account
  - cannot change own role

### Testing Points
- admin-only access
- self-modification restrictions
- email change request creation
- notice reassignment on user delete

## Phase 10: Profile Module

### Goals
- implement current-user profile behavior

### Deliverables
- `app/controllers/ProfileController.php`
- `app/views/profile/account.php`

### Functional Coverage
- display account details
- change password
- align actual editable behavior to current implemented rules

### Dependencies
- Phase 2 auth

### Risks
- current code/UI mismatch around account details editing

### Testing Points
- password update success/failure
- current password verification
- profile display correctness

## Cross-Phase Testing Strategy

### Unit-Level Targets
- status calculation
- prerequisite logic
- archive eligibility logic
- ownership validation
- file validation

### Integration-Level Targets
- registration to verification
- login to dashboard
- bid creation to public visibility
- related notice chain progression
- archive/unarchive grouped behavior
- admin user lifecycle

### Regression Focus Areas
- `reference_code` grouping
- pending-only edit restriction
- uploader-only delete restriction
- archive behavior across grouped notices
- public filtering to active non-archived bids

## File-by-File / Module Sequence

| Sequence | Area | Main Files |
|---|---|---|
| 1 | bootstrap/routing | `public/index.php`, `routes/*.php`, `app/config/*` |
| 2 | auth core | `AuthController`, `VerificationController`, `User`, `AuthService` |
| 3 | notice core | `Notice`, `DateStatusService`, `FileUploadService`, `NoticeValidationService` |
| 4 | bid flow | `NoticeController`, create/edit notice views |
| 5 | workflow rules | `NoticeWorkflowService`, `PrerequisiteService`, `WorkflowValidationController` |
| 6 | dashboard queries/views | `DashboardController`, `DashboardQueryService`, dashboard/notice views |
| 7 | archive | `ArchiveController`, `ArchiveService` |
| 8 | public module | `PublicNoticeController`, `PublicNoticeQueryService`, public view |
| 9 | admin users | `UserController`, `UserManagementService`, user views |
| 10 | profile | `ProfileController`, profile views |

## Current Codebase Risks to Track During Implementation
- status enum mismatch with runtime `expired`
- field naming inconsistencies (`firstname` vs `first_name`)
- upload path inconsistencies
- mixed direct SQL and rendering in current pages
- relationship inference via `reference_code` only
- duplicated status/prerequisite logic spread across endpoints and views

## Definition of Ready for Coding
Implementation should begin only when:
- MVC folder structure is accepted
- workflow rules are approved
- database semantics are accepted as current source of truth
- known inconsistencies are documented and consciously handled during implementation

## Summary
This plan stages the implementation from infrastructure to workflow-sensitive modules, preserving the current business behavior while moving the system into a maintainable PHP/MySQL MVC structure.
