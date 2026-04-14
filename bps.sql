-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2026 at 07:17 AM
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
-- Table structure for table `abstract_of_quotations`
--

CREATE TABLE `abstract_of_quotations` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_procurement_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_hash` char(64) NOT NULL,
  `document_type` varchar(50) NOT NULL DEFAULT 'abstract_of_quotations',
  `sequence_stage` tinyint(3) UNSIGNED NOT NULL DEFAULT 2,
  `posted_at` datetime NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `abstract_of_quotations`
--
DELIMITER $$
CREATE TRIGGER `tr_abstract_of_quotations_no_delete` BEFORE DELETE ON `abstract_of_quotations` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Abstract of Quotations documents cannot be deleted.';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_abstract_of_quotations_no_update` BEFORE UPDATE ON `abstract_of_quotations` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Abstract of Quotations documents are immutable after posting.';
END
$$
DELIMITER ;

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
-- Table structure for table `canvasses`
--

CREATE TABLE `canvasses` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_procurement_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_hash` char(64) NOT NULL,
  `document_type` varchar(50) NOT NULL DEFAULT 'canvass',
  `sequence_stage` tinyint(3) UNSIGNED NOT NULL DEFAULT 2,
  `posted_at` datetime NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `contract_or_purchase_orders`
--

CREATE TABLE `contract_or_purchase_orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_procurement_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_hash` char(64) NOT NULL,
  `document_type` varchar(50) NOT NULL DEFAULT 'contract_or_purchase_order',
  `sequence_stage` tinyint(3) UNSIGNED NOT NULL DEFAULT 4,
  `posted_at` datetime NOT NULL,
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
  `procurement_mode` enum('competitive_bidding','svp') NOT NULL,
  `reference_number` varchar(50) NOT NULL,
  `procurement_title` varchar(255) NOT NULL,
  `abc` decimal(15,2) NOT NULL DEFAULT 0.00,
  `mode_of_procurement` enum('competitive_bidding','svp') NOT NULL,
  `posting_date` datetime DEFAULT NULL,
  `bid_submission_deadline` datetime DEFAULT NULL,
  `description` text NOT NULL,
  `posting_status` enum('scheduled','open','closed','archived') NOT NULL DEFAULT 'scheduled',
  `current_stage` enum('bid_notice','supplemental_bid_bulletin','resolution','rfq','abstract_of_quotations','canvass','award','contract','contract_or_purchase_order','notice_to_proceed') NOT NULL DEFAULT 'bid_notice',
  `archived_at` datetime DEFAULT NULL,
  `archive_reason` varchar(255) DEFAULT NULL,
  `archived_by` int(10) UNSIGNED DEFAULT NULL,
  `archive_approval_reference` varchar(255) DEFAULT NULL,
  `archive_approved_by` int(10) UNSIGNED DEFAULT NULL,
  `archive_approved_at` datetime DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `end_user_unit` varchar(255) DEFAULT NULL,
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
-- Table structure for table `rfqs`
--

CREATE TABLE `rfqs` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_procurement_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_hash` char(64) NOT NULL,
  `document_type` varchar(50) NOT NULL DEFAULT 'rfq',
  `sequence_stage` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `posted_at` datetime NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `rfqs`
