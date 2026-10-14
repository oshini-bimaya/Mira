<?php
session_start();if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}
if(empty($_SESSION['user_id'])){header('Location: login.html');exit;}
require __DIR__.'/db.php';require __DIR__.'/event-common.php';
$uid=(int)$_SESSION['user_id'];if(!eventStudent($conn,$uid)){http_response_code(403);exit('Student access only');}
if(!eventValidToken()){http_response_code(403);exit('Invalid form token');}
$eid=filter_var($_POST['event_id']??null,FILTER_VALIDATE_INT);$aid=filter_var($_POST['artwork_id']??null,FILTER_VALIDATE_INT);
if(!$eid||!$aid){http_response_code(400);exit('Invalid input');}
$s=$conn->prepare("SELECT e.event_id FROM events e JOIN artworks a ON a.artwork_id=? WHERE e.event_id=? AND e.status='published' AND e.registration_deadline >= NOW() AND e.starts_at > NOW() AND a.user_id=? AND LOWER(a.status)='approved' LIMIT 1");
$s->bind_param('iii',$aid,$eid,$uid);$s->execute();$valid=$s->get_result()->num_rows>0;$s->close();
if(!$valid)eventGo('request-exhibition.php?event_id='.$eid.'&msg=closed');
try{$s=$conn->prepare("INSERT INTO exhibition_requests(event_id,artwork_id,user_id,status) VALUES(?,?,?,'pending')");$s->bind_param('iii',$eid,$aid,$uid);$s->execute();$s->close();eventGo('request-exhibition.php?event_id='.$eid.'&msg=sent');}
catch(Throwable $e){error_log('MIRA exhibition request: '.$e->getMessage());eventGo('request-exhibition.php?event_id='.$eid.'&msg=error');}
