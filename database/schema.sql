CREATE DATABASE IF NOT EXISTS `bps`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `bps`;

DROP TRIGGER IF EXISTS `tr_parent_procurement_no_delete`;
DROP TRIGGER IF EXISTS `tr_parent_procurement_guard_update`;
DROP TRIGGER IF EXISTS `tr_bid_notices_no_update`;
DROP TRIGGER IF EXISTS `tr_bid_notices_no_delete`;
DROP TRIGGER IF EXISTS `tr_sbb_no_update`;
DROP TRIGGER IF EXISTS `tr_sbb_no_delete`;
DROP TRIGGER IF EXISTS `tr_resolutions_no_update`;
DROP TRIGGER IF EXISTS `tr_resolutions_no_delete`;
DROP TRIGGER IF EXISTS `tr_awards_no_update`;
DROP TRIGGER IF EXISTS `tr_awards_no_delete`;
DROP TRIGGER IF EXISTS `tr_contracts_no_update`;
DROP TRIGGER IF EXISTS `tr_contracts_no_delete`;
DROP TRIGGER IF EXISTS `tr_ntp_no_update`;
DROP TRIGGER IF EXISTS `tr_ntp_no_delete`;
DROP TRIGGER IF EXISTS `tr_procurement_activity_logs_no_update`;
DROP TRIGGER IF EXISTS `tr_procurement_activity_logs_no_delete`;

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
    `posting_status` ENUM('scheduled', 'open', 'closed', 'archived') NOT NULL DEFAULT 'scheduled',
    `current_stage` ENUM('bid_notice', 'supplemental_bid_bulletin', 'resolution', 'award', 'contract', 'notice_to_proceed') NOT NULL DEFAULT 'bid_notice',
    `archived_at` DATETIME NULL,
    `archive_reason` VARCHAR(255) NULL,
    `archived_by` INT UNSIGNED NULL,
    `archive_approval_reference` VARCHAR(255) NULL,
    `archive_approved_by` INT UNSIGNED NULL,
    `archive_approved_at` DATETIME NULL,
    `region` VARCHAR(20) NOT NULL,
    `branch` VARCHAR(100) NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `updated_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_parent_procurement_reference_number` (`reference_number`),
    KEY `idx_parent_procurement_posting_status` (`posting_status`),
    KEY `idx_parent_procurement_stage` (`current_stage`),
    KEY `idx_parent_procurement_public_listing` (`posting_status`, `posting_date`, `bid_submission_deadline`),
    KEY `idx_parent_procurement_region_branch` (`region`, `branch`),
    KEY `idx_parent_procurement_created_by` (`created_by`),
    KEY `idx_parent_procurement_archived_by` (`archived_by`),
    KEY `idx_parent_procurement_archive_approved_by` (`archive_approved_by`),
    CONSTRAINT `fk_parent_procurement_created_by_users`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_parent_procurement_updated_by_users`
        FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_parent_procurement_archived_by_users`
        FOREIGN KEY (`archived_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_parent_procurement_archive_approved_by_users`
        FOREIGN KEY (`archive_approved_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bid_notices` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_hash` CHAR(64) NOT NULL,
    `document_type` VARCHAR(50) NOT NULL DEFAULT 'bid_notice',
    `sequence_stage` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `posted_at` DATETIME NOT NULL,
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
        ON DELETE RESTRICT,
    CONSTRAINT `fk_bid_notices_created_by_users`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_bid_notices_updated_by_users`
        FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `supplemental_bid_bulletins` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_hash` CHAR(64) NOT NULL,
    `document_type` VARCHAR(50) NOT NULL DEFAULT 'supplemental_bid_bulletin',
    `sequence_stage` TINYINT UNSIGNED NOT NULL DEFAULT 2,
    `posted_at` DATETIME NOT NULL,
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
        ON DELETE RESTRICT,
    CONSTRAINT `fk_sbb_created_by_users`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_sbb_updated_by_users`
        FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `resolutions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_hash` CHAR(64) NOT NULL,
    `document_type` VARCHAR(50) NOT NULL DEFAULT 'resolution',
    `sequence_stage` TINYINT UNSIGNED NOT NULL DEFAULT 3,
    `posted_at` DATETIME NOT NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `updated_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_resolutions_parent` (`parent_procurement_id`),
    CONSTRAINT `fk_resolutions_parent_procurement`
        FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_resolutions_created_by_users`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_resolutions_updated_by_users`
        FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `awards` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_hash` CHAR(64) NOT NULL,
    `document_type` VARCHAR(50) NOT NULL DEFAULT 'award',
    `sequence_stage` TINYINT UNSIGNED NOT NULL DEFAULT 4,
    `posted_at` DATETIME NOT NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `updated_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_awards_parent` (`parent_procurement_id`),
    CONSTRAINT `fk_awards_parent_procurement`
        FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_awards_created_by_users`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_awards_updated_by_users`
        FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contracts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_hash` CHAR(64) NOT NULL,
    `document_type` VARCHAR(50) NOT NULL DEFAULT 'contract',
    `sequence_stage` TINYINT UNSIGNED NOT NULL DEFAULT 5,
    `posted_at` DATETIME NOT NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `updated_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_contracts_parent` (`parent_procurement_id`),
    CONSTRAINT `fk_contracts_parent_procurement`
        FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_contracts_created_by_users`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_contracts_updated_by_users`
        FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notices_to_proceed` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_hash` CHAR(64) NOT NULL,
    `document_type` VARCHAR(50) NOT NULL DEFAULT 'notice_to_proceed',
    `sequence_stage` TINYINT UNSIGNED NOT NULL DEFAULT 6,
    `posted_at` DATETIME NOT NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `updated_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_notices_to_proceed_parent` (`parent_procurement_id`),
    CONSTRAINT `fk_ntp_parent_procurement`
        FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_ntp_created_by_users`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_ntp_updated_by_users`
        FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `procurement_activity_logs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `action_type` VARCHAR(50) NOT NULL,
    `document_type` VARCHAR(50) NOT NULL,
    `document_id` INT UNSIGNED NULL,
    `before_snapshot` JSON NULL,
    `after_snapshot` JSON NULL,
    `reason` VARCHAR(255) NULL,
    `file_hash` CHAR(64) NULL,
    `approval_reference` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_procurement_activity_parent` (`parent_procurement_id`),
    KEY `idx_procurement_activity_user` (`user_id`),
    KEY `idx_procurement_activity_action` (`action_type`),
    CONSTRAINT `fk_procurement_activity_parent_procurement`
        FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_procurement_activity_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
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

DELIMITER $$

CREATE TRIGGER `tr_parent_procurement_no_delete`
BEFORE DELETE ON `parent_procurement`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Official procurement records cannot be deleted.';
END $$

CREATE TRIGGER `tr_parent_procurement_guard_update`
BEFORE UPDATE ON `parent_procurement`
FOR EACH ROW
BEGIN
    IF OLD.reference_number <> NEW.reference_number
        OR OLD.procurement_title <> NEW.procurement_title
        OR OLD.abc <> NEW.abc
        OR OLD.mode_of_procurement <> NEW.mode_of_procurement
        OR OLD.posting_date <> NEW.posting_date
        OR OLD.bid_submission_deadline <> NEW.bid_submission_deadline
        OR OLD.description <> NEW.description
        OR OLD.region <> NEW.region
        OR COALESCE(OLD.branch, '') <> COALESCE(NEW.branch, '')
        OR OLD.created_by <> NEW.created_by THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Official procurement records are immutable after posting.';
    END IF;

    IF OLD.posting_status = 'archived' AND NEW.posting_status <> 'archived' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Archived procurement records cannot be restored.';
    END IF;
END $$

CREATE TRIGGER `tr_bid_notices_no_update`
BEFORE UPDATE ON `bid_notices`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted bid notices are immutable.';
END $$

CREATE TRIGGER `tr_bid_notices_no_delete`
BEFORE DELETE ON `bid_notices`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted bid notices cannot be deleted.';
END $$

CREATE TRIGGER `tr_sbb_no_update`
BEFORE UPDATE ON `supplemental_bid_bulletins`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted supplemental bid bulletins are immutable.';
END $$

CREATE TRIGGER `tr_sbb_no_delete`
BEFORE DELETE ON `supplemental_bid_bulletins`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted supplemental bid bulletins cannot be deleted.';
END $$

CREATE TRIGGER `tr_resolutions_no_update`
BEFORE UPDATE ON `resolutions`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted resolutions are immutable.';
END $$

CREATE TRIGGER `tr_resolutions_no_delete`
BEFORE DELETE ON `resolutions`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted resolutions cannot be deleted.';
END $$

CREATE TRIGGER `tr_awards_no_update`
BEFORE UPDATE ON `awards`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted awards are immutable.';
END $$

CREATE TRIGGER `tr_awards_no_delete`
BEFORE DELETE ON `awards`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted awards cannot be deleted.';
END $$

CREATE TRIGGER `tr_contracts_no_update`
BEFORE UPDATE ON `contracts`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted contracts are immutable.';
END $$

CREATE TRIGGER `tr_contracts_no_delete`
BEFORE DELETE ON `contracts`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted contracts cannot be deleted.';
END $$

CREATE TRIGGER `tr_ntp_no_update`
BEFORE UPDATE ON `notices_to_proceed`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted notices to proceed are immutable.';
END $$

CREATE TRIGGER `tr_ntp_no_delete`
BEFORE DELETE ON `notices_to_proceed`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted notices to proceed cannot be deleted.';
END $$

CREATE TRIGGER `tr_procurement_activity_logs_no_update`
BEFORE UPDATE ON `procurement_activity_logs`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Procurement audit logs are append-only.';
END $$

CREATE TRIGGER `tr_procurement_activity_logs_no_delete`
BEFORE DELETE ON `procurement_activity_logs`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Procurement audit logs cannot be deleted.';
END $$

DELIMITER ;
