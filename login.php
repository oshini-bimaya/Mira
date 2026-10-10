<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
require __DIR__ . '/db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); exit; }
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
if ($email === '' || $password === '') { echo json_encode(['success'=>false,'message'=>'Please enter your email and password.']); exit; }
$stmt = $conn->prepare('SELECT USER_ID, FULL_NAME, EMAIL, PASSWORD, ROLE FROM users WHERE EMAIL = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$user || !password_verify($password, $user['PASSWORD'])) { echo json_encode(['success'=>false,'message'=>'Invalid email or password.']); exit; }
session_regenerate_id(true);
$_SESSION['user_id'] = (int)$user['USER_ID'];
$_SESSION['full_name'] = $user['FULL_NAME'];
$_SESSION['email'] = $user['EMAIL'];
$_SESSION['role'] = strtoupper($user['ROLE']);
$isAdmin = $_SESSION['role'] === 'ADMIN';
echo json_encode(['success'=>true,'message'=>'Login successful!','redirect'=>$isAdmin ? 'admin/dashboard.php' : 'user-home.php']);
