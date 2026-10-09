<?php
require_once __DIR__ . '/includes/bootstrap.php';
Auth::requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('dashboard.php');
verifyCsrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
if (Recipe::delete($id, Auth::id())) flash('success', 'The recipe was removed from the shelf.'); else flash('error', 'That recipe could not be removed.');
redirect('dashboard.php');
