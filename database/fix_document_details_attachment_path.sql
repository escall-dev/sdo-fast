-- Fix: document_details.attachment_path is VARCHAR(255) but the code stores
-- a JSON array of ALL uploaded file paths, which can easily exceed 255 chars.
-- This causes "Database error during document submission" on reimbursement/CA
-- resubmissions with multiple attached documents.
ALTER TABLE `document_details` CHANGE `attachment_path` `attachment_path` TEXT NULL DEFAULT NULL;
