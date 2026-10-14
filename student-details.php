<?php
require __DIR__.'/auth.php';
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
if(!$id){http_response_code(400);exit('Invalid student ID');}
$stmt=$conn->prepare("SELECT USER_ID,FULL_NAME,EMAIL,CREATED_AT,account_status,suspension_reason,status_updated_at FROM users WHERE USER_ID=? AND UPPER(ROLE)='STUDENT' LIMIT 1");$stmt->bind_param('i',$id);$stmt->execute();$student=$stmt->get_result()->fetch_assoc();$stmt->close();
if(!$student){http_response_code(404);exit('Student not found');}
$stmt=$conn->prepare('SELECT artwork_id,title,status,created_at FROM artworks WHERE user_id=? ORDER BY created_at DESC,artwork_id DESC');$stmt->bind_param('i',$id);$stmt->execute();$artworks=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);$stmt->close();
$registrations=[];
$hasEvents=$conn->query("SHOW TABLES LIKE 'event_registrations'")->num_rows>0;
if($hasEvents){
  $stmt=$conn->prepare('SELECT e.title, r.status FROM event_registrations r JOIN events e ON e.event_id=r.event_id WHERE r.user_id=? ORDER BY e.event_id DESC');$stmt->bind_param('i',$id);$stmt->execute();$registrations=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);$stmt->close();
}
if(empty($_SESSION['mira_student_csrf'])) $_SESSION['mira_student_csrf']=bin2hex(random_bytes(32));
$activeAdminPage='students';
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Student Profile | MIRA Admin</title>
<script>try{document.documentElement.dataset.theme=localStorage.getItem('mira-theme')==='dark'?'dark':'light'}catch(e){}</script><link rel="stylesheet" href="admin.css"><link rel="stylesheet" href="students.css"><script src="admin.js" defer></script></head><body class="admin-page"><div class="admin-layout"><?php require __DIR__.'/sidebar.php';?><main class="admin-main"><header class="admin-topbar"><p class="admin-breadcrumb">MIRA / <strong>Student Profile</strong></p><button id="themeToggle" class="admin-theme-toggle" type="button">☾ Dark mode</button></header>
<p><a class="student-link" href="students.php">← All students</a></p><section class="admin-hero"><h1><?=h($student['FULL_NAME'])?></h1><p><?=h($student['EMAIL'])?> · Joined <?=h(date('M j, Y',strtotime($student['CREATED_AT'])))?></p></section>
<?php if(isset($_GET['updated'])):?><p class="student-message">Account status updated.</p><?php endif;?>
<div class="student-detail-grid"><section class="admin-panel"><h2>Account status</h2><p class="student-current"><span class="student-pill <?=$student['account_status']==='active'?'is-active':'is-suspended'?>"><?=h(ucfirst($student['account_status']))?></span></p>
<?php if($student['suspension_reason']):?><p><strong>Reason:</strong> <?=h($student['suspension_reason'])?></p><?php endif;?>
<form method="post" action="update-student.php" class="student-status-form" onsubmit="return confirm('Confirm this account status change?');"><input type="hidden" name="csrf" value="<?=h($_SESSION['mira_student_csrf'])?>"><input type="hidden" name="student_id" value="<?=(int)$id?>">
<?php if($student['account_status']==='active'):?><input type="hidden" name="action" value="suspend"><label for="reason">Reason for suspension</label><textarea name="reason" id="reason" maxlength="500" required placeholder="Why is this account being suspended?"></textarea><button type="submit" class="student-danger">Suspend student</button>
<?php else:?><input type="hidden" name="action" value="reactivate"><button type="submit">Reactivate student</button><?php endif;?></form></section>
<section class="admin-panel"><h2>Artwork history (<?=count($artworks)?>)</h2><div class="admin-table-scroll"><table class="admin-table"><thead><tr><th>Artwork</th><th>Status</th><th>Date</th></tr></thead><tbody><?php foreach($artworks as $a):?><tr><td><?=h($a['title'])?></td><td><?=h(ucfirst($a['status']))?></td><td><?=h(date('M j, Y',strtotime($a['created_at'])))?></td></tr><?php endforeach;?><?php if(!$artworks):?><tr><td colspan="3">No uploaded artwork yet.</td></tr><?php endif;?></tbody></table></div></section>
<section class="admin-panel"><h2>Exhibition registrations (<?=count($registrations)?>)</h2><div class="admin-table-scroll"><table class="admin-table"><thead><tr><th>Exhibition</th><th>Status</th></tr></thead><tbody><?php foreach($registrations as $r):?><tr><td><?=h($r['title'])?></td><td><?=h(ucfirst($r['status']))?></td></tr><?php endforeach;?><?php if(!$registrations):?><tr><td colspan="2">No registrations yet.</td></tr><?php endif;?></tbody></table></div></section></div></main></div></body></html>