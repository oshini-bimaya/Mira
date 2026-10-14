<?php
// Shared helpers for the MIRA artwork workflow.
function artworkEscape($v): string { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function artworkCsrf(): string {
    if (empty($_SESSION['artwork_csrf'])) $_SESSION['artwork_csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['artwork_csrf'];
}
function artworkValidCsrf(): bool {
    return isset($_POST['csrf'], $_SESSION['artwork_csrf']) && hash_equals($_SESSION['artwork_csrf'], (string)$_POST['csrf']);
}
function artworkIsStudent(mysqli $conn, int $uid): bool {
    $stmt=$conn->prepare('SELECT ROLE FROM users WHERE USER_ID=? LIMIT 1');
    $stmt->bind_param('i',$uid); $stmt->execute(); $r=$stmt->get_result()->fetch_assoc();$stmt->close();
    return $r && strtoupper($r['ROLE'])==='STUDENT';
}
