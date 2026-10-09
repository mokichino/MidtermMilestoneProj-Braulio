<?php
require_once __DIR__ . '/includes/bootstrap.php';
Auth::requireLogin();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$statement = Database::connection()->prepare('SELECT * FROM comments WHERE id = :id AND user_id = :user_id LIMIT 1');
$statement->execute(['id' => $id, 'user_id' => Auth::id()]);
$comment = $statement->fetch();
if (!$comment) { http_response_code(403); exit('You can only edit your own comments.'); }
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf(); $text = trim((string) ($_POST['comment_text'] ?? ''));
    if (mb_strlen($text) < 2 || mb_strlen($text) > 1000) $errors[] = 'Your note must be between 2 and 1,000 characters.';
    else { Comment::update($id, Auth::id(), $text); flash('success', 'Your note was updated.'); redirect('recipe_view.php?id=' . (int) $comment['recipe_id']); }
}
$pageTitle = 'Edit note'; require __DIR__ . '/includes/header.php';
?><section class="container page-section narrow-content"><p class="eyebrow">Neighbor note</p><h1>Edit your note.</h1><div class="form-panel compact-panel"><?php if ($errors): ?><div class="alert alert-error"><?= e($errors[0]) ?></div><?php endif; ?><form method="post"><?= csrfField() ?><label>Comment<textarea name="comment_text" rows="6" maxlength="1000" required><?= old('comment_text', $comment['comment_text']) ?></textarea></label><div class="form-actions"><button class="button" type="submit">Save note</button><a class="text-link" href="<?= BASE_URL ?>/recipe_view.php?id=<?= (int) $comment['recipe_id'] ?>">Cancel</a></div></form></div></section><?php require __DIR__ . '/includes/footer.php'; ?>
