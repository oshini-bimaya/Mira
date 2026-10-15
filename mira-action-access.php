<?php
// Shared enforcement for user interactions; Admin preview cannot POST.
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['user_id'])) { http_response_code(401); exit('Login required.'); }
if (!isset($conn) || !($conn instanceof mysqli)) require_once __DIR__ . '/db.php';
$miraWriteId = (int)$_SESSION['user_id'];
$miraWriteStmt = $conn->prepare('SELECT ROLE, account_status FROM users WHERE USER_ID=? LIMIT 1');
$miraWriteStmt->bind_param('i', $miraWriteId);
$miraWriteStmt->execute();
$miraWriteAccount = $miraWriteStmt->get_result()->fetch_assoc();
$miraWriteStmt->close();
if (!$miraWriteAccount || strtolower((string)$miraWriteAccount['account_status']) !== 'active' ||
    !in_array(strtoupper((string)$miraWriteAccount['ROLE']), ['STUDENT','ARTIST'], true)) {
    http_response_code(403); exit('This action requires an active Viewer or Artist account.');
}
$_SESSION['role'] = strtoupper((string)$miraWriteAccount['ROLE']);
