<?php
require_once __DIR__ . '/../includes/auth.php';

$assessmentId = (int)($_GET['id'] ?? 0);
$userId = current_user_id();

$stmt = $pdo->prepare('SELECT * FROM assessments WHERE id = ? AND user_id = ?');
$stmt->execute([$assessmentId, $userId]);
$assessment = $stmt->fetch();

if (!$assessment) {
    redirect('dashboard.php');
}

$categoryScores = get_category_scores($pdo, $assessmentId);
$strongestField = !empty($categoryScores) ? array_values($categoryScores)[0]['name'] : '—';
$recommendations = get_career_recommendations($pdo, $categoryScores, 5);

$page_title = 'Assessment Complete — NEXTPath';
$active = 'assessment';
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
  <section class="result-hero">
    <p class="eyebrow">Assessment Complete</p>
    <h1>Excellent work.</h1>
    <p class="dashboard-sub">Your results are ready.</p>

    <div class="result-score">
      <span class="result-score-value"><?= clean($assessment['overall_score']) ?>%</span>
      <span class="result-score-label">Overall Score</span>
    </div>

    <div class="result-strongest">
      <span class="result-strongest-label">Your strongest area</span>
      <span class="result-strongest-value"><?= strtoupper(clean($strongestField)) ?></span>
    </div>
  </section>

  <section class="card">
    <h2 class="card-title">Top Career Matches</h2>
    <div class="career-list">
      <?php foreach ($recommendations as $rec): ?>
        <div class="career-row">
          <span class="career-match"><?= clean($rec['match']) ?>%</span>
          <span class="career-name"><?= clean($rec['career_name']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <div class="dashboard-actions">
    <a href="career-dashboard.php" class="btn btn-primary btn-lg">View Career Dashboard</a>
    <a href="career-report.php?id=<?= $assessment['id'] ?>" class="btn btn-outline btn-lg" target="_blank" rel="noopener">Download Report</a>
  </div>
</main>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
