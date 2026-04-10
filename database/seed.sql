USE `bps`;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `email_change_requests`;
TRUNCATE TABLE `procurement_activity_logs`;
TRUNCATE TABLE `notices_to_proceed`;
TRUNCATE TABLE `contracts`;
TRUNCATE TABLE `awards`;
TRUNCATE TABLE `resolutions`;
TRUNCATE TABLE `supplemental_bid_bulletins`;
TRUNCATE TABLE `bid_notices`;
TRUNCATE TABLE `parent_procurement`;
TRUNCATE TABLE `users`;
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO `users` (
    `id`,
    `username`,
    `firstname`,
    `middle_initial`,
    `lastname`,
    `region`,
    `branch`,
    `password`,
    `role`,
    `email`,
    `verification_code`,
    `token_expiry`,
    `is_verified`,
    `is_active`
) VALUES
(
    1,
    'secretariat1',
    'Secretariat',
    'A',
    'Officer',
    'Central Office',
    'Administrative and General Services Department',
    '$2y$10$xU0mMJ/okV9cheDamfxGfumXclp/JZHPCTEKLgNIICq2VT0HJpiBG',
    'author',
    'secretariat.officer@nfa.gov.ph',
    NULL,
    NULL,
    1,
    1
),
(
    2,
    'sysadmin',
    'System',
    NULL,
    'Administrator',
    'Central Office',
    'Administrative and General Services Department',
    '$2y$10$XgllwcfsWZj7qOq5SghaF.N7AanuAVD4ex/YhdZFNa2inp5I4./6y',
    'admin',
    'system.admin@nfa.gov.ph',
    NULL,
    NULL,
    1,
    1
);

INSERT INTO `parent_procurement` (
    `id`,
    `reference_number`,
    `procurement_title`,
    `abc`,
    `mode_of_procurement`,
    `posting_date`,
    `bid_submission_deadline`,
    `description`,
    `posting_status`,
    `current_stage`,
    `archived_at`,
    `archive_reason`,
    `archived_by`,
    `archive_approval_reference`,
    `archive_approved_by`,
    `archive_approved_at`,
    `region`,
    `branch`,
    `created_by`,
    `updated_by`
) VALUES
(
    1,
    'BAC-2026-0401',
    'Procurement of Property Information System',
    12000000.00,
    'competitive_bidding',
    '2026-04-20 09:00:00',
    '2026-05-02 09:00:00',
    'Official public procurement posting for the Property Information System.',
    'scheduled',
    'bid_notice',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'Central Office',
    'Administrative and General Services Department',
    1,
    1
),
(
    2,
    'BAC-2026-0402',
    'Procurement of Human Resource Information System',
    14000000.00,
    'competitive_bidding',
    '2026-04-01 09:00:00',
    '2026-04-15 09:00:00',
    'Official public procurement posting for the Human Resource Information System.',
    'closed',
    'notice_to_proceed',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'Central Office',
    'Administrative and General Services Department',
    1,
    1
),
(
    3,
    'BAC-2026-0403',
    'Procurement of Financial Management Information System',
    16000000.00,
    'competitive_bidding',
    '2026-03-01 09:00:00',
    '2026-03-18 09:00:00',
    'Official public procurement posting for the Financial Management Information System.',
    'archived',
    'notice_to_proceed',
    '2026-04-05 10:00:00',
    'Lifecycle completed and archived for records retention.',
    2,
    'ADM-ARCH-2026-001',
    2,
    '2026-04-05 10:00:00',
    'Central Office',
    'Administrative and General Services Department',
    1,
    2
);

INSERT INTO `bid_notices` (
    `id`,
    `parent_procurement_id`,
    `title`,
    `description`,
    `file_path`,
    `file_hash`,
    `document_type`,
    `sequence_stage`,
    `posted_at`,
    `created_by`,
    `updated_by`
) VALUES
(
    1,
    1,
    'Procurement of Property Information System',
    'Official Bid Notice for the Property Information System.',
    'storage/uploads/notices/sample-bid-001.pdf',
    REPEAT('a', 64),
    'bid_notice',
    1,
    '2026-04-20 09:00:00',
    1,
    1
),
(
    2,
    2,
    'Procurement of Human Resource Information System',
    'Official Bid Notice for the Human Resource Information System.',
    'storage/uploads/notices/sample-bid-002.pdf',
    REPEAT('b', 64),
    'bid_notice',
    1,
    '2026-04-01 09:00:00',
    1,
    1
),
(
    3,
    3,
    'Procurement of Financial Management Information System',
    'Official Bid Notice for the Financial Management Information System.',
    'storage/uploads/notices/sample-bid-003.pdf',
    REPEAT('c', 64),
    'bid_notice',
    1,
    '2026-03-01 09:00:00',
    1,
    1
);

