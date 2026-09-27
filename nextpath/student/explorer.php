<?php
require_once __DIR__ . '/../includes/auth.php';

$categories = $pdo->query('SELECT id, name, description FROM categories ORDER BY name ASC')->fetchAll();

$careersStmt = $pdo->query('SELECT category_id, career_name, description FROM careers ORDER BY career_name ASC');

try {
    $coursesStmt = $pdo->query('SELECT category_id, course_name, description FROM courses ORDER BY course_name ASC');
    $courseRows = $coursesStmt->fetchAll();
} catch (PDOException $e) {
    // Degrade gracefully on installs that haven't run the courses migration yet.
    error_log('[NEXTPath] courses lookup failed: ' . $e->getMessage());
    $courseRows = [];
}

$careersByCategory = [];
foreach ($careersStmt->fetchAll() as $c) {
    $careersByCategory[$c['category_id']][] = $c;
}
$coursesByCategory = [];
foreach ($courseRows as $c) {
    $coursesByCategory[$c['category_id']][] = $c;
}

$page_title = 'Career Explorer — NEXTPath';
$active = 'explorer';
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
<link rel="stylesheet" href="<?= base_url('assets/css/explorer.css') ?>">
</head>
<body class="dashboard-body">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<main class="dashboard-main">
  <header class="dashboard-header">
    <div>
      <h1>Career Explorer</h1>
      <p class="dashboard-sub">Browse every field NEXTPath covers — careers, and the courses that lead there.</p>
    </div>
  </header>

  <div class="explorer-search">
    <input type="search" id="explorerSearch" placeholder="Search a field or career, e.g. &quot;Nursing&quot; or &quot;Data Analyst&quot;" aria-label="Search career fields">
  </div>

  <div class="explorer-grid" id="explorerGrid">
    <?php foreach ($categories as $cat):
        $careers = $careersByCategory[$cat['id']] ?? [];
        $courses = $coursesByCategory[$cat['id']] ?? [];
        $searchBlob = strtolower($cat['name'] . ' ' . implode(' ', array_column($careers, 'career_name')));
    ?>
      <article class="explorer-card" data-search="<?= clean($searchBlob) ?>">
        <button type="button" class="explorer-card-head" aria-expanded="false">
          <div>
            <h2><?= clean($cat['name']) ?></h2>
            <p><?= clean($cat['description']) ?></p>
          </div>
          <span class="explorer-count"><?= count($careers) ?> career<?= count($careers) === 1 ? '' : 's' ?></span>
          <span class="explorer-chevron" aria-hidden="true">&#9662;</span>
        </button>
        <div class="explorer-card-body">
          <?php if (!empty($careers)): ?>
            <h3 class="explorer-subhead">Careers</h3>
            <ul class="explorer-list">
              <?php foreach ($careers as $career): ?>
                <li>
                  <span class="explorer-career-name"><?= clean($career['career_name']) ?></span>
                  <?php if (!empty($career['description'])): ?>
                    <span class="explorer-career-desc"><?= clean($career['description']) ?></span>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="explorer-empty">Careers for this field are coming soon.</p>
          <?php endif; ?>

          <?php if (!empty($courses)): ?>
            <h3 class="explorer-subhead">Suggested Courses</h3>
            <div class="course-chips">
              <?php foreach ($courses as $course): ?>
                <span class="course-chip" title="<?= clean($course['description']) ?>"><?= clean($course['course_name']) ?></span>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </article>
    <?php endforeach; ?>
  </div>

  <p class="explorer-no-results" id="explorerNoResults" style="display:none;">No fields match your search.</p>
</main>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
<script src="<?= base_url('assets/js/explorer.js') ?>"></script>
</body>
</html>
