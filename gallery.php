<?php
session_start();
if (empty($_SESSION['user_id'])) { header('Location: login.html'); exit; }
require __DIR__.'/db.php';
require __DIR__.'/gallery-common.php';
$category=trim((string)($_GET['category']??''));
$categories=[];
$categoryResults=$conn->query('SELECT category_name FROM categories ORDER BY category_name');
if ($categoryResults) while($cat=$categoryResults->fetch_assoc()) $categories[]=$cat['category_name'];
if ($category!==''&&!in_array($category,$categories,true)) $category='';
$gallery=miraApprovedArtworks($conn,$category,0,200);
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Student Gallery | MIRA</title><link rel="stylesheet" href="style.css"><link rel="stylesheet" href="gallery.css"><script src="theme.js" defer></script><script src="gallery-search.js" defer></script></head>
<body class="user-home-page mira-gallery-page"><main class="mira-gallery-wrap">
<header class="mira-gallery-top"><a href="user-home.php">← MIRA Home</a><div><a href="upload-artwork.php" class="mira-gallery-upload">+ Upload Artwork</a><button class="theme-toggle" type="button" aria-label="Toggle theme">🌙</button></div></header>
<section class="mira-gallery-heading"><p>NSBM STUDENT ART & CRAFT</p><h1>Explore student creativity.</h1><span>Only administrator-approved work is shown here.</span></section>
<form method="get" class="mira-gallery-filter" aria-label="Category filter"><label for="category">Category</label><select name="category" id="category" onchange="this.form.submit()"><option value="">All Categories</option>
<?php foreach($categories as $name): ?><option value="<?= miraEsc($name) ?>" <?= $category===$name?'selected':'' ?>><?= miraEsc($name) ?></option><?php endforeach; ?></select></form>
<div class="mira-feed-search"><span class="search-icon">⌕</span><input id="artSearch" type="search" placeholder="Search approved artworks, artists or categories..." aria-label="Search approved artworks"></div>
<section class="mira-pin-grid" id="artGrid">
<?php foreach ($gallery as $art): ?><a href="artwork-view.php?id=<?= (int)$art['artwork_id'] ?>" class="mira-pin mira-live-pin" data-search="<?= miraEsc(strtolower($art['title'].' '.$art['artist_name'].' '.$art['category_name'])) ?>"><img loading="lazy" src="<?= miraEsc(miraArtworkImage($art['image_path'])) ?>" alt="<?= miraEsc($art['title']) ?>"><span class="mira-pin-overlay"><strong><?= miraEsc($art['title']) ?></strong><small><?= miraEsc($art['artist_name']) ?> · <?= miraEsc($art['category_name']) ?></small></span></a><?php endforeach; ?>
</section><p class="mira-empty" id="noResults" <?= $gallery?'hidden':'' ?>><?= $gallery?'No artworks match your search.':'No approved artworks in this category yet.' ?></p></main></body></html>
