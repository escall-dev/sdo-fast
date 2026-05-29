-- SDO FAST Integration: Add approval file path to transactions
-- Script: add_approval_file_path.sql

ALTER TABLE transactions
    ADD COLUMN approval_file_path VARCHAR(255) NULL;
