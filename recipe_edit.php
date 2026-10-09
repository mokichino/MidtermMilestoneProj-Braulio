<?php
require_once __DIR__ . '/includes/bootstrap.php';
Auth::requireLogin();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$recipe = $id ? Recipe::find($id) : null;
if (!$recipe || (int) $recipe['user_id'] !== Auth::id()) { http_response_code(403); exit('You can only edit your own recipes.'); }
$pageTitle = 'Edit recipe';
$categories = Recipe::categories();
$currentIngredients = Recipe::ingredients($id);
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $title = trim((string) ($_POST['title'] ?? '')); $description = trim((string) ($_POST['short_description'] ?? '')); $instructions = trim((string) ($_POST['instructions'] ?? '')); $categoryId = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT) ?: 0;
    $quantities = $_POST['quantity'] ?? []; $units = $_POST['unit'] ?? []; $names = $_POST['ingredient_name'] ?? []; $ingredients = [];
    foreach ($names as $index => $name) {
        $name = trim((string) $name);
        $quantity = trim((string) ($quantities[$index] ?? ''));
        $unit = trim((string) ($units[$index] ?? ''));

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
    if (!$categoryId) $errors[] = 'Please choose a category.'; if (mb_strlen($instructions) < 20) $errors[] = 'Please add clear cooking instructions.'; if (!$ingredients) $errors[] = 'Add at least one ingredient.';
    foreach ($ingredients as $ingredient) {
        if ($ingredient['name'] === '' || $ingredient['quantity'] === '' || $ingredient['unit'] === '') {
            $errors[] = 'Complete every ingredient row or remove it.';
        }
        if (mb_strlen($ingredient['unit']) > 50) {
            $errors[] = 'Each unit must be 50 characters or fewer.';
        }
    }
    if (!$errors) { Recipe::update($id, Auth::id(), $categoryId, $title, $description, $instructions, $ingredients); flash('success', 'Your recipe was updated.'); redirect('recipe_view.php?id=' . $id); }
    $currentIngredients = $ingredients;
}
require __DIR__ . '/includes/header.php';
?>
<section class="container page-section narrow-content"><p class="eyebrow">A fresh edit</p><h1>Polish your recipe.</h1><p class="lead">Keep the good parts, clarify the little details, and save the updated version.</p><?php require __DIR__ . '/includes/recipe_form.php'; ?></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
