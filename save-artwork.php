<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method not allowed'); }
if (!isset($_SESSION['user_id'])) { header('Location: login.html');exit; }
require __DIR__.'/db.php';require __DIR__.'/artwork-common.php';
$uid=(int)$_SESSION['user_id'];
if (!artworkIsStudent($conn,$uid)) { http_response_code(403);exit('Student access only'); }
if (!artworkValidCsrf()) {http_response_code(403); exit('Invalid form token. Refresh and try again.');}
$title=trim((string)($_POST['title']??''));$description=trim((string)($_POST['description']??''));$category=filter_var($_POST['category_id']??null,FILTER_VALIDATE_INT);
if ($title===''||strlen($title)>200||$description===''||strlen($description)>3000||!$category) {header('Location: upload-artwork.php?status=error');exit;}
$check=$conn->prepare('SELECT category_id FROM categories WHERE category_id=?');$check->bind_param('i',$category);$check->execute();$validCat=$check->get_result()->num_rows===1;$check->close();
if(!$validCat||!isset($_FILES['artwork_image'])||!is_array($_FILES['artwork_image'])||$_FILES['artwork_image']['error']!==UPLOAD_ERR_OK){header('Location: upload-artwork.php?status=error');exit;}
$f=$_FILES['artwork_image'];
if($f['size']<1||$f['size']>5*1024*1024||!is_uploaded_file($f['tmp_name'])){header('Location: upload-artwork.php?status=error');exit;}
$info=@getimagesize($f['tmp_name']);$mime=$info['mime']??'';
$types=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
if(!isset($types[$mime])||$info[0]<1||$info[1]<1||$info[0]>10000||$info[1]>10000){header('Location: upload-artwork.php?status=error');exit;}
$dir=__DIR__.'/uploads/artworks';if(!is_dir($dir)&&!mkdir($dir,0755,true)){http_response_code(500);exit('Upload storage is unavailable.');}
$filename=bin2hex(random_bytes(20)).'.'.$types[$mime];$dest=$dir.'/'.$filename;$relative='uploads/artworks/'.$filename;
if(!move_uploaded_file($f['tmp_name'],$dest)){http_response_code(500);exit('Could not save image.');}
try {
 $conn->begin_transaction();
 $stmt=$conn->prepare("INSERT INTO artworks (user_id,category_id,title,description,status,is_featured) VALUES (?,?,?,?,'pending',0)");
 $stmt->bind_param('iiss',$uid,$category,$title,$description);$stmt->execute();$artworkId=$conn->insert_id;$stmt->close();
 $stmt=$conn->prepare('INSERT INTO artwork_images (artwork_id,image_path) VALUES (?,?)');
 $stmt->bind_param('is',$artworkId,$relative);$stmt->execute();$stmt->close();
 $conn->commit(); header('Location: upload-artwork.php?status=success');exit;
} catch(Throwable $e){$conn->rollback();@unlink($dest);error_log('MIRA artwork upload: '.$e->getMessage());http_response_code(500);exit('Unable to save the artwork. Check the database migration and try again.');}
