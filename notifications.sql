-- Phase: MIRA student notification inbox. Select mira_artcraft before importing.
CREATE TABLE IF NOT EXISTS notifications (
 notification_id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 type VARCHAR(40) NOT NULL,
 title VARCHAR(180) NOT NULL,
 message VARCHAR(600) NOT NULL,
 link_path VARCHAR(255) DEFAULT NULL,
 is_read TINYINT(1) NOT NULL DEFAULT 0,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_notifications_user (user_id, is_read, created_at),
 CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(USER_ID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
