-- Run once in phpMyAdmin > mira_artcraft > SQL, AFTER backing up your database.
-- Existing artworks, users and categories are preserved.
ALTER TABLE artworks ADD COLUMN rejection_reason TEXT NULL;
ALTER TABLE artworks ADD COLUMN reviewed_at DATETIME NULL;
ALTER TABLE artworks ADD COLUMN reviewed_by INT NULL;
CREATE TABLE IF NOT EXISTS artwork_images (
 image_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
 artwork_id INT NOT NULL,
 image_path VARCHAR(255) NOT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_mira_image_artwork FOREIGN KEY (artwork_id) REFERENCES artworks(artwork_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Set up initial categories without duplicates; feel free to edit category descriptions in phpMyAdmin.
INSERT INTO categories(category_name,description)
SELECT seed.category_name,seed.description FROM (
 SELECT 'Painting' AS category_name,'Paintings in varied media' AS description UNION ALL
 SELECT 'Sketching','Pencil, charcoal and ink art' UNION ALL
 SELECT 'Handcraft','Handmade creative works' UNION ALL
 SELECT 'Photography','Creative photography' UNION ALL
 SELECT 'Digital Art','Digital illustrations and designs' UNION ALL
 SELECT 'Sculpture','Three-dimensional works'
) seed WHERE NOT EXISTS (SELECT 1 FROM categories c WHERE LOWER(c.category_name)=LOWER(seed.category_name));
