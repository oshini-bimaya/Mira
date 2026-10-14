<?php
session_start();
if (empty($_SESSION['user_id'])) { header('Location: login.html'); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method not allowed'); }
require __DIR__ . '/db.php';
if (!hash_equals((string)($_SESSION['artwork_csrf'] ?? ''), (string)($_POST['csrf_token'] ?? ''))) {
    http_response_code(403); exit('Invalid request token. Refresh and try again.');
}
$uid=(int)$_SESSION['user_id'];
$id=filter_var($_POST['artwork_id'] ?? null, FILTER_VALIDATE_INT);
if (!$id || $id <= 0) { http_response_code(400); exit('Invalid artwork'); }
try {
    $check=$conn->prepare("SELECT artwork_id FROM artworks WHERE artwork_id=? AND LOWER(status)='approved' LIMIT 1");
    $check->bind_param('i',$id); $check->execute(); $exists=$check->get_result()->num_rows > 0; $check->close();
    if (!$exists) { http_response_code(404); exit('Artwork unavailable'); }
    if (($_POST['action'] ?? '') === 'save') {
        $stmt=$conn->prepare('INSERT IGNORE INTO saved_artworks (user_id, artwork_id) VALUES (?,?)');
    } elseif (($_POST['action'] ?? '') === 'remove') {
        $stmt=$conn->prepare('DELETE FROM saved_artworks WHERE user_id=? AND artwork_id=?');
    } else { http_response_code(400); exit('Invalid action'); }
    $stmt->bind_param('ii',$uid,$id); $stmt->execute(); $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log('MIRA saved artworks: '.$e->getMessage());
    http_response_code(500); exit('Saving failed. Ensure saved_artworks.sql has been imported.');
}
$destination=($_POST['return_to']??'')==='saved' ? 'saved-artworks.php' : 'artwork-view.php?id='.$id;
header('Location: '.$destination, true, 303); exit;
