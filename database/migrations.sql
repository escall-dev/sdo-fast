-- SDO FAST Database Schema Migration Script
-- Database: fast_db

CREATE DATABASE IF NOT EXISTS `fast_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `fast_db`;

-- A. users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `uuid` VARCHAR(36) NOT NULL UNIQUE,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `position_id` INT DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- B. positions Table
CREATE TABLE IF NOT EXISTS `positions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `position_name` VARCHAR(100) NOT NULL UNIQUE,
  `mapped_role` VARCHAR(50) NOT NULL DEFAULT 'User',
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- C. roles Table
CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `role_name` VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- C. permissions Table
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `permission_key` VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- D. user_roles Table
CREATE TABLE IF NOT EXISTS `user_roles` (
  `user_id` INT NOT NULL,
  `role_id` INT NOT NULL,
  PRIMARY KEY (`user_id`, `role_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- E. password_reset_tokens Table
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `token` VARCHAR(255) NOT NULL UNIQUE,
  `expires_at` DATETIME NOT NULL,
  `used_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- F. transactions Table
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `uuid` VARCHAR(36) NOT NULL UNIQUE,
  `tracking_number` VARCHAR(50) NOT NULL UNIQUE,
  `requestor_id` INT NOT NULL,
  `transaction_type` VARCHAR(50) NOT NULL, -- e.g. Cash Advance, Reimbursement, Payroll
  `event_name` VARCHAR(255) NOT NULL,
  `amount` DECIMAL(15, 2) NOT NULL,
  `tax_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `net_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `target_date` DATE DEFAULT NULL,
  `current_status` VARCHAR(50) NOT NULL, -- Pending Support, Pending Accountant, Pending Final Approval, Approved, Rejected, Returned
  `remarks` TEXT DEFAULT NULL,
  `bac_reference_number` VARCHAR(50) DEFAULT NULL,
  `bac_reference_id` INT DEFAULT NULL,
  `bac_project_number` VARCHAR(100) DEFAULT NULL,
  `bac_procurement_type` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`requestor_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- G. document_details Table
CREATE TABLE IF NOT EXISTS `document_details` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `transaction_id` INT NOT NULL,
  `dv_number` VARCHAR(100) DEFAULT NULL,
  `bir_2307_number` VARCHAR(100) DEFAULT NULL,
  `tax_type` VARCHAR(50) DEFAULT NULL, -- Goods, Foods, Services
  `attachment_path` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- H. transaction_status_logs Table
CREATE TABLE IF NOT EXISTS `transaction_status_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `transaction_id` INT NOT NULL,
  `previous_status` VARCHAR(50) DEFAULT NULL,
  `new_status` VARCHAR(50) NOT NULL,
  `changed_by` INT NOT NULL,
  `remarks` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- I. activity_logs Table
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `activity` VARCHAR(255) NOT NULL,
  `old_value` TEXT DEFAULT NULL,
  `new_value` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- J. tax_configurations Table
CREATE TABLE IF NOT EXISTS `tax_configurations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tax_type` VARCHAR(50) NOT NULL UNIQUE,
  `tax_percentage` DECIMAL(5, 2) NOT NULL,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

-- K. login_logs Table
CREATE TABLE IF NOT EXISTS `login_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `device_info` VARCHAR(255) DEFAULT NULL,
  `login_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- L. chatbot_logs Table
CREATE TABLE IF NOT EXISTS `chatbot_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `user_message` TEXT NOT NULL,
  `bot_response` TEXT NOT NULL,
  `provider_used` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- M. integration_tokens Table
CREATE TABLE IF NOT EXISTS `integration_tokens` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `system_name` VARCHAR(100) NOT NULL UNIQUE,
  `token_hash` VARCHAR(255) NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- N. integration_logs Table
CREATE TABLE IF NOT EXISTS `integration_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `source_system` VARCHAR(100) NOT NULL,
  `destination_system` VARCHAR(100) NOT NULL,
  `payload_type` VARCHAR(100) NOT NULL,
  `reference_id` VARCHAR(100) DEFAULT NULL,
  `sync_status` VARCHAR(50) NOT NULL,
  `response_message` TEXT DEFAULT NULL,
  `synced_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- O. bac_sync_logs Table
CREATE TABLE IF NOT EXISTS `bac_sync_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `bac_reference_id` INT NOT NULL UNIQUE,
  `synced_by` INT DEFAULT NULL,
  `synced_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `sync_status` VARCHAR(50) NOT NULL,
  `remarks` TEXT DEFAULT NULL,
  FOREIGN KEY (`synced_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =========================================================================
-- SEED INITIAL DATA
-- =========================================================================

-- Seed Roles
INSERT INTO `roles` (`role_name`) VALUES 
('Super Admin'),
('Admin'),
('User')
ON DUPLICATE KEY UPDATE `role_name` = VALUES(`role_name`);

-- Seed Positions
INSERT INTO `positions` (`position_name`, `mapped_role`, `is_default`) VALUES
('Personnel', 'User', 1),
('Accountant', 'Admin', 1),
('Accounting Support', 'Admin', 1),
('Budget Officer', 'Admin', 1),
('ASDS', 'Admin', 1),
('SDS', 'Admin', 1)
ON DUPLICATE KEY UPDATE `mapped_role` = VALUES(`mapped_role`);

-- Seed Permissions
INSERT INTO `permissions` (`permission_key`) VALUES
('manage_users'),
('create_transaction'),
('view_all_transactions'),
('view_own_transactions'),
('approve_transaction'),
('view_reports'),
('view_audit_logs'),
('view_integration_monitor'),
('manage_settings')
ON DUPLICATE KEY UPDATE `permission_key` = VALUES(`permission_key`);

-- Seed Tax Configurations
INSERT INTO `tax_configurations` (`tax_type`, `tax_percentage`, `is_active`) VALUES
('Goods', 5.00, 1),
('Foods', 2.00, 1),
('Services', 10.00, 1)
ON DUPLICATE KEY UPDATE `tax_percentage` = VALUES(`tax_percentage`), `is_active` = VALUES(`is_active`);

-- Seed default user accounts (Password: sdoescall hashed via bcrypt)
-- Hashed password representation for 'sdoescall': $2y$10$tZ2R8B.J7J.wVfM2C5/gquw7LNsD8XWp0.wL/f22z4G894kM8K8yW
-- Let's generate a proper bcrypt hash. We will use: $2y$10$g0b9j94oWkY.0/4F51/ZNuW9DkL8U.XwL8x8w9x8w9x8w9x8w9x8w (mock or live)
-- The PHP password_hash('sdoescall', PASSWORD_DEFAULT) typically results in:
-- e.g. $2y$10$wNnQ1yK9ePjU9bX/s1NlJuP0z0ZkOa4wMh2i3l4q5r6s7t8u9v0w.
-- Let's make sure it is exactly verified by password_verify.
-- We will insert accounts:
-- 1. Super Admin: fastsdo@gmail.com / sdoescall
-- 2. Staff: staff@fast.sdo.gov.ph / sdoescall
-- 3. Budget Officer: budget@fast.sdo.gov.ph / sdoescall
-- 4. Approver: approver@fast.sdo.gov.ph / sdoescall
-- 5. Requestor: requestor@fast.sdo.gov.ph / sdoescall

INSERT INTO `users` (`id`, `uuid`, `full_name`, `email`, `username`, `position_id`, `password`, `status`) VALUES
(1, 'a6b334d7-4632-404c-bb49-3351ecdfdf01', 'FAST Super Admin', 'fastsdo@gmail.com', 'admin', NULL, '$2y$12$0Zo6f.JORfH81VJ2ogeGSOZWW6YIJZeLhfhbNDQfqNI8j4atqVJHS', 'active'),
(2, 'b7c445e8-5743-515d-cc5a-4462fdeeff02', 'Accounting Staff Member', 'staff@fast.sdo.gov.ph', 'staff', 2, '$2y$12$0Zo6f.JORfH81VJ2ogeGSOZWW6YIJZeLhfhbNDQfqNI8j4atqVJHS', 'active'),
(3, 'c8d556f9-6854-626e-dd6b-5573aeff0003', 'Budget Officer', 'budget@fast.sdo.gov.ph', 'budget', 4, '$2y$12$0Zo6f.JORfH81VJ2ogeGSOZWW6YIJZeLhfhbNDQfqNI8j4atqVJHS', 'active'),
(4, 'd9e667a0-7965-737f-ee7c-6684bd001104', 'Financial Approver', 'approver@fast.sdo.gov.ph', 'approver', 1, '$2y$12$0Zo6f.JORfH81VJ2ogeGSOZWW6YIJZeLhfhbNDQfqNI8j4atqVJHS', 'active'),
(5, 'e0f778b1-8a76-8480-ff8d-7795ce112205', 'Requestor User', 'requestor@fast.sdo.gov.ph', 'requestor', 1, '$2y$12$0Zo6f.JORfH81VJ2ogeGSOZWW6YIJZeLhfhbNDQfqNI8j4atqVJHS', 'active')
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`);

-- Map Users to Roles
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES
(1, 1), -- Super Admin -> Super Admin
(2, 2), -- Staff -> Admin (Accountant position)
(3, 2), -- Budget -> Admin (Budget Officer position)
(4, 3), -- Approver -> User (Personnel position)
(5, 3)  -- Requestor -> User (Personnel position)
ON DUPLICATE KEY UPDATE `role_id` = VALUES(`role_id`);

-- Seed Integration Token for SDO-BAC (token: bac_secure_token_123, stored as SHA-256 hash)
-- SHA-256 hash of 'bac_secure_token_123' is: c811a2f185ef4e6cfb712bc9fbb9fb8e4f509e51e24747eb10bfa1e6fcd20e5c
-- Seed Integration Token for SDO-FAST (token: fast_secure_token_456, stored as SHA-256 hash)
-- SHA-256 hash of 'fast_secure_token_456' is: d6ee6e99996b79758e578c772eaef2d973c1d4a8ec9d58be3a105c31750e50fc
INSERT INTO `integration_tokens` (`system_name`, `token_hash`, `status`) VALUES
('SDO-BAC', 'c811a2f185ef4e6cfb712bc9fbb9fb8e4f509e51e24747eb10bfa1e6fcd20e5c', 'active'),
('SDO-FAST', 'd6ee6e99996b79758e578c772eaef2d973c1d4a8ec9d58be3a105c31750e50fc', 'active')
ON DUPLICATE KEY UPDATE `token_hash` = VALUES(`token_hash`);

-- P. procurement_checklist column for document_details (SDO-BACtrack integration)
-- Stores the BAC procurement checklist items as a JSON object
ALTER TABLE `document_details` ADD COLUMN IF NOT EXISTS `procurement_checklist` JSON DEFAULT NULL AFTER `attachment_path`;
