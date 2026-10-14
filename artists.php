<?php
session_start();
if (empty($_SESSION['user_id'])) { header('Location: login.html'); exit; }
require __DIR__ . '/db.php';
require __DIR__ . '/gallery-common.php';
$artists = $conn->query("SELECT u.USER_ID, u.FULL_NAME, u.ABOUT_ARTIST, u.PROFILE_PHOTO,
    COUNT(DISTINCT a.artwork_id) AS artworks,
    (SELECT COUNT(*) FROM follows f WHERE f.following_id = u.USER_ID) AS followers
    FROM users u LEFT JOIN artworks a ON a.user_id = u.USER_ID AND LOWER(a.status) = 'approved'
    WHERE UPPER(u.ROLE) = 'STUDENT'
    GROUP BY u.USER_ID, u.FULL_NAME, u.ABOUT_ARTIST, u.PROFILE_PHOTO
    ORDER BY artworks DESC, u.FULL_NAME ASC")->fetch_all(MYSQLI_ASSOC);
function miraProfilePhoto($path): string {
    $path = str_replace('\\', '/', (string)$path);
    return preg_match('~^uploads/profiles/[a-zA-Z0-9_.-]+\.(?:jpg|jpeg|png|webp)$~i', $path) ? $path : '';
}
?><!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Artists | MIRA</title>
<link rel="stylesheet" href="style.css"><link rel="stylesheet" href="gallery.css"><link rel="stylesheet" href="mira-student-nav.css?v=2"><link rel="stylesheet" href="artist-enhancements.css?v=1"><script src="theme.js" defer></script>
</head><body class="user-home-page mira-gallery-page mira-artists-directory">
<?php require __DIR__ . '/mira-student-nav.php'; ?>
<main class="mira-gallery-wrap">
<section class="mira-gallery-heading"><p>MIRA COMMUNITY</p><h1>Meet our artists</h1><span>Discover student creators, explore their portfolios and follow your favorites.</span></section>
<div class="mira-artist-grid">
<?php foreach ($artists as $a): $photo = miraProfilePhoto($a['PROFILE_PHOTO']); ?>
<article class="mira-artist-card">
<div class="mira-artist-avatar">
<?php if ($photo !== ''): ?><img loading="lazy" src="<?= miraEsc($photo) ?>" alt="<?= miraEsc($a['FULL_NAME']) ?> profile picture">
<?php else: ?><?= miraEsc(strtoupper(substr(trim($a['FULL_NAME']), 0, 1))) ?><?php endif; ?>
</div>
<h2><?= miraEsc($a['FULL_NAME']) ?></h2>
<p class="mira-artist-bio"><?= miraEsc($a['ABOUT_ARTIST'] ?: 'Sharing creativity on MIRA.') ?></p>
<div class="mira-artist-metrics"><span><strong><?= (int)$a['artworks'] ?></strong> artworks</span><span><strong><?= (int)$a['followers'] ?></strong> followers</span></div>
<a class="mira-artist-button" href="<?= (int)$a['USER_ID'] === (int)$_SESSION['user_id'] ? 'profile.php' : 'artist-profile.php?id='.(int)$a['USER_ID'] ?>"><?= (int)$a['USER_ID'] === (int)$_SESSION['user_id'] ? 'My profile' : 'View profile / Follow' ?> →</a>
</article>
<?php endforeach; ?>
</div>
<?php if (!$artists): ?><p class="mira-artist-empty">No artists found yet.</p><?php endif; ?>
</main></body></html>
