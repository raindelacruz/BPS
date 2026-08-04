# System Design

## Purpose
This document defines the target PHP/MySQL MVC application design for the eBPS system based on the current implemented behavior of the existing codebase. It is intended to serve as the architectural source of truth before implementation.

## System Scope
The system manages:
- internal user registration, verification, login, and session-based dashboard access
- creation and lifecycle management of bid notices
- creation of related procurement notices linked by `reference_code`
- prerequisite enforcement for the notice workflow chain
- pending-notice editing, deletion, and grouped archive/unarchive
- public listing of active bid notices and their related documents
- admin management of users

## Architectural Style
- Language: PHP
- Database: MySQL / MariaDB
- Pattern: MVC
- Supporting layers: Services, Helpers, Repositories/Model methods

## MVC Layers

### Models
Models represent database-backed entities and persistence logic.

Core models:
- `User`
- `Notice`
- `EmailChangeRequest`

Model responsibilities:
- map to tables
- encapsulate query logic
- expose entity-specific persistence methods
- avoid request/session/view handling

### Controllers
Controllers receive requests, call services/models, and return views or JSON.

Core controllers:
- `AuthController`
- `VerificationController`
- `DashboardController`
- `NoticeController`
- `WorkflowValidationController`
- `ArchiveController`
- `PublicNoticeController`
- `UserController`
- `ProfileController`

Controller responsibilities:
- request handling
- authorization guard usage
- orchestration of services
- selection of views/responses

### Views
Views render UI only.

Main view groups:
- auth views
- dashboard views
- notice management views
- user management views
- profile views
- public notice views

View responsibilities:
- render data prepared by controller
- keep presentation separate from business rules and SQL

### Services
Services centralize reusable business logic.

Core services:
- `AuthService`
- `NoticeWorkflowService`
- `NoticeValidationService`
- `PrerequisiteService`
- `ArchiveService`
- `DateStatusService`
- `FileUploadService`
- `DashboardQueryService`
- `PublicNoticeQueryService`
- `UserManagementService`
- `EmailService`

Service responsibilities:
- workflow logic
- validation rules
- status calculation
- file handling
- transactional orchestration

### Helpers
Helpers provide cross-cutting support utilities.

Core helpers:
- `SessionHelper`
- `ResponseHelper`
- `SecurityHelper`
- `ViewHelper`

Helper responsibilities:
- session access
- standardized responses
- guard and ownership checks
- formatting utilities

## Functional Modules

### Authentication Module
Handles:
- registration
- login/logout
- registration email verification
- account activation checks

Primary components:
- `AuthController`
- `VerificationController`
- `User`
- `AuthService`
- `EmailService`

### Notice Management Module
Handles:
- bid creation
- related notice creation
- pending notice editing
- deletion
- listing of pending/active/archived notices

Primary components:
- `NoticeController`
- `Notice`
- `NoticeValidationService`
- `NoticeWorkflowService`
- `FileUploadService`

### Workflow Validation Module
Handles:
- prerequisite checks
- duplicate checks
- eligible parent bid lookup

Primary components:
- `WorkflowValidationController`
- `PrerequisiteService`
- `Notice`

### Archive Module
Handles:
- archive eligibility
- archive/unarchive by `reference_code`
- grouped status recalculation

Primary components:
- `ArchiveController`
- `ArchiveService`
- `Notice`

### Public Notice Module
Handles:
- public bid listing
- search/filter
- related notice grouping

Primary components:
- `PublicNoticeController`
- `PublicNoticeQueryService`
- `Notice`

### User Management Module
Handles:
- user listing
- role/region/email/username updates
- active/inactive toggling
- user deletion and notice reassignment

Primary components:
- `UserController`
- `UserManagementService`
- `User`
- `Notice`

### Profile Module
Handles:
- current user password update
- current user account display

Primary components:
- `ProfileController`
- `User`
- `AuthService`

## Request Flow

### Standard MVC Request Flow
1. HTTP request enters front controller.
2. Router resolves route to controller action.
3. Controller validates access and request intent.
4. Controller delegates business rules to services.
5. Services call models for data access.
6. Models read/write MySQL tables.
7. Controller returns:
- rendered view
- JSON payload
- redirect/flash response

### Example: Create Bid Notice
1. Route: `POST /notices`
2. `NoticeController@store`
3. `SecurityHelper` confirms authenticated user
4. `NoticeValidationService` validates required fields and PDF
5. `NoticeWorkflowService` determines root notice behavior
6. `FileUploadService` stores PDF
7. `Notice` inserts row into `notices`
8. Controller returns JSON success or redirect

### Example: Create Award Notice
1. Route: `POST /notices`
2. `NoticeController@store`
3. `PrerequisiteService` checks:
- selected active bid exists
- same-reference `resolution` exists
- same-reference `award` does not already exist
4. `NoticeWorkflowService` derives title/reference/procurement type
5. `FileUploadService` stores PDF
6. `Notice` inserts row
7. Controller returns result

### Example: Archive Notice Set
1. Route: `POST /notices/{id}/archive`
2. `ArchiveController@archive`
3. Load notice by id
4. Ownership check
5. `ArchiveService` checks:
- active `proceed` exists for reference code, or
- notice is expired
6. `Notice` updates all rows for same `reference_code`
7. Controller returns JSON result

## Folder Structure

```text
project-root/
  app/
    controllers/
      AuthController.php
      VerificationController.php
      DashboardController.php
      NoticeController.php
      WorkflowValidationController.php
      ArchiveController.php
      PublicNoticeController.php
      UserController.php
      ProfileController.php
    models/
      User.php
      Notice.php
      EmailChangeRequest.php
    services/
      AuthService.php
      NoticeWorkflowService.php
      NoticeValidationService.php
      PrerequisiteService.php
      ArchiveService.php
      DateStatusService.php
      FileUploadService.php
      DashboardQueryService.php
      PublicNoticeQueryService.php
      UserManagementService.php
      EmailService.php
    helpers/
      SessionHelper.php
      ResponseHelper.php
      SecurityHelper.php
      ViewHelper.php
    views/
      layouts/
      partials/
      auth/
      dashboard/
      notice/
      user/
      profile/
      public/
    config/
      app.php
      database.php
      mail.php
  bootstrap/
  routes/
    web.php
    api.php
  public/
    index.php
    assets/
  storage/
    uploads/
      notices/
    temp/
  vendor/
```

## Folder Responsibilities
- `app/controllers`: request handling and response coordination
- `app/models`: entity persistence and table interaction
- `app/services`: workflow and business rules
- `app/helpers`: shared framework-like utilities
- `app/views`: presentation templates
- `app/config`: application and infrastructure configuration
- `routes`: route definitions
- `public`: web entry point and public assets
- `storage/uploads`: persisted user-uploaded PDFs
- `storage/temp`: temporary files if preview/staging remains part of implementation

## Separation of Concerns Rules
- SQL should not appear in views
- request/session logic should not live in models
- controllers should not contain heavy workflow logic
- file handling should be isolated in a service
- prerequisite and status logic should be reusable, not duplicated across pages

## Implementation Notes
- The MVC implementation must preserve the current workflow semantics:
  - `bid` is the root notice
  - related notices are associated by `reference_code`
  - archive/unarchive affects all notices sharing a `reference_code`
  - editing remains restricted to `pending`
  - public listing continues to show only active, non-archived bids as primary items
