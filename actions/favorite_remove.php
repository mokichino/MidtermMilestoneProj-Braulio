<?php
require_once __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
if (!Auth::check()) { http_response_code(401); echo json_encode(['success' => false, 'message' => 'Please log in first.']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success' => false, 'message' => 'Method not allowed.']); exit; }
verifyCsrf();
$recipeId = filter_input(INPUT_POST, 'recipe_id', FILTER_VALIDATE_INT) ?: 0;
if (!$recipeId || !Recipe::find($recipeId)) { http_response_code(404); echo json_encode(['success' => false, 'message' => 'Recipe not found.']); exit; }
Favorite::remove(Auth::id(), $recipeId);
echo json_encode(['success' => true, 'saved' => false, 'message' => 'Removed from your favorites.']);
