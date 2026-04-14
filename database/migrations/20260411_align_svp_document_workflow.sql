USE `bps`;

ALTER TABLE `parent_procurement`
    MODIFY `posting_status` ENUM(
        'scheduled',
        'open',
        'closed',
        'draft',
        'posted',
        'under_evaluation',
        'awarded',
        'contracted',
        'completed',
        'archived'
    ) NOT NULL DEFAULT 'scheduled',
    MODIFY `current_stage` ENUM(
        'bid_notice',
        'supplemental_bid_bulletin',
        'resolution',
        'rfq',
        'abstract_of_quotations',
        'award',
        'contract',
        'notice_to_proceed',
        'draft',
        'archived'
    ) NOT NULL DEFAULT 'bid_notice';

CREATE TABLE IF NOT EXISTS `rfqs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_hash` CHAR(64) NOT NULL,
    `document_type` VARCHAR(50) NOT NULL DEFAULT 'rfq',
    `sequence_stage` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `posted_at` DATETIME NOT NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `updated_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_rfqs_parent` (`parent_procurement_id`),
    CONSTRAINT `fk_rfqs_parent_procurement`
        FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_rfqs_created_by_users`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_rfqs_updated_by_users`
        FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `abstract_of_quotations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_hash` CHAR(64) NOT NULL,
    `document_type` VARCHAR(50) NOT NULL DEFAULT 'abstract_of_quotations',
    `sequence_stage` TINYINT UNSIGNED NOT NULL DEFAULT 2,
    `posted_at` DATETIME NOT NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `updated_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_abstract_of_quotations_parent` (`parent_procurement_id`),
    CONSTRAINT `fk_abstract_of_quotations_parent_procurement`
        FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_abstract_of_quotations_created_by_users`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_abstract_of_quotations_updated_by_users`
        FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TRIGGER IF EXISTS `tr_rfqs_no_update`;
DROP TRIGGER IF EXISTS `tr_rfqs_no_delete`;
DROP TRIGGER IF EXISTS `tr_abstract_of_quotations_no_update`;
DROP TRIGGER IF EXISTS `tr_abstract_of_quotations_no_delete`;

DELIMITER $$

CREATE TRIGGER `tr_rfqs_no_update`
BEFORE UPDATE ON `rfqs`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'RFQ documents are immutable after posting.';
END$$

CREATE TRIGGER `tr_rfqs_no_delete`
BEFORE DELETE ON `rfqs`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'RFQ documents cannot be deleted.';
END$$

CREATE TRIGGER `tr_abstract_of_quotations_no_update`
BEFORE UPDATE ON `abstract_of_quotations`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Abstract of Quotations documents are immutable after posting.';
END$$

CREATE TRIGGER `tr_abstract_of_quotations_no_delete`
BEFORE DELETE ON `abstract_of_quotations`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Abstract of Quotations documents cannot be deleted.';
END$$

DELIMITER ;
