<?php
// Read-only public-side access: verify the database account on every request.
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['user_id'])) { header('Location: login.html'); exit; }
require_once __DIR__ . '/db.php';
$miraAccountId = (int)$_SESSION['user_id'];
$miraAccessStmt = $conn->prepare('SELECT ROLE, account_status, FULL_NAME FROM users WHERE USER_ID=? LIMIT 1');
$miraAccessStmt->bind_param('i', $miraAccountId);
$miraAccessStmt->execute();
$miraAccount = $miraAccessStmt->get_result()->fetch_assoc();
$miraAccessStmt->close();
if (!$miraAccount || strtolower(trim((string)$miraAccount['account_status'])) !== 'active') {
    $_SESSION = []; session_destroy(); http_response_code(403); exit('This account is not active.');
}
$miraAccountRole = strtoupper(trim((string)$miraAccount['ROLE']));
if (!in_array($miraAccountRole, ['STUDENT','ARTIST','ADMIN'], true)) {
    http_response_code(403); exit('Access denied.');
}
$_SESSION['role'] = $miraAccountRole;
$_SESSION['full_name'] = (string)$miraAccount['FULL_NAME'];
$miraAdminPreview = $miraAccountRole === 'ADMIN';
