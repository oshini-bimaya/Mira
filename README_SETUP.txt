MIRA APPROVED ARTWORK GALLERY INTEGRATION

1. Back up your existing user-home.php and artwork-view.php.
2. Copy user-home.php, artwork-view.php, gallery.php, gallery-common.php,
   gallery.css, gallery-search.js to D:\xampp\htdocs\Mira copy\ (the ROOT folder).
3. Keep db.php, style.css, theme.js, all admin files, and the uploads/artworks directory unchanged.
4. No new SQL migration needed if the previous artwork_workflow_migration.sql ran successfully.
5. Log in as a student, upload an artwork, then log in as admin to approve it.
6. Visit http://localhost/Mira%20copy/user-home.php or /gallery.php.
7. The artwork will appear after approval and disappear from public view if rejected.
8. Test /artwork-view.php?id=YOUR_APPROVED_ARTWORK_ID for a live detail page.

NOTE: Any old sample artworks in user-home.php are intentionally removed.
The old artwork-view.php demonstration likes/comments were not database-backed, and
are intentionally replaced by genuine approved submission details; no fake counters.
The homepage categories in index.html are still static, and gallery category filtering
uses the real names in MySQL. There are no changes to the public index.html in this phase.