--
DELIMITER $$
CREATE TRIGGER `tr_rfqs_no_delete` BEFORE DELETE ON `rfqs` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'RFQ documents cannot be deleted.';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_rfqs_no_update` BEFORE UPDATE ON `rfqs` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'RFQ documents are immutable after posting.';
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
-- Table structure for table `svp_awards`
--

CREATE TABLE `svp_awards` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_procurement_id` int(10) UNSIGNED NOT NULL,
  `supplier_id` int(10) UNSIGNED NOT NULL,
  `award_no` varchar(100) DEFAULT NULL,
  `award_date` date NOT NULL,
  `award_amount` decimal(15,2) NOT NULL,
  `award_type` varchar(50) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svp_contracts`
--

CREATE TABLE `svp_contracts` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_procurement_id` int(10) UNSIGNED NOT NULL,
  `award_id` int(10) UNSIGNED NOT NULL,
  `contract_no` varchar(100) DEFAULT NULL,
  `contract_date` date NOT NULL,
  `contract_amount` decimal(15,2) NOT NULL,
  `contract_type` varchar(50) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svp_evaluations`
--

CREATE TABLE `svp_evaluations` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_procurement_id` int(10) UNSIGNED NOT NULL,
  `evaluation_date` date NOT NULL,
  `quotation_count` int(11) NOT NULL DEFAULT 0,
  `is_posting_compliant` tinyint(1) NOT NULL DEFAULT 0,
  `is_supplier_invitation_compliant` tinyint(1) NOT NULL DEFAULT 0,
  `exception_note` text DEFAULT NULL,
  `recommended_supplier_id` int(10) UNSIGNED DEFAULT NULL,
  `recommended_amount` decimal(15,2) DEFAULT NULL,
  `recommendation_text` longtext DEFAULT NULL,
  `approved_by` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svp_evaluation_items`
--

CREATE TABLE `svp_evaluation_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `evaluation_id` int(10) UNSIGNED NOT NULL,
  `quotation_id` int(10) UNSIGNED NOT NULL,
  `rank_no` int(11) DEFAULT NULL,
  `quoted_amount` decimal(15,2) NOT NULL,
  `is_calculated` tinyint(1) NOT NULL DEFAULT 1,
  `is_responsive` tinyint(1) NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svp_ntps`
--

CREATE TABLE `svp_ntps` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_procurement_id` int(10) UNSIGNED NOT NULL,
  `contract_id` int(10) UNSIGNED NOT NULL,
  `ntp_no` varchar(100) DEFAULT NULL,
  `ntp_date` date NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svp_quotations`
--

CREATE TABLE `svp_quotations` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_procurement_id` int(10) UNSIGNED NOT NULL,
  `supplier_id` int(10) UNSIGNED NOT NULL,
  `quotation_no` varchar(100) DEFAULT NULL,
  `quotation_date` date DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `delivery_offer` varchar(255) DEFAULT NULL,
  `warranty_offer` varchar(255) DEFAULT NULL,
  `payment_offer` varchar(255) DEFAULT NULL,
  `submission_time` datetime NOT NULL,
  `is_late` tinyint(1) NOT NULL DEFAULT 0,
  `is_responsive` tinyint(1) NOT NULL DEFAULT 0,
  `responsiveness_notes` text DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svp_rfqs`
--

CREATE TABLE `svp_rfqs` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_procurement_id` int(10) UNSIGNED NOT NULL,
  `rfq_no` varchar(100) NOT NULL,
  `rfq_date` date NOT NULL,
  `quotation_deadline` datetime NOT NULL,
  `delivery_period` varchar(255) DEFAULT NULL,
  `payment_terms` varchar(255) DEFAULT NULL,
  `warranty_terms` text DEFAULT NULL,
  `technical_specs` longtext NOT NULL,
  `terms_and_conditions` longtext DEFAULT NULL,
  `is_posting_required` tinyint(1) NOT NULL DEFAULT 0,
  `issued_at` datetime DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svp_rfq_postings`
--

CREATE TABLE `svp_rfq_postings` (
  `id` int(10) UNSIGNED NOT NULL,
  `svp_rfq_id` int(10) UNSIGNED NOT NULL,
  `posting_channel` varchar(50) NOT NULL,
  `posting_reference` varchar(255) DEFAULT NULL,
  `posted_at` datetime NOT NULL,
  `posting_end_at` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svp_suppliers`
--

CREATE TABLE `svp_suppliers` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_procurement_id` int(10) UNSIGNED NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `tin_no` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `philgeps_registration_no` varchar(100) DEFAULT NULL,
  `is_invited` tinyint(1) NOT NULL DEFAULT 0,
  `invited_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'secretariat1', 'Secretariat', 'A', 'Officer', 'Central Office', 'Administrative and General Services Department', '/okV9cheDamfxGfumXclp/JZHPCTEKLgNIICq2VT0HJpiBG', 'author', 'secretariat.officer@nfa.gov.ph', NULL, NULL, NULL, 1, 1, '2026-04-08 16:33:32', '2026-04-08 16:33:32'),
