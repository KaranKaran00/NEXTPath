<?php
require_once __DIR__ . '/../includes/admin_auth.php';

$students = $pdo->query("
    SELECT id, full_name, email, mobile, education_level, college_name, gender, created_at
    FROM users WHERE role = 'student' ORDER BY created_at DESC
")->fetchAll();

$page_title = 'Students — NEXTPath Admin';
$active = 'students';
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
<link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>
<body class="dashboard-body">
<?php require __DIR__ . '/../includes/admin-sidebar.php'; ?>
<main class="dashboard-main">
  <header class="dashboard-header">
    <div>
      <h1>Students</h1>
      <p class="dashboard-sub"><?= count($students) ?> registered students.</p>
    </div>
  </header>

  <section class="card">
    <div class="table-scroll">
      <table class="data-table">
        <thead>
          <tr>
            <th>Name</th><th>Email</th><th>Mobile</th><th>Education Level</th>
            <th>College</th><th>Gender</th><th>Registered</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($students as $s): ?>
            <tr>
              <td><?= clean($s['full_name']) ?></td>
              <td><?= clean($s['email']) ?></td>
              <td><?= clean($s['mobile']) ?></td>
              <td><?= clean($s['education_level']) ?></td>
              <td><?= clean($s['college_name']) ?></td>
              <td><?= clean($s['gender']) ?></td>
              <td><?= date('d M Y', strtotime($s['created_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($students)): ?>
            <tr><td colspan="7">No students registered yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
