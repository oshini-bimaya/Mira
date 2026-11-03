<?php
// Shared access check for signed-in VIEWERS (STUDENT) and ARTISTS.
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['user_id'])) { header('Location: login.html'); exit; }
require_once __DIR__ . '/db.php';
$uid = (int) $_SESSION['user_id'];
$stmt = $conn->prepare('SELECT ROLE, account_status FROM users WHERE USER_ID = ? LIMIT 1');
$stmt->bind_param('i', $uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$user) { $_SESSION=[]; session_destroy(); http_response_code(403); exit('Account not found.'); }
$role = strtoupper(trim((string)$user['ROLE']));
$status = strtolower(trim((string)$user['account_status']));
if ($status !== 'active') {
    $_SESSION=[]; session_destroy(); http_response_code(403);
    exit('This account is suspended. Please contact an administrator.');
}
if (!in_array($role, ['STUDENT', 'ARTIST'], true)) {
    http_response_code(403); exit('This page is for registered viewers and artists.');
}
$_SESSION['role'] = $role;
