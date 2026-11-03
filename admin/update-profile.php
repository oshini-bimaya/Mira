<?php
require __DIR__ . '/auth.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method not allowed.'); }
if (!isset($_SESSION['admin_profile_csrf']) || !hash_equals($_SESSION['admin_profile_csrf'], (string)($_POST['csrf_token'] ?? ''))) { http_response_code(403); exit('Invalid request token.'); }
function finishProfile(string $status): void { header('Location: profile.php?status=' . urlencode($status), true, 303); exit; }
$action = $_POST['action'] ?? '';
if ($action === 'details') {
    $name = trim((string)($_POST['full_name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    if ($name === '' || strlen($name) > 150 || strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) finishProfile('invalid');
    $check = $conn->prepare('SELECT USER_ID FROM users WHERE EMAIL = ? AND USER_ID <> ? LIMIT 1');
    $check->bind_param('si', $email, $uid); $check->execute();
    $existing = $check->get_result()->fetch_assoc(); $check->close();
    if ($existing) finishProfile('taken');
    try {
        $stmt = $conn->prepare('UPDATE users SET FULL_NAME = ?, EMAIL = ? WHERE USER_ID = ? AND ROLE = \'ADMIN\'');
        $stmt->bind_param('ssi', $name, $email, $uid); $stmt->execute(); $stmt->close();
    } catch (mysqli_sql_exception $e) { finishProfile('failed'); }
    $_SESSION['full_name'] = $name; $_SESSION['email'] = $email;
    finishProfile('saved');
}
if ($action === 'password') {
    $current = (string)($_POST['current_password'] ?? '');
    $new = (string)($_POST['new_password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');
    if (strlen($new) < 8 || strlen($new) > 256) finishProfile('weak');
    if ($new !== $confirm) finishProfile('mismatch');
    $stmt = $conn->prepare('SELECT PASSWORD FROM users WHERE USER_ID = ? AND ROLE = \'ADMIN\' LIMIT 1');
    $stmt->bind_param('i', $uid); $stmt->execute(); $record = $stmt->get_result()->fetch_assoc(); $stmt->close();
    if (!$record || !password_verify($current, $record['PASSWORD'])) finishProfile('incorrect');
    $hash = password_hash($new, PASSWORD_DEFAULT);
    $update = $conn->prepare('UPDATE users SET PASSWORD = ? WHERE USER_ID = ? AND ROLE = \'ADMIN\'');
    $update->bind_param('si', $hash, $uid); $update->execute(); $update->close();
    session_regenerate_id(true);
    finishProfile('password');
}
http_response_code(400); exit('Invalid action.');
