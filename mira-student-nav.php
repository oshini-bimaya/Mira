<?php
// Shared student navigation. Include after session_start() and inside <body>.
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$navName = (string)($_SESSION['full_name'] ?? 'Artist');
$navCurrent = basename($_SERVER['SCRIPT_NAME'] ?? '');
$navSection = match ($navCurrent) {
    'user-home.php' => 'home',
    'gallery.php', 'artwork-view.php' => 'categories',
    'artists.php', 'artist-profile.php' => 'artists',
    'exhibitions.php','exhibition-detail.php','request-exhibition.php','my-events.php' => 'exhibitions',
    default => ''
};
$navUnread = 0;
if (isset($conn) && $conn instanceof mysqli && isset($_SESSION['user_id'])) {
    try {
        $navStmt = $conn->prepare('SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0');
        $navId = (int)$_SESSION['user_id'];
        $navStmt->bind_param('i', $navId);
        $navStmt->execute();
        $navStmt->bind_result($navUnread);
        $navStmt->fetch();
        $navStmt->close();
    } catch (Throwable $e) { $navUnread = 0; }
}
?>
<header class="mira-shared-nav" aria-label="Student navigation">
  <a class="mira-shared-logo" href="user-home.php"><img src="Assets/Logo.jpeg" alt="MIRA Home"></a>
  <nav class="mira-shared-links" aria-label="Main navigation">
    <a href="user-home.php" <?= $navSection==='home'?'aria-current="page"':'' ?>>Home</a>
    <a href="gallery.php" <?= $navSection==='categories'?'aria-current="page"':'' ?>>Categories</a>
    <a href="artists.php" <?= $navSection==='artists'?'aria-current="page"':'' ?>>Artists</a>
    <a href="exhibitions.php" <?= $navSection==='exhibitions'?'aria-current="page"':'' ?>>Exhibitions</a>
  </nav>
  <div class="mira-shared-actions">
    <button class="mira-shared-theme theme-toggle" type="button" aria-label="Toggle theme" title="Toggle theme">☀️</button>
    <a class="mira-shared-upload" href="upload-artwork.php">+ Upload</a>
    <a class="mira-shared-bell" href="notifications.php" aria-label="Notifications" title="Notifications">♧<?php if($navUnread>0): ?><span class="mira-shared-count"><?= $navUnread>99?'99+':(int)$navUnread ?></span><?php endif; ?></a>
    <div class="mira-shared-profile">
      <button type="button" class="mira-shared-profile-button" aria-expanded="false" aria-controls="mira-shared-dropdown"><span class="mira-shared-avatar"><?= htmlspecialchars(strtoupper(substr($navName,0,1)),ENT_QUOTES,'UTF-8') ?></span><span class="mira-shared-name"><?= htmlspecialchars($navName,ENT_QUOTES,'UTF-8') ?></span><span aria-hidden="true">⌄</span></button>
      <div id="mira-shared-dropdown" class="mira-shared-dropdown" hidden>
        <a href="profile.php">My Profile</a>
        <a href="my-artworks.php">My Artworks / Submission History</a>
        <a href="my-events.php">My Exhibitions</a>
        <a href="artists.php">Browse Artists</a>
        <a href="logout.php">Log Out</a>
      </div>
    </div>
  </div>
</header>
<script src="mira-student-nav.js" defer></script>
