<?php
session_start();if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}
if(empty($_SESSION['user_id'])){header('Location: login.html');exit;}
require __DIR__.'/db.php';
require_once __DIR__ . '/mira-action-access.php';require __DIR__.'/event-common.php';
$uid=(int)$_SESSION['user_id'];if(!eventStudent($conn,$uid)){http_response_code(403);exit('Student access only');}
if(!eventValidToken()){http_response_code(403);exit('Invalid form token');}
$eid=filter_var($_POST['event_id']??null,FILTER_VALIDATE_INT);$action=$_POST['action']??'';if(!$eid||!in_array($action,['register','cancel'],true))eventGo('exhibitions.php?msg=error');
try {
 $conn->begin_transaction();
 $s=$conn->prepare('SELECT status,starts_at,registration_deadline,capacity FROM events WHERE event_id=? FOR UPDATE');$s->bind_param('i',$eid);$s->execute();$ev=$s->get_result()->fetch_assoc();$s->close();
 if(!$ev){$conn->rollback();eventGo('exhibitions.php?msg=closed');}
 if($action==='cancel'){
  $s=$conn->prepare("UPDATE event_registrations SET status='cancelled' WHERE event_id=? AND user_id=? AND status='registered'");$s->bind_param('ii',$eid,$uid);$s->execute();$s->close();$conn->commit();eventGo('exhibitions.php?msg=cancelled');
 }
 if($ev['status']!=='published'||strtotime($ev['starts_at'])<=time()||strtotime($ev['registration_deadline'])<time()){$conn->rollback();eventGo('exhibitions.php?msg=closed');}
 $s=$conn->prepare("SELECT COUNT(*) FROM event_registrations WHERE event_id=? AND status IN ('registered','attended')");$s->bind_param('i',$eid);$s->execute();$count=(int)$s->get_result()->fetch_row()[0];$s->close();
 $s=$conn->prepare('SELECT status FROM event_registrations WHERE event_id=? AND user_id=?');$s->bind_param('ii',$eid,$uid);$s->execute();$old=$s->get_result()->fetch_assoc();$s->close();
 if($old && $old['status']!=='cancelled'){$conn->commit();eventGo('exhibitions.php?msg=joined');}
 if($count>=(int)$ev['capacity']){$conn->rollback();eventGo('exhibitions.php?msg=full');}
 $s=$conn->prepare("INSERT INTO event_registrations(event_id,user_id,status) VALUES(?,?,'registered') ON DUPLICATE KEY UPDATE status='registered',registered_at=CURRENT_TIMESTAMP");$s->bind_param('ii',$eid,$uid);$s->execute();$s->close();$conn->commit();eventGo('exhibitions.php?msg=joined');
}catch(Throwable $e){$conn->rollback();error_log('MIRA registration: '.$e->getMessage());eventGo('exhibitions.php?msg=error');}
