-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2026 at 07:00 AM
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
(2, 'sysadmin', 'System', NULL, 'Administrator', 'Central Office', 'Administrative and General Services Department', '$2y$10$XgllwcfsWZj7qOq5SghaF.N7AanuAVD4ex/YhdZFNa2inp5I4./6y', 'admin', 'system.admin@nfa.gov.ph', NULL, NULL, NULL, 1, 1, '2026-04-09 00:33:32', '2026-04-09 00:33:32'),
(3, 'SVProcurement', 'Jason', 'S', 'Cañares', 'Central Office', 'Administrative and General Services Department', '$2y$10$MqOJC5nklT4CEGElvMrEKeMF.y0SchG16xNE1HND1lE5fF276xtOi', 'author', 'agsd.purchasing@nfa.gov.ph', NULL, NULL, NULL, 1, 1, '2026-04-10 05:10:07', '2026-04-10 09:40:49'),
(4, 'aacarillo', 'Angelo', 'A', 'Carillo', 'Central Office', 'Administrative and General Services Department', '$2y$10$ukahBZM1v77YScrR.OVMH.dVkKgMMdgrf5gMw2XUALwMto2sgAWkS', 'author', 'bac@nfa.gov.ph', NULL, NULL, NULL, 1, 1, '2026-04-10 05:34:30', '2026-04-10 08:13:08');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
