<?php
require_once __DIR__ . '/../includes/auth.php';

$userId = current_user_id();
$stmt = $pdo->prepare('SELECT full_name, email, mobile, gender, education_level, college_name, interests, skills, hobbies FROM users WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();

$education_levels = ['High School', 'Higher Secondary', 'Undergraduate', 'Graduate', 'Postgraduate'];

$page_title = 'My Profile — NEXTPath';
$active = 'profile';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= clean($page_title) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/css/main.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
</head>
<body class="dashboard-body">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<main class="dashboard-main">
  <header class="dashboard-header">
    <div>
      <h1>My Profile</h1>
      <p class="dashboard-sub">Keep your details up to date.</p>
    </div>
  </header>

  <?php $flash = get_flash(); if ($flash): ?>
    <div class="flash flash-<?= clean($flash['type']) ?>"><?= clean($flash['message']) ?></div>
  <?php endif; ?>

  <section class="card">
    <form method="POST" action="update-profile.php" novalidate>
      <div class="form-grid">
        <div class="form-group">
          <label for="full_name">Full Name</label>
          <input type="text" id="full_name" name="full_name" value="<?= clean($user['full_name']) ?>" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" value="<?= clean($user['email']) ?>" disabled>
        </div>
        <div class="form-group">
          <label for="mobile">Mobile Number</label>
          <input type="text" id="mobile" name="mobile" value="<?= clean($user['mobile']) ?>" maxlength="10" required>
        </div>
        <div class="form-group">
          <label for="gender">Gender</label>
          <select id="gender" name="gender" required>
            <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
              <option value="<?= $g ?>" <?= $user['gender'] === $g ? 'selected' : '' ?>><?= $g ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="education_level">Education Level</label>
          <select id="education_level" name="education_level" required>
            <?php foreach ($education_levels as $lvl): ?>
              <option value="<?= $lvl ?>" <?= $user['education_level'] === $lvl ? 'selected' : '' ?>><?= $lvl ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="college_name">School / College</label>
          <input type="text" id="college_name" name="college_name" value="<?= clean($user['college_name']) ?>" required>
        </div>
        <div class="form-group">
          <label for="interests">Interests</label>
          <input type="text" id="interests" name="interests" value="<?= clean($user['interests']) ?>">
        </div>
        <div class="form-group">
          <label for="skills">Skills</label>
          <input type="text" id="skills" name="skills" value="<?= clean($user['skills']) ?>">
        </div>
        <div class="form-group">
          <label for="hobbies">Hobbies</label>
          <input type="text" id="hobbies" name="hobbies" value="<?= clean($user['hobbies']) ?>">
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
  </section>
</main>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
