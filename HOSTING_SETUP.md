# MIRA — Combined Hosting Package

This ZIP merges both supplied codebases. It uses the more feature-complete `Mira copy(1).zip` pages as the foundation and restores missing student profile, artwork interactions, styling and image assets from `miar.zip`. The `.git` histories and the contributor account records are excluded.

## Install on XAMPP
1. Extract the **MIRA** folder into `htdocs` (e.g. `C:\xampp\htdocs\MIRA`).
2. Start Apache and MySQL. In phpMyAdmin, create an empty database `mira_artcraft` with `utf8mb4` character set.
3. Import **database/mira_complete_fresh_install.sql** into that empty database. Do not import this into a live database with existing tables.
4. Check **db.php** for your database name, username and password. For shared hosting, replace XAMPP's root/blank-password defaults with your host-issued database credentials.
5. Open `http://localhost/MIRA/`. Register a new student account using the site.
6. To create an administrator, after registering that account, run the following in phpMyAdmin, replacing the address with *your own*: `UPDATE users SET ROLE='ADMIN' WHERE EMAIL='your-admin-email@example.com';` Log out and log in again.

## Deploy to PHP/MySQL hosting
- Use hosting that supports PHP 8+ with mysqli/mysqlnd, fileinfo, and a MySQL/MariaDB database. **GitHub Pages alone cannot run PHP or MySQL**.
- Upload the **contents of the MIRA folder** to `public_html` (or place the MIRA folder under it if a `/MIRA/` address is desired). Keep the directory structure.
- Create a database and database user via your hosting control panel. Import the fresh SQL, then update **db.php** with the host's credentials.
- Ensure `uploads/artworks`, `uploads/categories`, `uploads/events`, and `uploads/profiles` are writable for PHP uploads. Do not grant unnecessarily broad permissions (avoid chmod 777).
- Use HTTPS and back up files/database regularly.

## Included application areas
Student registration and login, student homepage, category gallery, artwork upload and moderation, interactions, artist profile, student profile, exhibitions and posters, participation, submission requests, notifications, and admin categories/artwork moderation/event management/students/featured items/reports/account profile.

## Important limits
- The SQL is a **new-install database schema** reconstructed from supplied exports and application SQL references; it is not a migration of either person's running database.
- Existing user accounts, real uploads and exhibition records from a live database are **not** merged. Importing the schema will not restore those records.
- PHP syntax can be checked here, but interactive MySQL, upload, and browser tests must be run on your own configured PHP/MySQL server before publishing.
- Review security, account permissions, content and privacy before making the site publicly available.


## Artwork interactions (added)
Open a category artwork and click its image to reach `artwork-view.php`. The detail page now has working form controls for like/unlike, 1–5 star ratings, and comments (including deletion of your own comments). Click an artist name to visit `artist-profile.php` and follow/unfollow; the Artists navigation now opens `artists.php`. These features require `artwork_likes`, `comments`, `ratings`, and `follows` tables from `database/mira_complete_fresh_install.sql`. Importing the fresh-install SQL into a database containing data can overwrite or conflict with existing tables; back up data and apply a tested migration instead.

Student navigation: shared mira-student-nav.php / .css / .js on student pages. Gallery tab removed; Categories still uses gallery.php.
