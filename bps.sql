-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2026 at 06:58 AM
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
  `document_type` varchar(50) NOT NULL DEFAULT 'award',
  `sequence_stage` tinyint(3) UNSIGNED NOT NULL DEFAULT 4,
  `posted_at` datetime NOT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `locked_at` datetime DEFAULT NULL,
  `lock_reason` varchar(255) DEFAULT NULL,
  `is_reopened` tinyint(1) NOT NULL DEFAULT 0,
  `reopened_at` datetime DEFAULT NULL,
  `reopened_by` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `awards`
--

INSERT INTO `awards` (`id`, `parent_procurement_id`, `title`, `description`, `file_path`, `document_type`, `sequence_stage`, `posted_at`, `is_locked`, `locked_at`, `lock_reason`, `is_reopened`, `reopened_at`, `reopened_by`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 2, 'Notice of Award to CTX Technologies', 'NTP to CTX', 'storage/uploads/notices/notice_69cf0bf08401c5.35153007.pdf', 'award', 4, '2026-04-03 08:37:00', 0, NULL, NULL, 0, NULL, NULL, 1, 1, '2026-04-03 00:38:08', '2026-04-03 00:38:08');

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
  `document_type` varchar(50) NOT NULL DEFAULT 'bid_notice',
  `sequence_stage` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `posted_at` datetime NOT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `locked_at` datetime DEFAULT NULL,
  `lock_reason` varchar(255) DEFAULT NULL,
  `is_reopened` tinyint(1) NOT NULL DEFAULT 0,
  `reopened_at` datetime DEFAULT NULL,
  `reopened_by` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bid_notices`
--

INSERT INTO `bid_notices` (`id`, `parent_procurement_id`, `title`, `description`, `file_path`, `document_type`, `sequence_stage`, `posted_at`, `is_locked`, `locked_at`, `lock_reason`, `is_reopened`, `reopened_at`, `reopened_by`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'Procurement of Property Information System', 'Procurement of PIS', 'storage/uploads/notices/notice_69cdbaf58286e1.01695717.pdf', 'bid_notice', 1, '2026-04-02 08:42:00', 0, NULL, NULL, 0, NULL, NULL, 1, 1, '2026-04-02 00:40:21', '2026-04-02 00:40:21'),
(2, 2, 'Procurement of Human Resource Information System', 'Procurement of HRIS', 'storage/uploads/notices/notice_69cdc4a80e5901.16983582.pdf', 'bid_notice', 1, '2026-04-02 09:22:00', 1, '2026-04-03 08:31:02', 'Locked after resolution was posted.', 0, NULL, NULL, 1, 1, '2026-04-02 01:21:44', '2026-04-03 00:31:02'),
(4, 4, 'Procurement of FMIS', 'Procurment of FMIS', 'storage/uploads/notices/notice_69cf08295df955.83863844.pdf', 'bid_notice', 1, '2026-04-03 08:23:00', 0, NULL, NULL, 0, NULL, NULL, 1, 1, '2026-04-03 00:22:01', '2026-04-03 00:22:01');

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
  `document_type` varchar(50) NOT NULL DEFAULT 'contract',
  `sequence_stage` tinyint(3) UNSIGNED NOT NULL DEFAULT 5,
  `posted_at` datetime NOT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `locked_at` datetime DEFAULT NULL,
  `lock_reason` varchar(255) DEFAULT NULL,
  `is_reopened` tinyint(1) NOT NULL DEFAULT 0,
  `reopened_at` datetime DEFAULT NULL,
  `reopened_by` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `reference_code` varchar(50) NOT NULL,
  `type` enum('bid','sbb','resolution','award','contract','proceed','rfq') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `uploaded_by` int(10) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('pending','active','expired','archived') NOT NULL DEFAULT 'pending',
  `region` varchar(20) NOT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `procurement_type` enum('competitive_bidding','limited_source_bidding','competitive_dialogue','unsolicited_offer_with_bid_matching','direct_contracting','direct_acquisition','repeat_order','small_value_procurement','direct_sales','direct_procurement_for_science_technology_and_innovation','procurement_of_agricultural_and_fishery_products','negotiated_procurement') NOT NULL,
  `archived_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notices`
--

INSERT INTO `notices` (`id`, `title`, `reference_code`, `type`, `file_path`, `upload_date`, `start_date`, `end_date`, `uploaded_by`, `description`, `is_archived`, `status`, `region`, `branch`, `procurement_type`, `archived_at`) VALUES
(1, 'Procurement of Property Information System', 'BAC-2026-03-31', 'bid', 'storage/uploads/notices/notice_69cdbaf58286e1.01695717.pdf', '2026-04-02 00:40:21', '2026-04-02 08:42:00', '2026-04-13 08:39:00', 1, 'Procurement of PIS', 0, 'active', 'Central Office', 'Administrative and General Services Department', 'competitive_bidding', NULL),
(2, 'Procurement of Human Resource Information System', 'BAC-2026-04-31', 'bid', 'storage/uploads/notices/notice_69cdc4a80e5901.16983582.pdf', '2026-04-02 01:21:44', '2026-04-02 09:22:00', '2026-04-17 09:21:00', 1, 'Procurement of HRIS', 0, 'active', 'Central Office', 'Administrative and General Services Department', 'competitive_bidding', NULL),
(3, 'Procurement of HRIS SBB', 'BAC-2026-04-31', 'sbb', 'storage/uploads/notices/notice_69cdc5239b3b25.21718700.pdf', '2026-04-02 01:23:47', '2026-04-02 09:25:00', '2026-04-10 09:23:00', 1, 'Procurement of HRIS', 0, 'active', 'Central Office', 'Administrative and General Services Department', 'competitive_bidding', NULL);

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
  `document_type` varchar(50) NOT NULL DEFAULT 'notice_to_proceed',
  `sequence_stage` tinyint(3) UNSIGNED NOT NULL DEFAULT 6,
  `posted_at` datetime NOT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `locked_at` datetime DEFAULT NULL,
  `lock_reason` varchar(255) DEFAULT NULL,
  `is_reopened` tinyint(1) NOT NULL DEFAULT 0,
  `reopened_at` datetime DEFAULT NULL,
  `reopened_by` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `status` enum('pending','active','expired','archived') NOT NULL DEFAULT 'pending',
  `current_stage` enum('draft','bid_notice','resolution','award','contract','notice_to_proceed') NOT NULL DEFAULT 'draft',
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `archived_at` datetime DEFAULT NULL,
  `region` varchar(20) NOT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parent_procurement`
