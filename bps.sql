-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2026 at 05:14 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bps`
--

-- --------------------------------------------------------

--
-- Table structure for table `awards`
--

CREATE TABLE `awards` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_procurement_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_hash` char(64) NOT NULL,
  `document_type` varchar(50) NOT NULL DEFAULT 'award',
  `sequence_stage` tinyint(3) UNSIGNED NOT NULL DEFAULT 4,
  `posted_at` datetime NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `awards`
--
DELIMITER $$
CREATE TRIGGER `tr_awards_no_delete` BEFORE DELETE ON `awards` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted awards cannot be deleted.';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_awards_no_update` BEFORE UPDATE ON `awards` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted awards are immutable.';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `bid_notices`
--

CREATE TABLE `bid_notices` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_procurement_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_hash` char(64) NOT NULL,
  `document_type` varchar(50) NOT NULL DEFAULT 'bid_notice',
  `sequence_stage` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `posted_at` datetime NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `bid_notices`
--
DELIMITER $$
CREATE TRIGGER `tr_bid_notices_no_delete` BEFORE DELETE ON `bid_notices` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted bid notices cannot be deleted.';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_bid_notices_no_update` BEFORE UPDATE ON `bid_notices` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted bid notices are immutable.';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_procurement_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_hash` char(64) NOT NULL,
  `document_type` varchar(50) NOT NULL DEFAULT 'contract',
  `sequence_stage` tinyint(3) UNSIGNED NOT NULL DEFAULT 5,
  `posted_at` datetime NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `contracts`
--
DELIMITER $$
CREATE TRIGGER `tr_contracts_no_delete` BEFORE DELETE ON `contracts` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted contracts cannot be deleted.';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_contracts_no_update` BEFORE UPDATE ON `contracts` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted contracts are immutable.';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `email_change_requests`
--

CREATE TABLE `email_change_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `current_email` varchar(255) NOT NULL,
  `new_email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `status` enum('pending','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notices_to_proceed`
--

CREATE TABLE `notices_to_proceed` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_procurement_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_hash` char(64) NOT NULL,
  `document_type` varchar(50) NOT NULL DEFAULT 'notice_to_proceed',
  `sequence_stage` tinyint(3) UNSIGNED NOT NULL DEFAULT 6,
  `posted_at` datetime NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `notices_to_proceed`
--
DELIMITER $$
CREATE TRIGGER `tr_ntp_no_delete` BEFORE DELETE ON `notices_to_proceed` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted notices to proceed cannot be deleted.';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_ntp_no_update` BEFORE UPDATE ON `notices_to_proceed` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted notices to proceed are immutable.';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `parent_procurement`
--

CREATE TABLE `parent_procurement` (
  `id` int(10) UNSIGNED NOT NULL,
  `reference_number` varchar(50) NOT NULL,
  `procurement_title` varchar(255) NOT NULL,
  `abc` decimal(15,2) NOT NULL DEFAULT 0.00,
  `mode_of_procurement` enum('competitive_bidding','limited_source_bidding','competitive_dialogue','unsolicited_offer_with_bid_matching','direct_contracting','direct_acquisition','repeat_order','small_value_procurement','direct_sales','direct_procurement_for_science_technology_and_innovation','procurement_of_agricultural_and_fishery_products','negotiated_procurement') NOT NULL,
  `posting_date` datetime NOT NULL,
  `bid_submission_deadline` datetime NOT NULL,
  `description` text NOT NULL,
  `posting_status` enum('scheduled','open','closed','archived') NOT NULL DEFAULT 'scheduled',
  `current_stage` enum('bid_notice','supplemental_bid_bulletin','resolution','award','contract','notice_to_proceed') NOT NULL DEFAULT 'bid_notice',
  `archived_at` datetime DEFAULT NULL,
  `archive_reason` varchar(255) DEFAULT NULL,
  `archived_by` int(10) UNSIGNED DEFAULT NULL,
  `archive_approval_reference` varchar(255) DEFAULT NULL,
  `archive_approved_by` int(10) UNSIGNED DEFAULT NULL,
  `archive_approved_at` datetime DEFAULT NULL,
  `region` varchar(20) NOT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `parent_procurement`
--
DELIMITER $$
CREATE TRIGGER `tr_parent_procurement_guard_update` BEFORE UPDATE ON `parent_procurement` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_parent_procurement_no_delete` BEFORE DELETE ON `parent_procurement` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Official procurement records cannot be deleted.';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `procurement_activity_logs`
--

CREATE TABLE `procurement_activity_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_procurement_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `document_id` int(10) UNSIGNED DEFAULT NULL,
  `before_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`before_snapshot`)),
  `after_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`after_snapshot`)),
  `reason` varchar(255) DEFAULT NULL,
  `file_hash` char(64) DEFAULT NULL,
  `approval_reference` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `procurement_activity_logs`
