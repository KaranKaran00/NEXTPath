<?php
require_once __DIR__ . '/../includes/admin_auth.php';

$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name ASC')->fetchAll();
$errors = [];
$old = ['category_id' => '', 'question_text' => '', 'option_a' => '', 'option_b' => '', 'option_c' => '', 'option_d' => '', 'correct_answer' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $key => $_) {
        $old[$key] = trim($_POST[$key] ?? '');
    }

    if (empty($old['category_id'])) $errors['category_id'] = 'Please select a category.';
    if (empty($old['question_text'])) $errors['question_text'] = 'Please enter the question.';
    foreach (['option_a', 'option_b', 'option_c', 'option_d'] as $opt) {
        if (empty($old[$opt])) $errors[$opt] = 'Required.';
    }
    if (!in_array($old['correct_answer'], ['A', 'B', 'C', 'D'], true)) {
        $errors['correct_answer'] = 'Please select the correct answer.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('
            INSERT INTO questions (category_id, question_text, option_a, option_b, option_c, option_d, correct_answer)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            (int)$old['category_id'], $old['question_text'],
            $old['option_a'], $old['option_b'], $old['option_c'], $old['option_d'], $old['correct_answer'],
        ]);
        set_flash('success', 'Question added successfully.');
        redirect('questions.php');
    }
}

$page_title = 'Add Question — NEXTPath Admin';
$active = 'questions';
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
    <div><h1>Add Question</h1></div>
  </header>

  <section class="card">
    <form method="POST" action="add-question.php" novalidate>
      <div class="form-group">
        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
          <option value="">Select Category</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= (string)$old['category_id'] === (string)$c['id'] ? 'selected' : '' ?>><?= clean($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <span class="field-error"><?= clean($errors['category_id'] ?? '') ?></span>
      </div>
      <div class="form-group">
        <label for="question_text">Question</label>
        <textarea id="question_text" name="question_text" rows="2" required><?= clean($old['question_text']) ?></textarea>
        <span class="field-error"><?= clean($errors['question_text'] ?? '') ?></span>
      </div>
      <div class="form-grid">
        <div class="form-group">
          <label for="option_a">Option A</label>
          <input type="text" id="option_a" name="option_a" value="<?= clean($old['option_a']) ?>" required>
          <span class="field-error"><?= clean($errors['option_a'] ?? '') ?></span>
        </div>
        <div class="form-group">
          <label for="option_b">Option B</label>
          <input type="text" id="option_b" name="option_b" value="<?= clean($old['option_b']) ?>" required>
          <span class="field-error"><?= clean($errors['option_b'] ?? '') ?></span>
        </div>
        <div class="form-group">
          <label for="option_c">Option C</label>
          <input type="text" id="option_c" name="option_c" value="<?= clean($old['option_c']) ?>" required>
          <span class="field-error"><?= clean($errors['option_c'] ?? '') ?></span>
        </div>
        <div class="form-group">
          <label for="option_d">Option D</label>
          <input type="text" id="option_d" name="option_d" value="<?= clean($old['option_d']) ?>" required>
          <span class="field-error"><?= clean($errors['option_d'] ?? '') ?></span>
        </div>
      </div>
      <div class="form-group">
        <label for="correct_answer">Correct Answer</label>
        <select id="correct_answer" name="correct_answer" required>
          <option value="">Select Correct Answer</option>
          <?php foreach (['A', 'B', 'C', 'D'] as $letter): ?>
            <option value="<?= $letter ?>" <?= $old['correct_answer'] === $letter ? 'selected' : '' ?>>Option <?= $letter ?></option>
          <?php endforeach; ?>
        </select>
        <span class="field-error"><?= clean($errors['correct_answer'] ?? '') ?></span>
      </div>
      <button type="submit" class="btn btn-primary">Save Question</button>
      <a href="questions.php" class="btn btn-outline">Cancel</a>
    </form>
  </section>
</main>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
