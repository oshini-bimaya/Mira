<?php
require __DIR__ . '/auth.php';

function countQuery(mysqli $conn, string $sql): int {
    $result = $conn->query($sql);
    return $result ? (int) $result->fetch_row()[0] : 0;
}

$totalStudents = countQuery($conn, "SELECT COUNT(*) FROM users WHERE ROLE = 'STUDENT'");
$totalArtworks = countQuery($conn, 'SELECT COUNT(*) FROM artworks');
$pendingArtworks = countQuery($conn, "SELECT COUNT(*) FROM artworks WHERE LOWER(status) = 'pending'");
$approvedArtworks = countQuery($conn, "SELECT COUNT(*) FROM artworks WHERE LOWER(status) = 'approved'");
$categoryCount = countQuery($conn, 'SELECT COUNT(*) FROM categories');
$featuredCount = countQuery($conn, "SELECT COUNT(*) FROM artworks WHERE is_featured = 1 AND LOWER(status) = 'approved'");

$recent = $conn->query(
    'SELECT a.artwork_id, a.title, a.status, a.created_at,
            u.FULL_NAME AS artist_name, c.category_name
     FROM artworks a
     JOIN users u ON u.USER_ID = a.user_id
     JOIN categories c ON c.category_id = a.category_id
     ORDER BY a.created_at DESC, a.artwork_id DESC LIMIT 8'
);
$stats = [
    ['Students', $totalStudents, '♙'],
    ['Artworks', $totalArtworks, '▧'],
    ['Pending approval', $pendingArtworks, '◷'],
    ['Approved artworks', $approvedArtworks, '✓'],
    ['Categories', $categoryCount, '◈'],
    ['Featured artworks', $featuredCount, '✧'],
];
$initial = function_exists('mb_substr') ? mb_substr($adminName, 0, 1) : substr($adminName, 0, 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MIRA | Admin Dashboard</title>
    <script>
        try {
            document.documentElement.dataset.theme = localStorage.getItem('mira-theme') === 'dark' ? 'dark' : 'light';
        } catch (e) {
            document.documentElement.dataset.theme = 'light';
        }
    </script>
    <link rel="stylesheet" href="admin.css">
    <script src="admin.js" defer></script>
</head>
<body class="admin-page">
<div class="admin-layout">
    <aside class="admin-sidebar">
        <a class="admin-brand" href="dashboard.php" aria-label="MIRA Admin Dashboard">
            <span class="brand-symbol" aria-hidden="true">✦</span>
            <span class="brand-copy">MIRA <small>ADMIN STUDIO</small></span>
        </a>

        <nav class="admin-nav" aria-label="Admin sections">
            <a class="active" href="dashboard.php" aria-current="page"><span>▦ &nbsp; Dashboard</span></a>
            <a href="artworks.php"><span>▤ &nbsp; Artworks</span></a>
            <span class="admin-nav-disabled" title="Coming in Phase 2"><span>◈ &nbsp; Categories</span><small>Soon</small></span>
            <span class="admin-nav-disabled"><span>▣ &nbsp; Exhibitions</span><small>Soon</small></span>
            <span class="admin-nav-disabled"><span>✧ &nbsp; Featured Art</span><small>Soon</small></span>
            <span class="admin-nav-disabled"><span>♙ &nbsp; Students</span><small>Soon</small></span>
            <span class="admin-nav-disabled"><span>▥ &nbsp; Reports</span><small>Soon</small></span>
        </nav>

        <div class="admin-user-box">
            <div class="admin-user">
                <span class="admin-avatar" aria-hidden="true"><?= h(strtoupper($initial)) ?></span>
                <div><strong><?= h($adminName) ?></strong><span>Administrator</span></div>
            </div>
            <form method="post" action="logout.php">
                <button class="logout-button" type="submit">↪ &nbsp; Log out</button>
            </form>
        </div>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar">
            <p class="admin-breadcrumb">MIRA / <strong>Dashboard</strong></p>
            <div class="admin-top-actions">
                <button id="themeToggle" class="theme-button admin-theme-toggle" type="button" aria-label="Toggle dark mode">☾ &nbsp; Dark mode</button>
                <span class="admin-avatar admin-avatar-small" aria-hidden="true"><?= h(strtoupper($initial)) ?></span>
            </div>
        </header>

        <section class="admin-hero">
            <p class="admin-hero-label">NSBM GREEN UNIVERSITY · ART &amp; CRAFT</p>
            <h1>Welcome back, <?= h($adminName) ?> <span aria-hidden="true">✦</span></h1>
            <p>Here's what's happening in your MIRA creative community.</p>
        </section>

        <section class="admin-stats" aria-label="Dashboard statistics">
            <?php foreach ($stats as [$label, $value, $symbol]): ?>
            <article class="stat-card">
                <div class="stat-card-top">
                    <span><?= h($label) ?></span>
                    <span class="stat-icon" aria-hidden="true"><?= h($symbol) ?></span>
                </div>
                <strong class="stat-value"><?= number_format($value) ?></strong>
            </article>
            <?php endforeach; ?>
        </section>

        <section class="admin-grid" aria-label="Activity and management">
            <article class="admin-panel">
                <div class="admin-panel-heading">
                    <div>
                        <h2>Recent artwork submissions</h2>
                        <p class="admin-panel-subtitle">Latest entries from the database</p>
                    </div>
                    <span class="live-tag">LIVE DATA</span>
                </div>
                <div class="admin-table-scroll">
                    <table class="admin-table">
                        <thead><tr><th>Artwork</th><th>Artist</th><th>Category</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php if ($recent && $recent->num_rows > 0): ?>
                            <?php while ($art = $recent->fetch_assoc()):
                                $status = strtolower((string) $art['status']);
                                $statusClass = in_array($status, ['pending', 'approved', 'rejected'], true) ? $status : 'other';
                            ?>
                            <tr>
                                <td><strong><?= h($art['title']) ?></strong><small>#<?= (int) $art['artwork_id'] ?></small></td>
                                <td><?= h($art['artist_name']) ?></td>
                                <td><?= h($art['category_name']) ?></td>
                                <td><span class="status-pill status-<?= h($statusClass) ?>"><?= h(ucfirst($status)) ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="empty-state">No artwork submissions yet. New uploads will appear here.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="admin-panel">
                <h2>Management workspace</h2>
                <p class="admin-panel-subtitle">Your admin dashboard is connected. Management tools will be added in the next phases.</p>
                <div class="admin-action-list">
                    <div class="admin-action-item"><span class="work-icon">▤</span><div><strong>Review submissions</strong><small><?= number_format($pendingArtworks) ?> pending approval</small></div><span class="coming-soon">Soon</span></div>
                    <div class="admin-action-item"><span class="work-icon">◈</span><div><strong>Categories</strong><small><?= number_format($categoryCount) ?> category records</small></div><span class="coming-soon">Soon</span></div>
                    <div class="admin-action-item"><span class="work-icon">▥</span><div><strong>Reports</strong><small>Coming in Phase 4</small></div><span class="coming-soon">Soon</span></div>
                </div>
                <div class="admin-notice">Phase 1: Dashboard and authentication. Management actions are not enabled yet.</div>
            </article>
        </section>
        <footer class="admin-footer">© <?= date('Y') ?> MIRA · NSBM Student Arts &amp; Handcraft Showcasing System</footer>
    </main>
</div>
</body>
</html>
