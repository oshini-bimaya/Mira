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
$profileMessage = '';
$profileMessageType = 'success';
if (empty($_SESSION['profile_csrf'])) {
    $_SESSION['profile_csrf'] = bin2hex(random_bytes(32));
}

function profileEsc($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function profileExcerpt(string $value, int $length): string {
    return function_exists('mb_substr') ? mb_substr($value, 0, $length) : substr($value, 0, $length);
}

/** Validate and store one profile image. Returns a relative path or null if no new image was selected. */
function saveProfileImage(string $field, int $userId): ?string {
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $upload = $_FILES[$field];
    if ($upload['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($upload['tmp_name'])) {
        throw new RuntimeException('The selected image could not be uploaded. Please try again.');
    }
    if ((int)$upload['size'] < 1 || (int)$upload['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Each image must be smaller than 5 MB.');
    }

    $imageInfo = @getimagesize($upload['tmp_name']);
    $mime = $imageInfo['mime'] ?? '';
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!$imageInfo || !isset($extensions[$mime])) {
        throw new RuntimeException('Use a valid JPG, PNG, or WebP image.');
    }

    $relativeDirectory = 'uploads/profiles';
    $absoluteDirectory = __DIR__ . '/' . $relativeDirectory;
    if (!is_dir($absoluteDirectory) && !mkdir($absoluteDirectory, 0755, true) && !is_dir($absoluteDirectory)) {
        throw new RuntimeException('The profile image folder is not writable on the server.');
    }
    $fileName = 'user_' . $userId . '_' . bin2hex(random_bytes(12)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($upload['tmp_name'], $absoluteDirectory . '/' . $fileName)) {
        throw new RuntimeException('The image could not be saved. Check that uploads/profiles is writable.');
    }
    return $relativeDirectory . '/' . $fileName;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_profile') {
    try {
        $csrf = (string)($_POST['csrf_token'] ?? '');
        if (!hash_equals((string)$_SESSION['profile_csrf'], $csrf)) {
            throw new RuntimeException('Your session check expired. Reload the page and try again.');
        }
        $aboutArtist = trim((string)($_POST['about_artist'] ?? ''));
        if (strlen($aboutArtist) > 4000) {
            throw new RuntimeException('The artist bio must be 4,000 characters or fewer.');
        }

        // Read existing paths so an omitted image keeps the current photo.
        $stmt = $conn->prepare('SELECT PROFILE_PHOTO, COVER_PHOTO FROM users WHERE USER_ID = ? LIMIT 1');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $oldProfile = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$oldProfile) {
            throw new RuntimeException('User account not found.');
        }

        $newFiles = [];
        try {
            $newFiles['profile'] = saveProfileImage('profile_photo', $userId);
            $newFiles['cover'] = saveProfileImage('cover_photo', $userId);
        } catch (Throwable $uploadError) {
            foreach ($newFiles as $newPath) {
                if ($newPath !== null) {
                    @unlink(__DIR__ . '/' . $newPath);
                }
            }
            throw $uploadError;
        }

        $profilePhoto = $newFiles['profile'] ?? $oldProfile['PROFILE_PHOTO'];
        $coverPhoto = $newFiles['cover'] ?? $oldProfile['COVER_PHOTO'];
        $stmt = $conn->prepare('UPDATE users SET ABOUT_ARTIST = ?, PROFILE_PHOTO = ?, COVER_PHOTO = ? WHERE USER_ID = ?');
        $stmt->bind_param('sssi', $aboutArtist, $profilePhoto, $coverPhoto, $userId);
        if (!$stmt->execute()) {
            $stmt->close();
            foreach ($newFiles as $newPath) {
                if ($newPath !== null) {
                    @unlink(__DIR__ . '/' . $newPath);
                }
            }
            throw new RuntimeException('Your profile could not be saved. Please try again.');
        }
        $stmt->close();

        // Remove only old images stored in this profile's own upload folder.
        foreach ([[$oldProfile['PROFILE_PHOTO'], $newFiles['profile']], [$oldProfile['COVER_PHOTO'], $newFiles['cover']]] as [$oldPath, $newPath]) {
            if ($newPath !== null && is_string($oldPath) && preg_match('~^uploads/profiles/[A-Za-z0-9_.-]+$~', $oldPath)) {
                @unlink(__DIR__ . '/' . $oldPath);
            }
        }
        $_SESSION['profile_csrf'] = bin2hex(random_bytes(32));
        $_SESSION['profile_flash'] = ['type' => 'success', 'message' => 'Your profile has been updated.'];
        header('Location: profile.php');
        exit;
    } catch (Throwable $error) {
        $profileMessage = $error->getMessage();
        $profileMessageType = 'error';
    }
}

if (isset($_SESSION['profile_flash'])) {
    $profileMessage = (string)$_SESSION['profile_flash']['message'];
    $profileMessageType = (string)$_SESSION['profile_flash']['type'];
    unset($_SESSION['profile_flash']);
}

$stmt = $conn->prepare('SELECT FULL_NAME, ABOUT_ARTIST, PROFILE_PHOTO, COVER_PHOTO FROM users WHERE USER_ID = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$user) {
    http_response_code(404);
    exit('User account not found.');
}
$userName = (string)$user['FULL_NAME'];
$aboutArtist = (string)($user['ABOUT_ARTIST'] ?? '');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $profileMessageType === 'error') {
    $aboutArtist = trim((string)($_POST['about_artist'] ?? $aboutArtist));
}
$profilePhoto = (string)($user['PROFILE_PHOTO'] ?? '');
$coverPhoto = (string)($user['COVER_PHOTO'] ?? '');
$initial = strtoupper(substr(trim($userName), 0, 1));

