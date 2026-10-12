<?php
require __DIR__.'/auth.php';require __DIR__.'/../event-common.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}
if(!eventValidToken()){http_response_code(403);exit('Invalid form token');}
if(($_POST['action']??'')==='attendance'){
 $rid=filter_var($_POST['registration_id']??null,FILTER_VALIDATE_INT);$eid=filter_var($_POST['event_id']??null,FILTER_VALIDATE_INT);$state=$_POST['attendance']??'';
 if(!$rid||!$eid||!in_array($state,['registered','attended'],true)) eventGo('events.php?msg=invalid');
 $s=$conn->prepare("UPDATE event_registrations SET status=? WHERE registration_id=? AND event_id=? AND status <> 'cancelled'");$s->bind_param('sii',$state,$rid,$eid);$s->execute();$s->close();eventGo('events.php?registrations='.$eid.'&msg=attendance');
}
$id=filter_var($_POST['event_id']??0,FILTER_VALIDATE_INT) ?: 0;
$title=trim($_POST['title']??'');$desc=trim($_POST['description']??'');$venue=trim($_POST['venue']??'');$status=$_POST['status']??'';$capacity=filter_var($_POST['capacity']??null,FILTER_VALIDATE_INT);
$start=DateTimeImmutable::createFromFormat('!Y-m-d\TH:i',$_POST['starts_at']??'');$end=DateTimeImmutable::createFromFormat('!Y-m-d\TH:i',$_POST['ends_at']??'');$deadline=DateTimeImmutable::createFromFormat('!Y-m-d\TH:i',$_POST['registration_deadline']??'');
if(!$start||!$end||!$deadline||$end<=$start||$deadline>$start||!$capacity||$capacity<1||$capacity>100000||strlen($title)<2||strlen($title)>200||strlen($venue)<2||strlen($venue)>200||strlen($desc)>5000||!in_array($status,['draft','published','cancelled','completed'],true)) eventGo('events.php?msg=invalid');
$starts=$start->format('Y-m-d H:i:s');$ends=$end->format('Y-m-d H:i:s');$until=$deadline->format('Y-m-d H:i:s');
try{
 if($id){$s=$conn->prepare('UPDATE events SET title=?,description=?,venue=?,starts_at=?,ends_at=?,registration_deadline=?,capacity=?,status=? WHERE event_id=?');$s->bind_param('ssssssisi',$title,$desc,$venue,$starts,$ends,$until,$capacity,$status,$id);}
 else{$uid=(int)$_SESSION['user_id'];$s=$conn->prepare('INSERT INTO events(title,description,venue,starts_at,ends_at,registration_deadline,capacity,status,created_by) VALUES(?,?,?,?,?,?,?,?,?)');$s->bind_param('ssssssisi',$title,$desc,$venue,$starts,$ends,$until,$capacity,$status,$uid);}
 $s->execute();$s->close();eventGo('events.php?msg=saved');
}catch(Throwable $e){error_log('MIRA event save: '.$e->getMessage());eventGo('events.php?msg=error');}
