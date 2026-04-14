ALTER TABLE `parent_procurement`
    ADD COLUMN `procurement_mode` ENUM('competitive_bidding', 'svp') NULL AFTER `id`;

UPDATE `parent_procurement`
SET `procurement_mode` = CASE
    WHEN `mode_of_procurement` = 'competitive_bidding' THEN 'competitive_bidding'
    ELSE 'svp'
END,
    `mode_of_procurement` = CASE
        WHEN `mode_of_procurement` = 'competitive_bidding' THEN 'competitive_bidding'
        ELSE 'svp'
    END;

ALTER TABLE `parent_procurement`
    MODIFY COLUMN `procurement_mode` ENUM('competitive_bidding', 'svp') NOT NULL,
    MODIFY COLUMN `mode_of_procurement` ENUM('competitive_bidding', 'svp') NOT NULL,
    MODIFY COLUMN `posting_status` ENUM('scheduled', 'open', 'closed', 'archived') NOT NULL DEFAULT 'scheduled',
    MODIFY COLUMN `current_stage` ENUM('bid_notice', 'supplemental_bid_bulletin', 'resolution', 'rfq', 'abstract_of_quotations', 'canvass', 'award', 'contract', 'contract_or_purchase_order', 'notice_to_proceed') NOT NULL DEFAULT 'bid_notice',
    DROP COLUMN `is_svp_ntp_required`,
    DROP COLUMN `quotation_receipt_closed_at`,
    DROP COLUMN `completed_at`;

CREATE TABLE IF NOT EXISTS `canvasses` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_hash` CHAR(64) NOT NULL,
    `document_type` VARCHAR(50) NOT NULL DEFAULT 'canvass',
    `sequence_stage` TINYINT UNSIGNED NOT NULL DEFAULT 2,
    `posted_at` DATETIME NOT NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `updated_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_canvasses_parent` (`parent_procurement_id`),
    CONSTRAINT `fk_canvasses_parent_procurement` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_canvasses_created_by_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_canvasses_updated_by_users` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `contract_or_purchase_orders` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_hash` CHAR(64) NOT NULL,
    `document_type` VARCHAR(50) NOT NULL DEFAULT 'contract_or_purchase_order',
    `sequence_stage` TINYINT UNSIGNED NOT NULL DEFAULT 4,
    `posted_at` DATETIME NOT NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `updated_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_contract_or_purchase_orders_parent` (`parent_procurement_id`),
    CONSTRAINT `fk_contract_or_purchase_orders_parent_procurement` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_contract_or_purchase_orders_created_by_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_contract_or_purchase_orders_updated_by_users` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
);