--

INSERT INTO `parent_procurement` (`id`, `reference_number`, `procurement_title`, `abc`, `mode_of_procurement`, `posting_date`, `bid_submission_deadline`, `description`, `status`, `current_stage`, `is_archived`, `archived_at`, `region`, `branch`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'BAC-2026-03-31', 'Procurement of Property Information System', 0.00, 'competitive_bidding', '2026-04-02 08:42:00', '2026-04-13 08:39:00', 'Procurement of PIS', 'active', 'bid_notice', 0, NULL, 'Central Office', 'Administrative and General Services Department', 1, 1, '2026-04-02 00:40:21', '2026-04-02 00:40:21'),
(2, 'BAC-2026-04-31', 'Procurement of Human Resource Information System', 0.00, 'competitive_bidding', '2026-04-02 09:22:00', '2026-04-17 09:21:00', 'Procurement of HRIS', 'active', 'award', 0, NULL, 'Central Office', 'Administrative and General Services Department', 1, 1, '2026-04-02 01:21:44', '2026-04-03 00:38:08'),
(4, 'BAC-2026-04-03', 'Procurement of FMIS', 14000000.00, 'competitive_bidding', '2026-04-03 08:23:00', '2026-04-17 08:21:00', 'Procurment of FMIS', 'active', 'bid_notice', 0, NULL, 'Central Office', 'Administrative and General Services Department', 1, 1, '2026-04-03 00:22:01', '2026-04-03 00:23:24');

