<?php
require __DIR__.'/auth.php';require __DIR__.'/../artwork-common.php';require __DIR__.'/../notification-common.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}
if(!artworkValidCsrf()){http_response_code(403);exit('Invalid form token');}
$id=filter_var($_POST['artwork_id']??null,FILTER_VALIDATE_INT);$decision=(string)($_POST['decision']??'');$reason=trim((string)($_POST['reason']??''));
if(!$id||!in_array($decision,['approved','rejected','toggle_featured'],true)){header('Location: artworks.php?msg=error');exit;}
if($decision==='rejected'&&($reason===''||strlen($reason)>1000)){http_response_code(422);exit('A rejection reason (maximum 1000 characters) is required. Use Back to revise.');}
$uid=(int)$_SESSION['user_id'];
if($decision==='toggle_featured'){
 $stmt=$conn->prepare("UPDATE artworks SET is_featured=IF(is_featured=1,0,1) WHERE artwork_id=? AND LOWER(status)='approved'");$stmt->bind_param('i',$id);$stmt->execute();$ok=$stmt->affected_rows===1;$stmt->close();$msg=$ok?'featured':'error';
}else{
 $ownerStmt=$conn->prepare('SELECT user_id,title FROM artworks WHERE artwork_id=?');
 $ownerStmt->bind_param('i',$id);$ownerStmt->execute();$owner=$ownerStmt->get_result()->fetch_assoc();$ownerStmt->close();
 $stmt=$conn->prepare("UPDATE artworks SET status=?, rejection_reason=?, reviewed_at=NOW(), reviewed_by=?, is_featured=0 WHERE artwork_id=? AND LOWER(status)='pending'");
 $rejection=$decision==='rejected'?$reason:null;$stmt->bind_param('ssii',$decision,$rejection,$uid,$id);$stmt->execute();$ok=$stmt->affected_rows===1;$stmt->close();$msg=$ok?$decision:'error';
 if($ok){
   try{
     $log=$conn->prepare('INSERT INTO artwork_status_history (artwork_id,actor_id,action,details) VALUES (?,?,?,?)');
     $logDetails=$decision==='rejected'?$reason:null;
     $log->bind_param('iiss',$id,$uid,$decision,$logDetails);$log->execute();$log->close();
   }catch(Throwable $e){error_log('MIRA status history: '.$e->getMessage());}
 }

 if($ok && $owner){
   try{
     $title=$decision==='approved'?'Artwork approved':'Artwork rejected';
     $body=$decision==='approved'?('Your artwork "'.$owner['title'].'" was approved and is now visible in the gallery.'):('Your artwork "'.$owner['title'].'" was rejected. Reason: '.$reason);
     miraNotify($conn,(int)$owner['user_id'],'artwork_'.$decision,$title,substr($body,0,600),'my-artworks.php');
   }catch(Throwable $e){error_log('MIRA notification failed: '.$e->getMessage());}
 }
}
header('Location: artworks.php?msg='.$msg);exit;
