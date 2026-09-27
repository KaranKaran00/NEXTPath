<?php
require_once __DIR__ . '/../includes/admin_auth.php';

$totalStudents = (int)$pdo->query("SELECT COUNT(*) AS c FROM users WHERE role = 'student'")->fetch()['c'];
$totalQuestions = (int)$pdo->query('SELECT COUNT(*) AS c FROM questions')->fetch()['c'];
$totalCategories = (int)$pdo->query('SELECT COUNT(*) AS c FROM categories')->fetch()['c'];
$totalCareers = (int)$pdo->query('SELECT COUNT(*) AS c FROM careers')->fetch()['c'];
try {
    $totalCourses = (int)$pdo->query('SELECT COUNT(*) AS c FROM courses')->fetch()['c'];
} catch (PDOException $e) {
    error_log('[NEXTPath] courses lookup failed: ' . $e->getMessage());
    $totalCourses = 0;
}
$completedAssessments = (int)$pdo->query('SELECT COUNT(*) AS c FROM assessments')->fetch()['c'];

$avgScoreRow = $pdo->query('SELECT AVG(overall_score) AS avg_score FROM assessments')->fetch();
$avgScore = $avgScoreRow['avg_score'] !== null ? round($avgScoreRow['avg_score'], 1) : 0;

$popularCategory = $pdo->query('
    SELECT c.name, COUNT(*) AS attempts
    FROM assessment_answers aa
    JOIN questions q ON q.id = aa.question_id
    JOIN categories c ON c.id = q.category_id
    GROUP BY c.id, c.name ORDER BY attempts DESC LIMIT 1
')->fetch();

$mostRecommendedCareer = $pdo->query('
    SELECT ca.career_name, COUNT(*) AS times_top
    FROM assessments a
    JOIN assessment_answers aa ON aa.assessment_id = a.id
    JOIN questions q ON q.id = aa.question_id
    JOIN careers ca ON ca.category_id = q.category_id
    GROUP BY ca.id, ca.career_name
    ORDER BY times_top DESC LIMIT 1
')->fetch();

$page_title = 'Statistics — NEXTPath Admin';
$active = 'statistics';
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
      <h1>Statistics</h1>
      <p class="dashboard-sub">Platform-wide performance at a glance.</p>
    </div>
  </header>

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
      <span class="stat-label">Total Categories</span>
      <span class="stat-value"><?= number_format($totalCategories) ?></span>
    </div>
    <div class="stat-card">
      <span class="stat-label">Total Careers</span>
      <span class="stat-value"><?= number_format($totalCareers) ?></span>
    </div>
  </section>

  <section class="stat-grid stat-grid-3">
    <div class="stat-card">
      <span class="stat-label">Completed Assessments</span>
      <span class="stat-value"><?= number_format($completedAssessments) ?></span>
    </div>
    <div class="stat-card">
      <span class="stat-label">Average Assessment Score</span>
      <span class="stat-value"><?= $avgScore ?>%</span>
    </div>
    <div class="stat-card">
      <span class="stat-label">Total Courses</span>
      <span class="stat-value"><?= number_format($totalCourses) ?></span>
    </div>
  </section>

  <section class="card">
    <h2 class="card-title">Most Popular Category</h2>
    <p class="dashboard-sub"><?= $popularCategory ? clean($popularCategory['name']) : 'No data yet.' ?></p>
  </section>

  <section class="card">
    <h2 class="card-title">Most Recommended Career</h2>
    <p class="dashboard-sub">
      <?= $mostRecommendedCareer ? clean($mostRecommendedCareer['career_name']) : 'No data yet.' ?>
    </p>
  </section>
</main>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
