<?php
require_once __DIR__ . '/includes/bootstrap.php';
Auth::requireLogin();
$pageTitle = 'My kitchen';
$recipes = Recipe::latest('', null, Auth::id());
require __DIR__ . '/includes/header.php';
?>
<section class="container page-section dashboard-head"><div><p class="eyebrow">Member kitchen</p><h1>Welcome back, <?= e($_SESSION['user_name']) ?>.</h1><p class="lead">Your shared recipes and saved favorites, all in one cozy place.</p></div><a class="button" href="<?= BASE_URL ?>/recipe_create.php">+ Share a recipe</a></section><section class="container page-section"><div class="section-heading"><div><p class="eyebrow">Your contributions</p><h2>Recipes you’ve shared</h2></div><a class="text-link" href="<?= BASE_URL ?>/favorites.php">View favorites <span>→</span></a></div><?php if (!$recipes): ?><div class="empty-state"><h3>Your shelf is still quiet.</h3><p>Share the first recipe with your neighbors.</p><a class="button" href="<?= BASE_URL ?>/recipe_create.php">Write a recipe</a></div><?php else: ?><div class="recipe-grid"><?php foreach ($recipes as $recipe): ?><article class="recipe-card"><div class="recipe-image"><span><?= e(strtoupper(substr($recipe['category_name'], 0, 2))) ?></span></div><div class="recipe-card-body"><div class="card-meta"><span><?= e($recipe['category_name']) ?></span><span><?= date('M j, Y', strtotime($recipe['created_at'])) ?></span></div><h3><a href="<?= BASE_URL ?>/recipe_view.php?id=<?= (int) $recipe['id'] ?>"><?= e($recipe['title']) ?></a></h3><p><?= e($recipe['short_description']) ?></p><div class="card-footer"><a class="text-link" href="<?= BASE_URL ?>/recipe_edit.php?id=<?= (int) $recipe['id'] ?>">Edit recipe</a></div></div></article><?php endforeach; ?></div><?php endif; ?></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
