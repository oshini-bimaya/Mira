<?php
require __DIR__ . '/auth.php';
if (empty($_SESSION['category_csrf'])) $_SESSION['category_csrf'] = bin2hex(random_bytes(32));
$token = $_SESSION['category_csrf'];
$rows = $conn->query('SELECT c.category_id, c.category_name, c.description, c.image_path, c.is_active, COUNT(a.artwork_id) AS artwork_count FROM categories c LEFT JOIN artworks a ON a.category_id = c.category_id GROUP BY c.category_id, c.category_name, c.description, c.image_path, c.is_active ORDER BY c.category_name');
$categories = $rows ? $rows->fetch_all(MYSQLI_ASSOC) : [];
$messages = ['created'=>'Category created successfully.', 'updated'=>'Category updated successfully.', 'duplicate'=>'A category with that name already exists.', 'invalid'=>'Please check the fields and try again.', 'image'=>'Invalid image. Use JPG, PNG or WebP (up to 3 MB).', 'missing'=>'Category not found.', 'error'=>'Could not save the category. Check migration and server logs.'];
$code = (string)($_GET['msg'] ?? ''); $message = $messages[$code] ?? '';
?><!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Categories | MIRA Admin</title>
<script>try{document.documentElement.dataset.theme=localStorage.getItem('mira-theme')==='dark'?'dark':'light'}catch(e){document.documentElement.dataset.theme='light'}</script>
<link rel="stylesheet" href="admin.css"><link rel="stylesheet" href="categories.css"><script src="admin.js" defer></script></head>
<body class="admin-page"><div class="admin-layout">
<aside class="admin-sidebar"><a href="dashboard.php" class="admin-brand"><span class="brand-symbol">✦</span><span class="brand-copy">MIRA <small>ADMIN STUDIO</small></span></a>
<nav class="admin-nav" aria-label="Admin navigation"><a href="dashboard.php">▦ Dashboard</a><a href="artworks.php">▤ Artworks</a><a href="categories.php" class="active" aria-current="page">◈ Categories</a><span class="admin-nav-disabled">▣ Exhibitions <small>Soon</small></span><span class="admin-nav-disabled">✧ Featured Art <small>Soon</small></span><span class="admin-nav-disabled">▥ Reports <small>Soon</small></span></nav>
<div class="admin-user-box"><div class="admin-user"><span class="admin-avatar">✦</span><div><strong><?=h($adminName)?></strong><span>Administrator</span></div></div><form action="logout.php" method="post"><button class="logout-button" type="submit">Log out</button></form></div></aside>
<main class="admin-main"><header class="admin-topbar"><p class="admin-breadcrumb">MIRA / <strong>Categories</strong></p><button id="themeToggle" class="admin-theme-toggle" type="button">☾ Dark mode</button></header>
<section class="admin-hero"><p class="admin-hero-label">ART & CRAFT COLLECTION</p><h1>Manage categories <span>✦</span></h1><p>Create, edit, and deactivate categories without deleting existing artwork.</p></section>
<?php if($message):?><p class="cat-message <?= in_array($code,['created','updated'],true)?'ok':'bad' ?>" role="status"><?=h($message)?></p><?php endif; ?>
<section class="cat-layout"><div class="cat-list"><div class="cat-header"><h2>Categories <small>(<?= count($categories) ?>)</small></h2><input id="categorySearch" type="search" placeholder="Search categories..." aria-label="Search categories"></div>
<div class="cat-grid" id="categoryGrid">
<?php foreach($categories as $cat): $validImage = is_string($cat['image_path']) && preg_match('~^uploads/categories/[a-zA-Z0-9_-]+\.(jpg|jpeg|png|webp)$~i',$cat['image_path']); ?>
<article class="cat-card" data-search="<?=h(strtolower($cat['category_name'].' '.$cat['description']))?>">
<div class="cat-photo"><?php if($validImage):?><img src="../<?=h($cat['image_path'])?>" alt="<?=h($cat['category_name'])?> category cover" loading="lazy"><?php else:?><span aria-hidden="true">✦</span><?php endif;?></div>
<div class="cat-content"><div class="cat-line"><h3><?=h($cat['category_name'])?></h3><span class="cat-state <?=((int)$cat['is_active']===1)?'is-active':'is-inactive'?>"><?=((int)$cat['is_active']===1)?'Active':'Inactive'?></span></div><p><?=h($cat['description'] ?: 'No description yet.')?></p><small><?= (int)$cat['artwork_count'] ?> artwork(s)</small>
<div class="cat-buttons"><button type="button" class="cat-edit" data-id="<?= (int)$cat['category_id'] ?>" data-name="<?=h($cat['category_name'])?>" data-description="<?=h($cat['description'])?>">Edit</button>
<form method="post" action="save-category.php" onsubmit="return confirm('Change category availability for new student uploads?')"><input type="hidden" name="csrf" value="<?=h($token)?>"><input type="hidden" name="category_id" value="<?= (int)$cat['category_id'] ?>"><button type="submit" name="action" value="toggle"><?=((int)$cat['is_active']===1)?'Deactivate':'Activate'?></button></form></div></div></article>
<?php endforeach; ?>
</div><p id="categoryEmpty" <?= $categories?'hidden':'' ?>><?= $categories?'No matching categories.':'No categories yet. Create one using the form.' ?></p></div>
<div class="cat-editor admin-panel"><h2 id="editorTitle">Add a category</h2><p>Category changes are reflected in the student upload form.</p><form method="post" action="save-category.php" enctype="multipart/form-data" id="categoryForm">
<input type="hidden" name="csrf" value="<?=h($token)?>"><input type="hidden" id="categoryId" name="category_id" value=""><input type="hidden" id="categoryAction" name="action" value="create">
<label for="categoryName">Category name</label><input id="categoryName" name="category_name" maxlength="200" required placeholder="e.g. Painting">
<label for="categoryDescription">Description</label><textarea id="categoryDescription" name="description" maxlength="2000" rows="5" placeholder="What kind of artwork belongs here?"></textarea>
<label for="categoryCover">Cover image (optional)</label><input id="categoryCover" name="cover" type="file" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG or WebP; maximum 3 MB. Upload a new cover to replace an old one.</small>
<div class="cat-editor-actions"><button class="cat-primary" id="submitCategory" type="submit">+ Add category</button><button class="cat-secondary" id="resetCategory" type="button">Clear</button></div></form></div></section>
<footer class="admin-footer">© <?=date('Y')?> MIRA · NSBM Student Arts & Handcraft</footer></main></div>
<script>
const form=document.getElementById('categoryForm');const id=document.getElementById('categoryId');const action=document.getElementById('categoryAction');const nameField=document.getElementById('categoryName');const desc=document.getElementById('categoryDescription');
function clearEditor(){form.reset();id.value='';action.value='create';document.getElementById('editorTitle').textContent='Add a category';document.getElementById('submitCategory').textContent='+ Add category';}
document.getElementById('resetCategory').addEventListener('click',clearEditor);
document.querySelectorAll('.cat-edit').forEach(button=>button.addEventListener('click',()=>{id.value=button.dataset.id;action.value='edit';nameField.value=button.dataset.name;desc.value=button.dataset.description;document.getElementById('editorTitle').textContent='Edit category';document.getElementById('submitCategory').textContent='Save changes';nameField.focus();document.getElementById('categoryForm').scrollIntoView({behavior:'smooth',block:'center'});}));
document.getElementById('categorySearch').addEventListener('input',e=>{let visible=0;const query=e.target.value.trim().toLowerCase();document.querySelectorAll('.cat-card').forEach(card=>{const show=card.dataset.search.includes(query);card.hidden=!show;if(show)visible++;});document.getElementById('categoryEmpty').hidden=visible>0;document.getElementById('categoryEmpty').textContent=query?'No matching categories.':'No categories yet.';});
</script></body></html>