--
DELIMITER $$
CREATE TRIGGER `tr_procurement_activity_logs_no_delete` BEFORE DELETE ON `procurement_activity_logs` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Procurement audit logs cannot be deleted.';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_procurement_activity_logs_no_update` BEFORE UPDATE ON `procurement_activity_logs` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Procurement audit logs are append-only.';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `resolutions`
--

CREATE TABLE `resolutions` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_procurement_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_hash` char(64) NOT NULL,
  `document_type` varchar(50) NOT NULL DEFAULT 'resolution',
  `sequence_stage` tinyint(3) UNSIGNED NOT NULL DEFAULT 3,
  `posted_at` datetime NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `resolutions`
--
DELIMITER $$
CREATE TRIGGER `tr_resolutions_no_delete` BEFORE DELETE ON `resolutions` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted resolutions cannot be deleted.';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_resolutions_no_update` BEFORE UPDATE ON `resolutions` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted resolutions are immutable.';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `supplemental_bid_bulletins`
--

CREATE TABLE `supplemental_bid_bulletins` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_procurement_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_hash` char(64) NOT NULL,
  `document_type` varchar(50) NOT NULL DEFAULT 'supplemental_bid_bulletin',
  `sequence_stage` tinyint(3) UNSIGNED NOT NULL DEFAULT 2,
  `posted_at` datetime NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `supplemental_bid_bulletins`
--
DELIMITER $$
CREATE TRIGGER `tr_sbb_no_delete` BEFORE DELETE ON `supplemental_bid_bulletins` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted supplemental bid bulletins cannot be deleted.';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_sbb_no_update` BEFORE UPDATE ON `supplemental_bid_bulletins` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted supplemental bid bulletins are immutable.';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `middle_initial` varchar(1) DEFAULT NULL,
  `lastname` varchar(255) NOT NULL,
  `region` varchar(50) NOT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('author','admin') NOT NULL DEFAULT 'author',
  `email` varchar(255) NOT NULL,
  `verification_token` varchar(64) DEFAULT NULL,
  `verification_code` varchar(6) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `firstname`, `middle_initial`, `lastname`, `region`, `branch`, `password`, `role`, `email`, `verification_token`, `verification_code`, `token_expiry`, `is_verified`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'secretariat1', 'Secretariat', 'A', 'Officer', 'Central Office', 'Administrative and General Services Department', '$2y$10$xU0mMJ/okV9cheDamfxGfumXclp/JZHPCTEKLgNIICq2VT0HJpiBG', 'author', 'secretariat.officer@nfa.gov.ph', NULL, NULL, NULL, 1, 1, '2026-04-09 00:33:32', '2026-04-09 00:33:32'),
