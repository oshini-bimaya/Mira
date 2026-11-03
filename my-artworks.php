
<?php
session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

require __DIR__ . '/db.php';
require __DIR__ . '/artwork-common.php';

$uid = (int) $_SESSION['user_id'];

// Allow both STUDENT and ARTIST accounts
$stmt = $conn->prepare(
    "SELECT ROLE FROM users WHERE USER_ID = ? LIMIT 1"
);
$stmt->bind_param("i", $uid);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$role = strtoupper(trim($user['ROLE'] ?? ''));

if (!in_array($role, ['STUDENT', 'ARTIST'], true)) {
    http_response_code(403);
    exit('Student or artist access only.');
}

// Filter artworks by status
$filter = (string) ($_GET['filter'] ?? 'all');

if (!in_array($filter, ['all', 'pending', 'approved', 'rejected'], true)) {
    $filter = 'all';
}

// Artwork statistics
$stats = [
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0
];

$stmt = $conn->prepare(
    "SELECT LOWER(status) AS state, COUNT(*) AS total
     FROM artworks
     WHERE user_id = ?
     GROUP BY LOWER(status)"
);

$stmt->bind_param("i", $uid);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    if (isset($stats[$row['state']])) {
        $stats[$row['state']] = (int) $row['total'];
    }
}

$stmt->close();

// Retrieve artworks belonging to logged-in user
$sql = "
    SELECT
        a.artwork_id,
        a.title,
        a.status,
        a.rejection_reason,
        a.created_at,
        a.reviewed_at,
        c.category_name,
        (
            SELECT image_path
            FROM artwork_images
            WHERE artwork_id = a.artwork_id
            ORDER BY image_id
            LIMIT 1
        ) AS image_path
    FROM artworks a
    JOIN categories c
        ON c.category_id = a.category_id
    WHERE a.user_id = ?
";

if ($filter !== 'all') {
    $sql .= " AND LOWER(a.status) = ?";
}

$sql .= " ORDER BY a.created_at DESC, a.artwork_id DESC";

$stmt = $conn->prepare($sql);

if ($filter === 'all') {
    $stmt->bind_param("i", $uid);
} else {
    $stmt->bind_param("is", $uid, $filter);
}

$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Success and error messages
$msg = $_GET['message'] ?? '';

