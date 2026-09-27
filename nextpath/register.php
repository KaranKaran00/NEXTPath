<?php
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    redirect(base_url(current_user_role() === 'admin' ? 'admin/dashboard.php' : 'student/dashboard.php'));
}

$education_levels = ['High School', 'Higher Secondary', 'Undergraduate', 'Graduate', 'Postgraduate'];

$errors = [];
$old = [
    'full_name' => '', 'email' => '', 'mobile' => '', 'gender' => '',
    'education_level' => '', 'college_name' => '', 'interests' => '',
    'skills' => '', 'hobbies' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $key => $_) {
        $old[$key] = trim($_POST[$key] ?? '');
    }
    $password = $_POST['password'] ?? '';

    $errors = validate_registration(array_merge($old, ['password' => $password]));

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$old['email']]);
        if ($stmt->fetch()) {
            $errors['email'] = 'An account with this email already exists.';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('
            INSERT INTO users (full_name, email, mobile, password, gender, education_level, college_name, interests, skills, hobbies, role)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "student")
        ');
        $stmt->execute([
            $old['full_name'], $old['email'], $old['mobile'], hash_password($password),
            $old['gender'], $old['education_level'], $old['college_name'],
            $old['interests'], $old['skills'], $old['hobbies'],
        ]);

        $userId = $pdo->lastInsertId();
        $_SESSION['user_id']   = $userId;
        $_SESSION['user_name'] = $old['full_name'];
        $_SESSION['role']      = 'student';
        set_flash('success', 'Welcome to NEXTPath! Your profile is ready.');
        redirect(base_url('student/dashboard.php'));
    }
}

$page_title = 'Create Your Profile — NEXTPath';
require __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">

<section class="auth-section">
  <div class="auth-card auth-card-wide">
    <p class="eyebrow">Create Your NEXTPath Profile</p>
    <h1 class="auth-title">Let's get to know you</h1>

    <form method="POST" action="register.php" novalidate id="registerForm">

      <h2 class="form-section-title">Personal Information</h2>
      <div class="form-grid">
        <div class="form-group">
          <label for="full_name">Full Name</label>
          <input type="text" id="full_name" name="full_name" value="<?= clean($old['full_name']) ?>" required>
          <span class="field-error"><?= clean($errors['full_name'] ?? '') ?></span>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?= clean($old['email']) ?>" required>
          <span class="field-error"><?= clean($errors['email'] ?? '') ?></span>
        </div>
        <div class="form-group">
          <label for="mobile">Mobile Number</label>
          <input type="text" id="mobile" name="mobile" value="<?= clean($old['mobile']) ?>" maxlength="10" required>
          <span class="field-error"><?= clean($errors['mobile'] ?? '') ?></span>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <div class="password-wrap">
            <input type="password" id="password" name="password" required minlength="6">
            <button type="button" class="password-toggle" data-target="password">Show</button>
          </div>
          <span class="field-error"><?= clean($errors['password'] ?? '') ?></span>
        </div>
        <div class="form-group">
          <label for="gender">Gender</label>
          <select id="gender" name="gender" required>
            <option value="">Select Gender</option>
            <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
              <option value="<?= $g ?>" <?= $old['gender'] === $g ? 'selected' : '' ?>><?= $g ?></option>
            <?php endforeach; ?>
          </select>
          <span class="field-error"><?= clean($errors['gender'] ?? '') ?></span>
        </div>
      </div>

      <h2 class="form-section-title">Education</h2>
      <div class="form-grid">
        <div class="form-group">
          <label for="education_level">Education Level</label>
          <select id="education_level" name="education_level" required>
            <option value="">Select Education Level</option>
            <?php foreach ($education_levels as $lvl): ?>
              <option value="<?= $lvl ?>" <?= $old['education_level'] === $lvl ? 'selected' : '' ?>><?= $lvl ?></option>
            <?php endforeach; ?>
          </select>
          <span class="field-error"><?= clean($errors['education_level'] ?? '') ?></span>
        </div>
        <div class="form-group">
          <label for="college_name">School / College Name</label>
          <input type="text" id="college_name" name="college_name" value="<?= clean($old['college_name']) ?>" required>
          <span class="field-error"><?= clean($errors['college_name'] ?? '') ?></span>
        </div>
      </div>

      <h2 class="form-section-title">About You</h2>
      <div class="form-grid">
        <div class="form-group">
          <label for="interests">Interests</label>
          <input type="text" id="interests" name="interests" value="<?= clean($old['interests']) ?>" placeholder="e.g. Robotics, Reading">
        </div>
        <div class="form-group">
          <label for="skills">Skills</label>
          <input type="text" id="skills" name="skills" value="<?= clean($old['skills']) ?>" placeholder="e.g. Communication, Coding">
        </div>
        <div class="form-group">
          <label for="hobbies">Hobbies</label>
          <input type="text" id="hobbies" name="hobbies" value="<?= clean($old['hobbies']) ?>" placeholder="e.g. Chess, Painting">
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-block">Create Account</button>
    </form>

    <div class="auth-links">
      <span>Already have an account? <a href="<?= base_url('login.php') ?>">Login</a></span>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
<script src="<?= base_url('assets/js/validation.js') ?>"></script>
