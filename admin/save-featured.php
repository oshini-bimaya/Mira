<?php
require __DIR__.'/auth.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}
if(!isset($_SESSION['featured_csrf'],$_POST['csrf'])||!hash_equals($_SESSION['featured_csrf'],(string)$_POST['csrf'])){http_response_code(403);exit('Invalid form token');}
$id=filter_var($_POST['artwork_id']??null,FILTER_VALIDATE_INT);
$action=$_POST['action']??'';
if(!$id||!in_array($action,['add','remove','up','down'],true)){header('Location: featured.php?msg=invalid');exit;}
try {
 $conn->begin_transaction();
 // Serialize changes through the artwork row lock.
 $stmt=$conn->prepare('SELECT artwork_id,is_featured,status FROM artworks WHERE artwork_id=? FOR UPDATE');$stmt->bind_param('i',$id);$stmt->execute();$art=$stmt->get_result()->fetch_assoc();$stmt->close();
 if(!$art||strtolower($art['status'])!=='approved')throw new RuntimeException('Not approved');
 if($action==='add'){
  $stmt=$conn->prepare('SELECT COALESCE(MAX(featured_order),0)+1 AS next_order FROM artworks WHERE is_featured=1');$stmt->execute();$pos=(int)$stmt->get_result()->fetch_assoc()['next_order'];$stmt->close();
  $stmt=$conn->prepare("UPDATE artworks SET is_featured=1,featured_order=? WHERE artwork_id=? AND LOWER(status)='approved'");$stmt->bind_param('ii',$pos,$id);$stmt->execute();$stmt->close();
 }elseif($action==='remove'){
  $stmt=$conn->prepare('UPDATE artworks SET is_featured=0,featured_order=NULL WHERE artwork_id=?');$stmt->bind_param('i',$id);$stmt->execute();$stmt->close();
 } else {
  $rows=$conn->query("SELECT artwork_id FROM artworks WHERE is_featured=1 AND LOWER(status)='approved' ORDER BY COALESCE(featured_order,2147483647),artwork_id FOR UPDATE")->fetch_all(MYSQLI_ASSOC);
  $ids=array_map(fn($r)=>(int)$r['artwork_id'],$rows);$idx=array_search($id,$ids,true);
  if($idx===false)throw new RuntimeException('Not featured');
  $target=$idx+($action==='up'?-1:1);
  if($target>=0&&$target<count($ids)) {[$ids[$idx],$ids[$target]]=[$ids[$target],$ids[$idx]];}
  $s=$conn->prepare('UPDATE artworks SET featured_order=? WHERE artwork_id=?');foreach($ids as $i=>$artId){$pos=$i+1;$s->bind_param('ii',$pos,$artId);$s->execute();}$s->close();
 }
 $conn->commit();header('Location: featured.php?msg='.($action==='add'?'added':($action==='remove'?'removed':'moved')));exit;
} catch(Throwable $e) {$conn->rollback();error_log('Featured error: '.$e->getMessage());header('Location: featured.php?msg=error');exit;}