$stmt = $conn->prepare("SELECT COUNT(*) AS total,
    COALESCE(SUM(LOWER(status) = 'approved'), 0) AS approved,
    COALESCE(SUM(LOWER(status) = 'pending'), 0) AS pending
    FROM artworks WHERE user_id = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
$stmt->close();
// Get followers count
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM follows
     WHERE following_id = ?"
);
$stmt->bind_param("i", $userId);
$stmt->execute();
$followersCount = (int)$stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Get following count
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM follows
     WHERE follower_id = ?"
);
$stmt->bind_param("i", $userId);
$stmt->execute();
$followingCount = (int)$stmt->get_result()->fetch_assoc()['total'];
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIRA | My Profile</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="artist-enhancements.css?v=1">
    <link rel="stylesheet" href="profile.css?v=<?= (int) filemtime(__DIR__ . '/profile.css') ?>">
    <script src="theme.js" defer></script>
<link rel="stylesheet" href="mira-student-nav.css?v=2"></head>
<body class="user-home-page mira-profile-page"><?php require __DIR__ . "/mira-student-nav.php"; ?>


<main class="mira-profile-container">
    <div class="profile-eyebrow">THE CREATIVE COMMUNITY</div>
    <h1>My Profile</h1>
    <p class="profile-intro">Your little corner of MIRA — where creativity comes to life.</p>
    <?php if ($profileMessage !== ''): ?>
        <div class="profile-alert <?= profileEsc($profileMessageType) ?>" role="status"><?= profileEsc($profileMessage) ?></div>
    <?php endif; ?>

    <section class="profile-hero" aria-label="Artist profile">
        <div class="profile-cover">
            <?php if ($coverPhoto !== ''): ?><img src="<?= profileEsc($coverPhoto) ?>" alt="Artist cover photo"><?php endif; ?>
            <span>MIRA · NSBM ART &amp; CRAFT CAFE</span>
        </div>
        <div class="profile-identity">
            <div class="profile-large-avatar" aria-label="Profile photo">
                <?php if ($profilePhoto !== ''): ?><img src="<?= profileEsc($profilePhoto) ?>" alt="Profile photo of <?= profileEsc($userName) ?>"><?php else: ?><?= profileEsc($initial) ?><?php endif; ?>
            </div>
            <div class="profile-identity-copy">
                <span class="profile-kicker">STUDENT CREATOR</span>
                <h2><?= profileEsc($userName) ?></h2>
                <p><?= $aboutArtist !== '' ? profileEsc(profileExcerpt($aboutArtist, 150)) : 'Sharing imagination, one creation at a time.' ?></p>
            </div>
            <a class="profile-primary-btn" href="my-artworks.php">Manage My Artworks ↗</a>
        </div>
    </section>

    <section class="profile-stats" aria-label="Artwork statistics">
        <div class="profile-stat"><strong><?= (int)$stats['total'] ?></strong><span>Total Artworks</span></div>
        <div class="profile-stat"><strong><?= (int)$stats['approved'] ?></strong><span>Approved</span></div>
        <div class="profile-stat"><strong><?= (int)$stats['pending'] ?></strong><span>Pending Review</span></div>

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
        <div class="profile-section-heading"><div><span class="profile-kicker">YOUR CREATIVE JOURNEY</span><h2>About the artist</h2></div></div>
        <?php if ($aboutArtist !== ''): ?>
            <p class="profile-bio"><?= nl2br(profileEsc($aboutArtist)) ?></p>
        <?php else: ?>
            <p class="profile-muted-copy">Tell the MIRA community what inspires your work, what you like to create, and what you are learning.</p>
        <?php endif; ?>
        <details class="profile-edit-details" <?= $profileMessageType === 'error' ? 'open' : '' ?>>
            <summary><?= $aboutArtist !== '' || $profilePhoto !== '' || $coverPhoto !== '' ? 'Edit my profile' : 'Add your bio and photos' ?></summary>
            <form class="profile-edit-form" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_profile">
                <input type="hidden" name="csrf_token" value="<?= profileEsc($_SESSION['profile_csrf']) ?>">
                <label for="about_artist">About the artist <span>Share your story, style, or inspirations.</span></label>
                <textarea id="about_artist" name="about_artist" rows="5" maxlength="4000" placeholder="I love creating..." aria-describedby="bio-help"><?= profileEsc($aboutArtist) ?></textarea>
                <small id="bio-help" class="profile-field-help">Up to 4,000 characters. Keep it personal and focused on your art.</small>
                <div class="profile-upload-grid">
                    <label class="profile-file-field" for="profile_photo">Profile photo <span>Square JPG, PNG, or WebP. Maximum 5 MB.</span>
                        <input id="profile_photo" name="profile_photo" type="file" accept="image/jpeg,image/png,image/webp">
                    </label>
                    <label class="profile-file-field" for="cover_photo">Cover photo <span>Wide artwork or a photo of your creative space. Maximum 5 MB.</span>
                        <input id="cover_photo" name="cover_photo" type="file" accept="image/jpeg,image/png,image/webp">
                    </label>
                </div>
                <button class="profile-primary-btn profile-save-btn" type="submit">Save profile</button>
            </form>
        </details>
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
