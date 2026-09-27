<?php
require_once __DIR__ . '/../includes/auth.php';

$userId = current_user_id();
$stmt = $pdo->prepare('SELECT * FROM assessments WHERE user_id = ? ORDER BY completed_at DESC');
$stmt->execute([$userId]);
$assessments = $stmt->fetchAll();

$history = [];
foreach ($assessments as $a) {
    $scores = get_category_scores($pdo, $a['id']);
    $history[] = [
        'id'        => $a['id'],
        'date'      => date('d M Y', strtotime($a['completed_at'])),
        'score'     => $a['overall_score'],
        'strongest' => !empty($scores) ? array_values($scores)[0]['name'] : '—',
    ];
}

$page_title = 'Assessment History — NEXTPath';
$active = 'history';
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
</head>
<body class="dashboard-body">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<main class="dashboard-main">
  <header class="dashboard-header">
    <div>
      <h1>Assessment History</h1>
      <p class="dashboard-sub">Every assessment you've completed, in one place.</p>
    </div>
  </header>

  <?php if (empty($history)): ?>
    <section class="empty-state">
      <h2>No assessment completed yet.</h2>
      <p>Take your first assessment to discover your strengths and career possibilities.</p>
      <a href="assessment.php" class="btn btn-primary">Start Assessment</a>
    </section>
  <?php else: ?>
    <section class="card">
      <div class="table-scroll">
        <table class="data-table">
          <thead>
            <tr><th>Date</th><th>Score</th><th>Strongest Field</th><th></th><th></th></tr>
          </thead>
          <tbody>
            <?php foreach ($history as $h): ?>
              <tr>
                <td><?= clean($h['date']) ?></td>
                <td><?= clean($h['score']) ?>%</td>
                <td><?= clean($h['strongest']) ?></td>
                <td><a href="career-dashboard.php?id=<?= $h['id'] ?>" class="btn btn-outline btn-sm">View</a></td>
                <td><a href="career-report.php?id=<?= $h['id'] ?>" class="btn btn-outline btn-sm" target="_blank" rel="noopener">Report</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  <?php endif; ?>
</main>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
