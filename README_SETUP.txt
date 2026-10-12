MIRA PHASE 4 — EXHIBITIONS & EVENTS
==================================
1. BACK UP both your current website files and your InfinityFree MySQL database.
2. Run sql/phase4_exhibitions.sql ONCE in phpMyAdmin on the existing MIRA database.
   This creates events, event_registrations and event_artworks; it does not replace users, categories or artworks.
3. Upload these new files into InfinityFree /htdocs/:
     event-common.php
     event.css
     exhibitions.php
     register-event.php
     my-events.php
     exhibition-detail.php
4. Upload into /htdocs/admin/:
     events.php
     save-event.php
     event-artworks.php
   Keep existing auth.php, dashboard.php, admin.css, admin.js, artwork approval and category files.
5. In /htdocs/admin/dashboard.php, replace the disabled Exhibitions navigation with a real link:
     <a href="events.php">▣ Exhibitions</a>
   Do the same in /htdocs/admin/categories.php if it shows a disabled Exhibitions link.
6. In /htdocs/user-home.php, change the existing Exhibitions navbar href="gallery.php" to href="exhibitions.php".
   Optionally add a "My Exhibitions" menu link to my-events.php (if present).
7. Log in as ADMIN, open /admin/events.php. Create an event with valid future dates and publish it.
   Select approved artwork via "Select artworks". Review registrations and mark attendance.
8. Log in as STUDENT, open /exhibitions.php, register, then check /my-events.php.

SECURITY AND BEHAVIOR
- PHP sessions, database role checks for students, admin/auth.php for admin access, CSRF on modifications.
- Event capacity enforced inside MySQL transaction using SELECT FOR UPDATE on event row.
- Student registrations are unique per user and event, and can be cancelled/re-registered while registration is open.
- Cancelled/closed events do not accept new registrations.
- Dates are interpreted in PHP server timezone; align your PHP timezone to Asia/Colombo if needed.
- Admin should NOT mark attendance as 'attended' until participation is actually verified.
- No email notifications are included; no automatic emails are claimed.
- Admin views registered student email addresses; keep access limited to authorized administrators.
- The existing student gallery and category code remain unchanged.
- Be sure that your InfinityFree SQL export contains the latest artwork_images table and approval columns.
