<?php
require __DIR__.'/auth.php';require __DIR__.'/../event-common.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}
if(!eventValidToken()){http_response_code(403);exit('Invalid form token');}
$eid=filter_var($_POST['event_id']??null,FILTER_VALIDATE_INT);$rid=filter_var($_POST['request_id']??null,FILTER_VALIDATE_INT);
$decision=$_POST['decision']??'';$note=trim($_POST['admin_note']??'');
if(!$eid||!$rid||!in_array($decision,['approved','rejected'],true)||strlen($note)>1000){http_response_code(400);exit('Invalid request');}
try{
 $conn->begin_transaction();
 $s=$conn->prepare("SELECT r.artwork_id,r.status,a.status AS artwork_status FROM exhibition_requests r JOIN artworks a ON a.artwork_id=r.artwork_id WHERE r.request_id=? AND r.event_id=? FOR UPDATE");$s->bind_param('ii',$rid,$eid);$s->execute();$r=$s->get_result()->fetch_assoc();$s->close();if(!$r)throw new RuntimeException('Request missing');
 if($decision==='approved' && strtolower($r['artwork_status'])!=='approved') throw new RuntimeException('Artwork is no longer approved');
 $adminId=(int)$_SESSION['user_id'];
 $s=$conn->prepare('UPDATE exhibition_requests SET status=?,admin_note=?,reviewed_at=NOW(),reviewed_by=? WHERE request_id=?');$s->bind_param('ssii',$decision,$note,$adminId,$rid);$s->execute();$s->close();
 if($decision==='approved'){
   $s=$conn->prepare('INSERT IGNORE INTO event_artworks(event_id,artwork_id) VALUES(?,?)');$s->bind_param('ii',$eid,$r['artwork_id']);$s->execute();$s->close();
 }elseif($r['status']==='approved'){
   $s=$conn->prepare('DELETE FROM event_artworks WHERE event_id=? AND artwork_id=?');$s->bind_param('ii',$eid,$r['artwork_id']);$s->execute();$s->close();
 }
 $conn->commit();eventGo('event-requests.php?event_id='.$eid);
}catch(Throwable $e){$conn->rollback();error_log('MIRA request moderation: '.$e->getMessage());http_response_code(500);exit('Could not update request. Check artwork approval and database configuration.');}
