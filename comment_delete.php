<?php
require_once __DIR__ . '/includes/bootstrap.php';
Auth::requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('recipes.php');
verifyCsrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
$recipeId = filter_input(INPUT_POST, 'recipe_id', FILTER_VALIDATE_INT) ?: 0;
if (Comment::delete($id, Auth::id())) flash('success', 'Your note was deleted.'); else flash('error', 'That note could not be deleted.');
redirect('recipe_view.php?id=' . $recipeId);
