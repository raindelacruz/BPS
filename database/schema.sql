CREATE DATABASE IF NOT EXISTS `bps`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `bps`;

DROP TABLE IF EXISTS `email_change_requests`;
DROP TABLE IF EXISTS `procurement_activity_logs`;
DROP TABLE IF EXISTS `notices_to_proceed`;
DROP TABLE IF EXISTS `contracts`;
DROP TABLE IF EXISTS `awards`;
DROP TABLE IF EXISTS `resolutions`;
DROP TABLE IF EXISTS `supplemental_bid_bulletins`;
DROP TABLE IF EXISTS `bid_notices`;
DROP TABLE IF EXISTS `parent_procurement`;
DROP TABLE IF EXISTS `notices`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `firstname` VARCHAR(255) NOT NULL,
    `middle_initial` VARCHAR(1) NULL,
    `lastname` VARCHAR(255) NOT NULL,
    `region` VARCHAR(50) NOT NULL,
    `branch` VARCHAR(100) NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('author', 'admin') NOT NULL DEFAULT 'author',
    `email` VARCHAR(255) NOT NULL,
    `verification_token` VARCHAR(64) NULL,
    `verification_code` VARCHAR(6) NULL,
    `token_expiry` DATETIME NULL,
    `is_verified` TINYINT(1) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_username` (`username`),
    UNIQUE KEY `uq_users_email` (`email`),
    KEY `idx_users_role` (`role`),
    KEY `idx_users_region` (`region`),
    KEY `idx_users_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notices` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `reference_code` VARCHAR(50) NOT NULL,
    `type` ENUM('bid', 'sbb', 'resolution', 'award', 'contract', 'proceed', 'rfq') NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `upload_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `start_date` DATETIME NOT NULL,
    `end_date` DATETIME NOT NULL,
    `uploaded_by` INT UNSIGNED NOT NULL,
    `description` TEXT NOT NULL,
    `is_archived` TINYINT(1) NOT NULL DEFAULT 0,
    `status` ENUM('pending', 'active', 'expired', 'archived') NOT NULL DEFAULT 'pending',
    `region` VARCHAR(20) NOT NULL,
    `branch` VARCHAR(100) NULL,
    `procurement_type` ENUM(
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
    `archived_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `idx_notices_reference_code` (`reference_code`),
    KEY `idx_notices_type` (`type`),
    KEY `idx_notices_status` (`status`),
    KEY `idx_notices_archived` (`is_archived`),
    KEY `idx_notices_uploaded_by` (`uploaded_by`),
    KEY `idx_notices_reference_type_region` (`reference_code`, `type`, `region`),
    KEY `idx_notices_public_listing` (`type`, `status`, `is_archived`, `start_date`),
    CONSTRAINT `fk_notices_uploaded_by_users`
        FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `parent_procurement` (
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
    UNIQUE KEY `uq_parent_procurement_reference_number` (`reference_number`),
    KEY `idx_parent_procurement_status` (`status`),
    KEY `idx_parent_procurement_stage` (`current_stage`),
    KEY `idx_parent_procurement_region_branch` (`region`, `branch`),
    KEY `idx_parent_procurement_created_by` (`created_by`),
    CONSTRAINT `fk_parent_procurement_created_by_users`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_parent_procurement_updated_by_users`
        FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bid_notices` (
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
    UNIQUE KEY `uq_bid_notices_parent` (`parent_procurement_id`),
    KEY `idx_bid_notices_posted_at` (`posted_at`),
    CONSTRAINT `fk_bid_notices_parent_procurement`
        FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `fk_bid_notices_created_by_users`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_bid_notices_updated_by_users`
        FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_bid_notices_reopened_by_users`
        FOREIGN KEY (`reopened_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `supplemental_bid_bulletins` (
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
    PRIMARY KEY (`id`),
    KEY `idx_sbb_parent_procurement` (`parent_procurement_id`),
    KEY `idx_sbb_posted_at` (`posted_at`),
    CONSTRAINT `fk_sbb_parent_procurement`
        FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `fk_sbb_created_by_users`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_sbb_updated_by_users`
        FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_sbb_reopened_by_users`
        FOREIGN KEY (`reopened_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `resolutions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `document_type` VARCHAR(50) NOT NULL DEFAULT 'resolution',
    `sequence_stage` TINYINT UNSIGNED NOT NULL DEFAULT 3,
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
    UNIQUE KEY `uq_resolutions_parent` (`parent_procurement_id`),
    CONSTRAINT `fk_resolutions_parent_procurement`
        FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `fk_resolutions_created_by_users`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_resolutions_updated_by_users`
        FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_resolutions_reopened_by_users`
        FOREIGN KEY (`reopened_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `awards` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `document_type` VARCHAR(50) NOT NULL DEFAULT 'award',
    `sequence_stage` TINYINT UNSIGNED NOT NULL DEFAULT 4,
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
    UNIQUE KEY `uq_awards_parent` (`parent_procurement_id`),
    CONSTRAINT `fk_awards_parent_procurement`
        FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `fk_awards_created_by_users`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_awards_updated_by_users`
        FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_awards_reopened_by_users`
        FOREIGN KEY (`reopened_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contracts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `document_type` VARCHAR(50) NOT NULL DEFAULT 'contract',
    `sequence_stage` TINYINT UNSIGNED NOT NULL DEFAULT 5,
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
    UNIQUE KEY `uq_contracts_parent` (`parent_procurement_id`),
    CONSTRAINT `fk_contracts_parent_procurement`
        FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `fk_contracts_created_by_users`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_contracts_updated_by_users`
        FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_contracts_reopened_by_users`
        FOREIGN KEY (`reopened_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notices_to_proceed` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `document_type` VARCHAR(50) NOT NULL DEFAULT 'notice_to_proceed',
    `sequence_stage` TINYINT UNSIGNED NOT NULL DEFAULT 6,
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
    UNIQUE KEY `uq_notices_to_proceed_parent` (`parent_procurement_id`),
    CONSTRAINT `fk_ntp_parent_procurement`
        FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `fk_ntp_created_by_users`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_ntp_updated_by_users`
        FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_ntp_reopened_by_users`
        FOREIGN KEY (`reopened_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `procurement_activity_logs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `document_type` VARCHAR(50) NOT NULL,
    `document_id` INT UNSIGNED NULL,
    `sequence_stage` TINYINT UNSIGNED NOT NULL,
    `action_type` ENUM('create', 'edit', 'lock', 'reopen', 'archive', 'unarchive') NOT NULL,
    `acted_by` INT UNSIGNED NOT NULL,
    `action_note` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_procurement_activity_parent` (`parent_procurement_id`),
    KEY `idx_procurement_activity_action` (`action_type`),
    CONSTRAINT `fk_procurement_activity_parent_procurement`
        FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `fk_procurement_activity_acted_by_users`
        FOREIGN KEY (`acted_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `email_change_requests` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `current_email` VARCHAR(255) NOT NULL,
    `new_email` VARCHAR(255) NOT NULL,
    `token` VARCHAR(64) NOT NULL,
    `status` ENUM('pending', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_email_change_requests_token` (`token`),
    KEY `idx_email_change_requests_user_status` (`user_id`, `status`),
    CONSTRAINT `fk_email_change_requests_user_id_users`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
