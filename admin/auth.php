<?php
// Include this at the top of EVERY admin PHP page, before output.
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.html'); exit; }
require __DIR__ . '/../db.php';
$uid = (int)$_SESSION['user_id'];
$stmt = $conn->prepare('SELECT FULL_NAME, ROLE FROM users WHERE USER_ID = ? LIMIT 1');
$stmt->bind_param('i', $uid); $stmt->execute(); $record = $stmt->get_result()->fetch_assoc(); $stmt->close();
if (!$record || strtoupper($record['ROLE']) !== 'ADMIN') { http_response_code(403); exit('403 – Administrators only.'); }
$_SESSION['role'] = 'ADMIN';
$adminName = $record['FULL_NAME'];
function h($value) { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
