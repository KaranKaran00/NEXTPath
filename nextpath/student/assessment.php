<?php
require_once __DIR__ . '/../includes/auth.php';

$assessmentLength = (int) get_setting($pdo, 'assessment_length', 40);
$questions = get_assessment_questions($pdo, $assessmentLength);

if (empty($questions)) {
    set_flash('error', 'No assessment questions are available yet. Please check back soon.');
    redirect('dashboard.php');
}

// Persist the exact question set in the session so submit_assessment.php
// grades against the same questions the student actually saw.
$_SESSION['assessment_question_ids'] = array_column($questions, 'id');

$total = count($questions);
$page_title = 'Assessment — NEXTPath';
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
<link rel="stylesheet" href="<?= base_url('assets/css/assessment.css') ?>">
</head>
<body class="dashboard-body">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<main class="dashboard-main assessment-main">
  <div class="assessment-shell">
    <div class="assessment-topbar">
      <span class="assessment-label">Assessment</span>
      <span class="assessment-count" id="questionCounter">Question 01 / <?= sprintf('%02d', $total) ?></span>
    </div>
    <div class="progress-track">
      <div class="progress-fill" id="progressFill" style="width: <?= round(100 / $total, 2) ?>%;"></div>
    </div>

    <form method="POST" action="submit_assessment.php" id="assessmentForm">
      <?php foreach ($questions as $i => $q): ?>
        <div class="question-card <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>">
          <span class="category-badge"><?= strtoupper(clean($q['category_name'])) ?></span>
          <h2 class="question-text"><?= clean($q['question_text']) ?></h2>
          <div class="options-list">
            <?php foreach (['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $key => $label): ?>
              <label class="option-item">
                <input type="radio" name="q_<?= $q['id'] ?>" value="<?= $label ?>" required>
                <span class="option-marker"></span>
                <span class="option-text"><?= clean($q["option_{$key}"]) ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>

      <div class="assessment-nav">
        <button type="button" class="btn btn-outline" id="prevBtn" disabled>Previous</button>
        <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
        <button type="submit" class="btn btn-primary" id="submitBtn" style="display:none;">Submit</button>
      </div>
    </form>
  </div>
</main>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
<script src="<?= base_url('assets/js/assessment.js') ?>"></script>
</body>
</html>
