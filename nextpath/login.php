<?php
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    redirect(base_url(current_user_role() === 'admin' ? 'admin/dashboard.php' : 'student/dashboard.php'));
}

$errors = [];
$old = ['email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $old['email'] = $email;

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if (empty($password)) {
        $errors['password'] = 'Please enter your password.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id, full_name, password, role FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && verify_password($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];
            redirect(base_url($user['role'] === 'admin' ? 'admin/dashboard.php' : 'student/dashboard.php'));
        } else {
            $errors['general'] = 'Incorrect email or password.';
        }
    }
}

$page_title = 'Login — NEXTPath';
require __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">

<section class="auth-section">
  <div class="auth-card">
    <p class="eyebrow">Welcome Back</p>
    <h1 class="auth-title">Log in to NEXTPath</h1>

    <?php if (!empty($errors['general'])): ?>
      <div class="form-error-banner"><?= clean($errors['general']) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php" novalidate id="loginForm">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= clean($old['email']) ?>" required>
        <span class="field-error"><?= clean($errors['email'] ?? '') ?></span>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <div class="password-wrap">
          <input type="password" id="password" name="password" required>
          <button type="button" class="password-toggle" data-target="password">Show</button>
        </div>
        <span class="field-error"><?= clean($errors['password'] ?? '') ?></span>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>

    <div class="auth-links">
      <a href="#">Forgot password?</a>
      <span>Don't have an account? <a href="<?= base_url('register.php') ?>">Create account</a></span>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
<script src="<?= base_url('assets/js/validation.js') ?>"></script>