INSERT INTO `supplemental_bid_bulletins` (
    `id`,
    `parent_procurement_id`,
    `title`,
    `description`,
    `file_path`,
    `file_hash`,
    `document_type`,
    `sequence_stage`,
    `posted_at`,
    `created_by`,
    `updated_by`
) VALUES
(
    1,
    2,
    'HRIS Supplemental/Bid Bulletin No. 1',
    'Clarification bulletin issued before the bid deadline.',
    'storage/uploads/notices/sample-sbb-001.pdf',
    REPEAT('d', 64),
    'supplemental_bid_bulletin',
    2,
    '2026-04-10 13:00:00',
    1,
    1
),
(
    2,
    3,
    'FMIS Supplemental/Bid Bulletin No. 1',
    'Clarification bulletin issued before the bid deadline.',
    'storage/uploads/notices/sample-sbb-002.pdf',
    REPEAT('e', 64),
    'supplemental_bid_bulletin',
    2,
    '2026-03-10 13:00:00',
    1,
    1
);

INSERT INTO `resolutions` (
    `id`,
    `parent_procurement_id`,
    `title`,
    `description`,
    `file_path`,
    `file_hash`,
    `document_type`,
    `sequence_stage`,
    `posted_at`,
    `created_by`,
    `updated_by`
) VALUES
(
    1,
    2,
    'BAC Resolution for HRIS',
    'Resolution issued after bid closing.',
    'storage/uploads/notices/sample-resolution-001.pdf',
    REPEAT('f', 64),
    'resolution',
    3,
    '2026-04-16 10:00:00',
    1,
    1
),
(
    2,
    3,
    'BAC Resolution for FMIS',
    'Resolution issued after bid closing.',
    'storage/uploads/notices/sample-resolution-002.pdf',
    REPEAT('1', 64),
    'resolution',
    3,
    '2026-03-19 10:00:00',
    1,
    1
);

INSERT INTO `awards` (
    `id`,
    `parent_procurement_id`,
    `title`,
    `description`,
    `file_path`,
    `file_hash`,
    `document_type`,
    `sequence_stage`,
    `posted_at`,
    `created_by`,
    `updated_by`
) VALUES
(
    1,
    2,
    'Notice of Award for HRIS',
    'Award issued after the resolution.',
    'storage/uploads/notices/sample-award-001.pdf',
    REPEAT('2', 64),
    'award',
    4,
    '2026-04-17 09:00:00',
    1,
    1
),
(
    2,
    3,
    'Notice of Award for FMIS',
    'Award issued after the resolution.',
    'storage/uploads/notices/sample-award-002.pdf',
    REPEAT('3', 64),
    'award',
    4,
    '2026-03-20 09:00:00',
    1,
    1
);

INSERT INTO `contracts` (
    `id`,
    `parent_procurement_id`,
    `title`,
    `description`,
    `file_path`,
    `file_hash`,
    `document_type`,
    `sequence_stage`,
    `posted_at`,
    `created_by`,
    `updated_by`
) VALUES
(
    1,
    2,
    'Contract for HRIS',
    'Contract issued after award.',
    'storage/uploads/notices/sample-contract-001.pdf',
    REPEAT('4', 64),
    'contract',
    5,
    '2026-04-18 15:00:00',
    1,
    1
),
(
    2,
    3,
    'Contract for FMIS',
    'Contract issued after award.',
    'storage/uploads/notices/sample-contract-002.pdf',
    REPEAT('5', 64),
    'contract',
    5,
    '2026-03-22 15:00:00',
    1,
    1
);

INSERT INTO `notices_to_proceed` (
    `id`,
    `parent_procurement_id`,
    `title`,
    `description`,
    `file_path`,
    `file_hash`,
    `document_type`,
    `sequence_stage`,
    `posted_at`,
    `created_by`,
    `updated_by`
) VALUES
(
    1,
    2,
    'Notice to Proceed for HRIS',
    'Notice to Proceed issued after contract execution.',
    'storage/uploads/notices/sample-ntp-001.pdf',
    REPEAT('6', 64),
    'notice_to_proceed',
    6,
    '2026-04-21 08:00:00',
    1,
    1
),
(
    2,
    3,
    'Notice to Proceed for FMIS',
    'Notice to Proceed issued after contract execution.',
    'storage/uploads/notices/sample-ntp-002.pdf',
    REPEAT('7', 64),
    'notice_to_proceed',
    6,
    '2026-03-24 08:00:00',
    1,
    1
);

INSERT INTO `procurement_activity_logs` (
    `parent_procurement_id`,
    `user_id`,
    `action_type`,
    `document_type`,
    `document_id`,
    `before_snapshot`,
    `after_snapshot`,
    `reason`,
    `file_hash`,
    `approval_reference`
) VALUES
(
    1,
    1,
    'create_parent',
    'bid_notice',
    1,
    NULL,
    JSON_OBJECT('reference_number', 'BAC-2026-0401', 'posting_status', 'scheduled'),
    'Official public procurement posting created.',
    REPEAT('a', 64),
    NULL
),
(
    2,
    1,
    'create_document',
    'notice_to_proceed',
    1,
    NULL,
    JSON_OBJECT('document_type', 'notice_to_proceed', 'posted_at', '2026-04-21 08:00:00'),
    'Official signed procurement document posted.',
    REPEAT('6', 64),
    NULL
),
(
    3,
    2,
    'archive',
    'notice_to_proceed',
    NULL,
    JSON_OBJECT('posting_status', 'closed'),
    JSON_OBJECT('posting_status', 'archived', 'archive_reason', 'Lifecycle completed and archived for records retention.'),
    'Lifecycle completed and archived for records retention.',
    NULL,
    'ADM-ARCH-2026-001'
);
