<?php
require __DIR__ . '/auth.php';
$stmt = $conn->prepare('SELECT USER_ID, FULL_NAME, EMAIL, ROLE, CREATED_AT FROM users WHERE USER_ID = ? LIMIT 1');
$stmt->bind_param('i', $uid);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$profile) { http_response_code(404); exit('Profile not found.'); }
if (empty($_SESSION['admin_profile_csrf'])) {
    $_SESSION['admin_profile_csrf'] = bin2hex(random_bytes(32));
}
$initial = function_exists('mb_substr') ? mb_substr($profile['FULL_NAME'], 0, 1) : substr($profile['FULL_NAME'], 0, 1);
$messages = [
    'saved' => 'Profile updated successfully.',
    'password' => 'Password updated successfully.',
    'invalid' => 'Please enter a valid name and email address.',
    'taken' => 'This email address is already registered.',
    'incorrect' => 'Your current password is incorrect.',
    'weak' => 'Your new password must contain at least 8 characters.',
    'mismatch' => 'New password and confirmation do not match.',
    'failed' => 'Unable to save changes. Please try again.'
];
$status = $_GET['status'] ?? '';
$isError = $status !== '' && !in_array($status, ['saved','password'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Profile | MIRA</title>
<script>try{document.documentElement.dataset.theme=localStorage.getItem('mira-theme')==='dark'?'dark':'light';}catch(e){document.documentElement.dataset.theme='light';}</script>
<link rel="stylesheet" href="admin.css">
<link rel="stylesheet" href="profile.css">
<script src="admin.js" defer></script>
</head>
<body class="admin-page">
<div class="admin-layout">
<?php $activeAdminPage = 'profile'; require __DIR__ . '/sidebar.php'; ?>
<main class="admin-main">
<header class="admin-topbar">
<p class="admin-breadcrumb"><a href="dashboard.php">MIRA</a> / <strong>My Profile</strong></p>
<div class="admin-top-actions"><button id="themeToggle" class="theme-button admin-theme-toggle" type="button" aria-label="Toggle dark mode">☾ &nbsp; Dark mode</button><a href="profile.php" class="admin-avatar admin-avatar-small admin-profile-link" aria-label="Admin profile"><?= h(strtoupper($initial)) ?></a></div>
</header>
<section class="admin-hero"><p class="admin-hero-label">MIRA · ADMIN STUDIO</p><h1>My Profile ✦</h1><p>Manage your administrator account information.</p></section>
<?php if (isset($messages[$status])): ?><div class="profile-alert <?= $isError ? 'profile-alert-error' : 'profile-alert-success' ?>" role="status"><?= h($messages[$status]) ?></div><?php endif; ?>
<div class="profile-grid">
<section class="admin-panel profile-overview">
<div class="profile-avatar-large" aria-hidden="true"><?= h(strtoupper($initial)) ?></div>
<h2><?= h($profile['FULL_NAME']) ?></h2>
<p class="profile-role">Administrator</p>
<div class="profile-facts"><div><span>Email address</span><strong><?= h($profile['EMAIL']) ?></strong></div><div><span>Role</span><strong><?= h($profile['ROLE']) ?></strong></div><div><span>Member since</span><strong><?= h(!empty($profile['CREATED_AT']) ? date('F j, Y', strtotime($profile['CREATED_AT'])) : 'Not available') ?></strong></div></div>
<a class="profile-back" href="dashboard.php">← Back to Dashboard</a>
</section>
<div class="profile-forms">
<section class="admin-panel"><h2>Edit profile</h2><p class="admin-panel-subtitle">Update your display name and email address.</p>
<form method="post" action="update-profile.php" class="profile-form">
<input type="hidden" name="csrf_token" value="<?= h($_SESSION['admin_profile_csrf']) ?>"><input type="hidden" name="action" value="details">
<label for="full_name">Full name</label><input id="full_name" name="full_name" type="text" maxlength="150" required value="<?= h($profile['FULL_NAME']) ?>">
<label for="email">Email address</label><input id="email" name="email" type="email" maxlength="255" required value="<?= h($profile['EMAIL']) ?>">
<button class="admin-primary-button" type="submit">Save changes</button>
</form></section>
<section class="admin-panel"><h2>Change password</h2><p class="admin-panel-subtitle">Enter your current password to set a new one.</p>
<form method="post" action="update-profile.php" class="profile-form">
<input type="hidden" name="csrf_token" value="<?= h($_SESSION['admin_profile_csrf']) ?>"><input type="hidden" name="action" value="password">
<label for="current_password">Current password</label><input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
<label for="new_password">New password (8+ characters)</label><input id="new_password" name="new_password" type="password" minlength="8" autocomplete="new-password" required>
<label for="confirm_password">Confirm new password</label><input id="confirm_password" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required>
<button class="admin-primary-button" type="submit">Update password</button>
</form></section>
</div></div>
<footer class="admin-footer">© <?= date('Y') ?> MIRA · NSBM Student Arts &amp; Handcraft Showcasing System</footer>
</main></div>
</body></html>
