-- Migration: Add localization columns for Amharic (am) and Afaan Oromo (om)
-- Run this in phpMyAdmin or via MySQL CLI after backing up your database.
-- Example (MySQL CLI):
--   mysql -u root -p anakoklish_db < 001_add_localization_columns.sql

-- Backup recommendation:
--   mysqldump -u root -p anakoklish_db > backup_before_localization.sql

-- Add text columns for localized question text and options.
ALTER TABLE questions
  ADD COLUMN IF NOT EXISTS question_am TEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS question_om TEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS option_a_am VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS option_a_om VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS option_b_am VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS option_b_om VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS option_c_am VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS option_c_om VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS option_d_am VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS option_d_om VARCHAR(255) DEFAULT NULL;

-- NOTE:
-- - `ADD COLUMN IF NOT EXISTS` requires MySQL 8.0.16+. If your MySQL version
--   doesn't support it, either upgrade or run individual ALTER statements
--   and ignore the 'duplicate column' errors after a successful run.
-- - After adding columns, populate `*_am` and `*_om` fields for questions
--   using phpMyAdmin or SQL `UPDATE` statements. The app will automatically
--   fall back to the English columns when localized fields are NULL.
