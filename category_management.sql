-- RUN ONCE after backing up the existing mira_artcraft database.
-- Select the correct database in phpMyAdmin before running.
-- Keeps all existing category IDs and artwork foreign keys intact.
ALTER TABLE categories
  ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1,
  ADD COLUMN image_path VARCHAR(255) NULL DEFAULT NULL;
