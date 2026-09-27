<?php
require_once __DIR__ . '/../includes/admin_auth.php';

$assessments = $pdo->query('
    SELECT a.id, a.overall_score, a.total_questions, a.correct_answers, a.completed_at, u.full_name, u.email
    FROM assessments a JOIN users u ON u.id = a.user_id
    ORDER BY a.completed_at DESC
')->fetchAll();

$page_title = 'Assessments — NEXTPath Admin';
$active = 'assessments';
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
      <h1>Assessments</h1>
      <p class="dashboard-sub"><?= count($assessments) ?> completed assessments.</p>
    </div>
  </header>

  <section class="card">
    <div class="table-scroll">
      <table class="data-table">
        <thead><tr><th>Student</th><th>Email</th><th>Score</th><th>Correct / Total</th><th>Completed</th></tr></thead>
        <tbody>
          <?php foreach ($assessments as $a): ?>
            <tr>
              <td><?= clean($a['full_name']) ?></td>
              <td><?= clean($a['email']) ?></td>
              <td><?= clean($a['overall_score']) ?>%</td>
              <td><?= (int)$a['correct_answers'] ?> / <?= (int)$a['total_questions'] ?></td>
              <td><?= date('d M Y, H:i', strtotime($a['completed_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($assessments)): ?>
            <tr><td colspan="5">No assessments completed yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
