-- Run once in phpMyAdmin, after selecting your existing mira_artcraft database.
-- This adds only the saved artworks feature; it does not delete existing data.
CREATE TABLE IF NOT EXISTS saved_artworks (
  user_id INT NOT NULL,
  artwork_id INT NOT NULL,
  saved_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, artwork_id),
  KEY idx_saved_artwork (artwork_id),
  CONSTRAINT fk_saved_user FOREIGN KEY (user_id) REFERENCES users (USER_ID) ON DELETE CASCADE,
  CONSTRAINT fk_saved_artwork FOREIGN KEY (artwork_id) REFERENCES artworks (artwork_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
