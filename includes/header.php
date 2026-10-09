<?php
$flash = getFlash();
$pageTitle = $pageTitle ?? 'Sugar Nest';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= BASE_URL ?>">
    <meta name="csrf-token" content="<?= e(csrfToken()) ?>">
    <title><?= e($pageTitle) ?> | Sugar Nest</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="<?= BASE_URL ?>/index.php"><span class="brand-mark">SN</span><span>Sugar Nest</span></a>
        <button class="menu-toggle" type="button" aria-label="Toggle menu" aria-expanded="false">Menu</button>
        <nav class="site-nav" aria-label="Main navigation">
            <a href="<?= BASE_URL ?>/recipes.php">Browse recipes</a>
            <?php if (Auth::check()): ?>
                <a href="<?= BASE_URL ?>/favorites.php">Favorites</a>
                <a href="<?= BASE_URL ?>/recipe_create.php">Share a recipe</a>
                <a class="nav-account" href="<?= BASE_URL ?>/dashboard.php">Hi, <?= e($_SESSION['user_name'] ?? 'baker') ?></a>
                <a href="<?= BASE_URL ?>/logout.php" data-confirm="Log out of your Sugar Nest account?">Log out</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/login.php">Log in</a>
                <a class="button button-small" href="<?= BASE_URL ?>/register.php">Join Sugar Nest</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main>
    <?php if ($flash): ?>
        <div class="container"><div class="alert alert-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div></div>
    <?php endif; ?>
