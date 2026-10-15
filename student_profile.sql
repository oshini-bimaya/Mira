-- Run once in the selected mira_artcraft database after taking a backup.
ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) NULL DEFAULT NULL;
