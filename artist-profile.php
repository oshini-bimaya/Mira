
<?php
require __DIR__ . '/mira-view-access.php';
require __DIR__ . '/gallery-common.php';

$currentUserId = (int) $_SESSION['user_id'];
$artistId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$artistId || $artistId < 1) {
    http_response_code(404);
    exit('Artist not found.');
}

// Redirect to own profile
if ($artistId === $currentUserId) {
    header('Location: profile.php');
    exit;
}

if (empty($_SESSION['follow_csrf'])) {
    $_SESSION['follow_csrf'] = bin2hex(random_bytes(32));
}

// Get artist details
$stmt = $conn->prepare(
    "SELECT USER_ID, FULL_NAME, ABOUT_ARTIST, PROFILE_PHOTO, COVER_PHOTO
     FROM users WHERE USER_ID = ? LIMIT 1"
);
$stmt->bind_param('i', $artistId);
$stmt->execute();
$artist = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$artist) {
    http_response_code(404);
    exit('Artist not found.');
}

// Handle Follow / Unfollow
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/mira-action-access.php';
    $token = (string) ($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['follow_csrf'], $token)) {
        http_response_code(403);
        exit('Invalid request.');
    }

    $stmt = $conn->prepare(
        "SELECT follow_id FROM follows
         WHERE follower_id = ? AND following_id = ?"
    );
    $stmt->bind_param('ii', $currentUserId, $artistId);
    $stmt->execute();
    $existingFollow = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($existingFollow) {
        $stmt = $conn->prepare(
            "DELETE FROM follows
             WHERE follower_id = ? AND following_id = ?"
        );
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO follows (follower_id, following_id)
             VALUES (?, ?)"
        );
    }

    $stmt->bind_param('ii', $currentUserId, $artistId);
    $stmt->execute();
    $stmt->close();

    header('Location: artist-profile.php?id=' . $artistId);
    exit;
}

// Approved works only: do not expose pending/rejected uploads on public profiles.
$stmt = $conn->prepare("SELECT a.artwork_id, a.title, a.created_at, c.category_name,
    (SELECT ai.image_path FROM artwork_images ai WHERE ai.artwork_id = a.artwork_id ORDER BY ai.image_id ASC LIMIT 1) AS image_path
    FROM artworks a
    LEFT JOIN categories c ON c.category_id = a.category_id
    WHERE a.user_id = ? AND LOWER(a.status) = 'approved'
    ORDER BY a.created_at DESC, a.artwork_id DESC LIMIT 60");
$stmt->bind_param('i', $artistId);
$stmt->execute();
$artistArtworks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Check following status
$stmt = $conn->prepare(
    "SELECT follow_id FROM follows
     WHERE follower_id = ? AND following_id = ?"
);
$stmt->bind_param('ii', $currentUserId, $artistId);
$stmt->execute();
$isFollowing = (bool) $stmt->get_result()->fetch_assoc();
$stmt->close();

// Followers count
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total FROM follows WHERE following_id = ?"
);
$stmt->bind_param('i', $artistId);
$stmt->execute();
$followersCount = (int) $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Following count
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total FROM follows WHERE follower_id = ?"
);
$stmt->bind_param('i', $artistId);
$stmt->execute();
$followingCount = (int) $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

function artistEsc($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= artistEsc($artist['FULL_NAME']) ?> | MIRA</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="artist-enhancements.css?v=1">
    <script src="theme.js" defer></script>
<link rel="stylesheet" href="mira-student-nav.css?v=2"><link rel="stylesheet" href="mira-design-system.css?v=1">
</head>

<body class="user-home-page mira-profile-page"><?php require __DIR__ . "/mira-student-nav.php"; ?>

<main class="mira-profile-container">

    

    <section class="profile-hero">

        <div class="profile-cover">
            <?php if (!empty($artist['COVER_PHOTO'])): ?>
                <img src="<?= artistEsc($artist['COVER_PHOTO']) ?>"
                     alt="Artist cover">
            <?php endif; ?>
            <span>MIRA · NSBM ART &amp; CRAFT CAFE</span>
        </div>

        <div class="profile-identity">

            <div class="profile-large-avatar">
                <?php if (!empty($artist['PROFILE_PHOTO'])): ?>
                    <img src="<?= artistEsc($artist['PROFILE_PHOTO']) ?>"
                         alt="Artist profile photo">
                <?php else: ?>
                    <?= artistEsc(strtoupper(substr($artist['FULL_NAME'], 0, 1))) ?>
                <?php endif; ?>
            </div>

            <div class="profile-identity-copy">
                <span class="profile-kicker">STUDENT CREATOR</span>

                <h2><?= artistEsc($artist['FULL_NAME']) ?></h2>

                <p><?= artistEsc($artist['ABOUT_ARTIST'] ?: 'Sharing creativity with MIRA.') ?></p>
            </div>

            <?php if (!$miraAdminPreview): ?><form method="POST">
                <input type="hidden" name="csrf_token"
                       value="<?= artistEsc($_SESSION['follow_csrf']) ?>">

                <button type="submit" class="profile-primary-btn">
                    <?= $isFollowing ? '✓ Following' : '+ Follow' ?>
                </button>
            </form><?php endif; ?>

        </div>
    </section>

    <section class="profile-stats">
        <div class="profile-stat"><strong><?= count($artistArtworks) ?></strong><span>Approved artworks</span></div>
        <div class="profile-stat">
            <strong><?= $followersCount ?></strong>
            <span>Followers</span>
        </div>

        <div class="profile-stat">
            <strong><?= $followingCount ?></strong>
            <span>Following</span>
        </div>
    </section>

    <section class="profile-panel">
        <div class="profile-section-heading"><div><span class="profile-kicker">ABOUT THE CREATOR</span><h2>Artist biography</h2></div></div>
        <p class="profile-bio"><?= nl2br(artistEsc($artist['ABOUT_ARTIST'] ?: 'This artist has not added a biography yet.')) ?></p>
    </section>

    <section class="profile-panel">
        <div class="profile-section-heading"><div><span class="profile-kicker">PORTFOLIO</span><h2>Approved artworks</h2></div><span><?= count($artistArtworks) ?> shown</span></div>
        <?php if (!$artistArtworks): ?>
            <div class="profile-empty"><h3>No published artworks yet</h3><p>Approved creations will appear here.</p></div>
        <?php else: ?>
            <div class="profile-art-grid">
                <?php foreach ($artistArtworks as $art): $imagePath = miraArtworkImage($art['image_path']); ?>
                    <a class="profile-art-card mira-portfolio-link" href="artwork-view.php?id=<?= (int)$art['artwork_id'] ?>">
                        <?php if ($imagePath !== ''): ?>
                            <img loading="lazy" src="<?= artistEsc($imagePath) ?>" alt="<?= artistEsc($art['title']) ?>">
                        <?php else: ?><div class="profile-image-placeholder">No image available</div><?php endif; ?>
                        <div class="profile-art-info"><span class="profile-kicker"><?= artistEsc($art['category_name'] ?? 'Artwork') ?></span><h3><?= artistEsc($art['title']) ?></h3><small><?= artistEsc(substr((string)$art['created_at'], 0, 10)) ?></small></div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

</main>

</body>
</html>
