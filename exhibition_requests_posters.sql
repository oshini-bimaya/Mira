-- Run ONCE after backing up the selected database. Do not re-import the old database dump.
ALTER TABLE events ADD COLUMN poster_path VARCHAR(255) NULL DEFAULT NULL;
CREATE TABLE IF NOT EXISTS exhibition_requests (
 request_id INT AUTO_INCREMENT PRIMARY KEY,
 event_id INT NOT NULL,
 artwork_id INT NOT NULL,
 user_id INT NOT NULL,
 status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
 admin_note VARCHAR(1000) NULL,
 requested_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 reviewed_at DATETIME NULL,
 reviewed_by INT NULL,
 UNIQUE KEY unique_event_artwork_request (event_id,artwork_id),
 KEY idx_event_status (event_id,status),
 CONSTRAINT fk_er_event FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
 CONSTRAINT fk_er_artwork FOREIGN KEY (artwork_id) REFERENCES artworks(artwork_id) ON DELETE CASCADE,
 CONSTRAINT fk_er_student FOREIGN KEY (user_id) REFERENCES users(USER_ID) ON DELETE CASCADE,
 CONSTRAINT fk_er_admin FOREIGN KEY (reviewed_by) REFERENCES users(USER_ID) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
