<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (Auth::check()) redirect('dashboard.php');
$pageTitle = 'Join the community';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $name = trim((string) ($_POST['full_name'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['password_confirmation'] ?? '');
    if (mb_strlen($name) < 2 || mb_strlen($name) > 100) $errors[] = 'Please enter your full name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) $errors[] = 'Please enter a valid email address.';
    if (strlen($password) < 8) $errors[] = 'Your password must be at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'The passwords do not match.';
    if (!$errors && User::findByEmail($email)) $errors[] = 'An account with that email already exists.';
    if (!$errors) { User::create($name, $email, $password); flash('success', 'Your Sugar Nest account is ready. Please log in.'); redirect('login.php'); }
}
require __DIR__ . '/includes/header.php';
?>
<section class="auth-shell container"><div class="auth-copy"><p class="eyebrow">Welcome in</p><h1>Pull up a chair.</h1><p>Sugar Nest is a warm corner for recipes, notes, and the little tricks that make baking feel like home.</p></div><div class="form-panel"><h2>Create your account</h2><p class="muted">Join the barangay recipe shelf.</p><?php if ($errors): ?><div class="alert alert-error"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?><form method="post" novalidate><?= csrfField() ?><label>Full name<input type="text" name="full_name" value="<?= old('full_name') ?>" required maxlength="100" autocomplete="name"></label><label>Email address<input type="email" name="email" value="<?= old('email') ?>" required maxlength="150" autocomplete="email"></label><label>Password<input type="password" name="password" required minlength="8" autocomplete="new-password"><small>At least 8 characters.</small></label><label>Confirm password<input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"></label><button class="button button-block" type="submit">Create account</button></form><p class="form-note">Already a member? <a href="<?= BASE_URL ?>/login.php">Log in</a></p></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
