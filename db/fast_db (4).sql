-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2026 at 09:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.5.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fast_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `activity` varchar(255) NOT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `activity`, `old_value`, `new_value`, `ip_address`, `created_at`) VALUES
(1, 1, 'User login successful', NULL, NULL, '::1', '2026-05-28 12:32:04'),
(2, 1, 'User login successful', NULL, NULL, '::1', '2026-05-28 12:32:21'),
(3, 1, 'User logout successful', NULL, NULL, '::1', '2026-05-28 12:33:17'),
(4, 1, 'User login successful', NULL, NULL, '::1', '2026-05-28 12:33:39'),
(5, 1, 'User login successful', NULL, NULL, '::1', '2026-05-28 12:45:00'),
(6, 1, 'User login successful', NULL, NULL, '::1', '2026-05-28 12:47:26'),
(7, 1, 'User logout successful', NULL, NULL, '::1', '2026-05-28 13:06:21'),
(8, 1, 'User login successful', NULL, NULL, '::1', '2026-05-28 13:15:33'),
(9, 1, 'User logout successful', NULL, NULL, '::1', '2026-05-28 13:16:00'),
(10, 1, 'User login successful', NULL, NULL, '::1', '2026-05-28 13:16:05'),
(11, 1, 'User login successful', NULL, NULL, '::1', '2026-05-28 13:16:29'),
(12, 1, 'User logout successful', NULL, NULL, '::1', '2026-05-28 13:20:50'),
(13, 1, 'User login successful', NULL, NULL, '::1', '2026-05-28 13:20:54'),
(14, 1, 'Updated profile details for user: staff', '{\"full_name\":\"Accounting Staff Member\",\"email\":\"staff@fast.sdo.gov.ph\",\"position\":\"Accountant\"}', '{\"full_name\":\"Accountant\",\"email\":\"staff@fast.sdo.gov.ph\",\"position\":\"Accountant\"}', '::1', '2026-05-28 13:23:48'),
(15, 1, 'Updated profile details for user: requestor', '{\"full_name\":\"Requestor User\",\"email\":\"requestor@fast.sdo.gov.ph\",\"position\":\"Personnel\"}', '{\"full_name\":\"BAC Requestor\",\"email\":\"requestor@fast.sdo.gov.ph\",\"position\":\"Personnel\"}', '::1', '2026-05-28 13:24:02'),
(16, 1, 'Updated profile details and role for user: approver', '{\"full_name\":\"Financial Approver\",\"email\":\"approver@fast.sdo.gov.ph\",\"position\":\"Personnel\",\"role\":\"User\"}', '{\"full_name\":\"Accounting Support\",\"email\":\"approver@fast.sdo.gov.ph\",\"position\":\"Personnel\",\"role\":\"User\"}', '::1', '2026-05-28 13:25:11'),
(17, 1, 'Created user account: SDS (sds@gmail.com)', 'null', '{\"full_name\":\"Phillip B. Gallendez\",\"position\":\"SDS\",\"auto_role\":\"Admin\"}', '::1', '2026-05-28 13:26:00'),
(18, 1, 'Created user account: ASDS (asds@gmail.com)', 'null', '{\"full_name\":\"Joe Bren L. Consuelo\",\"position\":\"ASDS\",\"auto_role\":\"Admin\"}', '::1', '2026-05-28 13:26:33'),
(19, 1, 'Updated profile details and role for user: approver', '{\"full_name\":\"Accounting Support\",\"email\":\"approver@fast.sdo.gov.ph\",\"position\":\"Personnel\",\"role\":\"User\"}', '{\"full_name\":\"Accounting Support\",\"email\":\"approver@fast.sdo.gov.ph\",\"position\":\"Personnel\",\"role\":\"Admin\"}', '::1', '2026-05-28 13:26:47'),
(20, 1, 'Updated profile details and role for user: approver', '{\"full_name\":\"Accounting Support\",\"email\":\"approver@fast.sdo.gov.ph\",\"position\":\"Personnel\",\"role\":\"Admin\"}', '{\"full_name\":\"Accounting Support\",\"email\":\"approver@fast.sdo.gov.ph\",\"position\":\"Accounting Support\",\"role\":\"Admin\"}', '::1', '2026-05-28 13:27:00'),
(21, 1, 'Updated profile details and role for user: Accounting support', '{\"full_name\":\"Accounting Support\",\"email\":\"approver@fast.sdo.gov.ph\",\"position\":\"Accounting Support\",\"role\":\"Admin\"}', '{\"full_name\":\"Accounting Support\",\"email\":\"approver@fast.sdo.gov.ph\",\"position\":\"Accounting Support\",\"role\":\"Admin\"}', '::1', '2026-05-28 13:27:20'),
(22, 1, 'Changed account status: User #4', '{\"status\":\"active\"}', '{\"status\":\"inactive\"}', '::1', '2026-05-28 13:27:40'),
(23, 1, 'Deleted user account: Accounting support (approver@fast.sdo.gov.ph)', '{\"full_name\":\"Accounting Support\"}', 'null', '::1', '2026-05-28 13:30:42'),
(24, 1, 'Updated profile details and role for user: BAC requestor', '{\"full_name\":\"BAC Requestor\",\"email\":\"requestor@fast.sdo.gov.ph\",\"position\":\"Personnel\",\"role\":\"User\"}', '{\"full_name\":\"BAC Requestor\",\"email\":\"requestor@fast.sdo.gov.ph\",\"position\":\"Personnel\",\"role\":\"User\"}', '::1', '2026-05-28 13:31:02'),
(25, 1, 'Updated profile details and role for user: Accountant', '{\"full_name\":\"Accountant\",\"email\":\"staff@fast.sdo.gov.ph\",\"position\":\"Accountant\",\"role\":\"Admin\"}', '{\"full_name\":\"Accountant\",\"email\":\"staff@fast.sdo.gov.ph\",\"position\":\"Accountant\",\"role\":\"Admin\"}', '::1', '2026-05-28 13:31:17'),
(26, 1, 'User logout successful', NULL, NULL, '::1', '2026-05-28 13:32:48'),
(27, 1, 'User login successful', NULL, NULL, '::1', '2026-05-28 13:33:33'),
(28, 1, 'Updated profile details and role for user: Superadmin', '{\"full_name\":\"FAST Super Admin\",\"email\":\"fastsdo@gmail.com\",\"position\":\"Super Admin\",\"role\":\"Super Admin\"}', '{\"full_name\":\"FAST Super Admin\",\"email\":\"fastsdo@gmail.com\",\"position\":\"Super Admin\",\"role\":\"Super Admin\"}', '::1', '2026-05-28 13:33:52'),
(29, 1, 'Created user account: Accounting support (acctgsupp@gmail.com)', 'null', '{\"full_name\":\"Accounting Support\",\"position\":\"Accounting Support\",\"auto_role\":\"Admin\"}', '::1', '2026-05-28 13:34:51'),
(30, 1, 'Updated profile details and role for user: Accountant', '{\"full_name\":\"Accountant\",\"email\":\"staff@fast.sdo.gov.ph\",\"position\":\"Accountant\",\"role\":\"Admin\"}', '{\"full_name\":\"Accountant\",\"email\":\"accountant@gmail.com\",\"position\":\"Accountant\",\"role\":\"Admin\"}', '::1', '2026-05-28 13:35:17'),
(31, 1, 'Updated profile details and role for user: budget', '{\"full_name\":\"Budget Officer\",\"email\":\"budget@fast.sdo.gov.ph\",\"position\":\"Budget Officer\",\"role\":\"Admin\"}', '{\"full_name\":\"Budget Officer\",\"email\":\"budget@gmail.com\",\"position\":\"Budget Officer\",\"role\":\"Admin\"}', '::1', '2026-05-28 13:35:27'),
(32, 1, 'Updated profile details and role for user: BAC requestor', '{\"full_name\":\"BAC Requestor\",\"email\":\"requestor@fast.sdo.gov.ph\",\"position\":\"Personnel\",\"role\":\"User\"}', '{\"full_name\":\"BAC Requestor\",\"email\":\"bac@gmail.com\",\"position\":\"Personnel\",\"role\":\"User\"}', '::1', '2026-05-28 13:35:39'),
(33, 1, 'User logout successful', NULL, NULL, '::1', '2026-05-28 14:00:11'),
(34, 1, 'User login successful', NULL, NULL, '::1', '2026-05-28 14:00:14'),
(35, 1, 'User login successful', NULL, NULL, '::1', '2026-05-28 14:02:18'),
(36, 1, 'Submitted new transaction: FAST-2026-000001', 'null', '{\"tracking_number\":\"FAST-2026-000001\",\"amount\":20000,\"tax_amount\":2000,\"net_amount\":18000,\"tax_type\":\"Services\"}', '::1', '2026-05-28 14:44:07'),
(37, 1, 'User logout successful', NULL, NULL, '::1', '2026-05-28 14:44:24'),
(38, 8, 'User login successful', NULL, NULL, '::1', '2026-05-28 14:44:37'),
(39, 8, 'User logout successful', NULL, NULL, '::1', '2026-05-28 15:32:26'),
(40, 8, 'User login successful', NULL, NULL, '::1', '2026-05-28 15:32:29'),
(41, 8, 'User logout successful', NULL, NULL, '::1', '2026-05-28 15:43:28'),
(42, 1, 'User login successful', NULL, NULL, '::1', '2026-05-28 15:43:32'),
(43, 1, 'User logout successful', NULL, NULL, '::1', '2026-05-28 15:44:27'),
(44, 8, 'User login successful', NULL, NULL, '::1', '2026-05-28 15:44:30'),
(45, 8, 'User logout successful', NULL, NULL, '::1', '2026-05-28 15:44:44'),
(46, 1, 'User login successful', NULL, NULL, '::1', '2026-05-28 15:48:27'),
(47, 1, 'User login successful', NULL, NULL, '::1', '2026-05-28 16:07:12'),
(48, 1, 'User login successful', NULL, NULL, '::1', '2026-05-29 12:34:33'),
(49, 1, 'User login successful', NULL, NULL, '::1', '2026-05-29 13:39:55'),
(50, 1, 'Transaction Status changed: FAST-2026-000001', '{\"status\":\"Pending Support\"}', '{\"status\":\"Pending Accountant\",\"remarks\":\"test gg\"}', '::1', '2026-05-29 14:02:29'),
(51, 1, 'Transaction Status changed: FAST-2026-000001', '{\"status\":\"Pending Accountant\"}', '{\"status\":\"Pending Final Approval\",\"remarks\":\"TEST GG\"}', '::1', '2026-05-29 14:03:03'),
(52, 1, 'Transaction Status changed: FAST-2026-000001', '{\"status\":\"Pending Final Approval\"}', '{\"status\":\"Approved\",\"remarks\":\"TEST GG\"}', '::1', '2026-05-29 14:03:12'),
(53, 1, 'Enterprise Sync: Generated FAST draft FAST-2026-000002 from BAC reference BT-TEST-0002', 'null', 'null', '::1', '2026-05-29 14:08:26'),
(54, 1, 'Enterprise Sync: Generated FAST draft FAST-2026-000002 from BAC reference BT-TEST-0002', 'null', 'null', '::1', '2026-05-29 14:11:44'),
(55, 1, 'Enterprise Sync: Generated FAST draft FAST-2026-000003 from BAC reference BT-TEST-0003', 'null', 'null', '0.0.0.0', '2026-05-29 14:13:01'),
(56, 1, 'Transaction Status changed: FAST-2026-000003', '{\"status\":\"Pending Support\"}', '{\"status\":\"Pending Accountant\",\"remarks\":\"TEST\"}', '::1', '2026-05-29 14:13:32'),
(57, 1, 'Transaction Status changed: FAST-2026-000003', '{\"status\":\"Pending Accountant\"}', '{\"status\":\"Pending Final Approval\",\"remarks\":\"TEST\"}', '::1', '2026-05-29 14:13:49'),
(58, 1, 'Transaction Status changed: FAST-2026-000003', '{\"status\":\"Pending Final Approval\"}', '{\"status\":\"Approved\",\"remarks\":\"TEST APPROVE\"}', '::1', '2026-05-29 14:13:58'),
(59, 1, 'Transaction Status changed: FAST-2026-000003', '{\"status\":\"Approved\"}', '{\"status\":\"Approved\",\"remarks\":\"TEST APPROVE\"}', '::1', '2026-05-29 14:14:00'),
(60, 1, 'Enterprise Sync: Generated FAST draft FAST-2026-000004 from BAC reference BT-TEST-0002', 'null', 'null', '::1', '2026-05-29 14:14:29'),
(61, 1, 'Enterprise Sync: Generated FAST draft FAST-2026-000004 from BAC reference BT-TEST-0002', 'null', 'null', '::1', '2026-05-29 14:14:55'),
(62, 1, 'Enterprise Sync: Generated FAST draft FAST-2026-000005 from BAC reference BTNE0-202604-007', 'null', 'null', '::1', '2026-05-29 14:26:11'),
(63, 1, 'Enterprise Sync: Generated FAST draft FAST-2026-000006 from BAC reference BTJOR-202604-006', 'null', 'null', '::1', '2026-05-29 14:52:44'),
(64, 1, 'Enterprise Sync: Generated FAST draft FAST-2026-000007 from BAC reference PR-0027', 'null', 'null', '0.0.0.0', '2026-05-29 15:08:17'),
(65, 1, 'Enterprise Sync: Generated FAST draft FAST-2026-000007 from BAC reference BTLWD-202604-005', 'null', 'null', '::1', '2026-05-29 15:47:22'),
(66, 1, 'User logout successful', NULL, NULL, '::1', '2026-05-29 16:26:19'),
(67, 5, 'User login successful', NULL, NULL, '::1', '2026-05-29 16:26:36'),
(68, 5, 'User logout successful', NULL, NULL, '::1', '2026-05-29 16:29:31'),
(69, 1, 'User login successful', NULL, NULL, '::1', '2026-05-29 16:29:35'),
(70, 1, 'User logout successful', NULL, NULL, '::1', '2026-05-29 16:31:51'),
(71, 5, 'User login successful', NULL, NULL, '::1', '2026-05-29 16:31:56'),
(72, 5, 'Submitted new transaction: FAST-2026-000008', 'null', '{\"tracking_number\":\"FAST-2026-000008\",\"amount\":15000,\"tax_amount\":1500,\"net_amount\":13500,\"tax_type\":\"Services\"}', '::1', '2026-05-29 16:33:44'),
(73, 5, 'User logout successful', NULL, NULL, '::1', '2026-05-29 16:50:36'),
(74, 1, 'User login successful', NULL, NULL, '::1', '2026-05-29 16:50:41'),
(75, 1, 'Enterprise Sync: Generated FAST draft FAST-2026-000009 from BAC reference BTK1E-202604-004', 'null', 'null', '::1', '2026-05-29 17:00:16'),
(76, 1, 'User login successful', NULL, NULL, '::1', '2026-05-29 18:07:26'),
(77, 1, 'Enterprise Sync: Generated FAST draft FAST-2026-000010 from BAC reference PR-0023', 'null', 'null', '::1', '2026-05-29 18:09:16'),
(78, 1, 'User logout successful', NULL, NULL, '::1', '2026-05-29 18:09:43'),
(79, 8, 'User login successful', NULL, NULL, '::1', '2026-05-29 18:09:48'),
(80, 8, 'User logout successful', NULL, NULL, '::1', '2026-05-29 18:09:51'),
(81, 5, 'User login successful', NULL, NULL, '::1', '2026-05-29 18:09:54'),
(82, 5, 'User logout successful', NULL, NULL, '::1', '2026-05-29 18:11:14'),
(83, 5, 'User login successful', NULL, NULL, '::1', '2026-05-31 14:20:39'),
(84, 5, 'User login successful', NULL, NULL, '::1', '2026-05-31 15:12:10'),
(85, 5, 'User logout successful', NULL, NULL, '::1', '2026-05-31 15:12:12'),
(86, 1, 'User login successful', NULL, NULL, '::1', '2026-05-31 15:12:16'),
(87, 1, 'Created user account: alex (joerenz.dev@gmail.com)', 'null', '{\"full_name\":\"Alexander Joerenz Escallente\",\"position\":\"Personnel\",\"auto_role\":\"User\"}', '::1', '2026-05-31 15:42:30'),
(88, 1, 'User logout successful', NULL, NULL, '::1', '2026-05-31 15:42:37'),
(89, 9, 'User login successful', NULL, NULL, '::1', '2026-05-31 15:42:45'),
(90, 9, 'User logout successful', NULL, NULL, '::1', '2026-05-31 15:47:06'),
(91, 5, 'User login successful', NULL, NULL, '::1', '2026-05-31 16:41:40'),
(92, 5, 'User logout successful', NULL, NULL, '::1', '2026-05-31 16:41:49'),
(93, 1, 'User login successful', NULL, NULL, '::1', '2026-05-31 16:41:52'),
(94, 1, 'User logout successful', NULL, NULL, '::1', '2026-05-31 16:42:27'),
(95, 8, 'User login successful', NULL, NULL, '::1', '2026-05-31 16:42:31'),
(96, 8, 'Transaction Status changed: FAST-2026-000010', '{\"status\":\"Pending Support\"}', '{\"status\":\"Pending Accountant\",\"remarks\":\"okay noted\"}', '::1', '2026-05-31 16:43:11'),
(97, 8, 'User logout successful', NULL, NULL, '::1', '2026-05-31 16:43:15'),
(98, 2, 'User login successful', NULL, NULL, '::1', '2026-05-31 16:43:26'),
(99, 2, 'Transaction Status changed: FAST-2026-000010', '{\"status\":\"Pending Accountant\"}', '{\"status\":\"Pending Final Approval\",\"remarks\":\"NOTED\"}', '::1', '2026-05-31 16:44:17'),
(100, 2, 'User logout successful', NULL, NULL, '::1', '2026-05-31 16:44:28'),
(101, 7, 'User login successful', NULL, NULL, '::1', '2026-05-31 16:44:44'),
(102, 7, 'Transaction Status changed: FAST-2026-000010', '{\"status\":\"Pending Final Approval\"}', '{\"status\":\"Approved\",\"remarks\":\"gege\"}', '::1', '2026-05-31 16:44:54'),
(103, 7, 'User logout successful', NULL, NULL, '::1', '2026-05-31 16:44:59'),
(104, 1, 'User login successful', NULL, NULL, '::1', '2026-05-31 16:45:03'),
(105, 1, 'User logout successful', NULL, NULL, '::1', '2026-05-31 16:50:07'),
(106, 1, 'User login successful', NULL, NULL, '::1', '2026-06-01 01:29:08'),
(107, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-01 01:30:46'),
(108, 9, 'User login successful', NULL, NULL, '::1', '2026-06-01 01:30:52'),
(109, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-01 01:32:46'),
(110, 9, 'User login successful', NULL, NULL, '::1', '2026-06-01 01:36:06'),
(111, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-01 01:38:54'),
(112, 1, 'User login successful', NULL, NULL, '::1', '2026-06-01 01:39:02'),
(113, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-01 01:43:25'),
(114, 9, 'User login successful', NULL, NULL, '::1', '2026-06-01 01:43:33'),
(115, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-01 01:48:27'),
(116, 1, 'User login successful', NULL, NULL, '::1', '2026-06-01 01:48:34'),
(117, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-01 01:49:09'),
(118, 9, 'User login successful', NULL, NULL, '::1', '2026-06-01 01:49:13'),
(119, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-01 02:10:11'),
(120, 9, 'User login successful', NULL, NULL, '::1', '2026-06-01 02:18:23'),
(121, 9, 'User login successful', NULL, NULL, '::1', '2026-06-01 05:03:24'),
(122, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-01 05:06:57'),
(123, 1, 'User login successful', NULL, NULL, '::1', '2026-06-01 05:07:09'),
(124, 9, 'User login successful', NULL, NULL, '::1', '2026-06-01 05:54:14'),
(125, 9, 'User login successful', NULL, NULL, '::1', '2026-06-02 00:06:39'),
(126, 9, 'User login successful', NULL, NULL, '::1', '2026-06-02 05:08:27'),
(127, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-02 05:53:45'),
(128, 1, 'User login successful', NULL, NULL, '::1', '2026-06-02 05:53:51'),
(129, 1, 'Transaction Status changed: FAST-2026-000009', '{\"status\":\"Pending Support\"}', '{\"status\":\"Pending Accountant\",\"remarks\":\"okay noted\"}', '::1', '2026-06-02 05:54:37'),
(130, 1, 'Transaction Status changed: FAST-2026-000008', '{\"status\":\"Pending Support\"}', '{\"status\":\"Returned\",\"remarks\":\"not needed anymore\"}', '::1', '2026-06-02 05:54:53'),
(131, 1, 'User login successful', NULL, NULL, '::1', '2026-06-02 06:37:52'),
(132, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-02 06:37:54'),
(133, 9, 'User login successful', NULL, NULL, '::1', '2026-06-02 06:37:58'),
(134, 9, 'User login successful', NULL, NULL, '::1', '2026-06-02 07:33:10'),
(135, 1, 'User login successful', NULL, NULL, '::1', '2026-06-02 08:38:36'),
(136, 9, 'User login successful', NULL, NULL, '::1', '2026-06-04 00:03:36'),
(137, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-04 00:04:45'),
(138, 1, 'User login successful', NULL, NULL, '::1', '2026-06-04 00:04:51'),
(139, 1, 'User login successful', NULL, NULL, '::1', '2026-06-04 00:40:22'),
(140, 1, 'User login successful', NULL, NULL, '::1', '2026-06-04 01:38:53'),
(141, 1, 'User login successful', NULL, NULL, '::1', '2026-06-04 01:40:17'),
(142, 1, 'User login successful', NULL, NULL, '::1', '2026-06-04 02:56:26'),
(143, 1, 'User login successful', NULL, NULL, '::1', '2026-06-04 02:56:38'),
(144, 1, 'User login successful', NULL, NULL, '::1', '2026-06-04 02:59:00'),
(145, 1, 'Updated permissions override for user: Alexander Joerenz Escallente', '{\"view\":0,\"encode\":0,\"edit\":0,\"approve\":0,\"delete\":0,\"manage_users\":0,\"configure_system\":0}', '{\"view\":1,\"encode\":0,\"edit\":0,\"approve\":0,\"delete\":0,\"manage_users\":0,\"configure_system\":1}', '::1', '2026-06-04 03:29:15'),
(146, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-04 03:29:17'),
(147, 9, 'User login successful', NULL, NULL, '::1', '2026-06-04 03:29:22'),
(148, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-04 03:29:39'),
(149, 1, 'User login successful', NULL, NULL, '::1', '2026-06-04 03:29:44'),
(150, 1, 'Updated permissions override for user: Alexander Joerenz Escallente', '{\"view\":1,\"encode\":0,\"edit\":0,\"approve\":0,\"delete\":0,\"manage_users\":0,\"configure_system\":1}', '{\"view\":1,\"encode\":0,\"edit\":0,\"approve\":0,\"delete\":0,\"manage_users\":0,\"configure_system\":0}', '::1', '2026-06-04 03:29:55'),
(151, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-04 03:29:56'),
(152, 9, 'User login successful', NULL, NULL, '::1', '2026-06-04 03:30:00'),
(153, 1, 'Updated permissions override for user: Alexander Joerenz Escallente', '{\"view\":1,\"encode\":0,\"edit\":0,\"approve\":0,\"delete\":0,\"manage_users\":0,\"configure_system\":0}', '{\"view\":1,\"encode\":1,\"edit\":0,\"approve\":0,\"delete\":0,\"manage_users\":0,\"configure_system\":0}', '::1', '2026-06-04 03:37:26'),
(154, 1, 'Updated permissions override for user: Alexander Joerenz Escallente', '{\"view\":1,\"encode\":1,\"edit\":0,\"approve\":0,\"delete\":0,\"manage_users\":0,\"configure_system\":0}', '{\"view\":0,\"encode\":1,\"edit\":0,\"approve\":0,\"delete\":0,\"manage_users\":0,\"configure_system\":0}', '::1', '2026-06-04 03:44:14'),
(155, 9, 'User login successful', NULL, NULL, '::1', '2026-06-04 07:52:31'),
(156, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-04 07:53:50'),
(157, 1, 'User login successful', NULL, NULL, '::1', '2026-06-04 07:53:56'),
(158, 1, 'Enterprise Sync: Generated FAST draft FAST-2026-000011 from BAC reference BTKKX-202604-003', 'null', 'null', '::1', '2026-06-04 07:55:04'),
(159, 9, 'User login successful', NULL, NULL, '::1', '2026-06-10 07:14:38'),
(160, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-10 07:15:01'),
(161, 1, 'User login successful', NULL, NULL, '::1', '2026-06-10 07:15:05'),
(162, 1, 'User login successful', NULL, NULL, '::1', '2026-06-10 15:02:24'),
(163, 1, 'User login successful', NULL, NULL, '::1', '2026-06-11 01:35:15'),
(164, 1, 'Enterprise Sync: Generated FAST draft FAST-2026-000012 from BAC reference BTICY-202606-001', 'null', 'null', '::1', '2026-06-11 01:40:53'),
(165, 1, 'User login successful', NULL, NULL, '::1', '2026-06-11 02:10:17'),
(166, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-11 02:10:20'),
(167, 9, 'User login successful', NULL, NULL, '::1', '2026-06-11 02:10:24'),
(168, 9, 'User login successful', NULL, NULL, '::1', '2026-06-11 02:32:29'),
(169, 9, 'Submitted new transaction: FAST-2026-000013', 'null', '{\"tracking_number\":\"FAST-2026-000013\",\"amount\":10000,\"tax_amount\":1000,\"net_amount\":9000,\"tax_type\":\"Services\"}', '::1', '2026-06-11 03:08:20'),
(170, 8, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:08:50'),
(171, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-11 03:09:01'),
(172, 2, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:09:03'),
(173, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-11 03:13:49'),
(174, 1, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:13:54'),
(175, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-11 03:14:15'),
(176, 9, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:14:18'),
(177, 9, 'Submitted new transaction: FAST-2026-000014', 'null', '{\"tracking_number\":\"FAST-2026-000014\",\"amount\":5000,\"tax_amount\":500,\"net_amount\":4500,\"tax_type\":\"Services\"}', '::1', '2026-06-11 03:16:06'),
(178, 2, 'Transaction Status changed: FAST-2026-000014', '{\"status\":\"Pending Accountant 1\"}', '{\"status\":\"Pending Support\",\"remarks\":\"PA DOUBLE CHECK NG MAAYOS\"}', '::1', '2026-06-11 03:16:57'),
(179, 2, 'User logout successful', NULL, NULL, '::1', '2026-06-11 03:17:00'),
(180, 8, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:17:03'),
(181, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-11 03:17:22'),
(182, 1, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:17:25'),
(183, 1, 'Updated permissions override for user: Accounting Support', '{\"view\":0,\"encode\":0,\"edit\":0,\"approve\":0,\"delete\":0,\"manage_users\":0,\"configure_system\":0}', '{\"view\":1,\"encode\":1,\"edit\":1,\"approve\":1,\"delete\":0,\"manage_users\":0,\"configure_system\":0}', '::1', '2026-06-11 03:17:47'),
(184, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-11 03:18:24'),
(185, 2, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:18:27'),
(186, NULL, 'Failed login attempt', '{\"identity\":\"acctgsupport@gmail.com\"}', NULL, '::1', '2026-06-11 03:24:46'),
(187, 8, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:25:11'),
(188, 8, 'Transaction Status changed: FAST-2026-000014', '{\"status\":\"Pending Support\"}', '{\"status\":\"Pending Budget Check\",\"remarks\":\"okay na\"}', '::1', '2026-06-11 03:29:17'),
(189, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-11 03:29:20'),
(190, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-11 03:29:29'),
(191, 1, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:29:36'),
(192, 2, 'User logout successful', NULL, NULL, '::1', '2026-06-11 03:29:51'),
(193, 3, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:29:59'),
(194, 3, 'Transaction Status changed: FAST-2026-000014', '{\"status\":\"Pending Budget Check\"}', '{\"status\":\"Pending Accountant 2\",\"remarks\":\"okay naaaa\"}', '::1', '2026-06-11 03:30:26'),
(195, 3, 'User logout successful', NULL, NULL, '::1', '2026-06-11 03:30:32'),
(196, 2, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:30:36'),
(197, 2, 'Transaction Status changed: FAST-2026-000014', '{\"status\":\"Pending Accountant 2\"}', '{\"status\":\"Pending Final Approval\",\"remarks\":\"boss\"}', '::1', '2026-06-11 03:31:00'),
(198, 2, 'User logout successful', NULL, NULL, '::1', '2026-06-11 03:31:02'),
(199, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-11 03:31:09'),
(200, 6, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:31:22'),
(201, 6, 'Transaction Status changed: FAST-2026-000014', '{\"status\":\"Pending Final Approval\"}', '{\"status\":\"Approved\",\"remarks\":\"ok\"}', '::1', '2026-06-11 03:31:38'),
(202, 6, 'User logout successful', NULL, NULL, '::1', '2026-06-11 03:31:39'),
(203, 9, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:31:43'),
(204, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-11 03:31:55'),
(205, 3, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:32:07'),
(206, 8, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:36:19'),
(207, 8, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:41:25'),
(208, 8, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:49:59'),
(209, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-11 03:52:46'),
(210, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-11 03:52:53'),
(211, 3, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:52:57'),
(212, 3, 'User logout successful', NULL, NULL, '::1', '2026-06-11 03:53:46'),
(213, 1, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:53:51'),
(214, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-11 03:55:52'),
(215, 5, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:55:56'),
(216, 3, 'User login successful', NULL, NULL, '::1', '2026-06-11 03:59:12'),
(217, 10, 'User account registered & verified successfully', NULL, NULL, '::1', '2026-06-11 04:11:42'),
(218, 10, 'User login successful', NULL, NULL, '::1', '2026-06-11 04:12:13'),
(219, 5, 'User logout successful', NULL, NULL, '::1', '2026-06-11 04:14:02'),
(220, NULL, 'User account registered & verified successfully', NULL, NULL, '::1', '2026-06-11 04:16:01'),
(221, NULL, 'User login successful', NULL, NULL, '::1', '2026-06-11 04:16:33'),
(222, NULL, 'User logout successful', NULL, NULL, '::1', '2026-06-11 04:16:53'),
(223, NULL, 'Failed login attempt', '{\"identity\":\"accountant@deped.gov.ph\"}', NULL, '::1', '2026-06-11 04:17:23'),
(224, NULL, 'Failed login attempt', '{\"identity\":\"accountant@deped.gov.ph\"}', NULL, '::1', '2026-06-11 04:18:40'),
(225, NULL, 'Failed login attempt', '{\"identity\":\"accountant\"}', NULL, '::1', '2026-06-11 04:19:29'),
(226, 3, 'User login successful', NULL, NULL, '::1', '2026-06-11 06:12:05'),
(227, 3, 'User logout successful', NULL, NULL, '::1', '2026-06-11 06:12:18'),
(228, 9, 'User login successful', NULL, NULL, '::1', '2026-06-11 06:12:24'),
(229, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-11 06:12:28'),
(230, 2, 'User login successful', NULL, NULL, '::1', '2026-06-11 06:12:30'),
(231, 2, 'User logout successful', NULL, NULL, '::1', '2026-06-11 06:12:36'),
(232, 3, 'User login successful', NULL, NULL, '::1', '2026-06-11 06:12:40'),
(233, 3, 'User logout successful', NULL, NULL, '::1', '2026-06-11 06:12:44'),
(234, 9, 'User login successful', NULL, NULL, '::1', '2026-06-11 06:12:49'),
(235, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-11 06:13:09'),
(236, 2, 'User login successful', NULL, NULL, '::1', '2026-06-11 06:13:11'),
(237, 2, 'User logout successful', NULL, NULL, '::1', '2026-06-11 06:13:27'),
(238, 8, 'User login successful', NULL, NULL, '::1', '2026-06-11 06:13:30'),
(239, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-11 06:13:46'),
(240, 2, 'User login successful', NULL, NULL, '::1', '2026-06-11 06:13:49'),
(241, 2, 'User logout successful', NULL, NULL, '::1', '2026-06-11 06:14:59'),
(242, 8, 'User login successful', NULL, NULL, '::1', '2026-06-11 06:15:01'),
(243, 8, 'Transaction Status changed: FAST-2026-000013', '{\"status\":\"Pending Support\"}', '{\"status\":\"Pending Budget Check\",\"remarks\":\"okay\"}', '::1', '2026-06-11 06:15:09'),
(244, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-11 06:15:14'),
(245, 3, 'User login successful', NULL, NULL, '::1', '2026-06-11 06:15:18'),
(246, 3, 'User logout successful', NULL, NULL, '::1', '2026-06-11 06:16:28'),
(247, 2, 'User login successful', NULL, NULL, '::1', '2026-06-11 06:16:31'),
(248, 2, 'User logout successful', NULL, NULL, '::1', '2026-06-11 06:16:38'),
(249, 8, 'User login successful', NULL, NULL, '::1', '2026-06-11 06:16:40'),
(250, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-11 06:21:24'),
(251, 3, 'User login successful', NULL, NULL, '::1', '2026-06-11 06:24:19'),
(252, 3, 'Transaction Status changed: FAST-2026-000013', '{\"status\":\"Pending Budget Check\"}', '{\"status\":\"Pending Accountant 2\",\"remarks\":\"ok\"}', '::1', '2026-06-11 06:24:32'),
(253, 1, 'User login successful', NULL, NULL, '::1', '2026-06-11 08:44:14'),
(254, 1, 'Submitted new transaction: FAST-2026-000017', 'null', '{\"tracking_number\":\"FAST-2026-000017\",\"amount\":80000,\"tax_amount\":4000,\"net_amount\":76000,\"tax_type\":\"Goods\"}', '::1', '2026-06-11 08:45:22'),
(255, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-11 08:45:30'),
(256, 2, 'User login successful', NULL, NULL, '::1', '2026-06-11 08:45:35'),
(257, 2, 'Transaction Status changed: FAST-2026-000017', '{\"status\":\"Pending Accountant 1\"}', '{\"status\":\"Pending Support\",\"remarks\":\"ok\"}', '::1', '2026-06-11 08:45:56'),
(258, 2, 'User logout successful', NULL, NULL, '::1', '2026-06-11 08:45:58'),
(259, 8, 'User login successful', NULL, NULL, '::1', '2026-06-11 08:46:02'),
(260, 8, 'Transaction Status changed: FAST-2026-000017', '{\"status\":\"Pending Support\"}', '{\"status\":\"Pending Budget Check\",\"remarks\":\"ok\"}', '::1', '2026-06-11 08:46:12'),
(261, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-11 08:46:16'),
(262, 3, 'User login successful', NULL, NULL, '::1', '2026-06-11 08:46:19'),
(263, 3, 'User logout successful', NULL, NULL, '::1', '2026-06-11 08:50:52'),
(264, 2, 'User login successful', NULL, NULL, '::1', '2026-06-11 08:51:47'),
(265, 9, 'User login successful', NULL, NULL, '::1', '2026-06-11 08:56:03'),
(266, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-11 08:56:06'),
(267, 9, 'User login successful', NULL, NULL, '::1', '2026-06-11 08:57:02'),
(268, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-11 08:57:45'),
(269, 1, 'User login successful', NULL, NULL, '::1', '2026-06-11 08:57:50'),
(270, 1, 'Submitted new transaction: FAST-2026-000018', 'null', '{\"tracking_number\":\"FAST-2026-000018\",\"amount\":50000,\"tax_amount\":1000,\"net_amount\":49000,\"tax_type\":\"Foods\"}', '::1', '2026-06-11 09:00:06'),
(271, 9, 'User login successful', NULL, NULL, '::1', '2026-06-11 11:02:10'),
(272, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-11 11:02:22'),
(273, 1, 'User login successful', NULL, NULL, '::1', '2026-06-11 11:02:30'),
(274, 1, 'Updated permissions for role: User', '{\"view\":1,\"encode\":0,\"edit\":0,\"approve\":0,\"delete\":0,\"manage_users\":0,\"configure_system\":0,\"view_bactrack\":0}', '{\"view\":1,\"encode\":1,\"edit\":0,\"approve\":0,\"delete\":0,\"manage_users\":0,\"configure_system\":0,\"view_bactrack\":0}', '::1', '2026-06-11 11:03:09'),
(275, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-11 11:03:12'),
(276, 9, 'User login successful', NULL, NULL, '::1', '2026-06-11 11:03:20'),
(277, 9, 'User login successful', NULL, NULL, '::1', '2026-06-11 11:44:30'),
(278, 1, 'User login successful', NULL, NULL, '::1', '2026-06-12 13:27:39'),
(279, 9, 'User login successful', NULL, NULL, '::1', '2026-06-12 14:58:25'),
(280, NULL, 'Failed login attempt', '{\"identity\":\"bacsec@deped.gov.ph\"}', NULL, '::1', '2026-06-12 17:07:37'),
(281, NULL, 'Failed login attempt', '{\"identity\":\"bacsec@deped.gov.ph\"}', NULL, '::1', '2026-06-12 17:07:40'),
(282, 9, 'User login successful', NULL, NULL, '::1', '2026-06-12 17:07:46'),
(283, 1, 'User login successful', NULL, NULL, '::1', '2026-06-14 07:24:09'),
(284, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-14 07:24:18'),
(285, 1, 'User login successful', NULL, NULL, '::1', '2026-06-14 07:24:22'),
(286, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-14 07:24:28'),
(287, 1, 'User login successful', NULL, NULL, '::1', '2026-06-14 07:24:35'),
(288, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-14 07:25:58'),
(289, 1, 'User login successful', NULL, NULL, '::1', '2026-06-14 07:26:00'),
(290, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-14 07:26:01'),
(291, 9, 'User login successful', NULL, NULL, '::1', '2026-06-14 07:26:09'),
(292, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-14 07:26:52'),
(293, 1, 'User login successful', NULL, NULL, '::1', '2026-06-14 07:26:58'),
(294, 1, 'Updated permissions for role: Requestor', '{\"view\":1,\"encode\":0,\"edit\":0,\"approve\":0,\"delete\":0,\"manage_users\":0,\"configure_system\":0,\"view_bactrack\":0}', '{\"view\":1,\"encode\":1,\"edit\":0,\"approve\":0,\"delete\":0,\"manage_users\":0,\"configure_system\":0,\"view_bactrack\":0}', '::1', '2026-06-14 07:27:36'),
(295, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-14 07:29:38'),
(296, 9, 'User login successful', NULL, NULL, '::1', '2026-06-16 10:52:45'),
(297, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-16 10:54:01'),
(298, 1, 'User login successful', NULL, NULL, '::1', '2026-06-16 10:54:09'),
(299, NULL, 'Failed login attempt', '{\"identity\":\"jadmin\"}', NULL, '::1', '2026-06-16 10:55:35'),
(300, 9, 'User login successful', NULL, NULL, '::1', '2026-06-16 10:55:46'),
(301, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-16 10:56:12'),
(302, 9, 'User login successful', NULL, NULL, '::1', '2026-06-16 10:56:18'),
(303, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-16 11:11:48'),
(304, NULL, 'User login successful', NULL, NULL, '::1', '2026-06-16 11:12:02'),
(305, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-16 11:12:10'),
(306, NULL, 'Failed login attempt', '{\"identity\":\"fastsdo@gmail.com\"}', NULL, '::1', '2026-06-16 11:12:16'),
(307, NULL, 'Failed login attempt', '{\"identity\":\"fastsdo@gmail.com\"}', NULL, '::1', '2026-06-16 11:12:21'),
(308, NULL, 'Failed login attempt', '{\"identity\":\"fastsdo@gmail.com\"}', NULL, '::1', '2026-06-16 11:12:29'),
(309, 9, 'User login successful', NULL, NULL, '::1', '2026-06-16 11:12:41'),
(310, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-16 11:12:43'),
(311, NULL, 'Failed login attempt', '{\"identity\":\"fastsdo@gmail.com\"}', NULL, '::1', '2026-06-16 11:12:48'),
(312, NULL, 'Failed login attempt', '{\"identity\":\"fastsdo@gmail.com\"}', NULL, '::1', '2026-06-16 11:13:01'),
(313, 1, 'User login successful', NULL, NULL, '::1', '2026-06-16 11:13:21'),
(314, 1, 'Changed own password', 'null', 'null', '::1', '2026-06-16 11:13:39'),
(315, 1, 'User login successful', NULL, NULL, '::1', '2026-06-17 03:02:51'),
(316, 1, 'Added new position: Cashier (mapped to Approver)', 'null', '{\"position_name\":\"Cashier\",\"mapped_role\":\"Approver\"}', '::1', '2026-06-17 03:04:12'),
(317, 1, 'Created user account: cashier (cashier@gmail.com)', 'null', '{\"full_name\":\"cashier\",\"position\":\"Cashier\",\"auto_role\":\"Approver\"}', '::1', '2026-06-17 03:04:46'),
(318, 1, 'Updated profile details and role for user: cashier', '{\"full_name\":\"cashier\",\"email\":\"cashier@gmail.com\",\"position\":\"Cashier\",\"role\":\"Approver\"}', '{\"full_name\":\"Cashier\",\"email\":\"cashier@gmail.com\",\"position\":\"Cashier\",\"role\":\"Approver\"}', '::1', '2026-06-17 03:05:08'),
(319, NULL, 'Workflow Migration v2: 5-Stage → 6-Stage', '[\"Pending Accountant 1\",\"Pending Support\",\"Pending Budget Check\",\"Pending Accountant 2\",\"Pending Final Approval\",\"Approved\"]', '[\"Pending ACCTG Support\",\"Pending ACCTG Support\",\"Pending Budget\",\"Pending ACCT Support\",\"Pending Signatories\",\"Pending Cashier Release\"]', '::1', '2026-06-17 03:30:14'),
(320, NULL, 'Workflow Migration v2: 5-Stage → 6-Stage', '[\"Pending Accountant 1\",\"Pending Support\",\"Pending Budget Check\",\"Pending Accountant 2\",\"Pending Final Approval\",\"Approved\"]', '[\"Pending ACCTG Support\",\"Pending ACCTG Support\",\"Pending Budget\",\"Pending ACCT Support\",\"Pending Signatories\",\"Pending Cashier Release\"]', '::1', '2026-06-17 03:30:14'),
(321, 1, 'Enterprise Sync: Generated FAST draft FAST-2026-000027 from BAC reference BAC-TEST-9999', 'null', 'null', '::1', '2026-06-17 03:36:06'),
(322, 1, 'Enterprise Sync: Generated FAST draft FAST-2026-000027 from BAC reference BAC-TEST-9999', 'null', 'null', '::1', '2026-06-17 03:36:39'),
(323, 1, 'Enterprise Sync: Generated FAST draft FAST-2026-000027 from BAC reference BAC-TEST-9999', 'null', 'null', '::1', '2026-06-17 03:36:39'),
(324, 1, 'Enterprise Sync: Generated FAST draft FAST-2026-000027 from BAC reference BAC-TEST-9999', 'null', 'null', '::1', '2026-06-17 03:36:49'),
(325, 1, 'Enterprise Sync: Generated FAST draft FAST-2026-000027 from BAC reference BAC-TEST-9999', 'null', 'null', '::1', '2026-06-17 03:36:49'),
(326, 9, 'User login successful', NULL, NULL, '::1', '2026-06-17 03:38:44'),
(327, 5, 'Submitted new transaction: FAST-2026-000027', 'null', '{\"tracking_number\":\"FAST-2026-000027\",\"amount\":10000,\"tax_amount\":0,\"net_amount\":10000,\"tax_type\":null}', '::1', '2026-06-17 03:49:36'),
(328, 2, 'Tax classification set and transaction auto-advanced: FAST-2026-000027', '{\"status\":\"Pending ACCTG Support\",\"tax_type\":null}', '{\"status\":\"Pending Budget\",\"tax_type\":\"Services\"}', '::1', '2026-06-17 03:49:36'),
(329, 5, 'Submitted new transaction: FAST-2026-000027', 'null', '{\"tracking_number\":\"FAST-2026-000027\",\"amount\":10000,\"tax_amount\":0,\"net_amount\":10000,\"tax_type\":null}', '::1', '2026-06-17 03:49:36'),
(330, 2, 'Tax classification set and transaction auto-advanced: FAST-2026-000027', '{\"status\":\"Pending ACCTG Support\",\"tax_type\":null}', '{\"status\":\"Pending Budget\",\"tax_type\":\"Services\"}', '::1', '2026-06-17 03:49:36'),
(331, 9, 'Submitted new transaction: FAST-2026-000027', 'null', '{\"tracking_number\":\"FAST-2026-000027\",\"amount\":10000,\"tax_amount\":0,\"net_amount\":10000,\"tax_type\":null}', '::1', '2026-06-17 03:52:12'),
(332, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-17 03:52:26'),
(333, 8, 'User login successful', NULL, NULL, '::1', '2026-06-17 03:52:31'),
(334, 8, 'Updated tax classification to \'Services\' for transaction: FAST-2026-000027', '{\"tax_type\":null}', '{\"tax_type\":\"Services\"}', '::1', '2026-06-17 03:53:55'),
(335, 8, 'Updated tax classification to \'Goods\' for transaction: FAST-2026-000027', '{\"tax_type\":null}', '{\"tax_type\":\"Goods\"}', '::1', '2026-06-17 03:54:03'),
(336, 8, 'Updated tax classification to \'Services\' for transaction: FAST-2026-000027', '{\"tax_type\":null}', '{\"tax_type\":\"Services\"}', '::1', '2026-06-17 03:54:04'),
(337, 8, 'All attachments approved and tax classification set, transaction auto-advanced: FAST-2026-000027', '{\"status\":\"Pending ACCTG Support\"}', '{\"status\":\"Pending Budget\"}', '::1', '2026-06-17 03:54:15'),
(338, 8, 'Transaction Status changed: FAST-2026-000019', '{\"status\":\"Pending ACCTG Support\"}', '{\"status\":\"Returned\",\"remarks\":\"no attach documents\"}', '::1', '2026-06-17 03:54:38'),
(339, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-17 03:54:46'),
(340, 3, 'User login successful', NULL, NULL, '::1', '2026-06-17 03:54:50'),
(341, 3, 'Budget check approved: FAST-2026-000027', '{\"status\":\"Pending Budget\"}', '{\"status\":\"Pending ACCT Support\",\"fund_source\":\"MOOE\"}', '::1', '2026-06-17 03:55:34'),
(342, 3, 'User logout successful', NULL, NULL, '::1', '2026-06-17 03:55:37'),
(343, 8, 'User login successful', NULL, NULL, '::1', '2026-06-17 03:55:41'),
(344, 8, 'Transaction Status changed: FAST-2026-000027', '{\"status\":\"Pending ACCT Support\"}', '{\"status\":\"Pending Signatories\",\"remarks\":\"noted test\"}', '::1', '2026-06-17 03:56:08'),
(345, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-17 03:56:10'),
(346, 9, 'User login successful', NULL, NULL, '::1', '2026-06-17 03:56:14'),
(347, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-17 03:56:26'),
(348, 7, 'User login successful', NULL, NULL, '::1', '2026-06-17 03:56:31'),
(349, 7, 'Both signatory tasks completed, auto-advanced: FAST-2026-000027', '{\"status\":\"Pending Signatories\"}', '{\"status\":\"Pending Cashier Release\"}', '::1', '2026-06-17 03:57:13'),
(350, 7, 'User logout successful', NULL, NULL, '::1', '2026-06-17 04:01:31'),
(351, 12, 'User login successful', NULL, NULL, '::1', '2026-06-17 04:01:34'),
(352, 12, 'User logout successful', NULL, NULL, '::1', '2026-06-17 04:01:40'),
(353, 12, 'User login successful', NULL, NULL, '::1', '2026-06-17 04:01:49'),
(354, 12, 'User logout successful', NULL, NULL, '::1', '2026-06-17 04:01:58'),
(355, 7, 'User login successful', NULL, NULL, '::1', '2026-06-17 04:02:02'),
(356, 7, 'User logout successful', NULL, NULL, '::1', '2026-06-17 04:02:17'),
(357, 12, 'User login successful', NULL, NULL, '::1', '2026-06-17 04:02:22'),
(358, 12, 'User logout successful', NULL, NULL, '::1', '2026-06-17 04:02:31'),
(359, 1, 'User login successful', NULL, NULL, '::1', '2026-06-17 04:02:34'),
(360, 1, 'Updated permissions for role: Cashier', '{\"view\":1,\"encode\":0,\"edit\":0,\"approve\":1,\"delete\":0,\"manage_users\":0,\"configure_system\":0,\"view_bactrack\":0}', '{\"view\":1,\"encode\":0,\"edit\":1,\"approve\":1,\"delete\":0,\"manage_users\":0,\"configure_system\":0,\"view_bactrack\":0}', '::1', '2026-06-17 04:02:59'),
(361, 1, 'Updated profile details and role for user: cashier', '{\"full_name\":\"Cashier\",\"email\":\"cashier@gmail.com\",\"position\":\"Cashier\",\"role\":\"Approver\"}', '{\"full_name\":\"Cashier\",\"email\":\"cashier@gmail.com\",\"position\":\"Cashier\",\"role\":\"Approver\"}', '::1', '2026-06-17 04:03:12'),
(362, 1, 'Updated profile details and role for user: alex', '{\"full_name\":\"Alexander Joerenz Escallente\",\"email\":\"joerenz.dev@gmail.com\",\"position\":\"Personnel\",\"role\":\"User\"}', '{\"full_name\":\"Alexander Joerenz Escallente\",\"email\":\"joerenz.dev@gmail.com\",\"position\":\"Personnel\",\"role\":\"Requestor\"}', '::1', '2026-06-17 04:07:08'),
(363, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-17 04:07:21'),
(364, 12, 'User login successful', NULL, NULL, '::1', '2026-06-17 04:07:25'),
(365, 12, 'Transaction Status changed: FAST-2026-000027', '{\"status\":\"Pending Cashier Release\"}', '{\"status\":\"Released\",\"remarks\":\"notedddddddddd\"}', '::1', '2026-06-17 04:07:39'),
(366, 12, 'User logout successful', NULL, NULL, '::1', '2026-06-17 04:07:41'),
(367, 9, 'User login successful', NULL, NULL, '::1', '2026-06-17 04:07:46'),
(368, 9, 'User logout successful', NULL, NULL, '::1', '2026-06-17 04:10:19'),
(369, 1, 'User login successful', NULL, NULL, '::1', '2026-06-17 04:10:24'),
(370, 1, 'Updated profile details and role for user: test.personnel', '{\"full_name\":\"Test Personnel\",\"email\":\"test.personnel@deped.gov.ph\",\"position\":\"N\\/A\",\"role\":\"Requestor\"}', '{\"full_name\":\"Test Personnel\",\"email\":\"test.personnel@deped.gov.ph\",\"position\":\"Personnel\",\"role\":\"User\"}', '::1', '2026-06-17 04:11:20'),
(371, 1, 'Updated profile details and role for user: test.personnel', '{\"full_name\":\"Test Personnel\",\"email\":\"test.personnel@deped.gov.ph\",\"position\":\"Personnel\",\"role\":\"User\"}', '{\"full_name\":\"Test Personnel\",\"email\":\"test.personnel@deped.gov.ph\",\"position\":\"Personnel\",\"role\":\"Requestor\"}', '::1', '2026-06-17 04:13:29'),
(372, 1, 'Updated profile details and role for user: alex', '{\"full_name\":\"Alexander Joerenz Escallente\",\"email\":\"joerenz.dev@gmail.com\",\"position\":\"Personnel\",\"role\":\"User\"}', '{\"full_name\":\"Alexander Joerenz Escallente\",\"email\":\"joerenz.dev@gmail.com\",\"position\":\"Personnel\",\"role\":\"Requestor\"}', '::1', '2026-06-17 04:13:58'),
(373, 1, 'Updated profile details and role for user: test.user', '{\"full_name\":\"Test User\",\"email\":\"test.user@deped.gov.ph\",\"position\":\"N\\/A\",\"role\":\"Requestor\"}', '{\"full_name\":\"Test User\",\"email\":\"test.user@gmail.com\",\"position\":\"Personnel\",\"role\":\"Requestor\"}', '::1', '2026-06-17 04:15:34'),
(374, 1, 'Deleted user account: test.personnel (test.personnel@deped.gov.ph)', '{\"full_name\":\"Test Personnel\"}', 'null', '::1', '2026-06-17 04:15:40'),
(375, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-17 04:29:18'),
(376, 1, 'User login successful', NULL, NULL, '::1', '2026-06-17 04:29:24'),
(377, 1, 'Updated profile details, password, and role for user: test.user', '{\"full_name\":\"Test User\",\"email\":\"test.user@gmail.com\",\"position\":\"Personnel\",\"role\":\"Requestor\"}', '{\"full_name\":\"Test User\",\"email\":\"test.user@gmail.com\",\"position\":\"Personnel\",\"role\":\"Requestor\",\"password_changed\":true}', '::1', '2026-06-17 04:33:33'),
(378, 1, 'Updated profile details, password, and role for user: cashier', '{\"full_name\":\"Cashier\",\"email\":\"cashier@gmail.com\",\"position\":\"Cashier\",\"role\":\"Cashier\"}', '{\"full_name\":\"Cashier\",\"email\":\"cashier@gmail.com\",\"position\":\"Cashier\",\"role\":\"Cashier\",\"password_changed\":true}', '::1', '2026-06-17 04:34:07'),
(379, 1, 'Updated profile details, password, and role for user: test.user', '{\"full_name\":\"Test User\",\"email\":\"test.user@gmail.com\",\"position\":\"Personnel\",\"role\":\"Requestor\"}', '{\"full_name\":\"Test User\",\"email\":\"test.user@gmail.com\",\"position\":\"Personnel\",\"role\":\"Requestor\",\"password_changed\":true}', '::1', '2026-06-17 04:34:14'),
(380, 1, 'Updated profile details, password, and role for user: alex', '{\"full_name\":\"Alexander Joerenz Escallente\",\"email\":\"joerenz.dev@gmail.com\",\"position\":\"Personnel\",\"role\":\"Requestor\"}', '{\"full_name\":\"Alexander Joerenz Escallente\",\"email\":\"joerenz.dev@gmail.com\",\"position\":\"Personnel\",\"role\":\"Requestor\",\"password_changed\":true}', '::1', '2026-06-17 04:34:24'),
(381, 1, 'Updated profile details, password, and role for user: Accounting support', '{\"full_name\":\"Accounting Support\",\"email\":\"acctgsupp@gmail.com\",\"position\":\"Accounting Support\",\"role\":\"Accounting Staff\"}', '{\"full_name\":\"Accounting Support\",\"email\":\"acctgsupp@gmail.com\",\"position\":\"Accounting Support\",\"role\":\"Accounting Staff\",\"password_changed\":true}', '::1', '2026-06-17 04:34:35'),
(382, 1, 'Updated profile details, password, and role for user: ASDS', '{\"full_name\":\"Joe Bren L. Consuelo\",\"email\":\"asds@gmail.com\",\"position\":\"ASDS\",\"role\":\"Approver\"}', '{\"full_name\":\"Joe Bren L. Consuelo\",\"email\":\"asds@gmail.com\",\"position\":\"ASDS\",\"role\":\"Approver\",\"password_changed\":true}', '::1', '2026-06-17 04:35:28'),
(383, 1, 'Updated profile details, password, and role for user: SDS', '{\"full_name\":\"Phillip B. Gallendez\",\"email\":\"sds@gmail.com\",\"position\":\"SDS\",\"role\":\"Approver\"}', '{\"full_name\":\"Phillip B. Gallendez\",\"email\":\"sds@gmail.com\",\"position\":\"SDS\",\"role\":\"Approver\",\"password_changed\":true}', '::1', '2026-06-17 04:35:46'),
(384, 1, 'Updated profile details, password, and role for user: Superadmin', '{\"full_name\":\"FAST Super Admin\",\"email\":\"fastsdo@gmail.com\",\"position\":\"Super Admin\",\"role\":\"Super Admin\"}', '{\"full_name\":\"FAST Super Admin\",\"email\":\"fastsdo@gmail.com\",\"position\":\"Super Admin\",\"role\":\"Super Admin\",\"password_changed\":true}', '::1', '2026-06-17 04:36:00'),
(385, 1, 'Updated profile details, password, and role for user: budget', '{\"full_name\":\"Budget Officer\",\"email\":\"budget@gmail.com\",\"position\":\"Budget Officer\",\"role\":\"Budget Officer\"}', '{\"full_name\":\"Budget Officer\",\"email\":\"budget@gmail.com\",\"position\":\"Budget Officer\",\"role\":\"Budget Officer\",\"password_changed\":true}', '::1', '2026-06-17 04:36:16'),
(386, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-17 04:59:23'),
(387, 1, 'User login successful', NULL, NULL, '::1', '2026-06-17 04:59:41'),
(388, 1, 'User login successful', NULL, NULL, '::1', '2026-06-17 05:00:46'),
(389, 1, 'Updated profile details and role for user: alex', '{\"full_name\":\"Alexander Joerenz Escallente\",\"email\":\"joerenz.dev@gmail.com\",\"office\":\"N\\/A\",\"unit_section\":\"N\\/A\",\"employee_no\":\"N\\/A\",\"position\":\"N\\/A\",\"role\":\"Requestor\",\"status\":\"active\"}', '{\"full_name\":\"Alexander Joerenz Escallente\",\"email\":\"joerenz.dev@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"ICT\",\"employee_no\":\"1000045\",\"position\":\"LSB Clerk\",\"role\":\"Requestor\",\"status\":\"active\"}', '::1', '2026-06-17 05:01:42'),
(390, 1, 'Created user account: testalpas (testalpas@gmail.com)', 'null', '{\"full_name\":\"Test ALPAS User\",\"office\":\"OSDS\",\"unit_section\":\"Personnel\",\"employee_no\":\"EMP-9999\",\"position\":\"Administrative Assistant\",\"status\":\"active\"}', '::1', '2026-06-17 05:03:16'),
(391, 1, 'Updated profile details and role for user: testalpas', '{\"full_name\":\"Test ALPAS User\",\"email\":\"testalpas@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"Personnel\",\"employee_no\":\"EMP-9999\",\"position\":\"Administrative Assistant\",\"role\":\"Requestor\",\"status\":\"active\"}', '{\"full_name\":\"Test ALPAS User Edited\",\"email\":\"testalpas@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"Personnel\",\"employee_no\":\"EMP-8888\",\"position\":\"Administrative Assistant\",\"role\":\"Requestor\",\"status\":\"active\"}', '::1', '2026-06-17 05:08:11'),
(392, 1, 'Triggered password reset link for user: testalpas@gmail.com', 'null', '{\"expires_at\":\"2026-06-17 08:09:01\"}', '::1', '2026-06-17 05:09:01'),
(393, 1, 'Deleted user account: testalpas (testalpas@gmail.com)', '{\"full_name\":\"Test ALPAS User Edited\"}', 'null', '::1', '2026-06-17 05:19:54'),
(394, 1, 'Updated profile details and role for user: test.user', '{\"full_name\":\"Test User\",\"email\":\"test.user@gmail.com\",\"office\":\"SGOD\",\"unit_section\":\"SGOD (School Governance and Operations Division)\",\"employee_no\":\"E-12345\",\"position\":\"Teacher I\",\"role\":\"Requestor\",\"status\":\"active\"}', '{\"full_name\":\"Test User\",\"email\":\"test.user@gmail.com\",\"office\":\"SGOD\",\"unit_section\":\"SGOD (School Governance and Operations Division)\",\"employee_no\":\"E-12345\",\"position\":\"\",\"role\":\"Requestor\",\"status\":\"active\"}', '::1', '2026-06-17 05:20:05');
INSERT INTO `activity_logs` (`id`, `user_id`, `activity`, `old_value`, `new_value`, `ip_address`, `created_at`) VALUES
(395, 1, 'Updated profile details and role for user: test.user', '{\"full_name\":\"Test User\",\"email\":\"test.user@gmail.com\",\"office\":\"SGOD\",\"unit_section\":\"SGOD (School Governance and Operations Division)\",\"employee_no\":\"E-12345\",\"position\":\"N\\/A\",\"role\":\"Requestor\",\"status\":\"active\"}', '{\"full_name\":\"Test User\",\"email\":\"test.user@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"ICT\",\"employee_no\":\"E-12345\",\"position\":\"\",\"role\":\"Requestor\",\"status\":\"active\"}', '::1', '2026-06-17 05:20:19'),
(396, 1, 'Updated profile details and role for user: Accounting support', '{\"full_name\":\"Accounting Support\",\"email\":\"acctgsupp@gmail.com\",\"office\":\"N\\/A\",\"unit_section\":\"N\\/A\",\"employee_no\":\"N\\/A\",\"position\":\"N\\/A\",\"role\":\"Accounting Staff\",\"status\":\"active\"}', '{\"full_name\":\"Accounting Support\",\"email\":\"acctgsupp@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"Accounting (Finance - Accounting)\",\"employee_no\":\"\",\"position\":\"\",\"role\":\"Accounting Staff\",\"status\":\"active\"}', '::1', '2026-06-17 05:20:28'),
(397, 1, 'Updated profile details and role for user: ASDS', '{\"full_name\":\"Joe Bren L. Consuelo\",\"email\":\"asds@gmail.com\",\"office\":\"N\\/A\",\"unit_section\":\"N\\/A\",\"employee_no\":\"N\\/A\",\"position\":\"N\\/A\",\"role\":\"Approver\",\"status\":\"active\"}', '{\"full_name\":\"Joe Bren L. Consuelo\",\"email\":\"asds@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"OASDS (Office of the ASDS Staff)\",\"employee_no\":\"\",\"position\":\"\",\"role\":\"Approver\",\"status\":\"active\"}', '::1', '2026-06-17 05:20:36'),
(398, 1, 'Updated profile details and role for user: SDS', '{\"full_name\":\"Phillip B. Gallendez\",\"email\":\"sds@gmail.com\",\"office\":\"N\\/A\",\"unit_section\":\"N\\/A\",\"employee_no\":\"N\\/A\",\"position\":\"N\\/A\",\"role\":\"Approver\",\"status\":\"active\"}', '{\"full_name\":\"Phillip B. Gallendez\",\"email\":\"sds@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"OSDS (Office of the SDS Staff)\",\"employee_no\":\"\",\"position\":\"\",\"role\":\"Approver\",\"status\":\"active\"}', '::1', '2026-06-17 05:20:42'),
(399, 1, 'Updated profile details and role for user: Accountant', '{\"full_name\":\"Accountant\",\"email\":\"accountant@gmail.com\",\"office\":\"N\\/A\",\"unit_section\":\"N\\/A\",\"employee_no\":\"N\\/A\",\"position\":\"N\\/A\",\"role\":\"Accounting Staff\",\"status\":\"active\"}', '{\"full_name\":\"Accountant\",\"email\":\"accountant@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"Accounting (Finance - Accounting)\",\"employee_no\":\"\",\"position\":\"\",\"role\":\"Accounting Staff\",\"status\":\"active\"}', '::1', '2026-06-17 05:20:56'),
(400, 1, 'Updated profile details and role for user: budget', '{\"full_name\":\"Budget Officer\",\"email\":\"budget@gmail.com\",\"office\":\"N\\/A\",\"unit_section\":\"N\\/A\",\"employee_no\":\"N\\/A\",\"position\":\"N\\/A\",\"role\":\"Budget Officer\",\"status\":\"active\"}', '{\"full_name\":\"Budget Officer\",\"email\":\"budget@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"Budget (Finance - Budget)\",\"employee_no\":\"\",\"position\":\"\",\"role\":\"Budget Officer\",\"status\":\"active\"}', '::1', '2026-06-17 05:21:04'),
(401, 1, 'Updated profile details and role for user: ASDS', '{\"full_name\":\"Joe Bren L. Consuelo\",\"email\":\"asds@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"OASDS (Office of the ASDS Staff)\",\"employee_no\":\"N\\/A\",\"position\":\"N\\/A\",\"role\":\"Approver\",\"status\":\"active\"}', '{\"full_name\":\"Joe Bren L. Consuelo\",\"email\":\"asds@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"OASDS (Office of the ASDS Staff)\",\"employee_no\":\"\",\"position\":\"OIC - ASDS\",\"role\":\"Approver\",\"status\":\"active\"}', '::1', '2026-06-17 05:21:19'),
(402, 1, 'Updated profile details and role for user: SDS', '{\"full_name\":\"Phillip B. Gallendez\",\"email\":\"sds@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"OSDS (Office of the SDS Staff)\",\"employee_no\":\"N\\/A\",\"position\":\"N\\/A\",\"role\":\"Approver\",\"status\":\"active\"}', '{\"full_name\":\"Phillip B. Gallendez\",\"email\":\"sds@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"OSDS (Office of the SDS Staff)\",\"employee_no\":\"\",\"position\":\"OIC - SDS\",\"role\":\"Approver\",\"status\":\"active\"}', '::1', '2026-06-17 05:21:32'),
(403, 1, 'Updated profile details and role for user: budget', '{\"full_name\":\"Budget Officer\",\"email\":\"budget@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"Budget (Finance - Budget)\",\"employee_no\":\"N\\/A\",\"position\":\"N\\/A\",\"role\":\"Budget Officer\",\"status\":\"active\"}', '{\"full_name\":\"Budget Officer\",\"email\":\"budget@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"Budget (Finance - Budget)\",\"employee_no\":\"\",\"position\":\"Budget Officer\",\"role\":\"Budget Officer\",\"status\":\"active\"}', '::1', '2026-06-17 05:21:50'),
(404, 1, 'Updated profile details and role for user: cashier', '{\"full_name\":\"Cashier\",\"email\":\"cashier@gmail.com\",\"office\":\"N\\/A\",\"unit_section\":\"N\\/A\",\"employee_no\":\"N\\/A\",\"position\":\"N\\/A\",\"role\":\"Cashier\",\"status\":\"active\"}', '{\"full_name\":\"Cashier\",\"email\":\"cashier@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"Budget (Finance - Budget)\",\"employee_no\":\"\",\"position\":\"\",\"role\":\"Cashier\",\"status\":\"active\"}', '::1', '2026-06-17 05:22:09'),
(405, 10, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:01:30'),
(406, 10, 'Submitted new transaction: FAST-2026-000028', 'null', '{\"tracking_number\":\"FAST-2026-000028\",\"amount\":100000,\"tax_amount\":0,\"net_amount\":100000,\"tax_type\":null}', '::1', '2026-06-17 06:02:33'),
(407, 10, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:02:46'),
(408, 8, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:02:49'),
(409, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:03:01'),
(410, 1, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:03:07'),
(411, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:03:16'),
(412, 8, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:03:19'),
(413, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:03:24'),
(414, 10, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:03:29'),
(415, 10, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:03:38'),
(416, 1, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:03:42'),
(417, 1, 'Updated permissions for role: Accounting Staff', '{\"view\":1,\"encode\":1,\"edit\":0,\"approve\":1,\"delete\":0,\"manage_users\":0,\"configure_system\":0,\"view_bactrack\":0}', '{\"view\":1,\"encode\":1,\"edit\":1,\"approve\":1,\"delete\":0,\"manage_users\":0,\"configure_system\":0,\"view_bactrack\":0}', '::1', '2026-06-17 06:03:56'),
(418, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:03:57'),
(419, 8, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:04:00'),
(420, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:04:23'),
(421, 3, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:04:25'),
(422, 3, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:04:27'),
(423, 8, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:04:34'),
(424, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:04:50'),
(425, 1, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:05:02'),
(426, 1, 'Updated permissions for role: Requestor', '{\"view\":1,\"encode\":1,\"edit\":0,\"approve\":0,\"delete\":0,\"manage_users\":0,\"configure_system\":0,\"view_bactrack\":0}', '{\"view\":1,\"encode\":1,\"edit\":0,\"approve\":0,\"delete\":0,\"manage_users\":0,\"configure_system\":0,\"view_bactrack\":0}', '::1', '2026-06-17 06:05:20'),
(427, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:05:27'),
(428, 8, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:05:30'),
(429, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:05:36'),
(430, 1, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:05:41'),
(431, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:05:45'),
(432, 10, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:05:53'),
(433, 8, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:06:32'),
(434, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:06:43'),
(435, 10, 'Submitted new transaction: FAST-2026-000029', 'null', '{\"tracking_number\":\"FAST-2026-000029\",\"amount\":112031023,\"tax_amount\":0,\"net_amount\":112031023,\"tax_type\":null}', '::1', '2026-06-17 06:08:49'),
(436, 10, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:08:55'),
(437, 8, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:08:58'),
(438, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:09:37'),
(439, 10, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:09:40'),
(440, 10, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:09:55'),
(441, 8, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:09:58'),
(442, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:10:02'),
(443, 1, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:10:05'),
(444, 1, 'Updated profile details and role for user: Accounting support', '{\"full_name\":\"Accounting Support\",\"email\":\"acctgsupp@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"Accounting (Finance - Accounting)\",\"employee_no\":\"N\\/A\",\"position\":\"N\\/A\",\"role\":\"Accounting Staff\",\"status\":\"active\"}', '{\"full_name\":\"Accounting Support\",\"email\":\"acctgsupp@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"Accounting (Finance - Accounting)\",\"employee_no\":\"\",\"position\":\"\",\"role\":\"Accounting Staff\",\"status\":\"active\"}', '::1', '2026-06-17 06:10:32'),
(445, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:10:33'),
(446, NULL, 'Failed login attempt', '{\"identity\":\"accountant@gmail.com\"}', NULL, '::1', '2026-06-17 06:10:48'),
(447, 1, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:10:50'),
(448, 1, 'Updated profile details and role for user: Accountant', '{\"full_name\":\"Accountant\",\"email\":\"accountant@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"Accounting (Finance - Accounting)\",\"employee_no\":\"N\\/A\",\"position\":\"N\\/A\",\"role\":\"Accounting Staff\",\"status\":\"active\"}', '{\"full_name\":\"Accountant\",\"email\":\"accountant@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"Accounting (Finance - Accounting)\",\"employee_no\":\"\",\"position\":\"\",\"role\":\"Accounting Staff\",\"status\":\"active\"}', '::1', '2026-06-17 06:11:05'),
(449, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:11:09'),
(450, NULL, 'Failed login attempt', '{\"identity\":\"accountant@gmail.com\"}', NULL, '::1', '2026-06-17 06:11:11'),
(451, NULL, 'Failed login attempt', '{\"identity\":\"accountant@gmail.com\"}', NULL, '::1', '2026-06-17 06:11:15'),
(452, NULL, 'Failed login attempt', '{\"identity\":\"accountant@gmail.com\"}', NULL, '::1', '2026-06-17 06:11:22'),
(453, 1, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:11:27'),
(454, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:11:37'),
(455, NULL, 'Failed login attempt', '{\"identity\":\"accountant@gmail.com\"}', NULL, '::1', '2026-06-17 06:11:42'),
(456, NULL, 'Failed login attempt', '{\"identity\":\"accountant\"}', NULL, '::1', '2026-06-17 06:11:52'),
(457, 1, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:11:54'),
(458, 1, 'Updated profile details, password, and role for user: Accountant', '{\"full_name\":\"Accountant\",\"email\":\"accountant@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"Accounting (Finance - Accounting)\",\"employee_no\":\"N\\/A\",\"position\":\"N\\/A\",\"role\":\"Accounting Staff\",\"status\":\"active\"}', '{\"full_name\":\"Accountant\",\"email\":\"accountant@gmail.com\",\"office\":\"OSDS\",\"unit_section\":\"Accounting (Finance - Accounting)\",\"employee_no\":\"\",\"position\":\"Accountant\",\"role\":\"Accounting Staff\",\"status\":\"active\",\"password_changed\":true}', '::1', '2026-06-17 06:12:23'),
(459, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:12:29'),
(460, 2, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:12:31'),
(461, 2, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:12:34'),
(462, 8, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:12:36'),
(463, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:16:29'),
(464, 10, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:16:34'),
(465, 10, 'Submitted new transaction: FAST-2026-000030', 'null', '{\"tracking_number\":\"FAST-2026-000030\",\"amount\":123123123,\"tax_amount\":0,\"net_amount\":123123123,\"tax_type\":null}', '::1', '2026-06-17 06:18:19'),
(466, 10, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:18:22'),
(467, 8, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:18:25'),
(468, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:18:40'),
(469, 10, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:18:44'),
(470, 10, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:19:06'),
(471, 8, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:19:11'),
(472, 8, 'Transaction Status changed: FAST-2026-000013', '{\"status\":\"Pending ACCT Support\"}', '{\"status\":\"Pending Signatories\",\"remarks\":\"adssdasdadsasda\"}', '::1', '2026-06-17 06:21:22'),
(473, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:21:24'),
(474, 3, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:21:28'),
(475, 3, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:28:36'),
(476, 8, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:28:40'),
(477, 8, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:29:38'),
(478, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:29:49'),
(479, 8, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:29:53'),
(480, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:30:08'),
(481, 10, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:30:13'),
(482, 10, 'Submitted new transaction: FAST-2026-000031', 'null', '{\"tracking_number\":\"FAST-2026-000031\",\"amount\":14000,\"tax_amount\":0,\"net_amount\":14000,\"tax_type\":null}', '::1', '2026-06-17 06:30:54'),
(483, 8, 'Updated tax classification to \'Goods\' for transaction: FAST-2026-000031', '{\"tax_type\":null}', '{\"tax_type\":\"Goods\"}', '::1', '2026-06-17 06:31:06'),
(484, 8, 'All attachments approved and tax classification set, transaction auto-advanced: FAST-2026-000031', '{\"status\":\"Pending ACCTG Support\"}', '{\"status\":\"Pending Budget\"}', '::1', '2026-06-17 06:31:17'),
(485, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:31:21'),
(486, 3, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:31:24'),
(487, 3, 'Budget check approved: FAST-2026-000031', '{\"status\":\"Pending Budget\"}', '{\"status\":\"Pending ACCT Support\",\"fund_source\":\"mooe\"}', '::1', '2026-06-17 06:31:40'),
(488, 3, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:31:42'),
(489, 8, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:31:45'),
(490, 8, 'Transaction Status changed: FAST-2026-000031', '{\"status\":\"Pending ACCT Support\"}', '{\"status\":\"Pending Signatories\",\"remarks\":\"noted boss\"}', '::1', '2026-06-17 06:32:04'),
(491, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:32:08'),
(492, 6, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:32:11'),
(493, 6, 'Both signatory tasks completed, auto-advanced: FAST-2026-000031', '{\"status\":\"Pending Signatories\"}', '{\"status\":\"Pending Cashier Release\"}', '::1', '2026-06-17 06:32:41'),
(494, 6, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:32:43'),
(495, 12, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:32:45'),
(496, 12, 'Transaction Status changed: FAST-2026-000031', '{\"status\":\"Pending Cashier Release\"}', '{\"status\":\"Released\",\"remarks\":\"sahod kana boss\"}', '::1', '2026-06-17 06:32:59'),
(497, 10, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:43:14'),
(498, 10, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:43:38'),
(499, 10, 'Submitted new transaction: FAST-2026-000032', 'null', '{\"tracking_number\":\"FAST-2026-000032\",\"amount\":28000,\"tax_amount\":0,\"net_amount\":28000,\"tax_type\":null}', '::1', '2026-06-17 06:45:16'),
(500, 10, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:45:32'),
(501, 8, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:45:35'),
(502, 8, 'Tax classification set and transaction auto-advanced: FAST-2026-000032', '{\"status\":\"Pending ACCTG Support\",\"tax_type\":null}', '{\"status\":\"Pending Budget\",\"tax_type\":\"Goods\"}', '::1', '2026-06-17 06:46:44'),
(503, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:46:51'),
(504, 3, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:46:54'),
(505, 3, 'Budget check approved: FAST-2026-000032', '{\"status\":\"Pending Budget\"}', '{\"status\":\"Pending ACCT Support\",\"fund_source\":\"MOOE\"}', '::1', '2026-06-17 06:47:21'),
(506, 3, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:47:26'),
(507, 8, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:47:29'),
(508, 8, 'Transaction Status changed: FAST-2026-000032', '{\"status\":\"Pending ACCT Support\"}', '{\"status\":\"Pending Signatories\",\"remarks\":\"for signatory\"}', '::1', '2026-06-17 06:48:00'),
(509, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:48:04'),
(510, 7, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:48:11'),
(511, 7, 'Both signatory tasks completed, auto-advanced: FAST-2026-000032', '{\"status\":\"Pending Signatories\"}', '{\"status\":\"Pending Cashier Release\"}', '::1', '2026-06-17 06:48:29'),
(512, 7, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:48:35'),
(513, 12, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:48:38'),
(514, 12, 'Transaction Status changed: FAST-2026-000032', '{\"status\":\"Pending Cashier Release\"}', '{\"status\":\"Released\",\"remarks\":\"release\"}', '::1', '2026-06-17 06:48:55'),
(515, 12, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:48:58'),
(516, 10, 'User login successful', NULL, NULL, '::1', '2026-06-17 06:49:04'),
(517, 10, 'User logout successful', NULL, NULL, '::1', '2026-06-17 06:49:18'),
(518, 3, 'User login successful', NULL, NULL, '::1', '2026-06-17 07:16:18'),
(519, 3, 'User logout successful', NULL, NULL, '::1', '2026-06-17 07:17:10'),
(520, 1, 'User login successful', NULL, NULL, '::1', '2026-06-17 07:17:18'),
(521, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-17 07:19:28'),
(522, 3, 'User login successful', NULL, NULL, '::1', '2026-06-17 07:19:32'),
(523, 3, 'User logout successful', NULL, NULL, '::1', '2026-06-17 07:44:01'),
(524, 2, 'User login successful', NULL, NULL, '::1', '2026-06-17 07:44:04'),
(525, 2, 'User logout successful', NULL, NULL, '::1', '2026-06-17 07:44:12'),
(526, 3, 'User login successful', NULL, NULL, '::1', '2026-06-17 07:44:46'),
(527, 3, 'User logout successful', NULL, NULL, '::1', '2026-06-17 07:45:33'),
(528, 12, 'User login successful', NULL, NULL, '::1', '2026-06-17 07:45:36'),
(529, 12, 'User logout successful', NULL, NULL, '::1', '2026-06-17 07:46:21'),
(530, 12, 'User login successful', NULL, NULL, '::1', '2026-06-17 07:46:24'),
(531, 12, 'User logout successful', NULL, NULL, '::1', '2026-06-17 07:46:29'),
(532, 3, 'User login successful', NULL, NULL, '::1', '2026-06-17 07:46:35'),
(533, 3, 'User logout successful', NULL, NULL, '::1', '2026-06-17 07:46:59'),
(534, 1, 'User login successful', NULL, NULL, '::1', '2026-06-17 07:47:05'),
(535, 1, 'Updated dynamic system tax configurations', '[{\"tax_type\":\"Foods\",\"tax_percentage\":\"2.00\",\"is_active\":1},{\"tax_type\":\"Goods\",\"tax_percentage\":\"5.00\",\"is_active\":1},{\"tax_type\":\"Services\",\"tax_percentage\":\"10.00\",\"is_active\":1}]', '[{\"tax_type\":\"Foods\",\"tax_percentage\":\"2.00\",\"is_active\":1},{\"tax_type\":\"Goods\",\"tax_percentage\":\"5.00\",\"is_active\":1},{\"tax_type\":\"Non - Taxable\",\"tax_percentage\":\"0.00\",\"is_active\":1},{\"tax_type\":\"Services\",\"tax_percentage\":\"10.00\",\"is_active\":1}]', '::1', '2026-06-17 07:47:27'),
(536, 1, 'User logout successful', NULL, NULL, '::1', '2026-06-17 07:47:29'),
(537, 8, 'User login successful', NULL, NULL, '::1', '2026-06-17 07:47:34'),
(538, 8, 'User logout successful', NULL, NULL, '::1', '2026-06-17 07:49:32'),
(539, 1, 'User login successful', NULL, NULL, '::1', '2026-06-17 07:49:35'),
(540, NULL, 'Workflow Migration v2: 5-Stage → 6-Stage', '[\"Pending Accountant 1\",\"Pending Support\",\"Pending Budget Check\",\"Pending Accountant 2\",\"Pending Final Approval\",\"Approved\"]', '[\"Pending ACCTG Support\",\"Pending ACCTG Support\",\"Pending Budget\",\"Pending ACCT Support\",\"Pending Signatories\",\"Pending Cashier Release\"]', 'CLI', '2026-06-17 07:53:01');

-- --------------------------------------------------------

--
-- Table structure for table `attachment_approvals`
--

CREATE TABLE `attachment_approvals` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_label` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attachment_approvals`
--

INSERT INTO `attachment_approvals` (`id`, `transaction_id`, `file_path`, `file_label`, `status`, `reviewed_by`, `reviewed_at`, `remarks`, `created_at`) VALUES
(1, 10016, 'uploads/transactions/f28836c5bf05003d45842bc9e110ee9e.pdf', 'Approved Travel Authority', 'approved', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(2, 10016, 'uploads/transactions/d1e4f9515eb9f0e6caeead073b96ef3e.docx', 'Travel Itinerary', 'approved', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(3, 10017, 'uploads/transactions/bb8fe6d3a85ecc15d16d894cbef43c51.docx', 'Approved Travel Authority', 'approved', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(4, 10017, 'uploads/transactions/1ef087a7cae54de488e53b36140ed943.pdf', 'Travel Itinerary', 'approved', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(5, 10018, 'uploads/transactions/47e3641a936a95eecb6f47a5adea6158.pdf', 'DTR Document', 'pending', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(6, 10018, 'uploads/transactions/3ef8f061248b3413586c547c78c8652e.pdf', 'Certificate', 'pending', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(7, 10018, 'uploads/transactions/f1a526f7986b7fdd138436fb80069b42.pdf', 'Bill / Proof of Payment', 'pending', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(8, 10019, 'uploads/transactions/6aeef8cfd7b39b566cfb8af2a5676982.pdf', 'DTR Document', 'pending', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(9, 10019, 'uploads/transactions/49790b8ff0101810c9e526faa17b0b9d.pdf', 'Certificate', 'pending', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(10, 10019, 'uploads/transactions/5cb2cb3f4c18106a9d82cb4a6d4ff66d.pdf', 'Bill / Proof of Payment', 'pending', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(11, 10020, 'uploads/transactions/ef839076b06f59c2ca486170ffbcda1c.docx', 'DTR Document', 'approved', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(12, 10020, 'uploads/transactions/113f27fc2c96c568828a1a49546f3c31.docx', 'Certificate', 'approved', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(13, 10020, 'uploads/transactions/cd6fb4d8d08918ac5a27ba9043114f89.pdf', 'Bill / Proof of Payment', 'approved', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(14, 10021, 'uploads/transactions/08c4a6a4e830b14b25e1d4387ee8d0ae.docx', 'Approved Travel Authority', 'pending', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(15, 10021, 'uploads/transactions/7017aabdb982b36af361860c0941bd17.docx', 'Travel Itinerary', 'pending', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(16, 10016, 'uploads/transactions/f28836c5bf05003d45842bc9e110ee9e.pdf', 'Approved Travel Authority', 'approved', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(17, 10016, 'uploads/transactions/d1e4f9515eb9f0e6caeead073b96ef3e.docx', 'Travel Itinerary', 'approved', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(18, 10017, 'uploads/transactions/bb8fe6d3a85ecc15d16d894cbef43c51.docx', 'Approved Travel Authority', 'approved', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(19, 10017, 'uploads/transactions/1ef087a7cae54de488e53b36140ed943.pdf', 'Travel Itinerary', 'approved', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(20, 10018, 'uploads/transactions/47e3641a936a95eecb6f47a5adea6158.pdf', 'DTR Document', 'pending', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(21, 10018, 'uploads/transactions/3ef8f061248b3413586c547c78c8652e.pdf', 'Certificate', 'pending', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(22, 10018, 'uploads/transactions/f1a526f7986b7fdd138436fb80069b42.pdf', 'Bill / Proof of Payment', 'pending', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(23, 10019, 'uploads/transactions/6aeef8cfd7b39b566cfb8af2a5676982.pdf', 'DTR Document', 'pending', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(24, 10019, 'uploads/transactions/49790b8ff0101810c9e526faa17b0b9d.pdf', 'Certificate', 'pending', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(25, 10019, 'uploads/transactions/5cb2cb3f4c18106a9d82cb4a6d4ff66d.pdf', 'Bill / Proof of Payment', 'pending', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(26, 10020, 'uploads/transactions/ef839076b06f59c2ca486170ffbcda1c.docx', 'DTR Document', 'approved', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(27, 10020, 'uploads/transactions/113f27fc2c96c568828a1a49546f3c31.docx', 'Certificate', 'approved', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(28, 10020, 'uploads/transactions/cd6fb4d8d08918ac5a27ba9043114f89.pdf', 'Bill / Proof of Payment', 'approved', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(29, 10021, 'uploads/transactions/08c4a6a4e830b14b25e1d4387ee8d0ae.docx', 'Approved Travel Authority', 'pending', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(30, 10021, 'uploads/transactions/7017aabdb982b36af361860c0941bd17.docx', 'Travel Itinerary', 'pending', NULL, NULL, NULL, '2026-06-17 03:30:14'),
(38, 10040, 'uploads/transactions/f35c5bafa3313e2962ae59bcbc7714ed.docx', 'Supporting Attachment: f35c5bafa3313e2962ae59bcbc7714ed.docx', 'approved', 8, '2026-06-17 03:54:07', '', '2026-06-17 03:52:12'),
(39, 10040, 'uploads/transactions/aba24ceea3b00e22058b49fd4ca1372c.docx', 'Supporting Attachment: aba24ceea3b00e22058b49fd4ca1372c.docx', 'approved', 8, '2026-06-17 03:54:09', '', '2026-06-17 03:52:12'),
(40, 10040, 'uploads/transactions/77883ec739c302a8819f95dc19d1b858.docx', 'Supporting Attachment: 77883ec739c302a8819f95dc19d1b858.docx', 'approved', 8, '2026-06-17 03:54:10', '', '2026-06-17 03:52:12'),
(41, 10040, 'uploads/transactions/44dab9e3c56d1bf40adcfe3de7b9bd56.docx', 'Supporting Attachment: 44dab9e3c56d1bf40adcfe3de7b9bd56.docx', 'approved', 8, '2026-06-17 03:54:11', '', '2026-06-17 03:52:12'),
(42, 10040, 'uploads/transactions/1828fa1b997625a763f0dae97e69febd.jpg', 'Supporting Attachment: 1828fa1b997625a763f0dae97e69febd.jpg', 'approved', 8, '2026-06-17 03:54:12', '', '2026-06-17 03:52:12'),
(43, 10040, 'uploads/transactions/a9d251409adbf1d538a6584f99640ad5.pdf', 'Approved Travel Authority', 'approved', 8, '2026-06-17 03:54:14', '', '2026-06-17 03:52:12'),
(44, 10040, 'uploads/transactions/e1b89b2bfd1c3c1a039580aca12262f6.docx', 'Travel Itinerary', 'approved', 8, '2026-06-17 03:54:15', '', '2026-06-17 03:52:12'),
(45, 10041, 'uploads/transactions/1f938de8b672a8cda26fd3f169330f8f.docx', 'Supporting Attachment: 1f938de8b672a8cda26fd3f169330f8f.docx', 'pending', NULL, NULL, NULL, '2026-06-17 06:02:33'),
(46, 10041, 'uploads/transactions/16548f31c32266b984164b23d11fd13d.docx', 'Supporting Attachment: 16548f31c32266b984164b23d11fd13d.docx', 'pending', NULL, NULL, NULL, '2026-06-17 06:02:33'),
(47, 10041, 'uploads/transactions/a2d60d623f88420ed6352417966f63c7.docx', 'Supporting Attachment: a2d60d623f88420ed6352417966f63c7.docx', 'pending', NULL, NULL, NULL, '2026-06-17 06:02:33'),
(48, 10041, 'uploads/transactions/ee167e1105d2c253eabc31016e30460e.docx', 'Supporting Attachment: ee167e1105d2c253eabc31016e30460e.docx', 'pending', NULL, NULL, NULL, '2026-06-17 06:02:33'),
(49, 10041, 'uploads/transactions/5f8364d0826a34eadea6ba6efdd69ce7.pdf', 'Supporting Attachment: 5f8364d0826a34eadea6ba6efdd69ce7.pdf', 'pending', NULL, NULL, NULL, '2026-06-17 06:02:33'),
(50, 10042, 'uploads/transactions/4424a7f95daa11a456314dbfb309bcb5.docx', 'Supporting Attachment: 4424a7f95daa11a456314dbfb309bcb5.docx', 'pending', NULL, NULL, NULL, '2026-06-17 06:08:49'),
(51, 10042, 'uploads/transactions/865c389c08e19562f449e4e63af46ff5.pdf', 'Supporting Attachment: 865c389c08e19562f449e4e63af46ff5.pdf', 'pending', NULL, NULL, NULL, '2026-06-17 06:08:49'),
(52, 10042, 'uploads/transactions/3a28b48b12d86d907ca61b26c6fee388.docx', 'Supporting Attachment: 3a28b48b12d86d907ca61b26c6fee388.docx', 'pending', NULL, NULL, NULL, '2026-06-17 06:08:49'),
(53, 10042, 'uploads/transactions/3c7d9fd96a17da8a7723aaf20790ea9f.docx', 'Supporting Attachment: 3c7d9fd96a17da8a7723aaf20790ea9f.docx', 'pending', NULL, NULL, NULL, '2026-06-17 06:08:49'),
(54, 10042, 'uploads/transactions/24940667f344d623f77a3a39f37a0e15.png', 'Activity Proposal', 'pending', NULL, NULL, NULL, '2026-06-17 06:08:49'),
(55, 10043, 'uploads/transactions/7d8ee7b7022c07433b52e023c74c8b6a.docx', 'Supporting Attachment: 7d8ee7b7022c07433b52e023c74c8b6a.docx', 'pending', NULL, NULL, NULL, '2026-06-17 06:18:19'),
(56, 10043, 'uploads/transactions/672b5668477264df44d47f4bcdca7b88.docx', 'Supporting Attachment: 672b5668477264df44d47f4bcdca7b88.docx', 'pending', NULL, NULL, NULL, '2026-06-17 06:18:19'),
(57, 10043, 'uploads/transactions/b9f07af7b067a9a8dda2806f16147792.docx', 'Supporting Attachment: b9f07af7b067a9a8dda2806f16147792.docx', 'pending', NULL, NULL, NULL, '2026-06-17 06:18:19'),
(58, 10043, 'uploads/transactions/6330a3b14864bd74654eade94aca6769.docx', 'Supporting Attachment: 6330a3b14864bd74654eade94aca6769.docx', 'pending', NULL, NULL, NULL, '2026-06-17 06:18:19'),
(59, 10043, 'uploads/transactions/19bc895ee107e9341d4640b3e17aea68.docx', 'Supporting Attachment: 19bc895ee107e9341d4640b3e17aea68.docx', 'pending', NULL, NULL, NULL, '2026-06-17 06:18:19'),
(60, 10044, 'uploads/transactions/041b675b4b36353c8e7eff78616bc449.docx', 'Supporting Attachment: 041b675b4b36353c8e7eff78616bc449.docx', 'approved', 8, '2026-06-17 06:31:13', '', '2026-06-17 06:30:54'),
(61, 10044, 'uploads/transactions/4f9c62f0f5754cab72f76b845f23dee9.docx', 'Supporting Attachment: 4f9c62f0f5754cab72f76b845f23dee9.docx', 'approved', 8, '2026-06-17 06:31:14', '', '2026-06-17 06:30:54'),
(62, 10044, 'uploads/transactions/bf2102dded4fbf14b52a33846a6f589c.docx', 'Supporting Attachment: bf2102dded4fbf14b52a33846a6f589c.docx', 'approved', 8, '2026-06-17 06:31:16', '', '2026-06-17 06:30:54'),
(63, 10044, 'uploads/transactions/e1c5aa1e98e3e4cf5de90d6587911147.png', 'Supporting Attachment: e1c5aa1e98e3e4cf5de90d6587911147.png', 'approved', 8, '2026-06-17 06:31:17', '', '2026-06-17 06:30:54'),
(64, 10045, 'uploads/transactions/27e83dceb6a96a22998f1a414aa4ee67.docx', 'Supporting Attachment: 27e83dceb6a96a22998f1a414aa4ee67.docx', 'approved', 8, '2026-06-17 06:46:02', '', '2026-06-17 06:45:16'),
(65, 10045, 'uploads/transactions/cd4b6bb0dfc75b819d98954dc9a6cb8e.docx', 'Supporting Attachment: cd4b6bb0dfc75b819d98954dc9a6cb8e.docx', 'approved', 8, '2026-06-17 06:46:06', '', '2026-06-17 06:45:16'),
(66, 10045, 'uploads/transactions/1a028e1d918cad9ac0b376f4a27e1154.docx', 'Supporting Attachment: 1a028e1d918cad9ac0b376f4a27e1154.docx', 'approved', 8, '2026-06-17 06:46:07', '', '2026-06-17 06:45:16'),
(67, 10045, 'uploads/transactions/575ad84789d1ebcbbbff2f01ff3a1192.png', 'Supporting Attachment: 575ad84789d1ebcbbbff2f01ff3a1192.png', 'approved', 8, '2026-06-17 06:46:10', '', '2026-06-17 06:45:16'),
(68, 10045, 'uploads/transactions/508ff4e7833594caecca62d8a404eed5.pdf', 'Supporting Attachment: 508ff4e7833594caecca62d8a404eed5.pdf', 'approved', 8, '2026-06-17 06:46:12', '', '2026-06-17 06:45:16'),
(69, 10045, 'uploads/transactions/8a57360a92bc32e9a552478f3d35eccf.pdf', 'Supporting Attachment: 8a57360a92bc32e9a552478f3d35eccf.pdf', 'approved', 8, '2026-06-17 06:46:14', '', '2026-06-17 06:45:16'),
(70, 10045, 'uploads/transactions/789a0b182b1cbc640658ef94dfd0e294.pdf', 'Approved Travel Authority', 'approved', 8, '2026-06-17 06:46:16', '', '2026-06-17 06:45:16'),
(71, 10045, 'uploads/transactions/27e68209c7d4861977d36296e1cc3b68.docx', 'Travel Itinerary', 'approved', 8, '2026-06-17 06:46:18', '', '2026-06-17 06:45:16'),
(72, 10016, 'uploads/transactions/f28836c5bf05003d45842bc9e110ee9e.pdf', 'Approved Travel Authority', 'approved', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(73, 10016, 'uploads/transactions/d1e4f9515eb9f0e6caeead073b96ef3e.docx', 'Travel Itinerary', 'approved', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(74, 10017, 'uploads/transactions/bb8fe6d3a85ecc15d16d894cbef43c51.docx', 'Approved Travel Authority', 'approved', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(75, 10017, 'uploads/transactions/1ef087a7cae54de488e53b36140ed943.pdf', 'Travel Itinerary', 'approved', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(76, 10018, 'uploads/transactions/47e3641a936a95eecb6f47a5adea6158.pdf', 'DTR Document', 'pending', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(77, 10018, 'uploads/transactions/3ef8f061248b3413586c547c78c8652e.pdf', 'Certificate', 'pending', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(78, 10018, 'uploads/transactions/f1a526f7986b7fdd138436fb80069b42.pdf', 'Bill / Proof of Payment', 'pending', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(79, 10019, 'uploads/transactions/6aeef8cfd7b39b566cfb8af2a5676982.pdf', 'DTR Document', 'pending', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(80, 10019, 'uploads/transactions/49790b8ff0101810c9e526faa17b0b9d.pdf', 'Certificate', 'pending', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(81, 10019, 'uploads/transactions/5cb2cb3f4c18106a9d82cb4a6d4ff66d.pdf', 'Bill / Proof of Payment', 'pending', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(82, 10020, 'uploads/transactions/ef839076b06f59c2ca486170ffbcda1c.docx', 'DTR Document', 'approved', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(83, 10020, 'uploads/transactions/113f27fc2c96c568828a1a49546f3c31.docx', 'Certificate', 'approved', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(84, 10020, 'uploads/transactions/cd6fb4d8d08918ac5a27ba9043114f89.pdf', 'Bill / Proof of Payment', 'approved', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(85, 10021, 'uploads/transactions/08c4a6a4e830b14b25e1d4387ee8d0ae.docx', 'Approved Travel Authority', 'pending', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(86, 10021, 'uploads/transactions/7017aabdb982b36af361860c0941bd17.docx', 'Travel Itinerary', 'pending', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(87, 10040, '[\"uploads\\/transactions\\/f35c5bafa3313e2962ae59bcbc7714ed.docx\",\"uploads\\/transactions\\/aba24ceea3b00e22058b49fd4ca1372c.docx\",\"uploads\\/transactions\\/77883ec739c302a8819f95dc19d1b858.docx\",\"uploads\\/transactions\\/44dab9e3c56d1bf40adcfe3de7b9bd56.docx\",\"u', 'Supporting Attachment: 44dab9e3c56d1bf40adcfe3de7b9bd56.docx\",\"u', 'approved', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(88, 10040, 'uploads/transactions/a9d251409adbf1d538a6584f99640ad5.pdf', 'Approved Travel Authority', 'approved', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(89, 10040, 'uploads/transactions/e1b89b2bfd1c3c1a039580aca12262f6.docx', 'Travel Itinerary', 'approved', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(90, 10041, '[\"uploads\\/transactions\\/1f938de8b672a8cda26fd3f169330f8f.docx\",\"uploads\\/transactions\\/16548f31c32266b984164b23d11fd13d.docx\",\"uploads\\/transactions\\/a2d60d623f88420ed6352417966f63c7.docx\",\"uploads\\/transactions\\/ee167e1105d2c253eabc31016e30460e.docx\",\"u', 'Supporting Attachment: ee167e1105d2c253eabc31016e30460e.docx\",\"u', 'pending', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(91, 10042, 'uploads/transactions/4424a7f95daa11a456314dbfb309bcb5.docx', 'Supporting Attachment: 4424a7f95daa11a456314dbfb309bcb5.docx', 'pending', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(92, 10042, 'uploads/transactions/865c389c08e19562f449e4e63af46ff5.pdf', 'Supporting Attachment: 865c389c08e19562f449e4e63af46ff5.pdf', 'pending', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(93, 10042, 'uploads/transactions/3a28b48b12d86d907ca61b26c6fee388.docx', 'Supporting Attachment: 3a28b48b12d86d907ca61b26c6fee388.docx', 'pending', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(94, 10042, 'uploads/transactions/3c7d9fd96a17da8a7723aaf20790ea9f.docx', 'Supporting Attachment: 3c7d9fd96a17da8a7723aaf20790ea9f.docx', 'pending', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(95, 10042, 'uploads/transactions/24940667f344d623f77a3a39f37a0e15.png', 'Activity Proposal', 'pending', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(96, 10043, '[\"uploads\\/transactions\\/7d8ee7b7022c07433b52e023c74c8b6a.docx\",\"uploads\\/transactions\\/672b5668477264df44d47f4bcdca7b88.docx\",\"uploads\\/transactions\\/b9f07af7b067a9a8dda2806f16147792.docx\",\"uploads\\/transactions\\/6330a3b14864bd74654eade94aca6769.docx\",\"u', 'Supporting Attachment: 6330a3b14864bd74654eade94aca6769.docx\",\"u', 'pending', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(97, 10044, 'uploads/transactions/041b675b4b36353c8e7eff78616bc449.docx', 'Supporting Attachment: 041b675b4b36353c8e7eff78616bc449.docx', 'approved', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(98, 10044, 'uploads/transactions/4f9c62f0f5754cab72f76b845f23dee9.docx', 'Supporting Attachment: 4f9c62f0f5754cab72f76b845f23dee9.docx', 'approved', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(99, 10044, 'uploads/transactions/bf2102dded4fbf14b52a33846a6f589c.docx', 'Supporting Attachment: bf2102dded4fbf14b52a33846a6f589c.docx', 'approved', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(100, 10044, 'uploads/transactions/e1c5aa1e98e3e4cf5de90d6587911147.png', 'Supporting Attachment: e1c5aa1e98e3e4cf5de90d6587911147.png', 'approved', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(101, 10045, '[\"uploads\\/transactions\\/27e83dceb6a96a22998f1a414aa4ee67.docx\",\"uploads\\/transactions\\/cd4b6bb0dfc75b819d98954dc9a6cb8e.docx\",\"uploads\\/transactions\\/1a028e1d918cad9ac0b376f4a27e1154.docx\",\"uploads\\/transactions\\/575ad84789d1ebcbbbff2f01ff3a1192.png\",\"up', 'Supporting Attachment: 575ad84789d1ebcbbbff2f01ff3a1192.png\",\"up', 'approved', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(102, 10045, 'uploads/transactions/789a0b182b1cbc640658ef94dfd0e294.pdf', 'Approved Travel Authority', 'approved', NULL, NULL, NULL, '2026-06-17 07:53:01'),
(103, 10045, 'uploads/transactions/27e68209c7d4861977d36296e1cc3b68.docx', 'Travel Itinerary', 'approved', NULL, NULL, NULL, '2026-06-17 07:53:01');

-- --------------------------------------------------------

--
-- Table structure for table `bac_sync_logs`
--

CREATE TABLE `bac_sync_logs` (
  `id` int(11) NOT NULL,
  `bac_reference_id` int(11) NOT NULL,
  `synced_by` int(11) DEFAULT NULL,
  `synced_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sync_status` varchar(50) NOT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bac_sync_logs`
--

INSERT INTO `bac_sync_logs` (`id`, `bac_reference_id`, `synced_by`, `synced_at`, `sync_status`, `remarks`) VALUES
(5, 9997, 1, '2026-05-29 14:13:01', 'SUCCESS', 'FAST tracking code assigned: FAST-2026-000003.'),
(7, 9998, 1, '2026-05-29 14:14:55', 'SUCCESS', 'FAST tracking code assigned: FAST-2026-000004.'),
(8, 35, 1, '2026-05-29 14:26:11', 'SUCCESS', 'FAST tracking code assigned: FAST-2026-000005.'),
(9, 34, 1, '2026-05-29 14:52:44', 'SUCCESS', 'FAST tracking code assigned: FAST-2026-000006.'),
(10, 27, 1, '2026-05-29 15:08:17', 'SUCCESS', 'FAST tracking code assigned: FAST-2026-000007.'),
(11, 33, 1, '2026-05-29 15:47:22', 'SUCCESS', 'FAST tracking code assigned: FAST-2026-000007.'),
(12, 32, 1, '2026-05-29 17:00:16', 'SUCCESS', 'FAST tracking code assigned: FAST-2026-000009.'),
(13, 23, 1, '2026-05-29 18:09:16', 'SUCCESS', 'FAST tracking code assigned: FAST-2026-000010.'),
(14, 31, 1, '2026-06-04 07:55:04', 'SUCCESS', 'FAST tracking code assigned: FAST-2026-000011.'),
(15, 36, 1, '2026-06-11 01:40:53', 'SUCCESS', 'FAST tracking code assigned: FAST-2026-000012.'),
(23, 9999, 1, '2026-06-17 03:36:49', 'SUCCESS', 'FAST tracking code assigned: FAST-2026-000027.');

-- --------------------------------------------------------

--
-- Table structure for table `budget_checks`
--

CREATE TABLE `budget_checks` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `fund_source` varchar(255) NOT NULL,
  `fund_source_tracking_number` varchar(255) DEFAULT NULL,
  `fund_available` tinyint(1) NOT NULL DEFAULT 1,
  `checked_by` int(11) NOT NULL,
  `checked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `budget_checks`
--

INSERT INTO `budget_checks` (`id`, `transaction_id`, `fund_source`, `fund_source_tracking_number`, `fund_available`, `checked_by`, `checked_at`, `remarks`) VALUES
(6, 10040, 'MOOE', NULL, 1, 3, '2026-06-17 03:55:34', 'okay sige'),
(7, 10044, 'mooe', NULL, 1, 3, '2026-06-17 06:31:40', 'noted to'),
(8, 10045, 'MOOE', NULL, 1, 3, '2026-06-17 06:47:21', 'copy noted');

-- --------------------------------------------------------

--
-- Table structure for table `cash_advance_details`
--

CREATE TABLE `cash_advance_details` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `inclusive_dates` varchar(255) DEFAULT NULL,
  `fund_source` varchar(255) DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `approved_ta_path` varchar(255) DEFAULT NULL,
  `travel_itinerary_path` varchar(255) DEFAULT NULL,
  `activity_proposal_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cash_advance_details`
--

INSERT INTO `cash_advance_details` (`id`, `transaction_id`, `category`, `inclusive_dates`, `fund_source`, `venue`, `approved_ta_path`, `travel_itinerary_path`, `activity_proposal_path`, `created_at`) VALUES
(1, 10016, 'MOOE', '2026-06-01 to 2026-06-05', 'MOOE', 'AGUSAN DEL SUR', 'uploads/transactions/f28836c5bf05003d45842bc9e110ee9e.pdf', 'uploads/transactions/d1e4f9515eb9f0e6caeead073b96ef3e.docx', NULL, '2026-06-11 03:08:20'),
(2, 10017, 'MOOE', '2026-06-09 to 2026-06-08', 'MOOE', 'PACITA 1', 'uploads/transactions/bb8fe6d3a85ecc15d16d894cbef43c51.docx', 'uploads/transactions/1ef087a7cae54de488e53b36140ed943.pdf', NULL, '2026-06-11 03:16:06'),
(3, 10021, 'MOOE', '2026-06-02 to 2026-06-05', 'MOOE', 'AGUSAN DEL SUR', 'uploads/transactions/08c4a6a4e830b14b25e1d4387ee8d0ae.docx', 'uploads/transactions/7017aabdb982b36af361860c0941bd17.docx', NULL, '2026-06-11 09:00:06'),
(4, 10022, 'Travel', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 10:54:08'),
(5, 10023, 'Training', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 10:54:08'),
(6, 10026, 'Travel', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 10:54:08'),
(7, 10027, 'Training', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 10:54:08'),
(10, 10040, 'Travel', '2026-06-08 to 2026-06-12', 'MOOE', 'Davao', 'uploads/transactions/a9d251409adbf1d538a6584f99640ad5.pdf', 'uploads/transactions/e1b89b2bfd1c3c1a039580aca12262f6.docx', NULL, '2026-06-17 03:52:12'),
(11, 10042, 'Training', '2026-07-03 to 2026-07-04', NULL, 'svnhs', NULL, NULL, 'uploads/transactions/24940667f344d623f77a3a39f37a0e15.png', '2026-06-17 06:08:49'),
(12, 10045, 'Travel', '2026-06-29 to 2026-07-03', 'MOOE', 'Boracay', 'uploads/transactions/789a0b182b1cbc640658ef94dfd0e294.pdf', 'uploads/transactions/27e68209c7d4861977d36296e1cc3b68.docx', NULL, '2026-06-17 06:45:16');

-- --------------------------------------------------------

--
-- Table structure for table `chatbot_logs`
--

CREATE TABLE `chatbot_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_message` text NOT NULL,
  `bot_response` text NOT NULL,
  `provider_used` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chatbot_logs`
--

INSERT INTO `chatbot_logs` (`id`, `user_id`, `user_message`, `bot_response`, `provider_used`, `created_at`) VALUES
(1, 1, 'What are the tax rates?', 'The current tax rates in the SDO FAST system are:\n\n* Goods: 5%\n* Foods: 2%\n* Services: 10%\n\nThese tax rates are applied to transactions based on the tax computation rules configured in the system.', 'Groq', '2026-05-28 13:53:12'),
(2, 1, 'How to track a DV?', 'To track a Disbursement Voucher (DV), follow these steps:\n\n1. Log in to the SDO FAST system.\n2. Click on the \"Progress Tracker\" tab.\n3. Search for the specific DV number you want to track.\n4. Once found, click on the DV number to view its status and details.\n\nAlternatively, you can also use the \"Transaction Search\" feature to find the DV by date, amount, or other criteria.\n\nIf you need further assistance or have questions about the status of your DV, please feel free to ask.', 'Groq', '2026-05-28 13:53:37'),
(3, 1, 'how to change profile name?', 'I\'m FAST AI, your SDO FAST Financial Accounting Virtual Assistant. I can help you with general inquiries related to FAST, but I need to clarify that changing your profile name is not a financial or accounting-related task.\n\nHowever, I can suggest that you check the \"User Profile\" or \"Account Settings\" section within the SDO FAST system. You may be able to update your profile information, including your name, from there.\n\nIf you need further assistance or have questions about a specific financial or accounting-related task, please feel free to ask.', 'Groq', '2026-05-28 13:53:54'),
(4, 1, 'explain the transaction workflow step by step and the roles involved', '**Transaction Workflow: A Step-by-Step Guide**\n\nThe transaction workflow in SDO FAST involves several stages and roles to ensure accurate and efficient processing of financial transactions. Here\'s a step-by-step breakdown of the workflow:\n\n### 1. Draft/Created\n\n*   **Requestor (User)**: Creates a new transaction by entering the required details, such as Event Name, Transaction Type, Amount, and attaching necessary documents (max 10MB, PDF, JPG, PNG, DOCX).\n*   The transaction is assigned a unique code in the format: FAST-YYYY-000001 (e.g., FAST-2026-000045).\n\n### 2. Pending Support\n\n*   **Accounting Support (Admin)**: Reviews the transaction attachments to ensure completeness and accuracy.\n*   Assigns the appropriate tax configuration type based on the transaction type.\n*   Issues a Document Voucher (DV) number to the transaction.\n\n### 3. Pending Accountant\n\n*   **Accountant (Admin)**: Verifies the transaction computations to ensure accuracy.\n*   Generates the BIR Form 2307 number if applicable (for tax-related transactions).\n*   Passes the transaction forward to the next stage.\n\n### 4. Pending Final Approval\n\n*   **Final Approver (User)**: Reviews the transaction details and verifies the accuracy of the computations.\n*   Approves or rejects the transaction based on the review.\n\n### 5. Final Statuses\n\n*   **Transaction Status**:\n    *   **Approved**: Funds are released, and the transaction is considered complete.\n    *   **Rejected**: The transaction is denied, and a remark is provided explaining the reason for rejection.\n    *   **Returned**: The transaction is sent back to the requestor for corrections.\n\n**Roles Involved:**\n\n*   **Requestor (User)**: Creates new transactions and submits them for processing.\n*   **Accounting Support (Admin)**: Reviews transaction attachments, assigns tax configurations, and issues DV numbers.\n*   **Accountant (Admin)**: Verifies transaction computations, generates BIR Form 2307 numbers, and passes transactions forward.\n*   **Final Approver (User)**: Reviews and approves or rejects transactions.\n\n**Tracking Progress:**\n\nTo track the progress of a transaction, refer to the **Progress Tracker** tab in the SDO FAST system. This tab provides real-time updates on the transaction\'s status throughout the workflow.', 'Groq', '2026-05-28 13:56:27'),
(5, 1, 'how to add a user?', '**Adding a New User in SDO FAST**\n\nTo add a new user in SDO FAST, follow these steps:\n\n1. **Log in as a Super Admin**: Ensure you have a Super Admin account to access the user management feature.\n2. **Navigate to User Management**: Click on the **Settings** icon (gear icon) on the top right corner of the dashboard, then select **User Management** from the dropdown menu.\n3. **Click on \"Add New User\"**: On the User Management page, click on the **Add New User** button.\n4. **Fill in User Details**:\n	* **Username**: Enter a unique username for the new user.\n	* **Email**: Enter the user\'s email address.\n	* **Password**: Set a strong password for the new user. Ensure it meets the password policy requirements.\n	* **Role**: Select the user\'s role from the dropdown menu (e.g., Admin, Accountant, Budget Officer, Requestor, Approver).\n5. **Assign Permissions**: Depending on the user\'s role, assign the necessary permissions by checking the relevant boxes.\n6. **Save User**: Click the **Save User** button to create the new user account.\n7. **Send Welcome Email**: The system will automatically send a welcome email to the new user with their login credentials.\n\n**Note**: After adding a new user, ensure they receive proper training and onboarding to familiarize them with the SDO FAST system.\n\n**Additional Tips**:\n\n* Ensure the new user\'s email address is valid and not already in use.\n* Use a unique username to avoid conflicts with existing users.\n* Assign the correct role and permissions to ensure the user can perform their tasks efficiently.\n* Regularly review and update user permissions to maintain system security and compliance.', 'Groq', '2026-05-28 13:58:24'),
(6, 1, 'How do I submit a transaction?', 'You can submit a transaction through the transactions module.\n\n<ol>\n  <li>Go to the <strong>Transactions</strong> menu.</li>\n  <li>Click <strong>New Transaction</strong>.</li>\n  <li>Fill in the <strong>Event Name</strong>, <strong>Transaction Type</strong>, and <strong>Amount</strong>.</li>\n  <li>Attach your documents (<strong>PDF</strong>, <strong>JPG</strong>, <strong>PNG</strong>, or <strong>DOCX</strong>, max <strong>10MB</strong>).</li>\n  <li>Click <strong>Submit</strong>.</li>\n</ol>\nDo you need help with a specific transaction type?', 'Groq', '2026-05-28 14:21:27'),
(7, 1, 'What is the capital of France?', 'That is outside my knowledge of the FAST system. Please contact your SDO Admin for assistance.\n\nIs there anything else you need help with?', 'Groq', '2026-05-28 14:21:27'),
(8, 1, 'how to log transaction?', 'You can log a transaction through the **Transactions** module.\n\n<ol>\n  <li>Go to the <strong>Transactions</strong> menu.</li>\n  <li>Click <strong>New Transaction</strong>.</li>\n  <li>Fill in the <strong>Event Name</strong>, <strong>Transaction Type</strong>, and <strong>Amount</strong>.</li>\n  <li>Attach your documents (<strong>PDF</strong>, <strong>JPG</strong>, <strong>PNG</strong>, or <strong>DOCX</strong>, max <strong>10MB</strong>).</li>\n  <li>Click <strong>Submit</strong> to send the transaction to the Accounting Support for review.</li>\n</ol>\nDo you need help with a specific transaction type or the next steps in the workflow?', 'Groq', '2026-05-28 14:21:56'),
(9, 1, 'hot chnage my proffile?', 'You can update your profile through the **User Profile** module.\n\n<ol>\n  <li>Go to the <strong>User Profile</strong> menu.</li>\n  <li>Click <strong>Edit Profile</strong> to make changes.</li>\n  <li>Update your <strong>Name</strong>, <strong>Email</strong>, and <strong>Phone Number</strong> as needed.</li>\n  <li>Change your <strong>Password</strong> by entering a new password and confirming it.</li>\n  <li>Click <strong>Save Changes</strong> to apply your updates.</li>\n</ol>\nIs there anything else you need help with?', 'Groq', '2026-05-28 14:22:10'),
(10, 1, 'how to add new role or position?', 'You can add a new role or position by configuring user roles in the system settings.\n\n<ol>\n  <li>Log in as a **Super Admin**.</li>\n  <li>Go to the **Settings** menu.</li>\n  <li>Click on **User Roles**.</li>\n  <li>Click **New Role**.</li>\n  <li>Enter the **Role Name** and **Description**.</li>\n  <li>Assign the necessary **Permissions** for the new role.</li>\n  <li>Click **Save** to create the new role.</li>\n</ol>\nIs there anything else you need help with?', 'Groq', '2026-05-28 14:26:28'),
(11, 1, 'what documents i can attach? what kind of document tyopes?', 'You can attach the following types of documents to a transaction in the FAST system:\n\n1. PDF (Portable Document Format)\n2. JPG (Joint Photographic Experts Group)\n3. PNG (Portable Network Graphics)\n4. DOCX (Microsoft Word document)\n\nThe maximum file size for attachments is 10MB.\n\nIs there anything else you need help with?', 'Groq', '2026-05-28 14:27:03'),
(12, 1, 'How do I submit a transaction?', 'You can submit a transaction through the transactions module.\n\n<ol>\n  <li>Go to the <strong>Transactions</strong> menu.</li>\n  <li>Click <strong>New Transaction</strong>.</li>\n  <li>Fill in the <strong>Event Name</strong>, <strong>Transaction Type</strong>, and <strong>Amount</strong>.</li>\n  <li>Attach your documents (<strong>PDF</strong>, <strong>JPG</strong>, <strong>PNG</strong>, or <strong>DOCX</strong>, max <strong>10MB</strong>).</li>\n  <li>Click <strong>Submit</strong>.</li>\n</ol>\nDo you need help with a specific transaction type?', 'Groq', '2026-05-28 14:29:13'),
(13, 1, 'What is the capital of France?', 'That is outside my knowledge of the FAST system. Please contact your SDO Admin for assistance.\n\nIs there anything else you need help with?', 'Groq', '2026-05-28 14:29:13'),
(14, 8, 'what is the current pending transaction?', 'You can view the current pending transaction through the **Transactions** module.\n<ol>\n  <li>Go to the <strong>Transactions</strong> menu.</li>\n  <li>Click on the <strong>Progress Tracker</strong> tab.</li>\n  <li>Look for the transaction with the status <strong>Pending Support</strong>, <strong>Pending Accountant</strong>, or <strong>Pending Final Approval</strong>.</li>\n  <li>Click on the transaction to view its details.</li>\n</ol>\nIs there anything else you need help with?', 'Groq', '2026-05-28 14:45:08'),
(15, 8, 'what kind is the transaction being filed now that is in my pending transaction now?', 'You can view the transaction type in the **Transaction Details** section of your pending transaction.\n\n1. Go to the **Transactions** menu.\n2. Click on the **Pending Transactions** tab.\n3. Find your transaction and click on the **View Details** button.\n4. Scroll down to the **Transaction Details** section.\n5. Look for the **Transaction Type** field.\n\nIs there anything else you need help with?', 'Groq', '2026-05-28 14:45:34'),
(18, 8, 'what tracking number is at the transaction right now in the cash advance', 'The current tracking number for the Cash Advance transaction is FAST-2026-000001.\n\n<ol>\n  <li>Go to the <strong>Cash Advance</strong> transaction.</li>\n  <li>Click on the <strong>Progress Tracker</strong> tab.</li>\n  <li>Look for the <strong>Transaction ID</strong> or <strong>Tracking Number</strong> field.</li>\n  <li>Copy and verify the tracking number.</li>\n</ol>\n\nIs there anything else you need help with?', 'Groq', '2026-05-28 14:55:37'),
(19, 8, 'what is that cash advance for?', 'A Cash Advance is a type of transaction in the SDO FAST system that allows personnel to request a cash advance for official business purposes.\n\n1. <li>Go to the <strong>Transactions</strong> menu.</li>\n2. <li>Click <strong>New Transaction</strong> and select <strong>Cash Advance</strong> as the <strong>Transaction Type</strong>.</li>\n3. <li>Fill in the <strong>Event Name</strong> and <strong>Amount</strong> requested for the cash advance.</li>\n4. <li>Attach required documents, such as a <strong>Travel Order</strong> or <strong>Meeting Invitation</strong>, to support the request.</li>\n5. <li>Click <strong>Submit</strong> to send the request to Accounting Support for review and processing.</li>\n\nIs there anything else you need help with?', 'Groq', '2026-05-28 14:55:52'),
(22, 8, 'what is that cash advance for?', 'A Cash Advance is a type of transaction in the SDO FAST system that allows users to request a cash advance for official business purposes.\n\n1. <li>Go to the <strong>Transactions</strong> menu.</li>\n2. <li>Click <strong>New Transaction</strong> and select <strong>Cash Advance</strong> as the <strong>Transaction Type</strong>.</li>\n3. <li>Fill in the <strong>Event Name</strong>, <strong>Amount</strong>, and attach required documents (e.g., receipts, invoices).</li>\n4. <li>Click <strong>Submit</strong> to send the transaction for review and approval.</li>\n\nIs there anything else you need help with?', 'Groq', '2026-05-28 14:59:29'),
(23, 8, 'what is that cash advance for?', 'A Cash Advance is a type of transaction in the SDO FAST system that allows personnel to request an advance payment for expenses related to official business, such as travel, training, or other authorized activities.\n\n1. Go to the **Transactions** menu.\n2. Click **New Transaction**.\n3. Select **Cash Advance** as the **Transaction Type**.\n4. Fill in the **Event Name**, **Amount**, and attach required documents (e.g., travel itinerary, receipts).\n5. Click **Submit**.\n\nIs there anything else you need help with?', 'Groq', '2026-05-28 14:59:42'),
(24, 8, 'what is the cash advance in the transaction is being for? like what it will be ued for base on the details?', 'A Cash Advance transaction in the SDO FAST system is used to request a cash advance from the School Division Office for a specific purpose.\n\n1. Go to the **Transactions** menu.\n2. Click **New Transaction**.\n3. Select **Cash Advance** as the **Transaction Type**.\n4. Fill in the **Event Name** with a description of the purpose for which the cash advance is being requested (e.g., \"Training and Development for Teachers\").\n5. Enter the **Amount** of the cash advance requested.\n6. Attach required documents (e.g., a detailed breakdown of expenses, a travel itinerary, or a list of training materials).\n7. Click **Submit**.\n\nThe cash advance will be used for the specific purpose described in the **Event Name** field. The requestor will be required to submit a reimbursement transaction after the expenses have been incurred and documented.\n\nIs there anything else you need help with?', 'Groq', '2026-05-28 15:00:11'),
(26, 8, 'who submitted the cash advance request in the transaction?', 'You can view the requestor of a cash advance transaction by checking the transaction details.\n\n<ol>\n  <li>Go to the <strong>Transactions</strong> menu.</li>\n  <li>Search for the specific cash advance transaction.</li>\n  <li>Click on the transaction to view its details.</li>\n  <li>Look for the <strong>Requestor</strong> field, which displays the name of the user who submitted the request.</li>\n</ol>\nIs there anything else you need help with?', 'Groq', '2026-05-28 15:04:18'),
(27, 8, 'who submitted the cash advance request in the transaction?', 'You can view the requestor of the cash advance transaction in the transaction details.\n\n1. Go to the <strong>Transactions</strong> menu.\n2. Click on the <strong>Transaction ID</strong> of the cash advance request.\n3. Click on the <strong>Details</strong> tab.\n4. Look for the <strong>Requestor</strong> field, which displays the name of the user who submitted the request.\n\nIs there anything else you need help with?', 'Groq', '2026-05-28 15:04:48'),
(29, 8, 'who submits latest cash advance transaction?', 'The latest cash advance transaction was submitted by Personnel (Role: Requestor, ID: 5).\n\n<ol>\n  <li>Go to the <strong>Transactions</strong> menu.</li>\n  <li>Click <strong>Transaction List</strong>.</li>\n  <li>Filter by <strong>Transaction Type</strong> as <strong>Cash Advance</strong>.</li>\n  <li>Sort by <strong>Transaction Date</strong> in <strong>Descending</strong> order.</li>\n  <li>The latest transaction will be at the top of the list.</li>\n</ol>\nIs there anything else you need help with?', 'Groq', '2026-05-28 15:12:51'),
(30, 8, 'how much is its gross amount?', 'You can find the gross amount of a transaction in the <strong>Transaction Details</strong> section.\n\n<ol>\n  <li>Go to the <strong>Transactions</strong> menu.</li>\n  <li>Click on the transaction you want to view.</li>\n  <li>Scroll down to the <strong>Transaction Details</strong> section.</li>\n  <li>The <strong>Gross Amount</strong> will be displayed.</li>\n</ol>\nIs there anything else you need help with?', 'Groq', '2026-05-28 15:13:08'),
(31, 5, 'who submits the latest cash advance transaction?', 'The latest cash advance transaction (<strong>FAST-2026-000005</strong>) was submitted by <strong>Personnel</strong>.\n\nIs there anything else you need help with?', 'Groq', '2026-05-28 15:25:12'),
(32, 5, 'how much is its gross amount?', 'The gross amount of the latest cash advance transaction (<strong>FAST-2026-000005</strong>) is PHP 20,000.00.\n\nIs there anything else you need help with?', 'Groq', '2026-05-28 15:25:12'),
(33, 5, 'what is its current status?', 'The latest cash advance transaction (<strong>FAST-2026-000005</strong>) is currently in the <strong>Pending Support</strong> stage.\n\nIs there anything else you need help with?', 'Groq', '2026-05-28 15:25:13'),
(34, 1, 'who submits the latest cash advance transaction?', 'The latest cash advance transaction (<strong>FAST-2026-000001</strong>) was submitted by <strong>FAST Super Admin</strong>.\n\nIs there anything else you need help with?', 'Groq', '2026-05-28 15:26:24'),
(35, 1, 'how much is its gross amount?', 'The gross amount of the latest cash advance transaction (<strong>FAST-2026-000001</strong>) is PHP 20,000.00.\n\nIs there anything else you need help with?', 'Groq', '2026-05-28 15:26:25'),
(36, 8, 'what is the current status of the latest cash advance transaction?', 'That is outside my knowledge of the FAST system. Please contact your SDO Admin for assistance. Is there anything else you need help with?', 'Groq', '2026-05-28 15:32:50'),
(37, 8, 'who submitted the transaction?', 'The transaction <strong>FAST-2026-000001</strong> was submitted by <strong>FAST Super Admin</strong>.  \nIs there anything else you need help with?', 'Groq', '2026-05-28 15:33:25'),
(38, 8, 'how much is the grossamounting to?', 'The gross amount for transaction <strong>FAST-2026-000001</strong> is PHP 20,000.00.  \nIs there anything else you need help with?', 'Groq', '2026-05-28 15:33:35'),
(39, 8, 'what is the event it says in there?', 'The event for transaction <strong>FAST-2026-000001</strong> is <strong>SDO SEMINAR</strong>.  \nIs there anything else you need help with?', 'Groq', '2026-05-28 15:33:49'),
(40, 8, 'status of it?', 'The current status of transaction <strong>FAST-2026-000001</strong> is <strong>Pending Support</strong>.  \nIs there anything else you need help with?', 'Groq', '2026-05-28 15:34:05'),
(41, 8, 'how many cash advances are in there?', 'There is <strong>1</strong> cash‑advance transaction visible to you (transaction <strong>FAST-2026-000001</strong>).  \nIs there anything else you need help with?', 'Groq', '2026-05-28 15:35:10'),
(42, 8, 'who submits the latest cash advance transaction?', 'The latest cash‑advance transaction (<strong>FAST-2026-000001</strong>) was submitted by <strong>FAST Super Admin</strong>.  \nIs there anything else you need help with?', 'Groq', '2026-05-28 15:36:19'),
(43, 8, 'how much is its gross amount?', 'The gross amount for transaction <strong>FAST-2026-000001</strong> is PHP 20,000.00.  \nIs there anything else you need help with?', 'Groq', '2026-05-28 15:36:20'),
(44, 8, 'what is its current status?', 'The current status of transaction <strong>FAST-2026-000001</strong> is <strong>Pending Support</strong>.  \nIs there anything else you need help with?', 'Groq', '2026-05-28 15:36:20'),
(45, 1, 'how much is the net amount in my cash advance?', 'The net amount of your cash advance transaction (<strong>FAST-2026-000001</strong>) is PHP 18,000.00.\n\nIs there anything else you need help with?', 'Groq', '2026-05-28 15:44:23'),
(46, 1, 'how can i submit to the sdo bac?', 'You can submit a transaction to the SDO‑BAC through the FAST portal’s integration feature.\n\n<ol>\n  <li>Log in to FAST and go to the <strong>Transactions</strong> menu.</li>\n  <li>Select <strong>New Transaction</strong> and fill in the required details.</li>\n  <li>In the <strong>Project Reference</strong> field, choose the SDO‑BAC project you want to link.</li>\n  <li>Attach the supporting documents (PDF, JPG, PNG, or DOCX, max 10 MB).</li>\n  <li>Click <strong>Submit</strong>; the system will automatically push the data to SDO‑BAC.</li>\n  <li>Verify the submission in the <strong>Progress Tracker</strong> tab for confirmation.</li>\n</ol>\nDo you need help with a specific project reference or document type?', 'Groq', '2026-05-28 15:49:14'),
(47, 1, 'WHAT IS THE CURRENT TRANSACTION RIGHT NOW , WHAT IS ITS EVENT NAME?', 'The current transaction is <strong>FAST-2026-000006</strong> with the event name <strong>Office renovation for ICT unit - renovation of ICT unit for tiles repair and pathway</strong>.\n\nIs there anything else you need help with?', 'Groq', '2026-05-29 15:01:37'),
(48, 1, 'WHERE I CAN FIND THE FILES BEING RECEIVED FROM THE BACTRACK?', 'You can view the files received from BACTRACK in the transaction details page.\n\n<ol>\n  <li>Log in to FAST and open the <strong>Transactions</strong> module.</li>\n  <li>Locate the desired transaction (e.g., <strong>FAST-2026-000006</strong>) in the list.</li>\n  <li>Click the transaction’s <strong>View Details</strong> button.</li>\n  <li>Scroll to the <strong>Attachments</strong> section; BACTRACK files appear there.</li>\n  <li>If needed, use the <strong>Download</strong> icon to open or save the file.</li>\n</ol>\nIs there anything else you’d like to know?', 'Groq', '2026-05-29 15:02:27'),
(49, 1, 'IS THERE NO SPECIFIC SECTION YET TO VIEW THE FILES BEING RECEIVED FROM BACTRACK?', 'There isn’t a separate “BACTRACK Files” tab; the files are shown within each transaction’s attachment area.\n\n<ol>\n  <li>Log in to FAST and go to the <strong>Transactions</strong> module.</li>\n  <li>Find the transaction you want (e.g., <strong>FAST-2026-000006</strong>) and click <strong>View Details</strong>.</li>\n  <li>Scroll down to the <strong>Attachments</strong> section; any files received from BACTRACK will be listed there.</li>\n  <li>Use the <strong>Download</strong> icon next to a file to open or save it.</li>\n</ol>\nIs there anything else you’d like assistance with?', 'Groq', '2026-05-29 15:03:39'),
(50, 1, 'THEN WHAT IS THE BACTRACK TRANSACTION WAS ALL ABOUT? THAT SECTION BEING AT THE TRANSACTION SUB MENU DROPDOWN', 'The BACTRACK transaction <strong>FAST-2026-000006</strong> is a procurement‑linked claim for <strong>Office renovation for the ICT unit</strong>. It covers tile repair and pathway work, with a <strong>gross amount of PHP 150,000.00</strong>, a <strong>tax amount of PHP 7,500.00</strong> (Goods tax at 5 %), and a <strong>net amount of PHP 142,500.00</strong>. The request was automatically generated from the SDO‑BAC procurement link <strong>BTJOR-202604-006</strong> and is currently in <strong>Pending Support</strong> status.\n\nIs there anything else you need help with?', 'Groq', '2026-05-29 15:04:24'),
(51, 1, 'I MEAN THE SEPCIFIC BACTRACK SECTION AFTER THE REIMBURSEMENT SECTION MENU, WHAT IS THAT ALL ABOUT? ITS PURPOSE?', 'The <strong>BACTRACK</strong> section in the FAST portal is a dedicated area that links procurement projects from the SDO‑BAC system to your financial claims.\n\n<ul>\n  <li>It shows transactions that were automatically created from SDO‑BAC procurement links.</li>\n  <li>Each entry displays the project reference, event description, amount, tax details, and current workflow status.</li>\n  <li>The purpose is to streamline the hand‑off between procurement and accounting, ensuring that all costs related to a BAC‑approved project are captured, verified, and reimbursed within FAST.</li>\n  <li>Users can view attached BAC documents, assign DV numbers, and process the claim without manually re‑entering data.</li>\n</ul>\n\nTo locate this section:\n\n<ol>\n  <li>Log in to FAST and open the <strong>Transactions</strong> module.</li>\n  <li>In the submenu dropdown, select <strong>Reimbursement</strong>.</li>\n  <li>Below the reimbursement options, click the <strong>BACTRACK</strong> tab.</li>\n  <li>The list of BAC‑linked transactions will appear, where you can view details and attachments.</li>\n</ol>\nIs there anything else you’d like to know?', 'Groq', '2026-05-29 15:04:57'),
(52, 1, 'EXPLAIN IT LIKE YOU ARE EXPLAINAING IT TO A 5 YEAR OLD', 'The <strong>BACTRACK</strong> part is like a special folder that holds the papers from the buying team.  \nWhen the buying team says “We need to fix the ICT room,” FAST makes a claim automatically and puts it in this folder.  \nYou can see the reason, the money amount, and any attached files all together, so the accounting team can check and pay it.\n\n<ol>\n  <li>Log in to FAST and open the <strong>Transactions</strong> menu.</li>\n  <li>Choose <strong>Reimbursement</strong> from the dropdown.</li>\n  <li>Click the <strong>BACTRACK</strong> tab that appears below.</li>\n  <li>Look at the list – each line is a “paper” from the buying team with its details.</li>\n</ol>\nIs there anything else you’d like to know?', 'Groq', '2026-05-29 15:05:30'),
(53, 1, 'SO THAT SECTION ISNT FOR RECEIVING FILES BEING SENT FROM BACTRACK SYSTEM?', 'The BACTRACK tab itself is not a separate inbox for files; it lists the procurement‑linked transactions. Any files sent from the BACTRACK system are attached to each individual transaction and can be viewed in that transaction’s <strong>Attachments</strong> section.\n\n<ol>\n  <li>Open FAST and go to the <strong>Transactions</strong> module.</li>\n  <li>Select <strong>Reimbursement</strong> from the dropdown, then click the <strong>BACTRACK</strong> tab.</li>\n  <li>Find the transaction you need (e.g., <strong>FAST-2026-000006</strong>) and click <strong>View Details</strong>.</li>\n  <li>Scroll down to the <strong>Attachments</strong> area; any BACTRACK files will be listed there.</li>\n  <li>Click the <strong>Download</strong> icon next to a file to open or save it.</li>\n</ol>\nIs there anything else you’d like help with?', 'Groq', '2026-05-29 15:06:00'),
(54, 1, 'THANKS!', 'You’re welcome! Is there anything else I can help you with?', 'Groq', '2026-05-29 15:06:27'),
(55, 1, 'now, does this system has a special dedicated section for the send, received from the bactrack?', 'No, FAST does not have a separate inbox or outbox just for files sent from the BACTRACK system. All BACTRACK‑linked transactions appear in the <strong>BACTRACK</strong> tab, and any files received from BACTRACK are attached to each individual transaction record.\n\n<ol>\n  <li>Log in to FAST and open the <strong>Transactions</strong> module.</li>\n  <li>Select <strong>Reimbursement</strong> from the dropdown, then click the <strong>BACTRACK</strong> tab.</li>\n  <li>Locate the transaction you need (e.g., <strong>FAST-2026-000006</strong>) and click <strong>View Details</strong>.</li>\n  <li>Scroll to the <strong>Attachments</strong> section to see files sent from BACTRACK.</li>\n  <li>Use the <strong>Download</strong> icon next to each file to open or save it.</li>\n</ol>\nIs there anything else you’d like to know?', 'Groq', '2026-05-29 15:49:24'),
(56, 1, 'How long is approval?', 'The approval time varies by transaction type and the workload of each approver, but typical timelines are:\n\n<ul>\n  <li><strong>Cash Advance</strong>: 1–2 business days after the accountant signs off.</li>\n  <li><strong>Reimbursement</strong>: 2–4 business days after the accountant’s verification.</li>\n  <li><strong>BACTRACK</strong> (procurement‑linked) : 3–5 business days after the accountant completes tax and DV assignment.</li>\n</ul>\nIf a claim exceeds these windows, you can check its current stage in the Progress Tracker or contact the responsible approver.\n\n<ol>\n  <li>Log in to FAST and go to the <strong>Transactions</strong> module.</li>\n  <li>Find the transaction (e.g., <strong>FAST-2026-000007</strong>) and click <strong>View Details</strong>.</li>\n  <li>Open the <strong>Progress Tracker</strong> tab to see the exact status and timestamps.</li>\n  <li>If the status shows “Pending Final Approval” for longer than the typical range, send a reminder to the assigned approver.</li>\n</ol>\nIs there anything else you need help with?', 'Groq', '2026-05-29 15:50:00'),
(57, 1, 'check the sidebar and look for integrations section, that is the deicated section for it', 'FAST does not have a separate <strong>Integrations</strong> sidebar tab for receiving files from the BACTRACK system. All BACTRACK‑linked transactions are shown in the <strong>BACTRACK</strong> tab of the <strong>Reimbursement</strong> module, and any files sent from BACTRACK appear as attachments on each individual transaction record.\n\n<ol>\n  <li>Log in to FAST and open the <strong>Transactions</strong> module.</li>\n  <li>Select <strong>Reimbursement</strong> from the dropdown, then click the <strong>BACTRACK</strong> tab.</li>\n  <li>Open a transaction (e.g., <strong>FAST-2026-000006</strong>) and scroll to the <strong>Attachments</strong> area to view files received from BACTRACK.</li>\n</ol>\nIs there anything else you’d like help with?', 'Groq', '2026-05-29 15:50:19'),
(58, 1, 'WHAT ARE THE SIDEBAR MENU, LIST THEM ALL', 'The FAST portal’s left‑hand sidebar contains the main navigation links for all system functions.\n\n<ol>\n  <li><strong>Dashboard</strong></li>\n  <li><strong>Transactions</strong></li>\n  <li><strong>Reimbursement</strong></li>\n  <li><strong>Cash Advance</strong></li>\n  <li><strong>Payroll</strong></li>\n  <li><strong>BACTRACK</strong></li>\n  <li><strong>Progress Tracker</strong></li>\n  <li><strong>Reports</strong></li>\n  <li><strong>Tax Settings</strong></li>\n  <li><strong>Integration</strong></li>\n  <li><strong>User Management</strong></li>\n  <li><strong>Activity Logs</strong></li>\n  <li><strong>System Settings</strong></li>\n  <li><strong>Help / Documentation</strong></li>\n</ol>\nIs there anything else you need help with?', 'Groq', '2026-05-29 15:50:57'),
(59, 1, 'WHAT WAS THAT NUMBER 10 DOES?', 'The <strong>Integration</strong> menu lets you manage connections between FAST and external systems (e.g., BACTRACK). From there you can configure API endpoints, view integration logs, set authentication tokens, and enable or disable specific data syncs.\n\nIs there anything else you’d like to know?', 'Groq', '2026-05-29 15:51:07'),
(60, 5, 'WHO ARE ALLOWED TO SUBMIT TRANSACTIONS?', 'Only <strong>Personnel</strong> (requestors) are permitted to submit transactions in the FAST system.\n\nIs there anything else you need help with?', 'Groq', '2026-05-29 16:28:09'),
(61, 5, 'WHY CANT BAC REQUESTOR PERSONELL CANT REQUEST A TRANSACTION FOR REIMBURSEMENT?', 'Only users with the <strong>Personnel</strong> role can create transaction requests. The <strong>BAC Requestor</strong> role is a standard user role that does not have the permission to submit reimbursement (or any) transactions. To request a reimbursement, you need to be assigned the Personnel role or have an admin grant you the appropriate requestor privileges.\n\nIs there anything else you need help with?', 'Groq', '2026-05-29 16:28:31'),
(62, 5, 'TRACK TRANSACTION FAST-2026-000010', 'The transaction <strong>FAST-2026-000010</strong> is currently in <strong>Pending Support</strong> status. It was submitted by <strong>FAST Super Admin</strong> on 2026-05-30 02:09:16, with a gross amount of PHP 150,000.00, tax amount PHP 7,500.00 (Goods tax), and net amount PHP 142,500.00. No Document Voucher or BIR 2307 number has been assigned yet, and there are no attached files. The transaction references BAC Project PR-0023 (NEGOTIATED_PROCUREMENT).\n\nIs there anything else you need help with?', 'Groq', '2026-05-29 18:10:55'),
(63, 1, 'what was the total net payout in the transac summary report?', 'The total net payout shown in the transaction summary report is <strong>PHP 1,095,500.00</strong>.\n\nIs there anything else you need help with?', 'Groq', '2026-05-31 15:16:32'),
(64, 1, 'who got the most system audit trails?', 'That is outside my knowledge of the FAST system. Please contact your SDO Admin for assistance. Is there anything else you need help with?', 'Groq', '2026-05-31 15:24:01'),
(65, 1, 'who got the most system audit trails?', 'That is outside my knowledge of the FAST system. Please contact your SDO Admin for assistance. Is there anything else you need help with?', 'Groq', '2026-05-31 15:24:11'),
(66, 1, 'whose user got the most system audit trails report?', 'That is outside my knowledge of the FAST system. Please contact your SDO Admin for assistance. Is there anything else you need help with?', 'Groq', '2026-05-31 15:24:36'),
(67, 1, 'whats the latest transaction now?', 'The latest transaction is <strong>FAST-2026-000010</strong>, created on 2026‑05‑30 02:09:16. It is a <strong>BACtrack</strong> transaction for <strong>NEGOTIATED_PROCUREMENT</strong> with a gross amount of PHP 150,000.00, tax PHP 7,500.00, and net amount PHP 142,500.00; its current status is <strong>Pending Support</strong>.\n\nIs there anything else you need help with?', 'Groq', '2026-05-31 15:24:56'),
(68, 1, 'why is it currently pending support status?', 'The transaction is in <strong>Pending Support</strong> because it has just been created from the SDO‑BAC procurement link. At this stage the system routes it to <strong>Accounting Support</strong> to review the attached documents, confirm the tax configuration, and assign a Document Voucher (DV) number before it can move to the Accountant for verification.\n\nIs there anything else you need help with?', 'Groq', '2026-05-31 15:25:22'),
(69, 1, 'what is the current workflow routing of the reviewing up until the final approving?', 'The transaction moves through several review stages before final approval.\n\n<ol>\n  <li>It is created by the requestor and set to <strong>Pending Support</strong>.</li>\n  <li><strong>Accounting Support</strong> reviews attachments, confirms the tax type, and assigns a <strong>Document Voucher (DV)</strong> number.</li>\n  <li>After support approval, the transaction moves to <strong>Pending Accountant</strong> for the Accountant to verify calculations and generate a <strong>BIR 2307</strong> number if needed.</li>\n  <li>Once the Accountant signs off, the transaction advances to <strong>Pending Final Approval</strong> where the designated Financial Approver (ASDS, SDS, or other) reviews and either approves or rejects it.</li>\n  <li>When approved, the status changes to <strong>Approved</strong> and funds are released.</li>\n</ol>\nIs there anything else you need help with?', 'Groq', '2026-05-31 16:47:40'),
(70, 1, 'so there is no budget office in there?', 'The standard FAST transaction workflow does not include a separate <strong>Budget Office</strong> step. After the requestor creates the transaction, it goes to <strong>Accounting Support</strong>, then to the <strong>Accountant</strong>, and finally to the <strong>Financial Approver</strong> for final sign‑off. The <strong>Budget Officer</strong> role exists in the system, but it is not part of this specific routing sequence.\n\nIs there anything else you need help with?', 'Groq', '2026-05-31 16:47:56'),
(71, 9, 'whats the current update in the transaction?', 'The transaction <strong>FAST-2026-000010</strong> is currently <strong>Approved</strong>. It was submitted by <strong>FAST Super Admin</strong> on 2026-05-30, with a gross amount of PHP 150,000.00, tax of PHP 7,500.00, and a net amount of PHP 142,500.00. Remarks: <strong>gege</strong>.  \nIs there anything else you need help with?', 'Groq', '2026-06-01 01:43:47'),
(72, 9, 'What are the tax rates?', 'The current tax rates are: <strong>Goods 5.00%</strong>, <strong>Foods 2.00%</strong>, and <strong>Services 10.00%</strong>.  \nIs there anything else you need help with?', 'Groq', '2026-06-01 05:06:26'),
(73, 9, 'does the RBAC already existing or still not yet?', 'Role‑based access control (RBAC) is already implemented in FAST; the system defines <strong>Super Admin</strong>, <strong>Admin</strong> (Accountant, Accounting Support, Budget Officer), and <strong>User</strong> (Personnel, Approvers) roles with specific permissions.  \nIs there anything else you need help with?', 'Groq', '2026-06-02 00:07:06'),
(74, 9, 'but in the user acc, it also sees the contents that are intended for the supreadmin only', 'FAST uses role‑based access control, so a <strong>User</strong> should not see items that are limited to the <strong>Super Admin</strong> role. If you’re seeing Super Admin‑only content, you may need to verify your role assignment and permission settings.\n\n<ol>\n  <li>Log in to FAST and go to the <strong>Profile</strong> or <strong>My Account</strong> section.</li>\n  <li>Check the <strong>Role</strong> displayed; it should show <strong>User</strong> (Personnel or Approver).</li>\n  <li>If the role is correct, navigate to the <strong>Settings → Permissions</strong> page (available to Admins) to see which modules are granted to your role.</li>\n  <li>Look for any items marked as <strong>Super Admin only</strong> that appear in your view; note their module names.</li>\n  <li>Report the discrepancy to your SDO Admin or Super Admin so they can adjust the role‑based permissions or correct any misconfiguration.</li>\n</ol>\n\nIs there anything else you need help with?', 'Groq', '2026-06-02 00:07:52'),
(75, 9, 'do i have access to view transactions of all or just mine?', 'As a <strong>User</strong> (Personnel or Approver) you can view only the transactions you submitted or that are assigned to you for approval. Viewing all transactions is reserved for <strong>Admin</strong> and <strong>Super Admin</strong> roles.\n\n<ol>\n  <li>Log in to FAST.</li>\n  <li>Go to the <strong>Transactions</strong> menu.</li>\n  <li>Select <strong>My Transactions</strong> to see only your own claims and approvals.</li>\n  <li>If you need to view every transaction, request an upgrade to an <strong>Admin</strong> or <strong>Super Admin</strong> role from your SDO Admin.</li>\n</ol>\n\nIs there anything else you need help with?', 'Groq', '2026-06-04 03:47:45'),
(76, 9, 'how can i connect with super admin?', 'You can reach the Super Admin through the following steps:\n\n<ol>\n  <li>Log in to FAST and click on your profile name at the top right corner.</li>\n  <li>Select <strong>Contact Support</strong> or <strong>Help Desk</strong> from the dropdown menu.</li>\n  <li>Choose the option to <strong>Send Message to Super Admin</strong> and fill in your request details.</li>\n  <li>Submit the form; the Super Admin will receive a notification and reply via email or the FAST messaging center.</li>\n  <li>Alternatively, you may use the organization’s internal directory or email system to email the Super Admin directly (e.g., superadmin@yourdomain.edu).</li>\n</ol>\n\nIs there anything else you need help with?', 'Groq', '2026-06-04 03:48:09'),
(77, 9, 'how to submit documents for cash advance', 'You can submit documents for a cash‑advance claim directly in the transaction submission form.\n\n<ol>\n  <li>Log in to FAST and go to the <strong>Transactions</strong> menu.</li>\n  <li>Click <strong>New Transaction</strong> and select <strong>Cash Advance</strong> as the transaction type.</li>\n  <li>Enter the required details such as <strong>Event Name</strong> and <strong>Amount</strong>.</li>\n  <li>In the <strong>Attachments</strong> section, click <strong>Add Files</strong> and upload your documents (PDF, JPG, PNG, or DOCX) – each file must be under <strong>10 MB</strong>.</li>\n  <li>Review the information, then click <strong>Submit</strong> to send the claim for review.</li>\n</ol>\n\nIs there anything else you need help with?', 'Groq', '2026-06-04 07:53:26'),
(78, 9, 'what is MOOE?', '<strong>MOOE</strong> stands for <strong>Maintenance and Other Operating Expenses</strong>. It covers routine costs needed to keep an office or program running, such as supplies, utilities, minor repairs, and other day‑to‑day expenses.  \n\nIs there anything else you need help with?', 'Groq', '2026-06-11 02:29:04'),
(79, 9, 'how to file for mooe?', 'To file a <strong>MOOE</strong> (Maintenance and Other Operating Expenses) claim in FAST, follow these steps:\n\n<ol>\n  <li>Log in to FAST and open the <strong>Transactions</strong> menu.</li>\n  <li>Click <strong>New Transaction</strong> and choose <strong>Reimbursement</strong> as the transaction type.</li>\n  <li>In the <strong>Event Name</strong> field, enter a brief description (e.g., “MOOE – Office Supplies – May 2026”).</li>\n  <li>Select the appropriate <strong>Category</strong> or add a custom tag such as <strong>MOOE</strong> if your system allows.</li>\n  <li>Enter the <strong>Gross Amount</strong> of the expense.</li>\n  <li>Attach supporting documents (receipts, invoices, or purchase orders) in PDF, JPG, PNG, or DOCX format; each file must be under <strong>10 MB</strong>.</li>\n  <li>Review the tax configuration (MOOE is usually non‑taxable, but verify with your Admin).</li>\n  <li>Click <strong>Submit</strong> to send the claim for Accounting Support review.</li>\n</ol>\n\nIs there anything else you need help with?', 'Groq', '2026-06-11 02:29:17'),
(80, 9, 'how can i file mooe thru cash advance?', 'You can request a cash‑advance specifically for <strong>MOOE</strong> expenses by creating a cash‑advance transaction and indicating the purpose.  \n\n<ol>\n  <li>Log in to FAST and go to the <strong>Transactions</strong> menu.</li>\n  <li>Click <strong>New Transaction</strong> and select <strong>Cash Advance</strong> as the transaction type.</li>\n  <li>In the <strong>Event Name</strong> field, type a clear description such as “MOOE – Office Supplies – June 2026”.</li>\n  <li>Enter the requested <strong>Gross Amount</strong> for the MOOE items.</li>\n  <li>In the <strong>Category/Tag</strong> or notes section, add the label <strong>MOOE</strong> so reviewers know the purpose.</li>\n  <li>Attach any supporting documents (e.g., purchase request, budget allocation) using the <strong>Add Files</strong> button; allowed formats are PDF, JPG, PNG, or DOCX and each file must be under <strong>10 MB</strong>.</li>\n  <li>Review the details, then click <strong>Submit</strong> to send the cash‑advance request to Accounting Support.</li>\n  <li>Once approved, use the cash‑advance funds to cover your MOOE expenses and later submit a reimbursement claim with receipts to settle the advance.</li>\n</ol>\n\nIs there anything else you need help with?', 'Groq', '2026-06-11 02:53:01'),
(81, 1, 'whatare the coverage types of the cash advances?', 'Cash advances can be covered by any of the three tax categories configured in FAST:\n\n<ul>\n  <li><strong>Goods</strong> – taxed at 5 %</li>\n  <li><strong>Foods</strong> – taxed at 2 %</li>\n  <li><strong>Services</strong> – taxed at 10 %</li>\n</ul>\n\nThe appropriate tax type is selected when the cash‑advance request is created, and the system computes the tax and net amounts accordingly.\n\nIs there anything else you need help with?', 'Groq', '2026-06-14 07:25:19'),
(82, 1, 'i mean the cash advance coverage type? diff type of cash advance', 'Cash advances are classified by the tax category applied to the request. The available coverage types are:\n\n<ul>\n  <li><strong>Goods</strong> – taxed at 5 %</li>\n  <li><strong>Foods</strong> – taxed at 2 %</li>\n  <li><strong>Services</strong> – taxed at 10 %</li>\n</ul>\n\nWhen you create a cash‑advance transaction, you select one of these categories, and FAST automatically computes the tax and net amounts.\n\nIs there anything else you need help with?', 'Groq', '2026-06-14 07:25:43'),
(83, 1, 'how many users are in here in the user management?', 'I don’t have the exact user count available right now. You can view it in the system’s User Management module.\n\n<ol>\n  <li>Log in to FAST with your admin credentials.</li>\n  <li>Navigate to the <strong>User Management</strong> menu.</li>\n  <li>Look for the summary panel or the user list header that shows the total number of users.</li>\n  <li>If needed, you can export the user list to see the count in a spreadsheet.</li>\n</ol>\nIs there anything else you need help with?', 'Groq', '2026-06-17 04:31:44');

-- --------------------------------------------------------

--
-- Table structure for table `document_details`
--

CREATE TABLE `document_details` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `dv_number` varchar(100) DEFAULT NULL,
  `bir_2307_number` varchar(100) DEFAULT NULL,
  `tax_type` varchar(50) DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `procurement_checklist` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`procurement_checklist`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `document_details`
--

INSERT INTO `document_details` (`id`, `transaction_id`, `dv_number`, `bir_2307_number`, `tax_type`, `attachment_path`, `procurement_checklist`, `created_at`) VALUES
(1, 1, 'DV-20260501', 'BIR-123456', 'Services', NULL, NULL, '2026-05-28 14:44:07'),
(9, 10004, 'DV-20260502', 'BIR-123457', 'Goods', NULL, '{\"purchase_request\":true,\"memorandum\":false,\"activity_proposal\":false,\"saro\":false}', '2026-05-29 14:13:01'),
(11, 10006, NULL, NULL, 'Goods', NULL, NULL, '2026-05-29 14:14:55'),
(12, 10007, NULL, NULL, 'Goods', NULL, '{\"purchase_request\":true,\"memorandum\":false,\"activity_proposal\":false,\"saro\":false}', '2026-05-29 14:26:11'),
(13, 10008, NULL, NULL, 'Goods', NULL, '{\"purchase_request\":true,\"memorandum\":false,\"activity_proposal\":false,\"saro\":false}', '2026-05-29 14:52:44'),
(15, 10010, NULL, NULL, 'Goods', NULL, '{\"purchase_request\":true,\"memorandum\":false,\"activity_proposal\":false,\"saro\":false}', '2026-05-29 15:47:22'),
(16, 10011, NULL, NULL, 'Services', 'uploads/transactions/6163837a34899864995eefbb96f4740b.docx', NULL, '2026-05-29 16:33:44'),
(17, 10012, NULL, NULL, 'Goods', NULL, '{\"purchase_request\":true,\"memorandum\":false,\"activity_proposal\":false,\"saro\":false}', '2026-05-29 17:00:16'),
(18, 10013, 'DV-20260504', 'BIR-123455', 'Goods', NULL, '{\"purchase_request\":true,\"memorandum\":false,\"activity_proposal\":false,\"saro\":false}', '2026-05-29 18:09:16'),
(19, 10014, NULL, NULL, 'Goods', NULL, '{\"purchase_request\":true,\"memorandum\":false,\"activity_proposal\":false,\"saro\":false}', '2026-06-04 07:55:04'),
(20, 10015, NULL, NULL, 'Goods', NULL, '{\"purchase_request\":true,\"memorandum\":false,\"activity_proposal\":false,\"saro\":false}', '2026-06-11 01:40:53'),
(21, 10016, 'DV-202605010', 'BIR-1234570', 'Services', NULL, NULL, '2026-06-11 03:08:20'),
(22, 10017, 'DV-20260508', 'BIR-1234568', 'Services', NULL, NULL, '2026-06-11 03:16:06'),
(23, 10018, NULL, NULL, 'Services', NULL, NULL, '2026-06-11 06:27:45'),
(24, 10019, NULL, NULL, 'Services', NULL, NULL, '2026-06-11 06:27:45'),
(25, 10020, NULL, NULL, 'Goods', NULL, NULL, '2026-06-11 08:45:22'),
(26, 10021, NULL, NULL, 'Foods', NULL, NULL, '2026-06-11 09:00:06'),
(27, 10022, NULL, NULL, 'Services', NULL, NULL, '2026-06-16 10:54:08'),
(28, 10023, NULL, NULL, 'Goods', NULL, NULL, '2026-06-16 10:54:08'),
(29, 10024, NULL, NULL, 'Services', NULL, NULL, '2026-06-16 10:54:08'),
(30, 10025, NULL, NULL, 'Services', NULL, NULL, '2026-06-16 10:54:08'),
(31, 10026, NULL, NULL, 'Services', NULL, NULL, '2026-06-16 10:54:08'),
(32, 10027, NULL, NULL, 'Goods', NULL, NULL, '2026-06-16 10:54:08'),
(33, 10028, NULL, NULL, 'Services', NULL, NULL, '2026-06-16 10:54:08'),
(34, 10029, NULL, NULL, 'Services', NULL, NULL, '2026-06-16 10:54:08'),
(45, 10040, 'DV-20260509', 'BIR-1234569', 'Services', '[\"uploads\\/transactions\\/f35c5bafa3313e2962ae59bcbc7714ed.docx\",\"uploads\\/transactions\\/aba24ceea3b00e22058b49fd4ca1372c.docx\",\"uploads\\/transactions\\/77883ec739c302a8819f95dc19d1b858.docx\",\"uploads\\/transactions\\/44dab9e3c56d1bf40adcfe3de7b9bd56.docx\",\"u', NULL, '2026-06-17 03:52:12'),
(46, 10041, NULL, NULL, NULL, '[\"uploads\\/transactions\\/1f938de8b672a8cda26fd3f169330f8f.docx\",\"uploads\\/transactions\\/16548f31c32266b984164b23d11fd13d.docx\",\"uploads\\/transactions\\/a2d60d623f88420ed6352417966f63c7.docx\",\"uploads\\/transactions\\/ee167e1105d2c253eabc31016e30460e.docx\",\"u', NULL, '2026-06-17 06:02:33'),
(47, 10042, NULL, NULL, NULL, '[\"uploads\\/transactions\\/4424a7f95daa11a456314dbfb309bcb5.docx\",\"uploads\\/transactions\\/865c389c08e19562f449e4e63af46ff5.pdf\",\"uploads\\/transactions\\/3a28b48b12d86d907ca61b26c6fee388.docx\",\"uploads\\/transactions\\/3c7d9fd96a17da8a7723aaf20790ea9f.docx\"]', NULL, '2026-06-17 06:08:49'),
(48, 10043, NULL, NULL, NULL, '[\"uploads\\/transactions\\/7d8ee7b7022c07433b52e023c74c8b6a.docx\",\"uploads\\/transactions\\/672b5668477264df44d47f4bcdca7b88.docx\",\"uploads\\/transactions\\/b9f07af7b067a9a8dda2806f16147792.docx\",\"uploads\\/transactions\\/6330a3b14864bd74654eade94aca6769.docx\",\"u', NULL, '2026-06-17 06:18:19'),
(49, 10044, 'DV-202605011', 'BIR-1234571', 'Goods', '[\"uploads\\/transactions\\/041b675b4b36353c8e7eff78616bc449.docx\",\"uploads\\/transactions\\/4f9c62f0f5754cab72f76b845f23dee9.docx\",\"uploads\\/transactions\\/bf2102dded4fbf14b52a33846a6f589c.docx\",\"uploads\\/transactions\\/e1c5aa1e98e3e4cf5de90d6587911147.png\"]', NULL, '2026-06-17 06:30:54'),
(50, 10045, 'DV-202605012', 'BIR-1234572', 'Goods', '[\"uploads\\/transactions\\/27e83dceb6a96a22998f1a414aa4ee67.docx\",\"uploads\\/transactions\\/cd4b6bb0dfc75b819d98954dc9a6cb8e.docx\",\"uploads\\/transactions\\/1a028e1d918cad9ac0b376f4a27e1154.docx\",\"uploads\\/transactions\\/575ad84789d1ebcbbbff2f01ff3a1192.png\",\"up', NULL, '2026-06-17 06:45:16');

-- --------------------------------------------------------

--
-- Table structure for table `integration_logs`
--

CREATE TABLE `integration_logs` (
  `id` int(11) NOT NULL,
  `source_system` varchar(100) NOT NULL,
  `destination_system` varchar(100) NOT NULL,
  `payload_type` varchar(100) NOT NULL,
  `reference_id` varchar(100) DEFAULT NULL,
  `sync_status` varchar(50) NOT NULL,
  `response_message` text DEFAULT NULL,
  `synced_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `integration_logs`
--

INSERT INTO `integration_logs` (`id`, `source_system`, `destination_system`, `payload_type`, `reference_id`, `sync_status`, `response_message`, `synced_at`) VALUES
(1, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 13:57:35'),
(2, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 13:57:43'),
(3, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 13:57:44'),
(4, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 13:57:46'),
(5, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 13:58:00'),
(6, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 13:58:02'),
(7, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 13:58:03'),
(8, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 13:58:05'),
(9, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 13:58:07'),
(10, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 13:58:38'),
(11, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 13:58:41'),
(12, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 13:58:42'),
(13, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 13:58:48'),
(14, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 14:01:56'),
(15, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 14:01:56'),
(16, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 14:05:52'),
(17, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 14:05:52'),
(18, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 14:06:35'),
(19, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 14:06:35'),
(20, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 14:06:46'),
(21, 'SDO-BAC', 'SDO-FAST', 'INBOUND_CONNECT', NULL, 'FAILED_AUTH', 'Unauthorized Bearer Token supplied by integration request.', '2026-05-29 14:06:46'),
(35, 'SDO-BAC', 'SDO-FAST', 'PROCUREMENT_APPROVED', 'BT-TEST-0001', 'FAILED', 'Approval document is required. Filename:  | Sender: SDO-BAC | PR Number: ', '2026-05-29 14:14:55'),
(36, 'SDO-BAC', 'SDO-FAST', 'PROCUREMENT_APPROVED', 'PR-9998', 'SUCCESS', 'Draft generated successfully: FAST-2026-000004 | Filename: approval_proof_test.pdf | Sender: SDO-BAC | PR Number: PR-9998 | Timestamp: 2026-05-29 16:14:55', '2026-05-29 14:14:55'),
(37, 'SDO-BAC', 'SDO-FAST', 'PROCUREMENT_APPROVED', 'BTNE0-202604-007', 'INVALID_PAYLOAD', 'Missing required payload parameters (reference_number, reference_id, particulars, amount).', '2026-05-29 14:24:41'),
(38, 'SDO-BAC', 'SDO-FAST', 'PROCUREMENT_APPROVED', 'PR-0035', 'SUCCESS', 'Draft generated successfully: FAST-2026-000005 | Filename: mock_approval.pdf | Sender: SDO-BAC | PR Number: PR-0035 | Timestamp: 2026-05-29 16:26:11', '2026-05-29 14:26:11'),
(39, 'SDO-BAC', 'SDO-FAST', 'PROCUREMENT_APPROVED', 'PR-0034', 'SUCCESS', 'Draft generated successfully: FAST-2026-000006 | Filename: 34_purchase_request_1780065130.pdf | Sender: SDO-BAC | PR Number: PR-0034 | Timestamp: 2026-05-29 16:52:44', '2026-05-29 14:52:44'),
(40, 'SDO-BAC', 'SDO-FAST', 'PROCUREMENT_APPROVED', 'PR-0033', 'SUCCESS', 'Draft generated successfully: FAST-2026-000007 | Filename: ESCALL-DTR (MAY4-19).pdf | Sender: SDO-BAC | PR Number: PR-0033 | Timestamp: 2026-05-29 17:47:22', '2026-05-29 15:47:22'),
(41, 'SDO-BAC', 'SDO-FAST', 'PROCUREMENT_APPROVED', 'BTLWD-202604-005', 'SUCCESS', 'Draft generated successfully: FAST-2026-000007 | Filename: approval_6a19b50a3fc48_PR-0033.pdf | Sender: SDO-BAC | PR Number: BTLWD-202604-005 | Timestamp: 2026-05-29 18:24:32', '2026-05-29 16:24:32'),
(42, 'SDO-BAC', 'SDO-FAST', 'PROCUREMENT_APPROVED', 'BTK1E-202604-004', 'SUCCESS', 'Draft generated successfully: FAST-2026-000009 | Filename: approval_6a19c616498c6_PR-0032.pdf | Sender: SDO-BAC | PR Number: BTK1E-202604-004 | Timestamp: 2026-05-29 19:00:16', '2026-05-29 17:00:16'),
(43, 'SDO-BAC', 'SDO-FAST', 'PROCUREMENT_APPROVED', 'PR-0023', 'SUCCESS', 'Draft generated successfully: FAST-2026-000010 | Filename: approval_6a19d601a323d_PR-0023.pdf | Sender: SDO-BAC | PR Number: PR-0023 | Timestamp: 2026-05-29 20:09:16', '2026-05-29 18:09:16'),
(44, 'SDO-FAST', 'SDO-BAC', 'FINANCIAL_COMPLETED', 'PR-0023', 'FAILED', 'HTTP API Error Code 401. Response: {\"success\":false,\"message\":\"Unauthorized: invalid or missing token.\"}', '2026-05-31 16:44:54');
INSERT INTO `integration_logs` (`id`, `source_system`, `destination_system`, `payload_type`, `reference_id`, `sync_status`, `response_message`, `synced_at`) VALUES
(45, 'SDO-FAST', 'SDO-BAC', 'DV_CREATED', 'BTLWD-202604-005', 'SUCCESS', '{\"reference_number\":\"BTLWD-202604-005\",\"event_type\":\"DV_CREATED\",\"system_token\":\"fast_secure_token_456\",\"payload\":{\"fast_reference_number\":\"FAST-2026-000007\",\"fast_financial_status\":\"Pending Support\",\"dv_number\":\"\",\"remarks\":\"Automatically generated draft from SDO-BAC procurement link: BTLWD-202604-005.\",\"synced_at\":\"2026-06-01 07:07:45\",\"checklist_files\":{\"purchase_request\":{\"base64_file\":\"JVBERi0xLjcNCiW1tbW1DQoxIDAgb2JqDQo8PC9UeXBlL0NhdGFsb2cvUGFnZXMgMiAwIFIvTGFuZyhlbikgL1N0cnVjdFRyZWVSb290IDIyIDAgUi9NYXJrSW5mbzw8L01hcmtlZCB0cnVlPj4vTWV0YWRhdGEgODcyIDAgUi9WaWV3ZXJQcmVmZXJlbmNlcyA4NzMgMCBSPj4NCmVuZG9iag0KMiAwIG9iag0KPDwvVHlwZS9QYWdlcy9Db3VudCAxL0tpZHNbIDMgMCBSXSA+Pg0KZW5kb2JqDQozIDAgb2JqDQo8PC9UeXBlL1BhZ2UvUGFyZW50IDIgMCBSL1Jlc291cmNlczw8L0ZvbnQ8PC9GMSA1IDAgUi9GMiA5IDAgUi9GMyAxMSAwIFIvRjQgMTMgMCBSL0Y1IDE1IDAgUi9GNiAxNyAwIFIvRjcgMTkgMCBSPj4vRXh0R1N0YXRlPDwvR1M3IDcgMCBSL0dTOCA4IDAgUj4+L1Byb2NTZXRbL1BERi9UZXh0L0ltYWdlQi9JbWFnZUMvSW1hZ2VJXSA+Pi9NZWRpYUJveFsgMCAwIDYxMiA3OTJdIC9Db250ZW50cyA0IDAgUi9Hcm91cDw8L1R5cGUvR3JvdXAvUy9UcmFuc3BhcmVuY3kvQ1MvRGV2aWNlUkdCPj4vVGFicy9TL1N0cnVjdFBhcmVudHMgMD4+DQplbmRvYmoNCjQgMCBvYmoNCjw8L0ZpbHRlci9GbGF0ZURlY29kZS9MZW5ndGggMTU4MDM+Pg0Kc3RyZWFtDQp4nLR929IeRXLtPRG8w38pOeCnz4cJhyM4aBjGgNhIzHjs8IWMBCgGEFuA957H3G+0uyszq0+VK7Pq++0Ia4S6eq38squ71uqqrL774NkvL36+++d\\/\\/uCLjz\\/75K764PMXP39\\/9+jVz+9\\/8+zxv\\/zL3UeffHz3v999p7qv1v+b6+auuhuWP8e5uXv76t13\\/vpPdz+\\/+85Hz99954M\\/1nfLgeffvftOvTSq7uq7sbmvmv5urKb7frp7\\/tPS6NNn4933vy6Ad9+H\\/5r4vz59953\\/eHT3+D\\/vnv\\/53XeeLHD\\/69137p58EcjnKcDUzXDf9XddPdzX7d3Qdfdzv4thaVhX49Lybhy6++6ubu+7lXzX5O74Y2vlx1ow9Gubu\\/nwY+u6ue9aPmv9reEnpn\\/Z7sfVTbX+Kjqtr+4nFHGjRQxBlHi7\\/n7q8+Md6\\/tGTusmK8etFrEBo8Q899vv9Mfc1PP9MAnZvPwBIu6UiDFIOt5m6aTNmB\\/vMN1PO6oG57jXIjZglJjn9j4\\/xW093dfuiAclYgMlHXDbNff5OW6H8b6V23WocJ8YtXghiBLuPN73TXa8XTPej0I1DvcDzvCkRGzBpGPuhn592GbF\\/MGHb397\\/d2Lb3+7+2j5r\\/j44Gdr39Kzdb3E9Zo49ZfMxlMaINGPaZUHdd\\/bP+Y2np5OdPAkBoTA107t0rdxgmpt0LaRlMAnesomA\\/\\/k8ft19ejJVx8+rptHX4f\\/eB7+\\/OJxXT968uX6J\\/3DXfjz6eOmefTH3T88e\\/rx2uaz8B8frn\\/9fP2DDv51+XNBedy0y7+u\\/\\/DHrRmRhWN3279+Gf76yQbBAf5lbfeEQJ5+JcFRuJdLXJiorp3vey1RrtsinLDSh7GwXQbulbe6HwL3d\\/+0O9jxoe5waNAO1fcVHVrkQHM8Fofe9Z8XVVdfDjNqR8Jrf3QbBZd\\/bufl\\/46Hl5AqOtydjgyzHEmeuBzuJmEdTqxxXEieux6NAU\\/nU+MjOn3uIM\\/D5fl9OLI9KZMnds20ytD18PIYPaepr7r7endJlyw3h8saG3S7w93lqoceuR6oU+hyNJx+aHF+3LbLEDnJ02vtNk0PHiiWMMZo+Knbduu5dLMYT9wyGnroWjSJBy7R0e1vpshS4hBMCZ38zbjo8mYf+b9uD7uvPts9cO\\/Wf\\/ko\\/JX++dn6yKO\\/fnq3Pp4\\/38786PzE5Ifxh\\/TgXP7Y0fyJQeNDl8gvZ39MT9fwcP1q93im8\\/lx+4wi+ov54P5s+1f6gU8+XU\\/ZRcD\\/fH3Yb+PFnozO\\/Fuig5VfpGa9o6dE\\/3rqHuOePaHA\\/vLZeo0oh0+ePWSQ7SJFZ8e9lh6T5NlDxOtTZUe7f\\/zEBuH5c2p0fgI1zX07+RRfrZpGBxZ++jTdeqbr6VNCQs8eiyTx7CEyl9rT\\/KkDSunS1dJqTIT9r+l7\\/9O\\/7eQYdfE\\/pZ8ufNPGni+Abfvom\\/DXL+nWOEk89JT6SGm8\\/PFp+Ovf1BspOzHdkpE+43qmbyOiVcfweNgexOv6fh6dt5D2TsCDhW+h5ZzlTNctVEJCt5BFkriFiMx1C2mvHxxQyi20atYpEfbHWx999qdt1Kax4unX27+Q8Qlm6Wu+V+Jtw0Pr0\\/Nw+IXa1bN\\/QDuGl6ruvKe7OtGqXT0etrt6Vd+P+57TgKupvZzxYOGuXrVurVpCQl3dIkl0dSIL73mN3GivgWwkJWh+abye5w2a7SZR8ateI2z1nY8DC785zghcrC6TzdStQdiN9iLGRsKvj3OC5ne+kcq+gRrN7nmw4DvkjLjZ4eeErVkwBxR8kZwTNb9cIK6hsruIJm1tJPw2OSNoebFBVMuD374hG010erDwK2Vv4OkRh3gP2dqPOPGwjDiNNuIMc+32J40hriAWHHGG5b5x+pMikp7ONEiuIw6TecRVY4grBKVZ7mF9c3gN+5NNCpEN+VwM\\/+Krn2\\/\\/TC9\\/SUvxW4qPt3fTX\\/OLBEVL5cfb9tN6T7jTnOzZTKtpqe2wqaWGqXLbhsbQUhAL9+yp8dqGIhLq2RZJomcTmatnG2IKQVHYXWJ8X3v2JWzyB2\\/Wfvt254N\\/W\\/vrD+Gvr5Z\\/malnh2Y\\/U4MfNtfwJvz53XqK+gYsP+S2Xh74iYi7B6RomiTF+w9J0Sco7t5fl7l0d8+\\/\\/Y9H9fyAbG2KLXkHlFNM96mbLHSQD+mx+IBsfZj7+J\\/9RYsGGq8c+2vUVM3wgITLGWPGMyT92CZa9bEdD9uP7XViansKtlCeGXPkEIuy0SuP7WH2WuAiEnpsWySnx\\/awJLIf1kTOc5h5a8BTu0Wz4xhJs8DDaoGHRb8eg6aH9u\\/rHfdj+OuP28OY3ma++OnVH9K3SFEgZGmvgZiWli9VM3Xri1\\/ct1rVHTqwFEG3PH3XYe9y2Z88270v+3wn6fbLCp5cEnhDJFN\\/n+rk721X7m7\\/1wdkXnzbnGDe\\/fQn\\/xbWWISfzvNcX2\\/B\\/PnpOo0009wXda9\\/f8D42nkKT8PErXnJSvjrw1F3db1a4VT3eL4mJKTo493sw9OHJO8X8v5Cvjyyq3kKo87dg\\/7W5aZP0CXT\\/ICsg8r6kCRLD3KQ4PGUeCteenUZT+NhGk9bYPDp8eqyQa06u+\\/AwuPpopy8NqiEhMZTiyQxIhCZXF+cHe0dlgeLAh+UEeES+Ocvfl0fcr9td4OMpOSC1offe8oT6WH+SqP667erj\\/qVXdh5WF+j4IDeW59QDxeGdjvmp7idF12b6hvBIrx++fLHV3dxuHkRf82S\\/AfObFyw8X85mw\\/3G7uZHjne\\/q8+cuq4Di8sjzstfltbtJNElpL5EULWfR1ghIh+oPbycjtsv7xcJGnvfbap6wccWPjZ1sUyD\\/PZVkJCzzaLJPGKh8hkkgvlRn2xayIpQTdhuiyclzddxlTbFBcKW33l6sBSpsvWpeF5gcvNwWTz3gKnw9ZeNtpIyqwTjyI5QdMU10bluIG014AeLCXwMczz5QROk1xZcWsvCRxQytxTs87yZUVNs1zMJbNcIOhOc+82khLz2ARfkxE0z3Ex1TbHhcLWTLMHS5kva1sqe3MGnh7biFd9PRUP26+n2jB9tz3B4XLZzhLUGA0PO+3oltRlNDTwWDSJgYfo5tmXI3XVowtNX1Nc9YngP9zmFl69CHMJPF+wW7n366+vV0X4ffgXmmD4KShunmz4g\\/rytSTUpqbJkIw8y0BBdJtSxHk2CvwMNPxK6xL8R7tZx0\\/\\/dr+5g2fPtynI+7O9CUuh\\/x812DkKbfFjAKfVj0HD75Zjyls0TWSX\\/Nh26sNDzHul0o8iYuZHUWJ58dZAHkZgefHQdMc7Gz2crel7hIUfRcvdtpzpehSVkNCDyCJJPIiIzKGAO2v2XkfCCpjOy1LAdIpLAXfqvLUDCyvgjMDlnmIyWwF3qpQ0kbACzgmaVWukctxAmpL0YGEFnBE4y9aMuHtVTNpQUAHnRM26lU5xKOBek5I2ElbAGUGLaqVTXAq41xSfBwsrYG\\/g6rDT9vDtztKi55tOCv9iEypi5CZbWeC1VbcBSTnjsRH3OYxElY0b0JQC4n5gIFGdI7cKpY7HBnJdMAxXPXKrWPh4bLQbwNvefAtGl1O1JPGwbUnqcIZLA\\/RGiRHEwgud1u1JnBqghIQ0gEVy0gDrQFa169Wo15redhHbaM63RzVGFpZmREgHLL9yPAT+9LvvXgeN+u3r1YW8+HGTu3+l1U3rX8mp\\/H01H6\\/ZjizHvt9085+2FVO\\/7874VZ1tz\\/8JzfKwX0Ty9SfYgytd6HUfG7NjGpt5IChFrS+t5kuPCetoeEbzw2shSTnber9m9U95fsnNMCxPOStJ6v4hDiyt1msOD7ZUmpYOsqbpq2uabuBb15r3\\/kSlB7O1Mi09mEk3X0vYgl4fLo\\/pnTbu5QlbJ1ttQHFHgNOIt+lVNC7S5gAb1JQYOqOCbPdAzXGk3loZwzBryn4\\/DF1bDfugOn2wxnw8WEeg5Ki\\/zgy11u\\/btTKG9L6xft\\/aao5Y\\/ZxI+k6SOYZ+amUM\\/bFjwqGfbpvLBNjx4SMUhyaSIdBEQokk9jQajwkuAWGtlEZYWEBUQTa5BEQJCQkIiyQxiBFZR+vTjOxYcyQIS1uqNa0Dyynu3bqcz75UR7ECNlqelZElfkQwFUl1I0nGhAyCUgbf1SElon5K1dbXjW\\/KmfomyWQP88Q1rLchTs9gTf3oSNqSNloBkdWFSsm6qk6ReVw2Ublc9mDNMyEsfUVacIjuTnQD19inubJE0PL460UE9Uk1IS1kYDu3Ip2wtBqBXQ8PhMjF41qTaDCCwbgOy\\/Qjk+bn69YKWgZ\\/glL8PDdQIt61ACGvreYY83J1GzDsn8mOMcsFo46iDvtyODXsU5IjwHVIl+wBEPndAEV+D2hy0g798nDp90MxsNeDMRsKseAERD8HcebRDkUkPZ1pkFwnIIRs6fJNa2XHmAeFWHAS4hp4eMPw6tXf1zcPL1\\/8Y\\/2f9BuDQt6lMyZ5TbHFZJtkQQlTJzQdWOnA1\\/nAMRH39Ic1RVWlKa0CqrZq7h0d96K0hCqKFpQh7aWKA0qJuq2TUdeNlqFyqr5dnnlZCSLNwlyiWVB+jPcpAEmJeQrVA4n8qOkpZOqq4b7NeyaxXmEqV\\/37oLk9D5YSeD+uQ8w18F5L0Q1cY7cO3u4kpXVW1bBOSA7sA7WY4xNSe7UjMFiMERQWYzGgpNpgrRWBkNbCMYnWIiigtXBAoqQIxlBSGIovCXcFTUnFw6qS2gBUJYVA+HcjFP49qMlZSU31UZgg\\/6OWZDuwsJKaWreSKiEhJWWRJJQUk0UVgrKjloU6sLCSugS+V1Kvfn6piqgCynXHxRSlLaKIzPXGalT37nFgaSKqX2+xU9zLPVAvl3593fDlBx+qQiqfrq2qBJ1DSDGV55XVqC5GsKE0IdWHE7OSVM7WVwk2j5YiLsdrq1HdlshE0rTUvA4buSkqZOuW5\\/Wc92QSXUJUrvdWo+bxPFianprCBEdemm7gG9sUX56m6udaFj8oE3jrwFnvNFWXmgiLMKJfTgtbSFMx1La99nHOiQVBvV9ucUYiTbUBTSkg0Q0wJtZUDEVbbx8aRG0BA2JNxTBRUx2hogaBUHJJqCtc1qMcH8w7OdSdUxgBritWJDcARH43QJHfA5qcNdUY1iy4NNVoFIhBLKypxgavnLiRhDSVRZLQVETmWB47GgViAAnrqdG\\/poSn7fkU1\\/LY0SgQg1hweWxO4LxIQMjs5bGjUSAGkODy2Kyg+baPVI4byJj8hFhweWxO4KyRcuLG85EFYdPy2KyoWbbQKY7lsZMxSwiQ4PLYnKBFRNApLgE0GRN3EAsuj3UHnhYk6wZFWJAso04dVxQdB1p5nFCTbUXRtdUGtH1jJLUYN0LFD3AcW9GKog1qUiWSAcQSaR95k2w17OlSamNTAQCJhRQOam007tg6oLYMpCGKChQULRWSKzwnrvBOnQAgf6uNztBusV9C7UZ9\\/\\/I+TEDiYfsl1PLrvfVEkzWdh7DgUqB+qL31REUkJJgskpNgWm\\/dZRCgZ0BnrCSe0GQeRlKCbsNHUvq+Pq2b3G0Gmt7proit79Nstjyj6zGPnsFAVdwOLK2Ysg0PxtO1NVb93sC33MsJPo+yYjLH2ujJWBuNoLS10fWqbFJZ0hf9lrM1YVY1I0ciYuTmdqyNnqy5PISlfxxgTj0ouIT1+b7cNO708qetvvWbXRHss+0zQLsvBsnHgOI\\/8LHrtrzlP6ObqXbRm\\/+0QFrGkBoLpKVFyw+ZlPSpdiBx+W+qRauP52urbtdKF1pTbNWNydc1Oy8FCEVoMRQSWvpv27VAVCzF9r9Nl2IxoKQ4isYF8NGy7Q0o+XKMVZb+23YtEBfrMPzbRIdxR5oTyd7ZBEDHS7a51faZuqTCih0bKiy67S4KS7qtHE5MFh4fm2hVN2oi0cY4bClHg75rZdZkrOqGWFjKWRLiRhKScgU6hcjYyxjZUWdbHVjaHqZVeESdAr+Da3JvoZupD\\/rzJM9HJputHKm10QaOoqvaLh2wuhq3kKdvsxPD97MQOVaqzdYkK8JCZQF5\\/ecGtlAWkJMlHoJiV7UXq83GHCuCQmUBGX2onCmUBWTlh+UccTnWqs3W\\/KqOpO1mUtOgcw6aBew323Yr1480lpJ263Jqx6iQeMtIVK5la7M1zYqwtLrFPsxwXAL\\/YvveBon5b3ZbNl8Td0MEU9i81p26tIxf9INWOblJmYO0urbqqBUui+ymK1RzUeDcClUz1rPIp7Aw5Npg01cgblHXMW59fd2WIykdSL3nlKhRwcPGBhbhGYGLdCYofRGeEfWuFQhbXk1SK7xU70KYFs7U0VXhLIdTwpn7B2pyGNHQgr8Yhr7gD4BI9gCKZAU0OQv0dnLv2zBbk9MIC09Od5VboJeQkEC3SBLvWpeUrwuc5Z1lA7Y2m9H0tIWlfe2gDc+5Zl7Huf1KbvUlawnJlCax7Qtdis0DoL5jzYIjLOWy0icY+m7yr01kfc5ksxW0NQeu4ShrEumLglkB87NFiByLKWdrBhxhKYGHLwrmxM0iNl5Ue3XjbEyAIyhl7V74omBW1Cwoicux3rCurBlwHUoJeuaHvD9qUXNE5ZoBrytrChyBKWvy6JuC7sjT0pDOrvQ6h7XFGN\\/wXrfLqHYg6YoCUY8EZKhHDCW6kKF0XWjAsC6MEQFdGBME6i42ICT5cEwi+QgKSD4ckIg5gjHEHIaSLkJd87CmZH\\/x5XBieZ9cVNTk8JBEKw1jGPpKQwAi2QMokhXQ5CzmaPTfaSMkWOrKmjvHcFjRWXLiZhrSdAWqheh2SgMnSZ1Ed8Fh6dKO2Qv4mG52RG7stQugsH7JiZpvJ6GKmgNHbs01YzgoYjKCZ+URL\\/Dkil2dAvagQSWTEzrLD2KL8gNHrqldDxhWMxmRiwAhsp0AwbFrotcHhxWNN\\/q0olnO1hSNjFijR65QFIZcwVAiVxhKlysGDMuVGBGQK\\/HXI7kSgZBcwTGJXCEoIFdwQCJXCMaQKxhKrj\\/1PHn31PSXyy8Ntlc+u0ZyYXGjw2NRacQXJIYTXw3t23CuIZBkEiJJjmCjs3yph\\/u+9b2Lqivjs5sQDEuXdU2CU7qUkJBwsUhOL6PWJwCRhcW\\/RnJq9N1NDIXfRF2ibtRi03yKKU1hKzqicr2HqmvN+XrAsJprwkqtLDXHZNaLqKVnGlpOA8JKLidifsYIkeNNVF2rH6tzgEEVlxE46654We1XUXWtKWcHFlRwOWGz6CIuz7uoWt1oxYTC6i0japFbROV7F1VritkDhpWbN\\/K0clvOHpudcpsu76IIH76LEpC9bJkOrbZHhyHuMJSIO4bSxZ0Bw+IuRgTEXUzQTgBNu0ZB3EUgJO5wTCLuCAqIOxyQiDuCMcQdhpIusnbNTdxdLr4cBhOLqMnuKak04QsRw9AnFgGIZA+gSFZAk7OYq8J3alwr\\/+raWvqHwLCYq8IzxCXmSkhIzFkkCTFHZCKPYHLUlX8mFBZzl6hbVczlU0xpClvMEZVrTWRdqybAAYbF3HJlZ2fkosGYzFqluOTUEHMaEBZzORHzk0WIHMsH60b9Fr0DDIq5jMBZgMXLaq\\/oqxt1zZqNBcVcTtiswIjLsdCubjTpbENhMZcRtegvonIteqsbTTp7wLCY80aeFnN0NiodWb8G0GIxJyB4WRoBGWIOQ4mYYyhdzBkwLOZiREDMxQShzUciEBJzOCYRcwQFxBwOSMQcwRhiDkNJF6GuqYo5OQzEHGpyeEoiMRfD0MUcAJHsARTJCmhyEnNr9dbofTPXGJ\\/4g2BQzHVzEOUeMVdE0tOZBslVzDGZ581cY3zjD0BBMXeNutPEXAHFlKYwxRxT+d7MNcZ3BCGYckmnsFHKOXKqMe1CicKY+JBqOd9ySoP52sTavWK+pgrSL+fKsMYUMvPNY2N8JlEFUiJuQ230NWLevPeanUKetXuneK63RSlBfz9kpp6GgEjkeYXaGJ97hGDa1ohrnYh6BXqtZKeEap7vUzkaHpCjrbokh2UittvN8Ua4xV+vhFja3ovrztLXsHkHYqUsqIRo\\/a54gmh6QIq+TlKYhoi5PG+3W+NTnABKM0QdvSh1JKaYYk5TmJ6LqXwv0Fvjc58QDHuuRSLW5Z6Lzkaea\\/34SHyBTjo0Ibtr23MR0GE\\/o6Q8B1DiuRhqvsJsEh7BsOeKEQ2JiDapX5ueKwJNKaDNEYCYxHMRVNzwMekaQEDiuQjmsGlQ0l0AKO4i3DW1xZzxsL6YEzY5DCpgMecWhrqYE4Fw9hAKZwU1OXuuqV0fHj7P1RqfVIVg2HOtGyk7PVcJCXkuiyThuYjM47la9EVVDIU91yXqq3Yqp5jSFLbnIiqf52pVr+4AUy4pbU7p6jc3kMx05pUkfpX2QfkWIzld+ZxGsoBveU4kLv+er7uK6HI+MZIZ3U1sE5OZRrLV3nxYQNhIXiI2jWQmDxvJC8915VMpARnJnNTzuCZEHiPZqrVsDjBoJLUroBvJAioyko4HbjkHG8mM68CGKd5uHiOpvU5xYEEjeU2NchuUE5GRvF7uB6QgI5lzCdiaEZfLSKqFhiYUNpKXqHUjmU8xpylsI0lUPiPZaW85PGDQSK66ty1eibWejY0k4yMjGUGgkWQgbCQNKDaSAqUaSQuGjOQWkW4ktwQBI7kBASNpxMRGkqF0I2kExEaSYbCRNKCki1DX1Cbv4mF98g42OQwqYPJuC0OdvEMgkj2AIlkBTc5Gcgx7U\\/uMZKcuj3aAYSM5Gu+SbiQhI2mRJIwkkXmMZKcWTJpQ2Eheok5o+2KKKU1hG0mi8hnJziiThGB48u4UudNzFfDx5B3gaxIr5Ir5xHNlXBlxGExmeq7OKAFVgbDnukQsX95ULVEmD3uuC881\\/aUE5LlyUs9DgBB5PFdn1LFCMOi5tCug+6ECKvJcF6rEDGoxB3uujOvA3iLebg7P1eGSXIgFPdclbMNzFRCR53LcCOUU5LlyLgG7GOLyeK7OKC0GUNhzXaLWPVc+xZymsD0XUTk9l1G5DMGw51q\\/jlbuuZaz2wZ6LsJHCyYjCPZcBIQXTBpQ4rkYSl0wacGw54oR6QsmtwQhzxWBwIJJIybxXASlL5g0AhLPRTB4waQBJV2EuqbqueQw8FyoyWFQQZ4rhqF7LgAi2QMokhXQ5Oy5hua4pwlaM90ZpcwQDHuuofPuwlJEQp7LIkl4LiLzVL\\/0RikzgMKe6xL1qHqufIopTWF7LqLyVb\\/0RikzBMOe6xS503MV8LHnAnxt4roU84nnyrgy4jCYzKzu6Y1SbRUIey6QoVX7a74ok6zr6SxApk+sZXKxB8u5FDwkCJGnbKk3atAhGPRguVeknG8OJgnw9QnnXUzHnizjurD3iLejoyqrxyX2EAt6MpSl60UpZ+uCa0J94OrFy9n6FJvHoxGXp+KsN7YPAFDYo12i1j1aPsWcprA9GlH5itp6Y4cCCIY92tjc4tHobOTR1g9q4qK2CII9GgEZHg1DiUdjKN2jGTDs0WJEwKPFBCGPFoGQR8MxiUcjKODRcEDi0QjG8GgYSroIdU3Vo8lh4NFQk8MggzxaDEP3aABEsgdQJCugydmjLQ\\/V1rvAsjd2KIBg2KP1rdujlZCQR7NIEh6NyDzzYr2xQwGAwh7tEvV16CinmNIUtkcjKt+8WG\\/sUADBNM\\/UhrWIl8jfV41SCcmQJrGNEpOZk1ODsQ2CCqR5lyY8ixxpKSXo0wS2YREiz6TRYOyzAME0A1Gvz2BPZm7g6JIcpmuIfdMxkzPgvRwglq7ja1dqbqDokhS2eCcuzwTLYGwXAaA0ZR2WmrkSU0wxBbGTkRjR20Tlm2AZjB0pIJgm3rvwSR1Hcm4hGdMkWQ5hqKW+RtnDbG1h7GEWQeAeZgxkOAQMJQ6BoXSHYMCwQ4gRAYcQEwT2MNuAkEPAMYlDICjgEHBA4hAIxnAIGEq6CHXNSivBksOgBAs1OQwoqAQrhqGXYAEQyR5AkayAJmeH0FUrqM8hDNa2FwgMO4TOeElwIwk5BIsk4RCIzOMQBmvbCx0KO4RL1LPqEPIppjSF7RCIyucQBmvbCwSGHcIlct0hlJAMaRLbITCZ7RCsvSc0IOwQHGkpJejTBLZDECKXQ7D2hEBg0CF4MnMDR5fkMB1C7JsOhzAaGzUgLOgQHKm5gaJLUtgOgbg8DmG09k\\/QobBD8CSmmIIcQkZiRG8Tlc8hjNb+CQgMOwRHcm4hGdMkWQ5hGRANh7C0sByCgGCHQECGQ8BQ4hAYSncIBgw7hBgRcAgxQcghRCDkEHBM4hAICjgEHJA4BIIxHAKGki5CXVOdQ5DDYA4BNTkMKGgOIYahzyEAEMkeQJGsgCZnh9DM\\/o3xRmuTBgSGHUJbe3c5LiIhh2CRJBwCkXkcwmht0qBDafK9Cvr0FPVhiv66bKKYrU+yecwCUfnMwmjt14DAsFm4RK6bhRKSITc9IsGZzDQLo7W\\/gAaEzYIjLaUEfZrANgtC5DELo1X3j8CgWfBk5gaOLslhmoXYNz1mwSjGR1jQLDhScwNFl6SwzQJxucyCVSOvQ2Gz4ElMMQWZhYzEiPQmKp9ZmKwaeQSGzYIjObeQjGmSLLPQzlJlrS04Wj+ybiw4EhC84IiADLOAocQsMJRuFgwYNgsxImAWYoLQgqMIhMwCjknMAkEBs4ADErNAMIZZwFDSRahrqmZBDgOzgJocBhRkFmIYulkAIJI9gCJZAU3OZmG5+vO+wqJBn7ScrEp8iIbtwnKpZp9bKCIhu2CQJNxCIItlFjg7Vik+wMJ+4Ri2sfS9nK1PsXnsQqDa1TzgNFll+RAN14joiQIlIiV8XCOi8zV6iUgBn9SI+C+NGAAimx2XxarLV5Fwkcg5ZLMuP5eHC\\/PPPPpeaNkEVBSSkXseEZgoljng\\/FuF+RANVoWAxwcoCinho6qQc6b06vwijjnFcV2FeQMHl5v4rzg7J7mzJ9cFN7YAgGCw3uQct7EFQAkR7QFwIXpICqovybgGbNICV6zXwJfA2gMAYOECE7t73kAxJylsTxmodhUhODfWJgAQDVeYLPq0fAtvPhsZvqWFtfOagGDDR0DGzmsYSgwfQ+k7rxkwbPhiRGDntZggZPgiENp5Dcckho+gwM5rOCAxfARj7LyGoaSLhL4pfq+5fN9cjm8ua9dGLitssx9blDZ8MSSUaMX2TTjNCEZyiHAkOajN2fhV48FFwfdL1mYAAAubvmr2mr4CCrJ8BkXC8lXjzjihvMzWPgAqErZ7x5CPek21e7lcfYrLY\\/aq8WBfYIKs3QAAFjZ6eoqA0ctnY5uns7WJ6p9SNjF5\\/ksijoaozOmo2doGQMHBBu8crmnw8ljY3p1ZdHuXCU\\/mLiPn\\/OhnGs9812zV+wMsaOzQg0I3dvlsZOvOOdI3ditgmFMMiYtcysCWzn+d2bnIXeyYupuN\\/QMAFLRzl7xjO5dPQ2buTHPd77OYgKxcRu7ZGwUmz+TgbG0UoCJhG3cOWbdx2QRzksA2cYHINy84W3sEACxs4IKFKPVv4WRk35YGPZ6uYwhs3gKMMVkHgcS6EZA+VYdB2LhJNGCiThKDbJvAoGk6GI+YtgAEJulgMGLZAogxRQeBpE+Enlhp9T58FJT7gBb7EQMV+0gIeq2PDiFJ0zEkG3qLk0FbP45dOw3abOwEgLCgQVsfU7XLoJVQ9HQiprgaNKJyGTRjEwAdCRq0U8gH3ZXYjbmQq09xOQwaETkNmrEVAMKCBg2kSDdoBWxk0ABbm\\/jmcCkbG7SMS8KehKksg9ZUxvYDGg40aKi36ru0ZVLRJm2IKuEWipjIsOVcAxoAhMZh2JrK2O8AYSHDdona2iK7gCg4qQuROj2Wz0BOKuMCkG2It5vtpJoK76mAoJCTAt1T3YetgCtswwa4quvSgGKuPsHlcFbE5HBWTWXs4qAjQWfl6KDFBHOSwHRWRORyVstAgJ0VwsLOalFTfbGzWk7GziqgQ2fFENhZBRjDWUEgcVYEpDsrDMLOSqIBzkoSg5yVwCBnBeMRZxWAgLOCwYizCiCGs4JA3CeoJ2pLH+WovvIRtdiPIGDdYwxBXfYIIDhpAIOzAVqcndUiUZ31UU1l7KCAsLCzmvArlNsoyFkZFAlnFagczqqpjM0TdCTsrI4hHwRkQsgXcvUpLo+zCkQuZ9VUxhYKCAs7Kz1FwFnls7Gz0tn6xJeFS9nEWfkviZgHorKdlbFtg4aDnRXorcBZ5VGxswJU6heIMpnYWWVcAx4AmMblrIx9IhAWdFbnqE1nlU9EzupMpDurbAZ2Vv4LwLZBbjeHs6rxXhQICjqrc8w0R1VpCwILeMjpZOSG7UNg8jid2tiNQkfCTsfuMMUEc5LAdjqByOd0amMjCoQFnU47D0tnL3Q668k9cjqEjlYACgR0OgSD1\\/9hIHY6DKSu\\/jNAyOnEaPS1fzExwOlEGLDyD8fDToeA9HV\\/OBh2OgSCV\\/1hIOkToSeqToePAqcDWuyf6MjpSAi609EhJGk6hmRDb3F2OmO\\/5M31xZ+mNnaCQFjY6Yz4lcZtFOR0DIqE0wlUjo\\/9NLWxCYSOhJ3OMWTjVXkpV5\\/i8jidQOT65E9TG\\/s\\/ICy4\\/cMlbHX3hxKKITMzoqmJyvrkTlMbWz9oOHDnB0dGCuH7JLwt\\/ZnG8dWbpjZ2fUBYaNMHT1LKGboUg6nIpT\\/aX51parzhA4JC+z04slJO0KUIbCkemBzffVn6sCHFVSS41YMnJ6UEYaOHjJyIfA5Erk++NI2xzQPCgrs8OPJyA8WYpMhS+1O\\/SESk9pcGM5zXEAis9gMMntfAQKL2CUid1zBAWO1LNPq8RkwMUvsCA+Y1cDyi9gOQPq+BgxG1H0DwvAYGkj4ReqKq9vkoUPugxX60QGpfQtDVvg4hSdMxJBt6i7PaX+9m57xGY+zkgLCw2h8Gr9ovoCC1b1Ak1H6g8sxrNMYeDjoSVvvHkA9q\\/7rOupSrT3F51H4g8s1rNMbuDQgLq\\/1z2LraL6AYMjMjMpqozOmFxtg8QcPBat\\/OSCF8n4S31T7TeF70N8Z2BggLqn1HUsoZuhSDqfalPzrevze46B9BQbVvZ6WcoEsR2Go\\/MHlevDdGFb6OhNW+IyelBKT2\\/TkR+RyIfC\\/eG6P+HmFhtW\\/n5QaKMUmRpfbHjmsBNLW\\/KKIaq32GwGo\\/wBhqHwKJ2icgXe1jEFb7Eg1Q+5IYpPYFBql9GI+o\\/QAE1D4MRtR+ADHUPgSSPhF6YqXUh8hRvT4EtdiPFqA+JIag1ocACEmajiHZ0Fuc1X7fLi7JqfaNAn6EhdV+38NJvNsoSO0bFAm1H6g8ar81Cvh1JKz2jyEf1H5iW7BCrj7F5VH7gcin9lujgB9hYbV\\/DltX+wUUQ2ZmREYTlan2W6OOXsPBat\\/OSCF8n4S31T7TeNR+a1S4Iyyo9h1JKWfoUgym2pf+6FD7La4IR1BQ7dtZKSfoUgS22g9MHrXfGqXaOhJW+46clBKQ2vfnRORzIPKp\\/dYo1UZYWO3bebmBYkxSZKn9oTXU\\/tBZap8hsNoPMIbah0Ci9glIV\\/sYhNW+RAPUviQGqX2BQWofxiNqPwABtQ+DEbUfQAy1D4GkT4SeqL7b56Pg3T5osR8t0Lt9CUF\\/t69DSNJ0DMmG3uKs9heR4Nyuq2mtanCAhdV+1zm36yqhILVvUCTUfqByqX2rGlxFwmr\\/GPJB7avfhs\\/m6lNcHrUfiJxq36oGB1i4ZkFPEahZyGfjmgWdrU98XaeUTWoW\\/JdE9DtRmTajs6rBFRxcs3AOV7bruu4nVEbDVQQZWeFHMtN47EZn1WcDLFhFoCSn0xf5ZzPxIn9\\/fljKS\\/902I7OKJ8GUHCR\\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\\/3i\\/oOWMUSEMwegQMabNQT413VqKIpKczDZKrWWCydmrvRys7vVEhjbCUuLtx7Z\\/13Jyej589fr+ulujXP78Nf756+7hpHv0W\\/v76u8d1\\/egfuzZvfn7ctPz3n84Hf3jz85vl7I7\\/kzB+eLGe8Rs3W87gf361NJofvfivN+sJ\\/03U4Z9er\\/y\\/7mBfbJR07tvfX23\\/9OLnl+G0b1fsN2\\/fvvp215KavH31yxpXe\\/jHN+s\\/fKeE9cOb38MJv4IT\\/g\\/969u\\/r\\/+R6GTF16ptw7W69LFfXr39bg3uzdufKF0v33tcN\\/E3fsvxvNxy8+a7rQGF+0PILjX94W49Sv\\/+4tetJaG\\/ePlqu5QvX7z+8R9bixe\\/bX+XvJ0vUiD6aXdd98G8ePv29XrN60cvflzbHS\\/ly1e\\/vAj9iJB+f\\/sqREq5f\\/vmJ0r49gPpwGvuv2sg9w93ObrFD4ypy5ElLargLZG0qIIwgNKCmmyD5rVVF1sZ0gJDibRgKF1aGDAsLWJEQFoIkgy\\/50ZBWkQgJC1wTCItqBWQFjggkRbUypAW8eIjacE9c73+sV\\/uQLbDAeTQ5DwSjwToWlDcG6\\/tIJjytohH4jHILtdIXEJCI7FFkhiJiWyeXekxXtxBMCXypg\\/vKC+Rf\\/WWHmC\\/8CNye5y93Ead\\/wp\\/\\/cf6r39QH3IFQU1VOijzFRaTdfN+ab+STHWHChMqHfW6Eq2K5+W8ZeJTmpb2yDHiVl++OcDSkbf98pRJpPvD9RpTB6A\\/3wZZE\\/76369erqOj3Q3Kw+qWu6rPuKuSgx3zXmo95EkWD5ulEXU\\/H1\\/nw6tklEZAMDgBUQ+1dwKiiISeZBZJ4klGZOvLeTM5RmkEgFKi5qcYnZczJcCnLKO4Y6lsbxQrQDCKvDlHvozWQ17k8nxiMs+jzqgkAFDpqOVRlxM1PwYilecmMpb6QzAl8pG2FsiInF4fZwU+4HX4BXG3TXuf10345S6fMlR2NxmMRfIASgl6eXKPTVbU\\/NaVT\\/GtYB+MFewQLB15t9jeuvVHnh566Gx16ImH7aFndRnbg7xGyTAW2QIoSsWoDDt97RbQ+RSdiyIx6DDVaudaIzHG0loAJfJkTA864fRd1E8eL2ro2frHx4+nRURpwjibcN3zJUX4+SrCwh9PFiU2PfpyoX2+\\/G2kUN57\\/P64vqIYl1impd2DxTP1NHYlE\\/Bv6x8fPn6\\/qUI8nyz\\/T9F9\\/WAB8JuBawD6C7BsiqZOU\\/z5ASn6NMXTx3wBvw65q9dErun8d37lxEfv6fI+WDhDv76UMZOaGNuJSiQ+vBmNqXIApViYpSu0sjRkF\\/RHwZo8\\/XpN2df09y\\/CjfL0PbIv74fb9P3QV9uW23xOLZ+FF4B\\/fvrsyfby8dn1bV5pzF3f3s99IuisQYZYwyBTX4aYeJCGmFodYNoxdJaWhkh46VTxbkLhAaY1xtqbKDoXRWKAISqyIjAtqjcwgCTi9OBCZ2U5GjpFTAiMWd3PyoTCbiYjarEgTDVv\\/ViJWfUEBhB2MjkR8+0eiUxNNqrbPplQ2MVkRM3OIyNo1Q1YSNDB5ITMtoNOGSqra4zqVkkGEHYvGRGL4aBT1pvBzLIm120o7Fy8UauDSiczRKeJHVeLwWgRppC4Rb\\/O46fmjw404duzqVYbFZhlGqYNSpkeWqKuYqvkLNO8a4BmmbppF1F6lincQRhpbbT\\/ados09qxDaSBuhK1UmaZRhOma6bQs6jV0iNTyd4JDzBhdVYfzeBVH6Pq4kworD6aya0+8ik6F0VCfRCVrT5G9XWzAaRE\\/P8rN2McAEAYBD5L\\/\\/8yjUUTB6CsGpAJL00K+ihVRB8l6dDHoCBprTR9BKlvN+ApSx+Dbg0aI00fSWIgw3vIf+R0+mutNH0EqdF4QWi6XeecJH0kkdGtJfH0MenM1xhp+ggS3xovSYc+JmM8b6Xpo5v6p49ztK8WjitkZA0KZW5kc3RyZWFtDQplbmRvYmoNCjUgMCBvYmoNCjw8L1R5cGUvRm9udC9TdWJ0eXBlL1RydWVUeXBlL05hbWUvRjEvQmFzZUZvbnQvQkNERUVFK0NhbGlicmkvRW5jb2RpbmcvV2luQW5zaUVuY29kaW5nL0ZvbnREZXNjcmlwdG9yIDYgMCBSL0ZpcnN0Q2hhciAzMi9MYXN0Q2hhciAzMi9XaWR0aHMgODU4IDAgUj4+DQplbmRvYmoNCjYgMCBvYmoNCjw8L1R5cGUvRm9udERlc2NyaXB0b3IvRm9udE5hbWUvQkNERUVFK0NhbGlicmkvRmxhZ3MgMzIvSXRhbGljQW5nbGUgMC9Bc2NlbnQgNzUwL0Rlc2NlbnQgLTI1MC9DYXBIZWlnaHQgNzUwL0F2Z1dpZHRoIDUyMS9NYXhXaWR0aCAxNzQzL0ZvbnRXZWlnaHQgNDAwL1hIZWlnaHQgMjUwL1N0ZW1WIDUyL0ZvbnRCQm94WyAtNTAzIC0yNTAgMTI0MCA3NTBdIC9Gb250RmlsZTIgODU5IDAgUj4+DQplbmRvYmoNCjcgMCBvYmoNCjw8L1R5cGUvRXh0R1N0YXRlL0JNL05vcm1hbC9jYSAxPj4NCmVuZG9iag0KOCAwIG9iag0KPDwvVHlwZS9FeHRHU3RhdGUvQk0vTm9ybWFsL0NBIDE+Pg0KZW5kb2JqDQo5IDAgb2JqDQo8PC9UeXBlL0ZvbnQvU3VidHlwZS9UcnVlVHlwZS9OYW1lL0YyL0Jhc2VGb250L0JDREZFRStUaW1lc05ld1JvbWFuUFNNVC9FbmNvZGluZy9XaW5BbnNpRW5jb2RpbmcvRm9udERlc2NyaXB0b3IgMTAgMCBSL0ZpcnN0Q2hhciAzMi9MYXN0Q2hhciAzMi9XaWR0aHMgODYwIDAgUj4+DQplbmRvYmoNCjEwIDAgb2JqDQo8PC9UeXBlL0ZvbnREZXNjcmlwdG9yL0ZvbnROYW1lL0JDREZFRStUaW1lc05ld1JvbWFuUFNNVC9GbGFncyAzMi9JdGFsaWNBbmdsZSAwL0FzY2VudCA4OTEvRGVzY2VudCAtMjE2L0NhcEhlaWdodCA2OTMvQXZnV2lkdGggNDAxL01heFdpZHRoIDI2MTQvRm9udFdlaWdodCA0MDAvWEhlaWdodCAyNTAvTGVhZGluZyA0Mi9TdGVtViA0MC9Gb250QkJveFsgLTU2OCAtMjE2IDIwNDYgNjkzXSAvRm9udEZpbGUyIDg2MSAwIFI+Pg0KZW5kb2JqDQoxMSAwIG9iag0KPDwvVHlwZS9Gb250L1N1YnR5cGUvVHJ1ZVR5cGUvTmFtZS9GMy9CYXNlRm9udC9CQ0RHRUUrQXJpYWwtQm9sZE1UL0VuY29kaW5nL1dpbkFuc2lFbmNvZGluZy9Gb250RGVzY3JpcHRvciAxMiAwIFIvRmlyc3RDaGFyIDMyL0xhc3RDaGFyIDIwOS9XaWR0aHMgODYyIDAgUj4+DQplbmRvYmoNCjEyIDAgb2JqDQo8PC9UeXBlL0ZvbnREZXNjcmlwdG9yL0ZvbnROYW1lL0JDREdFRStBcmlhbC1Cb2xkTVQvRmxhZ3MgMzIvSXRhbGljQW5nbGUgMC9Bc2NlbnQgOTA1L0Rlc2NlbnQgLTIxMC9DYXBIZWlnaHQgNzI4L0F2Z1dpZHRoIDQ3OS9NYXhXaWR0aCAyNjI4L0ZvbnRXZWlnaHQgNzAwL1hIZWlnaHQgMjUwL0xlYWRpbmcgMzMvU3RlbVYgNDcvRm9udEJCb3hbIC02MjggLTIxMCAyMDAwIDcyOF0gL0ZvbnRGaWxlMiA4NjMgMCBSPj4NCmVuZG9iag0KMTMgMCBvYmoNCjw8L1R5cGUvRm9udC9TdWJ0eXBlL1RydWVUeXBlL05hbWUvRjQvQmFzZUZvbnQvQkNESEVFK0FyaWFsLUJvbGRJdGFsaWNNVC9FbmNvZGluZy9XaW5BbnNpRW5jb2RpbmcvRm9udERlc2NyaXB0b3IgMTQgMCBSL0ZpcnN0Q2hhciAzMi9MYXN0Q2hhciAxMTYvV2lkdGhzIDg2NCAwIFI+Pg0KZW5kb2JqDQoxNCAwIG9iag0KPDwvVHlwZS9Gb250RGVzY3JpcHRvci9Gb250TmFtZS9CQ0RIRUUrQXJpYWwtQm9sZEl0YWxpY01UL0ZsYWdzIDMyL0l0YWxpY0FuZ2xlIC0xMi9Bc2NlbnQgOTA1L0Rlc2NlbnQgLTIxMC9DYXBIZWlnaHQgNzI4L0F2Z1dpZHRoIDQ3OS9NYXhXaWR0aCAxOTUwL0ZvbnRXZWlnaHQgNzAwL1hIZWlnaHQgMjUwL0xlYWRpbmcgMzMvU3RlbVYgNDcvRm9udEJCb3hbIC01NjAgLTIxMCAxMzkwIDcyOF0gL0ZvbnRGaWxlMiA4NjUgMCBSPj4NCmVuZG9iag0KMTUgMCBvYmoNCjw8L1R5cGUvRm9udC9TdWJ0eXBlL1RydWVUeXBlL05hbWUvRjUvQmFzZUZvbnQvQkNESUVFK0FyaWFsTVQvRW5jb2RpbmcvV2luQW5zaUVuY29kaW5nL0ZvbnREZXNjcmlwdG9yIDE2IDAgUi9GaXJzdENoYXIgMzIvTGFzdENoYXIgMTIxL1dpZHRocyA4NjYgMCBSPj4NCmVuZG9iag0KMTYgMCBvYmoNCjw8L1R5cGUvRm9udERlc2NyaXB0b3IvRm9udE5hbWUvQkNESUVFK0FyaWFsTVQvRmxhZ3MgMzIvSXRhbGljQW5nbGUgMC9Bc2NlbnQgOTA1L0Rlc2NlbnQgLTIxMC9DYXBIZWlnaHQgNzI4L0F2Z1dpZHRoIDQ0MS9NYXhXaWR0aCAyNjY1L0ZvbnRXZWlnaHQgNDAwL1hIZWlnaHQgMjUwL0xlYWRpbmcgMzMvU3RlbVYgNDQvRm9udEJCb3hbIC02NjUgLTIxMCAyMDAwIDcyOF0gL0ZvbnRGaWxlMiA4NjcgMCBSPj4NCmVuZG9iag0KMTcgMCBvYmoNCjw8L1R5cGUvRm9udC9TdWJ0eXBlL1RydWVUeXBlL05hbWUvRjYvQmFzZUZvbnQvQkNESkVFK0FyaWFsLUl0YWxpY01UL0VuY29kaW5nL1dpbkFuc2lFbmNvZGluZy9Gb250RGVzY3JpcHRvciAxOCAwIFIvRmlyc3RDaGFyIDMyL0xhc3RDaGFyIDEyMS9XaWR0aHMgODY4IDAgUj4+DQplbmRvYmoNCjE4IDAgb2JqDQo8PC9UeXBlL0ZvbnREZXNjcmlwdG9yL0ZvbnROYW1lL0JDREpFRStBcmlhbC1JdGFsaWNNVC9GbGFncyAzMi9JdGFsaWNBbmdsZSAtMTIvQXNjZW50IDkwNS9EZXNjZW50IC0yMDgvQ2FwSGVpZ2h0IDcyOC9BdmdXaWR0aCA0NDEvTWF4V2lkdGggMTg3Ni9Gb250V2VpZ2h0IDQwMC9YSGVpZ2h0IDI1MC9MZWFkaW5nIDMzL1N0ZW1WIDQ0L0ZvbnRCQm94WyAtNTE3IC0yMDggMTM1OSA3MjhdIC9Gb250RmlsZTIgODY5IDAgUj4+DQplbmRvYmoNCjE5IDAgb2JqDQo8PC9UeXBlL0ZvbnQvU3VidHlwZS9UcnVlVHlwZS9OYW1lL0Y3L0Jhc2VGb250L0JDREtFRStDYWxpYnJpLUJvbGQvRW5jb2RpbmcvV2luQW5zaUVuY29kaW5nL0ZvbnREZXNjcmlwdG9yIDIwIDAgUi9GaXJzdENoYXIgMzIvTGFzdENoYXIgMzIvV2lkdGhzIDg3MCAwIFI+Pg0KZW5kb2JqDQoyMCAwIG9iag0KPDwvVHlwZS9Gb250RGVzY3JpcHRvci9Gb250TmFtZS9CQ0RLRUUrQ2FsaWJyaS1Cb2xkL0ZsYWdzIDMyL0l0YWxpY0FuZ2xlIDAvQXNjZW50IDc1MC9EZXNjZW50IC0yNTAvQ2FwSGVpZ2h0IDc1MC9BdmdXaWR0aCA1MzYvTWF4V2lkdGggMTc4MS9Gb250V2VpZ2h0IDcwMC9YSGVpZ2h0IDI1MC9TdGVtViA1My9Gb250QkJveFsgLTUxOSAtMjUwIDEyNjMgNzUwXSAvRm9udEZpbGUyIDg3MSAwIFI+Pg0KZW5kb2JqDQoyMSAwIG9iag0KPDwvQXV0aG9yKGdlbiBzZXJldm9sKSAvQ3JlYXRvcij+\\/wBNAGkAYwByAG8AcwBvAGYAdACuACAAVwBvAHIAZAAgADIAMAAyADQpIC9DcmVhdGlvbkRhdGUoRDoyMDI2MDUxOTE2MDQzNiswOCcwMCcpIC9Nb2REYXRlKEQ6MjAyNjA1MTkxNjA0MzYrMDgnMDAnKSAvUHJvZHVjZXIo\\/v8ATQBpAGMAcgBvAHMAbwBmAHQArgAgAFcAbwByAGQAIAAyADAAMgA0KSA+Pg0KZW5kb2JqDQozMyAwIG9iag0KPDwvVHlwZS9PYmpTdG0vTiA1MDAvRmlyc3QgNDczNy9GaWx0ZXIvRmxhdGVEZWNvZGUvTGVuZ3RoIDU2NjU+Pg0Kc3RyZWFtDQp4nJ1dzYpmO3LcG\\/wO5w1aSv3DMCsbDN4MM3dnvDD2xZsxGDNe+O0doTrZXYv8gQ8ut053lRRSZmSkTpby61af8rTyjPq08dSyn9afOs7T1iN1Pm0+MtfT8BfSn4Zvr\\/H0+vQmTy9P3w2Pz+CjPOPgG+OZfT+9P\\/Ocp69njfn0+eyCCc+zZ39kP2ddyFqkPqPg68Lo9tSKHxmCrxsznacKEDG+toLvY31t9Gfsp\\/aKcQtfJ8GxYCnPxHxjAxfzTawb36rztGdivoVlYVN1Y56J+Tb+NzHfqfuZmO9gV9imFFkPV1gwz6qwALaCpYqU8qyGrwDFUqWVg43hK+ZZ+PmOeRbGd\\/zlgskG5lkHXznPhgmxHmxJuJ6N+bgemE64no35NjaxMd\\/BvjBEDhY\\/YfOCfW0YvQrGww0Vkx04TOCsTZ\\/gLze81GBvTN06HHgEX2GEM+DSup4Dvw1MBue0iXkO5puY5GC+Ba9V7K0tOAv\\/tf3lFTgcO6wFQw7sd+BCLqWWjofDMfibCs\\/XQjrg+6ROF\\/oIju4Nf6qF3IBVKzbVO0hTK2jSsZ5a8a3x5XCQA5PVinkmXVIBsWDOiqX3tTh8kmdAryDUxrZBD7CDS7\\/0wSorFjcKmSAgBKevAsZU+lrgejgOD+COLC4MJGsgeAUTR+eO4a4xsLnKUBhkDok4K6mHeeYi90g5jKAvxoLhwUaQkbRDzID7HI55DohSyb8iHH7wsDkcTOP\\/wF08HJIXnBWyH3acZFTFlsAU7OtGETZXGUcdDKuIpIlFk\\/J4gM8r4mNO\\/CA5Oie8WvFtrHQxHMDqxXgA1hEGCOMRjKsw+ipYXUVYrQKOVPhscQ4ED7hdOJwkZxgNsh5Wr\\/jD6rQzFgfuYoXY9oLF8dAZ4p0xB+rDaTT6moiCCneuJRzO6ACPK9iwNpmAoFqnMFIRSIdMQHjuUhmrCBEypSLQNmetsP4WuWGMB\\/oCtNiNSgWY3egLRNvusA0jZPfDWEcoDYoFAg5eAij2vyeQK0JuL+HM+JmFP0EQEGeb32Jg0c5Y7j6kOmY\\/hXZmzDAC6mYw0c749qFKQkfwgK3UwzgkSRCNp2EJjI3T4eoKW59+ODN+ZtCGjLlJApyriNADmPgs2Jh6czZiQvC3Z1NiEJRYDjUK8xysV7iUUkCw67RSMIswlArdLjd+BXaUG4uQIMgZg7vBkvx7PFHCGCulw5ZC45dOY5KdiKZ5UwBiHe6XekO7chbOtyiD9HbZiC254rApXaRLOUwf9Fg52L\\/IjX0QVsjFSq0Qmr+SRsLAqZxBGDCIMz5RABq\\/K1QAaA1Fl09XhqkBAz8tjcliUDobMSYCTMgL8AoroB\\/BnkKhpmrAJlROPNHGDEa4EBjUAmyaQl6oJRgnjemn0M5XOxiS0q+ugKlyxQeyjCd+tzFZdMpPF85CbWFulH4FiFn0prNxOMvVEPxfvqSHmZEskEU7MxZkVyYPYmzambIqBzuUK3GH+yX\\/WgGmfCVIep+R1chMrhHKxVVdfbo+Z0RD9pjRqVAdlhCKTesgtMyv9MpkRSGDet3Mjyd6gUKKTXIEZ160FRNn2\\/AUcfAE5gijqx2sQ6iCvRQmOwoaxVne1A27UB46fXvDuQuTI6UZROQIqiOmYYKkBg6OoD4O2mpfgcSKOAp6SOZsSuRkyqXIgSRMrsRY56ZXqiT5TBHqByHEzIkn+vJcpQOrhbll8CwilNxByRdG7KCXhWIw5HAEZbNhN3KutpIHjJnRaXFmQ8Q3R3C+cRM69ZXsbFTRQXY2yt9YPHkVYuzKkxgxeEhq1NZxECWt3BPZ4giqK9W7Mc5nwcpxZKAYg12MG+jz5AhqLg4LPDjwCdZujP2JxIwnijV93hj7c4DVjXE+6fPGOEci5ogr2Phzu6q+eCBhnE\\/6\\/CstbPiC3EK4weeNcY5j37iHRWg8fN6+ZJYHS+rQYgpojOlF7zVGPJzFEYdPPKww9lcHExtjetHnjbGPNIMRjOlFnzfGPhINRrSbEvCdr5zA5Hzz+6LPG2P\\/JpvGmF70eWs3L1QemZgY6HMyD0kDK2qM+M1UwHMVsgVPRoz93QZH8ABJnzfGPiAwgjG96fPG2N8DTGyM6X19ztjfkwezcRPKPW0Tdxce0ohxfc7Y3zyTNsb0vj4fN\\/M0jmBaoc\\/bzT08GzTq0GGSoFYi6dDnjPNDRWqM83N9zjg\\/CAE8cb7rc8Y5jMERnO\\/6nHF+Fpl4s9n1+T0gbzKRcX6uzxnn55CJjPNzfb5u3iITmVfK9fnNXDw1NEZaYSZoPBCX63MemUsjE3koLtfnjOEyyEQejMv1OdWhTDKRsVSuz6muheexdnPh9Tl1sfB8ATFiBqTPqUo453QeaZn36HPqHdIa32uYxa7PDzMRzwpXISv1v52bH+lzahFEjCP4YnR9zsx23zwaNaFen1OFEXA43hbOR593aiAIzTP0zZ48MlNdQRMcaKlF9z2kl5s9F0cwe9LnvTD\\/8PzYmbuEPu83e\\/LM0A5zITPBVaCbhfhmhCwrPJZ3PsFivd5XFzCx15vPDkcwa8\\/BEZyPPu+VuAu86Dcb0+edmQhiiRHMIUKf82SKp8OXvpuX4fMuzJn3zH\\/zMn3OVSBD8\\/zOnN6o9V2Yg+mBLjdXd74lMAfT5+QqnvjKeHMwfc5XTb5oYQQzVqPP73ml8eTWedJo+76SEncfjiAGfd55HoAD+ELKDE2f8zCGTCl8KWn31Y1vJczkPDl05qRO1edpEjmdrzM39\\/fJEZyPPu+dPzfAxM7zRb8+5wmn4+T98IiMUwB9zrNY3+BAZwbs1+fMZ\\/3cl6B7MqDPB3M6Xxk688q4Pr+ZnK8Q\\/Z4M6PM+7skA8\\/eb+6nrfAvGyYA+57kGWYgjON\\/1OV9oxyAT+Yo7rs+ZPcciEyfnuz6fxN1kIrPYuD7ny+\\/gyeOeu8b1+bpnCTKRcT6vz+95gCePzpie1+f3pMETQb8nCCp8Z5zP63PG+exkIuN8Xp\\/fV+RJJjLO5\\/U543zysN73rUXQ54zzuclExvm8PmecIwthBON8XZ8zzlchExnn6\\/qccb74SsEXUZxN6HPG0eLZoFObFjWcK8MpgD5nnEOyOYLzXZ8zzpGF8P7IOF\\/0+bhnndX5Ssr56HNyGk+sWjDOF33O90w8TY5YtwSA90nG+S6bI3iGoc8H43zzRWIwpjd9PhhHm9m\\/U5s21XwwzjftORjnm2+Jg3G+6XOuGychMHHcEw59zqoAngZrLZyPPh+M883T5mCcb\\/p8MM43T5uDcX7o88E4Pzxt3vP3fQ\\/6Ouvw7WIwpg99zgoIoojv3Pd0RL0e9zxFyw7GOV6C6vOHP\\/z4EwtQ5fnzj7\\/8+NOP3\\/7vv3\\/\\/8Ze\\/\\/c\\/\\/\\/vvf\\/vGvv\\/\\/Xj3\\/+l6f+6\\/PjT\\/\\/5NP7MH\\/\\/49393h4BMX0N++ydrTGNd68\\/WQPg7wJJPsIaLNSOs9gnWcrF2hNU\\/wToeVg\\/9NT7A6q6\\/oMwB1vwEq7lYITfWJ1guN3rIjf0JlssNUW789mfT0dedb2S8pH359Lr69cJroHftJtL6ucTf\\/+0\\/zAh7WfVzyAjJdEzbxYYYLplGSKZaYrB\\/MMF8NsVW\\/9r3uyRzsSNcrCmLo8eWcR03Vghm6uIvMNsy+1PLjC\\/LuHyecb4whXXEnJkuZ2bMGVNZR8yZ+Slnvjb+LslcbMwZU5pnzJnpcmbGnDG1ecacmZ9y5mvj75Ks8SvmjCnuM+bMcjmzYs6Y6j5jziyXMyvMWtVU0AzMTVsrTFtiKmgG5vssPNOIqYAZmHuo2SFBxFTABGy7BNkhQcRUsAzMJciOj7ymgmVg\\/rkmjtOvMHwD5KXuS6rX3a8jXhO9ize3FcqPmFq3lb52RG9XfvYJwUyt+wVm2vCUD234tfF3Sdb4E7PL1LpTQ8scl10nZpepdb\\/AbMt8yq6vjb9LMoFjzphaeWLOHJczJ+RMM7XyxJzhL+sdNP62PoIz1TKFc08amSe+OPplAl2bvewWLcEeEp5yminV33BMN\\/JKg7vEkDXNVOtkX\\/fCxGem5e+gv4w63q9uAuXv0aOVm9LP+xGhparvzBoqTzM1+Ruebanqag9vi0R4pizneK788C5KhGcqa44X+C88ATVTXHM8N3HwHk2EZ+pjiic+XyTkSzclMsfz+SIhX7qpkTnep+nqDU8NG6Wz0kzdr25Rc+k27B2GWtVtbZQ4xVVxtapKmOS6rY2SpJ3mZ7kWZrluK1qK92mae82tZtDl2SuPuWdrY0uyll98rnH1udva+AvPsZSvVUkF2tbGFM\\/XqrgK3W1tzPD8QnSNK9Hd1sYUz9equBo9bG1M8Xy+xBXpYWtjiufzpYd8GbZSpXj+Ly2SSH7L32\\/YKJ2VZup+dYuaS7dhLnaEWjVsbfxZD3ci3i8Y19FCPFsbf+HZFh1+rokr1MNWtBTPP4fHRephK1qK9\\/E5\\/Mu9anY1hy7TXkx4lDCHzJhEtqwm1e7qV5DrjElky2pS8K7TJ1Fcsh62rKZ4PoniqvW0ZTXF80Vghgekactqhrc+rR3V91cIL63U3eoGNY8u21xcXN2etnCvuL5U\\/fp2jQvc05bRFZeYql\\/irnGNe9oymuL5ohCXuactoyme77+40j1tGc3w\\/Fp3jYvd01awFM\\/lSxYBb+35pZW6W92g5tFl2zsKNWvaGrnj37PV7WtWXOqetkbu+Fdt1S9217javWyNzPD8enc9YY5btkameL7\\/Tpjjlq1gKZ6f407Il2UrWIrn8iWLgP1q\\/fuLhNfd6gY1jy7b3lGoWcvWyPPrdoi9I1ez2BAT4dka+QvPtKD4VWkpoWYtWyNTPFezpMRXj2yNTPFc\\/0kJc9yyFSzF828glZgvtoKleD5f4sL3thUsw\\/ML3xIXvretYCnexznuKzw1bJTOSjN1v7pFzaXbsHcYati2b07WOOdJ9W+S1TDnbfv2ZI01U6qb86SGOW\\/bNygzPHFznkiY87Z9izLF8\\/0nYc7b9k3KFM\\/NeQlDXzqpm9X8ahZdrr2TES3KHhKTyb7a+QvHJq9fFZe4Kr7t653xvm4DpIcXV8W3Ka85nk+mFpLpmPKa47lkkhaKz7GviKd4vvi0kC\\/HFrsUz+dLC\\/lybLHL8PqnRQKRNz28v9146aw0U\\/erW9Rcug1zsXH1\\/Njy2uOigfjVc4mr58eW1x6\\/dIpfPZe4en5seU3x\\/ANVXD0\\/tqKleL7\\/kmvatqJleP5NbYmvah9b0VI8ny8j5EsttqSlgD5hRkiYWmxNSwFdxmQx\\/\\/5+4w0cJbQSTQmgjlGD6T7sLe5oseaQ5Gp3sZX3F5CtE\\/7tbkmudxdbeuOd3Q57FzAhmq2EKaBPtJkQzZbCFNCXphlKUy22FqaAvjYld7uLLYYZoH+9W5L73cVWwxTw09c9GW9+fov7L6mVa0oB9YwaTPdhbzHuJCi2AK\\/kfW8FnUNxN4HdnPMN0LGpf+Za4Zmr2g02KeD2D+k7PKRXu8kmB\\/R9uMNTerUbZXJA\\/5ge18Wr3SyTA\\/qkiQvj1W54yQF90sSV8Wo3vaSAH18Ff8NUo0dJrVxTCqhn1GC6D3Ox8e3xarfKSHJ\\/XPwL5BLfIK92u4wkd8jFL17LiTOi3TKTA\\/oZMa6WV7ttJgd0fcjPlgkBnZ7qGLD59fIW18ur3T6TA7qkaXHBvNotNDmg38sdV8yr3UaTA358Xn97Hd7oUVIr15QC6hk1mO7D3mIsbnZLTSvx4btVV9xajTOi3VbzDdC2qV\\/EbjXOiHZrTA7oZsQWV82r3R6TA\\/o+jMvm1W5xyQHdjNjiunm121xSQL9w3uLCebUbXXJAnzRx5bza\\/SY54Kel8zdMNXqU1Mo1pYB6Rg2m+7C3GIub3eLSkgJ5E\\/\\/TKiTOiHZnSksqnk38jJh8xojdmpICBp8z0uKMaPem5IC+D+P75dVuTskB\\/YwYXzCvdndKDuiTJr5hXu32lBzQJ018xbza\\/SkpYPBhJ0nkv3X5N3qU1Mo1pYB6Rg2m+7C3GIub3RLTflbzncjvfkbscUa0O1S+ATo29TNijzOi3aKSA\\/oZsccZ0e5RSQGH78P4nnm1W0tyQD8jxhfNq91bkgP6pEk+C8VuLskBfdIkn4did5fkgJ\\/eNX\\/DVKNHSa1cUwqoZ9Rgug9zsUmp3W5oacnN8uaX2ltSarc7WlpyFbr5pfaWlNrtlpYcMPi8rzgj2j0tOWDgwzgj2i0mOaCfEZNSu91jkgL6pfaWlNrtJpMc0CdN8nEqdpdJDvhpj+gbpho9SmrlmlJAPaMG033YW4zFze4paT+vxXsfTednxKTUbjeVfAO0beqX2ltSare7SnJAPyMmpXa7rSQH9H2YlNrtvpIc0M+ISandbvvIAX3SJKV2u+8jBfRL7S2+hV7txo8c8NPe4jdMNXqU1Mo1pYB6Rg2m+7C3GIub3WvSfl6X9z4o0s+ISandbjb5BujY1M+ISand7jbJAYPPwowzot0MkgF2v9Tek1K73Q2SA7oZsSeldrsdJAf0M2JSarf7QXJAv9Qe306vdkNIDvhpL\\/Ibpho9SmrlmlJAPaMG032YiEmp3e5B6T+v0Xsf2+qLW1Jqt1tEvgHaNvVL7T0ptds9Ijmg32WTlNrtJpEcMPBhnBHtLpEU0C+196TUbreJ5IA+aZJSu90nkgP6pJGYNHajSA74aWvWG6YaPUpq5ZpSQD2jBtN92FuMxc3uTenfPsnX3qIvbkmp3W4V+QZo29Qvtfek1G73iuSAfkZMSu12s0gO6PswKbXb3SI5oJ8Rk1K73S6SA\\/qkSUrtdotGChiU2uML6dXu0cgBfdIkkf\\/W9N\\/oUVIr15QC6hk1mO7D3mIibraa9vgOX\\/dL7T0ptdt9Id8AHZv6GTEptduNISmgX2rvSand7gzJAX0fJqV2uzUkB\\/QzYlJqtzs1ckCfNEmp3W7VyAF90oyENLa2ZYDz0zt8b5hq9CiplWtKAfWMGkz3YSImpXa7PaTP+A5fDz63PCm12\\/0h3wAdmwb\\/fEKcEe0GkRzQz4hxqV3sBpEc0PdhXGoXu0EkBfRL7T0utYvde5ED+qSJS+1i917kgD5psk8vt7UtBfz0Dt8bpho9SmrlmlJAPaMG033YW4w\\/qNru9ug\\/P4LGiXy\\/1N7jUrvY3R7fAG2b+qX2Hpfaxe72yAGD61hhRhS72yMHDN4R40+ptnsvcsDgl88xaZx\\/GCUDDG61x6V2sXsvckCfNPEnvojde5EDfnqH7w1TjR4ltXJNKaCeUYPpPuwtxuJmd3v0E9\\/h636pvceldrG7Pb4BOjZ1MyL\\/hbUQ0Na2BHD4pfYRl9rF7vbIAV0fjrjULnbvRQ7oZsQRl9rF7r3IAV3SjLjULnbvRQ7okyb+JBixey9SQP+jYLLIf2v6b\\/QoqZVrSgH1jBpM92FvMRY3u9tj1PgO3\\/BL7SMutYvd7fEN0LGp\\/69TxaV2sbs9ckA3I4641C52t0cK6JfaR1xqF7v3Igf0\\/3WxuNQudu9FDuiTJi61i917kQP6pIk\\/9EXs3osc8ON\\/1uyt6b\\/Ro6RWrikF1DNqMN2Hudi41C52t8dI\\/iHBYZba\\/x+OUAMsDQplbmRzdHJlYW0NCmVuZG9iag0KNTM0IDAgb2JqDQo8PC9UeXBlL09ialN0bS9OIDMzNC9GaXJzdCAzMTQ0L0ZpbHRlci9GbGF0ZURlY29kZS9MZW5ndGggNTIzND4+DQpzdHJlYW0NCnicnVxdjx23sXwPkP8w\\/2CH3yQQGMiNE\\/giiGFIAvJg5GEt77WFSFpDXgPxv79Vc7qkBdIkgX2QzuxZDmvYXV3N7SFZUjrOo6R4lIyPeoSQ8FmOUBs++xFjxWc7YkezfB4pDfw8jjTQLscjF94ajnKiXUYfteAzHTWgXa5HbR2f5WiEiONoA23zOMbJdv0YBe1KOMIZ0LCcuGhoWRKfBE3RfQgdbQueKRIADxriYON2hIT\\/SsFj58DGAxcND1r6EUrk06Dn0vGoFT1XjKNU9FwHHrai51Y4KvTTTzxGRT8jpmsMYTQ2hgHOxMawwDnQGL3HkNl4wDYnGuNxYSQ8cwtHTAGNW8ZFwzO3dMSMQZdWcdHxzPg20mwFZon1xDMDJlYMrsDAsQU8c0PPDcilop8e8RgYQBw0cgfWGGxcjnTSZT0fKZxo3Bsu6LRejxQjG8NLl9sAnFJCY9g+XY7DSFLGuAt+SJfrRsQFjFTQe7qcN9BzbWyMni\\/3wWqpofvS0U+HU+uJfi4PjnHkE9SpJyhBD3JsOYA89cy46GycjhxBnQq8TA9WcCCnwsb9yPRgPRsuYP4aTlIr4xv0TGvVgJ7pwYp2uWIoFcbK9GAFcO4nB4h+eiX70A89WOmPM4UbH+nBSp4FEKnSRvRgjRh2BJEqfU8P1hhJejSOZCk8WEmm3NmYhIMHKwODJql0MD1YIx2M560kLj1YE62Pzmqgz2C2erkBNq6JxspsXDlaNuawQaSKsKv04DXaCMpUBF6lByvNl+DPyiegBytoUQuMXTkkerAy+mibyvCjBytd1SIbdwYkvuad\\/eoV\\/XR4ryIG2+VBGLSdIFJFDLbLg\\/BQQ4QcHGS7PAjmtTjYuCGy6UHEYMsnGyPGLw8iBluB1fEsuKAH8XDtAoSH2uVBjLY1Ph1uaPRgBedbLxzgpRV4DNixXx7EM\\/WTRIJj+uVBxGAPJBII1y8PIgZ7IpEQg\\/3yIMbfM4mEGOyXB2HQTkmjNPXLgyBK57grBtkvDyIGeyOREHH98iCc1\\/EVQNHPoK\\/wKOPyIGJwBBIJMTguD8LEI5JIiMFxeRA+G4lEwrDH5UFwcWS6EXYclwcRg4M6V8GPcXkQMThogIoYHJcH8bijkUjw2bg8iPGPjoYVYwOzYFrEYDjpQjiUwhrp2nxJLH0LATwjyAQv82rwDgjemTLvgHKedGRDKOKKPgtUZbqSpsFVu4iBKzqzBWJwxBQvqDfFPhCj4RFbIAYd2gIxBu5rJ\\/sbcFVDn4G\\/Jr9wFcCthqfFFUbVEJchRLIv3tIB7ojMB4l0QWiGQM82\\/MNV4x145kDftshURsEDT3F1sToSg5ZoiRj0b0vE6IE0JgY9TK3DAySOnP3BuvgO\\/UU6uTE5xnARH\\/1FurkBEVegG2IAV3R0w1hwBac0OJmpC+NAsOJqMFaYxejshnBFlJ1X2PAK7qYNQ+RYWyYGHd6YpmIHOiUPV3B5y8QYaNPgz5BO+KwxdabL58ydCTkOV+gvXT5n9kRWYGwyfV4+Z\\/5MkG9cDV7R58ygCTqJQA1XmmXIAjdRAGk5XNHnzKKJo25MmunyeSVGJxMrMS6fM7cmcJmagCR9wnsN9kLeps8rE3cgExHJcCp93tgukYmIZVzR53gKBD6Z2JjzL59jfCFzBtPwE67ocwR0yJQ9iMo1JcA42jU5IBM7MS6fd2J0MrET4\\/J5JwZC66CCh3LCPw2BHcrlc4wZMwsyEazAFX3e2S7FS7l4RZ8jlgPmQ7cJT8DEht9lXlHzMNJQmJvaYH+UpDZ4b7umSOyPvbZB3E7mQC1AZ\\/ioM6aR6CBrjCNkVcoiJzUn+uqMfWQb6BhjGqkNQsbYR7riHY2zIPCqM\\/aRX3gHxoGcUSiuvOoUVWBUTs86Y78yq3fGdKWYd8Z+ZTz3cE0UqbWMfYgln5n9QSrxHfpDsPOOxiuwsDOmIRm8Y\\/CK6s6YBtlxB2MfYco78Hwg9Uldx1XmHYxzBAnvYH\\/luoPtOCHpjPNWrzuIS+3sjPPGiOiM88bxd8Y53IiRM87hKNzBmIaxMXLGEczJO27TQoycsY8B8g5gYAgYB2Mfj4Y7qE0A6kwzuMrotTPOkWyYcdgfJ8Cdcd45UeqM8878yDkUJCfwDvZHzvdrOspRd8Z572BiZ5wj6TBfEWOAF51xhFk07mBMD+go0xqvEH+9XHNZPFtn7CMH4Q5q00gYDcFGLpxw4mJcfXCCe4ILMCguEOrwSkTuwP+YfZ4xcC6LC05j8XAwAkiS2Kbglj\\/96e6768+I83h19\\/ruu7s3v\\/\\/ycPf66dNvb5\\/++v7hw93fv0c\\/57+Ou+9+Ovgnx6uvvvrjH253wXG3u9587d1Wrj9RXrm3wm9LwPAywDoHbGvA+DLAPgcca8D0IkAwYQYI6i4B88sA4xwwrQHLywDzHHBDmvoywClpctedr3zvX85Q9IjU4pooIM\\/IYBqHP8S+HmLz7xK533zjD3FMAcu5BuwbQN+mJcwBN1IzXgY4l5qSl4DZ17Yt4NyHpa4BfW3bArY54Jo02de2LeCcNHVNmuxr2w6wzklT16TJvrZtAaek2UX+7feKHpFaXBMF5BkZTOPwh7gWt+yraRW5J5Ff5xmxrjNi9tX0C+DEpvOMWNcZMfvatgNs84zY1hkx+9q2BZz7sK0zYva1bQs4z4htTZria9sWcE6atiZN8bVtCzgnTVuTpvjatgPsU9LsIr9ajq+W46vl+GY5vlmOb9auWbs2V9O+Frfiq2kXuSeR3+cZsa8zYvHV9AvgxKbzjNjXGbH42rYFnGfEvs6Ixde2LeDch2OdEYuvbTvAMc+IY0MaX9u2gHPSjA1pfG3bAs5JM9akqb62bQGnpNlFfrcc3y3Hd8vx3XL8sBw\\/rN2wdmOupmMtbtVX0\\/H5Qd3I57udCSDf7SwBfTX9AujalEWmKeA6I1Zf27aA04zIN1NLQF\\/btoBTH\\/LF1xLQ17Yt4DQjsuC8BPS1bQcY5qQJG9L42rYFnJMmbEjja9sWcEqaXeTfwlTRI1KLa6KAPCODaRz+EDfi5qopX0YuIz9MxY0vT1eAzVXTZ4ATm04zIt\\/NLgFdbdsCxmlG5KvfJaCrbXvAuQ\\/jOiM2V9v2gNOMyBfXS0BX2\\/aAc9LEDWlcbdsDzkmTNqRxtW0LmKak2US+hamiR6QW10QBeUYG0zj8Ia7FrblqWj8X6CeRn+YZcVNqb66aPgOc2HSeETel9uZr2xZwnhE3pfbua9sOcF5qr5tSe\\/e1bQs4z4ibUnv3tW0LOCfNptTefW3bAs5Jk9ek6b62bQGnpNlFfrIcbzV9I7W4JgrIMzKYxuE+7KbU3n01zZ9fX\\/iRPy+1102pvftqmtfvS+q81F43pfbua9sWcJ4RN6X27mvbFnDhw3VG7L62bQHnGXFTah++tu0A56X2uim1D1\\/btoBz0tQ1aYavbVvAKWl2kW81fYsekVpcEwXkGRlM4\\/CHuBa34atpFbknkT8vtddNqX34avoF0LfpvNReN6X24WvbFnCeETel9uFr2xZw7sNNqX342rYFnGfETal9+Nq2BZyTZlNqH7627QDnpfbal6RJp69tW8ApaXaRbzV9ix6RWlwTBeQZGUzj8Ie4FLd0+mraRe5J5M9L7XVdak+nr6ZfACc2nWfEdak9nb62bQHnGXFdak+nr207wHmpva5L7en0tW0LOM+I61J7On1t2wLOSbMutafT17Yt4Jw0Y0MaX9u2gFPS7CLfavoWPSK1uCYKyDMymMbhIbZ1qT2drppyhfUq8tu81N7WpfYUXDV9BujatM1L7W1dak\\/B1bY94DQjtnWpPQVX2\\/aACx8uM2IKrrZtAeel9rYutafgatsecE6adak9BVfb9oBz0oQNaVxt2wO+dA2fhamiR6QW10QBeUYG0zj8Ia7FLbhq2sJ6DV+bl9rbutSegqumzwB9m85L7W1dak\\/B17Yt4DQjtnWpPUVf27aAcx+uS+0p+tq2BZxmxLYutafoa9sWcE6adak9RV\\/bdoDzUntbrzFP0de2LeCUNLvIt5q+RY9ILa6JAvKMDKZx+ENci1v01TSt1\\/C1eam9rUvtKfpqmtaro9q81N7WpfYUfW3bAc5L7W1dak\\/R17Yt4NyH61J7ir62bQHnGXFdak\\/+3os94Jw061J78vde7AHnpMlr0vh7L7aA5aVr+CxMFT0itbgmCsgzMpjG4T7sutSe\\/N0erazX8LV5qb2tS+3J3+3xDHBi03lGXJfak7\\/bYw84z4jrUnvyd3vsAec+XJfak7\\/3Ygs4L7W3dak9+Xsv9oBz0qxL7cnfe7EHnJOmrknj773YA750DZ+FqaJHpBbXRAF5RgbTOPwhrsXN3+3R6noNX5uX2tu61J783R7PAH2bzkvtbV1qT\\/5ujz3gPCOuS+3J3+2xB5z7cF1qT\\/7eiz3gPCOuS+3J33uxBZyX2tum1O7vvdgDzknTN6TxtW0L+NI1fBamih6RWlwTBeQZGUzj8Ie4ETdfTft6DV+bl9rbptTu7\\/Z4Bjix6UurpmYAPZcLPta88\\/eLtLGpgo4578aad\\/5+kWeAvo3GXKzGWqz83Rt7wLlYjbVY+bs39oAvXSljLpblZRA9p9cjzz9YDsHX27FeCdPP6Uyrn+uZlr8f5Bmga7N+TrWhn+uZlr8fZA84nWn1cy1G\\/n6QPeDChxsx8tVvCzgVox7WpPH3g2wBw5w0YU0af3fGHnBOmrAmjb87Yw\\/40pUwFqaKHpFaXBMF5BkZTOPwh7gWL38\\/SA\\/rlTA9TMWrh\\/VMy98P8gzQt2mczrR6XGc8fz\\/IHvClixrM4jKEns9\\/9A3\\/XJXsMa2dM6+y902V3d9Z8gxwYqu5aG2q7P7Okj3gXLQ2VXZ\\/n8cWcF5l75squ7\\/PYw84F620Jo2\\/z2MPOCdNWpPG3+exB5yTJq1J4+\\/z2AO+dNptYaroEanFNVFAnpHBNA73YTdVdn9nSc\\/raXqfV9n7psru7yx5BujbdF5l75squ7+zZA84nab3TZXd3+exB1z4cJ3p\\/H0eW8B5lb2XDWl8bdsCzklT1qTx93nsAeekKWvS+Ps89oAvXaxgYaroEanFNVFAnpHBNA4PMX5+vfo\\/jz\\/+7oFmHWJ0+9D2h9uH4d0Exv4QtLVR4fbXEQ+MvH2aXAXbWRFsnXWwLoNVZoKtxQq3vx95aO\\/t8zaP4Pk5t0+r2cZmvzf4ZLPSZO3TTYX4F8nt81YHvM5B5me22Wu+meLINxE58q1wf+RbGQ92sBMdbJZr50rplBmdOaEd6NqPqt1p2quiletax6pVbVrjojfeev+larhqY6rn6C96zf81hVTiEQ1dp3+ebN7\\/8P7Bc7qlFKnNf9\\/pBoQbf5\\/Xinz9+Pa3Dw8fn1w8I7BeB9ndz1q++fTw8Orx8enu1eP7h3\\/c\\/3JYfv3u\\/hP65G8Pm76yu88R9vm33z785+nvD78fwbr+G\\/r6+Pj0cPct\\/\\/vrxx+\\/\\/PAGTX94\\/M\\/d64e3T3ffPNz\\/+PDpds17dP2\\/H9+\\/+\\/jw+ud7PiG\\/+PNH9HD\\/9O7xo\\/386end\\/93j4vrpn4+f\\/v3D4+O\\/v1iB3\\/z688PDEx\\/y6e4f928\\/PT77+S8\\/4\\/9nP3\\/97v7940\\/Pvnj9\\/t2PBn21vV2i2U+f7j\\/c\\/e3dT799wlDePcHB34S7vzx+IOqfP779+REj+OX+4zXWX6+7zCTf\\/vbhVzjx0AIHfX3\\/4eHX728\\/fi8XHcmizYLMvhwWUrcPC7BicWThc\\/tIFjQWMxYqFikWIBYfFhYWFRYUtw8LFUXA7UOid\\/u43W7BYjFvoWwRGizygv2dG04pmP3e1n4FW\\/sVLKKDRWoIUjhrZxEYTIGCRVOwCVowJQhmvGDWC2a+YPYLZsBgFgxmwmAKEuxtVjBtDmbNYOYM9hYrmDIFs2gwk4Zmz2NGDWbVYGYNZtfQpczWzvQ+mG2DGTfYKtkwpODWzhQq2lq7aJkh2lq7aGvtomWKaPaOZu9oyhZtxU60FTvRVuzEqAxhvzc7R7NzNCWNNhGOydpla2d2jlkZxp7H7ByLMo61K9bOQiEab6MRN9oZQbHq99aPcTiavaNlomgZJnZ7HjtpJFomjGbvaPaOZu9obI5G56iMafZOpzJisk8L1lMhfNqnZUjLtMnsnYIyqbUzeyezdzLVTpYJkzTBMnySOEgdjN\\/J+J0s06ZseHaqWrKZQSrK3NbO+J2M38nsnszuyeyezO6pKuNbO7N7ahIt68fsnszuyeyezO7J7J7M7sl4nszuaUj9TP7M7tnsns3u2eyeT8lktE+bcZjds9k9R81MrJ3ZPdtMKpvdc5LeGp7ZPZvds2RZuixhNrtns3s2u2ezeza7Z7N7Nrtne0ueze7Z7J7N7tnsnptmUNauKRNYO7N7Nrtns3s2u+eumZe1M7tn05dsds9m92J2L2b3YnYvZvdidi+mL8XsXszuxexezO4laqZn7czuxXSmJOUywzN9sVNhdVirzlDV0aY6cVTHcuq0TB1iqbMldeSjTmLUAYk6t1CH++nMPR2FpxPqdHCcznPTMWs6\\/UxHhOnkLh2opXOudPyUToXSYU06Q0kHDen8Hx3Lo9NydIiNzpbRkS86iUXHlegUER3uoTM3dBSGTqjQwRE6z0GHHugsAh0RoJ372lCvfe7afq5d4do6rR3N2mis\\/b\\/alqvdstrEqr2l2oCpfZHarqhdhNrcpz132gqnHWraxqXdVdr0pL1I2iKknTvaUKN9LtoMoj0a2jqhHQ3aaKD1\\/1qWr9XyWlKuld5agK110VqurFXEWtyrNbdamKr1olrGqdWVWvSotYhaIqiVe1replVnWgymNVpaOqUVTVpopPU\\/WiSjtStaUqKVHlqAoXURWq6gVQR61a434HqtrLe9egmrd6N6waj3fnodp7dkenmld0p61aM3MHpNobcHKuqrQq7CterJKvOq+qqiqGqVKiGqzqbyl6pSKhaphqPSiioeXwoRf\\/zD\\/wPubgbMDQplbmRzdHJlYW0NCmVuZG9iag0KODU4IDAgb2JqDQpbIDIyNl0gDQplbmRvYmoNCjg1OSAwIG9iag0KPDwvRmlsdGVyL0ZsYXRlRGVjb2RlL0xlbmd0aCA2MzI2NS9MZW5ndGgxIDI3MjE0OD4+DQpzdHJlYW0NCnic7H0HfJRVuv453zctUzIzIZM2CTPDJCEwJKEESADJQAq9M5iElpBCwAABQhEBoyhgBHuv6Kq4YpkMKAEbuljWgr2snV11dRXL7uoqCLnP+d45ENjV\\/717967X+583eeZ5znvKd+r7nfxIfjDOGHPgQ8cml40snZ7f234X4759jPEny0aOL7musdzGuOcSxpS+k6bl97\\/xsdr7kbcZtaprF9U0X\\/bF+TMYa5rMmLqldmWLd0\\/zmwMZu1XHmP6hhub5i9a\\/pw5mbMk7jNkC85vObHitbGcBY9vRvv3DxvqaOsOYNX9Ae1a0N6gRDts96YeQLkU6s3FRy+rhY42Hkf6EsYWNTUtqa2595eZujO2+jbHBCYtqVjfnLc\\/6I\\/IbUd67qL6l5rpzt61k3C\\/6d97imkX1pZFZLzP2RQdjfTualyxv6XSzjRhPsyjfvKy++fYjCx5hbJ0Jj\\/ueibkwlHkcpWd3m2sf9g1LhRv24GdrnxP8Udni0iOHj7bGfW4ahGQcUxgZ6hnYMcb3m7cdOXx4W9znWktdLPVu4XFvZq3MwYZBK+B8tomxhBHaczlTdQF+CdMzk\\/5a\\/QA02Z1YfZFtVJiJKXa9oig6VdF9yPI6H2eZZ2k9gE2Y5vWyIGNZz1EfjDcp2V7GO0WeulsfL0bKEnXxJ3rDX2D\\/35vhDXb3z92H\\/yumq2e3\\/Nx9+K+YwfA\\/01\\/10C9rHv4nTFfAqn\\/uPsTsv2\\/KM+zan7sPvwRTPmKj\\/5l6\\/FvW9K\\/uS8xiFrOYxeyfN+V6bv7RvGp26N\\/Zl1+KqQPZhT93H2IWs5jFLGb\\/vOkeYw3\\/9mcuYhf9u58Zs5jFLGYxi1nMYhazmMUsZjH7v2uxnzNjFrOYxSxmMYtZzGIWs5jFLGYxi1nM\\/ncbj\\/02esxiFrOYxSxmMYtZzGIWs5jFLGYxi1nMYhazmMUsZjGLWcxiFrOYxSxmMYtZzGIWs5jFLGYxi1nMYhazmMUsZjGL2f8S69z7c\\/cgZjGL2T9tOkAFMqP\\/C9ZlSEEp68Fm8OcosRj+JOaAxwBlYz3YBFbHlrFtGUXeuKznOrX\\/vQp+79\\/5eec3iBF\\/Yw\\/wtM7azzYd6vn+adHnJPxdT1R1rHo1M\\/DPtdTXp\\/6vXNr\\/w0X\\/h5fCftp4l\\/b+J6z0v1KYp\\/1E3pb\\/blf+zab+S1v79+4sq6a3RX3WLvlcewZB7C1vFGK0PoI6FtyDoO2qCVGIvVBH0FZ6GUFb2W1RoJ2MIoJpH5qOI9j6iv8HjqCPC1ZtPL9l+bKlzUsWL2o6Y+GCxvkN9XXz5s6ZPWtmVWVFaPq0qVMmT5o4Yfy4sWNGjyovKy0ZOSJYPPy0YUOHFBUOHjQwPy+3T052Vqa\\/hycl0emw2yzmOJPRoNepCmd9yvzl1d5wdnVYl+0fPTpXpP01cNR0cVSHvXCVn1wm7K3WinlPLhlEyYZTSgapZPB4Se7wDmPDcvt4y\\/ze8POlfm8Hr5pSAb211F\\/pDR\\/S9ARN67K1hA0Jnw81vGUpjaXeMK\\/2loXLVza2lVWXor12i7nEX1Jvzu3D2s0WSAtUOMff3M5zhnNNKDllQ9oVZrKJx4bVrLKauvDkKRVlpW6fr1LzsRKtrbChJGzU2vIuEH1mF3rb++xr29LhYPOqA9Y6f13NrIqwWoNKbWpZW9umsDMQ7uUvDfda82EKhlwf7uMvLQsH\\/Ghs3NTjD+BhfZbD7237hqHz\\/kOfn+ypiXoMWY5vmJBiiMenCflSM\\/QNPcT4fD7Rlws7gmweEuHWKRWU9rJ57ggL5gcqw0q1yNknc1whkdMqc45Xr\\/b7xFKVVUe\\/VzamhFvneXP7YPa17yx8I98bVrOr59U2Cq6pb\\/OXltK8Ta8IB0shgjXRsZa1981H+ZpqDGKBmIYpFeF8f3M40T+SCsDhFWuwYFqFViVaLZxYEmbVtdFa4fyyUtEvb1lbdSl1ULTln1Kxhw3o\\/KC9wOveOYAVsErRj3BSCRYlu6ytoq4h7Kl212F\\/Nngr3L5wsBLTV+mvqK8Uq+R3hHt9gMf5tCdqtTC2U0rLwmLkxiyTt0Jxq5ViteDwluPDP3IYMhxYLi0pVnTkMG8FdzNZDE+JlhDqpHaQULNKRossVVQtGe32VfrIfqJL7mif9FlhU5e2HHAc7xM950e7RqVFh3p5y+pLu3TwpEb10Q5GW\\/vH\\/VTEXEQfjBomsZyjZZaahZMLn4JmNJdYxRRvmE32Vvjr\\/ZV+7KHg5AoxNjHX2vqOm+YfN6WqQlvt6C6ZflKK8gspFWY+ZMuEUoI9WB5wy2XV0qO09PHk6FOyx8hsv+hXW1tdO1OzxFZ2t3NN6EsurAxPClT6w\\/MCfp\\/oZ26fdhOz+qZXl+CsliPc+ctr\\/F6Ht7ytpqOzdV5bezDY1lxW3TgE56LNP6auzT+tYphb6\\/zUinXuNeLZCWwcHzd9JJpS2Mh2P988pT3IN0+rqtjjQPDfPL0ionClpHpkZXsm8ir24HUT1LyK8AqnSHhFQrQ0FQmTVt69J8hYq5ar0xxauraDM81nkj7OajsU8jnoQdnag4J4w9V26CgnKEvr4DORr5VK50RLm5DjEDl7mSLuhSKTrJ2JCQ6a9UFTMC5oVWwKplS4IvDsRdk4znZauY2729HmVM3dwVvb44LuPVpLU6MlW1FS+FqP+9BzUaxLQ3geDTx0YgShqoqdVob2tU+UGCkMuzClEXsI75Myb53Yf2srG9uqK0X0YEnYq\\/jmYe4fzsKKfzh6bLCGzf76kWGLf6TwFwt\\/MfkNwm\\/EzudJHIstgm5btR+BGCemgrk5nTVVNOnt6OycXuF73n2o0oezNAuoqgjHBfBy02eNRblRAtVwjwq31taIfrBQhahrzBpTW4lzKRtEkTHhOLQQF20BJcq1OuK8oVIt9lqNX5NwI3S0VoYrA+KhFQsqtfPqCLPR\\/iFhQza1qc8WD8qvbEvw99eCD866OWuToDj0jU2rII8bSTyskibJaEXPa\\/3Iqq320h6ZhrNMLwuzmzz1iPm67HoNZnc0k4lhqVkWmzkcl4cG8S20JU\\/EHH2WsbKSOq+lNkUL4NmOsAU9yu4yldEKmB1kjRF9wfcmdFUUfUw0M6WDTfWvRugUndZaMiI7bMsaU4O3G9W3wOMvlJVNIghaom3sJ69RjNyKeUdI6Ojc7j\\/T18UQO8TbT+w\\/5t6Dg8oq2051hGcGcvuYTvXaNHdbm8n2jyvQfJlsx1lzKlm14q0AFhtO22\\/eMvGq9I9tVyYGNOYat4314w2iZAngoqPi+Pi8dZWiFLo8WYtlP1qIdykkXtNa422OoTLFoylazLbw\\/JOTjceT5QK4DGbl0R0CQxGxFntloTvchJ0pi4gV8bZ5Hf4hfvGhVR4lUI1FOn4ssP2x68Shaa31VszDZkeD5dVt5W3iilpbE5226JPCiwMnNYlzwbF50JAYTrh1sre60luNqymfUuHzuXEawd4G3FP9NeJVMJnGM7lKu6rUtIktznBTqXSHjXgxNdTU+314g4RFBKLZF33URY8Nc7e1+dvC2rktR2E0n41jN0YQvpsD\\/pp6cYVuEDfoeq1uObqrzY5ozV3mx1muh1ubS0wcQt888VHbJi7os6sDmAlnW0Kbt6gNIXg23h667NoZ1XhViTeSV1vqGjdSmIQxIlWJhqhgXJYoSEdA9GZRoH22MeuER\\/teEqDCJq1V9GxqRXiyLKKdJyGWBsJKciEyxeD51KoKGadUkT0G0xvErnKL2t6wMr0iujxa\\/TGiqlsuGFWDR3uHRM\\/X8beNfA\\/NcmNOf9SPl4M6YprytPIkK2Qe5akov8sKlbdYSPkd+A3wm1F+Hfwa+FXwK+CXwS+BHwU\\/An4Y\\/BALMZ3yNisApgPqcVUH3Aa8CujZGWiJMwvqc5aoPM5KgTqgBbgC0KPsI8i7DS1y5lXO2xWXwsdiQTdIca4U50jRKsXZUqyXYp0Ua6U4S4o1UpwpxWopVkmxUooVUrRIsVyKpVI0S7FEisVSLJKiSYozpFgoxQIpGqWYL0WDFPVS1ElRK8U8KWqkqJZirhRzpJgtxSwpZkpRJUWlFBVSnC7FDClCUkyXYpoUU6WYIsVkKSZJMVGKCVKMl2KcFGOlGCPFaClGSVEuRZkUpVKUSDFSihFSBKUolmK4FKdJMUyKoVIMkaJIikIpBksxSIqBUhRIMUCK\\/lL0k6KvFPlS5EmRK0UfKQJS9JailxQ5UvSUIluKLCkypfBL0UMKnxReKTxSdJciQ4p0KdxSpEmRKkWKFMlSJEnhkiJRim5SJEjhlMIhhV2KeClsUlilsEhhliJOCpMURikMUuil0EmhSqFIwaVgUcE7pTgmxVEpfpDiiBSHpfheiu+k+JsU30rxjRR\\/leIvUvxZiq+l+EqKL6X4QopDUnwuxWdS\\/EmKT6X4RIo\\/SvGxFB9J8aEUf5Di91IclOIDKd6X4j0p3pXiHSneluItKX4nxZtSvCHF61K8JsWrUrwixctSvCTFi1K8IMUBKZ6X4jkpnpXiGSl+K8XTUjwlxZNSPCHFfil+I8XjUjwmxT4pHpXiESkeluIhKR6UYq8Ue6TokGK3FA9Icb8Uu6TYKUVEinYpwlLcJ8W9Utwjxd1S7JDiLil+LcWdUmyX4g4pbpfiNil+JcWtUtwixTYpbpbiJilulOIGKa6X4joprpXiGimuluIqKa6U4gopLpfiMikuleISKS6W4iIptkqxRYoLpWiT4gIpNkuxSYqNUpwvhbz2cHnt4fLaw+W1h8trD5fXHi6vPVxee7i89nB57eHy2sPltYfLaw+X1x4urz1cXnu4vPZwee3hy6SQ9x8u7z9c3n+4vP9wef\\/h8v7D5f2Hy\\/sPl\\/cfLu8\\/XN5\\/uLz\\/cHn\\/4fL+w+X9h8v7D5f3Hy7vP1zef7i8\\/3B5\\/+Hy\\/sPl\\/YfL+w+X9x8u7z9c3n+4vP9wef\\/h8v7D5f2Hy\\/sPl9ceLq89XF57uLztcHnb4fK2w+Vth8vbDpe3HS5vO1zedri87fCSnUJ0KOdFug\\/34M4c6e4CnUupcyLdh4BaKXU20fpIdytoHaXWEp1FtIbozEjGCNDqSEYJaBXRSqIVlNdCqeVEy8i5NJIxEtRMtIRoMRVZRNREdEYkvQy0kGgBUSPRfKKGSHopqJ5SdUS1RPOIaoiqieYSzaF6syk1i2gmURVRJVEF0elEM4hCRNOJphFNJZpCNJloEtFEoglE44nGEY2NuMeAxhCNjrjHgkYRlUfc40BlEfd4UClRCdFIyhtB9YJExVRvONFpRMOo5FCiIVS9iKiQaDDRIKKB1FgB0QBqpT9RP6K+1Fg+UR7VyyXqQxQg6k3UiyiHqCc1nU2URW1mEvmJelDTPiIv1fMQdSfKIEonchOlRdImglKJUiJpk0DJREnkdBElkrMbUQKRk\\/IcRHZyxhPZiKyUZyEyE8VRnonISGSIpE4G6SOpU0A6IpWcCqU4EdOIdxId04rwo5T6gegI0WHK+55S3xH9jehbom8iKdNBf42kTAP9hVJ\\/Jvqa6CvK+5JSXxAdIvqc8j4j+hM5PyX6hOiPRB9TkY8o9SGl\\/kCp3xMdJPqA8t4neo+c7xK9Q\\/Q20VtU5HeUepPojUjy6aDXI8kzQK8RvUrOV4heJnqJ6EUq8gLRAXI+T\\/Qc0bNEz1CR3xI9Tc6niJ4keoJoP9FvqOTjlHqMaB\\/Ro5T3CNHD5HyI6EGivUR7iDqo5G5KPUB0P9Euop2RpGJQJJI0E9ROFCa6j+heonuI7ibaQXRXJAnxmv+aWrmTaDvl3UF0O9FtRL8iupXoFqJtRDdTYzdRKzcS3UB51xNdR3Qt0TVU4WpKXUV0JdEVlHc5tXIZ0aWUdwnRxUQXEW0l2kIlL6RUG9EFRJuJNhFtjLhqQOdHXPNA5xFtiLgaQOcSnRNxhUCtEReCMT874hoEWk+0jqqvpXpnEa2JuOpAZ1L11USriFYSrSBqIVpOTS+j6kuJmiOuWtASamwxlVxE1ER0BtFCogVUr5FoPvWsgarXE9VRyVqieUQ1RNVEc4nm0KBnU89mEc2kQVdR05X0oAqi06m7M+hBIWplOtE0oqlEUyKJQdDkSKJ4wqRIotjeEyOJG0ATIom5oPFUZBzR2Egi7gV8DKVGE40iZ3kkcT2oLJK4CVQaSTwbVBJJbAWNjCSUg0YQBYmKiYZHEvB+56dRaljEWQkaSjQk4hRbo4ioMOIcBRoccVaABkWcVaCBlFdANCDi7APqTyX7RZxiYH0jTnE284nyqHouPaEPUYAa603UixrLIepJlE2UFXGKWcok8lObPahNHzXmpVY8RN2pXgZROpGbKI0oNeKYDUqJOOaAkiOOuaAkIhdRIlE3ogSq4KQKDnLaieKJbERWKmmhkmZyxhGZiIxEBiqpp5I6cqpEChEnYsFO+zyPwDF7reeovc7zA\\/QR4DDwPXzfwfc34FvgG+Cv8P8F+DPyvkb6K+BL4AvgEPyfA58h709Ifwp8AvwR+Dh+vuej+EbPh8AfgN8DB+H7APw+8B7wLtLvgN8G3gJ+B7xpO8Pzhq2f53Xwa7Ymz6u2bM8rwMvQL9kCnheBF4ADyH8evudsizzPQj8D\\/Vvop20LPU\\/ZFnietDV6nrDN9+xH3d+gvceBx4Bg5z58Pgo8AjxsXep5yLrM86B1uWevtcWzB+gAdsP\\/AHA\\/8nYhbyd8EaAdCAP3Wc703GtZ47nHstZzt2WdZ4dlvecu4NfAncB24A7gdkuu5zbwr4BbUecW8DbLGZ6boW+CvhG4Afp6tHUd2roWbV0D39XAVcCVwBXA5cBlqHcp2rvEPNFzsXmS5yLzfM9W8+2eLebtnvPVLM95aqFnAy\\/0nBtqDZ2zozV0dmhdaP2OdSHLOm5Z5143bt1Z63ase3tdMMFgXhtaEzprx5rQmaFVodU7VoX2KhtZg3J+cFho5Y4VId2KxBUtK9S\\/ruA7VvDSFbzvCq6wFY4V3hWqtSW0LLR8x7IQWzZ5Weuy8DLd0PCyD5YpbBk3d3Tu27nM3b0cHNy0zOYoXxpaEmresSS0uGFRaCE6uKBwfqhxx\\/xQQ2FdqH5HXchel1+n1BbOC9UUVofmFs4OzdkxOzSrsCo0c0dVyF6VX6VYKwsrQqej6ozC6aHQjumhaYVTQlN3TAlNKpwYmgj\\/hMJxofE7xoXGFo4OjdkxOjSqsDxUhnlg6Y50b7rqEH2ZmI5OMTcf2dcddH\\/g\\/sqtY+6we59bTbCnedKUXvZUXjIplS9JPTv14lTVnvJCihJM6dWn3J78QvL7yV8m67oFk3vllbMkR5I3SXWJYSZNmF6ucXEpcb+B2rA9Sf7scruL210el1L2pYtvZCr3cs64A6SaUGYXd3nK1Ye5+L09PeP8EjY9MK7DxKaOC5smzwzzzeGsaeIzOKUqbNgcZqGqmRXtnF9Uqf16QjhR\\/H6Jlj5\\/61aWMXJcOGNaRUTdti1jZOW4cKvQwaCmO4VmKFIZmLN8xfJARfA05vzA+ZVTdT3qeMGh2O3cbu+0K0E7Om+P98Qr4qMzXg3G9xtcbrd5bIr46LSpSUEbPGJ8Pa2Tp5fbLR6LEiq2TLIoQUtxSXnQktu3\\/O\\/GuVOMk54caJmDjznLWwLaN1KVfIVIBoRXfC9vQVp8rdDSLHCSidrClnd1raA25y6HtUhnS+AXbfzn7sAv3+j3ekZ0KuexOmUDcC5wDtAKnA2sB9YBa4GzgDXAmcBqYBWwElgBtADLgaVAM7AEWAwsApqAM4CFwAKgEZgPNAD1QB1QC8wDaoBqYC4wB5gNzAJmAlVAJVABnA7MAELAdGAaMBWYAkwGJgETgQnAeGAcMBYYA4wGRgHlQBlQCpQAI4ERQBAoBoYDpwHDgKHAEKAIKAQGA4OAgUABMADoD\\/QD+gL5QB6QC\\/QBAkBvoBeQA\\/QEsoEsIBPwAz0AH+AFPEB3IANIB9xAGpAKpADJQBLgAhKBbkAC4AQcgB2IB2yAFbAAZiAOMAFGwADoAd2ITnyqgAJwgLE6Dh8\\/BhwFfgCOAIeB74HvgL8B3wLfAH8F\\/gL8Gfga+Ar4EvgCOAR8DnwG\\/An4FPgE+CPwMfAR8CHwB+D3wEHgA+B94D3gXeAd4G3gLeB3wJvAG8DrwGvAq8ArwMvAS8CLwAvAAeB54DngWeAZ4LfA08BTwJPAE8B+4DfA48BjwD7gUeAR4GHgIeBBYC+wB+gAdgMPAPcDu4CdQARoB8LAfcC9wD3A3cAO4C7g18CdwHbgDuB24DbgV8CtwC3ANuBm4CbgRuAG4HrgOuBa4BrgauAq4ErgCuBy4DLgUuAS4GLgImArsAW4EGgDLgA2A5uAjcD5rG5EK8f55zj\\/HOef4\\/xznH+O889x\\/jnOP8f55zj\\/HOef4\\/xznH+O889x\\/jnOP8f55zj\\/fBmAGMARAzhiAEcM4IgBHDGAIwZwxACOGMARAzhiAEcM4IgBHDGAIwZwxACOGMARAzhiAEcM4IgBHDGAIwZwxACOGMARAzhiAEcM4IgBHDGAIwZwxACO889x\\/jnOP8fZ5zj7HGef4+xznH2Os89x9jnOPsfZ5zj7P3cc\\/oVb5c\\/dgV+4seXLu1zMhKXMnaP9uYrxJsaOXX7SX7hMZgvZctaKr41sK7ucPcreZvPYBqhr2TZ2B\\/s1C7PH2G\\/ZG3\\/\\/Rzj\\/vB07U7+IWdXdzMC6MdZ5uPPQsTuADn18F8\\/lSHXTeU94Oh2dX5zi++LY5Z2OYx2GBGbW6tqUl+H9Cz\\/aeRgvXaQ7B4m0sgnartX42njTsfuObT9lDqawKjaTzWKzWTWrwfjrWCNbgJk5gzWxRWyxllqMvPn4bEBqLkohwGj6RKklrBlYxlrYCrYSX83Qy6MpkbdUS69gq\\/C1mp3J1rCz2Fq2Lvq5SvOsRc4aLb0aWM\\/Oxsqcw87VlGTybGDnsfOxapvYZnbBT6YuOK7a2IVsC9b5Inbxj+qtJ6Uuwdel7DLshyvYlewqdg32xfXshlO8V2v+69hN7GbsGZF3JTw3a0rkPsSeZPeze9l97AFtLmsxazQjcl4atDlsxhysxQg3dOkxzd+q47O1HmMXY2uLjnQ1\\/Od2qbEyOo+i5AaUpFZoHUQr606ZiUswBtInRkSpK7Xxn\\/B2nZWf8sr5uKHLzFyvpYQ61ftj+ip2I07gLfgUsyrUrdCkbtZ0V\\/9Nx8tu09K\\/Yrex27EW2zUlmTx3QG9nd+Js38V2sLvxdUJ3VcT3snu0lQuzdhZhO9kurOQDbDfr0Pw\\/lfeP\\/Duj\\/shxzx62lz2IHfII24dI8zi+pOdh+B6NevdrPko\\/zn6DtChFqSfZU4hQz7Bn2XPsBfYEUge0z6eRepG9zF5hb3Ab1EvsU3weZS\\/qP2TxbARj+r2Y5xvYHDbnXxndTjV9GnOxbZ3fda7q\\/E4dzRr4dFwh78Yq7WJb8GP74hMluYeZdb9niWxX57fqLHDO0bf0jcdu7fyS6RE1l6svI8qpzMiK2AQ2kV0dPj9Q8RCz4Z6SxIbw++93lZaaco2P4A6iMC9uMSbGeUnQrlNsu9PSiv27Bxq2qs4xHTx3V7FxK+7nxUffO3og\\/+h7hxKK8g\\/x\\/HcPvnfQ8fUBZ1H+gIOvHuzX1x1MTLPtbkLVgf7dTQNVw9Ym1Vks6gfjmoqDinFrExpJKQ6kHQgcyA8cCKCZQN9+ldzpc2pIjFeMxkSDv0eeMrBn9qABA\\/oPVwYWZPt7xCuar2DQ4OHqgP7dFTVReoYrIs3Vl3+oUicdNSjr\\/cUzBui7p9kTbQa9kp6SkDssyzFtZtawvAyjajSoepMxZ\\/DIHuOaynq8ZXRmuJIyEkymhIwkV4bTePRtffzhP+vjj5Tomo5coRqGzirOVK8xmxSdwdDRPSW191DfmBn2bg6dpZvDmWQyJjitOaWzjm50pYs20l0uauvoBMbZ3Z2HDQHM\\/jD2upj1oKN6ePNwxda3b3J+vjkvJSWto\\/OTnQ4+AfzVTnuUbRp\\/u9Oq8Sc7LYIVZ7B7Zj+r1ZyC4maHXXygoNmMUuYUFDHvxQ9erHNfMBUJljloiiUl2Zaf0i\\/P4MmZ4gklhPQhVgxLSC5yDijm+a8GDmpv+f7OAY7jyll0Wv6AAc4B\\/frOxjL+wzZSTjSCRcuSS+D083hVqJ7c7zzuLBCr111J5gM4lkxIlyFgSvSkJvu6mZRjA1SLKyPR1T3RohwbxU2J3tQUbzdjH3ejt29mShxfpecbLWme7NRFdnc3a5rJatTrjVaTbv6RK4xmo6ozmg1YomuP++\\/onWlNy3H\\/cLp6R\\/feqZa4bhkubOlbGFN\\/wNs\\/gXnYcNr73fAzNGNpSmIwLi7l+\\/g69\\/f6+az4UDF2c3QLW+NTvm+Kr9O7v29CFjZrsbZFxcD8PbK1gfkwGmNBHhxOsUPVH8a0Pb31SGJmZiJ3tj22oTScE9rUdOklDRsr+yieLc9tHJHhU2\\/zZZSd9+j6qVvmD\\/nhi371V4u\\/Bb6l87C+Hv0rZAtF73b1ceX2TOngncG4HrZ8c25ujwKzSDlZj4F1uUkWNSO7LqPR0ahvlMspFvNg\\/wQsXUJRkeNgf2dRkRiC\\/dTicuVOXTeD4f+5bkkufb2xmzc51ZtgVI5dqPPnYLfHqceuVYwJ3tRUT4IxO6XJ08eHReul4\\/2tqb5e6Q2pmclGi1Gnw4e66ofzrFbVEGdQ1\\/5wwXHvUz28YsGOFihPd++dZvH2EH8PjflQb8B8DGBBVidmZA8zK65d\\/RwBZ4H4lY3soc4OrJw9PeD8eOjQ5KJvvXXJ0dnQIlIRFrH\\/qwcxF69rS5kQGOr8uAklvUXfNkXLiqnQ4k5Rl7no2TNP9Z88CWKNXSIedVeTk5OS1C7LfYPJlZXu9rnM6gx7Zt8RBfO17etLNGH906rPn9k3Y+D4fu7cLJ+j0mz83NV3XPDKi4ZP7J\\/azYhJUOPiLX\\/uXZqfdmzS8cl41peRXT5\\/RMGMsv4Oi69vMOfTtFTlPf+wQOqxe1PzxV+hVXceUm\\/AHTgbkfwhLZ54iodyi7tIRIIiEQmKHA7xgehQJGJC0YP8e2z0\\/M4PRFDJjwab\\/Giw0dga9VsEK+aguZuv3FLU062L7y3+eSplbEEH1+2Mn6AfjwnGCaH9RgHj1WjcKNLChVlWTBE1dzWljI0XdXc1aZUx4zhDp+6+gbT3KIAnJTujgdylZmvh3pXYXRGTPVi9wehMTxQRdtS1M2u3nJ7Tf96lcydtCBoTPSnYk3F3lKwrLa4YnOoqmDHCd1qwvGcqggKm1WpaNWHGhA3t81oePG9UWYliMdpErLAZj5ZNO33YvLXB0nPrT0voXdIP5\\/Ba3P63q89g323UzmHzQJ5tj8Zhe3SKwF\\/tsjv4eHs0UNs7+HfBBBbshpgbdOLDCydLw4nNCsYFxmbbXd4xLjF12I4ivOzHfGmzps1Ze0AraG46UTKFih6PNpgdMRPGLtsyOkcu7SVoULYrhjiTKTkj05Xad+AQvymBoqghIT05KcNhzBoxpCjD5svMsOpUrs5L6u6Mi4szJeaNH3w0bLKYdDp8qOeZLHHYlBbThkGlPe2qyWyOi3djx41WnlDWGJwskw1kVWJWInGpAx\\/kFdhUufyCoMPpWZQap+aEk5b2v97aoi6P7pEibY8gKGmBqJtWKCkn3JS01Nr\\/+iatYHQ\\/FGn7gUff9v+p7TBosLIm1edMshvya4aNnFmU5h0xt7jf1ByjPS0xMc1h2JwzKiezwGO3du+fnTkmT\\/nQatMh8IzI75c\\/acGw8uWTAtnZPE9v0qmqzqQ\\/Ni0vz1tQ4s8sH+gLDBTxuEl5lr+kd7NcVi5GvLNHGsMqnx60ppn391zaw+7q3uxafmJFv96foI3S1tO8v+lE\\/n9iHQeJuEqrqOMvKTqj3mSxu5z2dK8\\/Se+gwaT6\\/ckpvbP93eJ9SUYd173sTIk36g16S0pOxrE7MSydGJuSYoWN8uQkm3QmQ3wyU7i581v+jn4O7pC9WJYYx\\/36LPcERzk6\\/u4B9PcBfVZQS6Ojae8e6NLNgWp2dNq7nXq\\/etgo7jfpCUYnN7n86W6\\/yxQfl5rj8fRKwYu0l8eTkxrHV5isYldZTepea4JVb7A6rUeKfAG3xeIO+Hy5qRZLaq6I84c6D\\/H7dHO1HhbSezlJqWNe5lKKHrA4eqO\\/Cxg669gv38oPCGcQ3hTRZcf+Lp3uqRb8WKevNNrdriS3w8Cdhm6Z6e4eiMBxSZkZ6dnJcXHJ2ekZmUlxfKC4UKj4UDqtDrNeb7Fbf\\/Bm9EyxWFJ6ZmTkpJrNqTno84Vqg3KdfkXXWXVnj3KMwqw+31+bVXdQS4tZfb7\\/SbMa7Y\\/xFE+SS9lgcCQnJKTYDcnmRF8y3iFx\\/Nimk3x9s9WNclr5C1Id63eyz+FgzIGfiat0M3UTcd+3s2TceXqyfDaYFbNRbBI7nc1l8\\/HT8yp2Nh+vvUEWT25smt5UuHrtsLU5zS19WrzVdZl1ptHjreNZsFRX6uhbkFjQtLalbnxpQUHp+LqWtU3G9IpZKeljl62cuHLkmvXl6\\/svXDxocVrVnO5zEqbOSJqhDBluGG7unReft3L94jkzhuflDZ8xZ\\/H6lcbshnn\\/wd63AEZVXWvvc848kslkTxLIO8Ejagh2SEKAiEgtIQ8SDEmchCiWnzKZR2ZgMjOdmSSEUkREpJQipYhIKUVKKeVyqVJKqeXSiDZSq0h9IL4wPi7XUvVSRGqt4l17nT2TOYfHD\\/zt\\/7f+ky3f2muftfdee+21114nk8ThRaT0mdJn0rNvLFW+IPd8pvziILAeGZfTg53G8Vem36QiklOad7kq4jZfM3zc2DHlIzgdwmk2p9HnRg2vpdrnxiw1f51m\\/Oh80gtlY8eWrWHwlzGjx4y+ltXO3lAOX\\/8+ZvToMaKN4Wd5rEG8Oyb72c6yseXl1wqjx44dLTzJHp6dyfAvTHoNq0lrAcqAO\\/vSmDGj3wBGeAAqbWy0bwAI\\/1FeOu6zOqjdX1Y2VpS50FkjVN5l3V4eWza2BCqff05Wis9Kb+jfFQ3sr9JApOU8uYnMZr74yKhc9mNV15SZGCHXjNsr3vOLkuwUaVgxqw0Lp4f14fik9\\/3ytPfZbv+KjDufZHy+Gzt06RJ\\/TZGuGXJOujtkzJDoa4r0hjEtN3NIPjX+UUi2ZFnSsmiy8JogGNNyoNViHDakNlvOTTP8TnremJGZmzHVNMScLL6th6wD8g69OOmzfRK8eko6gw7qj8faj+RlwhDpn50SUzPyLAa9OT1V9ZenzMwS+QgzZpSBlT5\\/1HifWGb8CN7gkx6BEFQ6pmy0dHXm1bVi92fLjR+5oc9v\\/jmKcNe\\/RPnDP6aIMy6jHP9\\/XaT7v\\/hFd9U\\/YXnwIuXTREmUL0bRT1OVn\\/wTlb8mSqJ8sYux9opLY6IkSqIkSqIkSqJcUnk+URIlURIlURIlURLlC1beTJRESZRESZRESZRESZRESZRESZRESZRESZRESZRESZRESZRE+QKUU4mSKInyr1jw9+hGicMBJVYVh2KLhL9zSJFjdZFQ3W5el8i1ugO8rouT0ZMc3XFeN8S1G0m37iyvJ5Hr9Yt5PZnIxhW8bhI3xeRTyALjDl43k+uTink9lRqSbLxOUYb\\/LqBgyrqd1wVizG7ndZEYc3bxukRycn7F67o4GT0x5\\/yB1w1x7UZyU87rvJ5EMrO+xuvJJC03h9dNQnNMPoXcnFvM62aSmevm9VSjlPsNXqcoIxFBlwzK5egjvK7YWakrdlbqip2Vui5ORrGzUjfEtSt2VuqKnZW6YmelrthZqSt2VuqKnZV6Ks2RF\\/K6YuefEpmUkzL4Nw5q0\\/Cvi4VIgIThn5tEoK0K\\/yqb8rfZ7NDihZqflMCTSuKDIhMbtHUQDzwLI+cC6gLpbkAnSqZCqQOuHVpdpAdammBEF4zTSnqxJpMGGL0Xxu7CWX1Q60BtZPgXwL9tForNI8f0LiNjoFYU424gVtTBDiMEQVaGee0wDxvDQeZy2anAeaCVPe0CHcOxNbXiX1gLowYX0seNtpDJZODb4QlrtaMl1GtUxgnwlco4Sxc8deB6oxbugb4hbOkCKSdaToZ2D7ZNI\\/WgE7OOF\\/v50bY3YX8XSrhIJ8zJLO1ElLlGUVkZ28O4r17QJbqDg+tgzyOghRd6hsEKVbgaL67EG1uHHf51Qg9FQ2U9dpxD5vvthRHZqHaQY2P1AtcDtQjuA\\/vbfe1Q96FOIbQFWy\\/724Ad3FLKqBFckzKnH1fkQE39OEsY96ked8UNLXb823QhXKOMVNkLL65JsUUYvSIMo9q5z7IdC\\/L26CydMI4P7RPkWvqhpRNnVcYMo6UGNWAzBnEt0b9dqNhW0d2HXsM8wcM9l2nF\\/k4f+\\/uHEeT8uNdRv1Zspsyi7KOfryuAtm1HyUGN41fErDYP+ymrngt8CZ7f+N0cgaN14gi9aIcuflLj7R31Pj\\/3ZLZ+ZV9C6A1RH3XhXjPPDcZWo+jYwWXCwM3no0dgFcoOdcd2yY4+wk5Ap2pd0ejjAE3sOL+Dz1+CEaYD94o9OTdmTThn1W3cc6KeXwGjlEOsuLCnR3BOJ3oim2VubA8GT+a5sbKD+3UwJs08V9lxP8i70Hf+b8VcE\\/5LxN1\\/lbjbAJo4SDGetZH8uUymoG8EULMIFBa1JpBSKD48RSG0YOc5XlTCfa8UNRoFY4ehHsF9D6Cn2nGneqHVjZpFQMqH1otAm0N1NqJzR2fuwufhWM9wbL7z+XMYz0QQx1NsFe3H9n4G7o8SlXpxPxT7RWI+EZWOxhAHj\\/MsIljRUkwuyH0nPqYHUXs\\/jyXKKC7O23n8dmH08eIKFe3aUY+oL2j3NcJ7KF4WOqfFHVuD9ZKihnKDONGmEX5TKWdZmdcam0e7AiXi9vC\\/Cuu5gM16+Eq9eB59ePKiUUJre9ZHuYWKQX6kys\\/PP7qiw5XaNv4UKZmAzO\\/yCO6cQ3WnalcweINq9bopzgfYSpS1KJlFNK6GYlmKE+9pP0Yb+wVXqvieXeVVStQIcFRWpdS78LwoUcyJd56XRyBlHCbpw5viwj6qRHw\\/35nB0aMnxBuXgXjwrHq5nUsw527lVmZriGYjUSurvdqKO2PHupNEczFtNNSehGJNXHBhLOrB7MOLu8921Q5tzEIdIBF9VsrH\\/Jomwo7kp3cwWgxmDlFtLucOu8Q7Qy7QjNEQHUMujHkz+6vLyj5FvUbJZHz8rhn07ovdg1GvvPBdyHauOXZywnF5i7Lfihe4+FxKJPfzfbfimkP8jormIEoO1cH3OerHil8FeW6kzBDAHN2O64x6ip0M5gLaePYP2IuYhey4dmY3L4\\/1Tn5WHTwv96Ou8TerF2\\/FMPom1\\/HCewv1FnU2ALs9Ms5Gzri3ifjzcMnjkcE3oKj0+aObVRPdorbX9vbhG4RXs+6oXoOZ2uCpGbyJontoJdE3OfbGFuVdcR4SxHc1JRfwxN2witbtqIuL31Rdsb2MjyXKHpbyHQ\\/jKfHFdIiea7UvXbpV4294ZZXxN43apwct0YN27LzCfYzeBl2YcSmWccVp4ERkcw7aZQ5IOOLujshF4rES+Z24guiNN0EVxdnf1g9gxDl\\/bu7HOyJ6y8S\\/y0XvifPFFHWvMMYKZa\\/a+brPf+faL7Cjodjqw+ilfhxdOUXnviVfqQdE77c6UoNPm0gtcLfBbWnDlnpokyGK2uBJG3DV0FoNLSNAooU\\/H4E7dRveQ3UgNx3vOGUMG2Aj8DMwxtUSGXnG3QLyjTAW61tDbsc5amC0FpS04djToLUBaA2XYz2qoGU68Kw+BaOgMl8j9FLeNOr5naho2grtcmyFaq3qccaoZtOAs8H4dfxpJYxdj+Mx\\/dn8tVhvjOlZyzWtRBuxkdmYVaBRA3KsdTrQZpBrwfkrcc2Kto24hlp4rqylBjVgM5fwtSpyzD5t\\/AnbI6ZfA5TBVVWiDepQm0H7VQFtBs3Z+FPgaSveEE3QsxpX2oLWq+E2Y6ttQG5wVcpOVeFqmFWZDaqhPg3+TYnZzoao6GKLG01tu9vw+aCUsr5KjlVouSbklN2oQq4V94o9tfK9tOE6tLPehp5Yg1KVuOKWmIfUovcq2ke9U5mjKU4TZT62t\\/G6RL1avsgZUUaJPp\\/Od\\/pcuzCrV6JNmF4tsZkvNDKczZ\\/K5WXl4+RpXkcoEA64I3JVIBQMhOwRb8BfIlf6fLLN2+GJhGWbK+wKdbucJXJqap2rPeTqkZuCLn9rb9AlN9h7A10R2Rfo8DpkRyDYG2J9ZDZ22Ri5iJEbrLLN7gt65Dq73xFwzIXWqQGPX67rcobZTK0eb1j2xY\\/jDoTkyd52n9dh98l8RpAJwKRyONAVcrhkpnCPPeSSu\\/xOV0iOeFzytPpWucHrcPnDrpvksMsluzrbXU6nyyn7lFbZ6Qo7Qt4gWyDO4XRF7F5fuKTK7vO2h7xsDrvcGYABYR67PwyjhLxu2W3v9Pp65R5vxCOHu9ojPpccCsC8Xn8HKAWiEVcn9PQ7wQAhvysULpHrI7LbZY90hVxhOeSCVXgjMIcjbJXDnXawrMMehDrr0tnli3iDMKS\\/q9MVAsmwK4IDhOVgKAD7wbSF0X2+QI\\/sAePK3s6g3RGRvX45wmwNmkEXWKMf5gq45XZvBw6sTBRxzYtAZ+9cV4nMlzkiLHfa\\/b2yows2VdGbmc8PRg7ZYS0hb5hZ1GXvlLuCbBoYsQNawt75IB4JwIK62ZLsMmxApzIXcx+Hxx4CxVyhEpuro8tnD8U8a0J06gnMH8a1gYnYFlSUlN+gMn0kZHe6Ou2huWwduKUx3+wAiwdZsyMAy\\/d7XeGShi5HsT08EnZRnhIKBCKeSCQYnlBa6nPZQ\\/6SzmjXEuhR6vKP6gqXRnqDgY6QPejpLXUH\\/JFRPm84UupQNGC9Wecuhz3MHoZZv8H5w13BoM8LvsSelcgzAl1gxF65C7wqwvyXNTPbOGC3Iy6r7PSGg+DTyh4HQ1546gARF1A77Kwr1OmNRGC49l5caNRDwXrgSoFQtOJmM1jPNQe4hrPLEbEyD+2GvlbWJzoBbFmPx+vwxGnWA5N6\\/Q5fl5Md46j2AT84T7F3pHJS4sRhhItpqxwscH9whXAk5HUoPhqdAF0zOtZNaIFiL8wCx4TFlxA7TM5Aj98XsDvV1rMrpgJng+XAjrJKVyQIgcHpYstkMh6XL6i2KAQrcGdFnG2IF4+Ox9vuBZ1LUlNbQWV3gB0gpjI3tVVut4dB14A\\/Fjyim1DMfcHlL+nxzvUGXU6vvSQQ6ihlXClIfo2HmZGwvegWeCzYMOePi+eLZ89xiQYm8Twz85wArImZBo6XD2IdmlsdOZkpVbEzNbWZbU4YzxOsG0zggl7g5GAZp1V2hyAOslMDZ7MD1sxsDLaCHYXucqAd4p+fGcWOsTvqZ5e+CqaQPRwOOLx25h\\/OgAOimD9iV0Ks1weWKWYjqlYrt\\/Dg\\/fxI1MiJAVLZh\\/PKYehlzXHuZuXuxrSPPvZ5wU+VudlYIeX6ghnwELEVWll497oZdaFBgl2woLAHDywM3d7FDm+YNXIvgRWWwsLDLha1A0GvEmQvqKpy4GFK5dBwS6MSPZ5A50XWyI5BV8gPyrhwAGcAwirqMsfliEQdbNCPwfmdXjx4ExQXt7cHul1xd7A\\/EGFHRonvXn6MFU\\/hj8IedkW0u1Qn1x630BCbPhwBZ\\/LCFsUuo4sZgJ23uhq5pam29bZKW41c3yI325ra6qtrquURlS3Aj7DKt9W31jVNb5VBwlbZ2DpDbqqVKxtnyLfUN1Zb5Zrbm201LS1yk02un9bcUF8DbfWNVQ3Tq+sbp8iToV9jE1z19XASYdDWJplNyIeqr2lhg02rsVXVAVs5ub6hvnWGVa6tb21kY9bCoJVyc6Wttb5qekOlTW6ebmtuaqmB6ath2Mb6xlobzFIzraaxFW7hRmiTa9qAkVvqKhsacKrK6aC9DfWramqeYaufUtcq1zU1VNdA4+Qa0KxyckONMhUsqqqhsn6aVa6unFY5pQZ7NcEoNhTj2t1WV4NNMF8l\\/FfVWt\\/UyJZR1dTYagPWCqu0tca63lbfUmOVK231LcwgtbYmGJ6ZE3o04SDQr7FGGYWZWlbtCIgwfnpLzaAu1TWVDTBWC+scL1ySmvisIPFZwWXYNvFZwT\\/uswLls+LE5wX\\/mp8XKLuX+Mwg8ZlB4jODxGcG2mie+NxA\\/blB1DqJzw4Snx0kPjv4p\\/vsAM6mpPz0\\/+c5ZOl5\\/pfA7Of\\/lZ\\/qJwL7DQIr\\/nbAxb6qpQfMZgFkhNZLlU9NRflFlypvsaD8jkuVT0tD+aOXKp+ezuRF3aXKDxkC8kAJ+y0HHcqzvnrYESLkkVRhBcmTppLrQKIc2idoZG+Ok80E2WtAtgQkJrLRNbKL42SzQbYIZMtBohLap2pkn46TzQXZkSA7DiRqoL1RLQsSg7L5IGsF2RtBYiq0t2hkO+NkC0G2FGS\\/DBJN0D6D+UtSkpBkevzxH8PXgw8m6YUkY1LSvGXwNc8gCQbdwCL2lSQISTqsLSKLJElI0m\\/atCkpWUhKeWzRY4s2Q1kDZRmUZL2QDCNEh9AJBv3DfaxfsiAk8yGUMZLZGMkmIdncB18PTXpo0mosK6CYDIIpSafTRVYsWbJkRcSoE4x8mEUmQTTpY+Ms0ukEk2EVfJlSBFNq3+y+2TDqpu\\/K35WXQ1kCJcUgsP+N5XkHSxHElOhgfLQUHC0lVUix9OX05Wwq3lS8qm5VHVvOPUn3JC1OMhsFc7IIXxNqF8NX7YQknZBk4AMuMgui2bBIPaTZyIY0U8GcNlAwUHBy4mHrUd9R38GGp59+YsWTKx43P25OTRJSTRJ83dTxOPvquAkNeXSgT\\/lKFcVUQ1\\/si\\/T16Q1CatLT7As9O+r37NyLTp+\\/g9dLwkq9jdUrQ\\/Z2q1wZ6vRb5arekM8qT3EF5iKGAEMuqLNvMlvlBnvEf3nSqIFJKhi6ixjt0JNY7dBTGOaAnsKkDugjDHRAH+E9D\\/QRZR\\/0EWtJAdHVVtrgLW5awwzIW1oaq2WycboNsB\\/i3OdkKDGSL5F8iJS3kq+Tb5MHyUbw8V\\/D0+fIMXKC\\/Dc5KQiCgZ1LkM4Av7fCmNPwvW0FWU9+CDF0H3mSPE\\/eIH8SRMFIvsIlk8koUghni\\/2OxXfI98kmiNb\\/QQ6SF8gAeU8AHyeTUDId4m4JGQYnhmWFK8kG8hBY83fkRfImeV\\/QCcnsPIPcEJICOfpVmLt4IeO6j\\/yAbIb4vZ88RY6Qt8gHgl4wwU3Iog2TN0OPcXC\\/\\/C\\/IQO8kq8gOyHv+TP4qpMC84uSmFpmktdgmsxyPSWeCJcykAvOFWZAL95BF5Lvk3wn7DbUfk1PkE\\/Ib8nvyEnlbMHP5TLgxboD4cTX5GrwvzyN3kdVkJ\\/kF7NNW8iH5G+mDmHOUvCOkcvksQsl4uHOGk9n4Xr6YfI\\/8jOyBGPETcpp8Sh4jz5CXyX8KNLYCgVwTq2eSbGIhN8Jddi1hv\\/swn9xN1pCHyS\\/hztpGPiKfkQPkEHmFHBcsYE+h\\/jbY8xzsqSNl5DrIXn\\/EudGkCHLTLaCPVN\\/cXEem2pqmQe7cartFJutQJht6pkFkLoe7cgTkxd8gS8gycj95hOyF\\/jPIT8kZcpY8Tp4lr5L\\/EtKwTzK5CW7YYsh9F5B7yFpyh5CO7TkkF3Z4IhkD0XMkZONB8k24W79FHiC7yK8gcn6VbCd\\/IZ+TJ8hh8hp5V8jgGuSBB32ZjIXM9XrInheSe8ly0O7n5FG4fWeSfyMfw3H4LfkDeZ38URgiDHWUhx2G+YiLEZcjrkZcj7gZcbvD7osYdiGeYGjMQJyA2I64FnETIsobdyPuRzyI+JzDHnYZX0N8C\\/FdxA8QTyN+wjCJIBocjs5gkhlxGGIxYjniRMRGxJmIcxDnIS5EXIG4D\\/E9xE8YJichZjl93o7k4YhWxArESYhTEVsR70CcjehG9Dn9gc7kEOI8xIWISxCXI65CXIu4AXGz2+u3J29D3Im4G\\/FRxD53yO5I7kc8jPgK4juIHyB+zNAkIpoRs7x+b8QkIxYhWhHLEcd7wwGf6WbEalDcbpqKuABxJ+IAw5QMRJRJQZmUZsQ2xJk+f1dnSjuiB9GPGEGcj7gIcSniCsTVvoDDl7IOcSPiFsTtiA8j7kXcj9iP+DTic4hHO11Ob8oxxHcQ30M8hfgxTBJKOcvQrEM0IaYhZiEWIA5HLEYsCQAxj0WcgDgJsRaxAdGGeDviLERnAHbbPAcxiNgdZO0LEBcjLkNcibgGcT3iJsStiDsQdyHuRdyP+ATiUyGHL2I+zDA1CbEM8XbERSHYjNTdiE8gvoD4DuJphlSHaArDiaNpiDmIwxCLEK2I5YgTwl6\\/m05CrEVsQLQh3o44C9GJOCfc6QjSIOJ8xCWIKxHXIW5G3BEOl42mexD3IR5APIh4CPEI4jHE44gfIJ5BPAtYbjEgmhEzEHMQhyFei2hFHIs4EbEasQGxFXCMZSZiO6IH0Y8YQZyPuBhxOeJqxPWImxG3A4617EI8gPgc4luIpximiYhJiBRxKGIeooxYBDguzYo4EbEBcSaiD7AibQHg+LQl7IPXtNWI6xA3Im5B3I74MOIexH3hrmA47QDiQcRDiC8gvoI4gHgc8b1wT9iTdophuoiYgTgcsTwCPpk+GbEOsRGxFfEOxNmIbkQfYghxHuJCxCWIyxFXsWwQ8oyhV0Dz4O6bCdnJLMgfZuP3yhz4HSs3fn\\/MCznIXMgTOvH7XUHIzUJwb7I3j4y4ftEeUfmo7D9CLueC+p5PW0VXCVaZD\\/ni\\/1lNgOzictAIWVYdZCNTIXtogFyVjaW0nL92rrwe8hw9++19yHcvzgmQrWrbsiBHHgU5bSlkXqMhixoDecw4yC1vgPzvRsirboJ86MuQUX8FMtHKK5C\\/dO10yCl1CbKydZDfX3pNgNxrEK86B1OvCG+9CEqQu6ZAHv73rwmk9aLYHMNseF\\/ogXy+F3Lsb0A2+03IPu+Et4G7IGO\\/GzLhe8C374V8+FuQkd6Pdvqi9BDg3YfhsAtgxhXhZMQqxGrEGoyCIv+LIKKKExBHxiFBtKEU+z5qlFOj6Yow\\/Yqw8BxsvCimXQH+\\/3A7GMh0eNO\\/Dd7LZ5A7\\/g68QL4UQ+ZPVyN39TlctI2o2uhloYRv9DlXXMvGmkDWnxdFMhze\\/S+fCmTDRVAk15LrroAK\\/NSdH0VSREZcATWQb5MV5DtkFfkuWf134AX8ftylIOu5ktwHPb9H1vwdeJH8kGwi7O6dgp8hdpHvX4FHCST3svDvvQoBv2d9YbRcLgrFok1sF0PiYnG1uFncJR4QnxPfEk9JopQhDZfKpcmSTWqXQtJiabW0WdolHZCek96STunMugk6j26ebplunW6bbq\\/uoO6o7l3dx\\/okfY6+WD9eX6e\\/Xb\\/UsNOw33DIcMzwgeGskRqHGUuMNxsbjbOMfuPCpEjSkqQ1ye3JC5JXmIgpzSSbykyTTM2m2aagaY3pgOk103sphpSslKKUipTalLYUd0p3ytKUTSkPp\\/SlvJJyIuUTs8mcZw6aXzGfTqWpw1LHp9al+lJXpW5KPZR6ipppO91Cd9Mn6DH6gSXDMtXitEQs6yzbLHvTctImprWlBdMOpw2knUyvTW9Ld6d3py9NX5v+cHpf+uGMgozWjMUZj2Y8lfFKxomMT4aYhuQN2TFk39DGobOGzhu6bOipTHNmQaY1c2JmJHNHZn\\/mkczjmWeyDFlZWUVZFVm1WW1Z7qzurKVZa7O2Zu3J6s86knU860y2ITsruyi7Irs2uy3bnd2dvTR7bfbW7D3Z\\/dlHso9nn8kx5GTlFOVU5NTmtOW4c7pzluaszdmasyenP+dIzvGcM7mG3KzcotyK3Nrctlx3bnfu0ty1uVtz9+T25x7JPZ57Js+Ql5VXlFeRV5vXlufO685bmrc2b2venrz+vCN5x\\/PO5Bvys\\/KL8ivya\\/Pb8t353flL89fmb83fk9+ffyT\\/eP6ZAkNBVkFRQUVBbUFbgbugu2BpwdqCrQV7CvoLjhQcLzhTaCjMKiwqrCisLWwrdBd2Fy4tXFu4tXBPYX\\/hkcLjhWeGGYZlDSsaVgGZLPOsJCVPyH9PzRcQDX+9hrfG8TrNcwMhhd3EKMTx121Q88Umdf\\/rZ6nHLz2g5ss86vHLFmj4lRr5hzX8Exr+tJoffYeG36vhB9R8eZJa\\/\\/F16uc3OtX8pNc0\\/LE4Hm7PSZrxK8uRFyG3ylBWWEU4VVaigziXBblkkdJavYjT5Zyu4XTT+aRr3uH0A04\\/VmitTq1F7QT1KmuPq\\/mpO9X8LUnq\\/rdovGxakZpvTNPwGRp+p4bX7GrjUxr+aQ1\\/EnhhUL8mWcMXI6+xTpOV0wpOJ3PaiNKF8OZYB283t0Nu5oEMbB68ByyDyL8O7q1t5GGyl\\/SRg+QwOUoGyLvkJPvuupAkpAk5gszH2cTpTk73c3qI0wFO3+P0E4U2c19vzuG0mNMJnE7ldCanHk67OV3K6VpOt3C6m9N+To9yepzTMwq91cBpFqd8\\/lv5\\/Lc2cjpbvQu2rWqr27Zo+Ic1\\/Asa\\/mwcD+O3rlU\\/b31aw7+m5h3b1Lxzqpp3HVfzbnMcD\\/q7hyKfQyrIzaQW3kPaIDN3Q\\/7dDW+BS+HeX0s2kq1kJ9lD9pN+cogcIcfIccLPloufNddJTs8q1E055V7hLuH0Zk6bOXVyGuR0IacrOeXe5Obe5N7H6dOcvsYp16eDx5GOoZzyXewYyyn39o5GtdU9zWorefzIDyUll2gXZZTbOeW+6eG+6eG+6eFxy8N907OL0wOcHuaUnxEPX5WHW9Vr5rSA0+s55T7qreOU6+F1c9qNq4mLt3OWcLpSve65BcinwBV5LbGSsWQivIk3kFZ4Y3PCexn2mctnn8sjydyJnPITOpfPPpfPPjfC6WJOV3G6kdMdMGfyoEf63tHwJzX8KTXfOVG9hs74vYXbx+9Wy\\/sjyEvEDPvL1+LnuvlXcLoOZTSx089jh\\/8tTvn++', '2026-06-01 05:07:46');
INSERT INTO `integration_logs` (`id`, `source_system`, `destination_system`, `payload_type`, `reference_id`, `sync_status`, `response_message`, `synced_at`) VALUES
(46, 'SDO-FAST', 'SDO-BAC', 'FINANCIAL_COMPLETED', 'PR-0023', 'SUCCESS', '{\"reference_number\":\"PR-0023\",\"event_type\":\"FINANCIAL_COMPLETED\",\"system_token\":\"fast_secure_token_456\",\"payload\":{\"fast_reference_number\":\"FAST-2026-000010\",\"fast_financial_status\":\"Approved\",\"dv_number\":\"DV-20260504\",\"remarks\":\"gege\",\"synced_at\":\"2026-06-02 07:54:02\",\"checklist_files\":{\"purchase_request\":{\"base64_file\":\"JVBERi0xLjcNCiW1tbW1DQoxIDAgb2JqDQo8PC9UeXBlL0NhdGFsb2cvUGFnZXMgMiAwIFIvTGFuZyhlbikgL1N0cnVjdFRyZWVSb290IDExNiAwIFIvTWFya0luZm88PC9NYXJrZWQgdHJ1ZT4+L01ldGFkYXRhIDQyMyAwIFIvVmlld2VyUHJlZmVyZW5jZXMgNDI0IDAgUj4+DQplbmRvYmoNCjIgMCBvYmoNCjw8L1R5cGUvUGFnZXMvQ291bnQgMS9LaWRzWyAzIDAgUl0gPj4NCmVuZG9iag0KMyAwIG9iag0KPDwvVHlwZS9QYWdlL1BhcmVudCAyIDAgUi9SZXNvdXJjZXM8PC9FeHRHU3RhdGU8PC9HUzUgNSAwIFIvR1M2IDYgMCBSPj4vRm9udDw8L0YxIDcgMCBSL0YyIDExIDAgUi9GMyAxNiAwIFIvRjQgMjEgMCBSL0Y1IDIzIDAgUi9GNiAyOCAwIFIvRjcgMzggMCBSL0Y4IDQzIDAgUi9GOSA0NSAwIFIvRjEwIDUwIDAgUi9GMTEgNTQgMCBSL0YxMiA1OSAwIFI+Pi9YT2JqZWN0PDwvSW1hZ2U5IDkgMCBSL0ltYWdlMzAgMzAgMCBSL0ltYWdlMzIgMzIgMCBSL0ltYWdlMzQgMzQgMCBSL0ltYWdlMzYgMzYgMCBSL0ltYWdlNTIgNTIgMCBSL0ltYWdlNjEgNjEgMCBSL0ltYWdlNjMgNjMgMCBSL0ltYWdlNjUgNjUgMCBSL0ltYWdlNjcgNjcgMCBSL0ltYWdlNjkgNjkgMCBSL0ltYWdlNzIgNzIgMCBSL0ltYWdlNzQgNzQgMCBSL0ltYWdlNzYgNzYgMCBSL0ltYWdlNzggNzggMCBSL0ltYWdlODAgODAgMCBSL0ltYWdlODIgODIgMCBSL0ltYWdlODQgODQgMCBSL0ltYWdlODYgODYgMCBSL0ltYWdlODggODggMCBSL0ltYWdlOTAgOTAgMCBSL0ltYWdlOTIgOTIgMCBSL0ltYWdlOTUgOTUgMCBSL0ltYWdlOTcgOTcgMCBSL0ltYWdlOTkgOTkgMCBSL0ltYWdlMTAxIDEwMSAwIFIvSW1hZ2UxMDMgMTAzIDAgUi9JbWFnZTEwNSAxMDUgMCBSL0ltYWdlMTA3IDEwNyAwIFIvSW1hZ2UxMDkgMTA5IDAgUi9JbWFnZTExMSAxMTEgMCBSL0ltYWdlMTEzIDExMyAwIFI+Pi9Qcm9jU2V0Wy9QREYvVGV4dC9JbWFnZUIvSW1hZ2VDL0ltYWdlSV0gPj4vQW5ub3RzWyA3MSAwIFIgOTQgMCBSXSAvTWVkaWFCb3hbIDAgMCA1OTUuNCA4NDEuOF0gL0NvbnRlbnRzIDQgMCBSL0dyb3VwPDwvVHlwZS9Hcm91cC9TL1RyYW5zcGFyZW5jeS9DUy9EZXZpY2VSR0I+Pi9UYWJzL1MvU3RydWN0UGFyZW50cyAwPj4NCmVuZG9iag0KNCAwIG9iag0KPDwvRmlsdGVyL0ZsYXRlRGVjb2RlL0xlbmd0aCA3MjQyPj4NCnN0cmVhbQ0KeJztPdmOJLlx7wPMP+SjJKBreB\\/AoIDumm5ZhgTI3gH8YPhhsdAuDFhray3Av++IIJlJZpKZLFbVHppdqaeqkkcEg2ScDOaH5x\\/+\\/p\\/ffv3N36eXP12mD7\\/\\/Sk\\/f\\/e\\/7d+zk+fTd+3eS6ZPTkxLqpCdh2UlM0lt89MNf3r\\/79ndQY\\/q\\/9++gnYntuPLT79+\\/O2j51ft30ysA\\/BuCsCes57GesSfhJunsyVK9f\\/vd9D1U\\/fDV\\/3z9\\/fTx44c\\/Xf7waWIf\\/vj1999Nv\\/nL909\\/\\/qffns\\/Ty6fenl4+A7JvfOJ8+vzt+3d8YvA\\/PinFT1xPBpCFj89\\/xZLv8B8Yyb\\/\\/Zvrtf0yf\\/\\/n9u1do\\/C\\/0f8J9+vDnGSVeYmHDwCWgYU4KBm4yHMIzBIzPi+rfAOQPf\\/jr19\\/9xU+f\\/ntKkD4UkxQpy\\/3JGFuflHcHdTbkB+hGFFQzOxMgdifgoK\\/9KRCAshiaArmZAuhK4eh75yDWb0zCigaqQQN2Yvifc1ZA79prgONgbO6YBNJMlurj6BmOnlVHX0NH34yOWKOjoS1MhbXi5O0ap4+MGX8u0Lpt8NrtQOuhgLn\\/hGgVcAJKSDeCk70\\/TlyJkwKchB2kk7s\\/ToKLk7CTBaYzRif\\/CJw48h3jRtcTb4mZ2wllDCwoPYQUfwBSwP2cm4yEFTWGVEse3ISUoOnjepRS8iEMWnsxSqXbJcaGRUdmoG1tPX1k8tNdWTRXINlb0LpIcLuU2i4UKVHN0ApWrxhC6gGCIyEl9EnLIaQeITmsOTGYPthZg5R6gOiISCnHTnoMqQfIjoSUHl1T4gGyIyElYU0N4fQA0cGhGnAEBWLND+H0AMkBTFp6NkijBwiNqERKMIfGaPQASyPhpEbX0gMYeVxLUgjURkaQegQjBy1SgRHL2EkNaZHiAYw8qrbCgsk0htRDbACilNB6lFKPMAJAtWWOxDEbWlPyEUZAUG0FZ6i7jSD1AE4OXJM7hwJmBKHb2fhWtcXKGrQzXaHS\\/VVbT16qOrQuEjxAcgjhkRVyJUfn5QGiIyElhlfvI2RH0I188CGNIPUA2RGRstBkEKkHyA40YWU3ldCXyuXJTlaqEweeBYqDQ2Pr5ITOgGE9cmBbI9AFZC1VKOqsB9eSQYcdhZHJyZcDEw70Kwsyseqs5M\\/nJwGfyp81fDh75vjTy\\/MT\\/lbm\\/CThU4gzd\\/DJ+Fnl5cJtuE0nnmqNp1VoVNQRXU\\/C3wpvN\\/xSDGZjhsphIo7I3JKqxz3V6ayYO0nZJrTxZ4X0vgRCAgG5yR7HaQB6PqkwD0+euDmT6g2rSC2YfGU0PfRYs\\/DYwZ+EP7uZid6hrKdCOYlu\\/+6pGIWjucPNMzLlZeQH9r0TCj6kNfCv5tMPGKMDjRl6d5awAlbAKf4yB+jmYp2KVVEMOzsV28BAyxoK9BUucwAYTaqUVvtHooFln8oD0coqGuxs69ojyMobQ6ARxmmB4gry1cLUcbVwZoBhn4PZbW0PY1PN8ORRR7uMTYuKb504FXGtwKVE4GJxVy277AX\\/WhzsCKENBzOOOFgVo24OFqB2cDDV0naPe9rnYA2KytdPgeGowIs+FZyLa3fmQGPOLow7fbbEpt7OT5wFfoXEBjWScflMHBALOY9lBiYIuCHnpsnErh6NleiNbY2Gy63kGgflTnoPFIzYfgzcmy\\/UYJdPUCbib3UWuDTfFGOvr2efURSa42qlavyCRAqEBCorX3S7GdGN44oLAjSo6hbTLzD1ML0wENpfNPfmGZcFjgsfj5J4I5Kkwyh4HZVrREXiiQwZXGSpa54Phd5mPH\\/LNKutE0cPzReOvmWrDeCEGboxbeS5mThJpaRuRlbfW0iDCoU7g2oAnournacxx9L2mBu9z8XV3vdLaXYjx+bIsTUYFlqTMi6gOqxeJbfKuJ2MdsgaBHQrHOGzy2ebpzp6OmvtLItENWAD8YoRP6uDSR1XLHCHF1UVVqNYCBJYLSwKDg1iUshSbxWmzsXRlrgnloYii01ama3iexM0j2uoDe2msW3YGqxY3oJWZ2vVMzctJ8tNi0NgjM4YtUVtekJL20yfv8Flom1UCvBPR4tEp+e3kWt9GAp5q64jdReLAXSGyYD5I2v8ORXqOpO1qTAOKi8nNDWM2bvQv0XY3ntTGA11tLJ2emlXgifcAk0jk2WZzl6AD7VSN6uauXZvgaPS2Yc4IOt3uGTLpXbYUVp+bqVMw0zDkJRfYupxCwIL0qgrSlI0QGUk1VFfzk8uMCJ6JotnjVV4PVqOItcVtEAlI7Tk813hedWAx14daosEEzRFtHMSCokkDPQzjjQCTRHLiTZQx83UIxUUnthqIfRuYoHWZx4fPaH2TsqpDyRGeYDDjrosVMACGwRDmiYsx7qrfqVA2EET5jRZWDpIPLUinhakkG+IdwVjbflkR6dTYUDYSJIxxWwGWwDpgup\\/Eqm0gospvQSqQtVy+lDvtstSyGiMajgtElgEZE\\/gXPK5CvUBrQRfbyIJ9e1cD+vcaVEraUnqVcgwPvkbIGAq6CqQmZiBiA2OkehGX18Nrdy0yqk6\\/n7hcUvoct7wIW4CXNNoACeac\\/ES4GBHsCsTfWlPFfvimbbvvTaCYugh2tDiio3QigOMbgTrTxZUT+HR01gsAdR9dbkus92RdgA9Qr4mI8VBRZ4paz+dVyuXNlGY73lSUnF8HJhnTv3cWMfiwOdIDwa9l3gdqsQmLggXvAphndxt6hj5YjeEumLqWtGSQYxwYyFG3C8H\\/q7EaC\\/EcQtGbJjPt6IBnRjpjZrA0bLSfiu2gQPgXodFRx+cPjguSWTGTIbHGioG9ixZ2A5Lg7ImWIjKhi5c9ji2Eip8F+n789yxGF6jZrNGidVuRttPf73nGx5h\\/rBrkP7OL3ZCpL8jlYl5F1zCsHnRpiN+gN+RjSQBAJxIiBD0SkEZn\\/2M1cNs8LNlewb69YvaErPeDOFuVo42lNtRcYFZKlywbRoyoYvFIDEHhkyAHEmBvVoXY7OLEROdOFsDa7cwkDi4f\\/aRjtAT0gUGu+ZX7L5mfhWeKAwLS2DXoiMsDDbySfcET3TL2X\\/Uz17sBEOu\\/EeMCR9h2QgJV9HsjacEmB3hFN1y8x12tOs8b9FYvpICnyJRmcOPIlTAnoSPUSx4JkXgLW4VEraNkDB0xlOohrWc8Ecj2oaFLfL3genoj9ZKJ041ppTKWu7tUNb2bi\\/9spNAstYqNN3bsbDp3a5jnRW2XPZxCqqh2WpZ6rNWVgZmpWQnl4UvZy9PffXveRAPO9tlMspuz9LeIz7bhVMjRltFqpulBMDzxjmgbMsx1tfbPnOp07aI1Ko5Ugv6MUXqgg1EoUYyjch7oMs4I86EySJ7UJ\\/ijuj2Ce4FsL7eisBuEQrgz82I8MqqCnBlR\\/zyJkpJg6dwK\\/58QUuOBoeWPdp37CGxWstX3WL4lNuR8GkXJTanbaAetw1KXB9ClVrD7q5HUCUohnyPG1fbJmZLjXeYbQMwIQWEMUuYcXMWJuplDWbcKqQhhcKdMTVBpwr17tOoY2l72M3+U4V6\\/\\/ulmxgqt5RtkMdQRS2GKrTE8+xZ\\/GaHBe57UXe7au1qj7ua8vf3TjVW4qdu3tSbgGbauFkcUy8soh366xjANvBHEaz6AK6wm\\/fdciOUxcRSAXU9NtkL+6m3\\/PAh\\/Jn4Kcl9ITWPYcCWSdxFtk0AkM6BVNG7m2mMCSTKVlhMKjStAGAsPDBBY\\/9ZALDQSHet0AihHQSMVF3phgX4UGMJADaCf9zS+frZZXGg5+y7GQ862w0CCqWW3Jn7BwHHUAuBwApqXYHAMZghGFgjxxcXDOwiYCMguCHgFQx333U9Nq0UFMQ2xv7DBAWHKBEDgzVS3LYQGsHBCqBffHBwbGOEAOGGHldsjP0IytjGCEFCAT24LylIODaFIVC4IVb\\/FJr9IMwQVjE0J0CrZINY7Xm+b8SK6XSn1tVY7R8K7cKqETTkXpVY\\/SKChl0jbgQONyO+Yh72Tz6OCYcQPORuOw8\\/UfBwbJGHAOJmGHezkjSnhNdqAFFogTn4R4ZQ6OLaAGIkRwogFl4eufgw6iHEZmEgdPB+7KMd4echxNzU2jPfIoC9IGJ+d59RcUVzhVnFeBmiaNxsuFvjq3n3ZH3r2HcdYY5eOLql0tGVhZhzDlb3Ct+iQ7N0qCQswxOnyRWwCrFHXBMi3nypJ8kROekpF7jdp82yeyfK75XGCN5I7hUeL\\/CgDSCBBg6IMCnA2rrs9kLJ4vWFWZ9Mc9fqEyw9F+9ENOJkYNFNCi8tMXmfYumTW6ScpasThdEYbUYVv7xmkR5Tn1SUt8l6VeVFixld3EIXQRdEYpKEwfNudBRGFc6U8BChUUHRIINm2ndrJs+ACxmIFvGH7QI\\/cV2otlvAtPTC454CW7MrtuZcaGcbeX9CJ8EVPuX6N4+\\/g1ADHR0fXoIuCAsBRRyJUGDQ0gdmLUKUZO4r9QFC0uXF6bEN2if2Fs034bNHwT6YqyEkEMQmR+4CEsJQ6zWv7qWbW9FNKLrJoU631j2i9UXgJaWZWq3wHDtyBbxSle25gG1Lt8z7co2+woD8ZMprBBgDSkDLRsK1NFF7SZo8aDmB3jjJDv5sLMBpDV9EmqXwKUwsENGemKcMfkucQ\\/ZWtsC+N93hMpIi7yahtABwoQ+RNVvWTPz9liorWqWhskvA5j63WkQ\\/kTlbUVkAszCuQeWe62dtdgXwU7igwaBnOPJZ6DBnu\\/+VfjL8WtSnJwFXRgaFm57Qk4k8FJ3Wk4a9IDJ+psXONbW2pcALg3hAX3gfhImANmTCa5nCbRHfzmjCfwqUiumJT8UTYen6T+1tOsklps8\\/gOCBabQcr4nObvQN80zcxOFyypZSZDFRj1\\/P8cPwxlNeugfvHxUpw\\/uICTtW+WXPUFyHuII8ax\\/FAo+8nHyV+ESpsMcVD79JS8cWtr6\\/+oYprh+mdfMNuzvDrF5RUmfd6B2YNOxpixcLGdTHpD7tyG\\/bMrCOe1pmt2QpoCtpxdMNgmlc2oIQ2+4FlNoqTU8ySJFz249V8diNllijxcEYFb2odV1y3Dr5cQXp3IZywmGzDD0BCqqtLHwfPOggcyh6CVJJBQM+0vESTnKgVLHhk0eaUn3SVHiwYUE7uYHOK\\/3NkP7SN44r9JLyUVDDDfAJh9q1wn8Vz7Dj0iZLAaMBYCiAuQDVDVppiwwxfE+GtOLa+6CT9wdms3CGhPuQjJXppH8kjOdKeCRM3VDbPv3XeQkExQKmfj19PRiaNYYc5uyOGPayrTXVWzFviiBjogQeLpYC7Uo861LbVuXOt0Glxtuny3GBta7oPQ4rE+NCApr0Rx0EX9L\\/KaSFOtksUkywSXg0FJT8WJ2QbuTFWg3mpAb3Id9B8hkXMJMBkTUW+PKCsG3Q0wCGuOFU2aGfINs3cm\\/ftELFe4DTrimPK5hgDWKKX6kOiMS0r1mSQQbLcMCBcQ1\\/Mn5uA7nXI+vFPZElFLeHewfQ0g+kIdLP4ucAomaFKOeSlvqd6Nclw1vx3mFmI1E3xEw82cNqQIpblbSeJKlb7MfN3gke\\/QtJA4gC\\/kmhLcE\\/LQ2H2dB6WE6QE7R\\/YAlJEdQQGGjkkMHe2WGdQX9Jo+aGVhep5uwtPKcueFB4MGLts5\\/bAMY461UGnS99Y26stsxQ9rv+WHwXi5wU3pzuySGLvn1l265Tx2J\\/9PYZQNM7PDuKTRkdGuVl27rBIJWEigZtVxtiCRbfB+PaFoNrBuyOu2ocJMf3jEyWObz+YPUGnI+bY3rphj1KnXiJqSpuOZZrfDCvX+fzfqgjGb65gC+dHLTZ4wyIiiEbv7naiQ4ZMv0Smsk9XOJJw3ibypJiE4YUk0QAiMWo8+tWDe+mae3uPbxap0rUHr+OEw2\\/Dn0XSgFHrjvUpUCBE3QIDFRgvALNZvTYy0KJ0ItTPYIIjvpDnz0e6FXBZy8M7Gg0qC2BgOHmfntjFxDC4U3k0tHhPMkERT6K06\\/CyeS2F8yg\\/SLhQyvYlqASq7zfnfdULafs8JZxJRWe\\/lG460HDNuV52\\/Xeaoc7j\\/tKblRbbi6JZ5+1VskcWxngJixmQefbgeTxM1rgSoXfmBNWFGQ6cd4eY9Y2yqC8Gp5O84Fbh3o6fLJLskZju5dY7sJpNvwSHvgVgEuqkApelhbbKGsv9ThbkU9hMEU26Nf12qH2xSk3Tqmc39Kw4peRtnj2C2mrnuPvS5rD9WSuZ19dAtuaV0MqnxvGaVRv0eMmY8EMizLVluUTO0z10ywuy0rM7Y4p2j5uPkpRxWm3N0k6L+P+T8EfU35rv+N7Y0O0oKRcQ7QWcndEypKydrg50sqcN4labYHnVb0LJfjMK\\/eaPRT4Us4ipS5Z2x0ZllYCdYU6ATr8A+H1l5HDGDyP7IJXGzRV771bK6Tl1mznK9zI7IANyzScZBwn+pkoS5JIMsHGWM9MmrqV6EoTZd1qolr9zf28rfqL9dZLaY1HN\\/zO\\/rGcFpSUsUIkjLLLGpc1DKLwn5dqQjSu9TUC6ELO98TM7GfM+VzhniLYU0b\\/Zgl0Sd921sKNC5Kp9OKmxSUf6R9P86FnPidb+p2qzdUv83oI160mI3m1UPCIHv2MQeq5XThWv9QKvb6FrP25ExkPGsfqa6wwVL2Gfs9JlHTjxIZsXZPYPpR\\/2yQqMC78ahIHNNXExaP+WTtCOYwnWDdArxqiFd\\/gLXDoKoYqHDOz2TjANPLIBrhIAi0VvKxJUrvCcxhVZAfy8SRRjGEOYhWOWlsndh75PfcM19WRdu2Z9nn9YXzipnH2enR8++z5jVsY9L319ESxNvPH7FBPwVYjI1yO4RRiNjFSOZ+hLvlo4pPCxtPZ54y7ZiIzrJSZLT+fZ19u1nvi7XM7HYHP9XThPxUpbQQquOMxpd8zScretjKiGAw6mrnNoZv5mPlKENG9JDio+2u0m5nuWnft7IJb1x1fHAKjoiN9yoWz3l90VBB9iOiowZFrQZBExvNadKSal7Ww8aXKyu0iZhPxygp6Dcvdg7oVxxClqG+G3bUq29kltzJnI0\\/8anQe5XVUeCBRDjHnyFXnamumHH9vWFPkXDNH0\\/VuXleMT6+U38TUA3eddeN91Rt6i2fRZskRe7UlUsE5tq1Wyog8ViZk\\/oAX7LfB+3OeT3ssUf6u\\/lGt6DLtzUx3rbtHuUaV0id7I2+2a968fXXJ7bx5i+dDWHMFjN6q6KVManLmy8oMmCk389nLTMI7auOc\\/xgUU1y2KKbWgsfeY2VU5YqVGwy69tPtjvGWWEHrfQebWgDRmwwB7k9i0nic2+PBdK3jv9mBPfpNQFnIH8qaLIE3K1opM97m8BxFQIWZ6BXN9E8GC38SKPySVc7gNFNzvCvHhe+hB\\/KIiQ4Y0D8LJJ\\/iifSlqJ7BMk1YfoGFV6NEdzueridv7OSKtCP8hcDowF9RPQPmWsA4Y\\/nIJCwZDRYoxytEEGGhSmjhaTgqhiVFkwWgY22AfD4S4YOXmpLcgEqS3vPbzoNrVaklwnEmdk9eUCYcx\\/uXeWcmHAee15MKh53ilVxdqXCcqYX87DjMXsuFAynsD1LhdruspMKhQrmbCWdhD+ErMq\\/JhIttejLhONPZqSzKCsC7rpy4IhUuNhhKhZOGn7oy4ThrBT8Oe9rNhMMrDWs3jH4hiXBHVGvkwVWpNpQGJ\\/H1En1ZcJy1wg0dfe1mwUmQg\\/7XJLiOJLhjErdy4Ko07tF0OHONs1JX58Bl3\\/NsOExhmJ5QdE3ChSQ\\/fGtWzurdXjYcZy13sDBIIuwND0NFSDdmcEkiPcyHKPNs7psO9yC8UXSb4XS4hyFl0yUPP4t0uJ5hXpcOR8MEtSxd3HWfdDiOhg692h5zmNhRMhznrTDFUUetXDg6e8zBllstqXo2HApMMU9fDOACYzVpGrPEuHCqupL504fpNj0OLCC8dKML1x5rlPOW772bmK5CS7BHVZkcUM+PWxLh3jY0Ux+XvDkkK2lDsDNo61SS4bppWiJsPJ2Z70P55lQ4YWSW7VXIPUyGM3MynOEes+FAOQbVqpQhak+G8JbX+gh8KyEuvDZBgKbmihzY4YQ4UnXf6K7CmkbZh2clLc7Tifo74TmYFsd5M1MX94UQdAPXkt9Qe99iKzMOzNZ0L9xPlBnXh34jN64P\\/Sty40RiNYTJYXackLLMjnNmdx8184Z3Ye8myKEMZsXi\\/JES5K5EN6bI3QndvRS5axHTD6RjSpKrqFIdqDbS5O5Fwz5Z3nIt38B8KFMOr+JSroP1\\/CSZcmMDi7ly\\/UP7meXKDTLjkC3XN+rDbDm8TPM4XU6wkzVd2XKcp\\/AAtrSTAPPU+bFsOeHp8ruYjiXYnkHRPLp41NNeqpxkMgm8X1PlylS5Q4o2EuXqJO3y\\/nC\\/8dtzIQCJRu4a48lvLyT5pYXDjL4nWfqondv47qNnqBUPEMl5jzE+o8Ian55A41cgi5aefRYVkEyFuw6X7oUV5HOATeFk4cp3eGtiyo4DAJKh9oFhDu4LAKIvO85OGIwksRuCkqCh76XHcbF3jPCwt\\/aRAT+BjpPu1f1CE+Q6yVfNkJOyQcAu5ULsHdK7bVblfDnql5UjBwzo\\/jTFLDnY502i\\/oNnyY1SzdCtn9dQ7Zo0uVGs4u3BRxvk558nN8q38FgxBlKqFLgmUc6Tpuz0NYlyXOydubyN53Gb3I2\\/psr9UlPlhoWxp\\/cnbdZAnxzeO49525pkdEDly8uWG57H8KaUDd365nHvHOBN84jGlVjxlkclzI0iagQeyK9h2jgpOgxIn7isA3pYytworsgWGrjelSiYNKcb1L8yaW546+B5vspY+7bOXgL7KEZh70Tt4FqE9hKYb9rL1mzn6NfMuZ9n5tyNOu5mqvuW3l7a9U1Lz7g7iJHO5LnbxEgN04eIkSqgX0L63LjbyKOk2Iy7b2nuZTffxqbxzaPXIyQf5pjUjAKTv+bQ\\/QJy6MbZNB0g2Ex139J7mPcUAww3s+nOPLrb2HQN04ew6Sqgny6XbtiRyynH7OFUU1w1qXZtPt0tgsZXlkjf5rqDG70haKQ8EDTVYKOUGQ6UIodH2SS\\/KqsuNunJquNS5QBxkJTp25dWFyv3pNVxqcuR4WlLyTGK251YF+v3ZNZxmaUnxlw5bO7sklpHH7vZdbFFV3adLPITQxYbpytmetPr8jZd+XWySFXUGIbAy2QYnmk8YQ7BSRteTmCI78aSsk0W39VtkL4cpYHmeFwc3yOFqXr5sVl6EjLDprJuBso2Qak8XZH5lPetKOkL31yoi9UZn9LwsKRokgH0bYA8Hxu+2w\\/f7MxML8C8SR9AUczfpEFZphc87VNyrthHRllucM9PGoHEFWIa68NMWeWupaEyTkJXG03cWTxRYCg7WZy4z88z0FM6shxKiiYLQM54G6LOIZpTPDvBORKI8g2KCQsPQ0IlqT95ixyibEPMk57p2LfSYIKbCda1Dv9kI2RxhPSlqJ5D26FozlLMpPHcmesBNdfthJPxEUOvdMV7yhmeIFe4EIrjYeEZy69zphpFwxzuztLMmAmdeeEoykH4OEeZ7LIYY3gaGCaW5C1yeO39pjOOovDsDPpPlJw0vTVG21K2hqcIL5TkLTJ4vL0+dcZQBIUQMf1CYbrWCd8XXJzvCQ9pdWJBXj+HVqzN\\/wczBb7aDQplbmRzdHJlYW0NCmVuZG9iag0KNSAwIG9iag0KPDwvVHlwZS9FeHRHU3RhdGUvQk0vTm9ybWFsL2NhIDE+Pg0KZW5kb2JqDQo2IDAgb2JqDQo8PC9UeXBlL0V4dEdTdGF0ZS9CTS9Ob3JtYWwvQ0EgMT4+DQplbmRvYmoNCjcgMCBvYmoNCjw8L1R5cGUvRm9udC9TdWJ0eXBlL1RydWVUeXBlL05hbWUvRjEvQmFzZUZvbnQvQkNERUVFK0FwdG9zL0VuY29kaW5nL1dpbkFuc2lFbmNvZGluZy9Gb250RGVzY3JpcHRvciA4IDAgUi9GaXJzdENoYXIgMzIvTGFzdENoYXIgMzIvV2lkdGhzIDQwMiAwIFI+Pg0KZW5kb2JqDQo4IDAgb2JqDQo8PC9UeXBlL0ZvbnREZXNjcmlwdG9yL0ZvbnROYW1lL0JDREVFRStBcHRvcy9GbGFncyAzMi9JdGFsaWNBbmdsZSAwL0FzY2VudCA5MzkvRGVzY2VudCAtMjgyL0NhcEhlaWdodCA5MzkvQXZnV2lkdGggNTYxL01heFdpZHRoIDE2ODIvRm9udFdlaWdodCA0MDAvWEhlaWdodCAyNTAvU3RlbVYgNTYvRm9udEJCb3hbIC01MDAgLTI4MiAxMTgyIDkzOV0gL0ZvbnRGaWxlMiA0MDAgMCBSPj4NCmVuZG9iag0KOSAwIG9iag0KPDwvVHlwZS9YT2JqZWN0L1N1YnR5cGUvSW1hZ2UvV2lkdGggODE0L0hlaWdodCAxMjEwL0NvbG9yU3BhY2UvRGV2aWNlUkdCL0JpdHNQZXJDb21wb25lbnQgOC9GaWx0ZXIvRENURGVjb2RlL0ludGVycG9sYXRlIHRydWUvU01hc2sgMTAgMCBSL0xlbmd0aCAyNzMxMT4+DQpzdHJlYW0NCv\\/Y\\/+AAEEpGSUYAAQEBAHgAeAAA\\/+EAWkV4aWYAAE1NACoAAAAIAAUDAQAFAAAAAQAAAEoDAwABAAAAAQAAAABREAABAAAAAQEAAABREQAEAAAAAQAAEnRREgAEAAAAAQAAEnQAAAAAAAGGoAAAsY\\/\\/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL\\/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL\\/wAARCAS6Ay4DASIAAhEBAxEB\\/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL\\/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6\\/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL\\/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6\\/9oADAMBAAIRAxEAPwD5\\/ooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiilAJIAGSegoAdDE88qxoMsxrp7S1S0gEa8nqx9TUGm2ItYtzgGVhz7D0q9WsY2M5O4UUUVQgooooELRRRigAoopRQMKWkpaQBS0UqqWOAM0AFKFLHAGamSDux\\/CpQAowBipcikiJYO7H8BUoAXoMUuaSpu2VYWjNJmkzSAdmkpKKYC5pM0maM0CFopM0maAFzRmkozTAXNJmkozQAuaTNJmigBc0ZpKKAFozSZpKBC0ZpM0UALmikooAWg0maDQAlGaSgUxjs0ZpKTNIQuaKSkJoAXNGaTNFAxe9FJS0AFFJSUAOpCaSkzQA6jNJSGgBc0UlFAC5ozTc0uaAFzSUmaM0ALRmm0tAC5pM0lLSGFGaTIooELmkpKKAFzRSZozQAtJSZozQMWjNNzRmgB2aM02jNIBc0hNJnNBOKtIlsM0lFFMk4+iiisjUKKKKACiiigAooooAKKKKACiiigAooooAKKKKACtrSbDAFzKOf4Af51U0yx+0y+Y4\\/dKefc+ldEBgYFXGPUiT6BRRRWhAUUUUAFLSUUAL3ooFFAwpaKAM0ALSgFjgDJqVICeW4HpU6qFGAKlyGkRJB\\/eP4VMAFGAMUmaM1Ddyhc0ZpuaKBi5opKTNAhaKTNJmgB2aTNJmjNMBc0lJmjNAC5pM0ZpKAFzRmkozQIKKTNGaYC5ozSZpM0AOpKTNGaAFzRmm5ozQIdmjNNzRQA7NGabRmgB1ITSZoJoASlzSUUDFzRSUUCFzSZozSUDFpabRmgBTRSUZoEFFJRQMXNFJRQAtBNJmkJpALmkzRRQAUUUUDCiikoAWjNJmikAtJRSGmAtJRmikAUZopKAFopKKACkzRmigYUUUlAC5o60lFWlYhsD7UUUUxBRmiikM4+iiisjQKKKKACiiigAooooAKKKKACiiigAooooAKntLZ7ucRrwOrH0FRIjSOEUZYnAFdNY2i2kAXgueWPqaqKuKTsTRRJDEsaDCqMCn0UVqZBRRR3oGGKKKKAClpKWgQUU5ULnAFWEhVeTyaTdhpXIkiZ+egqwiKnQc+tLRUNtlpWFzSUZpM0gFozTc0ZoAXNJmkopgLmkzRSZoAWjNJmkzRYQ7NJSZopgLSZpM0ZoAXNGabmjNAhc0ZpM0ZpgLmkpKM0ALRTc0ZoAdmkpM0ZoAXNFNzRmgB1FNzRmgB2aM03NGaAHUU3NLmgAzSZpM0ZoAdRSZozQAUUZpDQAtLTM04GgBaQ0ZoxQAmaWkooAKU0lFIAoopKBi9KSlpKACiiikAlLSUUAFFFFAwooooAKSg0UAFFHeg80AFJRRSAKSlpKLAFFHU0uatKxLYlFFHWmIKKKKQxKWkpaAOPooorI0CiiigAooooAKKKKACiiigAooooAKKK0NLsvtMvmOP3SH8z6U0rg3Yu6TY+Wn2iQfOw+UegrUoorVKxi3cKKKKYBRRR3oGHeiinJGzn5fzoATHNTRwE8twPSpEiVPc+tPzUORSQABRgDApaTNJmpGLmkzSZpM0wHZpKSjNAC0lJmjNMQuaTNIaM0ALmjNJmkzQIXNGaTNJmmAuaKTNJmgB1JmkzSZoAdmkzSZpM0AOzRmm0UxC5opM0ZoAWkzRmkoAWikozQAtFJRQAtFJRQAtGaSigBaKSloADSUZFJmgBaWm5opALmiikoAXpRmkzSUxj80E02igQuaKSikMXNLmm0UALRRmkoAWjNJmikAuaTNFFABRSUUDFoopKAFNJRSUAOpDRRSASlpKKACiiiiwXCk60Uoq7WJYnSijvRQAUUdKSkMWkopaADtRRSUAchRRRWRoFFFFABRRRQAUUUUAFFFFABRRSgEkADJPQUAS21u91Osad+p9BXUQxJBEsaDCqMVW06zFpB8w\\/etyx9ParlaxVjOTuFFFFUSFFFFABRSopc4AqzHEE5PJpN2GlcjjgJ5bgelWAABgDApM0ZqG7l2sLmm5ozSUALmkzSZpM0CHZpM0maTNMB2aTNJmjNAhc0maTNJmmA6kpM0maAHZpM0maTNADs0maTNFMQuaTNJRQAtJmiigAozSUZoAWikzRmgBaM0lJTAWiijNIAopKKAFzRmkopgLmjNJRQAtFJRQAtGaSloASiik70gFopKWgBaKSigBaSiigAozRRQAtFJzRmgBaKSigBaKSjNAC0lFFIAooooGFFFFABRRRQAUlLRSASilpKBi0UlITmiwri0najpRVCCg0tJQAUUUdqACiiikMKKKKYCGiiikByFFFFZGgUUUUAFFFFABRRRQAUUUUAFa+kWW4\\/aZF4H3Af51RsbRru4Cc7Byx9BXTqqooVRhQMAVcV1Jk+gtFFFaGYUUUdaACpI4i\\/J4FPjhxy\\/wCVTVLl2KSEChBgDFGaM0lSULSZpCaTNMQtJmkzSZoAdmkzSZpM0xC5ozTc0ZoAXNGabmjNAC5optFMQuaM0lGaACikzRQAuaKSkoAWikzRTAXNJRRQAUZopKAFzRRSUALRSUtAB0oopKAFopKKAFoozSUCFopKKBi0UlFAC0UZpKAFopKKADvRRRQAUUlFAC0UUUgClzSUUALRSUUALRmiigAzRSUUALRmkzRQAtFJS0AFFFFIAoopKACiiigYUUUUWFcSiiiqAWkoo7UgF7UgpT0pBQAGig0dqACigUUAAoPWig0DEooopAchRRRWRoFFFFABRRRQAUUUUAFKqs7BVBLE4AFJWzo9lgfaZByfuA\\/zppXYm7F+ytVtLcJ\\/GeWPqas0UVsZBRRT0jLn0HrQA1VLHAFWUjCc9T60qqFGAKWobuWlYM0maKTNIBc0hNJmkzTAXNIaTNJmmIWkzSZpM0AKTSZopKYhc0lFJmgBaKTNFAC5pM0lFAC5pKKKYBRRRmgAopKKACig0UCCiiigAooo70AGaKKKACiiigYUUUuKBCZoo70UDCiiigAooooAKKKKACjvRRQAUUUUAFGaKKACkxS0UAJ0opaKBCUUYooGLRSZooAWikopALRRSUALRRmigAooooAKKKKACijNFAAaTNGaBRYAooooEFFFJTGLRRRQAvakoopAFLRSUAFFL2pKAA0UUUAJR0paSkM5CiiisjQKKKKACiiigAoopVUuwVQSScACgCzYWhu7gKQdi8sfaumUBVCqMAcAVXsbVbS3CfxHlj71ZrWKsjKTuFFFTxxY5br6U27CSuNji3ct0qfoMDpRSVLdy1oFJmjNJQAZpM0E0maBATSZozSZpgGaTNGaSmAZoopM0CFpM0lFAC5pKKTNMBaKKSgBaTNFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFHaigAozRRQIKKaXQdWUfjTDcwL1mjH1cUDJaKrtfWq9biP8GzUZ1WyH\\/Lb\\/x0\\/wCFK6CzLlFUTq9mP+WhP0U006zaer\\/980cyCzL9LWd\\/bNr6P+VH9tW3pJ+VHMgszRorN\\/tq2\\/uyflR\\/bVv\\/AHZPyo5kFmaVFZv9tW392T8qP7atvST8qOZDszSorO\\/tm1\\/2\\/wDvmlGs2vq4\\/wCA0cyFZmhRmqI1ezP\\/AC0I\\/wCAmlGq2f8Az2\\/8dP8AhRdBZl2iqy6haN0nT8eKeLu2PS4i\\/wC+xTugsTCg1GJYj0kQ\\/RhTwynowP40ALRRRQAUUUZoASjHFLRQAlFLRQAlGaMUd6ACiloxQIKKMUUAFFFFAwxRRRQAneilpDQAUtJRmkAUUtJQIKWkxRTGFGaKMUALSUUUAFL1pMUUgCiijigAooxijNAHH0UUViahRRRQAUUUUAFbGj2ef9JceyD+tZ9nbG7uFjHC9WPoK6hFCIFUYUDAFXBdSZPoLQAScDrSqpY4FWEQIPf1q27EJXEjjCcnrUmaSkNSUKTSUlJmgAzSE0UhNMQUhNGaQmgAzSUUmaYhaTNGaSgAoopKYC0lFFAC0lFFAgooooGFFFFABmijioJbu3h+\\/MgPpnJ\\/KgCeis59ZtV+7vf6CqsmuSH\\/AFcKr\\/vHNLmQ7M26K5t9Wu36SBR\\/sqKrPPNJ9+V2+rE1POh8rOpeeKP78qL9WAqu+qWaf8tdx\\/2QTXNUUudj5TebW7cfdSRvwqB9db+CAD3LZrIopczDlRotrVyegjX6Com1W8b\\/AJa4+iiqdFK7HZE7XlyxybiT8GIqIyOxyXYn3NNopDDOaKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKUEjoTSUUAPWWRTlZGH0NPF5cr0uJf8Avo1DRQBbGqXg\\/wCWxP1AqQaxdjuh+q1Qop3YrI0xrc46xxn86lXXefmt\\/wAn\\/wDrVj0U+Zhyo3l1u3PWOQflUi6vaHq7D6rXO0Uc7Fyo6hNQtH6ToP8AeOP51Ms0b8rIjfRhXI0U+cOU7HINArkFlkX7rsPoatW9xfO+2KWRj7nIH50+cXKdLRVSB50jHnSB2+mKl80+gq7kk1FReeO4o85PU0AS0UwSxn+IU4EHoQaAFpKWigQlFLSUAFFFKBQAlFGKAKAFopOlGaQC0lFFAwooopgFFFFABRRxRSA4+iiisTUKKKKACjrRWnpFp5s3nuPkTpnuaaVxN2NLTrT7LbfMP3jct\\/hV1ELHApUQueOnrVhVCjArS9tCNwVQgwKWjNJSGGaSikzQAE0lJSE0xCk03NBNJTAKKKSgQUlFFABSUUUwCiiigAooprusalnYKo7k4oEOoqjNq1rH0YyH0UVQl1uZsiONU9zyalySKUWbtV5b62hJDzLkdhya5yS6nm5klZvbPFQ1LmPkN2XW4l4ijZz6ngVSk1m6f7m1PoM\\/zrPopOTK5USyXE0v35Xb6moqKKkYUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRUkMEk7YRc+p7CtW2sY4cM3zP79qaVxN2KdrpzzYaTKp+prWiiSFAqKAKXNGa0SsQ3cdmkLU3NJmgBc0hNNLU0mgBSaaWxSE0wmkBJ5rjo5H40ou5l\\/jz9RUBNMJouOxbGoSDqqmpF1Jc\\/NGR9Dms4mpo4sfM3X0ouwsjTFzERnJH1FPE0Z\\/5aL+JxWaTSE07isa4G4ZUgj2NLsb+6axc46U4Tyr92Rh+NHMFjX2n+6fypCvtWauoXKdJcj3ANSrq0w+8iH8KOYLF3FJVddZH8cP4hqnXV7VuodfqtHMFh1FSLqFm3\\/LZR9eKnjlhl\\/1bo\\/8AukGlzBylSkq\\/gelOxRzBymdg9gaUI5\\/hP5VoUtHMPlPPaKKKzLCiiigCSGFp5ljQcsa6y1tljiWNBhVGM1Q0Sw2xfaHGC\\/T2FbYAAwBxVrQl6gAFGB0pM0ZpM0xBSZoJpKBBmkJpDSZpgLmm0UUxBSZozSUALSUUlABRRRTAKKqz6hbQA7pNzD+FeTWbNrMrgiFAg9TyaTkkOzNtnVFLOwVR3JxVGbVraLhCZG\\/2en51hSTSTHMjsx9zUZNQ59hqJoT6xcSZEe2Me3Jqi8rynMjsx9zmmUVLbZSSCiiikMKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKsQWkk5zjavqaAIACxwBk1et9OZsNNwP7verkFrHAPlGW\\/vGp81aj3JchERY1CoABTs03NJmqJHZpM03NITQA4mm5ppNITSAcTTSaaWppNAxSaaTSE0wmkApNNJzSZqeKLb8zdaBixRbfmbr6VITSE0hNMQE0maSkJoACaQmgmmk0gAmmk0tNJoGFJmikNAATSUUlICRbiZPuyuPo1Srqd4n3bhvxwf51UooA00127XAYRt9VqZfELj71up+jVi0hoGVqKKKkYVf0vT2vZwWGIV5Y+vtUVnZtcvk5EY7+tdZbQLbwKigD1xTSEyUAKAAMAUZozSE1ZIUhNITSE0xBSGikJoACaSiimIKSikoAKKKhmuYYBmRwPbvQBLTXkSJdzsFHqTWRcawzArAm0f3m5NZskskrbpHLH1JqXNDUTZn1iNCRCpc+p4FZk99cXGd8hCn+EcCq1IT2qHJstJB1ooopDCm0p5ooASiiloAKSiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKVVZzhQSfagBKfHC8rYRc1chsOjSn\\/gIq6qqgwoAHtVKImyvBYpH80mGb07VbzikzSZqtiR2aM03NJmgQ7NITTSabmgB5amk00mkJoGLmkJppNIWpAKWppamk00mgY4mm5ycCk5JwOTVmKIJyfvUAEUW35m61ITQTSZpiAmm5oJpCaADNNJoJzSZxSAKaTSk03NAwpKDSUABNJRSGkAUlFJQAUlFFACGkoopDK9WbS0a4fJ4jHU+tFpatcP6IOprajjA2xRjA6AUhlmxgUYIXCJwB71fzTEQRxhB0FLmrSIbFJpuaDSZqhATSGkJpKYBmiikoEGaSioJ7uG3Hztz\\/dHWgCeoJ7uG3Hzv8390cmsu41OWQkR\\/u19utUCcnnrUuXYpRL1xqssnEQ8tfXvWezFiSSST1JpCaQmobKsBNJRQTikAhPpRRRQAUhpaSgYUlFFABRS0lABRRRQAUUUUAFFFFABRRTgjnopP4UANoqQW8x6ROf+AmnizuT0hf8AKiwEFFWRp90f+WJ\\/MU8aZdn\\/AJZj\\/voU7MV0U6Ku\\/wBlXX9xf++hS\\/2Vdei\\/99UWYXRRoq9\\/ZN16J\\/31R\\/ZN16J\\/31RZhdFGir39k3X91f8Avqk\\/sq7\\/ALg\\/76FFmF0UqKuHTLsf8s\\/\\/AB4U06ddj\\/lifzFFmF0VaKsGyuR\\/ywf8qYbacdYZP++TSsO5FRTzFIOsbD8KaVI6gigBKKKKACiiigAooooAKKKKACiiigAooooAKKKAMnigApQCTgDJqxFZu+C3yj9auxwxxD5V59aaQmypDYs2DIdo9O9Xo40iGEUClzRmqSsS3cdmjNNzSZpgOzSZpuaTNADs0E0zNJuoAdmkJppNNLUgH7qaWppNNJoGOLUhNNJppNIBxNIMscDrSKC7YAyatxxiMep7mhAEcQQZPLetPJozTTVCFJpM0maQmgBaaaKTNIYZxTSaDSUABNJRSUAFJRRSASkNBpKACkpTSUDCkopKQBSUGkoA21VYkCIMKKvWUWAZW78CqcSGaUIPx+laoAVQAMAU4oGxSaQmjNNJqyBc03NBNJTAKKKY8ixqWdgAO5oEONRTXEcC5kYD27ms+41Mn5YBgf3jWc7s7FmJJPc1LkNRLtxqcj5EXyL696z2Yk5JyaQmmk1Ddy7ATSE0hNJQAUlFFIApOpoJyaKBhSE0tNoAKWkqVLeaT7sbH8KAIqWriaZM33iq\\/rVlNLiH33ZvpxT5WK6MmlCk9AT9K3Y7K2TkRgn35qwqqo+VQPoKrlFzHPpaXEn3Ym\\/EYqwulXLdQq\\/U1s5pM0+VC5mZi6O38UwH0FTLpMA+87n9KvZozT5UK7Ky6bar\\/AT9TUq2lsvSFPxGakzRmiyC4BI16Io+gpwx2puaM0xD80Zpm6lzQA7NGabmjNAD80ZpmaXNADs0U3NGaAH0ZpuaM0AOzRmm5pc0wHZozTc0UAO4pCFPUD8qKM0ANaGFvvRIfqoqNrG1brAn4Cps0Z4pAVW0y0P\\/ACzx9CajbR7Y9N4+hq\\/mjNFkF2ZjaJGfuzMPqM1E2iP\\/AAzKfqK2KWlyofMzBbR7kdCh\\/Gom0y7X\\/lln6EGujzRRyIOZnLNazr96GQf8BNRlWXqpH1FdbSFQeoB+opcg+Y5KgDJwK6lrWB\\/vQxn\\/AIDTBYWy8rEAfalyMfMYMdo78t8o\\/WrkcMcX3Rz6mtI2UZ6FhTGsf7sn5inyi5ipmjNWDZSjoVNRtazr\\/Bn6UWC5HmkzQ0ci9UYfhTTnvQA7NJmm5pN1IB2aTNNzSZoGO3UhNNzTSaAHE0hNNzSE0gHE00mkzTSaAHE0qKztgCkjRpDx09auIoRcLQARxiNcDr3NOzSZpM1QhaTNGaTNABmkoppNIYpNNJoJpKACkopKACkoopAFJQabQAUUUlABSUUlAwpDSmkpAJSUUUAdVZReXFvP3m\\/QVZJopKtIlsDTSaCaSqEFITiop7qO3HzHLdlHWsm4vZJ+Cdq\\/3RSbsCRduNRSPKxfO3r2rLmnkmbdIxNRk00mobuUlYCaaTQTTSaQwJpM0GkoASiikpAFIT2p6ozcKpJqZLN25chaLAVqcqM5wqk\\/QVfjtYl5I3H3qcYHAAA9qrlFcoLYyvjdhR71YTT4h95i36VYzS7qdkK7BIYox8qKPwqUGot1LupiJc0ZqLNLupgS5ozUe6jdQBJmjNR7qN1AEmaM0zdSbqAJM0ZqPdRuoAkzS5qPdRuoAkzRmo91LuoAfmlzUe6gNQBJupc1HmjNAEmaM0zNGaBEmaM0zNGaYEmaM1Hml3UAPzS5qPNLmgB+aXNR5pc0APzRmmZpc0APzRmmZpc0AOzS5pmaUGgB2aXOaQUuaYC0UmaM0CHUUmaM0ALRSZooAWikooAXNIVU9VB\\/ClooAia2hfrGv4cVG1hAegYfQ1ZzRSsMpNpqH7shH1FQtpsn8Min68Vp0UcqC7MdrC4HRQfoaha2nXrE34DNb2aKXKPmOcZWXqpH1FMJrpSAeozTGghb70Sn\\/gNLlHzHOZqSKIyHJ4WtptPtW6xAfQ4pfsMWMDIo5WHMZ6gKMAcUZq6bEdnP4imGxk7MposwuVaTNTmzmHYH6GmG2mHWM\\/hRYCLNJTmjcdVYfhTDSGGaSikoAKSiigApKKSkAUlBpDQAUlFJQAUlLSUDEoopKQBSUGkoAKSiigDsyabmgmq9xdxwDBOW\\/uitCCZmCqWYgAdSazrnUTysPH+0aqT3Mk7ZY8dgOgquTUuQ0hWYsSSST6mmE0E00mkUBNNJoJppNIAJpDRmgKzHgZoASk61YW37sfwFTKqoOBinYLlZLd268D3qwltGvJ+Y+9PzRmiyEOGAMDilzTM0ZpiH5pc1FmlzQBJmjNR5o3UASZpd1RbqN1AEu6jdUe6jdTAl3Uu6os0bqAJd1G6ot1G6gCXdRuqLdRuoAl3Ubqi3UbqAJt1JuqPdRuoAl3Ubqi3Uu6gCUGjdUW6l3UAS7qN1RbqXdQIk3Uu6ot1LuoAl3Ubqi3Uu6gCXdRuqLdS5pgS5ozUe6l3UASZpc1Fupd1AEmaXNMGTThQA7NKBSAijNMQ8UuaZmlzQA7NLmmZpc0AOzRTc0ZoAfRmm5ozQA7NLmmZozTAdmlzTc0UAOzS5plKDQA7NFNzRmgB2aKbmjNADqKTNFADqKSlyaADNGaM0UCFzRSUUALRSUUALSFVPUCiigBhhiPWNfypptID\\/AMsx+FS0UWGVjYQH+Ej6GmnTYj0ZhVuilZBdlE6YnaRvypp0v0l\\/StCijlQXZmHSn7SL+VMOlzdmQ\\/jWtzRzRyoLsxzplx\\/sn8aadOuf7o\\/OtqilyofMzDOnXP8AcH5ik\\/s+5\\/55\\/qK3aOaOVBzGCdPuv+eR\\/MUn9n3X\\/PI\\/nW\\/RRyIOZnP\\/ANnXX\\/PL9aP7Nuv+ef6iugoo5EHMzAGmXR\\/gH5il\\/sq6P8K\\/99VvUUciDmZTudQ6pD\\/31\\/hWczEkknJpCaaTUNl2Ammk0E0wmgBSaaTSE0BS3SkAhNAVm6CpVjA68mn5p2AYsKj73NSjgcU3NGaBDs0ZpuaTNAD80ZpmaM0AOzRmmZo3UAPzRmmZpM0ASZozUeaM0ASZozUe6jNAEmaN1R5o3UAS7qN1R5o3UASbqN3vUe6jdQBJuo3Go91G6gCTdRuqPdRu96AJd1Aao91G6mBJml3VFupd1AEu6jdUW6jNAEu6l3VFuozQBNuo3VFuo3UATbqN1RbqN1AEwal3VGMntSgepoEPDU4ZPSmjFODUwHAepp4wKi3UuaBEu6lzUW6l3UwJc0ZqLdS7qAJc0ZqPdRuoAl3UZqPdS7qAJM0bqjzS5oAfml3VHmjNMRLuozUe6jNAEmaXNRZpc0ASZozTN1GaAJM0ZqPNLmgB+aM0zNKOaAHjmndKaKKYDqM0lFADs0ZpuaM0AOopuaM0AOopM0ZoAXiikzRmgBaKTNGaBC0UlFAxaKSjNAC0tNooELRSUZoAWikzRmgYtGaTNGaAFopM0ZoELRmm5ozQMwSaaTQTTc1iaATScnpShfWnUAIEA6807NNopgOzRmm5ozQIdmkzTc0ZoAdmkzTc0ZoGOzSZpuaM0AOzRmmZozSAfmkzTc0maAH5ozTM0ZoAfmjNR5ozQBJmjNMzSZoAkzRmmZo3UAP3UZpmaN1AD80u6o80ZoAkzRmo80ZoAkzRmo80uaAH5pc1HmjNMCTNGaZmjJoAk3UbqaKXigQ7NKM00Gl3UAPApwOKj3UZpgTbqXdUO6l3UAS7qXdUW6jdQBNuo3VFupd1AiXdS7qhDUu6gCXdTt1Q7qXdTAl3Uu6od1LuoAl3Uu6od1LuoAl3Uu6oQ1LuoAm3Ubqi3UbqBEu6l3VFuo3UwJt1G6ot1G6gCbdRuqLdS7qAJc0ZzUQJJ4qVRjr1oAeo9aeDTM0ZpiH5ozTM0uaAH5ozTM0ZoAfmjNNzRmgB+aM0zNLmgB2aM03NGaAHZopuaM0AOzRmm5ozQA7NFNzRmgB1GabmjNADs0ZpuR60Z96AHZozTc0ZoAdmjNNzRmgB2aM03IpM0APzRmmZozQA\\/NJmm5ozQBg4z1penSkzSZrI0HE0mabRTAdmjNNozQAuaM03NGaQC5ozTc0ZoAXNJmkzSZoAdmkzSZpKAHZpM0mRRn2pALmjNJmkyaAHZozTaPxoAXNFJRQAuaKSigBaKSigB2aSkooAWlzTefWigB2aM02jmgB2aM03NLmgBc0uabmjNAD80uajzS5pgPzS5pmaM0ASZozTM0ZoAkzS5qPNGaAJM0u6o80uaYD80u6o80ZoES7qN1R5ozQBLmjNR5ozQBLupd1RbqN1AEu6l3VFuo3UAS7qXdUW6jdTAlDUu6ot1G6gCbdQGqLdRuoAm3c0bqizRuoAm3Ubqi3UbqAJt1OXLdKYiE8ngVOCAMCmIeoC0uaZmjNMRJmjNMzRmgCTNGajzS5oAfmlzUeaXNAD80ZpmaM0APzS5pmaM0APzRmmZoz70APzRmmZozQA\\/NGaZmjNAD80ZpmaM0APzRmmZozQA\\/NGaZmkzQBJmkzTN1G6gCTNGaj3UbqAH5ozTM0ZoAfmjNMzRmgB+6l3VHmlzQBiZpM1GjY4PSn5rMsXNGabRQAuaM0lJQAtGaSkoAXNGaTNJmkA6kzSUUDDNFJmigBaKSkoAXNGaSigBcmjJpKKACj8aKKACj8aKKAFo\\/GkooAKKKKAFoyaSigBcmjNJRQAuaM0lFAC5pabS0CFozSUZoGOzRmm5paAHUZptGTQIdmlzTM+1GaAH5ozTc0UwH5ozTKKAJM0ZpmaM0APzS5qPNLmgB+6lzUe6gGgCTdRupmaM0ASZozUeaXNAEmaM1HmjNAEuaN1R5ozTAl3UbqjzT0Qv7D1oAcCWOBU6RheTyaRFCjin5piH5pQajzS5piH5pd1R5pc0AP3UZpmaXNAD80ZpmaM0ASZozTM0ZoEPzS5qPNG6gCTNGaZupN1AEm6jdTM0ZoAk3Um6mZpN1AEm6jdUe6jdQBJuo3VHuo3UASZpN1R7qM0AS7qTdUe6jNAEm6jdUe6jNAEmaM1HuozQBJuozUeaXNAEmaM1HmjdQMkzS5qMGlzTEYVOVscGkorI0H0U0HtTqAEopCaKBC59KTmikzQMWjNNzRQAvJpKWigBKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAWikooAMUUtFACUuaKSgBc0ZpMUUAOyKKbRQA6im0tAC0ZpOaTmgB2aXPtTc0ZoAdmjNNzRmgQ7NGabkUtAx2aM02igQ7NGabzRmgB+aByeKEQueBx61YSMJ05PrTQCRxd2\\/KpwQKZmlpiH5ozTM0uaYD80ZpmaXNAD80ZpmaXNAh2aXNMzRmgB+aM038aM0APzRmmZozQA\\/NGaZmjNAD80ZpmaTNAEmaM1HmjNAEmaM1HmjNAD80ZqPdRuoAk3Um6mbqN1Ax+6jdTN1JuoESbqN1R5pd1Ax+6jdUeaN1AEmaXNRhqM0ASZpc1GDS5oEPzS5pmaWmA8GlpmaXNAGPmijFGKzLClBptGaAHUUgNLQAlJRS0AJS0lFABRRRQAUUUUAFFFFABRRRQAUUUUAFFGDS4oASil2ml20ANop2z3o2e9ADaKf5fvR5fvQAyipPL96PK96AI6Kk8o+oo8k+ooAjoqTyW9qPJf0H50AR0U\\/yn\\/u0nlv\\/dNADaKdsb+6fypMEdjQAlFFFABRRRQAUUUUAFLmkooAXNFJRQAtFJRQAtJRRQAtJRRQAtJRRQAUuaSnKhfp0oATk1MkPdvyp6Rqn19afTSEKOBgUuabS0xC5pabS5pgLRSZozQA6ikzRmkAtFJmimAuaWm0ZoAdmjNNzRmgB2aM03NGaAHZpM03NGaAH5pM03NGaAHZozTM0ZoAdmjNNzSZpAPzRmmZozQA7NGaZmjNAD80ZpmaM0APzRmmZozQA\\/NLmmZpaAHZpc0zNLmgB4NKDTKWmA\\/rSg00GlFADs0tNzjrUL3ODhefegClRRRUFBSEUtFADaUGlpuKAHGm0UtACUUUUAFFFFABRRRQAUUUUAFFFFAC0CiigB1JSA06gBAcU6m0UwHUtNzS0gFpabS0wFpc03NLQIdmlzTKXNAD80oNMzS5oAePalzTM0uaAH5pc0wGlzQA7APUCk8tD\\/CPyoBpc0AJ5ER\\/hpDbRn1H40\\/NKDQBCbRezGk+xns4\\/KrGaXNFgKhs5OxU002sw\\/gz9DV4GnBqLBczTDKOqN+VMKsOoI\\/CtYGlzRYLmPRWuY426op\\/Cmm2gP8AyzH4UWC5lUVpmxhPTcPoaYdOU\\/dkI+oosFzPoq6dNfs6n60xrCcdAG+hosO5VoAJOBVgWU\\/eMgVIITGPuEfUUWFchSLu35VKPSiimIWiiigBaKSloAWikooAWiiimAUZoooAM0ZoopAGaM0lFAC5opM0ZoAWim5ozQA6kzSZpKAHZpM0maM0ALmim0UDFo4pM0ZoAXNGabmjNAC0U3NLmgBcmlzTaUUALmikooELS0UUAOopBS0AOopKUUwHCkZ1QZJqKScLwvJqszFjknNK47EskzPx0HpUeaTNJUgFFFFABRRRQMKKKKAG0tLTaAFpKWkoAKKKKACiiigAooooAKKKKAFopKWgAoBoooAdmimU4GgAozS5ooAM0tNxRTAdRSZpc0ALmlzTaKAHUuabmjNAh2aXNNzRmgB+aXNMzS5oAfmlB96ZmjNAEmaXNR5pc0ASZpQajBpc0ASZpc1HmlzQBJmnBqizS5pgTBqXNRZpQ1AiUGnA1EDmpFX1oAeMnpUigDrTARTgaYEgNOBqIGl3UCJCqN95QfwphtoG\\/wCWa\\/hSg0oNAEZsYD\\/CR9DTDp0Z+67D681Z3UuaAKR0xu0g\\/EVGdOmHQqfxrTBpQ1FguZBs7hf+WZP0qMwyr1jYfhW4DS5osFzAII6gikroDg9QDTDDC33okP4UWC5hUVtGytj\\/AMswPoaYdOtz0DD6Giw7mRRmtNtLjP3ZGH1qM6U3aUfiKLBcz6KunS5uzIaYdOuR\\/CD9DSAq5pKnNncL1ib8OaYYZV6xOP8AgNAEdFKVYdVI\\/Cm0ALmkopKAFozSUlAxc0maKKACiikpALRSUtABRRRQAUtJQKYDhRSCloAWgUUtACilpOlRPOBwvJ9aAJWcIMk1BJMW4HAqMksck5pKVwsGaM0lFIYuTRSU4I56Kx\\/CgBKWlIpMUAFFJS0CCiiigYUhpaKAGilpD1paAEooooAKKKKACiiigAooooAKKKKAFopKXNABRRRQAlLmiigBQaWmUUAPpKTNLmgApc0maKAFzS03FFMB1FJk0ZoAdmjNJmigB2aXNMpc0hDs0uaZmlzTAfmlzUeaXNAEmaXNR5pc0ASZpc1GDT1BPWgBwNSKpPWmqAOlPzTAkGB0pwNRZpc0xEuaUNUYNLmgCQNTgaizS5oAlzTg1Qg04NQIlBpQaizS7qAJgaXdUIanbqAJQaXNRBqXdQBLml3VFuo3UwJd1LuqLdS7qAJM0uaizS7vegCTNLmos+9LmgCTNGajzS7qAH5HfFIVQ9UU\\/hTd1G6gANvA3WJP++aYbO2PWFfwqTNG6gCA6fan\\/ln+ppp0y2PZh+NWd1LmkBTOlW5\\/icfjSHSYe0j\\/AKVdzRuosguUDo6dpm\\/Km\\/2OO03\\/AI7WjmjNFkFzNOjntMP++aT+x2\\/57L+VamaM0WQXMr+yJP8Anqv5Uf2RJ\\/z1StXNGadkFzK\\/siT\\/AJ6JS\\/2RJ\\/z0StTNGaLBcy\\/7Jk\\/56r+VKNJf\\/nqv5VqZozRYLmb\\/AGSe8w\\/KlGk+s3\\/jtaG6jNFkBnNo6t1nbHoBQNEg7ySH8q0M0bqVh3KI0a1HUyH8aeNJtB\\/Cx+rVbzRmiwrlcabZj\\/lkD9SakWztV6QR\\/lUmaN1FgARxr92NR9Fp3HpTN1G6mBy9GKSlrMoZRT8ZppGKBhRSUtAgooooGIaKWm0ABopaSgAopaSgAooooAKKKKACiiigAooooAKKKKAFzRSUUALRSUUALRRmjNACYpaKKADJpc0lFAC5FFJRQA6kpM0uaAFzRmkyKOtADs0U2imA7NLmm5pwBNABmnhSaFUD60\\/NAhVAFPzTKWmA\\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\\/yFOwXK1FXBaxjqSakEMa\\/wD8aLCuZ4BPQU9YZG6Ia0AAOgApadguU1tJD1wKkWzX+JifpVilzRZCuRrbRL\\/Dn61KqqvRQPpSZozTAdRTc0ZpiHUUmaM0gM080mKdijFSWMxRTqKAEpMClxRikAmKQilozQAmKKWjAoASilxSbaACik6UUALRijNFACYopaKAEopaMUAJRRRQAUUUUAFFFFABRRRQAUUUUAFLSUUAFLSUUAFOptKKAFooooAKSlooEIDing5plAoGPpKXNFAhKXNFJQAtFJS0wFzRSUUALRgUUA0AGBRtopc0AJt96NtLmjNACYNIQ1OooAj2n0o2n0qWigCLB9KKlzRQBFRU1FAEJOKb1qxgelGB6CiwXIKKnwvoKML6Ciw7leirG1f7opdq+gosFytRVnaPQUYHoKLCuVqKtYHoKXiiwXKmKXB9DVqiiwXKu1v7p\\/Kl8tz\\/CatZozRYLlbyX\\/ALtL5D+1Wc0UWC5ALdvUUotvVv0qeinYLkQtl7k04W8foT+NPzS5osIaIYx\\/CKcFUdFH5UUUAOopKKYC5ozSZozQA6jNJmjNAC5pc02jNADgaM03NGaAHZxS5pmaM0APzRmmZozQA+jIplGaAKuKMVIRTcVIxmKTFPxRigBmKTFPxSYoAbRinYpMUDG4oxTsUlADaM06jAoAbRgUuKTFIAxSEUtLmgBvNJT6TFACZooxRQAUUZooAMUlLRQAlFFFABRRRQAUUUUAFFFFABRRRQAtJ0NLSGgB1FIKWgAooooAKSlooEGaXNJSYoGOzRSc0ZoAWijNFABS5pKWgQUUlFAC5opKWmAtFJRQAuaKSigB1FJmjNAC5ozSUUAOozSZooAXNLmm5ozQA6im0uaAFozSZpc0ALmjNJRQA7NFNozTAdS5puaM0AOopM0UCFpc02jNADs0uabmigB2aKbRmgB9FNzRmgB2aM03NLmgBc0uabmjNADs0ZpuaM0AOzRmm5ozQA7NGabmjNADs0ZpuaM0AOzRmm5ozQA7NGabmjNAC4pMU8ikxSGR4oIp+KTFADMUmKkxTcUAMxSYp5FJigBmKMU\\/FJigBlGKdikoAbRTsUmKBiYpMU6kpANop1JimAZoxSYopAGKTFOoxQA2igiigApKWg0AJRRRQAUUUUAFFFFABRRRQAUtJS0AJTqSk6UAOooooAKKKKACiiigAoopKBC0UmaKAFzS7qSigYuRRSYooELRSUUALRSZNGTQAtFJmloAWkozRxQAuaM0lFAC0UlLQAUuabRmmA7NFJmigB2aM02loAXNLmm0UAOzRmm5pc0AOzRTc0ZoAdRmm5pc0AOzRmm5ozQA7NLmm5ooAdmim5ozTAdRmm5ozQA7NLmm5ozQA7NGabmjNADs0ZpuaM0AOzRmm5ozQA7NGabmjNADs0ZpuaM0AOzRmm5pM0APzSZpuaM0AW8UmKqC7k7hT+FO+1nug\\/OlcLFjFJiofta91NL9pjPUEUASYpMU3z4z\\/F+lL5kZ\\/iFABikIpdyn+IfnS5HqKAGYpMU\\/FJ9aAGYpMU8ikxQAzFJT8UmKAG0mKdikoASkp1JQMSkIpaKAG0U6mnigBaaRS0GkAlFHeigBKKKKACiiigAooooAKKKKACiiigBaKSnCgBuadSEUlMB1FJmlzSAKKKKACiiigAooooAKKKKACiiigAopM0uRQAUUZozQAUUmaM0ALRSZpc0AGKKKKACjJoooAXPtRkGkooAWiko5oELRSZNLn2oAM0uaTIooAWikooAdRTaM0wHUZpuaXNAC5ozSZozQA7NGabRQA7NGabS5oAdmjNNozQA7NGabRmgB2aM03NGaAHZozTc0UAOzRmm0ZoAdmjNNzRmgB2aTNNzRmgB2aM03NGaAHZozTc0ZoAjooxRikMKKKKACiiigAooooAXJ9TS72\\/vH86bRQA7zH\\/vGjzH9abRQA\\/zH9aPMb1plFAD\\/ADGpPMb2ptFADt59qN5ptFAC7jRuNJRQAu40ZNJRQAZooooAKKKKACiiigAooooAKKKKACiijFABS0UlABSiiigBaQiloFADcUU4ikoATNLmijFABmjNGKMUAGaSlooASloooAKKKM0AFFJmlzQAUUlFABRRS0AJRS0UAFFGKSgBaM0lGaAHZpM0lFADs0U2igB1FJ+NGTQAtFJmjNAC0ZNJkUtABk0ZoooEGaXIpKKBi0UlFAhaKSjn1oAWik59aMmgBaKTJoyaAFopMmjNAC5opMmjJoAWikyaOaAFopMmigBaKTn1ooAWikooAXijIpKKADNGfaijIoGNop1JigBM0tGKMUAFFJRQAUUtFACUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAtFFAFACUoFLRQAUUYNLgUAJSilpKBC00inClxQMjopWXHI6U2gBaKSigBaKSigAzRRRQAUUUUAFFLRQAUUmaKAFzRSUUALSZoooAXNJRRQAuaSiigAooooAKKKM0AFFGaKACiiigAooxRQAUvNJRQAuTRmkooAXNGaSigBc0ZpKKAFzRkUlFAC5FGRSUUALkUZFJRQA7IpMikooAXNGaSigBc0ZpKKAFzRmkooAXJoyaSigBaT8aKKACiiigB+BSYp+KTFAhuDRTsUYoAbRilxRQMbijFLj3o6UAJSU6kxQISilxRigYlFLijFACUUuKMUAJRRiloASil5o5oASiilxQAlFLg0YoASilxRigBKKXFGKACilooATFGKXFGKACil6UtAhMUUuKXFADaXFLilxTAbS4pcUuKAG0tLijFACYqNlxz2qYCjAIxQBXopzoVPtTcUhhRS4ooASlxRRQAUUlFABmiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigCxikxUmKTFMQzFJipNtJigCPFGKk20mKAI8UYp+KQigBmKMCnUYoAbikxTsUYpANxRzTsUYoAbg0YNOxRigBuKKdijFADeaKdikxQAn4UU7FGKAG0U7FGKAG80Yp2KMUANxS4pcUYoATAop2KMUAJijFOxRimAmKMU7FGKAExRinYpcUANxS4pcUuKAG0U6jHtTAbS4p2KAKAExS4FBIHvScmkAEbhioGUqcGrIFK0YdcEUAU80lOdCjYNNpDCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAL+KTbTzgUhJqyRm2kIpxBNJtpANzTTUm2kxQBHijFSbaTbQMZikxUmKMUAR4oxT8UYpAMxRin4oxQAzFGKfijFADMUmKkxRtoAjxRipMUmKAGYoxT8UYoAZijFPxRigBmKMU\\/FGKAG4oxT8UYoAZilxTsUuKYDMUuKdijFADcUuKdijFAhMUYp2KMUANx7UU7FGKAG0tKSF60wuT04oGKSBSEk0gFOApAIBTwKAKcBQAAU8CgCrNvavO3AwvdjQBXaDzhtAyaznQo2DXRXYS0txDH99+p74rLkgEq+\\/rQwRn0UrKUYgjBFJSGFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQBp7aTbU22k21ZBFtpNtTbaTbQMixSYqbFJtoAixSbal2+1BX2oAi20bal28Um2gCLbRtqXbSbaAI9tG2pNtG2gCPbRtqTbRtoAjxRtqTbRtoAi20bal20m2kBHtoxUm2jFAEWKMVJijFAEeKXbT8UYoAZijFPxS4oAjxS4p+KMUAMxRin4oxTAbijFOxRikA3FGKGdV6nn0qFpienFAEpIXqajMhPQYqPrTgKVxh1PNKBSgUoFAABTgKAKeBTAAKcqknHWpYbd5m2oPqewrVtrNIOfvP6nt9KEhNla208th5uB\\/drS+WNOyqo\\/KlFUdSm2oIVPLct9KrZC3KE8hnnaQ9+g9BSKKFWpFWsyyC4tRMmV4cdPestlKsQRgjqK3yQilmOAOSaxbqYTzFwMDoKAIaKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigDc20YqXbSba1MyLFG2pdtJtpARbaTbUu2jbQBFto21LtpNtAyLbRtqXbRtoAhxRt9ql20baAIttG2pdtG2gCLbRtqTbS7aAIttG2pNtG2gCPbSbam20m2gCLFJtqXbRtoAi20bfapNtGKAI9tJtqTFGKQEeKMc1JijFAEeKXHtT8VG8sadWGfQUAGPag4HXiq73ZP3Fx7moGdnPzEmlcdi086LwOagaZ29h7VHS0rjClAoApwFAABTgKAKcBQAAU4CgCp4LaSdsIvHcnoKYEYWr9tp7PhpflX07mrdvZxwYJ+Z\\/U1ZqlHuS32ESNY1CoAAPSngUYpcUxDXYRoXboBmsWRzLKzt1Jq7qE2SIVPu1U1Woky0gVakAAGTQorPvrzdmGM8fxEd6kZHe3fnN5aH92P1qnRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAHSYoxUmKTFbGRHijFSYpMUAMxSYqTFG2kBHtoxT8UYoGR7aMVJikxQAzbSbakxRigCPbRtqTFGKAI9tG2pMUYoAj20bakxRigCPbSbalxRigCHbRipcUmKAIttGKV5I0+86j6mq738C\\/dy30FIZNtoxVB9Qc\\/cUL+tV3mkk++5PtS5h2NJ54o+rj6Dmqz3v9xfxNU6KXMOxI88knBY49BTKOgoqQClopaYBS4opwFAABTgKAKcBQAAU9ULEAAknsKtW1hLNhm+RPU961YLaOBcIvPcnrVJXE2UrbTejTf98itJVVFCqAAOwpaKtKxNwpwFAFLQAYpssgiiZz2p4FZ99Lvk8tfur1+tS2NIqEl3LNyScmnqKAtVb27EI8tD+8PX2rMsZfXmwGGM\\/N\\/ER2rMo60UAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAdVijFOxRitjIbikxTsUYoAbijFOoxQAzFGKfikoAbijbTuB3ppkjXq6j6mgAxSYpjXVuvWZP++qjbULVf8AlqD9BRdDsT4oxVRtUth0LH6Com1iMfciY\\/U4pXQWZoYoxWU2ryH7sSj6nNRNqly3RlX6ClzIfKza20jMq\\/eYD6mufe6nf70r\\/nURYnqSfrS5w5Tee9tk6yg\\/Tmq76rCPuIzH8qyKKXMx8qL76pK33UVf1qtJdzyfekP0HFQ0UrsdgJJ680tFFIYCloooAKUDvSDk0tAgpaSnCgApQKAKcBTAAKcBUkFvLO2I1J9T2FatvpkceGl+dvTtTSuJuxnwWktwfkXj+8elatvYRQfMfnf1Iq0AAMAYFLVpWJbuFLRRTEFOAoxS4oGAFKKAKdSAhuJfJhLfxHgVlgZOTzVi5l82Xj7q8CqV1dLbJgcyHoKzbuWkNvLsW67V5kPT2rHJLEknJPehmZ2LMck9TSUhhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQBN9ruB\\/y2k\\/76pftlz\\/AM93\\/OoKKdwsT\\/bbn\\/nu\\/wCdH225\\/wCe7\\/nUFFF2Fif7Zc\\/89n\\/Ok+1XH\\/PZ\\/wDvqoaKVwsSG4mPWV\\/++jSGWQ9ZG\\/OmUUAKWY9WP50lFFABRRRQAUUUUAFFFFABRRRQAUtFJQAUtJRQAvU0tAooAKOpo6mlAxQAdKKKUCgQYpwFTQWs1wf3aEj1PStS30mNMNM28+g6VSTYm7GXDbyznEaE+p7CtS30pEw0x3H+6OlaCqqKFVQAOwp1WopEuQiqqqFUAAdhS0uKWmIKKKXFABilAoApaQwpwoApRQAVBdy+XHtB+ZqsEhQSeAKxL+9WMlzyx+6tS2UkR3Nytsnq56CsV3aRy7nJNEkjSuXc5JptQUFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUtABQBQOaWgAoop0cTythEZj6AUCEAwKK0oNHlfDTMEHoOTWnBY28GNqAsP4m5NUotickYtvp1xPghdi\\/3m4rVt9Kgh5f943v0H4Veoq1FIltgAAAAMAdBS0UUxBS4opaACiinAUAJilxS0tIYYpQKAKWkAUtLiq97dpZ2zStyR0HqaBlXVb9LaPZnLntXMSyvM5dzkmlmmeeVpJGyzGo6zZYUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFLQAUlFKAScAZNABSdatw6ddT9Iyo9W4rQg0RF5mkLey8U1FsTaMYDJwBk1bg025n52bF9W4rehtYLf8A1caqfXv+dTVah3J5jNg0aFOZWMh9OgrQjjSJdsaBR6AU6iqSSJuFFFLTAKKKXFIAoxS0UAFGKUClxQAYpaMUuKQwpQKXFKBSATFOFGKa8gQepoGDuEHvXK6tem5uNitmNOPqa09VvTBBhT+9fgew9a5ylLTQa7hRRRUFBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFPSKST7kbN\\/ujNW49Ju3wSgQH+8admwuUaK2YtDHWaYn2Qf1q5FplpFz5QY\\/7XNNQZPMjnUieQ4RGY+wzV2LSLqTBYLGP9o810AUKMKAB7UtUoIXMzLh0WFRmV2c+3Aq\\/DbQQD91Gq++OaloqkkiW2wooopiCiiigYUUtFABRRS0gDFLRS4oASlApcUuKQwxS0UoFAABS0UuKQwxSgUVHJLt4XrQAskgXgdaqySBEaRzgAZJNO71i6zd5b7Mh4HLn+lP4ULdmddXDXVw0jdD0HoKhoorI0CiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAorTXRJyfmkjA9smp10Jf45yfouKrlYuZGLRW+ui2w6s7fjU6aZZxnIhBP+0SafIxcyOZqRIJpPuRO30UmuqWGJB8saL9FAp9PkFznNppV2\\/\\/ACzC\\/wC8RViPQ5T\\/AKyZV+gzW5RT5ELmZmpotuv3mdvxxVqOxtYvuwpn1Iyf1qxRTshXYgAAwABS0UUxBRRRQMKKKO1ABRRS0CEoopaBiUtFLikAlLiiloAMUUUoFAABS4pQKXFIYmKUClApcUAGKWgClpDClo6DNQSS7uF6UJXBuwskvZfzqGikJCgknAHJNWlYjcr310LS3L\\/xnhR71zDMWYsxySckmrN\\/dm7uSwJ2Lwo9qq1lJ3ZpFWCiiipKCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigDsaKBRW5kFFFFABRRRigAooooAKKKKACiiigAopcUUAJS0YooAKKKWgBKWjFLSATFLRS4oASlApcUuKBiYpcUuKUCkAmKUClxS4pAJS4pcUuKBiUEhRk9KR3CDnr6VXdy5yaErg3YWSQufQelMooqyArJ1i82r9mjPJ+\\/j09Kv3lytrbtI3Xoo9TXLu7SOzucsxyTUTfQqK6jaKKKzNAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAOxooorcyCiiloASiiigApcUUUAFFFFABRS4oxQAlFOxRSASgClooAMUUYpcUAJS4pcUuKAExSgUUuKQwoApRS0gDFGKUClxQMTFLS4paQxKZJKE4HJpkk3ZPzqGqS7kt9hSSTknmkooqiQpCQASTgDqaWsnWL3av2aM8n75HYelJuw0rmfqF4bu4JB\\/drwo\\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\\/8ArUm7Alcj1LUDcv5cZxED\\/wB9Gs+iism7mqVgooopAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAdxRRmkrUzCkoopgFJRRQIKSikoAXNJRSUwFpKKKAEooooAO9GaM0UAFFFZOp6jtzBC3PRmHb2pN2BK4zVNRzm3hbjo7D+QrHpcUVk3c0SsJRRRSGFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAHb0lFFbGQUlFFABSUUUAFJRSUwFpKKKQBRRSUxC0lFFABRRWZqOo+WDDCfn6M393\\/69Juw0rialqPl5ghb5+jMO3t9axM0uMnJNGBWTdzRKwlFLj3pp4pDA0lFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQB21JRRWxkFJRRQAUlFFACUUGimAlFFFAC0lBNFAgoorK1DUtuYYD838TDt9KTdhpXF1HUfLzDCfn\\/AIm9P\\/r1i0uKKybuWlYSiloPApDEJxTKUnJpKBhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAdrSUUlbGQtJRSUwCjNFFABRRSUCCiiigYlLQSAMnpWNf6iZMxQnCdGb1pN2BK47UNS6wwN7M4\\/pWVRilxWbdzRKwlGKWlpAN6VGTk0rtk4HSm0hhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAHZ0UUlbGQUUUUwEooooAKOlJS0AFIzKilmICjqTTZZUhjLyNhRWFeXr3TY+7GOi\\/40m7DSuPvtQa4JjjysX6tVHFLilxWb1L2ExS0uKMUgEqJ2zwKdI+PlFRUAFFFFIYUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQB2VFFJW5kLRSUUCFpCaKKACobm5jtY9znk9FHU1Fd3yWwKrhpPT0+tYkkjzSF3YljUuVikrj7i5kuZNznjso6CocUuKXFQWJilpcUUCEpkj7eB1pZH2DHeq\\/U0mNBRRRSGFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAHY0UZorcyF7UlFMkkWNCzsFUdzQA+s281ILmOA5bu\\/p9KrXeoPPlI8rH+pqmBUOXYpIQ5JJJyT3pcUuKXFSMTFLilxS4oAbimyOEHvTpHEY569hVRiWOTSY0BJJyaSiikMKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigDsaKKz7vUlTKQ4Zv73YVu3YySLFzdx2y\\/Mct2UdTWNcXMly+XPHZR0FRszOxZiST1JpAKzbuWlYMUuKXFLikAgFLilApcUAJimyOI1yevYUruI1yfyqm7l2yaGxiMxdsmkooqRhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAGtdX8lxlV+SP07n61UxSgU4CqJEApcUoFOAoAaBTsUoFOxTAbimyOI1yfypZJFjXLfgKoO7SNk0mwSB3Ltk02iipKCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigC+BTgKUCnAVZI0ClApwFOxQA0CmSyLEuT17ClllWFcnr2FZ0kjSNuY0m7AkEkjSNuam0UVJQUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAGqBTgKcBSgVoQNxUc0ywr6segouLhYRjq\\/YVmu7OxZjkmk2NIHdpGLMcmm0UVBQUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQBuAVXuboQjavL\\/AMqsPwjfSsRjliTVt2ISBmLMWY5J70lFFQWFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAH\\/\\/ZDQplbmRzdHJlYW0NCmVuZG9iag0KMTAgMCBvYmoNCjw8L1R5cGUvWE9iamVjdC9TdWJ0eXBlL0ltYWdlL1dpZHRoIDgxNC9IZWlnaHQgMTIxMC9Db2xvclNwYWNlL0RldmljZUdyYXkvTWF0dGVbIDAgMCAwXSAvQml0c1BlckNvbXBvbmVudCA4L0ludGVycG9sYXRlIGZhbHNlL0ZpbHRlci9GbGF0ZURlY29kZS9MZW5ndGggNTgyODA+Pg0Kc3RyZWFtDQp4nOydB1vizBqGP4oIKoggFkSQKohgl96R8\\/\\/\\/0UnPlHcmk0ZYzXN59tuz7pqAc\\/u8bSb\\/\\/XeoijR2\\/wvlWjuOfnBtf7aQNgytKa0YWmJaMDRnaIZqytCE1pilkfQBa4hrsiLehaCZYOt6FfRC+xWyQwsst7Qsl65omXlMCwMUkhRJUxKWw6XldBP0Ovsd2hstYqgwaZkL0cKCZSpoK0xWAFpoWA6WltQk6GX2SyRICocWNjIELRAwHtoKixUxUNiYQLYyHAyna\\/rVB00FQ7HXMGnxRjZokXBhEuM0EvOSFlfZChMUipaBpCEKi\\/mSg8aCocegF9mvkR1aOP7ikBbnKQvpKwK0SEw4oIW2FZkWEJZ10FiAitwHvcZ+j9zTIpy3WHqL40qYBArFimtbAUgZaBrO1gAr68OkJb8Meo39HvlGi1j12AktQrZC0wKDAsMCWApKCwTLodISnwe9xH6RxGmxEYQJ0SJSDhNK7ilaXJWLh3KqQqb0g4FBiqT5hn7xyosMmgxAx4OgV9hvEosWGhhXKYt\\/tLipgjFoYVmKFoYxYTlAWqJPQS+wXyUOLbu90rIAQzF\\/abEOwgaUOLAcIC13P0EvsF+lPdAikrKAScucwsUytxejxV4BDNMIgMV4mUGzQamwDnp9\\/S4dCi1OEnwQFc9ooW1FhmXBguUQaUmH5TBv5ZKWjUgktldaLBN80XQFhoX+KYG8yKDpIJR4D3p1\\/TYJw+KuJuYoEHOdsgC0MGxFxFcGg\\/GSlbKoryloPAh1w4EXjyVGig+0+OMtToIwkXRFhWXFh+XAaKkFvbZ+n\\/yhRaAkZkULOHQcHC3f399jeuhYe6WrQ6SlEG5p8VzitPxsYWIEfAXMXPi0QKVjPi1iPUknQdj3t\\/wxAZwFg2W5DBoQVIlp0EvrF8oGLQyDcRKFOaDFbgefMUJpaSwgLRIsU2tYlkETgig2Dnpl\\/UbZo8Wht1iyIpC30KSQkZgftqLTgsBCvkzkBQWNiKl42MP3Q35l+XxgKFREsnzr4jFCjGdBmBqGQRP62CtbHhYtxW3QC+tXKpiamKWvOKJl6oQWBBkYFllIA3\\/LhGWxCJoRQyfhPnxf5J4WkW3GVjUx2lvm\\/JqYN+Uw1FbAOExlBRkNg8MwnfmgIdF1Ogt6Wf1SBeMtlv4Cl8S4R1ZY0cINxABadF\\/hj4YhznIwtMRfg15Vv1UMWgBegqfFTk3MkhSCFjoQ02mBRsMOm5Zq2MP3UWLAiNOylj\\/c0WJVE7MZiVkm+EDeosEyXtBhGFUM0+4\\/aExU3YYZvp8StRc7tNDi4+KaFm5jUqgcBtIyXlrAskJuP2hOFKXDKX1fJRyM2diXb8kKQYx1TcwNLQAvAqMuMizg2S6UtSwOh5bEZ9DL6ZfLHS2OGi5QMEYiw89cOLgAGT4NjAgtgwmrzQKwIt1e0KTIeg6TFn\\/lkhYYGGta7EVilL\\/YooUghUpbwOrxYAbk90BT8oBoiZSDXky\\/XsHQItSh5CUvjrN8mhSQFgiWNctZlBsNmpX\\/\\/suHg8d+aw+0gMgERQvhLaCv4G0WTp9lidxn0Kz8Fw+PB\\/ddvtBi22BEYjGUFRe0jCxoIdss8GzYkrSWwLP82FvQS+kPiEWLHWLc00ICA6Qt3CwfB0a8ggynLNAOfAAW9F4XQe8GizSCXkl\\/QV7Q4kfyQo+KWWT57KlKe+byPRhDsNA5CxqGzZdB01IIZyn3oH3RYtWhtNmi9K7fQtGCbiq2rh0vNFgCpuU43C25D\\/lHi4i\\/8HnhBGMu8pbhkNtvgZ5khNfDKGORYQmWlvh30OvobyhYWuyOV4r0J61p4fVbZmvoVTFg0e5wsQqalqewLbkX2aSFRUzgtEyZeQufFjwKGyJtFgYsUIYfNC23YdKyL\\/F4AZGx4y9k8sLPXuzlLuxwTDhtIXIWsM0CtSVpWIKkJbUIeg39KflHCw0M1188osWiKsaiBWuzELTgd0vDEiAt8XC35F7lDS1iE8lW0ZjleKVYqs\\/DhZG3jJZMViBjMWrH6icCgyXWCZOWvcpm8sLEBSTGHS3yoykEaREOxmBasMqxaOnYgCU4Wq7CDWD7lU1aeMg4MRer5MVJNCYYihmwoE8qRl+HKCyB0XISwrJn8WixaTCM5GXjBhhOMMbuU1q1KHFa8GKYwFZJDJYAaUkNg148f04e0iKUvVjRYmdrmNM2JUaLXAz7gV4CCxY8ww+OlkgrTFr2LU9pESkl26MFAMbLVB9\\/kBFtLfgdIrezWAVOSyF8tuTe5YQWt9GY5aF8jpMX26n+eIE9QoC8YVYctsA+EQgs2XAD2P5lSQuDFz9psQrH3NJi4jJZ4c\\/bEIzDFqvAaTkaBL1y\\/qq8jcYEaLGsJZOVZAe5i0iebwELOu+CJS2r4GkJk5ag5HE0tt\\/OPpm8sPyFBmY03RCvQiwMI2EJgpbrsHgclA6QFqtasoPGC5m8jOcb8lWI0bIkbjiAncanobMEJoHcxXZj37qzb3\\/DPiMcE2zqE7SMFxsYFShpQa+\\/JO91\\/7SER+0FKKe02DcYjBWal+XSzjGWAoVkNi6T5XbLooWVtIDOEsTzW2ph8Tg47Y0W3Figs5O47mI\\/1WcmL1M5v2fQghkfuxwWHC25MA4LVA55IX88Y2uPBsY6FFtahWILnJa5ncIYgstszWRFGBbTdvYMSybc0xK0hHgBHlbBbrw4Ot7C4Y4X7JQxa1q0\\/F6wLYlcfbEi2vsBPL8l3g96rYRySouttIXihabFRptSqKkP0bKgYGHsLCathXaWAGgph3FY4LLEREHFzgYxGBmBaIxTRuYgI1ZGVvN76sYZzkIUjxmw7JeWQthpCV52fcWNwdgpJDulZcqgZQzk96zTwQlrWSzpG9w7Lclwb\\/EByCkshxOOkbjAwMzWACy8s\\/RNa1lSScv+nw0WDZ\\/Tcghy7C0OkXGV7hOlZOtwTAdmIm9mYVsL+zykheQsZIQWxLPBSmGnJVhJIIgm+V4ajD1\\/EaSFTvanKC2TxRa8YzhtweMwxFmCo+UsdJaAJQiKG38RcRcaGV66T\\/KCl5NnsMFMluDdkrfEgoVlLfujJRnuLQ5ednhhA+PaX2ym+zxgQFz0lIVFC3Eb2EWX2JxOULSEY\\/oHIFu0eFkc83aTGC8gk0IyvSXJtBb8NvALEkNt+Cf3BUs2hOUA5A0tDpGxCMccpvskMJPpYsO4S04cpu3EV2EhrcW8hz3BklkHvVBC\\/c82LTxifDYYChdRg5ktLWHh0MKHZU+0xF+DXiehZHlIi+0tyBQuNsMxABmAltlqy7hFBiwYEhAs+6elHBaPD0be4eJLPRmjxapERrnLfM28P2trUWGhGpP7jsQuwjjscOTAX7w0GDvZi7JUhY9RkmCRUhbsZq1pQYFYEXcUjLcchWP6ByRvaXHgL1a8LCmJ0rLc4rCIFsTUCyyJ+6GtZR+0hKfpH5ic8MJBxodwTFmp7JwfxmW+2u6sYWEeTUnCAljLPmgJn1t8eDowg4FosdPil2FZrGVnYcRh5JVpWihYiIrYnmhJhLAcnhzai7P+vlD\\/BTCYFacFQ9IyV1IWa1rAaAuBhR4RW+6TlqOPoFdGKFqe02JBjEj+YvuhLygvSspC3J81LcYXXgI3QcPiPy2VsHh8iPKBFtf24uIhFkrKsmNmLdxntch1N+ge6LTFd1rOwuLxocqpv3iawJC42DypT2VFj8KYsDAeP2F85TUVoumfwy\\/lMyxHo6DXRCiWHNPCw8W+v0AT\\/dyqMsnLYrmB7kzAWvSvu6bGkmla9uEtvaCXRCiOnPPi6Ty\\/AC3sEbL5YrX9sYDFIslfbdi07DMSuw7jsAOXc1ocNfiFW\\/wWTUsEl\\/UWRJhxSfxL686CXD+4SCw2DnoxhLKQG3txFpGxcbE3Q6ZqufkBb4h1RZqWNUZrgHlLuF3yX5AvtDg4fox2GOtwDInCduxAjHeK2AoM0fYfiUXKQa+DUGJyzgsPGR4ugkP9VgnMasu8E\\/haLFgAbyFCPr9pSc+DXgWhxOQPLfb9BeKFF46tf9h3IkbLirwocB2EF\\/9giYRN\\/H9JrohxmPE7JUYvZfHuQYSW1Yq+IttcfN3fUgv6+x\\/KjvyixaHBYMDQ4Zi0lNfbH949CNECXI30FizN9w2WcPL4H5RPwLjGBeBls+XfAXgN4otBFwskEgsfmPcvyg0tjoHZbhjU8BL+zY\\/F9Tm06F8KvBqduCDu4hctzXAH2D8qv3jhZzD2pvrXtLHYp4VxpQBouQwnj\\/9ZucLFqb8wiIHtBTQWZiDGoIVxpQC8JTUN+lseyoV2B0UMebASld6L02KGW2vqcZlQgrQXWqLtMA77t+WOFqcFZSFaGFEYhxbaXFRYII6Aucqlz7RkQlj+dbnExY2\\/QLyYixmohVnRAnwZChaESHYN2ZdzkFOroL\\/XoTyRO1z4DsPlho0LIwrj0kJ7ywa4DoALlbv4AEvsJejvciiv5IoX\\/tQl12VAf9lwjMUWLRAsvFDMzydS3IX1sF+mYJgBcGEbC5cWYgZ5A18iEFqOw2ex\\/jYFQwsFDM9YBGlRDYrxCchdfH6mcSTcif\\/75A4Xa16E\\/MUCFvacGAmdHVqWvtISCYcpf638JcbCYDjpvSUuTFjgGvL+aEmHcdjvlStcjNVs\\/0BleYlL\\/97i+mLewnMvaAclNrnvNS3hMOUvlwe0OHig5Y\\/88HLHtGwFYeE09FVgPIalEvQ3M5TvcsELZ3VzgfnZiVyZ94UMWLgFADjRNzN9b2HJhSci\\/Qm5HSTj8QIQs9NmQ2x9OQiKLePPA6El\\/h7sNzHU3uQJLWInYPzosDiPxDQstha0MIHxhZZyOB\\/2l+QJL5YOs9uJXpBrUZqzMG3Hwl5UYLyE5TzcXPyn5AUt9CLHl\\/QPAosrWrZbBix2gjEPYYmHJ1P+QfmLzM\\/OzrX4uLAxYoZjpL14B0ukFs6H\\/VX5hctuZ+cyZCuHAQuc08D+svaLllRYD\\/u78gAXABgCFle0cG1HjJbVyjNYYpNgvk+hDkOe8IIRQ7Iidg1GNMYP0kRrY17BEmkE8S0KdUDyGBcaFpErwFRQrkXRwgnHfKAlF+6X\\/PPypmfJZMU5LeBsmri5mMB4BEsk7EuG0uUFM2DnTuxfUlDAg5yWwRgAjEe0NMK+ZChdjOXvlhXmV7ag5eeHLgDA0ZhlbcwbWLIhLKEwAevfA1qcBGM\\/0FMpGbkLSYwvtCTCh0+EIsRY\\/a5QcWQuP6w\\/F8n1iV6lJ7SUw75kKFLw+ncHiwNa2BQJ0rL2mJbjrd\\/vfKh\\/VtD6d8qK5T+mweBwJE6MwYsHsBwN\\/HiXQ\\/0SQQCwc37rr2aLFiZF3OyFiYsHtFTDFD+UbTmDxR4t4Cf4eyz51TH3sJyFD2MN5Vh2SAH+DZcXPkpcWhiHZbqGJfLm43sZ6tfLAS3eHm8moSEWjnlBSxiHhXInu7C4ZAXeHLAfWtILH9\\/HUPL3Vv7GyeVL5Yye+Xw+m82muqTfS38yl8+6Wi61Z\\/hs1Rgk6Fv3UerL8xoYaBiGgMYlLNH+b\\/6u7Fm77WY5nwy\\/3vvddrNeuS8Wi7e3N9fXV4XCZT6Xu8hmz88zmUw6fXZ2qunsLJ1OS392fp7NXuTy+ctC4er6+ub29rZYvCs\\/1Jvtbu\\/142s4nilPzA76FXolgdPFPPEX0mRc0nLxa74BwWi3WYw+eq3KdTYVd\\/mtEFEkkb64KTe6b9\\/T5eYfh8cVLDtgdIwBC4qLu3f\\/JNwvaUO77XoxHX6+9rqdVrV0lT1NRLyBwKniyfTF1d1D47H73H\\/7GIxnC8mAgn6XxOSWFQ0Ym\\/bi6t2O9oN+0\\/4J\\/aynX\\/1mqZC\\/yKZPk0dRj9a6t4rFj1Nn6Wwul7+uNJ8\\/hvP19oC9x5oERzstuU\\/5c0vLdTjywpCUm2\\/Wq8XwtVXM7CPE8kWRVL702P+aLJarzfZwsx4QFcYnrN2FS4ybdzMWPqqF0m4zH753W43yTTZ1mCbiQNHjdP62XGs+Pr8N5IQn6DcZlxta7GT7LmnpBfsuHZh262HvIZ8+TR3Hfw0mpKLxRPLk9Oz8pvb8PdvsDqNMzWDF7kiZz7QUAn2PDkS77Wo+Gb42r1OeLcl\\/R8lcqdX\\/HE7k8nRQ5LCMRbwOAEZjMDYu3qrvYN6eg9F28fVcKxfzJ7\\/WSgQVS50XiuWH5vPneLX\\/0gADFnFc2LRQyDh\\/jx4OwoaD0c+kX0onjn5vyOVEkWgsfpRIXlS6X4u9QgPD4h0uJjGO35uzP1gP220Xo8+XxnXSwzX2S3WUua11X6UobbGfLijNCv4Jd8y4pSU+3MNbcEjaTt\\/qxcL5ccANxX9LkcRp9vKmWO9+TTd+JzYwLPZosXokptP3of6X4rD1V+UvJvHeKnJW7Hwv918M0DzHvcU4p+V0uu8XHYR2q\\/Fbt3qZ8HTV\\/HGlCu1xEM1OYY9RnsDE6MM4fM2\\/\\/2jK3bx\\/nz9PxTxdKqH+S971R8peg\\/l8sdzsrxpggxa2yTh7yXf7eo1B6GezmnTyYbnLD0VOW4OJorGi0Wg0mcvQ7HwO0WzDAuLi6DWfznx9ZQFqO3lplS9CQ\\/FHkUSxb5Ki0TIaDoeDwWg8lcxm5V8BbWfzbFnPaIm0f2WKv5u2L8+SISm+KZZpfZmsjBRUVFYG399fqr6\\/B2P\\/xp\\/dGoyTV535ba2W3Xo2eLoMYy9fFUkW+0PUU1RYBgMdlk9JHx\\/v729vb69vn8PpYr3Zwk+mcPfNdkGMg5d99LueArb+bpUvjz1fHKFwxbL1zxHGylARxMrr68tLv99\\/kaD5Hk1m3m7z3DMtHe\\/uPGjtZq1MIh42HP3X0c3rEHMVhBWdFhmWdxOWXu\\/5uSvpqdvtv36M5iuPmNkrLRe\\/4pSX3Xb69Xxz5P2yCAUoetb4HCHSSUGM5YNipSez8vTU6bTbj4+tVvOx3el\\/DCdzDwaeZQZsUOOCll9w2t5u+VounIX5\\/L6ULL4OCFT0fAWB5UOFxWDlWWbFhKXRaNTr9Vqt3up0X94HM3elAHunxujnwdp+5VdeLdnANG+GYyz7VPS8\\/YWBgrCi1MF0Y8Fg6T2rxqLD0tRgqT48PFQq5fL9fbn1NnI37Gw7HLP70k8PbJupLe0WH51imNDvVZHju96ADL8QVNT0\\/t2Iwkxj6Sq+IsHS0p1FYqWqslK+L5Xu7u6KxbvW+2g6m68cn1XjJy3RZ0+X7z61GzfyaTmjj4RZ\\/f4Uv2h+DPHwS4vAvnVUPo0gTIHFYEWFRTcWGZZqtaobS0mmpVgsVjvPknr917f3z+F848BpfKQl\\/0+2Wnbb1aAcj+jyY1WEghRJ3vaHACjfXygqSnqvZPcvSMZiBGEtIwgzjOVeZeVWg6WrZTjSP+i8DOZruzObgsD873\\/2Xn3sH9zVslu8Nm6OI4h8WRihaEVzra8BEX4ZEZie26ulsDe8bIzBotFSRaKwkuwrt8WHzrNWZVb+ge5Fj53+x\\/dgNPf8dBp7L\\/\\/fa7Usu7mTeCQSwrJ\\/RVLl9wEighS9waKwAsAiL3wsvUeisDsElmcCFhUw6V\\/JJeen3tfMSXjGkK3Xf7Hx7Lp70M9y1DmPkPJnZYQiFTm6bH+BpJgRmJncUxkLXgrTUxY9YykasGjGov4LzYxUWDTIZMzqT5\\/jxXLlfk+NnTcg\\/urJKt6LdtOn+2yMYiWkZU+KZSov33D4ZST2hq2oGUtf7Ud2tZVvlsKoWpjy8IHbStswFhMWPXRDYakqVeeHeqf38vY1WbkIz+y8A4V\\/pnq8ap0mIFRCWPakxNXzJ8oKG5ZXvXnf15v3ZkNSdRZ1xUuwVEBYujQsurPUDVYe1H8uqVJ5eGi+TdaObMbGO3C88npR+6Hd4rtNx18hLPtULF17\\/dY5wQMwMwIz5ifRfqRhLI+EsZgpi8qKAguZ3yNhmMqKAksVYeVeieSUVs196+VrNLG3O0D8LYj8Cwfqr56L2TiTlZCWfSiSKHY\\/IVAAV9FsRcnuTVge8YZk9cEwFgYsbQ4siLHcI7AU1S90X2u1Oy9DwZkA8Tfh+uBT\\/N1nLhqJclgJYdmDotne55cJCh5\\/GZm9HoLpsKDde6IhqQVhZpdFUtlI8Du4s7QYsFQAWOSvdCPpWtLVVakzWFohI\\/wmxD72suKd6mfUuz+ORGWFsASo2ElZhoUylQ8yAiNQweeNkR4L1mRRWbnRncWMwkxnQRJ82lk0VgxnMWCRaLkqSLqptF8+vkbzNVw+E34bKoe8u3jeyJ\\/EoppCWgJTJFFovdPhlxF\\/ma6is9Izy8Z43diIwirYqIsOS9d0FjAMq7OcBYblWoVF1uVl4fqu\\/FBt9YdLsqwl+j4kDxaW7eolHTUVshKc5LLxJ47KxwfgKrSt6D2WtmksNcBYisoCrzw946y00SgMKYZp+b0OC+ostxosOis6LZey8opyki5Lna\\/ZarPRdjwLvg\\/xA93Vsh21bxPRqAgtPq6SUIqSN0\\/vn5+4pWCoQLaCL3y0FkbPUKoLvNIBuyx45VhzFp0V7Yug+T0IS8GERaEldyErf1t97PbfBzPBN+LqIFP8zeNFKhaNhrQcgqLZ5huAyjuMisJKD2KlidWNy+ioCw5Lhxx24cBCJPi3ZM4CwILQklV0kS8IvhOH98y87fzjOqZIiJWQFn8VPS31DU7QnB7NVcymfQ9J7ZHkA9\\/IQoyFyev7+ubhCS8c4y3JBtVmIRJ82lmuwDBMZSVnspI9lyT2XjSDZoPQbtEtncbswOLnSgn1X\\/Kq\\/YrGXkRar5eLGSEY4hA6LPpK1+fCVFYkWDp4FIZPhhGlY8RY7gFYbiBYLjmwiNGSPawUfze+TsZjsZCWQ1H8vNr\\/oKIvLf5CMns0tcdzj0esbiw7S4WqGxvO8kRVjpvkHGWVcBbKWm5QWgpghu+QlkNK8XfrQfsshiiEJWhFU7cd0lGo+AuxlR6wJ0Va8024bmyUwqSVfauHYURLEkpZqJ4kmLNcXXEyfCRpUVg5z4i8HYfTxd9N6oXjmH1YQlr801Gh8fKOg0Km9ZqpoLbSpRMPfN64QtSNr\\/UwjDUZxoBFK4ZxEnxu0nKBOEtGiJZl0JBo2n1nY6RCawlY6drb+zsNCmYrfbIKZtrDI5qkA2NhZspydVPt4An+I5WzEGEYSouzpAWHRYCWyEFsmNytPhqncUlMVkJY9i85CKNBAUjRM3vW2HCDYqVMRGFXeoLfQUtorJTlwaLPYs9ZshosArRcHkIcNq\\/ljuMULIK0+L1m\\/qoix5eNPs4JltNjrRUjXenSjRKkAf9AHlRhwFJtQ5NhRJtF\\/QrmhL7pLEXr2jHLWQxYrGmJBb5hcrcZFOK6HDhLSIs\\/iqYrvTcEE8NTkPoX4SpIU9GYGca6JAgrJZQVJQyja2E6LPU68hXo7Sy2chaqK3l+Lk7LbcAbJrdfjWzcGpbQWfasSOqm\\/WJyYpKC14pxV0GXO7kZRU83ytioiwFLG23f0yPHRJsFLx2XEFiYOUuem+GL0RILNsXfPeVSR0cgLCEtQeoo1+zTnoLUipGOvYoKMayilIyNBouRsFBNFmVZa85Cw0JEYVUDFqCFf4v1WYStJWvCYkVLpBcgKptx\\/UiWgLWEYdheFcvUeq+EXl4oVJ6fyRAMa783kNy+inTeiYzl6uq21gH3sjRRY0HDsAqes+h7yOA4jMHKBcWKJS354PbiLzrFJM5KCMthKHZ6137BSaEyFZ0VzFW0ijGW29ewQph5wLEBS+HqptaBUxYyCiPDMASWIh8WuoVPh2GZTNrifQmsiz8rniQIY3FCyz4Wzx9TJFFo9BFHwcpfWLJiBGDslW6UsbTePdm9l3dp3dY7T9QUpV1YTFpMWLi00GGYFS2RYjADYouPm0RChUXEWtjOEtLiueIXlecXJPCCQTHnwJBsw9y01dSXeRUNwvQsA2mySAu6KDsL\\/DXAmWM8vy8hzgLmLLx5F6R2LLOS5tOSD6LVshs95I6tYAl7+MEocnbX6QOckBEYVQJD+yNEDIayckeyUijWAViaeE9Sh8XCWW4AZ6F3tDilJT0OAJZpPqFKmJbQWvam4+sOjArmKnQNjDiSBT0Zr6JP5t+jld4b\\/VyJ20YHKhBQG\\/DBYhi+CR8Kw1g9fCjF59ISyTzv+\\/ETu+nz5THKirs4bF9r6K8omW88426CgoIWi\\/EtKMgwGH58kd5fQcdT9FWtLmfFWRgpCzRGicVhBivAxmJ4Gz6zdKywwqElkmvM9gzLqJw9Pj6GnYU98RLSshdFjrKVDouT3jNSASMSez3TwMpgdAxmsGJGYZeFYqPDat+TDfyKAcs9eRSSkeFbw8IMw\\/i0RLKNl32SstsOL48NVsKk5dAUO7t7BCgho68ukdcT2QqeZ5g1Y7PMe6P3WOSVXGxQ2++BWZcqFYXB54YJh2GAtaTTPFqiF42nfcZhs+7V8fEx4SwsawmLx\\/tWJHXT6EpgkKhgoKCokMmK6gYNnBUzxTAKV0bZWHaWO46z8Pr3jAQfLocJxWFpLi2RXP1xjwdXzEqZJAqLDWth0rLPxfTLFYkW6t0e6CikqwC+Ahd7K1gQZh6zisLS7DwxqgRGQIeGYdjQMXZ8q5myoBuLaWuhEnyCljP47bmoP\\/b2VT3ejCupJIqKhbOEcdh+FYlnH7ogKF2AE3NxG9lKA69eYWetUvmKVgqTlvFdky6pAWEYPuwiAov4cBgJC0hLLFdvtSb7YWXdLaaSOCwWziJWPN7vivq9isTPS49dI40nQcHDL6IGBjxKBWHlHmtGojGYtJDzhVITZ+8Rj8LAnmT5HoTFyO+N446FjkMyaDFggWiRnKXVet1PF7+TlVghYPEixd\\/zmvq1ip7ctrrPBCooK13KVgxWgNSe3i5\\/V8SNxYClBaY\\/5AZ8PGch97MQ4y70AS9WO4uxpAWkJZqtPbY6e4jDdtPOacqABU9ZwjjsEBRJXdY7ACFI+IUm9aYLYIk9wgp+KLE5lILmK\\/IKLty3gEdNYF0WNAEiepKMfcXMchhv\\/5dBy9kZREs0W5fu7st3a9kNKhcpBRaUlqMjihY2LCEt\\/uooV2srZOCoPBGOgnoK0BLBc\\/EywYqZ3Gu+Iq\\/eQtl0FsCoSKtihWGQswDlMDFrgWnJyLA8+W4t86uUoiQeiFnREsKyN8Uy1SfSUAxHoVIV01XMhY2fLEE12rGswkjupcV7WX7kwlIDcxbq+SxFDizcWUpWHEbREslU5XvzuXq8er8\\/hVhJ0KyEsASjo8x9yyBE\\/aA4oeMvIgLTU3t0+woeg2l9ED1fQZ0FqIWZJ45BY5QsWOiD9O3DorByeoq\\/R5FMXb61F1\\/jsF3v+uzkRIcFchYkwQ934geh2Jk8zajFXQAmHfRnPxsV4kBi\\/IRVOrdX1m\\/uqgI5SwNts8D5PWM2jD5I334YllZhIWg5rcmHcLT83DC5eUufqKyYsLD6LGETPxBFU1e1pyfITHBLQdsqACpksIT97EfSFT0Gu1SWbuGhTfZsoIoBo4FPlY4hZ6HKYfxRStNacFpOq0o36cM3a9nNu1enGCxJqisZZ9ISwrIXJQvVtgApZrWKSFXMLjtxHDF++goKi+4rudx1tU3UjbFdkgKwmNuKBWixkbOcErSc1ZXjnfyzlk0zdyrDcoLBwrOWuG1YQlrcKZGX1ysJCc0JFn9RrKDlXTK1R9vrZm6vLt0rEJamICx3OCw3RPEYCMREaDk7g2hJ1dSz0L79QWW3eMqcntLOEsZhh6NIIqtlDSgmHZoTJP5qQLlKFR1wwQ6MNH\\/kYzGYvG4vZFjoaQDqZBd2S1IEFng6LEsHYpizKLCYtETOHhRY6l1\\/4rDpY\\/70zKQlaTsOC7dL+i2JlVKzbWkprRYVfxGVXeLpqEh\\/HbMVvXGvLduL3HWtjacsVOMGnna5R40Fze+FYWHsLCZgMWnRYGn4swdsXTlPi8AiaC0hLD4onr5rtOVACEcENhUk\\/NKCJOxML\\/IZdiUitzdZudRZubi4rgOwNGlYkOaNWTlmV8OYsPBTfDJnQWiJnJS1JzO9eH+S6276dK5dEgvDkjZGKcPxMJ8VP72tQ5BQsRfuKVAEBm2PN9r213TNWF2yuZu6CSa7yoYfCg5slCySzsJ4\\/JeNRgtBS+S03Gypx9YsPIdl3sjr9WorZ2HNu4TO4rOiZ7e1R9BLCFJaKCmIqeDnrhBHReLDwKatIL6SvSg2iKI0VjogT6MsG7QQlWMqDhNO8JnOYsByor5ZqfuWBsuH16xs62dGc4dtLRajlCEtvipyUqw\\/PhqU4KgQpOCpCpF7g8euYD\\/wdVrkRXtpsCKt12ITy4yIOhsV5pnWAoVhjJ3FAoeH8eKwE4WWSPJOOxCt1vY2xd9Nu3ntoiYsJisELeww7MBgka4kfUg3Ev1P+pDuSP5tJBrIvbhWJHZSeGg+UmrhAsIvsrOCZfb3Jbyii6JCxGDSQs2VWlQprEmxgl8Fn6NEBykdwYLFYRkaFrnzIb9dyXvj9MChp7BsG4U0BUuKBYugs+w3a5EwiEelW0sdp9Jn59l8vnB9dVss3pfKlUq1Wq\\/Vm\\/VWq95uNNr1ZqvWrD\\/UHx5q9+Vy8e72+lr6zkjfhbMz6SVLX0J6GQdIUSxVqDRbGh+PLRIS01GIAKxWQ9LuKrSG77C2Pd64R1LsLAxLA2hJ4pEec+rYUaOFWQ47xWlJlLQEv1bz9OSK9bN2XZ0V4Qw\\/KFjkrxKNxePHiWT69OLypliu1dqd7uv799d4PF5M5svZcjFfLZcrReu19KFrpX0Y\\/12upb83X85ni8l48j38fv3od59bjdp96eYyf54+TSUTR\\/JLC9SIIsl8tUEDwsCEthTmVl+imosm26SvSLDcP0I9lgZ5LXzmuKyhAlbDWM5iZ\\/8XBYtEy9GdlrNI9+Th9uLF8412YcBZjgFnYc4d+x2HRWORhOQcF\\/mrUumh\\/th5fn3\\/GkzHM2mdLxbLpc4GS2tQG1DrzXq1WS1X0tcejQdfny\\/9p3bzoXp3e5u\\/yJwljxPSvfy3P3aiyXyFZqXZNDlBSAFZIUIjKFvB2vZEzVhdp\\/myliuZlDYAY4EH9KnZMG8zfAyWk\\/+OKpqz1GvVvmdZy+756jxDWAuxoUUwDpMA8akvGY3GT9KX1w+19tPL++doNJ7OZ\\/OFTIjCCK6VBTUwMxIdygdPsi9JjjWbTL+\\/P5+fW9XS7eVZ+jh+JCVC3gOCKJIskKw0MTUaXFTMxgrdJ6TOvMP69nmUlfNc5RGd+WdMMldpLsFBSjIOw4b06ekwgBZqOsyAJRUvqrDI99Twag\\/Y+rOgXJrOWaghfQFYvIzD5DTk6Pgkk78tNTuvHxOJEAUQtmhwdHw88BpDW0Sb7XqxGA\\/e+51a9fYqnz5LJqQ3xWt0Yol8udGk1CBVR0SQUqkACTd0qD2OisqKsUbzVXCUGchYYBOzk7OwBo8trEVjJZW6aRhhWNWj2ePtWzlrDQszDnMGi+VaisQTp5nLm\\/vaU082kulsLotPiiU1Ehx2oOHZzJZgRuFGcp3ZbPT98dJrtcrFm4t0MhmLRD1gJZrKl2ooLA1lkWKw1BFUsJyeaSqYrciLlh5xQWrG8hLNnF8+kLPMSHpfp+nEO\\/gULM4zfE4cZtCCwNL0xlqmtxfnBixghg\\/BAtNCwuKIFomT84tS\\/fX1Wwq2ZiolfNm3GjtBmrDJYOBIpC2X89H4\\/blVucmeHx\\/FXRQHjnPlusYE7SaEo9QMwajQhVwgBENhuTBhOT+\\/rIHjNIKwUKVjChahWUqrtqQOS6ppwPLw5QEqu1FNuTqWshiwAHEYN8MXZwVYOFLMlTg5KxSr7dfP0UTCRAFlpvxX\\/hCgRoeHjY8wOKy8hukzTG5kdFbzyeC1J6U4V7n06fGRvTAtmszeg4iofoLFXup61dNsatkSO0uIpj2KyiW6TLP6Gjm\\/quGsILASZsazMvqpkt5n+Mr6VWGRb+jJfRy2mzYK8tUzZBjGjMP4+\\/CdOkskkjy9LNZbvbfv0WQyVUjhSYZIAWmu\\/s625wghswap4bgMDxjVb1ar+Xj48dpuP9xenp\\/Gj6ICEWnq8r6mLUkoN9EpqWOOArsKtbGEek4KyYqZ28uLJHtdwysMJCzQnmLgqGOUFiYsQqOUGZazqCvYcJYH9wNiu475U0MMFq9oQVdDQvoePH0Ox1NaMhn6f2dTHkVWxJDgLI0Ps4YmGJxZ5P9WyJg5znz0\\/tS4KZwlj9jIRJP5+xoAB4GKvlSxRMXM6ysMVoocVtBCWFaD5abBhaUKbsAHOvhoHAbRYj1LidNyxrCWlO4slb7b2ePVy5UWjvJgcRqHRSIRrrVEIrHEyfl1qdX7+h5PAFIsNdOJYlFkw3BE8xp7pTMLXn5+fpQgbf713m2VbwqZ0+NEFIvRYqn83YMlJKifSEtDjYNwUKA0Gz9xmEAlR6GSyVwUGygqaCmMWwvjOYtVgu8iw5elwFJ9qFTn7lj5ebnL4bCksbFjDBb2dkleA59NSzR2li3VX16\\/huMJXxIOE1GWJG5EqXEToJHIyD3MDYyNNS6K5N+vV4vp6Ou907i7Tp8cx2RmolIMVq3JK9HEQyUEAUVeoWTwRZDCMhV07wo2kk\\/UwbSlmbtrYE0evG5M1RRIVqxhYSctIluLIVhSahRWqfTdsTIvmRGpXWexthZOdh+NJ9PF1ssXgsl4PJb\\/Nzb\\/rykLmPgkQfiIkuPAaZzHZj+Ituvl\\/PuzU729Lj80G5iDoKriMkMvbNYEWa53eK7CzVYoW5HWR67UpGdrQFiwueZ7qoEPZPg2B144sKBhmLSENVgargbExs0LFBZt3AVzFtsTL9awxI7Ocre1p\\/evEUwGSxpOyF+3BQ6NDGg2NDX4iMAKbHCKVs34zPwQxMj\\/f71dL6bjwftb7+mxpSWrmo8wUKmYwmDRVyu5n0RZp3oIRqT2JCyZ3H0TmR2wAYt7Z7E6D4kNS1IJwyoVN7PH6\\/qV3pzVLm2UjoFRSt5OfHFaoseZm\\/rT2+dgOBpBRIxk6b\\/VPsZj+O9i1qOQo\\/9XgwYGR0lwjByHkdJQzAjEZjaSGa7BoMTsdP1sN5LZjIfvL912g8YEMRUIFP0Hu8EK\\/lPdXKVgydhYHel0vmw4i1m8RpInCJYyfgPMFj6zh0\\/DkkVYsUxaZFqUx\\/+1nTcm1x\\/X6BuCDVLiLXyrDJ+VtJA5SuL0\\/Lbx+jGQNBwOR6rGI2Ep8IzVD\\/MP2QCJmA5mNSA6fGrgkQBhp7GKyna0fjab+XTw0X\\/qNBu1GtdRsGUKnRZB7OflpfbqiryscKabOa2de7K5Y1UNYzgLkLMwu5JG0iItYPVRmY7PPd59VrC3hJg6tobFmhYs+Epkrsut3sfn9\\/dA1XBoEmNqqMv83RD+m0ycFKJwagB6iBiNDNIoZAhqKKMhoaF5YQLDQAZmxdB2s1pMh9\\/vr91Os16lLUVbpOgqJQ9WwcYXsZ\\/k2Lo067Ppqwdox4xROCaaO6S53d2Rt0HT4n05TFnECi1Np43JRUm+CQAWLWexDMPsxGFHaTn4+vj8kvUtCyHGYITkA5L8V0dD9R\\/ov4W5EbMbts\\/ITU+BMgCa0lg6DKNgBhNjAYsZoMnVgOHXS7dRr9ILFEQFsxWyC8hK7mVYrmuwryDWQo\\/XlEFYGG1J5igl9gAw0VlKcxFLN3NfWTtkpVvQ3JbIWXRn4cBiJ2mJRuPJzFXlqf\\/+8anqiyJGRkblZiAg6W+xQDJ5A9Ch8ZmY8MDJDctqnFXOBHszdmExqflZr6ZyUvPYrFUr6', '2026-06-02 05:54:02');
INSERT INTO `integration_logs` (`id`, `source_system`, `destination_system`, `payload_type`, `reference_id`, `sync_status`, `response_message`, `synced_at`) VALUES
(47, 'SDO-BAC', 'SDO-FAST', 'PROCUREMENT_APPROVED', 'BT-TEST-0003', 'SUCCESS', 'Draft generated successfully: FAST-2026-000003 | Filename: approval_6a1c557d979fc_PR-0003.pdf | Sender: SDO-BAC | PR Number: BT-TEST-0003 | Timestamp: 2026-06-02 07:54:10', '2026-06-02 05:54:10'),
(48, 'SDO-BAC', 'SDO-FAST', 'PROCUREMENT_APPROVED', 'BTKKX-202604-003', 'SUCCESS', 'Draft generated successfully: FAST-2026-000011 | Filename: approval_6a1ce3407a0ce_PR-0031.pdf | Sender: SDO-BAC | PR Number: BTKKX-202604-003 | Timestamp: 2026-06-04 09:55:04', '2026-06-04 07:55:04');
INSERT INTO `integration_logs` (`id`, `source_system`, `destination_system`, `payload_type`, `reference_id`, `sync_status`, `response_message`, `synced_at`) VALUES
(49, 'SDO-FAST', 'SDO-BAC', 'DV_CREATED', 'BTKKX-202604-003', 'SUCCESS', '{\"reference_number\":\"BTKKX-202604-003\",\"event_type\":\"DV_CREATED\",\"system_token\":\"fast_secure_token_456\",\"payload\":{\"fast_reference_number\":\"FAST-2026-000011\",\"fast_financial_status\":\"Pending Support\",\"dv_number\":\"\",\"remarks\":\"Automatically generated draft from SDO-BAC procurement link: BTKKX-202604-003.\",\"synced_at\":\"2026-06-10 17:03:28\",\"checklist_files\":{\"purchase_request\":{\"base64_file\":\"JVBERi0xLjcNCiW1tbW1DQoxIDAgb2JqDQo8PC9UeXBlL0NhdGFsb2cvUGFnZXMgMiAwIFIvTGFuZyhlbikgL1N0cnVjdFRyZWVSb290IDkxIDAgUi9NYXJrSW5mbzw8L01hcmtlZCB0cnVlPj4vTWV0YWRhdGEgMjU5IDAgUi9WaWV3ZXJQcmVmZXJlbmNlcyAyNjAgMCBSPj4NCmVuZG9iag0KMiAwIG9iag0KPDwvVHlwZS9QYWdlcy9Db3VudCAxL0tpZHNbIDMgMCBSXSA+Pg0KZW5kb2JqDQozIDAgb2JqDQo8PC9UeXBlL1BhZ2UvUGFyZW50IDIgMCBSL1Jlc291cmNlczw8L0V4dEdTdGF0ZTw8L0dTNSA1IDAgUi9HUzYgNiAwIFI+Pi9Gb250PDwvRjEgNyAwIFIvRjIgMTEgMCBSL0YzIDE2IDAgUi9GNCAyMSAwIFIvRjUgMjMgMCBSL0Y2IDI4IDAgUi9GNyAzOCAwIFIvRjggNDMgMCBSL0Y5IDQ1IDAgUi9GMTAgNTAgMCBSL0YxMSA1MiAwIFIvRjEyIDU3IDAgUj4+L1hPYmplY3Q8PC9JbWFnZTkgOSAwIFIvSW1hZ2UzMCAzMCAwIFIvSW1hZ2UzMiAzMiAwIFIvSW1hZ2UzNCAzNCAwIFIvSW1hZ2UzNiAzNiAwIFIvSW1hZ2U1OSA1OSAwIFIvSW1hZ2U2MSA2MSAwIFIvSW1hZ2U2NCA2NCAwIFIvSW1hZ2U2NiA2NiAwIFIvSW1hZ2U2OCA2OCAwIFIvSW1hZ2U3MCA3MCAwIFIvSW1hZ2U3MiA3MiAwIFIvSW1hZ2U3NCA3NCAwIFIvSW1hZ2U3NiA3NiAwIFIvSW1hZ2U3OCA3OCAwIFIvSW1hZ2U4MCA4MCAwIFIvSW1hZ2U4MiA4MiAwIFIvSW1hZ2U4NCA4NCAwIFIvSW1hZ2U4NiA4NiAwIFIvSW1hZ2U4OCA4OCAwIFI+Pi9Qcm9jU2V0Wy9QREYvVGV4dC9JbWFnZUIvSW1hZ2VDL0ltYWdlSV0gPj4vQW5ub3RzWyA2MyAwIFJdIC9NZWRpYUJveFsgMCAwIDU5NS40IDg0MS44XSAvQ29udGVudHMgNCAwIFIvR3JvdXA8PC9UeXBlL0dyb3VwL1MvVHJhbnNwYXJlbmN5L0NTL0RldmljZVJHQj4+L1RhYnMvUy9TdHJ1Y3RQYXJlbnRzIDA+Pg0KZW5kb2JqDQo0IDAgb2JqDQo8PC9GaWx0ZXIvRmxhdGVEZWNvZGUvTGVuZ3RoIDQ3MzQ+Pg0Kc3RyZWFtDQp4nL1cW48rORF+H+n8h35kkdLH94t0FGkmM8NFIAF7JB4QD6vV7gFpWWBB4u9TF7vbdreTTidZljNJt+1yuVz+qsou5+PzT\\/\\/9+\\/fffPvf4eX3p+Hjr762w5f\\/fHgSY5TDlw9PWtgx2MEoM9pBeTGqQUePr3767sPT97+EGsP\\/PjxBO5faSROHX314utDy6w9Pwxt0+G\\/swo9YL2I950cVBh386Knen385\\/AhVP379r29+HD59+vj7029eB\\/Hxd9\\/8+GX4xXc\\/Hv7w66+Ox+HldSull8\\/A7LscpBw+f\\/\\/hSQ4C\\/pODMXKUdnDALHx8\\/geWfME\\/MJK\\/\\/GL46q\\/D599+eHqDxn+k\\/xPvw8c\\/TCzJmgvPA9fAhhsNDNwVPPA77BjfV9W\\/hZ4\\/\\/uYf33z5Lg6v\\/xzmnqrxq874xSjwfyF4BcRttNBNgJGFywLQbvBUH8cucOxidexr7Oj7s2PNKJQdvFdj9Ht4Mo\\/jyYpRhz082fvzJL3BFeWV3yknd3+eFAjKhcHD+t8nJ\\/8AnqQcnR9c2KtP4QE8GZi7MDgH+mT38BQfwZMYDfCkQZ928SR72HwLU0LR5Em7U1BSPgQwbVR7pXQ7gqslFNhR2MH6NXX6JPRrPFZ83Qo8YF57vW0SwQOshtIabb41oL1qF1MPMBuZKWVHq3cx9RC74UhZYGXtlNQDDEdiygQx2n1MPcByZKbsbp16gOnITGnQqV08PcJ0RPS0jQSN2sOSeoDhAIzWUWwUEXruUsMQvIYgwJMbExBMxqBs0RfWI1\\/dO4UODgAhVqjqtGPr2Z+LhHhgeoj1uFQAIXsfVpyZT0LI5+NBwaeJRwsfwR8lPkZ9POCzcceDhk+ljjLAp5BHU5arsDAUG\\/k0LZ\\/gncKiWWe0nYR\\/V7EVPBkBszH1KmEiLom5Z1UvU1qXsxFh1LovaAuSlAJFF44en1+ODiX6Yo6BLK7QFoQLX60V+k3QPPBrwa8D\\/HNYvBD5Vp5bmRtsYHfJ\\/GO1FQDrLigIU0ftHfy1cvgJ9wOUwiUCUTVyBUsR\\/GpVbAZMxTYXm6oYllYuBg4xnqprGLBIUpcd4MbBSukqfRQamI5czkKrq1gAch\\/6IyjKO0OgEaZpgeIV5lcLM+HVwgmBeKEBrnu\\/CVl6XtRFQmeRxaqV0I2ggmCDYUIxjAC8HCLDxsHg8wv+60HIJYYWEOICQcgqR5shhHvdAiE9B\\/AypfMQ0pEoAYM15J2j8Bx9SPWCeCKlE9K\\/Hh0UnF5J2tKGo0SpvxshwzuBCUpcOqDz+n6UkgEGbMBUV4oT1LVHEz9VdbBP92lqLvXzUdq6inAwyS5xogPVUQB4hGW5Dk746RXK1GqQUcPZXvmBwV\\/VyGCOyrM8xNvbMc4iQl5RIVkcJx4DjhNE4WUzBkDug\\/SFnFFm7pkmA8aFr3sAfWlEC4AOtMe4PqJrADojkUBYSUDWIi0URl8g7RKqVltnHOXmM44uwazTOXGG3qlPSFeAeC4lLysB7NZCGhQXnhlUp+OpeJV4HnMq7Y+5Q30qXqV+vpRmN+GkRJy04OBbSz6oguqwCIxe+qB+cDagtVdAFmJM5OcsuvUiyU3EOhYjalygztu8c97xkRjaJu8TkSWm1YdmxQKK6LfssGaLQmsyWRoGJMnVsTmRxQfJKz07vODIrlmfjQNcrFeIIPBoYHWA6+t1Nb7pBcw3yN4ogQrtnFmyNhwwcHLD52\\/JzPj0z6R\\/NjmfNr+\\/TWTtcYrT2GqVsbs4oUB+cEEXLlwBErnQriOIz4VpUGU5sWlhzDEwfY99xxhd5Yeus1W0s3O7unvijWWaEEQUbmDVPdfKZJqapcPoAS5ovzYNyMczENDbIrlIKKtgaPwzMBEwJBPnfcC08mEZWnQdNFlR6diTsKfjIQVH9E5X7zpaeD1bgXbbVtgCF4DY0s937S+aTn\\/iLaB3Qn2CZ4Kuc2Yhi0Sg84EyAs\\/kYJNsoE6YpEf+Fbzxq4VA3aUCCCRlenXA4J6cocgilo6HnR21AxV49vbyNGE51m3oanX0iQ0tabKwdKfwTCM8q8g5XgjvCnDt7bHtnU6Du1hORzTC1Wyyo0t+M7ib2X8kDa6m9MRShar19KFT6WdVKGSMPiYpCSgB+a84l3KqQjSglZLtItLJdeV6WOdOSm20J8u3Iob9k7\\/oBPxgu9rJJEwWYgcxstzo65sjzc1aTtXx+UWmJWHrecOXuAhQp9GbyDLHAOygEyFYlVm+tKaqdfFMy\\/deC8EI3HRYyOKKhdDb2N27EHwcvRqcipijUKkAOl221stideQVQK8Q17IDZ8QsWYhtG82lRcTzPU1KLk6vGTxL6ZeRKBYzzh0VdUkx8pE9TlYIhEUdEtrdbeowGrRLQW2fOt3NatnHES4s5EjG+ZDySo7ObVvfwpHYi\\/O6t8O7kSO7cBMk7i7ZuDTbgAC41kHp6EPSh0SVRDAWml9bqMjwrAUvh7lBXdMDMnkmEYrXqZUy\\/F3l788TYbVbR91CRwlqF6O9Qv7nthv3gD+sGpR\\/iHOcMG\\/sWN5ZT+EiRZAcSSqGkTDtdR2V4oMM3oH0COrTY6rOsyGPXvAO\\/Z0WvvEE1osh3C3KsQ4Xzdr+jqfCmdtuIMMk5oDEXQhkuOckCqTqQzpum4OYtEOxDLDOFrKIeW\\/jPNOp98x0xcHZ8CuRXwu\\/8jZLmbinTdJiaUbDaXhqPdHx6WyNr6cVU9C2ifY6w9KNkvMjA6UrAnFpY8tvRdDNBI0GFRxlCBiZwoCRorIKd62IpgX8RuZ0pLPiPk1fnLUOdNqqnVOyc9SqIqbBcfYiyCCAEAYDXPtQJC9qkbIXC5rCytCjCdFaSBmRTo0OFG4wIBCYvoKmmmlKj5LzlDipnEVFQZe9TrKk10STiso2BVVT51kWcgmzXBRlh+LmnUNXlayYSfsB3B2\\/xN6ooGpQ9ObK3moQyBF94PMoj\\/z7QcIj6oXph\\/S65\\/NdpsSQ5htIC4Hb+c4pkLLZUPGnbp9lemYjBr43vjyxrweKgCaNTCYAs44M0nh65AtamQYYxVAW59eevUuklsIxFYtX7PdP1bAnMLyuZO4ElsFR6xant8otNHJTxmOe2rrceknE60oQNR06emtwCwpRAVayFJXKtRmvPf+xpBU6tHhAEPLWaSZCgCSgZef8W7vkrWRPHbwaljdOcoB\\/PhXgtPIXlWeJP1XeHlYpXpimDJ41zqF4r1sg7QU5VCOtSjKZpbmDwDRU0WzWmfT8nisb0lKuHHJnE82l97BdyFI0UlYAFpgvuyrl69QGcB8zzSTGbBDroC3QdjyDHabn4l+mlEYDWuKaI2SIY2VOloK\\/P4EBMNbDAvpflVaf3F+ZoINlLWcYAZhJmkFa0Up8M4eq5VBKOyq9kctNyea9sOQKKYaFEFXAZgV7CuykXwoxr0CSmU84yacruIrMDLo6BSA3iLJmE1wtPA\\/YxuoVqly\\/YoPvBucC2nGDf40suJPGZp\\/EgI8G5leNmFjt0B+cra89d6fC9EKb813nuBImrI4KCA6c13k7IAkmSqMiCmbdJVy+\\/VM9yxQsesK5dha3MOpaRqWm6b4Xo6urZWWCW+H30i7ohBU3VTAQ0QodWe3WF1C9xj3bcEyur8clYYnTlaXGpznRBjcZLMsWITsctBeORkBlxHIJnZJnYjRbuMWy2sq8au2uJLu7jfkNIp94Ab8cGGm5wJtSvHowtAHP30mqHDAwmZePk+eWT+9o+VzHefHUp5qO3U88DpDV6FWG52tUks9BNZ+DCmnhn06fy43y65mN6p7MEoviHmzZB8oQ5Qeen1yenl1m1DWMSqlJ1e8kv03WuncSvxtswFjyrr3eAjXgq3qTfcx3hpku\\/IQpHJIpoKHDt1wfmhv0keTr3HA3DLXDCoqy9LYPLDOpKJEMB5oQkt3nM9DJXmAeNWmXPlqKF9\\/5PZGQltwZPPKKxeNyh3Q\\/9BqH0d62MXe0bd5DMP7sBhBe\\/dTgPdoRXBPcAcI9M+P7ezUm70nQTVdgMwbMQcOmArOFRlm3XY8StNFQ0UVPqUiBU4xitSPTLpnusdJlUp20IbxFOXgRMFWiuW\\/7acrkSUlCQp5yqjrllnL6epjT+1yk5aDf8kEhRXsuO8Vh2hcmNTOCF0\\/I+8NTJybtDUff5jhSRjclKam0I93lhVMmkSeFn1PaPQ+JecROPB5jvS298c0ybTOVrAyUY7Yq1OsCSY06CT6xGSPdCRQQ0yqHrnVXR2z\\/\\/Ooyrbz\\/4Gsl0ZgMaCGmTtFFE\\/g4nhTFKbFWps8UAxnDz5hxVhUUvl3ZHg93fMLSshqmZ0RGHa5n+VOc+P2EWi+pPHA6B37hF7Hp4JQr5IKXucXyWGKr9KRoxGdwF1J35LfpwnL\\/APDGKdXTbapm3SfZYhIEytY8p+dTnsN2MtvZNydefpM25PKpYZpG884VlU4FU1+CExVNTTDXz7M4q5Wa2l2WaP8Ac69EDQTsML1dkU5qvP1TyceU30p3\\/9pYCI2N7TVC6zF3R6Y8OR0XF0fWzGmRmGYJPDf1TpTwPmnuNWuIcamESG1raLsjYFmj0OatC2BDnMu\\/WJIQxmFCXuDfbACPK8YYWseqXpr9s+0bwQ5gWOfh5CAvy88lW5JNkmNfuZ2ZPHWN6coT5UMzUT16E533hl6q16pSy8fm\\/jfSx3JSKK1ThSQY8JwnHddrHCTjP6lqZjTpessARErVmpjAfuJcThXuaYIjnWIvVGCT9e1tit2skMLkC9bzTnySf0p7gfiyElt+ztWm6qdJHw6KT1mO+SSoVBTMZaHHdLozteO80rkWU33nG6kTEZ0y71L1lis842l7v+ckasqyWIht0yT2r1zcNokm2hkks9W63lPNKJ78z7Vco918OoXyWmN0ZY\\/rln7olvFqP26C2TTAPPIEA1Jlg5YLXlqRLE+ebmAV4UA\\/XiRGCLyUs9qPaaMTP438nmtG2tWRbloz\\/asyu\\/lJiyb4Hez0r0zcuITB32unJ5m1CR+L0\\/AKVhMQzufXlZnNQKqnZMMaRzNOKp\\/SGI8FuhYmkzVlguXn47QnWVDP2D61S+e2cz1b7QOqnEcNFcLlMeXnSSQ1taWNqAaDG6bSl727KR+zMUQHlRIH7u\\/RLmZ6k971LzXcqndy3hDYazryp56R9f6mY4XRh5iOtX50awiyyXhuTUeueWqNTaxdVulnM5uFV1ewbV\\/hHtJd2RiKZB7aYW\\/Syv4Ng1vB2elRXsuOe9Suo8FMHr0LnBOqTtVaUE7PC2hKyDUhml0n89YAn22c3wzqjK6Tb3ze9QZqJtaWI1H1NVO8ObasVtuI8sxH6fKFrOC3g\\/0l5tMay5K\\/6\\/6oNXSBdDHTm\\/TuUVujxtjR34jNvsVm9wBsXvL5EGhe6cYuXfTaJnWR+dSEAZPkJpw9TSK8ozcu5c8hMSN1T2KmNTz+Hpqxale8XnCwaT3dvjHeMysYvZ\\/hZu3c1umCARlHBXG8Hk3EjE5r098i\\/4yeqVPBifdFkyKBpptr7kzZX6CTPOUG+i01+lP0hY\\/UFX4pKhf9uG4\\/th6XsbRRqgY6KKc\\/c08x58\\/Tl6p60Vfo9uXmvowLebtd2yHQbuwQqnx9fMLOKHGtqj535kW3M18OTIPGWAhAJV6bR36VqTvjt5zxhCVVk6I\\/1e0vlP1Z+gFnCHWFxGkBDXWjdbKetcCzxiV1m6LHvorEske6V2CcoLNhzHXQZW4DveGbD0Ndt+ipqyRelL9lHbNNMnSpIWByc6WQ6S0NDkuqJkV\\/XUXxxW9n42EWHoGAXCQY6EAXWHzVH7\\/k+ydQULeY+wtdXfGqRBe60oO\\/zuTAlwQloD9Fbhw+0rUO\\/FJVL\\/rq6okvgETyFVG8nCMsXSvwoH50NauYOnrNmskXv6pWRZ9dTfEFmCj+PXYL8afzA2IgIEV6+iE9AUzFgE+58uLFVP9vmU2BySYu+uEg+WfeEY8mWgWXfS0roOjAv\\/HoYKw2XQ4CCuVdoR\\/yY1X1h6apwDeZQQAs4I7XueIRmOAw8btgL5xJafTdPDEIVAwTw\\/VsEt6vpcZLOn5Co5QYFXjCALbpIIfqjfKa95inq98p7wiQYiXleyXZ9IFMSZevEJ9jai0D9oFMKdtkaK1LaiU37IFMgZviN0hqmbz9SKZMm0a8YIrvE4ac4ClXT1AeyqJv0j9X5fYzM2Uvyu0veeMx3UWihC88nUpXkTj0dUfV+V3VR3If6ZfGLnEv855vccdO0PeY9hY8r6L8E4\\/pDp3JWwsUzZ+YwlQ913MsERCRqu\\/Gm6I01yYKnp9V2mQ4pKv1ewWnrhecx+t\\/5+W2GtD8H8fHPX4NCmVuZHN0cmVhbQ0KZW5kb2JqDQo1IDAgb2JqDQo8PC9UeXBlL0V4dEdTdGF0ZS9CTS9Ob3JtYWwvY2EgMT4+DQplbmRvYmoNCjYgMCBvYmoNCjw8L1R5cGUvRXh0R1N0YXRlL0JNL05vcm1hbC9DQSAxPj4NCmVuZG9iag0KNyAwIG9iag0KPDwvVHlwZS9Gb250L1N1YnR5cGUvVHJ1ZVR5cGUvTmFtZS9GMS9CYXNlRm9udC9CQ0RFRUUrQXB0b3MvRW5jb2RpbmcvV2luQW5zaUVuY29kaW5nL0ZvbnREZXNjcmlwdG9yIDggMCBSL0ZpcnN0Q2hhciAzMi9MYXN0Q2hhciAzMi9XaWR0aHMgMjM4IDAgUj4+DQplbmRvYmoNCjggMCBvYmoNCjw8L1R5cGUvRm9udERlc2NyaXB0b3IvRm9udE5hbWUvQkNERUVFK0FwdG9zL0ZsYWdzIDMyL0l0YWxpY0FuZ2xlIDAvQXNjZW50IDkzOS9EZXNjZW50IC0yODIvQ2FwSGVpZ2h0IDkzOS9BdmdXaWR0aCA1NjEvTWF4V2lkdGggMTY4Mi9Gb250V2VpZ2h0IDQwMC9YSGVpZ2h0IDI1MC9TdGVtViA1Ni9Gb250QkJveFsgLTUwMCAtMjgyIDExODIgOTM5XSAvRm9udEZpbGUyIDIzNiAwIFI+Pg0KZW5kb2JqDQo5IDAgb2JqDQo8PC9UeXBlL1hPYmplY3QvU3VidHlwZS9JbWFnZS9XaWR0aCA4MTQvSGVpZ2h0IDEyMTAvQ29sb3JTcGFjZS9EZXZpY2VSR0IvQml0c1BlckNvbXBvbmVudCA4L0ZpbHRlci9EQ1REZWNvZGUvSW50ZXJwb2xhdGUgdHJ1ZS9TTWFzayAxMCAwIFIvTGVuZ3RoIDI3MzExPj4NCnN0cmVhbQ0K\\/9j\\/4AAQSkZJRgABAQEAYABgAAD\\/4QBaRXhpZgAATU0AKgAAAAgABQMBAAUAAAABAAAASgMDAAEAAAABAAAAAFEQAAEAAAABAQAAAFERAAQAAAABAAAOw1ESAAQAAAABAAAOwwAAAAAAAYagAACxj\\/\\/bAEMACAYGBwYFCAcHBwkJCAoMFA0MCwsMGRITDxQdGh8eHRocHCAkLicgIiwjHBwoNyksMDE0NDQfJzk9ODI8LjM0Mv\\/bAEMBCQkJDAsMGA0NGDIhHCEyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMv\\/AABEIBLoDLgMBIgACEQEDEQH\\/xAAfAAABBQEBAQEBAQAAAAAAAAAAAQIDBAUGBwgJCgv\\/xAC1EAACAQMDAgQDBQUEBAAAAX0BAgMABBEFEiExQQYTUWEHInEUMoGRoQgjQrHBFVLR8CQzYnKCCQoWFxgZGiUmJygpKjQ1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4eLj5OXm5+jp6vHy8\\/T19vf4+fr\\/xAAfAQADAQEBAQEBAQEBAAAAAAAAAQIDBAUGBwgJCgv\\/xAC1EQACAQIEBAMEBwUEBAABAncAAQIDEQQFITEGEkFRB2FxEyIygQgUQpGhscEJIzNS8BVictEKFiQ04SXxFxgZGiYnKCkqNTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqCg4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2dri4+Tl5ufo6ery8\\/T19vf4+fr\\/2gAMAwEAAhEDEQA\\/APn+iiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKUAkgAZJ6CgB0MTzyrGgyzGuntLVLSARryerH1NQabYi1i3OAZWHPsPSr1axjYzk7hRRRVCCiiigQtFFGKACiilFAwpaSlpAFLRSqpY4AzQAUoUscAZqZIO7H8KlACjAGKlyKSIlg7sfwFSgBegxS5pKm7ZVhaM0maTNIB2aSkopgLmkzSZozQIWikzSZoAXNGaSjNMBc0maSjNAC5pM0maKAFzRmkooAWjNJmkoELRmkzRQAuaKSigBaDSZoNACUZpKBTGOzRmkpM0hC5opKQmgBc0ZpM0UDF70UlLQAUUlJQA6kJpKTNADqM0lIaAFzRSUUALmjNNzS5oAXNJSZozQAtGabS0ALmkzSUtIYUZpMiigQuaSkooAXNFJmjNAC0lJmjNAxaM03NGaAHZozTaM0gFzSE0mc0E4q0iWwzSUUUyTj6KKKyNQooooAKKKKACiiigAooooAKKKKACiiigAooooAK2tJsMAXMo5\\/gB\\/nVTTLH7TL5jj90p59z6V0QGBgVcY9SJPoFFFFaEBRRRQAUtJRQAveigUUDClooAzQAtKAWOAMmpUgJ5bgelTqoUYAqXIaREkH94\\/hUwAUYAxSZozUN3KFzRmm5ooGLmikpM0CFopM0maAHZpM0maM0wFzSUmaM0ALmkzRmkoAXNGaSjNAgopM0ZpgLmjNJmkzQA6kpM0ZoAXNGabmjNAh2aM03NFADs0ZptGaAHUhNJmgmgBKXNJRQMXNFJRQIXNJmjNJQMWlptGaAFNFJRmgQUUlFAxc0UlFAC0E0maQmkAuaTNFFABRRRQMKKKSgBaM0maKQC0lFIaYC0lGaKQBRmikoAWikooAKTNGaKBhRRSUALmjrSUVaViGwPtRRRTEFGaKKQzj6KKKyNAooooAKKKKACiiigAooooAKKKKACiiigAqe0tnu5xGvA6sfQVEiNI4RRlicAV01jaLaQBeC55Y+pqoq4pOxNFEkMSxoMKowKfRRWpkFFFHegYYooooAKWkpaBBRTlQucAVYSFV5PJpN2GlciSJn56CrCIqdBz60tFQ22WlYXNJRmkzSAWjNNzRmgBc0maSimAuaTNFJmgBaM0maTNFhDs0lJmimAtJmkzRmgBc0ZpuaM0CFzRmkzRmmAuaSkozQAtFNzRmgB2aSkzRmgBc0U3NGaAHUU3NGaAHZozTc0ZoAdRTc0uaADNJmkzRmgB1FJmjNABRRmkNAC0tMzTgaAFpDRmjFACZpaSigApTSUUgCiikoGL0pKWkoAKKKKQCUtJRQAUUUUDCiiigApKDRQAUUd6DzQAUlFFIApKWkosAUUdTS5q0rEtiUUUdaYgooopDEpaSloA4+iiisjQKKKKACiiigAooooAKKKKACiiigAoorQ0uy+0y+Y4\\/dIfzPpTSuDdi7pNj5afaJB87D5R6CtSiitUrGLdwooopgFFFHegYd6KKckbOfl\\/OgBMc1NHATy3A9KkSJU9z60\\/NQ5FJAAFGAMClpM0makYuaTNJmkzTAdmkpKM0ALSUmaM0xC5pM0hozQAuaM0maTNAhc0ZpM0maYC5opM0maAHUmaTNJmgB2aTNJmkzQA7NGabRTELmikzRmgBaTNGaSgBaKSjNAC0UlFAC0UlFAC0ZpKKAFopKWgANJRkUmaAFpabmikAuaKKSgBelGaTNJTGPzQTTaKBC5opKKQxc0uabRQAtFGaSgBaM0maKQC5pM0UUAFFJRQMWiikoAU0lFJQA6kNFFIBKWkooAKKKKLBcKTrRSirtYlidKKO9FABRR0pKQxaSiloAO1FFJQByFFFFZGgUUUUAFFFFABRRRQAUUUUAFFFKASQAMk9BQBLbW73U6xp36n0FdRDEkESxoMKoxVbTrMWkHzD963LH09quVrFWM5O4UUUVRIUUUUAFFKilzgCrMcQTk8mk3YaVyOOAnluB6VYAAGAMCkzRmobuXawuabmjNJQAuaTNJmkzQIdmkzSZpM0wHZpM0maM0CFzSZpM0maYDqSkzSZoAdmkzSZpM0AOzSZpM0UxC5pM0lFAC0maKKACjNJRmgBaKTNGaAFozSUlMBaKKM0gCikooAXNGaSimAuaM0lFAC0UlFAC0ZpKWgBKKKTvSAWikpaAFopKKAFpKKKACjNFFAC0UnNGaAFopKKAFopKM0ALSUUUgCiiigYUUUUAFFFFABSUtFIBKKWkoGLRSUhOaLCuLSdqOlFUIKDS0lABRRR2oAKKKKQwooopgIaKKKQHIUUUVkaBRRRQAUUUUAFFFFABRRRQAVr6RZbj9pkXgfcB\\/nVGxtGu7gJzsHLH0FdOqqihVGFAwBVxXUmT6C0UUVoZhRRR1oAKkjiL8ngU+OHHL\\/AJVNUuXYpIQKEGAMUZozSVJQtJmkJpM0xC0maTNJmgB2aTNJmkzTELmjNNzRmgBc0ZpuaM0ALmim0UxC5ozSUZoAKKTNFAC5opKSgBaKTNFMBc0lFFABRmikoAXNFFJQAtFJS0AHSiikoAWikooAWijNJQIWikooGLRSUUALRRmkoAWikooAO9FFFABRSUUALRRRSAKXNJRQAtFJRQAtGaKKADNFJRQAtGaTNFAC0UlLQAUUUUgCiikoAKKKKBhRRRRYVxKKKKoBaSijtSAXtSClPSkFAAaKDR2oAKKBRQACg9aKDQMSiiikByFFFFZGgUUUUAFFFFABRRRQAUqqzsFUEsTgAUlbOj2WB9pkHJ+4D\\/OmldibsX7K1W0twn8Z5Y+pqzRRWxkFFFPSMufQetADVUscAVZSMJz1PrSqoUYApahu5aVgzSZopM0gFzSE0maTNMBc0hpM0maYhaTNJmkzQApNJmikpiFzSUUmaAFopM0UALmkzSUUALmkoopgFFFGaACikooAKKDRQIKKKKACiijvQAZooooAKKKKBhRRS4oEJmijvRQMKKKKACiiigAooooAKO9FFABRRRQAUZoooAKTFLRQAnSilooEJRRiigYtFJmigBaKSikAtFFJQAtFGaKACiiigAooooAKKM0UABpM0ZoFFgCiiigQUUUlMYtFFFAC9qSiikAUtFJQAUUvakoADRRRQAlHSlpKQzkKKKKyNAooooAKKKKACiilVS7BVBJJwAKALNhaG7uApB2Lyx9q6ZQFUKowBwBVextVtLcJ\\/EeWPvVmtYqyMpO4UUVPHFjluvpTbsJK42OLdy3Sp+gwOlFJUt3LWgUmaM0lABmkzQTSZoEBNJmjNJmmAZpM0ZpKYBmiikzQIWkzSUUALmkopM0wFoopKAFpM0UUAFFFFABRRRQAUUUUAFFFFABRRRQAUUdqKACjNFFAgoppdB1ZR+NMNzAvWaMfVxQMloqu19ar1uI\\/wbNRnVbIf8tv\\/HT\\/AIUroLMuUVROr2Y\\/5aE\\/RTTTrNp6v\\/3zRzILMv0tZ39s2vo\\/5Uf21bekn5UcyCzNGis3+2rb+7J+VH9tW\\/8Adk\\/KjmQWZpUVm\\/21bf3ZPyo\\/tq29JPyo5kOzNKis7+2bX\\/b\\/AO+aUaza+rj\\/AIDRzIVmaFGaojV7M\\/8ALQj\\/AICaUarZ\\/wDPb\\/x0\\/wCFF0FmXaKrLqFo3SdPx4p4u7Y9LiL\\/AL7FO6CxMKDUYliPSRD9GFPDKejA\\/jQAtFFFABRRRmgBKMcUtFACUUtFACUZoxR3oAKKWjFAgooxRQAUUUUDDFFFFACd6KWkNABS0lGaQBRS0lAgpaTFFMYUZooxQAtJRRQAUvWkxRSAKKKOKACijGKM0AcfRRRWJqFFFFABRRRQAVsaPZ5\\/0lx7IP61n2dsbu4WMcL1Y+grqEUIgVRhQMAVcF1Jk+gtABJwOtKqljgVYRAg9\\/WrbsQlcSOMJyetSZpKQ1JQpNJSUmaADNITRSE0xBSE0ZpCaADNJRSZpiFpM0ZpKACiikpgLSUUUALSUUUCCiiigYUUUUAGaKOKglu7eH78yA+mcn8qAJ6Kzn1m1X7u9\\/oKqya5If8AVwqv+8c0uZDszborm31a7fpIFH+yoqs880n35Xb6sTU86Hys6l54o\\/vyov1YCq76pZp\\/y13H\\/ZBNc1RS52PlN5tbtx91JG\\/CoH11v4IAPctmsiilzMOVGi2tXJ6CNfoKibVbxv8Alrj6KKp0UrsdkTteXLHJuJPwYiojI7HJdifc02ikMM5ooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigApQSOhNJRQA9ZZFOVkYfQ08XlyvS4l\\/wC+jUNFAFsapeD\\/AJbE\\/UCpBrF2O6H6rVCindisjTGtzjrHGfzqVdd5+a3\\/ACf\\/AOtWPRT5mHKjeXW7c9Y5B+VSLq9oersPqtc7RRzsXKjqE1C0fpOg\\/wB44\\/nUyzRvysiN9GFcjRT5w5Tscg0CuQWWRfuuw+hq1b3F877YpZGPucgfnT5xcp0tFVIHnSMedIHb6YqXzT6CruSTUVF547ijzk9TQBLRTBLGf4hTgQehBoAWkpaKBCUUtJQAUUUoFACUUYoAoAWik6UZpALSUUUDCiiimAUUUUAFFHFFIDj6KKKxNQooooAKOtFaekWnmzee4+ROme5ppXE3Y0tOtPstt8w\\/eNy3+FXUQscClRC546etWFUKMCtL20I3BVCDApaM0lIYZpKKTNAATSUlITTEKTTc0E0lMAoopKBBSUUUAFJRRTAKKKKACiimu6xqWdgqjuTigQ6iqM2rWsfRjIfRRVCXW5myI41T3PJqXJIpRZu1XlvraEkPMuR2HJrnJLqebmSVm9s8VDUuY+Q3ZdbiXiKNnPqeBVKTWbp\\/ubU+gz\\/Os+ik5MrlRLJcTS\\/fldvqaiooqRhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFSQwSTthFz6nsK1baxjhwzfM\\/v2ppXE3Yp2unPNhpMqn6mtaKJIUCooApc0ZrRKxDdx2aQtTc0maAFzSE00tTSaAFJppbFITTCaQEnmuOjkfjSi7mX+PP1FQE0wmi47FsahIOqqakXUlz80ZH0Oaziamjix8zdfSi7CyNMXMRGckfUU8TRn\\/lov4nFZpNITTuKxrgbhlSCPY0uxv7prFzjpThPKv3ZGH40cwWNfaf7p\\/KkK+1Zq6hcp0lyPcA1KurTD7yIfwo5gsXcUlV11kfxw\\/iGqddXtW6h1+q0cwWHUVIuoWbf8tlH14qeOWGX\\/Vuj\\/wC6QaXMHKVKSr+B6U7FHMHKZ2D2BpQjn+E\\/lWhS0cw+U89ooorMsKKKKAJIYWnmWNByxrrLW2WOJY0GFUYzVDRLDbF9ocYL9PYVtgADAHFWtCXqAAUYHSkzRmkzTEFJmgmkoEGaQmkNJmmAuabRRTEFJmjNJQAtJRSUAFFFFMAoqrPqFtADuk3MP4V5NZs2syuCIUCD1PJpOSQ7M22dUUs7BVHcnFUZtWtouEJkb\\/Z6fnWFJNJMcyOzH3NRk1Dn2GomhPrFxJkR7Yx7cmqLyvKcyOzH3OaZRUttlJIKKKKQwooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooqxBaSTnONq+poAgALHAGTV6305mw03A\\/u96uQWscA+UZb+8anzVqPclyERFjUKgAFOzTc0maokdmkzTc0hNADiabmmk0hNIBxNNJppamk0DFJppNITTCaQCk00nNJmp4otvzN1oGLFFt+ZuvpUhNITSE0xATSZpKQmgAJpCaCaaTSACaaTS00mgYUmaKQ0ABNJRSUgJFuJk+7K4+jVKup3ifduG\\/HB\\/nVSigDTTXbtcBhG31Wpl8QuPvW6n6NWLSGgZWoooqRhV\\/S9Pa9nBYYhXlj6+1RWdm1y+TkRjv611ltAtvAqKAPXFNITJQAoAAwBRmjNITVkhSE0hNITTEFIaKQmgAJpKKKYgpKKSgAooqGa5hgGZHA9u9AEtNeRIl3OwUepNZFxrDMCsCbR\\/ebk1mySyStukcsfUmpc0NRNmfWI0JEKlz6ngVmT31xcZ3yEKf4RwKrUhPaocmy0kHWiiikMKbSnmigBKKKWgApKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoopVVnOFBJ9qAEp8cLythFzVyGw6NKf+AirqqqDCgAe1UoibK8FikfzSYZvTtVvOKTNJmq2JHZozTc0maBDs0hNNJpuaAHlqaTTSaQmgYuaQmmk0hakApamlqaTTSaBjiabnJwKTknA5NWYognJ+9QARRbfmbrUhNBNJmmICabmgmkJoAM00mgnNJnFIAppNKTTc0DCkoNJQAE0lFIaQBSUUlABSUUUAIaSiikMr1ZtLRrh8niMdT60Wlq1w\\/og6mtqOMDbFGMDoBSGWbGBRghcInAHvV\\/NMRBHGEHQUuatIhsUmm5oNJmqEBNIaQmkpgGaKKSgQZpKKgnu4bcfO3P90daAJ6gnu4bcfO\\/zf3Ryay7jU5ZCRH+7X261QJyeetS5dilEvXGqyycRDy19e9Z7MWJJJJPUmkJpCahsqwE0lFBOKQCE+lFFFABSGlpKBhSUUUAFFLSUAFFFFABRRRQAUUUUAFFFOCOeik\\/hQA2ipBbzHpE5\\/4CaeLO5PSF\\/wAqLAQUVZGn3R\\/5Yn8xTxpl2f8AlmP++hTsxXRToq7\\/AGVdf3F\\/76FL\\/ZV16L\\/31RZhdFGir39k3Xon\\/fVH9k3Xon\\/fVFmF0UaKvf2Tdf3V\\/wC+qT+yrv8AuD\\/voUWYXRSoq4dMux\\/yz\\/8AHhTTp12P+WJ\\/MUWYXRVoqwbK5H\\/LB\\/yphtpx1hk\\/75NKw7kVFPMUg6xsPwppUjqCKAEooooAKKKKACiiigAooooAKKKKACiiigAoooAyeKAClAJOAMmrEVm74LfKP1q7HDHEPlXn1ppCbKkNizYMh2j071ejjSIYRQKXNGapKxLdx2aM03NJmmA7NJmm5pM0AOzQTTM0m6gB2aQmmk00tSAfuppamk00mgY4tSE00mmk0gHE0gyxwOtIoLtgDJq3HGIx6nuaEARxBBk8t608mjNNNUIUmkzSZpCaAFppopM0hhnFNJoNJQAE0lFJQAUlFFIBKQ0GkoAKSlNJQMKSikpAFJQaSgDbVViQIgwoq9ZRYBlbvwKpxIZpQg\\/H6VqgBVAAwBTigbFJpCaM00mrIFzTc0E0lMAoopjyLGpZ2AA7mgQ41FNcRwLmRgPbuaz7jUyflgGB\\/eNZzuzsWYkk9zUuQ1Eu3GpyPkRfIvr3rPZiTknJpCaaTUN3LsBNITSE0lABSUUUgCk6mgnJooGFITS02gApaSpUt5pPuxsfwoAipauJpkzfeKr+tWU0uIffdm+nFPlYroyaUKT0BP0rdjsrZORGCffmrCqqj5VA+gquUXMc+lpcSfdib8RirC6Vct1Cr9TWzmkzT5ULmZmLo7fxTAfQVMukwD7zuf0q9mjNPlQrsrLptqv8BP1NSraWy9IU\\/EZqTNGaLILgEjXoij6CnDHam5ozTEPzRmmbqXNADs0ZpuaM0APzRmmZpc0AOzRTc0ZoAfRmm5ozQA7NGabmlzTAdmjNNzRQA7ikIU9QPyoozQA1oYW+9Eh+qio2sbVusCfgKmzRnikBVbTLQ\\/8ALPH0JqNtHtj03j6Gr+aM0WQXZmNokZ+7Mw+ozUTaI\\/8ADMp+orYpaXKh8zMFtHuR0KH8aibTLtf+WWfoQa6PNFHIg5mcs1rOv3oZB\\/wE1GVZeqkfUV1tIVB6gH6ilyD5jkqAMnArqWtYH+9DGf8AgNMFhbLysQB9qXIx8xgx2jvy3yj9auRwxxfdHPqa0jZRnoWFMax\\/uyfmKfKLmKmaM1YNlKOhU1G1rOv8GfpRYLkeaTNDRyL1Rh+FNOe9ADs0mabmk3UgHZpM03NJmgY7dSE03NNJoAcTSE03NITSAcTTSaTNNJoAcTSorO2AKSNGkPHT1q4ihFwtABHGI1wOvc07NJmkzVCFpM0ZpM0AGaSimk0hik00mgmkoAKSikoAKSiikAUlBptABRRSUAFJRSUDCkNKaSkAlJRRQB1VlF5cW8\\/eb9BVkmikq0iWwNNJoJpKoQUhOKinuo7cfMct2Udaybi9kn4J2r\\/dFJuwJF241FI8rF87evasuaeSZt0jE1GTTSahu5SVgJppNBNNJpDAmkzQaSgBKKKSkAUhPanqjNwqkmpks3blyFosBWpyoznCqT9BV+O1iXkjcfepxgcAAD2quUVygtjK+N2FHvVhNPiH3mLfpVjNLup2QrsEhijHyoo\\/CpQai3Uu6mIlzRmos0u6mBLmjNR7qN1AEmaM1Huo3UASZozTN1JuoAkzRmo91G6gCTNLmo91G6gCTNGaj3Uu6gB+aXNR7qA1AEm6lzUeaM0ASZozTM0ZoESZozTM0ZpgSZozUeaXdQA\\/NLmo80uaAH5pc1HmlzQA\\/NGaZmlzQA\\/NGaZmlzQA7NLmmZpQaAHZpc5pBS5pgLRSZozQIdRSZozQAtFJmigBaKSigBc0hVT1UH8KWigCJraF+sa\\/hxUbWEB6Bh9DVnNFKwyk2mofuyEfUVC2myfwyKfrxWnRRyoLsx2sLgdFB+hqFradesTfgM1vZopco+Y5xlZeqkfUUwmulIB6jNMaCFvvRKf+A0uUfMc5mpIojIcnha2m0+1brEB9Dil+wxYwMijlYcxnqAowBxRmrpsR2c\\/iKYbGTsymizC5VpM1ObOYdgfoaYbaYdYz+FFgIs0lOaNx1Vh+FMNIYZpKKSgApKKKACkopKQBSUGkNABSUUlABSUtJQMSiikpAFJQaSgApKKKAOzJpuaCar3F3HAME5b+6K0IJmYKpZiAB1JrOudRPKw8f7RqpPcyTtljx2A6Cq5NS5DSFZixJJJPqaYTQTTSaRQE00mgmmk0gAmkNGaArMeBmgBKTrVhbfux\\/AVMqqg4GKdguVkt3brwPerCW0a8n5j70\\/NGaLIQ4YAwOKXNMzRmmIfmlzUWaXNAEmaM1HmjdQBJml3VFuo3UAS7qN1R7qN1MCXdS7qizRuoAl3Ubqi3UbqAJd1G6ot1G6gCXdRuqLdRuoAm3Um6o91G6gCXdRuqLdS7qAJQaN1RbqXdQBLuo3VFupd1AiTdS7qi3Uu6gCXdRuqLdS7qAJd1G6ot1LmmBLmjNR7qXdQBJmlzUW6l3UASZpc0wZNOFADs0oFICKM0xDxS5pmaXNADs0uaZmlzQA7NFNzRmgB9GabmjNADs0uaZmjNMB2aXNNzRQA7NLmmUoNADs0U3NGaAHZopuaM0AOopM0UAOopKXJoAM0ZozRQIXNFJRQAtFJRQAtIVU9QKKKAGGGI9Y1\\/Kmm0gP8AyzH4VLRRYZWNhAf4SPoaadNiPRmFW6KVkF2UTpidpG\\/KmnS\\/SX9K0KKOVBdmYdKftIv5Uw6XN2ZD+Na3NHNHKguzHOmXH+yfxpp065\\/uj862qKXKh8zMM6dc\\/wBwfmKT+z7n\\/nn+ordo5o5UHMYJ0+6\\/55H8xSf2fdf88j+db9FHIg5mc\\/8A2ddf88v1o\\/s26\\/55\\/qK6CijkQczMAaZdH+AfmKX+yro\\/wr\\/31W9RRyIOZlO51DqkP\\/fX+FZzMSSScmkJppNQ2XYCaaTQTTCaAFJppNITQFLdKQCE0BWboKlWMDryafmnYBiwqPvc1KOBxTc0ZoEOzRmm5pM0APzRmmZozQA7NGaZmjdQA\\/NGaZmkzQBJmjNR5ozQBJmjNR7qM0ASZo3VHmjdQBLuo3VHmjdQBJuo3e9R7qN1AEm6jcaj3UbqAJN1G6o91G73oAl3UBqj3UbqYEmaXdUW6l3UAS7qN1RbqM0AS7qXdUW6jNAE26jdUW6jdQBNuo3VFuo3UATBqXdUYye1KB6mgQ8NThk9KaMU4NTAcB6mnjAqLdS5oES7qXNRbqXdTAlzRmot1LuoAlzRmo91G6gCXdRmo91LuoAkzRuqPNLmgB+aXdUeaM0xEu6jNR7qM0ASZpc1FmlzQBJmjNM3UZoAkzRmo80uaAH5ozTM0o5oAeOad0poopgOozSUUAOzRmm5ozQA6im5ozQA6ikzRmgBeKKTNGaAFopM0ZoELRSUUDFopKM0ALS02igQtFJRmgBaKTNGaBi0ZpM0ZoAWikzRmgQtGabmjNAzBJppNBNNzWJoBNJyelKF9adQAgQDrzTs02imA7NGabmjNAh2aTNNzRmgB2aTNNzRmgY7NJmm5ozQA7NGaZmjNIB+aTNNzSZoAfmjNMzRmgB+aM1HmjNAEmaM0zNJmgCTNGaZmjdQA\\/dRmmZo3UAPzS7qjzRmgCTNGajzRmgCTNGajzS5oAfmlzUeaM0wJM0ZpmaMmgCTdRupopeKBDs0ozTQaXdQA8CnA4qPdRmmBNupd1Q7qXdQBLupd1RbqN1AE26jdUW6l3UCJd1LuqENS7qAJd1O3VDupd1MCXdS7qh3Uu6gCXdS7qh3Uu6gCXdS7qhDUu6gCbdRuqLdRuoES7qXdUW6jdTAm3Ubqi3UbqAJt1G6ot1LuoAlzRnNRAknipVGOvWgB6j1p4NMzRmmIfmjNMzS5oAfmjNMzRmgB+aM03NGaAH5ozTM0uaAHZozTc0ZoAdmim5ozQA7NGabmjNADs0U3NGaAHUZpuaM0AOzRmm5HrRn3oAdmjNNzRmgB2aM03NGaAHZozTcikzQA\\/NGaZmjNAD80mabmjNAGDjPWl6dKTNJmsjQcTSZptFMB2aM02jNAC5ozTc0ZpALmjNNzRmgBc0maTNJmgB2aTNJmkoAdmkzSZFGfakAuaM0maTJoAdmjNNo\\/GgBc0UlFAC5opKKAFopKKAHZpKSigBaXNN59aKAHZozTaOaAHZozTc0uaAFzS5puaM0APzS5qPNLmmA\\/NLmmZozQBJmjNMzRmgCTNLmo80ZoAkzS7qjzS5pgPzS7qjzRmgRLuo3VHmjNAEuaM1HmjNAEu6l3VFuo3UAS7qXdUW6jdQBLupd1RbqN1MCUNS7qi3UbqAJt1Aaot1G6gCbdzRuqLNG6gCbdRuqLdRuoAm3U5ct0piITyeBU4IAwKYh6gLS5pmaM0xEmaM0zNGaAJM0ZqPNLmgB+aXNR5pc0APzRmmZozQA\\/NLmmZozQA\\/NGaZmjPvQA\\/NGaZmjNAD80ZpmaM0APzRmmZozQA\\/NGaZmjNAD80ZpmaTNAEmaTNM3UbqAJM0ZqPdRuoAfmjNMzRmgB+aM0zNGaAH7qXdUeaXNAGJmkzUaNjg9Kfmsyxc0ZptFAC5ozSUlAC0ZpKSgBc0ZpM0maQDqTNJRQMM0UmaKAFopKSgBc0ZpKKAFyaMmkooAKPxoooAKPxoooAWj8aSigAooooAWjJpKKAFyaM0lFAC5ozSUUALmlptLQIWjNJRmgY7NGabmloAdRmm0ZNAh2aXNMz7UZoAfmjNNzRTAfmjNMooAkzRmmZozQA\\/NLmo80uaAH7qXNR7qAaAJN1G6mZozQBJmjNR5pc0ASZozUeaM0AS5o3VHmjNMCXdRuqPNPRC\\/sPWgBwJY4FTpGF5PJpEUKOKfmmIfmlBqPNLmmIfml3VHmlzQA\\/dRmmZpc0APzRmmZozQBJmjNMzRmgQ\\/NLmo80bqAJM0Zpm6k3UASbqN1MzRmgCTdSbqZmk3UASbqN1R7qN1AEm6jdUe6jdQBJmk3VHuozQBLupN1R7qM0ASbqN1R7qM0ASZozUe6jNAEm6jNR5pc0ASZozUeaN1AyTNLmowaXNMRhU5WxwaSisjQfRTQe1OoASikJooELn0pOaKTNAxaM03NFAC8mkpaKAEooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigBaKSigAxRS0UAJS5opKAFzRmkxRQA7IoptFADqKbS0ALRmk5pOaAHZpc+1NzRmgB2aM03NGaBDs0ZpuRS0DHZozTaKBDs0ZpvNGaAH5oHJ4oRC54HHrVhIwnTk+tNAJHF3b8qnBApmaWmIfmjNMzS5pgPzRmmZpc0APzRmmZpc0CHZpc0zNGaAH5ozTfxozQA\\/NGaZmjNAD80ZpmaM0APzRmmZpM0ASZozUeaM0ASZozUeaM0APzRmo91G6gCTdSbqZuo3UDH7qN1M3Um6gRJuo3VHml3UDH7qN1R5o3UASZpc1GGozQBJmlzUYNLmgQ\\/NLmmZpaYDwaWmZpc0AY+aKMUYrMsKUGm0ZoAdRSA0tACUlFLQAlLSUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUYNLigBKKXaaXbQA2inbPejZ70ANop\\/l+9Hl+9ADKKk8v3o8r3oAjoqTyj6ijyT6igCOipPJb2o8l\\/QfnQBHRT\\/Kf+7SeW\\/900ANop2xv7p\\/KkwR2NACUUUUAFFFFABRRRQAUuaSigBc0UlFAC0UlFAC0lFFAC0lFFAC0lFFABS5pKcqF+nSgBOTUyQ92\\/KnpGqfX1p9NIQo4GBS5ptLTELmlptLmmAtFJmjNADqKTNGaQC0UmaKYC5pabRmgB2aM03NGaAHZozTc0ZoAdmkzTc0ZoAfmkzTc0ZoAdmjNMzRmgB2aM03NJmkA\\/NGaZmjNADs0ZpmaM0APzRmmZozQA\\/NGaZmjNAD80uaZmloAdmlzTM0uaAHg0oNMpaYD+tKDTQaUUAOzS03OOtQvc4OF596AKVFFFQUFIRS0UANpQaWm4oAcabRS0AJRRRQAUUUUAFFFFABRRRQAUUUUALQKKKAHUlIDTqAEBxTqbRTAdS03NLSAWlptLTAWlzTc0tAh2aXNMpc0APzSg0zNLmgB49qXNMzS5oAfmlzTAaXNADsA9QKTy0P8I\\/KgGlzQAnkRH+GkNtGfUfjT80oNAEJtF7MaT7Gezj8qsZpc0WAqGzk7FTTTazD+DP0NXgacGosFzNMMo6o35Uwqw6gj8K1gaXNFguY9Fa5jjbqin8KabaA\\/wDLMfhRYLmVRWmbGE9Nw+hph05T92Qj6iiwXM+irp01+zqfrTGsJx0Ab6Giw7lWgAk4FWBZT94yBUghMY+4R9RRYVyFIu7flUo9KKKYhaKKKAFopKWgBaKSigBaKKKYBRmiigAzRmiikAZozSUUALmikzRmgBaKbmjNADqTNJmkoAdmkzSZozQAuaKbRQMWjikzRmgBc0ZpuaM0ALRTc0uaAFyaXNNpRQAuaKSigQtLRRQA6ikFLQA6ikpRTAcKRnVBkmopJwvC8mqzMWOSc0rjsSyTM\\/HQelR5pM0lSAUUUUAFFFFAwooooAbS0tNoAWkpaSgAooooAKKKKACiiigAooooAWikpaACgGiigB2aKZTgaACjNLmigAzS03FFMB1FJmlzQAuaXNNooAdS5puaM0CHZpc03NGaAH5pc0zNLmgB+aUH3pmaM0ASZpc1HmlzQBJmlBqMGlzQBJmlzUeaXNAEmacGqLNLmmBMGpc1FmlDUCJQacDUQOakVfWgB4yelSKAOtMBFOBpgSA04GogaXdQIkKo33lB\\/CmG2gb\\/AJZr+FKDSg0ARmxgP8JH0NMOnRn7rsPrzVndS5oApHTG7SD8RUZ06YdCp\\/GtMGlDUWC5kGzuF\\/5Zk\\/SozDKvWNh+FbgNLmiwXMAgjqCKSugOD1ANMMMLfeiQ\\/hRYLmFRW0bK2P8AyzA+hph063PQMPoaLDuZFGa020uM\\/dkYfWozpTdpR+IosFzPoq6dLm7Mhph065H8IP0NICrmkqc2dwvWJvw5phhlXrE4\\/wCA0AR0UpVh1Uj8KbQAuaSikoAWjNJSUDFzSZoooAKKKSkAtFJS0AFFFFABS0lApgOFFIKWgBaBRS0AKKWk6VE84HC8n1oAlZwgyTUEkxbgcCoySxyTmkpXCwZozSUUhi5NFJTgjnorH8KAEpaUikxQAUUlLQIKKKKBhSGlooAaKWkPWloASiiigAooooAKKKKACiiigAooooAWikpc0AFFFFACUuaKKAFBpaZRQA+kpM0uaAClzSZooAXNLTcUUwHUUmTRmgB2aM0maKAHZpc0ylzSEOzS5pmaXNMB+aXNR5pc0ASZpc1HmlzQBJmlzUYNPUE9aAHA1Iqk9aaoA6U\\/NMCQYHSnA1FmlzTES5pQ1Rg0uaAJA1OBqLNLmgCXNODVCDTg1AiUGlBqLNLuoAmBpd1QhqduoAlBpc1EGpd1AEuaXdUW6jdTAl3Uu6ot1LuoAkzS5qLNLu96AJM0uaiz70uaAJM0ZqPNLuoAfkd8UhVD1RT+FN3UbqAA28DdYk\\/75phs7Y9YV\\/CpM0bqAIDp9qf+Wf6mmnTLY9mH41Z3UuaQFM6Vbn+Jx+NIdJh7SP8ApV3NG6iyC5QOjp2mb8qb\\/Y47Tf8AjtaOaM0WQXM06Oe0w\\/75pP7Hb\\/nsv5VqZozRZBcyv7Ik\\/wCeq\\/lR\\/ZEn\\/PVK1c0Zp2QXMr+yJP8AnolL\\/ZEn\\/PRK1M0ZosFzL\\/smT\\/nqv5Uo0l\\/+eq\\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XtRTEFJQKDQAUlLSUgCgUUUADdKaKeelMoGLRSZpKAA0UUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUCjtRQAU6koFAC0UUUAJTgcikNAOKAFNFLSUCCiiigBaKKKYBS0lFABRmlooAKKTFFAC0UlFAC0UlFAC0UUUAFJS0ZoAKKSigAooozQAUmaKKQBRRS9KAEoJxSbqSgYpOaMUUUAFFFFABSZpDzS0AJS0UlABRRiloAKSiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAJqSlopiEoNFFACUhpaaWFIBaQnFITmkoGKWJpKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAWgUUd6AFooooAKSlpDQIcDmikBp1ACUUUUAFLSUUALRSZpaACiiimAuaKSigBaKSigBaM0lFABmiiigAopM0UgFpM0UUAFFFGKAClpMgUhOaAFyKbRS0AGKKKKACiikJoGGaOtJS0AFJRRQAUtFFABSUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFABPQUAFFSLBK3RD+NTLZOfvMBTsFyrRWgtnGOuWqVYo16IPyosK5RopTgUwv6UAKaYW9KQ5PekwaQATmjFFFAwxSUtFACUUtFACUUuKTFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQACiiloAB0paQUtABRRRQISnA8U2igY+kpRzRQISiiigAoopcUAJmlzRijFABRSYNFAC0UlFAC5pM0UUAFFFFABRRS4oASlxSZApCaAFyKQmkpaBiUtFFAgooooGFITQTSUAL1oopKAFpKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAopQpPQGnCGQ\\/wmgBlFTC2kPXA\\/Gni09X\\/IU7BcrUVcFrGOpJqQQxr\\/APxosK5ngE9BT1hkbohrQAA6AClp2C5TW0kPXAqRbNf4mJ+lWKXNFkK5GttEv8OfrUqqq9FA+lJmjNMB1FNzRmmIdRSZozSAzTzSYp2KMVJYzFFOooASkwKXFGKQCYpCKWjNACYopaMCgBKKXFJtoAKKTpRQAtGKM0UAJiilooASiloxQAlFFFABRRRQAUUUUAFFFFABRRRQAUtJRQAUtJRQAU6m0ooAWiiigApKWigQgOKeDmmUCgY+kpc0UCEpc0UlAC0UlLTAXNFJRQAtGBRQDQAYFG2ilzQAm33o20uaM0AJg0hDU6igCPafSjafSpaKAIsH0oqXNFAEVFTUUAQk4pvWrGB6UYHoKLBcgoqfC+gowvoKLDuV6KsbV\\/uil2r6CiwXK1FWdo9BRgegosK5Woq1gegpeKLBcqYpcH0NWqKLBcq7W\\/un8qXy3P8Jq1mjNFguVvJf8Au0vkP7VZzRRYLkAt29RSi29W\\/Sp6KdguRC2XuTThbx+hP40\\/NLmiwhohjH8IpwVR0UflRRQA6ikopgLmjNJmjNADqM0maM0ALmlzTaM0AOBozTc0ZoAdnFLmmZozQA\\/NGaZmjNAD6MimUZoAq4oxUhFNxUjGYpMU\\/FGKAGYpMU\\/FJigBtGKdikxQMbijFOxSUANozTqMCgBtGBS4pMUgDFIRS0uaAG80lPpMUAJmijFFABRRmigAxSUtFACUUUUAFFFFABRRRQAUUUUAFFFFAC0nQ0tIaAHUUgpaACiiigApKWigQZpc0lJigY7NFJzRmgBaKM0UAFLmkpaBBRSUUALmikpaYC0UlFAC5opKKAHUUmaM0ALmjNJRQA6jNJmigBc0uabmjNADqKbS5oAWjNJmlzQAuaM0lFADs0U2jNMB1Lmm5ozQA6ikzRQIWlzTaM0AOzS5puaKAHZoptGaAH0U3NGaAHZozTc0uaAFzS5puaM0AOzRmm5ozQA7NGabmjNADs0ZpuaM0AOzRmm5ozQA7NGabmjNADs0ZpuaM0ALikxTyKTFIZHigin4pMUAMxSYqTFNxQAzFJinkUmKAGYoxT8UmKAGUYp2KSgBtFOxSYoGJikxTqSkA2inUmKYBmjFJiikAYpMU6jFADaKCKKACkpaDQAlFFFABRRRQAUUUUAFFFFABS0lLQAlOpKTpQA6iiigAooooAKKKKACiikoELRSZooAXNLupKKBi5FFJiigQtFJRQAtFJk0ZNAC0UmaWgBaSjNHFAC5ozSUUALRSUtABS5ptGaYDs0UmaKAHZozTaWgBc0uabRQA7NGabmlzQA7NFNzRmgB1GabmlzQA7NGabmjNADs0uabmigB2aKbmjNMB1GabmjNADs0uabmjNADs0ZpuaM0AOzRmm5ozQA7NGabmjNADs0ZpuaM0AOzRmm5ozQA7NGabmkzQA\\/NJmm5ozQBbxSYqoLuTuFP4U77We6D86VwsWMUmKh+1r3U0v2mM9QRQBJikxTfPjP8X6UvmRn+IUAGKQil3Kf4h+dLkeooAZikxT8Un1oAZikxTyKTFADMUlPxSYoAbSYp2KSgBKSnUlAxKQilooAbRTqaeKAFppFLQaQCUUd6KAEooooAKKKKACiiigAooooAKKKKAFopKcKAG5p1IRSUwHUUmaXNIAooooAKKKKACiiigAooooAKKKKACikzS5FABRRmjNABRSZozQAtFJmlzQAYooooAKMmiigBc+1GQaSigBaKSjmgQtFJk0ufagAzS5pMiigBaKSigB1FNozTAdRmm5pc0ALmjNJmjNADs0ZptFADs0ZptLmgB2aM02jNADs0ZptGaAHZozTc0ZoAdmjNNzRQA7NGabRmgB2aM03NGaAHZpM03NGaAHZozTc0ZoAdmjNNzRmgCOijFGKQwooooAKKKKACiiigBcn1NLvb+8fzptFADvMf+8aPMf1ptFAD\\/Mf1o8xvWmUUAP8AMak8xvam0UAO3n2o3mm0UALuNG40lFAC7jRk0lFABmiiigAooooAKKKKACiiigAooooAKKKMUAFLRSUAFKKKKAFpCKWgUANxRTiKSgBM0uaKMUAGaM0YoxQAZpKWigBKWiigAooozQAUUmaXNABRSUUAFFFLQAlFLRQAUUYpKAFozSUZoAdmkzSUUAOzRTaKAHUUn40ZNAC0UmaM0ALRk0mRS0AGTRmiigQZpcikooGLRSUUCFopKOfWgBaKTn1oyaAFopMmjJoAWikyaM0ALmikyaMmgBaKTJo5oAWikyaKAFopOfWigBaKSigBeKMikooAM0Z9qKMigY2inUmKAEzS0YoxQAUUlFABRS0UAJRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAC0UUAUAJSgUtFABRRg0uBQAlKKWkoELTSKcKXFAyOilZccjpTaAFopKKAFopKKADNFFFABRRRQAUUtFABRSZooAXNFJRQAtJmiigBc0lFFAC5pKKKACiiigAooozQAUUZooAKKKKACijFFABS80lFAC5NGaSigBc0ZpKKAFzRmkooAXNGRSUUALkUZFJRQAuRRkUlFADsikyKSigBc0ZpKKAFzRmkooAXNGaSigBcmjJpKKAFpPxoooAKKKKAH4FJin4pMUCG4NFOxRigBtGKXFFAxuKMUuPejpQAlJTqTFAhKKXFGKBiUUuKMUAJRS4oxQAlFGKWgBKKXmjmgBKKKXFACUUuDRigBKKXFGKAEopcUYoAKKWigBMUYpcUYoAKKXpS0CExRS4pcUANpcUuKXFMBtLilxS4oAbS0uKMUAJio2XHPapgKMAjFAFeinOhU+1NxSGFFLiigBKXFFFABRSUUAGaKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKALGKTFSYpMUxDMUmKk20mKAI8UYqTbSYoAjxRin4pCKAGYowKdRigBuKTFOxRikA3FHNOxRigBuDRg07FGKAG4op2KMUAN5op2KTFACfhRTsUYoAbRTsUYoAbzRinYoxQA3FLilxRigBMCinYoxQAmKMU7FGKYCYoxTsUYoATFGKdilxQA3FLilxS4oAbRTqMe1MBtLinYoAoATFLgUEge9JyaQARuGKgZSpwasgUrRh1wRQBTzSU50KNg02kMKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAv4pNtPOBSEmrJGbaQinEE0m2kA3NNNSbaTFAEeKMVJtpNtAxmKTFSYoxQBHijFPxRikAzFGKfijFADMUYp+KMUAMxSYqTFG2gCPFGKkxSYoAZijFPxRigBmKMU\\/FGKAGYoxT8UYoAbijFPxRigBmKXFOxS4pgMxS4p2KMUANxS4p2KMUCExRinYoxQA3HtRTsUYoAbS0pIXrTC5PTigYpIFISTSAU4CkAgFPAoApwFAABTwKAKs29q87cDC92NAFdoPOG0DJrOdCjYNdFdhLS3EMf336nvisuSASr7+tDBGfRSspRiCMEUlIYUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAGntpNtTbaTbVkEW2k21NtpNtAyLFJipsUm2gCLFJtqXb7UFfagCLbRtqXbxSbaAIttG2pdtJtoAj20bak20baAI9tG2pNtG2gCPFG2pNtG2gCLbRtqXbSbaQEe2jFSbaMUARYoxUmKMUAR4pdtPxRigBmKMU\\/FLigCPFLin4oxQAzFGKfijFMBuKMU7FGKQDcUYoZ1XqefSoWmJ6cUASkhepqMyE9Bio+tOApXGHU80oFKBSgUAAFOAoAp4FMAApyqScdalht3mbag+p7CtW2s0g5+8\\/qe30oSE2VrbTy2Hm4H92tL5Y07Kqj8qUVR1KbaghU8ty30qtkLcoTyGedpD36D0FIooVakVazLILi1EyZXhx096y2UqxBGCOorfJCKWY4A5JrFuphPMXAwOgoAhooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKANzbRipdtJtrUzIsUbal20m2kBFtpNtS7aNtAEW2jbUu2k20DIttG2pdtG2gCHFG32qXbRtoAi20bal20baAIttG2pNtLtoAi20bak20baAI9tJtqbbSbaAIsUm2pdtG2gCLbRt9qk20YoAj20m2pMUYpAR4oxzUmKMUAR4pce1PxUbyxp1YZ9BQAY9qDgdeKrvdk\\/cXHuagZ2c\\/MSaVx2LTzovA5qBpnb2HtUdLSuMKUCgCnAUAAFOAoApwFAABTgKAKngtpJ2wi8dyegpgRhav22ns+Gl+VfTuat29nHBgn5n9TVmqUe5LfYRI1jUKgAA9KeBRilxTENdhGhdugGaxZHMsrO3UmruoTZIhU+7VTVaiTLSBVqQAAZNCis++vN2YYzx\\/ER3qRkd7d+c3lof3Y\\/WqdFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAdJijFSYpMVsZEeKMVJikxQAzFJipMUbaQEe2jFPxRigZHtoxUmKTFADNtJtqTFGKAI9tG2pMUYoAj20bakxRigCPbRtqTFGKAI9tJtqXFGKAIdtGKlxSYoAi20YpXkjT7zqPqarvfwL93LfQUhk22jFUH1Bz9xQv61XeaST77k+1LmHY0nnij6uPoOarPe\\/3F\\/E1Topcw7EjzyScFjj0FMo6CipAKWilpgFLiinAUAAFOAoApwFAABT1QsQACSewq1bWEs2Gb5E9T3rVgto4Fwi89yetUlcTZSttN6NN\\/3yK0lVUUKoAA7Cloq0rE3CnAUAUtABimyyCKJnPangVn30u+Ty1+6vX61LY0ioSXcs3JJyaeooC1VvbsQjy0P7w9fasyxl9ebAYYz838RHasyjrRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQB1WKMU7FGK2MhuKTFOxRigBuKMU6jFADMUYp+KSgBuKNtO4HemmSNerqPqaADFJimNdW69Zk\\/76qNtQtV\\/wCWoP0FF0OxPijFVG1S2HQsfoKibWIx9yJj9TildBZmhijFZTavIfuxKPqc1E2qXLdGVfoKXMh8rNrbSMyr95gPqa597qd\\/vSv+dRFiepJ+tLnDlN5722TrKD9OarvqsI+4jMfyrIopczHyovvqkrfdRV\\/Wq0l3PJ96Q\\/QcVDRSux2AknrzS0UUhgKWiigApQO9IOTS0CClpKcKAClAoApwFMAApwFSQW8s7YjUn1PYVq2+mRx4aX529O1NK4m7GfBaS3B+ReP7x6Vq29hFB8x+d\\/UirQAAwBgUtWlYlu4UtFFMQU4CjFLigYAUooAp1ICG4l8mEt\\/EeBWWBk5PNWLmXzZePurwKpXV0tsmBzIegrNu5aQ28uxbrtXmQ9PascksSSck96GZnYsxyT1NJSGFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAE32u4H\\/LaT\\/vql+2XP8Az3f86gop3CxP9tuf+e7\\/AJ0fbbn\\/AJ7v+dQUUXYWJ\\/tlz\\/z2f86T7Vcf89n\\/AO+qhopXCxIbiY9ZX\\/76NIZZD1kb86ZRQApZj1Y\\/nSUUUAFFFFABRRRQAUUUUAFFFFABS0UlABS0lFAC9TS0CigAo6mjqaUDFAB0oopQKBBinAVNBazXB\\/doSPU9K1LfSY0w0zbz6DpVJNibsZcNvLOcRoT6nsK1LfSkTDTHcf7o6VoKqooVVAA7CnVaikS5CKqqoVQAB2FLS4paYgoopcUAGKUCgClpDCnCgClFABUF3L5ce0H5mqwSFBJ4ArEv71YyXPLH7q1LZSRHc3K2yernoKxXdpHLuck0SSNK5dzkmm1BQUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRS0AFAFA5paACiinRxPK2ERmPoBQIQDAorSg0eV8NMwQeg5NacFjbwY2oCw\\/ibk1Si2JyRi2+nXE+CF2L\\/ebitW30qCHl\\/3je\\/QfhV6irUUiW2AAAAAwB0FLRRTEFLiiloAKKKcBQAmKXFLS0hhilAoApaQBS0uKr3t2lnbNK3JHQepoGVdVv0to9mcue1cxLK8zl3OSaWaZ55WkkbLMajrNlhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUtABSUUoBJwBk0AFJ1q3Dp11P0jKj1bitCDREXmaQt7LxTUWxNoxgMnAGTVuDTbmfnZsX1bit6G1gt\\/wDVxqp9e\\/51NVqHcnmM2DRoU5lYyH06CtCONIl2xoFHoBTqKpJIm4UUUtMAoopcUgCjFLRQAUYpQKXFABiloxS4pDClApcUoFIBMU4UYpryBB6mgYO4Qe9crq16bm42K2Y04+prT1W9MEGFP71+B7D1rnKUtNBruFFFFQUFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUU9IpJPuRs3+6M1bj0m7fBKBAf7xp2bC5RorZi0MdZpifZB\\/WrkWmWkXPlBj\\/tc01Bk8yOdSJ5DhEZj7DNXYtIupMFgsY\\/2jzXQBQowoAHtS1SghczMuHRYVGZXZz7cCr8NtBAP3Uar745qWiqSSJbbCiiimIKKKKBhRS0UAFFFLSAMUtFLigBKUClxS4pDDFLRSgUAAFLRS4pDDFKBRUcku3hetACySBeB1qrJIERpHOABkk07vWLrN3lvsyHgcuf6U\\/hQt2Z11cNdXDSN0PQegqGiisjQKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACitNdEnJ+aSMD2yanXQl\\/jnJ+i4quVi5kYtFb66LbDqzt+NTpplnGciEE\\/7RJp8jFzI5mpEgmk+5E7fRSa6pYYkHyxov0UCn0+QXOc2mlXb\\/8ALML\\/ALxFWI9DlP8ArJlX6DNblFPkQuZmami26\\/eZ2\\/HFWo7G1i+7CmfUjJ\\/WrFFOyFdiAADAAFLRRTEFFFFAwooo7UAFFFLQISiiloGJS0UuKQCUuKKWgAxRRSgUAAFLilApcUhiYpQKUClxQAYpaAKWkMKWjoM1BJLu4XpQlcG7CyS9l\\/OoaKQkKCScAck1aViNyvfXQtLcv\\/GeFHvXMMxZizHJJySas392bu5LAnYvCj2qrWUndmkVYKKKKkoKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAOxooFFbmQUUUUAFFFGKACiiigAooooAKKKKACilxRQAlLRiigAoopaAEpaMUtIBMUtFLigBKUClxS4oGJilxS4pQKQCYpQKXFLikAlLilxS4oGJQSFGT0pHcIOevpVd3LnJoSuDdhZJC59B6UyiirICsnWLzav2aM8n7+PT0q\\/eXK2tu0jdeij1Ncu7tI7O5yzHJNRN9CorqNooorM0CiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA7GiiitzIKKKWgBKKKKAClxRRQAUUUUAFFLijFACUU7FFIBKAKWigAxRRilxQAlLilxS4oATFKBRS4pDCgClFLSAMUYpQKXFAxMUtLilpDEpkkoTgcmmSTdk\\/OoapLuS32FJJOSeaSiiqJCkJABJOAOppaydYvdq\\/Zozyfvkdh6Um7DSuZ+oXhu7gkH92vCj+tVKKKxepqFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAdjS0UVuZBRS4ooASlxRS4pAJRilooASlopcUAJRS4pcUANpaXFGKAExS4pcUuKQxuKXFLilxQAmKMUuKXFACYpcUuKMUhhilAoxS0gEp2KAKZJKE4HJoGOZgoyTVaSUv7D0prMWOSaSrSsQ2FFFFMQUUUjMqKWYgKBkk0AQXt0tpblzgseFHqa5h3aRy7HLE5Jqe+u2u7gt\\/AOFHoKrVlJ3NIqwUUUVJQUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQB2eKMUuKMVsZBRS4oxQAlLilxRigBMUYp2KMUAJijFOxRikMTFLiiloATFGKdijFACYoxS4pcUh2ExRilxSgUAJijFOxRSASlxS4pcUDExQcKMnpSPIsY55PpVV5Gfr09KaVxN2JJJ88J09ah70UVaVibhRRRQIKKKKACsTV73c32aM8D75Hc+lXtRvRaQ4U\\/vW+77e9c2SSSSck1EpdC4rqFFFFZlhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQB22KMU7FGK1MxuKXFLilxQAmKMUuKXFACYoxS4pcUANxS0uKMUhiYpQKXFFACYpcUYpaQCUUuKXFAxMUYp2KMUAJilxS0x5VT3PpSAdwBzUEk\\/ZPzqN5Gc89PSmVaiS2BJzzRRRVCCiiigAooooEFRzzJbwtK54H61ITgZNc5qV6bqbahPlJ09z60pOw0rla4ne5maVzyeg9B6VFRRWJqFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQB3OKXFLilxWhmNxS4oxS0DEopcUuKLgJijFLS0gEopcUYoGJS4pcUYoATFLilopAJiloxS9KAEpGIUZJxUbzgcLyfWoGYsck5ppCbJHnJ4XgetQnmig1drEhSUUUxB3ooooGFFFFABRRVPUL0WkPykGVvuj096G7C3Kmr320G2jPJ++R\\/KsWlJLEknJPJNJWLdzVKwUUUUhhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAd5S0uKMVZImKXFLijFACYoxTsUUAJijFLS0gExRS0YoATFLilxRigYmKXFMaVU75PpUDys3sPSmk2JsmeZV4HJqu8jP1PHpTaSqSsS2FJS0lUIKDRRQAlFFFAgo70UUAFFFIzBFLMcADJNAEdxOltC0jngdB6muYuJ3uZmkc8nt6CptQvTdzcZEa\\/dH9aqVlKVzSKsFFFFSUFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAd\\/ilxS0VRIYooxS4oASilxS0DExRilopAJilpjSqvuaheVm9h7U0mxNkzSKvfJ9KgeZm6cCmUlUkS2FJS0lUIKSg0UAFJRRTAKKKKBCUUlLQMKKKKBBWFqt\\/5rG3iPyD7xHc1Z1W\\/8lTBEf3hHzEfwisKolLoXFdQooorMsKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAPQsUuKMUuKYhKKWml1XqaAFxQcDkmomm\\/uioixbqc01EVyZpgOnNQtIzdTxTaKpJE3EoopKoQUUUlABRRSUAFFGaSgAoopKYBRRSUALRRRQAVT1C+FpFhcGVvuj096lu7pLSEyPyf4V9TXMzTPPK0khyzVMpWHFXGMzOxZiSxOSTSUUVkaBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAeiEgdTio2mUdOahJJPNJVqJFx7SMe+PpTM0UlUIKKKSgApKKKYBSUUUCCkopKACiikoAKKKSmAUUUUCCijNFAwqOaZIImkkOFFOd1jQu5AUDJJrm7++a8l44jX7o\\/rSk7Alcju7p7uYu3A\\/hX0FQUUViahRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAHc0hpaStTMKKKSgApKKKYBSUUUCCkoooASiiigApM0UUwCkpaSgQGjFFFAwpCQASTgDuaWsLU9R80mCE\\/ux95vX\\/wCtSbsCVyPUtQNy\\/lxnEQP\\/AH0az6KKybuapWCiiikAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQB3FFGaStTMKSiimAUlFFAgpKKSgBc0lFJTAWkoooASiiigA70ZozRQAUUVk6nqO3MELc9GYdvak3YErjNU1HObeFuOjsP5CselxRWTdzRKwlFFFIYUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAdvSUUVsZBSUUUAFJRRQAUlFJTAWkoopAFFFJTELSUUUAFFFZmo6j5YMMJ+fozf3f\\/r0m7DSuJqWo+XmCFvn6Mw7e31rEzS4yck0YFZN3NErCUUuPemnikMDSUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAHbUlFFbGQUlFFABSUUUAJRQaKYCUUUUALSUE0UCCiisrUNS25hgPzfxMO30pN2GlcXUdR8vMMJ+f8Aib0\\/+vWLS4orJu5aVhKKWg8CkMQnFMpScmkoGFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQB2tJRSVsZC0lFJTAKM0UUAFFFJQIKKKKBiUtBIAyelY1\\/qJkzFCcJ0ZvWk3YErjtQ1LrDA3szj+lZVGKXFZt3NErCUYpaWkA3pUZOTSu2TgdKbSGFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAdnRRSVsZBRRRTASiiigAo6UlLQAUjMqKWYgKOpNNllSGMvI2FFYV5evdNj7sY6L\\/jSbsNK4++1BrgmOPKxfq1UcUuKXFZvUvYTFLS4oxSASonbPAp0j4+UVFQAUUUUhhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAHZUUUlbmQtFJRQIWkJoooAKhubmO1j3OeT0UdTUV3fJbAquGk9PT61iSSPNIXdiWNS5WKSuPuLmS5k3OeOyjoKhxS4pcVBYmKWlxRQISmSPt4HWlkfYMd6r9TSY0FFFFIYUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAdjRRmitzIXtSUUySRY0LOwVR3NAD6zbzUguY4Dlu7+n0qtd6g8+Ujysf6mqYFQ5dikhDkkknJPelxS4pcVIxMUuKXFLigBuKbI4Qe9OkcRjnr2FVGJY5NJjQEknJpKKKQwooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAOxoorPu9SVMpDhm\\/vdhW7djJIsXN3HbL8xy3ZR1NY1xcyXL5c8dlHQVGzM7FmJJPUmkArNu5aVgxS4pcUuKQCAUuKUClxQAmKbI4jXJ69hSu4jXJ\\/KqbuXbJobGIzF2yaSiipGFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAa11fyXGVX5I\\/TufrVTFKBTgKokQClxSgU4CgBoFOxSgU7FMBuKbI4jXJ\\/KlkkWNct+Aqg7tI2TSbBIHcu2TTaKKkoKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAL4FOApQKcBVkjQKUCnAU7FADQKZLIsS5PXsKWWVYVyevYVnSSNI25jSbsCQSSNI25qbRRUlBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAaoFOApwFKBWhA3FRzTLCvqx6Ci4uFhGOr9hWa7s7FmOSaTY0gd2kYsxyabRRUFBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAG4BVe5uhCNq8v8Ayqw\\/CN9KxGOWJNW3YhIGYsxZjknvSUUVBYUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAf\\/9kNCmVuZHN0cmVhbQ0KZW5kb2JqDQoxMCAwIG9iag0KPDwvVHlwZS9YT2JqZWN0L1N1YnR5cGUvSW1hZ2UvV2lkdGggODE0L0hlaWdodCAxMjEwL0NvbG9yU3BhY2UvRGV2aWNlR3JheS9NYXR0ZVsgMCAwIDBdIC9CaXRzUGVyQ29tcG9uZW50IDgvSW50ZXJwb2xhdGUgZmFsc2UvRmlsdGVyL0ZsYXRlRGVjb2RlL0xlbmd0aCA1ODI4MD4+DQpzdHJlYW0NCnic7J0HW+LMGoY\\/iggqiCAWRJAqiGCX3pHz\\/\\/\\/RSc+UdyaTRljNc3n227PumoBz+7xtJv\\/9d6iKNHb\\/C+VaO45+cG1\\/tpA2DK0prRhaYlowNGdohmrK0ITWmKWR9AFriGuyIt6FoJlg63oV9EL7FbJDCyy3tCyXrmiZeUwLAxSSFElTEpbDpeV0E\\/Q6+x3aGy1iqDBpmQvRwoJlKmgrTFYAWmhYDpaW1CToZfZLJEgKhxY2MgQtEDAe2gqLFTFQ2JhAtjIcDKdr+tUHTQVDsdcwafFGNmiRcGES4zQS85IWV9kKExSKloGkIQqL+ZKDxoKhx6AX2a+RHVo4\\/uKQFucpC+krArRITDighbYVmRYQlnXQWICK3Ae9xn6P3NMinLdYeovjSpgECsWKa1sBSBloGs7WACvrw6Qlvwx6jf0e+UaLWPXYCS1CtkLTAoMCwwJYCkoLBMuh0hKfB73EfpHEabERhAnRIlIOE0ruKVpclYuHcqpCpvSDgUGKpPmGfvHKiwyaDEDHg6BX2G8SixYaGFcpi3+0uKmCMWhhWYoWhjFhOUBaok9BL7BfJQ4tu73SsgBDMX9psQ7CBpQ4sBwgLXc\\/QS+wX6U90CKSsoBJy5zCxTK3F6PFXgEM0wiAxXiZQbNBqbAOen39Lh0KLU4SfBAVz2ihbUWGZcGC5RBpSYflMG\\/lkpaNSCS2V1osE3zRdAWGhf4pgbzIoOkglHgPenX9NgnD4q4m5igQc52yALQwbEXEVwaD8ZKVsqivKWg8CHXDgRePJUaKD7T44y1OgjCRdEWFZcWH5cBoqQW9tn6f\\/KFFoCRmRQs4dBwcLd\\/f32N66Fh7patDpKUQbmnxXOK0\\/GxhYgR8Bcxc+LRApWM+LWI9SSdB2Pe3\\/DEBnAWDZbkMGhBUiWnQS+sXygYtDINxEoU5oMVuB58xQmlpLCAtEixTa1iWQROCKDYOemX9RtmjxaG3WLIikLfQpJCRmB+2otOCwEK+TOQFBY2IqXjYw\\/dDfmX5fGAoVESyfOviMUKMZ0GYGoZBE\\/rYK1seFi3FbdAL61cqmJqYpa84omXqhBYEGRgWWUgDf8uEZbEImhFDJ+E+fF\\/knhaRbcZWNTHaW+b8mpg35TDUVsA4TGUFGQ2DwzCd+aAh0XU6C3pZ\\/VIF4y2W\\/gKXxLhHVljRwg3EAFp0X+GPhiHOcjC0xF+DXlW\\/VQxaAF6Cp8VOTcySFIIWOhDTaYFGww6blmrYw\\/dRYsCI07KWP9zRYlUTsxmJWSb4QN6iwTJe0GEYVQzT7j9oTFTdhhm+nxK1Fzu00OLj4poWbmNSqBwG0jJeWsCyQm4\\/aE4UpcMpfV8lHIzZ2JdvyQpBjHVNzA0tAC8Coy4yLODZLpS1LA6HlsRn0Mvpl8sdLY4aLlAwRiLDz1w4uAAZPg2MCC2DCavNArAi3V7QpMh6DpMWf+WSFhgYa1rsRWKUv9iihSCFSlvA6vFgBuT3QFPygGiJlINeTL9ewdAi1KHkJS+Os3yaFJAWCJY1y1mUGw2alf\\/+y4eDx35rD7SAyARFC+EtoK\\/gbRZOn2WJ3GfQrPwXD48H912+0GLbYERiMZQVF7SMLGgh2yzwbNiStJbAs\\/zYW9BL6Q+IRYsdYtzTQgIDpC3cLB8HRryCDKcs0A58ABb0XhdB7waLNIJeSX9BXtDiR\\/JCj4pZZPnsqUp75vI9GEOw0DkLGobNl0HTUghnKfegfdFi1aG02aL0rt9C0YJuKrauHS80WAKm5TjcLbkP+UeLiL\\/weeEEYy7yluGQ22+BnmSE18MoY5FhCZaW+HfQ6+hvKFha7I5XivQnrWnh9Vtma+hVMWDR7nCxCpqWp7AtuRfZpIVFTOC0TJl5C58WPAobIm0WBixQhh80Lbdh0rIv8XgBkbHjL2Tyws9e7OUu7HBMOG0hchawzQK1JWlYgqQltQh6Df0p+UcLDQzXXzyixaIqxqIFa7MQtOB3S8MSIC3xcLfkXuUNLWITyVbRmOV4pViqz8OFkbeMlkxWIGMxasfqJwKDJdYJk5a9ymbywsQFJMYdLfKjKQRpEQ7GYFqwyrFo6diAJTharsINYPuVTVp4yDgxF6vkxUk0JhiKGbCgTypGX4coLIHRchLCsmfxaLFpMIzkZeMGGE4wxu5TWrUocVrwYpjAVkkMlgBpSQ2DXjx\\/Th7SIpS9WNFiZ2uY0zYlRotcDPuBXgILFjzDD46WSCtMWvYtT2kRKSXbowUAxstUH3+QEW0t+B0it7NYBU5LIXy25N7lhBa30ZjloXyOkxfbqf54gT1CgLxhVhy2wD4RCCzZcAPY\\/mVJC4MXP2mxCsfc0mLiMlnhz9sQjMMWq8BpORoEvXL+qryNxgRosawlk5VkB7mLSJ5vAQs674IlLavgaQmTlqDkcTS2384+mbyw\\/IUGZjTdEK9CLAwjYQmCluuweByUDpAWq1qyg8YLmbyM5xvyVYjRsiRuOICdxqehswQmgdzFdmPfurNvf8M+IxwTbOoTtIwXGxgVKGlBr78k73X\\/tIRH7QUop7TYNxiMFZqX5dLOMZYChWQ2LpPldsuihZW0gM4SxPNbamHxODjtjRbcWKCzk7juYj\\/VZyYvUzm\\/Z9CCGR+7HBYcLbkwDgtUDnkhfzxja48GxjoUW1qFYguclrmdwhiCy2zNZEUYFtN29gxLJtzTErSEeAEeVsFuvDg63sLhjhfslDFrWrT8XrAtiVx9sSLa+wE8vyXeD3qthHJKi620heKFpsVGm1KoqQ\\/RsqBgYewsJq2FdpYAaCmHcVjgssREQcXOBjEYGYFojFNG5iAjVkZW83vqxhnOQhSPGbDsl5ZC2GkJXnZ9xY3B2CkkO6VlyqBlDOT3rNPBCWtZLOkb3DstyXBv8QHIKSyHE46RuMDAzNYALLyz9E1rWVJJy\\/6fDRYNn9NyCHLsLQ6RcZXuE6Vk63BMB2Yib2ZhWwv7PKSF5CxkhBbEs8FKYaclWEkgiCb5XhqMPX8RpIVO9qcoLZPFFrxjOG3B4zDEWYKj5Sx0loAlCIobfxFxFxoZXrpP8oKXk2ewwUyW4N2St8SChWUt+6MlGe4tDl52eGED49pfbKb7PGBAXPSUhUULcRvYRZfYnE5QtIRj+gcgW7R4WRzzdpMYLyCTQjK9Jcm0Fvw28AsSQ234J\\/cFSzaE5QDkDS0OkbEIxxym+yQwk+liw7hLThym7cRXYSGtxbyHPcGSWQe9UEL9zzYtPGJ8NhgKF1GDmS0tYeHQwodlT7TEX4NeJ6FkeUiL7S3IFC42wzEAGYCW2WrLuEUGLBgSECz7p6UcFo8PRt7h4ks9GaPFqkRGuct8zbw\\/a2tRYaEak\\/uOxC7COOxw5MBfvDQYO9mLslSFj1GSYJFSFuxmrWlBgVgRdxSMtxyFY\\/oHJG9pceAvVrwsKYnSstzisIgWxNQLLIn7oa1lH7SEp+kfmJzwwkHGh3BMWansnB\\/GZb7a7qxhYR5NScICWMs+aAmfW3x4OjCDgWix0+KXYVmsZWdhxGHklWlaKFiIitieaEmEsByeHNqLs\\/6+UP8FMJgVpwVD0jJXUhZrWsBoC4GFHhFb7pOWo4+gV0YoWp7TYkGMSP5i+6EvKC9KykLcnzUtxhdeAjdBw+I\\/LZWweHyI8oEW1\\/bi4iEWSsqyY2Yt3Ge1yHU36B7otMV3Ws7C4vGhyqm\\/eJrAkLjYPKlPZUWPwpiwMB4\\/YXzlNRWi6Z\\/DL+UzLEejoNdEKJYc08LDxb6\\/QBP93KoyyctiuYHuTMBa9K+7psaSaVr24S29oJdEKI6c8+LpPL8ALewRsvlitf2xgMUiyV9t2LTsMxK7DuOwA5dzWhw1+IVb\\/BZNSwSX9RZEmHFJ\\/EvrzoJcP7hILDYOejGEspAbe3EWkbFxsTdDpmq5+QFviHVFmpY1RmuAeUu4XfJfkC+0ODh+jHYY63AMicJ27ECMd4rYCgzR9h+JRcpBr4NQYnLOCw8ZHi6CQ\\/1WCcxqy7wT+FosWABvIUI+v2lJz4NeBaHE5A8t9v0F4oUXjq1\\/2HciRsuKvChwHYQX\\/2CJhE38f0muiHGY8TslRi9l8e5BhJbVir4i21x83d9SC\\/r7H8qO\\/KLFocFgwNDhmLSU19sf3j0I0QJcjfQWLM33DZZw8vgflE\\/AuMYF4GWz5d8BeA3ii0EXCyQSCx+Y9y\\/KDS2OgdluGNTwEv7Nj8X1ObToXwq8Gp24IO7iFy3NcAfYPyq\\/eOFnMPam+te0sdinhXGlAGi5DCeP\\/1m5wsWpvzCIge0FNBZmIMaghXGlALwlNQ36Wx7KhXYHRQx5sBKV3ovTYoZba+pxmVCCtBdaou0wDvu35Y4WpwVlIVoYURiHFtpcVFggjoC5yqXPtGRCWP51ucTFjb9AvJiLGaiFWdECfBkKFoRIdg3Zl3OQU6ugv9ehPJE7XPgOw+WGjQsjCuPSQnvLBrgOgAuVu\\/gAS+wl6O9yKK\\/kihf+1CXXZUB\\/2XCMxRYtECy8UMzPJ1LchfWwX6ZgmAFwYRsLlxZiBnkDXyIQWo7DZ7H+NgVDCwUMz1gEaVENivEJyF18fqZxJNyJ\\/\\/vkDhdrXoT8xQIW9pwYCZ0dWpa+0hIJhyl\\/rfwlxsJgOOm9JS5MWOAa8v5oSYdx2O+VK1yM1Wz\\/QGV5iUv\\/3uL6Yt7Ccy9oByU2ue81LeEw5S+XB7Q4eKDlj\\/zwcse0bAVh4TT0VWA8hqUS9DczlO9ywQtndXOB+dmJXJn3hQxYuAUAONE3M31vYcmFJyL9CbkdJOPxAhCz02ZDbH05CIot488DoSX+Huw3MdTe5AktYidg\\/OiwOI\\/ENCy2FrQwgfGFlnI4H\\/aX5Akvlg6z24lekGtRmrMwbcfCXlRgvITlPNxc\\/KfkBS30IseX9A8CiytatlsGLHaCMQ9hiYcnU\\/5B+YvMz87Otfi4sDFihmOkvXgHS6QWzof9VfmFy25n5zJkK4cBC5zTwP6y9ouWVFgP+7vyABcAGAIWV7RwbUeMltXKM1hik2C+T6EOQ57wghFDsiJ2DUY0xg\\/SRGtjXsESaQTxLQp1QPIYFxoWkSvAVFCuRdHCCcd8oCUX7pf88\\/KmZ8lkxTkt4GyauLmYwHgESyTsS4bS5QUzYOdO7F9SUMCDnJbBGACMR7Q0wr5kKF2M5e+WFeZXtqDl54cuAMDRmGVtzBtYsiEsoTAB698DWpwEYz\\/QUykZuQtJjC+0JMKHT4QixFj9rlBxZC4\\/rD8XyfWJXqUntJTDvmQoUvD6dweLA1rYFAnSsvaYluOt3+98qH9W0Pp3yorlP6bB4HAkTozBiwewHA38eJdD\\/RJBALBzfuuvZosWJkXc7IWJiwe0VMMUP5RtOYPFHi3gJ\\/h7LPnVMfewnIUPYw3lWHZIAf4Nlxc+SlxaGIdluoYl8ubjexnq18sBLd4ebyahIRaOeUFLGIeFcie7sLhkBd4csB9a0gsf38dQ8vdW\\/sbJ5UvljJ75fD6bzaa6pN9LfzKXz7paLrVn+GzVGCToW\\/dR6svzGhhoGIaAxiUs0f5v\\/q7sWbvtZjmfDL\\/e+912s165LxaLt7c319dXhcJlPpe7yGbPzzOZTDp9dnaq6ewsnU5Lf3Z+ns1e5PL5y0Lh6vr65vb2tli8Kz\\/Um+1u7\\/XjazieKU\\/MDvoVeiWB08U88RfSZFzScvFrvgHBaLdZjD56rcp1NhV3+a0QUSSRvrgpN7pv39Pl5h+HxxUsO2B0jAELiou7d\\/8k3C9pQ7vtejEdfr72up1WtXSVPU1EvIHAqeLJ9MXV3UPjsfvcf\\/sYjGcLyYCCfpfE5JYVDRib9uLq3Y72g37T\\/gn9rKdf\\/WapkL\\/Ipk+TR1GP1rq3isWPU2fpbC6Xv640nz+G8\\/X2gL3HmgRHOy25T\\/lzS8t1OPLCkJSbb9arxfC1VczsI8TyRZFUvvTY\\/5oslqvN9nCzHhAVxies3YVLjJt3MxY+qoXSbjMfvndbjfJNNnWYJuJA0eN0\\/rZcaz4+vw3khCfoNxmXG1rsZPsuaekF+y4dmHbrYe8hnz5NHcd\\/DSakovFE8uT07Pym9vw92+wOo0zNYMXuSJnPtBQCfY8ORLvtaj4ZvjavU54tyX9HyVyp1f8cTuTydFDksIxFvA4ARmMwNi7equ9g3p6D0Xbx9VwrF\\/Mnv9ZKBBVLnReK5Yfm8+d4tf\\/SAAMWcVzYtFDIOH+PHg7ChoPRz6RfSieOfm\\/I5USRaCx+lEheVLpfi71CA8PiHS4mMY7fm7M\\/WA\\/bbRejz5fGddLDNfZLdZS5rXVfpShtsZ8uKM0K\\/gl3zLilJT7cw1twSNpO3+rFwvlxwA3Ff0uRxGn28qZY735NN34nNjAs9mixeiSm0\\/eh\\/pfisPVX5S8m8d4qclbsfC\\/3XwzQPMe9xTin5XS67xcdhHar8Vu3epnwdNX8caUK7XEQzU5hj1GewMTowzh8zb\\/\\/aMrdvH+fP0\\/FPF0qof5L3vVHyl6D+Xyx3OyvGmCDFrbJOHvJd\\/t6jUHoZ7OadPJhucsPRU5bg4misaLRaDSZy9DsfA7RbMMC4uLoNZ\\/OfH1lAWo7eWmVL0JD8UeRRLFvkqLRMhoOh4PBaDyVzGblXwFtZ\\/NsWc9oibR\\/ZYq\\/m7Yvz5IhKb4plml9mayMFFRUVgbf31+qvr8HY\\/\\/Gn90ajJNXnfltrZbdejZ4ugxjL18VSRb7Q9RTVFgGAx2WT0kfH+\\/vb29vr2+fw+livdnCT6Zw9812QYyDl330u54Ctv5ulS+PPV8coXDFsvXPEcbKUBHEyuvry0u\\/33+RoPkeTWbebvPcMy0d7+48aO1mrUwiHjYc\\/dfRzesQcxWEFZ0WGZZ3E5Ze7\\/m5K+mp2+2\\/fozmK4+Y2SstF7\\/ilJfddvr1fHPk\\/bIIBSh61vgcIdJJQYzlg2KlJ7Py9NTptNuPj61W87Hd6X8MJ3MPBp5lBmxQ44KWX3Da3m75Wi6chfn8vpQsvg4IVPR8BYHlQ4XFYOVZZsWEpdFo1Ov1Wq3e6nRf3gczd6UAe6fG6OfB2n7lV14t2cA0b4ZjLPtU9Lz9hYGCsKLUwXRjwWDpPavGosPS1GCpPjw8VCrl8v19ufU2cjfsbDscs\\/vSTw9sm6kt7RYfnWKY0O9VkeO73oAMvxBU1PT+3YjCTGPpKr4iwdLSnUVipaqyUr4vle7u7orFu9b7aDqbrxyfVeMnLdFnT5fvPrUbN\\/JpOaOPhFn9\\/hS\\/aH4M8fBLi8C+dVQ+jSBMgcVgRYVFNxYZlmq1qhtLSaalWCxWO8+Sev3Xt\\/fP4XzjwGl8pCX\\/T7ZadtvVoByP6PJjVYSCFEne9ocAKN9fKCpKeq9k9y9IxmIEYS0jCDOM5V5l5VaDpatlONI\\/6LwM5mu7M5uCwPzvf\\/Zefewf3NWyW7w2bo4jiHxZGKFoRXOtrwERfhkRmJ7bq6WwN7xsjMGi0VJForCS7Cu3xYfOs1ZlVv6B7kWPnf7H92A09\\/x0Gnsv\\/99rtSy7uZN4JBLCsn9FUuX3ASKCFL3BorACwCIvfCy9R6KwOwSWZwIWFTDpX8kl56fe18xJeMaQrdd\\/sfHsunvQz3LUOY+Q8mdlhCIVObpsf4GkmBGYmdxTGQteCtNTFj1jKRqwaMai\\/gvNjFRYNMhkzOpPn+PFcuV+T42dNyD+6skq3ot206f7bIxiJaRlT4plKi\\/fcPhlJPaGragZS1\\/tR3a1lW+WwqhamPLwgdtK2zAWExY9dENhqSpV54d6p\\/fy9jVZuQjP7LwDhX+merxqnSYgVEJY9qTE1fMnygoblle9ed\\/Xm\\/dmQ1J1FnXFS7BUQFi6NCy6s9QNVh7Ufy6pUnl4aL5N1o5sxsY7cLzyelH7od3iu03HXyEs+1QsXXv91jnBAzAzAjPmJ9F+pGEsj4SxmCmLyooCC5nfI2GYyooCSxVh5V6J5JRWzX3r5Ws0sbc7QPwtiPwLB+qvnovZOJOVkJZ9KJIodj8hUABX0WxFye5NWB7xhmT1wTAWBixtDiyIsdwjsBTVL3Rfa7U7L0PBmQDxN+H64FP83WcuGolyWAlh2YOi2d7nlwkKHn8Zmb0egumwoN17oiGpBWFml0VS2UjwO7iztBiwVABY5K90I+la0tVVqTNYWiEj\\/CbEPvay4p3qZ9S7P45EZYWwBKjYSVmGhTKVDzICI1DB542RHgvWZFFZudGdxYzCTGdBEnzaWTRWDGcxYJFouSpIuqm0Xz6+RvM1XD4Tfhsqh7y7eN7In8SimkJaAlMkUWi90+GXEX+ZrqKz0jPLxnjd2IjCKtioiw5L13QWMAyrs5wFhuVahUXW5WXh+q78UG31h0uyrCX6PiQPFpbt6iUdNRWyEpzksvEnjsrHB+AqtK3oPZa2aSw1wFiKygKvPD3jrLTRKAwphmn5vQ4L6iy3Giw6Kzotl7LyinKSLkudr9lqs9F2PAu+D\\/ED3dWyHbVvE9GoCC0+rpJQipI3T++fn7ilYKhAtoIvfLQWRs9Qqgu80gG7LHjlWHMWnRXti6D5PQhLwYRFoSV3ISt\\/W33s9t8HM8E34uogU\\/zN40UqFo2GtByCotnmG4DKO4yKwkoPYqWJ1Y3L6KgLDkuHHHbhwEIk+LdkzgLAgtCSVXSRLwi+E4f3zLzt\\/OM6pkiIlZAWfxU9LfUNTtCcHs1VzKZ9D0ntkeQD38hCjIXJ6\\/v65uEJLxzjLckG1WYhEnzaWa7AMExlJWeykj2XJPZeNINmg9Bu0S2dxuzA4udKCfVf8qr9isZeRFqvl4sZIRjiEDos+krX58JUViRYOngUhk+GEaVjxFjuAVhuIFguObCI0ZI9rBR\\/N75OxmOxkJZDUfy82v+goi8t\\/kIyezS1x3OPR6xuLDtLhaobG87yRFWOm+QcZZVwFspablBaCmCG75CWQ0rxd+tB+yyGKIQlaEVTtx3SUaj4C7GVHrAnRVrzTbhubJTCpJV9q4dhREsSSlmoniSYs1xdcTJ8JGlRWDnPiLwdh9PF303qheOYfVhCWvzTUaHx8o6DQqb1mqmgttKlEw983rhC1I2v9TCMNRnGgEUrhnESfG7ScoE4S0aIlmXQkGjafWdjpEJrCVjp2tv7Ow0KZit9sgpm2sMjmqQDY2FmynJ1U+3gCf4jlbMQYRhKi7OkBYdFgJbIQWyY3K0+GqdxSUxWQlj2LzkIo0EBSNEze9bYcINipUxEYVd6gt9BS2islOXBos9iz1myGiwCtFweQhw2r+WO4xQsgrT4vWb+qiLHl40+zgmW02OtFSNd6dKNEqQB\\/0AeVGHAUm1Dk2FEm0X9CuaEvuksRevaMctZDFisaYkFvmFytxkU4rocOEtIiz+Kpiu9NwQTw1OQ+hfhKkhT0ZgZxrokCCsllBUlDKNrYTos9TryFejtLLZyFqoreX4uTsttwBsmt1+NbNwaltBZ9qxI6qb9YnJikoLXinFXQZc7uRlFTzfK2KiLAUsbbd\\/TI8dEmwUvHZcQWJg5S56b4YvREgs2xd895VJHRyAsIS1B6ijX7NOegtSKkY69igoxrKKUjI0Gi5GwUE0WZVlrzkLDQkRhVQMWoIV\\/i\\/VZhK0la8JiRUukFyAqm3H9SJaAtYRh2F4Vy9R6r4ReXihUnp\\/JEAxrvzeQ3L6KdN6JjOXq6rbWAfeyNFFjQcOwCp6z6HvI4DiMwcoFxYolLfng9uIvOsUkzkoIy2EodnrXfsFJoTIVnRXMVbSKMZbb17BCmHnAsQFL4eqm1oFTFjIKI8MwBJYiHxa6hU+HYZlM2uJ9CayLPyueJAhjcULLPhbPH1MkUWj0EUfByl9YsmIEYOyVbpSxtN492b2Xd2nd1jtP1BSlXVhMWkxYuLTQYZgVLZFiMANii4+bREKFRcRa2M4S0uK54heV5xck8IJBMefAkGzD3LTV1Jd5FQ3C9CwDabJIC7ooOwv8NcCZYzy\\/LyHOAuYsvHkXpHYss5Lm05IPotWyGz3kjq1gCXv4wShydtfpA5yQERhVAkP7I0QMhrJyR7JSKNYBWJp4T1KHxcJZbgBnoXe0OKUlPQ4Almk+oUqYltBa9qbj6w6MCuYqdA2MOJIFPRmvok\\/m36OV3hv9XInbRgcqEFAb8MFiGL4JHwrDWD18KMXn0hLJPO\\/78RO76fPlMcqKuzhsX2voryiZbzzjboKCghaL8S0oyDAYfnyR3l9Bx1P0Va0uZ8VZGCkLNEaJxWEGK8DGYngbPrN0rLDCoSWSa8z2DMuonD0+PoadhT3xEtKyF0WOspUOi5PeM1IBIxJ7PdPAymB0DGawYkZhl4Vio8Nq35MN\\/IoByz15FJKR4VvDwgzD+LREso2XfZKy2w4vjw1WwqTl0BQ7u3sEKCGjry6R1xPZCp5nmDVjs8x7o\\/dY5JVcbFDb74FZlyoVhcHnhgmHYYC1pNM8WqIXjad9xmGz7tXx8THhLCxrCYvH+1YkddPoSmCQqGCgoKiQyYrqBg2cFTPFMApXRtlYdpY7jrPw+veMBB8uhwnFYWkuLZFc\\/XGPB1fMSpkkCosNa2HSss\\/F9MsViRbq3R7oKKSrAL4CF3srWBBmHrOKwtLsPDGqBEZAh4Zh2NAxdnyrmbKgG4tpa6ESfIKWM\\/jtuag\\/9vZVPd6MK6kkioqFs4Rx2H4ViWcfuiAoXYATc3Eb2UoDr15hZ61S+YpWCpOW8V2TLqkBYRg+7CICi\\/hwGAkLSEssV2+1JvthZd0tppI4LBbOIlY83u+K+r2KxM9Lj10jjSdBwcMvogYGPEoFYeUea0aiMZi0kPOFUhNn7xGPwsCeZPkehMXI743jjoWOQzJoMWCBaJGcpdV63U8Xv5OVWCFg8SLF3\\/Oa+rWKnty2us8EKigrXcpWDFaA1J7eLn9XxI3FgKUFpj\\/kBnw8ZyH3sxDjLvQBL1Y7i7GkBaQlmq09tjp7iMN2085pyoAFT1nCOOwQFEld1jsAIUj4hSb1pgtgiT3CCn4osTmUguYr8gou3LeAR01gXRY0ASJ6kox9xcxyGG\\/\\/l0HL2RlESzRbl+7uy3dr2Q0qFykFFpSWoyOKFjYsIS3+6ihXaytk4Kg8EY6CegrQEsFz8TLBipnca74ir95C2XQWwKhIq2KFYZCzAOUwMWuBacnIsDz5bi3zq5SiJB6IWdESwrI3xTLVJ9JQDEehUhXTVcyFjZ8sQTXasazCSO6lxXtZfuTCUgNzFur5LEUOLNxZSlYcRtESyVTle\\/O5erx6vz+FWEnQrISwBKOjzH3LIET9oDih4y8iAtNTe3T7Ch6DaX0QPV9BnQWohZknjkFjlCxY6IP07cOisHJ6ir9HkUxdvrUXX+OwXe\\/67OREhwVyFiTBD3fiB6HYmTzNqMVdACYd9Gc\\/GxXiQGL8hFU6t1fWb+6qAjlLA22zwPk9YzaMPkjffhiWVmEhaDmtyYdwtPzcMLl5S5+orJiwsPosYRM\\/EEVTV7WnJ8hMcEtB2yoAKmSwhP3sR9IVPQa7VJZu4aFN9mygigGjgU+VjiFnocph\\/FFK01pwWk6rSjfpwzdr2c27V6cYLEmqKxln0hLCshclC9W2AClmtYpIVcwuO3EcMX76CgqL7iu53HW1TdSNsV2SArCY24oFaLGRs5wStJzVleOd\\/LOWTTN3KsNygsHCs5a4bVhCWtwpkZfXKwkJzQkWf1GsoOVdMrVH2+tmbq8u3SsQlqYgLHc4LDdE8RgIxERoOTuDaEnV1LPQvv1BZbd4ypye0s4SxmGHo0giq2UNKCYdmhMk\\/mpAuUoVHXDBDow0f+RjMZi8bi9kWOhpAOpkF3ZLUgQWeDosSwdimLMosJi0RM4eFFjqXX\\/isOlj\\/vTMpCVpOw4Lt0v6LYmVUrNtaSmtFhV\\/EZVd4umoSH8dsxW9ca8t24vcda2NpyxU4waedrlHjQXN74VhYewsJmAxadFgafizB2xdOU+LwCJoLSEsPiievmu05UAIRwQ2FST80oIk7Ewv8hl2JSK3N1m51Fm5uLiuA7A0aViQ5o1ZOWZXw5iw8FN8MmdBaImclLUnM714f5Lrbvp0rl0SC8OSNkYpw\\/EwnxU\\/va1DkFCxF+4pUAQGbY832vbXdM1YXbK5m7oJJrvKhh8KDmyULJLOwnj8l41GC0FL5LTcbKnH1iw8h2XeyOv1aitnYc27hM7is6Jnt7VH0EsIUlooKYip4OeuEEdF4sPApq0gvpK9KDaIojRWOiBPoywbtBCVYyoOE07wmc5iwHKivlmp+5YGy4fXrGzrZ0Zzh20tFqOUIS2+KnJSrD8+GpTgqBCk4KkKkXuDx65gP\\/B1WuRFe2mwIq3XYhPLjIg6GxXmmdYChWGMncUCh4fx4rAThZZI8k47EK3W9jbF3027ee2iJiwmKwQt7DDswGCRriR9SDcS\\/U\\/6kO5I\\/m0kGsi9uFYkdlJ4aD5SauECwi+ys4Jl9vclvKKLokLEYNJCzZVaVCmsSbGCXwWfo0QHKR3BgsVhGRoWufMhv13Je+P0wKGnsGwbhTQFS4oFi6Cz7DdrkTCIR6VbSx2n0mfn2Xy+cH11Wyzel8qVSrVar9Wb9Var3m402vVmq9asP9QfHmr35XLx7vb6WvrOSN+FszPpJUtfQnoZB0hRLFWoNFsaH48tEhLTUYgArFZD0u4qtIbvsLY93rhHUuwsDEsDaEnikR5z6thRo4VZDjvFaUmUtAS\\/VvP05Ir1s3ZdnRXhDD8oWOSvEo3F48eJZPr04vKmWK7V2p3u6\\/v313g8Xkzmy9lyMV8tlytF67X0oWulfRj\\/Xa6lvzdfzmeLyXjyPfx+\\/eh3n1uN2n3p5jJ\\/nj5NJRNH8ksL1IgiyXy1QQPCwIS2FOZWX6KaiybbpK9IsNw\\/Qj2WBnktfOa4rKECVsNYzmJn\\/xcFi0TL0Z2Ws0j35OH24sXzjXZhwFmOAWdhzh37HYdFY5GE5BwX+atS6aH+2Hl+ff8aTMczaZ0vFsulzgZLa1AbUOvNerVZLVfS1x6NB1+fL\\/2ndvOhend7m7\\/InCWPE9K9\\/Lc\\/dqLJfIVmpdk0OUFIAVkhQiMoW8Ha9kTNWF2n+bKWK5mUNgBjgQf0qdkwbzN8DJaT\\/44qmrPUa9W+Z1nL7vnqPENYC7GhRTAOkwDxqS8ZjcZP0pfXD7X208v752g0ns5n84VMiMIIrpUFNTAzEh3KB0+yL0mONZtMv78\\/n59b1dLt5Vn6OH4kJULeA4IokiyQrDQxNRpcVMzGCt0npM68w\\/r2eZSV81zlEZ35Z0wyV2kuwUFKMg7DhvTp6TCAFmo6zIAlFS+qsMj31PBqD9j6s6Bcms5ZqCF9AVi8jMPkNOTo+CSTvy01O68fE4kQBRC2aHB0fDzwGkNbRJvterEYD977nVr19iqfPksmpDfFa3RiiXy50aTUIFVHRJBSqQAJN3SoPY6KyoqxRvNVcJQZyFhgE7OTs7AGjy2sRWMllbppGGFY1aPZ4+1bOWsNCzMOcwaL5VqKxBOnmcub+9pTTzaS6Wwui0+KJTUSHHag4dnMlmBG4UZyndls9P3x0mu1ysWbi3QyGYtEPWAlmsqXaigsDWWRYrDUEVSwnJ5pKpityIuWHnFBasbyEs2cXz6Qs8xIel+n6cQ7+BQszjN8Thxm0ILA0vTGWqa3F+cGLGCGD8EC00LC4ogWiZPzi1L99fVbCrZmKiV82bcaO0GasMlg4EikLZfz0fj9uVW5yZ4fH8VdFAeOc+W6xgTtJoSj1AzBqNCFXCAEQ2G5MGE5P7+sgeM0grBQpWMKFqFZSqu2pA5LqmnA8vDlASq7UU25OpayGLAAcRg3wxdnBVg4UsyVODkrFKvt18\\/RRMJEAWWm\\/Ff+EKBGh4eNjzA4rLyG6TNMbmR0VvPJ4LUnpThXufTp8ZG9MC2azN6DiKh+gsVe6nrV02xq2RI7S4imPYrKJbpMs\\/oaOb+q4awgsBJmxrMy+qmS3mf4yvpVYZFv6Ml9HLabNgry1TNkGMaMw\\/j78J06SySSPL0s1lu9t+\\/RZDJVSOFJhkgBaa7+zrbnCCGzBqnhuAwPGNVvVqv5ePjx2m4\\/3F6en8aPogIRaeryvqYtSSg30SmpY44Cuwq1sYR6TgrJipnby4ske13DKwwkLNCeYuCoY5QWJixCo5QZlrOoK9hwlgf3A2K7jvlTQwwWr2hBV0NC+h48fQ7HU1oyGfp\\/Z1MeRVbEkOAsjQ+zhiYYnFnk\\/1bImDnOfPT+1LgpnCWP2MhEk\\/n7GgAHgYq+VLFExczrKwxWihxW0EJYVoPlpsGFpQpuwAc6+GgcBtFiPUuJ03LGsJaU7iyVvtvZ49XLlRaO8mBxGodFIhGutUQiscTJ+XWp1fv6Hk8AUiw104liUWTDcETzGnulMwtefn5+lCBt\\/vXebZVvCpnT40QUi9FiqfzdgyUkqJ9IS0ONg3BQoDQbP3GYQCVHoZLJXBQbKCpoKYxbC+M5i1WC7yLDl6XAUn2oVOfuWPl5ucvhsKSxsWMMFvZ2SV4Dn01LNHaWLdVfXr+G4wlfEg4TUZYkbkSpcROgkcjIPcwNjI01Lork369Xi+no673TuLtOnxzHZGaiUgxWrckr0cRDJQQBRV6hZPBFkMIyFXTvCjaST9TBtKWZu2tgTR68bkzVFEhWrGFhJy0iW4shWFJqFFap9N2xMi+ZEaldZ7G2Fk52H40n08XWyxeCyXg8lv83Nv+vKQuY+CRB+IiS48BpnMdmP4i26+X8+7NTvb0uPzQbmIOgquIyQy9s1gRZrnd4rsLNVihbkdZHrtSkZ2tAWLC55nuqgQ9k+DYHXjiwoGGYtIQ1WBquBsTGzQsUFm3cBXMW2xMv1rDEjs5yt7Wn968RTAZLGk7IX7cFDo0MaDY0NfiIwApscIpWzfjM\\/BDEyP9\\/vV0vpuPB+1vv6bGlJauajzBQqZjCYNFXK7mfRFmneghGpPYkLJncfROZHbABi3tnsToPiQ1LUgnDKhU3s8fr+pXenNUubZSOgVFK3k58cVqix5mb+tPb52A4GkFEjGTpv9U+xmP472LWo5Cj\\/1eDBgZHSXCMHIeR0lDMCMRmNpIZrsGgxOx0\\/Ww3ktmMh+8v3XaDxgQxFQgU\\/Qe7wQr+U91cpWDJ2Fgd6XS+bDiLWbxGkicIljJ+A8wWPrOHT8OSRVixTFpkWpTH\\/7WdNybXH9foG4INUuItfKsMn5W0kDlK4vT8tvH6MZA0HA5HqsYjYSnwjNUP8w\\/ZAImYDmY1IDp8auCRAGGnsYrKdrR+Npv5dPDRf+o0G7Ua11GwZQqdFkHs5+Wl9uqKvKxwpps5rZ17srljVQ1jOAuQszC7kkbSIi1g9VGZjs893n1WsLeEmDq2hsWaFiz4SmSuy63ex+f390DVcGgSY2qoy\\/zdEP6bTJwUonBqAHqIGI0M0ihkCGoooyGhoXlhAsNABmbF0HazWkyH3++v3U6zXqUtRVuk6ColD1bBxhexn+TYujTrs+mrB2jHjFE4Jpo7pLnd3ZG3QdPifTlMWcQKLU2njclFSb4JABYtZ7EMw+zEYUdpOfj6+PyS9S0LIcZghOQDkvxXR0P1H+i\\/hbkRsxu2z8hNT4EyAJrSWDoMo2AGE2MBixmgydWA4ddLt1Gv0gsURAWzFbILyEruZViua7CvINZCj9eUQVgYbUnmKCX2ADDRWUpzEUs3c19ZO2SlW9DclshZdGfhwGInaYlG48nMVeWp\\/\\/7xqeqLIkZGRuVmICDpb7FAMnkD0KHxmZjwwMkNy2qcVc4EezN2YTGp+VmvpnJS89isVSvoGRFADcpcnkDMg9R9TFuR10bmpgaxQs9tUlXre+peTINzOfBiVQ7TFrF8I6+OWNn07vIQLOYgJb0N30EPPxJNZm6r7d7r+\\/v7hyaEGBwZQtInkM99I7+yMRIL3diOQ5fQSGRmXKtB5gGsR842bGrQSrJEgT1mtpv1YjYavPeemrVymUjq4WzlEk3sYVtR1uL5Xb3RbDiBhQaXTvAtRimpR0tyBl5OaVgkWu5rjmZeFjfYNAPyE8QIw+zEYTEGLSeXtc7L69vbuyEDF4wXQ4NvIakocfmhTYcPDRaa0U5jo8GJEgMkM0LZP1lEZiT7Ftr+rGej916jXr6DlifFCj6Fhe+wUpZipoSPPeNzBEiCL+AsAC0WsOQYYZigtSSlm3DyQNbpYwGb\\/DFn9AlncQ5LLHGSKza6\\/VdZb7IgYjRmvr++THa+BMRgSKZoQHjVEDAdPjjcChqvCMANzSxrZhgrFCouuPnZruT6Wa\\/bqJbvpTWKJwjALqssGoMhkx1nF+UmTQpydh9zivKe6rMwnaWAxmHc6TCxUUpzESfvSw\\/2G5PrTvGSgEW\\/Ki9pSYglLcrm4uT5XbX93H9R9GoSY0JDEQMK\\/Rznr2kISRChHNk1HSCnYUVobKfhEQPOatK8kP18t7TozGzXy+nk6637WCvdUaNgSr2HZgVr+OXKUGqP0cKohdEtfKGkxd0o5QkBy3GpNLbLys\\/49pKEhSodYzmLFSwxXPFUrvrY6\\/VVqbxoxBA284EiY1tfPNK+dQfip0YINNzEBjabGS8+s1cB0M+fwbsvfGZ0amx7zWY1Gbw9NSula2ltgjUwwlaUZXGWq5DZChGC0RkLXcAu8sMwoeEwW7VjZBGX2nZnj8dN8zwoZlOSl+Fz47B46vy6+vjcU9RHgTE8xrAZhBiFGfmXj0\\/jt5\\/qn2u\\/fOq\\/tUTIKmwDEh1GhCZjooMjVnPm5zOE0UAuA3QqrYhRsZE\\/7CGz26wW46\\/XXqtRlqi5pJv2GXwVpgsVIAKj99CQuwIwY7GCxfEoJXPgBaXFbmNy27o1D7lBw7A0YC2CcZiJymnuvtnpPmuwwMi8oMjgqcyH9B+dHuW36h8j1TRYXHqoWA2tFLBCNH4NzaK1aYMYAhkIFkFcnAZo6tzmePDSa1aKhRw6TIL\\/xE5fPVC9FYAVDJayDVgsDq0QeQAYNwyTV3HLVoq\\/HdwVEGfJYrBQzgIN6bNhSUhGrT9GVwJGVu+ZCQ1uMgg1tqSQhBgUmx8o7SGKbGBegxLDqqGJlgGsQrO1xRCMDX6cYLPdLhaDt06jcnWdz0mrAvv5eZq5qQIHyKDZPWNvAMoKlOHbiMMsRim5Gb6yiO1sa9mNW1cFOgzDKseibUmCllT2qtLsyM85RB7O\\/qyJRsagBs\\/\\/hfWO+xJAEUoOZD42oSGSmhEJDJ3QsJyG0Z2RwzDroTEbxGjGYZudrTzrPPjsP9Ufite5bFpbEee3VSAEoyafK2QYZoynlWzDwj0OCYKF3cPXV7EdZ+leyzdiDgGh0y7oiRW88TDojJfj82Jdfn6b8WRQgBgEGogXDRrpf9rHm\\/lnbwZSyqfeQLjeWfgIQ\\/MFIqMUoEFizMqZVUJDnhUAeMxqxWznu7EYBB17zPzIAdp6MZ6+tCvFXDqTLdXoCAwfgQZhIWihZ\\/QtkxYbJ7ygtICwiNPyMygVCugZtlnQWShYLJwlkcrcVslHHWrIPHVRbrq4zejYUBUAVOafMv6CARptUWR4xw7WWBEaXArglc4MZiYCgdncxGXlZDoZIUaUnZ2DIG2zXUyHg6\\/3t163\\/dhskFUwfS8zFYbhsIBzlKLb8C9YsACdFnYYZoOWWevmSncWMgzL4LCkaFgYbcnE8UXxoak+N0R5hhqFDOE2z13TZp7x0OzFmZj4UOYDk8MpCcARGhib4V0aeMCZlcsorFjMjclnLnlnNrp72ELnZ7tZLWeT8ed7v\\/vYahBFYzi\\/B8MwMViYU\\/pCxWPYWYRpedOe1cyG5YwThsHjYUfZO+XoNeD5bSA0QHhGZTPCepHwAiADPAiJ3N5xv\\/nQCnBa6RqChg8M22j4OwJQWpZQe9Kp0dgKz2yYjbKGDG52m\\/V8\\/PXWbz82qlVq\\/xmwpwYYOhYrh9nvtGDTYY5o+RlXzcduYKfYQmHYCX0duhyWSGVvKs1Wi3jCDp+aDosaPTIzfiEkUUGXCUCErII72G3IEO2DE6ExhgIsqJnALrNYor0WfNyS6zRC3NiGBzeTHxyXHV59ld1GGd38eOs9dSSzqbK6LE5gERqltDgOiYZFhJZtu4g7CzbugpaqRTP8VOamUm+ih7s\\/qr8w0RHxGpQPPVZ77plVaDzpwXGSMKJIooI2gh9ujAZHaGAdAJoGgMcA8MhsvqA6lHaQEXcbGRlRanRKwDiNWlqq1aibn0efUmbTatQrFZCWIpsWJiz2B4\\/5sFjT8jO4vcYe6ETu\\/QISfPwyBCvJk2sJFVQtSAYyLGw6JDRm9UzKb9BiGvZ\\/aLEI4rADGc7725sVNlAh4Js2GlZCgyEzW8ANStaUMu+8co0JYcdx6DYmNQo5lNns5K2ci+no463badWN7TVUT5JXO\\/ZhlNKEJWEFy7R9oz9Wk6gcWzUlQVqS6ctSVR7Zpp+FoEDThIihYzQEmQ7hM7ZlTQ+TGkaMBgRojPIZ22iGHGAkVuQgzNbWMZUXL\\/IaW9Dwchk9QNtpv5jYqOXn6Xjw+frSbUvgKHtshGvHNCzI+JrlkD4wHmaUqvis7F5L8v0gzoJvZwET\\/CROixmHJU5zdw\\/G4bumAHIox7GK0J6EpTLy1CUQe9bZUT9YrgMbjlWExi06fzOhgZOZif7MmSUpCBhbNmOTGQUILjX85B\\/hhvAZvSbwI5fSVovpdPD11u80G1JOIzGD0iIyHYaGYeeC1kLDYkHLbv5wbTyphjGhz7YWonacSKWlCIwUjY6BUINFDgBOx46e1IIBVaJWOjwc37F2HLgiQECDdzm5XRo6NJNZmXGH+llbLte2Divzxm6EWMGoMUIySD\\/KkRvj74+35+5jo1YpS35ze6MvURAWp2eHwbDwaNn27hR2IWfJZKziMByWZKZw93\\/2zoOhjWRZ20sGkZSFRM7JxphosL27Z\\/fbe\\/\\/\\/P\\/om9UyFt7p7JAE+554yFkIEjTT9zFtVXV19eHTsbVhtg2MGOEJsUhbyT+Uj4nuXNlkpP3CSpxSfChswS8qcNADNnXbQfNAYJWevxS5NqnuMlhrIDN6EKQKYWorjsKlJSyU0AUsVJwHn98RXe77\\/+vX6+uLk+GB\\/e3s42EoGa6dr0wJKKaODFi8tr6MtAEsTumGEFvEsGSurrdGBq3YoOu8ekXa8ceyYfpqVfo61hCLFDUkiXF1JyYFqw6dyFDQqpLkLEyNnaLJdaHw1MOFVMCYxETJTU2zGg2Ucy7ql\\/\\/3373\\/8fH19fPh6c3V+drC\\/P9ze6g06nW6r3W62DFriaimrgWyx8ufNcBDthunMcRXgL61s9HYOD\\/MWonnJw5HPDP2J0ZvzOpbm2tJ\\/EB8qQSrsQ', '2026-06-10 15:03:28');
INSERT INTO `integration_logs` (`id`, `source_system`, `destination_system`, `payload_type`, `reference_id`, `sync_status`, `response_message`, `synced_at`) VALUES
(50, 'SDO-BAC', 'SDO-FAST', 'PROCUREMENT_APPROVED', 'BTICY-202606-001', 'SUCCESS', 'Draft generated successfully: FAST-2026-000012 | Filename: approval_6a2a120c46278_PR-0036.pdf | Sender: SDO-BAC | PR Number: BTICY-202606-001 | Timestamp: 2026-06-11 03:40:53', '2026-06-11 01:40:53'),
(51, 'SDO-BAC', 'SDO-FAST', 'PROCUREMENT_APPROVED', 'PR-0020', 'REJECTED', 'Rejected: svp 200k below test | Reason: lack of docu | Timestamp: 2026-06-11 03:43:13', '2026-06-11 01:43:13');

-- --------------------------------------------------------

--
-- Table structure for table `integration_tokens`
--

CREATE TABLE `integration_tokens` (
  `id` int(11) NOT NULL,
  `system_name` varchar(100) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `integration_tokens`
--

INSERT INTO `integration_tokens` (`id`, `system_name`, `token_hash`, `status`, `created_at`) VALUES
(1, 'SDO-BAC', 'f57c675b52eeb23b273d763e6a089f1d03941c855f27a5fa0d31d8259f3b52e3', 'active', '2026-05-28 12:03:03'),
(2, 'SDO-FAST', '06af8524eed845faa299ede20f1be14d91a386378e08f35d93f2dc2fb2c74240', 'active', '2026-05-28 12:03:03');

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `device_info` varchar(255) DEFAULT NULL,
  `login_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `ip_address`, `device_info`, `login_at`) VALUES
(1, 1, '::1', 'curl/8.19.0', '2026-05-28 12:32:04'),
(2, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 12:32:21'),
(3, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 12:33:39'),
(4, 1, '::1', 'curl/8.19.0', '2026-05-28 12:45:00'),
(5, 1, '::1', 'curl/8.19.0', '2026-05-28 12:47:26'),
(6, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 13:15:33'),
(7, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 13:16:05'),
(8, 1, '::1', 'curl/8.19.0', '2026-05-28 13:16:29'),
(9, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 13:20:54'),
(10, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 13:33:33'),
(11, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 14:00:14'),
(12, 1, '::1', 'curl/8.19.0', '2026-05-28 14:02:18'),
(13, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 14:44:37'),
(14, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 15:32:29'),
(15, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 15:43:32'),
(16, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 15:44:30'),
(17, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 15:48:27'),
(18, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 16:07:12'),
(19, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 12:34:33'),
(20, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 13:39:55'),
(21, 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 16:26:36'),
(22, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 16:29:35'),
(23, 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 16:31:56'),
(24, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 16:50:41'),
(25, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 18:07:26'),
(26, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 18:09:48'),
(27, 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-29 18:09:54'),
(28, 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 14:20:39'),
(29, 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 15:12:10'),
(30, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 15:12:16'),
(31, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 15:42:45'),
(32, 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 16:41:40'),
(33, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 16:41:52'),
(34, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 16:42:31'),
(35, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 16:43:26'),
(36, 7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 16:44:44'),
(37, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 16:45:02'),
(38, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 01:29:08'),
(39, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 01:30:52'),
(40, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 01:36:06'),
(41, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 01:39:02'),
(42, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 01:43:33'),
(43, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 01:48:34'),
(44, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 01:49:13'),
(45, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 02:18:23'),
(46, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 05:03:24'),
(47, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 05:07:09'),
(48, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-01 05:54:14'),
(49, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 00:06:39'),
(50, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 05:08:27'),
(51, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 05:53:51'),
(52, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 06:37:52'),
(53, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 06:37:58'),
(54, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 07:33:10'),
(55, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 08:38:36'),
(56, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 00:03:36'),
(57, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 00:04:51'),
(58, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 00:40:22'),
(59, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 01:38:53'),
(60, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 01:40:17'),
(61, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 02:56:26'),
(62, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 02:56:38'),
(63, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 02:59:00'),
(64, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 03:29:22'),
(65, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 03:29:44'),
(66, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 03:30:00'),
(67, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 07:52:31'),
(68, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-04 07:53:56'),
(69, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 07:14:38'),
(70, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 07:15:05'),
(71, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 15:02:24'),
(72, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 01:35:15'),
(73, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 02:10:17'),
(74, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 02:10:24'),
(75, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 02:32:29'),
(76, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 03:08:50'),
(77, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 03:09:03'),
(78, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 03:13:54'),
(79, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 03:14:18'),
(80, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 03:17:03'),
(81, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 03:17:25'),
(82, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 03:18:27'),
(83, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 03:25:11'),
(84, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 03:29:36'),
(85, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 03:29:59'),
(86, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 03:30:36'),
(87, 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 03:31:22'),
(88, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 03:31:43'),
(89, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 03:32:07'),
(90, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 03:36:19'),
(91, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/3.0.16 Chrome/142.0.7444.265 Electron/39.8.1 Safari/537.36', '2026-06-11 03:41:25'),
(92, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 03:49:59'),
(93, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 03:52:57'),
(94, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 03:53:51'),
(95, 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 03:55:56'),
(96, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 03:59:12'),
(97, 10, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 04:12:13'),
(99, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 06:12:05'),
(100, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 06:12:24'),
(101, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 06:12:30'),
(102, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 06:12:40'),
(103, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 06:12:49'),
(104, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 06:13:11'),
(105, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 06:13:30'),
(106, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 06:13:49'),
(107, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 06:15:01'),
(108, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 06:15:18'),
(109, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 06:16:31'),
(110, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 06:16:40'),
(111, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 06:24:19'),
(112, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 08:44:14'),
(113, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 08:45:35'),
(114, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 08:46:02'),
(115, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 08:46:19'),
(116, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 08:51:47'),
(117, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 08:56:03'),
(118, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 08:57:02'),
(119, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 08:57:50'),
(120, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 11:02:10'),
(121, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 11:02:30'),
(122, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 11:03:20'),
(123, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 11:44:30'),
(124, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 13:27:39'),
(125, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 14:58:25'),
(126, 9, '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1', '2026-06-12 17:07:46'),
(127, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-14 07:24:09'),
(128, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-14 07:24:22'),
(129, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-14 07:24:35'),
(130, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-14 07:26:00'),
(131, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-14 07:26:09'),
(132, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-14 07:26:58'),
(133, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-16 10:52:45'),
(134, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-16 10:54:09'),
(135, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-16 10:55:46'),
(136, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-16 10:56:18'),
(138, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-16 11:12:41'),
(139, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-16 11:13:21'),
(140, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 03:02:51'),
(141, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 03:38:44'),
(142, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 03:52:30'),
(143, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 03:54:50'),
(144, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 03:55:41'),
(145, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 03:56:14'),
(146, 7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 03:56:31'),
(147, 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 04:01:34'),
(148, 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 04:01:49'),
(149, 7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 04:02:02'),
(150, 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 04:02:22'),
(151, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 04:02:34'),
(152, 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 04:07:25'),
(153, 9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 04:07:46'),
(154, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 04:10:24'),
(155, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 04:29:24'),
(156, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 04:59:41'),
(157, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 05:00:46'),
(158, 10, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:01:30'),
(159, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:02:49'),
(160, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:03:07'),
(161, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:03:19'),
(162, 10, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:03:29'),
(163, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:03:42'),
(164, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:04:00'),
(165, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:04:25'),
(166, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:04:34'),
(167, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:05:02'),
(168, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:05:30'),
(169, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:05:41'),
(170, 10, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:05:53'),
(171, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:06:32'),
(172, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:08:58'),
(173, 10, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:09:40'),
(174, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:09:58'),
(175, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:10:05'),
(176, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:10:50'),
(177, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:11:27'),
(178, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:11:54'),
(179, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:12:31'),
(180, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:12:36'),
(181, 10, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:16:34'),
(182, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:18:25'),
(183, 10, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:18:44'),
(184, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:19:11'),
(185, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:21:28'),
(186, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:28:40'),
(187, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:29:38'),
(188, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:29:53'),
(189, 10, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:30:13'),
(190, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:31:24'),
(191, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:31:45'),
(192, 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:32:11'),
(193, 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:32:45'),
(194, 10, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:43:38'),
(195, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:45:35'),
(196, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:46:54'),
(197, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:47:29'),
(198, 7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:48:11'),
(199, 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:48:38'),
(200, 10, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 06:49:04'),
(201, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 07:16:18'),
(202, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 07:17:18'),
(203, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 07:19:32'),
(204, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 07:44:04'),
(205, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 07:44:46'),
(206, 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 07:45:36'),
(207, 12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 07:46:24'),
(208, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 07:46:35'),
(209, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 07:47:05'),
(210, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 07:47:34'),
(211, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 07:49:35');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`id`, `user_id`, `token`, `expires_at`, `used_at`, `created_at`) VALUES
(1, 9, '$2y$12$KWhw20t3SPBM3lBghBe1WOVaxG6ZudzIaD6yT8YZVrcg3r3f5Yh9K', '2026-06-02 03:40:21', '2026-06-02 09:39:32', '2026-06-02 01:35:21'),
(2, 9, '$2y$12$XH1n2O6yBlVYRjtjsXRqLeZOzGq/f.hn6XBrLYnd.yMw/4F4tl94C', '2026-06-02 03:44:32', '2026-06-02 09:46:00', '2026-06-02 01:39:32'),
(3, 9, '$2y$12$tcF4ipMngr/Bj337dbu7mO2h0tH4xJwISTqqDCbzyNjOBzoyr7qV.', '2026-06-02 09:51:00', '2026-06-02 09:46:15', '2026-06-02 01:46:00'),
(4, 9, 'b0c6ad17c9a80ff09e53fe9a9a868537b50cb5ce994b0a9d7a8f80ad952a4f39', '2026-06-02 10:16:16', NULL, '2026-06-02 01:46:16');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `permission_key` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `permission_key`) VALUES
(5, 'approve_transaction'),
(2, 'create_transaction'),
(9, 'manage_settings'),
(1, 'manage_users'),
(3, 'view_all_transactions'),
(7, 'view_audit_logs'),
(8, 'view_integration_monitor'),
(4, 'view_own_transactions'),
(6, 'view_reports');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` int(11) NOT NULL,
  `position_name` varchar(100) NOT NULL,
  `mapped_role` varchar(50) NOT NULL DEFAULT 'User',
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `position_name`, `mapped_role`, `is_default`, `created_at`) VALUES
(1, 'Personnel', 'User', 1, '2026-05-28 13:14:05'),
(2, 'Accountant', 'Accounting Staff', 1, '2026-05-28 13:14:05'),
(3, 'Accounting Support', 'Accounting Staff', 1, '2026-05-28 13:14:05'),
(4, 'Budget Officer', 'Budget Officer', 1, '2026-05-28 13:14:05'),
(5, 'ASDS', 'Approver', 1, '2026-05-28 13:14:05'),
(6, 'SDS', 'Approver', 1, '2026-05-28 13:14:05'),
(7, 'Super Admin', 'Super Admin', 1, '2026-05-28 13:33:26'),
(8, 'Cashier', 'Cashier', 1, '2026-06-17 03:04:12');

-- --------------------------------------------------------

--
-- Table structure for table `reimbursement_details`
--

CREATE TABLE `reimbursement_details` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `reimbursement_month` varchar(50) DEFAULT NULL,
  `inclusive_dates` varchar(255) DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `approved_ta_path` varchar(255) DEFAULT NULL,
  `travel_itinerary_path` varchar(255) DEFAULT NULL,
  `activity_proposal_path` varchar(255) DEFAULT NULL,
  `dtr_path` varchar(255) DEFAULT NULL,
  `certificate_path` varchar(255) DEFAULT NULL,
  `bill_proof_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reimbursement_details`
--

INSERT INTO `reimbursement_details` (`id`, `transaction_id`, `category`, `reimbursement_month`, `inclusive_dates`, `venue`, `approved_ta_path`, `travel_itinerary_path`, `activity_proposal_path`, `dtr_path`, `certificate_path`, `bill_proof_path`, `created_at`) VALUES
(1, 10018, 'Communications Allowance', 'June 2026', NULL, NULL, NULL, NULL, NULL, 'uploads/transactions/47e3641a936a95eecb6f47a5adea6158.pdf', 'uploads/transactions/3ef8f061248b3413586c547c78c8652e.pdf', 'uploads/transactions/f1a526f7986b7fdd138436fb80069b42.pdf', '2026-06-11 06:27:45'),
(2, 10019, 'Communications Allowance', 'June 2026', NULL, NULL, NULL, NULL, NULL, 'uploads/transactions/6aeef8cfd7b39b566cfb8af2a5676982.pdf', 'uploads/transactions/49790b8ff0101810c9e526faa17b0b9d.pdf', 'uploads/transactions/5cb2cb3f4c18106a9d82cb4a6d4ff66d.pdf', '2026-06-11 06:27:45'),
(3, 10020, 'Communications Allowance', 'June 2026', NULL, NULL, NULL, NULL, NULL, 'uploads/transactions/ef839076b06f59c2ca486170ffbcda1c.docx', 'uploads/transactions/113f27fc2c96c568828a1a49546f3c31.docx', 'uploads/transactions/cd6fb4d8d08918ac5a27ba9043114f89.pdf', '2026-06-11 08:45:22'),
(4, 10024, 'Travel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 10:54:08'),
(5, 10025, 'Communication Load', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 10:54:08'),
(6, 10028, 'Travel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 10:54:08'),
(7, 10029, 'Communication Load', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 10:54:08'),
(8, 10041, 'Meals', NULL, '2026-07-01 to 2026-07-02', 'pacita 1', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-17 06:02:33'),
(9, 10043, 'Meals', NULL, '2026-07-04 to 2026-07-05', 'test venue', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-17 06:18:19'),
(10, 10044, 'Job Order', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-17 06:30:54');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(2, 'Accounting Staff'),
(6, 'Admin'),
(4, 'Approver'),
(3, 'Budget Officer'),
(8, 'Cashier'),
(5, 'Requestor'),
(1, 'Super Admin'),
(7, 'User');

-- --------------------------------------------------------

--
-- Table structure for table `role_data_scope`
--

CREATE TABLE `role_data_scope` (
  `id` int(11) NOT NULL,
  `role` varchar(50) NOT NULL,
  `scope` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_data_scope`
--

INSERT INTO `role_data_scope` (`id`, `role`, `scope`) VALUES
(1, 'Super Admin', 'all'),
(2, 'Admin', 'all'),
(3, 'Accounting Staff', 'assigned'),
(4, 'User', 'own'),
(5, 'Budget Officer', 'assigned'),
(6, 'Approver', 'assigned'),
(7, 'Requestor', 'own'),
(8, 'Cashier', 'assigned');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_key` varchar(100) NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_key`, `is_enabled`, `updated_at`) VALUES
(1, 1, 'view', 1, '2026-06-11 04:13:16'),
(2, 1, 'encode', 1, '2026-06-11 04:13:16'),
(3, 1, 'edit', 1, '2026-06-11 04:13:16'),
(4, 1, 'approve', 1, '2026-06-11 04:13:16'),
(5, 1, 'delete', 1, '2026-06-11 04:13:16'),
(6, 1, 'manage_users', 1, '2026-06-11 04:13:16'),
(7, 1, 'configure_system', 1, '2026-06-11 04:13:16'),
(8, 1, 'view_bactrack', 1, '2026-06-11 04:13:16'),
(9, 6, 'view', 1, '2026-06-11 04:13:16'),
(10, 6, 'encode', 1, '2026-06-11 04:13:16'),
(11, 6, 'edit', 1, '2026-06-11 04:13:16'),
(12, 6, 'approve', 1, '2026-06-11 04:13:16'),
(13, 6, 'delete', 0, '2026-06-11 04:13:16'),
(14, 6, 'manage_users', 0, '2026-06-11 04:13:16'),
(15, 6, 'configure_system', 0, '2026-06-11 04:13:16'),
(16, 6, 'view_bactrack', 0, '2026-06-11 04:13:16'),
(17, 2, 'view', 1, '2026-06-11 04:13:16'),
(18, 2, 'encode', 1, '2026-06-11 04:13:16'),
(19, 2, 'edit', 1, '2026-06-17 06:03:56'),
(20, 2, 'approve', 1, '2026-06-11 04:13:16'),
(21, 2, 'delete', 0, '2026-06-11 04:13:16'),
(22, 2, 'manage_users', 0, '2026-06-11 04:13:16'),
(23, 2, 'configure_system', 0, '2026-06-11 04:13:16'),
(24, 2, 'view_bactrack', 0, '2026-06-11 04:13:16'),
(25, 3, 'view', 1, '2026-06-11 04:13:16'),
(26, 3, 'encode', 0, '2026-06-11 04:13:16'),
(27, 3, 'edit', 0, '2026-06-11 04:13:16'),
(28, 3, 'approve', 1, '2026-06-11 04:13:25'),
(29, 3, 'delete', 0, '2026-06-11 04:13:16'),
(30, 3, 'manage_users', 0, '2026-06-11 04:13:16'),
(31, 3, 'configure_system', 0, '2026-06-11 04:13:16'),
(32, 3, 'view_bactrack', 0, '2026-06-11 04:13:16'),
(33, 4, 'view', 1, '2026-06-11 04:13:16'),
(34, 4, 'encode', 0, '2026-06-11 04:13:16'),
(35, 4, 'edit', 0, '2026-06-11 04:13:16'),
(36, 4, 'approve', 1, '2026-06-11 04:13:25'),
(37, 4, 'delete', 0, '2026-06-11 04:13:16'),
(38, 4, 'manage_users', 0, '2026-06-11 04:13:16'),
(39, 4, 'configure_system', 0, '2026-06-11 04:13:16'),
(40, 4, 'view_bactrack', 0, '2026-06-11 04:13:16'),
(41, 5, 'view', 1, '2026-06-11 04:13:16'),
(42, 5, 'encode', 1, '2026-06-14 07:27:36'),
(43, 5, 'edit', 0, '2026-06-11 04:13:16'),
(44, 5, 'approve', 0, '2026-06-11 04:13:16'),
(45, 5, 'delete', 0, '2026-06-11 04:13:16'),
(46, 5, 'manage_users', 0, '2026-06-11 04:13:16'),
(47, 5, 'configure_system', 0, '2026-06-11 04:13:16'),
(48, 5, 'view_bactrack', 0, '2026-06-11 04:13:16'),
(49, 7, 'view', 1, '2026-06-11 04:13:16'),
(50, 7, 'encode', 1, '2026-06-11 11:03:09'),
(51, 7, 'edit', 0, '2026-06-11 04:13:16'),
(52, 7, 'approve', 0, '2026-06-11 04:13:16'),
(53, 7, 'delete', 0, '2026-06-11 04:13:16'),
(54, 7, 'manage_users', 0, '2026-06-11 04:13:16'),
(55, 7, 'configure_system', 0, '2026-06-11 04:13:16'),
(56, 7, 'view_bactrack', 0, '2026-06-11 04:13:16'),
(93, 8, 'view', 1, '2026-06-17 03:29:33'),
(94, 8, 'encode', 0, '2026-06-17 03:29:33'),
(95, 8, 'edit', 0, '2026-06-17 07:53:01'),
(96, 8, 'approve', 1, '2026-06-17 03:29:33'),
(97, 8, 'delete', 0, '2026-06-17 03:29:33'),
(98, 8, 'manage_users', 0, '2026-06-17 03:29:33'),
(99, 8, 'configure_system', 0, '2026-06-17 03:29:33'),
(100, 8, 'view_bactrack', 0, '2026-06-17 03:29:33');

-- --------------------------------------------------------

--
-- Table structure for table `signatory_tasks`
--

CREATE TABLE `signatory_tasks` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `task_type` enum('payroll_prep','dv_ors_prep') NOT NULL,
  `status` enum('pending','completed') NOT NULL DEFAULT 'pending',
  `completed_by` int(11) DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `signatory_tasks`
--

INSERT INTO `signatory_tasks` (`id`, `transaction_id`, `task_type`, `status`, `completed_by`, `completed_at`, `document_path`, `remarks`, `created_at`) VALUES
(1, 1, 'payroll_prep', 'completed', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(2, 1, 'dv_ors_prep', 'completed', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(3, 10004, 'payroll_prep', 'completed', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(4, 10004, 'dv_ors_prep', 'completed', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(5, 10006, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(6, 10006, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(7, 10007, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(8, 10007, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(9, 10008, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(10, 10008, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(11, 10010, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(12, 10010, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(13, 10012, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(14, 10012, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(15, 10013, 'payroll_prep', 'completed', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(16, 10013, 'dv_ors_prep', 'completed', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(17, 10014, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(18, 10014, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(19, 10015, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(20, 10015, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(21, 10016, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(22, 10016, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(23, 10017, 'payroll_prep', 'completed', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(24, 10017, 'dv_ors_prep', 'completed', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(25, 10018, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(26, 10018, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(27, 10019, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(28, 10019, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(29, 10020, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(30, 10020, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(31, 10021, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(32, 10021, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(33, 10022, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(34, 10022, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(35, 10023, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(36, 10023, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(37, 10024, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(38, 10024, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(39, 10025, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(40, 10025, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(41, 10026, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(42, 10026, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(43, 10027, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(44, 10027, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(45, 10028, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(46, 10028, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(47, 10029, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(48, 10029, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 03:30:14'),
(111, 10040, 'payroll_prep', 'completed', 7, '2026-06-17 03:57:07', 'uploads/signatory-docs/10040_payroll_prep_1781668627.pdf', '', '2026-06-17 03:52:12'),
(112, 10040, 'dv_ors_prep', 'completed', 7, '2026-06-17 03:57:13', 'uploads/signatory-docs/10040_dv_ors_prep_1781668633.docx', '', '2026-06-17 03:52:12'),
(113, 10041, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 06:02:33'),
(114, 10041, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 06:02:33'),
(115, 10042, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 06:08:49'),
(116, 10042, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 06:08:49'),
(117, 10043, 'payroll_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 06:18:19'),
(118, 10043, 'dv_ors_prep', 'pending', NULL, NULL, NULL, NULL, '2026-06-17 06:18:19'),
(119, 10044, 'payroll_prep', 'completed', 6, '2026-06-17 06:32:34', 'uploads/signatory-docs/10044_payroll_prep_1781677954.pdf', '', '2026-06-17 06:30:54'),
(120, 10044, 'dv_ors_prep', 'completed', 6, '2026-06-17 06:32:41', 'uploads/signatory-docs/10044_dv_ors_prep_1781677961.docx', '', '2026-06-17 06:30:54'),
(121, 10045, 'payroll_prep', 'completed', 7, '2026-06-17 06:48:24', 'uploads/signatory-docs/10045_payroll_prep_1781678904.xlsx', '', '2026-06-17 06:45:16'),
(122, 10045, 'dv_ors_prep', 'completed', 7, '2026-06-17 06:48:29', 'uploads/signatory-docs/10045_dv_ors_prep_1781678909.xlsx', '', '2026-06-17 06:45:16');

-- --------------------------------------------------------

--
-- Table structure for table `tax_configurations`
--

CREATE TABLE `tax_configurations` (
  `id` int(11) NOT NULL,
  `tax_type` varchar(50) NOT NULL,
  `tax_percentage` decimal(5,2) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tax_configurations`
--

INSERT INTO `tax_configurations` (`id`, `tax_type`, `tax_percentage`, `is_active`) VALUES
(1, 'Goods', 5.00, 1),
(2, 'Foods', 2.00, 1),
(3, 'Services', 10.00, 1),
(7, 'Non - Taxable', 0.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `uuid` varchar(36) NOT NULL,
  `tracking_number` varchar(50) NOT NULL,
  `requestor_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `division_id` int(11) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `transaction_type` varchar(50) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `target_date` date DEFAULT NULL,
  `current_status` varchar(50) NOT NULL,
  `remarks` text DEFAULT NULL,
  `bac_reference_number` varchar(50) DEFAULT NULL,
  `bac_reference_id` int(11) DEFAULT NULL,
  `bac_project_number` varchar(100) DEFAULT NULL,
  `bac_procurement_type` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approval_file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `uuid`, `tracking_number`, `requestor_id`, `created_by`, `division_id`, `assigned_to`, `transaction_type`, `event_name`, `amount`, `tax_amount`, `net_amount`, `target_date`, `current_status`, `remarks`, `bac_reference_number`, `bac_reference_id`, `bac_project_number`, `bac_procurement_type`, `created_at`, `approval_file_path`) VALUES
(1, 'f135b4df-2ef7-0a3f-5f26-053c58d9d2e1', 'FAST-2026-000001', 1, 1, NULL, NULL, 'Cash Advance', 'SDO SEMINAR', 20000.00, 2000.00, 18000.00, '2026-05-31', 'Pending Cashier Release', 'TEST GG', NULL, NULL, NULL, NULL, '2026-05-28 14:44:07', NULL),
(10004, 'b0d3e6d0-7eba-6af5-f0b2-0271fc135f13', 'FAST-2026-000003', 1, 1, NULL, NULL, 'BACtrack', 'Diagnostic Test Project', 120000.00, 6000.00, 114000.00, NULL, 'Pending Cashier Release', 'TEST APPROVE', 'BT-TEST-0003', 9997, 'PR-9997', 'Goods', '2026-05-29 14:13:01', 'uploads/received-approvals/BT-TEST-0003_1780379650.pdf'),
(10006, '29de0281-507b-053e-f773-6e625973340c', 'FAST-2026-000004', 1, 1, NULL, NULL, 'BACtrack', 'Test Project with file', 250000.00, 12500.00, 237500.00, NULL, 'Pending ACCTG Support', 'Automatically generated draft from SDO-BAC procurement link: BT-TEST-0002.', 'BT-TEST-0002', 9998, 'PR-9998', 'Goods', '2026-05-29 14:14:55', 'uploads/received-approvals/PR-9998_1780064095.pdf'),
(10007, '9278cc3d-7dbb-a049-82c4-f5eda5125dcf', 'FAST-2026-000005', 1, 1, NULL, NULL, 'BACtrack', 'OSDS & ASDS Office renovation - OSDS & ASDS Office renovation', 150000.00, 7500.00, 142500.00, NULL, 'Pending ACCTG Support', 'Automatically generated draft from SDO-BAC procurement link: BTNE0-202604-007.', 'BTNE0-202604-007', 35, 'PR-0035', 'Goods', '2026-05-29 14:26:11', 'uploads/received-approvals/PR-0035_1780064771.pdf'),
(10008, '876c4d8c-3de1-12c5-acfe-9cc9e5b8268b', 'FAST-2026-000006', 1, 1, NULL, NULL, 'BACtrack', 'Office renovation for ICT unit - renovation of ICT unit for tiles repair and pathway', 150000.00, 7500.00, 142500.00, NULL, 'Pending ACCTG Support', 'Automatically generated draft from SDO-BAC procurement link: BTJOR-202604-006.', 'BTJOR-202604-006', 34, 'PR-0034', 'Goods', '2026-05-29 14:52:44', 'uploads/received-approvals/PR-0034_1780066364.pdf'),
(10010, '38f32722-cd94-252a-2dfe-3d64a36d8140', 'FAST-2026-000007', 1, 1, NULL, NULL, 'BACtrack', 'Building for OSDS - OSDS BUILDING', 150000.00, 7500.00, 142500.00, NULL, 'Pending ACCTG Support', 'Automatically generated draft from SDO-BAC procurement link: BTLWD-202604-005.', 'BTLWD-202604-005', 33, 'PR-0033', 'Goods', '2026-05-29 15:47:22', 'uploads/received-approvals/BTLWD-202604-005_1780071872.pdf'),
(10011, 'cf04252e-dc90-6121-9654-f594b3fbacd4', 'FAST-2026-000008', 5, 5, NULL, NULL, 'Reimbursement', 'PALARONG PAMBANSA EXPENSES', 15000.00, 1500.00, 13500.00, '2026-05-29', 'Returned', 'not needed anymore', NULL, NULL, NULL, NULL, '2026-05-29 16:33:44', NULL),
(10012, '223add1e-838c-b895-7fce-1eb47dfb7a4c', 'FAST-2026-000009', 1, 1, NULL, NULL, 'BACtrack', 'Building for CID', 150000.00, 7500.00, 142500.00, NULL, 'Pending Accountant', 'okay noted', 'BTK1E-202604-004', 32, 'BTK1E-202604-004', 'PUBLIC_BIDDING', '2026-05-29 17:00:16', 'uploads/received-approvals/BTK1E-202604-004_1780074016.pdf'),
(10013, 'ac8657b2-eb68-d093-5815-e13db291ac56', 'FAST-2026-000010', 1, 1, NULL, NULL, 'BACtrack', 'NEGOTIATED_PROCUREMENT', 150000.00, 7500.00, 142500.00, NULL, 'Pending Cashier Release', 'gege', 'PR-0023', 23, 'PR-0023', 'NEGOTIATED_PROCUREMENT', '2026-05-29 18:09:16', 'uploads/received-approvals/PR-0023_1780078156.pdf'),
(10014, '689ca3ef-6a9e-50d7-a5c4-ab4398a39d00', 'FAST-2026-000011', 1, NULL, NULL, NULL, 'BACtrack', 'Building for SGOD', 150000.00, 7500.00, 142500.00, NULL, 'Pending ACCTG Support', 'Automatically generated draft from SDO-BAC procurement link: BTKKX-202604-003.', 'BTKKX-202604-003', 31, 'BTKKX-202604-003', 'PUBLIC_BIDDING', '2026-06-04 07:55:04', 'uploads/received-approvals/BTKKX-202604-003_1780559704.pdf'),
(10015, 'c850b638-79f4-7f8b-d00e-796005a9b171', 'FAST-2026-000012', 1, NULL, NULL, NULL, 'BACtrack', 'Procurement of supply and materials for the conduct of SDO Aral Implementation', 150000.00, 7500.00, 142500.00, NULL, 'Pending ACCTG Support', 'Automatically generated draft from SDO-BAC procurement link: BTICY-202606-001.', 'BTICY-202606-001', 36, 'BTICY-202606-001', 'SMALL_VALUE_PROCUREMENT', '2026-06-11 01:40:53', 'uploads/received-approvals/BTICY-202606-001_1781142053.pdf'),
(10016, 'd3889b2e-e3d6-a248-c859-117833424c9c', 'FAST-2026-000013', 9, 9, NULL, NULL, 'Cash Advance', 'PALARONG PAMBANSA EXPENSES', 10000.00, 1000.00, 9000.00, '2026-07-03', 'Pending Signatories', 'adssdasdadsasda', NULL, NULL, NULL, NULL, '2026-06-11 03:08:20', NULL),
(10017, '7ffed834-e82d-e63c-9c07-b53acfc4e97f', 'FAST-2026-000014', 9, 9, NULL, NULL, 'Cash Advance', 'SDO SEMINAR', 5000.00, 500.00, 4500.00, '2026-06-21', 'Pending Cashier Release', 'ok', NULL, NULL, NULL, NULL, '2026-06-11 03:16:06', NULL),
(10018, '7e7082fb-01c5-a896-41ca-21cc234c7277', 'FAST-2026-000015', 5, 5, NULL, NULL, 'Reimbursement', 'Automated CLI Verification Reimbursement 1781159265', 1500.00, 150.00, 1350.00, '2026-06-18', 'Pending ACCTG Support', 'Automated CLI verification testing sequence.', NULL, NULL, NULL, NULL, '2026-06-11 06:27:45', NULL),
(10019, '126f9951-5748-62c4-7092-ec489f3f4c9f', 'FAST-2026-000016', 5, 5, NULL, NULL, 'Reimbursement', 'Automated CLI Verification Reimbursement 1781159265', 1500.00, 150.00, 1350.00, '2026-06-18', 'Pending ACCTG Support', 'Automated CLI verification testing sequence.', NULL, NULL, NULL, NULL, '2026-06-11 06:27:45', NULL),
(10020, '49d92aa3-307d-a985-feb0-3553d362615d', 'FAST-2026-000017', 1, 1, NULL, NULL, 'Reimbursement', 'PALARONG PAMBANSA EXPENSES', 80000.00, 4000.00, 76000.00, '2026-06-30', 'Pending Budget', 'ok', NULL, NULL, NULL, NULL, '2026-06-11 08:45:22', NULL),
(10021, 'ab2b5edb-ebdc-df9d-010c-2cee0e45d25b', 'FAST-2026-000018', 1, 1, NULL, NULL, 'Cash Advance', 'PALARONG PAMBANSA EXPENSES', 50000.00, 1000.00, 49000.00, '2026-06-26', 'Pending ACCTG Support', '', NULL, NULL, NULL, NULL, '2026-06-11 09:00:06', NULL),
(10022, '912f66f5-aa6d-1b3c-faee-0d5f83df2ff6', 'FAST-2026-000019', 1, 1, NULL, NULL, 'Cash Advance', 'Test Travel Cash Advance DM 214', 12000.00, 600.00, 11400.00, NULL, 'Returned', 'no attach documents', NULL, NULL, NULL, NULL, '2026-06-16 10:54:08', NULL),
(10023, '65128072-e205-2ada-4d27-39c22c84f1a4', 'FAST-2026-000020', 1, 1, NULL, NULL, 'Cash Advance', 'Test Training Cash Advance DM 214', 25000.00, 1250.00, 23750.00, NULL, 'Pending ACCTG Support', 'Seeded for DM 214 checklist testing', NULL, NULL, NULL, NULL, '2026-06-16 10:54:08', NULL),
(10024, '7c0d06a1-5a96-260a-d9e2-dbd237521af4', 'FAST-2026-000021', 1, 1, NULL, NULL, 'Reimbursement', 'Test Travel Reimbursement DM 214', 8500.00, 425.00, 8075.00, NULL, 'Pending ACCTG Support', 'Seeded for DM 214 checklist testing', NULL, NULL, NULL, NULL, '2026-06-16 10:54:08', NULL),
(10025, 'f201b54f-8b0a-8c5d-2b4f-2eaeb305c33d', 'FAST-2026-000022', 1, 1, NULL, NULL, 'Reimbursement', 'Test Communication Reimbursement DM 214', 1500.00, 75.00, 1425.00, NULL, 'Pending ACCTG Support', 'Seeded for DM 214 checklist testing', NULL, NULL, NULL, NULL, '2026-06-16 10:54:08', NULL),
(10026, '5fa4862a-6f35-728b-f2b6-0faefa3e7d89', 'FAST-2026-000023', 1, 1, NULL, NULL, 'Cash Advance', 'Test Travel Cash Advance DM 214', 12000.00, 600.00, 11400.00, NULL, 'Pending ACCTG Support', 'Seeded for DM 214 checklist testing', NULL, NULL, NULL, NULL, '2026-06-16 10:54:08', NULL),
(10027, 'b757afe2-f4e0-9f93-458c-9cc6e2a8dfd2', 'FAST-2026-000024', 1, 1, NULL, NULL, 'Cash Advance', 'Test Training Cash Advance DM 214', 25000.00, 1250.00, 23750.00, NULL, 'Pending ACCTG Support', 'Seeded for DM 214 checklist testing', NULL, NULL, NULL, NULL, '2026-06-16 10:54:08', NULL),
(10028, '2b689e21-30ca-e7c4-a84f-25ba12afc640', 'FAST-2026-000025', 1, 1, NULL, NULL, 'Reimbursement', 'Test Travel Reimbursement DM 214', 8500.00, 425.00, 8075.00, NULL, 'Pending ACCTG Support', 'Seeded for DM 214 checklist testing', NULL, NULL, NULL, NULL, '2026-06-16 10:54:08', NULL),
(10029, 'bca98598-50da-c523-5500-929ffd576637', 'FAST-2026-000026', 1, 1, NULL, NULL, 'Reimbursement', 'Test Communication Reimbursement DM 214', 1500.00, 75.00, 1425.00, NULL, 'Pending ACCTG Support', 'Seeded for DM 214 checklist testing', NULL, NULL, NULL, NULL, '2026-06-16 10:54:08', NULL),
(10040, 'cf064dfd-2d6d-e757-2fc0-81374de970e5', 'FAST-2026-000027', 9, 9, NULL, NULL, 'Cash Advance', 'test event', 10000.00, 1000.00, 9000.00, NULL, 'Released', 'notedddddddddd', NULL, NULL, NULL, NULL, '2026-06-17 03:52:12', NULL),
(10041, 'b2707077-5c75-8d71-7741-e84ea77d149b', 'FAST-2026-000028', 10, 10, NULL, NULL, 'Reimbursement', 'seminar meals', 100000.00, 0.00, 100000.00, '2026-06-26', 'Pending ACCTG Support', '', NULL, NULL, NULL, NULL, '2026-06-17 06:02:33', NULL),
(10042, '6c4208ae-da50-8cf3-cdf0-4ea6abce2b20', 'FAST-2026-000029', 10, 10, NULL, NULL, 'Cash Advance', 'testtt', 112031023.00, 0.00, 112031023.00, '2026-06-26', 'Pending ACCTG Support', '', NULL, NULL, NULL, NULL, '2026-06-17 06:08:49', NULL),
(10043, '61e0d88f-4d56-c78a-5a76-c11a63d21f8a', 'FAST-2026-000030', 10, 10, NULL, NULL, 'Reimbursement', 'testtt eveeent', 123123123.00, 0.00, 123123123.00, '2026-06-24', 'Pending ACCTG Support', '', NULL, NULL, NULL, NULL, '2026-06-17 06:18:19', NULL),
(10044, '4ea7c55b-44d4-4a4c-7ce2-6c0d7735a628', 'FAST-2026-000031', 10, 10, NULL, NULL, 'Reimbursement', 'sahod', 14000.00, 700.00, 13300.00, '2026-06-19', 'Released', 'sahod kana boss', NULL, NULL, NULL, NULL, '2026-06-17 06:30:54', NULL),
(10045, '6089d30a-1fa1-e7f9-05f8-eb09b9fd6c31', 'FAST-2026-000032', 10, 10, NULL, NULL, 'Cash Advance', 'test event local', 28000.00, 1400.00, 26600.00, '2026-06-27', 'Released', 'release', NULL, NULL, NULL, NULL, '2026-06-17 06:45:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_documents`
--

CREATE TABLE `transaction_documents` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL DEFAULT 0,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction_documents`
--

INSERT INTO `transaction_documents` (`id`, `transaction_id`, `category`, `file_path`, `original_name`, `file_size`, `uploaded_at`) VALUES
(6, 10006, 'purchase_request', 'uploads/received-approvals/PR-9998_1780064095.pdf', 'approval_proof_test.pdf', 51, '2026-05-29 14:14:55'),
(7, 10007, 'purchase_request', 'uploads/received-approvals/PR-0035_1780064771.pdf', 'mock_approval.pdf', 29, '2026-05-29 14:26:11'),
(8, 10008, 'purchase_request', 'uploads/received-approvals/PR-0034_1780066364.pdf', '34_purchase_request_1780065130.pdf', 665356, '2026-05-29 14:52:44'),
(11, 10010, 'purchase_request', 'uploads/procurement-docs/10010_purchase_request_1780071872.pdf', 'ESCALL-DTR (MAY4-19).pdf', 322007, '2026-05-29 16:24:32'),
(12, 10012, 'purchase_request', 'uploads/procurement-docs/10012_purchase_request_1780074016.pdf', 'revised-barangay-id-Edison_M__Amante_and_Lara_Jill_M__Alon_-_alon.pdf', 679836, '2026-05-29 17:00:16'),
(13, 10013, 'purchase_request', 'uploads/procurement-docs/10013_purchase_request_1780078156.pdf', 'barangay-id-virgilio_J_amado_and_gloria_salvador.pdf', 665379, '2026-05-29 18:09:16'),
(14, 10004, 'purchase_request', 'uploads/procurement-docs/10004_purchase_request_1780379650.pdf', 'GEN-DTR.pdf', 246289, '2026-06-02 05:54:10'),
(15, 10014, 'purchase_request', 'uploads/procurement-docs/10014_purchase_request_1780559704.pdf', 'barangay-id-Andoy_G__Manabit.pdf', 411565, '2026-06-04 07:55:04'),
(16, 10015, 'purchase_request', 'uploads/procurement-docs/10015_purchase_request_1781142053.png', 'fast dfd based on prototype.png', 1052468, '2026-06-11 01:40:53');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_status_logs`
--

CREATE TABLE `transaction_status_logs` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `previous_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `changed_by` int(11) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction_status_logs`
--

INSERT INTO `transaction_status_logs` (`id`, `transaction_id`, `previous_status`, `new_status`, `changed_by`, `remarks`, `created_at`) VALUES
(1, 1, NULL, 'Pending ACCTG Support', 1, 'Initial submission by requestor.', '2026-05-28 14:44:07'),
(2, 1, 'Pending ACCTG Support', 'Pending Accountant', 1, 'test gg', '2026-05-29 14:02:29'),
(3, 1, 'Pending Accountant', 'Pending Signatories', 1, 'TEST GG', '2026-05-29 14:03:03'),
(4, 1, 'Pending Signatories', 'Pending Cashier Release', 1, 'TEST GG', '2026-05-29 14:03:12'),
(9, 10004, NULL, 'Pending ACCTG Support', 1, 'Integration Sync: Link established with SDO-BAC procurement reference: BT-TEST-0003.', '2026-05-29 14:13:01'),
(10, 10004, 'Pending ACCTG Support', 'Pending Accountant', 1, 'TEST', '2026-05-29 14:13:32'),
(11, 10004, 'Pending Accountant', 'Pending Signatories', 1, 'TEST', '2026-05-29 14:13:49'),
(12, 10004, 'Pending Signatories', 'Pending Cashier Release', 1, 'TEST APPROVE', '2026-05-29 14:13:58'),
(13, 10004, 'Pending Cashier Release', 'Pending Cashier Release', 1, 'TEST APPROVE', '2026-05-29 14:14:00'),
(15, 10006, NULL, 'Pending ACCTG Support', 1, 'Integration Sync: Link established with SDO-BAC procurement reference: BT-TEST-0002.', '2026-05-29 14:14:55'),
(16, 10007, NULL, 'Pending ACCTG Support', 1, 'Integration Sync: Link established with SDO-BAC procurement reference: BTNE0-202604-007.', '2026-05-29 14:26:11'),
(17, 10008, NULL, 'Pending ACCTG Support', 1, 'Integration Sync: Link established with SDO-BAC procurement reference: BTJOR-202604-006.', '2026-05-29 14:52:44'),
(19, 10010, NULL, 'Pending ACCTG Support', 1, 'Integration Sync: Link established with SDO-BAC procurement reference: BTLWD-202604-005.', '2026-05-29 15:47:22'),
(20, 10011, NULL, 'Pending ACCTG Support', 5, 'Initial submission by requestor.', '2026-05-29 16:33:44'),
(21, 10012, NULL, 'Pending ACCTG Support', 1, 'Integration Sync: Link established with SDO-BAC procurement reference: BTK1E-202604-004.', '2026-05-29 17:00:16'),
(22, 10013, NULL, 'Pending ACCTG Support', 1, 'Integration Sync: Link established with SDO-BAC procurement reference: PR-0023.', '2026-05-29 18:09:16'),
(23, 10013, 'Pending ACCTG Support', 'Pending Accountant', 8, 'okay noted', '2026-05-31 16:43:11'),
(24, 10013, 'Pending Accountant', 'Pending Signatories', 2, 'NOTED', '2026-05-31 16:44:17'),
(25, 10013, 'Pending Signatories', 'Pending Cashier Release', 7, 'gege', '2026-05-31 16:44:54'),
(26, 10012, 'Pending ACCTG Support', 'Pending Accountant', 1, 'okay noted', '2026-06-02 05:54:37'),
(27, 10011, 'Pending ACCTG Support', 'Returned', 1, 'not needed anymore', '2026-06-02 05:54:53'),
(28, 10014, NULL, 'Pending ACCTG Support', 1, 'Integration Sync: Link established with SDO-BAC procurement reference: BTKKX-202604-003.', '2026-06-04 07:55:04'),
(29, 10015, NULL, 'Pending ACCTG Support', 1, 'Integration Sync: Link established with SDO-BAC procurement reference: BTICY-202606-001.', '2026-06-11 01:40:53'),
(30, 10016, NULL, 'Pending ACCTG Support', 9, 'Initial submission by requestor.', '2026-06-11 03:08:20'),
(31, 10017, NULL, 'Pending ACCTG Support', 9, 'Initial submission by requestor.', '2026-06-11 03:16:06'),
(32, 10017, 'Pending ACCTG Support', 'Pending ACCTG Support', 2, 'PA DOUBLE CHECK NG MAAYOS', '2026-06-11 03:16:57'),
(33, 10017, 'Pending ACCTG Support', 'Pending Budget', 8, 'okay na', '2026-06-11 03:29:17'),
(34, 10017, 'Pending Budget', 'Pending ACCT Support', 3, 'okay naaaa', '2026-06-11 03:30:26'),
(35, 10017, 'Pending ACCT Support', 'Pending Signatories', 2, 'boss', '2026-06-11 03:31:00'),
(36, 10017, 'Pending Signatories', 'Pending Cashier Release', 6, 'ok', '2026-06-11 03:31:38'),
(37, 10016, 'Pending ACCTG Support', 'Pending Budget', 8, 'okay', '2026-06-11 06:15:09'),
(38, 10016, 'Pending Budget', 'Pending ACCT Support', 3, 'ok', '2026-06-11 06:24:32'),
(39, 10018, NULL, 'Pending ACCTG Support', 5, 'Initial submission by requestor.', '2026-06-11 06:27:45'),
(40, 10019, NULL, 'Pending ACCTG Support', 5, 'Initial submission by requestor.', '2026-06-11 06:27:45'),
(41, 10020, NULL, 'Pending ACCTG Support', 1, 'Initial submission by requestor.', '2026-06-11 08:45:22'),
(42, 10020, 'Pending ACCTG Support', 'Pending ACCTG Support', 2, 'ok', '2026-06-11 08:45:56'),
(43, 10020, 'Pending ACCTG Support', 'Pending Budget', 8, 'ok', '2026-06-11 08:46:12'),
(44, 10021, NULL, 'Pending ACCTG Support', 1, 'Initial submission by requestor.', '2026-06-11 09:00:06'),
(45, 10022, NULL, 'Pending ACCTG Support', 1, 'Seeded', '2026-06-16 10:54:08'),
(46, 10023, NULL, 'Pending ACCTG Support', 1, 'Seeded', '2026-06-16 10:54:08'),
(47, 10024, NULL, 'Pending ACCTG Support', 1, 'Seeded', '2026-06-16 10:54:08'),
(48, 10025, NULL, 'Pending ACCTG Support', 1, 'Seeded', '2026-06-16 10:54:08'),
(49, 10026, NULL, 'Pending ACCTG Support', 1, 'Seeded', '2026-06-16 10:54:08'),
(50, 10027, NULL, 'Pending ACCTG Support', 1, 'Seeded', '2026-06-16 10:54:08'),
(51, 10028, NULL, 'Pending ACCTG Support', 1, 'Seeded', '2026-06-16 10:54:08'),
(52, 10029, NULL, 'Pending ACCTG Support', 1, 'Seeded', '2026-06-16 10:54:08'),
(65, 10040, NULL, 'Pending ACCTG Support', 9, 'Initial submission by requestor.', '2026-06-17 03:52:12'),
(66, 10040, 'Pending ACCTG Support', 'Pending Budget', 8, 'All attachments approved and tax classification set. Auto-advanced to Stage 3 (Budget).', '2026-06-17 03:54:15'),
(67, 10022, 'Pending ACCTG Support', 'Returned', 8, 'no attach documents', '2026-06-17 03:54:38'),
(68, 10040, 'Pending Budget', 'Pending ACCT Support', 3, 'Budget approved. Fund source: MOOE. okay sige', '2026-06-17 03:55:34'),
(69, 10040, 'Pending ACCT Support', 'Pending Signatories', 8, 'noted test', '2026-06-17 03:56:08'),
(70, 10040, 'Pending Signatories', 'Pending Cashier Release', 7, 'Both signatory tasks completed. Auto-advanced to Stage 6 (Cashier Release).', '2026-06-17 03:57:13'),
(71, 10040, 'Pending Cashier Release', 'Released', 12, 'notedddddddddd', '2026-06-17 04:07:39'),
(72, 10041, NULL, 'Pending ACCTG Support', 10, 'Initial submission by requestor.', '2026-06-17 06:02:33'),
(73, 10042, NULL, 'Pending ACCTG Support', 10, 'Initial submission by requestor.', '2026-06-17 06:08:49'),
(74, 10043, NULL, 'Pending ACCTG Support', 10, 'Initial submission by requestor.', '2026-06-17 06:18:19'),
(75, 10016, 'Pending ACCT Support', 'Pending Signatories', 8, 'adssdasdadsasda', '2026-06-17 06:21:22'),
(76, 10044, NULL, 'Pending ACCTG Support', 10, 'Initial submission by requestor.', '2026-06-17 06:30:54'),
(77, 10044, 'Pending ACCTG Support', 'Pending Budget', 8, 'All attachments approved and tax classification set. Auto-advanced to Stage 3 (Budget).', '2026-06-17 06:31:17'),
(78, 10044, 'Pending Budget', 'Pending ACCT Support', 3, 'Budget approved. Fund source: mooe. noted to', '2026-06-17 06:31:40'),
(79, 10044, 'Pending ACCT Support', 'Pending Signatories', 8, 'noted boss', '2026-06-17 06:32:04'),
(80, 10044, 'Pending Signatories', 'Pending Cashier Release', 6, 'Both signatory tasks completed. Auto-advanced to Stage 6 (Cashier Release).', '2026-06-17 06:32:41'),
(81, 10044, 'Pending Cashier Release', 'Released', 12, 'sahod kana boss', '2026-06-17 06:32:59'),
(82, 10045, NULL, 'Pending ACCTG Support', 10, 'Initial submission by requestor.', '2026-06-17 06:45:16'),
(83, 10045, 'Pending ACCTG Support', 'Pending Budget', 8, 'Tax classification set to \'Goods\'. All attachments approved. Auto-advanced to Stage 3 (Budget).', '2026-06-17 06:46:44'),
(84, 10045, 'Pending Budget', 'Pending ACCT Support', 3, 'Budget approved. Fund source: MOOE. copy noted', '2026-06-17 06:47:21'),
(85, 10045, 'Pending ACCT Support', 'Pending Signatories', 8, 'for signatory', '2026-06-17 06:48:00'),
(86, 10045, 'Pending Signatories', 'Pending Cashier Release', 7, 'Both signatory tasks completed. Auto-advanced to Stage 6 (Cashier Release).', '2026-06-17 06:48:29'),
(87, 10045, 'Pending Cashier Release', 'Released', 12, 'release', '2026-06-17 06:48:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `uuid` varchar(36) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `office` varchar(100) DEFAULT NULL,
  `unit_section` varchar(100) DEFAULT NULL,
  `employee_no` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `position_id` int(11) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `uuid`, `full_name`, `email`, `office`, `unit_section`, `employee_no`, `position`, `username`, `position_id`, `password`, `status`, `created_at`) VALUES
(1, 'a6b334d7-4632-404c-bb49-3351ecdfdf01', 'FAST Super Admin', 'fastsdo@gmail.com', NULL, NULL, NULL, NULL, 'Superadmin', 7, '$2y$12$tcVe9iUlENvg3h.dxzuOUOeF5ZJ.RG8Ttzl08J6oBAti73haOX4.K', 'active', '2026-05-28 12:03:03'),
(2, 'b7c445e8-5743-515d-cc5a-4462fdeeff02', 'Accountant', 'accountant@gmail.com', 'OSDS', 'Accounting (Finance - Accounting)', NULL, 'Accountant', 'Accountant', 2, '$2y$12$0.9YIPKPTsrvJxaAZ8Pry.0Xi2bMVG9du9oEGjvS3x.JeagWjZzYu', 'active', '2026-05-28 12:03:03'),
(3, 'c8d556f9-6854-626e-dd6b-5573aeff0003', 'Budget Officer', 'budget@gmail.com', 'OSDS', 'Budget (Finance - Budget)', NULL, 'Budget Officer', 'budget', 4, '$2y$12$P/UVx4iovAt9FmWEl824Ium1Zu.WOu.jpLG0cUNcNJVfSbHl/YI8.', 'active', '2026-05-28 12:03:03'),
(5, 'e0f778b1-8a76-8480-ff8d-7795ce112205', 'BAC Requestor', 'bac@gmail.com', NULL, NULL, NULL, NULL, 'BAC requestor', 1, '$2y$12$0Zo6f.JORfH81VJ2ogeGSOZWW6YIJZeLhfhbNDQfqNI8j4atqVJHS', 'active', '2026-05-28 12:03:03'),
(6, '1cc653c2-f1e0-17ee-9a65-b682402fcfac', 'Phillip B. Gallendez', 'sds@gmail.com', 'OSDS', 'OSDS (Office of the SDS Staff)', NULL, 'OIC - SDS', 'SDS', 6, '$2y$12$DvQcfezXELqKBi92/lNX1O4onnVTK2IWoc0B9l/Jv3pS/Or38cATa', 'active', '2026-05-28 13:26:00'),
(7, 'adc31af5-d12c-c223-21ca-14d9de88b907', 'Joe Bren L. Consuelo', 'asds@gmail.com', 'OSDS', 'OASDS (Office of the ASDS Staff)', NULL, 'OIC - ASDS', 'ASDS', 5, '$2y$12$pxCwBchNl1BqGjIRSg.qsOWceOmWkxgIWfPLvcS1.TmjHjuKOodH2', 'active', '2026-05-28 13:26:33'),
(8, '2fbeb071-0408-f0f0-afa8-f8f589f2e3c4', 'Accounting Support', 'acctgsupp@gmail.com', 'OSDS', 'Accounting (Finance - Accounting)', NULL, 'Accounting Support', 'Accounting support', 3, '$2y$12$D/Xn2FDjyyeqrz4TBgaXT.tbonv/EMbr3B2f2Ld89b4tj0c3FLg66', 'active', '2026-05-28 13:34:51'),
(9, 'f321590b-6818-3739-cda1-6d55944e29ec', 'Alexander Joerenz Escallente', 'joerenz.dev@gmail.com', 'OSDS', 'ICT', '1000045', 'LSB Clerk', 'alex', 1, '$2y$12$/Tr/GBB9tlaBtP2Lx2ITyuJdqdbtO3sFr6y2r1DJRS6OMNkd5uhvy', 'active', '2026-05-31 15:42:30'),
(10, '52d1dd61-8fc8-413a-8ec6-59c5926804a7', 'Test User', 'test.user@gmail.com', 'OSDS', 'ICT', 'E-12345', NULL, 'test.user', 1, '$2y$12$ps6vrSdcNzmhQSKnZEGRe.g4JlHejEN1vkA/ALi20LeMgZd7Mp8gG', 'active', '2026-06-11 04:11:42'),
(12, '25a158ba-887d-e662-1ccf-4eda7c3bdd39', 'Cashier', 'cashier@gmail.com', 'OSDS', 'Budget (Finance - Budget)', NULL, NULL, 'cashier', 8, '$2y$12$wulN67a4dHgo5BegWXOQjuObYLpNzCcQX6YjxhW8BoBP6yCTAb3ze', 'active', '2026-06-17 03:04:46');

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permission_key` varchar(100) NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`id`, `user_id`, `permission_key`, `is_enabled`, `updated_at`) VALUES
(1, 9, 'view', 0, '2026-06-04 03:44:14'),
(2, 9, 'encode', 1, '2026-06-04 03:37:26'),
(3, 9, 'edit', 0, '2026-06-04 03:29:15'),
(4, 9, 'approve', 0, '2026-06-04 03:29:15'),
(5, 9, 'delete', 0, '2026-06-04 03:29:15'),
(6, 9, 'manage_users', 0, '2026-06-04 03:29:15'),
(7, 9, 'configure_system', 0, '2026-06-04 03:29:55'),
(29, 8, 'view', 1, '2026-06-11 03:17:47'),
(30, 8, 'encode', 1, '2026-06-11 03:17:47'),
(31, 8, 'edit', 1, '2026-06-11 03:17:47'),
(32, 8, 'approve', 1, '2026-06-11 03:17:47'),
(33, 8, 'delete', 0, '2026-06-11 03:17:47'),
(34, 8, 'manage_users', 0, '2026-06-11 03:17:47'),
(35, 8, 'configure_system', 0, '2026-06-11 03:17:47');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES
(1, 1),
(2, 2),
(3, 3),
(5, 7),
(6, 4),
(7, 4),
(8, 2),
(9, 5),
(10, 5),
(12, 8);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `attachment_approvals`
--
ALTER TABLE `attachment_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviewed_by` (`reviewed_by`),
  ADD KEY `idx_aa_tx_status` (`transaction_id`,`status`);

--
-- Indexes for table `bac_sync_logs`
--
ALTER TABLE `bac_sync_logs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bac_reference_id` (`bac_reference_id`),
  ADD KEY `synced_by` (`synced_by`);

--
-- Indexes for table `budget_checks`
--
ALTER TABLE `budget_checks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `budget_tx_unique` (`transaction_id`),
  ADD KEY `checked_by` (`checked_by`);

--
-- Indexes for table `cash_advance_details`
--
ALTER TABLE `cash_advance_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `chatbot_logs`
--
ALTER TABLE `chatbot_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `document_details`
--
ALTER TABLE `document_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `integration_logs`
--
ALTER TABLE `integration_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `integration_tokens`
--
ALTER TABLE `integration_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_name` (`system_name`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permission_key` (`permission_key`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `position_name` (`position_name`);

--
-- Indexes for table `reimbursement_details`
--
ALTER TABLE `reimbursement_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `role_data_scope`
--
ALTER TABLE `role_data_scope`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role` (`role`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_permission_unique` (`role_id`,`permission_key`);

--
-- Indexes for table `signatory_tasks`
--
ALTER TABLE `signatory_tasks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `st_tx_task_unique` (`transaction_id`,`task_type`),
  ADD KEY `completed_by` (`completed_by`),
  ADD KEY `idx_st_tx_status` (`transaction_id`,`status`);

--
-- Indexes for table `tax_configurations`
--
ALTER TABLE `tax_configurations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tax_type` (`tax_type`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `tracking_number` (`tracking_number`),
  ADD KEY `requestor_id` (`requestor_id`);

--
-- Indexes for table `transaction_documents`
--
ALTER TABLE `transaction_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tx_id` (`transaction_id`),
  ADD KEY `idx_cat` (`category`);

--
-- Indexes for table `transaction_status_logs`
--
ALTER TABLE `transaction_status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_permission` (`user_id`,`permission_key`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=541;

--
-- AUTO_INCREMENT for table `attachment_approvals`
--
ALTER TABLE `attachment_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `bac_sync_logs`
--
ALTER TABLE `bac_sync_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `budget_checks`
--
ALTER TABLE `budget_checks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cash_advance_details`
--
ALTER TABLE `cash_advance_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `chatbot_logs`
--
ALTER TABLE `chatbot_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `document_details`
--
ALTER TABLE `document_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `integration_logs`
--
ALTER TABLE `integration_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `integration_tokens`
--
ALTER TABLE `integration_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=212;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reimbursement_details`
--
ALTER TABLE `reimbursement_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `role_data_scope`
--
ALTER TABLE `role_data_scope`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT for table `signatory_tasks`
--
ALTER TABLE `signatory_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=181;

--
-- AUTO_INCREMENT for table `tax_configurations`
--
ALTER TABLE `tax_configurations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10046;

--
-- AUTO_INCREMENT for table `transaction_documents`
--
ALTER TABLE `transaction_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `transaction_status_logs`
--
ALTER TABLE `transaction_status_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `attachment_approvals`
--
ALTER TABLE `attachment_approvals`
  ADD CONSTRAINT `attachment_approvals_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attachment_approvals_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bac_sync_logs`
--
ALTER TABLE `bac_sync_logs`
  ADD CONSTRAINT `bac_sync_logs_ibfk_1` FOREIGN KEY (`synced_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `budget_checks`
--
ALTER TABLE `budget_checks`
  ADD CONSTRAINT `budget_checks_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `budget_checks_ibfk_2` FOREIGN KEY (`checked_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `cash_advance_details`
--
ALTER TABLE `cash_advance_details`
  ADD CONSTRAINT `cash_advance_details_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chatbot_logs`
--
ALTER TABLE `chatbot_logs`
  ADD CONSTRAINT `chatbot_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `document_details`
--
ALTER TABLE `document_details`
  ADD CONSTRAINT `document_details_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD CONSTRAINT `login_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD CONSTRAINT `password_reset_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reimbursement_details`
--
ALTER TABLE `reimbursement_details`
  ADD CONSTRAINT `reimbursement_details_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `signatory_tasks`
--
ALTER TABLE `signatory_tasks`
  ADD CONSTRAINT `signatory_tasks_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `signatory_tasks_ibfk_2` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`requestor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `transaction_documents`
--
ALTER TABLE `transaction_documents`
  ADD CONSTRAINT `transaction_documents_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_status_logs`
--
ALTER TABLE `transaction_status_logs`
  ADD CONSTRAINT `transaction_status_logs_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_status_logs_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `user_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
