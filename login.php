<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (Auth::check()) redirect('dashboard.php');
$pageTitle = 'Log in';
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !Auth::attempt($email, $password)) $error = 'The email or password was not recognized.';
    else redirect('dashboard.php');
}
require __DIR__ . '/includes/header.php';
?>
<section class="auth-shell container"><div class="auth-copy"><p class="eyebrow">Good to see you</p><h1>Back to the kitchen.</h1><p>Sign in to keep sharing, saving, and tending your collection of favorite recipes.</p></div><div class="form-panel"><h2>Log in</h2><p class="muted">Your recipe shelf is waiting.</p><?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?><form method="post" novalidate><?= csrfField() ?><label>Email address<input type="email" name="email" value="<?= old('email') ?>" required autocomplete="email"></label><label>Password<input type="password" name="password" required autocomplete="current-password"></label><button class="button button-block" type="submit">Log in</button></form><p class="form-note">New to Sugar Nest? <a href="<?= BASE_URL ?>/register.php">Create an account</a></p></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
