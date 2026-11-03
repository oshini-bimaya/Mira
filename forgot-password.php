<?php
// MIRA - Forgot Password Backend

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'message' => 'Method not allowed.'
    ]);
    exit;
}

require __DIR__ . '/db.php';
require __DIR__ . '/brevo-config.php';

function respond($message) {
    echo json_encode(['message' => $message]);
    exit;
}

$genericMessage =
    'If this email is registered, a password reset link will be sent shortly.';

$email = trim($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
    http_response_code(400);
    respond('Please enter a valid email address.');
}

// Basic request rate limit per IP address
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateFile = sys_get_temp_dir() . '/mira_reset_' . hash('sha256', $ip);
$now = time();

$attempts = [];

if (is_file($rateFile)) {
    $attempts = json_decode(
        file_get_contents($rateFile),
        true
    ) ?: [];
}

$attempts = array_values(array_filter(
    $attempts,
    fn($time) => $time > $now - 3600
));

if (count($attempts) >= 5) {
    respond($genericMessage);
}

$attempts[] = $now;
file_put_contents($rateFile, json_encode($attempts), LOCK_EX);

// Find account
$stmt = $conn->prepare(
    'SELECT USER_ID FROM users WHERE EMAIL = ? LIMIT 1'
);

$stmt->bind_param('s', $email);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Same response for registered and unregistered emails
if (!$user) {
    respond($genericMessage);
}

$userId = (int)$user['USER_ID'];

// Prevent frequent reset emails for the same user
$stmt = $conn->prepare(
    'SELECT id FROM password_resets
     WHERE user_id = ?
     AND created_at > DATE_SUB(NOW(), INTERVAL 2 MINUTE)
     LIMIT 1'
);

$stmt->bind_param('i', $userId);
$stmt->execute();

$recentRequest = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($recentRequest) {
    respond($genericMessage);
}

// Generate secure token
$token = bin2hex(random_bytes(32));
$tokenHash = hash('sha256', $token);

// Store hashed token, valid for 30 minutes
$stmt = $conn->prepare(
    'INSERT INTO password_resets
     (user_id, token_hash, expires_at)
     VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))'
);

$stmt->bind_param('is', $userId, $tokenHash);

if (!$stmt->execute()) {
    error_log('MIRA password reset: Failed to store token.');
    respond($genericMessage);
}

$resetId = $conn->insert_id;
$stmt->close();

// Create reset URL
$resetLink = rtrim(MIRA_BASE_URL, '/') .
    '/reset-password.php?token=' .
    urlencode($token);

// Email content
$emailHtml = '
<div style="font-family:Arial,sans-serif;max-width:560px;margin:auto;padding:30px;">
    <h1 style="color:#7654bb;">MIRA</h1>
    <h2>Reset Your Password</h2>
    <p>We received a request to reset your MIRA account password.</p>
    <p>Click the button below to create a new password.</p>
    <p style="margin:30px 0;">
        <a href="' . htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8') . '"
           style="background:#7654bb;color:white;padding:14px 24px;
                  text-decoration:none;border-radius:8px;">
            Reset Password
        </a>
    </p>
    <p>This link expires in 30 minutes and can only be used once.</p>
    <p>If you did not request this, you can ignore this email.</p>
    <hr>
    <p style="color:#888;font-size:12px;">MIRA | NSBM ArtCraft</p>
</div>';

// Send email through Brevo
$payload = [
    'sender' => [
        'name' => BREVO_SENDER_NAME,
        'email' => BREVO_SENDER_EMAIL
    ],
    'to' => [
        ['email' => $email]
    ],
    'subject' => 'Reset Your MIRA Password',
    'htmlContent' => $emailHtml
];

$ch = curl_init('https://api.brevo.com/v3/smtp/email');

curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 20,
    CURLOPT_HTTPHEADER => [
        'accept: application/json',
        'content-type: application/json',
        'api-key: ' . BREVO_API_KEY
    ],
    CURLOPT_POSTFIELDS => json_encode($payload)
]);

$result = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($result === false || $status < 200 || $status >= 300) {
    error_log(
        'MIRA Brevo email failed. HTTP: ' .
        $status . ' Error: ' . $curlError
    );

    // Remove token if email API rejected the request
    $stmt = $conn->prepare(
        'DELETE FROM password_resets WHERE id = ?'
    );
    $stmt->bind_param('i', $resetId);
    $stmt->execute();
    $stmt->close();
}

respond($genericMessage);