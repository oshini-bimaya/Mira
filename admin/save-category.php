<?php
require __DIR__ . '/auth.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method not allowed'); }
if (!isset($_SESSION['category_csrf'], $_POST['csrf']) || !hash_equals($_SESSION['category_csrf'], (string)$_POST['csrf'])) { http_response_code(403); exit('Invalid form token. Refresh the page.'); }
function back(string $result): void { header('Location: categories.php?msg=' . rawurlencode($result), true, 303); exit; }
$action = (string)($_POST['action'] ?? '');
$id = filter_var($_POST['category_id'] ?? null, FILTER_VALIDATE_INT);
if (!in_array($action, ['create','edit','toggle'], true)) back('invalid');
if ($action !== 'create' && (!$id || $id < 1)) back('invalid');
if ($action === 'toggle') {
    $stmt = $conn->prepare('UPDATE categories SET is_active = IF(is_active = 1, 0, 1) WHERE category_id = ?');
    $stmt->bind_param('i', $id); $stmt->execute(); $changed = $stmt->affected_rows; $stmt->close();
    back($changed ? 'updated' : 'missing');
}
$name = trim((string)($_POST['category_name'] ?? ''));
$description = trim((string)($_POST['description'] ?? ''));
if ($name === '' || strlen($name) > 200 || strlen($description) > 2000) back('invalid');
if ($action === 'edit') {
    $check = $conn->prepare('SELECT category_id FROM categories WHERE category_id = ?');
    $check->bind_param('i', $id); $check->execute(); $found = (bool)$check->get_result()->fetch_assoc(); $check->close();
    if (!$found) back('missing');
}
$check = $conn->prepare('SELECT category_id FROM categories WHERE LOWER(category_name) = LOWER(?)' . ($action === 'edit' ? ' AND category_id <> ?' : '') . ' LIMIT 1');
if ($action === 'edit') $check->bind_param('si', $name, $id); else $check->bind_param('s', $name);
$check->execute(); $duplicate = (bool)$check->get_result()->fetch_assoc(); $check->close();
if ($duplicate) back('duplicate');
$newPath = null;
if (isset($_FILES['cover']) && ($_FILES['cover']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['cover'];
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] < 1 || $file['size'] > 3 * 1024 * 1024 || !is_uploaded_file($file['tmp_name'])) back('image');
    $details = @getimagesize($file['tmp_name']);
    $allowed = ['image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp'];
    if (!$details || !isset($allowed[$details['mime']]) || $details[0] < 1 || $details[1] < 1 || $details[0] > 8000 || $details[1] > 8000) back('image');
    $directory = __DIR__ . '/../uploads/categories';
    if (!is_dir($directory) && !mkdir($directory, 0755, true)) back('image');
    $filename = bin2hex(random_bytes(18)) . '.' . $allowed[$details['mime']];
    if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $filename)) back('image');
    $newPath = 'uploads/categories/' . $filename;
}
try {
    if ($action === 'create') {
        $stmt = $conn->prepare('INSERT INTO categories (category_name, description, image_path, is_active) VALUES (?, ?, ?, 1)');
        $stmt->bind_param('sss', $name, $description, $newPath);
    } elseif ($newPath !== null) {
        $stmt = $conn->prepare('UPDATE categories SET category_name = ?, description = ?, image_path = ? WHERE category_id = ?');
        $stmt->bind_param('sssi', $name, $description, $newPath, $id);
    } else {
        $stmt = $conn->prepare('UPDATE categories SET category_name = ?, description = ? WHERE category_id = ?');
        $stmt->bind_param('ssi', $name, $description, $id);
    }
    $stmt->execute(); $stmt->close();
    back($action === 'create' ? 'created' : 'updated');
} catch (Throwable $e) {
    if ($newPath !== null) @unlink(__DIR__ . '/../' . $newPath);
    error_log('MIRA category save: ' . $e->getMessage()); back('error');
}