(2, 'sysadmin', 'System', NULL, 'Administrator', 'Central Office', 'Administrative and General Services Department', '$2y$10$XgllwcfsWZj7qOq5SghaF.N7AanuAVD4ex/YhdZFNa2inp5I4./6y', 'admin', 'system.admin@nfa.gov.ph', NULL, NULL, NULL, 1, 1, '2026-04-09 00:33:32', '2026-04-09 00:33:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `awards`
--
ALTER TABLE `awards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_awards_parent` (`parent_procurement_id`),
  ADD KEY `fk_awards_created_by_users` (`created_by`),
  ADD KEY `fk_awards_updated_by_users` (`updated_by`);

--
-- Indexes for table `bid_notices`
--
ALTER TABLE `bid_notices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_bid_notices_parent` (`parent_procurement_id`),
  ADD KEY `idx_bid_notices_posted_at` (`posted_at`),
  ADD KEY `fk_bid_notices_created_by_users` (`created_by`),
  ADD KEY `fk_bid_notices_updated_by_users` (`updated_by`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_contracts_parent` (`parent_procurement_id`),
  ADD KEY `fk_contracts_created_by_users` (`created_by`),
  ADD KEY `fk_contracts_updated_by_users` (`updated_by`);

--
-- Indexes for table `email_change_requests`
--
ALTER TABLE `email_change_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_email_change_requests_token` (`token`),
  ADD KEY `idx_email_change_requests_user_status` (`user_id`,`status`);

--
-- Indexes for table `notices_to_proceed`
--
ALTER TABLE `notices_to_proceed`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_notices_to_proceed_parent` (`parent_procurement_id`),
  ADD KEY `fk_ntp_created_by_users` (`created_by`),
  ADD KEY `fk_ntp_updated_by_users` (`updated_by`);

--
-- Indexes for table `parent_procurement`
--
ALTER TABLE `parent_procurement`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_parent_procurement_reference_number` (`reference_number`),
  ADD KEY `idx_parent_procurement_posting_status` (`posting_status`),
  ADD KEY `idx_parent_procurement_stage` (`current_stage`),
  ADD KEY `idx_parent_procurement_public_listing` (`posting_status`,`posting_date`,`bid_submission_deadline`),
  ADD KEY `idx_parent_procurement_region_branch` (`region`,`branch`),
  ADD KEY `idx_parent_procurement_created_by` (`created_by`),
  ADD KEY `idx_parent_procurement_archived_by` (`archived_by`),
  ADD KEY `idx_parent_procurement_archive_approved_by` (`archive_approved_by`),
  ADD KEY `fk_parent_procurement_updated_by_users` (`updated_by`);

--
-- Indexes for table `procurement_activity_logs`
--
ALTER TABLE `procurement_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_procurement_activity_parent` (`parent_procurement_id`),
  ADD KEY `idx_procurement_activity_user` (`user_id`),
  ADD KEY `idx_procurement_activity_action` (`action_type`);

--
-- Indexes for table `resolutions`
--
ALTER TABLE `resolutions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_resolutions_parent` (`parent_procurement_id`),
  ADD KEY `fk_resolutions_created_by_users` (`created_by`),
  ADD KEY `fk_resolutions_updated_by_users` (`updated_by`);

--
-- Indexes for table `supplemental_bid_bulletins`
--
ALTER TABLE `supplemental_bid_bulletins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sbb_parent_procurement` (`parent_procurement_id`),
  ADD KEY `idx_sbb_posted_at` (`posted_at`),
  ADD KEY `fk_sbb_created_by_users` (`created_by`),
  ADD KEY `fk_sbb_updated_by_users` (`updated_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_username` (`username`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD KEY `idx_users_role` (`role`),
  ADD KEY `idx_users_region` (`region`),
  ADD KEY `idx_users_is_active` (`is_active`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `awards`
--
ALTER TABLE `awards`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bid_notices`
--
ALTER TABLE `bid_notices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_change_requests`
--
ALTER TABLE `email_change_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notices_to_proceed`
--
ALTER TABLE `notices_to_proceed`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parent_procurement`
--
ALTER TABLE `parent_procurement`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `procurement_activity_logs`
--
ALTER TABLE `procurement_activity_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resolutions`
--
ALTER TABLE `resolutions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplemental_bid_bulletins`
--
ALTER TABLE `supplemental_bid_bulletins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `awards`
--
ALTER TABLE `awards`
  ADD CONSTRAINT `fk_awards_created_by_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_awards_parent_procurement` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_awards_updated_by_users` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `bid_notices`
--
ALTER TABLE `bid_notices`
  ADD CONSTRAINT `fk_bid_notices_created_by_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bid_notices_parent_procurement` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bid_notices_updated_by_users` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `contracts`
--
ALTER TABLE `contracts`
  ADD CONSTRAINT `fk_contracts_created_by_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_contracts_parent_procurement` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_contracts_updated_by_users` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `email_change_requests`
--
ALTER TABLE `email_change_requests`
  ADD CONSTRAINT `fk_email_change_requests_user_id_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notices_to_proceed`
--
ALTER TABLE `notices_to_proceed`
  ADD CONSTRAINT `fk_ntp_created_by_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ntp_parent_procurement` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ntp_updated_by_users` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `parent_procurement`
--
ALTER TABLE `parent_procurement`
  ADD CONSTRAINT `fk_parent_procurement_archive_approved_by_users` FOREIGN KEY (`archive_approved_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_parent_procurement_archived_by_users` FOREIGN KEY (`archived_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_parent_procurement_created_by_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_parent_procurement_updated_by_users` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `procurement_activity_logs`
--
ALTER TABLE `procurement_activity_logs`
  ADD CONSTRAINT `fk_procurement_activity_parent_procurement` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_procurement_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `resolutions`
--
ALTER TABLE `resolutions`
  ADD CONSTRAINT `fk_resolutions_created_by_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_resolutions_parent_procurement` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_resolutions_updated_by_users` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `supplemental_bid_bulletins`
--
ALTER TABLE `supplemental_bid_bulletins`
  ADD CONSTRAINT `fk_sbb_created_by_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sbb_parent_procurement` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sbb_updated_by_users` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
