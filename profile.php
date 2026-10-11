<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}
if (strtoupper(trim((string)($_SESSION['role'] ?? ''))) === 'ADMIN') {
    header('Location: admin/dashboard.php');
    exit;
}
require __DIR__ . '/db.php';
require __DIR__ . '/gallery-common.php';

$userId = (int)$_SESSION['user_id'];
$userName = (string)($_SESSION['full_name'] ?? 'Artist');

// Read the name from the database rather than relying only on a potentially old session.
$stmt = $conn->prepare('SELECT FULL_NAME FROM users WHERE USER_ID = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$user) {
    http_response_code(404);
    exit('User account not found.');
}
$userName = $user['FULL_NAME'];

$stmt = $conn->prepare("SELECT COUNT(*) AS total,
    COALESCE(SUM(LOWER(status) = 'approved'), 0) AS approved,
    COALESCE(SUM(LOWER(status) = 'pending'), 0) AS pending
    FROM artworks WHERE user_id = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Include artworks of all statuses on the owner's own profile.
$stmt = $conn->prepare("SELECT a.artwork_id, a.title, a.status, a.created_at,
    (SELECT ai.image_path FROM artwork_images ai
     WHERE ai.artwork_id = a.artwork_id
     ORDER BY ai.image_id ASC LIMIT 1) AS image_path
    FROM artworks a WHERE a.user_id = ?
    ORDER BY a.created_at DESC, a.artwork_id DESC LIMIT 12");
$stmt->bind_param('i', $userId);
$stmt->execute();
$artworks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

function profileEsc($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
$initial = strtoupper(substr(trim($userName), 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIRA | My Profile</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="profile.css">
    <script src="theme.js" defer></script>
</head>
<body class="user-home-page mira-profile-page">
<header class="user-navbar">
    <a href="user-home.php" class="user-nav-logo"><img src="./Assets/Logo.jpeg" alt="MIRA Logo"></a>
    <nav class="user-nav-links" aria-label="Main navigation">
        <a href="user-home.php">Home</a>
        <a href="gallery.php">Categories</a>
        <a href="gallery.php">Gallery</a>
        <a href="gallery.php">Artists</a>
        <a href="gallery.php">Exhibitions</a>
    </nav>
    <div class="user-nav-actions">
        <a href="upload-artwork.php" class="upload-nav-button">+ Upload</a>
        <button class="notification-button" type="button" aria-label="Notifications"><span>♡</span></button>
        <div class="profile-menu">
            <button class="profile-button" id="profileButton" type="button" aria-expanded="false" aria-controls="profileDropdown">
                <span class="profile-avatar"><?= profileEsc($initial) ?></span>
                <span class="profile-name"><?= profileEsc($userName) ?></span>
                <span class="profile-arrow">▾</span>
            </button>
            <div class="profile-dropdown" id="profileDropdown">
                <a href="profile.php">My Profile</a>
                <a href="my-artworks.php">My Artworks</a>
                <a href="my-artworks.php">Submission History</a>
                <a href="my-events.php">My Exhibitions</a>
                <a href="following.php">Following</a>
                <div class="dropdown-line"></div>
                <a href="logout.php" class="logout-link">Log Out</a>
            </div>
        </div>
    </div>
</header>

<main class="mira-profile-container">
    <div class="profile-eyebrow">THE CREATIVE COMMUNITY</div>
    <h1>My Profile</h1>
    <p class="profile-intro">Your little corner of MIRA — where creativity comes to life.</p>

    <section class="profile-hero" aria-label="Artist profile">
        <div class="profile-cover"><span>MIRA · NSBM ART &amp; CRAFT CAFE</span></div>
        <div class="profile-identity">
            <div class="profile-large-avatar" aria-label="Profile initial"><?= profileEsc($initial) ?></div>
            <div class="profile-identity-copy">
                <span class="profile-kicker">STUDENT CREATOR</span>
                <h2><?= profileEsc($userName) ?></h2>
                <p>Sharing imagination, one creation at a time.</p>
            </div>
            <a class="profile-primary-btn" href="my-artworks.php">Manage My Artworks ↗</a>
        </div>
    </section>

    <section class="profile-stats" aria-label="Artwork statistics">
        <div class="profile-stat"><strong><?= (int)$stats['total'] ?></strong><span>Total Artworks</span></div>
        <div class="profile-stat"><strong><?= (int)$stats['approved'] ?></strong><span>Approved</span></div>
        <div class="profile-stat"><strong><?= (int)$stats['pending'] ?></strong><span>Pending Review</span></div>
    </section>

    <section class="profile-panel">
        <div class="profile-section-heading"><div><span class="profile-kicker">YOUR CREATIVE JOURNEY</span><h2>About the artist</h2></div></div>
        <p>Welcome to your MIRA profile! This is where your artistic journey is showcased to the NSBM creative community.</p>
        <p class="profile-note">To add an editable biography, profile photo, or social links, the corresponding database fields and update handler will need to be added.</p>
    </section>

    <section class="profile-panel">
        <div class="profile-section-heading">
            <div><span class="profile-kicker">MADE BY YOU</span><h2>My creations</h2></div>
            <a href="my-artworks.php" class="profile-text-link">View all artworks →</a>
        </div>
        <?php if (!$artworks): ?>
            <div class="profile-empty"><div class="profile-empty-icon">✳</div><h3>Your canvas is waiting</h3><p>Upload your first creation and begin sharing your art with MIRA.</p><a class="profile-primary-btn" href="upload-artwork.php">+ Upload Artwork</a></div>
        <?php else: ?>
            <div class="profile-art-grid">
            <?php foreach ($artworks as $art):
                $imagePath = miraArtworkImage($art['image_path']);
                $status = strtolower(trim((string)$art['status']));
                $statusClass = in_array($status, ['approved', 'pending', 'rejected'], true) ? $status : 'other';
            ?>
                <article class="profile-art-card">
                    <?php if ($imagePath !== ''): ?>
                        <img loading="lazy" src="<?= profileEsc($imagePath) ?>" alt="<?= profileEsc($art['title']) ?>">
                    <?php else: ?>
                        <div class="profile-image-placeholder">No image available</div>
                    <?php endif; ?>
                    <div class="profile-art-info"><span class="profile-status <?= profileEsc($statusClass) ?>"><?= profileEsc(ucfirst($status ?: 'Unknown')) ?></span><h3><?= profileEsc($art['title']) ?></h3><small><?= profileEsc(substr((string)$art['created_at'], 0, 10)) ?></small></div>
                </article>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
<script>
const profileButton = document.getElementById('profileButton');
const profileDropdown = document.getElementById('profileDropdown');
profileButton.addEventListener('click', () => {
    const open = profileDropdown.classList.toggle('show-profile-menu');
    profileButton.setAttribute('aria-expanded', String(open));
});
document.addEventListener('click', (event) => {
    if (!event.target.closest('.profile-menu')) {
        profileDropdown.classList.remove('show-profile-menu');
        profileButton.setAttribute('aria-expanded', 'false');
    }
});
</script>
</body>
</html>
