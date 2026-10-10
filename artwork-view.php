<?php
session_start();
if (empty($_SESSION['user_id'])) { header('Location: login.html'); exit; }
require __DIR__.'/db.php';require __DIR__.'/gallery-common.php';
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
$art=($id&&$id>0)?(miraApprovedArtworks($conn,'',(int)$id,1)[0]??null):null;
if(!$art){http_response_code(404);}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= $art?miraEsc($art['title']).' | MIRA':'Artwork Not Found | MIRA' ?></title><link rel="stylesheet" href="style.css"><link rel="stylesheet" href="gallery.css"><script src="theme.js" defer></script></head><body class="user-home-page mira-gallery-page"><main class="mira-gallery-wrap"><header class="mira-gallery-top"><a href="gallery.php">← Back to gallery</a><button type="button" class="theme-toggle">🌙</button></header>
<?php if(!$art): ?><div class="mira-empty"><h1>Artwork unavailable</h1><p>This artwork is not approved, or it no longer exists.</p></div>
<?php else: ?><section class="mira-artwork-detail"><div class="mira-detail-photo"><img src="<?= miraEsc(miraArtworkImage($art['image_path'])) ?>" alt="<?= miraEsc($art['title']) ?>"></div><div class="mira-detail-copy"><p class="mira-detail-label"><?= miraEsc($art['category_name']) ?></p><h1><?= miraEsc($art['title']) ?></h1><p class="mira-detail-artist">Created by <?= miraEsc($art['artist_name']) ?></p><p class="mira-detail-desc"><?= nl2br(miraEsc($art['description']??'')) ?></p><p class="mira-detail-date">Shared <?= miraEsc(date('M j, Y',strtotime($art['created_at']))) ?></p></div></section><?php endif; ?></main></body></html>
