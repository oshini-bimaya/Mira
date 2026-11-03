<?php
require __DIR__.'/student-access.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}
function profileDone(bool $ok,string $message): void {
 $_SESSION['profile_flash']=['ok'=>$ok,'text'=>$message];
 header('Location: profile.php',true,303);exit;
}
if(empty($_SESSION['profile_csrf'])||!isset($_POST['csrf'])||!hash_equals($_SESSION['profile_csrf'],(string)$_POST['csrf'])) profileDone(false,'Invalid form session. Please try again.');
$uid=(int)$_SESSION['user_id'];$action=(string)($_POST['action']??'');
if($action==='details'){
 $name=trim((string)($_POST['full_name']??'')); $email=trim((string)($_POST['email']??''));
 if($name===''||mb_strlen($name)>100||strlen($email)>190||!filter_var($email,FILTER_VALIDATE_EMAIL)) profileDone(false,'Enter a valid name and email.');
 $stmt=$conn->prepare('SELECT USER_ID FROM users WHERE EMAIL=? AND USER_ID<>? LIMIT 1');$stmt->bind_param('si',$email,$uid);$stmt->execute();$taken=$stmt->get_result()->fetch_assoc();$stmt->close();
 if($taken)profileDone(false,'That email is already registered.');
 try{$stmt=$conn->prepare('UPDATE users SET FULL_NAME=?,EMAIL=? WHERE USER_ID=?');$stmt->bind_param('ssi',$name,$email,$uid);$stmt->execute();$stmt->close();}catch(mysqli_sql_exception $e){profileDone(false,'Unable to save details. Please try a different email.');}
 $_SESSION['full_name']=$name;profileDone(true,'Your profile details were updated.');
}
if($action==='password'){
 $current=(string)($_POST['current_password']??'');$new=(string)($_POST['new_password']??'');$confirm=(string)($_POST['confirm_password']??'');
 if(strlen($new)<8||strlen($new)>200||$new!==$confirm)profileDone(false,'Use a matching password with at least 8 characters.');
 $stmt=$conn->prepare('SELECT PASSWORD FROM users WHERE USER_ID=? LIMIT 1');$stmt->bind_param('i',$uid);$stmt->execute();$row=$stmt->get_result()->fetch_assoc();$stmt->close();
 if(!$row||!password_verify($current,$row['PASSWORD'])) profileDone(false,'Current password is incorrect.');
 $hash=password_hash($new,PASSWORD_DEFAULT);$stmt=$conn->prepare('UPDATE users SET PASSWORD=? WHERE USER_ID=?');$stmt->bind_param('si',$hash,$uid);$stmt->execute();$stmt->close();profileDone(true,'Password changed successfully.');
}
if($action==='avatar'){
 $file=$_FILES['profile_photo']??null;
 if(!$file||$file['error']!==UPLOAD_ERR_OK||$file['size']<1||$file['size']>2*1024*1024||!is_uploaded_file($file['tmp_name']))profileDone(false,'Choose a valid picture under 2 MB.');
 $mime=(new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);$allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
 if(!isset($allowed[$mime])||!getimagesize($file['tmp_name']))profileDone(false,'Only valid JPG, PNG or WebP images are supported.');
 $dir=__DIR__.'/uploads/profiles';if(!is_dir($dir)&&!mkdir($dir,0755,true))profileDone(false,'Cannot create profile image directory.');
 $relative='uploads/profiles/'.bin2hex(random_bytes(16)).'.'.$allowed[$mime];
 if(!move_uploaded_file($file['tmp_name'],__DIR__.'/'.$relative))profileDone(false,'Image upload failed.');
 $stmt=$conn->prepare('UPDATE users SET profile_image=? WHERE USER_ID=?');$stmt->bind_param('si',$relative,$uid);
 try{$stmt->execute();$stmt->close();}catch(Throwable $e){@unlink(__DIR__.'/'.$relative);profileDone(false,'Could not save profile picture.');}
 profileDone(true,'Your profile picture was updated.');
}
profileDone(false,'Unknown action.');
