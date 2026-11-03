<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

if (empty($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

require __DIR__ . '/db.php';
require __DIR__ . '/artwork-common.php';

$uid = (int) $_SESSION['user_id'];

if (!artworkIsStudent($conn, $uid)) {
    http_response_code(403);
    exit('Artist account required.');
}

if (!artworkValidCsrf()) {
    http_response_code(403);
    exit('Invalid form token. Refresh and try again.');
}

function uploadFailed() {
    header('Location: upload-artwork.php?status=error');
    exit;
}

// Get form details
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');

$category = filter_var(
    $_POST['category_id'] ?? null,
    FILTER_VALIDATE_INT
);

// Validate details
if (
    $title === '' ||
    mb_strlen($title) > 200 ||
    $description === '' ||
    mb_strlen($description) > 3000 ||
    !$category
) {
    uploadFailed();
}

// Check category
$stmt = $conn->prepare(
    "SELECT category_id
     FROM categories
     WHERE category_id = ? AND is_active = 1"
);

$stmt->bind_param('i', $category);
$stmt->execute();

$validCategory = $stmt->get_result()->num_rows > 0;
$stmt->close();

if (!$validCategory) {
    uploadFailed();
}

// Check uploaded image
if (
    !isset($_FILES['artwork_image']) ||
    $_FILES['artwork_image']['error'] !== UPLOAD_ERR_OK
) {
    uploadFailed();
}

$file = $_FILES['artwork_image'];

if (
    $file['size'] <= 0 ||
    $file['size'] > 5 * 1024 * 1024 ||
    !is_uploaded_file($file['tmp_name'])
) {
    uploadFailed();
}

// Validate image type
$imageInfo = @getimagesize($file['tmp_name']);

$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp'
];

$mime = $imageInfo['mime'] ?? '';

if (
    !isset($allowedTypes[$mime]) ||
    $imageInfo[0] < 1 ||
    $imageInfo[1] < 1 ||
    $imageInfo[0] > 10000 ||
    $imageInfo[1] > 10000
) {
    uploadFailed();
}

// Create upload directory
$uploadDir = __DIR__ . '/uploads/artworks/';

if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        uploadFailed();
    }
}

// Generate unique filename
$filename = bin2hex(random_bytes(20))
          . '.' . $allowedTypes[$mime];

$imagePath = 'uploads/artworks/' . $filename;
$destination = __DIR__ . '/' . $imagePath;

// Upload image
if (!move_uploaded_file($file['tmp_name'], $destination)) {
    uploadFailed();
}

// Save artwork to database
try {
    $conn->begin_transaction();

    $stmt = $conn->prepare(
        "INSERT INTO artworks
         (user_id, category_id, title, description, status)
         VALUES (?, ?, ?, ?, 'pending')"
    );

    $stmt->bind_param(
        'iiss',
        $uid,
        $category,
        $title,
        $description
    );

    $stmt->execute();

    $artworkId = $conn->insert_id;
    $stmt->close();

    // Save image path
    $stmt = $conn->prepare(
        "INSERT INTO artwork_images
         (artwork_id, image_path)
         VALUES (?, ?)"
    );

    $stmt->bind_param(
        'is',
        $artworkId,
        $imagePath
    );

    $stmt->execute();
    $stmt->close();

    $conn->commit();

    header('Location: upload-artwork.php?status=success');
    exit;

} catch (Throwable $e) {

    $conn->rollback();

    // Remove uploaded image if database operation fails
    if (file_exists($destination)) {
        unlink($destination);
    }

    error_log('MIRA artwork upload: ' . $e->getMessage());

    uploadFailed();
}