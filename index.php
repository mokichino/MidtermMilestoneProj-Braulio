<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Home';
$recipes = Recipe::latest('', null, Auth::id());
$categories = Recipe::categories();
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="container hero-grid">
        <div>
            <p class="eyebrow">A little sweetness, shared locally</p>
            <h1>Good recipes live longer when neighbors pass them on.</h1>
            <p class="hero-copy">Sugar Nest is a cozy recipe shelf for the bakers and home cooks of our community.</p>
            <div class="hero-actions"><a class="button" href="<?= BASE_URL ?>/recipes.php">Explore recipes</a><a class="text-link" href="<?= BASE_URL ?>/register.php">Become a member <span>→</span></a></div>
        </div>
        <div class="hero-note"><span class="note-pin">✦</span><p>Today’s kitchen note</p><strong>“The best ingredient is the one you are happy to share.”</strong><small>— Sugar Nest community</small></div>
    </div>
</section>
<section class="container page-section">
    <div class="section-heading"><div><p class="eyebrow">From the community</p><h2>Fresh from our ovens</h2></div><a class="text-link" href="<?= BASE_URL ?>/recipes.php">See all recipes <span>→</span></a></div>
    <?php if (!$recipes): ?>
        <div class="empty-state"><h3>The recipe shelf is waiting.</h3><p>Be the first neighbor to share a favorite bake.</p><a class="button" href="<?= BASE_URL ?>/register.php">Join the community</a></div>
    <?php else: ?>
        <div class="recipe-grid">
            <?php foreach (array_slice($recipes, 0, 6) as $recipe): ?>
                <article class="recipe-card"><div class="recipe-image"><span><?= e(strtoupper(substr($recipe['category_name'], 0, 2))) ?></span></div><div class="recipe-card-body"><div class="card-meta"><span><?= e($recipe['category_name']) ?></span><span><?= date('M j, Y', strtotime($recipe['created_at'])) ?></span></div><h3><a href="<?= BASE_URL ?>/recipe_view.php?id=<?= (int) $recipe['id'] ?>"><?= e($recipe['title']) ?></a></h3><p><?= e($recipe['short_description']) ?></p><div class="card-footer"><small>by <?= e($recipe['author_name']) ?></small><a class="arrow-link" href="<?= BASE_URL ?>/recipe_view.php?id=<?= (int) $recipe['id'] ?>" aria-label="View recipe">↗</a></div></div></article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<section class="community-band"><div class="container band-grid"><div><p class="eyebrow">Made for neighbors</p><h2>Every kitchen has a story worth saving.</h2></div><p>Keep your family favorites in one place, discover something new, and leave a kind note for the person who shared it.</p></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
