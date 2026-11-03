-- Run once in phpMyAdmin against your existing mira_artcraft database.
-- Does not delete or change existing submissions.
CREATE TABLE IF NOT EXISTS artwork_status_history (
  history_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  artwork_id INT NOT NULL,
  actor_id INT NOT NULL,
  action VARCHAR(40) NOT NULL,
  details TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_artwork_history (artwork_id, created_at),
  CONSTRAINT fk_mira_history_artwork FOREIGN KEY (artwork_id) REFERENCES artworks(artwork_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
