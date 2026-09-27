<?php
require_once __DIR__ . '/../includes/admin_auth.php';

$totalStudents = (int)$pdo->query("SELECT COUNT(*) AS c FROM users WHERE role = 'student'")->fetch()['c'];
$totalQuestions = (int)$pdo->query('SELECT COUNT(*) AS c FROM questions')->fetch()['c'];
$totalCategories = (int)$pdo->query('SELECT COUNT(*) AS c FROM categories')->fetch()['c'];
$completedAssessments = (int)$pdo->query('SELECT COUNT(*) AS c FROM assessments')->fetch()['c'];

$recentStudents = $pdo->query("
    SELECT full_name, email, created_at FROM users
    WHERE role = 'student' ORDER BY created_at DESC LIMIT 5
")->fetchAll();

$recentAssessments = $pdo->query('
    SELECT a.id, a.overall_score, a.completed_at, u.full_name
    FROM assessments a JOIN users u ON u.id = a.user_id
    ORDER BY a.completed_at DESC LIMIT 5
')->fetchAll();

$popularCategory = $pdo->query('
    SELECT c.name, COUNT(*) AS attempts
    FROM assessment_answers aa
    JOIN questions q ON q.id = aa.question_id
    JOIN categories c ON c.id = q.category_id
    GROUP BY c.id, c.name ORDER BY attempts DESC LIMIT 1
')->fetch();

$questionDistribution = $pdo->query('
    SELECT c.name, COUNT(q.id) AS total
    FROM categories c LEFT JOIN questions q ON q.category_id = c.id
    GROUP BY c.id, c.name ORDER BY total DESC
')->fetchAll();

$page_title = 'Admin Dashboard — NEXTPath';
$active = 'dashboard';
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
      <h1>Admin Dashboard</h1>
      <p class="dashboard-sub">An overview of NEXTPath activity.</p>
    </div>
  </header>

  <?php $flash = get_flash(); if ($flash): ?>
    <div class="flash flash-<?= clean($flash['type']) ?>"><?= clean($flash['message']) ?></div>
  <?php endif; ?>

  <section class="stat-grid stat-grid-4">
    <div class="stat-card">
      <span class="stat-label">Total Students</span>
      <span class="stat-value"><?= number_format($totalStudents) ?></span>
    </div>
    <div class="stat-card">
      <span class="stat-label">Total Questions</span>
      <span class="stat-value"><?= number_format($totalQuestions) ?></span>
    </div>
    <div class="stat-card">
      <span class="stat-label">Career Categories</span>
      <span class="stat-value"><?= number_format($totalCategories) ?></span>
    </div>
    <div class="stat-card">
      <span class="stat-label">Completed Assessments</span>
      <span class="stat-value"><?= number_format($completedAssessments) ?></span>
    </div>
  </section>

  <div class="admin-grid">
    <section class="card">
      <h2 class="card-title">Recent Students</h2>
      <div class="table-scroll">
        <table class="data-table">
          <thead><tr><th>Name</th><th>Email</th><th>Registered</th></tr></thead>
          <tbody>
            <?php foreach ($recentStudents as $s): ?>
              <tr>
                <td><?= clean($s['full_name']) ?></td>
                <td><?= clean($s['email']) ?></td>
                <td><?= date('d M Y', strtotime($s['created_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($recentStudents)): ?>
              <tr><td colspan="3">No students yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="card">
      <h2 class="card-title">Recent Assessments</h2>
      <div class="table-scroll">
        <table class="data-table">
          <thead><tr><th>Student</th><th>Score</th><th>Date</th></tr></thead>
          <tbody>
            <?php foreach ($recentAssessments as $a): ?>
              <tr>
                <td><?= clean($a['full_name']) ?></td>
                <td><?= clean($a['overall_score']) ?>%</td>
                <td><?= date('d M Y', strtotime($a['completed_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($recentAssessments)): ?>
              <tr><td colspan="3">No assessments yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>

  <section class="card">
    <h2 class="card-title">Question Distribution</h2>
    <p class="dashboard-sub" style="margin-bottom:16px;">
      Most assessed category:
      <strong><?= $popularCategory ? clean($popularCategory['name']) : '—' ?></strong>
    </p>
    <div class="bar-list">
      <?php $max = max(array_column($questionDistribution, 'total') ?: [1]); $max = max($max, 1); ?>
      <?php foreach ($questionDistribution as $row): ?>
        <div class="bar-row">
          <span class="bar-label"><?= clean($row['name']) ?></span>
          <div class="bar-track">
            <div class="bar-fill" style="width: <?= round(($row['total'] / $max) * 100, 1) ?>%;"></div>
          </div>
          <span class="bar-value"><?= (int)$row['total'] ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
