<?php
session_start();
if (empty($_SESSION['user_id'])) { header('Location: login.html'); exit; }
require __DIR__.'/db.php';
require __DIR__.'/gallery-common.php';
if (empty($_SESSION['artwork_csrf'])) $_SESSION['artwork_csrf']=bin2hex(random_bytes(32));
$uid=(int)$_SESSION['user_id'];
$stmt=$conn->prepare("SELECT a.artwork_id,a.title,u.FULL_NAME AS artist_name,c.category_name,
 (SELECT ai.image_path FROM artwork_images ai WHERE ai.artwork_id=a.artwork_id ORDER BY ai.image_id LIMIT 1) AS image_path
 FROM saved_artworks s JOIN artworks a ON a.artwork_id=s.artwork_id
 JOIN users u ON u.USER_ID=a.user_id JOIN categories c ON c.category_id=a.category_id
 WHERE s.user_id=? AND LOWER(a.status)='approved' ORDER BY s.saved_at DESC,a.artwork_id DESC");
$stmt->bind_param('i',$uid);$stmt->execute();$saved=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);$stmt->close();
$saved=array_values(array_filter($saved,fn($a)=>miraArtworkImage($a['image_path'])!==''));
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Saved Artworks | MIRA</title><link rel="stylesheet" href="style.css"><link rel="stylesheet" href="gallery.css"><link rel="stylesheet" href="mira-student-nav.css?v=3"><link rel="stylesheet" href="saved-artworks.css"><script src="theme.js" defer></script><link rel="stylesheet" href="mira-design-system.css?v=1">
</head>
<body class="user-home-page mira-gallery-page"><?php require __DIR__.'/mira-student-nav.php'; ?><main class="mira-gallery-wrap mira-saved-page">
<section class="mira-gallery-heading"><p>YOUR COLLECTION</p><h1>Saved Artworks</h1><span>Explore the artworks you have bookmarked (<?= count($saved) ?>).</span></section>
<?php if (!$saved): ?><section class="mira-saved-empty"><h2>Nothing saved yet</h2><p>Explore categories and save the artworks that inspire you.</p><a href="gallery.php">Explore artworks →</a></section><?php else: ?>
<section class="mira-saved-grid" aria-label="Your saved artworks">
<?php foreach($saved as $art): ?><article class="mira-saved-card">
<a class="mira-saved-image" href="artwork-view.php?id=<?= (int)$art['artwork_id'] ?>"><img loading="lazy" src="<?= miraEsc(miraArtworkImage($art['image_path'])) ?>" alt="<?= miraEsc($art['title']) ?>"></a>
<div class="mira-saved-info"><h2><a href="artwork-view.php?id=<?= (int)$art['artwork_id'] ?>"><?= miraEsc($art['title']) ?></a></h2><p><?= miraEsc($art['artist_name']) ?> · <?= miraEsc($art['category_name']) ?></p>
<form method="post" action="save-artwork.php"><input type="hidden" name="csrf_token" value="<?= miraEsc($_SESSION['artwork_csrf']) ?>"><input type="hidden" name="artwork_id" value="<?= (int)$art['artwork_id'] ?>"><input type="hidden" name="action" value="remove"><input type="hidden" name="return_to" value="saved"><button type="submit">Remove from Saved</button></form></div></article><?php endforeach; ?></section><?php endif; ?></main></body></html>