$messages = [
    'updated' => 'Artwork updated.',
    'resubmitted' => 'Your artwork was resubmitted for review.',
    'error' => 'Could not complete the action. Please try again.'
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Submission Tracking | MIRA</title>

    <link rel="stylesheet" href="artwork-workflow.css">
    <link rel="stylesheet" href="submission-tracking.css">
    <link rel="stylesheet" href="mira-student-nav.css?v=2">

    <script src="theme.js" defer></script>
<link rel="stylesheet" href="mira-design-system.css?v=1">
</head>

<body class="mira-workflow">

<?php require __DIR__ . '/mira-student-nav.php'; ?>

<main class="workflow-wrap">

    <!-- Page heading -->
    <div class="workflow-heading">
        <p class="overline">MY MIRA STUDIO</p>
        <h1>Submission Tracking</h1>
        <p>Manage your artworks from submission to approval.</p>
    </div>

    <!-- Messages -->
    <?php if (isset($messages[$msg])): ?>
        <p class="notice <?= $msg === 'error' ? 'error' : 'success' ?>"
           role="status">
            <?= artworkEscape($messages[$msg]) ?>
        </p>
    <?php endif; ?>

    <!-- Artwork statistics -->
    <div class="tracking-stats">

        <?php foreach ($stats as $key => $count): ?>

            <a class="tracking-stat"
               href="?filter=<?= $key ?>">

                <span>
                    <?= artworkEscape(ucfirst($key)) ?>
                </span>

                <strong><?= $count ?></strong>

            </a>

        <?php endforeach; ?>

    </div>

    <!-- Filters -->
    <div class="tracking-toolbar">

        <div class="filter-pills">

            <?php
            $filters = [
                'all' => 'All',
                'pending' => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected'
            ];
            ?>

            <?php foreach ($filters as $key => $text): ?>

                <a
                    class="filter-pill <?= $filter === $key ? 'selected' : '' ?>"
                    href="?filter=<?= $key ?>"
                >
                    <?= $text ?>
                </a>

            <?php endforeach; ?>

        </div>

        <a class="primary-btn" href="upload-artwork.php">
            + Submit Artwork
        </a>

    </div>

    <!-- Artwork list -->
    <section class="art-list">

        <?php if (!$rows): ?>

            <div class="form-card">
                No <?= $filter === 'all' ? '' : artworkEscape($filter) . ' ' ?>
                submissions found.
            </div>

        <?php endif; ?>

        <?php foreach ($rows as $artwork): ?>

            <?php
            $state = strtolower($artwork['status']);
            $artworkId = (int) $artwork['artwork_id'];
            ?>

            <article class="art-row tracking-row">

                <!-- Artwork image -->
                <div class="art-thumb">

                    <?php if ($artwork['image_path']): ?>

                        <img
                            src="<?= artworkEscape($artwork['image_path']) ?>"
                            alt="<?= artworkEscape($artwork['title']) ?>"
                        >

                    <?php else: ?>

                        ✦

                    <?php endif; ?>

                </div>

                <!-- Artwork details -->
                <div class="art-meta">

                    <h2>
                        <?= artworkEscape($artwork['title']) ?>
                    </h2>

                    <p>
                        <?= artworkEscape($artwork['category_name']) ?>
                        · Submitted
                        <?= artworkEscape(
                            date('M j, Y', strtotime($artwork['created_at']))
                        ) ?>
                    </p>

                    <!-- Submission status -->
                    <span class="state state-<?= in_array(
                        $state,
                        ['pending', 'approved', 'rejected'],
                        true
                    ) ? $state : 'pending' ?>">

                        <?= artworkEscape(ucfirst($state)) ?>

                    </span>

                    <!-- Rejection reason -->
                    <?php if (
                        $state === 'rejected' &&
                        $artwork['rejection_reason']
                    ): ?>

                        <p class="tracking-reason">
                            <strong>Admin Feedback:</strong>
                            <?= artworkEscape($artwork['rejection_reason']) ?>
                        </p>

                    <?php endif; ?>

                    <!-- Review date -->
                    <?php if ($artwork['reviewed_at']): ?>

                        <p>
                            Last reviewed:
                            <?= artworkEscape(
                                date(
                                    'M j, Y g:i A',
                                    strtotime($artwork['reviewed_at'])
                                )
                            ) ?>
                        </p>

                    <?php endif; ?>

                    <!-- Artwork actions -->
                    <div class="tracking-actions">

                        <!-- Edit pending or rejected artwork -->
                        <?php if (in_array(
                            $state,
                            ['pending', 'rejected'],
                            true
                        )): ?>

                            <a
                                class="outline-btn"
                                href="edit-artwork.php?id=<?= $artworkId ?>"
                            >
                                Edit Artwork
                            </a>

                        <?php endif; ?>

                        <!-- Resubmit rejected artwork -->
                        <?php if ($state === 'rejected'): ?>

                            <form
                                method="POST"
                                action="resubmit-artwork.php"
                            >

                                <input
                                    type="hidden"
                                    name="csrf"
                                    value="<?= artworkEscape(artworkCsrf()) ?>"
                                >

                                <input
                                    type="hidden"
                                    name="artwork_id"
                                    value="<?= $artworkId ?>"
                                >

                                <button
                                    class="primary-btn"
                                    type="submit"
                                >
                                    Resubmit for Approval
                                </button>

                            </form>

                        <?php endif; ?>

                        <!-- View approved artwork -->
                        <?php if ($state === 'approved'): ?>

                            <a
                                class="outline-btn"
                                href="artwork-view.php?id=<?= $artworkId ?>"
                            >
                                View Artwork
                            </a>

                        <?php endif; ?>

                        <!-- Review history -->
                        <a
                            class="tracking-history-link"
                            href="submission-history.php?id=<?= $artworkId ?>"
                        >
                            Review History →
                        </a>

                    </div>

                </div>

            </article>

        <?php endforeach; ?>

    </section>

</main>

</body>
</html>

