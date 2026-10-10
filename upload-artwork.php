<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.html'); exit; }
require __DIR__.'/db.php';
require __DIR__.'/artwork-common.php';
$uid=(int)$_SESSION['user_id'];
if (!artworkIsStudent($conn,$uid)) { http_response_code(403); exit('Student access only.'); }
$cats=$conn->query('SELECT category_id,category_name FROM categories ORDER BY category_name');
$name=$_SESSION['full_name']??'Student';
$status=$_GET['status']??'';
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Upload Artwork | MIRA</title><link rel="stylesheet" href="artwork-workflow.css"><script src="theme.js" defer></script></head>
<body class="mira-workflow"><main class="workflow-wrap">
<header class="workflow-top"><a href="user-home.php" class="back-link">← Back to gallery</a><button type="button" class="theme-toggle" aria-label="Toggle theme">🌙</button></header>
<div class="workflow-heading"><p class="overline">MIRA · SHARE YOUR CREATIVITY</p><h1>Upload your artwork</h1><p>Submit your original artwork for review. Approved work can appear in the MIRA gallery.</p></div>
<?php if($status==='success'): ?><p class="notice success" role="status">Artwork submitted successfully! It is pending admin approval.</p><?php endif; ?>
<?php if($status==='error'): ?><p class="notice error" role="alert">Upload failed. Check your artwork details and image, then try again.</p><?php endif; ?>
<form action="save-artwork.php" method="post" enctype="multipart/form-data" class="form-card">
<input type="hidden" name="csrf" value="<?= artworkEscape(artworkCsrf()) ?>">
<label for="title">Artwork title</label><input id="title" name="title" maxlength="200" required placeholder="e.g. Colours of Nature">
<label for="category">Category</label><select id="category" name="category_id" required><option value="">Choose a category</option><?php if($cats):while($cat=$cats->fetch_assoc()): ?><option value="<?= (int)$cat['category_id'] ?>"><?= artworkEscape($cat['category_name']) ?></option><?php endwhile;endif; ?></select>
<label for="description">Description</label><textarea id="description" name="description" maxlength="3000" rows="5" placeholder="Tell the story behind your artwork..." required></textarea>
<label for="artwork_image">Artwork image (JPG, PNG or WebP, max 5 MB)</label><input id="artwork_image" type="file" name="artwork_image" accept="image/jpeg,image/png,image/webp" required>
<img id="preview" alt="Selected artwork preview" class="upload-preview" hidden>
<div class="form-footer"><p>Only approved artwork is publicly visible.</p><button class="primary-btn" type="submit">Submit for review →</button></div>
</form><p class="under-form"><a href="my-artworks.php">View my submission history →</a></p></main>
<script>document.getElementById('artwork_image').addEventListener('change',e=>{const f=e.target.files[0],im=document.getElementById('preview');if(im.dataset.url)URL.revokeObjectURL(im.dataset.url);if(!f){im.hidden=true;return;}im.dataset.url=URL.createObjectURL(f);im.src=im.dataset.url;im.hidden=false;});</script>
</body></html>
