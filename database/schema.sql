CREATE DATABASE IF NOT EXISTS `bps`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `bps`;

DROP TABLE IF EXISTS `email_change_requests`;
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
