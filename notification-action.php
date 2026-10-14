<?php
require __DIR__ . '/student-access.php';
require __DIR__ . '/notification-common.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}
if(!isset($_POST['csrf'])||!hash_equals(miraNotificationToken(),(string)$_POST['csrf'])){http_response_code(403);exit('Invalid form token');}
$uid=(int)$_SESSION['user_id'];
if(($_POST['action']??'')==='all'){
 $stmt=$conn->prepare('UPDATE notifications SET is_read=1 WHERE user_id=? AND is_read=0');$stmt->bind_param('i',$uid);
}elseif(($_POST['action']??'')==='read'){
 $id=filter_var($_POST['notification_id']??null,FILTER_VALIDATE_INT);if(!$id){http_response_code(400);exit('Invalid notification');}
 $stmt=$conn->prepare('UPDATE notifications SET is_read=1 WHERE notification_id=? AND user_id=?');$stmt->bind_param('ii',$id,$uid);
}else{http_response_code(400);exit('Invalid action');}
$stmt->execute();$stmt->close();header('Location: notifications.php');exit;
