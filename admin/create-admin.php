<?php
require __DIR__ . '/auth.php';
// Protected existing-admin-only promotion. Public signups cannot create admins.
if (empty($_SESSION['admin_promote_csrf'])) $_SESSION['admin_promote_csrf'] = bin2hex(random_bytes(32));
$message = ''; $success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string)($_POST['csrf'] ?? '');
    if (!hash_equals($_SESSION['admin_promote_csrf'], $token)) { http_response_code(403); $message='Session verification failed. Refresh and retry.'; }
    else {
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['admin_password'] ?? '');
        $verify = $conn->prepare("SELECT PASSWORD FROM users WHERE USER_ID = ? AND ROLE = 'ADMIN' LIMIT 1");
        $verify->bind_param('i', $uid); $verify->execute(); $current = $verify->get_result()->fetch_assoc(); $verify->close();
        if (!$current || !password_verify($password, $current['PASSWORD'])) $message='Administrator password is incorrect.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $message='Enter a valid account email.';
        else {
            $query = $conn->prepare('SELECT USER_ID, ROLE, account_status FROM users WHERE EMAIL = ? LIMIT 1');
            $query->bind_param('s', $email); $query->execute(); $target = $query->get_result()->fetch_assoc(); $query->close();
            if (!$target) $message='Account not found. Ask the person to sign up first.';
            elseif (strtolower((string)$target['account_status']) !== 'active') $message='The target account must be active.';
            elseif (strtoupper((string)$target['ROLE']) === 'ADMIN') $message='That account is already an administrator.';
            elseif (!in_array(strtoupper((string)$target['ROLE']), ['STUDENT','ARTIST'],true)) $message='Account role is not eligible.';
            else {
                $id=(int)$target['USER_ID'];
                $promote=$conn->prepare("UPDATE users SET ROLE='ADMIN' WHERE USER_ID = ? AND ROLE IN ('STUDENT','ARTIST') AND account_status='active'");
                $promote->bind_param('i',$id); $promote->execute(); $success = $promote->affected_rows === 1; $promote->close();
                $message=$success?'Administrator role granted. The user must log out and log back in.':'No account was changed.';
                $_SESSION['admin_promote_csrf']=bin2hex(random_bytes(32));
            }
        }
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Account Management | MIRA</title>
<script>try{document.documentElement.dataset.theme=localStorage.getItem('mira-theme')==='dark'?'dark':'light'}catch(e){}</script>
<link rel="stylesheet" href="admin.css"><link rel="stylesheet" href="create-admin.css"><script src="admin.js" defer></script><link rel="stylesheet" href="../mira-design-system.css?v=1">
<link rel="stylesheet" href="mira-admin-polish.css?v=1">
</head>
<body class="admin-page"><div class="admin-layout">
<?php $activeAdminPage='create-admin'; require __DIR__ . '/sidebar.php'; ?>
<main class="admin-main"><header class="admin-topbar"><p class="admin-breadcrumb">MIRA / <strong>Admin Account Management</strong></p><div class="admin-top-actions"><button id="themeToggle" class="theme-button admin-theme-toggle" type="button">☾ Dark mode</button></div></header>
<section class="admin-panel mira-promote"><h1>Grant Administrator Access</h1><p class="admin-panel-subtitle">Only existing administrators can promote a registered Viewer or Artist. Use this only for people authorized to manage MIRA.</p>
<?php if($message!==''):?><p role="status" class="mira-feedback <?= $success?'is-success':'' ?>"><?= h($message) ?></p><?php endif;?>
<form method="post" autocomplete="off"><input type="hidden" name="csrf" value="<?= h($_SESSION['admin_promote_csrf']) ?>"><label for="targetEmail">Registered user email</label><input id="targetEmail" name="email" type="email" required maxlength="254" placeholder="user@example.com"><label for="adminPassword">Your administrator password</label><input id="adminPassword" name="admin_password" type="password" required autocomplete="current-password"><p class="admin-panel-subtitle">This action grants full admin privileges. Confirm you trust the account holder before proceeding.</p><button class="admin-primary-button" type="submit">Grant Admin Role</button></form>
</section></main></div></body></html>
