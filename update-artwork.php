<?php
session_start();if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}if(!isset($_SESSION['user_id'])){header('Location: login.html');exit;}
require __DIR__.'/db.php';require __DIR__.'/artwork-common.php';$uid=(int)$_SESSION['user_id'];if(!artworkIsStudent($conn,$uid)){http_response_code(403);exit('Student only');}if(!artworkValidCsrf()){http_response_code(403);exit('Invalid form token');}
$id=filter_var($_POST['artwork_id']??null,FILTER_VALIDATE_INT);$category=filter_var($_POST['category_id']??null,FILTER_VALIDATE_INT);$title=trim((string)($_POST['title']??''));$description=trim((string)($_POST['description']??''));
if(!$id){http_response_code(400);exit('Invalid artwork');}function failEdit(int $id):never{header('Location: edit-artwork.php?id='.$id.'&error=1');exit;}
if(!$category||$title===''||mb_strlen($title)>200||$description===''||mb_strlen($description)>3000)failEdit($id);
$s=$conn->prepare('SELECT category_id FROM categories WHERE category_id=? AND is_active=1');$s->bind_param('i',$category);$s->execute();$valid=$s->get_result()->num_rows===1;$s->close();if(!$valid)failEdit($id);
$img=null;$dest=null;
if(isset($_FILES['artwork_image']) && $_FILES['artwork_image']['error']!==UPLOAD_ERR_NO_FILE){
 $f=$_FILES['artwork_image'];if($f['error']!==UPLOAD_ERR_OK||$f['size']<1||$f['size']>5*1024*1024||!is_uploaded_file($f['tmp_name']))failEdit($id);
 $info=@getimagesize($f['tmp_name']);$types=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];$mime=$info['mime']??'';
 if(!isset($types[$mime])||$info[0]<1||$info[1]<1||$info[0]>10000||$info[1]>10000)failEdit($id);
 $dir=__DIR__.'/uploads/artworks';if(!is_dir($dir)&&!mkdir($dir,0755,true))failEdit($id);
 $img='uploads/artworks/'.bin2hex(random_bytes(20)).'.'.$types[$mime];$dest=__DIR__.'/'.$img;
 if(!move_uploaded_file($f['tmp_name'],$dest))failEdit($id);
}
try{
 $conn->begin_transaction();
 $s=$conn->prepare("SELECT status FROM artworks WHERE artwork_id=? AND user_id=? AND LOWER(status) IN ('pending','rejected') FOR UPDATE");$s->bind_param('ii',$id,$uid);$s->execute();$art=$s->get_result()->fetch_assoc();$s->close();if(!$art)throw new RuntimeException('Artwork status changed or you do not own it');
 $s=$conn->prepare('UPDATE artworks SET title=?,description=?,category_id=? WHERE artwork_id=? AND user_id=?');$s->bind_param('ssiii',$title,$description,$category,$id,$uid);$s->execute();$s->close();
 if($img!==null){$s=$conn->prepare('SELECT image_id FROM artwork_images WHERE artwork_id=? ORDER BY image_id ASC LIMIT 1');$s->bind_param('i',$id);$s->execute();$old=$s->get_result()->fetch_assoc();$s->close();if($old){$s=$conn->prepare('UPDATE artwork_images SET image_path=? WHERE image_id=? AND artwork_id=?');$s->bind_param('sii',$img,$old['image_id'],$id);}else{$s=$conn->prepare('INSERT INTO artwork_images(artwork_id,image_path) VALUES (?,?)');$s->bind_param('is',$id,$img);}$s->execute();$s->close();}
 $conn->commit();header('Location: my-artworks.php?message=updated');exit;
}catch(Throwable $e){$conn->rollback();if($dest)@unlink($dest);error_log('MIRA edit: '.$e->getMessage());header('Location: my-artworks.php?message=error');exit;}