(2, 'sysadmin', 'System', NULL, 'Administrator', 'Central Office', 'Administrative and General Services Department', '.N7AanuAVD4ex/YhdZFNa2inp5I4./6y', 'admin', 'system.admin@nfa.gov.ph', NULL, NULL, NULL, 1, 1, '2026-04-08 16:33:32', '2026-04-08 16:33:32'),
(3, 'SVProcurement', 'Jason', 'S', 'Cañares', 'Central Office', 'Administrative and General Services Department', '.y0SchG16xNE1HND1lE5fF276xtOi', 'author', 'agsd.purchasing@nfa.gov.ph', NULL, NULL, NULL, 1, 1, '2026-04-09 21:10:07', '2026-04-14 05:10:31'),
(4, 'aacarillo', 'Angelo', 'A', 'Carillo', 'Central Office', 'Administrative and General Services Department', '.OVMH.dVkKgMMdgrf5gMw2XUALwMto2sgAWkS', 'author', 'bac@nfa.gov.ph', NULL, NULL, NULL, 1, 1, '2026-04-09 21:34:30', '2026-04-10 00:13:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abstract_of_quotations`
--
ALTER TABLE `abstract_of_quotations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_abstract_of_quotations_parent` (`parent_procurement_id`),
  ADD KEY `fk_abstract_of_quotations_created_by_users` (`created_by`),
  ADD KEY `fk_abstract_of_quotations_updated_by_users` (`updated_by`);

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
-- Indexes for table `canvasses`
--
ALTER TABLE `canvasses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_canvasses_parent` (`parent_procurement_id`),
  ADD KEY `fk_canvasses_created_by_users` (`created_by`),
  ADD KEY `fk_canvasses_updated_by_users` (`updated_by`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_contracts_parent` (`parent_procurement_id`),
  ADD KEY `fk_contracts_created_by_users` (`created_by`),
  ADD KEY `fk_contracts_updated_by_users` (`updated_by`);

--
-- Indexes for table `contract_or_purchase_orders`
--
ALTER TABLE `contract_or_purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_contract_or_purchase_orders_parent` (`parent_procurement_id`),
  ADD KEY `fk_contract_or_purchase_orders_created_by_users` (`created_by`),
  ADD KEY `fk_contract_or_purchase_orders_updated_by_users` (`updated_by`);

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
-- Indexes for table `rfqs`
--
ALTER TABLE `rfqs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_rfqs_parent` (`parent_procurement_id`),
  ADD KEY `fk_rfqs_created_by_users` (`created_by`),
  ADD KEY `fk_rfqs_updated_by_users` (`updated_by`);

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
-- Indexes for table `svp_awards`
--
ALTER TABLE `svp_awards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_svp_awards_parent` (`parent_procurement_id`),
  ADD KEY `fk_svp_awards_supplier` (`supplier_id`);

--
-- Indexes for table `svp_contracts`
--
ALTER TABLE `svp_contracts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_svp_contracts_parent` (`parent_procurement_id`),
  ADD KEY `fk_svp_contracts_award` (`award_id`);

--
-- Indexes for table `svp_evaluations`
--
ALTER TABLE `svp_evaluations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_svp_evaluations_parent` (`parent_procurement_id`),
  ADD KEY `fk_svp_evaluations_supplier` (`recommended_supplier_id`);

--
-- Indexes for table `svp_evaluation_items`
--
ALTER TABLE `svp_evaluation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_svp_eval_items_eval` (`evaluation_id`),
  ADD KEY `fk_svp_eval_items_quote` (`quotation_id`);

--
-- Indexes for table `svp_ntps`
--
ALTER TABLE `svp_ntps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_svp_ntps_parent` (`parent_procurement_id`),
  ADD KEY `fk_svp_ntps_contract` (`contract_id`);

--
-- Indexes for table `svp_quotations`
--
ALTER TABLE `svp_quotations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_svp_quote_supplier` (`parent_procurement_id`,`supplier_id`),
  ADD KEY `fk_svp_quotations_supplier` (`supplier_id`);

--
-- Indexes for table `svp_rfqs`
--
ALTER TABLE `svp_rfqs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_svp_rfqs_parent` (`parent_procurement_id`),
  ADD UNIQUE KEY `uq_svp_rfqs_no` (`rfq_no`);

--
-- Indexes for table `svp_rfq_postings`
--
ALTER TABLE `svp_rfq_postings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_svp_rfq_postings_channel` (`svp_rfq_id`,`posting_channel`);

--
-- Indexes for table `svp_suppliers`
--
ALTER TABLE `svp_suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_svp_suppliers_parent` (`parent_procurement_id`);

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
-- AUTO_INCREMENT for table `abstract_of_quotations`
--
ALTER TABLE `abstract_of_quotations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `canvasses`
--
ALTER TABLE `canvasses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract_or_purchase_orders`
--
ALTER TABLE `contract_or_purchase_orders`
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
-- AUTO_INCREMENT for table `rfqs`
--
ALTER TABLE `rfqs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplemental_bid_bulletins`
--
ALTER TABLE `supplemental_bid_bulletins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svp_awards`
--
ALTER TABLE `svp_awards`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svp_contracts`
--
ALTER TABLE `svp_contracts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svp_evaluations`
--
ALTER TABLE `svp_evaluations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svp_evaluation_items`
--
ALTER TABLE `svp_evaluation_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svp_ntps`
--
ALTER TABLE `svp_ntps`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svp_quotations`
--
ALTER TABLE `svp_quotations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svp_rfqs`
--
ALTER TABLE `svp_rfqs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svp_rfq_postings`
--
ALTER TABLE `svp_rfq_postings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svp_suppliers`
--
ALTER TABLE `svp_suppliers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `abstract_of_quotations`
--
ALTER TABLE `abstract_of_quotations`
  ADD CONSTRAINT `fk_abstract_of_quotations_created_by_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_abstract_of_quotations_parent_procurement` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_abstract_of_quotations_updated_by_users` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

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
-- Constraints for table `canvasses`
--
ALTER TABLE `canvasses`
  ADD CONSTRAINT `fk_canvasses_created_by_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_canvasses_parent_procurement` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_canvasses_updated_by_users` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `contracts`
--
ALTER TABLE `contracts`
  ADD CONSTRAINT `fk_contracts_created_by_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_contracts_parent_procurement` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_contracts_updated_by_users` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `contract_or_purchase_orders`
--
ALTER TABLE `contract_or_purchase_orders`
  ADD CONSTRAINT `fk_contract_or_purchase_orders_created_by_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_contract_or_purchase_orders_parent_procurement` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_contract_or_purchase_orders_updated_by_users` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

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
-- Constraints for table `rfqs`
--
ALTER TABLE `rfqs`
  ADD CONSTRAINT `fk_rfqs_created_by_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rfqs_parent_procurement` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rfqs_updated_by_users` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `supplemental_bid_bulletins`
--
ALTER TABLE `supplemental_bid_bulletins`
  ADD CONSTRAINT `fk_sbb_created_by_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sbb_parent_procurement` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sbb_updated_by_users` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `svp_awards`
--
ALTER TABLE `svp_awards`
  ADD CONSTRAINT `fk_svp_awards_parent` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_svp_awards_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `svp_suppliers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `svp_contracts`
--
ALTER TABLE `svp_contracts`
  ADD CONSTRAINT `fk_svp_contracts_award` FOREIGN KEY (`award_id`) REFERENCES `svp_awards` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_svp_contracts_parent` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `svp_evaluations`
--
ALTER TABLE `svp_evaluations`
  ADD CONSTRAINT `fk_svp_evaluations_parent` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_svp_evaluations_supplier` FOREIGN KEY (`recommended_supplier_id`) REFERENCES `svp_suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `svp_evaluation_items`
--
ALTER TABLE `svp_evaluation_items`
  ADD CONSTRAINT `fk_svp_eval_items_eval` FOREIGN KEY (`evaluation_id`) REFERENCES `svp_evaluations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_svp_eval_items_quote` FOREIGN KEY (`quotation_id`) REFERENCES `svp_quotations` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `svp_ntps`
--
ALTER TABLE `svp_ntps`
  ADD CONSTRAINT `fk_svp_ntps_contract` FOREIGN KEY (`contract_id`) REFERENCES `svp_contracts` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_svp_ntps_parent` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `svp_quotations`
--
ALTER TABLE `svp_quotations`
  ADD CONSTRAINT `fk_svp_quotations_parent` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_svp_quotations_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `svp_suppliers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `svp_rfqs`
--
ALTER TABLE `svp_rfqs`
  ADD CONSTRAINT `fk_svp_rfqs_parent` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `svp_rfq_postings`
--
ALTER TABLE `svp_rfq_postings`
  ADD CONSTRAINT `fk_svp_rfq_postings_rfq` FOREIGN KEY (`svp_rfq_id`) REFERENCES `svp_rfqs` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `svp_suppliers`
--
ALTER TABLE `svp_suppliers`
  ADD CONSTRAINT `fk_svp_suppliers_parent` FOREIGN KEY (`parent_procurement_id`) REFERENCES `parent_procurement` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
