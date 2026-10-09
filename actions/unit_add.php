<?php
require_once __DIR__ . '/../includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

if (!Auth::check()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please log in first.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

verifyCsrf();

$categoryId = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT) ?: 0;
$unit = trim((string) ($_POST['unit'] ?? ''));

if (!$categoryId || $unit === '' || mb_strlen($unit) > 50) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Choose a category and enter a unit of 50 characters or fewer.']);
    exit;
}

$saveResult = Recipe::addCategoryUnit($categoryId, $unit);

if ($saveResult === 'not_found') {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'The selected category was not found.']);
    exit;
}

if ($saveResult === 'duplicate') {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'That unit already exists for this category.']);
    exit;
}

if ($saveResult !== 'added') {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'The unit could not be saved.']);
    exit;
}

echo json_encode([
    'success' => true,
    'unit' => $unit,
    'message' => 'The custom unit was saved for this category.',
]);