-- --------------------------------------------------------

--
-- Table structure for table `procurement_activity_logs`
--

CREATE TABLE `procurement_activity_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_procurement_id` int(10) UNSIGNED NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `document_id` int(10) UNSIGNED DEFAULT NULL,
  `sequence_stage` tinyint(3) UNSIGNED NOT NULL,
  `action_type` enum('create','edit','lock','reopen','archive','unarchive') NOT NULL,
  `acted_by` int(10) UNSIGNED NOT NULL,
  `action_note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `procurement_activity_logs`
--

INSERT INTO `procurement_activity_logs` (`id`, `parent_procurement_id`, `document_type`, `document_id`, `sequence_stage`, `action_type`, `acted_by`, `action_note`, `created_at`) VALUES
(1, 1, 'bid_notice', 1, 1, 'create', 1, 'Migrated bid notice activity.', '2026-04-02 00:40:21'),
(2, 2, 'bid_notice', 2, 1, 'create', 1, 'Migrated bid notice activity.', '2026-04-02 01:21:44'),
(4, 4, 'bid_notice', 4, 1, 'create', 1, 'Created the root bid notice posting record.', '2026-04-03 00:22:01'),
(5, 2, 'resolution', 1, 3, 'create', 1, 'Posted Resolution.', '2026-04-03 00:31:02'),
(6, 2, 'bid_notice', 2, 1, 'lock', 1, 'Locked after resolution was posted.', '2026-04-03 00:31:02'),
(7, 2, 'supplemental_bid_bulletin', 1, 2, 'lock', 1, 'Locked after resolution was posted.', '2026-04-03 00:31:02'),
(8, 2, 'award', 1, 4, 'create', 1, 'Posted Notice of Award / Award.', '2026-04-03 00:38:08'),
(9, 2, 'resolution', 1, 3, 'lock', 1, 'Locked after award was posted.', '2026-04-03 00:38:08');

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
  `document_type` varchar(50) NOT NULL DEFAULT 'resolution',
  `sequence_stage` tinyint(3) UNSIGNED NOT NULL DEFAULT 3,
  `posted_at` datetime NOT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `locked_at` datetime DEFAULT NULL,
  `lock_reason` varchar(255) DEFAULT NULL,
  `is_reopened` tinyint(1) NOT NULL DEFAULT 0,
  `reopened_at` datetime DEFAULT NULL,
  `reopened_by` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `resolutions`
--

INSERT INTO `resolutions` (`id`, `parent_procurement_id`, `title`, `description`, `file_path`, `document_type`, `sequence_stage`, `posted_at`, `is_locked`, `locked_at`, `lock_reason`, `is_reopened`, `reopened_at`, `reopened_by`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 2, 'BAC Resolution for the Procurement of HRIS', 'BAC Resolution', 'storage/uploads/notices/notice_69cf0a46a911a9.36293253.pdf', 'resolution', 3, '2026-04-03 08:29:00', 1, '2026-04-03 08:38:08', 'Locked after award was posted.', 0, NULL, NULL, 1, 1, '2026-04-03 00:31:02', '2026-04-03 00:38:08');

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
  `document_type` varchar(50) NOT NULL DEFAULT 'supplemental_bid_bulletin',
  `sequence_stage` tinyint(3) UNSIGNED NOT NULL DEFAULT 2,
  `posted_at` datetime NOT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `locked_at` datetime DEFAULT NULL,
  `lock_reason` varchar(255) DEFAULT NULL,
  `is_reopened` tinyint(1) NOT NULL DEFAULT 0,
  `reopened_at` datetime DEFAULT NULL,
  `reopened_by` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplemental_bid_bulletins`
--

