<?php
// Include on ALL student-only routes, including POST action handlers.
// Requires a session and the existing db.php connection.
if(session_status()!==PHP_SESSION_ACTIVE) session_start();
if(!isset($_SESSION['user_id'])){header('Location: login.html');exit;}
require_once __DIR__.'/db.php';
$studentUid=(int)$_SESSION['user_id'];
$accessStmt=$conn->prepare('SELECT ROLE,account_status FROM users WHERE USER_ID=? LIMIT 1');
$accessStmt->bind_param('i',$studentUid);$accessStmt->execute();$studentRecord=$accessStmt->get_result()->fetch_assoc();$accessStmt->close();
if(!$studentRecord||strtoupper((string)$studentRecord['ROLE'])!=='STUDENT'||$studentRecord['account_status']!=='active'){
 $_SESSION=[];session_destroy();http_response_code(403);exit('Student account is not active. Please contact an administrator.');
}
