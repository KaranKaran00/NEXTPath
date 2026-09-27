<?php
require_once __DIR__ . '/../includes/auth.php';

$userId = current_user_id();

$stmt = $pdo->prepare('SELECT full_name FROM users WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();

$stmt = $pdo->prepare('SELECT * FROM assessments WHERE user_id = ? ORDER BY completed_at DESC LIMIT 1');
$stmt->execute([$userId]);
$latest = $stmt->fetch();

$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM assessments WHERE user_id = ?');
$stmt->execute([$userId]);
$assessmentCount = (int)$stmt->fetch()['total'];

$strongestField = '—';
$careerMatches = 0;

if ($latest) {
    $categoryScores = get_category_scores($pdo, $latest['id']);
    if (!empty($categoryScores)) {
        $strongestField = array_values($categoryScores)[0]['name'];
    }
    $careerMatches = count(get_career_recommendations($pdo, $categoryScores, 10));
}

$page_title = 'Dashboard — NEXTPath';
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
</head>
<body class="dashboard-body">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<main class="dashboard-main">
  <header class="dashboard-header">
    <div>
      <h1>Welcome back, <?= clean($user['full_name']) ?>.</h1>
      <p class="dashboard-sub">Continue discovering your next path.</p>
    </div>
  </header>

  <?php $flash = get_flash(); if ($flash): ?>
    <div class="flash flash-<?= clean($flash['type']) ?>"><?= clean($flash['message']) ?></div>
  <?php endif; ?>

  <section class="stat-grid">
    <div class="stat-card">
      <span class="stat-label">Assessment Score</span>
      <span class="stat-value"><?= $latest ? clean($latest['overall_score']) . '%' : '—' ?></span>
    </div>
    <div class="stat-card">
      <span class="stat-label">Strongest Field</span>
      <span class="stat-value stat-value-sm"><?= clean($strongestField) ?></span>
    </div>
    <div class="stat-card">
      <span class="stat-label">Career Matches</span>
      <span class="stat-value"><?= $latest ? $careerMatches : '—' ?></span>
    </div>
    <div class="stat-card">
      <span class="stat-label">Assessment Status</span>
      <span class="stat-value stat-value-sm"><?= $latest ? 'Completed' : 'Not Started' ?></span>
    </div>
  </section>

  <?php if (!$latest): ?>
    <section class="empty-state">
      <h2>No assessment completed yet.</h2>
      <p>Take your first assessment to discover your strengths and career possibilities.</p>
      <a href="assessment.php" class="btn btn-primary">Start Assessment</a>
    </section>
  <?php else: ?>
    <section class="dashboard-actions">
      <a href="career-dashboard.php" class="btn btn-primary">View Career Dashboard</a>
      <a href="assessment.php" class="btn btn-outline">Retake Assessment</a>
      <a href="history.php" class="btn btn-outline">View History (<?= $assessmentCount ?>)</a>
    </section>
  <?php endif; ?>
</main>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
