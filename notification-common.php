<?php
// Shared notification functions. Call only with trusted server-generated content.
function miraNotify(mysqli $db, int $recipient, string $type, string $title, string $message, ?string $link = null): void {
    if ($recipient <= 0) return;
    $stmt = $db->prepare('INSERT INTO notifications (user_id,type,title,message,link_path) VALUES (?,?,?,?,?)');
    $stmt->bind_param('issss', $recipient, $type, $title, $message, $link);
    $stmt->execute();
    $stmt->close();
}
function miraNotificationToken(): string {
    if (empty($_SESSION['notification_csrf'])) $_SESSION['notification_csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['notification_csrf'];
}
function miraNotificationEscape($value): string {return htmlspecialchars((string)$value,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
