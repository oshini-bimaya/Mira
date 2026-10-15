<?php

require __DIR__ . '/mira-view-access.php';

// Prevent users from opening this page without logging in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}


$userName = $_SESSION["full_name"] ?? 'Artist';
require __DIR__ . '/db.php';
require __DIR__ . '/gallery-common.php';
$gallery = miraApprovedArtworks($conn, '', 0, 60);
$featuredArtworks=[];
$featuredResult=$conn->query("SELECT a.artwork_id,a.title,u.FULL_NAME AS artist_name,
 (SELECT ai.image_path FROM artwork_images ai WHERE ai.artwork_id=a.artwork_id ORDER BY ai.image_id LIMIT 1) AS image_path
 FROM artworks a JOIN users u ON u.USER_ID=a.user_id
 WHERE LOWER(a.status)='approved' AND a.is_featured=1
 ORDER BY COALESCE(a.featured_order,2147483647),a.artwork_id LIMIT 8");
if($featuredResult)$featuredArtworks=$featuredResult->fetch_all(MYSQLI_ASSOC);
$exhibitionPosters=[];
$posterResult=$conn->query("SELECT event_id,title,venue,starts_at,poster_path FROM events WHERE status='published' AND ends_at >= NOW() AND poster_path IS NOT NULL AND poster_path <> '' ORDER BY starts_at ASC LIMIT 6");
if($posterResult){$exhibitionPosters=$posterResult->fetch_all(MYSQLI_ASSOC);}



$miraUnreadNotifications=0;
$ns=$conn->prepare('SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0');
$notifUid=(int)$_SESSION['user_id'];$ns->bind_param('i',$notifUid);$ns->execute();$ns->bind_result($miraUnreadNotifications);$ns->fetch();$ns->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MIRA | Home</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="gallery.css">
<link rel="stylesheet" href="featured-home.css">
    <script src="theme.js" defer></script>
<link rel="stylesheet" href="exhibition-home.css">
<style>.mira-notification-link{position:relative;display:inline-flex;align-items:center;justify-content:center;text-decoration:none}.mira-notification-count{position:absolute;top:-8px;right:-10px;background:#e04b7a;color:white;border-radius:20px;min-width:20px;height:20px;display:grid;place-items:center;font:700 11px Arial,sans-serif;padding:0 4px}</style>
<link rel="stylesheet" href="mira-student-nav.css?v=2">
<link rel="stylesheet" href="mira-professional.css?v=1">
</head>

<body class="user-home-page"><?php require __DIR__ . "/mira-student-nav.php"; ?>


    <main class="mira-feed-page mira-editorial-home">
      <section class="mira-editorial-hero" aria-labelledby="miraWelcomeTitle">
        <div class="mira-editorial-hero-copy">
          <p class="mira-eyebrow">NSBM GREEN UNIVERSITY · STUDENT CREATIVITY</p>
          <h1 id="miraWelcomeTitle">A home for ideas, art and imagination<span class="mira-accent-dot">.</span></h1>
          <p class="mira-hero-lead">Explore original work from our creative community, discover emerging artists and take part in exhibitions.</p>
          <div class="mira-hero-actions">
            <a class="mira-button-main" href="gallery.php">Explore categories <span aria-hidden="true">↗</span></a>
            <a class="mira-button-secondary" href="exhibitions.php">Upcoming exhibitions</a>
          </div>
          <div class="mira-hero-micro"><span class="mira-micro-icon" aria-hidden="true">✦</span> Made by students, celebrated by MIRA</div>
        </div>
        <div class="mira-editorial-hero-media" aria-hidden="true">
          <img src="Assets/art1.jpeg" alt="" loading="eager">
          <span class="mira-hero-media-caption">A space to see creativity differently</span>
        </div>
      </section>

      <section class="mira-featured-home" aria-labelledby="featuredArtTitle">
        <div class="mira-featured-head mira-editorial-section-head">
          <div><p class="mira-eyebrow">CURATED BY MIRA</p><h2 id="featuredArtTitle">Featured creations<span class="mira-accent-dot">.</span></h2>
          <span class="mira-section-description">Thoughtful work from the artists in our community.</span></div>
          <a class="mira-text-link" href="gallery.php">Discover more <span aria-hidden="true">↗</span></a>
        </div>
        <?php if ($featuredArtworks): ?>
        <div class="mira-featured-tiles">
          <?php foreach ($featuredArtworks as $fa): ?>
            <?php $fp=miraArtworkImage($fa['image_path']); if (!$fp) continue; ?>
            <a class="mira-featured-tile" href="artwork-view.php?id=<?=(int)$fa['artwork_id']?>">
              <img src="<?=miraEsc($fp)?>" alt="<?=miraEsc($fa['title'])?>" loading="lazy">
              <div><strong><?=miraEsc($fa['title'])?></strong><small>By <?=miraEsc($fa['artist_name'])?></small></div>
            </a>
          <?php endforeach; ?>
        </div>
        <?php else: ?><p class="mira-curated-empty">Featured student artwork will appear here soon.</p><?php endif; ?>
      </section>

      <section class="mira-exhibition-showcase" aria-labelledby="miraExhibitHeading">
        <div class="mira-exhibition-heading mira-editorial-section-head">
          <div><p class="mira-eyebrow">BE PART OF SOMETHING</p><h2 id="miraExhibitHeading">Exhibitions & events<span class="mira-accent-dot">.</span></h2>
          <span class="mira-section-description">Discover opportunities to connect and create together.</span></div>
          <a class="mira-text-link" href="exhibitions.php">View exhibitions <span aria-hidden="true">↗</span></a>
        </div>
        <div class="mira-exhibition-posters">
          <?php foreach ($exhibitionPosters as $poster): ?>
            <?php if(preg_match('~^uploads/events/[a-zA-Z0-9_-]+\.(jpg|jpeg|png|webp)$~i',(string)$poster['poster_path'])): ?>
              <a class="mira-exhibition-tile" href="exhibition-detail.php?id=<?=(int)$poster['event_id']?>">
                <img src="<?=htmlspecialchars($poster['poster_path'],ENT_QUOTES,'UTF-8')?>" alt="<?=htmlspecialchars($poster['title'],ENT_QUOTES,'UTF-8')?> exhibition poster" loading="lazy">
                <div><h3><?=htmlspecialchars($poster['title'],ENT_QUOTES,'UTF-8')?></h3><p><?=htmlspecialchars(date('M j, Y',strtotime($poster['starts_at'])),ENT_QUOTES,'UTF-8')?> · <?=htmlspecialchars($poster['venue'],ENT_QUOTES,'UTF-8')?></p></div>
              </a>
            <?php endif; ?>
          <?php endforeach; ?>
          <?php if (!$exhibitionPosters): ?><p class="mira-no-exhibitions">Upcoming exhibition posters will appear here.</p><?php endif; ?>
        </div>
      </section>

      <section class="mira-discover-section" aria-labelledby="miraDiscoverTitle">
        <div class="mira-editorial-section-head"><div><p class="mira-eyebrow">EXPLORE THE COLLECTION</p><h2 id="miraDiscoverTitle">Discover artworks<span class="mira-accent-dot">.</span></h2>
        <span class="mira-section-description">Art worth pausing for, shared by MIRA artists.</span></div><a class="mira-text-link" href="gallery.php">Browse categories <span aria-hidden="true">↗</span></a></div>
        <div class="mira-feed-search"><span class="search-icon" aria-hidden="true">⌕</span><input type="search" id="artSearch" placeholder="Search titles, artists or categories..." aria-label="Search artworks"></div>
        <div class="mira-pin-grid" id="artGrid" aria-label="Approved student artworks">
          <?php foreach ($gallery as $art): ?>
            <a class="mira-pin mira-live-pin" href="artwork-view.php?id=<?= (int)$art['artwork_id'] ?>" data-search="<?= miraEsc(strtolower($art['title'].' '.$art['artist_name'].' '.$art['category_name'])) ?>">
              <img loading="lazy" src="<?= miraEsc(miraArtworkImage($art['image_path'])) ?>" alt="<?= miraEsc($art['title']) ?>">
              <span class="mira-pin-overlay"><strong><?= miraEsc($art['title']) ?></strong><small><?= miraEsc($art['artist_name']) ?> · <?= miraEsc($art['category_name']) ?></small></span>
            </a>
          <?php endforeach; ?>
        </div>
        <p id="noResults" class="mira-empty" <?= $gallery ? 'hidden' : '' ?>><?= $gallery ? 'No artworks match your search.' : 'Approved artwork will appear here once it has been reviewed.' ?></p>
      </section>
      <footer class="mira-editorial-footer"><span>MIRA · NSBM Student Arts &amp; Handcraft</span><span>Made for creativity, made for community ✦</span></footer>
    </main>

    <script src="gallery-search.js" defer></script>

</body>
</html>