INSERT INTO `supplemental_bid_bulletins` (`id`, `parent_procurement_id`, `title`, `description`, `file_path`, `document_type`, `sequence_stage`, `posted_at`, `is_locked`, `locked_at`, `lock_reason`, `is_reopened`, `reopened_at`, `reopened_by`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 2, 'Procurement of HRIS SBB', 'Procurement of HRIS', 'storage/uploads/notices/notice_69cdc5239b3b25.21718700.pdf', 'supplemental_bid_bulletin', 2, '2026-04-02 09:25:00', 1, '2026-04-03 08:31:02', 'Locked after resolution was posted.', 0, NULL, NULL, 1, 1, '2026-04-02 01:23:47', '2026-04-03 00:31:02');

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
(1, 'rjdc', 'Rainier John', 'J', 'Dela Cruz', 'Central Office', 'Administrative and General Services Department', '$2y$10$xU0mMJ/okV9cheDamfxGfumXclp/JZHPCTEKLgNIICq2VT0HJpiBG', 'author', 'rainier.delacruz@nfa.gov.ph', NULL, NULL, NULL, 1, 1, '2026-03-30 06:08:06', '2026-03-30 06:21:48'),
(2, 'SysAdmin', 'System', NULL, 'Admin', 'Central Office', 'Administrative and General Services Department', '$2y$10$XgllwcfsWZj7qOq5SghaF.N7AanuAVD4ex/YhdZFNa2inp5I4./6y', 'admin', 'system.admin@nfa.gov.ph', NULL, NULL, NULL, 1, 1, '2026-03-30 06:16:20', '2026-03-30 06:16:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `awards`
--
ALTER TABLE `awards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_awards_parent` (`parent_procurement_id`);

--
-- Indexes for table `bid_notices`
--
ALTER TABLE `bid_notices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_bid_notices_parent` (`parent_procurement_id`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_contracts_parent` (`parent_procurement_id`);

--
-- Indexes for table `email_change_requests`
--
ALTER TABLE `email_change_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_email_change_requests_token` (`token`),
  ADD KEY `idx_email_change_requests_user_status` (`user_id`,`status`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notices_reference_code` (`reference_code`),
  ADD KEY `idx_notices_type` (`type`),
  ADD KEY `idx_notices_status` (`status`),
  ADD KEY `idx_notices_archived` (`is_archived`),
  ADD KEY `idx_notices_uploaded_by` (`uploaded_by`),
  ADD KEY `idx_notices_reference_type_region` (`reference_code`,`type`,`region`),
  ADD KEY `idx_notices_public_listing` (`type`,`status`,`is_archived`,`start_date`);

--
-- Indexes for table `notices_to_proceed`
--
ALTER TABLE `notices_to_proceed`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_notices_to_proceed_parent` (`parent_procurement_id`);

--
-- Indexes for table `parent_procurement`
--
ALTER TABLE `parent_procurement`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_parent_procurement_reference_number` (`reference_number`);

--
-- Indexes for table `procurement_activity_logs`
--
ALTER TABLE `procurement_activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resolutions`
--
ALTER TABLE `resolutions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_resolutions_parent` (`parent_procurement_id`);

--
-- Indexes for table `supplemental_bid_bulletins`
--
ALTER TABLE `supplemental_bid_bulletins`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bid_notices`
--
ALTER TABLE `bid_notices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notices_to_proceed`
--
ALTER TABLE `notices_to_proceed`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parent_procurement`
--
ALTER TABLE `parent_procurement`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `procurement_activity_logs`
--
ALTER TABLE `procurement_activity_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `resolutions`
--
ALTER TABLE `resolutions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `supplemental_bid_bulletins`
--
ALTER TABLE `supplemental_bid_bulletins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `email_change_requests`
--
ALTER TABLE `email_change_requests`
  ADD CONSTRAINT `fk_email_change_requests_user_id_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notices`
--
ALTER TABLE `notices`
  ADD CONSTRAINT `fk_notices_uploaded_by_users` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
