<?php
require_once __DIR__ . '/../includes/auth.php';

$userId = current_user_id();
$assessmentId = (int)($_GET['id'] ?? 0);

if ($assessmentId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM assessments WHERE id = ? AND user_id = ?');
    $stmt->execute([$assessmentId, $userId]);
    $assessment = $stmt->fetch();
} else {
    $stmt = $pdo->prepare('SELECT * FROM assessments WHERE user_id = ? ORDER BY completed_at DESC LIMIT 1');
    $stmt->execute([$userId]);
    $assessment = $stmt->fetch();
}

$page_title = 'Career Dashboard — NEXTPath';
$active = 'career';
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
      <h1>Career Dashboard</h1>
      <p class="dashboard-sub">Your skills, strengths and matching career paths.</p>
    </div>
  </header>

  <?php if (!$assessment): ?>
    <section class="empty-state">
      <h2>No assessment completed yet.</h2>
      <p>Take your first assessment to discover your strengths and career possibilities.</p>
      <a href="assessment.php" class="btn btn-primary">Start Assessment</a>
    </section>
  <?php else:
    $categoryScores = get_category_scores($pdo, $assessment['id']);
    $skillProfile = get_skill_profile($pdo, $categoryScores);
    $recommendations = get_career_recommendations($pdo, $categoryScores, 6);
    $strongestField = !empty($categoryScores) ? array_values($categoryScores)[0]['name'] : '—';
    $courseMap = get_courses_for_categories($pdo, array_keys($categoryScores));
  ?>

  <header class="dashboard-header" style="margin-top:-8px;">
    <div></div>
    <div class="dashboard-actions" style="margin-top:0;">
      <a href="career-report.php?id=<?= $assessment['id'] ?>" class="btn btn-outline" target="_blank" rel="noopener">Download Report</a>
      <a href="explorer.php" class="btn btn-outline">Explore All Careers</a>
    </div>
  </header>


  <section class="stat-grid">
    <div class="stat-card">
      <span class="stat-label">Overall Assessment Score</span>
      <span class="stat-value"><?= clean($assessment['overall_score']) ?>%</span>
    </div>
    <div class="stat-card">
      <span class="stat-label">Strongest Field</span>
      <span class="stat-value stat-value-sm"><?= clean($strongestField) ?></span>
    </div>
  </section>

  <section class="card">
    <h2 class="card-title">Your Skill Profile</h2>
    <div class="skill-list">
      <?php foreach ($skillProfile as $skill => $score): ?>
        <div class="skill-row">
          <span class="skill-name"><?= clean($skill) ?></span>
          <span class="skill-score"><?= clean($score) ?>%</span>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="card">
    <h2 class="card-title">Your Strengths</h2>
    <div class="strength-list">
      <?php $rank = 1; foreach ($skillProfile as $skill => $score): if ($rank > 5) break; ?>
        <div class="strength-row">
          <span class="strength-rank"><?= sprintf('%02d', $rank) ?></span>
          <span class="strength-name"><?= clean($skill) ?></span>
          <span class="strength-level"><?= skill_level_label($score) ?></span>
        </div>
      <?php $rank++; endforeach; ?>
    </div>
  </section>

  <section class="card" id="recommendations">
    <h2 class="card-title">Recommended Careers</h2>
    <div class="career-list">
      <?php foreach ($recommendations as $rec): ?>
        <div class="career-row career-row-expanded">
          <span class="career-match"><?= clean($rec['match']) ?>% Match</span>
          <div class="career-row-body">
            <span class="career-name"><?= clean($rec['career_name']) ?></span>
            <span class="career-category"><?= clean($rec['category_name']) ?></span>
            <?php if (!empty($courseMap[$rec['category_id']])): ?>
              <div class="course-chips">
                <?php foreach (array_slice($courseMap[$rec['category_id']], 0, 3) as $course): ?>
                  <span class="course-chip" title="<?= clean($course['description']) ?>"><?= clean($course['course_name']) ?></span>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="card">
    <h2 class="card-title">Category Performance</h2>
    <div class="bar-list">
      <?php foreach ($categoryScores as $cat): ?>
        <div class="bar-row">
          <span class="bar-label"><?= clean($cat['name']) ?></span>
          <div class="bar-track">
            <div class="bar-fill" style="width: <?= clean($cat['score']) ?>%;"></div>
          </div>
          <span class="bar-value"><?= clean($cat['score']) ?>%</span>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <?php endif; ?>
</main>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
