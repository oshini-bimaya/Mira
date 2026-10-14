<?php
// Shared admin sidebar. $activeAdminPage identifies current section.
$activeAdminPage = $activeAdminPage ?? '';
$displayName = $adminName ?? ($_SESSION['full_name'] ?? 'Administrator');
$initialLetter = function_exists('mb_substr') ? mb_substr($displayName, 0, 1) : substr($displayName, 0, 1);
?>
<aside class="admin-sidebar">
  <a class="admin-brand" href="dashboard.php" aria-label="MIRA Admin Dashboard"><span class="brand-symbol">✦</span><span class="brand-copy">MIRA <small>ADMIN STUDIO</small></span></a>
  <nav class="admin-nav" aria-label="Admin sections">
    <?php foreach ([['dashboard','dashboard.php','▦ Dashboard'],['artworks','artworks.php','▤ Artworks'],['categories','categories.php','◈ Categories'],['events','events.php','▣ Exhibitions']] as $nav): ?>
      <a href="<?= htmlspecialchars($nav[1], ENT_QUOTES, 'UTF-8') ?>" <?= $activeAdminPage === $nav[0] ? 'class="active" aria-current="page"' : '' ?>><?= htmlspecialchars($nav[2], ENT_QUOTES, 'UTF-8') ?></a>
    <?php endforeach; ?>
    <a href="featured.php" <?= $activeAdminPage === "featured" ? 'class="active" aria-current="page"' : '' ?>>✧ &nbsp; Featured Art</a>
    <a href="students.php" <?= $activeAdminPage === "students" ? 'class="active" aria-current="page"' : '' ?>>♙ &nbsp; Students</a>
    <a href="reports.php" <?= $activeAdminPage === "reports" ? 'class="active" aria-current="page"' : '' ?>>▥ &nbsp; Reports</a>
  </nav>
  <div class="admin-user-box"><div class="admin-user"><span class="admin-avatar" aria-hidden="true"><?= htmlspecialchars(strtoupper($initialLetter), ENT_QUOTES, 'UTF-8') ?></span><div><strong><?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?></strong><span>Administrator</span></div></div><form method="post" action="logout.php"><button class="logout-button" type="submit">↪ &nbsp; Log out</button></form></div>
</aside>
