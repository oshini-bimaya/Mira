<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed.']); exit; }
require __DIR__ . '/db.php';
$name = trim((string)($_POST['fullName'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$password = (string)($_POST['password'] ?? '');
$confirm = (string)($_POST['confirmPassword'] ?? '');
$role = strtoupper(trim((string)($_POST['role'] ?? 'STUDENT')));
// IMPORTANT: No public registration can ever assign ADMIN.
if (!in_array($role, ['STUDENT','ARTIST'], true)) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Choose Viewer or Artist.']); exit; }
if ($name === '' || strlen($name) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) { echo json_encode(['success'=>false,'message'=>'Enter a valid name and email address.']); exit; }
if (strlen($password) < 8 || $password !== $confirm) { echo json_encode(['success'=>false,'message'=>'Passwords must match and contain at least 8 characters.']); exit; }
$check = $conn->prepare('SELECT USER_ID FROM users WHERE EMAIL = ? LIMIT 1');
$check->bind_param('s',$email); $check->execute(); $exists = (bool)$check->get_result()->fetch_assoc(); $check->close();
if ($exists) { echo json_encode(['success'=>false,'message'=>'An account with this email already exists.']); exit; }
$hash = password_hash($password,PASSWORD_DEFAULT);
$stmt = $conn->prepare('INSERT INTO users (FULL_NAME, EMAIL, PASSWORD, ROLE) VALUES (?, ?, ?, ?)');
$stmt->bind_param('ssss',$name,$email,$hash,$role);
try { $ok=$stmt->execute(); } catch (mysqli_sql_exception $e) { $ok=false; }
$stmt->close();
if (!$ok) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Unable to create account. Please try again.']); exit; }
echo json_encode(['success'=>true,'message'=>'Account created. Please sign in.']);
