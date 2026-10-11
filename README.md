# MIRA — Artist Profile Page

The artist profile page for **MIRA · NSBM ART & CRAFT CAFE** gives signed-in users a personal space to introduce themselves, update their photos, and view their artwork activity.

## Try it online

**You can test the profile page on the [MIRA website](http://whyismira.infy.click/Mira).**

Sign in with a regular user account, then open **My Profile**. The profile page requires a login; administrator accounts are redirected to the admin dashboard.

## Features

- **Personal profile:** Displays the artist's name, profile photo, and a short introduction.
- **Profile and cover uploads:** Supports JPG, PNG, and WebP images, up to 5 MB per image. Existing photos are kept when no replacement is selected.
- **About the artist:** An expandable form lets users write and update their artist biography.
- **Artwork statistics:** Shows total artworks, approved artworks, and artworks pending review.
- **My creations:** Displays up to 12 recent artworks with thumbnails, titles, dates, and approval statuses.
- **Artwork management:** Links to manage existing artworks and upload new creations.
- **Responsive design:** Rounded cards, a pastel lavender-and-pink cover frame, and orange-to-pink action buttons.
- **Button interaction:** The Save profile button lifts slightly on hover or keyboard focus. Reduced-motion preferences are respected.

## Technology

| Layer | Technology |
| --- | --- |
| Page structure | HTML |
| Styling and animation | CSS |
| Profile dropdown | JavaScript |
| Sessions, validation, and updates | PHP |
| Data storage | MySQL / MariaDB |

## Main files

| File or folder | Purpose |
| --- | --- |
| `profile.php` | Renders the profile, handles image uploads, and saves profile changes. |
| `profile.css` | Profile layout, responsive styles, theme styles, and button transitions. |
| `style.css` | Shared website styles. |
| `db.php` | Database connection used by the application. |
| `gallery-common.php` | Shared artwork image helper. |
| `uploads/profiles/` | Uploaded profile and cover images. |

The profile page is part of the MIRA application and depends on its authentication, database, and shared files.

## Test the profile page

1. Visit [MIRA](http://whyismira.infy.click/Mira) and sign in with a regular user account.
2. Open **My Profile** and expand **Edit my profile** or **Add your bio and photos**.
3. Update the artist biography and optionally select a profile photo and cover photo.
4. Hover over **Save profile** to check the upward movement, then click it to save.
5. Confirm the success message and refresh the page to check that your changes remain saved.
6. Check the artwork statistics and **My creations** section.
7. Try a narrower browser window to check the responsive layout.

## Local setup

1. Place the complete MIRA project in your PHP server's web directory, such as `htdocs/Mira` in XAMPP.
2. Import the project's database and configure the connection in `db.php`.
3. Ensure the `users` table includes `ABOUT_ARTIST`, `PROFILE_PHOTO`, and `COVER_PHOTO`, alongside the existing user fields. The page also reads the `artworks` and `artwork_images` tables.
4. Make `uploads/profiles/` writable by PHP and configure PHP's upload and POST limits to accommodate the image uploads.
5. Start Apache and MySQL, open `http://localhost/Mira/`, and sign in.

## Implementation notes

- Profile updates use prepared SQL statements and a session-based CSRF token.
- Displayed profile text is escaped before being rendered as HTML.
- Uploaded images are checked for supported image types and size, then saved with randomized filenames.
- The stylesheet URL includes its modification time so browsers can fetch the latest CSS after an update.
