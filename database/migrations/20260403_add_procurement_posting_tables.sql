USE `bps`;

CREATE TABLE IF NOT EXISTS `parent_procurement` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `reference_number` VARCHAR(50) NOT NULL,
    `procurement_title` VARCHAR(255) NOT NULL,
    `abc` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `mode_of_procurement` ENUM(
        'competitive_bidding',
        'limited_source_bidding',
        'competitive_dialogue',
        'unsolicited_offer_with_bid_matching',
        'direct_contracting',
        'direct_acquisition',
        'repeat_order',
        'small_value_procurement',
        'direct_sales',
        'direct_procurement_for_science_technology_and_innovation',
        'procurement_of_agricultural_and_fishery_products',
        'negotiated_procurement'
    ) NOT NULL,
    `posting_date` DATETIME NOT NULL,
    `bid_submission_deadline` DATETIME NOT NULL,
    `description` TEXT NOT NULL,
    `status` ENUM('pending', 'active', 'expired', 'archived') NOT NULL DEFAULT 'pending',
    `current_stage` ENUM('draft', 'bid_notice', 'resolution', 'award', 'contract', 'notice_to_proceed') NOT NULL DEFAULT 'draft',
    `is_archived` TINYINT(1) NOT NULL DEFAULT 0,
    `archived_at` DATETIME NULL,
    `region` VARCHAR(20) NOT NULL,
    `branch` VARCHAR(100) NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `updated_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_parent_procurement_reference_number` (`reference_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `bid_notices` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `document_type` VARCHAR(50) NOT NULL DEFAULT 'bid_notice',
    `sequence_stage` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `posted_at` DATETIME NOT NULL,
    `is_locked` TINYINT(1) NOT NULL DEFAULT 0,
    `locked_at` DATETIME NULL,
    `lock_reason` VARCHAR(255) NULL,
    `is_reopened` TINYINT(1) NOT NULL DEFAULT 0,
    `reopened_at` DATETIME NULL,
    `reopened_by` INT UNSIGNED NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `updated_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_bid_notices_parent` (`parent_procurement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `supplemental_bid_bulletins` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `document_type` VARCHAR(50) NOT NULL DEFAULT 'supplemental_bid_bulletin',
    `sequence_stage` TINYINT UNSIGNED NOT NULL DEFAULT 2,
    `posted_at` DATETIME NOT NULL,
    `is_locked` TINYINT(1) NOT NULL DEFAULT 0,
    `locked_at` DATETIME NULL,
    `lock_reason` VARCHAR(255) NULL,
    `is_reopened` TINYINT(1) NOT NULL DEFAULT 0,
    `reopened_at` DATETIME NULL,
    `reopened_by` INT UNSIGNED NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `updated_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `resolutions` LIKE `bid_notices`;
ALTER TABLE `resolutions`
    DROP INDEX `uq_bid_notices_parent`,
    ADD UNIQUE KEY `uq_resolutions_parent` (`parent_procurement_id`);
ALTER TABLE `resolutions`
    MODIFY COLUMN `document_type` VARCHAR(50) NOT NULL DEFAULT 'resolution',
    MODIFY COLUMN `sequence_stage` TINYINT UNSIGNED NOT NULL DEFAULT 3;

CREATE TABLE IF NOT EXISTS `awards` LIKE `bid_notices`;
ALTER TABLE `awards`
    DROP INDEX `uq_bid_notices_parent`,
    ADD UNIQUE KEY `uq_awards_parent` (`parent_procurement_id`);
ALTER TABLE `awards`
    MODIFY COLUMN `document_type` VARCHAR(50) NOT NULL DEFAULT 'award',
    MODIFY COLUMN `sequence_stage` TINYINT UNSIGNED NOT NULL DEFAULT 4;

CREATE TABLE IF NOT EXISTS `contracts` LIKE `bid_notices`;
ALTER TABLE `contracts`
    DROP INDEX `uq_bid_notices_parent`,
    ADD UNIQUE KEY `uq_contracts_parent` (`parent_procurement_id`);
ALTER TABLE `contracts`
    MODIFY COLUMN `document_type` VARCHAR(50) NOT NULL DEFAULT 'contract',
    MODIFY COLUMN `sequence_stage` TINYINT UNSIGNED NOT NULL DEFAULT 5;

CREATE TABLE IF NOT EXISTS `notices_to_proceed` LIKE `bid_notices`;
ALTER TABLE `notices_to_proceed`
    DROP INDEX `uq_bid_notices_parent`,
    ADD UNIQUE KEY `uq_notices_to_proceed_parent` (`parent_procurement_id`);
ALTER TABLE `notices_to_proceed`
    MODIFY COLUMN `document_type` VARCHAR(50) NOT NULL DEFAULT 'notice_to_proceed',
    MODIFY COLUMN `sequence_stage` TINYINT UNSIGNED NOT NULL DEFAULT 6;

CREATE TABLE IF NOT EXISTS `procurement_activity_logs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `document_type` VARCHAR(50) NOT NULL,
    `document_id` INT UNSIGNED NULL,
    `sequence_stage` TINYINT UNSIGNED NOT NULL,
    `action_type` ENUM('create', 'edit', 'lock', 'reopen', 'archive', 'unarchive') NOT NULL,
    `acted_by` INT UNSIGNED NOT NULL,
    `action_note` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `parent_procurement` (
    `reference_number`,
    `procurement_title`,
    `abc`,
    `mode_of_procurement`,
    `posting_date`,
    `bid_submission_deadline`,
    `description`,
    `status`,
    `current_stage`,
    `is_archived`,
    `archived_at`,
    `region`,
    `branch`,
    `created_by`,
    `updated_by`,
    `created_at`,
    `updated_at`
)
SELECT
    n.`reference_code`,
    n.`title`,
    0.00,
    n.`procurement_type`,
    n.`start_date`,
    n.`end_date`,
    n.`description`,
    n.`status`,
    CASE
        WHEN EXISTS (SELECT 1 FROM `notices` nx WHERE nx.`reference_code` = n.`reference_code` AND nx.`type` = 'proceed') THEN 'notice_to_proceed'
        WHEN EXISTS (SELECT 1 FROM `notices` nx WHERE nx.`reference_code` = n.`reference_code` AND nx.`type` = 'contract') THEN 'contract'
        WHEN EXISTS (SELECT 1 FROM `notices` nx WHERE nx.`reference_code` = n.`reference_code` AND nx.`type` = 'award') THEN 'award'
        WHEN EXISTS (SELECT 1 FROM `notices` nx WHERE nx.`reference_code` = n.`reference_code` AND nx.`type` = 'resolution') THEN 'resolution'
        ELSE 'bid_notice'
    END,
    n.`is_archived`,
    n.`archived_at`,
    n.`region`,
    n.`branch`,
    n.`uploaded_by`,
    n.`uploaded_by`,
    n.`upload_date`,
    n.`upload_date`
FROM `notices` n
WHERE n.`type` = 'bid'
  AND NOT EXISTS (
      SELECT 1
      FROM `parent_procurement` p
      WHERE p.`reference_number` = n.`reference_code`
  );

INSERT INTO `bid_notices` (
    `parent_procurement_id`,
    `title`,
    `description`,
    `file_path`,
    `document_type`,
    `sequence_stage`,
    `posted_at`,
    `is_locked`,
    `locked_at`,
    `lock_reason`,
    `is_reopened`,
    `reopened_at`,
    `reopened_by`,
    `created_by`,
    `updated_by`,
    `created_at`,
    `updated_at`
)
SELECT
    p.`id`,
    n.`title`,
    n.`description`,
    n.`file_path`,
    'bid_notice',
    1,
    n.`start_date`,
    CASE WHEN EXISTS (SELECT 1 FROM `notices` nx WHERE nx.`reference_code` = n.`reference_code` AND nx.`type` IN ('resolution', 'award', 'contract', 'proceed')) THEN 1 ELSE 0 END,
    CASE WHEN EXISTS (SELECT 1 FROM `notices` nx WHERE nx.`reference_code` = n.`reference_code` AND nx.`type` IN ('resolution', 'award', 'contract', 'proceed')) THEN NOW() ELSE NULL END,
    CASE WHEN EXISTS (SELECT 1 FROM `notices` nx WHERE nx.`reference_code` = n.`reference_code` AND nx.`type` IN ('resolution', 'award', 'contract', 'proceed')) THEN 'Locked by migrated downstream posting.' ELSE NULL END,
    0,
    NULL,
    NULL,
    n.`uploaded_by`,
    n.`uploaded_by`,
    n.`upload_date`,
    n.`upload_date`
FROM `notices` n
INNER JOIN `parent_procurement` p ON p.`reference_number` = n.`reference_code`
WHERE n.`type` = 'bid'
  AND NOT EXISTS (
      SELECT 1
      FROM `bid_notices` b
      WHERE b.`parent_procurement_id` = p.`id`
  );

INSERT INTO `supplemental_bid_bulletins` (
    `parent_procurement_id`,
    `title`,
    `description`,
    `file_path`,
    `document_type`,
    `sequence_stage`,
    `posted_at`,
    `is_locked`,
    `locked_at`,
    `lock_reason`,
    `is_reopened`,
    `reopened_at`,
    `reopened_by`,
    `created_by`,
    `updated_by`,
    `created_at`,
    `updated_at`
)
SELECT
    p.`id`,
    n.`title`,
    n.`description`,
    n.`file_path`,
    'supplemental_bid_bulletin',
    2,
    n.`start_date`,
    CASE WHEN EXISTS (SELECT 1 FROM `notices` nx WHERE nx.`reference_code` = n.`reference_code` AND nx.`type` = 'resolution') OR n.`end_date` < NOW() THEN 1 ELSE 0 END,
    CASE WHEN EXISTS (SELECT 1 FROM `notices` nx WHERE nx.`reference_code` = n.`reference_code` AND nx.`type` = 'resolution') OR n.`end_date` < NOW() THEN NOW() ELSE NULL END,
    CASE WHEN EXISTS (SELECT 1 FROM `notices` nx WHERE nx.`reference_code` = n.`reference_code` AND nx.`type` = 'resolution') THEN 'Locked after migrated resolution posting.' WHEN n.`end_date` < NOW() THEN 'Locked after bid submission deadline.' ELSE NULL END,
    0,
    NULL,
    NULL,
    n.`uploaded_by`,
    n.`uploaded_by`,
    n.`upload_date`,
    n.`upload_date`
FROM `notices` n
INNER JOIN `parent_procurement` p ON p.`reference_number` = n.`reference_code`
WHERE n.`type` = 'sbb'
  AND NOT EXISTS (
      SELECT 1
      FROM `supplemental_bid_bulletins` s
      WHERE s.`parent_procurement_id` = p.`id`
        AND s.`title` = n.`title`
        AND s.`posted_at` = n.`start_date`
  );

INSERT INTO `resolutions` (`parent_procurement_id`, `title`, `description`, `file_path`, `document_type`, `sequence_stage`, `posted_at`, `is_locked`, `locked_at`, `lock_reason`, `is_reopened`, `reopened_at`, `reopened_by`, `created_by`, `updated_by`, `created_at`, `updated_at`)
SELECT p.`id`, n.`title`, n.`description`, n.`file_path`, 'resolution', 3, n.`start_date`,
    CASE WHEN EXISTS (SELECT 1 FROM `notices` nx WHERE nx.`reference_code` = n.`reference_code` AND nx.`type` IN ('award', 'contract', 'proceed')) THEN 1 ELSE 0 END,
    CASE WHEN EXISTS (SELECT 1 FROM `notices` nx WHERE nx.`reference_code` = n.`reference_code` AND nx.`type` IN ('award', 'contract', 'proceed')) THEN NOW() ELSE NULL END,
    CASE WHEN EXISTS (SELECT 1 FROM `notices` nx WHERE nx.`reference_code` = n.`reference_code` AND nx.`type` IN ('award', 'contract', 'proceed')) THEN 'Locked by migrated downstream posting.' ELSE NULL END,
    0, NULL, NULL, n.`uploaded_by`, n.`uploaded_by`, n.`upload_date`, n.`upload_date`
FROM `notices` n
INNER JOIN `parent_procurement` p ON p.`reference_number` = n.`reference_code`
WHERE n.`type` = 'resolution'
  AND NOT EXISTS (
      SELECT 1
      FROM `resolutions` r
      WHERE r.`parent_procurement_id` = p.`id`
  );

INSERT INTO `awards` (`parent_procurement_id`, `title`, `description`, `file_path`, `document_type`, `sequence_stage`, `posted_at`, `is_locked`, `locked_at`, `lock_reason`, `is_reopened`, `reopened_at`, `reopened_by`, `created_by`, `updated_by`, `created_at`, `updated_at`)
SELECT p.`id`, n.`title`, n.`description`, n.`file_path`, 'award', 4, n.`start_date`,
    CASE WHEN EXISTS (SELECT 1 FROM `notices` nx WHERE nx.`reference_code` = n.`reference_code` AND nx.`type` IN ('contract', 'proceed')) THEN 1 ELSE 0 END,
    CASE WHEN EXISTS (SELECT 1 FROM `notices` nx WHERE nx.`reference_code` = n.`reference_code` AND nx.`type` IN ('contract', 'proceed')) THEN NOW() ELSE NULL END,
    CASE WHEN EXISTS (SELECT 1 FROM `notices` nx WHERE nx.`reference_code` = n.`reference_code` AND nx.`type` IN ('contract', 'proceed')) THEN 'Locked by migrated downstream posting.' ELSE NULL END,
    0, NULL, NULL, n.`uploaded_by`, n.`uploaded_by`, n.`upload_date`, n.`upload_date`
FROM `notices` n
INNER JOIN `parent_procurement` p ON p.`reference_number` = n.`reference_code`
WHERE n.`type` = 'award'
  AND NOT EXISTS (
      SELECT 1
      FROM `awards` a
      WHERE a.`parent_procurement_id` = p.`id`
  );

INSERT INTO `contracts` (`parent_procurement_id`, `title`, `description`, `file_path`, `document_type`, `sequence_stage`, `posted_at`, `is_locked`, `locked_at`, `lock_reason`, `is_reopened`, `reopened_at`, `reopened_by`, `created_by`, `updated_by`, `created_at`, `updated_at`)
SELECT p.`id`, n.`title`, n.`description`, n.`file_path`, 'contract', 5, n.`start_date`,
    CASE WHEN EXISTS (SELECT 1 FROM `notices` nx WHERE nx.`reference_code` = n.`reference_code` AND nx.`type` = 'proceed') THEN 1 ELSE 0 END,
    CASE WHEN EXISTS (SELECT 1 FROM `notices` nx WHERE nx.`reference_code` = n.`reference_code` AND nx.`type` = 'proceed') THEN NOW() ELSE NULL END,
    CASE WHEN EXISTS (SELECT 1 FROM `notices` nx WHERE nx.`reference_code` = n.`reference_code` AND nx.`type` = 'proceed') THEN 'Locked by migrated notice to proceed.' ELSE NULL END,
    0, NULL, NULL, n.`uploaded_by`, n.`uploaded_by`, n.`upload_date`, n.`upload_date`
FROM `notices` n
INNER JOIN `parent_procurement` p ON p.`reference_number` = n.`reference_code`
WHERE n.`type` = 'contract'
  AND NOT EXISTS (
      SELECT 1
      FROM `contracts` c
      WHERE c.`parent_procurement_id` = p.`id`
  );

INSERT INTO `notices_to_proceed` (`parent_procurement_id`, `title`, `description`, `file_path`, `document_type`, `sequence_stage`, `posted_at`, `is_locked`, `locked_at`, `lock_reason`, `is_reopened`, `reopened_at`, `reopened_by`, `created_by`, `updated_by`, `created_at`, `updated_at`)
SELECT p.`id`, n.`title`, n.`description`, n.`file_path`, 'notice_to_proceed', 6, n.`start_date`,
    0, NULL, NULL, 0, NULL, NULL, n.`uploaded_by`, n.`uploaded_by`, n.`upload_date`, n.`upload_date`
FROM `notices` n
INNER JOIN `parent_procurement` p ON p.`reference_number` = n.`reference_code`
WHERE n.`type` = 'proceed'
  AND NOT EXISTS (
      SELECT 1
      FROM `notices_to_proceed` ntp
      WHERE ntp.`parent_procurement_id` = p.`id`
  );

INSERT INTO `procurement_activity_logs` (`parent_procurement_id`, `document_type`, `document_id`, `sequence_stage`, `action_type`, `acted_by`, `action_note`, `created_at`)
SELECT p.`id`, 'bid_notice', b.`id`, 1, 'create', b.`created_by`, 'Migrated bid notice activity.', b.`created_at`
FROM `bid_notices` b
INNER JOIN `parent_procurement` p ON p.`id` = b.`parent_procurement_id`
WHERE NOT EXISTS (
    SELECT 1
    FROM `procurement_activity_logs` l
    WHERE l.`parent_procurement_id` = p.`id`
      AND l.`document_type` = 'bid_notice'
      AND l.`action_type` = 'create'
);
