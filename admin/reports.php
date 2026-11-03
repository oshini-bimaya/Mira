<?php
require __DIR__ . '/auth.php';
// Phase 7 requires the Phase 4 and Exhibition Requests tables to be installed.
function reportCount(mysqli $conn, string $query): int {
    return (int) $conn->query($query)->fetch_row()[0];
}
function reportRows(mysqli $conn, string $query): array {
    return $conn->query($query)->fetch_all(MYSQLI_ASSOC);
}
$totals = [
    'students' => reportCount($conn, "SELECT COUNT(*) FROM users WHERE UPPER(ROLE) IN ('STUDENT','ARTIST')"),
    'artworks' => reportCount($conn, 'SELECT COUNT(*) FROM artworks'),
    'approved' => reportCount($conn, "SELECT COUNT(*) FROM artworks WHERE LOWER(status)='approved'"),
    'pending' => reportCount($conn, "SELECT COUNT(*) FROM artworks WHERE LOWER(status)='pending'"),
    'featured' => reportCount($conn, "SELECT COUNT(*) FROM artworks WHERE LOWER(status)='approved' AND is_featured=1"),
    'exhibitions' => reportCount($conn, 'SELECT COUNT(*) FROM events'),
    'registrations' => reportCount($conn, "SELECT COUNT(*) FROM event_registrations WHERE status IN ('registered','attended')"),
    'requests' => reportCount($conn, 'SELECT COUNT(*) FROM exhibition_requests'),
];
$artworkStatuses = reportRows($conn, "SELECT LOWER(status) AS label, COUNT(*) AS total FROM artworks GROUP BY LOWER(status) ORDER BY total DESC");
$categoryTotals = reportRows($conn, 'SELECT c.category_name AS label, COUNT(a.artwork_id) AS total FROM categories c LEFT JOIN artworks a ON a.category_id=c.category_id GROUP BY c.category_id,c.category_name ORDER BY total DESC,c.category_name');
$eventStatuses = reportRows($conn, "SELECT status AS label, COUNT(*) AS total FROM events GROUP BY status ORDER BY total DESC");
$requestStatuses = reportRows($conn, "SELECT status AS label, COUNT(*) AS total FROM exhibition_requests GROUP BY status ORDER BY total DESC");
$topArtists = reportRows($conn, "SELECT u.FULL_NAME AS label, COUNT(a.artwork_id) AS total FROM users u JOIN artworks a ON a.user_id=u.USER_ID WHERE UPPER(u.ROLE) IN ('STUDENT','ARTIST') GROUP BY u.USER_ID,u.FULL_NAME ORDER BY total DESC,u.FULL_NAME LIMIT 5");
// Last six calendar months, including months with no submissions.
$months=[];
$start=new DateTimeImmutable('first day of this month 00:00:00');
for ($i=5;$i>=0;$i--) {
    $month=$start->modify("-$i months");
    $months[$month->format('Y-m')]=['label'=>$month->format('M Y'),'total'=>0];
}
$firstMonth=array_key_first($months);
$stmt=$conn->prepare("SELECT DATE_FORMAT(created_at,'%Y-%m') AS month_key, COUNT(*) AS total FROM artworks WHERE created_at >= ? GROUP BY DATE_FORMAT(created_at,'%Y-%m') ORDER BY month_key");
$from=$firstMonth.'-01 00:00:00';
$stmt->bind_param('s',$from); $stmt->execute();
foreach ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $r) {
    if(isset($months[$r['month_key']])) $months[$r['month_key']]['total']=(int)$r['total'];
}
$stmt->close();
$activeAdminPage='reports';
function reportBars(array $items): void {
    $maximum=max(1,...array_map(static fn($r)=>(int)$r['total'],$items));
    if(!$items) { echo '<p class="report-empty">No data available yet.</p>'; return; }
    foreach($items as $item) {
        $value=(int)$item['total']; $width=round($value/$maximum*100,1);
        echo '<div class="report-bar-row"><div class="report-bar-label"><span>'.htmlspecialchars((string)$item['label'],ENT_QUOTES,'UTF-8').'</span><strong>'.$value.'</strong></div><div class="report-track"><div class="report-fill" style="width:'.$width.'%"></div></div></div>';
    }
}
?><!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Reports &amp; Analytics | MIRA Admin</title>
<script>try{document.documentElement.dataset.theme=localStorage.getItem('mira-theme')==='dark'?'dark':'light'}catch(e){document.documentElement.dataset.theme='light'}</script>
<link rel="stylesheet" href="admin.css"><link rel="stylesheet" href="reports.css"><script src="admin.js" defer></script><link rel="stylesheet" href="../mira-design-system.css?v=1">
<link rel="stylesheet" href="mira-admin-polish.css?v=1">
</head>
<body class="admin-page"><div class="admin-layout">
<?php require __DIR__.'/sidebar.php'; ?>
<main class="admin-main"><header class="admin-topbar"><p class="admin-breadcrumb">MIRA / <strong>Reports &amp; Analytics</strong></p><div class="admin-top-actions"><button id="themeToggle" class="admin-theme-toggle" type="button" aria-label="Toggle dark mode">☾ Dark mode</button></div></header>
<section class="admin-hero"><p class="admin-hero-label">MIRA · ADMIN STUDIO</p><h1>Reports &amp; analytics ✦</h1><p>Live insights into student creativity, submissions and exhibitions.</p></section>
<section class="report-stat-grid" aria-label="Live summary">
<?php foreach (['students'=>'Students','artworks'=>'Artworks','approved'=>'Approved artworks','pending'=>'Pending approval','featured'=>'Featured artworks','exhibitions'=>'Exhibitions','registrations'=>'Active registrations','requests'=>'Artwork requests'] as $key=>$label): ?>
<article class="report-stat"><span><?=h($label)?></span><strong><?=number_format($totals[$key])?></strong></article>
<?php endforeach; ?></section>
<div class="report-toolbar"><h2>Explore the data</h2><div class="report-downloads"><a href="export-report.php?type=artworks">Export artworks CSV</a><a href="export-report.php?type=students">Export students CSV</a><a href="export-report.php?type=exhibitions">Export exhibitions CSV</a><a href="export-report.php?type=requests">Export requests CSV</a></div></div>
<section class="report-grid" aria-label="Breakdowns">
<article class="admin-panel report-panel"><h2>Artwork submission status</h2><p class="admin-panel-subtitle">Breakdown of all uploaded artworks</p><?php reportBars($artworkStatuses); ?></article>
<article class="admin-panel report-panel"><h2>Artwork categories</h2><p class="admin-panel-subtitle">Submissions by category, including empty categories</p><?php reportBars($categoryTotals); ?></article>
<article class="admin-panel report-panel report-wide"><h2>Monthly artwork submissions</h2><p class="admin-panel-subtitle">Last six calendar months</p><div class="report-month-grid"><?php $maxMonth=max(1,...array_column(array_values($months),'total')); foreach($months as $entry): ?><div class="report-month"><div class="report-month-column"><div class="report-month-fill" style="height:<?=round($entry['total']/$maxMonth*100,1)?>%"></div></div><strong><?=(int)$entry['total']?></strong><small><?=h($entry['label'])?></small></div><?php endforeach; ?></div></article>
<article class="admin-panel report-panel"><h2>Exhibition status</h2><p class="admin-panel-subtitle">Draft, published, cancelled and completed events</p><?php reportBars($eventStatuses); ?></article>
<article class="admin-panel report-panel"><h2>Exhibition artwork requests</h2><p class="admin-panel-subtitle">Student request review statuses</p><?php reportBars($requestStatuses); ?></article>
<article class="admin-panel report-panel report-wide"><h2>Most active student artists</h2><p class="admin-panel-subtitle">Ranked by artwork submissions</p><?php reportBars($topArtists); ?></article>
</section><footer class="admin-footer">© <?=date('Y')?> MIRA · NSBM Student Arts &amp; Handcraft Showcasing System</footer></main></div></body></html>
