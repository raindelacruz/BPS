USE `bps`;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `email_change_requests`;
TRUNCATE TABLE `svp_ntps`;
TRUNCATE TABLE `svp_contracts`;
TRUNCATE TABLE `svp_awards`;
TRUNCATE TABLE `svp_evaluation_items`;
TRUNCATE TABLE `svp_evaluations`;
TRUNCATE TABLE `svp_quotations`;
TRUNCATE TABLE `svp_suppliers`;
TRUNCATE TABLE `svp_rfq_postings`;
TRUNCATE TABLE `svp_rfqs`;
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

-- Seed users only. Sample procurement/demo records were removed intentionally.
