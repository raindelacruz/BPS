USE `bps`;

ALTER TABLE `parent_procurement`
    MODIFY `posting_date` DATETIME NULL,
    MODIFY `bid_submission_deadline` DATETIME NULL,
    MODIFY `current_stage` ENUM(
        'bid_notice',
        'supplemental_bid_bulletin',
        'resolution',
        'award',
        'contract',
        'notice_to_proceed',
        'draft',
        'rfq_prepared',
        'rfq_posted',
        'quotation_open',
        'under_evaluation',
        'awarded',
        'contract_prepared',
        'ntp_issued',
        'completed',
        'archived'
    ) NOT NULL DEFAULT 'bid_notice',
    ADD COLUMN `category` VARCHAR(50) NULL AFTER `archive_approved_at`,
    ADD COLUMN `end_user_unit` VARCHAR(255) NULL AFTER `category`,
    ADD COLUMN `is_svp_ntp_required` TINYINT(1) NOT NULL DEFAULT 0 AFTER `end_user_unit`,
    ADD COLUMN `quotation_receipt_closed_at` DATETIME NULL AFTER `is_svp_ntp_required`,
    ADD COLUMN `completed_at` DATETIME NULL AFTER `quotation_receipt_closed_at`;

CREATE TABLE IF NOT EXISTS `svp_rfqs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `rfq_no` VARCHAR(100) NOT NULL,
    `rfq_date` DATE NOT NULL,
    `quotation_deadline` DATETIME NOT NULL,
    `delivery_period` VARCHAR(255) NULL,
    `payment_terms` VARCHAR(255) NULL,
    `warranty_terms` TEXT NULL,
    `technical_specs` LONGTEXT NOT NULL,
    `terms_and_conditions` LONGTEXT NULL,
    `is_posting_required` TINYINT(1) NOT NULL DEFAULT 0,
    `issued_at` DATETIME NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `updated_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_svp_rfqs_parent` (`parent_procurement_id`),
    UNIQUE KEY `uq_svp_rfqs_no` (`rfq_no`),
    CONSTRAINT `fk_svp_rfqs_parent` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `svp_rfq_postings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `svp_rfq_id` INT UNSIGNED NOT NULL,
    `posting_channel` VARCHAR(50) NOT NULL,
    `posting_reference` VARCHAR(255) NULL,
    `posted_at` DATETIME NOT NULL,
    `posting_end_at` DATETIME NULL,
    `remarks` TEXT NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_svp_rfq_postings_channel` (`svp_rfq_id`, `posting_channel`),
    CONSTRAINT `fk_svp_rfq_postings_rfq` FOREIGN KEY (`svp_rfq_id`) REFERENCES `svp_rfqs` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `svp_suppliers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `supplier_name` VARCHAR(255) NOT NULL,
    `tin_no` VARCHAR(50) NULL,
    `address` TEXT NULL,
    `contact_person` VARCHAR(255) NULL,
    `email` VARCHAR(255) NULL,
    `phone` VARCHAR(100) NULL,
    `philgeps_registration_no` VARCHAR(100) NULL,
    `is_invited` TINYINT(1) NOT NULL DEFAULT 0,
    `invited_at` DATETIME NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_svp_suppliers_parent` (`parent_procurement_id`),
    CONSTRAINT `fk_svp_suppliers_parent` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `svp_quotations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `supplier_id` INT UNSIGNED NOT NULL,
    `quotation_no` VARCHAR(100) NULL,
    `quotation_date` DATE NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `delivery_offer` VARCHAR(255) NULL,
    `warranty_offer` VARCHAR(255) NULL,
    `payment_offer` VARCHAR(255) NULL,
    `submission_time` DATETIME NOT NULL,
    `is_late` TINYINT(1) NOT NULL DEFAULT 0,
    `is_responsive` TINYINT(1) NOT NULL DEFAULT 0,
    `responsiveness_notes` TEXT NULL,
    `attachment_path` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_svp_quote_supplier` (`parent_procurement_id`, `supplier_id`),
    CONSTRAINT `fk_svp_quotations_parent` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_svp_quotations_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `svp_suppliers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `svp_evaluations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `evaluation_date` DATE NOT NULL,
    `quotation_count` INT NOT NULL DEFAULT 0,
    `is_posting_compliant` TINYINT(1) NOT NULL DEFAULT 0,
    `is_supplier_invitation_compliant` TINYINT(1) NOT NULL DEFAULT 0,
    `exception_note` TEXT NULL,
    `recommended_supplier_id` INT UNSIGNED NULL,
    `recommended_amount` DECIMAL(15,2) NULL,
    `recommendation_text` LONGTEXT NULL,
    `approved_by` INT UNSIGNED NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_svp_evaluations_parent` (`parent_procurement_id`),
    CONSTRAINT `fk_svp_evaluations_parent` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_svp_evaluations_supplier` FOREIGN KEY (`recommended_supplier_id`) REFERENCES `svp_suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `svp_evaluation_items` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `evaluation_id` INT UNSIGNED NOT NULL,
    `quotation_id` INT UNSIGNED NOT NULL,
    `rank_no` INT NULL,
    `quoted_amount` DECIMAL(15,2) NOT NULL,
    `is_calculated` TINYINT(1) NOT NULL DEFAULT 1,
    `is_responsive` TINYINT(1) NOT NULL DEFAULT 0,
    `remarks` TEXT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_svp_eval_items_eval` FOREIGN KEY (`evaluation_id`) REFERENCES `svp_evaluations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_svp_eval_items_quote` FOREIGN KEY (`quotation_id`) REFERENCES `svp_quotations` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `svp_awards` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `supplier_id` INT UNSIGNED NOT NULL,
    `award_no` VARCHAR(100) NULL,
    `award_date` DATE NOT NULL,
    `award_amount` DECIMAL(15,2) NOT NULL,
    `award_type` VARCHAR(50) NOT NULL,
    `remarks` TEXT NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_svp_awards_parent` (`parent_procurement_id`),
    CONSTRAINT `fk_svp_awards_parent` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_svp_awards_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `svp_suppliers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `svp_contracts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `award_id` INT UNSIGNED NOT NULL,
    `contract_no` VARCHAR(100) NULL,
    `contract_date` DATE NOT NULL,
    `contract_amount` DECIMAL(15,2) NOT NULL,
    `contract_type` VARCHAR(50) NOT NULL,
    `file_path` VARCHAR(255) NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_svp_contracts_parent` (`parent_procurement_id`),
    CONSTRAINT `fk_svp_contracts_parent` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_svp_contracts_award` FOREIGN KEY (`award_id`) REFERENCES `svp_awards` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `svp_ntps` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_procurement_id` INT UNSIGNED NOT NULL,
    `contract_id` INT UNSIGNED NOT NULL,
    `ntp_no` VARCHAR(100) NULL,
    `ntp_date` DATE NOT NULL,
    `remarks` TEXT NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_svp_ntps_parent` (`parent_procurement_id`),
    CONSTRAINT `fk_svp_ntps_parent` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_svp_ntps_contract` FOREIGN KEY (`contract_id`) REFERENCES `svp_contracts` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
