-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 04, 2026 at 02:03 AM
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
CREATE DATABASE IF NOT EXISTS `bps` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `bps`;

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
-- Dumping data for table `awards`
--

INSERT INTO `awards` (`id`, `parent_procurement_id`, `title`, `description`, `file_path`, `file_hash`, `document_type`, `sequence_stage`, `posted_at`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 2, 'Notice of Award', 'Notice of Award', 'storage/uploads/notices/notice_6a20193e6d1634.98355173.pdf', 'b3d451f305536ff2b41a58ce8ee1e1298243fe271c10aad792a478a7480dacc6', 'award', 4, '2026-06-03 21:00:00', 4, 4, '2026-06-03 12:08:30', '2026-06-03 12:08:30');

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
-- Dumping data for table `bid_notices`
--

INSERT INTO `bid_notices` (`id`, `parent_procurement_id`, `title`, `description`, `file_path`, `file_hash`, `document_type`, `sequence_stage`, `posted_at`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(2, 2, 'Procurement of Property Information System', 'Invitation to Bid for Procurement of Property Information System\r\n\r\nThe National Food Authority (NFA) Central Office, through the approved 2026 Corporate Operating Budget intends to apply the sum of Fourteen Million Pesos Only (Php 14,000,000.00), inclusive of all applicable taxes, being the Approved Budget for the Contract (ABC) to payments under the contract for the Procurement of Property Information System. Bids received in excess of the ABC shall be automatically rejected at bid opening. \r\n\r\nLot No.\r\nProject ID No.\r\nQty.\r\nItem/Description\r\nApproved Budget for the Contract \r\n(in PhP)\r\nPrice of Bid Documents\r\n(in PhP)\r\n1\r\n2026-01\r\nOne (1) Lot\r\nProcurement of Property Information System\r\n14,000,000.00\r\n25,000.00\r\n\r\n\r\nThe summary of bidding activities is as follows:\r\n\r\nAdvertisement/Posting of Invitation to Bid\r\nApril 15, 2026\r\nIssuance and Availability of Bid Documents\r\nApril 15, 2026\r\nPre-Bid Conference\r\nApril 22, 2026\r\nDeadline of Submission of Request for Clarification\r\nApril 26, 2026\r\nLast Day of Issuance of Bid Bulletin\r\nApril 29, 2026\r\nDeadline for Submission of Bids\r\nMay 6, 2026\r\n\r\n\r\nThe NFA Central Office, through the Bids and Awards Committee, now invites bids for the above Procurement Project for Procurement of Property Information System. Delivery of the Goods is required within one hundred eighty (180) calendar days from receipt of Notice to Proceed. Bidders should have completed, at least five (5) years from the date of submission and receipt of bids, contracts similar to the Project. The description of an eligible bidder is contained in the Bidding Documents, particularly, in Section II. Instructions to Bidders.\r\n\r\nBidding will be conducted through competitive bidding procedures using a non-discretionary “pass/fail” criterion as specified in the IRR of RA No. 12009. Bidding is open to all interested Bidders, whether local or foreign, subject to the conditions for eligibility provided in the IRR.\r\n\r\nInterested Bidders may obtain further information from the NFA Central Office BAC Secretariat and inspect the Bidding Documents at the address given below from 8:00 AM to 5:00 PM.\r\n\r\nA complete set of Bidding Documents may be acquired by interested Bidders on 15 April 2026 to 06 May 2026 from the address, e-mail, and website given below upon payment of the applicable fee for the Bidding Documents, pursuant to the latest Guidelines issued by the GPPB, in the amount of TWENTY-FIVE THOUSAND PESOS ONLY (PhP 25,000.00).\r\n\r\nIt may also be downloaded free of charge from the website of the Philippine Government Electronic Procurement System (PhilGEPS) and the website of the Procuring Entity, provided that Bidders shall pay the applicable fee for the Bidding Documents not later than the submission of their bids.\r\n\r\nThe NFA Central Office will hold a Pre-Bid Conference on 22 April 2026, 9:00 a.m. onwards at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or through video conferencing or webcasting via the Google Meet platform, which shall be open to prospective Bidders. Meeting details shall be made available to interested bidders upon request sent to the official e-mail of NFA Central Office at bac@nfa.gov.ph. The deadline for clarifications shall be on 26 April 2026 and the last day for issuance of relevant bid bulletins shall be on 29 April 2026.\r\n \r\nBids must be duly received by the Bids and Awards Committee (BAC) Secretariat through manual submission at the office address indicated below, on or before 06 May 2026, 8:30 a.m.. Late bids shall not be accepted.\r\n\r\nAll Bids must be accompanied by a Bid Security in any of the acceptable forms and in the amount stated in ITB Clause 16.1.\r\n\r\nBid opening shall be on 06 May 2026, 9:00 a.m. at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or via video conferencing or webcasting via the Google Meet platform.  Bids will be opened in the presence of the Bidders’ representatives who choose to attend the activity.\r\n\r\nIn accordance with Government Procurement Policy Board (GPPB) Circular 06-2005-Tie-Breaking Method, the Bids and Awards Committee (BAC) shall use a non-discretionary and non-discriminatory measure based on sheer luck or chance, which is “DRAW LOTS”, in the event that the two (2) or more bidders have been post-qualified and determined as the bidder having the Lowest Calculated and Responsive Bid (LCRB), based on the following procedures: \r\nIn alphabetical order, the bidder shall pick one roll of paper:\r\nThe lucky bidders who pick the paper with a “CONGRATULATIONS” remark shall be declared as the final bidder having the LCRB and recommended for the award of the contract.\r\n\r\nThe National Food Authority reserves the right to reject any and all bids, declare a failure of bidding, or not award the contract at any time prior to contract award in accordance with Section 70 of R.A. No. 12009, without incurring any liability to the affected Bidder or Bidders.\r\n\r\nFor further information, please refer to:\r\n\r\nName of Contact Person	:	ENGR. LESLIE M. NAVARRO\r\n						Head, BAC Secretariat \r\nPostal Address		:	National Food Authority Central Office\r\n						Visayas Avenue, Brgy. Vasra, Diliman,\r\n						Quezon City, 1128\r\nE-mail Address		:	bac@nfa.gov.ph\r\n\r\nYou may visit the following websites for downloading of Bidding Documents:\r\n\r\nNFA Central Office website (www.nfa.gov.ph)\r\nPhilGEPS website (www.philgeps.gov.ph)\r\n\r\n\r\n\r\n\r\n										            Approved by:\r\n\r\n\r\n              ____________________________\r\n Veralew DG. De Vera\r\nChairman, Bids and Awards Committee\r\nNational Food Authority', 'storage/uploads/notices/notice_69deeb172ae864.16356818.pdf', '3dd8f8ce706517b2740737f7cbec545b7b14fd7d18ecc25693a567ad4fcf48f2', 'bid_notice', 1, '2026-04-15 10:00:00', 4, 4, '2026-04-15 01:34:15', '2026-04-15 01:34:15'),
(4, 9, 'Supply and Delivery of Various Laptops', 'Invitation to Bid for \r\nSupply and Delivery of Various Laptops, Desktop Computers, and Tablets\r\n\r\nThe National Food Authority (NFA) Central Office, through the approved 2026 Corporate Operating Budget intends to apply the sum of:\r\n\r\n \r\nLot No.\r\nProject ID No.\r\nProject Name\r\nApproved Budget for the Contract \r\n(in PhP inclusive of all applicable taxes)\r\n1\r\n2026-03a\r\nSupply and Delivery of Various Laptops\r\n107,920,000.00\r\n2\r\n2026-03b\r\nSupply and Delivery of Desktop Computers\r\n103,360,000.00\r\n3\r\n2026-03c\r\nSupply and Delivery of Tablets\r\n8,240,000.00 \r\nTOTAL\r\n219,520,000.00\r\n\r\n\r\n\r\nbeing the Approved Budget for the Contract (ABC) to payments under the contract for the Supply and Delivery of Various Laptops, Desktop Computers, and Tablets. Bids received in excess of the ABC shall be automatically rejected at bid opening. \r\n\r\nThe summary of bidding activities is as follows:\r\n\r\nAdvertisement/Posting of Invitation to Bid\r\nJuly 16, 2026\r\nIssuance and Availability of Bid Documents\r\nJuly 16, 2026\r\nPre-Bid Conference\r\nJuly 24, 2026\r\nDeadline of Submission of Request for Clarification\r\nJuly 26, 2026\r\nLast Day of Issuance of Bid Bulletin\r\nJuly 29, 2026\r\nDeadline for Submission of Bids\r\nAugust 5, 2026\r\n\r\n\r\nThe NFA Central Office, through the Bids and Awards Committee, now invites bids for the Three (3) Lots Supply and Delivery of Various Laptops, Desktop Computers, and Tablets. Delivery of the Goods is required within sixty (60) calendar days from receipt of Notice to Proceed. Bidders should have completed, at least three (3) years prior to the date of submission and receipt of bids, contracts similar to the Project. The description of an eligible bidder is contained in the Bidding Documents, particularly, in Section II. Instructions to Bidders.\r\n\r\nBidding will be conducted through competitive bidding procedures using a non-discretionary “pass/fail” criterion as specified in the IRR of RA No. 12009. Bidding is restricted to Filipino citizens/sole proprietorships, partnerships, or organizations with at least sixty percent (60%) interest or outstanding capital stock belonging to citizens of the Philippines, and to citizens or organizations of a country the laws or regulations of which grant similar rights or privileges to Filipino citizens, pursuant to RA No. 5183. \r\n\r\nInterested Bidders may obtain further information from the NFA Central Office BAC Secretariat and inspect the Bidding Documents at the address given below from 8:00 AM to 5:00 PM, Mondays to Fridays, except for holidays or inspect the Bidding Documents at the address and e-mail given below. \r\n\r\nA complete set of Bidding Documents may be acquired by interested Bidders on 16 July 2026 to 5 August 2026 from the address, e-mail, and website given below, upon payment of the applicable fee for the Bidding Documents, pursuant to the latest Guidelines issued by the GPPB, in the following amount:\r\n\r\n\r\nLot No.\r\nProject ID No.\r\nProject Name\r\nApproved Budget for the Contract \r\n(in PhP inclusive of all applicable taxes)\r\nCost of Bidding Documents\r\n(in PhP) \r\n1\r\n2026-03a\r\nSupply and Delivery of Various Laptops\r\n107,920,000.00\r\n50,000.00\r\n2\r\n2026-03b\r\nSupply and Delivery of Desktop Computers\r\n103,360,000.00\r\n50,000.00\r\n3\r\n2026-03c\r\nSupply and Delivery of Various Tablets\r\n8,240,000.00 \r\n10,000.00\r\n\r\n\r\nIt may also be downloaded free of charge from the website of the Philippine Government Electronic Procurement System (PhilGEPS) and the website of the Procuring Entity, provided that Bidders shall pay the applicable fee for the Bidding Documents not later than the submission of their bids.\r\n\r\nThe NFA Central Office will hold a Pre-Bid Conference on 24 July 2026, 1:30 p.m. onwards at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or through video conferencing or webcasting via the Google Meet platform, which shall be open to prospective Bidders. Meeting details shall be made available to interested bidders upon request sent to the official e-mail of NFA Central Office at bac@nfa.gov.ph. The deadline for clarifications shall be on 26 July, 2026 and the last day for issuance of relevant bid bulletins shall be on 29 July, 2026.\r\n \r\nBids must be duly received by the Bids and Awards Committee (BAC) Secretariat through manual submission at the office address indicated below, on or before 5 August 2026, 1:30 p.m. Late bids shall not be accepted.\r\n\r\nAll Bids must be accompanied by a Bid Security in any of the acceptable forms and in the amount stated in ITB Clause 16.1.\r\n\r\nBid opening shall be on 5 August 2026, 2:00 p.m. at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or via video conferencing or webcasting via the Google Meet platform.  Bids will be opened in the presence of the Bidders’ representatives who choose to attend the activity.\r\n\r\nIn accordance with Government Procurement Policy Board (GPPB) Circular 06-2005-Tie-Breaking Method, the Bids and Awards Committee (BAC) shall use a non-discretionary and non-discriminatory measure based on sheer luck or chance, which is “DRAW LOTS”, in the event that the two (2) or more bidders have been post-qualified and determined as the bidder having the Lowest Calculated and Responsive Bid (LCRB), based on the following procedures: \r\n\r\nIn alphabetical order, the bidder shall pick one roll of paper:\r\n\r\nThe lucky bidders who pick the paper with a “CONGRATULATIONS” remark shall be declared as the final bidder having the LCRB and recommended for the award of the contract.\r\n\r\nThe National Food Authority reserves the right to reject any and all bids, declare a failure of bidding, or not award the contract at any time prior to contract award in accordance with Section 70 of R.A. No. 12009, without incurring any liability to the affected Bidder or Bidders.\r\n\r\nFor further information, please refer to:\r\n\r\nName of Contact Person:	ENGR. LESLIE M. NAVARRO\r\n						Head, BAC Secretariat \r\nPostal Address		:	National Food Authority Central Office\r\n						Visayas Avenue, Brgy. Vasra, Diliman,\r\n						Quezon City, 1128\r\nE-mail Address		:	bac@nfa.gov.ph\r\n\r\nYou may visit the following websites for downloading of Bidding Documents:\r\n\r\nNFA Central Office website (www.nfa.gov.ph)\r\nPhilGEPS website (www.philgeps.gov.ph)\r\n\r\n\r\nApproved by:\r\n\r\n\r\n              \r\nVERALEW DG. DE VERA\r\nChairperson, Bids and Awards Committee\r\nNational Food Authority\r\n\r\n\r\n\r\n16 July 2026', 'storage/uploads/notices/notice_6a5794419c3854.97449857.pdf', '6c70af08d73df6410d3d6532a7552a7dc65eeb837e30643f7a4333824da9baa5', 'bid_notice', 1, '2026-07-16 00:00:00', 4, 4, '2026-07-15 14:08:02', '2026-07-15 14:08:02'),
(5, 10, 'Supply and Delivery of Desktop Computers', 'Invitation to Bid for \r\nSupply and Delivery of Various Laptops, Desktop Computers, and Tablets\r\n\r\nThe National Food Authority (NFA) Central Office, through the approved 2026 Corporate Operating Budget intends to apply the sum of:\r\n\r\n \r\nLot No.\r\nProject ID No.\r\nProject Name\r\nApproved Budget for the Contract \r\n(in PhP inclusive of all applicable taxes)\r\n1\r\n2026-03a\r\nSupply and Delivery of Various Laptops\r\n107,920,000.00\r\n2\r\n2026-03b\r\nSupply and Delivery of Desktop Computers\r\n103,360,000.00\r\n3\r\n2026-03c\r\nSupply and Delivery of Tablets\r\n8,240,000.00 \r\nTOTAL\r\n219,520,000.00\r\n\r\n\r\n\r\nbeing the Approved Budget for the Contract (ABC) to payments under the contract for the Supply and Delivery of Various Laptops, Desktop Computers, and Tablets. Bids received in excess of the ABC shall be automatically rejected at bid opening. \r\n\r\nThe summary of bidding activities is as follows:\r\n\r\nAdvertisement/Posting of Invitation to Bid\r\nJuly 16, 2026\r\nIssuance and Availability of Bid Documents\r\nJuly 16, 2026\r\nPre-Bid Conference\r\nJuly 24, 2026\r\nDeadline of Submission of Request for Clarification\r\nJuly 26, 2026\r\nLast Day of Issuance of Bid Bulletin\r\nJuly 29, 2026\r\nDeadline for Submission of Bids\r\nAugust 5, 2026\r\n\r\n\r\nThe NFA Central Office, through the Bids and Awards Committee, now invites bids for the Three (3) Lots Supply and Delivery of Various Laptops, Desktop Computers, and Tablets. Delivery of the Goods is required within sixty (60) calendar days from receipt of Notice to Proceed. Bidders should have completed, at least three (3) years prior to the date of submission and receipt of bids, contracts similar to the Project. The description of an eligible bidder is contained in the Bidding Documents, particularly, in Section II. Instructions to Bidders.\r\n\r\nBidding will be conducted through competitive bidding procedures using a non-discretionary “pass/fail” criterion as specified in the IRR of RA No. 12009. Bidding is restricted to Filipino citizens/sole proprietorships, partnerships, or organizations with at least sixty percent (60%) interest or outstanding capital stock belonging to citizens of the Philippines, and to citizens or organizations of a country the laws or regulations of which grant similar rights or privileges to Filipino citizens, pursuant to RA No. 5183. \r\n\r\nInterested Bidders may obtain further information from the NFA Central Office BAC Secretariat and inspect the Bidding Documents at the address given below from 8:00 AM to 5:00 PM, Mondays to Fridays, except for holidays or inspect the Bidding Documents at the address and e-mail given below. \r\n\r\nA complete set of Bidding Documents may be acquired by interested Bidders on 16 July 2026 to 5 August 2026 from the address, e-mail, and website given below, upon payment of the applicable fee for the Bidding Documents, pursuant to the latest Guidelines issued by the GPPB, in the following amount:\r\n\r\n\r\nLot No.\r\nProject ID No.\r\nProject Name\r\nApproved Budget for the Contract \r\n(in PhP inclusive of all applicable taxes)\r\nCost of Bidding Documents\r\n(in PhP) \r\n1\r\n2026-03a\r\nSupply and Delivery of Various Laptops\r\n107,920,000.00\r\n50,000.00\r\n2\r\n2026-03b\r\nSupply and Delivery of Desktop Computers\r\n103,360,000.00\r\n50,000.00\r\n3\r\n2026-03c\r\nSupply and Delivery of Various Tablets\r\n8,240,000.00 \r\n10,000.00\r\n\r\n\r\nIt may also be downloaded free of charge from the website of the Philippine Government Electronic Procurement System (PhilGEPS) and the website of the Procuring Entity, provided that Bidders shall pay the applicable fee for the Bidding Documents not later than the submission of their bids.\r\n\r\nThe NFA Central Office will hold a Pre-Bid Conference on 24 July 2026, 1:30 p.m. onwards at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or through video conferencing or webcasting via the Google Meet platform, which shall be open to prospective Bidders. Meeting details shall be made available to interested bidders upon request sent to the official e-mail of NFA Central Office at bac@nfa.gov.ph. The deadline for clarifications shall be on 26 July, 2026 and the last day for issuance of relevant bid bulletins shall be on 29 July, 2026.\r\n \r\nBids must be duly received by the Bids and Awards Committee (BAC) Secretariat through manual submission at the office address indicated below, on or before 5 August 2026, 1:30 p.m. Late bids shall not be accepted.\r\n\r\nAll Bids must be accompanied by a Bid Security in any of the acceptable forms and in the amount stated in ITB Clause 16.1.\r\n\r\nBid opening shall be on 5 August 2026, 2:00 p.m. at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or via video conferencing or webcasting via the Google Meet platform.  Bids will be opened in the presence of the Bidders’ representatives who choose to attend the activity.\r\n\r\nIn accordance with Government Procurement Policy Board (GPPB) Circular 06-2005-Tie-Breaking Method, the Bids and Awards Committee (BAC) shall use a non-discretionary and non-discriminatory measure based on sheer luck or chance, which is “DRAW LOTS”, in the event that the two (2) or more bidders have been post-qualified and determined as the bidder having the Lowest Calculated and Responsive Bid (LCRB), based on the following procedures: \r\n\r\nIn alphabetical order, the bidder shall pick one roll of paper:\r\n\r\nThe lucky bidders who pick the paper with a “CONGRATULATIONS” remark shall be declared as the final bidder having the LCRB and recommended for the award of the contract.\r\n\r\nThe National Food Authority reserves the right to reject any and all bids, declare a failure of bidding, or not award the contract at any time prior to contract award in accordance with Section 70 of R.A. No. 12009, without incurring any liability to the affected Bidder or Bidders.\r\n\r\nFor further information, please refer to:\r\n\r\nName of Contact Person:	ENGR. LESLIE M. NAVARRO\r\n						Head, BAC Secretariat \r\nPostal Address		:	National Food Authority Central Office\r\n						Visayas Avenue, Brgy. Vasra, Diliman,\r\n						Quezon City, 1128\r\nE-mail Address		:	bac@nfa.gov.ph\r\n\r\nYou may visit the following websites for downloading of Bidding Documents:\r\n\r\nNFA Central Office website (www.nfa.gov.ph)\r\nPhilGEPS website (www.philgeps.gov.ph)\r\n\r\n\r\nApproved by:\r\n\r\n\r\n              \r\nVERALEW DG. DE VERA\r\nChairperson, Bids and Awards Committee\r\nNational Food Authority\r\n\r\n\r\n\r\n16 July 2026', 'storage/uploads/notices/notice_6a5794c17226a8.08717001.pdf', '6c70af08d73df6410d3d6532a7552a7dc65eeb837e30643f7a4333824da9baa5', 'bid_notice', 1, '2026-07-16 00:00:00', 4, 4, '2026-07-15 14:10:10', '2026-07-15 14:10:10'),
(6, 11, 'Supply and Delivery of Various Tablets', 'Invitation to Bid for \r\nSupply and Delivery of Various Laptops, Desktop Computers, and Tablets\r\n\r\nThe National Food Authority (NFA) Central Office, through the approved 2026 Corporate Operating Budget intends to apply the sum of:\r\n\r\n \r\nLot No.\r\nProject ID No.\r\nProject Name\r\nApproved Budget for the Contract \r\n(in PhP inclusive of all applicable taxes)\r\n1\r\n2026-03a\r\nSupply and Delivery of Various Laptops\r\n107,920,000.00\r\n2\r\n2026-03b\r\nSupply and Delivery of Desktop Computers\r\n103,360,000.00\r\n3\r\n2026-03c\r\nSupply and Delivery of Tablets\r\n8,240,000.00 \r\nTOTAL\r\n219,520,000.00\r\n\r\n\r\n\r\nbeing the Approved Budget for the Contract (ABC) to payments under the contract for the Supply and Delivery of Various Laptops, Desktop Computers, and Tablets. Bids received in excess of the ABC shall be automatically rejected at bid opening. \r\n\r\nThe summary of bidding activities is as follows:\r\n\r\nAdvertisement/Posting of Invitation to Bid\r\nJuly 16, 2026\r\nIssuance and Availability of Bid Documents\r\nJuly 16, 2026\r\nPre-Bid Conference\r\nJuly 24, 2026\r\nDeadline of Submission of Request for Clarification\r\nJuly 26, 2026\r\nLast Day of Issuance of Bid Bulletin\r\nJuly 29, 2026\r\nDeadline for Submission of Bids\r\nAugust 5, 2026\r\n\r\n\r\nThe NFA Central Office, through the Bids and Awards Committee, now invites bids for the Three (3) Lots Supply and Delivery of Various Laptops, Desktop Computers, and Tablets. Delivery of the Goods is required within sixty (60) calendar days from receipt of Notice to Proceed. Bidders should have completed, at least three (3) years prior to the date of submission and receipt of bids, contracts similar to the Project. The description of an eligible bidder is contained in the Bidding Documents, particularly, in Section II. Instructions to Bidders.\r\n\r\nBidding will be conducted through competitive bidding procedures using a non-discretionary “pass/fail” criterion as specified in the IRR of RA No. 12009. Bidding is restricted to Filipino citizens/sole proprietorships, partnerships, or organizations with at least sixty percent (60%) interest or outstanding capital stock belonging to citizens of the Philippines, and to citizens or organizations of a country the laws or regulations of which grant similar rights or privileges to Filipino citizens, pursuant to RA No. 5183. \r\n\r\nInterested Bidders may obtain further information from the NFA Central Office BAC Secretariat and inspect the Bidding Documents at the address given below from 8:00 AM to 5:00 PM, Mondays to Fridays, except for holidays or inspect the Bidding Documents at the address and e-mail given below. \r\n\r\nA complete set of Bidding Documents may be acquired by interested Bidders on 16 July 2026 to 5 August 2026 from the address, e-mail, and website given below, upon payment of the applicable fee for the Bidding Documents, pursuant to the latest Guidelines issued by the GPPB, in the following amount:\r\n\r\n\r\nLot No.\r\nProject ID No.\r\nProject Name\r\nApproved Budget for the Contract \r\n(in PhP inclusive of all applicable taxes)\r\nCost of Bidding Documents\r\n(in PhP) \r\n1\r\n2026-03a\r\nSupply and Delivery of Various Laptops\r\n107,920,000.00\r\n50,000.00\r\n2\r\n2026-03b\r\nSupply and Delivery of Desktop Computers\r\n103,360,000.00\r\n50,000.00\r\n3\r\n2026-03c\r\nSupply and Delivery of Various Tablets\r\n8,240,000.00 \r\n10,000.00\r\n\r\n\r\nIt may also be downloaded free of charge from the website of the Philippine Government Electronic Procurement System (PhilGEPS) and the website of the Procuring Entity, provided that Bidders shall pay the applicable fee for the Bidding Documents not later than the submission of their bids.\r\n\r\nThe NFA Central Office will hold a Pre-Bid Conference on 24 July 2026, 1:30 p.m. onwards at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or through video conferencing or webcasting via the Google Meet platform, which shall be open to prospective Bidders. Meeting details shall be made available to interested bidders upon request sent to the official e-mail of NFA Central Office at bac@nfa.gov.ph. The deadline for clarifications shall be on 26 July, 2026 and the last day for issuance of relevant bid bulletins shall be on 29 July, 2026.\r\n \r\nBids must be duly received by the Bids and Awards Committee (BAC) Secretariat through manual submission at the office address indicated below, on or before 5 August 2026, 1:30 p.m. Late bids shall not be accepted.\r\n\r\nAll Bids must be accompanied by a Bid Security in any of the acceptable forms and in the amount stated in ITB Clause 16.1.\r\n\r\nBid opening shall be on 5 August 2026, 2:00 p.m. at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or via video conferencing or webcasting via the Google Meet platform.  Bids will be opened in the presence of the Bidders’ representatives who choose to attend the activity.\r\n\r\nIn accordance with Government Procurement Policy Board (GPPB) Circular 06-2005-Tie-Breaking Method, the Bids and Awards Committee (BAC) shall use a non-discretionary and non-discriminatory measure based on sheer luck or chance, which is “DRAW LOTS”, in the event that the two (2) or more bidders have been post-qualified and determined as the bidder having the Lowest Calculated and Responsive Bid (LCRB), based on the following procedures: \r\n\r\nIn alphabetical order, the bidder shall pick one roll of paper:\r\n\r\nThe lucky bidders who pick the paper with a “CONGRATULATIONS” remark shall be declared as the final bidder having the LCRB and recommended for the award of the contract.\r\n\r\nThe National Food Authority reserves the right to reject any and all bids, declare a failure of bidding, or not award the contract at any time prior to contract award in accordance with Section 70 of R.A. No. 12009, without incurring any liability to the affected Bidder or Bidders.\r\n\r\nFor further information, please refer to:\r\n\r\nName of Contact Person:	ENGR. LESLIE M. NAVARRO\r\n						Head, BAC Secretariat \r\nPostal Address		:	National Food Authority Central Office\r\n						Visayas Avenue, Brgy. Vasra, Diliman,\r\n						Quezon City, 1128\r\nE-mail Address		:	bac@nfa.gov.ph\r\n\r\nYou may visit the following websites for downloading of Bidding Documents:\r\n\r\nNFA Central Office website (www.nfa.gov.ph)\r\nPhilGEPS website (www.philgeps.gov.ph)\r\n\r\n\r\nApproved by:\r\n\r\n\r\n              \r\nVERALEW DG. DE VERA\r\nChairperson, Bids and Awards Committee\r\nNational Food Authority\r\n\r\n\r\n\r\n16 July 2026', 'storage/uploads/notices/notice_6a5795576bcad8.52347194.pdf', '6c70af08d73df6410d3d6532a7552a7dc65eeb837e30643f7a4333824da9baa5', 'bid_notice', 1, '2026-07-16 00:00:00', 4, 4, '2026-07-15 14:12:40', '2026-07-15 14:12:40'),
(7, 12, 'Supply and Delivery of One Hundred Fifty (150) Units Custom-Built Delivery and Cargo Trucks', 'Invitation to Bid for Supply and Delivery of One Hundred Fifty (150) Units Custom-Built Delivery and Cargo Trucks\r\n\r\nThe National Food Authority (NFA) Central Office, through the approved 2026 Corporate Operating Budget intends to apply the sum of Seven Hundred Five Million Pesos Only (Php 705,000,000.00), inclusive of all applicable taxes, being the Approved Budget for the Contract (ABC) to payments under the contract for the Supply and Delivery of One Hundred Fifty (150) Units Custom-Built Delivery and Cargo Trucks. Bids received in excess of the ABC shall be automatically rejected at bid opening. \r\n\r\nLot No.\r\nProject ID No.\r\nQty.\r\nItem/Description\r\nApproved Budget for the Contract \r\n(in PhP)\r\nPrice of Bid Documents\r\n(in PhP)\r\n1\r\n2026-02\r\nOne (1) Lot\r\nSupply and Delivery of One Hundred Fifty (150) Units Custom-Built Delivery and Cargo Trucks\r\n\r\nItem 1: 110 units 6-Wheeler 120 bags capacity Delivery Trucks\r\n\r\nItem 2: 28 units 6-Wheeler 180 bags capacity Delivery Trucks\r\n\r\nItem 3: 12 units 10-Wheeler 360 bags capacity Cargo Trucks\r\n705,000,000.00\r\n\r\n\r\n\r\n\r\n\r\nABC Per item:\r\n462,859,110.06\r\n\r\n\r\n\r\n\r\n159,281,681.91\r\n\r\n\r\n\r\n\r\n82,589,208.03\r\n75,000.00\r\n\r\n\r\nThe summary of bidding activities is as follows:\r\n\r\nAdvertisement/Posting of Invitation to Bid\r\nJuly 17, 2026\r\nIssuance and Availability of Bid Documents\r\nJuly 17, 2026\r\nPre-Bid Conference\r\nJuly 27, 2026\r\nDeadline of Submission of Request for Clarification\r\nJuly 29, 2026\r\nLast Day of Issuance of Bid Bulletin\r\nAugust 3, 2026\r\nDeadline for Submission of Bids\r\nAugust 10, 2026\r\n\r\n\r\nThe NFA Central Office, through the Bids and Awards Committee, now invites bids for the above Supply and Delivery of One Hundred Fifty (150) Units Custom-Built Delivery and Cargo Trucks. Delivery of the Goods is required within one hundred eighty (180) calendar days from receipt of Notice to Proceed. Bidders should have completed, at least ten (10) years from the date of submission and receipt of bids, contracts similar to the Project. The description of an eligible bidder is contained in the Bidding Documents, particularly, in Section II. Instructions to Bidders.\r\n\r\nBidding will be conducted through competitive bidding procedures using a non-discretionary “pass/fail” criterion as specified in the IRR of RA No. 12009. Bidding is open to all interested Bidders, whether local or foreign, subject to the conditions for eligibility provided in the IRR.\r\n\r\nInterested Bidders may obtain further information from the NFA Central Office BAC Secretariat and inspect the Bidding Documents at the address given below from 8:00 AM to 5:00 PM.\r\n\r\nA complete set of Bidding Documents may be acquired by interested Bidders on 17 July 2026 to 10 August 2026 from the address, e-mail, and website given below upon payment of the applicable fee for the Bidding Documents, pursuant to the latest Guidelines issued by the GPPB, in the amount of SEVENTY-FIVE THOUSAND PESOS ONLY (PhP 75,000.00).\r\n\r\nIt may also be downloaded free of charge from the website of the Philippine Government Electronic Procurement System (PhilGEPS) and the website of the Procuring Entity, provided that Bidders shall pay the applicable fee for the Bidding Documents not later than the submission of their bids.\r\n\r\nThe NFA Central Office will hold a Pre-Bid Conference on 27 July 2026, 9:00 a.m. onwards at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or through video conferencing or webcasting via the Google Meet platform, which shall be open to prospective Bidders. Meeting details shall be made available to interested bidders upon request sent to the official e-mail of NFA Central Office at bac@nfa.gov.ph. The deadline for clarifications shall be on 29 July 2026 and the last day for issuance of relevant bid bulletins shall be on 3 August 2026.\r\n \r\nBids must be duly received by the Bids and Awards Committee (BAC) Secretariat through manual submission at the office address indicated below, on or before 10 August 2026, 8:30 a.m.. Late bids shall not be accepted.\r\n\r\nAll Bids must be accompanied by a Bid Security in any of the acceptable forms and in the amount stated in ITB Clause 16.1.\r\n\r\nBid opening shall be on 10 August 2026, 9:00 a.m. at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or via video conferencing or webcasting via the Google Meet platform.  Bids will be opened in the presence of the Bidders’ representatives who choose to attend the activity.\r\n\r\nIn accordance with Government Procurement Policy Board (GPPB) Circular 06-2005-Tie-Breaking Method, the Bids and Awards Committee (BAC) shall use a non-discretionary and non-discriminatory measure based on sheer luck or chance, which is “DRAW LOTS”, in the event that the two (2) or more bidders have been post-qualified and determined as the bidder having the Lowest Calculated and Responsive Bid (LCRB), based on the following procedures: \r\nIn alphabetical order, the bidder shall pick one roll of paper:\r\nThe lucky bidders who pick the paper with a “CONGRATULATIONS” remark shall be declared as the final bidder having the LCRB and recommended for the award of the contract.\r\n\r\nThe National Food Authority reserves the right to reject any and all bids, declare a failure of bidding, or not award the contract at any time prior to contract award in accordance with Section 70 of R.A. No. 12009, without incurring any liability to the affected Bidder or Bidders.\r\n\r\nFor further information, please refer to:\r\n\r\nName of Contact Person	:	ENGR. LESLIE M. NAVARRO\r\n						Head, BAC Secretariat \r\nPostal Address		:	National Food Authority Central Office\r\n						Visayas Avenue, Brgy. Vasra, Diliman,\r\n						Quezon City, 1128\r\nE-mail Address		:	bac@nfa.gov.ph\r\n\r\nYou may visit the following websites for downloading of Bidding Documents:\r\n\r\nNFA Central Office website (www.nfa.gov.ph)\r\nPhilGEPS website (www.philgeps.gov.ph)\r\n\r\n\r\n\r\n\r\n										            Approved by:\r\n\r\n\r\n              ____________________________\r\n Veralew DG. De Vera\r\nChairman, Bids and Awards Committee\r\nNational Food Authority\r\n\r\n\r\n\r\n17 July 2026', 'storage/uploads/notices/notice_6a58d21435e7b7.39601509.pdf', '072345dccf30c4907c4a1a9c38ff95bae16d1b7209117c5457c3bb62da1093aa', 'bid_notice', 1, '2026-07-17 00:01:00', 4, 4, '2026-07-16 12:44:04', '2026-07-16 12:44:04');

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
-- Dumping data for table `contracts`
--

INSERT INTO `contracts` (`id`, `parent_procurement_id`, `title`, `description`, `file_path`, `file_hash`, `document_type`, `sequence_stage`, `posted_at`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 2, 'Approved Contract', 'Approved Contract for Procurement of Property Information System', 'storage/uploads/notices/notice_6a689a5a9d7e20.36838469.pdf', 'cc57c4018a87cd9c33fd7c14398f6a76746f6f0239c83be067e4d912f094ea6e', 'contract', 5, '2026-07-28 21:00:00', 4, 4, '2026-07-28 12:02:34', '2026-07-28 12:02:34');

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
-- Table structure for table `login_attempt_logs`
--

CREATE TABLE `login_attempt_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `username_entered` varchar(100) DEFAULT NULL,
  `event_type` enum('login_attempt','csrf_failure','logout') NOT NULL DEFAULT 'login_attempt',
  `outcome` enum('success','failure') NOT NULL,
  `failure_reason` varchar(50) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `request_method` varchar(10) DEFAULT NULL,
  `request_uri` varchar(255) DEFAULT NULL,
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_attempt_logs`
--

INSERT INTO `login_attempt_logs` (`id`, `user_id`, `username_entered`, `event_type`, `outcome`, `failure_reason`, `message`, `ip_address`, `user_agent`, `request_method`, `request_uri`, `context`, `created_at`) VALUES
(1, 6, 'jcañares', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '192.168.105.92', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-07-16 01:06:04'),
(2, 5, 'superadmin', 'logout', 'success', NULL, 'User logged out.', '192.168.104.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'POST', '/BPS/public/logout', NULL, '2026-07-16 01:06:19'),
(3, 6, 'jcañares', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '192.168.105.92', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-07-16 01:06:24'),
(4, 5, 'superadmin', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '192.168.104.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-07-16 01:06:31'),
(5, 5, 'superadmin', 'login_attempt', 'success', NULL, 'User logged in successfully.', '192.168.104.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-07-16 01:06:37'),
(6, 6, 'jcañares', 'login_attempt', 'success', NULL, 'User logged in successfully.', '192.168.105.92', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-07-16 01:06:41'),
(7, 6, 'jcañares', 'logout', 'success', NULL, 'User logged out.', '192.168.105.92', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'POST', '/BPS/public/logout', NULL, '2026-07-16 01:07:48'),
(8, 6, 'jcañares', 'login_attempt', 'success', NULL, 'User logged in successfully.', '192.168.105.92', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-07-16 01:08:00'),
(9, 6, 'jcañares', 'logout', 'success', NULL, 'User logged out.', '192.168.105.92', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'POST', '/BPS/public/logout', NULL, '2026-07-16 01:08:10'),
(10, 5, 'superadmin', 'logout', 'success', NULL, 'User logged out.', '192.168.104.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'POST', '/BPS/public/logout', NULL, '2026-07-16 01:44:08'),
(11, 5, 'superadmin', 'login_attempt', 'success', NULL, 'User logged in successfully.', '192.168.104.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-07-16 01:44:20'),
(12, 4, 'aacarillo', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '112.203.39.58', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', 'POST', '/BPS/public/login', NULL, '2026-07-16 12:40:19'),
(13, 4, 'aacarillo', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '112.203.39.58', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', 'POST', '/BPS/public/login', NULL, '2026-07-16 12:40:30'),
(14, 4, 'aacarillo', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '112.203.39.58', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', 'POST', '/BPS/public/login', NULL, '2026-07-16 12:41:26'),
(15, 4, 'aacarillo', 'login_attempt', 'success', NULL, 'User logged in successfully.', '112.203.39.58', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', 'POST', '/BPS/public/login', NULL, '2026-07-16 12:42:13'),
(16, 4, 'aacarillo', 'logout', 'success', NULL, 'User logged out.', '112.203.39.58', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', 'POST', '/BPS/public/logout', NULL, '2026-07-16 12:44:08'),
(17, 4, 'aacarillo', 'login_attempt', 'success', NULL, 'User logged in successfully.', '112.203.39.58', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0', 'POST', '/BPS/public/login', NULL, '2026-07-28 12:01:13'),
(18, 4, 'aacarillo', 'logout', 'success', NULL, 'User logged out.', '112.203.39.58', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0', 'POST', '/BPS/public/logout', NULL, '2026-07-28 12:03:25'),
(19, 4, 'aacarillo', 'login_attempt', 'success', NULL, 'User logged in successfully.', '112.203.39.58', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0', 'POST', '/BPS/public/login', NULL, '2026-07-29 12:42:28'),
(20, 4, 'aacarillo', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '14.1.65.39', 'Mozilla/5.0 (Linux; Android 14; SM-A528B Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/571.0.0.41.92;]', 'POST', '/BPS/public/login', NULL, '2026-08-03 09:58:49'),
(21, 4, 'aacarillo', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '14.1.65.39', 'Mozilla/5.0 (Linux; Android 14; SM-A528B Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/571.0.0.41.92;]', 'POST', '/BPS/public/login', NULL, '2026-08-03 09:59:03'),
(22, 4, 'aacarillo', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '14.1.65.39', 'Mozilla/5.0 (Linux; Android 14; SM-A528B Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/571.0.0.41.92;]', 'POST', '/BPS/public/login', NULL, '2026-08-03 10:01:11'),
(23, 4, 'aacarillo', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '192.168.104.153', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-08-03 10:01:25'),
(24, 4, 'aacarillo', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '192.168.104.153', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-08-03 10:02:36'),
(25, 4, 'aacarillo', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '192.168.104.153', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-08-03 10:02:53'),
(26, 4, 'aacarillo', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '192.168.104.153', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-08-03 10:03:18'),
(27, 4, 'aacarillo', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '192.168.104.153', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-08-03 10:03:21'),
(28, 4, 'aacarillo', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '192.168.104.153', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-08-03 10:03:33'),
(29, 4, 'aacarillo', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '192.168.104.153', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-08-03 10:03:50'),
(30, 4, 'aacarillo', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '192.168.104.153', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-08-03 10:04:17'),
(31, 4, 'aacarillo', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '192.168.104.153', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-08-03 10:06:53'),
(32, 4, 'aacarillo', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '192.168.104.153', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-08-03 10:07:14'),
(33, 4, 'aacarillo', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '192.168.104.153', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-08-03 10:07:33'),
(34, 4, 'aacarillo', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '192.168.104.153', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-08-03 10:08:15'),
(35, 4, 'aacarillo', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '192.168.104.153', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-08-03 10:08:40'),
(36, 5, 'superadmin', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '192.168.104.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-08-03 23:57:03'),
(37, 2, 'sysadmin', 'login_attempt', 'failure', 'invalid_password', 'Password verification failed for an existing user.', '192.168.104.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-08-03 23:57:17'),
(38, 2, 'sysadmin', 'login_attempt', 'success', NULL, 'User logged in successfully.', '192.168.104.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'POST', '/BPS/public/login', NULL, '2026-08-04 00:00:11');

--
-- Triggers `login_attempt_logs`
--
DELIMITER $$
CREATE TRIGGER `tr_login_attempt_logs_no_delete` BEFORE DELETE ON `login_attempt_logs` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Login attempt logs cannot be deleted.';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_login_attempt_logs_no_update` BEFORE UPDATE ON `login_attempt_logs` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Login attempt logs are append-only.';
END
$$
DELIMITER ;

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
-- Dumping data for table `notices_to_proceed`
--

INSERT INTO `notices_to_proceed` (`id`, `parent_procurement_id`, `title`, `description`, `file_path`, `file_hash`, `document_type`, `sequence_stage`, `posted_at`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 2, 'Notice to Proceed', 'Notice to Proceed for Procurement of Property Information System', 'storage/uploads/notices/notice_6a689a809b7e50.39701815.pdf', '4ccbb3780bf9549e983bce9771543c8276de2da6fc9c0d10c00142f3ae27d654', 'notice_to_proceed', 6, '2026-07-28 21:00:00', 4, 4, '2026-07-28 12:03:12', '2026-07-28 12:03:12');

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
-- Dumping data for table `parent_procurement`
--

INSERT INTO `parent_procurement` (`id`, `procurement_mode`, `reference_number`, `procurement_title`, `abc`, `mode_of_procurement`, `posting_date`, `bid_submission_deadline`, `description`, `posting_status`, `current_stage`, `archived_at`, `archive_reason`, `archived_by`, `archive_approval_reference`, `archive_approved_by`, `archive_approved_at`, `category`, `end_user_unit`, `region`, `branch`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(2, 'competitive_bidding', '2026-01', 'Procurement of Property Information System', 14000000.00, 'competitive_bidding', '2026-04-15 10:00:00', '2026-05-06 08:30:00', 'Invitation to Bid for Procurement of Property Information System\r\n\r\nThe National Food Authority (NFA) Central Office, through the approved 2026 Corporate Operating Budget intends to apply the sum of Fourteen Million Pesos Only (Php 14,000,000.00), inclusive of all applicable taxes, being the Approved Budget for the Contract (ABC) to payments under the contract for the Procurement of Property Information System. Bids received in excess of the ABC shall be automatically rejected at bid opening. \r\n\r\nLot No.\r\nProject ID No.\r\nQty.\r\nItem/Description\r\nApproved Budget for the Contract \r\n(in PhP)\r\nPrice of Bid Documents\r\n(in PhP)\r\n1\r\n2026-01\r\nOne (1) Lot\r\nProcurement of Property Information System\r\n14,000,000.00\r\n25,000.00\r\n\r\n\r\nThe summary of bidding activities is as follows:\r\n\r\nAdvertisement/Posting of Invitation to Bid\r\nApril 15, 2026\r\nIssuance and Availability of Bid Documents\r\nApril 15, 2026\r\nPre-Bid Conference\r\nApril 22, 2026\r\nDeadline of Submission of Request for Clarification\r\nApril 26, 2026\r\nLast Day of Issuance of Bid Bulletin\r\nApril 29, 2026\r\nDeadline for Submission of Bids\r\nMay 6, 2026\r\n\r\n\r\nThe NFA Central Office, through the Bids and Awards Committee, now invites bids for the above Procurement Project for Procurement of Property Information System. Delivery of the Goods is required within one hundred eighty (180) calendar days from receipt of Notice to Proceed. Bidders should have completed, at least five (5) years from the date of submission and receipt of bids, contracts similar to the Project. The description of an eligible bidder is contained in the Bidding Documents, particularly, in Section II. Instructions to Bidders.\r\n\r\nBidding will be conducted through competitive bidding procedures using a non-discretionary “pass/fail” criterion as specified in the IRR of RA No. 12009. Bidding is open to all interested Bidders, whether local or foreign, subject to the conditions for eligibility provided in the IRR.\r\n\r\nInterested Bidders may obtain further information from the NFA Central Office BAC Secretariat and inspect the Bidding Documents at the address given below from 8:00 AM to 5:00 PM.\r\n\r\nA complete set of Bidding Documents may be acquired by interested Bidders on 15 April 2026 to 06 May 2026 from the address, e-mail, and website given below upon payment of the applicable fee for the Bidding Documents, pursuant to the latest Guidelines issued by the GPPB, in the amount of TWENTY-FIVE THOUSAND PESOS ONLY (PhP 25,000.00).\r\n\r\nIt may also be downloaded free of charge from the website of the Philippine Government Electronic Procurement System (PhilGEPS) and the website of the Procuring Entity, provided that Bidders shall pay the applicable fee for the Bidding Documents not later than the submission of their bids.\r\n\r\nThe NFA Central Office will hold a Pre-Bid Conference on 22 April 2026, 9:00 a.m. onwards at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or through video conferencing or webcasting via the Google Meet platform, which shall be open to prospective Bidders. Meeting details shall be made available to interested bidders upon request sent to the official e-mail of NFA Central Office at bac@nfa.gov.ph. The deadline for clarifications shall be on 26 April 2026 and the last day for issuance of relevant bid bulletins shall be on 29 April 2026.\r\n \r\nBids must be duly received by the Bids and Awards Committee (BAC) Secretariat through manual submission at the office address indicated below, on or before 06 May 2026, 8:30 a.m.. Late bids shall not be accepted.\r\n\r\nAll Bids must be accompanied by a Bid Security in any of the acceptable forms and in the amount stated in ITB Clause 16.1.\r\n\r\nBid opening shall be on 06 May 2026, 9:00 a.m. at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or via video conferencing or webcasting via the Google Meet platform.  Bids will be opened in the presence of the Bidders’ representatives who choose to attend the activity.\r\n\r\nIn accordance with Government Procurement Policy Board (GPPB) Circular 06-2005-Tie-Breaking Method, the Bids and Awards Committee (BAC) shall use a non-discretionary and non-discriminatory measure based on sheer luck or chance, which is “DRAW LOTS”, in the event that the two (2) or more bidders have been post-qualified and determined as the bidder having the Lowest Calculated and Responsive Bid (LCRB), based on the following procedures: \r\nIn alphabetical order, the bidder shall pick one roll of paper:\r\nThe lucky bidders who pick the paper with a “CONGRATULATIONS” remark shall be declared as the final bidder having the LCRB and recommended for the award of the contract.\r\n\r\nThe National Food Authority reserves the right to reject any and all bids, declare a failure of bidding, or not award the contract at any time prior to contract award in accordance with Section 70 of R.A. No. 12009, without incurring any liability to the affected Bidder or Bidders.\r\n\r\nFor further information, please refer to:\r\n\r\nName of Contact Person	:	ENGR. LESLIE M. NAVARRO\r\n						Head, BAC Secretariat \r\nPostal Address		:	National Food Authority Central Office\r\n						Visayas Avenue, Brgy. Vasra, Diliman,\r\n						Quezon City, 1128\r\nE-mail Address		:	bac@nfa.gov.ph\r\n\r\nYou may visit the following websites for downloading of Bidding Documents:\r\n\r\nNFA Central Office website (www.nfa.gov.ph)\r\nPhilGEPS website (www.philgeps.gov.ph)\r\n\r\n\r\n\r\n\r\n										            Approved by:\r\n\r\n\r\n              ____________________________\r\n Veralew DG. De Vera\r\nChairman, Bids and Awards Committee\r\nNational Food Authority', 'closed', 'notice_to_proceed', NULL, NULL, NULL, NULL, NULL, NULL, 'goods', 'AGSD', 'Central Office', 'Administrative and General Services Department', 4, 4, '2026-04-15 01:34:15', '2026-07-28 12:03:12'),
(4, 'svp', '202605001', 'Preventive Maintenance of Air conditioning Units for CY 2026 (Central Office New Bldg. & L-Shaped Bldg.)', 1289000.00, 'svp', NULL, NULL, 'Preventive Maintenance of Air conditioning Units for CY 2026 (Central Office New Bldg. & L-Shaped Bldg.)\r\n\r\nThe description of the item, including other requirements, is defined/indicated in the attached Technical Specifications/Terms of Reference/Terms and Conditions. Please submit the duly accomplished Tender Form and other required documents in a sealed envelope from May 12, 2026 until May 18, 2026, 10:00 AM, to:\r\n\r\nNational Food Authority\r\n6F General Services Division\r\nAdministrative and General Services Department\r\nVisayas Avenue, Barangay Vasra, Quezon City\r\n\r\nThe following eligibility documents are also required and shall be submitted:\r\n1. 2026 Mayor’s Permit/Business Permit;\r\n2. 2025 ITR or its equivalent document (2026 Quarterly ITR/Monthly Business tax Returns);\r\n3. Conformed Bid Form (Technical Specifications) / Terms of Reference;\r\n4. SEC Registration or DTI\r\n5. PhilGeps Certification or Printout of PhilGeps Organizational Number; and\r\n6. Original Notarized Omnibus Sworn Statement with Authority of Signatory (Secretary’s Certificate, SPA, Partnership Resolutions).\r\n7. Certification that the Service Provider is Daikin Accredited', 'closed', 'rfq', NULL, NULL, NULL, NULL, NULL, NULL, 'goods', 'AGSD-GSD', 'Central Office', 'Administrative and General Services Department', 6, 6, '2026-05-11 12:44:50', '2026-05-15 06:09:45'),
(5, 'svp', '202605002', 'PREVENTIVE MAINTENANCE OF FIRE ALARM AND DETECTION SYSTEM (FDAS) OF NFA CENTRAL OFFICE BUILDING', 337000.00, 'svp', NULL, NULL, 'PREVENTIVE MAINTENANCE OF FIRE ALARM AND DETECTION SYSTEM (FDAS) OF NFA CENTRAL OFFICE BUILDING\r\n\r\nThe description of the item, including other requirements, is defined/indicated in the attached Technical Specifications/Terms of Reference/Terms and Conditions. Please submit the duly accomplished Tender Form and other required documents in a sealed envelope from May 12, 2026 until May 18, 2026, 10:00 AM, to:\r\n\r\nNational Food Authority\r\n6F General Services Division\r\nAdministrative and General Services Department\r\nVisayas Avenue, Barangay Vasra, Quezon City\r\n\r\nThe following eligibility documents are also required and shall be submitted:\r\n1. 2026 Mayor’s Permit/Business Permit;\r\n2. 2025 ITR or its equivalent document (2026 Quarterly ITR/Monthly Business tax Returns);\r\n3. Conformed Bid Form (Technical Specifications) / Terms of Reference;\r\n4. SEC Registration or DTI\r\n5. PhilGeps Certification or Printout of PhilGeps Organizational Number; and\r\n6. Original Notarized Omnibus Sworn Statement with Authority of Signatory (Secretary’s Certificate, SPA, Partnership Resolutions).', 'closed', 'rfq', NULL, NULL, NULL, NULL, NULL, NULL, 'goods', 'AGSD-GSD', 'Central Office', 'Administrative and General Services Department', 6, 6, '2026-05-11 12:52:37', '2026-05-15 06:09:45'),
(6, 'svp', '202605003', '1 LOT PREVENTIVE MAINTENANCE OF GENERATOR SETS FOR CY 2026', 525000.00, 'svp', '2026-05-19 00:00:00', '2026-05-22 11:00:00', '1 LOT PREVENTIVE MAINTENANCE OF GENERATOR SETS FOR CY 2026\r\n\r\nThe description of the item, including other requirements, is defined/indicated in the attached Technical Specifications/Terms of Reference/Terms and Conditions. Please submit the duly accomplished Tender Form and other required documents in a sealed envelope from May 19, 2026 until May 22, 2025, 10:00 AM, to:\r\n\r\n National Food Authority\r\n6F General Services Division\r\nAdministrative and General Services Department\r\nVisayas Avenue, Barangay Vasra, Quezon City\r\n \r\n\r\nThe following eligibility documents are also required and shall be submitted:\r\n\r\n2026 Mayor’s Permit/Business Permit ;\r\n2025 ITR or its equivalent document (2026 Quarterly ITR/Monthly Business tax Returns);\r\nConformed Bid Form (Technical Specifications) / Terms of Reference;\r\nSEC Registration or DTI\r\nPhilGeps Certification or Printout of PhilGeps Organizational Number; and\r\nOriginal Notarized Omnibus Sworn Statement with Authority of Signatory (Secretary’s Certificate, SPA, Partnership Resolution).', 'closed', 'rfq', NULL, NULL, NULL, NULL, NULL, NULL, 'goods', 'AGSD-GSD', 'Central Office', 'Administrative and General Services Department', 6, 6, '2026-05-18 08:01:01', '2026-06-25 06:49:16'),
(7, 'svp', '202606001', '1 LOT PREVENTIVE MAINTENANCE OF ELECTRICAL FACILITIES FOR CY 2026', 1885000.00, 'svp', '2026-06-05 00:00:00', '2026-06-11 11:00:00', 'The description of the item, including other requirements, is defined/indicated in the attached Technical Specifications/Terms of Reference/Terms and Conditions. Please submit the duly accomplished Tender Form and other required documents in a sealed envelope from June 05, 2026 until June 11, 2026, 10:00 AM, to:\r\n\r\nNational Food Authority\r\n6F General Services Division\r\nAdministrative and General Services Department\r\nVisayas Avenue, Barangay Vasra, Quezon City\r\n\r\nThe following eligibility documents are also required and shall be submitted:\r\n\r\n1.	2026 Mayor’s Permit/Business Permit;\r\n2.	2025 ITR or its equivalent document (2026 Quarterly ITR/Monthly Business tax Returns);\r\n3.	Conformed Bid Form (Technical Specifications) / Terms of Reference;\r\n4.	SEC Registration or DTI\r\n5.	PhilGeps Certification or Printout of PhilGeps Organizational Number; and\r\n6.	Original Notarized Omnibus Sworn Statement with Authority of Signatory (Secretary’s Certificate, SPA, Partnership Resolutions).', 'closed', 'rfq', NULL, NULL, NULL, NULL, NULL, NULL, 'goods', 'AGSD-GSD', 'Central Office', 'Administrative and General Services Department', 6, 6, '2026-06-04 06:35:31', '2026-06-16 00:32:38'),
(8, 'svp', '202606002', 'SUPPLY AND DELIVERY OF GRAINS MAGAZINE 2026', 366298.00, 'svp', '2026-06-16 00:00:00', '2026-06-22 14:00:00', 'SUPPLY AND DELIVERY OF GRAINS MAGAZINE 2026\r\nThe description of the item, including other requirements, is defined/indicated in the attached Technical Specifications/Terms of Reference/Terms and Conditions. Please submit the duly accomplished Tender Form and other required documents in a sealed envelope from June 16, 2026 until June 22, 2026, 12:00 PM, to:\r\n\r\n \r\nNational Food Authority\r\n6F General Services Division\r\nAdministrative and General Services Department\r\nVisayas Avenue, Barangay Vasra, Quezon City\r\n\r\n\r\nThe following eligibility documents are also required and shall be submitted:\r\n1. 2026 Mayor’s Permit/Business Permit;\r\n2. 2025 ITR or its equivalent document (2026 Quarterly ITR/Monthly Business tax Returns);\r\n3. Conformed Bid Form (Technical Specifications) / Terms of Reference;\r\n4. SEC Registration or DTI\r\n5. PhilGeps Certification or Printout of PhilGeps Organizational Number; and\r\n6. Original Notarized Omnibus Sworn Statement with Authority of Signatory (Secretary’s Certificate, SPA, Partnership Resolutions).', 'closed', 'rfq', NULL, NULL, NULL, NULL, NULL, NULL, 'goods', 'AO-PAD', 'Central Office', 'Administrative and General Services Department', 6, 6, '2026-06-16 01:03:02', '2026-06-24 23:32:56'),
(9, 'competitive_bidding', '2026-03a', 'Supply and Delivery of Various Laptops', 107920000.00, 'competitive_bidding', '2026-07-16 00:00:00', '2026-08-05 13:30:00', 'Invitation to Bid for \r\nSupply and Delivery of Various Laptops, Desktop Computers, and Tablets\r\n\r\nThe National Food Authority (NFA) Central Office, through the approved 2026 Corporate Operating Budget intends to apply the sum of:\r\n\r\n \r\nLot No.\r\nProject ID No.\r\nProject Name\r\nApproved Budget for the Contract \r\n(in PhP inclusive of all applicable taxes)\r\n1\r\n2026-03a\r\nSupply and Delivery of Various Laptops\r\n107,920,000.00\r\n2\r\n2026-03b\r\nSupply and Delivery of Desktop Computers\r\n103,360,000.00\r\n3\r\n2026-03c\r\nSupply and Delivery of Tablets\r\n8,240,000.00 \r\nTOTAL\r\n219,520,000.00\r\n\r\n\r\n\r\nbeing the Approved Budget for the Contract (ABC) to payments under the contract for the Supply and Delivery of Various Laptops, Desktop Computers, and Tablets. Bids received in excess of the ABC shall be automatically rejected at bid opening. \r\n\r\nThe summary of bidding activities is as follows:\r\n\r\nAdvertisement/Posting of Invitation to Bid\r\nJuly 16, 2026\r\nIssuance and Availability of Bid Documents\r\nJuly 16, 2026\r\nPre-Bid Conference\r\nJuly 24, 2026\r\nDeadline of Submission of Request for Clarification\r\nJuly 26, 2026\r\nLast Day of Issuance of Bid Bulletin\r\nJuly 29, 2026\r\nDeadline for Submission of Bids\r\nAugust 5, 2026\r\n\r\n\r\nThe NFA Central Office, through the Bids and Awards Committee, now invites bids for the Three (3) Lots Supply and Delivery of Various Laptops, Desktop Computers, and Tablets. Delivery of the Goods is required within sixty (60) calendar days from receipt of Notice to Proceed. Bidders should have completed, at least three (3) years prior to the date of submission and receipt of bids, contracts similar to the Project. The description of an eligible bidder is contained in the Bidding Documents, particularly, in Section II. Instructions to Bidders.\r\n\r\nBidding will be conducted through competitive bidding procedures using a non-discretionary “pass/fail” criterion as specified in the IRR of RA No. 12009. Bidding is restricted to Filipino citizens/sole proprietorships, partnerships, or organizations with at least sixty percent (60%) interest or outstanding capital stock belonging to citizens of the Philippines, and to citizens or organizations of a country the laws or regulations of which grant similar rights or privileges to Filipino citizens, pursuant to RA No. 5183. \r\n\r\nInterested Bidders may obtain further information from the NFA Central Office BAC Secretariat and inspect the Bidding Documents at the address given below from 8:00 AM to 5:00 PM, Mondays to Fridays, except for holidays or inspect the Bidding Documents at the address and e-mail given below. \r\n\r\nA complete set of Bidding Documents may be acquired by interested Bidders on 16 July 2026 to 5 August 2026 from the address, e-mail, and website given below, upon payment of the applicable fee for the Bidding Documents, pursuant to the latest Guidelines issued by the GPPB, in the following amount:\r\n\r\n\r\nLot No.\r\nProject ID No.\r\nProject Name\r\nApproved Budget for the Contract \r\n(in PhP inclusive of all applicable taxes)\r\nCost of Bidding Documents\r\n(in PhP) \r\n1\r\n2026-03a\r\nSupply and Delivery of Various Laptops\r\n107,920,000.00\r\n50,000.00\r\n2\r\n2026-03b\r\nSupply and Delivery of Desktop Computers\r\n103,360,000.00\r\n50,000.00\r\n3\r\n2026-03c\r\nSupply and Delivery of Various Tablets\r\n8,240,000.00 \r\n10,000.00\r\n\r\n\r\nIt may also be downloaded free of charge from the website of the Philippine Government Electronic Procurement System (PhilGEPS) and the website of the Procuring Entity, provided that Bidders shall pay the applicable fee for the Bidding Documents not later than the submission of their bids.\r\n\r\nThe NFA Central Office will hold a Pre-Bid Conference on 24 July 2026, 1:30 p.m. onwards at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or through video conferencing or webcasting via the Google Meet platform, which shall be open to prospective Bidders. Meeting details shall be made available to interested bidders upon request sent to the official e-mail of NFA Central Office at bac@nfa.gov.ph. The deadline for clarifications shall be on 26 July, 2026 and the last day for issuance of relevant bid bulletins shall be on 29 July, 2026.\r\n \r\nBids must be duly received by the Bids and Awards Committee (BAC) Secretariat through manual submission at the office address indicated below, on or before 5 August 2026, 1:30 p.m. Late bids shall not be accepted.\r\n\r\nAll Bids must be accompanied by a Bid Security in any of the acceptable forms and in the amount stated in ITB Clause 16.1.\r\n\r\nBid opening shall be on 5 August 2026, 2:00 p.m. at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or via video conferencing or webcasting via the Google Meet platform.  Bids will be opened in the presence of the Bidders’ representatives who choose to attend the activity.\r\n\r\nIn accordance with Government Procurement Policy Board (GPPB) Circular 06-2005-Tie-Breaking Method, the Bids and Awards Committee (BAC) shall use a non-discretionary and non-discriminatory measure based on sheer luck or chance, which is “DRAW LOTS”, in the event that the two (2) or more bidders have been post-qualified and determined as the bidder having the Lowest Calculated and Responsive Bid (LCRB), based on the following procedures: \r\n\r\nIn alphabetical order, the bidder shall pick one roll of paper:\r\n\r\nThe lucky bidders who pick the paper with a “CONGRATULATIONS” remark shall be declared as the final bidder having the LCRB and recommended for the award of the contract.\r\n\r\nThe National Food Authority reserves the right to reject any and all bids, declare a failure of bidding, or not award the contract at any time prior to contract award in accordance with Section 70 of R.A. No. 12009, without incurring any liability to the affected Bidder or Bidders.\r\n\r\nFor further information, please refer to:\r\n\r\nName of Contact Person:	ENGR. LESLIE M. NAVARRO\r\n						Head, BAC Secretariat \r\nPostal Address		:	National Food Authority Central Office\r\n						Visayas Avenue, Brgy. Vasra, Diliman,\r\n						Quezon City, 1128\r\nE-mail Address		:	bac@nfa.gov.ph\r\n\r\nYou may visit the following websites for downloading of Bidding Documents:\r\n\r\nNFA Central Office website (www.nfa.gov.ph)\r\nPhilGEPS website (www.philgeps.gov.ph)\r\n\r\n\r\nApproved by:\r\n\r\n\r\n              \r\nVERALEW DG. DE VERA\r\nChairperson, Bids and Awards Committee\r\nNational Food Authority\r\n\r\n\r\n\r\n16 July 2026', 'open', 'supplemental_bid_bulletin', NULL, NULL, NULL, NULL, NULL, NULL, 'goods', 'CPMSD', 'Central Office', 'Administrative and General Services Department', 4, 4, '2026-07-15 14:08:02', '2026-07-29 12:45:25'),
(10, 'competitive_bidding', '2026-03b', 'Supply and Delivery of Desktop Computers', 103360000.00, 'competitive_bidding', '2026-07-16 00:00:00', '2026-08-05 13:30:00', 'Invitation to Bid for \r\nSupply and Delivery of Various Laptops, Desktop Computers, and Tablets\r\n\r\nThe National Food Authority (NFA) Central Office, through the approved 2026 Corporate Operating Budget intends to apply the sum of:\r\n\r\n \r\nLot No.\r\nProject ID No.\r\nProject Name\r\nApproved Budget for the Contract \r\n(in PhP inclusive of all applicable taxes)\r\n1\r\n2026-03a\r\nSupply and Delivery of Various Laptops\r\n107,920,000.00\r\n2\r\n2026-03b\r\nSupply and Delivery of Desktop Computers\r\n103,360,000.00\r\n3\r\n2026-03c\r\nSupply and Delivery of Tablets\r\n8,240,000.00 \r\nTOTAL\r\n219,520,000.00\r\n\r\n\r\n\r\nbeing the Approved Budget for the Contract (ABC) to payments under the contract for the Supply and Delivery of Various Laptops, Desktop Computers, and Tablets. Bids received in excess of the ABC shall be automatically rejected at bid opening. \r\n\r\nThe summary of bidding activities is as follows:\r\n\r\nAdvertisement/Posting of Invitation to Bid\r\nJuly 16, 2026\r\nIssuance and Availability of Bid Documents\r\nJuly 16, 2026\r\nPre-Bid Conference\r\nJuly 24, 2026\r\nDeadline of Submission of Request for Clarification\r\nJuly 26, 2026\r\nLast Day of Issuance of Bid Bulletin\r\nJuly 29, 2026\r\nDeadline for Submission of Bids\r\nAugust 5, 2026\r\n\r\n\r\nThe NFA Central Office, through the Bids and Awards Committee, now invites bids for the Three (3) Lots Supply and Delivery of Various Laptops, Desktop Computers, and Tablets. Delivery of the Goods is required within sixty (60) calendar days from receipt of Notice to Proceed. Bidders should have completed, at least three (3) years prior to the date of submission and receipt of bids, contracts similar to the Project. The description of an eligible bidder is contained in the Bidding Documents, particularly, in Section II. Instructions to Bidders.\r\n\r\nBidding will be conducted through competitive bidding procedures using a non-discretionary “pass/fail” criterion as specified in the IRR of RA No. 12009. Bidding is restricted to Filipino citizens/sole proprietorships, partnerships, or organizations with at least sixty percent (60%) interest or outstanding capital stock belonging to citizens of the Philippines, and to citizens or organizations of a country the laws or regulations of which grant similar rights or privileges to Filipino citizens, pursuant to RA No. 5183. \r\n\r\nInterested Bidders may obtain further information from the NFA Central Office BAC Secretariat and inspect the Bidding Documents at the address given below from 8:00 AM to 5:00 PM, Mondays to Fridays, except for holidays or inspect the Bidding Documents at the address and e-mail given below. \r\n\r\nA complete set of Bidding Documents may be acquired by interested Bidders on 16 July 2026 to 5 August 2026 from the address, e-mail, and website given below, upon payment of the applicable fee for the Bidding Documents, pursuant to the latest Guidelines issued by the GPPB, in the following amount:\r\n\r\n\r\nLot No.\r\nProject ID No.\r\nProject Name\r\nApproved Budget for the Contract \r\n(in PhP inclusive of all applicable taxes)\r\nCost of Bidding Documents\r\n(in PhP) \r\n1\r\n2026-03a\r\nSupply and Delivery of Various Laptops\r\n107,920,000.00\r\n50,000.00\r\n2\r\n2026-03b\r\nSupply and Delivery of Desktop Computers\r\n103,360,000.00\r\n50,000.00\r\n3\r\n2026-03c\r\nSupply and Delivery of Various Tablets\r\n8,240,000.00 \r\n10,000.00\r\n\r\n\r\nIt may also be downloaded free of charge from the website of the Philippine Government Electronic Procurement System (PhilGEPS) and the website of the Procuring Entity, provided that Bidders shall pay the applicable fee for the Bidding Documents not later than the submission of their bids.\r\n\r\nThe NFA Central Office will hold a Pre-Bid Conference on 24 July 2026, 1:30 p.m. onwards at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or through video conferencing or webcasting via the Google Meet platform, which shall be open to prospective Bidders. Meeting details shall be made available to interested bidders upon request sent to the official e-mail of NFA Central Office at bac@nfa.gov.ph. The deadline for clarifications shall be on 26 July, 2026 and the last day for issuance of relevant bid bulletins shall be on 29 July, 2026.\r\n \r\nBids must be duly received by the Bids and Awards Committee (BAC) Secretariat through manual submission at the office address indicated below, on or before 5 August 2026, 1:30 p.m. Late bids shall not be accepted.\r\n\r\nAll Bids must be accompanied by a Bid Security in any of the acceptable forms and in the amount stated in ITB Clause 16.1.\r\n\r\nBid opening shall be on 5 August 2026, 2:00 p.m. at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or via video conferencing or webcasting via the Google Meet platform.  Bids will be opened in the presence of the Bidders’ representatives who choose to attend the activity.\r\n\r\nIn accordance with Government Procurement Policy Board (GPPB) Circular 06-2005-Tie-Breaking Method, the Bids and Awards Committee (BAC) shall use a non-discretionary and non-discriminatory measure based on sheer luck or chance, which is “DRAW LOTS”, in the event that the two (2) or more bidders have been post-qualified and determined as the bidder having the Lowest Calculated and Responsive Bid (LCRB), based on the following procedures: \r\n\r\nIn alphabetical order, the bidder shall pick one roll of paper:\r\n\r\nThe lucky bidders who pick the paper with a “CONGRATULATIONS” remark shall be declared as the final bidder having the LCRB and recommended for the award of the contract.\r\n\r\nThe National Food Authority reserves the right to reject any and all bids, declare a failure of bidding, or not award the contract at any time prior to contract award in accordance with Section 70 of R.A. No. 12009, without incurring any liability to the affected Bidder or Bidders.\r\n\r\nFor further information, please refer to:\r\n\r\nName of Contact Person:	ENGR. LESLIE M. NAVARRO\r\n						Head, BAC Secretariat \r\nPostal Address		:	National Food Authority Central Office\r\n						Visayas Avenue, Brgy. Vasra, Diliman,\r\n						Quezon City, 1128\r\nE-mail Address		:	bac@nfa.gov.ph\r\n\r\nYou may visit the following websites for downloading of Bidding Documents:\r\n\r\nNFA Central Office website (www.nfa.gov.ph)\r\nPhilGEPS website (www.philgeps.gov.ph)\r\n\r\n\r\nApproved by:\r\n\r\n\r\n              \r\nVERALEW DG. DE VERA\r\nChairperson, Bids and Awards Committee\r\nNational Food Authority\r\n\r\n\r\n\r\n16 July 2026', 'open', 'supplemental_bid_bulletin', NULL, NULL, NULL, NULL, NULL, NULL, 'goods', 'CPMSD', 'Central Office', 'Administrative and General Services Department', 4, 4, '2026-07-15 14:10:10', '2026-07-29 12:44:22'),
(11, 'competitive_bidding', '2026-03c', 'Supply and Delivery of Various Tablets', 8240000.00, 'competitive_bidding', '2026-07-16 00:00:00', '2026-08-05 13:30:00', 'Invitation to Bid for \r\nSupply and Delivery of Various Laptops, Desktop Computers, and Tablets\r\n\r\nThe National Food Authority (NFA) Central Office, through the approved 2026 Corporate Operating Budget intends to apply the sum of:\r\n\r\n \r\nLot No.\r\nProject ID No.\r\nProject Name\r\nApproved Budget for the Contract \r\n(in PhP inclusive of all applicable taxes)\r\n1\r\n2026-03a\r\nSupply and Delivery of Various Laptops\r\n107,920,000.00\r\n2\r\n2026-03b\r\nSupply and Delivery of Desktop Computers\r\n103,360,000.00\r\n3\r\n2026-03c\r\nSupply and Delivery of Tablets\r\n8,240,000.00 \r\nTOTAL\r\n219,520,000.00\r\n\r\n\r\n\r\nbeing the Approved Budget for the Contract (ABC) to payments under the contract for the Supply and Delivery of Various Laptops, Desktop Computers, and Tablets. Bids received in excess of the ABC shall be automatically rejected at bid opening. \r\n\r\nThe summary of bidding activities is as follows:\r\n\r\nAdvertisement/Posting of Invitation to Bid\r\nJuly 16, 2026\r\nIssuance and Availability of Bid Documents\r\nJuly 16, 2026\r\nPre-Bid Conference\r\nJuly 24, 2026\r\nDeadline of Submission of Request for Clarification\r\nJuly 26, 2026\r\nLast Day of Issuance of Bid Bulletin\r\nJuly 29, 2026\r\nDeadline for Submission of Bids\r\nAugust 5, 2026\r\n\r\n\r\nThe NFA Central Office, through the Bids and Awards Committee, now invites bids for the Three (3) Lots Supply and Delivery of Various Laptops, Desktop Computers, and Tablets. Delivery of the Goods is required within sixty (60) calendar days from receipt of Notice to Proceed. Bidders should have completed, at least three (3) years prior to the date of submission and receipt of bids, contracts similar to the Project. The description of an eligible bidder is contained in the Bidding Documents, particularly, in Section II. Instructions to Bidders.\r\n\r\nBidding will be conducted through competitive bidding procedures using a non-discretionary “pass/fail” criterion as specified in the IRR of RA No. 12009. Bidding is restricted to Filipino citizens/sole proprietorships, partnerships, or organizations with at least sixty percent (60%) interest or outstanding capital stock belonging to citizens of the Philippines, and to citizens or organizations of a country the laws or regulations of which grant similar rights or privileges to Filipino citizens, pursuant to RA No. 5183. \r\n\r\nInterested Bidders may obtain further information from the NFA Central Office BAC Secretariat and inspect the Bidding Documents at the address given below from 8:00 AM to 5:00 PM, Mondays to Fridays, except for holidays or inspect the Bidding Documents at the address and e-mail given below. \r\n\r\nA complete set of Bidding Documents may be acquired by interested Bidders on 16 July 2026 to 5 August 2026 from the address, e-mail, and website given below, upon payment of the applicable fee for the Bidding Documents, pursuant to the latest Guidelines issued by the GPPB, in the following amount:\r\n\r\n\r\nLot No.\r\nProject ID No.\r\nProject Name\r\nApproved Budget for the Contract \r\n(in PhP inclusive of all applicable taxes)\r\nCost of Bidding Documents\r\n(in PhP) \r\n1\r\n2026-03a\r\nSupply and Delivery of Various Laptops\r\n107,920,000.00\r\n50,000.00\r\n2\r\n2026-03b\r\nSupply and Delivery of Desktop Computers\r\n103,360,000.00\r\n50,000.00\r\n3\r\n2026-03c\r\nSupply and Delivery of Various Tablets\r\n8,240,000.00 \r\n10,000.00\r\n\r\n\r\nIt may also be downloaded free of charge from the website of the Philippine Government Electronic Procurement System (PhilGEPS) and the website of the Procuring Entity, provided that Bidders shall pay the applicable fee for the Bidding Documents not later than the submission of their bids.\r\n\r\nThe NFA Central Office will hold a Pre-Bid Conference on 24 July 2026, 1:30 p.m. onwards at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or through video conferencing or webcasting via the Google Meet platform, which shall be open to prospective Bidders. Meeting details shall be made available to interested bidders upon request sent to the official e-mail of NFA Central Office at bac@nfa.gov.ph. The deadline for clarifications shall be on 26 July, 2026 and the last day for issuance of relevant bid bulletins shall be on 29 July, 2026.\r\n \r\nBids must be duly received by the Bids and Awards Committee (BAC) Secretariat through manual submission at the office address indicated below, on or before 5 August 2026, 1:30 p.m. Late bids shall not be accepted.\r\n\r\nAll Bids must be accompanied by a Bid Security in any of the acceptable forms and in the amount stated in ITB Clause 16.1.\r\n\r\nBid opening shall be on 5 August 2026, 2:00 p.m. at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or via video conferencing or webcasting via the Google Meet platform.  Bids will be opened in the presence of the Bidders’ representatives who choose to attend the activity.\r\n\r\nIn accordance with Government Procurement Policy Board (GPPB) Circular 06-2005-Tie-Breaking Method, the Bids and Awards Committee (BAC) shall use a non-discretionary and non-discriminatory measure based on sheer luck or chance, which is “DRAW LOTS”, in the event that the two (2) or more bidders have been post-qualified and determined as the bidder having the Lowest Calculated and Responsive Bid (LCRB), based on the following procedures: \r\n\r\nIn alphabetical order, the bidder shall pick one roll of paper:\r\n\r\nThe lucky bidders who pick the paper with a “CONGRATULATIONS” remark shall be declared as the final bidder having the LCRB and recommended for the award of the contract.\r\n\r\nThe National Food Authority reserves the right to reject any and all bids, declare a failure of bidding, or not award the contract at any time prior to contract award in accordance with Section 70 of R.A. No. 12009, without incurring any liability to the affected Bidder or Bidders.\r\n\r\nFor further information, please refer to:\r\n\r\nName of Contact Person:	ENGR. LESLIE M. NAVARRO\r\n						Head, BAC Secretariat \r\nPostal Address		:	National Food Authority Central Office\r\n						Visayas Avenue, Brgy. Vasra, Diliman,\r\n						Quezon City, 1128\r\nE-mail Address		:	bac@nfa.gov.ph\r\n\r\nYou may visit the following websites for downloading of Bidding Documents:\r\n\r\nNFA Central Office website (www.nfa.gov.ph)\r\nPhilGEPS website (www.philgeps.gov.ph)\r\n\r\n\r\nApproved by:\r\n\r\n\r\n              \r\nVERALEW DG. DE VERA\r\nChairperson, Bids and Awards Committee\r\nNational Food Authority\r\n\r\n\r\n\r\n16 July 2026', 'open', 'supplemental_bid_bulletin', NULL, NULL, NULL, NULL, NULL, NULL, 'goods', 'CPMSD', 'Central Office', 'Administrative and General Services Department', 4, 4, '2026-07-15 14:12:40', '2026-07-29 12:43:36'),
(12, 'competitive_bidding', '2026-02', 'Supply and Delivery of One Hundred Fifty (150) Units Custom-Built Delivery and Cargo Trucks', 705000000.00, 'competitive_bidding', '2026-07-17 00:01:00', '2026-08-10 09:00:00', 'Invitation to Bid for Supply and Delivery of One Hundred Fifty (150) Units Custom-Built Delivery and Cargo Trucks\r\n\r\nThe National Food Authority (NFA) Central Office, through the approved 2026 Corporate Operating Budget intends to apply the sum of Seven Hundred Five Million Pesos Only (Php 705,000,000.00), inclusive of all applicable taxes, being the Approved Budget for the Contract (ABC) to payments under the contract for the Supply and Delivery of One Hundred Fifty (150) Units Custom-Built Delivery and Cargo Trucks. Bids received in excess of the ABC shall be automatically rejected at bid opening. \r\n\r\nLot No.\r\nProject ID No.\r\nQty.\r\nItem/Description\r\nApproved Budget for the Contract \r\n(in PhP)\r\nPrice of Bid Documents\r\n(in PhP)\r\n1\r\n2026-02\r\nOne (1) Lot\r\nSupply and Delivery of One Hundred Fifty (150) Units Custom-Built Delivery and Cargo Trucks\r\n\r\nItem 1: 110 units 6-Wheeler 120 bags capacity Delivery Trucks\r\n\r\nItem 2: 28 units 6-Wheeler 180 bags capacity Delivery Trucks\r\n\r\nItem 3: 12 units 10-Wheeler 360 bags capacity Cargo Trucks\r\n705,000,000.00\r\n\r\n\r\n\r\n\r\n\r\nABC Per item:\r\n462,859,110.06\r\n\r\n\r\n\r\n\r\n159,281,681.91\r\n\r\n\r\n\r\n\r\n82,589,208.03\r\n75,000.00\r\n\r\n\r\nThe summary of bidding activities is as follows:\r\n\r\nAdvertisement/Posting of Invitation to Bid\r\nJuly 17, 2026\r\nIssuance and Availability of Bid Documents\r\nJuly 17, 2026\r\nPre-Bid Conference\r\nJuly 27, 2026\r\nDeadline of Submission of Request for Clarification\r\nJuly 29, 2026\r\nLast Day of Issuance of Bid Bulletin\r\nAugust 3, 2026\r\nDeadline for Submission of Bids\r\nAugust 10, 2026\r\n\r\n\r\nThe NFA Central Office, through the Bids and Awards Committee, now invites bids for the above Supply and Delivery of One Hundred Fifty (150) Units Custom-Built Delivery and Cargo Trucks. Delivery of the Goods is required within one hundred eighty (180) calendar days from receipt of Notice to Proceed. Bidders should have completed, at least ten (10) years from the date of submission and receipt of bids, contracts similar to the Project. The description of an eligible bidder is contained in the Bidding Documents, particularly, in Section II. Instructions to Bidders.\r\n\r\nBidding will be conducted through competitive bidding procedures using a non-discretionary “pass/fail” criterion as specified in the IRR of RA No. 12009. Bidding is open to all interested Bidders, whether local or foreign, subject to the conditions for eligibility provided in the IRR.\r\n\r\nInterested Bidders may obtain further information from the NFA Central Office BAC Secretariat and inspect the Bidding Documents at the address given below from 8:00 AM to 5:00 PM.\r\n\r\nA complete set of Bidding Documents may be acquired by interested Bidders on 17 July 2026 to 10 August 2026 from the address, e-mail, and website given below upon payment of the applicable fee for the Bidding Documents, pursuant to the latest Guidelines issued by the GPPB, in the amount of SEVENTY-FIVE THOUSAND PESOS ONLY (PhP 75,000.00).\r\n\r\nIt may also be downloaded free of charge from the website of the Philippine Government Electronic Procurement System (PhilGEPS) and the website of the Procuring Entity, provided that Bidders shall pay the applicable fee for the Bidding Documents not later than the submission of their bids.\r\n\r\nThe NFA Central Office will hold a Pre-Bid Conference on 27 July 2026, 9:00 a.m. onwards at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or through video conferencing or webcasting via the Google Meet platform, which shall be open to prospective Bidders. Meeting details shall be made available to interested bidders upon request sent to the official e-mail of NFA Central Office at bac@nfa.gov.ph. The deadline for clarifications shall be on 29 July 2026 and the last day for issuance of relevant bid bulletins shall be on 3 August 2026.\r\n \r\nBids must be duly received by the Bids and Awards Committee (BAC) Secretariat through manual submission at the office address indicated below, on or before 10 August 2026, 8:30 a.m.. Late bids shall not be accepted.\r\n\r\nAll Bids must be accompanied by a Bid Security in any of the acceptable forms and in the amount stated in ITB Clause 16.1.\r\n\r\nBid opening shall be on 10 August 2026, 9:00 a.m. at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or via video conferencing or webcasting via the Google Meet platform.  Bids will be opened in the presence of the Bidders’ representatives who choose to attend the activity.\r\n\r\nIn accordance with Government Procurement Policy Board (GPPB) Circular 06-2005-Tie-Breaking Method, the Bids and Awards Committee (BAC) shall use a non-discretionary and non-discriminatory measure based on sheer luck or chance, which is “DRAW LOTS”, in the event that the two (2) or more bidders have been post-qualified and determined as the bidder having the Lowest Calculated and Responsive Bid (LCRB), based on the following procedures: \r\nIn alphabetical order, the bidder shall pick one roll of paper:\r\nThe lucky bidders who pick the paper with a “CONGRATULATIONS” remark shall be declared as the final bidder having the LCRB and recommended for the award of the contract.\r\n\r\nThe National Food Authority reserves the right to reject any and all bids, declare a failure of bidding, or not award the contract at any time prior to contract award in accordance with Section 70 of R.A. No. 12009, without incurring any liability to the affected Bidder or Bidders.\r\n\r\nFor further information, please refer to:\r\n\r\nName of Contact Person	:	ENGR. LESLIE M. NAVARRO\r\n						Head, BAC Secretariat \r\nPostal Address		:	National Food Authority Central Office\r\n						Visayas Avenue, Brgy. Vasra, Diliman,\r\n						Quezon City, 1128\r\nE-mail Address		:	bac@nfa.gov.ph\r\n\r\nYou may visit the following websites for downloading of Bidding Documents:\r\n\r\nNFA Central Office website (www.nfa.gov.ph)\r\nPhilGEPS website (www.philgeps.gov.ph)\r\n\r\n\r\n\r\n\r\n										            Approved by:\r\n\r\n\r\n              ____________________________\r\n Veralew DG. De Vera\r\nChairman, Bids and Awards Committee\r\nNational Food Authority\r\n\r\n\r\n\r\n17 July 2026', 'open', 'bid_notice', NULL, NULL, NULL, NULL, NULL, NULL, 'goods', 'AGSD', 'Central Office', 'Administrative and General Services Department', 4, 4, '2026-07-16 12:44:04', '2026-07-16 23:28:48');

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
-- Dumping data for table `procurement_activity_logs`
--

INSERT INTO `procurement_activity_logs` (`id`, `parent_procurement_id`, `user_id`, `action_type`, `document_type`, `document_id`, `before_snapshot`, `after_snapshot`, `reason`, `file_hash`, `approval_reference`, `created_at`) VALUES
(2, 2, 4, 'create_parent', 'bid_notice', 2, NULL, '{\"id\":2,\"procurement_mode\":\"competitive_bidding\",\"reference_number\":\"2026-01\",\"procurement_title\":\"Procurement of Property Information System\",\"abc\":\"14000000.00\",\"mode_of_procurement\":\"competitive_bidding\",\"posting_date\":\"2026-04-15 10:00:00\",\"bid_submission_deadline\":\"2026-05-06 08:30:00\",\"description\":\"Invitation to Bid for Procurement of Property Information System\\r\\n\\r\\nThe National Food Authority (NFA) Central Office, through the approved 2026 Corporate Operating Budget intends to apply the sum of Fourteen Million Pesos Only (Php 14,000,000.00), inclusive of all applicable taxes, being the Approved Budget for the Contract (ABC) to payments under the contract for the Procurement of Property Information System. Bids received in excess of the ABC shall be automatically rejected at bid opening. \\r\\n\\r\\nLot No.\\r\\nProject ID No.\\r\\nQty.\\r\\nItem/Description\\r\\nApproved Budget for the Contract \\r\\n(in PhP)\\r\\nPrice of Bid Documents\\r\\n(in PhP)\\r\\n1\\r\\n2026-01\\r\\nOne (1) Lot\\r\\nProcurement of Property Information System\\r\\n14,000,000.00\\r\\n25,000.00\\r\\n\\r\\n\\r\\nThe summary of bidding activities is as follows:\\r\\n\\r\\nAdvertisement/Posting of Invitation to Bid\\r\\nApril 15, 2026\\r\\nIssuance and Availability of Bid Documents\\r\\nApril 15, 2026\\r\\nPre-Bid Conference\\r\\nApril 22, 2026\\r\\nDeadline of Submission of Request for Clarification\\r\\nApril 26, 2026\\r\\nLast Day of Issuance of Bid Bulletin\\r\\nApril 29, 2026\\r\\nDeadline for Submission of Bids\\r\\nMay 6, 2026\\r\\n\\r\\n\\r\\nThe NFA Central Office, through the Bids and Awards Committee, now invites bids for the above Procurement Project for Procurement of Property Information System. Delivery of the Goods is required within one hundred eighty (180) calendar days from receipt of Notice to Proceed. Bidders should have completed, at least five (5) years from the date of submission and receipt of bids, contracts similar to the Project. The description of an eligible bidder is contained in the Bidding Documents, particularly, in Section II. Instructions to Bidders.\\r\\n\\r\\nBidding will be conducted through competitive bidding procedures using a non-discretionary \\u201cpass/fail\\u201d criterion as specified in the IRR of RA No. 12009. Bidding is open to all interested Bidders, whether local or foreign, subject to the conditions for eligibility provided in the IRR.\\r\\n\\r\\nInterested Bidders may obtain further information from the NFA Central Office BAC Secretariat and inspect the Bidding Documents at the address given below from 8:00 AM to 5:00 PM.\\r\\n\\r\\nA complete set of Bidding Documents may be acquired by interested Bidders on 15 April 2026 to 06 May 2026 from the address, e-mail, and website given below upon payment of the applicable fee for the Bidding Documents, pursuant to the latest Guidelines issued by the GPPB, in the amount of TWENTY-FIVE THOUSAND PESOS ONLY (PhP 25,000.00).\\r\\n\\r\\nIt may also be downloaded free of charge from the website of the Philippine Government Electronic Procurement System (PhilGEPS) and the website of the Procuring Entity, provided that Bidders shall pay the applicable fee for the Bidding Documents not later than the submission of their bids.\\r\\n\\r\\nThe NFA Central Office will hold a Pre-Bid Conference on 22 April 2026, 9:00 a.m. onwards at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or through video conferencing or webcasting via the Google Meet platform, which shall be open to prospective Bidders. Meeting details shall be made available to interested bidders upon request sent to the official e-mail of NFA Central Office at bac@nfa.gov.ph. The deadline for clarifications shall be on 26 April 2026 and the last day for issuance of relevant bid bulletins shall be on 29 April 2026.\\r\\n \\r\\nBids must be duly received by the Bids and Awards Committee (BAC) Secretariat through manual submission at the office address indicated below, on or before 06 May 2026, 8:30 a.m.. Late bids shall not be accepted.\\r\\n\\r\\nAll Bids must be accompanied by a Bid Security in any of the acceptable forms and in the amount stated in ITB Clause 16.1.\\r\\n\\r\\nBid opening shall be on 06 May 2026, 9:00 a.m. at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or via video conferencing or webcasting via the Google Meet platform.  Bids will be opened in the presence of the Bidders\\u2019 representatives who choose to attend the activity.\\r\\n\\r\\nIn accordance with Government Procurement Policy Board (GPPB) Circular 06-2005-Tie-Breaking Method, the Bids and Awards Committee (BAC) shall use a non-discretionary and non-discriminatory measure based on sheer luck or chance, which is \\u201cDRAW LOTS\\u201d, in the event that the two (2) or more bidders have been post-qualified and determined as the bidder having the Lowest Calculated and Responsive Bid (LCRB), based on the following procedures: \\r\\nIn alphabetical order, the bidder shall pick one roll of paper:\\r\\nThe lucky bidders who pick the paper with a \\u201cCONGRATULATIONS\\u201d remark shall be declared as the final bidder having the LCRB and recommended for the award of the contract.\\r\\n\\r\\nThe National Food Authority reserves the right to reject any and all bids, declare a failure of bidding, or not award the contract at any time prior to contract award in accordance with Section 70 of R.A. No. 12009, without incurring any liability to the affected Bidder or Bidders.\\r\\n\\r\\nFor further information, please refer to:\\r\\n\\r\\nName of Contact Person\\t:\\tENGR. LESLIE M. NAVARRO\\r\\n\\t\\t\\t\\t\\t\\tHead, BAC Secretariat \\r\\nPostal Address\\t\\t:\\tNational Food Authority Central Office\\r\\n\\t\\t\\t\\t\\t\\tVisayas Avenue, Brgy. Vasra, Diliman,\\r\\n\\t\\t\\t\\t\\t\\tQuezon City, 1128\\r\\nE-mail Address\\t\\t:\\tbac@nfa.gov.ph\\r\\n\\r\\nYou may visit the following websites for downloading of Bidding Documents:\\r\\n\\r\\nNFA Central Office website (www.nfa.gov.ph)\\r\\nPhilGEPS website (www.philgeps.gov.ph)\\r\\n\\r\\n\\r\\n\\r\\n\\r\\n\\t\\t\\t\\t\\t\\t\\t\\t\\t\\t            Approved by:\\r\\n\\r\\n\\r\\n              ____________________________\\r\\n Veralew DG. De Vera\\r\\nChairman, Bids and Awards Committee\\r\\nNational Food Authority\",\"posting_status\":\"scheduled\",\"current_stage\":\"bid_notice\",\"archived_at\":null,\"archive_reason\":null,\"archived_by\":null,\"archive_approval_reference\":null,\"archive_approved_by\":null,\"archive_approved_at\":null,\"category\":\"goods\",\"end_user_unit\":\"AGSD\",\"region\":\"Central Office\",\"branch\":\"Administrative and General Services Department\",\"created_by\":4,\"updated_by\":4,\"created_at\":\"2026-04-15 09:34:15\",\"updated_at\":\"2026-04-15 09:34:15\",\"creator_username\":\"aacarillo\",\"creator_firstname\":\"Angelo\",\"creator_lastname\":\"Carillo\"}', 'Competitive Bidding procurement record created.', '3dd8f8ce706517b2740737f7cbec545b7b14fd7d18ecc25693a567ad4fcf48f2', NULL, '2026-04-15 01:34:15'),
(3, 2, 4, 'create_document', 'supplemental_bid_bulletin', 1, NULL, '{\"id\":1,\"parent_procurement_id\":2,\"title\":\"Supplemental bid Bulletin No.1\",\"description\":\"Supplemental Bid Bulletin No.1\",\"file_path\":\"storage/uploads/notices/notice_69df4ab32d8fb5.50375415.pdf\",\"file_hash\":\"afa0515be75dce8a0a392a83699175e8a3acbcebd8bc4813c79e06c5047af45a\",\"document_type\":\"supplemental_bid_bulletin\",\"sequence_stage\":2,\"posted_at\":\"2026-04-15 17:00:00\",\"created_by\":4,\"updated_by\":4,\"created_at\":\"2026-04-15 16:22:11\",\"updated_at\":\"2026-04-15 16:22:11\",\"creator_username\":\"aacarillo\",\"creator_firstname\":\"Angelo\",\"creator_lastname\":\"Carillo\"}', 'Competitive Bidding document posted.', 'afa0515be75dce8a0a392a83699175e8a3acbcebd8bc4813c79e06c5047af45a', NULL, '2026-04-15 08:22:11'),
(6, 2, 4, 'create_document', 'supplemental_bid_bulletin', 3, NULL, '{\"id\":3,\"parent_procurement_id\":2,\"title\":\"Supplemental bid Bulletin No. 2\",\"description\":\"Supplemental bid Bulletin No.2\",\"file_path\":\"storage/uploads/notices/notice_69f09f0668f624.78294584.pdf\",\"file_hash\":\"690b01303315b8f145216ab9a211ac7675ee4ece706361d9a6ceedf12e3461fe\",\"document_type\":\"supplemental_bid_bulletin\",\"sequence_stage\":2,\"posted_at\":\"2026-04-28 20:00:00\",\"created_by\":4,\"updated_by\":4,\"created_at\":\"2026-04-28 19:50:30\",\"updated_at\":\"2026-04-28 19:50:30\",\"creator_username\":\"aacarillo\",\"creator_firstname\":\"Angelo\",\"creator_lastname\":\"Carillo\"}', 'Competitive Bidding document posted.', '690b01303315b8f145216ab9a211ac7675ee4ece706361d9a6ceedf12e3461fe', NULL, '2026-04-28 11:50:30'),
(7, 4, 6, 'create_parent', 'svp_procurement', NULL, NULL, '{\"id\":4,\"procurement_mode\":\"svp\",\"reference_number\":\"202605001\",\"procurement_title\":\"Preventive Maintenance of Air conditioning Units for CY 2026 (Central Office New Bldg. & L-Shaped Bldg.)\",\"abc\":\"1289000.00\",\"mode_of_procurement\":\"svp\",\"posting_date\":null,\"bid_submission_deadline\":null,\"description\":\"Preventive Maintenance of Air conditioning Units for CY 2026 (Central Office New Bldg. & L-Shaped Bldg.)\\r\\n\\r\\nThe description of the item, including other requirements, is defined/indicated in the attached Technical Specifications/Terms of Reference/Terms and Conditions. Please submit the duly accomplished Tender Form and other required documents in a sealed envelope from May 12, 2026 until May 18, 2026, 10:00 AM, to:\\r\\n\\r\\nNational Food Authority\\r\\n6F General Services Division\\r\\nAdministrative and General Services Department\\r\\nVisayas Avenue, Barangay Vasra, Quezon City\\r\\n\\r\\nThe following eligibility documents are also required and shall be submitted:\\r\\n1. 2026 Mayor\\u2019s Permit/Business Permit;\\r\\n2. 2025 ITR or its equivalent document (2026 Quarterly ITR/Monthly Business tax Returns);\\r\\n3. Conformed Bid Form (Technical Specifications) / Terms of Reference;\\r\\n4. SEC Registration or DTI\\r\\n5. PhilGeps Certification or Printout of PhilGeps Organizational Number; and\\r\\n6. Original Notarized Omnibus Sworn Statement with Authority of Signatory (Secretary\\u2019s Certificate, SPA, Partnership Resolutions).\\r\\n7. Certification that the Service Provider is Daikin Accredited\",\"posting_status\":\"scheduled\",\"current_stage\":\"rfq\",\"archived_at\":null,\"archive_reason\":null,\"archived_by\":null,\"archive_approval_reference\":null,\"archive_approved_by\":null,\"archive_approved_at\":null,\"category\":\"goods\",\"end_user_unit\":\"AGSD-GSD\",\"region\":\"Central Office\",\"branch\":\"Administrative and General Services Department\",\"created_by\":6,\"updated_by\":6,\"created_at\":\"2026-05-11 20:44:50\",\"updated_at\":\"2026-05-11 20:44:50\",\"creator_username\":\"jca\\u00f1ares\",\"creator_firstname\":\"Jason\",\"creator_lastname\":\"Ca\\u00f1ares\"}', 'Small Value Procurement record created.', NULL, NULL, '2026-05-11 12:44:50'),
(8, 4, 6, 'create_svp_document', 'rfq', 1, NULL, '{\"id\":1,\"parent_procurement_id\":4,\"title\":\"RFQ and Tender Form\",\"description\":\"RFQ and Tender Form\",\"file_path\":\"storage/uploads/notices/notice_6a01cffecb8c78.32151512.pdf\",\"file_hash\":\"c2b2ffe5462984388a268e23d72de326f43fce925a51e873cd6d259a9ee4bb41\",\"document_type\":\"rfq\",\"sequence_stage\":1,\"posted_at\":\"2026-05-12 20:46:00\",\"created_by\":6,\"updated_by\":6,\"created_at\":\"2026-05-11 20:47:58\",\"updated_at\":\"2026-05-11 20:47:58\",\"creator_username\":\"jca\\u00f1ares\",\"creator_firstname\":\"Jason\",\"creator_lastname\":\"Ca\\u00f1ares\"}', 'SVP document posted.', 'c2b2ffe5462984388a268e23d72de326f43fce925a51e873cd6d259a9ee4bb41', NULL, '2026-05-11 12:47:58'),
(9, 5, 6, 'create_parent', 'svp_procurement', NULL, NULL, '{\"id\":5,\"procurement_mode\":\"svp\",\"reference_number\":\"202605002\",\"procurement_title\":\"PREVENTIVE MAINTENANCE OF FIRE ALARM AND DETECTION SYSTEM (FDAS) OF NFA CENTRAL OFFICE BUILDING\",\"abc\":\"337000.00\",\"mode_of_procurement\":\"svp\",\"posting_date\":null,\"bid_submission_deadline\":null,\"description\":\"PREVENTIVE MAINTENANCE OF FIRE ALARM AND DETECTION SYSTEM (FDAS) OF NFA CENTRAL OFFICE BUILDING\\r\\n\\r\\nThe description of the item, including other requirements, is defined/indicated in the attached Technical Specifications/Terms of Reference/Terms and Conditions. Please submit the duly accomplished Tender Form and other required documents in a sealed envelope from May 12, 2026 until May 18, 2026, 10:00 AM, to:\\r\\n\\r\\nNational Food Authority\\r\\n6F General Services Division\\r\\nAdministrative and General Services Department\\r\\nVisayas Avenue, Barangay Vasra, Quezon City\\r\\n\\r\\nThe following eligibility documents are also required and shall be submitted:\\r\\n1. 2026 Mayor\\u2019s Permit/Business Permit;\\r\\n2. 2025 ITR or its equivalent document (2026 Quarterly ITR/Monthly Business tax Returns);\\r\\n3. Conformed Bid Form (Technical Specifications) / Terms of Reference;\\r\\n4. SEC Registration or DTI\\r\\n5. PhilGeps Certification or Printout of PhilGeps Organizational Number; and\\r\\n6. Original Notarized Omnibus Sworn Statement with Authority of Signatory (Secretary\\u2019s Certificate, SPA, Partnership Resolutions).\",\"posting_status\":\"scheduled\",\"current_stage\":\"rfq\",\"archived_at\":null,\"archive_reason\":null,\"archived_by\":null,\"archive_approval_reference\":null,\"archive_approved_by\":null,\"archive_approved_at\":null,\"category\":\"goods\",\"end_user_unit\":\"AGSD-GSD\",\"region\":\"Central Office\",\"branch\":\"Administrative and General Services Department\",\"created_by\":6,\"updated_by\":6,\"created_at\":\"2026-05-11 20:52:37\",\"updated_at\":\"2026-05-11 20:52:37\",\"creator_username\":\"jca\\u00f1ares\",\"creator_firstname\":\"Jason\",\"creator_lastname\":\"Ca\\u00f1ares\"}', 'Small Value Procurement record created.', NULL, NULL, '2026-05-11 12:52:37'),
(10, 5, 6, 'create_svp_document', 'rfq', 2, NULL, '{\"id\":2,\"parent_procurement_id\":5,\"title\":\"RFQ nad Tender Form\",\"description\":\"RFQ and Tender Form\",\"file_path\":\"storage/uploads/notices/notice_6a01d1763375d4.52785046.pdf\",\"file_hash\":\"d58df5bdfba7b66ca581e7ffcd13c5443466cf4b17e34ac6b923ccd3d0031bfb\",\"document_type\":\"rfq\",\"sequence_stage\":1,\"posted_at\":\"2026-05-12 20:52:00\",\"created_by\":6,\"updated_by\":6,\"created_at\":\"2026-05-11 20:54:14\",\"updated_at\":\"2026-05-11 20:54:14\",\"creator_username\":\"jca\\u00f1ares\",\"creator_firstname\":\"Jason\",\"creator_lastname\":\"Ca\\u00f1ares\"}', 'SVP document posted.', 'd58df5bdfba7b66ca581e7ffcd13c5443466cf4b17e34ac6b923ccd3d0031bfb', NULL, '2026-05-11 12:54:14'),
(11, 6, 6, 'create_parent', 'svp_procurement', NULL, NULL, '{\"id\":6,\"procurement_mode\":\"svp\",\"reference_number\":\"202605003\",\"procurement_title\":\"1 LOT PREVENTIVE MAINTENANCE OF GENERATOR SETS FOR CY 2026\",\"abc\":\"525000.00\",\"mode_of_procurement\":\"svp\",\"posting_date\":\"2026-05-19 00:00:00\",\"bid_submission_deadline\":\"2026-05-22 11:00:00\",\"description\":\"1 LOT PREVENTIVE MAINTENANCE OF GENERATOR SETS FOR CY 2026\\r\\n\\r\\nThe description of the item, including other requirements, is defined/indicated in the attached Technical Specifications/Terms of Reference/Terms and Conditions. Please submit the duly accomplished Tender Form and other required documents in a sealed envelope from May 19, 2026 until May 22, 2025, 10:00 AM, to:\\r\\n\\r\\n National Food Authority\\r\\n6F General Services Division\\r\\nAdministrative and General Services Department\\r\\nVisayas Avenue, Barangay Vasra, Quezon City\\r\\n \\r\\n\\r\\nThe following eligibility documents are also required and shall be submitted:\\r\\n\\r\\n2026 Mayor\\u2019s Permit/Business Permit ;\\r\\n2025 ITR or its equivalent document (2026 Quarterly ITR/Monthly Business tax Returns);\\r\\nConformed Bid Form (Technical Specifications) / Terms of Reference;\\r\\nSEC Registration or DTI\\r\\nPhilGeps Certification or Printout of PhilGeps Organizational Number; and\\r\\nOriginal Notarized Omnibus Sworn Statement with Authority of Signatory (Secretary\\u2019s Certificate, SPA, Partnership Resolution).\",\"posting_status\":\"scheduled\",\"current_stage\":\"rfq\",\"archived_at\":null,\"archive_reason\":null,\"archived_by\":null,\"archive_approval_reference\":null,\"archive_approved_by\":null,\"archive_approved_at\":null,\"category\":\"goods\",\"end_user_unit\":\"AGSD-GSD\",\"region\":\"Central Office\",\"branch\":\"Administrative and General Services Department\",\"created_by\":6,\"updated_by\":6,\"created_at\":\"2026-05-18 16:01:01\",\"updated_at\":\"2026-05-18 16:01:01\",\"creator_username\":\"jca\\u00f1ares\",\"creator_firstname\":\"Jason\",\"creator_lastname\":\"Ca\\u00f1ares\"}', 'Small Value Procurement record created.', NULL, NULL, '2026-05-18 08:01:01'),
(12, 2, 4, 'create_document', 'resolution', 1, NULL, '{\"id\":1,\"parent_procurement_id\":2,\"title\":\"BAC Resolution\",\"description\":\"BAC Resolution Recommending Award\",\"file_path\":\"storage/uploads/notices/notice_6a201919b21f91.56965576.pdf\",\"file_hash\":\"dc3253a11405e186a6cb40d12003b25b3f1b13d34789bb92a1cf5a88278127ac\",\"document_type\":\"resolution\",\"sequence_stage\":3,\"posted_at\":\"2026-06-03 21:00:00\",\"created_by\":4,\"updated_by\":4,\"created_at\":\"2026-06-03 20:07:53\",\"updated_at\":\"2026-06-03 20:07:53\",\"creator_username\":\"aacarillo\",\"creator_firstname\":\"Angelo\",\"creator_lastname\":\"Carillo\"}', 'Competitive Bidding document posted.', 'dc3253a11405e186a6cb40d12003b25b3f1b13d34789bb92a1cf5a88278127ac', NULL, '2026-06-03 12:07:53'),
(13, 2, 4, 'create_document', 'award', 1, NULL, '{\"id\":1,\"parent_procurement_id\":2,\"title\":\"Notice of Award\",\"description\":\"Notice of Award\",\"file_path\":\"storage/uploads/notices/notice_6a20193e6d1634.98355173.pdf\",\"file_hash\":\"b3d451f305536ff2b41a58ce8ee1e1298243fe271c10aad792a478a7480dacc6\",\"document_type\":\"award\",\"sequence_stage\":4,\"posted_at\":\"2026-06-03 21:00:00\",\"created_by\":4,\"updated_by\":4,\"created_at\":\"2026-06-03 20:08:30\",\"updated_at\":\"2026-06-03 20:08:30\",\"creator_username\":\"aacarillo\",\"creator_firstname\":\"Angelo\",\"creator_lastname\":\"Carillo\"}', 'Competitive Bidding document posted.', 'b3d451f305536ff2b41a58ce8ee1e1298243fe271c10aad792a478a7480dacc6', NULL, '2026-06-03 12:08:30'),
(14, 7, 6, 'create_parent', 'svp_procurement', NULL, NULL, '{\"id\":7,\"procurement_mode\":\"svp\",\"reference_number\":\"202606001\",\"procurement_title\":\"1 LOT PREVENTIVE MAINTENANCE OF ELECTRICAL FACILITIES FOR CY 2026\",\"abc\":\"1885000.00\",\"mode_of_procurement\":\"svp\",\"posting_date\":\"2026-06-05 00:00:00\",\"bid_submission_deadline\":\"2026-06-11 11:00:00\",\"description\":\"The description of the item, including other requirements, is defined/indicated in the attached Technical Specifications/Terms of Reference/Terms and Conditions. Please submit the duly accomplished Tender Form and other required documents in a sealed envelope from June 05, 2026 until June 11, 2026, 10:00 AM, to:\\r\\n\\r\\nNational Food Authority\\r\\n6F General Services Division\\r\\nAdministrative and General Services Department\\r\\nVisayas Avenue, Barangay Vasra, Quezon City\\r\\n\\r\\nThe following eligibility documents are also required and shall be submitted:\\r\\n\\r\\n1.\\t2026 Mayor\\u2019s Permit/Business Permit;\\r\\n2.\\t2025 ITR or its equivalent document (2026 Quarterly ITR/Monthly Business tax Returns);\\r\\n3.\\tConformed Bid Form (Technical Specifications) / Terms of Reference;\\r\\n4.\\tSEC Registration or DTI\\r\\n5.\\tPhilGeps Certification or Printout of PhilGeps Organizational Number; and\\r\\n6.\\tOriginal Notarized Omnibus Sworn Statement with Authority of Signatory (Secretary\\u2019s Certificate, SPA, Partnership Resolutions).\",\"posting_status\":\"scheduled\",\"current_stage\":\"rfq\",\"archived_at\":null,\"archive_reason\":null,\"archived_by\":null,\"archive_approval_reference\":null,\"archive_approved_by\":null,\"archive_approved_at\":null,\"category\":\"goods\",\"end_user_unit\":\"AGSD-GSD\",\"region\":\"Central Office\",\"branch\":\"Administrative and General Services Department\",\"created_by\":6,\"updated_by\":6,\"created_at\":\"2026-06-04 14:35:31\",\"updated_at\":\"2026-06-04 14:35:31\",\"creator_username\":\"jca\\u00f1ares\",\"creator_firstname\":\"Jason\",\"creator_lastname\":\"Ca\\u00f1ares\"}', 'Small Value Procurement record created.', NULL, NULL, '2026-06-04 06:35:31'),
(15, 7, 6, 'create_svp_document', 'rfq', 3, NULL, '{\"id\":3,\"parent_procurement_id\":7,\"title\":\"RFQ and TENDER FORM\",\"description\":\"The description of the item, including other requirements, is defined/indicated in the attached Technical Specifications/Terms of Reference/Terms and Conditions. Please submit the duly accomplished Tender Form and other required documents in a sealed envelope from June 05, 2026 until June 11, 2026, 10:00 AM, to:\\r\\n\\r\\nNational Food Authority\\r\\n6F General Services Division\\r\\nAdministrative and General Services Department\\r\\nVisayas Avenue, Barangay Vasra, Quezon City\\r\\n\\r\\nThe following eligibility documents are also required and shall be submitted:\\r\\n\\r\\n1.\\t2026 Mayor\\u2019s Permit/Business Permit;\\r\\n2.\\t2025 ITR or its equivalent document (2026 Quarterly ITR/Monthly Business tax Returns);\\r\\n3.\\tConformed Bid Form (Technical Specifications) / Terms of Reference;\\r\\n4.\\tSEC Registration or DTI\\r\\n5.\\tPhilGeps Certification or Printout of PhilGeps Organizational Number; and\\r\\n6.\\tOriginal Notarized Omnibus Sworn Statement with Authority of Signatory (Secretary\\u2019s Certificate, SPA, Partnership Resolutions).\",\"file_path\":\"storage/uploads/notices/notice_6a211d15b3f565.20876220.pdf\",\"file_hash\":\"f3472b912ed92f97966be1f4aa522bba06762381934ba033943bfd9895aa7dcc\",\"document_type\":\"rfq\",\"sequence_stage\":1,\"posted_at\":\"2026-06-05 00:00:00\",\"created_by\":6,\"updated_by\":6,\"created_at\":\"2026-06-04 14:37:09\",\"updated_at\":\"2026-06-04 14:37:09\",\"creator_username\":\"jca\\u00f1ares\",\"creator_firstname\":\"Jason\",\"creator_lastname\":\"Ca\\u00f1ares\"}', 'SVP document posted.', 'f3472b912ed92f97966be1f4aa522bba06762381934ba033943bfd9895aa7dcc', NULL, '2026-06-04 06:37:09'),
(16, 8, 6, 'create_parent', 'svp_procurement', NULL, NULL, '{\"id\":8,\"procurement_mode\":\"svp\",\"reference_number\":\"202606002\",\"procurement_title\":\"SUPPLY AND DELIVERY OF GRAINS MAGAZINE 2026\",\"abc\":\"366298.00\",\"mode_of_procurement\":\"svp\",\"posting_date\":\"2026-06-16 00:00:00\",\"bid_submission_deadline\":\"2026-06-22 14:00:00\",\"description\":\"SUPPLY AND DELIVERY OF GRAINS MAGAZINE 2026\\r\\nThe description of the item, including other requirements, is defined/indicated in the attached Technical Specifications/Terms of Reference/Terms and Conditions. Please submit the duly accomplished Tender Form and other required documents in a sealed envelope from June 16, 2026 until June 22, 2026, 12:00 PM, to:\\r\\n\\r\\n \\r\\nNational Food Authority\\r\\n6F General Services Division\\r\\nAdministrative and General Services Department\\r\\nVisayas Avenue, Barangay Vasra, Quezon City\\r\\n\\r\\n\\r\\nThe following eligibility documents are also required and shall be submitted:\\r\\n1. 2026 Mayor\\u2019s Permit/Business Permit;\\r\\n2. 2025 ITR or its equivalent document (2026 Quarterly ITR/Monthly Business tax Returns);\\r\\n3. Conformed Bid Form (Technical Specifications) / Terms of Reference;\\r\\n4. SEC Registration or DTI\\r\\n5. PhilGeps Certification or Printout of PhilGeps Organizational Number; and\\r\\n6. Original Notarized Omnibus Sworn Statement with Authority of Signatory (Secretary\\u2019s Certificate, SPA, Partnership Resolutions).\",\"posting_status\":\"scheduled\",\"current_stage\":\"rfq\",\"archived_at\":null,\"archive_reason\":null,\"archived_by\":null,\"archive_approval_reference\":null,\"archive_approved_by\":null,\"archive_approved_at\":null,\"category\":\"goods\",\"end_user_unit\":\"AO-PAD\",\"region\":\"Central Office\",\"branch\":\"Administrative and General Services Department\",\"created_by\":6,\"updated_by\":6,\"created_at\":\"2026-06-16 09:03:02\",\"updated_at\":\"2026-06-16 09:03:02\",\"creator_username\":\"jca\\u00f1ares\",\"creator_firstname\":\"Jason\",\"creator_lastname\":\"Ca\\u00f1ares\"}', 'Small Value Procurement record created.', NULL, NULL, '2026-06-16 01:03:02'),
(17, 8, 6, 'create_svp_document', 'rfq', 4, NULL, '{\"id\":4,\"parent_procurement_id\":8,\"title\":\"RFQ and Tender form\",\"description\":\"Suppliers/bidders must download the attached supporting documents for reference\",\"file_path\":\"storage/uploads/notices/notice_6a30a15e2e5173.81750397.pdf\",\"file_hash\":\"ac6c99e80e29b849782af38af6b4eaf2a14c4b75ae80d209e2d640726e3ec90c\",\"document_type\":\"rfq\",\"sequence_stage\":1,\"posted_at\":\"2026-06-16 00:00:00\",\"created_by\":6,\"updated_by\":6,\"created_at\":\"2026-06-16 09:05:34\",\"updated_at\":\"2026-06-16 09:05:34\",\"creator_username\":\"jca\\u00f1ares\",\"creator_firstname\":\"Jason\",\"creator_lastname\":\"Ca\\u00f1ares\"}', 'SVP document posted.', 'ac6c99e80e29b849782af38af6b4eaf2a14c4b75ae80d209e2d640726e3ec90c', NULL, '2026-06-16 01:05:34'),
(18, 6, 6, 'create_svp_document', 'rfq', 5, NULL, '{\"id\":5,\"parent_procurement_id\":6,\"title\":\"RFQ and Tender Form\",\"description\":\"1 LOT PREVENTIVE MAINTENANCE OF GENERATOR SETS FOR CY 2026\",\"file_path\":\"storage/uploads/notices/notice_6a3ccf6caf6b88.31966823.pdf\",\"file_hash\":\"12bd56cbeff1b1768fcc60827d7d0ef744b1e39cab6fd49fad30274ec7ef8175\",\"document_type\":\"rfq\",\"sequence_stage\":1,\"posted_at\":\"2026-05-19 00:00:00\",\"created_by\":6,\"updated_by\":6,\"created_at\":\"2026-06-25 14:49:16\",\"updated_at\":\"2026-06-25 14:49:16\",\"creator_username\":\"jca\\u00f1ares\",\"creator_firstname\":\"Jason\",\"creator_lastname\":\"Ca\\u00f1ares\"}', 'SVP document posted.', '12bd56cbeff1b1768fcc60827d7d0ef744b1e39cab6fd49fad30274ec7ef8175', NULL, '2026-06-25 06:49:16'),
(19, 9, 4, 'create_parent', 'bid_notice', 4, NULL, '{\"id\":9,\"procurement_mode\":\"competitive_bidding\",\"reference_number\":\"2026-03a\",\"procurement_title\":\"Supply and Delivery of Various Laptops\",\"abc\":\"107920000.00\",\"mode_of_procurement\":\"competitive_bidding\",\"posting_date\":\"2026-07-16 00:00:00\",\"bid_submission_deadline\":\"2026-08-05 13:30:00\",\"description\":\"Invitation to Bid for \\r\\nSupply and Delivery of Various Laptops, Desktop Computers, and Tablets\\r\\n\\r\\nThe National Food Authority (NFA) Central Office, through the approved 2026 Corporate Operating Budget intends to apply the sum of:\\r\\n\\r\\n \\r\\nLot No.\\r\\nProject ID No.\\r\\nProject Name\\r\\nApproved Budget for the Contract \\r\\n(in PhP inclusive of all applicable taxes)\\r\\n1\\r\\n2026-03a\\r\\nSupply and Delivery of Various Laptops\\r\\n107,920,000.00\\r\\n2\\r\\n2026-03b\\r\\nSupply and Delivery of Desktop Computers\\r\\n103,360,000.00\\r\\n3\\r\\n2026-03c\\r\\nSupply and Delivery of Tablets\\r\\n8,240,000.00 \\r\\nTOTAL\\r\\n219,520,000.00\\r\\n\\r\\n\\r\\n\\r\\nbeing the Approved Budget for the Contract (ABC) to payments under the contract for the Supply and Delivery of Various Laptops, Desktop Computers, and Tablets. Bids received in excess of the ABC shall be automatically rejected at bid opening. \\r\\n\\r\\nThe summary of bidding activities is as follows:\\r\\n\\r\\nAdvertisement/Posting of Invitation to Bid\\r\\nJuly 16, 2026\\r\\nIssuance and Availability of Bid Documents\\r\\nJuly 16, 2026\\r\\nPre-Bid Conference\\r\\nJuly 24, 2026\\r\\nDeadline of Submission of Request for Clarification\\r\\nJuly 26, 2026\\r\\nLast Day of Issuance of Bid Bulletin\\r\\nJuly 29, 2026\\r\\nDeadline for Submission of Bids\\r\\nAugust 5, 2026\\r\\n\\r\\n\\r\\nThe NFA Central Office, through the Bids and Awards Committee, now invites bids for the Three (3) Lots Supply and Delivery of Various Laptops, Desktop Computers, and Tablets. Delivery of the Goods is required within sixty (60) calendar days from receipt of Notice to Proceed. Bidders should have completed, at least three (3) years prior to the date of submission and receipt of bids, contracts similar to the Project. The description of an eligible bidder is contained in the Bidding Documents, particularly, in Section II. Instructions to Bidders.\\r\\n\\r\\nBidding will be conducted through competitive bidding procedures using a non-discretionary \\u201cpass/fail\\u201d criterion as specified in the IRR of RA No. 12009. Bidding is restricted to Filipino citizens/sole proprietorships, partnerships, or organizations with at least sixty percent (60%) interest or outstanding capital stock belonging to citizens of the Philippines, and to citizens or organizations of a country the laws or regulations of which grant similar rights or privileges to Filipino citizens, pursuant to RA No. 5183. \\r\\n\\r\\nInterested Bidders may obtain further information from the NFA Central Office BAC Secretariat and inspect the Bidding Documents at the address given below from 8:00 AM to 5:00 PM, Mondays to Fridays, except for holidays or inspect the Bidding Documents at the address and e-mail given below. \\r\\n\\r\\nA complete set of Bidding Documents may be acquired by interested Bidders on 16 July 2026 to 5 August 2026 from the address, e-mail, and website given below, upon payment of the applicable fee for the Bidding Documents, pursuant to the latest Guidelines issued by the GPPB, in the following amount:\\r\\n\\r\\n\\r\\nLot No.\\r\\nProject ID No.\\r\\nProject Name\\r\\nApproved Budget for the Contract \\r\\n(in PhP inclusive of all applicable taxes)\\r\\nCost of Bidding Documents\\r\\n(in PhP) \\r\\n1\\r\\n2026-03a\\r\\nSupply and Delivery of Various Laptops\\r\\n107,920,000.00\\r\\n50,000.00\\r\\n2\\r\\n2026-03b\\r\\nSupply and Delivery of Desktop Computers\\r\\n103,360,000.00\\r\\n50,000.00\\r\\n3\\r\\n2026-03c\\r\\nSupply and Delivery of Various Tablets\\r\\n8,240,000.00 \\r\\n10,000.00\\r\\n\\r\\n\\r\\nIt may also be downloaded free of charge from the website of the Philippine Government Electronic Procurement System (PhilGEPS) and the website of the Procuring Entity, provided that Bidders shall pay the applicable fee for the Bidding Documents not later than the submission of their bids.\\r\\n\\r\\nThe NFA Central Office will hold a Pre-Bid Conference on 24 July 2026, 1:30 p.m. onwards at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or through video conferencing or webcasting via the Google Meet platform, which shall be open to prospective Bidders. Meeting details shall be made available to interested bidders upon request sent to the official e-mail of NFA Central Office at bac@nfa.gov.ph. The deadline for clarifications shall be on 26 July, 2026 and the last day for issuance of relevant bid bulletins shall be on 29 July, 2026.\\r\\n \\r\\nBids must be duly received by the Bids and Awards Committee (BAC) Secretariat through manual submission at the office address indicated below, on or before 5 August 2026, 1:30 p.m. Late bids shall not be accepted.\\r\\n\\r\\nAll Bids must be accompanied by a Bid Security in any of the acceptable forms and in the amount stated in ITB Clause 16.1.\\r\\n\\r\\nBid opening shall be on 5 August 2026, 2:00 p.m. at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or via video conferencing or webcasting via the Google Meet platform.  Bids will be opened in the presence of the Bidders\\u2019 representatives who choose to attend the activity.\\r\\n\\r\\nIn accordance with Government Procurement Policy Board (GPPB) Circular 06-2005-Tie-Breaking Method, the Bids and Awards Committee (BAC) shall use a non-discretionary and non-discriminatory measure based on sheer luck or chance, which is \\u201cDRAW LOTS\\u201d, in the event that the two (2) or more bidders have been post-qualified and determined as the bidder having the Lowest Calculated and Responsive Bid (LCRB), based on the following procedures: \\r\\n\\r\\nIn alphabetical order, the bidder shall pick one roll of paper:\\r\\n\\r\\nThe lucky bidders who pick the paper with a \\u201cCONGRATULATIONS\\u201d remark shall be declared as the final bidder having the LCRB and recommended for the award of the contract.\\r\\n\\r\\nThe National Food Authority reserves the right to reject any and all bids, declare a failure of bidding, or not award the contract at any time prior to contract award in accordance with Section 70 of R.A. No. 12009, without incurring any liability to the affected Bidder or Bidders.\\r\\n\\r\\nFor further information, please refer to:\\r\\n\\r\\nName of Contact Person:\\tENGR. LESLIE M. NAVARRO\\r\\n\\t\\t\\t\\t\\t\\tHead, BAC Secretariat \\r\\nPostal Address\\t\\t:\\tNational Food Authority Central Office\\r\\n\\t\\t\\t\\t\\t\\tVisayas Avenue, Brgy. Vasra, Diliman,\\r\\n\\t\\t\\t\\t\\t\\tQuezon City, 1128\\r\\nE-mail Address\\t\\t:\\tbac@nfa.gov.ph\\r\\n\\r\\nYou may visit the following websites for downloading of Bidding Documents:\\r\\n\\r\\nNFA Central Office website (www.nfa.gov.ph)\\r\\nPhilGEPS website (www.philgeps.gov.ph)\\r\\n\\r\\n\\r\\nApproved by:\\r\\n\\r\\n\\r\\n              \\r\\nVERALEW DG. DE VERA\\r\\nChairperson, Bids and Awards Committee\\r\\nNational Food Authority\\r\\n\\r\\n\\r\\n\\r\\n16 July 2026\",\"posting_status\":\"scheduled\",\"current_stage\":\"bid_notice\",\"archived_at\":null,\"archive_reason\":null,\"archived_by\":null,\"archive_approval_reference\":null,\"archive_approved_by\":null,\"archive_approved_at\":null,\"category\":\"goods\",\"end_user_unit\":\"CPMSD\",\"region\":\"Central Office\",\"branch\":\"Administrative and General Services Department\",\"created_by\":4,\"updated_by\":4,\"created_at\":\"2026-07-15 22:08:02\",\"updated_at\":\"2026-07-15 22:08:02\",\"creator_username\":\"aacarillo\",\"creator_firstname\":\"Angelo\",\"creator_lastname\":\"Carillo\"}', 'Competitive Bidding procurement record created.', '6c70af08d73df6410d3d6532a7552a7dc65eeb837e30643f7a4333824da9baa5', NULL, '2026-07-15 14:08:02'),
(20, 10, 4, 'create_parent', 'bid_notice', 5, NULL, '{\"id\":10,\"procurement_mode\":\"competitive_bidding\",\"reference_number\":\"2026-03b\",\"procurement_title\":\"Supply and Delivery of Desktop Computers\",\"abc\":\"103360000.00\",\"mode_of_procurement\":\"competitive_bidding\",\"posting_date\":\"2026-07-16 00:00:00\",\"bid_submission_deadline\":\"2026-08-05 13:30:00\",\"description\":\"Invitation to Bid for \\r\\nSupply and Delivery of Various Laptops, Desktop Computers, and Tablets\\r\\n\\r\\nThe National Food Authority (NFA) Central Office, through the approved 2026 Corporate Operating Budget intends to apply the sum of:\\r\\n\\r\\n \\r\\nLot No.\\r\\nProject ID No.\\r\\nProject Name\\r\\nApproved Budget for the Contract \\r\\n(in PhP inclusive of all applicable taxes)\\r\\n1\\r\\n2026-03a\\r\\nSupply and Delivery of Various Laptops\\r\\n107,920,000.00\\r\\n2\\r\\n2026-03b\\r\\nSupply and Delivery of Desktop Computers\\r\\n103,360,000.00\\r\\n3\\r\\n2026-03c\\r\\nSupply and Delivery of Tablets\\r\\n8,240,000.00 \\r\\nTOTAL\\r\\n219,520,000.00\\r\\n\\r\\n\\r\\n\\r\\nbeing the Approved Budget for the Contract (ABC) to payments under the contract for the Supply and Delivery of Various Laptops, Desktop Computers, and Tablets. Bids received in excess of the ABC shall be automatically rejected at bid opening. \\r\\n\\r\\nThe summary of bidding activities is as follows:\\r\\n\\r\\nAdvertisement/Posting of Invitation to Bid\\r\\nJuly 16, 2026\\r\\nIssuance and Availability of Bid Documents\\r\\nJuly 16, 2026\\r\\nPre-Bid Conference\\r\\nJuly 24, 2026\\r\\nDeadline of Submission of Request for Clarification\\r\\nJuly 26, 2026\\r\\nLast Day of Issuance of Bid Bulletin\\r\\nJuly 29, 2026\\r\\nDeadline for Submission of Bids\\r\\nAugust 5, 2026\\r\\n\\r\\n\\r\\nThe NFA Central Office, through the Bids and Awards Committee, now invites bids for the Three (3) Lots Supply and Delivery of Various Laptops, Desktop Computers, and Tablets. Delivery of the Goods is required within sixty (60) calendar days from receipt of Notice to Proceed. Bidders should have completed, at least three (3) years prior to the date of submission and receipt of bids, contracts similar to the Project. The description of an eligible bidder is contained in the Bidding Documents, particularly, in Section II. Instructions to Bidders.\\r\\n\\r\\nBidding will be conducted through competitive bidding procedures using a non-discretionary \\u201cpass/fail\\u201d criterion as specified in the IRR of RA No. 12009. Bidding is restricted to Filipino citizens/sole proprietorships, partnerships, or organizations with at least sixty percent (60%) interest or outstanding capital stock belonging to citizens of the Philippines, and to citizens or organizations of a country the laws or regulations of which grant similar rights or privileges to Filipino citizens, pursuant to RA No. 5183. \\r\\n\\r\\nInterested Bidders may obtain further information from the NFA Central Office BAC Secretariat and inspect the Bidding Documents at the address given below from 8:00 AM to 5:00 PM, Mondays to Fridays, except for holidays or inspect the Bidding Documents at the address and e-mail given below. \\r\\n\\r\\nA complete set of Bidding Documents may be acquired by interested Bidders on 16 July 2026 to 5 August 2026 from the address, e-mail, and website given below, upon payment of the applicable fee for the Bidding Documents, pursuant to the latest Guidelines issued by the GPPB, in the following amount:\\r\\n\\r\\n\\r\\nLot No.\\r\\nProject ID No.\\r\\nProject Name\\r\\nApproved Budget for the Contract \\r\\n(in PhP inclusive of all applicable taxes)\\r\\nCost of Bidding Documents\\r\\n(in PhP) \\r\\n1\\r\\n2026-03a\\r\\nSupply and Delivery of Various Laptops\\r\\n107,920,000.00\\r\\n50,000.00\\r\\n2\\r\\n2026-03b\\r\\nSupply and Delivery of Desktop Computers\\r\\n103,360,000.00\\r\\n50,000.00\\r\\n3\\r\\n2026-03c\\r\\nSupply and Delivery of Various Tablets\\r\\n8,240,000.00 \\r\\n10,000.00\\r\\n\\r\\n\\r\\nIt may also be downloaded free of charge from the website of the Philippine Government Electronic Procurement System (PhilGEPS) and the website of the Procuring Entity, provided that Bidders shall pay the applicable fee for the Bidding Documents not later than the submission of their bids.\\r\\n\\r\\nThe NFA Central Office will hold a Pre-Bid Conference on 24 July 2026, 1:30 p.m. onwards at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or through video conferencing or webcasting via the Google Meet platform, which shall be open to prospective Bidders. Meeting details shall be made available to interested bidders upon request sent to the official e-mail of NFA Central Office at bac@nfa.gov.ph. The deadline for clarifications shall be on 26 July, 2026 and the last day for issuance of relevant bid bulletins shall be on 29 July, 2026.\\r\\n \\r\\nBids must be duly received by the Bids and Awards Committee (BAC) Secretariat through manual submission at the office address indicated below, on or before 5 August 2026, 1:30 p.m. Late bids shall not be accepted.\\r\\n\\r\\nAll Bids must be accompanied by a Bid Security in any of the acceptable forms and in the amount stated in ITB Clause 16.1.\\r\\n\\r\\nBid opening shall be on 5 August 2026, 2:00 p.m. at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or via video conferencing or webcasting via the Google Meet platform.  Bids will be opened in the presence of the Bidders\\u2019 representatives who choose to attend the activity.\\r\\n\\r\\nIn accordance with Government Procurement Policy Board (GPPB) Circular 06-2005-Tie-Breaking Method, the Bids and Awards Committee (BAC) shall use a non-discretionary and non-discriminatory measure based on sheer luck or chance, which is \\u201cDRAW LOTS\\u201d, in the event that the two (2) or more bidders have been post-qualified and determined as the bidder having the Lowest Calculated and Responsive Bid (LCRB), based on the following procedures: \\r\\n\\r\\nIn alphabetical order, the bidder shall pick one roll of paper:\\r\\n\\r\\nThe lucky bidders who pick the paper with a \\u201cCONGRATULATIONS\\u201d remark shall be declared as the final bidder having the LCRB and recommended for the award of the contract.\\r\\n\\r\\nThe National Food Authority reserves the right to reject any and all bids, declare a failure of bidding, or not award the contract at any time prior to contract award in accordance with Section 70 of R.A. No. 12009, without incurring any liability to the affected Bidder or Bidders.\\r\\n\\r\\nFor further information, please refer to:\\r\\n\\r\\nName of Contact Person:\\tENGR. LESLIE M. NAVARRO\\r\\n\\t\\t\\t\\t\\t\\tHead, BAC Secretariat \\r\\nPostal Address\\t\\t:\\tNational Food Authority Central Office\\r\\n\\t\\t\\t\\t\\t\\tVisayas Avenue, Brgy. Vasra, Diliman,\\r\\n\\t\\t\\t\\t\\t\\tQuezon City, 1128\\r\\nE-mail Address\\t\\t:\\tbac@nfa.gov.ph\\r\\n\\r\\nYou may visit the following websites for downloading of Bidding Documents:\\r\\n\\r\\nNFA Central Office website (www.nfa.gov.ph)\\r\\nPhilGEPS website (www.philgeps.gov.ph)\\r\\n\\r\\n\\r\\nApproved by:\\r\\n\\r\\n\\r\\n              \\r\\nVERALEW DG. DE VERA\\r\\nChairperson, Bids and Awards Committee\\r\\nNational Food Authority\\r\\n\\r\\n\\r\\n\\r\\n16 July 2026\",\"posting_status\":\"scheduled\",\"current_stage\":\"bid_notice\",\"archived_at\":null,\"archive_reason\":null,\"archived_by\":null,\"archive_approval_reference\":null,\"archive_approved_by\":null,\"archive_approved_at\":null,\"category\":\"goods\",\"end_user_unit\":\"CPMSD\",\"region\":\"Central Office\",\"branch\":\"Administrative and General Services Department\",\"created_by\":4,\"updated_by\":4,\"created_at\":\"2026-07-15 22:10:10\",\"updated_at\":\"2026-07-15 22:10:10\",\"creator_username\":\"aacarillo\",\"creator_firstname\":\"Angelo\",\"creator_lastname\":\"Carillo\"}', 'Competitive Bidding procurement record created.', '6c70af08d73df6410d3d6532a7552a7dc65eeb837e30643f7a4333824da9baa5', NULL, '2026-07-15 14:10:10');
INSERT INTO `procurement_activity_logs` (`id`, `parent_procurement_id`, `user_id`, `action_type`, `document_type`, `document_id`, `before_snapshot`, `after_snapshot`, `reason`, `file_hash`, `approval_reference`, `created_at`) VALUES
(21, 11, 4, 'create_parent', 'bid_notice', 6, NULL, '{\"id\":11,\"procurement_mode\":\"competitive_bidding\",\"reference_number\":\"2026-03c\",\"procurement_title\":\"Supply and Delivery of Various Tablets\",\"abc\":\"8240000.00\",\"mode_of_procurement\":\"competitive_bidding\",\"posting_date\":\"2026-07-16 00:00:00\",\"bid_submission_deadline\":\"2026-08-05 13:30:00\",\"description\":\"Invitation to Bid for \\r\\nSupply and Delivery of Various Laptops, Desktop Computers, and Tablets\\r\\n\\r\\nThe National Food Authority (NFA) Central Office, through the approved 2026 Corporate Operating Budget intends to apply the sum of:\\r\\n\\r\\n \\r\\nLot No.\\r\\nProject ID No.\\r\\nProject Name\\r\\nApproved Budget for the Contract \\r\\n(in PhP inclusive of all applicable taxes)\\r\\n1\\r\\n2026-03a\\r\\nSupply and Delivery of Various Laptops\\r\\n107,920,000.00\\r\\n2\\r\\n2026-03b\\r\\nSupply and Delivery of Desktop Computers\\r\\n103,360,000.00\\r\\n3\\r\\n2026-03c\\r\\nSupply and Delivery of Tablets\\r\\n8,240,000.00 \\r\\nTOTAL\\r\\n219,520,000.00\\r\\n\\r\\n\\r\\n\\r\\nbeing the Approved Budget for the Contract (ABC) to payments under the contract for the Supply and Delivery of Various Laptops, Desktop Computers, and Tablets. Bids received in excess of the ABC shall be automatically rejected at bid opening. \\r\\n\\r\\nThe summary of bidding activities is as follows:\\r\\n\\r\\nAdvertisement/Posting of Invitation to Bid\\r\\nJuly 16, 2026\\r\\nIssuance and Availability of Bid Documents\\r\\nJuly 16, 2026\\r\\nPre-Bid Conference\\r\\nJuly 24, 2026\\r\\nDeadline of Submission of Request for Clarification\\r\\nJuly 26, 2026\\r\\nLast Day of Issuance of Bid Bulletin\\r\\nJuly 29, 2026\\r\\nDeadline for Submission of Bids\\r\\nAugust 5, 2026\\r\\n\\r\\n\\r\\nThe NFA Central Office, through the Bids and Awards Committee, now invites bids for the Three (3) Lots Supply and Delivery of Various Laptops, Desktop Computers, and Tablets. Delivery of the Goods is required within sixty (60) calendar days from receipt of Notice to Proceed. Bidders should have completed, at least three (3) years prior to the date of submission and receipt of bids, contracts similar to the Project. The description of an eligible bidder is contained in the Bidding Documents, particularly, in Section II. Instructions to Bidders.\\r\\n\\r\\nBidding will be conducted through competitive bidding procedures using a non-discretionary \\u201cpass/fail\\u201d criterion as specified in the IRR of RA No. 12009. Bidding is restricted to Filipino citizens/sole proprietorships, partnerships, or organizations with at least sixty percent (60%) interest or outstanding capital stock belonging to citizens of the Philippines, and to citizens or organizations of a country the laws or regulations of which grant similar rights or privileges to Filipino citizens, pursuant to RA No. 5183. \\r\\n\\r\\nInterested Bidders may obtain further information from the NFA Central Office BAC Secretariat and inspect the Bidding Documents at the address given below from 8:00 AM to 5:00 PM, Mondays to Fridays, except for holidays or inspect the Bidding Documents at the address and e-mail given below. \\r\\n\\r\\nA complete set of Bidding Documents may be acquired by interested Bidders on 16 July 2026 to 5 August 2026 from the address, e-mail, and website given below, upon payment of the applicable fee for the Bidding Documents, pursuant to the latest Guidelines issued by the GPPB, in the following amount:\\r\\n\\r\\n\\r\\nLot No.\\r\\nProject ID No.\\r\\nProject Name\\r\\nApproved Budget for the Contract \\r\\n(in PhP inclusive of all applicable taxes)\\r\\nCost of Bidding Documents\\r\\n(in PhP) \\r\\n1\\r\\n2026-03a\\r\\nSupply and Delivery of Various Laptops\\r\\n107,920,000.00\\r\\n50,000.00\\r\\n2\\r\\n2026-03b\\r\\nSupply and Delivery of Desktop Computers\\r\\n103,360,000.00\\r\\n50,000.00\\r\\n3\\r\\n2026-03c\\r\\nSupply and Delivery of Various Tablets\\r\\n8,240,000.00 \\r\\n10,000.00\\r\\n\\r\\n\\r\\nIt may also be downloaded free of charge from the website of the Philippine Government Electronic Procurement System (PhilGEPS) and the website of the Procuring Entity, provided that Bidders shall pay the applicable fee for the Bidding Documents not later than the submission of their bids.\\r\\n\\r\\nThe NFA Central Office will hold a Pre-Bid Conference on 24 July 2026, 1:30 p.m. onwards at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or through video conferencing or webcasting via the Google Meet platform, which shall be open to prospective Bidders. Meeting details shall be made available to interested bidders upon request sent to the official e-mail of NFA Central Office at bac@nfa.gov.ph. The deadline for clarifications shall be on 26 July, 2026 and the last day for issuance of relevant bid bulletins shall be on 29 July, 2026.\\r\\n \\r\\nBids must be duly received by the Bids and Awards Committee (BAC) Secretariat through manual submission at the office address indicated below, on or before 5 August 2026, 1:30 p.m. Late bids shall not be accepted.\\r\\n\\r\\nAll Bids must be accompanied by a Bid Security in any of the acceptable forms and in the amount stated in ITB Clause 16.1.\\r\\n\\r\\nBid opening shall be on 5 August 2026, 2:00 p.m. at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or via video conferencing or webcasting via the Google Meet platform.  Bids will be opened in the presence of the Bidders\\u2019 representatives who choose to attend the activity.\\r\\n\\r\\nIn accordance with Government Procurement Policy Board (GPPB) Circular 06-2005-Tie-Breaking Method, the Bids and Awards Committee (BAC) shall use a non-discretionary and non-discriminatory measure based on sheer luck or chance, which is \\u201cDRAW LOTS\\u201d, in the event that the two (2) or more bidders have been post-qualified and determined as the bidder having the Lowest Calculated and Responsive Bid (LCRB), based on the following procedures: \\r\\n\\r\\nIn alphabetical order, the bidder shall pick one roll of paper:\\r\\n\\r\\nThe lucky bidders who pick the paper with a \\u201cCONGRATULATIONS\\u201d remark shall be declared as the final bidder having the LCRB and recommended for the award of the contract.\\r\\n\\r\\nThe National Food Authority reserves the right to reject any and all bids, declare a failure of bidding, or not award the contract at any time prior to contract award in accordance with Section 70 of R.A. No. 12009, without incurring any liability to the affected Bidder or Bidders.\\r\\n\\r\\nFor further information, please refer to:\\r\\n\\r\\nName of Contact Person:\\tENGR. LESLIE M. NAVARRO\\r\\n\\t\\t\\t\\t\\t\\tHead, BAC Secretariat \\r\\nPostal Address\\t\\t:\\tNational Food Authority Central Office\\r\\n\\t\\t\\t\\t\\t\\tVisayas Avenue, Brgy. Vasra, Diliman,\\r\\n\\t\\t\\t\\t\\t\\tQuezon City, 1128\\r\\nE-mail Address\\t\\t:\\tbac@nfa.gov.ph\\r\\n\\r\\nYou may visit the following websites for downloading of Bidding Documents:\\r\\n\\r\\nNFA Central Office website (www.nfa.gov.ph)\\r\\nPhilGEPS website (www.philgeps.gov.ph)\\r\\n\\r\\n\\r\\nApproved by:\\r\\n\\r\\n\\r\\n              \\r\\nVERALEW DG. DE VERA\\r\\nChairperson, Bids and Awards Committee\\r\\nNational Food Authority\\r\\n\\r\\n\\r\\n\\r\\n16 July 2026\",\"posting_status\":\"scheduled\",\"current_stage\":\"bid_notice\",\"archived_at\":null,\"archive_reason\":null,\"archived_by\":null,\"archive_approval_reference\":null,\"archive_approved_by\":null,\"archive_approved_at\":null,\"category\":\"goods\",\"end_user_unit\":\"CPMSD\",\"region\":\"Central Office\",\"branch\":\"Administrative and General Services Department\",\"created_by\":4,\"updated_by\":4,\"created_at\":\"2026-07-15 22:12:40\",\"updated_at\":\"2026-07-15 22:12:40\",\"creator_username\":\"aacarillo\",\"creator_firstname\":\"Angelo\",\"creator_lastname\":\"Carillo\"}', 'Competitive Bidding procurement record created.', '6c70af08d73df6410d3d6532a7552a7dc65eeb837e30643f7a4333824da9baa5', NULL, '2026-07-15 14:12:40'),
(22, 12, 4, 'create_parent', 'bid_notice', 7, NULL, '{\"id\":12,\"procurement_mode\":\"competitive_bidding\",\"reference_number\":\"2026-02\",\"procurement_title\":\"Supply and Delivery of One Hundred Fifty (150) Units Custom-Built Delivery and Cargo Trucks\",\"abc\":\"705000000.00\",\"mode_of_procurement\":\"competitive_bidding\",\"posting_date\":\"2026-07-17 00:01:00\",\"bid_submission_deadline\":\"2026-08-10 09:00:00\",\"description\":\"Invitation to Bid for Supply and Delivery of One Hundred Fifty (150) Units Custom-Built Delivery and Cargo Trucks\\r\\n\\r\\nThe National Food Authority (NFA) Central Office, through the approved 2026 Corporate Operating Budget intends to apply the sum of Seven Hundred Five Million Pesos Only (Php 705,000,000.00), inclusive of all applicable taxes, being the Approved Budget for the Contract (ABC) to payments under the contract for the Supply and Delivery of One Hundred Fifty (150) Units Custom-Built Delivery and Cargo Trucks. Bids received in excess of the ABC shall be automatically rejected at bid opening. \\r\\n\\r\\nLot No.\\r\\nProject ID No.\\r\\nQty.\\r\\nItem/Description\\r\\nApproved Budget for the Contract \\r\\n(in PhP)\\r\\nPrice of Bid Documents\\r\\n(in PhP)\\r\\n1\\r\\n2026-02\\r\\nOne (1) Lot\\r\\nSupply and Delivery of One Hundred Fifty (150) Units Custom-Built Delivery and Cargo Trucks\\r\\n\\r\\nItem 1: 110 units 6-Wheeler 120 bags capacity Delivery Trucks\\r\\n\\r\\nItem 2: 28 units 6-Wheeler 180 bags capacity Delivery Trucks\\r\\n\\r\\nItem 3: 12 units 10-Wheeler 360 bags capacity Cargo Trucks\\r\\n705,000,000.00\\r\\n\\r\\n\\r\\n\\r\\n\\r\\n\\r\\nABC Per item:\\r\\n462,859,110.06\\r\\n\\r\\n\\r\\n\\r\\n\\r\\n159,281,681.91\\r\\n\\r\\n\\r\\n\\r\\n\\r\\n82,589,208.03\\r\\n75,000.00\\r\\n\\r\\n\\r\\nThe summary of bidding activities is as follows:\\r\\n\\r\\nAdvertisement/Posting of Invitation to Bid\\r\\nJuly 17, 2026\\r\\nIssuance and Availability of Bid Documents\\r\\nJuly 17, 2026\\r\\nPre-Bid Conference\\r\\nJuly 27, 2026\\r\\nDeadline of Submission of Request for Clarification\\r\\nJuly 29, 2026\\r\\nLast Day of Issuance of Bid Bulletin\\r\\nAugust 3, 2026\\r\\nDeadline for Submission of Bids\\r\\nAugust 10, 2026\\r\\n\\r\\n\\r\\nThe NFA Central Office, through the Bids and Awards Committee, now invites bids for the above Supply and Delivery of One Hundred Fifty (150) Units Custom-Built Delivery and Cargo Trucks. Delivery of the Goods is required within one hundred eighty (180) calendar days from receipt of Notice to Proceed. Bidders should have completed, at least ten (10) years from the date of submission and receipt of bids, contracts similar to the Project. The description of an eligible bidder is contained in the Bidding Documents, particularly, in Section II. Instructions to Bidders.\\r\\n\\r\\nBidding will be conducted through competitive bidding procedures using a non-discretionary \\u201cpass/fail\\u201d criterion as specified in the IRR of RA No. 12009. Bidding is open to all interested Bidders, whether local or foreign, subject to the conditions for eligibility provided in the IRR.\\r\\n\\r\\nInterested Bidders may obtain further information from the NFA Central Office BAC Secretariat and inspect the Bidding Documents at the address given below from 8:00 AM to 5:00 PM.\\r\\n\\r\\nA complete set of Bidding Documents may be acquired by interested Bidders on 17 July 2026 to 10 August 2026 from the address, e-mail, and website given below upon payment of the applicable fee for the Bidding Documents, pursuant to the latest Guidelines issued by the GPPB, in the amount of SEVENTY-FIVE THOUSAND PESOS ONLY (PhP 75,000.00).\\r\\n\\r\\nIt may also be downloaded free of charge from the website of the Philippine Government Electronic Procurement System (PhilGEPS) and the website of the Procuring Entity, provided that Bidders shall pay the applicable fee for the Bidding Documents not later than the submission of their bids.\\r\\n\\r\\nThe NFA Central Office will hold a Pre-Bid Conference on 27 July 2026, 9:00 a.m. onwards at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or through video conferencing or webcasting via the Google Meet platform, which shall be open to prospective Bidders. Meeting details shall be made available to interested bidders upon request sent to the official e-mail of NFA Central Office at bac@nfa.gov.ph. The deadline for clarifications shall be on 29 July 2026 and the last day for issuance of relevant bid bulletins shall be on 3 August 2026.\\r\\n \\r\\nBids must be duly received by the Bids and Awards Committee (BAC) Secretariat through manual submission at the office address indicated below, on or before 10 August 2026, 8:30 a.m.. Late bids shall not be accepted.\\r\\n\\r\\nAll Bids must be accompanied by a Bid Security in any of the acceptable forms and in the amount stated in ITB Clause 16.1.\\r\\n\\r\\nBid opening shall be on 10 August 2026, 9:00 a.m. at the Office of the Assistant Administrator for Operations, Seventh (7th) Floor, NFA Building, Visayas Avenue, Brgy. VASRA, Diliman, Quezon City, and/or via video conferencing or webcasting via the Google Meet platform.  Bids will be opened in the presence of the Bidders\\u2019 representatives who choose to attend the activity.\\r\\n\\r\\nIn accordance with Government Procurement Policy Board (GPPB) Circular 06-2005-Tie-Breaking Method, the Bids and Awards Committee (BAC) shall use a non-discretionary and non-discriminatory measure based on sheer luck or chance, which is \\u201cDRAW LOTS\\u201d, in the event that the two (2) or more bidders have been post-qualified and determined as the bidder having the Lowest Calculated and Responsive Bid (LCRB), based on the following procedures: \\r\\nIn alphabetical order, the bidder shall pick one roll of paper:\\r\\nThe lucky bidders who pick the paper with a \\u201cCONGRATULATIONS\\u201d remark shall be declared as the final bidder having the LCRB and recommended for the award of the contract.\\r\\n\\r\\nThe National Food Authority reserves the right to reject any and all bids, declare a failure of bidding, or not award the contract at any time prior to contract award in accordance with Section 70 of R.A. No. 12009, without incurring any liability to the affected Bidder or Bidders.\\r\\n\\r\\nFor further information, please refer to:\\r\\n\\r\\nName of Contact Person\\t:\\tENGR. LESLIE M. NAVARRO\\r\\n\\t\\t\\t\\t\\t\\tHead, BAC Secretariat \\r\\nPostal Address\\t\\t:\\tNational Food Authority Central Office\\r\\n\\t\\t\\t\\t\\t\\tVisayas Avenue, Brgy. Vasra, Diliman,\\r\\n\\t\\t\\t\\t\\t\\tQuezon City, 1128\\r\\nE-mail Address\\t\\t:\\tbac@nfa.gov.ph\\r\\n\\r\\nYou may visit the following websites for downloading of Bidding Documents:\\r\\n\\r\\nNFA Central Office website (www.nfa.gov.ph)\\r\\nPhilGEPS website (www.philgeps.gov.ph)\\r\\n\\r\\n\\r\\n\\r\\n\\r\\n\\t\\t\\t\\t\\t\\t\\t\\t\\t\\t            Approved by:\\r\\n\\r\\n\\r\\n              ____________________________\\r\\n Veralew DG. De Vera\\r\\nChairman, Bids and Awards Committee\\r\\nNational Food Authority\\r\\n\\r\\n\\r\\n\\r\\n17 July 2026\",\"posting_status\":\"scheduled\",\"current_stage\":\"bid_notice\",\"archived_at\":null,\"archive_reason\":null,\"archived_by\":null,\"archive_approval_reference\":null,\"archive_approved_by\":null,\"archive_approved_at\":null,\"category\":\"goods\",\"end_user_unit\":\"AGSD\",\"region\":\"Central Office\",\"branch\":\"Administrative and General Services Department\",\"created_by\":4,\"updated_by\":4,\"created_at\":\"2026-07-16 20:44:04\",\"updated_at\":\"2026-07-16 20:44:04\",\"creator_username\":\"aacarillo\",\"creator_firstname\":\"Angelo\",\"creator_lastname\":\"Carillo\"}', 'Competitive Bidding procurement record created.', '072345dccf30c4907c4a1a9c38ff95bae16d1b7209117c5457c3bb62da1093aa', NULL, '2026-07-16 12:44:04'),
(23, 2, 4, 'create_document', 'contract', 1, NULL, '{\"id\":1,\"parent_procurement_id\":2,\"title\":\"Approved Contract\",\"description\":\"Approved Contract for Procurement of Property Information System\",\"file_path\":\"storage/uploads/notices/notice_6a689a5a9d7e20.36838469.pdf\",\"file_hash\":\"cc57c4018a87cd9c33fd7c14398f6a76746f6f0239c83be067e4d912f094ea6e\",\"document_type\":\"contract\",\"sequence_stage\":5,\"posted_at\":\"2026-07-28 21:00:00\",\"created_by\":4,\"updated_by\":4,\"created_at\":\"2026-07-28 20:02:34\",\"updated_at\":\"2026-07-28 20:02:34\",\"creator_username\":\"aacarillo\",\"creator_firstname\":\"Angelo\",\"creator_lastname\":\"Carillo\"}', 'Competitive Bidding document posted.', 'cc57c4018a87cd9c33fd7c14398f6a76746f6f0239c83be067e4d912f094ea6e', NULL, '2026-07-28 12:02:34'),
(24, 2, 4, 'create_document', 'notice_to_proceed', 1, NULL, '{\"id\":1,\"parent_procurement_id\":2,\"title\":\"Notice to Proceed\",\"description\":\"Notice to Proceed for Procurement of Property Information System\",\"file_path\":\"storage/uploads/notices/notice_6a689a809b7e50.39701815.pdf\",\"file_hash\":\"4ccbb3780bf9549e983bce9771543c8276de2da6fc9c0d10c00142f3ae27d654\",\"document_type\":\"notice_to_proceed\",\"sequence_stage\":6,\"posted_at\":\"2026-07-28 21:00:00\",\"created_by\":4,\"updated_by\":4,\"created_at\":\"2026-07-28 20:03:12\",\"updated_at\":\"2026-07-28 20:03:12\",\"creator_username\":\"aacarillo\",\"creator_firstname\":\"Angelo\",\"creator_lastname\":\"Carillo\"}', 'Competitive Bidding document posted.', '4ccbb3780bf9549e983bce9771543c8276de2da6fc9c0d10c00142f3ae27d654', NULL, '2026-07-28 12:03:12'),
(25, 11, 4, 'create_document', 'supplemental_bid_bulletin', 4, NULL, '{\"id\":4,\"parent_procurement_id\":11,\"title\":\"Supplemental/Bid Bulletin #1 Supply and Delivery of Various Tablets\",\"description\":\"Supplemental/Bid Bulletin #1 Supply and Delivery of Various Tablets\",\"file_path\":\"storage/uploads/notices/notice_6a69f578d8e597.49289372.pdf\",\"file_hash\":\"7c5ddef04058fe1688d395feb0ae7ae797ec51d012790cdb2ab64671325a65ed\",\"document_type\":\"supplemental_bid_bulletin\",\"sequence_stage\":2,\"posted_at\":\"2026-07-29 21:30:00\",\"created_by\":4,\"updated_by\":4,\"created_at\":\"2026-07-29 20:43:36\",\"updated_at\":\"2026-07-29 20:43:36\",\"creator_username\":\"aacarillo\",\"creator_firstname\":\"Angelo\",\"creator_lastname\":\"Carillo\"}', 'Competitive Bidding document posted.', '7c5ddef04058fe1688d395feb0ae7ae797ec51d012790cdb2ab64671325a65ed', NULL, '2026-07-29 12:43:36'),
(26, 10, 4, 'create_document', 'supplemental_bid_bulletin', 5, NULL, '{\"id\":5,\"parent_procurement_id\":10,\"title\":\"Supplemental/Bid Bulletin #1 Supply and Delivery of Desktop Computers\",\"description\":\"Supplemental/Bid Bulletin #1 Supply and Delivery of Desktop Computers\",\"file_path\":\"storage/uploads/notices/notice_6a69f5a6ab0621.79060218.pdf\",\"file_hash\":\"7c5ddef04058fe1688d395feb0ae7ae797ec51d012790cdb2ab64671325a65ed\",\"document_type\":\"supplemental_bid_bulletin\",\"sequence_stage\":2,\"posted_at\":\"2026-07-29 21:30:00\",\"created_by\":4,\"updated_by\":4,\"created_at\":\"2026-07-29 20:44:22\",\"updated_at\":\"2026-07-29 20:44:22\",\"creator_username\":\"aacarillo\",\"creator_firstname\":\"Angelo\",\"creator_lastname\":\"Carillo\"}', 'Competitive Bidding document posted.', '7c5ddef04058fe1688d395feb0ae7ae797ec51d012790cdb2ab64671325a65ed', NULL, '2026-07-29 12:44:22'),
(27, 9, 4, 'create_document', 'supplemental_bid_bulletin', 6, NULL, '{\"id\":6,\"parent_procurement_id\":9,\"title\":\"Supplemental/Bid Bulletin #1 Supply and Delivery of Various Laptops\",\"description\":\"Supplemental/Bid Bulletin #1 Supply and Delivery of Various Laptops\",\"file_path\":\"storage/uploads/notices/notice_6a69f5e594cf60.92812592.pdf\",\"file_hash\":\"7c5ddef04058fe1688d395feb0ae7ae797ec51d012790cdb2ab64671325a65ed\",\"document_type\":\"supplemental_bid_bulletin\",\"sequence_stage\":2,\"posted_at\":\"2026-07-29 21:30:00\",\"created_by\":4,\"updated_by\":4,\"created_at\":\"2026-07-29 20:45:25\",\"updated_at\":\"2026-07-29 20:45:25\",\"creator_username\":\"aacarillo\",\"creator_firstname\":\"Angelo\",\"creator_lastname\":\"Carillo\"}', 'Competitive Bidding document posted.', '7c5ddef04058fe1688d395feb0ae7ae797ec51d012790cdb2ab64671325a65ed', NULL, '2026-07-29 12:45:25');

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
-- Dumping data for table `resolutions`
--

INSERT INTO `resolutions` (`id`, `parent_procurement_id`, `title`, `description`, `file_path`, `file_hash`, `document_type`, `sequence_stage`, `posted_at`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 2, 'BAC Resolution', 'BAC Resolution Recommending Award', 'storage/uploads/notices/notice_6a201919b21f91.56965576.pdf', 'dc3253a11405e186a6cb40d12003b25b3f1b13d34789bb92a1cf5a88278127ac', 'resolution', 3, '2026-06-03 21:00:00', 4, 4, '2026-06-03 12:07:53', '2026-06-03 12:07:53');

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
-- Dumping data for table `rfqs`
--

INSERT INTO `rfqs` (`id`, `parent_procurement_id`, `title`, `description`, `file_path`, `file_hash`, `document_type`, `sequence_stage`, `posted_at`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 4, 'RFQ and Tender Form', 'RFQ and Tender Form', 'storage/uploads/notices/notice_6a01cffecb8c78.32151512.pdf', 'c2b2ffe5462984388a268e23d72de326f43fce925a51e873cd6d259a9ee4bb41', 'rfq', 1, '2026-05-12 20:46:00', 6, 6, '2026-05-11 12:47:58', '2026-05-11 12:47:58'),
(2, 5, 'RFQ nad Tender Form', 'RFQ and Tender Form', 'storage/uploads/notices/notice_6a01d1763375d4.52785046.pdf', 'd58df5bdfba7b66ca581e7ffcd13c5443466cf4b17e34ac6b923ccd3d0031bfb', 'rfq', 1, '2026-05-12 20:52:00', 6, 6, '2026-05-11 12:54:14', '2026-05-11 12:54:14'),
(3, 7, 'RFQ and TENDER FORM', 'The description of the item, including other requirements, is defined/indicated in the attached Technical Specifications/Terms of Reference/Terms and Conditions. Please submit the duly accomplished Tender Form and other required documents in a sealed envelope from June 05, 2026 until June 11, 2026, 10:00 AM, to:\r\n\r\nNational Food Authority\r\n6F General Services Division\r\nAdministrative and General Services Department\r\nVisayas Avenue, Barangay Vasra, Quezon City\r\n\r\nThe following eligibility documents are also required and shall be submitted:\r\n\r\n1.	2026 Mayor’s Permit/Business Permit;\r\n2.	2025 ITR or its equivalent document (2026 Quarterly ITR/Monthly Business tax Returns);\r\n3.	Conformed Bid Form (Technical Specifications) / Terms of Reference;\r\n4.	SEC Registration or DTI\r\n5.	PhilGeps Certification or Printout of PhilGeps Organizational Number; and\r\n6.	Original Notarized Omnibus Sworn Statement with Authority of Signatory (Secretary’s Certificate, SPA, Partnership Resolutions).', 'storage/uploads/notices/notice_6a211d15b3f565.20876220.pdf', 'f3472b912ed92f97966be1f4aa522bba06762381934ba033943bfd9895aa7dcc', 'rfq', 1, '2026-06-05 00:00:00', 6, 6, '2026-06-04 06:37:09', '2026-06-04 06:37:09'),
(4, 8, 'RFQ and Tender form', 'Suppliers/bidders must download the attached supporting documents for reference', 'storage/uploads/notices/notice_6a30a15e2e5173.81750397.pdf', 'ac6c99e80e29b849782af38af6b4eaf2a14c4b75ae80d209e2d640726e3ec90c', 'rfq', 1, '2026-06-16 00:00:00', 6, 6, '2026-06-16 01:05:34', '2026-06-16 01:05:34'),
(5, 6, 'RFQ and Tender Form', '1 LOT PREVENTIVE MAINTENANCE OF GENERATOR SETS FOR CY 2026', 'storage/uploads/notices/notice_6a3ccf6caf6b88.31966823.pdf', '12bd56cbeff1b1768fcc60827d7d0ef744b1e39cab6fd49fad30274ec7ef8175', 'rfq', 1, '2026-05-19 00:00:00', 6, 6, '2026-06-25 06:49:16', '2026-06-25 06:49:16');

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
-- Dumping data for table `supplemental_bid_bulletins`
--

INSERT INTO `supplemental_bid_bulletins` (`id`, `parent_procurement_id`, `title`, `description`, `file_path`, `file_hash`, `document_type`, `sequence_stage`, `posted_at`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 2, 'Supplemental bid Bulletin No.1', 'Supplemental Bid Bulletin No.1', 'storage/uploads/notices/notice_69df4ab32d8fb5.50375415.pdf', 'afa0515be75dce8a0a392a83699175e8a3acbcebd8bc4813c79e06c5047af45a', 'supplemental_bid_bulletin', 2, '2026-04-15 17:00:00', 4, 4, '2026-04-15 08:22:11', '2026-04-15 08:22:11'),
(3, 2, 'Supplemental bid Bulletin No. 2', 'Supplemental bid Bulletin No.2', 'storage/uploads/notices/notice_69f09f0668f624.78294584.pdf', '690b01303315b8f145216ab9a211ac7675ee4ece706361d9a6ceedf12e3461fe', 'supplemental_bid_bulletin', 2, '2026-04-28 20:00:00', 4, 4, '2026-04-28 11:50:30', '2026-04-28 11:50:30'),
(4, 11, 'Supplemental/Bid Bulletin #1 Supply and Delivery of Various Tablets', 'Supplemental/Bid Bulletin #1 Supply and Delivery of Various Tablets', 'storage/uploads/notices/notice_6a69f578d8e597.49289372.pdf', '7c5ddef04058fe1688d395feb0ae7ae797ec51d012790cdb2ab64671325a65ed', 'supplemental_bid_bulletin', 2, '2026-07-29 21:30:00', 4, 4, '2026-07-29 12:43:36', '2026-07-29 12:43:36'),
(5, 10, 'Supplemental/Bid Bulletin #1 Supply and Delivery of Desktop Computers', 'Supplemental/Bid Bulletin #1 Supply and Delivery of Desktop Computers', 'storage/uploads/notices/notice_6a69f5a6ab0621.79060218.pdf', '7c5ddef04058fe1688d395feb0ae7ae797ec51d012790cdb2ab64671325a65ed', 'supplemental_bid_bulletin', 2, '2026-07-29 21:30:00', 4, 4, '2026-07-29 12:44:22', '2026-07-29 12:44:22'),
(6, 9, 'Supplemental/Bid Bulletin #1 Supply and Delivery of Various Laptops', 'Supplemental/Bid Bulletin #1 Supply and Delivery of Various Laptops', 'storage/uploads/notices/notice_6a69f5e594cf60.92812592.pdf', '7c5ddef04058fe1688d395feb0ae7ae797ec51d012790cdb2ab64671325a65ed', 'supplemental_bid_bulletin', 2, '2026-07-29 21:30:00', 4, 4, '2026-07-29 12:45:25', '2026-07-29 12:45:25');

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
(2, 'sysadmin', 'System', NULL, 'Administrator', 'Central Office', 'Administrative and General Services Department', '$2y$10$HuCTGefxrK1d7TIBjqDOk.2I2f.w6lJhJmzlwz8LFb8Go6h34NJ1e', 'admin', 'system.admin@nfa.gov.ph', NULL, NULL, NULL, 1, 1, '2026-04-08 16:33:32', '2026-08-04 00:00:39'),
(4, 'aacarillo', 'Angelo', 'A', 'Carillo', 'Central Office', 'Administrative and General Services Department', '$2y$10$hMRsRcwsl4lQqdX/8eW4g.TlXDG8zxIZbUFHRdeKM0AUD1v98rjLK', 'author', 'bac@nfa.gov.ph', NULL, NULL, NULL, 1, 1, '2026-04-09 21:34:30', '2026-08-03 23:45:22'),
(5, 'superadmin', 'Rainier John', 'J', 'Dela Cruz', 'Central Office', 'Administrative and General Services Department', '$2y$10$WV2Jp8v3Y77LdCya3Ges4OnCGcKOV9Mo/YUm8vB4bhBY9.YGfFwGa', 'admin', 'rainier.delacruz@nfa.gov.ph', NULL, NULL, NULL, 1, 1, '2026-04-14 11:18:20', '2026-07-15 23:26:40'),
(6, 'jcañares', 'Jason', 'S', 'Cañares', 'Central Office', 'Administrative and General Services Department', '$2y$10$US4P984qX8P3xmmSJULIyOhMVx62yP/FYq9w54V7/dJSuJ0fSRrwq', 'author', 'agsd.purchasing@nfa.gov.ph', NULL, NULL, NULL, 1, 1, '2026-05-08 00:31:00', '2026-05-08 00:34:11');

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
-- Indexes for table `login_attempt_logs`
--
ALTER TABLE `login_attempt_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_login_attempt_logs_user` (`user_id`),
  ADD KEY `idx_login_attempt_logs_created` (`created_at`),
  ADD KEY `idx_login_attempt_logs_outcome_reason` (`outcome`,`failure_reason`),
  ADD KEY `idx_login_attempt_logs_username` (`username_entered`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bid_notices`
--
ALTER TABLE `bid_notices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `canvasses`
--
ALTER TABLE `canvasses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `login_attempt_logs`
--
ALTER TABLE `login_attempt_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `notices_to_proceed`
--
ALTER TABLE `notices_to_proceed`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `parent_procurement`
--
ALTER TABLE `parent_procurement`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `procurement_activity_logs`
--
ALTER TABLE `procurement_activity_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `resolutions`
--
ALTER TABLE `resolutions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rfqs`
--
ALTER TABLE `rfqs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `supplemental_bid_bulletins`
--
ALTER TABLE `supplemental_bid_bulletins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- Constraints for table `login_attempt_logs`
--
ALTER TABLE `login_attempt_logs`
  ADD CONSTRAINT `fk_login_attempt_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

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
--
-- Database: `ictts`
--
CREATE DATABASE IF NOT EXISTS `ictts` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ictts`;

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `actor_name` varchar(160) DEFAULT NULL,
  `action` varchar(120) NOT NULL,
  `entity_type` varchar(80) DEFAULT NULL,
  `entity_id` varchar(80) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(60) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `actor_name`, `action`, `entity_type`, `entity_id`, `details`, `ip_address`, `created_at`) VALUES
(1, 4, 'Administrator', 'User login', 'user', '4', NULL, '::1', '2026-07-08 09:52:02'),
(2, 4, 'Administrator', 'User logout', 'user', '4', NULL, '::1', '2026-07-08 09:52:43'),
(3, NULL, 'Rainier John Dela Cruz', 'ICT personnel registration', 'user', '5', 'Pending administrator activation.', '192.168.108.254', '2026-07-08 09:54:22'),
(4, 4, 'Administrator', 'User login', 'user', '4', NULL, '192.168.108.254', '2026-07-08 09:54:51'),
(5, 4, 'Administrator', 'User management changes', 'user', '5', 'User updated.', '192.168.108.254', '2026-07-08 09:55:04'),
(6, 4, 'Administrator', 'User logout', 'user', '4', NULL, '192.168.108.254', '2026-07-08 09:55:11'),
(7, 5, 'Rainier John Dela Cruz', 'User login', 'user', '5', NULL, '192.168.108.254', '2026-07-08 09:55:29'),
(8, 5, 'Rainier John Dela Cruz', 'User logout', 'user', '5', NULL, '192.168.108.254', '2026-07-08 09:55:45'),
(9, 4, 'Administrator', 'User login', 'user', '4', NULL, '192.168.108.254', '2026-07-08 09:55:58'),
(10, 4, 'Administrator', 'User management changes', 'user', '5', 'User updated.', '192.168.108.254', '2026-07-08 09:56:10'),
(11, 4, 'Administrator', 'User logout', 'user', '4', NULL, '192.168.108.254', '2026-07-08 09:56:11'),
(12, 5, 'Rainier John Dela Cruz', 'User login', 'user', '5', NULL, '192.168.108.254', '2026-07-08 09:56:48'),
(13, 5, 'Rainier John Dela Cruz', 'User logout', 'user', '5', NULL, '192.168.108.254', '2026-07-08 09:56:57'),
(14, 5, 'Rainier John Dela Cruz', 'User login', 'user', '5', NULL, '192.168.108.254', '2026-07-08 09:57:37'),
(15, 5, 'Rainier John Dela Cruz', 'User logout', 'user', '5', NULL, '192.168.108.254', '2026-07-08 10:08:49'),
(16, 4, 'Administrator', 'User login', 'user', '4', NULL, '192.168.108.254', '2026-07-08 10:09:08'),
(17, 4, 'Administrator', 'User login', 'user', '4', NULL, '::1', '2026-07-08 10:20:17'),
(18, 4, 'Administrator', 'User login', 'user', '4', NULL, '192.168.108.254', '2026-07-08 10:20:42'),
(19, 4, 'Administrator', 'User login', 'user', '4', NULL, '::1', '2026-07-08 10:52:35'),
(20, 4, 'Administrator', 'User login', 'user', '4', NULL, '192.168.108.254', '2026-07-08 13:28:38'),
(21, 4, 'Administrator', 'Service library change', 'service_category', NULL, 'Service category added.', '192.168.108.254', '2026-07-08 13:46:16'),
(22, 4, 'Administrator', 'Service library change', 'service_item', '8', 'Service item updated.', '192.168.108.254', '2026-07-08 13:47:03'),
(23, 4, 'Administrator', 'Service library change', 'service_item', NULL, 'Service item added.', '192.168.108.254', '2026-07-08 13:47:46'),
(24, 4, 'Administrator', 'Service library change', 'service_item', NULL, 'Service item added.', '192.168.108.254', '2026-07-08 13:48:03'),
(25, 4, 'Administrator', 'Service library change', 'service_item', NULL, 'Service item added.', '192.168.108.254', '2026-07-08 13:48:12'),
(26, 4, 'Administrator', 'Service library change', 'service_item', NULL, 'Service item added.', '192.168.108.254', '2026-07-08 13:48:53'),
(27, 4, 'Administrator', 'Service library change', 'service_category', '1', 'Service category updated.', '192.168.108.254', '2026-07-08 13:49:03'),
(28, 4, 'Administrator', 'Service library change', 'service_item', '1', 'Service item updated.', '192.168.108.254', '2026-07-08 13:49:23'),
(29, 4, 'Administrator', 'Service library change', 'service_item', '4', 'Service item updated.', '192.168.108.254', '2026-07-08 13:49:32'),
(30, 4, 'Administrator', 'Service library change', 'service_item', '4', 'Service item updated.', '192.168.108.254', '2026-07-08 13:49:37'),
(31, 4, 'Administrator', 'Service library change', 'service_item', '4', 'Service item updated.', '192.168.108.254', '2026-07-08 13:49:39'),
(32, 4, 'Administrator', 'Service library change', 'service_item', '4', 'Service item updated.', '192.168.108.254', '2026-07-08 13:49:47'),
(33, 4, 'Administrator', 'Service library change', 'service_item', '2', 'Service item updated.', '192.168.108.254', '2026-07-08 13:50:18'),
(34, 4, 'Administrator', 'Service library change', 'service_item', '5', 'Service item updated.', '192.168.108.254', '2026-07-08 13:50:25'),
(35, 4, 'Administrator', 'Service library change', 'service_item', '3', 'Service item updated.', '192.168.108.254', '2026-07-08 13:50:30'),
(36, 4, 'Administrator', 'Service library change', 'service_item', NULL, 'Service item added.', '192.168.108.254', '2026-07-08 13:50:38'),
(37, 4, 'Administrator', 'Service library change', 'service_item', NULL, 'Service item added.', '192.168.108.254', '2026-07-08 13:50:45'),
(38, 4, 'Administrator', 'Service library change', 'service_item', NULL, 'Service item added.', '192.168.108.254', '2026-07-08 13:51:10'),
(39, 4, 'Administrator', 'Service library change', 'service_item', NULL, 'Service item added.', '192.168.108.254', '2026-07-08 13:51:14'),
(40, 4, 'Administrator', 'Service library change', 'service_item', NULL, 'Service item added.', '192.168.108.254', '2026-07-08 13:51:20'),
(41, 4, 'Administrator', 'Service library change', 'service_item', NULL, 'Service item added.', '192.168.108.254', '2026-07-08 13:51:26'),
(42, 4, 'Administrator', 'Service library change', 'service_item', NULL, 'Service item added.', '192.168.108.254', '2026-07-08 13:51:32'),
(43, 4, 'Administrator', 'Service library change', 'service_item', NULL, 'Service item added.', '192.168.108.254', '2026-07-08 13:52:16'),
(44, 4, 'Administrator', 'Service library change', 'service_item', NULL, 'Service item added.', '192.168.108.254', '2026-07-08 13:52:27'),
(45, 4, 'Administrator', 'Service library change', 'service_item', NULL, 'Service item added.', '192.168.108.254', '2026-07-08 13:52:34'),
(46, 4, 'Administrator', 'Service library change', 'service_item', NULL, 'Service item added.', '192.168.108.254', '2026-07-08 13:52:38'),
(47, 4, 'Administrator', 'Service library change', 'service_item', NULL, 'Service item added.', '192.168.108.254', '2026-07-08 13:52:49'),
(48, 4, 'Administrator', 'User management changes', 'user', '5', 'User updated.', '192.168.108.254', '2026-07-08 13:53:30'),
(49, NULL, 'Christian Abrera', 'ICT personnel registration', 'user', '6', 'Pending administrator activation.', '192.168.108.254', '2026-07-08 13:56:43'),
(50, NULL, 'Paulo Anthony A. Jacinto', 'ICT personnel registration', 'user', '7', 'Pending administrator activation.', '192.168.108.254', '2026-07-08 13:57:30'),
(51, 4, 'Administrator', 'User management changes', 'user', '7', 'User updated.', '192.168.108.254', '2026-07-08 13:58:01'),
(52, 4, 'Administrator', 'User management changes', 'user', '6', 'User updated.', '192.168.108.254', '2026-07-08 13:58:09'),
(53, 4, 'Administrator', 'User management changes', 'user', '7', 'User updated.', '192.168.108.254', '2026-07-08 14:00:08'),
(54, 4, 'Administrator', 'User management changes', 'user', '6', 'User updated.', '192.168.108.254', '2026-07-08 14:00:14'),
(55, NULL, 'Boots B. Torres', 'ICT personnel registration', 'user', '8', 'Pending administrator activation.', '192.168.108.254', '2026-07-08 14:03:31'),
(56, NULL, 'Eric', 'ICT personnel registration', 'user', '9', 'Pending administrator activation.', '192.168.108.254', '2026-07-08 14:07:45'),
(57, 4, 'Administrator', 'User management changes', 'user', '9', 'User updated.', '192.168.108.254', '2026-07-08 14:11:51'),
(58, 4, 'Administrator', 'User management changes', 'user', '8', 'User updated.', '192.168.108.254', '2026-07-08 14:11:59'),
(59, NULL, 'Jay Jason M. Peñas', 'ICT personnel registration', 'user', '10', 'Pending administrator activation.', '192.168.108.254', '2026-07-08 14:14:34'),
(60, 4, 'Administrator', 'User management changes', 'user', '10', 'User updated.', '192.168.108.254', '2026-07-08 14:21:47'),
(61, 4, 'Administrator', 'User management changes', 'user', '10', 'User updated.', '192.168.108.254', '2026-07-08 14:30:15'),
(62, 4, 'Administrator', 'User management changes', 'user', '9', 'User updated.', '192.168.108.254', '2026-07-08 14:30:29'),
(63, NULL, 'Rommel Siscar', 'ICT personnel registration', 'user', '11', 'Pending administrator activation.', '192.168.108.254', '2026-07-08 14:38:35'),
(64, 8, 'Boots B. Torres', 'User login', 'user', '8', NULL, '192.168.108.254', '2026-07-08 14:56:24'),
(65, NULL, 'Mark Edison N.Quiddam', 'ICT personnel registration', 'user', '12', 'Pending administrator activation.', '192.168.108.254', '2026-07-08 15:12:48'),
(66, 4, 'Administrator', 'User login', 'user', '4', NULL, '192.168.108.254', '2026-07-08 15:26:11'),
(67, 4, 'Administrator', 'User management changes', 'user', '12', 'User updated.', '192.168.108.254', '2026-07-08 15:26:24'),
(68, 4, 'Administrator', 'User management changes', 'user', '11', 'User updated.', '192.168.108.254', '2026-07-08 15:26:42'),
(69, 4, 'Administrator', 'User management changes', 'user', '5', 'User updated.', '192.168.108.254', '2026-07-08 15:27:06'),
(70, 4, 'Administrator', 'User logout', 'user', '4', NULL, '192.168.108.254', '2026-07-08 15:27:10'),
(71, 5, 'Rainier John Dela Cruz', 'User login', 'user', '5', NULL, '192.168.108.254', '2026-07-08 15:27:21'),
(72, 5, 'Mang Juan', 'Ticket submission', 'ticket', '1', 'Public request submitted.', '192.168.108.254', '2026-07-08 15:54:44'),
(73, 5, 'Rainier John Dela Cruz', 'Ticket assignment', 'ticket', '1', 'Primary assignment to Boots B. Torres', '192.168.108.254', '2026-07-08 16:04:01'),
(74, 4, 'Administrator', 'User login', 'user', '4', NULL, '192.168.108.254', '2026-07-09 07:19:10'),
(75, 4, 'Administrator', 'User management changes', 'user', '5', 'User updated.', '192.168.108.254', '2026-07-09 07:19:33'),
(76, 4, 'Administrator', 'User logout', 'user', '4', NULL, '192.168.108.254', '2026-07-09 07:19:35'),
(77, 5, 'Rainier John Dela Cruz', 'User login', 'user', '5', NULL, '192.168.108.254', '2026-07-09 07:19:49'),
(78, 5, 'Rainier Dela Cruz', 'Ticket submission', 'ticket', '2', 'Public request submitted.', '192.168.108.254', '2026-07-09 07:21:51'),
(79, 5, 'Rainier John Dela Cruz', 'Ticket assignment', 'ticket', '2', 'Primary assignment to Paulo Anthony A. Jacinto', '192.168.108.254', '2026-07-09 07:23:18'),
(80, 8, 'Boots B. Torres', 'User login', 'user', '8', NULL, '192.168.108.254', '2026-07-09 07:41:26'),
(81, 8, 'Boots B. Torres', 'Status update', 'ticket', '1', 'Changed to Completed', '192.168.108.254', '2026-07-09 07:41:55'),
(82, 8, 'Boots B. Torres', 'Completion tagging', 'ticket', '1', 'Confirmation email sent to requester.', '192.168.108.254', '2026-07-09 07:41:59'),
(83, 5, 'Mang Juan', 'Requester confirmation', 'ticket', '1', 'Requester confirmed completion.', '192.168.108.254', '2026-07-09 07:50:12'),
(84, 5, 'Rainier Dela Cruz', 'Ticket submission', 'ticket', '3', 'Public request submitted.', '192.168.108.254', '2026-07-09 07:52:21'),
(85, 5, 'Rainier John Dela Cruz', 'Ticket assignment', 'ticket', '3', 'Primary assignment to Boots B. Torres', '192.168.108.254', '2026-07-09 07:53:56'),
(86, 5, 'Rainier John Dela Cruz', 'User logout', 'user', '5', NULL, '192.168.108.254', '2026-07-09 07:54:10'),
(87, 8, 'Boots B. Torres', 'Status update', 'ticket', '3', 'Changed to In Progress', '192.168.108.254', '2026-07-09 07:54:20'),
(88, 8, 'Boots B. Torres', 'Status update', 'ticket', '3', 'Changed to Completed', '192.168.108.254', '2026-07-09 07:54:43'),
(89, 8, 'Boots B. Torres', 'Completion tagging', 'ticket', '3', 'Confirmation email sent to requester.', '192.168.108.254', '2026-07-09 07:54:47'),
(90, NULL, 'Rainier Dela Cruz', 'Requester confirmation', 'ticket', '3', 'Requester confirmed completion.', '192.168.108.254', '2026-07-09 07:55:11'),
(91, 5, 'Rainier John Dela Cruz', 'User login', 'user', '5', NULL, '192.168.108.254', '2026-07-09 08:03:42'),
(92, 5, 'Rainier John Dela Cruz', 'User logout', 'user', '5', NULL, '192.168.108.254', '2026-07-09 08:07:45'),
(93, 4, 'Administrator', 'User login', 'user', '4', NULL, '192.168.108.254', '2026-07-09 08:08:24'),
(94, 7, 'Paulo Anthony A. Jacinto', 'User login', 'user', '7', NULL, '192.168.108.254', '2026-07-09 08:27:35'),
(95, 7, 'Paulo Anthony A. Jacinto', 'Status update', 'ticket', '2', 'Changed to In Progress', '192.168.108.254', '2026-07-09 08:28:03'),
(96, 7, 'Paulo Anthony A. Jacinto', 'Status update', 'ticket', '2', 'Changed to Completed', '192.168.108.254', '2026-07-09 08:28:47'),
(97, 7, 'Paulo Anthony A. Jacinto', 'Completion tagging', 'ticket', '2', 'Confirmation email sent to requester.', '192.168.108.254', '2026-07-09 08:28:52'),
(98, 4, 'Rainier Dela Cruz', 'Requester confirmation', 'ticket', '2', 'Requester confirmed completion.', '192.168.108.254', '2026-07-09 08:29:13'),
(99, NULL, 'Jewell Mayrena', 'ICT personnel registration', 'user', '13', 'Pending administrator activation.', '192.168.108.254', '2026-07-09 10:26:00'),
(100, 6, 'Christian Abrera', 'User login', 'user', '6', NULL, '192.168.108.254', '2026-07-09 11:54:35'),
(101, 6, 'Christian Abrera', 'User logout', 'user', '6', NULL, '192.168.108.254', '2026-07-09 11:54:41'),
(102, 4, 'Administrator', 'User logout', 'user', '4', NULL, '192.168.108.254', '2026-07-09 13:09:05'),
(103, 4, 'Administrator', 'User login', 'user', '4', NULL, '192.168.108.254', '2026-07-09 13:09:22'),
(104, 4, 'Administrator', 'User logout', 'user', '4', NULL, '192.168.108.254', '2026-07-09 13:43:34'),
(105, 4, 'Administrator', 'User login', 'user', '4', NULL, '192.168.108.254', '2026-07-09 13:43:49'),
(106, 4, 'Administrator', 'User management changes', 'user', '13', 'User updated.', '192.168.108.254', '2026-07-09 13:44:11'),
(107, 4, 'Administrator', 'User logout', 'user', '4', NULL, '192.168.108.254', '2026-07-09 13:46:00'),
(108, 4, 'Administrator', 'User login', 'user', '4', NULL, '192.168.108.254', '2026-07-09 13:46:10'),
(109, 4, 'Administrator', 'User management changes', 'user', '4', 'User updated.', '192.168.108.254', '2026-07-09 13:46:59'),
(110, 4, 'Administrator', 'User logout', 'user', '4', NULL, '192.168.108.254', '2026-07-09 15:07:24'),
(111, NULL, 'Henri Jasper Salibad', 'ICT personnel registration', 'user', '14', 'Pending administrator activation.', '192.168.108.254', '2026-07-09 17:33:48'),
(112, 5, 'Rainier John Dela Cruz', 'User login', 'user', '5', NULL, '192.168.108.254', '2026-07-13 14:41:18'),
(113, 5, 'Rainier John Dela Cruz', 'User logout', 'user', '5', NULL, '192.168.108.254', '2026-07-13 14:45:08'),
(114, 4, 'Administrator', 'User login', 'user', '4', NULL, '192.168.108.254', '2026-07-13 14:45:27'),
(115, 4, 'Administrator', 'User management changes', 'user', '14', 'User updated.', '192.168.108.254', '2026-07-13 14:45:57'),
(116, 4, 'Administrator', 'User logout', 'user', '4', NULL, '192.168.108.254', '2026-07-13 14:48:38'),
(117, NULL, 'Gary L Riparip', 'ICT personnel registration', 'user', '15', 'Pending administrator activation.', '192.168.108.254', '2026-07-13 14:51:40'),
(118, 5, 'Rainier John Dela Cruz', 'User login', 'user', '5', NULL, '192.168.108.254', '2026-07-13 14:51:50'),
(119, 5, 'Rainier John Dela Cruz', 'User logout', 'user', '5', NULL, '192.168.108.254', '2026-07-13 14:51:54'),
(120, 4, 'Administrator', 'User login', 'user', '4', NULL, '192.168.108.254', '2026-07-13 14:52:22'),
(121, 4, 'Administrator', 'User management changes', 'user', '15', 'User updated.', '192.168.108.254', '2026-07-13 14:52:39'),
(122, 4, 'Administrator', 'User management changes', 'user', '15', 'User updated.', '192.168.108.254', '2026-07-13 14:54:20'),
(123, 4, 'Administrator', 'User management changes', 'user', '15', 'User updated.', '192.168.108.254', '2026-07-13 14:54:22'),
(124, 4, 'Administrator', 'User management changes', 'user', '15', 'User updated.', '192.168.108.254', '2026-07-13 14:55:14'),
(125, NULL, 'ARMIN VELASQUEZ JAZMINES', 'ICT personnel registration', 'user', '16', 'Pending administrator activation.', '192.168.108.254', '2026-07-13 15:47:51'),
(126, 4, 'Administrator', 'User management changes', 'user', '16', 'User updated.', '192.168.108.254', '2026-07-13 15:53:39'),
(127, 4, 'Administrator', 'User logout', 'user', '4', NULL, '192.168.108.254', '2026-07-13 15:54:08'),
(128, 4, 'Administrator', 'User login', 'user', '4', NULL, '192.168.108.254', '2026-07-13 16:03:38'),
(129, 4, 'Administrator', 'User management changes', 'user', '16', 'User updated.', '192.168.108.254', '2026-07-13 16:04:04'),
(130, 16, 'Armin V. Jazmines', 'User login', 'user', '16', NULL, '192.168.108.254', '2026-07-14 15:09:28'),
(131, 16, 'Armin V. Jazmines', 'Profile update', 'user', '16', 'User updated own profile.', '192.168.108.254', '2026-07-14 15:09:57'),
(132, 16, 'Armin V. Jazmines', 'User logout', 'user', '16', NULL, '192.168.108.254', '2026-07-14 15:10:07'),
(133, 16, 'Armin V. Jazmines', 'User login', 'user', '16', NULL, '192.168.108.254', '2026-07-14 15:10:26'),
(134, 12, 'Mark Edison N.Quiddam', 'User login', 'user', '12', NULL, '192.168.108.254', '2026-07-14 15:18:36'),
(135, 7, 'Paulo Anthony A. Jacinto', 'User login', 'user', '7', NULL, '192.168.108.254', '2026-07-15 08:17:48');

-- --------------------------------------------------------

--
-- Table structure for table `email_logs`
--

CREATE TABLE `email_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED DEFAULT NULL,
  `recipient_email` varchar(190) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` mediumtext NOT NULL,
  `status` enum('queued','sent','failed','logged') NOT NULL DEFAULT 'logged',
  `error_message` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_logs`
--

INSERT INTO `email_logs` (`id`, `ticket_id`, `recipient_email`, `subject`, `body`, `status`, `error_message`, `created_at`) VALUES
(1, NULL, 'tech.support@nfa.gov.ph', 'ICTTS SMTP Test', '<p>This is a test email from ICTTS SMTP configuration.</p>', 'sent', NULL, '2026-07-08 09:43:18'),
(2, 1, 'rainierjdelacruz@gmail.com', 'ICTSD Request Submitted - ICTSD-20260708-A54F85', '<p>Dear Mang Juan,</p><p>Thank you for submitting your request. Your service ticket has been created with the following details:</p><p><strong>Ticket Number:</strong> ICTSD-20260708-A54F85<br><strong>Name of Requestee:</strong> Mang Juan<br><strong>Email Address:</strong> rainierjdelacruz@gmail.com<br><strong>Contact Number:</strong> <br><strong>Category:</strong> SYSTEMS AND APPLICATION<br><strong>Department:</strong> ILOCOS NORTE<br><strong>Specific Request:</strong> HUMAN RESOURCE INFORMATION SYSTEM<br><strong>Description of Request:</strong> I cant log in</p><p>Our ICTSD team will contact you shortly regarding your request.</p><p>Best regards,<br>ICTSD Support Team</p>', 'failed', 'SMTP Error: Could not authenticate.', '2026-07-08 15:54:46'),
(3, 1, 'tech.support@nfa.gov.ph', 'New ICTSD Request - ICTSD-20260708-A54F85', '<p>A new ICTSD request was submitted.</p><p><strong>Ticket No:</strong> ICTSD-20260708-A54F85</p><p><strong>Requester:</strong> Mang Juan</p>', 'failed', 'SMTP Error: Could not authenticate.', '2026-07-08 15:54:48'),
(4, 1, 'rainier.delacruz@nfa.gov.ph', 'New ICTSD Request for Your Unit - ICTSD-20260708-A54F85', '<p>Good day Rainier John Dela Cruz,</p><p>A new ICTSD request was submitted under your service category.</p><p><strong>Ticket No:</strong> ICTSD-20260708-A54F85<br><strong>Requester:</strong> Mang Juan<br><strong>Email Address:</strong> rainierjdelacruz@gmail.com<br><strong>Category:</strong> Systems and Application<br><strong>Specific Request:</strong> Human Resource Information System<br><strong>Description:</strong> I cant log in</p>', 'failed', 'SMTP Error: Could not authenticate.', '2026-07-08 15:54:51'),
(5, NULL, 'tech.support@nfa.gov.ph', 'ICTTS SMTP Auth Re-Test', '<p>SMTP re-test after failed ticket emails.</p>', 'sent', NULL, '2026-07-08 15:58:54'),
(6, NULL, 'tech.support@nfa.gov.ph', 'ICTTS SMTP Post-Fix Test', '<p>Post-fix SMTP test.</p>', 'sent', NULL, '2026-07-08 16:00:04'),
(7, 1, 'rainierjdelacruz@gmail.com', 'ICTSD Request Submitted - ICTSD-20260708-A54F85', '<p>Dear Mang Juan,</p><p>Thank you for submitting your request. Your service ticket has been created with the following details:</p><p><strong>Ticket Number:</strong> ICTSD-20260708-A54F85<br><strong>Name of Requestee:</strong> Mang Juan<br><strong>Email Address:</strong> rainierjdelacruz@gmail.com<br><strong>Contact Number:</strong> <br><strong>Category:</strong> SYSTEMS AND APPLICATION<br><strong>Department:</strong> ILOCOS NORTE<br><strong>Specific Request:</strong> HUMAN RESOURCE INFORMATION SYSTEM<br><strong>Description of Request:</strong> I cant log in</p><p>Our ICTSD team will contact you shortly regarding your request.</p><p>Best regards,<br>ICTSD Support Team</p>', 'sent', NULL, '2026-07-08 16:00:34'),
(8, 1, 'tech.support@nfa.gov.ph', 'New ICTSD Request - ICTSD-20260708-A54F85', '<p>A new ICTSD request was submitted.</p><p><strong>Ticket No:</strong> ICTSD-20260708-A54F85</p><p><strong>Requester:</strong> Mang Juan</p>', 'sent', NULL, '2026-07-08 16:00:37'),
(9, 1, 'rainier.delacruz@nfa.gov.ph', 'New ICTSD Request for Your Unit - ICTSD-20260708-A54F85', '<p>Good day Rainier John Dela Cruz,</p><p>A new ICTSD request was submitted under your service category.</p><p><strong>Ticket No:</strong> ICTSD-20260708-A54F85<br><strong>Requester:</strong> Mang Juan<br><strong>Email Address:</strong> rainierjdelacruz@gmail.com<br><strong>Category:</strong> Systems and Application<br><strong>Specific Request:</strong> Human Resource Information System<br><strong>Description:</strong> I cant log in</p>', 'sent', NULL, '2026-07-08 16:00:41'),
(10, 1, 'boots.torres@nfa.gov.ph', 'ICTSD Ticket Assigned - ICTSD-20260708-A54F85', '<p>Good day Boots B. Torres,</p><p>You have been assigned an ICT service request. Please see the request details below:</p><p><strong>Ticket Number:</strong> ICTSD-20260708-A54F85<br><strong>Name of Requestee:</strong> Mang Juan<br><strong>Email Address:</strong> rainierjdelacruz@gmail.com<br><strong>Department:</strong> Ilocos Norte<br><strong>Category:</strong> Systems and Application<br><strong>Specific Concern:</strong> Human Resource Information System<br><strong>Description:</strong> I cant log in</p><p>Kindly coordinate with the requester and provide the necessary technical assistance.</p><p>Regards,<br>ICTSD Support Team</p>', 'failed', 'SMTP Error: Could not authenticate.', '2026-07-08 16:04:00'),
(11, 1, 'rainierjdelacruz@gmail.com', 'ICTSD Ticket Assigned - ICTSD-20260708-A54F85', '<p>Good day,</p><p>Thank you for submitting your service request with <strong>Ticket #: ICTSD-20260708-A54F85</strong>.</p><p>Please be informed that your request has been assigned to <strong>Boots B. Torres</strong>, our Technical Support Personnel who will be assisting and coordinating with you regarding your concern. Our assigned personnel shall accommodate and attend to your request shortly.</p><p>We sincerely appreciate your patience and cooperation as we work to provide the necessary assistance and support.</p><p>Should you have additional concerns or clarifications, please feel free to coordinate with us.</p><p>Thank you.</p><p>Regards,<br>ICTSD Support Team</p>', 'failed', 'SMTP Error: Could not authenticate.', '2026-07-08 16:04:01'),
(12, 1, 'boots.torres@nfa.gov.ph', 'ICTSD Ticket Assigned - ICTSD-20260708-A54F85', '<p>Good day Boots B. Torres,</p><p>You have been assigned an ICT service request. Please see the request details below:</p><p><strong>Ticket Number:</strong> ICTSD-20260708-A54F85<br><strong>Name of Requestee:</strong> Mang Juan<br><strong>Email Address:</strong> rainierjdelacruz@gmail.com<br><strong>Department:</strong> Ilocos Norte<br><strong>Category:</strong> Systems and Application<br><strong>Specific Concern:</strong> Human Resource Information System<br><strong>Description:</strong> I cant log in</p><p>Kindly coordinate with the requester and provide the necessary technical assistance.</p><p>Regards,<br>ICTSD Support Team</p>', 'sent', NULL, '2026-07-08 16:07:58'),
(13, 1, 'rainierjdelacruz@gmail.com', 'ICTSD Ticket Assigned - ICTSD-20260708-A54F85', '<p>Good day,</p><p>Thank you for submitting your service request with <strong>Ticket #: ICTSD-20260708-A54F85</strong>.</p><p>Please be informed that your request has been assigned to <strong>Boots B. Torres</strong>, our Technical Support Personnel who will be assisting and coordinating with you regarding your concern. Our assigned personnel shall accommodate and attend to your request shortly.</p><p>We sincerely appreciate your patience and cooperation as we work to provide the necessary assistance and support.</p><p>Should you have additional concerns or clarifications, please feel free to coordinate with us.</p><p>Thank you.</p><p>Regards,<br>ICTSD Support Team</p>', 'sent', NULL, '2026-07-08 16:08:01'),
(14, 2, 'rainierjdelacruz@gmail.com', 'ICTSD Request Submitted - ICTSD-20260709-3476E3', '<p>Dear Rainier Dela Cruz,</p><p>Thank you for submitting your request. Your service ticket has been created with the following details:</p><p><strong>Ticket Number:</strong> ICTSD-20260709-3476E3<br><strong>Name of Requestee:</strong> Rainier Dela Cruz<br><strong>Email Address:</strong> rainierjdelacruz@gmail.com<br><strong>Contact Number:</strong> <br><strong>Category:</strong> SYSTEMS AND APPLICATION<br><strong>Department:</strong> ADMINISTRATIVE AND GENERAL SERVICES DEPARTMENT<br><strong>Specific Request:</strong> GOVMAIL SUPPORT<br><strong>Description of Request:</strong> Request for Govmail Account</p><p>Our ICTSD team will contact you shortly regarding your request.</p><p>Best regards,<br>ICTSD Support Team</p>', 'sent', NULL, '2026-07-09 07:21:55'),
(15, 2, 'tech.support@nfa.gov.ph', 'New ICTSD Request - ICTSD-20260709-3476E3', '<p>A new ICTSD request was submitted.</p><p><strong>Ticket No:</strong> ICTSD-20260709-3476E3</p><p><strong>Requester:</strong> Rainier Dela Cruz</p>', 'sent', NULL, '2026-07-09 07:21:59'),
(16, 2, 'rainier.delacruz@nfa.gov.ph', 'New ICTSD Request for Your Unit - ICTSD-20260709-3476E3', '<p>Good day Rainier John Dela Cruz,</p><p>A new ICTSD request was submitted under your service category.</p><p><strong>Ticket No:</strong> ICTSD-20260709-3476E3<br><strong>Requester:</strong> Rainier Dela Cruz<br><strong>Email Address:</strong> rainierjdelacruz@gmail.com<br><strong>Category:</strong> Systems and Application<br><strong>Specific Request:</strong> GovMail Support<br><strong>Description:</strong> Request for Govmail Account</p>', 'sent', NULL, '2026-07-09 07:22:03'),
(17, 2, 'paulo.jacinto@nfa.gov.ph', 'ICTSD Ticket Assigned - ICTSD-20260709-3476E3', '<p>Good day Paulo Anthony A. Jacinto,</p><p>You have been assigned an ICT service request. Please see the request details below:</p><p><strong>Ticket Number:</strong> ICTSD-20260709-3476E3<br><strong>Name of Requestee:</strong> Rainier Dela Cruz<br><strong>Email Address:</strong> rainierjdelacruz@gmail.com<br><strong>Department:</strong> Administrative and General Services Department<br><strong>Category:</strong> Systems and Application<br><strong>Specific Concern:</strong> GovMail Support<br><strong>Description:</strong> Request for Govmail Account</p><p>Kindly coordinate with the requester and provide the necessary technical assistance.</p><p>Regards,<br>ICTSD Support Team</p>', 'sent', NULL, '2026-07-09 07:23:14'),
(18, 2, 'rainierjdelacruz@gmail.com', 'ICTSD Ticket Assigned - ICTSD-20260709-3476E3', '<p>Good day,</p><p>Thank you for submitting your service request with <strong>Ticket #: ICTSD-20260709-3476E3</strong>.</p><p>Please be informed that your request has been assigned to <strong>Paulo Anthony A. Jacinto</strong>, our Technical Support Personnel who will be assisting and coordinating with you regarding your concern. Our assigned personnel shall accommodate and attend to your request shortly.</p><p>We sincerely appreciate your patience and cooperation as we work to provide the necessary assistance and support.</p><p>Should you have additional concerns or clarifications, please feel free to coordinate with us.</p><p>Thank you.</p><p>Regards,<br>ICTSD Support Team</p>', 'sent', NULL, '2026-07-09 07:23:18'),
(19, 1, 'rainierjdelacruz@gmail.com', 'Confirm ICTSD Ticket Completion - ICTSD-20260708-A54F85', '<p>The ICTSD technical personnel marked your request as completed.</p><p><strong>Ticket No:</strong> ICTSD-20260708-A54F85</p><p><strong>Marked Completed By:</strong> Boots B. Torres</p><p><a href=\"https://ebps.nfa.gov.ph/ICTTS/public/confirm/16cc6cfbf2b662faa904906f5f8a3c2acdd1ec58c9665e02bf335ccde0608572\" style=\"display:inline-block;padding:10px 16px;background:#0d6efd;color:#ffffff;text-decoration:none;border-radius:4px;\">Confirm Completion</a></p>', 'sent', NULL, '2026-07-09 07:41:59'),
(20, 1, 'rainierjdelacruz@gmail.com', 'Confirm ICTSD Ticket Completion - ICTSD-20260708-A54F85', '<p>The ICTSD technical personnel marked your request as completed.</p><p><strong>Ticket No:</strong> ICTSD-20260708-A54F85</p><p><strong>Marked Completed By:</strong> Boots B. Torres</p><p><a href=\"http://192.168.108.16/ICTTS/public/confirm/c24f58c161e5c85c7c8ca6f61f269d4caa812e9038a435bfc61f807657a25b77\" style=\"display:inline-block;padding:10px 16px;background:#0d6efd;color:#ffffff;text-decoration:none;border-radius:4px;\">Confirm Completion</a></p>', 'sent', NULL, '2026-07-09 07:46:51'),
(21, 1, 'rainierjdelacruz@gmail.com', 'Confirm ICTSD Ticket Completion - ICTSD-20260708-A54F85', '<p>The ICTSD technical personnel marked your request as completed.</p><p><strong>Ticket No:</strong> ICTSD-20260708-A54F85</p><p><strong>Marked Completed By:</strong> Boots B. Torres</p><p><a href=\"https://webapp.nfa.gov.ph/ICTTS/public/confirm/8f2bdb3b995311600f9892edf6fdd10c0cc6c2a600ea2db52560dbbabf99d880\" style=\"display:inline-block;padding:10px 16px;background:#0d6efd;color:#ffffff;text-decoration:none;border-radius:4px;\">Confirm Completion</a></p>', 'sent', NULL, '2026-07-09 07:49:17'),
(22, 1, 'tech.support@nfa.gov.ph', 'ICTSD Ticket Confirmed - ICTSD-20260708-A54F85', '<p>The requester confirmed completion.</p><p><strong>Ticket No:</strong> ICTSD-20260708-A54F85</p>', 'sent', NULL, '2026-07-09 07:50:16'),
(23, 1, 'rainier.delacruz@nfa.gov.ph', 'ICTSD Ticket Confirmed - ICTSD-20260708-A54F85', '<p>The requester confirmed completion.</p><p><strong>Ticket No:</strong> ICTSD-20260708-A54F85</p>', 'sent', NULL, '2026-07-09 07:50:19'),
(24, 1, 'eric.aguirre@nfa.gov.ph', 'ICTSD Ticket Confirmed - ICTSD-20260708-A54F85', '<p>The requester confirmed completion.</p><p><strong>Ticket No:</strong> ICTSD-20260708-A54F85</p>', 'sent', NULL, '2026-07-09 07:50:23'),
(25, 1, 'boots.torres@nfa.gov.ph', 'ICTSD Ticket Confirmed - ICTSD-20260708-A54F85', '<p>The requester confirmed completion.</p><p><strong>Ticket No:</strong> ICTSD-20260708-A54F85</p>', 'sent', NULL, '2026-07-09 07:50:27'),
(26, 3, 'rainierjdelacruz@gmail.com', 'ICTSD Request Submitted - ICTSD-20260709-7E87EB', '<p>Dear Rainier Dela Cruz,</p><p>Thank you for submitting your request. Your service ticket has been created with the following details:</p><p><strong>Ticket Number:</strong> ICTSD-20260709-7E87EB<br><strong>Name of Requestee:</strong> Rainier Dela Cruz<br><strong>Email Address:</strong> rainierjdelacruz@gmail.com<br><strong>Contact Number:</strong> <br><strong>Category:</strong> SYSTEMS AND APPLICATION<br><strong>Department:</strong> CORPORATE PLANNING AND MANAGEMENT SERVICES DEPARMENT<br><strong>Specific Request:</strong> E-IFOMIS<br><strong>Description of Request:</strong> Cannot access</p><p>Our ICTSD team will contact you shortly regarding your request.</p><p>Best regards,<br>ICTSD Support Team</p>', 'sent', NULL, '2026-07-09 07:52:25'),
(27, 3, 'tech.support@nfa.gov.ph', 'New ICTSD Request - ICTSD-20260709-7E87EB', '<p>A new ICTSD request was submitted.</p><p><strong>Ticket No:</strong> ICTSD-20260709-7E87EB</p><p><strong>Requester:</strong> Rainier Dela Cruz</p>', 'sent', NULL, '2026-07-09 07:52:28'),
(28, 3, 'rainier.delacruz@nfa.gov.ph', 'New ICTSD Request for Your Unit - ICTSD-20260709-7E87EB', '<p>Good day Rainier John Dela Cruz,</p><p>A new ICTSD request was submitted under your service category.</p><p><strong>Ticket No:</strong> ICTSD-20260709-7E87EB<br><strong>Requester:</strong> Rainier Dela Cruz<br><strong>Email Address:</strong> rainierjdelacruz@gmail.com<br><strong>Category:</strong> Systems and Application<br><strong>Specific Request:</strong> E-IFOMIS<br><strong>Description:</strong> Cannot access</p>', 'sent', NULL, '2026-07-09 07:52:32'),
(29, 3, 'boots.torres@nfa.gov.ph', 'ICTSD Ticket Assigned - ICTSD-20260709-7E87EB', '<p>Good day Boots B. Torres,</p><p>You have been assigned an ICT service request. Please see the request details below:</p><p><strong>Ticket Number:</strong> ICTSD-20260709-7E87EB<br><strong>Name of Requestee:</strong> Rainier Dela Cruz<br><strong>Email Address:</strong> rainierjdelacruz@gmail.com<br><strong>Department:</strong> Corporate Planning and Management Services Deparment<br><strong>Category:</strong> Systems and Application<br><strong>Specific Concern:</strong> E-IFOMIS<br><strong>Description:</strong> Cannot access</p><p>Kindly coordinate with the requester and provide the necessary technical assistance.</p><p>Regards,<br>ICTSD Support Team</p>', 'sent', NULL, '2026-07-09 07:53:53'),
(30, 3, 'rainierjdelacruz@gmail.com', 'ICTSD Ticket Assigned - ICTSD-20260709-7E87EB', '<p>Good day,</p><p>Thank you for submitting your service request with <strong>Ticket #: ICTSD-20260709-7E87EB</strong>.</p><p>Please be informed that your request has been assigned to <strong>Boots B. Torres</strong>, our Technical Support Personnel who will be assisting and coordinating with you regarding your concern. Our assigned personnel shall accommodate and attend to your request shortly.</p><p>We sincerely appreciate your patience and cooperation as we work to provide the necessary assistance and support.</p><p>Should you have additional concerns or clarifications, please feel free to coordinate with us.</p><p>Thank you.</p><p>Regards,<br>ICTSD Support Team</p>', 'sent', NULL, '2026-07-09 07:53:56'),
(31, 3, 'rainierjdelacruz@gmail.com', 'Confirm ICTSD Ticket Completion - ICTSD-20260709-7E87EB', '<p>The ICTSD technical personnel marked your request as completed.</p><p><strong>Ticket No:</strong> ICTSD-20260709-7E87EB</p><p><strong>Marked Completed By:</strong> Boots B. Torres</p><p><a href=\"https://webapp.nfa.gov.ph/ICTTS/public/confirm/c5bf2764b693df7734dc5b43bbc57dfc3e03c222bdf9797011e7944c53572216\" style=\"display:inline-block;padding:10px 16px;background:#0d6efd;color:#ffffff;text-decoration:none;border-radius:4px;\">Confirm Completion</a></p>', 'sent', NULL, '2026-07-09 07:54:47'),
(32, 3, 'tech.support@nfa.gov.ph', 'ICTSD Ticket Confirmed - ICTSD-20260709-7E87EB', '<p>The requester confirmed completion.</p><p><strong>Ticket No:</strong> ICTSD-20260709-7E87EB</p>', 'sent', NULL, '2026-07-09 07:55:14'),
(33, 3, 'rainier.delacruz@nfa.gov.ph', 'ICTSD Ticket Confirmed - ICTSD-20260709-7E87EB', '<p>The requester confirmed completion.</p><p><strong>Ticket No:</strong> ICTSD-20260709-7E87EB</p>', 'sent', NULL, '2026-07-09 07:55:17'),
(34, 3, 'eric.aguirre@nfa.gov.ph', 'ICTSD Ticket Confirmed - ICTSD-20260709-7E87EB', '<p>The requester confirmed completion.</p><p><strong>Ticket No:</strong> ICTSD-20260709-7E87EB</p>', 'sent', NULL, '2026-07-09 07:55:21'),
(35, 3, 'boots.torres@nfa.gov.ph', 'ICTSD Ticket Confirmed - ICTSD-20260709-7E87EB', '<p>The requester confirmed completion.</p><p><strong>Ticket No:</strong> ICTSD-20260709-7E87EB</p>', 'sent', NULL, '2026-07-09 07:55:24'),
(36, 2, 'rainierjdelacruz@gmail.com', 'Confirm ICTSD Ticket Completion - ICTSD-20260709-3476E3', '<p>The ICTSD technical personnel marked your request as completed.</p><p><strong>Ticket No:</strong> ICTSD-20260709-3476E3</p><p><strong>Marked Completed By:</strong> Paulo Anthony A. Jacinto</p><p><a href=\"https://webapp.nfa.gov.ph/ICTTS/public/confirm/449d306fa73716d35d56c6e37a24daf85b3e4be8b8d33698c6ac37979217e545\" style=\"display:inline-block;padding:10px 16px;background:#0d6efd;color:#ffffff;text-decoration:none;border-radius:4px;\">Confirm Completion</a></p>', 'sent', NULL, '2026-07-09 08:28:52'),
(37, 2, 'tech.support@nfa.gov.ph', 'ICTSD Ticket Confirmed - ICTSD-20260709-3476E3', '<p>The requester confirmed completion.</p><p><strong>Ticket No:</strong> ICTSD-20260709-3476E3</p>', 'sent', NULL, '2026-07-09 08:29:16'),
(38, 2, 'rainier.delacruz@nfa.gov.ph', 'ICTSD Ticket Confirmed - ICTSD-20260709-3476E3', '<p>The requester confirmed completion.</p><p><strong>Ticket No:</strong> ICTSD-20260709-3476E3</p>', 'sent', NULL, '2026-07-09 08:29:20'),
(39, 2, 'eric.aguirre@nfa.gov.ph', 'ICTSD Ticket Confirmed - ICTSD-20260709-3476E3', '<p>The requester confirmed completion.</p><p><strong>Ticket No:</strong> ICTSD-20260709-3476E3</p>', 'sent', NULL, '2026-07-09 08:29:24'),
(40, 2, 'paulo.jacinto@nfa.gov.ph', 'ICTSD Ticket Confirmed - ICTSD-20260709-3476E3', '<p>The requester confirmed completion.</p><p><strong>Ticket No:</strong> ICTSD-20260709-3476E3</p>', 'sent', NULL, '2026-07-09 08:29:27');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(160) NOT NULL,
  `message` varchar(500) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `link`, `read_at`, `created_at`) VALUES
(1, 5, 'New ticket submitted', 'ICTSD-20260708-A54F85 was submitted by Mang Juan.', 'tickets/1', '2026-07-09 07:22:58', '2026-07-08 15:54:51'),
(2, 8, 'Ticket assigned to you', 'ICTSD-20260708-A54F85 has been assigned to you.', 'tickets/1', '2026-07-09 07:41:31', '2026-07-08 16:04:01'),
(3, 5, 'New ticket submitted', 'ICTSD-20260709-3476E3 was submitted by Rainier Dela Cruz.', 'tickets/2', NULL, '2026-07-09 07:22:03'),
(4, 7, 'Ticket assigned to you', 'ICTSD-20260709-3476E3 has been assigned to you.', 'tickets/2', '2026-07-09 08:27:43', '2026-07-09 07:23:18'),
(5, 4, 'Ticket status updated', 'ICTSD-20260708-A54F85 is now Completed.', 'tickets/1', NULL, '2026-07-09 07:41:55'),
(6, 5, 'Ticket status updated', 'ICTSD-20260708-A54F85 is now Completed.', 'tickets/1', NULL, '2026-07-09 07:41:55'),
(7, 9, 'Ticket status updated', 'ICTSD-20260708-A54F85 is now Completed.', 'tickets/1', NULL, '2026-07-09 07:41:55'),
(8, 4, 'Ticket confirmed completed', 'ICTSD-20260708-A54F85 was confirmed completed by Mang Juan.', 'tickets/1', NULL, '2026-07-09 07:50:27'),
(9, 5, 'Ticket confirmed completed', 'ICTSD-20260708-A54F85 was confirmed completed by Mang Juan.', 'tickets/1', NULL, '2026-07-09 07:50:27'),
(10, 9, 'Ticket confirmed completed', 'ICTSD-20260708-A54F85 was confirmed completed by Mang Juan.', 'tickets/1', NULL, '2026-07-09 07:50:27'),
(11, 8, 'Ticket confirmed completed', 'ICTSD-20260708-A54F85 was confirmed completed by Mang Juan.', 'tickets/1', '2026-07-09 07:51:11', '2026-07-09 07:50:27'),
(12, 5, 'New ticket submitted', 'ICTSD-20260709-7E87EB was submitted by Rainier Dela Cruz.', 'tickets/3', NULL, '2026-07-09 07:52:32'),
(13, 8, 'Ticket assigned to you', 'ICTSD-20260709-7E87EB has been assigned to you.', 'tickets/3', '2026-07-09 07:54:05', '2026-07-09 07:53:56'),
(14, 4, 'Ticket status updated', 'ICTSD-20260709-7E87EB is now In Progress.', 'tickets/3', NULL, '2026-07-09 07:54:20'),
(15, 5, 'Ticket status updated', 'ICTSD-20260709-7E87EB is now In Progress.', 'tickets/3', NULL, '2026-07-09 07:54:20'),
(16, 9, 'Ticket status updated', 'ICTSD-20260709-7E87EB is now In Progress.', 'tickets/3', NULL, '2026-07-09 07:54:20'),
(17, 4, 'Ticket status updated', 'ICTSD-20260709-7E87EB is now Completed.', 'tickets/3', NULL, '2026-07-09 07:54:43'),
(18, 5, 'Ticket status updated', 'ICTSD-20260709-7E87EB is now Completed.', 'tickets/3', NULL, '2026-07-09 07:54:43'),
(19, 9, 'Ticket status updated', 'ICTSD-20260709-7E87EB is now Completed.', 'tickets/3', NULL, '2026-07-09 07:54:43'),
(20, 4, 'Ticket confirmed completed', 'ICTSD-20260709-7E87EB was confirmed completed by Rainier Dela Cruz.', 'tickets/3', '2026-07-09 08:29:38', '2026-07-09 07:55:24'),
(21, 5, 'Ticket confirmed completed', 'ICTSD-20260709-7E87EB was confirmed completed by Rainier Dela Cruz.', 'tickets/3', '2026-07-09 08:04:06', '2026-07-09 07:55:24'),
(22, 9, 'Ticket confirmed completed', 'ICTSD-20260709-7E87EB was confirmed completed by Rainier Dela Cruz.', 'tickets/3', NULL, '2026-07-09 07:55:24'),
(23, 8, 'Ticket confirmed completed', 'ICTSD-20260709-7E87EB was confirmed completed by Rainier Dela Cruz.', 'tickets/3', '2026-07-09 07:55:36', '2026-07-09 07:55:24'),
(24, 4, 'Ticket status updated', 'ICTSD-20260709-3476E3 is now In Progress.', 'tickets/2', NULL, '2026-07-09 08:28:03'),
(25, 5, 'Ticket status updated', 'ICTSD-20260709-3476E3 is now In Progress.', 'tickets/2', NULL, '2026-07-09 08:28:03'),
(26, 9, 'Ticket status updated', 'ICTSD-20260709-3476E3 is now In Progress.', 'tickets/2', NULL, '2026-07-09 08:28:03'),
(27, 4, 'Ticket status updated', 'ICTSD-20260709-3476E3 is now Completed.', 'tickets/2', NULL, '2026-07-09 08:28:47'),
(28, 5, 'Ticket status updated', 'ICTSD-20260709-3476E3 is now Completed.', 'tickets/2', NULL, '2026-07-09 08:28:47'),
(29, 9, 'Ticket status updated', 'ICTSD-20260709-3476E3 is now Completed.', 'tickets/2', NULL, '2026-07-09 08:28:47'),
(30, 4, 'Ticket confirmed completed', 'ICTSD-20260709-3476E3 was confirmed completed by Rainier Dela Cruz.', 'tickets/2', NULL, '2026-07-09 08:29:27'),
(31, 5, 'Ticket confirmed completed', 'ICTSD-20260709-3476E3 was confirmed completed by Rainier Dela Cruz.', 'tickets/2', '2026-07-13 14:41:31', '2026-07-09 08:29:27'),
(32, 9, 'Ticket confirmed completed', 'ICTSD-20260709-3476E3 was confirmed completed by Rainier Dela Cruz.', 'tickets/2', NULL, '2026-07-09 08:29:27'),
(33, 7, 'Ticket confirmed completed', 'ICTSD-20260709-3476E3 was confirmed completed by Rainier Dela Cruz.', 'tickets/2', NULL, '2026-07-09 08:29:27');

-- --------------------------------------------------------

--
-- Table structure for table `offices`
--

CREATE TABLE `offices` (
  `id` int(10) UNSIGNED NOT NULL,
  `region_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(190) NOT NULL,
  `office_type` enum('Regional Office','Branch Office','Central Office','District Office','Other') NOT NULL DEFAULT 'Branch Office',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `offices`
--

INSERT INTO `offices` (`id`, `region_id`, `name`, `office_type`, `status`, `created_at`) VALUES
(1, 1, 'Maguindanao', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(2, 1, 'Basilan', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(3, 1, 'ARMM Regional Office', 'Regional Office', 'active', '2026-05-28 15:19:34'),
(4, 1, 'Lanao del Sur', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(5, 2, 'Surigao del Sur', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(6, 2, 'Agusan del Sur', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(7, 2, 'NFA CARAGA Regional Office', 'Regional Office', 'active', '2026-05-28 15:19:34'),
(8, 3, 'Administrative and General Services Department', 'Central Office', 'active', '2026-05-28 15:19:34'),
(9, 3, 'Finance Department', 'Central Office', 'active', '2026-05-28 15:19:34'),
(10, 3, 'Operations and Coordination Department', 'Central Office', 'active', '2026-05-28 15:19:34'),
(11, 3, 'Legal Affairs Department', 'Central Office', 'active', '2026-05-28 15:19:34'),
(12, 3, 'Corporate Planning and Management Services Deparment', 'Central Office', 'active', '2026-05-28 15:19:34'),
(13, 3, 'Public Affairs Division', 'Central Office', 'active', '2026-05-28 15:19:34'),
(14, 4, 'East District', 'District Office', 'active', '2026-05-28 15:19:34'),
(15, 4, 'Central District', 'District Office', 'active', '2026-05-28 15:19:34'),
(16, 4, 'NCR Regional Office', 'Regional Office', 'active', '2026-05-28 15:19:34'),
(17, 5, 'Eastern Pangasinan', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(18, 5, 'Region I Office', 'Regional Office', 'active', '2026-05-28 15:19:34'),
(19, 5, 'La Union', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(20, 5, 'Ilocos Norte', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(21, 6, 'Cagayan', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(22, 6, 'Nueva Vizcaya', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(23, 6, 'Region II Office', 'Regional Office', 'active', '2026-05-28 15:19:34'),
(24, 6, 'Isabela', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(25, 7, 'Region III Office', 'Regional Office', 'active', '2026-05-28 15:19:34'),
(26, 7, 'Bulacan', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(27, 7, 'Tarlac', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(28, 7, 'Pampanga', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(29, 7, 'Nueva Ecija', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(30, 8, 'Occidental Mindoro', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(31, 8, 'Palawan', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(32, 8, 'Oriental Mindoro', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(33, 8, 'Region IV Office', 'Regional Office', 'active', '2026-05-28 15:19:34'),
(34, 8, 'Batangas', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(35, 8, 'Quezon', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(36, 8, 'Laguna', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(37, 9, 'Zamboanga Del Sur', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(38, 9, 'Region IX Office', 'Regional Office', 'active', '2026-05-28 15:19:34'),
(39, 9, 'Zamboanga', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(40, 10, 'Region V Office', 'Regional Office', 'active', '2026-05-28 15:19:34'),
(41, 10, 'Camarines Sur', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(42, 10, 'Sorsogon', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(43, 10, 'Albay', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(44, 11, 'Region VI Office', 'Regional Office', 'active', '2026-05-28 15:19:34'),
(45, 11, 'Iloilo', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(46, 11, 'Capiz', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(47, 11, 'Negros Occidental', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(48, 12, 'Region VII Office', 'Regional Office', 'active', '2026-05-28 15:19:34'),
(49, 12, 'Cebu', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(50, 12, 'Negros Oriental', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(51, 12, 'Bohol', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(52, 13, 'Leyte', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(53, 13, 'Region VIII Office', 'Regional Office', 'active', '2026-05-28 15:19:34'),
(54, 13, 'Northern Samar', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(55, 14, 'Misamis Oriental', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(56, 14, 'Bukidnon', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(57, 14, 'Region X Office', 'Regional Office', 'active', '2026-05-28 15:19:34'),
(58, 14, 'Lanao del Norte', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(59, 15, 'Davao Oriental', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(60, 15, 'Davao del Sur', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(61, 15, 'Region XI Office', 'Regional Office', 'active', '2026-05-28 15:19:34'),
(62, 15, 'Davao del Norte', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(63, 16, 'Sultan Kudarat', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(64, 16, 'North Cotabato', 'Branch Office', 'active', '2026-05-28 15:19:34'),
(65, 16, 'Region XII Office', 'Regional Office', 'active', '2026-05-28 15:19:34'),
(66, 16, 'South Cotabato', 'Branch Office', 'active', '2026-05-28 15:19:34');

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

CREATE TABLE `regions` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(30) NOT NULL,
  `name` varchar(160) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`id`, `code`, `name`, `status`, `created_at`) VALUES
(1, 'ARMM', 'NFA ARMM Regional Office', 'active', '2026-05-28 15:19:34'),
(2, 'CARAGA', 'NFA CARAGA Regional Office', 'active', '2026-05-28 15:19:34'),
(3, 'CO', 'NFA Central Office', 'active', '2026-05-28 15:19:34'),
(4, 'NCR', 'NFA NCR Regional Office', 'active', '2026-05-28 15:19:34'),
(5, 'R1', 'NFA Region I Office', 'active', '2026-05-28 15:19:34'),
(6, 'R2', 'NFA Region II Office', 'active', '2026-05-28 15:19:34'),
(7, 'R3', 'NFA Region III Office', 'active', '2026-05-28 15:19:34'),
(8, 'R4', 'NFA Region IV Office', 'active', '2026-05-28 15:19:34'),
(9, 'R9', 'NFA Region IX Office', 'active', '2026-05-28 15:19:34'),
(10, 'R5', 'NFA Region V Office', 'active', '2026-05-28 15:19:34'),
(11, 'R6', 'NFA Region VI Office', 'active', '2026-05-28 15:19:34'),
(12, 'R7', 'NFA Region VII Office', 'active', '2026-05-28 15:19:34'),
(13, 'R8', 'NFA Region VIII Office', 'active', '2026-05-28 15:19:34'),
(14, 'R10', 'NFA Region X Office', 'active', '2026-05-28 15:19:34'),
(15, 'R11', 'NFA Region XI Office', 'active', '2026-05-28 15:19:34'),
(16, 'R12', 'NFA Region XII Office', 'active', '2026-05-28 15:19:34');

-- --------------------------------------------------------

--
-- Table structure for table `requester_confirmation_tokens`
--

CREATE TABLE `requester_confirmation_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `requester_confirmation_tokens`
--

INSERT INTO `requester_confirmation_tokens` (`id`, `ticket_id`, `token_hash`, `expires_at`, `used_at`, `created_at`) VALUES
(1, 1, '88f0b5554889475bd7d8e1711b5bcd272e107c617628a0777c060bc60388e464', '2026-07-23 07:41:55', '2026-07-09 07:46:47', '2026-07-09 07:41:55'),
(2, 1, '321793c1b498bc51e511ac67984f00812f61a0d2c1d279aba979e9582d1bc960', '2026-07-23 07:46:47', '2026-07-09 07:49:12', '2026-07-09 07:46:47'),
(3, 1, '5eaaf5a351a977e06ff683b831740f7acec3bc8d27936b8f4186d1ad0138c8d3', '2026-07-23 07:49:12', '2026-07-09 07:50:12', '2026-07-09 07:49:12'),
(4, 3, '76eb112d9e7e3e2f90c628cdd22bac9d9062756a22e59cfc65bd48f6df412669', '2026-07-23 07:54:43', '2026-07-09 07:55:11', '2026-07-09 07:54:43'),
(5, 2, '6314ca7bb869e8a9d91c3fb868b92de7641007848aeff1295b74bf47bcd8c05f', '2026-07-23 08:28:47', '2026-07-09 08:29:13', '2026-07-09 08:28:47');

-- --------------------------------------------------------

--
-- Table structure for table `service_categories`
--

CREATE TABLE `service_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(160) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_categories`
--

INSERT INTO `service_categories` (`id`, `name`, `status`, `created_at`) VALUES
(1, 'Hardware and Maintenance', 'active', '2026-05-28 15:19:34'),
(2, 'Systems and Application', 'active', '2026-05-28 15:19:34'),
(3, 'Cybersecurity and Network Operations', 'active', '2026-07-08 13:46:16');

-- --------------------------------------------------------

--
-- Table structure for table `service_items`
--

CREATE TABLE `service_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `service_category_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(160) NOT NULL,
  `default_priority` enum('Low','Medium','High','Critical') NOT NULL DEFAULT 'Medium',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_items`
--

INSERT INTO `service_items` (`id`, `service_category_id`, `name`, `default_priority`, `status`, `created_at`) VALUES
(1, 1, 'Hardware Certifications', 'Low', 'active', '2026-05-28 15:19:34'),
(2, 1, 'Hardware Troubleshooting and Repair', 'Medium', 'active', '2026-05-28 15:19:34'),
(3, 1, 'ICT Equipment Deployment and Asset Management', 'Medium', 'active', '2026-05-28 15:19:34'),
(4, 1, 'Hardware Installation and Configuration', 'High', 'active', '2026-05-28 15:19:34'),
(5, 1, 'Hardware Upgrade and Replacement', 'Medium', 'active', '2026-05-28 15:19:34'),
(6, 2, 'E-IFOMIS', 'High', 'active', '2026-05-28 15:19:34'),
(7, 2, 'Cash Monitoring', 'High', 'active', '2026-05-28 15:19:34'),
(8, 2, 'Human Resource Information System', 'High', 'active', '2026-05-28 15:19:34'),
(9, 2, 'Payroll', 'High', 'active', '2026-05-28 15:19:34'),
(10, 2, 'Website Posting', 'Medium', 'active', '2026-05-28 15:19:34'),
(11, 2, 'GovMail Support', 'Medium', 'active', '2026-05-28 15:19:34'),
(12, 2, 'Bid Posting', 'Medium', 'active', '2026-05-28 15:19:34'),
(13, 2, 'Digital Signature', 'Medium', 'active', '2026-07-08 13:47:46'),
(14, 2, 'Document Tracking System', 'Medium', 'active', '2026-07-08 13:48:03'),
(15, 2, 'Farmer-Seller Registry', 'Medium', 'active', '2026-07-08 13:48:12'),
(16, 2, 'Property Information System', 'Medium', 'active', '2026-07-08 13:48:53'),
(17, 1, 'Preventive Maintenance and Inspection', 'Medium', 'active', '2026-07-08 13:50:38'),
(18, 1, 'Warranty and Technical Support Services', 'Low', 'active', '2026-07-08 13:50:45'),
(19, 3, 'Network Connectivity Services', 'High', 'active', '2026-07-08 13:51:10'),
(20, 3, 'User Account and Access Management', 'Medium', 'active', '2026-07-08 13:51:14'),
(21, 3, 'Network Security Services', 'Critical', 'active', '2026-07-08 13:51:20'),
(22, 3, 'Cybersecurity Incident Reporting and Response', 'High', 'active', '2026-07-08 13:51:26'),
(23, 3, 'Security Monitoring and Threat Management', 'Critical', 'active', '2026-07-08 13:51:32'),
(24, 3, 'Network Infrastructure Support', 'Medium', 'active', '2026-07-08 13:52:16'),
(25, 3, 'Secure Remote Access Services', 'Medium', 'active', '2026-07-08 13:52:27'),
(26, 3, 'Cybersecurity Compliance and Technical Consultation', 'Medium', 'active', '2026-07-08 13:52:34'),
(27, 3, 'External Network and Vendor Coordination', 'Medium', 'active', '2026-07-08 13:52:38'),
(28, 3, 'Digital Certificate and Secure Communication Services', 'Medium', 'active', '2026-07-08 13:52:49');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(120) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'ict_notification_email', 'ict@nfa.gov.ph', '2026-05-28 15:19:34', NULL),
(2, 'system_public_url', 'https://ebps.nfa.gov.ph/ICTTS/public', '2026-05-28 15:19:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_no` varchar(40) NOT NULL,
  `requested_at` datetime NOT NULL,
  `requester_name` varchar(160) NOT NULL,
  `requester_position` varchar(160) DEFAULT NULL,
  `requester_email` varchar(190) NOT NULL,
  `requester_contact` varchar(50) NOT NULL,
  `region_id` int(10) UNSIGNED NOT NULL,
  `office_id` int(10) UNSIGNED NOT NULL,
  `requested_for` datetime NOT NULL,
  `service_category_id` int(10) UNSIGNED NOT NULL,
  `service_item_id` int(10) UNSIGNED NOT NULL,
  `responsible_group` varchar(160) DEFAULT NULL,
  `description` text NOT NULL,
  `priority` enum('Low','Medium','High','Critical') NOT NULL DEFAULT 'Medium',
  `status` enum('Submitted','Assigned','In Progress','Pending','Completed','Confirmed Completed','Returned for Further Action','Cancelled') NOT NULL DEFAULT 'Submitted',
  `assigned_to` int(10) UNSIGNED DEFAULT NULL,
  `assigned_by` int(10) UNSIGNED DEFAULT NULL,
  `assigned_at` datetime DEFAULT NULL,
  `response_due_at` datetime DEFAULT NULL,
  `resolution_due_at` datetime DEFAULT NULL,
  `first_responded_at` datetime DEFAULT NULL,
  `sla_status` enum('Within SLA','Response Overdue','Resolution Overdue','Met','Breached') NOT NULL DEFAULT 'Within SLA',
  `sla_breached_at` datetime DEFAULT NULL,
  `completed_by_tech_at` datetime DEFAULT NULL,
  `requester_confirmed_at` datetime DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `ticket_no`, `requested_at`, `requester_name`, `requester_position`, `requester_email`, `requester_contact`, `region_id`, `office_id`, `requested_for`, `service_category_id`, `service_item_id`, `responsible_group`, `description`, `priority`, `status`, `assigned_to`, `assigned_by`, `assigned_at`, `response_due_at`, `resolution_due_at`, `first_responded_at`, `sla_status`, `sla_breached_at`, `completed_by_tech_at`, `requester_confirmed_at`, `closed_at`, `created_at`, `updated_at`) VALUES
(1, 'ICTSD-20260708-A54F85', '2026-07-08 15:54:44', 'Mang Juan', 'Clerk', 'rainierjdelacruz@gmail.com', '', 5, 20, '2026-07-08 15:53:00', 2, 8, 'Systems and Application', 'I cant log in', 'High', 'Confirmed Completed', 8, 5, '2026-07-08 16:03:58', '2026-07-08 19:54:44', '2026-07-10 15:54:44', '2026-07-09 07:41:55', 'Breached', '2026-07-09 07:19:10', '2026-07-09 07:41:55', '2026-07-09 07:50:12', '2026-07-09 07:50:12', '2026-07-08 15:54:44', '2026-07-09 07:50:12'),
(2, 'ICTSD-20260709-3476E3', '2026-07-09 07:21:51', 'Rainier Dela Cruz', 'Clerk 4', 'rainierjdelacruz@gmail.com', '', 3, 8, '2026-07-09 07:20:00', 2, 11, 'Systems and Application', 'Request for Govmail Account', 'Medium', 'Confirmed Completed', 7, 5, '2026-07-09 07:23:10', '2026-07-09 15:21:51', '2026-07-12 07:21:51', '2026-07-09 08:28:03', 'Met', NULL, '2026-07-09 08:28:47', '2026-07-09 08:29:13', '2026-07-09 08:29:13', '2026-07-09 07:21:51', '2026-07-09 08:29:13'),
(3, 'ICTSD-20260709-7E87EB', '2026-07-09 07:52:21', 'Rainier Dela Cruz', 'Clerk 5', 'rainierjdelacruz@gmail.com', '', 3, 12, '2026-07-09 07:51:00', 2, 6, 'Systems and Application', 'Cannot access', 'High', 'Confirmed Completed', 8, 5, '2026-07-09 07:53:48', '2026-07-09 11:52:21', '2026-07-11 07:52:21', '2026-07-09 07:54:20', 'Met', NULL, '2026-07-09 07:54:43', '2026-07-09 07:55:11', '2026-07-09 07:55:11', '2026-07-09 07:52:21', '2026-07-09 07:55:11');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_assignees`
--

CREATE TABLE `ticket_assignees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `assignment_role` enum('primary','supporting') NOT NULL DEFAULT 'supporting',
  `assigned_by` int(10) UNSIGNED NOT NULL,
  `assigned_at` datetime NOT NULL DEFAULT current_timestamp(),
  `removed_at` datetime DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ticket_assignees`
--

INSERT INTO `ticket_assignees` (`id`, `ticket_id`, `user_id`, `assignment_role`, `assigned_by`, `assigned_at`, `removed_at`, `notes`) VALUES
(1, 1, 8, 'primary', 5, '2026-07-08 16:03:58', NULL, 'Take charge'),
(2, 2, 7, 'primary', 5, '2026-07-09 07:23:10', NULL, NULL),
(3, 3, 8, 'primary', 5, '2026-07-09 07:53:48', NULL, 'ulit ulit');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_assignments`
--

CREATE TABLE `ticket_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `previous_assignee` int(10) UNSIGNED DEFAULT NULL,
  `assigned_to` int(10) UNSIGNED NOT NULL,
  `assigned_by` int(10) UNSIGNED NOT NULL,
  `assignment_role` enum('primary','supporting') NOT NULL DEFAULT 'primary',
  `action` enum('assign','reassign','add_support','remove_support') NOT NULL DEFAULT 'assign',
  `assigned_at` datetime NOT NULL DEFAULT current_timestamp(),
  `notes` varchar(500) DEFAULT NULL,
  `reason` varchar(700) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ticket_assignments`
--

INSERT INTO `ticket_assignments` (`id`, `ticket_id`, `previous_assignee`, `assigned_to`, `assigned_by`, `assignment_role`, `action`, `assigned_at`, `notes`, `reason`) VALUES
(1, 1, NULL, 8, 5, 'primary', 'assign', '2026-07-08 16:03:58', 'Take charge', NULL),
(2, 2, NULL, 7, 5, 'primary', 'assign', '2026-07-09 07:23:10', NULL, NULL),
(3, 3, NULL, 8, 5, 'primary', 'assign', '2026-07-09 07:53:48', 'ulit ulit', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ticket_attachments`
--

CREATE TABLE `ticket_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `uploaded_by` int(10) UNSIGNED DEFAULT NULL,
  `uploaded_by_name` varchar(160) DEFAULT NULL,
  `source` enum('requester','technical','manager','admin') NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `mime_type` varchar(120) NOT NULL,
  `file_size` bigint(20) UNSIGNED NOT NULL,
  `remarks` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_endorsements`
--

CREATE TABLE `ticket_endorsements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `from_group` varchar(160) DEFAULT NULL,
  `to_group` varchar(160) NOT NULL,
  `old_service_category_id` int(10) UNSIGNED DEFAULT NULL,
  `new_service_category_id` int(10) UNSIGNED DEFAULT NULL,
  `old_service_item_id` int(10) UNSIGNED DEFAULT NULL,
  `new_service_item_id` int(10) UNSIGNED DEFAULT NULL,
  `endorsed_by` int(10) UNSIGNED NOT NULL,
  `reason` varchar(700) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_escalations`
--

CREATE TABLE `ticket_escalations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `escalation_type` enum('response_overdue','resolution_overdue','manual') NOT NULL,
  `escalated_to_role` varchar(80) NOT NULL,
  `escalated_to_user` int(10) UNSIGNED DEFAULT NULL,
  `escalated_by` int(10) UNSIGNED DEFAULT NULL,
  `reason` varchar(700) NOT NULL,
  `notice_key` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_feedback`
--

CREATE TABLE `ticket_feedback` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED DEFAULT NULL,
  `resolved_yes_no` enum('yes','no') NOT NULL,
  `feedback_comments` text DEFAULT NULL,
  `submitted_by_name` varchar(160) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ticket_feedback`
--

INSERT INTO `ticket_feedback` (`id`, `ticket_id`, `rating`, `resolved_yes_no`, `feedback_comments`, `submitted_by_name`, `created_at`) VALUES
(1, 1, 5, 'yes', NULL, 'Mang Juan', '2026-07-09 07:50:12'),
(2, 3, 5, 'yes', NULL, 'Rainier Dela Cruz', '2026-07-09 07:55:11'),
(3, 2, 5, 'yes', NULL, 'Rainier Dela Cruz', '2026-07-09 08:29:13');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_reopen_logs`
--

CREATE TABLE `ticket_reopen_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `old_status` varchar(80) NOT NULL,
  `new_status` varchar(80) NOT NULL,
  `reopened_by` int(10) UNSIGNED DEFAULT NULL,
  `reopened_by_name` varchar(160) DEFAULT NULL,
  `reason` varchar(700) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_status_logs`
--

CREATE TABLE `ticket_status_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `old_status` varchar(80) DEFAULT NULL,
  `new_status` varchar(80) NOT NULL,
  `changed_by` int(10) UNSIGNED DEFAULT NULL,
  `changed_by_name` varchar(160) DEFAULT NULL,
  `remarks` varchar(700) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ticket_status_logs`
--

INSERT INTO `ticket_status_logs` (`id`, `ticket_id`, `old_status`, `new_status`, `changed_by`, `changed_by_name`, `remarks`, `created_at`) VALUES
(1, 1, NULL, 'Submitted', NULL, 'Public Requester', 'Ticket submitted.', '2026-07-08 15:54:44'),
(2, 1, 'Submitted', 'Assigned', 5, NULL, 'Ticket assigned.', '2026-07-08 16:03:58'),
(3, 2, NULL, 'Submitted', NULL, 'Public Requester', 'Ticket submitted.', '2026-07-09 07:21:51'),
(4, 2, 'Submitted', 'Assigned', 5, NULL, 'Ticket assigned.', '2026-07-09 07:23:10'),
(5, 1, 'Assigned', 'Completed', 8, NULL, NULL, '2026-07-09 07:41:55'),
(6, 1, 'Completed', 'Confirmed Completed', NULL, 'Public Requester', 'Requester confirmed completion.', '2026-07-09 07:50:12'),
(7, 3, NULL, 'Submitted', NULL, 'Public Requester', 'Ticket submitted.', '2026-07-09 07:52:21'),
(8, 3, 'Submitted', 'Assigned', 5, NULL, 'Ticket assigned.', '2026-07-09 07:53:48'),
(9, 3, 'Assigned', 'In Progress', 8, NULL, NULL, '2026-07-09 07:54:20'),
(10, 3, 'In Progress', 'Completed', 8, NULL, NULL, '2026-07-09 07:54:43'),
(11, 3, 'Completed', 'Confirmed Completed', NULL, 'Public Requester', 'Requester confirmed completion.', '2026-07-09 07:55:11'),
(12, 2, 'Assigned', 'In Progress', 7, NULL, NULL, '2026-07-09 08:28:03'),
(13, 2, 'In Progress', 'Completed', 7, NULL, NULL, '2026-07-09 08:28:47'),
(14, 2, 'Completed', 'Confirmed Completed', NULL, 'Public Requester', 'Requester confirmed completion.', '2026-07-09 08:29:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_number` varchar(50) NOT NULL,
  `name` varchar(160) NOT NULL,
  `position` varchar(160) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('technical','unit_head','division_chief','admin') NOT NULL DEFAULT 'technical',
  `service_category_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `id_number`, `name`, `position`, `email`, `password_hash`, `role`, `service_category_id`, `status`, `last_login_at`, `created_at`, `updated_at`) VALUES
(4, 'ADMIN-001', 'Administrator', 'System Administrator', 'tech.support@nfa.gov.ph', '$2y$10$.KtczS.hnkBVoi0j4GlBseBaVOLGPX5lohc5TeSuT3QcCfz1Gy7ym', 'admin', NULL, 'active', '2026-07-13 16:03:38', '2026-05-28 15:19:34', '2026-07-13 16:03:38'),
(5, '939908', 'Rainier John Dela Cruz', 'Information Systems Analysts III', 'rainier.delacruz@nfa.gov.ph', '$2y$10$PYilkvPOrO9EU1a/c4tp8u5AIWNr7TgcVBmpXBbj2fUS8bpMZm1c2', 'unit_head', 2, 'active', '2026-07-13 14:51:50', '2026-07-08 09:54:22', '2026-07-13 14:51:50'),
(6, '946097', 'Christian Abrera', 'Computer Programmer III', 'christian.abrera@nfa.gov.ph', '$2y$10$CyzkbNAtH/Qi1PFd.RXu/uoUPhOSruOaDppnY9o7eafT5lNooadQG', 'technical', 2, 'active', '2026-07-09 11:54:35', '2026-07-08 13:56:43', '2026-07-09 11:54:35'),
(7, '940640', 'Paulo Anthony A. Jacinto', 'Computer Programmer II', 'paulo.jacinto@nfa.gov.ph', '$2y$10$Mrt3FaMb7UEjUXp9DH8SBuUMZsuypfiCOmPNNHEwk2qcedvJzhcpC', 'technical', 2, 'active', '2026-07-15 08:17:48', '2026-07-08 13:57:30', '2026-07-15 08:17:48'),
(8, '934365', 'Boots B. Torres', 'Information Systems Analyst II', 'boots.torres@nfa.gov.ph', '$2y$10$a5cLjrbPvcmX2QSgylDmbunx6c5Il.kCTs3T1Ljj7b/8iMCBlx85.', 'technical', 2, 'active', '2026-07-09 07:41:26', '2026-07-08 14:03:31', '2026-07-09 07:41:26'),
(9, '129817', 'Eric', 'Information Technology Officer II', 'eric.aguirre@nfa.gov.ph', '$2y$10$oBYMk.Q/QUmTaMdZ.zyHE.In4R83.uC5hg5d/9p/FrPOl7gk00ZoO', 'division_chief', NULL, 'active', NULL, '2026-07-08 14:07:45', '2026-07-08 14:30:29'),
(10, '945407', 'Jay Jason M. Peñas', 'Computer Maintenance Technologies I', 'jayjason.penas@nfa.gov.ph', '$2y$10$aDq6CflqfiuqXb8KadirguxEK.BzILrkMPEt.zZzHsqe50xIa9N4K', 'technical', 3, 'active', NULL, '2026-07-08 14:14:34', '2026-07-08 14:30:15'),
(11, '936838', 'Rommel Siscar', 'INFORMATION SYSTEMS ANALYST II', 'rommel.siscar@nfa.gov.ph', '$2y$10$fty5iADznR7JxVbH.l6nIe7ziC0zUK1PPswg68D/16RlvGW9qAiWO', 'technical', 3, 'active', NULL, '2026-07-08 14:38:35', '2026-07-08 15:26:42'),
(12, '942515', 'Mark Edison N.Quiddam', 'Computer Maintenance Technologist II', 'markedison.quiddam@nfa.gov.ph', '$2y$10$mYssH3duSbbuyAkj6AY6KuA7WbMBTuDxnDahom4iWUhg2ACIZpoE2', 'technical', 1, 'active', '2026-07-14 15:18:36', '2026-07-08 15:12:48', '2026-07-14 15:18:36'),
(13, '945401', 'Jewell Mayrena', 'Computer Maintenance Technologist I', 'jewell.mayrena@nfa.gov.ph', '$2y$10$2KMDYRwq.HWHDm3U/DOxMe3rRRraI4ZAQW1SSg4ti5qPBBnlkFJxe', 'technical', 1, 'active', NULL, '2026-07-09 10:26:00', '2026-07-09 13:44:11'),
(14, '943892', 'Henri Jasper Salibad', 'Computer Maintenance Technologist II', 'henrijasper.salibad@nfa.gov.ph', '$2y$10$lhpFeNPtVRI/349FEmVVjuv15oJapPQSZTMseKsE1Zsm.XUUf5dli', 'technical', 1, 'active', NULL, '2026-07-09 17:33:48', '2026-07-13 14:45:57'),
(15, '945569', 'Gary L Riparip', 'Chief Information Technology Officer', 'gary.riparip@nfa.gov.ph', '$2y$10$9Gcgb17xOUcVQO.wRPP/VOF1HJQ2FDpGuRrGFeCZGm3xJikkJmBau', 'division_chief', NULL, 'active', NULL, '2026-07-13 14:51:40', '2026-07-13 14:55:14'),
(16, '146761', 'Armin V. Jazmines', 'CMT III', 'armin.jazmines@nfa.gov.ph', '$2y$10$CEO79FFzOYlckoFJzosS6.TTP7SIeC2uPt.JL4IOrl7gAoyaTE4ky', 'unit_head', 1, 'active', '2026-07-14 15:10:26', '2026-07-13 15:47:51', '2026-07-14 15:10:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_action_idx` (`action`),
  ADD KEY `activity_created_idx` (`created_at`),
  ADD KEY `activity_logs_user_fk` (`user_id`);

--
-- Indexes for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_logs_ticket_fk` (`ticket_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_read_idx` (`user_id`,`read_at`),
  ADD KEY `notifications_created_idx` (`created_at`);

--
-- Indexes for table `offices`
--
ALTER TABLE `offices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `office_unique` (`region_id`,`name`);

--
-- Indexes for table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `requester_confirmation_tokens`
--
ALTER TABLE `requester_confirmation_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token_hash` (`token_hash`),
  ADD KEY `confirmation_tokens_ticket_fk` (`ticket_id`);

--
-- Indexes for table `service_categories`
--
ALTER TABLE `service_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `service_items`
--
ALTER TABLE `service_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_item_unique` (`service_category_id`,`name`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_no` (`ticket_no`),
  ADD KEY `tickets_status_idx` (`status`),
  ADD KEY `tickets_priority_idx` (`priority`),
  ADD KEY `tickets_sla_status_idx` (`sla_status`),
  ADD KEY `tickets_resolution_due_idx` (`resolution_due_at`),
  ADD KEY `tickets_requested_at_idx` (`requested_at`),
  ADD KEY `tickets_assigned_to_idx` (`assigned_to`),
  ADD KEY `tickets_region_fk` (`region_id`),
  ADD KEY `tickets_office_fk` (`office_id`),
  ADD KEY `tickets_category_fk` (`service_category_id`),
  ADD KEY `tickets_item_fk` (`service_item_id`),
  ADD KEY `tickets_assigned_by_fk` (`assigned_by`);

--
-- Indexes for table `ticket_assignees`
--
ALTER TABLE `ticket_assignees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_assignees_ticket_idx` (`ticket_id`,`removed_at`),
  ADD KEY `ticket_assignees_user_idx` (`user_id`,`removed_at`),
  ADD KEY `ticket_assignees_by_fk` (`assigned_by`);

--
-- Indexes for table `ticket_assignments`
--
ALTER TABLE `ticket_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_assignments_ticket_fk` (`ticket_id`),
  ADD KEY `ticket_assignments_previous_fk` (`previous_assignee`),
  ADD KEY `ticket_assignments_to_fk` (`assigned_to`),
  ADD KEY `ticket_assignments_by_fk` (`assigned_by`);

--
-- Indexes for table `ticket_attachments`
--
ALTER TABLE `ticket_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_attachments_ticket_idx` (`ticket_id`),
  ADD KEY `ticket_attachments_user_fk` (`uploaded_by`);

--
-- Indexes for table `ticket_endorsements`
--
ALTER TABLE `ticket_endorsements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_endorsements_ticket_idx` (`ticket_id`),
  ADD KEY `ticket_endorsements_old_category_fk` (`old_service_category_id`),
  ADD KEY `ticket_endorsements_new_category_fk` (`new_service_category_id`),
  ADD KEY `ticket_endorsements_old_item_fk` (`old_service_item_id`),
  ADD KEY `ticket_endorsements_new_item_fk` (`new_service_item_id`),
  ADD KEY `ticket_endorsements_by_fk` (`endorsed_by`);

--
-- Indexes for table `ticket_escalations`
--
ALTER TABLE `ticket_escalations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_escalations_notice_unique` (`ticket_id`,`notice_key`),
  ADD KEY `ticket_escalations_ticket_idx` (`ticket_id`),
  ADD KEY `ticket_escalations_to_user_fk` (`escalated_to_user`),
  ADD KEY `ticket_escalations_by_fk` (`escalated_by`);

--
-- Indexes for table `ticket_feedback`
--
ALTER TABLE `ticket_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_feedback_ticket_idx` (`ticket_id`);

--
-- Indexes for table `ticket_reopen_logs`
--
ALTER TABLE `ticket_reopen_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_reopen_logs_ticket_idx` (`ticket_id`),
  ADD KEY `ticket_reopen_logs_user_fk` (`reopened_by`);

--
-- Indexes for table `ticket_status_logs`
--
ALTER TABLE `ticket_status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_status_logs_ticket_fk` (`ticket_id`),
  ADD KEY `ticket_status_logs_user_fk` (`changed_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_number` (`id_number`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `users_service_category_idx` (`service_category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `offices`
--
ALTER TABLE `offices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `regions`
--
ALTER TABLE `regions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `requester_confirmation_tokens`
--
ALTER TABLE `requester_confirmation_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `service_categories`
--
ALTER TABLE `service_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `service_items`
--
ALTER TABLE `service_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ticket_assignees`
--
ALTER TABLE `ticket_assignees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ticket_assignments`
--
ALTER TABLE `ticket_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ticket_attachments`
--
ALTER TABLE `ticket_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_endorsements`
--
ALTER TABLE `ticket_endorsements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_escalations`
--
ALTER TABLE `ticket_escalations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_feedback`
--
ALTER TABLE `ticket_feedback`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ticket_reopen_logs`
--
ALTER TABLE `ticket_reopen_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_status_logs`
--
ALTER TABLE `ticket_status_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD CONSTRAINT `email_logs_ticket_fk` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `offices`
--
ALTER TABLE `offices`
  ADD CONSTRAINT `offices_region_fk` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`);

--
-- Constraints for table `requester_confirmation_tokens`
--
ALTER TABLE `requester_confirmation_tokens`
  ADD CONSTRAINT `confirmation_tokens_ticket_fk` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_items`
--
ALTER TABLE `service_items`
  ADD CONSTRAINT `service_items_category_fk` FOREIGN KEY (`service_category_id`) REFERENCES `service_categories` (`id`);

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_assigned_by_fk` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tickets_assigned_to_fk` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tickets_category_fk` FOREIGN KEY (`service_category_id`) REFERENCES `service_categories` (`id`),
  ADD CONSTRAINT `tickets_item_fk` FOREIGN KEY (`service_item_id`) REFERENCES `service_items` (`id`),
  ADD CONSTRAINT `tickets_office_fk` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`),
  ADD CONSTRAINT `tickets_region_fk` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`);

--
-- Constraints for table `ticket_assignees`
--
ALTER TABLE `ticket_assignees`
  ADD CONSTRAINT `ticket_assignees_by_fk` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ticket_assignees_ticket_fk` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_assignees_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `ticket_assignments`
--
ALTER TABLE `ticket_assignments`
  ADD CONSTRAINT `ticket_assignments_by_fk` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ticket_assignments_previous_fk` FOREIGN KEY (`previous_assignee`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ticket_assignments_ticket_fk` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_assignments_to_fk` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`);

--
-- Constraints for table `ticket_attachments`
--
ALTER TABLE `ticket_attachments`
  ADD CONSTRAINT `ticket_attachments_ticket_fk` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_attachments_user_fk` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `ticket_endorsements`
--
ALTER TABLE `ticket_endorsements`
  ADD CONSTRAINT `ticket_endorsements_by_fk` FOREIGN KEY (`endorsed_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ticket_endorsements_new_category_fk` FOREIGN KEY (`new_service_category_id`) REFERENCES `service_categories` (`id`),
  ADD CONSTRAINT `ticket_endorsements_new_item_fk` FOREIGN KEY (`new_service_item_id`) REFERENCES `service_items` (`id`),
  ADD CONSTRAINT `ticket_endorsements_old_category_fk` FOREIGN KEY (`old_service_category_id`) REFERENCES `service_categories` (`id`),
  ADD CONSTRAINT `ticket_endorsements_old_item_fk` FOREIGN KEY (`old_service_item_id`) REFERENCES `service_items` (`id`),
  ADD CONSTRAINT `ticket_endorsements_ticket_fk` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ticket_escalations`
--
ALTER TABLE `ticket_escalations`
  ADD CONSTRAINT `ticket_escalations_by_fk` FOREIGN KEY (`escalated_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ticket_escalations_ticket_fk` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_escalations_to_user_fk` FOREIGN KEY (`escalated_to_user`) REFERENCES `users` (`id`);

--
-- Constraints for table `ticket_feedback`
--
ALTER TABLE `ticket_feedback`
  ADD CONSTRAINT `ticket_feedback_ticket_fk` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ticket_reopen_logs`
--
ALTER TABLE `ticket_reopen_logs`
  ADD CONSTRAINT `ticket_reopen_logs_ticket_fk` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_reopen_logs_user_fk` FOREIGN KEY (`reopened_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `ticket_status_logs`
--
ALTER TABLE `ticket_status_logs`
  ADD CONSTRAINT `ticket_status_logs_ticket_fk` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_status_logs_user_fk` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_service_category_fk` FOREIGN KEY (`service_category_id`) REFERENCES `service_categories` (`id`);
--
-- Database: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Table structure for table `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Table structure for table `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Table structure for table `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Table structure for table `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

--
-- Dumping data for table `pma__export_templates`
--

INSERT INTO `pma__export_templates` (`id`, `username`, `export_type`, `template_name`, `template_data`) VALUES
(1, 'root', 'database', 'bps', '{\"quick_or_custom\":\"quick\",\"what\":\"sql\",\"structure_or_data_forced\":\"0\",\"table_select[]\":[\"abstract_of_quotations\",\"awards\",\"bid_notices\",\"canvasses\",\"contracts\",\"contract_or_purchase_orders\",\"email_change_requests\",\"notices_to_proceed\",\"parent_procurement\",\"procurement_activity_logs\",\"resolutions\",\"rfqs\",\"supplemental_bid_bulletins\",\"svp_awards\",\"svp_contracts\",\"svp_evaluations\",\"svp_evaluation_items\",\"svp_ntps\",\"svp_quotations\",\"svp_rfqs\",\"svp_rfq_postings\",\"svp_suppliers\",\"users\"],\"table_structure[]\":[\"abstract_of_quotations\",\"awards\",\"bid_notices\",\"canvasses\",\"contracts\",\"contract_or_purchase_orders\",\"email_change_requests\",\"notices_to_proceed\",\"parent_procurement\",\"procurement_activity_logs\",\"resolutions\",\"rfqs\",\"supplemental_bid_bulletins\",\"svp_awards\",\"svp_contracts\",\"svp_evaluations\",\"svp_evaluation_items\",\"svp_ntps\",\"svp_quotations\",\"svp_rfqs\",\"svp_rfq_postings\",\"svp_suppliers\",\"users\"],\"table_data[]\":[\"abstract_of_quotations\",\"awards\",\"bid_notices\",\"canvasses\",\"contracts\",\"contract_or_purchase_orders\",\"email_change_requests\",\"notices_to_proceed\",\"parent_procurement\",\"procurement_activity_logs\",\"resolutions\",\"rfqs\",\"supplemental_bid_bulletins\",\"svp_awards\",\"svp_contracts\",\"svp_evaluations\",\"svp_evaluation_items\",\"svp_ntps\",\"svp_quotations\",\"svp_rfqs\",\"svp_rfq_postings\",\"svp_suppliers\",\"users\"],\"aliases_new\":\"\",\"output_format\":\"sendit\",\"filename_template\":\"@DATABASE@\",\"remember_template\":\"on\",\"charset\":\"utf-8\",\"compression\":\"none\",\"maxsize\":\"\",\"codegen_structure_or_data\":\"data\",\"codegen_format\":\"0\",\"csv_separator\":\",\",\"csv_enclosed\":\"\\\"\",\"csv_escaped\":\"\\\"\",\"csv_terminated\":\"AUTO\",\"csv_null\":\"NULL\",\"csv_columns\":\"something\",\"csv_structure_or_data\":\"data\",\"excel_null\":\"NULL\",\"excel_columns\":\"something\",\"excel_edition\":\"win\",\"excel_structure_or_data\":\"data\",\"json_structure_or_data\":\"data\",\"json_unicode\":\"something\",\"latex_caption\":\"something\",\"latex_structure_or_data\":\"structure_and_data\",\"latex_structure_caption\":\"Structure of table @TABLE@\",\"latex_structure_continued_caption\":\"Structure of table @TABLE@ (continued)\",\"latex_structure_label\":\"tab:@TABLE@-structure\",\"latex_relation\":\"something\",\"latex_comments\":\"something\",\"latex_mime\":\"something\",\"latex_columns\":\"something\",\"latex_data_caption\":\"Content of table @TABLE@\",\"latex_data_continued_caption\":\"Content of table @TABLE@ (continued)\",\"latex_data_label\":\"tab:@TABLE@-data\",\"latex_null\":\"\\\\textit{NULL}\",\"mediawiki_structure_or_data\":\"structure_and_data\",\"mediawiki_caption\":\"something\",\"mediawiki_headers\":\"something\",\"htmlword_structure_or_data\":\"structure_and_data\",\"htmlword_null\":\"NULL\",\"ods_null\":\"NULL\",\"ods_structure_or_data\":\"data\",\"odt_structure_or_data\":\"structure_and_data\",\"odt_relation\":\"something\",\"odt_comments\":\"something\",\"odt_mime\":\"something\",\"odt_columns\":\"something\",\"odt_null\":\"NULL\",\"pdf_report_title\":\"\",\"pdf_structure_or_data\":\"structure_and_data\",\"phparray_structure_or_data\":\"data\",\"sql_include_comments\":\"something\",\"sql_header_comment\":\"\",\"sql_use_transaction\":\"something\",\"sql_compatibility\":\"NONE\",\"sql_structure_or_data\":\"structure_and_data\",\"sql_create_table\":\"something\",\"sql_auto_increment\":\"something\",\"sql_create_view\":\"something\",\"sql_procedure_function\":\"something\",\"sql_create_trigger\":\"something\",\"sql_backquotes\":\"something\",\"sql_type\":\"INSERT\",\"sql_insert_syntax\":\"both\",\"sql_max_query_size\":\"50000\",\"sql_hex_for_binary\":\"something\",\"sql_utc_time\":\"something\",\"texytext_structure_or_data\":\"structure_and_data\",\"texytext_null\":\"NULL\",\"xml_structure_or_data\":\"data\",\"xml_export_events\":\"something\",\"xml_export_functions\":\"something\",\"xml_export_procedures\":\"something\",\"xml_export_tables\":\"something\",\"xml_export_triggers\":\"something\",\"xml_export_views\":\"something\",\"xml_export_contents\":\"something\",\"yaml_structure_or_data\":\"data\",\"\":null,\"lock_tables\":null,\"as_separate_files\":null,\"csv_removeCRLF\":null,\"excel_removeCRLF\":null,\"json_pretty_print\":null,\"htmlword_columns\":null,\"ods_columns\":null,\"sql_dates\":null,\"sql_relation\":null,\"sql_mime\":null,\"sql_disable_fk\":null,\"sql_views_as_tables\":null,\"sql_metadata\":null,\"sql_create_database\":null,\"sql_drop_table\":null,\"sql_if_not_exists\":null,\"sql_simple_view_export\":null,\"sql_view_current_user\":null,\"sql_or_replace_view\":null,\"sql_truncate\":null,\"sql_delayed\":null,\"sql_ignore\":null,\"texytext_columns\":null}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Table structure for table `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Table structure for table `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- Dumping data for table `pma__recent`
--

INSERT INTO `pma__recent` (`username`, `tables`) VALUES
('root', '[{\"db\":\"bps\",\"table\":\"users\"},{\"db\":\"bps\",\"table\":\"bid_notices\"},{\"db\":\"ictts\",\"table\":\"users\"},{\"db\":\"ictts\",\"table\":\"email_logs\"},{\"db\":\"ictts\",\"table\":\"activity_logs\"},{\"db\":\"ictts\",\"table\":\"tickets\"},{\"db\":\"ictts\",\"table\":\"service_categories\"},{\"db\":\"bps\",\"table\":\"parent_procurement\"},{\"db\":\"bps\",\"table\":\"supplemental_bid_bulletins\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Table structure for table `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- Table structure for table `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Dumping data for table `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2026-08-03 23:56:30', '{\"Console\\/Mode\":\"collapse\"}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Table structure for table `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Indexes for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Indexes for table `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Indexes for table `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Indexes for table `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Indexes for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Indexes for table `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Indexes for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Indexes for table `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Indexes for table `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Indexes for table `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Indexes for table `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Indexes for table `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Indexes for table `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Database: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
