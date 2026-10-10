<?php
require __DIR__.'/auth.php';require __DIR__.'/../artwork-common.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}
if(!artworkValidCsrf()){http_response_code(403);exit('Invalid form token');}
$id=filter_var($_POST['artwork_id']??null,FILTER_VALIDATE_INT);$decision=(string)($_POST['decision']??'');$reason=trim((string)($_POST['reason']??''));
if(!$id||!in_array($decision,['approved','rejected','toggle_featured'],true)){header('Location: artworks.php?msg=error');exit;}
if($decision==='rejected'&&($reason===''||strlen($reason)>1000)){http_response_code(422);exit('A rejection reason (maximum 1000 characters) is required. Use Back to revise.');}
$uid=(int)$_SESSION['user_id'];
if($decision==='toggle_featured'){
 $stmt=$conn->prepare("UPDATE artworks SET is_featured=IF(is_featured=1,0,1) WHERE artwork_id=? AND LOWER(status)='approved'");$stmt->bind_param('i',$id);$stmt->execute();$ok=$stmt->affected_rows===1;$stmt->close();$msg=$ok?'featured':'error';
}else{
 $stmt=$conn->prepare("UPDATE artworks SET status=?, rejection_reason=?, reviewed_at=NOW(), reviewed_by=?, is_featured=0 WHERE artwork_id=? AND LOWER(status)='pending'");
 $rejection=$decision==='rejected'?$reason:null;$stmt->bind_param('ssii',$decision,$rejection,$uid,$id);$stmt->execute();$ok=$stmt->affected_rows===1;$stmt->close();$msg=$ok?$decision:'error';
}
header('Location: artworks.php?msg='.$msg);exit;
