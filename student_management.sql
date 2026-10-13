-- Run once on the selected mira_artcraft database. Back it up first.
ALTER TABLE users
 ADD COLUMN account_status ENUM('active','suspended') NOT NULL DEFAULT 'active',
 ADD COLUMN suspension_reason VARCHAR(500) NULL,
 ADD COLUMN status_updated_at DATETIME NULL;
CREATE INDEX idx_users_role_status ON users (ROLE, account_status);
