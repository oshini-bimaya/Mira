<?php
require __DIR__ . '/auth.php';
if (empty($_SESSION['featured_csrf'])) $_SESSION['featured_csrf'] = bin2hex(random_bytes(32));
function fe($v): string { return htmlspecialchars((string)$v, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }
$featured = $conn->query("SELECT a.artwork_id,a.title,a.featured_order,u.FULL_NAME AS artist,c.category_name,
 (SELECT ai.image_path FROM artwork_images ai WHERE ai.artwork_id=a.artwork_id ORDER BY ai.image_id LIMIT 1) AS image_path
 FROM artworks a JOIN users u ON a.user_id=u.USER_ID JOIN categories c ON a.category_id=c.category_id
 WHERE LOWER(a.status)='approved' AND a.is_featured=1
 ORDER BY COALESCE(a.featured_order,2147483647),a.artwork_id")->fetch_all(MYSQLI_ASSOC);
$available = $conn->query("SELECT a.artwork_id,a.title,u.FULL_NAME AS artist,c.category_name,
 (SELECT ai.image_path FROM artwork_images ai WHERE ai.artwork_id=a.artwork_id ORDER BY ai.image_id LIMIT 1) AS image_path
 FROM artworks a JOIN users u ON a.user_id=u.USER_ID JOIN categories c ON a.category_id=c.category_id
 WHERE LOWER(a.status)='approved' AND a.is_featured=0 ORDER BY a.created_at DESC LIMIT 100")->fetch_all(MYSQLI_ASSOC);
$message=['added'=>'Artwork added to featured collection.','removed'=>'Artwork removed from featured collection.','moved'=>'Order updated.','invalid'=>'Invalid operation.','error'=>'Could not update. Check migration and try again.'][($_GET['msg']??'')]??'';
function featuredImg($p): string {return preg_match('~^uploads/artworks/[a-zA-Z0-9_-]+\.(jpg|jpeg|png|webp)$~i',(string)$p)?'../'.$p:'';}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Featured Art | MIRA Admin</title>
<script>try{document.documentElement.dataset.theme=localStorage.getItem('mira-theme')==='dark'?'dark':'light'}catch(e){}</script>
<link rel="stylesheet" href="admin.css"><link rel="stylesheet" href="featured.css"><script src="admin.js" defer></script></head>
<body class="admin-page"><div class="admin-layout"><?php $activeAdminPage='featured';require __DIR__.'/sidebar.php'; ?>
<main class="admin-main"><header class="admin-topbar"><p class="admin-breadcrumb">MIRA / <strong>Featured Art</strong></p><button id="themeToggle" class="admin-theme-toggle" type="button">☾ Dark mode</button></header>
<section class="admin-hero"><p class="admin-hero-label">MIRA · CURATED COLLECTION</p><h1>Featured artworks ✦</h1><p>Choose approved artwork for the homepage and arrange its order.</p></section>
<?php if($message): ?><p class="featured-notice" role="status"><?=fe($message)?></p><?php endif; ?>
<section class="admin-panel featured-section"><h2>Currently featured (<?=count($featured)?>)</h2><p class="admin-panel-subtitle">Use Up and Down to control the order shown on the homepage.</p>
<div class="featured-grid"><?php foreach($featured as $i=>$a): ?><article class="featured-card"><?php if(featuredImg($a['image_path'])): ?><img src="<?=fe(featuredImg($a['image_path']))?>" alt="<?=fe($a['title'])?>"><?php endif; ?><div class="featured-card-body"><strong><?=fe($a['title'])?></strong><small><?=fe($a['artist'])?> · <?=fe($a['category_name'])?></small><div class="featured-actions">
<?php foreach (['up'=>'↑ Up','down'=>'↓ Down','remove'=>'Remove'] as $action=>$text): ?><?php if(($action==='up'&&$i===0)||($action==='down'&&$i===count($featured)-1))continue;?><form method="post" action="save-featured.php"><input type="hidden" name="csrf" value="<?=fe($_SESSION['featured_csrf'])?>"><input type="hidden" name="artwork_id" value="<?=(int)$a['artwork_id']?>"><input type="hidden" name="action" value="<?=$action?>"><button type="submit" class="<?= $action==='remove'?'featured-danger':'' ?>"><?=$text?></button></form><?php endforeach; ?></div></div></article><?php endforeach; ?></div>
<?php if(!$featured):?><p class="featured-empty">No featured artworks yet. Add one from the collection below.</p><?php endif;?></section>
<section class="admin-panel featured-section"><h2>Approved artworks</h2><p class="admin-panel-subtitle">Only administrator-approved artworks can be featured.</p><div class="featured-grid"><?php foreach($available as $a):?><article class="featured-card"><?php if(featuredImg($a['image_path'])): ?><img src="<?=fe(featuredImg($a['image_path']))?>" alt="<?=fe($a['title'])?>"><?php endif;?><div class="featured-card-body"><strong><?=fe($a['title'])?></strong><small><?=fe($a['artist'])?> · <?=fe($a['category_name'])?></small><form method="post" action="save-featured.php"><input type="hidden" name="csrf" value="<?=fe($_SESSION['featured_csrf'])?>"><input type="hidden" name="artwork_id" value="<?=(int)$a['artwork_id']?>"><input type="hidden" name="action" value="add"><button class="featured-add" type="submit">✦ Feature artwork</button></form></div></article><?php endforeach;?></div><?php if(!$available):?><p class="featured-empty">No additional approved artworks available.</p><?php endif;?></section>
</main></div></body></html>
