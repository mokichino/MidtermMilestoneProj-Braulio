<?php
require_once __DIR__ . '/includes/bootstrap.php';
Auth::requireLogin();
$pageTitle = 'Share a recipe';
$categories = Recipe::categories();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $title = trim((string) ($_POST['title'] ?? ''));
    $description = trim((string) ($_POST['short_description'] ?? ''));
    $instructions = trim((string) ($_POST['instructions'] ?? ''));
    $categoryId = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT) ?: 0;
    $quantities = $_POST['quantity'] ?? [];
    $units = $_POST['unit'] ?? [];
    $names = $_POST['ingredient_name'] ?? [];
    $ingredients = [];
    foreach ($names as $index => $name) {
        $name = trim((string) $name); $quantity = trim((string) ($quantities[$index] ?? '')); $unit = trim((string) ($units[$index] ?? ''));
        if ($name !== '' || $quantity !== '' || $unit !== '') {
            $ingredients[] = [
                'name' => $name,
                'quantity' => $quantity,
                'unit' => $unit,
            ];
        }
    }
    if (mb_strlen($title) < 3 || mb_strlen($title) > 150) $errors[] = 'Title must be between 3 and 150 characters.';
    if (mb_strlen($description) < 10 || mb_strlen($description) > 255) $errors[] = 'Short description must be between 10 and 255 characters.';
    if (!$categoryId) $errors[] = 'Please choose a category.';
    if (mb_strlen($instructions) < 20) $errors[] = 'Please add clear cooking instructions.';
    if (!$ingredients) $errors[] = 'Add at least one ingredient.';
    foreach ($ingredients as $ingredient) {
        if ($ingredient['name'] === '' || $ingredient['quantity'] === '' || $ingredient['unit'] === '') {
            $errors[] = 'Complete every ingredient row or remove it.';
        }
        if (mb_strlen($ingredient['unit']) > 50) {
            $errors[] = 'Each unit must be 50 characters or fewer.';
        }
    }
    if (!$errors) { $id = Recipe::create(Auth::id(), $categoryId, $title, $description, $instructions, $ingredients); flash('success', 'Your recipe is now on the community shelf.'); redirect('recipe_view.php?id=' . $id); }
}
require __DIR__ . '/includes/header.php';
?>
<section class="container page-section narrow-content"><p class="eyebrow">Share something good</p><h1>Tell us how you make it.</h1><p class="lead">Write the kind of recipe you would hand to a neighbor on a well-loved index card.</p><?php require __DIR__ . '/includes/recipe_form.php'; ?></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
