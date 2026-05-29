-- Migration: Add procurement_checklist column to document_details
-- Date: 2026-05-29
-- Purpose: Store checklist data from SDO-BACtrack procurement approvals as JSON
-- Run this once against fast_db to add the column.

ALTER TABLE `document_details` ADD COLUMN IF NOT EXISTS `procurement_checklist` JSON DEFAULT NULL AFTER `attachment_path`;
