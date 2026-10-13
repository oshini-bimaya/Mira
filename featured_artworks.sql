-- Run once, in the currently selected MIRA database. Existing artworks are preserved.
ALTER TABLE artworks ADD COLUMN featured_order INT NULL DEFAULT NULL;
-- Existing featured artworks will still show and can be reordered from the admin panel.
