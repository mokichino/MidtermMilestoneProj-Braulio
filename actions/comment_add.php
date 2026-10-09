<?php
require_once __DIR__ . '/../includes/bootstrap.php';
Auth::requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('recipes.php');
verifyCsrf();
$recipeId = filter_input(INPUT_POST, 'recipe_id', FILTER_VALIDATE_INT) ?: 0;
$text = trim((string) ($_POST['comment_text'] ?? ''));
if (!$recipeId || !Recipe::find($recipeId)) { flash('error', 'That recipe could not be found.'); redirect('recipes.php'); }
if (mb_strlen($text) < 2 || mb_strlen($text) > 1000) flash('error', 'Your note must be between 2 and 1,000 characters.');
else { Comment::add($recipeId, Auth::id(), $text); flash('success', 'Your neighbor note was posted.'); }
redirect('recipe_view.php?id=' . $recipeId);
