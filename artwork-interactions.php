
<?php
session_start();

if (empty($_SESSION['artwork_csrf'])) {
    $_SESSION['artwork_csrf'] = bin2hex(random_bytes(32));
}

if (empty($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

require __DIR__ . '/db.php';
require __DIR__ . '/gallery-common.php';

$userId = (int) $_SESSION['user_id'];
$artworkId = filter_input(INPUT_POST, 'artwork_id', FILTER_VALIDATE_INT);
$action = $_POST['action'] ?? '';

if (!$artworkId || $artworkId < 1) {
    http_response_code(400);
    exit('Invalid artwork.');
}

// Verify that the artwork is approved
$artwork = miraApprovedArtworks($conn, '', $artworkId, 1)[0] ?? null;

if (!$artwork) {
    http_response_code(404);
    exit('Artwork not found.');
}

// Verify CSRF token
if (
    empty($_SESSION['artwork_csrf']) ||
    !hash_equals($_SESSION['artwork_csrf'], $_POST['csrf_token'] ?? '')
) {
    http_response_code(403);
    exit('Invalid request.');
}

switch ($action) {
   
case 'like':
    $stmt = $conn->prepare(
        "SELECT like_id FROM artwork_likes
         WHERE artwork_id = ? AND user_id = ?"
    );
    $stmt->bind_param('ii', $artworkId, $userId);
    $stmt->execute();
    $liked = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    if ($liked) {
        $stmt = $conn->prepare(
            "DELETE FROM artwork_likes
             WHERE artwork_id = ? AND user_id = ?"
        );
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO artwork_likes (artwork_id, user_id)
             VALUES (?, ?)"
        );
    }

    $stmt->bind_param('ii', $artworkId, $userId);
    $stmt->execute();
    $stmt->close();

    header('Location: artwork-view.php?id=' . $artworkId);
    exit;


    case 'comment':
        $comment = trim($_POST['comment_text'] ?? '');

        if ($comment === '' || strlen($comment) > 4000) {
            http_response_code(400);
            exit('Comment must be between 1 and 1000 characters.');
        }

        $stmt = $conn->prepare(
            "INSERT INTO comments (artwork_id, user_id, comment_text)
             VALUES (?, ?, ?)"
        );
        $stmt->bind_param('iis', $artworkId, $userId, $comment);
        $stmt->execute();
        $stmt->close();
        break;

            case 'delete_comment':

        $commentId = filter_input(
            INPUT_POST,
            'comment_id',
            FILTER_VALIDATE_INT
        );

        if (!$commentId || $commentId < 1) {
            http_response_code(400);
            exit('Invalid comment.');
        }

        // Delete only the logged-in user's own comment
        $stmt = $conn->prepare(
            "DELETE FROM comments
             WHERE comment_id = ?
             AND artwork_id = ?
             AND user_id = ?"
        );

        $stmt->bind_param(
            'iii',
            $commentId,
            $artworkId,
            $userId
        );

        $stmt->execute();
        $stmt->close();

        break;

    case 'rating':
        $rating = filter_input(INPUT_POST, 'rating_value', FILTER_VALIDATE_INT);

        if (!$rating || $rating < 1 || $rating > 5) {
            http_response_code(400);
            exit('Invalid rating.');
        }

        $stmt = $conn->prepare(
            "INSERT INTO ratings (artwork_id, user_id, rating_value)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE rating_value = VALUES(rating_value)"
        );
        $stmt->bind_param('iii', $artworkId, $userId, $rating);
        $stmt->execute();
        $stmt->close();
        break;

    default:
        http_response_code(400);
        exit('Invalid action.');
}

header('Location: artwork-view.php?id=' . $artworkId);
exit;
