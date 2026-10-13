MIRA — Student Notifications (Phase 8 enhancement)

IMPORTANT: Back up your files and database before installation. Do NOT reimport old database dumps.

1) In phpMyAdmin, CLICK the local mira_artcraft database first. Import sql/notifications.sql. Repeat on InfinityFree only when ready to deploy.
2) Copy notification-common.php, notification-action.php, notifications.php, notification.css to the MIRA project root.
3) Replace admin/review-artwork.php and admin/review-event-request.php with these versions. If you changed them since earlier phases, MERGE the notification changes rather than overwriting your work.
4) user-home.php included is based on the earlier Phase 5 homepage (with featured artworks and exhibition posters). If your current homepage is newer, MERGE rather than replace: 
   a. After db.php, query SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0.
   b. Replace the existing inert notification <button> with <a href="notifications.php" class="notification-button">Notifications ...</a>, optionally add unread badge CSS.
   c. Keep all other homepage features untouched.
5) Ensure student-access.php exists in the project root from Phase 6. notifications.php and notification-action.php require it. If you have not yet integrated student-access.php, do so across all student-only routes as described in Phase 6.
6) Check ./admin/review-artwork.php already includes your latest phase 5 featured-order changes; if so, MERGE this notification call rather than replace the action file.
7) Test: login student; submit artwork; login admin; approve/reject artwork; login student; click bell; mark as read. Repeat with exhibition request. Unread count should decrease.
8) No emails, push alerts, or polling: this implementation is an in-website inbox, updated when pages reload. Existing approvals are not retroactively notified.

SECURITY: CSRF-protected mark-as-read, ownership-scoped updates, escape output, whitelisted local detail links; only admins send via existing protected review endpoints. Admin event review creates notification inside transaction.
