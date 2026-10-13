MIRA — Exhibition Poster & Student Artwork Requests (Phase 4 extension)

Backup local code and MySQL database before installing. Latest project sidebar.php and original admin.css/admin.js remain unchanged.

1. Run sql/exhibition_requests_posters.sql ONCE in the selected database via phpMyAdmin. Do this separately for local XAMPP and InfinityFree, if both are used. Do not import the old full SQL dump again.
2. Add admin/event-requests.php and admin/review-event-request.php to admin/.
3. Replace admin/events.php and admin/save-event.php after backing them up. Keep admin/auth.php, sidebar.php, admin.css and admin.js intact.
4. Add request-exhibition.php, submit-exhibition-request.php, exhibition-home.css to the site root.
5. Replace root exhibitions.php, exhibition-detail.php, event.css and user-home.php with provided files. Back up user-home.php first. If you have made additional edits since the last ZIP you provided, compare/merge the changes instead of blindly replacing it.
6. Create uploads/events/ and put index.html and .htaccess in it.
7. In admin/events.php create/edit a published event and upload a JPG/PNG/WebP poster up to 3MB.
8. Login as a student. Click Exhibitions, then 'Request to exhibit my art' and select an approved artwork.
9. Admin opens Exhibitions > Artwork requests to approve/reject. Approved requests are inserted in event_artworks and appear in exhibition detail pages.
10. Open user-home.php to see upcoming published posters.

NOTES:
- Attending an exhibition (register button) is different from requesting to display artwork.
- The poster upload requires PHP file uploads to be enabled. Hosting upload limits may be lower than 3MB.
- If a poster isn't visible, check the event status, date, actual uploads/events directory and image permissions.
- Existing manual artwork selections are preserved. Declining a previously approved request will unselect its artwork from the event.
- Don't upload database dumps or credentials to a public folder/repository.
- This package was syntax-checked locally but must be tested with your live database.
