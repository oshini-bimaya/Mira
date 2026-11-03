<?php
require __DIR__.'/auth.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}
$token=(string)($_POST['csrf']??'');
if(!isset($_SESSION['mira_student_csrf'])||!hash_equals($_SESSION['mira_student_csrf'],$token)){http_response_code(403);exit('Invalid security token');}
$id=filter_input(INPUT_POST,'student_id',FILTER_VALIDATE_INT);
$action=(string)($_POST['action']??'');
$reason=trim((string)($_POST['reason']??''));
if(!$id||!in_array($action,['suspend','reactivate'],true)){http_response_code(400);exit('Invalid request');}
if($action==='suspend'&&($reason===''||mb_strlen($reason)>500)){http_response_code(400);exit('Please enter a reason up to 500 characters');}
if($action==='suspend'){
 $stmt=$conn->prepare("UPDATE users SET account_status='suspended',suspension_reason=?,status_updated_at=NOW() WHERE USER_ID=? AND UPPER(ROLE) IN ('STUDENT','ARTIST')");$stmt->bind_param('si',$reason,$id);
}else{
 $stmt=$conn->prepare("UPDATE users SET account_status='active',suspension_reason=NULL,status_updated_at=NOW() WHERE USER_ID=? AND UPPER(ROLE) IN ('STUDENT','ARTIST')");$stmt->bind_param('i',$id);
}
$stmt->execute();$stmt->close();
header('Location: student-details.php?id='.$id.'&updated=1');exit;
