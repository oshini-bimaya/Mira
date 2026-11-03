<?php
session_start();
require __DIR__ . '/db.php';

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$validToken = false;
$userId = null;
$error = '';
$success = false;

if (preg_match('/^[a-f0-9]{64}$/', $token)) {
    $tokenHash = hash('sha256', $token);

    $stmt = $conn->prepare(
        "SELECT user_id
         FROM password_resets
         WHERE token_hash = ?
         AND expires_at > NOW()
         LIMIT 1"
    );

    $stmt->bind_param('s', $tokenHash);
    $stmt->execute();
    $reset = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($reset) {
        $validToken = true;
        $userId = (int)$reset['user_id'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!$validToken) {
        $error = 'This reset link is invalid or has expired.';
    } elseif (
        !isset($_POST['csrf_token']) ||
        !hash_equals(
            $_SESSION['reset_csrf'] ?? '',
            $_POST['csrf_token']
        )
    ) {
        $error = 'Invalid request. Please refresh the page.';
    } else {
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (strlen($password) < 8) {
            $error = 'Password must contain at least 8 characters.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } else {
            try {
                $conn->begin_transaction();

                // Lock token to prevent simultaneous reuse
                $stmt = $conn->prepare(
                    "SELECT user_id
                     FROM password_resets
                     WHERE token_hash = ?
                     AND expires_at > NOW()
                     LIMIT 1 FOR UPDATE"
                );
                $stmt->bind_param('s', $tokenHash);
                $stmt->execute();
                $lockedReset = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if (!$lockedReset) {
                    throw new Exception('Reset link already used or expired.');
                }

                $userId = (int)$lockedReset['user_id'];
                $hashedPassword = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $stmt = $conn->prepare(
                    "UPDATE users SET PASSWORD = ? WHERE USER_ID = ?"
                );
                $stmt->bind_param('si', $hashedPassword, $userId);
                $stmt->execute();
                $stmt->close();

                // Invalidate all reset links for this user
                $stmt = $conn->prepare(
                    "DELETE FROM password_resets WHERE user_id = ?"
                );
                $stmt->bind_param('i', $userId);
                $stmt->execute();
                $stmt->close();

                $conn->commit();

                unset($_SESSION['reset_csrf']);
                $success = true;
                $validToken = false;

            } catch (Throwable $e) {
                $conn->rollback();
                error_log('MIRA password reset error: ' . $e->getMessage());
                $error = 'Unable to reset password. Please try again.';
            }
        }
    }
}

if ($validToken && !isset($_SESSION['reset_csrf'])) {
    $_SESSION['reset_csrf'] = bin2hex(random_bytes(32));
}

function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | MIRA</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: #F5F2F7;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .card {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 40px 35px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 35px rgba(0,0,0,0.06);
        }

        .logo {
            color: #7654bb;
            font-size: 34px;
            font-weight: bold;
            letter-spacing: 4px;
            margin-bottom: 25px;
        }

        h2 {
            color: #292333;
            margin-bottom: 12px;
        }

        p {
            color: #777;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 25px;
        }

        input {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: #7654bb;
        }

        button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #ff8a3d, #e04b7a);
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .message {
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .error {
            background: #fff0f0;
            color: #c0392b;
        }

        .success {
            background: #eaf8ef;
            color: #228b50;
        }

        .back {
            display: inline-block;
            margin-top: 25px;
            color: #7654bb;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="card">

    <div class="logo">MIRA</div>

    <?php if ($success): ?>

        <h2>Password Updated!</h2>

        <div class="message success">
            Your password has been reset successfully.
        </div>

        <a class="back" href="login.html">
            Go to Login →
        </a>

    <?php elseif (!$validToken): ?>

        <h2>Invalid Reset Link</h2>

        <div class="message error">
            This password reset link is invalid or has expired.
        </div>

        <a class="back" href="forgot-password.html">
            Request New Link →
        </a>

    <?php else: ?>

        <h2>Create New Password</h2>

        <p>Enter your new password below.</p>

        <?php if ($error): ?>
            <div class="message error">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <input
                type="hidden"
                name="token"
                value="<?= e($token) ?>"
            >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($_SESSION['reset_csrf']) ?>"
            >

            <input
                type="password"
                name="password"
                placeholder="New Password"
                minlength="8"
                autocomplete="new-password"
                required
            >

            <input
                type="password"
                name="confirm_password"
                placeholder="Confirm New Password"
                minlength="8"
                autocomplete="new-password"
                required
            >

            <button type="submit">
                Reset Password
            </button>

        </form>

        <a class="back" href="login.html">
            ← Back to Login
        </a>

    <?php endif; ?>

</div>

</body>
</html>