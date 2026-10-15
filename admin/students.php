<?php
require __DIR__ . '/auth.php';
$search = trim((string)($_GET['q'] ?? ''));
$status = (string)($_GET['status'] ?? 'all');
if (!in_array($status, ['all','active','suspended'], true)) $status='all';
if (strlen($search)>120) $search=substr($search,0,120);
$like='%'.$search.'%';
$query = "SELECT u.USER_ID, u.FULL_NAME, u.EMAIL, u.CREATED_AT, u.account_status,
 (SELECT COUNT(*) FROM artworks a WHERE a.user_id=u.USER_ID) artwork_count,
 (SELECT COUNT(*) FROM artworks a WHERE a.user_id=u.USER_ID AND LOWER(a.status)='approved') approved_count
 FROM users u WHERE UPPER(u.ROLE) IN ('STUDENT','ARTIST') AND (u.FULL_NAME LIKE ? OR u.EMAIL LIKE ?)";
if ($status !== 'all') $query.=' AND u.account_status=?';
$query.=' ORDER BY u.CREATED_AT DESC, u.USER_ID DESC';
$stmt=$conn->prepare($query);
if ($status==='all') $stmt->bind_param('ss',$like,$like); else $stmt->bind_param('sss',$like,$like,$status);
$stmt->execute();$students=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);$stmt->close();
$activeAdminPage='students';
?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Students | MIRA Admin</title>
<script>try{document.documentElement.dataset.theme=localStorage.getItem('mira-theme')==='dark'?'dark':'light'}catch(e){}</script>
<link rel="stylesheet" href="admin.css"><link rel="stylesheet" href="students.css"><script src="admin.js" defer></script><link rel="stylesheet" href="../mira-design-system.css?v=1">
<link rel="stylesheet" href="mira-admin-polish.css?v=1">
</head>
<body class="admin-page"><div class="admin-layout"><?php require __DIR__.'/sidebar.php'; ?><main class="admin-main">
<header class="admin-topbar"><p class="admin-breadcrumb">MIRA / <strong>Students</strong></p><button id="themeToggle" class="admin-theme-toggle" type="button">☾ Dark mode</button></header>
<section class="admin-hero"><p class="admin-hero-label">MIRA · ADMIN STUDIO</p><h1>Student management ✦</h1><p>Explore student accounts and review their activity.</p></section>
<section class="admin-panel"><div class="student-head"><h2>Registered students (<?=count($students)?>)</h2><form method="get" class="student-filters"><input name="q" value="<?=h($search)?>" placeholder="Search name or email" aria-label="Search students"><select name="status" aria-label="Account status"><option value="all" <?=$status==='all'?'selected':''?>>All statuses</option><option value="active" <?=$status==='active'?'selected':''?>>Active</option><option value="suspended" <?=$status==='suspended'?'selected':''?>>Suspended</option></select><button type="submit">Search</button></form></div>
<div class="admin-table-scroll"><table class="admin-table"><thead><tr><th>Name</th><th>Email</th><th>Joined</th><th>Artworks</th><th>Approved</th><th>Status</th><th>Details</th></tr></thead><tbody>
<?php foreach($students as $s):?><tr><td><strong><?=h($s['FULL_NAME'])?></strong></td><td><?=h($s['EMAIL'])?></td><td><?=h(date('M j, Y',strtotime($s['CREATED_AT'])))?></td><td><?=(int)$s['artwork_count']?></td><td><?=(int)$s['approved_count']?></td><td><span class="student-pill <?=$s['account_status']==='active'?'is-active':'is-suspended'?>"><?=h(ucfirst($s['account_status']))?></span></td><td><a class="student-link" href="student-details.php?id=<?=(int)$s['USER_ID']?>">View profile →</a></td></tr><?php endforeach; ?>
<?php if (!$students):?><tr><td colspan="7">No students match your search.</td></tr><?php endif;?></tbody></table></div></section>
<footer class="admin-footer">© <?=date('Y')?> MIRA · NSBM Student Arts &amp; Handcraft Showcasing System</footer></main></div></body></html>